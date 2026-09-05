@extends('admin.social-case.layout')
@section('title', 'Rejected Online Requests')
@section('page_title', 'Rejected Online Requests')

@section('content')
<style>
    /* Resets & Base Layout */
    html, body {
        overflow-x: auto !important;
        overflow-y: auto !important;
    }
    .main {
        display: flex !important;
        flex-direction: column !important;
        padding-top: 14px !important;
        overflow-x: auto !important;
        overflow-y: auto !important;
    }
    @media (max-width: 767.98px) {
        .main {
            padding-top: 72px !important;
        }
    }

    /* ── Filter bar ── */
    .online-filter-bar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 12px; }
    .filter-item { display: flex; flex-direction: column; gap: 6px; }
    .filter-label { font-size: 0.75rem; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.05em; }

    .filter-search { flex: 1 1 200px; min-width: 180px; }
    .filter-search-wrap { display: flex; align-items: stretch; width: 100%; border-radius: 8px; box-sizing: border-box; transition: box-shadow .15s; }
    .filter-search-wrap:focus-within { box-shadow: 0 0 0 3px rgba(26,35,126,.12); border-radius: 8px; }
    .filter-search input { flex: 1 1 auto; width: 1%; min-width: 0; height: 44px !important; border: 1px solid #D1D5DB; border-right: none; border-radius: 8px 0 0 8px; padding: 0 16px; font-size: 0.875rem; color: #111827; background: #fff; outline: none; transition: border-color .15s; box-sizing: border-box !important; margin: 0 !important; }
    .filter-search input:focus { border-color: #1A237E; }
    .filter-search input::placeholder { color: #9CA3AF; }
    .filter-search-btn { height: 44px !important; padding: 0 20px; border: 1px solid #1A237E; border-radius: 0 8px 8px 0; background: #1A237E; color: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: background .15s; flex-shrink: 0; box-sizing: border-box !important; margin: 0 !important; align-self: stretch; }
    .filter-search-btn:hover { background: #121858; }

    .filter-dropdown { flex: 1 1 200px; min-width: 180px; position: relative; }
    .filter-select-btn { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 0 14px; height: 44px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 0.875rem; cursor: pointer; background: #fff; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
    .filter-select-btn:hover { border-color: #9CA3AF; }
    .filter-select-label { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #111827; font-weight: 500; }
    .filter-menu { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #D1D5DB; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 50; max-height: 260px; overflow-y: auto; padding: 4px; }

    .filter-reset { flex: 0 0 auto; display: flex; flex-direction: column; gap: 6px; }
    .filter-reset-btn { height: 44px; padding: 0 20px; border: 1px solid #EF4444; border-radius: 8px; background: #fff; color: #EF4444; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: none; align-items: center; gap: 6px; transition: all .15s; white-space: nowrap; }
    .filter-reset-btn.visible { display: inline-flex; }
    .filter-reset-btn:hover { background: #FEE2E2; border-color: #DC2626; }

    .type-opt.selected, .brgy-opt.selected { background: #EEF2FF; color: #1A237E; font-weight: 600; }
    .type-opt:hover, .brgy-opt:hover { background: #F3F4F6; }
    .filter-select-btn.active { border-color: #1A237E; background: #EEF2FF; }

    @media (max-width: 767.98px) {
        .online-filter-bar {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            padding: 12px !important;
            margin-bottom: 10px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .filter-item {
            gap: 4px !important;
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }
        .filter-label { font-size: 0.68rem !important; }
        .filter-search {
            grid-column: 1 / -1 !important;
            min-width: 0 !important;
        }
        .filter-search-wrap { width: 100% !important; box-sizing: border-box !important; }
        .filter-search input {
            height: 40px !important;
            font-size: 0.8rem !important;
            min-width: 0 !important;
            width: 1% !important;
            flex: 1 1 auto !important;
            box-sizing: border-box !important;
        }
        .filter-search-btn { height: 40px !important; padding: 0 14px !important; }
        .filter-dropdown {
            min-width: 0 !important;
            width: 100% !important;
        }
        .filter-select-btn {
            height: 40px !important;
            padding: 0 10px !important;
            font-size: 0.8rem !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .filter-reset {
            grid-column: 1 / -1 !important;
            min-width: 0 !important;
            width: 100% !important;
        }
        .filter-reset-btn {
            height: 40px !important;
            font-size: 0.8rem !important;
            width: 100% !important;
            justify-content: center !important;
            box-sizing: border-box !important;
        }
    }

    @media (min-width: 768px) and (max-width: 1199.98px) {
        .online-filter-bar { gap: 10px; padding: 14px; margin-bottom: 12px; flex-wrap: wrap; }
        .filter-search { flex: 1 1 180px; min-width: 160px; }
        .filter-dropdown { flex: 1 1 180px; min-width: 160px; }
        .filter-reset { flex: 0 0 auto; }
    }

    @media (min-width: 1200px) {
        .online-filter-bar { flex-wrap: nowrap; gap: 12px; padding: 16px; margin-bottom: 12px; }
        .filter-search { flex: 1 1 200px; min-width: 180px; }
        .filter-dropdown { flex: 1 1 200px; min-width: 180px; }
        .filter-reset { flex: 0 0 auto; }
    }

    .online-requests-panel {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
    }
    .online-requests-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        border: 2px solid #CBD5E1;
        border-radius: 8px;
        background: #fff;
    }

    /* Table Base */
    #onlineRequestsTable {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: auto;
    }
    #onlineRequestsTable thead tr {
        background: #F8FAFC;
        border-bottom: 2px solid #94A3B8;
    }
    #onlineRequestsTable thead th {
        padding: 12px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        white-space: nowrap;
        text-align: left;
        border-bottom: 2px solid #94A3B8;
    }
    #onlineRequestsTable tbody tr {
        border-bottom: 1px solid #CBD5E1;
        transition: background .15s;
    }
    #onlineRequestsTable tbody tr:last-child {
        border-bottom: none;
    }
    #onlineRequestsTable tbody tr:hover {
        background: #F8FAFC;
    }
    #onlineRequestsTable tbody td {
        padding: 12px 14px;
        font-size: 0.875rem;
        color: #1E293B;
        vertical-align: middle;
        border-bottom: 1px solid #CBD5E1;
    }
    
    /* Column specific spacing (Desktop) */
    #onlineRequestsTable tbody td[data-label="Name"] {
        min-width: 180px;
        font-weight: 600;
        color: #0F172A;
        white-space: normal;
        word-break: break-word;
    }
    #onlineRequestsTable tbody td[data-label="Contact"] {
        min-width: 130px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Service Type"] {
        min-width: 140px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Assistance Type"] {
        min-width: 140px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Barangay"] {
        min-width: 130px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Date Rejected"] {
        min-width: 130px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Action"] {
        min-width: 90px;
        white-space: nowrap;
    }
    
    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .status-rejected {
        background: #FEE2E2;
        color: #DC2626;
    }
    
    /* Empty State */
    .empty-row {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    .empty-cell {
        padding: 3rem 1rem !important;
        text-align: center !important;
        border: none !important;
        vertical-align: middle !important;
    }
    .empty-cell::before {
        display: none !important;
        content: none !important;
    }
    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 12px;
        padding: 2rem 1rem;
        margin: 20px auto;
    }
    .empty-icon-wrap {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #EEF2FF;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1A237E;
    }
    .empty-icon-wrap svg {
        width: 36px;
        height: 36px;
    }
    .empty-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1F2937;
        margin: 0;
    }
    .empty-subtitle {
        font-size: 0.875rem;
        color: #6B7280;
        margin: 0;
        line-height: 1.5;
        max-width: 360px;
    }
    
    /* Pagination */
    .sc-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        flex-shrink: 0;
        padding: 0;
        flex-wrap: wrap;
        border: none;
    }
    .sc-pagination-info {
        font-size: 0.813rem;
        color: #6B7280;
        font-weight: 500;
    }
    .sc-pagination-controls {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }
    .sc-page-btn {
        height: 36px;
        min-width: 36px;
        padding: 0 10px;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        background: #fff;
        color: #374151;
        font-size: 0.813rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s;
        text-decoration: none;
    }
    .sc-page-btn:hover:not([disabled]):not(.active) {
        background: #F8FAFC;
        border-color: #CBD5E1;
    }
    .sc-page-btn.active {
        background: #1A237E;
        color: #fff;
        border-color: #1A237E;
        font-weight: 700;
    }
    .sc-page-btn[disabled],
    .sc-page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Action Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }
    .btn-primary {
        background: #1A237E;
        color: #fff;
    }
    .btn-primary:hover {
        background: #121858;
    }
    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    /* ═══════════════════════════════════════════════════════════════
       MOBILE & TABLET (< 1200px): CARD VIEW
    ═══════════════════════════════════════════════════════════════ */
    @media (max-width: 1199.98px) {
        .online-requests-panel {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }
        .online-requests-table-wrap {
            overflow: visible !important;
            border: none !important;
            background: transparent !important;
        }
        
        #onlineRequestsTable,
        #onlineRequestsTable thead,
        #onlineRequestsTable tbody,
        #onlineRequestsTable tbody tr,
        #onlineRequestsTable tbody td {
            display: block !important;
            width: 100% !important;
        }
        #onlineRequestsTable {
            min-width: 0 !important;
        }
        #onlineRequestsTable thead {
            display: none !important;
        }
        
        #onlineRequestsTable tbody tr:not(.empty-row) {
            background: #ffffff !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 12px !important;
            margin-bottom: 12px !important;
            padding: 14px 18px !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        #onlineRequestsTable tbody tr:not(.empty-row):hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }
        
        #onlineRequestsTable tbody td {
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
            text-align: right;
        }
        #onlineRequestsTable tbody td:last-child {
            border-bottom: none !important;
        }
        #onlineRequestsTable tbody td::before {
            content: attr(data-label) !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            color: #64748B !important;
            flex-shrink: 0 !important;
            min-width: 110px !important;
            text-align: left;
            display: block !important;
        }
        #onlineRequestsTable tbody td .req-val-wrap {
            text-align: right;
        }
        #onlineRequestsTable tbody td[data-label="Action"] {
            justify-content: flex-end !important;
            padding-top: 12px !important;
            border-bottom: none !important;
        }
        #onlineRequestsTable tbody td[data-label="Action"]::before {
            display: none !important;
        }
        
        #onlineRequestsTable tbody tr.empty-row {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
        }
        #onlineRequestsTable tbody tr.empty-row td {
            border-bottom: none !important;
            justify-content: center !important;
            text-align: center !important;
        }
        #onlineRequestsTable tbody tr.empty-row td::before {
            display: none !important;
        }
    }

    /* Small Screens (< 768px) */
    @media (max-width: 767.98px) {
        .sc-pagination {
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .sc-pagination-controls {
            justify-content: flex-end;
            padding-right: 20px;
        }
    }

    /* Extra Small Devices (< 480px) */
    @media (max-width: 479px) {
        #onlineRequestsTable tbody td::before {
            min-width: 85px !important;
            font-size: 0.7rem !important;
        }
        #onlineRequestsTable tbody td {
            font-size: 0.813rem !important;
            padding: 6px 0 !important;
        }
        #onlineRequestsTable tbody tr:not(.empty-row) {
            padding: 12px 14px !important;
        }
    }

    /* Large Desktop (>= 1200px) */
    @media (min-width: 1200px) {
        html, body {
            overflow: hidden !important;
        }
        .app {
            height: 100vh !important;
            overflow: hidden !important;
        }
        .main {
            height: 100vh !important;
            overflow: hidden !important;
        }
        .online-requests-panel {
            flex: 1;
            min-height: 0;
            height: auto;
        }
        .online-requests-table-wrap {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
        }
    }

    /* Responsive Modal Styles */
    .modal-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    @media (max-width: 575.98px) {
        .modal-grid-2 {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
        }
        .swal2-popup {
            padding: 14px 10px !important;
            width: 95vw !important;
        }
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
            <p class="mobile-brand-subtitle">Rejected Online Requests</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

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
        <li><a href="/admin/social-case/submitted"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        @endif
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li class="sidebar-dropdown open" id="onlineRequestsDropdown">
            <a href="#" class="sidebar-dropdown-toggle" onclick="toggleDropdown('onlineRequestsDropdown'); return false;">
                <i data-lucide="file-text" style="width:20px;height:20px"></i>
                <span>Online Requests</span>
                <i data-lucide="chevron-down" class="dropdown-chevron" style="width:16px;height:16px;margin-left:auto;"></i>
            </a>
            <ul class="sidebar-dropdown-menu">
                <li><a href="/admin/social-case/online-requests"><i data-lucide="clock" style="width:18px;height:18px"></i><span>Pending Requests</span><span class="badge-count badge-pending" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#F59E0B;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['pending'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/accepted"><i data-lucide="check-circle" style="width:18px;height:18px"></i><span>Accepted Requests</span><span class="badge-count badge-accepted" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#10B981;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['accepted'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/rejected" class="active"><i data-lucide="x-circle" style="width:18px;height:18px"></i><span>Rejected Requests</span><span class="badge-count badge-rejected" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#EF4444;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['rejected'] ?? 0 }}</span></a></li>
            </ul>
        </li>
        @endif
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <header class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none mb-4 lg:mb-2">
        <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Rejected Online Requests</h1>
    </header>
    <div style="margin-bottom:10px;">
        <p class="text-sm text-slate-500 m-0">View and manage rejected online service requests from the public.</p>
    </div>

    @php
    $barangaysList = [
      "Acacia","Adlas","Anahaw I","Anahaw II","Balite I","Balite II","Balubad","Banaba","Batas",
      "Biga I","Biga II","Biluso","Bucal","Buho","Bulihan","Cabangaan","Carmen","Hoyo","Hukay","Iba",
      "Inchican","Ipil I","Ipil II","Kalubkob","Kaong","Lalaan I","Lalaan II","Litlit","Lucsuhin","Lumil",
      "Maguyam","Malabag","Malaking Tatyao","Mataas na Burol","Munting Ilog","Narra I","Narra II","Narra III",
      "Paligawan","Pasong Langka","Barangay I (Poblacion)","Barangay II (Poblacion)","Barangay III (Poblacion)",
      "Barangay IV (Poblacion)","Barangay V (Poblacion)","Pooc I","Pooc II","Pulong Bunga","Pulong Saging",
      "Puting Kahoy","Sabutan","San Miguel I","San Miguel II","San Vicente I","San Vicente II","Santol",
      "Tartaria","Tibig","Toledo","Tubuan I","Tubuan II","Tubuan III","Ulat","Yakal"
    ];
    $typesList = [
        "Medical Assistance",
        "Burial Assistance",
        "Educational Assistance",
        "Financial Assistance",
        "Food / Relief Assistance",
        "Livelihood Assistance",
        "Other"
    ];
    @endphp

    <!-- Search and Filter Bar -->
    <div class="online-filter-bar">
        <div class="filter-item filter-search">
            <label class="filter-label">Search</label>
            <div class="filter-search-wrap">
                <input type="text" id="onlineSearchInput" value="{{ request('search') }}" placeholder="Search name, contact, email..." oninput="updateClearButtonVisibility()" onkeydown="if(event.key==='Enter') applyOnlineFilters()">
                <button type="button" class="filter-search-btn" onclick="applyOnlineFilters()">
                    <i data-lucide="search" style="width:18px;height:18px"></i>
                </button>
            </div>
        </div>
        <div class="filter-item filter-dropdown" id="barangayDropdown">
            <label class="filter-label">Filter by Barangay</label>
            <div onclick="toggleBarangayMenu()" class="filter-select-btn {{ request('barangay') && request('barangay') !== 'All' ? 'active' : '' }}" id="barangayBtn">
                <span id="barangayLabel" class="filter-select-label">{{ request('barangay') && request('barangay') !== 'All' ? request('barangay') : 'All Barangays' }}</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="barangayMenu" class="filter-menu" style="display:none">
                <div class="brgy-opt {{ !request('barangay') || request('barangay') === 'All' ? 'selected' : '' }}" data-value="" onclick="selectBarangay(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Barangays</div>
                @foreach($barangaysList as $b)
                <div class="brgy-opt {{ request('barangay') === $b ? 'selected' : '' }}" data-value="{{ $b }}" onclick="selectBarangay(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">{{ $b }}</div>
                @endforeach
            </div>
        </div>
        <div class="filter-item filter-dropdown" id="typeDropdown">
            <label class="filter-label">Filter by Type</label>
            <div onclick="toggleTypeMenu()" class="filter-select-btn {{ request('type') && request('type') !== 'All' ? 'active' : '' }}" id="typeBtn">
                <span id="typeLabel" class="filter-select-label">{{ request('type') && request('type') !== 'All' ? request('type') : 'All Types' }}</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="typeMenu" class="filter-menu" style="display:none">
                <div class="type-opt {{ !request('type') || request('type') === 'All' ? 'selected' : '' }}" data-value="" onclick="selectType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Types</div>
                @foreach($typesList as $t)
                <div class="type-opt {{ request('type') === $t ? 'selected' : '' }}" data-value="{{ $t }}" onclick="selectType(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">{{ $t }}</div>
                @endforeach
            </div>
        </div>
        <div class="filter-item filter-reset">
            <label class="filter-label">&nbsp;</label>
            <button type="button" class="filter-reset-btn" onclick="resetOnlineFilters()">
                <i data-lucide="x" style="width:16px;height:16px"></i> Clear
            </button>
        </div>
    </div>

    <div class="online-requests-panel">
        <div class="online-requests-table-wrap">
            <table id="onlineRequestsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Service Type</th>
                        <th>Assistance Type</th>
                        <th>Barangay</th>
                        <th>Date Rejected</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rejectedRequests as $req)
                    <tr data-name="{{ $req->first_name }} {{ $req->last_name }}">
                        <td data-label="Name">
                            <div class="req-val-wrap">
                                <div style="font-weight: 600; color: #0F172A;">{{ $req->first_name }} {{ $req->last_name }}</div>
                                <div class="text-xs text-slate-500">{{ $req->email }}</div>
                            </div>
                        </td>
                        <td data-label="Contact"><span class="req-val-wrap">{{ $req->contact_number }}</span></td>
                        <td data-label="Service Type"><span class="req-val-wrap">{{ ucfirst(str_replace('_', ' ', $req->service_type)) }}</span></td>
                        <td data-label="Assistance Type"><span class="req-val-wrap">{{ ucfirst(str_replace('_', ' ', $req->assistance_type)) }}</span></td>
                        <td data-label="Barangay"><span class="req-val-wrap">{{ $req->barangay }}</span></td>
                        <td data-label="Date Rejected"><span class="req-val-wrap">{{ $req->updated_at->format('M d, Y') }}</span></td>
                        <td data-label="Action">
                            <div class="req-val-wrap">
                                <button class="btn btn-primary btn-sm" onclick="viewOnlineRequest({{ $req->id }})" title="View">
                                    <i data-lucide="eye" style="width:14px;height:14px"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7" class="empty-cell">
                            <div class="empty-state-content">
                                <div class="empty-icon-wrap">
                                    <i data-lucide="x-circle"></i>
                                </div>
                                <div class="empty-title">No rejected requests</div>
                                <div class="empty-subtitle">Rejected online service requests will appear here after decline.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="border-top: 2px solid #94A3B8; margin: 20px 0;"></div>

    <!-- Pagination -->
    <div class="sc-pagination">
        <div class="sc-pagination-info">
            @if($rejectedRequests->count() > 0)
                Showing {{ $rejectedRequests->firstItem() }} to {{ $rejectedRequests->lastItem() }} of {{ $rejectedRequests->total() }} requests
            @else
                Showing 0 of 0 requests
            @endif
        </div>
        <div class="sc-pagination-controls">
            @if($rejectedRequests->hasPages())
                @if($rejectedRequests->onFirstPage())
                    <span class="sc-page-btn" disabled>Previous</span>
                @else
                    <a href="{{ $rejectedRequests->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                @endif
                
                @foreach($rejectedRequests->getUrlRange(1, $rejectedRequests->lastPage()) as $page => $url)
                    @if($page == $rejectedRequests->currentPage())
                        <span class="sc-page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="sc-page-btn">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($rejectedRequests->hasMorePages())
                    @if($rejectedRequests->onLastPage())
                        <span class="sc-page-btn" disabled>Next</span>
                    @else
                        <a href="{{ $rejectedRequests->nextPageUrl() }}" class="sc-page-btn">Next</a>
                    @endif
                @endif
            @else
                <span class="sc-page-btn" disabled>Previous</span>
                <span class="sc-page-btn active">1</span>
                <span class="sc-page-btn" disabled>Next</span>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.onlineFilterState = {
    barangay: '{{ request('barangay') && request('barangay') !== 'All' ? request('barangay') : '' }}',
    type: '{{ request('type') && request('type') !== 'All' ? request('type') : '' }}'
};

