<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birthday Payout History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    fontFamily: { sans: ['"Public Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Helvetica', 'Arial', 'sans-serif'] }
                }
            }
        }
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
            --shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            --font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; overflow-x: hidden; overflow-y: auto; background: var(--background); color: var(--text-primary); font-family: var(--font-family); }
        body { font-size: 14px; line-height: 1.5; }

        /* ── Sidebar ── */
        .sidebar{width:260px;flex-shrink:0;background:var(--primary);color:#FFFFFF;position:fixed;left:0;top:0;height:100vh;z-index:1000;display:flex;flex-direction:column;transition:transform .3s ease;}
        .sidebar-brand{height:72px;padding:0 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:.65rem;}
        .sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:var(--accent-yellow);}
        .sidebar-menu{list-style:none;margin:0;padding:1rem 0;flex:1;}
        .sidebar-menu li{margin-bottom:.2rem;}
        .sidebar-menu a{color:rgba(255,255,255,.75);padding:.75rem 1.5rem;display:flex;align-items:center;gap:.75rem;text-decoration:none;font-size:.9rem;border-left:3px solid transparent;transition:all .2s ease;}
        .sidebar-menu a:hover{background:rgba(255,255,255,.1);color:var(--accent-yellow);}
        .sidebar-menu a.active{background:rgba(255,255,255,.1);color:var(--accent-yellow);border-left-color:var(--accent-yellow);}
        .sidebar-menu a i,.sidebar-menu a [data-lucide]{width:20px;height:20px;text-align:center;}

/* ── Main Content ── */
        .main-content {
            flex: 1; min-width: 0; margin-left: 260px; padding: 32px;
            max-width: calc(100% - 260px); height: 100vh;
            display: flex; flex-direction: column; overflow: hidden;
            animation: fadeIn .3s ease;
        }
        .main-scroll {
            flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden;
        }

        /* ── Table Card ── */
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

        /* ── Table Scroll ── */
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

        /* ── Badges ── */
        .badge-generated { background: var(--info-bg); color: var(--info); font-weight: 600; }
        .badge-released { background: var(--success-bg); color: var(--success); font-weight: 600; }
        .badge-cancelled { background: var(--danger-bg); color: var(--danger); font-weight: 600; }
        .badge-reset { background: #FEF3C7; color: #D97706; font-weight: 600; }
        .badge-span { padding: .3rem .7rem; border-radius: 999px; font-size: .78rem; display: inline-block; }

        /* ── Form Controls ── */
        .filter-select{width:100%;height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        input[type="date"].filter-select{cursor:text;appearance:none;-webkit-appearance:none;position:relative;background-image:none;padding-right:12px;}

        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;}

        .filter-section{background:var(--surface);border-radius:16px;border:1px solid var(--border);padding:20px;margin-bottom:24px;box-shadow:var(--shadow);flex-shrink:0;}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);height:42px;text-decoration:none;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn svg{width:16px;height:16px;}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);transform:translateY(-1px);}
        .btn.ghost{background:transparent;box-shadow:none;border-color:transparent;color:var(--text-secondary);}
        .btn.ghost:hover{background:var(--background);color:var(--text-primary);border-color:var(--border);}

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
            .main, .main-content { margin-left: 0 !important; max-width: 100% !important; padding: 16px !important; padding-top: 64px !important; height: 100vh !important; }
            .main-scroll { flex: 1 !important; min-height: 0 !important; overflow: hidden !important; }
            .table-card { flex: 1 !important; min-height: 0 !important; overflow: hidden !important; }
            .table-scroll { flex: 1 !important; min-height: 0 !important; overflow-y: auto !important; overflow-x: auto !important; }
        }

        /* ── Responsive: Mobile (< 768px) ── */
        @media (max-width: 767px) {
            .main, .main-content { padding: 12px !important; padding-top: 64px !important; height: auto !important; min-height: 100vh !important; }
            .main-scroll { flex: none !important; min-height: 0 !important; height: auto !important; overflow: visible !important; }
            input[type="date"].filter-select{background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3e%3cline x1='16' y1='2' x2='16' y2='6'/%3e%3cline x1='8' y1='2' x2='8' y2='6'/%3e%3cline x1='3' y1='10' x2='21' y2='10'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 10px center;background-size:18px;padding-right:36px;}

            /* ── Filter → 2 columns ── */
            #filterGrid { grid-template-columns: 1fr 1fr !important; }
            #filterGrid > div:last-child { grid-column: 1 / -1 !important; }
            #filterGrid > div:last-child .btn { flex: 1 !important; }

            /* ── Table Card ── */
            .table-card { padding: 1rem !important; border-radius: 12px !important; flex: none !important; min-height: 0 !important; overflow: visible !important; height: auto !important; }
            .table-card-title { font-size: 1rem !important; margin-bottom: 1rem !important; }

            /* ── Table → Card layout ── */
            .table-scroll { border: none !important; border-radius: 0 !important; flex: none !important; min-height: 0 !important; overflow: visible !important; height: auto !important; }
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
                align-items: flex-start;
                padding: 8px 0;
                border: none;
                font-size: 0.82rem;
                gap: 8px;
                word-break: break-word;
                overflow-wrap: break-word;
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

            /* ── Empty state ── */
            .table-scroll tbody td.empty-state-cell { display: flex !important; justify-content: center !important; text-align: center !important; padding: 40px 12px !important; }
            .table-scroll tbody td.empty-state-cell::before { display: none !important; }
        }

        /* ── Responsive: Small Mobile (< 480px) ── */
        @media (max-width: 479px) {
            .table-scroll tbody td { font-size: 0.78rem !important; }
            .table-scroll tbody td::before { min-width: 60px !important; font-size: 0.68rem !important; }
            .main, .main-content { padding: 10px !important; padding-top: 88px !important; height: 100vh !important; }
            #filterGrid { grid-template-columns: 1fr !important; }
            #filterGrid > div:last-child { grid-column: 1 / -1 !important; }
            #filterGrid > div:last-child .btn { flex: 1 !important; }
            .filter-section { padding: 14px !important; }
        }
        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
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
            header,.top-navbar{display:none !important;}
            .hamburger-btn{display:none !important;}
            .mobile-header{display:flex !important;}
        }
        @media(max-width:479px){
            .main,.main-content{padding:10px !important;padding-top:88px !important;height:auto !important;}
            .mobile-header{height:72px !important;}
            .mobile-logo{width:48px !important;height:48px !important;}
            .mobile-brand-title{font-size:16px !important;}
            .mobile-brand-subtitle{font-size:11px !important;}
            .mobile-menu-icon{width:28px !important;height:28px !important;}
        }
    </style>
