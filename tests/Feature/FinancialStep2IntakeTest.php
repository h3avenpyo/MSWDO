<?php

namespace Tests\Feature;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialStep2IntakeTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_financial_test@mwsdo.test'],
            [
                'name' => 'Financial Admin Test',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    public function test_financial_step2_masterlist_displays_all_step1_general_intake_records(): void
    {
        $uniqueCtrl = 'MSWDO-' . date('Y') . '-' . rand(10000, 99999);

        $intake = BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'Roberto',
            'beneficiary_last_name' => 'SantosTest',
            'beneficiary_middle_name' => 'Gomez',
            'beneficiary_birthday' => '1985-05-15',
            'beneficiary_age' => 41,
            'beneficiary_sex' => 'Male',
            'beneficiary_street_address' => '123 Rizal St',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
            'beneficiary_contact_number' => '09991234567',
            'beneficiary_category' => 'PWD',
            'has_representative' => true,
            'rep_first_name' => 'Elena',
            'rep_last_name' => 'SantosTest',
            'rep_relationship' => 'Spouse (Husband / Wife)',
            'recommended_amount' => 5000.00,
            'recommended_assistance_type' => 'Financial Assistance',
            'assistance_purpose' => 'Hospital Bill / Medical Needs',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill / Medical Needs',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.financialstep2'));

        $response->assertStatus(200);
        $response->assertSee($uniqueCtrl);
        $response->assertSee('Roberto SantosTest');
        $response->assertSee('Elena SantosTest');
        $response->assertSee('Biluso');
        $response->assertSee('5,000.00');
        $response->assertSee(route('admin.financial.financialstep2.process', $intake));
    }

    public function test_selecting_client_from_masterlist_opens_step2_processing_page(): void
    {
        $uniqueCtrl = 'MSWDO-' . date('Y') . '-' . rand(10000, 99999);

        $intake = BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'Returning',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'Maria',
            'beneficiary_last_name' => 'ClaraTest',
            'beneficiary_middle_name' => 'Santos',
            'beneficiary_birthday' => '1990-08-20',
            'beneficiary_age' => 36,
            'beneficiary_sex' => 'Female',
            'beneficiary_street_address' => '456 Mabini St',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
            'beneficiary_contact_number' => '09171234567',
            'beneficiary_category' => 'Solo Parents',
            'recommended_amount' => 4500.00,
            'recommended_assistance_type' => 'Medical Assistance',
            'assistance_purpose' => 'Medicine Purchase',
            'social_worker_assessment' => 'Client requires urgent maintenance medicine assistance.',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Medicine Purchase',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.financialstep2.process', $intake));

        $response->assertStatus(200);
        $response->assertSee($uniqueCtrl);
        $response->assertSee('Maria ClaraTest');
        $response->assertSee('456 Mabini St');
        $response->assertSee('Solo Parents');
        $response->assertSee('4,500.00');
        $response->assertSee('Client requires urgent maintenance medicine assistance.');
        $response->assertSee(route('admin.financial.financialstep2'));
    }

    public function test_financial_step2_masterlist_search_and_sorting(): void
    {
        $uniqueCtrl = 'MSWDO-' . date('Y') . '-' . rand(10000, 99999);

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'UniqueSearchFirst',
            'beneficiary_last_name' => 'UniqueSearchLast',
            'beneficiary_birthday' => '1992-03-10',
            'beneficiary_barangay' => 'Iba',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medicine',
            'submitted_to' => 'MSWDO Silang Main Office',
            'recommended_amount' => 3000.00,
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.financialstep2', [
            'search' => 'UniqueSearchFirst',
            'sort' => 'name_asc',
        ]));

        $response->assertStatus(200);
        $response->assertSee($uniqueCtrl);
        $response->assertSee('UniqueSearchFirst UniqueSearchLast');
    }

    public function test_financial_dashboard_calculates_dynamic_intake_metrics(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalIntakes');
        $response->assertViewHas('todayIntakes');
        $response->assertViewHas('step1Approved');
        $response->assertViewHas('readyForStep2');
        $response->assertViewHas('recentIntakes');
    }
}
