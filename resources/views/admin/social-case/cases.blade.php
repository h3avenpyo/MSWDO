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
    /* ── Cases page resets ── */
    html, body { overflow-x: hidden !important; overflow-y: auto !important; }
    .main {
        display: flex !important;
        flex-direction: column !important;
        padding-top: 14px !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }
    @media (max-width: 767.98px) { .main { padding-top: 72px !important; } }

    /* ── Subtitle ── */
    .cases-subtitle { color: #6B7280; font-size: 0.85rem; margin: 0 0 10px; white-space: normal; overflow-wrap: break-word; line-height: 1.4; }

    /* ── Filter bar ── */
    .cases-filter-bar { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 12px; padding: 12px 14px; background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; }
    .filter-item { display: flex; flex-direction: column; justify-content: flex-end; position: relative; }
    .filter-label { display: block; font-size: 0.72rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; line-height: 1; white-space: nowrap; }
    
    .filter-search { flex: 1.4 1 200px; min-width: 170px; max-width: 280px; }
    .search-input-wrap { display: flex; align-items: center; height: 40px; }
    .search-input-wrap input { flex: 1; height: 40px; border: 1px solid #D1D5DB; border-right: none; border-radius: 6px 0 0 6px; padding: 0 0.85rem; font-size: 0.85rem; color: #111827; background: #fff; outline: none; transition: border-color .15s, box-shadow .15s; }
    .search-input-wrap input:focus { border-color: #1A237E; box-shadow: 0 0 0 3px rgba(26,35,126,.08); }
    .search-input-wrap button { background: #1A237E; color: #fff; border: none; padding: 0 1rem; border-radius: 0 6px 6px 0; cursor: pointer; height: 40px; display: flex; align-items: center; justify-content: center; transition: background .15s; }
    .search-input-wrap button:hover { background: #121858; }

    .filter-dropdown { flex: 1 1 125px; min-width: 120px; }
    .filter-select-btn { display: flex; align-items: center; gap: 6px; padding: 0 10px; height: 40px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 0.83rem; cursor: pointer; background: #fff; transition: border-color .15s, box-shadow .15s; }
    .filter-select-btn:hover { border-color: #9CA3AF; }
    .filter-select-label { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #111827; font-weight: 500; }
    .filter-menu { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #D1D5DB; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 50; max-height: 260px; overflow-y: auto; padding: 4px; }

    .filter-reset { flex: 0 0 auto; }
    .filter-reset-btn { height: 40px; padding: 0 0.85rem; border: 1px solid #EF4444; border-radius: 6px; background: #fff; color: #EF4444; font-size: 0.813rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all .15s; }
    .filter-reset-btn:hover { background: #FEE2E2; }

    /* ── Dropdown options ── */
    .status-opt.selected, .assistance-opt.selected, .barangay-opt.selected { background: #EEF2FF; color: #1A237E; font-weight: 600; }
    .status-opt:hover, .assistance-opt:hover, .barangay-opt:hover { background: #F3F4F6; }
    #statusBtn.active   { border-color: #1A237E; background: #EEF2FF; }
    #assistanceBtn.active { border-color: #1A237E; background: #EEF2FF; }
    #barangayBtn.active { border-color: #059669; background: #ECFDF5; color: #065F46; }

    /* ── Panel / wrap ── */
    .cases-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 0; padding: 0; }
    .cases-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: #fff; }

    /* ── Table base ── */
    #dataTable { width: 100%; border-collapse: separate; border-spacing: 0; table-layout: auto; }
    #dataTable thead tr { background: #F8FAFC; border-bottom: 2px solid #E2E8F0; }
    #dataTable thead th { padding: 12px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; white-space: nowrap; text-align: left; border-bottom: 2px solid #E2E8F0; }
    #dataTable tbody tr { border-bottom: 1px solid #F1F5F9; transition: background .15s; }
    #dataTable tbody tr:last-child { border-bottom: none; }
    #dataTable tbody tr:hover { background: #F8FAFC; }
    #dataTable tbody td { padding: 12px 14px; font-size: 0.875rem; color: #1E293B; vertical-align: middle; border-bottom: 1px solid #F1F5F9; }
    
    /* Column specific spacing */
    #dataTable tbody td[data-label="Control No."] { min-width: 125px; white-space: nowrap; font-family: 'Courier New', monospace; font-size: 0.813rem; font-weight: 600; color: #1E293B; }
    #dataTable tbody td[data-label="Client"] { min-width: 160px; max-width: 240px; font-weight: 600; color: #0F172A; white-space: normal; word-break: break-word; }
    #dataTable tbody td[data-label="Type"] { min-width: 130px; white-space: nowrap; color: #334155; }
    #dataTable tbody td[data-label="Barangay"] { min-width: 120px; white-space: nowrap; color: #475569; }

    #dataTable tbody td[data-label="Status"] { min-width: 110px; white-space: nowrap; }
    #dataTable tbody td[data-label="Created"] { min-width: 105px; white-space: nowrap; color: #64748B; font-size: 0.813rem; }
    #dataTable tbody td[data-label="Action"] { min-width: 120px; white-space: nowrap; }

    /* Badge & Button styling */
    .badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; line-height: 1.2; }
    .actions { display: inline-flex; align-items: center; gap: 6px; flex-wrap: nowrap; }
    .actions button { width: 32px; height: 32px; min-width: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; cursor: pointer; transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease; border: none; }
    .actions button:hover { transform: translateY(-1px); box-shadow: 0 2px 5px rgba(0,0,0,0.15); opacity: 0.95; }
    .actions button:active { transform: translateY(0); }

    .control-no { font-family: 'Courier New', monospace; font-size: 0.78rem; color: #374151; font-weight: 600; }
    .muted { color: #9CA3AF; font-style: italic; }

    /* ── Pagination ── */
    .sc-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; flex-shrink: 0; padding: 4px 0; flex-wrap: wrap; }
    .sc-pagination-info { font-size: 0.813rem; color: #6B7280; font-weight: 500; }
    .sc-pagination-controls { display: flex; gap: 4px; flex-wrap: wrap; }
    .sc-page-btn { height: 36px; min-width: 36px; padding: 0 10px; border: 1px solid #E5E7EB; border-radius: 6px; background: #fff; color: #374151; font-size: 0.813rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
    .sc-page-btn:hover:not(:disabled) { background: #F3F4F6; border-color: #D1D5DB; }
    .sc-page-btn.active { background: #1A237E; color: #fff; border-color: #1A237E; font-weight: 700; }
    .sc-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* ── Empty state ── */
    .empty-row { background: transparent !important; border: none !important; box-shadow: none !important; }
    .empty-cell { padding: 3rem 1rem !important; text-align: center !important; border: none !important; }
    .empty-cell::before { display: none !important; content: none !important; }
    .empty-state-content { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
    .empty-icon-wrap { width: 72px; height: 72px; border-radius: 50%; background: #EEF2FF; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #1A237E; }
    .empty-icon-wrap svg { width: 36px; height: 36px; }
    .empty-title { font-size: 1.125rem; font-weight: 700; color: #1F2937; margin-bottom: 6px; }
    .empty-subtitle { font-size: 0.875rem; color: #6B7280; line-height: 1.5; max-width: 360px; }

    /* ═══════════════════════════════════════════════════════════════
       MOBILE, TABLET & COLLAPSED SIDEBAR (< 1200px): CARD LAYOUT
    ═══════════════════════════════════════════════════════════════ */
    @media (max-width: 1199.98px) {
        .cases-panel { background: transparent; border: none; padding: 0; box-shadow: none; }
        .cases-table-wrap { overflow: visible; border: none; background: transparent; }

        #dataTable, 
        #dataTable thead, 
        #dataTable tbody, 
        #dataTable tbody tr, 
        #dataTable tbody td { 
            display: block !important; 
            width: 100% !important; 
        }
        #dataTable { min-width: 0 !important; }
        #dataTable thead { display: none !important; }

        #dataTable tbody tr:not(.empty-row) { 
            background: #ffffff !important; 
            border: 1px solid #E2E8F0 !important; 
            border-radius: 12px !important; 
            margin-bottom: 12px !important; 
            padding: 14px 18px !important; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important; 
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        #dataTable tbody tr:not(.empty-row):hover { 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }

        #dataTable tbody td { 
            display: flex !important; 
            justify-content: space-between !important; 
            align-items: center !important; 
            padding: 8px 0 !important; 
            border-bottom: 1px solid #F1F5F9 !important; 
            font-size: 0.875rem !important; 
            gap: 12px !important; 
            white-space: normal !important; 
            word-break: break-word !important; 
            max-width: none !important; 
            overflow: visible !important; 
            min-width: 0 !important; 
        }
        #dataTable tbody td:last-child { 
            border-bottom: none !important; 
        }
        #dataTable tbody td::before { 
            content: attr(data-label) !important; 
            font-weight: 700 !important; 
            font-size: 0.72rem !important; 
            text-transform: uppercase !important; 
            letter-spacing: 0.04em !important; 
            color: #64748B !important; 
            flex-shrink: 0 !important; 
            min-width: 100px !important; 
            display: block !important; 
        }
        #dataTable tbody td[data-label="Action"] { 
            justify-content: flex-end !important; 
            padding-top: 12px !important; 
            border-bottom: none !important; 
        }
        #dataTable tbody td[data-label="Action"]::before { 
            display: none !important; 
        }

        #dataTable tbody tr.empty-row { 
            border: none !important; 
            box-shadow: none !important; 
            background: transparent !important; 
            padding: 0 !important; 
        }
        #dataTable tbody tr.empty-row td { 
            border-bottom: none !important; 
            justify-content: center !important; 
        }
        #dataTable tbody tr.empty-row td::before { 
            display: none !important; 
        }
    }

    /* Mobile-specific filter & pagination (< 768px) */
    @media (max-width: 767.98px) {
        .cases-filter-bar { 
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px; 
            padding: 10px 12px; 
            margin-bottom: 10px;
        }
        .filter-search { max-width: none; width: 100%; min-width: 0; }
        .filter-dropdown { width: 100%; min-width: 0; }
        .filter-reset { width: 100%; }
        .filter-reset-btn { width: 100% !important; justify-content: center !important; }

        .sc-pagination { flex-direction: column; align-items: center; gap: 8px; }
        .sc-pagination-controls { justify-content: center; }
    }
    @media (min-width: 480px) and (max-width: 767.98px) {
        .cases-filter-bar {
            grid-template-columns: 1fr 1fr;
        }
        .filter-search { grid-column: 1 / -1; }
        .filter-reset { grid-column: 1 / -1; }
    }
    @media (max-width: 479px) {
        #dataTable tbody td::before { min-width: 75px; font-size: 0.68rem; }
        #dataTable tbody td { font-size: 0.813rem !important; }
    }

    /* Collapsed Sidebar Filter & Pagination (768px - 1199.98px) */
    @media (min-width: 768px) and (max-width: 1199.98px) {
        .cases-filter-bar { gap: 8px 10px; padding: 10px 14px; margin-bottom: 12px; flex-wrap: wrap; }
        .filter-search { flex: 1 1 190px; min-width: 170px; max-width: 250px; }
        .filter-dropdown { flex: 1 1 120px; min-width: 115px; }
        .filter-reset { flex: 0 0 auto; }

        .sc-pagination { flex-direction: row; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    }

    /* ══════════════════════════════════
       LARGE DESKTOP (1200px+): FULL TABLE
    ══════════════════════════════════ */
    @media (min-width: 1200px) {
        html, body { overflow: hidden !important; }
        .app { height: 100vh !important; overflow: hidden !important; }
        .main { height: 100vh !important; overflow: hidden !important; }

        .cases-filter-bar { flex-wrap: nowrap; gap: 10px; padding: 12px 16px; margin-bottom: 12px; }
        .filter-search { flex: 0 0 260px; max-width: 260px; }
        .filter-dropdown { flex: 1; min-width: 130px; }
        .filter-reset { flex: 0 0 auto; }

        .cases-panel { flex: 1; min-height: 0; overflow: hidden; display: flex; flex-direction: column; }
        .cases-table-wrap { flex: 1; min-height: 0; overflow: auto; border: 1px solid #E5E7EB; border-radius: 8px; }
        #dataTable { min-width: 1000px; width: 100%; table-layout: auto; }
        #dataTable thead th { padding: 12px 16px; font-size: 0.75rem; }
        #dataTable tbody td { padding: 12px 16px; font-size: 0.875rem; }
        #dataTable tbody td::before { display: none !important; content: none !important; }
        #dataTable tbody tr.empty-row td.empty-cell { white-space: normal !important; overflow: visible !important; max-width: none !important; }

        .sc-pagination { flex-direction: row; justify-content: space-between; margin-top: 12px; flex-shrink: 0; }
        .sc-page-btn { height: 38px; }
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
    <p class="cases-subtitle">View and manage all registered social case study records.</p>

    <!-- Search and Filter Bar -->
    <div class="cases-filter-bar">
        <div class="filter-item filter-search">
            <label class="filter-label">Search</label>
            <div class="search-input-wrap">
                <input type="text" id="searchInput" placeholder="Search name, control no..." oninput="applyFilters()">
                <button type="button" onclick="applyFilters()" title="Search">
                    <i data-lucide="search" style="width:16px;height:16px"></i>
                </button>
            </div>
        </div>
        <div class="filter-item filter-dropdown" id="statusDropdown">
            <label class="filter-label">Filter by Status</label>
            <div onclick="toggleStatusMenu()" class="filter-select-btn" id="statusBtn">
                <i data-lucide="filter" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                <span id="statusLabel" class="filter-select-label">All Status</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="statusMenu" class="filter-menu" style="display:none"></div>
        </div>
        <div class="filter-item filter-dropdown" id="assistanceDropdown">
            <label class="filter-label">Filter by Type</label>
            <div onclick="toggleAssistanceMenu()" class="filter-select-btn" id="assistanceBtn">
                <i data-lucide="filter" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                <span id="assistanceLabel" class="filter-select-label">All Types</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="assistanceMenu" class="filter-menu" style="display:none"></div>
        </div>
        <div class="filter-item filter-dropdown" id="barangayDropdown">
            <label class="filter-label">Filter by Barangay</label>
            <div onclick="toggleBarangayMenu()" class="filter-select-btn" id="barangayBtn">
                <i data-lucide="map-pin" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                <span id="barangayLabel" class="filter-select-label">All Barangays</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="barangayMenu" class="filter-menu" style="display:none"></div>
        </div>
        <div class="filter-item filter-reset">
            <button type="button" class="filter-reset-btn" onclick="resetFilters()">
                <i data-lucide="x" style="width:14px;height:14px"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table Panel -->
    <div class="cases-panel">
        <div class="cases-table-wrap">
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>Control No.</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Barangay</th>

                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
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
