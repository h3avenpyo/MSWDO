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
     * Parse a full name into components.
     * Supports both "First [Middle] Last" and "Last, First [Middle]" formats.
     */
    public static function parseFullName(string $fullName): array
    {
        $fullName = trim($fullName);

        if (str_contains($fullName, ',')) {
            $commaParts = explode(',', $fullName, 2);
            $lastName = self::normalizeName($commaParts[0]);
            $firstMiddleNormalized = self::normalizeName($commaParts[1]);
            $firstMiddleParts = array_values(array_filter(explode(' ', $firstMiddleNormalized)));

            $firstName = $firstMiddleParts[0] ?? '';
            $middleName = count($firstMiddleParts) > 1 ? implode(' ', array_slice($firstMiddleParts, 1)) : '';
            $normalized = trim($firstMiddleNormalized . ' ' . $lastName);
            $parts = array_values(array_filter(explode(' ', $normalized)));

            return [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $middleName,
                'normalized' => $normalized,
                'parts' => $parts,
            ];
        }

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
     * Extract given name parts (excluding the surname and single-character initials).
     */
    public static function extractGivenParts(array $parts, string $lastName): array
    {
        return array_values(array_filter($parts, function ($part) use ($lastName) {
            if ($part === $lastName) {
                return false;
            }
            if (mb_strlen($part) <= 1) {
                return false;
            }
            return true;
        }));
    }

    /**
     * Check if there is a real given-name match or overlap between two sets of given parts.
     */
    public static function hasGivenNameMatch(array $inputGiven, array $clientGiven): bool
    {
        foreach ($inputGiven as $ig) {
            foreach ($clientGiven as $cg) {
                if ($ig === $cg) {
                    return true;
                }
                if (strlen($ig) >= 4 && strlen($cg) >= 4) {
                    if (str_starts_with($ig, $cg) || str_starts_with($cg, $ig)) {
                        return true;
                    }
                }
                if (strlen($ig) >= 5 && strlen($cg) >= 5 && levenshtein($ig, $cg) <= 1) {
                    return true;
                }
            }
        }

        return false;
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

        $allByLastName = Client::whereRaw('LOWER(last_name) = ?', [$lastName])->get();
        if ($allByLastName->isEmpty()) {
            return $results;
        }

        $inputNormalized = $parsed['normalized'];
        $inputGivenParts = self::extractGivenParts($parsed['parts'], $lastName);

        // Level 1 — Exact match
        $results['exact'] = $allByLastName->filter(function ($client) use ($inputNormalized, $firstName, $lastName, $inputGivenParts) {
            $clientNormFull = self::normalizeName(
                trim(sprintf('%s %s %s', $client->first_name, $client->middle_name, $client->last_name))
            );
            if ($clientNormFull === $inputNormalized) {
                return true;
            }

            $clientNormFirst = self::normalizeName((string) $client->first_name);
            $clientNormLast = self::normalizeName((string) $client->last_name);
            if ($clientNormFirst === $firstName && $clientNormLast === $lastName) {
                return true;
            }

            $clientParts = array_values(array_filter(explode(' ', $clientNormFull)));
            $clientGivenParts = self::extractGivenParts($clientParts, $clientNormLast);
            if (!empty($inputGivenParts) && $clientGivenParts === $inputGivenParts) {
                return true;
            }

            return false;
        })->values();

        if ($results['exact']->isNotEmpty()) {
            return $results;
        }

        // Level 2 — Partial match: Same last name AND at least one matching/overlapping given name part
        // Single-character initials or common surnames NEVER produce a match on their own
        if (mb_strlen($lastName) >= 3 && !empty($inputGivenParts)) {
            $results['partial'] = $allByLastName->filter(function ($client) use ($inputGivenParts, $lastName) {
                $clientNormFull = self::normalizeName(
                    trim(sprintf('%s %s %s', $client->first_name, $client->middle_name, $client->last_name))
                );
                $clientParts = array_values(array_filter(explode(' ', $clientNormFull)));
                $clientGivenParts = self::extractGivenParts($clientParts, $lastName);

                if (empty($clientGivenParts)) {
                    return false;
                }

                return self::hasGivenNameMatch($inputGivenParts, $clientGivenParts);
            })->values();
        }

        return $results;
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
