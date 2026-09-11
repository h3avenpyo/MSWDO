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
        $response->assertSee('Roberto Gomez SantosTest');
        $response->assertSee('Elena SantosTest');
        $response->assertSee('Biluso');
        $response->assertSee('5,000.00');
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

    public function test_financial_step2_masterlist_strictly_displays_only_intakes_processed_today(): void
    {
        $todayCtrl = 'MSWDO-STEP2-TODAY-' . rand(10000, 99999);
        $yesterdayCtrl = 'MSWDO-STEP2-YEST-' . rand(10000, 99999);

        // Today intake
        BeneficiaryIntake::create([
            'control_number' => $todayCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'TodayStep2Client',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_sex' => 'Male',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'PWD',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Yesterday intake
        BeneficiaryIntake::create([
            'control_number' => $yesterdayCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::yesterday()->format('Y-m-d'),
            'beneficiary_first_name' => 'YesterdayStep2Client',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_sex' => 'Male',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'PWD',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.financialstep2'));

        $response->assertStatus(200);
        $response->assertSee($todayCtrl);
        $response->assertDontSee($yesterdayCtrl);
    }

    public function test_step2_all_intakes_page_is_accessible_to_authorized_step2_users(): void
    {
        $uniqueCtrl = 'MSWDO-STEP2-' . rand(10000, 99999);

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'AllIntakesFirst',
            'beneficiary_last_name' => 'AllIntakesLast',
            'beneficiary_birthday' => '1985-05-15',
            'beneficiary_sex' => 'Male',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'PWD',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medicine',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.financialstep2.all-intakes'));

        $response->assertStatus(200);
        $response->assertSee($uniqueCtrl);
        $response->assertSee('AllIntakesFirst AllIntakesLast');
        $response->assertSee('Step 2: All General Intakes');
        $response->assertSee('btn-view-intake');
        $response->assertDontSee('btn-message-beneficiary');
        $response->assertDontSee('financialstep2-sms.js');
        $response->assertDontSee('smsModal');
    }

    public function test_unauthorized_step1_user_cannot_access_step2_all_intakes(): void
    {
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step1User->id,
            'admin_user_name' => $this->step1User->name,
            'admin_user_role' => 'financialstep1',
        ])->get(route('admin.financial.financialstep2.all-intakes'));

        $response->assertRedirect(route('admin.financial.financialstep1'));
    }

    public function test_step2_all_intakes_search_and_filtering(): void
    {
        $uniqueCtrl = 'MSWDO-FILTER-' . rand(10000, 99999);

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_first_name' => 'SpecificSearchPerson',
            'beneficiary_last_name' => 'TargetLast',
            'beneficiary_birthday' => '1980-01-01',
            'beneficiary_sex' => 'Female',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_category' => 'Solo Parents',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Emergency Aid',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.financialstep2.all-intakes', [
            'search' => 'SpecificSearchPerson',
            'barangay' => 'Acacia',
        ]));

        $response->assertStatus(200);
        $response->assertSee($uniqueCtrl);
        $response->assertSee('SpecificSearchPerson TargetLast');
    }

    public function test_step2_all_intakes_monthly_filtering_and_pagination(): void
    {
        $currentMonthCtrl = 'MSWDO-MTH-CURR-' . rand(10000, 99999);
        $pastMonthCtrl = 'MSWDO-MTH-PAST-' . rand(10000, 99999);

        // Intake processed in 2026-09
        BeneficiaryIntake::create([
            'control_number' => $currentMonthCtrl,
            'client_type' => 'New',
            'date_processed' => '2026-09-10',
            'created_at' => '2026-09-10 08:00:00',
            'beneficiary_first_name' => 'CurrentMonthUser',
            'beneficiary_last_name' => 'MonthFilterTest',
            'beneficiary_birthday' => '1992-04-12',
            'beneficiary_sex' => 'Female',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'Solo Parents',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Education',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Intake processed in 2025-05
        $pastIntake = BeneficiaryIntake::create([
            'control_number' => $pastMonthCtrl,
            'client_type' => 'New',
            'date_processed' => '2025-05-15',
            'beneficiary_first_name' => 'PastMonthUser',
            'beneficiary_last_name' => 'MonthFilterTest',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_sex' => 'Male',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'PWD',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);
        $pastIntake->timestamps = false;
        $pastIntake->created_at = '2025-05-15 08:00:00';
        $pastIntake->save();

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.financialstep2.all-intakes', [
            'month' => '2026-09',
        ]));

        $response->assertStatus(200);
        $response->assertSee($currentMonthCtrl);
        $response->assertSee('CurrentMonthUser');
        $response->assertDontSee($pastMonthCtrl);
        $response->assertSee('15 records / page');
        $response->assertSee('Month &amp; Year', false);
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
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Single',
            'beneficiary_contact_number' => '09123456789',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
            'beneficiary_category' => 'PWD',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response->assertRedirect();
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
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Single',
            'beneficiary_contact_number' => '09123456789',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
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
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Single',
            'beneficiary_contact_number' => '09123456789',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
            'beneficiary_category' => 'PWD',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response->assertRedirect();
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
            'beneficiary_sex' => 'Male',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Yesterday intake
        BeneficiaryIntake::create([
            'control_number' => $yesterdayCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::yesterday()->format('Y-m-d'),
            'beneficiary_first_name' => 'YesterdayBeneficiary',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_sex' => 'Male',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Tomorrow intake
        BeneficiaryIntake::create([
            'control_number' => $tomorrowCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::tomorrow()->format('Y-m-d'),
            'beneficiary_first_name' => 'TomorrowBeneficiary',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_sex' => 'Male',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'submitted_to' => 'MSWDO Silang Main Office',
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

    public function test_financial_step2_statistics_calculates_medical_concerns(): void
    {
        $uniqueNum = rand(100000, 999999);
        BeneficiaryIntake::create([
            'control_number' => 'MED-TEST-1-' . $uniqueNum,
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_name' => 'Medical Patient One',
            'beneficiary_birthday' => '1985-05-15',
            'beneficiary_sex' => 'Female',
            'beneficiary_street_address' => 'Purok 2',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_city' => 'Silang',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'medical_conditions' => ['Dialysis', 'Hypertension'],
            'recommended_amount' => 5000,
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        BeneficiaryIntake::create([
            'control_number' => 'MED-TEST-2-' . $uniqueNum,
            'date_processed' => Carbon::today()->format('Y-m-d'),
            'beneficiary_name' => 'Medical Patient Two',
            'beneficiary_birthday' => '1975-08-20',
            'beneficiary_sex' => 'Male',
            'beneficiary_street_address' => 'Purok 3',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_city' => 'Silang',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medical',
            'medical_conditions' => ['Dialysis'],
            'recommended_amount' => 4000,
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->step2Officer->id,
            'admin_user_name' => $this->step2Officer->name,
            'admin_user_role' => 'financialstep2',
            'financial_step2_authorized' => true,
            'financial_step2_auth_time' => time(),
        ])->get(route('admin.financial.financialstep2.statistics'));

        $response->assertStatus(200);
        $response->assertViewHas('medicalRanked');
        $response->assertViewHas('topMedicalConcern');
        $response->assertViewHas('topMedicalConcernCount');

        $medicalRanked = $response->viewData('medicalRanked');
        $this->assertIsArray($medicalRanked);

        // Find Dialysis in results
        $dialysis = collect($medicalRanked)->firstWhere('concern', 'Dialysis');
        $this->assertNotNull($dialysis);
        $this->assertGreaterThanOrEqual(2, $dialysis['beneficiaries']);
        $this->assertGreaterThanOrEqual(9000, $dialysis['amount']);

        $response->assertSee('medicalBarChart');
        $response->assertSee('Most Common Medical Concerns');
    }

    public function test_contact_number_must_be_strictly_11_digits(): void
    {
        $uniqueCtrl = 'MSWDO-' . date('Y') . '-' . rand(10000, 99999);

        // Test with invalid contact numbers: less than 11 digits, more than 11 digits, non-numeric
        $invalidNumbers = [
            '0912345678',       // 10 digits
            '091234567890',     // 12 digits
            '0912abc3456',     // contains letters
            '0912-345-678',     // contains dashes
            '0912 345 678',     // contains spaces
            '+63912345678',     // contains plus sign
        ];

        foreach ($invalidNumbers as $invalidNumber) {
            $response = $this->withSession([
                'admin_logged_in' => true,
                'admin_user_id' => $this->admin->id,
                'admin_user_name' => $this->admin->name,
                'admin_user_role' => 'admin',
            ])->from(route('admin.beneficiary-intake.create'))->post(route('admin.beneficiary-intake.store'), [
                'control_number' => $uniqueCtrl,
                'client_type' => 'New',
                'beneficiary_first_name' => 'InvalidContact',
                'beneficiary_last_name' => 'Tester',
                'beneficiary_birthday' => '1995-06-15',
                'beneficiary_sex' => 'Male',
                'beneficiary_civil_status' => 'Single',
                'beneficiary_contact_number' => $invalidNumber,
                'beneficiary_street_address' => 'Purok 1',
                'beneficiary_barangay' => 'Biluso',
                'beneficiary_city' => 'Silang',
                'beneficiary_province' => 'Cavite',
                'beneficiary_category' => 'PWD',
                'has_representative' => false,
                'service_provided' => 'Financial Assistance',
                'purpose' => 'Hospital Bill',
                'submitted_to' => 'MSWDO Silang Main Office',
            ]);

            $response->assertSessionHasErrors(['beneficiary_contact_number']);
        }

        // Test with valid exactly 11 digits
        $validCtrl = 'MSWDO-' . date('Y') . '-' . rand(10000, 99999);
        $validResponse = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->post(route('admin.beneficiary-intake.store'), [
            'control_number' => $validCtrl,
            'client_type' => 'New',
            'beneficiary_first_name' => 'ValidContact',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1995-06-15',
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Single',
            'beneficiary_contact_number' => '09123456789',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
            'beneficiary_category' => 'PWD',
            'has_representative' => false,
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $validResponse->assertSessionHasNoErrors();
        $validResponse->assertRedirect();
        $this->assertDatabaseHas('beneficiary_intakes', [
            'control_number' => $validCtrl,
            'beneficiary_contact_number' => '09123456789',
        ]);
    }

    public function test_beneficiary_intakes_masterlist_monthly_filtering(): void
    {
        $septCtrl = 'MSWDO-SEPT-' . rand(10000, 99999);
        $augCtrl = 'MSWDO-AUG-' . rand(10000, 99999);

        // September intake
        BeneficiaryIntake::create([
            'control_number' => $septCtrl,
            'client_type' => 'New',
            'date_processed' => '2026-09-05',
            'beneficiary_first_name' => 'SeptClient',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_sex' => 'Female',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_category' => 'Solo Parents',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Medicine',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // August intake
        BeneficiaryIntake::create([
            'control_number' => $augCtrl,
            'client_type' => 'New',
            'date_processed' => '2026-08-20',
            'beneficiary_first_name' => 'AugClient',
            'beneficiary_last_name' => 'Tester',
            'beneficiary_birthday' => '1991-02-02',
            'beneficiary_sex' => 'Male',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_category' => 'PWD',
            'service_provided' => 'Financial Assistance',
            'purpose' => 'Hospital Bill',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Filter for September 2026
        $response = $this->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.beneficiary-intake.index', [
            'month' => '2026-09',
        ]));

        $response->assertStatus(200);
        $response->assertSee($septCtrl);
        $response->assertSee('SeptClient');
        $response->assertDontSee($augCtrl);
        $response->assertSee('Month:');
        $response->assertSee('September 2026');
    }
}


