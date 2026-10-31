<?php

namespace Tests\Feature;

use App\Models\Wcpu\{ActivityLog, CaseModel, User};
use App\Services\Wcpu\MultiroleActionNotifications;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\TestCase;

class CaseReferralNotificationTest extends TestCase
{
    public function test_referral_notifications_follow_the_officers_departments(): void
    {
        config(['database.connections.wcpu' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge('wcpu');
        $schema = Schema::connection('wcpu');
        $schema->create('vawc_users', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->string('department');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
        });
        $schema->create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->string('event')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->text('properties')->nullable();
            $table->timestamps();
        });
        DB::connection('wcpu')->table('vawc_users')->insert([
            ['id' => 1, 'position' => 'Administrator', 'department' => 'BCPC', 'is_active' => true],
            ['id' => 2, 'position' => 'Administrator', 'department' => 'VAWC', 'is_active' => true],
            ['id' => 3, 'position' => 'Desk Officer', 'department' => 'BCPC', 'is_active' => true],
            ['id' => 4, 'position' => 'Administrator', 'department' => 'BCPC', 'is_active' => false],
        ]);
        $case = new CaseModel();
        $case->forceFill(['id' => 10, 'type' => 'BCPC', 'case_number' => 'BCPC-10', 'case_designation' => 1]);
        foreach (['BCPC' => [1], 'VAWC' => [2], 'BCPC, VAWC' => [1, 2]] as $department => $expected) {
            $actor = new User();
            $actor->forceFill(['id' => 20, 'position' => 'Desk Officer', 'department' => $department]);
            MultiroleActionNotifications::recordCaseReferred($case, $actor, 'REF-10');
            $entry = ActivityLog::latest('id')->first();
            $this->assertSame($expected, $entry->properties['recipient_ids']);
            $this->assertSame(10, $entry->subject_id);
            $this->assertSame('REF-10', $entry->properties['referral_number']);
            MultiroleActionNotifications::recordCaseReferred($case, $actor);
            $directEntry = ActivityLog::latest('id')->first();
            $this->assertSame($expected, $directEntry->properties['recipient_ids']);
            $this->assertSame('', $directEntry->properties['referral_number']);
            $this->assertSame('Case BCPC-10 was referred.', $directEntry->description);
        }
        DB::disconnect('wcpu');
    }
}
