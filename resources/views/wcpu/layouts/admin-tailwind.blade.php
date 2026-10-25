 <!DOCTYPE html>
@php
    $persistsAdminSidebar = auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin();
    $adminSidebarStateKey = auth()->user()?->isBcpcAdmin()
        ? 'bcpcAdminSidebarCollapsed'
        : (auth()->user()?->isVawcAdmin() ? 'swdAdminSidebarCollapsed' : 'sidebarCollapsed');
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'iSERVE SILANG Admin')</title>
    <link rel="icon" href="{{ asset('images/iserve_logo.jpg') }}" type="image/jpeg">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Public+Sans:wght@400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite / Tailwind CSS -->
    @if(auth()->user()?->isVawcAdmin() || auth()->user()?->isBcpcAdmin())
        {!! (clone app(\Illuminate\Foundation\Vite::class))
            ->useHotFile(is_file(public_path('build/hot')) ? public_path('build/hot') : public_path('hot'))
            ->useManifestFilename(is_file(public_path('build/manifest.json')) ? 'manifest.json' : '.vite/manifest.json')
            (['resources/css/app.css', 'resources/js/app.js']) !!}
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    @if($persistsAdminSidebar)
    <script>
        try {
            document.documentElement.classList.toggle('admin-sidebar-collapsed', localStorage.getItem(@json($adminSidebarStateKey)) === 'true');
        } catch (e) {}
    </script>
    @endif
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table-action-controls.css') }}?v={{ filemtime(public_path('css/table-action-controls.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/portal-theme.css') }}?v={{ filemtime(public_path('css/portal-theme.css')) }}">
    
    <style>
        /* Hide elements with x-cloak until Alpine.js initializes */
        [x-cloak] { display: none !important; }
        /* Unify colored primary actions across BCPC/SWD admin pages. Status
           badges and informational surfaces are excluded by targeting controls. */
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
        @if(auth()->user()?->isVawcAdmin())
        html, body, #mainContent { scrollbar-width: none; }
        html::-webkit-scrollbar, body::-webkit-scrollbar, #mainContent::-webkit-scrollbar { display: none; }
        .portal-ui.swd-admin-header-theme #mainContent :is(table.archived-static-table, .archived-quarterly-table-view > table) {
            table-layout: auto !important;
            width: 100% !important;
        }
        .portal-ui.swd-admin-header-theme #mainContent .archived-quarterly-table-view > table > colgroup > col:last-child {
            width: 1% !important;
        }
        .portal-ui.swd-admin-header-theme #mainContent :is(table.archived-static-table, .archived-quarterly-table-view > table) > :is(thead, tbody) > tr > :is(th, td):last-child:not([colspan]) {
            width: 1% !important;
            min-width: 0 !important;
            max-width: none !important;
            padding-inline: .75rem !important;
            white-space: nowrap !important;
        }
        .portal-ui.swd-admin-header-theme #mainContent :is(table.archived-static-table, .archived-quarterly-table-view > table) > tbody > tr > td:last-child:not([colspan]) > div {
            display: inline-flex !important;
            width: auto !important;
            min-width: max-content !important;
            flex-wrap: nowrap !important;
        }
        @endif
        /* Native disabled controls in BCPC and SWD Admin use the shared muted
           red state. Actionable deactivate/archive controls do not match :disabled. */
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
        .portal-ui #mainContent button:disabled > :is(i, svg),
        .portal-ui #mainContent button:disabled > :is(span, .case-card-action-label) > :is(i, svg),
        .portal-ui #mainContent [role="button"][aria-disabled="true"] > :is(i, svg),
        .portal-ui #mainContent a[aria-disabled="true"]:not(#bcpc-refer-case-button) > :is(i, svg) {
            color: #B91C1C !important;
            stroke: currentColor;
        }
        .portal-ui #mainContent button:disabled,
        .portal-ui #mainContent [role="button"][aria-disabled="true"],
        .portal-ui #mainContent a[aria-disabled="true"]:not(#bcpc-refer-case-button) {
            box-shadow: 0 2px 6px rgba(196, 30, 58, .12) !important;
            opacity: 1 !important;
        }
        @if(auth()->user()?->isBcpcAdmin())
        /* BCPC Admin uses the standard red treatment for every existing View action. */
        .portal-ui #mainContent :is(a, button)[data-action="view"],
        .portal-ui #mainContent .table-action-control[title^="View"],
        .portal-ui #mainContent :is(a, button)[title^="View"] {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
        }
        .portal-ui #mainContent :is(a, button)[data-action="view"]:hover,
        .portal-ui #mainContent .table-action-control[title^="View"]:hover,
        .portal-ui #mainContent :is(a, button)[title^="View"]:hover {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
        }

        /* Keep BCPC Admin Download and Print actions in the same red action palette. */
        .portal-ui #mainContent :is(a, button)[data-action="download"],
        .portal-ui #mainContent :is(a, button)[data-action="print"],
        .portal-ui #mainContent .table-action-control[title^="Download"],
        .portal-ui #mainContent .table-action-control[title^="Print"],
        .portal-ui #mainContent :is(a, button)[title^="Download"],
        .portal-ui #mainContent :is(a, button)[title^="Print"] {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
        }
        .portal-ui #mainContent :is(a, button)[data-action="download"]:hover,
        .portal-ui #mainContent :is(a, button)[data-action="print"]:hover,
        .portal-ui #mainContent .table-action-control[title^="Download"]:hover,
        .portal-ui #mainContent .table-action-control[title^="Print"]:hover,
        .portal-ui #mainContent :is(a, button)[title^="Download"]:hover,
        .portal-ui #mainContent :is(a, button)[title^="Print"]:hover {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
        }

        /* Prevent BCPC Admin Edit and Archive actions from flashing white while interacted with. */
        .portal-ui #mainContent :is(a, button)[data-action="edit"]:not(:disabled):not([aria-disabled="true"]),
        .portal-ui #mainContent :is(a, button)[data-action="archive"]:not(:disabled):not([aria-disabled="true"]),
        .portal-ui #mainContent :is(a, button)[title^="Edit"]:not(:disabled):not([aria-disabled="true"]),
        .portal-ui #mainContent :is(a, button)[title^="Archive"]:not(:disabled):not([aria-disabled="true"]) {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
            transition: none !important;
        }
        .portal-ui #mainContent :is(a, button)[data-action="edit"]:not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[data-action="archive"]:not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Edit"]:not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Archive"]:not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active) {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
            color: #ffffff !important;
        }
        @endif
        @if(auth()->user()?->isVawcAdmin())
        /* SWD Admin uses the standard red treatment for every existing View action. */
        .portal-ui #mainContent :is(a, button)[data-action="view"],
        .portal-ui #mainContent .table-action-control[title^="View"],
        .portal-ui #mainContent :is(a, button)[title^="View"] {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
        }
        .portal-ui #mainContent :is(a, button)[data-action="view"]:hover,
        .portal-ui #mainContent .table-action-control[title^="View"]:hover,
        .portal-ui #mainContent :is(a, button)[title^="View"]:hover {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
        }

        /* Keep SWD Admin Download and Print actions in the same red action palette. */
        .portal-ui #mainContent :is(a, button)[data-action="download"],
        .portal-ui #mainContent :is(a, button)[data-action="print"],
        .portal-ui #mainContent .table-action-control[title^="Download"],
        .portal-ui #mainContent .table-action-control[title^="Print"],
        .portal-ui #mainContent :is(a, button)[title^="Download"],
        .portal-ui #mainContent :is(a, button)[title^="Print"] {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
        }
        .portal-ui #mainContent :is(a, button)[data-action="download"]:hover,
        .portal-ui #mainContent :is(a, button)[data-action="print"]:hover,
        .portal-ui #mainContent .table-action-control[title^="Download"]:hover,
        .portal-ui #mainContent .table-action-control[title^="Print"]:hover,
        .portal-ui #mainContent :is(a, button)[title^="Download"]:hover,
        .portal-ui #mainContent :is(a, button)[title^="Print"]:hover {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
        }

        /* Prevent SWD Admin Edit and Archive actions from flashing white while interacted with. */
        .portal-ui #mainContent :is(a, button)[data-action="edit"]:not(:disabled):not([aria-disabled="true"]),
        .portal-ui #mainContent :is(a, button)[data-action="archive"]:not(:disabled):not([aria-disabled="true"]),
        .portal-ui #mainContent :is(a, button)[title^="Edit"]:not(:disabled):not([aria-disabled="true"]),
        .portal-ui #mainContent :is(a, button)[title^="Archive"]:not(:disabled):not([aria-disabled="true"]) {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
            transition: none !important;
        }
        .portal-ui #mainContent :is(a, button)[data-action="edit"]:not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[data-action="archive"]:not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Edit"]:not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Archive"]:not(:disabled):not([aria-disabled="true"]):is(:hover, :focus, :active) {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
            color: #ffffff !important;
        }
        @endif
        @if(auth()->user()?->isDepartmentAdmin())
        #sidebar [x-show] > a, #sidebar [x-show] > button { font-family: inherit; font-size: .875rem; line-height: 1.25rem; font-weight: 500; }
        #sidebar [x-show] > a > i, #sidebar [x-show] > button > i { font-size: .875rem; width: 1rem; text-align: center; flex-shrink: 0; }
        @media (min-width: 64rem) {
            #sidebar.sidebar-collapsed [x-show],
            #sidebar.sidebar-collapsed-init [x-show] {
                width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            #sidebar.sidebar-collapsed [x-show] > :is(a, button),
            #sidebar.sidebar-collapsed-init [x-show] > :is(a, button) {
                justify-content: center !important;
                gap: 0 !important;
                width: 100% !important;
                min-height: 3rem !important;
                margin-left: auto !important;
                margin-right: auto !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            #sidebar.sidebar-collapsed [x-show] > :is(a, button).bg-purple-600,
            #sidebar.sidebar-collapsed-init [x-show] > :is(a, button).bg-purple-600 {
                background-color: #C41E3A !important;
                box-shadow: none !important;
            }
        }
        @endif
        @if(auth()->user()?->defaultCaseDepartment() === 'BCPC')
        /* Match the Desk Officer Profile pagination design across BCPC tables. */
        #mainContent nav [aria-current="page"] span,
        #mainContent .admin-page-control.admin-page-current,
        #mainContent [class~="bg-[#C41E3A]"] {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
            transition: none !important;
        }
        @endif
        
        /* SweetAlert2 high z-index override */
        .swal2-container-high-z { z-index: 9999 !important; }
        .swal2-container.swal2-backdrop-show { z-index: 9999 !important; }
        
        /* Custom scrollbar for sidebar */
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 2px; }
        .sidebar-menu::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.5); }

        
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
            transition: margin-left 0.3s ease;
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
        }
        
        .sidebar-link i {
            flex-shrink: 0;
            width: 1.25rem;
            min-width: 1.25rem;
            text-align: center;
        }
        
        /* Sidebar header */
        .sidebar-header {
            min-height: 5.5rem; /* Keep consistent height */
            padding-left: 0.75rem !important; /* Fixed left padding */
        }
        
        .sidebar-header > div {
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
        #sidebar > nav.sidebar-menu {
            overflow-x: hidden !important;
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
            
            #sidebar.sidebar-collapsed .sidebar-header > div {
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
            #sidebar.sidebar-collapsed > div:last-of-type {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            /* Toggle button position when collapsed */
            #sidebar.sidebar-collapsed ~ #sidebarToggleBtn {
                left: calc(4.5rem - 0.75rem);
            }
            
            #sidebar.sidebar-collapsed ~ #sidebarToggleBtn i {
                transform: rotate(180deg);
            }
            
            /* Main content margin when collapsed */
            #sidebar.sidebar-collapsed ~ #mainContent {
                margin-left: 4.5rem;
            }
        }
        
        /* Floating sidebar toggle button - on edge of sidebar */
        #sidebarToggleBtn {
            position: fixed;
            top: calc(1.25rem + 1.5rem); /* p-5 padding + half of w-12 logo */
            left: calc(18rem - 0.75rem); /* sidebar width minus half button width */
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
        #sidebar.sidebar-collapsed-init ~ #sidebarToggleBtn {
            left: calc(4.5rem - 0.75rem) !important;
            transition: none !important;
        }
        #sidebar.sidebar-collapsed-init ~ #sidebarToggleBtn i {
            transform: rotate(180deg) !important;
        }
        #sidebar.sidebar-collapsed-init ~ #mainContent {
            margin-left: 4.5rem !important;
            transition: none !important;
        }

        /* Mobile phone navigation (< 600px / < 37.5rem) */
        @media (max-width: 37.4375rem) {
            #mobileTopBar {
                display: flex !important;
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
            #sidebar.sidebar-collapsed ~ #mainContent,
            #sidebar.sidebar-collapsed-init ~ #mainContent {
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
            #sidebar.sidebar-collapsed > div:last-of-type,
            #sidebar.sidebar-collapsed-init > div:last-of-type {
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
            #sidebar.sidebar-collapsed ~ #sidebarToggleBtn,
            #sidebar.sidebar-collapsed-init ~ #sidebarToggleBtn {
                left: calc(4.5rem - 0.75rem) !important;
            }
            #sidebar.sidebar-collapsed ~ #sidebarToggleBtn i,
            #sidebar.sidebar-collapsed-init ~ #sidebarToggleBtn i {
                transform: rotate(180deg) !important;
            }
            .portal-ui #sidebar ~ #mainContent {
                width: calc(100vw - 18rem) !important;
                min-width: 0 !important;
                margin-left: 18rem !important;
                overflow-x: hidden;
            }
            .portal-ui #sidebar.sidebar-collapsed ~ #mainContent,
            .portal-ui #sidebar.sidebar-collapsed-init ~ #mainContent {
                width: calc(100vw - 4.5rem) !important;
                min-width: 0 !important;
                margin-left: 4.5rem !important;
            }
        }

        @if(auth()->user()?->isDepartmentAdmin())
        /* Keep BCPC and SWD administrator pages within the tablet viewport. */
        @media (min-width: 37.5rem) and (max-width: 80rem) {
            #mainContent > :is(main, div, section) {
                min-width: 0;
                max-width: 100%;
            }

            #mainContent > main {
                padding: clamp(1rem, 2.5vw, 1.5rem) !important;
            }

            #mainContent .portal-page-header > div,
            #mainContent > header > div {
                flex-wrap: nowrap;
                gap: .75rem;
            }

            #mainContent .portal-page-header > div > #mobileMenuToggle + div,
            #mainContent > header > div > #mobileMenuToggle + div {
                min-width: 0;
                flex: 1 1 auto;
            }

            #mainContent #mobileMenuToggle {
                background-color: #BC0202 !important;
                color: #fff !important;
            }

            #mainContent #mobileMenuToggle > i {
                color: inherit !important;
            }

            #mainContent .portal-page-header > div > div:last-child,
            #mainContent > header > div > div:last-child {
                flex-wrap: nowrap;
                justify-content: flex-end;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]),
            #mainContent > header div:has(> img[alt="User Profile"]) {
                min-width: 0;
                max-width: 100%;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]) > div,
            #mainContent > header div:has(> img[alt="User Profile"]) > div {
                min-width: 0;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]) > div > p:first-child,
            #mainContent > header div:has(> img[alt="User Profile"]) > div > p:first-child {
                overflow: hidden;
                text-overflow: ellipsis;
            }

            #mainContent .department-admin-tablet-role-badge {
                display: none !important;
            }

            #mainContent :is(.portal-table-scroll, .overflow-x-auto, .table-responsive) {
                max-width: 100%;
                overscroll-behavior-x: contain;
            }

            #mainContent form:not(table form) > :is(.flex, .grid) {
                min-width: 0;
            }

            #mainContent form:not(table form) :is(input, select, textarea, .relative) {
                min-width: 0;
                max-width: 100%;
            }

            #mainContent :is(.pagination, [aria-label*="Pagination"]) {
                max-width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
            }

            :is(dialog, .modal-dialog, .swal2-popup) {
                width: min(calc(100vw - 2rem), 680px) !important;
                max-width: calc(100vw - 2rem) !important;
            }
        }

        /* Phone header: retain navigation, title, notifications, and avatar only. */
        @media (max-width: 40rem) {
            #mainContent .portal-page-clock,
            #mainContent .portal-clock-only-header,
            #mainContent .department-admin-tablet-role-badge {
                display: none !important;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]),
            #mainContent > header div:has(> img[alt="User Profile"]) {
                display: flex !important;
            }

            body.portal-ui.case-profiles-mobile-profile-hidden #mainContent .portal-page-header div:has(> img[alt="User Profile"]),
            body.portal-ui.case-profiles-mobile-profile-hidden #mainContent > header div:has(> img[alt="User Profile"]) {
                display: none !important;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]) > div,
            #mainContent > header div:has(> img[alt="User Profile"]) > div {
                display: none !important;
            }

            #mainContent .tablet-header-row {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) auto !important;
                align-items: center;
                column-gap: .75rem;
            }

            #mainContent .tablet-header-row > #mobileMenuToggle {
                display: none !important;
            }

            #mainContent .tablet-header-row > #mobileMenuToggle + div {
                grid-column: 1 !important;
                grid-row: 1;
                min-width: 0;
            }

            #mainContent .tablet-header-row > #mobileMenuToggle + div ~ div:last-child {
                grid-column: 2 !important;
                grid-row: 1;
                min-width: 0;
            }
        }
        @endif

        /* Keep the phone header focused on navigation and account controls. */
        @media (max-width: 47.9375rem) {
            #mobileTopBar .admin-mobile-brand {
                display: none !important;
            }
        }

        @if(request()->routeIs('admin-dashboard'))
        /* Apply after portal-theme.css so the mobile dashboard alignment wins. */
        @media (max-width: 47.9375rem) {
            body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header > div {
                padding-left: max(.5rem, env(safe-area-inset-left)) !important;
                padding-right: max(1rem, env(safe-area-inset-right)) !important;
                align-items: flex-start !important;
                flex-wrap: nowrap !important;
                min-width: 0 !important;
            }
            body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header > div > div:last-child {
                flex: 0 0 auto !important;
                flex-wrap: nowrap !important;
            }
            body.portal-ui.admin-dashboard-mobile-nav #dashboardMenuToggle {
                position: relative !important;
                left: 0 !important;
                margin-left: 0 !important;
                transform: none !important;
            }
            body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header > div > div:first-of-type {
                flex-direction: row !important;
                align-items: flex-start !important;
                gap: .5rem !important;
                min-width: 0 !important;
                flex: 1 1 auto !important;
            }
            body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header > div > div:first-of-type > div {
                width: auto !important;
                flex: 1 1 auto !important;
                min-width: 0 !important;
                text-align: left !important;
            }
            body.portal-ui.admin-dashboard-mobile-nav #mainContent .tablet-header-row {
                display: flex !important;
            }
            body.portal-ui.admin-dashboard-mobile-nav #sidebar ~ #mainContent {
                width: 100% !important;
                min-width: 0 !important;
                margin-left: 0 !important;
            }
        }

        @media (max-width: 40rem) {
            body.portal-ui.admin-dashboard-mobile-nav #mainContent > header#admin-dashboard-header div:has(> img[alt="User Profile"]) {
                display: none !important;
            }
        }

        @media (min-width: 48rem) and (max-width: 63.9375rem) {
            body.portal-ui.admin-dashboard-mobile-nav #sidebarToggleBtn,
            body.portal-ui.case-profiles-admin-mobile-nav #sidebarToggleBtn,
            body.portal-ui.case-profiles-mobile-profile-hidden #sidebarToggleBtn,
            body.portal-ui.barangay-cases-admin-mobile-nav #sidebarToggleBtn,
            body.portal-ui.add-case-admin-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-referrals-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-desk-officers-profile-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-register-user-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-user-logs-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-referral-info-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-quarterly-reports-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-calendar-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-quarterly-reports-archived-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-referrals-archived-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-users-archived-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-bcpc-case-monitoring-archived-mobile-nav #sidebarToggleBtn,
            body.portal-ui.admin-bcpc-case-monitoring-mobile-nav #sidebarToggleBtn {
                display: none !important;
            }
        }
        @endif

        @if(auth()->user()?->isDepartmentAdmin())
        /* User Logs and Referrals share the Dashboard's mobile header and drawer. */
        body.portal-ui.admin-reference-mobile-nav #dashboardMenuToggle { display: none; }
        @media (max-width: 40rem) {
            body.portal-ui.admin-reference-mobile-nav #mobileTopBar,
            body.portal-ui.admin-reference-mobile-nav #sidebarOverlay {
                display: none !important;
                pointer-events: none !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent .tablet-header-row > #mobileMenuToggle {
                display: none !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar {
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
            body.portal-ui.admin-reference-mobile-nav #sidebar.sidebar-open {
                opacity: 1 !important;
                visibility: visible !important;
                transform: translateY(0) !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar .sidebar-header,
            body.portal-ui.admin-reference-mobile-nav #sidebar #sidebarCloseBtn,
            body.portal-ui.admin-reference-mobile-nav #sidebarToggleBtn {
                display: none !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar .sidebar-text {
                opacity: 1 !important;
                max-width: none !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar .sidebar-menu {
                flex: 0 1 auto;
                min-height: 0;
                padding: .5rem !important;
                overflow-y: auto !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar .sidebar-link {
                justify-content: flex-start !important;
                gap: .75rem !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar nav :is(a, button),
            body.portal-ui.admin-reference-mobile-nav #sidebar .sidebar-link {
                background: #C41E3A !important;
                color: #ffffff !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
            body.portal-ui.admin-reference-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
                color: #ffffff !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar nav :is(a, button):hover,
            body.portal-ui.admin-reference-mobile-nav #sidebar .sidebar-link:hover {
                background: #99172E !important;
                color: #ffffff !important;
            }
            body.portal-ui.admin-reference-mobile-nav #sidebar .sidebar-link:is(.bg-purple-600, [class~="bg-purple-700/70"]),
            body.portal-ui.admin-reference-mobile-nav #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
                background: #99172E !important;
                color: #ffffff !important;
                box-shadow: inset 3px 0 0 #ffffff !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent {
                width: 100% !important;
                min-width: 0 !important;
                margin-left: 0 !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header {
                background: #C41E3A !important;
                border-bottom-color: #99172E !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div {
                align-items: flex-start !important;
                flex-wrap: nowrap !important;
                min-width: 0 !important;
                padding-top: max(.875rem, env(safe-area-inset-top)) !important;
                padding-right: max(1rem, env(safe-area-inset-right)) !important;
                padding-bottom: .875rem !important;
                padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent .tablet-header-row {
                display: flex !important;
                align-items: flex-start !important;
                gap: 0 !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div > .admin-reference-mobile-leading {
                display: flex !important;
                flex: 1 1 auto !important;
                align-items: flex-start !important;
                gap: .5rem !important;
                min-width: 0 !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-title {
                flex: 1 1 auto !important;
                min-width: 0 !important;
            }
            body.portal-ui.admin-reference-mobile-nav #dashboardMenuToggle {
                display: inline-flex !important;
                position: relative !important;
                flex: 0 0 2.5rem !important;
                width: 2.5rem !important;
                height: 2.5rem !important;
                align-items: center !important;
                justify-content: center !important;
                left: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                transform: none !important;
                border: 0 !important;
                border-radius: 0 !important;
                background: transparent !important;
                box-shadow: none !important;
                color: #ffffff !important;
            }
            body.portal-ui.admin-reference-mobile-nav #dashboardMenuToggle i { color: inherit !important; }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header :is(h1, h2, h3, p) {
                color: #ffffff !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-subtitle {
                display: block !important;
                margin-top: .125rem !important;
                color: rgba(255, 255, 255, .85) !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header div:has(> img[alt="User Profile"]) {
                display: none !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div > div:last-child {
                flex: 0 0 auto !important;
                flex-wrap: nowrap !important;
            }
        }
        @media (max-width: 40rem) {
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] {
                box-sizing: border-box !important;
                width: 3rem !important;
                min-width: 3rem !important;
                height: 3rem !important;
                min-height: 3rem !important;
                padding: .5rem !important;
                border-radius: .625rem !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] > i {
                margin: 0 !important;
                font-size: 1.25rem !important;
                line-height: 1 !important;
            }
            body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header #notifications-unread-count {
                width: 1.25rem !important;
                height: 1.25rem !important;
                top: -.25rem !important;
                right: -.25rem !important;
                font-size: .75rem !important;
                line-height: 1 !important;
            }
        }
        @media (max-width: 26.875rem) {
            body.portal-ui.admin-reference-mobile-nav #dashboardMenuToggle {
                min-width: 3rem !important;
                min-height: 3rem !important;
            }
        }
        @endif
    </style>
    
    <script>
        document.documentElement.classList.add('js');
    </script>
    <link rel="stylesheet" href="{{ asset('css/portal-modals.css') }}?v={{ filemtime(public_path('css/portal-modals.css')) }}">
    <script src="{{ asset('js/archive-case-modal-design.js') }}?v={{ filemtime(public_path('js/archive-case-modal-design.js')) }}" defer></script>
    <script src="{{ asset('js/portal-modal-close.js') }}?v={{ filemtime(public_path('js/portal-modal-close.js')) }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/logout-modal.css') }}?v={{ filemtime(public_path('css/logout-modal.css')) }}">
    @if(auth()->user()?->isBcpcAdmin() || (auth()->user()?->isVawcAdmin() && request()->routeIs('cases.show') && request()->boolean('bcpc_modal_preview')) || (auth()->user()?->isVawcAdmin() && request()->routeIs('admin.quarterly-reports.archived') && request('section') === 'barangay-cases'))
    <link rel="stylesheet" href="{{ asset('css/bcpc-admin-modals.css') }}?v={{ filemtime(public_path('css/bcpc-admin-modals.css')) }}">
    <script src="{{ asset('js/bcpc-admin-modal-design.js') }}?v={{ filemtime(public_path('js/bcpc-admin-modal-design.js')) }}" defer></script>
    @endif
    <script src="{{ asset('js/portal-modal-presentation.js') }}?v={{ filemtime(public_path('js/portal-modal-presentation.js')) }}" defer></script>
    @if(auth()->user()?->isDepartmentAdmin())
    <style>
        @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: landscape) {
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-menu {
                padding-inline: .5rem !important;
            }
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-link {
                display: flex !important;
                justify-content: center !important;
                padding-inline: 0 !important;
                gap: 0 !important;
            }
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-link > span:has(> i) {
                justify-content: center !important;
                gap: 0 !important;
            }
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-text {
                display: none !important;
            }
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-link i {
                margin-inline: 0 !important;
                text-align: center !important;
            }
            html body.portal-ui #sidebar .sidebar-header {
                box-sizing: border-box;
                height: 5.5rem !important;
                min-height: 5.5rem !important;
                display: flex !important;
                align-items: center !important;
            }
            html body.portal-ui #sidebarToggleBtn {
                top: 2.75rem !important;
                transform: translateY(-50%) !important;
            }
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-header {
                height: 7rem !important;
                min-height: 7rem !important;
                padding: 1.25rem 0 0 !important;
                align-items: flex-start !important;
                justify-content: center !important;
            }
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-header > div {
                width: 100%;
                justify-content: center !important;
                gap: 0 !important;
            }
            html body.portal-ui #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) ~ #sidebarToggleBtn {
                top: 4.75rem !important;
                left: 2.25rem !important;
                transform: translateX(-50%) !important;
            }
        }
    </style>
    @endif
    @if(auth()->user()?->isDepartmentAdmin() && request()->routeIs('cases.show', 'admin-barangay-cases', 'admin-add-new-case', 'admin.users.index', 'admin.users.create', 'admin.users.logs', 'admin.referrals', 'admin.referrals.index', 'admin.referral-info.index', 'admin.quarterly-reports.index', 'admin.quarterly-reports.show', 'admin.quarterly-reports.edit', 'admin.bcpc-case-monitoring.edit', 'admin.calendar.index', 'admin.quarterly-reports.archived', 'admin.referrals.archived', 'admin.users.archived', 'admin.bcpc-case-monitoring.archived'))
    <style>
        @media (max-width: 47.9375rem) {
            html body.portal-ui #mainContent > header #dashboardMenuToggle {
                flex: 0 0 2.5rem !important;
                width: 2.5rem !important;
                height: 2.5rem !important;
                min-width: 2.5rem !important;
                min-height: 2.5rem !important;
            }
            html body.portal-ui #mainContent > header #dashboardMenuToggle > i {
                font-size: 1.25rem !important;
                line-height: 1 !important;
            }
        }
        @media (max-width: 26.875rem) {
            html body.portal-ui #mainContent > header #dashboardMenuToggle {
                min-width: 3rem !important;
                min-height: 3rem !important;
            }
        }
    </style>
    @endif
    @if(auth()->user()?->isDepartmentAdmin() && request()->routeIs('case-profiles.index', 'admin.referrals', 'admin.referrals.index', 'admin.referral-info.index', 'admin.quarterly-reports.index', 'admin.bcpc-case-monitoring.edit', 'admin.users.index', 'admin.quarterly-reports.archived', 'admin.referrals.archived', 'admin.users.archived', 'admin.bcpc-case-monitoring.archived'))
    @include('admin-tailwind.components.portrait-four-cards')
    @endif
    @if(auth()->user()?->isDepartmentAdmin() && (request()->routeIs('case-profiles.index', 'cases.show', 'admin-barangay-cases', 'admin.barangay-cases.show', 'admin-add-new-case', 'admin.users.index', 'admin.users.create', 'admin.users.logs', 'admin.referrals', 'admin.referrals.index', 'admin.referral-info.index', 'admin.quarterly-reports.index', 'admin.quarterly-reports.show', 'admin.quarterly-reports.edit', 'admin.calendar.index', 'admin.referrals.archived', 'admin.users.archived', 'admin.bcpc-case-monitoring.archived') || (request()->routeIs('admin.bcpc-case-monitoring.edit') && (request()->has('view') || request()->has('edit') || !request()->has('add'))) || (request()->routeIs('admin.quarterly-reports.archived') && in_array(request('section'), ['barangay-cases', 'quarterly'], true))))
    @include('admin-tailwind.components.tablet-portrait-sidebar-overlay')
    @endif
    @if(auth()->user()?->isDepartmentAdmin() && request()->routeIs('admin-add-new-case', 'admin.users.index', 'admin.users.create'))
    <style>
        @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
            html body.portal-ui #mainContent > header:is(#add-case-page-header, #desk-officers-profile-header, #register-user-page-header) div:has(> img[alt="User Profile"]) > .text-right > p:first-child {
                color: #1f2937 !important;
            }
            html body.portal-ui #mainContent > header:is(#add-case-page-header, #desk-officers-profile-header, #register-user-page-header) div:has(> img[alt="User Profile"]) > .text-right > p:last-child {
                color: #bc0202 !important;
                background-color: #fff1f2 !important;
            }
        }
    </style>
    @endif
    @if(auth()->user()?->isDepartmentAdmin() && (request()->routeIs('admin-barangay-cases', 'admin.barangay-cases.show', 'admin-add-new-case', 'admin.users.index', 'admin.users.create', 'admin.users.logs', 'admin.referrals', 'admin.referrals.index', 'admin.referral-info.index', 'admin.quarterly-reports.index', 'admin.quarterly-reports.show', 'admin.quarterly-reports.edit', 'admin.calendar.index', 'admin.referrals.archived', 'admin.users.archived', 'admin.bcpc-case-monitoring.archived') || (request()->routeIs('admin.bcpc-case-monitoring.edit') && !request()->has('add')) || (request()->routeIs('admin.quarterly-reports.archived') && in_array(request('section'), ['barangay-cases', 'quarterly'], true))))
    @include('admin-tailwind.components.tablet-landscape-sidebar-motion')
    @endif
    @if(auth()->user()?->isDepartmentAdmin())
    <link rel="stylesheet" href="{{ asset('css/desk-officer-tablet-portrait-sidebar.css') }}?v={{ filemtime(public_path('css/desk-officer-tablet-portrait-sidebar.css')) }}">
    @endif
