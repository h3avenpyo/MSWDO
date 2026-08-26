<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinancialGeneralIntakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $encoder = User::where('role', 'social_worker')->first()
            ?? User::where('role', 'admin')->first()
            ?? User::first();

        $encoderId = $encoder?->id;
        $dateProcessed = '2026-08-27'; // Tomorrow's date: August 27, 2026

        /*
        |--------------------------------------------------------------------------
        | SAMPLE DATA SET 1: Eduardo Mendoza (Cardiovascular / Post-Stroke)
        | Representative: Teresa Mendoza (Spouse)
        |--------------------------------------------------------------------------
        */
        $client1 = Client::firstOrCreate(
            [
                'first_name' => 'Eduardo',
                'last_name' => 'Mendoza',
                'birthdate' => '1961-11-14',
            ],
            [
                'middle_name' => 'Ramos',
                'gender' => 'Male',
                'age' => Carbon::parse('1961-11-14')->age,
                'address' => '089 Bonifacio Street, Purok 2',
                'barangay' => 'Biga I',
                'contact_number' => '09201234567',
                'birthplace' => 'Silang, Cavite',
                'religion' => 'Roman Catholic',
                'education' => 'High School Graduate',
                'civil_status' => 'Married',
                'occupation' => 'Carpenter / Construction Worker',
                'income' => '8500.00',
            ]
        );

        BeneficiaryIntake::updateOrCreate(
            ['control_number' => 'MSWDO-2026-00002'],
            [
                'client_id' => $client1->id,
                'client_type' => 'New',
                'date_processed' => $dateProcessed,
                'time_start' => '08:30 AM',
                'time_end' => '09:15 AM',
                'encoder' => $encoderId,
                'is_client_beneficiary' => false,

                // Beneficiary Information
                'beneficiary_last_name' => 'Mendoza',
                'beneficiary_first_name' => 'Eduardo',
                'beneficiary_middle_name' => 'Ramos',
                'beneficiary_extension_name' => null,
                'beneficiary_street_address' => '089 Bonifacio Street, Purok 2',
                'beneficiary_barangay' => 'Biga I',
                'beneficiary_city' => 'Silang',
                'beneficiary_province' => 'Cavite',
                'beneficiary_region' => 'Region IV-A',
                'beneficiary_contact_number' => '09201234567',
                'beneficiary_birthday' => '1961-11-14',
                'beneficiary_age' => Carbon::parse('1961-11-14')->age,
                'beneficiary_sex' => 'Male',
                'beneficiary_civil_status' => 'Married',
                'beneficiary_occupation' => 'Carpenter / Construction Worker',
                'beneficiary_monthly_salary' => 8500.00,
                'beneficiary_category' => 'PWD',
                'beneficiary_category_other' => null,
                'beneficiary_categories' => ['PWD', 'Indigent Resident'],
                'beneficiary_relationship' => null,

                // Representative Information
                'has_representative' => true,
                'rep_last_name' => 'Mendoza',
                'rep_first_name' => 'Teresa',
                'rep_middle_name' => 'Cruz',
                'rep_extension_name' => null,
                'rep_street_address' => '089 Bonifacio Street, Purok 2',
                'rep_barangay' => 'Biga I',
                'rep_city' => 'Silang',
                'rep_province' => 'Cavite',
                'rep_region' => 'Region IV-A',
                'rep_contact_number' => '09287654321',
                'rep_birthday' => '1965-03-22',
                'rep_age' => Carbon::parse('1965-03-22')->age,
                'rep_sex' => 'Female',
                'rep_civil_status' => 'Married',
                'rep_occupation' => 'Housewife / Sari-sari Store Owner',
                'rep_monthly_salary' => 6000.00,
                'rep_relationship' => 'Spouse (Husband / Wife)',

                // Family Composition
                'family_composition' => [
                    [
                        'name' => 'Eduardo R. Mendoza',
                        'relationship' => 'Beneficiary / Head',
                        'age' => Carbon::parse('1961-11-14')->age,
                        'occupation' => 'Carpenter',
                        'salary' => '8500.00',
                    ],
                    [
                        'name' => 'Teresa C. Mendoza',
                        'relationship' => 'Spouse / Representative',
                        'age' => Carbon::parse('1965-03-22')->age,
                        'occupation' => 'Sari-sari Store Owner',
                        'salary' => '6000.00',
                    ],
                    [
                        'name' => 'Carlo C. Mendoza',
                        'relationship' => 'Son',
                        'age' => 24,
                        'occupation' => 'Delivery Rider',
                        'salary' => '11000.00',
                    ],
                ],

                // Assessment & Assistance Purpose Details
                'social_worker_assessment' => 'Client Eduardo Mendoza is a 64-year-old construction worker who suffered a mild ischemic stroke resulting in partial right-sided mobility weakness. His spouse Teresa Cruz Mendoza is applying on his behalf. Household income is insufficient to cover ongoing physical therapy and cardiovascular maintenance medications. Recommending medical financial assistance.',
                'recommended_assistance_type' => 'Medical Assistance',
                'assistance_purpose' => 'Cardiovascular',
                'recommended_amount' => 4000.00,
                'interviewed_by' => 'Maria Santos, RSW',
                'reviewed_by' => 'Elena Ramos, MSWDO Officer',
                'medical_conditions' => ['Cardiovascular', 'Hospital Bill / Medical Needs'],
                'medical_condition_other' => null,
                'service_provided' => 'Financial Assistance Intake',
                'purpose' => 'Hospital Bill / Medical Needs',
                'purpose_other' => null,
                'submitted_to' => 'MSWDO Silang Main Office',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | SAMPLE DATA SET 2: Maricel Delos Santos (Respiratory Disease / Hospital Care)
        | Representative: Lourdes Villanueva (Mother)
        |--------------------------------------------------------------------------
        */
        $client2 = Client::firstOrCreate(
            [
                'first_name' => 'Maricel',
                'last_name' => 'Delos Santos',
                'birthdate' => '1992-09-05',
            ],
            [
                'middle_name' => 'Villanueva',
                'gender' => 'Female',
                'age' => Carbon::parse('1992-09-05')->age,
                'address' => '215 Aguinaldo Highway, Purok 4',
                'barangay' => 'Biluso',
                'contact_number' => '09395556789',
                'birthplace' => 'Silang, Cavite',
                'religion' => 'Roman Catholic',
                'education' => 'College Undergraduate',
                'civil_status' => 'Single',
                'occupation' => 'Unemployed / Solo Parent',
                'income' => '4000.00',
            ]
        );

        BeneficiaryIntake::updateOrCreate(
            ['control_number' => 'MSWDO-2026-00003'],
            [
                'client_id' => $client2->id,
                'client_type' => 'New',
                'date_processed' => $dateProcessed,
                'time_start' => '10:00 AM',
                'time_end' => '10:45 AM',
                'encoder' => $encoderId,
                'is_client_beneficiary' => false,

                // Beneficiary Information
                'beneficiary_last_name' => 'Delos Santos',
                'beneficiary_first_name' => 'Maricel',
                'beneficiary_middle_name' => 'Villanueva',
                'beneficiary_extension_name' => null,
                'beneficiary_street_address' => '215 Aguinaldo Highway, Purok 4',
                'beneficiary_barangay' => 'Biluso',
                'beneficiary_city' => 'Silang',
                'beneficiary_province' => 'Cavite',
                'beneficiary_region' => 'Region IV-A',
                'beneficiary_contact_number' => '09395556789',
                'beneficiary_birthday' => '1992-09-05',
                'beneficiary_age' => Carbon::parse('1992-09-05')->age,
                'beneficiary_sex' => 'Female',
                'beneficiary_civil_status' => 'Single',
                'beneficiary_occupation' => 'Unemployed / Solo Parent',
                'beneficiary_monthly_salary' => 4000.00,
                'beneficiary_category' => 'Solo Parents',
                'beneficiary_category_other' => null,
                'beneficiary_categories' => ['Solo Parents', 'Indigent Resident'],
                'beneficiary_relationship' => null,

                // Representative Information
                'has_representative' => true,
                'rep_last_name' => 'Villanueva',
                'rep_first_name' => 'Lourdes',
                'rep_middle_name' => 'Gomez',
                'rep_extension_name' => null,
                'rep_street_address' => '215 Aguinaldo Highway, Purok 4',
                'rep_barangay' => 'Biluso',
                'rep_city' => 'Silang',
                'rep_province' => 'Cavite',
                'rep_region' => 'Region IV-A',
                'rep_contact_number' => '09198881234',
                'rep_birthday' => '1968-12-10',
                'rep_age' => Carbon::parse('1968-12-10')->age,
                'rep_sex' => 'Female',
                'rep_civil_status' => 'Married',
                'rep_occupation' => 'Barangay Clean-up Worker',
                'rep_monthly_salary' => 5000.00,
                'rep_relationship' => 'Parent (Father / Mother)',

                // Family Composition
                'family_composition' => [
                    [
                        'name' => 'Maricel V. Delos Santos',
                        'relationship' => 'Beneficiary / Solo Parent',
                        'age' => Carbon::parse('1992-09-05')->age,
                        'occupation' => 'None',
                        'salary' => '4000.00',
                    ],
                    [
                        'name' => 'Lourdes G. Villanueva',
                        'relationship' => 'Mother / Representative',
                        'age' => Carbon::parse('1968-12-10')->age,
                        'occupation' => 'Clean-up Worker',
                        'salary' => '5000.00',
                    ],
                    [
                        'name' => 'Nathan V. Delos Santos',
                        'relationship' => 'Son',
                        'age' => 5,
                        'occupation' => 'Dependent / Minor',
                        'salary' => '0.00',
                    ],
                    [
                        'name' => 'Sophia V. Delos Santos',
                        'relationship' => 'Daughter',
                        'age' => 3,
                        'occupation' => 'Dependent / Minor',
                        'salary' => '0.00',
                    ],
                ],

                // Assessment & Assistance Purpose Details
                'social_worker_assessment' => 'Client Maricel Delos Santos is a 33-year-old solo parent whose 5-year-old child was admitted to Silang General Hospital due to severe acute bronchitis/pneumonia. Her mother Lourdes Villanueva is filing the intake assistance on their behalf while Maricel attends to the child. The family relies on intermittent municipal clean-up stipends and has accumulated unpaid medical and pharmacy bills. Recommending emergency medical financial aid.',
                'recommended_assistance_type' => 'Medical Assistance',
                'assistance_purpose' => 'Respiratory Diseases',
                'recommended_amount' => 5000.00,
                'interviewed_by' => 'Maria Santos, RSW',
                'reviewed_by' => 'Elena Ramos, MSWDO Officer',
                'medical_conditions' => ['Respiratory Diseases', 'Hospital Bill / Medical Needs'],
                'medical_condition_other' => null,
                'service_provided' => 'Financial Assistance Intake',
                'purpose' => 'Hospital Bill / Medical Needs',
                'purpose_other' => null,
                'submitted_to' => 'MSWDO Silang Main Office',
            ]
        );
    }
}
