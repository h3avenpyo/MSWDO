<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialAssistanceController;
use App\Http\Controllers\ServiceRequestController;

Route::get('/', function () {
    return view('welcome');
});

// Financial Assistance Public Intake Routes
Route::get('/financial-assistance', [FinancialAssistanceController::class, 'create'])->name('financial-assistance.create');
Route::post('/financial-assistance', [FinancialAssistanceController::class, 'store'])->name('financial-assistance.store');
Route::post('/financial-assistance/check-duplicate', [FinancialAssistanceController::class, 'checkDuplicate'])->name('financial-assistance.check-duplicate');

// Public Service Request Routes
Route::get('/service-request', [ServiceRequestController::class, 'create'])->name('service-request.create');
Route::post('/service-request', [ServiceRequestController::class, 'store'])->name('service-request.store');
