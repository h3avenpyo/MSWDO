<?php

namespace Tests\Feature;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Services\Financial\FinancialDuplicateChecker;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FinancialDuplicateCheckerTest extends TestCase
{
    use DatabaseTransactions;

    private FinancialDuplicateChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = new FinancialDuplicateChecker();
    }

    public function test_previous_beneficiary_cannot_apply_as_beneficiary_within_six_months(): void
    {
        $uniqueCtrl = 'TEST-' . uniqid();

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::now()->subMonths(2)->format('Y-m-d'),
            'beneficiary_first_name' => 'JuanTest',
            'beneficiary_last_name' => 'DelaCruzTest',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_street_address' => 'Sample Street',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09123456789',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'JuanTest',
            'beneficiary_last_name' => 'DelaCruzTest',
            'beneficiary_birthday' => '1990-01-01',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertTrue($result['is_duplicate']);
        $this->assertCount(1, $result['matches']);
        $this->assertEquals($uniqueCtrl, $result['matches'][0]['control_number']);
    }

    public function test_previous_representative_CAN_apply_as_beneficiary_within_six_months(): void
    {
        $uniqueCtrl = 'TEST-' . uniqid();

        // Create an intake where MariaRepTest was strictly a Representative (not the Beneficiary)
        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'beneficiary_first_name' => 'PedroOtherPatient',
            'beneficiary_last_name' => 'SantosOther',
            'beneficiary_birthday' => '1950-01-01',
            'has_representative' => true,
            'rep_first_name' => 'MariaRepTest',
            'rep_last_name' => 'SantosRepTest',
            'rep_birthday' => '1985-05-15',
            'beneficiary_street_address' => 'Sample Street',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09123456789',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Now MariaRepTest applies as the primary Beneficiary
        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'MariaRepTest',
            'beneficiary_last_name' => 'SantosRepTest',
            'beneficiary_birthday' => '1985-05-15',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        // Should NOT be blocked because she was only a representative previously!
        $this->assertFalse($result['is_duplicate']);
        $this->assertEmpty($result['matches']);
    }

    public function test_allows_beneficiary_older_than_six_months(): void
    {
        $uniqueCtrl = 'TEST-' . uniqid();

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::now()->subMonths(7)->format('Y-m-d'),
            'beneficiary_first_name' => 'JuanOldTest',
            'beneficiary_last_name' => 'DelaCruzOldTest',
            'beneficiary_birthday' => '1990-01-01',
            'beneficiary_street_address' => 'Sample Street',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09123456789',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'JuanOldTest',
            'beneficiary_last_name' => 'DelaCruzOldTest',
            'beneficiary_birthday' => '1990-01-01',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertFalse($result['is_duplicate']);
        $this->assertEmpty($result['matches']);
    }

    public function test_blocks_same_person_as_beneficiary_and_representative(): void
    {
        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'SamePerson',
            'beneficiary_last_name' => 'SameLastName',
            'beneficiary_birthday' => '1990-01-01',
            'has_representative' => true,
            'rep_first_name' => 'SamePerson',
            'rep_last_name' => 'SameLastName',
            'rep_birthday' => '1990-01-01',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertTrue($result['is_duplicate']);
        $this->assertStringContainsString('Representative cannot be the exact same person', $result['warning_message']);
    }
}
