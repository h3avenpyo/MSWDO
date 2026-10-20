{{-- filepath: c:\Users\USER\WCPU_WEB\resources\views\desk-officer\referrals.blade.php --}}
@extends('layouts.desk-officer')

@section('content')
@php
    $isBcpcDeskOfficer = auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' && !auth()->user()?->isMultiCaseDepartment();
    $isSwdDeskOfficer = auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'VAWC' && !auth()->user()?->isMultiCaseDepartment();
    $isMultiroleDeskOfficer = auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment();
@endphp
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Referrals', 'subtitle' => 'View and manage case referrals'])
@if($isBcpcDeskOfficer)
@push('styles')
<style>
    .view-referral-btn:hover,
    .view-referral-btn:focus,
    .view-referral-btn:active {
        background-color: #A01830 !important;
        border-color: #A01830 !important;
        color: #ffffff !important;
    }
</style>
@endpush
@endif
@push('styles')
<style>


    @media (max-width: 47.9375rem) {
        #desk-referrals-content .referral-mobile-cards .referral-card-actions {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .5rem !important;
            width: 100%;
        }
        #desk-referrals-content .referral-mobile-cards .referral-card-actions > :is(a, button) {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 3rem !important;
            min-width: 3rem !important;
            min-height: 3rem !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }

    /* Dashboard-style red navbar for mobile/tablet, matching /admin-dashboard */
    @media (max-width: 80rem) {
        html body.portal-ui.desk-officer-referrals-mobile-nav #mobileTopBar {
            display: none !important;
        }
    }

    @media (max-width: 47.9375rem) {
        html body.portal-ui.desk-officer-referrals-mobile-nav #mobileTopBar {
            display: none !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebarCloseBtn,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header > div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:first-of-type,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header > div > div:first-of-type {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:first-of-type > div,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header > div > div:first-of-type > div {
            min-width: 0 !important;
            flex: 1 1 auto !important;
            text-align: left !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
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
        html body.portal-ui.desk-officer-referrals-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) i {
            color: inherit !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header p {
            display: block !important;
            margin-top: .125rem !important;
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p :is(span, strong),
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header p :is(span, strong) {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) div:first-of-type :is(span, a),
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:last-child,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header > div > div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header button[aria-label="Notifications"],
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header button[aria-label="Notifications"] > i,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header #notifications-unread-count,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header #notifications-unread-count {
            width: 1.25rem !important;
            height: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
        }

        /* Sidebar dropdown drawer below header */
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar {
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
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar nav :is(a, button),
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar nav :is(a, button):hover,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600, .bg-purple-700\/70),
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }
    }

    @media (max-width: 40rem) {
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header button:has(> img[alt="User Profile"]) {
            display: none !important;
        }
    }

    /* Referrals tablet navigation (768px–1280px / 48rem–80rem): collapsed sidebar on the left without button, standard clean header */
    @media (min-width: 48rem) and (max-width: 80rem) {
        html body.portal-ui.desk-officer-referrals-mobile-nav #mobileTopBar,
        body.portal-ui.desk-officer-referrals-mobile-nav #mobileTopBar,
        body.desk-officer-referrals-mobile-nav #mobileTopBar,
        .desk-officer-referrals-mobile-nav #mobileTopBar,
        #mobileTopBar {
            display: none !important;
        }

        html body.portal-ui.desk-officer-referrals-mobile-nav #mobileMenuToggle,
        body.desk-officer-referrals-mobile-nav #mobileMenuToggle,
        #mobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-referrals-mobile-nav #dashboardMenuToggle,
        body.desk-officer-referrals-mobile-nav #dashboardMenuToggle,
        #dashboardMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-referrals-mobile-nav #pageMobileMenuToggle,
        body.desk-officer-referrals-mobile-nav #pageMobileMenuToggle,
        #pageMobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebarCloseBtn,
        body.desk-officer-referrals-mobile-nav #sidebarCloseBtn,
        #sidebarCloseBtn {
            display: none !important;
        }

        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebarOverlay,
        body.desk-officer-referrals-mobile-nav #sidebarOverlay,
        #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebarToggleBtn,
        body.portal-ui.desk-officer-referrals-mobile-nav #sidebarToggleBtn,
        body.desk-officer-referrals-mobile-nav #sidebarToggleBtn,
        .desk-officer-referrals-mobile-nav #sidebarToggleBtn,
        #sidebarToggleBtn {
            display: none !important;
        }

        /* Clean standard header on tablet */
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header,
        body.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header,
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
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header > div,
        #desk-officer-dashboard-header > div {
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3),
        #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #1f2937 !important;
            font-size: 1.5rem !important;
            line-height: 1.25 !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header p,
        #desk-officer-dashboard-header p {
            color: #6b7280 !important;
            font-size: 0.875rem !important;
            display: block !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a),
        #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #4b5563 !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header button[aria-label="Notifications"],
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"],
        #desk-officer-dashboard-header button[aria-label="Notifications"] {
            border: 1px solid #e5e7eb !important;
            background: transparent !important;
            color: #4b5563 !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent > header button[aria-label="Notifications"] > i,
        html body.portal-ui.desk-officer-referrals-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] > i,
        #desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            color: #4b5563 !important;
        }

        /* Sidebar: collapsed icon rail on the left */
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar,
        body.portal-ui.desk-officer-referrals-mobile-nav #sidebar,
        body.desk-officer-referrals-mobile-nav #sidebar,
        .desk-officer-referrals-mobile-nav #sidebar,
        .portal-ui.desk-officer-referrals-mobile-nav #sidebar {
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
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-header,
        body.desk-officer-referrals-mobile-nav #sidebar .sidebar-header,
        .desk-officer-referrals-mobile-nav #sidebar .sidebar-header {
            display: flex !important;
            min-height: 5.5rem;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-header img,
        body.desk-officer-referrals-mobile-nav #sidebar .sidebar-header img,
        .desk-officer-referrals-mobile-nav #sidebar .sidebar-header img {
            width: 3rem !important;
            height: 3rem !important;
            flex-shrink: 0 !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-header .sidebar-text,
        body.desk-officer-referrals-mobile-nav #sidebar .sidebar-header .sidebar-text,
        .desk-officer-referrals-mobile-nav #sidebar .sidebar-header .sidebar-text {
            display: none !important;
            opacity: 0 !important;
        }

        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init,
        body.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed,
        body.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-text,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed .sidebar-text,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-text,
        body.desk-officer-referrals-mobile-nav #sidebar .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-menu,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed .sidebar-menu,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-menu,
        body.desk-officer-referrals-mobile-nav #sidebar .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-link,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed .sidebar-link,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-link,
        body.desk-officer-referrals-mobile-nav #sidebar .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar .sidebar-link i,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed .sidebar-link i,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-link i,
        body.desk-officer-referrals-mobile-nav #sidebar .sidebar-link i {
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            text-align: center !important;
        }

        /* Main Content offset by collapsed sidebar */
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar ~ #mainContent,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed ~ #mainContent,
        html body.portal-ui.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init ~ #mainContent,
        html body.portal-ui.desk-officer-referrals-mobile-nav #mainContent,
        body.desk-officer-referrals-mobile-nav #sidebar ~ #mainContent,
        body.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed ~ #mainContent,
        body.desk-officer-referrals-mobile-nav #sidebar.sidebar-collapsed-init ~ #mainContent,
        body.desk-officer-referrals-mobile-nav #mainContent,
        .desk-officer-referrals-mobile-nav #mainContent,
        .portal-ui.desk-officer-referrals-mobile-nav #mainContent {
            width: calc(100% - 4.5rem) !important;
            margin-left: 4.5rem !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            min-width: 0 !important;
            height: auto !important;
            min-height: 100vh;
            overflow: visible !important;
        }

        /* Tablet Content & Table Layout */
        #desk-referrals-content {
            width: 100% !important;
            max-width: 100% !important;
            padding: 1.25rem 1.5rem !important;
            box-sizing: border-box !important;
        }
        #desk-referrals-content .referrals-table-view {
            display: block !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            border-radius: 0.75rem !important;
        }
        #desk-referrals-content .referrals-table-view + .md\:hidden {
            display: none !important;
        }
        #desk-referrals-content .referrals-toolbar {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 1rem !important;
            flex-wrap: wrap !important;
            margin-bottom: 1rem !important;
        }
        #desk-referrals-content .printable-referrals-table table {
            min-width: 58rem;
            width: 100%;
        }
        #desk-referrals-content .printable-referrals-table :is(th, td) {
            padding: 0.75rem 1rem !important;
            font-size: 0.8125rem !important;
        }
        #desk-referrals-content .actions-column {
            white-space: nowrap !important;
        }
        #desk-referrals-content .actions-column > div {
            display: inline-flex !important;
            flex-wrap: nowrap !important;
            gap: 0.375rem !important;
            align-items: center !important;
            justify-content: center !important;
        }
        #desk-referrals-content .actions-column :is(button, a) {
            flex: 0 0 auto !important;
            width: 2.25rem !important;
            min-width: 2.25rem !important;
            height: 2.25rem !important;
            padding: 0 !important;
        }
    }
