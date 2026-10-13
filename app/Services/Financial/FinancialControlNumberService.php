<?php

namespace App\Services\Financial;

use App\Models\Financial\OnlineFinancialIntake;
use App\Models\SocialCase\BeneficiaryIntake;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialControlNumberService
{
    public const PREFIX = 'MSWDO';
    public const REGEX_PATTERN = '/^MSWDO-(\d{4})-(0[1-9]|1[0-2])-(\d{4})$/';

    /**
     * Atomically generate the next unique Control Number for a given date or the current month.
     * Format: MSWDO-YYYY-MM-0000 (e.g. MSWDO-2026-07-0001)
     * Resets sequence to 0001 at the start of each new month.
     *
     * @param Carbon|string|null $date
     * @return string
     */
    public static function generate($date = null): string
    {
        $targetDate = self::resolveDate($date);
        $year = $targetDate->format('Y');
        $month = $targetDate->format('m');
        $prefix = self::PREFIX . "-{$year}-{$month}-";

        return DB::transaction(function () use ($prefix, $year, $month) {
            // Find existing numbers matching this month prefix with lock to avoid race conditions
            $intakeNumbers = BeneficiaryIntake::where('control_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('control_number');

            $onlineNumbers = OnlineFinancialIntake::where('control_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('control_number');

            $allNumbers = $intakeNumbers->concat($onlineNumbers);

            $maxSeq = 0;
            foreach ($allNumbers as $cn) {
                if (empty($cn)) {
                    continue;
                }
                $suffix = substr($cn, strlen($prefix));
                if (is_numeric($suffix)) {
                    $seq = (int) $suffix;
                    if ($seq > $maxSeq) {
                        $maxSeq = $seq;
                    }
                }
            }

            $nextSeq = $maxSeq + 1;
            $candidate = self::format($year, $month, $nextSeq);

            // Double check existence in both tables to guarantee zero duplicate collisions
            while (
                BeneficiaryIntake::where('control_number', $candidate)->exists() ||
                OnlineFinancialIntake::where('control_number', $candidate)->exists()
            ) {
                $nextSeq++;
                $candidate = self::format($year, $month, $nextSeq);
            }

            return $candidate;
        });
    }

    /**
     * Preview the next Control Number for form display without acquiring database locks.
     *
     * @param Carbon|string|null $date
     * @return string
     */
    public static function previewNext($date = null): string
    {
        $targetDate = self::resolveDate($date);
        $year = $targetDate->format('Y');
        $month = $targetDate->format('m');
        $prefix = self::PREFIX . "-{$year}-{$month}-";

        $intakeNumbers = BeneficiaryIntake::where('control_number', 'like', "{$prefix}%")
            ->pluck('control_number');

        $onlineNumbers = OnlineFinancialIntake::where('control_number', 'like', "{$prefix}%")
            ->pluck('control_number');

        $allNumbers = $intakeNumbers->concat($onlineNumbers);

        $maxSeq = 0;
        foreach ($allNumbers as $cn) {
            if (empty($cn)) {
                continue;
            }
            $suffix = substr($cn, strlen($prefix));
            if (is_numeric($suffix)) {
                $seq = (int) $suffix;
                if ($seq > $maxSeq) {
                    $maxSeq = $seq;
                }
            }
        }

        $nextSeq = $maxSeq + 1;
        $candidate = self::format($year, $month, $nextSeq);

        while (
            BeneficiaryIntake::where('control_number', $candidate)->exists() ||
            OnlineFinancialIntake::where('control_number', $candidate)->exists()
        ) {
            $nextSeq++;
            $candidate = self::format($year, $month, $nextSeq);
        }

        return $candidate;
    }

    /**
     * Check if a given string matches the required MSWDO-YYYY-MM-0000 format.
     *
     * @param string|null $controlNumber
     * @return bool
     */
    public static function isValidFormat(?string $controlNumber): bool
    {
        if (empty($controlNumber)) {
            return false;
        }

        return (bool) preg_match(self::REGEX_PATTERN, trim($controlNumber));
    }

    /**
     * Parse components of a Control Number if valid.
     *
     * @param string|null $controlNumber
     * @return array{year: string, month: string, sequence: int}|null
     */
    public static function parse(?string $controlNumber): ?array
    {
        if (!self::isValidFormat($controlNumber)) {
            return null;
        }

        if (preg_match(self::REGEX_PATTERN, trim($controlNumber), $matches)) {
            return [
                'year' => $matches[1],
                'month' => $matches[2],
                'sequence' => (int) $matches[3],
            ];
        }

        return null;
    }

    /**
     * Format year, month, and sequence into standard format.
     *
     * @param int|string $year
     * @param int|string $month
     * @param int|string $sequence
     * @return string
     */
    public static function format($year, $month, $sequence): string
    {
        return sprintf(
            '%s-%04d-%02d-%04d',
            self::PREFIX,
            (int) $year,
            (int) $month,
            (int) $sequence
        );
    }

    /**
     * Normalize input into a Carbon instance.
     *
     * @param Carbon|string|null $date
     * @return Carbon
     */
    protected static function resolveDate($date = null): Carbon
    {
        if ($date instanceof Carbon) {
            return $date->copy();
        }

        if (is_string($date) && !empty($date)) {
            try {
                return Carbon::parse($date);
            } catch (\Throwable $e) {
                // Fallback to today on invalid parse
            }
        }

        return Carbon::now();
    }
}
