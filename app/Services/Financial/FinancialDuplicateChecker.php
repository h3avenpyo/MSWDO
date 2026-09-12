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
     * - Duplication detection must be based on valid, complete identifying criteria (First Name, Last Name, Birthday, Middle Name, Extension).
     * - Partial or unrelated information must not trigger the 6-month restriction.
     *
     * @param array $data Identifying data for Beneficiary and optional Representative
     * @param int|null $excludeId ID to exclude (used during intake updates)
     * @return array Standardized result array with duplicate status and details
     */
    public function checkDuplicate(array $data, ?int $excludeId = null): array
    {
        $currentDateStr = $this->parseDateSafely($data['date_processed'] ?? null);
        $currentDate = !empty($currentDateStr) ? Carbon::parse($currentDateStr) : Carbon::today();
        $cutoffDate = $currentDate->copy()->subMonths(6)->startOfDay();

        $beneficiaryFirstName = trim($data['beneficiary_first_name'] ?? '');
        $beneficiaryLastName = trim($data['beneficiary_last_name'] ?? '');
        $beneficiaryMiddleName = trim($data['beneficiary_middle_name'] ?? '');
        $beneficiaryExtName = trim($data['beneficiary_extension_name'] ?? '');
        $beneficiaryBirthday = $this->parseDateSafely($data['beneficiary_birthday'] ?? null);

        $hasRepresentative = !empty($data['has_representative']) && ($data['has_representative'] === true || $data['has_representative'] === '1' || $data['has_representative'] === 1);
        $repFirstName = $hasRepresentative ? trim($data['rep_first_name'] ?? '') : '';
        $repLastName = $hasRepresentative ? trim($data['rep_last_name'] ?? '') : '';
        $repMiddleName = $hasRepresentative ? trim($data['rep_middle_name'] ?? '') : '';
        $repExtName = $hasRepresentative ? trim($data['rep_extension_name'] ?? '') : '';
        $repBirthday = ($hasRepresentative && !empty($data['rep_birthday'])) ? $this->parseDateSafely($data['rep_birthday']) : null;

        // 1. Same-form Beneficiary vs Representative check
        if ($hasRepresentative && strlen($repFirstName) >= 2 && strlen($repLastName) >= 2 && strlen($beneficiaryFirstName) >= 2 && strlen($beneficiaryLastName) >= 2) {
            if ($this->isRepresentativeSamePerson(
                $beneficiaryFirstName, $beneficiaryMiddleName, $beneficiaryLastName, $beneficiaryExtName, $beneficiaryBirthday,
                $repFirstName, $repMiddleName, $repLastName, $repExtName, $repBirthday
            )) {
                return [
                    'is_duplicate' => true,
                    'matches' => [],
                    'warning_message' => 'The Representative cannot be the exact same person as the Beneficiary. Uncheck "Has Representative" if the Beneficiary is filing directly.',
                ];
            }
        }

        // 2. Identifying criteria: First Name and Last Name must each have at least 2 characters to evaluate duplicate records
        // Do not trigger the restriction on partial or incomplete names (e.g. typing only 1 character or last name only)
        if (strlen($beneficiaryFirstName) < 2 || strlen($beneficiaryLastName) < 2) {
            return [
                'is_duplicate' => false,
                'matches' => [],
                'warning_message' => null,
            ];
        }

        // 3. Query candidate intake records within the 6-month validity cutoff
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

            $intakeBirthday = $intake->beneficiary_birthday ? $intake->beneficiary_birthday->format('Y-m-d') : null;

            $matchedAsBeneficiary = $this->isBeneficiaryMatch(
                $beneficiaryFirstName,
                $beneficiaryMiddleName,
                $beneficiaryLastName,
                $beneficiaryExtName,
                $beneficiaryBirthday,
                $intake->beneficiary_first_name ?? '',
                $intake->beneficiary_middle_name ?? '',
                $intake->beneficiary_last_name ?? '',
                $intake->beneficiary_extension_name ?? '',
                $intakeBirthday
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
     * Compare incoming beneficiary profile against a previous beneficiary record.
     */
    private function isBeneficiaryMatch(
        string $firstName1,
        ?string $middleName1,
        string $lastName1,
        ?string $extName1,
        ?string $birthday1,
        string $firstName2,
        ?string $middleName2,
        string $lastName2,
        ?string $extName2,
        ?string $birthday2
    ): bool {
        if (empty($firstName1) || empty($lastName1) || empty($firstName2) || empty($lastName2)) {
            return false;
        }

        $fn1 = $this->normalizeString($firstName1);
        $ln1 = $this->normalizeString($lastName1);
        $fn2 = $this->normalizeString($firstName2);
        $ln2 = $this->normalizeString($lastName2);

        // 1. First Name and Last Name must match exactly
        if ($fn1 !== $fn2 || $ln1 !== $ln2) {
            return false;
        }

        // 2. Birthday conflict check: If BOTH records provide a valid birthday and they differ, they are conclusively different individuals
        if (!empty($birthday1) && !empty($birthday2) && $birthday1 !== $birthday2) {
            return false;
        }

        // 3. Extension name (Jr., Sr., III, etc.) check
        $ext1 = $this->normalizeString($extName1 ?? '');
        $ext2 = $this->normalizeString($extName2 ?? '');
        if (!empty($ext1) && !empty($ext2) && $ext1 !== $ext2) {
            return false;
        }

        // 4. Middle name comparison (if both records have middle names or initials)
        $mn1 = $this->normalizeString($middleName1 ?? '');
        $mn2 = $this->normalizeString($middleName2 ?? '');
        if (!empty($mn1) && !empty($mn2)) {
            if (strlen($mn1) === 1 || strlen($mn2) === 1) {
                // If one is middle initial, first letter must match
                if ($mn1[0] !== $mn2[0]) {
                    return false;
                }
            } else {
                // If both are full words, they must match
                if ($mn1 !== $mn2) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if the representative on the same form is identical to the beneficiary.
     */
    private function isRepresentativeSamePerson(
        string $benFirst,
        ?string $benMiddle,
        string $benLast,
        ?string $benExt,
        ?string $benBday,
        string $repFirst,
        ?string $repMiddle,
        string $repLast,
        ?string $repExt,
        ?string $repBday
    ): bool {
        $bfn = $this->normalizeString($benFirst);
        $bln = $this->normalizeString($benLast);
        $rfn = $this->normalizeString($repFirst);
        $rln = $this->normalizeString($repLast);

        if ($bfn !== $rfn || $bln !== $rln) {
            return false;
        }

        // Check extensions if both present
        $bext = $this->normalizeString($benExt ?? '');
        $rext = $this->normalizeString($repExt ?? '');
        if (!empty($bext) && !empty($rext) && $bext !== $rext) {
            return false;
        }

        // Check birthdays if both present
        if (!empty($benBday) && !empty($repBday)) {
            return $benBday === $repBday;
        }

        // Check middle names if both present
        $bmn = $this->normalizeString($benMiddle ?? '');
        $rmn = $this->normalizeString($repMiddle ?? '');
        if (!empty($bmn) && !empty($rmn)) {
            if (strlen($bmn) === 1 || strlen($rmn) === 1) {
                if ($bmn[0] !== $rmn[0]) {
                    return false;
                }
            } else if ($bmn !== $rmn) {
                return false;
            }
        }

        return true;
    }

    /**
     * Safely parse dates formatted as MM/DD/YYYY or YYYY-MM-DD without throwing exceptions on partial strings.
     */
    public function parseDateSafely(?string $dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }

        $trimmed = trim($dateStr);

        // Standard MM/DD/YYYY format (e.g. 06/06/2004)
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $trimmed, $matches)) {
            $month = (int) $matches[1];
            $day = (int) $matches[2];
            $year = (int) $matches[3];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
            return null;
        }

        // Standard YYYY-MM-DD format (e.g. 2004-06-06)
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $trimmed, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            $day = (int) $matches[3];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
            return null;
        }

        // Fallback for complete strings (>= 8 chars) wrapped in try-catch
        if (strlen($trimmed) >= 8) {
            try {
                return Carbon::parse($trimmed)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Normalize string for reliable comparison (lowercase, alphanumeric only).
     */
    private function normalizeString(?string $str): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($str ?? ''));
    }
}

