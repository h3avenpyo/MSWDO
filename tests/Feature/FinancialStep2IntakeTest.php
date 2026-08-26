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
    private User $step1User;
    private User $step2Officer;

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

        $this->step1User = User::firstOrCreate(
            ['email' => 'step1_user_test@mwsdo.test'],
            [
                'name' => 'Step 1 Officer Test',
                'password' => bcrypt('password123'),
                'role' => 'financialstep1',
                'is_active' => true,
            ]
        );

        $this->step2Officer = User::firstOrCreate(
            ['email' => 'step2_officer_test@mwsdo.test'],
            [
                'name' => 'Step 2 Officer Test',
                'password' => bcrypt('Step2Pass!'),
                'role' => 'financialstep2',
                'is_active' => true,
            ]
        );
    }

    public function test_unauthorized_step1_user_cannot_access_step2_and_is_redirected_to_step1(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get(route('admin.financial.financialstep2'));

        $response->assertRedirect(route('admin.financial.financialstep1'));
        $response->assertSessionHas('step2_auth_required', true);
    }

    public function test_submitting_invalid_credentials_fails_and_keeps_user_on_step1(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->post(route('admin.financial.step2.authenticate'), [
            'email' => $this->step2Officer->email,
            'password' => 'WrongPassword',
        ]);

        $response->assertRedirect(route('admin.financial.financialstep1'));
        $response->assertSessionHas('step2_auth_error');
        $response->assertSessionMissing('financial_step2_authorized');
    }

    public function test_submitting_valid_credentials_authorizes_step2_access(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->post(route('admin.financial.step2.authenticate'), [
            'email' => $this->step2Officer->email,
            'password' => 'Step2Pass!',
        ]);

        $response->assertRedirect(route('admin.financial.financialstep2'));
        $response->assertSessionHas('financial_step2_authorized', true);

        // Subsequent GET request succeeds with session authorization
        $step2Response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2'));

        $step2Response->assertStatus(200);
    }

    public function test_locking_step2_revokes_authorization_and_redirects_to_step1(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
            'financial_step2_authorized' => true,
        ])->post(route('admin.financial.step2.lock'));

        $response->assertRedirect(route('admin.financial.financialstep1'));
        $response->assertSessionMissing('financial_step2_authorized');
    }

    public function test_step1_data_isolation_does_not_display_step2_grant_amounts(): void
    {
        $uniqueCtrl = 'MSWDO-' . date('Y') . '-' . rand(10000, 99999);

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'IsolatedFirst',
            'beneficiary_last_name' => 'IsolatedLast',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_barangay' => 'Biluso',
            'recommended_amount' => 9999.00,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get(route('admin.financial.financialstep1', ['search' => $uniqueCtrl]));

        $response->assertStatus(200);
        $response->assertSee($uniqueCtrl);
        $response->assertSee('IsolatedFirst IsolatedLast');
        $response->assertDontSee('9,999.00');
        $response->assertDontSee('Forward to Step 2 Verification');
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
        $response->assertViewHas('recentIntakes');
    }

    public function test_store_intake_sheet_strictly_uses_current_date_on_backend(): void
    {
        $uniqueCtrl = 'MSWDO-' . date('Y') . '-' . rand(10000, 99999);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->post(route('admin.beneficiary-intake.store'), [
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => '2020-01-01', // Attempted tamper with past date
            'beneficiary_first_name' => 'AutoDate',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1995-06-15',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'PWD',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill',
        ]);

        $intake = BeneficiaryIntake::where('control_number', $uniqueCtrl)->first();
        $this->assertNotNull($intake);
        $this->assertEquals(Carbon::today()->format('Y-m-d'), $intake->date_processed->format('Y-m-d'));
    }

    public function test_update_intake_sheet_does_not_modify_existing_date_processed(): void
    {
        $uniqueCtrl = 'MSWDO-' . date('Y') . '-' . rand(10000, 99999);

        $intake = BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => '2025-05-15',
            'beneficiary_first_name' => 'OriginalDate',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1995-06-15',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'PWD',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->put(route('admin.beneficiary-intake.update', $intake), [
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => '2026-12-31', // Attempted tamper with future date
            'beneficiary_first_name' => 'UpdatedName',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1995-06-15',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'PWD',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill',
        ]);

        $intake->refresh();
        $this->assertEquals('UpdatedName', $intake->beneficiary_first_name);
        $this->assertEquals('2025-05-15', $intake->date_processed->format('Y-m-d'));
    }

    public function test_financial_step1_strictly_displays_only_intakes_processed_today(): void
    {
        $todayCtrl = 'MSWDO-TODAY-' . rand(10000, 99999);
        $yesterdayCtrl = 'MSWDO-YEST-' . rand(10000, 99999);
        $tomorrowCtrl = 'MSWDO-TOMO-' . rand(10000, 99999);

        // Today intake
        BeneficiaryIntake::create([
            'control_number' => $todayCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'TodayBeneficiary',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
        ]);

        // Yesterday intake
        BeneficiaryIntake::create([
            'control_number' => $yesterdayCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::yesterday()->format('Y-m-d'),
            'beneficiary_first_name' => 'YesterdayBeneficiary',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
        ]);

        // Tomorrow intake
        BeneficiaryIntake::create([
            'control_number' => $tomorrowCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::tomorrow()->format('Y-m-d'),
            'beneficiary_first_name' => 'TomorrowBeneficiary',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
        ]);

        // Normal request
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get(route('admin.financial.financialstep1'));

        $response->assertStatus(200);
        $response->assertSee($todayCtrl);
        $response->assertDontSee($yesterdayCtrl);
        $response->assertDontSee($tomorrowCtrl);

        // Attempting to bypass using ?all=1 or ?date=
        $bypassResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get(route('admin.financial.financialstep1', ['all' => 1, 'date' => Carbon::yesterday()->format('Y-m-d')]));

        $bypassResponse->assertStatus(200);
        $bypassResponse->assertSee($todayCtrl);
        $bypassResponse->assertDontSee($yesterdayCtrl);
        $bypassResponse->assertDontSee($tomorrowCtrl);
    }
}

