<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialCase\StoreSocialCaseStudyRequest;
use App\Models\Client;
use App\Models\SocialCaseStudy;
use Illuminate\Http\Request;

class SocialCaseStudyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! session('admin_user_id')) {
                return redirect()->route('admin.login.form');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $studies = SocialCaseStudy::on('mswdo_social_case')
            ->with('client')
            ->latest()
            ->paginate(12);

        return view('admin.social-case-eligibility.case-studies.index', compact('studies'));
    }

    public function create(Client $client)
    {
        return view('admin.social-case-eligibility.case-studies.create', compact('client'));
    }

    public function store(StoreSocialCaseStudyRequest $request, Client $client)
    {
        $officerId = session('admin_user_id');

        SocialCaseStudy::on('mswdo_social_case')->create([
            'client_id' => $client->id,
            'officer_id' => $officerId,
            'case_number' => $this->generateCaseNumber($client),
            'status' => $request->status,
            'summary' => $request->summary,
            'interview_date' => $request->interview_date,
        ]);

        return redirect()->route('admin.social-case-studies.index')->with('success', 'Social case study created successfully.');
    }

    public function edit(SocialCaseStudy $socialCaseStudy)
    {
        $socialCaseStudy->load('client');

        return view('admin.social-case-eligibility.case-studies.edit', compact('socialCaseStudy'));
    }

    public function update(StoreSocialCaseStudyRequest $request, SocialCaseStudy $socialCaseStudy)
    {
        $socialCaseStudy->update($request->validated());

        return redirect()->route('admin.social-case-studies.index')->with('success', 'Social case study updated successfully.');
    }

    public function destroy(SocialCaseStudy $socialCaseStudy)
    {
        $socialCaseStudy->delete();

        return redirect()->route('admin.social-case-studies.index')->with('success', 'Social case study deleted successfully.');
    }

    private function generateCaseNumber(Client $client): string
    {
        $date = now()->format('Ymd');
        $count = SocialCaseStudy::on('mswdo_social_case')->whereDate('created_at', now()->toDateString())->count() + 1;

        return sprintf('SCS-%s-%s-%04d', $date, $client->id, $count);
    }
}
