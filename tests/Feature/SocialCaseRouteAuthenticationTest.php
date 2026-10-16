<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialCaseRouteAuthenticationTest extends TestCase
{
    #[Test]
    public function all_social_case_and_beneficiary_intake_routes_require_an_admin_session(): void
    {
        $protectedPrefixes = [
            'admin/social-case',
            'admin/social-case-eligibility',
            'admin/social-case-studies',
            'admin/beneficiary-intake',
        ];

        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn ($route) => Str::startsWith($route->uri(), $protectedPrefixes));

        $this->assertGreaterThanOrEqual(17, $routes->count());

        $routes->each(function ($route) {
            $this->assertContains('admin.auth', $route->middleware(), $route->uri());
        });

    }

    #[Test]
    public function guests_are_redirected_to_the_admin_login_from_social_case_routes(): void
    {
        $response = $this->get(route('admin.social-case.dashboard'));

        $response->assertRedirect(route('admin.login.form'));
    }
}
