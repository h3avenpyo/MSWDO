@php
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
    // Get recent cases for notifications
    try {
        $recentCases = \App\Models\Wcpu\CaseModel::with('barangay')->orderBy('created_at', 'desc')->limit(10)->get();
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
        // Convert to UTC timestamp for consistent comparison
        $caseTimestamp = $case->created_at ? $case->created_at->getTimestamp() : $currentTime;
        
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
            'url' => url('/admin/cases/' . $case->id . '?action=created')
        ];
        
        // Only add status update as separate notification if it was updated AFTER creation
        if (!empty($case->status) && $case->updated_at && $case->created_at) {
            $updateTimestamp = $case->updated_at->getTimestamp();
            $createdTimestamp = $case->created_at->getTimestamp();
            
            // Only show status update if it happened at least 1 minute after creation
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
                    'url' => url('/admin/cases/' . $case->id . '?action=status_updated')
                ];
            }
        }
    }
    foreach (\App\Models\Wcpu\CaseReferral::latest()->limit(10)->get() as $referral) {
        $timestamp = $referral->created_at?->getTimestamp() ?? $currentTime;
        $id = 'referral_created_' . $referral->id;
        $activities[] = ['id' => $id, 'type' => 'referral_created', 'icon' => 'share-square', 'text' => 'Referral <strong>' . e($referral->referral_number ?? ('#' . $referral->id)) . '</strong> was added', 'time' => $referral->created_at?->diffForHumans() ?? 'Just now', 'timestamp' => $timestamp, 'unread' => !in_array($id, $readNotificationIds) && $timestamp > $lastViewedTime && $timestamp <= $currentTime, 'url' => route('admin.referrals.index')];
    }
    foreach (\App\Models\Wcpu\Event::forUser(Auth::user())->latest()->limit(10)->get() as $event) {
        $timestamp = $event->created_at?->getTimestamp() ?? $currentTime;
        $id = 'event_created_' . $event->id;
        $activities[] = ['id' => $id, 'type' => 'event_created', 'icon' => 'calendar-alt', 'text' => 'Event <strong>' . e($event->title) . '</strong> on ' . e($event->event_date?->format('M d, Y') ?? 'an upcoming date') . ' was added', 'time' => $event->created_at?->diffForHumans() ?? 'Just now', 'timestamp' => $timestamp, 'unread' => !in_array($id, $readNotificationIds) && $timestamp > $lastViewedTime && $timestamp <= $currentTime, 'url' => route('admin.calendar.index', ['event' => $event->id])];
    }
    foreach (\App\Models\Wcpu\QuarterlyReport::latest()->limit(10)->get() as $report) {
        $timestamp = $report->created_at?->getTimestamp() ?? $currentTime;
        $id = 'quarterly_report_' . $report->id;
        $activities[] = ['id' => $id, 'type' => 'quarterly_report', 'icon' => 'file-alt', 'text' => 'Quarterly report for <strong>Q' . e($report->quarter) . ' ' . e($report->year) . '</strong> was added', 'time' => $report->created_at?->diffForHumans() ?? 'Just now', 'timestamp' => $timestamp, 'unread' => !in_array($id, $readNotificationIds) && $timestamp > $lastViewedTime && $timestamp <= $currentTime, 'url' => route('admin.quarterly-reports.index')];
    }
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

<div class="notification-icon">
    <i class="fas fa-bell"></i>
    @if($unreadCount > 0)
    <span class="notification-badge" style="background-color: #eab308 !important; color: #ffffff !important;">{{ $unreadCount }}</span>
    @endif
</div>

