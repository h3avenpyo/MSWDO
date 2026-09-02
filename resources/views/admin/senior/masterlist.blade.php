<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen Masterlist</title>
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
            --primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4,h5{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}

        /* ── Buttons (Flat Design) ── */
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .15s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:none;height:44px;min-height:44px;text-decoration:none;white-space:nowrap;}
        .btn:hover{border-color:var(--primary);transform:none;}
        .btn svg{width:16px;height:16px;}
        .btn-export{background:var(--primary);color:#fff;border-color:var(--primary);}
        .btn-export:hover{background:var(--primary-hover);border-color:var(--primary-hover);}
        .btn-bulk{background:#E0E7FF;color:#3730A3;border:1px solid #C7D2FE;}
        .btn-bulk:hover{border-color:#3730A3;transform:none;}
        .btn-bulk:disabled{opacity:.45;cursor:not-allowed;pointer-events:none;}
        .btn-clear{background:var(--surface);color:var(--danger);border:1px solid #FECACA;}
        .btn-clear:hover{border-color:var(--danger);}

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
        .modal-btn-danger{background:#DC2626;}
        .modal-btn-danger:hover{background:#B91C1C;}
        .modal-btn-indigo{background:var(--primary);}
        .modal-btn-indigo:hover{background:var(--primary-hover);}

        /* ── Summary / Filters ── */
        .section-spacing{margin-bottom:28px;}
        #summaryGrid{display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:stretch;}
        .filter-field{display:flex;flex-direction:column;justify-content:flex-end;min-width:0;gap:3px;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .filter-select{width:100%;height:44px;min-height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .15s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        .input-group{display:flex;align-items:center;}
        .input-group input{flex:1;min-width:0;height:44px;border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;padding:0 1rem;font-size:14px;color:var(--text-primary);background:var(--surface);transition:all .15s ease;font-family:inherit;}
        .input-group input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(30,58,138,.15);}
        .search-btn{background:var(--primary);color:#ffffff;border:none;padding:0 1.1rem;border-radius:0 8px 8px 0;cursor:pointer;height:44px;width:48px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:background .15s;}
        .search-btn:hover{background:var(--primary-hover);}
        .search-btn svg{width:18px;height:18px;}
        .bulk-actions-row{display:flex;gap:8px;align-items:center;min-width:0;}

        /* ── Archive-style panel & table ── */
        .archive-panel-wrap{width:100%;padding:1rem;margin-bottom:1rem;border-radius:12px;background:var(--surface);border:1px solid var(--border);}
        .archive-table-wrap{border:2px solid #CBD5E1;border-radius:8px;overflow-x:auto;-webkit-overflow-scrolling:touch;max-height: 500px; overflow-y: auto;}
        .archive-table{width:100%;border-collapse:collapse;font-size:14px;}
        .archive-table thead th{padding:14px 16px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.03em;color:#1E293B;text-align:left;border-bottom:2px solid #94A3B8;background:#E2E8F0;white-space:nowrap;position: sticky; top: 0; z-index: 10;}
        .archive-table tbody td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid #CBD5E1;vertical-align:middle;white-space:normal;word-break:break-word;}
        .archive-table tbody tr:last-child td{border-bottom:none;}
        .archive-table input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}
        .archive-table .col-check{width:40px;text-align:center;}

        /* ── Badges ── */
        .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:500;white-space:nowrap;}
        .badge-active{background:var(--success-bg);color:var(--success);}
        .badge-pending{background:#FEF3C7;color:#92400E;}

        /* ── Action buttons (Flat Design) ── */
        .actions{display:flex;gap:6px;align-items:center;}
        .action-btn{width:34px !important;height:34px !important;min-height:34px !important;max-height:34px !important;padding:0 !important;display:inline-flex !important;align-items:center !important;justify-content:center !important;border-radius:8px !important;box-shadow:none !important;cursor:pointer;transition:background .15s ease, border-color .15s ease;}
        .action-btn:hover{transform:none;}
        .action-btn svg, .action-btn i{width:16px !important;height:16px !important;}

        /* ── Mobile Select All ── */
        .mobile-select-all{display:none;align-items:center;gap:8px;padding:10px 12px;margin-bottom:10px;background:var(--surface);border:1px solid var(--border);border-radius:10px;font-size:13px;color:var(--text-secondary);}
        .mobile-select-all input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--primary);}

        /* ── Empty State ── */
        .empty-row{background:transparent !important;border:none !important;box-shadow:none !important;padding:0 !important;margin:0 !important;}
        .empty-cell{padding:2.5rem 1rem !important;border:none !important;display:flex !important;flex-direction:column !important;align-items:center !important;justify-content:center !important;width:100% !important;}
        .empty-cell::before{display:none !important;}
        .empty-state-content{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;}
        .empty-icon-wrap{width:64px;height:64px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:#9CA3AF;}
        .empty-icon-wrap svg{width:32px;height:32px;}
        .empty-title{font-size:1.125rem;font-weight:700;color:#1F2937;margin-bottom:4px;}
        .empty-subtitle{font-size:0.875rem;color:#6B7280;}

        /* ── Pagination info ── */
        .archive-pagination-info{font-size:0.875rem;color:var(--text-secondary);text-align:center;padding-top:0.75rem;}

        /* ── Pagination links ── */
        .pagination-wrap{display:flex;justify-content:center;flex-wrap:wrap;padding-top:1rem;margin-top:1rem;border-top:1px solid var(--border);}

        /* ── Social Case Style Pagination ── */
        .sc-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; flex-shrink: 0; padding: 4px 0; flex-wrap: wrap; }
        .sc-pagination-info { font-size: 0.813rem; color: #6B7280; font-weight: 500; }
        .sc-pagination-controls { display: flex; gap: 4px; flex-wrap: wrap; }
        .sc-page-btn { height: 36px; min-width: 36px; padding: 0 10px; border: 1px solid #E5E7EB; border-radius: 6px; background: #fff; color: #374151; font-size: 0.813rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
        .sc-page-btn:hover:not(:disabled) { background: #F3F4F6; border-color: #D1D5DB; }
        .sc-page-btn.active { background: #1A237E; color: #fff; border-color: #1A237E; font-weight: 700; }
        .sc-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

        /* ── Filter container ── */
        .archive-filter-bar{display:block;margin-bottom:16px;padding:14px 16px;background:#fff;border:1px solid #E5E7EB;border-radius:12px;}
        .archive-filter-bar #summaryGrid{margin-bottom:0;}

        /* ── Mobile Pagination ── */
        @media (max-width: 767.98px) {
            .sc-pagination { position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important; background: #fff !important; padding: 15px 0 !important; z-index: 100 !important; border-top: 1px solid #E5E7EB !important; flex-direction: column; align-items: center; gap: 8px; }
            .sc-pagination-controls { justify-content: flex-end; padding-right: 20px; }
        }

        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ══════════════════════════════════════════════ */

        /* ── Extra Large (1400px+) ── */
        @media (min-width:1400px){
            .section-spacing{margin-bottom:32px;}
            #summaryGrid{grid-template-columns:1fr 1fr auto;gap:16px;}
            .filter-label{font-size:13px !important;}
            .filter-select,.input-group input,.search-btn{height:48px !important;min-height:48px !important;}
            .filter-select{font-size:14px !important;}
            .input-group input{font-size:15px !important;}
            .search-btn{width:52px !important;}
            .archive-table thead th{font-size:13px !important;padding:14px 18px !important;}
            .archive-table tbody td{font-size:15px !important;padding:16px 18px !important;}
            .badge{font-size:13px !important;padding:5px 12px !important;}
        }

        /* ── Large Desktop (1200–1399px) ── */
        @media (min-width:1200px) and (max-width:1399px){
            .section-spacing{margin-bottom:28px;}
            #summaryGrid{grid-template-columns:1fr 1fr auto;gap:14px;}
            .filter-label{font-size:12px !important;}
            .filter-select,.input-group input,.search-btn{height:46px !important;min-height:46px !important;}
            .filter-select{font-size:13px !important;}
            .input-group input{font-size:14px !important;}
            .search-btn{width:50px !important;}
            .archive-table thead th{font-size:12px !important;padding:12px 16px !important;}
            .archive-table tbody td{font-size:14px !important;padding:14px 16px !important;}
        }

        /* ── Medium Desktop (992–1199px) ── */
        @media (min-width:992px) and (max-width:1199px){
            .section-spacing{margin-bottom:24px;}
            #summaryGrid{grid-template-columns:1fr 1fr;gap:14px;}
            .archive-table thead th{padding:11px 14px !important;}
            .archive-table tbody td{padding:13px 14px !important;}
            .empty-icon-wrap{width:56px;height:56px;}
            .empty-title{font-size:1rem !important;}
        }

        /* ── Tablet (768–991px) ── */
        @media (min-width:768px) and (max-width:991px){
            .section-spacing{margin-bottom:20px;}
            #summaryGrid{grid-template-columns:1fr 1fr;gap:12px;}
            .archive-table thead th{padding:10px 12px !important;}
            .archive-table tbody td{padding:12px 12px !important;}
        }

        /* ── Desktop full-height layout (matches archive page) ── */
        @media (min-width:1200px){
            html,body{overflow:hidden !important;}
            .app{height:100vh !important;overflow:hidden !important;}
            .app .main{height:100vh !important;overflow:hidden !important;display:flex !important;flex-direction:column !important;}
            .app .main-scroll{flex:1 !important;min-height:0 !important;overflow-y:auto !important;overflow-x:hidden !important;display:flex !important;flex-direction:column !important;}
            .archive-panel-wrap{padding:1rem !important;margin-bottom:0 !important;flex:1 !important;min-height:0 !important;overflow:hidden !important;display:flex !important;flex-direction:column !important;}
            .archive-table-wrap{flex:1 !important;min-height:0 !important;border:1px solid var(--border) !important;overflow:auto !important;border-radius:8px !important;}
            .empty-icon-wrap{width:80px;height:80px;margin-bottom:20px;background:#EEF2FF;color:#1A237E;}
            .empty-icon-wrap svg{width:40px !important;height:40px !important;}
            .empty-title{font-size:1.35rem !important;font-weight:700 !important;color:#111827 !important;margin-bottom:8px !important;}
            .empty-subtitle{font-size:0.95rem !important;color:#6B7280 !important;max-width:400px;line-height:1.5;}
        }
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
        }

        /* ── Bulk Actions & Export Styling ── */
        .bulk-actions-row {
            display: flex;
            gap: 8px;
            align-items: center;
            min-width: 0;
            width: 100%;
        }
        .bulk-actions-row .btn-export,
        .bulk-actions-row .btn-clear {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }
        .bulk-actions-row #bulkActionDropdown {
            display: inline-block;
            position: relative;
        }
        .bulk-actions-row #bulkActionButton {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            width: 100%;
        }
        .selected-count-badge {
            background: #3730A3;
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            margin-left: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ── Tablet & Small Desktop (768–1199px) ── */
        @media (min-width:768px) and (max-width:1199px){
            .bulk-field {
                grid-column: 1 / -1;
            }
            .bulk-actions-row {
                justify-content: flex-start;
            }
        }

        /* ── Mobile (<768px) ── */
        @media (max-width:767px){
            .archive-panel-wrap{padding:.75rem;}
            .archive-table-wrap{border:none;border-radius:0;overflow:visible;}
            .archive-table thead{display:none;}
            .archive-table tbody tr{display:block;background:var(--surface);border:1px solid #D1D5DB;border-radius:10px;margin-bottom:10px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
            .archive-table tbody tr:last-child{margin-bottom:0;}
            .archive-table tbody td{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border:none;font-size:.82rem;gap:8px;text-align:right;}
            .archive-table tbody td:not(:last-child){border-bottom:1px solid var(--border);}
            .archive-table tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:.72rem;text-transform:uppercase;letter-spacing:.03em;flex-shrink:0;min-width:80px;text-align:left;}
            .archive-table tbody td.col-check{justify-content:flex-end;padding:0 0 6px;border-bottom:none;}
            .archive-table tbody td.col-check::before{display:none;}
            .archive-table tbody td[data-label="Control No"]{white-space:nowrap;}
            .archive-table tbody td[data-label="Action"]{justify-content:flex-end;padding-top:8px;border-bottom:none;}
            .archive-table tbody td[data-label="Action"]::before{display:none;}
            .archive-table tbody td.empty-cell{display:flex !important;justify-content:center !important;align-items:center !important;text-align:center !important;padding:0 !important;}
            .archive-table tbody td.empty-cell::before{display:none !important;}
            .action-btn{width:44px;height:44px;}
            .mobile-select-all{display:flex;}
            
            /* Responsive Filter Buttons */
            .desktop-only-label { display: none !important; }
            .bulk-field { grid-column: 1 / -1; margin-top: 2px; }
            .bulk-actions-row {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 8px;
                width: 100%;
            }
            .bulk-actions-row .btn-export {
                flex: 1 1 calc(50% - 4px);
                min-width: 120px;
                height: 42px;
                min-height: 42px;
                padding: 0 10px;
                font-size: 13px;
            }
            .bulk-actions-row #bulkActionDropdown {
                flex: 1 1 calc(50% - 4px);
                min-width: 120px;
            }
            .bulk-actions-row #bulkActionButton {
                height: 42px;
                min-height: 42px;
                padding: 0 10px;
                font-size: 13px;
            }
            .bulk-actions-row .btn-clear {
                flex: 1 1 100%;
                height: 40px;
                min-height: 40px;
                padding: 0 10px;
                font-size: 13px;
            }
            .dropdown-menu {
                right: 0;
                left: 0;
                min-width: 100%;
            }
        }

        /* ── Small Mobile (<480px) ── */
        @media (max-width:479px){
            .section-spacing{margin-bottom:14px;}
            #summaryGrid{grid-template-columns:1fr;gap:10px;}
            .archive-table tbody td{font-size:.75rem;}
            .archive-table tbody td::before{font-size:.65rem;min-width:70px;}
            .bulk-actions-row .btn-export,
            .bulk-actions-row #bulkActionDropdown {
                flex: 1 1 100%;
                width: 100%;
            }
        }

        /* ── Modal Styles ── */
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
        }
        .senior-modal-dialog {
            background: var(--background);
            border-radius: 14px;
            width: 100%;
            max-width: 780px;
            max-height: 75vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .senior-modal-header {
            background: #1A237E;
            color: #ffffff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .senior-modal-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
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
            padding: 2px;
        }
        .senior-modal-close:hover { opacity: 1; }
        .senior-modal-body {
            padding: 16px 20px;
            overflow-y: auto;
            flex: 1;
            -webkit-overflow-scrolling: touch;
        }
        .senior-modal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
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
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.72rem;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .senior-modal-value {
            background: var(--surface);
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 500;
            border: 1px solid var(--border);
            font-size: 0.813rem;
            color: var(--text-primary);
            word-break: break-word;
            overflow-wrap: anywhere;
            min-height: 32px;
            display: flex;
            align-items: center;
        }
        .senior-modal-footer {
            padding: 10px 20px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        /* Modal Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .senior-modal-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .senior-modal-col-span2 {
                grid-column: 1 / -1;
            }
        }
        @media (max-width: 575.98px) {
            .senior-modal-backdrop {
                padding: 8px;
            }
            .senior-modal-dialog {
                max-height: 82vh;
                border-radius: 12px;
            }
            .senior-modal-header {
                padding: 10px 14px;
            }
            .senior-modal-title {
                font-size: 0.9rem;
            }
            .senior-modal-body {
                padding: 12px 12px;
            }
            .senior-modal-grid {
                grid-template-columns: 1fr !important;
                gap: 8px;
            }
            .senior-modal-col-full,
            .senior-modal-col-span2 {
                grid-column: 1 / -1 !important;
            }
            .senior-modal-label {
                font-size: 0.7rem;
            }
            .senior-modal-value {
                padding: 5px 8px;
                font-size: 0.8rem;
                min-height: 30px;
            }
            .senior-modal-footer {
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'masterlist', 'mobileSubtitle' => 'Senior Citizen Masterlist'])

    <div class="main">
        @php
            $userName = session('admin_user_name') ?? 'Admin User';
            $words = explode(' ', $userName);
            $initials = '';
            if (count($words) >= 2) {
                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } else {
                $initials = strtoupper(substr($userName, 0, 2));
            }
        @endphp

        <div class="main-scroll">
            <div style="margin-bottom:1.5rem;">
                <p style="margin:0;font-size:0.875rem;color:#6B7280;">Step 1 of 2 — Search for an existing senior citizen and verify their record before proceeding with a new registration.</p>
            </div>

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('admin.senior.masterlist') }}" id="filterForm">
                <div class="archive-filter-bar section-spacing">
                    <div id="summaryGrid">
                        <div class="filter-field">
                            <label class="filter-label" for="searchInput">Search by Name</label>
                            <div class="input-group">
                                <input type="text" id="searchInput" name="search" placeholder="Search by name..." value="{{ request('search') }}">
                                <button type="submit" class="search-btn" aria-label="Search"><i data-lucide="search"></i></button>
                            </div>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label" for="barangaySelect">Filter by Barangay</label>
                            <select class="filter-select" id="barangaySelect" name="barangay" onchange="this.form.submit()">
                                <option value="">All Barangays</option>
                                <option value="Acacia" {{ request('barangay') == 'Acacia' ? 'selected' : '' }}>Acacia</option>
                                <option value="Adlas" {{ request('barangay') == 'Adlas' ? 'selected' : '' }}>Adlas</option>
                                <option value="Anahaw I" {{ request('barangay') == 'Anahaw I' ? 'selected' : '' }}>Anahaw I</option>
                                <option value="Anahaw II" {{ request('barangay') == 'Anahaw II' ? 'selected' : '' }}>Anahaw II</option>
                                <option value="Balite I" {{ request('barangay') == 'Balite I' ? 'selected' : '' }}>Balite I</option>
                                <option value="Balite II" {{ request('barangay') == 'Balite II' ? 'selected' : '' }}>Balite II</option>
                                <option value="Balubad" {{ request('barangay') == 'Balubad' ? 'selected' : '' }}>Balubad</option>
                                <option value="Banaba" {{ request('barangay') == 'Banaba' ? 'selected' : '' }}>Banaba</option>
                                <option value="Batas" {{ request('barangay') == 'Batas' ? 'selected' : '' }}>Batas</option>
                                <option value="Biga I" {{ request('barangay') == 'Biga I' ? 'selected' : '' }}>Biga I</option>
                                <option value="Biga II" {{ request('barangay') == 'Biga II' ? 'selected' : '' }}>Biga II</option>
                                <option value="Biluso" {{ request('barangay') == 'Biluso' ? 'selected' : '' }}>Biluso</option>
                                <option value="Bucal" {{ request('barangay') == 'Bucal' ? 'selected' : '' }}>Bucal</option>
                                <option value="Buho" {{ request('barangay') == 'Buho' ? 'selected' : '' }}>Buho</option>
                                <option value="Bulihan" {{ request('barangay') == 'Bulihan' ? 'selected' : '' }}>Bulihan</option>
                                <option value="Cabangaan" {{ request('barangay') == 'Cabangaan' ? 'selected' : '' }}>Cabangaan</option>
                                <option value="Carmen" {{ request('barangay') == 'Carmen' ? 'selected' : '' }}>Carmen</option>
                                <option value="Hoyo" {{ request('barangay') == 'Hoyo' ? 'selected' : '' }}>Hoyo</option>
                                <option value="Hukay" {{ request('barangay') == 'Hukay' ? 'selected' : '' }}>Hukay</option>
                                <option value="Iba" {{ request('barangay') == 'Iba' ? 'selected' : '' }}>Iba</option>
                                <option value="Inchican" {{ request('barangay') == 'Inchican' ? 'selected' : '' }}>Inchican</option>
                                <option value="Ipil I" {{ request('barangay') == 'Ipil I' ? 'selected' : '' }}>Ipil I</option>
                                <option value="Ipil II" {{ request('barangay') == 'Ipil II' ? 'selected' : '' }}>Ipil II</option>
                                <option value="Kalubkob" {{ request('barangay') == 'Kalubkob' ? 'selected' : '' }}>Kalubkob</option>
                                <option value="Kaong" {{ request('barangay') == 'Kaong' ? 'selected' : '' }}>Kaong</option>
                                <option value="Lalaan I" {{ request('barangay') == 'Lalaan I' ? 'selected' : '' }}>Lalaan I</option>
                                <option value="Lalaan II" {{ request('barangay') == 'Lalaan II' ? 'selected' : '' }}>Lalaan II</option>
                                <option value="Litlit" {{ request('barangay') == 'Litlit' ? 'selected' : '' }}>Litlit</option>
                                <option value="Lucsuhin" {{ request('barangay') == 'Lucsuhin' ? 'selected' : '' }}>Lucsuhin</option>
                                <option value="Lumil" {{ request('barangay') == 'Lumil' ? 'selected' : '' }}>Lumil</option>
                                <option value="Maguyam" {{ request('barangay') == 'Maguyam' ? 'selected' : '' }}>Maguyam</option>
                                <option value="Malabag" {{ request('barangay') == 'Malabag' ? 'selected' : '' }}>Malabag</option>
                                <option value="Malaking Tatyao" {{ request('barangay') == 'Malaking Tatyao' ? 'selected' : '' }}>Malaking Tatyao</option>
                                <option value="Mataas na Burol" {{ request('barangay') == 'Mataas na Burol' ? 'selected' : '' }}>Mataas na Burol</option>
                                <option value="Munting Ilog" {{ request('barangay') == 'Munting Ilog' ? 'selected' : '' }}>Munting Ilog</option>
                                <option value="Narra I" {{ request('barangay') == 'Narra I' ? 'selected' : '' }}>Narra I</option>
                                <option value="Narra II" {{ request('barangay') == 'Narra II' ? 'selected' : '' }}>Narra II</option>
                                <option value="Narra III" {{ request('barangay') == 'Narra III' ? 'selected' : '' }}>Narra III</option>
                                <option value="Paligawan" {{ request('barangay') == 'Paligawan' ? 'selected' : '' }}>Paligawan</option>
                                <option value="Pasong Langka" {{ request('barangay') == 'Pasong Langka' ? 'selected' : '' }}>Pasong Langka</option>
                                <option value="Barangay I (Poblacion)" {{ request('barangay') == 'Barangay I (Poblacion)' ? 'selected' : '' }}>Barangay I (Poblacion)</option>
                                <option value="Barangay II (Poblacion)" {{ request('barangay') == 'Barangay II (Poblacion)' ? 'selected' : '' }}>Barangay II (Poblacion)</option>
                                <option value="Barangay III (Poblacion)" {{ request('barangay') == 'Barangay III (Poblacion)' ? 'selected' : '' }}>Barangay III (Poblacion)</option>
                                <option value="Barangay IV (Poblacion)" {{ request('barangay') == 'Barangay IV (Poblacion)' ? 'selected' : '' }}>Barangay IV (Poblacion)</option>
                                <option value="Barangay V (Poblacion)" {{ request('barangay') == 'Barangay V (Poblacion)' ? 'selected' : '' }}>Barangay V (Poblacion)</option>
                                <option value="Pooc I" {{ request('barangay') == 'Pooc I' ? 'selected' : '' }}>Pooc I</option>
                                <option value="Pooc II" {{ request('barangay') == 'Pooc II' ? 'selected' : '' }}>Pooc II</option>
                                <option value="Pulong Bunga" {{ request('barangay') == 'Pulong Bunga' ? 'selected' : '' }}>Pulong Bunga</option>
                                <option value="Pulong Saging" {{ request('barangay') == 'Pulong Saging' ? 'selected' : '' }}>Pulong Saging</option>
                                <option value="Puting Kahoy" {{ request('barangay') == 'Puting Kahoy' ? 'selected' : '' }}>Puting Kahoy</option>
                                <option value="Sabutan" {{ request('barangay') == 'Sabutan' ? 'selected' : '' }}>Sabutan</option>
                                <option value="San Miguel I" {{ request('barangay') == 'San Miguel I' ? 'selected' : '' }}>San Miguel I</option>
                                <option value="San Miguel II" {{ request('barangay') == 'San Miguel II' ? 'selected' : '' }}>San Miguel II</option>
                                <option value="San Vicente I" {{ request('barangay') == 'San Vicente I' ? 'selected' : '' }}>San Vicente I</option>
                                <option value="San Vicente II" {{ request('barangay') == 'San Vicente II' ? 'selected' : '' }}>San Vicente II</option>
                                <option value="Santol" {{ request('barangay') == 'Santol' ? 'selected' : '' }}>Santol</option>
                                <option value="Tartaria" {{ request('barangay') == 'Tartaria' ? 'selected' : '' }}>Tartaria</option>
                                <option value="Tibig" {{ request('barangay') == 'Tibig' ? 'selected' : '' }}>Tibig</option>
                                <option value="Toledo" {{ request('barangay') == 'Toledo' ? 'selected' : '' }}>Toledo</option>
                                <option value="Tubuan I" {{ request('barangay') == 'Tubuan I' ? 'selected' : '' }}>Tubuan I</option>
                                <option value="Tubuan II" {{ request('barangay') == 'Tubuan II' ? 'selected' : '' }}>Tubuan II</option>
                                <option value="Tubuan III" {{ request('barangay') == 'Tubuan III' ? 'selected' : '' }}>Tubuan III</option>
                                <option value="Ulat" {{ request('barangay') == 'Ulat' ? 'selected' : '' }}>Ulat</option>
                                <option value="Yakal" {{ request('barangay') == 'Yakal' ? 'selected' : '' }}>Yakal</option>
                            </select>
                        </div>
                        <div class="filter-field bulk-field">
                            <label class="filter-label desktop-only-label">&nbsp;</label>
                            <div class="bulk-actions-row">
                                <button type="button" id="bulkActionButton" class="btn btn-bulk" onclick="showBulkActionPopup()" disabled>
                                    <i data-lucide="list-checks"></i> <span>Bulk Actions</span>
                                    <span id="selectedCount" class="selected-count-badge">0</span>
                                </button>
                                @if(request('search') || request('barangay'))
                                    <a href="{{ route('admin.senior.masterlist') }}" class="btn btn-clear">
                                        <i data-lucide="x"></i> <span>Clear</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @if($seniors->count() > 0)
                <div class="mobile-select-all">
                    <input type="checkbox" id="mobileSelectAll" onchange="toggleSelectAllMobile(this.checked)">
                    <label for="mobileSelectAll" style="cursor:pointer;font-weight:500;">Select all</label>
                    <span id="mobileSelectedCount" style="margin-left:auto;font-size:12px;font-weight:600;color:var(--primary);"></span>
                </div>
            @endif

            @if($seniors->total() > $seniors->count())
            <div id="selectAllPagesNotice" style="display:none;background:#EEF2FF;border:1px solid #C7D2FE;color:#3730A3;padding:10px 16px;border-radius:8px;margin-bottom:12px;font-size:13px;align-items:center;flex-wrap:wrap;gap:8px;">
                <span id="selectAllPagesText">All {{ $seniors->total() }} senior citizens in {{ request('barangay') ? 'Barangay ' . request('barangay') : 'the list' }} are selected.</span>
            </div>
            @endif
            <div class="archive-table-wrap">
                <table class="archive-table">
                    <thead>
                        <tr>
                            <th class="col-check"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                            <th>Control No</th>
                            <th>Full Name</th>
                            <th>Barangay</th>
                            <th>Status</th>
                            <th>Address</th>
                            <th>Age</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($seniors as $senior)
                            <tr>
                                <td data-label="" class="col-check"><input type="checkbox" class="senior-checkbox" data-id="{{ $senior->id }}" onchange="updateBulkActions()"></td>
                                <td data-label="Control No" style="font-weight:600;">{{ $senior->control_number ?? '-' }}</td>
                                <td data-label="Full Name">{{ $senior->full_name ?? '-' }}</td>
                                <td data-label="Barangay">{{ $senior->barangay ?? '-' }}</td>
                                <td data-label="Status">
                                    <span class="badge {{ $senior->status->value == 'active' ? 'badge-active' : 'badge-pending' }}">
                                        {{ ucfirst($senior->status->value ?? 'pending') }}
                                    </span>
                                </td>
                                <td data-label="Address">{{ $senior->address ?? '-' }}</td>
                                <td data-label="Age">{{ $senior->age ?? '-' }}</td>
                                <td data-label="Action">
                                    <div class="actions">
                                        <button class="action-btn" style="background:var(--primary);border-color:var(--primary);color:#fff;" onclick="viewProfile({{ $senior->id }})" title="View Profile">
                                            <i data-lucide="eye"></i>
                                        </button>
                                        <button class="action-btn archive-senior-btn"
                                            data-id="{{ $senior->id }}"
                                            data-name="{{ $senior->full_name }}"
                                            style="background:var(--danger-bg);border-color:#FECACA;color:var(--danger);"
                                            title="Archive">
                                            <i data-lucide="archive"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="8" class="empty-cell">
                                    <div class="empty-state-content">
                                        <div class="empty-icon-wrap">
                                            <i data-lucide="users"></i>
                                        </div>
                                        <div class="empty-title">No senior citizens found</div>
                                        <div class="empty-subtitle">No records match your search criteria</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="border-top: 2px solid #94A3B8; margin: 20px 0;"></div>

            <div class="sc-pagination">
                <div class="sc-pagination-info">
                    @if($seniors->total() === 0)
                        Showing 0 of 0 Records
                    @else
                        Showing {{ $seniors->firstItem() }}–{{ $seniors->lastItem() }} of {{ $seniors->total() }} Records
                    @endif
                </div>
                <div class="sc-pagination-controls">
                    @if($seniors->hasPages())
                        @if($seniors->onFirstPage())
                            <span class="sc-page-btn" disabled>Previous</span>
                        @else
                            <a href="{{ $seniors->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                        @endif

                        <span class="sc-page-btn active">{{ $seniors->currentPage() }}</span>

                        @if($seniors->hasMorePages())
                            <a href="{{ $seniors->nextPageUrl() }}" class="sc-page-btn">Next</a>
                        @else
                            <span class="sc-page-btn" disabled>Next</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================== MODAL ======================== -->
<div id="seniorModal" class="senior-modal-backdrop">
    <div class="senior-modal-dialog">
        <div class="senior-modal-header">
            <h5 class="senior-modal-title">
                <i data-lucide="user-circle" style="width:20px;height:20px;"></i>
                Senior Citizen Details
            </h5>
            <button onclick="closeModal()" class="senior-modal-close" aria-label="Close modal">
                <i data-lucide="x" style="width:24px;height:24px;"></i>
            </button>
        </div>
        <div class="senior-modal-body">
            <div class="senior-modal-grid">
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Control Number</label>
                    <div class="senior-modal-value" id="modalControlNumber">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Year Applied</label>
                    <div class="senior-modal-value" id="modalYearApplied">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Status</label>
                    <div class="senior-modal-value" id="modalStatus">—</div>
                </div>
                <div class="senior-modal-field senior-modal-col-full">
                    <label class="senior-modal-label">Full Name</label>
                    <div class="senior-modal-value" id="modalFullName">—</div>
                </div>
                <div class="senior-modal-field senior-modal-col-full">
                    <label class="senior-modal-label">Address</label>
                    <div class="senior-modal-value" id="modalAddress">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Barangay</label>
                    <div class="senior-modal-value" id="modalBarangay">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Birth Date</label>
                    <div class="senior-modal-value" id="modalBirthDate">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Month</label>
                    <div class="senior-modal-value" id="modalMonth">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Age</label>
                    <div class="senior-modal-value" id="modalAge">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Sex</label>
                    <div class="senior-modal-value" id="modalSex">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">Contact Number</label>
                    <div class="senior-modal-value" id="modalContactNumber">—</div>
                </div>
                <div class="senior-modal-field">
                    <label class="senior-modal-label">PhilSys Number</label>
                    <div class="senior-modal-value" id="modalPhilsysNumber">—</div>
                </div>
                <div class="senior-modal-field senior-modal-col-span2">
                    <label class="senior-modal-label">RRN Number</label>
                    <div class="senior-modal-value" id="modalRrnNumber">—</div>
                </div>
                <div class="senior-modal-field senior-modal-col-full">
                    <label class="senior-modal-label">Remarks</label>
                    <div class="senior-modal-value" id="modalRemarks" style="white-space:pre-wrap;">—</div>
                </div>
            </div>
        </div>
        <div class="senior-modal-footer">
            <button onclick="closeModal()" class="btn">Close</button>
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
                <button type="button" class="modal-btn modal-btn-danger" onclick="bulkArchive(event)">
                    <i data-lucide="archive"></i> Archive Selected
                </button>
                <button type="button" class="modal-btn modal-btn-indigo" onclick="exportPdf(event)">
                    <i data-lucide="file-output"></i> Export Selected (PDF)
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

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

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        const modal = document.getElementById('bulkActionModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeBulkModal();
            });
        }
    });

    // Custom Modal
    let currentSeniorId = null;

    function openModal() {
        const modal = document.getElementById('seniorModal');
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => modal.style.opacity = '1', 10);
    }
    function closeModal() {
        const modal = document.getElementById('seniorModal');
        if (!modal) return;
        modal.style.opacity = '0';
        setTimeout(() => { modal.style.display = 'none'; document.body.style.overflow = ''; }, 200);
    }
    const seniorModalEl = document.getElementById('seniorModal');
    if (seniorModalEl) {
        seniorModalEl.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    }

    // View senior profile function
    function viewProfile(id) {
        currentSeniorId = id;
        const fields = ['modalControlNumber','modalFullName','modalAddress','modalBarangay','modalBirthDate','modalMonth','modalAge','modalSex','modalContactNumber','modalPhilsysNumber','modalRrnNumber','modalRemarks','modalStatus','modalYearApplied'];
        fields.forEach(f => { const el = document.getElementById(f); if(el) el.textContent = 'Loading...'; });

        openModal();

        fetch(`{{ route('admin.senior.profile.json', 0) }}`.replace('/0', `/${id}`))
            .then(r => r.json())
            .then(d => {
                document.getElementById('modalControlNumber').textContent = d.control_number;
                document.getElementById('modalFullName').textContent = d.full_name;
                document.getElementById('modalAddress').textContent = d.address;
                document.getElementById('modalBarangay').textContent = d.barangay;
                document.getElementById('modalBirthDate').textContent = d.birth_date;
                document.getElementById('modalMonth').textContent = d.month;
                document.getElementById('modalAge').textContent = d.current_age;
                document.getElementById('modalSex').textContent = d.sex;
                document.getElementById('modalContactNumber').textContent = d.contact_number;
                document.getElementById('modalPhilsysNumber').textContent = d.philsys_number;
                document.getElementById('modalRrnNumber').textContent = d.rrn_number;
                document.getElementById('modalRemarks').textContent = d.remarks;
                document.getElementById('modalStatus').textContent = d.status;
                document.getElementById('modalYearApplied').textContent = d.year_applied;
            })
            .catch(err => {
                console.error('Error loading profile:', err);
                document.getElementById('modalFullName').textContent = 'Error loading data';
            });
    }

    // Event delegation for Archive buttons
    document.addEventListener('click', function(e) {
        const button = e.target.closest ? e.target.closest('.archive-senior-btn') : null;
        if (button) {
            const seniorId = button.dataset.id;
            const seniorName = button.dataset.name;

            Swal.fire({
                title: 'Archive Senior Citizen',
                text: `Are you sure you want to archive ${seniorName}? This can be undone from the archive page.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1A237E',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Archive',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/senior/archive/${seniorId}`;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.getAttribute('content');
                        form.appendChild(csrfInput);
                    }

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    });

    window.selectAllMatching = false;

    // Bulk Actions Functions
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        var mobileAll = document.getElementById('mobileSelectAll');
        if (mobileAll) mobileAll.checked = selectAll.checked;

        const notice = document.getElementById('selectAllPagesNotice');
        const total = {{ $seniors->total() ?? 0 }};
        const currentCount = {{ $seniors->count() ?? 0 }};
        const hasMorePages = total > currentCount;

        if (selectAll.checked) {
            window.selectAllMatching = true;
            if (notice && hasMorePages) {
                notice.style.display = 'flex';
                document.getElementById('selectAllPagesText').textContent = `All ${total} senior citizens in {{ request('barangay') ? 'Barangay ' . request('barangay') : 'the list' }} are selected.`;
            }
        } else {
            window.selectAllMatching = false;
            if (notice) notice.style.display = 'none';
        }

        updateBulkActions();
    }

    function toggleSelectAllMobile(checked) {
        var selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = checked;
        toggleSelectAll();
    }

    function selectAllAcrossPages(e) {
        if (e) e.preventDefault();
        window.selectAllMatching = true;
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = true;
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        checkboxes.forEach(cb => cb.checked = true);
        const mobileAll = document.getElementById('mobileSelectAll');
        if (mobileAll) mobileAll.checked = true;

        const notice = document.getElementById('selectAllPagesNotice');
        if (notice) {
            notice.style.display = 'flex';
            document.getElementById('selectAllPagesText').textContent = `All {{ $seniors->total() }} senior citizens in {{ request('barangay') ? 'Barangay ' . request('barangay') : 'this filter' }} are selected.`;
        }
        updateBulkActions();
    }

    function clearAllSelection() {
        window.selectAllMatching = false;
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;
        const checkboxes = document.querySelectorAll('.senior-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        const mobileAll = document.getElementById('mobileSelectAll');
        if (mobileAll) mobileAll.checked = false;

        const notice = document.getElementById('selectAllPagesNotice');
        if (notice) notice.style.display = 'none';
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const button = document.getElementById('bulkActionButton');
        const countSpan = document.getElementById('selectedCount');
        const mobileCount = document.getElementById('mobileSelectedCount');
        const mobileAll = document.getElementById('mobileSelectAll');
        const totalMatching = {{ $seniors->total() ?? 0 }};
        const pageTotal = document.querySelectorAll('.senior-checkbox').length;

        const count = window.selectAllMatching ? totalMatching : checkboxes.length;

        countSpan.textContent = count;
        if (mobileCount) {
            mobileCount.textContent = count > 0 ? (window.selectAllMatching ? `${count} / ${totalMatching} selected (all pages)` : `${count} / ${pageTotal} selected`) : '';
        }
        if (mobileAll) {
            mobileAll.checked = checkboxes.length === pageTotal && pageTotal > 0;
        }

        if (checkboxes.length < pageTotal) {
            window.selectAllMatching = false;
            const notice = document.getElementById('selectAllPagesNotice');
            if (notice) notice.style.display = 'none';
        }

        if (count > 0) {
            button.disabled = false;
            button.style.opacity = '1';
            button.style.background = '#3730A3';
            button.style.color = 'white';
            button.style.borderColor = '#312E81';
        } else {
            button.disabled = true;
            button.style.opacity = '0.45';
            button.style.background = '#E0E7FF';
            button.style.color = '#3730A3';
            button.style.borderColor = '#C7D2FE';
        }
    }

    function bulkArchive(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        closeBulkModal();

        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

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
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const payload = window.selectAllMatching 
                    ? { select_all: true, barangay: `{{ request('barangay') ?? '' }}`, search: `{{ request('search') ?? '' }}` }
                    : { ids: ids };

                fetch('/admin/senior/bulk-archive', {
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

    async function exportPdf(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        closeBulkModal();

        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const selectAll = document.getElementById('selectAll');
        const isAllPageChecked = selectAll && selectAll.checked;
        const currentBarangay = `{{ request('barangay') ?? '' }}`;
        const currentSearch = `{{ request('search') ?? '' }}`;

        let baseQuery = `barangay=${encodeURIComponent(currentBarangay)}&search=${encodeURIComponent(currentSearch)}`;

        // If specific individual checkboxes are checked AND NOT select-all / selectAllMatching
        if (checkboxes.length > 0 && !window.selectAllMatching && !isAllPageChecked) {
            const ids = Array.from(checkboxes).map(cb => cb.dataset.id);
            baseQuery += `&ids=${ids.join(',')}`;
        }

        Swal.fire({
            title: 'Preparing PDF Export...',
            text: 'Calculating records and parts...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const countRes = await fetch(`{{ route('admin.senior.export-pdf') }}?${baseQuery}&check_count=1`);
            if (!countRes.ok) throw new Error('Count check failed');
            const countData = await countRes.json();

            const totalCount = countData.total_count || 0;
            const batchSize = countData.per_part || 1000;
            const totalBatches = countData.total_parts || Math.max(1, Math.ceil(totalCount / batchSize));

            if (totalCount === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'No Records',
                    text: 'No senior citizen records found to export.',
                    confirmButtonColor: '#1A237E'
                });
                return;
            }

            for (let batch = 1; batch <= totalBatches; batch++) {
                const startRecord = ((batch - 1) * batchSize) + 1;
                const endRecord = Math.min(totalCount, batch * batchSize);

                Swal.fire({
                    title: `Downloading Batch ${batch}…`,
                    html: `
                        <p style="font-size:14px;color:#334155;margin:10px 0 6px 0;">
                            Downloading records <b>${startRecord.toLocaleString()}–${endRecord.toLocaleString()} of ${totalCount.toLocaleString()}</b> total records.
                        </p>
                        <p style="font-size:12px;color:#64748b;margin:0;">
                            Please keep this window open until the download is complete.
                        </p>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const pdfRes = await fetch(`{{ route('admin.senior.export-pdf') }}?${baseQuery}&part=${batch}`);
                if (!pdfRes.ok) throw new Error(`Download of Batch ${batch} failed`);
                const blob = await pdfRes.blob();

                let filename = 'senior_citizens';
                if (currentBarangay) {
                    filename += '_' + currentBarangay.toLowerCase().replace(/[^a-z0-9]+/g, '_');
                }
                if (totalBatches > 1) {
                    filename += `_batch_${batch}_of_${totalBatches}`;
                }
                filename += '_' + new Date().toISOString().slice(0, 10) + '.pdf';
                triggerFileDownload(blob, filename);

                if (batch < totalBatches) {
                    await new Promise(r => setTimeout(r, 1200));
                }
            }

            Swal.fire({
                icon: 'success',
                title: 'Download Complete',
                text: totalBatches > 1 
                    ? `Successfully downloaded all ${totalBatches} batches (${totalCount.toLocaleString()} total records).`
                    : `Successfully exported ${totalCount.toLocaleString()} record(s).`,
                confirmButtonColor: '#1A237E',
                timer: 3500,
                timerProgressBar: true
            });
        } catch (err) {
            console.error('PDF Export Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Download Failed',
                text: 'Something went wrong during export. Please try again.',
                confirmButtonColor: '#1A237E'
            });
        }
    }

    function triggerFileDownload(blob, filename) {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 100);
    }

    function bulkExport(e) {
        if (e) { e.preventDefault(); e.stopPropagation(); }
        closeBulkModal();

        const checkboxes = document.querySelectorAll('.senior-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.dataset.id);
        const currentBarangay = `{{ request('barangay') ?? '' }}`;
        const currentSearch = `{{ request('search') ?? '' }}`;

        if (checkboxes.length === 0 && !window.selectAllMatching) {
            Swal.fire('No Selection', 'Please select at least one record.', 'warning');
            return;
        }

        const count = window.selectAllMatching ? {{ $seniors->total() ?? 0 }} : ids.length;

        Swal.fire({
            title: 'Export Selected Records?',
            text: `You are about to export ${count} record(s) to CSV.`,
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

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
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
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif
    });
</script>
</body>
</html>
