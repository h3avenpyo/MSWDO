@extends('layouts.desk-officer')

@section('title', 'Calendar')

@php
    $isMultiroleDeskOfficer = auth()->user()?->position === 'Desk Officer'
        && auth()->user()?->isMultiCaseDepartment();
    $isBcpcDeskOfficer = auth()->user()?->position === 'Desk Officer'
        && auth()->user()?->defaultCaseDepartment() === 'BCPC'
        && !$isMultiroleDeskOfficer;
    $isSwdDeskOfficer = auth()->user()?->position === 'Desk Officer'
        && auth()->user()?->defaultCaseDepartment() === 'VAWC'
        && !$isMultiroleDeskOfficer;
@endphp

@push('styles')
<style>
    /* Dashboard-style red navbar for mobile/tablet, matching /admin-dashboard */
    @media (max-width: 80rem) {
        html body.portal-ui.desk-officer-calendar-mobile-nav #mobileTopBar {
            display: none !important;
        }
    }

    @media (max-width: 47.9375rem) {
        html body.portal-ui.desk-officer-calendar-mobile-nav #mobileTopBar {
            display: none !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebarCloseBtn,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header > div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:first-of-type,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header > div > div:first-of-type {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:first-of-type > div,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header > div > div:first-of-type > div {
            min-width: 0 !important;
            flex: 1 1 auto !important;
            text-align: left !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
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
        html body.portal-ui.desk-officer-calendar-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) i {
            color: inherit !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header p {
            display: block !important;
            margin-top: .125rem !important;
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p :is(span, strong),
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header p :is(span, strong) {
            color: #ffffff !important;
            opacity: 1 !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) div:first-of-type :is(span, a),
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div > div:last-child,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header > div > div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header button[aria-label="Notifications"],
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header button[aria-label="Notifications"] > i,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header #notifications-unread-count,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header #notifications-unread-count {
            width: 1.25rem !important;
            height: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
        }

        /* Sidebar dropdown drawer below header */
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar {
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
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar nav :is(a, button),
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar nav :is(a, button):hover,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600, .bg-purple-700\/70),
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }
    }

    @media (max-width: 40rem) {
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header button:has(> img[alt="User Profile"]) {
            display: none !important;
        }
    }

    /* Calendar tablet navigation (768px–1280px / 48rem–80rem): collapsed sidebar on the left without button, standard clean header */
    @media (min-width: 48rem) and (max-width: 80rem) {
        html body.portal-ui.desk-officer-calendar-mobile-nav #mobileTopBar,
        body.portal-ui.desk-officer-calendar-mobile-nav #mobileTopBar,
        body.desk-officer-calendar-mobile-nav #mobileTopBar,
        .desk-officer-calendar-mobile-nav #mobileTopBar,
        #mobileTopBar {
            display: none !important;
        }

        html body.portal-ui.desk-officer-calendar-mobile-nav #mobileMenuToggle,
        body.desk-officer-calendar-mobile-nav #mobileMenuToggle,
        #mobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-calendar-mobile-nav #dashboardMenuToggle,
        body.desk-officer-calendar-mobile-nav #dashboardMenuToggle,
        #dashboardMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-calendar-mobile-nav #pageMobileMenuToggle,
        body.desk-officer-calendar-mobile-nav #pageMobileMenuToggle,
        #pageMobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebarCloseBtn,
        body.desk-officer-calendar-mobile-nav #sidebarCloseBtn,
        #sidebarCloseBtn {
            display: none !important;
        }

        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebarOverlay,
        body.desk-officer-calendar-mobile-nav #sidebarOverlay,
        #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebarToggleBtn,
        body.portal-ui.desk-officer-calendar-mobile-nav #sidebarToggleBtn,
        body.desk-officer-calendar-mobile-nav #sidebarToggleBtn,
        .desk-officer-calendar-mobile-nav #sidebarToggleBtn,
        #sidebarToggleBtn {
            display: none !important;
        }

        /* Clean standard header on tablet */
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header,
        body.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header,
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
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) > div,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header > div,
        #desk-officer-dashboard-header > div {
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3),
        #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #1f2937 !important;
            font-size: 1.5rem !important;
            line-height: 1.25 !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header p,
        #desk-officer-dashboard-header p {
            color: #6b7280 !important;
            font-size: 0.875rem !important;
            display: block !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a),
        #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #4b5563 !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header button[aria-label="Notifications"],
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"],
        #desk-officer-dashboard-header button[aria-label="Notifications"] {
            border: 1px solid #e5e7eb !important;
            background: transparent !important;
            color: #4b5563 !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent > header button[aria-label="Notifications"] > i,
        html body.portal-ui.desk-officer-calendar-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] > i,
        #desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            color: #4b5563 !important;
        }

        /* Sidebar: collapsed icon rail on the left */
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar,
        body.portal-ui.desk-officer-calendar-mobile-nav #sidebar,
        body.desk-officer-calendar-mobile-nav #sidebar,
        .desk-officer-calendar-mobile-nav #sidebar,
        .portal-ui.desk-officer-calendar-mobile-nav #sidebar {
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
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-header,
        body.desk-officer-calendar-mobile-nav #sidebar .sidebar-header,
        .desk-officer-calendar-mobile-nav #sidebar .sidebar-header {
            display: flex !important;
            min-height: 5.5rem;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-header img,
        body.desk-officer-calendar-mobile-nav #sidebar .sidebar-header img,
        .desk-officer-calendar-mobile-nav #sidebar .sidebar-header img {
            width: 3rem !important;
            height: 3rem !important;
            flex-shrink: 0 !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-header .sidebar-text,
        body.desk-officer-calendar-mobile-nav #sidebar .sidebar-header .sidebar-text,
        .desk-officer-calendar-mobile-nav #sidebar .sidebar-header .sidebar-text {
            display: none !important;
            opacity: 0 !important;
        }

        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init,
        body.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed,
        body.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-text,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed .sidebar-text,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-text,
        body.desk-officer-calendar-mobile-nav #sidebar .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-menu,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed .sidebar-menu,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-menu,
        body.desk-officer-calendar-mobile-nav #sidebar .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-link,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed .sidebar-link,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-link,
        body.desk-officer-calendar-mobile-nav #sidebar .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar .sidebar-link i,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed .sidebar-link i,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-link i,
        body.desk-officer-calendar-mobile-nav #sidebar .sidebar-link i {
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            text-align: center !important;
        }

        /* Main Content offset by collapsed sidebar */
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar ~ #mainContent,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed ~ #mainContent,
        html body.portal-ui.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init ~ #mainContent,
        html body.portal-ui.desk-officer-calendar-mobile-nav #mainContent,
        body.desk-officer-calendar-mobile-nav #sidebar ~ #mainContent,
        body.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed ~ #mainContent,
        body.desk-officer-calendar-mobile-nav #sidebar.sidebar-collapsed-init ~ #mainContent,
        body.desk-officer-calendar-mobile-nav #mainContent,
        .desk-officer-calendar-mobile-nav #mainContent,
        .portal-ui.desk-officer-calendar-mobile-nav #mainContent {
            width: calc(100% - 4.5rem) !important;
            margin-left: 4.5rem !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            min-width: 0 !important;
            height: auto !important;
            min-height: 100vh;
            overflow: visible !important;
        }

        /* Calendar tablet content adjustments */
        body.desk-officer-calendar-mobile-nav #mainContent main {
            padding: 1.25rem 1.5rem !important;
        }
        body.desk-officer-calendar-mobile-nav .bcpc-admin-calendar {
            max-width: 100% !important;
            padding: 1.5rem !important;
        }
        body.desk-officer-calendar-mobile-nav .bcpc-admin-calendar .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Calendar', 'subtitle' => 'View scheduled activities'])