function toggleBarangayMenu() {
    const menu = document.getElementById('barangayMenu');
    const typeMenu = document.getElementById('typeMenu');
    if (typeMenu) typeMenu.style.display = 'none';
    if (menu) {
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
}

function toggleTypeMenu() {
    const menu = document.getElementById('typeMenu');
    const brgyMenu = document.getElementById('barangayMenu');
    if (brgyMenu) brgyMenu.style.display = 'none';
    if (menu) {
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
}

function selectBarangay(el) {
    const val = el.getAttribute('data-value');
    window.onlineFilterState.barangay = val;
    applyOnlineFilters();
    updateClearButtonVisibility();
}

function selectType(el) {
    const val = el.getAttribute('data-value');
    window.onlineFilterState.type = val;
    applyOnlineFilters();
    updateClearButtonVisibility();
}

function applyOnlineFilters() {
    const search = document.getElementById('onlineSearchInput') ? document.getElementById('onlineSearchInput').value.trim() : '';
    const barangay = window.onlineFilterState ? window.onlineFilterState.barangay : '';
    const type = window.onlineFilterState ? window.onlineFilterState.type : '';

    const params = new URLSearchParams(window.location.search);
    if (search) params.set('search', search); else params.delete('search');
    if (barangay && barangay !== 'All') params.set('barangay', barangay); else params.delete('barangay');
    if (type && type !== 'All') params.set('type', type); else params.delete('type');
    params.delete('page');

    window.location.href = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
}

function resetOnlineFilters() {
    window.location.href = window.location.pathname;
}

function updateClearButtonVisibility() {
    var searchValue = document.getElementById('onlineSearchInput') ? document.getElementById('onlineSearchInput').value.trim() : '';
    var barangayValue = window.onlineFilterState && window.onlineFilterState.barangay && window.onlineFilterState.barangay !== 'All';
    var typeValue = window.onlineFilterState && window.onlineFilterState.type && window.onlineFilterState.type !== 'All';
    var clearBtn = document.querySelector('.filter-reset-btn');
    if (clearBtn) {
        if (searchValue || barangayValue || typeValue) {
            clearBtn.classList.add('visible');
        } else {
            clearBtn.classList.remove('visible');
        }
    }
}

document.addEventListener('click', function(e) {
    const brgyDropdown = document.getElementById('barangayDropdown');
    const typeDropdown = document.getElementById('typeDropdown');
    const brgyMenu = document.getElementById('barangayMenu');
    const typeMenu = document.getElementById('typeMenu');

    if (brgyDropdown && !brgyDropdown.contains(e.target) && brgyMenu) {
        brgyMenu.style.display = 'none';
    }
    if (typeDropdown && !typeDropdown.contains(e.target) && typeMenu) {
        typeMenu.style.display = 'none';
    }
});

function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle('open');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    updateClearButtonVisibility();
});

