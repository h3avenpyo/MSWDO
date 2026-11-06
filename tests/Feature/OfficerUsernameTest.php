<?php

namespace Tests\Feature;

use App\Http\Requests\Admin\Officer\StoreOfficerRequest;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class OfficerUsernameTest extends TestCase
{
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'username_test',
            'database.connections.username_test' => ['driver' => 'sqlite', 'database' => ':memory:'],
            'database.connections.wcpu' => ['driver' => 'sqlite', 'database' => ':memory:'],
        ]);
        app('db')->purge('wcpu');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('status');
            $table->string('phone')->nullable();
            $table->string('signature_position')->nullable();
            $table->string('signature_image')->nullable();
            $table->timestamps();
        });
        $migration = require database_path('migrations/2026_11_06_000001_add_username_to_mswdo_users_table.php');
        $migration->up();
        Schema::connection('wcpu')->create('vawc_users', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('username')->nullable();
        });
    }

    public function test_officer_can_login_with_username_and_email(): void
    {
        $user = $this->officer();
        foreach ([$user->username, $user->email] as $identifier) {
            $this->post(route('admin.login'), [
                'email' => $identifier,
                'password' => 'Password123!',
            ])->assertRedirect(route('admin.social-case.dashboard'))
                ->assertSessionHas('admin_user_id', $user->id);
        }
    }

    public function test_inactive_officer_cannot_login_with_username(): void
    {
        $user = $this->officer();
        $user->update(['status' => 'inactive']);
        $this->post(route('admin.login'), [
            'email' => $user->username,
            'password' => 'Password123!',
        ])->assertSessionHas('account_deactivated', true);
        $this->assertNull(session('admin_user_id'));
    }

    public function test_username_validation_rejects_duplicates_in_both_account_sources(): void
    {
        $this->officer();
        app('db')->connection('wcpu')->table('vawc_users')->insert([
            'email' => 'department@example.com', 'username' => 'department.admin',
        ]);
        $rules = (new StoreOfficerRequest())->rules()['username'];
        foreach (['', 'ab', 'has spaces', 'jane.officer', 'department.admin'] as $username) {
            $this->assertTrue(Validator::make(compact('username'), ['username' => $rules])->fails());
        }
        $this->assertTrue(Validator::make(['username' => 'new.officer'], ['username' => $rules])->passes());
    }

    public function test_existing_officer_can_add_username_and_login(): void
    {
        $user = $this->officer();
        $user->update(['username' => null]);
        $password = $user->password;
        $this->put(route('admin.officers.update', $user->id), [
            'name' => $user->name, 'email' => $user->email,
            'username' => 'updated.officer', 'role' => 'social_worker',
            'status' => 'active',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.officers-directory'));
        $this->assertSame('updated.officer', $user->fresh()->username);
        $this->assertSame($password, $user->fresh()->password);
        $this->post(route('admin.login'), [
            'email' => 'updated.officer', 'password' => 'Password123!',
        ])->assertRedirect(route('admin.social-case.dashboard'));

        $this->put(route('admin.officers.update', $user->id), [
            'name' => $user->name, 'email' => $user->email,
            'username' => 'updated.officer', 'role' => 'social_worker',
        ])->assertSessionHasNoErrors();
    }

    private function officer(): User
    {
        return User::create([
            'name' => 'Jane Officer', 'email' => 'jane@example.com',
            'username' => 'jane.officer', 'password' => 'Password123!',
            'role' => 'social_worker', 'status' => 'active',
        ]);
    }
}
