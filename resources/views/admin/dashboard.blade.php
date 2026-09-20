@extends('admin.layout')
@section('title', 'MSWDO Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@section('content')
@php
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', trim($adminName));
$initials = count($words) >= 2
    ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
    : strtoupper(substr($adminName,0,2));
@endphp

<style>
    /* ── Scope Variables ── */
    :root {
        --dash-navy: #1A237E;
        --dash-navy-light: #1E3A8A;
        --dash-blue: #2563EB;
        --dash-emerald: #059669;
        --dash-amber: #D97706;
        --dash-rose: #E11D48;
        --dash-cyan: #0891B2;
        --dash-slate: #475569;
        --dash-border: #E2E8F0;
        --dash-card-bg: #FFFFFF;
    }

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

    /* ── Top Executive KPI Strip ── */
    .kpi-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--dash-border);
        border-radius: 16px;
        padding: 1.25rem 1.4rem;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        gap: 1.15rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -3px rgba(15, 23, 42, 0.08);
    }
    .kpi-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kpi-icon-box svg { width: 26px; height: 26px; }
    .kpi-data { flex: 1; min-width: 0; }
    .kpi-label {
        font-size: 0.775rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748B;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .kpi-val {
        font-size: 1.65rem;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }
    .kpi-hint {
        font-size: 0.75rem;
        color: #94A3B8;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    /* KPI Accents */
    .kpi-card.kpi-indigo .kpi-icon-box { background: #EEF2FF; color: #4338CA; }
    .kpi-card.kpi-sky .kpi-icon-box { background: #E0F2FE; color: #0284C7; }
    .kpi-card.kpi-emerald .kpi-icon-box { background: #ECFDF5; color: #059669; }
    .kpi-card.kpi-amber .kpi-icon-box { background: #FEF3C7; color: #D97706; }

    /* ── General Section Headers ── */
    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.15rem;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1E293B;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 0;
    }
    .section-title svg { color: var(--dash-navy); width: 22px; height: 22px; }
    .section-subtitle {
        font-size: 0.85rem;
        color: #64748B;
        margin: 0.2rem 0 0 0;
    }

    /* ── Service Cards Responsive Grid ── */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 1.35rem;
        margin-bottom: 2rem;
    }
    .service-card {
        background: #FFFFFF;
        border: 1px solid var(--dash-border);
        border-radius: 16px;
        padding: 1.4rem;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -2px rgba(15, 23, 42, 0.08);
    }
    .service-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.15rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #F1F5F9;
    }
    .service-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .service-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .service-avatar svg { width: 22px; height: 22px; }
    .service-title-text {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0F172A;
        margin: 0;
        line-height: 1.2;
    }
    .service-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    
    /* 4-Stat Box Grid inside Service Card */
    .service-metrics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    .service-stat-tile {
        background: #F8FAFC;
        border: 1px solid #EDF2F7;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 72px;
    }
    .stat-tile-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .stat-tile-val {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.1;
    }
    .stat-tile-val.val-success { color: #059669; }
    .stat-tile-val.val-info { color: #2563EB; }
    .stat-tile-val.val-warning { color: #D97706; }
    .stat-tile-val.val-danger { color: #DC2626; }

    /* Themed Accents for Service Cards */
    .card-theme-indigo .service-avatar { background: #EEF2FF; color: #4338CA; }
    .card-theme-indigo .service-badge { background: #EEF2FF; color: #4338CA; }
    .card-theme-emerald .service-avatar { background: #ECFDF5; color: #059669; }
    .card-theme-emerald .service-badge { background: #ECFDF5; color: #059669; }
    .card-theme-amber .service-avatar { background: #FEF3C7; color: #D97706; }
    .card-theme-amber .service-badge { background: #FEF3C7; color: #D97706; }
    .card-theme-rose .service-avatar { background: #FFE4E6; color: #E11D48; }
    .card-theme-rose .service-badge { background: #FFE4E6; color: #E11D48; }
    .card-theme-cyan .service-avatar { background: #CFFAFE; color: #0891B2; }
    .card-theme-cyan .service-badge { background: #CFFAFE; color: #0891B2; }

    /* Service Card Footer Link */
    .service-footer {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px dashed #F1F5F9;
        display: flex;
        justify-content: flex-end;
    }
    .service-nav-btn {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4338CA;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .service-nav-btn:hover { color: #1E3A8A; text-decoration: underline; }

    /* Standby Service Placeholder */
    .standby-state {
        background: #F8FAFC;
        border: 1px dashed #CBD5E1;
        border-radius: 12px;
        padding: 1.5rem 1rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* ── Analytics Charts Grid ── */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
        gap: 1.35rem;
        margin-bottom: 2rem;
    }
    .chart-panel {
        background: #FFFFFF;
        border: 1px solid var(--dash-border);
        border-radius: 16px;
        padding: 1.35rem;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
    }
    .chart-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    .chart-panel-title {
        font-size: 0.975rem;
        font-weight: 700;
        color: #1E293B;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    .chart-panel-title svg { width: 18px; height: 18px; color: var(--dash-navy); }
    .chart-canvas-box {
        position: relative;
        height: 270px;
        width: 100%;
        flex: 1;
    }

    /* ── Data Panels & Recent Cases ── */
    .panel-box {
        background: #FFFFFF;
        border: 1px solid var(--dash-border);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        margin-bottom: 1.75rem;
    }

    /* Modern Table */
    .dash-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 12px;
        border: 1px solid #F1F5F9;
    }
    .dash-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.875rem;
    }
    .dash-table th {
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
    .dash-table td {
        padding: 0.95rem 1.15rem;
        border-bottom: 1px solid #F1F5F9;
        color: #1E293B;
        vertical-align: middle;
        white-space: nowrap;
        background: #FFFFFF;
    }
    .dash-table tbody tr:last-child td { border-bottom: none; }
    .dash-table tbody tr:hover td { background: #F8FAFC; }

    /* Client name in table */
    .client-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        color: #0F172A;
    }
    .client-avatar-mini {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #EFF6FF;
        color: #1E3A8A;
        font-weight: 700;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Semantic Status Pills */
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
    .status-pill-blue    { background: #EFF6FF; color: #1D4ED8; }
    .status-pill-emerald { background: #ECFDF5; color: #047857; }
    .status-pill-amber   { background: #FFFBEB; color: #B45309; }
    .status-pill-rose    { background: #FFF1F2; color: #BE123C; }
    .status-pill-slate   { background: #F1F5F9; color: #475569; }

    .service-tag {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 500;
        color: #475569;
        background: #F1F5F9;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
    }

    .table-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.7rem;
        border-radius: 6px;
        font-size: 0.775rem;
        font-weight: 600;
        color: #1E3A8A;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .table-action-btn:hover {
        background: #1E3A8A;
        color: #FFFFFF;
        border-color: #1E3A8A;
    }

    /* ── Mobile Stacked Case Cards (<640px) ── */
    .mobile-cases-list { display: none; }
    @media (max-width: 639.98px) {
        .dash-table-wrap { display: none; }
        .mobile-cases-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .mobile-case-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }
        .mobile-case-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.5rem;
        }
        .mobile-case-card-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.775rem;
            color: #64748B;
            border-top: 1px dashed #E2E8F0;
            padding-top: 0.5rem;
        }
    }

    /* ── Reports Summary Grid ── */
    .rep-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 1rem;
    }
    .rep-card {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.15rem;
        transition: box-shadow 0.2s ease;
    }
    .rep-card:hover {
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    }
    .rep-title {
        font-size: 0.725rem;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.35rem;
    }
    .rep-val {
        font-size: 1.45rem;
        font-weight: 700;
        color: #1E3A8A;
        line-height: 1.1;
    }

    /* ── Report Studio & Generator ── */
    .report-studio-card {
        background: #FFFFFF;
        border: 1px solid var(--dash-border);
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .report-controls-bar {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 1.15rem;
        margin-bottom: 1.25rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.85rem;
        align-items: flex-end;
    }
    .report-field-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }
    .report-field-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748B;
    }
    .report-select, .report-input {
        height: 38px;
        border-radius: 8px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        padding: 0 10px;
        font-size: 0.85rem;
        color: #1E293B;
        font-family: inherit;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        width: 100%;
        box-sizing: border-box;
    }
    .report-select:focus, .report-input:focus {
        border-color: #1A237E;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.12);
    }
    .report-actions-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .btn-report-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        height: 38px;
        padding: 0 14px;
        border-radius: 8px;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
        border: 1px solid transparent;
    }
    .btn-report-generate {
        background: #1A237E;
        color: #FFFFFF;
        border-color: #1A237E;
        flex: 1;
        min-width: 130px;
    }
    .btn-report-generate:hover {
        background: #121858;
        border-color: #121858;
    }
    .btn-report-pdf {
        background: #EEF2FF;
        color: #1A237E;
        border-color: #C7D2FE;
    }
    .btn-report-pdf:hover {
        background: #E0E7FF;
        color: #121858;
    }
    .btn-report-print {
        background: #FFFFFF;
        color: #334155;
        border-color: #CBD5E1;
    }
    .btn-report-print:hover {
        background: #F1F5F9;
        color: #0F172A;
    }

    /* Live Scope Badge Bar */
    .report-scope-strip {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 10px;
        padding: 0.65rem 1rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .report-scope-info {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.825rem;
        color: #1E3A8A;
        font-weight: 600;
    }
    .report-scope-pill {
        background: #1E3A8A;
        color: #FFFFFF;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        letter-spacing: 0.04em;
    }

    /* Report Breakdown Tables */
    .report-breakdown-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-top: 1.25rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 767.98px) {
        .report-breakdown-grid { grid-template-columns: 1fr; }
        .report-controls-bar { grid-template-columns: 1fr; }
        .report-actions-wrap { width: 100%; }
        .btn-report-action { width: 100%; }
    }

    /* ── Action Buttons ── */
    .btn-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.95rem;
        border-radius: 8px;
        font-size: 0.825rem;
        font-weight: 600;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-pill:hover {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #CBD5E1;
    }
    .btn-pill-primary {
        background: #1A237E;
        color: #FFFFFF;
        border-color: #1A237E;
    }
    .btn-pill-primary:hover {
        background: #121858;
        color: #FFFFFF;
        border-color: #121858;
    }

    /* ── Responsive Adaptations ── */
    @media (max-width: 767.98px) {
        .dash-banner {
            padding: 1.25rem 1.4rem;
            border-radius: 14px;
            margin-bottom: 1.25rem;
        }
        .dash-banner-title {
            font-size: 1.35rem;
        }
        .kpi-strip {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .kpi-card {
            padding: 1rem;
            gap: 0.75rem;
        }
        .kpi-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
        }
        .kpi-icon-box svg { width: 20px; height: 20px; }
        .kpi-val { font-size: 1.35rem; }
        .panel-box { padding: 1.15rem; }
    }

    @media (max-width: 480px) {
        .kpi-strip {
            grid-template-columns: 1fr;
        }
        .services-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

{{-- Page Header Banner --}}
<header class="dash-banner flex flex-col md:flex-row md:items-center justify-between gap-4 select-none">
    <div>
        <div class="dash-banner-title">Welcome back, {{ $adminName }}</div>
        <div class="dash-banner-sub">
            <span>MSWDO Silang Portal</span>
            <span class="opacity-40">•</span>
            <span class="dash-live-badge">
                <span class="dash-pulse-dot"></span>
                <span>System Online</span>
            </span>
        </div>
    </div>
    <div class="flex items-center gap-3 self-start md:self-auto">
        <div class="text-right hidden sm:block">
            <div class="text-xs font-semibold uppercase tracking-wider text-white/70">Philippine Standard Time</div>
            <div class="text-sm font-semibold text-white tracking-wide" id="liveClock">Loading date...</div>
        </div>
        <div class="w-11 h-11 rounded-full bg-white/20 border-2 border-white/40 text-white font-bold text-sm flex items-center justify-center shadow-inner cursor-default" title="Logged in as: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

{{-- Top Executive KPI Strip --}}
<section class="kpi-strip">
    <!-- Card 1: Total Beneficiaries -->
    <div class="kpi-card kpi-indigo">
        <div class="kpi-icon-box">
            <i data-lucide="users"></i>
        </div>
        <div class="kpi-data">
            <div class="kpi-label">Beneficiaries</div>
            <div class="kpi-val">{{ number_format($executiveSummary['totalBeneficiaries'] ?? 0) }}</div>
            <div class="kpi-hint">Registered Clients &amp; Seniors</div>
        </div>
    </div>

    <!-- Card 2: Active / In-Flight Cases -->
    <div class="kpi-card kpi-sky">
        <div class="kpi-icon-box">
            <i data-lucide="activity"></i>
        </div>
        <div class="kpi-data">
            <div class="kpi-label">Active / Ongoing</div>
            <div class="kpi-val">{{ number_format($executiveSummary['activeCases'] ?? 0) }}</div>
            <div class="kpi-hint">Requires Worker Attention</div>
        </div>
    </div>

    <!-- Card 3: Resolved This Month -->
    <div class="kpi-card kpi-emerald">
        <div class="kpi-icon-box">
            <i data-lucide="check-circle-2"></i>
        </div>
        <div class="kpi-data">
            <div class="kpi-label">Closed This Month</div>
            <div class="kpi-val">{{ number_format($executiveSummary['closedThisMonth'] ?? 0) }}</div>
            <div class="kpi-hint">Cases Resolved in {{ now()->format('M') }}</div>
        </div>
    </div>

    <!-- Card 4: Total Financial Released -->
    <div class="kpi-card kpi-amber">
        <div class="kpi-icon-box">
            <i data-lucide="banknote"></i>
        </div>
        <div class="kpi-data">
            <div class="kpi-label">Funds Disbursed</div>
            <div class="kpi-val">₱{{ number_format(($executiveSummary['financialReleased'] ?? 0) / 1000, 1) }}k</div>
            <div class="kpi-hint">₱{{ number_format($executiveSummary['financialReleased'] ?? 0, 2) }} total</div>
        </div>
    </div>
</section>

{{-- Service Breakdown Section --}}
<div class="section-head">
    <div>
        <h3 class="section-title">
            <i data-lucide="layout-grid"></i> Services Breakdown
        </h3>
        <p class="section-subtitle">Real-time status metrics across municipal welfare divisions</p>
    </div>
</div>

<div class="services-grid">
    @foreach($serviceBreakdown as $serviceName => $service)
    @php
        $color = $service['color'] ?? 'indigo';
        $icon = $service['icon'] ?? 'folder';
        $isStandby = ($service['status'] ?? 'active') === 'standby';
        $total = $service['total'] ?? 0;
        $route = $service['route'] ?? '#';
    @endphp
    <div class="service-card card-theme-{{ $color }}">
        <div>
            <div class="service-card-top">
                <div class="service-info">
                    <div class="service-avatar">
                        <i data-lucide="{{ $icon }}"></i>
                    </div>
                    <div>
                        <h4 class="service-title-text">{{ $serviceName }}</h4>
                        <span class="text-xs text-slate-500 font-medium">Department Portal</span>
                    </div>
                </div>
                @if(!$isStandby)
                    <span class="service-badge">Total: {{ number_format($total) }}</span>
                @else
                    <span class="service-badge" style="background:#F1F5F9; color:#64748B;">Standby</span>
                @endif
            </div>

            @if($isStandby)
                <div class="standby-state">
                    <i data-lucide="clock" class="w-6 h-6 text-slate-400"></i>
                    <div class="text-xs font-semibold text-slate-600">Module Under Setup</div>
                    <p class="text-[11px] text-slate-400 m-0">Case encoding &amp; intake will activate upon system schedule.</p>
                </div>
            @else
                <div class="service-metrics-grid">
                    @foreach($service['metrics'] ?? [] as $m)
                    @php
                        $badgeStyle = 'val-neutral';
                        if (($m['badge'] ?? '') === 'success') $badgeStyle = 'val-success';
                        elseif (($m['badge'] ?? '') === 'info') $badgeStyle = 'val-info';
                        elseif (($m['badge'] ?? '') === 'warning') $badgeStyle = 'val-warning';
                        elseif (($m['badge'] ?? '') === 'danger') $badgeStyle = 'val-danger';
                    @endphp
                    <div class="service-stat-tile">
                        <span class="stat-tile-label" title="{{ $m['label'] }}">{{ $m['label'] }}</span>
                        <span class="stat-tile-val {{ $badgeStyle }}">{{ $m['val'] }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if(!$isStandby && $route !== '#')
        <div class="service-footer">
            <a href="{{ $route }}" class="service-nav-btn">
                <span>Access Module</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
        @endif
    </div>
    @endforeach
</div>

{{-- Analytics Overview --}}
<div class="section-head">
    <div>
        <h3 class="section-title">
            <i data-lucide="bar-chart-3"></i> Analytics &amp; Visualizations
        </h3>
        <p class="section-subtitle">Trends, caseload progression, and service distribution overview</p>
    </div>
</div>

<div class="analytics-grid">
    <!-- Chart 1: Social Case Study Trends -->
    <div class="chart-panel">
        <div class="chart-panel-header">
            <h4 class="chart-panel-title">
                <i data-lucide="trending-up"></i> Social Case Inflow (12 Months)
            </h4>
        </div>
        <div class="chart-canvas-box">
            <canvas id="socialCaseChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Financial Assistance Pipeline -->
    <div class="chart-panel">
        <div class="chart-panel-header">
            <h4 class="chart-panel-title">
                <i data-lucide="git-commit"></i> Financial Assistance Pipeline
            </h4>
        </div>
        <div class="chart-canvas-box">
            <canvas id="financialChart"></canvas>
        </div>
    </div>

    <!-- Chart 3: Senior Citizens Demographics -->
    <div class="chart-panel">
        <div class="chart-panel-header">
            <h4 class="chart-panel-title">
                <i data-lucide="pie-chart"></i> Senior Citizen Status Ratio
            </h4>
        </div>
        <div class="chart-canvas-box">
            <canvas id="seniorChart"></canvas>
        </div>
    </div>

    <!-- Chart 4: Overall Service Distribution -->
    <div class="chart-panel">
        <div class="chart-panel-header">
            <h4 class="chart-panel-title">
                <i data-lucide="layers"></i> Total Services Distribution
            </h4>
        </div>
        <div class="chart-canvas-box">
            <canvas id="distributionChart"></canvas>
        </div>
    </div>
</div>

{{-- Recent Cases Panel --}}
<div class="panel-box">
    <div class="section-head">
        <div>
            <h3 class="section-title">
                <i data-lucide="clock-3"></i> Recent Casework Activity
            </h3>
            <p class="section-subtitle">Recently registered or updated beneficiaries across services</p>
        </div>
    </div>

    <!-- Desktop & Tablet Table -->
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Beneficiary / Client</th>
                    <th>Service Division</th>
                    <th>Assigned Worker</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @if($recentCases && $recentCases->count() > 0)
                    @foreach($recentCases as $case)
                    @php
                        $cName = $case['client'] ?? 'Unknown Client';
                        $cWords = explode(' ', trim($cName));
                        $cInit = count($cWords) >= 2
                            ? strtoupper(substr($cWords[0],0,1).substr($cWords[1],0,1))
                            : strtoupper(substr($cName,0,2));

                        $st = strtolower($case['status'] ?? 'pending');
                        $pillClass = 'status-pill-slate';
                        if (in_array($st, ['active', 'in review', 'step 1 approved'])) {
                            $pillClass = 'status-pill-blue';
                        } elseif (in_array($st, ['resolved', 'completed', 'released'])) {
                            $pillClass = 'status-pill-emerald';
                        } elseif (in_array($st, ['pending', 'pending review'])) {
                            $pillClass = 'status-pill-amber';
                        } elseif (in_array($st, ['overdue', 'rejected', 'archived'])) {
                            $pillClass = 'status-pill-rose';
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="client-cell">
                                <div class="client-avatar-mini">{{ $cInit }}</div>
                                <span>{{ $cName }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="service-tag">{{ $case['service'] }}</span>
                        </td>
                        <td>
                            <span class="text-slate-600">{{ $case['officer'] }}</span>
                        </td>
                        <td>
                            <span class="status-pill {{ $pillClass }}">{{ $case['status'] }}</span>
                        </td>
                        <td>
                            <span class="text-xs text-slate-500 font-medium">{{ $case['updated'] }}</span>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ $case['url'] ?? '#' }}" class="table-action-btn">
                                <span>View</span>
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" style="padding:2.5rem; text-align:center; color:#94A3B8;">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                            <div>No recent casework activity recorded yet.</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Mobile Stacked Card View (<640px) -->
    <div class="mobile-cases-list">
        @if($recentCases && $recentCases->count() > 0)
            @foreach($recentCases as $case)
            @php
                $st = strtolower($case['status'] ?? 'pending');
                $pillClass = 'status-pill-slate';
                if (in_array($st, ['active', 'in review', 'step 1 approved'])) {
                    $pillClass = 'status-pill-blue';
                } elseif (in_array($st, ['resolved', 'completed', 'released'])) {
                    $pillClass = 'status-pill-emerald';
                } elseif (in_array($st, ['pending', 'pending review'])) {
                    $pillClass = 'status-pill-amber';
                } elseif (in_array($st, ['overdue', 'rejected', 'archived'])) {
                    $pillClass = 'status-pill-rose';
                }
            @endphp
            <div class="mobile-case-card">
                <div class="mobile-case-card-head">
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $case['client'] }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $case['service'] }} • {{ $case['officer'] }}</div>
                    </div>
                    <span class="status-pill {{ $pillClass }}">{{ $case['status'] }}</span>
                </div>
                <div class="mobile-case-card-body">
                    <span>Updated {{ $case['updated'] }}</span>
                    <a href="{{ $case['url'] ?? '#' }}" class="table-action-btn">
                        <span>Details</span>
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-6 text-slate-400 text-sm">
                No recent casework activity recorded yet.
            </div>
        @endif
    </div>
</div>

{{-- Executive Report Generator Studio Panel --}}
<div class="report-studio-card" id="reportStudioSection">
    <div class="section-head mb-4">
        <div>
            <h3 class="section-title">
                <i data-lucide="file-check-2"></i> Executive Report Generator &amp; Analytics
            </h3>
            <p class="section-subtitle">Produce official weekly, monthly, quarterly, and yearly welfare reports with live metrics and PDF export</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.social-case.cases') }}" class="btn-pill" title="Jump to Social Cases">
                <i data-lucide="file-text" class="w-4 h-4 text-indigo-600"></i> Social Cases
            </a>
            <a href="{{ route('admin.senior') }}" class="btn-pill" title="Jump to Senior Records">
                <i data-lucide="heart" class="w-4 h-4 text-amber-600"></i> Senior Records
            </a>
            <a href="{{ url('/admin/financial/dashboard') }}" class="btn-pill btn-pill-primary" title="Jump to Financial Portal">
                <i data-lucide="banknote" class="w-4 h-4"></i> Financial Portal
            </a>
        </div>
    </div>

    <!-- Controls Form -->
    <div class="report-controls-bar">
        <!-- Service Selector -->
        <div class="report-field-group">
            <label class="report-field-label" for="reportService">Service Division</label>
            <select id="reportService" class="report-select" onchange="onReportConfigChange()">
                <option value="all">Consolidated (All Services)</option>
                <option value="social_case" selected>Social Case Study</option>
                <option value="senior">Senior Citizen</option>
                <option value="financial">Financial Assistance</option>
            </select>
        </div>

        <!-- Period Selector -->
        <div class="report-field-group">
            <label class="report-field-label" for="reportPeriod">Reporting Period</label>
            <select id="reportPeriod" class="report-select" onchange="onPeriodTypeChange()">
                <option value="weekly">Weekly Report</option>
                <option value="monthly" selected>Monthly Report</option>
                <option value="quarterly">Quarterly Report</option>
                <option value="yearly">Yearly Report</option>
            </select>
        </div>

        <!-- Dynamic Period Pickers -->
        <!-- Weekly Picker -->
        <div class="report-field-group" id="wrapWeekPicker" style="display: none;">
            <label class="report-field-label" for="reportWeekDate">Week Anchor Date</label>
            <input type="date" id="reportWeekDate" class="report-input" value="{{ date('Y-m-d') }}" onchange="onReportConfigChange()">
        </div>

        <!-- Monthly Month Picker -->
        <div class="report-field-group" id="wrapMonthPicker">
            <label class="report-field-label" for="reportMonth">Month</label>
            <select id="reportMonth" class="report-select" onchange="onReportConfigChange()">
                @php $curM = (int) date('n'); @endphp
                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $idx => $mName)
                    <option value="{{ $idx + 1 }}" {{ ($idx + 1) === $curM ? 'selected' : '' }}>{{ $mName }}</option>
                @endforeach
            </select>
        </div>

        <!-- Quarterly Quarter Picker -->
        <div class="report-field-group" id="wrapQuarterPicker" style="display: none;">
            <label class="report-field-label" for="reportQuarter">Quarter</label>
            <select id="reportQuarter" class="report-select" onchange="onReportConfigChange()">
                @php $curQ = (int) ceil(date('n') / 3); @endphp
                <option value="1" {{ $curQ === 1 ? 'selected' : '' }}>Q1 (Jan – Mar)</option>
                <option value="2" {{ $curQ === 2 ? 'selected' : '' }}>Q2 (Apr – Jun)</option>
                <option value="3" {{ $curQ === 3 ? 'selected' : '' }}>Q3 (Jul – Sep)</option>
                <option value="4" {{ $curQ === 4 ? 'selected' : '' }}>Q4 (Oct – Dec)</option>
            </select>
        </div>

        <!-- Year Picker (Shared by Monthly, Quarterly, Yearly) -->
        <div class="report-field-group" id="wrapYearPicker">
            <label class="report-field-label" for="reportYear">Year</label>
            <select id="reportYear" class="report-select" onchange="onReportConfigChange()">
                @php $curY = (int) date('Y'); @endphp
                @for($y = $curY + 1; $y >= $curY - 3; $y--)
                    <option value="{{ $y }}" {{ $y === $curY ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="report-actions-wrap">
            <button type="button" class="btn-report-action btn-report-generate" id="btnRunReport" onclick="generateReport()">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Generate</span>
            </button>
            <a href="#" target="_blank" rel="opener" id="btnDownloadPdf" class="btn-report-action btn-report-pdf" title="Download Printable PDF">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>PDF</span>
            </a>
            <a href="#" target="_blank" rel="opener" id="btnPrintReport" class="btn-report-action btn-report-print" title="Open Print-Friendly View">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print</span>
            </a>
        </div>
    </div>

    <!-- Live Scope & Active Interval Banner -->
    <div class="report-scope-strip">
        <div class="report-scope-info">
            <span class="report-scope-pill" id="reportScopeBadge">Monthly Report</span>
            <span id="reportScopeTitle" class="font-bold text-slate-800">Social Case Study</span>
            <span class="text-slate-400">•</span>
            <span id="reportScopeRange" class="text-slate-600 font-normal">Loading range...</span>
        </div>
        <div class="text-xs text-slate-500" id="reportTimestamp">
            Status: Ready
        </div>
    </div>

    <!-- Output Container with Spinner Overlay -->
    <div style="position: relative; min-height: 200px;">
        <div id="reportLoadingSpinner" style="display:none; position: absolute; inset: 0; background: rgba(255,255,255,0.78); backdrop-filter: blur(2px); z-index: 20; border-radius: 12px; align-items: center; justify-content: center;">
            <div class="flex flex-col items-center gap-2">
                <div class="w-8 h-8 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs font-semibold text-slate-600">Aggregating report records...</span>
            </div>
        </div>

        <!-- Dynamic KPI Metric Tiles -->
        <div class="rep-grid" id="reportKpisContainer">
            <div class="rep-card">
                <div class="rep-title">Total Caseload Volume</div>
                <div class="rep-val" id="repStatVolume">—</div>
            </div>
            <div class="rep-card">
                <div class="rep-title">Pending / In Review</div>
                <div class="rep-val text-amber-600" id="repStatPending">—</div>
            </div>
            <div class="rep-card">
                <div class="rep-title">Completed / Released</div>
                <div class="rep-val text-emerald-600" id="repStatCompleted">—</div>
            </div>
            <div class="rep-card">
                <div class="rep-title">Total Assistance Disbursed</div>
                <div class="rep-val text-indigo-700" id="repStatDisbursed">—</div>
            </div>
        </div>

        <!-- 2-Column Breakdown: Categories & Top Barangays -->
        <div class="report-breakdown-grid">
            <!-- Program / Assistance Category Breakdown -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4 text-indigo-600"></i>
                    <span>Classification Breakdown</span>
                </h5>
                <div id="reportCategoryBreakdown" class="space-y-2">
                    <div class="text-xs text-slate-400">Loading breakdown data...</div>
                </div>
            </div>

            <!-- Top Barangays Served -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                    <span>Top Barangays Served</span>
                </h5>
                <div id="reportBarangayBreakdown" class="space-y-2">
                    <div class="text-xs text-slate-400">Loading barangay data...</div>
                </div>
            </div>
        </div>

        <!-- Detailed Registry Records Roster -->
        <div class="mt-4">
            <div class="flex items-center justify-between mb-2">
                <h5 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-slate-600"></i>
                    <span>Casework &amp; Client Registry (Recent in Period)</span>
                </h5>
                <span class="text-xs text-slate-500" id="reportRecordsCount">0 records listed</span>
            </div>
            <div class="dash-table-wrap" style="max-height: 380px; overflow-y: auto;">
                <table class="dash-table" style="font-size: 13px;">
                    <thead>
                        <tr>
                            <th style="width: 16%;">Control / Ref No.</th>
                            <th style="width: 26%;">Client / Beneficiary</th>
                            <th style="width: 18%;">Barangay</th>
                            <th style="width: 20%;">Category / Service</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%; text-align:right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="reportRecordsTbody">
                        <tr>
                            <td colspan="6" class="text-center py-6 text-slate-400">
                                Click "Generate Report" to populate records.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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

    // Lucide Icons Initialization
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        setTimeout(() => lucide.createIcons(), 120);
    }

    // Chart.js Global Default Settings
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Public Sans', -apple-system, BlinkMacSystemFont, sans-serif";
        Chart.defaults.color = '#64748B';
    }

    // 1. Social Case Study 12-Month Inflow Chart
    const socialCaseCtx = document.getElementById('socialCaseChart');
    if (socialCaseCtx) {
        @php
            $monthlyLabels = $monthlySocialCases['labels'] ?? [];
            $monthlyData = $monthlySocialCases['data'] ?? [];
        @endphp

        const scLabels = {!! json_encode($monthlyLabels) !!};
        const scData = {!! json_encode($monthlyData) !!};

        const ctx = socialCaseCtx.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(26, 35, 126, 0.28)');
        gradient.addColorStop(1, 'rgba(26, 35, 126, 0.01)');

        new Chart(socialCaseCtx, {
            type: 'line',
            data: {
                labels: scLabels,
                datasets: [{
                    label: 'New Cases',
                    data: scData,
                    borderColor: '#1A237E',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.38,
                    pointBackgroundColor: '#1A237E',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { stepSize: 1, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 2. Financial Assistance Pipeline Chart
    const financialCtx = document.getElementById('financialChart');
    if (financialCtx) {
        @php
            $finData = $serviceBreakdown['Financial Assistance'] ?? [];
        @endphp

        new Chart(financialCtx, {
            type: 'bar',
            data: {
                labels: ['Total Intakes', 'Pending Review', 'Step 1 Approved', 'Ready Step 2'],
                datasets: [{
                    label: 'Applications',
                    data: [
                        {{ $finData['active'] ?? 0 }}, 
                        {{ $finData['pending'] ?? 0 }}, 
                        {{ $finData['overdue'] ?? 0 }}, 
                        {{ $finData['completed'] ?? 0 }}
                    ],
                    backgroundColor: ['#1A237E', '#F59E0B', '#3B82F6', '#10B981'],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 3. Senior Citizen Demographics Chart
    const seniorCtx = document.getElementById('seniorChart');
    if (seniorCtx) {
        @php
            $senData = $serviceBreakdown['Senior Citizen'] ?? [];
        @endphp

        const activeCount = {{ $senData['pending'] ?? 0 }};
        const archivedCount = {{ $senData['overdue'] ?? 0 }};

        new Chart(seniorCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active Seniors', 'Archived / Inactive'],
                datasets: [{
                    data: [activeCount, archivedCount],
                    backgroundColor: ['#10B981', '#94A3B8'],
                    borderWidth: 3,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 10,
                        cornerRadius: 8
                    }
                }
            }
        });
    }

    // 4. Overall Service Distribution Chart
    const distributionCtx = document.getElementById('distributionChart');
    if (distributionCtx) {
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Social Case', 'Financial Asst.', 'Senior Citizen', 'VAWC', 'BCPC'],
                datasets: [{
                    data: [
                        {{ $serviceBreakdown['Social Case Study']['total'] ?? 0 }},
                        {{ $serviceBreakdown['Financial Assistance']['total'] ?? 0 }},
                        {{ $serviceBreakdown['Senior Citizen']['total'] ?? 0 }},
                        {{ $serviceBreakdown['VAWC']['total'] ?? 0 }},
                        {{ $serviceBreakdown['BCPC']['total'] ?? 0 }}
                    ],
                    backgroundColor: ['#1A237E', '#059669', '#D97706', '#E11D48', '#0891B2'],
                    borderWidth: 3,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 10,
                        cornerRadius: 8
                    }
                }
            }
        });
    }

    // ── Interactive Report Studio Engine ──
    window.onPeriodTypeChange = function () {
        const p = document.getElementById('reportPeriod').value;
        const wPicker = document.getElementById('wrapWeekPicker');
        const mPicker = document.getElementById('wrapMonthPicker');
        const qPicker = document.getElementById('wrapQuarterPicker');
        const yPicker = document.getElementById('wrapYearPicker');

        if (wPicker) wPicker.style.display = (p === 'weekly') ? 'flex' : 'none';
        if (mPicker) mPicker.style.display = (p === 'monthly') ? 'flex' : 'none';
        if (qPicker) qPicker.style.display = (p === 'quarterly') ? 'flex' : 'none';
        if (yPicker) yPicker.style.display = (p === 'weekly') ? 'none' : 'flex';

        updateReportExportUrls();
    };

    window.onReportConfigChange = function () {
        updateReportExportUrls();
    };

    function getReportQueryParams() {
        const s = document.getElementById('reportService') ? document.getElementById('reportService').value : 'social_case';
        const p = document.getElementById('reportPeriod') ? document.getElementById('reportPeriod').value : 'monthly';
        const params = new URLSearchParams({ service: s, period: p });

        if (p === 'weekly') {
            const w = document.getElementById('reportWeekDate');
            if (w && w.value) params.append('week_date', w.value);
        } else if (p === 'monthly') {
            const m = document.getElementById('reportMonth');
            const y = document.getElementById('reportYear');
            if (m) params.append('month', m.value);
            if (y) params.append('year', y.value);
        } else if (p === 'quarterly') {
            const q = document.getElementById('reportQuarter');
            const y = document.getElementById('reportYear');
            if (q) params.append('quarter', q.value);
            if (y) params.append('year', y.value);
        } else if (p === 'yearly') {
            const y = document.getElementById('reportYear');
            if (y) params.append('year', y.value);
        }

        return params.toString();
    }

    function updateReportExportUrls() {
        const qs = getReportQueryParams();
        const pdfBtn = document.getElementById('btnDownloadPdf');
        const printBtn = document.getElementById('btnPrintReport');

        if (pdfBtn) pdfBtn.href = "{{ route('admin.dashboard.reports.pdf') }}?" + qs;
        if (printBtn) printBtn.href = "{{ route('admin.dashboard.reports.print') }}?" + qs;
    }

    window.generateReport = function () {
        const spinner = document.getElementById('reportLoadingSpinner');
        const runBtn = document.getElementById('btnRunReport');
        if (spinner) spinner.style.display = 'flex';
        if (runBtn) runBtn.disabled = true;

        updateReportExportUrls();
        const qs = getReportQueryParams();

        fetch("{{ route('admin.dashboard.reports.data') }}?" + qs, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response failed');
            return response.json();
        })
        .then(res => {
            if (!res.success || !res.data) throw new Error('Invalid payload');
            const data = res.data;
            renderReportOutput(data);
        })
        .catch(err => {
            console.error('Report Generation Error:', err);
            const tbody = document.getElementById('reportRecordsTbody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-6 text-rose-500 font-semibold">Unable to aggregate report records. Please try again.</td></tr>';
            }
        })
        .finally(() => {
            if (spinner) spinner.style.display = 'none';
            if (runBtn) runBtn.disabled = false;
        });
    };

    function renderReportOutput(data) {
        // Scope Strip
        const scopeBadge = document.getElementById('reportScopeBadge');
        const scopeTitle = document.getElementById('reportScopeTitle');
        const scopeRange = document.getElementById('reportScopeRange');
        const timestamp = document.getElementById('reportTimestamp');

        if (scopeBadge) scopeBadge.textContent = (data.range.period || 'Report').toUpperCase();
        if (scopeTitle) scopeTitle.textContent = data.serviceTitle || 'MSWDO Division';
        if (scopeRange) scopeRange.textContent = (data.range.label || '') + ' (' + (data.range.formattedRange || '') + ')';
        if (timestamp) timestamp.textContent = 'Updated: ' + (new Date()).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});

        // KPI Numbers
        const vol = document.getElementById('repStatVolume');
        const pnd = document.getElementById('repStatPending');
        const cmp = document.getElementById('repStatCompleted');
        const dsb = document.getElementById('repStatDisbursed');

        const stats = data.stats || {};
        if (vol) vol.textContent = Number(stats.totalVolume || 0).toLocaleString();

        if (pnd) {
            if (stats.pendingVolume !== undefined) {
                pnd.textContent = Number(stats.pendingVolume).toLocaleString();
            } else if (stats.activeSeniors !== undefined) {
                pnd.textContent = Number(stats.activeSeniors).toLocaleString() + ' Active';
            } else {
                pnd.textContent = '0';
            }
        }

        if (cmp) {
            if (stats.completedVolume !== undefined) {
                cmp.textContent = Number(stats.completedVolume).toLocaleString();
            } else if (stats.payoutBeneficiaries !== undefined) {
                cmp.textContent = Number(stats.payoutBeneficiaries).toLocaleString() + ' Paid';
            } else {
                cmp.textContent = '0';
            }
        }

        if (dsb) {
            const amt = Number(stats.totalAmount || 0);
            dsb.textContent = '₱' + amt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Category Breakdown
        const catBox = document.getElementById('reportCategoryBreakdown');
        if (catBox) {
            const categories = (data.breakdowns && data.breakdowns.categories) ? data.breakdowns.categories : {};
            const keys = Object.keys(categories);
            if (keys.length === 0) {
                catBox.innerHTML = '<div class="text-xs text-slate-400 py-2">No category data recorded in this interval.</div>';
            } else {
                let catHtml = '';
                const total = Object.values(categories).reduce((a, b) => a + b, 0) || 1;
                keys.forEach(k => {
                    const cnt = categories[k];
                    const pct = ((cnt / total) * 100).toFixed(1);
                    catHtml += `
                        <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100 last:border-0">
                            <span class="font-semibold text-slate-700 truncate max-w-[200px]" title="${k}">${k}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500 font-mono">${cnt.toLocaleString()}</span>
                                <span class="bg-indigo-50 text-indigo-700 font-bold px-1.5 py-0.5 rounded text-[10px]">${pct}%</span>
                            </div>
                        </div>
                    `;
                });
                catBox.innerHTML = catHtml;
            }
        }

        // Barangay Breakdown
        const brgyBox = document.getElementById('reportBarangayBreakdown');
        if (brgyBox) {
            const barangays = (data.breakdowns && data.breakdowns.barangays) ? data.breakdowns.barangays : {};
            const bKeys = Object.keys(barangays);
            if (bKeys.length === 0) {
                brgyBox.innerHTML = '<div class="text-xs text-slate-400 py-2">No barangay distribution recorded.</div>';
            } else {
                let brgyHtml = '';
                bKeys.forEach(b => {
                    const cnt = barangays[b];
                    brgyHtml += `
                        <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100 last:border-0">
                            <span class="font-medium text-slate-700 truncate max-w-[200px]">${b}</span>
                            <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded text-[11px]">${cnt.toLocaleString()} served</span>
                        </div>
                    `;
                });
                brgyBox.innerHTML = brgyHtml;
            }
        }

        // Records Table
        const tbody = document.getElementById('reportRecordsTbody');
        const countSpan = document.getElementById('reportRecordsCount');
        const records = data.records || [];

        if (countSpan) countSpan.textContent = records.length + ' records listed';

        if (tbody) {
            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-slate-400">No casework records found matching this timeframe.</td></tr>';
            } else {
                let rows = '';
                records.forEach(r => {
                    const amtStr = r.amount > 0 ? ('₱' + Number(r.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})) : '—';
                    let stBadge = 'bg-slate-100 text-slate-700';
                    const stLower = (r.status || '').toLowerCase();
                    if (stLower.includes('active') || stLower.includes('approved') || stLower.includes('ready')) {
                        stBadge = 'bg-blue-50 text-blue-700';
                    } else if (stLower.includes('resolved') || stLower.includes('completed') || stLower.includes('claimed') || stLower.includes('released')) {
                        stBadge = 'bg-emerald-50 text-emerald-700';
                    } else if (stLower.includes('pending')) {
                        stBadge = 'bg-amber-50 text-amber-700';
                    }

                    rows += `
                        <tr>
                            <td class="font-mono font-bold text-slate-800 text-xs">${r.ref || '—'}</td>
                            <td class="font-semibold text-slate-900">${r.name || 'Beneficiary'}</td>
                            <td class="text-slate-600 text-xs">${r.barangay || 'N/A'}</td>
                            <td class="text-slate-600 text-xs">${r.category || 'General'}</td>
                            <td><span class="px-2 py-0.5 rounded-full text-[11px] font-semibold ${stBadge}">${r.status || 'Active'}</span></td>
                            <td class="text-right font-semibold text-slate-800 text-xs">${amtStr}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = rows;
            }
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Auto-initialize Report Studio on load
    updateReportExportUrls();
    generateReport();
});
</script>
@endpush