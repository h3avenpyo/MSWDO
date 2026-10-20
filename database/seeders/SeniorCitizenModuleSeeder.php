<?php

namespace Database\Seeders;

use App\Enums\PayoutStatus;
use App\Enums\SeniorStatus;
use App\Enums\UserRole;
use App\Models\Senior\BirthdayPayout;
use App\Models\Senior\InBetweenBenefitConfiguration;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Curated, idempotent sample data for the Senior Citizen module.
 *
 * Registers the Senior Citizen officer account, a small set of senior records
 * (including milestone ages that qualify for in-between benefits), the birthday
 * payouts and the default in-between benefit configuration.
 */
class SeniorCitizenModuleSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::updateOrCreate(
            ['email' => 'senior@mswdo.test'],
            [
                'name' => 'Senior Citizen Officer',
                'password' => Hash::make('password'),
                'role' => UserRole::SeniorCitizenOfficer,
                'status' => 'active',
            ]
        );

        $year = 2026;

        $seniors = [
            [
                'control_number' => 'SC-ACA-2026-000001',
                'senior_id_number' => 'SC-ACA-2026-000001',
                'osca_id' => 'SC-ACA-2026-000001',
                'first_name' => 'Maria', 'middle_name' => 'Santos', 'last_name' => 'Reyes',
                'barangay' => 'Acacia', 'birth_date' => '1936-03-14', 'sex' => 'Female',
                'civil_status' => 'Widowed', 'blood_type' => 'O+',
                'emergency_contact_name' => 'Juan Reyes', 'emergency_contact_relationship' => 'Child',
            ],
            [
                'control_number' => 'SC-ADL-2026-000001',
                'senior_id_number' => 'SC-ADL-2026-000001',
                'osca_id' => 'SC-ADL-2026-000001',
                'first_name' => 'Jose', 'middle_name' => 'Cruz', 'last_name' => 'Bautista',
                'barangay' => 'Adlas', 'birth_date' => '1931-07-22', 'sex' => 'Male',
                'civil_status' => 'Married', 'blood_type' => 'A+',
                'emergency_contact_name' => 'Rosa Bautista', 'emergency_contact_relationship' => 'Spouse',
            ],
            [
                'control_number' => 'SC-BG1-2026-000001',
                'senior_id_number' => 'SC-BG1-2026-000001',
                'osca_id' => 'SC-BG1-2026-000001',
                'first_name' => 'Carmen', 'middle_name' => 'Garcia', 'last_name' => 'Fernandez',
                'barangay' => 'Biga I', 'birth_date' => '1956-01-05', 'sex' => 'Female',
                'civil_status' => 'Single', 'blood_type' => 'B+',
                'emergency_contact_name' => 'Ana Fernandez', 'emergency_contact_relationship' => 'Sibling',
            ],
            [
                'control_number' => 'SC-BUL-2026-000001',
                'senior_id_number' => 'SC-BUL-2026-000001',
                'osca_id' => 'SC-BUL-2026-000001',
                'first_name' => 'Antonio', 'middle_name' => 'Ramos', 'last_name' => 'Flores',
                'barangay' => 'Bulihan', 'birth_date' => '1964-09-19', 'sex' => 'Male',
                'civil_status' => 'Married', 'blood_type' => 'AB+',
                'emergency_contact_name' => 'Luz Flores', 'emergency_contact_relationship' => 'Spouse',
            ],
            [
                'control_number' => 'SC-LUM-2026-000001',
                'senior_id_number' => 'SC-LUM-2026-000001',
                'osca_id' => 'SC-LUM-2026-000001',
                'first_name' => 'Rosa', 'middle_name' => 'Mendoza', 'last_name' => 'Castillo',
                'barangay' => 'Lumil', 'birth_date' => '1941-12-01', 'sex' => 'Female',
                'civil_status' => 'Widowed', 'blood_type' => 'O-',
                'emergency_contact_name' => 'Pedro Castillo', 'emergency_contact_relationship' => 'Child',
            ],
            [
                'control_number' => 'SC-KAO-2026-000001',
                'senior_id_number' => 'SC-KAO-2026-000001',
                'osca_id' => 'SC-KAO-2026-000001',
                'first_name' => 'Pedro', 'middle_name' => 'Torres', 'last_name' => 'Rivera',
                'barangay' => 'Kaong', 'birth_date' => '1926-05-30', 'sex' => 'Male',
                'civil_status' => 'Married', 'blood_type' => 'A-',
                'emergency_contact_name' => 'Sofia Rivera', 'emergency_contact_relationship' => 'Spouse',
            ],
            [
                'control_number' => 'SC-BIL-2026-000001',
                'senior_id_number' => 'SC-BIL-2026-000001',
                'osca_id' => 'SC-BIL-2026-000001',
                'first_name' => 'Teresa', 'middle_name' => 'Villanueva', 'last_name' => 'Morales',
                'barangay' => 'Biluso', 'birth_date' => '1958-11-11', 'sex' => 'Female',
                'civil_status' => 'Separated', 'blood_type' => 'B-',
                'emergency_contact_name' => 'Miguel Morales', 'emergency_contact_relationship' => 'Child',
            ],
            [
                'control_number' => 'SC-TAR-2026-000001',
                'senior_id_number' => 'SC-TAR-2026-000001',
                'osca_id' => 'SC-TAR-2026-000001',
                'first_name' => 'Francisco', 'middle_name' => 'Navarro', 'last_name' => 'Santiago',
                'barangay' => 'Tartaria', 'birth_date' => '1949-02-27', 'sex' => 'Male',
                'civil_status' => 'Married', 'blood_type' => 'O+',
                'emergency_contact_name' => 'Elena Santiago', 'emergency_contact_relationship' => 'Spouse',
            ],
        ];

        $created = 0;

        foreach ($seniors as $senior) {
            $birthDate = Carbon::parse($senior['birth_date']);
            $record = SeniorCitizenRecord::updateOrCreate(
                ['control_number' => $senior['control_number']],
                array_merge($senior, [
                    'record_number' => $senior['control_number'],
                    'year_applied' => $year,
                    'address' => 'House ' . (($created % 80) + 1) . ', Purok ' . (($created % 6) + 1) . ', ' . $senior['barangay'] . ', Silang, Cavite',
                    'contact_number' => '09' . str_pad((string) (170000000 + $created * 137), 9, '0', STR_PAD_LEFT),
                    'philsys_number' => 'PHL-' . str_pad((string) (100000000 + $created * 313), 9, '0', STR_PAD_LEFT),
                    'rrn_number' => 'RRN-' . str_pad((string) (200000000 + $created * 719), 9, '0', STR_PAD_LEFT),
                    'emergency_contact_number' => '09' . str_pad((string) (180000000 + $created * 231), 9, '0', STR_PAD_LEFT),
                    'date_issued' => $birthDate->copy()->startOfYear()->toDateString(),
                    'remarks' => 'Sample senior citizen record seeded for testing.',
                    'created_by' => $officer->id,
                    'status' => SeniorStatus::Active,
                ])
            );

            // Birthday payout for the current year — released for most, pending for a few.
            $isReleased = $created % 3 !== 2;
            BirthdayPayout::updateOrCreate(
                ['senior_id' => $record->id, 'payout_year' => $year],
                [
                    'amount' => 500.00,
                    'status' => $isReleased ? PayoutStatus::Released : PayoutStatus::Pending,
                    'released_by' => $isReleased ? $officer->id : null,
                    'released_date' => $isReleased ? Carbon::parse($year . '-06-15')->toDateTimeString() : null,
                    'remarks' => $isReleased ? 'Birthday cash gift released.' : 'For release during the barangay payout schedule.',
                ]
            );

            $created++;
        }

        InBetweenBenefitConfiguration::updateOrCreate(
            ['benefit_name' => 'In-Between Birthday Cash Gift'],
            [
                'description' => 'Municipality of Silang In-Between Birthday Cash Gift for Senior Citizens',
                'benefit_amount' => 1000.00,
                'milestone_ages' => [80, 85, 90, 95, 100],
                'eligible_intervals' => ['81-84', '86-89', '91-94', '96-99'],
                'is_active' => true,
                'effective_date' => Carbon::parse($year . '-01-01')->toDateString(),
            ]
        );

        $this->command->info("Senior Citizen module seeded: {$created} senior records, birthday payouts and the in-between benefit configuration.");
    }
}
