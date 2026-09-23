<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Financial\FinancialDashboardController;
use App\Http\Controllers\Admin\Financial\FinancialIntakeController;
use App\Http\Controllers\Admin\Financial\FinancialSmsController;
use App\Http\Controllers\Admin\Financial\OnlineFinancialIntakeController;
use App\Http\Controllers\Admin\Historical\HistoricalFinancialIntakeController;

// Financial Module Routes
Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::get('/admin/financial/dashboard', [FinancialDashboardController::class, 'financialDashboard'])->name('admin.financial.dashboard');
    Route::get('/admin/financial/financialstep1', [FinancialDashboardController::class, 'financialStep1'])->name('admin.financial.financialstep1');
    
    // Step 1: Intake Online Applications Management Routes
    Route::prefix('admin/financial/online-intakes')->name('admin.financial.online-intakes.')->group(function () {
        Route::get('/', [OnlineFinancialIntakeController::class, 'index'])->name('index');
        Route::get('/{id}', [OnlineFinancialIntakeController::class, 'show'])->name('show');
        Route::post('/{id}/accept', [OnlineFinancialIntakeController::class, 'accept'])->name('accept');
        Route::post('/{id}/reject', [OnlineFinancialIntakeController::class, 'reject'])->name('reject');
    });

    // Step 1: Archive Routes
    Route::get('/admin/financial/step1/archive', [FinancialDashboardController::class, 'step1Archive'])->name('admin.financial.step1.archive');
    Route::post('/admin/financial/step1/archive/{id}', [FinancialDashboardController::class, 'archiveStep1'])->name('admin.financial.step1.archive.post');
    Route::post('/admin/financial/step1/restore/{id}', [FinancialDashboardController::class, 'restoreStep1'])->name('admin.financial.step1.restore');

    Route::post('/admin/financial/step2/authenticate', [FinancialDashboardController::class, 'authenticateStep2'])->name('admin.financial.step2.authenticate');
    Route::get('/admin/financial/financialstep1statistics', [FinancialDashboardController::class, 'statistics'])->name('admin.financial.financialstep1statistics');

    // Step 2 Protected Routes (requires Step 2 credentials or authorized role)
    Route::middleware('financial.step2.auth')->group(function () {
        Route::get('/admin/financial/financialstep2', [FinancialDashboardController::class, 'financialStep2'])->name('admin.financial.financialstep2');
        Route::get('/admin/financial/financialstep2/all-intakes', [FinancialDashboardController::class, 'financialStep2AllIntakes'])->name('admin.financial.financialstep2.all-intakes');
        Route::get('/admin/financial/financialstep2/payroll', [FinancialDashboardController::class, 'financialStep2Payroll'])->name('admin.financial.financialstep2.payroll');
        Route::post('/admin/financial/financialstep2/payroll/update-amount', [FinancialDashboardController::class, 'updateIntakeAmount'])->name('admin.financial.financialstep2.payroll.update-amount');
        Route::post('/admin/financial/financialstep2/payroll/bulk-update-amounts', [FinancialDashboardController::class, 'bulkUpdateIntakeAmounts'])->name('admin.financial.financialstep2.payroll.bulk-update-amounts');
        Route::post('/admin/financial/financialstep2/payroll/generate', [FinancialDashboardController::class, 'generatePayroll'])->name('admin.financial.financialstep2.payroll.generate');
        Route::get('/admin/financial/financialstep2/payroll/print', [FinancialDashboardController::class, 'printPayroll'])->name('admin.financial.financialstep2.payroll.print');
        Route::get('/admin/financial/financialstep2/payroll-records', [FinancialDashboardController::class, 'financialStep2PayrollRecords'])->name('admin.financial.financialstep2.payroll-records');
        Route::get('/admin/financial/financialstep2/payroll-records/date/{date}', [FinancialDashboardController::class, 'financialStep2PayrollRecords'])->name('admin.financial.financialstep2.payroll-records.date');
        Route::post('/admin/financial/financialstep2/payroll/intake/{id}/claim-status', [FinancialDashboardController::class, 'updateIntakeClaimStatus'])->name('admin.financial.financialstep2.payroll.intake.claim-status');
        Route::get('/admin/financial/financialstep2/liquidation', [FinancialDashboardController::class, 'financialStep2Liquidation'])->name('admin.financial.financialstep2.liquidation');
        Route::get('/admin/financial/financialstep2/liquidation/report/month/{yearMonth}', [FinancialDashboardController::class, 'financialStep2LiquidationReportMonthly'])->name('admin.financial.financialstep2.liquidation.report.month');
        Route::get('/admin/financial/financialstep2/liquidation/report/{id}', [FinancialDashboardController::class, 'financialStep2LiquidationReport'])->name('admin.financial.financialstep2.liquidation.report');
        Route::get('/admin/financial/financialstep2/archive', [FinancialDashboardController::class, 'financialStep2Archive'])->name('admin.financial.financialstep2.archive');
        Route::post('/admin/financial/financialstep2/archive/{id}', [FinancialDashboardController::class, 'archiveStep2'])->name('admin.financial.financialstep2.archive.post');
        Route::post('/admin/financial/financialstep2/restore/{id}', [FinancialDashboardController::class, 'restoreStep2'])->name('admin.financial.financialstep2.restore');
        Route::get('/admin/financial/financialstep2/statistics', [FinancialDashboardController::class, 'financialStep2Statistics'])->name('admin.financial.financialstep2.statistics');

        // Financial Step 2 SMS Messaging Routes
        Route::post('/admin/financial/financialstep2/messages/send', [FinancialSmsController::class, 'send'])->name('admin.financial.financialstep2.messages.send');
        Route::get('/admin/financial/financialstep2/messages/history/{intakeId}', [FinancialSmsController::class, 'history'])->name('admin.financial.financialstep2.messages.history');
        Route::post('/admin/financial/financialstep2/intakes/{id}/claiming-date', [FinancialSmsController::class, 'updateClaimingDate'])->name('admin.financial.financialstep2.intakes.claiming-date');
        Route::get('/admin/financial/financialstep2/messages/unclaimed-by-month', [FinancialSmsController::class, 'getUnclaimedByMonth'])->name('admin.financial.financialstep2.messages.unclaimed-by-month');
        Route::post('/admin/financial/financialstep2/messages/send-bulk-unclaimed', [FinancialSmsController::class, 'sendBulkUnclaimed'])->name('admin.financial.financialstep2.messages.send-bulk-unclaimed');
        Route::get('/admin/financial/financialstep2/messages/gateway-status', [FinancialSmsController::class, 'gatewayStatus'])->name('admin.financial.financialstep2.messages.gateway-status');
    });
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

// Historical Data Entry Routes (admin session required)
Route::middleware(['admin.auth', 'check.account.status'])->group(function () {
    Route::prefix('admin/historical-data')->name('admin.historical-data.')->group(function () {
        Route::get('/financial-intake', [HistoricalFinancialIntakeController::class, 'create'])->name('financial-intake');
        Route::post('/financial-intake', [HistoricalFinancialIntakeController::class, 'store'])->name('financial-intake.store');
        Route::post('/financial-intake/check-duplicate', [HistoricalFinancialIntakeController::class, 'checkDuplicate'])->name('financial-intake.check-duplicate');
    });
});
