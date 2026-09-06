<?php

namespace Tests\Feature;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialStep2PayrollRecordsTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $step1User;
    private User $step2Officer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_payroll_records_test@mswdo.test'],
            [
                'name' => 'Admin Payroll Records Tester',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->step1User = User::firstOrCreate(
            ['email' => 'step1_payroll_records_test@mswdo.test'],
            [
                'name' => 'Step 1 Officer Records Tester',
                'password' => bcrypt('password123'),
                'role' => 'financialstep1',
                'is_active' => true,
            ]
        );

        $this->step2Officer = User::firstOrCreate(
            ['email' => 'step2_payroll_records_test@mswdo.test'],
            [
                'name' => 'Step 2 Officer Records Tester',
                'password' => bcrypt('Step2Pass!'),
                'role' => 'financialstep2',
                'is_active' => true,
            ]
        );
    }

    public function test_unauthorized_user_cannot_access_payroll_records(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get(route('admin.financial.financialstep2.payroll-records'));

        $response->assertRedirect(route('admin.financial.financialstep1'));
        $response->assertSessionHas('step2_auth_required', true);
    }

    public function test_authorized_step2_officer_can_access_payroll_records_and_see_required_fields(): void
    {
        $ctrl1 = 'MSWDO-REC-TEST-' . rand(10000, 99999);
        $ctrl2 = 'MSWDO-REC-TEST-' . rand(10000, 99999);
        $targetDateStr = Carbon::today()->format('Y-m-d');

        // Self-representative intake (beneficiary is representative)
        $intake1 = BeneficiaryIntake::create([
            'control_number' => $ctrl1,
            'client_type' => 'New',
            'date_processed' => $targetDateStr,
            'beneficiary_first_name' => 'Clara',
            'beneficiary_last_name' => 'Reyes',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_contact_number' => '09171112233',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 4500.00,
            'is_payroll_generated' => true,
        ]);

        // With separate authorized representative
        $intake2 = BeneficiaryIntake::create([
            'control_number' => $ctrl2,
            'client_type' => 'New',
            'date_processed' => $targetDateStr,
            'beneficiary_first_name' => 'Pedro',
            'beneficiary_last_name' => 'Penduko',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_contact_number' => '09182223344',
            'has_representative' => true,
            'rep_first_name' => 'Juana',
            'rep_last_name' => 'Penduko',
            'rep_contact_number' => '09193334455',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Burial Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => true,
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['date' => $targetDateStr]));

        $response->assertStatus(200);
        $response->assertSee('Step 2: Payroll Records');

        // Verify table column headers
        $response->assertSee('Name of Representative');
        $response->assertSee('Name of Beneficiary');
        $response->assertSee('Barangay');
        $response->assertSee('Contact Number');
        $response->assertSee('Amount of Financial Assistance');
        $response->assertSee('Payroll Date');

        // Verify Representative Name Rule
        // Self-rep intake: Clara Reyes appears as Representative and Beneficiary
        $response->assertSee($ctrl1);
        $response->assertSee('Clara Reyes');
        $response->assertSee('Biga I');
        $response->assertSee('09171112233');
        $response->assertSee('4,500.00');

        // Separate-rep intake: Juana Penduko as Representative, Pedro Penduko as Beneficiary
        $response->assertSee($ctrl2);
        $response->assertSee('Juana Penduko');
        $response->assertSee('Pedro Penduko');
        $response->assertSee('Biluso');
        $response->assertSee('5,000.00');

        // Verify link to open existing print view
        $expectedPrintUrl = route('admin.financial.financialstep2.payroll.print', ['date' => $targetDateStr]);
        $response->assertSee($expectedPrintUrl);
    }

    public function test_filter_and_search_payroll_records_by_date(): void
    {
        $date1 = '2026-10-15';
        $date2 = '2026-10-20';

        $intake1 = BeneficiaryIntake::create([
            'control_number' => 'MSWDO-REC-D1-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $date1,
            'beneficiary_first_name' => 'Jose',
            'beneficiary_last_name' => 'Rizal',
            'beneficiary_barangay' => 'Kaong',
            'beneficiary_contact_number' => '09111111111',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 3000.00,
            'is_payroll_generated' => true,
        ]);

        $intake2 = BeneficiaryIntake::create([
            'control_number' => 'MSWDO-REC-D2-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $date2,
            'beneficiary_first_name' => 'Andres',
            'beneficiary_last_name' => 'Bonifacio',
            'beneficiary_barangay' => 'Tibig',
            'beneficiary_contact_number' => '09222222222',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Burial Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 6000.00,
            'is_payroll_generated' => true,
        ]);

        // Filter by date1
        $dateFilterResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['date' => $date1]));

        $dateFilterResponse->assertStatus(200);
        $dateFilterResponse->assertSee('Jose Rizal');
        $dateFilterResponse->assertDontSee('Andres Bonifacio');

        // Search by keyword within date2
        $searchResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', [
            'date' => $date2,
            'search' => 'Bonifacio'
        ]));

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Andres Bonifacio');
        $searchResponse->assertDontSee('Jose Rizal');
    }

    public function test_open_selected_payroll_uses_existing_print_view(): void
    {
        $todayStr = Carbon::today()->format('Y-m-d');

        $intake = BeneficiaryIntake::create([
            'control_number' => 'MSWDO-PRINT-REC-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Gabriela',
            'beneficiary_last_name' => 'Silang',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09333333333',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Educational Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 8000.00,
            'is_payroll_generated' => true,
        ]);

        // Access the existing print view directly
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll.print', ['date' => $todayStr]));

        $response->assertStatus(200);
        $response->assertSee('PAYROLL FOR FINANCIAL ASSISTANCE');
        $response->assertSee('GABRIELA SILANG');
        $response->assertSee('8,000.00');
        $response->assertSee('Print Legal Landscape Payroll');
    }

    public function test_records_page_displays_each_separately_generated_payroll_as_an_individual_record(): void
    {
        $targetDateStr = '2026-11-20';

        // Create Payroll Record 1 on target date
        $payroll1 = \App\Models\Financial\FinancialPayrollRecord::create([
            'payroll_number' => 'PAYROLL-20261120-001-' . rand(1000, 9999),
            'payroll_date' => $targetDateStr,
            'batch_number' => 1,
            'total_beneficiaries' => 1,
            'total_amount' => 3000.00,
            'status' => 'Completed',
        ]);

        $intake1 = BeneficiaryIntake::create([
            'control_number' => 'MSWDO-REC-SAME-1-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $targetDateStr,
            'beneficiary_first_name' => 'Diego',
            'beneficiary_last_name' => 'Silang',
            'beneficiary_barangay' => 'Kaong',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 3000.00,
            'is_payroll_generated' => true,
            'payroll_date' => $targetDateStr,
            'payroll_record_id' => $payroll1->id,
        ]);

        // Create Payroll Record 2 on the SAME date
        $payroll2 = \App\Models\Financial\FinancialPayrollRecord::create([
            'payroll_number' => 'PAYROLL-20261120-002-' . rand(1000, 9999),
            'payroll_date' => $targetDateStr,
            'batch_number' => 2,
            'total_beneficiaries' => 1,
            'total_amount' => 5000.00,
            'status' => 'Completed',
        ]);

        $intake2 = BeneficiaryIntake::create([
            'control_number' => 'MSWDO-REC-SAME-2-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $targetDateStr,
            'beneficiary_first_name' => 'Melchora',
            'beneficiary_last_name' => 'Ramos',
            'beneficiary_barangay' => 'Biluso',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Burial',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => true,
            'payroll_date' => $targetDateStr,
            'payroll_record_id' => $payroll2->id,
        ]);

        // 1. Visit records page: both separate individual payroll records are listed in the records list
        $recordsResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['payroll_id' => $payroll1->id]));

        $recordsResponse->assertStatus(200);
        $recordsResponse->assertSee($payroll1->payroll_number);
        $recordsResponse->assertSee('Diego Silang');
        $recordsResponse->assertDontSee('Melchora Ramos');
        $recordsResponse->assertSee('3,000.00');

        // 2. View Payroll Record 2
        $record2Response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', [
            'payroll_id' => $payroll2->id,
        ]));

        $record2Response->assertStatus(200);
        $record2Response->assertSee($payroll2->payroll_number);
        $record2Response->assertSee('Melchora Ramos');
        $record2Response->assertDontSee('Diego Silang');
        $record2Response->assertSee('5,000.00');

        // 3. Print view for Record 2
        $printRecord2Response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll.print', [
            'payroll_id' => $payroll2->id,
        ]));

        $printRecord2Response->assertStatus(200);
        $printRecord2Response->assertSee('PAYROLL FOR FINANCIAL ASSISTANCE');
        $printRecord2Response->assertSee('MELCHORA RAMOS');
        $printRecord2Response->assertDontSee('DIEGO SILANG');

        // 4. Visit records page for target date without payroll_id: BOTH generated payrolls are displayed directly on the page with collapsible tables
        $allRecordsOnPageResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['date' => $targetDateStr]));

        $allRecordsOnPageResponse->assertStatus(200);
        $allRecordsOnPageResponse->assertSee('Diego Silang');
        $allRecordsOnPageResponse->assertSee('Melchora Ramos');
        $allRecordsOnPageResponse->assertSee('Expand All');
        $allRecordsOnPageResponse->assertSee('Collapse All');
    }

    public function test_payroll_generation_automatically_sets_claim_status_to_unclaimed(): void
    {
        $todayStr = Carbon::today()->format('Y-m-d');
        $ctrl1 = 'CLAIM-GEN-' . rand(10000, 99999);
        $ctrl2 = 'CLAIM-GEN-' . rand(10000, 99999);

        $intake1 = BeneficiaryIntake::create([
            'control_number' => $ctrl1,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Jose',
            'beneficiary_last_name' => 'Rizal',
            'beneficiary_barangay' => 'Biga I',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => false,
        ]);

        $intake2 = BeneficiaryIntake::create([
            'control_number' => $ctrl2,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Andres',
            'beneficiary_last_name' => 'Bonifacio',
            'beneficiary_barangay' => 'Biluso',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Burial Assistance',
            'recommended_amount' => 3000.00,
            'is_payroll_generated' => false,
        ]);

        $generateResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.generate'), [
            'date' => $todayStr,
        ]);

        $generateResponse->assertStatus(200);
        $generateResponse->assertJson(['success' => true]);

        $intake1->refresh();
        $intake2->refresh();

        $this->assertTrue($intake1->is_payroll_generated);
        $this->assertTrue($intake2->is_payroll_generated);
        $this->assertSame('Unclaimed', $intake1->claim_status);
        $this->assertSame('Unclaimed', $intake2->claim_status);
        $this->assertNull($intake1->claimed_at);
        $this->assertNull($intake2->claimed_at);
    }

    public function test_step2_officer_can_mark_intake_as_claimed_and_only_selected_intake_updates(): void
    {
        $todayStr = Carbon::today()->format('Y-m-d');
        $ctrl1 = 'CLAIM-UPD-' . rand(10000, 99999);
        $ctrl2 = 'CLAIM-UPD-' . rand(10000, 99999);

        $intake1 = BeneficiaryIntake::create([
            'control_number' => $ctrl1,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Apolinario',
            'beneficiary_last_name' => 'Mabini',
            'beneficiary_barangay' => 'Biga I',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 4000.00,
            'is_payroll_generated' => true,
            'claim_status' => 'Unclaimed',
        ]);

        $intake2 = BeneficiaryIntake::create([
            'control_number' => $ctrl2,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Emilio',
            'beneficiary_last_name' => 'Aguinaldo',
            'beneficiary_barangay' => 'Biluso',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Educational Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 2500.00,
            'is_payroll_generated' => true,
            'claim_status' => 'Unclaimed',
        ]);

        // Mark ONLY intake 1 as Claimed
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.intake.claim-status', ['id' => $intake1->id]), [
            'status' => 'Claimed',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'intake_id' => $intake1->id,
            'claim_status' => 'Claimed',
        ]);

        $intake1->refresh();
        $intake2->refresh();

        // Selected intake is Claimed
        $this->assertSame('Claimed', $intake1->claim_status);
        $this->assertNotNull($intake1->claimed_at);
        $this->assertSame($this->step2Officer->name, $intake1->claimed_by);

        // Other intake strictly remains Unclaimed
        $this->assertSame('Unclaimed', $intake2->claim_status);
        $this->assertNull($intake2->claimed_at);
        $this->assertNull($intake2->claimed_by);

        // Can revert intake 1 back to Unclaimed
        $revertResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.intake.claim-status', ['id' => $intake1->id]), [
            'status' => 'Unclaimed',
        ]);

        $revertResponse->assertStatus(200);
        $revertResponse->assertJson([
            'success' => true,
            'claim_status' => 'Unclaimed',
        ]);

        $intake1->refresh();
        $this->assertSame('Unclaimed', $intake1->claim_status);
        $this->assertNull($intake1->claimed_at);
        $this->assertNull($intake1->claimed_by);
    }

    public function test_payroll_records_view_displays_claim_status_and_filters_by_status(): void
    {
        $todayStr = Carbon::today()->format('Y-m-d');
        $ctrl1 = 'FILTER-CLAIM-' . rand(10000, 99999);
        $ctrl2 = 'FILTER-CLAIM-' . rand(10000, 99999);

        $intakeClaimed = BeneficiaryIntake::create([
            'control_number' => $ctrl1,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Gabriela',
            'beneficiary_last_name' => 'Silang',
            'beneficiary_barangay' => 'Biga I',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical Assistance',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => false,
        ]);

        $intakeUnclaimed = BeneficiaryIntake::create([
            'control_number' => $ctrl2,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Teresa',
            'beneficiary_last_name' => 'Magbanua',
            'beneficiary_barangay' => 'Biluso',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Burial Assistance',
            'recommended_amount' => 3000.00,
            'is_payroll_generated' => false,
        ]);

        // Generate payroll
        $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.generate'), [
            'date' => $todayStr,
        ]);

        // Mark Gabriela Silang as Claimed
        $intakeClaimed->refresh();
        $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.intake.claim-status', ['id' => $intakeClaimed->id]), [
            'status' => 'Claimed',
        ]);

        // 1. Visit records page: should see Claim Status column and both statuses
        $pageResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['date' => $todayStr]));

        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Claim Status');
        $pageResponse->assertSee('Gabriela Silang');
        $pageResponse->assertSee('Teresa Magbanua');
        $pageResponse->assertSee('Claimed');
        $pageResponse->assertSee('Unclaimed');

        // 2. Filter by Claimed
        $claimedFilterResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', [
            'date' => $todayStr,
            'claim_status' => 'Claimed',
        ]));

        $claimedFilterResponse->assertStatus(200);
        $claimedFilterResponse->assertSee('Gabriela Silang');
        $claimedFilterResponse->assertDontSee('Teresa Magbanua');

        // 3. Filter by Unclaimed
        $unclaimedFilterResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', [
            'date' => $todayStr,
            'claim_status' => 'Unclaimed',
        ]));

        $unclaimedFilterResponse->assertStatus(200);
        $unclaimedFilterResponse->assertSee('Teresa Magbanua');
        $unclaimedFilterResponse->assertDontSee('Gabriela Silang');
    }

    public function test_intake_model_accessor_returns_correct_step2_status_across_stages(): void
    {
        $todayStr = Carbon::today()->format('Y-m-d');

        // Stage 1: No amount assigned -> Pending Amount
        $intakePending = BeneficiaryIntake::create([
            'control_number' => 'STATUS-STAGE-1-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Juan',
            'beneficiary_last_name' => 'Luna',
            'beneficiary_barangay' => 'Biga I',
            'service_provided' => 'Financial Assistance',
            'recommended_amount' => null,
            'is_payroll_generated' => false,
        ]);

        $this->assertSame('Pending Amount', $intakePending->step2_status);

        // Stage 2: Amount encoded and generated in payroll -> Unclaimed
        $intakeUnclaimed = BeneficiaryIntake::create([
            'control_number' => 'STATUS-STAGE-2-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Marcelo',
            'beneficiary_last_name' => 'Del Pilar',
            'beneficiary_barangay' => 'Biluso',
            'service_provided' => 'Financial Assistance',
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => true,
            'claim_status' => 'Unclaimed',
        ]);

        $this->assertSame('Unclaimed', $intakeUnclaimed->step2_status);

        // Stage 3: Beneficiary received assistance -> Claimed
        $intakeClaimed = BeneficiaryIntake::create([
            'control_number' => 'STATUS-STAGE-3-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Graciano',
            'beneficiary_last_name' => 'Lopez Jaena',
            'beneficiary_barangay' => 'Kaong',
            'service_provided' => 'Financial Assistance',
            'recommended_amount' => 4000.00,
            'is_payroll_generated' => true,
            'claim_status' => 'Claimed',
        ]);

        $this->assertSame('Claimed', $intakeClaimed->step2_status);
    }

    public function test_step2_masterlist_and_all_intakes_display_and_filter_pending_amount_status(): void
    {
        $todayStr = Carbon::today()->format('Y-m-d');

        $intakeWithoutAmount = BeneficiaryIntake::create([
            'control_number' => 'MST-PENDING-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Lapu',
            'beneficiary_last_name' => 'Lapu',
            'beneficiary_barangay' => 'Biga I',
            'service_provided' => 'Financial Assistance',
            'recommended_amount' => null,
            'is_payroll_generated' => false,
        ]);

        $intakeWithPayroll = BeneficiaryIntake::create([
            'control_number' => 'MST-UNCLAIMED-' . rand(1000, 9999),
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Francisco',
            'beneficiary_last_name' => 'Dagohoy',
            'beneficiary_barangay' => 'Biluso',
            'service_provided' => 'Financial Assistance',
            'recommended_amount' => 3500.00,
            'is_payroll_generated' => true,
            'claim_status' => 'Unclaimed',
        ]);

        // 1. Step 2 Masterlist page
        $masterlistResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2'));

        $masterlistResponse->assertStatus(200);
        $masterlistResponse->assertSee('Pending Amount');
        $masterlistResponse->assertSee('Lapu Lapu');

        // Filter masterlist by Pending Amount
        $pendingFilterResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2', ['status' => 'pending_amount']));

        $pendingFilterResponse->assertStatus(200);
        $pendingFilterResponse->assertSee('Lapu Lapu');
        $pendingFilterResponse->assertDontSee('Francisco Dagohoy');

        // 2. Step 2 All Intakes page
        $allIntakesResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.all-intakes'));

        $allIntakesResponse->assertStatus(200);
        $allIntakesResponse->assertSee('Pending Amount');
        $allIntakesResponse->assertSee('Lapu Lapu');

        // Filter all intakes by Pending Amount
        $allIntakesPendingFilter = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.all-intakes', ['status' => 'pending_amount']));

        $allIntakesPendingFilter->assertStatus(200);
        $allIntakesPendingFilter->assertSee('Lapu Lapu');
        $allIntakesPendingFilter->assertDontSee('Francisco Dagohoy');
    }
}
