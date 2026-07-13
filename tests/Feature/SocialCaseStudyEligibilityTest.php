<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\SocialCase\SocialCaseStudyController;
use App\Models\Client;
use App\Services\SocialCase\EligibilityChecker;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialCaseStudyEligibilityTest extends TestCase
{
    #[Test]
    public function an_ineligible_client_cannot_open_the_case_study_create_form_directly(): void
    {
        $client = new Client([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        $client->id = 123;

        $checker = Mockery::mock(EligibilityChecker::class);
        $checker->shouldReceive('check')
            ->once()
            ->with($client)
            ->andReturn([
                'eligible' => false,
                'eligibleAgainDate' => Carbon::parse('2026-12-01'),
            ]);

        $response = app(SocialCaseStudyController::class)->create(new Request(), $client, $checker);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(
            route('admin.social-case-eligibility.show', $client),
            $response->getTargetUrl()
        );
        $this->assertStringContainsString('not eligible', session('error'));
    }
}
