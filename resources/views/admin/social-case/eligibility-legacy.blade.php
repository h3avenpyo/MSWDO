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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

        /* Responsive */
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.show {
            transform: translateX(0);
        }
        .main-content {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 260px;
                width: calc(100% - 260px);
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
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-building"></i> MSWDO Admin</div>
        <ul class="sidebar-menu">
            <li><a href="/admin/social-case/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="/admin/social-case" class="active"><i class="fas fa-clipboard-list"></i> Eligibility Check</a></li>
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
                    <h5 class="mb-0 me-4">Social Case Eligibility</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-4 text-muted small" id="currentDateTime"></div>
                    <div class="activity-avatar" style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
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
                cancelButtonColor: '#EF4444',
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

        // Welcome popup
        window.addEventListener('load', function() {
            setTimeout(function() {
                Swal.fire({
                    title: 'Welcome to Social Case Study System!',
                    html: '<div style="text-align:center;line-height:1.7;color:#475569;font-size:15px">' +
                          '<p style="margin:0 0 8px">Track and manage social case study reports for Silang residents.</p>' +
                          '<p style="margin:0;font-size:13px;color:#94A3B8">Use the sidebar to navigate between eligibility checks, case studies, and more.</p>' +
                          '</div>',
                    icon: 'info',
                    confirmButtonColor: '#1A237E',
                    confirmButtonText: 'Get Started',
                    background: '#ffffff',
                    customClass: { popup: 'rounded-4 shadow-lg' },
                    allowOutsideClick: false
                });
            }, 500);
        });
    </script>
</body>
</html>
