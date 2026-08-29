<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Archived Senior Citizens</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--purple:#7C3AED;--purple-bg:#F3E8FF;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4,h5{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}

        /* ── Buttons ── */
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);height:44px;min-height:44px;text-decoration:none;white-space:nowrap;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn svg{width:16px;height:16px;}
        .btn-clear{background:var(--surface);color:var(--danger);border-color:var(--danger);font-weight:600;}
        .btn-clear:hover{border-color:var(--danger);color:var(--danger);}

        /* ── Summary / Filters ── */
        .section-spacing{margin-bottom:28px;}
        #summaryGrid{display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:stretch;}
        .filter-field{display:flex;flex-direction:column;justify-content:flex-end;min-width:0;gap:3px;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .filter-select{width:100%;height:44px;min-height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        .input-group{display:flex;align-items:center;}
        .input-group input{flex:1;min-width:0;height:44px;border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;padding:0 1rem;font-size:14px;color:var(--text-primary);background:var(--surface);transition:all .2s ease;font-family:inherit;}
        .input-group input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(30,58,138,.15);}
        .search-btn{background:var(--primary);color:#ffffff;border:none;padding:0 1.1rem;border-radius:0 8px 8px 0;cursor:pointer;height:44px;width:48px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:background .2s;}
        .search-btn:hover{background:var(--primary-hover);}
        .search-btn svg{width:18px;height:18px;}
        .bulk-actions-row{display:flex;gap:8px;align-items:center;min-width:0;}
        .bulk-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;background:#E0E7FF;color:#3730A3;border:1px solid #C7D2FE;cursor:pointer;transition:all .2s ease;font-family:inherit;height:44px;min-height:44px;flex:1;white-space:nowrap;}
        .bulk-btn:disabled{opacity:.45;cursor:not-allowed;}
        .bulk-btn svg{width:15px;height:15px;flex-shrink:0;}
        .bulk-count{background:#3730A3;color:white;padding:2px 8px;border-radius:10px;font-size:11px;margin-left:4px;}

        /* ── Table Card ── */
        .table-card-title{font-size:1.25rem;font-weight:700;color:var(--text-primary);margin:0 0 1.25rem 0;flex-shrink:0;padding:0 24px;}
        .table-responsive{width:100%;max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow);margin:0 24px 24px 0;}
        .mobile-select-all{margin:0 24px 10px 0;}
        .table-responsive table{width:100%;border-collapse:collapse;table-layout:auto;}
        .table-responsive thead th{padding:12px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);text-align:left;border-bottom:2px solid var(--border);background:var(--surface);white-space:nowrap;}
        .table-responsive tbody td{padding:12px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid var(--border);vertical-align:middle;white-space:normal;word-break:break-word;}
        .table-responsive tbody tr:last-child td{border-bottom:none;}
        .table-responsive tbody tr:hover td{background:var(--background);}
        .table-responsive input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}
        .td-name{font-weight:500;color:var(--text-primary);font-size:13px;}
        .td-addr{font-size:0.75rem;color:var(--text-secondary);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;}
        .control-no{font-weight:600;color:var(--text-primary);font-size:13px;}
        .sex-age-wrap{display:inline-flex;align-items:center;gap:6px;white-space:nowrap;}
        .sex-letter{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:#6B7280;color:white;font-size:11px;font-weight:700;flex-shrink:0;}
        .sex-sep{color:var(--text-muted);font-size:13px;}
        .badge-archived{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:500;white-space:nowrap;background:rgba(156,163,175,.15);color:#6B7280;}
        .btn-restore{display:inline-flex;align-items:center;justify-content:center;gap:4px;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:500;background:#ECFDF5;color:#16A34A;border:1px solid #6EE7B7;cursor:pointer;transition:all .2s ease;font-family:inherit;white-space:nowrap;text-decoration:none;}
        .btn-restore:hover{background:#D1FAE5;}
        .btn-restore svg{width:14px;height:14px;}

        /* ── Empty State ── */
        .empty-state-cell{padding:0 !important;}
        .empty-state{min-height:260px;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem 1.5rem;width:100%;}
        .empty-state [data-lucide]{width:56px;height:56px;color:#D1D5DB;margin-bottom:1rem;}
        .empty-state h5{color:#6B7280;font-weight:600;font-size:1rem;}
        .empty-state p{color:#9CA3AF;font-size:.85rem;margin-top:.25rem;max-width:28rem;text-align:center;}
        .empty-state a{display:inline-flex;align-items:center;gap:6px;background:var(--primary);color:#fff;border-radius:8px;font-weight:600;font-size:13px;padding:10px 16px;margin-top:14px;transition:background .2s;font-family:inherit;}
        .empty-state a:hover{background:var(--primary-hover);}
        .empty-state a svg{width:16px;height:16px;}

        /* ── Pagination ── */
        .pagination-wrap{display:flex;justify-content:center;flex-wrap:wrap;padding-top:1rem;margin-top:1rem;border-top:1px solid var(--border);}

        /* ── Mobile Select All ── */
        .mobile-select-all{display:none;align-items:center;gap:8px;padding:10px 12px;margin-bottom:10px;background:var(--surface);border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text-secondary);}
        .mobile-select-all input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}

        /* ── Flash Messages ── */
        .flash-message{display:flex;align-items:center;gap:12px;padding:.875rem 1.25rem;border-radius:10px;font-size:.875rem;font-weight:500;margin-bottom:1rem;animation:fadeIn .3s ease;}
        .flash-message svg{width:20px;height:20px;flex-shrink:0;}
        .flash-success{background:var(--success-bg);color:#166534;border:1px solid #BBF7D0;}
        .flash-error{background:var(--danger-bg);color:#991B1B;border:1px solid #FECACA;}
        .flash-close{margin-left:auto;cursor:pointer;opacity:.6;transition:opacity .2s;background:none;border:none;padding:0;line-height:0;}
        .flash-close:hover{opacity:1;}
        .flash-close svg{width:18px;height:18px;}

        /* ── Modal ── */
        .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
        .modal-overlay.active{display:flex;}
        .modal-panel{background:var(--surface);border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.15);width:90%;max-width:440px;overflow:hidden;transform:scale(.95);opacity:0;transition:all .2s ease;}
        .modal-overlay.active .modal-panel{transform:scale(1);opacity:1;}
        .modal-panel-header{padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#1A237E;color:#ffffff;}
        .modal-panel-header h5{font-size:1rem;font-weight:600;display:flex;align-items:center;gap:8px;color:#ffffff;margin:0;}
        .modal-close{color:#ffffff;cursor:pointer;border:none;background:transparent;padding:4px;border-radius:6px;display:flex;align-items:center;justify-content:center;opacity:0.8;transition:opacity 0.2s;}
        .modal-close:hover{opacity:1;}
        .modal-close svg{width:20px;height:20px;}
        .modal-panel-body{padding:1.5rem;}
        .modal-btn{display:flex;align-items:center;justify-content:center;gap:10px;border:none;border-radius:12px;padding:14px 20px;font-size:1rem;font-weight:500;transition:background .2s;cursor:pointer;width:100%;font-family:inherit;color:#fff;}
        .modal-btn svg{width:20px;height:20px;}
        .modal-btn-teal{background:#0f766e;}
        .modal-btn-teal:hover{background:#0d6e61;}
        .modal-btn-indigo{background:var(--primary);}
        .modal-btn-indigo:hover{background:var(--primary-hover);}

        /* ── Animations ── */
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}

        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ══════════════════════════════════════════════ */

        /* ── Extra Large (1400px+): summary in one row ── */
        @media (min-width:1400px){
            .section-spacing{margin-bottom:32px;}
            #summaryGrid{grid-template-columns:1fr 1fr auto;gap:16px;}
            .filter-label{font-size:13px !important;}
            .filter-select,.input-group input,.search-btn,.bulk-btn{height:48px !important;min-height:48px !important;}
            .filter-select{font-size:14px !important;}
            .input-group input{font-size:15px !important;}
            .search-btn{width:52px !important;}
            .bulk-btn{font-size:14px !important;padding:0 22px !important;}
            .table-card-title{font-size:1.5rem !important;padding:0 28px !important;}
            .table-responsive{margin:0 28px 28px 0 !important;}
            .mobile-select-all{margin:0 28px 10px 0 !important;}
            .table-responsive th{font-size:13px !important;padding:14px 18px !important;}
            .table-responsive td{font-size:15px !important;padding:16px 18px !important;}
            .badge-archived{font-size:13px !important;padding:5px 12px !important;}
            .btn-restore{font-size:14px !important;padding:8px 16px !important;}
            .td-name{font-size:15px !important;font-weight:600 !important;}
            .td-addr{font-size:13px !important;white-space:normal !important;overflow:visible !important;text-overflow:clip !important;}
        }

        /* ── Large Desktop (1200–1399px): summary in one row ── */
        @media (min-width:1200px) and (max-width:1399px){
            .section-spacing{margin-bottom:28px;}
            #summaryGrid{grid-template-columns:1fr 1fr auto;gap:14px;}
            .filter-label{font-size:12px !important;}
            .filter-select,.input-group input,.search-btn,.bulk-btn{height:46px !important;min-height:46px !important;}
            .filter-select{font-size:13px !important;}
            .input-group input{font-size:14px !important;}
            .search-btn{width:50px !important;}
            .bulk-btn{font-size:13px !important;padding:0 20px !important;}
            .table-card-title{font-size:1.3rem !important;padding:0 24px !important;}
            .table-responsive{margin:0 24px 24px 0 !important;}
            .mobile-select-all{margin:0 24px 10px 0 !important;}
            .table-responsive th{font-size:12px !important;padding:12px 16px !important;}
            .table-responsive td{font-size:14px !important;padding:14px 16px !important;}
            .badge-archived{font-size:12px !important;padding:4px 10px !important;}
            .btn-restore{font-size:13px !important;padding:7px 14px !important;}
            .td-name{font-size:14px !important;font-weight:600 !important;}
            .td-addr{font-size:12px !important;white-space:normal !important;overflow:visible !important;text-overflow:clip !important;}
        }

        /* ── Medium Desktop (992–1199px): summary in two rows ── */
        @media (min-width:992px) and (max-width:1199px){
            .section-spacing{margin-bottom:24px;}
            #summaryGrid{grid-template-columns:1fr 1fr;gap:14px;}
            .table-card-title{font-size:1.2rem !important;padding:0 20px !important;}
            .table-responsive{margin:0 20px 20px 0 !important;}
            .mobile-select-all{margin:0 20px 10px 0 !important;}
            .table-responsive th{padding:11px 14px !important;}
            .table-responsive td{padding:13px 14px !important;}
            .empty-state{min-height:220px;padding:2.5rem 1.5rem;}
            .td-name{font-size:13px !important;font-weight:600 !important;}
            .td-addr{font-size:11px !important;white-space:normal !important;overflow:visible !important;text-overflow:clip !important;}
        }

        /* ── Tablet (768–991px): summary in two rows, table stays a table ── */
        @media (min-width:768px) and (max-width:991px){
            .section-spacing{margin-bottom:20px;}
            #summaryGrid{grid-template-columns:1fr 1fr;gap:12px;}
            .table-card-title{font-size:1.1rem !important;padding:0 16px !important;}
            .table-responsive{margin:0 16px 16px 0 !important;}
            .mobile-select-all{margin:0 16px 10px 0 !important;}
            .table-responsive th{padding:10px 12px !important;}
            .table-responsive td{padding:12px 12px !important;}
            .empty-state{min-height:220px;padding:2.5rem 1.5rem;}
            .td-name{font-size:12px !important;font-weight:600 !important;}
            .td-addr{font-size:10px !important;white-space:normal !important;overflow:visible !important;text-overflow:clip !important;}
        }

        /* ── Large Mobile (576–767px): stacked filters, table → cards ── */
        @media (min-width:576px) and (max-width:767px){
            .section-spacing{margin-bottom:18px;}
            #summaryGrid{grid-template-columns:1fr;gap:12px;}
            .table-card-title{font-size:1.1rem !important;padding:0 14px !important;}
            .table-responsive{margin:0 14px 14px 0 !important;}
            .mobile-select-all{margin:0 14px 10px 0 !important;}
            .bulk-btn{flex:1 1 auto;}
            .empty-state{min-height:180px;padding:2rem 1rem;}
            .empty-state [data-lucide]{width:48px !important;height:48px !important;}
            .empty-state h5{font-size:.95rem !important;}
            .empty-state p{font-size:.8rem !important;}

            /* Table → stacked cards */
            .table-responsive{overflow:visible !important;border:none !important;background:transparent !important;box-shadow:none !important;border-radius:0 !important;}
            .table-responsive table{display:block !important;width:100% !important;min-width:0 !important;table-layout:auto !important;}
            .table-responsive thead{display:none !important;}
            .table-responsive tbody{display:block;}
            .table-responsive tbody tr{display:block;background:var(--surface);border:1px solid var(--border);border-radius:12px;margin-bottom:12px;padding:12px 14px;box-shadow:var(--shadow);}
            .table-responsive tbody tr:last-child{margin-bottom:0;}
            .table-responsive tbody td{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:8px 0;border:none;border-bottom:1px solid var(--border);font-size:13px !important;white-space:normal;word-break:break-word;text-align:right;}
            .table-responsive tbody td:last-child{border-bottom:none;}
            .table-responsive tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:11px;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:88px;text-align:left;}
            .table-responsive tbody td.col-check{justify-content:flex-end;border-bottom:none;padding:0 0 6px;}
            .table-responsive tbody td.col-check::before{display:none !important;}
            .table-responsive tbody td[data-label="Control No."]{white-space:nowrap;}
            .table-responsive tbody td[data-label="Action"]{justify-content:flex-end;border-bottom:none;padding-top:10px;}
            .table-responsive tbody td[data-label="Action"]::before{display:none !important;}
            .table-responsive tbody td.empty-state-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:0 !important;}
            .table-responsive tbody td.empty-state-cell::before{display:none !important;}
            .td-name{font-size:14px !important;}
            .td-addr{white-space:normal;overflow:visible;text-overflow:clip;}
            .btn-restore{min-height:42px;padding:9px 14px;font-size:13px;}
        }

        /* ── Mobile (<576px): stacked filters, table → cards ── */
        @media (max-width:575px){
            .section-spacing{margin-bottom:16px;}
            #summaryGrid{grid-template-columns:1fr;gap:10px;}
            .table-card-title{font-size:1rem !important;padding:0 12px !important;}
            .table-responsive{margin:0 12px 12px 0 !important;}
            .mobile-select-all{margin:0 12px 10px 0 !important;}
            .bulk-btn{flex:1 1 auto;}
            .empty-state{min-height:180px;padding:2rem 1rem;}
            .empty-state [data-lucide]{width:48px !important;height:48px !important;}
            .empty-state h5{font-size:.95rem !important;}
            .empty-state p{font-size:.8rem !important;}

            /* Table → stacked cards (matches masterlist) */
            .table-responsive{overflow:visible !important;border:none !important;background:transparent !important;box-shadow:none !important;border-radius:0 !important;}
            .table-responsive table{display:block !important;width:100% !important;min-width:0 !important;table-layout:auto !important;}
            .table-responsive thead{display:none !important;}
            .table-responsive tbody{display:block;}
            .table-responsive tbody tr{display:block;background:var(--surface);border:1px solid var(--border);border-radius:12px;margin-bottom:12px;padding:12px 14px;box-shadow:var(--shadow);}
            .table-responsive tbody tr:last-child{margin-bottom:0;}
            .table-responsive tbody td{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:8px 0;border:none;border-bottom:1px solid var(--border);font-size:13px !important;white-space:normal;word-break:break-word;text-align:right;}
            .table-responsive tbody td:last-child{border-bottom:none;}
            .table-responsive tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:11px;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:88px;text-align:left;}
            .table-responsive tbody td.col-check{justify-content:flex-end;border-bottom:none;padding:0 0 6px;}
            .table-responsive tbody td.col-check::before{display:none !important;}
            .table-responsive tbody td[data-label="Control No."]{white-space:nowrap;}
            .table-responsive tbody td[data-label="Action"]{justify-content:flex-end;border-bottom:none;padding-top:10px;}
            .table-responsive tbody td[data-label="Action"]::before{display:none !important;}
            .table-responsive tbody td.empty-state-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:0 !important;}
            .table-responsive tbody td.empty-state-cell::before{display:none !important;}
            .td-addr{white-space:normal;overflow:visible;text-overflow:clip;}
            .btn-restore{min-height:44px;padding:10px 14px;font-size:13px;}
        }

        /* ── Small Mobile (<480px): stacked filters, table → cards ── */
        @media (max-width:479px){
            .section-spacing{margin-bottom:14px;}
            #summaryGrid{grid-template-columns:1fr;gap:8px;}
            .table-card-title{font-size:.95rem !important;padding:0 10px !important;}
            .table-responsive{margin:0 10px 10px 0 !important;}
            .mobile-select-all{margin:0 10px 10px 0 !important;}
            .bulk-btn{flex:1 1 auto;}
            .mobile-select-all{display:flex;}
            .empty-state{min-height:160px;padding:1.5rem 0.75rem;}
            .empty-state [data-lucide]{width:44px !important;height:44px !important;}
            .empty-state h5{font-size:.9rem !important;}
            .empty-state p{font-size:.75rem !important;}

            /* Table → stacked cards */
            .table-responsive{overflow:visible !important;border:none !important;background:transparent !important;box-shadow:none !important;border-radius:0 !important;}
            .table-responsive table{display:block !important;width:100% !important;min-width:0 !important;table-layout:auto !important;}
            .table-responsive thead{display:none !important;}
            .table-responsive tbody{display:block;}
            .table-responsive tbody tr{display:block;background:var(--surface);border:1px solid var(--border);border-radius:10px;margin-bottom:10px;padding:10px 12px;box-shadow:var(--shadow);}
            .table-responsive tbody tr:last-child{margin-bottom:0;}
            .table-responsive tbody td{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:6px 0;border:none;border-bottom:1px solid var(--border);font-size:12px !important;white-space:normal;word-break:break-word;text-align:right;}
            .table-responsive tbody td:last-child{border-bottom:none;}
            .table-responsive tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:10px;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:70px;text-align:left;}
            .table-responsive tbody td.col-check{justify-content:flex-end;border-bottom:none;padding:0 0 6px;}
            .table-responsive tbody td.col-check::before{display:none !important;}
            .table-responsive tbody td[data-label="Control No."]{white-space:nowrap;}
            .table-responsive tbody td[data-label="Action"]{justify-content:flex-end;border-bottom:none;padding-top:10px;}
            .table-responsive tbody td[data-label="Action"]::before{display:none !important;}
            .table-responsive tbody td.empty-state-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:0 !important;}
            .table-responsive tbody td.empty-state-cell::before{display:none !important;}
            .td-name{font-size:13px !important;}
            .td-addr{white-space:normal;overflow:visible;text-overflow:clip;}
            .btn-restore{min-height:40px;padding:8px 12px;font-size:12px;}
        }

        /* ══════════════════════════════════════════════
           ARCHIVE TABLE — visual match with Social Case archive
           ══════════════════════════════════════════════ */
        .archive-panel-wrap{width:100%;padding:1rem;margin-bottom:1rem;border-radius:12px;background:var(--surface);border:1px solid var(--border);}
        .archive-table-wrap{border:1px solid var(--border);border-radius:8px;overflow-x:auto;-webkit-overflow-scrolling:touch;}
        .archive-table{width:100%;border-collapse:collapse;font-size:14px;}
        .archive-table thead th{padding:14px 16px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:var(--text-secondary);text-align:left;border-bottom:1px solid var(--border);background:var(--background);white-space:nowrap;}
        .archive-table tbody td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid var(--border);vertical-align:middle;white-space:normal;word-break:break-word;}
        .archive-table tbody tr:last-child td{border-bottom:none;}
        .archive-table input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}
        .archive-table .col-check{width:40px;text-align:center;}
        .mobile-select-all{margin:0 0 10px 0 !important;}

        .empty-row{background:transparent !important;border:none !important;box-shadow:none !important;padding:0 !important;margin:0 !important;}
        .empty-cell{padding:2.5rem 1rem !important;border:none !important;display:flex !important;flex-direction:column !important;align-items:center !important;justify-content:center !important;width:100% !important;}
        .empty-cell::before{display:none !important;}
        .empty-state-content{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;}
        .empty-icon-wrap{width:64px;height:64px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:#9CA3AF;}
        .empty-icon-wrap svg{width:32px;height:32px;}
        .empty-title{font-size:1.125rem;font-weight:700;color:#1F2937;margin-bottom:4px;}
        .empty-subtitle{font-size:0.875rem;color:#6B7280;}
        /* Tablet (768-1199px): empty state stays a full-width centered row */
        @media (min-width:768px) and (max-width:1199px){
            .archive-table tbody tr.empty-row{display:table-row !important;background:transparent !important;border:none !important;box-shadow:none !important;margin:0 !important;}
            .archive-table tbody tr.empty-row td.empty-cell{display:table-cell !important;padding:2.5rem 1.5rem !important;border:none !important;text-align:center !important;}
            .archive-table tbody tr.empty-row td.empty-cell::before{display:none !important;}
            .archive-table tbody tr.empty-row td.empty-cell .empty-state-content{align-items:center;justify-content:center;}
        }
        @media (min-width:1200px){
            .archive-table tbody tr.empty-row{display:table-row !important;background:transparent !important;border:none !important;box-shadow:none !important;margin:0 !important;}
            .archive-table tbody tr.empty-row td.empty-cell{display:table-cell !important;padding:3rem 1.5rem !important;border:none !important;text-align:center !important;}
            .archive-table tbody tr.empty-row td.empty-cell::before{display:none !important;}
            .empty-icon-wrap{width:80px;height:80px;margin-bottom:20px;background:#EEF2FF;color:#1A237E;}
            .empty-icon-wrap svg{width:40px !important;height:40px !important;}
            .empty-title{font-size:1.35rem !important;font-weight:700 !important;color:#111827 !important;margin-bottom:8px !important;}
            .empty-subtitle{font-size:0.95rem !important;color:#6B7280 !important;max-width:400px;line-height:1.5;}
        }

        /* Desktop (1200px+): table fills remaining viewport height (matches Social Case archive).
           Higher-specificity selectors so they beat the nav partial's later-in-cascade .main/.main-scroll rules. */
        @media (min-width:1200px){
            html,body{overflow:hidden !important;}
            .app{height:100vh !important;overflow:hidden !important;}
            .app .main{height:100vh !important;overflow:hidden !important;display:flex !important;flex-direction:column !important;}
            .app .main-scroll{flex:1 !important;min-height:0 !important;overflow-y:auto !important;overflow-x:hidden !important;display:flex !important;flex-direction:column !important;}
            .archive-panel-wrap{padding:1rem !important;margin-bottom:0 !important;flex:1 !important;min-height:0 !important;overflow:hidden !important;display:flex !important;flex-direction:column !important;}
            .archive-table-wrap{flex:1 !important;min-height:0 !important;border:1px solid var(--border) !important;overflow:auto !important;border-radius:8px !important;}
        }

        /* ── Pagination info ── */
        .archive-pagination-info{font-size:0.875rem;color:var(--text-secondary);text-align:center;padding-top:0.75rem;}

        /* ── Filter container (matches Social Case archive filter bar) ── */
        .archive-filter-bar{display:block;margin-bottom:16px;padding:14px 16px;background:#fff;border:1px solid #E5E7EB;border-radius:12px;}
        .archive-filter-bar #summaryGrid{margin-bottom:0;}

        /* Mobile (<768px): table → stacked cards (matches Social Case archive) */
        @media (max-width:767px){
            .archive-panel-wrap{padding:.75rem;}
            .archive-table-wrap{border:none;border-radius:0;overflow:visible;}
            .archive-table thead{display:none;}
            .archive-table tbody tr{display:block;background:var(--surface);border:1px solid #D1D5DB;border-radius:10px;margin-bottom:10px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
            .archive-table tbody tr:last-child{margin-bottom:0;}
            .archive-table tbody td{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border:none;font-size:.82rem;gap:8px;text-align:right;}
            .archive-table tbody td:not(:last-child){border-bottom:1px solid var(--border);}
            .archive-table tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:.72rem;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:70px;text-align:left;}
            .archive-table tbody td.col-check{justify-content:flex-end;padding:0 0 6px;border-bottom:none;}
            .archive-table tbody td.col-check::before{display:none;}
            .archive-table tbody td[data-label="Action"]{justify-content:flex-end;padding-top:8px;border-bottom:none;}
            .archive-table tbody td[data-label="Action"]::before{display:none;}
            .archive-table tbody td.empty-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:0 !important;}
            .archive-table tbody td.empty-cell::before{display:none !important;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'archive', 'mobileSubtitle' => 'Archived Records'])

    <div class="main">
        <div class="main-scroll">
            <div style="margin-bottom:1.5rem;">
                <p style="margin:0;font-size:0.875rem;color:#6B7280;">Review archived senior citizen records. Use the filters to locate and restore a record when needed.</p>
            </div>
            @php
                $barangays = ['Acacia','Adlas','Anahaw I','Anahaw II','Balite I','Balite II','Balubad','Banaba','Batas','Biga I','Biga II','Biluso','Bucal','Buho','Bulihan','Cabangaan','Carmen','Hoyo','Hukay','Iba','Inchican','Ipil I','Ipil II','Kalubkob','Kaong','Lalaan I','Lalaan II','Litlit','Lucsuhin','Lumil','Maguyam','Malabag','Malaking Tatyao','Mataas na Burol','Munting Ilog','Narra I','Narra II','Narra III','Paligawan','Pasong Langka','Barangay I (Poblacion)','Barangay II (Poblacion)','Barangay III (Poblacion)','Barangay IV (Poblacion)','Barangay V (Poblacion)','Pooc I','Pooc II','Pulong Bunga','Pulong Saging','Puting Kahoy','Sabutan','San Miguel I','San Miguel II','San Vicente I','San Vicente II','Santol','Tartaria','Tibig','Toledo','Tubuan I','Tubuan II','Tubuan III','Ulat','Yakal'];
            @endphp



            <!-- Summary Section -->
            <form method="GET" action="{{ route('admin.senior.archive.list') }}">
                <div class="archive-filter-bar">
                    <div id="summaryGrid">
                    <div class="filter-field">
                        <label class="filter-label" for="searchInput">Search by Name</label>
                        <div class="input-group">
                            <input type="text" id="searchInput" name="search" placeholder="Search by name..." value="{{ request('search') }}">
                            <button type="submit" class="search-btn" aria-label="Search"><i data-lucide="search"></i></button>
                        </div>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label" for="barangayFilter">Filter by Barangay</label>
                        <select class="filter-select" id="barangayFilter" name="barangay" onchange="this.form.submit()">
                            <option value="" {{ !request('barangay') ? 'selected' : '' }}>All Barangays</option>
                            @foreach($barangays as $b)
                                <option value="{{ $b }}" {{ request('barangay') == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label class="filter-label">&nbsp;</label>
                        <div class="bulk-actions-row">
                            <button type="button" id="bulkActionButton" class="bulk-btn" onclick="showBulkActionPopup()" disabled>
                                <i data-lucide="list-checks"></i> Bulk Actions <span class="bulk-count" id="selectedCount">0</span>
                            </button>
                            @if(request('search') || request('barangay'))
                                <a href="{{ route('admin.senior.archive.list') }}" class="btn btn-clear">
                                    <i data-lucide="x"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                </div>
            </form>

            <!-- Archived Records Table -->
            @if($archivedSeniors->count() > 0)
                <div class="mobile-select-all">
                    <input type="checkbox" id="mobileSelectAll" onchange="toggleSelectAllMobile(this.checked)">
                    <label for="mobileSelectAll" style="cursor:pointer;font-weight:500;">Select all</label>
                    <span id="mobileSelectedCount" style="margin-left:auto;font-size:12px;font-weight:600;color:var(--primary);"></span>
                </div>
            @endif
            <div class="panel archive-panel-wrap">
                @if($archivedSeniors->total() > $archivedSeniors->count())
                <div id="selectAllPagesNotice" style="display:none;background:#EEF2FF;border:1px solid #C7D2FE;color:#3730A3;padding:10px 16px;border-radius:8px;margin-bottom:12px;font-size:13px;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                    <span id="selectAllPagesText">All {{ $archivedSeniors->count() }} seniors on this page are selected.</span>
                    <button type="button" id="selectAllPagesBtn" onclick="selectAllAcrossPages(event)" style="background:none;border:none;color:#1A237E;font-weight:700;cursor:pointer;text-decoration:underline;padding:0;font-size:13px;">
                        Select all {{ $archivedSeniors->total() }} archived senior citizens in {{ request('barangay') ? 'Barangay ' . request('barangay') : 'the list' }}
                    </button>
                </div>
                @endif
                <div class="archive-table-wrap">
                    <table class="archive-table">
                        <thead>
                            <tr>
                                <th class="col-check"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)"></th>
                                <th>Control No.</th>
                                <th>Full Name</th>
                                <th>Barangay</th>
                                <th>Sex / Age</th>
                                <th>Archived On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($archivedSeniors as $index => $senior)
                                <tr>
                                    <td data-label="" class="col-check"><input type="checkbox" class="senior-checkbox" data-id="{{ $senior->id }}" onchange="updateBulkActions()"></td>
                                    <td data-label="Control No."><span class="control-no">{{ $senior->control_number ?? '-' }}</span></td>
                                    <td data-label="Full Name">
                                        <div class="td-name">{{ $senior->full_name ?? '-' }}</div>
                                        @if($senior->address)
                                            <div class="td-addr">{{ $senior->address }}</div>
                                        @endif
                                    </td>
                                    <td data-label="Barangay">
                                        @if($senior->barangay)
                                            <span class="badge-archived">{{ $senior->barangay }}</span>
                                        @else
                                            <span class="text-[var(--text-muted)]">-</span>
                                        @endif
                                    </td>
                                    <td data-label="Sex / Age">
                                        <span class="sex-age-wrap">
                                            @if($senior->sex)
                                                <span class="sex-letter">{{ $senior->sex == 'Male' ? 'M' : 'F' }}</span>
                                            @endif
                                            <span class="sex-sep">/</span>
                                            <strong>{{ $senior->age ?? '-' }}</strong>
                                        </span>
                                    </td>
                                    <td data-label="Archived On">
                                        <span class="text-[var(--text-muted)]">{{ $senior->updated_at ? \Carbon\Carbon::parse($senior->updated_at)->format('M d, Y') : '-' }}</span>
                                    </td>
                                    <td data-label="Status">
                                        <span class="badge-archived">Archived</span>
                                    </td>
                                    <td data-label="Action">
                                        <form method="POST" action="{{ route('admin.senior.unarchive', $senior->id) }}" id="restore-form-{{ $senior->id }}" style="display:inline;">
                                            @csrf
                                            <button type="button" class="btn-restore" onclick="confirmRestore({{ $senior->id }}, '{{ addslashes($senior->full_name) }}')">
                                                <i data-lucide="rotate-ccw"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="9" class="empty-cell">
                                        <div class="empty-state-content">
                                            <div class="empty-icon-wrap">
                                                <i data-lucide="archive"></i>
                                            </div>
                                            <div class="empty-title">No archived cases</div>
                                            <div class="empty-subtitle">Archived cases will appear here</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>

                <div class="archive-pagination-info">
                    @if($archivedSeniors->total() === 0)
                        Showing 0 of 0 Archived Cases
                    @else
                        Showing {{ $archivedSeniors->firstItem() }}–{{ $archivedSeniors->lastItem() }} of {{ $archivedSeniors->total() }} Archived Cases
                    @endif
                </div>

                @if($archivedSeniors->count() > 0 && $archivedSeniors->hasPages())
                    <div class="pagination-wrap">
                        {{ $archivedSeniors->appends(['barangay' => request('barangay'), 'search' => request('search')])->links('vendor.pagination.custom') }}
                    </div>
                @endif
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
            <div class="flex flex-col gap-3">
                <button type="button" class="modal-btn modal-btn-teal" onclick="bulkRestore()">
                    <i data-lucide="undo-2"></i> Restore Selected
                </button>
                <button type="button" class="modal-btn modal-btn-indigo" onclick="bulkExport()">
                    <i data-lucide="download"></i> Export Selected
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    window.selectAllMatching = false;

    function toggleSelectAll(checked) {
        document.querySelectorAll('.senior-checkbox').forEach(cb => cb.checked = checked);

        const notice = document.getElementById('selectAllPagesNotice');
        const total = {{ $archivedSeniors->total() ?? 0 }};
        const currentCount = {{ $archivedSeniors->count() ?? 0 }};
        const hasMorePages = total > currentCount;

        if (checked) {
            window.selectAllMatching = true;
            if (notice && hasMorePages) {
                notice.style.display = 'flex';
                document.getElementById('selectAllPagesText').textContent = `All ${total} archived senior citizens in {{ request('barangay') ? 'Barangay ' . request('barangay') : 'the list' }} are selected.`;
                const btn = document.getElementById('selectAllPagesBtn');
                if (btn) {
                    btn.textContent = 'Clear selection';
                    btn.onclick = function(e) { e.preventDefault(); clearAllSelection(); };
                }
            }
        } else {
            window.selectAllMatching = false;
            if (notice) notice.style.display = 'none';
        }

        updateBulkActions();
    }

    function toggleSelectAllMobile(checked) {
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = checked;
        toggleSelectAll(checked);
    }

    function selectAllAcrossPages(e) {
        if (e) e.preventDefault();
        window.selectAllMatching = true;
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = true;
        document.querySelectorAll('.senior-checkbox').forEach(cb => cb.checked = true);
        const mobileAll = document.getElementById('mobileSelectAll');
        if (mobileAll) mobileAll.checked = true;

        const notice = document.getElementById('selectAllPagesNotice');
        if (notice) {
            notice.style.display = 'flex';
            document.getElementById('selectAllPagesText').textContent = `All {{ $archivedSeniors->total() }} archived senior citizens in {{ request('barangay') ? 'Barangay ' . request('barangay') : 'this filter' }} are selected.`;
            const btn = document.getElementById('selectAllPagesBtn');
            if (btn) {
                btn.textContent = 'Clear selection';
                btn.onclick = function(ev) { ev.preventDefault(); clearAllSelection(); };
            }
        }
        updateBulkActions();
    }

    function clearAllSelection() {
        window.selectAllMatching = false;
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;
        document.querySelectorAll('.senior-checkbox').forEach(cb => cb.checked = false);
        const mobileAll = document.getElementById('mobileSelectAll');
        if (mobileAll) mobileAll.checked = false;
        const notice = document.getElementById('selectAllPagesNotice');
        if (notice) notice.style.display = 'none';
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        const selected = document.querySelectorAll('.senior-checkbox:checked');
        const button = document.getElementById('bulkActionButton');
        const countSpan = document.getElementById('selectedCount');
        const selectAll = document.getElementById('selectAll');
        const mobileAll = document.getElementById('mobileSelectAll');
        const mobileCount = document.getElementById('mobileSelectedCount');
        const totalMatching = {{ $archivedSeniors->total() ?? 0 }};
        const pageTotal = checkboxes.length;

        const count = window.selectAllMatching ? totalMatching : selected.length;

        if (countSpan) countSpan.textContent = count;
        if (selectAll) selectAll.checked = pageTotal > 0 && selected.length === pageTotal;
        if (mobileAll) mobileAll.checked = pageTotal > 0 && selected.length === pageTotal;
        if (mobileCount) mobileCount.textContent = count > 0
            ? (window.selectAllMatching ? `${count} / ${totalMatching} selected (all pages)` : `${count} / ${pageTotal} selected`)
            : '';

        // If user manually unchecks any box, reset select-all-matching state
        if (selected.length < pageTotal) {
            window.selectAllMatching = false;
            const notice = document.getElementById('selectAllPagesNotice');
            if (notice) notice.style.display = 'none';
        }

        if (button) {
            const has = count > 0;
            button.disabled = !has;
            button.style.opacity = has ? '1' : '0.45';
            button.style.background = has ? '#3730A3' : '#E0E7FF';
            button.style.color = has ? 'white' : '#3730A3';
            button.style.borderColor = has ? '#312E81' : '#C7D2FE';
        }
    }

    function showBulkActionPopup() {
        const selected = document.querySelectorAll('.senior-checkbox:checked');
        if (selected.length === 0 && !window.selectAllMatching) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }
        document.getElementById('bulkActionModal').classList.add('active');
    }

    function closeBulkModal() {
        document.getElementById('bulkActionModal').classList.remove('active');
    }

    document.getElementById('bulkActionModal').addEventListener('click', function(e) {
        if (e.target === this) closeBulkModal();
    });

    function bulkRestore() {
        const checked = document.querySelectorAll('.senior-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.dataset.id);
        const currentBarangay = `{{ request('barangay') ?? '' }}`;
        const currentSearch = `{{ request('search') ?? '' }}`;

        if (ids.length === 0 && !window.selectAllMatching) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }
        closeBulkModal();

        const count = window.selectAllMatching ? {{ $archivedSeniors->total() ?? 0 }} : ids.length;

        Swal.fire({
            title: 'Restore Selected Records?',
            text: `You are about to restore ${count} record(s) back to active status.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0f766e',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Restore',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const payload = window.selectAllMatching
                    ? { select_all: true, barangay: currentBarangay, search: currentSearch }
                    : { ids: ids };

                fetch('/admin/senior/bulk-restore', {
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
                        Swal.fire('Restored!', 'Selected records have been restored.', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire('Error', data.message || 'Failed to restore records.', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'An error occurred while restoring records.', 'error');
                });
            }
        });
    }

    function bulkExport() {
        const checked = document.querySelectorAll('.senior-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.dataset.id);
        const currentBarangay = `{{ request('barangay') ?? '' }}`;
        const currentSearch = `{{ request('search') ?? '' }}`;

        if (ids.length === 0 && !window.selectAllMatching) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }
        closeBulkModal();

        const count = window.selectAllMatching ? {{ $archivedSeniors->total() ?? 0 }} : ids.length;

        Swal.fire({
            title: 'Export Selected Records?',
            text: `You are about to export ${count} record(s).`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Export',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                if (window.selectAllMatching) {
                    window.location.href = `{{ route('admin.senior.export') }}?barangay=${encodeURIComponent(currentBarangay)}&search=${encodeURIComponent(currentSearch)}`;
                } else {
                    window.location.href = `/admin/senior/export?ids=${ids.join(',')}`;
                }
            }
        });
    }

    function confirmRestore(id, name) {
        Swal.fire({
            title: 'Restore Senior?',
            html: `Are you sure you want to restore <strong>${name}</strong> back to active status?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0f766e',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, Restore',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('restore-form-' + id).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK',
                background: '#ffffff',
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#DC2626',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif
    });
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>
