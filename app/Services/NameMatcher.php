<?php

namespace App\Services;

use App\Models\Client;

class NameMatcher
{
    /**
     * Normalize a name for comparison
     * Handles case, spacing, punctuation, and special characters
     */
    public static function normalizeName(string $name): string
    {
        $name = trim($name);
        $name = mb_strtolower($name, 'UTF-8');
        $name = str_replace(['.', ',', '"', "'", "\xE2\x80\x99"], '', $name);
        $name = str_replace('-', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    /**
     * Parse a full name into components
     */
    public static function parseFullName(string $fullName): array
    {
        $normalized = self::normalizeName($fullName);
        $parts = array_values(array_filter(explode(' ', $normalized)));

        $firstName = $parts[0] ?? '';
        $lastName = $parts ? end($parts) : '';
        $middleName = count($parts) > 2
            ? implode(' ', array_slice($parts, 1, -1))
            : '';

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'normalized' => $normalized,
            'parts' => $parts,
        ];
    }

    /**
     * Find candidate clients matching a given name
     * Returns array with 'exact' and 'partial' matches
     */
    public static function findCandidateClients(array $parsed): array
    {
        $firstName = $parsed['first_name'];
        $lastName = $parsed['last_name'];

        $results = ['exact' => collect(), 'partial' => collect()];

        if ($firstName === '' || $lastName === '') {
            return $results;
        }

        // Level 1 — Strong match: normalized first + last name
        $results['exact'] = Client::whereRaw('LOWER(first_name) = ?', [$firstName])
            ->whereRaw('LOWER(last_name) = ?', [$lastName])
            ->get();

        if ($results['exact']->isNotEmpty()) {
            return $results;
        }

        // Level 2 — Partial: same last name + overlapping name components
        if (mb_strlen($lastName) >= 3) {
            $byLastName = Client::whereRaw('LOWER(last_name) = ?', [$lastName])->get();

            $results['partial'] = $byLastName->filter(function ($client) use ($parsed) {
                $clientNorm = self::normalizeName(
                    trim(sprintf('%s %s %s', $client->first_name, $client->middle_name, $client->last_name))
                );
                $clientParts = array_values(array_filter(explode(' ', $clientNorm)));

                return self::countEffectiveOverlap($parsed['parts'], $clientParts) >= 2;
            });
        }

        return $results;
    }

    /**
     * Count effective overlap between name parts
     * Includes concatenated parts (e.g., "geraldlouis" = "gerald" + "louis")
     */
    private static function countEffectiveOverlap(array $inputParts, array $clientParts): int
    {
        $overlap = 0;
        $usedClient = [];

        foreach ($inputParts as $ip) {
            // 1. Direct match
            $matched = false;
            foreach ($clientParts as $j => $cp) {
                if ($ip === $cp && ! in_array($j, $usedClient, true)) {
                    $overlap++;
                    $usedClient[] = $j;
                    $matched = true;
                    break;
                }
            }
            if ($matched) continue;

            // 2. Concatenated consecutive client parts
            $n = count($clientParts);
            for ($start = 0; $start < $n; $start++) {
                if (in_array($start, $usedClient, true)) continue;
                $concat = '';
                $usedInRun = [];
                for ($j = $start; $j < $n; $j++) {
                    $concat .= $clientParts[$j];
                    $usedInRun[] = $j;
                    if ($concat === $ip) {
                        $overlap += count($usedInRun);
                        $usedClient = array_merge($usedClient, $usedInRun);
                        $matched = true;
                        break 2;
                    }
                    if (strlen($concat) > strlen($ip)) break;
                }
            }
        }

        return $overlap;
    }

    /**
     * Check if a client name matches an existing client
     * Returns the matching client or null
     */
    public static function findMatchingClient(string $fullName): ?Client
    {
        $parsed = self::parseFullName($fullName);
        $candidates = self::findCandidateClients($parsed);

        return $candidates['exact']->first() ?? $candidates['partial']->first();
    }
}
