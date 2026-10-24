@extends('layouts.admin-tailwind')

@section('title', 'Quarterly Reports')

@section('content')
@if(auth()->user()->isDepartmentAdmin())
<header class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Quarterly Reports</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">View all submitted barangay compliance monitoring reports.</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
<style>
    @if(auth()->user()?->isDepartmentAdmin())
    @media (max-width: 47.9375rem) {
        #quarterly-mobile-cards .quarterly-existing-pagination { display: none; }
        #quarterly-mobile-cards .quarterly-phone-pagination { display: block !important; }
        #quarterly-mobile-cards .quarterly-phone-pagination > [data-pagination-label] {
            flex-direction: column; justify-content: center; gap: .5rem; padding: .75rem; text-align: center;
        }
        #quarterly-mobile-cards .quarterly-phone-pagination [data-pagination-label] > div:last-child {
            justify-content: center; gap: clamp(.25rem, 2vw, .625rem); max-width: 100%;
        }
        #quarterly-mobile-cards .quarterly-phone-pagination a {
            flex: 0 0 auto; width: clamp(2rem, 10vw, 2.75rem) !important; min-width: 0 !important;
            height: 2.75rem !important; padding: 0 !important; margin: 0 !important;
            border: 1px solid #e5e7eb !important; border-radius: .5rem;
            background: #fff !important; color: #C41E3A !important;
            box-shadow: 0 1px 2px rgb(0 0 0 / 4%);
        }
        #quarterly-mobile-cards .quarterly-phone-pagination a[class~="bg-[#C41E3A]"] {
            background: #C41E3A !important; border-color: #C41E3A !important; color: #fff !important;
        }
        #quarterly-mobile-cards .quarterly-phone-pagination a > i { color: inherit !important; }
    }
    @media (max-width: 85.375rem) {
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-edit {
            align-items: center !important;
            justify-content: center !important;
            gap: 0 !important;
        }
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-edit > span { display: none !important; }
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-edit > i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1rem;
            height: 1rem;
            margin: 0 !important;
            line-height: 1 !important;
            transform: none !important;
            animation: none !important;
            transition: none !important;
        }
    }
    @endif
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        html body.portal-ui.admin-quarterly-reports-mobile-nav #mainContent > header.admin-reference-mobile-header > div {
            padding: 1rem !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 1rem !important;
        }
        html body.portal-ui.admin-quarterly-reports-mobile-nav #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-leading {
            flex: 1 1 0 !important;
            min-width: 0 !important;
        }
        html body.portal-ui.admin-quarterly-reports-mobile-nav #mainContent > header.admin-reference-mobile-header > div > div:last-child {
            margin-left: auto !important;
            flex: 0 0 auto !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 1rem !important;
        }
        html body.portal-ui.admin-quarterly-reports-mobile-nav #mainContent > header.admin-reference-mobile-header div:has(> img[alt="User Profile"]) {
            display: flex !important;
            align-items: center !important;
            gap: .75rem !important;
        }
        html body.portal-ui.admin-quarterly-reports-mobile-nav #mainContent > header.admin-reference-mobile-header .text-right > p:first-child { color: #1f2937 !important; }
        html body.portal-ui.admin-quarterly-reports-mobile-nav #mainContent > header.admin-reference-mobile-header .text-right > p:last-child { color: #bc0202 !important; }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        html body.portal-ui.admin-quarterly-reports-mobile-nav #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-subtitle {
            font-size: .75rem !important;
        }
    }
    @if(auth()->user()?->isDepartmentAdmin())
    @media (max-width: 47.9375rem), (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-edit {
            align-items: center !important;
            justify-content: center !important;
            gap: 0 !important;
        }
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-edit > i {
            margin: 0 !important;
            text-align: center;
        }
    }
    @media (max-width: 85.375rem) {
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-actions > .quarterly-report-card-view { order: 1 !important; }
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-actions > .quarterly-report-card-edit { order: 2 !important; }
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-actions > .quarterly-report-card-print { order: 3 !important; }
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-actions > .quarterly-report-card-archive { order: 4 !important; }
        html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-print > span { display: none !important; }
    }
    @endif
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui.admin-quarterly-reports-mobile-nav #quarterly-mobile-cards > .quarterly-reports-empty-state {
            grid-column: 1 / -1;
            width: 100%;
            text-align: center;
        }
    }
    @if(auth()->user()->isDepartmentAdmin())
    #quarterly-reports-results a[title="Edit report"] { order: 1; }
    #quarterly-reports-results a[title="Preview / Print"] { order: 2; }
    #quarterly-reports-results button[title="Archive report"],
    #quarterly-reports-results .quarterly-report-archive-button { order: 3; }
    @endif
    @if(auth()->user()->isVawcAdmin())
    #quarterly-reports-results .swd-quarterly-print { order: 2 !important; }
    #quarterly-reports-results .swd-quarterly-archive { order: 3 !important; }
    html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-actions > .quarterly-report-card-print { order: 3 !important; }
    html body.portal-ui #quarterly-mobile-cards .quarterly-report-card-actions > .quarterly-report-card-archive { order: 4 !important; }
    @endif
    #quarterly-reports-content nav [aria-current="page"] span {
        background-color: #C41E3A !important;
        border-color: #C41E3A !important;
        color: #ffffff !important;
    }
    #quarterly-reports-results table { min-width: 62.5rem; }
    @if(auth()->user()->isBcpcAdmin())
    #quarterly-reports-results .quarterly-report-actions { min-width: 17rem; white-space: nowrap; }
    #quarterly-reports-results .quarterly-report-archive-button { min-width: 5.5rem; transition: none !important; animation: none !important; }
    @endif
    @if(auth()->user()->isDepartmentAdmin())
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        #quarterly-reports-admin-actions {
            margin-left: auto;
            justify-content: flex-end;
        }
    }
    @media (max-width: 47.9375rem) {
        #quarterly-reports-admin-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
            gap: .5rem;
        }
        #quarterly-reports-admin-actions :is(.generate-quarterly-report-button, .print-all-quarterly-reports-button) {
            display: inline-flex;
            width: 100%;
            min-width: 0;
            min-height: 2.75rem;
            padding: .75rem;
            align-items: center;
            justify-content: center;
            gap: .375rem;
            font-size: .875rem;
            line-height: 1.25;
            white-space: nowrap;
        }
    }
    @endif

    @if(auth()->user()?->isDepartmentAdmin())
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
    @endif

    /* Android tablets (600px–1280px): the shared Admin navigation is a
       drawer; filters use a grid and the report table scrolls independently. */
    @media (min-width: 37.5rem) and (max-width: 80rem) {
        #quarterly-reports-content {
            max-width: none;
            padding: clamp(1rem, 2vw, 1.5rem) !important;
        }
        #quarterly-reports-filter-form {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: clamp(.75rem, 1.5vw, 1rem);
        }
        #quarterly-reports-filter-form > div {
            width: auto !important;
            min-width: 0 !important;
        }
        #quarterly-reports-filter-form > div:first-child { grid-column: auto; }
        #quarterly-reports-filter-form > div:last-child { flex-direction: row; align-items: stretch; }
        #quarterly-reports-filter-form :is(button, a) { justify-self: stretch; }
        #quarterly-reports-results table { min-width: 56rem; }
        #quarterly-reports-results :is(th, td) {
            padding: .75rem clamp(.625rem, 1.2vw, 1rem) !important;
            font-size: clamp(.6875rem, 1.15vw, .8125rem);
        }
        #quarterly-reports-results .quarterly-report-actions {
            min-width: 0 !important;
            gap: .375rem !important;
            flex-wrap: nowrap;
        }
        #quarterly-reports-results .quarterly-report-actions :is(a, button) {
            flex: 0 0 auto;
        }
        #quarterly-reports-results .quarterly-report-archive-button {
            min-width: 0 !important;
        }
    }

    /* Report cards for phones and portrait tablets. */
    @media (max-width: 47.9375rem), (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        .quarterly-table-view { display: none !important; }
        #quarterly-mobile-cards { display: block !important; }
        #quarterly-mobile-cards .quarterly-report-card { min-width: 0; padding: 1rem; }
        #quarterly-mobile-cards .quarterly-report-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-auto-rows: minmax(3rem, auto);
            gap: .5rem;
        }
        #quarterly-mobile-cards .quarterly-report-card-actions > :is(a, button) {
            width: 100% !important;
            min-width: 0;
            min-height: 3rem;
            padding: .5rem !important;
            justify-content: center;
        }
        #quarterly-mobile-cards .quarterly-report-card-print {
            gap: 0 !important;
        }
        #quarterly-mobile-cards .quarterly-report-card-print span {
            display: none !important;
        }
    }

    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        #quarterly-mobile-cards .quarterly-report-card-actions {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
        #quarterly-mobile-cards .quarterly-report-card-actions > :is(a, button) {
            align-items: center;
            white-space: nowrap;
        }
    }

    @if(auth()->user()?->isDepartmentAdmin())
    @media (max-width: 47.9375rem) {
        #quarterly-mobile-cards .quarterly-report-card-actions {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            grid-auto-rows: 2.75rem;
            gap: .375rem;
        }
        #quarterly-mobile-cards .quarterly-report-card-actions > :is(a, button) {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            height: 2.75rem !important;
            min-height: 2.75rem !important;
            padding: .375rem .25rem !important;
            font-size: .75rem;
            white-space: nowrap;
        }
    }
    @endif
    /* Quarterly Reports tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
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
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        #quarterly-reports-filter-form {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            align-items: start;
            gap: 1rem;
        }
        #quarterly-reports-filter-form > div { grid-column: auto !important; min-width: 0; }
        #quarterly-reports-filter-form label { display: none !important; }
        #quarterly-reports-filter-form :is(input, select, button, a) {
            box-sizing: border-box;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            font-size: .875rem !important;
            border-radius: .5rem;
        }
        #quarterly-reports-filter-form > div:last-child { align-items: stretch; }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header {
            background: #fff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header > div {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 1rem !important;
            padding: 1rem 2rem !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-leading {
            flex: 1 1 auto;
            min-width: 0;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header #dashboardMenuToggle { display: none !important; }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header h2 { color: #1f2937 !important; }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header p { color: #6b7280 !important; }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-subtitle { display: block !important; }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header > div > div:last-child {
            display: flex !important;
            flex: 0 0 auto !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header div:has(> img[alt="User Profile"]) {
            display: flex !important;
        }
    }
</style>
<div id="quarterly-reports-content" class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        @unless(auth()->user()->isDepartmentAdmin())
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Quarterly Reports</h1>
            <p class="text-gray-500 text-sm mt-1">View all submitted barangay compliance monitoring reports.</p>
        </div>
        @endunless
        <div id="quarterly-reports-admin-actions" class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            @if(auth()->user()->isDepartmentAdmin())
            <button type="button" onclick="openGenerateQuarterlyReportModal()" class="generate-quarterly-report-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg font-medium text-sm">Generate Quarterly Report</button>
            @endif
            @unless(auth()->user()->isDepartmentAdmin())
            <a href="{{ route('admin.quarterly-reports.archived') }}"
               class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-800 border border-red-300 px-4 py-2.5 rounded-lg font-medium transition shadow-sm text-sm">
                <i class="fas fa-archive text-red-600"></i> Archived Reports
                @if(($archivedCount ?? 0) > 0)
                <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                    {{ $archivedCount }}
                </span>
                @endif
            </a>
            @endunless
            <a id="printAllQuarterlyReportsButton" href="{{ route('admin.quarterly-reports.print-all', request()->only(['barangay_name', 'quarter', 'year'])) }}"
               class="print-all-quarterly-reports-button inline-flex items-center gap-2 bg-purple-700 hover:bg-purple-800 text-white px-5 py-2.5 rounded-lg font-medium transition shadow-md text-sm">
                <i class="fas fa-print"></i> Print All
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6 border border-gray-100">
        <form id="quarterly-reports-filter-form" action="{{ route('admin.quarterly-reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:items-start gap-3">
            <div class="min-w-0">
                <label for="barangay-search" class="block text-sm font-medium text-gray-600 mb-1 lg:hidden">Barangay</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" aria-hidden="true"></i>
                    <input type="search" id="barangay-search" name="barangay_name" aria-label="Search Barangay" list="barangay-options" maxlength="255" placeholder="Search Barangay" autocomplete="off"
                           value="{{ old('barangay_name', request('barangay_name', $barangays->firstWhere('id', request('barangay_id'))?->name)) }}"
                           class="w-full h-10 pl-9 pr-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                           oninput="this.setCustomValidity('')">
                </div>
                <datalist id="barangay-options">
                    @foreach($barangays as $brgy)
                    <option value="{{ $brgy->name }}"></option>
                    @endforeach
                </datalist>
                @error('barangay_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="min-w-0">
                <label class="block text-sm font-medium text-gray-600 mb-1 lg:hidden">Quarter</label>
                <select name="quarter" aria-label="Quarter" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="">All Quarters</option>
                    <option value="1" {{ request('quarter') == 1 ? 'selected' : '' }}>Q1</option>
                    <option value="2" {{ request('quarter') == 2 ? 'selected' : '' }}>Q2</option>
                    <option value="3" {{ request('quarter') == 3 ? 'selected' : '' }}>Q3</option>
                    <option value="4" {{ request('quarter') == 4 ? 'selected' : '' }}>Q4</option>
                </select>
            </div>
            <div class="min-w-0">
                <label class="block text-sm font-medium text-gray-600 mb-1 lg:hidden">Year</label>
                <select name="year" aria-label="Year" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="">All Years</option>
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-1">
                <button type="submit" class="w-full sm:flex-1 lg:flex-1 h-10 px-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['barangay_name', 'barangay_id', 'quarter', 'year']))
                <a href="{{ route('admin.quarterly-reports.index') }}" class="w-full sm:flex-1 lg:flex-1 h-10 inline-flex items-center justify-center px-3 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg text-sm transition">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Reports Table -->
    <div id="quarterly-reports-results" class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="quarterly-table-view">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed">
                <colgroup>
                    <col class="w-[19%]">
                    <col class="w-[12%]">
                    <col class="w-[9%]">
                    <col class="w-[8%]">
                    <col class="w-[17%]">
                    <col class="w-[13%]">
                    <col class="w-[22%]">
                </colgroup>
                <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Barangay</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Period</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Type</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Cases</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Submitted By</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Date</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                    <tr class="hover:bg-purple-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <span class="font-medium text-gray-800">{{ $report->barangay->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-calendar-alt text-xs"></i>
                                Q{{ $report->quarter }} {{ $report->year }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ ($report->type ?? 'VAWC') === 'BCPC' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center bg-gray-100 text-gray-700 w-10 h-10 rounded-full font-bold">
                                {{ $report->report_data['total_cases'] ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-800">{{ $report->user->first_name ?? '' }} {{ $report->user->last_name ?? '' }}</div>
                            <div class="text-xs text-gray-400">{{ $report->user->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ $report->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $report->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="{{ auth()->user()->isBcpcAdmin() ? 'quarterly-report-actions ' : '' }}flex items-center justify-center gap-2">
                                <a href="{{ route('admin.quarterly-reports.show', $report->id) }}" 
                                   class="inline-flex items-center {{ auth()->user()->isDepartmentAdmin() ? 'justify-center w-9 h-9' : 'gap-1.5 px-3 py-1.5' }} bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                   title="View Report">
                                    <i class="fas fa-eye"></i><span class="{{ auth()->user()->isDepartmentAdmin() ? 'sr-only' : '' }}">View</span>
                                </a>
                                <a href="{{ route('admin.quarterly-reports.download', $report->id) }}{{ auth()->user()->isDepartmentAdmin() ? '?preview=1' : '' }}"
                                   {!! auth()->user()->isDepartmentAdmin() ? 'target="_blank" rel="noopener"' : '' !!}
                                   class="{{ auth()->user()->isVawcAdmin() ? 'swd-quarterly-print ' : '' }}inline-flex items-center justify-center w-9 h-9 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition"
                                   title="Preview / Print">
                                    <i class="fas fa-print"></i><span class="sr-only">Preview / Print</span>
                                </a>
                                <a href="{{ route('admin.quarterly-reports.edit', $report->id) }}"
                                   class="inline-flex items-center justify-center w-9 h-9 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-medium transition"
                                   title="Edit report">
                                    <i class="fas fa-edit"></i><span class="sr-only">Edit</span>
                                </a>
                                <button type="button" 
                                        onclick="openArchiveModal({{ $report->id }}, '{{ $report->barangay->name ?? 'Barangay' }} - Q{{ $report->quarter }} {{ $report->year }} ({{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }})')"
                                        class="{{ auth()->user()->isBcpcAdmin() ? 'quarterly-report-archive-button ' : (auth()->user()->isVawcAdmin() ? 'swd-quarterly-archive ' : '') }}inline-flex items-center {{ auth()->user()->isDepartmentAdmin() ? 'justify-center w-9 h-9' : 'gap-1.5 px-3 py-1.5' }} bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 border border-red-200 rounded-lg text-xs font-medium {{ auth()->user()->isBcpcAdmin() ? '' : 'transition' }}"
                                        title="Archive report">
                                    <i class="fas fa-archive"></i><span class="{{ auth()->user()->isDepartmentAdmin() ? 'sr-only' : '' }}">Archive</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-alt text-5xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">No quarterly reports submitted yet</p>
                                <p class="text-gray-400 text-sm mt-1">Reports will appear here once desk officers submit them</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @include('components.archived-pagination', ['paginator' => $reports, 'label' => 'reports'])
        </div>

        <!-- Phone and portrait tablet report cards -->
        <div id="quarterly-mobile-cards" class="hidden p-3 space-y-3">
            @forelse($reports as $report)
            <article class="quarterly-report-card bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 shrink-0 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $report->barangay->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $report->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <span class="shrink-0 inline-flex items-center justify-center bg-gray-100 text-gray-700 w-10 h-10 rounded-full font-bold text-sm">
                        {{ $report->report_data['total_cases'] ?? 0 }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Period</p>
                        <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 px-2 py-1 rounded-full text-xs font-medium">
                            <i class="fas fa-calendar-alt"></i> Q{{ $report->quarter }} {{ $report->year }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Type</p>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ ($report->type ?? 'VAWC') === 'BCPC' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }}
                        </span>
                    </div>
                    <div class="col-span-2 min-w-0">
                        <p class="text-xs text-gray-500 mb-1">Submitted By</p>
                        <p class="font-medium text-gray-800 truncate">{{ $report->user->first_name ?? '' }} {{ $report->user->last_name ?? '' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $report->user->email ?? '' }}</p>
                    </div>
                </div>

                <div class="quarterly-report-card-actions pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.quarterly-reports.show', $report->id) }}" class="quarterly-report-card-view inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition" title="View Report">
                        <i class="fas fa-eye"></i><span class="{{ auth()->user()->isDepartmentAdmin() ? 'sr-only' : '' }}">View</span>
                    </a>
                    <a href="{{ route('admin.quarterly-reports.download', $report->id) }}{{ auth()->user()->isDepartmentAdmin() ? '?preview=1' : '' }}" {!! auth()->user()->isDepartmentAdmin() ? 'target="_blank" rel="noopener"' : '' !!} class="quarterly-report-card-print inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-medium transition" title="Preview / Print" aria-label="Preview / Print">
                        <i class="fas fa-print"></i><span>Print</span>
                    </a>
                    <a href="{{ route('admin.quarterly-reports.edit', $report->id) }}" @if(auth()->user()?->isDepartmentAdmin()) data-icon-action="labelled" aria-label="Edit report" @endif class="quarterly-report-card-edit inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-medium transition" title="Edit report">
                        <i class="fas fa-edit"></i><span>Edit</span>
                    </a>
                    <button type="button" onclick="openArchiveModal({{ $report->id }}, '{{ $report->barangay->name ?? 'Barangay' }} - Q{{ $report->quarter }} {{ $report->year }} ({{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }})')" class="quarterly-report-card-archive inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg text-xs font-medium transition" title="Archive report">
                        <i class="fas fa-archive"></i><span class="{{ auth()->user()->isDepartmentAdmin() ? 'sr-only' : '' }}">Archive</span>
                    </button>
                </div>
            </article>
            @empty
            <div class="quarterly-reports-empty-state bg-white rounded-xl border border-gray-100 p-8 text-center">
                <i class="fas fa-file-alt text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 font-medium">No quarterly reports submitted yet</p>
                <p class="text-sm text-gray-400 mt-1">Reports will appear here once desk officers submit them</p>
            </div>
            @endforelse

            <div class="pt-1">
                @if(auth()->user()?->isDepartmentAdmin())
                <div class="quarterly-phone-pagination hidden">
                    @include('components.archived-pagination', ['paginator' => $reports, 'label' => 'reports', 'pageLinkLimit' => 4])
                </div>
                @endif
                <div class="quarterly-existing-pagination">
                @include('components.archived-pagination', ['paginator' => $reports, 'label' => 'reports'])
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Custom Archive Confirmation Modal -->
@if(auth()->user()->isDepartmentAdmin())
<dialog id="generateQuarterlyModal" class="rounded-xl p-6 shadow-xl" style="box-sizing:border-box; margin:auto; width:min(28rem, calc(100vw - 2rem)); max-height:min(90vh, calc(100vh - 2rem)); max-height:min(90dvh, calc(100dvh - 2rem)); overflow-y:auto; overscroll-behavior:contain;">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Generate Quarterly Report</h2>
    <form id="generateQuarterlyReportForm" method="POST" action="{{ route('admin.quarterly-reports.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="portrait_cards" value="0">
        <input type="hidden" name="type" value="{{ auth()->user()->isVawcAdmin() ? 'VAWC' : 'BCPC' }}">
        <label class="block text-sm text-gray-700">Barangay
            <select name="barangay_id" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Barangay</option>
                @foreach($barangays as $barangay)<option value="{{ $barangay->id }}">{{ $barangay->name }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm text-gray-700">Quarter
            <select name="quarter" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Quarter</option>
                @foreach([1,2,3,4] as $q)<option value="{{ $q }}">Q{{ $q }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm text-gray-700">Year
            <select name="year" required class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Year</option>
                @for($yearOption = now()->year; $yearOption <= now()->year + 2; $yearOption++)
                    <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                @endfor
            </select>
        </label>
        <div class="flex justify-end gap-3">
            <button type="button" data-skip-unsaved-guard onclick="resetGenerateQuarterlyReportForm(); this.closest('dialog').close()" class="px-4 py-2 border border-gray-300 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded-lg">Generate</button>
        </div>
    </form>
</dialog>
<style>#generateQuarterlyModal::backdrop { background:rgba(0,0,0,.5); }</style>
@endif
<div id="archiveModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeArchiveModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 transform transition-all">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-archive text-red-600 text-xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-bold text-gray-800 text-center mb-2">Archive Report</h3>
            <p class="text-gray-500 text-sm text-center mb-6">
                Are you sure you want to archive the <strong id="archiveReportName" class="text-gray-700"></strong> report? It will be moved to archived reports.
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeArchiveModal()" 
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition text-sm">
                    Cancel
                </button>
                <form id="archiveForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition text-sm">
                        <i class="fas fa-archive mr-1"></i> Archive
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showQuarterlyReportFeedback(options) {
    return Swal.fire(options).finally(() => {
        window.quarterlyReportFeedbackPending = false;
        window.dispatchEvent(new Event('quarterly-report-feedback-dismissed'));
    });
}
function openArchiveModal(reportId, reportName) {
    document.getElementById('archiveReportName').textContent = reportName;
    document.getElementById('archiveForm').action = "{{ url('admin/quarterly-reports') }}/" + reportId + "/archive";
    document.getElementById('archiveModal').classList.remove('hidden');
}

function closeArchiveModal() {
    document.getElementById('archiveModal').classList.add('hidden');
}

@if(auth()->user()?->isDepartmentAdmin())
document.getElementById('archiveForm')?.addEventListener('submit', function () {
    this.setAttribute('data-skip-loading-modal', '');
    closeArchiveModal();
    Swal.fire({
        title: 'Archiving Report...',
        text: 'Please wait while the quarterly report is archived.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading(),
    });
});
@endif

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeArchiveModal();
});

@if(auth()->user()->isBcpcAdmin() && session('generated_bcpc_quarterly_report'))
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('generateQuarterlyReportForm');
    form?.reset();
    if (form) form.elements.year.value = '';
    showQuarterlyReportFeedback({
        icon: 'success',
        title: 'Report Generated Successfully!',
        text: 'The quarterly report has been successfully generated and saved.',
        confirmButtonColor: '#7e22ce',
        confirmButtonText: 'OK',
    });
});
@endif

@if(auth()->user()->isVawcAdmin() && session('generated_vawc_quarterly_report'))
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('generateQuarterlyReportForm');
    form?.reset();
    if (form) form.elements.year.value = '';
    showQuarterlyReportFeedback({
        icon: 'success',
        title: 'Report Generated Successfully!',
        text: 'The quarterly report has been successfully generated and saved.',
        confirmButtonColor: '#7e22ce',
        confirmButtonText: 'OK',
    });
});
@endif

@if(auth()->user()->isDepartmentAdmin() && session('archived_quarterly_report'))
document.addEventListener('DOMContentLoaded', function () {
    showQuarterlyReportFeedback({
        icon: 'success',
        title: 'Report Archived Successfully!',
        text: @json(session('archived_quarterly_report')),
        confirmButtonColor: '#7e22ce',
        confirmButtonText: 'OK',
    });
});
@endif

@if(auth()->user()->isDepartmentAdmin() && session('duplicate_quarterly_report'))
document.addEventListener('DOMContentLoaded', function () {
    showQuarterlyReportFeedback({
        icon: 'info',
        title: 'Report Already Generated',
        text: 'This report has already been generated. Please check the Quarterly Reports section to view the existing report.',
        confirmButtonColor: '#7c3aed',
    });
});
@endif

@if(auth()->user()->isDepartmentAdmin() && session('no_cases_for_quarterly_report'))
document.addEventListener('DOMContentLoaded', function () {
    showQuarterlyReportFeedback({
        icon: 'info',
        title: 'No Cases Available',
        text: 'No cases are available for {{ session('no_cases_for_quarterly_report') }}, so a report cannot be generated.',
        confirmButtonColor: '#7c3aed',
        confirmButtonText: 'OK',
    });
});
@endif

@if(auth()->user()->isDepartmentAdmin())
function resetGenerateQuarterlyReportForm() {
    document.getElementById('generateQuarterlyReportForm')?.reset();
}

function openGenerateQuarterlyReportModal() {
    resetGenerateQuarterlyReportForm();
    document.getElementById('generateQuarterlyModal')?.showModal();
}

document.getElementById('generateQuarterlyReportForm')?.addEventListener('submit', function (event) {
    this.elements.portrait_cards.value = matchMedia('(min-width: 48rem) and (max-width: 85.375rem)').matches ? '1' : '0';
    const barangayId = this.elements.barangay_id.value;
    const quarter = this.elements.quarter.value;
    const year = this.elements.year.value;
    const activePeriods = @json($activeBcpcReportPeriods ?? []);

    if (barangayId && quarter && year && activePeriods.includes(`${barangayId}-${quarter}-${year}`)) {
        event.preventDefault();
        this.closest('dialog')?.close();
        requestAnimationFrame(() => {
            Swal.fire({
                icon: 'info',
                title: 'Report Already Generated',
                text: 'This report has already been generated. Please check the Quarterly Reports section to view the existing report.',
                confirmButtonColor: '#7c3aed',
            });
        });
        return;
    }
    event.preventDefault();
    this.closest('dialog')?.close();
    this.setAttribute('data-skip-loading-modal', '');
    requestAnimationFrame(() => {
        Swal.fire({
            title: 'Generating Report...',
            text: 'Please wait while the quarterly report is generated.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading(),
        });
        HTMLFormElement.prototype.submit.call(this);
    });
});
@endif

document.getElementById('printAllQuarterlyReportsButton')?.addEventListener('click', function (event) {
    event.preventDefault();
    const url = new URL(this.href, window.location.origin);
    const filters = new FormData(document.getElementById('quarterly-reports-filter-form'));
    url.search = new URLSearchParams(filters).toString();
    url.searchParams.set('preview', '1');
    window.open(url, '_blank', 'noopener');
});

@if(auth()->user()->isDepartmentAdmin())
(() => {
    let activeRequest;
    const refreshReports = async (url) => {
        activeRequest?.abort();
        const request = new AbortController();
        activeRequest = request;
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: request.signal,
            });
            if (!response.ok) return;
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const replacement = page.querySelector('#quarterly-reports-results');
            const current = document.querySelector('#quarterly-reports-results');
            if (!replacement || !current || request.signal.aborted || activeRequest !== request) return;
            current.replaceWith(replacement);
            history.replaceState({}, '', url);
        } catch (error) {
            if (error.name !== 'AbortError') throw error;
        }
    };

    document.addEventListener('input', (event) => {
        if (event.target.id !== 'barangay-search') return;
        const form = event.target.closest('#quarterly-reports-filter-form');
        const url = new URL(form.action, location.origin);
        url.search = new URLSearchParams(new FormData(form)).toString();
        refreshReports(url);
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('#quarterly-reports-results a[href]');
        if (!link) return;
        const url = new URL(link.href, location.origin);
        if (url.origin !== location.origin || !url.searchParams.has('page')) return;
        event.preventDefault();
        refreshReports(url);
    });
})();
@endif

</script>
@endsection
