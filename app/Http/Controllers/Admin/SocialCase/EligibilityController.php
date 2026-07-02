<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialCase\ClientSearchRequest;
use App\Http\Requests\SocialCase\StoreClientRequest;
use App\Models\Client;
use App\Models\EligibilityAuditLog;
use App\Models\User;
use App\Services\SocialCase\EligibilityChecker;
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
            'averageSearchTimeMs' => (int) EligibilityAuditLog::on('mswdo_social_case')
                ->whereDate('created_at', $today->toDateString())
                ->avg('search_duration_ms'),
            'recentChecks' => EligibilityAuditLog::on('mswdo_social_case')
                ->latest()
                ->limit(6)
                ->get(),
        ];

        return view('admin.social-case-eligibility.index', compact('metrics'));
    }

    public function search(ClientSearchRequest $request)
    {
        $query = Client::on('mswdo_social_case')->query();

        if ($request->filled('client_id')) {
            $query->where('id', $request->client_id);
        }

        if ($request->filled('full_name')) {
            $name = strtolower(trim($request->full_name));
            $query->whereRaw("LOWER(CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)) LIKE ?", ["%{$name}%"]);
        }

        if ($request->filled('birthdate')) {
            $query->whereDate('birthdate', $request->birthdate);
        }

        $clients = $query->limit(12)->get();

        return response()->json([ 'clients' => $clients ]);
    }

    public function show(Client $client, EligibilityChecker $checker)
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

        return view('admin.social-case-eligibility.show', $result);
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
}