<!-- Top Header -->
@unless($isBcpcDeskOfficer || $isSwdDeskOfficer || (auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment()))
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Calendar</h2>
            <p class="text-sm text-gray-500 hidden sm:block">View and manage events</p>
        </div>
        @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' && !auth()->user()?->isMultiCaseDepartment())
        <div class="flex items-center gap-4">
            @include('desk-officer.components.notifications')
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</header>
@endunless

<!-- Main Content -->
<main class="px-4 pt-4 pb-4 sm:p-6 lg:p-8">
    @if($isMultiroleDeskOfficer)
    <div class="calendar-department-toggle mb-4 flex flex-wrap gap-2">
        <button type="button" data-calendar-department="BCPC" class="calendar-department-button inline-flex items-center gap-2 rounded-lg bg-[#C41E3A] px-4 py-2 text-sm font-medium text-white">
            <i class="fas fa-child"></i> BCPC Calendar
        </button>
        <button type="button" data-calendar-department="VAWC" class="calendar-department-button inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700">
            <i class="fas fa-shield-alt"></i> SWD Calendar
        </button>
    </div>
    @endif
    <!-- Calendar Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 {{ true ? 'bcpc-admin-calendar' : '' }}">
        @if(true)
        <div id="calendar-list-filter" class="hidden mb-4">
            <label for="calendar-list-range" class="block text-sm font-medium text-gray-700 mb-2">Date range</label>
            <select id="calendar-list-range" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                <option value="all">Whole month</option>
                <option value="3">Days 1–3</option>
                <option value="7">Days 1–7</option>
                <option value="15">Days 1–15</option>
            </select>
        </div>
        @endif
        <div id="calendar"></div>
        @if(true)
        <div id="calendar-list-pagination" class="hidden mt-4 flex items-center justify-between gap-3 text-sm">
            <span id="calendar-list-page-info" aria-live="polite"></span>
            <div class="flex gap-2">
                <button type="button" id="calendar-list-prev" class="px-3 py-2 border border-gray-300 rounded-lg text-purple-700 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed">Previous</button>
                <button type="button" id="calendar-list-next" class="px-3 py-2 border border-gray-300 rounded-lg text-purple-700 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed">Next</button>
            </div>
        </div>
        @endif
    </div>
    <div class="flex justify-center mt-6">
        <button type="button" onclick="openEventModal()"
           class="inline-flex w-full sm:w-80 items-center justify-center gap-2 bg-purple-700 hover:bg-purple-800 text-white px-5 py-3 rounded-lg font-medium transition shadow-md">
            <i class="fas fa-plus-circle"></i> Add Event
        </button>
    </div>
