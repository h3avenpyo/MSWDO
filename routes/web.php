<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MultiDatabaseDemoController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BeneficiaryIntakeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', [AuthController::class, 'showLogin'])->name('admin.login.form');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::get('/admin/add-officers', [DashboardController::class, 'addOfficers'])->name('admin.add-officers');
Route::post('/admin/add-officers', [DashboardController::class, 'storeOfficer'])->name('admin.officers.store');
Route::get('/admin/financial', [DashboardController::class, 'financial'])->name('admin.financial');
Route::get('/admin/senior', [DashboardController::class, 'senior'])->name('admin.senior');
Route::post('/admin/senior/clear-activities', [DashboardController::class, 'clearRecentActivities'])->name('admin.senior.clear-activities');
Route::get('/admin/senior/registration', [DashboardController::class, 'seniorRegistration'])->name('admin.senior.registration');
Route::post('/admin/senior/registration', [DashboardController::class, 'storeSeniorRegistration'])->name('admin.senior.registration.store');
Route::get('/admin/senior/masterlist', [DashboardController::class, 'seniorMasterlist'])->name('admin.senior.masterlist');
Route::get('/admin/senior/archive', [DashboardController::class, 'seniorArchiveList'])->name('admin.senior.archive.list');
Route::post('/admin/senior/archive/{id}', [DashboardController::class, 'archiveSenior'])->name('admin.senior.archive');
Route::post('/admin/senior/unarchive/{id}', [DashboardController::class, 'unarchiveSenior'])->name('admin.senior.unarchive');
Route::get('/admin/senior/id-card/{id}', [DashboardController::class, 'showIdCard'])->name('admin.senior.id-card');
Route::post('/admin/senior/id-card/{id}/generate', [DashboardController::class, 'generateIdCard'])->name('admin.senior.id-card.generate');
Route::post('/admin/senior/id-card/{id}/reprint', [DashboardController::class, 'reprintIdCard'])->name('admin.senior.id-card.reprint');
Route::get('/admin/senior/id-card/{id}/download', [DashboardController::class, 'downloadIdCardPdf'])->name('admin.senior.id-card.download');
Route::get('/admin/senior/profile/{id}', [DashboardController::class, 'seniorProfile'])->name('admin.senior.profile');
Route::get('/admin/senior/profile/{id}/json', [DashboardController::class, 'seniorProfileJson'])->name('admin.senior.profile.json');
Route::prefix('admin/senior/birthdays')->name('admin.senior.birthdays')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\BirthdayController::class, 'index']);
    Route::get('/data', [App\Http\Controllers\Admin\BirthdayController::class, 'data'])->name('.data');
    Route::get('/profile/{id}', [App\Http\Controllers\Admin\BirthdayController::class, 'profile'])->name('.profile');
    Route::get('/by-barangay', [App\Http\Controllers\Admin\BirthdayController::class, 'dataByBarangay'])->name('.by-barangay');
    Route::get('/export/pdf', [App\Http\Controllers\Admin\BirthdayController::class, 'exportPdf'])->name('.export.pdf');
    Route::get('/export/csv', [App\Http\Controllers\Admin\BirthdayController::class, 'exportCsv'])->name('.export.csv');
    Route::get('/print', [App\Http\Controllers\Admin\BirthdayController::class, 'printView'])->name('.print');
});
Route::get('/admin/senior/statistics', [App\Http\Controllers\Admin\SeniorAnalyticsController::class, 'index'])->name('admin.senior.analytics');
Route::get('/admin/senior/reports', [DashboardController::class, 'senior'])->name('admin.senior.reports');
Route::prefix('admin/senior/birthday-payouts')->name('admin.senior.birthday-payouts')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'index']);
    Route::get('/history', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'history'])->name('.history');
    Route::post('/generate', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'generate'])->name('.generate');
    Route::post('/reset', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'reset'])->name('.reset');
    Route::post('/release/{id}', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'release'])->name('.release');
    Route::post('/bulk-release', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'bulkRelease'])->name('.bulk-release');
    Route::post('/cancel/{id}', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'cancel'])->name('.cancel');
    Route::get('/print', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'print'])->name('.print');
    Route::get('/export-pdf', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'exportPdf'])->name('.export-pdf');
    Route::get('/export-excel', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'exportExcel'])->name('.export-excel');
    Route::get('/receipt/{id}', [App\Http\Controllers\Admin\BirthdayPayoutController::class, 'receipt'])->name('.receipt');
});
Route::post('/admin/senior/bulk-archive', [DashboardController::class, 'bulkArchive'])->name('admin.senior.bulk-archive');
Route::post('/admin/senior/bulk-restore', [DashboardController::class, 'bulkRestore'])->name('admin.senior.bulk-restore');
Route::get('/admin/senior/export', [DashboardController::class, 'exportSeniors'])->name('admin.senior.export');
Route::get('/admin/senior/export-pdf', [DashboardController::class, 'exportSeniorsPdf'])->name('admin.senior.export-pdf');
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
