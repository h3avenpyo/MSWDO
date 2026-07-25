<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MultiDatabaseDemoController;
use App\Http\Controllers\Admin\Auth\AuthController;
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
    });
});

Route::get('/admin/add-officers', [OfficerController::class, 'addOfficers'])->name('admin.add-officers');
Route::post('/admin/add-officers', [OfficerController::class, 'storeOfficer'])->name('admin.officers.store');
Route::get('/admin/financial/dashboard', [DashboardController::class, 'financialDashboard'])->name('admin.financial.dashboard');
Route::get('/admin/financial/financialstep1', [DashboardController::class, 'financialStep1'])->name('admin.financial.financialstep1');
Route::get('/admin/financial/financialstep2', [DashboardController::class, 'financialStep2'])->name('admin.financial.financialstep2');
Route::get('/admin/senior', [SeniorController::class, 'senior'])->name('admin.senior');
Route::post('/admin/senior/clear-activities', [SeniorController::class, 'clearRecentActivities'])->name('admin.senior.clear-activities');
Route::get('/admin/senior/registration', [SeniorController::class, 'seniorRegistration'])->name('admin.senior.registration');
Route::post('/admin/senior/registration', [SeniorController::class, 'storeSeniorRegistration'])->name('admin.senior.registration.store');
Route::get('/admin/senior/masterlist', [SeniorController::class, 'seniorMasterlist'])->name('admin.senior.masterlist');
Route::get('/admin/senior/archive', [SeniorController::class, 'seniorArchiveList'])->name('admin.senior.archive.list');
Route::post('/admin/senior/archive/{id}', [SeniorController::class, 'archiveSenior'])->name('admin.senior.archive');
Route::post('/admin/senior/unarchive/{id}', [SeniorController::class, 'unarchiveSenior'])->name('admin.senior.unarchive');
Route::get('/admin/senior/id-card/{id}', [SeniorController::class, 'showIdCard'])->name('admin.senior.id-card');
Route::post('/admin/senior/id-card/{id}/generate', [SeniorController::class, 'generateIdCard'])->name('admin.senior.id-card.generate');
Route::post('/admin/senior/id-card/{id}/reprint', [SeniorController::class, 'reprintIdCard'])->name('admin.senior.id-card.reprint');
Route::get('/admin/senior/id-card/{id}/download', [SeniorController::class, 'downloadIdCardPdf'])->name('admin.senior.id-card.download');
Route::get('/admin/senior/profile/{id}', [SeniorController::class, 'seniorProfile'])->name('admin.senior.profile');
Route::get('/admin/senior/profile/{id}/json', [SeniorController::class, 'seniorProfileJson'])->name('admin.senior.profile.json');
Route::prefix('admin/senior/birthdays')->name('admin.senior.birthdays')->group(function () {
    Route::get('/', [BirthdayController::class, 'index']);
    Route::get('/data', [BirthdayController::class, 'data'])->name('.data');
    Route::get('/profile/{id}', [BirthdayController::class, 'profile'])->name('.profile');
    Route::get('/by-barangay', [BirthdayController::class, 'dataByBarangay'])->name('.by-barangay');
    Route::get('/export/pdf', [BirthdayController::class, 'exportPdf'])->name('.export.pdf');
    Route::get('/export/csv', [BirthdayController::class, 'exportCsv'])->name('.export.csv');
    Route::get('/print', [BirthdayController::class, 'printView'])->name('.print');
});
Route::get('/admin/senior/statistics', [SeniorAnalyticsController::class, 'index'])->name('admin.senior.analytics');
Route::get('/admin/senior/reports', [SeniorController::class, 'senior'])->name('admin.senior.reports');
Route::prefix('admin/senior/birthday-payouts')->name('admin.senior.birthday-payouts')->group(function () {
    Route::get('/', [BirthdayPayoutController::class, 'index']);
    Route::get('/history', [BirthdayPayoutController::class, 'history'])->name('.history');
    Route::post('/generate', [BirthdayPayoutController::class, 'generate'])->name('.generate');
    Route::post('/reset', [BirthdayPayoutController::class, 'reset'])->name('.reset');
    Route::post('/release/{id}', [BirthdayPayoutController::class, 'release'])->name('.release');
    Route::post('/bulk-release', [BirthdayPayoutController::class, 'bulkRelease'])->name('.bulk-release');
    Route::post('/cancel/{id}', [BirthdayPayoutController::class, 'cancel'])->name('.cancel');
    Route::get('/print', [BirthdayPayoutController::class, 'print'])->name('.print');
    Route::get('/export-pdf', [BirthdayPayoutController::class, 'exportPdf'])->name('.export-pdf');
    Route::get('/export-excel', [BirthdayPayoutController::class, 'exportExcel'])->name('.export-excel');
    Route::get('/receipt/{id}', [BirthdayPayoutController::class, 'receipt'])->name('.receipt');
});
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
