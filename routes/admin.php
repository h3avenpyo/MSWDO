<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\EmailCodeController;
use App\Http\Controllers\Admin\Auth\OfficerController;
use App\Http\Controllers\Admin\Auth\PasswordResetManagementController;
use App\Http\Controllers\Admin\MultiDatabaseDemoController;

// Admin Authentication Routes
Route::get('/admin', [AuthController::class, 'showLogin'])->name('admin.login.form');
Route::redirect('/admin/login', '/admin');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login/code/send', [EmailCodeController::class, 'send'])->name('admin.login.code.send');
Route::post('/admin/login/code/verify', [EmailCodeController::class, 'verify'])->name('admin.login.code.verify');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::post('/admin/clear-welcome', [AuthController::class, 'clearWelcome'])->name('admin.clear-welcome');
Route::get('/admin/check-account-status', [AuthController::class, 'checkAccountStatus'])->name('admin.check-account-status');

// Forgot Password Routes
Route::get('/admin/forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.forgot-password');
Route::post('/admin/forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.password.send-link');
Route::get('/admin/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('admin.password.reset');
Route::post('/admin/reset-password', [AuthController::class, 'resetPassword'])->name('admin.password.update');

// Authenticated Admin Dashboard & Management Routes
Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/reports/data', [AdminReportController::class, 'getReportData'])->name('admin.dashboard.reports.data');
    Route::get('/admin/dashboard/reports/pdf', [AdminReportController::class, 'exportPdf'])->name('admin.dashboard.reports.pdf');
    Route::get('/admin/dashboard/reports/print', [AdminReportController::class, 'printView'])->name('admin.dashboard.reports.print');

    // Officers Directory & Management
    Route::get('/admin/add-officers', [OfficerController::class, 'addOfficers'])->name('admin.add-officers');
    Route::post('/admin/add-officers', [OfficerController::class, 'storeOfficer'])->name('admin.officers.store');
    Route::get('/admin/officers-directory', [OfficerController::class, 'officersDirectory'])->name('admin.officers-directory');
    Route::get('/admin/officers/{id}/edit', [OfficerController::class, 'editOfficer'])->name('admin.officers.edit');
    Route::put('/admin/officers/{id}', [OfficerController::class, 'updateOfficer'])->name('admin.officers.update');
    Route::post('/admin/officers/{id}/deactivate', [OfficerController::class, 'deactivateOfficer'])->name('admin.officers.deactivate');
    Route::post('/admin/officers/{id}/activate', [OfficerController::class, 'activateOfficer'])->name('admin.officers.activate');

    // WCPU (BCPC / SWD) Officer Management
    Route::get('/admin/wcpu-officers/{id}/edit', [OfficerController::class, 'wcpuEditOfficer'])->name('admin.wcpu-officers.edit');
    Route::put('/admin/wcpu-officers/{id}', [OfficerController::class, 'wcpuUpdateOfficer'])->name('admin.wcpu-officers.update');
    Route::post('/admin/wcpu-officers/{id}/deactivate', [OfficerController::class, 'wcpuDeactivateOfficer'])->name('admin.wcpu-officers.deactivate');
    Route::post('/admin/wcpu-officers/{id}/activate', [OfficerController::class, 'wcpuActivateOfficer'])->name('admin.wcpu-officers.activate');

    // Password Reset Management
    Route::get('/admin/password-reset-management', [PasswordResetManagementController::class, 'index'])->name('admin.password-reset-management');
    Route::post('/admin/password-reset/{id}/approve', [PasswordResetManagementController::class, 'approve'])->name('admin.password-reset.approve');
    Route::post('/admin/password-reset/{id}/reject', [PasswordResetManagementController::class, 'reject'])->name('admin.password-reset.reject');
    Route::delete('/admin/password-reset/{id}', [PasswordResetManagementController::class, 'delete'])->name('admin.password-reset.delete');

    // Multi-Database Demo
    Route::get('/admin/multi-database', [MultiDatabaseDemoController::class, 'index'])->name('admin.multi-database.index');
    Route::post('/admin/multi-database', [MultiDatabaseDemoController::class, 'store'])->name('admin.multi-database.store');
});
