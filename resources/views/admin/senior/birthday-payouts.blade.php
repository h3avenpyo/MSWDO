<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Birthday Payout List - MSWDO Silang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    fontFamily: { sans: ["'Public Sans'", '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Helvetica', 'Arial', 'sans-serif'] }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1A237E;
            --primary-hover: #121858;
            --sidebar-bg: #1A237E;
            --accent-yellow: #FBC02D;
            --background: #F5F7FB;
            --surface: #FFFFFF;
            --border: #E5E7EB;
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            --success: #16A34A;
            --success-bg: #ECFDF5;
            --danger: #DC2626;
            --danger-bg: #FEF2F2;
            --info: #3B82F6;
            --info-bg: #EEF2FF;
            --purple: #7C3AED;
            --shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            --font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100vh; overflow: hidden; background: var(--background); color: var(--text-primary); font-family: var(--font-family); }
        body { font-size: 14px; line-height: 1.5; }

        /* Sidebar */
        .sidebar{width:260px;flex-shrink:0;background:var(--primary);color:#FFFFFF;position:fixed;left:0;top:0;height:100vh;z-index:1000;display:flex;flex-direction:column;transition:transform .3s ease;}
        .sidebar-brand{height:72px;padding:0 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:.65rem;}
        .sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:var(--accent-yellow);}
        .sidebar-menu{list-style:none;margin:0;padding:1rem 0;flex:1;}
        .sidebar-menu li{margin-bottom:.2rem;}
        .sidebar-menu a{color:rgba(255,255,255,.75);padding:.75rem 1.5rem;display:flex;align-items:center;gap:.75rem;text-decoration:none;font-size:.9rem;border-left:3px solid transparent;transition:all .2s ease;}
        .sidebar-menu a:hover{background:rgba(255,255,255,.1);color:var(--accent-yellow);}
        .sidebar-menu a.active{background:rgba(255,255,255,.1);color:var(--accent-yellow);border-left-color:var(--accent-yellow);}
        .sidebar-menu a i,.sidebar-menu a [data-lucide]{width:20px;height:20px;text-align:center;}

        /* Main Content */
        .main-content {
            flex: 1; min-width: 0; margin-left: 260px; padding: 32px;
            max-width: calc(100% - 260px); height: 100vh;
            display: flex; flex-direction: column; overflow: hidden;
            animation: fadeIn .3s ease;
        }

        /* ---------- Buttons ---------- */
        .btn {
            border: 1px solid var(--border); background: var(--surface);
            color: var(--text-primary); padding: 10px 20px; border-radius: 10px;
            font-size: 14px; font-weight: 500; display: inline-flex;
            align-items: center; gap: 8px; box-shadow: var(--shadow);
            transition: all 0.2s ease; height: 42px; cursor: pointer; text-decoration: none;
        }
        .btn:hover { border-color: var(--primary); transform: translateY(-1px); }
        .btn.primary { background: var(--primary); color: #FFFFFF; border-color: var(--primary); }
        .btn.primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
        .btn.danger { background: var(--danger); color: #FFFFFF; border-color: var(--danger); }
        .btn.danger:hover { background: #B91C1C; border-color: #B91C1C; }
        .btn.success { background: var(--success); color: #FFFFFF; border-color: var(--success); }
        .btn.success:hover { background: #15803D; border-color: #15803D; }
        .btn.ghost { background: transparent; box-shadow: none; border-color: transparent; color: var(--text-secondary); }
        .btn.ghost:hover { background: var(--background); color: var(--text-primary); }
        .btn:disabled { opacity: 0.45; cursor: not-allowed; pointer-events: none; }
        .btn-sm { padding: 6px 12px; font-size: 13px; height: 36px; }

        /* ---------- Table Card ---------- */
        .table-card {
            background: var(--surface); border-radius: 16px;
            border: 1px solid var(--border); box-shadow: var(--shadow);
            padding: 2rem; display: flex; flex-direction: column;
            overflow: hidden; flex: 1; min-height: 0;
        }
        .table-card-title {
            font-size: 1.25rem; font-weight: 700; color: var(--text-primary);
            margin-top: 0; margin-bottom: 1.5rem; flex-shrink: 0;
        }

        /* ---------- Filter Section ---------- */
        .filter-section { margin-bottom: 1.5rem; flex-shrink: 0; }
        .filter-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .filter-left { display: flex; gap: 12px; flex: 1; min-width: 0; flex-wrap: wrap; }
        .filter-right { display: flex; gap: 12px; flex-shrink: 0; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-group.select-group { min-width: 160px; }
        .filter-label { font-size: 0.75rem; font-weight: 600; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; }

        /* ---------- Search Input ---------- */
        .input-group { display: flex; align-items: center; height: 44px; }
        .input-group input {
            flex: 1; height: 44px; border: 1px solid var(--border); border-right: none;
            border-radius: 6px 0 0 6px; padding: 0 1rem; font-size: 0.875rem;
            color: var(--text-primary); background: var(--surface); transition: all 0.2s ease;
        }
        .input-group input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,58,138,0.15); }
        .input-group .search-btn {
            background-color: var(--primary); color: #ffffff; border: none;
            padding: 0 1.25rem; border-radius: 0 6px 6px 0; cursor: pointer;
            height: 44px; display: flex; align-items: center; justify-content: center;
            transition: background 0.2s;
        }
        .input-group .search-btn:hover { background-color: var(--primary-hover); }

        /* ---------- Select ---------- */
        .filter-select {
            height: 44px; border: 1px solid var(--border); border-radius: 6px;
            padding: 0 2.25rem 0 1rem; font-size: 0.875rem; color: var(--text-primary);
            background: var(--surface); cursor: pointer; width: 100%;
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px 12px;
            transition: all 0.2s ease;
        }
        .filter-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,58,138,0.15); }

        /* Custom Table */
        .custom-table { width: 100%; border-collapse: collapse; }
        .custom-table thead th {
            background: var(--surface);
            border-bottom: 2px solid var(--border);
            font-weight: 700;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-secondary);
            padding: .75rem .65rem;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 2;
        }
        .custom-table tbody td {
            border-bottom: 1px solid var(--border);
            padding: .65rem;
            font-size: .85rem;
            color: var(--text-primary);
            vertical-align: middle;
        }
        .custom-table tbody tr:hover { background-color: #F9FAFB; }
        .custom-table tbody tr:last-child td { border-bottom: none; }

        /* Badge */
        .badge-status { padding: .3rem .65rem; font-size: .72rem; font-weight: 600; border-radius: 6px; display: inline-flex; align-items: center; gap: .3rem; }
        .badge-pending { background: rgba(245,158,11,.1); color: #D97706; }
        .badge-released { background: rgba(22,163,74,.1); color: #16A34A; }
        .badge-cancelled { background: rgba(220,38,38,.1); color: #DC2626; }

        /* Table Scroll */
        .table-scroll { flex: 1; overflow-y: auto; min-height: 0; }
        .table-scroll::-webkit-scrollbar { width: 6px; }
        .table-scroll::-webkit-scrollbar-track { background: transparent; }
        .table-scroll::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 3px; }
        .table-scroll::-webkit-scrollbar-thumb:hover { background: #9CA3AF; }

        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: 4px; margin: 0; list-style: none; padding: 0; }
        .pagination .page-item { margin: 0; }
        .pagination .page-link,
        .pagination .page-item span {
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid var(--border); color: var(--text-primary); padding: 0.375rem 0.75rem;
            border-radius: 6px; text-decoration: none; background: var(--surface);
            transition: all 0.2s; min-width: 40px; text-align: center; font-size: 13px; font-weight: 500;
        }
        .pagination .page-link:hover,
        .pagination .page-item span:hover { background-color: var(--primary); color: white; border-color: var(--primary); }
        .pagination .page-item.active .page-link,
        .pagination .page-item.active span { background-color: var(--primary); color: white; border-color: var(--primary); }
        .pagination .page-item.disabled .page-link,
        .pagination .page-item.disabled span { color: var(--text-muted); background-color: var(--background); border-color: var(--border); cursor: not-allowed; }

        /* Summary Cards */
        .summary-cards { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 1.5rem; flex-shrink: 0; }
        .summary-card {
            background: var(--surface); border-radius: 16px; padding: 20px;
            display: flex; align-items: center; gap: 16px;
            border: 1px solid var(--border); box-shadow: var(--shadow);
            transition: all 0.3s ease; position: relative; overflow: hidden;
        }
        .summary-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; transition: all 0.3s ease; }
        .summary-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,.1); }
        .summary-card.primary::before { background: var(--primary); }
        .summary-card.danger::before { background: var(--danger); }
        .summary-card-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .summary-card-icon [data-lucide] { width: 24px; height: 24px; }
        .summary-card-content { flex: 1; }
        .summary-card-label { font-size: 12px; font-weight: 600; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .summary-card-value { font-size: 1.75rem; font-weight: 700; color: var(--text-primary); line-height: 1; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i data-lucide="users" style="width:24px;height:24px"></i>
            <span>Senior Citizen</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/senior"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
            <li><a href="/admin/senior/registration"><i data-lucide="user-plus" style="width:20px;height:20px"></i> Registration</a></li>
            <li><a href="/admin/senior/masterlist"><i data-lucide="list" style="width:20px;height:20px"></i> Masterlist</a></li>
            <li><a href="/admin/senior/birthdays"><i data-lucide="cake" style="width:20px;height:20px"></i> Birthday Beneficiaries</a></li>
            <li><a href="/admin/senior/birthday-payouts" class="active"><i data-lucide="banknote" style="width:20px;height:20px"></i> Birthday Payouts</a></li>
            <li><a href="/admin/senior/birthday-payouts/history"><i data-lucide="history" style="width:20px;height:20px"></i> Payout History</a></li>
            <li><a href="/admin/senior/statistics"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i> Statistics</a></li>
            <li><a href="/admin/senior/reports"><i data-lucide="file-text" style="width:20px;height:20px"></i> Reports</a></li>
            <li><a href="/admin/senior/archive"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Modern Header -->
        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none flex-shrink-0"
                style="margin-top:-32px;margin-left:-32px;margin-right:-32px;margin-bottom:24px">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Birthday Payouts</h1>
            </div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ session('admin_user_name') ?? 'Admin' }}">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin'), 0, 2)) }}</div>
            </div>
        </header>

        <!-- Table Card -->
        <div class="table-card">
            <h2 class="table-card-title">Payout List</h2>

            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card primary">
                    <div class="summary-card-icon" style="background:rgba(26,35,126,.1);color:var(--primary)">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Total Beneficiaries</div>
                        <div class="summary-card-value">{{ $totalBeneficiaries }}</div>
                    </div>
                </div>
                <div class="summary-card danger">
                    <div class="summary-card-icon" style="background:rgba(220,38,38,.1);color:var(--danger)">
                        <i data-lucide="banknote"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Total Budget</div>
                        <div class="summary-card-value">₱{{ number_format($totalBudget, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form id="filterForm" method="GET" action="{{ route('admin.senior.birthday-payouts') }}">
                    <div class="filter-row">
                        <div class="filter-left">
                            <div class="filter-group select-group">
                                <label class="filter-label">Month</label>
                                <select class="filter-select" name="month" id="monthSelect" onchange="document.getElementById('filterForm').submit()">
                                    @foreach($months as $month)
                                        <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group select-group">
                                <label class="filter-label">Year</label>
                                <select class="filter-select" name="year" id="yearSelect" onchange="document.getElementById('filterForm').submit()">
                                    @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="filter-group select-group">
                                <label class="filter-label">Barangay</label>
                                <select class="filter-select" name="barangay" id="barangaySelect" onchange="document.getElementById('filterForm').submit()">
                                    <option value="">All Barangays</option>
                                    @foreach($barangays as $barangay)
                                        <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group" style="flex:1;min-width:200px">
                                <label class="filter-label">Search</label>
                                <div class="input-group">
                                    <input type="text" name="search" id="searchInput" placeholder="Search by name..." value="{{ $search }}">
                                    <button type="submit" class="search-btn">
                                        <i data-lucide="search" style="width:16px;height:16px"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="filter-right">
                            <button type="button" class="btn primary" onclick="generatePayoutList()">
                                <i data-lucide="refresh-cw" style="width:16px;height:16px"></i> Generate List
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="filter-right" style="margin-bottom:1.5rem;flex-shrink:0">
                <button type="button" class="btn success" style="height:44px;" onclick="bulkRelease()">
                    <i data-lucide="check" style="width:16px;height:16px"></i> Release
                </button>
                <button type="button" class="btn danger" style="height:44px;" onclick="resetPayoutList()">
                    <i data-lucide="x" style="width:16px;height:16px"></i> Reset List
                </button>
                <button type="button" class="btn" style="height:44px;" onclick="printPayoutList()">
                    <i data-lucide="printer" style="width:16px;height:16px"></i> Print List
                </button>
                <button type="button" class="btn" style="height:44px;" onclick="exportPdf()">
                    <i data-lucide="file-output" style="width:16px;height:16px"></i> Export PDF
                </button>
            </div>

            <!-- Table Scroll -->
            <div class="table-scroll" style="border:1px solid var(--border);border-radius:8px;">
                <table class="custom-table" id="payoutTable">
                    <thead>
                        <tr>
                            <th style="width:40px;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-4 h-4 rounded border-gray-300 text-[var(--primary)] focus:ring-[var(--primary)] cursor-pointer"></th>
                            <th style="width:50px;">#</th>
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
                                <td><input type="checkbox" class="payout-checkbox w-4 h-4 rounded border-gray-300 text-[var(--primary)] focus:ring-[var(--primary)] cursor-pointer" value="{{ $payout->id }}"></td>
                                <td>{{ $existingPayouts->firstItem() + $index }}</td>
                                <td>{{ $senior->control_number ?? '-' }}</td>
                                <td>{{ $senior->osca_id ?? '-' }}</td>
                                <td>
                                    <div class="font-semibold">{{ $senior->full_name }}</div>
                                </td>
                                <td>{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('F d, Y') : '-' }}</td>
                                <td>{{ $age }}</td>
                                <td>{{ $senior->barangay }}</td>
                                <td>{{ $senior->contact_number ?? '-' }}</td>
                                <td>₱{{ number_format($payout->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="inbox" style="width:48px;height:48px" class="text-[var(--text-muted)] mb-3"></i>
                                        <p class="text-[var(--text-secondary)] mb-0 text-sm">No payout records found. Click "Generate List" to create payout records.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($existingPayouts->hasPages())
            <div style="display:flex;justify-content:center;margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--border);">
                {{ $existingPayouts->appends(['month' => $selectedMonth, 'year' => $selectedYear, 'barangay' => $selectedBarangay, 'search' => $search])->links('vendor.pagination.custom') }}
            </div>
            @endif
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Update current date/time
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

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
                cancelButtonColor: '#EF4444',
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
                        cancelButtonColor: '#EF4444'
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
                title: 'Are you sure?',
                text: 'Do you really want to log out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#EF4444',
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
    </script>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>