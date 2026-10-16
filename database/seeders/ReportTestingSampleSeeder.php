<?php

namespace Database\Seeders;

use App\Enums\PayoutStatus;
use App\Enums\SeniorStatus;
use App\Models\Client;
use App\Models\Senior\BirthdayPayout;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\SocialCase\SocialCaseStudy;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportTestingSampleSeeder extends Seeder
{
    public function run(): void
    {
        $barangays = [
            'Biga I', 'Biga II', 'Bulihan', 'Lumil', 'Biluso', 
            'Kaong', 'Lucsuhin', 'Puting Kahoy', 'Tartaria', 
            'Balite I', 'Sabutan', 'Adlas', 'Tibig', 'Acacia', 'Bucal'
        ];

        $firstNames = [
            'Rodel', 'Cristina', 'Mark', 'Angelica', 'Jerome', 
            'Lovely', 'Dennis', 'Joy', 'Ramon', 'Cherry', 
            'Nancy', 'Joel', 'Marites', 'Allan', 'Grace', 
            'Paolo', 'Rita', 'Nelson', 'Teresa', 'Eduardo'
        ];

        $lastNames = [
            'Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Torres', 
            'Flores', 'Mendoza', 'Rivera', 'Ramos', 'Bautista',
            'Aquino', 'Castillo', 'Villanueva', 'Navarro', 'Mercado'
        ];

        $socialServices = [
            'Medical Assistance' => ['Hospital bill subsidy', 'Chemotherapy assistance', 'Dialysis medication', 'Maintenance drugs'],
            'Burial Assistance' => ['Funeral and burial expense', 'Casket subsidy', 'Cemetery lot fee'],
            'Educational Assistance' => ['College tuition subsidy', 'Vocational training fees', 'School supplies allowance'],
            'Emergency Food Aid' => ['Nutritional support for family', 'Disaster emergency relief', 'Daily food assistance'],
            'Transportation Assistance' => ['Stranded passenger fare', 'Medical referral travel aid', 'Provincial repatriation'],
        ];

        $financialTypes = [
            'Medical Assistance' => [3000, 5000, 7500, 10000],
            'Burial Assistance' => [5000, 8000, 10000],
            'Educational Aid' => [2500, 3500, 5000],
            'Food & Subsistence' => [2000, 3000, 4000],
            'Crisis Emergency Aid' => [3000, 5000, 6000],
        ];

        $now = Carbon::now();

        // Specific representative dates across 2026:
        $dateClusters = [
            // This week (Weekly test)
            Carbon::create(2026, 9, 17, 10, 30),
            Carbon::create(2026, 9, 16, 14, 15),
            Carbon::create(2026, 9, 15, 9, 0),
            Carbon::create(2026, 9, 14, 11, 45),
            
            // Earlier in September (Monthly test)
            Carbon::create(2026, 9, 11, 15, 20),
            Carbon::create(2026, 9, 8, 10, 0),
            Carbon::create(2026, 9, 5, 13, 10),
            Carbon::create(2026, 9, 2, 9, 30),

            // August 2026 (Q3 test)
            Carbon::create(2026, 8, 25, 11, 0),
            Carbon::create(2026, 8, 18, 14, 30),
            Carbon::create(2026, 8, 10, 10, 15),
            Carbon::create(2026, 8, 4, 16, 0),

            // July 2026 (Q3 test)
            Carbon::create(2026, 7, 28, 10, 0),
            Carbon::create(2026, 7, 15, 14, 45),
            Carbon::create(2026, 7, 6, 9, 15),

            // Q2 (April - June 2026)
            Carbon::create(2026, 6, 18, 11, 0),
            Carbon::create(2026, 5, 20, 13, 30),
            Carbon::create(2026, 4, 14, 10, 15),

            // Q1 (Jan - March 2026)
            Carbon::create(2026, 3, 12, 14, 0),
            Carbon::create(2026, 2, 18, 9, 45),
            Carbon::create(2026, 1, 22, 15, 30),
        ];

        // ─────────────────────────────────────────────────────────────
        // 1. Seed Social Case Studies
        // ─────────────────────────────────────────────────────────────
        $scStatuses = ['Draft', 'In Progress', 'Review', 'Completed', 'Printed', 'Released', 'Archived'];

        foreach ($dateClusters as $idx => $date) {
            $fName = $firstNames[array_rand($firstNames)];
            $lName = $lastNames[array_rand($lastNames)];
            $fullName = "{$fName} {$lName}";
            $brgy = $barangays[array_rand($barangays)];
            $serviceKeys = array_keys($socialServices);
            $service = $serviceKeys[array_rand($serviceKeys)];
            $purposes = $socialServices[$service];
            $purpose = $purposes[array_rand($purposes)];
            $status = $scStatuses[array_rand($scStatuses)];
            $amount = in_array($status, ['Completed', 'Printed', 'Released', 'Archived']) ? rand(3, 10) * 1000 : 0;
            $isReleased = in_array($status, ['Completed', 'Released', 'Printed']);

            $caseNum = sprintf('SC-2026-%04d', 800 + $idx);

            $client = Client::firstOrCreate(
                ['first_name' => $fName, 'last_name' => $lName],
                [
                    'middle_name' => 'Cruz',
                    'birthdate' => Carbon::create(rand(1950, 2000), rand(1, 12), rand(1, 28))->toDateString(),
                    'gender' => rand(0, 1) ? 'Male' : 'Female',
                    'address' => 'Purok ' . rand(1, 6),
                    'barangay' => $brgy,
                    'contact_number' => '09' . rand(100000000, 999999999),
                    'civil_status' => 'Single',
                ]
            );

            SocialCaseStudy::updateOrCreate(
                ['case_number' => $caseNum],
                [
                    'main_client_id' => $client->id,
                    'intake_first_name' => $fName,
                    'intake_last_name' => $lName,
                    'intake_full_name' => $fullName,
                    'intake_barangay' => $brgy,
                    'intake_gender' => rand(0, 1) ? 'Male' : 'Female',
                    'intake_age' => rand(22, 75),
                    'service_provided' => $service,
                    'purpose' => $purpose,
                    'status' => $status,
                    'assistance_amount' => $amount,
                    'assistance_released' => $isReleased,
                    'released_at' => $isReleased ? $date : null,
                    'date_processed' => $date->toDateString(),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );
        }

        // ─────────────────────────────────────────────────────────────
        // 2. Seed Financial Assistance Intakes
        // ─────────────────────────────────────────────────────────────
        foreach ($dateClusters as $idx => $date) {
            $fName = $firstNames[array_rand($firstNames)];
            $lName = $lastNames[array_rand($lastNames)];
            $brgy = $barangays[array_rand($barangays)];
            $catKeys = array_keys($financialTypes);
            $cat = $catKeys[array_rand($catKeys)];
            $amounts = $financialTypes[$cat];
            $amount = $amounts[array_rand($amounts)];

            $ctrlNum = sprintf('FA-2026-%04d', 800 + $idx);

            $client = Client::firstOrCreate(
                ['first_name' => $fName, 'last_name' => $lName],
                [
                    'middle_name' => 'Santos',
                    'birthdate' => Carbon::create(rand(1955, 2002), rand(1, 12), rand(1, 28))->toDateString(),
                    'gender' => rand(0, 1) ? 'Male' : 'Female',
                    'address' => 'Purok ' . rand(1, 6),
                    'barangay' => $brgy,
                    'contact_number' => '09' . rand(100000000, 999999999),
                    'civil_status' => 'Married',
                ]
            );

            BeneficiaryIntake::updateOrCreate(
                ['control_number' => $ctrlNum],
                [
                    'client_id' => $client->id,
                    'is_client_beneficiary' => true,
                    'beneficiary_first_name' => $fName,
                    'beneficiary_last_name' => $lName,
                    'beneficiary_barangay' => $brgy,
                    'beneficiary_age' => rand(25, 78),
                    'beneficiary_sex' => rand(0, 1) ? 'Male' : 'Female',
                    'service_provided' => $cat,
                    'recommended_assistance_type' => $cat,
                    'purpose' => "Direct {$cat} assistance subsidy",
                    'submitted_to' => 'MSWDO Silang',
                    'recommended_amount' => $amount,
                    'date_processed' => $date->toDateString(),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );
        }

        // ─────────────────────────────────────────────────────────────
        // 3. Seed Senior Citizen Registrations & Birthday Payouts
        // ─────────────────────────────────────────────────────────────
        $existingSeniors = SeniorCitizenRecord::take(25)->get();

        foreach ($dateClusters as $idx => $date) {
            // Check or create Senior
            $fName = $firstNames[array_rand($firstNames)];
            $lName = $lastNames[array_rand($lastNames)];
            $brgy = $barangays[array_rand($barangays)];
            $ctrlNum = sprintf('SR-2026-%04d', 800 + $idx);
            $birthYear = rand(1925, 1965);

            $senior = SeniorCitizenRecord::updateOrCreate(
                ['control_number' => $ctrlNum],
                [
                    'first_name' => $fName,
                    'last_name' => $lName,
                    'barangay' => $brgy,
                    'birth_date' => Carbon::create($birthYear, rand(1, 12), rand(1, 28))->toDateString(),
                    'sex' => rand(0, 1) ? 'Male' : 'Female',
                    'status' => SeniorStatus::Active,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );

            // Create a Birthday Payout for this senior for 2026
            BirthdayPayout::updateOrCreate(
                [
                    'senior_id' => $senior->id,
                    'payout_year' => 2026,
                ],
                [
                    'amount' => 1000.00,
                    'status' => PayoutStatus::Released,
                    'released_date' => $date,
                    'remarks' => 'Birthday Cash Gift Program 2026',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );
        }

        echo "Report testing sample data successfully seeded across all services and intervals!\n";
    }
}
