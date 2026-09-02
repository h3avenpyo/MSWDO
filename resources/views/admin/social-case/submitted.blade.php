@extends('admin.social-case.layout')
@section('title', 'Submitted Cases')

@section('content')
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
            <p class="mobile-brand-subtitle">Submitted Cases</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<style>
    /* ── Submitted page resets ── */
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
    .submitted-subtitle { color: #6B7280; font-size: 0.85rem; margin: 0 0 10px; white-space: normal; overflow-wrap: break-word; line-height: 1.4; }

    /* ── Submitted Requests section ── */
    .submitted-section-header { margin-bottom: 10px; }
    .submitted-section-title { font-size: 1.05rem; font-weight: 800; color: #1A237E; margin: 0 0 4px; letter-spacing: -0.01em; }

    /* ── Filter bar ── */
    .submitted-filter-bar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 12px; padding: 16px; background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; }
    .filter-item { display: flex; flex-direction: column; gap: 6px; }
    .filter-label { font-size: 0.75rem; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em; }

    .filter-search { flex: 0 1 280px; max-width: 320px; min-width: 220px; }
    .filter-search-wrap { display: flex; align-items: stretch; width: 100%; border-radius: 8px; box-sizing: border-box; transition: box-shadow .15s; }
    .filter-search-wrap:focus-within { box-shadow: 0 0 0 3px rgba(26,35,126,.12); border-radius: 8px; }
    .filter-search input { flex: 1 1 auto; width: 1%; min-width: 0; height: 44px !important; border: 1px solid #D1D5DB; border-right: none; border-radius: 8px 0 0 8px; padding: 0 16px; font-size: 0.875rem; color: #111827; background: #fff; outline: none; transition: border-color .15s; box-sizing: border-box !important; margin: 0 !important; }
    .filter-search input:focus { border-color: #1A237E; }
    .filter-search input::placeholder { color: #9CA3AF; }
    .filter-search-btn { height: 44px !important; padding: 0 20px; border: 1px solid #1A237E; border-radius: 0 8px 8px 0; background: #1A237E; color: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: background .15s; flex-shrink: 0; box-sizing: border-box !important; margin: 0 !important; align-self: stretch; }
    .filter-search-btn:hover { background: #121858; }

    .filter-reset { flex: 0 0 auto; display: flex; flex-direction: column; gap: 6px; }
    .filter-reset-btn { height: 44px; padding: 0 20px; border: 1px solid #EF4444; border-radius: 8px; background: #fff; color: #EF4444; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .15s; white-space: nowrap; }
    .filter-reset-btn:hover { background: #FEE2E2; border-color: #DC2626; }

    /* ── Panel / wrap ── */
    .submitted-panel { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 0; padding: 0; }
    .submitted-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: #fff; height: 500px !important; overflow-y: auto; }

    /* ── Table base ── */
    #submittedTable { width: 100%; border-collapse: separate; border-spacing: 0; table-layout: auto; }
    #submittedTable thead tr { background: #F8FAFC; border-bottom: 2px solid #E2E8F0; }
    #submittedTable thead th { padding: 12px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; white-space: nowrap; text-align: left; border-bottom: 2px solid #E2E8F0; }
    #submittedTable tbody tr { border-bottom: 1px solid #F1F5F9; transition: background .15s; }
    #submittedTable tbody tr:last-child { border-bottom: none; }
    #submittedTable tbody tr:hover { background: #F8FAFC; }
    #submittedTable tbody td { padding: 12px 14px; font-size: 0.875rem; color: #1E293B; vertical-align: middle; border-bottom: 1px solid #F1F5F9; }

    /* Column specific spacing */
    #submittedTable tbody td[data-label="Control No."] { min-width: 130px; white-space: nowrap; font-family: 'Courier New', monospace; font-size: 0.813rem; font-weight: 600; color: #1E293B; }
    #submittedTable tbody td[data-label="Client"] { min-width: 180px; font-weight: 600; color: #0F172A; white-space: normal; word-break: break-word; }
    #submittedTable tbody td[data-label="Forwarded By"] { min-width: 160px; white-space: nowrap; color: #475569; }
    #submittedTable tbody td[data-label="Date Submitted"] { min-width: 130px; white-space: nowrap; color: #64748B; font-size: 0.813rem; }
    #submittedTable tbody td[data-label="Action"] { min-width: 120px; white-space: nowrap; }

    .control-no { font-family: 'Courier New', monospace; font-size: 0.78rem; color: #374151; font-weight: 600; }

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
        .submitted-panel { background: transparent; border: none; padding: 0; box-shadow: none; }
        .submitted-table-wrap { overflow: visible; border: none; background: transparent; height: auto !important; }

        #submittedTable, 
        #submittedTable thead, 
        #submittedTable tbody, 
        #submittedTable tbody tr, 
        #submittedTable tbody td { 
            display: block !important; 
            width: 100% !important; 
        }
        #submittedTable { min-width: 0 !important; }
        #submittedTable thead { display: none !important; }

        #submittedTable tbody tr:not(.empty-row) { 
            background: #ffffff !important; 
            border: 1px solid #E2E8F0 !important; 
            border-radius: 12px !important; 
            margin-bottom: 12px !important; 
            padding: 14px 18px !important; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important; 
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        #submittedTable tbody tr:not(.empty-row):hover { 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }

        #submittedTable tbody td { 
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
        #submittedTable tbody td:last-child { 
            border-bottom: none !important; 
        }
        #submittedTable tbody td::before { 
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
        #submittedTable tbody td[data-label="Action"] { 
            justify-content: flex-end !important; 
            padding-top: 12px !important; 
            border-bottom: none !important; 
        }
        #submittedTable tbody td[data-label="Action"]::before { 
            display: none !important; 
        }
        #submittedTable tbody td[data-label="Action"] .btn { 
            width: auto !important; 
            min-width: 100px !important; 
        }

        #submittedTable tbody tr.empty-row { 
            border: none !important; 
            box-shadow: none !important; 
            background: transparent !important; 
            padding: 0 !important; 
        }
        #submittedTable tbody tr.empty-row td { 
            border-bottom: none !important; 
            justify-content: center !important; 
        }
        #submittedTable tbody tr.empty-row td::before { 
            display: none !important; 
        }
    }

    /* Mobile (< 768px) */
    @media (max-width: 767.98px) {
        .submitted-filter-bar { padding: 10px 12px; margin-bottom: 10px; }
        .filter-search { max-width: none; width: 100%; min-width: 0; }
        .filter-reset { width: 100%; }
        .filter-reset-btn { width: 100% !important; justify-content: center !important; }

        .sc-pagination { flex-direction: column; align-items: center; gap: 8px; }
        .sc-pagination-controls { justify-content: center; }
    }
    @media (max-width: 479px) {
        #submittedTable tbody td::before { min-width: 75px; font-size: 0.68rem; }
        #submittedTable tbody td { font-size: 0.813rem !important; }
    }

    /* Collapsed Sidebar (768px - 1199.98px) */
    @media (min-width: 768px) and (max-width: 1199.98px) {
        .submitted-filter-bar { padding: 10px 14px; margin-bottom: 12px; }
        .filter-search { flex: 0 1 280px; max-width: 320px; min-width: 220px; }

        .sc-pagination { flex-direction: row; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    }

    /* ══════════════════════════════════
       LARGE DESKTOP (1200px+): FULL TABLE
    ══════════════════════════════════ */
    @media (min-width: 1200px) {
        html, body { overflow: hidden !important; }
        .app { height: 100vh !important; overflow: hidden !important; }
        .main { height: 100vh !important; overflow-y: auto !important; overflow-x: hidden !important; }

        .submitted-filter-bar { padding: 16px; margin-bottom: 12px; }
        .filter-search { flex: 0 1 280px; max-width: 320px; min-width: 220px; }
        .filter-search-btn { height: 44px !important; }

        .submitted-panel { flex: 0 0 auto; overflow: visible; }
        .submitted-table-wrap { flex: 0 0 auto; overflow: auto; border: 1px solid #E5E7EB; border-radius: 8px; height: 500px !important; }
        #submittedTable { min-width: 900px; width: 100%; table-layout: auto; }
        #submittedTable thead th { padding: 12px 16px; font-size: 0.75rem; }
        #submittedTable tbody td { padding: 12px 16px; font-size: 0.875rem; }
        #submittedTable tbody td::before { display: none !important; content: none !important; }
        #submittedTable tbody tr.empty-row td.empty-cell { white-space: normal !important; overflow: visible !important; max-width: none !important; }

        .sc-pagination { flex-direction: row; justify-content: space-between; margin-top: 12px; flex-shrink: 0; }
        .sc-page-btn { height: 38px; }
    }

    /* ── Accepted Online Requests table ── */
    .accepted-section { margin-top: 32px; padding-top: 32px; border-top: 3px solid #1A237E; }
    .accepted-section-header { margin-bottom: 10px; }
    .accepted-section-title { font-size: 1.05rem; font-weight: 800; color: #1A237E; margin: 0 0 4px; letter-spacing: -0.01em; }
    .accepted-online-panel { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; }
    .accepted-online-table-wrap { overflow-x: auto; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: #fff; height: 500px !important; overflow-y: auto; -webkit-overflow-scrolling: touch; }
    #acceptedOnlineTable { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 800px; }
    #acceptedOnlineTable thead tr { background: #F8FAFC; border-bottom: 2px solid #E2E8F0; }
    #acceptedOnlineTable thead th { padding: 12px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; white-space: nowrap; text-align: left; border-bottom: 2px solid #E2E8F0; }
    #acceptedOnlineTable tbody tr { border-bottom: 1px solid #F1F5F9; transition: background .15s; }
    #acceptedOnlineTable tbody tr:last-child { border-bottom: none; }
    #acceptedOnlineTable tbody tr:hover { background: #F8FAFC; }
    #acceptedOnlineTable tbody td { padding: 12px 14px; font-size: 0.875rem; color: #1E293B; vertical-align: middle; border-bottom: 1px solid #F1F5F9; }
    #acceptedOnlineTable tbody td[data-label="Name"] { min-width: 200px; font-weight: 600; color: #0F172A; white-space: normal; word-break: break-word; }
    #acceptedOnlineTable tbody td[data-label="Service Type"],
    #acceptedOnlineTable tbody td[data-label="Assistance Type"],
    #acceptedOnlineTable tbody td[data-label="Barangay"] { min-width: 140px; white-space: nowrap; color: #475569; }
    #acceptedOnlineTable tbody td[data-label="Contact"] { min-width: 130px; white-space: nowrap; color: #475569; }
    #acceptedOnlineTable tbody td[data-label="Date Accepted"] { min-width: 130px; white-space: nowrap; color: #64748B; font-size: 0.813rem; }
    #acceptedOnlineTable tbody td[data-label="Action"] { min-width: 110px; white-space: nowrap; }

    @media (max-width: 1199.98px) {
        .accepted-online-panel { background: transparent; border: none; }
        .accepted-online-table-wrap { overflow: visible; border: none; background: transparent; height: auto !important; }
        #acceptedOnlineTable, #acceptedOnlineTable thead, #acceptedOnlineTable tbody, #acceptedOnlineTable tbody tr, #acceptedOnlineTable tbody td { display: block !important; width: 100% !important; }
        #acceptedOnlineTable { min-width: 0 !important; }
        #acceptedOnlineTable thead { display: none !important; }

        #acceptedOnlineTable tbody tr:not(.empty-row) { background: #ffffff !important; border: 1px solid #E2E8F0 !important; border-radius: 12px !important; margin-bottom: 12px !important; padding: 14px 18px !important; box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important; transition: transform 0.15s ease, box-shadow 0.15s ease; }
        #acceptedOnlineTable tbody tr:not(.empty-row):hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }

        #acceptedOnlineTable tbody td { display: flex !important; justify-content: space-between !important; align-items: center !important; padding: 8px 0 !important; border-bottom: 1px solid #F1F5F9 !important; font-size: 0.875rem !important; gap: 12px !important; white-space: normal !important; word-break: break-word !important; max-width: none !important; overflow: visible !important; min-width: 0 !important; }
        #acceptedOnlineTable tbody td:last-child { border-bottom: none !important; }
        #acceptedOnlineTable tbody td::before { content: attr(data-label) !important; font-weight: 700 !important; font-size: 0.72rem !important; color: #64748B !important; text-transform: uppercase !important; letter-spacing: 0.04em !important; min-width: 100px !important; flex-shrink: 0 !important; display: block !important; }
        #acceptedOnlineTable tbody td[data-label="Action"] { justify-content: flex-end !important; padding-top: 12px !important; border-bottom: none !important; }
        #acceptedOnlineTable tbody td[data-label="Action"]::before { display: none !important; }
        #acceptedOnlineTable tbody td[data-label="Action"] .btn { width: auto !important; min-width: 100px !important; }

        #acceptedOnlineTable tbody tr.empty-row { border: none !important; box-shadow: none !important; background: transparent !important; padding: 0 !important; }
        #acceptedOnlineTable tbody tr.empty-row td { border-bottom: none !important; justify-content: center !important; }
        #acceptedOnlineTable tbody tr.empty-row td::before { display: none !important; }
    }

    @media (max-width: 479px) {
        #acceptedOnlineTable tbody td::before { min-width: 75px; font-size: 0.68rem; }
        #acceptedOnlineTable tbody td { font-size: 0.813rem !important; }
    }

    /* ── Sidebar Badge Styling ── */
    .sidebar-badge {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-left: auto;
    }

    .badge-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 0.7rem;
        font-weight: 700;
        line-height: 1;
        color: #fff;
    }

    .badge-pending {
        background: #F59E0B;
    }

    .badge-accepted {
        background: #10B981;
    }

    .badge-rejected {
        background: #EF4444;
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        @if((string) session('admin_user_role') !== 'social_worker')
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>Client Eligibility</span></a></li>
        @endif
        @if((string) session('admin_user_role') !== 'eligibility_checker')
        <li><a href="/admin/social-case/submitted" class="active"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        @endif
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li class="sidebar-dropdown" id="onlineRequestsDropdown">
            <a href="#" class="sidebar-dropdown-toggle" onclick="toggleDropdown('onlineRequestsDropdown'); return false;">
                <i data-lucide="file-text" style="width:20px;height:20px"></i>
                <span>Online Requests</span>
                <i data-lucide="chevron-down" class="dropdown-chevron" style="width:16px;height:16px;margin-left:auto;"></i>
            </a>
            <ul class="sidebar-dropdown-menu">
                <li><a href="/admin/social-case/online-requests"><i data-lucide="clock" style="width:18px;height:18px"></i><span>Pending Requests</span><span class="badge-count badge-pending" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#F59E0B;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['pending'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/accepted"><i data-lucide="check-circle" style="width:18px;height:18px"></i><span>Accepted Requests</span><span class="badge-count badge-accepted" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#10B981;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['accepted'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/rejected"><i data-lucide="x-circle" style="width:18px;height:18px"></i><span>Rejected Requests</span><span class="badge-count badge-rejected" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#EF4444;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['rejected'] ?? 0 }}</span></a></li>
            </ul>
        </li>
        @endif
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <!-- Submitted Requests Section -->
    <div class="submitted-section-header">
        <h2 class="submitted-section-title">Submitted Requests</h2>
        <p class="submitted-subtitle">Clients forwarded by the Eligibility Checker and waiting to be encoded.</p>
    </div>

    <!-- Search Bar -->
    <div class="submitted-filter-bar">
        <div class="filter-item filter-search">
            <label class="filter-label">Search</label>
            <div class="filter-search-wrap">
                <input type="text" id="submittedSearch" placeholder="Search client name, control no..."
                       oninput="filterSubmitted()" onkeydown="if(event.key==='Enter'){event.preventDefault();filterSubmitted();}">
                <button type="button" class="filter-search-btn" onclick="filterSubmitted()">
                    <i data-lucide="search" style="width:18px;height:18px"></i>
                </button>
            </div>
        </div>
        <div class="filter-item filter-reset">
            <label class="filter-label">&nbsp;</label>
            <button type="button" class="filter-reset-btn" onclick="clearSubmittedSearch()">
                <i data-lucide="x" style="width:16px;height:16px"></i> Clear
            </button>
        </div>
    </div>

    <!-- Table Panel -->
    <div class="submitted-panel">
        <div class="submitted-table-wrap">
            <table id="submittedTable">
                <thead>
                    <tr>
                        <th>Control No.</th>
                        <th>Client</th>
                        <th>Forwarded By</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submitted as $c)
                    <tr data-name="{{ mb_strtolower($c->client ? $c->client->full_name : '') }}" data-search="{{ mb_strtolower(($c->case_number ?? '') . ' ' . ($c->client ? $c->client->full_name : '') . ' ' . ($c->eligibleByUser ? $c->eligibleByUser->name : '')) }}">
                        <td data-label="Control No."><span class="control-no" title="{{ $c->case_number }}">{{ $c->case_number ?: '—' }}</span></td>
                        <td data-label="Client" title="{{ $c->client ? $c->client->full_name : '' }}">{{ $c->client ? $c->client->full_name : 'Unnamed' }}</td>
                        <td data-label="Forwarded By" title="{{ $c->eligibleByUser ? $c->eligibleByUser->name : 'Eligibility Checker' }}">{{ $c->eligibleByUser ? $c->eligibleByUser->name : 'Eligibility Checker' }}</td>
                        <td data-label="Date Submitted">{{ $c->eligible_at ? \Carbon\Carbon::parse($c->eligible_at)->format('M d, Y') : '—' }}</td>
                        <td data-label="Action">
                            <button class="btn primary btn-sm" onclick="startEncodingFromQueue('{{ $c->id }}', '{{ $c->client ? addslashes($c->client->full_name) : '' }}')">
                                <i data-lucide="pen-line" style="width:14px;height:14px"></i> Encode
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="5" class="empty-cell">
                            <div class="empty-state-content">
                                <div class="empty-icon-wrap">
                                    <i data-lucide="send"></i>
                                </div>
                                <div class="empty-title">No submitted cases</div>
                                <div class="empty-subtitle">Submitted cases will appear here</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="sc-pagination">
        <div class="sc-pagination-info" id="submittedPaginationInfo">Showing 0 of 0 Records</div>
        <div class="sc-pagination-controls" id="submittedPaginationControls"></div>
    </div>

    <!-- Accepted Online Requests -->
    <div class="accepted-section">
        <div class="accepted-section-header">
            <h2 class="accepted-section-title">Accepted Online Requests</h2>
            <p class="submitted-subtitle">Online service requests accepted by the Eligibility Checker.</p>
        </div>
        <div class="accepted-online-panel">
            <div class="accepted-online-table-wrap">
                <table id="acceptedOnlineTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Service Type</th>
                            <th>Assistance Type</th>
                            <th>Barangay</th>
                            <th>Contact</th>
                            <th>Date Accepted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($acceptedOnlineRequests as $req)
                        @php
                        $details = [
                            'id' => $req->id,
                            'first_name' => $req->first_name,
                            'last_name' => $req->last_name,
                            'email' => $req->email,
                            'contact_number' => $req->contact_number,
                            'dob' => $req->dob ? $req->dob->format('Y-m-d') : '',
                            'barangay' => $req->barangay,
                            'address' => $req->address ?? '',
                            'service_type' => ucfirst(str_replace('_', ' ', $req->service_type)),
                            'assistance_type' => ucfirst(str_replace('_', ' ', $req->assistance_type)),
                            'situation' => $req->situation ?? 'N/A',
                            'created_at' => $req->created_at->format('M d, Y g:i A'),
                            'attachments' => $req->attachments->map(function ($att) {
                                return [
                                    'file_name' => $att->file_name,
                                    'file_url' => asset('storage/' . $att->file_path),
                                    'file_size' => $att->file_size,
                                ];
                            })->toArray(),
                        ];
                        @endphp
                        <tr data-details="{{ json_encode($details) }}">
                            <td data-label="Name">
                                <div>{{ $req->first_name }} {{ $req->last_name }}</div>
                                <div class="text-xs text-slate-500">{{ $req->email }}</div>
                            </td>
                            <td data-label="Service Type">{{ ucfirst(str_replace('_', ' ', $req->service_type)) }}</td>
                            <td data-label="Assistance Type">{{ ucfirst(str_replace('_', ' ', $req->assistance_type)) }}</td>
                            <td data-label="Barangay">{{ $req->barangay }}</td>
                            <td data-label="Contact">{{ $req->contact_number }}</td>
                            <td data-label="Date Accepted">{{ $req->updated_at->format('M d, Y') }}</td>
                            <td data-label="Action">
                                <button class="btn primary btn-sm" onclick="viewAcceptedOnlineRequest(this)" title="View">
                                    <i data-lucide="eye" style="width:14px;height:14px"></i> View
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="7" class="empty-cell">
                                <div class="empty-state-content">
                                    <div class="empty-icon-wrap">
                                        <i data-lucide="inbox"></i>
                                    </div>
                                    <div class="empty-title">No accepted online requests</div>
                                    <div class="empty-subtitle">Accepted online service requests will appear here.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <div class="sc-pagination">
            <div class="sc-pagination-info" id="acceptedPaginationInfo">Showing 0 of 0 Records</div>
            <div class="sc-pagination-controls" id="acceptedPaginationControls"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') . '?v=' . filemtime(public_path('js/social-case.js')) }}"></script>
<script>
    var _submittedPage = 1;
    var _submittedPageSize = 10;

    function getSubmittedRows() {
        return Array.from(document.querySelectorAll('#submittedTable tbody tr:not(.empty-row)'));
    }

    function clearSubmittedSearch() {
        document.getElementById('submittedSearch').value = '';
        _submittedPage = 1;
        _acceptedPage = 1;
        renderSubmitted();
        renderAccepted();
    }

    function renderSubmitted() {
        var query = (document.getElementById('submittedSearch').value || '').trim().toLowerCase();
        var allRows = getSubmittedRows();

        // Filter
        var filtered = allRows.filter(function(row) {
            var searchData = (row.getAttribute('data-search') || row.getAttribute('data-name') || row.textContent || '').toLowerCase();
            return !query || searchData.indexOf(query) !== -1;
        });

        // Reset to page 1 only when search changed
        var totalPages = Math.max(1, Math.ceil(filtered.length / _submittedPageSize));
        if (_submittedPage > totalPages) _submittedPage = totalPages;

        var startIndex = (_submittedPage - 1) * _submittedPageSize;
        var endIndex   = startIndex + _submittedPageSize;
        var pageRows   = filtered.slice(startIndex, endIndex);

        // Hide all rows, then show only current page slice
        allRows.forEach(function(r) { r.style.display = 'none'; });
        pageRows.forEach(function(r) { r.style.display = ''; });

        // Empty state
        var emptyRow = document.querySelector('#submittedTable tbody tr.empty-row');
        if (allRows.length > 0) {
            if (filtered.length === 0) {
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.className = 'empty-row';
                    emptyRow.innerHTML = '<td colspan="5" class="empty-cell">' +
                        '<div class="empty-state-content">' +
                        '<div class="empty-icon-wrap"><i data-lucide="search-x"></i></div>' +
                        '<div class="empty-title">No matching submitted cases</div>' +
                        '<div class="empty-subtitle">Try adjusting your search</div>' +
                        '</div></td>';
                    document.querySelector('#submittedTable tbody').appendChild(emptyRow);
                } else {
                    emptyRow.querySelector('.empty-title').textContent = 'No matching submitted cases';
                    emptyRow.querySelector('.empty-subtitle').textContent = 'Try adjusting your search';
                    var iconWrap = emptyRow.querySelector('.empty-icon-wrap');
                    if (iconWrap) iconWrap.innerHTML = '<i data-lucide="search-x"></i>';
                }
                emptyRow.style.display = '';
            } else {
                if (emptyRow) emptyRow.style.display = 'none';
            }
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Pagination info
        var infoEl = document.getElementById('submittedPaginationInfo');
        if (infoEl) {
            if (filtered.length === 0) {
                infoEl.textContent = 'Showing 0 of 0 Records';
            } else {
                infoEl.textContent = 'Showing ' + (startIndex + 1) + '\u2013' + Math.min(endIndex, filtered.length) + ' of ' + filtered.length + ' Records';
            }
        }

        // Pagination controls
        var controls = document.getElementById('submittedPaginationControls');
        if (controls) {
            var btns = '';
            btns += '<button class="sc-page-btn" ' + (_submittedPage <= 1 ? 'disabled' : '') +
                    ' onclick="goToSubmittedPage(' + (_submittedPage - 1) + ')">' +
                    '<i data-lucide="chevron-left" style="width:14px;height:14px"></i> Previous</button>';
            var maxBtns = 5;
            var sp = Math.max(1, _submittedPage - Math.floor(maxBtns / 2));
            var ep = Math.min(totalPages, sp + maxBtns - 1);
            if (ep - sp < maxBtns - 1) sp = Math.max(1, ep - maxBtns + 1);
            for (var i = sp; i <= ep; i++) {
                btns += '<button class="sc-page-btn ' + (i === _submittedPage ? 'active' : '') +
                        '" onclick="goToSubmittedPage(' + i + ')">' + i + '</button>';
            }
            btns += '<button class="sc-page-btn" ' + (_submittedPage >= totalPages ? 'disabled' : '') +
                    ' onclick="goToSubmittedPage(' + (_submittedPage + 1) + ')">Next ' +
                    '<i data-lucide="chevron-right" style="width:14px;height:14px"></i></button>';
            controls.innerHTML = btns;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function filterSubmitted() {
        _submittedPage = 1;
        _acceptedPage = 1;
        renderSubmitted();
        renderAccepted();
    }

    function goToSubmittedPage(page) {
        _submittedPage = page;
        renderSubmitted();
    }

    var _acceptedPage = 1;
    var _acceptedPageSize = 10;

    function getAcceptedRows() {
        return Array.from(document.querySelectorAll('#acceptedOnlineTable tbody tr:not(.empty-row)'));
    }

    function renderAccepted() {
        var query = (document.getElementById('submittedSearch') ? document.getElementById('submittedSearch').value : '').trim().toLowerCase();
        var allRows = getAcceptedRows();

        var filtered = allRows.filter(function(row) {
            var searchData = (row.getAttribute('data-search') || row.textContent || '').toLowerCase();
            return !query || searchData.indexOf(query) !== -1;
        });

        var totalPages = Math.max(1, Math.ceil(filtered.length / _acceptedPageSize));
        if (_acceptedPage > totalPages) _acceptedPage = totalPages;

        var startIndex = (_acceptedPage - 1) * _acceptedPageSize;
        var endIndex   = startIndex + _acceptedPageSize;
        var pageRows   = filtered.slice(startIndex, endIndex);

        allRows.forEach(function(r) { r.style.display = 'none'; });
        pageRows.forEach(function(r) { r.style.display = ''; });

        var emptyRow = document.querySelector('#acceptedOnlineTable tbody tr.empty-row');
        if (allRows.length > 0) {
            if (filtered.length === 0) {
                if (!emptyRow) {
                    emptyRow = document.createElement('tr');
                    emptyRow.className = 'empty-row';
                    emptyRow.innerHTML = '<td colspan="7" class="empty-cell">' +
                        '<div class="empty-state-content">' +
                        '<div class="empty-icon-wrap"><i data-lucide="search-x"></i></div>' +
                        '<div class="empty-title">No matching accepted requests</div>' +
                        '<div class="empty-subtitle">Try adjusting your search</div>' +
                        '</div></td>';
                    document.querySelector('#acceptedOnlineTable tbody').appendChild(emptyRow);
                } else {
                    emptyRow.querySelector('.empty-title').textContent = 'No matching accepted requests';
                    emptyRow.querySelector('.empty-subtitle').textContent = 'Try adjusting your search';
                    var iconWrap = emptyRow.querySelector('.empty-icon-wrap');
                    if (iconWrap) iconWrap.innerHTML = '<i data-lucide="search-x"></i>';
                }
                emptyRow.style.display = '';
            } else {
                if (emptyRow) emptyRow.style.display = 'none';
            }
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Pagination info
        var infoEl = document.getElementById('acceptedPaginationInfo');
        if (infoEl) {
            if (filtered.length === 0) {
                infoEl.textContent = 'Showing 0 of 0 Records';
            } else {
                infoEl.textContent = 'Showing ' + (startIndex + 1) + '\u2013' + Math.min(endIndex, filtered.length) + ' of ' + filtered.length + ' Records';
            }
        }

        // Pagination controls
        var controls = document.getElementById('acceptedPaginationControls');
        if (controls) {
            var btns = '';
            btns += '<button class="sc-page-btn" ' + (_acceptedPage <= 1 ? 'disabled' : '') +
                    ' onclick="goToAcceptedPage(' + (_acceptedPage - 1) + ')">' +
                    '<i data-lucide="chevron-left" style="width:14px;height:14px"></i> Previous</button>';
            var maxBtns = 5;
            var sp = Math.max(1, _acceptedPage - Math.floor(maxBtns / 2));
            var ep = Math.min(totalPages, sp + maxBtns - 1);
            if (ep - sp < maxBtns - 1) sp = Math.max(1, ep - maxBtns + 1);
            for (var i = sp; i <= ep; i++) {
                btns += '<button class="sc-page-btn ' + (i === _acceptedPage ? 'active' : '') +
                        '" onclick="goToAcceptedPage(' + i + ')">' + i + '</button>';
            }
            btns += '<button class="sc-page-btn" ' + (_acceptedPage >= totalPages ? 'disabled' : '') +
                    ' onclick="goToAcceptedPage(' + (_acceptedPage + 1) + ')">Next ' +
                    '<i data-lucide="chevron-right" style="width:14px;height:14px"></i></button>';
            controls.innerHTML = btns;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function goToAcceptedPage(page) {
        _acceptedPage = page;
        renderAccepted();
    }

    function viewAcceptedOnlineRequest(btn) {
        var row = btn.closest('tr');
        if (!row) return;
        var details = {};
        try { details = JSON.parse(row.getAttribute('data-details') || '{}'); } catch (e) {}

        var attachmentsHtml = '';
        if (details.attachments && details.attachments.length > 0) {
            var items = details.attachments.map(function (a) {
                return '<li style="margin-bottom:4px;"><a href="' + a.file_url + '" target="_blank" style="color:#1A237E;text-decoration:underline;">' + a.file_name + '</a></li>';
            }).join('');
            attachmentsHtml = '<div style="margin-top:12px;"><h4 style="margin:0 0 8px 0;color:#1A237E;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Attached Files</h4><ul style="margin:0;padding-left:20px;">' + items + '</ul></div>';
        } else {
            attachmentsHtml = '<div style="margin-top:12px;"><p style="margin:0;font-size:14px;color:#6B7280;">No files attached</p></div>';
        }

        Swal.fire({
            title: '<div style="display: flex; align-items: center; gap: 10px;"><i data-lucide="file-text" style="width: 24px; height: 24px; color: #1A237E;"></i><span>Accepted Online Request</span></div>',
            html: '<div style="text-align: left; padding: 10px;">' +
                '<div style="background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 16px;">' +
                    '<h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Personal Information</h4>' +
                    '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">' +
                        '<div><span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Full Name</span><span style="font-size: 14px; color: #1F2937; font-weight: 500;">' + details.first_name + ' ' + details.last_name + '</span></div>' +
                        '<div><span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Email</span><span style="font-size: 14px; color: #1F2937;">' + details.email + '</span></div>' +
                        '<div><span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Contact Number</span><span style="font-size: 14px; color: #1F2937;">' + details.contact_number + '</span></div>' +
                        '<div><span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Barangay</span><span style="font-size: 14px; color: #1F2937;">' + details.barangay + '</span></div>' +
                    '</div>' +
                '</div>' +
                '<div style="background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 16px;">' +
                    '<h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Service Information</h4>' +
                    '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">' +
                        '<div><span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Service Type</span><span style="font-size: 14px; color: #1F2937;">' + details.service_type + '</span></div>' +
                        '<div><span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Assistance Type</span><span style="font-size: 14px; color: #1F2937;">' + details.assistance_type + '</span></div>' +
                    '</div>' +
                '</div>' +
                '<div style="background: #F8FAFC; border-radius: 8px; padding: 16px;">' +
                    '<h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Request Details</h4>' +
                    '<div style="margin-bottom: 12px;"><span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Date Submitted</span><span style="font-size: 14px; color: #1F2937;">' + details.created_at + '</span></div>' +
                    '<div style="margin-bottom: 12px;"><span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Situation</span><p style="margin: 0; font-size: 14px; color: #1F2937; line-height: 1.5;">' + details.situation + '</p></div>' +
                    attachmentsHtml +
                '</div>' +
            '</div>',
            icon: false,
            showDenyButton: true,
            denyButtonText: 'Encode',
            denyButtonColor: '#1A237E',
            confirmButtonText: 'Close',
            confirmButtonColor: '#6B7280',
            width: '600px',
            didOpen: function () {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }).then(function (result) {
            if (result.isDenied) {
                encodeAcceptedOnlineRequest(details);
            }
        });
    }

    function encodeAcceptedOnlineRequest(details) {
        var fullName = ((details.first_name || '') + ' ' + (details.last_name || '')).trim();

        sessionStorage.setItem('intake_clientName', fullName);
        sessionStorage.setItem('intake_clientAddress', matchIntakeBarangay(details.barangay));
        sessionStorage.setItem('intake_clientContact', details.contact_number || '');
        sessionStorage.setItem('intake_clientBirthdate', details.dob || '');
        sessionStorage.setItem('intake_clientAge', computeAgeFromDob(details.dob) || '');
        sessionStorage.setItem('intake_onlineRequestId', details.id || '');
        sessionStorage.removeItem('intake_caseId');
        window.location.href = '/admin/social-case/intake';
    }

    function matchIntakeBarangay(raw) {
        if (!raw) return '';
        var overrides = { 'MALAKING TATIAO': 'Malaking Tatyao' };
        var override = overrides[String(raw).trim().toUpperCase()];
        if (override) return override;

        var B = (typeof BARANGAYS !== 'undefined') ? BARANGAYS : [];
        var norm = String(raw).toLowerCase().replace(/[^a-z0-9]/g, '');
        var romanMap = { '1': 'i', '2': 'ii', '3': 'iii', '4': 'iv', '5': 'v' };
        var m = norm.match(/^([a-z]+)(\d+)$/);
        if (m && romanMap[m[2]]) norm = m[1] + romanMap[m[2]];

        if (norm.indexOf('poblacion') === 0 && norm.length > 'poblacion'.length) {
            var target = 'barangay' + norm.replace('poblacion', '') + 'poblacion';
            for (var i = 0; i < B.length; i++) {
                if (B[i].toLowerCase().replace(/[^a-z0-9]/g, '') === target) return B[i];
            }
        }

        for (var j = 0; j < B.length; j++) {
            if (B[j].toLowerCase().replace(/[^a-z0-9]/g, '') === norm) return B[j];
        }
        return String(raw).trim();
    }

    function computeAgeFromDob(dob) {
        if (!dob) return '';
        var birth = new Date(dob);
        if (isNaN(birth.getTime())) return '';
        var today = new Date();
        var age = today.getFullYear() - birth.getFullYear();
        var mo = today.getMonth() - birth.getMonth();
        if (mo < 0 || (mo === 0 && today.getDate() < birth.getDate())) age--;
        return String(age);
    }

    document.addEventListener('DOMContentLoaded', function(){
        if(typeof lucide !== 'undefined') lucide.createIcons();
        renderSubmitted();
        renderAccepted();
    });
</script>
@endpush