</main>

<style>
    @if($isMultiroleDeskOfficer)
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        .calendar-department-toggle {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
            width: 100%;
        }
        .calendar-department-toggle .calendar-department-button {
            width: 100%;
            min-width: 0;
            min-height: 2.5rem;
            justify-content: center;
            gap: .375rem;
            padding: .5rem;
            white-space: nowrap;
        }
    }
    @endif
    .fc {
        font-family: 'Raleway', 'Montserrat', sans-serif;
    }
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
    }
    .fc .fc-button {
        background-color: #7c3aed;
        border-color: #7c3aed;
        color: white;
        font-family: 'Raleway', 'Montserrat', sans-serif;
        font-weight: 500;
    }
    .fc .fc-button:hover {
        background-color: #6d28d9;
        border-color: #6d28d9;
    }
    .fc .fc-button:active,
    .fc .fc-button.fc-button-active {
        background-color: #5b21b6;
        border-color: #5b21b6;
    }
    .fc .fc-col-header-cell-cushion {
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .fc .fc-daygrid-day-number {
        color: #374151;
        font-weight: 500;
    }
    .fc .fc-day-today {
        background-color: #f3e8ff !important;
    }
    .fc .fc-event {
        border: none;
        border-radius: 0.375rem;
        font-size: 0.8rem;
        box-sizing: border-box;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        padding: 0.125rem 0.25rem;
        transition: filter 120ms ease, box-shadow 120ms ease;
    }
    .fc .fc-daygrid-day-events,
    .fc .fc-daygrid-event-harness {
        min-width: 0;
        max-width: 100%;
    }
    .fc .fc-daygrid-day-events {
        width: 100%;
        box-sizing: border-box;
        overflow-x: clip;
        overflow-y: visible;
    }
    .fc .fc-daygrid-event-harness { padding-inline: 0.125rem; }
    .fc .fc-daygrid-event { width: calc(100% - 0.25rem); }
    .fc .fc-daygrid-event .fc-event-main,
    .fc .fc-daygrid-event .fc-event-main-frame,
    .fc .fc-daygrid-event .fc-event-title-container,
    .fc .fc-daygrid-event .fc-event-title {
        display: block;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .fc .fc-event:hover,
    .fc .fc-event:focus-visible {
        filter: brightness(0.97);
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
    }
    .fc .calendar-event-count {
        clear: both;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
        padding-inline: 0.125rem;
    }
    .fc .calendar-event-count button {
        width: auto;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
        white-space: normal;
        overflow-wrap: anywhere;
        text-align: center;
    }
    @if(true)
    .bcpc-admin-calendar {
        max-width: 960px;
        margin-inline: auto;
        padding: 20px;
        border-color: #e5e7eb;
    }
    .bcpc-admin-calendar .fc,
    .bcpc-admin-calendar .fc .fc-button { font-family: inherit; }
    .bcpc-admin-calendar .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 700; }
    .bcpc-admin-calendar .fc .fc-button {
        background-color: #7e22ce;
        border-color: #7e22ce;
        font-size: .875rem;
        border-radius: .5rem;
        padding: .4rem .7rem;
    }
    .bcpc-admin-calendar .fc .fc-button:hover,
    .bcpc-admin-calendar .fc .fc-button.fc-button-active {
        background-color: #6b21a8;
        border-color: #6b21a8;
    }
    .bcpc-admin-calendar .fc { --fc-border-color: #e5e7eb; }
    .bcpc-admin-calendar .fc .fc-col-header-cell { background: #faf5ff; }
    @media (max-width: 640px) {
        .bcpc-admin-calendar { padding: 12px; }
        .bcpc-admin-calendar .fc .fc-toolbar { flex-wrap: wrap; justify-content: center; gap: .75rem; }
    }
    @media (max-width: 37.4375rem) {
        .bcpc-admin-calendar .fc .fc-header-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-rows: auto auto;
            align-items: center;
            column-gap: .75rem;
            row-gap: .875rem;
            margin-bottom: 1rem;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > .fc-toolbar-chunk:nth-child(1) {
            grid-column: 1;
            grid-row: 1;
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .5rem;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > .fc-toolbar-chunk:nth-child(3) {
            grid-column: 2;
            grid-row: 1;
            display: flex;
            align-items: center;
            justify-self: end;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > .fc-toolbar-chunk:nth-child(3) .fc-button-group {
            display: flex;
            gap: .5rem;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > .fc-toolbar-chunk:nth-child(3) .fc-button {
            border-radius: .5rem !important;
        }
        .bcpc-admin-calendar .fc .fc-header-toolbar > .fc-toolbar-chunk:nth-child(2) {
            grid-column: 1 / -1;
            grid-row: 2;
            justify-self: center;
            text-align: center;
        }
        .bcpc-admin-calendar .fc .fc-toolbar-title { font-size: 1.125rem; }
    }
    @endif

    /* Scroll the modal card itself so its form and actions remain reachable on short screens. */
    #eventModalDialog {
        height: 100vh;
        height: 100dvh;
        min-height: 0;
        padding: 1rem;
        overflow: hidden;
    }
    #eventModalContent {
        width: min(100%, 34rem);
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }
    #dateEventsModalContent,
    #eventDetailsModalContent {
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }

    /* Keep the existing tablet modal width and spacing. */
    @media (min-width: 37.5rem) and (max-width: 80rem) {
        #eventModalContent { width: min(100%, 34rem); }
    }
    @media (min-width: 64rem) and (max-width: 80rem) {
        #eventModalDialog { padding: 1.5rem; }
        #eventModalContent {
            width: min(100%, 42rem);
            max-height: min(46rem, calc(100dvh - 3rem));
            border-radius: 1rem;
        }
    }
</style>

<!-- Date Events Modal -->
<div id="dateEventsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDateEventsModal()"></div>
    <div class="flex min-h-full items-center justify-center overflow-y-auto p-4">
        <div id="dateEventsModalContent" class="relative bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4" id="dateEventsTitle">Events on Date</h2>
            <div id="dateEventsList" class="space-y-3 mb-6"></div>
            <div class="flex justify-end">
                <button type="button" onclick="closeDateEventsModal()" 
                        class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div id="eventModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEventModal()"></div>
    <div id="eventModalDialog" class="flex items-center justify-center overflow-y-auto p-4">
        <div id="eventModalContent" class="relative bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Add New Event</h2>
            
            <form id="eventForm" onsubmit="submitEvent(event)">
                @csrf
                <input type="hidden" name="event_date" id="event_date">
                <div id="eventFormFields">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" id="location" name="location"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="event_date_input" class="block text-sm font-medium text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="event_date_input" name="event_date_input" required oninput="validateEventEndTime()"
                           min="{{ date('Y-m-d\TH:i') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div class="mb-4">
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                    <input type="datetime-local" id="end_time" name="end_time" oninput="validateEventEndTime()" aria-describedby="eventEndTimeHint"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <p id="eventEndTimeHint" class="mt-1 text-xs text-gray-500">Optional. Choose a date and time after the event starts.</p>
                </div>
                
                <div class="mb-4">
                    <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">Target Audience <span class="text-red-500">*</span></label>
                    <select id="target_audience" name="target_audience" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="all">All Users</option>
                        <option value="admins_only">Administrators Only</option>
                        <option value="bcpc_only">BCPC Only</option>
                        <option value="vawc_only">SWD Only</option>
                    </select>
                </div>
                
                </div>
                <div id="eventFormActions" class="flex gap-3 justify-end">
                    <button type="button" onclick="closeEventModal()" 
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition">
                        Save Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Conflict Modal -->
<div id="conflictModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeConflictModal()"></div>
    <div class="flex min-h-full items-center justify-center overflow-y-auto p-4">
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                </div>
            </div>
            <h2 class="text-xl font-bold text-gray-800 text-center mb-2">Event Conflict</h2>
            <p class="text-gray-500 text-sm text-center mb-6">
                There is already an event on this date. Do you want to continue adding this event?
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeConflictModal()" 
                        class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="button" onclick="submitEventWithConflict()" 
                        class="flex-1 px-4 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventDetailsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEventDetailsModal()"></div>
    <div class="flex min-h-full items-center justify-center overflow-y-auto p-4">
        <div id="eventDetailsModalContent" class="relative bg-white rounded-xl shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
            <h2 id="eventTitle" class="text-xl font-bold text-gray-800 mb-4"></h2>
            <div id="eventDetails" class="space-y-3 mb-6"></div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeEventDetailsModal()" 
                        class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Close
                </button>
                <button type="button" id="deleteEventBtn" onclick="deleteEvent()"
                        class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet'>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<script>
let calendar;
let currentEventId = null;
let pendingEventData = null;
let allEvents = [];
let activeCalendarEvents = [];
let calendarEventsByDate = new Map();

document.addEventListener('DOMContentLoaded', function() {
    const events = @json($events);
    allEvents = events;
    
    // Format events for FullCalendar
    const formattedEvents = events.map(function(event) {
        let colors = { background: '#ecfdf5', border: '#a7f3d0', text: '#065f46' };
        if (event.target_audience === 'admins_only') {
            colors = { background: '#fef2f2', border: '#fecaca', text: '#991b1b' };
        } else if (event.target_audience === 'bcpc_only') {
            colors = { background: '#f5f3ff', border: '#ddd6fe', text: '#5b21b6' };
        } else if (event.target_audience === 'vawc_only') {
            colors = { background: '#fffbeb', border: '#fde68a', text: '#92400e' };
        }
        return {
            id: event.id,
            title: event.title,
            start: event.calendar_start || event.event_date,
            backgroundColor: colors.background,
            borderColor: colors.border,
            textColor: colors.text,
            extendedProps: {
                targetAudience: event.target_audience,
            calendarDate: event.calendar_date || eventDateKey(event.event_date)
            }
        };
    });
    setActiveCalendarEvents(formattedEvents);

    const calendarEl = document.getElementById('calendar');
    @if(true)
    const listRange = document.getElementById('calendar-list-range');
    let listPage = 1;
    function applyListDateRange(view) {
        const isList = view.type === 'listMonth';
        document.getElementById('calendar-list-filter').classList.toggle('hidden', !isList);
        document.getElementById('calendar-list-pagination').classList.toggle('hidden', !isList);
        const month = new Intl.DateTimeFormat('en', { month: 'short' }).format(view.currentStart);
        Array.from(listRange.options).forEach(option => {
            option.textContent = option.value === 'all' ? 'Whole month' : `${month} 1–${option.value}`;
        });
        const matchingEvents = calendar.getEvents().filter(event =>
            event.start >= view.currentStart && event.start < view.currentEnd
            && (listRange.value === 'all' || Number(event.startStr.slice(8, 10)) <= Number(listRange.value))
        ).sort((a, b) => a.start - b.start || a.title.localeCompare(b.title) || String(a.id).localeCompare(String(b.id)));
        const pageCount = Math.max(1, Math.ceil(matchingEvents.length / 5));
        listPage = Math.min(listPage, pageCount);
        const pageIds = new Set(matchingEvents.slice((listPage - 1) * 5, listPage * 5).map(event => event.id));
        document.getElementById('calendar-list-page-info').textContent = `Page ${listPage} of ${pageCount} · ${matchingEvents.length} events`;
        document.getElementById('calendar-list-prev').disabled = listPage === 1;
        document.getElementById('calendar-list-next').disabled = listPage === pageCount;
        calendar.batchRendering(function() {
            calendar.setOption('height', isList ? 'auto' : 560);
            calendar.getEvents().forEach(event => {
                const dateEvents = getCalendarDateEvents(event.extendedProps.calendarDate);
                const isGroupedMonthEvent = view.type === 'dayGridMonth' && dateEvents.length >= 2;
                const isOutsideListPage = isList && !pageIds.has(event.id);
                event.setProp('display', isGroupedMonthEvent || isOutsideListPage ? 'none' : 'auto');
            });
        });
    }
    listRange.addEventListener('change', function() { listPage = 1; applyListDateRange(calendar.view); });
    document.getElementById('calendar-list-prev').addEventListener('click', function() { listPage--; applyListDateRange(calendar.view); });
    document.getElementById('calendar-list-next').addEventListener('click', function() { listPage++; applyListDateRange(calendar.view); });
    @endif
    calendar = new FullCalendar.Calendar(calendarEl, {
        @if(true)
        height: 560,
        datesSet: function(info) {
            listPage = 1;
            applyListDateRange(info.view);
        },
        @endif
        initialView: 'dayGridMonth',
        timeZone: 'Asia/Manila',
        customButtons: {
            month: {
                text: 'Month',
                click: function() {
                    calendar.changeView('dayGridMonth');
                }
            },
            list: {
                text: 'List',
                click: function() {
                    calendar.changeView('listMonth');
                }
            }
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'month list'
        },
        events: activeCalendarEvents,
        @if(true)
        dayCellDidMount: function(info) {
            if (info.view.type !== 'dayGridMonth') return;
            const date = info.el.dataset.date || info.date.toISOString().slice(0, 10);
            renderDateEventCount(info.el, date);
        },
        eventDidMount: function(info) {
            if (info.view.type === 'dayGridMonth') return;
            if (info.view.type !== 'listMonth') return;
            info.el.style.cursor = 'pointer';
            info.el.tabIndex = 0;
            info.el.setAttribute('role', 'button');
            info.el.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    showEventDetails(info.event.id);
                }
            });
            const today = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date());
            if (info.event.startStr.slice(0, 10) < today) {
                const badge = document.createElement('span');
                badge.textContent = 'Done';
                badge.className = 'ml-2 inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700';
                info.el.querySelector('.fc-list-event-title')?.appendChild(badge);
            }
        },
        @endif
        eventClick: function(info) {
            @if(true)
            if (info.view.type === 'listMonth') {
                showEventDetails(info.event.id);
                return;
            }
            @endif
            const eventDate = info.event.extendedProps.calendarDate;
            const dateEvents = getCalendarDateEvents(eventDate);
            if (dateEvents.length === 1) {
                showEventDetails(info.event.id);
                return;
            }
            showEventsOnDate(eventDate);
        },
        dateClick: function(info) {
            showEventsOnDate(info.dateStr);
        }
    });
    calendar.render();
    const notificationEventId = Number(new URLSearchParams(window.location.search).get('event'));
    if (Number.isInteger(notificationEventId) && notificationEventId > 0) {
        showEventDetails(notificationEventId);
    }
    @if($isMultiroleDeskOfficer)
    const calendarDepartmentButtons = document.querySelectorAll('.calendar-department-button');
    const applyCalendarDepartment = (department) => {
        const audience = department === 'BCPC' ? 'bcpc_only' : 'vawc_only';
        document.querySelectorAll('.calendar-event-count').forEach(element => element.remove());
        calendar.removeAllEvents();
        setActiveCalendarEvents(formattedEvents.filter(event => ['all', audience].includes(event.extendedProps.targetAudience)));
        calendar.addEventSource(activeCalendarEvents);
        calendarDepartmentButtons.forEach(button => {
            const active = button.dataset.calendarDepartment === department;
            button.className = 'calendar-department-button inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium ' + (active
                ? 'bg-[#C41E3A] text-white'
                : 'border border-gray-300 bg-white text-gray-700');
        });
        applyListDateRange(calendar.view);
        refreshDateEventCounts();
    };
    calendarDepartmentButtons.forEach(button => button.addEventListener('click', () => applyCalendarDepartment(button.dataset.calendarDepartment)));
    applyCalendarDepartment('BCPC');
    @endif
    @if(true)
    applyListDateRange(calendar.view);
    @endif
});

