<?php

namespace Tests\Feature;

use App\Models\Wcpu\{ActivityLog, QuarterlyReport, User};
use App\Services\Wcpu\MultiroleActionNotifications;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\TestCase;

class QuarterlyReportGenerationNotificationTest extends TestCase
{
    public function test_each_generation_notifies_only_active_matching_admins(): void
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
        $actor = new User();
        $actor->forceFill(['id' => 20, 'position' => 'Desk Officer', 'department' => 'BCPC, VAWC']);
        foreach (['BCPC' => [1], 'VAWC' => [2]] as $type => $recipients) {
            $report = new QuarterlyReport();
            $report->forceFill(['id' => 10, 'type' => $type, 'quarter' => 4, 'year' => 2026]);
            foreach ([1, 2] as $generation) {
                MultiroleActionNotifications::recordQuarterlyReportGenerated($report, $actor);
                $entry = ActivityLog::latest('id')->first();
                $this->assertSame($recipients, $entry->properties['recipient_ids']);
                $this->assertTrue($entry->properties['quarterly_report_generated']);
                $this->assertSame('quarterly report', $entry->properties['kind']);
                $this->assertSame(10, $entry->subject_id);
            }
        }
        $this->assertSame(4, ActivityLog::count());
        $actor->position = 'Administrator';
        MultiroleActionNotifications::recordQuarterlyReportGenerated($report, $actor);
        $this->assertSame(4, ActivityLog::count());
        DB::disconnect('wcpu');
    }
}
