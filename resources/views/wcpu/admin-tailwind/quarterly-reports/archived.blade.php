@extends('layouts.admin-tailwind')
@section('title', 'Archived Quarterly Reports - iSERVE SILANG Admin')
@push('styles')
<style>
    @if(auth()->user()?->isDepartmentAdmin() && request('section') === 'quarterly')
    @media (max-width: 47.9375rem) {
        #archived-quarterly-results .quarterly-archive-existing-pagination { display: none; }
        #archived-quarterly-results .quarterly-archive-phone-pagination { display: block !important; }
        #archived-quarterly-results .quarterly-archive-phone-pagination > [data-pagination-label] {
            flex-direction: column; justify-content: center; gap: .5rem; padding: .75rem; text-align: center;
        }
        #archived-quarterly-results .quarterly-archive-phone-pagination [data-pagination-label] > div:last-child {
            justify-content: center; gap: clamp(.25rem, 2vw, .625rem); max-width: 100%;
        }
        #archived-quarterly-results .quarterly-archive-phone-pagination a {
            flex: 0 0 auto; width: clamp(2rem, 10vw, 2.75rem) !important; min-width: 0 !important;
            height: 2.75rem !important; padding: 0 !important; margin: 0 !important;
            border: 1px solid #e5e7eb !important; border-radius: .5rem;
            background: #fff !important; color: #C41E3A !important;
            box-shadow: 0 1px 2px rgb(0 0 0 / 4%);
        }
        #archived-quarterly-results .quarterly-archive-phone-pagination a[class~="bg-[#C41E3A]"] {
            background: #C41E3A !important; border-color: #C41E3A !important; color: #fff !important;
        }
        #archived-quarterly-results .quarterly-archive-phone-pagination a > i { color: inherit !important; }
    }
    @endif
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui:is(.bcpc-admin-header-theme, .swd-admin-header-theme) .archived-quarterly-mobile-cards > .archive-empty-state {
            grid-column: 1 / -1;
            width: 100%;
            text-align: center;
        }
    }

    @if(auth()->user()?->isDepartmentAdmin() && request('section') === 'quarterly')
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: landscape) {
        html body.portal-ui.admin-quarterly-reports-archived-mobile-nav > aside#sidebar {
            position: fixed !important;
            inset: 0 auto 0 0 !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            margin-block: 0 !important;
            box-sizing: border-box;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
        }
        html body.portal-ui.admin-quarterly-reports-archived-mobile-nav > aside#sidebar > nav.sidebar-menu {
            flex: 1 1 0 !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overscroll-behavior-y: contain;
        }
        html body.portal-ui.admin-quarterly-reports-archived-mobile-nav > aside#sidebar > div.sidebar-menu {
            flex: 0 0 auto !important;
            height: auto !important;
            overflow: visible !important;
            margin-top: auto;
            padding-bottom: max(1rem, env(safe-area-inset-bottom)) !important;
        }
        html body.portal-ui.admin-quarterly-reports-archived-mobile-nav > aside#sidebar > .sidebar-header { flex-shrink: 0; }
    }
    @endif
    @if(auth()->user()?->isVawcAdmin() && request('section') === 'quarterly')
    @media (max-width: 47.9375rem) {
        html body.portal-ui #swdArchiveViewDialog :is(.abuse-type-table-view, .case-status-table-view, .case-records-table-view) {
            display: none !important;
        }
        html body.portal-ui #swdArchiveViewDialog :is(#abuse-type-mobile-cards, #case-status-mobile-cards, #case-records-mobile-cards) {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr);
            gap: .75rem;
            padding: .75rem !important;
        }
        html body.portal-ui #swdArchiveViewDialog :is(#abuse-type-mobile-cards, #case-status-mobile-cards, #case-records-mobile-cards) > article {
            min-width: 0;
            margin: 0 !important;
            padding: 1rem !important;
            background: #fff;
            border: 1px solid #dbe3e8;
            border-radius: .75rem;
            overflow-wrap: anywhere;
        }
        html body.portal-ui #swdArchiveViewDialog :is(#abuse-type-classification, #case-status-breakdown, #case-records) > div:first-child :is(h2, h3) {
            font-size: .9375rem !important;
            line-height: 1.35 !important;
        }
        html body.portal-ui #swdArchiveViewDialog :is(#abuse-type-classification, #case-status-breakdown, #case-records) > div:first-child p {
            font-size: .75rem !important;
            line-height: 1.4 !important;
        }
        html body.portal-ui #swdArchiveViewDialog:has([data-archive-report-title]) .swd-archive-dialog-header {
            padding: .875rem 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #swdArchiveViewDialog:has([data-archive-report-title]) #swdArchiveViewTitle,
        html body.portal-ui.swd-admin-header-theme .admin-archive-view-popup--report > .swal2-title {
            padding: 0 !important;
            margin: 0 !important;
            text-align: left !important;
        }
        html body.portal-ui #swdArchiveViewDialog:has([data-archive-report-title]) :is(.archive-case-heading, .archive-case-heading-summary) {
            text-align: left !important;
            margin-left: 0 !important;
        }
        html body.portal-ui #swdArchiveViewDialog [data-archive-report-title] > .bg-white > .grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: .75rem !important;
        }
        html body.portal-ui #swdArchiveViewDialog [data-archive-report-title] > .bg-white > .grid > div {
            grid-column: auto !important;
            width: auto !important;
            min-width: 0 !important;
            padding: .75rem !important;
            overflow-wrap: anywhere;
        }
    }
    @endif
    .archived-quarterly-reports-stable,
    .archived-quarterly-reports-stable :is(table, button, a),
    #archived-quarterly-reports-header {
        animation: none !important;
        transition: none !important;
    }
    html body.portal-ui.swd-admin-header-theme #archived-quarterly-report-filter-form > div {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: .5rem;
    }
    @media (min-width: 40rem) {
        html body.portal-ui.swd-admin-header-theme #archived-quarterly-report-filter-form > div {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }
        html body.portal-ui.swd-admin-header-theme #archived-quarterly-report-filter-form > div > .relative {
            grid-column: span 2;
        }
    }
    @media (min-width: 80rem) {
        html body.portal-ui.swd-admin-header-theme #archived-quarterly-report-filter-form > div {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            align-items: center;
        }
        html body.portal-ui.swd-admin-header-theme #archived-quarterly-report-filter-form > div > .relative {
            grid-column: 1 !important;
        }
        html body.portal-ui.swd-admin-header-theme #archived-quarterly-report-filter-form > div > .flex,
        html body.portal-ui.swd-admin-header-theme #archived-quarterly-report-filter-form > div > button:last-child {
            grid-column: 5 !important;
        }
    }
    html body.portal-ui.swd-admin-header-theme #archived-quarterly-report-filter-form :is(input, select, button) {
        box-sizing: border-box;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        height: 2.5rem;
    }
    @media (min-width: 48rem) and (max-width: 80rem) {
        #archived-quarterly-report-filter-form > div {
            display: grid !important;
            grid-template-columns: minmax(120px, 1.6fr) repeat(3, minmax(90px, 1.15fr)) minmax(72px, .9fr) !important;
            align-items: center !important;
            gap: .5rem !important;
        }
        #archived-quarterly-report-filter-form > div:has(> .flex a) {
            grid-template-columns: minmax(120px, 1.6fr) repeat(3, minmax(90px, 1.15fr)) repeat(2, minmax(72px, .9fr)) !important;
        }
        #archived-quarterly-report-filter-form > div > .relative,
        #archived-quarterly-report-filter-form > div > select,
        #archived-quarterly-report-filter-form > div > .flex {
            grid-column: auto !important;
        }
        #archived-quarterly-report-filter-form > div > .flex { display: contents !important; }
        #archived-quarterly-report-filter-form :is(input, select, button, a) {
            box-sizing: border-box;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            height: 2.5rem !important;
        }
    }
    @if(auth()->user()->isDepartmentAdmin() && request('section') === 'quarterly')
    @media (max-width: 47.9375rem), (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        html body.portal-ui .archived-quarterly-mobile-cards dl > div:nth-child(2) dd {
            background-color: #ffe4e6 !important;
            color: #C41E3A !important;
        }
    }
    @endif
    @media (max-width: 47.9375rem)@if(auth()->user()->isDepartmentAdmin() && request('section') === 'quarterly'), (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)@endif {
        .archived-quarterly-table-view { display: none !important; }
        .archived-quarterly-mobile-cards { display: block !important; }
        .archived-quarterly-mobile-cards dl > div:nth-child(2) dd {
            display: inline-flex;
            box-sizing: border-box;
            max-width: 100%;
            margin-top: .375rem;
            padding: .25rem .625rem;
            border-radius: 9999px;
            background: #f3e8ff;
            color: #7e22ce;
        }
        .archived-quarterly-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        .archived-quarterly-card-actions > form { display: contents; }
        .archived-quarterly-card-actions > :is(a, button),
        .archived-quarterly-card-actions > form > button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            box-sizing: border-box;
            max-width: 100% !important;
            min-width: 0 !important;
            min-height: 3rem;
            padding: .5rem !important;
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
        }
        .archived-quarterly-card-actions > :is(a, button):not(:disabled):hover,
        .archived-quarterly-card-actions > form > button:not(:disabled):hover {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
        }
        .archived-quarterly-card-actions i { color: #ffffff !important; }
        .archived-quarterly-card-actions [data-restore-report-button] {
            font-size: 0 !important;
        }
        .archived-quarterly-card-actions [data-restore-report-button] i {
            font-size: .875rem !important;
        }
    }
