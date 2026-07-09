<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Archived Senior Citizens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1A237E;
            --primary-dark: #121858;
            --secondary: #6B7280;
            --accent: #FBC02D;
            --danger: #D32F2F;
            --background: #F8FAFC;
            --cards: #FFFFFF;
            --text: #1F2937;
            --sidebar-bg: #1A237E;
            --border: #E5E7EB;
        }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* Sidebar */
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
        .sidebar-menu a i { width: 20px; text-align: center; }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .top-navbar {
            background-color: var(--cards);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            flex-shrink: 0;
        }

        /* Cards */
        .card {
            background-color: var(--cards);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        /* Table */
        .table th {
            background-color: var(--background);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem;
        }
        .table td {
            padding: 1rem;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background-color: transparent;
        }

        /* Archive Badge */
        .badge-archived {
            background-color: rgba(156, 163, 175, 0.15);
            color: #6B7280;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
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
        .empty-state i {
            font-size: 3.5rem;
            color: #D1D5DB;
            margin-bottom: 1rem;
            display: block;
        }
        .empty-state h5 {
            color: #6B7280;
            font-weight: 600;
        }
        .empty-state p {
            color: #9CA3AF;
            font-size: 0.9rem;
        }

        /* Pagination styling */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.25rem;
            margin: 0;
            list-style: none;
            padding: 0;
        }
        .pagination .page-item {
            margin: 0;
        }
        .pagination .page-link,
        .pagination .page-item span {
            display: inline-block;
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            text-decoration: none;
            background: var(--cards);
            transition: all 0.2s;
            min-width: 40px;
            text-align: center;
        }
        .pagination .page-link:hover,
        .pagination .page-item span:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .pagination .page-item.active .page-link,
        .pagination .page-item.active span {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .pagination .page-item.disabled .page-link,
        .pagination .page-item.disabled span {
            color: var(--secondary);
            background-color: var(--background);
            border-color: var(--border);
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.5s ease forwards; }
    </style>
</head>
<body>
    <!-- ======================== SIDEBAR ======================== -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-user-friends"></i>
            <span>Senior Citizen</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/senior"><i class="fas fa-user-friends"></i> Dashboard</a></li>
            <li><a href="/admin/senior/registration"><i class="fas fa-user-plus"></i> Registration</a></li>
            <li><a href="/admin/senior/masterlist"><i class="fas fa-list"></i> Masterlist</a></li>
            <li><a href="/admin/senior/birthdays"><i class="fas fa-birthday-cake"></i> Birthday Beneficiaries</a></li>
            <li><a href="/admin/senior/birthday-payouts"><i class="fas fa-money-bill-wave"></i> Birthday Payouts</a></li>
            <li><a href="/admin/senior/birthday-payouts/history"><i class="fas fa-history"></i> Payout History</a></li>
            <li><a href="/admin/senior/statistics"><i class="fas fa-chart-bar"></i> Statistics</a></li>
            <li><a href="/admin/senior/reports"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="/admin/senior/archive" class="active"><i class="fas fa-archive"></i> Archive</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none me-3" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <nav aria-label="breadcrumb" style="margin-bottom: 0;">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="/admin/senior" style="color: var(--primary); text-decoration: none;">Senior</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Archive</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-muted small" id="currentDateTime"></div>
                    <div style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">
                        {{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}
                    </div>
                </div>
            </div>
        </nav>

        <!-- Archive Content -->
        <div class="p-4" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">

            <!-- Page Header -->
            <div class="mb-3">
                <h5 class="mb-1" style="font-weight: 700; color: var(--text); font-size: 1rem;">
                    <i class="fas fa-archive me-2" style="color: var(--secondary);"></i> Archived Senior Citizens
                </h5>
                <p class="text-muted mb-0" style="font-size: 0.8rem;">Records that have been removed from the active list. You can restore them at any time.</p>
            </div>

            <!-- Alert messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; border: none; background: rgba(20,184,166,0.1); color: #0f766e;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px; border: none; background: rgba(220,38,38,0.1); color: #b91c1c;">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Summary Card -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card animate-fade-in" style="padding: 0;">
                        <div style="display: flex; align-items: center; padding: 0.75rem 1rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background: rgba(107,114,128,0.1); color: #6B7280; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-archive"></i>
                            </div>
                            <div>
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.1rem 0; font-weight: 500;">Total Archived</p>
                                <h4 style="font-size: 1.4rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $archivedSeniors->total() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="card animate-fade-in" style="padding: 0.75rem 1rem; margin-bottom: 0.75rem; overflow: visible;">
                <form method="GET" action="{{ route('admin.senior.archive.list') }}">
                    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                        <!-- Left Section: Search and Filter -->
                        <div style="display: flex; gap: 12px; flex: 1; min-width: 0;">
                            <div style="flex: 1; min-width: 250px;">
                                <label class="form-label small text-muted fw-semibold mb-1">Search by Name</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}" style="height: 38px; border-right: none;">
                                    <button type="submit" style="background-color: var(--primary); color: white; border: none; padding: 0 1rem; border-radius: 0 6px 6px 0; cursor: pointer; height: 38px;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div style="min-width: 200px;">
                                <label class="form-label small text-muted fw-semibold mb-1">Filter by Barangay</label>
                                <select class="form-select" name="barangay" onchange="this.form.submit()" style="height: 38px;">
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

                        <!-- Right Section: Action Buttons -->
                        <div style="display: flex; gap: 12px; flex-shrink: 0;">
                            <a href="/admin/senior/masterlist" class="btn" style="background-color: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; padding: 0 1rem; height: 38px; display: flex; align-items: center;">
                                <i class="fas fa-list me-1"></i> Back to Masterlist
                            </a>
                            <button type="button" class="btn" style="background-color: var(--accent); color: var(--primary-dark); border: none; border-radius: 8px; font-weight: 600; font-size: 0.8rem; padding: 0 1rem; height: 38px; display: flex; align-items: center;" id="bulkActionButton" disabled data-bs-toggle="modal" data-bs-target="#bulkActionModal">
                                <i class="fas fa-tasks me-1"></i> Bulk Actions <span id="selectedCount" style="background: var(--primary-dark); color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: 5px;">0</span>
                            </button>
                            @if(request('search') || request('barangay'))
                                <a href="{{ route('admin.senior.archive.list') }}" class="btn" style="background-color: #6B7280; color: white; border: none; border-radius: 8px; padding: 0 1rem; font-size: .875rem; height: 38px; display: flex; align-items: center;">
                                    <i class="fas fa-times me-1"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Archive Table -->
            <div class="card p-0 animate-fade-in" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                <div class="d-flex justify-content-between align-items-center p-4 pb-2" style="flex-shrink: 0;">
                    <div>
                        <h6 class="mb-1" style="font-weight: 700; color: var(--text);">Archived Records</h6>
                        <span class="text-muted small">Showing {{ $archivedSeniors->firstItem() ?? 0 }}–{{ $archivedSeniors->lastItem() ?? 0 }} of {{ $archivedSeniors->total() }} records</span>
                    </div>
                </div>
                <div class="table-responsive" style="flex: 1; overflow-y: auto; min-height: 0;">
                    <table class="table" id="archiveTable" style="margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; z-index: 1; background: var(--cards);">
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" style="cursor: pointer;"></th>
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
                                <td><input type="checkbox" class="senior-checkbox" data-id="{{ $senior->id }}" onchange="updateBulkActions()" style="cursor: pointer;"></td>
                                <td style="color: #9CA3AF; font-weight: 600;">{{ $archivedSeniors->firstItem() + $index }}</td>
                                <td><strong>{{ $senior->control_number ?? '-' }}</strong></td>
                                <td>
                                    <div style="font-weight: 600;">{{ $senior->full_name ?? '-' }}</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">{{ $senior->address ? \Illuminate\Support\Str::limit($senior->address, 35) : '' }}</div>
                                </td>
                                <td>
                                    @if($senior->barangay)
                                        <span class="badge" style="background: rgba(107, 114, 128, 0.1); color: #6B7280; font-weight: 500; padding: 0.35rem 0.65rem; font-size: 0.8rem;">{{ $senior->barangay }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($senior->sex)
                                        <span class="badge" style="background: #6B7280; color: white; border-radius: 50%; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; padding: 0; font-size: 0.7rem; font-weight: 700; margin-right: 0.3rem;">{{ $senior->sex == 'Male' ? 'M' : 'F' }}</span>
                                    @endif
                                    <strong>{{ $senior->age ?? '-' }}</strong>
                                </td>
                                <td>
                                    @if($senior->birth_date)
                                        {{ \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size: 0.82rem;">
                                        {{ $senior->updated_at ? \Carbon\Carbon::parse($senior->updated_at)->format('M d, Y') : '-' }}
                                    </span>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <span class="badge-archived">Archived</span>
                                </td>
                                <td style="vertical-align: middle;">
                                    <!-- Restore Button -->
                                    <form method="POST" action="{{ route('admin.senior.unarchive', $senior->id) }}" id="restore-form-{{ $senior->id }}" style="display: inline;">
                                        @csrf
                                        <button type="button"
                                            class="btn btn-sm"
                                            style="background-color: rgba(20, 184, 166, 0.1); color: #0f766e; border: 1px solid rgba(20, 184, 166, 0.3); border-radius: 6px; padding: 0.35rem 0.6rem; font-size: 0.8rem;"
                                            onclick="confirmRestore({{ $senior->id }}, '{{ addslashes($senior->full_name) }}')"
                                            title="Restore to Active">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-archive"></i>
                                        <h5>No Archived Records</h5>
                                        <p>There are no archived senior citizens at the moment. Archived records from the masterlist will appear here.</p>
                                        <a href="/admin/senior/masterlist" class="btn btn-sm mt-2" style="background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600;">
                                            <i class="fas fa-list me-1"></i> Go to Masterlist
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
                <div class="d-flex justify-content-center mt-4 pb-3">
                    {{ $archivedSeniors->appends(['barangay' => request('barangay'), 'search' => request('search')])->links('vendor.pagination.custom') }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div class="modal fade" id="bulkActionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.12);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border); background: var(--accent); color: var(--primary-dark); border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-tasks me-2"></i>Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <button type="button" class="btn" style="background: #0f766e; color: white; border: none; border-radius: 8px; padding: 12px 20px; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 10px;" onclick="bulkRestore()">
                            <i class="fas fa-undo"></i> Restore Selected
                        </button>
                        <button type="button" class="btn" style="background: #1A237E; color: white; border: none; border-radius: 8px; padding: 12px 20px; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 10px;" onclick="bulkExport()">
                            <i class="fas fa-download"></i> Export Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

            const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
            modal.show();
        }

        function bulkRestore() {
            const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

            if (ids.length === 0) {
                Swal.fire('No Selection', 'Please select at least one record.', 'warning');
                return;
            }

            // Close the modal first
            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkActionModal'));
            modal.hide();

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
                    // Send AJAX request to bulk restore
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

            // Close the modal first
            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkActionModal'));
            modal.hide();

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
                    // Redirect to export with IDs
                    window.location.href = `/admin/senior/export?ids=${ids.join(',')}`;
                }
            });
        }

        // Show popup for restore success/error
        document.addEventListener('DOMContentLoaded', function() {
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

        function confirmRestore(id, name) {
            Swal.fire({
                title: 'Restore Senior?',
                html: `Are you sure you want to restore <strong>${name}</strong> back to active status?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '<i class="fas fa-undo me-1"></i> Yes, Restore',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('restore-form-' + id).submit();
                }
            });
        }
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