<!-- Notification Dropdown -->
<div class="notification-dropdown">
    <div class="notification-header">
        <h4 style="color: #b91c1c;">Recent Activity</h4>
        <span class="mark-all-read" style="background-color: #b91c1c;">Mark all as read</span>
    </div>
    <div class="notification-list">
        @forelse($recentActivities as $activity)
            <a href="{{ $activity['url'] ?? '#' }}" 
               class="notification-item {{ $activity['unread'] ?? false ? 'unread' : '' }} {{ ($activity['is_password_reset'] ?? false) ? 'password-reset-item' : '' }}" 
               data-url="{{ $activity['url'] ?? '' }}"
               data-notification-id="{{ $activity['id'] ?? '' }}"
               data-is-password-reset="{{ ($activity['is_password_reset'] ?? false) ? 'true' : 'false' }}"
               data-request-id="{{ $activity['request_id'] ?? '' }}">
                <div class="notification-icon">
                    <i class="fas fa-{{ $activity['icon'] ?? 'bell' }}"></i>
                </div>
                <div class="notification-content">
                    <p>{!! $activity['text'] ?? 'No activity details' !!}</p>
                    <span class="notification-time">{{ $activity['time'] ?? 'Just now' }}</span>
                </div>
            </a>
        @empty
            <div class="notification-item">
                <div class="notification-content">
                    <p>No recent activity</p>
                </div>
            </div>
        @endforelse
    </div>
    <div class="notification-footer">
        <a href="#"></a>
    </div>
</div>

