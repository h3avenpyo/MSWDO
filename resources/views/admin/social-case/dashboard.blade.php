@extends('admin.social-case.layout')
@section('title', 'Dashboard - Social Case Study')

@section('content')
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard" class="active"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i> New case</a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i> All cases</a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <!-- Modern Page Header -->
    @php
        $userName = session('admin_user_name') ?? 'Admin User';
        $words = explode(' ', $userName);
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($userName, 0, 2));
        }
    @endphp
    <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
        <div class="flex items-center">
            <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Social Case Study Dashboard</h1>
        </div>
        <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
            <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Thursday, July 16, 2026 at 01:51 PM</div>
            <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                {{ $initials }}
            </div>
        </div>
    </header>

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
<script src="{{ asset('js/social-case.js') }}"></script>
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

        // Welcome popup — only after login
        @if(session('admin_just_logged_in'))
        setTimeout(function() {
            Swal.fire({
                title: 'Welcome Admin!',
                html: '<div style="text-align:center;line-height:1.7;color:#475569;font-size:15px">' +
                      '<p style="margin:0 0 8px;font-weight:500">Social Case Study Officer</p>' +
                      '</div>',
                icon: 'info',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'Continue',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' },
                allowOutsideClick: false
            });
            fetch('/admin/clear-welcome', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
        }, 500);
        @endif
    });
</script>
@endpush