</head>
<body>
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
            <li><a href="/admin/senior/payouts-history" class="active"><i data-lucide="history" style="width:20px;height:20px"></i> Payout History</a></li>
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
                <p class="mobile-brand-subtitle">Payout History</p>
            </div>
            <div class="mobile-logo">
                @if($logo)
                <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Payout History</h1>
            </div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ session('admin_user_name') ?? 'Admin' }}">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin'), 0, 2)) }}</div>
            </div>
        </header>

        <!-- Page Body -->
        <div class="main-scroll">
            <!-- Table Card -->
            <div class="table-card" style="flex:1;min-height:0;display:flex;flex-direction:column;overflow:hidden">
                <h2 class="table-card-title">Birthday Payout History Log</h2>

                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="GET" action="{{ route('admin.senior.payouts-history') }}">
                        <div id="filterGrid" style="display:grid;grid-template-columns:1fr 1fr auto auto;gap:12px;align-items:end">
                            <div>
                                <label class="filter-label">Barangay</label>
                                <select class="filter-select" name="barangay">
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
                            <div>
                                <label class="filter-label">Date From</label>
                                <input type="date" class="filter-select" name="date_from" value="{{ $dateFrom }}">
                            </div>
                            <div>
                                <label class="filter-label">Date To</label>
                                <input type="date" class="filter-select" name="date_to" value="{{ $dateTo }}">
                            </div>
                            <div style="display:flex;gap:8px">
                                <button type="submit" class="btn primary">
                                    <i data-lucide="filter" style="width:16px;height:16px"></i> Filter
                                </button>
                                @if(request('barangay') || request('date_from') || request('date_to'))
                                    <a href="{{ route('admin.senior.payouts-history') }}" class="btn ghost">
                                        <i data-lucide="x" style="width:16px;height:16px"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <!-- History Table -->
                <div class="table-scroll" style="flex:1;overflow-y:auto;min-height:0;border-radius:8px;border:1px solid var(--border);">
                    <table style="table-layout:fixed;">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Action</th>
                                <th>Senior</th>
                                <th>Amount</th>
                                <th>Details</th>
                                <th>Performed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $record)
                                <tr>
                                    <td data-label="Date & Time">{{ $record->created_at->format('M d, Y g:i A') }}</td>
                                    <td data-label="Action">
                                        <span class="badge-span badge-{{ $record->action }}">
                                            {{ ucfirst(str_replace('_', ' ', $record->action)) }}
                                        </span>
                                    </td>
                                    <td data-label="Senior">
                                        @if($record->senior)
                                            <div>
                                                <strong>{{ $record->senior->full_name }}</strong>
                                                <div class="text-[var(--text-muted)]" style="font-size:0.72rem;margin-top:2px">{{ $record->senior->control_number ?? '-' }}</div>
                                            </div>
                                        @else
                                            <span class="text-[var(--text-muted)] text-xs">System-wide action</span>
                                        @endif
                                    </td>
                                    <td data-label="Amount">PHP {{ number_format($record->payout->amount ?? 0, 2) }}</td>
                                    <td data-label="Details"><div style="min-width:0;text-align:right">{{ $record->details ?? '-' }}</div></td>
                                    <td data-label="Performed By">
                                        @if($record->performedBy)
                                            {{ $record->performedBy->name ?? 'Admin' }}
                                        @else
                                            <span class="text-[var(--text-muted)] text-xs">System</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state-cell text-center py-12">
                                        <div class="flex flex-col items-center text-[var(--text-muted)]">
                                            <i data-lucide="history" style="width:48px;height:48px;opacity:.4" class="mb-3"></i>
                                            <p class="text-sm m-0">No payout history found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($history->hasPages())
                    <div style="display:flex;justify-content:center;padding-top:1rem;border-top:1px solid var(--border);">
                        {{ $history->appends(['barangay' => request('barangay'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Update current date/time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Toggle sidebar
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
    </script>


    <script>
        // Confirm logout
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

        function toggleMobileMoreNav(){const extra=document.getElementById('mobileNavExtra');const icon=document.getElementById('mobileMoreIcon');if(!extra)return;if(extra.style.display==='none'||extra.style.display===''){extra.style.display='flex';if(icon){icon.setAttribute('data-lucide','chevron-down');lucide.createIcons();}}else{extra.style.display='none';if(icon){icon.setAttribute('data-lucide','chevron-up');lucide.createIcons();}}}
        document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
        lucide.createIcons();
    </script>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>