<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Archived Senior Citizens</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
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
        html, body { margin: 0; padding: 0; height: 100%; overflow-x: hidden; overflow-y: auto; background: var(--background); color: var(--text-primary); font-family: var(--font-family); }
        body { font-size: 14px; line-height: 1.5; }

        /* Sidebar */
        .sidebar{width:260px;flex-shrink:0;background:var(--primary);color:#FFFFFF;position:fixed;left:0;top:0;height:100vh;z-index:1000;display:flex;flex-direction:column;transition:transform .3s ease;}
        .sidebar-brand{height:72px;padding:0 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:.65rem;}
        .sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:var(--accent-yellow);}
        .sidebar-menu{list-style:none;margin:0;padding:1rem 0;flex:1;}
        .sidebar-menu li{margin-bottom:.2rem;}
        .sidebar-menu a{color:rgba(255,255,255,.75);padding:.75rem 1.5rem;display:flex;align-items:center;gap:.75rem;text-decoration:none;font-size:.9rem;border-left:3px solid transparent;transition:all .2s ease;}
        .sidebar-menu a:hover{background:rgba(255,255,255,.1);color:var(--accent-yellow);}
        .sidebar-menu a.active{background:rgba(255,255,255,.1);color:var(--accent-yellow);border-left-color:var(--accent-yellow);}
        .sidebar-menu a i,.sidebar-menu a [data-lucide]{width:20px;height:20px;text-align:center;}

        /* Main Content */
