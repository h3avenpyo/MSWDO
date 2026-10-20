@extends('layouts.desk-officer')

@section('title', 'Dashboard - iSERVE SILANG Desk Officer')

@push('styles')
<style>
    html, body, #mainContent { scrollbar-width: none; }
    html::-webkit-scrollbar, body::-webkit-scrollbar, #mainContent::-webkit-scrollbar { display: none; }

    /* Keep the five dashboard summaries legible in both tablet orientations. */
    .dashboard-statistics > * { min-width: 0; }
    .dashboard-statistic-content { min-width: 0; }
    .dashboard-statistic-label {
        overflow-wrap: anywhere;
        line-height: 1.25;
    }

    /* Android tablet grid: two columns in narrow portrait, then 3 + 2,
       then five evenly spaced cards in landscape widths. */
    @media (min-width: 37.5rem) and (max-width: 47.999rem) {
        .dashboard-statistics {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: clamp(.75rem, 2vw, 1rem) !important;
        }
        .dashboard-statistics > * { grid-column: span 1 !important; }
        .dashboard-statistics > *:last-child { grid-column: 1 / -1 !important; }
    }

    @media (min-width: 48rem) and (max-width: 63.999rem) {
        .dashboard-statistics {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: clamp(.75rem, 1.5vw, 1rem) !important;
        }
        .dashboard-statistics > *:nth-child(-n + 3) { grid-column: span 2; }
        .dashboard-statistics > *:nth-child(n + 4) { grid-column: span 3; }
        .dashboard-statistic-card { padding: clamp(.875rem, 2vw, 1rem); }
        .dashboard-statistic-card > div { gap: 0.75rem; }
        .dashboard-statistic-card .dashboard-statistic-icon {
            width: 3rem;
            height: 3rem;
        }
    }

    @media (min-width: 64rem) and (max-width: 80rem) {
        .dashboard-statistics {
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: clamp(.75rem, 1.25vw, 1rem) !important;
        }
        .dashboard-statistics > * { grid-column: span 1 !important; }
        .dashboard-statistic-card { padding: clamp(.875rem, 1.5vw, 1rem); }
        .dashboard-statistic-card > div { gap: .75rem; }
        .dashboard-statistic-card .dashboard-statistic-icon {
            width: 2.75rem;
            height: 2.75rem;
        }
    }

    /* Multi-role controls stay balanced on Android tablet portrait widths. */
    @media (min-width: 37.5rem) and (max-width: 47.999rem) {
        #desk-officer-dashboard-content { padding: clamp(1rem, 2vw, 1.5rem) !important; }
        .dashboard-department-switcher {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100%;
            gap: .5rem;
        }
        .dashboard-department-switcher > a {
            width: 100%;
            min-width: 0;
            justify-content: center;
            text-align: center;
        }
    }

    @media (min-width: 48rem) and (max-width: 80rem) {
        #desk-officer-dashboard-content { padding-left: 1rem !important; padding-right: 1rem !important; }
        .dashboard-department-switcher { flex-wrap: wrap; margin-right: 1.5rem !important; }
    }

    /* Phone-first dashboard (320px–430px), including compact landscape
       viewports.  Tablet and desktop layout rules above are intentionally
       left untouched. */
    @media (max-width: 26.875rem), (orientation: landscape) and (max-height: 30rem) and (max-width: 58rem) {
        #desk-officer-dashboard-content {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            padding-top: max(1rem, env(safe-area-inset-top)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
            padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom))) !important;
            padding-left: max(1rem, env(safe-area-inset-left)) !important;
        }
        .dashboard-statistics {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: .75rem !important;
            margin-bottom: 1.5rem !important;
        }
        .dashboard-statistics > * { grid-column: span 1 !important; }
        .dashboard-statistics > *:last-child { grid-column: 1 / -1 !important; }
        .dashboard-statistic-card { padding: .875rem !important; }
        .dashboard-statistic-card > div { gap: .625rem !important; }
        .dashboard-statistic-card .dashboard-statistic-icon {
            width: 3rem !important;
            height: 3rem !important;
        }
        .dashboard-statistic-card .text-2xl { font-size: 1.25rem !important; }
        .dashboard-statistic-label { font-size: .6875rem !important; }
        .dashboard-department-switcher {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100%;
            gap: .5rem;
        }
        .dashboard-department-switcher > a,
        .dashboard-view-all {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 3rem;
            min-height: 3rem;
        }
        .dashboard-department-switcher > a { width: 100%; padding: .5rem !important; }
        #caseTypesChart { max-width: 100%; }
    }

    /* Keep the existing multi-role department views in one row on tablets. */
    @media (min-width: 37.5rem) and (max-width: 80rem) {
        .dashboard-department-switcher {
            display: flex !important;
            width: max-content;
            flex-wrap: nowrap !important;
        }
        .dashboard-department-switcher > a {
            width: auto;
            flex: 0 0 auto;
        }
    }