@push('scripts')
<script>
// Notification functionality - only add once
if (!window.notificationInitialized) {
    window.notificationInitialized = true;
    
    // Initialize immediately if DOM is already ready, otherwise wait for DOMContentLoaded
    function initNotifications() {
        console.log('Initializing notifications...');

        const notificationIcon = document.querySelector('.notification-icon');
        const notificationDropdown = document.querySelector('.notification-dropdown');
        const markAllReadBtn = document.querySelector('.mark-all-read');

        console.log('Elements found:', {
            notificationIcon: !!notificationIcon,
            notificationDropdown: !!notificationDropdown,
            markAllReadBtn: !!markAllReadBtn,
            csrfToken: !!document.querySelector('meta[name="csrf-token"]')
        });

        if (notificationIcon && notificationDropdown) {
            // Track last toggle time to avoid accidental clicks
            let lastToggleTime = 0;

            // Toggle dropdown on click
            notificationIcon.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
                lastToggleTime = Date.now();
                console.log('Notification dropdown toggled');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationIcon.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.remove('show');
                }
            });

            // Mark all as read functionality
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Ignore clicks that happen immediately after toggling dropdown (likely the same click)
                    const now = Date.now();
                    if (now - lastToggleTime < 300) {
                        console.log('Ignored accidental mark-as-read click during toggle');
                        return;
                    }
                    // Only allow marking when dropdown is visible
                    if (!notificationDropdown.classList.contains('show')) {
                        console.log('Dropdown not open; ignoring mark-as-read click');
                        return;
                    }
                    console.log('Mark all as read clicked!');

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        console.error('CSRF token not found!');
                        alert('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    console.log('Sending request to mark notifications as read...');

                    fetch('/notifications/mark-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => {
                        console.log('Response received:', response.status, response.statusText);
                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Mark as read response:', data);
                        if (data.success) {
                            // Remove unread styling
                            document.querySelectorAll('.notification-item.unread').forEach(item => {
                                item.classList.remove('unread');
                            });
                            // Hide the badge
                            const badge = document.querySelector('.notification-badge');
                            if (badge) {
                                badge.style.display = 'none';
                                console.log('Badge hidden');
                            }
                            // Reload page to refresh notifications with new timestamp
                            console.log('Reloading page in 500ms...');
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            console.error('Failed to mark as read:', data);
                            alert('Failed to mark notifications as read: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error marking notifications as read:', error);
                        alert('Failed to mark notifications as read. Error: ' + error.message);
                    });
                });
                console.log('Mark all as read button listener attached');
            } else {
                console.warn('Mark all as read button not found');
            }

            // Modal container for AJAX-loaded case details
            let notifModalOverlay = document.querySelector('#notif-modal-overlay');
            if (!notifModalOverlay) {
                notifModalOverlay = document.createElement('div');
                notifModalOverlay.id = 'notif-modal-overlay';
                notifModalOverlay.className = 'notif-modal-overlay';
                notifModalOverlay.style.cssText = `
                    position: fixed;
                    inset: 0;
                    display: none;
                    align-items: center;
                    justify-content: center;
                    background: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(4px);
                    z-index: 1200;
                    padding: 1.5rem;
                    animation: fadeIn 0.2s ease-out;
                `;
                
                const modalContent = document.createElement('div');
                modalContent.id = 'notif-modal';
                modalContent.className = 'notif-modal';
                modalContent.style.cssText = `
                    background: #ffffff;
                    border-radius: 12px;
                    max-width: 950px;
                    width: 100%;
                    max-height: 85vh;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                    box-shadow: 0 20px 60px rgba(106, 27, 154, 0.3), 0 10px 30px rgba(0, 0, 0, 0.2);
                    border-top: 4px solid #6a1b9a;
                    animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                `;
                
                const modalHeader = document.createElement('div');
                modalHeader.style.cssText = `
                    background: linear-gradient(135deg, #6a1b9a 0%, #9c27b0 100%);
                    color: white;
                    padding: 1.5rem 2rem;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                `;
                modalHeader.innerHTML = `
                    <h4 style="margin: 0; font-size: 1.25rem; font-weight: 600; display: flex; align-items: center;">
                        <i class="fas fa-file-alt" style="margin-right: 0.75rem; opacity: 0.9;"></i>
                        Case Details
                    </h4>
                    <button id="notif-modal-close-x" style="
                        background: rgba(255, 255, 255, 0.2);
                        border: none;
                        color: white;
                        width: 32px;
                        height: 32px;
                        border-radius: 6px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s ease;
                        font-size: 1.2rem;
                    " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                const modalBody = document.createElement('div');
                modalBody.id = 'notif-modal-body';
                modalBody.style.cssText = `
                    padding: 0;
                    overflow-y: auto;
                    flex: 1;
                    background: #f8f9fa;
                `;
                
                // Add disclaimer banner
                const disclaimer = document.createElement('div');
                disclaimer.style.cssText = `
                    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
                    border-left: 4px solid #f0ad4e;
                    padding: 0.875rem 1.25rem;
                    margin: 0;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    color: #856404;
                    font-size: 0.9rem;
                    border-bottom: 1px solid #e0e0e0;
                `;
                disclaimer.innerHTML = `
                    <i class="fas fa-info-circle" style="font-size: 1.1rem; color: #f0ad4e;"></i>
                    <span style="font-weight: 500;">This modal is view-only for notifications. To edit case details, please navigate to the <a href="#" id="notif-full-case-link" style="color: #6a1b9a; font-weight: 600; text-decoration: underline;">full case page</a>.</span>
                `;
                
                const modalBodyContent = document.createElement('div');
                modalBodyContent.id = 'notif-modal-body-content';
                modalBodyContent.style.cssText = `
                    padding: 2rem;
                `;
                
                modalBody.appendChild(disclaimer);
                modalBody.appendChild(modalBodyContent);
                
                const modalFooter = document.createElement('div');
                modalFooter.style.cssText = `
                    padding: 1.25rem 2rem;
                    background: white;
                    border-top: 1px solid #e0e0e0;
                    display: flex;
                    justify-content: flex-end;
                    gap: 0.75rem;
                `;
                modalFooter.innerHTML = `
                    <button id="notif-modal-close" style="
                        background: #6b7280;
                        border: none;
                        color: white;
                        padding: 0.625rem 1.5rem;
                        border-radius: 6px;
                        cursor: pointer;
                        font-weight: 500;
                        font-size: 0.95rem;
                        transition: all 0.2s ease;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                    " onmouseover="this.style.background='#4b5563'" onmouseout="this.style.background='#6b7280'">
                        <i class="fas fa-times"></i>
                        Close
                    </button>
                `;
                
                modalContent.appendChild(modalHeader);
                modalContent.appendChild(modalBody);
                modalContent.appendChild(modalFooter);
                notifModalOverlay.appendChild(modalContent);
                
                // Add animations
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes fadeIn {
                        from { opacity: 0; }
                        to { opacity: 1; }
                    }
                    @keyframes slideUp {
                        from { 
                            opacity: 0;
                            transform: translateY(30px) scale(0.95);
                        }
                        to { 
                            opacity: 1;
                            transform: translateY(0) scale(1);
                        }
                    }
                    #notif-modal-body::-webkit-scrollbar {
                        width: 8px;
                    }
                    #notif-modal-body::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 4px;
                    }
                    #notif-modal-body::-webkit-scrollbar-thumb {
                        background: #9c27b0;
                        border-radius: 4px;
                    }
                    #notif-modal-body::-webkit-scrollbar-thumb:hover {
                        background: #6a1b9a;
                    }
                `;
                document.head.appendChild(style);
                
                document.body.appendChild(notifModalOverlay);
            }

            function openNotificationModal(url) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const modalBodyContent = document.querySelector('#notif-modal-body-content');
                const notifModalOverlay = document.querySelector('#notif-modal-overlay');
                
                if (!modalBodyContent || !notifModalOverlay) return;
                
                // Show loading state
                modalBodyContent.innerHTML = `
                    <div style="text-align: center; padding: 3rem;">
                        <div style="
                            width: 50px;
                            height: 50px;
                            border: 4px solid #e1bee7;
                            border-top-color: #6a1b9a;
                            border-radius: 50%;
                            margin: 0 auto 1rem;
                            animation: spin 0.8s linear infinite;
                        "></div>
                        <p style="color: #666; font-size: 0.95rem;">Loading case details...</p>
                    </div>
                `;
                
                // Add spinner animation if not already present
                if (!document.getElementById('notif-modal-spinner-style')) {
                    const spinnerStyle = document.createElement('style');
                    spinnerStyle.id = 'notif-modal-spinner-style';
                    spinnerStyle.textContent = `
                        @keyframes spin {
                            to { transform: rotate(360deg); }
                        }
                        @keyframes highlightPulse {
                            0%, 100% { transform: scale(1); }
                            50% { transform: scale(1.02); }
                        }
                    `;
                    document.head.appendChild(spinnerStyle);
                }
                
                notifModalOverlay.style.display = 'flex';
                
                // Mark as read first (best-effort)
                if (csrfToken) {
                    fetch('/notifications/mark-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    }).catch(() => {});
                }

                // Fetch modal partial (AJAX)
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(resp => {
                    if (!resp.ok) throw new Error('Failed to load details');
                    return resp.text();
                })
                .then(html => {
                    modalBodyContent.innerHTML = html;
                    
                    // Update the full case page link to point to the barangay cases page
                    const fullCaseLink = document.getElementById('notif-full-case-link');
                    if (fullCaseLink) {
                        // Extract barangay info from the hidden metadata in the modal content
                        const metadataDiv = modalBodyContent.querySelector('[data-barangay-id]');
                        if (metadataDiv) {
                            const barangayId = metadataDiv.getAttribute('data-barangay-id');
                            const barangayName = metadataDiv.getAttribute('data-barangay-name');
                            
                            if (barangayId) {
                                const barangayUrl = `/admin-barangay-cases/${barangayId}`;
                                fullCaseLink.href = barangayUrl;
                                fullCaseLink.textContent = `${barangayName || 'Barangay'} Cases page`;
                                fullCaseLink.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    notifModalOverlay.style.display = 'none';
                                    window.location.href = barangayUrl;
                                });
                            }
                        }
                    }
                    
                    // Highlight relevant section based on action
                    const urlParams = new URLSearchParams(new URL(url, window.location.origin).search);
                    const action = urlParams.get('action');
                    
                    if (action === 'created') {
                        // Highlight the Date Reported section for new cases
                        setTimeout(() => {
                            const sections = modalBodyContent.querySelectorAll('.section-block');
                            sections.forEach(section => {
                                const heading = section.querySelector('.section-heading');
                                if (heading && heading.textContent.includes('Case Information')) {
                                    section.style.cssText = `
                                        background: linear-gradient(135deg, #f8f1ff 0%, #e1bee7 100%);
                                        border: 2px solid #9c27b0;
                                        border-radius: 8px;
                                        padding: 1.25rem;
                                        animation: highlightPulse 1.5s ease-in-out;
                                        box-shadow: 0 4px 12px rgba(156, 39, 176, 0.2);
                                    `;
                                    heading.innerHTML = '<i class="fas fa-star" style="color: #9c27b0; margin-right: 0.5rem;"></i>' + heading.textContent;
                                }
                            });
                        }, 100);
                    } else if (action === 'status_updated') {
                        // Highlight the Last Updated row for status changes
                        setTimeout(() => {
                            const infoRows = modalBodyContent.querySelectorAll('.info-row');
                            infoRows.forEach(row => {
                                const label = row.querySelector('.info-label');
                                if (label && label.textContent.includes('Last Updated')) {
                                    row.style.cssText = `
                                        background: linear-gradient(135deg, #f8f1ff 0%, #e1bee7 100%);
                                        border-left: 4px solid #9c27b0;
                                        padding: 0.75rem;
                                        margin: -0.25rem -0.5rem;
                                        border-radius: 6px;
                                        animation: highlightPulse 1.5s ease-in-out;
                                    `;
                                    label.innerHTML = '<i class="fas fa-star" style="color: #9c27b0; margin-right: 0.5rem;"></i>' + label.textContent;
                                }
                            });
                        }, 100);
                    }
                    
                    // Attach close handlers
                    const closeBtn = document.getElementById('notif-modal-close');
                    const closeBtnX = document.getElementById('notif-modal-close-x');
                    
                    const closeModal = () => {
                        notifModalOverlay.style.display = 'none';
                        // Hide badge after viewing
                        const badge = document.querySelector('.notification-badge');
                        if (badge) badge.style.display = 'none';
                    };
                    
                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    if (closeBtnX) closeBtnX.addEventListener('click', closeModal);
                    
                    // Close on overlay click (outside modal content)
                    notifModalOverlay.addEventListener('click', (ev) => {
                        if (ev.target === notifModalOverlay) closeModal();
                    });
                    
                    // Close on ESC key
                    const escHandler = (ev) => {
                        if (ev.key === 'Escape' && notifModalOverlay.style.display === 'flex') {
                            closeModal();
                            document.removeEventListener('keydown', escHandler);
                        }
                    };
                    document.addEventListener('keydown', escHandler);
                })
                .catch(err => {
                    console.error('Error loading notification details:', err);
                    modalBodyContent.innerHTML = `
                        <div style="text-align: center; padding: 3rem;">
                            <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #e74c3c; margin-bottom: 1rem;"></i>
                            <h4 style="color: #333; margin-bottom: 0.5rem;">Failed to Load Details</h4>
                            <p style="color: #666; margin-bottom: 1.5rem;">Unable to fetch case information. Please try again or open the case in a new tab.</p>
                            <a href="${url}" target="_blank" style="
                                display: inline-flex;
                                align-items: center;
                                gap: 0.5rem;
                                background: #6a1b9a;
                                color: white;
                                padding: 0.625rem 1.25rem;
                                border-radius: 6px;
                                text-decoration: none;
                                font-weight: 500;
                                transition: background 0.2s;
                            " onmouseover="this.style.background='#5a1682'" onmouseout="this.style.background='#6a1b9a'">
                                <i class="fas fa-external-link-alt"></i>
                                Open in New Tab
                            </a>
                        </div>
                    `;
                });
            }

            // Delegated click handler for notification items to open modal
            document.addEventListener('click', function(e) {
                const el = e.target.closest('.notification-item[data-url]');
                if (!el) return;
                // allow ctrl/cmd/middle click to open in new tab
                if (e.ctrlKey || e.metaKey || e.button === 1) return;
                e.preventDefault();
                
                const url = el.getAttribute('href') || el.dataset.url;
                const isPasswordReset = el.getAttribute('data-is-password-reset') === 'true';
                const notificationId = el.getAttribute('data-notification-id');
                
                // Mark this specific notification as read
                if (notificationId) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        fetch('/notifications/mark-read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.content,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                notification_id: notificationId
                            })
                        }).then(response => response.json())
                          .then(data => {
                              if (data.success) {
                                  // Remove unread class from this notification
                                  el.classList.remove('unread');
                                  // Update badge count
                                  updateBadgeCount();
                              }
                          })
                          .catch(err => console.error('Error marking notification as read:', err));
                    }
                }
                
                if (url) {
                    // Close the notification dropdown
                    const dropdown = document.querySelector('.notification-dropdown');
                    if (dropdown) dropdown.classList.remove('show');
                    
                    // Password reset requests: dismiss and reload
                    if (isPasswordReset) {
                        e.preventDefault();
                        const requestId = el.getAttribute('data-request-id');
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken && requestId) {
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
                        } else {
                            window.location.reload();
                        }
                    } else {
                        openNotificationModal(url);
                    }
                }
            });
            
            // Helper function to update badge count
            function updateBadgeCount() {
                const unreadItems = document.querySelectorAll('.notification-item.unread');
                const badge = document.querySelector('.notification-badge');
                const count = unreadItems.length;
                
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count;
                        badge.style.display = '';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        } else {
            console.warn('Notification elements not found on this page');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        // DOM already ready
        initNotifications();
    }
}
</script>
@endpush
