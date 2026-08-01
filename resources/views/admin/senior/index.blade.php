<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;
            --primary-hover:#121858;
            --sidebar-bg:#1A237E;
            --accent-yellow:#FBC02D;
            --background:#F1F5F9;
            --surface:#FFFFFF;
            --border:#E5E7EB;
            --text-primary:#111827;
            --text-secondary:#6B7280;
            --text-muted:#9CA3AF;
            --success:#16A34A;
            --success-bg:#ECFDF5;
            --danger:#DC2626;
            --danger-bg:#FEF2F2;
            --info:#3B82F6;
            --info-bg:#EEF2FF;
            --purple:#7C3AED;
            --purple-bg:#F3E8FF;
            --icon-blue:#3B82F6;
            --icon-green:#16A34A;
            --icon-purple:#7C3AED;
            --sidebar-width:260px;
            --content-padding:32px;
            --shadow:0 10px 30px rgba(15,23,42,.08);
            --shadow-hover:0 20px 40px rgba(15,23,42,.12);
            --font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);height:100%;overflow:hidden;}
        body{font-size:14px;line-height:1.5;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        .app{display:flex;min-height:100vh;flex-direction:row;}

        /* Sidebar */
        .sidebar{width:var(--sidebar-width);flex-shrink:0;background:var(--primary);color:#FFF;position:fixed;left:0;top:0;height:100vh;z-index:1001;display:flex;flex-direction:column;transition:transform .3s ease;transform:translateX(-100%);}
        .sidebar.show{transform:translateX(0);}
        .sidebar-brand{height:72px;padding:0 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);color:#fff;font-weight:700;font-size:1.1rem;display:flex;align-items:center;gap:.65rem;}
        .sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:var(--accent-yellow);}
        .sidebar-menu{list-style:none;margin:0;padding:1rem 0;flex:1;}
        .sidebar-menu li{margin-bottom:.2rem;}
        .sidebar-menu a{color:rgba(255,255,255,.75);padding:.75rem 1.5rem;display:flex;align-items:center;gap:.75rem;text-decoration:none;font-size:.9rem;border-left:3px solid transparent;transition:all .2s ease;}
        .sidebar-menu a:hover{background:rgba(255,255,255,.1);color:var(--accent-yellow);}
        .sidebar-menu a.active{background:rgba(255,255,255,.1);color:var(--accent-yellow);border-left-color:var(--accent-yellow);}
        .sidebar-menu a i,.sidebar-menu a [data-lucide]{width:20px;height:20px;text-align:center;}
        .sidebar-foot{padding:1rem 1.5rem;font-size:11px;color:rgba(255,255,255,.4);border-top:1px solid rgba(255,255,255,.1);}

        /* Main */
        .main{flex:1;min-width:0;margin-left:0;padding:16px;padding-top:72px;max-width:100%;height:auto;overflow:visible;display:flex;flex-direction:column;}
        .main-scroll{overflow:visible;flex:none;}

        @media(max-width:767px){
            .main{height:auto;overflow:visible;}
        }

        /* Dashboard Grid */
        .dashboard-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:32px;}
        @media(max-width:1024px){.dashboard-grid{grid-template-columns:1fr;}}

        /* Stat Cards */
        .main-scroll{flex:1;overflow-y:auto;min-height:0;scrollbar-width:none;-ms-overflow-style:none;}
        .main-scroll::-webkit-scrollbar{display:none;}
        .stat-cards{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:32px;animation:fadeInUp .6s ease-out;flex-shrink:0;}
        @media(min-width:768px) and (max-width:1199px){.stat-cards{grid-template-columns:repeat(2,1fr);gap:16px;}}
        @media(max-width:767px){.stat-cards{grid-template-columns:1fr 1fr;gap:12px;}}

        .stat-card{background:var(--surface);border-radius:16px;padding:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;position:relative;overflow:hidden;min-height:0;}
        .stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;transition:all .3s ease;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);}
        .stat-card-blue::before{background:var(--icon-blue);}
        .stat-card-green::before{background:var(--icon-green);}
        .stat-card-purple::before{background:var(--icon-purple);}
        .stat-card-orange::before{background:#F59E0B;}
        .stat-card-red::before{background:var(--danger);}

        .stat-card-content{flex:1;}
        .stat-card-label{font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--text-secondary);margin-bottom:6px;}
        .stat-card-value{font-size:32px;font-weight:700;color:var(--text-primary);line-height:1;}
        .stat-card-icon{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .stat-card-icon svg{width:24px;height:24px;}
        .stat-card-blue .stat-card-icon{background:var(--info-bg);color:var(--icon-blue);}
        .stat-card-green .stat-card-icon{background:var(--success-bg);color:var(--icon-green);}
        .stat-card-purple .stat-card-icon{background:var(--purple-bg);color:var(--icon-purple);}
        .stat-card-orange .stat-card-icon{background:#FFF7ED;color:#F59E0B;}
        .stat-card-red .stat-card-icon{background:var(--danger-bg);color:var(--danger);}

        /* Analytics Card */
        .analytics-card{background:var(--surface);border-radius:16px;padding:24px;border:1px solid var(--border);min-height:420px;animation:fadeInUp .6s ease-out .1s backwards;}
        .analytics-card h3{font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:20px;}

        /* Activity Card */
        .activity-card{background:var(--surface);border-radius:16px;padding:24px;border:1px solid var(--border);min-height:420px;animation:fadeInUp .6s ease-out .2s backwards;overflow:hidden;display:flex;flex-direction:column;}
        .activity-card h3{font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:20px;flex-shrink:0;}
        .activity-feed{flex:1;overflow-y:auto;overflow-x:hidden;padding-right:8px;min-height:0;}
        .activity-feed::-webkit-scrollbar{width:6px;}
        .activity-feed::-webkit-scrollbar-track{background:var(--background);border-radius:3px;}
        .activity-feed::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}
        .activity-feed::-webkit-scrollbar-thumb:hover{background:var(--text-muted);}

        .activity-item{display:flex;gap:14px;padding:14px;border-radius:12px;background:var(--background);margin-bottom:10px;transition:all .2s ease;overflow:hidden;}
        .activity-item:last-child{margin-bottom:0;}
        .activity-item:hover{transform:translateX(4px);background:var(--surface);box-shadow:0 2px 8px rgba(0,0,0,.04);}
        .activity-icon{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .activity-icon svg{width:20px;height:20px;}
        .activity-content{flex:1;min-width:0;overflow:hidden;display:flex;flex-direction:column;}
        .activity-text{font-size:13px;font-weight:500;color:var(--text-primary);margin-bottom:2px;line-height:1.4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
        .activity-time{font-size:11px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

        /* Table Card */
        .table-card{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;display:flex;flex-direction:column;animation:fadeInUp .6s ease-out .3s backwards;}
        .table-card-header{display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--border);}
        .table-scroll{flex:1;overflow-y:auto;}
        .table-scroll table{width:100%;border-collapse:collapse;}
        .table-scroll thead{position:sticky;top:0;z-index:1;background:var(--surface);}
        .table-scroll th{padding:12px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);text-align:left;border-bottom:2px solid var(--border);}
        .table-scroll td{padding:14px 16px;font-size:13px;color:var(--text-primary);border-bottom:1px solid var(--border);vertical-align:middle;}
        .table-scroll tr:hover td{background:var(--background);}
        .table-scroll tr:last-child td{border-bottom:none;}
        .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;}
        .badge-blue{background:var(--info-bg);color:var(--icon-blue);}
        .badge-green{background:var(--success-bg);color:var(--success);}

        /* Animations */
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
        @keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
        .animate-fade-in{animation:fadeIn .5s ease forwards;}
        .delay-1{animation-delay:.1s;}
        .delay-2{animation-delay:.2s;}
        .delay-3{animation-delay:.3s;}

        /* Welcome Greeting */
        .welcome-greeting {
            padding: 4px 0 16px 24px;
            animation: fadeIn .6s ease forwards;
        }
        .welcome-text {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            line-height: 1.2;
        }

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
            border-radius: 12px;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: background 0.2s;
        }
        .hamburger-btn:hover { background: var(--primary-hover); }
        
        /* ── Mobile Header (hidden on desktop) ── */
        .mobile-header{display:none !important;position:fixed;top:0;left:0;right:0;z-index:1000;background:#1A237E;color:#fff;padding:0 16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);align-items:center;justify-content:space-between;height:80px;}
        .mobile-header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            min-width: 0;
        }
        .mobile-logo {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #FBC02D;
            padding: 4px;
            flex-shrink: 0;
        }
        .mobile-logo-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .mobile-brand-text {
            flex: 1;
            min-width: 0;
        }
        .mobile-brand-title {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mobile-brand-subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin: 2px 0 0 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #ffffff;
            cursor: pointer;
            padding: 8px;
            flex-shrink: 0;
            margin-right: 24px;
        }
        .mobile-menu-icon {
            width: 32px;
            height: 32px;
        }
        
        /* ── Mobile Search (hidden on desktop) ── */
        .mobile-search {
            display: none;
            position: relative;
        }
        
        /* ── Quick Actions (hidden on desktop) ── */
        .quick-actions {
            display: none;
        }
        
        /* ── Mobile Barangay List (hidden on desktop) ── */
        .mobile-barangay-list {
            display: none;
        }
        
        /* ── Stat Card Trend (hidden on desktop) ── */
        .stat-card-trend {
            display: none;
        }

        /* ── Responsive: Mobile (< 768px) ── */
        @media (max-width: 767px) {
            /* Mobile-specific background */
            body { background: #F5F7FB; overflow-y: auto !important; }

            /* Welcome greeting - smaller on mobile */
            .welcome-greeting { padding: 12px 0 16px 0; }
            .welcome-text { font-size: 18px; font-weight: 600; }

            /* Fix main container for mobile - allow natural scrolling */
            .app { flex-direction: column; }
            .main, .main-content {
                margin-left: 0 !important;
                max-width: 100% !important;
                padding: 12px 14px !important;
                padding-top: 90px !important;
                height: auto !important;
                overflow: visible !important;
                flex: none !important;
            }
            .main-scroll {
                overflow: visible !important;
                height: auto !important;
                min-height: auto !important;
                flex: none !important;
            }

            /* Hide desktop header & standalone hamburger button, show integrated mobile header */
            header { display: none !important; }
            .hamburger-btn { display: none !important; }
            .mobile-header { display: flex !important; z-index: 998 !important; }

            /* Stat cards - compact modern style */
            .stat-cards {
                grid-template-columns: 1fr 1fr !important;
                gap: 12px !important;
                margin-bottom: 16px !important;
            }
            .stat-card {
                width: 100% !important;
                height: 110px !important;
                padding: 16px !important;
                border-radius: 16px !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px !important;
                position: relative !important;
            }
            .stat-card::before { display: none !important; }
            .stat-card-content { width: 100%; padding-right: 48px; }
            .stat-card-label {
                font-size: 11px !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.3px !important;
                color: var(--text-secondary) !important;
                margin-bottom: 4px !important;
            }
            .stat-card-value { font-size: 28px !important; font-weight: 700 !important; }
            .stat-card-icon {
                width: 40px !important;
                height: 40px !important;
                border-radius: 50% !important;
                position: absolute !important;
                top: 14px !important;
                right: 14px !important;
            }
            .stat-card-icon svg { width: 20px !important; height: 20px !important; }

            /* Quick Actions section */
            .quick-actions { display: flex !important; }
            .quick-actions-grid {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 12px !important;
                margin-bottom: 16px !important;
            }
            .quick-action-btn {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 8px !important;
                padding: 18px 12px !important;
                background: var(--surface) !important;
                border: 1px solid var(--border) !important;
                border-radius: 16px !important;
                text-decoration: none !important;
                color: var(--text-primary) !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                transition: all 0.2s ease !important;
                box-shadow: var(--shadow) !important;
            }
            .quick-action-btn:active {
                transform: scale(0.97) !important;
            }
            .quick-action-btn i, .quick-action-btn [data-lucide] {
                width: 24px !important;
                height: 24px !important;
                color: var(--primary) !important;
            }

            /* Search field */
            .mobile-search { display: block !important; margin-bottom: 16px !important; }
            .mobile-search input {
                width: 100% !important;
                height: 46px !important;
                padding: 0 16px 0 44px !important;
                border: 1px solid var(--border) !important;
                border-radius: 14px !important;
                font-size: 14px !important;
                background: var(--surface) !important;
                box-shadow: var(--shadow) !important;
                font-family: var(--font-family) !important;
            }
            .mobile-search input:focus {
                outline: none !important;
                border-color: var(--primary) !important;
                box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1) !important;
            }
            .mobile-search-icon {
                position: absolute !important;
                left: 14px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                color: var(--text-muted) !important;
                width: 20px !important;
                height: 20px !important;
            }

            /* Top Barangays - display donut chart on mobile */
            .analytics-card {
                min-height: auto !important;
                padding: 16px !important;
                border-radius: 16px !important;
            }
            #barangayChartWrap { 
                display: flex !important; 
                flex-direction: column !important; 
                align-items: center !important; 
                gap: 16px !important;
            }
            #barangayChartBox { 
                width: 220px !important; 
                height: 220px !important; 
            }
            #barangayLegend { 
                width: 100% !important; 
                max-height: 180px !important; 
            }
            .mobile-barangay-list { display: none !important; }
            .barangay-rank-item {
                display: flex !important;
                align-items: center !important;
                gap: 12px !important;
                padding: 12px !important;
                background: var(--background) !important;
                border-radius: 12px !important;
                margin-bottom: 8px !important;
                transition: background 0.15s !important;
            }
            .barangay-rank-item:last-child { margin-bottom: 0 !important; }
            .barangay-rank-item:active { background: var(--border) !important; }
            .barangay-rank {
                width: 28px !important;
                height: 28px !important;
                border-radius: 50% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 12px !important;
                font-weight: 700 !important;
                flex-shrink: 0 !important;
            }
            .barangay-rank.gold { background: #FFF3CD; color: #856404; }
            .barangay-rank.silver { background: #E2E8F0; color: #475569; }
            .barangay-rank.bronze { background: #FED7AA; color: #9A3412; }
            .barangay-rank.normal { background: var(--border); color: var(--text-secondary); }
            .barangay-info { flex: 1 !important; min-width: 0 !important; }
            .barangay-name { font-size: 14px !important; font-weight: 600 !important; color: var(--text-primary) !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .barangay-count { font-size: 12px !important; color: var(--text-secondary) !important; }

            /* Recent Activities */
            .activity-card {
                min-height: auto !important;
                height: 340px !important;
                max-height: 340px !important;
                padding: 16px !important;
                border-radius: 16px !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .activity-feed {
                flex: 1 !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                padding-right: 4px !important;
            }
            .activity-item {
                padding: 12px !important;
                border-radius: 12px !important;
                background: var(--background) !important;
                gap: 12px !important;
            }
            .activity-icon {
                width: 36px !important;
                height: 36px !important;
                border-radius: 10px !important;
            }
            .activity-icon svg { width: 18px !important; height: 18px !important; }
            .activity-text { font-size: 13px !important; font-weight: 600 !important; }
            .activity-text strong { font-weight: 700 !important; }
            .activity-time { font-size: 11px !important; word-break: break-word !important; }

            /* Dashboard grid - single column on mobile */
            .dashboard-grid { grid-template-columns: 1fr !important; gap: 16px !important; margin-bottom: 16px !important; }

            /* Hamburger button adjustments */
            .hamburger-btn { top: 14px !important; left: 16px !important; z-index: 1002 !important; }

            /* Hide trend indicators (fake data) */
            .stat-card-trend { display: none !important; }

            /* Barangay modal on mobile */
            #barangayModal > div {
                max-width: 100% !important;
                max-height: 90vh !important;
                border-radius: 16px !important;
                margin: 0 8px !important;
            }
            #barangayModalCards {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        /* ── Responsive: Small Mobile (< 480px) ── */
        @media (max-width: 479px) {
            .main, .main-content { padding: 10px !important; padding-top: 88px !important; }
            .stat-cards { grid-template-columns: 1fr 1fr !important; gap: 10px !important; }
            .stat-card-value { font-size: 24px !important; }
            .stat-card-icon { width: 36px !important; height: 36px !important; top: 12px !important; right: 12px !important; }
            .stat-card-icon svg { width: 16px !important; height: 16px !important; }
            .stat-card { padding: 14px !important; }
            .quick-actions-grid { gap: 10px !important; }
            .quick-action-btn { padding: 14px 10px !important; font-size: 11px !important; }
            .quick-action-btn i, .quick-action-btn [data-lucide] { width: 22px !important; height: 22px !important; }
            .mobile-header { padding: 0 12px !important; height: 72px !important; }
            .mobile-logo { width: 48px !important; height: 48px !important; }
            .mobile-brand-title { font-size: 16px !important; }
            .mobile-brand-subtitle { font-size: 11px !important; }
            .mobile-menu-icon { width: 28px !important; height: 28px !important; }
            .analytics-card, .activity-card { padding: 14px !important; }
            .activity-item { padding: 10px !important; gap: 10px !important; }
            .activity-icon { width: 32px !important; height: 32px !important; }
            .activity-icon svg { width: 16px !important; height: 16px !important; }
            .activity-text { font-size: 12px !important; }
            .activity-time { font-size: 10px !important; }
            .barangay-rank-item { padding: 10px !important; gap: 10px !important; }
            .barangay-name { font-size: 13px !important; }
            .barangay-count { font-size: 11px !important; }
        }

        /* ── lg: Desktops (1200px+) ── */
        @media (min-width: 1200px) {
            .sidebar { transform: translateX(0) !important; z-index: 1000 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: var(--sidebar-width) !important; max-width: calc(100% - var(--sidebar-width)) !important; padding: var(--content-padding) !important; padding-top: var(--content-padding) !important; height: 100vh !important; overflow: hidden !important; flex: 1 !important; }
            .main-scroll { overflow-y: auto !important; flex: 1 !important; }
            .hamburger-btn { display: none !important; }
            .mobile-header { display: none !important; }
            header {
                display: flex !important;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                height: auto !important;
                padding: 0 !important;
                margin-bottom: 0.75rem !important;
                align-items: center !important;
                gap: 0 !important;
            }
            .dashboard-grid { grid-template-columns: 1.8fr 1fr; }
            .analytics-card, .activity-card { display: flex; flex-direction: column; height: 520px !important; min-height: 520px !important; max-height: 520px !important; flex: none; }
            #barangayChartBox { width: 360px !important; height: 360px !important; }
            .activity-feed { flex: 1; min-height: 0; max-height: none !important; overflow-y: auto; padding-right: 8px; }
        }

        /* ── Large Tablets (992px - 1199px) ── */
        @media (min-width: 992px) and (max-width: 1199px) {
            .sidebar { transform: translateX(-100%) !important; z-index: 1001 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: 0 !important; max-width: 100% !important; padding: 24px !important; padding-top: 72px !important; height: auto !important; overflow: visible !important; }
            .main-scroll { overflow: visible !important; }
            .hamburger-btn { display: flex !important; }
            .mobile-header { display: none !important; }
            header { display: flex !important; }
            .dashboard-grid { grid-template-columns: 1fr; }
            .activity-card { display: flex !important; flex-direction: column !important; height: 400px !important; min-height: 400px !important; max-height: 400px !important; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: none !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ── Small Tablets (768px - 991px) ── */
        @media (min-width: 768px) and (max-width: 991px) {
            .sidebar { transform: translateX(-100%) !important; z-index: 1001 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: 0 !important; max-width: 100% !important; padding: 20px !important; padding-top: 72px !important; height: auto !important; overflow: visible !important; }
            .main-scroll { overflow: visible !important; }
            .hamburger-btn { display: flex !important; }
            .mobile-header { display: none !important; }
            header { display: flex !important; }
            .dashboard-grid { grid-template-columns: 1fr; }
            .activity-card { display: flex !important; flex-direction: column !important; height: 380px !important; min-height: 380px !important; max-height: 380px !important; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: none !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ── Mobile (0px - 767px) ── */
        @media (max-width: 767px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .activity-card { display: flex !important; flex-direction: column !important; height: 350px !important; min-height: 350px !important; max-height: 350px !important; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: none !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ══════════════════════════════════════════════
           REDESIGN: maximize screen space per breakpoint
           ══════════════════════════════════════════════ */

        /* ── Large laptop (1200–1399px): tighter spacing, stat cards 3+2 ── */
        @media (min-width: 1200px) and (max-width: 1399px) {
            .main, .main-content { padding: 24px !important; padding-top: 24px !important; }
            header { margin-bottom: 0.5rem !important; }
            .stat-cards { gap: 18px; margin-bottom: 24px; }
            .stat-card { padding: 18px; }
            .dashboard-grid { gap: 18px !important; margin-bottom: 24px !important; }
            .analytics-card, .activity-card { height: 460px !important; min-height: 460px !important; max-height: 460px !important; }
            #barangayChartBox { width: 300px !important; height: 300px !important; }
        }

        /* ── Tablet (768–1199px): icon-only sidebar, 2-col cards, stacked content ── */
        @media (min-width: 768px) and (max-width: 1199px) {
            /* Sidebar: collapse to icon-only, always visible */
            .sidebar {
                width: 72px !important;
                transform: translateX(0) !important;
                z-index: 1000 !important;
            }
            .sidebar.show { transform: translateX(0) !important; }
            .sidebar-brand { justify-content: center; padding: 1.25rem 0 !important; }
            .sidebar-brand span { display: none !important; }
            .sidebar-menu { padding: 0.75rem 0; }
            .sidebar-menu a { position: relative; justify-content: center; padding: 0.95rem 0 !important; }
            .sidebar-menu a span {
                display: none;
                position: absolute;
                left: 72px;
                top: 50%;
                transform: translateY(-50%);
                background: var(--primary-dark);
                color: #fff;
                padding: 0.4rem 0.65rem;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                white-space: nowrap;
                z-index: 1002;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }
            .sidebar-menu a:hover span { display: block; }
            .sidebar-foot { display: none !important; }
            .sidebar-overlay { display: none !important; }
            .hamburger-btn { display: none !important; }

            /* Main offset for the icon-only sidebar */
            .main, .main-content {
                margin-left: 72px !important;
                max-width: calc(100% - 72px) !important;
                padding: 16px !important;
                padding-top: 16px !important;
                height: auto !important;
                overflow: visible !important;
                flex: none !important;
            }
            .main-scroll { overflow: visible !important; flex: none !important; }

            /* Header: compact */
            header {
                display: flex !important;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                height: auto !important;
                padding: 0.5rem 0 !important;
                margin-bottom: 1rem !important;
                gap: 0 !important;
            }
            header h1 { font-size: 20px !important; }
            #currentDateTime { font-size: 12px !important; }

            /* Stat cards: 2-col grid */
            .stat-cards { grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; margin-bottom: 20px !important; }

            /* Dashboard grid: stacked, chart full width */
            .dashboard-grid { grid-template-columns: 1fr !important; gap: 16px !important; margin-bottom: 16px !important; }

            /* Chart: slightly reduced height, legend wraps cleanly */
            .analytics-card { padding: 20px !important; min-height: auto !important; border-radius: 16px !important; }
            #barangayChartWrap { justify-content: center; }
            #barangayChartBox { width: 300px !important; height: 300px !important; }
            #barangayLegend { max-height: 200px !important; }

            /* Activities below chart */
            .activity-card { height: 380px !important; min-height: 380px !important; max-height: 380px !important; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: none !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ── Small mobile (<400px): keep 2 cards per row, compact ── */
        @media (max-width: 399px) {
            .stat-cards { grid-template-columns: 1fr 1fr !important; gap: 10px !important; }
            .stat-card { height: auto !important; min-height: 92px !important; }
            .stat-card-value { font-size: 24px !important; }
        }
    </style>
</head>
<body>
<div class="app">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i data-lucide="users" style="width:24px;height:24px"></i>
            <span>Senior Citizen</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/senior" class="active"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
            <li><a href="/admin/senior/registration"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>Registration</span></a></li>
            <li><a href="/admin/senior/masterlist"><i data-lucide="list" style="width:20px;height:20px"></i><span>Masterlist</span></a></li>
            <li><a href="/admin/senior/birthdays"><i data-lucide="cake" style="width:20px;height:20px"></i><span>Birthday Beneficiaries</span></a></li>
            <li><a href="/admin/senior/payouts-history"><i data-lucide="history" style="width:20px;height:20px"></i><span>Payout History</span></a></li>
            <li><a href="/admin/senior/statistics"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i><span>Statistics</span></a></li>
            <li><a href="/admin/senior/reports"><i data-lucide="file-text" style="width:20px;height:20px"></i><span>Reports</span></a></li>
            <li><a href="/admin/senior/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>

    <!-- Hamburger Button (fixed position) -->
    <button id="hamburgerBtn" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <i data-lucide="menu" style="width:24px;height:24px"></i>
    </button>

    @php
        $userName = session('admin_user_name') ?? 'Admin User';
        $words = explode(' ', $userName);
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($userName, 0, 2));
        }
        use App\Models\Senior\SeniorCitizenRecord;
        $bdayToday = SeniorCitizenRecord::where('status','active')->whereNotNull('birth_date')->whereRaw("MONTH(birth_date) = ? AND DAY(birth_date) = ?", [now()->format('n'), now()->format('j')])->count();
        $bdayWeek = SeniorCitizenRecord::where('status','active')->whereNotNull('birth_date')->where(function($q){ $s=now();$e=now()->addDays(7);$sMD=$s->format('m-d');$eMD=$e->format('m-d');if($sMD<=$eMD){$q->whereRaw("DATE_FORMAT(birth_date,'%m-%d') BETWEEN ? AND ?",[$sMD,$eMD]);}else{$q->whereRaw("DATE_FORMAT(birth_date,'%m-%d') >= ?",[$sMD])->orWhereRaw("DATE_FORMAT(birth_date,'%m-%d') <= ?",[$eMD]);}})->count();
        $bdayNextMonth = SeniorCitizenRecord::where('status','active')->whereNotNull('birth_date')->whereRaw("MONTH(birth_date) = ?", [now()->addMonth()->format('n')])->count();
    @endphp

    <!-- Mobile Header (visible only on mobile) -->
    <div class="mobile-header">
        <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <div class="mobile-header-brand">
            <div class="mobile-brand-text">
                <h1 class="mobile-brand-title">MSWDO SILANG</h1>
                <p class="mobile-brand-subtitle">Senior Citizen Dashboard</p>
            </div>
            <div class="mobile-logo">
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
                @if($logo)
                <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <!-- Page Header -->
        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8 lg:mb-3">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Welcome, {{ $userName }}</h1>
            </div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                    {{ $initials }}
                </div>
            </div>
        </header>

        <div class="main-scroll">


        <!-- Stat Cards -->
        <div class="stat-cards">
            <a href="/admin/senior/masterlist" style="text-decoration:none">
                <div class="stat-card stat-card-blue">
                    <div class="stat-card-content">
                        <div class="stat-card-label">TOTAL SENIORS</div>
                        <div class="stat-card-value counter" data-target="{{ $totalSeniors }}">{{ $totalSeniors }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="users"></i></div>
                </div>
            </a>
            <a href="/admin/senior/masterlist" style="text-decoration:none">
                <div class="stat-card stat-card-green">
                    <div class="stat-card-content">
                        <div class="stat-card-label">ACTIVE SENIORS</div>
                        <div class="stat-card-value counter" data-target="{{ $activeSeniors }}">{{ $activeSeniors }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="check-circle"></i></div>
                </div>
            </a>
            <a href="/admin/senior/birthdays" style="text-decoration:none">
                <div class="stat-card stat-card-red">
                    <div class="stat-card-content">
                        <div class="stat-card-label">TODAY'S BIRTHDAYS</div>
                        <div class="stat-card-value">{{ $bdayToday }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="cake"></i></div>
                </div>
            </a>
            <a href="/admin/senior/birthdays" style="text-decoration:none">
                <div class="stat-card stat-card-orange">
                    <div class="stat-card-content">
                        <div class="stat-card-label">NEXT 7 DAYS</div>
                        <div class="stat-card-value">{{ $bdayWeek }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="calendar-days"></i></div>
                </div>
            </a>
            <a href="/admin/senior/birthdays" style="text-decoration:none">
                <div class="stat-card stat-card-purple">
                    <div class="stat-card-content">
                        <div class="stat-card-label">NEXT MONTH</div>
                        <div class="stat-card-value">{{ $bdayNextMonth }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="calendar"></i></div>
                </div>
            </a>
        </div>




        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Top Barangays -->
            <div class="analytics-card">
                <div class="flex items-center justify-between mb-5">
                    <h3><i data-lucide="map-pin" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-blue)"></i>Top Barangays</h3>
                    <button class="text-xs font-semibold px-3 py-1 rounded-lg view-all-btn" style="background:var(--info-bg);color:var(--icon-blue);border:none" onclick="document.getElementById('barangayModal').style.display='flex'">View All <i data-lucide="arrow-right" style="width:14px;height:14px;margin-left:2px"></i></button>
                </div>
                <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap" id="barangayChartWrap">
                    <div id="barangayChartBox" style="width:280px;height:280px;flex-shrink:0"><canvas id="barangayDonut"></canvas></div>
                    <div id="barangayLegend" style="flex:1;min-width:0;max-height:220px;overflow-y:auto"></div>
                </div>
                <!-- Mobile Barangay List (visible only on mobile) -->
                <div class="mobile-barangay-list" id="mobileBarangayList"></div>
            </div>

            <!-- Recent Activities -->
            <div class="activity-card">
                <div class="flex items-center justify-between mb-5">
                    <h3><i data-lucide="activity" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-blue)"></i>Recent Activities</h3>
                    @if(count($recentActivities) > 0)
                    <button class="text-xs font-semibold px-3 py-1 rounded-lg" style="background:var(--danger-bg);color:var(--danger);border:none" onclick="confirmClearActivities()">Clear <i data-lucide="trash-2" style="width:12px;height:12px;margin-left:2px;vertical-align:middle"></i></button>
                    @endif
                </div>
                <div class="activity-feed">
                    @if(count($recentActivities) > 0)
                        @foreach($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--info-bg);color:var(--icon-blue)">
                                <i data-lucide="{{ $activity->action == 'registered' ? 'user-plus' : ($activity->action == 'archived' ? 'archive' : ($activity->action == 'restored' ? 'undo-2' : 'id-card')) }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">{{ ucfirst($activity->action) }} <strong>{{ $activity->name }}</strong></div>
                                <div class="activity-time">{{ $activity->identifier }} &middot; {{ $activity->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8" style="color:var(--text-muted)">
                            <i data-lucide="inbox" style="width:32px;height:32px;margin:0 auto 8px;display:block;color:#D1D5DB"></i>
                            <span class="text-sm">No recent activities</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        </div>

    </div>
</div>


<!-- Barangay Distribution Modal -->
<div id="barangayModal" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:20px" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--surface);border-radius:16px;width:100%;max-width:800px;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15)">
        <div class="flex items-center justify-between px-6 py-4" style="background:var(--accent-yellow);color:var(--primary)">
            <h4 class="font-bold flex items-center gap-2 m-0"><i data-lucide="map-pin" style="width:20px;height:20px"></i> All Barangays Distribution</h4>
            <button onclick="document.getElementById('barangayModal').style.display='none'" class="w-8 h-8 rounded-full flex items-center justify-center" style="background:rgba(0,0,0,.1);border:none;color:var(--primary)"><i data-lucide="x" style="width:16px;height:16px"></i></button>
        </div>
        <div class="p-6 overflow-auto" style="max-height:60vh">
            <div id="barangayModalCards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px"></div>
        </div>
    </div>
</div>

<!-- Hidden form for secure POST logout -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none">@csrf</form>

<script>
    // Date time
    function updateDateTime(){
        const now=new Date();
        const compact=window.innerWidth<1200;
        const opts=compact
            ? {month:'short',day:'numeric',hour:'numeric',minute:'2-digit',hour12:true}
            : {weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'numeric',minute:'2-digit',hour12:true};
        const dtEl=document.getElementById('currentDateTime');
        if(dtEl) dtEl.textContent=now.toLocaleDateString('en-US',opts).replace(',',' at');
        
        // Update mobile greeting based on time
        const hour=now.getHours();
        let greeting='Good Evening';
        if(hour<12) greeting='Good Morning';
        else if(hour<17) greeting='Good Afternoon';
        const greetingEl=document.querySelector('.mobile-greeting');
        if(greetingEl) greetingEl.textContent=greeting+', Administrator';
    }
    updateDateTime();
    setInterval(updateDateTime,60000);
    window.addEventListener('resize', function(){ updateDateTime(); });

    // Counter animation
    document.querySelectorAll('.counter').forEach(counter=>{
        const target=parseInt(counter.getAttribute('data-target'));
        if(!target)return;
        const duration=2000;
        const step=target/(duration/16);
        let current=0;
        const update=()=>{current+=step;if(current<target){counter.textContent=Math.floor(current);requestAnimationFrame(update);}else{counter.textContent=target;}};
        update();
    });

        // Toggle sidebar
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

    // Welcome popup
    document.addEventListener('DOMContentLoaded',function(){
        @if($justLoggedIn ?? false)
            Swal.fire({
                title:'Welcome Admin!',
                html:'<div style="text-align:center;line-height:1.7;color:#475569;font-size:15px"><p style="margin:0 0 8px;font-weight:500">Senior Citizen Officer</p></div>',
                icon:'info',
                confirmButtonColor:'#1A237E',
                confirmButtonText:'Continue',
                background:'#ffffff',
                customClass:{popup:'rounded-4 shadow-lg'},
                allowOutsideClick:false
            });
        @endif
        initBarangayChart();
    });

    // Barangay Distribution
    function initBarangayChart(){
        const barangayData=@json($barangayDistribution);
        const sortedData=[...barangayData].sort((a,b)=>b.count-a.count);
        renderTopBarangaysList(sortedData);
        renderMobileBarangayList(sortedData);
    }

    let barangayDonutChart = null;

    function renderTopBarangaysList(data){
        const legendContainer=document.getElementById('barangayLegend');
        const canvas=document.getElementById('barangayDonut');
        if(!canvas)return;

        const totalCount=data.reduce((sum,item)=>sum+item.count,0);
        const colors=data.map((_,i)=>{
            const hue=(i*137.508)%360;
            return `hsl(${hue},65%,50%)`;
        });

        // Donut chart
        if(barangayDonutChart) barangayDonutChart.destroy();
        barangayDonutChart=new Chart(canvas,{
            type:'doughnut',
            data:{
                labels:data.map(d=>d.barangay),
                datasets:[{
                    data:data.map(d=>d.count),
                    backgroundColor:colors.slice(0,data.length),
                    borderWidth:0,
                    hoverOffset:4
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:true,
                cutout:'65%',
                plugins:{
                    legend:{display:false},
                    tooltip:{
                        backgroundColor:'#111827',
                        titleFont:{family:'Public Sans',size:13,weight:'600'},
                        bodyFont:{family:'Public Sans',size:12},
                        padding:10,
                        cornerRadius:8,
                        callbacks:{
                            label:function(ctx){
                                const pct=((ctx.parsed/totalCount)*100).toFixed(1);
                                return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                            }
                        }
                    }
                }
            },
            plugins:[{
                id:'centerText',
                afterDraw(chart){
                    const {ctx,chartArea:{left,right,top,bottom}}=chart;
                    const cx=(left+right)/2;
                    const cy=(top+bottom)/2;
                    ctx.save();
                    ctx.textAlign='center';ctx.textBaseline='middle';
                    ctx.font='bold 22px Public Sans';ctx.fillStyle='#111827';
                    ctx.fillText(totalCount,cx,cy-6);
                    ctx.font='500 11px Public Sans';ctx.fillStyle='#6B7280';
                    ctx.fillText('Total',cx,cy+14);
                    ctx.restore();
                }
            }]
        });

        // Legend
        if(legendContainer){
            legendContainer.innerHTML=data.map((item,i)=>{
                const pct=totalCount>0?((item.count/totalCount)*100).toFixed(1):0;
                const color=colors[i]||'#9CA3AF';
                return `<div class="flex items-center gap-3 py-2 px-2 rounded-lg" style="transition:background .2s" onmouseover="this.style.background='var(--background)'" onmouseout="this.style.background=''">
                    <div class="rounded-sm flex-shrink-0" style="width:10px;height:10px;background:${color}"></div>
                    <span class="text-sm flex-1 truncate">${item.barangay}</span>
                    <span class="text-sm font-semibold" style="color:var(--primary)">${item.count}</span>
                    <span class="text-xs font-medium" style="color:var(--text-muted);width:42px;text-align:right">${pct}%</span>
                </div>`;
            }).join('');
        }
    }

    function renderModalCards(){
        const container=document.getElementById('barangayModalCards');
        if(!container)return;
        const barangayData=@json($barangayDistribution);
        const sortedData=[...barangayData].sort((a,b)=>b.count-a.count);
        container.innerHTML=sortedData.map((item,index)=>{
            const bgColor=index===0?'rgba(26,35,126,.1)':index===1?'rgba(59,130,246,.1)':index===2?'rgba(99,102,241,.1)':'rgba(107,114,128,.05)';
            const textColor=index<3?'var(--primary)':'var(--text-primary)';
            const borderColor=index<3?'var(--primary)':'var(--border)';
            return `<div style="background:${bgColor};border:1px solid ${borderColor};border-radius:8px;padding:1rem;transition:transform .2s,box-shadow .2s" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="text-xs font-medium mb-1" style="color:var(--text-secondary)">${item.barangay}</div>
                <div class="text-xl font-bold" style="color:${textColor}">${item.count}</div>
            </div>`;
        }).join('');
    }

    // Render mobile barangay list (ranked list instead of donut chart)
    function renderMobileBarangayList(data){
        const container=document.getElementById('mobileBarangayList');
        if(!container)return;
        const top5=data.slice(0,5);
        container.innerHTML=top5.map((item,index)=>{
            let rankClass='normal';
            let rankEmoji=index+1;
            if(index===0){rankClass='gold';rankEmoji='🥇';}
            else if(index===1){rankClass='silver';rankEmoji='🥈';}
            else if(index===2){rankClass='bronze';rankEmoji='🥉';}
            return `<div class="barangay-rank-item">
                <div class="barangay-rank ${rankClass}">${rankEmoji}</div>
                <div class="barangay-info">
                    <div class="barangay-name">${item.barangay}</div>
                    <div class="barangay-count">${item.count} Seniors</div>
                </div>
            </div>`;
        }).join('');
    }

    // Confirm clear activities
    function confirmClearActivities(){
        Swal.fire({
            title:'Clear Recent Activities?',
            text:'This will remove all recent activity logs. This action cannot be undone.',
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor:'#DC2626',
            cancelButtonColor:'#6B7280',
            confirmButtonText:'Yes, clear all',
            cancelButtonText:'Cancel',
            background:'#ffffff',
            customClass:{popup:'rounded-4 shadow-lg'}
        }).then(result=>{
            if(result.isConfirmed){
                const form=document.createElement('form');
                form.method='POST';
                form.action='{{ route("admin.senior.clear-activities") }}';
                const csrf=document.createElement('input');
                csrf.type='hidden';csrf.name='_token';csrf.value='{{ csrf_token() }}';
                form.appendChild(csrf);document.body.appendChild(form);form.submit();
            }
        });
    }

    // Confirm logout
    function confirmLogout(e){
        e.preventDefault();
        Swal.fire({
            title:'Are you sure?',
            text:'Do you really want to log out?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor:'#1A237E',
            cancelButtonColor:'#EF4444',
            confirmButtonText:'Yes, log out',
            cancelButtonText:'Cancel',
            background:'#ffffff',
            customClass:{popup:'rounded-4 shadow-lg'}
        }).then(result=>{
            if(result.isConfirmed) document.getElementById('logout-form').submit();
        });
    }

    // Toggle mobile bottom nav extra row
    function toggleMobileMoreNav(){
        const extraRow = document.getElementById('mobileNavExtra');
        const icon = document.getElementById('mobileMoreIcon');
        const btn = document.querySelector('.mobile-more-toggle');
        if(!extraRow) return;

        if(extraRow.style.display === 'none' || extraRow.style.display === ''){
            extraRow.style.display = 'flex';
            if(btn) btn.classList.add('active');
            if(icon) {
                icon.setAttribute('data-lucide', 'chevron-down');
                lucide.createIcons();
            }
        } else {
            extraRow.style.display = 'none';
            if(btn) btn.classList.remove('active');
            if(icon) {
                icon.setAttribute('data-lucide', 'chevron-up');
                lucide.createIcons();
            }
        }
    }

    lucide.createIcons();

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
                    var ov = document.getElementById('sidebarOverlay');
                    if (ov) ov.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1200) {
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
</script>
</body>
</html>
