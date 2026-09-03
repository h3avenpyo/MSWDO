<?php

namespace Tests\Feature;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialStep2PayrollTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $step1User;
    private User $step2Officer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_payroll_test@mwsdo.test'],
            [
                'name' => 'Financial Admin Payroll Test',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->step1User = User::firstOrCreate(
            ['email' => 'step1_payroll_test@mwsdo.test'],
            [
                'name' => 'Step 1 Officer Payroll Test',
                'password' => bcrypt('password123'),
                'role' => 'financialstep1',
                'is_active' => true,
            ]
        );

        $this->step2Officer = User::firstOrCreate(
            ['email' => 'step2_payroll_test@mwsdo.test'],
            [
                'name' => 'Step 2 Officer Payroll Test',
                'password' => bcrypt('Step2Pass!'),
                'role' => 'financialstep2',
                'is_active' => true,
            ]
        );
    }

    public function test_unauthorized_user_cannot_access_payroll_generation(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get(route('admin.financial.financialstep2.payroll'));

        $response->assertRedirect(route('admin.financial.financialstep1'));
        $response->assertSessionHas('step2_auth_required', true);
    }

    public function test_authorized_step2_officer_can_access_payroll_generation(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll'));

        $response->assertStatus(200);
        $response->assertSee('Step 2: Payroll Generation');
        $response->assertSee('Generate Payroll');
    }

    public function test_payroll_generation_displays_today_intakes_and_allows_encoding_amount(): void
    {
        $todayCtrl = 'MSWDO-PAYROLL-TODAY-' . rand(10000, 99999);

        $intake = BeneficiaryIntake::create([
            'control_number' => $todayCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'Maria',
            'beneficiary_middle_name' => 'Santos',
            'beneficiary_last_name' => 'Dela Cruz',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_contact_number' => '09171234567',
            'beneficiary_category' => 'Indigent Resident',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical Needs',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => null,
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll'));

        $response->assertStatus(200);
        $response->assertSee($todayCtrl);
        $response->assertSee('Maria Santos Dela Cruz');
        $response->assertSee('Required'); // Status indicator

        // Test updating amount for this intake via AJAX
        $updateResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.update-amount'), [
            'intake_id' => $intake->id,
            'recommended_amount' => 3500.00,
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'success' => true,
            'intake_id' => $intake->id,
            'recommended_amount' => 3500.00,
            'formatted_amount' => '₱3,500.00',
        ]);

        $intake->refresh();
        $this->assertEquals(3500.00, (float) $intake->recommended_amount);
    }

    public function test_bulk_update_intake_amounts_persists_successfully(): void
    {
        $ctrl1 = 'MSWDO-BULK-1-' . rand(10000, 99999);
        $ctrl2 = 'MSWDO-BULK-2-' . rand(10000, 99999);

        $intake1 = BeneficiaryIntake::create([
            'control_number' => $ctrl1,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'Juan',
            'beneficiary_last_name' => 'Luna',
            'beneficiary_barangay' => 'Biluso',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $intake2 = BeneficiaryIntake::create([
            'control_number' => $ctrl2,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'Emilio',
            'beneficiary_last_name' => 'Aguinaldo',
            'beneficiary_barangay' => 'Kaong',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Burial',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.bulk-update-amounts'), [
            'amounts' => [
                $intake1->id => 2000.00,
                $intake2->id => 5000.00,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'updated_count' => 2,
        ]);

        $intake1->refresh();
        $intake2->refresh();
        $this->assertEquals(2000.00, (float) $intake1->recommended_amount);
        $this->assertEquals(5000.00, (float) $intake2->recommended_amount);
    }

    public function test_representative_name_rule_treats_beneficiary_as_representative_when_no_separate_rep(): void
    {
        // 1. Intake with Beneficiary only (no representative)
        $selfRepCtrl = 'MSWDO-SELF-REP-' . rand(10000, 99999);
        BeneficiaryIntake::create([
            'control_number' => $selfRepCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'Andres',
            'beneficiary_middle_name' => 'Castro',
            'beneficiary_last_name' => 'Bonifacio',
            'beneficiary_barangay' => 'Balite I',
            'beneficiary_contact_number' => '09181112222',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 4000.00,
        ]);

        // 2. Intake with authorized separate representative
        $withRepCtrl = 'MSWDO-WITH-REP-' . rand(10000, 99999);
        BeneficiaryIntake::create([
            'control_number' => $withRepCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'Gabriela',
            'beneficiary_last_name' => 'Silang',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09193334444',
            'has_representative' => true,
            'rep_first_name' => 'Diego',
            'rep_last_name' => 'Silang',
            'rep_relationship' => 'Spouse',
            'rep_contact_number' => '09195556666',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 6000.00,
        ]);

        // Test Print Payroll view
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll.print'));

        $response->assertStatus(200);
        $response->assertSee('PAYROLL FOR FINANCIAL ASSISTANCE');
        $response->assertSee('NAME OF REPRESENTATIVE');
        $response->assertSee('NAME OF BENEFICIARY/BENEFICIARIES');
        $response->assertSee('SIGNATURE');

        // Verify Self-rep intake has Andres Bonifacio in Representative column and Beneficiary column
        $response->assertSee($selfRepCtrl);
        $response->assertSee('ANDRES CASTRO BONIFACIO');

        // Verify With-rep intake has Diego Silang as representative and Gabriela Silang as beneficiary
        $response->assertSee($withRepCtrl);
        $response->assertSee('DIEGO SILANG');
        $response->assertSee('GABRIELA SILANG');

        // Verify amounts are formatted
        $response->assertSee('4,000.00');
        $response->assertSee('6,000.00');
    }

    public function test_generate_payroll_marks_intakes_as_processed_and_removes_from_generation_list(): void
    {
        $ctrl1 = 'MSWDO-GEN-1-' . rand(10000, 99999);
        $ctrl2 = 'MSWDO-GEN-2-' . rand(10000, 99999);
        $todayStr = Carbon::today()->format('Y-m-d');

        $intake1 = BeneficiaryIntake::create([
            'control_number' => $ctrl1,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Apolinario',
            'beneficiary_last_name' => 'Mabini',
            'beneficiary_barangay' => 'Kaong',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => false,
        ]);

        $intake2 = BeneficiaryIntake::create([
            'control_number' => $ctrl2,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Melchora',
            'beneficiary_last_name' => 'Aquino',
            'beneficiary_barangay' => 'Tibig',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Burial',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 7000.00,
            'is_payroll_generated' => false,
        ]);

        // Step 1: Intakes are visible in Payroll Generation before generating
        $beforeGenResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll'));

        $beforeGenResponse->assertStatus(200);
        $beforeGenResponse->assertSee($ctrl1);
        $beforeGenResponse->assertSee($ctrl2);

        // Step 2: Trigger Generate Payroll endpoint
        $genResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.generate'), [
            'date' => $todayStr,
        ]);

        $genResponse->assertStatus(200);
        $genResponse->assertJson([
            'success' => true,
        ]);

        $intake1->refresh();
        $intake2->refresh();
        $this->assertTrue((bool) $intake1->is_payroll_generated);
        $this->assertTrue((bool) $intake2->is_payroll_generated);
        $this->assertEquals($todayStr, $intake1->payroll_date->format('Y-m-d'));

        // Step 3: Processed intakes are removed from generation list (table becomes empty)
        $afterGenResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll'));

        $afterGenResponse->assertStatus(200);
        $afterGenResponse->assertDontSee($ctrl1);
        $afterGenResponse->assertDontSee($ctrl2);
        $afterGenResponse->assertSee('No Pending Intakes for Payroll Generation');

        // Step 4: Processed intakes are now accessible in Payroll Records
        $recordsResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['date' => $todayStr]));

        $recordsResponse->assertStatus(200);
        $recordsResponse->assertSee($ctrl1);
        $recordsResponse->assertSee($ctrl2);
        $recordsResponse->assertSee('Apolinario Mabini');
        $recordsResponse->assertSee('Melchora Aquino');
    }

    public function test_duplicate_payroll_generation_is_prevented_when_no_unprocessed_intakes(): void
    {
        $uniqueDateStr = '2026-12-15';

        // Create intake already processed into payroll on a distinct date
        BeneficiaryIntake::create([
            'control_number' => 'MSWDO-ALREADY-GEN-' . rand(10000, 99999),
            'client_type' => 'New',
            'date_processed' => $uniqueDateStr,
            'beneficiary_first_name' => 'Marcelo',
            'beneficiary_last_name' => 'Del Pilar',
            'beneficiary_barangay' => 'Acacia',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 4500.00,
            'is_payroll_generated' => true,
            'payroll_generated_at' => Carbon::now(),
            'payroll_date' => $uniqueDateStr,
        ]);

        // Attempting to generate payroll again for uniqueDateStr should be rejected because no unprocessed intakes exist
        $genResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.generate'), [
            'date' => $uniqueDateStr,
        ]);

        $genResponse->assertStatus(422);
        $genResponse->assertJson([
            'success' => false,
        ]);
    }

    public function test_multiple_separate_payrolls_can_be_generated_on_the_same_day(): void
    {
        $todayStr = Carbon::today()->format('Y-m-d');
        $ctrlBatch1A = 'MSWDO-B1-A-' . rand(10000, 99999);
        $ctrlBatch1B = 'MSWDO-B1-B-' . rand(10000, 99999);

        // 1. Morning Intakes on Same Day
        $intake1A = BeneficiaryIntake::create([
            'control_number' => $ctrlBatch1A,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Emilio',
            'beneficiary_last_name' => 'Aguinaldo',
            'beneficiary_barangay' => 'Kaong',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 4000.00,
            'is_payroll_generated' => false,
        ]);

        $intake1B = BeneficiaryIntake::create([
            'control_number' => $ctrlBatch1B,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Gregoria',
            'beneficiary_last_name' => 'De Jesus',
            'beneficiary_barangay' => 'Biluso',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Burial',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 6000.00,
            'is_payroll_generated' => false,
        ]);

        // Generate Payroll 1
        $genResponse1 = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.generate'), [
            'date' => $todayStr,
        ]);

        $genResponse1->assertStatus(200);
        $genResponse1->assertJson([
            'success' => true,
        ]);

        $intake1A->refresh();
        $intake1B->refresh();
        $this->assertTrue((bool) $intake1A->is_payroll_generated);
        $this->assertTrue((bool) $intake1B->is_payroll_generated);
        $this->assertNotNull($intake1A->payroll_record_id);

        // 2. Later on the Same Day: New intakes are added
        $ctrlBatch2A = 'MSWDO-B2-A-' . rand(10000, 99999);
        $ctrlBatch2B = 'MSWDO-B2-B-' . rand(10000, 99999);

        $intake2A = BeneficiaryIntake::create([
            'control_number' => $ctrlBatch2A,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Antonio',
            'beneficiary_last_name' => 'Luna',
            'beneficiary_barangay' => 'Biga I',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Educational',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => false,
        ]);

        $intake2B = BeneficiaryIntake::create([
            'control_number' => $ctrlBatch2B,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Teresa',
            'beneficiary_last_name' => 'Magbanua',
            'beneficiary_barangay' => 'Acacia',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 3500.00,
            'is_payroll_generated' => false,
        ]);

        // 3. View Payroll Generation page: ONLY new intakes appear (Batch 1 intakes are NOT combined or visible)
        $payrollGenPageResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll'));

        $payrollGenPageResponse->assertStatus(200);
        $payrollGenPageResponse->assertSee($ctrlBatch2A);
        $payrollGenPageResponse->assertSee($ctrlBatch2B);
        $payrollGenPageResponse->assertDontSee($ctrlBatch1A);
        $payrollGenPageResponse->assertDontSee($ctrlBatch1B);

        // 4. Generate Payroll 2
        $genResponse2 = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->postJson(route('admin.financial.financialstep2.payroll.generate'), [
            'date' => $todayStr,
        ]);

        $genResponse2->assertStatus(200);
        $genResponse2->assertJson([
            'success' => true,
        ]);

        $intake2A->refresh();
        $intake2B->refresh();
        $this->assertTrue((bool) $intake2A->is_payroll_generated);
        $this->assertTrue((bool) $intake2B->is_payroll_generated);
        $this->assertNotEquals($intake1A->payroll_record_id, $intake2A->payroll_record_id);

        // 5. Check Payroll Records Page: Both individual records exist and can be accessed
        $payrollRecord1Id = $intake1A->payroll_record_id;
        $payrollRecord2Id = $intake2A->payroll_record_id;

        $record1Response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['payroll_id' => $payrollRecord1Id]));

        $record1Response->assertStatus(200);
        $record1Response->assertSee($ctrlBatch1A);
        $record1Response->assertDontSee($ctrlBatch2A);

        $record2Response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['payroll_id' => $payrollRecord2Id]));

        $record2Response->assertStatus(200);
        $record2Response->assertSee($ctrlBatch2A);
        $record2Response->assertDontSee($ctrlBatch1A);

        // Check records page for today's date: both separate payroll records appear directly on the page
        $allRecordsToday = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll-records', ['date' => $todayStr]));

        $allRecordsToday->assertStatus(200);
        $allRecordsToday->assertSee($ctrlBatch1A);
        $allRecordsToday->assertSee($ctrlBatch2A);
    }

    public function test_print_payroll_action_button_and_printable_view_work(): void
    {
        $todayStr = Carbon::today()->format('Y-m-d');
        $ctrl = 'MSWDO-PRN-' . rand(10000, 99999);

        $intake = BeneficiaryIntake::create([
            'control_number' => $ctrl,
            'client_type' => 'New',
            'date_processed' => $todayStr,
            'beneficiary_first_name' => 'Juan',
            'beneficiary_last_name' => 'Luna',
            'beneficiary_barangay' => 'Acacia',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 5000.00,
            'is_payroll_generated' => false,
        ]);

        // 1. Generator page has Print Payroll action button
        $genPage = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll', ['date' => $todayStr]));

        $genPage->assertStatus(200);
        $genPage->assertSee('Print Payroll');

        // 2. Print Payroll view returns 200 with the intake details
        $printResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.payroll.print', ['date' => $todayStr]));

        $printResponse->assertStatus(200);
        $printResponse->assertSee($ctrl);
        $printResponse->assertSee('JUAN LUNA');
        $printResponse->assertSee('5,000.00');
    }
}
