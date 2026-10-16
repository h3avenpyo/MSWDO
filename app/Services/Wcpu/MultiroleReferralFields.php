<?php

namespace App\Services\Wcpu;

use App\Models\Wcpu\Barangay;
use App\Models\Wcpu\CaseModel;
use App\Models\Wcpu\User;

class MultiroleReferralFields
{
    public static function officeAddress(User $user): string
    {
        $isBcpc = $user->isBcpcAdmin() || ($user->defaultCaseDepartment() === 'BCPC' && !$user->isMultiCaseDepartment());
        $barangay = Barangay::where('id', $user->barangay)->orWhere('name', $user->barangay)->value('name');
        $place = $barangay ?: ($user->barangay_name ?? $user->barangay ?? 'Acacia');
        return ($isBcpc ? 'BCPC office, ' : 'SWD office, ') . $place;
    }

    public static function forCase(User $user, CaseModel $case): array
    {
        return [
            'referred_by_name' => trim($user->first_name.' '.$user->last_name),
            'referred_by_designation' => $user->position,
            'referred_by_address' => self::officeAddress($user),
            'referred_by_contact' => $user->contact_number ?? $user->phone_number,
            'referred_by_email' => $user->email,
            'guardian_name' => $case->guardian,
            'guardian_contact' => $case->guardian_contact,
            'guardian_address' => $case->guardian_address,
            'relationship_to_victim' => $case->relationship_to_victim,
        ];
    }

    public static function reason(CaseModel $case): string
    {
        if (strcasecmp(trim((string) $case->abuse_type), 'Others') === 0) {
            if (!empty($case->other_abuse_details)) {
                return trim((string) $case->other_abuse_details);
            }
            if (preg_match('/Specific Type of Abuse \(Others\):\s*(.*?)(?:\R\R|$)/s', (string) $case->incident_details, $matches)) {
                return trim($matches[1]);
            }
        }
        return (string) $case->abuse_type;
    }
}
