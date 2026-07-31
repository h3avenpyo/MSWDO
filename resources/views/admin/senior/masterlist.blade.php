<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen Masterlist</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1A237E;
            --primary-hover: #121858;
            --sidebar-bg: #1A237E;
            --accent-yellow: #FBC02D;
            --background: #F5F7FB;
            --surface: #FFFFFF;
            --border: #E5E7EB;
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            --success: #16A34A;
            --success-bg: #ECFDF5;
            --danger: #DC2626;
            --danger-bg: #FEF2F2;
            --info: #3B82F6;
            --info-bg: #EEF2FF;
            --purple: #7C3AED;
            --purple-bg: #F3E8FF;
            --shadow: 0 10px 30px rgba(15,23,42,.08);
            --font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; background: var(--background); color: var(--text-primary); font-family: var(--font-family); overflow-x: hidden; overflow-y: auto; }
        body { font-size: 14px; line-height: 1.5; }
        h1, h2, h3, h4 { margin: 0; font-weight: 600; letter-spacing: -0.01em; }
        button { font-family: inherit; cursor: pointer; }
        input, select, textarea { font-family: inherit; font-size: 14px; }
        .app { display: flex; min-height: 100vh; }

        /* ---------- Sidebar ---------- */
        .sidebar {
            width: 260px; flex-shrink: 0; background: var(--primary); color: #FFFFFF;
            position: fixed; left: 0; top: 0; height: 100vh; z-index: 1000;
            display: flex; flex-direction: column; transition: transform .3s ease;
        }
        .sidebar-brand {
            height: 72px; padding: 0 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff; font-weight: 700; font-size: 1.1rem;
            display: flex; align-items: center; gap: .65rem;
        }
        .sidebar-brand i, .sidebar-brand [data-lucide] { width: 24px; height: 24px; color: var(--accent-yellow); }
        .sidebar-menu { list-style: none; margin: 0; padding: 1rem 0; flex: 1; }
        .sidebar-menu li { margin-bottom: .2rem; }
        .sidebar-menu a {
            color: rgba(255,255,255,.75); padding: .75rem 1.5rem;
            display: flex; align-items: center; gap: .75rem;
            text-decoration: none; font-size: .9rem;
            border-left: 3px solid transparent; transition: all .2s ease;
        }
        .sidebar-menu a:hover { background: rgba(255,255,255,.1); color: var(--accent-yellow); }
        .sidebar-menu a.active { background: rgba(255,255,255,.1); color: var(--accent-yellow); border-left-color: var(--accent-yellow); }
        .sidebar-menu a i, .sidebar-menu a [data-lucide] { width: 20px; height: 20px; text-align: center; }

        /* ---------- Main ---------- */
