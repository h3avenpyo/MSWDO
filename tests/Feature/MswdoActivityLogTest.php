<?php

namespace Tests\Feature;

use App\Models\MswdoActivityLog;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\User;
use App\Services\MswdoActivityLogger;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MswdoActivityLogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Never migrate, truncate or modify the project's configured MySQL database.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->nullable();
            $table->string('password');
            $table->string('role');
            $table->string('status')->default('active');
            $table->string('phone')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('senior_citizen_records', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('status')->default('active');
            $table->integer('print_count')->default(0);
            $table->timestamps();
        });
        (require database_path('migrations/2026_11_10_000001_create_mswdo_activity_logs_table.php'))->up();
    }

    private function user(string $role = 'admin'): User
    {
        return User::create([
            'name' => 'Audit Test User', 'email' => uniqid().'@example.com',
            'password' => 'Password123!', 'role' => $role, 'status' => 'active',
        ]);
    }

    private function sessionFor(User $user, string $source = 'mswdo'): array
    {
        return ['admin_user_id' => $user->id, 'admin_user_name' => $user->name,
            'admin_user_role' => $user->getRawOriginal('role'), 'auth_source' => $source];
    }

    private function asUser(User $user, string $source = 'mswdo'): void
    {
        $this->withSession($this->sessionFor($user, $source));
        request()->setLaravelSession(app('session')->driver());
    }

    public function test_instance_changes_are_recorded_once_and_sensitive_fields_are_excluded(): void
    {
        $actor = $this->user();
        $this->asUser($actor);
        $record = SeniorCitizenRecord::create(['first_name' => 'Confidential name', 'status' => 'active']);
        $record->update(['status' => 'archived', 'first_name' => 'Another private name']);
        $record->update(['status' => 'active']);
        $record->delete();

        $this->assertSame(['created', 'archived', 'restored', 'deleted'], MswdoActivityLog::orderBy('id')->pluck('activity_type')->all());
        $log = MswdoActivityLog::where('activity_type', 'archived')->first();
        $this->assertSame(['status' => 'active'], $log->old_values);
        $this->assertSame(['status' => 'archived'], $log->new_values);
        $this->assertStringNotContainsString('private', MswdoActivityLog::all()->toJson());
        $this->assertStringNotContainsString('Confidential', MswdoActivityLog::all()->toJson());

        $actor->update(['password' => 'DifferentSecret123!', 'phone' => '09171234567']);
        $last = MswdoActivityLog::latest('id')->first();
        $this->assertSame([], $last->old_values);
        $this->assertSame([], $last->new_values);
        $this->assertStringNotContainsString('DifferentSecret', $last->toJson());
    }

    public function test_bulk_updates_and_deletes_capture_each_affected_record_once(): void
    {
        SeniorCitizenRecord::create(['status' => 'active']);
        SeniorCitizenRecord::create(['status' => 'active']);
        $this->asUser($this->user('Senior Citizen officer'));
        $this->assertSame(2, SeniorCitizenRecord::where('status', 'active')->update(['status' => 'archived']));
        $this->assertSame(2, MswdoActivityLog::where('activity_type', 'archived')->count());
        SeniorCitizenRecord::where('status', 'archived')->update(['status' => 'active']);
        $this->assertSame(2, MswdoActivityLog::where('activity_type', 'restored')->count());
        SeniorCitizenRecord::query()->delete();
        $this->assertSame(2, MswdoActivityLog::where('activity_type', 'deleted')->count());
    }

    public function test_noop_and_timestamp_only_updates_do_not_generate_duplicate_logs(): void
    {
        $user = $this->user();
        $this->asUser($user);
        $record = SeniorCitizenRecord::create(['status' => 'active']);
        $record->update(['status' => 'active']);
        SeniorCitizenRecord::whereKey($record->id)->update(['updated_at' => now()->addMinute()]);
        $this->assertSame(1, MswdoActivityLog::count());
    }

    public function test_logs_roll_back_with_the_business_transaction(): void
    {
        $this->asUser($this->user());
        DB::beginTransaction();
        SeniorCitizenRecord::create(['status' => 'active']);
        DB::rollBack();
        $this->assertSame(0, MswdoActivityLog::count());
        $this->assertSame(0, SeniorCitizenRecord::count());
    }

    public function test_all_wcpu_sessions_are_excluded_even_when_user_ids_collide(): void
    {
        $this->asUser($this->user(), 'wcpu');
        SeniorCitizenRecord::create(['status' => 'active']);
        SeniorCitizenRecord::query()->update(['status' => 'archived']);
        SeniorCitizenRecord::query()->delete();
        app(MswdoActivityLogger::class)->record('login', 'Authentication', 'Logged in');
        $this->assertSame(0, MswdoActivityLog::count());
    }

    public function test_guests_and_unrecognized_roles_are_not_logged(): void
    {
        SeniorCitizenRecord::create(['status' => 'active']);
        $user = $this->user();
        DB::table('users')->where('id', $user->id)->update(['role' => 'bcpc_admin']);
        $this->asUser($user);
        SeniorCitizenRecord::query()->update(['status' => 'archived']);
        $this->assertSame(0, MswdoActivityLog::count());
    }

    public function test_password_login_and_logout_are_recorded_once(): void
    {
        $user = $this->user('social_worker');
        $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'Password123!'])->assertRedirect();
        $this->post(route('admin.logout'))->assertRedirect();
        $this->assertSame(['login', 'logout'], MswdoActivityLog::orderBy('id')->pluck('activity_type')->all());
    }

    public function test_email_code_login_records_one_activity_without_storing_the_code(): void
    {
        $user = $this->user('financialstep1');
        Schema::create('email_login_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('auth_source');
            $table->string('email');
            $table->string('code');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
        \App\Models\EmailLoginCode::create([
            'user_id' => $user->id, 'auth_source' => 'mswdo', 'email' => $user->email,
            'code' => \Illuminate\Support\Facades\Hash::make('123456'), 'expires_at' => now()->addMinutes(5),
        ]);
        $this->postJson(route('admin.login.code.verify'), ['email' => $user->email, 'code' => '123456'])->assertOk();
        $this->assertSame(1, MswdoActivityLog::where('activity_type', 'login')->count());
        $this->assertStringNotContainsString('123456', MswdoActivityLog::all()->toJson());
    }

    public function test_important_action_middleware_logs_exports_without_duplicate_model_logs(): void
    {
        $this->asUser($this->user());
        \Illuminate\Support\Facades\Route::middleware('web')->get('/audit-test/export', fn () => response('CSV'))->name('admin.senior.test.export');
        \Illuminate\Support\Facades\Route::middleware('web')->get('/audit-test/print', function () {
            SeniorCitizenRecord::create(['status' => 'active']);

            return response('PDF');
        })->name('admin.senior.test.print');
        $this->get('/audit-test/export')->assertOk();
        $this->get('/audit-test/print')->assertOk();
        $this->assertSame(['export', 'created'], MswdoActivityLog::orderBy('id')->pluck('activity_type')->all());
        $this->withSession(['auth_source' => 'wcpu'])->get('/audit-test/export')->assertOk();
        $this->assertSame(2, MswdoActivityLog::count());
    }

    public function test_inactive_account_logout_is_logged_once_and_access_is_denied(): void
    {
        $user = $this->user();
        DB::table('users')->where('id', $user->id)->update(['status' => 'inactive']);
        $this->withSession($this->sessionFor($user))->getJson(route('admin.activity-logs.index'))->assertUnauthorized();
        $this->assertSame(1, MswdoActivityLog::where('activity_type', 'logout')->count());
        $this->getJson(route('admin.activity-logs.index'))->assertUnauthorized();
        $this->assertSame(1, MswdoActivityLog::where('activity_type', 'logout')->count());
    }

    public function test_only_active_mswdo_administrators_can_read_list_and_details(): void
    {
        $admin = $this->user();
        $this->asUser($admin);
        app(MswdoActivityLogger::class)->record('login', 'Authentication', 'Logged in');
        $log = MswdoActivityLog::first();
        $this->withSession($this->sessionFor($admin))->get(route('admin.activity-logs.index'))->assertOk()->assertSee('User Logs');
        $this->get(route('admin.activity-logs.show', $log))->assertOk()->assertSee('Role at time of activity');
        $officer = $this->user('social_worker');
        $this->withSession($this->sessionFor($officer))->get(route('admin.activity-logs.index'))->assertForbidden();
        $this->get(route('admin.activity-logs.show', $log))->assertForbidden();
        $this->withSession($this->sessionFor($admin, 'wcpu'))->get(route('admin.activity-logs.index'))->assertForbidden();
        $this->get(route('admin.activity-logs.show', $log))->assertForbidden();
    }

    public function test_filters_pagination_local_date_boundaries_and_escaped_output(): void
    {
        $admin = $this->user();
        $this->asUser($admin);
        for ($i = 0; $i < 23; $i++) {
            app(MswdoActivityLogger::class)->record('updated', 'Senior Citizens', 'Updated record');
        }
        app(MswdoActivityLogger::class)->record('export', 'Financial Assistance', '<script>alert(1)</script>');
        $log = MswdoActivityLog::latest('id')->first();
        DB::table('mswdo_activity_logs')->where('id', $log->id)->update(['created_at' => '2026-11-09 16:30:00']);
        $this->get(route('admin.activity-logs.index'))->assertOk()->assertSee('Page 1 of 5')->assertViewHas('logs', fn ($logs) => $logs->count() === 5)->assertDontSee('<script>alert(1)</script>', false);
        $this->get(route('admin.activity-logs.index', ['activity_type' => 'updated', 'page' => 2]))
            ->assertOk()->assertViewHas('logs', fn ($logs) => $logs->currentPage() === 2 && $logs->count() === 5 && $logs->total() === 23 && str_contains($logs->nextPageUrl(), 'activity_type=updated'));
        $this->get(route('admin.activity-logs.index', ['search' => 'Financial', 'role' => 'admin', 'user' => $admin->id, 'activity_type' => 'export', 'from' => '2026-11-10', 'to' => '2026-11-10']))
            ->assertOk()->assertViewHas('logs', fn ($logs) => $logs->total() === 1)->assertSee('12:30:00 AM');
        $this->get(route('admin.activity-logs.index', ['activity_type' => 'logout']))->assertOk()->assertSee('No activities match');
    }

    public function test_logs_have_no_write_routes_and_model_rejects_changes(): void
    {
        $this->asUser($this->user());
        app(MswdoActivityLogger::class)->record('login', 'Authentication', 'Logged in');
        $log = MswdoActivityLog::first();
        $this->assertFalse($log->update(['description' => 'Tampered']));
        $this->assertFalse($log->delete());
        $this->assertSame('Logged in', $log->fresh()->description);
        $this->put(route('admin.activity-logs.show', $log), ['description' => 'Tampered'])->assertStatus(405);
        $this->delete(route('admin.activity-logs.show', $log))->assertStatus(405);
    }

    public function test_actor_snapshot_survives_account_role_change_and_deletion(): void
    {
        $actor = $this->user();
        $this->asUser($actor);
        $actor->update(['role' => 'staff']);
        $this->assertSame('admin', MswdoActivityLog::first()->role);
        $actor->delete();
        $this->assertSame(2, MswdoActivityLog::count());
        $this->assertSame('staff', MswdoActivityLog::latest('id')->first()->role);
    }

    public function test_date_filter_accepts_either_boundary_and_rejects_reversed_range(): void
    {
        $admin = $this->user();
        $this->asUser($admin);
        $this->get(route('admin.activity-logs.index', ['to' => '2026-11-10']))->assertOk();
        $this->get(route('admin.activity-logs.index', ['from' => '2026-11-10']))->assertOk();
        $this->getJson(route('admin.activity-logs.index', ['from' => '2026-11-10', 'to' => '2026-11-09']))->assertUnprocessable()->assertJsonValidationErrors('to');
    }
}
