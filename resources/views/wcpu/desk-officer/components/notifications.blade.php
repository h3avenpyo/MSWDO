@php
    $user = Auth::user();
    $isMultiroleNotifications = $user?->position === 'Desk Officer' && $user?->isMultiCaseDepartment();
    $barangayId = null;
    if ($user && $user->barangay) {
        $barangayRecord = \App\Models\Wcpu\Barangay::where('id', $user->barangay)
            ->orWhere('name', $user->barangay)
            ->first();
        $barangayId = $barangayRecord?->id;
    }

    $lastViewedTime = 0;
    if ($user && $user->notifications_last_viewed_at) {
        try {
            $lastViewedTime = $user->notifications_last_viewed_at->getTimestamp();
        } catch (\Exception $e) {
            $lastViewedTime = 0;
        }
    }

    $readNotificationIds = [];
    if ($user && $user->read_notifications) {
        $readNotificationIds = $user->read_notifications;
    }

    $currentTime = time();
    $activities = [];
    $isSwdDeskOfficer = $user?->position === 'Desk Officer'
        && $user?->defaultCaseDepartment() === 'VAWC';

    // Password reset notification for Desk Officer
    $passwordResetNoticeSeen = session('password_reset_notice_popup_displayed', false);
    $resetData = session('password_reset_notice');
    $passwordResetIsValid = $resetData && !empty($resetData['new_password'])
        && (!isset($resetData['expires_at']) || now()->lte(\Carbon\Carbon::parse($resetData['expires_at'])));
    if ($passwordResetIsValid && !$passwordResetNoticeSeen) {
        $activities[] = [
            'id' => $resetData['id'] ?? 'password_reset_notice',
            'type' => 'password_reset',
            'icon' => 'key',
            'text' => 'Your password was recently reset by the <strong>Administrator</strong>',
            'time' => isset($resetData['expires_at']) ? 'Available for 24 hours' : 'Just now',
            'timestamp' => $currentTime,
            'unread' => true,
            'url' => 'javascript:void(0)',
            'onclick' => "showPasswordResetModal('" . addslashes($resetData['email'] ?? '') . "', '" . addslashes($resetData['new_password'] ?? '') . "')",
        ];
    }

    // Recent cases belonging to the signed-in Desk Officer.
    try {
        $casesQuery = \App\Models\Wcpu\CaseModel::where('created_by', $user->id);
        if ($barangayId) {
            $casesQuery->where('case_designation', $barangayId);
        }
        if ($isSwdDeskOfficer) {
            $casesQuery->where('type', 'VAWC');
        }
        $recentCases = $casesQuery->orderBy('created_at', 'desc')->limit(10)->get();
    } catch (\Exception $e) {
        $recentCases = collect();
    }

    foreach($recentCases as $case) {
        $caseTimestamp = $case->created_at ? $case->created_at->getTimestamp() : $currentTime;
        $caseNotificationUrl = route('desk-officer.cases.show', ['hash' => encrypt($case->id)]);
        $notificationId = 'desk_case_created_' . $case->id;
        $isRead = in_array($notificationId, $readNotificationIds);
        $isUnread = !$isRead && ($caseTimestamp > $lastViewedTime) && ($caseTimestamp <= $currentTime);

        $activities[] = [
            'id' => $notificationId,
            'type' => 'case_created',
            'icon' => 'file-alt',
            'text' => 'New case <strong>#' . ($case->case_number ?? 'N/A') . '</strong> was recorded',
            'time' => $case->created_at ? $case->created_at->diffForHumans() : 'Just now',
            'timestamp' => $caseTimestamp,
            'unread' => $isUnread,
            'url' => $caseNotificationUrl,
        ];

        if (!empty($case->status) && $case->updated_at && $case->created_at) {
            $updateTimestamp = $case->updated_at->getTimestamp();
            $createdTimestamp = $case->created_at->getTimestamp();

            if ($updateTimestamp > $createdTimestamp + 60) {
                $statusNotificationId = 'desk_status_updated_' . $case->id;
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
                    'url' => $caseNotificationUrl,
                ];
            }
        }
    }
    foreach (\App\Models\Wcpu\CaseReferral::where('referred_by_user_id', $user?->id)->latest()->limit(10)->get() as $referral) {
        $timestamp = $referral->created_at?->getTimestamp() ?? $currentTime;
        $id = 'desk_referral_created_' . $referral->id;
        $activities[] = ['id' => $id, 'type' => 'referral_created', 'icon' => 'share-square', 'text' => 'Referral <strong>' . e($referral->referral_number ?? ('#' . $referral->id)) . '</strong> was added', 'time' => $referral->created_at?->diffForHumans() ?? 'Just now', 'timestamp' => $timestamp, 'unread' => !in_array($id, $readNotificationIds) && $timestamp > $lastViewedTime && $timestamp <= $currentTime, 'url' => route('desk-officer.referrals.show', $referral->id)];
    }
    foreach (\App\Models\Wcpu\Event::forUser($user)->where('created_by', $user->id)->latest()->limit(10)->get() as $event) {
        $timestamp = $event->created_at?->getTimestamp() ?? $currentTime;
        $id = 'desk_event_created_' . $event->id;
        $activities[] = ['id' => $id, 'type' => 'event_created', 'icon' => 'calendar-alt', 'text' => 'Event <strong>' . e($event->title) . '</strong> on ' . e($event->event_date?->format('M d, Y') ?? 'an upcoming date') . ' was added', 'time' => $event->created_at?->diffForHumans() ?? 'Just now', 'timestamp' => $timestamp, 'unread' => !in_array($id, $readNotificationIds) && $timestamp > $lastViewedTime && $timestamp <= $currentTime, 'url' => route('desk-officer.calendar.index', ['event' => $event->id])];
    }

    $activities = array_merge($activities, \App\Services\Wcpu\MultiroleActionNotifications::forUser($user, true));
    usort($activities, function($a, $b) {
        return $b['timestamp'] <=> $a['timestamp'];
    });
    if ($isMultiroleNotifications) {
        foreach ($activities as &$activity) {
            if ($activity['type'] === 'referral_created') {
                $activity['url'] = route('desk-officer.referrals.show', substr($activity['id'], strlen('desk_referral_created_')));
            } elseif ($activity['type'] === 'event_created') {
                $activity['url'] = route('desk-officer.calendar.index', ['event' => substr($activity['id'], strlen('desk_event_created_'))]);
            }
            if (in_array($activity['id'], $readNotificationIds)
                || $activity['timestamp'] <= $lastViewedTime) {
                $activity['unread'] = false;
            }
        }
        unset($activity);
    }
    $recentActivities = array_slice($activities, 0, 10);

    $unreadCount = count(array_filter($recentActivities, function($activity) {
        return $activity['unread'] ?? false;
    }));
