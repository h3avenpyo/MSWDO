<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SocialCase\SocialCaseController;
use App\Http\Controllers\Admin\OnlineRequestController;

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
        Route::get('/api/cases/{id}/document-data', [SocialCaseController::class, 'getDocumentData'])->name('api.document-data');
        Route::get('/api/activities', [SocialCaseController::class, 'getActivities'])->name('api.activities.get');
        Route::get('/api/eligibility-data', [SocialCaseController::class, 'getEligibilityData'])->name('api.eligibility-data');
        Route::get('/api/encoders', [SocialCaseController::class, 'getEncoders'])->name('api.encoders');
        Route::get('/api/debug/document-counter', [SocialCaseController::class, 'debugDocumentCounter'])->name('api.debug.document-counter');
        Route::get('/api/notifications', [SocialCaseController::class, 'getNotifications'])->name('api.notifications');
        Route::post('/api/notifications/mark-read', [SocialCaseController::class, 'markNotificationsRead'])->name('api.notifications.mark-read');

        // Eligibility checker only (social2@mwsdo.test)
        Route::middleware('role:admin,eligibility_checker')->group(function () {
            Route::post('/api/eligibility/check', [SocialCaseController::class, 'checkEligibility'])->name('api.eligibility.check');
            Route::post('/api/eligibility/submit', [SocialCaseController::class, 'submitEligibility'])->name('api.eligibility.submit');
        });

        // Social case module routes (eligibility checker only)
        Route::middleware('role:eligibility_checker')->group(function () {
            Route::get('/online-requests', [OnlineRequestController::class, 'index'])->name('online-requests');
            Route::get('/online-requests/accepted', [OnlineRequestController::class, 'accepted'])->name('online-requests.accepted');
            Route::get('/online-requests/rejected', [OnlineRequestController::class, 'rejected'])->name('online-requests.rejected');
            Route::get('/online-requests/{id}', [OnlineRequestController::class, 'show'])->name('online-requests.show');
            Route::get('/online-requests/{id}/details', [OnlineRequestController::class, 'showDetails'])->name('online-requests.details');
            Route::post('/online-requests/{id}/archive', [OnlineRequestController::class, 'archive'])->name('online-requests.archive');
            Route::post('/online-requests/{id}/accept', [OnlineRequestController::class, 'accept'])->name('online-requests.accept');
            Route::post('/online-requests/{id}/decline', [OnlineRequestController::class, 'decline'])->name('online-requests.decline');
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
