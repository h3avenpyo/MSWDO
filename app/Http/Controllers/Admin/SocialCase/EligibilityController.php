<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialCase\ClientSearchRequest;
use App\Http\Requests\SocialCase\StoreClientRequest;
use App\Models\Client;
use App\Models\EligibilityAuditLog;
use App\Models\SocialCaseStudy;
use App\Models\User;
use App\Services\SocialCase\CaseRejectionRecorder;
use App\Services\SocialCase\EligibilityChecker;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EligibilityController extends Controller
{
    public function index()
    {
        $today = now();
        $metrics = [
            'checksToday' => EligibilityAuditLog::on('mswdo_social_case')
                ->whereDate('created_at', $today->toDateString())
                ->count(),
            'eligibleToday' => EligibilityAuditLog::on('mswdo_social_case')
                ->whereDate('created_at', $today->toDateString())
                ->where('result', 'eligible')
                ->count(),
            'notEligibleToday' => EligibilityAuditLog::on('mswdo_social_case')
                ->whereDate('created_at', $today->toDateString())
                ->where('result', 'not_eligible')
                ->count(),
            'waitingRequirements' => SocialCaseStudy::on('mswdo_social_case')
                ->where('status', 'Waiting for Requirements')
                ->count(),
            'recentChecks' => EligibilityAuditLog::on('mswdo_social_case')
                ->latest()
                ->limit(6)
                ->get(),
        ];

        return view('admin.social-case-eligibility.index', compact('metrics'));
    }

    public function search(ClientSearchRequest $request)
    {
        $search = $request->input('query');
        $query = Client::on('mswdo_social_case');

        $query->where(function ($q) use ($search) {
            $q->whereRaw('LOWER(first_name) LIKE ?', ['%'.mb_strtolower($search).'%'])
              ->orWhereRaw('LOWER(last_name) LIKE ?', ['%'.mb_strtolower($search).'%'])
              ->orWhereRaw('LOWER(address) LIKE ?', ['%'.mb_strtolower($search).'%'])
              ->orWhere('id', 'like', '%'.$search.'%')
              ->orWhere('contact_number', 'like', '%'.$search.'%');
        });

        $clients = $query->with(['assistanceRecords' => function ($assistanceQuery) {
            $assistanceQuery->orderByDesc('release_date');
        }])->limit(12)->get()->map(function (Client $client) {
            $lastRequest = $client->assistanceRecords->first();

            return [
                'id' => $client->id,
                'first_name' => $client->first_name,
                'middle_name' => $client->middle_name,
                'last_name' => $client->last_name,
                'full_name' => $client->full_name,
                'address' => $client->address,
                'contact_number' => $client->contact_number,
                'last_request_date' => $lastRequest?->release_date?->format('M d, Y'),
                'last_assistance_type' => $lastRequest?->assistance_type,
            ];
        })->values();

        return response()->json([ 'clients' => $clients ]);
    }

    public function show(Client $client, EligibilityChecker $checker, CaseRejectionRecorder $rejections)
    {
        $start = microtime(true);
        $result = $checker->check($client);
        $duration = (int) round((microtime(true) - $start) * 1000);

        $officerName = session('admin_user_name');
        $officerId = session('admin_user_id');

        EligibilityAuditLog::on('mswdo_social_case')->create([
            'client_id' => $client->id,
            'client_name' => $client->full_name,
            'officer_id' => $officerId,
            'officer_name' => $officerName,
            'result' => $result['eligible'] ? 'eligible' : 'not_eligible',
            'result_details' => $result['eligible']
                ? 'No approved or released assistance in the last 6 months.'
                : sprintf('Last approved/released assistance on %s', $result['blockingRecord']->release_date->toDateString()),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'search_duration_ms' => $duration,
        ]);

        if (! $result['eligible']) {
            $rejections->record($client, $result);
        }

        return view('admin.social-case-eligibility.show', $result);
    }

    public function checkEligibility(Client $client, EligibilityChecker $checker, CaseRejectionRecorder $rejections)
    {
        $result = $checker->check($client);

        if (! $result['eligible']) {
            $rejections->record($client, $result);
        }

        return response()->json([
            'eligible' => $result['eligible'],
            'reason' => $result['eligible']
                ? 'No approved or released assistance in the last 6 months.'
                : 'This client has already received assistance within the last six (6) months.',
            'assistance_date' => $result['lastAssistanceDate']?->format('M d, Y') ?? 'None',
            'assistance_type' => $result['latestAssistance']?->assistance_type ?? 'None',
            'next_eligible_date' => $result['eligibleAgainDate']?->format('M d, Y') ?? 'N/A',
        ]);
    }

    public function reject(Client $client, EligibilityChecker $checker, CaseRejectionRecorder $rejections)
    {
        $result = $checker->check($client);

        if ($result['eligible']) {
            return redirect()
                ->route('admin.social-case-eligibility.show', $client)
                ->with('error', 'This client is currently eligible and cannot be rejected.');
        }

        $rejections->record($client, $result, close: true);

        return redirect()
            ->route('admin.social-case')
            ->with('success', 'Eligibility rejection recorded and case closed.');
    }

    public function createRegistration()
    {
        return view('admin.social-case-eligibility.register');
    }

    public function storeClient(StoreClientRequest $request)
    {
        $client = Client::on('mswdo_social_case')->create($request->validated());

        return redirect()->route('admin.social-case-eligibility.show', $client->id);
    }

    public function downloadRejectionLetter(Client $client, EligibilityChecker $checker)
    {
        $result = $checker->check($client);

        if ($result['eligible']) {
            return redirect()
                ->route('admin.social-case-eligibility.show', $client)
                ->with('error', 'This client is currently eligible and does not have an ineligibility notice.');
        }

        $pdf = Pdf::loadView('admin.social-case-eligibility.rejection-letter', [
            'client' => $client,
            'result' => $result,
            'officer_name' => session('admin_user_name') ?? 'Social Worker Officer',
        ])
        ->setPaper('a4', 'portrait')
        ->setOption('isRemoteEnabled', false)
        ->setOption('enable_php', false)
        ->setOption('enable_javascript', false);

        return $pdf->download('ineligibility-notice-' . str($client->full_name)->slug() . '.pdf');
    }
}
