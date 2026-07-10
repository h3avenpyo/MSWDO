<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSWDO Admin - Social Case Study Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
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
        .delay-4 { animation-delay: 0.4s; }

        .page-title { font-size: 1.15rem; font-weight: 700; margin: 0; }
        .page-subtitle { color: var(--text-muted); margin: .35rem 0 0; font-size: .93rem; }
        .btn-icon {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: all .2s ease;
        }
        .btn-icon:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        .page-body { padding: 2rem; flex: 1; }
        .card-body { padding: 1.5rem; }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .quick-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
            padding: 1.5rem;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--cards);
            transition: all .2s ease;
            text-decoration: none;
            color: var(--text);
        }
        .quick-action-btn:hover {
            border-color: var(--primary);
            background: rgba(26,35,126,.05);
            transform: translateY(-2px);
        }
        .quick-action-btn i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        .quick-action-btn span {
            font-size: .85rem;
            font-weight: 600;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-building"></i> MSWDO Admin</div>
        <ul class="sidebar-menu">
            <li><a href="/admin/social-case/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="/admin/social-case"><i class="fas fa-clipboard-list"></i> Eligibility Check</a></li>
            <li><a href="/admin/social-case-studies"><i class="fas fa-file-alt"></i> Case Studies</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-md-none me-3" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 me-4">Social Case Study Dashboard</h5>
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
            <div class="row g-3 mb-4">
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <div class="card animate-fade-in" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(37, 99, 235, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Total Clients</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $totalClients }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <div class="card animate-fade-in delay-1" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(16, 185, 129, 0.1); color: #10B981; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Active Cases</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $activeCases }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <div class="card animate-fade-in delay-2" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(245, 158, 11, 0.1); color: #F59E0B; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Pending Assessment</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $pendingAssessment }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <div class="card animate-fade-in delay-3" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(37, 99, 235, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Approved Cases</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $approvedCases }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-xl col-lg-3 col-md-4">
                    <div class="card animate-fade-in delay-4" style="padding: 1rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div style="display: flex; align-items: center;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem; background-color: rgba(16, 185, 129, 0.1); color: #10B981; flex-shrink: 0; margin-right: 0.75rem;">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                            <div style="flex: 1;">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.15rem 0; font-weight: 500;">Released Assistance</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $releasedAssistance }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">Quick Actions</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="/admin/social-case-eligibility/register" class="quick-action-btn">
                                <i class="fas fa-user-plus"></i>
                                <span>New Intake</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="/admin/social-case" class="quick-action-btn">
                                <i class="fas fa-search"></i>
                                <span>Search Client</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('admin.social-case-studies.index') }}" class="quick-action-btn">
                                <i class="fas fa-list"></i>
                                <span>View Pending Cases</span>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="#" class="quick-action-btn">
                                <i class="fas fa-chart-bar"></i>
                                <span>Generate Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row g-4 mb-4">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">Monthly Social Case Study Requests</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="monthlyRequestsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">Assistance by Purpose</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="assistanceByPurposeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">Assistance by Barangay</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="assistanceByBarangayChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">Latest Encoded Cases</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestEncodedCases as $case)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $case->client->full_name }}</div>
                                                    <div class="text-secondary small">{{ $case->case_number }}</div>
                                                </td>
                                                <td>
                                                    <div class="small">{{ $case->created_at->format('M d, Y') }}</div>
                                                    <span class="badge-pill {{ $case->status == 'Open' ? 'success' : 'warning' }}">{{ $case->status }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-secondary">No cases found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">Latest Approved Cases</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestApprovedCases as $case)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $case->client->full_name }}</div>
                                                    <div class="text-secondary small">{{ $case->assistance_type }}</div>
                                                </td>
                                                <td>
                                                    <div class="small">{{ $case->created_at->format('M d, Y') }}</div>
                                                    <span class="badge-pill info">{{ $case->status }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-secondary">No approved cases</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">Latest Released Assistance</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestReleasedAssistance as $case)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $case->client->full_name }}</div>
                                                    <div class="text-secondary small">{{ $case->assistance_type }}</div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">₱{{ number_format($case->amount, 2) }}</div>
                                                    <div class="small">{{ $case->release_date ? $case->release_date->format('M d, Y') : 'N/A' }}</div>
                                                    <span class="badge-pill success">{{ $case->status }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-secondary">No released assistance</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

        // Monthly Requests Chart
        const monthlyRequestsCtx = document.getElementById('monthlyRequestsChart').getContext('2d');
        new Chart(monthlyRequestsCtx, {
            type: 'line',
            data: {
                labels: {{ $monthlyRequests->pluck('month') }},
                datasets: [{
                    label: 'Case Studies',
                    data: {{ $monthlyRequests->pluck('count') }},
                    borderColor: '#1A237E',
                    backgroundColor: 'rgba(26, 35, 126, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Assistance by Purpose Chart
        const assistanceByPurposeCtx = document.getElementById('assistanceByPurposeChart').getContext('2d');
        new Chart(assistanceByPurposeCtx, {
            type: 'doughnut',
            data: {
                labels: {{ $assistanceByPurpose->pluck('assistance_type') }},
                datasets: [{
                    data: {{ $assistanceByPurpose->pluck('count') }},
                    backgroundColor: [
                        '#1A237E',
                        '#22C55E',
                        '#F59E0B',
                        '#3B82F6',
                        '#D32F2F',
                        '#6B7280'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Assistance by Barangay Chart
        const assistanceByBarangayCtx = document.getElementById('assistanceByBarangayChart').getContext('2d');
        new Chart(assistanceByBarangayCtx, {
            type: 'bar',
            data: {
                labels: {{ $assistanceByBarangay->pluck('barangay') }},
                datasets: [{
                    label: 'Assistance Count',
                    data: {{ $assistanceByBarangay->pluck('count') }},
                    backgroundColor: '#1A237E'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // Logout confirmation
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

    <!-- Hidden form for secure POST logout -->
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
