<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeniorCitizenTopUpSeeder extends Seeder
{
    private const TARGET_PER_BARANGAY = 50;

    public function run(): void
    {
        $barangays = [
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
            'Yakal' => 'YAK',
        ];

        $firstNames = [
            'Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Carlos', 'Elena', 'Miguel', 'Sofia',
            'Antonio', 'Carmen', 'Francisco', 'Luz', 'Ricardo', 'Teresa', 'Luis', 'Patricia', 'Fernando', 'Isabel',
            'Gregorio', 'Natividad', 'Benedicto', 'Corazon', 'Emilio', 'Flor', 'Generoso', 'Hilda', 'Isagani', 'Juliana',
        ];
        $middleNames = [
            'Santos', 'Cruz', 'Garcia', 'Rivera', 'Flores', 'Torres', 'Reyes', 'Bautista', 'Mendoza', 'Ramos',
            'Morales', 'Navarro', 'Villanueva', 'Castillo', 'Aquino', 'Pascual', 'Del Rosario', 'Ocampo', 'Dizon', 'Mercado',
        ];
        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Rivera', 'Torres', 'Flores', 'Ramos',
            'Morales', 'Navarro', 'Villanueva', 'Castillo', 'Aquino', 'Pascual', 'Del Rosario', 'Ocampo', 'Dizon', 'Mercado',
        ];
        $sexes = ['Male', 'Female'];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $civilStatuses = ['Single', 'Married', 'Widowed', 'Separated'];
        $relationships = ['Spouse', 'Child', 'Sibling', 'Parent'];
        $currentYear = now()->year;
        $now = now()->toDateTimeString();
        $createdBy = DB::table('users')->value('id');
        $createdCount = 0;
        $records = [];

        DB::transaction(function () use (
            $barangays,
            $firstNames,
            $middleNames,
            $lastNames,
            $sexes,
            $bloodTypes,
            $civilStatuses,
            $relationships,
            $currentYear,
            $now,
            $createdBy,
            &$createdCount,
            &$records
        ): void {
            foreach ($barangays as $barangay => $barangayCode) {
                $existing = DB::table('senior_citizen_records')
                    ->where('barangay', $barangay)
                    ->where('status', 'active')
                    ->pluck('control_number');
                $toCreate = max(0, self::TARGET_PER_BARANGAY - $existing->count());
                $sequence = 1;
                $controlNumberPattern = '/^SC-' . preg_quote($barangayCode, '/') . '-' . $currentYear . '-(\d+)$/';

                foreach ($existing as $controlNumber) {
                    if (preg_match($controlNumberPattern, (string) $controlNumber, $matches)) {
                        $sequence = max($sequence, (int) $matches[1] + 1);
                    }
                }

                for ($index = 0; $index < $toCreate; $index++) {
                    $firstName = $firstNames[array_rand($firstNames)];
                    $middleName = $middleNames[array_rand($middleNames)];
                    $lastName = $lastNames[array_rand($lastNames)];
                    $birthDate = now()
                        ->subYears(random_int(60, 90))
                        ->subMonths(random_int(0, 11))
                        ->subDays(random_int(0, 27))
                        ->toDateString();
                    $controlNumber = sprintf('SC-%s-%d-%06d', $barangayCode, $currentYear, $sequence++);
                    $phoneNumber = '09' . random_int(10, 99) . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);

                    $records[] = [
                        'year_applied' => (string) $currentYear,
                        'control_number' => $controlNumber,
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'address' => random_int(1, 500) . ' Street, ' . $barangay . ', Silang, Cavite',
                        'barangay' => $barangay,
                        'birth_date' => $birthDate,
                        'date_issued' => $now,
                        'sex' => $sexes[array_rand($sexes)],
                        'contact_number' => $phoneNumber,
                        'philsys_number' => str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT),
                        'rrn_number' => str_pad((string) random_int(0, 999999999999999), 15, '0', STR_PAD_LEFT)
                            . str_pad((string) random_int(0, 99999999999999), 14, '0', STR_PAD_LEFT),
                        'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                        'civil_status' => $civilStatuses[array_rand($civilStatuses)],
                        'osca_id' => $controlNumber,
                        'qr_code' => $controlNumber,
                        'created_by' => $createdBy,
                        'status' => 'active',
                        'emergency_contact_name' => $firstName . ' ' . $lastName . ' Jr.',
                        'emergency_contact_number' => '09' . random_int(10, 99) . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
                        'emergency_contact_relationship' => $relationships[array_rand($relationships)],
                        'remarks' => 'Synthetic seed data for testing',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $createdCount++;
                }
            }

            foreach (array_chunk($records, 500) as $chunk) {
                DB::table('senior_citizen_records')->insert($chunk);
            }
        });

        $this->command->info(sprintf(
            'Added %d synthetic senior records across %d barangays, topping each up to at least %d active records.',
            $createdCount,
            count($barangays),
            self::TARGET_PER_BARANGAY
        ));
    }
}
