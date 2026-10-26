@php
    $usesReferenceNotificationStyle = auth()->user()?->isDepartmentAdmin() && request()->routeIs(
        'admin-dashboard', 'case-profiles.index', 'cases.show', 'admin-barangay-cases',
        'admin.barangay-cases.show', 'admin-add-new-case', 'admin.users.index',
        'admin.users.create', 'admin.users.logs', 'admin.referrals', 'admin.referrals.index',
        'admin.referral-info.index', 'admin.quarterly-reports.index', 'admin.quarterly-reports.show',
        'admin.quarterly-reports.edit', 'admin.bcpc-case-monitoring.edit', 'admin.calendar.index',
        'admin.quarterly-reports.archived', 'admin.referrals.archived', 'admin.users.archived',
        'admin.bcpc-case-monitoring.archived', 'cases.refer.form'
    );
    // Get last notification view time from database (user record)
    $lastViewedTime = 0;
    if (Auth::check() && Auth::user()->notifications_last_viewed_at) {
        try {
            $lastViewedTime = Auth::user()->notifications_last_viewed_at->getTimestamp();
        } catch (\Exception $e) {
            $lastViewedTime = 0;
        }
    }
    
    // Get read notification IDs from user record
    $readNotificationIds = [];
    if (Auth::check() && Auth::user()->read_notifications) {
        $readNotificationIds = Auth::user()->read_notifications;
    }
    
    // Use current UTC timestamp for comparison
    $currentTime = time();
    
    $activities = [];
    $isSwdDeskOfficer = Auth::check()
        && Auth::user()->position === 'Desk Officer'
        && Auth::user()->defaultCaseDepartment() === 'VAWC';
    $isBcpcDeskOfficer = Auth::check()
        && Auth::user()->position === 'Desk Officer'
        && Auth::user()->defaultCaseDepartment() === 'BCPC';
    $deskOfficerBarangayId = null;
    if ($isSwdDeskOfficer || $isBcpcDeskOfficer) {
        $deskOfficerBarangayId = \App\Models\Wcpu\Barangay::where('id', Auth::user()->barangay)
            ->orWhere('name', Auth::user()->barangay)
            ->value('id');
    }

    // Get recent cases for notifications
    try {
        $recentCases = \App\Models\Wcpu\CaseModel::with('barangay')
            ->when(Auth::user()->isVawcAdmin(), fn ($query) => $query->where('type', 'VAWC'))
            ->when($isSwdDeskOfficer, fn ($query) => $query
                ->where('type', 'VAWC')
                ->where('case_designation', $deskOfficerBarangayId ?: 0))
            ->when($isBcpcDeskOfficer, fn ($query) => $query
                ->where('type', 'BCPC')
                ->where('case_designation', $deskOfficerBarangayId ?: 0))
            ->orderBy('created_at', 'desc')->limit(10)->get();
    } catch (\Exception $e) {
        $recentCases = collect();
    }
    
    // Get pending password reset requests (Admin only)
    $passwordResetRequests = collect();
    if (Auth::check() && strtolower(Auth::user()->position) === 'administrator') {
        try {
            $passwordResetRequests = \App\Models\Wcpu\PasswordResetRequest::with('user')
                ->where('status', 'pending')
                ->orderBy('requested_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            $passwordResetRequests = collect();
        }
    }
    
    // Filter out dismissed password reset requests
    $dismissedRequests = [];
    if (Auth::check()) {
        foreach ($passwordResetRequests as $resetRequest) {
            if (session('dismissed_password_request_' . $resetRequest->id)) {
                $dismissedRequests[] = $resetRequest->id;
            }
        }
        $passwordResetRequests = $passwordResetRequests->reject(function($request) use ($dismissedRequests) {
            return in_array($request->id, $dismissedRequests);
        });
    }
    
    // Add password reset request activities
    foreach($passwordResetRequests as $resetRequest) {
        $requestTimestamp = $resetRequest->requested_at ? $resetRequest->requested_at->getTimestamp() : $currentTime;
        $notificationId = 'password_reset_' . $resetRequest->id;
        $isRead = in_array($notificationId, $readNotificationIds);
        $isUnread = !$isRead && ($requestTimestamp > $lastViewedTime) && ($requestTimestamp <= $currentTime);
        
        $activities[] = [
            'id' => $notificationId,
            'type' => 'password_reset_request',
            'icon' => 'key',
            'text' => 'User <strong>' . ($resetRequest->username ?? 'Unknown') . '</strong> requested a password reset',
            'time' => $resetRequest->requested_at ? $resetRequest->requested_at->diffForHumans() : 'Just now',
            'timestamp' => $requestTimestamp,
            'unread' => $isUnread,
            'url' => url('/admin/users?tab=password-requests&reset_request=' . $resetRequest->id),
            'is_password_reset' => true,
            'request_id' => $resetRequest->id
        ];
    }


    
    // Add case creation activities
    foreach($recentCases as $case) {
        $caseTimestamp = $case->created_at ? $case->created_at->getTimestamp() : $currentTime;
        $caseNotificationUrl = ($isSwdDeskOfficer || $isBcpcDeskOfficer)
            ? route('desk-officer.cases.show', ['hash' => encrypt($case->id)])
            : url('/admin/cases/' . $case->id . '?action=created');
        
        $notificationId = 'case_created_' . $case->id;
        $isRead = in_array($notificationId, $readNotificationIds);
        $isUnread = !$isRead && ($caseTimestamp > $lastViewedTime) && ($caseTimestamp <= $currentTime);
        
        $activities[] = [
            'id' => $notificationId,
            'type' => 'case_created',
            'icon' => 'file-alt',
            'text' => 'New case <strong>#' . ($case->case_number ?? 'N/A') . '</strong> was created',
            'time' => $case->created_at ? $case->created_at->diffForHumans() : 'Just now',
            'timestamp' => $caseTimestamp,
            'unread' => $isUnread,
            'url' => $caseNotificationUrl
        ];
        
        // Only add status update as separate notification if it was updated AFTER creation
        if (!empty($case->status) && $case->updated_at && $case->created_at) {
            $updateTimestamp = $case->updated_at->getTimestamp();
            $createdTimestamp = $case->created_at->getTimestamp();
            
            if ($updateTimestamp > $createdTimestamp + 60) {
                $statusNotificationId = 'status_updated_' . $case->id;
                $isStatusRead = in_array($statusNotificationId, $readNotificationIds);
                $isUpdateUnread = !$isStatusRead && ($updateTimestamp > $lastViewedTime) && ($updateTimestamp <= $currentTime);
                
                $activities[] = [
                    'id' => $statusNotificationId,
                    'type' => 'status_updated',
                    'icon' => 'check-circle',
                    'text' => 'Case <strong>#' . ($case->case_number ?? 'N/A') . '</strong> status set to <strong>' . $case->status . '</strong>',
                    'time' => $case->updated_at->diffForHumans(),
                    'timestamp' => $updateTimestamp,
                    'unread' => $isUpdateUnread,
                    'url' => $caseNotificationUrl
                ];
            }
        }
    }

    // Calendar events are presented from persisted Event records, so one event creates one notification per eligible user.
    try {
        $calendarEvents = \App\Models\Wcpu\Event::forUser(Auth::user())->with('creator')->latest()->limit(10)->get();
    } catch (\Exception $e) {
        $calendarEvents = collect();
    }
    foreach ($calendarEvents as $event) {
        $eventTimestamp = $event->created_at?->getTimestamp() ?? $currentTime;
        $notificationId = 'event_created_' . $event->id;
        $activities[] = [
            'id' => $notificationId,
            'type' => 'event_created',
            'icon' => 'calendar-alt',
            'text' => 'Event <strong>' . e($event->title) . '</strong> on ' . e($event->event_date?->format('M d, Y') ?? 'an upcoming date') . ' was added',
            'time' => $event->created_at?->diffForHumans() ?? 'Just now',
            'timestamp' => $eventTimestamp,
            'unread' => !in_array($notificationId, $readNotificationIds) && $eventTimestamp > $lastViewedTime && $eventTimestamp <= $currentTime,
            'url' => route(Auth::user()->position === 'Desk Officer' ? 'desk-officer.calendar.index' : 'admin.calendar.index', ['event' => $event->id]),
        ];
    }
    
    // Get quarterly reports (Admin only)
    $quarterlyReports = collect();
    if (Auth::check() && strtolower(Auth::user()->position) === 'administrator') {
        try {
            $quarterlyReports = \App\Models\Wcpu\QuarterlyReport::with(['user', 'barangay'])
                ->when(Auth::user()->isVawcAdmin(), fn ($query) => $query->where('type', 'VAWC'))
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $quarterlyReports = collect();
        }
    }
    
    // Add quarterly report activities
    foreach($quarterlyReports as $report) {
        $reportTimestamp = $report->created_at ? $report->created_at->getTimestamp() : $currentTime;
        $notificationId = 'quarterly_report_' . $report->id;
        $isRead = in_array($notificationId, $readNotificationIds);
        $isUnread = !$isRead && ($reportTimestamp > $lastViewedTime) && ($reportTimestamp <= $currentTime);
        
        $activities[] = [
            'id' => $notificationId,
            'type' => 'quarterly_report',
            'icon' => 'file-alt',
            'text' => '<strong>' . ($report->barangay->name ?? 'Unknown') . '</strong> Q' . $report->quarter . ' ' . $report->year . ' quarterly report submitted',
            'time' => $report->created_at ? $report->created_at->diffForHumans() : 'Just now',
            'timestamp' => $reportTimestamp,
            'unread' => $isUnread,
            'url' => route('admin.quarterly-reports.show', $report->id)
        ];
    }

    // Show BCPC monitoring reports submitted by desk officers to BCPC admins.
    $bcpcMonitorings = collect();
    if (Auth::check() && Auth::user()->canAccessCaseDepartment('BCPC')) {
        try {
            $bcpcMonitorings = \App\Models\Wcpu\BcpcCaseMonitoring::with(['barangay', 'submitter'])
                ->whereNotNull('submitted_at')
                ->whereNull('archived_at')
                ->when($isBcpcDeskOfficer, fn ($query) => $query->where('barangay_id', $deskOfficerBarangayId ?: 0))
                ->latest('submitted_at')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            $bcpcMonitorings = collect();
        }
    }

    foreach ($bcpcMonitorings as $monitoring) {
        $monitoringTimestamp = $monitoring->submitted_at->getTimestamp();
        $notificationId = 'bcpc_monitoring_submitted_' . $monitoring->id;
        $isRead = in_array($notificationId, $readNotificationIds);
        $isUnread = !$isRead && ($monitoringTimestamp > $lastViewedTime) && ($monitoringTimestamp <= $currentTime);

        $activities[] = [
            'id' => $notificationId,
            'type' => 'bcpc_monitoring_submitted',
            'icon' => 'child',
            'text' => '<strong>' . e($monitoring->barangay->name ?? 'BCPC') . '</strong> Q' . $monitoring->quarter . ' ' . $monitoring->year . ' BCPC monitoring submitted by ' . e($monitoring->submitter?->full_name ?? 'Desk Officer'),
            'time' => $monitoring->submitted_at->diffForHumans(),
            'timestamp' => $monitoringTimestamp,
            'unread' => $isUnread,
            'url' => $isBcpcDeskOfficer
                ? route('desk-officer.bcpc-case-monitoring.edit', ['view' => $monitoring->id])
                : route('admin.bcpc-case-monitoring.edit', ['view' => $monitoring->id]),
        ];
    }

    // Include recorded user actions so the notification bell reflects actions
    // performed throughout the portal, not only cases and reports.
    try {
        $recentActions = Auth::user()->isBcpcAdmin()
            ? \Spatie\Activitylog\Models\Activity::with('causer')->where('log_name', '!=', 'multirole_workflow')->latest()->limit(10)->get()
            : (Auth::user()->isVawcAdmin()
                ? \Spatie\Activitylog\Models\Activity::with('causer')->where('log_name', '!=', 'multirole_workflow')
                    ->whereHas('causer', fn ($query) => $query->whereRaw('LOWER(department) LIKE ?', ['%vawc%']))
                    ->latest()->limit(10)->get()
                : collect());
    } catch (\Exception $e) {
        $recentActions = collect();
    }

    foreach ($recentActions as $action) {
        $actionTimestamp = $action->created_at ? $action->created_at->getTimestamp() : $currentTime;
        $notificationId = 'action_' . $action->id;
        $isRead = in_array($notificationId, $readNotificationIds);
        $actionSubject = class_basename($action->subject_type ?? '');
        $actionUrl = match ($actionSubject) {
            'CaseModel' => route('cases.show', $action->subject_id),
            'CaseReferral' => route('admin.referrals.show', $action->subject_id),
            'QuarterlyReport' => route('admin.quarterly-reports.show', $action->subject_id),
            'User' => route('admin.users.logs', $action->subject_id),
            default => route('admin.users.logs', $action->causer_id ?: Auth::id()),
        };

        $activities[] = [
            'id' => $notificationId,
            'type' => 'portal_action',
            'icon' => 'history',
            'text' => e($action->description ?: 'Portal action recorded'),
            'time' => $action->created_at ? $action->created_at->diffForHumans() : 'Just now',
            'timestamp' => $actionTimestamp,
            'unread' => !$isRead && ($actionTimestamp > $lastViewedTime) && ($actionTimestamp <= $currentTime),
            'url' => $actionUrl,
        ];
    }
    
    $activities = array_merge($activities, \App\Services\Wcpu\MultiroleActionNotifications::forUser(Auth::user()));
    // Sort activities by timestamp (newest first)
    usort($activities, function($a, $b) {
        return $b['timestamp'] <=> $a['timestamp'];
    });
    $recentActivities = array_slice($activities, 0, 10);
    
    // Count only unread notifications
    $unreadCount = count(array_filter($activities, function($activity) {
        return $activity['unread'] ?? false;
    }));
@endphp

<!-- Notification Bell -->
@if($usesReferenceNotificationStyle)
    @include('admin-tailwind.components.notification-reference-style')
@endif
<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false" @if($usesReferenceNotificationStyle) data-admin-notification-reference @endif>
    <button 
        @click="open = !open" 
        class="relative p-2 {{ auth()->user()->isBcpcAdmin() ? 'text-white border border-white hover:text-white hover:bg-white/10' : 'text-gray-600 hover:text-purple-700 hover:bg-purple-50' }} rounded-full transition-colors"
        aria-label="Notifications"
        @if(request()->routeIs('desk-officer.cases.show') && auth()->user()?->position === 'Desk Officer' && (auth()->user()->canAccessCaseDepartment('BCPC') || auth()->user()->canAccessCaseDepartment('VAWC')))
        style="background-color: #c00000 !important; background-image: none !important; border: 1px solid #8f0202 !important; color: #ffffff !important;"
        @endif
    >
        <i class="fas fa-bell text-xl"></i>
        @if($unreadCount > 0)
        <span id="notifications-unread-count" class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full" style="background-color: #eab308 !important; color: #ffffff !important;">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>
    
    <!-- Dropdown -->
    <div 
        x-show="open" 
        x-cloak
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="notification-dropdown absolute right-0 mt-2 w-80 max-h-[50vh] flex flex-col bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden"
        style="display: none; max-height: 50vh; max-height: 50dvh;"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-purple-700 to-purple-600 text-white">
            <h4 class="font-semibold text-sm" @if($usesReferenceNotificationStyle) style="color: #ffffff !important;" @endif>{{ $usesReferenceNotificationStyle ? 'Notifications' : 'Recent Activity' }}</h4>
            <button 
                id="markAllReadBtn"
                type="button"
                class="text-xs bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded-full transition-colors {{ auth()->user()?->isDepartmentAdmin() && request()->routeIs('admin-dashboard') ? 'text-white' : '' }}"
                @if(request()->routeIs('desk-officer.cases.show')) style="background: #ffffff !important; color: #C41E3A !important;" @endif
            >
                Mark all as read
            </button>
        </div>
        
        <!-- Notification List -->
        <div id="recent-activity-list" class="notification-dropdown-list min-h-0 flex-1 overflow-y-auto divide-y divide-gray-100">
            @forelse($recentActivities as $activity)
            <a 
                href="{{ $activity['url'] ?? '#' }}" 
                class="flex items-start gap-3 p-4 hover:bg-gray-50 transition-colors notification-item
                       {{ $activity['unread'] ?? false ? 'bg-purple-50/50 border-l-4 border-purple-500' : '' }}"
                data-url="{{ $activity['url'] ?? '' }}"
                data-notification-id="{{ $activity['id'] ?? '' }}"
                data-is-password-reset="{{ ($activity['is_password_reset'] ?? false) ? 'true' : 'false' }}"
                data-request-id="{{ $activity['request_id'] ?? '' }}"
                @if(isset($activity['action_details'])) data-action-details='@json($activity['action_details'])' @endif
            >
                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                    <i class="fas fa-{{ $activity['icon'] ?? 'bell' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm leading-snug" style="display:block !important;color:#374151 !important;">{!! $activity['text'] ?? 'No activity details' !!}</p>
                    <span class="text-xs mt-1 block" style="color:#b91c1c !important;">{{ $activity['time'] ?? 'Just now' }}</span>
                </div>
                @if($activity['unread'] ?? false)
                <div class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0 mt-2"></div>
                @endif
            </a>
            @empty
            <div class="p-6 text-center text-gray-400">
                <i class="fas fa-bell-slash text-3xl mb-2"></i>
                <p class="text-sm">No recent activity</p>
            </div>
            @endforelse
        </div>
        
        <!-- Footer -->
        <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
            <span class="text-xs" style="color:#6b7280 !important;">Last 10 activities</span>
        </div>
    </div>
</div>

<dialog id="notification-action-details" class="m-auto w-full max-w-md rounded-2xl border-0 p-0 shadow-2xl backdrop:bg-black/50">
    <div class="p-6">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <h4 class="text-lg font-bold text-gray-800">Desk Officer Action</h4>
                <p class="text-sm text-gray-500">Notification details</p>
            </div>
            <button type="button" id="notification-action-details-close" class="text-gray-400 hover:text-gray-700 text-xl" aria-label="Close">&times;</button>
        </div>
        <dl class="space-y-4 text-sm">
            <div><dt class="font-semibold text-gray-500">Action</dt><dd id="notification-action-value" class="mt-1 text-gray-800"></dd></div>
            <div><dt class="font-semibold text-gray-500">Performed by</dt><dd id="notification-officer-value" class="mt-1 text-gray-800"></dd></div>
            <div><dt class="font-semibold text-gray-500">Performed at</dt><dd id="notification-time-value" class="mt-1 text-gray-800"></dd></div>
        </dl>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" id="notification-action-details-cancel" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">Close</button>
            <a id="notification-action-details-link" href="#" class="px-4 py-2 rounded-lg bg-[#C41E3A] hover:bg-[#A01830] text-white text-sm font-medium">View Record</a>
        </div>
    </div>
</dialog>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const actionDetailsDialog = document.getElementById('notification-action-details');
    const closeActionDetails = () => actionDetailsDialog?.close();
    document.getElementById('notification-action-details-close')?.addEventListener('click', closeActionDetails);
    document.getElementById('notification-action-details-cancel')?.addEventListener('click', closeActionDetails);
    const showUnavailableNotification = () => {
        const message = 'This notification is no longer available.';
        if (window.Swal) {
            window.Swal.fire({ icon: 'info', text: message, confirmButtonColor: '#C41E3A' });
        } else {
            window.alert(message);
        }
    };
    const openNotificationDestination = async (url) => {
        try {
            const response = await fetch(url, {
                method: 'HEAD',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (response.ok) {
                window.location.assign(url);
                return;
            }
        } catch (_) {
            // Treat an unavailable destination as a stale notification.
        }
        showUnavailableNotification();
    };
    
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) return;
            
            fetch('/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread styling
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('bg-purple-50/50', 'border-l-4', 'border-purple-500');
                    });
                    // Hide all unread dots
                    document.querySelectorAll('.notification-item .bg-purple-500.rounded-full').forEach(dot => {
                        dot.style.display = 'none';
                    });
                    @if(auth()->user()?->isDepartmentAdmin())
                    document.getElementById('notifications-unread-count')?.remove();
                    @else
                    setTimeout(() => window.location.reload(), 300);
                    @endif
                }
            })
            .catch(console.error);
        });
    }
    
    // Handle notification clicks
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.ctrlKey || e.metaKey) return;
            
            const isPasswordReset = this.getAttribute('data-is-password-reset') === 'true';
            const url = this.getAttribute('href');
            const notificationId = this.getAttribute('data-notification-id');
            const requestId = this.getAttribute('data-request-id');
            const actionDetails = this.getAttribute('data-action-details');
            
            // Mark as read
            if (notificationId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    fetch('/notifications/mark-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ notification_id: notificationId })
                    }).catch(() => {});
                }
            }
            
            // Password reset: dismiss the notification and reload
            if (isPasswordReset && requestId) {
                e.preventDefault();
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    fetch('/admin/password-reset-requests/' + requestId + '/dismiss', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.content,
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        window.location.reload();
                    }).catch(() => {
                        window.location.reload();
                    });
                }
            }

            // Each notification is a direct link to its related record or activity.
            if (!isPasswordReset && url && url !== '#' && !url.startsWith('javascript:')) {
                e.preventDefault();
                openNotificationDestination(url);
            }
        });
    });

    if (@json(auth()->user()->isVawcAdmin())) {
        let refreshing = false;
        const refreshRecentActivity = async () => {
            if (refreshing || document.hidden) return;
            refreshing = true;
            try {
                const response = await fetch(window.location.href, {
                    cache: 'no-store',
                    @if(auth()->user()->isVawcAdmin() && request()->routeIs('cases.show'))
                    headers: { 'Accept': 'text/html' },
                    @else
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    @endif
                });
                if (!response.ok) return;
                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                @if(auth()->user()->isVawcAdmin() && request()->routeIs('cases.show'))
                if (!page.querySelector('[aria-label="Notifications"]')) return;
                @endif
                const nextList = page.querySelector('#recent-activity-list');
                const currentList = document.querySelector('#recent-activity-list');
                if (nextList && currentList && nextList.innerHTML !== currentList.innerHTML) {
                    currentList.innerHTML = nextList.innerHTML;
                }

                const nextCount = page.querySelector('#notifications-unread-count');
                const currentCount = document.querySelector('#notifications-unread-count');
                if (nextCount && currentCount) {
                    currentCount.textContent = nextCount.textContent;
                } else if (nextCount && !currentCount) {
                    document.querySelector('[aria-label="Notifications"]')?.append(nextCount.cloneNode(true));
                } else if (!nextCount && currentCount) {
                    currentCount.remove();
                }
            } catch (_) {
                // Keep the currently displayed activity list if a refresh is unavailable.
            } finally {
                refreshing = false;
            }
        };

        window.setInterval(refreshRecentActivity, 3000);
        window.addEventListener('focus', refreshRecentActivity);
    }
});
</script>
@endpush
