<?php

namespace Tests\Feature;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialArchiveTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $step1User;
    private User $step2Officer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_archive_test@mwsdo.test'],
            [
                'name' => 'Archive Admin Test',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->step1User = User::firstOrCreate(
            ['email' => 'step1_archive_user@mwsdo.test'],
            [
                'name' => 'Step 1 Officer Test',
                'password' => bcrypt('password123'),
                'role' => 'financialstep1',
                'is_active' => true,
            ]
        );

        $this->step2Officer = User::firstOrCreate(
            ['email' => 'step2_archive_officer@mwsdo.test'],
            [
                'name' => 'Step 2 Officer Test',
                'password' => bcrypt('Step2Pass!'),
                'role' => 'financialstep2',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test archiving a Step 1 intake safely removes it from active list and moves it to Step 1 Archive.
     */
    public function test_archive_step1_intake_removes_from_active_list_and_appears_in_archive(): void
    {
        $today = Carbon::today();

        $intake = BeneficiaryIntake::create([
            'control_number' => 'INTAKE-ARC-001',
            'beneficiary_first_name' => 'Juan',
            'beneficiary_last_name' => 'Dela Cruz',
            'beneficiary_sex' => 'Male',
            'beneficiary_age' => 45,
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_category' => 'Indigent Resident',
            'date_processed' => $today->format('Y-m-d'),
            'recommended_assistance_type' => 'Medical Assistance',
            'purpose' => 'Medicine support',
            'encoder_user_id' => $this->step1User->id,
            'is_archived' => false,
        ]);

        // Verify it appears on active Step 1
        $activeResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get('/admin/financial/financialstep1');

        $activeResponse->assertStatus(200);
        $activeResponse->assertSee('INTAKE-ARC-001');

        // Post archive action
        $archiveResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->post("/admin/financial/step1/archive/{$intake->id}", [
            'reason' => 'Completed transaction',
        ]);

        $archiveResponse->assertRedirect();
        
        $intake->refresh();
        $this->assertTrue($intake->is_archived);
        $this->assertEquals('step1', $intake->archive_module);
        $this->assertEquals('Completed transaction', $intake->archive_reason);
        $this->assertNotNull($intake->archived_at);

        // Check active list no longer includes the record
        $activeResponseAfter = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get('/admin/financial/financialstep1');

        $activeResponseAfter->assertDontSee('INTAKE-ARC-001');

        // Check Step 1 Archive page displays the record
        $archivePageResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get('/admin/financial/step1/archive');

        $archivePageResponse->assertStatus(200);
        $archivePageResponse->assertSee('INTAKE-ARC-001');
        $archivePageResponse->assertSee('Juan Dela Cruz');
    }

    /**
     * Test restoring an archived Step 1 intake safely returns it to the active list.
     */
    public function test_restore_step1_intake_returns_record_to_active_intakes(): void
    {
        $today = Carbon::today();

        $intake = BeneficiaryIntake::create([
            'control_number' => 'INTAKE-ARC-RESTORE',
            'beneficiary_first_name' => 'Maria',
            'beneficiary_last_name' => 'Santos',
            'beneficiary_sex' => 'Female',
            'beneficiary_age' => 38,
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'Solo Parents',
            'date_processed' => $today->format('Y-m-d'),
            'recommended_assistance_type' => 'Financial Assistance',
            'purpose' => 'Educational assistance',
            'encoder_user_id' => $this->step1User->id,
            'is_archived' => true,
            'archived_at' => now(),
            'archive_module' => 'step1',
            'archive_reason' => 'Archived by mistake',
        ]);

        // Restore action
        $restoreResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->post("/admin/financial/step1/restore/{$intake->id}");

        $restoreResponse->assertRedirect();

        $intake->refresh();
        $this->assertFalse($intake->is_archived);
        $this->assertNull($intake->archived_at);
        $this->assertNull($intake->archive_module);
        $this->assertEquals('INTAKE-ARC-RESTORE', $intake->control_number);

        // Check it is back on active Step 1
        $activeResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get('/admin/financial/financialstep1');

        $activeResponse->assertStatus(200);
        $activeResponse->assertSee('INTAKE-ARC-RESTORE');
    }

    /**
     * Test archiving a Step 2 financial record removes it from Step 2 active masterlist and excludes from Payroll Generation.
     */
    public function test_archive_step2_record_excludes_from_active_masterlist_and_payroll_generation(): void
    {
        $today = Carbon::today();

        $intake = BeneficiaryIntake::create([
            'control_number' => 'INTAKE-STEP2-ARC-001',
            'beneficiary_first_name' => 'Pedro',
            'beneficiary_last_name' => 'Penduko',
            'beneficiary_sex' => 'Male',
            'beneficiary_age' => 50,
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_category' => 'Indigent Resident',
            'date_processed' => $today->format('Y-m-d'),
            'recommended_amount' => 3000.00,
            'is_payroll_generated' => false,
            'claim_status' => null,
            'encoder_user_id' => $this->step1User->id,
            'is_archived' => false,
        ]);

        // Verify Step 2 sees it initially
        $step2Response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authenticated' => true,
        ])->get('/admin/financial/financialstep2/all-intakes');

        $step2Response->assertStatus(200);
        $step2Response->assertSee('INTAKE-STEP2-ARC-001');

        // Archive from Step 2
        $archiveResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authenticated' => true,
        ])->post("/admin/financial/financialstep2/archive/{$intake->id}", [
            'reason' => 'Liquidated and finished',
        ]);

        $archiveResponse->assertRedirect();

        $intake->refresh();
        $this->assertTrue($intake->is_archived);
        $this->assertEquals('step2', $intake->archive_module);

        // Check Step 2 Active Masterlist does NOT see it
        $step2ResponseAfter = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authenticated' => true,
        ])->get('/admin/financial/financialstep2/all-intakes');

        $step2ResponseAfter->assertDontSee('INTAKE-STEP2-ARC-001');

        // Check Payroll Generation does NOT include it
        $payrollResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authenticated' => true,
        ])->get('/admin/financial/financialstep2/payroll');

        $payrollResponse->assertStatus(200);
        $payrollResponse->assertDontSee('INTAKE-STEP2-ARC-001');

        // Check Step 2 Archive page displays the record
        $archivePageResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authenticated' => true,
        ])->get('/admin/financial/financialstep2/archive');

        $archivePageResponse->assertStatus(200);
        $archivePageResponse->assertSee('INTAKE-STEP2-ARC-001');
        $archivePageResponse->assertSee('Pedro Penduko');
    }

    /**
     * Test restoring a Step 2 archived record returns it to active Step 2 masterlist.
     */
    public function test_restore_step2_record_returns_to_active_masterlist(): void
    {
        $today = Carbon::today();

        $intake = BeneficiaryIntake::create([
            'control_number' => 'INTAKE-STEP2-RESTORE',
            'beneficiary_first_name' => 'Clara',
            'beneficiary_last_name' => 'Batumbakal',
            'beneficiary_sex' => 'Female',
            'beneficiary_age' => 62,
            'beneficiary_barangay' => 'Balite I',
            'beneficiary_category' => 'Senior Citizen',
            'date_processed' => $today->format('Y-m-d'),
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => true,
            'claim_status' => 'Claimed',
            'encoder_user_id' => $this->step1User->id,
            'is_archived' => true,
            'archived_at' => now(),
            'archive_module' => 'step2',
        ]);

        // Restore action
        $restoreResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authenticated' => true,
        ])->post("/admin/financial/financialstep2/restore/{$intake->id}");

        $restoreResponse->assertRedirect();

        $intake->refresh();
        $this->assertFalse($intake->is_archived);
        $this->assertNull($intake->archived_at);

        // Check it appears on active Step 2 masterlist
        $step2Response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authenticated' => true,
        ])->get('/admin/financial/financialstep2/all-intakes');

        $step2Response->assertStatus(200);
        $step2Response->assertSee('INTAKE-STEP2-RESTORE');
    }
}
