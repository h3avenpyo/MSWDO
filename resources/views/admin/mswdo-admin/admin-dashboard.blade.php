@extends('admin.mswdo-admin.layout')
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

    /* ── Dashboard Header Banner (MSWDO Red Gradient Theme) ── */
    .dash-banner {
        background: linear-gradient(135deg, #b11116 0%, #c4181e 50%, #8e0d11 100%) !important;
        border-radius: 18px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 10px 25px -5px rgba(177, 17, 22, 0.25) !important;
        border: 1px solid rgba(255, 255, 255, 0.12);
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
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255,255,255,0) 70%);
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
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr));
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
        min-width: 0;
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
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr));
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
        min-width: 0;
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
    .stat-tile-val.val-success { color: #0F172A; }
    .stat-tile-val.val-info { color: #0F172A; }
    .stat-tile-val.val-warning { color: #0F172A; }
    .stat-tile-val.val-danger { color: #0F172A; }
    .stat-tile-val.val-neutral { color: #0F172A; }

    /* Themed Accents for Service Cards - All using MSWDO Red */
    .card-theme-indigo .service-avatar { background: #FEF2F2; color: #B11116; }
    .card-theme-indigo .service-badge { background: #FEF2F2; color: #B11116; }
    .card-theme-emerald .service-avatar { background: #FEF2F2; color: #B11116; }
    .card-theme-emerald .service-badge { background: #FEF2F2; color: #B11116; }
    .card-theme-amber .service-avatar { background: #FEF2F2; color: #B11116; }
    .card-theme-amber .service-badge { background: #FEF2F2; color: #B11116; }
    .card-theme-rose .service-avatar { background: #FEF2F2; color: #B11116; }
    .card-theme-rose .service-badge { background: #FEF2F2; color: #B11116; }
    .card-theme-cyan .service-avatar { background: #FEF2F2; color: #B11116; }
    .card-theme-cyan .service-badge { background: #FEF2F2; color: #B11116; }
    .card-theme-red .service-avatar { background: #FEF2F2; color: #B11116; }
    .card-theme-red .service-badge { background: #FEF2F2; color: #B11116; }

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
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr));
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
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
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
        height: 320px;
        width: 100%;
        min-width: 0;
        max-width: 100%;
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

    /* ── Mobile Stacked Case Cards (<768px) ── */
    .mobile-cases-list { display: none; }
    @media (max-width: 767.98px) {
        .panel-box .dash-table-wrap { display: none; }
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
    .report-studio-card .section-title i,
    .report-studio-card .section-title svg {
        color: #b11116;
    }
    .report-select:focus, .report-input:focus {
        border-color: #b11116;
        box-shadow: 0 0 0 3px rgba(177, 17, 22, 0.12);
    }
    .report-actions-wrap {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: nowrap;
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
        background: #b11116;
        color: #FFFFFF;
        border-color: #b11116;
        flex: 1;
        min-width: 130px;
    }
    .btn-report-generate:hover {
        background: #8A0D11;
        border-color: #8A0D11;
        color: #FFFFFF;
    }
    .btn-report-pdf {
        background: #FEF2F2;
        color: #b11116;
        border-color: #FECACA;
    }
    .btn-report-pdf:hover {
        background: #FEE2E2;
        color: #8A0D11;
        border-color: #F87171;
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
        background: #FEF2F2;
        border: 1px solid #FECACA;
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
        color: #8A0D11;
        font-weight: 600;
    }
    .report-scope-pill {
        background: #b11116;
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
        background: #b11116;
        color: #FFFFFF;
        border-color: #b11116;
    }
    .btn-pill-primary:hover {
        background: #8A0D11;
        color: #FFFFFF;
        border-color: #8A0D11;
    }

    .report-head-links {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* ── Tablet Viewports (768px - 1199.98px) ── */
    @media (min-width: 768px) and (max-width: 1199.98px) {
        .kpi-strip {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .services-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.15rem;
        }
        .analytics-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.15rem;
        }
        .chart-canvas-box {
            height: 280px;
        }
        .report-controls-bar {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem;
        }
        .report-actions-wrap {
            grid-column: span 2;
            justify-content: flex-end;
            margin-top: 0.25rem;
        }
        .report-actions-wrap .btn-report-generate {
            flex: initial;
            min-width: 130px;
        }
        .report-breakdown-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* ── Mobile Viewports (< 768px) ── */
    @media (max-width: 767.98px) {
        .dash-banner {
            padding: 1.15rem 1.15rem !important;
            border-radius: 14px !important;
            margin-bottom: 1.25rem !important;
        }
        .dash-banner-title {
            font-size: 1.25rem !important;
            line-height: 1.25 !important;
        }
        .dash-banner-sub {
            font-size: 0.825rem !important;
        }
        .kpi-strip {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .kpi-card {
            padding: 0.85rem;
            gap: 0.65rem;
            border-radius: 12px;
        }
        .kpi-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }
        .kpi-icon-box svg {
            width: 20px;
            height: 20px;
        }
        .kpi-label {
            font-size: 0.7rem;
            margin-bottom: 0.15rem;
        }
        .kpi-val {
            font-size: 1.25rem;
        }
        .kpi-hint {
            font-size: 0.68rem;
            margin-top: 0.15rem;
        }
        .section-head {
            margin-bottom: 0.85rem;
            gap: 0.5rem;
        }
        .section-title {
            font-size: 1.1rem;
        }
        .section-subtitle {
            font-size: 0.78rem;
        }
        .services-grid {
            grid-template-columns: 1fr;
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .service-card {
            padding: 1.1rem;
            border-radius: 14px;
        }
        .service-metrics-grid {
            gap: 0.5rem;
        }
        .service-stat-tile {
            padding: 0.65rem 0.75rem;
            min-height: 64px;
        }
        .stat-tile-label {
            font-size: 0.65rem;
        }
        .stat-tile-val {
            font-size: 1.15rem;
        }
        .analytics-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .chart-panel {
            padding: 1rem;
            border-radius: 14px;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
        }
        .chart-canvas-box {
            height: 250px;
            min-width: 0;
            max-width: 100%;
        }
        .panel-box {
            padding: 1rem;
            border-radius: 14px;
            margin-bottom: 1.25rem;
        }
        .panel-box .dash-table-wrap {
            display: none !important;
        }
        .report-studio-card .dash-table-wrap {
            display: block !important;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .mobile-cases-list {
            display: flex !important;
            flex-direction: column;
            gap: 0.75rem;
        }
        .report-studio-card {
            padding: 1rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
        }
        .report-head-links {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .report-head-links .btn-pill {
            flex: 1 1 calc(50% - 0.5rem);
            justify-content: center;
            font-size: 0.75rem;
            padding: 0.4rem 0.5rem;
        }
        .report-head-links .btn-pill-primary {
            flex: 1 1 100%;
        }
        .report-controls-bar {
            grid-template-columns: 1fr;
            padding: 0.85rem;
            gap: 0.75rem;
            border-radius: 12px;
        }
        .report-actions-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            width: 100%;
            margin-top: 0.25rem;
        }
        .btn-report-generate {
            grid-column: span 2;
            width: 100% !important;
            height: 42px !important;
            font-size: 0.875rem !important;
        }
        .btn-report-pdf,
        .btn-report-print {
            width: 100% !important;
            height: 38px !important;
        }
        .report-scope-strip {
            padding: 0.65rem 0.75rem;
            gap: 0.4rem;
        }
        .report-scope-info {
            flex-wrap: wrap;
            gap: 0.35rem;
            font-size: 0.775rem;
        }
        .rep-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.65rem;
        }
        .rep-card {
            padding: 0.85rem;
        }
        .rep-title {
            font-size: 0.68rem;
        }
        .rep-val {
            font-size: 1.2rem;
        }
        .report-breakdown-grid {
            grid-template-columns: 1fr;
            gap: 0.85rem;
        }
    }

    /* ── Extra Small Phones (< 480px) ── */
    @media (max-width: 480px) {
        .kpi-strip {
            grid-template-columns: 1fr;
            gap: 0.65rem;
        }
        .rep-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

{{-- Page Header Banner --}}
<header class="dash-banner flex flex-col sm:flex-row sm:items-center justify-between gap-3 select-none">
    <div>
        <div class="dash-banner-title">Welcome back, {{ $adminName }}</div>
        <div class="dash-banner-sub">
            <span>MSWDO Silang Portal</span>
        </div>
    </div>
    <div class="flex items-center gap-3 self-start sm:self-auto">
        <div class="text-right">
            <div class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-white/70">Philippine Standard Time</div>
            <div class="text-xs sm:text-sm font-semibold text-white tracking-wide" id="liveClock">Loading date...</div>
        </div>
        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-white/20 border-2 border-white/40 text-white font-bold text-sm flex items-center justify-center shadow-inner cursor-default flex-shrink-0" title="Logged in as: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

{{-- Session Success Notification --}}
@if(session('success'))
    <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between gap-3 shadow-sm">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 bg-transparent border-0 cursor-pointer font-bold text-base leading-none px-2">&times;</button>
    </div>
@endif

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
                <i data-lucide="pie-chart"></i> Senior Citizens per Barangay
            </h4>
        </div>
        <div class="chart-canvas-box">
            <canvas id="seniorChart"></canvas>
        </div>
    </div>

    <!-- Chart 5: VAWC Category Distribution -->
    <div class="chart-panel">
        <div class="chart-panel-header">
            <h4 class="chart-panel-title">
                <i data-lucide="shield-alert"></i> VAWC Case Categories
            </h4>
        </div>
        <div class="chart-canvas-box">
            <canvas id="vawcChart"></canvas>
        </div>
    </div>

    <!-- Chart 6: BCPC Category Distribution -->
    <div class="chart-panel">
        <div class="chart-panel-header">
            <h4 class="chart-panel-title">
                <i data-lucide="smile"></i> BCPC Case Categories
            </h4>
        </div>
        <div class="chart-canvas-box">
            <canvas id="bcpcChart"></canvas>
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
            <p class="section-subtitle">Showing the 10 latest registered or updated beneficiaries across services</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                10 Latest
            </span>
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
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @if($recentCases && $recentCases->count() > 0)
                    @foreach($recentCases->take(10) as $case)
                    @php
                        $cName = $case['client'] ?? 'Unknown Client';
                        $cWords = explode(' ', trim($cName));
                        $cInit = count($cWords) >= 2
                            ? strtoupper(substr($cWords[0],0,1).substr($cWords[1],0,1))
                            : strtoupper(substr($cName,0,2));
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
                            <span class="text-xs text-slate-500 font-medium">{{ $case['updated'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="padding:2.5rem; text-align:center; color:#94A3B8;">
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
            @foreach($recentCases->take(10) as $case)
            <div class="mobile-case-card">
                <div class="mobile-case-card-head">
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $case['client'] }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $case['service'] }} • {{ $case['officer'] }}</div>
                    </div>
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
            <p class="section-subtitle">Produce official weekly, monthly, quarterly, and yearly welfare reports per service division with live metrics and PDF export</p>
        </div>
        <div class="report-head-links">
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
                <option value="bcpc">BCPC (Child Protection)</option>
                <option value="vawc">VAWC (Women &amp; Children)</option>
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
                <option value="1" {{ $curQ === 1 ? 'selected' : '' }}>Q1 (Jan - Mar)</option>
                <option value="2" {{ $curQ === 2 ? 'selected' : '' }}>Q2 (Apr - Jun)</option>
                <option value="3" {{ $curQ === 3 ? 'selected' : '' }}>Q3 (Jul - Sep)</option>
                <option value="4" {{ $curQ === 4 ? 'selected' : '' }}>Q4 (Oct - Dec)</option>
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
            <span class="text-slate-400">&bull;</span>
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
                <div class="w-8 h-8 border-3 border-[#b11116] border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs font-semibold text-slate-600">Aggregating report records...</span>
            </div>
        </div>

        <!-- Dynamic KPI Metric Tiles (labels adapt per selected service) -->
        <div class="rep-grid" id="reportKpisContainer">
            <div class="rep-card">
                <div class="rep-title">Awaiting Report</div>
                <div class="rep-val">&mdash;</div>
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
                <table class="dash-table" style="font-size: 13px; min-width: 580px;">
                    <thead>
                        <tr>
                            <th style="width: 16%;">Control / Ref No.</th>
                            <th style="width: 26%;">Client / Beneficiary</th>
                            <th style="width: 18%;">Barangay</th>
                            <th style="width: 20%;">Category / Service</th>
                            <th style="width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="reportRecordsTbody">
                        <tr>
                            <td colspan="5" class="text-center py-6 text-slate-400">
                                Click "Generate" to populate records.
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
@php
    // Prepare chart data in PHP to avoid issues in JavaScript
    $monthlyLabels = $monthlySocialCases['labels'] ?? [];
    $monthlyData = $monthlySocialCases['data'] ?? [];
    $finData = $serviceBreakdown['Financial Assistance'] ?? [];
    $seniorData = $serviceBreakdown['Senior Citizen'] ?? [];
    $barangayLabels = $barangayStats['labels'] ?? [];
    $barangayData = $barangayStats['data'] ?? [];
    $vawcCategories = $serviceBreakdown['VAWC']['categories'] ?? [];
    if (empty($vawcCategories)) {
        $vawcCategories = [
            'Active Cases' => $serviceBreakdown['VAWC']['active'] ?? 0,
            'Pending Action' => $serviceBreakdown['VAWC']['pending'] ?? 0,
            'Resolved' => $serviceBreakdown['VAWC']['completed'] ?? 0
        ];
    }
    $bcpcCategories = $serviceBreakdown['BCPC']['categories'] ?? [];
    if (empty($bcpcCategories)) {
        $bcpcCategories = [
            'Active Cases' => $serviceBreakdown['BCPC']['active'] ?? 0,
            'Monitorings' => $serviceBreakdown['BCPC']['pending'] ?? 0,
            'Resolved' => $serviceBreakdown['BCPC']['completed'] ?? 0
        ];
    }
@endphp
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
        const scLabels = @json($monthlyLabels);
        const scData = @json($monthlyData);

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
        const seniorLabels = @json($barangayLabels);
        const seniorData = @json($barangayData);

        new Chart(seniorCtx, {
            type: 'doughnut',
            data: {
                labels: seniorLabels.length > 0 ? seniorLabels : ['No Data'],
                datasets: [{
                    data: seniorData.length > 0 ? seniorData : [1],
                    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4'],
                    borderWidth: 3,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 8,
                            usePointStyle: true,
                            font: { size: 10 },
                            boxWidth: 8,
                            padding: 8
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

    // 5. VAWC Case Distribution Chart
    const vawcCtx = document.getElementById('vawcChart');
    if (vawcCtx) {
        const vawcCategories = @json($vawcCategories);
        const vawcLabels = Object.keys(vawcCategories);
        const vawcData = Object.values(vawcCategories);

        new Chart(vawcCtx, {
            type: 'doughnut',
            data: {
                labels: vawcLabels.length > 0 ? vawcLabels : ['No Data'],
                datasets: [{
                    data: vawcData.length > 0 ? vawcData : [1],
                    backgroundColor: ['#B11116', '#F97316', '#22C55E', '#3B82F6', '#8B5CF6', '#EC4899'],
                    borderWidth: 3,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 8,
                            usePointStyle: true,
                            font: { size: 10 },
                            boxWidth: 8,
                            padding: 8
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

    // 6. BCPC Case Distribution Chart
    const bcpcCtx = document.getElementById('bcpcChart');
    if (bcpcCtx) {
        const bcpcCategories = @json($bcpcCategories);
        const bcpcLabels = Object.keys(bcpcCategories);
        const bcpcData = Object.values(bcpcCategories);

        new Chart(bcpcCtx, {
            type: 'doughnut',
            data: {
                labels: bcpcLabels.length > 0 ? bcpcLabels : ['No Data'],
                datasets: [{
                    data: bcpcData.length > 0 ? bcpcData : [1],
                    backgroundColor: ['#B11116', '#F97316', '#22C55E', '#3B82F6', '#8B5CF6', '#EC4899'],
                    borderWidth: 3,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 8,
                            usePointStyle: true,
                            font: { size: 10 },
                            boxWidth: 8,
                            padding: 8
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
            renderReportOutput(res.data);
        })
        .catch(err => {
            console.error('Report Generation Error:', err);
            const tbody = document.getElementById('reportRecordsTbody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-6 text-rose-500 font-semibold">Unable to aggregate report records. Please try again.</td></tr>';
            }
        })
        .finally(() => {
            if (spinner) spinner.style.display = 'none';
            if (runBtn) runBtn.disabled = false;
        });
    };

    // ── Per-service KPI card definitions ──
    // Each card reads a stat key from the report payload and is only shown
    // when it is relevant to the selected service division.
    const REPORT_CARD_CONFIG = {
        social_case: [
            { title: 'Total Case Volume', key: 'totalVolume', fmt: 'number' },
            { title: 'Pending / In Review', key: 'pendingVolume', fmt: 'number', color: 'text-amber-600' },
            { title: 'Completed / Released', key: 'completedVolume', fmt: 'number', color: 'text-emerald-600' }
        ],
        senior: [
            { title: 'New Registrations', key: 'totalVolume', fmt: 'number' },
            { title: 'Active Senior Citizens', key: 'activeSeniors', fmt: 'number', color: 'text-sky-600' },
            { title: 'Birthday Cash Recipients', key: 'payoutBeneficiaries', fmt: 'number', color: 'text-emerald-600' },
            { title: 'Total Cash Subsidies', key: 'totalAmount', fmt: 'currency' }
        ],
        financial: [
            { title: 'Total Intake Volume', key: 'totalVolume', fmt: 'number' },
            { title: 'Pending Assessment', key: 'pendingVolume', fmt: 'number', color: 'text-amber-600' },
            { title: 'Approved Assistance', key: 'completedVolume', fmt: 'number', color: 'text-emerald-600' },
            { title: 'Assistance Granted', key: 'totalAmount', fmt: 'currency' }
        ],
        bcpc: [
            { title: 'Total BCPC Cases', key: 'totalVolume', fmt: 'number' },
            { title: 'Active / Open Cases', key: 'pendingVolume', fmt: 'number', color: 'text-amber-600' },
            { title: 'Resolved / Closed', key: 'completedVolume', fmt: 'number', color: 'text-emerald-600' }
        ],
        vawc: [
            { title: 'Total VAWC Cases', key: 'totalVolume', fmt: 'number' },
            { title: 'Active / Open Cases', key: 'pendingVolume', fmt: 'number', color: 'text-amber-600' },
            { title: 'Resolved / Closed', key: 'completedVolume', fmt: 'number', color: 'text-emerald-600' }
        ],
        all: [
            { title: 'Total Beneficiaries', key: 'totalVolume', fmt: 'number' },
            { title: 'Social Case Studies', key: 'socialCaseCases', fmt: 'number', color: 'text-indigo-600' },
            { title: 'Senior Registrations', key: 'seniorRegistrations', fmt: 'number', color: 'text-sky-600' },
            { title: 'Financial Intakes', key: 'financialIntakes', fmt: 'number', color: 'text-amber-600' },
            { title: 'BCPC Cases', key: 'bcpcCases', fmt: 'number', color: 'text-rose-600' },
            { title: 'VAWC Cases', key: 'vawcCases', fmt: 'number', color: 'text-rose-600' },
            { title: 'Total Assistance Disbursed', key: 'totalAmount', fmt: 'currency', color: 'text-emerald-600' }
        ]
    };

    function renderReportOutput(data) {
        const scopeBadge = document.getElementById('reportScopeBadge');
        const scopeTitle = document.getElementById('reportScopeTitle');
        const scopeRange = document.getElementById('reportScopeRange');
        const timestamp = document.getElementById('reportTimestamp');

        if (scopeBadge) scopeBadge.textContent = (data.range.period || 'Report').toUpperCase();
        if (scopeTitle) scopeTitle.textContent = data.serviceTitle || 'MSWDO Division';
        if (scopeRange) scopeRange.textContent = (data.range.label || '') + ' (' + (data.range.formattedRange || '') + ')';
        if (timestamp) timestamp.textContent = 'Updated: ' + (new Date()).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});

        // KPI Tiles — labels, values and count adapt to the selected service
        const stats = data.stats || {};
        const kpiBox = document.getElementById('reportKpisContainer');
        if (kpiBox) {
            const config = REPORT_CARD_CONFIG[data.service] || REPORT_CARD_CONFIG.social_case;
            let kpiHtml = '';
            config.forEach(card => {
                const raw = stats[card.key];
                let val;
                if (raw === undefined || raw === null) {
                    val = '&mdash;';
                } else if (card.fmt === 'currency') {
                    val = '₱' + Number(raw).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                } else {
                    val = Number(raw).toLocaleString();
                }
                kpiHtml += `
                    <div class="rep-card">
                        <div class="rep-title">${card.title}</div>
                        <div class="rep-val ${card.color || ''}">${val}</div>
                    </div>
                `;
            });
            kpiBox.innerHTML = kpiHtml;
        }

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
                                <span class="bg-[#FDF2F2] text-[#B11116] font-bold px-1.5 py-0.5 rounded text-[10px]">${pct}%</span>
                            </div>
                        </div>
                    `;
                });
                catBox.innerHTML = catHtml;
            }
        }

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

        const tbody = document.getElementById('reportRecordsTbody');
        const countSpan = document.getElementById('reportRecordsCount');
        const records = data.records || [];

        if (countSpan) countSpan.textContent = records.length + ' records listed';

        if (tbody) {
            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-slate-400">No casework records found matching this timeframe.</td></tr>';
            } else {
                let rows = '';
                records.forEach(r => {
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

    // Lucide Icons Initialization
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        setTimeout(() => lucide.createIcons(), 120);
    }
});
</script>
@endpush
