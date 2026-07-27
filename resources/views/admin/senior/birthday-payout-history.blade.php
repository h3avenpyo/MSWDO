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

        /* ── Custom Table ── */
        .custom-table-wrapper {
            background: var(--surface);
            border-radius: 12px;
            box-shadow: var(--shadow);
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        .custom-table-scroll {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .custom-table-scroll::-webkit-scrollbar { display: none; }
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-family: var(--font-family);
        }
        .custom-table thead { position: sticky; top: 0; z-index: 10; }
        .custom-table thead th {
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: .85rem 1rem;
            text-align: left;
            white-space: nowrap;
            border: none;
        }
        .custom-table tbody td {
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--border);
            font-size: .875rem;
            color: var(--text-primary);
            vertical-align: middle;
        }
        .custom-table tbody tr:hover { background: #F9FAFB; }
        .custom-table tbody tr:last-child td { border-bottom: none; }

        /* ── Badges ── */
        .badge-generated { background: var(--info-bg); color: var(--info); font-weight: 600; }
        .badge-released { background: var(--success-bg); color: var(--success); font-weight: 600; }
        .badge-cancelled { background: var(--danger-bg); color: var(--danger); font-weight: 600; }
        .badge-reset { background: #FEF3C7; color: #D97706; font-weight: 600; }
        .badge-span { padding: .3rem .7rem; border-radius: 999px; font-size: .78rem; display: inline-block; }

        /* ── Form Controls ── */
        .ctrl-select, .ctrl-input {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .55rem .85rem;
            font-size: .875rem;
            color: var(--text-primary);
            font-family: var(--font-family);
            width: 100%;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .ctrl-select:focus, .ctrl-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,35,126,.08);
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
            .custom-table-wrapper { overflow: visible !important; }
            .custom-table-scroll { overflow: visible !important; min-height: 0; }
        }

        /* ── Responsive: Mobile (< 768px) ── */
        @media (max-width: 767px) {
            .main, .main-content { padding: 12px !important; padding-top: 64px !important; }
            .main-content > .flex-1 { overflow: visible !important; overflow-y: auto !important; border-radius: 16px; scrollbar-width: none; -ms-overflow-style: none; }
            .main-content > .flex-1::-webkit-scrollbar { display: none; }
            .main-content .rounded-xl { border-radius: 16px !important; }

            /* ── Filter card side spacing ── */
            .main-content > .flex-1 > .bg-white,
            .main-content > .flex-1 > div > .bg-white { width: 100% !important; box-sizing: border-box !important; margin-left: 0 !important; margin-right: 0 !important; }

            /* ── Filter → stack ── */
            .filter-grid { grid-template-columns: 1fr !important; }
            .filter-grid .flex { width: 100% !important; }
            .filter-grid .flex > * { flex: 1 !important; }

            /* ── Table wrapper full width ── */
            .custom-table-wrapper { width: 100% !important; box-sizing: border-box !important; margin-left: 0 !important; margin-right: 0 !important; }

            /* ── Table → Card layout ── */
            .custom-table-scroll { border: none !important; overflow: visible !important; }
            .custom-table { display: block !important; width: 100%; }
            .custom-table tbody { display: block; }
            .custom-table thead { display: none !important; }
            .custom-table tbody tr {
                display: block;
                background: var(--surface);
                border: 1px solid #D1D5DB;
                border-radius: 10px;
                margin-bottom: 10px;
                padding: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            }
            .custom-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border: none;
                font-size: 0.82rem;
                gap: 8px;
            }
            .custom-table tbody td:not(:last-child) {
                border-bottom: 1px solid var(--border);
            }
            .custom-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--text-secondary);
                font-size: 0.72rem;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                flex-shrink: 0;
                min-width: 80px;
            }

            /* ── Empty state ── */
            .custom-table tbody td.empty-state-cell { display: flex !important; justify-content: center !important; text-align: center !important; padding: 40px 12px !important; }
            .custom-table tbody td.empty-state-cell::before { display: none !important; }

            /* ── Pagination ── */
            .custom-table-wrapper > div:last-child { padding: 12px !important; }
        }

        /* ── Responsive: Small Mobile (< 480px) ── */
        @media (max-width: 479px) {
            .custom-table tbody td { font-size: 0.78rem !important; }
            .custom-table tbody td::before { min-width: 65px !important; font-size: 0.68rem !important; }
            .badge-span { font-size: 0.7rem !important; padding: 0.2rem 0.5rem !important; }
            .main, .main-content { padding: 10px !important; padding-top: 64px !important; padding-bottom: 140px !important; }
            .main-content > .flex-1 > .bg-white,
            .main-content > .flex-1 > div > .bg-white { width: 100% !important; box-sizing: border-box !important; }
            .custom-table-wrapper { width: 100% !important; box-sizing: border-box !important; }
        }
        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
        .mobile-header{display:none !important;position:fixed;top:0;left:0;right:0;z-index:1000;background:linear-gradient(135deg,#1A237E 0%,#283593 100%);color:#fff;padding:10px 16px;box-shadow:0 2px 12px rgba(26,35,126,0.2);align-items:center;justify-content:space-between;height:56px;}
        .mobile-header-title{font-size:16px;font-weight:700;color:#fff;letter-spacing:-0.2px;}
        .mobile-header-sub{font-size:11px;color:rgba(255,255,255,0.7);font-weight:500;}
        .mobile-avatar-hdr{width:34px;height:34px;border-radius:50%;background:#FBC02D;color:#1A237E;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.15);}
        .mobile-bottom-nav{display:none !important;position:fixed;bottom:0;left:0;right:0;z-index:1000;background:#fff;border-top:1px solid #E5E7EB;padding:8px 4px;box-shadow:0 -2px 10px rgba(15,23,42,0.05);flex-direction:column;gap:6px;}
        .mobile-bottom-nav-row{display:flex;align-items:center;justify-content:space-around;width:100%;}
        .mobile-bottom-nav-item{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;text-decoration:none;color:#6B7280;font-size:10px;font-weight:500;padding:6px 0;transition:all 0.2s;background:none;border:none;cursor:pointer;font-family:inherit;}
        .mobile-bottom-nav-item.active{color:#1A237E;font-weight:700;}
        .mobile-bottom-nav-item [data-lucide]{width:20px;height:20px;}
        .mobile-bottom-nav-item:hover{color:#1A237E;}
        .mobile-nav-extra{padding-top:4px;margin-top:2px;}
        @media(max-width:767px){
            .app{flex-direction:column !important;min-height:100vh !important;}
            .main,.main-content{margin-left:0 !important;max-width:100% !important;height:auto !important;overflow:visible !important;padding:12px 14px !important;padding-top:66px !important;padding-bottom:140px !important;}
            .custom-table-wrapper{overflow:visible !important;flex:none !important;height:auto !important;margin-bottom:40px !important;padding-bottom:30px !important;}
            header,.top-navbar{display:none !important;}
            .hamburger-btn{display:none !important;}
            .mobile-header{display:flex !important;}
            .mobile-bottom-nav{display:flex !important;flex-direction:column !important;}
        }
        @media(max-width:479px){
            .main,.main-content{padding:10px !important;padding-top:64px !important;padding-bottom:140px !important;}
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
    @php $mhUser=session('admin_user_name')??'Admin';$mhW=explode(' ',$mhUser);$mhI=count($mhW)>=2?strtoupper(substr($mhW[0],0,1).substr($mhW[1],0,1)):strtoupper(substr($mhUser,0,2)); @endphp
    <div class="mobile-header"><div><div class="mobile-header-sub">Senior Citizen</div><div class="mobile-header-title">Payout History</div></div><div class="mobile-avatar-hdr">{{ $mhI }}</div></div>

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
        <div class="flex-1 overflow-hidden flex flex-col">
            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-[0_1px_3px_rgba(0,0,0,.1)] p-5 mb-6 border border-[#E5E7EB] flex-shrink-0">
                <form method="GET" action="{{ route('admin.senior.payouts-history') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end filter-grid">
                        <div>
                            <label class="block text-[13px] font-semibold text-[var(--text-primary)] mb-1">Barangay</label>
                            <select class="ctrl-select" name="barangay">
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
                            <label class="block text-[13px] font-semibold text-[var(--text-primary)] mb-1">Date From</label>
                            <input type="date" class="ctrl-input" name="date_from" value="{{ $dateFrom }}">
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-[var(--text-primary)] mb-1">Date To</label>
                            <input type="date" class="ctrl-input" name="date_to" value="{{ $dateTo }}">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold rounded-lg transition-colors duration-200 cursor-pointer border-0" style="font-family:var(--font-family)">
                                <i data-lucide="filter" style="width:16px;height:16px"></i> Filter
                            </button>
                            @if(request('barangay') || request('date_from') || request('date_to'))
                                <a href="{{ route('admin.senior.payouts-history') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-[#6B7280] hover:bg-[#4B5563] text-white text-sm font-medium rounded-lg transition-colors duration-200 no-underline" style="font-family:var(--font-family);white-space:nowrap">
                                    <i data-lucide="x" style="width:15px;height:15px"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- History Table -->
            <div class="custom-table-wrapper">
                <div class="custom-table-scroll">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Action</th>
                                <th>Senior</th>
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
                                            <strong>{{ $record->senior->full_name }}</strong>
                                            <br>
                                            <small class="text-[var(--text-muted)] text-xs">{{ $record->senior->control_number ?? '-' }}</small>
                                        @else
                                            <span class="text-[var(--text-muted)]">System-wide action</span>
                                        @endif
                                    </td>
                                    <td data-label="Details">{{ $record->details ?? '-' }}</td>
                                    <td data-label="Performed By">
                                        @if($record->performedBy)
                                            {{ $record->performedBy->name ?? 'Admin' }}
                                        @else
                                            <span class="text-[var(--text-muted)]">System</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty-state-cell text-center py-12">
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
                    <div class="flex justify-center py-5 border-t border-[var(--border)]">
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

    <div class="mobile-bottom-nav"><div class="mobile-bottom-nav-row"><a href="/admin/senior" class="mobile-bottom-nav-item"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></a><a href="/admin/senior/registration" class="mobile-bottom-nav-item"><i data-lucide="user-plus"></i><span>Register</span></a><a href="/admin/senior/masterlist" class="mobile-bottom-nav-item"><i data-lucide="list"></i><span>Masterlist</span></a><a href="/admin/senior/birthdays" class="mobile-bottom-nav-item"><i data-lucide="cake"></i><span>Birthdays</span></a><button type="button" class="mobile-bottom-nav-item" onclick="toggleMobileMoreNav()"><i data-lucide="chevron-up" id="mobileMoreIcon"></i><span>More</span></button></div><div class="mobile-bottom-nav-row mobile-nav-extra" id="mobileNavExtra" style="display:none;"><a href="/admin/senior/payouts-history" class="mobile-bottom-nav-item active"><i data-lucide="history"></i><span>Payouts</span></a><a href="/admin/senior/statistics" class="mobile-bottom-nav-item"><i data-lucide="bar-chart-3"></i><span>Stats</span></a><a href="/admin/senior/archive" class="mobile-bottom-nav-item"><i data-lucide="archive"></i><span>Archive</span></a><a href="#" onclick="confirmLogout(event)" class="mobile-bottom-nav-item"><i data-lucide="log-out"></i><span>Logout</span></a></div></div>

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