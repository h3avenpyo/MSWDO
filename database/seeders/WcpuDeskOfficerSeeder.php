<?php

namespace Database\Seeders;

use App\Models\Wcpu\Barangay;
use App\Models\Wcpu\CaseModel;
use App\Models\Wcpu\CaseReferral;
use App\Models\Wcpu\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Sample WCPU login accounts for the desk officers plus a small set of cases
 * and referrals so the SWD (VAWC) and BCPC dashboards have data to display.
 *
 * All dummy accounts share the password: Password1!
 *
 * | Email                    | Position     | Department |
 * |--------------------------|--------------|------------|
 * | swd.desk@wcpu.test       | Desk Officer | VAWC       |
 * | bcpc.desk@wcpu.test      | Desk Officer | BCPC       |
 */
class WcpuDeskOfficerSeeder extends Seeder
{
    public const PASSWORD = 'Password1!';

    public function run(): void
    {
        $swdOfficer = $this->deskOfficer(
            email: 'swd.desk@wcpu.test',
            username: 'swd.desk',
            firstName: 'Maria',
            lastName: 'Bacor',
            department: 'VAWC',
            barangay: 'Biga I'
        );

        $bcpcOfficer = $this->deskOfficer(
            email: 'bcpc.desk@wcpu.test',
            username: 'bcpc.desk',
            firstName: 'Kirsten',
            lastName: 'Miral',
            department: 'BCPC',
            barangay: 'Adlas'
        );

        $this->seedCase(
            officer: $swdOfficer,
            department: 'VAWC',
            caseNumberPrefix: 'SWD',
            barangayName: 'Biga I',
            abuseType: 'RA 9262',
            victim: [
                'name' => 'Cristina Reyes', 'age' => 34, 'gender' => 'Female',
                'contact' => '09171234567', 'address' => 'Purok 2, Biga I, Silang, Cavite',
                'guardian' => 'Rosa Reyes', 'guardian_contact' => '09179998888',
                'guardian_address' => 'Purok 2, Biga I, Silang, Cavite', 'relationship_to_victim' => 'Mother',
            ],
            abuser: [
                'name' => 'Ricardo Reyes', 'age' => 39, 'address' => 'Purok 2, Biga I, Silang, Cavite',
                'relationship' => 'Husband', 'guardian_name' => null,
            ],
            incident: [
                'date' => '2026-06-02', 'time' => '20:30', 'reported' => '2026-06-03',
                'location' => 'Residence, Purok 2, Biga I',
                'details' => 'Victim reported being physically abused by her husband after a domestic argument. She sustained bruises on her arms and back.',
                'witness_name' => 'Lourdes Reyes', 'witness_contact' => '09175551111',
            ],
            priority: 'Urgent',
            status: 'Case Filed',
        );

        $vawcClosedCase = $this->seedCase(
            officer: $swdOfficer,
            department: 'VAWC',
            caseNumberPrefix: 'SWD',
            barangayName: 'Biluso',
            abuseType: 'RA 9262',
            victim: [
                'name' => 'Angel Delos Santos', 'age' => 27, 'gender' => 'Female',
                'contact' => '09391234567', 'address' => 'Purok 4, Biluso, Silang, Cavite',
                'guardian' => null, 'guardian_contact' => null,
                'guardian_address' => null, 'relationship_to_victim' => null,
            ],
            abuser: [
                'name' => 'Mark Villanueva', 'age' => 30, 'address' => 'Purok 4, Biluso, Silang, Cavite',
                'relationship' => 'Live-in Partner', 'guardian_name' => null,
            ],
            incident: [
                'date' => '2026-04-18', 'time' => '18:00', 'reported' => '2026-04-19',
                'location' => 'Residence, Purok 4, Biluso',
                'details' => 'Victim reported psychological and economic abuse by her live-in partner, including threats and withholding of financial support.',
                'witness_name' => null, 'witness_contact' => null,
            ],
            priority: 'Non-urgent',
            status: 'Case Closed',
        );

        if ($vawcClosedCase) {
            $vawcClosedCase->update([
                'resolution_date' => '2026-05-30',
                'closed_at' => Carbon::parse('2026-05-30 15:00:00'),
            ]);
        }

        $bcpcCase = $this->seedCase(
            officer: $bcpcOfficer,
            department: 'BCPC',
            caseNumberPrefix: 'BCPC',
            barangayName: 'Adlas',
            abuseType: 'RA 7610',
            victim: [
                'name' => 'Nathan Aquino', 'age' => 12, 'gender' => 'Male',
                'contact' => null, 'address' => 'Purok 1, Adlas, Silang, Cavite',
                'guardian' => 'Josefina Aquino', 'guardian_contact' => '09182223333',
                'guardian_address' => 'Purok 1, Adlas, Silang, Cavite', 'relationship_to_victim' => 'Mother',
            ],
            abuser: [
                'name' => 'Pedro Aquino', 'age' => 45, 'address' => 'Purok 1, Adlas, Silang, Cavite',
                'relationship' => 'Father', 'guardian_name' => null,
            ],
            incident: [
                'date' => '2026-05-21', 'time' => '16:45', 'reported' => '2026-05-22',
                'location' => 'Residence, Purok 1, Adlas',
                'details' => 'Minor victim reported being physically harmed and neglected by his father. Visible marks were documented by the barangay health worker.',
                'witness_name' => 'Barangay Tanod Cruz', 'witness_contact' => '09183334444',
            ],
            priority: 'Emergency',
            status: 'Case Referred',
        );

        $this->seedReferral(
            $bcpcCase,
            $bcpcOfficer,
            'Nathan Aquino',
            12,
            'Male',
            'Purok 1, Adlas, Silang, Cavite'
        );

        $this->command->info('WCPU Desk Officer seed complete. 2 desk officer accounts created (password: ' . self::PASSWORD . ').');
        $this->command->info('  SWD (VAWC) desk officer: swd.desk@wcpu.test');
        $this->command->info('  BCPC desk officer:       bcpc.desk@wcpu.test');
        $this->command->info('Sample cases seeded: 2 VAWC (SWD), 1 BCPC with 1 referral.');
    }