@endphp

<!-- Notification Bell -->
<style>
    @media (max-width: 47.9375rem) {
        html body.portal-ui #mainContent#mainContent#mainContent [data-desk-phone-notifications] > .notification-dropdown {
            max-width: calc(100vw - 1.5rem) !important;
            max-height: min(29.625rem, calc(100dvh - var(--desk-notification-top, 4.5rem) - 1rem - env(safe-area-inset-bottom))) !important;
        }
        html body.portal-ui #mainContent#mainContent#mainContent [data-desk-phone-notifications] .notification-dropdown-list {
            min-height: 0 !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        html body.portal-ui #mainContent#mainContent#mainContent [data-desk-phone-notifications] .desk-notification-item p {
            display: block !important;
            white-space: normal !important;
            overflow: visible !important;
            overflow-wrap: anywhere;
            text-overflow: clip !important;
            font-size: .875rem !important;
            line-height: 1.5 !important;
            color: #374151 !important;
            -webkit-text-fill-color: #374151 !important;
        }
        html body.portal-ui #mainContent#mainContent#mainContent [data-desk-phone-notifications] .desk-notification-item p :is(strong, span, small) {
            color: inherit !important;
            -webkit-text-fill-color: inherit !important;
        }
        html body.portal-ui #mainContent#mainContent#mainContent [data-desk-phone-notifications] .desk-notification-item > div > span {
            display: block !important;
            white-space: normal !important;
            color: #b91c1c !important;
            -webkit-text-fill-color: #b91c1c !important;
        }
        html body.portal-ui [data-desk-phone-notifications] > .notification-dropdown {
            position: fixed !important;
            top: var(--desk-notification-top, 4.5rem) !important;
            left: auto !important;
            right: max(.75rem, env(safe-area-inset-right)) !important;
            width: 20rem !important;
            max-width: calc(100vw - 1.5rem) !important;
            margin-top: 0 !important;
            max-height: min(29.625rem, calc(100dvh - var(--desk-notification-top, 4.5rem) - 1rem - env(safe-area-inset-bottom))) !important;
            z-index: 100 !important;
            transform-origin: top center;
        }
    }
