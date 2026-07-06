<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizen Masterlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
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
            --muted: #6B7280;
            --sidebar-bg: #1A237E;
            --border: #E5E7EB;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
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
        .sidebar-menu a i { width: 20px; text-align: center; font-size: .95rem; }

        /* Main content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top-bar */
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

        /* Page body */
        .page-body { padding: 2rem; flex: 1; }

        /* Table Card */
        .table-card {
            background: var(--cards);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background-color: var(--background);
            border-bottom: 2px solid var(--border);
            font-weight:600;
            font-size: .85rem;
            color: var(--text);
            padding: .75rem;
        }
        .table tbody td {
            border-bottom: 1px solid var(--border);
            padding: .75rem;
            font-size: .875rem;
            color: var(--text);
        }
        .table tbody tr:hover {
            background-color: var(--background);
        }

        .badge {
            padding: .35rem .65rem;
            font-size: .75rem;
            font-weight: 600;
            border-radius: 6px;
        }
        .badge-active {
            background-color: #DCFCE7;
            color: #166534;
        }
        .badge-pending {
            background-color: #FEF3C7;
            color: #92400E;
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
            color: var(--muted);
            background-color: var(--background);
            border-color: var(--border);
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .page-body { padding: 1rem; }
            .table-responsive {
                font-size: .75rem;
            }
        }
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
        <!-- <li><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li> -->
        <li><a href="/admin/senior"><i class="fas fa-user-friends"></i> Dashboard</a></li>
        <li><a href="/admin/senior/registration"><i class="fas fa-user-plus"></i> Registration</a></li>
        <li><a href="/admin/senior/masterlist" class="active"><i class="fas fa-list"></i> Masterlist</a></li>
        <li><a href="/admin/senior/birthdays"><i class="fas fa-birthday-cake"></i> Birthday Beneficiaries</a></li>
        <li><a href="/admin/senior/statistics"><i class="fas fa-chart-bar"></i> Statistics</a></li>
        <li><a href="/admin/senior/reports"><i class="fas fa-file-alt"></i> Reports</a></li>
        
        <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<!-- ======================== MAIN ======================== -->
