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

    /* ── Desk Officer Top Navbar: Mobile & Tablet Overrides ── */
    body.desk-officer-dashboard-mobile-nav #mobileTopBar { display: none !important; }
    body.desk-officer-dashboard-mobile-nav #mobileMenuToggle { display: none !important; }

    /* Mobile view (< 768px): unified crimson top header with drawer toggle */
    @media (max-width: 47.9375rem) {
        html body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 30 !important;
        }
        body.desk-officer-dashboard-mobile-nav #desk-officer-dashboard-header > div > div:first-child {
            min-width: 0;
            flex: 1 1 auto;
            flex-direction: row;
            align-items: center;
            gap: .625rem;
        }
        body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header > div {
            align-items: center;
            padding-top: max(1rem, env(safe-area-inset-top)) !important;
            padding-bottom: 1rem !important;
            padding-left: max(.75rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }
        body.desk-officer-dashboard-mobile-nav #desk-officer-dashboard-header > div > div:first-child > div {
            width: auto;
            flex: 1 1 auto;
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0 !important;
            align-self: center !important;
        }
        body.desk-officer-dashboard-mobile-nav #dashboardMenuToggle {
            display: inline-flex !important;
            flex: 0 0 2.5rem;
            width: 2.5rem;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            align-self: center !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1 !important;
        }
        body.desk-officer-dashboard-mobile-nav #dashboardMenuToggle > i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.25rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            vertical-align: middle !important;
        }
        html body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header > div > div:first-of-type :is(h1, h2, h3, p) {
            color: #ffffff !important;
        }
        body.desk-officer-dashboard-mobile-nav #desk-officer-dashboard-header h2,
        body.desk-officer-dashboard-mobile-nav #desk-officer-dashboard-header p { min-width: 0; }

        /* Notifications bell button matching admin/dashboard */
        html body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"] {
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
        html body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }
        html body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"] > i {
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
        html body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header :is(#notifications-unread-count, [data-unread-badge], .notification-badge) {
            position: absolute !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem !important;
            top: -0.25rem !important;
            right: -0.25rem !important;
            font-size: 0.75rem !important;
            line-height: 1 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 9999px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
            border: 2px solid #C41E3A !important;
        }

        /* User Profile avatar hidden on mobile */
        body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar {
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
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-header { display: none !important; }
        html body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar {
            background: #C41E3A !important;
            background-image: none !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar nav :is(a, button),
        body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }
        body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }
        body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar nav :is(a, button):hover,
        body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }
        body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600, .bg-purple-700\/70),
        body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebarToggleBtn { display: none !important; }
        body.desk-officer-dashboard-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        body.desk-officer-dashboard-mobile-nav #mainContent {
            width: 100% !important;
            min-width: 0 !important;
            margin-left: 0 !important;
        }
        body.portal-ui.desk-officer-dashboard-mobile-nav .notification-dropdown {
            width: min(22rem, calc(100vw - 1.5rem)) !important;
            max-width: calc(100vw - 1.5rem) !important;
            right: 0 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
            border-radius: 0.875rem !important;
            z-index: 1000 !important;
        }
        body.portal-ui.desk-officer-dashboard-mobile-nav .notification-dropdown .bg-gradient-to-r * {
            color: #ffffff !important;
        }
        /* Mobile dashboard fit: allow scrolling so content is never cut off */
        html:has([data-swd-dashboard-fit]), body:has([data-swd-dashboard-fit]) {
            height: auto !important;
            overflow-y: auto !important;
        }
        #mainContent:has([data-swd-dashboard-fit]) {
            height: auto !important;
            min-height: 100dvh !important;
            overflow-y: auto !important;
        }
        #desk-officer-dashboard-content[data-swd-dashboard-fit] {
            overflow-y: visible !important;
        }
    }

    /* Tablet view (768px–1023px, up to 1280px / 80rem): collapsed rail, full-width content & clean header */
    @media (min-width: 48rem) and (max-width: 80rem) {
        body.desk-officer-dashboard-mobile-nav #mobileTopBar { display: none !important; }
        body.desk-officer-dashboard-mobile-nav #mobileMenuToggle { display: none !important; }
        body.desk-officer-dashboard-mobile-nav #dashboardMenuToggle { display: none !important; }
        body.desk-officer-dashboard-mobile-nav #sidebarCloseBtn { display: none !important; }
        body.desk-officer-dashboard-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        body.desk-officer-dashboard-mobile-nav #mainContent > header#desk-officer-dashboard-header > div {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            min-height: 4.25rem !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }
        body.desk-officer-dashboard-mobile-nav #mainContent .tablet-header-row {
            display: flex !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar {
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
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-header {
            display: flex !important;
            min-height: 4.25rem !important;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-header img {
            width: 2.75rem !important;
            height: 2.75rem !important;
            flex-shrink: 0 !important;
        }
        /* Collapsed navigation rail on tablet */
        body.desk-officer-dashboard-mobile-nav #sidebar,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-text,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed .sidebar-text,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-menu,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed .sidebar-menu,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-link,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed .sidebar-link,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar .sidebar-link i,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed .sidebar-link i,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed-init .sidebar-link i {
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            text-align: center !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebar ~ #mainContent,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed ~ #mainContent,
        body.desk-officer-dashboard-mobile-nav #sidebar.sidebar-collapsed-init ~ #mainContent {
            width: calc(100% - 4.5rem) !important;
            margin-left: 4.5rem !important;
            min-width: 0 !important;
        }
        body.desk-officer-dashboard-mobile-nav #sidebarToggleBtn {
            display: none !important;
        }
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
<header id="desk-officer-dashboard-header" data-dashboard-header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="flex items-center gap-3 min-w-0">
            <button id="dashboardMenuToggle" class="hidden" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Welcome, {{ $dashboardDepartment }} SILANG</h2>
                <p class="text-sm text-gray-500">{{ $dashboardDepartment }} {{ $dashboardRole }} - Silang, Cavite</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <!-- Notifications -->
            @include('desk-officer.components.notifications')
            
            <!-- User Menu -->
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position ?? 'Desk Officer' }}</p>
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
