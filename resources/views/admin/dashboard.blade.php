<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSWDO Admin Dashboard</title>
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

        *, *::before, *::after { box-sizing: border-box; }
        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 0;
        }

        /* ── Sidebar Overlay ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            -webkit-backdrop-filter: blur(2px);
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.active { display: block; }

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
        .sidebar-menu a:hover { background: rgba(255,255,255,.1); color: var(--accent); }
        .sidebar-menu a.active { background: rgba(255,255,255,.1); color: var(--accent); border-left-color: var(--accent); }
        .sidebar-menu a i { width: 20px; text-align: center; font-size: .95rem; }

        /* ── Main Content ── */
        .main-content {
            margin-left: 260px;
            padding: 0;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
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

        /* ── Cards ── */
        .card {
            background-color: var(--cards);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

        /* ── Stat Cards Grid ── */
        .stat-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-content { display: flex; flex-direction: column; justify-content: center; }
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
        .stat-value { font-size: 2rem; font-weight: 700; margin: 0; line-height: 1; }
        .stat-label { color: #6B7280; font-size: 0.875rem; margin: 0 0 0.5rem 0; font-weight: 500; }
        .stat-change { font-size: 0.75rem; margin-top: 0.5rem; }
        .stat-change.positive { color: var(--secondary); }
        .stat-change.negative { color: var(--danger); }

        /* ── Charts ── */
        .chart-container { position: relative; height: 300px; }

        /* ── Table ── */
        .table { margin-bottom: 0; }
        .table th {
            background-color: var(--background);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem;
        }
        .table td { padding: 1rem; vertical-align: middle; }

        .badge { padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
        .badge-high { background-color: rgba(220, 38, 38, 0.1); color: var(--danger); }
        .badge-medium { background-color: rgba(245, 158, 11, 0.1); color: var(--accent); }
        .badge-low { background-color: rgba(20, 184, 166, 0.1); color: var(--secondary); }

        /* ── Activity ── */
        .activity-item { display: flex; align-items: center; padding: 1rem; border-bottom: 1px solid #E5E7EB; }
        .activity-item:last-child { border-bottom: none; }
        .activity-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background-color: var(--primary); color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; margin-right: 1rem; flex-shrink: 0;
        }
        .activity-content h6 { margin: 0; font-size: 0.875rem; }
        .activity-content p { margin: 0; color: #6B7280; font-size: 0.75rem; }

        /* ── Animations ── */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.5s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ══════════════════════════════════════════════ */

        /* ── Large Desktop (≥ 1536px) ── */
        @media (min-width: 1536px) {
            .stat-cards-grid { gap: 2rem; }
        }

        /* ── Tablet Landscape / Small Laptop (< 1024px) ── */
        @media (max-width: 1023px) {
            .sidebar { transform: translateX(-100%); z-index: 1001; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; }
            .stat-cards-grid { grid-template-columns: repeat(2, 1fr); }
            .top-navbar { padding: 1rem 1.25rem; }
            .reports-actions { flex-wrap: wrap; gap: 0.5rem; }
        }

        /* ── Tablet Portrait (< 768px) ── */
        @media (max-width: 767px) {
            .top-navbar { padding: 0.75rem 1rem; }
            .navbar-title { font-size: 0.95rem; }
            .navbar-datetime { display: none; }
            .stat-cards-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
            .stat-card { padding: 1rem; }
            .stat-icon { width: 44px; height: 44px; font-size: 1.1rem; }
            .stat-value { font-size: 1.5rem; }
            .stat-label { font-size: 0.75rem; }
            .chart-container { height: 250px; }
            .card-body { padding: 1rem; }
            .card-header { padding: 1rem; font-size: 1rem; }
            .table th, .table td { padding: 0.625rem 0.75rem; font-size: 0.8125rem; }
            .reports-actions .btn { font-size: 0.75rem; padding: 0.375rem 0.625rem; }
            .reports-summary-grid { grid-template-columns: repeat(2, 1fr); }
        }

        /* ── Mobile (< 480px) ── */
        @media (max-width: 479px) {
            .stat-cards-grid { grid-template-columns: 1fr; }
            .stat-card { flex-direction: column; text-align: center; gap: 0.75rem; }
            .top-navbar h5 { font-size: 0.875rem; }
            .reports-summary-grid { grid-template-columns: 1fr; }
            .reports-actions { justify-content: center; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-building"></i>
            <span>MSWDO Admin</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/dashboard" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-hand-holding-usd"></i> Financial Assistance</a></li>
            <li><a href="#"><i class="fas fa-user-friends"></i> Senior Citizen</a></li>
            <li><a href="/admin/add-officers"><i class="fas fa-user-shield"></i> Add Officers</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-lg-none me-3" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 me-4">Dashboard</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-4 text-muted small navbar-datetime" id="currentDateTime"></div>
                    <div class="activity-avatar" style="width: 35px; height: 35px; font-size: 0.875rem;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4">
            <!-- Overview Cards -->
            <div class="stat-cards-grid">
                <div class="card animate-fade-in" style="padding: 0;">
                    <div class="stat-card">
                        <div class="stat-icon primary"><i class="fas fa-folder"></i></div>
                        <div class="stat-content">
                            <p class="stat-label">Total Cases</p>
                            <h3 class="counter stat-value" data-target="{{ $totalCases }}">0</h3>
                        </div>
                    </div>
                </div>
                <div class="card animate-fade-in delay-1" style="padding: 0;">
                    <div class="stat-card">
                        <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                        <div class="stat-content">
                            <p class="stat-label">Pending Cases</p>
                            <h3 class="counter stat-value" data-target="{{ $pendingCases }}">0</h3>
                        </div>
                    </div>
                </div>
                <div class="card animate-fade-in delay-2" style="padding: 0;">
                    <div class="stat-card">
                        <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-content">
                            <p class="stat-label">Resolved Cases</p>
                            <h3 class="counter stat-value" data-target="{{ $resolvedCases }}">0</h3>
                        </div>
                    </div>
                </div>
                <div class="card animate-fade-in delay-3" style="padding: 0;">
                    <div class="stat-card">
                        <div class="stat-icon info"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <p class="stat-label">Total Users</p>
                            <h3 class="counter stat-value" data-target="{{ $totalUsers }}">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Officers Table -->
            <div class="mb-4">
                <div class="card p-4 animate-fade-in">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h6 class="mb-0">Officers Directory</h6>
                        <a href="/admin/add-officers" class="btn btn-sm btn-primary">Add Officer</a>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="d-none d-md-table-cell">Email</th>
                                    <th>Role</th>
                                    <th class="d-none d-lg-table-cell">Contact</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($officers as $officer)
                                <tr>
                                    <td>{{ $officer->name }}</td>
                                    <td class="d-none d-md-table-cell">{{ $officer->email }}</td>
                                    <td>{{ $officer->role }}</td>
                                    <td class="d-none d-lg-table-cell">{{ $officer->phone ?? '-' }}</td>
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

            <!-- Case Distribution & Staff Performance -->
            <div class="row mb-4">
                <div class="col-lg-4 mb-4">
                    <div class="card p-4 animate-fade-in">
                        <h6 class="mb-4">Case Distribution</h6>
                        <div class="chart-container">
                            <canvas id="caseDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-4">
                    <div class="card p-4 animate-fade-in delay-1">
                        <h6 class="mb-4">Staff Performance</h6>
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Officer Name</th>
                                        <th class="d-none d-sm-table-cell">Assigned</th>
                                        <th class="d-none d-sm-table-cell">Completed</th>
                                        <th>Pending</th>
                                        <th class="d-none d-md-table-cell">Rate</th>
                                        <th class="d-none d-md-table-cell">Avg Time</th>
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
                                        <td class="d-none d-sm-table-cell">{{ $staff['assigned'] }}</td>
                                        <td class="d-none d-sm-table-cell">{{ $staff['completed'] }}</td>
                                        <td>{{ $staff['pending'] }}</td>
                                        <td class="d-none d-md-table-cell">{{ $staff['rate'] }}%</td>
                                        <td class="d-none d-md-table-cell">{{ $staff['avgTime'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Summary -->
            <div class="mb-4">
                <div class="card p-4 animate-fade-in">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h6 class="mb-0">Reports Summary</h6>
                        <div class="d-flex flex-wrap gap-2 reports-actions">
                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf me-1"></i> PDF</button>
                            <button class="btn btn-sm btn-outline-success"><i class="fas fa-file-excel me-1"></i> Excel</button>
                            <button class="btn btn-sm btn-primary"><i class="fas fa-chart-bar me-1"></i> Analytics</button>
                        </div>
                    </div>
                    <div class="reports-summary-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Cases This Month</small>
                            <h4 class="mb-0">{{ $reportsSummary['casesThisMonth'] }}</h4>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Closed This Month</small>
                            <h4 class="mb-0">{{ $reportsSummary['closedThisMonth'] }}</h4>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Pending Cases</small>
                            <h4 class="mb-0">{{ $reportsSummary['pendingCases'] }}</h4>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Generated Reports</small>
                            <h4 class="mb-0">{{ $reportsSummary['generatedReports'] }}</h4>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted">Financial Released</small>
                            <h4 class="mb-0">₱{{ number_format($reportsSummary['financialReleased'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
            var overlay = document.getElementById('sidebarOverlay');
            if (overlay) overlay.addEventListener('click', toggleSidebar);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    var sidebar = document.getElementById('sidebar');
                    if (sidebar && sidebar.classList.contains('show')) toggleSidebar();
                }
            });
            document.querySelectorAll('.sidebar-menu a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) toggleSidebar();
                });
            });
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    var sidebar = document.getElementById('sidebar');
                    var overlay = document.getElementById('sidebarOverlay');
                    if (sidebar && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        if (overlay) overlay.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }
            });
        });

        function updateDateTime() {
            var now = new Date();
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            var el = document.getElementById('currentDateTime');
            if (el) el.textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);

        var counters = document.querySelectorAll('.counter');
        counters.forEach(function(counter) {
            var target = parseInt(counter.getAttribute('data-target'));
            var duration = 2000;
            var step = target / (duration / 16);
            var current = 0;
            function updateCounter() {
                current += step;
                if (current < target) { counter.textContent = Math.floor(current); requestAnimationFrame(updateCounter); }
                else { counter.textContent = target; }
            }
            updateCounter();
        });

        var caseCtx = document.getElementById('caseDistributionChart').getContext('2d');
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
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($justLoggedIn ?? false)
                var adminName = '{{ session('admin_user_name') ?? 'Admin' }}';
                Swal.fire({
                    title: 'Welcome Admin!',
                    text: adminName,
                    icon: 'success',
                    confirmButtonColor: '#1A237E',
                    confirmButtonText: 'Continue',
                    background: '#ffffff',
                    customClass: { popup: 'rounded-4 shadow-lg' },
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
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then(function(result) {
                if (result.isConfirmed) document.getElementById('logout-form').submit();
            });
        }
    </script>
</body>
</html>