<div class="main-content">

    <!-- Top-bar -->
    <nav class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <p class="page-title">Senior Citizen Masterlist</p>
                <p class="breadcrumb-nav">
                    <a href="/admin/senior">Dashboard</a> / Masterlist
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <div id="currentDateTime" class="text-muted small d-none d-md-block"></div>
            <div style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%; margin-left: 1rem;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
        </div>
    </nav>

    <!-- Page Body -->
    <div class="page-body">

        <!-- Table Card -->
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h5 fw-bold mb-0">Registered Senior Citizens</h2>
                <a href="/admin/senior/registration" class="btn btn-primary" style="background-color: var(--primary); border: none; border-radius: 8px; padding: .5rem 1rem; font-size: .875rem;">
                    <i class="fas fa-plus me-2"></i>Add New
                </a>
            </div>

            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" action="{{ route('admin.senior.masterlist') }}" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small text-muted fw-semibold mb-1">Search by Name</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}" style="border-right: none;">
                                    <button type="submit" style="background-color: var(--primary); color: white; border: none; padding: 0 1rem; border-radius: 0 6px 6px 0; cursor: pointer;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-semibold mb-1">Filter by Barangay</label>
                                <select class="form-select" name="barangay" onchange="this.form.submit()">
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
                            <div class="col-md-2 d-flex gap-2 align-items-end">
                                @if(request('search') || request('barangay'))
                                    <a href="{{ route('admin.senior.masterlist') }}" class="btn" style="background-color: #6B7280; color: white; border: none; border-radius: 8px; padding: .5rem 1rem; font-size: .875rem; width: 100%;">
                                        <i class="fas fa-times me-1"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($seniors->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th style="width: 12%;">Control No</th>
                                <th style="width: 18%;">Full Name</th>
                                <th style="width: 15%;">Barangay</th>
                                <th style="width: 12%;">Status</th>
                                <th style="width: 20%;">Address</th>
                                <th style="width: 8%;">Age</th>
                                <th style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seniors as $senior)
                                <tr>
                                    <td style="word-wrap: break-word;"><strong>{{ $senior->control_number ?? '-' }}</strong></td>
                                    <td style="word-wrap: break-word;">{{ $senior->full_name ?? '-' }}</td>
                                    <td style="word-wrap: break-word;">{{ $senior->barangay ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $senior->status == 'active' ? 'badge-active' : 'badge-pending' }}">
                                            {{ ucfirst($senior->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td style="word-wrap: break-word;">{{ $senior->address ?? '-' }}</td>
                                    <td style="word-wrap: break-word;">{{ $senior->age ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm view-senior-btn" style="background-color: var(--primary); color: white; border: none; border-radius: 6px; padding: 0.35rem 0.6rem; font-size: 0.8rem;"
                                                data-id="{{ $senior->id }}"
                                                data-control-number="{{ $senior->control_number }}"
                                                data-full-name="{{ $senior->full_name }}"
                                                data-address="{{ $senior->address }}"
                                                data-barangay="{{ $senior->barangay }}"
                                                data-birth-date="{{ $senior->birth_date ? date('M d, Y', strtotime($senior->birth_date)) : '' }}"
                                                data-month="{{ $senior->month }}"
                                                data-age="{{ $senior->age }}"
                                                data-sex="{{ $senior->sex }}"
                                                data-contact-number="{{ $senior->contact_number }}"
                                                data-philsys-number="{{ $senior->philsys_number }}"
                                                data-rrn-number="{{ $senior->rrn_number }}"
                                                data-remarks="{{ $senior->remarks }}"
                                                data-status="{{ ucfirst($senior->status ?? 'pending') }}"
                                                data-year-applied="{{ $senior->year_applied }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm archive-senior-btn" style="background-color: #dc3545; color: white; border: none; border-radius: 6px; padding: 0.35rem 0.6rem; font-size: 0.8rem;"
                                                data-id="{{ $senior->id }}"
                                                data-name="{{ $senior->full_name }}">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $seniors->appends(['barangay' => request('barangay'), 'search' => request('search')])->links('vendor.pagination.custom') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No senior citizens registered yet.</p>
                    <a href="/admin/senior/registration" class="btn btn-primary" style="background-color: var(--primary); border: none; border-radius: 8px; padding: .5rem 1rem; font-size: .875rem;">
                        <i class="fas fa-plus me-2"></i>Register First Senior Citizen
                    </a>
                </div>
            @endif
        </div>

    </div><!-- /page-body -->
</div><!-- /main-content -->

<!-- Senior Citizen Details Modal -->
<div class="modal fade" id="seniorModal" tabindex="-1" aria-labelledby="seniorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--border); background: var(--cards);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border); background: var(--cards);">
                <h5 class="modal-title" id="seniorModalLabel" style="color: var(--text); font-weight: 600;">Senior Citizen Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(0.5);"></button>
            </div>
            <div class="modal-body" style="background: var(--background);">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Control Number</label>
                        <p id="modalControlNumber" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Year Applied</label>
                        <p id="modalYearApplied" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Status</label>
                        <p id="modalStatus" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Full Name</label>
                        <p id="modalFullName" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Address</label>
                        <p id="modalAddress" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Barangay</label>
                        <p id="modalBarangay" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Birth Date</label>
                        <p id="modalBirthDate" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Month</label>
                        <p id="modalMonth" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Age</label>
                        <p id="modalAge" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Sex</label>
                        <p id="modalSex" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Contact Number</label>
                        <p id="modalContactNumber" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">PhilSys Number</label>
                        <p id="modalPhilsysNumber" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">RRN Number</label>
                        <p id="modalRrnNumber" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label style="font-weight: 600; color: var(--muted); font-size: 0.8rem;">Remarks</label>
                        <p id="modalRemarks" style="font-weight: 500; color: var(--text); margin-bottom: 0.5rem; background: var(--cards); padding: 0.4rem; border-radius: 6px; font-size: 0.9rem;">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border); background: var(--cards);">
                <button type="button" class="btn" style="background-color: var(--primary); color: white; border: none; border-radius: 6px; padding: 0.5rem 1rem;" data-bs-dismiss="modal">Close</button>
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
        const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' };
        const el = document.getElementById('currentDateTime');
        if (el) el.textContent = now.toLocaleDateString('en-PH', opts);
    }
    updateDateTime();

    // Event delegation for View and Archive buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-senior-btn')) {
            const button = e.target;
            document.getElementById('modalControlNumber').textContent = button.dataset.controlNumber || '-';
            document.getElementById('modalFullName').textContent = button.dataset.fullName || '-';
            document.getElementById('modalAddress').textContent = button.dataset.address || '-';
            document.getElementById('modalBarangay').textContent = button.dataset.barangay || '-';
            document.getElementById('modalBirthDate').textContent = button.dataset.birthDate || '-';
            document.getElementById('modalMonth').textContent = button.dataset.month || '-';
            document.getElementById('modalAge').textContent = button.dataset.age || '-';
            document.getElementById('modalSex').textContent = button.dataset.sex || '-';
            document.getElementById('modalContactNumber').textContent = button.dataset.contactNumber || '-';
            document.getElementById('modalPhilsysNumber').textContent = button.dataset.philsysNumber || '-';
            document.getElementById('modalRrnNumber').textContent = button.dataset.rrnNumber || '-';
            document.getElementById('modalRemarks').textContent = button.dataset.remarks || '-';
            document.getElementById('modalStatus').textContent = button.dataset.status || '-';
            document.getElementById('modalYearApplied').textContent = button.dataset.yearApplied || '-';

            const modal = new bootstrap.Modal(document.getElementById('seniorModal'));
            modal.show();
        }

        if (e.target.classList.contains('archive-senior-btn')) {
            const button = e.target;
            const seniorId = button.dataset.id;
            const seniorName = button.dataset.name;

            Swal.fire({
                title: 'Archive Senior Citizen',
                text: `Are you sure you want to archive ${seniorName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Archive',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form to archive
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

    setInterval(updateDateTime, 60000);
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
</body>
</html>
