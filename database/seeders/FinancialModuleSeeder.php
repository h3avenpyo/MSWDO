<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Financial\FinancialAssistanceMessage;
use App\Models\Financial\FinancialPayrollRecord;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Curated, idempotent sample data for the Financial Assistance module.
 *
 * Registers the financial assistance officer accounts (Step 1 / Step 2 / officer)
 * and seeds general intake sheets, a generated payroll record with its linked
 * intakes, and the SMS claiming notifications sent to clients.
 */
class FinancialModuleSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::updateOrCreate(
            ['email' => 'financial@mswdo.test'],
            [
                'name' => 'Financial Assistance Officer',
                'password' => Hash::make('password'),
                'role' => UserRole::FinancialAssistanceOfficer,
                'status' => 'active',
            ]
        );

        $step1 = User::updateOrCreate(
            ['email' => 'financialstep1@mswdo.test'],
            [
                'name' => 'Financial Assistance Step 1',
                'password' => Hash::make('password'),
                'role' => UserRole::FinancialStep1,
                'status' => 'active',
            ]
        );

        $step2 = User::updateOrCreate(
            ['email' => 'financialstep2@mswdo.test'],
            [
                'name' => 'Financial Assistance Step 2',
                'password' => Hash::make('password'),
                'role' => UserRole::FinancialStep2,
                'status' => 'active',
            ]
        );

        $encoder = User::where('email', 'social@mswdo.test')->first()
            ?? User::where('role', UserRole::Admin)->first()
            ?? $step1;

        $dateProcessed = Carbon::parse('2026-06-08');

        $intakes = [
            [
                'control_number' => 'MSWDO-2026-06-0001',
                'client_type' => 'New',
                'is_client_beneficiary' => true,
                'beneficiary' => [
                    'first_name' => 'Eduardo', 'middle_name' => 'Ramos', 'last_name' => 'Mendoza',
                    'birthday' => '1961-11-14', 'sex' => 'Male', 'civil_status' => 'Married',
                    'barangay' => 'Biga I', 'occupation' => 'Carpenter / Construction Worker',
                    'salary' => 8500.00, 'category' => 'PWD',
                ],
                'client' => [
                    'first_name' => 'Eduardo', 'middle_name' => 'Ramos', 'last_name' => 'Mendoza',
                    'birthdate' => '1961-11-14', 'gender' => 'Male', 'barangay' => 'Biga I',
                    'address' => '089 Bonifacio Street, Purok 2, Biga I, Silang, Cavite',
                    'contact_number' => '09201234567', 'civil_status' => 'Married',
                    'occupation' => 'Carpenter', 'income' => '8500.00', 'religion' => 'Roman Catholic',
                    'education' => 'High School Graduate', 'birthplace' => 'Silang, Cavite',
                ],
                'representative' => [
                    'last_name' => 'Mendoza', 'first_name' => 'Teresa', 'middle_name' => 'Cruz',
                    'birthday' => '1965-03-22', 'sex' => 'Female', 'civil_status' => 'Married',
                    'barangay' => 'Biga I', 'occupation' => 'Sari-sari Store Owner',
                    'salary' => 6000.00, 'relationship' => 'Spouse (Husband / Wife)',
                    'contact_number' => '09287654321',
                ],
                'assessment' => 'Client Eduardo Mendoza is a 64-year-old construction worker who suffered a mild ischemic stroke resulting in partial right-sided mobility weakness. Household income is insufficient to cover ongoing physical therapy and maintenance medications.',
                'recommended_assistance_type' => 'Medical Assistance',
                'assistance_purpose' => 'Hospital Bill / Medical Needs',
                'assistance_type' => 'Hospital Bill',
                'medical_conditions' => ['Cardiovascular', 'Hospital Bill / Medical Needs'],
                'recommended_amount' => 4000.00,
                'family_composition' => [
                    ['name' => 'Eduardo R. Mendoza', 'relationship' => 'Beneficiary / Head', 'age' => 64, 'occupation' => 'Carpenter', 'salary' => '8500.00'],
                    ['name' => 'Teresa C. Mendoza', 'relationship' => 'Spouse / Representative', 'age' => 61, 'occupation' => 'Sari-sari Store Owner', 'salary' => '6000.00'],
                    ['name' => 'Carlo C. Mendoza', 'relationship' => 'Son', 'age' => 24, 'occupation' => 'Delivery Rider', 'salary' => '11000.00'],
                ],
                'linked_to_payroll' => true,
                'claim_status' => 'Claimed',
                'claimed_by' => 'Teresa Mendoza',
            ],
            [
                'control_number' => 'MSWDO-2026-06-0002',
                'client_type' => 'New',
                'is_client_beneficiary' => true,
                'beneficiary' => [
                    'first_name' => 'Maricel', 'middle_name' => 'Villanueva', 'last_name' => 'Delos Santos',
                    'birthday' => '1992-09-05', 'sex' => 'Female', 'civil_status' => 'Single',
                    'barangay' => 'Biluso', 'occupation' => 'Unemployed / Solo Parent',
                    'salary' => 4000.00, 'category' => 'Solo Parents',
                ],
                'client' => [
                    'first_name' => 'Maricel', 'middle_name' => 'Villanueva', 'last_name' => 'Delos Santos',
                    'birthdate' => '1992-09-05', 'gender' => 'Female', 'barangay' => 'Biluso',
                    'address' => '215 Aguinaldo Highway, Purok 4, Biluso, Silang, Cavite',
                    'contact_number' => '09395556789', 'civil_status' => 'Single',
                    'occupation' => 'Solo Parent', 'income' => '4000.00', 'religion' => 'Roman Catholic',
                    'education' => 'College Undergraduate', 'birthplace' => 'Silang, Cavite',
                ],
                'representative' => [
                    'last_name' => 'Villanueva', 'first_name' => 'Lourdes', 'middle_name' => 'Gomez',
                    'birthday' => '1968-12-10', 'sex' => 'Female', 'civil_status' => 'Married',
                    'barangay' => 'Biluso', 'occupation' => 'Barangay Clean-up Worker',
                    'salary' => 5000.00, 'relationship' => 'Parent (Father / Mother)',
                    'contact_number' => '09198881234',
                ],
                'assessment' => 'Client Maricel Delos Santos is a solo parent whose 5-year-old child was admitted to Silang General Hospital due to severe acute bronchitis. The family has accumulated unpaid medical and pharmacy bills.',
                'recommended_assistance_type' => 'Medical Assistance',
                'assistance_purpose' => 'Respiratory Diseases',
                'assistance_type' => 'Medicine/Gamot',
                'medical_conditions' => ['Respiratory Diseases', 'Hospital Bill / Medical Needs'],
                'recommended_amount' => 5000.00,
                'family_composition' => [
                    ['name' => 'Maricel V. Delos Santos', 'relationship' => 'Beneficiary / Solo Parent', 'age' => 33, 'occupation' => 'None', 'salary' => '4000.00'],
                    ['name' => 'Lourdes G. Villanueva', 'relationship' => 'Mother / Representative', 'age' => 57, 'occupation' => 'Clean-up Worker', 'salary' => '5000.00'],
                    ['name' => 'Nathan V. Delos Santos', 'relationship' => 'Son', 'age' => 5, 'occupation' => 'Dependent / Minor', 'salary' => '0.00'],
                ],
                'linked_to_payroll' => true,
                'claim_status' => 'Unclaimed',
                'claimed_by' => null,
            ],
            [
                'control_number' => 'MSWDO-2026-07-0001',
                'client_type' => 'Returning',
                'is_client_beneficiary' => true,
                'beneficiary' => [
                    'first_name' => 'Nelson', 'middle_name' => 'Reyes', 'last_name' => 'Garcia',
                    'birthday' => '1978-02-17', 'sex' => 'Male', 'civil_status' => 'Married',
                    'barangay' => 'Lumil', 'occupation' => 'Farmer',
                    'salary' => 7000.00, 'category' => 'Indigent Resident',
                ],
                'client' => [
                    'first_name' => 'Nelson', 'middle_name' => 'Reyes', 'last_name' => 'Garcia',
                    'birthdate' => '1978-02-17', 'gender' => 'Male', 'barangay' => 'Lumil',
                    'address' => 'Purok 3, Lumil, Silang, Cavite',
                    'contact_number' => '09451112233', 'civil_status' => 'Married',
                    'occupation' => 'Farmer', 'income' => '7000.00', 'religion' => 'Roman Catholic',
                    'education' => 'Elementary Graduate', 'birthplace' => 'Silang, Cavite',
                ],
                'representative' => null,
                'assessment' => 'Client requested financial assistance for laboratory tests and medicines related to a suspected kidney condition. He is the sole provider for a family of five.',
                'recommended_assistance_type' => 'Medical Assistance',
                'assistance_purpose' => 'Laboratory Test',
                'assistance_type' => 'Laboratory Test',
                'medical_conditions' => ['Kidney Disease'],
                'recommended_amount' => 3000.00,
                'family_composition' => [
                    ['name' => 'Nelson R. Garcia', 'relationship' => 'Beneficiary / Head', 'age' => 48, 'occupation' => 'Farmer', 'salary' => '7000.00'],
                    ['name' => 'Josefina R. Garcia', 'relationship' => 'Spouse', 'age' => 45, 'occupation' => 'Housewife', 'salary' => '0.00'],
                    ['name' => 'Mark R. Garcia', 'relationship' => 'Son', 'age' => 16, 'occupation' => 'Student', 'salary' => '0.00'],
                ],
                'linked_to_payroll' => false,
                'claim_status' => 'Unclaimed',
                'claimed_by' => null,
            ],
            [
                'control_number' => 'MSWDO-2026-07-0002',
                'client_type' => 'New',
                'is_client_beneficiary' => true,
                'beneficiary' => [
                    'first_name' => 'Lourdes', 'middle_name' => 'Cruz', 'last_name' => 'Aquino',
                    'birthday' => '1955-06-30', 'sex' => 'Female', 'civil_status' => 'Widowed',
                    'barangay' => 'Bulihan', 'occupation' => 'None',
                    'salary' => 0.00, 'category' => 'Senior Citizen',
                ],
                'client' => [
                    'first_name' => 'Lourdes', 'middle_name' => 'Cruz', 'last_name' => 'Aquino',
                    'birthdate' => '1955-06-30', 'gender' => 'Female', 'barangay' => 'Bulihan',
                    'address' => 'Purok 1, Bulihan, Silang, Cavite',
                    'contact_number' => '09567778899', 'civil_status' => 'Widowed',
                    'occupation' => 'None', 'income' => '0.00', 'religion' => 'Roman Catholic',
                    'education' => 'Elementary Graduate', 'birthplace' => 'Silang, Cavite',
                ],
                'representative' => [
                    'last_name' => 'Aquino', 'first_name' => 'Rita', 'middle_name' => 'Santos',
                    'birthday' => '1980-04-04', 'sex' => 'Female', 'civil_status' => 'Married',
                    'barangay' => 'Bulihan', 'occupation' => 'Vendor',
                    'salary' => 6000.00, 'relationship' => 'Child',
                    'contact_number' => '09175556677',
                ],
                'assessment' => 'Client is a widowed senior citizen requesting assistance for maintenance medicines for hypertension and arthritis. She has no regular source of income.',
                'recommended_assistance_type' => 'Medical Assistance',
                'assistance_purpose' => 'Medicine/Gamot',
                'assistance_type' => 'Medicine/Gamot',
                'medical_conditions' => ['Hypertension', 'Arthritis'],
                'recommended_amount' => 2500.00,
                'family_composition' => [
                    ['name' => 'Lourdes C. Aquino', 'relationship' => 'Beneficiary', 'age' => 70, 'occupation' => 'None', 'salary' => '0.00'],
                    ['name' => 'Rita S. Aquino', 'relationship' => 'Daughter / Representative', 'age' => 45, 'occupation' => 'Vendor', 'salary' => '6000.00'],
                ],
                'linked_to_payroll' => false,
                'claim_status' => 'Unclaimed',
                'claimed_by' => null,
            ],
        ];

        $createdIntakes = collect();

        foreach ($intakes as $definition) {
            $client = Client::firstOrCreate(
                [
                    'first_name' => $definition['client']['first_name'],
                    'last_name' => $definition['client']['last_name'],
                    'birthdate' => $definition['client']['birthdate'],
                ],
                $definition['client'] + ['middle_name' => $definition['client']['middle_name'] ?? null]
            );

            $beneficiary = $definition['beneficiary'];
            $representative = $definition['representative'];

            $attributes = [
                'client_id' => $client->id,
                'client_type' => $definition['client_type'],
                'date_processed' => $dateProcessed->toDateString(),
                'time_start' => '08:30 AM',
                'time_end' => '09:15 AM',
                'encoder' => $encoder->id,
                'is_client_beneficiary' => $definition['is_client_beneficiary'],
                'beneficiary_last_name' => $beneficiary['last_name'],
                'beneficiary_first_name' => $beneficiary['first_name'],
                'beneficiary_middle_name' => $beneficiary['middle_name'],
                'beneficiary_street_address' => $client->address,
                'beneficiary_barangay' => $beneficiary['barangay'],
                'beneficiary_city' => 'Silang',
                'beneficiary_province' => 'Cavite',
                'beneficiary_region' => 'Region IV-A',
                'beneficiary_contact_number' => $client->contact_number,
                'beneficiary_birthday' => $beneficiary['birthday'],
                'beneficiary_age' => Carbon::parse($beneficiary['birthday'])->age,
                'beneficiary_sex' => $beneficiary['sex'],
                'beneficiary_civil_status' => $beneficiary['civil_status'],
                'beneficiary_occupation' => $beneficiary['occupation'],
                'beneficiary_monthly_salary' => $beneficiary['salary'],
                'beneficiary_category' => $beneficiary['category'],
                'beneficiary_categories' => [$beneficiary['category'], 'Indigent Resident'],
                'has_representative' => $representative !== null,
                'family_composition' => $definition['family_composition'],
                'social_worker_assessment' => $definition['assessment'],
                'recommended_assistance_type' => $definition['recommended_assistance_type'],
                'assistance_purpose' => $definition['assistance_purpose'],
                'assistance_type' => $definition['assistance_type'],
                'recommended_amount' => $definition['recommended_amount'],
                'interviewed_by' => 'Maria Santos, RSW',
                'reviewed_by' => 'Elena Ramos, MSWDO Officer',
                'medical_conditions' => $definition['medical_conditions'],
                'service_provided' => 'Financial Assistance Intake',
                'purpose' => $definition['assistance_purpose'],
                'submitted_to' => 'MSWDO Silang Main Office',
                'claim_status' => $definition['claim_status'],
                'claimed_by' => $definition['claimed_by'],
                'claimed_at' => $definition['claim_status'] === 'Claimed' ? $dateProcessed->copy()->addDays(20)->toDateTimeString() : null,
            ];

            if ($representative) {
                $attributes = array_merge($attributes, [
                    'rep_last_name' => $representative['last_name'],
                    'rep_first_name' => $representative['first_name'],
                    'rep_middle_name' => $representative['middle_name'],
                    'rep_street_address' => $client->address,
                    'rep_barangay' => $representative['barangay'],
                    'rep_city' => 'Silang',
                    'rep_province' => 'Cavite',
                    'rep_region' => 'Region IV-A',
                    'rep_contact_number' => $representative['contact_number'],
                    'rep_birthday' => $representative['birthday'],
                    'rep_age' => Carbon::parse($representative['birthday'])->age,
                    'rep_sex' => $representative['sex'],
                    'rep_civil_status' => $representative['civil_status'],
                    'rep_occupation' => $representative['occupation'],
                    'rep_monthly_salary' => $representative['salary'],
                    'rep_relationship' => $representative['relationship'],
                ]);
            }

            $intake = BeneficiaryIntake::updateOrCreate(
                ['control_number' => $definition['control_number']],
                $attributes
            );

            $createdIntakes->put($definition['control_number'], $intake);
        }

        // ---- Payroll record covering the two June intakes ----
        $payrollDate = Carbon::parse('2026-06-20');
        $payrollIntakes = $createdIntakes->filter(fn ($intake) => in_array($intake->control_number, ['MSWDO-2026-06-0001', 'MSWDO-2026-06-0002'], true));
        $payrollData = $payrollIntakes->map(function (BeneficiaryIntake $intake) use ($payrollDate) {
            return [
                'intake_id' => $intake->id,
                'control_number' => $intake->control_number,
                'beneficiary_name' => $intake->beneficiary_full_name,
                'barangay' => $intake->beneficiary_barangay,
                'assistance_type' => $intake->assistance_type,
                'amount' => (float) $intake->recommended_amount,
                'claiming_date' => $payrollDate->toDateString(),
                'claim_status' => $intake->claim_status,
            ];
        })->values()->all();

        $payroll = FinancialPayrollRecord::updateOrCreate(
            ['payroll_number' => 'PAYROLL-2026-06-B01'],
            [
                'payroll_date' => $payrollDate->toDateString(),
                'batch_number' => 1,
                'generated_by_id' => $officer->id,
                'generated_by_name' => $officer->name,
                'disbursing_officer' => 'Elena Ramos, MSWDO Officer',
                'certified_by' => 'MSWDO HEAD / OFFICER-IN-CHARGE',
                'approved_by' => 'HON. MUNICIPAL MAYOR',
                'total_beneficiaries' => $payrollIntakes->count(),
                'total_amount' => $payrollIntakes->sum(fn (BeneficiaryIntake $intake) => (float) $intake->recommended_amount),
                'status' => 'Completed',
                'notes' => 'First batch payroll for June 2026 medical assistance beneficiaries.',
                'payroll_data' => $payrollData,
            ]
        );

        foreach ($payrollIntakes as $intake) {
            $intake->update([
                'is_payroll_generated' => true,
                'payroll_generated_at' => $payrollDate->copy()->subDays(2)->toDateTimeString(),
                'payroll_date' => $payrollDate->toDateString(),
                'claiming_date' => $payrollDate->toDateString(),
                'payroll_record_id' => $payroll->id,
            ]);
        }

        // ---- SMS claiming notifications ----
        foreach ($payrollIntakes as $intake) {
            FinancialAssistanceMessage::updateOrCreate(
                [
                    'intake_id' => $intake->id,
                    'message_type' => 'Unclaimed Assistance',
                ],
                [
                    'client_id' => $intake->client_id,
                    'beneficiary_name' => $intake->beneficiary_full_name,
                    'recipient_name' => $intake->has_representative ? $intake->representative_full_name : $intake->beneficiary_full_name,
                    'recipient_contact_number' => $intake->has_representative
                        ? $intake->rep_contact_number
                        : $intake->beneficiary_contact_number,
                    'message_body' => sprintf(
                        'MSWDO SILANG: Ang inyong financial assistance (%s) ay maaari nang makuha sa %s. Magdala ng valid ID at claim stub. %s',
                        $intake->assistance_type,
                        $payrollDate->format('F d, Y'),
                        $intake->control_number
                    ),
                    'claiming_date' => $payrollDate->toDateString(),
                    'status' => 'Sent',
                    'reference_id' => 'SMS-' . str_pad((string) $intake->id, 8, '0', STR_PAD_LEFT),
                    'sent_by' => $officer->name,
                    'sent_at' => $payrollDate->copy()->subDay()->setTime(9, 0)->toDateTimeString(),
                ]
            );
        }

        $this->command->info('Financial module seeded: 4 general intake sheets, 1 payroll record and 2 SMS claiming notifications.');
    }
}
