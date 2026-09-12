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

    public function test_does_not_trigger_on_incomplete_or_single_character_names(): void
    {
        // Only last name provided (first name empty)
        $res1 = $this->checker->checkDuplicate([
            'beneficiary_first_name' => '',
            'beneficiary_last_name' => 'SantosPartial',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);
        $this->assertFalse($res1['is_duplicate'], 'Should not flag duplicate when first name is empty');

        // Single character first name
        $res2 = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'M',
            'beneficiary_last_name' => 'SantosPartial',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);
        $this->assertFalse($res2['is_duplicate'], 'Should not flag duplicate on single-character name');
    }

    public function test_triggers_duplicate_when_identifying_names_match(): void
    {
        $uniqueCtrl = 'TEST-' . uniqid();

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'beneficiary_first_name' => 'MariaMatch',
            'beneficiary_last_name' => 'SantosMatch',
            'beneficiary_birthday' => '1988-03-15',
            'beneficiary_street_address' => 'Sample Street',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09123456789',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // When First Name & Last Name match an existing beneficiary record
        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'MariaMatch',
            'beneficiary_last_name' => 'SantosMatch',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertTrue($result['is_duplicate'], 'Should flag duplicate when client name matches previous intake within 6 months');
        $this->assertCount(1, $result['matches']);
    }

    public function test_does_not_trigger_when_birthday_is_different(): void
    {
        $uniqueCtrl = 'TEST-' . uniqid();

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'beneficiary_first_name' => 'EduardoDiff',
            'beneficiary_last_name' => 'RamosDiff',
            'beneficiary_birthday' => '1975-10-10',
            'beneficiary_street_address' => 'Sample Street',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09123456789',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Same name but different birthday (born in 1999 vs 1975)
        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'EduardoDiff',
            'beneficiary_last_name' => 'RamosDiff',
            'beneficiary_birthday' => '1999-04-20',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertFalse($result['is_duplicate'], 'Clients with different birthdays must not be flagged as duplicates');
        $this->assertEmpty($result['matches']);
    }

    public function test_does_not_trigger_when_middle_name_is_different(): void
    {
        $uniqueCtrl = 'TEST-' . uniqid();

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'beneficiary_first_name' => 'JosephMN',
            'beneficiary_middle_name' => 'Mercado',
            'beneficiary_last_name' => 'BautistaMN',
            'beneficiary_birthday' => '1990-05-05',
            'beneficiary_street_address' => 'Sample Street',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09123456789',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Same first name, last name, and birthday, but different middle name (Reyes vs Mercado)
        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'JosephMN',
            'beneficiary_middle_name' => 'Reyes',
            'beneficiary_last_name' => 'BautistaMN',
            'beneficiary_birthday' => '1990-05-05',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertFalse($result['is_duplicate'], 'Clients with different middle names are different individuals');
        $this->assertEmpty($result['matches']);
    }

    public function test_does_not_trigger_when_extension_name_is_different(): void
    {
        $uniqueCtrl = 'TEST-' . uniqid();

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'beneficiary_first_name' => 'AntonioExt',
            'beneficiary_last_name' => 'CastilloExt',
            'beneficiary_extension_name' => 'Sr.',
            'beneficiary_birthday' => '1960-01-01',
            'beneficiary_street_address' => 'Sample Street',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09123456789',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Incoming is Jr., previous is Sr.
        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'AntonioExt',
            'beneficiary_last_name' => 'CastilloExt',
            'beneficiary_extension_name' => 'Jr.',
            'beneficiary_birthday' => '1960-01-01',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertFalse($result['is_duplicate'], 'Jr. and Sr. are different individuals');
        $this->assertEmpty($result['matches']);
    }

    public function test_handles_mm_dd_yyyy_formatted_birthday_gracefully(): void
    {
        $uniqueCtrl = 'TEST-' . uniqid();

        BeneficiaryIntake::create([
            'control_number' => $uniqueCtrl,
            'client_type' => 'New',
            'date_processed' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'beneficiary_first_name' => 'ClarissaDate',
            'beneficiary_last_name' => 'NavarroDate',
            'beneficiary_birthday' => '1995-12-25',
            'beneficiary_street_address' => 'Sample Street',
            'beneficiary_barangay' => 'Acacia',
            'beneficiary_contact_number' => '09123456789',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospitalization',
            'submitted_to' => 'MSWDO Silang Main Office',
        ]);

        // Input formatted as MM/DD/YYYY from browser date mask
        $result = $this->checker->checkDuplicate([
            'beneficiary_first_name' => 'ClarissaDate',
            'beneficiary_last_name' => 'NavarroDate',
            'beneficiary_birthday' => '12/25/1995',
            'date_processed' => Carbon::now()->format('Y-m-d'),
        ]);

        $this->assertTrue($result['is_duplicate'], 'MM/DD/YYYY formatted birthday should correctly match valid duplicate');
        $this->assertCount(1, $result['matches']);
    }

    public function test_handles_malformed_partial_birthday_strings_without_crashing(): void
    {
        // Must not throw any 500 error on partial strings while user is typing
        $partialStrings = ['0', '12', '12/', '12/2', '12/25', '12/25/', '12/25/1', '12/25/19', '12/25/199', 'invalid'];

        foreach ($partialStrings as $partial) {
            $result = $this->checker->checkDuplicate([
                'beneficiary_first_name' => 'TestUser',
                'beneficiary_last_name' => 'TestLast',
                'beneficiary_birthday' => $partial,
                'date_processed' => Carbon::now()->format('Y-m-d'),
            ]);

            $this->assertFalse($result['is_duplicate']);
            $this->assertEmpty($result['matches']);
        }
    }
}