function showEventsOnDate(dateStr) {
    const formatEventDate = (value) => {
        if (!value) return '';

        const date = new Date(value);
        return Number.isNaN(date.getTime())
            ? value
            : date.toLocaleDateString('en-PH', {
                year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Manila'
            });
    };

    const rawEventsById = new Map(allEvents.map(event => [String(event.id), event]));
    const eventsOnDate = getCalendarDateEvents(dateStr)
        .map(event => rawEventsById.get(String(event.id)))
        .filter(Boolean);
    
    if (eventsOnDate.length === 0) {
        openEventModal(dateStr);
        return;
    }
    
    const dateEventsList = document.getElementById('dateEventsList');
    dateEventsList.innerHTML = '';
    
    eventsOnDate.forEach(function(event) {
        let color = '#059669';
        if (event.target_audience === 'admins_only') {
            color = '#dc2626';
        } else if (event.target_audience === 'bcpc_only') {
            color = '#7c3aed';
        } else if (event.target_audience === 'vawc_only') {
            color = '#f59e0b';
        }
        
        const eventItem = document.createElement('div');
        eventItem.className = 'p-4 rounded-lg border cursor-pointer hover:bg-gray-50 transition';
        eventItem.style.borderLeft = '4px solid ' + color;
        const startDate = formatEventDate(event.event_date);
        const endDate = formatEventDate(event.end_time);
        const title = document.createElement('h3');
        title.className = 'font-semibold text-gray-800';
        title.textContent = event.title;
        const date = document.createElement('p');
        date.className = 'text-sm text-gray-500';
        date.textContent = endDate ? `${startDate} - ${endDate}` : startDate;
        eventItem.append(title, date);
        eventItem.onclick = function() {
            closeDateEventsModal();
            showEventDetails(event.id);
        };
        dateEventsList.appendChild(eventItem);
    });
    
    const dateEventsTitle = document.getElementById('dateEventsTitle');
    dateEventsTitle.textContent = 'Events on ' + dateStr;
    
    document.getElementById('dateEventsModal').classList.remove('hidden');
}

