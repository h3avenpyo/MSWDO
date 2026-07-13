<?php

namespace App\Services\SocialCase;

use App\Models\SocialCaseStudy;
use InvalidArgumentException;
use LogicException;

class CaseWorkflowService
{
    public const STEPS = [
        'client_search',
        'eligibility',
        'beneficiary_intake',
        'requirements_verification',
        'assessment_interview',
        'family_composition',
        'social_case_assessment',
        'report_generation',
        'print_export',
        'release_report',
        'assistance_release',
        'case_closed',
    ];

    private string $step;

    public function __construct(private readonly ?SocialCaseStudy $study = null)
    {
        $this->step = $study?->workflow_step ?? 'client_search';
    }

    public function currentStep(): string
    {
        return $this->step;
    }

    public function canAdvanceTo(string $step): bool
    {
        $this->assertKnownStep($step);

        if ($this->stepIndex($step) !== $this->stepIndex($this->step) + 1) {
            return false;
        }

        if ($step === 'assistance_release') {
            return $this->study?->released_at !== null
                && $this->study?->released_by !== null
                && filled($this->study?->released_to);
        }

        return true;
    }

    public function advanceTo(string $step): void
    {
        if (! $this->canAdvanceTo($step)) {
            throw new LogicException(sprintf('Cannot advance workflow from [%s] to [%s].', $this->step, $step));
        }

        $this->step = $step;

        if ($this->study?->exists) {
            $this->study->update(['workflow_step' => $step]);
        }
    }

    public function isComplete(string $step): bool
    {
        $this->assertKnownStep($step);

        return $step === 'case_closed'
            ? $this->step === 'case_closed'
            : $this->stepIndex($this->step) > $this->stepIndex($step);
    }

    public function getNextStep(): ?string
    {
        return self::STEPS[$this->stepIndex($this->step) + 1] ?? null;
    }

    public function getPreviousStep(): ?string
    {
        $previous = $this->stepIndex($this->step) - 1;

        return $previous >= 0 ? self::STEPS[$previous] : null;
    }

    public function initialStep(bool $intakeCompleted): string
    {
        return $intakeCompleted ? 'requirements_verification' : 'beneficiary_intake';
    }

    private function stepIndex(string $step): int
    {
        $this->assertKnownStep($step);

        return array_search($step, self::STEPS, true);
    }

    private function assertKnownStep(string $step): void
    {
        if (! in_array($step, self::STEPS, true)) {
            throw new InvalidArgumentException(sprintf('Unknown workflow step [%s].', $step));
        }
    }
}
