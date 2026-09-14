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
     * Normalize a name into cleaned single-word tokens (already case-insensitive,
     * punctuation-free, and spacing-collapsed via normalizeName).
     */
    public static function nameTokens(string $name): array
    {
        return array_values(array_filter(explode(' ', self::normalizeName($name))));
    }

    /**
     * True when an input token covers an expected token: exact equality after
     * normalization, or — when both are at least 4 characters — one contains the
     * other. Containment absorbs swallowed spacing ("GeraldLouis" covers both
     * "gerald" and "louis") while staying biased toward whole-word matches.
     */
    private static function tokenCovers(string $inputToken, string $expectedToken): bool
    {
        if ($inputToken === $expectedToken) {
            return true;
        }

        if (mb_strlen($inputToken) < 4 || mb_strlen($expectedToken) < 4) {
            return false;
        }

        return str_contains($inputToken, $expectedToken)
            || str_contains($expectedToken, $inputToken);
    }

    private static function anyTokenCovers(array $inputTokens, string $expectedToken): bool
    {
        foreach ($inputTokens as $inputToken) {
            if (self::tokenCovers($inputToken, $expectedToken)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Score an order-insensitive match between the input tokens and a client.
     * Returns [isExact, isPartial].
     *
     * A partial match requires the surname AND the first name to be present in
     * the input in any order. An exact (strong) match additionally requires every
     * multi-character given-name token to be covered; single-character initials
     * are optional because users routinely omit them ("Gerald Louis F. Sumaylo"
     * vs input "Sumaylo Gerald Louis" must still resolve to the same client).
     */
    private static function scoreOrderInsensitive(array $inputTokens, Client $client): array
    {
        $surname   = self::normalizeName((string) $client->last_name);
        $firstName = self::normalizeName((string) $client->first_name);

        if ($surname === '' || $firstName === '') {
            return [false, false];
        }

        if (! self::anyTokenCovers($inputTokens, $surname)) {
            return [false, false];
        }

        if (! self::anyTokenCovers($inputTokens, $firstName)) {
            return [false, false];
        }

        $isExact = true;
        foreach (self::nameTokens((string) $client->middle_name) as $middleToken) {
            if (mb_strlen($middleToken) <= 1) {
                continue;
            }
            if (! self::anyTokenCovers($inputTokens, $middleToken)) {
                $isExact = false;
            }
        }

        return [$isExact, true];
    }

    /**
     * Order-insensitive matching that detects reordered names ("Sumaylo Gerald
     * Louis" vs a client named "Gerald Louis F. Sumaylo"), swallowed/extra
     * spacing, and any letter casing.
     *
     * Surname-first input is ambiguous (is "Gerald Louis Sumaylo" first-last or
     * surname-first?) so candidates are located by matching the client's surname
     * against ANY input word or contiguous run (up to 3 tokens, which also covers
     * compound surnames like "Dela Cruz"), then verified token-by-token.
     */
    public static function findAnyOrderMatches(array $parsed): array
    {
        $tokens = $parsed['parts'] ?? [];
        if (count($tokens) < 2) {
            return ['exact' => collect(), 'partial' => collect()];
        }

        $variants = [];
        $maxRun = min(3, count($tokens));
        for ($len = 1; $len <= $maxRun; $len++) {
            for ($i = 0; $i + $len <= count($tokens); $i++) {
                $variants[] = implode(' ', array_slice($tokens, $i, $len));
            }
        }
        $variants = array_values(array_unique($variants));

        $candidates = Client::where(function ($query) use ($variants) {
            foreach ($variants as $variant) {
                $query->orWhereRaw('LOWER(last_name) = ?', [$variant]);
            }
        })->get();

        $exact   = collect();
        $partial = collect();

        foreach ($candidates as $client) {
            [$isExact, $isPartial] = self::scoreOrderInsensitive($tokens, $client);
            if ($isExact) {
                $exact->push($client);
            } elseif ($isPartial) {
                $partial->push($client);
            }
        }

        return [
            'exact'   => $exact->unique('id')->values(),
            'partial' => $partial->unique('id')->values(),
        ];
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

        // Detect surname-first input WITHOUT a comma before the parser assigns
        // components, so the stored first/middle/last columns are correct.
        // Signal: at least 3 words and the LAST word is a single-letter (middle)
        // initial, as in "Sumaylo Gerald Louis F." (almost always written with a
        // trailing period). A standard-order name ends in its (multi-letter)
        // surname, so it never trips this rule.
        $isSurnameFirst = count($parts) >= 3
            && mb_strlen($parts[count($parts) - 1]) === 1
            && mb_strlen($parts[0]) >= 2
            && preg_match('/\s+\p{L}\s*\.?\s*$/u', $fullName) === 1;

        if ($isSurnameFirst) {
            $given = array_slice($parts, 1);
            $lastName = $parts[0];
            $firstName = $given[0] ?? '';
            $middleName = count($given) > 1 ? implode(' ', array_slice($given, 1)) : '';
            $normalized = trim(implode(' ', array_merge($given, [$lastName])));
            $parts = array_values(array_filter(explode(' ', $normalized)));
        } else {
            $firstName = $parts[0] ?? '';
            $lastName = $parts ? end($parts) : '';
            $middleName = count($parts) > 2
                ? implode(' ', array_slice($parts, 1, -1))
                : '';
        }

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

        if ($allByLastName->isNotEmpty()) {
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

            // Level 2 — Partial match: Same last name AND at least one matching/overlapping given name part
            // Single-character initials or common surnames NEVER produce a match on their own
            if ($results['exact']->isEmpty() && mb_strlen($lastName) >= 3 && !empty($inputGivenParts)) {
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
        }

        // Level 3 — Order-insensitive token-set match. Catches reordered names
        // ("Sumaylo Gerald Louis"), concatenated words ("GeraldLouis"), and any
        // case/spacing mix, even when the standard-order Levels 1-2 found nothing.
        $anyOrder = self::findAnyOrderMatches($parsed);

        $results['exact'] = $results['exact']
            ->merge($anyOrder['exact'])
            ->unique('id')
            ->values();

        $results['partial'] = $results['partial']
            ->merge($anyOrder['partial'])
            ->reject(fn ($client) => $results['exact']->contains('id', $client->id))
            ->unique('id')
            ->values();

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
