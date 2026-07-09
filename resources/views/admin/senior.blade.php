<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1A237E;
            --primary-dark: #121858;
            --secondary: #374151;
            --accent: #FBC02D;
            --danger: #D32F2F;
            --background: #F1F5F9;
            --cards: #FFFFFF;
            --text: #111827;
            --text-muted: #4B5563;
            --sidebar-bg: #1A237E;
            --border: #D1D5DB;
        }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 0;
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
            color: rgba(255,255,255,.85);
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
            background: rgba(255,255,255,.15);
            color: #fff;
        }
        .sidebar-menu a.active {
            background: rgba(255,255,255,.15);
            color: #fff;
            border-left-color: var(--accent);
        }
        .sidebar-menu a i { width: 20px; text-align: center; font-size: .95rem; }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
        }

        .stat-card {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-icon.primary { background-color: rgba(37, 99, 235, 0.1); color: var(--primary); }
        .stat-icon.warning { background-color: rgba(245, 158, 11, 0.1); color: var(--accent); }
        .stat-icon.success { background-color: rgba(20, 184, 166, 0.1); color: var(--secondary); }
        .stat-icon.info { background-color: rgba(37, 99, 235, 0.1); color: var(--primary); }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin: 0 0 0.5rem 0;
            font-weight: 500;
        }

        /* Table */
        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #E2E8F0;
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem;
            color: var(--text);
            border-bottom: 2px solid var(--border);
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            color: var(--text);
            border-bottom: 1px solid var(--border);
        }

        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-active { background-color: rgba(20, 184, 166, 0.15); color: #0D9488; }
        .badge-pending { background-color: rgba(245, 158, 11, 0.15); color: #D97706; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
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
            <li><a href="/admin/senior" class="active"><i class="fas fa-user-friends"></i> Dashboard</a></li>
            <li><a href="/admin/senior/registration"><i class="fas fa-user-plus"></i> Registration</a></li>
            <li><a href="/admin/senior/masterlist"><i class="fas fa-list"></i> Masterlist</a></li>
            <li><a href="/admin/senior/birthdays"><i class="fas fa-birthday-cake"></i> Birthday Beneficiaries</a></li>
            <li><a href="/admin/senior/birthday-payouts"><i class="fas fa-money-bill-wave"></i> Birthday Payouts</a></li>
            <li><a href="/admin/senior/birthday-payouts/history"><i class="fas fa-history"></i> Payout History</a></li>
            <li><a href="/admin/senior/statistics"><i class="fas fa-chart-bar"></i> Statistics</a></li>
            <li><a href="/admin/senior/reports"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="/admin/senior/archive"><i class="fas fa-archive"></i> Archive</a></li>

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
                    <h5 class="mb-0 me-4">Senior Citizen Dashboard</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-4 text-muted small" id="currentDateTime"></div>
                    <div class="activity-avatar" style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <!-- Summary Cards -->
            @php
                use App\Models\Senior\SeniorCitizenRecord;
                $bdayToday = SeniorCitizenRecord::on('mswdo_senior')->where('status','active')->whereNotNull('birth_date')->whereRaw("MONTH(birth_date) = ? AND DAY(birth_date) = ?", [now()->format('n'), now()->format('j')])->count();
                $bdayWeek = SeniorCitizenRecord::on('mswdo_senior')->where('status','active')->whereNotNull('birth_date')->where(function($q){ $s=now();$e=now()->addDays(7);$sMD=$s->format('m-d');$eMD=$e->format('m-d');if($sMD<=$eMD){$q->whereRaw("DATE_FORMAT(birth_date,'%m-%d') BETWEEN ? AND ?",[$sMD,$eMD]);}else{$q->whereRaw("DATE_FORMAT(birth_date,'%m-%d') >= ?",[$sMD])->orWhereRaw("DATE_FORMAT(birth_date,'%m-%d') <= ?",[$eMD]);}})->count();
                $bdayNextMonth = SeniorCitizenRecord::on('mswdo_senior')->where('status','active')->whereNotNull('birth_date')->whereRaw("MONTH(birth_date) = ?", [now()->addMonth()->format('n')])->count();
            @endphp
            <div class="row g-3 mb-4">
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <div class="card animate-fade-in" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(37, 99, 235, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Total Seniors</p>
                                <h3 class="counter" data-target="{{ $totalSeniors }}" style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $totalSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <div class="card animate-fade-in delay-1" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(16, 185, 129, 0.1); color: #10B981; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Active Seniors</p>
                                <h3 class="counter" data-target="{{ $activeSeniors }}" style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $activeSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <a href="/admin/senior/birthdays" style="text-decoration: none;">
                        <div class="card animate-fade-in delay-2" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(220, 38, 38, 0.1); color: #DC2626; flex-shrink: 0; margin-right: 0.75rem;">
                                    <i class="fas fa-birthday-cake"></i>
                                </div>
                                <div style="flex: 1;">
                                    <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Today's Birthdays</p>
                                    <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $bdayToday }}</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <a href="/admin/senior/birthdays" style="text-decoration: none;">
                        <div class="card animate-fade-in delay-3" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(245, 158, 11, 0.1); color: #F59E0B; flex-shrink: 0; margin-right: 0.75rem;">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <div style="flex: 1;">
                                    <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Next 7 Days</p>
                                    <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $bdayWeek }}</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <a href="/admin/senior/birthdays" style="text-decoration: none;">
                        <div class="card animate-fade-in delay-4" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(26, 35, 126, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 0.75rem;">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div style="flex: 1;">
                                    <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Next Month</p>
                                    <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $bdayNextMonth }}</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Dashboard Insights -->
            <div class="row g-4 mb-4">
                <!-- Top Barangays -->
                <div class="col-lg-6">
                    <div class="card animate-fade-in" style="padding: 1.25rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">
                                <i class="fas fa-building me-2" style="color: var(--primary);"></i>Top Barangays
                            </h6>
                            <button class="btn btn-sm" style="background: rgba(26, 35, 126, 0.1); color: var(--primary); border: none; border-radius: 6px; font-size: 0.7rem; padding: 0.25rem 0.5rem;" data-bs-toggle="modal" data-bs-target="#barangayModal">
                                View All
                            </button>
                        </div>
                        <div id="topBarangaysList" style="max-height: 280px; overflow-y: auto;">
                            <!-- Top barangays will be rendered here -->
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="col-lg-6">
                    <div class="card animate-fade-in delay-1" style="padding: 1.25rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">
                                <i class="fas fa-history me-2" style="color: var(--primary);"></i>Recent Activities
                            </h6>
                            <button type="button" class="btn btn-sm" style="background: rgba(220, 38, 38, 0.1); color: #DC2626; border: none; border-radius: 6px; font-size: 0.7rem; padding: 0.25rem 0.5rem;" onclick="confirmClearActivities()">
                                Clear
                            </button>
                        </div>
                        <div style="max-height: 280px; overflow-y: auto;">
                            @if(count($recentActivities) > 0)
                                @foreach($recentActivities as $activity)
                                <div class="d-flex align-items-start mb-3 pb-3" style="border-bottom: 1px solid var(--border);">
                                    <div style="width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; background: rgba(26, 35, 126, 0.1); color: var(--primary); flex-shrink: 0; margin-right: 0.75rem;">
                                        <i class="fas fa-{{ $activity['action'] == 'registered' ? 'user-plus' : ($activity['action'] == 'archived' ? 'archive' : ($activity['action'] == 'restored' ? 'undo' : 'id-card')) }}"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 0.8rem; color: var(--text);">
                                            {{ ucfirst($activity['action']) }} <strong>{{ $activity['name'] }}</strong>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            <span>{{ $activity['identifier'] }}</span> • {{ $activity['timestamp'] }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: #D1D5DB;"></i>
                                    <span style="font-size: 0.8rem;">No recent activities</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Senior Citizen Records -->
            <div class="row mb-4" style="flex: 1; min-height: 0;">
                <div class="col-12" style="height: 100%; display: flex; flex-direction: column;">
                    <div class="card p-0 animate-fade-in" style="overflow: hidden; flex: 1; display: flex; flex-direction: column; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="d-flex justify-content-between align-items-center p-3 pb-0" style="flex-shrink: 0;">
                            <div>
                                <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">Recent Senior Citizen Records</h6>
                                <span class="text-muted" style="font-size: 0.75rem;">Latest {{ $recentSeniors->count() }} registered seniors</span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <span class="input-group-text bg-white border-end-0" style="border-color: var(--border); padding: 0.375rem 0.5rem;">
                                        <i class="fas fa-search text-muted" style="font-size: 0.7rem;"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Search..." style="border-color: var(--border); font-size: 0.75rem; box-shadow: none; padding: 0.375rem 0.5rem;" onkeyup="filterSeniorTable(this.value)" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                                </div>
                                <button class="btn btn-sm" style="background-color: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 0.75rem; padding: 0.375rem 0.75rem;" onclick="window.location.href='/admin/senior/registration'">
                                    <i class="fas fa-plus me-1" style="font-size: 0.7rem;"></i> Add New
                                </button>
                            </div>
                        </div>
                        <div class="p-0" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
                            <div class="table-responsive" style="flex: 1; overflow-y: auto; min-height: 0;">
                                <table class="table table-hover" id="seniorTable" style="margin-bottom: 0; font-size: 0.8rem;">
                                    <thead style="position: sticky; top: 0; z-index: 1; background: var(--cards);">
                                        <tr>
                                            <th style="font-size: 0.75rem; font-weight: 600;">#</th>
                                            <th style="font-size: 0.75rem; font-weight: 600;">Control No.</th>
                                            <th style="font-size: 0.75rem; font-weight: 600;">Full Name</th>
                                            <th style="font-size: 0.75rem; font-weight: 600;">Barangay</th>
                                            <th style="font-size: 0.75rem; font-weight: 600;">Sex / Age</th>
                                            <th style="font-size: 0.75rem; font-weight: 600;">Birth Date</th>
                                            <th style="font-size: 0.75rem; font-weight: 600;">Contact</th>
                                            <th style="font-size: 0.75rem; font-weight: 600;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSeniors as $index => $senior)
                                        <tr>
                                            <td style="color: #9CA3AF; font-weight: 600;">{{ $index + 1 }}</td>
                                            <td><strong>{{ $senior->control_number ?? $senior->record_number ?? '-' }}</strong></td>
                                            <td>
                                                <div style="font-weight: 600;">{{ $senior->full_name ?? '-' }}</div>
                                                <div class="text-muted" style="font-size: 0.8rem;">{{ $senior->address ? \Illuminate\Support\Str::limit($senior->address, 30) : '' }}</div>
                                            </td>
                                            <td>
                                                @if($senior->barangay)
                                                    <span class="badge" style="background: rgba(26, 35, 126, 0.1); color: var(--primary); font-weight: 500; padding: 0.35rem 0.65rem; font-size: 0.8rem;">{{ $senior->barangay }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($senior->sex)
                                                    <span class="badge" style="background: var(--primary); color: white; border-radius: 50%; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; padding: 0; font-size: 0.7rem; font-weight: 700; margin-right: 0.3rem;">{{ $senior->sex == 'Male' ? 'M' : 'F' }}</span>
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
                                                @if($senior->contact_number)
                                                    <a href="tel:{{ $senior->contact_number }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">{{ $senior->contact_number }}</a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.senior.id-card', $senior->id) }}" class="btn btn-sm" style="background-color: var(--accent); color: var(--primary-dark); border: none; border-radius: 6px; padding: 0.35rem 0.6rem; font-size: 0.8rem;" title="ID Card">
                                                    <i class="fas fa-id-card"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.75rem; color: #D1D5DB;"></i>
                                                No senior citizen records found.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($recentSeniors->count() > 0)
                        <div style="border-top: 1px solid var(--border); padding: 0.75rem 1.5rem; background: #F9FAFB; text-align: right;">
                            <a href="/admin/senior/masterlist" style="text-decoration: none; color: var(--primary); font-weight: 600; font-size: 0.85rem; transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                                View All Records <i class="fas fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barangay Distribution Modal -->
    <div class="modal fade" id="barangayModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.12);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border); background: var(--accent); color: var(--primary-dark); border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-building me-2"></i>All Barangays Distribution</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="barangayModalCards" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; max-height: 600px; overflow-y: auto;">
                        <!-- All barangay cards will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Senior table filter
        function filterSeniorTable(value) {
            const filter = value.toLowerCase();
            const rows = document.querySelectorAll('#seniorTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }

        // Toggle Sidebar
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Update Date Time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Counter Animation
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            updateCounter();
        });

        // Welcome popup on page load - only show if just logged in
        document.addEventListener('DOMContentLoaded', function() {
            @if($justLoggedIn ?? false)
                const adminName = '{{ session('admin_user_name') ?? 'Admin' }}';
                Swal.fire({
                    title: 'Welcome Admin!',
                    text: adminName,
                    icon: 'success',
                    confirmButtonColor: '#1A237E',
                    confirmButtonText: 'Continue',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    },
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Initialize Barangay Distribution Chart
            initBarangayChart();
        });

        // Barangay Distribution Chart
        let barangayChart = null;
        let showingAll = false;

        function initBarangayChart() {
            const barangayData = @json($barangayDistribution);

            // Sort by count (highest to lowest)
            const sortedData = [...barangayData].sort((a, b) => b.count - a.count);

            renderTopBarangaysList(sortedData.slice(0, 10));
        }

        function renderTopBarangaysList(data) {
            const container = document.getElementById('topBarangaysList');
            if (!container) return;

            const totalCount = data.reduce((sum, item) => sum + item.count, 0);

            container.innerHTML = data.map((item, index) => {
                const percentage = totalCount > 0 ? ((item.count / totalCount) * 100).toFixed(1) : 0;
                const barColor = index === 0 ? '#1A237E' :
                               index === 1 ? '#3B82F6' :
                               index === 2 ? '#6366F1' :
                               '#9CA3AF';

                return `
                    <div style="display: flex; align-items: center; margin-bottom: 0.75rem;">
                        <div style="width: 24px; font-size: 0.75rem; color: #6B7280; font-weight: 600; flex-shrink: 0;">${index + 1}.</div>
                        <div style="flex: 1; margin-left: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                <span style="font-size: 0.8rem; font-weight: 500; color: var(--text);">${item.barangay}</span>
                                <span style="font-size: 0.8rem; font-weight: 600; color: var(--primary);">${item.count}</span>
                            </div>
                            <div style="height: 4px; background: #E5E7EB; border-radius: 2px; overflow: hidden;">
                                <div style="height: 100%; background: ${barColor}; border-radius: 2px; width: ${percentage}%"></div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Render all barangay cards in modal
        function renderModalCards() {
            const container = document.getElementById('barangayModalCards');
            if (!container) return;

            const barangayData = @json($barangayDistribution);
            const sortedData = [...barangayData].sort((a, b) => b.count - a.count);

            container.innerHTML = sortedData.map((item, index) => {
                const bgColor = index === 0 ? 'rgba(26, 35, 126, 0.1)' :
                               index === 1 ? 'rgba(59, 130, 246, 0.1)' :
                               index === 2 ? 'rgba(99, 102, 241, 0.1)' :
                               'rgba(107, 114, 128, 0.05)';
                const textColor = index < 3 ? 'var(--primary)' : 'var(--text)';
                const borderColor = index < 3 ? 'var(--primary)' : 'var(--border)';

                return `
                    <div style="background: ${bgColor}; border: 1px solid ${borderColor}; border-radius: 8px; padding: 1rem; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; margin-bottom: 0.25rem;">${item.barangay}</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: ${textColor};">${item.count}</div>
                    </div>
                `;
            }).join('');
        }

        // Initialize modal cards when modal is shown
        document.addEventListener('DOMContentLoaded', function() {
            const barangayModal = document.getElementById('barangayModal');
            if (barangayModal) {
                barangayModal.addEventListener('shown.bs.modal', function() {
                    renderModalCards();
                });
            }
        });
    </script>

    <!-- Hidden form for secure POST logout -->
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function confirmClearActivities() {
            Swal.fire({
                title: 'Clear Recent Activities?',
                text: 'This will remove all recent activity logs. This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, clear all',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form via AJAX
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.senior.clear-activities') }}';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

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
