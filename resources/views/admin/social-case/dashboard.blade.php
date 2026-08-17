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
            <h1 class="mobile-brand-title">MSWDO SILANG</h1>
            <p class="mobile-brand-subtitle">Social Case Study Dashboard</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard" class="active"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>New case</span></a></li>
        @if((string) session('admin_user_role') !== 'eligibility_checker')
        <li><a href="/admin/social-case/submitted"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @endif
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
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
    <header class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none mb-6 sm:mb-8 lg:mb-3">
        <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Welcome, {{ $userName }}</h1>
        <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
            <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Thursday, July 16, 2026 at 01:51 PM</div>
            <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                {{ $initials }}
            </div>
        </div>
    </header>

    @php
        $dashRole = (string) session('admin_user_role');
        $dashIsChecker = in_array($dashRole, ['eligibility_checker'], true);
    @endphp
    @if($dashIsChecker)
    <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:10px;padding:14px 18px;display:flex;align-items:center;gap:12px;margin-bottom:24px">
        <i data-lucide="shield-check" style="width:20px;height:20px;color:#4338CA;flex-shrink:0"></i>
        <div>
            <div style="font-weight:700;color:#4338CA;font-size:14px">Eligibility Checking Only</div>
            <div style="font-size:13px;color:#4F46E5">Your account only performs client eligibility checks and forwards eligible clients for case encoding. You cannot encode or modify Social Case Study information.</div>
        </div>
    </div>
    @endif

    <!-- Modern Statistic Cards -->
    <div class="stat-cards">
        <div class="stat-card stat-card-blue">
            <div class="stat-card-content">
                <div class="stat-card-label">TOTAL CLIENTS</div>
                <div class="stat-card-value" id="totalClients">0</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="users"></i>
            </div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-card-content">
                <div class="stat-card-label">CASES THIS MONTH</div>
                <div class="stat-card-value" id="casesThisMonth">0</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="calendar"></i>
            </div>
        </div>
        <div class="stat-card stat-card-purple">
            <div class="stat-card-content">
                <div class="stat-card-label">RELEASED TODAY</div>
                <div class="stat-card-value" id="releasedToday">0</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="check-circle"></i>
            </div>
        </div>
        <div class="stat-card stat-card-teal">
            <div class="stat-card-content">
                <div class="stat-card-label">TOTAL RELEASED</div>
                <div class="stat-card-value" id="totalReleased">0</div>
            </div>
            <div class="stat-card-icon">
                <i data-lucide="send"></i>
            </div>
        </div>
    </div>

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
            const dateTimeStr = now.toLocaleDateString('en-US', options).replace(',', ' at');
            document.getElementById('currentDateTime').textContent = dateTimeStr;
        }
        updateDateTime();
        setInterval(updateDateTime, 60000); // Update every minute
        
        lucide.createIcons();
        loadDashboard();
    });
</script>
@endpush
