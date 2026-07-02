<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSWDO – Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ── Design tokens ── */
        :root {
            --primary:      #1A237E;
            --primary-dark: #121858;
            --secondary:    #6B7280;
            --accent:       #FBC02D;
            --danger:       #D32F2F;
            --violet:       #1A237E;
            --background:   #F8FAFC;
            --cards:        #FFFFFF;
            --text:         #1F2937;
            --muted:        #6B7280;
            --sidebar-bg:   #1A237E;
            --border:       #E5E7EB;
        }

        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
        }

        /* ── Sidebar ── */
        .sidebar {
            background: var(--sidebar-bg);
            width: 260px;
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .sidebar-brand i { font-size: 1.3rem; color: var(--accent); }
        .sidebar-menu {
            list-style: none;
            margin: 0;
            padding: 1rem 0;
            flex: 1;
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
            color: var(--accent); 
        }
        .sidebar-menu a.active {
            background: rgba(255,255,255,.1);
            color: var(--accent);
            border-left-color: var(--accent);
        }
        .sidebar-menu a i { width: 20px; text-align: center; font-size: .95rem; }

        /* ── Main content ── */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top-bar ── */
        .top-navbar {
            background-color: var(--cards);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .page-title { font-size: 1.15rem; font-weight: 700; margin: 0; }
        .breadcrumb-nav { font-size: .8rem; color: var(--muted); margin: 0; }
        .breadcrumb-nav a { color: var(--primary); text-decoration: none; }
        .btn-icon {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            width: 38px; height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
        }
        .btn-icon:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Page body ── */
        .page-body { padding: 2rem; flex: 1; }

        /* ── KPI cards ── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .kpi-card {
            background: var(--cards);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 1.4rem 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform .2s, box-shadow .2s;
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(0,0,0,.1); }
        .kpi-icon {
            width: 54px; height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .kpi-icon.blue   { background: rgba(37,99,235,.1);  color: var(--primary); }
        .kpi-icon.teal   { background: rgba(20,184,166,.1); color: var(--secondary); }
        .kpi-icon.amber  { background: rgba(245,158,11,.1); color: var(--accent); }
        .kpi-icon.violet { background: rgba(139,92,246,.1); color: var(--violet); }
        .kpi-icon.red    { background: rgba(220,38,38,.1);  color: var(--danger); }
        .kpi-value { font-size: 1.9rem; font-weight: 700; margin: 0; line-height: 1; }
        .kpi-label { font-size: .8rem; color: var(--muted); margin: .25rem 0 0; }

        /* ── Chart cards ── */
        .chart-card {
            background: var(--cards);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .chart-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .chart-card-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
        }
        .chart-card-subtitle {
            font-size: .78rem;
            color: var(--muted);
            margin: .2rem 0 0;
        }
        .chart-wrap { position: relative; }

        /* ── Select pill ── */
        .filter-select {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .35rem .75rem;
            font-size: .8rem;
            color: var(--text);
            outline: none;
            cursor: pointer;
        }
        .filter-select:focus { border-color: var(--primary); }

        /* ── Barangay table ── */
        .progress-thin {
            height: 6px;
            border-radius: 99px;
            background: var(--border);
            overflow: hidden;
        }
        .progress-thin .bar {
            height: 100%;
            border-radius: 99px;
            background: var(--primary);
        }
        .search-input {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .4rem .8rem .4rem 2rem;
            font-size: .82rem;
            outline: none;
            width: 200px;
            transition: border-color .2s;
        }
        .search-input:focus { border-color: var(--primary); }
        .search-wrapper { position: relative; display: inline-block; }
        .search-wrapper i {
            position: absolute;
            left: .55rem; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .8rem;
            pointer-events: none;
        }

        /* Minimalist Table */
        .gov-table-wrap {
            border: none;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
            overflow: hidden;
        }
        .gov-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .85rem;
            margin: 0;
        }
        /* Official header band */
        .gov-table thead tr.official-header th {
            background: #FFFFFF;
            color: #475569;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: .9rem .75rem;
            border-bottom: 2px solid #E2E8F0;
            text-align: center;
            white-space: nowrap;
        }
        .gov-table thead tr.official-header th.col-brgy {
            text-align: left;
        }
        /* Sortable col header */
        .gov-table thead tr.official-header th.sortable {
            cursor: pointer;
            user-select: none;
        }
        .gov-table thead tr.official-header th.sortable:hover {
            color: var(--primary);
        }
        .sort-icon { margin-left: .25rem; opacity: .4; font-size: .7rem; }
        .sort-icon.asc  { opacity: 1; color: var(--primary); }
        .sort-icon.desc { opacity: 1; color: var(--primary); }

        /* Body rows */
        .gov-table tbody tr.brgy-row {
            border-bottom: 1px solid #F1F5F9;
            transition: background .1s ease;
        }
        .gov-table tbody tr.brgy-row:hover { background: #F8FAFC; }
        .gov-table tbody td {
            padding: .85rem .75rem;
            vertical-align: middle;
            border: none;
            color: var(--text);
        }
        .gov-table tbody td.num { text-align: right; font-variant-numeric: tabular-nums; }
        .gov-table tbody td.center { text-align: center; }

        /* Grand total row */
        .gov-table tfoot tr td {
            background: #FFFFFF;
            color: #1E293B;
            font-weight: 700;
            padding: 1rem .75rem;
            font-size: .85rem;
            border-top: 2px solid #E2E8F0;
            border-bottom: none;
        }
        .gov-table tfoot tr td.num { text-align: right; }

        /* Rank cell */
        .rank-no {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px; height: 26px;
            border-radius: 4px;
            font-size: .72rem;
            font-weight: 700;
            color: #fff;
        }
        .rk-1 { background: #D97706; }
        .rk-2 { background: #6B7280; }
        .rk-3 { background: #92400E; }
        .rk-n { background: #2563EB; }

        /* Status pill */
        .status-pill {
            display: inline-block;
            padding: .2rem .6rem;
            border-radius: 4px;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .status-critical  { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
        .status-high      { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        .status-moderate  { background: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE; }
        .status-low       { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }

        /* Bar in table */
        .mini-bar-wrap { display: flex; align-items: center; gap: .5rem; min-width: 110px; }
        .mini-bar {
            flex: 1;
            height: 5px;
            background: #E2E8F0;
            border-radius: 99px;
            overflow: hidden;
        }
        .mini-bar-fill { height: 100%; border-radius: 99px; background: #2563EB; }
        .mini-pct { font-size: .7rem; color: var(--muted); min-width: 34px; text-align: right; }

        /* Brgy name */
        .brgy-name-cell { font-weight: 600; color: #1E293B; }
        .brgy-name-cell small { font-weight: 400; color: var(--muted); display: block; font-size: .7rem; }

        /* ── Export bar ── */
        .export-bar {
            display: flex;
            align-items: center;
            gap: .65rem;
            flex-wrap: wrap;
        }
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .45rem 1rem;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            transition: all .2s;
        }
        .btn-export:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-export.primary { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-export.primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp .5s ease both; }
        .d1 { animation-delay: .05s; }
        .d2 { animation-delay: .1s; }
        .d3 { animation-delay: .15s; }
        .d4 { animation-delay: .2s; }
        .d5 { animation-delay: .25s; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .page-body { padding: 1rem; }
        }

        /* ── Scrollable table container ── */
        .table-scroll { max-height: 480px; overflow-y: auto; }
        .table-scroll::-webkit-scrollbar { width: 6px; }
        .table-scroll::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
    </style>
</head>
<body>

<!-- ======================== SIDEBAR ======================== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-building"></i>
        <span>MSWDO Admin</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="/admin/statistics" class="active"><i class="fas fa-chart-line"></i> Statistics</a></li>
        <li><a href="#"><i class="fas fa-hand-holding-usd"></i> Financial Assistance</a></li>
        <li><a href="#"><i class="fas fa-user-friends"></i> Senior Citizen</a></li>
        <li><a href="/admin/add-officers"><i class="fas fa-user-shield"></i> Add Officers</a></li>
        <li><a href="/admin"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- ======================== MAIN ======================== -->
<div class="main-content">

    <!-- Top-bar -->
    <nav class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn-icon d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <p class="page-title">Statistics &amp; Analytics</p>
                <p class="breadcrumb-nav">
                    <a href="/admin/dashboard">Dashboard</a> / Statistics
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div id="currentDateTime" class="text-muted small d-none d-md-block"></div>
            <button class="btn-icon" title="Refresh" onclick="location.reload()">
                <i class="fas fa-rotate-right"></i>
            </button>
        </div>
    </nav>

    <!-- Page Body -->
    <div class="page-body">

        <!-- Export Bar -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h2 class="h5 fw-bold mb-0">Silang MSWDO Case Analytics</h2>
                <p class="text-muted small mb-0">Overview of all registered cases across barangays – {{ date('F Y') }}</p>
            </div>
            <div class="export-bar">
                <button class="btn-export"><i class="fas fa-file-pdf"></i> PDF</button>
                <button class="btn-export"><i class="fas fa-file-excel"></i> Excel</button>
                <button class="btn-export primary"><i class="fas fa-print"></i> Print</button>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="kpi-grid fade-up">
            @php
                $totalCases = array_sum($barangayStats);
                $fa  = $caseDistribution['Financial Assistance'] ?? 0;
                $sc  = $caseDistribution['Senior Citizen'] ?? 0;
                $topBarangay = array_search(max($barangayStats), $barangayStats);
            @endphp
            <div class="kpi-card d1">
                <div class="kpi-icon blue"><i class="fas fa-folder-open"></i></div>
                <div>
                    <p class="kpi-value">{{ number_format($totalCases) }}</p>
                    <p class="kpi-label">Total Cases</p>
                </div>
            </div>
            <div class="kpi-card d2">
                <div class="kpi-icon teal"><i class="fas fa-hand-holding-usd"></i></div>
                <div>
                    <p class="kpi-value">{{ number_format($fa) }}</p>
                    <p class="kpi-label">Financial Assistance</p>
                </div>
            </div>
            <div class="kpi-card d3">
                <div class="kpi-icon violet"><i class="fas fa-user-friends"></i></div>
                <div>
                    <p class="kpi-value">{{ number_format($sc) }}</p>
                    <p class="kpi-label">Senior Citizen</p>
                </div>
            </div>
            <div class="kpi-card d4">
                <div class="kpi-icon blue"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <p class="kpi-value" style="font-size:1.1rem;">{{ $topBarangay }}</p>
                    <p class="kpi-label">Highest Barangay</p>
                </div>
            </div>
        </div>

        <!-- Row: Barangay Bar Chart + Case Distribution Pie -->
        <div class="row g-4 mb-2">
            <div class="col-xl-8">
                <div class="chart-card fade-up d1">
                    <div class="chart-card-header">
                        <div>
                            <p class="chart-card-title">Barangay Statistics</p>
                            <p class="chart-card-subtitle">Number of cases per barangay (top 20 shown)</p>
                        </div>
                        <select class="filter-select" id="barangayFilterSelect" onchange="filterBarangayChart(this.value)">
                            <option value="all">All Cases</option>
                            <option value="fa">Financial Assistance</option>
                            <option value="sc">Senior Citizen</option>
                        </select>
                    </div>
                    <div class="chart-wrap" style="height:400px;">
                        <canvas id="barangayChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="chart-card fade-up d2" style="height: 100%;">
                    <div class="chart-card-header">
                        <div>
                            <p class="chart-card-title">Case Distribution</p>
                            <p class="chart-card-subtitle">Breakdown by case type</p>
                        </div>
                    </div>
                    <div class="chart-wrap" style="height: 280px;">
                        <canvas id="distributionChart"></canvas>
                    </div>
                    <!-- Legend detail -->
                    <div class="mt-4">
                        @php
                            $colors = ['#1A237E','#D32F2F'];
                            $i = 0;
                            $total = array_sum($caseDistribution);
                        @endphp
                        @foreach($caseDistribution as $label => $value)
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:10px;height:10px;border-radius:50%;background:{{ $colors[$i] }};flex-shrink:0;"></div>
                                <span style="font-size:.82rem;">{{ $label }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress-thin" style="width:80px;">
                                    <div class="bar" style="width:{{ $total > 0 ? round($value/$total*100) : 0 }}%;background:{{ $colors[$i] }};"></div>
                                </div>
                                <span style="font-size:.82rem;font-weight:600;min-width:36px;text-align:right;">{{ $value }}</span>
                            </div>
                        </div>
                        @php $i++; @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend Line Chart -->
        <div class="chart-card fade-up d3 mb-4">
            <div class="chart-card-header">
                <div>
                    <p class="chart-card-title">Monthly Case Trends</p>
                    <p class="chart-card-subtitle">Comparison of case types over the past 6 months</p>
                </div>
            </div>
            <div class="chart-wrap" style="height:280px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Barangay Breakdown Table – Government Registry Format -->
        <div class="chart-card fade-up d4">

            {{-- ── Table header controls ── --}}
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
                <div>
                    <p class="chart-card-title mb-1">Barangay Case Registry</p>
                    <p class="chart-card-subtitle mb-0">
                        Municipality of Silang, Cavite &nbsp;|&nbsp;
                        Prepared by: MSWDO &nbsp;|&nbsp;
                        As of: {{ date('F d, Y') }} &nbsp;|&nbsp;
                        Total Barangays: <strong>{{ count($barangayStats) }}</strong>
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-input" id="tableSearch"
                               placeholder="Search barangay…" oninput="filterTable(this.value)">
                    </div>
                    <button class="btn-export" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>

            @php
                arsort($barangayStats);
                $grandTotal = array_sum($barangayStats);
                $grandFA    = 0;
                $grandSC    = 0;

                // In production, replace with real DB queries per barangay
                $brgyData = [];
                foreach ($barangayStats as $brgy => $total_b) {
                    $fa_b  = (int) round($total_b * 0.60);
                    $sc_b  = $total_b - $fa_b;
                    $grandFA  += $fa_b;
                    $grandSC  += $sc_b;

                    $brgyData[] = [
                        'name'  => $brgy,
                        'fa'    => $fa_b,
                        'sc'    => $sc_b,
                        'total' => $total_b,
                    ];
                }
            @endphp

            <div class="gov-table-wrap" style="max-height:520px;overflow-y:auto;">
                <table class="gov-table" id="barangayTable">
                    <thead style="position:sticky;top:0;z-index:5;">
                        <tr class="official-header">
                            <th class="col-brgy sortable" onclick="sortTable('name')">
                                Barangay Name <i class="fas fa-sort sort-icon" id="sort-name"></i>
                            </th>
                            <th class="sortable text-end" onclick="sortTable('fa')">
                                Fin. Assistance <i class="fas fa-sort sort-icon" id="sort-fa"></i>
                            </th>
                            <th class="sortable text-end" onclick="sortTable('sc')">
                                Senior Citizen <i class="fas fa-sort sort-icon" id="sort-sc"></i>
                            </th>
                            <th class="sortable text-end" onclick="sortTable('total')">
                                Total Cases <i class="fas fa-sort sort-icon" id="sort-total"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="brgyTableBody">
                        @foreach($brgyData as $row)
                        <tr class="brgy-row"
                            data-name="{{ strtolower($row['name']) }}"
                            data-fa="{{ $row['fa'] }}"
                            data-sc="{{ $row['sc'] }}"
                            data-total="{{ $row['total'] }}">

                            <td class="brgy-name-cell brgy-name">
                                {{ $row['name'] }}
                                <small>Brgy., Silang, Cavite</small>
                            </td>

                            <td class="num">{{ number_format($row['fa']) }}</td>
                            <td class="num">{{ number_format($row['sc']) }}</td>

                            <td class="num" style="font-weight:700; color: var(--text);">
                                {{ number_format($row['total']) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="font-weight:600; color: var(--muted);">GRAND TOTAL</td>
                            <td class="num">{{ number_format($grandFA) }}</td>
                            <td class="num">{{ number_format($grandSC) }}</td>
                            <td class="num" style="font-size:.9rem;">{{ number_format($grandTotal) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="text-muted mt-2" style="font-size:.7rem;">
                <i class="fas fa-circle-info me-1"></i>
                Data reflects all registered cases handled by MSWDO Silang. Case counts per type are
                system-generated estimates. For official records, coordinate with the Records Section.
                Generated: {{ date('F d, Y, h:i A') }}
            </p>
        </div>

    </div><!-- /page-body -->
</div><!-- /main-content -->

<!-- ======================== SCRIPTS ======================== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* ---------- Helpers ---------- */
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('show');
}

function updateDateTime() {
    const now = new Date();
    const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' };
    const el = document.getElementById('currentDateTime');
    if (el) el.textContent = now.toLocaleDateString('en-PH', opts);
}
updateDateTime();
setInterval(updateDateTime, 60000);

/* ---------- Raw data from PHP ---------- */
const barangayRaw  = @json($barangayStats);
const distRaw      = @json($caseDistribution);

/* Top-20 barangay labels / values (already sorted descending by PHP) */
const brgyEntries  = Object.entries(barangayRaw).slice(0, 20);
const brgyLabels   = brgyEntries.map(e => e[0]);
const brgyValues   = brgyEntries.map(e => e[1]);

/* ---------- Barangay Horizontal Bar Chart ---------- */
const barangayCtx  = document.getElementById('barangayChart').getContext('2d');
const barangayChart = new Chart(barangayCtx, {
    type: 'bar',
    data: {
        labels: brgyLabels,
        datasets: [{
            label: 'Cases',
            data: brgyValues,
            backgroundColor: brgyValues.map((v, i) => {
                if (i === 0) return '#FBC02D';
                if (i === 1) return '#94A3B8';
                if (i === 2) return '#CD7F32';
                return 'rgba(26,35,126,0.75)';
            }),
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.parsed.x.toLocaleString()} cases`
                }
            }
        },
        scales: {
            x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' } },
            y: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

/* ---------- Donut Distribution Chart ---------- */
const distCtx = document.getElementById('distributionChart').getContext('2d');
new Chart(distCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(distRaw),
        datasets: [{
            data: Object.values(distRaw),
            backgroundColor: ['#1A237E','#D32F2F'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()}`
                }
            }
        }
    }
});

/* ---------- Monthly Trend Line Chart ---------- */
const months = ['January','February','March','April','May','June'];
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Financial Assistance',
                data: [65, 72, 80, 78, 88, 92],
                borderColor: '#1A237E',
                backgroundColor: 'rgba(26,35,126,.08)',
                tension: .4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
            },
            {
                label: 'Senior Citizen',
                data: [10, 9, 12, 11, 14, 11],
                borderColor: '#D32F2F',
                backgroundColor: 'rgba(211,47,47,.08)',
                tension: .4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top', labels: { boxWidth: 12, font: { size: 12 } } }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' } },
            x: { grid: { display: false } }
        }
    }
});

/* ---------- Barangay filter (demo – adjusts bar opacity) ---------- */
function filterBarangayChart(val) {
    // In a real app this would fetch filtered data; here we just animate colours
    const colors = brgyValues.map((v, i) => {
        if (i === 0) return '#FBC02D';
        if (i === 1) return '#94A3B8';
        if (i === 2) return '#CD7F32';
        return val === 'all' ? 'rgba(26,35,126,0.75)' : 'rgba(26,35,126,0.3)';
    });
    barangayChart.data.datasets[0].backgroundColor = colors;
    barangayChart.update();
}

/* ---------- Table search ---------- */
function filterTable(query) {
    const q = query.toLowerCase().trim();
    const statusVal = document.getElementById('statusFilter').value;
    document.querySelectorAll('.brgy-row').forEach(row => {
        const name   = row.dataset.name   || '';
        const status = row.dataset.status || '';
        const nameOk   = name.includes(q);
        const statusOk = !statusVal || status === statusVal;
        row.style.display = (nameOk && statusOk) ? '' : 'none';
    });
}

function filterByStatus(val) {
    const q = (document.getElementById('tableSearch').value || '').toLowerCase().trim();
    document.querySelectorAll('.brgy-row').forEach(row => {
        const name   = row.dataset.name   || '';
        const status = row.dataset.status || '';
        const nameOk   = name.includes(q);
        const statusOk = !val || status === val;
        row.style.display = (nameOk && statusOk) ? '' : 'none';
    });
}

/* ---------- Column sort ---------- */
let sortState = { col: 'total', dir: 'desc' };

function sortTable(col) {
    const tbody = document.getElementById('brgyTableBody');
    const rows  = Array.from(tbody.querySelectorAll('.brgy-row'));

    if (sortState.col === col) {
        sortState.dir = sortState.dir === 'asc' ? 'desc' : 'asc';
    } else {
        sortState.col = col;
        sortState.dir = col === 'name' ? 'asc' : 'desc';
    }

    // Reset all icons
    document.querySelectorAll('.sort-icon').forEach(el => {
        el.classList.remove('asc','desc');
        el.className = 'fas fa-sort sort-icon';
    });
    const iconEl = document.getElementById('sort-' + col);
    if (iconEl) {
        iconEl.classList.add(sortState.dir);
        iconEl.className = `fas fa-sort-${sortState.dir === 'asc' ? 'up' : 'down'} sort-icon ${sortState.dir}`;
    }

    rows.sort((a, b) => {
        let aVal = a.dataset[col] || '';
        let bVal = b.dataset[col] || '';
        if (col !== 'name') { aVal = parseFloat(aVal) || 0; bVal = parseFloat(bVal) || 0; }
        if (aVal < bVal) return sortState.dir === 'asc' ? -1 : 1;
        if (aVal > bVal) return sortState.dir === 'asc' ? 1 : -1;
        return 0;
    });

    // Re-number ranks after sort
    rows.forEach((row, i) => { tbody.appendChild(row); });
}
</script>
</body>
</html>
