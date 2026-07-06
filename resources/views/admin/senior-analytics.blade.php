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

        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); background: var(--cards); padding: 1.5rem; }
        .stat-card { display: flex; align-items: center; gap: 1rem; }
        .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .chart-container { position: relative; height: 400px; width: 100%; }

        .animate-fade-in { opacity: 0; transform: translateY(12px); animation: fadeInUp 0.5s ease forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
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
            <li><a href="/admin/senior/statistics" class="active"><i class="fas fa-chart-bar"></i> Statistics</a></li>
            <li><a href="/admin/senior/reports"><i class="fas fa-file-alt"></i> Reports</a></li>
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
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card animate-fade-in">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(26,35,126,0.1); color: var(--primary);"><i class="fas fa-users"></i></div>
                            <div>
                                <p style="color: #6B7280; font-size: 0.8rem; margin: 0 0 0.2rem 0; font-weight: 500;">Total Seniors</p>
                                <h3 style="font-size: 1.6rem; font-weight: 700; margin: 0;">{{ $totalSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card animate-fade-in delay-1">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: #10B981;"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <p style="color: #6B7280; font-size: 0.8rem; margin: 0 0 0.2rem 0; font-weight: 500;">Active Seniors</p>
                                <h3 style="font-size: 1.6rem; font-weight: 700; margin: 0;">{{ $activeSeniors }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card animate-fade-in delay-2">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #D97706;"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <p style="color: #6B7280; font-size: 0.8rem; margin: 0 0 0.2rem 0; font-weight: 500;">Barangays</p>
                                <h3 style="font-size: 1.6rem; font-weight: 700; margin: 0;">{{ $totalBarangays }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card animate-fade-in delay-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: #6366F1;"><i class="fas fa-crown"></i></div>
                            <div>
                                <p style="color: #6B7280; font-size: 0.8rem; margin: 0 0 0.2rem 0; font-weight: 500;">Top Barangay</p>
                                <h3 style="font-size: 1.6rem; font-weight: 700; margin: 0;">{{ $topBarangay }}</h3>
                                <small class="text-muted">{{ $topBarangayCount }} seniors</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-3 mb-4">
                <div class="col-lg-8">
                    <div class="card animate-fade-in">
                        <h6 class="mb-3" style="font-weight: 700;">Senior Citizens per Barangay</h6>
                        <div class="chart-container">
                            <canvas id="barangayChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card animate-fade-in delay-1">
                        <h6 class="mb-3" style="font-weight: 700;">Distribution</h6>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barangay Table -->
            <div class="card animate-fade-in">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0" style="font-weight: 700;">Barangay Breakdown</h6>
                    <small class="text-muted">{{ $totalBarangays }} barangays &middot; {{ $avgPerBarangay }} avg per barangay</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                        <thead style="background: #F8FAFC;">
                            <tr>
                                <th>#</th>
                                <th>Barangay</th>
                                <th style="width: 120px;">Total</th>
                                <th style="width: 200px;">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangayStats as $i => $row)
                            @php $pct = $totalSeniors > 0 ? round(($row->total / $totalSeniors) * 100, 1) : 0; @endphp
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td><strong>{{ $row->barangay }}</strong></td>
                                <td><span class="badge" style="background: var(--primary);">{{ $row->total }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar" style="width: {{ $pct }}%; background: var(--primary);"></div>
                                        </div>
                                        <small class="text-muted" style="min-width: 40px;">{{ $pct }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        const colors = ['#1A237E','#283593','#3F51B5','#5C6BC0','#7986CB','#9FA8DA','#C5CAE9','#E8EAF6','#FBC02D','#F9A825','#F57F17','#E65100','#BF360C','#1B5E20','#2E7D32','#388E3C','#43A047','#4CAF50','#66BB6A','#81C784','#A5D6A7','#C8E6C9','#00695C','#00796B','#00897B','#009688','#26A69A','#4DB6AC','#80CBC4','#B2DFDB','#01579B','#0277BD','#0288D1','#039BE5','#03A9F4','#29B6F6','#4FC3F7','#81D4FA','#B3E5FC','#E1F5FE'];

        // Bar chart
        new Chart(document.getElementById('barangayChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Senior Citizens',
                    data: values,
                    backgroundColor: colors.slice(0, labels.length),
                    borderColor: '#fff',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(0,0,0,0.06)' } },
                    x: { grid: { display: false }, ticks: { maxRotation: 45, font: { size: 11 } } }
                }
            }
        });

        // Pie chart (top 8 + others)
        let pieLabels, pieValues, pieColors;
        if (labels.length <= 8) {
            pieLabels = labels; pieValues = values; pieColors = colors;
        } else {
            pieLabels = labels.slice(0, 7).toArray();
            pieValues = values.slice(0, 7).toArray();
            const otherSum = values.slice(7).reduce((a, b) => a + b, 0);
            pieLabels.push('Others');
            pieValues.push(otherSum);
            pieColors = colors.slice(0, 7);
            pieColors.push('#CBD5E1');
        }

        new Chart(document.getElementById('pieChart'), {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieValues,
                    backgroundColor: pieColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 10 } } }
                },
                cutout: '55%'
            }
        });
    </script>
</body>
</html>
