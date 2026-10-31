<!DOCTYPE html>
@php
$isArchivedReportPreview = auth()->user()?->position === 'Desk Officer'
&& auth()->user()?->isMultiCaseDepartment()
&& request()->boolean('archived')
&& ((request()->boolean('archive_report_preview')
&& request()->routeIs('desk-officer.quarterly-reports.show', 'desk-officer.bcpc-case-monitoring.edit'))
|| (request()->boolean('archive_case_preview')
&& request()->routeIs('desk-officer.cases.show', 'cases.show')));
$persistsDeskOfficerSidebar = auth()->user()?->position === 'Desk Officer'
&& (auth()->user()?->isMultiCaseDepartment() || in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'],
true));
$deskOfficerSidebarStateKey = auth()->user()?->isMultiCaseDepartment()
? 'multiRoleDeskOfficerSidebarCollapsed'
: ($persistsDeskOfficerSidebar
? (auth()->user()?->defaultCaseDepartment() === 'BCPC' ? 'bcpcDeskOfficerSidebarCollapsed' :
'swdDeskOfficerSidebarCollapsed')
: 'sidebarCollapsed');
@endphp
<html lang="en" @if($isArchivedReportPreview) class="archive-report-preview" @endif>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'iSERVE SILANG Desk Officer')</title>
    <link rel="icon" href="{{ asset('images/iserve_logo.jpg') }}" type="image/jpeg">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Public+Sans:wght@400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if($persistsDeskOfficerSidebar)
    <script>
        try {
            if (window.matchMedia('(min-width: 48rem) and (max-width: 80rem)').matches) {
                document.documentElement.classList.add('tablet-sidebar-loading');
            }
            document.documentElement.classList.toggle('bcpc-desk-sidebar-collapsed', window.matchMedia('(min-width: 48rem) and (max-width: 80rem)').matches || localStorage.getItem(@json($deskOfficerSidebarStateKey)) === 'true');
        } catch (e) {}
    </script>
    @endif

    @stack('styles')
    @if($isArchivedReportPreview)
    <style>
        html.archive-report-preview {
            width: 100%;
            height: 100%;
            overflow-y: auto;
        }

        html.archive-report-preview body.portal-ui {
            min-width: 0 !important;
            min-height: 0 !important;
            height: auto !important;
            overflow-y: visible !important;
        }

        html.archive-report-preview body.portal-ui #mainContent {
            box-sizing: border-box !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding-inline: 0 !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow-y: visible !important;
        }

        html.archive-report-preview #mainContent>header>div {
            display: block !important;
        }

        html.archive-report-preview #mainContent>header>div>div:first-child {
            width: 100%;
            min-width: 0;
            max-width: none;
        }
    </style>
    @endif
    <link rel="stylesheet" href="{{ asset('css/desk-officer-tables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-pagination.css') }}">
    <link rel="stylesheet"
        href="{{ asset('css/table-action-controls.css') }}?v={{ filemtime(public_path('css/table-action-controls.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('css/portal-theme.css') }}?v={{ filemtime(public_path('css/portal-theme.css')) }}">

    <style>
        /* Hide elements with x-cloak until Alpine.js initializes */
        [x-cloak] {
            display: none !important;
        }

        /* Unify colored primary actions across BCPC/SWD and multi-role desk pages. */
        .portal-ui #mainContent :is(button, a):is([class*="bg-blue-5"], [class*="bg-blue-6"], [class*="bg-blue-7"], [class*="bg-green-5"], [class*="bg-green-6"], [class*="bg-green-7"], [class*="bg-orange-5"], [class*="bg-orange-6"], [class*="bg-orange-7"], [class*="bg-amber-5"], [class*="bg-amber-6"], [class*="bg-amber-7"]):not(:disabled):not([aria-disabled="true"]) {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
        }

        .portal-ui #mainContent :is(button, a):is([class*="bg-blue-5"], [class*="bg-blue-6"], [class*="bg-blue-7"], [class*="bg-green-5"], [class*="bg-green-6"], [class*="bg-green-7"], [class*="bg-orange-5"], [class*="bg-orange-6"], [class*="bg-orange-7"], [class*="bg-amber-5"], [class*="bg-amber-6"], [class*="bg-amber-7"]):not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active) {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
            color: #ffffff !important;
        }

        #sidebar [x-show]>a,
        #sidebar [x-show]>button {
            font-family: inherit;
            font-size: .875rem;
            line-height: 1.25rem;
            font-weight: 500;
        }

        #sidebar [x-show]>a>i,
        #sidebar [x-show]>button>i {
            font-size: .875rem;
            width: 1rem;
            text-align: center;
            flex-shrink: 0;
        }

        @media (min-width: 37.5rem) and (max-width: 79.9375rem) {
            .desk-officer-tablet-role-badge {
                display: none !important;
            }
        }

        @if(auth()->user()?->position==='Desk Officer' && auth()->user()?->defaultCaseDepartment()==='VAWC')

        /* Keep SWD Desk Officer Edit and Archive actions from flashing white on interaction. */
        .portal-ui #mainContent :is(a, button)[title^="Edit"],
        .portal-ui #mainContent :is(a, button)[title^="Archive"],
        .portal-ui #mainContent .edit-case,
        .portal-ui #mainContent .archive-referral-btn {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
            transition: none !important;
        }

        .portal-ui #mainContent :is(a, button)[title^="Edit"]:is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Archive"]:is(:hover, :focus, :active),
        .portal-ui #mainContent .edit-case:is(:hover, :focus, :active),
        .portal-ui #mainContent .archive-referral-btn:is(:hover, :focus, :active) {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
            color: #ffffff !important;
        }

        @endif @if(auth()->user()?->position==='Desk Officer' && auth()->user()?->defaultCaseDepartment()==='BCPC' && !auth()->user()?->isMultiCaseDepartment())

        /* Keep BCPC case actions visually stable while the page finishes loading. */
        .portal-ui #mainContent .edit-case:not(:disabled),
        .portal-ui #mainContent button[title="Archive Case"] {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
            transition: none !important;
        }

        .portal-ui #mainContent .edit-case:not(:disabled):is(:hover, :focus, :active),
        .portal-ui #mainContent button[title="Archive Case"]:is(:hover, :focus, :active) {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
            color: #ffffff !important;
        }

        @endif

        /* Muted red treatment for disabled controls on BCPC and SWD pages.
           This native-state selector leaves clickable archive/deactivate actions unchanged. */
        .portal-ui #mainContent button:disabled,
        .portal-ui #mainContent input[type="button"]:disabled,
        .portal-ui #mainContent input[type="submit"]:disabled,
        .portal-ui #mainContent [role="button"][aria-disabled="true"],
        .portal-ui #mainContent a[aria-disabled="true"]:not(#bcpc-refer-case-button) {
            background-color: #FEE2E2 !important;
            border-color: #FCA5A5 !important;
            color: #991B1B !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            transition: none !important;
        }

        .portal-ui #mainContent button:disabled:is(:hover, :focus, :active),
        .portal-ui #mainContent input[type="button"]:disabled:is(:hover, :focus, :active),
        .portal-ui #mainContent input[type="submit"]:disabled:is(:hover, :focus, :active),
        .portal-ui #mainContent [role="button"][aria-disabled="true"]:is(:hover, :focus, :active),
        .portal-ui #mainContent a[aria-disabled="true"]:not(#bcpc-refer-case-button):is(:hover, :focus, :active) {
            background-color: #FEE2E2 !important;
            border-color: #FCA5A5 !important;
            color: #991B1B !important;
            box-shadow: 0 2px 6px rgba(196, 30, 58, .12) !important;
            transform: none !important;
            filter: none !important;
        }

        .portal-ui #mainContent button:disabled> :is(i, svg),
        .portal-ui #mainContent button:disabled> :is(span, .case-card-action-label)> :is(i, svg),
        .portal-ui #mainContent [role="button"][aria-disabled="true"]> :is(i, svg),
        .portal-ui #mainContent a[aria-disabled="true"]:not(#bcpc-refer-case-button)> :is(i, svg) {
            color: #B91C1C !important;
            stroke: currentColor;
        }

        .portal-ui #mainContent button:disabled,
        .portal-ui #mainContent [role="button"][aria-disabled="true"],
        .portal-ui #mainContent a[aria-disabled="true"]:not(#bcpc-refer-case-button) {
            box-shadow: 0 2px 6px rgba(196, 30, 58, .12) !important;
            opacity: 1 !important;
        }

        /* SweetAlert2 high z-index override */
        .swal2-container-high-z {
            z-index: 9999 !important;
        }

        .swal2-container.swal2-backdrop-show {
            z-index: 9999 !important;
        }

        .swal2-popup {
            border-radius: 12px !important;
            font-family: 'Montserrat', sans-serif;
        }

        #reportTypeModal>div {
            box-sizing: border-box;
            width: min(24rem, calc(100vw - 2rem));
        }

        #quarterly-report-success>div {
            box-sizing: border-box;
            max-width: 100%;
            max-height: calc(100vh - 2rem);
            max-height: calc(100dvh - 2rem);
            overflow-y: auto;
            overscroll-behavior-y: contain;
        }

        .portal-ui :is(input, select, textarea):focus {
            outline: none !important;
            border-color: #9ca3af !important;
            box-shadow: 0 0 0 2px rgba(156, 163, 175, .25) !important;
        }

        .portal-ui :is(input, select, textarea):focus-visible {
            outline: 2px solid #9ca3af !important;
            outline-offset: 1px;
        }

        #editCaseModal .case-form-container {
            margin-top: 0 !important;
            padding: 1.5rem !important;
        }

        #editCaseModal .form-note {
            margin-top: 0 !important;
            margin-bottom: 1rem !important;
        }

        /* Custom scrollbar for sidebar */
        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Smooth sidebar transition - mobile slide + fade */
        @media (max-width: 1023px) {
            #sidebar {
                opacity: 0;
                transform: translateX(-100%);
                transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1),
                    opacity 0.3s ease-out;
            }

            #sidebar.sidebar-open {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Desktop sidebar transition */
        @media (min-width: 1024px) {
            #sidebar {
                transition: width 0.3s ease;
            }
        }

        /* Mobile close button - hidden by default, shown when sidebar is open */
        #sidebarCloseBtn {
            opacity: 0;
            transform: scale(0.5) translateX(-10px);
            transition: opacity 0.25s ease 0.2s, transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s;
            pointer-events: none;
        }

        #sidebar.sidebar-open #sidebarCloseBtn {
            opacity: 1;
            transform: scale(1) translateX(0);
            pointer-events: auto;
        }

        /* Main content transition */
        .sidebar-transition {
            width: calc(100vw - 18rem);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        /* Sidebar text - smooth fade */
        .sidebar-text {
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.15s ease, max-width 0.3s ease;
            opacity: 1;
            max-width: 200px;
        }

        /* Sidebar link - fixed icon position from left edge */
        .sidebar-link {
            display: flex;
            align-items: center;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            text-decoration: none !important;
        }

        .sidebar-link i {
            flex-shrink: 0;
            width: 1.25rem;
            min-width: 1.25rem;
            text-align: center;
        }

        /* Reference-sized dropdown chevrons for the existing tablet range only. */
        @media screen and (min-width: 48rem) and (max-width: 80rem) {
            html body.portal-ui #sidebar nav .sidebar-link > i.fa-chevron-down.sidebar-text {
                align-self: center;
                margin-left: auto !important;
                margin-right: 0 !important;
                font-size: .625rem !important;
                font-weight: 900;
                line-height: 1 !important;
                text-align: center;
                transform-origin: center;
            }
        }

        /* Match regular link icon alignment in the collapsed tablet landscape rail. */
        @media screen and (min-width: 48rem) and (max-width: 80rem) and (orientation: landscape) {
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) nav .sidebar-link {
                justify-content: center !important;
                gap: 0 !important;
            }

            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) nav .sidebar-link > div {
                display: flex !important;
                flex: 0 0 1.25rem !important;
                width: 1.25rem !important;
                min-width: 0 !important;
                gap: 0 !important;
                align-items: center !important;
                justify-content: center !important;
            }

            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) nav .sidebar-link .sidebar-text {
                display: none !important;
            }
        }

        /* Keep the admin-style nesting while leaving room around submenu highlights. */
        @media screen and (min-width: 48rem) and (max-width: 80rem) {
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) nav [x-show] {
                box-sizing: border-box;
                width: 100% !important;
                padding-left: 1rem !important;
                padding-right: 0 !important;
            }

            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) nav [x-show] > :is(a, button) {
                justify-content: center !important;
                gap: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) nav [x-show] > :is(a, button) > i:not(.sidebar-text) {
                position: static;
                left: auto;
            }
        }

        /* Sidebar header */
        .sidebar-header {
            min-height: 5.5rem;
            /* Keep consistent height */
            padding-left: 0.75rem !important;
            /* Fixed left padding */
        }

        .sidebar-header>div {
            justify-content: flex-start !important;
        }

        .sidebar-header img {
            flex-shrink: 0;
        }

        /* Sidebar menu fixed padding */
        .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .sidebar-menu a {
            text-decoration: none !important;
        }

        .portal-ui #sidebar {
            border-radius: 0 !important;
        }

        .portal-ui #sidebar>nav.sidebar-menu,
        .portal-ui #sidebar>nav.sidebar-menu+.sidebar-menu {
            background-color: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .portal-ui #sidebar>nav.sidebar-menu {
            overflow-x: hidden !important;
        }

        @media (min-width: 40.0625rem) {

            #sidebar.sidebar-collapsed [x-show],
            #sidebar.sidebar-collapsed-init [x-show] {
                width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            #sidebar.sidebar-collapsed [x-show]> :is(a, button),
            #sidebar.sidebar-collapsed-init [x-show]> :is(a, button) {
                justify-content: center !important;
                gap: 0 !important;
                width: 100% !important;
                min-height: 3rem !important;
                margin-left: auto !important;
                margin-right: auto !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            #sidebar.sidebar-collapsed [x-show]> :is(a, button).bg-purple-600,
            #sidebar.sidebar-collapsed-init [x-show]> :is(a, button).bg-purple-600 {
                background-color: #C41E3A !important;
                box-shadow: none !important;
            }
        }

        /* Sidebar collapsed state - desktop only */
        @media (min-width: 1024px) {
            #sidebar.sidebar-collapsed {
                width: 4.5rem;
            }

            /* Hide text smoothly */
            #sidebar.sidebar-collapsed .sidebar-text {
                opacity: 0;
                max-width: 0;
                margin-left: 0;
            }

            /* Header - same padding as expanded */
            #sidebar.sidebar-collapsed .sidebar-header {
                padding-left: 0.75rem !important;
                padding-right: 0.5rem !important;
                min-height: 5.5rem;
            }

            #sidebar.sidebar-collapsed .sidebar-header>div {
                justify-content: flex-start !important;
                gap: 0;
            }

            /* Keep header icon same size */
            #sidebar.sidebar-collapsed .sidebar-header img {
                width: 3rem;
                height: 3rem;
            }

            /* Menu - same padding as expanded */
            #sidebar.sidebar-collapsed .sidebar-menu {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            /* Links - same left padding as expanded */
            #sidebar.sidebar-collapsed .sidebar-link {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            /* Footer - same padding */
            #sidebar.sidebar-collapsed>div:last-of-type {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            /* Toggle button position when collapsed */
            #sidebar.sidebar-collapsed~#sidebarToggleBtn {
                left: calc(4.5rem - 0.75rem);
            }

            #sidebar.sidebar-collapsed~#sidebarToggleBtn i {
                transform: rotate(180deg);
            }

            /* Main content margin when collapsed */
            #sidebar.sidebar-collapsed~#mainContent {
                margin-left: 4.5rem;
                width: calc(100vw - 4.5rem);
            }
        }

        /* Floating sidebar toggle button - on edge of sidebar */
        #sidebarToggleBtn {
            position: fixed;
            top: calc(1.25rem + 1.5rem);
            /* p-5 padding + half of w-12 logo */
            left: calc(18rem - 0.75rem);
            /* sidebar width minus half button width */
            transform: translateY(-50%);
            z-index: 50;
            transition: left 0.3s ease;
        }

        #sidebarToggleBtn i {
            transition: transform 0.3s ease;
        }

        /* Initial collapsed state - applied via JS before render */
        #sidebar.sidebar-collapsed-init {
            width: 4.5rem !important;
            transition: none !important;
        }

        #sidebar.sidebar-collapsed-init .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
        }

        #sidebar.sidebar-collapsed-init~#sidebarToggleBtn {
            left: calc(4.5rem - 0.75rem) !important;
            transition: none !important;
        }

        #sidebar.sidebar-collapsed-init~#sidebarToggleBtn i {
            transform: rotate(180deg) !important;
        }

        #sidebar.sidebar-collapsed-init~#mainContent {
            margin-left: 4.5rem !important;
            width: calc(100vw - 4.5rem) !important;
            transition: none !important;
        }

        /* Mobile phone navigation (< 600px / < 37.5rem) */
        @media (max-width: 37.4375rem) {
            #mobileTopBar {
                display: flex !important;
            }

            #mobileTopBar .multi-role-mobile-brand {
                max-width: min(13rem, calc(100vw - 12rem));
                gap: .5rem;
            }

            #mobileTopBar .multi-role-mobile-brand img {
                width: 2rem;
                height: 2rem;
                flex: 0 0 auto;
            }

            #mobileTopBar .multi-role-mobile-brand span {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: clamp(.8125rem, 1.5vw, 1rem);
            }

            #mobileMenuToggle {
                display: flex !important;
                flex: 0 0 2.25rem;
                width: 2.25rem;
                height: 2.25rem;
                align-items: center;
                justify-content: center;
                visibility: visible !important;
            }

            #sidebarOverlay {
                display: block !important;
            }

            #sidebar {
                inset: 0 auto 0 0 !important;
                width: min(18rem, 82vw) !important;
                min-width: min(18rem, 82vw) !important;
                max-width: 82vw !important;
                height: 100vh !important;
                height: 100dvh !important;
                max-height: none !important;
                opacity: 0;
                transform: translateX(-100%) !important;
                transition: transform .3s ease, opacity .2s ease !important;
                z-index: 50 !important;
            }

            #sidebar.sidebar-collapsed,
            #sidebar.sidebar-collapsed-init {
                width: min(18rem, 82vw) !important;
                min-width: min(18rem, 82vw) !important;
                max-width: 82vw !important;
            }

            #sidebar.sidebar-collapsed .sidebar-text,
            #sidebar.sidebar-collapsed-init .sidebar-text {
                opacity: 1 !important;
                max-width: 12.5rem !important;
            }

            #sidebar.sidebar-open {
                opacity: 1 !important;
                transform: translateX(0) !important;
            }

            #sidebarCloseBtn {
                display: flex !important;
                top: .75rem !important;
                right: .75rem !important;
                width: 2rem !important;
                height: 2rem !important;
                background-color: #ffffff !important;
                color: #C41E3A !important;
                border: .0625rem solid rgba(196, 30, 58, .2);
                z-index: 60;
            }

            #sidebarToggleBtn {
                display: none !important;
            }

            #mainContent,
            #sidebar.sidebar-collapsed~#mainContent,
            #sidebar.sidebar-collapsed-init~#mainContent {
                width: 100% !important;
                min-width: 0 !important;
                margin-left: 0 !important;
                overflow-x: hidden;
            }
        }

        /* Tablet navigation (600px–1280px / 37.5rem–80rem):
         * Remove the extra top header (#mobileTopBar) and retain the visible navigation rail.
         * The sidebar starts at the vertical top of the viewport (top: 0).
         * Sidebar toggle button is positioned at top: calc(1.25rem + 1.5rem) and correctly
         * aligned when expanded (276px) and collapsed (60px).
         */
        @media (min-width: 37.5rem) and (max-width: 80rem) {
            #mobileTopBar {
                display: none !important;
            }

            #sidebarOverlay {
                display: none !important;
                pointer-events: none !important;
            }

            #sidebarCloseBtn,
            #mobileMenuToggle,
            #mainContent #mobileMenuToggle {
                display: none !important;
            }

            #sidebar {
                inset: 0 auto 0 0 !important;
                width: 18rem !important;
                min-width: 18rem !important;
                max-width: 18rem !important;
                height: 100vh !important;
                height: 100dvh !important;
                max-height: none !important;
                opacity: 1 !important;
                transform: translateX(0) !important;
                transition: width .3s ease !important;
            }

            #sidebar.sidebar-collapsed,
            #sidebar.sidebar-collapsed-init {
                width: 4.5rem !important;
                min-width: 4.5rem !important;
                max-width: 4.5rem !important;
            }

            #sidebar.sidebar-collapsed .sidebar-text,
            #sidebar.sidebar-collapsed-init .sidebar-text {
                opacity: 0 !important;
                max-width: 0 !important;
            }

            #sidebar.sidebar-collapsed .sidebar-icon-container,
            #sidebar.sidebar-collapsed-init .sidebar-icon-container {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            #sidebar.sidebar-collapsed .sidebar-chevron,
            #sidebar.sidebar-collapsed-init .sidebar-chevron,
            #sidebar.sidebar-collapsed .sidebar-submenu,
            #sidebar.sidebar-collapsed-init .sidebar-submenu {
                display: none !important;
            }

            #sidebar.sidebar-collapsed .logo-container,
            #sidebar.sidebar-collapsed-init .logo-container {
                justify-content: center !important;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            #sidebar.sidebar-collapsed .logo-text,
            #sidebar.sidebar-collapsed-init .logo-text {
                display: none !important;
            }

            #sidebar.sidebar-collapsed .user-profile-compact,
            #sidebar.sidebar-collapsed-init .user-profile-compact {
                display: flex !important;
                justify-content: center !important;
            }

            #sidebar.sidebar-collapsed .user-profile-expanded,
            #sidebar.sidebar-collapsed-init .user-profile-expanded {
                display: none !important;
            }

            #sidebar.sidebar-collapsed nav a,
            #sidebar.sidebar-collapsed nav button,
            #sidebar.sidebar-collapsed-init nav a,
            #sidebar.sidebar-collapsed-init nav button {
                justify-content: center !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            #sidebar.sidebar-collapsed>div:last-of-type,
            #sidebar.sidebar-collapsed-init>div:last-of-type {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            #sidebarToggleBtn {
                display: flex !important;
                position: fixed !important;
                top: calc(1.25rem + 1.5rem) !important;
                left: calc(18rem - 0.75rem) !important;
                transform: translateY(-50%) !important;
                z-index: 50 !important;
                transition: left .3s ease !important;
            }

            #sidebar.sidebar-collapsed~#sidebarToggleBtn,
            #sidebar.sidebar-collapsed-init~#sidebarToggleBtn {
                left: calc(4.5rem - 0.75rem) !important;
            }

            #sidebar.sidebar-collapsed~#sidebarToggleBtn i,
            #sidebar.sidebar-collapsed-init~#sidebarToggleBtn i {
                transform: rotate(180deg) !important;
            }

            #sidebar.sidebar-collapsed~#sidebarToggleBtn,
            #sidebar.sidebar-collapsed-init~#sidebarToggleBtn {
                left: calc(4.5rem - 0.75rem) !important;
            }

            #sidebar.sidebar-collapsed~#sidebarToggleBtn i,
            #sidebar.sidebar-collapsed-init~#sidebarToggleBtn i {
                transform: rotate(180deg) !important;
            }

            .portal-ui #sidebar~#mainContent {
                width: calc(100vw - 18rem) !important;
                min-width: 0 !important;
                margin-left: 18rem !important;
                transition: margin-left .3s ease, width .3s ease !important;
                overflow-x: hidden;
            }

            .portal-ui #sidebar.sidebar-collapsed~#mainContent,
            .portal-ui #sidebar.sidebar-collapsed-init~#mainContent,
            html.bcpc-desk-sidebar-collapsed .portal-ui #sidebar~#mainContent,
            html.bcpc-desk-sidebar-collapsed .portal-ui #mainContent {
                width: calc(100vw - 4.5rem) !important;
                min-width: 0 !important;
                margin-left: 4.5rem !important;
                transition: margin-left .3s ease, width .3s ease !important;
            }
        }

        /* Keep shared Desk Officer pages stable across tablet portrait and landscape. */
        @media (min-width: 40.0625rem) and (max-width: 80rem) {
            /* .portal-ui #mainContent {
                padding-left: .75rem !important;
                padding-right: .75rem !important;
            } */

            #mainContent .tablet-header-row {
                grid-template-columns: minmax(0, 1fr) auto !important;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle+div {
                grid-column: 1 !important;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle+div~div:last-child {
                grid-column: 2 !important;
            }

            #mainContent> :is(main, div, section) {
                min-width: 0;
                max-width: 100%;
            }

            #mainContent .portal-page-header>div,
            #mainContent>header>div {
                flex-wrap: wrap;
                align-items: center;
            }

            #mainContent .portal-page-header>div>div:first-child,
            #mainContent>header>div>div:first-child {
                flex: 1 1 14rem;
                min-width: 0;
            }

            #mainContent .portal-page-clock {
                flex: 0 1 auto;
                max-width: 100%;
                margin-left: auto;
                margin-right: 0;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]),
            #mainContent>header div:has(> img[alt="User Profile"]) {
                flex: 0 1 auto;
                max-width: 100%;
                min-width: 0;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"])>div,
            #mainContent>header div:has(> img[alt="User Profile"])>div {
                min-width: 0;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"])>div>p:first-child,
            #mainContent>header div:has(> img[alt="User Profile"])>div>p:first-child {
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #mainContent :is(.portal-table-scroll, .overflow-x-auto, .table-responsive) {
                max-width: 100%;
                overscroll-behavior-x: contain;
            }

            #mainContent :is(.pagination, [aria-label*="Pagination"]) {
                max-width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
            }

            #mainContent :is(dialog, .modal-dialog, .swal2-popup) {
                max-width: calc(100vw - 2rem) !important;
            }
        }

        /* Tablet header: retain the existing controls in one white page header. */
        @media (min-width: 40.0625rem) and (max-width: 80rem) {
            #mainContent .tablet-header-row {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) auto !important;
                align-items: center;
                column-gap: .75rem;
                row-gap: 0;
                min-height: 5.5rem;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle {
                display: none !important;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle+div {
                grid-column: 1 !important;
                grid-row: 1;
                min-width: 0;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle+div~div:last-child {
                grid-column: 2 !important;
                grid-row: 1;
                flex-wrap: nowrap;
                justify-content: flex-end;
                min-width: 0;
            }

            #mainContent .desk-officer-tablet-role-badge {
                display: none !important;
            }
        }

        /* Phone-only: leave room for the menu and account controls. */
        @media (max-width: 47.9375rem) {
            #mobileTopBar .desk-officer-mobile-brand {
                display: none !important;
            }
        }

        /* ── Mobile-responsive overrides ── */
        /* Phone header: retain navigation, title, notifications, and avatar only. */
        @media (max-width: 40rem) {

            #mainContent .portal-page-clock,
            #mainContent .portal-clock-only-header,
            #mainContent .desk-officer-tablet-role-badge {
                display: none !important;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]),
            #mainContent>header div:has(> img[alt="User Profile"]) {
                display: flex !important;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"])>div,
            #mainContent>header div:has(> img[alt="User Profile"])>div {
                display: none !important;
            }

            #mainContent .tablet-header-row {
                display: grid !important;
                grid-template-columns: 2.25rem minmax(0, 1fr) auto;
                align-items: center;
                column-gap: .75rem;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle {
                grid-column: 1;
                grid-row: 1;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle+div {
                grid-column: 2;
                grid-row: 1;
                min-width: 0;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle+div~div:last-child {
                grid-column: 3;
                grid-row: 1;
                min-width: 0;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle+div~div:last-child>div:has(> img[alt="User Profile"]) {
                order: 1;
            }

            #mainContent .tablet-header-row>#mobileMenuToggle+div~div:last-child>div:has(> button[aria-label="Notifications"]) {
                order: 2;
            }

            .view-cases-toolbar-actions>.single-role-desk-view-filter {
                order: 2;
            }

            .view-cases-toolbar-actions>.single-role-desk-view-print {
                order: 1;
            }
        }

        @if(request()->routeIs('desk-officer.dashboard', 'desk-officer.bcpc.dashboard', 'cases.refer.form') || request()->is('desk-officer/dashboard*', 'desk-officer/bcpc-dashboard*'))

        /* Apply after portal-theme.css so the mobile dashboard alignment wins (matches admin/dashboard). */
        @media (max-width: 47.9375rem) {
            body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header>div {
                padding-top: max(1rem, env(safe-area-inset-top)) !important;
                padding-bottom: 1rem !important;
                padding-left: max(.75rem, env(safe-area-inset-left)) !important;
                padding-right: max(1rem, env(safe-area-inset-right)) !important;
                align-items: center !important;
                flex-wrap: nowrap !important;
                min-width: 0 !important;
            }

            body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header>div>div:last-child {
                flex: 0 0 auto !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
            }

            body.portal-ui.desk-officer-dashboard-mobile-nav #dashboardMenuToggle {
                position: relative !important;
                left: 0 !important;
                margin: 0 !important;
                align-self: center !important;
                transform: none !important;
            }

            body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header>div>div:first-of-type {
                flex-direction: row !important;
                align-items: center !important;
                gap: .625rem !important;
                min-width: 0 !important;
                flex: 1 1 auto !important;
            }

            body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header>div>div:first-of-type>div {
                width: auto !important;
                flex: 1 1 auto !important;
                min-width: 0 !important;
                text-align: left !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                margin: 0 !important;
                align-self: center !important;
            }

            body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent .tablet-header-row {
                display: flex !important;
            }

            body.portal-ui.desk-officer-dashboard-mobile-nav #sidebar~#mainContent {
                width: 100% !important;
                min-width: 0 !important;
                margin-left: 0 !important;
            }
        }

        @media (max-width: 40rem) {
            body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header div:has(> img[alt="User Profile"]) {
                display: none !important;
            }
        }

        body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header button[aria-label="Notifications"] {
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

        body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }

        body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header button[aria-label="Notifications"]>i {
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

        body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header :is(#notifications-unread-count, [data-unread-badge], .notification-badge) {
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
        }

        @media (max-width: 47.9375rem) {
            body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header :is(#notifications-unread-count, [data-unread-badge], .notification-badge) {
                border: 2px solid #C41E3A !important;
            }
        }

        @media (min-width: 48rem) {
            body.portal-ui.desk-officer-dashboard-mobile-nav #mainContent>header#desk-officer-dashboard-header :is(#notifications-unread-count, [data-unread-badge], .notification-badge) {
                border: 2px solid #ffffff !important;
            }
        }

        @media (min-width: 48rem) and (max-width: 63.9375rem) {
            body.portal-ui.desk-officer-dashboard-mobile-nav #sidebarToggleBtn {
                display: none !important;
            }
        }

        @endif .overflow-x-auto,
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 768px) {
            .main-content {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
                padding-top: 1rem;
            }

            table th,
            table td {
                padding: 0.5rem 0.5rem !important;
                font-size: 0.8rem !important;
                white-space: nowrap;
            }

            .dashboard-stat,
            .rounded-lg.shadow-lg.border-l-8 {
                padding: 1rem !important;
                height: auto !important;
            }

            .dashboard-stat .text-3xl,
            .rounded-lg.shadow-lg.border-l-8 .text-3xl {
                font-size: 1.5rem !important;
            }

            .dashboard-stat .h-16.w-16,
            .rounded-lg.shadow-lg.border-l-8 .h-16.w-16 {
                width: 2.5rem !important;
                height: 2.5rem !important;
                font-size: 1rem !important;
            }

            h1.text-2xl,
            h2.text-2xl {
                font-size: 1.25rem !important;
            }

            #caseModal .max-w-4xl {
                max-width: 100% !important;
                margin-left: 0.5rem !important;
                margin-right: 0.5rem !important;
                max-height: calc(100vh - 2rem) !important;
                max-height: calc(100dvh - 2rem) !important;
            }

            .modal-dialog {
                margin: 0.5rem !important;
                max-width: calc(100% - 1rem) !important;
            }

            .filter-option {
                padding: 0.5rem 0.75rem !important;
            }

            .btn-sm,
            .btn-group-sm>.btn {
                padding: 0.25rem 0.4rem !important;
                font-size: 0.75rem !important;
            }

            .card-body.p-0 {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .max-w-6xl {
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }

        @media (max-width: 480px) {

            table th,
            table td {
                padding: 0.35rem 0.4rem !important;
                font-size: 0.75rem !important;
            }

            .inline-flex.gap-2 {
                gap: 0.25rem !important;
            }
        }

        @if(request()->routeIs('desk-officer.dashboard', 'desk-officer.bcpc.dashboard', 'cases.refer.form', 'case-profiles.index', 'desk-officer.cases.*', 'admin.barangay-cases.show', 'desk-officer.referrals*', 'desk-officer.calendar*', 'desk-officer.quarterly-reports*', 'desk-officer.archived-barangay-cases*', 'desk-officer.bcpc-case-monitoring*') || request()->is('desk-officer/cases*', 'admin-barangay-cases*', 'desk-officer/bcpc-case-monitoring*'))

        /* Apply after portal-theme.css so the mobile dashboard alignment wins. */
        @media (max-width: 47.9375rem) {
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mobileTopBar {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, #case-profiles-header, .portal-page-header, .sticky) {
                background: #C41E3A !important;
                border-bottom: 1px solid #99172E !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, .portal-page-header, .sticky)>div {
                padding-left: max(.5rem, env(safe-area-inset-left)) !important;
                padding-right: max(1rem, env(safe-area-inset-right)) !important;
                align-items: flex-start !important;
                flex-wrap: nowrap !important;
                min-width: 0 !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, .portal-page-header, .sticky)>div>div:last-child {
                flex: 0 0 auto !important;
                flex-wrap: nowrap !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
                position: relative !important;
                left: 0 !important;
                margin-left: 0 !important;
                transform: none !important;
                display: inline-flex !important;
                flex: 0 0 2.5rem !important;
                width: 2.5rem !important;
                height: 2.5rem !important;
                min-width: 2.5rem !important;
                min-height: 2.5rem !important;
                align-items: center !important;
                justify-content: center !important;
                border: 0 !important;
                border-radius: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
                color: #ffffff !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) :is(#dashboardMenuToggle, #pageMobileMenuToggle)>i {
                color: inherit !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, .portal-page-header, .sticky)>div>div:first-of-type {
                flex-direction: row !important;
                align-items: flex-start !important;
                gap: .5rem !important;
                min-width: 0 !important;
                flex: 1 1 auto !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, .portal-page-header, .sticky)>div>div:first-of-type>div {
                width: auto !important;
                flex: 1 1 auto !important;
                min-width: 0 !important;
                text-align: left !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent .tablet-header-row {
                display: flex !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar~#mainContent {
                width: 100% !important;
                min-width: 0 !important;
                margin-left: 0 !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, #case-profiles-header, .portal-page-header, .sticky) :is(h1, h2, h3) {
                color: #ffffff !important;
                opacity: 1 !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, #case-profiles-header, .portal-page-header, .sticky) p {
                color: rgba(255, 255, 255, 0.85) !important;
                display: block !important;
                opacity: 1 !important;
                font-size: clamp(0.6875rem, 2.8vw, 0.75rem) !important;
                line-height: 1.2 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                margin: 0 !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, #case-profiles-header, .portal-page-header, .sticky) div:first-of-type :is(span, a) {
                color: #ffffff !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebarOverlay {
                display: none !important;
                pointer-events: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: auto !important;
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                max-height: calc(100vh - 60px) !important;
                max-height: calc(100dvh - 60px) !important;
                overflow-y: auto !important;
                z-index: 999 !important;
                border-radius: 0 0 1.25rem 1.25rem !important;
                box-shadow: 0 14px 28px rgba(0, 0, 0, .22) !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transform: translateY(-8px) !important;
                transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
                background: #C41E3A !important;
                background-image: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar.sidebar-open {
                opacity: 1 !important;
                visibility: visible !important;
                transform: translateY(0) !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar.sidebar-collapsed,
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar.sidebar-collapsed-init {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-header {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-text {
                opacity: 1 !important;
                max-width: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-menu {
                flex: 0 1 auto;
                min-height: 0;
                padding: .5rem !important;
                overflow-y: auto !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-link {
                justify-content: flex-start !important;
                gap: .75rem !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar #sidebarCloseBtn {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebarToggleBtn {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar nav :is(a, button),
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-link {
                background: #C41E3A !important;
                color: #ffffff !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar nav :is(a, button) :is(i, svg, span),
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-link :is(i, svg, span) {
                color: #ffffff !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar nav :is(a, button):hover,
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-link:hover {
                background: #99172E !important;
                color: #ffffff !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-link:is([class~="bg-purple-600"], [class~="bg-purple-700/70"]),
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar nav [x-show]> :is(a, button).bg-purple-600 {
                background: #99172E !important;
                color: #ffffff !important;
                box-shadow: inset 3px 0 0 #ffffff !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header button[aria-label="Notifications"] {
                box-sizing: border-box !important;
                width: 2.5rem !important;
                min-width: 2.5rem !important;
                height: 2.5rem !important;
                min-height: 2.5rem !important;
                padding: 0 !important;
                border-radius: .625rem !important;
                background-color: #A01830 !important;
                color: #ffffff !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
        }

        @media (max-width: 40rem) {
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header:is(#desk-officer-dashboard-header, #barangay-cases-view-header, .portal-page-header, .sticky) div:has(> img[alt="User Profile"]) {
                display: none !important;
            }
        }

        /* Tablet navigation (768px–1023px / 48rem–63.9375rem) */
        @media (min-width: 48rem) and (max-width: 63.9375rem) {
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mobileTopBar {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mobileMenuToggle {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #dashboardMenuToggle {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #pageMobileMenuToggle {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebarCloseBtn {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebarOverlay {
                display: none !important;
                pointer-events: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header.sticky,
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #desk-officer-dashboard-header,
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #barangay-cases-view-header {
                background: #ffffff !important;
                border-bottom: 1px solid #e5e7eb !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header :is(h1, h2, h3) {
                color: #1f2937 !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #mainContent>header p {
                color: #6b7280 !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar {
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

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-header {
                display: flex !important;
                min-height: 5.5rem;
                padding-left: 0.75rem !important;
                padding-right: 0.5rem !important;
                justify-content: flex-start !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-header img {
                width: 3rem !important;
                height: 3rem !important;
                flex-shrink: 0 !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-text {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-link {
                justify-content: center !important;
                padding: 0.75rem !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar .sidebar-link i {
                margin: 0 !important;
                font-size: 1.25rem !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebarToggleBtn {
                display: none !important;
            }

            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #sidebar~#mainContent {
                margin-left: 4.5rem !important;
                width: calc(100% - 4.5rem) !important;
            }
        }

        @media (min-width: 48rem) {
            body.portal-ui:is(.desk-officer-dashboard-mobile-nav, .case-profiles-desk-mobile-nav, .view-cases-desk-mobile-nav, .desk-officer-referrals-mobile-nav, .desk-officer-calendar-mobile-nav, .desk-officer-quarterly-reports-mobile-nav, .desk-officer-archived-cases-mobile-nav, .desk-officer-bcpc-monitoring-mobile-nav) #pageMobileMenuToggle {
                display: none !important;
            }
        }

        @endif

        @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment())
        @media screen and (min-width: 48rem) and (max-width: 80rem) {
            /* Collapsed: below the logo. Expanded: centered on the sidebar's right edge. */
            body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-header {
                min-height: 7rem !important;
                padding-left: .75rem !important;
                padding-right: .75rem !important;
                padding-bottom: 3rem !important;
                justify-content: center !important;
                flex-shrink: 0;
            }

            body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-header > div {
                width: 100%;
                justify-content: center !important;
                gap: 0;
            }

            body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-header .sidebar-text {
                display: none !important;
            }

            body.portal-ui #sidebar ~ #sidebarToggleBtn {
                display: flex !important;
                top: 2.75rem !important;
                left: 18rem !important;
                transform: translate(-50%, -50%) !important;
            }

            body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) ~ #sidebarToggleBtn {
                top: 5.5rem !important;
                left: 2.25rem !important;
            }
        }
        @endif
        @media (max-width: 47.9375rem) {
            body.portal-ui {
                overflow-x: clip !important;
                overflow-y: visible !important;
            }

            body.portal-ui #mainContent > header {
                position: sticky !important;
                top: 0 !important;
                z-index: 30;
            }
        }
    </style>

    <script>
        document.documentElement.classList.add('js');
    </script>
    @if(request()->routeIs('desk-officer.dashboard', 'desk-officer.bcpc.dashboard', 'case-profiles.index', 'desk-officer.cases.index', 'desk-officer.cases.create', 'desk-officer.cases.show', 'desk-officer.referrals', 'desk-officer.referrals.archived', 'desk-officer.calendar*', 'desk-officer.quarterly-reports.index', 'desk-officer.quarterly-reports.create', 'desk-officer.quarterly-reports.show', 'desk-officer.quarterly-reports.edit', 'desk-officer.quarterly-reports.archived', 'desk-officer.bcpc-case-monitoring.edit', 'desk-officer.archived-barangay-cases.index'))
    <link rel="stylesheet"
        href="{{ asset('css/bcpc-dashboard-portrait-sidebar.css') }}?v={{ filemtime(public_path('css/bcpc-dashboard-portrait-sidebar.css')) }}"
        media="screen and (min-width: 48rem) and (max-width: 80rem) and (orientation: portrait)">
    @endif
    <link rel="stylesheet"
        href="{{ asset('css/portal-modals.css') }}?v={{ filemtime(public_path('css/portal-modals.css')) }}">
    <script
        src="{{ asset('js/archive-case-modal-design.js') }}?v={{ filemtime(public_path('js/archive-case-modal-design.js')) }}"
        defer></script>
    <script src="{{ asset('js/portal-modal-close.js') }}?v={{ filemtime(public_path('js/portal-modal-close.js')) }}"
        defer></script>
    <link rel="stylesheet"
        href="{{ asset('css/logout-modal.css') }}?v={{ filemtime(public_path('css/logout-modal.css')) }}">
    <script
        src="{{ asset('js/portal-modal-presentation.js') }}?v={{ filemtime(public_path('js/portal-modal-presentation.js')) }}"
        defer></script>
    <style>
        @media (min-width: 48rem) and (max-width: 80rem) {
            html.tablet-sidebar-loading body.portal-ui #sidebar#sidebar {
                width: 4.5rem !important;
                min-width: 4.5rem !important;
                max-width: 4.5rem !important;
            }
            html.tablet-sidebar-loading body.portal-ui #sidebar#sidebar,
            html.tablet-sidebar-loading body.portal-ui #sidebar#sidebar *,
            html.tablet-sidebar-loading body.portal-ui #sidebarToggleBtn#sidebarToggleBtn,
            html.tablet-sidebar-loading body.portal-ui #sidebarToggleBtn#sidebarToggleBtn *,
            html.tablet-sidebar-loading body.portal-ui #sidebarOverlay#sidebarOverlay,
            html.tablet-sidebar-loading body.portal-ui #mainContent#mainContent {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>
    @if(auth()->user()?->position === 'Desk Officer' && (auth()->user()->canAccessCaseDepartment('BCPC') || auth()->user()->canAccessCaseDepartment('VAWC')))
    <link rel="stylesheet" href="{{ asset('css/desk-officer-tablet-portrait-sidebar.css') }}?v={{ filemtime(public_path('css/desk-officer-tablet-portrait-sidebar.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/desk-officer-tablet-landscape-sidebar.css') }}?v={{ filemtime(public_path('css/desk-officer-tablet-landscape-sidebar.css')) }}">
    @endif
</head>

<body
    class="portal-ui {{ request()->routeIs('desk-officer.dashboard', 'desk-officer.bcpc.dashboard') ? 'desk-officer-dashboard-mobile-nav' : '' }} {{ request()->routeIs('case-profiles.index') ? 'case-profiles-desk-mobile-nav' : '' }} {{ (request()->routeIs('admin.barangay-cases.show', 'admin-barangay-cases', 'desk-officer.cases.*') || request()->is('desk-officer/cases*', 'admin-barangay-cases*')) ? 'view-cases-desk-mobile-nav' : '' }} {{ request()->routeIs('desk-officer.referrals*') ? 'desk-officer-referrals-mobile-nav' : '' }} {{ request()->routeIs('desk-officer.calendar*') ? 'desk-officer-calendar-mobile-nav' : '' }} {{ request()->routeIs('desk-officer.quarterly-reports*') ? 'desk-officer-quarterly-reports-mobile-nav' : '' }} {{ request()->routeIs('desk-officer.archived-barangay-cases*') ? 'desk-officer-archived-cases-mobile-nav' : '' }} {{ (request()->routeIs('desk-officer.bcpc-case-monitoring*') || request()->is('desk-officer/bcpc-case-monitoring*')) ? 'desk-officer-bcpc-monitoring-mobile-nav' : '' }} {{ auth()->user() && (strtolower(trim((string) auth()->user()->position)) === 'desk officer' || in_array(strtoupper((string) auth()->user()->defaultCaseDepartment()), ['BCPC', 'VAWC'], true)) ? 'desk-officer-header-theme' : '' }} font-['Raleway','Montserrat',sans-serif] bg-gray-100 text-gray-600 min-h-screen antialiased overflow-x-hidden"
    style="--portal-user-initials: '{{ strtoupper(mb_substr((string) auth()->user()?->first_name, 0, 1) . mb_substr((string) auth()->user()?->last_name, 0, 1)) }}';">
    @php
    $passwordResetNotice = session('password_reset_notice');
    $passwordResetNoticeSeen = session('password_reset_notice_popup_displayed', false);
    $hasPasswordResetNotice = Auth::check() && $passwordResetNotice;
    if ($hasPasswordResetNotice && isset($passwordResetNotice['expires_at'])) {
    $expiresAt = \Carbon\Carbon::parse($passwordResetNotice['expires_at']);
    if (now()->greaterThan($expiresAt)) {
    $hasPasswordResetNotice = false;
    }
    }
    $deskOfficerNotifications = [];
    if ($hasPasswordResetNotice) {
    $deskOfficerNotifications[] = [
    'id' => $passwordResetNotice['id'] ?? 'password_reset_notice',
    'title' => 'Password Reset Ready',
    'message' => $passwordResetNotice['message'] ?? 'Your password has been reset and is ready for viewing.',
    'url' => '#',
    'email' => $passwordResetNotice['email'] ?? '',
    'new_password' => $passwordResetNotice['new_password'] ?? '',
    ];
    }
    $deskOfficerNotificationCount = count($deskOfficerNotifications);

    $caseDepartments = Auth::check() ? Auth::user()->caseDepartments() : ['VAWC'];
    $isMultiCaseDepartment = Auth::check() ? Auth::user()->isMultiCaseDepartment() : false;
    $defaultCaseDepartment = Auth::check() ? Auth::user()->defaultCaseDepartment() : 'VAWC';
    $defaultCaseType = strtolower($defaultCaseDepartment);
    $defaultDashboardRoute = $defaultCaseDepartment === 'BCPC'
    ? route('desk-officer.bcpc.dashboard')
    : route('desk-officer.dashboard');
    $isArchiveDetailPage = request()->boolean('archived') && request()->routeIs(
    'cases.show',
    'desk-officer.cases.show',
    'desk-officer.quarterly-reports.show',
    'desk-officer.bcpc-case-monitoring.edit'
    );
    $isArchiveListPage = request()->routeIs(
    'desk-officer.referrals.archived',
    'desk-officer.quarterly-reports.archived',
    'desk-officer.bcpc-case-monitoring.archived',
    'desk-officer.archived-barangay-cases.index'
    );
    $isArchivesOpen = $isArchiveListPage || $isArchiveDetailPage;
    @endphp

    @unless($isArchivedReportPreview || request()->routeIs('desk-officer.quarterly-reports.show', 'desk-officer.quarterly-reports.edit', 'desk-officer.bcpc-case-monitoring.edit'))
    <!-- Mobile Sticky Top Bar -->
    <div id="mobileTopBar"
        class="lg:hidden sticky top-0 z-30 relative bg-purple-900 text-white flex items-center justify-between px-4 py-2.5 shadow-md border-b border-purple-800">
        <div class="flex items-center gap-3">
            <button id="mobileMenuToggle"
                class="bg-purple-800/80 hover:bg-purple-700 text-white p-2 rounded-lg transition-colors focus:outline-none flex items-center justify-center w-9 h-9"
                aria-label="Toggle menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars text-base"></i>
            </button>
            <div
                class="desk-officer-mobile-brand {{ $isMultiCaseDepartment ? 'multi-role-mobile-brand flex items-center whitespace-nowrap' : 'flex items-center gap-2' }}">
                <img src="{{ asset('images/iserve_logo.jpg') }}" alt="I-serve Silang Logo"
                    class="w-8 h-8 rounded object-contain">
                <span
                    class="font-bold {{ $isMultiCaseDepartment ? 'text-sm sm:text-base' : 'text-base' }} tracking-wide">iSERVE
                    SILANG</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(Auth::check())
            <span
                class="{{ Auth::user()?->position === 'Desk Officer' && (Auth::user()?->isMultiCaseDepartment() || in_array(Auth::user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)) ? 'desk-officer-tablet-role-badge' : '' }} bg-[#C41E3A] text-white border-[#C41E3A] lg:bg-purple-800/80 lg:text-purple-200 lg:border-purple-700 text-xs px-2.5 py-1 rounded-full border font-medium">
                {{ Auth::user()->position ?? 'Desk Officer' }}
            </span>
            @endif
        </div>
    </div>
    @endunless

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay"
        class="fixed inset-0 bg-black/50 z-30 lg:hidden opacity-0 pointer-events-none transition-opacity duration-300">
    </div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 h-screen w-72 bg-[#C41E3A] text-white z-40
               lg:translate-x-0 flex flex-col shadow-2xl
               rounded-r-3xl lg:rounded-none">
        @if($persistsDeskOfficerSidebar)
        <script>
            if (document.documentElement.classList.contains('bcpc-desk-sidebar-collapsed')) {
                document.getElementById('sidebar')?.classList.add('sidebar-collapsed');
            }
        </script>
        @endif
        <!-- Sidebar Header -->
        <div class="sidebar-header py-5 border-b border-white">
            <div class="flex items-center justify-center gap-3 {{ $isMultiCaseDepartment ? 'w-full' : '' }}">
                <img src="{{ asset('images/iserve_logo.jpg') }}" alt="I-serve Silang Logo"
                    class="w-12 h-12 min-w-[3rem] rounded-lg object-contain">
                <div class="sidebar-text whitespace-nowrap overflow-hidden leading-tight">
                    <h1 class="text-xl font-bold tracking-wide">iSERVE SILANG</h1>
                    <p class="mt-1 text-[10px] font-medium tracking-[.12em] text-white/70">SWD &amp; BCPC PORTAL</p>
                </div>
            </div>
        </div>

        <!-- Mobile Close Button - positioned outside sidebar flow -->
        <button id="sidebarCloseBtn"
            class="lg:hidden absolute -right-4 top-6 w-8 h-8 bg-purple-600 rounded-full shadow-lg flex items-center justify-center text-white hover:bg-purple-700"
            aria-label="Close sidebar" aria-controls="sidebar">
            <i class="fas fa-chevron-left text-sm"></i>
        </button>

        <!-- Sidebar Menu -->
        <nav class="flex-1 overflow-y-auto py-4 sidebar-menu">
            <!-- Dashboard -->
            @if($isMultiCaseDepartment)
            @php
            $isBcpcDashboardActive = request()->routeIs('desk-officer.bcpc.dashboard')
            || (request()->routeIs('desk-officer.dashboard')
            && strtoupper((string) request('type', $defaultCaseDepartment)) === 'BCPC');
            $isSwdDashboardActive = request()->routeIs('desk-officer.dashboard') && !$isBcpcDashboardActive;
            $isDashboardActive = $isBcpcDashboardActive || $isSwdDashboardActive;
            @endphp
            <div x-data="{ dashboardOpen: {{ $isDashboardActive ? 'true' : 'false' }} }"
                @sidebar-dropdown-open.window="if ($event.detail !== 'dashboardOpen') dashboardOpen = false"
                class="mb-1">
                <button type="button"
                    @click="dashboardOpen = !dashboardOpen; if (dashboardOpen) $dispatch('sidebar-dropdown-open', 'dashboardOpen')"
                    class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all duration-200 text-left
                               {{ $isDashboardActive 
                                  ? 'bg-purple-700/70 text-white shadow-sm font-medium' 
                                  : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
                    data-tooltip="Dashboard" title="Dashboard">
                    <div class="flex items-center gap-3 min-w-0">
                        <i class="fas fa-tachometer-alt w-5 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">Dashboard</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs sidebar-text transition-transform duration-200"
                        :class="{ 'rotate-180': dashboardOpen }"></i>
                </button>
                <div x-show="dashboardOpen" x-cloak class="mt-1 pl-6 pr-2 space-y-1">
                    <a href="{{ route('desk-officer.bcpc.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ $isBcpcDashboardActive
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold' 
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                        title="BCPC Dashboard">
                        <i class="fas fa-child text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">BCPC Dashboard</span>
                    </a>
                    <a href="{{ route('desk-officer.dashboard', ['type' => 'VAWC']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ $isSwdDashboardActive
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold' 
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}" title="SWD Dashboard">
                        <i class="fas fa-shield-alt text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">SWD Dashboard</span>
                    </a>
                </div>
            </div>
            @else
            <a href="{{ $defaultDashboardRoute }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('desk-officer.dashboard') || request()->routeIs('desk-officer.bcpc.dashboard') 
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}" data-tooltip="Dashboard"
                title="Dashboard">
                <i class="fas fa-tachometer-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Dashboard</span>
            </a>
            @endif

            @php
            $isDeskOfficerBarangayCasesRoute = Auth::user()?->position === 'Desk Officer' &&
            in_array($defaultCaseDepartment, ['BCPC', 'VAWC'], true) && !Auth::user()?->isMultiCaseDepartment() &&
            (request()->routeIs('admin-barangay-cases', 'admin.barangay-cases.*') ||
            request()->is('admin-barangay-cases*'));
            $isAddNewCaseActive = request()->routeIs('desk-officer.cases.create', 'admin-add-new-case') ||
            request()->is('desk-officer/cases/create*');
            $isCaseProfileActive = request()->routeIs('case-profiles.*') || request()->is('case-profiles*');
            $isViewCasesActive = !$isAddNewCaseActive && !$isCaseProfileActive && !$isArchiveDetailPage &&
            (request()->routeIs('desk-officer.cases.index', 'desk-officer.cases.*') ||
            request()->is('desk-officer/cases*') || $isDeskOfficerBarangayCasesRoute);
            $isManageCasesActive = $isCaseProfileActive || $isViewCasesActive || $isAddNewCaseActive;
            @endphp
            <div x-data="{ manageCasesOpen: {{ $isManageCasesActive ? 'true' : 'false' }} }"
                @sidebar-dropdown-open.window="if ($event.detail !== 'manageCasesOpen') manageCasesOpen = false"
                class="mb-1">
                <button type="button"
                    @click="manageCasesOpen = !manageCasesOpen; if (manageCasesOpen) $dispatch('sidebar-dropdown-open', 'manageCasesOpen')"
                    class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-lg {{ $isManageCasesActive ? 'bg-purple-600 text-white shadow-sm font-medium' : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
                    title="Manage Cases">
                    <span class="flex items-center gap-3"><i class="fas fa-gavel w-5 text-center"></i><span
                            class="sidebar-text">Manage Cases</span></span><i
                        class="fas fa-chevron-down text-xs sidebar-text" :class="{ 'rotate-180': manageCasesOpen }"></i>
                </button>
                <div x-show="manageCasesOpen" x-cloak class="mt-1 pl-6 pr-2 space-y-1">
                    <a href="{{ route('case-profiles.index') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium {{ $isCaseProfileActive ? 'bg-purple-600 text-white shadow-sm font-semibold' : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"><i
                            class="fas fa-address-card" aria-hidden="true"></i><span class="sidebar-text">Case
                            Profile</span></a>
                    <a href="{{ route('desk-officer.cases.index') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium {{ $isViewCasesActive ? 'bg-purple-600 text-white shadow-sm font-semibold' : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"><i
                            class="fas fa-folder-open" aria-hidden="true"></i><span class="sidebar-text">View
                            Cases</span></a>
                    @if($isMultiCaseDepartment)
                    <button type="button" onclick="openCaseTypeModal()"
                        class="flex items-center gap-2.5 w-full text-left px-3 py-2 rounded-lg text-sm font-medium {{ $isAddNewCaseActive ? 'bg-purple-600 text-white shadow-sm font-semibold' : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"><i
                            class="fas fa-plus-circle" aria-hidden="true"></i><span class="sidebar-text">Add New
                            Case</span></button>
                    @else
                    <a href="{{ route('desk-officer.cases.create') }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium {{ $isAddNewCaseActive ? 'bg-purple-600 text-white shadow-sm font-semibold' : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"><i
                            class="fas fa-plus-circle" aria-hidden="true"></i><span class="sidebar-text">Add New
                            Case</span></a>
                    @endif
                </div>
            </div>
            <!-- Referrals -->
            <a href="{{ route('desk-officer.referrals') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('desk-officer.referrals*') && !request()->routeIs('desk-officer.referrals.archived')
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}" data-tooltip="Referrals"
                title="Referrals">
                <i class="fas fa-exchange-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Referrals</span>
            </a>

            <!-- Calendar -->
            <a href="{{ route('desk-officer.calendar.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('desk-officer.calendar*') 
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}" data-tooltip="Calendar"
                title="Calendar">
                <i class="fas fa-calendar-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Calendar</span>
            </a>

            <!-- Quarterly Reports -->
            <a href="{{ route('desk-officer.quarterly-reports.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ (request()->routeIs('desk-officer.quarterly-reports*') && !$isArchiveListPage && !$isArchiveDetailPage)
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
                data-tooltip="Quarterly Reports" title="Quarterly Reports">
                <i class="fas fa-file-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Quarterly Reports</span>
            </a>

            @if(in_array('BCPC', $caseDepartments, true))
            <!-- BCPC Case Monitoring -->
            <a href="{{ route('desk-officer.bcpc-case-monitoring.edit') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ (request()->routeIs('desk-officer.bcpc-case-monitoring*') && !$isArchiveListPage && !$isArchiveDetailPage)
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
                data-tooltip="BCPC Case Monitoring" title="BCPC Case Monitoring">
                <i class="fas fa-child w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">BCPC Monitoring</span>
            </a>
            @endif

            @if(Auth::user()?->position === 'Desk Officer' && in_array($defaultCaseDepartment, ['VAWC', 'BCPC'], true))
            <div x-data="{ archiveOpen: {{ $isArchivesOpen ? 'true' : 'false' }} }"
                @sidebar-dropdown-open.window="if ($event.detail !== 'archiveOpen') archiveOpen = false" class="mb-1">
                <button type="button"
                    @click="archiveOpen = !archiveOpen; if (archiveOpen) $dispatch('sidebar-dropdown-open', 'archiveOpen')"
                    :aria-expanded="archiveOpen"
                    class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-lg {{ $isArchivesOpen ? 'bg-purple-600 text-white shadow-sm font-medium' : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
                    title="Archives" data-tooltip="Archives">
                    <span class="flex items-center gap-3"><i class="fas fa-archive w-5 text-center"></i><span
                            class="sidebar-text">Archives</span></span><i
                        class="fas fa-chevron-down text-xs sidebar-text" :class="{ 'rotate-180': archiveOpen }"></i>
                </button>
                <div x-show="archiveOpen" x-cloak class="mt-1 pl-6 pr-2 space-y-1">
                    @foreach([
                    'desk-officer.archived-barangay-cases.index' => ['Archived Barangay Cases', 'fa-map-marked-alt'],
                    'desk-officer.quarterly-reports.archived' => ['Archived Reports', 'fa-chart-pie'],
                    'desk-officer.referrals.archived' => ['Archived Referrals', 'fa-paper-plane'],
                    ] as $archiveRoute => [$archiveLabel, $archiveIcon])
                    <a href="{{ route($archiveRoute) }}"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs($archiveRoute) ? 'bg-purple-600 text-white shadow-sm font-semibold' : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"><i
                            class="fas {{ $archiveIcon }} w-4 text-center"></i><span class="sidebar-text">{{
                            $archiveLabel }}</span></a>
                    @endforeach
                </div>
            </div>
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="py-4 sidebar-menu">
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden" data-skip-loading-modal>
                @csrf
            </form>
            <button type="button" id="logout-button" class="sidebar-link flex items-center gap-3 w-full py-3 rounded-lg 
                       text-purple-100 hover:bg-red-500/20 hover:text-red-300 
                       transition-all duration-200" title="Logout">
                <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Logout</span>
            </button>
        </div>
    </aside>

    <!-- Floating Sidebar Toggle Button (Desktop Only) -->
    <button id="sidebarToggleBtn" class="hidden lg:flex items-center justify-center w-6 h-6 bg-white rounded-full 
               shadow-lg shadow-gray-600/60 hover:shadow-xl hover:bg-gray-50 
               transition-all duration-200 border border-gray-300" aria-label="Toggle sidebar" aria-controls="sidebar"
        aria-expanded="true" title="Toggle sidebar">
        <i class="fas fa-chevron-left text-gray-600 text-xs transition-transform duration-300"></i>
    </button>

    <!-- Main Content -->
    <div id="mainContent" data-page-title="@yield('title', '')"
        class="lg:ml-72 min-h-screen sidebar-transition overflow-x-hidden">
        @yield('content')
    </div>
    @unless($isArchivedReportPreview)
    @include('components.portal-clock')
    @endunless

    <!-- Notification dropdown modal (desk-officer) -->
    @if($deskOfficerNotificationCount > 0)
    <div id="desk-officer-notification-dropdown"
        class="hidden fixed top-16 left-4 lg:left-72 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
        <div
            class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-purple-700 to-purple-600 text-white">
            <h4 class="font-semibold text-sm">Notifications</h4>
        </div>
        <div class="max-h-72 overflow-y-auto">
            @foreach($deskOfficerNotifications as $notification)
            <a href="javascript:void(0)"
                onclick="showPasswordResetModal('{{ $notification['email'] }}', '{{ $notification['new_password'] }}')"
                class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $notification['title'] }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $notification['message'] }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Modals -->
    @stack('modals')

    @if(Auth::check() && Auth::user()->isMultiCaseDepartment())
    <!-- Case Type Modal -->
    <dialog id="caseTypeModal" aria-labelledby="caseTypeModalTitle"
        class="m-auto p-0 border-0 rounded-xl bg-transparent backdrop:bg-black/50">
        <div class="mx-auto w-full max-w-sm bg-white rounded-xl shadow-xl p-4 sm:p-6">
            <h2 id="caseTypeModalTitle" class="text-xl font-bold text-center mb-2">Create New Case</h2>
            <p class="text-gray-500 text-center mb-6">Select the type of case you want to create.</p>
            <div class="space-y-4">
                @if(in_array('BCPC', $caseDepartments, true))
                <a href="{{ route('desk-officer.cases.create', ['type' => 'bcpc']) }}"
                    class="block w-full bg-red-600 hover:bg-red-700 text-white text-center py-3 rounded-lg font-semibold transition text-decoration-none">
                    <i class="fas fa-child mr-2"></i> BCPC Case
                </a>
                @endif
                @if(in_array('VAWC', $caseDepartments, true))
                <a href="{{ route('desk-officer.cases.create', ['type' => 'vawc']) }}"
                    class="block w-full bg-purple-700 hover:bg-purple-800 text-white text-center py-3 rounded-lg font-semibold transition text-decoration-none">
                    <i class="fas fa-shield-alt mr-2"></i> SWD Case
                </a>
                @endif
            </div>
            <button type="button" onclick="closeCaseTypeModal()"
                class="mt-5 w-full border border-gray-300 rounded-lg py-2 hover:bg-gray-100 text-gray-700">
                Cancel
            </button>
        </div>
    </dialog>

    <!-- Report Type Modal -->
    <div id="reportTypeModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-96 p-6">
            <h2 class="text-xl font-bold text-center mb-2">Generate Report</h2>
            <p class="text-gray-500 text-center mb-6">Select the type of report you want to create.</p>
            <div class="space-y-4">
                @if(in_array('BCPC', $caseDepartments, true))
                <a href="{{ route('desk-officer.quarterly-reports.create', ['type' => 'bcpc']) }}"
                    class="block w-full bg-red-600 hover:bg-red-700 text-white text-center py-3 rounded-lg font-semibold transition text-decoration-none">
                    <i class="fas fa-child mr-2"></i> Create BCPC Report
                </a>
                @endif
                @if(in_array('VAWC', $caseDepartments, true))
                <a href="{{ route('desk-officer.quarterly-reports.create', ['type' => 'vawc']) }}"
                    class="block w-full bg-purple-700 hover:bg-purple-800 text-white text-center py-3 rounded-lg font-semibold transition text-decoration-none">
                    <i class="fas fa-shield-alt mr-2"></i> Create SWD Report
                </a>
                @endif
            </div>
            <button type="button" onclick="closeReportTypeModal()"
                class="mt-5 w-full border border-gray-300 rounded-lg py-2 hover:bg-gray-100 text-gray-700">
                Cancel
            </button>
        </div>
    </div>
    @endif

    @php
    $showSwdQuarterlyResult = request()->routeIs('desk-officer.quarterly-reports.*')
    && !request()->routeIs('desk-officer.quarterly-reports.archived')
    && auth()->user()?->position === 'Desk Officer'
    && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)
    && !auth()->user()?->isMultiCaseDepartment()
    && (session('success') || session('error'));
    $showBcpcCaseArchivedResult = request()->routeIs('desk-officer.cases.index')
    && auth()->user()?->position === 'Desk Officer'
    && auth()->user()?->defaultCaseDepartment() === 'BCPC'
    && !auth()->user()?->isMultiCaseDepartment()
    && session('bcpc_case_archived_success');
    @endphp
    @if($showSwdQuarterlyResult)
    <div id="quarterly-report-success" role="alertdialog" aria-modal="true" aria-labelledby="quarterly-success-title"
        aria-describedby="quarterly-success-message"
        style="position: fixed; inset: 0; z-index: 10000; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, .45); padding: 1rem;">
        <div class="bg-white rounded-xl shadow-xl p-8 text-center" style="width: 32rem; max-width: 100%;">
            <i class="fas {{ session('success') ? 'fa-check-circle text-green-500' : 'fa-info-circle text-blue-500' }} text-5xl mb-4"
                aria-hidden="true"></i>
            <h2 id="quarterly-success-title" class="text-2xl font-semibold text-gray-800 mb-3">{{ session('success') ?
                'Success!' : 'Nothing to Update' }}</h2>
            <p id="quarterly-success-message" class="text-gray-600 mb-6">{{ session('success') ?: session('error') }}
            </p>
            <button type="button" autofocus
                class="px-6 py-2 rounded-lg {{ auth()->user()?->defaultCaseDepartment() === 'BCPC' ? 'bg-[#C41E3A] hover:bg-[#A01830]' : 'bg-purple-700' }} text-white font-medium"
                onclick="document.getElementById('quarterly-report-success').remove()">OK</button>
        </div>
    </div>
    @endif
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if((session('success') || session('error')) && !$showSwdQuarterlyResult && !$showBcpcCaseArchivedResult)
    <script>
        const showFlashModal = function () {
        if (!window.Swal) return;
        window.Swal.fire({
            icon: @json(session('success') ? 'success' : 'error'),
            title: @json(session('success') ? 'Success' : 'Error'),
            text: @json(session('success') ?: session('error')),
            confirmButtonColor: @json(request()->routeIs('desk-officer.quarterly-reports.archived') || (request()->routeIs('desk-officer.quarterly-reports.*') && auth()->user()?->defaultCaseDepartment() === 'BCPC') ? '#C41E3A' : '#7e22ce'),
            confirmButtonText: 'OK',
            showConfirmButton: true
        });
    };
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', showFlashModal) : showFlashModal();
    </script>
    @endif

    <script>
        (function () {
        const nativePrint = window.print.bind(window);
        window.print = function () {
            if (!window.Swal) return nativePrint();
            window.Swal.fire({
                title: 'Print Preview',
                text: 'Review the print preview before confirming printing.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Continue to Preview',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#7e22ce'
            }).then((result) => {
                if (result.isConfirmed) nativePrint();
            });
        };

        const isSubmittedAction = (form) => {
            const method = (form.getAttribute('method') || 'get').toLowerCase();
            return !form.hasAttribute('data-skip-loading-modal') && !['get', 'dialog'].includes(method);
        };

        const showActionLoading = () => {
            if (!window.Swal || window.Swal.isVisible()) return;
            window.Swal.fire({
                title: 'Processing...',
                text: 'Please wait while your action is being completed.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => window.Swal.showLoading()
            });
        };

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !isSubmittedAction(form)) return;

            window.setTimeout(() => {
                if (!event.defaultPrevented) showActionLoading();
            }, 0);
        });

        const nativeSubmit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = function () {
            if (isSubmittedAction(this)) showActionLoading();
            return nativeSubmit.call(this);
        };
    }());

    function openReportTypeModal() {
        const modal = document.getElementById('reportTypeModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeReportTypeModal() {
        const modal = document.getElementById('reportTypeModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function openCaseTypeModal() {
        const modal = document.getElementById('caseTypeModal');
        if (modal) {
            modal.showModal();
        }
    }

    function closeCaseTypeModal() {
        const modal = document.getElementById('caseTypeModal');
        if (modal) {
            modal.close();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(request('select_case_type'))
        openCaseTypeModal();
        @endif
        const reportModal = document.getElementById('reportTypeModal');
        if (reportModal) {
            reportModal.addEventListener('click', function(e) {
                if (e.target === reportModal) {
                    closeReportTypeModal();
                }
            });
        }
        const caseModal = document.getElementById('caseTypeModal');
        if (caseModal) {
            caseModal.addEventListener('click', function(e) {
                if (e.target === caseModal) {
                    caseModal.close();
                }
            });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReportTypeModal();
            const caseModal = document.getElementById('caseTypeModal');
            if (caseModal) {
                caseModal.close();
            }
        }
    });
    function showPasswordResetModal(email, password) {
        const badge = document.getElementById('desk-officer-notification-badge');
        if (badge) {
            badge.style.display = 'none';
        }
        fetch('{{ route('password.reset.notice.mark-seen') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        });
        Swal.fire({
            icon: 'info',
            title: 'Password Reset Ready',
            html: '<div style="text-align: left;"><p style="margin-bottom: 10px;"><strong>Email:</strong> ' + email + '</p><p><strong>New Password:</strong> ' + password + '</p></div>',
            confirmButtonText: 'Continue',
            confirmButtonColor: '#b91c1c'
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const pageMobileMenuToggle = document.getElementById('pageMobileMenuToggle');
        const dashboardMenuToggle = document.getElementById('dashboardMenuToggle');
        const mobileMenuToggles = [mobileMenuToggle, pageMobileMenuToggle, dashboardMenuToggle].filter(Boolean);
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
        const sidebarStateKey = @json($deskOfficerSidebarStateKey);
        const isQuarterlyReportDetail = @json(request()->routeIs('desk-officer.quarterly-reports.show'));
        const isDashboardDrawer = () => ((isQuarterlyReportDetail || document.body.classList.contains('desk-officer-dashboard-mobile-nav') || document.body.classList.contains('case-profiles-desk-mobile-nav') || document.body.classList.contains('view-cases-desk-mobile-nav') || document.body.classList.contains('desk-officer-referrals-mobile-nav') || document.body.classList.contains('desk-officer-calendar-mobile-nav') || document.body.classList.contains('desk-officer-quarterly-reports-mobile-nav') || document.body.classList.contains('desk-officer-archived-cases-mobile-nav') || document.body.classList.contains('desk-officer-bcpc-monitoring-mobile-nav') || document.body.classList.contains('desk-officer-create-case-mobile-nav') || document.body.classList.contains('desk-officer-cases-view-mobile-nav') || document.body.classList.contains('desk-officer-page-mobile-nav')) && window.matchMedia('(max-width: 47.9375rem)').matches);
        
        @if($persistsDeskOfficerSidebar)
        if (sidebar) sidebar.classList.toggle('sidebar-collapsed', window.matchMedia('(min-width: 48rem) and (max-width: 80rem)').matches || localStorage.getItem(sidebarStateKey) === 'true');
        @elseif(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->isMultiCaseDepartment() || in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
        if (sidebar) sidebar.classList.add('sidebar-collapsed');
        localStorage.setItem(sidebarStateKey, 'true');
        @else
        localStorage.setItem(sidebarStateKey, 'false');
        @endif

        if (sidebarToggleBtn && sidebar) {
            sidebarToggleBtn.setAttribute('aria-expanded', String(!sidebar.classList.contains('sidebar-collapsed')));
        }

        // Back/forward navigation can restore an expanded sidebar from the page cache.
        window.addEventListener('pageshow', function(event) {
            if (!event.persisted || !window.matchMedia('(min-width: 48rem) and (max-width: 80rem)').matches) return;
            sidebar?.classList.add('sidebar-collapsed');
            sidebar?.classList.remove('sidebar-open');
            document.documentElement.classList.add('bcpc-desk-sidebar-collapsed');
            sidebarToggleBtn?.setAttribute('aria-expanded', 'false');
            closeMobileSidebar();
        });

        function collapseSidebar() {
            if (sidebar?.classList.contains('sidebar-collapsed')) return;
            if (sidebar) sidebar.classList.add('sidebar-collapsed');
            if (sidebarToggleBtn) sidebarToggleBtn.setAttribute('aria-expanded', 'false');
            localStorage.setItem(sidebarStateKey, 'true');
            @if($persistsDeskOfficerSidebar)
            document.documentElement.classList.add('bcpc-desk-sidebar-collapsed');
            @endif
            window.dispatchEvent(new CustomEvent('sidebar-toggle', { detail: { collapsed: true } }));
            window.dispatchEvent(new Event('resize'));
            setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 320);
        }
        
        function expandSidebar() {
            document.documentElement.classList.remove('tablet-sidebar-loading');
            if (sidebar) sidebar.classList.remove('sidebar-collapsed');
            if (sidebarToggleBtn) sidebarToggleBtn.setAttribute('aria-expanded', 'true');
            localStorage.setItem(sidebarStateKey, 'false');
            @if($persistsDeskOfficerSidebar)
            document.documentElement.classList.remove('bcpc-desk-sidebar-collapsed');
            @endif
            const initialStyle = document.getElementById('sidebar-initial-collapsed-style');
            if (initialStyle) {
                initialStyle.remove();
            }
            document.documentElement.classList.remove('sidebar-is-collapsed');
            window.dispatchEvent(new CustomEvent('sidebar-toggle', { detail: { collapsed: false } }));
            window.dispatchEvent(new Event('resize'));
            setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 320);
        }
        
        // Floating toggle button click
        @if(auth()->user()?->position === 'Desk Officer' && (auth()->user()->canAccessCaseDepartment('BCPC') || auth()->user()->canAccessCaseDepartment('VAWC')))
        sidebar?.addEventListener('click', function(event) {
            if (window.matchMedia('(min-width: 48rem) and (max-width: 85.375rem)').matches
                && event.target.closest('nav a[href], #logout-button')) collapseSidebar();
        });
        @endif
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('sidebar-collapsed')) {
                    expandSidebar();
                } else {
                    collapseSidebar();
                }
            });
        }
        
        // Selecting the current tablet page dismisses navigation without reloading it.
        sidebar?.addEventListener('click', function(event) {
            if (!window.matchMedia('(min-width: 48rem) and (max-width: 80rem)').matches
                || event.defaultPrevented || event.button !== 0
                || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            const link = event.target.closest('a[href]');
            if (!link || !sidebar.contains(link) || link.hasAttribute('download')
                || (link.target && link.target !== '_self')) return;
            const destination = new URL(link.href, location.href);
            const current = new URL(location.href);
            if (destination.origin !== current.origin || destination.pathname !== current.pathname
                || destination.hash) return;
            const defaultType = @json(strtolower(auth()->user()?->defaultCaseDepartment() ?? 'vawc'));
            const destinationType = (destination.searchParams.get('type') || defaultType).toLowerCase();
            const currentType = (current.searchParams.get('type') || defaultType).toLowerCase();
            if (destinationType !== currentType) return;
            event.preventDefault();
            if (!sidebar.classList.contains('sidebar-collapsed')
                && !sidebar.classList.contains('sidebar-collapsed-init')) {
                collapseSidebar();
            }
        });

        // Mobile toggle - open sidebar
        if (mobileMenuToggles.length) {
            mobileMenuToggles.forEach(function(menuToggle) {
                menuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (isDashboardDrawer() && sidebar && sidebar.classList.contains('sidebar-open')) {
                        closeMobileSidebar();
                        return;
                    }
                    if (sidebar) sidebar.classList.add('sidebar-open');
                    mobileMenuToggles.forEach(toggle => toggle.setAttribute('aria-expanded', 'true'));
                    if (isDashboardDrawer()) {
                        document.body.style.overflow = 'hidden';
                        document.documentElement.style.setProperty('overflow-y', 'hidden', 'important');
                        mobileMenuToggles.forEach(function(menuToggle) {
                            const icon = menuToggle.querySelector('i');
                            if (icon) {
                                icon.classList.remove('fa-bars');
                                icon.classList.add('fa-times', 'fa-xmark');
                            }
                        });
                        const dashboardHeader = document.getElementById('desk-officer-dashboard-header')
                            || document.getElementById('desk-officer-create-case-header')
                            || document.getElementById('barangay-cases-view-header')
                            || document.querySelector('#mainContent > header.sticky')
                            || document.querySelector('#mainContent > header');
                        if (dashboardHeader && sidebar) {
                            const headerBottom = Math.max(0, dashboardHeader.getBoundingClientRect().bottom);
                            sidebar.style.setProperty('top', `${headerBottom}px`, 'important');
                            sidebar.style.setProperty('max-height', `calc(100vh - ${headerBottom}px)`, 'important');
                        }
                    } else {
                        if (sidebarOverlay) sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
                    }
                });
            });
        }
        
        // Function to close mobile sidebar
        function closeMobileSidebar() {
            if (sidebar) sidebar.classList.remove('sidebar-open');
            mobileMenuToggles.forEach(function(toggle) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open navigation menu');
            });
            if (isDashboardDrawer()) {
                document.body.style.overflow = '';
                document.documentElement.style.removeProperty('overflow-y');
                mobileMenuToggles.forEach(function(toggle) {
                    const icon = toggle.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times', 'fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                });
                if (sidebar) {
                    sidebar.style.removeProperty('top');
                    sidebar.style.removeProperty('max-height');
                }
            }
            if (sidebarOverlay) sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                if (window.matchMedia('(min-width: 48rem) and (max-width: 80rem) and (orientation: portrait)').matches
                    && document.querySelector('link[href*="bcpc-dashboard-portrait-sidebar.css"]')) {
                    collapseSidebar();
                    return;
                }
                closeMobileSidebar();
            });
        }
        
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', closeMobileSidebar);
        }

        document.addEventListener('click', function(event) {
            if (isDashboardDrawer() && sidebar?.classList.contains('sidebar-open')) {
                const isToggleClick = mobileMenuToggles.some(t => t.contains(event.target));
                if (!sidebar.contains(event.target) && !isToggleClick) {
                    closeMobileSidebar();
                }
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape'
                && window.matchMedia('(min-width: 48rem) and (max-width: 80rem) and (orientation: portrait)').matches
                && document.querySelector('link[href*="bcpc-dashboard-portrait-sidebar.css"]')
                && sidebar && !sidebar.classList.contains('sidebar-collapsed')) {
                collapseSidebar();
                return;
            }
            if (event.key === 'Escape' && sidebar?.classList.contains('sidebar-open')) {
                closeMobileSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.matchMedia('(min-width: 80.0625rem)').matches) closeMobileSidebar();
            if ((document.body.classList.contains('desk-officer-dashboard-mobile-nav') || document.body.classList.contains('case-profiles-desk-mobile-nav') || document.body.classList.contains('view-cases-desk-mobile-nav') || document.body.classList.contains('desk-officer-referrals-mobile-nav') || document.body.classList.contains('desk-officer-calendar-mobile-nav') || document.body.classList.contains('desk-officer-quarterly-reports-mobile-nav') || document.body.classList.contains('desk-officer-archived-cases-mobile-nav') || document.body.classList.contains('desk-officer-bcpc-monitoring-mobile-nav')) && window.matchMedia('(min-width: 48rem)').matches) {
                closeMobileSidebar();
            }
        });

        // Notification toggle
        const notificationToggle = document.getElementById('desk-officer-notification-toggle');
        const notificationDropdown = document.getElementById('desk-officer-notification-dropdown');
        if (notificationToggle && notificationDropdown) {
            notificationToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', (event) => {
                if (!notificationToggle.contains(event.target) && !notificationDropdown.contains(event.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }
        
        // Handle logout button
        const logoutForm = document.getElementById('logout-form');
        const logoutButton = document.getElementById('logout-button');
        
        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                const mobileLogout = window.matchMedia('(max-width: 47.9375rem)').matches;
                if (mobileLogout) closeMobileSidebar();
                
                if (typeof Swal === 'undefined') {
                    if (confirm('Are you sure you want to log out?')) {
                        if (logoutForm) {
                            logoutForm.submit();
                        }
                    }
                    return;
                }
                
                Swal.fire({
                    title: 'Logout',
                    heightAuto: !mobileLogout,
                    scrollbarPadding: !mobileLogout,
        html: '<svg class="logout-modal-symbol" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><path d="M12 5l7 7-7 7v-4H7V9h5V5Z" fill="currentColor"/></svg><p class="logout-modal-message">Are you sure you want to log out?</p>',
        showCancelButton: true,
                    confirmButtonText: 'Yes, log me out',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#b91c1c',
                    cancelButtonColor: '#ef4444',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-xl app-logout-modal',
                        container: 'swal2-container-high-z'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Logging out...',
                            heightAuto: !mobileLogout,
                            scrollbarPadding: !mobileLogout,
                            html: '<div class="text-center py-4">' +
                                '<i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>' +
                                '<p class="text-gray-700">You have been successfully logged out.</p>' +
                                '</div>',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'rounded-xl'
                            },
                            willClose: () => {
                                if (logoutForm) {
                                    logoutForm.submit();
                                }
                            }
                        });
                    }
                });
            });
        }

        window.addEventListener('pageshow', function(event) {
            if (event.persisted && !window.restoringDeskCasePopup && !window.hasPendingDeskCaseReturn?.()) {
                window.location.reload();
            }
        });
    });
    </script>

    <script src="{{ asset('js/desk-officer-pagination.js') }}?v={{ filemtime(public_path('js/desk-officer-pagination.js')) }}"></script>
    @stack('scripts')
    @if(auth()->user()?->position === 'Desk Officer' && (auth()->user()->canAccessCaseDepartment('BCPC') || auth()->user()->canAccessCaseDepartment('VAWC')))
    <script src="{{ asset('js/desk-case-referral-return.js') }}?v={{ filemtime(public_path('js/desk-case-referral-return.js')) }}"></script>
    @endif
    @if(Auth::user()?->position === 'Desk Officer' && in_array($defaultCaseDepartment, ['VAWC', 'BCPC'], true) &&
    $isArchiveListPage)
    @include('desk-officer.partials.swd-archive-interactions')
    @endif
    <script src="{{ asset('js/admin-pagination-style.js') }}"></script>
    <script
        src="{{ asset('js/table-action-controls.js') }}?v={{ filemtime(public_path('js/table-action-controls.js')) }}">
    </script>
    <script src="{{ asset('js/portal-theme.js') }}?v={{ filemtime(public_path('js/portal-theme.js')) }}"></script>
    <script
        src="{{ asset('js/unsaved-changes-guard.js') }}?v={{ filemtime(public_path('js/unsaved-changes-guard.js')) }}">
    </script>
    @include('components.record-table-columns')
    @include('components.multirole-loading')
    @include('components.inactivity-timeout')
    <script
        src="{{ asset('js/contact-number-validation.js') }}?v={{ filemtime(public_path('js/contact-number-validation.js')) }}">
    </script>
</body>

</html>
