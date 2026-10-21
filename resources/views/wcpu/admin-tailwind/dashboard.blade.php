@extends('layouts.admin-tailwind')

@section('title', 'Dashboard - iSERVE Admin')

@push('styles')
<style>
    .admin-dashboard-statistics > * { min-width: 0; }
    .admin-dashboard-statistic-content { min-width: 0; }
    .admin-dashboard-statistic-label { overflow-wrap: anywhere; line-height: 1.25; }
    #dashboardMenuToggle { display: none; }

    @media (min-width: 768px) and (max-width: 1439px) {
        .admin-dashboard-statistics { grid-template-columns: repeat(6, minmax(0, 1fr)) !important; }
        .admin-dashboard-statistics > *:nth-child(-n + 3) { grid-column: span 2; }
        .admin-dashboard-statistics > *:nth-child(n + 4) { grid-column: span 3; }
        .admin-dashboard-statistic-card { padding: 1rem; }
        .admin-dashboard-statistic-card > div { gap: .75rem; }
        .admin-dashboard-statistic-card .admin-dashboard-statistic-icon { width: 3rem; height: 3rem; }
    }

    /* Mobile-first Dashboard layout for 320px–430px Android phones/iPhones. */
    @media (max-width: 26.875rem) {
        #admin-dashboard-header > div {
            padding-top: max(.875rem, env(safe-area-inset-top));
            padding-inline: max(1rem, env(safe-area-inset-left)) max(1rem, env(safe-area-inset-right));
        }
        #admin-dashboard-main {
            padding: 1rem max(1rem, env(safe-area-inset-right)) calc(1rem + env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left));
        }
        #admin-dashboard-header button,
        #mobileMenuToggle {
            min-width: 3rem;
            min-height: 3rem;
        }
        #mobileMenuToggle {
            width: 2.25rem !important;
            min-width: 2.25rem !important;
            height: 2.25rem !important;
            min-height: 2.25rem !important;
            padding: .5rem !important;
        }
        #mobileMenuToggle i { font-size: 1rem !important; }
        .admin-dashboard-statistics {
            grid-template-columns: 1fr !important;
            gap: .75rem !important;
            margin-bottom: 1.5rem !important;
        }
        .admin-dashboard-statistics > * { grid-column: auto !important; }
        .admin-dashboard-statistic-card { padding: 1rem !important; }
        .admin-dashboard-statistic-card > div { gap: .75rem !important; }
        .admin-dashboard-statistic-icon {
            width: 3rem !important;
            height: 3rem !important;
        }
        #admin-dashboard-charts { gap: 1rem !important; }
        #admin-dashboard-charts .admin-dashboard-chart-title { padding: 1rem !important; }
        #admin-dashboard-charts .admin-dashboard-chart-body { padding: 1rem !important; }
        #admin-dashboard-charts .admin-dashboard-chart-canvas { height: 15rem !important; }
    }

    @media (min-width: 23.5rem) and (max-width: 26.875rem) {
        .admin-dashboard-statistics { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
        .admin-dashboard-statistics > *:last-child { grid-column: 1 / -1 !important; }
        .admin-dashboard-statistic-card > div { align-items: flex-start; }
        .admin-dashboard-statistic-content p:first-child { font-size: 1.5rem; }
    }

    /* Dashboard mobile navigation opens as a full-width drawer directly below the header, keeping the top navbar visible. */
    @media (max-width: 47.9375rem) {
        body.admin-dashboard-mobile-nav #mobileTopBar { display: none !important; }
        body.admin-dashboard-mobile-nav #mobileMenuToggle { display: none !important; }
        body.admin-dashboard-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        body.admin-dashboard-mobile-nav #sidebar {
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: auto !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            max-height: calc(100vh - 60px) !important;
            overflow-y: auto !important;
            z-index: 999 !important;
            border-radius: 0 0 14px 14px !important;
            box-shadow: 0 14px 28px rgba(0, 0, 0, .22) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-8px) !important;
            transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
        }
        body.admin-dashboard-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        body.admin-dashboard-mobile-nav #sidebar.sidebar-collapsed,
        body.admin-dashboard-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        body.admin-dashboard-mobile-nav #sidebar .sidebar-header { display: none !important; }
        html body.portal-ui.admin-dashboard-mobile-nav #sidebar {
            background: #C41E3A !important;
            background-image: none !important;
        }
        body.admin-dashboard-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }
        body.admin-dashboard-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        body.admin-dashboard-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        body.portal-ui.admin-dashboard-mobile-nav #sidebar nav :is(a, button),
        body.portal-ui.admin-dashboard-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }
        body.portal-ui.admin-dashboard-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        body.portal-ui.admin-dashboard-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }
        body.portal-ui.admin-dashboard-mobile-nav #sidebar nav :is(a, button):hover,
        body.portal-ui.admin-dashboard-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }
        body.portal-ui.admin-dashboard-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600, .bg-purple-700\/70),
        body.portal-ui.admin-dashboard-mobile-nav #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }
        body.admin-dashboard-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }
        body.admin-dashboard-mobile-nav #sidebarToggleBtn { display: none !important; }
        body.admin-dashboard-mobile-nav #mainContent {
            width: 100% !important;
            min-width: 0 !important;
            margin-left: 0 !important;
        }
        body.admin-dashboard-mobile-nav #admin-dashboard-header > div > div:first-child {
            min-width: 0;
            flex: 1 1 auto;
            flex-direction: row;
            align-items: center;
            gap: .625rem;
        }
        body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header > div {
            align-items: center;
            padding-top: max(1rem, env(safe-area-inset-top)) !important;
            padding-bottom: 1rem !important;
            padding-left: max(.75rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }
        body.admin-dashboard-mobile-nav #admin-dashboard-header > div > div:first-child > div {
            width: auto;
            flex: 1 1 auto;
            text-align: left;
        }
        body.admin-dashboard-mobile-nav #dashboardMenuToggle {
            display: inline-flex !important;
            flex: 0 0 2.5rem;
            width: 2.5rem;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff;
            margin-top: .15rem !important;
        }
        body.admin-dashboard-mobile-nav #dashboardMenuToggle > i {
            font-size: 1.25rem !important;
            line-height: 1 !important;
        }
        html body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header {
            background: #C41E3A !important;
            border-bottom-color: #99172E !important;
        }
        html body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header > div > div:first-of-type :is(h1, h2, h3, p) {
            color: #ffffff !important;
        }
        body.admin-dashboard-mobile-nav #admin-dashboard-header h2,
        body.admin-dashboard-mobile-nav #admin-dashboard-header p { min-width: 0; }
    }

    /* Dashboard tablet header styling (768px–1023px): white dashboard header with dark text */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        body.admin-dashboard-mobile-nav #dashboardMenuToggle { display: none !important; }
        html body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header > div > div:first-of-type :is(h1, h2, h3) {
            color: #1f2937 !important;
        }
        html body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header > div > div:first-of-type p {
            color: #6b7280 !important;
        }
    }
