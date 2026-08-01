<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Archived Senior Citizens</title>
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
        .btn-clear{background:var(--surface);color:var(--danger);border-color:var(--danger);font-weight:600;}
        .btn-clear:hover{border-color:var(--danger);color:var(--danger);}

        /* ── Summary / Filters ── */
        .section-spacing{margin-bottom:28px;}
        #summaryGrid{display:grid;grid-template-columns:1fr;gap:12px;align-items:stretch;}
        .filter-field{display:flex;flex-direction:column;justify-content:flex-end;min-width:0;gap:3px;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .filter-select{width:100%;height:44px;min-height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        .input-group{display:flex;align-items:center;}
        .input-group input{flex:1;min-width:0;height:44px;border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;padding:0 1rem;font-size:14px;color:var(--text-primary);background:var(--surface);transition:all .2s ease;font-family:inherit;}
        .input-group input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(30,58,138,.15);}
        .search-btn{background:var(--primary);color:#ffffff;border:none;padding:0 1.1rem;border-radius:0 8px 8px 0;cursor:pointer;height:44px;width:48px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:background .2s;}
        .search-btn:hover{background:var(--primary-hover);}
        .search-btn svg{width:18px;height:18px;}
        .bulk-actions-row{display:flex;gap:8px;align-items:center;min-width:0;}
        .bulk-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;background:#E0E7FF;color:#3730A3;border:1px solid #C7D2FE;cursor:pointer;transition:all .2s ease;font-family:inherit;height:44px;min-height:44px;flex:1;white-space:nowrap;}
        .bulk-btn:disabled{opacity:.45;cursor:not-allowed;}
        .bulk-btn svg{width:15px;height:15px;flex-shrink:0;}
        .bulk-count{background:#3730A3;color:white;padding:2px 8px;border-radius:10px;font-size:11px;margin-left:4px;}

        /* ── Summary Card ── */
        .stat-card{background:var(--surface);border-radius:16px;padding:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;position:relative;overflow:hidden;min-width:0;width:100%;max-width:100%;animation:fadeInUp .6s ease-out backwards;}
        .stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--purple);transition:all .3s ease;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 4px 20px rgba(0,0,0,0.08);}
        .stat-card-content{flex:1;min-width:0;}
        .stat-card-label{font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--text-secondary);margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .stat-card-value{font-size:32px;font-weight:700;color:var(--text-primary);line-height:1;}
        .stat-card-icon{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:var(--purple-bg);color:var(--purple);}
        .stat-card-icon svg{width:24px;height:24px;}

        /* ── Table Card ── */
        .table-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);padding:24px;display:flex;flex-direction:column;min-width:0;width:100%;max-width:100%;}
        .table-card-title{font-size:1.25rem;font-weight:700;color:var(--text-primary);margin:0 0 1.25rem 0;flex-shrink:0;}
        .table-responsive{overflow-x:auto;width:100%;max-width:100%;-webkit-overflow-scrolling:touch;border-radius:8px;border:1px solid var(--border);}
        .table-responsive table{width:100%;border-collapse:collapse;min-width:0;}
        .table-responsive thead{background:var(--surface);}
        .table-responsive th{padding:12px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);text-align:left;border-bottom:2px solid var(--border);white-space:nowrap;}
        .table-responsive td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid var(--border);vertical-align:middle;overflow-wrap:break-word;word-break:break-word;min-width:0;}
        .table-responsive tr:hover td{background:var(--background);}
        .table-responsive tr:last-child td{border-bottom:none;}
        .table-responsive input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}
        .td-name{font-weight:500;color:var(--text-primary);}
        .td-addr{font-size:0.75rem;color:var(--text-secondary);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;}
        .control-no{font-weight:600;color:var(--text-primary);}
        .sex-age-wrap{display:inline-flex;align-items:center;gap:6px;white-space:nowrap;}
        .sex-letter{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:#6B7280;color:white;font-size:11px;font-weight:700;flex-shrink:0;}
        .sex-sep{color:var(--text-muted);}
        .badge-archived{background:rgba(156,163,175,.15);color:#6B7280;padding:.35rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600;display:inline-flex;align-items:center;gap:4px;white-space:nowrap;}
        .btn-restore{display:inline-flex;align-items:center;justify-content:center;gap:4px;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:500;background:#ECFDF5;color:#16A34A;border:1px solid #6EE7B7;cursor:pointer;transition:all .2s ease;font-family:inherit;white-space:nowrap;text-decoration:none;}
        .btn-restore:hover{background:#D1FAE5;}
        .btn-restore svg{width:14px;height:14px;}

        /* ── Empty State ── */
        .empty-state-cell{padding:0 !important;}
        .empty-state{min-height:260px;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem 1.5rem;width:100%;}
        .empty-state [data-lucide]{width:56px;height:56px;color:#D1D5DB;margin-bottom:1rem;}
        .empty-state h5{color:#6B7280;font-weight:600;font-size:1rem;}
        .empty-state p{color:#9CA3AF;font-size:.85rem;margin-top:.25rem;max-width:28rem;text-align:center;}
        .empty-state a{display:inline-flex;align-items:center;gap:6px;background:var(--primary);color:#fff;border-radius:8px;font-weight:600;font-size:13px;padding:10px 16px;margin-top:14px;transition:background .2s;font-family:inherit;}
        .empty-state a:hover{background:var(--primary-hover);}
        .empty-state a svg{width:16px;height:16px;}

        /* ── Pagination ── */
        .pagination-wrap{display:flex;justify-content:center;flex-wrap:wrap;padding-top:1rem;margin-top:1rem;border-top:1px solid var(--border);}

        /* ── Mobile Select All ── */
        .mobile-select-all{display:none;align-items:center;gap:8px;padding:10px 12px;margin-bottom:10px;background:var(--surface);border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text-secondary);}
        .mobile-select-all input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}

        /* ── Flash Messages ── */
        .flash-message{display:flex;align-items:center;gap:12px;padding:.875rem 1.25rem;border-radius:10px;font-size:.875rem;font-weight:500;margin-bottom:1rem;animation:fadeIn .3s ease;}
        .flash-message svg{width:20px;height:20px;flex-shrink:0;}
        .flash-success{background:var(--success-bg);color:#166534;border:1px solid #BBF7D0;}
        .flash-error{background:var(--danger-bg);color:#991B1B;border:1px solid #FECACA;}
        .flash-close{margin-left:auto;cursor:pointer;opacity:.6;transition:opacity .2s;background:none;border:none;padding:0;line-height:0;}
        .flash-close:hover{opacity:1;}
        .flash-close svg{width:18px;height:18px;}

        /* ── Modal ── */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
        .modal-overlay.active{display:flex;}
        .modal-panel{background:var(--surface);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:90%;max-width:440px;overflow:hidden;transform:scale(.95);opacity:0;transition:all .2s ease;}
        .modal-overlay.active .modal-panel{transform:scale(1);opacity:1;}
        .modal-panel-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--accent-yellow);color:#121858;}
        .modal-panel-header h5{font-size:1rem;font-weight:700;display:flex;align-items:center;gap:8px;}
        .modal-close{color:#121858;cursor:pointer;border:none;background:transparent;padding:4px;border-radius:6px;display:flex;align-items:center;justify-content:center;}
        .modal-close:hover{opacity:.7;}
        .modal-close svg{width:20px;height:20px;}
        .modal-panel-body{padding:1.5rem;}
        .modal-btn{display:flex;align-items:center;justify-content:center;gap:10px;border:none;border-radius:12px;padding:14px 20px;font-size:1rem;font-weight:500;transition:background .2s;cursor:pointer;width:100%;font-family:inherit;color:#fff;}
        .modal-btn svg{width:20px;height:20px;}
        .modal-btn-teal{background:#0f766e;}
        .modal-btn-teal:hover{background:#0d6e61;}
        .modal-btn-indigo{background:var(--primary);}
        .modal-btn-indigo:hover{background:var(--primary-hover);}

        /* ── Animations ── */
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}

        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ══════════════════════════════════════════════ */

        /* ── Desktop (1200px+): summary in one row ── */
        @media (min-width:1200px){
            .section-spacing{margin-bottom:32px;}
            #summaryGrid{grid-template-columns:minmax(220px,300px) minmax(220px,1fr) minmax(180px,1fr) auto;gap:16px;}
            .filter-label{font-size:13px !important;}
            .filter-select,.input-group input,.search-btn,.bulk-btn{height:48px !important;min-height:48px !important;}
            .filter-select{font-size:14px !important;}
            .input-group input{font-size:15px !important;}
            .search-btn{width:52px !important;}
            .bulk-btn{font-size:14px !important;padding:0 22px !important;}
            .table-card-title{font-size:1.5rem !important;}
            .table-responsive th{font-size:13px !important;padding:14px 18px !important;}
            .table-responsive td{font-size:15px !important;padding:16px 18px !important;}
            .badge-archived{font-size:13px !important;padding:5px 12px !important;}
            .btn-restore{font-size:14px !important;padding:8px 16px !important;}
        }

        /* ── Tablet (768–1199px): summary in two rows, table stays a table ── */
        @media (min-width:768px) and (max-width:1199px){
            .section-spacing{margin-bottom:20px;}
            #summaryGrid{grid-template-columns:1fr 1fr;gap:16px;}
            .table-card{padding:16px !important;}
            .table-card-title{font-size:1.1rem !important;margin-bottom:1rem !important;}
            .empty-state{min-height:220px;padding:2.5rem 1.5rem;}
        }

        /* ── Tablet & Mobile (<1200px): table → stacked cards, no sideways scroll ── */
        @media (max-width:1199px){
            .table-responsive{overflow:visible !important;border:none !important;background:transparent !important;box-shadow:none !important;border-radius:0 !important;}
            .table-responsive table{display:block !important;width:100% !important;min-width:0 !important;table-layout:auto !important;}
            .table-responsive thead{display:none !important;}
            .table-responsive tbody{display:block;}
            .table-responsive tbody tr{display:block;background:var(--surface);border:1px solid var(--border);border-radius:12px;margin-bottom:12px;padding:12px 14px;box-shadow:var(--shadow);}
            .table-responsive tbody tr:last-child{margin-bottom:0;}
            .table-responsive tbody td{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:8px 0;border:none;border-bottom:1px solid var(--border);font-size:13px !important;white-space:normal;word-break:break-word;text-align:right;}
            .table-responsive tbody td:last-child{border-bottom:none;}
            .table-responsive tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:11px;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:88px;text-align:left;}
            .table-responsive tbody td.col-check{justify-content:flex-end;border-bottom:none;padding:0 0 6px;}
            .table-responsive tbody td.col-check::before{display:none !important;}
            .table-responsive tbody td[data-label="#"]{display:none !important;}
            .table-responsive tbody td[data-label="Control No."]{white-space:nowrap;}
            .table-responsive tbody td[data-label="Action"]{justify-content:flex-end;border-bottom:none;padding-top:10px;}
            .table-responsive tbody td[data-label="Action"]::before{display:none !important;}
            .table-responsive tbody td.empty-state-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:0 !important;}
            .table-responsive tbody td.empty-state-cell::before{display:none !important;}
            .td-addr{white-space:normal;overflow:visible;text-overflow:clip;}
            .btn-restore{min-height:44px;padding:10px 14px;font-size:13px;}
        }

        /* ── Mobile (<768px): stacked filters ── */
        @media (max-width:767px){
            .section-spacing{margin-bottom:16px;}
            #summaryGrid{grid-template-columns:1fr;gap:12px;}
            .table-card{padding:12px !important;border-radius:12px !important;}
            .table-card-title{font-size:1rem !important;margin-bottom:0.875rem !important;}
            .bulk-btn{flex:1 1 auto;}
            .mobile-select-all{display:flex;}
            .empty-state{min-height:180px;padding:2rem 1rem;}
            .empty-state [data-lucide]{width:48px !important;height:48px !important;}
            .empty-state h5{font-size:.95rem !important;}
            .empty-state p{font-size:.8rem !important;}
        }

        /* ── Small mobile (<480px) ── */
        @media (max-width:479px){
            .section-spacing{margin-bottom:14px;}
            .table-card{padding:10px !important;}
            .table-card-title{font-size:.95rem !important;}
            .table-responsive tbody td{font-size:12px !important;}
            .table-responsive tbody td::before{font-size:10px !important;min-width:70px;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'archive', 'mobileSubtitle' => 'Archived Records'])

    <div class="main">
        <div class="main-scroll">
            @php
                $barangays = ['Acacia','Adlas','Anahaw I','Anahaw II','Balite I','Balite II','Balubad','Banaba','Batas','Biga I','Biga II','Biluso','Bucal','Buho','Bulihan','Cabangaan','Carmen','Hoyo','Hukay','Iba','Inchican','Ipil I','Ipil II','Kalubkob','Kaong','Lalaan I','Lalaan II','Litlit','Lucsuhin','Lumil','Maguyam','Malabag','Malaking Tatyao','Mataas na Burol','Munting Ilog','Narra I','Narra II','Narra III','Paligawan','Pasong Langka','Barangay I (Poblacion)','Barangay II (Poblacion)','Barangay III (Poblacion)','Barangay IV (Poblacion)','Barangay V (Poblacion)','Pooc I','Pooc II','Pulong Bunga','Pulong Saging','Puting Kahoy','Sabutan','San Miguel I','San Miguel II','San Vicente I','San Vicente II','Santol','Tartaria','Tibig','Toledo','Tubuan I','Tubuan II','Tubuan III','Ulat','Yakal'];
            @endphp

            <!-- Flash Messages -->
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

            <!-- Summary Section -->
            <form method="GET" action="{{ route('admin.senior.archive.list') }}">
                <div id="summaryGrid" class="section-spacing">
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <div class="stat-card-label">TOTAL ARCHIVED</div>
                            <div class="stat-card-value">{{ $archivedSeniors->total() }}</div>
                        </div>
                        <div class="stat-card-icon"><i data-lucide="archive"></i></div>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="searchInput">Search by Name</label>
                        <div class="input-group">
                            <input type="text" id="searchInput" name="search" placeholder="Search by name..." value="{{ request('search') }}">
                            <button type="submit" class="search-btn" aria-label="Search"><i data-lucide="search"></i></button>
                        </div>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="barangayFilter">Filter by Barangay</label>
                        <select class="filter-select" id="barangayFilter" name="barangay" onchange="this.form.submit()">
                            <option value="" {{ !request('barangay') ? 'selected' : '' }}>All Barangays</option>
                            @foreach($barangays as $b)
                                <option value="{{ $b }}" {{ request('barangay') == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label">&nbsp;</label>
                        <div class="bulk-actions-row">
                            <button type="button" id="bulkActionButton" class="bulk-btn" onclick="showBulkActionPopup()" disabled>
                                <i data-lucide="list-checks"></i> Bulk Actions <span class="bulk-count" id="selectedCount">0</span>
                            </button>
                            @if(request('search') || request('barangay'))
                                <a href="{{ route('admin.senior.archive.list') }}" class="btn btn-clear">
                                    <i data-lucide="x"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>

            <!-- Archived Records Table -->
            <div class="table-card">
                <h2 class="table-card-title">Archived Records</h2>
                @if($archivedSeniors->count() > 0)
                    <div class="mobile-select-all">
                        <input type="checkbox" id="mobileSelectAll" onchange="toggleSelectAllMobile(this.checked)">
                        <label for="mobileSelectAll" style="cursor:pointer;font-weight:500;">Select all</label>
                        <span id="mobileSelectedCount" style="margin-left:auto;font-size:12px;font-weight:600;color:var(--primary);"></span>
                    </div>
                @endif
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="col-check"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)"></th>
                                <th>#</th>
                                <th>Control No.</th>
                                <th>Full Name</th>
                                <th>Barangay</th>
                                <th>Sex / Age</th>
                                <th>Archived On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archivedSeniors as $index => $senior)
                                <tr>
                                    <td data-label="" class="col-check"><input type="checkbox" class="senior-checkbox" data-id="{{ $senior->id }}" onchange="updateBulkActions()"></td>
                                    <td data-label="#">{{ $archivedSeniors->firstItem() + $index }}</td>
                                    <td data-label="Control No."><span class="control-no">{{ $senior->control_number ?? '-' }}</span></td>
                                    <td data-label="Full Name">
                                        <div class="td-name">{{ $senior->full_name ?? '-' }}</div>
                                        @if($senior->address)
                                            <div class="td-addr">{{ $senior->address }}</div>
                                        @endif
                                    </td>
                                    <td data-label="Barangay">
                                        @if($senior->barangay)
                                            <span class="badge-archived">{{ $senior->barangay }}</span>
                                        @else
                                            <span class="text-[var(--text-muted)]">-</span>
                                        @endif
                                    </td>
                                    <td data-label="Sex / Age">
                                        <span class="sex-age-wrap">
                                            @if($senior->sex)
                                                <span class="sex-letter">{{ $senior->sex == 'Male' ? 'M' : 'F' }}</span>
                                            @endif
                                            <span class="sex-sep">/</span>
                                            <strong>{{ $senior->age ?? '-' }}</strong>
                                        </span>
                                    </td>
                                    <td data-label="Archived On">
                                        <span class="text-[var(--text-muted)]">{{ $senior->updated_at ? \Carbon\Carbon::parse($senior->updated_at)->format('M d, Y') : '-' }}</span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge-archived">Archived</span>
                                    </td>
                                    <td data-label="Action">
                                        <form method="POST" action="{{ route('admin.senior.unarchive', $senior->id) }}" id="restore-form-{{ $senior->id }}" style="display:inline;">
                                            @csrf
                                            <button type="button" class="btn-restore" onclick="confirmRestore({{ $senior->id }}, '{{ addslashes($senior->full_name) }}')">
                                                <i data-lucide="rotate-ccw"></i> Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="empty-state-cell">
                                        <div class="empty-state">
                                            <i data-lucide="archive"></i>
                                            <h5>No Archived Cases</h5>
                                            <p>Archived cases will appear here. Records archived from the masterlist will show up in this list.</p>
                                            <a href="{{ route('admin.senior.masterlist') }}"><i data-lucide="list"></i> Go to Masterlist</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($archivedSeniors->count() > 0 && $archivedSeniors->hasPages())
                    <div class="pagination-wrap">
                        {{ $archivedSeniors->appends(['barangay' => request('barangay'), 'search' => request('search')])->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal-overlay" id="bulkActionModal">
    <div class="modal-panel">
        <div class="modal-panel-header">
            <h5><i data-lucide="list-checks"></i> Bulk Actions</h5>
            <button type="button" class="modal-close" onclick="closeBulkModal()"><i data-lucide="x"></i></button>
        </div>
        <div class="modal-panel-body">
            <div class="flex flex-col gap-3">
                <button type="button" class="modal-btn modal-btn-teal" onclick="bulkRestore()">
                    <i data-lucide="undo-2"></i> Restore Selected
                </button>
                <button type="button" class="modal-btn modal-btn-indigo" onclick="bulkExport()">
                    <i data-lucide="download"></i> Export Selected
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    function toggleSelectAll(checked) {
        document.querySelectorAll('.senior-checkbox').forEach(cb => cb.checked = checked);
        updateBulkActions();
    }

    function toggleSelectAllMobile(checked) {
        document.querySelectorAll('.senior-checkbox').forEach(cb => cb.checked = checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        const selected = document.querySelectorAll('.senior-checkbox:checked');
        const button = document.getElementById('bulkActionButton');
        const countSpan = document.getElementById('selectedCount');
        const selectAll = document.getElementById('selectAll');
        const mobileAll = document.getElementById('mobileSelectAll');
        const mobileCount = document.getElementById('mobileSelectedCount');
        if (countSpan) countSpan.textContent = selected.length;
        if (selectAll) selectAll.checked = checkboxes.length > 0 && selected.length === checkboxes.length;
        if (mobileAll) mobileAll.checked = checkboxes.length > 0 && selected.length === checkboxes.length;
        if (mobileCount) mobileCount.textContent = selected.length > 0 ? selected.length + ' / ' + checkboxes.length + ' selected' : '';
        if (button) {
            const has = selected.length > 0;
            button.disabled = !has;
            button.style.opacity = has ? '1' : '0.45';
            button.style.background = has ? '#3730A3' : '#E0E7FF';
            button.style.color = has ? 'white' : '#3730A3';
            button.style.borderColor = has ? '#312E81' : '#C7D2FE';
        }
    }

    function showBulkActionPopup() {
        const selected = document.querySelectorAll('.senior-checkbox:checked');
        if (selected.length === 0) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }
        document.getElementById('bulkActionModal').classList.add('active');
    }

    function closeBulkModal() {
        document.getElementById('bulkActionModal').classList.remove('active');
    }

    document.getElementById('bulkActionModal').addEventListener('click', function(e) {
        if (e.target === this) closeBulkModal();
    });

    function bulkRestore() {
        const ids = Array.from(document.querySelectorAll('.senior-checkbox:checked')).map(cb => cb.dataset.id);
        if (ids.length === 0) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }
        closeBulkModal();
        Swal.fire({
            title: 'Restore Selected Records?',
            text: `You are about to restore ${ids.length} record(s) back to active status.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0f766e',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Restore',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/senior/bulk-restore', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Restored!', 'Selected records have been restored.', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire('Error', data.message || 'Failed to restore records.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'An error occurred while restoring records.', 'error');
                });
            }
        });
    }

    function bulkExport() {
        const ids = Array.from(document.querySelectorAll('.senior-checkbox:checked')).map(cb => cb.dataset.id);
        if (ids.length === 0) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }
        closeBulkModal();
        Swal.fire({
            title: 'Export Selected Records?',
            text: `You are about to export ${ids.length} record(s).`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Export',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/admin/senior/export?ids=${ids.join(',')}`;
            }
        });
    }

    function confirmRestore(id, name) {
        Swal.fire({
            title: 'Restore Senior?',
            html: `Are you sure you want to restore <strong>${name}</strong> back to active status?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0f766e',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Restore',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('restore-form-' + id).submit();
            }
        });
    }
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>
