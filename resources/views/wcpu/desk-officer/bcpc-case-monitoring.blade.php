@extends('layouts.desk-officer')

@section('title', 'BCPC Case Monitoring')

@push('styles')
<style>
    .bcpc-monitoring-actions > * { display: inline-flex !important; width: 2.75rem !important; height: 1.875rem !important; min-width: 2.75rem !important; min-height: 1.875rem !important; padding: 0 !important; margin: 0 !important; align-items: center !important; justify-content: center !important; gap: 0 !important; text-align: center !important; }
    .bcpc-monitoring-actions > * > i { display: inline-flex !important; width: 1rem !important; height: 1rem !important; margin: 0 !important; padding: 0 !important; align-items: center !important; justify-content: center !important; line-height: 1 !important; flex: 0 0 1rem !important; text-align: center !important; }
    .bcpc-monitoring-actions > * > span { display: none !important; }
    .bcpc-monitoring-actions > :is(a, button) { background-color: #C41E3A !important; border-color: #C41E3A !important; color: #ffffff !important; transition: none !important; }
    .bcpc-monitoring-actions > :is(a, button):is(:hover, :focus, :active) { background-color: #A01830 !important; border-color: #A01830 !important; color: #ffffff !important; }
    #bcpc-monitoring-filters .monitoring-filter-actions {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(0, 1fr);
        align-items: stretch;
        flex: 1 1 24rem;
        min-width: 0;
        width: 100%;
        gap: .75rem;
    }
    #bcpc-monitoring-filters .monitoring-filter-actions > :is(a, button) {
        width: 100%;
        min-width: 0;
        min-height: 2.75rem;
        padding: .625rem .75rem;
        border-radius: .5rem;
        line-height: 1.25;
        text-align: center;
    }
    #bcpc-monitoring-filters .monitoring-filter-actions > :is(a, button) > i { flex-shrink: 0; }
    @media (max-width: 39.9375rem) {
        #bcpc-monitoring-filters .monitoring-filter-actions {
            flex: 0 0 auto;
            grid-auto-flow: row;
            grid-template-columns: minmax(0, 1fr);
        }
    }
    @media screen and (max-width: 80rem) {
        .bcpc-monitoring-table-view { display: none !important; }
        .bcpc-monitoring-mobile-cards { display: grid !important; grid-template-columns: minmax(0, 1fr); gap: .75rem; padding: .75rem; }
        .bcpc-monitoring-mobile-cards > :not(article) { grid-column: 1 / -1; }
        .bcpc-monitoring-mobile-card { min-width: 0; padding: 1rem; }
        .bcpc-monitoring-mobile-card .bcpc-monitoring-actions { display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .5rem; padding-top: .75rem; }
        .bcpc-monitoring-mobile-card .bcpc-monitoring-actions > :is(a, button) { width: 100% !important; height: 3rem !important; min-width: 3rem !important; min-height: 3rem !important; padding: 0 !important; }
    }
    @media screen and (min-width: 48rem) and (max-width: 80rem) {
        .bcpc-monitoring-mobile-cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    /* Dashboard-style red navbar for mobile/tablet, matching Case Profiles */
    @media (max-width: 80rem) {
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mobileTopBar {
            display: none !important;
        }
    }

    @media (max-width: 47.9375rem) {
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mobileTopBar {
            display: none !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebarCloseBtn,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header > div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:first-of-type,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header > div > div:first-of-type {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:first-of-type > div,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header > div > div:first-of-type > div {
            min-width: 0 !important;
            flex: 1 1 auto !important;
            text-align: left !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
            font-size: 1.125rem !important;
            position: relative !important;
            left: 0 !important;
            margin-left: 0 !important;
            transform: none !important;
            display: inline-flex !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) i {
            color: inherit !important;
            font-size: inherit !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header p {
            display: block !important;
            margin-top: .125rem !important;
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p :is(span, strong),
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header p :is(span, strong) {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) div:first-of-type :is(span, a),
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:last-child,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header > div > div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
            background: var(--mswdo-profile, #BC0202) !important;
            border: 1px solid #8f0202 !important;
            box-shadow: 0 2px 5px rgba(188, 2, 2, .18) !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            position: relative !important;
            margin: 0 !important;
            line-height: 1 !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            vertical-align: middle !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #mainContent > header#desk-officer-dashboard-header #notifications-unread-count {
            position: absolute !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            padding: 0 !important;
            font-size: .75rem !important;
            line-height: 1 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border: 2px solid #C41E3A !important;
            border-radius: 9999px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2) !important;
        }

        /* Sidebar dropdown drawer below header */
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar {
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
            background: #C41E3A !important;
            background-image: none !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar nav :is(a, button),
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar nav :is(a, button):hover,
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600, .bg-purple-700\/70),
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }
    }

    @media (max-width: 40rem) {
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #desk-officer-dashboard-header button:has(> img[alt="User Profile"]) {
            display: none !important;
        }
    }

    @media (min-width: 48rem) {
        html body.portal-ui.desk-officer-bcpc-monitoring-mobile-nav #pageMobileMenuToggle {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'BCPC Case Monitoring', 'subtitle' => 'View and manage BCPC case monitoring records'])
<div class="w-full max-w-full p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">BCPC Case Monitoring</h1>
        </div>
        @endunless
        @unless(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->defaultCaseDepartment() === 'BCPC' || auth()->user()?->isMultiCaseDepartment()))
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex flex-wrap items-center gap-3">

            <button onclick="document.getElementById('addMonitoringModal').classList.remove('hidden')"
               class="inline-flex items-center gap-2 px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition">
                <i class="fas fa-plus"></i>
                <span>Add Case Monitoring</span>
            </button>
            </div>
        </div>
        @endunless
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <form id="bcpc-monitoring-filters" method="GET" action="{{ route('desk-officer.bcpc-case-monitoring.edit') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-end gap-3 sm:gap-4 flex-wrap">
            <div class="w-full sm:w-40">
                <label for="monitoring-quarter" class="block text-sm font-medium text-gray-600 mb-1">Quarter</label>
                <select id="monitoring-quarter" name="quarter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="">All Quarters</option>
                    @foreach([1, 2, 3, 4] as $quarterOption)
                        <option value="{{ $quarterOption }}" {{ (request('quarter') == $quarterOption || (!request()->has('quarter') && ($quarter ?? '') == $quarterOption)) ? 'selected' : '' }}>Q{{ $quarterOption }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex w-full min-w-0 items-end gap-3 sm:w-auto">
            <div class="min-w-0 flex-1 sm:w-40 sm:flex-none">
                <label for="monitoring-year" class="block text-sm font-medium text-gray-600 mb-1">Year</label>
                <select id="monitoring-year" name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="">All Years</option>
                    @foreach(range(2026, 2030) as $yearOption)
                        <option value="{{ $yearOption }}" {{ (request('year') == $yearOption || (!request()->has('year') && ($year ?? '') == $yearOption)) ? 'selected' : '' }}>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('desk-officer.bcpc-case-monitoring.edit') }}" class="inline-flex shrink-0 items-center justify-center gap-2 px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium shadow-sm transition">
                <i class="fas fa-times"></i>
                <span>Clear</span>
            </a>
            </div>
            <div class="monitoring-filter-actions">
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium shadow-sm transition w-full sm:w-auto">
                <i class="fas fa-sync-alt"></i>
                <span>Load Monitoring</span>
            </button>
            @if(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->defaultCaseDepartment() === 'BCPC' || auth()->user()?->isMultiCaseDepartment()))
            <button type="button" onclick="resetAddMonitoringForm(); document.getElementById('addMonitoringModal').classList.remove('hidden')" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition w-full sm:w-auto">
                <i class="fas fa-plus"></i>
                <span>Add Case Monitoring</span>
            </button>
            @endif
            </div>
        </div>
    </form>

    <!-- Monitoring Table & Cards -->
    <div data-ajax-pagination="monitoring" data-ajax-page="page" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <!-- Desktop Table View -->
        <div class="bcpc-monitoring-table-view overflow-x-auto hidden md:block">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white">
                    <tr>
                        <th class="px-5 py-3.5 text-left font-semibold text-xs uppercase tracking-wider">Barangay</th>
                        <th class="px-5 py-3.5 text-center font-semibold text-xs uppercase tracking-wider">Period</th>
                        <th class="px-3 py-3.5 text-center font-semibold text-xs uppercase tracking-wider">Date</th>
                        <th class="px-3 py-3.5 text-center font-semibold text-xs uppercase tracking-wider">Time</th>
                        <th class="px-5 py-3.5 text-left font-semibold text-xs uppercase tracking-wider">Submitted By</th>
                        <th class="px-5 py-3.5 text-left font-semibold text-xs uppercase tracking-wider">Submitted At</th>
                        <th class="px-5 py-3.5 text-center font-semibold text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($monitorings as $monitoring)
                        <tr class="hover:bg-purple-50/40 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center font-semibold flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-sm"></i>
                                    </div>
                                    <span class="text-gray-800 font-semibold">{{ $monitoring->barangay->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-calendar-alt text-xs"></i>
                                    Q{{ $monitoring->quarter }} {{ $monitoring->year }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-center text-sm text-gray-700 whitespace-nowrap">
                                {{ $monitoring->created_at?->timezone('Asia/Manila')->format('M d, Y') ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-4 text-center text-sm text-gray-700 whitespace-nowrap">
                                {{ $monitoring->created_at?->timezone('Asia/Manila')->format('g:i A') ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-gray-800 font-medium">
                                    {{ $monitoring->creator?->first_name }} {{ $monitoring->creator?->last_name }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $monitoring->creator?->email }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-700">{{ $monitoring->submitted_at?->timezone('Asia/Manila')->format('M d, Y') ?? 'Not submitted' }}</div>
                                <div class="text-xs text-gray-400">{{ $monitoring->submitted_at?->timezone('Asia/Manila')->format('g:i A') ?? '' }}</div>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <div class="bcpc-monitoring-actions flex items-center justify-center gap-2">
                                    <a href="{{ route('desk-officer.bcpc-case-monitoring.edit', ['quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'view' => $monitoring->id]) }}"
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                       title="View Record">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="{{ route('desk-officer.bcpc-case-monitoring.edit', ['quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'edit' => $monitoring->id]) }}"
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                       title="Edit Record">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                    <button type="button"
                                            onclick="sendToAdmin({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')"
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                            title="Send to Admin">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>Send to Admin</span>
                                    </button>
                                    <button type="button" 
                                            onclick="archiveMonitoring({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')"
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 border border-red-200 rounded-lg text-xs font-medium transition"
                                            title="Archive Record">
                                        <i class="fas fa-archive"></i>
                                        <span>Archive</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-purple-50 text-purple-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-inbox text-3xl"></i>
                                </div>
                                <p class="text-gray-700 font-semibold text-base">No BCPC case monitoring records found.</p>
                                <p class="text-gray-400 text-xs mt-1">Submitted case monitoring reports will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phone and Tablet Cards View -->
        <div class="bcpc-monitoring-mobile-cards md:hidden">
            @forelse($monitorings as $monitoring)
                <article class="bcpc-monitoring-mobile-card border border-gray-100 rounded-xl bg-white shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 bg-purple-100 text-purple-700 rounded-lg flex items-center justify-center font-bold flex-shrink-0 text-sm">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="truncate">
                                <h3 class="font-bold text-gray-800 text-sm truncate">{{ $monitoring->barangay->name ?? 'N/A' }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $monitoring->creator?->first_name }} {{ $monitoring->creator?->last_name }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0">
                            <i class="fas fa-calendar-alt text-[10px]"></i>
                            Q{{ $monitoring->quarter }} {{ $monitoring->year }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3 bg-gray-50 rounded-lg px-3 py-2">
                        <span>Submitted At:</span>
                        <span class="font-medium text-gray-700">{{ $monitoring->submitted_at?->timezone('Asia/Manila')->format('M d, Y g:i A') ?? 'Not submitted' }}</span>
                    </div>

                    <div class="bcpc-monitoring-actions border-t border-gray-100">
                        <a href="{{ route('desk-officer.bcpc-case-monitoring.edit', ['quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'view' => $monitoring->id]) }}"
                           class="inline-flex items-center justify-center gap-1 px-2 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                           title="View Record">
                            <i class="fas fa-eye text-xs"></i>
                            <span>View</span>
                        </a>
                        <a href="{{ route('desk-officer.bcpc-case-monitoring.edit', ['quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'edit' => $monitoring->id]) }}"
                           class="inline-flex items-center justify-center gap-1 px-2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                           title="Edit Record">
                            <i class="fas fa-edit text-xs"></i>
                            <span>Edit</span>
                        </a>
                        <button type="button"
                                onclick="sendToAdmin({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')"
                                class="inline-flex items-center justify-center gap-1 px-2 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                title="Send to Admin">
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span class="leading-tight">Send to Admin</span>
                        </button>
                        <button type="button" 
                                onclick="archiveMonitoring({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')"
                                class="inline-flex items-center justify-center gap-1 px-2 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-xs font-medium transition"
                                title="Archive Record">
                            <i class="fas fa-archive text-xs"></i>
                            <span>Archive</span>
                        </button>
                    </div>
                </article>
            @empty
                <div class="text-center py-10 text-gray-500">
                    <div class="w-14 h-14 bg-purple-50 text-purple-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <p class="text-gray-700 font-semibold text-sm">No BCPC case monitoring records found.</p>
                </div>
            @endforelse
        </div>

        @if($monitorings->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Showing {{ $monitorings->firstItem() ?? 0 }} - {{ $monitorings->lastItem() ?? 0 }} of {{ $monitorings->total() }} records
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ $monitorings->appends(request()->query())->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $monitorings->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                        <i class="fas fa-chevron-left text-gray-600"></i>
                    </a>
                    @php
                        $current = $monitorings->currentPage();
                        $last = $monitorings->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp
                    <div class="flex items-center gap-1">
                        @if($start > 1)
                            <a href="{{ $monitorings->appends(request()->query())->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">1</a>
                            @if($start > 2)<span class="px-2 text-gray-400">...</span>@endif
                        @endif
                        @for($page = $start; $page <= $end; $page++)
                            <a href="{{ $monitorings->appends(request()->query())->url($page) }}" class="inline-flex items-center justify-center w-9 h-9 {{ $current == $page ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} rounded-lg text-sm">{{ $page }}</a>
                        @endfor
                        @if($end < $last)
                            @if($end < $last - 1)<span class="px-2 text-gray-400">...</span>@endif
                            <a href="{{ $monitorings->appends(request()->query())->url($last) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">{{ $last }}</a>
                        @endif
                    </div>
                    <a href="{{ $monitorings->appends(request()->query())->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $monitorings->currentPage() == $monitorings->lastPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                        <i class="fas fa-chevron-right text-gray-600"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add Monitoring Modal -->
<div id="addMonitoringModal" class="fixed inset-0 bg-black/50 hidden flex items-start sm:items-center justify-center overflow-y-auto p-4 z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-hidden relative flex flex-col">
        <div class="bg-[#C41E3A] px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>Add BCPC Case Monitoring</span>
            </h3>
            <button type="button" onclick="resetAddMonitoringForm(); document.getElementById('addMonitoringModal').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="add-monitoring-form" method="GET" action="{{ route('desk-officer.bcpc-case-monitoring.edit') }}" class="min-h-0 overflow-y-auto p-4 sm:p-6">
            <input type="hidden" name="add" value="1">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                    <input type="text" readonly value="{{ $barangay->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-medium cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                    <select name="quarter" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Quarter</option>
                        @foreach([1, 2, 3, 4] as $quarterOption)
                            <option value="{{ $quarterOption }}">Q{{ $quarterOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Year</option>
                        @for($yearOption = now()->year; $yearOption <= now()->year + 2; $yearOption++)
                            <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="resetAddMonitoringForm(); document.getElementById('addMonitoringModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-[#C41E3A] hover:bg-[#a71830] text-white rounded-lg font-medium transition">Continue</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<div id="bcpc-monitoring-flash"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}"
     data-validation-errors='@json($errors->all())'
     hidden></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    @if(auth()->user()->defaultCaseDepartment() === 'BCPC' && !auth()->user()->isMultiCaseDepartment())
    const existingPeriods = @json($existingMonitoringPeriods ?? []);
    const addModal = document.getElementById('addMonitoringModal');
    const quarterSelect = addModal.querySelector('select[name="quarter"]');
    const yearSelect = addModal.querySelector('select[name="year"]');
    const updateAvailablePeriods = () => {
        for (const option of quarterSelect.options) {
            if (!option.value) continue;
            option.disabled = !!yearSelect.value && existingPeriods.includes(`${option.value}-${yearSelect.value}`);
            if (option.disabled && option.selected) quarterSelect.value = '';
        }
        for (const option of yearSelect.options) {
            if (!option.value) continue;
            option.disabled = quarterSelect.value
                ? existingPeriods.includes(`${quarterSelect.value}-${option.value}`)
                : [1, 2, 3, 4].every(quarter => existingPeriods.includes(`${quarter}-${option.value}`));
            if (option.disabled && option.selected) yearSelect.value = '';
        }
    };
    yearSelect.addEventListener('change', updateAvailablePeriods);
    quarterSelect.addEventListener('change', updateAvailablePeriods);
    updateAvailablePeriods();
    @endif
    // Show success/error messages as SweetAlert2 popups
    const flash = document.getElementById('bcpc-monitoring-flash');
    const successMessage = flash?.dataset.success;
    const errorMessage = flash?.dataset.error;
    const validationErrors = JSON.parse(flash?.dataset.validationErrors || '[]');

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: successMessage,
            confirmButtonColor: '#10b981',
            timer: 3000,
            timerProgressBar: true
        });
    }

    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            confirmButtonColor: '#ef4444'
        });
    }

    if (validationErrors.length) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: '<ul style="text-align: left; padding-left: 20px;">' + 
                  validationErrors.map(msg => '<li>' + msg + '</li>').join('') +
                  '</ul>',
            confirmButtonColor: '#ef4444'
        });
    }
});

