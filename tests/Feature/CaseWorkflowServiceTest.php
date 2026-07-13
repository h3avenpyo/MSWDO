<?php

namespace Tests\Feature;

use App\Models\SocialCaseStudy;
use App\Services\SocialCase\SocialCaseReportGenerator;
use App\Services\SocialCase\CaseWorkflowService;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CaseWorkflowServiceTest extends TestCase
{
    #[Test]
    public function it_advances_only_to_the_next_mswdo_workflow_step(): void
    {
        $workflow = new CaseWorkflowService();

        $this->assertSame('client_search', $workflow->currentStep());
        $this->assertSame('eligibility', $workflow->getNextStep());
        $this->assertNull($workflow->getPreviousStep());
        $this->assertTrue($workflow->canAdvanceTo('eligibility'));
        $this->assertFalse($workflow->canAdvanceTo('beneficiary_intake'));

        $workflow->advanceTo('eligibility');

        $this->assertSame('eligibility', $workflow->currentStep());
        $this->assertTrue($workflow->isComplete('client_search'));
        $this->assertSame('client_search', $workflow->getPreviousStep());
    }

    #[Test]
    public function it_rejects_skipped_or_backward_transitions(): void
    {
        $workflow = new CaseWorkflowService();

        $this->expectException(LogicException::class);
        $workflow->advanceTo('requirements_verification');
    }

    #[Test]
    public function it_starts_a_created_case_at_the_correct_step_for_its_intake_state(): void
    {
        $workflow = new CaseWorkflowService();

        $this->assertSame('beneficiary_intake', $workflow->initialStep(false));
        $this->assertSame('requirements_verification', $workflow->initialStep(true));
    }

    #[Test]
    public function report_generation_follows_the_social_case_assessment(): void
    {
        $study = new SocialCaseStudy(['workflow_step' => 'social_case_assessment']);
        $workflow = new CaseWorkflowService($study);

        $this->assertTrue($workflow->canAdvanceTo('report_generation'));
    }

    #[Test]
    public function report_generator_refuses_cases_that_are_not_at_the_approved_report_step(): void
    {
        $study = new SocialCaseStudy(['workflow_step' => 'social_case_assessment']);

        $this->expectException(LogicException::class);
        app(SocialCaseReportGenerator::class)->generate($study, 1);
    }

    #[Test]
    public function assistance_release_requires_the_report_to_be_released_first(): void
    {
        $study = new SocialCaseStudy(['workflow_step' => 'release_report']);
        $workflow = new CaseWorkflowService($study);

        $this->assertFalse($workflow->canAdvanceTo('assistance_release'));

        $study->released_at = now();
        $study->released_by = 1;
        $study->released_to = 'Requesting Office';

        $this->assertTrue($workflow->canAdvanceTo('assistance_release'));
    }
}