</head>
<body class="portal-ui {{ auth()->user()?->isDepartmentAdmin() ? 'admin-tablet-portrait-sidebar' : '' }} {{ request()->routeIs('admin-dashboard') ? 'admin-dashboard-mobile-nav' : '' }} {{ request()->routeIs('case-profiles.index') ? 'case-profiles-mobile-profile-hidden' : '' }} {{ request()->routeIs('case-profiles.index') && auth()->user()?->isDepartmentAdmin() ? 'case-profiles-admin-mobile-nav' : '' }} {{ request()->routeIs('cases.show') && auth()->user()?->isDepartmentAdmin() ? 'case-details-admin-mobile-nav' : '' }} {{ request()->routeIs('admin.users.index') && auth()->user()?->isDepartmentAdmin() ? 'admin-desk-officers-profile-mobile-nav' : '' }} {{ request()->routeIs('admin.users.create') && auth()->user()?->isDepartmentAdmin() ? 'admin-register-user-mobile-nav' : '' }} {{ (request()->routeIs('admin.users.logs', 'admin.users.archived', 'admin.referrals.index', 'admin.referrals', 'admin.referrals.archived', 'admin.referral-info.index', 'admin.quarterly-reports.index', 'admin.quarterly-reports.show', 'admin.quarterly-reports.edit', 'admin.bcpc-case-monitoring.edit', 'admin.bcpc-case-monitoring.archived', 'admin.calendar.index') || (request()->routeIs('admin.quarterly-reports.archived') && in_array(request('section'), ['barangay-cases', 'quarterly'], true))) && auth()->user()?->isDepartmentAdmin() ? 'admin-reference-mobile-nav' : '' }} {{ request()->routeIs('admin.referrals', 'admin.referrals.index') && auth()->user()?->isDepartmentAdmin() ? 'admin-referrals-mobile-nav' : '' }} {{ request()->routeIs('admin.referral-info.index') && auth()->user()?->isDepartmentAdmin() ? 'admin-referral-info-mobile-nav' : '' }} {{ request()->routeIs('admin.quarterly-reports.index') && auth()->user()?->isDepartmentAdmin() ? 'admin-quarterly-reports-mobile-nav' : '' }} {{ request()->routeIs('admin.calendar.index') && auth()->user()?->isDepartmentAdmin() ? 'admin-calendar-mobile-nav' : '' }} {{ request()->routeIs('admin.quarterly-reports.archived') && auth()->user()?->isDepartmentAdmin() ? 'admin-quarterly-reports-archived-mobile-nav' : '' }} {{ request()->routeIs('admin.referrals.archived') && auth()->user()?->isDepartmentAdmin() ? 'admin-referrals-archived-mobile-nav' : '' }} {{ request()->routeIs('admin.users.archived') && auth()->user()?->isDepartmentAdmin() ? 'admin-users-archived-mobile-nav' : '' }} {{ request()->routeIs('admin.bcpc-case-monitoring.archived') && auth()->user()?->isDepartmentAdmin() ? 'admin-bcpc-case-monitoring-archived-mobile-nav' : '' }} {{ request()->routeIs('admin.bcpc-case-monitoring.edit') && auth()->user()?->isDepartmentAdmin() ? 'admin-bcpc-case-monitoring-mobile-nav' : '' }} {{ request()->routeIs('admin.users.logs') && auth()->user()?->isDepartmentAdmin() ? 'admin-user-logs-mobile-nav' : '' }} {{ request()->routeIs('admin-barangay-cases') && auth()->user()?->isDepartmentAdmin() ? 'barangay-cases-admin-mobile-nav' : '' }} {{ request()->routeIs('admin.barangay-cases.show') ? 'view-cases-admin-mobile-nav' : '' }} {{ request()->routeIs('admin-add-new-case') && auth()->user()?->isDepartmentAdmin() ? 'add-case-admin-mobile-nav' : '' }} {{ auth()->user()?->isBcpcAdmin() ? 'bcpc-admin-header-theme' : (auth()->user()?->isVawcAdmin() ? 'swd-admin-header-theme' : '') }} font-['Raleway','Montserrat',sans-serif] bg-gray-100 text-gray-600 min-h-screen antialiased" style="--portal-user-initials: '{{ strtoupper(mb_substr((string) auth()->user()?->first_name, 0, 1) . mb_substr((string) auth()->user()?->last_name, 0, 1)) }}';">
    
    <!-- Mobile Sticky Top Bar -->
    <div id="mobileTopBar" class="lg:hidden sticky top-0 z-30 bg-purple-900 text-white flex items-center justify-between px-4 py-2.5 shadow-md border-b border-purple-800">
        <div class="flex items-center gap-3">
            <button 
                type="button"
                id="mobileMenuToggle" 
                class="bg-purple-800/80 hover:bg-purple-700 text-white p-2 rounded-lg transition-colors focus:outline-none flex items-center justify-center w-9 h-9"
                aria-label="Toggle menu"
                aria-controls="sidebar"
                aria-expanded="false"
            >
                <i class="fas fa-bars text-base"></i>
            </button>
            <div class="admin-mobile-brand flex items-center gap-2">
                <img src="{{ asset('images/iserve_logo.jpg') }}" alt="I-serve Silang Logo" class="w-8 h-8 rounded object-contain">
                <span class="font-bold text-base tracking-wide">iSERVE SILANG</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if(Auth::check())
            <span class="{{ Auth::user()?->isDepartmentAdmin() ? 'department-admin-tablet-role-badge' : '' }} bg-[#C41E3A] text-white border-[#C41E3A] lg:bg-purple-800/80 lg:text-purple-200 lg:border-purple-700 text-xs px-2.5 py-1 rounded-full border font-medium">
                {{ Auth::user()->position ?? 'Admin' }}
            </span>
            @endif
        </div>
    </div>
    
    <!-- Sidebar Overlay (Mobile) -->
    <div 
        id="sidebarOverlay" 
        class="fixed inset-0 bg-black/50 z-30 lg:hidden opacity-0 pointer-events-none transition-opacity duration-300"
    ></div>
    
    <!-- Sidebar -->
    <aside 
        id="sidebar" 
        class="fixed top-0 left-0 h-screen w-72 bg-[#C41E3A] text-white z-40
               lg:translate-x-0 flex flex-col shadow-2xl
               rounded-r-3xl lg:rounded-none {{ !$persistsAdminSidebar && auth()->user()?->isDepartmentAdmin() ? 'sidebar-collapsed' : '' }}"
    >
        @if($persistsAdminSidebar)
        <script>
            document.getElementById('sidebar')?.classList.toggle('sidebar-collapsed', document.documentElement.classList.contains('admin-sidebar-collapsed'));
        </script>
        @endif
        <!-- Sidebar Header -->
        <div class="sidebar-header py-5 border-b border-white">
            <div class="flex items-center justify-center gap-3">
                <img src="{{ asset('images/iserve_logo.jpg') }}" alt="I-serve Silang Logo" class="w-12 h-12 min-w-[3rem] rounded-lg object-contain">
                <div class="sidebar-text whitespace-nowrap overflow-hidden leading-tight">
                    <h1 class="text-xl font-bold tracking-wide">iSERVE SILANG</h1>
                    <p class="mt-1 text-[10px] font-medium tracking-[.12em] text-white/70">SWD &amp; BCPC PORTAL</p>
                </div>
            </div>
        </div>

        <!-- Mobile Close Button - positioned outside sidebar flow -->
        <button 
            type="button"
            id="sidebarCloseBtn"
            class="lg:hidden absolute -right-4 top-6 w-8 h-8 bg-purple-600 rounded-full shadow-lg flex items-center justify-center text-white hover:bg-purple-700"
            aria-label="Close sidebar"
            aria-controls="sidebar"
        >
            <i class="fas fa-chevron-left text-sm"></i>
        </button>
        
        <!-- Sidebar Menu -->
        <nav class="flex-1 overflow-y-auto py-4 sidebar-menu">
            @php
                $isArchiveDetailPage = request()->boolean('archived') && request()->routeIs(
                    'cases.show',
                    'admin.quarterly-reports.show',
                    'admin.bcpc-case-monitoring.edit'
                );
                $isArchiveListPage = request()->routeIs(
                    'admin.referrals.archived',
                    'admin.users.archived',
                    'admin.bcpc-case-monitoring.archived',
                    'admin.quarterly-reports.archived'
                );
                $isArchivesOpen = $isArchiveListPage || $isArchiveDetailPage;
            @endphp
            <!-- Dashboard -->
            <a href="{{ route('admin-dashboard') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('admin-dashboard') 
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
               data-tooltip="Dashboard"
               title="Dashboard"
            >
                <i class="fas fa-tachometer-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Dashboard</span>
            </a>

            @if(auth()->user()->isDepartmentAdmin())
            @php($isManageCasesActive = !$isArchiveDetailPage && request()->routeIs('case-profiles.*', 'admin-barangay-cases', 'admin.barangay-cases.*', 'admin-add-new-case', 'cases.*'))
            <div x-data="{ casesOpen: {{ $isManageCasesActive ? 'true' : 'false' }} }" @sidebar-dropdown-open.window="if ($event.detail !== 'casesOpen') casesOpen = false" class="mb-1">
                <button type="button" @click="casesOpen = !casesOpen; if (casesOpen) $dispatch('sidebar-dropdown-open', 'casesOpen')" class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-lg {{ $isManageCasesActive ? 'bg-purple-600 text-white shadow-sm font-medium' : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}" title="Manage cases" data-tooltip="Manage cases">
                    <span class="flex items-center gap-3"><i class="fas fa-gavel w-5 text-center"></i><span class="sidebar-text">Manage cases</span></span>
                    <i class="fas fa-chevron-down text-xs sidebar-text" :class="{ 'rotate-180': casesOpen }"></i>
                </button>
                <div x-show="casesOpen" x-cloak class="mt-1 pl-6 pr-2 space-y-1">
                    @foreach(['case-profiles.index' => 'Case Profiles', 'admin-barangay-cases' => 'Barangay Cases', 'admin-add-new-case' => 'Add New Case'] as $caseRoute => $caseLabel)
                        @php($isCaseItemActive = $caseRoute === 'case-profiles.index' ? request()->routeIs('case-profiles.*') : request()->routeIs($caseRoute, $caseRoute . '.*'))
                        <a href="{{ route($caseRoute) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs {{ $isCaseItemActive ? 'bg-purple-600 text-white shadow-sm font-semibold' : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"><i class="fas {{ ['case-profiles.index' => 'fa-address-card', 'admin-barangay-cases' => 'fa-folder-open', 'admin-add-new-case' => 'fa-plus-circle'][$caseRoute] }}" aria-hidden="true"></i><span class="sidebar-text">{{ $caseLabel }}</span></a>
                    @endforeach
                </div>
            </div>
            @else
            <!-- Barangay Cases -->
            <a href="{{ route('case-profiles.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 text-purple-100 hover:bg-purple-700/50" title="Victim and Perpetrator Profiles" data-tooltip="Profiles">
                <i class="fas fa-address-card w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Case Profiles</span>
            </a>
            <a href="{{ route('admin-barangay-cases') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('admin-barangay-cases') 
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
               data-tooltip="Barangay Cases"
               title="Barangay Cases"
            >
                <i class="fas fa-gavel w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Barangay Cases</span>
            </a>

            @endif
            @if(auth()->check() && strtolower(auth()->user()->position) === 'administrator')
            <!-- {{ auth()->user()->isBcpcAdmin() ? 'User Management' : 'User Information' }} Dropdown -->
            <div x-data="{ userMenuOpen: {{ request()->routeIs('admin.users.index', 'admin.users.create', 'admin.users.logs') ? 'true' : 'false' }} }" @sidebar-dropdown-open.window="if ($event.detail !== 'userMenuOpen') userMenuOpen = false" class="mb-1">
                <button type="button" 
                        @click="userMenuOpen = !userMenuOpen; if (userMenuOpen && {{ auth()->user()->isDepartmentAdmin() ? 'true' : 'false' }}) $dispatch('sidebar-dropdown-open', 'userMenuOpen')"
                        class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all duration-200 text-left
                               {{ request()->routeIs('admin.users.index', 'admin.users.create', 'admin.users.logs')
                                  ? 'bg-purple-700/70 text-white shadow-sm font-medium' 
                                  : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
                        data-tooltip="{{ auth()->user()->isBcpcAdmin() ? 'User Management' : 'User Information' }}"
                        title="{{ auth()->user()->isBcpcAdmin() ? 'User Management' : 'User Information' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <i class="fas fa-users w-5 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">{{ auth()->user()->isBcpcAdmin() ? 'User Management' : 'User Information' }}</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs sidebar-text transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }"></i>
                </button>
                
                <div x-show="userMenuOpen" x-cloak class="mt-1 pl-6 pr-2 space-y-1">
                    <!-- User Information Link -->
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.users.index') 
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold' 
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="{{ auth()->user()->isVawcAdmin() ? 'Desk Officer Profile' : (auth()->user()->isBcpcAdmin() ? 'Desk officers Profile' : 'User Information') }}">
                        <i class="fas fa-table-list text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">{{ auth()->user()->isVawcAdmin() ? 'Desk Officer Profile' : (auth()->user()->isBcpcAdmin() ? 'Desk officers Profile' : 'User Information') }}</span>
                    </a>

                    <!-- User Register Link -->
                    <a href="{{ route('admin.users.create') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.users.create') 
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold' 
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="{{ auth()->user()->isVawcAdmin() ? 'Register Desk Officer' : (auth()->user()->isBcpcAdmin() ? 'Register Desk officer' : 'User Register') }}">
                        <i class="fas fa-user-plus text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">{{ auth()->user()->isVawcAdmin() ? 'Register Desk Officer' : (auth()->user()->isBcpcAdmin() ? 'Register Desk officer' : 'User Register') }}</span>
                    </a>
                    
                    <!-- User Logs Link -->
                    @if(auth()->check())
                    <a href="{{ route('admin.users.logs', ['user' => auth()->user()->id]) }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.users.logs') 
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold' 
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="{{ auth()->user()->isVawcAdmin() ? 'Desk officer logs' : 'User Logs' }}">
                        <i class="fas fa-history text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">{{ auth()->user()->isVawcAdmin() ? 'Desk officer logs' : 'User Logs' }}</span>
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Referrals -->
            <a href="{{ route('admin.referrals') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('admin.referrals*') && !request()->routeIs('admin.referrals.archived')
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
               data-tooltip="Referrals"
               title="Referrals"
            >
                <i class="fas fa-exchange-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Referrals</span>
            </a>
            
            <!-- Referral Info -->
            <a href="{{ route('admin.referral-info.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('admin.referral-info.*') 
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
               data-tooltip="Referral Info"
               title="Referral Info"
            >
                <i class="fas fa-envelope w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Referral Info</span>
            </a>
            
            <!-- Quarterly Reports -->
            <a href="{{ route('admin.quarterly-reports.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('admin.quarterly-reports*') && !$isArchiveListPage && !$isArchiveDetailPage
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
               data-tooltip="Quarterly Reports"
               title="Quarterly Reports"
            >
                <i class="fas fa-file-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Quarterly Reports</span>
            </a>

            @unless(auth()->user()->isVawcAdmin())
            <!-- BCPC Case Monitoring -->
            <a href="{{ route('admin.bcpc-case-monitoring.edit') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('admin.bcpc-case-monitoring*') && !$isArchiveListPage && !$isArchiveDetailPage
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
               data-tooltip="BCPC Case Monitoring"
               title="BCPC Case Monitoring"
            >
                <i class="fas fa-child w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">BCPC Monitoring</span>
            </a>
            
            @endunless
            <!-- Calendar -->
            <a href="{{ route('admin.calendar.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('admin.calendar*') 
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
               data-tooltip="Calendar"
               title="Calendar"
            >
                <i class="fas fa-calendar-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Calendar</span>
            </a>
            @endif

            @unless(auth()->user()->isDepartmentAdmin())
            <!-- Add New Case -->
            <a href="{{ route('admin-add-new-case') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg mb-1 transition-all duration-200
                      {{ request()->routeIs('admin-add-new-case') 
                         ? 'bg-purple-600 text-white shadow-lg' 
                         : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
               data-tooltip="Add New Case"
               title="Add New Case"
            >
                <i class="fas fa-plus-circle w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Add New Case</span>
            </a>
            @endunless
            <!-- Archives Dropdown -->
            <div x-data="{ archiveOpen: {{ $isArchivesOpen ? 'true' : 'false' }} }" @sidebar-dropdown-open.window="if ($event.detail !== 'archiveOpen') archiveOpen = false" class="mb-1">
                <button type="button"
                        @click="archiveOpen = !archiveOpen; if (archiveOpen && {{ auth()->user()->isDepartmentAdmin() ? 'true' : 'false' }}) $dispatch('sidebar-dropdown-open', 'archiveOpen')"
                        class="sidebar-link flex items-center justify-between w-full px-4 py-3 rounded-lg transition-all duration-200 text-left
                               {{ $isArchivesOpen
                                  ? 'bg-purple-700/70 text-white shadow-sm font-medium'
                                  : 'text-purple-100 hover:bg-purple-700/50 hover:text-white' }}"
                        data-tooltip="Archives"
                        title="Archives">
                    <div class="flex items-center gap-3 min-w-0">
                        <i class="fas fa-archive w-5 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">Archives</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs sidebar-text transition-transform duration-200" :class="{ 'rotate-180': archiveOpen }"></i>
                </button>

                <div x-show="archiveOpen" x-cloak class="mt-1 pl-6 pr-2 space-y-1">
                    @if(auth()->user()->isBcpcAdmin())
                    <a href="{{ route('admin.quarterly-reports.archived', ['section' => 'barangay-cases']) }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.quarterly-reports.archived') && request('section') === 'barangay-cases'
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold'
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="Archived Barangay Cases">
                        <i class="fas fa-map-marked-alt text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">Archived Barangay Cases</span>
                    </a>
                    @endif

                    @if(auth()->user()->isVawcAdmin())
                    <a href="{{ route('admin.quarterly-reports.archived', ['section' => 'barangay-cases']) }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.quarterly-reports.archived') && in_array(request('section'), [null, 'barangay-cases'], true)
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold'
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="Archived Barangay Cases">
                        <i class="fas fa-map-marked-alt text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">Archived Barangay Cases</span>
                    </a>
                    @endif

                    @if(auth()->user()->isDepartmentAdmin())
<a href="{{ route('admin.referrals.archived') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.referrals.archived') ? 'bg-purple-600 text-white shadow-sm font-semibold' : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}" title="Archived Referrals">
<i class="fas fa-paper-plane text-[11px] w-4 text-center flex-shrink-0"></i><span class="sidebar-text whitespace-nowrap overflow-hidden">Archived Referrals</span>
</a>
@endif
<!-- Archived Users Link -->
                    <a href="{{ route('admin.users.archived') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.users.archived')
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold'
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="Archived Users">
                        <i class="fas fa-user-lock text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">Archived Users</span>
                    </a>

                    @if(auth()->user()->isDepartmentAdmin())
                    <a href="{{ route('admin.quarterly-reports.archived', ['section' => 'quarterly']) }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.quarterly-reports.archived') && request('section') === 'quarterly'
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold'
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="Archived Quarterly Reports">
                        <i class="fas fa-chart-pie text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">Archived Quarterly Reports</span>
                    </a>
                    @endif

                    <!-- BCPC Case Monitoring Archive Link -->
                    @unless(auth()->user()->isVawcAdmin())
                    <a href="{{ route(auth()->user()->isVawcAdmin() ? 'admin.quarterly-reports.archived' : 'admin.bcpc-case-monitoring.archived') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs(auth()->user()->isVawcAdmin() ? 'admin.quarterly-reports.archived' : 'admin.bcpc-case-monitoring.archived')
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold'
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="{{ auth()->user()->isVawcAdmin() ? 'Archived Barangay Cases' : 'Archived BCPC Reports' }}">
                        <i class="fas fa-clipboard-check text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">{{ auth()->user()->isVawcAdmin() ? 'Archived Barangay Cases' : 'Archived BCPC Reports' }}</span>
                    </a>
                    @endunless
                </div>
            </div>


        </nav>
        
        <!-- Sidebar Footer -->
        <div class="py-4 border-t border-white sidebar-menu">
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden" @if(auth()->user()?->isVawcAdmin() || auth()->user()?->isBcpcAdmin()) data-skip-loading-modal @endif>
                @csrf
            </form>
            <button 
                type="button" 
                id="logout-button" 
                class="sidebar-link flex items-center gap-3 w-full py-3 rounded-lg 
                       text-purple-100 hover:bg-red-500/20 hover:text-red-300 
                       transition-all duration-200"
                title="Logout"
            >
                <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap overflow-hidden">Logout</span>
            </button>
        </div>
    </aside>
    
    <!-- Floating Sidebar Toggle Button (Desktop Only) -->
    <button 
        type="button"
        id="sidebarToggleBtn" 
        class="hidden lg:flex items-center justify-center w-6 h-6 bg-white rounded-full 
               shadow-lg shadow-gray-600/60 hover:shadow-xl hover:bg-gray-50 
               transition-all duration-200 border border-gray-300"
        aria-label="Toggle sidebar"
        aria-controls="sidebar"
        aria-expanded="true"
        title="Toggle sidebar"
    >
        <i class="fas fa-chevron-left text-gray-600 text-xs transition-transform duration-300"></i>
    </button>
    
    <!-- Main Content -->
    <div 
        id="mainContent" data-page-title="@yield('title', '')"
        class="lg:ml-72 min-h-screen sidebar-transition overflow-x-hidden"
    >
        @yield('content')
    </div>
    @include('components.portal-clock')
    
    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if((session('success') || session('error')) && !session('generated_bcpc_quarterly_report') && !session('generated_vawc_quarterly_report') && !(auth()->user()?->isDepartmentAdmin() && request()->routeIs('admin.quarterly-reports.index') && session('archived_quarterly_report')))
    <script>
    const showFlashModal = function () {
        if (!window.Swal) return;
        window.Swal.fire({
            icon: @json(session('success') ? 'success' : 'error'),
            title: @json(session('success') ? 'Success' : 'Error'),
            text: @json(session('success') ?: session('error')),
            confirmButtonColor: '#7e22ce',
            confirmButtonText: 'OK',
            showConfirmButton: true
        });
    };
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', showFlashModal) : showFlashModal();
    </script>
    @endif
    
    @if(auth()->user()?->isVawcAdmin() && request()->routeIs('admin.quarterly-reports.archived', 'admin.referrals.archived', 'admin.users.archived'))
    <template id="swd-archive-page-scripts">@stack('scripts')</template>
    @else
    @stack('scripts')
    @endif
    @include('admin-tailwind.partials.swd-archive-view-modal')
    @if(auth()->user()?->isVawcAdmin())
    <script>{!! file_get_contents(public_path('js/swd-archive-navigation.js')) !!}</script>
    @endif
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const dashboardMenuToggle = document.getElementById('dashboardMenuToggle');
        const mobileMenuToggles = [mobileMenuToggle, dashboardMenuToggle].filter(Boolean);
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
        const sidebarStateKey = @json($adminSidebarStateKey);
        const isDashboardDrawer = () => (document.body.classList.contains('admin-dashboard-mobile-nav') && window.matchMedia('(max-width: 47.9375rem)').matches)
            || (document.body.classList.contains('admin-reference-mobile-nav') && window.matchMedia('(max-width: 40rem)').matches)
            || (document.body.classList.contains('case-profiles-admin-mobile-nav') && window.matchMedia('(max-width: 47.9375rem)').matches)
            || (document.body.classList.contains('case-details-admin-mobile-nav') && window.matchMedia('(max-width: 40rem)').matches)
            || (document.body.classList.contains('admin-desk-officers-profile-mobile-nav') && window.matchMedia('(max-width: 40rem)').matches)
            || (document.body.classList.contains('admin-register-user-mobile-nav') && window.matchMedia('(max-width: 40rem)').matches)
            || (document.body.classList.contains('barangay-cases-admin-mobile-nav') && window.matchMedia('(max-width: 40rem)').matches)
            || (document.body.classList.contains('view-cases-admin-mobile-nav') && window.matchMedia('(max-width: 47.9375rem)').matches)
            || (document.body.classList.contains('add-case-admin-mobile-nav') && window.matchMedia('(max-width: 40rem)').matches);
        
        @if($persistsAdminSidebar)
        sidebar.classList.toggle('sidebar-collapsed', localStorage.getItem(sidebarStateKey) === 'true');
        @elseif(auth()->user()?->isDepartmentAdmin())
        sidebar.classList.add('sidebar-collapsed');
        localStorage.setItem(sidebarStateKey, 'true');
        @else
        localStorage.setItem(sidebarStateKey, 'false');
        @endif
        if ((document.body.classList.contains('admin-dashboard-mobile-nav') || document.body.classList.contains('case-profiles-admin-mobile-nav') || document.body.classList.contains('case-profiles-mobile-profile-hidden') || document.body.classList.contains('barangay-cases-admin-mobile-nav') || document.body.classList.contains('add-case-admin-mobile-nav') || document.body.classList.contains('admin-referrals-mobile-nav') || document.body.classList.contains('admin-desk-officers-profile-mobile-nav') || document.body.classList.contains('admin-register-user-mobile-nav') || document.body.classList.contains('admin-user-logs-mobile-nav') || document.body.classList.contains('admin-referral-info-mobile-nav') || document.body.classList.contains('admin-quarterly-reports-mobile-nav') || document.body.classList.contains('admin-calendar-mobile-nav') || document.body.classList.contains('admin-quarterly-reports-archived-mobile-nav') || document.body.classList.contains('admin-referrals-archived-mobile-nav') || document.body.classList.contains('admin-users-archived-mobile-nav')) && window.matchMedia('(min-width: 48rem) and (max-width: 63.9375rem)').matches) {
            sidebar?.classList.add('sidebar-collapsed');
            localStorage.setItem(sidebarStateKey, 'true');
        }
        if (sidebarToggleBtn && sidebar) {
            sidebarToggleBtn.setAttribute('aria-expanded', String(!sidebar.classList.contains('sidebar-collapsed')));
        }

        function collapseSidebar() {
            sidebar.classList.add('sidebar-collapsed');
            if (sidebarToggleBtn) sidebarToggleBtn.setAttribute('aria-expanded', 'false');
            localStorage.setItem(sidebarStateKey, 'true');
        }

        @if(auth()->user()?->isDepartmentAdmin())
        sidebar?.addEventListener('click', function(event) {
            if (window.matchMedia('(min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)').matches
                && event.target.closest('nav a[href], #logout-button')) collapseSidebar();
        });
        sidebar?.addEventListener('click', function(event) {
            if (!window.matchMedia('(min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)').matches) return;
            const link = event.target.closest('a[href]');
            if (link && new URL(link.href, location.origin).pathname === new URL(@json(route('admin.bcpc-case-monitoring.archived')), location.origin).pathname) {
                if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                if (sidebar.classList.contains('sidebar-collapsed')) return;
                event.preventDefault();
                const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                sidebar.style.setProperty('max-width', '18rem', 'important');
                sidebar.style.setProperty('transition', reducedMotion ? 'none' : 'width .3s ease', 'important');
                collapseSidebar();
                window.setTimeout(() => location.assign(link.href), reducedMotion ? 0 : 300);
            }
        }, true);
        @endif

        @if(auth()->user()?->isDepartmentAdmin() && (request()->routeIs('admin-dashboard', 'case-profiles.index', 'cases.show', 'admin-barangay-cases', 'admin.barangay-cases.show', 'admin-add-new-case', 'admin.users.index', 'admin.users.create', 'admin.users.logs', 'admin.referrals', 'admin.referrals.index', 'admin.referral-info.index', 'admin.quarterly-reports.index', 'admin.quarterly-reports.show', 'admin.quarterly-reports.edit', 'admin.calendar.index', 'admin.referrals.archived', 'admin.users.archived', 'admin.bcpc-case-monitoring.archived') || (request()->routeIs('admin.bcpc-case-monitoring.edit') && !request()->has('add')) || (request()->routeIs('admin.quarterly-reports.archived') && in_array(request('section'), ['barangay-cases', 'quarterly'], true))))
        sidebar?.addEventListener('click', function(event) {
            if (!window.matchMedia('(min-width: 48rem) and (max-width: 85.375rem) and (orientation: landscape)').matches) return;
            if (event.target.closest('nav a[href], #logout-button')) collapseSidebar();
        });
        @endif

        @if(auth()->user()?->isDepartmentAdmin() && request()->routeIs('admin.quarterly-reports.index', 'admin.bcpc-case-monitoring.edit'))
        const monitoringPortraitSidebar = window.matchMedia('(min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)');
        @if(request()->routeIs('admin.bcpc-case-monitoring.edit'))
        if (monitoringPortraitSidebar.matches) collapseSidebar();
        @endif
        sidebar?.querySelector('a[data-tooltip="BCPC Case Monitoring"]')?.addEventListener('click', function() {
            if (monitoringPortraitSidebar.matches) collapseSidebar();
        }, { capture: true });
        @endif
        
        function expandSidebar() {
            sidebar.classList.remove('sidebar-collapsed');
            if (sidebarToggleBtn) sidebarToggleBtn.setAttribute('aria-expanded', 'true');
            localStorage.setItem(sidebarStateKey, 'false');
            // Remove the initial collapsed style if it exists
            const initialStyle = document.getElementById('sidebar-initial-collapsed-style');
            if (initialStyle) {
                initialStyle.remove();
            }
            document.documentElement.classList.remove('sidebar-is-collapsed');
        }
        
        // Ensure all non-form buttons have explicit type="button"
        document.querySelectorAll('button:not([type])').forEach(btn => {
            if (!btn.closest('form')) {
                btn.setAttribute('type', 'button');
            }
        });

        // Floating toggle button click
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (sidebar.classList.contains('sidebar-collapsed')) {
                    expandSidebar();
                } else {
                    collapseSidebar();
                }
            });
        }
        
        // Mobile toggle - open sidebar
        if (mobileMenuToggles.length) {
            mobileMenuToggles.forEach(function(menuToggle) {
                menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isDashboardDrawer() && sidebar.classList.contains('sidebar-open')) {
                    closeMobileSidebar();
                    return;
                }
                // Open sidebar with fade in from left
                sidebar.classList.add('sidebar-open');
                mobileMenuToggles.forEach(toggle => toggle.setAttribute('aria-expanded', 'true'));
                if (isDashboardDrawer()) {
                    document.body.style.overflow = 'hidden';
                    document.documentElement.style.setProperty('overflow-y', 'hidden', 'important');
                    mobileMenuToggles.forEach(toggle => {
                        toggle.setAttribute('aria-label', 'Close navigation menu');
                        toggle.querySelector('i')?.classList.replace('fa-bars', 'fa-xmark');
                    });
                    const dashboardHeader = document.getElementById('admin-dashboard-header')
                        || document.querySelector('#mainContent > header.sticky');
                    if (dashboardHeader) {
                        const headerBottom = Math.max(0, dashboardHeader.getBoundingClientRect().bottom);
                        sidebar.style.setProperty('top', `${headerBottom}px`, 'important');
                        sidebar.style.setProperty('max-height', `calc(100vh - ${headerBottom}px)`, 'important');
                    }
                }
                // Show overlay
                if (sidebarOverlay) sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
                });
            });
        }
        
        // Function to close mobile sidebar
        function closeMobileSidebar() {
            // Close sidebar with fade out to left
            sidebar.classList.remove('sidebar-open');
            mobileMenuToggles.forEach(toggle => toggle.setAttribute('aria-expanded', 'false'));
            if (isDashboardDrawer()) {
                document.body.style.overflow = '';
                document.documentElement.style.removeProperty('overflow-y');
                mobileMenuToggles.forEach(toggle => {
                    toggle.setAttribute('aria-label', 'Open navigation menu');
                    toggle.querySelector('i')?.classList.replace('fa-xmark', 'fa-bars');
                });
                sidebar.style.removeProperty('top');
                sidebar.style.removeProperty('max-height');
            }
            // Hide overlay
            if (sidebarOverlay) sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
        }
        
        // Close sidebar on overlay click (mobile)
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeMobileSidebar();
            });
        }
        
        // Close sidebar via close button (mobile)
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMobileSidebar();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar?.classList.contains('sidebar-open')) {
                closeMobileSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.matchMedia('(min-width: 80.0625rem)').matches) closeMobileSidebar();
            if ((document.body.classList.contains('admin-dashboard-mobile-nav') || document.body.classList.contains('case-profiles-admin-mobile-nav') || document.body.classList.contains('case-profiles-mobile-profile-hidden') || document.body.classList.contains('barangay-cases-admin-mobile-nav') || document.body.classList.contains('add-case-admin-mobile-nav') || document.body.classList.contains('admin-referrals-mobile-nav') || document.body.classList.contains('admin-desk-officers-profile-mobile-nav') || document.body.classList.contains('admin-register-user-mobile-nav') || document.body.classList.contains('admin-user-logs-mobile-nav') || document.body.classList.contains('admin-referral-info-mobile-nav') || document.body.classList.contains('admin-quarterly-reports-mobile-nav') || document.body.classList.contains('admin-calendar-mobile-nav') || document.body.classList.contains('admin-quarterly-reports-archived-mobile-nav') || document.body.classList.contains('admin-referrals-archived-mobile-nav') || document.body.classList.contains('admin-users-archived-mobile-nav')) && window.matchMedia('(min-width: 48rem) and (max-width: 63.9375rem)').matches) {
                closeMobileSidebar();
            }
        });
        
        // Handle logout button
        const logoutForm = document.getElementById('logout-form');
        const logoutButton = document.getElementById('logout-button');
        
        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Logout',
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

        // Capture-phase listener: ensure any button outside forms has type="button" and prevent unintentional form submit/reload on first click
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('button');
            if (btn && !btn.hasAttribute('type') && !btn.closest('form')) {
                btn.setAttribute('type', 'button');
            }
        }, true);
    });
    </script>
    @if(auth()->user()?->isDepartmentAdmin())
    <script src="{{ asset('js/admin-table-actions.js') }}?v={{ filemtime(public_path('js/admin-table-actions.js')) }}"></script>
    @endif
    @if(auth()->user()?->isVawcAdmin())
    <script src="{{ asset('js/vawc-table-pagination.js') }}"></script>
    @endif
    <script src="{{ asset('js/admin-pagination-style.js') }}"></script>
    <script src="{{ asset('js/table-action-controls.js') }}?v={{ filemtime(public_path('js/table-action-controls.js')) }}"></script>
<script src="{{ asset('js/portal-theme.js') }}?v={{ filemtime(public_path('js/portal-theme.js')) }}"></script>
<script src="{{ asset('js/unsaved-changes-guard.js') }}?v={{ filemtime(public_path('js/unsaved-changes-guard.js')) }}"></script>
@include('components.record-table-columns')
@include('components.multirole-loading')
@include('components.inactivity-timeout')
<script src="{{ asset('js/contact-number-validation.js') }}?v={{ filemtime(public_path('js/contact-number-validation.js')) }}"></script>
</body>
</html>
