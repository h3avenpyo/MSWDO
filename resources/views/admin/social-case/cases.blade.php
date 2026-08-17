@extends('admin.social-case.layout')
@section('title', 'All Social Case Studies')

@section('content')
<!-- Mobile Header (visible only on mobile) -->
@php
$logo = null;
if(file_exists(public_path('images/mswdo-logo.png'))){
    $logo='mswdo-logo.png';
}else{
    $files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);
    if(!empty($files))
    $logo=basename($files[0]);
}
@endphp
<div class="mobile-header">
    <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
    <div class="mobile-header-brand">
        <div class="mobile-brand-text">
            <h1 class="mobile-brand-title">MSWDO SILANG</h1>
            <p class="mobile-brand-subtitle">All Social Case Studies</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<style>
    html,body{overflow-x:hidden!important;overflow-y:auto!important}
    .app{min-height:auto!important}
    .main{display:flex!important;flex-direction:column!important;overflow-x:hidden!important;overflow-y:auto!important}
    @media (min-width:1200px){
        html,body{overflow:hidden!important}
        .app{height:100vh!important;overflow:hidden!important}
        .main{height:100vh!important;overflow:hidden!important;overflow-x:hidden!important;overflow-y:hidden!important}
    }
    #searchInput:focus{border-color:#1A237E;box-shadow:0 0 0 3px rgba(26,35,126,.08)}
    .status-opt.selected,.assistance-opt.selected,.barangay-opt.selected{background:#F3F4F6;font-weight:600}
    .status-opt:not(.selected):hover,.assistance-opt:not(.selected):hover,.barangay-opt:not(.selected):hover{background:#F3F4F6}
    
    /* Color-coded filter buttons when active */
    #barangayBtn.active {
        border-color: #059669;
        background: #ECFDF5;
        color: #065F46;
    }
    #barangayBtn.active i[data-lucide="map-pin"] { color: #059669; }
    
    #statusBtn.active {
        border-color: #1A237E;
        background: #EEF2FF;
    }
    #statusBtn.active i[data-lucide="filter"] { color: #1A237E; }

    #assistanceBtn.active {
        border-color: #1A237E;
        background: #EEF2FF;
    }
    #assistanceBtn.active i[data-lucide="filter"] { color: #1A237E; }

    /* Archive: card layout for table (mobile first) */
    .archive-panel-wrap { padding: 1rem !important; margin-bottom: 1rem !important; border-radius: 12px; background: var(--surface); border: 1px solid var(--border); }
    .archive-filter-bar { flex-direction: column !important; gap: 10px !important; padding: 12px !important; }
    .archive-filter-bar > div { min-width: 0 !important; max-width: none !important; width: 100% !important; }
    .archive-filter-row-inline { flex-wrap: wrap; gap: 8px; }
    .archive-filter-row-inline > div { min-width: 0 !important; flex: 1; }
    .archive-table-wrap { border: none !important; overflow: visible !important; }
    .archive-table { width: 100%; }
    .archive-table thead { display: none; }
    .archive-table tbody tr {
        display: block;
        background: var(--surface);
        border: 1px solid #D1D5DB;
        border-radius: 10px;
        margin-bottom: 10px;
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .archive-table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border: none;
        font-size: 0.82rem;
        gap: 8px;
    }
    .archive-table tbody td:not(:last-child) {
        border-bottom: 1px solid var(--border);
    }
    .archive-table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        flex-shrink: 0;
        min-width: 70px;
    }
    .archive-table tbody td[data-label="Action"] {
        justify-content: flex-end;
        padding-top: 8px;
        border-bottom: none;
    }
    .archive-table tbody td[data-label="Action"]::before { display: none; }
    .archive-table tbody td:not([data-label]) { justify-content: center; text-align: center; }
    .archive-table tbody td:not([data-label])::before { display: none; }
    .archive-table tbody td .actions { justify-content: flex-end; }
    .archive-table tbody td .badge { font-size: 0.7rem; }
    .sc-pagination { gap: 8px; margin-top: 1rem; }
    .sc-page-btn { height: 34px; min-width: 34px; font-size: 0.8rem; padding: 0 0.5rem; }

    @media (min-width: 768px) and (max-width: 991px) {
        .archive-panel-wrap { padding: revert !important; margin-bottom: 0 !important; }
        .archive-filter-bar { flex-direction: row !important; gap: 8px !important; padding: 10px 12px !important; flex-wrap: nowrap; }
        .archive-filter-bar > div { min-width: initial; max-width: initial; width: initial; }
        .archive-filter-bar > div:first-child { max-width: 200px; flex: 0 0 auto; }
        .archive-filter-row-inline { gap: 8px; flex-wrap: nowrap; flex: 1; min-width: 0; }
        .archive-filter-row-inline > div { min-width: 0 !important; flex: 1; }
        .archive-table-wrap { border: initial; overflow: auto; }
        .archive-table thead { display: table-header-group; }
        .archive-table tbody tr {
            display: table-row;
            background: transparent;
            border: none;
            border-radius: 0;
            margin-bottom: 0;
            padding: 0;
            box-shadow: none;
        }
        .archive-table tbody td {
            display: table-cell;
            padding: 0.5rem 0.6rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.82rem;
        }
        .archive-table tbody td::before { content: none; }
        .archive-table tbody td[data-label="Action"] {
            padding-top: 0.5rem;
            border-bottom: 1px solid var(--border);
        }
        .archive-table tbody td[data-label="Action"]::before { content: none; }
        .archive-table tbody td:not([data-label]) { text-align: initial; }
        .archive-table tbody td:not([data-label])::before { content: none; }
        .archive-table tbody td .badge { font-size: inherit; }
        .sc-pagination { gap: 0.5rem; margin-top: 0; }
        .sc-page-btn { height: auto; min-width: auto; font-size: inherit; padding: 0.5rem 0.75rem; }
    }
    @media (min-width: 992px) and (max-width: 1199px) {
        .archive-panel-wrap { padding: revert !important; margin-bottom: 0 !important; }
        .archive-filter-bar { flex-direction: row !important; gap: 12px !important; padding: 14px 16px !important; flex-wrap: nowrap; }
        .archive-filter-bar > div { min-width: initial; max-width: initial; width: initial; }
        .archive-filter-row-inline { gap: 10px; flex-wrap: nowrap; }
        .archive-filter-row-inline > div { min-width: 140px; }
        .archive-table-wrap { border: initial; overflow: auto; }
        .archive-table thead { display: table-header-group; }
        .archive-table tbody tr {
            display: table-row;
            background: transparent;
            border: none;
            border-radius: 0;
            margin-bottom: 0;
            padding: 0;
            box-shadow: none;
        }
        .archive-table tbody td {
            display: table-cell;
            padding: 0.75rem 0.8rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.88rem;
        }
        .archive-table tbody td::before { content: none; }
        .archive-table tbody td[data-label="Action"] {
            padding-top: 0.75rem;
            border-bottom: 1px solid var(--border);
        }
        .archive-table tbody td[data-label="Action"]::before { content: none; }
        .archive-table tbody td:not([data-label]) { text-align: initial; }
        .archive-table tbody td:not([data-label])::before { content: none; }
        .archive-table tbody td .badge { font-size: inherit; }
        .sc-pagination { gap: 0.5rem; margin-top: 0; }
        .sc-page-btn { height: auto; min-width: auto; font-size: inherit; padding: 0.5rem 0.75rem; }
    }

    /* Base empty state styles */
    .empty-row {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .empty-cell {
        padding: 2.5rem 1rem !important;
        border: none !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
    }
    .empty-cell::before { display: none !important; }
    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .empty-icon-wrap {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #F3F4F6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: #9CA3AF;
    }
    .empty-icon-wrap svg {
        width: 32px;
        height: 32px;
    }
    .empty-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 4px;
    }
    .empty-subtitle {
        font-size: 0.875rem;
        color: #6B7280;
    }

    /* Tablet (768-1199px): empty state stays a full-width centered row */
    @media (min-width: 768px) and (max-width: 1199px) {
        .archive-table tbody tr.empty-row { display: table-row !important; background: transparent !important; border: none !important; box-shadow: none !important; margin: 0 !important; }
        .archive-table tbody tr.empty-row td.empty-cell {
            display: table-cell !important;
            padding: 2.5rem 1.5rem !important;
            border: none !important;
            text-align: center !important;
        }
        .archive-table tbody tr.empty-row td.empty-cell::before { display: none !important; }
        .archive-table tbody tr.empty-row td.empty-cell .empty-state-content { align-items: center; justify-content: center; }
    }

    @media (min-width: 1200px) {
        /* ── Panel & wrap: grow to fill all remaining height ── */
        .archive-panel-wrap { padding: 1rem !important; margin-bottom: 0 !important; flex: 1 !important; min-height: 0 !important; overflow: hidden !important; display: flex !important; flex-direction: column !important; }
        .archive-table-wrap { flex: 1 !important; min-height: 0 !important; border: 1px solid var(--border) !important; overflow: auto !important; border-radius: 8px !important; }

        /* ── Restore standard table display ── */
        .archive-table { display: table !important; width: 100% !important; }
        .archive-table thead { display: table-header-group !important; }
        .archive-table tbody { display: table-row-group !important; }
        .archive-table tbody tr {
            display: table-row !important;
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            margin-bottom: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
        }
        .archive-table tbody td {
            display: table-cell !important;
            padding: 0.9rem 1rem !important;
            border-bottom: 1px solid var(--border) !important;
            font-size: 0.9rem !important;
            justify-content: unset !important;
            gap: 0 !important;
        }
        .archive-table tbody td::before { content: none !important; display: none !important; }
        .archive-table tbody td[data-label="Action"] {
            justify-content: flex-start !important;
            padding-top: 0.9rem !important;
            border-bottom: 1px solid var(--border) !important;
        }
        .archive-table tbody td[data-label="Action"]::before { display: none !important; }
        .archive-table tbody td:not([data-label]) { text-align: left !important; }
        .archive-table tbody td:not([data-label])::before { display: none !important; }
        .archive-table tbody td .badge { font-size: inherit !important; }

        /* ── Empty row (keep it centered across the full table row) ── */
        .archive-table tbody tr.empty-row { display: table-row !important; background: transparent !important; border: none !important; box-shadow: none !important; margin: 0 !important; }
        .archive-table tbody tr.empty-row td.empty-cell {
            display: table-cell !important;
            padding: 3rem 1.5rem !important;
            border: none !important;
            text-align: center !important;
        }
        .archive-table tbody tr.empty-row td.empty-cell::before { display: none !important; }

        .empty-icon-wrap {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            background: #EEF2FF;
            color: #1A237E;
        }
        .empty-icon-wrap svg {
            width: 40px !important;
            height: 40px !important;
        }
        .empty-title {
            font-size: 1.35rem !important;
            font-weight: 700 !important;
            color: #111827 !important;
            margin-bottom: 8px !important;
        }
        .empty-subtitle {
            font-size: 0.95rem !important;
            color: #6B7280 !important;
            max-width: 400px;
            line-height: 1.5;
        }

        /* ── Filter bar ── */
        .archive-filter-bar {
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-end !important;
            gap: 14px !important;
            padding: 14px 18px !important;
        }
        /* Search box: fixed width, never shrinks */
        .archive-filter-bar > div:first-child {
            flex: 0 0 280px !important;
            max-width: 280px !important;
            width: 280px !important;
        }
        /* Row that holds the dropdowns: stretch to fill remaining space */
        .archive-filter-row-inline {
            flex: 1 1 0 !important;
            flex-wrap: nowrap !important;
            gap: 12px !important;
            min-width: 0 !important;
        }
        /* Each dropdown fills equal fraction */
        .archive-filter-row-inline > div {
            flex: 1 1 0 !important;
            min-width: 140px !important;
            max-width: none !important;
        }

        /* ── Pagination: pinned at the bottom of .main, centered ── */
        .sc-pagination { flex-direction: column !important; justify-content: center !important; align-items: center !important; gap: 8px !important; margin-top: 12px !important; flex-shrink: 0 !important; padding-bottom: 4px !important; }
        .sc-page-btn { height: 38px; min-width: 38px; font-size: 0.875rem; padding: 0 0.75rem; }
    }
    .barangay-opt:hover, .status-opt:hover, .assistance-opt:hover {
        background: #F3F4F6;
    }
    .barangay-opt.selected, .status-opt.selected, .assistance-opt.selected {
        background: #EEF2FF;
        color: #1A237E;
        font-weight: 600;
    }
    @media (max-width: 479px) {
        .archive-panel-wrap { padding: 0.75rem !important; }
        .archive-table tbody td::before { min-width: 60px; font-size: 0.68rem; }
        .archive-table tbody td { font-size: 0.78rem; }
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>New case</span></a></li>
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li><a href="#" onclick="return false" style="opacity:0.5;pointer-events:none;cursor:not-allowed" title="Not available for eligibility checker accounts"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @else
        <li><a href="/admin/social-case/submitted"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @endif
        <li><a href="/admin/social-case/cases" class="active"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <!-- Page Sub-Header -->
    <div class="mb-6">
        <p class="text-[#6B7280] text-sm m-0">View and manage all registered social case study records.</p>
    </div>

    <!-- Search and Filter Bar -->
    <div class="archive-filter-bar" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;margin-bottom:16px;padding:14px 16px;background:#fff;border:1px solid #E5E7EB;border-radius:12px">
        <div style="max-width:280px;width:100%;flex-shrink:0;display:flex;flex-direction:column;justify-content:flex-end">
            <label style="display:block;font-size:0.75rem;font-weight:600;color:#111827;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Search</label>
            <div style="display:flex;align-items:center;height:44px;">
                <input type="text" id="searchInput" placeholder="Search name, control no..."
                       oninput="applyFilters()"
                       style="flex:1;height:44px;border:1px solid #E5E7EB;border-right:none;border-radius:6px 0 0 6px;padding:0 1rem;font-size:0.875rem;color:#111827;background:#fff;transition:all .2s ease;outline:none;">
                <button type="button" onclick="applyFilters()" style="background:#1A237E;color:#fff;border:none;padding:0 1.25rem;border-radius:0 6px 6px 0;cursor:pointer;height:44px;display:flex;align-items:center;justify-content:center;transition:background .2s;">
                    <i data-lucide="search" style="width:16px;height:16px"></i>
                </button>
            </div>
        </div>
        <div class="archive-filter-row-inline" style="display:flex;gap:10px;flex:1;min-width:0">
            <div style="position:relative;flex:1;min-width:130px;display:flex;flex-direction:column;justify-content:flex-end" id="statusDropdown">
                <label style="display:block;font-size:0.75rem;font-weight:600;color:#111827;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Filter by Status</label>
                <div onclick="toggleStatusMenu()" style="display:flex;align-items:center;gap:8px;padding:0 12px;height:44px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;cursor:pointer;background:#fff;transition:border-color .2s,box-shadow .2s" id="statusBtn">
                    <i data-lucide="filter" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                    <span id="statusLabel" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#111827">All Status</span>
                    <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;transition:transform .2s"></i>
                </div>
                <div id="statusMenu" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D1D5DB;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:260px;overflow-y:auto;padding:4px"></div>
            </div>
            <div style="position:relative;flex:1;min-width:130px;display:flex;flex-direction:column;justify-content:flex-end" id="assistanceDropdown">
                <label style="display:block;font-size:0.75rem;font-weight:600;color:#111827;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Filter by Type</label>
                <div onclick="toggleAssistanceMenu()" style="display:flex;align-items:center;gap:8px;padding:0 12px;height:44px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;cursor:pointer;background:#fff;transition:border-color .2s,box-shadow .2s" id="assistanceBtn">
                    <i data-lucide="filter" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                    <span id="assistanceLabel" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#111827">All Types</span>
                    <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;transition:transform .2s"></i>
                </div>
                <div id="assistanceMenu" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D1D5DB;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:260px;overflow-y:auto;padding:4px"></div>
            </div>
            <div style="position:relative;flex:1;min-width:130px;display:flex;flex-direction:column;justify-content:flex-end" id="barangayDropdown">
                <label style="display:block;font-size:0.75rem;font-weight:600;color:#111827;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Filter by Barangay</label>
                <div onclick="toggleBarangayMenu()" style="display:flex;align-items:center;gap:8px;padding:0 12px;height:44px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;cursor:pointer;background:#fff;transition:border-color .2s,box-shadow .2s" id="barangayBtn">
                    <i data-lucide="map-pin" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                    <span id="barangayLabel" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#111827">All Barangays</span>
                    <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0;transition:transform .2s"></i>
                </div>
                <div id="barangayMenu" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #D1D5DB;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;max-height:260px;overflow-y:auto;padding:4px"></div>
            </div>
            <div style="flex:0 0 auto;display:flex;flex-direction:column;justify-content:flex-end">
                <button type="button" onclick="resetFilters()" style="width:fit-content;height:44px;padding:0 1rem;border:1px solid #EF4444;border-radius:8px;background:#fff;color:#EF4444;font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <i data-lucide="x" style="width:14px;height:14px"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Table Panel -->
    <div class="panel archive-panel-wrap">
        <div class="archive-table-wrap">
            <table class="archive-table" id="dataTable">
                <thead>
                    <tr>
                        <th style="width:14%">Control No.</th>
                        <th style="width:20%">Client</th>
                        <th style="width:16%">Assistance Type</th>
                        <th style="width:13%">Barangay</th>
                        <th style="width:13%">Eligibility</th>
                        <th style="width:11%">Status</th>
                        <th style="width:12%">Created</th>
                        <th style="width:14%">Action</th>
                    </tr>
                </thead>
                <tbody id="casesTableBody"></tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="sc-pagination">
        <div class="sc-pagination-info" id="paginationInfo">Showing 0 of 0 Records</div>
        <div class="sc-pagination-controls" id="paginationControls"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') . '?v=' . filemtime(public_path('js/social-case.js')) }}"></script>
<script>
    // Filter state
    var filterState = {
        status: 'All',
        assistance: 'All',
        barangay: 'All'
    };

    // Populate dropdown menus
    function populateDropdowns() {
        // Populate Status dropdown
        const statusMenu = document.getElementById('statusMenu');
        if(statusMenu && typeof STATUSES !== 'undefined') {
            statusMenu.innerHTML = '<div class="status-opt" data-value="All" onclick="selectStatus(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Status</div>';
            STATUSES.forEach(status => {
                statusMenu.innerHTML += `<div class="status-opt" data-value="${status}" onclick="selectStatus(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${status}</div>`;
            });
        }

        // Populate Assistance Type dropdown
        const assistanceMenu = document.getElementById('assistanceMenu');
        if(assistanceMenu && typeof PURPOSES !== 'undefined') {
            assistanceMenu.innerHTML = '<div class="assistance-opt" data-value="All" onclick="selectAssistance(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Types</div>';
            PURPOSES.forEach(purpose => {
                assistanceMenu.innerHTML += `<div class="assistance-opt" data-value="${purpose}" onclick="selectAssistance(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${purpose}</div>`;
            });
        }

        // Populate Barangay dropdown
        const barangayMenu = document.getElementById('barangayMenu');
        if(barangayMenu && typeof BARANGAYS !== 'undefined') {
            barangayMenu.innerHTML = '<div class="barangay-opt" data-value="All" onclick="selectBarangay(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Barangays</div>';
            BARANGAYS.forEach(barangay => {
                barangayMenu.innerHTML += `<div class="barangay-opt" data-value="${barangay}" onclick="selectBarangay(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${barangay}</div>`;
            });
        }
    }

    function toggleStatusMenu(){
        var menu=document.getElementById('statusMenu');
        var arrow=document.querySelector('#statusBtn [data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightStatusOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }

    function selectStatus(el){
        var val=el.getAttribute('data-value');
        filterState.status=val;
        document.getElementById('statusLabel').textContent=el.textContent;
        document.getElementById('statusMenu').style.display='none';
        var arrow=document.querySelector('#statusBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightStatusOpt();
        var btn=document.getElementById('statusBtn');
        if(val && val !== 'All'){ btn.classList.add('active'); btn.setAttribute('data-filter', val); }
        else { btn.classList.remove('active'); btn.removeAttribute('data-filter'); }
        applyFilters();
    }

    function highlightStatusOpt(){
        var opts=document.querySelectorAll('.status-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===filterState.status) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }

    function toggleAssistanceMenu(){
        var menu=document.getElementById('assistanceMenu');
        var arrow=document.querySelector('#assistanceBtn [data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightAssistanceOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }

    function selectAssistance(el){
        var val=el.getAttribute('data-value');
        filterState.assistance=val;
        document.getElementById('assistanceLabel').textContent=el.textContent;
        document.getElementById('assistanceMenu').style.display='none';
        var arrow=document.querySelector('#assistanceBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightAssistanceOpt();
        var btn=document.getElementById('assistanceBtn');
        if(val && val !== 'All'){ btn.classList.add('active'); btn.setAttribute('data-filter', val); }
        else { btn.classList.remove('active'); btn.removeAttribute('data-filter'); }
        applyFilters();
    }

    function highlightAssistanceOpt(){
        var opts=document.querySelectorAll('.assistance-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===filterState.assistance) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }

    function toggleBarangayMenu(){
        var menu=document.getElementById('barangayMenu');
        var arrow=document.querySelector('#barangayBtn [data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightBarangayOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }

    function selectBarangay(el){
        var val=el.getAttribute('data-value');
        filterState.barangay=val;
        document.getElementById('barangayLabel').textContent=el.textContent;
        document.getElementById('barangayMenu').style.display='none';
        var arrow=document.querySelector('#barangayBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightBarangayOpt();
        var btn=document.getElementById('barangayBtn');
        if(val && val !== 'All') btn.classList.add('active');
        else btn.classList.remove('active');
        applyFilters();
    }

    function highlightBarangayOpt(){
        var opts=document.querySelectorAll('.barangay-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===filterState.barangay) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }

    // Close menus when clicking outside
    document.addEventListener('click',function(e){
        var statusDD=document.getElementById('statusDropdown');
        var assistanceDD=document.getElementById('assistanceDropdown');
        var barangayDD=document.getElementById('barangayDropdown');

        if(statusDD && !statusDD.contains(e.target)){
            var menu=document.getElementById('statusMenu');
            var arrow=document.querySelector('#statusBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
        if(assistanceDD && !assistanceDD.contains(e.target)){
            var menu=document.getElementById('assistanceMenu');
            var arrow=document.querySelector('#assistanceBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
        if(barangayDD && !barangayDD.contains(e.target)){
            var menu=document.getElementById('barangayMenu');
            var arrow=document.querySelector('#barangayBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        populateDropdowns();
        loadCaseList();
    });
</script>
@endpush
