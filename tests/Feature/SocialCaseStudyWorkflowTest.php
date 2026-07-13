<?php

namespace Tests\Feature;

use App\Models\BeneficiaryIntake;
use App\Models\Client;
use App\Models\SocialCaseStudy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialCaseStudyWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->cleanSocialCaseTables();
    }

    #[Test]
    public function happy_path_completes_social_case_study_workflow(): void
    {
        $this->adminSession();
        $client = $this->client();

        $this->postJson(route('admin.social-case-eligibility.search'), [
            'query' => 'Juan',
        ])->assertOk()->assertJsonPath('clients.0.id', $client->id);

        $this->get(route('admin.social-case-eligibility.check', $client))
            ->assertOk()
            ->assertJsonPath('eligible', true);

        $intake = $this->intake($client);

        $this->get(route('admin.social-case-studies.create', ['client' => $client, 'intake' => $intake->id]))
            ->assertRedirect();

        $study = SocialCaseStudy::on('mswdo_social_case')->where('client_id', $client->id)->firstOrFail();
        $this->assertSame('requirements_verification', $study->workflow_step);

        $this->post(route('admin.social-case-studies.step.save', [$study, 'requirements_verification']), $this->requirementsPayload())
            ->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'assessment_interview']));

        $this->post(route('admin.social-case-studies.step.save', [$study, 'assessment_interview']), $this->interviewPayload())
            ->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'family_composition']));

        $this->post(route('admin.social-case-studies.step.save', [$study, 'family_composition']), $this->familyPayload())
            ->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'social_case_assessment']));

        $this->post(route('admin.social-case-studies.step.save', [$study, 'social_case_assessment']), $this->assessmentPayload())
            ->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'report_generation']));

        $this->post(route('admin.social-case-studies.reports.generate', $study))
            ->assertRedirect(route('admin.social-case-studies.reports.preview', $study));

        $study->refresh();
        $this->assertTrue($study->report_generated);
        $this->assertSame('print_export', $study->workflow_step);

        $this->get(route('admin.social-case-studies.reports.pdf', $study))->assertOk();

        $this->post(route('admin.social-case-studies.step.save', [$study, 'print_export']))
            ->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'release_report']));

        $this->post(route('admin.social-case-studies.reports.release', $study), ['released_to' => 'Juan Dela Cruz'])
            ->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'assistance_release']));

        $this->post(route('admin.social-case-studies.step.save', [$study, 'assistance_release']), [
            'assistance_amount' => 2500,
            'assistance_date' => '2026-07-13',
            'assistance_released' => '1',
        ])->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'case_closed']));

        $this->post(route('admin.social-case-studies.step.save', [$study, 'case_closed']), [
            'status' => 'Closed',
        ])->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'case_closed']));

        $this->assertDatabaseHas('social_case_studies', [
            'id' => $study->id,
            'workflow_step' => 'case_closed',
            'status' => 'Closed',
            'assistance_released' => 1,
        ], 'mswdo_social_case');
    }

    #[Test]
    public function ineligible_client_is_blocked_from_case_create(): void
    {
        $this->adminSession();
        $client = $this->client();
        $this->recentAssistance($client);

        $this->post(route('admin.social-case-studies.store', $client), [
            ...$this->requirementsPayload(),
            'beneficiary_intake_id' => $this->intake($client)->id,
            'status' => 'Open',
            'encoded_by' => 'Tester',
            'service_provided' => 'SOCIAL CASE STUDY REPORT',
            'purpose' => 'MEDICAL ASSISTANCE',
            'submitted_to' => 'NOT APPLICABLE',
        ])->assertRedirect(route('admin.social-case-eligibility.show', $client));

        $this->assertDatabaseMissing('social_case_studies', ['client_id' => $client->id], 'mswdo_social_case');
    }

    #[Test]
    public function direct_create_url_is_blocked_when_client_is_not_eligible(): void
    {
        $this->adminSession();
        $client = $this->client();
        $this->recentAssistance($client);

        $this->get(route('admin.social-case-studies.create', $client))
            ->assertRedirect(route('admin.social-case-eligibility.show', $client));

        $this->assertDatabaseMissing('social_case_studies', ['client_id' => $client->id], 'mswdo_social_case');
    }

    #[Test]
    public function workflow_steps_cannot_be_skipped(): void
    {
        $this->adminSession();
        $study = $this->studyAt('requirements_verification');

        $this->post(route('admin.social-case-studies.step.save', [$study, 'social_case_assessment']), $this->assessmentPayload())
            ->assertForbidden();

        $this->assertDatabaseHas('social_case_studies', [
            'id' => $study->id,
            'workflow_step' => 'requirements_verification',
        ], 'mswdo_social_case');
    }

    #[Test]
    public function assistance_release_creates_assistance_record(): void
    {
        $this->adminSession();
        $study = $this->studyAt('assistance_release', ['released_at' => now(), 'released_by' => 1, 'released_to' => 'Client']);

        $this->post(route('admin.social-case-studies.step.save', [$study, 'assistance_release']), [
            'assistance_amount' => 1500,
            'assistance_date' => '2026-07-13',
            'assistance_released' => '1',
        ])->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'case_closed']));

        $this->assertDatabaseHas('assistance_records', [
            'client_id' => $study->client_id,
            'status' => 'Released',
            'amount' => 1500,
        ], 'mswdo_social_case');
    }

    #[Test]
    public function assistance_release_does_not_duplicate_existing_case_assistance_record(): void
    {
        $this->adminSession();
        $study = $this->studyAt('assistance_release', ['released_at' => now(), 'released_by' => 1, 'released_to' => 'Client']);

        DB::connection('mswdo_social_case')->table('assistance_records')->insert([
            'client_id' => $study->client_id,
            'assistance_type' => 'Medical Assistance',
            'status' => 'Released',
            'release_date' => '2026-07-12',
            'amount' => 1000,
            'remarks' => 'Released through Social Case Study '.$study->case_number,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('admin.social-case-studies.step.save', [$study, 'assistance_release']), [
            'assistance_amount' => 1500,
            'assistance_date' => '2026-07-13',
            'assistance_released' => '1',
        ])->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'case_closed']));

        $this->assertSame(1, DB::connection('mswdo_social_case')->table('assistance_records')
            ->where('client_id', $study->client_id)
            ->where('remarks', 'Released through Social Case Study '.$study->case_number)
            ->count());
        $this->assertDatabaseHas('assistance_records', [
            'client_id' => $study->client_id,
            'amount' => 1500,
        ], 'mswdo_social_case');
    }

    #[Test]
    public function not_qualified_assessment_moves_case_to_closure_instead_of_report_generation(): void
    {
        $this->adminSession();
        $study = $this->studyAt('social_case_assessment');

        $this->post(route('admin.social-case-studies.step.save', [$study, 'social_case_assessment']), [
            'social_worker_assessment' => 'Client does not qualify under current guidelines.',
            'recommendation' => 'Not Qualified',
            'recommended_amount' => 0,
        ])->assertRedirect(route('admin.social-case-studies.step.show', [$study, 'case_closed']));

        $this->assertDatabaseHas('social_case_studies', [
            'id' => $study->id,
            'workflow_step' => 'case_closed',
            'status' => 'Closed',
        ], 'mswdo_social_case');
    }

    #[Test]
    public function supervisor_step_is_blocked_for_non_supervisor_role(): void
    {
        $this->adminSession(role: 'Social Case Study officer');
        $study = $this->studyAt('requirements_verification');

        $this->post(route('admin.social-case-studies.step.save', [$study, 'supervisor_review']), [
            'supervisor_notes' => 'Approve',
        ])->assertForbidden();
    }

    #[Test]
    public function non_social_case_roles_are_blocked_from_social_case_module(): void
    {
        $this->adminSession(role: 'Financial assistance officer');

        $this->get(route('admin.social-case-eligibility.index'))->assertForbidden();
    }

    #[Test]
    public function legacy_update_route_cannot_bypass_step_workflow(): void
    {
        $this->adminSession();
        $study = $this->studyAt('assessment_interview');

        $this->post(route('admin.social-case-studies.update', $study), [
            ...$this->requirementsPayload(),
            'service_provided' => 'SOCIAL CASE STUDY REPORT',
            'purpose' => 'MEDICAL ASSISTANCE',
            'submitted_to' => 'NOT APPLICABLE',
            'encoded_by' => 'Workflow Tester',
            'status' => 'Open',
        ])->assertForbidden();

        $this->assertDatabaseHas('social_case_studies', [
            'id' => $study->id,
            'workflow_step' => 'assessment_interview',
        ], 'mswdo_social_case');
    }

    #[Test]
    public function released_or_closed_cases_cannot_be_deleted(): void
    {
        $this->adminSession();
        $study = $this->studyAt('release_report', [
            'report_generated' => true,
            'released_at' => now(),
        ]);

        $this->post(route('admin.social-case-studies.destroy', $study))->assertForbidden();

        $this->assertDatabaseHas('social_case_studies', ['id' => $study->id], 'mswdo_social_case');
    }

    #[Test]
    public function unauthenticated_social_case_routes_redirect_to_login(): void
    {
        $client = $this->client();

        $this->get(route('admin.social-case-eligibility.index'))->assertRedirect(route('admin.login.form'));
        $this->get(route('admin.social-case-studies.create', $client))->assertRedirect(route('admin.login.form'));
        $this->post(route('admin.social-case-studies.store', $client))->assertRedirect(route('admin.login.form'));
    }

    private function adminSession(string $role = 'Social Case Study officer'): void
    {
        $this->withSession([
            'admin_user_id' => 1,
            'admin_user_name' => 'Workflow Tester',
            'admin_user_role' => $role,
        ]);
    }

    private function client(): Client
    {
        return Client::on('mswdo_social_case')->create([
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'birthdate' => '1986-01-15',
            'gender' => 'Male',
            'address' => 'Poblacion',
            'contact_number' => '09170000000',
        ]);
    }

    private function intake(Client $client): BeneficiaryIntake
    {
        return BeneficiaryIntake::on('mswdo_social_case')->create([
            'client_id' => $client->id,
            'control_number' => 'BI-'.uniqid(),
            'date_processed' => '2026-07-13',
            'encoder' => 'Workflow Tester',
            'client_last_name' => $client->last_name,
            'client_first_name' => $client->first_name,
            'client_middle_name' => $client->middle_name,
            'client_birthday' => '1986-01-15',
            'client_age' => 40,
            'client_sex' => 'Male',
            'client_civil_status' => 'Married',
            'client_address' => 'Poblacion',
            'client_barangay' => 'Poblacion',
            'client_contact_number' => '09170000000',
            'is_client_beneficiary' => true,
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospital bill',
            'submitted_to' => 'MSWDO',
        ]);
    }

    private function studyAt(string $step, array $overrides = []): SocialCaseStudy
    {
        $client = $this->client();

        return SocialCaseStudy::on('mswdo_social_case')->create([
            'client_id' => $client->id,
            'case_number' => 'SCS-'.uniqid(),
            'date_processed' => '2026-07-13',
            'client_last_name' => $client->last_name,
            'client_first_name' => $client->first_name,
            'client_age' => 40,
            'client_sex' => 'Male',
            'client_barangay' => 'Poblacion',
            'service_provided' => 'Medical Assistance',
            'purpose' => 'Hospital bill',
            'submitted_to' => 'MSWDO',
            'encoded_by' => 'Workflow Tester',
            'status' => 'Open',
            'workflow_step' => $step,
            ...$overrides,
        ]);
    }

    private function recentAssistance(Client $client): void
    {
        DB::connection('mswdo_social_case')->table('assistance_records')->insert([
            'client_id' => $client->id,
            'assistance_type' => 'Medical Assistance',
            'status' => 'Released',
            'release_date' => now()->subMonth()->toDateString(),
            'amount' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function requirementsPayload(): array
    {
        return [
            'date_processed' => '2026-07-13',
            'client_last_name' => 'Dela Cruz',
            'client_first_name' => 'Juan',
            'client_middle_name' => 'Santos',
            'client_age' => 40,
            'client_sex' => 'Male',
            'client_barangay' => 'Poblacion',
            'beneficiary_last_name' => 'Dela Cruz',
            'beneficiary_first_name' => 'Juan',
            'beneficiary_age' => 40,
            'beneficiary_birthday' => '1986-01-15',
            'beneficiary_sex' => 'Male',
            'beneficiary_barangay' => 'Poblacion',
            'medical_conditions' => ['Hypertension'],
            'requirements_complete' => '1',
        ];
    }

    private function interviewPayload(): array
    {
        return [
            'interview_date' => '2026-07-13',
            'interview_reason' => 'Medical expenses',
            'interview_situation' => 'Client needs support for hospital bill.',
            'interview_notes' => 'Verified documents.',
            'interview_complete' => '1',
        ];
    }

    private function familyPayload(): array
    {
        return [
            'interview_household' => 'Four household members.',
            'monthly_income' => 8000,
            'monthly_expenses' => 7500,
            'family_illnesses' => 'Hypertension',
            'previous_assistance' => 'No',
            'family_members' => [
                [
                    'full_name' => 'Maria Dela Cruz',
                    'relationship' => 'Spouse',
                    'age' => 38,
                    'sex' => 'Female',
                    'occupation' => 'Vendor',
                    'monthly_income' => 3000,
                    'is_dependent' => false,
                ],
            ],
        ];
    }

    private function assessmentPayload(): array
    {
        return [
            'social_worker_assessment' => 'Client is qualified for assistance.',
            'recommendation' => 'Approved',
            'recommended_amount' => 2500,
        ];
    }

    private function cleanSocialCaseTables(): void
    {
        DB::connection('mswdo_social_case')->statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ([
            'social_case_report_release_logs',
            'social_case_reports',
            'family_members',
            'beneficiary_intakes',
            'eligibility_audit_logs',
            'case_rejections',
            'social_case_studies',
            'assistance_records',
            'clients',
        ] as $table) {
            if (Schema::connection('mswdo_social_case')->hasTable($table)) {
                DB::connection('mswdo_social_case')->table($table)->truncate();
            }
        }
        DB::connection('mswdo_social_case')->statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
