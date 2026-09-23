<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Senior\SeniorController;
use App\Http\Controllers\Admin\Senior\BirthdayController;
use App\Http\Controllers\Admin\Senior\BirthdayPayoutController;
use App\Http\Controllers\Admin\Senior\SeniorAnalyticsController;
use App\Http\Controllers\Admin\Senior\InBetweenBenefitController;

// Senior Citizens Module Routes
Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::get('/admin/senior', [SeniorController::class, 'senior'])->name('admin.senior');
    Route::post('/admin/senior/clear-activities', [SeniorController::class, 'clearRecentActivities'])->name('admin.senior.clear-activities');
    Route::get('/admin/senior/registration', [SeniorController::class, 'seniorRegistration'])->name('admin.senior.registration');
    Route::post('/admin/senior/registration', [SeniorController::class, 'storeSeniorRegistration'])->name('admin.senior.registration.store');
    Route::get('/admin/senior/masterlist', [SeniorController::class, 'seniorMasterlist'])->name('admin.senior.masterlist');
    Route::get('/admin/senior/id-card', [SeniorController::class, 'seniorIdCard'])->name('admin.senior.id-card');
    Route::get('/admin/senior/id-card/{id}', [SeniorController::class, 'generateIdCard'])->name('admin.senior.generate-id-card');
    Route::match(['get', 'post'], '/admin/senior/bulk-print-ids', [SeniorController::class, 'bulkPrintIds'])->name('admin.senior.bulk-print-ids');
    Route::post('/admin/senior/bulk-reprint-ids', [SeniorController::class, 'bulkReprintIds'])->name('admin.senior.bulk-reprint-ids');
    Route::get('/admin/senior/archive', [SeniorController::class, 'seniorArchiveList'])->name('admin.senior.archive.list');
    Route::post('/admin/senior/archive/{id}', [SeniorController::class, 'archiveSenior'])->name('admin.senior.archive');
    Route::post('/admin/senior/unarchive/{id}', [SeniorController::class, 'unarchiveSenior'])->name('admin.senior.unarchive');
    Route::get('/admin/senior/profile/{id}/json', [SeniorController::class, 'seniorProfileJson'])->name('admin.senior.profile.json');
    Route::post('/admin/senior/update/{id}', [SeniorController::class, 'updateSenior'])->name('admin.senior.update');
    Route::post('/admin/senior/id-card/{id}/reprint', [SeniorController::class, 'reprintIdCard'])->name('admin.senior.reprint');
    
    // Birthday Cash Gift Submodule
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
    Route::get('/admin/senior/reports', [SeniorController::class, 'reports'])->name('admin.senior.reports');
    Route::get('/admin/senior/reports/statistics-pdf', [SeniorController::class, 'statisticsPdfReport'])->name('admin.senior.reports.statistics-pdf');
    Route::get('/admin/senior/reports/age-distribution', [SeniorController::class, 'ageDistributionReport'])->name('admin.senior.reports.age-distribution');
    Route::get('/admin/senior/reports/barangay-distribution', [SeniorController::class, 'barangayDistributionReport'])->name('admin.senior.reports.barangay-distribution');
    Route::get('/admin/senior/reports/gender-breakdown', [SeniorController::class, 'genderBreakdownReport'])->name('admin.senior.reports.gender-breakdown');
    Route::get('/admin/senior/payouts-history', [BirthdayPayoutController::class, 'history'])->name('admin.senior.payouts-history');
    Route::post('/admin/senior/bulk-archive', [SeniorController::class, 'bulkArchive'])->name('admin.senior.bulk-archive');
    Route::post('/admin/senior/bulk-restore', [SeniorController::class, 'bulkRestore'])->name('admin.senior.bulk-restore');
    Route::get('/admin/senior/export', [SeniorController::class, 'exportSeniors'])->name('admin.senior.export');
    Route::get('/admin/senior/export-pdf', [SeniorController::class, 'exportSeniorsPdf'])->name('admin.senior.export-pdf');
    
    // In-Between Birthday Cash Gift Routes (integrated into senior module)
    Route::prefix('admin/senior/in-between')->name('admin.senior.in-between.')->group(function () {
        Route::get('/dashboard', [InBetweenBenefitController::class, 'dashboard'])->name('dashboard');
        Route::get('/eligibility-list', [InBetweenBenefitController::class, 'eligibilityList'])->name('eligibility-list');
        Route::get('/check-eligibility/{id}', [InBetweenBenefitController::class, 'checkEligibility'])->name('check-eligibility');
        Route::post('/eligibility-export-data', [InBetweenBenefitController::class, 'eligibilityExportData'])->name('eligibility-export-data');
        Route::post('/process-claim/{id}', [InBetweenBenefitController::class, 'processClaim'])->name('process-claim');
        Route::post('/bulk-process-claims', [InBetweenBenefitController::class, 'bulkProcessClaims'])->name('bulk-process-claims');
        Route::get('/history', [InBetweenBenefitController::class, 'benefitHistory'])->name('history');
        Route::post('/history-export-data', [InBetweenBenefitController::class, 'exportBenefitHistoryData'])->name('history-export-data');
        Route::post('/mark-exported', [InBetweenBenefitController::class, 'markExported'])->name('mark-exported');
        Route::get('/senior-card/{id}', [InBetweenBenefitController::class, 'seniorBenefitCard'])->name('senior-card');
        Route::get('/reports', [InBetweenBenefitController::class, 'reports'])->name('reports');
        Route::post('/bulk-delete', [InBetweenBenefitController::class, 'bulkDelete'])->name('bulk-delete');
    });
});