</style>
@endpush
@unless($isBcpcDeskOfficer || $isSwdDeskOfficer || (auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment()))
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Referrals</h2>
            <p class="text-sm text-gray-500 hidden sm:block">View and manage case referrals</p>
        </div>
        <div class="flex items-center gap-4">
            @if($isBcpcDeskOfficer)
                @include('desk-officer.components.notifications')
                <div class="hidden sm:flex items-center gap-3">
                    <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</header>
@endunless
<div id="desk-referrals-content" class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="referrals-toolbar mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <label class="relative block w-full sm:w-80">
            <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400" aria-hidden="true"></i>
            <input type="search" id="referrals-search-input" placeholder="Search referrals..." aria-label="Search referrals" class="h-11 w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500">
        </label>
        <a href="{{ route('desk-officer.referrals.print-all') }}" class="download-referrals-btn inline-flex min-h-11 items-center justify-center gap-2 bg-[#C41E3A] hover:bg-[#A01830] text-white px-5 py-2.5 rounded-lg font-medium transition shadow-md">
            <i class="fas fa-print"></i> <span>Print All Referrals</span>
        </a>
    </div>
    <!-- Referral Registry Table -->
    <div class="{{ $isMultiroleDeskOfficer ? '' : 'card' }}">
    <!-- Referrals Table (Desktop) -->
    <div class="{{ $isMultiroleDeskOfficer ? 'space-y-6' : 'bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100' }}">
        @foreach($referralGroups as $department => $referrals)
        @if($isMultiroleDeskOfficer)
        <section class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <h2 class="px-6 py-4 text-lg font-semibold text-[#991b1b]">{{ $department === 'VAWC' ? 'SWD' : $department }} Referrals</h2>
        @endif
        <div class="referrals-table-view hidden md:block overflow-x-auto printable-referrals-table">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Client</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Age</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Sex</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Services Needed</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Receiving Agency</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Feedback</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold actions-column">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($referrals as $referral)
                    @php
                        $adminReferralExists = $referral->caseHasDepartmentAdminReferral();
                        $editDisabled = filled($referral->feedback) || $adminReferralExists;
                        $editDisabledTitle = $adminReferralExists
                            ? 'Editing is disabled after an Admin refers this case'
                            : 'Editing is disabled after feedback is submitted';
                    @endphp
                    <tr class="referral-searchable hover:bg-purple-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate"><a href="{{ route('desk-officer.referrals.show', $referral->id) }}" class="text-inherit no-underline">{{ censorLastName($referral->client_name) }}</a></p>
                                <p class="text-xs text-gray-500 truncate">{{ $referral->referral_number }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $referral->client_age }}</td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $referral->client_sex }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 max-w-[150px] truncate" title="{{ $referral->specific_services_requested }}">
                                {{ Str::limit($referral->specific_services_requested, 40) }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $referral->recipient_name }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-[180px]">{{ $referral->recipient_email }}</p>
                                @if($isBcpcDeskOfficer && $referral->recipient && ($referral->recipient->trashed() || !$referral->recipient->active))
                                    <p class="mt-1 text-xs font-medium text-red-600">The recipient is currently unavailable. The referral cannot be processed due to this reason.</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($referral->date_of_referral)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($referral->feedback)
                                <p class="text-sm text-gray-600 max-w-[120px] truncate" title="{{ $referral->feedback }}">
                                    {{ Str::limit($referral->feedback, 30) }}
                                </p>
                            @else
                                <span class="text-xs text-gray-400">No feedback yet</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 actions-column">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" data-icon-action="labelled" class="view-referral-btn inline-flex items-center justify-center gap-1.5 bg-blue-600 {{ $isBcpcDeskOfficer ? 'hover:bg-[#A01830]' : 'hover:bg-blue-700' }} text-white px-3 py-1.5 rounded-lg text-xs font-medium transition shadow-sm"
                                    title="View Details" data-id="{{ $referral->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($editDisabled)
                                <button type="button" disabled class="inline-flex items-center justify-center gap-1.5 bg-red-100 text-red-800 px-3 py-1.5 rounded-lg text-xs font-medium cursor-not-allowed" style="background-color: #FEE2E2 !important; color: #991B1B !important; border-color: #FCA5A5 !important;" title="{{ $editDisabledTitle }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @else
                                <button type="button" class="edit-referral-btn inline-flex items-center justify-center gap-1.5 bg-purple-700 hover:bg-purple-800 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition"
                                    title="Edit" data-id="{{ $referral->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endif