function getCalendarDateEvents(dateStr) {
    return calendarEventsByDate.get(dateStr) || [];
}

function setActiveCalendarEvents(events) {
    const uniqueEvents = new Map();
    const groupedEvents = new Map();
    events.forEach(event => {
        const id = String(event.id);
        if (uniqueEvents.has(id)) return;
        const date = event.extendedProps?.calendarDate || eventDateKey(event.start);
        if (!date) return;
        const normalizedEvent = {
            ...event,
            extendedProps: { ...event.extendedProps, calendarDate: date }
        };
        uniqueEvents.set(id, normalizedEvent);
        if (!groupedEvents.has(date)) groupedEvents.set(date, []);
        groupedEvents.get(date).push(normalizedEvent);
    });
    activeCalendarEvents = Array.from(uniqueEvents.values()).map(event => ({
        ...event,
        display: groupedEvents.get(event.extendedProps.calendarDate).length >= 2 ? 'none' : 'auto'
    }));
    calendarEventsByDate = groupedEvents;
}

function refreshDateEventCounts() {
    if (!calendar || calendar.view.type !== 'dayGridMonth') return;
    document.querySelectorAll('#calendar .fc-daygrid-day[data-date]').forEach(dayCell => {
        renderDateEventCount(dayCell, dayCell.dataset.date);
    });
}