    private function deskOfficer(string $email, string $username, string $firstName, string $lastName, string $department, string $barangay): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'password' => Hash::make(self::PASSWORD),
                'phone_number' => '0917' . str_pad((string) abs(crc32($email)) % 10000000, 7, '0', STR_PAD_LEFT),
                'position' => 'Desk Officer',
                'department' => $department,
                'barangay' => $barangay,
                'is_active' => true,
                'bcpc_is_active' => $department === 'BCPC',
                'vawc_is_active' => $department === 'VAWC',
                'failed_login_attempts' => 0,
                'account_locked' => false,
            ]
        );
    }

    private function seedCase(
        User $officer,
        string $department,
        string $caseNumberPrefix,
        string $barangayName,
        string $abuseType,
        array $victim,
        array $abuser,
        array $incident,
        string $priority,
        string $status,
    ): CaseModel {
        $barangay = Barangay::where('name', $barangayName)->firstOrFail();
        $incidentDate = Carbon::parse($incident['date']);
        $caseNumber = sprintf(
            '%s-%s-%s-001',
            $caseNumberPrefix,
            $barangay->code,
            $incidentDate->format('mdy')
        );

        return CaseModel::updateOrCreate(
            ['case_number' => $caseNumber],
            [
                'type' => $department,
                'case_designation' => $barangay->id,
                'incident_date' => $incidentDate->toDateString(),
                'incident_time' => $incident['time'],
                'date_reported' => Carbon::parse($incident['reported'])->toDateString(),
                'abuse_type' => $abuseType,
                'perpetrator_name' => $abuser['name'],
                'relationship_to_victim' => $victim['relationship_to_victim'],
                'case_details' => $incident['details'],
                'status' => $status,
                'priority' => $priority,
                'victim_name' => $victim['name'],
                'victim_age' => $victim['age'],
                'victim_gender' => $victim['gender'],
                'victim_contact' => $victim['contact'],
                'victim_address' => $victim['address'],
                'guardian' => $victim['guardian'],
                'guardian_contact' => $victim['guardian_contact'],
                'guardian_address' => $victim['guardian_address'],
                'abuser_name' => $abuser['name'],
                'abuser_age' => $abuser['age'],
                'abuser_address' => $abuser['address'],
                'abuse_relationship' => $abuser['relationship'],
                'guardian_abuser_name' => $abuser['guardian_name'],
                'incident_location' => $incident['location'],
                'incident_details' => $incident['details'],
                'witness_name' => $incident['witness_name'],
                'witness_contact' => $incident['witness_contact'],
                'assigned_to' => $officer->id,
                'created_by' => $officer->id,
                'updated_by' => $officer->id,
            ]
        );
    }

    private function seedReferral(
        CaseModel $case,
        User $officer,
        string $clientName,
        int $clientAge,
        string $clientSex,
        string $clientAddress
    ): void {
        $dateOfReferral = Carbon::parse($case->date_reported)->addDays(2);

        CaseReferral::updateOrCreate(
            ['referral_number' => 'REF-' . $dateOfReferral->format('Ymd') . '-001'],
            [
                'case_id' => $case->id,
                'date_of_referral' => $dateOfReferral->toDateString(),
                'referred_by_name' => $officer->full_name,
                'referred_by_designation' => 'BCPC Desk Officer',
                'referred_by_address' => 'WCPU Silang, Municipal Hall, Silang, Cavite',
                'referred_by_contact' => $officer->phone_number,
                'referred_by_email' => $officer->email,
                'recipient_name' => 'Municipal Social Welfare and Development Office',
                'recipient_address' => 'MSWDO Silang, Municipal Hall, Silang, Cavite',
                'recipient_contact' => '046-884-0000',
                'recipient_email' => 'mswdo@silang.gov.ph',
                'client_name' => $clientName,
                'client_age' => $clientAge,
                'client_sex' => $clientSex,
                'client_address' => $clientAddress,
                'family_guardian_name' => $case->guardian,
                'family_guardian_contact' => $case->guardian_contact,
                'family_guardian_address' => $case->guardian_address,
                'reason_for_referral' => 'For psychosocial intervention and case management of a minor victim of physical abuse and neglect.',
                'specific_services_requested' => 'Psychological evaluation and counseling; temporary shelter assistance if necessary; follow-up home visitation.',
                'additional_notes' => 'Coordinated with the barangay for monitoring while the case is ongoing.',
                'status' => 'Pending',
                'referred_by_user_id' => $officer->id,
                'created_by_department_admin' => false,
            ]
        );
    }
}
