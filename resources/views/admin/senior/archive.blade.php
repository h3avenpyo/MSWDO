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
            --shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            --font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100vh; overflow: hidden; background: var(--background); color: var(--text-primary); font-family: var(--font-family); }
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
            max-width: calc(100% - 260px); height: 100vh;
            display: flex; flex-direction: column; overflow: hidden;
            animation: fadeIn .3s ease;
        }

        .main-content-scroll {
            flex: 1;
            overflow-y: auto;
        }

        /* Custom Table */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: var(--font-family);
        }
        .custom-table thead th {
            background-color: var(--background);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1;
            white-space: nowrap;
        }
        .custom-table tbody td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: middle;
            font-size: 0.875rem;
            color: var(--text-primary);
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8"
                style="margin-top:-32px;margin-left:-32px;margin-right:-32px">
            <div class="flex items-center"><h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Archived Seniors</h1></div>
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
            <div class="animate-fade-in bg-white rounded-2xl border border-[#E5E7EB] shadow-[0_4px_6px_-1px_rgba(0,0,0,.05)] p-4 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[10px] flex items-center justify-center bg-[rgba(107,114,128,0.1)] flex-shrink-0">
                        <i data-lucide="archive" class="w-5 h-5 text-[#6B7280]"></i>
                    </div>
                    <div>
                        <p class="text-[#6B7280] text-[13px] font-medium m-0 leading-tight">Total Archived</p>
                        <h4 class="text-[1.4rem] font-bold m-0 leading-none text-[#111827]">{{ $archivedSeniors->total() }}</h4>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="animate-fade-in bg-white rounded-2xl border border-[#E5E7EB] shadow-[0_4px_6px_-1px_rgba(0,0,0,.05)] p-5 mb-5">
                <form method="GET" action="{{ route('admin.senior.archive.list') }}">
                    <div class="flex items-end justify-between gap-3 flex-wrap">
                        <!-- Left: Search + Filter -->
                        <div class="flex gap-3 flex-1 min-w-0">
                            <div class="flex-1 min-w-[250px]">
                                <label class="block text-xs text-[var(--text-primary)] font-semibold mb-1">Search by Name</label>
                                <div class="flex">
                                    <input type="text" name="search" placeholder="Search by name..." value="{{ request('search') }}"
                                           class="flex-1 border border-[#E5E7EB] border-r-0 rounded-l-lg px-3 text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#1A237E] focus:border-transparent"
                                           style="height:42px;font-family:var(--font-family)">
                                    <button type="submit"
                                            class="bg-[#1A237E] hover:bg-[#121858] text-white border-none rounded-r-lg px-3 cursor-pointer transition-colors flex items-center justify-center"
                                            style="height:42px;width:42px">
                                        <i data-lucide="search" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="min-w-[200px]">
                                <label class="block text-xs text-[var(--text-primary)] font-semibold mb-1">Filter by Barangay</label>
                                <select name="barangay" onchange="this.form.submit()"
                                        class="custom-select w-full border border-[#E5E7EB] rounded-lg px-3 text-sm text-[#111827] bg-white focus:outline-none focus:ring-2 focus:ring-[#1A237E] focus:border-transparent cursor-pointer"
                                        style="height:42px;font-family:var(--font-family)">
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

                        <!-- Right: Action Buttons -->
                        <div class="flex gap-3 flex-shrink-0">
                            <a href="/admin/senior/masterlist"
                               class="inline-flex items-center gap-1.5 bg-[#1A237E] hover:bg-[#121858] text-white rounded-lg font-semibold text-[13px] px-3.5 transition-colors no-underline"
                               style="height:38px;font-family:var(--font-family)">
                                <i data-lucide="list" class="w-4 h-4"></i> Back to Masterlist
                            </a>
                            <button type="button" id="bulkActionButton" disabled onclick="showBulkActionPopup()"
                                    class="inline-flex items-center gap-1.5 bg-[#FBC02D] hover:bg-[#F9A825] text-[#121858] rounded-lg font-semibold text-[13px] px-3.5 transition-colors border-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                    style="height:38px;font-family:var(--font-family)">
                                <i data-lucide="list-checks" class="w-4 h-4"></i> Bulk Actions <span id="selectedCount" class="bg-[#121858] text-white px-1.5 py-0.5 rounded-[10px] text-[11px] ml-1 font-bold">0</span>
                            </button>
                            @if(request('search') || request('barangay'))
                                <a href="{{ route('admin.senior.archive.list') }}"
                                   class="inline-flex items-center gap-1.5 bg-[#6B7280] hover:bg-[#4B5563] text-white rounded-lg px-3.5 transition-colors no-underline"
                                   style="height:38px;font-size:13px;font-family:var(--font-family)">
                                    <i data-lucide="x" class="w-4 h-4"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Archive Table Card -->
            <div class="animate-fade-in bg-white rounded-2xl border border-[#E5E7EB] shadow-[0_4px_6px_-1px_rgba(0,0,0,.05)] flex flex-col overflow-hidden mb-6">
                <div class="flex justify-between items-center px-5 pt-5 pb-3">
                    <div>
                        <h6 class="font-bold text-[15px] text-[#111827] m-0">Archived Records</h6>
                        <span class="text-[#9CA3AF] text-xs">Showing {{ $archivedSeniors->firstItem() ?? 0 }}–{{ $archivedSeniors->lastItem() ?? 0 }} of {{ $archivedSeniors->total() }} records</span>
                    </div>
                </div>
                <div class="flex-1">
                    <table class="custom-table" id="archiveTable">
                        <thead>
                            <tr>
                                <th class="w-[40px]"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="cursor-pointer accent-[#1A237E]" style="width:16px;height:16px"></th>
                                <th>#</th>
                                <th>Control No.</th>
                                <th>Full Name</th>
                                <th>Barangay</th>
                                <th>Sex / Age</th>
                                <th>Birth Date</th>
                                <th>Archived On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archivedSeniors as $index => $senior)
                            <tr>
                                <td><input type="checkbox" class="senior-checkbox cursor-pointer accent-[#1A237E]" data-id="{{ $senior->id }}" onchange="updateBulkActions()" style="width:16px;height:16px"></td>
                                <td class="text-[#9CA3AF] font-semibold">{{ $archivedSeniors->firstItem() + $index }}</td>
                                <td class="font-semibold">{{ $senior->control_number ?? '-' }}</td>
                                <td>
                                    <div class="font-semibold">{{ $senior->full_name ?? '-' }}</div>
                                    <div class="text-[#9CA3AF] text-[12px]">{{ $senior->address ? \Illuminate\Support\Str::limit($senior->address, 35) : '' }}</div>
                                </td>
                                <td>
                                    @if($senior->barangay)
                                        <span class="inline-block bg-[rgba(107,114,128,0.1)] text-[#6B7280] font-medium px-2.5 py-1 rounded-md text-[13px]">{{ $senior->barangay }}</span>
                                    @else
                                        <span class="text-[#9CA3AF]">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($senior->sex)
                                        <span class="inline-flex items-center justify-center w-[26px] h-[26px] rounded-full bg-[#6B7280] text-white text-[11px] font-bold mr-1">{{ $senior->sex == 'Male' ? 'M' : 'F' }}</span>
                                    @endif
                                    <strong>{{ $senior->age ?? '-' }}</strong>
                                </td>
                                <td>
                                    @if($senior->birth_date)
                                        {{ \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-[#9CA3AF]">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-[#9CA3AF] text-[13px]">
                                        {{ $senior->updated_at ? \Carbon\Carbon::parse($senior->updated_at)->format('M d, Y') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-archived">Archived</span>
                                </td>
                                <td>
                                    <!-- Restore Button -->
                                    <form method="POST" action="{{ route('admin.senior.unarchive', $senior->id) }}" id="restore-form-{{ $senior->id }}" style="display: inline;">
                                        @csrf
                                        <button type="button"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-[rgba(20,184,166,0.1)] text-[#0f766e] border border-[rgba(20,184,166,0.3)] cursor-pointer hover:bg-[rgba(20,184,166,0.2)] transition-colors"
                                                onclick="confirmRestore({{ $senior->id }}, '{{ addslashes($senior->full_name) }}')"
                                                title="Restore to Active">
                                            <i data-lucide="undo-2" class="w-4 h-4"></i>
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
            document.getElementById('sidebar').classList.toggle('show');
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
            updateBulkActions();
        }

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
            const button = document.getElementById('bulkActionButton');
            const countSpan = document.getElementById('selectedCount');

            countSpan.textContent = checkboxes.length;

            if (checkboxes.length > 0) {
                button.disabled = false;
                button.style.opacity = '1';
            } else {
                button.disabled = true;
                button.style.opacity = '0.5';
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
                cancelButtonColor: '#6B7280',
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
                cancelButtonColor: '#d33',
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
                cancelButtonColor: '#6B7280',
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
                cancelButtonColor: '#d33',
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
</body>
</html>