function renderDateEventCount(dayCell, dateStr) {
    if (!dayCell || (dayCell.dataset.date && dayCell.dataset.date !== dateStr)) return;
    const eventsContainer = dayCell.querySelector('.fc-daygrid-day-events');
    if (!eventsContainer) return;
    const events = getCalendarDateEvents(dateStr);
    let button = eventsContainer.querySelector(`[data-event-count-date="${dateStr}"]`);
    if (events.length < 2) {
        button?.closest('.calendar-event-count')?.remove();
        return;
    }
    if (!button) {
        const wrapper = document.createElement('div');
        wrapper.className = 'calendar-event-count flex w-full min-w-0 justify-center';
        button = document.createElement('button');
        button.type = 'button';
        button.dataset.eventCountDate = dateStr;
        button.className = 'inline-flex max-w-full items-center justify-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700 hover:bg-purple-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-700';
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            showEventsOnDate(dateStr);
        });
        wrapper.appendChild(button);
        eventsContainer.appendChild(wrapper);
    }
    button.textContent = `${events.length} Events`;
    button.setAttribute('aria-label', `${events.length} Events. View events for ${dateStr}`);
}

function eventDateKey(value) {
    if (!value) return '';
    if (typeof value === 'string') {
        const localDate = value.match(/^(\d{4}-\d{2}-\d{2})(?:$|[T ])/);
        const hasOffset = /(?:Z|[+-]\d{2}(?::?\d{2})?)$/i.test(value);
        if (localDate && !hasOffset) return localDate[1];
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return String(value).slice(0, 10);
    const parts = new Intl.DateTimeFormat('en-CA', {
        timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit'
    }).formatToParts(date).reduce((values, part) => {
        if (part.type !== 'literal') values[part.type] = part.value;
        return values;
    }, {});
    return `${parts.year}-${parts.month}-${parts.day}`;
}

function closeDateEventsModal() {
    document.getElementById('dateEventsModal').classList.add('hidden');
}

function openEventModal(date = null) {
    document.getElementById('eventModal').classList.remove('hidden');
    if (date) {
        document.getElementById('event_date').value = date;
        document.getElementById('event_date_input').value = date;
        validateEventEndTime();
    }
}

function closeEventModal() {
    document.getElementById('eventModal').classList.add('hidden');
    document.getElementById('eventForm').reset();
    validateEventEndTime();
    pendingEventData = null;
}

function toggleVisibilityOptions() {
    const visibility = document.getElementById('visibility').value;
    const container = document.getElementById('visible_to_container');
    if (visibility === 'specific') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}

function validateEventEndTime() {
    const start = document.getElementById('event_date_input');
    const end = document.getElementById('end_time');
    const hint = document.getElementById('eventEndTimeHint');
    if (!start || !end) return;

    const invalid = Boolean(start.value && end.value && end.value <= start.value);
    end.min = start.value || '';
    end.setCustomValidity(invalid ? 'Choose an end date and time later than the event start.' : '');
    if (hint) {
        hint.textContent = invalid
            ? 'Choose an end date and time later than the event start.'
            : 'Optional. Choose a date and time after the event starts.';
        hint.classList.toggle('text-red-600', invalid);
    }
}
function submitEvent(e) {
    e.preventDefault();
    validateEventEndTime();
    if (!document.getElementById('eventForm').reportValidity()) return;
    
    const formData = new FormData(document.getElementById('eventForm'));
    formData.set('event_date', document.getElementById('event_date_input').value);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch('{{ route("desk-officer.calendar.store") }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.has_conflict) {
            pendingEventData = formData;
            document.getElementById('conflictModal').classList.remove('hidden');
        } else if (data.success) {
            closeEventModal();
            location.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Event Not Saved',
                text: data.message || 'Unable to save the event.',
                confirmButtonColor: '#7e22ce',
            });
        }
    })
    .catch(() => Swal.fire({
        icon: 'error',
        title: 'Event Not Saved',
        text: 'Unable to save the event. Please try again.',
        confirmButtonColor: '#7e22ce',
    }));
}

