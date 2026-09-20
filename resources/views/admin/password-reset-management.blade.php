@extends('admin.layout')
@section('title', 'MSWDO – Password Reset Management')
@section('page_title', 'Password Reset Management')

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

    /* ── Top Status Counters ── */
    .reset-kpi-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }
    .reset-kpi-card {
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
    .reset-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -2px rgba(15, 23, 42, 0.08);
    }
    .kpi-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kpi-icon-box svg { width: 24px; height: 24px; }
    .card-kpi-indigo .kpi-icon-box { background: #EEF2FF; color: #4338CA; }
    .card-kpi-amber .kpi-icon-box  { background: #FEF3C7; color: #D97706; }
    .card-kpi-sky .kpi-icon-box    { background: #E0F2FE; color: #0284C7; }
    .card-kpi-emerald .kpi-icon-box{ background: #ECFDF5; color: #059669; }

    /* ── Content Shell ── */
    .reset-shell {
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

    /* Status Dropdown */
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
    .status-opt {
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
    .status-opt:hover {
        background: #EFF6FF;
        color: #1E3A8A;
    }
    .status-opt.selected {
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

    /* ── Table Layout ── */
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 14px;
        border: 1px solid #EDF2F7;
    }
    .reset-tbl {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
    }
    .reset-tbl th {
        background: #F8FAFC;
        color: #475569;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.85rem 1.15rem;
        text-align: left;
        border-bottom: 1px solid #E2E8F0;
        white-space: nowrap;
    }
    .reset-tbl td {
        padding: 0.95rem 1.15rem;
        border-bottom: 1px solid #F1F5F9;
        color: #1E293B;
        vertical-align: middle;
        white-space: nowrap;
        background: #FFFFFF;
    }
    .reset-tbl tbody tr:last-child td { border-bottom: none; }
    .reset-tbl tbody tr:hover td { background: #F8FAFC; }

    /* Officer Profile Cell */
    .officer-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .officer-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #EEF2FF;
        color: #4338CA;
        font-weight: 700;
        font-size: 0.825rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #C7D2FE;
    }

    /* Status Pills */
    .status-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
    }
    .tag-pending   { background: #FEF3C7; color: #B45309; }
    .tag-approved  { background: #EFF6FF; color: #1D4ED8; }
    .tag-completed { background: #ECFDF5; color: #047857; }
    .tag-rejected  { background: #FEF2F2; color: #BE123C; }

    /* Action Buttons */
    .btn-approve {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #FFFFFF;
        background: #059669;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-approve:hover { background: #047857; }

    .btn-reject {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #DC2626;
        background: #FEF2F2;
        border: 1px solid #FECACA;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-reject:hover { background: #FEE2E2; }

    .btn-delete-record {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.775rem;
        font-weight: 600;
        color: #64748B;
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-delete-record:hover {
        background: #FEF2F2;
        color: #DC2626;
        border-color: #FECACA;
    }

    /* ── Mobile Stacked Cards (< 768px) ── */
    .mobile-requests-list { display: none; }
    @media (max-width: 767.98px) {
        .dash-banner {
            padding: 1.15rem 1.25rem;
            border-radius: 14px;
            margin-bottom: 1rem;
        }
        .dash-banner-title {
            font-size: 1.35rem;
        }
        .dash-banner-sub {
            font-size: 0.8rem;
        }

        .reset-kpi-strip {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.65rem;
            margin-bottom: 1.25rem;
        }
        .reset-kpi-card {
            padding: 0.85rem 0.95rem;
            gap: 0.65rem;
            border-radius: 14px;
            min-width: 0;
            overflow: hidden;
        }
        .kpi-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
        }
        .kpi-icon-box svg {
            width: 18px;
            height: 18px;
        }
        .reset-kpi-card .text-2xl {
            font-size: 1.35rem;
            line-height: 1.2;
        }
        .reset-kpi-card .text-xs {
            font-size: 0.7rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .reset-kpi-card .text-\[11px\] {
            font-size: 0.65rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .reset-shell {
            padding: 1rem 0.85rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
        }

        .toolbar-rail {
            flex-direction: column;
            align-items: stretch;
            gap: 0.35rem;
            margin-bottom: 0.75rem;
        }
        .filter-rail {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            gap: 0.65rem;
        }
        .filter-item {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }
        .filter-item.filter-search {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }
        .filter-dropdown {
            width: 100%;
            max-width: 100%;
            min-width: 0;
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
        }
        #showingCounter {
            width: 100%;
            font-size: 0.75rem;
            text-align: left;
            margin-top: 0.2rem;
            margin-bottom: 0.2rem;
            align-self: flex-start;
            color: #64748B;
        }

        .table-container { display: none; }
        .mobile-requests-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
            min-width: 0;
        }
        .mobile-req-card {
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
        .mobile-req-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 0;
            gap: 0.5rem;
            width: 100%;
        }
        .mobile-req-body {
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
        .mobile-req-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            width: 100%;
            min-width: 0;
        }
        .mobile-req-actions .btn-reject,
        .mobile-req-actions .btn-approve {
            width: 100%;
            justify-content: center;
            padding: 0.55rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 10px;
            box-sizing: border-box;
        }
        .mobile-req-actions .btn-delete-record {
            grid-column: span 2;
            width: 100%;
            justify-content: center;
            padding: 0.55rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 10px;
            box-sizing: border-box;
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
        .reset-kpi-strip {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        .reset-shell {
            padding: 0.85rem 0.65rem;
        }
        .mobile-req-card {
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
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .pagination-info {
            justify-content: center;
            text-align: center;
            font-size: 0.775rem;
        }
        .sc-pagination-controls {
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
        }
        .sc-page-btn {
            height: 36px;
            min-width: 36px;
            padding: 0 12px;
            font-size: 0.775rem;
        }
    }
</style>

{{-- Page Header Banner --}}
<header class="dash-banner flex flex-col md:flex-row md:items-center justify-between gap-4 select-none">
    <div>
        <div class="dash-banner-title">Password Reset Management</div>
        <div class="dash-banner-sub">
            <span>MSWDO Silang — Access Security &amp; Officer Account Recovery</span>
            <span class="opacity-40">•</span>
            @if(($stats['pending'] ?? 0) > 0)
                <span class="inline-flex items-center gap-1.5 bg-amber-400/25 text-amber-200 border border-amber-300/30 text-xs font-bold px-3 py-1 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>{{ $stats['pending'] }} Pending Authorization</span>
                </span>
            @else
                <span class="dash-live-badge">
                    <span class="dash-pulse-dot"></span>
                    <span>All Requests Clear</span>
                </span>
            @endif
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

{{-- Status KPI Cards --}}
<section class="reset-kpi-strip">
    <div class="reset-kpi-card card-kpi-indigo">
        <div class="kpi-icon-box">
            <i data-lucide="key"></i>
        </div>
        <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Requests</div>
            <div class="text-2xl font-bold text-slate-900 leading-tight">{{ number_format($stats['total'] ?? $requests->total()) }}</div>
            <div class="text-[11px] text-slate-400">All Time Submissions</div>
        </div>
    </div>

    <div class="reset-kpi-card card-kpi-amber">
        <div class="kpi-icon-box">
            <i data-lucide="clock"></i>
        </div>
        <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Review</div>
            <div class="text-2xl font-bold text-amber-600 leading-tight">{{ number_format($stats['pending'] ?? 0) }}</div>
            <div class="text-[11px] text-slate-400">Requires Admin Approval</div>
        </div>
    </div>

    <div class="reset-kpi-card card-kpi-sky">
        <div class="kpi-icon-box">
            <i data-lucide="send"></i>
        </div>
        <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Link Dispatched</div>
            <div class="text-2xl font-bold text-slate-900 leading-tight">{{ number_format($stats['approved'] ?? 0) }}</div>
            <div class="text-[11px] text-slate-400">Email Sent to Officer</div>
        </div>
    </div>

    <div class="reset-kpi-card card-kpi-emerald">
        <div class="kpi-icon-box">
            <i data-lucide="shield-check"></i>
        </div>
        <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completed Resets</div>
            <div class="text-2xl font-bold text-emerald-600 leading-tight">{{ number_format($stats['completed'] ?? 0) }}</div>
            <div class="text-[11px] text-slate-400">Password Updated Successfully</div>
        </div>
    </div>
</section>

{{-- Alerts --}}
@if(session('success'))
    <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-sm">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

{{-- Main Table Shell --}}
<div class="reset-shell">
    {{-- Filter Rail --}}
    <div class="toolbar-rail">
        <div class="filter-rail">
            <div class="filter-item filter-search">
                <label class="filter-label">Search</label>
                <div class="filter-search-wrap">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, email, or status..." value="{{ request()->get('search', '') }}" oninput="onSearchInput()" onkeydown="if(event.key==='Enter'){event.preventDefault();handleSearch();}">
                    <button type="button" class="filter-search-btn" onclick="handleSearch()" title="Search">
                        <i data-lucide="search"></i>
                    </button>
                </div>
            </div>

            @php 
                $curStatus = request()->get('status', 'All Status');
                $statusLabels = [
                    'All Status' => 'All Status',
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'completed' => 'Completed',
                    'rejected' => 'Rejected',
                ];
                $curStatusLabel = $statusLabels[$curStatus] ?? $curStatus;
            @endphp
            <div class="filter-item filter-dropdown" id="statusDropdown">
                <label class="filter-label">Filter by Status</label>
                <div onclick="toggleStatusMenu(event)" class="filter-select-btn {{ ($curStatus && $curStatus !== 'All Status') ? 'active' : '' }}" id="statusBtn">
                    <span id="statusLabel" class="filter-select-label">{{ $curStatusLabel }}</span>
                    <i data-lucide="chevron-down" style="width:16px;height:16px;color:#64748B;flex-shrink:0;transition:transform 0.2s;"></i>
                </div>
                <div id="statusMenu" class="filter-menu" style="display:none">
                    @foreach($statusLabels as $val => $lbl)
                        <div class="status-opt {{ $curStatus === $val ? 'selected' : '' }}" data-value="{{ $val }}" onclick="selectStatus('{{ $val }}', '{{ $lbl }}')">
                            <span>{{ $lbl }}</span>
                            @if($curStatus === $val)
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
            @if($requests->total() > 0)
                Showing <span class="font-bold text-slate-800">{{ $requests->firstItem() ?? 0 }}–{{ $requests->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800">{{ $requests->total() }}</span> records
            @else
                Showing <span class="font-bold text-slate-800">0</span> records
            @endif
        </div>
    </div>

    {{-- Desktop Table --}}
    <div class="table-container">
        <table class="reset-tbl">
            <thead>
                <tr>
                    <th>Officer Details</th>
                    <th>Email Address</th>
                    <th>Request Status</th>
                    <th>Submitted At</th>
                    <th>Expires At</th>
                    <th>Processed By</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="resetTableBody">
                @forelse($requests as $req)
                @php
                    $userName = $req->user ? $req->user->name : 'Unregistered Officer';
                    $words = explode(' ', trim($userName));
                    $init = count($words) >= 2 
                        ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
                        : strtoupper(substr($userName,0,2));

                    $tagClass = 'tag-pending';
                    if ($req->status === 'approved') $tagClass = 'tag-approved';
                    elseif ($req->status === 'completed') $tagClass = 'tag-completed';
                    elseif ($req->status === 'rejected') $tagClass = 'tag-rejected';
                @endphp
                <tr>
                    <td>
                        <div class="officer-cell">
                            <div class="officer-avatar">{{ $init }}</div>
                            <div>
                                <div class="font-bold text-slate-900">{{ $userName }}</div>
                                <div class="text-[11px] text-slate-400">{{ $req->user ? ucfirst(str_replace('_', ' ', $req->user->role ?? 'Staff')) : 'External Request' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-1.5 text-slate-600 text-sm">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>{{ $req->email }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="status-tag {{ $tagClass }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            <span>{{ ucfirst($req->status) }}</span>
                        </span>
                    </td>
                    <td>
                        <div class="text-sm text-slate-700 font-medium">{{ $req->requested_at ? $req->requested_at->format('M d, Y') : '-' }}</div>
                        <div class="text-[11px] text-slate-400">{{ $req->requested_at ? $req->requested_at->format('h:i A') : '' }}</div>
                    </td>
                    <td>
                        @if($req->expires_at)
                            <div class="text-sm {{ $req->isExpired() ? 'text-rose-600 font-medium' : 'text-slate-700' }}">
                                {{ $req->expires_at->format('M d, Y') }}
                            </div>
                            <div class="text-[11px] {{ $req->isExpired() ? 'text-rose-500 font-bold' : 'text-slate-400' }}">
                                {{ $req->isExpired() ? 'Expired' : $req->expires_at->format('h:i A') }}
                            </div>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-sm text-slate-600">{{ $req->processedBy ? $req->processedBy->name : 'Pending Action' }}</span>
                    </td>
                    <td style="text-align:right;">
                        @if($req->status === 'pending')
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" class="btn-approve" onclick="confirmApprove({{ $req->id }}, '{{ addslashes($userName) }}')">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span>Approve</span>
                                </button>
                                <button type="button" class="btn-reject" onclick="confirmReject({{ $req->id }}, '{{ addslashes($userName) }}')">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    <span>Reject</span>
                                </button>
                            </div>
                        @else
                            <div class="flex items-center justify-end">
                                <button type="button" class="btn-delete-record" onclick="confirmDelete({{ $req->id }}, '{{ addslashes($userName) }}')">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-slate-400">
                        <i data-lucide="shield-check" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                        <div class="font-semibold text-slate-600">No password reset requests found.</div>
                        <div class="text-xs text-slate-400 mt-1">Pending password recovery submissions from staff will appear here.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Stacked Cards (< 768px) --}}
    <div class="mobile-requests-list" id="mobileRequestsList">
        @forelse($requests as $req)
        @php
            $userName = $req->user ? $req->user->name : 'Unregistered Officer';
            $words = explode(' ', trim($userName));
            $init = count($words) >= 2 
                ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
                : strtoupper(substr($userName,0,2));

            $tagClass = 'tag-pending';
            if ($req->status === 'approved') $tagClass = 'tag-approved';
            elseif ($req->status === 'completed') $tagClass = 'tag-completed';
            elseif ($req->status === 'rejected') $tagClass = 'tag-rejected';
        @endphp
        <div class="mobile-req-card">
            <div class="mobile-req-head">
                <div class="flex items-center gap-3 min-w-0 flex-1 mr-2">
                    <div class="officer-avatar flex-shrink-0">{{ $init }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-slate-900 text-sm truncate leading-snug">{{ $userName }}</div>
                        <div class="text-xs text-slate-500 truncate mt-0.5">{{ $req->email }}</div>
                    </div>
                </div>
                <span class="status-tag {{ $tagClass }} text-[11px] flex-shrink-0">{{ ucfirst($req->status) }}</span>
            </div>

            <div class="mobile-req-body">
                <div class="flex justify-between items-center text-xs gap-2">
                    <span class="text-slate-500 flex-shrink-0">Requested:</span>
                    <span class="font-medium text-slate-700 text-right truncate">{{ $req->requested_at ? $req->requested_at->format('M d, Y h:i A') : '-' }}</span>
                </div>
                <div class="flex justify-between items-center text-xs gap-2">
                    <span class="text-slate-500 flex-shrink-0">Expires:</span>
                    <span class="font-medium text-right truncate {{ $req->isExpired() ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                        {{ $req->expires_at ? ($req->isExpired() ? 'Expired' : $req->expires_at->format('M d, Y h:i A')) : '-' }}
                    </span>
                </div>
                <div class="flex justify-between items-center text-xs gap-2">
                    <span class="text-slate-500 flex-shrink-0">Processed By:</span>
                    <span class="text-slate-700 text-right truncate font-medium">{{ $req->processedBy ? $req->processedBy->name : 'Pending' }}</span>
                </div>
            </div>

            <div class="mobile-req-actions">
                @if($req->status === 'pending')
                    <button type="button" class="btn-reject" onclick="confirmReject({{ $req->id }}, '{{ addslashes($userName) }}')">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        <span>Reject</span>
                    </button>
                    <button type="button" class="btn-approve" onclick="confirmApprove({{ $req->id }}, '{{ addslashes($userName) }}')">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        <span>Approve Reset</span>
                    </button>
                @else
                    <button type="button" class="btn-delete-record" onclick="confirmDelete({{ $req->id }}, '{{ addslashes($userName) }}')">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Delete Record</span>
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-slate-400 text-sm">
            No password reset requests found.
        </div>
        @endforelse
    </div>

    {{-- Pagination Footer --}}
    <div class="pagination-footer" id="paginationFooter">
        <div class="pagination-info">
            @if($requests->total() > 0)
                <span>Showing</span>
                <span class="font-bold text-slate-800">{{ $requests->firstItem() ?? 0 }}–{{ $requests->lastItem() ?? 0 }}</span>
                <span>of</span>
                <span class="font-bold text-slate-800">{{ $requests->total() }}</span>
                <span>records</span>
                <span class="text-slate-300 mx-1.5">•</span>
                <span>Page <strong class="text-slate-800">{{ $requests->currentPage() }}</strong> of <strong class="text-slate-800">{{ $requests->lastPage() }}</strong></span>
            @else
                <span>Showing</span>
                <span class="font-bold text-slate-800">0</span>
                <span>records</span>
            @endif
        </div>
        <div>
            {{ $requests->appends(request()->only(['search', 'status', 'per_page']))->links('vendor.pagination.custom-simple') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') lucide.createIcons();
    updateLiveClock();
    setInterval(updateLiveClock, 1000);
    attachPaginationListeners();
    updateClearButtonVisibility();
});

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

let searchDebounceTimer = null;
let currentSearchAbortController = null;

function onSearchInput() {
    updateClearButtonVisibility();
    const q = document.getElementById('searchInput').value.trim();

    // 1. Instant client-side row filter on current visible rows (0ms feedback)
    filterTableRows(q);

    // 2. Debounced asynchronous server search (250ms) to query database across all records without full page reload
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(function() {
        performLiveSearch(1);
    }, 250);
}

function filterTableRows(query) {
    const q = query.toLowerCase();
    const rows = document.querySelectorAll('#resetTableBody tr, #mobileRequestsList .mobile-req-card');
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

    const tableContainer = document.querySelector('.table-container');
    const mobileContainer = document.getElementById('mobileRequestsList');
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
        const newTableBody = doc.getElementById('resetTableBody');
        const oldTableBody = document.getElementById('resetTableBody');
        if (newTableBody && oldTableBody) {
            oldTableBody.innerHTML = newTableBody.innerHTML;
        }

        // Update Mobile List
        const newMobileList = doc.getElementById('mobileRequestsList');
        const oldMobileList = document.getElementById('mobileRequestsList');
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

function toggleStatusMenu(e) {
    if (e) e.stopPropagation();
    const menu = document.getElementById('statusMenu');
    const arrow = document.querySelector('#statusBtn [data-lucide="chevron-down"]');
    if (!menu) return;
    const isHidden = (menu.style.display === 'none' || !menu.style.display);
    menu.style.display = isHidden ? 'block' : 'none';
    if (arrow) arrow.style.transform = isHidden ? 'rotate(180deg)' : '';
}

function selectStatus(status, label) {
    clearTimeout(searchDebounceTimer);
    const url = new URL(window.location.href);
    if (status && status !== 'All Status') {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    url.searchParams.set('page', '1');
    window.history.replaceState({}, '', url.toString());

    // Update label & button state
    const labelEl = document.getElementById('statusLabel');
    if (labelEl) labelEl.textContent = label || status;
    const btn = document.getElementById('statusBtn');
    if (btn) btn.classList.toggle('active', status !== 'All Status');

    // Update options selection
    const opts = document.querySelectorAll('#statusMenu .status-opt');
    opts.forEach(opt => {
        const val = opt.getAttribute('data-value');
        opt.classList.toggle('selected', val === status);
        const check = opt.querySelector('svg');
        if (check) check.style.display = (val === status) ? '' : 'none';
    });

    // Close menu
    const menu = document.getElementById('statusMenu');
    if (menu) menu.style.display = 'none';
    const arrow = document.querySelector('#statusBtn [data-lucide="chevron-down"]');
    if (arrow) arrow.style.transform = '';

    updateClearButtonVisibility();
    performLiveSearch(1);
}

// Close status dropdown when clicking outside
document.addEventListener('click', function(e) {
    const statusDD = document.getElementById('statusDropdown');
    if (statusDD && !statusDD.contains(e.target)) {
        const menu = document.getElementById('statusMenu');
        const arrow = document.querySelector('#statusBtn [data-lucide="chevron-down"]');
        if (menu) menu.style.display = 'none';
        if (arrow) arrow.style.transform = '';
    }
});

function updateClearButtonVisibility() {
    const searchInput = document.getElementById('searchInput');
    const hasSearch = searchInput && searchInput.value.trim().length > 0;
    const url = new URL(window.location.href);
    const hasStatus = url.searchParams.has('status') && url.searchParams.get('status') !== 'All Status';
    const clearBtn = document.getElementById('clearFiltersBtn');
    if (clearBtn) {
        clearBtn.style.display = (hasSearch || hasStatus) ? 'inline-flex' : 'none';
    }
}

function clearFilters() {
    clearTimeout(searchDebounceTimer);
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';

    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('status');
    url.searchParams.set('page', '1');
    window.history.replaceState({}, '', url.toString());

    // Reset dropdown UI
    const label = document.getElementById('statusLabel');
    if (label) label.textContent = 'All Status';
    const btn = document.getElementById('statusBtn');
    if (btn) btn.classList.remove('active');

    const opts = document.querySelectorAll('#statusMenu .status-opt');
    opts.forEach(opt => {
        const isAll = opt.getAttribute('data-value') === 'All Status';
        opt.classList.toggle('selected', isAll);
        const check = opt.querySelector('svg');
        if (check) check.style.display = isAll ? '' : 'none';
    });

    const menu = document.getElementById('statusMenu');
    if (menu) menu.style.display = 'none';
    const arrow = document.querySelector('#statusBtn [data-lucide="chevron-down"]');
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

function confirmApprove(id, name) {
    Swal.fire({
        title: 'Approve Password Reset?',
        text: `Authorize password reset for ${name}? A secure reset link will be sent to their email.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Approve & Dispatch Link',
        cancelButtonText: 'Cancel'
    }).then((res) => {
        if (res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/password-reset/${id}/approve`;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = `<input type="hidden" name="_token" value="${token}">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmReject(id, name) {
    Swal.fire({
        title: 'Reject Password Reset?',
        text: `Are you sure you want to reject the reset request for ${name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Reject',
        cancelButtonText: 'Cancel'
    }).then((res) => {
        if (res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/password-reset/${id}/reject`;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = `<input type="hidden" name="_token" value="${token}">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Request Record?',
        text: `Delete the password reset log for ${name}? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((res) => {
        if (res.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/password-reset/${id}`;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = `<input type="hidden" name="_token" value="${token}"><input type="hidden" name="_method" value="DELETE">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
