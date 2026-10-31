@extends('layouts.admin-tailwind')

@section('title', 'BCPC Case Monitoring')

@section('content')
@if(auth()->user()->isDepartmentAdmin())
<header data-bcpc-monitoring-page-header class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header {{ auth()->user()->isVawcAdmin() ? 'bcpc-monitoring-mobile-header-swd-only' : '' }}">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">BCPC Case Monitoring</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">Manage and View bcpc monitoring.</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
<style>
    @if(auth()->user()?->isDepartmentAdmin())
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        #bcpc-monitoring-primary-actions { justify-content: flex-end; }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: landscape) {
        #bcpc-monitoring-primary-actions { justify-content: flex-end; }
    }
    @media (max-width: 47.9375rem) {
        html body.portal-ui #bcpc-monitoring-pagination .monitoring-legacy-pagination { display: none !important; }
        html body.portal-ui #bcpc-monitoring-pagination .monitoring-tablet-pagination { display: block !important; background: #fff; }
        #bcpc-monitoring-pagination .monitoring-tablet-pagination > [data-pagination-label] {
            flex-direction: column; justify-content: center; gap: .5rem; padding: .75rem; text-align: center;
        }
        #bcpc-monitoring-pagination .monitoring-tablet-pagination [data-pagination-label] > div:last-child {
            justify-content: center; gap: clamp(.25rem, 2vw, .625rem); max-width: 100%;
        }
        #bcpc-monitoring-pagination .monitoring-tablet-pagination a {
            flex: 0 0 auto; width: clamp(2rem, 10vw, 2.75rem) !important; min-width: 0 !important;
            height: 2.75rem !important; padding: 0 !important; margin: 0 !important;
            border: 1px solid #e5e7eb !important; border-radius: .5rem;
            background: #fff !important; color: #C41E3A !important;
            box-shadow: 0 1px 2px rgb(0 0 0 / 4%);
        }
        #bcpc-monitoring-pagination .monitoring-tablet-pagination a[class~="bg-[#C41E3A]"] {
            background: #C41E3A !important; border-color: #C41E3A !important; color: #fff !important;
        }
        #bcpc-monitoring-pagination .monitoring-tablet-pagination a > i { color: inherit !important; }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui.admin-bcpc-case-monitoring-mobile-nav .monitoring-legacy-pagination { display: none !important; }
        html body.portal-ui.admin-bcpc-case-monitoring-mobile-nav .monitoring-tablet-pagination { display: block !important; background: #fff; }
    }
    @endif
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui.admin-bcpc-case-monitoring-mobile-nav .bcpc-monitoring-mobile-cards > .bcpc-monitoring-empty-state {
            grid-column: 1 / -1;
            width: 100%;
            text-align: center;
        }
    }
    .bcpc-monitoring-mobile-header-swd-only { display: none; }
    .bcpc-monitoring-actions > * { display: inline-flex !important; width: 2.75rem !important; height: 1.875rem !important; min-width: 2.75rem !important; min-height: 1.875rem !important; padding: 0 !important; margin: 0 !important; align-items: center !important; justify-content: center !important; gap: 0 !important; text-align: center !important; }
    .bcpc-monitoring-actions > * > i { display: inline-flex !important; width: 1rem !important; height: 1rem !important; margin: 0 !important; padding: 0 !important; align-items: center !important; justify-content: center !important; line-height: 1 !important; flex: 0 0 1rem !important; text-align: center !important; }
    .bcpc-monitoring-actions > * > span { display: none !important; }

    /* BCPC Case Monitoring tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #dashboardMenuToggle { display: none !important; }
        html body.portal-ui #sidebarCloseBtn { display: none !important; }
        html body.portal-ui #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header,
        html body.portal-ui #mainContent > header.sticky {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header h2,
        html body.portal-ui #mainContent > header.sticky h2 {
            color: #1f2937 !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header p,
        html body.portal-ui #mainContent > header.sticky p {
            color: #6b7280 !important;
        }
        html body.portal-ui #sidebar {
            inset: 0 auto 0 0 !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            right: auto !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
            background: #C41E3A !important;
            border-radius: 0 !important;
            z-index: 40 !important;
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
            transition: width 0.3s ease !important;
        }
        html body.portal-ui #sidebar .sidebar-header {
            display: flex !important;
            min-height: 5.5rem;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #sidebar .sidebar-header img {
            width: 3rem !important;
            height: 3rem !important;
            flex-shrink: 0 !important;
        }
        html body.portal-ui #sidebar .sidebar-header .sidebar-text,
        html body.portal-ui #sidebar .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #sidebar .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui #sidebar .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #sidebar .sidebar-link i {
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            text-align: center !important;
        }
        html body.portal-ui #sidebar ~ #mainContent,
        html body.portal-ui #sidebar.sidebar-collapsed ~ #mainContent,
        html body.portal-ui #sidebar.sidebar-collapsed-init ~ #mainContent {
            width: calc(100% - 4.5rem) !important;
            margin-left: 4.5rem !important;
            min-width: 0 !important;
        }
        html body.portal-ui #sidebarToggleBtn {
            display: none !important;
        }
    }

    @media (min-width: 48rem) and (max-width: 80rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #sidebar {
            inset: 0 auto 0 0 !important;
            height: 100vh !important;
            height: 100dvh !important;
        }
    }

    @if(auth()->user()->isDepartmentAdmin())
    @media (max-width: 40rem) {
        .bcpc-monitoring-mobile-header-swd-only { display: block !important; }
        .bcpc-monitoring-content-title { display: none !important; }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div {
            column-gap: .75rem !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div > .admin-reference-mobile-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-title {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header #dashboardMenuToggle {
            display: inline-flex !important;
            position: relative !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] {
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
    }

    @media (max-width: 47.9375rem) {
        #bcpc-monitoring-mobile-action-flow {
            display: flex;
            flex-direction: column;
            gap: .75rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            box-shadow: 0 1px 2px rgb(0 0 0 / .05);
        }
        #bcpc-monitoring-filter-form {
            order: 1;
            margin: 0;
            padding: 0;
            background: transparent;
            border: 0;
            box-shadow: none;
        }
        #bcpc-monitoring-primary-actions {
            order: 2;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: .5rem;
            margin: 0;
        }
        #bcpc-monitoring-primary-actions > button {
            width: 100%;
            min-height: 2.5rem;
            justify-content: center;
        }
    }
    @endif

    /* Monitoring cards for phones and portrait tablets. */
    @media (max-width: 47.9375rem), (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        .bcpc-monitoring-table-view { display: none !important; }
        .bcpc-monitoring-mobile-cards { display: grid !important; gap: .75rem; padding: .75rem; }
        .bcpc-monitoring-mobile-card { min-width: 0; padding: 1rem; }
        .bcpc-monitoring-card-actions {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .5rem;
            padding-top: .75rem;
        }
        .bcpc-monitoring-card-actions > * {
            width: 100% !important;
            height: 3rem !important;
            min-width: 3rem !important;
            min-height: 3rem !important;
            border-radius: .5rem !important;
        }
    }

    @media (min-width: 48rem) {
        .bcpc-monitoring-mobile-cards { display: none !important; }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        .bcpc-monitoring-mobile-cards { display: grid !important; }
        #bcpc-monitoring-filter-form > div {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) auto auto;
            align-items: end;
            gap: .75rem;
        }
        #bcpc-monitoring-filter-form > div > div { width: 100%; min-width: 0; }
        #bcpc-monitoring-filter-form :is(select, button, a) {
            box-sizing: border-box;
            height: 2.5rem;
            min-height: 2.5rem;
        }
        #bcpc-monitoring-filter-form :is(button, a) {
            padding-inline: .75rem;
            white-space: nowrap;
        }
    }
    @media (max-width: 47.9375rem) {
        #bcpc-monitoring-mobile-action-flow { display: flex; flex-direction: column; gap: .75rem; }
        #bcpc-monitoring-primary-actions,
        #bcpc-monitoring-filter-form,
        #bcpc-monitoring-filter-form > div { display: contents !important; }
        #bcpc-monitoring-filter-form > div > div { order: 0; width: 100%; }
        #bcpc-monitoring-filter-form button[type="submit"] { order: 1; }
        #bcpc-monitoring-primary-actions > button:first-child { order: 2; }
        #bcpc-monitoring-primary-actions > button:nth-child(2) { order: 3; }
        #bcpc-monitoring-filter-form > div > a { order: 4; }
        #bcpc-monitoring-mobile-action-flow :is(button, a) { width: 100%; justify-content: center; }
    }
