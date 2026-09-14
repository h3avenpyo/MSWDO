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
    .mobile-card-status {
        display: none;
    }

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
        #onlineRequestsTable tbody {
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
            display: flex !important;
            flex-direction: column !important;
            position: relative !important;
            box-sizing: border-box !important;
            width: 100% !important;
            max-width: 100% !important;
            background: #ffffff !important;
            border: 1px solid #D1D5DB !important;
            border-radius: 12px !important;
            margin-bottom: 12px !important;
            padding: 14px 16px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease !important;
        }
        #onlineRequestsTable tbody tr:not(.empty-row):last-child {
            margin-bottom: 0 !important;
        }
        #onlineRequestsTable tbody tr:not(.empty-row):hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
        }
        
        #onlineRequestsTable tbody td {
            display: grid !important;
            grid-template-columns: 110px minmax(0, 1fr) !important;
            align-items: start !important;
            gap: 16px !important;
            padding: 9px 0 !important;
            border: none !important;
            border-bottom: 1px solid #F1F5F9 !important;
            font-size: 0.82rem !important;
            overflow-wrap: anywhere !important;
            text-align: left !important;
            box-sizing: border-box !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            white-space: normal !important;
        }
        #onlineRequestsTable tbody td:last-child {
            border-bottom: none !important;
        }
        #onlineRequestsTable tbody td::before {
            content: attr(data-label) !important;
            font-weight: 600 !important;
            font-size: 0.72rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.03em !important;
            color: #64748B !important;
            text-align: left !important;
            line-height: 1.5 !important;
            grid-column: 1 !important;
            flex-shrink: 0 !important;
            display: block !important;
        }
        #onlineRequestsTable tbody td .req-val-wrap {
            text-align: left !important;
            min-width: 0 !important;
            color: #1E293B !important;
        }

        /* ── Client Name Header ── */
        #onlineRequestsTable tbody td[data-label="Name"] {
            order: -3 !important;
            display: block !important;
            padding: 2px 105px 10px 0 !important;
            border-bottom: none !important;
            font-size: 1.02rem !important;
            font-weight: 700 !important;
            line-height: 1.35 !important;
            color: #1A237E !important;
        }
        #onlineRequestsTable tbody td[data-label="Name"]::before {
            display: none !important;
        }
        #onlineRequestsTable tbody td[data-label="Name"] .req-val-wrap > div:first-child {
            font-size: 1.02rem !important;
            font-weight: 700 !important;
            color: #1A237E !important;
            line-height: 1.35 !important;
        }
        #onlineRequestsTable tbody td[data-label="Name"] .req-val-wrap > div.text-xs {
            font-size: 0.78rem !important;
            color: #64748B !important;
            font-weight: 400 !important;
            margin-top: 2px !important;
        }

        /* ── Top-Right Status Badge Pill ── */
        .mobile-card-status {
            position: absolute !important;
            top: 14px !important;
            right: 14px !important;
            display: inline-flex !important;
            align-items: center !important;
            padding: 4px 10px !important;
            border-radius: 999px !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.03em !important;
            text-transform: uppercase !important;
            line-height: 1 !important;
            z-index: 2 !important;
        }
        .mobile-card-status.status-pending {
            background: #FEF3C7 !important;
            color: #92400E !important;
            border: 1px solid #FDE68A !important;
        }
        .mobile-card-status.status-accepted {
            background: #DCFCE7 !important;
            color: #15803D !important;
            border: 1px solid #BBF7D0 !important;
        }
        .mobile-card-status.status-rejected {
            background: #FEE2E2 !important;
            color: #DC2626 !important;
            border: 1px solid #FECACA !important;
        }

        /* ── Contact (Divider below Header) ── */
        #onlineRequestsTable tbody td[data-label="Contact"] {
            order: -2 !important;
            border-top: 1px solid #E2E8F0 !important;
            padding-top: 12px !important;
            white-space: nowrap !important;
        }

        /* ── Action Buttons (Bottom Right) ── */
        #onlineRequestsTable tbody td[data-label="Action"] {
            order: 2 !important;
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
            padding: 12px 0 2px !important;
            border-bottom: none !important;
            margin-top: 2px !important;
            width: 100% !important;
        }
        #onlineRequestsTable tbody td[data-label="Action"]::before {
            display: none !important;
        }
        #onlineRequestsTable tbody td[data-label="Action"] .req-val-wrap {
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 6px !important;
            width: 100% !important;
        }
        #onlineRequestsTable tbody td[data-label="Action"] .btn {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            padding: 0 !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* ── Empty Row ── */
        #onlineRequestsTable tbody tr.empty-row {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        #onlineRequestsTable tbody tr.empty-row td.empty-cell {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
            padding: 2.5rem 1rem !important;
            border: none !important;
            width: 100% !important;
        }
        #onlineRequestsTable tbody tr.empty-row td::before {
            display: none !important;
        }
    }

    /* Small Screens (< 768px) */
    @media (max-width: 767.98px) {
        .sc-pagination {
            flex-direction: column !important;
            align-items: center !important;
            gap: 8px !important;
        }
        .sc-pagination-controls {
            justify-content: flex-end !important;
            padding-right: 20px !important;
        }
        .sc-pagination-info {
            text-align: center !important;
        }
    }

    /* Extra Small Devices (< 480px) */
    @media (max-width: 479px) {
        #onlineRequestsTable tbody tr:not(.empty-row) {
            padding: 12px 14px !important;
        }
        #onlineRequestsTable tbody td {
            font-size: 0.78rem !important;
            grid-template-columns: 90px minmax(0, 1fr) !important;
            gap: 12px !important;
            padding: 7px 0 !important;
        }
        #onlineRequestsTable tbody td::before {
            font-size: 0.68rem !important;
        }
        #onlineRequestsTable tbody td[data-label="Name"] {
            font-size: 0.95rem !important;
            padding-right: 85px !important;
        }
        .mobile-card-status {
            top: 12px !important;
            right: 12px !important;
            font-size: 0.68rem !important;
            padding: 3px 8px !important;
        }
        #onlineRequestsTable tbody td[data-label="Action"] .btn {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            padding: 0 !important;
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
            border: 2px solid #CBD5E1 !important;
            border-radius: 8px !important;
        }
        #onlineRequestsTable {
            min-width: 1000px !important;
            width: 100% !important;
            table-layout: auto !important;
            display: table !important;
        }
        #onlineRequestsTable thead {
            display: table-header-group !important;
        }
        #onlineRequestsTable thead th {
            padding: 12px 14px !important;
            font-size: 0.75rem !important;
        }
        #onlineRequestsTable tbody {
            display: table-row-group !important;
        }
        #onlineRequestsTable tbody tr {
            display: table-row !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        #onlineRequestsTable tbody tr:hover {
            background: #F8FAFC !important;
            transform: none !important;
            box-shadow: none !important;
        }
        #onlineRequestsTable tbody td {
            display: table-cell !important;
            padding: 12px 14px !important;
            font-size: 0.875rem !important;
            border-bottom: 1px solid #CBD5E1 !important;
            text-align: left !important;
            width: auto !important;
            max-width: none !important;
        }
        #onlineRequestsTable tbody td::before {
            display: none !important;
            content: none !important;
        }
        #onlineRequestsTable tbody tr.empty-row td.empty-cell {
            display: table-cell !important;
            white-space: normal !important;
            overflow: visible !important;
            max-width: none !important;
        }
        #onlineRequestsTable tbody td[data-label="Name"] {
            order: unset !important;
            display: table-cell !important;
            padding: 12px 14px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #0F172A !important;
        }
        #onlineRequestsTable tbody td[data-label="Name"] .req-val-wrap > div:first-child {
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #0F172A !important;
        }
        #onlineRequestsTable tbody td[data-label="Contact"] {
            order: unset !important;
            border-top: none !important;
            padding: 12px 14px !important;
        }
        #onlineRequestsTable tbody td[data-label="Action"] {
            order: unset !important;
            display: table-cell !important;
            padding: 12px 14px !important;
            margin-top: 0 !important;
            justify-content: unset !important;
        }
        .mobile-card-status {
            display: none !important;
        }
    }

    /* ── REDESIGNED ONLINE REQUEST VIEW MODAL STYLES ── */
    .or-modal-popup.swal2-popup {
        width: 780px !important;
        max-width: 95vw !important;
        border-radius: 20px !important;
        padding: 0 !important;
        overflow: hidden !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(15, 23, 42, 0.06) !important;
        font-family: inherit !important;
        border: none !important;
    }
    .or-modal-popup .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
        text-align: left !important;
        max-height: 84vh !important;
        overflow-y: auto !important;
    }
    .or-modal-popup .swal2-close {
        color: #FFFFFF !important;
        top: 16px !important;
        right: 18px !important;
        font-size: 24px !important;
        width: 32px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        background: rgba(255, 255, 255, 0.15) !important;
        transition: all 0.2s ease !important;
        outline: none !important;
        box-shadow: none !important;
    }
    .or-modal-popup .swal2-close:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        color: #FFFFFF !important;
    }
    .or-modal-popup .swal2-actions {
        margin: 0 !important;
        padding: 16px 24px !important;
        background: #F8FAFC !important;
        border-top: 1px solid #E2E8F0 !important;
        display: flex !important;
        justify-content: flex-end !important;
        gap: 10px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .or-modal-popup .swal2-actions button {
        margin: 0 !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
        font-size: 13.5px !important;
        padding: 10px 20px !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        font-family: inherit !important;
    }
    .or-modal-popup .swal2-actions .swal2-confirm {
        display: inline-flex !important;
    }
    .or-modal-popup .swal2-deny,
    .or-modal-popup .swal2-cancel,
    .or-modal-popup .swal2-actions .swal2-deny,
    .or-modal-popup .swal2-actions .swal2-cancel,
    .or-modal-popup button.swal2-deny,
    .or-modal-popup button.swal2-cancel,
    .or-modal-popup .swal2-actions button[style*="display: none"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        width: 0 !important;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .or-btn-close {
        background: #FFFFFF !important;
        color: #475569 !important;
        border: 1px solid #CBD5E1 !important;
    }
    .or-btn-close:hover {
        background: #F1F5F9 !important;
        color: #0F172A !important;
        border-color: #94A3B8 !important;
    }

    /* Modal Top Header */
    .or-modal-header {
        background: linear-gradient(135deg, #1A237E 0%, #283593 100%);
        color: #FFFFFF;
        padding: 22px 26px;
        position: relative;
    }
    .or-modal-header-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
        padding-right: 36px;
    }
    .or-modal-ref-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(4px);
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #E0E7FF;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .or-modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.01em;
    }
    .or-modal-subtitle {
        margin: 5px 0 0 0;
        font-size: 12.5px;
        color: #C7D2FE;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Modal Body Container */
    .or-modal-body {
        padding: 22px 24px;
        background: #F8FAFC;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Alert Banners */
    .or-alert-banner {
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 13px;
        line-height: 1.5;
    }
    .or-alert-banner.warning {
        background: #FEF3C7;
        border: 1px solid #FCD34D;
        color: #92400E;
    }
    .or-alert-banner.info {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        color: #1E40AF;
    }

    /* Content Cards */
    .or-card {
        background: #FFFFFF;
        border-radius: 14px;
        border: 1px solid #E2E8F0;
        padding: 16px 18px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .or-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #F1F5F9;
    }
    .or-card-title {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #1A237E;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .or-card-tag {
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        background: #EEF2FF;
        color: #3730A3;
        border: 1px solid #C7D2FE;
    }

    /* Grid Elements */
    .or-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 16px;
    }

    .or-field-label {
        font-size: 11px;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .or-field-val {
        font-size: 13.5px;
        font-weight: 600;
        color: #0F172A;
        word-break: break-word;
    }
    .or-field-val a {
        color: #1A237E;
        text-decoration: none;
        transition: color 0.15s;
    }
    .or-field-val a:hover {
        color: #2563EB;
        text-decoration: underline;
    }

    /* Situation Block */
    .or-situation-box {
        background: #F8FAFC;
        border-left: 4px solid #1A237E;
        border-radius: 0 10px 10px 0;
        padding: 14px 16px;
        font-size: 13.5px;
        line-height: 1.6;
        color: #1E293B;
        white-space: pre-wrap;
    }

    /* Attachments Grid */
    .or-attachments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 10px;
        margin-top: 4px;
    }
    .or-attachment-card {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
        text-decoration: none !important;
        color: inherit !important;
    }
    .or-attachment-card:hover {
        background: #FFFFFF;
        border-color: #1A237E;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 35, 126, 0.08);
    }
    .or-attachment-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .or-attachment-icon.pdf {
        background: #FEE2E2;
        color: #DC2626;
    }
    .or-attachment-icon.img {
        background: #E0E7FF;
        color: #4338CA;
    }
    .or-attachment-icon.doc {
        background: #DBEAFE;
        color: #1D4ED8;
    }
    .or-attachment-info {
        flex: 1;
        min-width: 0;
    }
    .or-attachment-name {
        font-size: 12.5px;
        font-weight: 600;
        color: #0F172A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .or-attachment-meta {
        font-size: 11px;
        color: #64748B;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Status Pills */
    .or-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .or-status-badge.pending {
        background: #FEF3C7;
        color: #92400E;
        border: 1px solid #FCD34D;
    }
    .or-status-badge.approved {
        background: #DCFCE7;
        color: #15803D;
        border: 1px solid #86EFAC;
    }
    .or-status-badge.rejected {
        background: #FEE2E2;
        color: #DC2626;
        border: 1px solid #FCA5A5;
    }

    @media (max-width: 640px) {
        .or-grid-2 {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }
        .or-attachments-grid {
            grid-template-columns: 1fr !important;
        }
        .or-modal-header {
            padding: 16px 18px !important;
        }
        .or-modal-body {
            padding: 16px !important;
            gap: 14px !important;
        }
        .or-modal-popup .swal2-actions {
            flex-direction: column !important;
            padding: 14px 16px !important;
        }
        .or-modal-popup .swal2-actions button {
            width: 100% !important;
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
        <img src="{{ asset('images/dswd.png') }}" style="width:48px;height:48px;object-fit:contain;flex-shrink:0;" alt="DSWD">
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
                            <span class="mobile-card-status status-rejected">Rejected</span>
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

function escapeHtml(str) {
    if (!str && str !== 0) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function viewOnlineRequest(id) {
    fetch(`/admin/social-case/online-requests/${id}`)
        .then(response => response.json())
        .then(data => {
            const statusLower = (data.raw_status || data.status || 'rejected').toLowerCase();
            let statusClass = 'rejected';
            let statusIcon = 'x-circle';

            // Warning Banner
            let warningBannerHtml = '';
            if (data.warning_recent) {
                warningBannerHtml = `
                    <div class="or-alert-banner warning">
                        <i data-lucide="alert-triangle" style="width: 22px; height: 22px; color: #D97706; flex-shrink: 0; margin-top: 2px;"></i>
                        <div>
                            <div style="font-weight: 700; color: #92400E; margin-bottom: 2px;">Recent Assistance Record (6-Month Rule)</div>
                            <div style="color: #78350F; font-size: 12.5px; line-height: 1.5;">
                                <strong>${escapeHtml(data.first_name)} ${escapeHtml(data.last_name)}</strong> has a case study or assistance record within the 6-month period.
                            </div>
                        </div>
                    </div>
                `;
            }

            // Attachments
            let attachmentsContent = '';
            if (data.attachments && data.attachments.length > 0) {
                const cards = data.attachments.map(att => {
                    const iconClass = att.is_pdf ? 'pdf' : (att.is_image ? 'img' : 'doc');
                    const iconLucide = att.is_pdf ? 'file-text' : (att.is_image ? 'image' : 'file');
                    return `
                        <a href="${att.file_url}" target="_blank" rel="noopener noreferrer" class="or-attachment-card" title="Click to open ${escapeHtml(att.file_name)}">
                            <div class="or-attachment-icon ${iconClass}">
                                <i data-lucide="${iconLucide}" style="width:18px;height:18px;"></i>
                            </div>
                            <div class="or-attachment-info">
                                <div class="or-attachment-name" title="${escapeHtml(att.file_name)}">${escapeHtml(att.file_name)}</div>
                                <div class="or-attachment-meta">
                                    <span>${escapeHtml(att.file_size)}</span>
                                    <span>•</span>
                                    <span style="color:#1A237E;font-weight:600;display:inline-flex;align-items:center;gap:3px;">
                                        Open <i data-lucide="external-link" style="width:10px;height:10px;"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    `;
                }).join('');
                attachmentsContent = `<div class="or-attachments-grid">${cards}</div>`;
            } else if (data.attachments_html && data.attachments_html.indexOf('<li') !== -1) {
                attachmentsContent = data.attachments_html;
            } else {
                attachmentsContent = `
                    <div style="padding: 16px; text-align: center; color: #64748B; background: #F8FAFC; border-radius: 10px; border: 1px dashed #CBD5E1; font-size: 13px;">
                        <i data-lucide="file-x" style="width: 22px; height: 22px; margin: 0 auto 6px; display: block; color: #94A3B8;"></i>
                        No supporting documents uploaded for this request
                    </div>
                `;
            }

            // Reason for Decline / Notes Block
            let notesBlock = '';
            if (data.notes && data.notes.trim() !== '' && data.notes.trim() !== 'N/A') {
                notesBlock = `
                    <div class="or-card" style="border-color:#FCA5A5;background:#FEF2F2;">
                        <div class="or-card-header" style="border-bottom-color:#FECACA;">
                            <h4 class="or-card-title" style="color:#991B1B;">
                                <i data-lucide="alert-circle" style="width:15px;height:15px;"></i>
                                <span>Reason for Decline</span>
                            </h4>
                        </div>
                        <p style="margin:0;font-size:13.5px;color:#B91C1C;line-height:1.5;font-weight:500;">${escapeHtml(data.notes)}</p>
                    </div>
                `;
            }

            const modalHtml = `
                <div class="or-modal-header">
                    <div class="or-modal-header-top">
                        <div class="or-modal-ref-pill">
                            <i data-lucide="hash" style="width:12px;height:12px;"></i>
                            <span>${escapeHtml(data.reference_no || ('REQ-' + data.id))}</span>
                        </div>
                        <span class="or-status-badge ${statusClass}">
                            <i data-lucide="${statusIcon}" style="width:13px;height:13px;"></i>
                            <span>${escapeHtml(data.status)}</span>
                        </span>
                    </div>
                    <h3 class="or-modal-title">
                        <i data-lucide="file-text" style="width:22px;height:22px;color:#93C5FD;"></i>
                        <span>Online Service Request Details</span>
                    </h3>
                    <div class="or-modal-subtitle">
                        <i data-lucide="clock" style="width:13px;height:13px;"></i>
                        <span>Submitted on ${escapeHtml(data.created_at)} ${data.created_at_human ? '(' + escapeHtml(data.created_at_human) + ')' : ''}</span>
                    </div>
                </div>

                <div class="or-modal-body">
                    ${warningBannerHtml}

                    <!-- 1. Beneficiary Information -->
                    <div class="or-card">
                        <div class="or-card-header">
                            <h4 class="or-card-title">
                                <i data-lucide="user" style="width:15px;height:15px;"></i>
                                <span>Beneficiary & Applicant Information</span>
                            </h4>
                            <span class="or-card-tag">${escapeHtml(data.request_for || 'Self')}</span>
                        </div>
                        <div class="or-grid-2">
                            <div>
                                <div class="or-field-label"><i data-lucide="user-check" style="width:12px;height:12px;"></i> Full Name</div>
                                <div class="or-field-val" style="font-size:15px;color:#1A237E;font-weight:700;">
                                    ${escapeHtml(data.first_name)} ${escapeHtml(data.last_name)}
                                </div>
                            </div>
                            <div>
                                <div class="or-field-label"><i data-lucide="calendar" style="width:12px;height:12px;"></i> Date of Birth & Age</div>
                                <div class="or-field-val">
                                    ${data.dob ? `${escapeHtml(data.dob)}` : '<span style="color:#94A3B8;font-weight:normal;">Not provided</span>'}
                                    ${data.age !== null && data.age !== undefined ? `<span style="color:#64748B;font-weight:normal;"> (${data.age} yrs old)</span>` : ''}
                                </div>
                            </div>
                            <div>
                                <div class="or-field-label"><i data-lucide="phone" style="width:12px;height:12px;"></i> Contact Number</div>
                                <div class="or-field-val">
                                    <a href="tel:${escapeHtml(data.contact_number)}">${escapeHtml(data.contact_number || 'N/A')}</a>
                                </div>
                            </div>
                            <div>
                                <div class="or-field-label"><i data-lucide="mail" style="width:12px;height:12px;"></i> Email Address</div>
                                <div class="or-field-val">
                                    <a href="mailto:${escapeHtml(data.email)}">${escapeHtml(data.email || 'N/A')}</a>
                                </div>
                            </div>
                            <div>
                                <div class="or-field-label"><i data-lucide="map-pin" style="width:12px;height:12px;"></i> Barangay</div>
                                <div class="or-field-val" style="color:#0F172A;">${escapeHtml(data.barangay || 'N/A')}</div>
                            </div>
                            <div>
                                <div class="or-field-label"><i data-lucide="home" style="width:12px;height:12px;"></i> Street / Home Address</div>
                                <div class="or-field-val" style="font-weight:500;">
                                    ${escapeHtml(data.address || 'No specific street address provided')}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Requested Service Information -->
                    <div class="or-card">
                        <div class="or-card-header">
                            <h4 class="or-card-title">
                                <i data-lucide="layers" style="width:15px;height:15px;"></i>
                                <span>Requested Service & Assistance Program</span>
                            </h4>
                        </div>
                        <div class="or-grid-2">
                            <div>
                                <div class="or-field-label"><i data-lucide="briefcase" style="width:12px;height:12px;"></i> Program Category</div>
                                <div class="or-field-val">
                                    <span style="display:inline-block;background:#EEF2FF;color:#1E3A8A;padding:4px 10px;border-radius:6px;font-size:12.5px;font-weight:700;border:1px solid #C7D2FE;">
                                        ${escapeHtml(data.service_type)}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="or-field-label"><i data-lucide="heart-handshake" style="width:12px;height:12px;"></i> Assistance Type</div>
                                <div class="or-field-val">
                                    <span style="display:inline-block;background:#F0FDF4;color:#166534;padding:4px 10px;border-radius:6px;font-size:12.5px;font-weight:700;border:1px solid #BBF7D0;">
                                        ${escapeHtml(data.assistance_type)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Situation Statement -->
                    <div class="or-card">
                        <div class="or-card-header">
                            <h4 class="or-card-title">
                                <i data-lucide="message-square" style="width:15px;height:15px;"></i>
                                <span>Statement of Need / Situation Description</span>
                            </h4>
                        </div>
                        <div class="or-situation-box">${escapeHtml(data.situation)}</div>
                    </div>

                    <!-- 4. Uploaded Requirements -->
                    <div class="or-card">
                        <div class="or-card-header">
                            <h4 class="or-card-title">
                                <i data-lucide="paperclip" style="width:15px;height:15px;"></i>
                                <span>Uploaded Requirements (${data.attachments_count !== undefined ? data.attachments_count : (data.attachments ? data.attachments.length : 0)})</span>
                            </h4>
                        </div>
                        ${attachmentsContent}
                    </div>

                    ${notesBlock}
                </div>
            `;

            Swal.fire({
                html: modalHtml,
                showCloseButton: true,
                showConfirmButton: true,
                showDenyButton: false,
                showCancelButton: false,
                confirmButtonText: 'Close',
                customClass: {
                    popup: 'or-modal-popup',
                    actions: 'or-modal-actions',
                    confirmButton: 'or-btn-close'
                },
                buttonsStyling: false,
                didOpen: (modal) => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    const popupEl = modal || Swal.getPopup();
                    if (popupEl) {
                        const denyBtn = popupEl.querySelector('.swal2-deny');
                        const cancelBtn = popupEl.querySelector('.swal2-cancel');
                        if (denyBtn) denyBtn.style.setProperty('display', 'none', 'important');
                        if (cancelBtn) cancelBtn.style.setProperty('display', 'none', 'important');
                    }
                },
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
