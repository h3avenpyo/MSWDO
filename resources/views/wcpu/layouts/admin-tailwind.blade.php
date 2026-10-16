 <!DOCTYPE html>
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vite / Tailwind CSS -->
    @if(auth()->user()?->isVawcAdmin())
        {!! (clone app(\Illuminate\Foundation\Vite::class))
            ->useHotFile(public_path('build/hot'))
            ->useManifestFilename(is_file(public_path('build/manifest.json')) ? 'manifest.json' : '.vite/manifest.json')
            (['resources/css/app.css', 'resources/js/app.js']) !!}
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table-action-controls.css') }}?v={{ filemtime(public_path('css/table-action-controls.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/portal-theme.css') }}?v={{ filemtime(public_path('css/portal-theme.css')) }}">
    
    <style>
        /* Hide elements with x-cloak until Alpine.js initializes */
        [x-cloak] { display: none !important; }
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
        .portal-ui #mainContent :is(a, button)[data-action="edit"],
        .portal-ui #mainContent :is(a, button)[data-action="archive"],
        .portal-ui #mainContent :is(a, button)[title^="Edit"],
        .portal-ui #mainContent :is(a, button)[title^="Archive"] {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
            transition: none !important;
        }
        .portal-ui #mainContent :is(a, button)[data-action="edit"]:is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[data-action="archive"]:is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Edit"]:is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Archive"]:is(:hover, :focus, :active) {
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
        .portal-ui #mainContent :is(a, button)[data-action="edit"],
        .portal-ui #mainContent :is(a, button)[data-action="archive"],
        .portal-ui #mainContent :is(a, button)[title^="Edit"],
        .portal-ui #mainContent :is(a, button)[title^="Archive"] {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
            transition: none !important;
        }
        .portal-ui #mainContent :is(a, button)[data-action="edit"]:is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[data-action="archive"]:is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Edit"]:is(:hover, :focus, :active),
        .portal-ui #mainContent :is(a, button)[title^="Archive"]:is(:hover, :focus, :active) {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
            color: #ffffff !important;
        }
        @endif
        @if(auth()->user()?->isDepartmentAdmin())
        #sidebar [x-show] > a, #sidebar [x-show] > button { font-family: inherit; font-size: .875rem; line-height: 1.25rem; font-weight: 500; }
        #sidebar [x-show] > a > i, #sidebar [x-show] > button > i { font-size: .875rem; width: 1rem; text-align: center; flex-shrink: 0; }
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

        /*
         * Tablet navigation (600px–1280px): retain one stable content column
         * and use the existing mobile menu controls as an off-canvas drawer.
         * rem/clamp values keep the layout proportional across portrait and
         * landscape Android tablets.
         */
        @media (min-width: 37.5rem) and (max-width: 80rem) {
            .department-admin-tablet-role-badge { display: none !important; }
            #mobileTopBar {
                display: flex !important;
            }

            #sidebarOverlay {
                display: block !important;
            }

            #sidebar {
                inset: 0 auto 0 0 !important;
                width: min(18rem, 82vw) !important;
                min-width: min(18rem, 82vw) !important;
                max-width: 82vw !important;
                height: auto !important;
                max-height: none !important;
                opacity: 0;
                transform: translateX(-100%) !important;
                transition: transform .3s ease, opacity .2s ease !important;
            }

            /* A desktop preference must not reduce the tablet drawer. */
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
                opacity: 1;
                transform: translateX(0) !important;
            }

            #sidebarCloseBtn {
                display: flex !important;
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

        @if(auth()->user()?->isDepartmentAdmin())
        /* Keep BCPC and SWD administrator pages within the tablet viewport. */
        @media (min-width: 37.5rem) and (max-width: 80rem) {
            #mobileTopBar {
                display: none !important;
            }

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
            #mobileTopBar,
            #mainContent .portal-page-clock,
            #mainContent .portal-clock-only-header,
            #mainContent .department-admin-tablet-role-badge {
                display: none !important;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]),
            #mainContent > header div:has(> img[alt="User Profile"]) {
                display: flex !important;
            }

            #mainContent .portal-page-header div:has(> img[alt="User Profile"]) > div,
            #mainContent > header div:has(> img[alt="User Profile"]) > div {
                display: none !important;
            }

            #mainContent .tablet-header-row {
                display: grid !important;
                grid-template-columns: 2.25rem minmax(0, 1fr) auto;
                align-items: center;
                column-gap: .75rem;
            }

            #mainContent .tablet-header-row > #mobileMenuToggle {
                grid-column: 1;
                grid-row: 1;
            }

            #mainContent .tablet-header-row > #mobileMenuToggle + div {
                grid-column: 2;
                grid-row: 1;
                min-width: 0;
            }

            #mainContent .tablet-header-row > #mobileMenuToggle + div ~ div:last-child {
                grid-column: 3;
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
    </style>
    
    <script>
        document.documentElement.classList.add('js');
    </script>
