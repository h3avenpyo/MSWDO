<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eligibility Validation | MSWDO Social Case Study</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
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

        .card-body { padding: 1.5rem; }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.5rem;
            font-weight: 600;
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

        .metric-card { padding: 1.25rem; }
        .metric-card .value { font-size: 2rem; font-weight: 700; margin: 0; }
        .metric-card .label { color: var(--text-muted); font-size: .9rem; margin: .5rem 0 0; }
        .search-panel { padding: 1.5rem; }
        .results-panel { padding: 1.5rem; }
        .badge-status { font-size: .9rem; font-weight: 600; }
        .btn-disabled { pointer-events: none; opacity: .65; }
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
                    <h5 class="mb-0 me-4">Eligibility Validation</h5>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-4 text-muted small" id="currentDateTime"></div>
                    <div class="activity-avatar" style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4" style="flex: 1; overflow: hidden; display: flex; flex-direction: column;">
            <div class="row g-4 mb-4">
                <div class="col-lg-3">
                    <div class="card metric-card">
                        <p class="label">Today's Eligibility Checks</p>
                        <p class="value">{{ $metrics['checksToday'] }}</p>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card metric-card">
                        <p class="label">Eligible Clients Today</p>
                        <p class="value">{{ $metrics['eligibleToday'] }}</p>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card metric-card">
                        <p class="label">Not Eligible Today</p>
                        <p class="value">{{ $metrics['notEligibleToday'] }}</p>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card metric-card">
                        <p class="label">Average Search Time</p>
                        <p class="value">{{ $metrics['averageSearchTimeMs'] }} ms</p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-5">
                    <div class="card search-panel">
                        <h6>Find Client</h6>
                        <form id="searchForm" class="row g-3" onsubmit="return false;">
                            <div class="col-12">
                                <label class="form-label">Control Number</label>
                                <input type="text" id="controlNumber" class="form-control" placeholder="Enter control number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" id="firstName" class="form-control" placeholder="Juan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" id="lastName" class="form-control" placeholder="Dela Cruz">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Contact Number</label>
                                <input type="text" id="contactNumber" class="form-control" placeholder="09123456789">
                            </div>
                            <div class="col-12 d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="searchClients()">Search</button>
                            </div>
                            <div class="col-12">
                                <div id="searchResultMessage" class="text-muted"></div>
                            </div>
                        </form>

                        <div id="searchResults" class="mt-4"></div>
                    </div>
                </div>
                <div class="col-xl-7">
                    <div class="card results-panel" id="eligibilityPanel">
                        <h6>Eligibility Result</h6>
                        <div id="eligibilityContent" class="mt-3">
                            <p class="text-muted">Search for a client to display eligibility validation results.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card p-4">
                        <h6 class="mb-4">Recent Eligibility Checks</h6>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Officer</th>
                                        <th>Client</th>
                                        <th>Result</th>
                                        <th>Duration</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($metrics['recentChecks'] as $log)
                                    <tr>
                                        <td>{{ $log->officer_name }}</td>
                                        <td>{{ $log->client_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $log->result === 'eligible' ? 'success' : 'danger' }}">{{ strtoupper(str_replace('_', ' ', $log->result)) }}</span>
                                        </td>
                                        <td>{{ $log->search_duration_ms }} ms</td>
                                        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    @endforeach
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
        async function searchClients() {
            const controlNumber = document.getElementById('controlNumber').value.trim();
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const contactNumber = document.getElementById('contactNumber').value.trim();
            const resultArea = document.getElementById('searchResults');
            const messageArea = document.getElementById('searchResultMessage');

            resultArea.innerHTML = '';
            messageArea.textContent = '';

            const response = await fetch('/admin/social-case-eligibility/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ control_number: controlNumber, first_name: firstName, last_name: lastName, contact_number: contactNumber }),
            });

            const payload = await response.json();
            const clients = payload.clients || [];

            if (!clients.length) {
                messageArea.textContent = 'Client not found. You can register a new client to continue eligibility validation.';
                resultArea.innerHTML = `
                    <div class="alert alert-warning">No matching client found.</div>
                    <a href="/admin/social-case-eligibility/register" class="btn btn-outline-primary">Register New Client</a>
                `;
                return;
            }

            const rows = clients.map(client => {
                return `
                    <div class="card mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">${client.first_name} ${client.middle_name || ''} ${client.last_name}</div>
                                <div class="text-muted small">ID ${client.id} • ${client.contact_number || 'No contact'}</div>
                            </div>
                            <button onclick="checkEligibility(${client.id})" class="btn btn-sm btn-primary">Check Eligibility</button>
                        </div>
                    </div>
                `;
            }).join('');

            resultArea.innerHTML = rows;
        }

        async function checkEligibility(clientId) {
            const response = await fetch(`/admin/social-case-eligibility/${clientId}/check`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            });

            const data = await response.json();

            if (data.eligible) {
                Swal.fire({
                    title: 'ELIGIBLE',
                    text: 'This client is eligible for assistance.',
                    icon: 'success',
                    confirmButtonColor: '#22C55E',
                    confirmButtonText: 'Proceed',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `/admin/social-case-studies/create/${clientId}`;
                    }
                });
            } else {
                Swal.fire({
                    title: 'NOT ELIGIBLE',
                    html: `
                        <div style="text-align: left;">
                            <p><strong>Reason:</strong> ${data.reason}</p>
                            <p><strong>Date of last assistance:</strong> ${data.assistance_date}</p>
                            <p><strong>Type of assistance:</strong> ${data.assistance_type}</p>
                            <p><strong>Next eligible date:</strong> ${data.next_eligible_date}</p>
                        </div>
                    `,
                    icon: 'error',
                    confirmButtonColor: '#D32F2F',
                    confirmButtonText: 'Close',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                });
            }
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
</body>
</html>
