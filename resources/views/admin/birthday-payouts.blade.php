<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Birthday Payout List - MSWDO Silang</title>
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

        .page-body { padding: 2rem; flex: 1; overflow: hidden; display: flex; flex-direction: column; }

        .stat-card { background: var(--cards); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,.04); padding: 1rem; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; transition: all .2s ease; height: 100%; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,.08); border-color: var(--primary); }
        .stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
        .stat-value { font-size: 1.4rem; font-weight: 700; margin: 0; line-height: 1.2; }
        .stat-label { font-size: .7rem; color: var(--muted); margin: 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

        .filter-section { background: var(--cards); border-radius: 16px; border: 1px solid var(--border); padding: 1rem; margin-bottom: 1rem; }

        .main-card { background: var(--cards); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,.04); overflow: hidden; flex: 1; display: flex; flex-direction: column; }

        .table { margin-bottom: 0; }
        .table thead th { background: var(--cards); border-bottom: 2px solid var(--border); font-weight: 600; font-size: .8rem; color: var(--text); padding: .7rem .6rem; white-space: nowrap; position: sticky; top: 0; z-index: 2; }
        .table tbody td { border-bottom: 1px solid var(--border); padding: .6rem; font-size: .85rem; color: var(--text); vertical-align: middle; }
        .table tbody tr:hover { background-color: #F9FAFB; }

        .badge { padding: .3rem .55rem; font-size: .7rem; font-weight: 600; border-radius: 6px; }
        .badge-pending { background: rgba(245,158,11,.1); color: var(--warning); }
        .badge-released { background: rgba(5,150,105,.1); color: var(--success); }
        .badge-cancelled { background: rgba(220,38,38,.1); color: var(--danger); }

        .form-select, .form-control { font-size: .85rem; }
        .form-select:focus, .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(26,35,126,.1); }

        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: var(--primary-dark); }

        .btn-sm { padding: .35rem .7rem; font-size: .8rem; }

        .table-wrapper { position: relative; min-height: 200px; flex: 1; overflow: hidden; display: flex; flex-direction: column; }
        .table-responsive { flex: 1; overflow-y: auto; min-height: 0; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .page-body { padding: 1rem; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-user-friends"></i>
            <span>Senior Citizen</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.senior') }}"><i class="fas fa-user-friends"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.senior.registration') }}"><i class="fas fa-user-plus"></i> Registration</a></li>
            <li><a href="{{ route('admin.senior.masterlist') }}"><i class="fas fa-list"></i> Masterlist</a></li>
            <li><a href="{{ route('admin.senior.birthdays') }}"><i class="fas fa-birthday-cake"></i> Birthday Beneficiaries</a></li>
            <li><a href="{{ route('admin.senior.birthday-payouts') }}" class="active"><i class="fas fa-money-bill-wave"></i> Birthday Payouts</a></li>
            <li><a href="{{ route('admin.senior.birthday-payouts.history') }}"><i class="fas fa-history"></i> Payout History</a></li>
            <li><a href="{{ route('admin.senior.analytics') }}"><i class="fas fa-chart-bar"></i> Statistics</a></li>
            <li><a href="{{ route('admin.senior.reports') }}"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="{{ route('admin.senior.archive.list') }}"><i class="fas fa-archive"></i> Archive</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="page-title mb-0">Birthday Payout List</h5>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3 text-muted small" id="currentDateTime"></div>
                <div class="avatar-circle">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin'), 0, 2)) }}</div>
            </div>
        </nav>

        <!-- Page Body -->
        <div class="page-body">
            <!-- Summary Cards -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(26,35,126,0.1); color: var(--primary);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <p class="stat-label">Total Beneficiaries</p>
                            <h3 class="stat-value">{{ $totalBeneficiaries }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(220,38,38,0.1); color: var(--danger);">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <p class="stat-label">Total Budget</p>
                            <h3 class="stat-value">₱{{ number_format($totalBudget, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form id="filterForm" method="GET" action="{{ route('admin.senior.birthday-payouts') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Month</label>
                            <select class="form-select" name="month" id="monthSelect" onchange="document.getElementById('filterForm').submit()">
                                @foreach($months as $month)
                                    <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Year</label>
                            <select class="form-select" name="year" id="yearSelect" onchange="document.getElementById('filterForm').submit()">
                                @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Barangay</label>
                            <select class="form-select" name="barangay" id="barangaySelect" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Search</label>
                            <input type="text" class="form-control" name="search" id="searchInput" placeholder="Search by name..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100" onclick="generatePayoutList()">
                                <i class="fas fa-sync-alt me-1"></i> Generate List
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-primary btn-sm" onclick="printPayoutList()">
                    <i class="fas fa-print me-1"></i> Print List
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="exportPdf()">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </button>
                <button type="button" class="btn btn-success btn-sm" onclick="bulkRelease()">
                    <i class="fas fa-check-double me-1"></i> Release
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="resetPayoutList()">
                    <i class="fas fa-trash-alt me-1"></i> Reset List
                </button>
            </div>

            <!-- Main Card -->
            <div class="main-card">
                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="table" id="payoutTable">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                                    <th style="width: 50px;">#</th>
                                    <th>Control No.</th>
                                    <th>OSCA ID</th>
                                    <th>Full Name</th>
                                    <th>Birthday</th>
                                    <th>Age</th>
                                    <th>Barangay</th>
                                    <th>Contact</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($existingPayouts as $index => $payout)
                                    @php
                                        $senior = $payout->senior;
                                        $age = $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->age : '-';
                                    @endphp
                                    <tr>
                                        <td><input type="checkbox" class="payout-checkbox" value="{{ $payout->id }}"></td>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $senior->control_number ?? '-' }}</td>
                                        <td>{{ $senior->osca_id ?? '-' }}</td>
                                        <td>
                                            <div style="font-weight: 600;">{{ $senior->full_name }}</div>
                                        </td>
                                        <td>{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('F d, Y') : '-' }}</td>
                                        <td>{{ $age }}</td>
                                        <td>{{ $senior->barangay }}</td>
                                        <td>{{ $senior->contact_number ?? '-' }}</td>
                                        <td>₱{{ number_format($payout->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No payout records found. Click "Generate List" to create payout records.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update current date/time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // View payout history
        function viewHistory() {
            window.location.href = '{{ route("admin.senior.birthday-payouts.history") }}';
        }

        // Toggle sidebar
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }

        // Toggle select all checkboxes
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.payout-checkbox:not(:disabled)');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }

        // Generate payout list
        function generatePayoutList() {
            const month = document.getElementById('monthSelect').value;
            const year = document.getElementById('yearSelect').value;
            const barangay = document.getElementById('barangaySelect').value;

            Swal.fire({
                title: 'Generate Payout List',
                text: `Generate payout list for ${month} ${year}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Generate',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#1A237E'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.senior.birthday-payouts.generate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            month: month,
                            year: year,
                            barangay: barangay
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Failed to generate payout list', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An error occurred', 'error');
                    });
                }
            });
        }

        // Release single payout
        function releasePayout(id) {
            Swal.fire({
                title: 'Release Payout',
                input: 'textarea',
                inputLabel: 'Remarks (optional)',
                inputPlaceholder: 'Enter any remarks...',
                showCancelButton: true,
                confirmButtonText: 'Release',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#059669'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('admin.senior.birthday-payouts.release', ':id') }}`.replace(':id', id), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            remarks: result.value || null
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Failed to release payout', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An error occurred', 'error');
                    });
                }
            });
        }

        // Bulk release
        function bulkRelease() {
            const selectedIds = Array.from(document.querySelectorAll('.payout-checkbox:checked:not(:disabled)')).map(cb => cb.value);
            
            if (selectedIds.length === 0) {
                Swal.fire('Warning', 'Please select at least one pending payout', 'warning');
                return;
            }

            Swal.fire({
                title: 'Bulk Release',
                text: `Release ${selectedIds.length} payout(s)?`,
                input: 'textarea',
                inputLabel: 'Remarks (optional)',
                inputPlaceholder: 'Enter any remarks...',
                showCancelButton: true,
                confirmButtonText: 'Release All',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#059669'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("admin.senior.birthday-payouts.bulk-release") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            payout_ids: selectedIds,
                            remarks: result.value || null
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Failed to generate payout list', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An error occurred', 'error');
                    });
                }
            });
        }

        // Reset payout list
        function resetPayoutList() {
            const month = document.getElementById('monthSelect').value;
            const year = document.getElementById('yearSelect').value;

            Swal.fire({
                title: 'Reset Payout List',
                text: 'Choose reset option:',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Reset Selected Month',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6B7280',
                showDenyButton: true,
                denyButtonText: 'Reset All Records',
                denyButtonColor: '#DC2626'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reset by selected month/year
                    fetch('{{ route("admin.senior.birthday-payouts.reset") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            month: month,
                            year: year,
                            reset_all: false
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.error || 'Failed to reset payout list', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'An error occurred', 'error');
                    });
                } else if (result.isDenied) {
                    // Reset all records
                    Swal.fire({
                        title: 'Reset All Records',
                        text: 'This will delete ALL payout records from the database. This action cannot be undone. Are you absolutely sure?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Delete All',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#DC2626',
                        cancelButtonColor: '#6B7280'
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            fetch('{{ route("admin.senior.birthday-payouts.reset") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    reset_all: true
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Success', data.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', data.error || 'Failed to reset payout list', 'error');
                                }
                            })
                            .catch(error => {
                                Swal.fire('Error', 'An error occurred', 'error');
                            });
                        }
                    });
                }
            });
        }

        // Print payout list
        function printPayoutList() {
            const month = document.getElementById('monthSelect').value;
            const year = document.getElementById('yearSelect').value;
            const barangay = document.getElementById('barangaySelect').value;
            
            const url = `{{ route('admin.senior.birthday-payouts.print') }}?month=${month}&year=${year}&barangay=${barangay}`;
            window.open(url, '_blank');
        }

        // Export PDF
        function exportPdf() {
            const month = document.getElementById('monthSelect').value;
            const year = document.getElementById('yearSelect').value;
            const barangay = document.getElementById('barangaySelect').value;
            
            const url = `{{ route('admin.senior.birthday-payouts.export-pdf') }}?month=${month}&year=${year}&barangay=${barangay}`;
            window.open(url, '_blank');
        }

        // Export Excel
        function exportExcel() {
            const month = document.getElementById('monthSelect').value;
            const year = document.getElementById('yearSelect').value;
            const barangay = document.getElementById('barangaySelect').value;
            
            const url = `{{ route('admin.senior.birthday-payouts.export-excel') }}?month=${month}&year=${year}&barangay=${barangay}`;
            window.open(url, '_blank');
        }

        // Print receipt
        function printReceipt(id) {
            const url = `{{ route('admin.senior.birthday-payouts.receipt', ':id') }}`.replace(':id', id);
            window.open(url, '_blank');
        }

        // View profile
        function viewProfile(id) {
            window.location.href = `{{ route('admin.senior.profile', ':id') }}`.replace(':id', id);
        }

        // Logout
        function confirmLogout(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Logout',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#DC2626'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('admin.logout') }}';
                }
            });
        }
    </script>
</body>
</html>
