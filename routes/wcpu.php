<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Wcpu\AdminController;
use App\Http\Controllers\Wcpu\BarangayController;
use App\Http\Controllers\Wcpu\CaseController;
use App\Http\Controllers\Wcpu\UserController;
use App\Http\Controllers\Wcpu\Auth\LoginController;
use App\Http\Controllers\Wcpu\Admin\CaseReportMailController;
use App\Http\Controllers\Wcpu\Admin\ReferralInfoController;
use App\Http\Controllers\Wcpu\Admin\ChangePassController;
use App\Http\Controllers\Wcpu\DeskOfficerController;
use App\Http\Controllers\Wcpu\ReferralController;
use App\Http\Controllers\Wcpu\QuarterlyReportController;
use App\Http\Controllers\Wcpu\CalendarController;
use App\Http\Controllers\Wcpu\CaseProfileController;
use App\Http\Controllers\Wcpu\AdminUserController;
use App\Http\Controllers\Wcpu\BcpcCaseMonitoringController;

Route::middleware(['web', 'wcpu.guard', 'inactivity.timeout'])->group(function () {

    // Public Awareness Page (Centralized MSWDO WCPU entry point)
    Route::get('/wcpu', function () {
        return view('clientSide.home');
    })->name('wcpu.home');

    // Centralized Authentication: Redirect WCPU login endpoints to the single MSWDO login page
    Route::get('/wcpu/login', function () {
        return redirect()->route('admin.login.form');
    })->name('wcpu.login');

    Route::get('/desk-officer/login', function () {
        return redirect()->route('admin.login.form');
    })->name('desk-officer.login');

    Route::post('/wcpu/login', function () {
        return redirect()->route('admin.login.form');
    })->name('wcpu.login.submit');

    // Default 'login' alias redirecting to MSWDO login page
    Route::get('/login', function () {
        return redirect()->route('admin.login.form');
    })->name('login');

    // Password Reset Routes
    Route::get('/password/reset', function() {
        return view('auth.passwords.email');
    })->name('password.request');

    Route::post('/password/email', function(\Illuminate\Http\Request $request) {
        $request->validate(['login' => 'required|string']);
        $user = \App\Models\Wcpu\User::where('email', $request->input('login'))
            ->orWhere('username', $request->input('login'))
            ->first();
        if (! $user || empty($user->email)) {
            return back()->withErrors(['login' => 'We could not find an account with that username or email address.'])->withInput();
        }
        $response = \Illuminate\Support\Facades\Password::broker('wcpu_users')->sendResetLink(['email' => $user->email]);
        return $response == \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('status', trans($response))
            : back()->withErrors(['login' => trans($response)]);
    })->name('password.email');

    Route::get('/password/reset/{token}', function($token) {
        return view('auth.passwords.reset', ['token' => $token]);
    })->name('password.reset');

    Route::post('/password/reset', function(\Illuminate\Http\Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        
        $response = \Illuminate\Support\Facades\Password::broker('wcpu_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = \Illuminate\Support\Facades\Hash::make($password);
                $user->save();
            }
        );
        
        return $response == \Illuminate\Support\Facades\Password::PASSWORD_RESET
            ? redirect()->route('wcpu.login')->with('password_reset_success', 'Your password has been reset successfully.')
            : back()->withErrors(['email' => trans($response)]);
    })->name('password.update');

    // Password Reset Request (public - no auth required - for desk officers)
    Route::post('/password-reset-request', [LoginController::class, 'requestPasswordReset'])->name('password.reset.request');
    Route::get('/password-reset-access/{id}', [LoginController::class, 'showPasswordResetAccessForm'])->name('password.reset.access');
    Route::post('/password-reset-access/{id}', [LoginController::class, 'verifyPasswordResetAccess'])->name('password.reset.access.verify');
    Route::post('/password-reset-notice/mark-seen', [LoginController::class, 'markPasswordResetNoticeSeen'])->name('password.reset.notice.mark-seen');

    // Public API Routes
    Route::prefix('api')->group(function () {
        Route::get('/barangays', [BarangayController::class, 'dropdown'])->name('api.barangays.dropdown');
        Route::get('/cases/types', [CaseController::class, 'getCaseTypes'])->name('api.cases.types');
        Route::get('/cases/generate-number/{barangayId}/{caseType?}', [CaseController::class, 'generateCaseNumber'])->name('api.cases.generate-number');
    });

    // Logout Route
    Route::post('/wcpu/logout', [LoginController::class, 'logout'])->name('wcpu.logout');
    if (!Route::has('logout')) {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    }

    // Authenticated Routes
    Route::middleware(['auth:wcpu'])->group(function () {
        Route::post('/session/activity', fn () => response()->json(['active' => true]))->name('session.activity');
        Route::get('/session/status', fn () => response()->json(['active' => true]))->name('session.status');
        
        // Common routes for all authenticated users
        Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])->name('admin-dashboard');
        
        // Desk Officer Routes
        Route::middleware(['position:Desk Officer'])->prefix('desk-officer')->name('desk-officer.')->group(function () {
            // Dashboard
            Route::get('/dashboard', [DeskOfficerController::class, 'dashboard'])->name('dashboard');
            Route::get('/bcpc-dashboard', [DeskOfficerController::class, 'bcpcDashboard'])->name('bcpc.dashboard');
            Route::get('/archived-barangay-cases', [DeskOfficerController::class, 'archivedBarangayCases'])->name('archived-barangay-cases.index');
            Route::post('/archived-barangay-cases/{id}/restore', [DeskOfficerController::class, 'restoreArchivedBarangayCase'])->name('archived-barangay-cases.restore');
            Route::delete('/archived-barangay-cases/{id}', [DeskOfficerController::class, 'forceDeleteArchivedBarangayCase'])->name('archived-barangay-cases.force-delete');
            
            // Cases Management
            Route::prefix('cases')->group(function () {
                Route::get('/', [DeskOfficerController::class, 'index'])->name('cases.index');
                Route::get('/create', [DeskOfficerController::class, 'create'])->name('cases.create');
                Route::post('/', [DeskOfficerController::class, 'store'])->name('cases.store');

                // Print all cases for the desk officer's barangay
                Route::get('/print-all', [DeskOfficerController::class, 'printAllCases'])->name('cases.print-all');

                // More-specific routes first (use encrypted hash for external links)
                Route::get('/{hash}/edit', [DeskOfficerController::class, 'edit'])->name('cases.edit');
                Route::put('/{hash}', [DeskOfficerController::class, 'update'])->name('cases.update');
                Route::delete('/{hash}', [DeskOfficerController::class, 'destroy'])->name('cases.destroy');

                // Print / PDF (opens inline) - use encrypted hash
                Route::get('/{hash}/print', [DeskOfficerController::class, 'print'])
                    ->name('cases.print');

                // Generic view by encrypted hash
                Route::get('/{hash}', [DeskOfficerController::class, 'show'])
                    ->name('cases.show');
            });

            // Case referral routes
            Route::get('/cases/{hash}/refer', [DeskOfficerController::class, 'showReferralForm'])->name('cases.refer');
            Route::post('/cases/{hash}/refer', [DeskOfficerController::class, 'storeReferral'])->name('cases.refer.store');
            
            // Referral Management Routes
            Route::prefix('referrals')->group(function () {
                Route::get('/archived', [DeskOfficerController::class, 'archivedReferrals'])->name('referrals.archived');
                Route::post('/{id}/archive', [DeskOfficerController::class, 'archiveReferral'])->name('referrals.archive');
                Route::post('/{id}/restore', [DeskOfficerController::class, 'restoreArchivedReferral'])->name('referrals.restore');
                Route::delete('/{id}/force-delete', [DeskOfficerController::class, 'forceDeleteArchivedReferral'])->name('referrals.force-delete');
                Route::get('/', [DeskOfficerController::class, 'referrals'])->name('referrals');
                Route::get('/print-all', [ReferralController::class, 'printAll'])->name('referrals.print-all');
                Route::get('/{id}/edit', [ReferralController::class, 'edit'])->name('desk-officer.referrals.edit');
                Route::get('/{id}', [DeskOfficerController::class, 'showReferral'])->name('referrals.show');
                Route::get('/{id}/print', [DeskOfficerController::class, 'printReferral'])->name('referrals.print');
            });

            Route::get('/referrals/{referral}/details', [ReferralController::class, 'details'])->name('desk-officer.referrals.details');
            Route::put('/referrals/{referral}', [ReferralController::class, 'update'])->name('desk-officer.referrals.update');
            Route::get('/api/recipients', [UserController::class, 'getRecipients'])->name('api.recipients');
            
            // Quarterly Reports
            Route::prefix('quarterly-reports')->name('quarterly-reports.')->group(function () {
                Route::get('/print-all', [QuarterlyReportController::class, 'printAllDeskOfficer'])->name('print-all');
                Route::get('/', [QuarterlyReportController::class, 'index'])->name('index');
                Route::get('/create', [QuarterlyReportController::class, 'create'])->name('create');
                Route::post('/', [QuarterlyReportController::class, 'store'])->name('store');
                Route::get('/archived', [QuarterlyReportController::class, 'archived'])->name('archived');
                Route::post('/{id}/archive', [QuarterlyReportController::class, 'archive'])->name('archive');
                Route::post('/{id}/restore', [QuarterlyReportController::class, 'restore'])->name('restore');
                Route::get('/{id}', [QuarterlyReportController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [QuarterlyReportController::class, 'edit'])->name('edit');
                Route::post('/{id}/regenerate', [QuarterlyReportController::class, 'regenerate'])->name('regenerate');
                Route::get('/{id}/download', [QuarterlyReportController::class, 'download'])->name('download');
                Route::delete('/{id}/force-delete', [QuarterlyReportController::class, 'forceDelete'])->name('force-delete');
                Route::delete('/{id}', [QuarterlyReportController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/send-to-admin', [QuarterlyReportController::class, 'sendToAdmin'])->name('send-to-admin');
            });

            // BCPC Case Monitoring
            Route::get('/bcpc-case-monitoring', [BcpcCaseMonitoringController::class, 'edit'])->name('bcpc-case-monitoring.edit');
            Route::post('/bcpc-case-monitoring', [BcpcCaseMonitoringController::class, 'update'])->name('bcpc-case-monitoring.update');
            Route::get('/bcpc-case-monitoring/print', [BcpcCaseMonitoringController::class, 'print'])->name('bcpc-case-monitoring.print');
            Route::post('/bcpc-case-monitoring/{id}/send-to-admin', [BcpcCaseMonitoringController::class, 'sendToAdmin'])->name('bcpc-case-monitoring.send-to-admin');
            Route::get('/bcpc-case-monitoring/archived', [BcpcCaseMonitoringController::class, 'archived'])->name('bcpc-case-monitoring.archived');
            Route::post('/bcpc-case-monitoring/{id}/archive', [BcpcCaseMonitoringController::class, 'archive'])->name('bcpc-case-monitoring.archive');
            Route::post('/bcpc-case-monitoring/{id}/restore', [BcpcCaseMonitoringController::class, 'restore'])->name('bcpc-case-monitoring.restore');
            Route::delete('/bcpc-case-monitoring/{id}', [BcpcCaseMonitoringController::class, 'forceDelete'])->name('bcpc-case-monitoring.force-delete');

            // Calendar
            Route::prefix('calendar')->name('calendar.')->group(function () {
                Route::get('/', [CalendarController::class, 'index'])->name('index');
                Route::post('/', [CalendarController::class, 'store'])->name('store');
                Route::post('/with-conflict', [CalendarController::class, 'storeWithConflict'])->name('store-with-conflict');
                Route::get('/{id}', [CalendarController::class, 'show'])->name('show');
                Route::delete('/{id}', [CalendarController::class, 'destroy'])->name('destroy');
            });
        });

        // Routes for both Administrators and Desk Officers
        Route::middleware(['position:Administrator,Desk Officer'])->group(function () {
            Route::get('/case-profiles', [CaseProfileController::class, 'index'])->name('case-profiles.index');
            
            Route::post('/notifications/mark-read', function(\Illuminate\Http\Request $request) {
                $user = Auth::user();
                if ($user) {
                    $notificationId = $request->input('notification_id');
                    
                    if ($notificationId) {
                        $readNotifications = $user->read_notifications ?? [];
                        if (!in_array($notificationId, $readNotifications)) {
                            $readNotifications[] = $notificationId;
                            $user->read_notifications = $readNotifications;
                            $user->save();
                            
                            return response()->json([
                                'success' => true,
                                'notification_id' => $notificationId
                            ]);
                        }
                        return response()->json(['success' => true, 'message' => 'Already marked as read']);
                    } else {
                        if ($user->position === 'Desk Officer' && $user->isMultiCaseDepartment()) {
                            $validated = $request->validate([
                                'notification_ids' => 'sometimes|array|max:100',
                                'notification_ids.*' => 'string|max:255',
                            ]);
                            $user->read_notifications = array_values(array_unique(array_merge(
                                $user->read_notifications ?? [],
                                $validated['notification_ids'] ?? []
                            )));
                        }
                        $user->notifications_last_viewed_at = now();
                        $user->save();
                        
                        return response()->json([
                            'success' => true,
                            'timestamp' => $user->notifications_last_viewed_at->toDateTimeString()
                        ]);
                    }
                }
                return response()->json(['success' => false, 'error' => 'Not authenticated'], 401);
            })->name('notifications.mark-read');
            
            Route::get('/admin-barangay-cases', [AdminController::class, 'casesIndexNew'])->name('admin-barangay-cases');
            
            // Case Management
            Route::prefix('admin/cases')->group(function () {
                Route::get('/add', [CaseController::class, 'create'])->name('admin-add-new-case');
                Route::post('/store', [CaseController::class, 'store'])->name('cases.store');
                Route::post('/{id}/restore', [CaseController::class, 'restoreArchivedCase'])->name('cases.restore');
                Route::delete('/{id}/force-delete', [CaseController::class, 'forceDelete'])->name('cases.force-delete');
                Route::delete('/{id}', [CaseController::class, 'destroy'])->name('cases.destroy')->where('id', '[0-9]+');
                Route::get('/{id}', [CaseController::class, 'show'])->name('cases.show')->where('id', '[0-9]+');
                Route::get('/{id}/edit', [CaseController::class, 'edit'])->name('cases.edit')->where('id', '[0-9]+');
                Route::put('/{id}', [CaseController::class, 'update'])->name('cases.update')->where('id', '[0-9]+');
                Route::post('/{id}/update', [CaseController::class, 'update'])->name('cases.inline-update')->where('id', '[0-9]+');
                Route::post('/{id}/refer/send', [CaseController::class, 'referCase'])->name('cases.refer')->where('id', '[0-9]+');
                Route::get('/{id}/refer', [CaseController::class, 'showReferralForm'])->name('cases.refer.form');
                Route::post('/{id}/refer', [CaseController::class, 'storeReferral'])->name('cases.refer.store');
            });
            
            // Referral Management Routes
            Route::prefix('admin/referrals')->group(function () {
                Route::get('/', [CaseController::class, 'referrals'])->name('admin.referrals.index');
                Route::get('/print-all', [CaseController::class, 'printAllReferrals'])->name('admin.referrals.print-all');
                Route::get('/{id}', [CaseController::class, 'showReferral'])->name('admin.referrals.show');
                Route::get('/{id}/print', [CaseController::class, 'printReferral'])->name('admin.referrals.print');
                Route::get('/{id}/details', [CaseController::class, 'showReferral'])->name('admin.referrals.details');
            });

            // Barangay Cases API
            Route::get('/api/barangay-cases/{barangay?}', [AdminController::class, 'getBarangayCases'])->name('api.barangay-cases');
            Route::get('/api/barangays', [BarangayController::class, 'index']);
            Route::get('/api/users-list', [AdminUserController::class, 'getUsersJson'])->name('api.users.list');
            
            // Dedicated Barangay Cases Views
            Route::get('/admin-barangay-cases/{barangay}', [AdminController::class, 'showBarangayCases'])->name('admin.barangay-cases.show');
            Route::get('/admin-barangay-cases-print', [AdminController::class, 'printAllBarangayCases'])->name('admin.barangay-cases.print');
            Route::get('/admin-barangay-cases/{barangay}/print', [AdminController::class, 'printBarangayCases'])->name('admin.barangay-cases.print-individual');
            Route::get('/admin/case/{case}/print', [CaseController::class, 'print'])->name('admin.case.print');
            Route::post('/admin/cases/{id}/send-copy', [CaseController::class, 'sendCopy'])->name('admin.case.sendCopy');
        });

        // Administrator-only Routes
        Route::middleware(['position:Administrator'])->group(function () {
            // User Management
            Route::prefix('admin/users')->group(function () {
                Route::get('/', [AdminController::class, 'usersIndex'])->name('admin.users.index');
                Route::get('/create', [AdminController::class, 'create'])->name('admin.users.create');
                Route::get('/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
                Route::get('/{user}/logs', [AdminController::class, 'userLogs'])->name('admin.users.logs');
                Route::get('/archived', [AdminController::class, 'archivedUsers'])->name('admin.users.archived');
                
                // User API Routes
                Route::prefix('api')->group(function () {
                    Route::get('/{user}/details', [UserController::class, 'show'])->name('api.users.details');
                    Route::post('/', [UserController::class, 'store'])->name('api.users.store');
                    Route::put('/{user}', [UserController::class, 'update'])->name('api.users.update');
                    Route::delete('/{user}', [UserController::class, 'archive'])->name('api.users.destroy');
                    Route::delete('/{user}/force-delete', [UserController::class, 'forceDelete'])->name('api.users.force-delete');
                    Route::post('/{user}', [UserController::class, 'forceDelete']);
                    Route::post('/{user}/restore', [UserController::class, 'restore'])->name('api.users.restore');
                    Route::post('/{user}/archive', [UserController::class, 'archive'])->name('api.users.archive');
                });

                Route::post('/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');
                Route::post('/{user}/department-access', [UserController::class, 'toggleDepartmentAccess'])->name('admin.users.department-access');
                Route::post('/{user}/archive', [UserController::class, 'archive'])->name('admin.users.archive');
            });
            
            // Referral Info Management
            Route::prefix('admin/referral-info')->group(function () {
                Route::get('/', [ReferralInfoController::class, 'index'])->name('admin.referral-info.index');
                Route::post('/', [ReferralInfoController::class, 'store'])->name('admin.referral-info.store');
                Route::put('/{id}', [ReferralInfoController::class, 'update'])->name('admin.referral-info.update');
                Route::delete('/{id}', [ReferralInfoController::class, 'destroy'])->name('admin.referral-info.destroy');
                Route::post('/{id}/toggle-status', [ReferralInfoController::class, 'toggleStatus'])->name('admin.referral-info.toggle-status');
            });
            
            // Quarterly Reports (Admin View)
            Route::prefix('admin/quarterly-reports')->name('admin.quarterly-reports.')->group(function () {
                Route::post('/generate-admin-cases', [QuarterlyReportController::class, 'generateAdminCases'])->name('generate-admin-cases');
                Route::post('/', [QuarterlyReportController::class, 'store'])->name('store');
                Route::get('/', [QuarterlyReportController::class, 'adminIndex'])->name('index');
                Route::get('/print-all', [QuarterlyReportController::class, 'printAll'])->name('print-all');
                Route::get('/summary', [QuarterlyReportController::class, 'summary'])->name('summary');
                Route::get('/summary/pdf', [QuarterlyReportController::class, 'summaryPdf'])->name('summary.pdf');
                Route::get('/archived', [QuarterlyReportController::class, 'adminArchived'])->name('archived');
                Route::post('/{id}/archive', [QuarterlyReportController::class, 'archive'])->name('archive');
                Route::post('/{id}/restore', [QuarterlyReportController::class, 'restore'])->name('restore');
                Route::delete('/{id}/force-delete', [QuarterlyReportController::class, 'forceDelete'])->name('force-delete');
                Route::get('/{id}/edit', [QuarterlyReportController::class, 'edit'])->name('edit');
                Route::post('/{id}/regenerate', [QuarterlyReportController::class, 'regenerate'])->name('regenerate');
                Route::get('/{id}', [QuarterlyReportController::class, 'show'])->name('show');
                Route::get('/{id}/download', [QuarterlyReportController::class, 'download'])->name('download');
            });

            // BCPC Case Monitoring (Admin View)
            Route::get('/admin/bcpc-case-monitoring', [AdminController::class, 'bcpcCaseMonitoring'])->name('admin.bcpc-case-monitoring.edit');
            Route::post('/admin/bcpc-case-monitoring', [AdminController::class, 'bcpcCaseMonitoringUpdate'])->name('admin.bcpc-case-monitoring.update');
            Route::get('/admin/bcpc-case-monitoring/print', [AdminController::class, 'bcpcCaseMonitoringPrint'])->name('admin.bcpc-case-monitoring.print');
            Route::get('/admin/bcpc-case-monitoring/archived', [AdminController::class, 'bcpcCaseMonitoringArchived'])->name('admin.bcpc-case-monitoring.archived');
            Route::post('/admin/bcpc-case-monitoring/{id}/archive', [AdminController::class, 'bcpcCaseMonitoringArchive'])->name('admin.bcpc-case-monitoring.archive');
            Route::post('/admin/bcpc-case-monitoring/{id}/restore', [AdminController::class, 'bcpcCaseMonitoringRestore'])->name('admin.bcpc-case-monitoring.restore');
            Route::delete('/admin/bcpc-case-monitoring/{id}', [AdminController::class, 'bcpcCaseMonitoringForceDelete'])->name('admin.bcpc-case-monitoring.force-delete');

            Route::prefix('admin/calendar')->name('admin.calendar.')->group(function () {
                Route::get('/', [CalendarController::class, 'index'])->name('index');
                Route::post('/', [CalendarController::class, 'store'])->name('store');
                Route::post('/with-conflict', [CalendarController::class, 'storeWithConflict'])->name('store-with-conflict');
                Route::get('/{id}', [CalendarController::class, 'show'])->name('show');
                Route::delete('/{id}', [CalendarController::class, 'destroy'])->name('destroy');
            });
        });

        // Route to send case report via email
        Route::post('/admin/cases/{id}/email', [CaseReportMailController::class, 'send'])
            ->name('admin.case.email');
            
        // Route to mark notifications as read
        Route::post('/admin/notifications/mark-read', function() {
            $user = Auth::user();
            if ($user) {
                $user->notifications_last_viewed_at = now();
                $user->save();
                
                return response()->json([
                    'success' => true,
                    'timestamp' => $user->notifications_last_viewed_at->toDateTimeString(),
                ]);
            }
            return response()->json(['success' => false, 'error' => 'Not authenticated'], 401);
        })->name('admin.notifications.mark-read');

        // Account management for admin
        Route::post('/admin/account/change-email', [AdminController::class, 'changeAccountEmail'])->name('admin.account.change-email');
        Route::put('/admin/account/settings', [AdminController::class, 'updateAccountSettings'])->name('admin.settings.update');

        // Change password for admin users (for modal AJAX)
        Route::post('/admin/users/{id}/change-password', [ChangePassController::class, 'changePassword'])->name('admin.users.change-password');
        Route::post('/admin/users/{id}/check-password', function(\Illuminate\Http\Request $request, $id) {
            $user = \App\Models\Wcpu\User::find($id);
            $valid = \Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password);
            return response()->json(['valid' => $valid]);
        })->name('admin.users.check-password');

        // Password reset request routes (admin only)
        Route::post('/admin/password-reset-requests/{id}/approve', [AdminController::class, 'approvePasswordResetRequest'])->name('admin.password-reset-requests.approve');
        Route::post('/admin/password-reset-requests/{id}/reject', [AdminController::class, 'rejectPasswordResetRequest'])->name('admin.password-reset-requests.reject');
        Route::post('/admin/password-reset-requests/{id}/dismiss', [AdminController::class, 'dismissPasswordResetRequest'])->name('admin.password-reset-requests.dismiss');

        // Referrals routes (admin)
        Route::get('/admin/archived-referrals', [AdminController::class, 'archivedReferrals'])->name('admin.referrals.archived');
        Route::post('/admin/archived-referrals/{type}/{id}/restore', [AdminController::class, 'restoreArchivedReferral'])->name('admin.referrals.archived.restore');
        Route::delete('/admin/archived-referrals/{type}/{id}', [AdminController::class, 'deleteArchivedReferral'])->name('admin.referrals.archived.delete');
        Route::post('/admin/referrals/{id}/archive', [AdminController::class, 'archiveReferral'])->name('admin.referrals.archive');
        Route::get('/admin/referrals', [AdminController::class, 'referrals'])->name('admin.referrals');
        Route::get('/admin/referrals/{id}', [AdminController::class, 'showReferral'])->name('admin.referrals.show');
        Route::get('/admin/referrals/{id}/details', [AdminController::class, 'showReferral'])->name('admin.referrals.details');
        Route::get('/admin/referrals/{id}/json', [AdminController::class, 'getReferralJson'])->name('admin.referrals.json');
        Route::post('/admin/referrals/{id}', [AdminController::class, 'updateReferral'])->name('admin.referrals.update');
        Route::get('/admin/referrals/{id}/print', [AdminController::class, 'printReferral'])->name('admin.referrals.print');
        Route::get('/admin/referrals-print-all', [AdminController::class, 'printAllReferrals'])->name('admin.referrals.print-all');
        Route::post('/admin/referrals-print-all/pdf', [AdminController::class, 'printEditableReferralRegistry'])->name('admin.referrals.print-edited-pdf');
        Route::post('/print/edited-pdf', [AdminController::class, 'printEditedDocument'])->name('print.edited-pdf');

        // AJAX route for dynamic user search/filter
        Route::get('/admin/users/ajax', [AdminUserController::class, 'ajaxTable'])
            ->name('admin.users.ajax')
            ->middleware(['position:Administrator']);
    });
});
