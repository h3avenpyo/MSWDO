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
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\EnsureAdminAuthenticated::class,
            'role' => \App\Http\Middleware\EnsureRole::class,
            'check.account.status' => \App\Http\Middleware\CheckAccountStatus::class,
            'financial.step2.auth' => \App\Http\Middleware\Financial\EnsureFinancialStep2Access::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
