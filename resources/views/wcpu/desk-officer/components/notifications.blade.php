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

    // Recent cases in Desk Officer's barangay
    try {
        $casesQuery = \App\Models\Wcpu\CaseModel::query();
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
    foreach (\App\Models\Wcpu\Event::forUser($user)->latest()->limit(10)->get() as $event) {
        $timestamp = $event->created_at?->getTimestamp() ?? $currentTime;
        $id = 'desk_event_created_' . $event->id;
        $activities[] = ['id' => $id, 'type' => 'event_created', 'icon' => 'calendar-alt', 'text' => 'Event <strong>' . e($event->title) . '</strong> on ' . e($event->event_date?->format('M d, Y') ?? 'an upcoming date') . ' was added', 'time' => $event->created_at?->diffForHumans() ?? 'Just now', 'timestamp' => $timestamp, 'unread' => !in_array($id, $readNotificationIds) && $timestamp > $lastViewedTime && $timestamp <= $currentTime, 'url' => route('desk-officer.calendar.index', ['event' => $event->id])];
    }

    $activities = array_merge($activities, \App\Services\Wcpu\MultiroleActionNotifications::forUser($user));
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
<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false" @if($isMultiroleNotifications) data-multirole-notifications data-mark-read-url="{{ route('notifications.mark-read') }}" @endif>
    <button 
        @click="open = !open" 
        class="relative p-2 text-gray-600 hover:text-purple-700 hover:bg-purple-50 rounded-full transition-colors"
        aria-label="Notifications"
        title="Notifications"
    >
        <i class="fas fa-bell text-xl"></i>
        @if($unreadCount > 0)
        <span @if($isMultiroleNotifications) data-unread-badge @endif class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full" style="color: #ffffff !important;">
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
        class="notification-dropdown absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden"
        style="display: none; max-height: 50vh; max-height: 50dvh;"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-purple-700 to-purple-600 text-white">
            <h4 class="font-semibold text-sm">Notifications</h4>
            <button 
                id="deskMarkAllReadBtn"
                class="text-xs bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded-full transition-colors"
            >
                Mark all as read
            </button>
        </div>
        
        <!-- Notification List -->
        <div class="notification-dropdown-list max-h-80 overflow-y-auto divide-y divide-gray-100">
            @forelse($recentActivities as $activity)
            <a 
                href="{{ $activity['url'] ?? '#' }}" 
                @if(!empty($activity['onclick'])) onclick="{{ $activity['onclick'] }}" @endif
                class="flex items-start gap-3 p-4 hover:bg-gray-50 transition-colors desk-notification-item
                       {{ $activity['unread'] ?? false ? 'bg-purple-50/50 border-l-4 border-purple-500' : '' }}"
                data-notification-id="{{ $activity['id'] ?? '' }}"
                @if($isMultiroleNotifications) data-unread="{{ !empty($activity['unread']) ? 'true' : 'false' }}" @endif
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
            <span class="text-xs text-gray-400">Last 10 activities</span>
        </div>
    </div>
</div>

@push('scripts')
@if($isMultiroleNotifications)
<script src="{{ asset('js/multirole-notifications.js') }}?v={{ filemtime(public_path('js/multirole-notifications.js')) }}"></script>
@else
<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('deskMarkAllReadBtn');
    const showUnavailableNotification = () => {
        const message = 'This notification is no longer available.';
        if (window.Swal) {
            window.Swal.fire({ icon: 'info', text: message, confirmButtonColor: '#C41E3A' });
        } else {
            window.alert(message);
        }
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
                    document.querySelectorAll('.desk-notification-item').forEach(item => {
                        item.classList.remove('bg-purple-50/50', 'border-l-4', 'border-purple-500');
                    });
                    document.querySelectorAll('.desk-notification-item .bg-purple-500.rounded-full').forEach(dot => {
                        dot.style.display = 'none';
                    });
                    setTimeout(() => window.location.reload(), 300);
                }
            })
            .catch(console.error);
        });
    }

    document.querySelectorAll('.desk-notification-item').forEach(item => {
        item.addEventListener('click', async function(event) {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            const href = this.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) return;

            event.preventDefault();
            try {
                const response = await fetch(href, {
                    method: 'HEAD',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (response.ok) {
                    window.location.assign(href);
                    return;
                }
            } catch (_) {
                // A missing record is handled as an unavailable notification.
            }
            showUnavailableNotification();
        });
    });
});
</script>
@endif
@endpush
