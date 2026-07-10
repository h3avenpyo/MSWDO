<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Social Case Study</title>
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
        .sidebar-menu a:hover { background: rgba(255,255,255,.1); color: var(--accent); }
        .sidebar-menu a.active { background: rgba(255,255,255,.1); color: var(--accent); border-left-color: var(--accent); }
        .sidebar-menu a i { width: 20px; text-align: center; font-size: .95rem; }

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
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .page-title { font-size: 1.15rem; font-weight: 700; margin: 0; }
        .page-subtitle { color: var(--secondary); margin: .35rem 0 0; font-size: .93rem; }
        .btn-icon {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 8px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            cursor: pointer;
            transition: all .2s ease;
        }
        .btn-icon:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        .page-body { padding: 2rem; flex: 1; }

        .card {
            background: var(--cards);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            margin-bottom: 1.5rem;
        }
        .card-body { padding: 1.5rem; }

        .form-control, .form-select {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: .75rem 1rem;
            color: var(--text);
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,35,126,.08); }
        .form-label { font-weight: 600; color: #475569; margin-bottom: .55rem; }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; }
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
            <li><a href="/admin/social-case"><i class="fas fa-clipboard-list"></i> Eligibility Check</a></li>
            <li><a href="/admin/social-case-studies"><i class="fas fa-file-alt"></i> Case Studies</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="top-navbar">
            <div>
                <h5 class="page-title">Create Social Case Study</h5>
                <p class="page-subtitle">Client: {{ $client->full_name }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn-icon d-md-none" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="text-end">
                    <div id="currentDateTime" class="text-secondary small"></div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-secondary" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:.85rem;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="page-body">
            <div class="card">
                <div class="card-body">
            <form method="POST" action="{{ route('admin.social-case-studies.store', $client) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Case Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Interview Date</label>
                    <input type="date" name="interview_date" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Summary</label>
                    <textarea name="summary" rows="6" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create Case Study</button>
            </form>
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
