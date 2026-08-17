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
            --sidebar-width: 260px;
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
            transition: transform .3s ease;
            transform: translateX(-100%);
        }
        .sidebar.show { transform: translateX(0); }
        .sidebar-brand {
            height: 72px;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .sidebar-brand i, .sidebar-brand [data-lucide] {
            width: 24px;
            height: 24px;
            color: var(--accent-yellow);
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
            .sidebar { transform: translateX(0) !important; z-index: 1000 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main {
                margin-left: var(--sidebar-width) !important;
                width: calc(100% - var(--sidebar-width)) !important;
                padding: var(--content-padding) !important;
                padding-top: var(--content-padding) !important;
            }
            .mobile-header { display: none !important; }
        }

        /* ---------- Tablet (768px - 1199px): icon-only sidebar ---------- */
        @media (min-width: 768px) and (max-width: 1199px) {
            .sidebar { width: 72px !important; transform: translateX(0) !important; z-index: 1000 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .sidebar-brand { justify-content: center; padding: 1.25rem 0 !important; }
            .sidebar-brand span { display: none !important; }
            .sidebar-menu { padding: 0.75rem 0; }
            .sidebar-menu a { position: relative; justify-content: center; padding: 0.95rem 0 !important; }
            .sidebar-menu a span {
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
            .sidebar-menu a:hover span { display: block; }
            .sidebar-overlay { display: none !important; }
            .main {
                margin-left: 72px !important;
                width: calc(100% - 72px) !important;
                padding: 20px !important;
                padding-top: 20px !important;
            }
            .mobile-header { display: none !important; }
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
    </style>
    @stack('styles')
</head>
<body>
<div class="app">
    @php
        $logo = null;
        if (file_exists(public_path('images/mswdo-logo.png'))) {
            $logo = 'mswdo-logo.png';
        } else {
            $files = glob(public_path('images/*.{png,jpg,jpeg,svg}'), GLOB_BRACE);
            if (!empty($files)) $logo = basename($files[0]);
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
                <h1 class="mobile-brand-title">MSWDO SILANG</h1>
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
            <i data-lucide="layout-grid"></i>
            <span>MSWDO Admin</span>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/admin/add-officers" class="{{ request()->is('admin/add-officers*') ? 'active' : '' }}">
                    <i data-lucide="user-check"></i>
                    <span>Add Officers</span>
                </a>
            </li>
            <li style="border-top:1px solid rgba(255,255,255,.1);margin-top:.5rem;padding-top:.5rem;">
                <a href="#" onclick="confirmLogout(event)">
                    <i data-lucide="log-out"></i>
                    <span>Logout</span>
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
    }

    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to log out of the admin panel?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#DC2626',
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

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var sb = document.getElementById('sidebar');
                var ov = document.getElementById('sidebarOverlay');
                if (sb && sb.classList.contains('show')) {
                    sb.classList.remove('show');
                    if (ov) ov.classList.remove('active');
                    document.body.style.overflow = '';
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
                }
            }
        });
    });
</script>
@stack('scripts')
</body>
</html>