</style>
@endpush

@section('content')

@php
    $dashboardUser = Auth::user();
    $dashboardDepartments = collect($dashboardUser?->caseDepartments() ?? [])
        ->map(fn ($department) => strtoupper($department) === 'VAWC' ? 'SWD' : strtoupper($department))
        ->unique()
        ->values();
    $dashboardDepartment = $dashboardDepartments->isNotEmpty()
        ? $dashboardDepartments->implode(' & ')
        : 'SWD';
    $dashboardRole = match (strtolower(trim((string) $dashboardUser?->position))) {
        'administrator', 'admin' => 'Administrator',
        'desk officer' => 'Desk Officer',
        default => ucwords(strtolower(trim((string) ($dashboardUser?->position ?: 'User')))),
    };
@endphp

<!-- Top Header -->
<header id="admin-dashboard-header" data-dashboard-header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="flex items-center gap-3 min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Welcome, {{ $dashboardDepartment }} SILANG</h2>
                <p class="text-sm text-gray-500">{{ $dashboardDepartment }} {{ $dashboardRole }} - Silang, Cavite</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <!-- Notifications -->
            @include('admin-tailwind.components.notifications')
            
            <!-- User Menu -->
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Dashboard Content -->
<main id="admin-dashboard-main" class="px-4 pt-4 pb-4 sm:p-6 lg:p-8">
    <!-- Page Title -->
    <div class="flex items-start gap-3 mb-6">
        <span class="inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600" aria-hidden="true">
            <i class="fas fa-table-columns inline-flex items-center justify-center leading-none"></i>
        </span>
        <div class="min-w-0">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-700">Overview &amp; Case Statistics</h2>
            <p class="mt-1 text-xs sm:text-sm text-gray-500">Real-time summary of registered cases and active desk officers</p>
        </div>
    </div>
    
    <!-- Summary Statistics -->
    <div class="admin-dashboard-statistics grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <!-- Total Cases -->
        <div class="admin-dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="admin-dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-white text-lg sm:text-xl"></i>
                </div>
                <div class="admin-dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalCases ?? 0 }}</p>
                    <p class="admin-dashboard-statistic-label text-xs sm:text-sm text-gray-500">Total Cases</p>
                </div>
            </div>
        </div>
        
        <!-- Active Users -->
        <div class="admin-dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="admin-dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-white text-lg sm:text-xl"></i>
                </div>
                <div class="admin-dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
                    <p class="admin-dashboard-statistic-label text-xs sm:text-sm text-gray-500">Active Users</p>
                </div>
            </div>
        </div>
        
        <!-- Active Cases -->
        <div class="admin-dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="admin-dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-folder-open text-white text-lg sm:text-xl"></i>
                </div>
                <div class="admin-dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $activeCases ?? 0 }}</p>
                    <p class="admin-dashboard-statistic-label text-xs sm:text-sm text-gray-500">{{ Auth::user()->isVawcAdmin() ? 'VAWC Filed Cases' : (Auth::user()->isBcpcAdmin() ? 'BCPC Filed Cases' : 'Active Cases') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Today's Cases -->
        <div class="admin-dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="admin-dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-line text-white text-lg sm:text-xl"></i>
                </div>
                <div class="admin-dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $todayCases ?? 0 }}</p>
                    <p class="admin-dashboard-statistic-label text-xs sm:text-sm text-gray-500">Today's Cases</p>
                </div>
            </div>
        </div>
        
        <!-- Referred Cases -->
        <div class="admin-dashboard-statistic-card bg-white rounded-xl p-4 sm:p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow col-span-2 sm:col-span-1">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="admin-dashboard-statistic-icon w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-share-square text-white text-lg sm:text-xl"></i>
                </div>
                <div class="admin-dashboard-statistic-content">
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $referredCases ?? 0 }}</p>
                    <p class="admin-dashboard-statistic-label text-xs sm:text-sm text-gray-500">Referred Cases</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Grid -->
    <div id="admin-dashboard-charts" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Barangay Case Counts Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="admin-dashboard-chart-title px-5 py-4 border-b border-gray-100 flex items-start gap-2">
                <i class="fas fa-chart-bar text-purple-600 mt-1"></i>
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Cases by Barangay</h3>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Distribution of recorded cases across barangays.</p>
                </div>
            </div>
            <div class="admin-dashboard-chart-body p-4 sm:p-5">
                <div class="admin-dashboard-chart-canvas h-80 sm:h-96 relative">
                    <canvas id="barangayChart"></canvas>
                    @if(empty($barangayCases) || count($barangayCases) === 0)
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-bar text-4xl mb-2"></i>
                        <p class="text-sm">No case data available by barangay</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Case Types Distribution Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="admin-dashboard-chart-title px-5 py-4 border-b border-gray-100 flex items-start gap-2">
                <i class="fas fa-chart-pie text-purple-600 mt-1"></i>
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Case Types Distribution</h3>
                    <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Breakdown of recorded cases by case type.</p>
                </div>
            </div>
            <div class="admin-dashboard-chart-body p-4 sm:p-5">
                <div class="admin-dashboard-chart-canvas h-80 sm:h-96 relative">
                    <canvas id="caseTypesChart"></canvas>
                    @if(empty($caseTypes) || count($caseTypes) === 0)
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-2"></i>
                        <p class="text-sm">No case type data available</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @if(auth()->user()->isVawcAdmin() || auth()->user()->isBcpcAdmin())
        <div class="w-full lg:col-span-2 mt-2 self-start">
            @include('components.swd-dashboard-announcements', [
                'announcementUser' => auth()->user(),
                'announcementLimit' => 4,
                'announcementTwoColumns' => true,
            ])
        </div>
        @endif
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isMobileDashboard = window.matchMedia('(max-width: 26.875rem)').matches;
    // Barangay cases chart
    const barangayCtx = document.getElementById('barangayChart');
    if (barangayCtx) {
        let barangayData = {!! json_encode($barangayCases ?? []) !!};
        
        if (!Array.isArray(barangayData) || barangayData.length === 0) {
            barangayData = [{ barangay: 'No Data Available', count: 1, color: '#9ca3af' }];
        }
        
        // SWD displays every barangay so the chart total matches its department case total.
        barangayData.sort((a, b) => b.count - a.count);
        if (!@json(Auth::user()->isVawcAdmin()) && barangayData.length > 10) {
            barangayData = barangayData.slice(0, 10);
        }
        
        new Chart(barangayCtx, {
            type: 'bar',
            data: {
                labels: barangayData.map(item => item.barangay || 'Unassigned'),
                datasets: [{
                    label: 'Number of Cases',
                    data: barangayData.map(item => item.count),
                    backgroundColor: barangayData.map(item => item.color || '#8b5cf6'),
                    borderColor: barangayData.map(item => item.color || '#7c3aed'),
                    borderWidth: 1,
                    borderRadius: 6,
                    maxBarThickness: 40,
                    barPercentage: 0.8,
                    categoryPercentage: 0.9
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 12 } },
                        grid: { display: true, color: 'rgba(0, 0, 0, 0.05)' },
                        title: { display: true, text: 'Number of Cases', font: { size: 12, weight: 'bold' } }
                    },
                    y: {
                        ticks: { font: { size: 12 } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${ctx.parsed.x} case${ctx.parsed.x !== 1 ? 's' : ''}`
                        }
                    }
                },
                animation: { duration: 1000, easing: 'easeOutQuart' }
            }
        });
    }

    // Case types chart
    const typeCtx = document.getElementById('caseTypesChart');
    if (typeCtx) {
        const caseTypes = {!! json_encode(collect($caseTypes ?? [])->sortByDesc('count')->values()) !!};
        
        if (caseTypes.length > 0) {
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: caseTypes.map(item => item.name),
                    datasets: [{
                        data: caseTypes.map(item => item.count),
                        backgroundColor: caseTypes.map(item => '#' + item.color),
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: isMobileDashboard ? 'bottom' : 'right',
                            labels: {
                                padding: isMobileDashboard ? 8 : 15,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: isMobileDashboard ? 10 : 12 },
                                generateLabels: chart => {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const dataset = data.datasets[0];
                                            const value = dataset.data[i];
                                            return {
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
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((ctx.raw / total) * 100);
                                    return `${ctx.label}: ${ctx.raw} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: { animateScale: true, animateRotate: true, duration: 1000 }
                }
            });
        }
    }
});
</script>
@endpush

@endsection