function submitEventWithConflict() {
    if (!pendingEventData) return;
    
    pendingEventData.set('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch('{{ route("desk-officer.calendar.store-with-conflict") }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: pendingEventData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeConflictModal();
            closeEventModal();
            location.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Event Not Saved',
                text: data.message || 'Unable to save the event.',
                confirmButtonColor: '#7e22ce',
            });
        }
    })
    .catch(() => Swal.fire({
        icon: 'error',
        title: 'Event Not Saved',
        text: 'Unable to save the event. Please try again.',
        confirmButtonColor: '#7e22ce',
    }));
}

function closeConflictModal() {
    document.getElementById('conflictModal').classList.add('hidden');
}

function renderEventDetails(data) {
    document.getElementById('eventDetails').parentElement.style.maxHeight = '';
    document.getElementById('eventDetails').parentElement.style.overflowY = '';
    const targetAudienceLabels = {
        bcpc_only: 'BCPC ONLY',
        vawc_only: 'SWD ONLY',
    };
    const formatEventDateTime = (value) => {
        if (!value) return 'Not specified';

        const date = new Date(value);
        return Number.isNaN(date.getTime())
            ? value
            : date.toLocaleString('en-PH', {
                dateStyle: 'medium', timeStyle: 'short', timeZone: 'Asia/Manila'
            });
    };

    document.getElementById('eventTitle').textContent = data.title || 'Event';
    document.getElementById('eventDetails').innerHTML = `
        <p><strong>Description:</strong> ${data.description || 'No description'}</p>
        <p><strong>Location:</strong> ${data.location || 'No location specified'}</p>
        <p><strong>Starts:</strong> ${formatEventDateTime(data.event_date)}</p>
        <p><strong>Ends:</strong> ${formatEventDateTime(data.end_time)}</p>
        <p><strong>Target Audience:</strong> ${targetAudienceLabels[data.target_audience] || data.target_audience || 'Not specified'}</p>
        ${data.creator ? `<p><strong>Created by:</strong> ${data.creator.first_name} ${data.creator.last_name}</p>` : ''}
    `;

    const deleteBtn = document.getElementById('deleteEventBtn');
    const userId = {{ Auth::id() }};
    const isAdmin = {{ strtolower(Auth::user()->position) === 'administrator' ? 'true' : 'false' }};
    deleteBtn.classList.toggle('hidden', data.created_by !== userId && !isAdmin);
    document.getElementById('eventDetailsModal').classList.remove('hidden');
}

