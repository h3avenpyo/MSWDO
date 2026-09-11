<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>In-Between Birthday Cash Gift - Benefit History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;
            --primary-hover:#121858;
            --primary-dark:#121858;
            --sidebar-bg:#1A237E;
            --accent-yellow:#FBC02D;
            --background:#F1F5F9;
            --surface:#FFFFFF;
            --border:#E5E7EB;
            --text-primary:#111827;
            --text-secondary:#6B7280;
            --text-muted:#9CA3AF;
            --success:#16A34A;
            --success-bg:#ECFDF5;
            --danger:#DC2626;
            --danger-bg:#FEF2F2;
            --info:#3B82F6;
            --info-bg:#EEF2FF;
            --purple:#7C3AED;
            --purple-bg:#F3E8FF;
            --icon-blue:#3B82F6;
            --icon-green:#16A34A;
            --icon-purple:#7C3AED;
            --sidebar-width:260px;
            --content-padding:32px;
            --shadow:0 10px 30px rgba(15,23,42,.08);
            --shadow-hover:0 20px 40px rgba(15,23,42,.12);
            --font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        .main{display:flex;min-height:100vh;}
        .sidebar{width:var(--sidebar-width);background:var(--sidebar-bg);color:white;position:fixed;top:0;left:0;bottom:0;overflow-y:auto;z-index:50;transition:transform .3s ease;}
        .sidebar-brand{height:72px;display:flex;align-items:center;padding:0 20px;border-bottom:1px solid rgba(255,255,255,.1);}
        .sidebar-brand img{width:56px;height:56px;object-fit:contain;}
        .sidebar-brand span{font-size:18px;font-weight:600;margin-left:12px;}
        .sidebar-menu{list-style:none;padding:20px 0;margin:0;}
        .sidebar-menu li{margin:0;}
        .sidebar-menu a{display:flex;align-items:center;padding:12px 20px;color:rgba(255,255,255,.8);text-decoration:none;transition:all .2s ease;}
        .sidebar-menu a:hover{background:rgba(255,255,255,.1);color:white;}
        .sidebar-menu a.active{background:rgba(255,255,255,.15);color:white;font-weight:500;}
        .sidebar-menu i{margin-right:12px;}
        .main-content{flex:1;margin-left:var(--sidebar-width);padding:var(--content-padding);min-height:100vh;}
        .page-header{margin-bottom:24px;}
        .page-header h1{font-size:28px;font-weight:700;margin:0 0 8px 0;color:var(--text-primary);}
        .page-header p{margin:0;color:var(--text-secondary);}
        .analytics-card{background:var(--surface);border-radius:16px;padding:24px;box-shadow:var(--shadow);border:1px solid var(--border);margin-bottom:20px;}
        .form-input{font-family:var(--font-family);}

        /* ── Pagination ── */
        .sc-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; flex-shrink: 0; padding: 4px 0; flex-wrap: wrap; }
        .sc-pagination-info { font-size: 0.813rem; color: #6B7280; font-weight: 500; }
        .sc-pagination-controls { display: flex; gap: 4px; flex-wrap: wrap; }
        .sc-page-btn { height: 36px; min-width: 36px; padding: 0 10px; border: 1px solid #E5E7EB; border-radius: 6px; background: #fff; color: #374151; font-size: 0.813rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; text-decoration: none; }
        .sc-page-btn:hover:not(:disabled) { background: #F3F4F6; border-color: #D1D5DB; }
        .sc-page-btn.active { background: #1A237E; color: #fff; border-color: #1A237E; font-weight: 700; }
        .sc-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* ── Table (Masterlist style) ── */
        .archive-table-wrap{border:2px solid #CBD5E1;border-radius:8px;overflow-x:auto;-webkit-overflow-scrolling:touch;max-height: 500px; overflow-y: auto;}
        .archive-table{width:100%;border-collapse:collapse;font-size:14px;}
        .archive-table thead th{padding:14px 16px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#1E293B;text-align:left;border-bottom:2px solid #94A3B8;background:#E2E8F0;white-space:nowrap;position: sticky; top: 0; z-index: 10;}
        .archive-table tbody td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid #CBD5E1;vertical-align:middle;white-space:normal;word-break:break-word;}
        .archive-table tbody tr:last-child td{border-bottom:none;}
        .td-sub{font-size:0.75rem;color:var(--text-secondary);margin-top:2px;}

        /* ── Badges ── */
        .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:500;white-space:nowrap;}
        .badge-pending{background:#FEF3C7;color:#92400E;}
        .badge-approved{background:var(--info-bg);color:var(--info);}
        .badge-released{background:var(--success-bg);color:var(--success);}
        .badge-rejected{background:var(--danger-bg);color:var(--danger);}
        .badge-cancelled{background:#F3F4F6;color:#6B7280;}

        /* ── Action buttons (Flat Design) ── */
        .actions{display:flex;gap:6px;align-items:center;}
        .action-btn{width:34px !important;height:34px !important;min-height:34px !important;max-height:34px !important;padding:0 !important;display:inline-flex !important;align-items:center !important;justify-content:center !important;border-radius:8px !important;box-shadow:none !important;cursor:pointer;transition:background .15s ease, border-color .15s ease;}
        .action-btn:hover{transform:none;}
        .action-btn svg, .action-btn i{width:16px !important;height:16px !important;}

        /* ── Empty State ── */
        .empty-row{background:transparent !important;border:none !important;box-shadow:none !important;padding:0 !important;margin:0 !important;}
        .empty-cell{padding:2.5rem 1rem !important;border:none !important;display:flex !important;flex-direction:column !important;align-items:center !important;justify-content:center !important;width:100% !important;}
        .empty-cell::before{display:none !important;}
        .empty-state-content{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;}
        .empty-icon-wrap{width:64px;height:64px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:#9CA3AF;}
        .empty-icon-wrap svg{width:32px;height:32px;}
        .empty-title{font-size:1.125rem;font-weight:700;color:#1F2937;margin-bottom:4px;}
        .empty-subtitle{font-size:0.875rem;color:#6B7280;}

        @media (max-width: 767.98px) {
            .sc-pagination { position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important; background: #fff !important; padding: 15px 0 !important; z-index: 100 !important; border-top: 1px solid #E5E7EB !important; flex-direction: column; align-items: center; gap: 8px; }
            .sc-pagination-controls { justify-content: flex-end; padding-right: 20px; }
            .archive-table-wrap{border:none;border-radius:0;overflow:visible;}
            .archive-table thead{display:none;}
            .archive-table tbody tr{display:block;background:var(--surface);border:1px solid #D1D5DB;border-radius:10px;margin-bottom:10px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
            .archive-table tbody tr:last-child{margin-bottom:0;}
            .archive-table tbody td{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border:none;font-size:.82rem;gap:8px;text-align:right;}
            .archive-table tbody td:not(:last-child){border-bottom:1px solid var(--border);}
            .archive-table tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:.72rem;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:80px;text-align:left;}
            .archive-table tbody td[data-label="Actions"]{justify-content:flex-end;padding-top:8px;border-bottom:none;}
            .archive-table tbody td[data-label="Actions"]::before{display:none;}
            .archive-table tbody td.empty-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:0 !important;}
            .archive-table tbody td.empty-cell::before{display:none !important;}
        }

        @media(max-width:1024px){
            .sidebar{transform:translateX(-100%);}
            .main-content{margin-left:0;}
        }
    </style>
</head>
<body>
<div class="main">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/dswd.png') }}" alt="DSWD">
            <span>Senior Citizen</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/senior"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
            <li><a href="/admin/senior/registration"><i data-lucide="users" style="width:20px;height:20px"></i><span>Registration</span></a></li>
            <li><a href="/admin/senior/masterlist"><i data-lucide="list" style="width:20px;height:20px"></i><span>Masterlist</span></a></li>
            <li><a href="/admin/senior/birthdays"><i data-lucide="cake" style="width:20px;height:20px"></i><span>Birthday Beneficiaries</span></a></li>
            <li><a href="/admin/senior/in-between/dashboard" class="active"><i data-lucide="gift" style="width:20px;height:20px"></i><span>In-Between Benefits</span></a></li>
            <li><a href="/admin/senior/payouts-history"><i data-lucide="history" style="width:20px;height:20px"></i><span>Payout History</span></a></li>
            <li><a href="/admin/senior/statistics"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i><span>Statistics</span></a></li>
            <li><a href="/admin/senior/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1>In-Between Birthday Cash Gift - Benefit History</h1>
            <p class="text-gray-600">View all benefit transactions</p>
        </div>

        <!-- Filters -->
        <div class="analytics-card" style="margin-bottom:20px;">
            <div class="flex items-center justify-between mb-4">
                <h3>Filters</h3>
                <button onclick="clearFilters()" class="text-sm text-blue-600 hover:text-blue-800">Clear All</button>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                <input type="text" id="searchInput" placeholder="Search by name..." class="form-input" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;width:100%;">
                <select id="intervalFilter" class="form-input" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;width:100%;">
                    <option value="">All Intervals</option>
                    <option value="81-84">81-84</option>
                    <option value="86-89">86-89</option>
                    <option value="91-94">91-94</option>
                    <option value="96-99">96-99</option>
                </select>
                <select id="statusFilter" class="form-input" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;width:100%;">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="released">Released</option>
                    <option value="rejected">Rejected</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <input type="date" id="fromDate" class="form-input" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;width:100%;">
                <input type="date" id="toDate" class="form-input" style="padding:8px 12px;border:1px solid var(--border);border-radius:8px;width:100%;">
            </div>
        </div>

        <!-- History Table -->
        <div class="analytics-card">
            <div class="flex items-center justify-between mb-4">
                <h3>Benefit Transactions ({{ $benefits->total() }})</h3>
            </div>
            
            <div class="archive-table-wrap">
                <table class="archive-table">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Interval</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Application Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($benefits as $benefit)
                            <tr>
                                <td data-label="Reference No." style="font-weight:600;font-family:monospace;">{{ $benefit->reference_number }}</td>
                                <td data-label="Name">
                                    <div style="font-weight:500;">{{ $benefit->full_name }}</div>
                                    <div class="td-sub">{{ $benefit->senior->barangay ?? 'N/A' }}</div>
                                </td>
                                <td data-label="Age">{{ $benefit->current_age }}</td>
                                <td data-label="Interval">{{ $benefit->eligibility_interval }}</td>
                                <td data-label="Amount">₱{{ number_format($benefit->amount, 2) }}</td>
                                <td data-label="Status">
                                    <span class="badge badge-{{ $benefit->status }}">
                                        {{ ucfirst($benefit->status) }}
                                    </span>
                                </td>
                                <td data-label="Application Date">{{ $benefit->application_date->format('M d, Y') }}</td>
                                <td data-label="Actions">
                                    <div class="actions">
                                        <button class="action-btn" style="background:var(--primary);border-color:var(--primary);color:#fff;" onclick="viewDetails({{ $benefit->id }})" title="View Details">
                                            <i data-lucide="eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="8" class="empty-cell">
                                    <div class="empty-state-content">
                                        <div class="empty-icon-wrap">
                                            <i data-lucide="receipt-text"></i>
                                        </div>
                                        <div class="empty-title">No benefit transactions found</div>
                                        <div class="empty-subtitle">No records match your search criteria</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($benefits->hasPages())
                <div class="sc-pagination">
                    <div class="sc-pagination-info">
                        @if($benefits->total() === 0)
                            Showing 0 of 0 Transactions
                        @else
                            Showing {{ $benefits->firstItem() }}–{{ $benefits->lastItem() }} of {{ $benefits->total() }} Transactions
                        @endif
                    </div>
                    <div class="sc-pagination-controls">
                        @if($benefits->count() > 0 && $benefits->hasPages())
                            @if($benefits->onFirstPage())
                                <span class="sc-page-btn" disabled>Previous</span>
                            @else
                                <a href="{{ $benefits->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                            @endif

                            <span class="sc-page-btn active">{{ $benefits->currentPage() }}</span>

                            @if($benefits->hasMorePages())
                                <a href="{{ $benefits->nextPageUrl() }}" class="sc-page-btn">Next</a>
                            @else
                                <span class="sc-page-btn" disabled>Next</span>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none">@csrf</form>

<script>
    function confirmLogout(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to logout?')) {
            document.getElementById('logout-form').submit();
        }
    }

    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('intervalFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('fromDate').value = '';
        document.getElementById('toDate').value = '';
        window.location.href = '{{ url()->current() }}';
    }

    function viewDetails(benefitId) {
        // You can implement a modal or redirect to a details page
        Swal.fire({
            title: 'Benefit Details',
            text: 'Benefit ID: ' + benefitId,
            icon: 'info'
        });
    }

    lucide.createIcons();
</script>
</body>
</html>
