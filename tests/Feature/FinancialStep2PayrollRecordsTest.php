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
}
