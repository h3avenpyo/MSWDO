<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MultiDatabaseDemoController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\EmailCodeController;
use App\Http\Controllers\Admin\Auth\OfficerController;
use App\Http\Controllers\Admin\SocialCase\SocialCaseController;
use App\Http\Controllers\Admin\SocialCase\SocialCaseIntakeController;
use App\Http\Controllers\Admin\Financial\FinancialDashboardController;
use App\Http\Controllers\Admin\Financial\FinancialIntakeController;
use App\Http\Controllers\Admin\Senior\SeniorController;
use App\Http\Controllers\Admin\Senior\BirthdayController;
use App\Http\Controllers\Admin\Senior\BirthdayPayoutController;
use App\Http\Controllers\Admin\Senior\SeniorAnalyticsController;
use App\Http\Controllers\Admin\Auth\PasswordResetManagementController;
use App\Http\Controllers\Admin\OnlineRequestController;
use App\Http\Controllers\ServiceRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/service-request', [ServiceRequestController::class, 'store'])->name('service-request.store');

Route::get('/admin', [AuthController::class, 'showLogin'])->name('admin.login.form');
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

Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

// Social Case Module Routes
Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::prefix('admin/social-case')->name('admin.social-case.')->group(function () {
        // Shared routes (both eligibility checker and case encoder)
        Route::get('/welcome', [SocialCaseController::class, 'socialCaseWelcome'])->name('welcome');
        Route::get('/dashboard', [SocialCaseController::class, 'socialCaseDashboard'])->name('dashboard');
        Route::get('/new', [SocialCaseController::class, 'socialCaseNew'])->name('new');
        Route::get('/cases', [SocialCaseController::class, 'socialCaseCases'])->name('cases');
        Route::get('/archive', [SocialCaseController::class, 'socialCaseArchive'])->name('archive');
        Route::get('/detail/{caseId}', [SocialCaseController::class, 'socialCaseDetail'])->name('detail');
        Route::get('/document/{caseId}/{agency}', [SocialCaseController::class, 'socialCaseDocument'])->name('document');

        // Read-only API (shared)
        Route::get('/api/cases', [SocialCaseController::class, 'getCases'])->name('api.cases');
        Route::get('/api/cases/{id}', [SocialCaseController::class, 'getCase'])->name('api.show');
        Route::get('/api/activities', [SocialCaseController::class, 'getActivities'])->name('api.activities.get');

        // Eligibility checker only (social2@mwsdo.test)
        Route::middleware('role:admin,eligibility_checker')->group(function () {
            Route::post('/api/eligibility/check', [SocialCaseController::class, 'checkEligibility'])->name('api.eligibility.check');
            Route::post('/api/eligibility/submit', [SocialCaseController::class, 'submitEligibility'])->name('api.eligibility.submit');
        });

        // Social case module routes (eligibility checker and social worker only)
        Route::middleware('role:eligibility_checker,social_worker')->group(function () {
            Route::get('/online-requests', [OnlineRequestController::class, 'index'])->name('online-requests');
            Route::get('/online-requests/{id}', [OnlineRequestController::class, 'show'])->name('online-requests.show');
            Route::post('/online-requests/{id}/archive', [OnlineRequestController::class, 'archive'])->name('online-requests.archive');
            Route::post('/online-requests/{id}/accept', [OnlineRequestController::class, 'accept'])->name('online-requests.accept');
        });

        // Case encoder only (social@mwsdo.test)
        Route::middleware('role:admin,social_worker')->group(function () {
            Route::get('/intake', [SocialCaseController::class, 'socialCaseIntake'])->name('intake');
            Route::get('/submitted', [SocialCaseController::class, 'socialCaseSubmitted'])->name('submitted');
        });

        // Write API (case encoder only - creating, updating, archiving cases)
        Route::middleware('role:admin,social_worker')->group(function () {
            Route::post('/api/cases', [SocialCaseController::class, 'storeCase'])->name('api.store');
            Route::put('/api/cases/{id}', [SocialCaseController::class, 'updateCase'])->name('api.update');
            Route::delete('/api/cases/{id}', [SocialCaseController::class, 'deleteCase'])->name('api.delete');
        });

        // Activity logging (shared write - both roles log their own actions)
        Route::post('/api/activities', [SocialCaseController::class, 'logActivity'])->name('api.activities.log');
        Route::post('/api/activities/clear', [SocialCaseController::class, 'clearActivities'])->name('api.activities.clear');
    });
});

Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::get('/admin/add-officers', [OfficerController::class, 'addOfficers'])->name('admin.add-officers');
    Route::post('/admin/add-officers', [OfficerController::class, 'storeOfficer'])->name('admin.officers.store');
    Route::get('/admin/officers-directory', [OfficerController::class, 'officersDirectory'])->name('admin.officers-directory');
    Route::get('/admin/officers/{id}/edit', [OfficerController::class, 'editOfficer'])->name('admin.officers.edit');
    Route::put('/admin/officers/{id}', [OfficerController::class, 'updateOfficer'])->name('admin.officers.update');
    Route::post('/admin/officers/{id}/deactivate', [OfficerController::class, 'deactivateOfficer'])->name('admin.officers.deactivate');
    Route::post('/admin/officers/{id}/activate', [OfficerController::class, 'activateOfficer'])->name('admin.officers.activate');

    // Password Reset Management
    Route::get('/admin/password-reset-management', [PasswordResetManagementController::class, 'index'])->name('admin.password-reset-management');
    Route::post('/admin/password-reset/{id}/approve', [PasswordResetManagementController::class, 'approve'])->name('admin.password-reset.approve');
    Route::post('/admin/password-reset/{id}/reject', [PasswordResetManagementController::class, 'reject'])->name('admin.password-reset.reject');
    Route::delete('/admin/password-reset/{id}', [PasswordResetManagementController::class, 'delete'])->name('admin.password-reset.delete');
});

