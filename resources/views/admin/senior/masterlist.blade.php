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
            --primary-dark: #121858;
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
            --sidebar-width: 260px;
            --content-padding: 32px;
            --shadow: 0 10px 30px rgba(15,23,42,.08);
            --font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; background: var(--background); color: var(--text-primary); font-family: var(--font-family); min-height: 100%; }
        body { font-size: 14px; line-height: 1.5; overflow-x: hidden; }
        h1, h2, h3, h4 { margin: 0; font-weight: 600; letter-spacing: -0.01em; }
        button { font-family: inherit; cursor: pointer; }
        a { text-decoration: none; }

        /* ---------- Filters ---------- */
        .filter-section { margin-bottom: 20px; }
        .filters-grid {
            display: grid;
            grid-template-columns: minmax(0,1fr) minmax(200px,280px) auto;
            gap: 14px; align-items: end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .filter-label {
            font-size: 12px; font-weight: 600; color: var(--text-primary);
            text-transform: uppercase; letter-spacing: .05em; display: flex; align-items: center; gap: 6px;
        }
        .input-group {
            display: flex; align-items: center; height: 44px; background: var(--surface);
            border: 1px solid var(--border); border-radius: 10px; overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
        }
        .input-group:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,35,126,.1); }
        .input-group input {
            flex: 1; min-width: 0; height: 44px; border: none; outline: none;
            padding: 0 14px; font-size: 14px; color: var(--text-primary); background: transparent;
        }
        .input-group .search-btn {
            background: var(--primary); color: #fff; border: none; width: 48px; height: 44px;
            display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0;
            transition: background .2s;
        }
        .input-group .search-btn:hover { background: var(--primary-hover); }
        .select-wrap { position: relative; }
        .filter-select {
            width: 100%; height: 44px; border: 1px solid var(--border); border-radius: 10px;
            padding: 0 40px 0 14px; font-size: 14px; color: var(--text-primary);
            background: var(--surface); cursor: pointer; appearance: none; -webkit-appearance: none; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .filter-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,35,126,.1); }
        .select-arrow {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            width: 16px; height: 16px; color: var(--text-secondary); pointer-events: none;
        }
        .filter-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        /* ---------- Buttons ---------- */
        .btn {
            border: 1px solid var(--border); background: var(--surface); color: var(--text-primary);
            padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 500;
            display: inline-flex; align-items: center; gap: 8px; box-shadow: var(--shadow);
            transition: all .2s ease; height: 44px; cursor: pointer;
        }
        .btn:hover { border-color: var(--primary); transform: translateY(-1px); }
        .btn-export { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-export:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
        .btn-bulk { background: #E0E7FF; color: #3730A3; border: 1px solid #C7D2FE; }
        .btn-bulk:hover { border-color: #3730A3; transform: translateY(-1px); }
        .btn-bulk:disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }
        .btn-clear { background: var(--surface); color: var(--danger); border: 1px solid #FECACA; }
        .btn-clear:hover { border-color: var(--danger); }

        /* ---------- Dropdown ---------- */
        .dropdown { position: relative; display: inline-block; }
        .dropdown-menu {
            position: absolute; top: 100%; right: 0; z-index: 50;
            background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,.12); min-width: 200px; padding: 6px;
            display: none; margin-top: 6px;
        }
        .dropdown-menu.show { display: block; }
        .dropdown-item {
            display: flex; align-items: center; gap: 8px; padding: 10px 14px;
            font-size: 13px; color: var(--text-primary); border-radius: 6px;
            text-decoration: none; cursor: pointer; transition: background .15s;
        }
        .dropdown-item:hover { background: var(--background); }

        /* ---------- Table ---------- */
        .mobile-select-all {
            display: none; align-items: center; gap: 8px; padding: 10px 12px; margin-bottom: 10px;
            background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
            font-size: 13px; color: var(--text-secondary);
        }
        .table-responsive {
            width: 100%; max-width: 100%; overflow-x: auto; overflow-y: auto; -webkit-overflow-scrolling: touch;
            background: var(--surface); border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow);
            max-height: min(620px, calc(100vh - 260px));
        }
        .table-responsive table { width: 100%; border-collapse: collapse; table-layout: auto; }
        .table-responsive thead th {
            padding: 12px 16px; font-size: 11px; font-weight: 600; text-transform: uppercase;
            letter-spacing: .05em; color: var(--text-secondary); text-align: left;
            border-bottom: 2px solid var(--border); background: var(--surface); white-space: nowrap;
            position: sticky; top: 0; z-index: 1;
        }
        .table-responsive tbody td {
            padding: 12px 16px; font-size: 13px; color: var(--text-primary);
            border-bottom: 1px solid var(--border); vertical-align: middle;
            white-space: normal; word-break: break-word;
        }
        .table-responsive tbody tr:last-child td { border-bottom: none; }
        .table-responsive tbody tr:hover td { background: var(--background); }
        .table-responsive td[data-label="Control No"],
        .table-responsive td[data-label="Age"] { white-space: nowrap; }
        .table-responsive td .badge { white-space: nowrap; }
        .actions { display: flex; gap: 8px; }
        .action-btn {
            width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center;
            justify-content: center; border-radius: 8px;
        }
        .action-btn i { width: 15px; height: 15px; }
        .badge {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
            border-radius: 999px; font-size: 12px; font-weight: 500; white-space: nowrap;
        }
        .badge-active { background: var(--success-bg); color: var(--success); }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .checkbox { cursor: pointer; accent-color: var(--primary); width: 16px; height: 16px; }

        /* ---------- Pagination ---------- */
        .pagination-wrap {
            display: flex; justify-content: center; flex-wrap: wrap; gap: 8px;
            margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--border);
        }

        /* ---------- Modal ---------- */
        #seniorModal { transition: opacity .2s ease; }

        /* ---------- Tablet (768-1199px): filters in 2 rows ---------- */
        @media (min-width: 768px) and (max-width: 1199px) {
            .filters-grid { grid-template-columns: 1fr 1fr; }
            .filter-actions { grid-column: 1 / -1; }
            .table-responsive thead th:nth-child(2) { width: 16%; }
            .table-responsive tbody td[data-label="Control No"] { font-size: 12px; }
        }

        /* ---------- Large Desktop (1400px+): adjust control number column ---------- */
        @media (min-width: 1400px) {
            .table-responsive thead th:nth-child(2) { width: 12%; }
            .table-responsive tbody td[data-label="Control No"] { font-size: 14px; }
        }

        /* ---------- Desktop (1200-1399px): adjust control number column ---------- */
        @media (min-width: 1200px) and (max-width: 1399px) {
            .table-responsive thead th:nth-child(2) { width: 13%; }
            .table-responsive tbody td[data-label="Control No"] { font-size: 13px; }
        }

        /* ---------- Mobile (<768px): stacked filters, scrollable table ---------- */
        @media (max-width: 767px) {
            .filter-section { margin-bottom: 14px; }
            .filters-grid { grid-template-columns: 1fr; gap: 12px; }
            .filter-actions { flex-direction: column; align-items: stretch; }
            .filter-actions .btn,
            .filter-actions .dropdown,
            .filter-actions .dropdown > button { width: 100%; justify-content: center; }
            .mobile-select-all { display: flex; }
            .action-btn { width: 44px; height: 44px; }

            /* Table → stacked cards */
            .table-responsive {
                overflow: visible; border: none; background: transparent;
                box-shadow: none; border-radius: 0; max-height: none;
            }
            .table-responsive table { display: block; width: 100%; }
            .table-responsive thead { display: none; }
            .table-responsive tbody { display: block; }
            .table-responsive tbody tr {
                display: block;
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 12px;
                margin-bottom: 12px;
                padding: 12px 14px;
                box-shadow: var(--shadow);
            }
            .table-responsive tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                padding: 8px 0;
                border: none;
                border-bottom: 1px solid var(--border);
                white-space: normal;
                word-break: break-word;
                text-align: right;
            }
            .table-responsive tbody td:last-child { border-bottom: none; }
            .table-responsive tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--text-secondary);
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: .03em;
                flex-shrink: 0;
                min-width: 88px;
                text-align: left;
            }
            .table-responsive tbody td.col-check {
                justify-content: flex-end;
                border-bottom: none;
                padding: 0 0 6px;
            }
            .table-responsive tbody td.col-check::before { display: none; }
            .table-responsive tbody td[data-label="Control No"],
            .table-responsive tbody td[data-label="Age"] { white-space: nowrap; }
            .table-responsive tbody td[data-label="Action"] {
                justify-content: flex-end;
                border-bottom: none;
                padding-top: 10px;
            }
            .table-responsive tbody td[data-label="Action"]::before { display: none; }
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'masterlist', 'mobileSubtitle' => 'Senior Citizen Masterlist'])

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

        <div class="main-scroll">
            <div style="margin-bottom:1.5rem;">
                <p style="margin:0;font-size:0.875rem;color:#6B7280;">Step 1 of 2 — Search for an existing senior citizen and verify their record before proceeding with a new registration.</p>
            </div>
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="{{ route('admin.senior.masterlist') }}" id="filterForm">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label" for="searchInput">Search by Name</label>
                            <div class="input-group">
                                <input type="text" id="searchInput" name="search" placeholder="Search by name..." value="{{ request('search') }}">
                                <button type="submit" class="search-btn" aria-label="Search">
                                    <i data-lucide="search" style="width:16px;height:16px"></i>
                                </button>
                            </div>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label" for="barangaySelect"><i data-lucide="filter" style="width:14px;height:14px"></i> Filter by Barangay</label>
                            <div class="select-wrap">
                                <select class="filter-select" id="barangaySelect" name="barangay" onchange="this.form.submit()">
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
                                <i data-lucide="chevron-down" class="select-arrow"></i>
                            </div>
                        </div>
                        <div class="filter-actions">
                            <a href="#" class="btn btn-export" onclick="exportPdf(event)">
                                <i data-lucide="file-output" style="width:16px;height:16px"></i> Export PDF
                            </a>
                            <div class="dropdown" id="bulkActionDropdown">
                                <button id="bulkActionButton" class="btn btn-bulk" onclick="toggleDropdown()" disabled>
                                    <i data-lucide="archive" style="width:14px;height:14px"></i> Bulk Actions
                                    <span id="selectedCount" style="background:#3730A3;color:#fff;padding:2px 8px;border-radius:10px;font-size:11px;margin-left:4px;">0</span>
                                </button>
                                <div class="dropdown-menu" id="bulkDropdownMenu">
                                    <a class="dropdown-item" href="#" onclick="bulkArchive()"><i data-lucide="archive" style="width:14px;height:14px"></i> Archive Selected</a>
                                    <a class="dropdown-item" href="#" onclick="bulkExport()"><i data-lucide="download" style="width:14px;height:14px"></i> Export Selected</a>
                                </div>
                            </div>
                            @if(request('search') || request('barangay'))
                                <a href="{{ route('admin.senior.masterlist') }}" class="btn btn-clear">
                                    <i data-lucide="x" style="width:16px;height:16px"></i> Clear Filters
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            @if($seniors->count() > 0)
                <!-- Mobile Select All (shown only on mobile since the table scrolls horizontally) -->
                <div class="mobile-select-all">
                    <input type="checkbox" id="mobileSelectAll" onchange="toggleSelectAllMobile(this.checked)" class="checkbox">
                    <label for="mobileSelectAll" style="cursor:pointer;font-weight:500;">Select all</label>
                    <span id="mobileSelectedCount" style="margin-left:auto;font-size:12px;font-weight:600;color:var(--primary);"></span>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="col-check" style="width:4%;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="checkbox"></th>
                                <th style="width:14%;">Control No</th>
                                <th style="width:20%;">Full Name</th>
                                <th style="width:15%;">Barangay</th>
                                <th style="width:10%;">Status</th>
                                <th style="width:22%;">Address</th>
                                <th style="width:6%;">Age</th>
                                <th style="width:9%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seniors as $senior)
                                <tr>
                                    <td data-label="#" class="col-check"><input type="checkbox" class="senior-checkbox checkbox" data-id="{{ $senior->id }}" onchange="updateBulkActions()"></td>
                                    <td data-label="Control No" style="font-weight:600;word-wrap:break-word;">{{ $senior->control_number ?? '-' }}</td>
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
                                        <div class="actions">
                                            <button class="btn action-btn primary" style="background:var(--primary);border-color:var(--primary);color:#fff;" onclick="viewProfile({{ $senior->id }})" title="View Profile">
                                                <i data-lucide="eye"></i>
                                            </button>
                                            <button class="btn action-btn danger archive-senior-btn"
                                                data-id="{{ $senior->id }}"
                                                data-name="{{ $senior->full_name }}"
                                                style="background:var(--danger-bg);border-color:#FECACA;color:var(--danger);"
                                                title="Archive">
                                                <i data-lucide="archive"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrap">
                    {{ $seniors->appends(['barangay' => request('barangay'), 'search' => request('search')])->links('vendor.pagination.custom') }}
                </div>
            @else
                <div style="text-align:center;padding:60px 20px;color:var(--text-secondary);">
                    <i data-lucide="users" style="width:56px;height:56px;color:#D1D5DB;margin:0 auto 12px;display:block"></i>
                    <p style="margin:8px 0 16px;font-size:14px;color:var(--text-muted);">No senior citizens registered yet.</p>
                    <a href="/admin/senior/registration" class="btn btn-export">
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

<!-- Hidden form for secure POST logout -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
    @csrf
</form>

<script>
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
</body>
</html>