</style>
@if($user?->position === 'Desk Officer' && ($user->canAccessCaseDepartment('BCPC') || $user->canAccessCaseDepartment('VAWC')))
@include('admin-tailwind.components.notification-reference-style', [
    'notificationStyleScope' => 'html body.portal-ui #mainContent#mainContent [data-desk-phone-notifications]',
    'notificationReadButton' => '#deskMarkAllReadBtn',
    'notificationItemSelector' => '.desk-notification-item',
    'notificationMobileOnly' => true,
])
@endif
<div class="relative" data-desk-phone-notifications x-data="{ open: false, positionDropdown() { if (window.matchMedia('(max-width: 47.9375rem)').matches) { this.$refs.panel.style.setProperty('--desk-notification-top', (this.$refs.bell.getBoundingClientRect().bottom + 8) + 'px'); } } }" @resize.window="positionDropdown()" @scroll.window="if (open) positionDropdown()" @keydown.escape.window="open = false" data-multirole-notifications data-mark-read-url="{{ route('notifications.mark-read') }}">
    <button 
        x-ref="bell" @click="positionDropdown(); open = !open"
        class="relative p-2 text-gray-600 hover:text-purple-700 hover:bg-purple-50 rounded-full transition-colors"
        @if($user?->position === 'Desk Officer' && ($user->canAccessCaseDepartment('BCPC') || $user->canAccessCaseDepartment('VAWC')))
        style="background-color: #c00000 !important; background-image: none !important; border: 1px solid #8f0202 !important; color: #ffffff !important;"
        @endif
        aria-label="Notifications"
        title="Notifications"
    >
        <i class="fas fa-bell text-xl" @if($user?->position === 'Desk Officer' && ($user->canAccessCaseDepartment('BCPC') || $user->canAccessCaseDepartment('VAWC'))) style="color: #ffffff !important;" @endif></i>
        @if($unreadCount > 0)
        <span id="notifications-unread-count" class="notification-badge absolute -top-1 -right-1 bg-yellow-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full" data-unread-badge style="background-color: #eab308 !important; color: #ffffff !important; border: 0 !important; outline: none !important; box-shadow: none !important;">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>
    
    <!-- Dropdown -->
    <div 
        x-ref="panel" x-show="open"
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
            <h4 class="font-semibold text-sm text-white" style="color: #ffffff !important;">Notifications</h4>
            <button 
                id="deskMarkAllReadBtn"
                type="button"
                class="text-xs bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded-full transition-colors text-white"
                style="{{ request()->routeIs('desk-officer.dashboard', 'desk-officer.bcpc.dashboard', 'cases.refer.form', 'case-profiles.index', 'desk-officer.cases.index', 'desk-officer.cases.create', 'desk-officer.cases.show', 'admin.barangay-cases.show', 'desk-officer.referrals', 'desk-officer.referrals.show', 'desk-officer.calendar.index', 'desk-officer.quarterly-reports.index', 'desk-officer.quarterly-reports.create', 'desk-officer.quarterly-reports.show', 'desk-officer.quarterly-reports.edit', 'desk-officer.bcpc-case-monitoring.edit', 'desk-officer.archived-barangay-cases.index', 'desk-officer.referrals.archived', 'desk-officer.quarterly-reports.archived') ? 'background: #ffffff !important; color: #C41E3A !important;' : 'color: #ffffff !important;' }}"
            >
                Mark all as read
            </button>
        </div>
        
        <!-- Notification List -->
        <div id="recent-activity-list" class="notification-dropdown-list min-h-0 flex-1 overflow-y-auto divide-y divide-gray-100">
            @forelse($recentActivities as $activity)
            <a 
                href="{{ $activity['url'] ?? '#' }}" 
                @if(!empty($activity['onclick'])) onclick="{{ $activity['onclick'] }}" @endif
                class="flex items-start gap-3 p-4 hover:bg-gray-50 transition-colors desk-notification-item
                       {{ $activity['unread'] ?? false ? 'bg-purple-50/50 border-l-4 border-purple-500' : '' }}"
                data-notification-id="{{ $activity['id'] ?? '' }}"
                data-unread="{{ !empty($activity['unread']) ? 'true' : 'false' }}"
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
            <span class="text-xs" style="color: #6b7280 !important;">Last 10 activities</span>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/multirole-notifications.js') }}?v={{ filemtime(public_path('js/multirole-notifications.js')) }}"></script>
@endpush
