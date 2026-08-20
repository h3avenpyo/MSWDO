@extends('admin.social-case.layout')
@section('title', 'Archive - Social Case Study')

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
            <p class="mobile-brand-subtitle">Archived Records</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<style>
    /* ── Archive page resets ── */
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
    .archive-subtitle { color: #6B7280; font-size: 0.85rem; margin: 0 0 10px; white-space: normal; overflow-wrap: break-word; line-height: 1.4; }

    /* ── Filter bar ── */
    .archive-filter-bar { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 12px; padding: 12px 14px; background: #fff; border: 1px solid #E5E7EB; border-radius: 10px; }
    .archive-filter-search { flex: 1.4 1 200px; min-width: 170px; max-width: 280px; display: flex; flex-direction: column; justify-content: flex-end; }
    .archive-filter-label { display: block; font-size: 0.72rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; line-height: 1; white-space: nowrap; }
    .archive-search-wrap { display: flex; align-items: center; height: 40px; }
    .archive-search-wrap input { flex: 1; height: 40px; border: 1px solid #D1D5DB; border-right: none; border-radius: 6px 0 0 6px; padding: 0 0.85rem; font-size: 0.85rem; color: #111827; background: #fff; outline: none; transition: border-color .15s, box-shadow .15s; }
    .archive-search-wrap input:focus { border-color: #1A237E; box-shadow: 0 0 0 3px rgba(26,35,126,.08); }
    .archive-search-wrap button { background: #1A237E; color: #fff; border: none; padding: 0 1rem; border-radius: 0 6px 6px 0; cursor: pointer; height: 40px; display: flex; align-items: center; justify-content: center; transition: background .15s; }
    .archive-search-wrap button:hover { background: #121858; }

    .archive-filter-dropdown { flex: 1 1 130px; min-width: 125px; position: relative; display: flex; flex-direction: column; justify-content: flex-end; }
    .archive-select-btn { display: flex; align-items: center; gap: 6px; padding: 0 10px; height: 40px; border: 1px solid #D1D5DB; border-radius: 6px; font-size: 0.83rem; cursor: pointer; background: #fff; transition: border-color .15s, box-shadow .15s; }
    .archive-select-btn:hover { border-color: #9CA3AF; }
    .archive-select-label { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #111827; font-weight: 500; }
    .archive-menu { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #D1D5DB; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 50; max-height: 260px; overflow-y: auto; padding: 4px; }

    .archive-filter-reset { flex: 0 0 auto; display: flex; flex-direction: column; justify-content: flex-end; }
    .archive-reset-btn { height: 40px; padding: 0 0.85rem; border: 1px solid #EF4444; border-radius: 6px; background: #fff; color: #EF4444; font-size: 0.813rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all .15s; }
    .archive-reset-btn:hover { background: #FEE2E2; }

    /* Dropdown options */
    .archive-type-opt.selected, .archive-brgy-opt.selected { background: #EEF2FF; color: #1A237E; font-weight: 600; }
    .archive-type-opt:hover, .archive-brgy-opt:hover { background: #F3F4F6; }
    #archiveBrgyBtn.active { border-color: #059669; background: #ECFDF5; color: #065F46; }
    #archiveTypeBtn.active { border-color: #1A237E; background: #EEF2FF; }

    /* ── Panel / wrap ── */
    .archive-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 0; padding: 0; }
    .archive-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: #fff; }

    /* ── Table base ── */
    .archive-table { width: 100%; border-collapse: separate; border-spacing: 0; table-layout: auto; }
    .archive-table thead tr { background: #F8FAFC; border-bottom: 2px solid #E2E8F0; }
    .archive-table thead th { padding: 12px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; white-space: nowrap; text-align: left; border-bottom: 2px solid #E2E8F0; }
    .archive-table tbody tr { border-bottom: 1px solid #F1F5F9; transition: background .15s; }
    .archive-table tbody tr:last-child { border-bottom: none; }
    .archive-table tbody tr:hover { background: #F8FAFC; }
    .archive-table tbody td { padding: 12px 14px; font-size: 0.875rem; color: #1E293B; vertical-align: middle; border-bottom: 1px solid #F1F5F9; }

    /* Column specific spacing */
    .archive-table tbody td[data-label="Control No"] { min-width: 125px; white-space: nowrap; font-family: 'Courier New', monospace; font-size: 0.813rem; font-weight: 600; color: #1E293B; }
    .archive-table tbody td[data-label="Client"] { min-width: 160px; max-width: 240px; font-weight: 600; color: #0F172A; white-space: normal; word-break: break-word; }
    .archive-table tbody td[data-label="Type"], .archive-table tbody td[data-label="Assistance Type"] { min-width: 130px; white-space: nowrap; color: #334155; }
    .archive-table tbody td[data-label="Status"] { min-width: 110px; white-space: nowrap; }
    .archive-table tbody td[data-label="Date"] { min-width: 105px; white-space: nowrap; color: #64748B; font-size: 0.813rem; }
    .archive-table tbody td[data-label="Action"], .archive-table tbody td[data-label="Actions"] { min-width: 120px; white-space: nowrap; }

    /* Badge & Button styling */
    .badge { display: inline-flex; align-items: center; justify-content: center; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; line-height: 1.2; }
    .b-archived { background: #F3F4F6; color: #4B5563; }
    .actions { display: inline-flex; align-items: center; gap: 6px; flex-wrap: nowrap; }
    .actions button { width: 32px; height: 32px; min-width: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; cursor: pointer; transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease; border: none; }
    .actions button:hover { transform: translateY(-1px); box-shadow: 0 2px 5px rgba(0,0,0,0.15); opacity: 0.95; }
    .actions button:active { transform: translateY(0); }

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
        .archive-panel { background: transparent; border: none; padding: 0; box-shadow: none; }
        .archive-table-wrap { overflow: visible; border: none; background: transparent; }

        .archive-table, 
        .archive-table thead, 
        .archive-table tbody, 
        .archive-table tbody tr, 
        .archive-table tbody td { 
            display: block !important; 
            width: 100% !important; 
        }
        .archive-table { min-width: 0 !important; }
        .archive-table thead { display: none !important; }

        .archive-table tbody tr:not(.empty-row) { 
            background: #ffffff !important; 
            border: 1px solid #E2E8F0 !important; 
            border-radius: 12px !important; 
            margin-bottom: 12px !important; 
            padding: 14px 18px !important; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important; 
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .archive-table tbody tr:not(.empty-row):hover { 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }

        .archive-table tbody td { 
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
        .archive-table tbody td:last-child { 
            border-bottom: none !important; 
        }
        .archive-table tbody td::before { 
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
        .archive-table tbody td[data-label="Action"], .archive-table tbody td[data-label="Actions"] { 
            justify-content: flex-end !important; 
            padding-top: 12px !important; 
            border-bottom: none !important; 
        }
        .archive-table tbody td[data-label="Action"]::before, .archive-table tbody td[data-label="Actions"]::before { 
            display: none !important; 
        }

        .archive-table tbody tr.empty-row { 
            border: none !important; 
            box-shadow: none !important; 
            background: transparent !important; 
            padding: 0 !important; 
        }
        .archive-table tbody tr.empty-row td { 
            border-bottom: none !important; 
            justify-content: center !important; 
        }
        .archive-table tbody tr.empty-row td::before { 
            display: none !important; 
        }
    }

    /* Mobile (< 768px) */
    @media (max-width: 767.98px) {
        .archive-filter-bar { 
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px; 
            padding: 10px 12px; 
            margin-bottom: 10px;
        }
        .archive-filter-search { max-width: none; width: 100%; min-width: 0; }
        .archive-filter-dropdown { width: 100%; min-width: 0; }
        .archive-filter-reset { width: 100%; }
        .archive-reset-btn { width: 100% !important; justify-content: center !important; }

        .sc-pagination { flex-direction: column; align-items: center; gap: 8px; }
        .sc-pagination-controls { justify-content: center; }
    }
    @media (min-width: 480px) and (max-width: 767.98px) {
        .archive-filter-bar {
            grid-template-columns: 1fr 1fr;
        }
        .archive-filter-search { grid-column: 1 / -1; }
        .archive-filter-reset { grid-column: 1 / -1; }
    }
    @media (max-width: 479px) {
        .archive-table tbody td::before { min-width: 75px; font-size: 0.68rem; }
        .archive-table tbody td { font-size: 0.813rem !important; }
    }

    /* Collapsed Sidebar (768px - 1199.98px) */
    @media (min-width: 768px) and (max-width: 1199.98px) {
        .archive-filter-bar { gap: 8px 10px; padding: 10px 14px; margin-bottom: 12px; flex-wrap: wrap; }
        .archive-filter-search { flex: 1 1 190px; min-width: 170px; max-width: 250px; }
        .archive-filter-dropdown { flex: 1 1 120px; min-width: 115px; }
        .archive-filter-reset { flex: 0 0 auto; }

        .sc-pagination { flex-direction: row; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    }

    /* ══════════════════════════════════
       LARGE DESKTOP (1200px+): FULL TABLE
    ══════════════════════════════════ */
    @media (min-width: 1200px) {
        html, body { overflow: hidden !important; }
        .app { height: 100vh !important; overflow: hidden !important; }
        .main { height: 100vh !important; overflow: hidden !important; }

        .archive-filter-bar { flex-wrap: nowrap; gap: 10px; padding: 12px 16px; margin-bottom: 12px; }
        .archive-filter-search { flex: 0 0 260px; max-width: 260px; }
        .archive-filter-dropdown { flex: 1; min-width: 130px; }
        .archive-filter-reset { flex: 0 0 auto; }

        .archive-panel { flex: 1; min-height: 0; overflow: hidden; display: flex; flex-direction: column; }
        .archive-table-wrap { flex: 1; min-height: 0; overflow: auto; border: 1px solid #E5E7EB; border-radius: 8px; }
        .archive-table { min-width: 900px; width: 100%; table-layout: auto; }
        .archive-table thead th { padding: 12px 16px; font-size: 0.75rem; }
        .archive-table tbody td { padding: 12px 16px; font-size: 0.875rem; }
        .archive-table tbody td::before { display: none !important; content: none !important; }
        .archive-table tbody tr.empty-row td.empty-cell { white-space: normal !important; overflow: visible !important; max-width: none !important; }

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
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive" class="active"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        @if((string) session('admin_user_role') === 'eligibility_checker' || (string) session('admin_user_role') === 'social_worker')
        <li class="sidebar-dropdown" id="onlineRequestsDropdown">
            <a href="#" class="sidebar-dropdown-toggle" onclick="toggleDropdown('onlineRequestsDropdown'); return false;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <i data-lucide="file-text" style="width:20px;height:20px"></i>
                    <span>Online Requests</span>
                </div>
                <i data-lucide="chevron-down" style="width:16px;height:16px"></i>
            </a>
            <ul class="sidebar-dropdown-menu">
                <li><a href="/admin/social-case/online-requests">Pending Requests</a></li>
                <li><a href="/admin/social-case/online-requests/accepted">Accepted Requests</a></li>
                <li><a href="/admin/social-case/online-requests/rejected">Rejected Requests</a></li>
            </ul>
        </li>
        @endif
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <!-- Page Sub-Header -->
    <p class="archive-subtitle">View and manage archived social case study records.</p>

    <!-- Search and Filter Bar -->
    <div class="archive-filter-bar">
        <div class="archive-filter-search">
            <label class="archive-filter-label">Search by Name</label>
            <div class="archive-search-wrap">
                <input type="text" id="archiveSearch" placeholder="Search by name..."
                       oninput="view.archiveSearch=this.value;view.archivePage=1;renderArchive()">
                <button type="button" onclick="renderArchive()" title="Search">
                    <i data-lucide="search" style="width:16px;height:16px"></i>
                </button>
            </div>
        </div>
        <div class="archive-filter-dropdown" id="archiveBrgyDropdown">
            <label class="archive-filter-label">Filter by Barangay</label>
            <div onclick="toggleArchiveBrgyMenu()" class="archive-select-btn" id="archiveBrgyBtn">
                <i data-lucide="map-pin" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                <span id="archiveBrgyLabel" class="archive-select-label">All Barangays</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="archiveBrgyMenu" class="archive-menu" style="display:none">
                <div class="archive-brgy-opt" data-value="" onclick="selectArchiveBrgy(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Barangays</div>
            </div>
        </div>
        <div class="archive-filter-dropdown" id="archiveTypeDropdown">
            <label class="archive-filter-label">Filter by Type</label>
            <div onclick="toggleArchiveTypeMenu()" class="archive-select-btn" id="archiveTypeBtn">
                <i data-lucide="filter" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
                <span id="archiveTypeLabel" class="archive-select-label">All Types</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="archiveTypeMenu" class="archive-menu" style="display:none">
                <div class="archive-type-opt" data-value="" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Types</div>
                <div class="archive-type-opt" data-value="Medical Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Medical Assistance</div>
                <div class="archive-type-opt" data-value="Burial Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Burial Assistance</div>
                <div class="archive-type-opt" data-value="Educational Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Educational Assistance</div>
                <div class="archive-type-opt" data-value="Financial Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Financial Assistance</div>
                <div class="archive-type-opt" data-value="Food / Relief Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Food / Relief Assistance</div>
                <div class="archive-type-opt" data-value="Livelihood Assistance" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Livelihood Assistance</div>
                <div class="archive-type-opt" data-value="Other" onclick="selectArchiveType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">Other</div>
            </div>
        </div>
        <div class="archive-filter-reset">
            <button type="button" class="archive-reset-btn" onclick="resetArchiveFilters()">
                <i data-lucide="x" style="width:14px;height:14px"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table Panel -->
    <div class="archive-panel">
        <div class="archive-table-wrap">
            <table class="archive-table">
                <thead><tr><th>Control No</th><th>Client</th><th>Assistance Type</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody id="archiveTable"></tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="sc-pagination">
        <div class="sc-pagination-info" id="archivePaginationInfo">Showing 0 of 0 Archived Cases</div>
        <div class="sc-pagination-controls" id="archivePaginationControls"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') . '?v=' . filemtime(public_path('js/social-case.js')) }}"></script>
<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle('open');
    }
}

    function toggleArchiveTypeMenu(){
        var menu=document.getElementById('archiveTypeMenu');
        var btn=document.getElementById('archiveTypeBtn');
        var arrow=btn.querySelector('[data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightArchiveTypeOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }
    function selectArchiveType(el){
        var val=el.getAttribute('data-value');
        view.archiveFilter=val;
        view.archivePage=1;
        document.getElementById('archiveTypeLabel').textContent=el.textContent;
        document.getElementById('archiveTypeMenu').style.display='none';
        var arrow=document.querySelector('#archiveTypeBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightArchiveTypeOpt();
        
        // Add active class and data-filter color to button
        var btn=document.getElementById('archiveTypeBtn');
        if(val){
            btn.classList.add('active');
            btn.setAttribute('data-filter', val);
        } else {
            btn.classList.remove('active');
            btn.removeAttribute('data-filter');
        }
        
        renderArchive();
    }
    function highlightArchiveTypeOpt(){
        var opts=document.querySelectorAll('.archive-type-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===view.archiveFilter) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }
    function toggleArchiveBrgyMenu(){
        var menu=document.getElementById('archiveBrgyMenu');
        var arrow=document.querySelector('#archiveBrgyBtn [data-lucide="chevron-down"]');
        if(menu.style.display==='none'||!menu.style.display){
            menu.style.display='block';
            if(arrow) arrow.style.transform='rotate(180deg)';
            highlightArchiveBrgyOpt();
        }else{
            menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    }
    function selectArchiveBrgy(el){
        var val=el.getAttribute('data-value');
        view.archiveBarangay=val;
        view.archivePage=1;
        document.getElementById('archiveBrgyLabel').textContent=el.textContent;
        document.getElementById('archiveBrgyMenu').style.display='none';
        var arrow=document.querySelector('#archiveBrgyBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform='';
        highlightArchiveBrgyOpt();
        
        // Add active class to button if filter is selected
        var btn=document.getElementById('archiveBrgyBtn');
        if(val) btn.classList.add('active');
        else btn.classList.remove('active');
        
        renderArchive();
    }
    function highlightArchiveBrgyOpt(){
        var opts=document.querySelectorAll('.archive-brgy-opt');
        opts.forEach(function(o){
            if(o.getAttribute('data-value')===view.archiveBarangay) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }
    function resetArchiveFilters() {
        // Clear search
        var searchEl = document.getElementById('archiveSearch');
        if (searchEl) searchEl.value = '';
        view.archiveSearch = '';
        // Reset barangay
        view.archiveBarangay = '';
        document.getElementById('archiveBrgyLabel').textContent = 'All Barangays';
        document.getElementById('archiveBrgyBtn').classList.remove('active');
        highlightArchiveBrgyOpt();
        // Reset type
        view.archiveType = '';
        document.getElementById('archiveTypeLabel').textContent = 'All Types';
        document.getElementById('archiveTypeBtn').classList.remove('active');
        highlightArchiveTypeOpt();
        // Reset page and re-render
        view.archivePage = 1;
        renderArchive();
    }
    document.addEventListener('click',function(e){
        var typeDD=document.getElementById('archiveTypeDropdown');
        var brgyDD=document.getElementById('archiveBrgyDropdown');
        if(typeDD && !typeDD.contains(e.target)){
            var menu=document.getElementById('archiveTypeMenu');
            var arrow=document.querySelector('#archiveTypeBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
        if(brgyDD && !brgyDD.contains(e.target)){
            var menu=document.getElementById('archiveBrgyMenu');
            var arrow=document.querySelector('#archiveBrgyBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display='none';
            if(arrow) arrow.style.transform='';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadArchive();
    });
</script>
@endpush
