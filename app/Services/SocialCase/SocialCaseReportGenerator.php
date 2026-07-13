<?php

namespace App\Services\SocialCase;

use App\Models\SocialCase\SocialCaseReport;
use App\Models\SocialCaseStudy;
use Illuminate\Support\Facades\DB;
use LogicException;

class SocialCaseReportGenerator
{
    public function generate(SocialCaseStudy $study, ?int $generatedBy): SocialCaseReport
    {
        if ($study->workflow_step !== 'report_generation') {
            throw new LogicException('The case must be at report generation before a report can be generated.');
        }

        $study->loadMissing('client.beneficiaryIntakes', 'familyMembers');
        $snapshot = $this->snapshot($study);
        $generatedAt = now();

        return DB::connection('mswdo_social_case')->transaction(function () use ($study, $generatedBy, $snapshot, $generatedAt) {
            $report = SocialCaseReport::on('mswdo_social_case')->firstOrNew([
                'case_number' => $study->case_number,
            ]);
            $report->fill([
                'social_case_study_id' => $study->id,
                'case_number' => $study->case_number,
                'title' => 'Social Case Study Report',
                'description' => $study->summary,
                'created_by' => $report->created_by ?? $generatedBy,
                'generated_at' => $generatedAt,
                'generated_by' => $generatedBy,
                'status' => 'draft',
                'body' => $this->body($snapshot),
                'snapshot' => $snapshot,
            ]);
            $report->save();

            $study->update([
                'report_generated' => true,
                'workflow_step' => 'print_export',
            ]);

            return $report;
        });
    }

    private function snapshot(SocialCaseStudy $study): array
    {
        $intake = $study->client?->beneficiaryIntakes?->firstWhere('social_case_study_id', $study->id);

        return [
            'case' => $study->only(['case_number', 'date_processed', 'service_provided', 'purpose', 'submitted_to', 'summary']),
            'client' => $study->client?->only(['first_name', 'middle_name', 'last_name', 'birthdate', 'gender', 'address']),
            'beneficiary' => $study->only(['beneficiary_last_name', 'beneficiary_first_name', 'beneficiary_middle_name', 'beneficiary_age', 'beneficiary_birthday', 'beneficiary_sex', 'beneficiary_barangay']),
            'intake' => $intake?->toArray(),
            'interview' => $study->only(['interview_date', 'interview_reason', 'interview_situation', 'interview_household', 'monthly_income', 'monthly_expenses', 'family_illnesses', 'previous_assistance', 'interview_notes']),
            'family' => $study->familyMembers->map->toArray()->all(),
            'assessment' => $study->only(['social_worker_assessment', 'recommendation', 'recommended_amount']),
        ];
    }

    private function body(array $snapshot): string
    {
        $client = collect($snapshot['client'] ?? [])->filter()->implode(' ');
        $assessment = $snapshot['assessment']['social_worker_assessment'] ?? '';
        return "SOCIAL CASE STUDY REPORT\n\nCase No.: ".($snapshot['case']['case_number'] ?? '')."\nClient: {$client}\n\nAssessment:\n{$assessment}";
    }
}
