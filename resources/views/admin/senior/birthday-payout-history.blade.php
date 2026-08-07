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
            --primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--purple:#7C3AED;--purple-bg:#F3E8FF;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4,h5{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}

        /* ── Buttons ── */
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);height:44px;min-height:44px;text-decoration:none;white-space:nowrap;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn svg{width:16px;height:16px;}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);transform:translateY(-1px);}
        .btn-clear{background:var(--surface);color:var(--danger);border-color:var(--danger);font-weight:600;}
        .btn-clear:hover{border-color:var(--danger);color:var(--danger);}

        /* ── Filter Bar (matches archive page) ── */
        .section-spacing{margin-bottom:28px;}
        #filterGrid{display:grid;grid-template-columns:1fr;gap:12px;align-items:stretch;}
        .filter-field{display:flex;flex-direction:column;justify-content:flex-end;min-width:0;gap:3px;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .filter-select{width:100%;height:44px;min-height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        input[type="date"].filter-select{cursor:text;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3e%3cline x1='16' y1='2' x2='16' y2='6'/%3e%3cline x1='8' y1='2' x2='8' y2='6'/%3e%3cline x1='3' y1='10' x2='21' y2='10'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 10px center;background-size:18px;padding-right:36px;}
        .filter-actions-row{display:flex;gap:8px;align-items:center;min-width:0;}
        .filter-actions-row .btn{white-space:nowrap;flex:1;}

        /* ── Archive-style panel & table (matches archive page) ── */
        .archive-panel-wrap{width:100%;padding:1rem;margin-bottom:1rem;border-radius:12px;background:var(--surface);border:1px solid var(--border);}
        .archive-table-wrap{border:1px solid var(--border);border-radius:8px;overflow-x:auto;-webkit-overflow-scrolling:touch;}
        .archive-table{width:100%;border-collapse:collapse;font-size:14px;}
        .archive-table thead th{padding:14px 16px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:var(--text-secondary);text-align:left;border-bottom:1px solid var(--border);background:var(--background);white-space:nowrap;}
        .archive-table tbody td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid var(--border);vertical-align:middle;white-space:normal;word-break:break-word;}
        .archive-table tbody tr:last-child td{border-bottom:none;}

        /* ── Badges ── */
        .badge-span{padding:.3rem .7rem;border-radius:999px;font-size:.78rem;display:inline-block;white-space:nowrap;font-weight:600;}
        .badge-released{background:var(--success-bg);color:var(--success);}
        .badge-generated{background:var(--info-bg);color:var(--info);}
        .badge-cancelled{background:var(--danger-bg);color:var(--danger);}
        .badge-reset{background:#FEF3C7;color:#D97706;}

        /* ── Empty State ── */
        .empty-row{background:transparent !important;border:none !important;box-shadow:none !important;padding:0 !important;margin:0 !important;}
        .empty-cell{padding:2.5rem 1rem !important;border:none !important;display:flex !important;flex-direction:column !important;align-items:center !important;justify-content:center !important;width:100% !important;}
        .empty-cell::before{display:none !important;}
        .empty-state-content{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;}
        .empty-icon-wrap{width:64px;height:64px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:#9CA3AF;}
        .empty-icon-wrap svg{width:32px;height:32px;}
        .empty-title{font-size:1.125rem;font-weight:700;color:#1F2937;margin-bottom:4px;}
        .empty-subtitle{font-size:0.875rem;color:#6B7280;}

        /* ── Pagination info ── */
        .archive-pagination-info{font-size:0.875rem;color:var(--text-secondary);text-align:center;padding-top:0.75rem;}

        /* ── Pagination links ── */
        .pagination-wrap{display:flex;justify-content:center;flex-wrap:wrap;padding-top:1rem;margin-top:1rem;border-top:1px solid var(--border);}

        /* ── Filter container (matches archive page) ── */
        .archive-filter-bar{display:block;margin-bottom:16px;padding:14px 16px;background:#fff;border:1px solid #E5E7EB;border-radius:12px;}
        .archive-filter-bar #filterGrid{margin-bottom:0;}

        /* ── Flash Messages ── */
        .flash-message{display:flex;align-items:center;gap:12px;padding:.875rem 1.25rem;border-radius:10px;font-size:.875rem;font-weight:500;margin-bottom:1rem;animation:fadeIn .3s ease;}
        .flash-message svg{width:20px;height:20px;flex-shrink:0;}
        .flash-success{background:var(--success-bg);color:#166534;border:1px solid #BBF7D0;}
        .flash-error{background:var(--danger-bg);color:#991B1B;border:1px solid #FECACA;}
        .flash-close{margin-left:auto;cursor:pointer;opacity:.6;transition:opacity .2s;background:none;border:none;padding:0;line-height:0;}
        .flash-close:hover{opacity:1;}
        .flash-close svg{width:18px;height:18px;}

        /* ── Animations ── */
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}

        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ══════════════════════════════════════════════ */

        /* ── Extra Large (1400px+) ── */
        @media (min-width:1400px){
            .section-spacing{margin-bottom:32px;}
            #filterGrid{grid-template-columns:minmax(180px,1fr) minmax(160px,1fr) minmax(160px,1fr) auto;gap:16px;}
            .filter-label{font-size:13px !important;}
            .filter-select,.filter-actions-row .btn{height:48px !important;min-height:48px !important;}
            .filter-select{font-size:14px !important;}
            .filter-actions-row .btn{font-size:14px !important;padding:12px 22px !important;}
            .archive-table thead th{font-size:13px !important;padding:14px 18px !important;}
            .archive-table tbody td{font-size:15px !important;padding:16px 18px !important;}
            .badge-span{font-size:13px !important;padding:5px 12px !important;}
        }

        /* ── Large Desktop (1200–1399px) ── */
        @media (min-width:1200px) and (max-width:1399px){
            .section-spacing{margin-bottom:28px;}
            #filterGrid{grid-template-columns:minmax(180px,1fr) minmax(160px,1fr) minmax(160px,1fr) auto;gap:14px;}
            .filter-label{font-size:12px !important;}
            .filter-select,.filter-actions-row .btn{height:46px !important;min-height:46px !important;}
            .filter-select{font-size:13px !important;}
            .filter-actions-row .btn{font-size:13px !important;padding:0 20px !important;}
            .archive-table thead th{font-size:12px !important;padding:12px 16px !important;}
            .archive-table tbody td{font-size:14px !important;padding:14px 16px !important;}
        }

        /* ── Medium Desktop (992–1199px) ── */
        @media (min-width:992px) and (max-width:1199px){
            .section-spacing{margin-bottom:24px;}
            #filterGrid{grid-template-columns:1fr 1fr;gap:14px;}
            .archive-table thead th{padding:11px 14px !important;}
            .archive-table tbody td{padding:13px 14px !important;}
            .empty-icon-wrap{width:56px;height:56px;}
            .empty-title{font-size:1rem !important;}
        }

        /* ── Tablet (768–991px) ── */
        @media (min-width:768px) and (max-width:991px){
            .section-spacing{margin-bottom:20px;}
            #filterGrid{grid-template-columns:1fr 1fr;gap:12px;}
            .archive-table thead th{padding:10px 12px !important;}
            .archive-table tbody td{padding:12px 12px !important;}
            .empty-icon-wrap{width:56px;height:56px;}
        }

        /* ── Desktop full-height layout (matches archive page) ── */
        @media (min-width:1200px){
            html,body{overflow:hidden !important;}
            .app{height:100vh !important;overflow:hidden !important;}
            .app .main{height:100vh !important;overflow:hidden !important;display:flex !important;flex-direction:column !important;}
            .app .main-scroll{flex:1 !important;min-height:0 !important;overflow-y:auto !important;overflow-x:hidden !important;display:flex !important;flex-direction:column !important;}
            .archive-panel-wrap{padding:1rem !important;margin-bottom:0 !important;flex:1 !important;min-height:0 !important;overflow:hidden !important;display:flex !important;flex-direction:column !important;}
            .archive-table-wrap{flex:1 !important;min-height:0 !important;border:1px solid var(--border) !important;overflow:auto !important;border-radius:8px !important;}
            .empty-icon-wrap{width:80px;height:80px;margin-bottom:20px;background:#EEF2FF;color:#1A237E;}
            .empty-icon-wrap svg{width:40px !important;height:40px !important;}
            .empty-title{font-size:1.35rem !important;font-weight:700 !important;color:#111827 !important;margin-bottom:8px !important;}
            .empty-subtitle{font-size:0.95rem !important;color:#6B7280 !important;max-width:400px;line-height:1.5;}
        }
        @media (min-width:768px) and (max-width:1199px){
            .archive-table tbody tr.empty-row{display:table-row !important;background:transparent !important;border:none !important;box-shadow:none !important;margin:0 !important;}
            .archive-table tbody tr.empty-row td.empty-cell{display:table-cell !important;padding:2.5rem 1.5rem !important;border:none !important;text-align:center !important;}
            .archive-table tbody tr.empty-row td.empty-cell::before{display:none !important;}
            .archive-table tbody tr.empty-row td.empty-cell .empty-state-content{align-items:center;justify-content:center;}
        }
        @media (min-width:1200px){
            .archive-table tbody tr.empty-row{display:table-row !important;background:transparent !important;border:none !important;box-shadow:none !important;margin:0 !important;}
            .archive-table tbody tr.empty-row td.empty-cell{display:table-cell !important;padding:3rem 1.5rem !important;border:none !important;text-align:center !important;}
            .archive-table tbody tr.empty-row td.empty-cell::before{display:none !important;}
        }

        /* ── Large Mobile (576–767px): stacked filters, table → cards ── */
        @media (min-width:576px) and (max-width:767px){
            .section-spacing{margin-bottom:18px;}
            #filterGrid{grid-template-columns:1fr;gap:12px;}
            .filter-actions-row{flex-direction:column;gap:8px;}
            .filter-actions-row .btn{width:100%;flex:1 1 auto;min-height:44px;}
        }

        /* ── Mobile (<768px): table → stacked cards (matches archive) ── */
        @media (max-width:767px){
            .archive-panel-wrap{padding:.75rem;}
            .archive-table-wrap{border:none;border-radius:0;overflow:visible;}
            .archive-table thead{display:none;}
            .archive-table tbody tr{display:block;background:var(--surface);border:1px solid #D1D5DB;border-radius:10px;margin-bottom:10px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
            .archive-table tbody tr:last-child{margin-bottom:0;}
            .archive-table tbody td{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border:none;font-size:.82rem;gap:8px;text-align:right;}
            .archive-table tbody td:not(:last-child){border-bottom:1px solid var(--border);}
            .archive-table tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:.72rem;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:80px;text-align:left;}
            .archive-table tbody td[data-label="Action"]{justify-content:flex-end;padding-top:8px;border-bottom:none;}
            .archive-table tbody td[data-label="Action"]::before{display:none;}
            .archive-table tbody td.empty-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:0 !important;}
            .archive-table tbody td.empty-cell::before{display:none !important;}
        }

        /* ── Small Mobile (<480px) ── */
        @media (max-width:479px){
            .section-spacing{margin-bottom:14px;}
            #filterGrid{grid-template-columns:1fr;gap:10px;}
            .archive-table tbody td{font-size:.75rem;}
            .archive-table tbody td::before{font-size:.65rem;min-width:70px;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'payouts', 'mobileSubtitle' => 'Payout History'])

    <div class="main">
        <div class="main-scroll">
            <div style="margin-bottom:1.5rem;">
                <p style="margin:0;font-size:0.875rem;color:#6B7280;">Review the release history of senior citizen birthday pension payouts.</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="flash-message flash-success">
                    <i data-lucide="check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i data-lucide="x"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="flash-message flash-error">
                    <i data-lucide="alert-circle"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i data-lucide="x"></i></button>
                </div>
            @endif

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('admin.senior.payouts-history') }}">
                <div class="archive-filter-bar section-spacing">
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
                        <div class="filter-field">
                            <label class="filter-label">&nbsp;</label>
                            <div class="filter-actions-row">
                                <button type="submit" class="btn primary">
                                    <i data-lucide="filter"></i> Filter
                                </button>
                                @if(request('barangay') || request('date_from') || request('date_to'))
                                    <a href="{{ route('admin.senior.payouts-history') }}" class="btn btn-clear">
                                        <i data-lucide="x"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- History Table --}}
            <div class="panel archive-panel-wrap">
                <div class="archive-table-wrap">
                    <table class="archive-table">
                        <thead>
                            <tr>
                                <th>Date &amp; Time</th>
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
                                                <div style="font-size:0.72rem;margin-top:2px;color:var(--text-muted);">{{ $record->senior->control_number ?? '-' }}</div>
                                            </div>
                                        @else
                                            <span style="font-size:0.8rem;color:var(--text-muted);">System-wide action</span>
                                        @endif
                                    </td>
                                    <td data-label="Amount">PHP {{ number_format($record->payout->amount ?? 0, 2) }}</td>
                                    <td data-label="Details"><div style="min-width:0;text-align:left">{{ $record->details ?? '-' }}</div></td>
                                    <td data-label="Performed By">
                                        @if($record->performedBy)
                                            {{ $record->performedBy->name ?? 'Admin' }}
                                        @else
                                            <span style="font-size:0.8rem;color:var(--text-muted);">System</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="6" class="empty-cell">
                                        <div class="empty-state-content">
                                            <div class="empty-icon-wrap">
                                                <i data-lucide="history"></i>
                                            </div>
                                            <div class="empty-title">No payout history found</div>
                                            <div class="empty-subtitle">Payout history records will appear here</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="archive-pagination-info">
                @if($history->total() === 0)
                    Showing 0 of 0 Records
                @else
                    Showing {{ $history->firstItem() }}–{{ $history->lastItem() }} of {{ $history->total() }} Records
                @endif
            </div>

            @if($history->hasPages())
                <div class="pagination-wrap">
                    {{ $history->appends(['barangay' => request('barangay'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>
