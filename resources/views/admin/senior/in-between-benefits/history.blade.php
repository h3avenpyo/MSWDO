<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>In-Between Birthday Cash Gift - Benefit History</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;
            --primary-hover:#121858;
            --primary-dark:#0D1442;
            --sidebar-bg:#1A237E;
            --sidebar-width:260px;
            --accent-yellow:#FBC02D;
            --background:#F1F5F9;
            --surface:#FFFFFF;
            --border:#CBD5E1;
            --border-light:#E2E8F0;
            --text-primary:#0F172A;
            --text-secondary:#475569;
            --text-muted:#94A3B8;
            --font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
            --content-padding:28px;
            --radius-md:10px;
            --radius-lg:14px;
            --shadow-sm:0 1px 3px rgba(15,23,42,0.06);
            --shadow-md:0 4px 12px rgba(15,23,42,0.08);
        }

        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}

        /* Page Header */
        .history-header-card{
            background:#fff;
            border:1px solid var(--border-light);
            border-radius:var(--radius-lg);
            padding:20px 24px;
            margin-bottom:20px;
            box-shadow:var(--shadow-sm);
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:16px;
        }
        .history-header-left h1{
            font-size:1.45rem;
            font-weight:800;
            color:#0F172A;
            margin:0 0 4px 0;
            display:flex;
            align-items:center;
            gap:10px;
            letter-spacing:-0.01em;
        }
        .history-header-left p{
            font-size:0.875rem;
            color:var(--text-secondary);
            margin:0;
        }
        .history-header-actions{
            display:flex;
            gap:10px;
            align-items:center;
            flex-wrap:wrap;
        }

        /* Constrained Layout Container */
        .page-container{
            width:100%;
            max-width:1380px;
            margin:0;
            box-sizing:border-box;
        }

        /* Metric Chips */
        .metrics-grid{
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:14px;
            margin-bottom:16px;
            width:100%;
        }
        .metric-card{
            background:#fff;
            border:1px solid var(--border-light);
            border-radius:var(--radius-md);
            padding:16px 18px;
            display:flex;
            align-items:center;
            gap:14px;
            box-shadow:var(--shadow-sm);
        }
        .metric-icon{
            width:44px;
            height:44px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }
        .metric-icon svg{width:22px;height:22px;}
        .metric-info{min-width:0;flex:1;}
        .metric-label{font-size:12px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.04em;margin-bottom:2px;}
        .metric-value{font-size:1.35rem;font-weight:800;color:#0F172A;line-height:1.2;font-variant-numeric:tabular-nums;}

        /* Filter Panel */
        .filter-panel{
            background:#fff;
            border:1px solid var(--border-light);
            border-radius:var(--radius-lg);
            padding:18px 20px;
            margin-bottom:20px;
            box-shadow:var(--shadow-sm);
        }
        .filter-panel-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:14px;
            padding-bottom:10px;
            border-bottom:1px solid var(--border-light);
        }
        .filter-panel-title{
            font-size:13px;
            font-weight:700;
            color:#1E293B;
            display:flex;
            align-items:center;
            gap:6px;
            text-transform:uppercase;
            letter-spacing:0.04em;
        }
        .filter-panel-title svg{width:16px;height:16px;color:var(--primary);}
        .filter-grid{
            display:grid;
            grid-template-columns:2fr 1.2fr 1fr 1fr 1.1fr 1.1fr auto;
            gap:12px;
            align-items:flex-end;
        }
        .filter-field{display:flex;flex-direction:column;gap:6px;}
        .filter-label{font-size:12px;font-weight:600;color:#475569;}
        .filter-input{
            height:42px;
            border:1px solid #CBD5E1;
            border-radius:8px;
            padding:0 12px;
            font-size:13px;
            color:#1E293B;
            background:#fff;
            font-family:inherit;
            outline:none;
            transition:border-color .15s, box-shadow .15s;
            width:100%;
        }
        .filter-input:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(26,35,126,0.12);
        }
        .filter-select{
            height:42px;
            border:1px solid #CBD5E1;
            border-radius:8px;
            padding:0 32px 0 12px;
            font-size:13px;
            color:#1E293B;
            background:#fff;
            font-family:inherit;
            outline:none;
            transition:border-color .15s, box-shadow .15s;
            width:100%;
            cursor:pointer;
            appearance:none;
            -webkit-appearance:none;
            background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748B' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat:no-repeat;
            background-position:right 12px center;
            background-size:14px 10px;
        }
        .filter-select:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(26,35,126,0.12);
        }
        .input-group{
            display:flex;
            align-items:stretch;
            width:100%;
            height:42px;
        }
        .input-group input{
            flex:1;
            min-width:0;
            height:42px;
            border:1px solid #CBD5E1;
            border-right:none;
            border-radius:8px 0 0 8px;
            padding:0 14px;
            font-size:13px;
            color:#1E293B;
            background:#fff;
            outline:none;
            font-family:inherit;
            transition:border-color .15s ease, box-shadow .15s ease;
        }
        .input-group input:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(26,35,126,0.12);
            position:relative;
            z-index:1;
        }
        .input-group input::placeholder{
            color:#94A3B8;
            font-size:13px;
        }
        .search-btn{
            background:var(--primary);
            color:#fff;
            border:1px solid var(--primary);
            border-radius:0 8px 8px 0;
            cursor:pointer;
            height:42px;
            width:44px;
            flex-shrink:0;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            transition:background .15s ease, border-color .15s ease;
            outline:none;
        }
        .search-btn:hover{
            background:var(--primary-hover);
            border-color:var(--primary-hover);
        }
        .search-btn svg, .search-btn i{
            width:16px;
            height:16px;
            color:#fff;
        }
        .search-group{
            position:relative;
            display:flex;
            align-items:center;
            width:100%;
            height:42px;
        }
        .search-group svg, .search-group i{
            position:absolute;
            left:13px;
            top:50%;
            transform:translateY(-50%);
            width:16px;
            height:16px;
            color:#94A3B8;
            pointer-events:none;
            z-index:2;
            transition:color .15s ease;
        }
        .search-group:focus-within svg, .search-group:focus-within i{
            color:var(--primary);
        }
        .search-group input{
            width:100%;
            height:42px;
            border:1px solid #CBD5E1;
            border-radius:8px;
            padding:0 14px 0 38px;
            font-size:13px;
            color:#1E293B;
            background:#fff;
            font-family:inherit;
            outline:none;
            transition:border-color .15s ease, box-shadow .15s ease;
        }
        .search-group input:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(26,35,126,0.12);
        }
        .search-group input::placeholder{
            color:#94A3B8;
            font-size:13px;
        }
        .filter-btn-group{
            display:flex;
            gap:8px;
            align-items:center;
        }

        /* Buttons */
        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            height:42px;
            padding:0 16px;
            font-size:13px;
            font-weight:600;
            border-radius:8px;
            border:1px solid transparent;
            cursor:pointer;
            text-decoration:none;
            white-space:nowrap;
            font-family:inherit;
            transition:all .15s ease;
        }
        .btn svg{width:16px;height:16px;}
        .btn-primary{
            background:var(--primary);
            color:#fff;
            border-color:var(--primary);
        }
        .btn-primary:hover{
            background:var(--primary-hover);
            border-color:var(--primary-hover);
            color:#fff;
        }
        .btn-outline{
            background:#fff;
            color:#334155;
            border-color:#CBD5E1;
        }
        .btn-outline:hover{
            background:#F8FAFC;
            border-color:#94A3B8;
            color:#0F172A;
        }
        .btn-clear{
            background:#FEF2F2;
            color:#DC2626;
            border-color:#FECACA;
        }
        .btn-clear:hover{
            background:#FEE2E2;
            border-color:#DC2626;
        }
        .btn-icon{
            width:34px;
            height:34px;
            padding:0;
            border-radius:8px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border:1px solid var(--primary);
            background:var(--primary);
            color:#ffffff !important;
            cursor:pointer;
            box-shadow:0 1px 2px rgba(26,35,126,0.15);
            transition:all .15s ease;
        }
        .btn-icon:hover{
            background:var(--primary-hover);
            border-color:var(--primary-hover);
            color:#ffffff !important;
            box-shadow:0 2px 5px rgba(26,35,126,0.25);
            transform:translateY(-1px);
        }
        .btn-icon svg{width:16px;height:16px;color:#ffffff !important;stroke:#ffffff !important;}

        /* Table & Card Wrapper */
        .table-card{
            background:#fff;
            border:1px solid var(--border-light);
            border-radius:var(--radius-lg);
            overflow:hidden;
            box-shadow:var(--shadow-sm);
            margin-bottom:20px;
        }
        .table-card-header{
            padding:16px 20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            border-bottom:1px solid var(--border-light);
            background:#fff;
        }
        .table-card-title{
            font-size:15px;
            font-weight:700;
            color:#0F172A;
            display:flex;
            align-items:center;
            gap:8px;
            margin:0;
        }
        .table-count-badge{
            background:#EEF2FF;
            color:var(--primary);
            font-size:12px;
            font-weight:700;
            padding:2px 8px;
            border-radius:999px;
            border:1px solid #C7D2FE;
        }
        .archive-table-wrap{
            border:none;
            border-radius:0;
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
            max-height:550px;
            overflow-y:auto;
            width:100%;
        }
        .archive-table{
            width:100%;
            min-width:1000px;
            border-collapse:collapse;
            font-size:13px;
            table-layout:fixed;
        }
        .archive-table thead{
            background:#E2E8F0;
            position:sticky;
            top:0;
            z-index:10;
        }
        .archive-table th{
            padding:12px 14px;
            font-size:11.5px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:0.04em;
            color:#1E293B;
            text-align:left;
            border-bottom:2px solid #94A3B8;
            white-space:nowrap;
            vertical-align:middle;
        }
        .archive-table td{
            padding:12px 14px;
            font-size:13px;
            color:#1E293B;
            border-bottom:1px solid #E2E8F0;
            vertical-align:middle;
        }
        .archive-table td.col-action,
        .archive-table th.col-action{
            text-align:center;
            overflow:visible;
        }
        .archive-table tbody tr:hover{
            background:#F8FAFC;
        }
        .archive-table tbody tr:last-child td{
            border-bottom:none;
        }

        /* Status & Badges */
        .badge{
            display:inline-flex;
            align-items:center;
            gap:5px;
            padding:4px 10px;
            border-radius:999px;
            font-size:11.5px;
            font-weight:700;
            white-space:nowrap;
            line-height:1.2;
        }
        .badge-dot{
            width:6px;
            height:6px;
            border-radius:50%;
            display:inline-block;
        }
        .badge-approved{background:#EEF2FF;color:#3730A3;border:1px solid #C7D2FE;}
        .badge-approved .badge-dot{background:#4F46E5;}
        .badge-released{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;}
        .badge-released .badge-dot{background:#10B981;}
        .badge-pending{background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;}
        .badge-pending .badge-dot{background:#F59E0B;}
        .badge-rejected{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}
        .badge-rejected .badge-dot{background:#EF4444;}
        .badge-cancelled{background:#F1F5F9;color:#475569;border:1px solid #CBD5E1;}
        .badge-cancelled .badge-dot{background:#94A3B8;}

        .interval-badge{
            background:#F8FAFC;
            color:#334155;
            border:1px solid #CBD5E1;
            padding:3px 8px;
            border-radius:6px;
            font-size:12px;
            font-weight:700;
        }
        .ref-badge{
            font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;
            font-size:12px;
            font-weight:700;
            color:#1A237E;
            background:#EEF2FF;
            padding:3px 8px;
            border-radius:6px;
            border:1px solid #C7D2FE;
            display:inline-block;
        }

        /* Pagination (Outside Table Card) */
        .pagination-container{
            margin-top:14px;
            padding:6px 0 24px 0;
            background:transparent;
            border:none;
            display:flex;
            align-items:center;
            justify-content:space-between;
            flex-wrap:wrap;
            gap:12px;
        }
        .sc-pagination-info{
            font-size:0.813rem;
            color:#64748B;
            font-weight:500;
        }
        .sc-pagination-controls{
            display:flex;
            gap:4px;
            flex-wrap:wrap;
        }
        .sc-page-btn{
            height:36px;
            min-width:36px;
            padding:0 12px;
            border:1px solid #CBD5E1;
            border-radius:8px;
            background:#fff;
            color:#334155;
            font-size:0.813rem;
            font-weight:600;
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:4px;
            transition:all .15s;
            text-decoration:none;
            box-shadow:0 1px 2px rgba(0,0,0,0.05);
        }
        .sc-page-btn:hover:not(:disabled){
            background:#F8FAFC;
            border-color:#94A3B8;
        }
        .sc-page-btn.active{
            background:var(--primary);
            color:#fff;
            border-color:var(--primary);
            font-weight:700;
        }
        .sc-page-btn[disabled], .sc-page-btn:disabled{
            opacity:0.4;
            cursor:not-allowed;
            pointer-events:none;
        }

        /* Modal Overlay & Panel (Bulk Actions) */
        .modal-overlay{
            position:fixed;
            inset:0;
            background:rgba(15,23,42,0.6);
            backdrop-filter:blur(3px);
            display:none;
            align-items:center;
            justify-content:center;
            z-index:9999;
            padding:16px;
        }
        .modal-overlay.active{
            display:flex;
        }
        .modal-panel{
            background:#fff;
            border-radius:12px;
            width:100%;
            max-width:400px;
            box-shadow:0 20px 60px rgba(0,0,0,0.25);
            overflow:hidden;
        }
        .modal-panel-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:16px 20px;
            border-bottom:1px solid #E2E8F0;
            background:#F8FAFC;
        }
        .modal-panel-header h5{
            margin:0;
            font-size:15px;
            font-weight:700;
            color:#1E293B;
            display:flex;
            align-items:center;
            gap:8px;
        }
        .modal-close{
            background:none;
            border:none;
            color:#64748B;
            cursor:pointer;
            padding:4px;
            border-radius:6px;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .modal-close:hover{
            background:#E2E8F0;
            color:#1E293B;
        }
        .modal-panel-body{
            padding:16px 20px;
        }
        .modal-actions-list{
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .modal-btn{
            display:flex;
            align-items:center;
            gap:10px;
            padding:12px 16px;
            border:1px solid #E2E8F0;
            border-radius:8px;
            background:#fff;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            color:#1E293B;
            transition:all 0.15s ease;
        }
        .modal-btn:hover{
            background:#F8FAFC;
            border-color:#CBD5E1;
        }
        .modal-btn-indigo{
            background:#EEF2FF;
            border-color:#C7D2FE;
            color:#3730A3;
        }
        .modal-btn-indigo:hover{
            background:#E0E7FF;
            border-color:#A5B4FC;
        }

        /* ── Masterlist Style Modal ── */
        .senior-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            z-index: 9999;
            backdrop-filter: blur(4px);
            transition: opacity 0.2s ease;
            opacity: 0;
        }
        .senior-modal-dialog {
            background: #F8FAFC;
            border-radius: 14px;
            width: 100%;
            max-width: 780px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: 1px solid #CBD5E1;
        }
        .senior-modal-header {
            background: #1A237E;
            color: #ffffff;
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .senior-modal-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ffffff;
        }
        .senior-modal-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 6px;
        }
        .senior-modal-close:hover { opacity: 1; background: rgba(255,255,255,0.1); }
        .senior-modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
            -webkit-overflow-scrolling: touch;
        }
        .senior-modal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .senior-modal-col-full {
            grid-column: 1 / -1;
        }
        .senior-modal-col-span2 {
            grid-column: span 2;
        }
        .senior-modal-field {
            margin-bottom: 0;
        }
        .senior-modal-label {
            font-weight: 700;
            color: #64748B;
            font-size: 0.72rem;
            display: block;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .senior-modal-value {
            background: #ffffff;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
            border: 1px solid #E2E8F0;
            font-size: 0.85rem;
            color: #0F172A;
            word-break: break-word;
            overflow-wrap: anywhere;
            min-height: 38px;
            display: flex;
            align-items: center;
        }
        .senior-modal-footer {
            padding: 12px 24px;
            border-top: 1px solid #E2E8F0;
            background: #ffffff;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }
        @media (max-width:767px){
            .senior-modal-dialog{
                max-width:95%;
                max-height:90vh;
            }
            .senior-modal-grid{
                grid-template-columns:1fr;
            }
            .senior-modal-col-span2{
                grid-column:auto;
            }
        }

        /* Responsive Breakpoints */
        @media (max-width:1100px){
            .metrics-grid{grid-template-columns:repeat(2, 1fr);}
            .filter-grid{
                grid-template-columns:1fr 1fr 1fr;
            }
            .filter-btn-group{
                grid-column:1 / -1;
                justify-content:flex-end;
            }
        }
        @media (max-width:767px){
            .filter-grid{
                grid-template-columns:1fr;
            }
            .filter-btn-group{
                width:100%;
                display:flex;
            }
            .filter-btn-group .btn{flex:1;}
            .history-header-card{flex-direction:column;align-items:flex-start;}
            .history-header-actions{width:100%;}
            .history-header-actions .btn{width:100%;}

            /* Mobile Table to Card transformation */
            .archive-table thead{display:none;}
            .archive-table tbody tr{
                display:block;
                background:#fff;
                border:1px solid #CBD5E1;
                border-radius:10px;
                margin:12px;
                padding:14px;
                box-shadow:var(--shadow-sm);
            }
            .archive-table tbody td{
                display:flex;
                justify-content:space-between;
                align-items:center;
                padding:7px 0;
                border:none;
                font-size:0.82rem;
                gap:10px;
                text-align:right;
            }
            .archive-table tbody td:not(:last-child){
                border-bottom:1px solid #F1F5F9;
            }
            .archive-table tbody td::before{
                content:attr(data-label);
                font-weight:700;
                color:#64748B;
                font-size:0.75rem;
                text-transform:uppercase;
                letter-spacing:0.04em;
                flex-shrink:0;
                text-align:left;
            }
            .archive-table tbody td[data-label="Action"]{
                justify-content:flex-end;
                padding-top:10px;
                border-bottom:none;
            }
            .archive-table tbody td[data-label="Action"]::before{display:none;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'in-between-history', 'mobileSubtitle' => 'Benefit History'])

    <div class="main">
        <div class="main-scroll">
            <div class="page-container">
            
            <!-- Page Header Card -->
            <div class="history-header-card">
                <div class="history-header-left">
                    <h1><i data-lucide="history" style="width:26px;height:26px;color:var(--primary);"></i> In-Between Birthday Cash Gift - Benefit History</h1>
                    <p>Audit trail and release history of approved in-between birthday benefit transactions.</p>
                </div>
                <div class="history-header-actions">
                    <a href="/admin/senior/in-between/eligibility-list" class="btn btn-primary">
                        <i data-lucide="users"></i> Eligibility List
                    </a>
                </div>
            </div>

            <!-- Metrics Summary -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-icon" style="background:#EEF2FF;color:#3730A3;">
                        <i data-lucide="receipt-text"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-label">Total Transactions</div>
                        <div class="metric-value">{{ number_format($totalRecords ?? $benefits->total()) }}</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon" style="background:#ECFDF5;color:#059669;">
                        <i data-lucide="banknote"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-label">Total Amount Processed</div>
                        <div class="metric-value">₱{{ number_format($totalAmount ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon" style="background:#FEF3C7;color:#D97706;">
                        <i data-lucide="check-circle"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-label">Approved Claims</div>
                        <div class="metric-value">{{ number_format($approvedCount ?? 0) }}</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon" style="background:#F0FDF4;color:#16A34A;">
                        <i data-lucide="gift"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-label">Released Payouts</div>
                        <div class="metric-value">{{ number_format($releasedCount ?? 0) }}</div>
                    </div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="filter-panel">
                <div class="filter-panel-header">
                    <div class="filter-panel-title">
                        <i data-lucide="filter"></i> Filter Benefit History
                    </div>
                    @if(request()->hasAny(['search', 'interval', 'status', 'from_date', 'to_date', 'barangay']))
                        <a href="{{ route('admin.senior.in-between.history') }}" class="text-xs font-semibold text-red-600 hover:text-red-800 flex items-center gap-1" style="text-decoration:none;">
                            <i data-lucide="x" style="width:14px;height:14px;"></i> Clear All Filters
                        </a>
                    @endif
                </div>
                <form method="GET" action="{{ route('admin.senior.in-between.history') }}" id="historyFilterForm">
                    <div class="filter-grid">
                        <div class="filter-field">
                            <label class="filter-label" for="searchInput">Search Records</label>
                            <div class="input-group">
                                <input type="text" id="searchInput" name="search" placeholder="Search name, ref #, or ID..." value="{{ request('search') }}" autocomplete="off">
                                <button type="submit" class="search-btn" title="Search">
                                    <i data-lucide="search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="filter-field">
                            <label class="filter-label" for="barangayFilter">Barangay</label>
                            <select id="barangayFilter" name="barangay" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Barangays</option>
                                @php
                                    $barangays = \App\Models\Senior\SeniorCitizenRecord::where('status', 'active')
                                        ->whereNotNull('barangay')
                                        ->pluck('barangay')
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp
                                @foreach($barangays as $barangay)
                                    <option value="{{ $barangay }}" {{ request('barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-label" for="intervalSelect">Interval</label>
                            <select id="intervalSelect" name="interval" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Intervals</option>
                                <option value="81-84" {{ request('interval') == '81-84' ? 'selected' : '' }}>81–84</option>
                                <option value="86-89" {{ request('interval') == '86-89' ? 'selected' : '' }}>86–89</option>
                                <option value="91-94" {{ request('interval') == '91-94' ? 'selected' : '' }}>91–94</option>
                                <option value="96-99" {{ request('interval') == '96-99' ? 'selected' : '' }}>96–99</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-label" for="statusSelect">Status</label>
                            <select id="statusSelect" name="status" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-label" for="fromDateInput">From Date</label>
                            <input type="date" id="fromDateInput" name="from_date" class="filter-input" value="{{ request('from_date') }}">
                        </div>

                        <div class="filter-field">
                            <label class="filter-label" for="toDateInput">To Date</label>
                            <input type="date" id="toDateInput" name="to_date" class="filter-input" value="{{ request('to_date') }}">
                        </div>

                        <div class="filter-btn-group">
                            <button type="submit" class="btn btn-primary" title="Apply Filter">
                                <i data-lucide="search"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'barangay', 'interval', 'status', 'from_date', 'to_date']))
                                <a href="{{ route('admin.senior.in-between.history') }}" class="btn btn-clear" title="Reset Filters">
                                    <i data-lucide="rotate-ccw"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="table-card">
                <div class="table-card-header">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <h3 class="table-card-title">
                            <i data-lucide="list"></i> Benefit History Records
                            <span class="table-count-badge">{{ $benefits->total() }}</span>
                        </h3>
                        <span id="selectedCountBadge" style="display:none; font-size:12px; font-weight:700; color:#3730A3; background:#EEF2FF; padding:3px 10px; border-radius:999px; border:1px solid #C7D2FE;">
                            <span id="selectedCount">0</span> selected
                        </span>
                    </div>
                    <div id="bulkHeaderActions" style="display:none; align-items:center; gap:8px;">
                        <button type="button" class="btn btn-primary btn-sm" onclick="showBulkActionPopup()">
                            <i data-lucide="list-checks"></i> Bulk Actions
                        </button>
                        <button type="button" class="btn btn-outline" style="height:34px; padding:0 12px; font-size:12px;" onclick="clearSelections()">
                            <i data-lucide="x" style="width:14px;height:14px;"></i> Clear Selection
                        </button>
                    </div>
                </div>

                <div class="archive-table-wrap">
                    <table class="archive-table">
                        <thead>
                            <tr>
                                <th class="col-check" style="width:4%; text-align:center;">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" title="Select All" style="cursor:pointer; width:16px; height:16px; accent-color:var(--primary);">
                                </th>
                                <th class="col-control" style="width:12%;">Control Number</th>
                                <th class="col-senior" style="width:14%;">Senior Citizen</th>
                                <th class="col-barangay" style="width:10%;">Barangay</th>
                                <th class="col-age" style="width:4%; text-align:center;">Age</th>
                                <th class="col-amount" style="width:8%; text-align:right;">Amount</th>
                                <th class="col-status" style="width:8%; text-align:center;">Status</th>
                                <th class="col-date" style="width:8%; text-align:center;">Date Applied</th>
                                <th class="col-eligible" style="width:10%; text-align:center;">Next Eligible</th>
                                <th class="col-action" style="width:8%; text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($benefits as $benefit)
                                <tr>
                                    <td class="col-check" data-label="Select" style="text-align:center;">
                                        <input type="checkbox" class="record-checkbox" value="{{ $benefit->id }}" onchange="updateSelectedCount()" style="cursor:pointer; width:16px; height:16px; accent-color:var(--primary);">
                                    </td>
                                    <td class="col-control" data-label="Control Number">
                                        <span class="ref-badge" style="background:#F1F5F9; color:#334155; border-color:#CBD5E1;">{{ $benefit->senior->control_number ?? $benefit->senior->senior_id_number ?? ('#' . $benefit->senior_id) }}</span>
                                    </td>
                                    <td class="col-senior" data-label="Senior Citizen">
                                        <div style="font-weight:600; color:#0F172A;">{{ $benefit->full_name }}</div>
                                    </td>
                                    <td class="col-barangay" data-label="Barangay">
                                        <div style="font-size:12.5px; color:#334155; display:flex; align-items:center; gap:5px;">
                                            <i data-lucide="map-pin" style="width:13px; height:13px; color:#64748B; flex-shrink:0;"></i>
                                            <span>{{ $benefit->senior->barangay ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="col-age" data-label="Age" style="text-align:center; font-weight:600; color:#0F172A;">
                                        {{ $benefit->current_age }}
                                    </td>
                                    <td class="col-amount" data-label="Amount" style="text-align:right; font-weight:700; color:#059669; font-variant-numeric:tabular-nums; white-space:nowrap;">
                                        ₱{{ number_format($benefit->amount, 2) }}
                                    </td>
                                    <td class="col-status" data-label="Status" style="text-align:center;">
                                        <span class="badge badge-{{ $benefit->status }}">
                                            <span class="badge-dot"></span>
                                            {{ ucfirst($benefit->status) }}
                                        </span>
                                    </td>
                                    <td class="col-date" data-label="Date Applied" style="text-align:center; color:#475569; white-space:nowrap;">
                                        {{ $benefit->application_date ? $benefit->application_date->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="col-eligible" data-label="Next Eligible" style="text-align:center; color:#475569; white-space:nowrap;">
                                        @if($benefit->payout_date)
                                            {{ $benefit->payout_date->addYears(6)->format('M d, Y') }}
                                        @else
                                            <span style="color:#94A3B8; font-style:italic;">Pending payout</span>
                                        @endif
                                    </td>
                                    <td class="col-action" data-label="Action" style="text-align:center;">
                                        <button type="button" class="btn-icon btn-view-details" title="View Transaction Details"
                                            data-reference="{{ $benefit->reference_number }}"
                                            data-name="{{ $benefit->full_name }}"
                                            data-senior-id="{{ $benefit->senior->control_number ?? $benefit->senior->senior_id_number ?? ('#' . $benefit->senior_id) }}"
                                            data-barangay="{{ $benefit->senior->barangay ?? 'N/A' }}"
                                            data-age="{{ $benefit->current_age }}"
                                            data-interval="{{ $benefit->eligibility_interval }}"
                                            data-amount="₱{{ number_format($benefit->amount, 2) }}"
                                            data-status="{{ ucfirst($benefit->status) }}"
                                            data-status-raw="{{ $benefit->status }}"
                                            data-application-date="{{ $benefit->application_date ? $benefit->application_date->format('F d, Y') : 'N/A' }}"
                                            data-payout-date="{{ $benefit->payout_date ? $benefit->payout_date->format('F d, Y') : 'Pending release' }}"
                                            data-processed-by="{{ $benefit->processedBy->name ?? 'Admin' }}"
                                            data-approved-by="{{ $benefit->approvedBy->name ?? 'Admin' }}"
                                            data-remarks="{{ $benefit->remarks ?? 'None' }}">
                                            <i data-lucide="eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="padding:56px 20px; text-align:center;">
                                        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
                                            <div style="width:60px; height:60px; border-radius:50%; background:#F1F5F9; display:flex; align-items:center; justify-content:center; color:#94A3B8;">
                                                <i data-lucide="receipt-text" style="width:28px; height:28px;"></i>
                                            </div>
                                            <div style="font-size:16px; font-weight:700; color:#1E293B;">No benefit transactions found</div>
                                            <div style="font-size:13px; color:#64748B; max-width:360px;">
                                                No records match your selected filters. Try resetting the filters or check the eligibility list to process new claims.
                                            </div>
                                            <a href="/admin/senior/in-between/eligibility-list" class="btn btn-primary" style="margin-top:8px;">
                                                <i data-lucide="users"></i> Go to Eligibility List
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> <!-- End Table Card -->

            <!-- Divider Line -->
            <div style="border-top:2px solid #CBD5E1; margin:0;"></div>

            <!-- Persistent Pagination (Outside Container) -->
            <div class="pagination-container">
                <div class="sc-pagination-info">
                    @if($benefits->total() === 0)
                        Showing 0 of 0 Transactions
                    @else
                        Showing {{ $benefits->firstItem() }}–{{ $benefits->lastItem() }} of {{ $benefits->total() }} Transactions
                    @endif
                </div>
                <div class="sc-pagination-controls">
                    @if($benefits->hasPages())
                        @if($benefits->onFirstPage())
                            <span class="sc-page-btn" disabled>Previous</span>
                        @else
                            <a href="{{ $benefits->appends(request()->except('page'))->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                        @endif

                        <span class="sc-page-btn active">{{ $benefits->currentPage() }}</span>

                        @if($benefits->hasMorePages())
                            <a href="{{ $benefits->appends(request()->except('page'))->nextPageUrl() }}" class="sc-page-btn">Next</a>
                        @else
                            <span class="sc-page-btn" disabled>Next</span>
                        @endif
                    @else
                        <span class="sc-page-btn" disabled>Previous</span>
                        <span class="sc-page-btn active">1</span>
                        <span class="sc-page-btn" disabled>Next</span>
                    @endif
                </div>
            </div>

            </div>
        </div>
    </div>
</div>

<!-- ======================== TRANSACTION DETAILS MODAL (Masterlist Style) ======================== -->
<div id="benefitDetailsModal" class="senior-modal-backdrop">
    <div class="senior-modal-dialog">
        <div class="senior-modal-header">
            <h5 class="senior-modal-title">
                <i data-lucide="receipt-text" style="width:20px;height:20px;"></i>
                Benefit Transaction Details
            </h5>
            <button onclick="closeBenefitModal()" class="senior-modal-close" aria-label="Close modal">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div class="senior-modal-body">
            <div class="senior-modal-grid">
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Reference Number</label>
                    <div class="senior-modal-value" id="modalReference">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Control Number</label>
                    <div class="senior-modal-value" id="modalControlNumber">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Status</label>
                    <div class="senior-modal-value" id="modalStatus">—</div>
                </div>
                <div class="senior-modal-field senior-modal-col-span2">
                    <label class="senior-modal-label">Senior Citizen Full Name</label>
                    <div class="senior-modal-value" id="modalFullName" style="font-weight:700;">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Barangay</label>
                    <div class="senior-modal-value" id="modalBarangay">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Age at Claim</label>
                    <div class="senior-modal-value" id="modalAge">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Benefit Amount</label>
                    <div class="senior-modal-value" id="modalAmount">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Date Applied</label>
                    <div class="senior-modal-value" id="modalAppDate">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Payout Date</label>
                    <div class="senior-modal-value" id="modalPayoutDate">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Processed By</label>
                    <div class="senior-modal-value" id="modalProcessedBy">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Approved By</label>
                    <div class="senior-modal-value" id="modalApprovedBy">—</div>
                </div>
                <div class="senior-modal-field senior-modal-col-span2">
                    <label class="senior-modal-label">Remarks</label>
                    <div class="senior-modal-value" id="modalRemarks" style="color:#475569;">—</div>
                </div>
            </div>
        </div>
        <div class="senior-modal-footer">
            <button type="button" onclick="closeBenefitModal()" class="btn btn-primary" style="height:38px; padding:0 22px;">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div id="bulkActionModal" class="senior-modal-backdrop">
    <div class="senior-modal-dialog" style="max-width: 480px;">
        <div class="senior-modal-header">
            <h5 class="senior-modal-title">
                <i data-lucide="list-checks" style="width:20px;height:20px;"></i> Bulk Actions
            </h5>
            <button onclick="closeBulkModal()" class="senior-modal-close" aria-label="Close modal">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div class="senior-modal-body">
            <div class="senior-modal-grid">
                <div class="senior-modal-field senior-modal-col-full">
                    <label class="senior-modal-label">Selected Records</label>
                    <div class="senior-modal-value" id="bulkModalSummary">0 selected</div>
                </div>
            </div>
            <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
                <button type="button" id="bulkBtnExport" class="btn btn-primary" onclick="exportPdf(event)" style="width: 100%; height: 42px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i data-lucide="file-output" style="width:18px;height:18px;"></i> Export Selected (PDF)
                </button>
            </div>
        </div>
        <div class="senior-modal-footer">
            <button type="button" onclick="closeBulkModal()" class="btn btn-outline" style="height:38px; padding:0 22px;">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        restoreSelections();
        
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-view-details');
            if (!btn) return;
            openBenefitDetails({
                reference: btn.dataset.reference,
                name: btn.dataset.name,
                senior_id: btn.dataset.seniorId,
                barangay: btn.dataset.barangay,
                age: btn.dataset.age,
                interval: btn.dataset.interval,
                amount: btn.dataset.amount,
                status: btn.dataset.status,
                status_raw: btn.dataset.statusRaw,
                application_date: btn.dataset.applicationDate,
                payout_date: btn.dataset.payoutDate,
                processed_by: btn.dataset.processedBy,
                approved_by: btn.dataset.approvedBy,
                remarks: btn.dataset.remarks
            });
        });

        const modalEl = document.getElementById('benefitDetailsModal');
        if (modalEl) {
            modalEl.addEventListener('click', function(e) {
                if (e.target === this) closeBenefitModal();
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeBenefitModal();
        });
    });

    window.selectAllMatching = false;
    window.totalRecords = {{ $totalAllRecords ?? $benefits->total() ?? 0 }};

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.record-checkbox');
        
        window.selectAllMatching = selectAll.checked;
        
        checkboxes.forEach(cb => { 
            cb.checked = selectAll.checked; 
        });
        
        // Save selections to localStorage
        if (selectAll.checked) {
            // Save that all records are selected
            localStorage.setItem('historySelectAll', 'true');
            localStorage.setItem('historyTotalRecords', window.totalRecords);
        } else {
            localStorage.removeItem('historySelectAll');
            localStorage.removeItem('historyTotalRecords');
            localStorage.removeItem('selectedHistoryIds');
        }
        
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.record-checkbox:checked');
        const count = checkedBoxes.length;
        const total = document.querySelectorAll('.record-checkbox').length;
        
        const countSpan = document.getElementById('selectedCount');
        const badge = document.getElementById('selectedCountBadge');
        const actions = document.getElementById('bulkHeaderActions');
        const selectAll = document.getElementById('selectAll');
        
        // If select all matching is enabled, use total records count
        if (window.selectAllMatching || localStorage.getItem('historySelectAll') === 'true') {
            const totalCount = parseInt(localStorage.getItem('historyTotalRecords')) || window.totalRecords;
            if (countSpan) countSpan.textContent = totalCount;
        } else {
            if (countSpan) countSpan.textContent = count;
        }
        
        if (badge) badge.style.display = count > 0 ? 'inline-block' : 'none';
        if (actions) actions.style.display = count > 0 ? 'flex' : 'none';
        
        if (selectAll) {
            selectAll.checked = total > 0 && count === total;
            selectAll.indeterminate = count > 0 && count < total;
        }
    }

    function clearSelections() {
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }
        window.selectAllMatching = false;
        localStorage.removeItem('historySelectAll');
        localStorage.removeItem('historyTotalRecords');
        localStorage.removeItem('selectedHistoryIds');
        document.querySelectorAll('.record-checkbox').forEach(cb => cb.checked = false);
        updateSelectedCount();
    }

    function restoreSelections() {
        const selectAll = localStorage.getItem('historySelectAll');
        if (selectAll === 'true') {
            window.selectAllMatching = true;
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
            }
            // Check all current page checkboxes
            document.querySelectorAll('.record-checkbox').forEach(cb => {
                cb.checked = true;
            });
        }
        updateSelectedCount();
    }

    function closeBenefitModal() {
        const modal = document.getElementById('benefitDetailsModal');
        if (modal) {
            modal.classList.remove('active');
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        }
    }

    function showBulkActionPopup() {
        const checkboxes = document.querySelectorAll('.record-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        if (ids.length === 0) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }

        const summary = document.getElementById('bulkModalSummary');
        if (summary) {
            summary.textContent = `${ids.length} record(s) selected`;
        }

        const modal = document.getElementById('bulkActionModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.style.opacity = '1';
        }
        lucide.createIcons();
    }

    function closeBulkModal() {
        const modal = document.getElementById('bulkActionModal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        }
    }

    async function exportPdf(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        closeBulkModal();

        const checkboxes = document.querySelectorAll('.record-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value);
        
        if (ids.length === 0) {
            Swal.fire('No Selection', 'Please select at least one record to export.', 'warning');
            return;
        }

        try {
            const table = document.querySelector('.archive-table');
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            const selectedRows = rows.filter(row => {
                const checkbox = row.querySelector('.record-checkbox:checked');
                return checkbox !== null;
            });

            const printContent = `
                <html>
                <head>
                    <title>In-Between Benefits History Export</title>
                    <style>
                        @page {
                            margin: 6mm 8mm 8mm 8mm;
                            size: landscape;
                        }
                        body {
                            font-family: Arial, Helvetica, sans-serif;
                            font-size: 11.5px;
                            line-height: 1.3;
                            color: #0f172a;
                            margin: 0;
                            padding: 0;
                        }
                        .header-table {
                            width: 100%;
                            border-collapse: collapse;
                            border: none;
                            margin-bottom: 4px;
                            table-layout: fixed;
                        }
                        .header-table td {
                            border: none;
                            padding: 2px 4px;
                            vertical-align: middle;
                        }
                        .logo-cell {
                            width: 60px;
                            text-align: center;
                            vertical-align: middle;
                            padding: 0 10px;
                        }
                        .logo-cell:first-child {
                            padding-left: 30px;
                        }
                        .logo-cell:last-child {
                            padding-right: 30px;
                        }
                        .gov {
                            text-align: center;
                            vertical-align: middle;
                            line-height: 1.3;
                            color: #0f172a;
                            padding: 0 6px;
                        }
                        .gov .gov-line {
                            font-size: 10px;
                            font-weight: 500;
                            color: #374151;
                        }
                        .gov h2 {
                            margin: 3px 0 0;
                            font-family: Arial, Helvetica, sans-serif;
                            font-size: 14px;
                            font-weight: bold;
                            color: #1A237E;
                            letter-spacing: 0.4px;
                        }
                        .line {
                            border-top: 2px solid #1A237E;
                            margin: 2px 0 1px;
                        }
                        .line2 {
                            border-top: 1px solid #1A237E;
                            margin-bottom: 4px;
                        }
                        .report-title {
                            text-align: center;
                            margin: 2px 0 4px;
                        }
                        .report-title h3 {
                            font-size: 14px;
                            margin: 0;
                            color: #1A237E;
                            text-transform: uppercase;
                            font-weight: bold;
                            letter-spacing: 0.5px;
                        }
                        .data-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 0;
                            font-size: 11.5px;
                            table-layout: fixed;
                        }
                        .data-table thead {
                            display: table-header-group;
                        }
                        .data-table tr {
                            page-break-inside: avoid;
                        }
                        .data-table th {
                            background: #1A237E;
                            color: #ffffff;
                            padding: 7px 4.5px;
                            text-align: left;
                            font-weight: bold;
                            font-size: 11.5px;
                            text-transform: uppercase;
                            border: 1px solid #1A237E;
                            overflow: hidden;
                            word-wrap: break-word;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .data-table td {
                            padding: 7px 4.5px;
                            border: 1px solid #94a3b8;
                            vertical-align: middle;
                            overflow: hidden;
                            word-wrap: break-word;
                            font-size: 11.5px;
                            color: #0f172a;
                        }
                        .data-table tbody tr:nth-child(even) {
                            background: #f8fafc;
                        }
                        .footer {
                            margin-top: 15px;
                            border-top: 1px solid #E2E8F0;
                            padding-top: 8px;
                            font-size: 9.5px;
                            color: #64748B;
                            text-align: center;
                        }
                        .signature-section {
                            margin-top: 30px;
                            display: flex;
                            justify-content: space-between;
                            page-break-inside: avoid;
                        }
                        .signature-block {
                            text-align: center;
                            width: 30%;
                        }
                        .signature-line {
                            border-top: 1px solid #0f172a;
                            margin-top: 60px;
                            padding-top: 8px;
                            font-weight: 600;
                            color: #0f172a;
                        }
                        .col-check { display: none; }
                        .badge { display: none; }
                        @media print {
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <table class="header-table">
                        <tr>
                            <td class="logo-cell">
                                <img src="/images/silang.png" style="width: 72px; height: 72px; object-fit: contain; display: block; margin: 0 auto;" alt="Silang Seal" onerror="this.style.display='none'">
                            </td>
                            <td class="gov">
                                <div class="gov-line">Republic of the Philippines • Province of Cavite • Municipality of Silang</div>
                                <h2>MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</h2>
                                <div style="font-size: 10px; color: #475569; margin-top: 2px;">In-Between Birthday Cash Gift - Benefit History</div>
                            </td>
                            <td class="logo-cell">
                                <img src="/images/dswd.png" style="width: 72px; height: 72px; object-fit: contain; display: block; margin: 0 auto;" alt="DSWD Logo" onerror="this.style.display='none'">
                            </td>
                        </tr>
                    </table>

                    <div class="line"></div>
                    <div class="line2"></div>

                    <div class="report-title">
                        <h3>In-Between Birthday Cash Gift Benefit History</h3>
                    </div>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 12%;">Control Number</th>
                                <th style="width: 20%;">Name</th>
                                <th style="width: 10%;">Barangay</th>
                                <th style="width: 4%;">Age</th>
                                <th style="width: 8%;">Amount</th>
                                <th style="width: 8%;">Status</th>
                                <th style="width: 8%;">Date Applied</th>
                                <th style="width: 10%;">Next Eligible</th>
                                <th style="width: 10%;">Signature</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${selectedRows.map(row => {
                                const cells = row.querySelectorAll('td');
                                return `<tr>
                                    ${Array.from(cells).slice(1, 9).map(cell => `<td>${cell.textContent.trim()}</td>`).join('')}
                                    <td style="border-bottom: 1px solid #94a3b8; height: 40px;"></td>
                                </tr>`;
                            }).join('')}
                        </tbody>
                    </table>
                    
                    <div class="footer">
                        This document is generated by the MSWDO Silang Senior Citizen Management System and is intended for official use only.
                    </div>
                    
                    <div class="signature-section">
                        <div class="signature-block">
                            <div class="signature-line">
                                OSCA Officer
                            </div>
                        </div>
                        <div class="signature-block">
                            <div class="signature-line">
                                MSWDO Head
                            </div>
                        </div>
                        <div class="signature-block">
                            <div class="signature-line">
                                Municipal Mayor
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            printWindow.onload = function() {
                printWindow.print();
                
                // After PDF is generated, mark records as exported
                fetch('/admin/senior/in-between/mark-exported', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Export Complete',
                            text: `PDF exported and ${ids.length} record(s) moved to payout history.`,
                            confirmButtonColor: '#1A237E',
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Export Complete',
                            text: 'PDF exported but records could not be moved.',
                            confirmButtonColor: '#1A237E'
                        });
                    }
                })
                .catch(err => {
                    console.error('Mark Exported Error:', err);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Export Complete',
                        text: 'PDF exported but records could not be moved.',
                        confirmButtonColor: '#1A237E'
                    });
                });
            };
        } catch (err) {
            console.error('Export Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Export Failed',
                text: 'An error occurred while preparing the export. Please try again.',
                confirmButtonColor: '#1A237E'
            });
        }
    }

    function openBenefitDetails(data) {
        document.getElementById('modalReference').innerHTML = `<span class="ref-badge">${data.reference}</span>`;
        document.getElementById('modalControlNumber').innerHTML = `<span class="ref-badge" style="background:#F1F5F9; color:#334155; border-color:#CBD5E1;">${data.senior_id}</span>`;
        
        const statusColors = {
            approved: { bg: '#EEF2FF', text: '#3730A3', border: '#C7D2FE', dot: '#4F46E5' },
            released: { bg: '#ECFDF5', text: '#065F46', border: '#A7F3D0', dot: '#10B981' },
            pending: { bg: '#FEF3C7', text: '#92400E', border: '#FDE68A', dot: '#F59E0B' },
            rejected: { bg: '#FEE2E2', text: '#991B1B', border: '#FECACA', dot: '#EF4444' },
            cancelled: { bg: '#F1F5F9', text: '#475569', border: '#CBD5E1', dot: '#94A3B8' }
        };
        const sColor = statusColors[data.status_raw] || statusColors.approved;
        document.getElementById('modalStatus').innerHTML = `
            <span class="badge" style="background:${sColor.bg}; color:${sColor.text}; border:1px solid ${sColor.border};">
                <span class="badge-dot" style="background:${sColor.dot};"></span> ${data.status}
            </span>
        `;
        
        document.getElementById('modalFullName').textContent = data.name || '—';
        document.getElementById('modalBarangay').textContent = data.barangay || '—';
        document.getElementById('modalAge').textContent = data.age ? `${data.age} yrs old` : '—';
        document.getElementById('modalAmount').innerHTML = `<span style="color:#059669; font-weight:800; font-size:1.05rem;">${data.amount || '—'}</span>`;
        document.getElementById('modalAppDate').textContent = data.application_date || '—';
        document.getElementById('modalPayoutDate').textContent = data.payout_date || '—';
        document.getElementById('modalProcessedBy').textContent = data.processed_by || '—';
        document.getElementById('modalApprovedBy').textContent = data.approved_by || '—';
        document.getElementById('modalRemarks').textContent = data.remarks || 'None';

        const modal = document.getElementById('benefitDetailsModal');
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => { modal.style.opacity = '1'; }, 10);
        lucide.createIcons();
    }

    function closeBenefitModal() {
        const modal = document.getElementById('benefitDetailsModal');
        if (!modal) return;
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 200);
    }
</script>
</body>
</html>
