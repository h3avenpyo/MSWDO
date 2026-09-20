@extends('admin.layout')
@section('title', 'MSWDO – Officers Directory')
@section('page_title', 'Officers Directory')

@section('content')
@php
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', trim($adminName));
$initials = count($words) >= 2
    ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
    : strtoupper(substr($adminName,0,2));
@endphp

<style>
    /* ── Dashboard Header Banner ── */
    .dash-banner {
        background: linear-gradient(135deg, #1A237E 0%, #1E3A8A 55%, #1e40af 100%);
        border-radius: 18px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 10px 25px -5px rgba(26, 35, 126, 0.25);
        position: relative;
        overflow: hidden;
        color: #FFFFFF;
    }
    .dash-banner::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(251, 192, 45, 0.2) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }
    .dash-banner-title {
        font-family: 'Public Sans', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
        letter-spacing: -0.02em;
    }
    .dash-banner-sub {
        font-size: 0.925rem;
        color: rgba(255, 255, 255, 0.85);
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .dash-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.825rem;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .dash-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ADE80;
        box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.35);
        animation: pulse-dot 2s infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(0.85); }
    }

    /* ── Top Metric Strip ── */
    .officer-kpi-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }
    .officer-kpi-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 1.15rem 1.35rem;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .officer-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -2px rgba(15, 23, 42, 0.08);
    }
    .kpi-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kpi-icon-wrap svg { width: 24px; height: 24px; }
    .kpi-card-indigo .kpi-icon-wrap { background: #EEF2FF; color: #4338CA; }
    .kpi-card-sky .kpi-icon-wrap { background: #E0F2FE; color: #0284C7; }
    .kpi-card-amber .kpi-icon-wrap { background: #FEF3C7; color: #D97706; }
    .kpi-card-emerald .kpi-icon-wrap { background: #ECFDF5; color: #059669; }

    /* ── Directory Shell ── */
    .directory-shell {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    /* ── Toolbar Rail & Filters ── */
    .toolbar-rail {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .filter-rail {
        display: flex;
        align-items: flex-end;
        gap: 0.75rem;
        flex-wrap: wrap;
        flex: 1;
    }
    #showingCounter {
        font-size: 0.75rem;
        color: #64748B;
        font-weight: 500;
        white-space: nowrap;
        margin-bottom: 10px;
    }
    .filter-item {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .filter-item.filter-search {
        min-width: 240px;
        max-width: 320px;
        flex: 1;
    }
    .filter-label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748B;
        line-height: 1;
    }
    .filter-search-wrap {
        display: flex;
        align-items: stretch;
        width: 100%;
        height: 40px;
        border-radius: 8px;
        box-sizing: border-box;
    }
    .filter-search-wrap .form-control {
        flex: 1;
        width: 0;
        min-width: 0;
        height: 40px;
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-right: none;
        border-radius: 8px 0 0 8px;
        padding: 0 14px;
        font-size: 0.875rem;
        color: #1E293B;
        outline: none;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }
    .filter-search-wrap .form-control:focus {
        background: #FFFFFF;
        border-color: #1A237E;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.12);
    }
    .filter-search-btn {
        height: 40px !important;
        padding: 0 16px;
        border: 1px solid #1E3A8A;
        border-radius: 0 8px 8px 0;
        background: #1E3A8A;
        color: #FFFFFF;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s ease;
        flex-shrink: 0;
        box-sizing: border-box !important;
        margin: 0 !important;
    }
    .filter-search-btn:hover {
        background: #1E40AF;
    }
    .filter-search-btn svg {
        width: 16px;
        height: 16px;
    }

    /* Role Dropdown */
    .filter-dropdown {
        position: relative;
        min-width: 180px;
        max-width: 220px;
        flex: 0 1 200px;
    }
    .filter-select-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 14px;
        height: 40px;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        font-size: 0.875rem;
        cursor: pointer;
        background: #F8FAFC;
        transition: all 0.15s ease;
        box-sizing: border-box;
        width: 100%;
        user-select: none;
    }
    .filter-select-btn:hover {
        border-color: #1E3A8A;
        background: #FFFFFF;
    }
    .filter-select-btn.active {
        border-color: #1E3A8A;
        background: #EFF6FF;
    }
    .filter-select-label {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #374151;
        font-weight: 500;
        font-size: 0.85rem;
    }
    .filter-menu {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        z-index: 50;
        max-height: 260px;
        overflow-y: auto;
        padding: 6px;
    }
    .role-opt, .status-opt {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: background 0.15s ease;
        color: #334155;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .role-opt:hover, .status-opt:hover {
        background: #EFF6FF;
        color: #1E3A8A;
    }
    .role-opt.selected, .status-opt.selected {
        background: #EFF6FF;
        color: #1E3A8A;
        font-weight: 600;
    }

    /* Clear Reset Button */
    .clear-filters-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        height: 40px;
        padding: 0 14px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #DC2626;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
        box-sizing: border-box;
    }
    .clear-filters-btn:hover {
        background: #FEE2E2;
        border-color: #F87171;
    }



    /* ── Desktop & Tablet Table ── */
    .table-responsive-container {
        width: 100%;
        max-width: 100%;
        overflow: hidden !important;
        overflow-x: hidden !important;
        overflow-y: hidden !important;
        border-radius: 14px;
        border: 1px solid #EDF2F7;
        background: #FFFFFF;
    }
    .officers-tbl {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
    }
    .officers-tbl th {
        background: #F8FAFC;
        color: #475569;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.85rem 1rem;
        text-align: left;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .officers-tbl td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid #F1F5F9;
        color: #1E293B;
        vertical-align: middle;
        background: #FFFFFF;
        overflow: hidden;
    }
    .officers-tbl tbody tr:last-child td { border-bottom: none; }
    .officers-tbl tbody tr:hover td { background: #F8FAFC; }

    /* Officer Profile Cell */
    .officer-profile-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .officer-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.825rem;
        flex-shrink: 0;
    }
    .avatar-admin { background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; }
    .avatar-social { background: #E0F2FE; color: #0284C7; border: 1px solid #BAE6FD; }
    .avatar-senior { background: #FEF3C7; color: #D97706; border: 1px solid #FDE68A; }
    .avatar-financial { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .avatar-staff { background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0; }

    /* Role Badges */
    .role-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 8px;
        font-size: 0.775rem;
        font-weight: 600;
        background: #F1F5F9;
        color: #334155;
    }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
    }
    .status-pill-active { background: #ECFDF5; color: #047857; }
    .status-pill-inactive { background: #FEF2F2; color: #B91C1C; }

    /* Action Buttons */
    .action-rail {
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }
    .btn-action-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .btn-action-icon svg { width: 16px; height: 16px; }
    .btn-action-icon.act-edit {
        color: #1A237E;
        background: #EEF2FF;
        border-color: #C7D2FE;
    }
    .btn-action-icon.act-edit:hover {
        background: #1A237E;
        color: #FFFFFF;
    }
    .btn-action-icon.act-deactivate {
        color: #DC2626;
        background: #FEF2F2;
        border-color: #FECACA;
    }
    .btn-action-icon.act-deactivate:hover {
        background: #DC2626;
        color: #FFFFFF;
    }
    .btn-action-icon.act-activate {
        color: #059669;
        background: #ECFDF5;
        border-color: #A7F3D0;
    }
    .btn-action-icon.act-activate:hover {
        background: #059669;
        color: #FFFFFF;
    }

    @media (max-width: 1199.98px) {
        .officers-tbl th,
        .officers-tbl td {
            padding: 0.75rem 0.65rem;
            font-size: 0.8125rem;
        }
        .officers-tbl th {
            font-size: 0.7rem;
            letter-spacing: 0.02em;
        }
        .btn-action-icon {
            width: 30px;
            height: 30px;
        }
        .btn-action-icon svg {
            width: 14px;
            height: 14px;
        }
        .role-tag {
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
        }
        .status-pill {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }
    }

    /* ── Mobile Layout & Responsiveness (< 768px) ── */
    .mobile-officers-list { display: none; }

    @media (max-width: 767.98px) {
        .dash-banner {
            padding: 1.15rem 1.25rem;
            border-radius: 14px;
            margin-bottom: 0.85rem;
        }
        .dash-banner-title {
            font-size: 1.35rem;
        }
        .dash-banner-sub {
            font-size: 0.8rem;
        }

        /* Compact KPI Strip on Mobile */
        .officer-kpi-strip {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.65rem;
            margin-bottom: 0.95rem;
        }
        .officer-kpi-card {
            padding: 0.85rem 0.95rem;
            gap: 0.65rem;
            border-radius: 14px;
            min-width: 0;
            overflow: hidden;
        }
        .kpi-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 10px;
        }
        .kpi-icon-wrap svg {
            width: 18px;
            height: 18px;
        }
        .officer-kpi-card .text-2xl {
            font-size: 1.35rem;
            line-height: 1.2;
        }
        .officer-kpi-card .text-xs {
            font-size: 0.7rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .officer-kpi-card .text-\[11px\] {
            font-size: 0.65rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .directory-shell {
            padding: 1rem 0.85rem;
            border-radius: 14px;
            margin-bottom: 1.25rem;
        }

        /* Toolbar & Filters on Mobile */
        .toolbar-rail {
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start !important;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .filter-rail {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            gap: 0.65rem;
            flex: none !important;
        }
        .filter-item {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            flex: none !important;
        }
        .filter-item.filter-search {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            flex: none !important;
        }
        .filter-dropdown {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            flex: none !important;
            height: auto !important;
        }
        .filter-search-wrap {
            width: 100%;
        }
        .filter-search-wrap .form-control {
            font-size: 16px !important;
        }
        .filter-select-btn {
            width: 100%;
        }
        .filter-menu {
            z-index: 100;
            max-height: 260px;
            -webkit-overflow-scrolling: touch;
        }
        .clear-filters-btn {
            width: 100%;
            justify-content: center;
            height: 40px;
            flex: none !important;
        }
        #showingCounter {
            width: 100%;
            font-size: 0.75rem;
            text-align: left;
            margin: 0.15rem 0 0.35rem 0 !important;
            align-self: flex-start;
            color: #64748B;
        }

        /* Table hidden, stacked cards shown */
        .table-responsive-container {
            display: none !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .mobile-officers-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
            min-width: 0;
        }
        .mobile-officer-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            min-width: 0;
            width: 100%;
            overflow: hidden;
            box-sizing: border-box;
        }
        .mobile-officer-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 0;
            gap: 0.5rem;
            width: 100%;
        }
        .mobile-officer-body {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: #64748B;
            padding: 0.5rem 0;
            border-top: 1px dashed #F1F5F9;
            border-bottom: 1px dashed #F1F5F9;
            min-width: 0;
            width: 100%;
        }
        .mobile-officer-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            min-width: 0;
        }

        /* Pagination on Mobile */
        .pagination-footer {
            flex-direction: column;
            align-items: center;
            gap: 0.85rem;
            text-align: center;
        }
        .pagination-info {
            justify-content: center;
            text-align: center;
            font-size: 0.8rem;
        }
        .sc-pagination-controls {
            justify-content: center;
            width: 100%;
        }
    }

    @media (max-width: 420px) {
        .dash-banner {
            padding: 1rem;
        }
        .officer-kpi-strip {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        .directory-shell {
            padding: 0.85rem 0.65rem;
        }
        .mobile-officer-card {
            padding: 0.85rem;
        }
    }

    /* ── Pagination Styling ── */
    .pagination-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid #EDF2F7;
    }
    .pagination-info {
        font-size: 0.85rem;
        color: #64748B;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex-wrap: wrap;
    }
    .sc-pagination-controls {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .sc-page-btn {
        height: 38px;
        min-width: 38px;
        padding: 0 14px;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        background: #FFFFFF;
        color: #334155;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: all 0.2s ease;
        user-select: none;
    }
    .sc-page-btn svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }
    .sc-page-btn:hover:not(:disabled):not(.active) {
        background: #EEF2FF;
        border-color: #A5B4FC;
        color: #1A237E;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(26, 35, 126, 0.12);
    }
    .sc-page-btn.active {
        background: #1A237E;
        color: #FFFFFF;
        border-color: #1A237E;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(26, 35, 126, 0.25);
        cursor: default;
    }
    .sc-page-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: #F8FAFC;
        border-color: #E2E8F0;
        color: #94A3B8;
        box-shadow: none;
        transform: none;
    }
    @media (max-width: 639.98px) {
        .pagination-footer {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.75rem;
        }
        .sc-pagination-controls {
            width: 100%;
            justify-content: center;
        }
    }
</style>

{{-- Page Header Banner --}}
<header class="dash-banner flex flex-col md:flex-row md:items-center justify-between gap-4 select-none">
    <div>
        <div class="dash-banner-title">Officers Directory</div>
        <div class="dash-banner-sub">
            <span>MSWDO Silang — Staff &amp; Account Management</span>
            <span class="opacity-40">•</span>
            <span class="dash-live-badge">
                <span class="dash-pulse-dot"></span>
                <span>System Online</span>
            </span>
        </div>
    </div>
    <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto">
        <div class="text-left md:text-right">
            <div class="text-[10px] md:text-xs font-semibold uppercase tracking-wider text-white/70">Philippine Standard Time</div>
            <div class="text-xs md:text-sm font-semibold text-white tracking-wide" id="liveClock">Loading date...</div>
        </div>
        <div class="w-10 h-10 md:w-11 md:h-11 rounded-full bg-white/20 border-2 border-white/40 text-white font-bold text-sm flex items-center justify-center shadow-inner flex-shrink-0 cursor-default" title="Logged in as: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

{{-- Top KPI Strip --}}
<section class="officer-kpi-strip">
    <div class="officer-kpi-card kpi-card-indigo">
        <div class="kpi-icon-wrap">
            <i data-lucide="users"></i>
        </div>
        <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Officers</div>
            <div class="text-2xl font-bold text-slate-900 leading-tight">{{ number_format($officerStats['total'] ?? $officers->total()) }}</div>
            <div class="text-[11px] text-slate-400">Registered System Accounts</div>
        </div>
    </div>

    <div class="officer-kpi-card kpi-card-sky">
        <div class="kpi-icon-wrap">
            <i data-lucide="shield"></i>
        </div>
        <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Administrators</div>
            <div class="text-2xl font-bold text-slate-900 leading-tight">{{ number_format($officerStats['admins'] ?? 0) }}</div>
            <div class="text-[11px] text-slate-400">Full Access Privileges</div>
        </div>
    </div>

    <div class="officer-kpi-card kpi-card-amber">
        <div class="kpi-icon-wrap">
            <i data-lucide="folder-heart"></i>
        </div>
        <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Social Workers</div>
            <div class="text-2xl font-bold text-slate-900 leading-tight">{{ number_format($officerStats['socialWorkers'] ?? 0) }}</div>
            <div class="text-[11px] text-slate-400">Checkers &amp; Case Encoders</div>
        </div>
    </div>

    <div class="officer-kpi-card kpi-card-emerald">
        <div class="kpi-icon-wrap">
            <i data-lucide="check-circle-2"></i>
        </div>
        <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Status</div>
            <div class="text-2xl font-bold text-slate-900 leading-tight">{{ number_format($officerStats['active'] ?? 0) }}</div>
            <div class="text-[11px] text-slate-400">{{ number_format($officerStats['inactive'] ?? 0) }} currently inactive</div>
        </div>
    </div>
</section>

{{-- Alerts --}}
@if(session('success'))
    <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm" id="successAlert">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- Main Directory Container --}}
<div class="directory-shell">
    {{-- Search & Filter Toolbar --}}
    <div class="toolbar-rail">
        <div class="filter-rail">
            <div class="filter-item filter-search">
                <label class="filter-label">Search</label>
                <div class="filter-search-wrap">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, role..." value="{{ request()->get('search', '') }}" oninput="onSearchInput()" onkeydown="if(event.key==='Enter'){event.preventDefault();handleSearch();}">
                    <button type="button" class="filter-search-btn" onclick="handleSearch()" title="Search">
                        <i data-lucide="search"></i>
                    </button>
                </div>
            </div>

            @php $curRole = request()->get('role', 'All Roles'); @endphp
            <div class="filter-item filter-dropdown" id="roleDropdown">
                <label class="filter-label">Filter by Role</label>
                <div onclick="toggleRoleMenu(event)" class="filter-select-btn {{ ($curRole && $curRole !== 'All Roles') ? 'active' : '' }}" id="roleBtn">
                    <span id="roleLabel" class="filter-select-label">{{ $curRole ?: 'All Roles' }}</span>
                    <i data-lucide="chevron-down" style="width:16px;height:16px;color:#64748B;flex-shrink:0;transition:transform 0.2s;"></i>
                </div>
                <div id="roleMenu" class="filter-menu" style="display:none">
                    @php
                        $roles = [
                            'All Roles',
                            'Administrator',
                            'Social Case Worker (Encoder)',
                            'Social Case Worker (Checker)',
                            'Senior Citizen Officer',
                            'Financial Assistance Step 1',
                            'Financial Assistance Step 2',
                        ];
                    @endphp
                    @foreach($roles as $r)
                        <div class="role-opt {{ $curRole === $r ? 'selected' : '' }}" data-value="{{ $r }}" onclick="selectRole('{{ $r }}')">
                            <span>{{ $r }}</span>
                            @if($curRole === $r)
                                <i data-lucide="check" style="width:14px;height:14px;color:#1E3A8A;"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="button" id="clearFiltersBtn" class="clear-filters-btn" onclick="clearFilters()" style="display:none;">
                <i data-lucide="x" style="width:14px;height:14px;"></i>
                <span>Reset</span>
            </button>
        </div>

        <div class="text-xs text-slate-500 font-medium" id="showingCounter">
            @if($officers->total() > 0)
                Showing <span class="font-bold text-slate-800">{{ $officers->firstItem() ?? 0 }}–{{ $officers->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800">{{ $officers->total() }}</span> officers
            @else
                Showing <span class="font-bold text-slate-800">0</span> officers
            @endif
        </div>
    </div>

    {{-- Desktop & Tablet Table --}}
    <div class="table-responsive-container">
        <table class="officers-tbl">
            <thead>
                <tr>
                    <th style="width: 27%;">Officer</th>
                    <th style="width: 28%;">Email Address</th>
                    <th style="width: 21%;">Assigned Role</th>
                    <th style="width: 12%;">Account Status</th>
                    <th style="width: 12%; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="officersTableBody">
                @forelse($officers as $officer)
                @php
                    $name = $officer->name ?? 'Officer';
                    $words = explode(' ', trim($name));
                    $init = count($words) >= 2 
                        ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
                        : strtoupper(substr($name,0,2));

                    // Determine Role Label & Avatar Style
                    $roleKey = is_object($officer->role) ? $officer->role->value : strtolower((string)$officer->role);
                    $avatarClass = 'avatar-staff';
                    $roleLabel = ucfirst(str_replace('_', ' ', $roleKey));

                    if ($roleKey === 'admin') {
                        $avatarClass = 'avatar-admin';
                        $roleLabel = 'Administrator';
                    } elseif (in_array($roleKey, ['social_worker', 'eligibility_checker'])) {
                        $avatarClass = 'avatar-social';
                        $roleLabel = $roleKey === 'social_worker' ? 'Social Worker (Encoder)' : 'Social Worker (Checker)';
                    } elseif (str_contains($roleKey, 'senior')) {
                        $avatarClass = 'avatar-senior';
                        $roleLabel = 'Senior Citizen Officer';
                    } elseif (str_contains($roleKey, 'financial')) {
                        $avatarClass = 'avatar-financial';
                        $roleLabel = $roleKey === 'financialstep1' ? 'Financial Asst. Step 1' : ($roleKey === 'financialstep2' ? 'Financial Asst. Step 2' : 'Financial Officer');
                    }

                    $statusVal = is_object($officer->status) ? $officer->status->value : $officer->status;
                    $isActive = ($statusVal === 'active' || empty($statusVal));
                @endphp
                <tr>
                    <td class="overflow-hidden">
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 truncate" title="{{ $name }}">{{ $name }}</div>
                            <div class="text-[11px] text-slate-400 truncate">Enrolled {{ $officer->created_at ? $officer->created_at->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    </td>
                    <td class="overflow-hidden">
                        <div class="flex items-center gap-1.5 text-slate-600 text-sm min-w-0">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                            <span class="truncate" title="{{ $officer->email ?? '-' }}">{{ $officer->email ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="overflow-hidden">
                        <span class="role-tag truncate max-w-full" title="{{ $roleLabel }}">{{ $roleLabel }}</span>
                    </td>
                    <td style="white-space: nowrap;">
                        @if($isActive)
                            <span class="status-pill status-pill-active">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Active</span>
                            </span>
                        @else
                            <span class="status-pill status-pill-inactive">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                <span>Inactive</span>
                            </span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space: nowrap;">
                        <div class="action-rail justify-end">
                            <a href="{{ route('admin.officers.edit', $officer->id) }}" class="btn-action-icon act-edit" title="Edit Officer Details">
                                <i data-lucide="pencil"></i>
                            </a>
                            @if($isActive)
                                <button type="button" class="btn-action-icon act-deactivate" title="Deactivate Officer Account" onclick="deactivateOfficer({{ $officer->id }}, '{{ addslashes($officer->name) }}')">
                                    <i data-lucide="user-x"></i>
                                </button>
                            @else
                                <button type="button" class="btn-action-icon act-activate" title="Activate Officer Account" onclick="activateOfficer({{ $officer->id }}, '{{ addslashes($officer->name) }}')">
                                    <i data-lucide="user-check"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-slate-400">
                        <i data-lucide="users" class="w-9 h-9 mx-auto mb-2 text-slate-300"></i>
                        <div class="font-semibold text-slate-600">No officers found matching your criteria.</div>
                        <div class="text-xs text-slate-400 mt-1">Try adjusting the search query or role filter.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Stacked Cards (< 768px) --}}
    <div class="mobile-officers-list" id="mobileOfficersList">
        @forelse($officers as $officer)
        @php
            $name = $officer->name ?? 'Officer';
            $words = explode(' ', trim($name));
            $init = count($words) >= 2 
                ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
                : strtoupper(substr($name,0,2));

            $roleKey = is_object($officer->role) ? $officer->role->value : strtolower((string)$officer->role);
            $avatarClass = 'avatar-staff';
            $roleLabel = ucfirst(str_replace('_', ' ', $roleKey));
            if ($roleKey === 'admin') { $avatarClass = 'avatar-admin'; $roleLabel = 'Administrator'; }
            elseif (in_array($roleKey, ['social_worker', 'eligibility_checker'])) { $avatarClass = 'avatar-social'; $roleLabel = 'Social Worker'; }
            elseif (str_contains($roleKey, 'senior')) { $avatarClass = 'avatar-senior'; $roleLabel = 'Senior Officer'; }
            elseif (str_contains($roleKey, 'financial')) { $avatarClass = 'avatar-financial'; $roleLabel = 'Financial Officer'; }

            $statusVal = is_object($officer->status) ? $officer->status->value : $officer->status;
            $isActive = ($statusVal === 'active' || empty($statusVal));
        @endphp
        <div class="mobile-officer-card">
            <div class="mobile-officer-head">
                <div class="flex items-center gap-2.5 min-w-0 flex-1 mr-2">
                    <div class="officer-avatar {{ $avatarClass }} flex-shrink-0" style="width:36px;height:36px;font-size:0.75rem;">{{ $init }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-slate-900 text-sm truncate leading-snug">{{ $name }}</div>
                        <span class="role-tag text-[10px] py-0.5 px-2 mt-0.5">{{ $roleLabel }}</span>
                    </div>
                </div>
                @if($isActive)
                    <span class="status-pill status-pill-active text-[11px] flex-shrink-0">Active</span>
                @else
                    <span class="status-pill status-pill-inactive text-[11px] flex-shrink-0">Inactive</span>
                @endif
            </div>

            <div class="mobile-officer-body">
                <div class="flex items-center gap-2 min-w-0">
                    <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0"></i>
                    <span class="truncate">{{ $officer->email ?? '-' }}</span>
                </div>
                <div class="flex items-center gap-2 min-w-0">
                    <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0"></i>
                    <span class="truncate">{{ $officer->phone ?? 'No phone' }}</span>
                </div>
            </div>

            <div class="mobile-officer-actions">
                <span class="text-[11px] text-slate-400">{{ $officer->created_at ? $officer->created_at->format('M d, Y') : '' }}</span>
                <div class="action-rail">
                    <a href="{{ route('admin.officers.edit', $officer->id) }}" class="btn-action-icon act-edit" title="Edit Officer">
                        <i data-lucide="pencil"></i>
                    </a>
                    @if($isActive)
                        <button type="button" class="btn-action-icon act-deactivate" title="Deactivate Officer" onclick="deactivateOfficer({{ $officer->id }}, '{{ addslashes($officer->name) }}')">
                            <i data-lucide="user-x"></i>
                        </button>
                    @else
                        <button type="button" class="btn-action-icon act-activate" title="Activate Officer" onclick="activateOfficer({{ $officer->id }}, '{{ addslashes($officer->name) }}')">
                            <i data-lucide="user-check"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-slate-400 text-sm">
            No officers found matching your criteria.
        </div>
        @endforelse
    </div>

    {{-- Pagination Footer --}}
    <div class="pagination-footer" id="paginationFooter">
        <div class="pagination-info">
            @if($officers->total() > 0)
                <span>Showing</span>
                <span class="font-bold text-slate-800">{{ $officers->firstItem() ?? 0 }}–{{ $officers->lastItem() ?? 0 }}</span>
                <span>of</span>
                <span class="font-bold text-slate-800">{{ $officers->total() }}</span>
                <span>officers</span>
                <span class="text-slate-300 mx-1.5">•</span>
                <span>Page <strong class="text-slate-800">{{ $officers->currentPage() }}</strong> of <strong class="text-slate-800">{{ $officers->lastPage() }}</strong></span>
            @else
                <span>Showing</span>
                <span class="font-bold text-slate-800">0</span>
                <span>officers</span>
            @endif
        </div>
        <div>
            {{ $officers->appends(request()->only(['search', 'role', 'status', 'per_page']))->links('vendor.pagination.custom-simple') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') lucide.createIcons();
    updateClearButtonVisibility();
    attachPaginationListeners();

    // Live Clock with Philippine formatting
    function updateLiveClock() {
        const now = new Date();
        const opts = { 
            weekday: 'short', 
            month: 'short', 
            day: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit', 
            second: '2-digit', 
            hour12: true 
        };
        const el = document.getElementById('liveClock');
        if (el) el.textContent = now.toLocaleDateString('en-US', opts);
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);
});

function updateClearButtonVisibility() {
    const searchInput = document.getElementById('searchInput');
    const hasSearch = searchInput && searchInput.value.trim().length > 0;
    const url = new URL(window.location.href);
    const hasRole = url.searchParams.has('role') && url.searchParams.get('role') !== 'All Roles';
    const clearBtn = document.getElementById('clearFiltersBtn');
    if (clearBtn) {
        clearBtn.style.display = (hasSearch || hasRole) ? 'inline-flex' : 'none';
    }
}

let searchDebounceTimer = null;
let currentSearchAbortController = null;

function onSearchInput() {
    updateClearButtonVisibility();
    const q = document.getElementById('searchInput').value.trim();

    // 1. Instant client-side row filter on current visible rows (0ms feedback)
    filterTableRows(q);

    // 2. Debounced asynchronous server search (250ms) to query database without full page reload
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(function() {
        performLiveSearch(1);
    }, 250);
}

function filterTableRows(query) {
    const q = query.toLowerCase();
    const rows = document.querySelectorAll('#officersTableBody tr, #mobileOfficersList .mobile-officer-card');
    rows.forEach(function(row) {
        if (row.querySelector('td[colspan]')) return;
        const text = row.textContent.toLowerCase();
        row.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
}

function performLiveSearch(page = 1) {
    if (currentSearchAbortController) {
        currentSearchAbortController.abort();
    }
    currentSearchAbortController = new AbortController();

    const searchInput = document.getElementById('searchInput');
    const query = searchInput ? searchInput.value.trim() : '';
    const url = new URL(window.location.href);

    if (query) {
        url.searchParams.set('search', query);
    } else {
        url.searchParams.delete('search');
    }
    if (page) {
        url.searchParams.set('page', page);
    }

    // Update browser URL without reloading
    window.history.replaceState({}, '', url.toString());

    const tableContainer = document.querySelector('.table-responsive-container');
    const mobileContainer = document.getElementById('mobileOfficersList');
    if (tableContainer) tableContainer.style.opacity = '0.65';
    if (mobileContainer) mobileContainer.style.opacity = '0.65';

    fetch(url.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        signal: currentSearchAbortController.signal
    })
    .then(response => response.text())
    .then(html => {
        if (tableContainer) tableContainer.style.opacity = '1';
        if (mobileContainer) mobileContainer.style.opacity = '1';
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Update Desktop Table Body
        const newTableBody = doc.getElementById('officersTableBody');
        const oldTableBody = document.getElementById('officersTableBody');
        if (newTableBody && oldTableBody) {
            oldTableBody.innerHTML = newTableBody.innerHTML;
        }

        // Update Mobile List
        const newMobileList = doc.getElementById('mobileOfficersList');
        const oldMobileList = document.getElementById('mobileOfficersList');
        if (newMobileList && oldMobileList) {
            oldMobileList.innerHTML = newMobileList.innerHTML;
        }

        // Update Showing Counter
        const newCounter = doc.getElementById('showingCounter');
        const oldCounter = document.getElementById('showingCounter');
        if (newCounter && oldCounter) {
            oldCounter.innerHTML = newCounter.innerHTML;
        }

        // Update Pagination Footer
        const newPagination = doc.getElementById('paginationFooter');
        const oldPagination = document.getElementById('paginationFooter');
        if (newPagination && oldPagination) {
            oldPagination.innerHTML = newPagination.innerHTML;
            attachPaginationListeners();
        }

        // Re-initialize icons
        if (typeof lucide !== 'undefined') lucide.createIcons();
    })
    .catch(err => {
        if (tableContainer) tableContainer.style.opacity = '1';
        if (mobileContainer) mobileContainer.style.opacity = '1';
        if (err.name !== 'AbortError') {
            console.error('Search request failed:', err);
        }
    });
}

function handleSearch() {
    clearTimeout(searchDebounceTimer);
    performLiveSearch(1);
}

function toggleRoleMenu(e) {
    if (e) e.stopPropagation();
    const menu = document.getElementById('roleMenu');
    const arrow = document.querySelector('#roleBtn [data-lucide="chevron-down"]');
    if (!menu) return;
    const isHidden = (menu.style.display === 'none' || !menu.style.display);
    menu.style.display = isHidden ? 'block' : 'none';
    if (arrow) arrow.style.transform = isHidden ? 'rotate(180deg)' : '';
}

function selectRole(role) {
    clearTimeout(searchDebounceTimer);
    const url = new URL(window.location.href);
    if (role && role !== 'All Roles') {
        url.searchParams.set('role', role);
    } else {
        url.searchParams.delete('role');
    }
    url.searchParams.set('page', '1');
    window.history.replaceState({}, '', url.toString());

    // Update label & button state
    const label = document.getElementById('roleLabel');
    if (label) label.textContent = role;
    const btn = document.getElementById('roleBtn');
    if (btn) btn.classList.toggle('active', role !== 'All Roles');

    // Update options selection
    const opts = document.querySelectorAll('#roleMenu .role-opt');
    opts.forEach(opt => {
        const val = opt.getAttribute('data-value');
        opt.classList.toggle('selected', val === role);
        const check = opt.querySelector('svg');
        if (check) check.style.display = (val === role) ? '' : 'none';
    });

    // Close menu
    const menu = document.getElementById('roleMenu');
    if (menu) menu.style.display = 'none';
    const arrow = document.querySelector('#roleBtn [data-lucide="chevron-down"]');
    if (arrow) arrow.style.transform = '';

    updateClearButtonVisibility();
    performLiveSearch(1);
}

// Close role dropdown when clicking outside
document.addEventListener('click', function(e) {
    const roleDD = document.getElementById('roleDropdown');
    if (roleDD && !roleDD.contains(e.target)) {
        const menu = document.getElementById('roleMenu');
        const arrow = document.querySelector('#roleBtn [data-lucide="chevron-down"]');
        if (menu) menu.style.display = 'none';
        if (arrow) arrow.style.transform = '';
    }
});

function clearFilters() {
    clearTimeout(searchDebounceTimer);
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';

    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('role');
    url.searchParams.delete('status');
    url.searchParams.set('page', '1');
    window.history.replaceState({}, '', url.toString());

    // Reset dropdown UI
    const label = document.getElementById('roleLabel');
    if (label) label.textContent = 'All Roles';
    const btn = document.getElementById('roleBtn');
    if (btn) btn.classList.remove('active');

    const opts = document.querySelectorAll('#roleMenu .role-opt');
    opts.forEach(opt => {
        const isAll = opt.getAttribute('data-value') === 'All Roles';
        opt.classList.toggle('selected', isAll);
        const check = opt.querySelector('svg');
        if (check) check.style.display = isAll ? '' : 'none';
    });

    const menu = document.getElementById('roleMenu');
    if (menu) menu.style.display = 'none';
    const arrow = document.querySelector('#roleBtn [data-lucide="chevron-down"]');
    if (arrow) arrow.style.transform = '';

    updateClearButtonVisibility();
    performLiveSearch(1);
}

function attachPaginationListeners() {
    const footer = document.getElementById('paginationFooter');
    if (!footer) return;
    const links = footer.querySelectorAll('a.sc-page-btn');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = link.getAttribute('href');
            if (href) {
                const parsedUrl = new URL(href, window.location.origin);
                const page = parsedUrl.searchParams.get('page') || 1;
                performLiveSearch(page);
            }
        });
    });
}

// Deactivate confirmation with SweetAlert2
function deactivateOfficer(id, name) {
    Swal.fire({
        title: 'Deactivate Account?',
        text: `Are you sure you want to deactivate ${name}? This officer will lose access to the MSWDO portal.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Deactivate',
        cancelButtonText: 'Cancel'
    }).then((res) => {
        if (res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/officers/${id}/deactivate`;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = `<input type="hidden" name="_token" value="${token}">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Activate confirmation with SweetAlert2
function activateOfficer(id, name) {
    Swal.fire({
        title: 'Activate Account?',
        text: `Are you sure you want to activate ${name}? This officer will be able to log in immediately.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Activate',
        cancelButtonText: 'Cancel'
    }).then((res) => {
        if (res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/officers/${id}/activate`;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = `<input type="hidden" name="_token" value="${token}">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