function showEventDetails(eventId) {
    const normalizedEventId = Number(eventId);
    if (!Number.isInteger(normalizedEventId) || normalizedEventId <= 0) {
        console.error('Invalid calendar event ID:', eventId);
        return;
    }
    currentEventId = normalizedEventId;

    const loadedEvent = allEvents.find(event => Number(event.id) === normalizedEventId);
    if (loadedEvent) {
        renderEventDetails(loadedEvent);
        return;
    }
    
    const detailUrl = @json(route('desk-officer.calendar.show', ['id' => '__EVENT_ID__']))
        .replace('__EVENT_ID__', encodeURIComponent(normalizedEventId));
    fetch(detailUrl, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch event details');
            }
            return response.json();
        })
        .then(renderEventDetails)
        .catch(error => {
            console.error('Error:', error);
            if (window.Swal) Swal.fire({ icon: 'error', title: 'Unable to Load Event', text: 'Failed to load event details. Please try again.' });
            else alert('Failed to load event details. Please try again.');
        });
}

function closeEventDetailsModal() {
    document.getElementById('eventDetailsModal').classList.add('hidden');
    currentEventId = null;
}

async function deleteEvent() {
    if (!currentEventId) return;
    const eventId = currentEventId;
    
    @if(true)
    const confirmation = await Swal.fire({
        title: 'Delete Event',
        text: 'Are you sure you want to delete this event?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        focusCancel: true,
        reverseButtons: true
    });
    if (!confirmation.isConfirmed) return;
    @else
    const confirmation = await Swal.fire({
        title: 'Delete Event',
        text: 'Are you sure you want to delete this event?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#C41E3A',
        width: 'min(28rem, calc(100vw - 2rem))'
    });
    if (!confirmation.isConfirmed) return;
    @endif
    
    fetch('{{ route("desk-officer.calendar.destroy", ":id") }}'.replace(':id', eventId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEventDetailsModal();
            location.reload();
        } else {
            if (window.Swal) Swal.fire({ icon: 'error', title: 'Unable to Delete Event', text: data.message });
            else alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Unable to Delete Event', text: 'Unable to delete the event. Please try again.' });
    });
}
</script>
@endsection
