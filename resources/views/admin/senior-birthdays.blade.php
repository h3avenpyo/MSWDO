<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birthday Beneficiaries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1A237E; --primary-dark: #121858; --accent: #FBC02D;
            --background: #F8FAFC; --cards: #FFFFFF; --text: #1F2937;
            --muted: #6B7280; --sidebar-bg: #1A237E; --border: #E5E7EB;
            --success: #059669; --warning: #D97706; --danger: #DC2626;
        }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: var(--background); font-family: 'Segoe UI', system-ui, sans-serif; color: var(--text); overflow: hidden; }

        .sidebar { background: var(--sidebar-bg); width: 260px; min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 1000; display: flex; flex-direction: column; transition: transform .3s ease; }
        .sidebar-brand { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1); color: #fff; font-weight: 700; display: flex; align-items: center; gap: .65rem; }
        .sidebar-brand i { font-size: 1.3rem; color: var(--accent); }
        .sidebar-menu { list-style: none; margin: 0; padding: 1rem 0; flex: 1; }
        .sidebar-menu li { margin-bottom: .2rem; }
        .sidebar-menu a { color: rgba(255,255,255,.75); padding: .75rem 1.5rem; display: flex; align-items: center; gap: .75rem; text-decoration: none; font-size: .9rem; border-left: 3px solid transparent; transition: all .2s ease; }
        .sidebar-menu a:hover { background: rgba(255,255,255,.1); color: var(--accent); }
        .sidebar-menu a.active { background: rgba(255,255,255,.1); color: var(--accent); border-left-color: var(--accent); }
        .sidebar-menu a i { width: 20px; text-align: center; }

        .main-content { margin-left: 260px; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }

        .top-navbar { background-color: var(--cards); border-bottom: 1px solid var(--border); padding: 1rem 2rem; position: sticky; top: 0; z-index: 999; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .page-title { font-size: 1.15rem; font-weight: 700; margin: 0; }
        .breadcrumb-nav { font-size: .8rem; color: var(--muted); margin: 0; }
        .breadcrumb-nav a { color: var(--primary); text-decoration: none; }

        .page-body { padding: 2rem; flex: 1; overflow: hidden; display: flex; flex-direction: column; }

        .stat-card { background: var(--cards); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,.04); padding: 1.25rem; display: flex; align-items: center; gap: 1rem; cursor: pointer; transition: all .2s ease; height: 100%; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,.08); border-color: var(--primary); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .stat-value { font-size: 1.5rem; font-weight: 700; margin: 0; line-height: 1.2; }
        .stat-label { font-size: .78rem; color: var(--muted); margin: 0; font-weight: 500; }

        .filter-section { background: var(--cards); border-radius: 16px; border: 1px solid var(--border); padding: 1.25rem; margin-bottom: 1.5rem; }

        .main-card { background: var(--cards); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,.04); overflow: hidden; flex: 1; display: flex; flex-direction: column; }

        .table { margin-bottom: 0; }
        .table thead th { background: var(--cards); border-bottom: 2px solid var(--border); font-weight: 600; font-size: .8rem; color: var(--text); padding: .7rem .6rem; white-space: nowrap; position: sticky; top: 0; z-index: 2; }
        .table thead th.sortable { cursor: pointer; user-select: none; }
        .table thead th.sortable:hover { color: var(--primary); }
        .table tbody td { border-bottom: 1px solid var(--border); padding: .6rem; font-size: .85rem; color: var(--text); vertical-align: middle; }
        .table tbody tr:hover { background-color: #F9FAFB; }

        .badge { padding: .3rem .55rem; font-size: .7rem; font-weight: 600; border-radius: 6px; }

        .countdown-badge { display: inline-flex; align-items: center; gap: .3rem; padding: .25rem .6rem; border-radius: 20px; font-size: .75rem; font-weight: 600; white-space: nowrap; }
        .countdown-badge.today { background: rgba(220,38,38,.1); color: var(--danger); }
        .countdown-badge.week { background: rgba(251,192,45,.15); color: var(--warning); }
        .countdown-badge.soon { background: rgba(26,35,126,.08); color: var(--primary); }

        .filter-chips { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: .75rem; }
        .filter-chip { padding: .3rem .8rem; border-radius: 20px; font-size: .78rem; font-weight: 500; border: 1px solid var(--border); background: var(--cards); cursor: pointer; transition: all .15s ease; color: var(--text); }
        .filter-chip:hover { border-color: var(--primary); color: var(--primary); }
        .filter-chip.active { background: var(--primary); color: white; border-color: var(--primary); }
        .filter-chip i { margin-right: .35rem; }

        .avatar-circle { width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .65rem; color: white; background: var(--primary); flex-shrink: 0; }

        .barangay-group { margin-bottom: 1rem; }
        .barangay-group-header { padding: .75rem 1rem; background: #F9FAFB; border: 1px solid var(--border); border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: space-between; transition: all .15s ease; flex-shrink: 0; flex-wrap: nowrap; }
        .barangay-group-header:hover { border-color: var(--primary); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .page-body { padding: 1rem; }
            .stat-card { padding: 1rem; }
            .stat-value { font-size: 1.2rem; }
        }

        .pagination { margin: 0; gap: .15rem; }
        .pagination .page-link { border-radius: 6px; border: 1px solid var(--border); color: var(--text); padding: .35rem .65rem; font-size: .8rem; transition: all .15s ease; }
        .pagination .page-link:hover { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination .active .page-link { background: var(--primary); color: white; border-color: var(--primary); }
        .pagination .disabled .page-link { color: #D1D5DB; cursor: not-allowed; }

        .form-select, .form-control { font-size: .85rem; }
        .form-select:focus, .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(26,35,126,.1); }

        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: var(--primary-dark); }

        .loading-overlay { position: absolute; inset: 0; background: rgba(255,255,255,.7); display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: 16px; }
        .table-wrapper { position: relative; min-height: 200px; flex: 1; overflow: hidden; display: flex; flex-direction: column; }

        .avatar-sm { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: .6rem; font-weight: 700; color: white; }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand"><i class="fas fa-user-friends"></i><span>Senior Citizen</span></div>
    <ul class="sidebar-menu">
        <li><a href="/admin/senior"><i class="fas fa-user-friends"></i> Dashboard</a></li>
        <li><a href="/admin/senior/registration"><i class="fas fa-user-plus"></i> Registration</a></li>
        <li><a href="/admin/senior/masterlist"><i class="fas fa-list"></i> Masterlist</a></li>
        <li><a href="/admin/senior/birthdays" class="active"><i class="fas fa-birthday-cake"></i> Birthday Beneficiaries</a></li>
        <li><a href="/admin/senior/birthday-payouts"><i class="fas fa-money-bill-wave"></i> Birthday Payouts</a></li>
        <li><a href="/admin/senior/birthday-payouts/history"><i class="fas fa-history"></i> Payout History</a></li>
        <li><a href="/admin/senior/statistics"><i class="fas fa-chart-bar"></i> Statistics</a></li>
        <li><a href="/admin/senior/reports"><i class="fas fa-file-alt"></i> Reports</a></li>
        <li><a href="/admin/senior/archive"><i class="fas fa-archive"></i> Archive</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <nav class="top-navbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link d-md-none p-0" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <div>
                <p class="page-title">Birthday Beneficiaries</p>
                <p class="breadcrumb-nav"><a href="/admin/senior">Dashboard</a> / Birthday Beneficiaries</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div id="currentDateTime" class="text-muted small d-none d-md-block"></div>
            <div style="width: 34px; height: 34px; font-size: .85rem; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">{{ strtoupper(substr(session('admin_user_name') ?? 'Admin', 0, 2)) }}</div>
        </div>
    </nav>

    <div class="page-body">

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card" onclick="applyFilter('today')">
                    <div class="stat-icon" style="background: rgba(220,38,38,.1); color: var(--danger);"><i class="fas fa-birthday-cake"></i></div>
                    <div>
                        <p class="stat-label">Today's Birthdays</p>
                        <h3 class="stat-value" id="statToday">{{ $todayCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card" onclick="applyFilter('week')">
                    <div class="stat-icon" style="background: rgba(251,192,45,.15); color: var(--warning);"><i class="fas fa-calendar-week"></i></div>
                    <div>
                        <p class="stat-label">This Week</p>
                        <h3 class="stat-value" id="statWeek">{{ $weekCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card" onclick="applyFilter('nextmonth')">
                    <div class="stat-icon" style="background: rgba(26,35,126,.08); color: var(--primary);"><i class="fas fa-calendar-alt"></i></div>
                    <div>
                        <p class="stat-label">Next Month</p>
                        <h3 class="stat-value" id="statNextMonth">{{ $nextMonthCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card" onclick="applyFilter('all')">
                    <div class="stat-icon" style="background: rgba(5,150,105,.1); color: var(--success);"><i class="fas fa-users"></i></div>
                    <div>
                        <p class="stat-label">Total (30 Days)</p>
                        <h3 class="stat-value" id="statTotal">{{ $total }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="filter-section">
            <div class="filter-chips" id="filterChips">
                <span class="filter-chip active" data-filter="all" onclick="applyFilter('all')"><i class="fas fa-calendar"></i> This Month</span>
                <span class="filter-chip" data-filter="today" onclick="applyFilter('today')"><i class="fas fa-birthday-cake"></i> Today</span>
                <span class="filter-chip" data-filter="week" onclick="applyFilter('week')"><i class="fas fa-calendar-week"></i> This Week</span>
                <span class="filter-chip" data-filter="nextmonth" onclick="applyFilter('nextmonth')"><i class="fas fa-calendar-alt"></i> Next Month</span>
            </div>
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Search</label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Name, control no., barangay..." onkeyup="debounceSearch()">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Barangay</label>
                    <select id="barangayFilter" class="form-select" onchange="loadData()">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Month</label>
                    <select id="monthFilter" class="form-select" onchange="loadData()">
                        <option value="">All Months</option>
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Sort By</label>
                    <select id="sortField" class="form-select" onchange="loadData()">
                        <option value="birth_date">Birthday</option>
                        <option value="full_name">Last Name</option>
                        <option value="barangay">Barangay</option>
                        <option value="control_number">Control No.</option>
                        <option value="age">Age</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-semibold text-muted mb-1">Order</label>
                    <select id="sortDir" class="form-select" onchange="loadData()">
                        <option value="asc">Asc</option>
                        <option value="desc">Desc</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <label class="form-label small fw-semibold text-muted mb-1">&nbsp;</label>
                    <button class="btn btn-sm w-100" style="background: var(--primary); color: white; border: none; border-radius: 8px; font-size: .8rem;" onclick="loadData()"><i class="fas fa-search me-1"></i> Search</button>
                    <button class="btn btn-sm" style="border: 1px solid var(--border); border-radius: 8px; font-size: .8rem;" onclick="resetFilters()"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small" id="resultCount">Loading...</span>
                <select class="form-select form-select-sm" style="width: auto;" onchange="loadData()" id="perPageSelect">
                    <option value="15">15 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
            <div class="d-flex gap-1">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary active" id="viewTable" onclick="setView('table')" style="font-size: .8rem;"><i class="fas fa-table"></i></button>
                    <button class="btn btn-outline-secondary" id="viewGrouped" onclick="setView('grouped')" style="font-size: .8rem;"><i class="fas fa-layer-group"></i></button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm" style="background: var(--primary); color: white; border: none; border-radius: 8px; font-size: .8rem;" data-bs-toggle="dropdown"><i class="fas fa-download me-1"></i> Export</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item small" href="{{ route('admin.senior.birthdays.export.pdf') }}" target="_blank"><i class="fas fa-file-pdf me-2 text-danger"></i>PDF</a></li>
                        <li><a class="dropdown-item small" href="{{ route('admin.senior.birthdays.export.csv') }}"><i class="fas fa-file-csv me-2 text-success"></i>CSV</a></li>
                        <li><a class="dropdown-item small" href="{{ route('admin.senior.birthdays.print') }}" target="_blank"><i class="fas fa-print me-2 text-muted"></i>Print</a></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Table View --}}
        <div class="main-card" id="tableView">
            <div class="table-wrapper">
                <div class="table-responsive" style="flex: 1; overflow-y: auto; min-height: 0;">
                    <table class="table" id="birthdayTable">
                        <thead>
                            <tr>
                                <th style="width: 4%;">#</th>
                                <th style="width: 11%;">Control No.</th>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 18%;">Full Name</th>
                                <th style="width: 10%;">Birth Date</th>
                                <th style="width: 7%;">Age</th>
                                <th style="width: 7%;">Turning</th>
                                <th style="width: 10%;">Barangay</th>
                                <th style="width: 11%;">Contact</th>
                                <th style="width: 10%;">Countdown</th>
                                <th style="width: 7%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr><td colspan="11" class="text-center py-5 text-muted">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="paginationWrapper" class="p-3 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted" id="paginationInfo"></small>
                    <nav><ul class="pagination pagination-sm" id="paginationLinks"></ul></nav>
                </div>
            </div>
        </div>

        {{-- Barangay Grouped View --}}
        <div class="main-card" id="groupedView" style="display: none; padding: 0;">
            <div id="groupedContent" style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                <div class="text-center py-5 text-muted">Loading...</div>
            </div>
        </div>

    </div>
</div>

{{-- Profile Modal --}}
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,.12);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border); background: var(--primary); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="fas fa-user-circle me-2"></i>Beneficiary Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4" id="profileContent">
                    <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentPage = 1;
    let currentFilter = 'all';
    let currentView = 'table';
    let searchTimeout;

    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('show'); }

    function updateDateTime() {
        const n = new Date();
        const el = document.getElementById('currentDateTime');
        if (el) el.textContent = n.toLocaleDateString('en-PH', { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' });
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadData, 400);
    }

    function applyFilter(filter) {
        currentFilter = filter;
        document.querySelectorAll('.filter-chip').forEach(c => c.classList.toggle('active', c.dataset.filter === filter));
        if (filter === 'all') { document.getElementById('monthFilter').value = ''; }
        loadData();
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('barangayFilter').value = '';
        document.getElementById('monthFilter').value = '';
        currentFilter = 'all';
        document.querySelectorAll('.filter-chip').forEach(c => c.classList.toggle('active', c.dataset.filter === 'all'));
        loadData();
    }

    function setView(view) {
        currentView = view;
        document.getElementById('viewTable').classList.toggle('active', view === 'table');
        document.getElementById('viewGrouped').classList.toggle('active', view === 'grouped');
        document.getElementById('tableView').style.display = view === 'table' ? '' : 'none';
        document.getElementById('groupedView').style.display = view === 'grouped' ? '' : 'none';
        if (view === 'grouped') loadGroupedData();
        else loadData();
    }

    function loadData(page) {
        if (page) currentPage = page;
        const params = new URLSearchParams({
            page: currentPage,
            filter: currentFilter,
            search: document.getElementById('searchInput').value,
            barangay: document.getElementById('barangayFilter').value,
            month: document.getElementById('monthFilter').value,
            sort_field: document.getElementById('sortField').value,
            sort_dir: document.getElementById('sortDir').value,
            per_page: document.getElementById('perPageSelect').value,
        });

        fetch(`{{ route('admin.senior.birthdays.data') }}?${params}`)
            .then(r => r.json())
            .then(res => {
                renderTable(res.data);
                renderPagination(res);
                document.getElementById('resultCount').textContent = `Showing ${res.from || 0}-${res.to || 0} of ${res.total} beneficiaries`;
            });
    }

    function renderTable(data) {
        const tbody = document.getElementById('tableBody');
        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="11" class="text-center py-5 text-muted"><i class="fas fa-calendar-check" style="font-size: 2.5rem; display: block; margin-bottom: .75rem; opacity: .3;"></i>No birthday beneficiaries found.</td></tr>`;
            return;
        }
        tbody.innerHTML = data.map((s, i) => {
            const countdownHtml = s.is_today
                ? `<span class="countdown-badge today"><i class="fas fa-gift"></i> Birthday Today!</span>`
                : s.days_left <= 7
                    ? `<span class="countdown-badge week"><i class="fas fa-exclamation-circle"></i> ${s.days_left} day${s.days_left !== 1 ? 's' : ''}</span>`
                    : `<span class="countdown-badge soon"><i class="far fa-clock"></i> ${s.days_left} days</span>`;

            const initial = s.full_name ? s.full_name.charAt(0).toUpperCase() : '?';

            return `<tr>
                <td style="color: #9CA3AF; font-weight: 600;">${i + 1}</td>
                <td><strong style="font-size: .8rem;">${s.control_number}</strong></td>
                <td><span style="font-size: .75rem; color: var(--muted);">${s.osca_id}</span></td>
                <td><div class="d-flex align-items-center gap-2"><span class="avatar-circle">${initial}</span><strong>${s.full_name}</strong></div></td>
                <td><span style="font-size: .82rem;">${s.birth_date_formatted}</span></td>
                <td><strong>${s.current_age}</strong></td>
                <td><span class="badge" style="background: rgba(26,35,126,.08); color: var(--primary);">${s.age_turning}</span></td>
                <td>${s.barangay !== '-' ? `<span class="badge" style="background: rgba(26,35,126,.08); color: var(--primary); font-weight: 500;">${s.barangay}</span>` : '<span class="text-muted">-</span>'}</td>
                <td>${s.contact_number !== '-' ? `<a href="tel:${s.contact_number}" style="color: var(--primary); text-decoration: none; font-size: .82rem;">${s.contact_number}</a>` : '<span class="text-muted">-</span>'}</td>
                <td>${countdownHtml}</td>
                <td><button class="btn btn-sm" style="background: var(--primary); color: white; border: none; border-radius: 6px; padding: .25rem .55rem; font-size: .75rem;" onclick="viewProfile(${s.id})"><i class="fas fa-eye"></i></button></td>
            </tr>`;
        }).join('');
    }

    function renderPagination(res) {
        const info = document.getElementById('paginationInfo');
        info.textContent = `Page ${res.current_page} of ${res.last_page}`;

        const ul = document.getElementById('paginationLinks');
        let html = '';
        const lp = res.last_page;
        const cp = res.current_page;

        html += `<li class="page-item ${cp <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadData(${cp - 1}); return false;"><i class="fas fa-chevron-left"></i></a></li>`;

        let start = Math.max(1, cp - 2);
        let end = Math.min(lp, cp + 2);
        if (start > 1) { html += `<li class="page-item"><a class="page-link" href="#" onclick="loadData(1); return false;">1</a></li>${start > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}`; }
        for (let i = start; i <= end; i++) { html += `<li class="page-item ${i === cp ? 'active' : ''}"><a class="page-link" href="#" onclick="loadData(${i}); return false;">${i}</a></li>`; }
        if (end < lp) { html += `${end < lp - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}<li class="page-item"><a class="page-link" href="#" onclick="loadData(${lp}); return false;">${lp}</a></li>`; }

        html += `<li class="page-item ${cp >= lp ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadData(${cp + 1}); return false;"><i class="fas fa-chevron-right"></i></a></li>`;
        ul.innerHTML = html;
    }

    function loadGroupedData() {
        const container = document.getElementById('groupedContent');
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

        fetch(`{{ route('admin.senior.birthdays.by-barangay') }}`)
            .then(r => r.json())
            .then(data => {
                if (!data || data.length === 0) {
                    container.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-calendar-check" style="font-size: 2.5rem; display: block; margin-bottom: .75rem; opacity: .3;"></i>No data to group.</div>';
                    return;
                }
                container.innerHTML = data.map(g => `
                    <div class="barangay-group">
                        <div class="barangay-group-header" onclick="this.nextElementSibling.classList.toggle('d-none'); this.querySelector('.chevron').classList.toggle('fa-chevron-down'); this.querySelector('.chevron').classList.toggle('fa-chevron-up');">
                            <div class="d-flex align-items-center gap-2" style="flex: 1; min-width: 0;">
                                <i class="fas fa-map-pin" style="color: var(--primary); flex-shrink: 0;"></i>
                                <strong style="flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${g.barangay}</strong>
                                <span class="badge" style="background: var(--primary); color: white; flex-shrink: 0;">${g.count} beneficiary${g.count !== 1 ? 'ies' : 'y'}</span>
                            </div>
                            <i class="fas fa-chevron-down chevron" style="color: #9CA3AF; flex-shrink: 0;"></i>
                        </div>
                        <div class="mt-2 d-none">
                            <table class="table table-sm">
                                <thead><tr><th style="width: 5%;">#</th><th>Full Name</th><th>Birth Date</th><th>Countdown</th></tr></thead>
                                <tbody>
                                    ${g.seniors.map((s, i) => {
                                        const cd = s.is_today ? '<span class="countdown-badge today"><i class="fas fa-gift"></i> Today!</span>' : `<span class="countdown-badge soon">${s.days_left} days</span>`;
                                        return `<tr><td style="color: #9CA3AF;">${i + 1}</td><td><strong>${s.full_name}</strong></td><td>${s.birth_date}</td><td>${cd}</td></tr>`;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `).join('');
            });
    }

    function viewProfile(id) {
        const modal = new bootstrap.Modal(document.getElementById('profileModal'));
        const content = document.getElementById('profileContent');
        content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
        modal.show();

        fetch(`{{ route('admin.senior.birthdays.profile', 0) }}`.replace('/0', `/${id}`))
            .then(r => r.json())
            .then(d => {
                const cdHtml = d.is_today
                    ? '<span class="countdown-badge today"><i class="fas fa-gift"></i> Birthday Today!</span>'
                    : `<span class="countdown-badge soon"><i class="far fa-clock"></i> ${d.days_left} days remaining</span>`;

                content.innerHTML = `
                    <div class="col-md-6">
                        <div class="mb-3"><small class="text-muted fw-semibold">Control Number</small><p class="fw-bold mb-0">${d.control_number}</p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Senior Citizen ID</small><p class="mb-0">${d.osca_id}</p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Full Name</small><p class="fw-bold mb-0" style="font-size: 1.1rem;">${d.full_name}</p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Address</small><p class="mb-0">${d.address}</p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Barangay</small><p class="mb-0"><span class="badge" style="background: rgba(26,35,126,.1); color: var(--primary);">${d.barangay}</span></p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Contact Number</small><p class="mb-0">${d.contact_number}</p></div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3"><small class="text-muted fw-semibold">Birth Date</small><p class="fw-bold mb-0">${d.birth_date}</p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Current Age</small><p class="mb-0"><strong>${d.current_age}</strong> years old</p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Age Turning</small><p class="mb-0"><span class="badge" style="background: var(--primary); color: white;">${d.age_turning} years</span></p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Sex</small><p class="mb-0">${d.sex}</p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Birth Month</small><p class="mb-0">${d.month}</p></div>
                        <div class="mb-3"><small class="text-muted fw-semibold">Days Remaining</small><p class="mb-0">${cdHtml}</p></div>
                        <div class="mb-0"><small class="text-muted fw-semibold">PhilSys / RRN</small><p class="mb-0" style="font-size: .82rem;">${d.philsys_number} / ${d.rrn_number}</p></div>
                    </div>
                    ${d.remarks && d.remarks !== '-' ? `<div class="col-12"><hr><small class="text-muted fw-semibold">Remarks</small><p class="mb-0">${d.remarks}</p></div>` : ''}
                `;
            })
            .catch(() => {
                content.innerHTML = '<div class="col-12 text-center py-4 text-danger">Failed to load profile.</div>';
            });
    }

    function confirmLogout(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?', text: 'Do you really want to log out?',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#1A237E', cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, log out', cancelButtonText: 'Cancel',
            background: '#ffffff', customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(r => { if (r.isConfirmed) document.getElementById('logout-form').submit(); });
    }

    loadData();
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>