.main-content {
    flex: 1; min-width: 0; margin-left: 260px; padding: 32px;
    max-width: calc(100% - 260px); min-height: 100vh;
    display: flex; flex-direction: column; overflow-y: auto;
    overflow-x: hidden;
}

        .main-content-scroll {
            flex: 1;
            overflow-y: auto;
        }

        /* ---------- Stat Cards ---------- */
        .stat-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:24px;animation:fadeInUp .6s ease-out;}
        @media(max-width:1024px){.stat-cards{grid-template-columns:repeat(3,1fr);}}
        @media(max-width:768px){.stat-cards{grid-template-columns:1fr 1fr;}}
        @media(max-width:480px){.stat-cards{grid-template-columns:1fr;}}

        .stat-card{background:var(--surface);border-radius:16px;padding:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;position:relative;overflow:hidden;}
        .stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;transition:all .3s ease;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 4px 20px rgba(0,0,0,0.08);}

        .stat-card-content{flex:1;}
        .stat-card-label{font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--text-secondary);margin-bottom:6px;}
        .stat-card-value{font-size:32px;font-weight:700;color:var(--text-primary);line-height:1;}
        .stat-card-icon{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .stat-card-icon svg{width:24px;height:24px;}

        .stat-card-purple::before{background:var(--purple);}
        .stat-card-purple .stat-card-icon{background:var(--purple-bg);color:var(--purple);}

        /* ---------- Table Card ---------- */
        .table-card {
            background: var(--surface); border-radius: 16px;
            border: 1px solid var(--border); box-shadow: var(--shadow);
            padding: 2rem; display: flex; flex-direction: column;
            overflow: hidden; flex: 1; min-height: 0; margin-bottom: 1.5rem;
        }
        .table-card-title {
            font-size: 1.25rem; font-weight: 700; color: var(--text-primary);
            margin-top: 0; margin-bottom: 1.5rem; flex-shrink: 0;
        }

        /* ---------- Filter Section ---------- */
        .filter-section { margin-bottom: 1.5rem; flex-shrink: 0; background: var(--surface); border-radius: 16px; border: 1px solid var(--border); padding: 20px; box-shadow: var(--shadow); }
        .filter-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .filter-left { display: flex; gap: 12px; flex: 1; min-width: 0; flex-wrap: wrap; }
        .filter-right { display: flex; gap: 12px; flex-shrink: 0; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group.search-group { max-width: 280px; width: 100%; flex-shrink: 0; }
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
        .btn.warning { background: #F59E0B; color: #FFFFFF; border-color: #F59E0B; }
        .btn.warning:hover { background: #D97706; border-color: #D97706; }
        .btn.ghost { background: transparent; box-shadow: none; border-color: transparent; color: var(--text-secondary); }
        .btn.ghost:hover { background: var(--background); color: var(--text-primary); }
        .btn:disabled { opacity: 0.45; cursor: not-allowed; pointer-events: none; }

        /* Custom Table */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: var(--font-family);
        }
        .custom-table thead th {
            background-color: var(--surface);
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            padding: 12px 16px;
            border-bottom: 2px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1;
            text-align: left;
        }
        .custom-table tbody td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: middle;
            font-size: 0.875rem;
            color: var(--text-primary);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .custom-table tbody tr:hover {
            background-color: #F9FAFB;
        }
        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badge */
        .badge-archived {
            background-color: rgba(156, 163, 175, 0.15);
            color: #6B7280;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Empty state */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .empty-state [data-lucide] { width: 56px; height: 56px; color: #D1D5DB; margin-bottom: 1rem; }
        .empty-state h5 { color: #6B7280; font-weight: 600; font-size: 1rem; }
        .empty-state p { color: #9CA3AF; font-size: 0.85rem; margin-top: 0.25rem; }

        /* Custom Pagination */
        .pagination-custom {
            display: flex;
            justify-content: center;
            gap: 0.25rem;
            margin: 0;
            list-style: none;
            padding: 0;
        }
        .pagination-custom li { margin: 0; }
        .pagination-custom li a,
        .pagination-custom li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            color: var(--text-primary);
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            background: var(--surface);
            transition: all 0.2s;
            min-width: 40px;
            text-align: center;
            font-size: 0.875rem;
            font-family: var(--font-family);
        }
        .pagination-custom li a:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .pagination-custom li.active span,
        .pagination-custom li.active a {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .pagination-custom li.disabled span,
        .pagination-custom li.disabled a {
            color: var(--text-muted);
            background-color: var(--background);
            border-color: var(--border);
            cursor: not-allowed;
        }

        /* Custom Select */
        .custom-select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px;
            padding-right: 2.5rem;
        }

        /* Custom Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-panel {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 440px;
            overflow: hidden;
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.2s ease;
        }
        .modal-overlay.active .modal-panel {
            transform: scale(1);
            opacity: 1;
        }
        .modal-panel-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-panel-body { padding: 1.5rem; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.5s ease forwards; }

        /* Flash messages */
        .flash-message {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            font-family: var(--font-family);
            margin-bottom: 1rem;
            animation: fadeIn 0.3s ease;
            position: relative;
        }
        .flash-message[data-lucide] { width: 20px; height: 20px; flex-shrink: 0; }
        .flash-success {
            background: var(--success-bg);
            color: #166534;
            border: 1px solid #BBF7D0;
        }
        .flash-error {
            background: var(--danger-bg);
            color: #991B1B;
            border: 1px solid #FECACA;
        }
        .flash-close {
            margin-left: auto;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
            background: none;
            border: none;
            padding: 0;
            line-height: 0;
        }
        .flash-close:hover { opacity: 1; }
        .flash-close svg { width: 18px; height: 18px; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* ── Sidebar Overlay ── */
        .sidebar-overlay.active { display: block !important; }

        /* ── Hamburger Button ── */
        .hamburger-btn {
            display: none;
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

        /* ── Responsive: Tablet (< 1024px) ── */
        @media (max-width: 1023px) {
            .hamburger-btn { display: flex; }
            .sidebar { transform: translateX(-100%) !important; z-index: 1001 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: 0 !important; max-width: 100% !important; padding: 16px !important; padding-top: 64px !important; }
            .stat-cards { grid-template-columns: repeat(2, 1fr) !important; }
            .dashboard-grid { grid-template-columns: 1fr !important; }
        }

        /* ── Responsive: Mobile (< 768px) ── */
        @media (max-width: 767px) {
            .mobile-select-all { display: flex !important; }
            .main, .main-content { padding: 12px !important; padding-top: 64px !important; }
            .stat-cards { grid-template-columns: 1fr !important; }
            .topnav, .top-navbar { padding: 10px 12px !important; }
            .topnav-datetime, .navbar-datetime { display: none !important; }

            /* ── Filter → stack ── */
            .filter-section { margin-bottom: 1rem; }
            .filter-row { flex-direction: column !important; gap: 10px !important; }
            .filter-left { flex-direction: column !important; gap: 10px !important; width: 100% !important; }
            .filter-group.search-group, .filter-group.select-group { min-width: 0 !important; width: 100% !important; }
            .filter-left .input-group { width: 100% !important; }
            .filter-right { width: 100% !important; flex-wrap: wrap !important; gap: 8px !important; display: flex !important; }
            .filter-right > * { flex: 1 1 calc(50% - 4px) !important; min-width: 0 !important; }
            .filter-right > a, .filter-right > button, .filter-right .btn, .filter-right > div { width: 100% !important; justify-content: center !important; text-align: center !important; }
            .filter-right > a { display: inline-flex !important; align-items: center !important; justify-content: center !important; }

            /* ── Table Card ── */
            .table-card { padding: 1rem !important; border-radius: 12px !important; }
            .table-card-title { font-size: 1rem !important; margin-bottom: 1rem !important; }

            /* ── Table → Card layout ── */
            .archive-table-wrap { border: none !important; overflow: visible !important; border-radius: 0 !important; }
            .archive-table-wrap table { table-layout: auto !important; width: 100%; }
            .archive-table-wrap thead { display: none !important; }
            .archive-table-wrap tbody tr {
                display: block;
                background: var(--surface);
                border: 1px solid #D1D5DB;
                border-radius: 10px;
                margin-bottom: 10px;
                padding: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            }
            .archive-table-wrap tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border: none;
                font-size: 0.82rem;
                gap: 8px;
            }
            .archive-table-wrap tbody td:not(:last-child) {
                border-bottom: 1px solid var(--border);
            }
            .archive-table-wrap tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--text-secondary);
                font-size: 0.72rem;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                flex-shrink: 0;
                min-width: 70px;
            }
            .archive-table-wrap tbody td.col-check {
                justify-content: flex-end;
                padding: 8px 0 4px;
                border-bottom: none;
            }
            .archive-table-wrap tbody td.col-check::before { display: none; }
            .archive-table-wrap tbody td[data-label="#"] {
                display: none !important;
            }
            .archive-table-wrap tbody td[data-label="Action"] {
                justify-content: flex-end;
                padding-top: 8px;
                border-bottom: none;
            }
            .archive-table-wrap tbody td[data-label="Action"]::before { display: none; }
            .archive-table-wrap tbody td .badge-archived { font-size: 0.7rem; }

            /* ── Pagination ── */
            .pagination-custom { flex-wrap: wrap; justify-content: center; }
        }

        /* ── Responsive: Small Mobile (< 480px) ── */
        @media (max-width: 479px) {
            .stat-card-icon { width: 40px !important; height: 40px !important; }
            .stat-card-value { font-size: 24px !important; }
            .stat-cards { gap: 12px !important; }
            .archive-table-wrap tbody td { font-size: 0.78rem !important; }
            .archive-table-wrap tbody td::before { min-width: 60px !important; font-size: 0.68rem !important; }
        }
    </style>
</head>
<body>
    <!-- ======================== SIDEBAR ======================== -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i data-lucide="users" style="width:24px;height:24px"></i>
            <span>Senior Citizen</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/senior"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
            <li><a href="/admin/senior/registration"><i data-lucide="user-plus" style="width:20px;height:20px"></i> Registration</a></li>
            <li><a href="/admin/senior/masterlist"><i data-lucide="list" style="width:20px;height:20px"></i> Masterlist</a></li>
            <li><a href="/admin/senior/birthdays"><i data-lucide="cake" style="width:20px;height:20px"></i> Birthday Beneficiaries</a></li>
            <li><a href="/admin/senior/birthday-payouts"><i data-lucide="banknote" style="width:20px;height:20px"></i> Birthday Payouts</a></li>
            <li><a href="/admin/senior/birthday-payouts/history"><i data-lucide="history" style="width:20px;height:20px"></i> Payout History</a></li>
            <li><a href="/admin/senior/statistics"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i> Statistics</a></li>
            <li><a href="/admin/senior/reports"><i data-lucide="file-text" style="width:20px;height:20px"></i> Reports</a></li>
            <li><a href="/admin/senior/archive" class="active"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
        </ul>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>

    <!-- Hamburger Button (fixed position) -->
    <button id="hamburgerBtn" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <i data-lucide="menu" style="width:24px;height:24px"></i>
    </button>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Archived Seniors</h1></div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ session('admin_user_name') ?? 'Admin User' }}">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="main-content-scroll">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="flash-message flash-success">
                    <i data-lucide="check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i data-lucide="x"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="flash-message flash-error">
                    <i data-lucide="alert-circle"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i data-lucide="x"></i></button>
                </div>
            @endif

            <!-- Summary Card -->
            <div class="stat-cards">
                <div class="stat-card stat-card-purple">
                    <div class="stat-card-content">
                        <div class="stat-card-label">TOTAL ARCHIVED</div>
                        <div class="stat-card-value">{{ $archivedSeniors->total() }}</div>
                    </div>
                    <div class="stat-card-icon">
                        <i data-lucide="archive"></i>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="filter-section">
                <form method="GET" action="{{ route('admin.senior.archive.list') }}">
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
                            <div class="filter-group" style="justify-content:flex-end;">
                                <label class="filter-label">&nbsp;</label>
                                <button type="button" id="bulkActionButton" onclick="showBulkActionPopup()" disabled
                                        style="height:44px;font-weight:600;display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:13px;background:#E0E7FF;color:#3730A3;border:1px solid #C7D2FE;cursor:pointer;transition:all 0.2s ease;font-family:inherit;opacity:0.45;">
                                    <i data-lucide="list-checks" style="width:14px;height:14px"></i> Bulk Actions <span id="selectedCount" style="background:#3730A3;color:white;padding:2px 8px;border-radius:10px;font-size:11px;margin-left:4px;">0</span>
                                </button>
                            </div>
                        </div>
                        <div class="filter-right">
                            @if(request('search') || request('barangay'))
                                <a href="{{ route('admin.senior.archive.list') }}" class="btn ghost" style="height:44px;">
                                    <i data-lucide="x" style="width:14px;height:14px"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Archive Table Card -->
            <div class="table-card">
                <h2 class="table-card-title">Archived Records</h2>
                <!-- Mobile Select All (shown only on mobile since thead is hidden) -->
                <div class="mobile-select-all" style="display:none;align-items:center;gap:8px;padding:8px 12px;margin-bottom:10px;background:var(--surface);border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text-secondary);">
                    <input type="checkbox" id="mobileSelectAll" onchange="toggleSelectAllMobile(this.checked)" class="cursor-pointer accent-[#1A237E]" style="width:16px;height:16px">
                    <label for="mobileSelectAll" style="cursor:pointer;font-weight:500;">Select all</label>
                    <span id="mobileSelectedCount" style="margin-left:auto;font-size:12px;font-weight:600;color:var(--primary);"></span>
                </div>
                <div style="flex:1;overflow-y:auto;overflow-x:auto;min-height:0;border-radius:8px;" class="archive-table-wrap">
                    <table class="custom-table" id="archiveTable" style="table-layout:fixed;">
                        <thead>
                            <tr>
                                <th class="col-check" style="width:3%;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="cursor-pointer accent-[#1A237E]" style="width:16px;height:16px"></th>
                                <th style="width:4%;">#</th>
                                <th style="width:11%;">Control No.</th>
                                <th style="width:18%;">Full Name</th>
                                <th style="width:12%;">Barangay</th>
                                <th style="width:8%;">Sex / Age</th>
                                <th style="width:11%;">Birth Date</th>
                                <th style="width:11%;">Archived On</th>
                                <th style="width:9%;">Status</th>
                                <th style="width:7%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archivedSeniors as $index => $senior)
                            <tr>
                                <td data-label="" class="col-check"><input type="checkbox" class="senior-checkbox cursor-pointer accent-[#1A237E]" data-id="{{ $senior->id }}" onchange="updateBulkActions()" style="width:16px;height:16px"></td>
                                <td data-label="#">{{ $archivedSeniors->firstItem() + $index }}</td>
                                <td data-label="Control No.">{{ $senior->control_number ?? '-' }}</td>
                                <td data-label="Full Name">
                                    <div class="font-semibold">{{ $senior->full_name ?? '-' }}</div>
                                    <div class="text-[#9CA3AF] text-[12px]">{{ $senior->address ? \Illuminate\Support\Str::limit($senior->address, 35) : '' }}</div>
                                </td>
                                <td data-label="Barangay">
                                    @if($senior->barangay)
                                        <span class="inline-block bg-[rgba(107,114,128,0.1)] text-[#6B7280] font-medium px-2.5 py-1 rounded-md text-[13px]">{{ $senior->barangay }}</span>
                                    @else
                                        <span class="text-[#9CA3AF]">-</span>
                                    @endif
                                </td>
                                <td data-label="Sex / Age">
                                    @if($senior->sex)
                                        <span class="inline-flex items-center justify-center w-[26px] h-[26px] rounded-full bg-[#6B7280] text-white text-[11px] font-bold mr-1">{{ $senior->sex == 'Male' ? 'M' : 'F' }}</span>
                                    @endif
                                    <strong>{{ $senior->age ?? '-' }}</strong>
                                </td>
                                <td data-label="Birth Date">
                                    @if($senior->birth_date)
                                        {{ \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-[#9CA3AF]">-</span>
                                    @endif
                                </td>
                                <td data-label="Archived On">
                                    <span class="text-[#9CA3AF] text-[13px]">
                                        {{ $senior->updated_at ? \Carbon\Carbon::parse($senior->updated_at)->format('M d, Y') : '-' }}
                                    </span>
                                </td>
                                <td data-label="Status">
                                    <span class="badge-archived">Archived</span>
                                </td>
                                <td data-label="Action">
                                    <form method="POST" action="{{ route('admin.senior.unarchive', $senior->id) }}" id="restore-form-{{ $senior->id }}" style="display: inline;">
                                        @csrf
                                        <button type="button"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[rgba(20,184,166,0.1)] text-[#0f766e] border border-[rgba(20,184,166,0.3)] cursor-pointer hover:bg-[rgba(20,184,166,0.2)] transition-colors"
                                                onclick="confirmRestore({{ $senior->id }}, '{{ addslashes($senior->full_name) }}')"
                                                title="Restore to Active">
                                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i data-lucide="archive"></i>
                                        <h5>No Archived Records</h5>
                                        <p>There are no archived senior citizens at the moment. Archived records from the masterlist will appear here.</p>
                                        <a href="/admin/senior/masterlist" class="inline-flex items-center gap-1.5 bg-[#1A237E] hover:bg-[#121858] text-white rounded-lg font-semibold text-[13px] px-4 py-2 mt-2 transition-colors no-underline" style="font-family:var(--font-family)">
                                            <i data-lucide="list" class="w-4 h-4"></i> Go to Masterlist
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($archivedSeniors->hasPages())
                <div class="flex justify-center py-4 border-t border-[#F3F4F6]">
                    {{ $archivedSeniors->appends(['barangay' => request('barangay'), 'search' => request('search')])->links('vendor.pagination.custom') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ======================== BULK ACTION MODAL ======================== -->
    <div class="modal-overlay" id="bulkActionModal">
        <div class="modal-panel">
            <div class="modal-panel-header" style="background: var(--accent-yellow); color: #121858;">
                <h5 class="font-bold text-base flex items-center gap-2 m-0">
                    <i data-lucide="list-checks" class="w-5 h-5"></i> Bulk Actions
                </h5>
                <button type="button" onclick="closeBulkModal()" class="text-[#121858] hover:opacity-70 transition-opacity cursor-pointer border-none bg-transparent p-1 rounded-md flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="modal-panel-body">
                <div class="flex flex-col gap-3">
                    <button type="button" onclick="bulkRestore()"
                            class="flex items-center justify-center gap-2.5 bg-[#0f766e] hover:bg-[#0d6e61] text-white border-none rounded-lg px-5 py-3 text-base font-medium transition-colors cursor-pointer"
                            style="font-family:var(--font-family)">
                        <i data-lucide="undo-2" class="w-5 h-5"></i> Restore Selected
                    </button>
                    <button type="button" onclick="bulkExport()"
                            class="flex items-center justify-center gap-2.5 bg-[#1A237E] hover:bg-[#121858] text-white border-none rounded-lg px-5 py-3 text-base font-medium transition-colors cursor-pointer"
                            style="font-family:var(--font-family)">
                        <i data-lucide="download" class="w-5 h-5"></i> Export Selected
                    </button>
                </div>
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
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);

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

        function showBulkActionPopup() {
            const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

            if (ids.length === 0) {
                Swal.fire('No Selection', 'Please select at least one record.', 'warning');
                return;
            }

            document.getElementById('bulkActionModal').classList.add('active');
        }

        function closeBulkModal() {
            document.getElementById('bulkActionModal').classList.remove('active');
        }

        // Close modal on overlay click
        document.getElementById('bulkActionModal').addEventListener('click', function(e) {
            if (e.target === this) closeBulkModal();
        });

        function bulkRestore() {
            const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

            if (ids.length === 0) {
                Swal.fire('No Selection', 'Please select at least one record.', 'warning');
                return;
            }

            closeBulkModal();

            Swal.fire({
                title: 'Restore Selected Records?',
                text: `You are about to restore ${ids.length} record(s) back to active status.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#EF4444',
                confirmButtonText: 'Yes, Restore',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/admin/senior/bulk-restore', {
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
                            Swal.fire('Restored!', 'Selected records have been restored.', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire('Error', data.message || 'Failed to restore records.', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An error occurred while restoring records.', 'error');
                    });
                }
            });
        }

        function bulkExport() {
            const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

            if (ids.length === 0) {
                Swal.fire('No Selection', 'Please select at least one record.', 'warning');
                return;
            }

            closeBulkModal();

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

        function confirmRestore(id, name) {
            Swal.fire({
                title: 'Restore Senior?',
                html: `Are you sure you want to restore <strong>${name}</strong> back to active status?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#EF4444',
                confirmButtonText: 'Yes, Restore',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('restore-form-' + id).submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons
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
                customClass: { popup: 'rounded-4 shadow-lg' }
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
            if (window.innerWidth >= 1024) {
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
