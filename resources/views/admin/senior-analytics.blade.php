<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Analytics - MSWDO Silang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary: #1A237E;
            --primary-dark: #121858;
            --secondary: #6B7280;
            --accent: #FBC02D;
            --danger: #D32F2F;
            --background: #F8FAFC;
            --cards: #FFFFFF;
            --border: #E5E7EB;
            --text: #1F2937;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--background); color: var(--text); display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 260px; background: var(--primary); color: white; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh; z-index: 1000; transition: transform 0.3s ease; }
        .sidebar-brand { padding: 1.2rem 1.5rem; font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-brand i { font-size: 1.4rem; color: var(--accent); }
        .sidebar-menu { list-style: none; margin: 0; padding: 1rem 0; flex: 1; overflow-y: auto; }
        .sidebar-menu li { margin-bottom: .2rem; }
        .sidebar-menu a { color: rgba(255,255,255,.75); padding: .75rem 1.5rem; display: flex; align-items: center; gap: .75rem; text-decoration: none; font-size: .9rem; border-left: 3px solid transparent; transition: all .2s ease; }
        .sidebar-menu a:hover { background: rgba(255,255,255,.1); color: var(--accent); }
        .sidebar-menu a.active { background: rgba(255,255,255,.1); color: var(--accent); border-left-color: var(--accent); }
        .sidebar-menu a i { width: 20px; text-align: center; font-size: .95rem; }

        .main-content { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background: var(--cards); border-bottom: 1px solid var(--border); padding: 1rem 2rem; position: sticky; top: 0; z-index: 999; display: flex; align-items: center; justify-content: space-between; }

        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); background: var(--cards); padding: 1.5rem; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); }
        .stat-card { display: flex; align-items: center; gap: 0.75rem; min-height: 70px; }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .chart-container { position: relative; height: 450px; width: 100%; }

        .animate-fade-in { opacity: 0; transform: translateY(12px); animation: fadeInUp 0.5s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 992px) {
            .stat-card { min-height: 90px; }
            .stat-icon { width: 56px; height: 56px; font-size: 1.3rem; }
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .stat-card { min-height: 80px; }
            .stat-icon { width: 48px; height: 48px; font-size: 1.2rem; }
            .chart-container { height: 350px; }
        }
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
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none me-3" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0">Senior Citizen Statistics</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-muted small" id="currentDateTime"></div>
                <div style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin'), 0, 2)) }}</div>
            </div>
        </nav>

        <div class="p-4">
            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card animate-fade-in">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(26,35,126,0.15) 0%, rgba(26,35,126,0.05) 100%); color: var(--primary);"><i class="fas fa-users"></i></div>
                            <div class="flex-grow-1">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.2rem 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Seniors</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $totalSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card animate-fade-in delay-1">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(16,185,129,0.15) 0%, rgba(16,185,129,0.05) 100%); color: #10B981;"><i class="fas fa-check-circle"></i></div>
                            <div class="flex-grow-1">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.2rem 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Active Seniors</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $activeSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card animate-fade-in delay-2">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(245,158,11,0.15) 0%, rgba(245,158,11,0.05) 100%); color: #D97706;"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="flex-grow-1">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.2rem 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Barangays</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $totalBarangays }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card animate-fade-in delay-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(99,102,241,0.15) 0%, rgba(99,102,241,0.05) 100%); color: #6366F1;"><i class="fas fa-crown"></i></div>
                            <div class="flex-grow-1">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.2rem 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Top Barangay</p>
                                <h4 style="font-size: 1.1rem; font-weight: 700; margin: 0; line-height: 1.2; color: var(--text);">{{ $topBarangay }}</h4>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $topBarangayCount }} seniors</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card animate-fade-in">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(236,72,153,0.15) 0%, rgba(236,72,153,0.05) 100%); color: #EC4899;"><i class="fas fa-user-plus"></i></div>
                            <div class="flex-grow-1">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.2rem 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">New This Month</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $newSeniorsThisMonth }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="card animate-fade-in delay-1">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(139,92,246,0.15) 0%, rgba(139,92,246,0.05) 100%); color: #8B5CF6;"><i class="fas fa-chart-line"></i></div>
                            <div class="flex-grow-1">
                                <p style="color: #6B7280; font-size: 0.75rem; margin: 0 0 0.2rem 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Avg per Barangay</p>
                                <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1; color: var(--text);">{{ $avgPerBarangay }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card animate-fade-in">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0" style="font-weight: 700; font-size: 1.1rem;">Top 10 Barangays by Senior Citizens</h6>
                            <span class="badge" style="background: rgba(26,35,126,0.1); color: var(--primary); padding: 0.5rem 1rem; font-weight: 600;">{{ $totalBarangays }} Total</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="barangayChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card animate-fade-in delay-1">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0" style="font-weight: 700; font-size: 1.1rem;">Barangay Ranking</h6>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                                <tbody>
                                    @foreach($barangayStats->take(10) as $i => $row)
                                    @php $pct = $totalSeniors > 0 ? round(($row->total / $totalSeniors) * 100, 1) : 0; @endphp
                                    <tr>
                                        <td style="width: 50px;">
                                            <span class="badge" style="background: rgba(26,35,126,0.1); color: var(--primary);">{{ $i + 1 }}</span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600;">{{ $row->barangay }}</div>
                                            <div class="progress mt-1" style="height: 6px;">
                                                <div class="progress-bar" style="width: {{ $pct }}%; background: var(--primary); border-radius: 3px;"></div>
                                            </div>
                                        </td>
                                        <td style="width: 100px; text-align: right;">
                                            <div style="font-weight: 700; color: var(--primary);">{{ $row->total }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $pct }}%</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Analytics Row -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card animate-fade-in">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0" style="font-weight: 700; font-size: 1rem;">Gender Distribution</h6>
                        </div>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card animate-fade-in delay-1">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0" style="font-weight: 700; font-size: 1rem;">Age Groups</h6>
                        </div>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        function updateClock() {
            const now = new Date();
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' | ' + now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });
        }
        updateClock();
        setInterval(updateClock, 1000);

        const labels = {!! json_encode($barangayStats->pluck('barangay')) !!};
        const values = {!! json_encode($barangayStats->pluck('total')) !!};
        const colors = [
            'rgba(26, 35, 126, 0.8)',
            'rgba(251, 192, 45, 0.8)',
            'rgba(107, 114, 128, 0.8)',
            'rgba(20, 184, 166, 0.8)',
            'rgba(220, 38, 38, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(34, 197, 94, 0.8)',
            'rgba(249, 115, 0.8)',
            'rgba(156, 163, 175, 0.8)'
        ];

        // Prepare data for Top 10 + Others
        let chartLabels, chartValues, chartColors;
        if (labels.length <= 10) {
            chartLabels = labels;
            chartValues = values;
            chartColors = colors.slice(0, labels.length);
        } else {
            chartLabels = labels.slice(0, 10);
            chartValues = values.slice(0, 10);
            const othersSum = values.slice(10).reduce((a, b) => a + b, 0);
            chartLabels.push('Others');
            chartValues.push(othersSum);
            chartColors = colors;
        }

        // Horizontal bar chart
        new Chart(document.getElementById('barangayChart'), {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Senior Citizens',
                    data: chartValues,
                    backgroundColor: chartColors,
                    borderColor: '#fff',
                    borderWidth: 2,
                    borderRadius: 6,
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
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return context.raw + ' seniors (' + percentage + '%)';
                            }
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(0,0,0,0.06)' } },
                    y: { grid: { display: false }, ticks: { font: { size: 12, weight: 500 } } }
                }
            }
        });

        // Gender chart
        const genderLabels = {!! json_encode($genderStats->pluck('sex')) !!};
        const genderValues = {!! json_encode($genderStats->pluck('total')) !!};
        const genderColors = ['#1A237E', '#EC4899'];

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
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Age groups chart
        const ageLabels = {!! json_encode($ageGroups->pluck('age_group')) !!};
        const ageValues = {!! json_encode($ageGroups->pluck('total')) !!};
        const ageColors = ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#9CA3AF'];

        new Chart(document.getElementById('ageChart'), {
            type: 'bar',
            data: {
                labels: ageLabels,
                datasets: [{
                    label: 'Seniors',
                    data: ageValues,
                    backgroundColor: ageColors,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(0,0,0,0.06)' } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    </script>
</body>
</html>
