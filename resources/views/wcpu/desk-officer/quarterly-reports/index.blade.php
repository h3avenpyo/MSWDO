@extends('layouts.desk-officer')
@section('title', 'Quarterly Reports')
@push('styles')
<style>
    @media (max-width: 767px), (hover: none) and (pointer: coarse) {
        .quarterly-report-actions-container,
        .quarterly-report-actions { width: 100%; }
        .quarterly-generate-report-button {
            display: inline-flex;
            width: 100%;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            padding-right: .75rem;
            padding-left: .75rem;
        }
    }
    @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment())
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        .quarterly-print-all-button {
            display: inline-flex;
            width: 100%;
            height: 2.5rem;
            min-height: 2.5rem;
            align-items: center;
            justify-content: center;
            padding-right: .75rem;
            padding-left: .75rem;
        }
    }
    @endif

    /* Dashboard-style red navbar for mobile/tablet, matching /admin-dashboard */
    @media (max-width: 80rem) {
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileTopBar {
            display: none !important;
        }
    }

    @media (max-width: 47.9375rem) {
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileTopBar {
            display: none !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebarCloseBtn,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header > div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:first-of-type,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header > div > div:first-of-type {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:first-of-type > div,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header > div > div:first-of-type > div {
            min-width: 0 !important;
            flex: 1 1 auto !important;
            text-align: left !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
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
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) i {
            color: inherit !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header p {
            display: block !important;
            margin-top: .125rem !important;
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p :is(span, strong),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header p :is(span, strong) {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) div:first-of-type :is(span, a),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:last-child,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header > div > div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header button[aria-label="Notifications"],
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header button[aria-label="Notifications"] > i,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header #notifications-unread-count,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header #notifications-unread-count {
            width: 1.25rem !important;
            height: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
        }

        /* Sidebar dropdown drawer below header */
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar {
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
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar nav :is(a, button),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar nav :is(a, button):hover,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600, .bg-purple-700\/70),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }
    }

    @media (max-width: 40rem) {
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header button:has(> img[alt="User Profile"]) {
            display: none !important;
        }
    }

    /* Quarterly reports tablet navigation (768px–1280px / 48rem–80rem): collapsed sidebar on the left without button, standard clean header */
    @media (min-width: 48rem) and (max-width: 80rem) {
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileTopBar,
        body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileTopBar,
        body.desk-officer-quarterly-reports-mobile-nav #mobileTopBar,
        .desk-officer-quarterly-reports-mobile-nav #mobileTopBar,
        #mobileTopBar {
            display: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileMenuToggle,
        body.desk-officer-quarterly-reports-mobile-nav #mobileMenuToggle,
        #mobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #dashboardMenuToggle,
        body.desk-officer-quarterly-reports-mobile-nav #dashboardMenuToggle,
        #dashboardMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #pageMobileMenuToggle,
        body.desk-officer-quarterly-reports-mobile-nav #pageMobileMenuToggle,
        #pageMobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebarCloseBtn,
        body.desk-officer-quarterly-reports-mobile-nav #sidebarCloseBtn,
        #sidebarCloseBtn {
            display: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebarOverlay,
        body.desk-officer-quarterly-reports-mobile-nav #sidebarOverlay,
        #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebarToggleBtn,
        body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebarToggleBtn,
        body.desk-officer-quarterly-reports-mobile-nav #sidebarToggleBtn,
        .desk-officer-quarterly-reports-mobile-nav #sidebarToggleBtn,
        #sidebarToggleBtn {
            display: none !important;
        }

        /* Clean standard header on tablet */
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header,
        body.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header,
        #desk-officer-dashboard-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            left: 0 !important;
            right: 0 !important;
            border-left: 0 !important;
            box-sizing: border-box !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header > div,
        #desk-officer-dashboard-header > div {
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3),
        #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #1f2937 !important;
            font-size: 1.5rem !important;
            line-height: 1.25 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header p,
        #desk-officer-dashboard-header p {
            color: #6b7280 !important;
            font-size: 0.875rem !important;
            display: block !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a),
        #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #4b5563 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header button[aria-label="Notifications"],
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"],
        #desk-officer-dashboard-header button[aria-label="Notifications"] {
            border: 1px solid #e5e7eb !important;
            background: transparent !important;
            color: #4b5563 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header button[aria-label="Notifications"] > i,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] > i,
        #desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            color: #4b5563 !important;
        }

        /* Sidebar: collapsed icon rail on the left */
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar,
        body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar,
        .desk-officer-quarterly-reports-mobile-nav #sidebar,
        .portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar {
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
            transition: width 0.3s ease !important;
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header,
        .desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header {
            display: flex !important;
            min-height: 5.5rem;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header img,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header img,
        .desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header img {
            width: 3rem !important;
            height: 3rem !important;
            flex-shrink: 0 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header .sidebar-text,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header .sidebar-text,
        .desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-header .sidebar-text {
            display: none !important;
            opacity: 0 !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-text,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed .sidebar-text,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-text,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-menu,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed .sidebar-menu,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-menu,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed .sidebar-link,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-link,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link i,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed .sidebar-link i,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-link i,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar .sidebar-link i {
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            text-align: center !important;
        }

        /* Main Content offset by collapsed sidebar */
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar ~ #mainContent,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed ~ #mainContent,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init ~ #mainContent,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar ~ #mainContent,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed ~ #mainContent,
        body.desk-officer-quarterly-reports-mobile-nav #sidebar.sidebar-collapsed-init ~ #mainContent,
        body.desk-officer-quarterly-reports-mobile-nav #mainContent,
        .desk-officer-quarterly-reports-mobile-nav #mainContent,
        .portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent {
            width: calc(100% - 4.5rem) !important;
            margin-left: 4.5rem !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            min-width: 0 !important;
            height: auto !important;
            min-height: 100vh;
            overflow: visible !important;
        }

        /* Tablet content adjustments */
        body.desk-officer-quarterly-reports-mobile-nav #mainContent > div.max-w-7xl {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1.25rem 1.5rem !important;
            box-sizing: border-box !important;
        }
        body.desk-officer-quarterly-reports-mobile-nav .quarterly-table-view {
            display: block !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            border-radius: 0.75rem !important;
        }
        body.desk-officer-quarterly-reports-mobile-nav .quarterly-table-view table {
            min-width: 50rem;
            width: 100%;
        }
        body.desk-officer-quarterly-reports-mobile-nav .quarterly-mobile-cards {
            display: none !important;
        }
        body.desk-officer-quarterly-reports-mobile-nav .quarterly-report-actions-container {
            width: auto !important;
        }
    }
</style>
@endpush
@section('content')

@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Quarterly Reports', 'subtitle' => 'View and manage quarterly reports'])
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
        <h1 class="text-2xl font-bold text-gray-800">Quarterly Reports</h1>
        @endunless
        <div class="quarterly-report-actions-container flex flex-wrap items-center gap-4">
            <div class="quarterly-report-actions flex flex-wrap items-center gap-2">
            @unless(auth()->user()->isMultiCaseDepartment() || (auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
            <a href="{{ route('desk-officer.quarterly-reports.archived') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm {{ auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) ? 'border border-[#C41E3A] bg-[#C41E3A] text-white hover:bg-[#A01830]' : 'border border-purple-200 text-purple-700 hover:bg-purple-50' }}">
                @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
                <i class="fas fa-archive text-amber-500"></i>
                @endunless
                <span>Archived Reports</span>
            </a>
            @endunless
            @if(auth()->user()->isMultiCaseDepartment())
            <button type="button" onclick="openReportTypeModal()" class="quarterly-generate-report-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">Generate New Report</button>
            <button type="button" onclick="printAllQuarterlyReports()" class="quarterly-print-all-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">Print All</button>
            @else
            <a href="{{ route('desk-officer.quarterly-reports.create') }}" class="quarterly-generate-report-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">Generate New Report</a>
            @endif
            </div>
        </div>
    </div>
    @if(session('success'))<p class="p-4 mb-4 bg-green-50 text-green-700 rounded-lg">{{ session('success') }}</p>@endif
    @if(session('error'))<p class="p-4 mb-4 bg-red-50 text-red-700 rounded-lg">{{ session('error') }}</p>@endif
    @include('desk-officer.quarterly-reports._tables')
</div>
@endsection
@push('scripts')
@if(auth()->user()->isMultiCaseDepartment())
<script>
async function printAllQuarterlyReports() {
    const result = await Swal.fire({ title: 'Print All Reports', input: 'select', inputOptions: { BCPC: 'BCPC Case Reports', VAWC: 'SWD Case Reports' }, inputPlaceholder: 'Select department', showCancelButton: true, confirmButtonText: 'Print', inputValidator: value => !value ? 'Select a department.' : undefined });
    if (!result.isConfirmed) return;
    const counts = @json(collect($reportGroups)->map(fn ($group) => $group->total()));
    if (!counts[result.value]) {
        await Swal.fire({ icon: 'info', title: 'No Reports to Print', text: 'There are no quarterly reports to print.' });
        return;
    }
    window.open(@json(route('desk-officer.quarterly-reports.print-all')) + '?type=' + encodeURIComponent(result.value), '_blank', 'noopener');
}
</script>
@endif
@if(request('select_type'))
<script>document.addEventListener('DOMContentLoaded', () => openReportTypeModal());</script>
@endif
@endpush
