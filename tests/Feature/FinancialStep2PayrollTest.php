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
}
