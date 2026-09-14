<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill the main-client / intake-snapshot / family-member person linkage for
     * any records that predate the redesign.
     *
     * - main_client_id is taken from the existing client_id FK (the historical main client).
     * - intake_* columns are copied from the linked client + case.
     * - family_members.full_name is parsed and linked to a client (person_id) only when a
     *   single confident match exists; otherwise the name fields are still parsed and
     *   person_id is left NULL for manual review. Nothing is guessed.
     */
    public function up(): void
    {
        $this->backfillMainClientsAndSnapshots();
        $this->backfillFamilyMembers();
    }

    private function backfillMainClientsAndSnapshots(): void
    {
        $schema = DB::getSchemaBuilder();
        if (!$schema->hasTable('social_case_studies')) {
            return;
        }

        $columns = $schema->getColumnListing('social_case_studies');
        $hasIntake = in_array('intake_full_name', $columns, true);

        $cases = DB::table('social_case_studies')
            ->select(array_merge(['id', 'client_id', 'main_client_id', 'date_processed'], $hasIntake ? ['intake_full_name'] : []))
            ->get();

        foreach ($cases as $case) {
            $update = [];

            if ($case->main_client_id === null && $case->client_id !== null) {
                $update['main_client_id'] = $case->client_id;
            }

            if ($hasIntake && empty($case->intake_full_name) && $case->client_id !== null) {
                $client = DB::table('clients')->where('id', $case->client_id)->first();
                if ($client) {
                    $update = array_merge($update, $this->snapshotFromClient($client));
                }
            }

            if (!empty($update)) {
                DB::table('social_case_studies')
                    ->where('id', $case->id)
                    ->update($update);
            }
        }
    }

    private function snapshotFromClient(object $client): array
    {
        $fullName = trim(implode(' ', array_filter([
            $client->first_name,
            $client->middle_name,
            $client->last_name,
            $client->suffix ?? null,
        ], fn ($part) => $part !== null && $part !== '')));

        return [
            'intake_first_name'      => $client->first_name,
            'intake_middle_name'     => $client->middle_name,
            'intake_last_name'       => $client->last_name,
            'intake_suffix'          => $client->suffix ?? null,
            'intake_full_name'       => $fullName ?: null,
            'intake_birthdate'       => $client->birthdate,
            'intake_age'             => $client->age,
            'intake_gender'          => $client->gender,
            'intake_civil_status'    => $client->civil_status,
            'intake_religion'        => $client->religion,
            'intake_birthplace'      => $client->birthplace,
            'intake_education'       => $client->education,
            'intake_occupation'      => $client->occupation,
            'intake_income'          => $client->income,
            'intake_address'         => $client->address,
            'intake_barangay'        => $client->barangay,
            'intake_contact_number'  => $client->contact_number,
        ];
    }

    private function backfillFamilyMembers(): void
    {
        $schema = DB::getSchemaBuilder();
        if (!$schema->hasTable('family_members')) {
            return;
        }

        $columns = $schema->getColumnListing('family_members');
        if (!in_array('person_id', $columns, true)) {
            return;
        }

        $members = DB::table('family_members')->whereNull('person_id')->get();

        foreach ($members as $member) {
            if (empty($member->full_name)) {
                continue;
            }

            $parsed = $this->parseFullName($member->full_name);
            $update = [
                'first_name'  => $parsed['first_name'] ?: null,
                'middle_name' => $parsed['middle_name'] ?: null,
                'last_name'   => $parsed['last_name'] ?: null,
                'suffix'      => $parsed['suffix'] ?: null,
            ];

            $match = $this->findConfidentClientMatch($parsed);
            if ($match !== null) {
                $update['person_id'] = $match;
            }

            DB::table('family_members')
                ->where('id', $member->id)
                ->update($update);
        }
    }

    private function normalizeName(string $name): string
    {
        $name = trim($name);
        $name = mb_strtolower($name, 'UTF-8');
        $name = str_replace(['.', ',', '"', "'", "\xE2\x80\x99"], '', $name);
        $name = str_replace('-', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    private function parseFullName(string $fullName): array
    {
        $fullName = trim($fullName);

        if (str_contains($fullName, ',')) {
            [$last, $rest] = explode(',', $fullName, 2);
            $parts = array_values(array_filter(explode(' ', $this->normalizeName($rest))));
            return [
                'first_name'  => $parts[0] ?? '',
                'middle_name' => count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '',
                'last_name'   => $this->normalizeName($last),
                'suffix'      => $this->extractSuffix($parts),
            ];
        }

        $parts = array_values(array_filter(explode(' ', $this->normalizeName($fullName))));
        if (empty($parts)) {
            return ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => ''];
        }

        $suffix = $this->extractSuffix($parts);
        $withoutSuffix = array_values(array_diff($parts, $suffix !== '' ? [$this->normalizeName($suffix)] : []));

        return [
            'first_name'  => $withoutSuffix[0] ?? '',
            'last_name'   => !empty($withoutSuffix) ? end($withoutSuffix) : '',
            'middle_name' => count($withoutSuffix) > 2 ? implode(' ', array_slice($withoutSuffix, 1, -1)) : '',
            'suffix'      => $suffix,
        ];
    }

    private function extractSuffix(array $parts): string
    {
        if (empty($parts)) {
            return '';
        }
        $last = end($parts);
        $suffixes = ['jr', 'sr', 'iii', 'ii', 'iv', 'v'];
        if (in_array($last, $suffixes, true)) {
            return $last;
        }
        return '';
    }

    private function findConfidentClientMatch(array $parsed): ?int
    {
        $firstName = $parsed['first_name'];
        $lastName  = $parsed['last_name'];

        if ($firstName === '' || $lastName === '') {
            return null;
        }

        $candidates = DB::table('clients')
            ->whereRaw('LOWER(last_name) = ?', [$lastName])
            ->whereNotNull('first_name')
            ->get();

        $scored = [];
        foreach ($candidates as $client) {
            $normFirst = $this->normalizeName($client->first_name);
            $normLast  = $this->normalizeName($client->last_name);

            if ($normFirst !== $firstName || $normLast !== $lastName) {
                continue;
            }

            $score = 2.0;

            if (!empty($parsed['middle_name']) && !empty($client->middle_name)) {
                $normMiddle = $this->normalizeName($client->middle_name);
                if ($normMiddle === $parsed['middle_name']) {
                    $score += 1.0;
                }
            }

            if (!empty($parsed['suffix']) && !empty($client->suffix)) {
                if ($this->normalizeName($client->suffix) === $parsed['suffix']) {
                    $score += 1.0;
                }
            }

            $scored[] = ['id' => $client->id, 'score' => $score];
        }

        if (empty($scored)) {
            return null;
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        $top = $scored[0]['score'];
        $ties = count(array_filter($scored, fn ($s) => $s['score'] === $top));

        // Only link when there is a single strongest candidate; otherwise flag for manual review.
        return $ties === 1 ? $scored[0]['id'] : null;
    }

    public function down(): void
    {
        // Backfill is intentionally irreversible; no action taken.
    }
};