function resetAddMonitoringForm() {
    const form = document.getElementById('add-monitoring-form');
    if (!form) return;

    form.reset();
    form.querySelectorAll('select').forEach((select) => {
        select.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

function sendToAdmin(id, barangayName, period) {
    const alreadySent = @json(auth()->user()->defaultCaseDepartment() === 'BCPC' && !auth()->user()->isMultiCaseDepartment()) && @json($monitorings->filter(fn ($record) => $record->submitted_at !== null)->pluck('id')->values()).includes(id);
    Swal.fire({
        title: alreadySent ? 'Send to Admin Again?' : 'Send to Admin?',
        html: `
            <div class="text-center py-2">
                <i class="fas fa-paper-plane text-5xl text-emerald-500 mb-4"></i>
                <p class="text-gray-700 font-medium">${alreadySent ? 'This report has already been sent. Are you sure you want to send it again?' : `Are you sure you want to send the BCPC case monitoring for <strong>${barangayName || 'this barangay'}</strong> (${period || ''}) to the Administrator?`}</p>
                <p class="text-gray-400 text-xs mt-2">This will update the quarterly compliance report and notify the Admin office.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Yes, Send to Admin',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sending to Admin...',
                text: 'Please wait while we submit the report to Admin.',
                allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/desk-officer/bcpc-case-monitoring/${id}/send-to-admin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sent Successfully!',
                        text: data.message || 'Report has been sent to Admin.',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to send report to Admin.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'An error occurred while sending report to Admin.', 'error'));
        }
    });
}

function archiveMonitoring(id, barangayName, period) {
    Swal.fire({
        title: 'Archive Case Monitoring?',
        html: `
            <div class="text-center py-2">
                <i class="fas fa-archive text-5xl text-amber-500 mb-4"></i>
                <p class="text-gray-700 font-medium">Are you sure you want to archive the BCPC case monitoring for <strong>${barangayName || 'this barangay'}</strong> (${period || ''})?</p>
                <p class="text-gray-400 text-xs mt-2">This record will be moved to the BCPC Monitoring Archive and can be restored anytime.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Archive',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Archiving...',
                text: 'Please wait while we archive this record.',
                allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(@json(route('desk-officer.bcpc-case-monitoring.archive', ['id' => '__ID__'])).replace('__ID__', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Archived!',
                        text: data.message || 'Record has been moved to archive.',
                           confirmButtonText: 'OK'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to archive record.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'An error occurred while archiving the record.', 'error'));
        }
    });
}
</script>
@endpush
@endsection
