<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Models\SocialCase\SocialCaseStudy;
use App\Models\SocialCase\SocialCaseActivityLog;
use App\Models\SocialCase\EligibilityAuditLog;
use App\Models\Client;
use App\Models\User;
use App\Services\SocialCase\EligibilityChecker;
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
        return view('admin.social-case.dashboard', compact('justLoggedIn'));
    }

    public function socialCaseNew()
    {
        $role = (string) session('admin_user_role');
        $canCheckEligibility = in_array($role, ['eligibility_checker', 'admin'], true);
        $canEncode = in_array($role, ['social_worker', 'admin'], true);

        return view('admin.social-case.new', compact('canCheckEligibility', 'canEncode'));
    }

    public function socialCaseIntake()
    {
        $role = (string) session('admin_user_role');
        $canEncode = in_array($role, ['social_worker', 'admin'], true);

        return view('admin.social-case.intake', compact('canEncode'));
    }

    public function socialCaseArchive()
    {
        return view('admin.social-case.archive');
    }

    public function socialCaseCases()
    {
        return view('admin.social-case.cases');
    }

    /**
     * Clients forwarded by the eligibility checker and waiting to be encoded.
     */
    public function socialCaseSubmitted()
    {
        $submitted = SocialCaseStudy::with('client', 'eligibleByUser')
            ->where('eligibility_status', 'eligible')
            ->whereNotNull('eligible_by')
            ->where('status', 'Draft')
            ->orderByDesc('eligible_at')
            ->get();

        return view('admin.social-case.submitted', compact('submitted'));
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

    public function getCase($id)
    {
        $case = SocialCaseStudy::with('client', 'officer', 'encoder', 'eligibleByUser', 'releasedByUser', 'interview', 'familyMembers')->find($id);
        if (!$case) {
            return response()->json(['error' => 'Case not found'], 404);
        }
        return response()->json($case);
    }

    /**
     * Server-side eligibility check for a client (6-month assistance rule).
     * Restricted to eligibility checker role via route middleware.
     */
    public function checkEligibility(Request $request, EligibilityChecker $checker)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
        ]);

        $name = trim($request->input('client_name'));
        $nameParts = array_filter(array_map('trim', explode(' ', $name)));
        $firstName = array_shift($nameParts) ?? '';
        $lastName = array_pop($nameParts) ?? '';

        $client = Client::whereRaw('LOWER(first_name) = ? AND LOWER(last_name) = ?', [
            strtolower($firstName),
            strtolower($lastName),
        ])->first();

        $start = microtime(true);

        $result = [
            'eligible' => true,
            'client_found' => (bool) $client,
            'client' => $client,
            'eligible_again_date' => null,
            'last_assistance_date' => null,
            'blocking' => null,
            'existing_case' => null,
        ];

        if ($client) {
            $checkResult = $checker->check($client);

            $result['eligible'] = $checkResult['eligible'];
            $result['eligible_again_date'] = $checkResult['eligibleAgainDate']?->toDateString();
            $result['last_assistance_date'] = $checkResult['lastAssistanceDate']?->toDateString();
            $result['blocking'] = $checkResult['blockingRecord'] ? [
                'assistance_type' => $checkResult['blockingRecord']->assistance_type,
                'release_date' => $checkResult['blockingRecord']->release_date?->toDateString(),
            ] : null;

            $activeCase = $client->socialCaseStudies()
                ->where('status', '!=', 'Archived')
                ->orderByDesc('created_at')
                ->first();

            if ($activeCase) {
                $result['existing_case'] = [
                    'id' => $activeCase->id,
                    'case_number' => $activeCase->case_number,
                    'status' => $activeCase->status,
                    'eligibility_status' => $activeCase->eligibility_status,
                ];
            }
        }

        EligibilityAuditLog::create([
            'client_id' => $client?->id,
            'client_name' => $name,
            'officer_id' => session('admin_user_id'),
            'officer_name' => session('admin_user_name') ?? 'Eligibility Checker',
            'result' => $result['eligible'] ? 'eligible' : 'ineligible',
            'result_details' => $result['eligible']
                ? 'Client is eligible for assistance.'
                : 'Client is within the 6-month assistance restriction period.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'search_duration_ms' => (int) round((microtime(true) - $start) * 1000),
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
            'override' => 'sometimes|boolean',
        ]);

        $name = trim($request->input('client_name'));
        $override = $request->boolean('override');
        $clientId = $this->findOrCreateClient(['name' => $name]);
        $client = \App\Models\Client::find($clientId);

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

            return $case;
        });

        return response()->json($case->load('client'), 201);
    }

    private function findOrCreateClient(array $clientData): int
    {
        $fullName = trim($clientData['name'] ?? '');
        $nameParts = array_filter(array_map('trim', explode(' ', $fullName)));
        $firstName = array_shift($nameParts) ?? '';
        $lastName  = array_pop($nameParts) ?? '';
        $middleName = implode(' ', $nameParts);

        $client = Client::whereRaw('LOWER(first_name) = ? AND LOWER(last_name) = ?', [
            strtolower($firstName),
            strtolower($lastName),
        ])->first();

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