</style>
<style>
    html:has([data-swd-dashboard-fit]), body:has([data-swd-dashboard-fit]) { height: 100%; overflow: hidden; }
    #mainContent:has([data-swd-dashboard-fit]) {
        height: 100dvh;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    #mainContent:has([data-swd-dashboard-fit]) > header { flex-shrink: 0; }
    #desk-officer-dashboard-content[data-swd-dashboard-fit] {
        width: 100%;
        align-self: stretch;
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        gap: .75rem;
        padding: .75rem !important;
        overflow: hidden;
    }
    [data-swd-dashboard-fit] > :first-child,
    [data-swd-dashboard-fit] .dashboard-statistics { margin-bottom: 0 !important; flex-shrink: 0; }
    [data-swd-dashboard-fit] .dashboard-statistics { grid-template-columns: repeat(5, minmax(0, 1fr)) !important; gap: .5rem !important; }
    [data-swd-dashboard-fit] .dashboard-statistics > * { grid-column: auto !important; padding: .625rem !important; }
    [data-swd-dashboard-fit] .dashboard-statistic-card > div { gap: .5rem; }
    [data-swd-dashboard-fit] .dashboard-statistic-icon { width: 2rem !important; height: 2rem !important; }
    [data-swd-dashboard-fit] .dashboard-statistic-content > :first-child { font-size: 1.25rem !important; }
    [data-swd-dashboard-fit] .dashboard-statistic-label { font-size: .6875rem !important; }
    [data-swd-dashboard-fit] .dashboard-lower-grid {
        flex: 1;
        min-height: 0;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: stretch;
        gap: .75rem;
    }
    [data-swd-dashboard-fit] .dashboard-distribution-box > :first-child { padding: .5rem .75rem; }
    [data-swd-dashboard-fit] .dashboard-distribution-box > :first-child h3 { font-size: .875rem; }
    [data-swd-dashboard-fit] .dashboard-distribution-box > :first-child p { font-size: .6875rem; }
    [data-swd-dashboard-fit] .dashboard-distribution-box > :last-child { min-height: 0; padding: .5rem; }
    [data-swd-dashboard-fit] .dashboard-distribution-box .h-56 { height: 100%; min-height: 0; }
    [data-swd-dashboard-fit] .dashboard-recent-box { min-height: 0; align-self: start; max-height: 100%; }
    [data-swd-dashboard-fit] .dashboard-recent-box > :last-child { min-height: 0; overflow-y: auto; }
    [data-swd-dashboard-fit] .dashboard-announcements-row { width: 100%; flex-shrink: 0; }
    @media (min-width: 48rem) and (max-width: 63.999rem) {
        [data-swd-dashboard-fit] .dashboard-statistics {
            grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
            gap: clamp(.75rem, 1.5vw, 1rem) !important;
        }
        [data-swd-dashboard-fit] .dashboard-statistics > *:nth-child(-n + 3) { grid-column: span 2 !important; }
        [data-swd-dashboard-fit] .dashboard-statistics > *:nth-child(n + 4) { grid-column: span 3 !important; }
        [data-swd-dashboard-fit] .dashboard-statistic-card { padding: clamp(.875rem, 2vw, 1rem) !important; }
        [data-swd-dashboard-fit] .dashboard-statistic-icon { width: 3rem !important; height: 3rem !important; }
    }
    @media (max-width: 47.999rem) {
        [data-swd-dashboard-fit] .dashboard-statistics { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
        [data-swd-dashboard-fit] .dashboard-statistic-icon { display: none; }
        [data-swd-dashboard-fit] > :first-child { gap: .25rem; }
        [data-swd-dashboard-fit] > :first-child h2 { font-size: .875rem; }
        [data-swd-dashboard-fit] > :first-child p { font-size: .625rem; }
        [data-swd-dashboard-fit] .dashboard-recent-box > :first-child { padding: .5rem; flex-wrap: wrap; gap: .25rem; }
        [data-swd-dashboard-fit] .dashboard-recent-box h3 { font-size: .875rem; }
    }
</style>
@endpush

@section('content')

@php
    $isBCPC = ($dashboardType ?? 'vawc') === 'bcpc';
    $dashboardDepartment = $isBCPC ? 'BCPC' : 'SWD';
    $dashboardUser = Auth::user();
    $dashboardRole = match (strtolower(trim((string) $dashboardUser?->position))) {
        'administrator', 'admin' => 'Administrator',
        'desk officer' => 'Desk Officer',
        default => ucwords(strtolower(trim((string) ($dashboardUser?->position ?: 'User')))),
    };
    $chartColors = $isBCPC
        ? ['#7c3aed', '#8b5cf6', '#a855f7', '#c084fc', '#ec4899', '#f43f5e', '#f97316', '#eab308']
        : ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#06b6d4', '#6366f1', '#14b8a6'];
@endphp

<!-- Top Header -->
<header data-dashboard-header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Welcome, {{ $dashboardDepartment }} SILANG</h2>
            <p class="text-sm text-gray-500">{{ $dashboardDepartment }} {{ $dashboardRole }} - Silang, Cavite</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Notifications -->
            @include('desk-officer.components.notifications')
            
            <!-- User Menu -->
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position ?? 'BCPC Desk Officer' }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Dashboard Content -->
<main id="desk-officer-dashboard-content" @if(!$isBCPC && $dashboardUser->position === 'Desk Officer' && $dashboardUser->canAccessCaseDepartment('VAWC')) data-swd-dashboard-fit @endif class="px-4 pt-4 pb-4 sm:p-6 lg:p-8" style="padding-bottom: {{ !$isBCPC && $dashboardUser->position === 'Desk Officer' && $dashboardUser->canAccessCaseDepartment('VAWC') ? '.75rem' : 'max(3rem, calc(1rem + env(safe-area-inset-bottom)))' }} !important;">
    <!-- Page Title & Department Switcher -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        @php
            $dashboardBarangayName = trim((string) ($barangayName ?? Auth::user()?->barangay ?? 'your barangay'));
            $dashboardBarangayName = preg_replace('/^barangay\s+/i', '', $dashboardBarangayName);
        @endphp
        <div class="flex items-start gap-3 min-w-0">
            <span class="inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600" aria-hidden="true">
                <i class="fas fa-chart-bar"></i>
            </span>
            <div class="min-w-0">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-700">Reports Overview</h2>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Real-time overview of cases and reports from Barangay {{ $dashboardBarangayName }}</p>
            </div>
        </div>
        @if(Auth::user()->isMultiCaseDepartment())
        <div class="dashboard-department-switcher flex items-center gap-2">
            <a href="{{ route('desk-officer.bcpc.dashboard') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shadow-sm {{ $isBCPC ? 'bg-purple-700 text-white shadow-purple-200' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-child mr-1"></i> BCPC View
            </a>
            <a href="{{ route('desk-officer.dashboard', ['type' => 'VAWC']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shadow-sm {{ !$isBCPC ? 'bg-purple-700 text-white shadow-purple-200' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-shield-alt mr-1"></i> SWD View
            </a>
        </div>
        @endif
    </div>
    
    <!-- Summary Statistics Grid -->
    <div class="dashboard-statistics grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <!-- Total Cases -->
        <div class="dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-white text-lg sm:text-xl"></i>
                </div>
                <div class="dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['totalCases'] ?? 0 }}</p>
                    <p class="dashboard-statistic-label text-xs sm:text-sm text-gray-500">Total Cases</p>
                </div>
            </div>
        </div>
        
        <!-- Pending Cases -->
        <div class="dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-white text-lg sm:text-xl"></i>
                </div>
                <div class="dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['pendingCases'] ?? 0 }}</p>
                    <p class="dashboard-statistic-label text-xs sm:text-sm text-gray-500">{{ in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) ? 'Case Filed' : 'Pending Cases' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Resolved Cases -->
        <div class="dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-white text-lg sm:text-xl"></i>
                </div>
                <div class="dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['resolvedCases'] ?? 0 }}</p>
                    <p class="dashboard-statistic-label text-xs sm:text-sm text-gray-500">{{ (Auth::check() && Auth::user()->isMultiCaseDepartment()) ? 'Complete Cases' : 'Resolved Cases' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Today's Cases -->
        <div class="dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-day text-white text-lg sm:text-xl"></i>
                </div>
                <div class="dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['todayCases'] ?? 0 }}</p>
                    <p class="dashboard-statistic-label text-xs sm:text-sm text-gray-500">Today's Cases</p>
                </div>
            </div>
        </div>
        
        <!-- Referred Cases -->
        <div class="dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow col-span-2 sm:col-span-1">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-share-square text-white text-lg sm:text-xl"></i>
                </div>
                <div class="dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['casesReferred'] ?? 0 }}</p>
                    <p class="dashboard-statistic-label text-xs sm:text-sm text-gray-500">Referred Cases</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts & Recent Cases Grid -->
    <div class="dashboard-lower-grid grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
        <!-- Case Types Distribution Chart -->
        <div class="dashboard-distribution-box bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="px-4 py-3 border-b border-gray-100 flex items-start gap-2">
                <i class="fas fa-chart-pie text-purple-600 mt-1"></i>
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Case Types Distribution</h3>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Breakdown of recorded cases by case type.</p>
                </div>
            </div>
            <div class="p-3 flex-1 flex flex-col justify-center">
                <div class="h-56 relative flex items-center justify-center">
                    <canvas id="caseTypesChart"></canvas>
                    <div id="noChartData" class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-2 opacity-50"></i>
                        <p class="text-sm">No case data available</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Cases -->
        <div class="dashboard-recent-box bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-list-ul text-purple-600"></i>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Recent Cases</h3>
                </div>
                <a href="{{ route('desk-officer.cases.index', ['type' => $dashboardType ?? 'vawc']) }}" class="dashboard-view-all text-xs font-semibold text-purple-600 hover:text-purple-800 transition">
                    View All <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                </a>
            </div>
            <div class="p-3 flex-1 divide-y divide-gray-100">
                @php
                    $isMultiroleUser = auth()->user() && auth()->user()->position === 'Desk Officer' && auth()->user()->isMultiCaseDepartment();
                    $casesToDisplay = ($isMultiroleUser || (!$isBCPC && $dashboardUser->position === 'Desk Officer')) ? $recentCases->take(3) : $recentCases;
                @endphp
                @forelse($casesToDisplay as $case)
                    <div class="py-2 first:pt-0 last:pb-0 hover:bg-gray-50/70 rounded-lg px-2 -mx-2 transition flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded">#{{ $case->case_number ?? 'N/A' }}</span>
                                <span class="text-xs text-gray-400">{{ $case->created_at ? $case->created_at->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-800 truncate mt-1">{{ censorLastName($case->victim_name) }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                @php
                                    $abuseType = strtolower($case->abuse_type ?? 'N/A');
                                    $typeColor = '#' . \App\Http\Controllers\Wcpu\AdminController::getAbuseTypeColor($abuseType);
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium border"
                                      style="background-color: {{ $typeColor }}15; color: {{ $typeColor }}; border-color: {{ $typeColor }}40;">
                                    {{ ucfirst($case->abuse_type ?? 'N/A') }}
                                </span>
                            </div>
                        </div>
                        <div>
                            @php
                                $statusColor = $case->status ? '#' . \App\Http\Controllers\Wcpu\AdminController::getStatusColor($case->status) : '#6c757d';
                            @endphp
                            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full text-white inline-block shadow-sm"
                                  style="background-color: {{ $statusColor }};">
                                {{ $case->status ?? 'Pending' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p class="text-sm font-medium text-gray-500">No cases found</p>
                        <p class="text-xs text-gray-400 mt-0.5">No recent cases recorded for this period</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="dashboard-announcements-row w-full mt-4">
        @include('components.swd-dashboard-announcements', [
            'announcementUser' => $dashboardUser,
            'announcementLimit' => 4,
            'announcementTwoColumns' => true,
        ])
    </div>
</main>



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const caseTypes = @json($caseTypes ?? []);
    const filtered = caseTypes.filter(c => (c.cases_count || 0) > 0);
    const colors = @json($chartColors);
    const ctx = document.getElementById('caseTypesChart');
    const noChartData = document.getElementById('noChartData');

    if (!ctx) return;

    if (filtered.length === 0) {
        ctx.style.display = 'none';
        if (noChartData) noChartData.classList.remove('hidden');
        return;
    }

    const sorted = [...filtered].sort((a, b) => b.cases_count - a.cases_count);
    const labels = sorted.map(c => c.republic_act || c.name);
    const data = sorted.map(c => c.cases_count);
    const total = data.reduce((a, b) => a + b, 0);
    const isCompactPhoneDashboard = window.matchMedia('(max-width: 26.875rem), (orientation: landscape) and (max-height: 30rem) and (max-width: 58rem)').matches;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, data.length),
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: isCompactPhoneDashboard ? 'bottom' : 'right',
                    labels: {
                        padding: isCompactPhoneDashboard ? 8 : 15,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: isCompactPhoneDashboard ? 10 : 12 },
                        generateLabels: chart => {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const dataset = data.datasets[0];
                                    const value = dataset.data[i];
                                    return {
                                        index: i,
                                        text: `${label}: ${value}`,
                                        fillStyle: dataset.backgroundColor[i],
                                        hidden: chart.getDatasetMeta(0).data[i].hidden,
                                        pointStyle: 'circle'
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: ctx => {
                            const pct = Math.round((ctx.raw / total) * 100);
                            return `${ctx.label}: ${ctx.raw} (${pct}%)`;
                        }
                    }
                }
            },
            animation: { animateScale: true, animateRotate: true, duration: 1000 }
        }
    });
});
</script>
@endpush

@endsection