html, body { overflow-x: hidden; overflow-y: auto; height: 100%; }
.main {
    flex: 1; min-width: 0; margin-left: 260px; padding: 32px;
    max-width: calc(100% - 260px); height: 100vh;
    display: flex; flex-direction: column; overflow: hidden;
}
.main-scroll{flex:1;overflow-y:auto;min-height:0;scrollbar-width:none;-ms-overflow-style:none;border-radius:16px;display:flex;flex-direction:column;}
.main-scroll::-webkit-scrollbar{display:none;}

        /* ---------- Buttons ---------- */
        .btn {
            border: 1px solid var(--border); background: var(--surface);
            color: var(--text-primary); padding: 10px 20px; border-radius: 10px;
            font-size: 14px; font-weight: 500; display: inline-flex;
            align-items: center; gap: 8px; box-shadow: var(--shadow);
            transition: all 0.2s ease; height: 42px; cursor: pointer; text-decoration: none;
        }
        .btn:hover { border-color: var(--primary); transform: translateY(-1px); }
        .btn.primary { background: var(--primary); color: #FFFFFF; border-color: var(--primary); }
        .btn.primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
        .btn.danger { background: var(--danger); color: #FFFFFF; border-color: var(--danger); }
        .btn.danger:hover { background: #B91C1C; border-color: #B91C1C; }
        .btn.success { background: var(--success); color: #FFFFFF; border-color: var(--success); }
        .btn.success:hover { background: #15803D; border-color: #15803D; }
        .btn.warning { background: #F59E0B; color: #FFFFFF; border-color: #F59E0B; }
        .btn.warning:hover { background: #D97706; border-color: #D97706; }
        .btn.ghost { background: transparent; box-shadow: none; border-color: transparent; color: var(--text-secondary); }
        .btn.ghost:hover { background: var(--background); color: var(--text-primary); }
        .btn:disabled { opacity: 0.45; cursor: not-allowed; pointer-events: none; }
        .btn-sm { padding: 6px 12px; font-size: 13px; height: 36px; }

        /* ---------- Table Card ---------- */
        .table-card {
            background: var(--surface); border-radius: 16px;
            border: 1px solid var(--border); box-shadow: var(--shadow);
            padding: 2rem; display: flex; flex-direction: column;
            overflow: hidden; flex: 1; min-height: 0;
        }
        .table-card-title {
            font-size: 1.25rem; font-weight: 700; color: var(--text-primary);
            margin-top: 0; margin-bottom: 1.5rem; flex-shrink: 0;
        }

        /* ---------- Filter Section ---------- */
        .filter-section { margin-bottom: 1.5rem; flex-shrink: 0; }
        .filter-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .filter-left { display: flex; gap: 12px; flex: 1; min-width: 0; flex-wrap: wrap; }
        .filter-right { display: flex; gap: 12px; flex-shrink: 0; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group.search-group { flex: 1; min-width: 250px; }
        .filter-group.select-group { min-width: 200px; }
        .filter-label { font-size: 0.75rem; font-weight: 600; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; }

        /* ---------- Search Input ---------- */
        .input-group { display: flex; align-items: center; height: 44px; }
        .input-group input {
            flex: 1; height: 44px; border: 1px solid var(--border); border-right: none;
            border-radius: 6px 0 0 6px; padding: 0 1rem; font-size: 0.875rem;
            color: var(--text-primary); background: var(--surface); transition: all 0.2s ease;
        }
        .input-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,58,138,0.15); }
        .input-group .search-btn {
            background-color: var(--primary); color: #ffffff; border: none;
            padding: 0 1.25rem; border-radius: 0 6px 6px 0; cursor: pointer;
            height: 44px; display: flex; align-items: center; justify-content: center;
            transition: background 0.2s;
        }
        .input-group .search-btn:hover { background-color: var(--primary-hover); }

        /* ---------- Select ---------- */
        .filter-select {
            height: 44px; border: 1px solid var(--border); border-radius: 6px;
            padding: 0 2.25rem 0 1rem; font-size: 0.875rem; color: var(--text-primary);
            background: var(--surface); cursor: pointer; width: 100%;
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px 12px;
            transition: all 0.2s ease;
        }
        .filter-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,58,138,0.15); }

        /* ---------- Table Scroll ---------- */
        .table-scroll { flex: 1; overflow-y: auto; min-height: 0; border-radius: 8px; border: 1px solid var(--border); overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; }
        .table-scroll::-webkit-scrollbar { display: none; }
        .table-scroll table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .table-scroll thead { position: sticky; top: 0; z-index: 1; background: var(--surface); }
        .table-scroll th {
            padding: 12px 16px; font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em;
            color: var(--text-secondary); text-align: left; border-bottom: 2px solid var(--border);
        }
        .table-scroll td {
            padding: 14px 16px; font-size: 13px; color: var(--text-primary);
            border-bottom: 1px solid var(--border); vertical-align: middle;
        }
        .table-scroll tr:hover td { background: var(--background); }

        /* ---------- Badge ---------- */
        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; font-size: 12px; font-weight: 500; white-space: nowrap; }
        .badge-active { background: var(--success-bg); color: var(--success); }
        .badge-pending { background: #FEF3C7; color: #92400E; }

        /* ---------- Dropdown ---------- */
        .dropdown { position: relative; display: inline-block; }
        .dropdown-menu {
            position: absolute; top: 100%; right: 0; z-index: 50;
            background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12); min-width: 200px;
            padding: 6px; display: none; margin-top: 4px;
        }
        .dropdown-menu.show { display: block; }
        .dropdown-item {
            display: flex; align-items: center; gap: 8px; padding: 10px 14px;
            font-size: 13px; color: var(--text-primary); border-radius: 6px;
            text-decoration: none; cursor: pointer; transition: background 0.15s;
        }
        .dropdown-item:hover { background: var(--background); }

        #seniorModal { transition: opacity 0.2s ease; }

        /* ---------- Responsive ---------- */
        @media (max-width: 1200px) {
            .main { padding: 24px; }
        }

        /* ---------- Animations ---------- */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ── Mobile Header ── */
        .mobile-header{display:none !important;position:fixed;top:0;left:0;right:0;z-index:1000;background:#1A237E;color:#fff;padding:0 16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);align-items:center;justify-content:space-between;height:80px;}
        .mobile-header-brand{display:flex;align-items:center;gap:16px;flex:1;min-width:0;}
        .mobile-logo{width:56px;height:56px;border-radius:50%;background:#FBC02D;padding:4px;flex-shrink:0;}
        .mobile-logo-img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
        .mobile-brand-text{flex:1;min-width:0;}
        .mobile-brand-title{font-size:18px;font-weight:700;color:#ffffff;margin:0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mobile-brand-subtitle{font-size:12px;color:rgba(255,255,255,0.8);margin:2px 0 0 0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mobile-menu-btn{display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:#ffffff;cursor:pointer;padding:8px;flex-shrink:0;margin-right:24px;}
        .mobile-menu-icon{width:32px;height:32px;}
        @media(max-width:767px){
            .app{flex-direction:column !important;min-height:100vh !important;}
            .main,.main-content{margin-left:0 !important;max-width:100% !important;height:auto !important;overflow:visible !important;padding:12px 14px !important;padding-top:90px !important;}
            .main-scroll{overflow:visible !important;flex:none !important;height:auto !important;}
            .table-card{margin-bottom:40px !important;padding-bottom:30px !important;}
            header{display:none !important;}
            .hamburger-btn{display:none !important;}
            .mobile-header{display:flex !important;}
        }
        @media(max-width:479px){
            .main,.main-content{padding:10px !important;padding-top:88px !important;}
            .mobile-header{height:72px !important;}
            .mobile-logo{width:48px !important;height:48px !important;}
            .mobile-brand-title{font-size:16px !important;}
            .mobile-brand-subtitle{font-size:11px !important;}
            .mobile-menu-icon{width:28px !important;height:28px !important;}
        }

        /* ── Sidebar: off-canvas by default (xs: 0–767px) ── */
        .sidebar { transform: translateX(-100%) !important; z-index: 1001 !important; }
        .sidebar.show { transform: translateX(0) !important; }
        .main, .main-content { margin-left: 0 !important; max-width: 100% !important; padding: 16px !important; padding-top: 64px !important; }
        .main { height: auto !important; overflow: visible !important; }
        .main-scroll { overflow: visible !important; flex: none !important; }

        /* ── Hamburger Button ── */
        .hamburger-btn {
            display: flex;
            position: fixed;
            top: 12px;
            left: 12px;
            z-index: 1002;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: background 0.2s;
        }
        .hamburger-btn:hover { background: var(--primary-hover); }

        /* ── Sidebar Overlay ── */
        .sidebar-overlay.active { display: block !important; }

        /* ── Responsive: Mobile (< 768px) ── */
        @media (max-width: 767px) {
            .mobile-select-all { display: flex !important; }
            .main, .main-content { padding: 12px !important; padding-top: 90px !important; }
            .topnav, .top-navbar { padding: 10px 12px !important; }
            .topnav-datetime, .navbar-datetime { display: none !important; }

            /* ── Filter → stack ── */
            .filter-section { margin-bottom: 1rem; }
            .filter-row { flex-direction: column !important; gap: 10px !important; }
            .filter-left { flex-direction: column !important; gap: 10px !important; width: 100% !important; }
            .filter-group.search-group, .filter-group.select-group { min-width: 0 !important; width: 100% !important; }
            .filter-right { width: 100% !important; flex-wrap: wrap !important; gap: 8px !important; display: flex !important; }
            .filter-right > * { flex: 1 1 calc(50% - 4px) !important; min-width: 0 !important; }
            .filter-right > a, .filter-right > button, .filter-right .btn, .filter-right > div { width: 100% !important; justify-content: center !important; text-align: center !important; padding: 8px 10px !important; font-size: 12px !important; }
            .filter-right > a { display: inline-flex !important; align-items: center !important; justify-content: center !important; }

            /* ── Table Card ── */
            .table-card { padding: 1rem !important; border-radius: 12px !important; overflow: visible !important; flex: none !important; min-height: 0 !important; height: auto !important; }
            .table-card-title { font-size: 1rem !important; margin-bottom: 1rem !important; }

            /* ── Table → Card layout ── */
            .table-scroll { border: none !important; border-radius: 0 !important; overflow: visible !important; flex: none !important; min-height: 0 !important; height: auto !important; }
            .table-scroll table { display: block !important; width: 100%; }
            .table-scroll tbody { display: block; }
            .table-scroll thead { display: none !important; }
            .table-scroll tbody tr {
                display: block;
                background: var(--surface);
                border: 1px solid #D1D5DB;
                border-radius: 10px;
                margin-bottom: 10px;
                padding: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            }
            .table-scroll tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border: none;
                font-size: 0.82rem;
                gap: 8px;
            }
            .table-scroll tbody td:not(:last-child) {
                border-bottom: 1px solid var(--border);
            }
            .table-scroll tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--text-secondary);
                font-size: 0.72rem;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                flex-shrink: 0;
                min-width: 70px;
            }
            .table-scroll tbody td.col-check {
                justify-content: flex-end;
                padding: 8px 0 4px;
                border-bottom: none;
            }
            .table-scroll tbody td.col-check::before { display: none; }
            .table-scroll tbody td[data-label="#"] {
                display: none !important;
            }
            .table-scroll tbody td[data-label="Action"] {
                justify-content: flex-end;
                padding-top: 8px;
                border-bottom: none;
            }
            .table-scroll tbody td[data-label="Action"]::before { display: none; }
            .table-scroll tbody td .badge { font-size: 0.7rem; }

            /* ── Action buttons → smaller for side-by-side ── */
            .table-scroll tbody td[data-label="Action"] .actions { gap: 6px !important; }
            .table-scroll tbody td[data-label="Action"] .actions > button,
            .table-scroll tbody td[data-label="Action"] .actions > a { padding: 4px 8px !important; height: 32px !important; min-width: 32px !important; }
            .table-scroll tbody td[data-label="Action"] .actions > button[style*="padding:6px 10px"],
            .table-scroll tbody td[data-label="Action"] .actions > a[style*="padding:6px 10px"] { padding: 4px 8px !important; }
            .table-scroll tbody td[data-label="Action"] .actions > button[style*="height:34px"],
            .table-scroll tbody td[data-label="Action"] .actions > a[style*="height:34px"] { height: 32px !important; }
            .table-scroll tbody td[data-label="Action"] .actions > button[style*="padding:6px 10px"][style*="height:34px"],
            .table-scroll tbody td[data-label="Action"] .actions > a[style*="padding:6px 10px"][style*="height:34px"] { padding: 4px 8px !important; height: 32px !important; min-width: 32px !important; }
            .table-scroll tbody td[data-label="Action"] .actions > button i,
            .table-scroll tbody td[data-label="Action"] .actions > a i { width: 14px !important; height: 14px !important; }

            /* ── Modal ── */
            #seniorModal > div { max-width: 100% !important; border-radius: 12px !important; max-height: 85vh !important; }
            #seniorModal > div > div:first-child { padding: 12px 16px !important; }
            #seniorModal > div > div:first-child h5 { font-size: 0.95rem !important; }
            #seniorModal > div > div:nth-child(2) { padding: 16px !important; }
            #seniorModal > div > div:last-child { padding: 12px 16px !important; flex-wrap: wrap !important; gap: 8px !important; }
            #seniorModal > div > div:last-child button { flex: 1; min-width: 0; justify-content: center; }
        }

        /* ── Responsive: Small Mobile (< 480px) ── */
        @media (max-width: 479px) {
            .stat-card-icon { width: 40px !important; height: 40px !important; }
            .stat-card-value { font-size: 24px !important; }
            .stat-cards { gap: 12px !important; }
            .filter-right > * { flex: 1 1 100% !important; }
            .table-scroll tbody td { font-size: 0.78rem !important; }
            .table-scroll tbody td::before { min-width: 60px !important; font-size: 0.68rem !important; }
        }

        /* ── lg: Desktops (1200px+) ── */
        @media (min-width: 1200px) {
            .sidebar { transform: translateX(0) !important; z-index: 1000 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: 260px !important; max-width: calc(100% - 260px) !important; padding: 32px !important; padding-top: 32px !important; height: 100vh !important; overflow: hidden !important; }
            .main-scroll { overflow-y: auto !important; flex: 1 !important; }
            .hamburger-btn { display: none !important; }
            .mobile-header { display: none !important; }
            header { display: none !important; }
            .desktop-datetime-container {
                display: flex !important;
                justify-content: flex-end !important;
                align-items: center !important;
                margin-bottom: 0.75rem !important;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                height: auto !important;
                padding: 0 !important;
            }
            .table-scroll table { table-layout: auto !important; width: 100% !important; }
            .table-scroll th, .table-scroll td { white-space: nowrap; padding: 14px 20px; }
            .table-scroll td[data-label="Address"], .table-scroll td[data-label="Full Name"] { white-space: normal !important; word-break: break-word; }
        }
    </style>
</head>
<body>
<div class="app">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="users" style="width:24px;height:24px"></i>
        <span>Senior Citizen</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/senior"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/senior/registration"><i data-lucide="user-plus" style="width:20px;height:20px"></i> Registration</a></li>
        <li><a href="/admin/senior/masterlist" class="active"><i data-lucide="list" style="width:20px;height:20px"></i> Masterlist</a></li>
        <li><a href="/admin/senior/birthdays"><i data-lucide="cake" style="width:20px;height:20px"></i> Birthday Beneficiaries</a></li>
        <li><a href="/admin/senior/payouts-history"><i data-lucide="history" style="width:20px;height:20px"></i> Payout History</a></li>
        <li><a href="/admin/senior/statistics"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i> Statistics</a></li>
        <li><a href="/admin/senior/reports"><i data-lucide="file-text" style="width:20px;height:20px"></i> Reports</a></li>
        <li><a href="/admin/senior/archive"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>

    <!-- Hamburger Button (fixed position) -->
    <button id="hamburgerBtn" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <i data-lucide="menu" style="width:24px;height:24px"></i>
    </button>
    <!-- Mobile Header -->
    @php
    $logo = null;
    if(file_exists(public_path('images/mswdo-logo.png'))){
        $logo='mswdo-logo.png';
    }else{
        $files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);
        if(!empty($files))
        $logo=basename($files[0]);
    }
    @endphp
    <div class="mobile-header">
        <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <div class="mobile-header-brand">
            <div class="mobile-brand-text">
                <h1 class="mobile-brand-title">MSWDO SILANG</h1>
                <p class="mobile-brand-subtitle">Senior Citizen Masterlist</p>
            </div>
            <div class="mobile-logo">
                @if($logo)
                <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
    @php
        $userName = session('admin_user_name') ?? 'Admin User';
        $words = explode(' ', $userName);
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($userName, 0, 2));
        }
    @endphp

    <!-- <div class="desktop-datetime-container" style="display:none">
        <div class="flex items-center gap-5 justify-end">
            <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="desktopDateTime"></div>
            <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">{{ $initials }}</div>
        </div>
    </div> -->

    <!-- Modern Page Header -->
    <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Senior Citizen Masterlist</h1>
        </div>
        <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
            <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
            <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">{{ $initials }}</div>
        </div>
    </header>

    <div class="main-scroll">
    <!-- Table Card -->
    <div class="table-card" style="flex:1;min-height:0;display:flex;flex-direction:column;overflow:hidden">
        <h2 class="table-card-title">Registered Senior Citizens</h2>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.senior.masterlist') }}" id="filterForm">
                <div class="filter-row">
                    <div class="filter-left">
                        <div class="filter-group search-group">
                            <label class="filter-label">Search by Name</label>
                            <div class="input-group">
                                <input type="text" name="search" placeholder="Search by name..." value="{{ request('search') }}">
                                <button type="submit" class="search-btn">
                                    <i data-lucide="search" style="width:16px;height:16px"></i>
                                </button>
                            </div>
                        </div>
                        <div class="filter-group select-group">
                            <label class="filter-label">Filter by Barangay</label>
                            <select class="filter-select" name="barangay" onchange="this.form.submit()">
                                <option value="">All Barangays</option>
                                <option value="Acacia" {{ request('barangay') == 'Acacia' ? 'selected' : '' }}>Acacia</option>
                                <option value="Adlas" {{ request('barangay') == 'Adlas' ? 'selected' : '' }}>Adlas</option>
                                <option value="Anahaw I" {{ request('barangay') == 'Anahaw I' ? 'selected' : '' }}>Anahaw I</option>
                                <option value="Anahaw II" {{ request('barangay') == 'Anahaw II' ? 'selected' : '' }}>Anahaw II</option>
                                <option value="Balite I" {{ request('barangay') == 'Balite I' ? 'selected' : '' }}>Balite I</option>
                                <option value="Balite II" {{ request('barangay') == 'Balite II' ? 'selected' : '' }}>Balite II</option>
                                <option value="Balubad" {{ request('barangay') == 'Balubad' ? 'selected' : '' }}>Balubad</option>
                                <option value="Banaba" {{ request('barangay') == 'Banaba' ? 'selected' : '' }}>Banaba</option>
                                <option value="Batas" {{ request('barangay') == 'Batas' ? 'selected' : '' }}>Batas</option>
                                <option value="Biga I" {{ request('barangay') == 'Biga I' ? 'selected' : '' }}>Biga I</option>
                                <option value="Biga II" {{ request('barangay') == 'Biga II' ? 'selected' : '' }}>Biga II</option>
                                <option value="Biluso" {{ request('barangay') == 'Biluso' ? 'selected' : '' }}>Biluso</option>
                                <option value="Bucal" {{ request('barangay') == 'Bucal' ? 'selected' : '' }}>Bucal</option>
                                <option value="Buho" {{ request('barangay') == 'Buho' ? 'selected' : '' }}>Buho</option>
                                <option value="Bulihan" {{ request('barangay') == 'Bulihan' ? 'selected' : '' }}>Bulihan</option>
                                <option value="Cabangaan" {{ request('barangay') == 'Cabangaan' ? 'selected' : '' }}>Cabangaan</option>
                                <option value="Carmen" {{ request('barangay') == 'Carmen' ? 'selected' : '' }}>Carmen</option>
                                <option value="Hoyo" {{ request('barangay') == 'Hoyo' ? 'selected' : '' }}>Hoyo</option>
                                <option value="Hukay" {{ request('barangay') == 'Hukay' ? 'selected' : '' }}>Hukay</option>
                                <option value="Iba" {{ request('barangay') == 'Iba' ? 'selected' : '' }}>Iba</option>
                                <option value="Inchican" {{ request('barangay') == 'Inchican' ? 'selected' : '' }}>Inchican</option>
                                <option value="Ipil I" {{ request('barangay') == 'Ipil I' ? 'selected' : '' }}>Ipil I</option>
                                <option value="Ipil II" {{ request('barangay') == 'Ipil II' ? 'selected' : '' }}>Ipil II</option>
                                <option value="Kalubkob" {{ request('barangay') == 'Kalubkob' ? 'selected' : '' }}>Kalubkob</option>
                                <option value="Kaong" {{ request('barangay') == 'Kaong' ? 'selected' : '' }}>Kaong</option>
                                <option value="Lalaan I" {{ request('barangay') == 'Lalaan I' ? 'selected' : '' }}>Lalaan I</option>
                                <option value="Lalaan II" {{ request('barangay') == 'Lalaan II' ? 'selected' : '' }}>Lalaan II</option>
                                <option value="Litlit" {{ request('barangay') == 'Litlit' ? 'selected' : '' }}>Litlit</option>
                                <option value="Lucsuhin" {{ request('barangay') == 'Lucsuhin' ? 'selected' : '' }}>Lucsuhin</option>
                                <option value="Lumil" {{ request('barangay') == 'Lumil' ? 'selected' : '' }}>Lumil</option>
                                <option value="Maguyam" {{ request('barangay') == 'Maguyam' ? 'selected' : '' }}>Maguyam</option>
                                <option value="Malabag" {{ request('barangay') == 'Malabag' ? 'selected' : '' }}>Malabag</option>
                                <option value="Malaking Tatyao" {{ request('barangay') == 'Malaking Tatyao' ? 'selected' : '' }}>Malaking Tatyao</option>
                                <option value="Mataas na Burol" {{ request('barangay') == 'Mataas na Burol' ? 'selected' : '' }}>Mataas na Burol</option>
                                <option value="Munting Ilog" {{ request('barangay') == 'Munting Ilog' ? 'selected' : '' }}>Munting Ilog</option>
                                <option value="Narra I" {{ request('barangay') == 'Narra I' ? 'selected' : '' }}>Narra I</option>
                                <option value="Narra II" {{ request('barangay') == 'Narra II' ? 'selected' : '' }}>Narra II</option>
                                <option value="Narra III" {{ request('barangay') == 'Narra III' ? 'selected' : '' }}>Narra III</option>
                                <option value="Paligawan" {{ request('barangay') == 'Paligawan' ? 'selected' : '' }}>Paligawan</option>
                                <option value="Pasong Langka" {{ request('barangay') == 'Pasong Langka' ? 'selected' : '' }}>Pasong Langka</option>
                                <option value="Barangay I (Poblacion)" {{ request('barangay') == 'Barangay I (Poblacion)' ? 'selected' : '' }}>Barangay I (Poblacion)</option>
                                <option value="Barangay II (Poblacion)" {{ request('barangay') == 'Barangay II (Poblacion)' ? 'selected' : '' }}>Barangay II (Poblacion)</option>
                                <option value="Barangay III (Poblacion)" {{ request('barangay') == 'Barangay III (Poblacion)' ? 'selected' : '' }}>Barangay III (Poblacion)</option>
                                <option value="Barangay IV (Poblacion)" {{ request('barangay') == 'Barangay IV (Poblacion)' ? 'selected' : '' }}>Barangay IV (Poblacion)</option>
                                <option value="Barangay V (Poblacion)" {{ request('barangay') == 'Barangay V (Poblacion)' ? 'selected' : '' }}>Barangay V (Poblacion)</option>
                                <option value="Pooc I" {{ request('barangay') == 'Pooc I' ? 'selected' : '' }}>Pooc I</option>
                                <option value="Pooc II" {{ request('barangay') == 'Pooc II' ? 'selected' : '' }}>Pooc II</option>
                                <option value="Pulong Bunga" {{ request('barangay') == 'Pulong Bunga' ? 'selected' : '' }}>Pulong Bunga</option>
                                <option value="Pulong Saging" {{ request('barangay') == 'Pulong Saging' ? 'selected' : '' }}>Pulong Saging</option>
                                <option value="Puting Kahoy" {{ request('barangay') == 'Puting Kahoy' ? 'selected' : '' }}>Puting Kahoy</option>
                                <option value="Sabutan" {{ request('barangay') == 'Sabutan' ? 'selected' : '' }}>Sabutan</option>
                                <option value="San Miguel I" {{ request('barangay') == 'San Miguel I' ? 'selected' : '' }}>San Miguel I</option>
                                <option value="San Miguel II" {{ request('barangay') == 'San Miguel II' ? 'selected' : '' }}>San Miguel II</option>
                                <option value="San Vicente I" {{ request('barangay') == 'San Vicente I' ? 'selected' : '' }}>San Vicente I</option>
                                <option value="San Vicente II" {{ request('barangay') == 'San Vicente II' ? 'selected' : '' }}>San Vicente II</option>
                                <option value="Santol" {{ request('barangay') == 'Santol' ? 'selected' : '' }}>Santol</option>
                                <option value="Tartaria" {{ request('barangay') == 'Tartaria' ? 'selected' : '' }}>Tartaria</option>
                                <option value="Tibig" {{ request('barangay') == 'Tibig' ? 'selected' : '' }}>Tibig</option>
                                <option value="Toledo" {{ request('barangay') == 'Toledo' ? 'selected' : '' }}>Toledo</option>
                                <option value="Tubuan I" {{ request('barangay') == 'Tubuan I' ? 'selected' : '' }}>Tubuan I</option>
                                <option value="Tubuan II" {{ request('barangay') == 'Tubuan II' ? 'selected' : '' }}>Tubuan II</option>
                                <option value="Tubuan III" {{ request('barangay') == 'Tubuan III' ? 'selected' : '' }}>Tubuan III</option>
                                <option value="Ulat" {{ request('barangay') == 'Ulat' ? 'selected' : '' }}>Ulat</option>
                                <option value="Yakal" {{ request('barangay') == 'Yakal' ? 'selected' : '' }}>Yakal</option>
                            </select>
                        </div>
                    </div>

                    <div class="filter-right">
                        <!-- <a href="/admin/senior/registration" style="height:44px;display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;background:#0f766e;color:white;border:none;cursor:pointer;text-decoration:none;transition:all 0.2s ease;font-family:inherit;">
                            <i data-lucide="plus" style="width:16px;height:16px"></i> Add New
                        </a> -->
                        <a href="#" style="height:44px;display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;background:#1A237E;color:white;border:none;cursor:pointer;text-decoration:none;transition:all 0.2s ease;font-family:inherit;" onclick="exportPdf(event)">
                            <i data-lucide="file-output" style="width:16px;height:16px"></i> Export PDF
                        </a>
                        <div class="dropdown" id="bulkActionDropdown">
                            <button id="bulkActionButton" onclick="toggleDropdown()" disabled style="height:44px;font-weight:600;display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:13px;background:#E0E7FF;color:#3730A3;border:1px solid #C7D2FE;cursor:pointer;transition:all 0.2s ease;font-family:inherit;opacity:0.45;">
                                <i data-lucide="archive" style="width:14px;height:14px"></i> Bulk Actions <span id="selectedCount" style="background: #3730A3; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 4px;">0</span>
                            </button>
                            <div class="dropdown-menu" id="bulkDropdownMenu">
                                <a class="dropdown-item" href="#" onclick="bulkArchive()"><i data-lucide="archive" style="width:14px;height:14px"></i> Archive Selected</a>
                                <a class="dropdown-item" href="#" onclick="bulkExport()"><i data-lucide="download" style="width:14px;height:14px"></i> Export Selected</a>
                            </div>
                        </div>
                        @if(request('search') || request('barangay'))
                            <a href="{{ route('admin.senior.masterlist') }}" style="height:44px;display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;background:white;color:#DC2626;border:1px solid #FECACA;cursor:pointer;text-decoration:none;transition:all 0.2s ease;font-family:inherit;">
                                <i data-lucide="x" style="width:16px;height:16px"></i> Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @if($seniors->count() > 0)
            <!-- Mobile Select All (shown only on mobile since thead is hidden) -->
            <div class="mobile-select-all" style="display:none;align-items:center;gap:8px;padding:8px 12px;margin-bottom:10px;background:var(--surface);border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text-secondary);">
                <input type="checkbox" id="mobileSelectAll" onchange="toggleSelectAllMobile(this.checked)" class="cursor-pointer accent-[#1A237E]" style="width:16px;height:16px">
                <label for="mobileSelectAll" style="cursor:pointer;font-weight:500;">Select all</label>
                <span id="mobileSelectedCount" style="margin-left:auto;font-size:12px;font-weight:600;color:var(--primary);"></span>
            </div>
            <div class="table-scroll" style="flex:1;overflow-y:auto;min-height:0;border-radius:8px;border:1px solid var(--border);">
                <table style="table-layout:fixed;">
                    <thead>
                        <tr>
                            <th class="col-check" style="width:5%;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" style="cursor:pointer;accent-color:var(--primary);"></th>
                            <th style="width:12%;">Control No</th>
                            <th style="width:18%;">Full Name</th>
                            <th style="width:15%;">Barangay</th>
                            <th style="width:12%;">Status</th>
                            <th style="width:20%;">Address</th>
                            <th style="width:8%;">Age</th>
                            <th style="width:15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($seniors as $senior)
                            <tr>
                                <td data-label="#" class="col-check"><input type="checkbox" class="senior-checkbox" data-id="{{ $senior->id }}" onchange="updateBulkActions()" style="cursor:pointer;accent-color:var(--primary);"></td>
                                <td data-label="Control No" style="word-wrap:break-word;font-weight:600;">{{ $senior->control_number ?? '-' }}</td>
                                <td data-label="Full Name" style="word-wrap:break-word;">{{ $senior->full_name ?? '-' }}</td>
                                <td data-label="Barangay" style="word-wrap:break-word;">{{ $senior->barangay ?? '-' }}</td>
                                <td data-label="Status">
                                    <span class="badge {{ $senior->status->value == 'active' ? 'badge-active' : 'badge-pending' }}">
                                        {{ ucfirst($senior->status->value ?? 'pending') }}
                                    </span>
                                </td>
                                <td data-label="Address" style="word-wrap:break-word;">{{ $senior->address ?? '-' }}</td>
                                <td data-label="Age" style="word-wrap:break-word;">{{ $senior->age ?? '-' }}</td>
                                <td data-label="Action">
                                    <div class="actions" style="display:flex;gap:6px;">
                                        <button class="btn btn-sm primary" style="padding:4px 8px;height:32px;min-width:32px;" onclick="viewProfile({{ $senior->id }})">
                                            <i data-lucide="eye" style="width:14px;height:14px"></i>
                                        </button>
                                        <a href="{{ route('admin.senior.id-card', $senior->id) }}" class="btn btn-sm warning" style="padding:4px 8px;height:32px;min-width:32px;" title="ID Card">
                                            <i data-lucide="id-card" style="width:14px;height:14px"></i>
                                        </a>
                                        <button class="btn btn-sm danger archive-senior-btn"
                                            data-id="{{ $senior->id }}"
                                            data-name="{{ $senior->full_name }}"
                                            style="padding:4px 8px;height:32px;min-width:32px;">
                                            <i data-lucide="archive" style="width:14px;height:14px"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="display:flex;justify-content:center;margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--border);">
                {{ $seniors->appends(['barangay' => request('barangay'), 'search' => request('search')])->links('vendor.pagination.custom') }}
            </div>
        @else
            <div style="text-align:center;padding:60px 20px;color:var(--text-secondary);">
                <i data-lucide="users" style="width:56px;height:56px;color:#D1D5DB;margin:0 auto 12px;display:block"></i>
                <p style="margin:8px 0 16px;font-size:14px;color:var(--text-muted);">No senior citizens registered yet.</p>
                <a href="/admin/senior/registration" class="btn primary">
                    <i data-lucide="plus" style="width:16px;height:16px"></i> Register First Senior Citizen
                </a>
            </div>
        @endif
    </div>
