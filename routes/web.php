<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MultiDatabaseDemoController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\EmailCodeController;
use App\Http\Controllers\Admin\Auth\OfficerController;
use App\Http\Controllers\Admin\SocialCase\SocialCaseController;
use App\Http\Controllers\Admin\SocialCase\BeneficiaryIntakeController;
use App\Http\Controllers\Admin\Senior\SeniorController;
use App\Http\Controllers\Admin\Senior\BirthdayController;
use App\Http\Controllers\Admin\Senior\BirthdayPayoutController;
use App\Http\Controllers\Admin\Senior\SeniorAnalyticsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', [AuthController::class, 'showLogin'])->name('admin.login.form');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login/code/send', [EmailCodeController::class, 'send'])->name('admin.login.code.send');
Route::post('/admin/login/code/verify', [EmailCodeController::class, 'verify'])->name('admin.login.code.verify');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');
Route::post('/admin/clear-welcome', [AuthController::class, 'clearWelcome'])->name('admin.clear-welcome');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::middleware(['admin.auth'])->group(function () {
    Route::prefix('admin/social-case')->name('admin.social-case.')->group(function () {
        Route::get('/welcome', [SocialCaseController::class, 'socialCaseWelcome'])->name('welcome');
        Route::get('/dashboard', [SocialCaseController::class, 'socialCaseDashboard'])->name('dashboard');
        Route::get('/new', [SocialCaseController::class, 'socialCaseNew'])->name('new');
        Route::get('/intake', [SocialCaseController::class, 'socialCaseIntake'])->name('intake');
        Route::get('/cases', [SocialCaseController::class, 'socialCaseCases'])->name('cases');
        Route::get('/archive', [SocialCaseController::class, 'socialCaseArchive'])->name('archive');
        Route::get('/detail/{caseId}', [SocialCaseController::class, 'socialCaseDetail'])->name('detail');
        Route::get('/document/{caseId}/{agency}', [SocialCaseController::class, 'socialCaseDocument'])->name('document');

        // API routes for CRUD operations
        Route::get('/api/cases', [SocialCaseController::class, 'getCases'])->name('api.cases');
        Route::post('/api/cases', [SocialCaseController::class, 'storeCase'])->name('api.store');
        Route::get('/api/cases/{id}', [SocialCaseController::class, 'getCase'])->name('api.show');
        Route::put('/api/cases/{id}', [SocialCaseController::class, 'updateCase'])->name('api.update');
        Route::delete('/api/cases/{id}', [SocialCaseController::class, 'deleteCase'])->name('api.delete');

        // Activity logging routes
        Route::post('/api/activities', [SocialCaseController::class, 'logActivity'])->name('api.activities.log');
        Route::get('/api/activities', [SocialCaseController::class, 'getActivities'])->name('api.activities.get');
        Route::post('/api/activities/clear', [SocialCaseController::class, 'clearActivities'])->name('api.activities.clear');
    });
});

Route::get('/admin/add-officers', [OfficerController::class, 'addOfficers'])->name('admin.add-officers');
Route::post('/admin/add-officers', [OfficerController::class, 'storeOfficer'])->name('admin.officers.store');
Route::get('/admin/financial', [DashboardController::class, 'financial'])->name('admin.financial');
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

// Beneficiary Intake Routes (admin session required)
Route::middleware(['admin.auth'])->group(function () {
    Route::prefix('admin/beneficiary-intake')->name('admin.beneficiary-intake.')->group(function () {
        Route::get('/', [BeneficiaryIntakeController::class, 'index'])->name('index');
        Route::get('/create/{client?}', [BeneficiaryIntakeController::class, 'create'])->name('create');
        Route::post('/', [BeneficiaryIntakeController::class, 'store'])->name('store');
        Route::get('/{intake}', [BeneficiaryIntakeController::class, 'show'])->name('show');
    });
});