function viewOnlineRequest(id) {
    // Fetch the online request details and show in a modal
    fetch(`/admin/social-case/online-requests/${id}`)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: '<div style="display: flex; align-items: center; gap: 10px;"><i data-lucide="file-text" style="width: 24px; height: 24px; color: #1A237E;"></i><span>Online Request Details</span></div>',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Personal Information</h4>
                            <div class="modal-grid-2">
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Full Name</span>
                                    <span style="font-size: 14px; color: #1F2937; font-weight: 500;">${data.first_name} ${data.last_name}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Email</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.email}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Contact Number</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.contact_number}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Barangay</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.barangay}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Service Information</h4>
                            <div class="modal-grid-2">
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Service Type</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.service_type}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Assistance Type</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.assistance_type}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Request Details</h4>
                            <div class="modal-grid-2">
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Status</span>
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #FEE2E2; color: #DC2626;">${data.status}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Date Submitted</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.created_at}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Additional Information</h4>
                            <div style="margin-bottom: 12px;">
                                <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Situation</span>
                                <p style="margin: 0; font-size: 14px; color: #1F2937; line-height: 1.5;">${data.situation}</p>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Reason for Decline</span>
                                <p style="margin: 0; font-size: 14px; color: #DC2626; line-height: 1.5; font-weight: 500;">${data.notes}</p>
                            </div>
                            ${data.attachments_html}
                        </div>
                    </div>
                `,
                icon: false,
                confirmButtonText: 'Close',
                confirmButtonColor: '#1A237E',
                width: '600px',
                didOpen: () => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Failed to load request details',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}
</script>
@endpush
