<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>In-Between Birthday Cash Gift - Eligibility List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
            grid-template-columns:2fr 1.2fr 1fr 1fr auto;
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
        .btn-sm{
            height:32px;
            padding:0 12px;
            font-size:12px;
            border-radius:6px;
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
        .badge-approved, .badge-eligible{background:#EEF2FF;color:#3730A3;border:1px solid #C7D2FE;}
        .badge-approved .badge-dot, .badge-eligible .badge-dot{background:#4F46E5;}
        .badge-released, .badge-claimed{background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0;}
        .badge-released .badge-dot, .badge-claimed .badge-dot{background:#10B981;}
        .badge-pending{background:#FEF3C7;color:#92400E;border:1px solid #FDE68A;}
        .badge-pending .badge-dot{background:#F59E0B;}
        .badge-rejected, .badge-ineligible{background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;}
        .badge-rejected .badge-dot, .badge-ineligible .badge-dot{background:#EF4444;}
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
        .modal-overlay.active{display:flex;}
        .modal-panel{
            background:#ffffff;
            border-radius:14px;
            width:100%;
            max-width:440px;
            box-shadow:0 20px 25px -5px rgba(0,0,0,0.2),0 10px 10px -5px rgba(0,0,0,0.1);
            overflow:hidden;
            animation:modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes modalPop{
            0%{opacity:0;transform:scale(0.96) translateY(8px);}
            100%{opacity:1;transform:scale(1) translateY(0);}
        }
        .modal-panel-header{
            background:#1A237E;
            color:#ffffff;
            padding:16px 20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .modal-panel-header h5{
            font-size:1.1rem;
            font-weight:700;
            display:flex;
            align-items:center;
            gap:8px;
            color:#ffffff;
            margin:0;
        }
        .modal-close{
            color:#ffffff;
            cursor:pointer;
            border:none;
            background:transparent;
            padding:4px;
            border-radius:6px;
            display:flex;
            align-items:center;
            justify-content:center;
            opacity:0.8;
            transition:opacity 0.2s;
        }
        .modal-close:hover{opacity:1;}
        .modal-close svg{width:20px;height:20px;}
        .modal-panel-body{padding:18px;}
        .modal-actions-list{display:flex;flex-direction:column;gap:10px;}
        .modal-btn{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            border:none;
            border-radius:10px;
            padding:12px 18px;
            font-size:0.95rem;
            font-weight:600;
            transition:background .2s;
            cursor:pointer;
            width:100%;
            font-family:inherit;
            color:#fff;
        }
        .modal-btn svg{width:18px;height:18px;}
        .modal-btn-claim{background:#1A237E;}
        .modal-btn-claim:hover{background:#121858;}
        .modal-btn-danger{background:#DC2626;}
        .modal-btn-danger:hover{background:#B91C1C;}
        .modal-btn-success{background:#10B981;}
        .modal-btn-success:hover{background:#059669;}
        .modal-btn-indigo{background:#4F46E5;}
        .modal-btn-indigo:hover{background:#4338CA;}

        /* ── Masterlist Style Modal (Senior Details) ── */
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
            .metrics-grid{grid-template-columns:1fr;}
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
            .archive-table tbody td[data-label="Select"]{
                justify-content:flex-start;
                padding-bottom:6px;
            }
            .archive-table tbody td[data-label="Select"]::before{display:none;}
            .archive-table tbody td[data-label="Action"]{
                justify-content:flex-end;
                padding-top:10px;
                border-bottom:none;
            }
            .archive-table tbody td[data-label="Action"]::before{display:none;}
            .archive-table tbody td[data-label="Action"] .btn{width:100%;}
        }

        .claim-process-popup{
            width:min(520px, calc(100vw - 24px)) !important;
            padding:0 0 18px !important;
            border:1px solid #CBD5E1 !important;
            border-radius:16px !important;
            box-shadow:0 24px 60px rgba(15,23,42,.22) !important;
            overflow:hidden;
        }
        .claim-process-title{margin:0 !important;padding:20px 24px 16px !important;background:#1A237E;color:#fff !important;font-size:1.05rem !important;font-weight:800 !important;text-align:left !important;}
        .claim-process-icon{margin:18px auto 0 !important;transform:scale(.78);}
        .claim-process-content{margin:0 !important;padding:18px 24px 0 !important;color:#334155 !important;}
        .claim-process-summary{margin:0 !important;padding:4px 16px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;text-align:left;}
        .claim-process-row{display:grid;grid-template-columns:minmax(120px,1fr) minmax(0,1.5fr);align-items:center;gap:16px;min-height:42px;padding:8px 0;border-bottom:1px solid #E2E8F0;}
        .claim-process-row:last-child{border-bottom:0;}
        .claim-process-label{color:#64748B;font-size:12px;font-weight:700;}
        .claim-process-value{color:#0F172A;font-size:13px;font-weight:600;text-align:right;overflow-wrap:anywhere;}
        .claim-process-id,.claim-process-interval{display:inline-block;padding:3px 8px;border-radius:6px;background:#EEF2FF;border:1px solid #C7D2FE;color:#3730A3;font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:12px;}
        .claim-process-amount{color:#059669;font-size:16px;font-weight:800;}
        .claim-process-remarks{margin-top:16px;text-align:left;}
        .claim-process-remarks label{display:block;margin-bottom:6px;color:#475569;font-size:12px;font-weight:700;}
        .claim-process-remarks input{width:100%;height:42px;padding:0 12px;border:1px solid #CBD5E1;border-radius:8px;color:#1E293B;background:#fff;font:inherit;outline:none;}
        .claim-process-remarks input:focus{border-color:#1A237E;box-shadow:0 0 0 3px rgba(26,35,126,.12);}
        .claim-process-confirm,.claim-process-cancel{min-width:132px !important;height:40px !important;padding:0 18px !important;border-radius:8px !important;font-family:inherit !important;font-size:13px !important;font-weight:700 !important;}
        .claim-process-confirm{background:#1A237E !important;}
        .claim-process-confirm:hover{background:#121858 !important;}
        .claim-process-cancel{color:#334155 !important;border:1px solid #CBD5E1 !important;background:#fff !important;}
        .claim-process-cancel:hover{background:#F8FAFC !important;}
        @media (max-width:520px){
            .claim-process-title{padding:18px 18px 14px !important;font-size:1rem !important;}
            .claim-process-content{padding:16px 18px 0 !important;}
            .claim-process-row{grid-template-columns:1fr;gap:3px;min-height:0;}
            .claim-process-value{text-align:left;}
            .claim-process-popup .swal2-actions{width:calc(100% - 36px);margin:18px auto 0;}
            .claim-process-confirm,.claim-process-cancel{width:100%;}
        }
        .claim-process-dialog{max-width:520px;}
        .claim-process-dialog .senior-modal-header{background:#1A237E;}
        .claim-process-dialog .senior-modal-body{padding:20px 24px;}
        .claim-process-dialog .claim-process-summary{margin:0;}
        .claim-process-error{display:none;margin-top:14px;padding:10px 12px;border:1px solid #FECACA;border-radius:8px;background:#FEF2F2;color:#991B1B;font-size:13px;text-align:left;}
        .claim-process-success{display:none;padding:18px;border:1px solid #A7F3D0;border-radius:10px;background:#ECFDF5;color:#065F46;text-align:center;font-size:14px;font-weight:600;}
        .claim-process-confirmation{display:flex;align-items:flex-start;gap:9px;margin-top:16px;color:#334155;font-size:13px;font-weight:600;text-align:left;line-height:1.4;}
        .claim-process-confirmation input{width:17px;height:17px;flex:0 0 auto;margin:1px 0 0;accent-color:#1A237E;cursor:pointer;}
        .claim-process-dialog.is-loading .claim-process-confirm{opacity:.65;pointer-events:none;}
        @media (max-width:520px){.claim-process-dialog .senior-modal-body{padding:18px;}}

        /* Match the senior masterlist table treatment. */
        .table-card{
            width:100%;
            padding:1rem;
            margin-bottom:1rem;
            border:1px solid var(--border-light);
            border-radius:12px;
            background:#fff;
            box-shadow:none;
        }
        .table-card-header{
            padding:0 0 1rem;
            border-bottom:0;
            background:transparent;
        }
        .archive-table-wrap{
            max-height:500px;
            border:2px solid #CBD5E1;
            border-radius:8px;
            overflow:auto;
        }
        .archive-table{
            min-width:0;
            table-layout:auto;
            font-size:14px;
        }
        .archive-table thead{background:#E2E8F0;}
        .archive-table th{
            padding:14px 16px;
            font-size:12px;
            font-weight:600;
            letter-spacing:.03em;
            color:#1E293B;
            border-bottom:2px solid #94A3B8;
        }
        .archive-table td{
            padding:14px 16px;
            font-size:13px;
            color:var(--text-primary);
            border-bottom:1px solid #CBD5E1;
            white-space:normal;
            word-break:break-word;
        }
        .archive-table tbody tr:hover{background:#F8FAFC;}
        .archive-table tbody tr:last-child td{border-bottom:none;}
        .archive-table input[type="checkbox"]{width:16px;height:16px;}
        .btn-view-senior{width:34px;height:34px;border-radius:8px;box-shadow:none;}
        .badge{padding:6px 12px;font-size:12px;font-weight:500;}
        @media (min-width:1200px){
            .table-card{flex:1;min-height:0;display:flex;flex-direction:column;overflow:hidden;}
            .archive-table-wrap{flex:1;min-height:0;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'in-between', 'mobileSubtitle' => 'In-Between Benefits'])

    <div class="main">
        <div class="main-scroll">
            <div class="page-container">
            
            <!-- Page Header Card -->
            <div class="history-header-card">
                <div class="history-header-left">
                    <h1><i data-lucide="gift" style="width:26px;height:26px;color:var(--primary);"></i> In-Between Birthday Cash Gift</h1>
                    <p>Eligible senior citizens for milestone interval cash gifts (ages 81–84, 86–89, 91–94, 96–99)</p>
                </div>
                <div class="history-header-actions">
                    <a href="/admin/senior/in-between/history" class="btn btn-primary">
                        <i data-lucide="history"></i> Benefit History
                    </a>
                </div>
            </div>

            <!-- Metrics Summary -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-icon" style="background:#EEF2FF;color:#3730A3;">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-label">Total Eligible Seniors</div>
                        <div class="metric-value">{{ number_format($totalEligible ?? $seniors->total()) }}</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon" style="background:#ECFDF5;color:#059669;">
                        <i data-lucide="banknote"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-label">Estimated Budget</div>
                        <div class="metric-value">₱{{ number_format($totalEstimatedBudget ?? ($seniors->total() * 1000), 2) }}</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon" style="background:#FEF3C7;color:#D97706;">
                        <i data-lucide="check-circle"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-label">Claimed Benefits</div>
                        <div class="metric-value">{{ number_format($totalClaimed ?? 0) }}</div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon" style="background:#F0FDF4;color:#16A34A;">
                        <i data-lucide="gift"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-label">Unclaimed / Pending</div>
                        <div class="metric-value">{{ number_format(max(0, ($totalEligible ?? $seniors->total()) - ($totalClaimed ?? 0))) }}</div>
                    </div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="filter-panel">
                <div class="filter-panel-header">
                    <div class="filter-panel-title">
                        <i data-lucide="filter"></i> Filter Eligible Beneficiaries
                    </div>
                </div>
                <form method="GET" action="{{ route('admin.senior.in-between.eligibility-list') }}" id="filterForm">
                    <div class="filter-grid">
                        <div class="filter-field">
                            <label class="filter-label" for="searchInput">Search Beneficiaries</label>
                            <div class="input-group">
                                <input type="text" id="searchInput" name="search" placeholder="Search by name, control #, or senior ID..." value="{{ request('search') }}">
                                <button type="submit" class="search-btn" aria-label="Search" title="Search">
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
                            <label class="filter-label" for="intervalFilter">Interval</label>
                            <select id="intervalFilter" name="interval" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Intervals</option>
                                <option value="81-84" {{ request('interval') == '81-84' ? 'selected' : '' }}>81–84</option>
                                <option value="86-89" {{ request('interval') == '86-89' ? 'selected' : '' }}>86–89</option>
                                <option value="91-94" {{ request('interval') == '91-94' ? 'selected' : '' }}>91–94</option>
                                <option value="96-99" {{ request('interval') == '96-99' ? 'selected' : '' }}>96–99</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label class="filter-label" for="statusFilter">Status</label>
                            <select id="statusFilter" name="status" class="filter-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="eligible" {{ request('status') == 'eligible' ? 'selected' : '' }}>Eligible</option>
                                <option value="claimed" {{ request('status') == 'claimed' ? 'selected' : '' }}>Claimed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>

                        <div class="filter-btn-group">
                            <button type="submit" class="btn btn-primary" title="Apply Filter">
                                <i data-lucide="filter"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'barangay', 'interval', 'status']))
                                <a href="{{ route('admin.senior.in-between.eligibility-list') }}" class="btn btn-clear" title="Reset Filters">
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
                    <h3 class="table-card-title">
                        <i data-lucide="users"></i> Eligible Beneficiaries
                        <span class="table-count-badge">{{ $seniors->total() }}</span>
                    </h3>
                    <div id="bulkHeaderActions" style="display:none; align-items:center; gap:8px;">
                        <span id="selectedCountBadge" style="font-size:12px; font-weight:700; color:#3730A3; background:#EEF2FF; padding:4px 10px; border-radius:999px; border:1px solid #C7D2FE;">
                            <span id="selectedCount">0</span> selected
                        </span>
                        <button type="button" id="bulkActionButton" class="btn btn-primary btn-sm" onclick="showBulkActionPopup()">
                            <i data-lucide="list-checks"></i> Bulk Actions
                        </button>
                        <button type="button" id="clearSelectionsBtn" class="btn btn-clear btn-sm" onclick="clearSelections()">
                            <i data-lucide="x"></i> Clear
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
                                <th class="col-control" style="width:14%;">Control Number</th>
                                <th class="col-senior" style="width:16%;">Senior Citizen</th>
                                <th class="col-barangay" style="width:12%;">Barangay</th>
                                <th class="col-age" style="width:5%; text-align:center;">Age</th>
                                <th class="col-amount" style="width:10%; text-align:right;">Amount</th>
                                <th class="col-status" style="width:9%; text-align:center;">Status</th>
                                <th class="col-action" style="width:8%; text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($seniors as $senior)
                                <tr>
                                    <td class="col-check" data-label="Select" style="text-align:center;">
                                        <input type="checkbox" class="senior-checkbox" 
                                            data-id="{{ $senior->id }}" 
                                            data-name="{{ $senior->full_name }}" 
                                            data-eligible="{{ $senior->is_eligible ? '1' : '0' }}" 
                                            data-has-claimed="{{ $senior->has_claimed ? '1' : '0' }}"
                                            onchange="updateBulkActions()"
                                            style="cursor:pointer; width:16px; height:16px; accent-color:var(--primary);">
                                    </td>
                                    <td class="col-control" data-label="Control Number">
                                        <span class="ref-badge" style="background:#F1F5F9; color:#334155; border-color:#CBD5E1;">{{ $senior->control_number ?? $senior->senior_id_number ?? ('#' . $senior->id) }}</span>
                                    </td>
                                    <td class="col-senior" data-label="Senior Citizen">
                                        <div style="font-weight:600; color:#0F172A;">{{ $senior->full_name }}</div>
                                    </td>
                                    <td class="col-barangay" data-label="Barangay">
                                        <div style="font-size:12.5px; color:#334155; display:flex; align-items:center; gap:5px;">
                                            <i data-lucide="map-pin" style="width:13px; height:13px; color:#64748B; flex-shrink:0;"></i>
                                            <span>{{ $senior->barangay ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="col-age" data-label="Age" style="text-align:center; font-weight:600; color:#0F172A;">
                                        {{ $senior->age }}
                                    </td>
                                    <td class="col-amount" data-label="Amount" style="text-align:right; font-weight:700; color:#059669; font-variant-numeric:tabular-nums; white-space:nowrap;">
                                        ₱{{ number_format($senior->benefit_amount ?? 1000, 2) }}
                                    </td>
                                    <td class="col-status" data-label="Status" style="text-align:center;">
                                        @if($senior->has_claimed)
                                            <span class="badge badge-released">
                                                <span class="badge-dot"></span> Claimed
                                            </span>
                                        @elseif($senior->is_pending ?? false)
                                            <span class="badge badge-pending">
                                                <span class="badge-dot"></span> Pending
                                            </span>
                                        @elseif($senior->is_eligible)
                                            <span class="badge badge-approved">
                                                <span class="badge-dot"></span> Eligible
                                            </span>
                                        @else
                                            <span class="badge badge-rejected">
                                                <span class="badge-dot"></span> Not Eligible
                                            </span>
                                        @endif
                                    </td>
                                    <td class="col-action" data-label="Action" style="text-align:center;">
                                        <button type="button" class="btn-icon btn-view-senior" title="View Details"
                                            data-id="{{ $senior->id }}"
                                            data-name="{{ $senior->full_name }}"
                                            data-senior-id="{{ $senior->senior_id_number ?? $senior->control_number ?? ('#' . $senior->id) }}"
                                            data-control-number="{{ $senior->control_number ?? $senior->senior_id_number ?? ('#' . $senior->id) }}"
                                            data-barangay="{{ $senior->barangay ?? 'N/A' }}"
                                            data-age="{{ $senior->age }}"
                                            data-interval="{{ $senior->eligibility_interval ?? 'N/A' }}"
                                            data-amount="₱{{ number_format($senior->benefit_amount ?? 1000, 2) }}"
                                            data-status="{{ $senior->has_claimed ? 'Claimed' : ($senior->is_pending ?? false ? 'Pending' : ($senior->is_eligible ? 'Eligible' : 'Not Eligible')) }}"
                                            data-status-raw="{{ $senior->has_claimed ? 'released' : ($senior->is_pending ?? false ? 'pending' : ($senior->is_eligible ? 'approved' : 'rejected')) }}"
                                            data-is-eligible="{{ $senior->is_eligible ? '1' : '0' }}"
                                            data-has-claimed="{{ $senior->has_claimed ? '1' : '0' }}">
                                            <i data-lucide="eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding:56px 20px; text-align:center;">
                                        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:12px;">
                                            <div style="width:60px; height:60px; border-radius:50%; background:#F1F5F9; display:flex; align-items:center; justify-content:center; color:#94A3B8;">
                                                <i data-lucide="users" style="width:28px; height:28px;"></i>
                                            </div>
                                            <div style="font-size:16px; font-weight:700; color:#1E293B;">No eligible senior citizens found</div>
                                            <div style="font-size:13px; color:#64748B; max-width:360px;">
                                                No records match your selected filters. Try resetting the filters or check milestone age brackets (81–84, 86–89, 91–94, 96–99).
                                            </div>
                                            @if(request()->hasAny(['search', 'barangay', 'interval', 'status']))
                                                <a href="{{ route('admin.senior.in-between.eligibility-list') }}" class="btn btn-primary" style="margin-top:8px;">
                                                    <i data-lucide="rotate-ccw"></i> Reset Filters
                                                </a>
                                            @endif
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
                    @if($seniors->total() === 0)
                        Showing 0 of 0 Seniors
                    @else
                        Showing {{ $seniors->firstItem() }}–{{ $seniors->lastItem() }} of {{ $seniors->total() }} Seniors
                    @endif
                </div>
                <div class="sc-pagination-controls">
                    @if($seniors->hasPages())
                        @if($seniors->onFirstPage())
                            <span class="sc-page-btn" disabled>Previous</span>
                        @else
                            <a href="{{ $seniors->appends(request()->except('page'))->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                        @endif

                        <span class="sc-page-btn active">{{ $seniors->currentPage() }}</span>

                        @if($seniors->hasMorePages())
                            <a href="{{ $seniors->appends(request()->except('page'))->nextPageUrl() }}" class="sc-page-btn">Next</a>
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

<!-- Bulk Action Modal -->
<div class="modal-overlay" id="bulkActionModal">
    <div class="modal-panel">
        <div class="modal-panel-header">
            <h5><i data-lucide="list-checks"></i> Bulk Actions</h5>
            <button type="button" class="modal-close" onclick="closeBulkModal()"><i data-lucide="x"></i></button>
        </div>
        <div class="modal-panel-body">
            <div class="modal-actions-list">
                <div id="bulkModalSummary" style="font-size:13px; color:#475569; padding:4px 0 8px; border-bottom:1px solid #E2E8F0; margin-bottom:4px;"></div>
                <button type="button" id="bulkBtnProcess" class="modal-btn modal-btn-claim" onclick="bulkProcessClaims(event)">
                    <i data-lucide="gift"></i> <span id="bulkBtnProcessText">Process Selected Claims</span>
                </button>
                <button type="button" id="bulkBtnArchive" class="modal-btn modal-btn-danger" onclick="bulkArchive(event)">
                    <i data-lucide="archive"></i> <span id="bulkBtnArchiveText">Archive Selected</span>
                </button>
                <button type="button" id="bulkBtnExport" class="modal-btn modal-btn-indigo" onclick="exportPdf(event)">
                    <i data-lucide="file-output"></i> <span id="bulkBtnExportText">Export Selected (PDF)</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ======================== SENIOR DETAILS MODAL (Masterlist Style) ======================== -->
<div id="seniorDetailsModal" class="senior-modal-backdrop">
    <div class="senior-modal-dialog">
        <div class="senior-modal-header">
            <h5 class="senior-modal-title">
                <i data-lucide="user" style="width:20px;height:20px;"></i>
                Benefit Transaction Details
            </h5>
            <button onclick="closeSeniorModal()" class="senior-modal-close" aria-label="Close modal">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div class="senior-modal-body">
            <div class="senior-modal-grid">
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Senior ID</label>
                    <div class="senior-modal-value" id="modalSeniorId">—</div>
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
                    <label class="senior-modal-label">Full Name</label>
                    <div class="senior-modal-value" id="modalFullName" style="font-weight:700;">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Barangay</label>
                    <div class="senior-modal-value" id="modalBarangay">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Age</label>
                    <div class="senior-modal-value" id="modalAge">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Eligibility Interval</label>
                    <div class="senior-modal-value" id="modalInterval">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Benefit Amount</label>
                    <div class="senior-modal-value" id="modalAmount">—</div>
                </div>
            </div>
        </div>
        <div class="senior-modal-footer" id="seniorModalFooter">
            <button type="button" onclick="closeSeniorModal()" class="btn btn-outline" style="height:38px; padding:0 22px;">
                Close
            </button>
            <button type="button" id="modalProcessClaimBtn" onclick="processClaimFromModal()" class="btn btn-primary" style="height:38px; padding:0 22px; display:none;">
                <i data-lucide="gift" style="width:16px;height:16px;"></i> Process Claim
            </button>
        </div>
    </div>
</div>

<!-- Process Claim Modal -->
<div id="processClaimModal" class="senior-modal-backdrop">
    <div class="senior-modal-dialog claim-process-dialog">
        <div class="senior-modal-header">
            <h5 class="senior-modal-title">
                <i data-lucide="gift" style="width:20px;height:20px;"></i>
                Process In-Between Birthday Cash Gift
            </h5>
            <button type="button" onclick="closeProcessClaimModal()" class="senior-modal-close" aria-label="Close modal">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div class="senior-modal-body">
            <div id="processClaimSummary" class="claim-process-summary">
                <div class="claim-process-row"><span class="claim-process-label">Senior ID</span><span id="processModalSeniorId" class="claim-process-value claim-process-id">—</span></div>
                <div class="claim-process-row"><span class="claim-process-label">Name</span><span id="processModalName" class="claim-process-value">—</span></div>
                <div class="claim-process-row"><span class="claim-process-label">Age</span><span id="processModalAge" class="claim-process-value">—</span></div>
                <div class="claim-process-row"><span class="claim-process-label">Amount</span><span id="processModalAmount" class="claim-process-value claim-process-amount">—</span></div>
            </div>
            <div id="processClaimSuccess" class="claim-process-success">Claim processed successfully. Reloading the eligibility list...</div>
            <div id="processClaimError" class="claim-process-error" role="alert"></div>
            <div class="claim-process-remarks">
                <label for="processModalRemarks">Remarks (Optional)</label>
                <input id="processModalRemarks" type="text" placeholder="e.g. Approved and processed">
            </div>
            <label class="claim-process-confirmation" for="processModalConfirm">
                <input id="processModalConfirm" type="checkbox">
                <span>I am sure I want to process this claim.</span>
            </label>
        </div>
        <div class="senior-modal-footer">
            <button type="button" onclick="closeProcessClaimModal()" class="btn btn-outline" style="height:38px;padding:0 22px;">Cancel</button>
            <button type="button" id="processModalSubmit" onclick="submitProcessClaim()" class="btn btn-primary" style="height:38px;padding:0 22px;">
                <i data-lucide="gift" style="width:16px;height:16px;"></i> Process Claim
            </button>
        </div>
    </div>
</div>

<script>
    window.selectAllMatching = false;

    function showBulkActionPopup() {
        const selected = document.querySelectorAll('.senior-checkbox:checked');
        const savedIds = localStorage.getItem('selectedInBetweenSeniorIds');
        const ids = savedIds ? JSON.parse(savedIds) : [];
        if (selected.length === 0 && ids.length === 0 && !window.selectAllMatching) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }

        // Analyze current page selections
        let eligibleCount = 0;
        let claimedCount = 0;
        let totalSelected = 0;

        selected.forEach(cb => {
            totalSelected++;
            if (cb.dataset.eligible === '1' && cb.dataset.hasClaimed !== '1') eligibleCount++;
            if (cb.dataset.hasClaimed === '1') claimedCount++;
        });

        // If select-all-matching, use server-side totals
        if (window.selectAllMatching) {
            totalSelected = {{ $seniors->total() ?? 0 }};
            eligibleCount = {{ $totalEligible ?? $seniors->total() }};
            claimedCount = {{ $totalClaimed ?? 0 }};
            eligibleCount = Math.max(0, eligibleCount - claimedCount);
        }

        // Update summary text
        const summary = document.getElementById('bulkModalSummary');
        if (summary) {
            let parts = [];
            parts.push(`<strong>${totalSelected}</strong> senior(s) selected`);
            if (eligibleCount > 0) parts.push(`<span style="color:#059669;"><strong>${eligibleCount}</strong> eligible for claim</span>`);
            if (claimedCount > 0) parts.push(`<span style="color:#64748B;"><strong>${claimedCount}</strong> already claimed</span>`);
            summary.innerHTML = parts.join(' · ');
        }

        // Show/hide Process Claims button
        const processBtn = document.getElementById('bulkBtnProcess');
        const processText = document.getElementById('bulkBtnProcessText');
        if (processBtn) {
            if (eligibleCount > 0) {
                processBtn.style.display = 'flex';
                processBtn.disabled = false;
                processBtn.style.opacity = '1';
                if (processText) processText.textContent = `Process ${eligibleCount} Eligible Claim${eligibleCount > 1 ? 's' : ''}`;
            } else {
                processBtn.style.display = 'none';
            }
        }

        // Update Archive button text
        const archiveText = document.getElementById('bulkBtnArchiveText');
        if (archiveText) archiveText.textContent = `Archive ${totalSelected} Selected`;

        // Update Export button text
        const exportText = document.getElementById('bulkBtnExportText');
        if (exportText) exportText.textContent = `Export ${totalSelected} Selected (PDF)`;

        document.getElementById('bulkActionModal').classList.add('active');
        lucide.createIcons();
    }

    function closeBulkModal() {
        document.getElementById('bulkActionModal').classList.remove('active');
    }

    function clearFilters() {
        document.getElementById('filterForm').reset();
        window.location.href = '{{ route('admin.senior.in-between.eligibility-list') }}';
    }

    // Toggle Select All on current page
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        const notice = document.getElementById('selectAllPagesNotice');
        const total = {{ $seniors->total() ?? 0 }};
        const currentCount = {{ $seniors->count() ?? 0 }};
        const hasMorePages = total > currentCount;

        if (selectAll.checked) {
            window.selectAllMatching = true;
        } else {
            window.selectAllMatching = false;
        }

        updateBulkActions();
    }

    function updateBulkActions() {
        syncActionButtons();
    }

    function syncActionButtons() {
        const bulkHeader     = document.getElementById('bulkHeaderActions');
        const bulkBtn        = document.getElementById('bulkActionButton');
        const clearSelectBtn = document.getElementById('clearSelectionsBtn');
        const countSpan      = document.getElementById('selectedCount');

        const checkedBoxes      = document.querySelectorAll('.senior-checkbox:checked');
        const pageTotal         = document.querySelectorAll('.senior-checkbox').length;
        const totalMatching     = {{ $seniors->total() ?? 0 }};

        // Merge current page selections into localStorage
        const savedIds          = localStorage.getItem('selectedInBetweenSeniorIds');
        let allSelectedIds      = savedIds ? JSON.parse(savedIds) : [];
        const currentPageIds    = Array.from(checkedBoxes).map(cb => cb.dataset.id);
        const currentPageAllIds = Array.from(document.querySelectorAll('.senior-checkbox')).map(cb => cb.dataset.id);
        allSelectedIds = allSelectedIds.filter(id => !currentPageAllIds.includes(id));
        allSelectedIds = [...allSelectedIds, ...currentPageIds];
        localStorage.setItem('selectedInBetweenSeniorIds', JSON.stringify(allSelectedIds));

        const selectionCount = window.selectAllMatching ? totalMatching : allSelectedIds.length;

        if (countSpan) countSpan.textContent = selectionCount;

        if (checkedBoxes.length < pageTotal) {
            window.selectAllMatching = false;
            const notice = document.getElementById('selectAllPagesNotice');
            if (notice) notice.style.display = 'none';
        }

        if (selectionCount >= 1) {
            if (bulkHeader) bulkHeader.style.display = 'inline-flex';
            if (bulkBtn) {
                bulkBtn.style.display = 'inline-flex';
                bulkBtn.disabled = false;
            }
            if (clearSelectBtn) clearSelectBtn.style.display = 'inline-flex';
        } else {
            if (bulkHeader) bulkHeader.style.display = 'none';
            if (bulkBtn) {
                bulkBtn.style.display = 'none';
                bulkBtn.disabled = true;
            }
            if (clearSelectBtn) clearSelectBtn.style.display = 'none';
        }
    }

    function restoreSelections() {
        const savedIds = localStorage.getItem('selectedInBetweenSeniorIds');
        if (savedIds) {
            const ids = JSON.parse(savedIds);
            const checkboxes = document.querySelectorAll('.senior-checkbox');
            checkboxes.forEach(cb => {
                if (ids.includes(cb.dataset.id)) {
                    cb.checked = true;
                }
            });
            const selectAll = document.getElementById('selectAll');
            const pageTotal = checkboxes.length;
            const checkedCount = document.querySelectorAll('.senior-checkbox:checked').length;
            if (selectAll && pageTotal > 0) selectAll.checked = (checkedCount === pageTotal);
            syncActionButtons();
        }
    }

    function clearSelections() {
        localStorage.removeItem('selectedInBetweenSeniorIds');
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        checkboxes.forEach(cb => cb.checked = false);

        const bulkHeader = document.getElementById('bulkHeaderActions');
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;

        window.selectAllMatching = false;

        const countSpan = document.getElementById('selectedCount');
        if (countSpan) countSpan.textContent = '0';

        const selectAllPagesNotice = document.getElementById('selectAllPagesNotice');
        if (selectAllPagesNotice) selectAllPagesNotice.style.display = 'none';

        if (bulkHeader) bulkHeader.style.display = 'none';
    }

    // ── Bulk Action: Process Claims ──
    function bulkProcessClaims(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        closeBulkModal();

        const savedIds = localStorage.getItem('selectedInBetweenSeniorIds');
        const ids = savedIds ? JSON.parse(savedIds) : [];

        if (ids.length === 0 && !window.selectAllMatching) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }

        const count = window.selectAllMatching ? {{ $seniors->total() ?? 0 }} : ids.length;

        Swal.fire({
            title: 'Process Selected Claims?',
            text: `You are about to process in-between birthday cash gift claims for ${count} selected senior(s). Ineligible or already-claimed seniors will be automatically skipped.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Process Claims',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const payload = window.selectAllMatching
                    ? {
                        select_all: true,
                        search: document.getElementById('searchInput')?.value || '',
                        barangay: document.getElementById('barangayFilter')?.value || '',
                        interval: document.getElementById('intervalFilter')?.value || '',
                        status: document.getElementById('statusFilter')?.value || ''
                    }
                    : { ids: ids };

                return fetch('{{ route('admin.senior.in-between.bulk-process-claims') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json().then(data => {
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Processing failed');
                    }
                    return data;
                }))
                .catch(error => {
                    Swal.showValidationMessage(error.message || 'An error occurred.');
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                clearSelections();
                Swal.fire({
                    title: 'Claims Processed!',
                    text: result.value.message || 'Selected claims have been processed successfully.',
                    icon: 'success',
                    confirmButtonColor: '#1A237E'
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    // ── Bulk Action: Archive Selected ──
    function bulkArchive(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        closeBulkModal();

        const savedIds = localStorage.getItem('selectedInBetweenSeniorIds');
        const ids = savedIds ? JSON.parse(savedIds) : [];

        if (ids.length === 0 && !window.selectAllMatching) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }

        const count = window.selectAllMatching ? {{ $seniors->total() ?? 0 }} : ids.length;

        Swal.fire({
            title: 'Archive Selected Records?',
            text: `You are about to archive ${count} record(s). This action can be undone from the archive page.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Archive',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const payload = window.selectAllMatching
                    ? { select_all: true, barangay: `{{ request('barangay') ?? '' }}`, search: `{{ request('search') ?? '' }}` }
                    : { ids: ids };

                fetch('{{ route('admin.senior.bulk-archive') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clearSelections();
                        Swal.fire('Archived!', 'Selected records have been archived.', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire('Error', data.message || 'Failed to archive records.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'An error occurred while archiving records.', 'error');
                });
            }
        });
    }

    // ── Bulk Action: Print Selected IDs ──
    function bulkPrintIdCards(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        closeBulkModal();

        const selectAll = document.getElementById('selectAll');
        const savedIds = localStorage.getItem('selectedInBetweenSeniorIds');
        const ids = savedIds ? JSON.parse(savedIds) : [];

        if (ids.length === 0 && !selectAll.checked) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }

        const count = selectAll.checked ? {{ $seniors->total() ?? 0 }} : ids.length;

        Swal.fire({
            title: 'Print Selected IDs?',
            text: `You are about to generate ID cards for ${count} senior(s). Continue?`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Generate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.senior.bulk-print-ids') }}';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                if (selectAll.checked) {
                    const searchInput = document.getElementById('searchInput');
                    const barangayFilter = document.getElementById('barangayFilter');
                    
                    if (searchInput && searchInput.value) {
                        const searchInput2 = document.createElement('input');
                        searchInput2.type = 'hidden';
                        searchInput2.name = 'search';
                        searchInput2.value = searchInput.value;
                        form.appendChild(searchInput2);
                    }
                    
                    if (barangayFilter && barangayFilter.value) {
                        const barangayInput = document.createElement('input');
                        barangayInput.type = 'hidden';
                        barangayInput.name = 'barangay';
                        barangayInput.value = barangayFilter.value;
                        form.appendChild(barangayInput);
                    }
                    
                    const selectAllInput = document.createElement('input');
                    selectAllInput.type = 'hidden';
                    selectAllInput.name = 'select_all';
                    selectAllInput.value = '1';
                    form.appendChild(selectAllInput);
                } else {
                    const idsInput = document.createElement('input');
                    idsInput.type = 'hidden';
                    idsInput.name = 'ids';
                    idsInput.value = JSON.stringify(ids);
                    form.appendChild(idsInput);
                }

                clearSelections();
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
        });
    }

    // ── Bulk Action: Export PDF ──
    function triggerFileDownload(blob, filename) {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(() => {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }, 100);
    }

    async function exportPdf(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        closeBulkModal();

        const savedIds = localStorage.getItem('selectedInBetweenSeniorIds');
        const ids = savedIds ? JSON.parse(savedIds) : Array.from(document.querySelectorAll('.senior-checkbox:checked')).map(cb => cb.dataset.id);
        
        if (ids.length === 0 && !window.selectAllMatching) {
            Swal.fire('No Selection', 'Please select at least one record to export.', 'warning');
            return;
        }

        try {
            const payload = window.selectAllMatching
                ? {
                    select_all: true,
                    search: document.getElementById('searchInput')?.value || '',
                    barangay: document.getElementById('barangayFilter')?.value || '',
                    interval: document.getElementById('intervalFilter')?.value || ''
                }
                : { ids };
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const dataResponse = await fetch('/admin/senior/in-between/eligibility-export-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : ''
                },
                body: JSON.stringify(payload)
            });
            if (!dataResponse.ok) throw new Error('Unable to retrieve the selected records.');
            const exportData = await dataResponse.json();
            const exportRecords = exportData.records || [];
            if (exportRecords.length === 0) throw new Error('No records were found for this export.');

            // Create a professional government-style PDF template with logos
            const printContent = `
                <html>
                <head>
                    <title>In-Between Benefits Export</title>
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
                        .data-table tbody tr:nth-child(even) {
                            background: #F8FAFC;
                        }
                        .footer {
                            margin-top: 15px;
                            border-top: 1px solid #E2E8F0;
                            padding-top: 8px;
                            font-size: 9.5px;
                            color: #64748B;
                            text-align: center;
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
                                <div style="font-size: 10px; color: #475569; margin-top: 2px;">In-Between Birthday Cash Gift - Eligibility List</div>
                            </td>
                            <td class="logo-cell">
                                <img src="/images/dswd.png" style="width: 72px; height: 72px; object-fit: contain; display: block; margin: 0 auto;" alt="DSWD Logo" onerror="this.style.display='none'">
                            </td>
                        </tr>
                    </table>

                    <div class="line"></div>
                    <div class="line2"></div>

                    <div class="report-title">
                        <h3>In-Between Birthday Cash Gift Eligibility List</h3>
                    </div>
                    
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Senior ID</th>
                                <th>Control Number</th>
                                <th>Name</th>
                                <th>Barangay</th>
                                <th>Birth Date</th>
                                <th>Age</th>
                                <th>Amount</th>
                                <th>Interval</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${exportRecords.map(record => `<tr>
                                <td>${record.senior_id}</td>
                                <td>${record.control_number}</td>
                                <td>${record.full_name}</td>
                                <td>${record.barangay}</td>
                                <td>${record.birth_date}</td>
                                <td>${record.age}</td>
                                <td>₱${Number(record.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                <td>${record.interval}</td>
                                <td>${record.status}</td>
                            </tr>`).join('')}
                        </tbody>
                    </table>
                    
                    <div class="footer">
                        This document is generated by the MSWDO Silang Senior Citizen Management System and is intended for official use only.
                    </div>
                    
                    <div class="no-print" style="margin-top: 20px; color: #666; font-size: 12px;">
                        Press Ctrl+P to save as PDF, or use "Save as PDF" in the print dialog.
                    </div>
                </body>
                </html>
            `;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Trigger print dialog immediately
            printWindow.onload = function() {
                printWindow.print();
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

    // Auto-submit form when select filters change
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Restore selections from localStorage
        restoreSelections();

        const modal = document.getElementById('bulkActionModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeBulkModal();
            });
        }

        // Delegated click handler for btn-view-senior (eye icon buttons)
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-view-senior');
            if (!btn) return;
            openSeniorDetails({
                id: btn.dataset.id,
                name: btn.dataset.name,
                senior_id: btn.dataset.seniorId,
                control_number: btn.dataset.controlNumber,
                barangay: btn.dataset.barangay,
                age: btn.dataset.age,
                interval: btn.dataset.interval,
                amount: btn.dataset.amount,
                status: btn.dataset.status,
                status_raw: btn.dataset.statusRaw,
                is_eligible: btn.dataset.isEligible,
                has_claimed: btn.dataset.hasClaimed
            });
        });

        // Senior details modal backdrop click
        const seniorModal = document.getElementById('seniorDetailsModal');
        if (seniorModal) {
            seniorModal.addEventListener('click', function(e) {
                if (e.target === this) closeSeniorModal();
            });
        }

        const processModal = document.getElementById('processClaimModal');
        if (processModal) {
            processModal.addEventListener('click', function(e) {
                if (e.target === this) closeProcessClaimModal();
            });
        }

        // Escape key closes modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSeniorModal();
                closeProcessClaimModal();
                closeBulkModal();
            }
        });

        const filters = ['barangayFilter', 'intervalFilter', 'statusFilter'];
        filters.forEach(filterId => {
            const filter = document.getElementById(filterId);
            if (filter) {
                filter.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
        });

        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });
        @endif
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK'
            });
        @endif
    });

    // ── Senior Details Modal ──
    let currentModalSeniorData = null;

    function openSeniorDetails(data) {
        currentModalSeniorData = data;

        document.getElementById('modalSeniorId').innerHTML = `<span class="ref-badge">${data.senior_id}</span>`;
        document.getElementById('modalControlNumber').innerHTML = `<span class="ref-badge" style="background:#F1F5F9; color:#334155; border-color:#CBD5E1;">${data.control_number}</span>`;

        const statusColors = {
            approved: { bg: '#EEF2FF', text: '#3730A3', border: '#C7D2FE', dot: '#4F46E5' },
            released: { bg: '#ECFDF5', text: '#065F46', border: '#A7F3D0', dot: '#10B981' },
            pending: { bg: '#FEF3C7', text: '#92400E', border: '#FDE68A', dot: '#F59E0B' },
            rejected: { bg: '#FEE2E2', text: '#991B1B', border: '#FECACA', dot: '#EF4444' }
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
        document.getElementById('modalInterval').innerHTML = `<span class="interval-badge">${data.interval || '—'}</span>`;
        document.getElementById('modalAmount').innerHTML = `<span style="color:#059669; font-weight:800; font-size:1.05rem;">${data.amount || '—'}</span>`;

        // Show/hide Process Claim button based on eligibility
        const processBtn = document.getElementById('modalProcessClaimBtn');
        if (processBtn) {
            processBtn.style.display = (data.is_eligible === '1' && data.has_claimed !== '1') ? 'inline-flex' : 'none';
        }

        const modal = document.getElementById('seniorDetailsModal');
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => { modal.style.opacity = '1'; }, 10);
        lucide.createIcons();
    }

    function closeSeniorModal() {
        const modal = document.getElementById('seniorDetailsModal');
        if (!modal) return;
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 200);
    }

    function processClaimFromModal() {
        if (!currentModalSeniorData) return;
        closeSeniorModal();
        const d = currentModalSeniorData;
        processClaim(
            d.id,
            d.name,
            d.age,
            d.interval,
            parseFloat(d.amount.replace(/[^\d.]/g, '')) || 1000,
            d.senior_id
        );
    }

    let processClaimData = null;

    async function processClaim(seniorId, fullName, age, interval, amount, seniorIdNum) {
        let displayName = fullName || '';
        let displayAge = age || '';
        let displayInterval = interval || '';
        let displayAmountNum = amount || 1000;
        let displaySeniorId = seniorIdNum || ('#' + seniorId);

        if (!displayName || !displayAge || !displayInterval) {
            try {
                const checkRes = await fetch(`/admin/senior/in-between/check-eligibility/${seniorId}`);
                const data = await checkRes.json();
                if (!data.is_eligible) {
                    showProcessClaimError(data.reason || 'This senior citizen is not currently eligible for the in-between birthday cash gift.');
                    return;
                }
                displaySeniorId = data.senior_id || displaySeniorId;
                displayName = data.full_name;
                displayAge = data.current_age;
                displayInterval = data.eligibility_interval;
                displayAmountNum = data.benefit_amount || 1000;
            } catch (error) {
                showProcessClaimError('Unable to retrieve senior citizen details. Please check your connection and try again.');
                return;
            }
        }

        processClaimData = { seniorId, displaySeniorId, displayName, displayAge, displayInterval, displayAmountNum };
        document.getElementById('processModalSeniorId').textContent = displaySeniorId;
        document.getElementById('processModalName').textContent = displayName;
        document.getElementById('processModalAge').textContent = `${displayAge} yrs old`;
        document.getElementById('processModalAmount').textContent = '₱' + Number(displayAmountNum).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('processModalRemarks').value = '';
        document.getElementById('processModalConfirm').checked = false;
        document.getElementById('processClaimError').style.display = 'none';
        document.getElementById('processClaimSuccess').style.display = 'none';
        document.getElementById('processClaimSummary').style.display = 'block';
        document.querySelector('#processClaimModal .claim-process-remarks').style.display = 'block';
        document.getElementById('processModalSubmit').style.display = 'inline-flex';
        showProcessClaimModal();
    }

    function showProcessClaimModal() {
        const modal = document.getElementById('processClaimModal');
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => { modal.style.opacity = '1'; }, 10);
        lucide.createIcons();
    }

    function closeProcessClaimModal() {
        const modal = document.getElementById('processClaimModal');
        if (!modal || modal.classList.contains('is-loading')) return;
        modal.style.opacity = '0';
        setTimeout(() => { modal.style.display = 'none'; document.body.style.overflow = ''; }, 200);
    }

    function showProcessClaimError(message) {
        const error = document.getElementById('processClaimError');
        if (!error) return;
        error.textContent = message;
        error.style.display = 'block';
        showProcessClaimModal();
    }

    async function submitProcessClaim() {
        if (!processClaimData) return;
        const modal = document.getElementById('processClaimModal');
        const submit = document.getElementById('processModalSubmit');
        const remarks = document.getElementById('processModalRemarks').value;
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const confirmation = document.getElementById('processModalConfirm');
        if (!confirmation.checked) {
            const error = document.getElementById('processClaimError');
            error.textContent = 'Please confirm that you are sure before processing this claim.';
            error.style.display = 'block';
            return;
        }
        modal.classList.add('is-loading');
        submit.disabled = true;
        submit.innerHTML = '<i data-lucide="loader-circle" style="width:16px;height:16px;"></i> Processing...';
        lucide.createIcons();

        try {
            const response = await fetch(`/admin/senior/in-between/process-claim/${processClaimData.seniorId}`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : '', 'Accept': 'application/json'},
                body: JSON.stringify({ confirmation: true, remarks })
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Failed to process claim.');
            document.getElementById('processClaimSummary').style.display = 'none';
            document.querySelector('#processClaimModal .claim-process-remarks').style.display = 'none';
            document.getElementById('processClaimSuccess').style.display = 'block';
            setTimeout(() => window.location.reload(), 1400);
        } catch (error) {
            modal.classList.remove('is-loading');
            submit.disabled = false;
            submit.innerHTML = '<i data-lucide="gift" style="width:16px;height:16px;"></i> Process Claim';
            document.getElementById('processClaimError').textContent = error.message || 'Error occurred while processing claim.';
            document.getElementById('processClaimError').style.display = 'block';
            lucide.createIcons();
        }
    }

    function viewClaim(seniorId) {
        window.location.href = `/admin/senior/in-between/senior-card/${seniorId}`;
    }
</script>
</body>
</html>
