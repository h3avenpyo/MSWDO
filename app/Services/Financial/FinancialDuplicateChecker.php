<?php

namespace App\Services\Financial;

use App\Models\SocialCase\BeneficiaryIntake;
use Carbon\Carbon;

class FinancialDuplicateChecker
{
    /**
     * Check if the Beneficiary violates the 6-month financial assistance validity policy.
     *
     * Business Rules:
     * - The 6-month eligibility rule strictly applies to BENEPISYARYO (Beneficiaries), as they are the assistance recipients.
     * - A previous Beneficiary cannot receive financial assistance again within 6 months.
     * - A person who previously acted ONLY as a Representative CAN apply as a Beneficiary within 6 months.
     * - A Representative cannot be the exact same person as the Beneficiary on the same application.
     *
     * @param array $data Identifying data for Beneficiary and optional Representative
     * @param int|null $excludeId ID to exclude (used during intake updates)
     * @return array Standardized result array with duplicate status and details
     */
    public function checkDuplicate(array $data, ?int $excludeId = null): array
    {
        $currentDateStr = $data['date_processed'] ?? null;
        $currentDate = !empty($currentDateStr) ? Carbon::parse($currentDateStr) : Carbon::today();
        $cutoffDate = $currentDate->copy()->subMonths(6)->startOfDay();

        $beneficiaryFirstName = trim($data['beneficiary_first_name'] ?? '');
        $beneficiaryLastName = trim($data['beneficiary_last_name'] ?? '');
        $beneficiaryBirthday = !empty($data['beneficiary_birthday']) ? Carbon::parse($data['beneficiary_birthday'])->format('Y-m-d') : null;

        $hasRepresentative = !empty($data['has_representative']) && ($data['has_representative'] === true || $data['has_representative'] === '1' || $data['has_representative'] === 1);
        $repFirstName = $hasRepresentative ? trim($data['rep_first_name'] ?? '') : '';
        $repLastName = $hasRepresentative ? trim($data['rep_last_name'] ?? '') : '';
        $repBirthday = ($hasRepresentative && !empty($data['rep_birthday'])) ? Carbon::parse($data['rep_birthday'])->format('Y-m-d') : null;

        if (empty($beneficiaryFirstName) && empty($beneficiaryLastName)) {
            return [
                'is_duplicate' => false,
                'matches' => [],
                'warning_message' => null,
            ];
        }

        // 1. Check if Representative is identical to Beneficiary on the same form
        if ($hasRepresentative && !empty($repFirstName) && !empty($repLastName)) {
            if ($this->isMatch($beneficiaryFirstName, $beneficiaryLastName, $beneficiaryBirthday, $repFirstName, $repLastName, $repBirthday)) {
                return [
                    'is_duplicate' => true,
                    'matches' => [],
                    'warning_message' => 'The Representative cannot be the exact same person as the Beneficiary. Uncheck "Has Representative" if the Beneficiary is filing directly.',
                ];
            }
        }

        // 2. Fetch candidate intake records where date_processed is within the last 6 months
        $query = BeneficiaryIntake::query()
            ->where(function ($q) use ($cutoffDate) {
                $q->whereDate('date_processed', '>=', $cutoffDate)
                  ->orWhere(function ($q2) use ($cutoffDate) {
                      $q2->whereNull('date_processed')
                         ->whereDate('created_at', '>=', $cutoffDate);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $recentIntakes = $query->latest('date_processed')->get();

        $matches = [];
        $matchedRecordIds = [];

        foreach ($recentIntakes as $intake) {
            if (in_array($intake->id, $matchedRecordIds, true)) {
                continue;
            }

            // Check if incoming Beneficiary was previously a BENEFICIARY within the last 6 months
            $matchedAsBeneficiary = $this->isMatch(
                $beneficiaryFirstName,
                $beneficiaryLastName,
                $beneficiaryBirthday,
                $intake->beneficiary_first_name,
                $intake->beneficiary_last_name,
                $intake->beneficiary_birthday?->format('Y-m-d')
            );

            if ($matchedAsBeneficiary) {
                $matchedRecordIds[] = $intake->id;
                $prevDate = $intake->date_processed ?? $intake->created_at;
                $eligibleAgain = $prevDate ? Carbon::parse($prevDate)->addMonths(6) : null;
                $daysAgo = $prevDate ? Carbon::parse($prevDate)->diffInDays($currentDate) : 0;

                $assistanceType = $intake->recommended_assistance_type 
                    ?: ($intake->service_provided ?: 'Financial Assistance Intake');

                $matches[] = [
                    'intake_id' => $intake->id,
                    'control_number' => $intake->control_number,
                    'matched_role' => 'Previous Beneficiary (6-Month Policy Restriction)',
                    'date_processed' => $prevDate ? Carbon::parse($prevDate)->format('M d, Y') : 'N/A',
                    'raw_date' => $prevDate ? Carbon::parse($prevDate)->format('Y-m-d') : null,
                    'days_ago' => $daysAgo,
                    'eligible_again_date' => $eligibleAgain ? $eligibleAgain->format('M d, Y') : 'N/A',
                    'beneficiary_name' => $intake->beneficiary_full_name,
                    'representative_name' => $intake->has_representative ? ($intake->representative_full_name ?: 'N/A') : 'None',
                    'assistance_type' => $assistanceType,
                    'purpose' => $intake->display_assistance_purpose,
                ];
            }
        }

        $isDuplicate = count($matches) > 0;
        $warningMessage = null;

        if ($isDuplicate) {
            $firstMatch = $matches[0];
            $warningMessage = sprintf(
                'Beneficiary %s previously received financial assistance on %s (Control No: %s). Under MSWDO policy, a Beneficiary can only receive financial assistance once every 6 months. Next eligible date: %s.',
                $firstMatch['beneficiary_name'],
                $firstMatch['date_processed'],
                $firstMatch['control_number'],
                $firstMatch['eligible_again_date']
            );
        }

        return [
            'is_duplicate' => $isDuplicate,
            'matches' => $matches,
            'warning_message' => $warningMessage,
        ];
    }

    /**
     * Check if two sets of names/birthdays match.
     */
    private function isMatch(
        string $firstName1,
        string $lastName1,
        ?string $birthday1,
        ?string $firstName2,
        ?string $lastName2,
        ?string $birthday2
    ): bool {
        if (empty($firstName2) || empty($lastName2)) {
            return false;
        }

        $fn1 = strtolower(preg_replace('/[^a-z0-9]/', '', $firstName1));
        $ln1 = strtolower(preg_replace('/[^a-z0-9]/', '', $lastName1));
        $fn2 = strtolower(preg_replace('/[^a-z0-9]/', '', $firstName2));
        $ln2 = strtolower(preg_replace('/[^a-z0-9]/', '', $lastName2));

        // Name check: First name and Last name must match
        $namesMatch = ($fn1 === $fn2 && $ln1 === $ln2);

        if (!$namesMatch) {
            return false;
        }

        // If both records have birthdays specified, ensure birthday also matches
        if (!empty($birthday1) && !empty($birthday2)) {
            return $birthday1 === $birthday2;
        }

        // If one or both birthdays are omitted, match based on first + last name match
        return true;
    }
}
