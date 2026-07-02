<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eligibility Validation | MSWDO Social Case Study</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1A237E;
            --secondary: #6B7280;
            --accent: #FBC02D;
            --success: #047857;
            --danger: #C62828;
            --bg: #F8FAFC;
            --cards: #FFFFFF;
            --border: #E5E7EB;
            --text: #1F2937;
        }
        body { background-color: var(--bg); color: var(--text); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; }
        .sidebar { background: var(--primary); width: 260px; min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 1000; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1); color: #fff; font-weight: 700; display: flex; align-items: center; gap: .75rem; }
        .sidebar-menu { list-style: none; margin: 0; padding: 1rem 0; flex: 1; }
        .sidebar-menu li { margin-bottom: .25rem; }
        .sidebar-menu a { display: flex; align-items: center; gap: .75rem; padding: .75rem 1.5rem; color: rgba(255,255,255,.8); text-decoration: none; transition: all .2s ease; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,.1); color: var(--accent); }
        .sidebar-menu a i { width: 20px; text-align: center; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .top-navbar { background: var(--cards); border-bottom: 1px solid var(--border); padding: 1rem 2rem; position: sticky; top: 0; z-index: 999; }
        .top-navbar h5 { margin: 0; }
        .card { border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 8px 24px rgba(15, 23, 42, .03); }
        .metric-card { padding: 1.25rem; }
        .metric-card .value { font-size: 2rem; font-weight: 700; margin: 0; }
        .metric-card .label { color: var(--secondary); font-size: .9rem; margin: .5rem 0 0; }
        .search-panel { padding: 1.5rem; }
        .results-panel { padding: 1.5rem; }
        .badge-status { font-size: .9rem; font-weight: 600; }
        .btn-disabled { pointer-events: none; opacity: .65; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-building"></i> MSWDO Admin</div>
        <ul class="sidebar-menu">
            <li><a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="/admin/social-case" class="active"><i class="fas fa-clipboard-list"></i> Eligibility Validation</a></li>
            <li><a href="/admin/statistics"><i class="fas fa-chart-line"></i> Statistics</a></li>
            <li><a href="/admin/add-officers"><i class="fas fa-user-shield"></i> Add Officers</a></li>
            <li><a href="/admin"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="top-navbar d-flex align-items-center justify-content-between">
            <div>
                <h5>Eligibility Validation</h5>
                <small class="text-muted">Search clients by name, ID, or birthdate and validate assistance eligibility instantly.</small>
            </div>
            <div class="text-end">
                <div class="text-muted small">Officer: {{ session('admin_user_name') ?? 'N/A' }}</div>
            </div>
        </nav>

        <div class="p-4">
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
                                <label class="form-label">Client ID</label>
                                <input type="text" id="clientId" class="form-control" placeholder="Enter client ID">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Full Name</label>
                                <input type="text" id="fullName" class="form-control" placeholder="Juan dela Cruz">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Birthdate</label>
                                <input type="date" id="birthdate" class="form-control">
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
            const clientId = document.getElementById('clientId').value.trim();
            const fullName = document.getElementById('fullName').value.trim();
            const birthdate = document.getElementById('birthdate').value;
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
                body: JSON.stringify({ client_id: clientId, full_name: fullName, birthdate }),
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
                                <div class="text-muted small">ID ${client.id} • ${client.birthdate}</div>
                            </div>
                            <a href="/admin/social-case-eligibility/${client.id}" class="btn btn-sm btn-primary">Validate Eligibility</a>
                        </div>
                    </div>
                `;
            }).join('');

            resultArea.innerHTML = rows;
        }
    </script>
</body>
</html>
