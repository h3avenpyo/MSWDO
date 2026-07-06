<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSWDO Admin Dashboard</title>
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
            --sidebar-bg: #1A237E;
            --border: #E5E7EB;
        }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 0;
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

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 0;
            min-height: 100vh;
        }

        .top-navbar {
            background-color: var(--cards);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }



        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Cards */
        .card {
            background-color: var(--cards);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
            color: #6B7280;
            font-size: 0.875rem;
            margin: 0 0 0.5rem 0;
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.75rem;
            margin-top: 0.5rem;
        }

        .stat-change.positive { color: var(--secondary); }
        .stat-change.negative { color: var(--danger); }

        /* Charts */
        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Table */
        .table {
            margin-bottom: 0;
        }

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

        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-high { background-color: rgba(220, 38, 38, 0.1); color: var(--danger); }
        .badge-medium { background-color: rgba(245, 158, 11, 0.1); color: var(--accent); }
        .badge-low { background-color: rgba(20, 184, 166, 0.1); color: var(--secondary); }

        /* Activity Item */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #E5E7EB;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 1rem;
        }

        .activity-content h6 {
            margin: 0;
            font-size: 0.875rem;
        }

        .activity-content p {
            margin: 0;
            color: #6B7280;
            font-size: 0.75rem;
        }

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
            <li><a href="/admin/dashboard" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="/admin/statistics"><i class="fas fa-chart-line"></i> Statistics</a></li>
            <li><a href="#"><i class="fas fa-hand-holding-usd"></i> Financial Assistance</a></li>
            <li><a href="#"><i class="fas fa-user-friends"></i> Senior Citizen</a></li>
            <li><a href="/admin/add-officers"><i class="fas fa-user-shield"></i> Add Officers</a></li>
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
                    <h5 class="mb-0 me-4">Dashboard</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-4 text-muted small" id="currentDateTime"></div>
                    <div class="activity-avatar" style="width: 35px; height: 35px; font-size: 0.875rem;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4">
            <!-- Overview Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card animate-fade-in" style="padding: 0;">
                        <div style="display: flex; align-items: center; padding: 1.5rem;">
                            <div style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background-color: rgba(37, 99, 235, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 1rem;">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <p style="color: #6B7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 500;">Total Cases</p>
                                <h3 class="counter" data-target="{{ $totalCases }}" style="font-size: 2rem; font-weight: 700; margin: 0; line-height: 1;">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card animate-fade-in delay-1" style="padding: 0;">
                        <div style="display: flex; align-items: center; padding: 1.5rem;">
                            <div style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background-color: rgba(245, 158, 11, 0.1); color: #FBC02D; flex-shrink: 0; margin-right: 1rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <p style="color: #6B7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 500;">Pending Cases</p>
                                <h3 class="counter" data-target="{{ $pendingCases }}" style="font-size: 2rem; font-weight: 700; margin: 0; line-height: 1;">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card animate-fade-in delay-2" style="padding: 0;">
                        <div style="display: flex; align-items: center; padding: 1.5rem;">
                            <div style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background-color: rgba(20, 184, 166, 0.1); color: #6B7280; flex-shrink: 0; margin-right: 1rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <p style="color: #6B7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 500;">Resolved Cases</p>
                                <h3 class="counter" data-target="{{ $resolvedCases }}" style="font-size: 2rem; font-weight: 700; margin: 0; line-height: 1;">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card animate-fade-in delay-3" style="padding: 0;">
                        <div style="display: flex; align-items: center; padding: 1.5rem;">
                            <div style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background-color: rgba(37, 99, 235, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 1rem;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <p style="color: #6B7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 500;">Total Users</p>
                                <h3 class="counter" data-target="{{ $totalUsers }}" style="font-size: 2rem; font-weight: 700; margin: 0; line-height: 1;">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Officers Table -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card p-4 animate-fade-in">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0">Officers Directory</h6>
                            <a href="/admin/add-officers" class="btn btn-sm btn-primary">Add Officer</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($officers as $officer)
                                    <tr>
                                        <td>{{ $officer->name }}</td>
                                        <td>{{ $officer->email }}</td>
                                        <td>{{ $officer->role }}</td>
                                        <td>{{ $officer->phone ?? '-' }}</td>
                                        <td>
                                            @if($officer->status == 'active')
                                                <span class="badge badge-low">Active</span>
                                            @else
                                                <span class="badge badge-high">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No officers created yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Distribution -->
            <div class="row mb-4">
                <div class="col-xl-4 mb-4">
                    <div class="card p-4 animate-fade-in">
                        <h6 class="mb-4">Case Distribution</h6>
                        <div class="chart-container">
                            <canvas id="caseDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 mb-4">
                    <div class="card p-4 animate-fade-in delay-1">
                        <h6 class="mb-4">Staff Performance</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Officer Name</th>
                                        <th>Assigned</th>
                                        <th>Completed</th>
                                        <th>Pending</th>
                                        <th>Rate</th>
                                        <th>Avg Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staffPerformance as $staff)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="activity-avatar me-2">{{ substr($staff['name'], 0, 1) }}{{ substr($staff['name'], strpos($staff['name'], ' ') + 1, 1) }}</div>
                                                <span>{{ $staff['name'] }}</span>
                                                @if($staff['rate'] >= 92)
                                                <span class="badge badge-high ms-2">Top</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $staff['assigned'] }}</td>
                                        <td>{{ $staff['completed'] }}</td>
                                        <td>{{ $staff['pending'] }}</td>
                                        <td>{{ $staff['rate'] }}%</td>
                                        <td>{{ $staff['avgTime'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Reports Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card p-4 animate-fade-in">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0">Reports Summary</h6>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-2"><i class="fas fa-file-pdf me-1"></i> PDF</button>
                                <button class="btn btn-sm btn-outline-success me-2"><i class="fas fa-file-excel me-1"></i> Excel</button>
                                <button class="btn btn-sm btn-primary"><i class="fas fa-chart-bar me-1"></i> Analytics</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Cases This Month</small>
                                    <h4 class="mb-0">{{ $reportsSummary['casesThisMonth'] }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Closed This Month</small>
                                    <h4 class="mb-0">{{ $reportsSummary['closedThisMonth'] }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Pending Cases</small>
                                    <h4 class="mb-0">{{ $reportsSummary['pendingCases'] }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Generated Reports</small>
                                    <h4 class="mb-0">{{ $reportsSummary['generatedReports'] }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 bg-light rounded">
                                    <small class="text-muted">Financial Released</small>
                                    <h4 class="mb-0">₱{{ number_format($reportsSummary['financialReleased'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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

        // Case Distribution Pie Chart
        const caseCtx = document.getElementById('caseDistributionChart').getContext('2d');
        new Chart(caseCtx, {
            type: 'pie',
            data: {
                labels: @json(array_keys($caseDistribution)),
                datasets: [{
                    data: @json(array_values($caseDistribution)),
                    backgroundColor: ['#1A237E', '#D32F2F', '#FBC02D', '#1F2937', '#6B7280'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
        });
    </script>

    <!-- Hidden form for secure POST logout -->
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
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
        });

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
