@extends('admin.social-case.layout')
@section('title', 'Dashboard - Social Case Study')

@section('content')
<!-- Mobile Header (visible only on mobile) -->
@php
$logo = null;
if(file_exists(public_path('images/mswdo-logo.png'))){
    $logo='mswdo-logo.png';
}else{
    $files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);
    if(!empty($files))
    $logo=basename($files[0]);
}
@endphp
<div class="mobile-header">
    <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
    <div class="mobile-header-brand">
        <div class="mobile-brand-text">
            <h1 class="mobile-brand-title">iSERVE SILANG</h1>
            <p class="mobile-brand-subtitle">Social Case Study Dashboard</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<style>
    /* Dashboard-scoped overrides */
    .main {
        padding-top: 14px !important;
    }
    @media (max-width: 767.98px) {
        .main {
            padding-top: 72px !important;
        }
    }

    /* Mobile welcome message */
    .mobile-welcome-message {
        display: none;
    }
    @media (max-width: 767.98px) {
        .mobile-welcome-message {
            display: block;
            padding: 16px;
            background: #F5F7FB;
            margin: 0 -16px 16px -16px;
            text-align: center;
        }
        .mobile-welcome-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
    }
    .main > header {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    /* Role banners: never clip text, flex-wrap on mobile */
    .role-banner {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        flex-wrap: nowrap;
    }
    .role-banner i, .role-banner svg {
        flex-shrink: 0;
        margin-top: 2px;
    }
    .role-banner-text {
        flex: 1;
        min-width: 0;
    }
    .role-banner-title {
        font-weight: 700;
        font-size: 14px;
        line-height: 1.4;
    }
    .role-banner-subtitle {
        font-size: 13px;
        line-height: 1.5;
        white-space: normal;
        overflow-wrap: break-word;
        word-break: break-word;
    }
    @media (max-width: 575.98px) {
        .role-banner { padding: 12px 14px; gap: 10px; }
        .role-banner-title { font-size: 13px; }
        .role-banner-subtitle { font-size: 12px; }
    }

    /* ── Notification Bell & Dropdown Styles ── */
    .notif-bell-btn {
        position: relative;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        outline: none;
    }
    .notif-bell-btn:hover {
        background: #EEF2FF;
        color: #4338CA;
        border-color: #C7D2FE;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(67, 56, 202, 0.15);
    }
    .notif-bell-btn.active {
        background: #EEF2FF;
        color: #4338CA;
        border-color: #818CF8;
    }
    .notif-badge {
        position: absolute;
        top: -3px;
        right: -3px;
        min-width: 19px;
        height: 19px;
        padding: 0 5px;
        border-radius: 9999px;
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #FFFFFF;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.35);
        z-index: 2;
        line-height: 1;
    }
    .notif-ping {
        position: absolute;
        top: -3px;
        right: -3px;
        width: 19px;
        height: 19px;
        border-radius: 9999px;
        background: #EF4444;
        opacity: 0.75;
        animation: notif-ping-anim 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        pointer-events: none;
        z-index: 1;
    }
    @keyframes notif-ping-anim {
        75%, 100% {
            transform: scale(2.2);
            opacity: 0;
        }
    }
    @keyframes notif-wiggle {
        0%, 100% { transform: rotate(0deg); }
        20% { transform: rotate(-15deg); }
        40% { transform: rotate(15deg); }
        60% { transform: rotate(-10deg); }
        80% { transform: rotate(10deg); }
    }
    .wiggle-bell {
        animation: notif-wiggle 0.7s ease-in-out;
    }

    .notif-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 390px;
        max-width: 92vw;
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(15, 23, 42, 0.08);
        z-index: 9999;
        overflow: hidden;
        animation: notif-pop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes notif-pop {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .notif-dropdown-header {
        background: linear-gradient(135deg, #1E1B4B 0%, #312E81 100%);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #FFFFFF;
    }
    .notif-header-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .notif-header-title {
        font-weight: 700;
        font-size: 15px;
        letter-spacing: -0.01em;
    }
    .notif-count-pill {
        background: #EF4444;
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 9999px;
    }
    .notif-mark-read-btn {
        background: rgba(255, 255, 255, 0.12);
        border: none;
        border-radius: 6px;
        color: #E0E7FF;
        font-size: 11.5px;
        font-weight: 600;
        padding: 5px 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .notif-mark-read-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #FFFFFF;
    }
    .notif-tabs {
        display: flex;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
        padding: 6px 10px;
        gap: 6px;
    }
    .notif-tab {
        flex: 1;
        background: transparent;
        border: none;
        border-radius: 8px;
        padding: 6px 4px;
        font-size: 12px;
        font-weight: 600;
        color: #64748B;
        cursor: pointer;
        transition: all 0.15s;
        text-align: center;
    }
    .notif-tab:hover {
        color: #1E293B;
        background: #F1F5F9;
    }
    .notif-tab.active {
        background: #FFFFFF;
        color: #4338CA;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .notif-tab-num {
        font-size: 11px;
        opacity: 0.8;
    }
    .notif-list-container {
        max-height: 380px;
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    .notif-list-container::-webkit-scrollbar {
        width: 6px;
    }
    .notif-list-container::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 3px;
    }
    .notif-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid #F1F5F9;
        transition: background 0.15s ease;
        position: relative;
        text-decoration: none;
        color: inherit;
    }
    .notif-item:hover {
        background: #F8FAFC;
    }
    .notif-item.is-unread {
        background: #F5F7FF;
    }
    .notif-item.is-unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3.5px;
        background: #4338CA;
        border-radius: 0 2px 2px 0;
    }
    .notif-icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .notif-icon-walkin {
        background: #EEF2FF;
        color: #4338CA;
        border: 1px solid #C7D2FE;
    }
    .notif-icon-online {
        background: #ECFDF5;
        color: #059669;
        border: 1px solid #A7F3D0;
    }
    .notif-icon-pending {
        background: #FFFBEB;
        color: #D97706;
        border: 1px solid #FDE68A;
    }
    .notif-content {
        flex: 1;
        min-width: 0;
    }
    .notif-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 2px;
    }
    .notif-source-badge {
        font-size: 10.5px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .badge-walkin {
        background: #E0E7FF;
        color: #3730A3;
    }
    .badge-online {
        background: #D1FAE5;
        color: #065F46;
    }
    .badge-pending-online {
        background: #FEF3C7;
        color: #92400E;
    }
    .notif-time {
        font-size: 11px;
        color: #94A3B8;
        white-space: nowrap;
    }
    .notif-client-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.3;
        margin-bottom: 2px;
    }
    .notif-subtext {
        font-size: 12px;
        color: #64748B;
        line-height: 1.35;
        margin-bottom: 4px;
    }
    .notif-actions-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }
    .notif-btn-action {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 5px;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        color: #334155;
        text-decoration: none;
        transition: all 0.15s;
    }
    .notif-btn-action:hover {
        background: #4338CA;
        color: #FFFFFF;
        border-color: #4338CA;
    }
    .notif-empty {
        padding: 36px 20px;
        text-align: center;
    }
    .notif-empty-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #F1F5F9;
        color: #94A3B8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
    }
    .notif-empty-title {
        font-size: 14px;
        font-weight: 700;
        color: #1E293B;
        margin-bottom: 4px;
    }
    .notif-empty-subtitle {
        font-size: 12px;
        color: #64748B;
        max-width: 240px;
        margin: 0 auto;
    }
    .notif-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 30px;
        color: #64748B;
        font-size: 13px;
    }
    .notif-spinner {
        width: 18px;
        height: 18px;
        border: 2px solid #E2E8F0;
        border-top-color: #4338CA;
        border-radius: 50%;
        animation: notif-spin 0.7s linear infinite;
    }
    @keyframes notif-spin {
        to { transform: rotate(360deg); }
    }
    .notif-dropdown-footer {
        padding: 10px 16px;
        background: #F8FAFC;
        border-top: 1px solid #E2E8F0;
        text-align: center;
    }
    .notif-footer-link {
        font-size: 12px;
        font-weight: 600;
        color: #4338CA;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.15s;
    }
    .notif-footer-link:hover {
        color: #312E81;
        text-decoration: underline;
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/IserveIcon.png') }}" style="width:48px;height:48px;object-fit:contain;flex-shrink:0;margin-right:8px;" alt="iSERVE">
        <span style="white-space:nowrap;">Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard" class="active"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        @if((string) session('admin_user_role') !== 'social_worker')
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>Client Eligibility</span></a></li>
        @endif
        @if((string) session('admin_user_role') !== 'eligibility_checker')
        <li><a href="/admin/social-case/submitted"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        @endif
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li class="sidebar-dropdown" id="onlineRequestsDropdown">
            <a href="#" class="sidebar-dropdown-toggle" onclick="toggleDropdown('onlineRequestsDropdown'); return false;">
                <i data-lucide="file-text" style="width:20px;height:20px"></i>
                <span>Online Requests</span>
                <i data-lucide="chevron-down" class="dropdown-chevron" style="width:16px;height:16px;margin-left:auto;"></i>
            </a>
            <ul class="sidebar-dropdown-menu">
                <li><a href="/admin/social-case/online-requests"><i data-lucide="clock" style="width:18px;height:18px"></i><span>Pending Requests</span><span class="badge-count badge-pending" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#F59E0B;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['pending'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/accepted"><i data-lucide="check-circle" style="width:18px;height:18px"></i><span>Accepted Requests</span><span class="badge-count badge-accepted" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#10B981;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['accepted'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/rejected"><i data-lucide="x-circle" style="width:18px;height:18px"></i><span>Rejected Requests</span><span class="badge-count badge-rejected" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#EF4444;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['rejected'] ?? 0 }}</span></a></li>
            </ul>
        </li>
        @endif
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <!-- Mobile Welcome Message -->
    <div class="mobile-welcome-message">
        <h2 class="mobile-welcome-title">Welcome, {{ session('admin_user_name') ?? 'Social Case Study Officer' }}</h2>
    </div>

    <!-- Modern Page Header -->
    @php
        $userName = session('admin_user_name') ?? 'Social Case Study Officer';
        $words = explode(' ', $userName);
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($userName, 0, 2));
        }
    @endphp
    <header class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none mb-4 lg:mb-2">
        <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Welcome, {{ $userName }}</h1>
        <div class="flex items-center gap-4 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
            <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Thursday, July 16, 2026 at 01:51 PM</div>

            <!-- Notification Bell & Dropdown Wrapper -->
            <div class="notification-wrapper" style="position: relative;">
                <button type="button" id="notifBellBtn" class="notif-bell-btn" title="Notifications" onclick="toggleNotificationDropdown(event)">
                    <i data-lucide="bell" style="width: 20px; height: 20px;"></i>
                    <span id="notifBadge" class="notif-badge" style="display: none;">0</span>
                    <span id="notifPing" class="notif-ping" style="display: none;"></span>
                </button>

                <!-- Notification Dropdown Panel -->
                <div id="notifDropdown" class="notif-dropdown" style="display: none;">
                    <!-- Dropdown Header -->
                    <div class="notif-dropdown-header">
                        <div class="notif-header-left">
                            <i data-lucide="bell" style="width: 17px; height: 17px; color: #818CF8;"></i>
                            <span class="notif-header-title">Notifications</span>
                            <span id="notifHeaderCountBadge" class="notif-count-pill" style="display: none;">0 New</span>
                        </div>
                        <button type="button" id="markAllReadBtn" class="notif-mark-read-btn" onclick="markAllNotificationsRead(event)">
                            <i data-lucide="check-check" style="width: 14px; height: 14px;"></i>
                            Mark all read
                        </button>
                    </div>

                    <!-- Category Tabs -->
                    <div class="notif-tabs">
                        <button type="button" class="notif-tab active" data-filter="all" onclick="filterNotifications('all', event)">
                            All <span id="countAll" class="notif-tab-num">(0)</span>
                        </button>
                        @if((string) session('admin_user_role') === 'eligibility_checker')
                        <button type="button" class="notif-tab" data-filter="pending_online_request" onclick="filterNotifications('pending_online_request', event)">
                            New Online <span id="countPending" class="notif-tab-num">(0)</span>
                        </button>
                        <button type="button" class="notif-tab" data-filter="client_eligibility" onclick="filterNotifications('client_eligibility', event)">
                            Walk-in <span id="countWalkin" class="notif-tab-num">(0)</span>
                        </button>
                        @else
                        <button type="button" class="notif-tab" data-filter="client_eligibility" onclick="filterNotifications('client_eligibility', event)">
                            Walk-in <span id="countWalkin" class="notif-tab-num">(0)</span>
                        </button>
                        <button type="button" class="notif-tab" data-filter="online_request" onclick="filterNotifications('online_request', event)">
                            Online <span id="countOnline" class="notif-tab-num">(0)</span>
                        </button>
                        @endif
                    </div>

                    <!-- Scrollable Notification List -->
                    <div id="notifListContainer" class="notif-list-container">
                        <div class="notif-loading">
                            <div class="notif-spinner"></div>
                            <span>Checking notifications...</span>
                        </div>
                    </div>

                    <!-- Dropdown Footer -->
                    <div class="notif-dropdown-footer">
                        @if((string) session('admin_user_role') === 'eligibility_checker')
                        <a href="{{ route('admin.social-case.online-requests') }}" class="notif-footer-link">
                            <span>Open Pending Online Requests</span>
                            <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                        </a>
                        @else
                        <a href="{{ route('admin.social-case.submitted') }}" class="notif-footer-link">
                            <span>Open Submitted Cases Queue</span>
                            <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                {{ $initials }}
            </div>
        </div>
    </header>

    @php
        $dashRole = (string) session('admin_user_role');
        $dashIsChecker = in_array($dashRole, ['eligibility_checker'], true);
        $dashIsEncoder = in_array($dashRole, ['social_worker', 'admin'], true);
    @endphp
    @if($dashIsChecker)
    <div class="role-banner" style="background:#EEF2FF;border:1px solid #C7D2FE;">
        <i data-lucide="shield-check" style="width:20px;height:20px;color:#4338CA;"></i>
        <div class="role-banner-text">
            <div class="role-banner-title" style="color:#4338CA;">Eligibility Checking Only</div>
            <div class="role-banner-subtitle" style="color:#4F46E5;">Your account only performs client eligibility checks and forwards eligible clients for case encoding. You cannot encode or modify Social Case Study information.</div>
        </div>
    </div>
    @elseif($dashIsEncoder)
    <div class="role-banner" style="background:#ECFDF5;border:1px solid #A7F3D0;">
        <i data-lucide="file-pen-line" style="width:20px;height:20px;color:#059669;"></i>
        <div class="role-banner-text">
            <div class="role-banner-title" style="color:#065F46;">Case Encoding Account</div>
            <div class="role-banner-subtitle" style="color:#047857;">Your account is authorized to encode, update, print, and process Social Case Studies for eligible clients.</div>
        </div>
    </div>
    @endif

    @if($dashIsChecker)
    <!-- Modern Statistic Cards for Eligibility Checker -->
    <div class="stat-cards">
        <div class="stat-card stat-card-blue">
            <div class="stat-card-content">
                <div class="stat-card-label">PENDING ELIGIBILITY CHECKS</div>
                <div class="stat-card-value">{{ $stats['pending_eligibility'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="clock"></i>
            </div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-card-content">
                <div class="stat-card-label">ELIGIBLE CLIENTS</div>
                <div class="stat-card-value">{{ $stats['eligible_clients'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="check-circle"></i>
            </div>
        </div>
        <div class="stat-card stat-card-purple">
            <div class="stat-card-content">
                <div class="stat-card-label">FORWARDED TO ENCODER</div>
                <div class="stat-card-value">{{ $stats['forwarded_to_encoder'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="send"></i>
            </div>
        </div>
        <div class="stat-card stat-card-teal">
            <div class="stat-card-content">
                <div class="stat-card-label">REJECTED</div>
                <div class="stat-card-value">{{ $stats['rejected_clients'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="x-circle"></i>
            </div>
        </div>
    </div>
    @elseif($dashIsEncoder)
    <!-- Modern Statistic Cards for Case Encoder -->
    <div class="stat-cards">
        <div class="stat-card stat-card-blue">
            <div class="stat-card-content">
                <div class="stat-card-label">TOTAL CLIENTS</div>
                <div class="stat-card-value">{{ $stats['total_clients'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="users"></i>
            </div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-card-content">
                <div class="stat-card-label">Forwarded to me</div>
                <div class="stat-card-value">{{ $stats['forwarded_to_me'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="calendar"></i>
            </div>
        </div>
        <div class="stat-card stat-card-purple">
            <div class="stat-card-content">
                <div class="stat-card-label">RELEASED TODAY</div>
                <div class="stat-card-value">{{ $stats['released_today'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="check-circle"></i>
            </div>
        </div>
        <div class="stat-card stat-card-teal">
            <div class="stat-card-content">
                <div class="stat-card-label">TOTAL RELEASED</div>
                <div class="stat-card-value">{{ $stats['total_released'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="send"></i>
            </div>
        </div>
    </div>
    @endif

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Left Column - Analytics -->
        <div class="analytics-section">
            <div class="analytics-card">
                <h3>Most Requested Assistance</h3>
                <div class="chart-wrapper">
                    <div class="chart-canvas">
                        <canvas id="assistanceChart"></canvas>
                    </div>
                    <div class="chart-legend" id="chartLegend"></div>
                </div>
            </div>
        </div>

        <!-- Right Column - Recent Activity -->
        <div class="activity-section">
            <div class="activity-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                    <h3 style="margin:0"><i data-lucide="activity" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-blue)"></i>Recent Activities</h3>
                    <button class="text-xs font-semibold px-3 py-1 rounded-lg" style="background:var(--danger-bg);color:var(--danger);border:none;cursor:pointer" onclick="confirmClearActivities()">Clear</button>
                </div>
                <div class="activity-feed" id="activityFeed"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') . '?v=' . filemtime(public_path('js/social-case.js')) }}"></script>
<script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.classList.toggle('open');
        }
    }

    // ── NOTIFICATION BELL & DROPDOWN LOGIC ──
    let allNotifications = [];
    let activeNotifFilter = 'all';
    let previousUnreadCount = 0;

    function escapeHtmlNotif(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
        });
    }

    function toggleNotificationDropdown(e) {
        if (e) e.stopPropagation();
        const dropdown = document.getElementById('notifDropdown');
        const bellBtn = document.getElementById('notifBellBtn');
        if (!dropdown) return;

        const isOpen = dropdown.style.display === 'block';
        if (isOpen) {
            closeNotificationDropdown();
        } else {
            dropdown.style.display = 'block';
            if (bellBtn) bellBtn.classList.add('active');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function closeNotificationDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        const bellBtn = document.getElementById('notifBellBtn');
        if (dropdown) dropdown.style.display = 'none';
        if (bellBtn) bellBtn.classList.remove('active');
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.querySelector('.notification-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeNotificationDropdown();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeNotificationDropdown();
        }
    });

    async function fetchNotifications(isPoll = false) {
        try {
            const response = await fetch('/admin/social-case/api/notifications', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) return;
            const data = await response.json();
            if (data.success) {
                allNotifications = data.notifications || [];
                updateNotificationUI(data.unread_count || 0, isPoll);
            }
        } catch (err) {
            console.error('Failed to load notifications:', err);
        }
    }

    function updateNotificationUI(unreadCount, isPoll) {
        const badge = document.getElementById('notifBadge');
        const ping = document.getElementById('notifPing');
        const bellBtn = document.getElementById('notifBellBtn');
        const headerCount = document.getElementById('notifHeaderCountBadge');

        const pendingCount = allNotifications.filter(n => n.type === 'pending_online_request').length;
        const walkinCount = allNotifications.filter(n => n.type === 'client_eligibility').length;
        const onlineCount = allNotifications.filter(n => n.type === 'online_request').length;

        const countAllEl = document.getElementById('countAll');
        const countPendingEl = document.getElementById('countPending');
        const countWalkinEl = document.getElementById('countWalkin');
        const countOnlineEl = document.getElementById('countOnline');

        if (countAllEl) countAllEl.textContent = `(${allNotifications.length})`;
        if (countPendingEl) countPendingEl.textContent = `(${pendingCount})`;
        if (countWalkinEl) countWalkinEl.textContent = `(${walkinCount})`;
        if (countOnlineEl) countOnlineEl.textContent = `(${onlineCount})`;

        if (unreadCount > 0) {
            if (badge) {
                badge.style.display = 'flex';
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            }
            if (ping) ping.style.display = 'block';
            if (headerCount) {
                headerCount.style.display = 'inline-block';
                headerCount.textContent = `${unreadCount} New`;
            }

            if (isPoll && unreadCount > previousUnreadCount && bellBtn) {
                bellBtn.classList.remove('wiggle-bell');
                void bellBtn.offsetWidth;
                bellBtn.classList.add('wiggle-bell');
            }
        } else {
            if (badge) badge.style.display = 'none';
            if (ping) ping.style.display = 'none';
            if (headerCount) headerCount.style.display = 'none';
        }

        previousUnreadCount = unreadCount;
        renderNotificationList();
    }

    function filterNotifications(filter, e) {
        if (e) e.stopPropagation();
        activeNotifFilter = filter;

        document.querySelectorAll('.notif-tab').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.filter === filter);
        });

        renderNotificationList();
    }

    function renderNotificationList() {
        const container = document.getElementById('notifListContainer');
        if (!container) return;

        let items = allNotifications;
        if (activeNotifFilter !== 'all') {
            if (activeNotifFilter === 'online_request') {
                items = items.filter(n => n.type === 'online_request' || n.type === 'pending_online_request');
            } else {
                items = items.filter(n => n.type === activeNotifFilter);
            }
        }

        if (items.length === 0) {
            container.innerHTML = `
                <div class="notif-empty">
                    <div class="notif-empty-icon">
                        <i data-lucide="bell-off" style="width:24px;height:24px;"></i>
                    </div>
                    <div class="notif-empty-title">All caught up!</div>
                    <div class="notif-empty-subtitle">No forwarded clients or online requests in this view right now.</div>
                </div>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return;
        }

        let html = '';
        items.forEach(n => {
            let iconName = 'globe';
            let iconClass = 'notif-icon-online';
            let badgeClass = n.badge_class || 'badge-online';
            let actionBtnText = 'View in Queue';
            let actionIcon = 'external-link';

            if (n.type === 'client_eligibility') {
                iconName = 'user-check';
                iconClass = 'notif-icon-walkin';
                actionBtnText = 'View in Queue';
                actionIcon = 'external-link';
            } else if (n.type === 'pending_online_request') {
                iconName = 'inbox';
                iconClass = 'notif-icon-pending';
                actionBtnText = 'Review Request';
                actionIcon = 'eye';
            }

            const unreadClass = n.is_unread ? 'is-unread' : '';

            html += `
                <div class="notif-item ${unreadClass}">
                    <div class="notif-icon-circle ${iconClass}">
                        <i data-lucide="${iconName}" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-meta-row">
                            <span class="notif-source-badge ${badgeClass}">${escapeHtmlNotif(n.badge_text)}</span>
                            <span class="notif-time">${escapeHtmlNotif(n.time_ago)}</span>
                        </div>
                        <div class="notif-client-name">${escapeHtmlNotif(n.client_name)}</div>
                        <div class="notif-subtext">
                            <span style="font-weight:600;color:#334155;">${escapeHtmlNotif(n.control_no)}</span> • ${escapeHtmlNotif(n.subtitle)}
                        </div>
                        <div class="notif-actions-row">
                            <a href="${n.url}" class="notif-btn-action">
                                <i data-lucide="${actionIcon}" style="width:12px;height:12px;"></i> ${actionBtnText}
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    async function markAllNotificationsRead(e) {
        if (e) e.stopPropagation();
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const response = await fetch('/admin/social-case/api/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            if (response.ok) {
                allNotifications.forEach(n => n.is_unread = false);
                updateNotificationUI(0, false);
            }
        } catch (err) {
            console.error('Failed to mark notifications as read:', err);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Set current date and time
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            };
            const dateTimeStr = now.toLocaleDateString('en-US', options).replace(',', '') + ' at';
            document.getElementById('currentDateTime').textContent = dateTimeStr;
        }
        updateDateTime();
        setInterval(updateDateTime, 60000); // Update every minute
        
        lucide.createIcons();
        loadDashboard();

        // Load notifications and poll every 30 seconds
        fetchNotifications();
        setInterval(() => fetchNotifications(true), 30000);
    });
</script>
@endpush
