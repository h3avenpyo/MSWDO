<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizen Statistics - MSWDO Silang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;
            --primary-hover:#121858;
            --sidebar-bg:#1A237E;
            --accent-yellow:#FBC02D;
            --background:#F5F7FB;
            --surface:#FFFFFF;
            --border:#E5E7EB;
            --text-primary:#111827;
            --text-secondary:#6B7280;
            --text-muted:#9CA3AF;
            --success:#16A34A;
            --success-bg:#ECFDF5;
            --danger:#DC2626;
            --danger-bg:#FEF2F2;
            --info:#3B82F6;
            --info-bg:#EEF2FF;
            --purple:#7C3AED;
            --purple-bg:#F3E8FF;
            --icon-blue:#3B82F6;
            --icon-green:#16A34A;
            --icon-purple:#7C3AED;
            --sidebar-width:260px;
            --content-padding:32px;
            --shadow:0 10px 30px rgba(15,23,42,.08);
            --shadow-hover:0 20px 40px rgba(15,23,42,.12);
            --font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);height:100%;overflow-x:hidden;overflow-y:auto;}
        body{font-size:14px;line-height:1.5;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        .app{display:flex;min-height:100vh;}

        /* Sidebar */
        .sidebar{width:var(--sidebar-width);flex-shrink:0;background:var(--primary);color:#FFF;position:fixed;left:0;top:0;height:100vh;z-index:1000;display:flex;flex-direction:column;transition:transform .3s ease;}
        .sidebar-brand{height:72px;padding:0 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:.65rem;}
        .sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:var(--accent-yellow);}
        .sidebar-menu{list-style:none;margin:0;padding:1rem 0;flex:1;}
        .sidebar-menu li{margin-bottom:.2rem;}
        .sidebar-menu a{color:rgba(255,255,255,.75);padding:.75rem 1.5rem;display:flex;align-items:center;gap:.75rem;text-decoration:none;font-size:.9rem;border-left:3px solid transparent;transition:all .2s ease;}
        .sidebar-menu a:hover{background:rgba(255,255,255,.1);color:var(--accent-yellow);}
        .sidebar-menu a.active{background:rgba(255,255,255,.1);color:var(--accent-yellow);border-left-color:var(--accent-yellow);}
        .sidebar-menu a i,.sidebar-menu a [data-lucide]{width:20px;height:20px;text-align:center;}

        /* Main */
        .main{flex:1;min-width:0;margin-left:var(--sidebar-width);padding:var(--content-padding);max-width:calc(100% - var(--sidebar-width));height:100vh;overflow:hidden;display:flex;flex-direction:column;}
        .main-scroll{flex:1;overflow-y:auto;min-height:0;scrollbar-width:none;-ms-overflow-style:none;border-radius:16px;}
        .main-scroll::-webkit-scrollbar{display:none;}

        /* Analytics Card */
        .analytics-card{background:var(--surface);border-radius:16px;padding:24px;box-shadow:var(--shadow);border:1px solid var(--border);height:100%;animation:fadeInUp .6s ease-out .1s backwards;}
        .analytics-card h3{font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:20px;}

        /* Stat Cards */
        .stat-cards{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;animation:fadeInUp .6s ease-out;flex-shrink:0;}
        @media(min-width:1200px){.stat-cards{grid-template-columns:repeat(3,1fr);}}

        .stat-card{background:var(--surface);border-radius:16px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;position:relative;overflow:hidden;}
        .stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;transition:all .3s ease;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);}
        .stat-card-blue::before{background:var(--icon-blue);}
        .stat-card-green::before{background:var(--icon-green);}
        .stat-card-purple::before{background:var(--icon-purple);}
        .stat-card-red::before{background:var(--danger);}
        .stat-card-orange::before{background:#F59E0B;}

        .stat-card-content{flex:1;}
        .stat-card-label{font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--text-primary);margin-bottom:6px;}
        .stat-card-value{font-size:24px;font-weight:700;color:var(--text-primary);line-height:1;}
        .stat-card-icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .stat-card-icon svg{width:22px;height:22px;}
        .stat-card-blue .stat-card-icon{background:var(--info-bg);color:var(--icon-blue);}
        .stat-card-green .stat-card-icon{background:var(--success-bg);color:var(--icon-green);}
        .stat-card-purple .stat-card-icon{background:var(--purple-bg);color:var(--icon-purple);}
        .stat-card-red .stat-card-icon{background:var(--danger-bg);color:var(--danger);}
        .stat-card-orange .stat-card-icon{background:#FFF7ED;color:#F59E0B;}

        /* Filter Section */
        .filter-section{background:var(--surface);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);padding:16px 20px;margin-bottom:20px;flex-shrink:0;}
        .filter-section h3{font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:10px;display:flex;align-items:center;gap:8px;}
        .filter-group{display:flex;flex-direction:column;gap:4px;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;}
        .filter-select{width:100%;height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}

        /* Chart containers */
        .chart-container{position:relative;height:380px;}

        /* Charts grid */
        .charts-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;width:100%;box-sizing:border-box;}
        .charts-outer{padding-bottom:8px;width:100%;box-sizing:border-box;}

        /* Buttons */
        .btn{border:1px solid var(--border);background:var(--surface);color:var(--text-primary);padding:10px 20px;border-radius:10px;font-size:14px;font-weight:500;display:inline-flex;align-items:center;gap:8px;box-shadow:var(--shadow);transition:all 0.2s ease;height:42px;cursor:pointer;text-decoration:none;font-family:inherit;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);}
        .btn.danger{background:var(--danger);color:#FFFFFF;border-color:var(--danger);}
        .btn.success{background:var(--success);color:#FFFFFF;border-color:var(--success);}
        .btn.ghost{background:transparent;box-shadow:none;border-color:transparent;color:var(--text-secondary);}
        .btn.ghost:hover{background:var(--background);color:var(--text-primary);}

        /* Animations */
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
        .animate-fade-in{animation:fadeIn .5s ease forwards;}
        .delay-1{animation-delay:.1s;}
        .delay-2{animation-delay:.2s;}
        .delay-3{animation-delay:.3s;}
        .mobile-header{display:none !important;position:fixed;top:0;left:0;right:0;z-index:1000;background:#1A237E;color:#fff;padding:0 16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);align-items:center;justify-content:space-between;height:80px;}
        .mobile-header-brand{display:flex;align-items:center;gap:16px;flex:1;min-width:0;}
        .mobile-logo{width:56px;height:56px;border-radius:50%;background:#FBC02D;padding:4px;flex-shrink:0;}
        .mobile-logo-img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
        .mobile-brand-text{flex:1;min-width:0;}
        .mobile-brand-title{font-size:18px;font-weight:700;color:#ffffff;margin:0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mobile-brand-subtitle{font-size:12px;color:rgba(255,255,255,0.8);margin:2px 0 0 0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mobile-menu-btn{display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:#ffffff;cursor:pointer;padding:8px;flex-shrink:0;margin-right:24px;}
        .mobile-menu-icon{width:32px;height:32px;}

        .app{flex-direction:column !important;min-height:100vh !important;}
        .main,.main-content{margin-left:0 !important;max-width:100% !important;height:auto !important;overflow:visible !important;padding:12px 14px !important;padding-top:90px !important;}
        .main-scroll{overflow:visible !important;flex:none !important;height:auto !important;}
        .charts-grid{grid-template-columns:1fr !important;gap:16px !important;width:100% !important;box-sizing:border-box !important;}
        .charts-outer{padding:0 0 8px 0 !important;width:100% !important;box-sizing:border-box !important;}
        .analytics-card{width:100% !important;box-sizing:border-box !important;margin-left:0 !important;margin-right:0 !important;margin-bottom:0 !important;padding:12px !important;border-radius:16px !important;min-height:auto !important;height:auto !important;}
        .chart-card,.stat-card,.card,.table-card{margin-bottom:16px !important;}
        .dashboard-grid,.analytics-grid,.stats-grid{padding-bottom:40px !important;}
        #analyticsFilterGrid{grid-template-columns:1fr 1fr !important;}
        header{display:none !important;}
        .hamburger-btn{display:none !important;}
        .mobile-header{display:flex !important;}
        .sidebar{transform:translateX(-100%) !important;z-index:1001 !important;}
        .sidebar.show{transform:translateX(0) !important;}
        .stat-card{width:100% !important;height:auto !important;padding:16px !important;border-radius:16px !important;flex-direction:column !important;align-items:flex-start !important;gap:8px !important;position:relative !important;}
        .stat-card::before{display:none !important;}
        .stat-card-content{width:100%;}
        .stat-card-value{font-size:28px !important;font-weight:700 !important;}
        .stat-card-icon{width:40px !important;height:40px !important;border-radius:50% !important;position:absolute !important;top:14px !important;right:14px !important;}
        .stat-card-icon svg{width:20px !important;height:20px !important;}
        .stat-card-label{font-size:11px !important;font-weight:600 !important;text-transform:uppercase !important;letter-spacing:0.3px !important;color:var(--text-secondary) !important;margin-bottom:4px !important;}
        .topnav,.top-navbar{padding:10px 12px !important;}
        .topnav-datetime,.navbar-datetime{display:none !important;}
        .filter-bar,.filter-group{flex-wrap:wrap;}
        .filter-bar>div,.filter-group>div{min-width:0 !important;}
        .filter-section{padding:12px !important;}
        .stat-cards{gap:12px !important;}

        @media (max-width: 479px){
            .main,.main-content{padding:10px !important;padding-top:88px !important;}
            .charts-grid{grid-template-columns:1fr !important;width:100% !important;box-sizing:border-box !important;}
            .analytics-card{width:100% !important;box-sizing:border-box !important;padding:10px !important;border-radius:14px !important;}
            .chart-container{height:260px !important;}
            #analyticsFilterGrid{grid-template-columns:1fr 1fr !important;}
            .mobile-header{height:72px !important;}
            .mobile-logo{width:48px !important;height:48px !important;}
            .mobile-brand-title{font-size:16px !important;}
            .mobile-brand-subtitle{font-size:11px !important;}
            .mobile-menu-icon{width:28px !important;height:28px !important;}
            .stat-card{padding:14px !important;}
            .stat-card-value{font-size:24px !important;}
            .stat-card-icon{width:36px !important;height:36px !important;top:12px !important;right:12px !important;}
            .stat-card-icon svg{width:16px !important;height:16px !important;}
            .stat-cards{gap:10px !important;}
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

        @media (min-width: 768px) {
            .hamburger-btn { display: flex; }
            .mobile-header { display: none !important; }
            header { display: flex !important; }
            .main, .main-content { padding: 16px !important; padding-top: 64px !important; }
            #analyticsFilterGrid { grid-template-columns: repeat(3, 1fr) !important; }
            .chart-container { height: 340px !important; }
            .dashboard-grid { grid-template-columns: 1fr !important; }
            .filter-section { padding: 16px 20px !important; }
        }

        @media (min-width: 1200px) {
            .app { flex-direction: row !important; }
            .sidebar { transform: translateX(0) !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: var(--sidebar-width) !important; max-width: calc(100% - var(--sidebar-width)) !important; padding: var(--content-padding) !important; padding-top: var(--content-padding) !important; height: 100vh !important; overflow: hidden !important; }
            .main-scroll { overflow-y: auto !important; flex: 1 !important; height: 100% !important; }
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
            .charts-grid { grid-template-columns: 1fr 1fr !important; gap: 20px !important; width: auto !important; }
            .charts-outer { padding: 0 0 8px 0 !important; width: 100% !important; }
            .analytics-card { width: auto !important; padding: 24px !important; border-radius: 16px !important; margin: 0 !important; height: auto !important; }
            .chart-container { height: 380px !important; }
            #analyticsFilterGrid { grid-template-columns: repeat(6, 1fr) !important; }
            .dashboard-grid { grid-template-columns: repeat(2, 1fr) !important; }
            .filter-section { padding: 16px 20px !important; }
            .stat-card { width: auto !important; height: auto !important; padding: 14px 16px !important; border-radius: 16px !important; flex-direction: row !important; align-items: center !important; gap: 0 !important; position: relative !important; }
            .stat-card::before { display: block !important; }
            .stat-card-content { width: auto; }
            .stat-card-value { font-size: 24px !important; }
            .stat-card-icon { width: 42px !important; height: 42px !important; border-radius: 50% !important; position: relative !important; top: auto !important; right: auto !important; }
            .stat-card-icon svg { width: 22px !important; height: 22px !important; }
            .stat-card-label { color: var(--text-primary) !important; }
            .topnav, .top-navbar { padding: 0 !important; }
            .topnav-datetime, .navbar-datetime { display: block !important; }
            .filter-bar, .filter-group { flex-wrap: nowrap; }
            .filter-bar > div, .filter-group > div { min-width: auto !important; }
            .chart-card, .stat-card, .card, .table-card { margin-bottom: 0 !important; }
            .dashboard-grid, .analytics-grid, .stats-grid { padding-bottom: 0 !important; }
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
            <li><a href="/admin/senior/masterlist"><i data-lucide="list" style="width:20px;height:20px"></i> Masterlist</a></li>
            <li><a href="/admin/senior/birthdays"><i data-lucide="cake" style="width:20px;height:20px"></i> Birthday Beneficiaries</a></li>
            <li><a href="/admin/senior/payouts-history"><i data-lucide="history" style="width:20px;height:20px"></i> Payout History</a></li>
            <li><a href="/admin/senior/statistics" class="active"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i> Statistics</a></li>
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
                <p class="mobile-brand-subtitle">Senior Citizen Statistics</p>
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

        <!-- Page Header -->
        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Statistics</h1>
            </div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">{{ $initials }}</div>
            </div>
        </header>

        <div class="main-scroll">
        <!-- Filter Card -->
        <div class="filter-section">
            <h3><i data-lucide="filter" style="width:16px;height:16px;color:var(--primary)"></i> Statistics Filters</h3>
            <form id="filterForm" method="GET" action="{{ route('admin.senior.analytics') }}" autocomplete="off">
                <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;align-items:end" id="analyticsFilterGrid">
                    <div class="filter-group">
                        <label class="filter-label">Year</label>
                        <select class="filter-select" name="year">
                            <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                            <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
                            <option value="2024" {{ $year == 2024 ? 'selected' : '' }}>2024</option>
                            <option value="2023" {{ $year == 2023 ? 'selected' : '' }}>2023</option>
                            <option value="2022" {{ $year == 2022 ? 'selected' : '' }}>2022</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Month</label>
                        <select class="filter-select" name="month">
                            <option value="">All</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $i, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Barangay</label>
                        <select class="filter-select" name="barangay" id="barangaySelect">
                            <option value="">All</option>
                            @foreach($allBarangays as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Gender</label>
                        <select class="filter-select" name="gender">
                            <option value="">All</option>
                            <option value="Male" {{ $gender == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $gender == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Age Group</label>
                        <select class="filter-select" name="age_group">
                            <option value="">All</option>
                            <option value="60-69" {{ $ageGroup == '60-69' ? 'selected' : '' }}>60-69</option>
                            <option value="70-79" {{ $ageGroup == '70-79' ? 'selected' : '' }}>70-79</option>
                            <option value="80-89" {{ $ageGroup == '80-89' ? 'selected' : '' }}>80-89</option>
                            <option value="90-99" {{ $ageGroup == '90-99' ? 'selected' : '' }}>90-99</option>
                            <option value="100+" {{ $ageGroup == '100+' ? 'selected' : '' }}>100+</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:8px;align-items:end">
                        <button type="submit" class="btn primary" style="flex:1;justify-content:center;height:44px">
                            <i data-lucide="check" style="width:16px;height:16px"></i> Apply
                        </button>
                        <a href="{{ route('admin.senior.analytics') }}" class="btn ghost" style="flex:1;justify-content:center;height:44px">
                            <i data-lucide="rotate-ccw" style="width:16px;height:16px"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="stat-cards">
            <div class="stat-card stat-card-blue animate-fade-in">
                <div class="stat-card-content">
                    <div class="stat-card-label">TOTAL SENIORS</div>
                    <div class="stat-card-value">{{ $totalSeniors }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="users"></i></div>
            </div>
            <div class="stat-card stat-card-blue animate-fade-in delay-1">
                <div class="stat-card-content">
                    <div class="stat-card-label">MALE</div>
                    <div class="stat-card-value">{{ $maleCount }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="male"></i></div>
            </div>
            <div class="stat-card stat-card-purple animate-fade-in delay-2">
                <div class="stat-card-content">
                    <div class="stat-card-label">FEMALE</div>
                    <div class="stat-card-value">{{ $femaleCount }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="female"></i></div>
            </div>
            <div class="stat-card stat-card-green animate-fade-in delay-3">
                <div class="stat-card-content">
                    <div class="stat-card-label">ACTIVE</div>
                    <div class="stat-card-value">{{ $activeSeniors }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="check-circle"></i></div>
            </div>
            <div class="stat-card stat-card-red animate-fade-in">
                <div class="stat-card-content">
                    <div class="stat-card-label">INACTIVE</div>
                    <div class="stat-card-value">{{ $inactiveSeniors }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="user-x"></i></div>
            </div>
            <div class="stat-card stat-card-orange animate-fade-in">
                <div class="stat-card-content">
                    <div class="stat-card-label">BARANGAYS</div>
                    <div class="stat-card-value">{{ $totalBarangays }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="map-pin"></i></div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-outer">
            <div class="charts-grid">
            <div class="analytics-card animate-fade-in">
                <div class="flex items-center justify-between mb-5">
                    <h3><i data-lucide="pie-chart" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-blue)"></i>Gender Distribution</h3>
                </div>
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
            <div class="analytics-card animate-fade-in delay-1">
                <div class="flex items-center justify-between mb-5">
                    <h3><i data-lucide="activity" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-blue)"></i>Age Group Distribution</h3>
                </div>
                <div class="chart-container">
                    <canvas id="ageChart"></canvas>
                </div>
            </div>
            </div>
        </div>

        </div>  <!-- close main-scroll -->
    </div>  <!-- close main -->
</div>  <!-- close app -->

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>

<script>
    // Immediately reset barangay dropdown to "All" if no parameter in URL
    (function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has('barangay')) {
            const barangaySelect = document.getElementById('barangaySelect');
            if (barangaySelect) {
                barangaySelect.value = '';
            }
        }
    })();

    function confirmLogout(e) {
        e.preventDefault();
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

    // Current date/time
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
        const dateTimeStr = now.toLocaleDateString('en-US', options).replace(',', ' at');
        const currentDateTime = document.getElementById('currentDateTime');
        const desktopDateTime = document.getElementById('desktopDateTime');
        if (currentDateTime) currentDateTime.textContent = dateTimeStr;
        if (desktopDateTime) desktopDateTime.textContent = dateTimeStr;
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Gender Distribution Chart
    const genderLabels = {!! json_encode($genderStats->pluck('sex')) !!};
    const genderValues = {!! json_encode($genderStats->pluck('total')) !!};
    const genderColors = ['#1A237E', '#EC4899'];
    const genderTotal = genderValues.reduce((a, b) => a + b, 0);

    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderValues,
                backgroundColor: genderColors,
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 20 },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 25,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 13, weight: 500 },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = ((value / genderTotal) * 100).toFixed(1);
                                    return {
                                        text: `${label} — ${value} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const percentage = ((context.raw / genderTotal) * 100).toFixed(1);
                            return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '72%'
        }
    });

    // Age Groups Chart
    const ageLabels = {!! json_encode($ageGroups->pluck('age_group')) !!};
    const ageValues = {!! json_encode($ageGroups->pluck('total')) !!};
    const ageColors = ageLabels.map(label => {
        if (label === '60-69') return 'rgba(26, 35, 126, 0.85)';
        if (label === '70-79') return 'rgba(63, 81, 181, 0.85)';
        if (label === '80-89') return 'rgba(92, 107, 192, 0.85)';
        if (label === '90-99') return 'rgba(121, 134, 203, 0.85)';
        return 'rgba(159, 168, 218, 0.85)';
    });

    new Chart(document.getElementById('ageChart'), {
        type: 'bar',
        data: {
            labels: ageLabels,
            datasets: [{
                label: 'Seniors',
                data: ageValues,
                backgroundColor: ageColors,
                borderRadius: 8,
                barPercentage: 0.6,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { top: 20, bottom: 10, left: 10, right: 10 }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' seniors';
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,0.06)' }, border: { display: false } },
                x: { grid: { display: false }, ticks: { font: { size: 12, weight: 500 } }, border: { display: false } }
            }
        }
    });
</script>

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

    function toggleMobileMoreNav(){const extra=document.getElementById('mobileNavExtra');const icon=document.getElementById('mobileMoreIcon');if(!extra)return;if(extra.style.display==='none'||extra.style.display===''){extra.style.display='flex';if(icon){icon.setAttribute('data-lucide','chevron-down');lucide.createIcons();}}else{extra.style.display='none';if(icon){icon.setAttribute('data-lucide','chevron-up');lucide.createIcons();}}}
    lucide.createIcons();
</script>
</body>
</html>
