<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\SocialCase\AssistanceRecord;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\SocialCase\CaseInterview;
use App\Models\SocialCase\CaseRejection;
use App\Models\SocialCase\FamilyMember;
use App\Models\SocialCase\SocialCaseStudy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Curated, idempotent sample data for the Social Case module.
 *
 * Creates the officers that own the sample cases, then seeds a small set of
 * realistic case studies with their interviews, family members, general intake
 * sheet, released assistance records and one rejection example.
 */
class SocialCaseModuleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->officer([
            'email' => 'admin@mswdo.test',
            'name' => 'Admin User',
            'role' => UserRole::Admin,
        ]);

        $encoder = $this->officer([
            'email' => 'social@mswdo.test',
            'name' => 'Social Case Worker (Encoder)',
            'role' => UserRole::SocialWorker,
        ]);

        $checker = $this->officer([
            'email' => 'social2@mswdo.test',
            'name' => 'Social Case Worker (Checker)',
            'role' => UserRole::EligibilityChecker,
        ]);

        $cases = [
            [
                'case_number' => 'MSWD-O-2026-01-0001',
                'date_processed' => '2026-01-14',
                'status' => 'Released',
                'workflow_step' => 'release',
                'service_provided' => 'Medical Assistance',
                'purpose' => 'Hospital bill payment',
                'submitted_to' => 'DSWD',
                'eligibility_status' => 'eligible',
                'amount' => 7500.00,
                'released_to' => 'Rodel Dela Cruz',
                'client' => [
                    'first_name' => 'Rodel', 'middle_name' => 'Santos', 'last_name' => 'Dela Cruz',
                    'birthdate' => '1968-04-12', 'gender' => 'Male', 'barangay' => 'Biga I',
                    'address' => '089 Bonifacio Street, Purok 2, Biga I, Silang, Cavite',
                    'contact_number' => '09171234567', 'civil_status' => 'Married',
                    'occupation' => 'Carpenter', 'income' => '8500.00', 'religion' => 'Roman Catholic',
                    'education' => 'High School Graduate', 'birthplace' => 'Silang, Cavite',
                ],
                'interview' => [
                    'interview_reason' => 'Requesting financial assistance for hospitalization',
                    'interview_situation' => 'Client was hospitalized for three days due to pneumonia. Household income is insufficient to cover the hospital bill.',
                    'interview_household' => 'Household has 5 members living in a concrete house in Purok 2.',
                    'monthly_income' => 8500, 'monthly_expenses' => 7200,
                    'family_illnesses' => 'Pneumonia',
                    'previous_assistance' => 'Received Medical Assistance last year',
                    'interview_notes' => 'Client was cooperative and provided complete documents during the interview.',
                    'social_worker_assessment' => 'Client is eligible for medical financial assistance.',
                    'recommendation' => 'Approve financial assistance',
                    'recommended_amount' => 7500,
                ],
                'family' => [
                    ['name' => 'Teresa Dela Cruz', 'relationship' => 'Spouse', 'age' => 54, 'sex' => 'Female', 'occupation' => 'Housewife', 'income' => '0.00', 'dependent' => false],
                    ['name' => 'Carlo Dela Cruz', 'relationship' => 'Child', 'age' => 22, 'sex' => 'Male', 'occupation' => 'Delivery Rider', 'income' => '11000.00', 'dependent' => false],
                    ['name' => 'Liza Dela Cruz', 'relationship' => 'Child', 'age' => 15, 'sex' => 'Female', 'occupation' => 'Student', 'income' => '0.00', 'dependent' => true],
                ],
            ],
            [
                'case_number' => 'MSWD-O-2026-02-0001',
                'date_processed' => '2026-02-18',
                'status' => 'Released',
                'workflow_step' => 'release',
                'service_provided' => 'Burial Assistance',
                'purpose' => 'Funeral expenses',
                'submitted_to' => 'LGU Silang',
                'eligibility_status' => 'eligible',
                'amount' => 10000.00,
                'released_to' => 'Cristina Reyes',
                'client' => [
                    'first_name' => 'Cristina', 'middle_name' => 'Garcia', 'last_name' => 'Reyes',
                    'birthdate' => '1979-11-02', 'gender' => 'Female', 'barangay' => 'Lumil',
                    'address' => 'Purok 4, Lumil, Silang, Cavite',
                    'contact_number' => '09281234567', 'civil_status' => 'Widowed',
                    'occupation' => 'Vendor', 'income' => '6000.00', 'religion' => 'Roman Catholic',
                    'education' => 'College Undergraduate', 'birthplace' => 'Tagaytay City',
                ],
                'interview' => [
                    'interview_reason' => 'Requesting burial assistance for deceased spouse',
                    'interview_situation' => 'Client\'s spouse passed away due to a heart attack. The family has no savings to cover funeral expenses.',
                    'interview_household' => 'Household has 4 members living in a wood and concrete house.',
                    'monthly_income' => 6000, 'monthly_expenses' => 5800,
                    'family_illnesses' => 'None',
                    'previous_assistance' => 'None',
                    'interview_notes' => 'Client appeared distressed but cooperative.',
                    'social_worker_assessment' => 'Client is eligible for burial assistance.',
                    'recommendation' => 'Approved for burial assistance',
                    'recommended_amount' => 10000,
                ],
                'family' => [
                    ['name' => 'Mark Reyes', 'relationship' => 'Child', 'age' => 19, 'sex' => 'Male', 'occupation' => 'Student', 'income' => '0.00', 'dependent' => true],
                    ['name' => 'Joy Reyes', 'relationship' => 'Child', 'age' => 16, 'sex' => 'Female', 'occupation' => 'Student', 'income' => '0.00', 'dependent' => true],
                    ['name' => 'Rita Reyes', 'relationship' => 'Mother-in-law', 'age' => 74, 'sex' => 'Female', 'occupation' => 'None', 'income' => '0.00', 'dependent' => true],
                ],
            ],
            [
                'case_number' => 'MSWD-O-2026-03-0001',
                'date_processed' => '2026-03-11',
                'status' => 'In Progress',
                'workflow_step' => 'evaluation',
                'service_provided' => 'Educational Assistance',
                'purpose' => 'School tuition fee',
                'submitted_to' => 'DepEd',
                'eligibility_status' => 'eligible',
                'amount' => null,
                'released_to' => null,
                'client' => [
                    'first_name' => 'Angel', 'middle_name' => 'Torres', 'last_name' => 'Flores',
                    'birthdate' => '2007-06-25', 'gender' => 'Female', 'barangay' => 'Bucal',
                    'address' => 'Purok 1, Bucal, Silang, Cavite',
                    'contact_number' => '09391234567', 'civil_status' => 'Single',
                    'occupation' => 'Student', 'income' => '0.00', 'religion' => 'Iglesia ni Cristo',
                    'education' => 'Senior High School', 'birthplace' => 'Dasmariñas, Cavite',
                ],
                'interview' => [
                    'interview_reason' => 'Requesting educational assistance for senior high school',
                    'interview_situation' => 'Both parents are jobless due to the closure of the family business. The student risks stopping schooling.',
                    'interview_household' => 'Household has 6 members living in a rented apartment.',
                    'monthly_income' => 4000, 'monthly_expenses' => 6500,
                    'family_illnesses' => 'Hypertension',
                    'previous_assistance' => 'None',
                    'interview_notes' => 'Student is determined and has good academic standing.',
                    'social_worker_assessment' => 'Client is eligible but still under evaluation.',
                    'recommendation' => 'Approve educational assistance',
                    'recommended_amount' => 5000,
                ],
                'family' => [
                    ['name' => 'Nelson Flores', 'relationship' => 'Father', 'age' => 48, 'sex' => 'Male', 'occupation' => 'Unemployed', 'income' => '0.00', 'dependent' => false],
                    ['name' => 'Marites Flores', 'relationship' => 'Mother', 'age' => 45, 'sex' => 'Female', 'occupation' => 'Laundry Woman', 'income' => '3500.00', 'dependent' => false],
                    ['name' => 'Paolo Flores', 'relationship' => 'Sibling', 'age' => 12, 'sex' => 'Male', 'occupation' => 'Student', 'income' => '0.00', 'dependent' => true],
                ],
            ],
            [
                'case_number' => 'MSWD-O-2026-04-0001',
                'date_processed' => '2026-04-09',
                'status' => 'Completed',
                'workflow_step' => 'release',
                'service_provided' => 'Livelihood Assistance',
                'purpose' => 'Capital for small business',
                'submitted_to' => 'PCSO',
                'eligibility_status' => 'eligible',
                'amount' => 15000.00,
                'released_to' => 'Dennis Ramos',
                'client' => [
                    'first_name' => 'Dennis', 'middle_name' => 'Cruz', 'last_name' => 'Ramos',
                    'birthdate' => '1985-09-30', 'gender' => 'Male', 'barangay' => 'Kaong',
                    'address' => 'Purok 6, Kaong, Silang, Cavite',
                    'contact_number' => '09451234567', 'civil_status' => 'Married',
                    'occupation' => 'Tricycle Driver', 'income' => '7000.00', 'religion' => 'Roman Catholic',
                    'education' => 'High School Graduate', 'birthplace' => 'Silang, Cavite',
                ],
                'interview' => [
                    'interview_reason' => 'Requesting capital for a sari-sari store',
                    'interview_situation' => 'Client lost income during the rainy season. Requests capital to start a small store to support the family.',
                    'interview_household' => 'Household has 4 members living in a concrete house.',
                    'monthly_income' => 7000, 'monthly_expenses' => 6800,
                    'family_illnesses' => 'Asthma',
                    'previous_assistance' => 'Received Food Assistance during typhoon',
                    'interview_notes' => 'Client showed willingness to attend livelihood training.',
                    'social_worker_assessment' => 'Client is eligible for livelihood assistance.',
                    'recommendation' => 'Refer to livelihood program',
                    'recommended_amount' => 15000,
                ],
                'family' => [
                    ['name' => 'Grace Ramos', 'relationship' => 'Spouse', 'age' => 38, 'sex' => 'Female', 'occupation' => 'Housewife', 'income' => '0.00', 'dependent' => false],
                    ['name' => 'Joel Ramos', 'relationship' => 'Child', 'age' => 10, 'sex' => 'Male', 'occupation' => 'Student', 'income' => '0.00', 'dependent' => true],
                ],
            ],
            [
                'case_number' => 'MSWD-O-2026-05-0001',
                'date_processed' => '2026-05-15',
                'status' => 'Draft',
                'workflow_step' => 'requirements_verification',
                'service_provided' => 'Food Assistance',
                'purpose' => 'Daily sustenance',
                'submitted_to' => 'MSWDO Silang Main Office',
                'eligibility_status' => 'pending',
                'amount' => null,
                'released_to' => null,
                'client' => [
                    'first_name' => 'Nancy', 'middle_name' => 'Rivera', 'last_name' => 'Bautista',
                    'birthdate' => '1990-01-19', 'gender' => 'Female', 'barangay' => 'Bulihan',
                    'address' => 'Purok 3, Bulihan, Silang, Cavite',
                    'contact_number' => '09561234567', 'civil_status' => 'Separated',
                    'occupation' => 'Solo Parent', 'income' => '3000.00', 'religion' => 'Born Again Christian',
                    'education' => 'High School Undergraduate', 'birthplace' => 'Silang, Cavite',
                ],
                'interview' => [
                    'interview_reason' => 'Requesting food assistance for children',
                    'interview_situation' => 'Client is a solo parent of three children and has no regular income after her separation.',
                    'interview_household' => 'Household has 4 members living in a makeshift shelter.',
                    'monthly_income' => 3000, 'monthly_expenses' => 5000,
                    'family_illnesses' => 'Malnutrition',
                    'previous_assistance' => 'Received Food Assistance last month',
                    'interview_notes' => 'Client appeared anxious but hopeful.',
                    'social_worker_assessment' => 'For approval pending verification of requirements.',
                    'recommendation' => 'Provide immediate food assistance',
                    'recommended_amount' => 3000,
                ],
                'family' => [
                    ['name' => 'Allan Bautista', 'relationship' => 'Child', 'age' => 8, 'sex' => 'Male', 'occupation' => 'Student', 'income' => '0.00', 'dependent' => true],
                    ['name' => 'Cherry Bautista', 'relationship' => 'Child', 'age' => 6, 'sex' => 'Female', 'occupation' => 'Student', 'income' => '0.00', 'dependent' => true],
                    ['name' => 'Tess Bautista', 'relationship' => 'Child', 'age' => 3, 'sex' => 'Female', 'occupation' => 'None', 'income' => '0.00', 'dependent' => true],
                ],
            ],
        ];

        foreach ($cases as $definition) {
            $client = Client::firstOrCreate(
                [
                    'first_name' => $definition['client']['first_name'],
                    'last_name' => $definition['client']['last_name'],
                    'birthdate' => $definition['client']['birthdate'],
                ],
                $definition['client'] + ['middle_name' => $definition['client']['middle_name'] ?? null]
            );

            $dateProcessed = Carbon::parse($definition['date_processed']);
            $interviewDate = $dateProcessed->copy()->addDays(2);
            $isReleased = in_array($definition['status'], ['Completed', 'Released'], true);

            $case = SocialCaseStudy::updateOrCreate(
                ['case_number' => $definition['case_number']],
                [
                    'main_client_id' => $client->id,
                    'officer_id' => $encoder->id,
                    'date_processed' => $dateProcessed->toDateString(),
                    'service_provided' => $definition['service_provided'],
                    'purpose' => $definition['purpose'],
                    'submitted_to' => $definition['submitted_to'],
                    'encoded_by' => $encoder->id,
                    'status' => $definition['status'],
                    'eligibility_status' => $definition['eligibility_status'],
                    'eligible_by' => $definition['eligibility_status'] === 'eligible' ? $checker->id : null,
                    'eligible_at' => $definition['eligibility_status'] === 'eligible' ? $dateProcessed->copy()->addDay()->toDateTimeString() : null,
                    'summary' => "Case study for {$client->full_name} regarding " . strtolower($definition['service_provided']) . '.',
                    'interview_date' => $interviewDate->toDateString(),
                    'workflow_step' => $definition['workflow_step'],
                    'requirements_complete' => $definition['workflow_step'] !== 'requirements_verification',
                    'interview_complete' => in_array($definition['workflow_step'], ['evaluation', 'report_generation', 'release'], true),
                    'evaluation_complete' => in_array($definition['workflow_step'], ['report_generation', 'release'], true),
                    'report_generated' => in_array($definition['workflow_step'], ['report_generation', 'release'], true),
                    'assistance_released' => $isReleased,
                    'assistance_amount' => $definition['amount'],
                    'assistance_date' => $isReleased ? $dateProcessed->copy()->addDays(7)->toDateString() : null,
                    'released_at' => $isReleased ? $dateProcessed->copy()->addDays(7)->toDateTimeString() : null,
                    'released_by' => $isReleased ? $admin->id : null,
                    'released_to' => $definition['released_to'],
                    'intake_first_name' => $client->first_name,
                    'intake_middle_name' => $client->middle_name,
                    'intake_last_name' => $client->last_name,
                    'intake_full_name' => $client->full_name,
                    'intake_birthdate' => $client->birthdate,
                    'intake_age' => $client->age,
                    'intake_gender' => $client->gender,
                    'intake_civil_status' => $client->civil_status,
                    'intake_religion' => $client->religion,
                    'intake_birthplace' => $client->birthplace,
                    'intake_education' => $client->education,
                    'intake_occupation' => $client->occupation,
                    'intake_income' => $client->income,
                    'intake_address' => $client->address,
                    'intake_barangay' => $client->barangay,
                    'intake_contact_number' => $client->contact_number,
                ]
            );

            CaseInterview::updateOrCreate(
                ['social_case_study_id' => $case->id],
                $definition['interview'] + ['social_case_study_id' => $case->id]
            );

            FamilyMember::where('social_case_study_id', $case->id)->delete();
            foreach ($definition['family'] as $member) {
                FamilyMember::create([
                    'social_case_study_id' => $case->id,
                    'full_name' => $member['name'],
                    'relationship' => $member['relationship'],
                    'age' => $member['age'],
                    'sex' => $member['sex'],
                    'occupation' => $member['occupation'],
                    'monthly_income' => $member['income'],
                    'is_dependent' => $member['dependent'],
                ]);
            }

            BeneficiaryIntake::updateOrCreate(
                ['control_number' => sprintf('MSWDO-%s-%04d', $dateProcessed->format('Y-m'), ($case->id % 9000) + 1)],
                [
                    'client_id' => $client->id,
                    'social_case_study_id' => $case->id,
                    'client_type' => 'New',
                    'date_processed' => $dateProcessed->toDateString(),
                    'time_start' => '08:30 AM',
                    'time_end' => '09:15 AM',
                    'encoder' => $encoder->id,
                    'is_client_beneficiary' => true,
                    'beneficiary_last_name' => $client->last_name,
                    'beneficiary_first_name' => $client->first_name,
                    'beneficiary_middle_name' => $client->middle_name,
                    'beneficiary_street_address' => $client->address,
                    'beneficiary_barangay' => $client->barangay,
                    'beneficiary_city' => 'Silang',
                    'beneficiary_province' => 'Cavite',
                    'beneficiary_region' => 'Region IV-A',
                    'beneficiary_contact_number' => $client->contact_number,
                    'beneficiary_birthday' => $client->birthdate,
                    'beneficiary_age' => $client->age,
                    'beneficiary_sex' => $client->gender,
                    'beneficiary_civil_status' => $client->civil_status,
                    'beneficiary_occupation' => $client->occupation,
                    'beneficiary_monthly_salary' => $client->income ?: 0,
                    'beneficiary_category' => 'Indigent Resident',
                    'beneficiary_categories' => ['Indigent Resident'],
                    'has_representative' => false,
                    'social_worker_assessment' => $definition['interview']['social_worker_assessment'],
                    'recommended_assistance_type' => $definition['service_provided'],
                    'assistance_purpose' => $definition['purpose'],
                    'assistance_type' => $definition['purpose'],
                    'recommended_amount' => $definition['amount'] ?? 0,
                    'interviewed_by' => 'Maria Santos, RSW',
                    'reviewed_by' => 'Elena Ramos, MSWDO Officer',
                    'service_provided' => 'Financial Assistance Intake',
                    'purpose' => $definition['purpose'],
                    'submitted_to' => $definition['submitted_to'],
                ]
            );

            if ($isReleased) {
                AssistanceRecord::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'social_case_study_id' => $case->id,
                        'assistance_type' => $definition['service_provided'],
                    ],
                    [
                        'status' => 'Released',
                        'release_date' => $dateProcessed->copy()->addDays(7)->toDateString(),
                        'amount' => $definition['amount'],
                        'remarks' => 'Assistance released to the client.',
                    ]
                );
            }
        }

        // One rejection example so the eligibility/rejection flow has a record.
        $rejectedClient = Client::firstOrCreate(
            [
                'first_name' => 'Ramon',
                'last_name' => 'Castillo',
                'birthdate' => '1975-08-08',
            ],
            [
                'middle_name' => 'Navarro',
                'gender' => 'Male',
                'address' => 'Purok 5, Acacia, Silang, Cavite',
                'barangay' => 'Acacia',
                'contact_number' => '09981234567',
                'civil_status' => 'Married',
                'occupation' => 'Farmer',
                'income' => '9000.00',
                'religion' => 'Roman Catholic',
                'education' => 'Elementary Graduate',
                'birthplace' => 'Silang, Cavite',
            ]
        );

        $blocking = AssistanceRecord::updateOrCreate(
            [
                'client_id' => $rejectedClient->id,
                'assistance_type' => 'Medical Assistance',
                'release_date' => '2026-05-01',
            ],
            [
                'social_case_study_id' => null,
                'status' => 'Released',
                'amount' => 5000.00,
                'remarks' => 'Previous medical assistance released on May 2026.',
            ]
        );

        CaseRejection::updateOrCreate(
            ['client_id' => $rejectedClient->id, 'blocking_assistance_id' => $blocking->id],
            [
                'social_case_study_id' => null,
                'officer_id' => $checker->id,
                'officer_name' => $checker->name,
                'reason' => 'Ineligible due to the 6-month reapplication rule — a medical assistance was already released within the cooling-off period.',
                'last_assistance_date' => '2026-05-01',
                'last_assistance_type' => 'Medical Assistance',
                'next_eligible_date' => '2026-11-01',
                'rejected_at' => Carbon::parse('2026-06-10')->toDateTimeString(),
            ]
        );

        $this->command->info('Social Case module seeded: 5 case studies, 5 general intake sheets, assistance records and 1 rejection.');
    }

    private function officer(array $attributes): User
    {
        return User::updateOrCreate(
            ['email' => $attributes['email']],
            [
                'name' => $attributes['name'],
                'password' => Hash::make('password'),
                'role' => $attributes['role'],
                'status' => 'active',
            ]
        );
    }
}
