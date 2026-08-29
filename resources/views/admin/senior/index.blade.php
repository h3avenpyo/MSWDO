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
            --primary-dark:#121858;
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
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        /* Dashboard Grid */
        .dashboard-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;width:100%;}
        @media(max-width:1199px){.dashboard-grid{grid-template-columns:1fr;}}
        @media(max-width:991px){.dashboard-grid{grid-template-columns:1fr;}}
        @media(max-width:767px){.dashboard-grid{grid-template-columns:1fr;}}
        @media(max-width:575px){.dashboard-grid{grid-template-columns:1fr;}}

        /* Stat Cards */
        .stat-cards{display:grid;grid-template-columns:repeat(5,1fr);gap:20px;margin-bottom:12px;animation:fadeInUp .6s ease-out;flex-shrink:0;}
        .stat-cards>.stat-card{display:flex;width:100%;min-width:0;}
        @media(min-width:1200px) and (max-width:1399px){.stat-cards{grid-template-columns:repeat(6,1fr);gap:18px;}.stat-cards>.stat-card:nth-child(1),.stat-cards>.stat-card:nth-child(2),.stat-cards>.stat-card:nth-child(3){grid-column:span 2;}.stat-cards>.stat-card:nth-child(4),.stat-cards>.stat-card:nth-child(5){grid-column:span 3;}}
        @media(min-width:992px) and (max-width:1199px){.stat-cards{grid-template-columns:repeat(2,1fr);gap:16px;}.stat-cards>.stat-card:nth-child(5){grid-column:span 2;}}
        @media(min-width:768px) and (max-width:991px){.stat-cards{grid-template-columns:repeat(2,1fr);gap:16px;}.stat-cards>.stat-card:nth-child(5){grid-column:span 2;}}
        @media(min-width:576px) and (max-width:767px){.stat-cards{grid-template-columns:repeat(2,1fr);gap:12px;}.stat-cards>.stat-card:nth-child(5){grid-column:span 2;}}
        @media(min-width:375px) and (max-width:575px){.stat-cards{grid-template-columns:1fr 1fr;gap:12px;}.stat-cards>.stat-card:nth-child(5){grid-column:1 / -1;}}
        @media(max-width:374px){.stat-cards{grid-template-columns:1fr 1fr;}.stat-cards>.stat-card:nth-child(5){grid-column:1 / -1;}}
        @media(max-width:1199.98px){.stat-cards>.stat-card:nth-child(5),.stat-cards>.stat-card.stat-card-orange{grid-column:1 / -1 !important;}}

        .stat-card{width:100%;background:var(--surface);border-radius:24px;padding:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;position:relative;overflow:hidden;min-height:0;cursor:default;}
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
        .analytics-card{width:100%;background:var(--surface);border-radius:24px;padding:24px;border:1px solid var(--border);min-height:0;height:550px;display:flex;flex-direction:column;animation:fadeInUp .6s ease-out .1s backwards;}
        .analytics-card h3{font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:20px;}
        @media(min-width:1400px){.analytics-card{width:100%;height:550px;}#barangayChartWrap{flex-direction:row;align-items:flex-start;}#barangayLegend{max-height:450px;}}
        @media(min-width:1200px) and (max-width:1399px){.analytics-card{width:100%;height:550px;}#barangayChartWrap{flex-direction:row;align-items:flex-start;}#barangayLegend{max-height:400px;}}
        @media(min-width:992px) and (max-width:1199px){.analytics-card{width:100%;height:450px;}#barangayChartWrap{flex-direction:row;align-items:flex-start;}#barangayLegend{max-height:350px;}}
        @media(min-width:768px) and (max-width:991px){.analytics-card{width:100%;height:400px;}#barangayChartWrap{flex-direction:row;align-items:flex-start;}#barangayLegend{max-height:300px;}}
        @media(min-width:576px) and (max-width:767px){.analytics-card{width:100%;height:auto;}#barangayLegend{max-height:180px;}}
        @media(max-width:575px){.analytics-card{width:100%;height:auto;}#barangayLegend{max-height:180px;}}

        /* Activity Card */
        .activity-card{width:100%;background:var(--surface);border-radius:24px;padding:24px;border:1px solid var(--border);min-height:0;animation:fadeInUp .6s ease-out .2s backwards;display:flex;flex-direction:column;}
        .activity-card h3{font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:20px;flex-shrink:0;}
        .activity-feed{flex:1;overflow-y:auto;overflow-x:hidden;padding-right:8px;min-height:0;max-height:400px;scrollbar-width:thin;scrollbar-color:#94A3B8 #E2E8F0;}
        .activity-feed::-webkit-scrollbar{width:8px;}
        .activity-feed::-webkit-scrollbar-track{background:#E2E8F0;border-radius:4px;}
        .activity-feed::-webkit-scrollbar-thumb{background:#94A3B8;border-radius:4px;}
        .activity-feed::-webkit-scrollbar-thumb:hover{background:var(--text-secondary);}

        .activity-item{display:flex;gap:14px;padding:16px;border-radius:12px;background:var(--background);margin-bottom:12px;transition:all .2s ease;}
        .activity-item:last-child{margin-bottom:0;}
        .activity-item:hover{transform:translateX(4px);background:var(--surface);box-shadow:0 2px 8px rgba(0,0,0,.04);}
        .activity-icon{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .activity-icon svg{width:20px;height:20px;}
        .activity-content{flex:1;min-width:0;}
        .activity-text{font-size:14px;font-weight:500;color:var(--text-primary);margin-bottom:4px;line-height:1.4;}
        .activity-time{font-size:12px;color:var(--text-muted);}

        /* Table Card */
        .table-card{background:var(--surface);border-radius:24px;border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;display:flex;flex-direction:column;animation:fadeInUp .6s ease-out .3s backwards;}
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

            /* Hide desktop header & standalone hamburger button, show integrated mobile header */
            header { display: none !important; }
            .activity-feed { max-height: 300px !important; }

            /* Stat cards - match statistics layout */
            .stat-cards {
                grid-template-columns: 1fr 1fr !important;
                gap: 12px !important;
                margin-bottom: 16px !important;
            }
            .stat-cards > .stat-card:nth-child(5) {
                grid-column: 1 / -1 !important;
            }
            .stat-card {
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
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
                letter-spacing: 0.5px !important;
                color: var(--text-secondary) !important;
                margin-bottom: 4px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .stat-card-value { font-size: 28px !important; font-weight: 700 !important; line-height: 1 !important; }
            .stat-card-icon {
                width: 40px !important;
                height: 40px !important;
                border-radius: 50% !important;
                position: absolute !important;
                top: 14px !important;
                right: 14px !important;
                flex-shrink: 0 !important;
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
                width: 100% !important;
                min-height: auto !important;
                padding: 16px !important;
                border-radius: 16px !important;
                overflow-x: hidden !important;
                max-width: 100% !important;
                height: auto !important;
            }
            #barangayChartWrap { 
                display: flex !important; 
                flex-direction: column !important; 
                align-items: center !important; 
                justify-content: center !important; 
                gap: 16px !important;
                flex-wrap: nowrap !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            #barangayChartBox { 
                width: min(220px, 100%) !important; 
                height: auto !important; 
                aspect-ratio: 1 / 1 !important; 
                max-width: 100% !important;
            }
            #barangayLegend { 
                width: 100% !important; 
                max-width: 100% !important; 
                max-height: 180px !important; 
                overflow-y: auto !important;
                overflow-x: hidden !important;
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
                padding: 16px !important;
                border-radius: 16px !important;
                display: flex !important;
                flex-direction: column !important;
                overflow-x: hidden !important;
                max-width: 100% !important;
            }
            .activity-feed {
                flex: 1 !important;
                min-height: 0 !important;
                max-height: 350px !important;
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
                border-radius: 50% !important;
            }
            .activity-icon svg { width: 18px !important; height: 18px !important; }
            .activity-text { font-size: 13px !important; font-weight: 600 !important; }
            .activity-text strong { font-weight: 700 !important; }
            .activity-time { font-size: 11px !important; word-break: break-word !important; }

            /* Dashboard grid - single column on mobile */
            .dashboard-grid { grid-template-columns: 1fr !important; gap: 16px !important; margin-bottom: 16px !important; }

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
            .stat-cards { grid-template-columns: 1fr 1fr !important; gap: 10px !important; }
            .stat-card-value { font-size: 24px !important; }
            .stat-card-icon { width: 36px !important; height: 36px !important; top: 12px !important; right: 12px !important; }
            .stat-card-icon svg { width: 16px !important; height: 16px !important; }
            .stat-card { padding: 14px !important; }
            .quick-actions-grid { gap: 10px !important; }
            .quick-action-btn { padding: 14px 10px !important; font-size: 11px !important; }
            .quick-action-btn i, .quick-action-btn [data-lucide] { width: 22px !important; height: 22px !important; }
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
            .dashboard-grid { grid-template-columns: minmax(0, 1.8fr) minmax(0, 1fr); gap: 24px; margin-bottom: 20px; }
            #barangayChartWrap { flex: 1 !important; min-height: 0 !important; height: 100% !important; flex-wrap: nowrap !important; align-items: center !important; }
            #barangayChartBox { position: relative !important; flex: 1 1 0% !important; min-width: 0 !important; min-height: 0 !important; width: auto !important; height: 100% !important; }
            #barangayLegend { flex: 0 1 300px !important; }
            .activity-card { height: 550px; min-height: 550px; max-height: 550px; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: none !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ── Large Tablets (992px - 1199px) ── */
        @media (min-width: 992px) and (max-width: 1199px) {
            header { display: flex !important; }
            .dashboard-grid { grid-template-columns: 1fr; }
            .activity-card { display: flex !important; flex-direction: column !important; min-height: 0 !important; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: 380px !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ── Small Tablets (768px - 991px) ── */
        @media (min-width: 768px) and (max-width: 991px) {
            header { display: flex !important; }
            .dashboard-grid { grid-template-columns: 1fr; }
            .activity-card { display: flex !important; flex-direction: column !important; min-height: 0 !important; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: 360px !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ── Mobile (0px - 767px) ── */
        @media (max-width: 767px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .activity-card { display: flex !important; flex-direction: column !important; min-height: 0 !important; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: 300px !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ══════════════════════════════════════════════
           REDESIGN: maximize screen space per breakpoint
           ══════════════════════════════════════════════ */

        /* ── Large laptop (1200–1399px): tighter spacing, stat cards 3+2 ── */
        @media (min-width: 1200px) and (max-width: 1399px) {
            header { margin-bottom: 0.5rem !important; }
            .stat-card { padding: 18px; }
            .dashboard-grid { gap: 24px !important; margin-bottom: 20px !important; }
            #barangayChartBox { position: relative !important; flex: 1 1 0% !important; min-width: 0 !important; min-height: 0 !important; width: auto !important; height: 100% !important; }
            .activity-card { height: 550px !important; min-height: 550px !important; max-height: 550px !important; }
            .activity-feed { max-height: none !important; }
        }

        /* ── Tablet (768–1199px): icon-only sidebar, 2-col cards, stacked content ── */
        @media (min-width: 768px) and (max-width: 1199px) {
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
            .stat-cards > .stat-card:nth-child(5) { grid-column: 1 / -1 !important; }

            /* Dashboard grid: stacked, chart full width */
            .dashboard-grid { grid-template-columns: 1fr !important; gap: 16px !important; margin-bottom: 16px !important; }

            /* Chart: stacked on tablet, chart on top, legend below */
            .analytics-card { width: 100% !important; padding: 20px !important; min-height: auto !important; border-radius: 16px !important; overflow-x: hidden !important; max-width: 100% !important; height: auto !important; }
            #barangayChartWrap { justify-content: center; flex-direction: column; align-items: center; gap: 16px; flex-wrap: nowrap; width: 100%; max-width: 100%; }
            #barangayChartBox { width: min(280px, 100%) !important; height: auto !important; aspect-ratio: 1 / 1 !important; max-width: 100% !important; }
            #barangayLegend { width: 100% !important; max-width: 100% !important; max-height: 200px !important; overflow-y: auto !important; overflow-x: hidden !important; }

            /* Activities below chart */
            .activity-card { display: flex !important; flex-direction: column !important; min-height: 0 !important; height: 520px !important; max-height: 520px !important; }
            .activity-feed { flex: 1 !important; min-height: 0 !important; max-height: none !important; overflow-y: auto !important; overflow-x: hidden !important; padding-right: 8px !important; }
        }

        /* ── Small mobile (<375px): still two cards per row (matches statistics) ── */
        @media (max-width: 374px) {
            .stat-cards { grid-template-columns: 1fr 1fr !important; gap: 10px !important; }
            .stat-card { height: auto !important; min-height: 0 !important; }
            .stat-card-value { font-size: 24px !important; }
        }

        /* Hide page-header date/time + avatar on small screens */
        @media (max-width: 767.98px) {
            header #currentDateTime,
            header [title^="User Profile:"] { display: none !important; }
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'dashboard', 'mobileSubtitle' => 'Senior Citizen Dashboard'])

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
        $archivedSeniors = SeniorCitizenRecord::where('status','archived')->whereNotNull('birth_date')->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60')->count();
    @endphp

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
            <div class="stat-card stat-card-blue">
                <div class="stat-card-content">
                    <div class="stat-card-label">TOTAL SENIORS</div>
                    <div class="stat-card-value counter" data-target="{{ $totalSeniors }}">{{ $totalSeniors }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="users"></i></div>
            </div>
            <div class="stat-card stat-card-green">
                <div class="stat-card-content">
                    <div class="stat-card-label">ACTIVE SENIORS</div>
                    <div class="stat-card-value counter" data-target="{{ $activeSeniors }}">{{ $activeSeniors }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="check-circle"></i></div>
            </div>
            <div class="stat-card stat-card-purple">
                <div class="stat-card-content">
                    <div class="stat-card-label">ARCHIVED</div>
                    <div class="stat-card-value">{{ $archivedSeniors ?? 0 }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="archive"></i></div>
            </div>
            <div class="stat-card stat-card-red">
                <div class="stat-card-content">
                    <div class="stat-card-label">TODAY'S BIRTHDAYS</div>
                    <div class="stat-card-value">{{ $bdayToday }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="cake"></i></div>
            </div>
            <div class="stat-card stat-card-orange">
                <div class="stat-card-content">
                    <div class="stat-card-label">TOTAL PAYOUT</div>
                    <div class="stat-card-value">₱{{ number_format($totalAmountReleased ?? 0, 2) }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="wallet"></i></div>
            </div>
        </div>




        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Top Barangays -->
            <div class="analytics-card">
                <div class="flex items-center justify-between mb-1">
                    <h3><i data-lucide="map-pin" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-blue)"></i>Top Barangays</h3>
                </div>
                <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap" id="barangayChartWrap">
                    <div id="barangayChartBox" style="width:280px;height:280px;flex-shrink:0"><canvas id="barangayDonut"></canvas></div>
                    <div id="barangayLegend" style="flex:1;min-width:0;max-height:270px;overflow-y:auto"></div>
                </div>
                <style>
                    @media(min-width:1200px) and (max-width:1399px){#barangayLegend{max-height:220px;}}
                    @media(min-width:992px) and (max-width:1199px){#barangayLegend{max-height:170px;}}
                    @media(min-width:768px) and (max-width:991px){#barangayLegend{max-height:120px;}}
                </style>
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
                        @php
                        $iconMap = [
                            'registered' => ['plus-circle', 'var(--success-bg)', 'var(--success)'],
                            'updated' => ['edit', 'var(--info-bg)', 'var(--info)'],
                            'viewed' => ['eye', 'var(--background)', 'var(--text-muted)'],
                            'archived' => ['archive', 'var(--danger-bg)', 'var(--danger)'],
                            'deleted' => ['trash-2', 'var(--danger-bg)', 'var(--danger)'],
                            'restored' => ['undo-2', 'var(--info-bg)', 'var(--info)'],
                            'printed birthday payout PDF' => ['printer', 'var(--purple-bg)', 'var(--purple)'],
                            'printed birthday payout receipt' => ['printer', 'var(--purple-bg)', 'var(--purple)'],
                            'released birthday payouts' => ['send', 'var(--success-bg)', 'var(--success)'],
                        ];
                        $ic = $iconMap[$activity->action] ?? $iconMap['updated'];
                        @endphp
                        <div class="activity-item">
                            <div class="activity-icon" style="background:{{ $ic[1] }};color:{{ $ic[2] }}">
                                <i data-lucide="{{ $ic[0] }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">{{ ucfirst($activity->action) }} <strong>{{ $activity->name }}</strong> ({{ $activity->identifier }})</div>
                                <div class="activity-time">{{ $activity->created_at->diffForHumans() }} &middot; {{ $activity->created_at->format('M d, Y g:i A') }}</div>
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
<div id="barangayModal" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(4px)" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--surface);border-radius:14px;width:100%;max-width:780px;max-height:75vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15)">
        <div class="flex items-center justify-between px-5 py-3" style="background:#1A237E;color:#ffffff">
            <h4 class="font-semibold flex items-center gap-2 m-0 text-white" style="font-size:1rem"><i data-lucide="map-pin" style="width:20px;height:20px"></i> All Barangays Distribution</h4>
            <button onclick="document.getElementById('barangayModal').style.display='none'" class="w-8 h-8 rounded-full flex items-center justify-center" style="background:rgba(255,255,255,.15);border:none;color:#ffffff;cursor:pointer"><i data-lucide="x" style="width:16px;height:16px"></i></button>
        </div>
        <div class="p-5 overflow-auto" style="flex:1;max-height:60vh;-webkit-overflow-scrolling:touch">
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

    // Barangay Distribution
    function initBarangayChart(){
        const barangayData=@json($barangayDistribution);
        const sortedData=[...barangayData].sort((a,b)=>a.barangay.localeCompare(b.barangay));
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
                maintainAspectRatio:false,
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
            legendContainer.style.paddingRight='16px';
            legendContainer.style.overflowY='auto';
            legendContainer.style.scrollbarWidth='thin';
            legendContainer.style.scrollbarColor='transparent transparent';
            legendContainer.innerHTML=data.map((item,i)=>{
                const color=colors[i]||'#9CA3AF';
                return `<div class="flex items-center gap-2 py-2 px-2 rounded-lg" style="transition:background .2s" onmouseover="this.style.background='var(--background)'" onmouseout="this.style.background=''">
                    <div class="rounded-sm flex-shrink-0" style="width:12px;height:12px;background:${color}"></div>
                    <span class="text-sm flex-1 min-w-0 font-medium" style="color:var(--text-primary)">${item.barangay}</span>
                    <span class="text-sm font-semibold" style="color:var(--primary);flex-shrink:0">${item.count}</span>
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

    initBarangayChart();

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
</script>
</body>
</html>
