<?php

namespace Tests\Feature;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricalFinancialIntakeTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_historical_test@mswdo.test'],
            [
                'name' => 'Admin Historical Tester',
                'password' => bcrypt('Password123!'),
                'role' => UserRole::Admin,
                'status' => UserStatus::Active,
            ]
        );
    }

    public function test_unauthenticated_user_cannot_access_historical_data_entry(): void
    {
        $response = $this->get(route('admin.historical-data.financial-intake'));
        $response->assertRedirect(route('admin.login.form'));
    }

    public function test_admin_can_access_historical_financial_intake_page(): void
    {
        $response = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.historical-data.financial-intake'));

        $response->assertStatus(200);
        $response->assertSee('Historical Financial Intake Encoding');
        $response->assertSee('Original Intake Date');
        $response->assertSee('Actual Financial Assistance Amount Provided');
    }

    public function test_validation_requires_original_intake_date_and_financial_amount(): void
    {
        $response = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->post(route('admin.historical-data.financial-intake.store'), [
            // Omit required fields
        ]);

        $response->assertSessionHasErrors([
            'date_processed',
            'recommended_amount',
            'beneficiary_last_name',
            'beneficiary_first_name',
            'beneficiary_street_address',
            'beneficiary_barangay',
            'beneficiary_contact_number',
            'beneficiary_birthday',
            'beneficiary_sex',
            'beneficiary_civil_status',
        ]);
    }

    public function test_original_intake_date_cannot_be_in_the_future(): void
    {
        $futureDate = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->post(route('admin.historical-data.financial-intake.store'), [
            'date_processed' => $futureDate,
            'recommended_amount' => 3000,
            'beneficiary_last_name' => 'Santos',
            'beneficiary_first_name' => 'Pedro',
            'beneficiary_street_address' => 'Purok 3',
            'beneficiary_barangay' => 'Biga I',
            'beneficiary_contact_number' => '09171234567',
            'beneficiary_birthday' => '1985-05-10',
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Married',
        ]);

        $response->assertSessionHasErrors(['date_processed']);
    }

    public function test_admin_can_successfully_encode_historical_intake_record(): void
    {
        $historicalDate = '2023-04-15';
        $controlNumber = 'MSWDO-2023-' . rand(10000, 99999);
        $amount = 7500.00;

        $response = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->post(route('admin.historical-data.financial-intake.store'), [
            'control_number' => $controlNumber,
            'client_type' => 'New',
            'date_processed' => $historicalDate,
            'recommended_amount' => $amount,
            'claim_status' => 'Claimed',
            'beneficiary_first_name' => 'Crisanto',
            'beneficiary_last_name' => 'Reyes',
            'beneficiary_middle_name' => 'Bautista',
            'beneficiary_street_address' => 'Purok 4, Ilaya',
            'beneficiary_barangay' => 'Biluso',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
            'beneficiary_region' => 'Region IV-A',
            'beneficiary_contact_number' => '09181234567',
            'beneficiary_birthday' => '1970-08-20',
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Married',
            'beneficiary_occupation' => 'Carpenter',
            'beneficiary_monthly_salary' => 8000,
            'beneficiary_categories' => ['PWD', 'Indigent Resident'],
            'assistance_purpose' => 'Hospital Bill / Medical Needs',
            'has_representative' => false,
            'interviewed_by' => 'Officer Reyes',
            'reviewed_by' => 'Supervisor Gomez',
        ]);

        $intake = BeneficiaryIntake::where('control_number', $controlNumber)->first();
        $this->assertNotNull($intake, 'Historical intake record should be created in database');
        $response->assertRedirect(route('admin.beneficiary-intake.show', $intake));

        // Assert that historical date, amount, and relationships are preserved
        $this->assertTrue($intake->is_historical);
        $this->assertEquals('2023-04-15', $intake->date_processed->format('Y-m-d'));
        $this->assertEquals($amount, (float) $intake->recommended_amount);
        $this->assertEquals('Claimed', $intake->claim_status);
        $this->assertTrue($intake->is_payroll_generated);
        $this->assertEquals('2023-04-15', $intake->claiming_date->format('Y-m-d'));
        $this->assertEquals('2023-04-15', $intake->created_at->format('Y-m-d'));
        $this->assertEquals('Crisanto Bautista Reyes', $intake->beneficiary_full_name);
    }

    public function test_historical_record_appears_in_step1_all_intakes_and_step2_masterlist(): void
    {
        $historicalDate = '2023-05-20';
        $controlNumber = 'MSWDO-2023-' . rand(10000, 99999);

        $intake = BeneficiaryIntake::create([
            'control_number' => $controlNumber,
            'client_type' => 'New',
            'date_processed' => $historicalDate,
            'recommended_amount' => 5000.00,
            'claim_status' => 'Claimed',
            'is_payroll_generated' => true,
            'claiming_date' => $historicalDate,
            'is_historical' => true,
            'beneficiary_first_name' => 'Josefina',
            'beneficiary_last_name' => 'Mercado',
            'beneficiary_street_address' => 'Sitio Central',
            'beneficiary_barangay' => 'Carmen',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
            'beneficiary_region' => 'Region IV-A',
            'beneficiary_contact_number' => '09201234567',
            'beneficiary_birthday' => '1965-03-12',
            'beneficiary_sex' => 'Female',
            'beneficiary_civil_status' => 'Widowed',
            'service_provided' => 'Financial Assistance Intake',
            'purpose' => 'Medical Needs',
            'submitted_to' => 'MSWDO Silang Main Office',
            'created_at' => Carbon::parse($historicalDate)->setTime(9, 0, 0),
        ]);

        // Step 1 All Intakes masterlist search
        $step1Response = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.beneficiary-intake.index', ['search' => 'Mercado']));

        $step1Response->assertStatus(200);
        $step1Response->assertSee('Josefina Mercado');
        $step1Response->assertSee($controlNumber);

        // Step 2 All Intakes Masterlist search
        $step2Response = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.all-intakes', ['search' => 'Mercado']));

        $step2Response->assertStatus(200);
        $step2Response->assertSee('Josefina Mercado');
        $step2Response->assertSee($controlNumber);
        $step2Response->assertSee('5,000.00');

        // Step 2 Dashboard / Masterlist page: Total Masterlist Records stat card reflects the encoded intake
        $step2DashboardResponse = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2'));

        $step2DashboardResponse->assertStatus(200);
        $step2DashboardResponse->assertSee('Total Masterlist Records');

        // Step 2 Dashboard / Masterlist search finds the encoded record
        $step2SearchResponse = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2', ['search' => 'Mercado']));

        $step2SearchResponse->assertStatus(200);
        $step2SearchResponse->assertSee('Josefina Mercado');
        $step2SearchResponse->assertSee($controlNumber);

        // Step 2 Dashboard / Masterlist view with period=all displays the encoded record
        $step2AllPeriodResponse = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2', ['period' => 'all', 'search' => 'Mercado']));

        $step2AllPeriodResponse->assertStatus(200);
        $step2AllPeriodResponse->assertSee('Josefina Mercado');
        $step2AllPeriodResponse->assertSee($controlNumber);
        $step2AllPeriodResponse->assertSee('All Masterlist Records');
    }

    public function test_historical_record_is_correctly_included_in_monthly_statistics(): void
    {
        $historicalDate = '2023-06-10';
        $controlNumber = 'MSWDO-2023-' . rand(10000, 99999);

        BeneficiaryIntake::create([
            'control_number' => $controlNumber,
            'client_type' => 'New',
            'date_processed' => $historicalDate,
            'recommended_amount' => 6000.00,
            'claim_status' => 'Claimed',
            'is_payroll_generated' => true,
            'claiming_date' => $historicalDate,
            'is_historical' => true,
            'beneficiary_first_name' => 'Ramil',
            'beneficiary_last_name' => 'Aquino',
            'beneficiary_street_address' => 'Purok 1',
            'beneficiary_barangay' => 'Tibig',
            'beneficiary_city' => 'Silang',
            'beneficiary_province' => 'Cavite',
            'beneficiary_region' => 'Region IV-A',
            'beneficiary_contact_number' => '09391234567',
            'beneficiary_birthday' => '1980-01-15',
            'beneficiary_sex' => 'Male',
            'beneficiary_civil_status' => 'Married',
            'service_provided' => 'Financial Assistance Intake',
            'purpose' => 'Hospital Bill',
            'submitted_to' => 'MSWDO Silang Main Office',
            'created_at' => Carbon::parse($historicalDate)->setTime(9, 0, 0),
        ]);

        // Step 1 Statistics filtered by June 2023
        $stats1Response = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
        ])->get(route('admin.financial.financialstep1statistics', ['month' => '2023-06']));

        $stats1Response->assertStatus(200);
        $stats1Response->assertSee('June 2023');

        // Step 2 Statistics filtered by June 2023
        $stats2Response = $this->withSession([
            'admin_user_id' => $this->admin->id,
            'admin_user_name' => $this->admin->name,
            'admin_user_role' => 'admin',
            'financial_step2_authorized' => true,
        ])->get(route('admin.financial.financialstep2.statistics', ['month' => '2023-06']));

        $stats2Response->assertStatus(200);
        $stats2Response->assertSee('June 2023');
        $stats2Response->assertSee('6,000');
    }
}
