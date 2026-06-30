<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return view('admin.login');
});

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/statistics', [DashboardController::class, 'statistics'])->name('admin.statistics');
Route::get('/admin/add-officers', [DashboardController::class, 'addOfficers'])->name('admin.add-officers');