</div>
</div>

<!-- ======================== MODAL ======================== -->
<div id="seniorModal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:none;align-items:center;justify-content:center;padding:16px;z-index:9999;backdrop-filter:blur(4px);">
    <div style="background:var(--background);border-radius:16px;width:100%;max-width:800px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.12);overflow:hidden;">
        <div style="background:#1A237E;color:white;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;">
            <h5 style="margin:0;font-size:1.1rem;font-weight:600;display:flex;align-items:center;gap:8px;">
                <i data-lucide="user-circle" style="width:20px;height:20px;"></i>
                Senior Citizen Details
            </h5>
            <button onclick="closeModal()" style="background:none;border:none;color:white;cursor:pointer;opacity:0.8;transition:opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                <i data-lucide="x" style="width:24px;height:24px;"></i>
            </button>
        </div>
        <div style="padding:24px;overflow-y:auto;flex:1;">
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:16px;">
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Control Number</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalControlNumber">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Year Applied</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalYearApplied">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Status</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalStatus">—</div>
                </div>
                <div style="margin-bottom:8px;grid-column:1/-1;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Full Name</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalFullName">—</div>
                </div>
                <div style="margin-bottom:8px;grid-column:1/-1;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Address</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalAddress">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Barangay</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalBarangay">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Birth Date</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalBirthDate">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Month</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalMonth">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Age</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalAge">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Sex</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalSex">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Contact Number</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalContactNumber">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">PhilSys Number</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalPhilsysNumber">—</div>
                </div>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">RRN Number</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);" id="modalRrnNumber">—</div>
                </div>
                <div style="margin-bottom:8px;grid-column:1/-1;">
                    <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Remarks</label>
                    <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);white-space:pre-wrap;" id="modalRemarks">—</div>
                </div>
            </div>
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--surface);display:flex;justify-content:flex-end;gap:12px;">
            <button onclick="closeModal()" style="padding:8px 16px;background:var(--background);border:1px solid var(--border);border-radius:6px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='var(--background)'">Close</button>
            <button onclick="window.location.href='/admin/senior/profile/' + currentSeniorId" style="padding:8px 16px;background:#1A237E;border:none;border-radius:6px;font-weight:500;color:white;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background 0.2s;" onmouseover="this.style.background='#3730A3'" onmouseout="this.style.background='#1A237E'">
                <i data-lucide="user" style="width:16px;height:16px;"></i> Full Profile
            </button>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
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

    function updateDateTime() {
        const now = new Date();
        const options = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'numeric', minute:'2-digit', hour12: true };
        const dateTimeStr = now.toLocaleDateString('en-US', options).replace(',', ' at');
        const currentDateTime = document.getElementById('currentDateTime');
        const desktopDateTime = document.getElementById('desktopDateTime');
        if (currentDateTime) currentDateTime.textContent = dateTimeStr;
        if (desktopDateTime) desktopDateTime.textContent = dateTimeStr;
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Custom Dropdown
    function toggleDropdown() {
        const menu = document.getElementById('bulkDropdownMenu');
        menu.classList.toggle('show');
    }
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('bulkActionDropdown');
        const menu = document.getElementById('bulkDropdownMenu');
        if (dropdown && !dropdown.contains(e.target)) {
            menu.classList.remove('show');
        }
    });

    // Custom Modal
    let currentSeniorId = null;

    function openModal() {
        const modal = document.getElementById('seniorModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => modal.style.opacity = '1', 10);
    }
    function closeModal() {
        const modal = document.getElementById('seniorModal');
        modal.style.opacity = '0';
        setTimeout(() => { modal.style.display = 'none'; document.body.style.overflow = ''; }, 200);
    }
    document.getElementById('seniorModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // View senior profile function
    function viewProfile(id) {
        currentSeniorId = id;
        const fields = ['modalControlNumber','modalFullName','modalAddress','modalBarangay','modalBirthDate','modalMonth','modalAge','modalSex','modalContactNumber','modalPhilsysNumber','modalRrnNumber','modalRemarks','modalStatus','modalYearApplied'];
        fields.forEach(f => { const el = document.getElementById(f); if(el) el.textContent = 'Loading...'; });

        openModal();

        fetch(`{{ route('admin.senior.profile.json', 0) }}`.replace('/0', `/${id}`))
            .then(r => r.json())
            .then(d => {
                document.getElementById('modalControlNumber').textContent = d.control_number;
                document.getElementById('modalFullName').textContent = d.full_name;
                document.getElementById('modalAddress').textContent = d.address;
                document.getElementById('modalBarangay').textContent = d.barangay;
                document.getElementById('modalBirthDate').textContent = d.birth_date;
                document.getElementById('modalMonth').textContent = d.month;
                document.getElementById('modalAge').textContent = d.current_age;
                document.getElementById('modalSex').textContent = d.sex;
                document.getElementById('modalContactNumber').textContent = d.contact_number;
                document.getElementById('modalPhilsysNumber').textContent = d.philsys_number;
                document.getElementById('modalRrnNumber').textContent = d.rrn_number;
                document.getElementById('modalRemarks').textContent = d.remarks;
                document.getElementById('modalStatus').textContent = d.status;
                document.getElementById('modalYearApplied').textContent = d.year_applied;
            })
            .catch(err => {
                console.error('Error loading profile:', err);
                document.getElementById('modalFullName').textContent = 'Error loading data';
            });
    }

    // Event delegation for Archive buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('archive-senior-btn') || e.target.closest('.archive-senior-btn')) {
            const button = e.target.classList.contains('archive-senior-btn') ? e.target : e.target.closest('.archive-senior-btn');
            const seniorId = button.dataset.id;
            const seniorName = button.dataset.name;

            Swal.fire({
                title: 'Archive Senior Citizen',
                text: `Are you sure you want to archive ${seniorName}? This can be undone from the archive page.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Archive',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/senior/archive/${seniorId}`;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.getAttribute('content');
                        form.appendChild(csrfInput);
                    }

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });

    // Bulk Actions Functions
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        var mobileAll = document.getElementById('mobileSelectAll');
        if (mobileAll) mobileAll.checked = selectAll.checked;
        updateBulkActions();
    }

    function toggleSelectAllMobile(checked) {
        var selectAll = document.getElementById('selectAll');
        var checkboxes = document.querySelectorAll('.senior-checkbox');
        checkboxes.forEach(cb => cb.checked = checked);
        if (selectAll) selectAll.checked = checked;
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const button = document.getElementById('bulkActionButton');
        const countSpan = document.getElementById('selectedCount');
        const mobileCount = document.getElementById('mobileSelectedCount');
        const mobileAll = document.getElementById('mobileSelectAll');
        const total = document.querySelectorAll('.senior-checkbox').length;

        countSpan.textContent = checkboxes.length;
        if (mobileCount) mobileCount.textContent = checkboxes.length > 0 ? checkboxes.length + ' / ' + total + ' selected' : '';
        if (mobileAll) mobileAll.checked = checkboxes.length === total && total > 0;

        if (checkboxes.length > 0) {
            button.disabled = false;
            button.style.opacity = '1';
            button.style.background = '#3730A3';
            button.style.color = 'white';
            button.style.borderColor = '#312E81';
        } else {
            button.disabled = true;
            button.style.opacity = '0.45';
            button.style.background = '#E0E7FF';
            button.style.color = '#3730A3';
            button.style.borderColor = '#C7D2FE';
        }
    }

    function bulkArchive() {
        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

        if (ids.length === 0) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Archive Selected Records?',
            text: `You are about to archive ${ids.length} record(s). This action can be undone from the archive page.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Archive',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/senior/bulk-archive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Archived!', 'Selected records have been archived.', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire('Error', data.message || 'Failed to archive records.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'An error occurred while archiving records.', 'error');
                });
            }
        });
    }

    function exportPdf(e) {
        e.preventDefault();
        const url = `{{ route('admin.senior.export-pdf') }}?barangay={{ request('barangay') }}&search={{ request('search') }}`;
        Swal.fire({
            title: 'Generating PDF...',
            text: 'Please wait while the file is being prepared.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
        fetch(url)
            .then(r => {
                if (!r.ok) throw new Error('Download failed');
                return r.blob();
            })
            .then(blob => {
                const disposition = '';
                const filename = 'senior_citizens.pdf';
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 100);
                Swal.fire({
                    icon: 'success',
                    title: 'Download Complete',
                    text: 'The PDF file has been saved to your device.',
                    confirmButtonColor: '#1A237E',
                    timer: 3000,
                    timerProgressBar: true
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Download Failed',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#1A237E'
                });
            });
    }

    function bulkExport() {
        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

        if (ids.length === 0) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Export Selected Records?',
            text: `You are about to export ${ids.length} record(s).`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Export',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/admin/senior/export?ids=${ids.join(',')}`;
            }
        });
    }
</script>


<script>
    function toggleMobileMoreNav(){const extra=document.getElementById('mobileNavExtra');const icon=document.getElementById('mobileMoreIcon');if(!extra)return;if(extra.style.display==='none'||extra.style.display===''){extra.style.display='flex';if(icon){icon.setAttribute('data-lucide','chevron-down');lucide.createIcons();}}else{extra.style.display='none';if(icon){icon.setAttribute('data-lucide','chevron-up');lucide.createIcons();}}}
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK',
                background: '#ffffff',
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif
    });
</script>

<!-- Hidden form for secure POST logout -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, log out',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-4 shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
<script>
(function() {
    var overlay = document.getElementById('sidebarOverlay');
    if (overlay) overlay.addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        if (sidebar) sidebar.classList.remove('show');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var sidebar = document.getElementById('sidebar');
            if (sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1200) {
            var sidebar = document.getElementById('sidebar');
            var ov = document.getElementById('sidebarOverlay');
            if (sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                if (ov) ov.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });
})();
</script>
</body>
</html>
