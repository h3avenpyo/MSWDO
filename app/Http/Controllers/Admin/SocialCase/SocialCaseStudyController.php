<?php

namespace App\Http\Controllers\Admin\SocialCase;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialCase\StoreSocialCaseStudyRequest;
use App\Models\AssistanceRecord;
use App\Models\Client;
use App\Models\BeneficiaryIntake;
use App\Models\EligibilityAuditLog;
use App\Models\SocialCaseStudy;
use App\Models\SocialCase\SocialCaseReport;
use App\Models\SocialCase\SocialCaseReportReleaseLog;
use App\Services\SocialCase\EligibilityChecker;
use App\Services\SocialCase\CaseWorkflowService;
use App\Services\SocialCase\SocialCaseReportGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

    public function create(Request $request, Client $client, EligibilityChecker $checker)
    {
        $eligibility = $this->checkEligibility($client, $checker);

        if (! $eligibility['eligible']) {
            return $this->ineligibleRedirect($client, $eligibility);
        }

        if (! $request->filled('intake')) {
            return redirect()
                ->route('admin.beneficiary-intake.create', $client)
                ->with('error', 'Complete the beneficiary intake before starting the case study requirements.');
        }

        $intake = BeneficiaryIntake::on('mswdo_social_case')
            ->whereKey($request->integer('intake'))
            ->where('client_id', $client->id)
            ->firstOrFail();

        $study = SocialCaseStudy::on('mswdo_social_case')
            ->where('client_id', $client->id)
            ->where('status', '!=', 'Closed')
            ->latest()
            ->first();

        if (! $study) {
            $study = SocialCaseStudy::on('mswdo_social_case')->create([
                'client_id' => $client->id,
                'officer_id' => session('admin_user_id'),
                'case_number' => $this->generateCaseNumber($client),
                'workflow_step' => 'requirements_verification',
                'status' => 'Open',
                // Keep existing installations with legacy NOT NULL columns
                // working while the draft-nullability migration is deployed.
                'date_processed' => $intake->date_processed ?? now(),
                'client_last_name' => $intake->client_last_name ?? $client->last_name,
                'client_first_name' => $intake->client_first_name ?? $client->first_name,
                'client_middle_name' => $intake->client_middle_name ?? $client->middle_name,
                'client_age' => $intake->client_age ?? $client->birthdate?->age,
                'client_sex' => $intake->client_sex ?? $client->gender,
                'client_barangay' => $intake->client_barangay,
                'service_provided' => $intake->service_provided,
                'purpose' => $intake->purpose,
                'submitted_to' => $intake->submitted_to,
                'encoded_by' => $intake->encoder ?? session('admin_user_name'),
            ]);
        }
        $intake->update(['social_case_study_id' => $study->id]);

        return redirect()->route('admin.social-case-studies.step.show', [$study, $study->workflow_step]);
    }

    public function store(StoreSocialCaseStudyRequest $request, Client $client, EligibilityChecker $checker, CaseWorkflowService $workflow)
    {
        $eligibility = $this->checkEligibility($client, $checker);

        if (! $eligibility['eligible']) {
            return $this->ineligibleRedirect($client, $eligibility);
        }

        $intake = $request->filled('beneficiary_intake_id')
            ? BeneficiaryIntake::on('mswdo_social_case')
                ->whereKey($request->integer('beneficiary_intake_id'))
                ->where('client_id', $client->id)
                ->first()
            : null;

        if (! $intake) {
            return redirect()
                ->route('admin.beneficiary-intake.create', $client)
                ->with('error', 'A beneficiary intake is required before creating a social case study.');
        }

        $officerId = session('admin_user_id');

        $study = SocialCaseStudy::on('mswdo_social_case')->create([
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
            'evaluation_complete' => false,
            'service_provided' => $request->service_provided,
            'purpose' => $request->purpose,
            'submitted_to' => $request->submitted_to,
            'encoded_by' => $request->encoded_by,
            'status' => $request->status,
            'workflow_step' => $workflow->initialStep($intake !== null),
            'summary' => $request->summary,
            'report_generated' => false,
            'assistance_released' => $request->has('assistance_released'),
            'assistance_amount' => $request->assistance_amount,
            'assistance_date' => $request->assistance_date,
        ]);

        $intake->update(['social_case_study_id' => $study->id]);

        $this->logEligibilityResult($client, $eligibility, $officerId);

        return redirect()->route('admin.social-case-studies.index')->with('success', 'Social case study created successfully.');
    }

    public function edit(SocialCaseStudy $socialCaseStudy)
    {
        return redirect()->route('admin.social-case-studies.step.show', [$socialCaseStudy, $socialCaseStudy->workflow_step]);
    }

    public function showStep(SocialCaseStudy $socialCaseStudy, string $step)
    {
        $workflow = new CaseWorkflowService($socialCaseStudy);
        if ($step !== $workflow->currentStep()) {
            return redirect()->route('admin.social-case-studies.step.show', [$socialCaseStudy, $workflow->currentStep()])
                ->with('info', 'You have been returned to the current workflow step.');
        }
        $socialCaseStudy->load('client.beneficiaryIntakes', 'familyMembers');

        return view('admin.social-case-eligibility.case-studies.step', [
            'socialCaseStudy' => $socialCaseStudy,
            'step' => $step,
            'steps' => array_slice(CaseWorkflowService::STEPS, 3),
        ]);
    }

    public function saveStep(Request $request, SocialCaseStudy $socialCaseStudy, string $step)
    {
        abort_if($step === 'supervisor_review' && ! str_contains(strtolower((string) session('admin_user_role')), 'supervisor'), 403, 'Only supervisors may complete this workflow step.');
        $workflow = new CaseWorkflowService($socialCaseStudy);
        $this->guardStep($workflow, $step);
        abort_if(in_array($step, ['report_generation', 'release_report'], true), 405, 'Use the dedicated report action for this workflow step.');
        abort_if($step === 'assistance_release' && $socialCaseStudy->released_at === null, 403, 'Release the report before recording assistance release.');
        $requestClass = $this->stepRequestClass($step);
        require_once app_path('Http/Requests/SocialCase/CaseStepRequests.php');
        /** @var \App\Http\Requests\SocialCase\CaseStepRequest $formRequest */
        $formRequest = new $requestClass;
        $validated = Validator::make($request->all(), $formRequest->rules())->validate();
        
        if ($step === 'requirements_verification') {
            $validated['requirements_complete'] = $request->has('requirements_complete');
        } else {
            foreach (['requirements_complete', 'interview_complete', 'evaluation_complete', 'assistance_released'] as $field) {
                if (array_key_exists($field, $validated)) $validated[$field] = true;
            }
        }

        if ($step === 'family_composition') {
            $members = $validated['family_members'];
            unset($validated['family_members']);
            $socialCaseStudy->familyMembers()->delete();
            $socialCaseStudy->familyMembers()->createMany(array_map(function (array $member) {
                $member['is_dependent'] = (bool) ($member['is_dependent'] ?? false);
                return $member;
            }, $members));
        }
        $socialCaseStudy->update($validated);

        if ($step === 'requirements_verification') {
            if (!$validated['requirements_complete']) {
                $socialCaseStudy->update(['status' => 'Waiting for Requirements']);
                return redirect()->route('admin.social-case-studies.index')
                    ->with('success', 'Case study saved as Incomplete (Waiting for Requirements).');
            } else {
                $socialCaseStudy->update(['status' => 'In Progress']);
            }
        }

        if ($step === 'social_case_assessment' && ($validated['recommendation'] ?? null) === 'Not Qualified') {
            $socialCaseStudy->update([
                'status' => 'Closed',
                'workflow_step' => 'case_closed',
            ]);

            return redirect()->route('admin.social-case-studies.step.show', [$socialCaseStudy, 'case_closed'])
                ->with('info', 'Case marked not qualified and moved to closure.');
        }
        if ($step === 'assistance_release') {
            AssistanceRecord::on('mswdo_social_case')->updateOrCreate(
                [
                    'client_id' => $socialCaseStudy->client_id,
                    'remarks' => 'Released through Social Case Study '.$socialCaseStudy->case_number,
                ],
                [
                    'assistance_type' => $socialCaseStudy->service_provided ?? 'Social Case Study Assistance',
                    'status' => 'Released',
                    'release_date' => $validated['assistance_date'],
                    'amount' => $validated['assistance_amount'],
                ]
            );
        }
        $next = $workflow->getNextStep();
        if ($next) $workflow->advanceTo($next);

        return redirect()->route('admin.social-case-studies.step.show', [$socialCaseStudy, $next ?? $step])
            ->with('success', $next ? 'Draft saved. Continue to the next step.' : 'Case closed successfully.');
    }

    public function generateReport(SocialCaseStudy $socialCaseStudy, SocialCaseReportGenerator $generator)
    {
        $workflow = new CaseWorkflowService($socialCaseStudy);
        if ($workflow->currentStep() !== 'report_generation') {
            if ($socialCaseStudy->report_generated && $socialCaseStudy->report()->exists()) {
                return redirect()->route('admin.social-case-studies.reports.preview', $socialCaseStudy)
                    ->with('info', 'This report has already been generated.');
            }

            return redirect()->route('admin.social-case-studies.step.show', [$socialCaseStudy, $workflow->currentStep()])
                ->with('info', 'You have been returned to the current workflow step.');
        }

        try {
            $report = $generator->generate($socialCaseStudy, session('admin_user_id'));
        } catch (\LogicException $exception) {
            return redirect()->route('admin.social-case-studies.index')->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.social-case-studies.reports.preview', $socialCaseStudy)
            ->with('success', 'Social Case Study Report generated successfully.');
    }

    public function previewReport(SocialCaseStudy $socialCaseStudy)
    {
        $report = $this->generatedReport($socialCaseStudy);

        return view('admin.social-case-eligibility.case-studies.report-preview', compact('socialCaseStudy', 'report'));
    }

    public function streamReportPdf(SocialCaseStudy $socialCaseStudy)
    {
        $report = $this->generatedReport($socialCaseStudy);

        return $this->makeReportPdf($socialCaseStudy, $report)->stream($this->reportFilename($report));
    }

    public function downloadReportPdf(SocialCaseStudy $socialCaseStudy)
    {
        $report = $this->generatedReport($socialCaseStudy);

        return $this->makeReportPdf($socialCaseStudy, $report)->download($this->reportFilename($report));
    }

    public function releaseReport(Request $request, SocialCaseStudy $socialCaseStudy)
    {
        $workflow = new CaseWorkflowService($socialCaseStudy);
        $this->guardStep($workflow, 'release_report');
        $report = $this->generatedReport($socialCaseStudy);
        $data = $request->validate(['released_to' => ['required', 'string', 'max:255']]);
        $releasedAt = now();

        DB::connection('mswdo_social_case')->transaction(function () use ($request, $socialCaseStudy, $workflow, $report, $data, $releasedAt) {
            $socialCaseStudy->update([
                'released_at' => $releasedAt,
                'released_by' => session('admin_user_id'),
                'released_to' => $data['released_to'],
            ]);
            SocialCaseReportReleaseLog::on('mswdo_social_case')->create([
                'social_case_study_id' => $socialCaseStudy->id,
                'social_case_report_id' => $report->id,
                'released_by' => session('admin_user_id'),
                'released_by_name' => session('admin_user_name'),
                'released_to' => $data['released_to'],
                'released_at' => $releasedAt,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            $workflow->advanceTo('assistance_release');
        });

        return redirect()->route('admin.social-case-studies.step.show', [$socialCaseStudy, 'assistance_release'])
            ->with('success', 'Report released and recorded. You may now process assistance release.');
    }

    public function update(StoreSocialCaseStudyRequest $request, SocialCaseStudy $socialCaseStudy)
    {
        abort(403, 'Use the step workflow to update social case studies.');
    }

    public function destroy(SocialCaseStudy $socialCaseStudy)
    {
        abort_unless(
            $socialCaseStudy->workflow_step === 'requirements_verification'
            && $socialCaseStudy->status !== 'Closed'
            && ! $socialCaseStudy->report_generated
            && $socialCaseStudy->released_at === null,
            403,
            'Only unreleased draft cases may be deleted.'
        );

        $socialCaseStudy->delete();

        return redirect()->route('admin.social-case-studies.index')->with('success', 'Social case study deleted successfully.');
    }

    private function generateCaseNumber(Client $client): string
    {
        $date = now()->format('Ymd');
        $count = SocialCaseStudy::on('mswdo_social_case')->whereDate('created_at', now()->toDateString())->count() + 1;

        return sprintf('SCS-%s-%s-%04d', $date, $client->id, $count);
    }

    private function guardStep(CaseWorkflowService $workflow, string $step): void
    {
        if ($step !== $workflow->currentStep()) {
            abort(403, 'Complete the current workflow step before continuing.');
        }
    }

    private function stepRequestClass(string $step): string
    {
        $requests = [
            'requirements_verification' => 'RequirementsStepRequest', 'assessment_interview' => 'InterviewStepRequest',
            'family_composition' => 'FamilyStepRequest', 'social_case_assessment' => 'AssessmentStepRequest',
            'report_generation' => 'ReportStepRequest',
            'print_export' => 'ExportStepRequest', 'release_report' => 'ReleaseReportStepRequest',
            'assistance_release' => 'AssistanceStepRequest', 'case_closed' => 'CloseStepRequest',
        ];
        abort_unless(isset($requests[$step]), 404);
        return 'App\\Http\\Requests\\SocialCase\\'.$requests[$step];
    }

    private function generatedReport(SocialCaseStudy $study): SocialCaseReport
    {
        $report = $study->report()->first();

        abort_unless($study->report_generated && $report !== null, 403, 'Generate the Social Case Study Report before printing or exporting it.');

        return $report;
    }

    private function makeReportPdf(SocialCaseStudy $study, SocialCaseReport $report)
    {
        return Pdf::loadView('admin.social-case-eligibility.case-studies.report-pdf', compact('study', 'report'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', false)
            ->setOption('enable_php', false)
            ->setOption('enable_javascript', false);
    }

    private function reportFilename(SocialCaseReport $report): string
    {
        return 'social-case-study-'.str($report->case_number)->slug().'.pdf';
    }

    private function checkEligibility(Client $client, EligibilityChecker $checker): array
    {
        $start = microtime(true);
        $result = $checker->check($client);
        $result['duration_ms'] = (int) round((microtime(true) - $start) * 1000);

        return $result;
    }

    private function ineligibleRedirect(Client $client, array $eligibility): RedirectResponse
    {
        $message = 'This client is not eligible for a new social case study because they received assistance within the last six months.';

        if ($eligibility['eligibleAgainDate'] !== null) {
            $message .= ' They may be eligible again on '.$eligibility['eligibleAgainDate']->format('M d, Y').'.';
        }

        return redirect()
            ->route('admin.social-case-eligibility.show', $client)
            ->with('error', $message);
    }

    private function logEligibilityResult(Client $client, array $eligibility, ?int $officerId): void
    {
        EligibilityAuditLog::on('mswdo_social_case')->create([
            'client_id' => $client->id,
            'client_name' => $client->full_name,
            'officer_id' => $officerId,
            'officer_name' => session('admin_user_name'),
            'result' => 'eligible',
            'result_details' => 'Eligibility confirmed before social case study creation.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'search_duration_ms' => $eligibility['duration_ms'],
        ]);
    }

    public function downloadReportWord(SocialCaseStudy $socialCaseStudy)
    {
        $report = $this->generatedReport($socialCaseStudy);
        $snapshot = $report->snapshot ?? [];
        $filename = 'social-case-study-' . str($report->case_number)->slug() . '.doc';

        $html = view('admin.social-case-eligibility.case-studies.report-word', compact('socialCaseStudy', 'report', 'snapshot'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-word',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ]);
    }
}
