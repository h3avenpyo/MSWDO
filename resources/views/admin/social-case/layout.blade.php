<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Social Case Study System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root{
            /* Primary Colors */
            --primary: #232C84;
            --primary-hover: #1A237E;
            --secondary-blue: #303F9F;
            --accent-yellow: #FFC107;
            
            /* Background & Surface */
            --background: #F4F6FA;
            --surface: #FFFFFF;
            --border: #E5E7EB;
            
            /* Text Colors */
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            
            /* Status Colors */
            --success: #10B981;
            --success-bg: #ECFDF5;
            --warning: #F59E0B;
            --warning-bg: #FFF7ED;
            --danger: #EF4444;
            --danger-bg: #FEF2F2;
            --info: #3B82F6;
            --info-bg: #EEF2FF;
            --purple: #8B5CF6;
            --purple-bg: #F3E8FF;
            
            /* Dimensions */
            --sidebar-width: 260px;
            --topnav-height: 70px;
            --content-padding: 24px;
            --card-gap: 20px;
            --card-padding: 20px;
            --radius: 14px;
            --shadow: 0 2px 10px rgba(0,0,0,0.05);
            
            /* Typography */
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }
        
        *{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);}
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
            padding:1.5rem;
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
            grid-template-columns:2fr 1fr;
            gap:20px;
        }
        @media (max-width:1024px){.dashboard-grid{grid-template-columns:1fr;}}
        
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
            grid-template-columns:repeat(auto-fit,minmax(140px,1fr));
            gap:12px;
            margin-bottom:24px;
        }
        .quick-action{
            background:var(--surface);
            border:1px solid var(--border);
            border-radius:10px;
            padding:16px;
            text-align:center;
            cursor:pointer;
            transition:all 0.2s ease;
            text-decoration:none;
            color:var(--text-primary);
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:8px;
        }
        .quick-action:hover{
            border-color:var(--primary);
            background:var(--primary);
            color:#fff;
        }
        .quick-action i{
            font-size:24px;
            color:var(--primary);
        }
        .quick-action:hover i{color:#fff;}
        .quick-action span{
            font-size:13px;
            font-weight:500;
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
            font-size:11px;
            color:var(--text-muted);
            text-transform:uppercase;
            letter-spacing:0.5px;
        }
        .client-info-value{
            font-size:14px;
            font-weight:500;
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
            font-size:14px;
            font-weight:600;
            margin-bottom:12px;
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
            font-size:13px;
            color:var(--text-secondary);
            margin-bottom:8px;
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
            font-size:12px;
            color:var(--text-secondary);
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
            border:1px solid var(--border);
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
            border-color:var(--danger-bg);
        }
        .action-btn.danger:hover{
            background:var(--danger-bg);
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
        .page-head{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:24px;
            gap:16px;
            flex-wrap:wrap;
        }
        .page-head h1{
            font-size:32px;
            font-weight:700;
            color:var(--text-primary);
        }
        .page-head p{
            margin:4px 0 0;
            color:var(--text-secondary);
            font-size:14px;
        }

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
            font-size:18px;
            margin-bottom:16px;
            color:var(--text-primary);
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
            font-size:12px;
            color:var(--text-muted);
            margin-top:6px;
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
</script>
</body>
</html>
