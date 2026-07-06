<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MultiDatabaseDemoController;
use App\Http\Controllers\Admin\AuthController;

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
Route::get('/admin/senior/registration', [DashboardController::class, 'seniorRegistration'])->name('admin.senior.registration');
Route::post('/admin/senior/registration', [DashboardController::class, 'storeSeniorRegistration'])->name('admin.senior.registration.store');
Route::get('/admin/senior/masterlist', [DashboardController::class, 'seniorMasterlist'])->name('admin.senior.masterlist');
Route::post('/admin/senior/archive/{id}', [DashboardController::class, 'archiveSenior'])->name('admin.senior.archive');
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
Route::get('/admin/multi-database', [MultiDatabaseDemoController::class, 'index'])->name('admin.multi-database.index');
Route::post('/admin/multi-database', [MultiDatabaseDemoController::class, 'store'])->name('admin.multi-database.store');
