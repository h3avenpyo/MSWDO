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
Route::get('/admin/statistics', [DashboardController::class, 'statistics'])->name('admin.statistics');
Route::get('/admin/add-officers', [DashboardController::class, 'addOfficers'])->name('admin.add-officers');
Route::post('/admin/add-officers', [DashboardController::class, 'storeOfficer'])->name('admin.officers.store');
Route::get('/admin/financial', [DashboardController::class, 'financial'])->name('admin.financial');
Route::get('/admin/senior', [DashboardController::class, 'senior'])->name('admin.senior');
Route::get('/admin/senior/registration', [DashboardController::class, 'seniorRegistration'])->name('admin.senior.registration');
Route::post('/admin/senior/registration', [DashboardController::class, 'storeSeniorRegistration'])->name('admin.senior.registration.store');
Route::get('/admin/senior/masterlist', [DashboardController::class, 'seniorMasterlist'])->name('admin.senior.masterlist');
Route::post('/admin/senior/archive/{id}', [DashboardController::class, 'archiveSenior'])->name('admin.senior.archive');
Route::get('/admin/senior/birthdays', [DashboardController::class, 'senior'])->name('admin.senior.birthdays');
Route::get('/admin/senior/reports', [DashboardController::class, 'senior'])->name('admin.senior.reports');
Route::get('/admin/multi-database', [MultiDatabaseDemoController::class, 'index'])->name('admin.multi-database.index');
Route::post('/admin/multi-database', [MultiDatabaseDemoController::class, 'store'])->name('admin.multi-database.store');
