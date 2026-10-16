<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/admin.php'));
            Route::middleware('web')->group(base_path('routes/social-case.php'));
            Route::middleware('web')->group(base_path('routes/financial.php'));
            Route::middleware('web')->group(base_path('routes/senior.php'));
            Route::middleware('web')->group(base_path('routes/wcpu.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Preserve raw contact input so validation rejects surrounding spaces too.
        $middleware->trimStrings(except: [
            'contact_number', 'phone', 'phone_number', 'mobile', 'mobile_number',
            'beneficiary_contact_number', 'rep_contact_number', 'emergency_contact_number',
            'victim_contact', 'guardian_contact', 'guardian_abuser_contact', 'witness_contact',
            'client_contact', 'recipient_contact', 'referred_by_contact', 'recipient_contact_number',
            'client.contact', 'bcpc_organizations.*.phone',
        ]);
        $middleware->redirectTo(
            guests: '/admin',
            users: '/admin/dashboard'
        );
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\EnsureAdminAuthenticated::class,
            'role' => \App\Http\Middleware\EnsureRole::class,
            'check.account.status' => \App\Http\Middleware\CheckAccountStatus::class,
            'financial.step2.auth' => \App\Http\Middleware\Financial\EnsureFinancialStep2Access::class,
            'position' => \App\Http\Middleware\Wcpu\CheckPosition::class,
            'inactivity.timeout' => \App\Http\Middleware\Wcpu\EnforceInactivityTimeout::class,
            'wcpu.guard' => \App\Http\Middleware\Wcpu\EnsureWcpuGuard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