</style>
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        @unless(auth()->user()->isBcpcAdmin())
        <div>
            <h1 class="bcpc-monitoring-content-title text-2xl sm:text-3xl font-bold text-gray-800">BCPC Case Monitoring</h1>
        </div>
        @endunless
        <div class="flex flex-wrap items-center gap-3">
            @unless(auth()->user()->isBcpcAdmin())
            <a href="{{ route('admin.bcpc-case-monitoring.archived') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-medium shadow-sm transition">
                <i class="fas fa-archive"></i>
                <span>Archived Records</span>
                @if(($archivedCount ?? 0) > 0)
                    <span class="bg-white/25 px-2 py-0.5 rounded-full text-xs font-bold">{{ $archivedCount }}</span>
                @endif
            </a>
            @endunless
        </div>
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

    <div id="bcpc-monitoring-mobile-action-flow">
    <div id="bcpc-monitoring-primary-actions" class="flex flex-wrap items-center gap-3 mb-6">
        <button onclick="resetGenerateMonitoringReportForm(); document.getElementById('generateReportModal').classList.remove('hidden')"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium shadow-sm transition">
            <i class="fas fa-file-alt"></i>
            <span>Generate Monitoring Report</span>
        </button>
        @if(auth()->user()->isBcpcAdmin())
        <button type="button" onclick="resetAddMonitoringForm(); document.getElementById('addMonitoringModal').classList.remove('hidden')"
           class="inline-flex items-center gap-2 px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition">
            <i class="fas fa-plus"></i>
            <span>Add New Case</span>
        </button>
        @endif
    </div>

    <!-- Filters -->
    <form id="bcpc-monitoring-filter-form" method="GET" action="{{ route('admin.bcpc-case-monitoring.edit') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <input type="hidden" name="barangay_id" value="{{ $barangayId }}">
        <div class="flex flex-col sm:flex-row sm:items-end gap-3 sm:gap-4 flex-wrap">
            <div class="w-full sm:w-40">
                <label for="monitoring-quarter" class="block text-sm font-medium text-gray-600 mb-1">Quarter</label>
                <select id="monitoring-quarter" name="quarter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="">All Quarters</option>
                    @foreach([1, 2, 3, 4] as $quarterOption)
                        <option value="{{ $quarterOption }}" {{ $quarter == $quarterOption ? 'selected' : '' }}>Q{{ $quarterOption }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <label for="monitoring-year" class="block text-sm font-medium text-gray-600 mb-1">Year</label>
                <select id="monitoring-year" name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="">All Years</option>
                    @foreach(range(2026, 2030) as $yearOption)
                        <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium shadow-sm transition w-full sm:w-auto">
                <i class="fas fa-sync-alt"></i>
                <span>Load Monitoring</span>
            </button>
            <a href="{{ route('admin.bcpc-case-monitoring.edit') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium shadow-sm transition w-full sm:w-auto">
                <i class="fas fa-times"></i>
                <span>Clear</span>
            </a>
        </div>
    </form>
    </div>

    <!-- Monitoring Table & Cards -->
    <div id="bcpc-monitoring-results" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <!-- Desktop Table View -->
        <div class="bcpc-monitoring-table-view overflow-x-auto hidden md:block">
            <table class="w-full min-w-[700px] text-sm table-fixed">
                <colgroup>
                    <col class="w-[26%]">
                    <col class="w-[16%]">
                    <col class="w-[24%]">
                    <col class="w-[16%]">
                    <col class="w-[18%] min-w-[13rem]">
                </colgroup>
                <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white">
                    <tr>
                        <th class="px-4 py-3.5 sm:px-5 text-left font-semibold text-xs uppercase tracking-wider">Barangay</th>
                        <th class="px-4 py-3.5 sm:px-5 text-center font-semibold text-xs uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3.5 sm:px-5 text-left font-semibold text-xs uppercase tracking-wider">Submitted By</th>
                        <th class="px-4 py-3.5 sm:px-5 text-left font-semibold text-xs uppercase tracking-wider">Submitted At</th>
                        <th class="px-4 py-3.5 sm:px-5 text-center font-semibold text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($monitorings as $monitoring)
                        <tr class="hover:bg-purple-50/40 transition">
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4">
                                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                    <div class="w-9 h-9 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center font-semibold flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-sm"></i>
                                    </div>
                                    <span class="text-gray-800 font-semibold truncate">{{ $monitoring->barangay->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 bg-purple-100 text-purple-700 px-2.5 sm:px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-calendar-alt text-xs"></i>
                                    Q{{ $monitoring->quarter }} {{ $monitoring->year }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4 min-w-0">
                                <div class="text-gray-800 font-medium truncate">
                                    {{ $monitoring->submitter?->first_name ?? $monitoring->creator?->first_name }} {{ $monitoring->submitter?->last_name ?? $monitoring->creator?->last_name }}
                                </div>
                                <div class="text-xs text-gray-400 truncate">
                                    {{ $monitoring->submitter?->email ?? $monitoring->creator?->email }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4 text-gray-600 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-700">{{ $monitoring->submitted_at?->timezone('Asia/Manila')->format('M d, Y') ?? 'Not submitted' }}</div>
                                <div class="text-xs text-gray-400">{{ $monitoring->submitted_at?->timezone('Asia/Manila')->format('g:i A') ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4 text-center whitespace-nowrap">
                                <div class="bcpc-monitoring-actions flex items-center justify-center gap-2">
                                    <a @if(auth()->user()?->isDepartmentAdmin()) style="background: #2563EB !important; border-color: #2563EB !important;" @endif href="{{ route('admin.bcpc-case-monitoring.edit', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'view' => $monitoring->id]) }}"
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                       title="View Record">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </a>
                                    <a @if(auth()->user()?->isDepartmentAdmin()) style="background: #D97706 !important; border-color: #D97706 !important;" @endif href="{{ route('admin.bcpc-case-monitoring.edit', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'edit' => $monitoring->id]) }}"
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                       title="Edit Record">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a @if(auth()->user()?->isDepartmentAdmin()) style="background: #0F766E !important; border-color: #0F766E !important;" @endif href="{{ route('admin.bcpc-case-monitoring.print', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year]) }}"
                                       target="_blank"
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                       title="Print Record" data-monitoring-print>
                                        <i class="fas fa-print"></i>
                                        <span>Print</span>
                                    </a>
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
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">
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

        <!-- Mobile Cards View -->
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
                                <p class="text-xs text-gray-500 truncate">{{ $monitoring->submitter?->first_name ?? $monitoring->creator?->first_name }} {{ $monitoring->submitter?->last_name ?? $monitoring->creator?->last_name }}</p>
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

                    <div class="bcpc-monitoring-actions bcpc-monitoring-card-actions border-t border-gray-100">
                        <a @if(auth()->user()?->isDepartmentAdmin()) style="background: #2563EB !important; border-color: #2563EB !important;" @endif href="{{ route('admin.bcpc-case-monitoring.edit', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'view' => $monitoring->id]) }}"
                           class="inline-flex items-center justify-center gap-1 px-2 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                           title="View Record">
                            <i class="fas fa-eye text-xs"></i>
                            <span>View</span>
                        </a>
                        <a @if(auth()->user()?->isDepartmentAdmin()) style="background: #D97706 !important; border-color: #D97706 !important;" @endif href="{{ route('admin.bcpc-case-monitoring.edit', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'edit' => $monitoring->id]) }}"
                           class="inline-flex items-center justify-center gap-1 px-2 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                           title="Edit Record">
                            <i class="fas fa-edit text-xs"></i>
                            <span>Edit</span>
                        </a>
                        <a @if(auth()->user()?->isDepartmentAdmin()) style="background: #0F766E !important; border-color: #0F766E !important;" @endif href="{{ route('admin.bcpc-case-monitoring.print', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year]) }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-1 px-2 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                           title="Print Record" data-monitoring-print>
                            <i class="fas fa-print text-xs"></i>
                            <span>Print</span>
                        </a>
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
                <div class="bcpc-monitoring-empty-state text-center py-10 text-gray-500">
                    <div class="w-14 h-14 bg-purple-50 text-purple-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <p class="text-gray-700 font-semibold text-sm">No BCPC case monitoring records found.</p>
                </div>
            @endforelse
        </div>

        @if($monitorings->hasPages())
            <div id="bcpc-monitoring-pagination">
                @if(auth()->user()?->isDepartmentAdmin())
                <div class="monitoring-tablet-pagination hidden">
                    @include('components.archived-pagination', ['paginator' => $monitorings->appends(request()->query()), 'label' => 'records', 'pageLinkLimit' => 4])
                </div>
                @endif
            <div class="monitoring-legacy-pagination px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
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
        <form id="add-monitoring-form" method="GET" action="{{ route('admin.bcpc-case-monitoring.edit') }}" class="min-h-0 overflow-y-auto p-4 sm:p-6">
            <input type="hidden" name="add" value="1">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">Barangay
                        <select id="add-monitoring-barangay" name="barangay_id" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select Barangay</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                    <select id="add-monitoring-quarter" name="quarter" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Quarter</option>
                        @foreach([1, 2, 3, 4] as $quarterOption)
                            <option value="{{ $quarterOption }}">Q{{ $quarterOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select id="add-monitoring-year" name="year" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
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

<!-- Generate Report Modal -->
<div id="generateReportModal" class="fixed inset-0 bg-black/50 hidden flex items-start sm:items-center justify-center overflow-y-auto p-4 z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-hidden relative flex flex-col">
        <div class="bg-[#C41E3A] px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="fas fa-file-alt"></i>
                <span>Generate Monitoring Report</span>
            </h3>
            <button type="button" onclick="resetGenerateMonitoringReportForm(); document.getElementById('generateReportModal').classList.add('hidden')" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="generate-monitoring-report-form" method="GET" action="{{ route('admin.bcpc-case-monitoring.edit') }}" class="min-h-0 overflow-y-auto p-4 sm:p-6">
            <input type="hidden" name="generate" value="1">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700">Barangay
                        <select id="generate-monitoring-barangay" name="barangay_id" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select Barangay</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quarter</label>
                    <select id="generate-monitoring-quarter" name="quarter" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Quarter</option>
                        @foreach([1, 2, 3, 4] as $quarterOption)
                            <option value="{{ $quarterOption }}">Q{{ $quarterOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select id="generate-monitoring-year" name="year" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Year</option>
                        @for($yearOption = now()->year; $yearOption <= now()->year + 2; $yearOption++)
                            <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="resetGenerateMonitoringReportForm(); document.getElementById('generateReportModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-[#C41E3A] hover:bg-[#a71830] text-white rounded-lg font-medium transition">Generate</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
@if(auth()->user()?->isDepartmentAdmin())
<script>
(() => {
    let pending = null;
    const cancelRefresh = () => pending?.abort();
    const dialogOpen = () => ['addMonitoringModal', 'generateReportModal'].some(id => {
        const modal = document.getElementById(id);
        return modal && !modal.classList.contains('hidden');
    })
        || document.querySelector('.swal2-container');
    const refreshMonitoring = async () => {
        const current = document.getElementById('bcpc-monitoring-results');
        if (document.hidden || pending || !current || current.hasAttribute('aria-busy') || dialogOpen()) return;
        const url = location.href;
        const request = new AbortController();
        pending = request;
        const timeout = setTimeout(() => request.abort(), 10000);
        try {
            const response = await fetch(url, {
                cache: 'no-store', signal: request.signal,
                headers: { 'Accept': 'text/html', 'X-Skip-Loading': '1' },
            });
            if (!response.ok || response.redirected) return;
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const next = page.getElementById('bcpc-monitoring-results');
            if (request.signal.aborted || location.href !== url || dialogOpen()
                || document.getElementById('bcpc-monitoring-results') !== current
                || current.hasAttribute('aria-busy') || !next) return;
            if (current.innerHTML !== next.innerHTML) current.innerHTML = next.innerHTML;
        } catch (_) {
            // Keep the displayed monitoring records if a background refresh is unavailable.
        } finally {
            clearTimeout(timeout);
            if (pending === request) pending = null;
        }
    };
    document.addEventListener('input', cancelRefresh, true);
    document.addEventListener('change', cancelRefresh, true);
    document.addEventListener('submit', cancelRefresh, true);
    document.addEventListener('click', event => {
        if (event.target.closest('#bcpc-monitoring-results a[href], #bcpc-monitoring-results button, #bcpc-monitoring-filter-form button')) cancelRefresh();
    }, true);
    window.addEventListener('popstate', cancelRefresh);
    window.addEventListener('focus', refreshMonitoring);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) cancelRefresh();
        else refreshMonitoring();
    });
    window.setInterval(refreshMonitoring, 3000);
})();

document.addEventListener('click', function (event) {
    const link = event.target.closest('a[data-monitoring-print]');
    if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    Swal.fire({
        title: 'Open Print Preview?',
        text: 'The report will open in a new tab for printing.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Open Print Preview',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#C41E3A',
        preConfirm: () => { window.location.assign(link.href); },
    });
});
</script>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tablet = matchMedia('(max-width: 85.375rem)');
    let activeRequest;
    document.addEventListener('click', async event => {
        const link = event.target.closest('#bcpc-monitoring-pagination a[href]');
        if (!tablet.matches || !link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.classList.contains('pointer-events-none')) return;
        event.preventDefault();
        activeRequest?.abort();
        const controller = new AbortController();
        activeRequest = controller;
        const results = document.getElementById('bcpc-monitoring-results');
        results.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(link.href, { signal: controller.signal, headers: { Accept: 'text/html' } });
            if (!response.ok || response.redirected) throw new Error('Unable to load monitoring records');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (controller.signal.aborted) return;
            const next = page.getElementById('bcpc-monitoring-results');
            if (!next) throw new Error('Missing monitoring records');
            const top = results.getBoundingClientRect().top;
            results.replaceWith(next);
            history.replaceState(null, '', link.href);
            window.scrollBy({ top: next.getBoundingClientRect().top - top, behavior: 'instant' });
        } catch (error) {
            if (error.name !== 'AbortError') location.assign(link.href);
        } finally {
            results.removeAttribute('aria-busy');
        }
    });
});
</script>
@endif
@if(auth()->user()->isBcpcAdmin() && session('saved_monitoring_id'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success', title: 'Monitoring Saved',
        text: 'Would you like to view the report or add another report?',
        showDenyButton: true, confirmButtonText: 'View Report', denyButtonText: 'Add More Report',
        confirmButtonColor: '#7e22ce', denyButtonColor: '#6b7280', allowOutsideClick: false
    }).then(result => {
        if (result.isConfirmed) window.location.assign(@json(route('admin.bcpc-case-monitoring.edit', ['view' => session('saved_monitoring_id')])));
        else if (result.isDenied) document.getElementById('addMonitoringModal').classList.remove('hidden');
    });
});
</script>
@endif
<div id="bcpc-monitoring-flash"
     data-success="{{ session('success') }}"
     data-error="{{ session('error') }}"
     data-validation-errors='@json($errors->all())'
     hidden></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
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

function archiveMonitoring(id, barangayName, period) {
    Swal.fire({
        title: 'Archive Case Monitoring?',
        customClass: { popup: 'bcpc-monitoring-archive-confirm' },
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

            fetch(`/admin/bcpc-case-monitoring/${id}/archive`, {
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
                        customClass: { popup: 'bcpc-monitoring-archive-success' },
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

document.querySelectorAll('[data-barangay-picker]').forEach((select) => {
    const picker = document.createElement('div');
    picker.className = 'relative mb-4';

    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-left text-gray-700 flex items-center justify-between';

    const label = document.createElement('span');
    label.dataset.barangayPickerLabel = '';
    label.textContent = select.selectedOptions[0]?.textContent.trim() || 'Select Barangay';
    const icon = document.createElement('i');
    icon.className = 'fas fa-chevron-down text-xs text-gray-500';
    button.append(label, icon);

    const menu = document.createElement('div');
    const isGenerateReportPicker = select.id === 'generate-monitoring-barangay';
    const menuPosition = isGenerateReportPicker ? 'relative' : 'absolute';
    menu.className = `${menuPosition} z-[60] ${isGenerateReportPicker ? '' : 'hidden'} mt-1 w-full max-h-52 overflow-y-auto rounded-lg border border-gray-300 bg-white shadow-lg`;
    if (isGenerateReportPicker) {
        menu.dataset.explicitVisibility = 'true';
        menu.style.display = 'none';
    }

    [...select.options].forEach((option) => {
        if (isGenerateReportPicker && !option.value) return;
        const item = document.createElement('button');
        item.type = 'button';
        item.className = `block w-full px-3 py-2 text-left text-gray-700 ${isGenerateReportPicker ? 'hover:bg-gray-100' : 'hover:bg-purple-50'}`;
        item.dataset.barangayPickerOption = option.value;
        item.textContent = option.textContent.trim();
        item.addEventListener('click', () => {
            if (option.disabled) return;
            select.value = option.value;
            label.textContent = option.textContent.trim();
            if (isGenerateReportPicker) menu.style.display = 'none';
            else menu.classList.add('hidden');
            select.dispatchEvent(new Event('change', { bubbles: true }));
        });
        menu.appendChild(item);
    });

    button.addEventListener('click', () => {
        const isOpen = isGenerateReportPicker ? menu.style.display !== 'none' : !menu.classList.contains('hidden');
        document.querySelectorAll('[data-barangay-picker-menu]').forEach((openMenu) => {
            if (openMenu.dataset.explicitVisibility === 'true') openMenu.style.display = 'none';
            else openMenu.classList.add('hidden');
        });
        if (!isOpen) {
            if (isGenerateReportPicker) menu.style.display = 'block';
            else menu.classList.remove('hidden');
        }
    });

    menu.dataset.barangayPickerMenu = '';
    picker.append(button, menu);
    select.insertAdjacentElement('afterend', picker);
});

document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-barangay-picker] + .relative')) {
        document.querySelectorAll('[data-barangay-picker-menu]').forEach((menu) => {
            if (menu.dataset.explicitVisibility === 'true') menu.style.display = 'none';
            else menu.classList.add('hidden');
        });
    }
});

const existingMonitoringPeriods = @json($existingMonitoringPeriods ?? []);
const addMonitoringBarangay = document.getElementById('add-monitoring-barangay');
const addMonitoringQuarter = document.getElementById('add-monitoring-quarter');
const addMonitoringYear = document.getElementById('add-monitoring-year');

function updateAddMonitoringAvailability() {
    if (!addMonitoringBarangay || !addMonitoringQuarter || !addMonitoringYear) return;

    const matchesExisting = (barangayId, quarter, year) => existingMonitoringPeriods.some(period =>
        period.barangay_id === String(barangayId)
        && period.quarter === String(quarter)
        && period.year === String(year)
    );
    const barangayId = addMonitoringBarangay.value;
    const quarter = addMonitoringQuarter.value;
    const year = addMonitoringYear.value;

    [...addMonitoringBarangay.options].forEach(option => {
        option.disabled = Boolean(option.value && quarter && year && matchesExisting(option.value, quarter, year));
    });
    [...addMonitoringQuarter.options].forEach(option => {
        option.disabled = Boolean(option.value && barangayId && year && matchesExisting(barangayId, option.value, year));
    });
    [...addMonitoringYear.options].forEach(option => {
        option.disabled = Boolean(option.value && barangayId && quarter && matchesExisting(barangayId, quarter, option.value));
    });

    [addMonitoringBarangay, addMonitoringQuarter, addMonitoringYear].forEach(select => {
        if (select.selectedOptions[0]?.disabled) select.value = '';
    });

    addMonitoringBarangay.parentElement?.querySelectorAll('[data-barangay-picker-option]').forEach(item => {
        const option = addMonitoringBarangay.querySelector(`option[value="${CSS.escape(item.dataset.barangayPickerOption)}"]`);
        const disabled = Boolean(option?.disabled);
        item.disabled = disabled;
        item.classList.toggle('opacity-50', disabled);
        item.classList.toggle('cursor-not-allowed', disabled);
        item.classList.toggle('hover:bg-purple-50', !disabled);
    });
}

function resetAddMonitoringForm() {
    const form = document.getElementById('add-monitoring-form');
    if (!form) return;

    form.reset();
    addMonitoringBarangay?.dispatchEvent(new Event('change', { bubbles: true }));
    addMonitoringQuarter?.dispatchEvent(new Event('change', { bubbles: true }));
    addMonitoringYear?.dispatchEvent(new Event('change', { bubbles: true }));

    const barangayLabel = addMonitoringBarangay?.parentElement?.querySelector('[data-barangay-picker-label]');
    if (barangayLabel) barangayLabel.textContent = addMonitoringBarangay.options[0]?.textContent.trim() || 'Select Barangay';
}

function resetGenerateMonitoringReportForm() {
    const form = document.getElementById('generate-monitoring-report-form');
    const barangay = document.getElementById('generate-monitoring-barangay');
    const quarter = document.getElementById('generate-monitoring-quarter');
    const year = document.getElementById('generate-monitoring-year');
    if (!form) return;

    form.reset();
    const barangayLabel = barangay?.parentElement?.querySelector('[data-barangay-picker-label]');
    if (barangayLabel) barangayLabel.textContent = barangay.options[0]?.textContent.trim() || 'Select Barangay';
    [barangay, quarter, year].forEach(select => select?.dispatchEvent(new Event('change', { bubbles: true })));
}

[addMonitoringBarangay, addMonitoringQuarter, addMonitoringYear].forEach(select => {
    select?.addEventListener('change', updateAddMonitoringAvailability);
});
updateAddMonitoringAvailability();
</script>
@endpush
@endsection

@include('admin-tailwind.components.profile-pagination', ['paginationRegions' => ['#bcpc-monitoring-results'], 'paginationLinks' => '#bcpc-monitoring-pagination a[href]'])
