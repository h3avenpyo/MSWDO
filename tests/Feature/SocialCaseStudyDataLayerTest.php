<?php

namespace Tests\Feature;

use App\Http\Requests\SocialCase\StoreSocialCaseStudyRequest;
use App\Models\SocialCaseStudy;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialCaseStudyDataLayerTest extends TestCase
{
    #[Test]
    public function all_migrated_study_data_fields_are_mass_assignable(): void
    {
        $study = new SocialCaseStudy();

        foreach ([
            'additional_requirements',
            'interview_reason',
            'interview_situation',
            'interview_household',
            'monthly_income',
            'monthly_expenses',
            'family_illnesses',
            'previous_assistance',
            'interview_notes',
            'social_worker_assessment',
            'recommendation',
            'recommended_amount',
            'workflow_step',
            'requirements_complete',
            'interview_complete',
            'evaluation_complete',
            'report_generated',
            'assistance_released',
            'assistance_amount',
            'assistance_date',
        ] as $field) {
            $this->assertTrue($study->isFillable($field), $field);
        }
    }

    #[Test]
    public function an_update_request_accepts_workflow_fields_without_requiring_create_only_fields(): void
    {
        $request = StoreSocialCaseStudyRequest::create('/admin/social-case-studies/update/1', 'POST', [
            'status' => 'In Progress',
            'workflow_step' => 'report_generation',
            'requirements_complete' => true,
            'interview_complete' => true,
            'evaluation_complete' => true,
            'report_generated' => true,
            'assistance_released' => false,
        ]);
        $route = new Route(['POST'], 'admin/social-case-studies/update/{socialCaseStudy}', []);
        $route->bind($request);
        $route->setParameter('socialCaseStudy', new SocialCaseStudy());
        $request->setRouteResolver(fn () => $route);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->passes(), $validator->errors()->toJson());
    }
}
