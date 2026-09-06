<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen ID Card Generator</title>
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
        h1,h2,h3,h4,h5{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .15s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:none;height:44px;min-height:44px;text-decoration:none;white-space:nowrap;}
        .btn:hover{border-color:var(--primary);transform:none;}
        .btn svg{width:16px;height:16px;}
        .btn-primary{background:var(--primary);color:#fff;border-color:var(--primary);}
        .btn-primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);}
        .btn-success{background:#10B981;color:#fff;border-color:#10B981;}
        .btn-success:hover{background:#059669;border-color:#059669;}

        .section-spacing{margin-bottom:28px;}
        #filterGrid{display:grid;grid-template-columns:280px 180px auto !important;gap:12px;align-items:stretch;}
        .filter-field{display:flex;flex-direction:column;justify-content:flex-end;min-width:0;gap:3px;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .filter-select{width:100%;height:44px;min-height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .15s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        .input-group{display:flex;align-items:center;}
        .input-group input{flex:1;min-width:0;height:44px;border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;padding:0 1rem;font-size:14px;color:var(--text-primary);background:var(--surface);transition:all .15s ease;font-family:inherit;}
        .input-group input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(30,58,138,.15);}
        .search-btn{background:var(--primary);color:#ffffff;border:none;padding:0 1.1rem;border-radius:0 8px 8px 0;cursor:pointer;height:44px;width:48px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:background .15s;}
        .search-btn:hover{background:var(--primary-hover);}
        .search-btn svg{width:18px;height:18px;}

        .archive-panel-wrap{width:100%;padding:1rem;margin-bottom:1rem;border-radius:12px;background:var(--surface);border:1px solid var(--border);}
        .archive-table-wrap{border:2px solid #CBD5E1;border-radius:8px;overflow-x:auto;-webkit-overflow-scrolling:touch;max-height: 500px; overflow-y: auto;}
        .archive-table{width:100%;border-collapse:collapse;font-size:14px;}
        .archive-table thead th{padding:14px 16px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#1E293B;text-align:left;border-bottom:2px solid #94A3B8;background:#E2E8F0;white-space:nowrap;position: sticky; top: 0; z-index: 10;}
        .archive-table tbody td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid #CBD5E1;vertical-align:middle;white-space:normal;word-break:break-word;}
        .archive-table tbody tr:last-child td{border-bottom:none;}
        .archive-table input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}
        .archive-table .col-check{width:40px;text-align:center;}

        .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:500;white-space:nowrap;}
        .badge-active{background:var(--success-bg);color:var(--success);}
        .badge-pending{background:#FEF3C7;color:#92400E;}

        .actions{display:flex;gap:6px;align-items:center;}
        .action-btn{width:34px !important;height:34px !important;min-height:34px !important;max-height:34px !important;padding:0 !important;display:inline-flex !important;align-items:center !important;justify-content:center !important;border-radius:8px !important;box-shadow:none !important;cursor:pointer;transition:background .15s ease, border-color .15s ease;}
        .action-btn:hover{transform:none;}
        .action-btn svg, .action-btn i{width:16px !important;height:16px !important;}

        .empty-row{background:transparent !important;border:none !important;box-shadow:none !important;padding:0 !important;margin:0 !important;}
        .empty-cell{padding:2.5rem 1rem !important;border:none !important;display:flex !important;flex-direction:column !important;align-items:center !important;justify-content:center !important;width:100% !important;}
        .empty-cell::before{display:none !important;}
        .empty-state-content{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;}
        .empty-icon-wrap{width:64px;height:64px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:#9CA3AF;}
        .empty-icon-wrap svg{width:32px;height:32px;}
        .empty-title{font-size:1.125rem;font-weight:700;color:#1F2937;margin-bottom:4px;}
        .empty-subtitle{font-size:0.875rem;color:#6B7280;}

        .archive-pagination-info{font-size:0.875rem;color:var(--text-secondary);text-align:center;padding-top:0.75rem;}
        .pagination-wrap{display:flex;justify-content:center;flex-wrap:wrap;padding-top:1rem;margin-top:1rem;border-top:1px solid var(--border);}

        .sc-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; flex-shrink: 0; padding: 4px 0; flex-wrap: wrap; }
        .sc-pagination-info { font-size: 0.813rem; color: #6B7280; font-weight: 500; }
        .sc-pagination-controls { display: flex; gap: 4px; flex-wrap: wrap; }
        .sc-page-btn { height: 36px; min-width: 36px; padding: 0 10px; border: 1px solid #E5E7EB; border-radius: 6px; background: #fff; color: #374151; font-size: 0.813rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
        .sc-page-btn:hover:not(:disabled) { background: #F3F4F6; border-color: #D1D5DB; }
        .sc-page-btn.active { background: #1A237E; color: #fff; border-color: #1A237E; font-weight: 700; }
        .sc-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        .archive-filter-bar{display:block;margin-bottom:16px;padding:14px 16px;background:#fff;border:1px solid #E5E7EB;border-radius:12px;}
        .archive-filter-bar #filterGrid{margin-bottom:0;}

        @media (max-width:767px){
            #filterGrid{grid-template-columns:1fr !important;gap:12px !important;}
            .filter-field{width:100%;}
            .filter-label{display:block !important;}
            .input-group{width:100%;}
            .input-group input{width:100%;}
            .filter-select{width:100%;}
        }

        @media (max-width: 767.98px) {
            .sc-pagination { position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important; background: #fff !important; padding: 15px 0 !important; z-index: 100 !important; border-top: 1px solid #E5E7EB !important; flex-direction: column; align-items: center; gap: 8px; }
            .sc-pagination-controls { justify-content: flex-end; padding-right: 20px; }
        }

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
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'id-card', 'mobileSubtitle' => 'ID Card Generator'])

    <div class="main">
        <div class="main-scroll">
            <div style="margin-bottom:1.5rem;">
                <p style="margin:0;font-size:0.875rem;color:#6B7280;">Select a senior citizen to generate their ID card.</p>
            </div>

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('admin.senior.id-card') }}" id="filterForm" style="margin-bottom: 20px;">
                <div id="filterGrid">
                    <div class="filter-field">
                        <label class="filter-label" for="searchInput">Search by Name</label>
                        <div class="input-group">
                            <input type="text" id="searchInput" name="search" placeholder="Search by name..." value="{{ request('search') }}">
                            <button type="submit" class="search-btn" aria-label="Search"><i data-lucide="search"></i></button>
                        </div>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="barangaySelect">Filter by Barangay</label>
                        <select class="filter-select" id="barangaySelect" name="barangay">
                            <option value="">All Barangays</option>
                            @foreach($allBarangays as $barangay)
                                <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label">&nbsp;</label>
                        <div class="filter-actions-row">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            @if(request('barangay') || request('search'))
                                <a href="{{ route('admin.senior.id-card') }}" class="btn" style="color:var(--danger);border-color:#FECACA;">Clear</a>
                            @endif
                            <button type="button" id="bulkPrintBtn" class="btn btn-success" onclick="bulkPrintIdCards()" disabled style="display: none;">
                                <i data-lucide="printer"></i> Print Selected IDs
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="archive-table-wrap">
                <table class="archive-table">
                    <thead>
                        <tr>
                            <th class="col-check"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                            <th>Control No</th>
                            <th>Full Name</th>
                            <th>Barangay</th>
                            <th>Status</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seniors as $senior)
                            <tr>
                                <td class="col-check"><input type="checkbox" class="senior-checkbox" data-id="{{ $senior->id }}" onchange="updateBulkActions()"></td>
                                <td style="font-weight:600;">{{ $senior->control_number ?? '-' }}</td>
                                <td>{{ $senior->full_name ?? '-' }}</td>
                                <td>{{ $senior->barangay ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $senior->status == 'active' ? 'badge-active' : 'badge-pending' }}">
                                        {{ ucfirst($senior->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>{{ $senior->address ?? '-' }}</td>
                                <td>
                                    <div class="actions">
                                        <button class="action-btn btn-success" onclick="generateIdCard({{ $senior->id }})" title="Generate ID Card">
                                            <i data-lucide="id-card"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="6" class="empty-cell">
                                    <div class="empty-state-content">
                                        <div class="empty-icon-wrap">
                                            <i data-lucide="id-card"></i>
                                        </div>
                                        <div class="empty-title">No senior citizens found</div>
                                        <div class="empty-subtitle">No records match your search criteria</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="border-top: 2px solid #94A3B8; margin: 20px 0;"></div>

            <div class="sc-pagination">
                <div class="sc-pagination-info">
                    @if($seniors->total() === 0)
                        Showing 0 of 0 Records
                    @else
                        Showing {{ $seniors->firstItem() }}–{{ $seniors->lastItem() }} of {{ $seniors->total() }} Records
                    @endif
                </div>
                <div class="sc-pagination-controls">
                    @if($seniors->hasPages())
                        @if($seniors->onFirstPage())
                            <span class="sc-page-btn" disabled>Previous</span>
                        @else
                            <a href="{{ $seniors->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                        @endif

                        <span class="sc-page-btn active">{{ $seniors->currentPage() }}</span>

                        @if($seniors->hasMorePages())
                            <a href="{{ $seniors->nextPageUrl() }}" class="sc-page-btn">Next</a>
                        @else
                            <span class="sc-page-btn" disabled>Next</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    function generateIdCard(id) {
        window.open(`{{ route('admin.senior.generate-id-card', 0) }}`.replace('/0', `/${id}`), '_blank');
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const bulkPrintBtn = document.getElementById('bulkPrintBtn');
        
        if (checkboxes.length > 0) {
            bulkPrintBtn.disabled = false;
            bulkPrintBtn.style.display = 'inline-flex';
        } else {
            bulkPrintBtn.disabled = true;
            bulkPrintBtn.style.display = 'none';
        }
    }

    function bulkPrintIdCards() {
        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.dataset.id);
        
        if (ids.length === 0) {
            Swal.fire('No Selection', 'Please select at least one senior citizen.', 'warning');
            return;
        }

        ids.forEach((id, index) => {
            setTimeout(() => {
                window.open(`{{ route('admin.senior.generate-id-card', 0) }}`.replace('/0', `/${id}`), '_blank');
            }, index * 300);
        });

        Swal.fire({
            title: 'Opening ID Cards',
            text: `Opening ${ids.length} ID card(s) in new tabs. Please print each one.`,
            icon: 'info',
            confirmButtonColor: '#1A237E',
            confirmButtonText: 'OK'
        });
    }
</script>
</body>
</html>
