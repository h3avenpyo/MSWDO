<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Social Case Study System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root{
            /* Government Color Palette */
            --primary: #1A237E;
            --primary-hover: #121858;
            --sidebar-bg: #1A237E;
            --background: #F5F7FB;
            --surface: #FFFFFF;
            --border: #E5E7EB;
            
            /* Text Colors */
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            
            /* Status Colors */
            --success: #16A34A;
            --success-bg: #ECFDF5;
            --warning: #F59E0B;
            --warning-bg: #FFF7ED;
            --danger: #DC2626;
            --danger-bg: #FEF2F2;
            --info: #3B82F6;
            --info-bg: #EEF2FF;
            --purple: #7C3AED;
            --purple-bg: #F3E8FF;
            --accent-yellow: #FBC02D;
            
            /* Icon Colors */
            --icon-blue: #3B82F6;
            --icon-green: #16A34A;
            --icon-purple: #7C3AED;
            
            /* Dimensions */
            --sidebar-width: 260px;
            --topnav-height: 70px;
            --content-padding: 32px;
            --card-gap: 24px;
            --card-padding: 24px;
            --radius: 16px;
            --shadow: 0 10px 30px rgba(15,23,42,.08);
            --shadow-hover: 0 20px 40px rgba(15,23,42,.12);
            
            /* Typography */
            --font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }
        
        *{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);height:100%;}
        body{font-size:14px;line-height:1.5;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        input,select,textarea{font-family:inherit;font-size:14px;}
        .app{display:flex;min-height:100vh;}

        /* ---------- Sidebar ---------- */
        .sidebar{
            width:var(--sidebar-width);
            flex-shrink:0;
            background:var(--primary);
            color:#FFFFFF;
            position:fixed;
            left:0;
            top:0;
            height:100vh;
            z-index:1000;
            display:flex;
            flex-direction:column;
            transition:transform .3s ease;
        }
        .sidebar-brand{
            height:72px;
            padding:0 1.5rem;
            border-bottom:1px solid rgba(255,255,255,.1);
            color:#fff;
            font-weight:700;
            font-size:1.1rem;
            display:flex;
            align-items:center;
            gap:.65rem;
        }
        .sidebar-brand i{font-size:1.3rem;color:var(--accent-yellow);}
        .sidebar-menu{
            list-style:none;
            margin:0;
            padding:1rem 0;
            flex:1;
        }
        .sidebar-menu li{margin-bottom:.2rem;}
        .sidebar-menu a{
            color:rgba(255,255,255,.75);
            padding:.75rem 1.5rem;
            display:flex;
            align-items:center;
            gap:.75rem;
            text-decoration:none;
            font-size:.9rem;
            border-left:3px solid transparent;
            transition:all .2s ease;
        }
        .sidebar-menu a:hover{
            background:rgba(255,255,255,.1);
            color:var(--accent-yellow);
        }
        .sidebar-menu a.active{
            background:rgba(255,255,255,.1);
            color:var(--accent-yellow);
            border-left-color:var(--accent-yellow);
        }
        .sidebar-menu a i{width:20px;text-align:center;}

        /* ---------- Dashboard Grid ---------- */
        .dashboard-grid{
            display:grid;
            grid-template-columns: 1.8fr 1fr;
            gap:24px;
        }
        @media (max-width:1024px){.dashboard-grid{grid-template-columns:1fr;}}
        
        /* ---------- Modern Stat Cards ---------- */
        .stat-cards{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:24px;
            margin-bottom:32px;
            animation:fadeInUp 0.6s ease-out;
        }
        @media (max-width:768px){.stat-cards{grid-template-columns:1fr;}}
        
        .stat-card{
            background:var(--surface);
            border-radius:16px;
            padding:24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            box-shadow:var(--shadow);
            border:1px solid var(--border);
            transition:all 0.3s ease;
            position:relative;
            overflow:hidden;
        }
        .stat-card::before{
            content:'';
            position:absolute;
            left:0;
            top:0;
            bottom:0;
            width:4px;
            transition:all 0.3s ease;
        }
        .stat-card:hover{
            transform:translateY(-2px);
            box-shadow:var(--shadow-hover);
        }
        .stat-card-blue::before{background:var(--icon-blue);}
        .stat-card-green::before{background:var(--icon-green);}
        .stat-card-purple::before{background:var(--icon-purple);}
        
        .stat-card-content{
            flex:1;
        }
        .stat-card-label{
            font-size:12px;
            font-weight:600;
            letter-spacing:0.5px;
            text-transform:uppercase;
            color:var(--text-secondary);
            margin-bottom:8px;
        }
        .stat-card-value{
            font-size:42px;
            font-weight:700;
            color:var(--text-primary);
            line-height:1;
        }
        .stat-card-icon{
            width:64px;
            height:64px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }
        .stat-card-blue .stat-card-icon{
            background:var(--info-bg);
            color:var(--icon-blue);
        }
        .stat-card-green .stat-card-icon{
            background:var(--success-bg);
            color:var(--icon-green);
        }
        .stat-card-purple .stat-card-icon{
            background:var(--purple-bg);
            color:var(--icon-purple);
        }
        .stat-card-icon svg{
            width:32px;
            height:32px;
        }

        /* ---------- Workflow Cards ---------- */
        .workflow-cards{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:16px;
            margin-bottom:24px;
        }
        .workflow-card{
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:var(--radius);
            padding:16px;
            box-shadow:var(--shadow);
            position:relative;
            transition:all 0.2s ease;
        }
        .workflow-card:hover{
            transform:translateY(-2px);
            box-shadow:0 4px 20px rgba(0,0,0,0.08);
        }
        .workflow-card .arrow{
            position:absolute;
            right:-8px;
            top:50%;
            transform:translateY(-50%);
            color:var(--text-muted);
            font-size:20px;
        }
        .workflow-card:last-child .arrow{display:none;}
        .workflow-card .num{
            font-size:28px;
            font-weight:700;
            line-height:1.1;
        }
        .workflow-card .label{
            color:var(--text-secondary);
            font-size:13px;
            margin-top:4px;
            font-weight:500;
        }
        .workflow-card .trend{
            font-size:11px;
            margin-top:8px;
            font-weight:600;
        }
        .trend.up{color:var(--success);}
        .trend.down{color:var(--danger);}
        .trend.neutral{color:var(--text-muted);}
        
        /* Status-specific card colors */
        .workflow-card.draft{border-left:4px solid var(--text-muted);}
        .workflow-card.review{border-left:4px solid var(--warning);}
        .workflow-card.approved{border-left:4px solid var(--success);}
        .workflow-card.printed{border-left:4px solid var(--info);}
        .workflow-card.released{border-left:4px solid var(--purple);}
        
        /* ---------- Quick Actions ---------- */
        .quick-actions{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:16px;
            margin-bottom:24px;
        }
        .quick-action-card{
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:var(--radius);
            padding:20px;
            display:flex;
            align-items:center;
            gap:16px;
            text-decoration:none;
            color:inherit;
            transition:all 0.2s ease;
            box-shadow:var(--shadow);
        }
        .quick-action-card:hover{
            transform:translateY(-2px);
            box-shadow:0 4px 12px rgba(0,0,0,0.1);
            border-color:var(--info);
        }
        .quick-action-icon{
            width:48px;
            height:48px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }
        .quick-action-card:hover .quick-action-icon{
            background:var(--info) !important;
            color:#fff !important;
        }
        .quick-action-content{
            flex:1;
        }
        .quick-action-title{
            font-weight:600;
            font-size:14px;
            margin-bottom:2px;
        }
        .quick-action-desc{
            font-size:12px;
            color:var(--text-secondary);
        }
        
        /* ---------- Activity Feed ---------- */
        .activity-item{
            display:flex;
            gap:12px;
            padding:12px 0;
            border-bottom:1px solid var(--border);
        }
        .activity-item:last-child{border-bottom:none;}
        .activity-icon{
            width:36px;
            height:36px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }
        .activity-content{
            flex:1;
        }
        .activity-text{
            font-size:13px;
            color:var(--text-primary);
            margin-bottom:2px;
        }
        .activity-time{
            font-size:11px;
            color:var(--text-muted);
        }
        
        /* ---------- Chart Containers ---------- */
        .chart-container{
            position:relative;
            height:200px;
        }
        .chart-container.large{
            height:250px;
        }

        /* ---------- Progress Stepper ---------- */
        .stepper{
            display:flex;
            align-items:center;
            gap:8px;
            margin-bottom:24px;
        }
        .step{
            display:flex;
            align-items:center;
            gap:8px;
            padding:8px 16px;
            border-radius:20px;
            font-size:13px;
            font-weight:500;
        }
        .step.active{
            background:var(--primary);
            color:#fff;
        }
        .step.inactive{
            background:var(--surface);
            color:var(--text-muted);
            border:1px solid var(--border);
        }
        .step.completed{
            background:var(--success-bg);
            color:var(--success);
        }
        .step-number{
            width:24px;
            height:24px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:12px;
            font-weight:600;
        }
        .step.active .step-number{
            background:rgba(255,255,255,0.2);
        }
        .step.inactive .step-number{
            background:var(--border);
            color:var(--text-muted);
        }
        .step.completed .step-number{
            background:var(--success);
            color:#fff;
        }
        .step-connector{
            flex:1;
            height:2px;
            background:var(--border);
            min-width:40px;
        }
        .step-connector.completed{
            background:var(--success);
        }

        /* ---------- Two Column Layout ---------- */
        .two-column{
            display:grid;
            grid-template-columns:2fr 1fr;
            gap:24px;
        }
        @media (max-width:1024px){.two-column{grid-template-columns:1fr;}}
        
        /* ---------- Search Box ---------- */
        .search-box-large{
            position:relative;
            margin-bottom:16px;
        }
        .search-box-large input{
            width:100%;
            padding:16px 48px 16px 16px;
            font-size:15px;
            color:var(--text-primary);
            border:2px solid var(--border);
            border-radius:12px;
            transition:all 0.2s ease;
        }
        .search-box-large input:focus{
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(35,44,132,0.1);
        }
        .search-box-large .search-icon{
            position:absolute;
            right:16px;
            top:50%;
            transform:translateY(-50%);
            color:var(--text-muted);
        }
        
        /* ---------- Search Results ---------- */
        .search-results{
            margin-top:16px;
            border:1px solid var(--border);
            border-radius:12px;
            overflow:hidden;
        }
        .search-result-item{
            padding:16px;
            border-bottom:1px solid var(--border);
            cursor:pointer;
            transition:background 0.2s ease;
        }
        .search-result-item:hover{
            background:var(--background);
        }
        .search-result-item:last-child{
            border-bottom:none;
        }
        .search-result-name{
            font-weight:600;
            font-size:14px;
            color:var(--text-primary);
        }
        .search-result-details{
            font-size:12px;
            color:var(--text-muted);
            margin-top:4px;
        }
        
        /* ---------- Eligibility Status Card ---------- */
        .eligibility-card{
            padding:20px;
            border-radius:12px;
            border:2px solid;
            margin-bottom:16px;
        }
        .eligibility-card.eligible{
            background:var(--success-bg);
            border-color:var(--success);
        }
        .eligibility-card.not-eligible{
            background:var(--danger-bg);
            border-color:var(--danger);
        }
        .eligibility-card .status-icon{
            font-size:24px;
            margin-bottom:8px;
        }
        .eligibility-card .status-title{
            font-weight:600;
            font-size:16px;
            margin-bottom:4px;
        }
        .eligibility-card .status-desc{
            font-size:13px;
            color:var(--text-secondary);
        }
        
        /* ---------- Client Summary Card ---------- */
        .client-summary{
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:12px;
            padding:20px;
            margin-bottom:20px;
        }
        .client-summary-header{
            display:flex;
            align-items:center;
            gap:12px;
            margin-bottom:16px;
            padding-bottom:16px;
            border-bottom:1px solid var(--border);
        }
        .client-avatar{
            width:48px;
            height:48px;
            border-radius:50%;
            background:var(--primary);
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:600;
            font-size:18px;
        }
        .client-name{
            font-weight:600;
            font-size:16px;
        }
        .client-info-grid{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:12px;
        }
        .client-info-item{
            display:flex;
            flex-direction:column;
        }
        .client-info-label{
            font-size:12px;
            color:var(--text-secondary);
            text-transform:uppercase;
            letter-spacing:0.5px;
            font-weight:500;
        }
        .client-info-value{
            font-size:15px;
            font-weight:600;
            color:var(--text-primary);
        }
        
        /* ---------- Info Panel ---------- */
        .info-panel{
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:12px;
            padding:20px;
        }
        .info-panel h4{
            font-size:15px;
            font-weight:700;
            margin-bottom:16px;
            color:var(--text-primary);
            display:flex;
            align-items:center;
            gap:8px;
        }
        .info-panel ul{
            list-style:none;
            padding:0;
            margin:0;
        }
        .info-panel li{
            display:flex;
            gap:8px;
            font-size:14px;
            color:var(--text-primary);
            margin-bottom:12px;
            line-height:1.5;
        }
        .info-panel li:last-child{
            margin-bottom:0;
        }
        .info-panel li i{
            color:var(--success);
            flex-shrink:0;
        }
        
        /* ---------- Workflow Steps ---------- */
        .workflow-steps{
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .workflow-step{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:13px;
            color:var(--text-primary);
            font-weight:500;
        }
        .workflow-step-number{
            width:24px;
            height:24px;
            border-radius:50%;
            background:var(--border);
            color:var(--text-muted);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:11px;
            font-weight:600;
        }
        .workflow-step.active .workflow-step-number{
            background:var(--primary);
            color:#fff;
        }
        .workflow-step.completed .workflow-step-number{
            background:var(--success);
            color:#fff;
        }
        .workflow-step.completed{
            color:var(--success);
        }
        .workflow-step-arrow{
            color:var(--text-muted);
            font-size:10px;
        }

        /* ---------- Summary Stats Cards ---------- */
        .summary-cards{
            display:grid;
            grid-template-columns:repeat(5,1fr);
            gap:16px;
            margin-bottom:24px;
        }
        @media (max-width:768px){.summary-cards{grid-template-columns:repeat(2,1fr);}}
        .summary-card{
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:12px;
            padding:16px;
            text-align:center;
        }
        .summary-card .num{
            font-size:28px;
            font-weight:700;
            color:var(--primary);
            line-height:1.1;
        }
        .summary-card .label{
            font-size:12px;
            color:var(--text-muted);
            margin-top:4px;
            font-weight:500;
        }
        
        /* ---------- Filter Bar ---------- */
        .filter-bar{
            display:flex;
            flex-wrap:wrap;
            gap:12px;
            align-items:center;
            margin-bottom:20px;
            padding:16px;
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:12px;
        }
        .filter-bar .search-box{
            flex:1;
            min-width:250px;
            position:relative;
        }
        .filter-bar .search-box input{
            width:100%;
            padding:12px 40px 12px 16px;
            border:1px solid var(--border);
            border-radius:8px;
            font-size:14px;
        }
        .filter-bar .search-box .search-icon{
            position:absolute;
            right:12px;
            top:50%;
            transform:translateY(-50%);
            color:var(--text-muted);
        }
        .filter-bar select{
            padding:12px 36px 12px 12px;
            border:1px solid var(--border);
            border-radius:8px;
            font-size:14px;
            background:var(--surface);
            cursor:pointer;
            min-width:140px;
        }
        .filter-bar .btn-group{
            display:flex;
            gap:8px;
        }
        
        /* ---------- Action Buttons ---------- */
        .action-btn{
            display:inline-flex;
            align-items:center;
            gap:4px;
            padding:6px 12px;
            border-radius:6px;
            font-size:12px;
            font-weight:500;
            cursor:pointer;
            transition:all 0.2s ease;
            border:1.5px solid #374151;
            background:var(--surface);
            color:var(--text-primary);
        }
        .action-btn:hover{
            background:var(--background);
            border-color:var(--text-muted);
        }
        .action-btn.primary{
            background:var(--primary);
            color:#fff;
            border-color:var(--primary);
        }
        .action-btn.primary:hover{
            background:var(--primary-hover);
        }
        .action-btn.danger{
            color:var(--danger);
            border-color:#DC2626;
        }
        .action-btn.danger:hover{
            background:var(--danger-bg);
            border-color:#B91C1C;
        }
        
        /* ---------- Data Table ---------- */
        .data-table{
            width:100%;
            border-collapse:collapse;
        }
        .data-table th{
            background:var(--background);
            padding:12px 16px;
            text-align:left;
            font-size:12px;
            font-weight:600;
            color:var(--text-secondary);
            text-transform:uppercase;
            letter-spacing:0.5px;
            border-bottom:2px solid var(--border);
        }
        .data-table th.sortable{
            cursor:pointer;
            user-select:none;
        }
        .data-table th.sortable:hover{
            color:var(--primary);
        }
        .data-table td{
            padding:14px 16px;
            border-bottom:1px solid var(--border);
            font-size:14px;
        }
        .data-table tr:hover td{
            background:var(--background);
        }
        .data-table .control-no{
            font-family:monospace;
            font-weight:600;
            color:var(--primary);
        }
        .data-table .actions{
            display:flex;
            gap:4px;
        }
        
        /* ---------- Pagination ---------- */
        .pagination{
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:16px 0;
            margin-top:16px;
        }
        .pagination-info{
            font-size:13px;
            color:var(--text-muted);
        }
        .pagination-controls{
            display:flex;
            gap:8px;
        }
        .pagination-btn{
            padding:8px 16px;
            border:1px solid var(--border);
            border-radius:6px;
            background:var(--surface);
            cursor:pointer;
            font-size:13px;
            transition:all 0.2s ease;
        }
        .pagination-btn:hover:not(:disabled){
            background:var(--background);
        }
        .pagination-btn.active{
            background:var(--primary);
            color:#fff;
            border-color:var(--primary);
        }
        .pagination-btn:disabled{
            opacity:0.5;
            cursor:not-allowed;
        }
        
        /* ---------- Empty State ---------- */
        .empty-state{
            text-align:center;
            padding:60px 20px;
        }
        .empty-state .icon{
            font-size:64px;
            color:var(--text-muted);
            margin-bottom:16px;
        }
        .empty-state h3{
            font-size:18px;
            font-weight:600;
            color:var(--text-primary);
            margin-bottom:8px;
        }
        .empty-state p{
            font-size:14px;
            color:var(--text-muted);
            margin-bottom:24px;
            max-width:400px;
            margin-left:auto;
            margin-right:auto;
        }

        /* ---------- Main ---------- */
        .main{
            flex:1;
            min-width:0;
            margin-left:var(--sidebar-width);
            padding:var(--content-padding);
            max-width:calc(100% - var(--sidebar-width));
        }
        
        /* ---------- Modern Page Header (Styling migrated to Tailwind classes in dashboard.blade.php) ---------- */

        /* ---------- Buttons ---------- */
        .btn{
            border:1px solid var(--border);
            background:var(--surface);
            color:var(--text-primary);
            padding:10px 20px;
            border-radius:10px;
            font-size:14px;
            font-weight:500;
            display:inline-flex;
            align-items:center;
            gap:8px;
            box-shadow:var(--shadow);
            transition:all 0.2s ease;
            height:42px;
            cursor:pointer;
        }
        .btn:hover{
            border-color:var(--primary);
            transform:translateY(-1px);
        }
        .btn.primary{
            background:var(--primary);
            color:#FFFFFF;
            border-color:var(--primary);
        }
        .btn.primary:hover{
            background:var(--primary-hover);
            border-color:var(--primary-hover);
        }
        .btn.danger{
            color:var(--danger);
            border-color:var(--danger-bg);
            background:var(--danger-bg);
        }
        .btn.danger:hover{
            background:var(--danger);
            color:#FFFFFF;
        }
        .btn.success{
            color:var(--success);
            border-color:var(--success-bg);
            background:var(--success-bg);
        }
        .btn.success:hover{
            background:var(--success);
            color:#FFFFFF;
        }
        .btn.ghost{
            background:transparent;
            box-shadow:none;
            border-color:transparent;
            color:var(--text-secondary);
        }
        .btn.ghost:hover{
            background:var(--background);
            color:var(--text-primary);
        }
        .btn:disabled{
            opacity:0.45;
            cursor:not-allowed;
            pointer-events:none;
        }
        .btn-sm{
            padding:6px 12px;
            font-size:13px;
            height:36px;
        }

        /* ---------- Cards / grid ---------- */
        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
            gap:var(--card-gap);
            margin-bottom:24px;
        }
        .stat-card{
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:var(--radius);
            padding:var(--card-padding);
            box-shadow:var(--shadow);
            transition:all 0.2s ease;
        }
        .stat-card:hover{
            transform:translateY(-2px);
            box-shadow:0 4px 20px rgba(0,0,0,0.08);
        }
        .stat-card .num{
            font-size:32px;
            font-weight:700;
            line-height:1.1;
            color:var(--text-primary);
        }
        .stat-card .label{
            color:var(--text-secondary);
            font-size:13px;
            margin-top:8px;
            font-weight:500;
        }
        .stat-card .icon{
            width:48px;
            height:48px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:20px;
            margin-bottom:12px;
        }
        .panel{
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            padding:var(--card-padding);
            margin-bottom:20px;
        }
        .panel h3{
            font-size:15px;
            font-weight:700;
            margin-bottom:16px;
            color:var(--text-primary);
        }
        
        /* ---------- Analytics Card ---------- */
        .analytics-card{
            background:var(--surface);
            border-radius:16px;
            padding:24px;
            box-shadow:var(--shadow);
            border:1px solid var(--border);
            height:100%;
            animation:fadeInUp 0.6s ease-out 0.1s backwards;
        }
        .analytics-card h3{
            font-size:14px;
            font-weight:600;
            color:var(--text-primary);
            margin-bottom:24px;
        }
        .chart-wrapper{
            display:flex;
            align-items:center;
            gap:32px;
        }
        .chart-canvas{
            flex:1;
            position:relative;
            height:280px;
        }
        .chart-legend{
            width:200px;
            display:flex;
            flex-direction:column;
            gap:12px;
        }
        .legend-item{
            display:flex;
            align-items:center;
            gap:10px;
            padding:8px 12px;
            border-radius:8px;
            transition:background 0.2s ease;
        }
        .legend-item:hover{
            background:var(--background);
        }
        .legend-color{
            width:12px;
            height:12px;
            border-radius:3px;
            flex-shrink:0;
        }
        .legend-info{
            flex:1;
        }
        .legend-name{
            font-size:13px;
            font-weight:500;
            color:var(--text-primary);
        }
        .legend-count{
            font-size:12px;
            color:var(--text-secondary);
        }
        .legend-percent{
            font-size:12px;
            font-weight:600;
            color:var(--text-primary);
        }
        
        /* ---------- Activity Card ---------- */
        .activity-card{
            background:var(--surface);
            border-radius:16px;
            padding:24px;
            box-shadow:var(--shadow);
            border:1px solid var(--border);
            height:100%;
            animation:fadeInUp 0.6s ease-out 0.2s backwards;
        }
        .activity-card h3{
            font-size:14px;
            font-weight:600;
            color:var(--text-primary);
            margin-bottom:20px;
        }
        .activity-feed{
            max-height:400px;
            overflow-y:auto;
            padding-right:8px;
        }
        .activity-feed::-webkit-scrollbar{
            width:6px;
        }
        .activity-feed::-webkit-scrollbar-track{
            background:var(--background);
            border-radius:3px;
        }
        .activity-feed::-webkit-scrollbar-thumb{
            background:var(--border);
            border-radius:3px;
        }
        .activity-feed::-webkit-scrollbar-thumb:hover{
            background:var(--text-muted);
        }
        
        /* ---------- Modern Activity Item ---------- */
        .activity-item{
            display:flex;
            gap:14px;
            padding:16px;
            border-radius:12px;
            background:var(--background);
            margin-bottom:12px;
            transition:all 0.2s ease;
        }
        .activity-item:last-child{
            margin-bottom:0;
        }
        .activity-item:hover{
            transform:translateX(4px);
            background:var(--surface);
            box-shadow:0 2px 8px rgba(0,0,0,0.04);
        }
        .activity-icon{
            width:40px;
            height:40px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }
        .activity-icon svg{
            width:20px;
            height:20px;
        }
        .activity-content{
            flex:1;
            min-width:0;
        }
        .activity-text{
            font-size:14px;
            font-weight:500;
            color:var(--text-primary);
            margin-bottom:4px;
            line-height:1.4;
        }
        .activity-time{
            font-size:12px;
            color:var(--text-muted);
        }
        
        /* ---------- Animations ---------- */
        @keyframes fadeInUp{
            from{
                opacity:0;
                transform:translateY(20px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }
        
        /* ---------- Responsive Design ---------- */
        @media (max-width:1200px){
            .dashboard-grid{
                grid-template-columns:1fr;
            }
            .chart-wrapper{
                flex-direction:column;
                align-items:center;
            }
            .chart-legend{
                width:100%;
                flex-direction:row;
                flex-wrap:wrap;
                justify-content:center;
            }
            .legend-item{
                flex:1 1 auto;
                min-width:150px;
            }
        }
        
        @media (max-width:768px){
            :root{
                --content-padding:20px;
                --card-padding:20px;
            }
            .main{
                margin-left:0;
                max-width:100%;
            }
            .sidebar{
                transform:translateX(-100%);
            }
            .sidebar.open{
                transform:translateX(0);
            }
            .stat-cards{
                grid-template-columns:1fr;
                gap:16px;
            }
            .stat-card-value{
                font-size:36px;
            }
            .page-head{
                flex-direction:column;
                align-items:stretch;
            }
            .page-head-actions{
                flex-direction:column;
                width:100%;
            }
            .search-box input{
                width:100%;
            }
            .btn{
                width:100%;
                justify-content:center;
            }
            .chart-canvas{
                height:220px;
            }
            .activity-feed{
                max-height:300px;
            }
        }
        
        @media (max-width:480px){
            .page-head h1{
                font-size:24px;
            }
            .stat-card{
                padding:20px;
            }
            .stat-card-value{
                font-size:32px;
            }
            .analytics-card,
            .activity-card{
                padding:20px;
            }
            .chart-wrapper{
                gap:20px;
            }
        }

        /* ---------- Table ---------- */
        table{
            width:100%;
            border-collapse:collapse;
            font-size:14px;
            border-radius:var(--radius);
            overflow:hidden;
        }
        th{
            text-align:left;
            color:var(--text-secondary);
            font-weight:600;
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:0.03em;
            padding:14px 16px;
            background:var(--background);
            border-bottom:1px solid var(--border);
        }
        td{
            padding:14px 16px;
            border-bottom:1px solid var(--border);
            vertical-align:middle;
            color:var(--text-primary);
        }
        tr.row-click{
            cursor:pointer;
            transition:background 0.15s ease;
        }
        tr.row-click:hover td{
            background:var(--background);
        }
        .empty{
            padding:60px 20px;
            text-align:center;
            color:var(--text-muted);
        }
        .empty i{
            font-size:48px;
            display:block;
            margin-bottom:12px;
            opacity:0.4;
        }

        /* ---------- Badges ---------- */
        .badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:6px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:500;
            white-space:nowrap;
        }
        .b-draft{background:var(--background);color:var(--text-secondary);}
        .b-review{background:var(--warning-bg);color:var(--warning);}
        .b-approved{background:var(--success-bg);color:var(--success);}
        .b-printed{background:var(--info-bg);color:var(--info);}
        .b-released{background:var(--success-bg);color:var(--success);}
        .b-archived{background:var(--danger-bg);color:var(--danger);}
        .b-blocked{background:var(--danger-bg);color:var(--danger);}

        /* ---------- Forms ---------- */
        .field{margin-bottom:16px;}
        .field label{
            display:block;
            font-size:13px;
            font-weight:600;
            color:var(--text-primary);
            margin-bottom:8px;
        }
        .field .hint{
            font-size:13px;
            color:var(--text-secondary);
            margin-top:6px;
            line-height:1.4;
        }
        input[type=text],input[type=date],input[type=number],input[type=tel],select,textarea{
            width:100%;
            padding:12px 16px;
            border:1px solid var(--border);
            border-radius:10px;
            background:var(--surface);
            color:var(--text-primary);
            height:44px;
            transition:all 0.2s ease;
        }
        input:focus,select:focus,textarea:focus{
            outline:none;
            border-color:var(--primary);
            box-shadow:0 0 0 3px rgba(35,44,132,0.1);
            background:var(--surface);
        }
        textarea{
            resize:vertical;
            min-height:80px;
            height:auto;
        }
        .grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
        @media (max-width:768px){.grid2,.grid3{grid-template-columns:1fr;}}
        .checkbox-row{
            display:flex;
            align-items:center;
            gap:12px;
            padding:12px 0;
            border-bottom:1px solid var(--border);
        }
        .checkbox-row input{
            width:auto;
            height:18px;
            width:18px;
            accent-color:var(--primary);
        }
        .checkbox-row span{
            flex:1;
            font-size:14px;
        }
        .pill-check{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 16px;
            border:1px solid var(--border);
            border-radius:10px;
            font-size:14px;
            cursor:pointer;
            background:var(--surface);
            transition:all 0.2s ease;
        }
        .pill-check.on{
            background:var(--success-bg);
            border-color:var(--success);
            color:var(--success);
        }
        .pill-check input{display:none;}

        /* ---------- Stepper ---------- */
        .stepper{display:flex;align-items:center;margin-bottom:16px;}
        .step{display:flex;flex-direction:column;align-items:center;flex:1;position:relative;}
        .step .dot{
            width:32px;
            height:32px;
            border-radius:50%;
            background:var(--background);
            color:var(--text-muted);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:13px;
            font-weight:600;
            border:2px solid var(--border);
            z-index:1;
            transition:all 0.2s ease;
        }
        .step.done .dot{background:var(--success);color:#fff;border-color:var(--success);}
        .step.current .dot{background:var(--primary);color:#fff;border-color:var(--primary);}
        .step .lbl{font-size:12px;color:var(--text-muted);margin-top:8px;text-align:center;font-weight:500;}
        .step.done .lbl,.step.current .lbl{color:var(--text-primary);font-weight:600;}
        .step-line{position:absolute;top:16px;left:-50%;width:100%;height:2px;background:var(--border);z-index:0;}
        .step.done .step-line{background:var(--success);}
        .step:first-child .step-line{display:none;}

        /* ---------- Eligibility timeline ---------- */
        .elig-wrap{background:var(--background);border-radius:10px;padding:16px 20px;}
        .elig-top{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:12px;}
        .elig-top .status-text{font-size:14px;font-weight:600;color:var(--text-primary);}
        .elig-track{position:relative;height:10px;background:var(--border);border-radius:100px;overflow:hidden;}
        .elig-fill{position:absolute;top:0;left:0;height:100%;border-radius:100px;transition:width 0.3s ease;}
        .elig-marks{display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-top:8px;}

        /* ---------- Detail layout ---------- */
        .detail-grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;}
        @media (max-width:900px){.detail-grid{grid-template-columns:1fr;}}
        .kv{display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);font-size:14px;}
        .kv span:first-child{color:var(--text-secondary);font-weight:500;}
        .kv span:last-child{font-weight:600;text-align:right;color:var(--text-primary);}
        .agency-tag{display:inline-block;background:var(--info-bg);color:var(--info);font-size:12px;padding:6px 12px;border-radius:8px;margin:0 6px 6px 0;font-weight:500;}
        .req-check{display:flex;align-items:center;gap:10px;padding:8px 0;}
        .req-check.missing{color:var(--danger);}

        /* ---------- Modal ---------- */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;padding:24px;z-index:1000;}
        .modal{background:var(--surface);border-radius:16px;max-width:600px;width:100%;padding:32px;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:90vh;overflow-y:auto;}
        .modal-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
        .modal-close{background:none;border:none;font-size:24px;color:var(--text-muted);padding:4px 8px;cursor:pointer;transition:color 0.2s ease;}
        .modal-close:hover{color:var(--text-primary);}

        /* ---------- Document / print view ---------- */
        .doc-page{background:#fff;border:1px solid var(--border);border-radius:12px;padding:48px;padding:48px 60px;max-width:800px;margin:0 auto;font-family:Georgia,'Times New Roman',serif;color:#1a1a1a;line-height:1.6;}
        .doc-letterhead{text-align:center;border-bottom:2px solid #1a1a1a;padding-bottom:16px;margin-bottom:24px;}
        .doc-letterhead .office{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#555;}
        .doc-letterhead h2{font-family:Georgia,serif;font-size:18px;margin-top:8px;}
        .doc-letterhead .addr{font-size:11px;color:#666;margin-top:4px;}
        .doc-title{text-align:center;font-size:16px;letter-spacing:.06em;text-transform:uppercase;margin-bottom:8px;}
        .doc-sub{text-align:center;font-size:13px;color:#555;margin-bottom:24px;}
        .doc-section{margin-bottom:20px;}
        .doc-section h4{font-size:13px;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #ccc;padding-bottom:8px;margin-bottom:12px;}
        .doc-row{display:flex;font-size:14px;margin-bottom:6px;}
        .doc-row .l{width:200px;color:#555;flex-shrink:0;font-weight:500;}
        .doc-body-text{font-size:14px;white-space:pre-wrap;line-height:1.7;}
        .doc-sign{margin-top:48px;display:flex;justify-content:space-between;font-size:13px;}
        .doc-sign .line{border-top:1px solid #333;padding-top:8px;width:240px;text-align:center;}
        .doc-toolbar{max-width:800px;margin:0 auto 20px;display:flex;justify-content:space-between;align-items:center;padding:20px;background:var(--surface);border-radius:12px;border:1px solid var(--border);}
        @media print{
            .no-print{display:none !important;}
            .sidebar,.page-head,.toolbar-row{display:none !important;}
            .main{padding:0;max-width:none;margin:0;}
            .doc-page{border:none;padding:0;max-width:none;page-break-after:always;break-after:page;}
            .doc-page:last-child{page-break-after:avoid;break-after:avoid;}
            body{background:#fff;}
        }

        .toolbar-row{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;}
        .search-box{position:relative;flex:1;min-width:200px;}
        .search-box input{padding-left:44px;}
        .search-box i{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:18px;}
        .tabs{display:flex;gap:4px;background:var(--background);padding:4px;border-radius:12px;width:fit-content;margin-bottom:20px;}
        .tab-btn{border:none;background:transparent;padding:10px 20px;border-radius:8px;font-size:14px;color:var(--text-secondary);font-weight:500;cursor:pointer;transition:all 0.2s ease;}
        .tab-btn.active{background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);}
        .banner{display:flex;gap:12px;align-items:flex-start;padding:16px 20px;border-radius:12px;font-size:14px;margin-bottom:20px;}
        .banner.warn{background:var(--warning-bg);color:var(--warning);}
        .banner.block{background:var(--danger-bg);color:var(--danger);}
        .banner.ok{background:var(--success-bg);color:var(--success);}
        .banner i{margin-top:2px;flex-shrink:0;}
        .muted{color:var(--text-muted);}
        .sr-only{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;}

        /* ---------- Responsive ---------- */
        @media (max-width:768px){
            .sidebar{
                transform:translateX(-100%);
                transition:transform 0.3s ease;
            }
            .sidebar.show{
                transform:translateX(0);
            }
            .main{
                margin-left:0;
                max-width:100%;
            }
            .page-head h1{
                font-size:24px;
            }
            .cards{
                grid-template-columns:1fr;
            }
            .detail-grid{
                grid-template-columns:1fr;
            }
        }
        /* ---------- Redesigned Social Case Masterlist CSS (Matches Senior Citizen design) ---------- */
        .sc-table-card {
            background: var(--surface);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex: 1;
            min-height: 0;
        }
        .sc-table-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 0;
            margin-bottom: 1.5rem;
        }
        .sc-filter-section {
            margin-bottom: 1.5rem;
        }
        .sc-filter-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .sc-filter-left {
            display: flex;
            gap: 12px;
            flex: 1;
            min-width: 0;
            flex-wrap: wrap;
        }
        .sc-search-group {
            flex: 1;
            min-width: 250px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .sc-filter-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .sc-input-group {
            display: flex;
            align-items: center;
            height: 44px;
        }
        input.sc-search-input {
            flex: 1;
            height: 44px;
            border: 1px solid var(--border);
            border-right: none;
            border-radius: 6px 0 0 6px;
            padding: 0 1rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            background: var(--surface);
            box-sizing: border-box;
            transition: all 0.2s ease;
        }
        input.sc-search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30,58,138,0.15);
        }
        .sc-search-btn {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            padding: 0 1.25rem;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .sc-search-btn:hover {
            background-color: var(--primary-hover);
        }
        .sc-select-group {
            min-width: 180px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        select.sc-filter-select {
            height: 44px;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 0 2.25rem 0 1rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            background: var(--surface);
            cursor: pointer;
            box-sizing: border-box;
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            transition: all 0.2s ease;
        }
        select.sc-filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30,58,138,0.15);
        }
        .sc-filter-right {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }
        .sc-action-btn {
            height: 44px;
            border-radius: 8px;
            padding: 0 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            box-sizing: border-box;
            transition: all 0.2s;
        }
        .sc-action-primary {
            background-color: var(--primary);
            color: #ffffff;
        }
        .sc-action-primary:hover {
            background-color: var(--primary-hover);
        }
        .sc-action-muted {
            background-color: var(--text-secondary);
            color: #ffffff;
        }
        .sc-action-muted:hover {
            background-color: var(--text-primary);
        }
        .sc-stats-row {
            display: flex;
            gap: 12px;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .sc-stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: #F3F4F6;
            border-radius: 9999px;
            font-size: 0.825rem;
        }
        .sc-stat-label {
            color: var(--text-secondary);
            font-weight: 500;
        }
        .sc-stat-value {
            font-weight: 700;
            color: var(--text-primary);
        }
        .sc-stat-approved {
            background-color: var(--success-bg);
        }
        .sc-stat-approved .sc-stat-label {
            color: var(--success);
        }
        .sc-stat-approved .sc-stat-value {
            color: var(--success);
        }
        .sc-stat-released {
            background-color: var(--purple-bg);
        }
        .sc-stat-released .sc-stat-label {
            color: var(--purple);
        }
        .sc-stat-released .sc-stat-value {
            color: var(--purple);
        }
        .sc-table-responsive {
            flex: 1;
            overflow-x: auto;
            overflow-y: auto;
            min-height: 0;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .sc-data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .sc-data-table th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #F8FAFC;
            border-bottom: 2px solid var(--border);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            text-align: left;
        }
        .sc-data-table td {
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            vertical-align: middle;
            word-wrap: break-word;
        }
        .sc-data-table tr:hover td {
            background-color: var(--background);
        }
        .sc-empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--text-secondary);
        }
        .sc-empty-state h3 {
            margin: 8px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .sc-empty-state p {
            margin: 0 0 16px 0;
            font-size: 0.875rem;
        }
        .sc-pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }
        .sc-pagination-info {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        .sc-pagination-controls {
            display: flex;
            gap: 4px;
        }
        .sc-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            min-width: 38px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--surface);
            color: var(--text-primary);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .sc-page-btn:hover {
            background-color: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }
        .sc-page-btn.active {
            background-color: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }
        .sc-page-btn:disabled {
            color: var(--text-muted);
            background-color: var(--background);
            border-color: var(--border);
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<h1 class="sr-only">Social case study management system for tracking client eligibility, intake, workflow, and generating agency-specific reports</h1>
<!-- Hidden form for secure POST logout -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>
<div id="app" class="app">
    @yield('content')
</div>
@stack('scripts')
<script>
    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will be logged out of the system.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('currentDateTime');
        if (el) {
            function updateDateTime() {
                const now = new Date();
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                };
                const dateTimeStr = now.toLocaleDateString('en-US', options).replace(',', ' at');
                const element = document.getElementById('currentDateTime');
                if (element) {
                    element.textContent = dateTimeStr;
                }
            }
            updateDateTime();
            setInterval(updateDateTime, 60000);
        }
    });
</script>
</body>
</html>
