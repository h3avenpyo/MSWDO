@extends('layouts.desk-officer')
@php($archiveTitle = in_array(auth()->user()?->defaultCaseDepartment(), ['VAWC', 'BCPC'], true) ? 'Archived Reports' : 'Archived Quarterly Reports')
@section('title', $archiveTitle)
@push('styles')
<style>
    .archived-quarterly-reports-stable,
    .archived-quarterly-reports-stable :is(table, button, a) {
        animation: none !important;
        transition: none !important;
    }
    @if(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
    #archived-report-quarter { width: 130px !important; min-width: 130px !important; max-width: 130px !important; flex: 0 0 130px !important; }
    #archived-report-year { width: 115px !important; min-width: 115px !important; max-width: 115px !important; flex: 0 0 115px !important; }
    @endif
    @media (max-width: 47.9375rem) {
        .archived-quarterly-reports-stable { padding: 1rem; padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom))); }
        .archived-quarterly-filter-controls {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) !important;
            gap: .75rem !important;
            width: 100%;
        }
        .archived-quarterly-filter-controls > :is(div, select, button, a) {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            flex: none !important;
            height: 3rem !important;
            min-height: 3rem !important;
        }
        .archived-quarterly-filter-controls > div { height: auto !important; }
        .archived-quarterly-filter-controls input { height: 3rem !important; }
        .archived-quarterly-filter-controls :is(select, button, a) { justify-content: center; }
    }
    @if(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        #archived-report-quarter,
        #archived-report-year {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            flex: none !important;
        }
        .archived-quarterly-filter-controls {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: .625rem !important;
        }
        .archived-quarterly-filter-controls > div,
        .archived-quarterly-filter-controls > :is(button, a) {
            grid-column: 1 / -1;
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

    /* Archived Quarterly Reports tablet navigation (768px–1280px / 48rem–80rem): collapsed sidebar on the left without button, standard clean header */
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
        body.desk-officer-quarterly-reports-mobile-nav .archived-quarterly-reports-stable {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1.25rem 1.5rem !important;
            box-sizing: border-box !important;
        }
        body.desk-officer-quarterly-reports-mobile-nav .archived-quarterly-filter-controls {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 0.75rem !important;
            flex-wrap: wrap !important;
        }
        body.desk-officer-quarterly-reports-mobile-nav #archived-report-quarter {
            width: 130px !important;
            min-width: 130px !important;
            flex: 0 0 130px !important;
        }
        body.desk-officer-quarterly-reports-mobile-nav #archived-report-year {
            width: 115px !important;
            min-width: 115px !important;
            flex: 0 0 115px !important;
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
    }
</style>
@endpush
@section('content')
@include('components.desk-officer.bcpc-dashboard-header', ['title' => $archiveTitle, 'subtitle' => auth()->user()->isMultiCaseDepartment() ? 'Review or restore archived quarterly reports for your assigned barangay.' : 'View archived quarterly reports'])
<div class="archived-quarterly-reports-stable w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $archiveTitle }}</h1>
            <p class="text-gray-500 text-sm mt-0.5">Review, download, or restore archived quarterly reports for your assigned barangay.</p>
        </div>
        @endunless
        @unless(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->isMultiCaseDepartment() || in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('desk-officer.quarterly-reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-file-alt"></i>
                <span>Active Reports</span>
            </a>
        </div>
        @endunless
    </div>
    @if(session('success'))<p class="p-4 mb-4 bg-green-50 text-green-700 rounded-lg border border-green-200">{{ session('success') }}</p>@endif
    <!-- Search and Filter Bar -->
    <form method="GET" action="{{ route('desk-officer.quarterly-reports.archived') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="archived-quarterly-filter-controls flex items-center gap-2 sm:gap-3">
            <div class="relative flex-1 min-w-0" style="flex: 1 1 auto !important; min-width: 0 !important;">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" id="archived-report-search" value="{{ request('search') }}" placeholder="Search archived reports..." class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <select name="quarter" id="archived-report-quarter" class="h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Quarters</option>
                @foreach([1, 2, 3, 4] as $qOpt)
                    <option value="{{ $qOpt }}" {{ request('quarter') == $qOpt ? 'selected' : '' }}>Q{{ $qOpt }}</option>
                @endforeach
            </select>
            <select name="year" id="archived-report-year" class="h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Years</option>
                @foreach(range(2026, 2030) as $yOpt)
                    <option value="{{ $yOpt }}" {{ request('year') == $yOpt ? 'selected' : '' }}>{{ $yOpt }}</option>
                @endforeach
            </select>
            <button type="submit" style="flex: 0 0 auto !important;" class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0">
                <i class="fas fa-filter"></i>
                <span>Filter</span>
            </button>
            @if(request()->filled('search') || request()->filled('quarter') || request()->filled('year'))
            <a href="{{ route('desk-officer.quarterly-reports.archived') }}" style="flex: 0 0 auto !important;" class="inline-flex h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition shrink-0">
                <i class="fas fa-times"></i>
                <span>Clear</span>
            </a>
            @endif
        </div>
    </form>

    @include('desk-officer.quarterly-reports._tables', ['archived' => true])
    @if(isset($archivedMonitorings))
        @include('desk-officer.quarterly-reports._archived-monitorings')
    @endif
</div>

@push('scripts')
<script>
// Live client-side search & filter on archived quarterly reports tables
const repSearch = document.getElementById('archived-report-search');
const repQuarter = document.getElementById('archived-report-quarter');
const repYear = document.getElementById('archived-report-year');
function filterReportRows() {
    const q = (repSearch?.value || '').toLowerCase().trim();
    const quarterVal = (repQuarter?.value || '').toLowerCase().trim();
    const yearVal = (repYear?.value || '').toLowerCase().trim();
    document.querySelectorAll('.desk-officer-quarterly-report-table table tbody tr').forEach(row => {
        if (row.cells.length < 5) return;
        const text = row.textContent.toLowerCase();
        const periodCell = (row.cells[0]?.textContent || '').toLowerCase().trim();
        const matchesSearch = !q || text.includes(q);
        const matchesQuarter = !quarterVal || periodCell.includes('q' + quarterVal);
        const matchesYear = !yearVal || periodCell.includes(yearVal);
        row.style.display = (matchesSearch && matchesQuarter && matchesYear) ? '' : 'none';
    });
}
if (repSearch) repSearch.addEventListener('input', filterReportRows);
if (repQuarter) repQuarter.addEventListener('change', filterReportRows);
if (repYear) repYear.addEventListener('change', filterReportRows);
</script>
@endpush
@endsection
