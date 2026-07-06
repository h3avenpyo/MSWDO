<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizen Dashboard</title>
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

        .badge-active { background-color: rgba(20, 184, 166, 0.1); color: var(--secondary); }
        .badge-pending { background-color: rgba(245, 158, 11, 0.1); color: var(--accent); }

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
            <li><a href="/admin/senior/birthdays"><i class="fas fa-birthday-cake"></i> Upcoming Birthday</a></li>
            <li><a href="/admin/senior/reports"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="/admin/statistics"><i class="fas fa-chart-line"></i> Statistics</a></li>
            
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
        <div class="p-4">
            <!-- Overview Cards -->
            <div class="row mb-4">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card animate-fade-in" style="padding: 0;">
                        <div style="display: flex; align-items: center; padding: 1.5rem;">
                            <div style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background-color: rgba(37, 99, 235, 0.1); color: #1A237E; flex-shrink: 0; margin-right: 1rem;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <p style="color: #6B7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 500;">Total Seniors</p>
                                <h3 class="counter" data-target="{{ $totalSeniors }}" style="font-size: 2rem; font-weight: 700; margin: 0; line-height: 1;">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card animate-fade-in delay-1" style="padding: 0;">
                        <div style="display: flex; align-items: center; padding: 1.5rem;">
                            <div style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background-color: rgba(20, 184, 166, 0.1); color: #6B7280; flex-shrink: 0; margin-right: 1rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <p style="color: #6B7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 500;">Active Seniors</p>
                                <h3 class="counter" data-target="{{ $activeSeniors }}" style="font-size: 2rem; font-weight: 700; margin: 0; line-height: 1;">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card animate-fade-in delay-2" style="padding: 0;">
                        <div style="display: flex; align-items: center; padding: 1.5rem;">
                            <div style="width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; background-color: rgba(245, 158, 11, 0.1); color: #FBC02D; flex-shrink: 0; margin-right: 1rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div style="display: flex; flex-direction: column; justify-content: center;">
                                <p style="color: #6B7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 500;">Pending Applications</p>
                                <h3 class="counter" data-target="{{ $pendingSeniors }}" style="font-size: 2rem; font-weight: 700; margin: 0; line-height: 1;">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Senior Citizen Records -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card p-4 animate-fade-in">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0">Recent Senior Citizen Records</h6>
                            <button class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Add New Record</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Record Number</th>
                                        <th>Full Name</th>
                                        <th>Birth Date</th>
                                        <th>OSCA ID</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSeniors as $senior)
                                    <tr>
                                        <td>{{ $senior->record_number ?? '-' }}</td>
                                        <td>{{ $senior->full_name ?? '-' }}</td>
                                        <td>{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') : '-' }}</td>
                                        <td>{{ $senior->osca_id ?? '-' }}</td>
                                        <td>
                                            @if($senior->status == 'active')
                                                <span class="badge badge-active">Active</span>
                                            @else
                                                <span class="badge badge-pending">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-link text-primary p-0 me-2" type="button"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-sm btn-link text-primary p-0 me-2" type="button"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-link text-danger p-0" type="button"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No senior citizen records found.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
