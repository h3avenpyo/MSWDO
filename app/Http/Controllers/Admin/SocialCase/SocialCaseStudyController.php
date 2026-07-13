<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialCase\StoreSocialCaseStudyRequest;
use App\Models\Client;
use App\Models\SocialCaseStudy;
use Illuminate\Http\Request;

class SocialCaseStudyController extends Controller
{
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
            'date_processed' => $request->date_processed,
            'client_last_name' => $request->client_last_name,
            'client_first_name' => $request->client_first_name,
            'client_middle_name' => $request->client_middle_name,
            'client_age' => $request->client_age,
            'client_sex' => $request->client_sex,
            'client_barangay' => $request->client_barangay,
            'beneficiary_last_name' => $request->beneficiary_last_name,
            'beneficiary_first_name' => $request->beneficiary_first_name,
            'beneficiary_middle_name' => $request->beneficiary_middle_name,
            'beneficiary_age' => $request->beneficiary_age,
            'beneficiary_birthday' => $request->beneficiary_birthday,
            'beneficiary_sex' => $request->beneficiary_sex,
            'beneficiary_barangay' => $request->beneficiary_barangay,
            'medical_conditions' => $request->medical_conditions,
            'additional_requirements' => $request->additional_requirements,
            'requirements_complete' => $request->has('requirements_complete'),
            'interview_date' => $request->interview_date,
            'interview_reason' => $request->interview_reason,
            'interview_situation' => $request->interview_situation,
            'interview_household' => $request->interview_household,
            'monthly_income' => $request->monthly_income,
            'monthly_expenses' => $request->monthly_expenses,
            'family_illnesses' => $request->family_illnesses,
            'previous_assistance' => $request->previous_assistance,
            'interview_notes' => $request->interview_notes,
            'interview_complete' => $request->has('interview_complete'),
            'social_worker_assessment' => $request->social_worker_assessment,
            'recommendation' => $request->recommendation,
            'recommended_amount' => $request->recommended_amount,
            'supervisor_notes' => $request->supervisor_notes,
            'evaluation_complete' => $request->has('evaluation_complete'),
            'service_provided' => $request->service_provided,
            'purpose' => $request->purpose,
            'submitted_to' => $request->submitted_to,
            'encoded_by' => $request->encoded_by,
            'status' => $request->status,
            'workflow_step' => 'assistance_release',
            'summary' => $request->summary,
            'report_generated' => $request->has('report_generated'),
            'assistance_released' => $request->has('assistance_released'),
            'assistance_amount' => $request->assistance_amount,
            'assistance_date' => $request->assistance_date,
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
