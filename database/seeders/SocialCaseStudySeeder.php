<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\SocialCaseStudy;
use App\Models\CaseInterview;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialCaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $encoder = User::where('role', 'social_worker')->first()
            ?? User::where('role', 'admin')->first();

        $barangays = [
            'Acacia', 'Adlas', 'Bucal', 'Lumil', 'Bulihan',
            'Barangay I (Poblacion)', 'Maguyam', 'Sabutan', 'Kaong', 'Tibig',
        ];

        $firstNames = [
            'Rodel', 'Cristina', 'Mark', 'Angel', 'Jerome',
            'Lovely', 'Dennis', 'Joy', 'Ramon', 'Cherry',
            'Bong', 'Nancy', 'Joel', 'Marites', 'Allan',
            'Grace', 'Paolo', 'Rita', 'Nelson', 'Tess',
        ];
        $lastNames = [
            'Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Torres',
            'Flores', 'Mendoza', 'Rivera', 'Ramos', 'Bautista',
        ];
        $middleNames = ['Santos', 'Cruz', 'Garcia', 'Reyes', 'Torres', null, null];

        $services = [
            'Medical Assistance', 'Educational Assistance', 'Burial Assistance',
            'Food Assistance', 'Financial Assistance', 'Housing Assistance',
            'Legal Assistance', 'Livelihood Assistance',
        ];
        $purposes = [
            'Hospital bill payment', 'School tuition fee', 'Funeral expenses',
            'Daily sustenance', 'Emergency cash aid', 'House repair',
            'Legal fees', 'Capital for small business',
        ];
        $agencies = [
            'DSWD', 'LGU Silang', 'DOH', 'DepEd', 'PCSO', 'PhilHealth', 'Pag-IBIG',
        ];

        $statuses = ['Open', 'In Progress', 'Pending Release', 'Released', 'Closed', 'Rejected'];
        $workflowSteps = [
            'requirements_verification', 'intake_interview', 'evaluation',
            'report_generation', 'assistance_release', 'completed',
        ];

        $relationships = ['Father', 'Mother', 'Spouse', 'Child', 'Sibling', 'Grandparent'];
        $occupations = ['Vendor', 'Driver', 'Farmer', 'Construction Worker', 'Housewife', 'Unemployed', 'Laborer', 'Fisherman', null];
        $illnesses = ['Diabetes', 'Hypertension', 'Asthma', 'Tuberculosis', 'Heart Disease', 'Arthritis', 'None'];
        $recommendations = ['Approve financial assistance', 'Refer to DSWD', 'Provide medical aid', 'Approved for burial assistance', 'Refer to livelihood program'];

        $caseCounter = 1;

        for ($i = 0; $i < 20; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $middleName = $middleNames[array_rand($middleNames)];
            $gender = rand(0, 1) ? 'Male' : 'Female';
            $barangay = $barangays[array_rand($barangays)];
            $birthYear = rand(1940, 1995);
            $birthMonth = rand(1, 12);
            $birthDay = rand(1, 28);

            $client = Client::create([
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'birthdate' => sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay),
                'gender' => $gender,
                'address' => rand(1, 100) . ' Street, ' . $barangay . ', Silang, Cavite',
                'barangay' => $barangay,
                'contact_number' => '09' . str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT) . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
            ]);

            $statusIndex = array_rand($statuses);
            $status = $statuses[$statusIndex];
            $workflowIndex = min($statusIndex, count($workflowSteps) - 1);

            $isComplete = $status === 'Released' || $status === 'Closed';
            $isInProgress = $status === 'In Progress' || $status === 'Pending Release';
            $isReleased = $status === 'Released' || $status === 'Closed';

            $dateProcessed = now()->subDays(rand(1, 90));
            $interviewDate = $dateProcessed->copy()->addDays(rand(1, 5));
            $assistanceAmount = $isReleased ? [2000, 3000, 5000, 7500, 10000, 15000][array_rand([2000, 3000, 5000, 7500, 10000, 15000])] : null;

            $caseNumber = 'SCS-' . str_pad($caseCounter, 4, '0', STR_PAD_LEFT);
            $caseCounter++;

            $socialCase = SocialCaseStudy::create([
                'client_id' => $client->id,
                'officer_id' => $encoder->id,
                'case_number' => $caseNumber,
                'date_processed' => $dateProcessed->toDateString(),
                'service_provided' => $services[array_rand($services)],
                'purpose' => $purposes[array_rand($purposes)],
                'submitted_to' => $agencies[array_rand($agencies)],
                'encoded_by' => $encoder->id,
                'status' => $status,
                'summary' => "Case study for {$client->full_name} regarding " . strtolower($services[array_rand($services)]) . ".",
                'interview_date' => $interviewDate->toDateString(),
                'workflow_step' => $workflowSteps[$workflowIndex],
                'requirements_complete' => $isInProgress || $isComplete,
                'interview_complete' => $workflowIndex >= 1,
                'evaluation_complete' => $workflowIndex >= 2,
                'report_generated' => $workflowIndex >= 3,
                'assistance_released' => $isReleased,
                'assistance_amount' => $assistanceAmount,
                'assistance_date' => $isReleased ? $dateProcessed->copy()->addDays(rand(5, 15))->toDateString() : null,
                'released_at' => $isReleased ? $dateProcessed->copy()->addDays(rand(5, 15))->toDateTimeString() : null,
                'released_by' => $isReleased ? $encoder->id : null,
                'released_to' => $isReleased ? $client->full_name : null,
            ]);

            CaseInterview::create([
                'social_case_study_id' => $socialCase->id,
                'interview_reason' => $purposes[array_rand($purposes)],
                'interview_situation' => 'Family is experiencing financial difficulty due to ' . ['job loss', 'medical emergency', 'natural disaster', 'death in the family', 'pandemic impact'][array_rand(['job loss', 'medical emergency', 'natural disaster', 'death in the family', 'pandemic impact'])] . '.',
                'interview_household' => 'Household has ' . rand(3, 8) . ' members living in a ' . ['concrete', 'wood', 'mixed'][array_rand(['concrete', 'wood', 'mixed'])] . ' house.',
                'monthly_income' => rand(5000, 25000),
                'monthly_expenses' => rand(4000, 20000),
                'family_illnesses' => $illnesses[array_rand($illnesses)],
                'previous_assistance' => rand(0, 1) ? 'Received ' . $services[array_rand($services)] . ' last year' : 'None',
                'interview_notes' => 'Client appears ' . ['distressed', 'cooperative', 'hopeful', 'anxious'][array_rand(['distressed', 'cooperative', 'hopeful', 'anxious'])] . ' during interview.',
                'social_worker_assessment' => 'Based on the interview, the client is ' . ['eligible', 'partially eligible', 'ineligible'][array_rand(['eligible', 'partially eligible', 'ineligible'])] . ' for assistance.',
                'recommendation' => $recommendations[array_rand($recommendations)],
                'recommended_amount' => $assistanceAmount,
            ]);

            $familyCount = rand(2, 5);
            for ($f = 0; $f < $familyCount; $f++) {
                $fGender = rand(0, 1) ? 'Male' : 'Female';
                $fFirstName = $firstNames[array_rand($firstNames)];
                $fLastName = $lastName;

                FamilyMember::create([
                    'social_case_study_id' => $socialCase->id,
                    'full_name' => $fFirstName . ' ' . $fLastName,
                    'relationship' => $relationships[array_rand($relationships)],
                    'age' => rand(5, 70),
                    'sex' => $fGender,
                    'occupation' => $occupations[array_rand($occupations)],
                    'monthly_income' => rand(0, 15000),
                    'is_dependent' => rand(0, 3) === 0,
                ]);
            }
        }

        $this->command->info('20 social case studies seeded (with clients, interviews, and family members).');
    }
}
