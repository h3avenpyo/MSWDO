<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MSWDO Admin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <!-- Anti-flicker pre-render script for collapsed admin sidebar -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('mswdo_admin_sidebar_collapsed') === 'true' && window.innerWidth >= 1200) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    @stack('head')
    <style>
        :root {
            /* Theme Color Palette */
            --primary: #1A237E;
            --primary-hover: #121858;
            --primary-dark: #121858;
            --sidebar-bg: #1A237E;
            --background: #F5F7FB;
            --surface: #FFFFFF;
            --border: #E5E7EB;
            --accent-yellow: #FBC02D;
            
            /* Text Colors */
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            
            /* Status Colors */
            --success: #16A34A;
            --success-bg: #ECFDF5;
            --warning: #F59E0B;
            --warning-bg: #FFF7ED;
            --danger: #DC2626;
            --danger-bg: #FEF2F2;
            --info: #3B82F6;
            --info-bg: #EEF2FF;
            --purple: #7C3AED;
            --purple-bg: #F3E8FF;
            
            /* Icon Colors */
            --icon-blue: #3B82F6;
            --icon-green: #16A34A;
            --icon-purple: #7C3AED;
            --icon-teal: #0D9488;
            
            /* Dimensions */
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 72px;
            --content-padding: 24px;
            --radius: 16px;
            --shadow: 0 10px 30px rgba(15,23,42,.06);
            --shadow-hover: 0 20px 40px rgba(15,23,42,.1);
            
            /* Typography */
            --font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }
        
        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            background: var(--background);
            color: var(--text-primary);
            font-family: var(--font-family);
            min-height: 100vh;
        }
        body { font-size: 14px; line-height: 1.5; overflow-x: hidden; }
        h1, h2, h3, h4, h5, h6 { margin: 0; font-weight: 600; letter-spacing: -0.01em; }
        button { font-family: inherit; cursor: pointer; }
        input, select, textarea { font-family: inherit; font-size: 14px; }

        .app { display: flex; min-height: 100vh; flex-direction: row; }

        /* ---------- Sidebar ---------- */
        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
            background: var(--sidebar-bg);
            color: #FFFFFF;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1001;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease, width .3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-100%);
        }
        .sidebar.show { transform: translateX(0); }
        .sidebar-brand {
            height: 72px;
            padding: 0 1rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff;
            font-weight: 700;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            white-space: nowrap;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-brand-content {
            display: flex;
            align-items: center;
            gap: .65rem;
            overflow: hidden;
            min-width: 0;
            color: #FFFFFF;
        }
        .sidebar-brand-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            flex-shrink: 0;
            transition: transform .2s ease;
        }
        .sidebar-brand-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: opacity .2s ease;
        }
        .sidebar-toggle-btn {
            width: 30px;
            height: 30px;
            min-width: 30px;
            border-radius: 8px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            transition: all .2s ease;
            flex-shrink: 0;
        }
        .sidebar-toggle-btn:hover {
            background: rgba(255,255,255,.24);
            border-color: var(--accent-yellow);
            color: var(--accent-yellow);
            transform: scale(1.05);
        }
        .sidebar-toggle-btn .toggle-icon {
            transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-brand i, .sidebar-brand [data-lucide] {
            width: 24px;
            height: 24px;
            color: var(--accent-yellow);
            flex-shrink: 0;
        }
        .sidebar-menu {
            list-style: none;
            margin: 0;
            padding: 1rem 0;
            flex: 1;
            overflow-y: auto;
        }
        .sidebar-menu li { margin-bottom: .2rem; }
        .sidebar-menu a {
            color: rgba(255,255,255,.75);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            font-size: .9rem;
            border-left: 3px solid transparent;
            transition: all .2s ease;
        }
        .sidebar-menu a:hover {
            background: rgba(255,255,255,.1);
            color: var(--accent-yellow);
        }
        .sidebar-menu a.active {
            background: rgba(255,255,255,.1);
            color: var(--accent-yellow);
            border-left-color: var(--accent-yellow);
            font-weight: 600;
        }
        .sidebar-menu a i, .sidebar-menu a [data-lucide] {
            width: 20px;
            height: 20px;
            text-align: center;
        }

        /* ---------- Main Area ---------- */
        .main {
            flex: 1;
            min-width: 0;
            margin-left: 0;
            padding: 16px;
            padding-top: 86px;
            width: 100%;
            display: flex;
            flex-direction: column;
            transition: margin-left .3s cubic-bezier(0.4, 0, 0.2, 1), width .3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ---------- Sidebar Overlay ---------- */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active { display: block !important; }

        /* ---------- Mobile Header ---------- */
        .mobile-header {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 998;
            background: #1A237E;
            color: #fff;
            padding: 0 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }
        .mobile-header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }
        .mobile-logo {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #FBC02D;
            padding: 2px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mobile-logo-img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .mobile-brand-text { flex: 1; min-width: 0; }
        .mobile-brand-title {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mobile-brand-subtitle {
            font-size: 11px;
            color: rgba(255,255,255,0.8);
            margin: 2px 0 0 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #ffffff;
            cursor: pointer;
            padding: 8px;
            flex-shrink: 0;
            margin-right: 12px;
        }
        .mobile-menu-icon { width: 28px; height: 28px; }

        /* ---------- Desktop (1200px+) ---------- */
        @media (min-width: 1200px) {
            .sidebar {
                transform: translateX(0) !important;
                z-index: 1000 !important;
                width: var(--sidebar-width);
            }
            .sidebar.show { transform: translateX(0) !important; }
            .sidebar-brand { justify-content: space-between; padding: 0 1rem; }
            .sidebar-brand-text { display: inline; }
            .main {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
                padding: var(--content-padding);
                padding-top: var(--content-padding);
            }
            .mobile-header { display: none !important; }

            /* Desktop Collapsed state */
            html.sidebar-collapsed .sidebar,
            body.sidebar-collapsed .sidebar,
            .sidebar.collapsed {
                width: var(--sidebar-collapsed-width) !important;
            }
            html.sidebar-collapsed .sidebar-brand,
            body.sidebar-collapsed .sidebar-brand,
            .sidebar.collapsed .sidebar-brand {
                padding: 0.5rem 0 !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 0.35rem !important;
                height: 76px !important;
            }
            html.sidebar-collapsed .sidebar-brand-text,
            body.sidebar-collapsed .sidebar-brand-text,
            .sidebar.collapsed .sidebar-brand-text {
                display: none !important;
            }
            html.sidebar-collapsed .sidebar-brand-logo,
            body.sidebar-collapsed .sidebar-brand-logo,
            .sidebar.collapsed .sidebar-brand-logo {
                width: 32px !important;
                height: 32px !important;
            }
            html.sidebar-collapsed .sidebar-toggle-btn .toggle-icon,
            body.sidebar-collapsed .sidebar-toggle-btn .toggle-icon,
            .sidebar.collapsed .sidebar-toggle-btn .toggle-icon {
                transform: rotate(180deg);
            }
            html.sidebar-collapsed .sidebar-menu a,
            body.sidebar-collapsed .sidebar-menu a,
            .sidebar.collapsed .sidebar-menu a {
                padding: 0.85rem 0 !important;
                justify-content: center !important;
                position: relative;
            }
            html.sidebar-collapsed .sidebar-menu a .menu-text,
            body.sidebar-collapsed .sidebar-menu a .menu-text,
            .sidebar.collapsed .sidebar-menu a .menu-text {
                display: none;
                position: absolute;
                left: calc(var(--sidebar-collapsed-width) + 8px);
                top: 50%;
                transform: translateY(-50%);
                background: #0F172A;
                color: #FFFFFF;
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
                z-index: 1005;
                box-shadow: 0 4px 12px rgba(0,0,0,0.25);
                pointer-events: none;
            }
            html.sidebar-collapsed .sidebar-menu a:hover .menu-text,
            body.sidebar-collapsed .sidebar-menu a:hover .menu-text,
            .sidebar.collapsed .sidebar-menu a:hover .menu-text {
                display: block;
            }
            html.sidebar-collapsed .sidebar-menu-header,
            body.sidebar-collapsed .sidebar-menu-header,
            .sidebar.collapsed .sidebar-menu-header {
                font-size: 0 !important;
                height: 1px !important;
                padding: 0 !important;
                margin: 0.5rem 0.75rem !important;
                border-top: 1px solid rgba(255,255,255,.15) !important;
                overflow: hidden !important;
            }
            html.sidebar-collapsed .main,
            body.sidebar-collapsed .main,
            .main.expanded {
                margin-left: var(--sidebar-collapsed-width) !important;
                width: calc(100% - var(--sidebar-collapsed-width)) !important;
            }
        }

        /* ---------- Tablet (768px - 1199px): icon-only sidebar ---------- */
        @media (min-width: 768px) and (max-width: 1199px) {
            .sidebar { width: 72px !important; transform: translateX(0) !important; z-index: 1000 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .sidebar-brand { justify-content: center; padding: 0.5rem 0 !important; flex-direction: column !important; align-items: center !important; gap: 0.35rem !important; height: 76px !important; }
            .sidebar-brand-text { display: none !important; }
            .sidebar-brand-logo { width: 34px !important; height: 34px !important; }
            .sidebar-toggle-btn { display: none !important; }
            .sidebar-menu { padding: 0.75rem 0; }
            .sidebar-menu a { position: relative; justify-content: center; padding: 0.95rem 0 !important; }
            .sidebar-menu a .menu-text {
                display: none;
                position: absolute;
                left: 72px;
                top: 50%;
                transform: translateY(-50%);
                background: var(--primary-dark);
                color: #fff;
                padding: 0.4rem 0.65rem;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
                z-index: 1002;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }
            .sidebar-menu a:hover .menu-text { display: block; }
            .sidebar-menu-header {
                font-size: 0 !important;
                height: 1px !important;
                padding: 0 !important;
                margin: 0.5rem 0.75rem !important;
                border-top: 1px solid rgba(255,255,255,.15) !important;
                overflow: hidden !important;
            }
            .sidebar-overlay { display: none !important; }
            .main {
                margin-left: 72px !important;
                width: calc(100% - 72px) !important;
                padding: 20px !important;
                padding-top: 20px !important;
            }
            .mobile-header { display: none !important; }
        }

        /* ---------- Mobile (< 768px) ---------- */
        @media (max-width: 767.98px) {
            .sidebar-toggle-btn { display: none !important; }
        }

        /* ---------- Stat Cards Shared ---------- */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        @media (max-width: 1199px) { .stat-cards { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 575px) { .stat-cards { grid-template-columns: 1fr 1fr; gap: 12px; } }

        .stat-card {
            background: var(--surface);
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .stat-card-blue::before { background: var(--icon-blue); }
        .stat-card-green::before { background: var(--icon-green); }
        .stat-card-purple::before { background: var(--icon-purple); }
        .stat-card-teal::before { background: var(--icon-teal); }
        .stat-card-orange::before { background: #F59E0B; }

        .stat-card-content { flex: 1; }
        .stat-card-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }
        .stat-card-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.1;
        }
        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-card-icon svg { width: 22px; height: 22px; }
        .stat-card-blue .stat-card-icon { background: var(--info-bg); color: var(--icon-blue); }
        .stat-card-green .stat-card-icon { background: var(--success-bg); color: var(--icon-green); }
        .stat-card-purple .stat-card-icon { background: var(--purple-bg); color: var(--icon-purple); }
        .stat-card-teal .stat-card-icon { background: #CCFBF1; color: var(--icon-teal); }
        .stat-card-orange .stat-card-icon { background: #FFF7ED; color: #F59E0B; }

        /* Hide page-header date/time + avatar on small screens */
        @media (max-width: 767.98px) {
            header #currentDateTime,
            header [title^="Admin:"] { display: none !important; }
        }

        /* ── Mobile menu as dropdown below the top bar (welcome-page style) ── */
        @media (max-width: 767.98px) {
            .sidebar {
                top: 72px !important;
                left: 0 !important;
                right: 0 !important;
                bottom: auto !important;
                width: 100% !important;
                max-width: none !important;
                height: auto !important;
                max-height: calc(100vh - 72px) !important;
                overflow-y: auto !important;
                z-index: 997 !important;
                transform: translateY(-8px) !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
                border-radius: 0 0 14px 14px !important;
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.22) !important;
            }
            .sidebar.show {
                transform: translateY(0) !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
            .sidebar-brand { display: none !important; }
            .sidebar-menu { padding: .5rem !important; }
            .sidebar-menu a {
                justify-content: flex-start !important;
                gap: .75rem !important;
            }
            .sidebar-overlay, .sidebar-overlay.active { display: none !important; pointer-events: none !important; }
        }

        /* Uniform SweetAlert & Custom Modal Styles */
        .swal2-popup {
            border-radius: 16px !important;
            padding: 1.5rem !important;
            font-family: inherit !important;
            max-width: 95vw !important;
            box-sizing: border-box !important;
        }
        .swal2-title {
            color: #1A237E !important;
            font-weight: 700 !important;
            font-size: 1.35rem !important;
        }
        .swal2-html-container {
            font-size: 0.925rem !important;
            color: #374151 !important;
            max-height: 75vh !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        .swal2-confirm {
            background-color: #1A237E !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 10px 24px !important;
        }
        .swal2-cancel {
            background-color: #EF4444 !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 10px 24px !important;
        }
        .swal2-actions {
            flex-direction: row !important;
            gap: 12px;
        }
        @media (max-width: 575.98px) {
            .swal2-popup {
                padding: 1rem 0.75rem !important;
                border-radius: 12px !important;
            }
            .swal2-title {
                font-size: 1.1rem !important;
            }
            .swal2-actions {
                flex-direction: column-reverse !important;
                gap: 8px !important;
                width: 100% !important;
                margin-top: 1rem !important;
            }
            .swal2-actions button {
                width: 100% !important;
                margin: 0 !important;
                height: 44px !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app">
    @php
        $logo = 'IserveIcon.png';
        if (!file_exists(public_path('images/IserveIcon.png'))) {
            $logo = null;
            if (file_exists(public_path('images/mswdo-logo.png'))) {
                $logo = 'mswdo-logo.png';
            } else {
                $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
                if (!empty($files)) $logo = basename($files[0]);
            }
        }
    @endphp

    <!-- Mobile Header -->
    <div class="mobile-header">
        <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Toggle Navigation">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <div class="mobile-header-brand">
            <div class="mobile-brand-text">
                <h1 class="mobile-brand-title">iSERVE SILANG</h1>
                <p class="mobile-brand-subtitle">@yield('page_title', 'Admin Portal')</p>
            </div>
            <div class="mobile-logo">
                @if($logo)
                    <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img" alt="MSWDO Logo">
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-content">
                <img src="{{ asset('images/IserveIcon.png') }}" class="sidebar-brand-logo" alt="iSERVE">
                <span class="sidebar-brand-text">MSWDO Admin</span>
            </div>
            <button type="button" class="sidebar-toggle-btn" id="sidebarCollapseBtn" onclick="toggleSidebarCollapse()" aria-label="Collapse sidebar" title="Collapse sidebar">
                <svg class="toggle-icon" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}" data-tooltip="Dashboard" title="Dashboard">
                    <i data-lucide="layout-dashboard"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/admin/add-officers" class="{{ request()->is('admin/add-officers*') ? 'active' : '' }}" data-tooltip="Add Officers" title="Add Officers">
                    <i data-lucide="user-plus"></i>
                    <span class="menu-text">Add Officers</span>
                </a>
            </li>
            <li>
                <a href="/admin/officers-directory" class="{{ request()->is('admin/officers-directory*') ? 'active' : '' }}" data-tooltip="Officers Directory" title="Officers Directory">
                    <i data-lucide="users"></i>
                    <span class="menu-text">Officers Directory</span>
                </a>
            </li>
            <li>
                <a href="/admin/password-reset-management" class="{{ request()->is('admin/password-reset-management*') ? 'active' : '' }}" data-tooltip="Password Resets" title="Password Resets">
                    <i data-lucide="key"></i>
                    <span class="menu-text">Password Resets</span>
                </a>
            </li>
            <li class="sidebar-menu-header" style="border-top:1px solid rgba(255,255,255,.1);margin-top:.75rem;padding-top:.75rem;padding-left:1.5rem;padding-right:1.5rem;font-size:11px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:rgba(255,255,255,0.45);">
                Pre-Record Data Entry
            </li>
            <li>
                <a href="{{ route('admin.historical-data.financial-intake') }}" class="{{ request()->routeIs('admin.historical-data.financial-intake*') ? 'active' : '' }}" data-tooltip="Financial Intake" title="Financial Intake">
                    <i data-lucide="history"></i>
                    <span class="menu-text">Financial Intake</span>
                </a>
            </li>
            <li style="border-top:1px solid rgba(255,255,255,.1);margin-top:.5rem;padding-top:.5rem;">
                <a href="#" onclick="confirmLogout(event)" data-tooltip="Logout" title="Logout">
                    <i data-lucide="log-out"></i>
                    <span class="menu-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Main Content Area -->
    <div class="main">
        @yield('content')
    </div>
</div>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
    @csrf
</form>

<script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        if (!sidebar) return;
        if (sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        } else {
            sidebar.classList.add('show');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        updateMobileMenuIcon();
    }

    function toggleSidebarCollapse(forceState = null) {
        var sidebar = document.getElementById('sidebar');
        var collapseBtn = document.getElementById('sidebarCollapseBtn');
        var isCurrentlyCollapsed = document.documentElement.classList.contains('sidebar-collapsed') ||
            document.body.classList.contains('sidebar-collapsed') ||
            (sidebar && sidebar.classList.contains('collapsed'));

        var willCollapse = forceState !== null ? forceState : !isCurrentlyCollapsed;

        if (willCollapse) {
            document.documentElement.classList.add('sidebar-collapsed');
            document.body.classList.add('sidebar-collapsed');
            if (sidebar) sidebar.classList.add('collapsed');
            if (collapseBtn) {
                collapseBtn.setAttribute('title', 'Expand sidebar');
                collapseBtn.setAttribute('aria-label', 'Expand sidebar');
            }
            try {
                localStorage.setItem('mswdo_admin_sidebar_collapsed', 'true');
            } catch (e) {}
        } else {
            document.documentElement.classList.remove('sidebar-collapsed');
            document.body.classList.remove('sidebar-collapsed');
            if (sidebar) sidebar.classList.remove('collapsed');
            if (collapseBtn) {
                collapseBtn.setAttribute('title', 'Collapse sidebar');
                collapseBtn.setAttribute('aria-label', 'Collapse sidebar');
            }
            try {
                localStorage.setItem('mswdo_admin_sidebar_collapsed', 'false');
            } catch (e) {}
        }

        // Trigger window resize so responsive charts and grids recalculate
        window.dispatchEvent(new Event('resize'));
    }

    function updateMobileMenuIcon() {
        var sidebar = document.getElementById('sidebar');
        var btn = document.getElementById('mobileMenuBtn');
        if (!sidebar || !btn) return;
        var path = btn.querySelector('path');
        if (!path) return;
        var open = sidebar.classList.contains('show');
        path.setAttribute('d', open ? 'M6 18L18 6M6 6l12 12' : 'M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5');
    }

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to log out?',
            icon: 'warning',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, log out',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        if (document.documentElement.classList.contains('sidebar-collapsed')) {
            var collapseBtn = document.getElementById('sidebarCollapseBtn');
            if (collapseBtn) {
                collapseBtn.setAttribute('title', 'Expand sidebar');
                collapseBtn.setAttribute('aria-label', 'Expand sidebar');
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var sb = document.getElementById('sidebar');
                var ov = document.getElementById('sidebarOverlay');
                if (sb && sb.classList.contains('show')) {
                    sb.classList.remove('show');
                    if (ov) ov.classList.remove('active');
                    document.body.style.overflow = '';
                    updateMobileMenuIcon();
                }
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1200) {
                var sb = document.getElementById('sidebar');
                var ov = document.getElementById('sidebarOverlay');
                if (sb && sb.classList.contains('show')) {
                    sb.classList.remove('show');
                    if (ov) ov.classList.remove('active');
                    document.body.style.overflow = '';
                    updateMobileMenuIcon();
                }
            }
        });
    });
</script>
@include('admin.partials.account-status')
@stack('scripts')
</body>
</html>