// Financial Module Routes
Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::get('/admin/financial/dashboard', [FinancialDashboardController::class, 'financialDashboard'])->name('admin.financial.dashboard');
    Route::get('/admin/financial/financialstep1', [FinancialDashboardController::class, 'financialStep1'])->name('admin.financial.financialstep1');
    Route::get('/admin/financial/financialstep2', [FinancialDashboardController::class, 'financialStep2'])->name('admin.financial.financialstep2');
    Route::get('/admin/financial/financialstep1statistics', [FinancialDashboardController::class, 'statistics'])->name('admin.financial.financialstep1statistics');
});

// Senior Citizens Module Routes
Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::get('/admin/senior', [SeniorController::class, 'senior'])->name('admin.senior');
    Route::post('/admin/senior/clear-activities', [SeniorController::class, 'clearRecentActivities'])->name('admin.senior.clear-activities');
    Route::get('/admin/senior/registration', [SeniorController::class, 'seniorRegistration'])->name('admin.senior.registration');
    Route::post('/admin/senior/registration', [SeniorController::class, 'storeSeniorRegistration'])->name('admin.senior.registration.store');
    Route::get('/admin/senior/masterlist', [SeniorController::class, 'seniorMasterlist'])->name('admin.senior.masterlist');
    Route::get('/admin/senior/archive', [SeniorController::class, 'seniorArchiveList'])->name('admin.senior.archive.list');
    Route::post('/admin/senior/archive/{id}', [SeniorController::class, 'archiveSenior'])->name('admin.senior.archive');
    Route::post('/admin/senior/unarchive/{id}', [SeniorController::class, 'unarchiveSenior'])->name('admin.senior.unarchive');
    Route::get('/admin/senior/profile/{id}/json', [SeniorController::class, 'seniorProfileJson'])->name('admin.senior.profile.json');
    Route::prefix('admin/senior/birthdays')->name('admin.senior.birthdays')->group(function () {
        Route::get('/', [BirthdayController::class, 'index']);
        Route::get('/data', [BirthdayController::class, 'data'])->name('.data');
        Route::get('/profile/{id}', [BirthdayController::class, 'profile'])->name('.profile');
        Route::get('/by-barangay', [BirthdayController::class, 'dataByBarangay'])->name('.by-barangay');
        Route::get('/export/pdf', [BirthdayController::class, 'exportPdf'])->name('.export.pdf');
        Route::get('/export/csv', [BirthdayController::class, 'exportCsv'])->name('.export.csv');
        Route::get('/print', [BirthdayController::class, 'printView'])->name('.print');
        Route::post('/generate-payouts', [BirthdayController::class, 'generatePayouts'])->name('.generate-payouts');
        Route::post('/release-payout/{id}', [BirthdayController::class, 'releasePayout'])->name('.release-payout');
        Route::post('/bulk-release', [BirthdayController::class, 'bulkRelease'])->name('.bulk-release');
        Route::post('/print-bulk', [BirthdayController::class, 'printBulkReleased'])->name('.print-bulk');
        Route::post('/generate-all', [BirthdayController::class, 'generateAllPayouts'])->name('.generate-all');
        Route::post('/release-all', [BirthdayController::class, 'releaseAllPayouts'])->name('.release-all');
        Route::post('/generate-barangay', [BirthdayController::class, 'generateBarangayPayouts'])->name('.generate-barangay');
        Route::post('/release-barangay', [BirthdayController::class, 'releaseBarangayPayouts'])->name('.release-barangay');
    });
    Route::get('/admin/senior/statistics', [SeniorAnalyticsController::class, 'index'])->name('admin.senior.analytics');
    Route::get('/admin/senior/reports', [SeniorController::class, 'senior'])->name('admin.senior.reports');
    Route::get('/admin/senior/payouts-history', [BirthdayPayoutController::class, 'history'])->name('admin.senior.payouts-history');
    Route::post('/admin/senior/bulk-archive', [SeniorController::class, 'bulkArchive'])->name('admin.senior.bulk-archive');
    Route::post('/admin/senior/bulk-restore', [SeniorController::class, 'bulkRestore'])->name('admin.senior.bulk-restore');
    Route::get('/admin/senior/export', [SeniorController::class, 'exportSeniors'])->name('admin.senior.export');
    Route::get('/admin/senior/export-pdf', [SeniorController::class, 'exportSeniorsPdf'])->name('admin.senior.export-pdf');
    Route::get('/admin/multi-database', [MultiDatabaseDemoController::class, 'index'])->name('admin.multi-database.index');
    Route::post('/admin/multi-database', [MultiDatabaseDemoController::class, 'store'])->name('admin.multi-database.store');
});

// Financial Assistance Intake Routes (admin session required)
Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::prefix('admin/beneficiary-intake')->name('admin.beneficiary-intake.')->group(function () {
        Route::get('/', [FinancialIntakeController::class, 'index'])->name('index');
        Route::get('/create/{client?}', [FinancialIntakeController::class, 'create'])->name('create');
        Route::post('/', [FinancialIntakeController::class, 'store'])->name('store');
        Route::post('/check-duplicate', [FinancialIntakeController::class, 'checkDuplicate'])->name('check-duplicate');
        Route::get('/transmittal', [FinancialIntakeController::class, 'transmittalReport'])->name('transmittal');
        Route::post('/transmittal', [FinancialIntakeController::class, 'transmittalReport'])->name('transmittal.generate');
        Route::get('/{intake}', [FinancialIntakeController::class, 'show'])->name('show');
        Route::get('/{intake}/edit', [FinancialIntakeController::class, 'edit'])->name('edit');
        Route::put('/{intake}', [FinancialIntakeController::class, 'update'])->name('update');
        Route::delete('/{intake}', [FinancialIntakeController::class, 'destroy'])->name('destroy');
    });
});