</head>
<body class="portal-ui {{ auth()->user()?->isBcpcAdmin() ? 'bcpc-admin-header-theme' : (auth()->user()?->isVawcAdmin() ? 'swd-admin-header-theme' : '') }} font-['Raleway','Montserrat',sans-serif] bg-gray-100 text-gray-600 min-h-screen antialiased" style="--portal-user-initials: '{{ strtoupper(mb_substr((string) auth()->user()?->first_name, 0, 1) . mb_substr((string) auth()->user()?->last_name, 0, 1)) }}';">
    
    <!-- Mobile Sticky Top Bar -->
    <div id="mobileTopBar" class="lg:hidden sticky top-0 z-30 bg-purple-900 text-white flex items-center justify-between px-4 py-2.5 shadow-md border-b border-purple-800">
        <div class="flex items-center gap-3">
            <button 
                type="button"
                id="mobileMenuToggle" 
                class="bg-purple-800/80 hover:bg-purple-700 text-white p-2 rounded-lg transition-colors focus:outline-none flex items-center justify-center w-9 h-9"
                aria-label="Toggle menu"
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
               rounded-r-3xl lg:rounded-none"
    >
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
                        <i class="fas fa-folder-open text-[11px] w-4 text-center flex-shrink-0"></i>
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
                        <i class="fas fa-child text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">Archived Barangay Cases</span>
                    </a>
                    @endif

                    @if(auth()->user()->isDepartmentAdmin())
<a href="{{ route('admin.referrals.archived') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.referrals.archived') ? 'bg-purple-600 text-white shadow-sm font-semibold' : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}" title="Archived Referrals">
<i class="fas fa-archive text-[11px] w-4 text-center flex-shrink-0"></i><span class="sidebar-text whitespace-nowrap overflow-hidden">Archived Referrals</span>
</a>
@endif
<!-- Archived Users Link -->
                    <a href="{{ route('admin.users.archived') }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.users.archived')
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold'
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="Archived Users">
                        <i class="fas fa-user-slash text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">Archived Users</span>
                    </a>

                    @if(auth()->user()->isDepartmentAdmin())
                    <a href="{{ route('admin.quarterly-reports.archived', ['section' => 'quarterly']) }}"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-150
                              {{ request()->routeIs('admin.quarterly-reports.archived') && request('section') === 'quarterly'
                                 ? 'bg-purple-600 text-white shadow-sm font-semibold'
                                 : 'text-purple-200 hover:bg-purple-700/50 hover:text-white' }}"
                       title="Archived Quarterly Reports">
                        <i class="fas fa-file-alt text-[11px] w-4 text-center flex-shrink-0"></i>
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
                        <i class="fas fa-child text-[11px] w-4 text-center flex-shrink-0"></i>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden">{{ auth()->user()->isVawcAdmin() ? 'Archived Barangay Cases' : 'Archived BCPC Reports' }}</span>
                    </a>
                    @endunless
                </div>
            </div>


        </nav>
        
        <!-- Sidebar Footer -->
        <div class="py-4 border-t border-white sidebar-menu">
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
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

    @if((session('success') || session('error')) && !session('generated_bcpc_quarterly_report') && !session('generated_vawc_quarterly_report'))
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
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
        
        // Always start each visit with the desktop sidebar expanded.
        localStorage.setItem('sidebarCollapsed', 'false');
        
        function collapseSidebar() {
            sidebar.classList.add('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'true');
        }
        
        function expandSidebar() {
            sidebar.classList.remove('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'false');
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
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                // Open sidebar with fade in from left
                sidebar.classList.add('sidebar-open');
                // Show overlay
                sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
            });
        }
        
        // Function to close mobile sidebar
        function closeMobileSidebar() {
            // Close sidebar with fade out to left
            sidebar.classList.remove('sidebar-open');
            // Hide overlay
            sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
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
        
        // Handle logout button
        const logoutForm = document.getElementById('logout-form');
        const logoutButton = document.getElementById('logout-button');
        
        if (logoutButton) {
            logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Logout',
                    html: '<div class="text-center py-4">' +
                        '<i class="fas fa-sign-out-alt text-5xl text-purple-500 mb-4"></i>' +
                        '<p class="text-gray-700">Are you sure you want to log out?</p>' +
                        '</div>',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, log me out',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#b91c1c',
                    cancelButtonColor: '#ef4444',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-xl',
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
</body>
</html>
