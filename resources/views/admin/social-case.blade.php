<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSWDO Admin - Social Case Eligibility</title>
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
        .breadcrumb-nav { font-size: .8rem; color: var(--secondary); margin: 0; }
        .breadcrumb-nav a { color: var(--primary); text-decoration: none; }
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
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
        .card-body { padding: 1.5rem; }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.5rem;
        }
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
        .stat-icon.blue { background: rgba(37,99,235,.1); color: var(--primary); }
        .stat-icon.teal { background: rgba(20,184,166,.1); color: var(--secondary); }
        .stat-icon.amber { background: rgba(245,158,11,.1); color: var(--accent); }

        .stat-value { font-size: 1.9rem; font-weight: 700; margin: 0; }
        .stat-label { color: var(--secondary); margin: .25rem 0 0; font-size: .88rem; }

        .form-control, .form-select {
            background: var(--background);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: .75rem 1rem;
            color: var(--text);
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.08); }
        .form-label { font-weight: 600; color: #475569; margin-bottom: .55rem; }

        .badge-pill {
            padding: .45rem .85rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 700;
        }
        .badge-pill.success { background: rgba(22,163,74,.12); color: #166534; }
        .badge-pill.warning { background: rgba(245,158,11,.12); color: #92400e; }
        .badge-pill.danger { background: rgba(220,38,38,.12); color: #991b1b; }

        .table thead th { background: var(--background); border-bottom: 1px solid var(--border); font-size: .82rem; color: var(--secondary); text-transform: uppercase; letter-spacing: .05em; }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-building"></i> MSWDO Admin</div>
        <ul class="sidebar-menu">
            <li><a href=""><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="/admin/social-case" class="active"><i class="fas fa-clipboard-list"></i> Social Case Eligibility</a></li>
            <li><a href="/admin/statistics"><i class="fas fa-chart-line"></i> Statistics</a></li>
            <!-- <li><a href="/admin/add-officers"><i class="fas fa-user-shield"></i> Add Officers</a></li> -->
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="top-navbar">
            <div>
                <h5 class="page-title">Social Case Eligibility</h5>
                <p class="page-subtitle">Search for clients, validate assistance status, and proceed to social case creation.</p>
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
            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <h5 class="mb-1">Eligibility Validation</h5>
                                    <p class="text-secondary mb-0">Search by Client ID, name, or birthdate. The system checks whether the client is eligible based on the last approved assistance records.</p>
                                </div>
                                <a href="/admin/social-case-eligibility/register" class="btn btn-outline-primary btn-sm">Register New Client</a>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="clientId">Client ID</label>
                                    <input id="clientId" type="text" class="form-control" placeholder="1234">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="fullName">Full Name</label>
                                    <input id="fullName" type="text" class="form-control" placeholder="Juan Dela Cruz">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="birthdate">Birthdate</label>
                                    <input id="birthdate" type="date" class="form-control">
                                </div>
                                <div class="col-12 d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary" onclick="searchClients()">Search Client</button>
                                    <a href="/admin/social-case-eligibility" class="btn btn-outline-secondary">Open Eligibility Dashboard</a>
                                </div>
                            </div>
                            <div id="searchResult" class="mt-4"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Dashboard Metrics</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="stat-card">
                                        <div>
                                            <p class="stat-label">Validations Today</p>
                                            <p class="stat-value">18</p>
                                        </div>
                                        <div class="stat-icon blue"><i class="fas fa-search"></i></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="stat-card">
                                        <div>
                                            <p class="stat-label">Eligible</p>
                                            <p class="stat-value">12</p>
                                        </div>
                                        <div class="stat-icon teal"><i class="fas fa-check-circle"></i></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="stat-card">
                                        <div>
                                            <p class="stat-label">Not Eligible</p>
                                            <p class="stat-value">6</p>
                                        </div>
                                        <div class="stat-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h6 class="mb-1">Recent Eligibility Activity</h6>
                                    <p class="text-secondary mb-0">Latest client validations and action items.</p>
                                </div>
                                <a href="/admin/social-case-eligibility/cases" class="btn btn-outline-primary btn-sm">View Case Studies</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Status</th>
                                            <th>Last Assistance</th>
                                            <th>Eligible Again</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentChecksBody">
                                        <tr>
                                            <td colspan="4" class="text-center text-secondary">Search a client to show recent eligibility checks.</td>
                                        </tr>
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

        async function searchClients() {
            const clientId = document.getElementById('clientId').value.trim();
            const fullName = document.getElementById('fullName').value.trim();
            const birthdate = document.getElementById('birthdate').value;
            const resultArea = document.getElementById('searchResult');
            const recentBody = document.getElementById('recentChecksBody');

            resultArea.innerHTML = '<div class="text-center py-4 text-secondary">Searching...</div>';
            recentBody.innerHTML = '<tr><td colspan="4" class="text-center text-secondary">Loading...</td></tr>';

            const response = await fetch('/admin/social-case-eligibility/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ client_id: clientId, full_name: fullName, birthdate }),
            });

            if (!response.ok) {
                resultArea.innerHTML = '<div class="alert alert-danger">Unable to search clients. Please try again.</div>';
                recentBody.innerHTML = '<tr><td colspan="4" class="text-center text-secondary">Search failed.</td></tr>';
                return;
            }

            const payload = await response.json();
            const clients = payload.clients || [];

            if (!clients.length) {
                resultArea.innerHTML = `
                    <div class="alert alert-warning">Client not found.</div>
                    <a href="/admin/social-case-eligibility/register" class="btn btn-outline-primary">Register New Client</a>
                `;
                recentBody.innerHTML = '<tr><td colspan="4" class="text-center text-secondary">No recent checks available.</td></tr>';
                return;
            }

            resultArea.innerHTML = clients.map(client => `
                <div class="card mb-3">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between gap-3 align-items-start">
                        <div>
                            <div class="fw-bold">${client.first_name} ${client.middle_name || ''} ${client.last_name}</div>
                            <div class="text-secondary small">ID ${client.id} • ${client.birthdate}</div>
                        </div>
                        <a href="/admin/social-case-eligibility/${client.id}" class="btn btn-primary btn-sm">Validate Eligibility</a>
                    </div>
                </div>
            `).join('');

            recentBody.innerHTML = clients.slice(0, 3).map(client => `
                <tr>
                    <td>${client.first_name} ${client.middle_name || ''} ${client.last_name}</td>
                    <td><span class="badge-pill warning">Pending</span></td>
                    <td>--</td>
                    <td>--</td>
                </tr>
            `).join('');
        }
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
