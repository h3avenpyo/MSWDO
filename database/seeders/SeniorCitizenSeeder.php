<?php

namespace Database\Seeders;

use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeniorCitizenSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        SeniorCitizenRecord::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $currentYear = date('Y');

        $barangays = [
            'Acacia', 'Adlas', 'Anahaw I', 'Anahaw II', 'Balite I', 'Balite II', 'Balubad', 'Banaba', 'Batas',
            'Biga I', 'Biga II', 'Biluso', 'Bucal', 'Buho', 'Bulihan', 'Cabangaan', 'Carmen', 'Hoyo', 'Hukay', 'Iba',
            'Inchican', 'Ipil I', 'Ipil II', 'Kalubkob', 'Kaong', 'Lalaan I', 'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil',
            'Maguyam', 'Malabag', 'Malaking Tatyao', 'Mataas na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
            'Paligawan', 'Pasong Langka', 'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Pooc I', 'Pooc II', 'Pulong Bunga', 'Pulong Saging',
            'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II', 'San Vicente I', 'San Vicente II', 'Santol',
            'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II', 'Tubuan III', 'Ulat', 'Yakal',
        ];

        $barangayCodes = [
            'Acacia' => 'ACA', 'Adlas' => 'ADL', 'Anahaw I' => 'ANA1', 'Anahaw II' => 'ANA2',
            'Balite I' => 'BLT1', 'Balite II' => 'BLT2', 'Balubad' => 'BLB', 'Banaba' => 'BAN', 'Batas' => 'BAT',
            'Biga I' => 'BIG1', 'Biga II' => 'BIG2', 'Biluso' => 'BIL', 'Bucal' => 'BUC', 'Buho' => 'BUH',
            'Bulihan' => 'BUL', 'Cabangaan' => 'CAB', 'Carmen' => 'CAR', 'Hoyo' => 'HOY', 'Hukay' => 'HUK',
            'Iba' => 'IBA', 'Inchican' => 'INC', 'Ipil I' => 'IPI1', 'Ipil II' => 'IPI2', 'Kalubkob' => 'KAL',
            'Kaong' => 'KAO', 'Lalaan I' => 'LAL1', 'Lalaan II' => 'LAL2', 'Litlit' => 'LIT', 'Lucsuhin' => 'LUC',
            'Lumil' => 'LUM', 'Maguyam' => 'MAG', 'Malabag' => 'MLB', 'Malaking Tatyao' => 'MLK',
            'Mataas na Burol' => 'MTA', 'Munting Ilog' => 'MUN', 'Narra I' => 'NAR1', 'Narra II' => 'NAR2',
            'Narra III' => 'NAR3', 'Paligawan' => 'PAL', 'Pasong Langka' => 'PAS',
            'Barangay I (Poblacion)' => 'POB1', 'Barangay II (Poblacion)' => 'POB2',
            'Barangay III (Poblacion)' => 'POB3', 'Barangay IV (Poblacion)' => 'POB4',
            'Barangay V (Poblacion)' => 'POB5', 'Pooc I' => 'POO1', 'Pooc II' => 'POO2',
            'Pulong Bunga' => 'PLB', 'Pulong Saging' => 'PLS', 'Puting Kahoy' => 'PUT', 'Sabutan' => 'SAB',
            'San Miguel I' => 'SMI1', 'San Miguel II' => 'SMI2', 'San Vicente I' => 'SVI1',
            'San Vicente II' => 'SVI2', 'Santol' => 'SAN', 'Tartaria' => 'TAR', 'Tibig' => 'TIB',
            'Toledo' => 'TOL', 'Tubuan I' => 'TUB1', 'Tubuan II' => 'TUB2', 'Tubuan III' => 'TUB3',
            'Ulat' => 'ULA', 'Yakal' => 'YAK',
        ];

        $firstNames = [
            'Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Carlos', 'Elena', 'Miguel', 'Sofia',
            'Antonio', 'Carmen', 'Francisco', 'Luz', 'Ricardo', 'Teresa', 'Luis', 'Patricia', 'Fernando', 'Isabel',
            'Gregorio', 'Natividad', 'Benedicto', 'Corazon', 'Emilio', 'Flor', 'Generoso', 'Hilda', 'Isagani', 'Juliana',
            'Lourdes', 'Melchor', 'Nora', 'Oscar', 'Pilar', 'Quintin', 'Remedios', 'Severino', 'Tiburcio', 'Unding',
            'Valentina', 'Wenceslao', 'Yolanda', 'Zenaida', 'Artemio', 'Belen', 'Cesar', 'Dionisio', 'Esperanza', 'Fidel',
        ];

        $middleNames = [
            'Santos', 'Cruz', 'Garcia', 'Rivera', 'Flores', 'Torres', 'Reyes', 'Bautista', 'Mendoza', 'Ramos',
            'Morales', 'Navarro', 'Villanueva', 'Castillo', 'Aquino', 'Pascual', 'Del Rosario', 'Tan', 'Ocampo', 'Dizon',
            'Mercado', 'Salazar', 'Villarosa', 'Manalo', 'Santiago', 'Gonzales', 'Dela Cruz', 'Fernandez', 'Lopez', 'Martin',
        ];

        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Rivera', 'Torres', 'Flores', 'Ramos',
            'Morales', 'Navarro', 'Villanueva', 'Castillo', 'Aquino', 'Pascual', 'Del Rosario', 'Tan', 'Ocampo', 'Dizon',
            'Mercado', 'Salazar', 'Villarosa', 'Manalo', 'Santiago', 'Gonzales', 'Dela Cruz', 'Fernandez', 'Lopez', 'Martin',
        ];

        $sexes = ['Male', 'Female'];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $civilStatuses = ['Single', 'Married', 'Widowed', 'Separated'];
        $relationships = ['Spouse', 'Child', 'Sibling', 'Parent'];

        $recordNumberCounter = 1;
        $sequenceCounter = [];
        $totalCreated = 0;

        foreach ($barangays as $barangay) {
            if (!isset($sequenceCounter[$barangay])) {
                $sequenceCounter[$barangay] = 1;
            }

            $barangayCode = $barangayCodes[$barangay];

            for ($i = 0; $i < 50; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $middleName = $middleNames[array_rand($middleNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $sex = $sexes[array_rand($sexes)];

                $age = rand(60, 90);
                $birthYear = $currentYear - $age;
                $birthMonth = rand(1, 12);
                $birthDay = rand(1, 28);
                $birthDate = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);

                $sequence = str_pad($sequenceCounter[$barangay], 6, '0', STR_PAD_LEFT);
                $controlNumber = "SC-{$barangayCode}-{$currentYear}-{$sequence}";
                $sequenceCounter[$barangay]++;

                $recordNumber = 'SR-' . str_pad($recordNumberCounter, 5, '0', STR_PAD_LEFT);
                $recordNumberCounter++;

                $contactNumber = '09' . str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT) . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);

                SeniorCitizenRecord::create([
                    'record_number' => $recordNumber,
                    'year_applied' => $currentYear,
                    'control_number' => $controlNumber,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'address' => ($i + 1) . ' Street, ' . $barangay . ', Silang, Cavite',
                    'barangay' => $barangay,
                    'birth_date' => $birthDate,
                    'sex' => $sex,
                    'contact_number' => $contactNumber,
                    'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                    'civil_status' => $civilStatuses[array_rand($civilStatuses)],
                    'osca_id' => 'OSCA-' . str_pad($recordNumberCounter, 4, '0', STR_PAD_LEFT),
                    'created_by' => 5,
                    'status' => 'active',
                    'emergency_contact_name' => $firstName . ' ' . $lastName . ' Jr.',
                    'emergency_contact_number' => '09' . str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT) . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT),
                    'emergency_contact_relationship' => $relationships[array_rand($relationships)],
                ]);

                $totalCreated++;
            }
        }

        $this->command->info("{$totalCreated} senior citizen records seeded (50 per barangay, " . count($barangays) . " barangays).");
    }
}
