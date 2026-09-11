<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Senior\SeniorCitizenRecord;
use Illuminate\Support\Facades\DB;

class InBetweenEligibleSeeder extends Seeder
{
    public function run(): void
    {
        $barangays = [
            'Acacia', 'Adlas', 'Anahaw I', 'Anahaw II', 'Balite I', 'Balite II',
            'Balubad', 'Banaba', 'Batas', 'Biga I', 'Biga II', 'Biluso',
            'Bucal', 'Buho', 'Bulihan', 'Cabangaan', 'Carmen', 'Hoyo',
            'Hukay', 'Iba', 'Inchican', 'Ipil I', 'Ipil II', 'Kalubkob',
            'Kaong', 'Lalaan I', 'Lalaan II', 'Litlit', 'Lucsuhin', 'Lumil',
            'Maguyam', 'Malabag', 'Malaking Tatyao', 'Mataas na Burol', 'Munting Ilog',
            'Narra I', 'Narra II', 'Narra III', 'Paligawan', 'Pasong Langka',
            'Barangay I (Poblacion)', 'Barangay II (Poblacion)', 'Barangay III (Poblacion)',
            'Barangay IV (Poblacion)', 'Barangay V (Poblacion)', 'Pooc I', 'Pooc II',
            'Pulong Bunga', 'Pulong Saging', 'Puting Kahoy', 'Sabutan',
            'San Miguel I', 'San Miguel II', 'San Vicente I', 'San Vicente II',
            'Santol', 'Tartaria', 'Tibig', 'Toledo', 'Tubuan I', 'Tubuan II',
            'Tubuan III', 'Ulat', 'Yakal'
        ];

        $barangayCodes = [
            'Acacia' => 'ACA', 'Adlas' => 'ADL', 'Anahaw I' => 'ANA1', 'Anahaw II' => 'ANA2',
            'Balite I' => 'BLT1', 'Balite II' => 'BLT2', 'Balubad' => 'BLB', 'Banaba' => 'BAN',
            'Batas' => 'BAT', 'Biga I' => 'BIG1', 'Biga II' => 'BIG2', 'Biluso' => 'BIL',
            'Bucal' => 'BUC', 'Buho' => 'BUH', 'Bulihan' => 'BUL', 'Cabangaan' => 'CAB',
            'Carmen' => 'CAR', 'Hoyo' => 'HOY', 'Hukay' => 'HUK', 'Iba' => 'IBA',
            'Inchican' => 'INC', 'Ipil I' => 'IPI1', 'Ipil II' => 'IPI2', 'Kalubkob' => 'KAL',
            'Kaong' => 'KAO', 'Lalaan I' => 'LAL1', 'Lalaan II' => 'LAL2', 'Litlit' => 'LIT',
            'Lucsuhin' => 'LUC', 'Lumil' => 'LUM', 'Maguyam' => 'MAG', 'Malabag' => 'MLB',
            'Malaking Tatyao' => 'MLK', 'Mataas na Burol' => 'MTA', 'Munting Ilog' => 'MUN',
            'Narra I' => 'NAR1', 'Narra II' => 'NAR2', 'Narra III' => 'NAR3', 'Paligawan' => 'PAL',
            'Pasong Langka' => 'PAS', 'Barangay I (Poblacion)' => 'POB1', 'Barangay II (Poblacion)' => 'POB2',
            'Barangay III (Poblacion)' => 'POB3', 'Barangay IV (Poblacion)' => 'POB4', 'Barangay V (Poblacion)' => 'POB5',
            'Pooc I' => 'POO1', 'Pooc II' => 'POO2', 'Pulong Bunga' => 'PLB', 'Pulong Saging' => 'PLS',
            'Puting Kahoy' => 'PUT', 'Sabutan' => 'SAB', 'San Miguel I' => 'SMI1', 'San Miguel II' => 'SMI2',
            'San Vicente I' => 'SVI1', 'San Vicente II' => 'SVI2', 'Santol' => 'SAN', 'Tartaria' => 'TAR',
            'Tibig' => 'TIB', 'Toledo' => 'TOL', 'Tubuan I' => 'TUB1', 'Tubuan II' => 'TUB2',
            'Tubuan III' => 'TUB3', 'Ulat' => 'ULA', 'Yakal' => 'YAK'
        ];

        $firstNames = ['Maria', 'Jose', 'Carmen', 'Antonio', 'Rosa', 'Juan', 'Teresa', 'Pedro', 'Sofia', 'Miguel', 'Ana', 'Carlos', 'Luz', 'Francisco', 'Elena', 'Ricardo', 'Lourdes', 'Fernando', 'Isabel', 'Roberto', 'Concepcion', 'Luis', 'Mercedes', 'Ramon', 'Victoria'];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Fernandez', 'Ramos', 'Flores', 'Mendoza', 'Castillo', 'Torres', 'Rivera', 'Morales', 'Navarro', 'Villanueva', 'Santiago', 'Del Rosario', 'Aquino', 'Dizon', 'Tan', 'Lim', 'Ong', 'Wong', 'Lee'];
        $sexes = ['Male', 'Female'];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $civilStatuses = ['Single', 'Married', 'Widowed', 'Separated'];

        $year = 2026;
        $totalRecords = 0;

        // Generate 100 seniors aged 81-84 for every barangay.
        foreach ($barangays as $barangay) {
            for ($i = 1; $i <= 100; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $middleInitial = chr(rand(65, 90)) . '.';
                $sex = $sexes[array_rand($sexes)];
                $bloodType = $bloodTypes[array_rand($bloodTypes)];
                $civilStatus = $civilStatuses[array_rand($civilStatuses)];

                // Generate birth date for ages 81-84 (born 1942-1945)
                $birthYear = rand(1942, 1945);
                $birthMonth = rand(1, 12);
                $birthDay = rand(1, 28);
                $birthDate = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);

                // Generate a control number unique across all barangays.
                $sequence = str_pad($i, 6, '0', STR_PAD_LEFT);
                $controlNumber = 'SC-' . $barangayCodes[$barangay] . '-' . $year . '-' . $sequence;

                // Generate contact number
                $contactNumber = '09' . rand(10, 99) . rand(1000000, 9999999);

                // Generate emergency contact
                $emergencyNames = ['Juan', 'Maria', 'Pedro', 'Ana', 'Jose', 'Rosa'];
                $emergencyLastNames = ['Santos', 'Reyes', 'Cruz', 'Garcia', 'Fernandez'];
                $emergencyContactName = $emergencyNames[array_rand($emergencyNames)] . ' ' . $emergencyLastNames[array_rand($emergencyLastNames)];
                $emergencyContactNumber = '09' . rand(10, 99) . rand(1000000, 9999999);
                $emergencyRelationships = ['Spouse', 'Child', 'Sibling', 'Parent', 'Relative'];
                $emergencyContactRelationship = $emergencyRelationships[array_rand($emergencyRelationships)];

                SeniorCitizenRecord::create([
                    'control_number' => $controlNumber,
                    'osca_id' => $controlNumber,
                    'first_name' => $firstName,
                    'middle_name' => $middleInitial,
                    'last_name' => $lastName,
                    'address' => 'House ' . rand(1, 100) . ', Street ' . rand(1, 20) . ', ' . $barangay,
                    'barangay' => $barangay,
                    'birth_date' => $birthDate,
                    'sex' => $sex,
                    'contact_number' => $contactNumber,
                    'philsys_number' => 'PHL-' . rand(100000000, 999999999),
                    'rrn_number' => 'RRN-' . rand(100000000, 999999999),
                    'remarks' => 'Auto-generated for In-Between Benefits testing (age 81-84)',
                    'status' => 'active',
                    'year_applied' => $year,
                    'created_by' => 1,
                    'blood_type' => $bloodType,
                    'civil_status' => $civilStatus,
                    'emergency_contact_name' => $emergencyContactName,
                    'emergency_contact_number' => $emergencyContactNumber,
                    'emergency_contact_relationship' => $emergencyContactRelationship,
                ]);

                $totalRecords++;
            }
        }

        $this->command->info('Successfully created ' . $totalRecords . ' senior citizen records (100 per barangay) aged 81-84 for In-Between Benefits testing.');
    }
}
