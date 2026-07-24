<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Models\SocialCase\SocialCaseStudy;
use App\Models\Client;
use App\Models\User;
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
        return view('admin.social-case.dashboard');
    }

    public function socialCaseNew()
    {
        return view('admin.social-case.new');
    }

    public function socialCaseIntake()
    {
        return view('admin.social-case.intake');
    }

    public function socialCaseArchive()
    {
        return view('admin.social-case.archive');
    }

    public function socialCaseCases()
    {
        return view('admin.social-case.cases');
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
        $cases = SocialCaseStudy::with('client', 'officer', 'encoder', 'releasedByUser', 'interview', 'familyMembers')
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
        $case = SocialCaseStudy::with('client', 'officer', 'encoder', 'releasedByUser', 'interview', 'familyMembers')->find($id);
        if (!$case) {
            return response()->json(['error' => 'Case not found'], 404);
        }
        return response()->json($case);
    }

    public function storeCase(Request $request)
    {
        $data = $request->validate([
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
        $caseNumber = $this->generateCaseNumber();

        $case = DB::transaction(function () use ($data, $clientId, $agencies, $caseNumber) {
            $case = SocialCaseStudy::create([
                'client_id'          => $clientId,
                'officer_id'         => session('admin_user_id'),
                'case_number'        => $caseNumber,
                'date_processed'     => now()->toDateString(),
                'interview_date'     => $data['interview']['report_date'] ?? null,
                'purpose'            => $data['purpose'],
                'submitted_to'       => implode(', ', $agencies),
                'encoded_by'         => session('admin_user_id'),
                'status'             => $data['status'],
                'summary'            => $data['interview']['problem_presented'] ?? null,
                'workflow_step'       => 'requirements_verification',
                'requirements_complete' => !empty($data['requirements']),
                'signers'            => $data['signers'] ?? [],
            ]);

            $interview = $data['interview'];
            \App\Models\SocialCase\CaseInterview::create([
                'social_case_study_id'    => $case->id,
                'interview_reason'        => $data['purpose'],
                'interview_situation'     => $interview['problem_presented'] ?? null,
                'interview_household'     => $interview['home_condition'] ?: null,
                'monthly_income'          => null,
                'monthly_expenses'        => null,
                'interview_notes'         => $interview['socio_economic'] ?: null,
                'social_worker_assessment' => $interview['evaluation'] ?: null,
                'recommendation'          => $interview['recommendation'] ?: null,
            ]);

            $household = $data['household'] ?? [];
            foreach ($household as $member) {
                if (empty($member['name'])) continue;
                \App\Models\SocialCase\FamilyMember::create([
                    'social_case_study_id' => $case->id,
                    'full_name'            => $member['name'] ?? '',
                    'relationship'         => $member['relationship'] ?? '',
                    'age'                  => is_numeric($member['age'] ?? null) ? (int) $member['age'] : null,
                    'education'            => $member['education'] ?: null,
                    'occupation'           => $member['occupation'] ?: null,
                    'monthly_income'       => is_numeric($member['income'] ?? null) ? $member['income'] : null,
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
        $case = SocialCaseStudy::find($id);
        if (!$case) {
            return response()->json(['error' => 'Case not found'], 404);
        }

        $case->delete();
        return response()->json(['message' => 'Case deleted successfully']);
    }
}