<button type="button" data-icon-action="labelled" class="archive-referral-btn inline-flex items-center gap-1.5 {{ ($isBcpcDeskOfficer || auth()->user()->isMultiCaseDepartment()) ? 'bg-[#C41E3A] hover:bg-[#A01830] border-[#C41E3A] text-white' : 'bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 border-red-200' }} border px-3 py-1.5 rounded-lg text-xs font-medium transition" title="Archive" data-url="{{ route('desk-officer.referrals.archive', $referral->id) }}"><i class="fas fa-archive"></i><span class="sr-only">Archive</span></button>

                                <a href="{{ route('desk-officer.referrals.print', $referral->id) }}"
                                    class="print-referral-btn inline-flex items-center justify-center {{ auth()->user()?->defaultCaseDepartment() === 'VAWC' ? 'bg-[#C41E3A] hover:bg-[#A01830]' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-1.5 rounded-lg text-xs font-medium transition" title="Print">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No referrals found</p>
                                <p class="text-sm text-gray-400 mt-1">Referrals will appear here once created.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phone-only case-style referral cards -->
        <div class="referral-mobile-cards md:hidden space-y-3 p-3">
            @forelse($referrals as $referral)
            @php
                $adminReferralExists = $referral->caseHasDepartmentAdminReferral();
                $editDisabled = filled($referral->feedback) || $adminReferralExists;
                $editDisabledTitle = $adminReferralExists
                    ? 'Editing is disabled after an Admin refers this case'
                    : 'Editing is disabled after feedback is submitted';
            @endphp
            <article class="referral-searchable rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3">
                    <div class="min-w-0"><p class="text-xs text-gray-500">Client</p><p class="font-semibold text-gray-800 break-words">{{ censorLastName($referral->client_name) }}</p><p class="text-xs text-gray-500 break-all">{{ $referral->referral_number }}</p></div>
                    <div class="shrink-0 text-right text-sm"><p class="text-xs text-gray-500">Date</p><p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($referral->date_of_referral)->format('M d, Y') }}</p></div>
                </div>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 py-3 text-sm">
                    <div><dt class="text-xs text-gray-500">Age</dt><dd class="font-medium text-gray-800">{{ $referral->client_age }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Sex</dt><dd class="font-medium text-gray-800">{{ $referral->client_sex }}</dd></div>
                    <div class="col-span-2"><dt class="text-xs text-gray-500">Services Needed</dt><dd class="font-medium text-gray-800 break-words">{{ $referral->specific_services_requested ?: 'Not recorded' }}</dd></div>
                    <div class="col-span-2"><dt class="text-xs text-gray-500">Receiving Agency</dt><dd class="font-medium text-gray-800 break-words">{{ $referral->recipient_name ?: 'Not recorded' }}<span class="block text-xs font-normal text-gray-500 break-all">{{ $referral->recipient_email }}</span></dd></div>
                    <div class="col-span-2"><dt class="text-xs text-gray-500">Feedback</dt><dd class="font-medium text-gray-800 break-words">{{ $referral->feedback ?: 'No feedback yet' }}</dd></div>
                </dl>
                <div class="referral-card-actions grid grid-cols-4 gap-2 border-t border-gray-100 pt-3">
                    <button type="button" class="view-referral-btn inline-flex min-h-12 items-center justify-center rounded-lg bg-blue-600 text-white" title="View Details" data-id="{{ $referral->id }}"><i class="fas fa-eye"></i><span class="sr-only">View</span></button>
                    @if($editDisabled)
                    <button type="button" disabled class="inline-flex min-h-12 items-center justify-center rounded-lg bg-red-100 text-red-800 cursor-not-allowed" style="background-color: #FEE2E2 !important; color: #991B1B !important; border-color: #FCA5A5 !important;" title="{{ $editDisabledTitle }}"><i class="fas fa-edit"></i><span class="sr-only">Edit</span></button>
                    @else
                    <button type="button" class="edit-referral-btn inline-flex min-h-12 items-center justify-center rounded-lg bg-purple-700 text-white" title="Edit" data-id="{{ $referral->id }}"><i class="fas fa-edit"></i><span class="sr-only">Edit</span></button>
                    @endif
                    <button type="button" class="archive-referral-btn inline-flex min-h-12 items-center justify-center rounded-lg bg-[#C41E3A] text-white" title="Archive" data-url="{{ route('desk-officer.referrals.archive', $referral->id) }}"><i class="fas fa-archive"></i><span class="sr-only">Archive</span></button>
                    <a href="{{ route('desk-officer.referrals.print', $referral->id) }}" class="print-referral-btn inline-flex min-h-12 items-center justify-center rounded-lg bg-[#C41E3A] text-white" title="Print"><i class="fas fa-print"></i><span class="sr-only">Print</span></a>
                </div>
            </article>
            @empty
            <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-gray-500">No referrals found.</div>
            @endforelse
        </div>

        @if($referrals->hasPages())
        @include('components.archived-pagination', ['paginator' => $referrals, 'label' => 'referrals'])
        @endif
        @if($isMultiroleDeskOfficer)
        </section>
        @endif
        @endforeach
    </div>
    </div>
</div>

<!-- Referral Details Modal -->
<div class="modal fade" id="referralDetailsModal" tabindex="-1" aria-labelledby="referralDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 720px;">
        <div class="modal-content">
            <div class="modal-header bg-[#C41E3A] text-white" style="background-color: var(--modal-brand, #B11116) !important; color: white !important;">
                <h5 class="modal-title text-white flex items-center gap-2" id="referralDetailsModalLabel">
                    <i id="deskReferralModalIcon" class="fas fa-file-alt"></i>
                    <span id="deskReferralModalHeading">Referral Details</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) brightness(200%);"></button>
            </div>
            <div class="modal-body" id="referralDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div>Loading...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneScreenWidth = Math.min(window.innerWidth || Infinity, window.screen?.width || Infinity);
    const isPhoneReferralView = window.matchMedia('(max-width: 47.9375rem)').matches
        || (/Android|iPhone|iPod/i.test(navigator.userAgent) && phoneScreenWidth <= 480);
    document.querySelectorAll('.referrals-table-view').forEach(view => {
        view.style.display = isPhoneReferralView ? 'none' : '';
    });
    document.querySelectorAll('.referral-mobile-cards').forEach(cards => {
        cards.style.display = isPhoneReferralView ? 'grid' : '';
    });
    const referralsSearchInput = document.getElementById('referrals-search-input');
    referralsSearchInput?.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        document.querySelectorAll('.referral-searchable').forEach(item => {
            item.style.display = !query || item.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    });
    const modal = new bootstrap.Modal(document.getElementById('referralDetailsModal'));
    const directReferralPopups = @json($isMultiroleDeskOfficer || $isBcpcDeskOfficer || $isSwdDeskOfficer);
    let referralPopupRequest = 0;
    document.querySelectorAll('.view-referral-btn').forEach(function(btn) {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            const referralId = this.getAttribute('data-id');
            const content = document.getElementById('referralDetailsContent');
            const requestId = directReferralPopups ? ++referralPopupRequest : null;
            if (!directReferralPopups) {
            content.innerHTML = `<div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div>Loading...</div>
            </div>`;
            modal.show();
            }
            const viewHeading = document.getElementById('deskReferralModalHeading');
            const viewIcon = document.getElementById('deskReferralModalIcon');
            if (viewHeading) viewHeading.textContent = 'Referral Details';
            if (viewIcon) viewIcon.className = 'fas fa-file-alt';
            fetch(`/desk-officer/referrals/${referralId}/details`, directReferralPopups ? { headers: { 'X-Skip-Loading': '1' } } : undefined)
                .then(response => {
                    if (directReferralPopups && (!response.ok || response.redirected)) throw new Error('Unable to load referral details.');
                    return response.text();
                })
                .then(html => {
                    if (directReferralPopups && requestId !== referralPopupRequest) return;
                    content.innerHTML = html;
                    if (directReferralPopups) {
                        if (viewHeading) viewHeading.textContent = 'Referral Details';
                        if (viewIcon) viewIcon.className = 'fas fa-file-alt';
                        modal.show();
                    }
                })
                .catch(() => {
                    if (directReferralPopups && requestId !== referralPopupRequest) return;
                    content.innerHTML = '<div class="alert alert-danger">Failed to load details.</div>';
                    if (directReferralPopups) modal.show();
                });
        });
    });
    // Edit button: load edit form into the same modal
    document.querySelectorAll('.edit-referral-btn').forEach(function(btn) {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            const referralId = this.getAttribute('data-id');
            const content = document.getElementById('referralDetailsContent');
            const requestId = directReferralPopups ? ++referralPopupRequest : null;
            if (!directReferralPopups) {
            content.innerHTML = `<div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div>Loading...</div>
            </div>`;
            modal.show();
            }
            fetch(`/desk-officer/referrals/${referralId}/edit`, directReferralPopups ? { headers: { 'X-Skip-Loading': '1' } } : undefined)
                .then(response => {
                    if (directReferralPopups && (!response.ok || response.redirected)) throw new Error('Unable to load edit form.');
                    return response.text();
                })
                .then(html => {
                    if (directReferralPopups && requestId !== referralPopupRequest) return;
                    content.innerHTML = html;
                    // update modal title
                    const editHeading = document.getElementById('deskReferralModalHeading');
                    const editIcon = document.getElementById('deskReferralModalIcon');
                    if (editHeading) editHeading.textContent = 'Edit Referral';
                    if (editIcon) editIcon.className = 'fas fa-edit';
                    if (directReferralPopups) modal.show();

                    const form = content.querySelector('#referralEditForm');
                    if (!form) return;

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        fetch(`/desk-officer/referrals/${referralId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: new FormData(form)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) throw new Error(data.message || 'Failed to save changes');

                            modal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Referral Updated',
                                text: 'Referral updated successfully.',
                                confirmButtonColor: '#7c3aed'
                            }).then(() => location.reload());
                        })
                        .catch((error) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Unable to Update Referral',
                                text: error.message || 'Failed to save changes.',
                                confirmButtonColor: '#7c3aed'
                            });
                        });
                    });
                })
                .catch(() => {
                    if (directReferralPopups && requestId !== referralPopupRequest) return;
                    content.innerHTML = '<div class="alert alert-danger">Failed to load edit form.</div>';
                    if (directReferralPopups) modal.show();
                });
        });
    });
});

function printTable() {
    window.print();
}

document.addEventListener('click', async function (event) {
    const printButton = event.target.closest('.print-referral-btn');
    const downloadButton = event.target.closest('.download-referrals-btn');
    if (!printButton && !downloadButton) return;

    event.preventDefault();
    if (downloadButton && @json(($isSwdDeskOfficer || $isBcpcDeskOfficer) && ($stats['total'] ?? 0) === 0)) {
        await Swal.fire({
            icon: 'info',
            title: 'No Referrals to Print',
            text: 'There are no referrals to print.',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }
    const link = printButton || downloadButton;
    let department = '';
    @if(auth()->user()->isMultiCaseDepartment())
    if (downloadButton) {
        const choice = await Swal.fire({ title: 'Choose department to print', input: 'select', inputOptions: { BCPC: 'BCPC', VAWC: 'SWD' }, inputPlaceholder: 'Select department', showCancelButton: true, confirmButtonText: 'Preview PDF', inputValidator: value => !value ? 'Choose a department.' : undefined });
        if (!choice.isConfirmed) return;
        department = choice.value;
        const referralCounts = @json(collect($referralGroups)->map(fn ($group) => $group->total()));
        if (!referralCounts[department]) {
            await Swal.fire({ icon: 'info', title: 'No Referrals to Print', text: 'There are no referrals to print.' });
            return;
        }
        window.open(link.href + '?preview=1&type=' + encodeURIComponent(department), '_blank', 'noopener');
        return;
    }
    @endif
    const result = await Swal.fire({
        title: downloadButton ? 'Print all referrals?' : 'Open referral printout?',
        text: downloadButton ? 'This will download ALL referrals into one PDF file.' : 'This will open the referral printout in a new tab.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: downloadButton ? 'Preview PDF' : 'Open printout',
        confirmButtonColor: '#7c3aed'
    });
    if (result.isConfirmed) {
        if (printButton) window.open(link.href, '_blank', 'noopener');
        else window.open(link.href + (link.href.includes('?') ? '&' : '?') + 'preview=1', '_blank', 'noopener');
    }
});
</script>
@endpush

<style>
    .actions-column { white-space: nowrap; }
    #referralDetailsModal .modal-content { max-height: 82vh; }
    #referralDetailsModal .modal-body { max-height: calc(82vh - 64px); overflow-y: auto; }

    /* Center both existing Referral View and Edit content on Android tablets. */
    @media (min-width: 37.5rem) and (max-width: 80rem) {
        #referralDetailsModal.modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        #referralDetailsModal .modal-dialog {
            width: min(100%, 45rem);
            height: min(88dvh, 44rem);
            margin: 0 !important;
        }
        #referralDetailsModal .modal-content {
            display: flex;
            flex-direction: column;
            height: 100%;
            max-height: none;
        }
        #referralDetailsModal .modal-header { flex: 0 0 auto; }
        #referralDetailsModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none;
            overflow-y: auto;
            overscroll-behavior: contain;
        }
    }

    @media print {
        @page {
            size: landscape;
            margin: 1cm;
        }

        body * {
            visibility: hidden;
        }
        .printable-referrals-table, .printable-referrals-table * {
            visibility: visible;
        }
        .printable-referrals-table {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .printable-referrals-table .actions-column {
            display: none;
        }

        .table {
            font-size: 10px;
        }

        .table th, .table td {
            padding: 4px;
        }
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('click', async event => {
    const button = event.target.closest('.archive-referral-btn');
    if (!button) return;
    event.preventDefault();
    event.stopPropagation();
    const confirmation = await Swal.fire({
        title: 'Archive Referral?',
        text: @json(($isSwdDeskOfficer || $isBcpcDeskOfficer) ? 'Only this referral will be archived. The recipient will remain available.' : 'Only this referral will be archived. Recipient info stays active while other referrals remain.'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Archive',
        confirmButtonColor: '#ef4444'
    });
    if (!confirmation.isConfirmed) return;
    try {
        const response = await fetch(button.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': @json(csrf_token()),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        let data;
        try {
            data = await response.json();
        } catch (e) {
            throw new Error('Server returned an invalid response. Please try again.');
        }
        if (!response.ok || !data.success) throw new Error(data.message || 'Unable to archive referral.');
        await Swal.fire({
            icon: 'success',
            title: 'Archived',
            text: data.message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#7c3aed'
        });
        location.reload();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'An unexpected error occurred.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#7c3aed'
        });
    }
});
</script>
@endpush