</style>
@if(auth()->user()->isDepartmentAdmin())
<style>
    @media (max-width: 40rem) {
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
            box-sizing: border-box !important;
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

    /* Archived Quarterly Reports tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
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

    @media (min-width: 48rem) and (max-width: 63.9375rem) and (orientation: portrait) {
        html body.portal-ui.admin-quarterly-reports-archived-mobile-nav #sidebar {
            position: fixed !important;
            inset: 0 auto 0 0 !important;
            top: 0 !important;
            right: auto !important;
            bottom: 0 !important;
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 100vh !important;
            min-height: 100dvh !important;
            max-height: none !important;
            margin: 0 !important;
            transform: none !important;
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
</style>
@endif
@endpush
@section('content')
<style>
    [data-restore-report-button]:disabled { background-color: #FEE2E2 !important; border-color: #FCA5A5 !important; color: #991B1B !important; opacity: 1 !important; filter: none !important; }
    [data-restore-report-button]:not(:disabled) { background-color: #C41E3A !important; }
    [data-restore-report-button]:not(:disabled):hover { background-color: #A01830 !important; }
</style>
<header id="archived-quarterly-reports-header" class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Archived Quarterly Reports</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">View archived quarterly reports</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
<div class="archived-quarterly-reports-stable w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    @if(session('success'))<p class="p-4 mb-4 bg-green-50 text-green-700 rounded-lg border border-green-200">{{ session('success') }}</p>@endif
    @if(session('error'))<p class="p-4 mb-4 bg-red-50 text-red-700 rounded-lg border border-red-200">{{ session('error') }}</p>@endif

    <form id="archived-quarterly-report-filter-form" method="GET" action="{{ route('admin.quarterly-reports.archived') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <input type="hidden" name="section" value="quarterly">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 xl:items-start gap-2 sm:gap-3">
            <div class="relative min-w-0 sm:col-span-2 xl:col-span-1"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i><input type="search" name="search" id="archived-report-search" value="{{ request('search') }}" placeholder="Search archived reports..." class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></div>
            <select name="quarter" id="archived-report-quarter" class="w-full h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Quarters</option>@foreach([1,2,3,4] as $quarter)<option value="{{ $quarter }}" {{ (string) request('quarter') === (string) $quarter ? 'selected' : '' }}>Q{{ $quarter }}</option>@endforeach</select>
            <select name="year" id="archived-report-year" class="w-full h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Years</option>@foreach(range(2026, 2030) as $year)<option value="{{ $year }}" {{ (string) request('year') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>@endforeach</select>
            <select name="barangay_id" id="archived-report-barangay" class="w-full h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Barangays</option>@foreach($barangays as $barangay)<option value="{{ $barangay->id }}" {{ (string) request('barangay_id') === (string) $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>@endforeach</select>
            <div class="flex items-center gap-2 sm:col-span-2 xl:col-span-1 xl:flex-col xl:items-stretch"><button type="submit" class="inline-flex w-full sm:flex-1 xl:flex-none h-10 items-center justify-center gap-2 px-3 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0"><i class="fas fa-filter"></i><span>Filter</span></button>
            @if(request()->filled('search') || request()->filled('quarter') || request()->filled('year') || request()->filled('barangay_id'))<a href="{{ route('admin.quarterly-reports.archived', ['section' => 'quarterly']) }}" class="inline-flex w-full sm:flex-1 xl:flex-none h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition shrink-0"><i class="fas fa-times"></i><span>Clear</span></a>@endif</div>
        </div>
    </form>

    <section id="archived-quarterly-results" class="desk-officer-quarterly-report-table bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="archived-quarterly-table-view hidden md:block overflow-x-auto">
            <table class="w-full table-fixed">
                <colgroup><col class="w-[15%]"><col class="w-[12%]"><col class="w-[10%]"><col class="w-[9%]"><col class="w-[16%]"><col class="w-[19%]"><col class="w-[19%]"></colgroup>
                <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white"><tr><th class="px-6 py-4 text-left text-sm font-semibold">Barangay</th><th class="px-6 py-4 text-center text-sm font-semibold">Period</th><th class="px-6 py-4 text-center text-sm font-semibold">Type</th><th class="px-6 py-4 text-center text-sm font-semibold">Cases</th><th class="px-6 py-4 text-left text-sm font-semibold">Archived Date</th><th class="px-6 py-4 text-left text-sm font-semibold">Submitted By</th><th class="px-6 py-4 text-center text-sm font-semibold">Actions</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($reports as $report)
                    @php($restoreBlocked = !empty(($activeReportPeriods ?? [])[$report->barangay_id . '-' . $report->quarter . '-' . $report->year]))
                    <tr class="hover:bg-purple-50/50 transition" data-archived-report data-quarter="{{ $report->quarter }}" data-year="{{ $report->year }}">
                        <td class="px-6 py-4 font-medium text-gray-800 break-words">{{ $report->barangay?->name ?? 'N/A' }}</td><td class="px-6 py-4 text-center whitespace-nowrap">Q{{ $report->quarter }} {{ $report->year }}</td><td class="px-6 py-4 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">{{ str_replace('VAWC', 'SWD', $report->type ?? 'BCPC') }}</span></td><td class="px-6 py-4 text-center font-medium">{{ $report->report_data['total_cases'] ?? 0 }}</td><td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $report->archived_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td><td class="px-6 py-4 text-sm text-gray-700 break-words">{{ trim(($report->user?->first_name ?? '') . ' ' . ($report->user?->last_name ?? '')) ?: ($report->user?->name ?? 'N/A') }}</td>
                        <td class="px-6 py-4 text-center align-middle whitespace-nowrap"><div class="inline-flex items-center justify-center gap-2">
                            <a href="{{ route('admin.quarterly-reports.show', ['id' => $report->id, 'archived' => 1]) }}" title="View Report" data-admin-archive-view="report" data-swd-archive-view="report" data-modal-title="Quarterly Report Details" class="inline-flex w-10 h-10 items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm"><i class="fas fa-eye text-sm"></i><span class="sr-only">View Report</span></a>
                            <button type="button" data-restore-report-button {{ $restoreBlocked ? 'disabled' : '' }} @unless($restoreBlocked) onclick='openRestoreModal({{ $report->id }}, @json(($report->barangay?->name ?? 'Barangay') . ' - Q' . $report->quarter . ' ' . $report->year))' @endunless title="{{ $restoreBlocked ? 'An active report already exists for this barangay, quarter, and year' : 'Restore Report' }}" class="inline-flex w-10 h-10 items-center justify-center {{ $restoreBlocked ? '!bg-red-100 hover:!bg-red-100 !text-red-800 !border !border-red-200 cursor-not-allowed pointer-events-none' : 'bg-red-600 hover:bg-red-700 text-white' }} rounded-lg transition shadow-sm"><i class="fas fa-undo text-sm"></i><span class="sr-only">Restore</span></button>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">No archived quarterly reports found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="archived-quarterly-mobile-cards md:hidden p-3 space-y-3">
            @forelse($reports as $report)
                @php($restoreBlocked = !empty(($activeReportPeriods ?? [])[$report->barangay_id . '-' . $report->quarter . '-' . $report->year]))
                <article class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm" data-archived-report data-quarter="{{ $report->quarter }}" data-year="{{ $report->year }}">
                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-gray-100"><div><p class="text-xs font-medium text-gray-500">Period</p><p class="text-sm font-bold text-gray-800">Q{{ $report->quarter }} {{ $report->year }}</p></div><div class="text-right"><p class="text-xs font-medium text-gray-500">Cases</p><p class="text-sm font-bold text-purple-700">{{ $report->report_data['total_cases'] ?? 0 }}</p></div></div>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3 py-3 text-sm"><div><dt class="text-xs text-gray-500">Barangay</dt><dd class="mt-0.5 font-medium text-gray-800 break-words">{{ $report->barangay?->name ?? 'N/A' }}</dd></div><div><dt class="text-xs text-gray-500">Type</dt><dd class="mt-0.5 font-medium text-gray-800">{{ str_replace('VAWC', 'SWD', $report->type ?? 'BCPC') }}</dd></div><div><dt class="text-xs text-gray-500">Archived Date</dt><dd class="mt-0.5 font-medium text-gray-800">{{ $report->archived_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</dd></div><div><dt class="text-xs text-gray-500">Submitted By</dt><dd class="mt-0.5 font-medium text-gray-800 break-words">{{ trim(($report->user?->first_name ?? '') . ' ' . ($report->user?->last_name ?? '')) ?: ($report->user?->name ?? 'N/A') }}</dd></div></dl>
                    <div class="archived-quarterly-card-actions flex items-center justify-center gap-2 pt-3 border-t border-gray-100"><a href="{{ route('admin.quarterly-reports.show', ['id' => $report->id, 'archived' => 1]) }}" title="View Report" data-admin-archive-view="report" data-swd-archive-view="report" data-modal-title="Quarterly Report Details" class="inline-flex w-10 h-10 items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm"><i class="fas fa-eye text-sm"></i><span class="sr-only">View Report</span></a><button type="button" data-restore-report-button {{ $restoreBlocked ? 'disabled' : '' }} @unless($restoreBlocked) onclick='openRestoreModal({{ $report->id }}, @json(($report->barangay?->name ?? 'Barangay') . ' - Q' . $report->quarter . ' ' . $report->year))' @endunless title="{{ $restoreBlocked ? 'An active report already exists for this barangay, quarter, and year' : 'Restore Report' }}" class="inline-flex w-10 h-10 items-center justify-center {{ $restoreBlocked ? '!bg-red-100 hover:!bg-red-100 !text-red-800 !border !border-red-200 cursor-not-allowed pointer-events-none' : 'bg-red-600 hover:bg-red-700 text-white' }} rounded-lg transition shadow-sm"><i class="fas fa-undo text-sm"></i></button></div>
                </article>
            @empty
                <div class="archive-empty-state text-center py-10 text-gray-500">No archived quarterly reports found.</div>
            @endforelse
        </div>
        @if(auth()->user()?->isDepartmentAdmin() && request('section') === 'quarterly')
        <div class="quarterly-archive-phone-pagination hidden">
            @include('components.archived-pagination', ['paginator' => $reports, 'label' => 'reports', 'pageLinkLimit' => 4])
        </div>
        @endif
        <div class="quarterly-archive-existing-pagination">
            @include('components.archived-pagination', ['paginator' => $reports, 'label' => 'reports'])
        </div>
    </section>
</div>

<div id="restoreModal" class="fixed inset-0 z-50 hidden"><div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRestoreModal()"></div><div class="flex items-center justify-center min-h-screen p-4"><div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6"><div class="flex justify-center mb-4"><div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-undo text-red-600 text-xl"></i></div></div><h3 class="text-lg font-bold text-gray-800 text-center mb-2">Restore Report</h3><p class="text-gray-500 text-sm text-center mb-6">Are you sure you want to restore <strong id="restoreReportName" class="text-gray-700"></strong> back to active quarterly reports?</p><div class="flex gap-3"><button type="button" onclick="closeRestoreModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition text-sm">Cancel</button><form id="restoreForm" method="POST" class="flex-1">@csrf<button type="submit" data-restore-report-button class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition text-sm"><i class="fas fa-undo mr-1"></i> Restore</button></form></div></div></div></div>
@endsection

@push('scripts')
@if(auth()->user()?->isDepartmentAdmin())
<script>
document.addEventListener('DOMContentLoaded', () => {
    let activeRequest;
    document.addEventListener('click', async event => {
        const link = event.target.closest('#archived-quarterly-results [data-pagination-label] a[href]');
        if (!matchMedia('(max-width: 85.375rem)').matches || !link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.classList.contains('pointer-events-none')) return;
        event.preventDefault();
        activeRequest?.abort();
        const controller = new AbortController();
        activeRequest = controller;
        const results = document.getElementById('archived-quarterly-results');
        results.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(link.href, { signal: controller.signal, headers: { Accept: 'text/html' } });
            if (!response.ok || response.redirected) throw new Error('Unable to load archived reports');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (controller.signal.aborted) return;
            const next = page.getElementById('archived-quarterly-results');
            if (!next) throw new Error('Missing archived reports');
            const top = results.getBoundingClientRect().top;
            results.replaceWith(next);
            history.replaceState(null, '', link.href);
            filterArchivedReports();
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
@include('admin-tailwind.components.archive-view-modal')
@if(session('restored_report'))
<script>
document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({ icon: 'success', title: 'Restored!', text: @json(session('restored_report')), confirmButtonText: 'OK' });
});
</script>
@endif
<script>
function openRestoreModal(id, name) { document.getElementById('restoreReportName').textContent = name; document.getElementById('restoreForm').action = "{{ url('admin/quarterly-reports') }}/" + id + "/restore"; document.getElementById('restoreModal').classList.remove('hidden'); }
function closeRestoreModal() { document.getElementById('restoreModal').classList.add('hidden'); }
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeRestoreModal(); });
const archivedSearch = document.getElementById('archived-report-search'), archivedQuarter = document.getElementById('archived-report-quarter'), archivedYear = document.getElementById('archived-report-year'), archivedBarangay = document.getElementById('archived-report-barangay'), archivedFilterForm = document.getElementById('archived-quarterly-report-filter-form');
function filterArchivedReports() { const search = (archivedSearch?.value || '').toLowerCase().trim(), quarter = archivedQuarter?.value || '', year = archivedYear?.value || ''; document.querySelectorAll('[data-archived-report]').forEach(item => item.classList.toggle('hidden', !((!search || item.textContent.toLowerCase().includes(search)) && (!quarter || item.dataset.quarter === quarter) && (!year || item.dataset.year === year)))); }
archivedSearch?.addEventListener('input', filterArchivedReports); archivedQuarter?.addEventListener('change', filterArchivedReports); archivedYear?.addEventListener('change', filterArchivedReports);
archivedBarangay?.addEventListener('change', () => archivedFilterForm?.requestSubmit());
archivedYear?.addEventListener('change', () => archivedFilterForm?.requestSubmit());
</script>
@endpush
