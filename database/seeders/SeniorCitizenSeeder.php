<?php

namespace Database\Seeders;

use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeniorCitizenSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing records using DB statement for foreign key constraints
        DB::connection('mswdo_senior')->statement('SET FOREIGN_KEY_CHECKS=0');
        SeniorCitizenRecord::on('mswdo_senior')->truncate();
        DB::connection('mswdo_senior')->statement('SET FOREIGN_KEY_CHECKS=1');

        $barangays = [
            'Acacia', 'Adlas', 'Anahaw I', 'Anahaw II', 'Balite I', 'Balite II', 'Balubad', 'Banaba', 'Batas',
            'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho', 'Bulihan', 'Cabangaan', 'Carmen', 'Hoyo', 'Hukay', 'Iba',
            'Inchican', 'Ipil I', 'Ipil II', 'Kalubkob', 'Kaong', 'Lalaan I', 'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil',
            'Maguyam', 'Malabag', 'Malaking Tatyao', 'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I', 'San Vicente II', 'Santol',
            'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II', 'Tubuan III', 'Ulat', 'Yakal'
        ];

        // Create unique barangay codes mapping
        $barangayCodes = [
            'Acacia' => 'ACA',
            'Adlas' => 'ADL',
            'Anahaw I' => 'ANA1',
            'Anahaw II' => 'ANA2',
            'Balite I' => 'BLT1',
            'Balite II' => 'BLT2',
            'Balubad' => 'BLB',
            'Banaba' => 'BAN',
            'Batas' => 'BAT',
            'Biga I' => 'BIG1',
            'Biga II' => 'BIG2',
            'Biluso' => 'BIL',
            'Bucal' => 'BUC',
            'Buho' => 'BUH',
            'Bulihan' => 'BUL',
            'Cabangaan' => 'CAB',
            'Carmen' => 'CAR',
            'Hoyo' => 'HOY',
            'Hukay' => 'HUK',
            'Iba' => 'IBA',
            'Inchican' => 'INC',
            'Ipil I' => 'IPI1',
            'Ipil II' => 'IPI2',
            'Kalubkob' => 'KAL',
            'Kaong' => 'KAO',
            'Lalaan I' => 'LAL1',
            'Lalaan II' => 'LAL2',
            'Litlit' => 'LIT',
            'Lucsuhin' => 'LUC',
            'Lumil' => 'LUM',
            'Maguyam' => 'MAG',
            'Malabag' => 'MLB',
            'Malaking Tatyao' => 'MLK',
            'Mataas na Burol' => 'MTA',
            'Munting Ilog' => 'MUN',
            'Narra I' => 'NAR1',
            'Narra II' => 'NAR2',
            'Narra III' => 'NAR3',
            'Paligawan' => 'PAL',
            'Pasong Langka' => 'PAS',
            'Barangay I (Poblacion)' => 'POB1',
            'Barangay II (Poblacion)' => 'POB2',
            'Barangay III (Poblacion)' => 'POB3',
            'Barangay IV (Poblacion)' => 'POB4',
            'Barangay V (Poblacion)' => 'POB5',
            'Pooc I' => 'POO1',
            'Pooc II' => 'POO2',
            'Pulong Bunga' => 'PLB',
            'Pulong Saging' => 'PLS',
            'Puting Kahoy' => 'PUT',
            'Sabutan' => 'SAB',
            'San Miguel I' => 'SMI1',
            'San Miguel II' => 'SMI2',
            'San Vicente I' => 'SVI1',
            'San Vicente II' => 'SVI2',
            'Santol' => 'SAN',
            'Tartaria' => 'TAR',
            'Tibig' => 'TIB',
            'Toledo' => 'TOL',
            'Tubuan I' => 'TUB1',
            'Tubuan II' => 'TUB2',
            'Tubuan III' => 'TUB3',
            'Ulat' => 'ULA',
            'Yakal' => 'YAK'
        ];

        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Carlos', 'Elena', 'Miguel', 'Sofia', 'Antonio', 'Carmen', 'Francisco', 'Luz', 'Ricardo', 'Teresa', 'Luis', 'Patricia', 'Fernando', 'Isabel'];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Rivera', 'Torres', 'Flores', 'Ramos', 'Morales', 'Navarro', 'Villanueva', 'Castillo', 'Aquino', 'Pascual', 'Del Rosario', 'Tan', 'Ocampo', 'Dizon'];
        $sexes = ['Male', 'Female'];
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        $sequenceCounter = [];
        $recordNumberCounter = 1;

        // Calculate date ranges for birthday distribution
        $today = date('Y-m-d');
        $next7DaysStart = date('Y-m-d', strtotime('+1 day'));
        $next7DaysEnd = date('Y-m-d', strtotime('+7 days'));
        $nextMonthStart = date('Y-m-01', strtotime('+1 month'));
        $nextMonthEnd = date('Y-m-t', strtotime('+1 month'));

        // Target distribution: 671 total seniors
        // Today: 0, Next 7 days: 10, Next month: 53, Others: 608
        $targetTotal = 671;
        $targetToday = 0;
        $targetNext7Days = 10;
        $targetNextMonth = 53;
        $targetOthers = $targetTotal - $targetToday - $targetNext7Days - $targetNextMonth;

        // Generate birth dates for each category
        $birthDates = [];

        // Today's birthdays (0)
        for ($i = 0; $i < $targetToday; $i++) {
            $birthDates[] = $today;
        }

        // Next 7 days birthdays (10)
        for ($i = 0; $i < $targetNext7Days; $i++) {
            $dayOffset = rand(1, 7);
            $birthDates[] = date('Y-m-d', strtotime("+$dayOffset days"));
        }

        // Next month birthdays (53)
        for ($i = 0; $i < $targetNextMonth; $i++) {
            $dayOffset = rand(1, 28);
            $birthDates[] = date('Y-m-d', strtotime("+1 month +$dayOffset days"));
        }

        // Other birthdays (608) - random dates throughout the year
        for ($i = 0; $i < $targetOthers; $i++) {
            $monthOffset = rand(2, 11); // Skip current and next month
            $dayOffset = rand(1, 28);
            $birthDates[] = date('Y-m-d', strtotime("+$monthOffset months +$dayOffset days"));
        }

        // Shuffle birth dates
        shuffle($birthDates);

        $birthDateIndex = 0;

        foreach ($barangays as $barangay) {
            // Initialize sequence counter for this barangay
            if (!isset($sequenceCounter[$barangay])) {
                $sequenceCounter[$barangay] = 1;
            }

            // Calculate seniors per barangay to reach exactly 671 total
            // Distribute unevenly: some barangays get more, some get fewer
            $baseCount = floor($targetTotal / count($barangays));
            $remaining = $targetTotal % count($barangays);
            $seniorCount = $baseCount + ($remaining > 0 ? 1 : 0);
            $remaining--;

            // Make it more uneven by random adjustment
            $adjustment = rand(-5, 5);
            $seniorCount = max(0, min(20, $seniorCount + $adjustment));

            for ($i = 1; $i <= $seniorCount && $birthDateIndex < count($birthDates); $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $fullName = $firstName . ' ' . $lastName;
                $sex = $sexes[array_rand($sexes)];

                // Use pre-generated birth date
                $birthDate = $birthDates[$birthDateIndex];
                $birthDateIndex++;

                // Calculate age from birth date (60-85 years old)
                $birthDateTime = new \DateTime($birthDate);
                $currentDateTime = new \DateTime();
                $age = $currentDateTime->diff($birthDateTime)->y;

                // Ensure age is in valid range
                if ($age < 60 || $age > 85) {
                    $age = rand(60, 85);
                    $birthYear = $currentYear - $age;
                    $birthMonth = rand(1, 12);
                    $birthDay = rand(1, 28);
                    $birthDate = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);
                }

                $birthMonth = date('m', strtotime($birthDate));
                $month = $months[$birthMonth - 1];

                // Generate barangay code using the unique mapping
                $barangayCode = $barangayCodes[$barangay];
                
                // Generate control number
                $sequence = str_pad($sequenceCounter[$barangay], 6, '0', STR_PAD_LEFT);
                $controlNumber = "SC-{$barangayCode}-{$currentYear}-{$sequence}";
                $sequenceCounter[$barangay]++;

                // Generate unique record number
                $recordNumber = 'SR-' . str_pad($recordNumberCounter, 5, '0', STR_PAD_LEFT);
                $recordNumberCounter++;

                // Generate random contact number
                $contactNumber = '09' . str_pad(rand(0, 99999999), 9, '0', STR_PAD_LEFT);

                SeniorCitizenRecord::on('mswdo_senior')->create([
                    'record_number' => $recordNumber,
                    'year_applied' => $currentYear,
                    'control_number' => $controlNumber,
                    'full_name' => $fullName,
                    'address' => "Street {$i}, {$barangay}, Silang, Cavite",
                    'barangay' => $barangay,
                    'birth_date' => $birthDate,
                    'month' => $month,
                    'sex' => $sex,
                    'age' => $age,
                    'contact_number' => $contactNumber,
                    'philsys_number' => rand(1000000000000, 9999999999999),
                    'rrn_number' => rand(1000000000000, 9999999999999),
                    'remarks' => null,
                    'osca_id' => 'OSCA-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'created_by' => 1,
                    'status' => 'active',
                    'senior_id_number' => null,
                    'photo' => null,
                    'qr_code' => null,
                    'date_issued' => null,
                    'last_printed_at' => null,
                    'print_count' => 0,
                    'blood_type' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'][array_rand(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
                    'civil_status' => ['Single', 'Married', 'Widowed', 'Separated'][array_rand(['Single', 'Married', 'Widowed', 'Separated'])],
                    'emergency_contact_name' => $firstName . ' ' . $lastName . ' Jr.',
                    'emergency_contact_number' => '09' . str_pad(rand(0, 99999999), 9, '0', STR_PAD_LEFT),
                    'emergency_contact_relationship' => ['Spouse', 'Child', 'Sibling', 'Parent'][array_rand(['Spouse', 'Child', 'Sibling', 'Parent'])],
                ]);
            }
        }

        $this->command->info('Senior citizen records seeded successfully (uneven distribution 0-20 per barangay).');
    }
}
