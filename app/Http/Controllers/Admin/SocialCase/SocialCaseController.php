<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Models\SocialCase\SocialCaseStudy;
use App\Models\SocialCase\SocialCaseActivityLog;
use App\Models\SocialCase\EligibilityAuditLog;
use App\Models\Client;
use App\Models\User;
use App\Models\OnlineRequest;
use App\Services\SocialCase\EligibilityChecker;
use App\Services\NameMatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialCaseController extends Controller
{
    public function socialCaseWelcome()
    {
        return redirect()->route('admin.social-case.dashboard');
    }

    public function socialCaseDashboard()
    {
        $justLoggedIn = session('admin_just_logged_in', false);
        if ($justLoggedIn) {
            session()->forget('admin_just_logged_in');
        }

        $role = (string) session('admin_user_role');
        $isEligibilityChecker = in_array($role, ['eligibility_checker'], true);

        // Calculate dashboard stats from database
        $stats = [
            'pending_eligibility' => 0,
            'eligible_clients' => 0,
            'forwarded_to_encoder' => 0,
            'rejected_clients' => 0,
            'total_clients' => 0,
            'for_encoding' => 0,
            'released_today' => 0,
            'total_released' => 0,
        ];

        if ($isEligibilityChecker) {
            // Eligibility checker stats - count online requests instead of social case studies
            $stats['pending_eligibility'] = OnlineRequest::where('status', 'pending')->whereNull('case_id')->count();
            $stats['eligible_clients'] = SocialCaseStudy::where('eligibility_status', 'eligible')->count();
            $stats['forwarded_to_encoder'] = SocialCaseStudy::where('eligibility_status', 'eligible')
                ->whereNotNull('eligible_by')
                ->count();
            $stats['rejected_clients'] = OnlineRequest::where('status', 'rejected')->whereNull('case_id')->count();
        } else {
            // Case encoder stats
            $stats['total_clients'] = Client::has('socialCaseStudies')->count();
            $stats['for_encoding'] = SocialCaseStudy::where('eligibility_status', 'eligible')
                ->where('status', 'Draft')
                ->count();
            $stats['released_today'] = SocialCaseStudy::where('status', 'Released')
                ->whereDate('released_at', today())
                ->count();
            $stats['total_released'] = SocialCaseStudy::whereIn('status', ['Printed', 'Released'])->count();
        }

        // Get online request counts for badge
        $pendingCount = OnlineRequest::where('status', 'pending')->whereNull('case_id')->count();
        $acceptedCount = OnlineRequest::where('status', 'approved')->whereNull('case_id')->count();
        $rejectedCount = OnlineRequest::where('status', 'rejected')->whereNull('case_id')->count();

        $onlineRequestCounts = [
            'pending' => $pendingCount,
            'accepted' => $acceptedCount,
            'rejected' => $rejectedCount,
        ];

        return view('admin.social-case.dashboard', compact('justLoggedIn', 'stats', 'onlineRequestCounts'));
    }

    public function socialCaseNew()
    {
        $role = (string) session('admin_user_role');
        $canCheckEligibility = in_array($role, ['eligibility_checker', 'admin'], true);
        $canEncode = in_array($role, ['social_worker', 'admin'], true);

        // Get online request counts for sidebar badges
        $onlineRequestCounts = [
            'pending' => \App\Models\OnlineRequest::where('status', 'pending')->whereNull('case_id')->count(),
            'accepted' => \App\Models\OnlineRequest::where('status', 'approved')->whereNull('case_id')->count(),
            'rejected' => \App\Models\OnlineRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.social-case.new', compact('canCheckEligibility', 'canEncode', 'onlineRequestCounts'));
    }

    public function socialCaseIntake()
    {
        $role = (string) session('admin_user_role');
        $canEncode = in_array($role, ['social_worker', 'admin'], true);

        return view('admin.social-case.intake', compact('canEncode'));
    }

    public function socialCaseArchive()
    {
        // Get online request counts for badge
        $pendingCount = OnlineRequest::where('status', 'pending')->whereNull('case_id')->count();
        $acceptedCount = OnlineRequest::where('status', 'approved')->whereNull('case_id')->count();
        $rejectedCount = OnlineRequest::where('status', 'rejected')->whereNull('case_id')->count();

        $onlineRequestCounts = [
            'pending' => $pendingCount,
            'accepted' => $acceptedCount,
            'rejected' => $rejectedCount,
        ];

        return view('admin.social-case.archive', compact('onlineRequestCounts'));
    }

    public function socialCaseCases()
    {
        // Get online request counts for badge
        $pendingCount = OnlineRequest::where('status', 'pending')->whereNull('case_id')->count();
        $acceptedCount = OnlineRequest::where('status', 'approved')->whereNull('case_id')->count();
        $rejectedCount = OnlineRequest::where('status', 'rejected')->whereNull('case_id')->count();

        $onlineRequestCounts = [
            'pending' => $pendingCount,
            'accepted' => $acceptedCount,
            'rejected' => $rejectedCount,
        ];

        return view('admin.social-case.cases', compact('onlineRequestCounts'));
    }

    /**
     * Clients forwarded by the eligibility checker and waiting to be encoded.
     */
    public function socialCaseSubmitted()
    {
        $submitted = SocialCaseStudy::with('client', 'eligibleByUser')
            ->where('eligibility_status', 'eligible')
            ->whereNotNull('eligible_by')
            ->whereNull('encoded_by')
            ->whereNotIn('status', ['Review', 'Approved', 'Printed', 'Released', 'Archived'])
            ->orderByDesc('eligible_at')
            ->get();

        $acceptedOnlineRequests = OnlineRequest::with('attachments')
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('case_id')
                  ->orWhereHas('case', function ($cq) {
                      $cq->whereNotIn('status', ['Printed', 'Released']);
                  });
            })
            ->orderByDesc('updated_at')
            ->get();

        // Get online request counts for badge
        $pendingCount = OnlineRequest::where('status', 'pending')->whereNull('case_id')->count();
        $acceptedCount = OnlineRequest::where('status', 'approved')->whereNull('case_id')->count();
        $rejectedCount = OnlineRequest::where('status', 'rejected')->whereNull('case_id')->count();

        $onlineRequestCounts = [
            'pending' => $pendingCount,
            'accepted' => $acceptedCount,
            'rejected' => $rejectedCount,
        ];

        return view('admin.social-case.submitted', compact('submitted', 'acceptedOnlineRequests', 'onlineRequestCounts'));
    }

    public function socialCaseDetail($caseId)
    {
        return view('admin.social-case.detail', compact('caseId'));
    }

    public function socialCaseDocument($caseId, $agency)
    {
        return view('admin.social-case.document', compact('caseId', 'agency'));
    }

    public function getCases()
    {
        $cases = SocialCaseStudy::with('client', 'officer', 'encoder', 'eligibleByUser', 'releasedByUser', 'interview', 'familyMembers')
            ->orderBy('created_at', 'desc')
            ->get();

        $cases->each(function ($case) {
            $case->client_name = $case->client ? $case->client->full_name : '';
            $case->client_barangay = $case->client ? $case->client->barangay : '';
        });

        return response()->json($cases);
    }

    public function getEligibilityData()
    {
        $cases = SocialCaseStudy::with('client', 'officer', 'encoder', 'eligibleByUser', 'releasedByUser', 'interview', 'familyMembers')
            ->orderBy('created_at', 'desc')
            ->get();

        $cases->each(function ($case) {
            $case->client_name = $case->client ? $case->client->full_name : '';
            $case->client_barangay = $case->client ? $case->client->barangay : '';
            $case->eligible_at = $case->eligible_at ? $case->eligible_at->format('Y-m-d H:i:s') : null;
            $case->rejected_at = $case->rejected_at ? $case->rejected_at->format('Y-m-d H:i:s') : null;
        });

        return response()->json($cases);
    }

    public function getCase($id)
    {
        $case = SocialCaseStudy::with('client', 'officer', 'encoder', 'eligibleByUser', 'releasedByUser', 'interview', 'familyMembers')->find($id);
        if (!$case) {
            return response()->json(['error' => 'Case not found'], 404);
        }
        return response()->json($case);
    }

    /**
     * Return authoritative document-generation values (date + age) that are
     * computed fresh from the database on every request.  The frontend MUST
     * use these instead of any cached JS state when generating/reprinting.
     */
    public function getDocumentData($id)
    {
        $case = SocialCaseStudy::with('client')->find($id);
        if (!$case) {
            return response()->json(['error' => 'Case not found'], 404);
        }

        $client = $case->client;
        $today  = now()->startOfDay();

        $documentAge = null;
        if ($client && $client->birthdate) {
            $birthdate = \Carbon\Carbon::parse($client->birthdate);
            $documentAge = (int) $birthdate->diffInYears($today, false);
            // diffInYears with false can return negative for future dates;
            // clamp to 0 as a safety net.
            if ($documentAge < 0) {
                $documentAge = 0;
            }
        }

        return response()->json([
            'document_date'     => $today->toDateString(),
            'client_age'        => $documentAge,
            'client_birthdate'  => $client?->birthdate?->toDateString(),
        ]);
    }

    /**
     * Server-side eligibility check for a client (6-month assistance rule).
     * Restricted to eligibility checker role via route middleware.
     */
    /* ── Name Normalization Helpers ──────────────────────────────────── */

    private static function normalizeName(string $name): string
    {
        $name = trim($name);
        $name = mb_strtolower($name, 'UTF-8');
        $name = str_replace(['.', ',', '"', "'", "\xE2\x80\x99"], '', $name);
        $name = str_replace('-', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    private static function parseFullName(string $fullName): array
    {
        $normalized = self::normalizeName($fullName);
        $parts = array_values(array_filter(explode(' ', $normalized)));

        $firstName  = $parts[0] ?? '';
        $lastName   = $parts ? end($parts) : '';
        $middleName = count($parts) > 2
            ? implode(' ', array_slice($parts, 1, -1))
            : '';

        return [
            'first_name'  => $firstName,
            'last_name'   => $lastName,
            'middle_name' => $middleName,
            'normalized'  => $normalized,
            'parts'       => $parts,
        ];
    }

    private static function findCandidateClients(array $parsed): array
    {
        $firstName = $parsed['first_name'];
        $lastName  = $parsed['last_name'];

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
     * Count how many input parts map to client parts, including
     * concatenated client parts (e.g. "geraldlouis" = "gerald" + "louis").
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

            // 2. Concatenated consecutive client parts match
            $n = count($clientParts);
            for ($start = 0; $start < $n; $start++) {
                if (in_array($start, $usedClient, true)) continue;
                $concat = '';
                $usedInRun = [];
                for ($j = $start; $j < $n; $j++) {
                    $concat .= $clientParts[$j];
                    $usedInRun[] = $j;
                    if ($concat === $ip) {
                        // Count as matching the number of parts that were concatenated
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

    public function checkEligibility(Request $request, EligibilityChecker $checker)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
        ]);

        $name = trim($request->input('client_name'));
        $parsed = NameMatcher::parseFullName($name);

        $start = microtime(true);
        $candidates = NameMatcher::findCandidateClients($parsed);

        $client    = $candidates['exact']->first();
        $matchType = $client ? 'exact' : null;

        if (!$client && $candidates['partial']->isNotEmpty()) {
            $client    = $candidates['partial']->first();
            $matchType = 'partial';
        }

        $result = [
            'eligible'            => true,
            'client_found'        => (bool) $client,
            'match_type'          => $matchType,
            'client'              => $client,
            'eligible_again_date' => null,
            'last_assistance_date'=> null,
            'blocking'            => null,
            'existing_case'       => null,
            'possible_matches'    => [],
        ];

        if ($client) {
            $checkResult = $checker->check($client);

            $result['eligible']             = $checkResult['eligible'];
            $result['eligible_again_date']  = $checkResult['eligibleAgainDate']?->toDateString();
            $result['last_assistance_date'] = $checkResult['lastAssistanceDate']?->toDateString();
            $result['blocking'] = $checkResult['blockingRecord'] ? [
                'assistance_type' => $checkResult['blockingRecord']->assistance_type,
                'release_date'    => $checkResult['blockingRecord']->release_date?->toDateString(),
            ] : null;

            $activeCase = $client->socialCaseStudies()
                ->where('status', '!=', 'Archived')
                ->orderByDesc('created_at')
                ->first();

            if ($activeCase) {
                $result['existing_case'] = [
                    'id'                => $activeCase->id,
                    'case_number'       => $activeCase->case_number,
                    'status'            => $activeCase->status,
                    'eligibility_status'=> $activeCase->eligibility_status,
                ];
            }

            // Return additional partial matches for verification (skip the primary match)
            if ($matchType === 'partial' && $candidates['partial']->count() > 1) {
                $result['possible_matches'] = $candidates['partial']
                    ->reject(fn($c) => $c->id === $client->id)
                    ->take(5)
                    ->map(fn($c) => [
                        'id'   => $c->id,
                        'name' => trim(sprintf('%s %s %s', $c->first_name, $c->middle_name, $c->last_name)),
                    ])
                    ->values()
                    ->toArray();
            }
        }

        EligibilityAuditLog::create([
            'client_id'         => $client?->id,
            'client_name'       => $name,
            'officer_id'        => session('admin_user_id'),
            'officer_name'      => session('admin_user_name') ?? 'Eligibility Checker',
            'result'            => $result['eligible'] ? 'eligible' : 'ineligible',
            'result_details'    => $result['eligible']
                ? 'Client is eligible for assistance.'
                : 'Client is within the 6-month assistance restriction period.',
            'ip_address'        => $request->ip(),
            'user_agent'        => $request->userAgent(),
            'search_duration_ms'=> (int) round((microtime(true) - $start) * 1000),
        ]);

        return response()->json($result);
    }

    /**
     * Record a passing eligibility result and forward the client for case encoding.
     * The server re-runs the eligibility check before creating the handoff record,
     * so a raw crafted request cannot bypass the 6-month rule.
     */
    public function submitEligibility(Request $request, EligibilityChecker $checker)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'override'    => 'sometimes|boolean',
        ]);

        $name     = trim($request->input('client_name'));
        $override = $request->boolean('override');

        // Use the same normalized matching as checkEligibility
        $parsed    = NameMatcher::parseFullName($name);
        $candidates = NameMatcher::findCandidateClients($parsed);
        $client    = $candidates['exact']->first()
                  ?? $candidates['partial']->first();

        // If no existing client found, create one (same as findOrCreateClient)
        if (!$client) {
            $client = Client::create([
                'first_name'  => $parsed['first_name'],
                'middle_name' => $parsed['middle_name'] ?: null,
                'last_name'   => $parsed['last_name'],
            ]);
        }

        $checkResult = $checker->check($client);

        if (! $checkResult['eligible']) {
            return response()->json([
                'error' => 'Client is not eligible. The 6-month assistance restriction still applies.',
                'eligible' => false,
            ], 422);
        }

        $activeCase = $client->socialCaseStudies()
            ->where('status', '!=', 'Archived')
            ->orderByDesc('created_at')
            ->first();

        if ($activeCase && ! $override) {
            return response()->json([
                'eligible' => true,
                'existing_case' => [
                    'id' => $activeCase->id,
                    'case_number' => $activeCase->case_number,
                    'status' => $activeCase->status,
                    'eligibility_status' => $activeCase->eligibility_status,
                ],
                'message' => 'Client already has an active case record.',
            ], 409);
        }

        $case = DB::transaction(function () use ($client) {
            $case = SocialCaseStudy::create([
                'client_id'          => $client->id,
                'officer_id'         => session('admin_user_id'),
                'case_number'        => $this->generateCaseNumber(),
                'date_processed'     => now()->toDateString(),
                'encoded_by'         => null,
                'status'             => 'Draft',
                'eligibility_status' => 'eligible',
                'eligible_by'        => session('admin_user_id'),
                'eligible_at'        => now(),
                'workflow_step'       => 'requirements_verification',
            ]);

            return $case;
        });

        return response()->json([
            'eligible' => true,
            'case' => $case->load('client'),
            'message' => 'Client passed eligibility and was forwarded for case encoding.',
        ], 201);
    }

    public function storeCase(Request $request)
    {
        $role = (string) session('admin_user_role');
        if (! in_array($role, ['social_worker', 'admin'], true)) {
            abort(403, 'Only case encoders can create or update cases.');
        }

        $data = $request->validate([
            'case_id'                         => 'nullable|integer|exists:social_case_studies,id',
            'online_request_id'               => 'nullable|integer|exists:online_requests,id',
            'control_no'                      => 'nullable|string|max:50',
            'status'                          => 'required|string|max:50',
            'client'                          => 'required|array',
            'client.name'                     => 'required|string|max:255',
            'client.age'                      => 'nullable|integer|min:0|max:150',
            'client.sex'                      => 'nullable|in:Male,Female',
            'client.address'                  => 'nullable|string|max:500',
            'client.birthdate'                => 'nullable|date_format:Y-m-d',
            'client.birthplace'               => 'nullable|string|max:255',
            'client.religion'                 => 'nullable|string|max:100',
            'client.education'                => 'nullable|string|max:100',
            'client.civil_status'             => 'nullable|in:Single,Married,Widowed,Separated',
            'client.occupation'               => 'nullable|string|max:255',
            'client.income'                   => 'nullable|string|max:100',
            'client.contact'                  => 'nullable|string|max:20',
            'household'                       => 'required|array|min:1',
            'household.*.name'                => 'nullable|string|max:255',
            'household.*.relationship'        => 'nullable|string|max:100',
            'household.*.age'                 => 'nullable|integer|min:0|max:150',
            'household.*.education'           => 'nullable|string|max:100',
            'household.*.occupation'          => 'nullable|string|max:255',
            'household.*.income'              => 'nullable|string|max:100',
            'interview'                       => 'required|array',
            'interview.problem_presented'     => 'nullable|string|max:10000',
            'interview.home_condition'       => 'nullable|string|max:10000',
            'interview.socio_economic'       => 'nullable|string|max:10000',
            'interview.evaluation'           => 'nullable|string|max:10000',
            'interview.recommendation'       => 'nullable|string|max:10000',
            'interview.report_date'          => 'nullable|date',
            'signers'                         => 'required|array',
            'signers.prepared_by_name'        => 'nullable|string|max:255',
            'signers.prepared_by_title'       => 'nullable|string|max:255',
            'signers.noted_by_name'           => 'nullable|string|max:255',
            'signers.noted_by_title'          => 'nullable|string|max:255',
            'purpose'                         => 'required|string|in:Medical Assistance,Burial Assistance,Educational Assistance,Financial Assistance,Food / Relief Assistance,Livelihood Assistance,Other',
            'agencies'                        => 'nullable|array',
            'agencies.*'                      => 'string|in:PCSO,DSWD,OP,DOH,MSWDO',
            'requirements'                    => 'required|array',
            'requirements.*.name'             => 'required|string',
            'requirements.*.submitted'        => 'required|boolean',
        ]);

        $clientId = $this->findOrCreateClient($request->input('client'));
        $client   = Client::find($clientId);

        $agencies = $data['agencies'] ?? [];
        $encodedById = session('admin_user_id');
        $caseId = $data['case_id'] ?? null;

        if ($caseId) {
            $existing = SocialCaseStudy::find($caseId);
            if (! $existing) {
                return response()->json(['error' => 'Case not found'], 404);
            }
            if ($existing->eligibility_status !== 'eligible') {
                return response()->json(['error' => 'Only clients that passed eligibility checking can be encoded.'], 403);
            }
        } else {
            // Enforce 6-month eligibility when creating a brand-new case
            $checker   = app(EligibilityChecker::class);
            $checkResult = $checker->check($client);

            if (! $checkResult['eligible']) {
                return response()->json([
                    'error'   => 'This client already has a Social Case Study request within the last 6 months. '
                                . ($checkResult['eligibleAgainDate']
                                    ? ' Eligible again on: ' . $checkResult['eligibleAgainDate']->toDateString()
                                    : ''),
                    'eligible'=> false,
                ], 422);
            }

            $hasActive = $client->socialCaseStudies()
                ->where('status', '!=', 'Archived')
                ->where('eligibility_status', '!=', 'ineligible')
                ->exists();

            if ($hasActive) {
                return response()->json([
                    'error'   => 'This client already has an active Social Case Study. Please use the existing case record.',
                    'eligible'=> true,
                ], 409);
            }
        }

        $case = DB::transaction(function () use ($data, $clientId, $agencies, $encodedById, $caseId) {
            $case = $caseId
                ? SocialCaseStudy::find($caseId)
                : SocialCaseStudy::create([
                    'client_id'          => $clientId,
                    'officer_id'         => $encodedById,
                    'case_number'        => $this->generateCaseNumber(),
                    'date_processed'     => now()->toDateString(),
                    'workflow_step'       => 'requirements_verification',
                ]);

            $case->update([
                'client_id'            => $clientId,
                'date_processed'       => now()->toDateString(),
                'interview_date'       => $data['interview']['report_date'] ?? null,
                'purpose'              => $data['purpose'],
                'submitted_to'         => implode(', ', $agencies),
                'encoded_by'           => $encodedById,
                'status'               => $data['status'],
                'eligibility_status'   => $caseId ? ($case->eligibility_status ?: 'eligible') : 'eligible',
                'summary'              => $data['interview']['problem_presented'] ?? null,
                'requirements_complete' => !empty($data['requirements']),
                'signers'              => $data['signers'] ?? [],
            ]);

            $interview = $data['interview'];
            \App\Models\SocialCase\CaseInterview::updateOrCreate(
                ['social_case_study_id' => $case->id],
                [
                    'interview_reason'        => $data['purpose'],
                    'interview_situation'     => $interview['problem_presented'] ?? null,
                    'interview_household'     => $interview['home_condition'] ?? null,
                    'monthly_income'          => null,
                    'monthly_expenses'        => null,
                    'interview_notes'         => $interview['socio_economic'] ?? null,
                    'social_worker_assessment' => $interview['evaluation'] ?? null,
                    'recommendation'          => $interview['recommendation'] ?? null,
                ]
            );

            $case->familyMembers()->delete();

            $household = $data['household'] ?? [];
            foreach ($household as $member) {
                if (empty($member['name'])) continue;
                \App\Models\SocialCase\FamilyMember::create([
                    'social_case_study_id' => $case->id,
                    'full_name'            => $member['name'] ?? '',
                    'relationship'         => $member['relationship'] ?? '',
                    'age'                  => is_numeric($member['age'] ?? null) ? (int) $member['age'] : null,
                    'education'            => $member['education'] ?? null,
                    'occupation'           => $member['occupation'] ?? null,
                    'monthly_income'       => $member['income'] ?? null,
                ]);
            }

            if (!empty($data['online_request_id'])) {
                \App\Models\OnlineRequest::where('id', $data['online_request_id'])->update(['case_id' => $case->id]);
            }

            return $case;
        });

        return response()->json($case->load('client'), 201);
    }

    private function findOrCreateClient(array $clientData): int
    {
        $fullName = trim($clientData['name'] ?? '');
        $parsed   = NameMatcher::parseFullName($fullName);
        $firstName  = $parsed['first_name'];
        $lastName   = $parsed['last_name'];
        $middleName = $parsed['middle_name'];

        $client = Client::whereRaw('LOWER(first_name) = ?', [$firstName])
            ->whereRaw('LOWER(last_name) = ?', [$lastName])
            ->first();

        if (!$client) {
            $client = Client::create([
                'first_name'     => $firstName,
                'middle_name'    => $middleName,
                'last_name'      => $lastName,
                'birthdate'      => $clientData['birthdate'] ?? null,
                'gender'         => $clientData['sex'] ?? null,
                'age'            => is_numeric($clientData['age'] ?? null) ? (int) $clientData['age'] : null,
                'address'        => $clientData['address'] ?? null,
                'barangay'       => $clientData['address'] ?? null,
                'contact_number' => $clientData['contact'] ?? null,
                'birthplace'     => $clientData['birthplace'] ?? null,
                'religion'       => $clientData['religion'] ?? null,
                'education'      => $clientData['education'] ?? null,
                'civil_status'   => $clientData['civil_status'] ?? null,
                'occupation'     => $clientData['occupation'] ?? null,
                'income'         => $clientData['income'] ?? null,
            ]);
        } else {
            $updates = [];
            if (!empty($clientData['birthdate'])) $updates['birthdate'] = $clientData['birthdate'];
            if (!empty($clientData['sex'])) $updates['gender'] = $clientData['sex'];
            if (isset($clientData['age']) && is_numeric($clientData['age'])) $updates['age'] = (int) $clientData['age'];
            if (!empty($clientData['address'])) {
                $updates['address'] = $clientData['address'];
                $updates['barangay'] = $clientData['address'];
            }
            if (!empty($clientData['contact'])) $updates['contact_number'] = $clientData['contact'];
            if (!empty($clientData['birthplace'])) $updates['birthplace'] = $clientData['birthplace'];
            if (!empty($clientData['religion'])) $updates['religion'] = $clientData['religion'];
            if (!empty($clientData['education'])) $updates['education'] = $clientData['education'];
            if (!empty($clientData['civil_status'])) $updates['civil_status'] = $clientData['civil_status'];
            if (!empty($clientData['occupation'])) $updates['occupation'] = $clientData['occupation'];
            if (!empty($clientData['income'])) $updates['income'] = $clientData['income'];
            if ($updates) $client->update($updates);
        }

        return $client->id;
    }

    private static function generateCaseNumber(): string
    {
        $now = now();
        $prefix = 'MSWD-O-' . $now->format('Y-m') . '-';
        $last = SocialCaseStudy::where('case_number', 'LIKE', $prefix . '%')
            ->orderByDesc('case_number')->value('case_number');
        $seq = 1;
        if ($last && preg_match('/-(\d{4})$/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function updateCase(Request $request, $id)
    {
        $role = (string) session('admin_user_role');
        if (! in_array($role, ['social_worker', 'admin'], true)) {
            abort(403, 'Only case encoders can update cases.');
        }

        $case = SocialCaseStudy::find($id);
        if (!$case) {
            return response()->json(['error' => 'Case not found'], 404);
        }

        $request->validate([
            'control_no' => 'sometimes|unique:social_case_studies,case_number,' . $id,
            'status' => 'sometimes',
            'purpose' => 'sometimes',
        ]);

        $fillable = $case->getFillable();
        $data = collect($request->only($fillable))->filter()->toArray();

        if (($data['status'] ?? null) === 'Released' && empty($data['released_at'])) {
            $data['released_at'] = now();
        }

        $case->update($data);

        return response()->json($case);
    }

    public function deleteCase($id)
    {
        $role = (string) session('admin_user_role');
        if (! in_array($role, ['social_worker', 'admin'], true)) {
            abort(403, 'Only case encoders can archive cases.');
        }

        $case = SocialCaseStudy::find($id);
        if (!$case) {
            return response()->json(['error' => 'Case not found'], 404);
        }

        $case->delete();
        return response()->json(['message' => 'Case deleted successfully']);
    }

    public function logActivity(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'details' => 'required|string',
            'case_info' => 'nullable|array',
        ]);

        $activity = SocialCaseActivityLog::create([
            'action' => $request->action,
            'details' => $request->details,
            'case_info' => $request->case_info,
            'admin' => session('admin_user_name') ?? 'Social Case Study Officer',
        ]);

        return response()->json($activity);
    }

    public function getActivities()
    {
        $activities = SocialCaseActivityLog::orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json($activities);
    }

    public function clearActivities()
    {
        SocialCaseActivityLog::truncate();

        return response()->json(['message' => 'Activities cleared']);
    }
}
