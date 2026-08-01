<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Birthday Beneficiaries</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={corePlugins:{preflight:false}}</script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;--primary-hover:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--purple:#7C3AED;--purple-bg:#F3E8FF;--shadow:0 10px 30px rgba(15,23,42,.08);--font-family:Arial,Helvetica,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);height:100%;overflow-x:hidden;overflow-y:auto;}
        body{font-size:14px;line-height:1.5;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        .app{display:flex;min-height:100vh;}

        .sidebar{width:260px;flex-shrink:0;background:var(--primary);color:#FFF;position:fixed;left:0;top:0;height:100vh;z-index:1000;display:flex;flex-direction:column;transition:transform .3s ease;}
        .sidebar-brand{height:72px;padding:0 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:.65rem;}
        .sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:var(--accent-yellow);}
        .sidebar-menu{list-style:none;margin:0;padding:1rem 0;flex:1;}
        .sidebar-menu li{margin-bottom:.2rem;}
        .sidebar-menu a{color:rgba(255,255,255,.75);padding:.75rem 1.5rem;display:flex;align-items:center;gap:.75rem;text-decoration:none;font-size:.9rem;border-left:3px solid transparent;transition:all .2s ease;}
        .sidebar-menu a:hover{background:rgba(255,255,255,.1);color:var(--accent-yellow);}
        .sidebar-menu a.active{background:rgba(255,255,255,.1);color:var(--accent-yellow);border-left-color:var(--accent-yellow);}
        .sidebar-menu a i,.sidebar-menu a [data-lucide]{width:20px;height:20px;text-align:center;}

        .main{flex:1;min-width:0;margin-left:260px;padding:32px;max-width:calc(100% - 260px);height:100vh;display:flex;flex-direction:column;overflow:hidden;}
        .main-scroll{flex:1;overflow-y:auto;min-height:0;scrollbar-width:none;-ms-overflow-style:none;border-radius:16px;}
        .main-scroll::-webkit-scrollbar{display:none;}


        .stat-cards{display:grid;grid-template-columns:1fr;gap:20px;margin-bottom:32px;animation:fadeInUp .6s ease-out;flex-shrink:0;}
        @media(min-width:768px){.stat-cards{grid-template-columns:1fr 1fr;}}
        @media(min-width:992px){.stat-cards{grid-template-columns:repeat(3,1fr);}}
        @media(min-width:1200px){.stat-cards{grid-template-columns:repeat(4,1fr);}}

        .stat-card{background:var(--surface);border-radius:16px;padding:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;position:relative;overflow:hidden;cursor:pointer;}
        .stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;transition:all .3s ease;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 10px 25px rgba(0,0,0,.1);}
        .stat-card-red::before{background:var(--danger);}
        .stat-card-orange::before{background:#F59E0B;}
        .stat-card-blue::before{background:var(--info);}
        .stat-card-green::before{background:var(--success);}

        .stat-card-content{flex:1;}
        .stat-card-label{font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--text-primary);margin-bottom:6px;}
        .stat-card-value{font-size:32px;font-weight:700;color:var(--text-primary);line-height:1;}
        .stat-card-icon{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .stat-card-icon svg{width:24px;height:24px;}
        .stat-card-red .stat-card-icon{background:var(--danger-bg);color:var(--danger);}
        .stat-card-orange .stat-card-icon{background:#FFF7ED;color:#F59E0B;}
        .stat-card-blue .stat-card-icon{background:var(--info-bg);color:var(--info);}
        .stat-card-green .stat-card-icon{background:var(--success-bg);color:var(--success);}

        .filter-section{background:var(--surface);border-radius:16px;border:1px solid var(--border);padding:20px;margin-bottom:24px;box-shadow:var(--shadow);flex-shrink:0;}
        .filter-chip{display:inline-flex;align-items:center;gap:.35rem;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:500;border:1px solid var(--border);background:var(--surface);cursor:pointer;transition:all .15s ease;color:var(--text-primary);}
        .filter-chip:hover{border-color:var(--primary);color:var(--primary);}
        .filter-chip.active{background:var(--primary);color:white;border-color:var(--primary);}
        .filter-chip svg{width:14px;height:14px;}

        .table-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;display:flex;flex-direction:column;animation:fadeInUp .6s ease-out .3s backwards;}
        .table-scroll{flex:1;overflow-y:auto;overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none;}
        .table-scroll::-webkit-scrollbar{display:none;}
        .table-scroll table{width:100%;border-collapse:collapse;}
        .table-scroll thead{position:sticky;top:0;z-index:1;background:var(--surface);}
        .table-scroll th{padding:12px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);text-align:left;border-bottom:2px solid var(--border);}
        .table-scroll th.sortable{cursor:pointer;user-select:none;}
        .table-scroll th.sortable:hover{color:var(--primary);}
        .table-scroll td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid var(--border);vertical-align:middle;}
        .table-scroll tr:hover td{background:var(--background);}
        .table-scroll tr:last-child td{border-bottom:none;}

        .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;}

        .countdown-badge{display:inline-flex;align-items:center;gap:.3rem;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;white-space:nowrap;}
        .countdown-badge.today{background:var(--danger-bg);color:var(--danger);}
        .countdown-badge.week{background:#FFF7ED;color:#D97706;}
        .countdown-badge.soon{background:var(--info-bg);color:var(--info);}
        .countdown-badge svg{width:14px;height:14px;}

        .avatar-circle{width:30px;height:30px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:11px;color:white;background:var(--primary);flex-shrink:0;}

        .pagination{display:flex;align-items:center;justify-content:center;gap:4px;list-style:none;margin:0;padding:0;}
        .pagination .page-item{margin:0;}
        .pagination .page-item a,.pagination .page-item span{display:inline-flex;align-items:center;justify-content:center;min-width:40px;padding:.375rem .75rem;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--text-primary);font-size:13px;font-weight:500;cursor:pointer;transition:all .2s ease;text-decoration:none;text-align:center;}
        .pagination .page-item a:hover{background:var(--primary);color:white;border-color:var(--primary);}
        .pagination .page-item.active a{background:var(--primary);color:white;border-color:var(--primary);}
        .pagination .page-item.disabled a,.pagination .page-item.disabled span{color:var(--text-muted);background:var(--background);border-color:var(--border);cursor:not-allowed;pointer-events:none;}
        .pagination .page-item a svg{width:16px;height:16px;}

        .filter-select{width:100%;height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}

        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:3px;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);height:42px;text-decoration:none;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn svg{width:16px;height:16px;}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);transform:translateY(-1px);}
        .btn.ghost{background:transparent;box-shadow:none;border-color:transparent;color:var(--text-secondary);}
        .btn.ghost:hover{background:var(--background);color:var(--text-primary);border-color:var(--border);}
        .btn-sm{padding:6px 12px;font-size:13px;border-radius:8px;}
        .btn-sm svg{width:14px;height:14px;}

        .view-toggle{display:inline-flex;border:1px solid var(--border);border-radius:8px;overflow:hidden;}
        .view-toggle button{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border:none;background:var(--surface);color:var(--text-secondary);cursor:pointer;transition:all .15s ease;}
        .view-toggle button.active{background:var(--primary);color:white;}
        .view-toggle button:not(:last-child){border-right:1px solid var(--border);}
        .view-toggle button svg{width:16px;height:16px;}

        .barangay-group{margin-bottom:12px;}
        .barangay-group-header{padding:12px 16px;background:var(--background);border:1px solid var(--border);border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;transition:all .15s ease;flex-shrink:0;flex-wrap:nowrap;}
        .barangay-group-header:hover{border-color:var(--primary);}

        .modal-overlay{display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:20px;}
        .modal-overlay.show{display:flex;}
        .modal-box{background:var(--surface);border-radius:16px;width:100%;max-width:250px;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);}
        .modal-header-bar{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--border);background:var(--primary);color:white;border-radius:16px 16px 0 0;}
        .modal-header-bar h4{font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px;margin:0;}
        .modal-header-bar h4 svg{width:20px;height:20px;}
        .modal-close-btn{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.15);border:none;color:white;cursor:pointer;transition:all .15s ease;}
        .modal-close-btn:hover{background:rgba(255,255,255,.25);}
        .modal-close-btn svg{width:16px;height:16px;}
        .modal-body-scroll{padding:24px;overflow-y:auto;max-height:60vh;}

        .loading-overlay{position:absolute;inset:0;background:rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;z-index:10;border-radius:16px;}
        .table-wrapper{position:relative;min-height:200px;flex:1;overflow:hidden;display:flex;flex-direction:column;}

        .spinner{width:40px;height:40px;border:3px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .6s linear infinite;margin:0 auto;}
        @keyframes spin{to{transform:rotate(360deg);}}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
        .mobile-header{display:none !important;position:fixed;top:0;left:0;right:0;z-index:1000;background:#1A237E;color:#fff;padding:0 16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);align-items:center;justify-content:space-between;height:80px;}
        .mobile-header-brand{display:flex;align-items:center;gap:16px;flex:1;min-width:0;}
        .mobile-logo{width:56px;height:56px;border-radius:50%;background:#FBC02D;padding:4px;flex-shrink:0;}
        .mobile-logo-img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
        .mobile-brand-text{flex:1;min-width:0;}
        .mobile-brand-title{font-size:18px;font-weight:700;color:#ffffff;margin:0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mobile-brand-subtitle{font-size:12px;color:rgba(255,255,255,0.8);margin:2px 0 0 0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mobile-menu-btn{display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:#ffffff;cursor:pointer;padding:8px;flex-shrink:0;margin-right:24px;}
        .mobile-menu-icon{width:32px;height:32px;}
        .app{flex-direction:column !important;}.main,.main-content{margin-left:0 !important;max-width:100% !important;height:auto !important;overflow:visible !important;padding:12px 14px !important;padding-top:90px !important;}.main-scroll{overflow:visible !important;flex:none !important;}header{display:none !important;}.hamburger-btn{display:none !important;}.mobile-header{display:flex !important;}
        @media (max-width: 479px){.main,.main-content{padding:10px !important;padding-top:88px !important;}.mobile-header{height:72px !important;}.mobile-logo{width:48px !important;height:48px !important;}.mobile-brand-title{font-size:16px !important;}.mobile-brand-subtitle{font-size:11px !important;}.mobile-menu-icon{width:28px !important;height:28px !important;}}

        /* ── Sidebar Overlay ── */
        .sidebar-overlay.active { display: block !important; }

        /* ── Hamburger Button ── */
        .hamburger-btn {
            display: none;
            position: fixed;
            top: 12px;
            left: 12px;
            z-index: 1002;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: background 0.2s;
        }
        .hamburger-btn:hover { background: var(--primary-hover); }

        .sidebar{transform:translateX(-100%) !important;z-index:1001 !important;}
        .sidebar.show{transform:translateX(0) !important;}
        .main,.main-content{margin-left:0 !important;max-width:100% !important;padding:12px 14px !important;padding-top:90px !important;}
        .table-card{overflow:visible !important;margin-bottom:40px !important;padding-bottom:30px !important;}
        .table-scroll{overflow:visible !important;height:auto !important;border:none !important;border-radius:0 !important;min-height:0;}
        .main-scroll{overflow:visible !important;flex:none !important;height:auto !important;}
        .stat-cards{gap:10px !important;margin-bottom:20px !important;}
        .stat-card{padding:14px !important;border-radius:12px !important;}
        .stat-card-icon{display:none !important;}
        .stat-card-value{font-size:24px !important;}
        .stat-card-label{font-size:10px !important;}
        .topnav,.top-navbar{padding:10px 12px !important;}
        .topnav-datetime,.navbar-datetime{display:none !important;}
        .filter-bar,.filter-group{flex-wrap:wrap;}
        .filter-bar>div,.filter-group>div{min-width:0 !important;}
        #filterGrid{grid-template-columns:1fr 1fr !important;}
        .barangay-cards-grid{grid-template-columns:1fr !important;gap:20px !important;}
        .action-bar-row{flex-direction:column !important;gap:10px !important;align-items:flex-start !important;}
        .action-bar-row>div{width:100% !important;}
        .table-scroll table{display:block !important;table-layout:auto !important;width:100%;}
        .table-scroll tbody{display:block;}
        .table-scroll thead{display:none !important;}
        .table-scroll tbody tr{display:block;background:var(--surface);border:1px solid #D1D5DB;border-radius:10px;margin-bottom:10px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);}
        .table-scroll tbody td{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border:none;font-size:0.82rem;gap:8px;}
        .table-scroll tbody td:not(:last-child){border-bottom:1px solid var(--border);}
        .table-scroll tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.03em;flex-shrink:0;min-width:70px;}
        .table-scroll tbody td[data-label="#"]{display:none !important;}
        .table-scroll tbody td[data-label="ID"]{display:none !important;}
        .table-scroll tbody td[data-label="Action"]{justify-content:flex-end;padding-top:8px;border-bottom:none;}
        .table-scroll tbody td[data-label="Action"]::before{display:none;}
        #paginationWrapper{flex-direction:column !important;gap:10px !important;padding:12px !important;}
        #paginationLinks{gap:3px !important;flex-wrap:wrap !important;justify-content:center;}
        #paginationLinks .page-item a,#paginationLinks .page-item span{min-width:34px;height:34px;padding:0 0.5rem;font-size:12px;}
        .modal-box{max-width:800px !important;border-radius:12px !important;max-height:85vh !important;}
        .modal-header-bar{padding:14px 16px !important;border-radius:12px 12px 0 0 !important;}
        .modal-header-bar h4{font-size:14px !important;}
        .modal-body-scroll{padding:16px !important;max-height:70vh !important;}
        #profileContent{grid-template-columns:1fr !important;}
        .barangay-group-header{padding:10px 12px !important;}
        .barangay-group-header strong{font-size:0.85rem !important;}
        #groupedContent{padding:12px !important;}
        #groupedContent table{display:block !important;width:100% !important;}
        #groupedContent table tbody{display:block;}
        #groupedContent table thead{display:none !important;}
        #groupedContent table tbody tr{display:block;background:var(--surface);border:1px solid #D1D5DB;border-radius:8px;margin-bottom:8px;padding:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);}
        #groupedContent table tbody td{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border:none;font-size:0.82rem;gap:8px;}
        #groupedContent table tbody td:not(:last-child){border-bottom:1px solid var(--border);}
        #groupedContent table tbody td::before{content:attr(data-label);font-weight:600;color:var(--text-secondary);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.03em;flex-shrink:0;min-width:60px;}
        #groupedContent table tbody td[data-label="#"]{display:none !important;}

        @media (max-width: 479px) {
            .stat-cards { gap: 8px !important; }
            .stat-card { padding: 12px !important; }
            .stat-card-value { font-size: 20px !important; }
            .stat-card-label { font-size: 9px !important; }
            #filterGrid { grid-template-columns: 1fr !important; }
            .filter-section { padding: 14px !important; }
            .table-scroll tbody td { font-size: 0.78rem !important; }
            .table-scroll tbody td::before { min-width: 60px !important; font-size: 0.68rem !important; }
            .view-toggle { display: none !important; }
        }

        @media (min-width: 768px) {
            .hamburger-btn { display: flex; }
            .mobile-header { display: none !important; }
            header { display: flex !important; }
            .sidebar { transform: translateX(-100%) !important; z-index: 1001 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: 0 !important; max-width: 100% !important; padding: 16px !important; padding-top: 64px !important; }
            .table-card { overflow: visible !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
            .table-scroll { overflow: visible !important; min-height: 0; border: none !important; border-radius: 0 !important; height: auto !important; }
            .main-scroll { overflow: visible !important; flex: none !important; height: auto !important; }
            .stat-cards { gap: 20px !important; margin-bottom: 32px !important; }
            .stat-card { padding: 20px !important; border-radius: 16px !important; }
            .stat-card-icon { display: flex !important; }
            .stat-card-value { font-size: 32px !important; }
            .stat-card-label { font-size: 11px !important; }
            #filterGrid { grid-template-columns: repeat(2, 1fr) !important; }
            .dashboard-grid { grid-template-columns: 1fr !important; }
            .barangay-cards-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 12px !important; }
        }
        @media (min-width: 992px) and (max-width: 1199px) {
            .barangay-cards-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 12px !important; }
        }

        @media (min-width: 1200px) {
            .app { flex-direction: row !important; }
            .hamburger-btn { display: none !important; }
            .mobile-header { display: none !important; }
            header { display: none !important; }
            .sidebar { transform: translateX(0) !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: 260px !important; max-width: calc(100% - 260px) !important; padding: 32px !important; padding-top: 32px !important; height: 100vh !important; overflow: hidden !important; }
            .main-scroll { flex: 1 !important; overflow-y: auto !important; height: 100% !important; }
            .table-card { overflow: hidden !important; flex: 1 !important; }
            .table-scroll { flex: 1 !important; overflow-y: auto !important; overflow-x: auto !important; height: auto !important; border: none !important; border-radius: 0 !important; }
            .barangay-cards-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 12px !important; }
            .desktop-datetime-container {
                display: flex !important;
                justify-content: flex-end !important;
                align-items: center !important;
                margin-bottom: 0.75rem !important;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                height: auto !important;
                padding: 0 !important;
            }
            /* Both Month and Year compact on desktop */
            #filterGrid { grid-template-columns: 160px 110px !important; }
            
            /* Make contents larger and more visible */
            .table-scroll th { font-size: 13px !important; padding: 14px 18px !important; }
            .table-scroll td { font-size: 15px !important; padding: 16px 18px !important; }
            .stat-card-label { font-size: 13px !important; }
            .stat-card-value { font-size: 36px !important; }
            .stat-card-icon { width: 56px !important; height: 56px !important; }
            .stat-card-icon svg { width: 26px !important; height: 26px !important; }
            .badge { font-size: 13px !important; padding: 5px 12px !important; }
            .countdown-badge { font-size: 13px !important; padding: 5px 12px !important; }
            .avatar-circle { width: 34px !important; height: 34px !important; font-size: 12px !important; }
            .filter-chip { font-size: 14px !important; padding: 7px 16px !important; }
            
            /* Budget Overview Card */
            .filter-section { padding: 20px !important; }
            .filter-section h2 { font-size: 22px !important; }
            .filter-section p { font-size: 15px !important; }
            .filter-section div[style*="font-size:28px"] { font-size: 36px !important; }
            .filter-section div[style*="font-size:12px"] { font-size: 14px !important; }
            .filter-section div[style*="font-size:11px"] { font-size: 13px !important; }
            .filter-section div[style*="font-size:20px"] { font-size: 26px !important; }
            .filter-section button { font-size: 14px !important; padding: 12px 20px !important; }
            
            /* Barangay Budget Breakdown */
            .filter-section h3 { font-size: 18px !important; }
            .filter-section label { font-size: 14px !important; }
            .filter-section select { font-size: 14px !important; }
            .filter-section div[style*="font-size:13px"] { font-size: 15px !important; }
            .filter-section strong { font-size: 16px !important; }
            
            /* Barangay Cards */
            .brgy-card { padding: 20px !important; min-height: 220px !important; }
            .brgy-card strong { font-size: 17px !important; }
            .brgy-card .badge { font-size: 14px !important; padding: 6px 12px !important; }
            .brgy-card div[style*="font-size:13px"] { font-size: 15px !important; }
            .brgy-card div[style*="font-size:14px"] { font-size: 16px !important; }
            .brgy-card button { font-size: 14px !important; padding: 12px 18px !important; }
            .brgy-card div[style*="font-size:12px"] { font-size: 14px !important; }
            
            /* Barangay cards grid */
            .barangay-cards-grid { gap: 16px !important; }

            /* Celebrant rows - fixed size */
            .brgy-card table { table-layout: fixed; width: 100%; }
            .brgy-card table th,
            .brgy-card table td { height: 44px !important; padding: 10px 12px !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; }
            .brgy-card table th:first-child,
            .brgy-card table td:first-child { width: 42%; }
        }
    </style>
</head>
<body>
<div class="app">
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i data-lucide="users" style="width:24px;height:24px"></i>
            <span>Senior Citizen</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/senior"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
            <li><a href="/admin/senior/registration"><i data-lucide="user-plus" style="width:20px;height:20px"></i> Registration</a></li>
            <li><a href="/admin/senior/masterlist"><i data-lucide="list" style="width:20px;height:20px"></i> Masterlist</a></li>
            <li><a href="/admin/senior/birthdays" class="active"><i data-lucide="cake" style="width:20px;height:20px"></i> Birthday Beneficiaries</a></li>
            <li><a href="/admin/senior/payouts-history"><i data-lucide="history" style="width:20px;height:20px"></i> Payout History</a></li>
            <li><a href="/admin/senior/statistics"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i> Statistics</a></li>
            <li><a href="/admin/senior/reports"><i data-lucide="file-text" style="width:20px;height:20px"></i> Reports</a></li>
            <li><a href="/admin/senior/archive"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
        </ul>
    </div>
 <div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>

    <!-- Hamburger Button (fixed position) -->
    <button id="hamburgerBtn" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <i data-lucide="menu" style="width:24px;height:24px"></i>
    </button>
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
                <p class="mobile-brand-subtitle">Birthday Beneficiaries</p>
            </div>
            <div class="mobile-logo">
                @if($logo)
                <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
                @endif
            </div>
        </div>
    </div>

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

        <!-- <div class="desktop-datetime-container" style="display:none">
            <div class="flex items-center gap-5 justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="desktopDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">{{ $initials }}</div>
            </div>
        </div> -->

        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Birthday Beneficiaries</h1></div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">{{ $initials }}</div>
            </div>
        </header>

        <div class="main-scroll">
        {{-- Budget Overview Card --}}
        <div class="filter-section" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-hover) 100%);color:white;margin-bottom:24px;padding:14px">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
                <div>
                    <h2 style="font-size:18px;font-weight:700;margin:0 0 4px 0">Budget Overview - {{ $months[$selectedMonth] }} {{ $selectedYear }}</h2>
                    <p style="margin:0;font-size:13px;opacity:0.9">Total budget needed for all barangays</p>
                </div>
                <div style="text-align:right">
                    <div style="font-size:28px;font-weight:700;margin:0">₱{{ number_format($grandTotal, 2) }}</div>
                    <div style="font-size:12px;opacity:0.9">Grand Total</div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.2)">
                <div style="background:rgba(255,255,255,0.1);border-radius:8px;padding:10px">
                    <div style="font-size:11px;opacity:0.9;margin-bottom:2px">Total Seniors</div>
                    <div style="font-size:20px;font-weight:700">{{ $barangayBreakdown->sum('total_seniors') }}</div>
                </div>
                <div style="background:rgba(255,255,255,0.1);border-radius:8px;padding:10px">
                    <div style="font-size:11px;opacity:0.9;margin-bottom:2px">Released</div>
                    <div style="font-size:20px;font-weight:700">{{ $barangayBreakdown->sum('released_count') }}</div>
                </div>
                <div style="background:rgba(255,255,255,0.1);border-radius:8px;padding:10px">
                    <div style="font-size:11px;opacity:0.9;margin-bottom:2px">Remaining</div>
                    <div style="font-size:20px;font-weight:700">{{ $barangayBreakdown->sum('remaining_count') }}</div>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:12px">
                <button class="btn" style="background:var(--success);color:white;padding:10px 18px;font-weight:600;font-size:13px" onclick="releaseAllPayouts()"><i data-lucide="check-circle" style="width:14px;height:14px"></i> Release All & Download</button>
            </div>
        </div>

        {{-- Barangay Budget Breakdown --}}
        <div class="filter-section" style="margin-bottom:24px">
            <div style="margin-bottom:16px">
                <h3 style="font-size:16px;font-weight:600;margin:0 0 12px 0;display:flex;align-items:center;gap:8px">
                    <i data-lucide="landmark" style="width:20px;height:20px;color:var(--primary)"></i>
                    Barangay Budget Breakdown
                </h3>
                <div id="filterGrid" style="display:grid;grid-template-columns:repeat(2, 1fr);gap:12px;align-items:start">
                    <div style="display:flex;flex-direction:column;gap:3px">
                        <label class="filter-label">Barangay</label>
                        <select id="barangayFilter" class="filter-select" style="height:44px" onchange="filterByDate()">
                            <option value="">All Barangays</option>
                            @foreach($barangays as $barangay)
                                <option value="{{ $barangay }}" {{ $selectedBarangay == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:3px">
                        <label class="filter-label">Date</label>
                        <input type="month" id="dateFilter" style="width:100%;height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;" value="{{ $selectedYear }}-{{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }}" onchange="filterByDate()">
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:16px;font-size:13px;margin-bottom:16px">
                <div><strong style="color:var(--text-primary)">Grand Total:</strong> <span style="color:var(--primary);font-weight:700">₱{{ number_format($grandTotal, 2) }}</span></div>
            </div>
            <div class="barangay-cards-grid" style="display:grid;grid-template-columns:1fr;gap:12px">
                @foreach($barangayBreakdown as $barangay)
                <div class="brgy-card" style="background:var(--background);border:1px solid var(--border);border-radius:12px;padding:16px;display:flex;flex-direction:column;min-height:200px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                        <strong style="font-size:15px;color:var(--text-primary)">{{ $barangay['barangay'] }}</strong>
                        <span class="badge" style="background:var(--primary);color:white;font-size:12px;padding:4px 10px;border-radius:8px">{{ $barangay['total_seniors'] }}</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;font-size:13px;flex:1">
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:var(--text-secondary);font-weight:500">Total Budget:</span>
                            <strong style="color:var(--text-primary);font-size:14px">₱{{ number_format($barangay['total_amount'], 2) }}</strong>
                        </div>
                        <div>
                            <span style="color:var(--text-secondary);font-weight:500">Celebrants:</span>
                            <div id="celebrants-{{ str_replace(' ', '-', $barangay['barangay']) }}" style="margin-top:8px">
                                @php
                                    $displayLimit = 3;
                                    $showAll = false;
                                    $celebrantsList = $barangay['celebrants'];
                                @endphp
                                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                                    <thead>
                                        <tr>
                                            <th style="padding:10px 12px;text-align:left;background:#e5e7eb;border-bottom:2px solid #9ca3af;color:#374151;font-weight:700;font-size:13px;">Control No.</th>
                                            <th style="padding:10px 12px;text-align:left;background:#e5e7eb;border-bottom:2px solid #9ca3af;color:#374151;font-weight:700;font-size:13px;">Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($celebrantsList as $celebrant)
                                            @if($loop->index < $displayLimit)
                                                <tr style="border-bottom:1px solid var(--border);">
                                                    <td style="padding:10px 12px;color:var(--text-secondary);">{{ $celebrant['control_number'] }}</td>
                                                    <td style="padding:10px 12px;color:var(--text-primary);">{{ $celebrant['full_name'] }}</td>
                                                </tr>
                                            @elseif($loop->index == $displayLimit)
                                                @php $showAll = true; @endphp
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                @if($showAll && $celebrantsList->count() > $displayLimit)
                                    <div id="more-{{ str_replace(' ', '-', $barangay['barangay']) }}" style="display:none">
                                        <table style="width:100%;border-collapse:collapse;font-size:14px;">
                                            <tbody>
                                                @foreach($celebrantsList as $celebrant)
                                                    @if($loop->index >= $displayLimit)
                                                        <tr style="border-bottom:1px solid var(--border);" data-control="{{ $celebrant['control_number'] }}" data-name="{{ $celebrant['full_name'] }}" data-birthday="{{ $celebrant['birth_date'] ?? '' }}">
                                                            <td style="padding:10px 12px;color:var(--text-secondary);">{{ $celebrant['control_number'] }}</td>
                                                            <td style="padding:10px 12px;color:var(--text-primary);">{{ $celebrant['full_name'] }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Store all celebrants data for modal in a script tag -->
                                    <script type="application/json" id="data-{{ str_replace(' ', '-', $barangay['barangay']) }}">
                                        {!! $celebrantsList->toJson() !!}
                                    </script>
                                    <button onclick="openCelebrantsModal('{{ str_replace(' ', '-', $barangay['barangay']) }}', '{{ addslashes($barangay['barangay']) }}')" style="background:none;border:none;color:var(--primary);cursor:pointer;font-size:12px;margin-top:8px;padding:0">View All ({{ $celebrantsList->count() }} total)</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($barangay['pending_count'] > 0 || $barangay['remaining_count'] > 0)
                    <div style="display:flex;gap:10px;margin-top:16px">
                        <button class="btn" style="flex:1;background:var(--success);color:white;padding:10px 16px;font-weight:600;font-size:13px" onclick="releaseBarangayPayouts('{{ $barangay['barangay'] }}')"><i data-lucide="check-circle" style="width:16px;height:16px"></i> Release & Download</button>
                    </div>
                    @else
                    <div style="display:flex;gap:10px;margin-top:16px">
                        <div style="flex:1;background:var(--text-muted);color:white;padding:10px 16px;font-weight:600;font-size:13px;text-align:center;border-radius:6px;opacity:0.5"><i data-lucide="check-circle" style="width:16px;height:16px"></i> Already Released</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Celebrants Modal -->
<div id="celebrantsModal" class="modal-overlay" style="padding:20px;">
    <div class="modal-box" style="width:800px !important;max-width:800px !important;">
        <div class="modal-header-bar" style="padding:24px 28px !important;">
            <h4 style="font-size:18px !important;"><i data-lucide="users" style="width:24px;height:24px"></i> <span id="modalBarangayName">Barangay</span> - All Celebrants</h4>
            <button class="modal-close-btn" onclick="closeCelebrantsModal()">
                <i data-lucide="x" style="width:18px;height:18px"></i>
            </button>
        </div>
        <div class="modal-body-scroll" id="modalBody" style="padding:28px !important;">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        if (sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        } else {
            sidebar.classList.add('show');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    (function() {
        var overlay = document.getElementById('sidebarOverlay');
        if (overlay) overlay.addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.remove('show');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                var sidebar = document.getElementById('sidebar');
                if (sidebar && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    if (overlay) overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                var sidebar = document.getElementById('sidebar');
                var ov = document.getElementById('sidebarOverlay');
                if (sidebar && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    if (ov) ov.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    })();

    function updateDateTime() {
        const n = new Date();
        const dateTimeStr = n.toLocaleDateString('en-PH', { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' });
        const currentDateTime = document.getElementById('currentDateTime');
        const desktopDateTime = document.getElementById('desktopDateTime');
        if (currentDateTime) currentDateTime.textContent = dateTimeStr;
        if (desktopDateTime) desktopDateTime.textContent = dateTimeStr;
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Initialize Lucide icons
    lucide.createIcons();

    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadData, 400);
    }

    function applyFilter(filter) {
        currentFilter = filter;
        document.querySelectorAll('.filter-chip').forEach(c => c.classList.toggle('active', c.dataset.filter === filter));
        if (filter === 'all') { document.getElementById('monthFilter').value = ''; }
        loadData();
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('barangayFilter').value = '';
        document.getElementById('monthFilter').value = '';
        currentFilter = 'all';
        document.querySelectorAll('.filter-chip').forEach(c => c.classList.toggle('active', c.dataset.filter === 'all'));
        loadData();
    }

    function setView(view) {
        currentView = view;
        document.getElementById('viewTable').classList.toggle('active', view === 'table');
        document.getElementById('viewGrouped').classList.toggle('active', view === 'grouped');
        document.getElementById('tableView').style.display = view === 'table' ? '' : 'none';
        document.getElementById('groupedView').style.display = view === 'grouped' ? '' : 'none';
        if (view === 'grouped') loadGroupedData();
        else loadData();
    }

    function loadData(page) {
        if (page) currentPage = page;
        const params = new URLSearchParams({
            page: currentPage,
            filter: currentFilter,
            search: document.getElementById('searchInput').value,
            barangay: document.getElementById('barangayFilter').value,
            month: document.getElementById('monthFilter').value,
            sort_field: document.getElementById('sortField').value,
            sort_dir: document.getElementById('sortDir').value,
            per_page: document.getElementById('perPageSelect').value,
        });

        fetch(`{{ route('admin.senior.birthdays.data') }}?${params}`)
            .then(r => r.json())
            .then(res => {
                renderTable(res.data);
                renderPagination(res);
                document.getElementById('resultCount').textContent = `Showing ${res.from || 0}-${res.to || 0} of ${res.total} beneficiaries`;
            });
    }

    function renderTable(data) {
        const tbody = document.getElementById('tableBody');
        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="13" style="text-align:center;padding:40px 0;color:var(--text-muted)"><i data-lucide="calendar-check" style="width:40px;height:40px;display:block;margin:0 auto 12px;opacity:.3"></i>No birthday beneficiaries found.</td></tr>`;
            lucide.createIcons();
            return;
        }
        tbody.innerHTML = data.map((s, i) => {
            const countdownHtml = s.is_today
                ? `<span class="countdown-badge today"><i data-lucide="gift"></i> Birthday Today!</span>`
                : s.days_left <= 7
                    ? `<span class="countdown-badge week"><i data-lucide="alert-circle"></i> ${s.days_left} day${s.days_left !== 1 ? 's' : ''}</span>`
                    : `<span class="countdown-badge soon"><i data-lucide="clock"></i> ${s.days_left} days</span>`;

            const initial = s.full_name ? s.full_name.charAt(0).toUpperCase() : '?';

            let payoutStatusHtml = '';
            if (s.payout_status === 'released') {
                payoutStatusHtml = `<span class="badge" style="background:var(--success-bg);color:var(--success)"><i data-lucide="check-circle" style="width:12px;height:12px;margin-right:4px"></i>Released</span>`;
            } else if (s.payout_status === 'pending') {
                payoutStatusHtml = `<span class="badge" style="background:var(--purple-bg);color:var(--purple)"><i data-lucide="clock" style="width:12px;height:12px;margin-right:4px"></i>Pending</span>`;
            } else if (s.payout_status === 'cancelled') {
                payoutStatusHtml = `<span class="badge" style="background:var(--danger-bg);color:var(--danger)"><i data-lucide="x-circle" style="width:12px;height:12px;margin-right:4px"></i>Cancelled</span>`;
            } else {
                payoutStatusHtml = `<span style="color:var(--text-muted);font-size:12px">Not Generated</span>`;
            }

            let actionButtons = `<button class="btn primary btn-sm" style="padding:6px 10px" onclick="viewProfile(${s.id})"><i data-lucide="eye"></i></button>`;
            if (s.payout_status === 'pending' && s.payout_id) {
                actionButtons += ` <button class="btn btn-sm" style="padding:6px 10px;background:var(--success);color:white" onclick="releasePayout(${s.payout_id})"><i data-lucide="banknote"></i></button>`;
            }

            const checkboxHtml = s.payout_status === 'pending' && s.payout_id
                ? `<input type="checkbox" class="row-checkbox" data-payout-id="${s.payout_id}" onchange="updateSelectedCount()" style="cursor:pointer">`
                : `<span style="color:var(--text-muted)">-</span>`;

            return `<tr>
                <td data-label="Select">${checkboxHtml}</td>
                <td data-label="#" style="color:var(--text-muted);font-weight:600">${i + 1}</td>
                <td data-label="Control No."><strong style="font-size:13px">${s.control_number}</strong></td>
                <td data-label="ID"><span style="font-size:12px;color:var(--text-secondary)">${s.osca_id}</span></td>
                <td data-label="Full Name"><div style="display:flex;align-items:center;gap:8px"><span class="avatar-circle">${initial}</span><strong>${s.full_name}</strong></div></td>
                <td data-label="Birth Date"><span style="font-size:13px">${s.birth_date_formatted}</span></td>
                <td data-label="Age"><strong>${s.current_age}</strong></td>
                <td data-label="Turning"><span class="badge" style="background:var(--info-bg);color:var(--info)">${s.age_turning}</span></td>
                <td data-label="Barangay">${s.barangay !== '-' ? `<span class="badge" style="background:var(--info-bg);color:var(--info);font-weight:500">${s.barangay}</span>` : `<span style="color:var(--text-muted)">-</span>`}</td>
                <td data-label="Contact">${s.contact_number !== '-' ? `<a href="tel:${s.contact_number}" style="color:var(--primary);text-decoration:none;font-size:13px">${s.contact_number}</a>` : `<span style="color:var(--text-muted)">-</span>`}</td>
                <td data-label="Countdown">${countdownHtml}</td>
                <td data-label="Payout Status">${payoutStatusHtml}</td>
                <td data-label="Action">${actionButtons}</td>
            </tr>`;
        }).join('');
        lucide.createIcons();
        updateSelectedCount();
    }

    function renderPagination(res) {
        const info = document.getElementById('paginationInfo');
        info.textContent = `Showing page ${res.current_page} of ${res.last_page}`;

        const ul = document.getElementById('paginationLinks');
        let html = '';
        const lp = res.last_page;
        const cp = res.current_page;

        html += `<li class="page-item${cp <= 1 ? ' disabled' : ''}"><a class="page-link" href="#" onclick="loadData(${cp - 1}); return false;"><i data-lucide="chevron-left"></i></a></li>`;

        let start = Math.max(1, cp - 2);
        let end = Math.min(lp, cp + 2);
        if (start > 1) { html += `<li class="page-item"><a class="page-link" href="#" onclick="loadData(1); return false;">1</a></li>${start > 2 ? '<li class="page-item"><span class="page-link">...</span></li>' : ''}`; }
        for (let i = start; i <= end; i++) { html += `<li class="page-item${i === cp ? ' active' : ''}"><a class="page-link" href="#" onclick="loadData(${i}); return false;">${i}</a></li>`; }
        if (end < lp) { html += `${end < lp - 1 ? '<li class="page-item"><span class="page-link">...</span></li>' : ''}<li class="page-item"><a class="page-link" href="#" onclick="loadData(${lp}); return false;">${lp}</a></li>`; }

        html += `<li class="page-item${cp >= lp ? ' disabled' : ''}"><a class="page-link" href="#" onclick="loadData(${cp + 1}); return false;"><i data-lucide="chevron-right"></i></a></li>`;
        ul.innerHTML = html;
        lucide.createIcons();
    }

    function loadGroupedData() {
        const container = document.getElementById('groupedContent');
        container.innerHTML = '<div style="text-align:center;padding:40px 0"><div class="spinner"></div></div>';

        fetch(`{{ route('admin.senior.birthdays.by-barangay') }}`)
            .then(r => r.json())
            .then(data => {
                if (!data || data.length === 0) {
                    container.innerHTML = '<div style="text-align:center;padding:40px 0;color:var(--text-muted)"><i data-lucide="calendar-check" style="width:40px;height:40px;display:block;margin:0 auto 12px;opacity:.3"></i>No data to group.</div>';
                    lucide.createIcons();
                    return;
                }
                container.innerHTML = data.map(g => `
                    <div class="barangay-group">
                        <div class="barangay-group-header" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'':'none'; this.querySelector('.chevron').style.transform=this.nextElementSibling.style.display==='none'?'rotate(0deg)':'rotate(180deg)';">
                            <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:0">
                                <i data-lucide="map-pin" style="width:16px;height:16px;color:var(--primary);flex-shrink:0"></i>
                                <strong style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${g.barangay}</strong>
                                <span class="badge" style="background:var(--primary);color:white;flex-shrink:0">${g.count} ${g.count === 1 ? 'beneficiary' : 'beneficiaries'}</span>
                            </div>
                            <i data-lucide="chevron-down" class="chevron" style="width:16px;height:16px;color:var(--text-muted);flex-shrink:0;transition:transform .2s ease"></i>
                        </div>
                        <div style="display:none;margin-top:8px">
                            <table style="width:100%;border-collapse:collapse">
                                <thead><tr><th style="width:5%">#</th><th>Full Name</th><th>Birth Date</th><th>Countdown</th></tr></thead>
                                <tbody>
                                    ${g.seniors.map((s, i) => {
                                        const cd = s.is_today ? '<span class="countdown-badge today"><i data-lucide="gift"></i> Today!</span>' : `<span class="countdown-badge soon">${s.days_left} days</span>`;
                                        return `<tr><td data-label="#" style="color:var(--text-muted)">${i + 1}</td><td data-label="Full Name"><strong>${s.full_name}</strong></td><td data-label="Birth Date">${s.birth_date}</td><td data-label="Countdown">${cd}</td></tr>`;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `).join('');
                lucide.createIcons();
            });
    }

    function viewProfile(id) {
        const modal = document.getElementById('profileModal');
        const content = document.getElementById('profileContent');
        content.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px 0"><div class="spinner"></div></div>';
        modal.classList.add('show');

        fetch(`{{ route('admin.senior.birthdays.profile', 0) }}`.replace('/0', `/${id}`))
            .then(r => r.json())
            .then(d => {
                const cdHtml = d.is_today
                    ? '<span class="countdown-badge today"><i data-lucide="gift"></i> Birthday Today!</span>'
                    : `<span class="countdown-badge soon"><i data-lucide="clock"></i> ${d.days_left} days remaining</span>`;

                content.innerHTML = `
                    <div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Control Number</span><strong style="font-size:14px">${d.control_number}</strong></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Senior Citizen ID</span><span style="font-size:14px">${d.osca_id}</span></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Full Name</span><strong style="font-size:18px">${d.full_name}</strong></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Address</span><span style="font-size:14px">${d.address}</span></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Barangay</span><span class="badge" style="background:var(--info-bg);color:var(--info)">${d.barangay}</span></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Contact Number</span><span style="font-size:14px">${d.contact_number}</span></div>
                    </div>
                    <div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Birth Date</span><strong style="font-size:14px">${d.birth_date}</strong></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Current Age</span><span style="font-size:14px"><strong>${d.current_age}</strong> years old</span></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Age Turning</span><span class="badge" style="background:var(--primary);color:white">${d.age_turning} years</span></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Sex</span><span style="font-size:14px">${d.sex}</span></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Birth Month</span><span style="font-size:14px">${d.month}</span></div>
                        <div style="margin-bottom:16px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Days Remaining</span><div>${cdHtml}</div></div>
                        <div style="margin-bottom:0"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">PhilSys / RRN</span><span style="font-size:13px">${d.philsys_number} / ${d.rrn_number}</span></div>
                    </div>
                    ${d.remarks && d.remarks !== '-' ? `<div style="grid-column:1/-1;border-top:1px solid var(--border);padding-top:16px;margin-top:8px"><span style="font-size:12px;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:4px">Remarks</span><span style="font-size:14px">${d.remarks}</span></div>` : ''}
                `;
                lucide.createIcons();
            })
            .catch(() => {
                content.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px 0;color:var(--danger)">Failed to load profile.</div>';
            });
    }

    function confirmLogout(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?', text: 'Do you really want to log out?',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#1A237E', cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, log out', cancelButtonText: 'Cancel',
            background: '#ffffff', customClass: { popup: 'rounded-4 shadow-lg' }
        }).then(r => { if (r.isConfirmed) document.getElementById('logout-form').submit(); });
    }

    function generatePayouts() {
        const currentMonth = new Date().toLocaleString('default', { month: 'long' });
        const currentYear = new Date().getFullYear();
        const barangay = document.getElementById('barangayFilter').value;

        Swal.fire({
            title: 'Generate Payouts',
            text: `Generate payouts for ${currentMonth} ${currentYear}${barangay ? ' in ' + barangay : ''}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Generate',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('month', currentMonth);
                formData.append('year', currentYear);
                if (barangay) formData.append('barangay', barangay);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('{{ route("admin.senior.birthdays.generate-payouts") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    let html = `<div style="text-align:left">\n                        <p><strong>${res.message}</strong></p>\n                        <p style="margin:12px 0"><strong>Total Amount:</strong> ₱${res.total_amount.toLocaleString('en-PH', {minimumFractionDigits:2})}</p>`;
                    
                    if (res.barangay_summary && res.barangay_summary.length > 0) {
                        html += `<div style="margin-top:12px;padding:12px;background:#f3f4f6;border-radius:8px">\n                            <strong style="display:block;margin-bottom:8px">Barangay Breakdown:</strong>`;
                        res.barangay_summary.forEach(b => {
                            html += `<div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:13px">\n                                <span>${b.barangay}</span>\n                                <span>${b.new_payouts} payouts (₱${b.amount.toLocaleString('en-PH', {minimumFractionDigits:2})})</span>\n                            </div>`;
                        });
                        html += `</div>`;
                    }
                    
                    html += `</div>`;

                    Swal.fire({
                        title: 'Success',
                        html: html,
                        icon: 'success',
                        confirmButtonColor: '#1A237E',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                    loadData();
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to generate payouts',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }

    function releasePayout(payoutId) {
        Swal.fire({
            title: 'Release Payout',
            text: 'Mark this payout as released?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Release',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch(`{{ route("admin.senior.birthdays.release-payout", 0) }}`.replace('/0', `/${payoutId}`), {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#16A34A',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                    loadData();
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to release payout',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }

    function generateAllPayouts() {
        Swal.fire({
            title: 'Generate All Payouts',
            text: 'Generate payouts for all barangays for current month?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Generate All',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('month', document.getElementById('monthFilter').value);
                formData.append('year', document.getElementById('yearFilter').value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('{{ route("admin.senior.birthdays.generate-all") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    Swal.fire({
                        title: 'Success',
                        text: res.message + ` Total: ₱${res.total_amount.toLocaleString('en-PH', {minimumFractionDigits:2})}`,
                        icon: 'success',
                        confirmButtonColor: '#1A237E',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                    location.reload();
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to generate payouts',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }

    function releaseAllPayouts() {
        Swal.fire({
            title: 'Release All Payouts',
            text: 'Release all pending payouts and download?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Release All & Download',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('month', document.getElementById('monthFilter').value);
                formData.append('year', document.getElementById('yearFilter').value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('{{ route("admin.senior.birthdays.release-all") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.released_payout_ids && res.released_payout_ids.length > 0) {
                        // Create hidden form for PDF download
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("admin.senior.birthdays.print-bulk") }}';
                        form.target = '_blank';
                        
                        const token = document.createElement('input');
                        token.type = 'hidden';
                        token.name = '_token';
                        token.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(token);
                        
                        res.released_payout_ids.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'payout_ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                        document.body.removeChild(form);
                    }

                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#16A34A',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    }).then(() => {
                        const month = document.getElementById('monthFilter').value;
                        const year = document.getElementById('yearFilter').value;
                        window.location.href = `{{ route('admin.senior.birthdays') }}?month=${month}&year=${year}`;
                    });
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to release payouts',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }

    function generateBarangayPayouts(barangay) {
        Swal.fire({
            title: 'Generate Payouts',
            text: `Generate payouts for ${barangay}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Generate',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('barangay', barangay);
                formData.append('month', document.getElementById('monthFilter').value);
                formData.append('year', document.getElementById('yearFilter').value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('{{ route("admin.senior.birthdays.generate-barangay") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    Swal.fire({
                        title: 'Success',
                        text: res.message + ` Total: ₱${res.total_amount.toLocaleString('en-PH', {minimumFractionDigits:2})}`,
                        icon: 'success',
                        confirmButtonColor: '#1A237E',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                    location.reload();
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to generate payouts',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }

    function releaseBarangayPayouts(barangay) {
        Swal.fire({
            title: 'Release Payouts',
            text: `Release pending payouts for ${barangay} and download?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Release & Download',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('barangay', barangay);
                formData.append('month', document.getElementById('monthFilter').value);
                formData.append('year', document.getElementById('yearFilter').value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                fetch('{{ route("admin.senior.birthdays.release-barangay") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.released_payout_ids && res.released_payout_ids.length > 0) {
                        // Create hidden form for PDF download
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("admin.senior.birthdays.print-bulk") }}';
                        form.target = '_blank';
                        
                        const token = document.createElement('input');
                        token.type = 'hidden';
                        token.name = '_token';
                        token.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(token);
                        
                        res.released_payout_ids.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'payout_ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });
                        
                        document.body.appendChild(form);
                        form.submit();
                        document.body.removeChild(form);
                    }

                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#16A34A',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    }).then(() => {
                        const month = document.getElementById('monthFilter').value;
                        const year = document.getElementById('yearFilter').value;
                        window.location.href = `{{ route('admin.senior.birthdays') }}?month=${month}&year=${year}`;
                    });
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to release payouts',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }

    function toggleCelebrants(barangayId) {
        const moreDiv = document.getElementById(`more-${barangayId}`);
        if (moreDiv.style.display === 'none') {
            moreDiv.style.display = 'block';
            event.target.textContent = 'Show Less';
        } else {
            moreDiv.style.display = 'none';
            event.target.textContent = 'View All';
        }
    }

    function openCelebrantsModal(barangayId, barangayName) {
        const dataScript = document.getElementById(`data-${barangayId}`);
        const modal = document.getElementById('celebrantsModal');
        const modalBarangayName = document.getElementById('modalBarangayName');
        const modalBody = document.getElementById('modalBody');

        if (!dataScript) {
            console.error('Could not find script with id: data-' + barangayId);
            alert('Error: Could not load celebrant data');
            return;
        }

        // Set the barangay name
        modalBarangayName.textContent = barangayName;

        // Parse all celebrants data from JSON script tag
        try {
            const celebrantsData = JSON.parse(dataScript.textContent);

            // Create a table structure
            let tableHTML = `
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:16px;text-align:left;background:var(--background);border-bottom:2px solid var(--border);font-size:15px;font-weight:600;color:var(--text-secondary);">Control Number</th>
                            <th style="padding:16px;text-align:left;background:var(--background);border-bottom:2px solid var(--border);font-size:15px;font-weight:600;color:var(--text-secondary);">Name</th>
                            <th style="padding:16px;text-align:left;background:var(--background);border-bottom:2px solid var(--border);font-size:15px;font-weight:600;color:var(--text-secondary);">Birthday</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            // Add all celebrants to the table
            celebrantsData.forEach(celebrant => {
                const controlNumber = celebrant.control_number || '-';
                const fullName = celebrant.full_name || '-';
                const birthday = celebrant.birth_date || '-';
                
                tableHTML += `
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:16px;color:var(--text-secondary);font-size:15px;">${controlNumber}</td>
                        <td style="padding:16px;color:var(--text-primary);font-size:15px;font-weight:500;">${fullName}</td>
                        <td style="padding:16px;color:var(--text-muted);font-size:15px;">${birthday}</td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                </table>
            `;

            modalBody.innerHTML = tableHTML;

            // Show modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';

            // Reinitialize Lucide icons
            lucide.createIcons();
        } catch (e) {
            console.error('Error parsing celebrant data:', e);
            alert('Error loading celebrant data: ' + e.message);
        }
    }

    function closeCelebrantsModal() {
        const modal = document.getElementById('celebrantsModal');
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    // Close modal on backdrop click
    document.getElementById('celebrantsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCelebrantsModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCelebrantsModal();
        }
    });

    function filterByDate() {
        const dateValue = document.getElementById('dateFilter').value;
        const barangay = document.getElementById('barangayFilter').value;
        
        if (dateValue) {
            const [year, month] = dateValue.split('-');
            window.location.href = `{{ route('admin.senior.birthdays') }}?month=${month}&year=${year}&barangay=${barangay}`;
        } else {
            window.location.href = `{{ route('admin.senior.birthdays') }}?barangay=${barangay}`;
        }
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const count = checkboxes.length;
        document.getElementById('selectedCount').textContent = count;
        const bulkBtn = document.getElementById('bulkReleaseBtn');
        bulkBtn.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    function bulkReleaseAll() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkboxes.length === 0) return;

        const payoutIds = Array.from(checkboxes).map(cb => cb.dataset.payoutId);

        Swal.fire({
            title: 'Release Selected Payouts',
            text: `Release ${payoutIds.length} payout(s)?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Release & Print',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                payoutIds.forEach(id => formData.append('payout_ids[]', id));

                fetch('{{ route("admin.senior.birthdays.bulk-release") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.released_payout_ids && res.released_payout_ids.length > 0) {
                        // Open print window with released payout IDs
                        const printParams = new URLSearchParams();
                        res.released_payout_ids.forEach(id => printParams.append('payout_ids[]', id));
                        window.open(`{{ route("admin.senior.birthdays.print-bulk") }}?${printParams.toString()}`, '_blank');
                    }

                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#16A34A',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                    document.getElementById('selectAll').checked = false;
                    loadData();
                })
                .catch(err => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to release payouts',
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        background: '#ffffff',
                        customClass: { popup: 'rounded-4 shadow-lg' }
                    });
                });
            }
        });
    }
    loadData();
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
    lucide.createIcons();
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>