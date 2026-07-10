<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizen Statistics - MSWDO Silang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--background); color: var(--text); margin: 0; padding: 0; }

        /* Sidebar */
        .sidebar { background: var(--sidebar-bg); width: 260px; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; display: flex; flex-direction: column; transition: transform .3s ease; }
        .sidebar-brand { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1); color: #fff; font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; gap: .65rem; }
        .sidebar-brand i { font-size: 1.3rem; color: var(--accent); }
        .sidebar-menu { list-style: none; margin: 0; padding: 1rem 0; flex: 1; }
        .sidebar-menu li { margin-bottom: .2rem; }
        .sidebar-menu a { color: rgba(255,255,255,.75); padding: .75rem 1.5rem; display: flex; align-items: center; gap: .75rem; text-decoration: none; font-size: .9rem; border-left: 3px solid transparent; transition: all .2s ease; }
        .sidebar-menu a:hover { background: rgba(255,255,255,.1); color: var(--accent); }
        .sidebar-menu a.active { background: rgba(255,255,255,.1); color: var(--accent); border-left-color: var(--accent); }
        .sidebar-menu a i { width: 20px; text-align: center; }

        .main-content { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; width: calc(100% - 260px); }
        .top-navbar { background: var(--cards); border-bottom: 1px solid var(--border); padding: 1rem 2rem; position: sticky; top: 0; z-index: 999; flex-shrink: 0; }

        .card { background-color: var(--cards); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08); margin-bottom: 1.5rem; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,0.12); }

        .animate-fade-in { opacity: 0; transform: translateY(12px); animation: fadeIn 0.5s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* Table styles */
        .table-responsive { border-radius: 8px; overflow: hidden; }
        .table { margin-bottom: 0; }
        .table thead th { background: var(--primary); color: white; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; border: none; padding: 0.875rem 1rem; }
        .table tbody td { padding: 0.875rem 1rem; font-size: 0.875rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .table tbody tr:hover { background: rgba(26, 35, 126, 0.02); }
    </style>
</head>
<body>
    <!-- Sidebar -->
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
            <li><a href="/admin/senior/statistics" class="active"><i class="fas fa-chart-bar"></i> Statistics</a></li>
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
                    <h5 class="mb-0 me-4">Statistics & Analytics</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-4 text-muted small" id="currentDateTime"></div>
                    <div class="activity-avatar" style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <!-- Statistics Filters -->
            <div class="card animate-fade-in" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">
                        <i class="fas fa-filter me-2" style="color: var(--primary);"></i>Statistics Filters
                    </h6>
                </div>
                <form id="filterForm" method="GET" action="{{ route('admin.senior.analytics') }}" autocomplete="off">
                    <div class="row g-2 align-items-end">
                        <div class="col-6 col-md-2">
                            <label style="font-size: 0.875rem; color: #6B7280; font-weight: 600; margin-bottom: 0.25rem; display: block;">Year</label>
                            <select name="year" class="form-select" style="font-size: 0.875rem; border-radius: 6px; border-color: var(--border); height: 44px;">
                                <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                                <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ $year == 2024 ? 'selected' : '' }}>2024</option>
                                <option value="2023" {{ $year == 2023 ? 'selected' : '' }}>2023</option>
                                <option value="2022" {{ $year == 2022 ? 'selected' : '' }}>2022</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label style="font-size: 0.875rem; color: #6B7280; font-weight: 600; margin-bottom: 0.25rem; display: block;">Month</label>
                            <select name="month" class="form-select" style="font-size: 0.875rem; border-radius: 6px; border-color: var(--border); height: 44px;">
                                <option value="">All</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label style="font-size: 0.875rem; color: #6B7280; font-weight: 600; margin-bottom: 0.25rem; display: block;">Barangay</label>
                            <select name="barangay" id="barangaySelect" class="form-select" style="font-size: 0.875rem; border-radius: 6px; border-color: var(--border); height: 44px;">
                                <option value="">All</option>
                                @foreach($allBarangays as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label style="font-size: 0.875rem; color: #6B7280; font-weight: 600; margin-bottom: 0.25rem; display: block;">Gender</label>
                            <select name="gender" class="form-select" style="font-size: 0.875rem; border-radius: 6px; border-color: var(--border); height: 44px;">
                                <option value="">All</option>
                                <option value="Male" {{ $gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $gender == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label style="font-size: 0.875rem; color: #6B7280; font-weight: 600; margin-bottom: 0.25rem; display: block;">Age Group</label>
                            <select name="age_group" class="form-select" style="font-size: 0.875rem; border-radius: 6px; border-color: var(--border); height: 44px;">
                                <option value="">All</option>
                                <option value="60-69" {{ $ageGroup == '60-69' ? 'selected' : '' }}>60-69</option>
                                <option value="70-79" {{ $ageGroup == '70-79' ? 'selected' : '' }}>70-79</option>
                                <option value="80-89" {{ $ageGroup == '80-89' ? 'selected' : '' }}>80-89</option>
                                <option value="90-99" {{ $ageGroup == '90-99' ? 'selected' : '' }}>90-99</option>
                                <option value="100+" {{ $ageGroup == '100+' ? 'selected' : '' }}>100+</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-sm flex-grow-1" style="background-color: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                    <i class="fas fa-check me-1"></i> Apply
                                </button>
                                <a href="{{ route('admin.senior.analytics') }}" class="btn btn-sm btn-outline-secondary flex-grow-1" style="border-radius: 6px; font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card animate-fade-in" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(37, 99, 235, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Total Seniors</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $totalSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card animate-fade-in delay-1" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(37, 99, 235, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-male"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Male</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $maleCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card animate-fade-in delay-2" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(236, 72, 153, 0.1); color: #EC4899; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-female"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Female</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $femaleCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card animate-fade-in delay-3" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(16, 185, 129, 0.1); color: #10B981; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Active</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $activeSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card animate-fade-in" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(239, 68, 68, 0.1); color: #EF4444; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Inactive</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $inactiveSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <div class="card animate-fade-in" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(245, 158, 11, 0.1); color: #F59E0B; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Barangays</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $totalBarangays }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row g-3 mb-4">
                <div class="col-lg-8">
                    <div class="card animate-fade-in h-100" style="padding: 1.5rem;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">
                                <i class="fas fa-chart-line me-2" style="color: var(--primary);"></i>Registration Trend - {{ $year }}
                            </h6>
                        </div>
                        <div class="chart-container">
                            <canvas id="registrationChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card animate-fade-in delay-1 h-100" style="padding: 1.5rem;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">
                                <i class="fas fa-chart-pie me-2" style="color: var(--primary);"></i>Gender Distribution
                            </h6>
                        </div>
                        <div class="chart-container">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row g-3 mb-4">
                <div class="col-lg-6">
                    <div class="card animate-fade-in h-100" style="padding: 1.5rem;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">
                                <i class="fas fa-chart-bar me-2" style="color: var(--primary);"></i>Top 10 Barangays
                            </h6>
                            <span class="badge" style="background: rgba(26,35,126,0.1); color: var(--primary); padding: 0.25rem 0.5rem; font-weight: 600; font-size: 0.7rem;">{{ $totalBarangays }} Total</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="barangayChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card animate-fade-in delay-1 h-100" style="padding: 1.5rem;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">
                                <i class="fas fa-chart-area me-2" style="color: var(--primary);"></i>Age Group Distribution
                            </h6>
                        </div>
                        <div class="chart-container">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Section -->
            <div class="card animate-fade-in" style="padding: 1.5rem;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 style="font-weight: 700; font-size: 0.875rem; color: var(--text); margin: 0;">
                        <i class="fas fa-file-export me-2" style="color: var(--primary);"></i>Export Reports
                    </h6>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <button onclick="window.print()" class="btn" style="background-color: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.8rem; padding: 0.625rem 1.25rem;">
                        <i class="fas fa-print me-2"></i>Print Statistics
                    </button>
                    <a href="{{ route('admin.senior.analytics', ['export' => 'pdf'] + request()->except('page')) }}" class="btn" style="background-color: #EF4444; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.8rem; padding: 0.625rem 1.25rem;">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </a>
                    <a href="{{ route('admin.senior.analytics', ['export' => 'excel'] + request()->except('page')) }}" class="btn" style="background-color: #10B981; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.8rem; padding: 0.625rem 1.25rem;">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            if (confirm('Are you sure you want to logout?')) {
                document.getElementById('logout-form').submit();
            }
        }

        // Current date/time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Registration Trend Chart
        const registrationLabels = {!! json_encode($monthlyRegistrations->pluck('month')) !!};
        const registrationValues = {!! json_encode($monthlyRegistrations->pluck('total')) !!};

        new Chart(document.getElementById('registrationChart'), {
            type: 'line',
            data: {
                labels: registrationLabels,
                datasets: [{
                    label: 'Registrations',
                    data: registrationValues,
                    borderColor: '#1A237E',
                    backgroundColor: 'rgba(26, 35, 126, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#1A237E',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' registrations';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(0,0,0,0.06)' } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });

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

        // Top 10 Barangays Chart
        const barangayLabels = {!! json_encode($barangayStats->take(10)->pluck('barangay')) !!};
        const barangayValues = {!! json_encode($barangayStats->take(10)->pluck('total')) !!};
        const barangayColors = barangayValues.map((_, index) => {
            return index === 0 ? 'rgba(26, 35, 126, 0.8)' : 'rgba(107, 114, 128, 0.6)';
        });

        new Chart(document.getElementById('barangayChart'), {
            type: 'bar',
            data: {
                labels: barangayLabels,
                datasets: [{
                    label: 'Senior Citizens',
                    data: barangayValues,
                    backgroundColor: barangayColors,
                    borderRadius: 8,
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
                            label: function(context) {
                                return context.raw + ' seniors';
                            }
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(0,0,0,0.06)' } },
                    y: { grid: { display: false }, ticks: { font: { size: 11, weight: 500 } } }
                }
            }
        });

        // Age Groups Chart
        const ageLabels = {!! json_encode($ageGroups->pluck('age_group')) !!};
        const ageValues = {!! json_encode($ageGroups->pluck('total')) !!};

        new Chart(document.getElementById('ageChart'), {
            type: 'bar',
            data: {
                labels: ageLabels,
                datasets: [{
                    label: 'Seniors',
                    data: ageValues,
                    backgroundColor: 'rgba(26, 35, 126, 0.8)',
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
            document.getElementById('sidebar').classList.toggle('show');
        }
    </script>
</body>
</html>
