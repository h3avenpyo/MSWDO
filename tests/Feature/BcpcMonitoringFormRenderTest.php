<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BcpcMonitoringFormRenderTest extends TestCase
{
    public static function monitoringModes(): array
    {
        return [
            'view' => [true, ['view' => 26], '80rem'],
            'edit' => [false, ['edit' => 26], '80rem'],
            'add' => [false, ['add' => 1], '47.9375rem'],
        ];
    }

    #[DataProvider('monitoringModes')]
    public function test_form_renders_sections_values_and_responsive_styles(bool $isView, array $query, string $cardMaxWidth): void
    {
        $this->withSession([]);
        $this->app->instance('request', Request::create('/desk-officer/bcpc-case-monitoring', 'GET', $query));
        Auth::shouldReceive('user')->andReturn(new class {
            public string $position = 'Test User';

            public function defaultCaseDepartment(): string { return 'BCPC'; }
            public function isMultiCaseDepartment(): bool { return false; }
        });

        // Render the real form and its sections without the database-backed portal shell.
        $template = str_replace("@extends('layouts.desk-officer')", '', file_get_contents(
            resource_path('views/wcpu/desk-officer/bcpc-case-monitoring-form.blade.php')
        ));
        $template .= "\n@stack('styles')\n@yield('content')\n@stack('scripts')";
        $view = $this->blade($template, [
            'isView' => $isView,
            'quarter' => 3,
            'year' => 2026,
            'barangay' => (object) ['name' => 'Test Barangay'],
            'monitoring' => (object) ['data' => [
                'bcpc_crimes_against_children' => ['rape' => ['year_2023' => ['male' => 7]]],
            ]],
        ]);

        foreach (['bcpc_crimes_against_children', 'bcpc_cicl_crimes', 'bcpc_cicl_status',
            'bcpc_children_at_risk', 'bcpc_special_protection', 'bcpc_organizations'] as $section) {
            $view->assertSee('data-monitoring-section="'.$section.'"', false);
        }
        $view->assertSee('value="7"', false)
            ->assertSee('Year 2024')
            ->assertSee('Year 2026')
            ->assertSee('data-card-max-width="'.$cardMaxWidth.'"', false)
            ->assertSee('bcpc-monitoring-card-layout.css')
            ->assertDontSee('@php', false);

        if ($cardMaxWidth === '80rem') {
            $view->assertSee('bcpc-monitoring-tablet-cards.css')
                ->assertSee('id="bcpc-monitoring-clear-section-filter"', false)
                ->assertSee('Clear filter');
        } else {
            $view->assertDontSee('bcpc-monitoring-tablet-cards.css')
                ->assertDontSee('id="bcpc-monitoring-clear-section-filter"', false);
        }
        if ($isView) {
            $view->assertDontSee('Save BCPC Case Monitoring');
        } else {
            $view->assertSee('Save BCPC Case Monitoring')->assertSee('Save & Send to Admin', false);
        }
    }
}
