<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Birthday Payout History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}

        /* ── Buttons ── */
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);height:44px;min-height:44px;text-decoration:none;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn svg{width:16px;height:16px;}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);transform:translateY(-1px);}
        .btn-clear{background:var(--surface);color:var(--danger);border-color:var(--danger);font-weight:600;}
        .btn-clear:hover{border-color:var(--danger);color:var(--danger);}

        /* ── Filter Section ── */
        .filter-section{background:var(--surface);border-radius:16px;border:1px solid var(--border);padding:20px;box-shadow:var(--shadow);}
        .section-spacing{margin-bottom:28px;}
        .filter-field{display:flex;flex-direction:column;gap:3px;min-width:0;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;}
        .filter-select{width:100%;height:44px;min-height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        input[type="date"].filter-select{cursor:text;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3e%3cline x1='16' y1='2' x2='16' y2='6'/%3e%3cline x1='8' y1='2' x2='8' y2='6'/%3e%3cline x1='3' y1='10' x2='21' y2='10'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 10px center;background-size:18px;padding-right:36px;}
        #filterGrid{display:grid;grid-template-columns:1fr;gap:12px;align-items:end;}
        .filter-actions{display:flex;gap:8px;align-items:center;min-width:0;}
        .filter-actions .btn{white-space:nowrap;flex:1;}

        /* ── Table Card ── */
        .table-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);padding:24px;display:flex;flex-direction:column;min-width:0;width:100%;max-width:100%;}
        .table-card-title{font-size:1.25rem;font-weight:700;color:var(--text-primary);margin:0 0 1.25rem 0;flex-shrink:0;}
        .table-responsive{overflow-x:auto;width:100%;max-width:100%;-webkit-overflow-scrolling:touch;border-radius:8px;border:1px solid var(--border);}
        .table-responsive table{width:100%;border-collapse:collapse;table-layout:fixed;min-width:0;}
        .table-responsive thead{background:var(--surface);}
        .table-responsive th{padding:12px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);text-align:left;border-bottom:2px solid var(--border);white-space:nowrap;}
        .table-responsive td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid var(--border);vertical-align:middle;overflow-wrap:anywhere;word-break:break-word;min-width:0;}
        .table-responsive tr:hover td{background:var(--background);}
        .table-responsive tr:last-child td{border-bottom:none;}
        .empty-state-cell{padding:48px 16px !important;}
        .empty-state-cell .empty-icon{width:48px;height:48px;opacity:.4;}

        /* ── Badges ── */
        .badge-span{padding:.3rem .7rem;border-radius:999px;font-size:.78rem;display:inline-block;white-space:nowrap;font-weight:600;}
        .badge-released{background:var(--success-bg);color:var(--success);}
        .badge-generated{background:var(--info-bg);color:var(--info);}
        .badge-cancelled{background:var(--danger-bg);color:var(--danger);}
        .badge-reset{background:#FEF3C7;color:#D97706;}

        /* ── Pagination ── */
        .pagination-wrap{display:flex;justify-content:center;padding-top:1rem;margin-top:1rem;border-top:1px solid var(--border);}

        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ══════════════════════════════════════════════ */

        /* ── Desktop (1200px+): filters in one row ── */
        @media (min-width:1200px){
            #filterGrid{grid-template-columns:minmax(180px,1fr) minmax(180px,1fr) minmax(180px,1fr) auto;}
            .filter-actions{flex:0 0 auto;}
            .filter-section{padding:24px;}
            .filter-label{font-size:13px !important;}
            .filter-select{font-size:14px !important;height:48px !important;min-height:48px !important;}
            .btn{height:48px !important;min-height:48px !important;font-size:14px !important;padding:12px 22px !important;}
            .table-card-title{font-size:1.5rem !important;}
            .table-responsive th{font-size:13px !important;padding:14px 18px !important;}
            .table-responsive td{font-size:15px !important;padding:16px 18px !important;}
            .badge-span{font-size:13px !important;padding:5px 12px !important;}
        }

        /* ── Tablet (768–1199px): filters in two rows ── */
        @media (min-width:768px) and (max-width:1199px){
            .section-spacing{margin-bottom:20px;}
            .filter-section{padding:16px !important;}
            #filterGrid{grid-template-columns:1fr 1fr;gap:12px;}
            .table-card{padding:16px !important;}
            .table-card-title{font-size:1.1rem !important;margin-bottom:1rem !important;}
            .table-responsive th{padding:10px 12px !important;}
            .table-responsive td{padding:12px 12px !important;}
        }

        /* ── Mobile (<768px): stacked filters, table scrolls horizontally ── */
        @media (max-width:767px){
            .section-spacing{margin-bottom:16px;}
            .filter-section{padding:14px !important;}
            #filterGrid{grid-template-columns:1fr 1fr;gap:12px;}
            #filterGrid > .filter-field:first-child{grid-column:1 / -1;}
            .filter-actions{grid-column:1 / -1;flex-direction:column;gap:8px;}
            .filter-actions .btn{width:100%;flex:1 1 auto;min-height:44px;}
            .table-card{padding:12px !important;border-radius:12px !important;}
            .table-card-title{font-size:1rem !important;margin-bottom:0.875rem !important;}

            /* Table → cards on mobile */
            .table-responsive{overflow-x:visible !important;border:none !important;border-radius:0 !important;}
            .table-responsive table{display:block !important;width:100% !important;min-width:0 !important;table-layout:auto !important;}
            .table-responsive thead{display:none !important;}
            .table-responsive tbody{display:block;}
            .table-responsive tbody tr{display:block;background:var(--surface);border:1px solid var(--border);border-radius:10px;margin-bottom:10px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);}
            .table-responsive tbody tr:last-child{margin-bottom:0;}
            .table-responsive tbody td{display:flex;justify-content:space-between;align-items:flex-start;padding:8px 0;border:none !important;font-size:13px !important;gap:12px;word-break:break-word;overflow-wrap:break-word;}
            .table-responsive tbody td:not(:last-child){border-bottom:1px solid var(--border) !important;}
            .table-responsive tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:11px;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:80px;}
            .table-responsive tbody td.empty-state-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:32px 12px !important;}
            .table-responsive tbody td.empty-state-cell::before{display:none !important;}
            .empty-state-cell .empty-icon{width:40px !important;height:40px !important;}
        }

        /* ── Small mobile (<480px) ── */
        @media (max-width:479px){
            .section-spacing{margin-bottom:14px;}
            .filter-section{padding:12px !important;}
            .table-card{padding:10px !important;}
            .table-card-title{font-size:.95rem !important;}
            .table-responsive tbody td{font-size:12px !important;}
            .table-responsive tbody td::before{font-size:10px !important;min-width:70px;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'payouts', 'mobileSubtitle' => 'Payout History'])

    <div class="main">
        <div class="main-scroll">
            <!-- Filter Section -->
            <div class="filter-section section-spacing">
                <form method="GET" action="{{ route('admin.senior.payouts-history') }}">
                    <div id="filterGrid">
                        <div class="filter-field">
                            <label class="filter-label" for="barangayFilter">Barangay</label>
                            <select class="filter-select" id="barangayFilter" name="barangay">
                                <option value="">All Barangays</option>
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label" for="dateFromFilter">Date From</label>
                            <input type="date" class="filter-select" id="dateFromFilter" name="date_from" value="{{ $dateFrom }}">
                        </div>
                        <div class="filter-field">
                            <label class="filter-label" for="dateToFilter">Date To</label>
                            <input type="date" class="filter-select" id="dateToFilter" name="date_to" value="{{ $dateTo }}">
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn primary">
                                <i data-lucide="filter" style="width:16px;height:16px"></i> Filter
                            </button>
                            @if(request('barangay') || request('date_from') || request('date_to'))
                                <a href="{{ route('admin.senior.payouts-history') }}" class="btn btn-clear">
                                    <i data-lucide="x" style="width:16px;height:16px"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- History Table -->
            <div class="table-card">
                <h2 class="table-card-title">Birthday Payout History Log</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Action</th>
                                <th>Senior</th>
                                <th>Amount</th>
                                <th>Details</th>
                                <th>Performed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $record)
                                <tr>
                                    <td data-label="Date & Time">{{ $record->created_at->format('M d, Y g:i A') }}</td>
                                    <td data-label="Action">
                                        <span class="badge-span badge-{{ $record->action }}">
                                            {{ ucfirst(str_replace('_', ' ', $record->action)) }}
                                        </span>
                                    </td>
                                    <td data-label="Senior">
                                        @if($record->senior)
                                            <div>
                                                <strong>{{ $record->senior->full_name }}</strong>
                                                <div class="text-[var(--text-muted)]" style="font-size:0.72rem;margin-top:2px">{{ $record->senior->control_number ?? '-' }}</div>
                                            </div>
                                        @else
                                            <span class="text-[var(--text-muted)] text-xs">System-wide action</span>
                                        @endif
                                    </td>
                                    <td data-label="Amount">PHP {{ number_format($record->payout->amount ?? 0, 2) }}</td>
                                    <td data-label="Details"><div style="min-width:0;text-align:left">{{ $record->details ?? '-' }}</div></td>
                                    <td data-label="Performed By">
                                        @if($record->performedBy)
                                            {{ $record->performedBy->name ?? 'Admin' }}
                                        @else
                                            <span class="text-[var(--text-muted)] text-xs">System</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state-cell text-center">
                                        <div class="flex flex-col items-center text-[var(--text-muted)]">
                                            <i data-lucide="history" class="empty-icon mb-3"></i>
                                            <p class="text-sm m-0">No payout history found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($history->hasPages())
                    <div class="pagination-wrap">
                        {{ $history->appends(['barangay' => request('barangay'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>
