<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Case Studies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        *, *::before, *::after { box-sizing: border-box; }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 0;
        }

        .sidebar {
            background: var(--sidebar-bg);
            width: 260px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
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
        .sidebar-menu-header {
            color: rgba(255,255,255,.5);
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: .75rem 1.5rem .25rem;
        }
        .sidebar-menu a.submenu {
            padding-left: 2.5rem;
            font-size: .85rem;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
        }

        .top-navbar {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--border);
            padding: 1.25rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .navbar-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            margin: 0;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .navbar-datetime {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .navbar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

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

        .card-body { padding: 1.5rem; }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text);
        }

        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-warning {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .badge-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .badge-info {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .badge-secondary {
            background-color: #F1F5F9;
            color: #475569;
        }

        .btn {
            padding: 0.6rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-outline-secondary {
            border-color: var(--border);
            color: var(--text-muted);
        }

        .btn-outline-secondary:hover {
            background-color: var(--border);
            color: var(--text);
        }

        .page-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
        }

        .page-subtitle {
            color: var(--text-muted);
            margin: .35rem 0 0;
            font-size: .93rem;
        }

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

        @media (max-width: 768px) {
            .main-content { margin-left: 0; width: 100%; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-building"></i> MSWDO Admin</div>
        <ul class="sidebar-menu">
            <li><a href="/admin/social-case/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            
            <li class="sidebar-menu-header">Cases</li>
            <li><a href="/admin/social-case-eligibility/register" class="submenu"><i class="fas fa-plus"></i> New Case</a></li>
            <li><a href="/admin/social-case-studies" class="submenu active"><i class="fas fa-folder-open"></i> Active Cases</a></li>
            <li><a href="#" class="submenu"><i class="fas fa-archive"></i> Released</a></li>
            
            <li class="sidebar-menu-header">Clients</li>
            <li><a href="/admin/beneficiary-intake" class="submenu"><i class="fas fa-users"></i> Beneficiary Intake</a></li>
            
            <li class="sidebar-menu-header">Reports</li>
            <li><a href="#" class="submenu"><i class="fas fa-chart-bar"></i> Generate Reports</a></li>
            
            <li class="sidebar-menu-header">System</li>
            <li><a href="#" class="submenu"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link d-md-none" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="navbar-title">Social Case Studies</h1>
                </div>
                <div class="navbar-right">
                    <div class="navbar-datetime" id="currentDateTime"></div>
                    <div class="navbar-avatar">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                </div>
            </div>
        </nav>

        <div class="page-body">
            <div class="card">
                <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Case Number</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Interview Date</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studies as $study)
                            <tr>
                                <td>{{ $study->id }}</td>
                                <td>{{ $study->case_number }}</td>
                                <td>{{ $study->client->full_name }}</td>
                                <td>{{ $study->status }}</td>
                                <td>{{ optional($study->interview_date)->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $study->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.social-case-studies.edit', $study) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.social-case-studies.destroy', $study) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $studies->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
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

        // Update current date and time
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);
    </script>

    <!-- Hidden form for secure POST logout -->
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>
</html>
