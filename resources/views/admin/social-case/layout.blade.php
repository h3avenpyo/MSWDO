<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="{{ session('admin_user_role') }}">
    <meta name="user-name" content="{{ session('admin_user_name') ?? 'Social Case Study Officer' }}">
    <meta name="admin-name" content="{{ optional(\App\Models\User::where('role', 'admin')->first())->name ?? '' }}">
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
            --primary-dark: #121858;
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
            --icon-teal: #0D9488;
            
            /* Dimensions */
            --sidebar-width: 260px;
            --topnav-height: 70px;
            --content-padding: 20px;
            --card-gap: 16px;
            --card-padding: 20px;
            --radius: 16px;
            --shadow: 0 10px 30px rgba(15,23,42,.08);
            --shadow-hover: 0 20px 40px rgba(15,23,42,.12);
            
            /* Typography */
            --font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
        }
        
        *{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);height:100%;overflow-x:auto;overflow-y:auto;}
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
            z-index:1001;
            display:flex;
            flex-direction:column;
            transition:transform .3s ease;
            transform:translateX(-100%);
        }
        .sidebar.show{transform:translateX(0);}
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
        .sidebar-brand i,.sidebar-brand [data-lucide]{width:24px;height:24px;color:var(--accent-yellow);}
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

        /* ---------- Sidebar Dropdown ---------- */
        .sidebar-dropdown {
            position: relative;
        }
        .sidebar-dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
        }
        .sidebar-dropdown-toggle .dropdown-chevron,
        .sidebar-dropdown-toggle i:last-child {
            transition: transform 0.2s ease;
            margin-left: auto;
        }
        .sidebar-dropdown.open .sidebar-dropdown-toggle .dropdown-chevron,
        .sidebar-dropdown.open .sidebar-dropdown-toggle i:last-child {
            transform: rotate(180deg);
        }
        .sidebar-dropdown-menu {
            display: none;
            background: rgba(0, 0, 0, 0.2);
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .sidebar-dropdown.open .sidebar-dropdown-menu {
            display: block;
        }
        .sidebar-dropdown-menu li {
            margin: 0;
        }
        .sidebar-dropdown-menu a {
            padding: 0.6rem 1.25rem 0.6rem 2.25rem;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.75);
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .sidebar-dropdown-menu a i {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            opacity: 0.85;
        }
        .sidebar-dropdown-menu a:hover {
            color: var(--accent-yellow);
        }
        .sidebar-dropdown-menu a:hover i {
            opacity: 1;
        }
        .sidebar-dropdown-menu a.active {
            color: var(--accent-yellow);
            background: rgba(255, 255, 255, 0.05);
        }
        .sidebar-dropdown-menu a.active i {
            opacity: 1;
            color: var(--accent-yellow);
        }

        /* ---------- Sidebar Badge Styling ---------- */
        .sidebar-badge {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-left: auto;
        }

        .badge-count {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 24px !important;
            height: 24px !important;
            border-radius: 50% !important;
            font-size: 0.7rem !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            color: #fff !important;
        }

        .badge-pending {
            background: #F59E0B !important;
        }

        .badge-accepted {
            background: #10B981 !important;
        }

        .badge-rejected {
            background: #EF4444 !important;
        }

        /* ---------- Dashboard Grid ---------- */
        .dashboard-grid{
            display:grid;
            grid-template-columns: 1fr;
            gap:24px;
            flex:1;
            min-height:0;
        }
        
        /* ---------- Modern Stat Cards ---------- */
        .stat-cards{
            display:grid;
            grid-template-columns:1fr;
            gap:16px;
            margin-bottom:32px;
            animation:fadeInUp 0.6s ease-out;
        }
        
        .stat-card{
            background:var(--surface);
            border-radius:16px;
            padding:16px;
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
        .stat-card-teal::before{background:var(--icon-teal);}
        
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
            font-size:32px;
            font-weight:700;
            color:var(--text-primary);
            line-height:1;
        }
        .stat-card-icon{
            width:52px;
            height:52px;
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
        .stat-card-teal .stat-card-icon{
            background:#CCFBF1;
            color:var(--icon-teal);
        }
        .stat-card-icon svg{
            width:24px;
            height:24px;
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
            grid-template-columns:1fr;
            gap:24px;
        }
        
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
            grid-template-columns:repeat(2,1fr);
            gap:16px;
            margin-bottom:24px;
        }
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
            margin-left:0;
            padding:20px;
            max-width:100%;
            min-height:100vh;
            overflow-y:auto;
            overflow-x:auto;
            display:flex;
            flex-direction:column;
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
        .analytics-section, .activity-section{
            display:flex;
            flex-direction:column;
            min-height:0;
        }
        .analytics-card{
            background:var(--surface);
            border-radius:16px;
            padding:24px;
            box-shadow:var(--shadow);
            border:1px solid var(--border);
            flex:1;
            display:flex;
            flex-direction:column;
            min-height:0;
            overflow:visible;
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
            gap:24px;
            flex:1;
            min-height:0;
            min-width:0;
        }
        .chart-canvas{
            flex:1;
            position:relative;
            min-height:250px;
            min-width:0;
        }
        .chart-legend{
            flex:0 1 220px;
            min-width:0;
            display:flex;
            flex-direction:column;
            gap:8px;
            overflow-y:auto;
            min-height:0;
        }
        .legend-item{
            display:flex;
            align-items:center;
            gap:10px;
            padding:6px 10px;
            border-radius:8px;
            transition:background 0.2s ease;
            min-width:0;
        }
        .legend-item:hover{
            background:var(--background);
        }
        .legend-color{
            width:12px;
            height:12px;
            border-radius:4px;
            flex-shrink:0;
        }
        .legend-info{
            flex:1;
            min-width:0;
        }
        .legend-name{
            font-size:14.5px;
            font-weight:600;
            color:var(--text-primary);
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        .legend-count{
            font-size:13px;
            font-weight:500;
            color:var(--text-primary);
        }
        .legend-percent{
            font-size:14.5px;
            font-weight:700;
            color:var(--text-primary);
            flex-shrink:0;
        }
        
        /* ---------- Activity Card ---------- */
        .activity-card{
            background:var(--surface);
            border-radius:16px;
            padding:24px;
            box-shadow:var(--shadow);
            border:1px solid var(--border);
            min-height:320px;
            display:flex;
            flex-direction:column;
            animation:fadeInUp 0.6s ease-out 0.2s backwards;
        }
        .activity-card h3{
            font-size:14px;
            font-weight:600;
            color:var(--text-primary);
            margin-bottom:20px;
        }
        .activity-feed{
            max-height:340px;
            overflow-y:auto;
            padding-right:8px;
        }
        .activity-feed::-webkit-scrollbar{width:6px;}
        .activity-feed::-webkit-scrollbar-track{background:var(--background);border-radius:3px;}
        .activity-feed::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}
        .activity-feed::-webkit-scrollbar-thumb:hover{background:var(--text-muted);}
        
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
        @keyframes fadeIn{
            from{opacity:0;transform:translateY(10px);}
            to{opacity:1;transform:translateY(0);}
        }

        /* ---------- Welcome Greeting ---------- */
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
        
        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS (Mobile-First)
           xs: 0–767px (default), sm: 768+, md: 992+, lg: 1200+
           ══════════════════════════════════════════════ */

        @media (min-width: 768px) {
            .stat-cards{grid-template-columns:repeat(2,1fr);gap:20px;}
            .summary-cards{grid-template-columns:repeat(3,1fr);}
            .field-row{grid-template-columns:1fr 1fr;gap:12px;}
        }

        @media (min-width: 992px) {
            :root{--card-gap:24px;--card-padding:24px;}
            .summary-cards{grid-template-columns:repeat(5,1fr);}
            .detail-grid{grid-template-columns:2fr 1fr;}
        }
        @media (min-width: 1200px) {
            :root{--content-padding:32px;--card-gap:24px;--card-padding:24px;}
            .sidebar{transform:translateX(0);z-index:1000;}
            .sidebar.show{transform:translateX(0);}
            .main{margin-left:var(--sidebar-width);padding:var(--content-padding) !important;padding-top:14px !important;max-width:calc(100% - var(--sidebar-width));}
            .hamburger-btn{display:none !important;}
            header{display:flex !important;}
            .topnav-datetime{display:inline;}
            .dashboard-grid{grid-template-columns:1.8fr 1fr;}
            .two-column{grid-template-columns:2fr 1fr;}
            .chart-wrapper{flex-direction:row;align-items:center;}
            .chart-legend{flex:0 1 220px;width:auto;flex-direction:column;flex-wrap:nowrap;justify-content:flex-start;gap:8px;}
            .legend-item{flex:none;min-width:0;}
            .chart-canvas{min-height:250px;}
        }

        @media (max-width: 479px) {
            .page-head h1{font-size:24px;}
            .stat-card-value{font-size:32px;}
            .analytics-card,.activity-card{padding:20px;}
            .activity-card{min-height:200px;}
            .chart-wrapper{gap:20px;}
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
        .grid2{display:grid;grid-template-columns:1fr;gap:16px;}
        .grid3{display:grid;grid-template-columns:1fr;gap:16px;}
        .field-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .field-sep{height:1px;background:var(--border);margin:4px 0 16px;}
        input::placeholder,textarea::placeholder{
            opacity:1;
            color:var(--text-secondary);
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        @media (max-width: 767px){input::placeholder,textarea::placeholder{font-size:12px;}}
        @media (min-width: 768px) and (max-width: 991px){input::placeholder,textarea::placeholder{font-size:13px;}}
        @media (min-width: 992px) and (max-width: 1199px){input::placeholder,textarea::placeholder{font-size:13.5px;}}
        @media (min-width: 1200px){input::placeholder,textarea::placeholder{font-size:15px;}}
        .field-control-no input{font-variant-numeric:tabular-nums;letter-spacing:0.02em;}
        @media (max-width: 767px){
            .field-control-no{grid-column:1 / -1;}
        }
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
        .detail-grid{display:grid;grid-template-columns:1fr;gap:20px;align-items:start;}
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
            html,body,.app,.main{overflow:visible !important;height:auto !important;}
            .no-print{display:none !important;}
            .sidebar,.mobile-header,header,.page-head,.toolbar-row,.hamburger-btn,.sidebar-overlay{display:none !important;}
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
            height: 44px !important;
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
            position: relative;
        }
        .sc-select-group [id$="Btn"] {
            height: 44px !important;
            box-sizing: border-box;
        }
        .status-opt:hover,
        .assistance-opt:hover,
        .barangay-opt:hover {
            background: #F3F4F6;
        }
        .status-opt.selected,
        .assistance-opt.selected,
        .barangay-opt.selected {
            background: #EEF2FF;
            color: var(--primary);
            font-weight: 600;
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

        /* ── Sidebar Overlay ── */
        .sidebar-overlay.active { display: block !important; }

        /* ── Mobile Header: hidden by default, shown only on mobile (< 768px) ── */
        .mobile-header { display: none !important; }
        @media (max-width: 767.98px) {
            .mobile-header { display: flex !important; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; background: #1A237E; color: #fff; padding: 0 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); align-items: center; justify-content: space-between; height: 60px; }
        }
        .mobile-header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            min-width: 0;
        }
        .mobile-logo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #FBC02D;
            padding: 3px;
            box-sizing: border-box;
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
            white-space: normal;
            word-break: break-word;
        }
        .mobile-brand-subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin: 2px 0 0 0;
            line-height: 1.2;
            white-space: normal;
            word-break: break-word;
        }
        .main p {
            white-space: normal !important;
            overflow-wrap: break-word !important;
            word-break: break-word !important;
            overflow: visible !important;
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

        /* ── xs: mobile-only defaults (max-width: 767.98px) ── */
        @media (max-width: 767.98px) {
            .filter-bar { flex-direction: column; gap: 12px; }
            .filter-bar > div { width: 100% !important; min-width: 0 !important; }
            .main { padding: 12px !important; padding-top: 72px !important; }
            header { display: none !important; }
            .hamburger-btn { display: none !important; }
            .topnav-datetime { display: none; }
            .stat-cards { grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
            .stat-card-value { font-size: 24px; }
            .page-head { flex-direction: column; align-items: stretch; }
            .page-head-actions { flex-direction: column; width: 100%; }
            .search-box input { width: 100%; }
            .btn { width: 100%; justify-content: center; }
            .panel { padding: 16px; margin-bottom: 16px; }
            .panel h3 { font-size: 14px; margin-bottom: 12px; }
            input[type=text],input[type=date],input[type=number],input[type=tel],select,textarea { font-size: 16px; height: 48px; padding: 12px 14px; }
            textarea { min-height: 120px; }
            .intake-actions { flex-direction: column; }
            .intake-actions .btn { width: 100%; justify-content: center; }
            .intake-scroll { max-height: none; overflow: visible; padding-right: 0; }
            .activity-feed { padding-right: 0; max-height: 50vh; overflow-y: auto; }
            .chart-wrapper { flex-direction: column; align-items: center; gap: 20px; }
            .chart-legend { flex: 1 1 auto; width: 100%; flex-direction: row; flex-wrap: wrap; justify-content: center; gap: 8px; }
            .legend-item { flex: 1 1 auto; min-width: 140px; }
            .chart-canvas { min-height: 200px; width: 100%; }
            .welcome-text { font-size: 18px; font-weight: 600; }
            .welcome-greeting { padding: 12px 0 16px 0; }
            .stat-card { height: auto; min-height: 90px; padding: 14px; border-radius: 16px; flex-direction: row; align-items: center; gap: 12px; position: relative; }
            .stat-card::before { display: none; }
            .stat-card-content { width: 100%; padding-right: 48px; }
            .stat-card-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; color: var(--text-secondary); margin-bottom: 4px; }
            .stat-card-value { font-size: 22px; font-weight: 700; }
            .stat-card-icon { width: 38px; height: 38px; position: absolute; top: 14px; right: 14px; }
            .analytics-card { min-height: 0; }
            .activity-card { min-height: 240px; }

            /* Cases page xs (sc-* classes) */
            .sc-table-card { padding: 1rem; margin-bottom: 1rem; }
            .sc-table-card-title { font-size: 1rem; margin-bottom: 1rem; }
            .sc-filter-row { flex-direction: column; align-items: stretch; gap: 10px; }
            .sc-filter-left { flex-direction: column; gap: 10px; width: 100%; }
            .sc-search-group { min-width: 0; width: 100%; }
            .sc-filter-row-inline { display: flex; gap: 10px; width: 100%; }
            .sc-filter-row-inline .sc-select-group { flex: 1; min-width: 0; }
            .sc-filter-right { width: 100%; }
            .sc-filter-right .sc-action-btn { width: 100%; justify-content: center; }
            .sc-table-responsive { border: 0; overflow-x: auto; border-radius: 0; }
            .sc-pagination { gap: 10px; margin-top: 1.25rem; }
            .sc-page-btn { height: 36px; min-width: 36px; font-size: 0.813rem; padding: 0 0.625rem; }
        }

        /* ── xs: 0-767px Card layout for data table ── */
        @media (max-width: 767px) {
            .sc-data-table,
            .sc-data-table thead,
            .sc-data-table tbody,
            .sc-data-table tbody tr,
            .sc-data-table tbody td {
                display: block;
            }
            .sc-data-table thead {
                display: none;
            }
            .sc-data-table tbody tr {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 8px;
                margin-bottom: 12px;
                padding: 12px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            }
            .sc-data-table tbody tr:hover td {
                background: transparent;
            }
            .sc-data-table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid #F3F4F6;
                font-size: 0.813rem;
                gap: 8px;
                min-height: 32px;
            }
            .sc-data-table tbody td:last-child {
                border-bottom: none;
            }
            .sc-data-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--text-secondary);
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                min-width: 80px;
                flex-shrink: 0;
            }
            .sc-data-table tbody td[data-label="Action"] {
                justify-content: space-between;
                padding-top: 12px;
                border-top: 1px solid var(--border);
                margin-top: 4px;
                border-bottom: none;
                flex-wrap: wrap;
            }
            .sc-data-table tbody td .badge {
                font-size: 0.7rem;
                padding: 4px 10px;
            }
            .sc-data-table tbody td .control-no {
                font-size: 0.78rem;
            }
            .sc-table-responsive {
                overflow-x: visible;
                border: none;
            }
            .sc-pagination {
                flex-direction: column;
                gap: 8px;
            }
            .sc-pagination-controls {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        /* ── sm: 768+ base rules (no topnav, no old 64px offset) ── */
        @media (min-width: 768px) {
            .main { padding: 16px; padding-top: 14px; overflow-y: auto; overflow-x: auto; }
            .filter-bar > div { width: auto !important; min-width: 0 !important; }
            .topnav-datetime { display: inline; }
            .topnav { padding: 12px 16px; }
            .page-head{flex-direction:row;align-items:flex-start;}
            .page-head-actions{flex-direction:row;width:auto;}
            .search-box input{width:auto;}
            .btn{width:auto;justify-content:flex-start;}
            .panel{padding:18px 20px;margin-bottom:18px;}
            .panel h3{font-size:14.5px;margin-bottom:12px;}
            input[type=text],input[type=date],input[type=number],input[type=tel],select,textarea{font-size:14px;height:auto;padding:8px 10px;}
            textarea{min-height:70px;}
            .intake-actions{flex-direction:row;}
            .intake-actions .btn{width:auto;justify-content:flex-start;}
            .intake-scroll{max-height:60vh;overflow-y:auto;padding-right:8px;}
            .activity-feed{padding-right:8px;}
            .chart-wrapper{flex-direction:column;align-items:center;gap:24px;}
            .chart-legend{flex:1 1 auto;width:100%;flex-direction:row;flex-wrap:wrap;justify-content:center;gap:8px;}
            .chart-canvas{min-height:250px;width:100%;}
            .welcome-text{font-size:32px;font-weight:700;}
            .welcome-greeting{padding:24px 0 32px 0;}
            .analytics-card{min-height:0;}
            .activity-card{min-height:400px;}
            .activity-feed{max-height:none;overflow-y:visible;}
            .topnav-title { font-size: 14px; }
            .stat-card{height:auto;padding:16px;border-radius:16px;flex-direction:row;align-items:center;gap:16px;position:relative;}
            .stat-card::before{display:block;}
            .stat-card-content{width:auto;padding-right:0;}
            .stat-card-label{font-size:12px;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;color:var(--text-secondary);margin-bottom:8px;}
            .stat-card-icon{width:52px;height:52px;position:static;}
            .sc-table-card { padding: 2rem; margin-bottom: 2rem; }
            .sc-table-card-title { font-size: 1.25rem; margin-bottom: 1.5rem; }
            .sc-filter-row { flex-direction: row; gap: 12px; }
            .sc-filter-left { flex-direction: row; gap: 12px; width: auto; }
            .sc-search-group { min-width: 250px; width: auto; }
            .sc-filter-row-inline { display: flex; gap: 10px; }
            .sc-filter-row-inline .sc-select-group { width: auto; flex: 1; }
            .sc-filter-right { width: auto; }
            .sc-filter-right .sc-action-btn { width: auto; justify-content: flex-start; }
            .archive-filter-bar { flex-direction: row !important; }
            .archive-filter-bar > div:first-child { width: auto !important; max-width: 280px !important; }
            .archive-filter-row-inline { display: flex !important; gap: 10px !important; width: auto !important; }
            .archive-filter-row-inline > div { width: auto !important; max-width: 180px !important; }
            .archive-filter-bar > div:nth-child(2) { width: auto !important; }
            .sc-table-responsive { border: 1px solid var(--border); overflow-x: auto; }
            .sc-data-table { table-layout: fixed; }
            .sc-data-table thead { display: table-header-group; }
            .sc-data-table tbody tr { display: table-row; background: transparent; border: none; border-radius: 0; margin-bottom: 0; padding: 0; box-shadow: none; }
            .sc-data-table tbody td { display: table-cell; justify-content: flex-start; padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); font-size: 0.875rem; gap: 0; }
            .sc-data-table tbody td::before { display: none; }
            .sc-data-table tbody td[data-label="Action"] { justify-content: flex-start; padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); }
            .sc-data-table tbody td .actions { justify-content: flex-start; }
            .sc-data-table tbody td .badge { font-size: 0.75rem; }
            .sc-pagination { gap: 12px; margin-top: 1.5rem; }
            .sc-page-btn { height: 38px; min-width: 38px; font-size: 0.875rem; padding: 0 0.75rem; }
        }

        /* ═════════════════════════════════════════════════════════════════════
           RESPONSIVE BREAKPOINT TIERS
           Mobile: 320px - 576px
           Tablet: 576px - 768px
           Small Desktop: 768px - 992px
           Desktop: 992px - 1200px
           Large Desktop: 1200px+
           ═════════════════════════════════════════════════════════════════════ */

        /* ── 1. Mobile (320px - 576px) ── */
        @media (max-width: 575.98px) {
            .main { padding: 12px !important; padding-top: 72px !important; }
            .stat-cards { grid-template-columns: repeat(2, 1fr) !important; gap: 10px !important; margin-bottom: 16px !important; }
            .stat-card { padding: 12px 10px !important; height: auto !important; min-height: 90px !important; }
            .stat-card-value { font-size: 20px !important; }
            .stat-card-label { font-size: 10px !important; }
            .stat-card-icon { width: 34px !important; height: 34px !important; top: 10px !important; right: 10px !important; }
            .dashboard-grid, .two-column { grid-template-columns: 1fr !important; gap: 14px !important; }
            .analytics-card, .activity-card { padding: 14px !important; }
            .sc-filter-row, .archive-filter-bar { flex-direction: column !important; width: 100% !important; gap: 8px !important; }
            .sc-filter-left, .sc-filter-row-inline, .archive-filter-row-inline { flex-direction: column !important; width: 100% !important; gap: 8px !important; }
            .sc-search-group, .sc-select-group, .archive-filter-bar > div { width: 100% !important; max-width: none !important; }
            .stepper { flex-wrap: wrap !important; gap: 6px !important; }
            .step span { font-size: 11px !important; }
            .step-number { width: 24px !important; height: 24px !important; font-size: 11px !important; }
        }

        /* ── 2. Tablet (576px - 768px) ── */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .main { padding: 16px !important; padding-top: 72px !important; }
            .stat-cards { grid-template-columns: repeat(2, 1fr) !important; gap: 14px !important; }
            .stat-card { padding: 14px !important; height: auto !important; }
            .stat-card-value { font-size: 24px !important; }
            .dashboard-grid, .two-column { grid-template-columns: 1fr !important; gap: 16px !important; }
            .sc-filter-row, .archive-filter-bar { flex-direction: row !important; flex-wrap: wrap !important; gap: 10px !important; }
            .sc-search-group { width: 100% !important; }
            .sc-filter-row-inline, .archive-filter-row-inline { display: flex !important; flex-direction: row !important; gap: 10px !important; width: 100% !important; }
            .sc-select-group, .archive-filter-row-inline > div { flex: 1 !important; min-width: 0 !important; }
        }

        /* ── 3 & 4. Collapsed icon-only sidebar (768px - 1199px) ── */
        @media (min-width: 768px) and (max-width: 1199.98px) {
            /* Sidebar: always visible, icon-only width */
            .sidebar {
                width: 72px !important;
                transform: translateX(0) !important;
                z-index: 1000 !important;
                overflow: visible !important;
            }
            .sidebar-overlay { display: none !important; }
            .hamburger-btn { display: none !important; }
            .mobile-header { display: none !important; }
            header { display: flex !important; }

            /* Brand: show only icon, centered */
            .sidebar-brand {
                justify-content: center !important;
                padding: 0 !important;
                gap: 0 !important;
            }
            .sidebar-brand span { display: none !important; }

            /* Menu items: icon-only, centered, tooltip on hover */
            .sidebar-menu { padding: 0.5rem 0 !important; }
            .sidebar-menu a {
                justify-content: center !important;
                padding: 0.9rem 0 !important;
                gap: 0 !important;
                position: relative !important;
                border-left: 3px solid transparent !important;
            }
            .sidebar-menu a.active { border-left-color: var(--accent-yellow) !important; }
            .sidebar-menu a span:not(.badge-count) {
                display: none !important;
                position: absolute !important;
                left: calc(100% + 12px) !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                background: #1A237E !important;
                color: #fff !important;
                padding: 6px 12px !important;
                border-radius: 6px !important;
                font-size: 12px !important;
                font-weight: 600 !important;
                white-space: nowrap !important;
                z-index: 2000 !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.25) !important;
                pointer-events: none !important;
            }
            .sidebar-menu a:hover span:not(.badge-count) { display: block !important; }
            .sidebar-menu li i[data-lucide], .sidebar-menu li svg {
                width: 22px !important;
                height: 22px !important;
                flex-shrink: 0 !important;
            }

            /* Dropdown toggle in collapsed sidebar */
            .sidebar-dropdown-toggle {
                justify-content: center !important;
                gap: 0 !important;
                padding: 0.9rem 0 !important;
            }
            .sidebar-dropdown-toggle .dropdown-chevron,
            .sidebar-dropdown-toggle i:last-child {
                display: none !important;
            }

            /* Dropdown submenu items in collapsed sidebar */
            .sidebar-dropdown-menu {
                background: rgba(0, 0, 0, 0.3) !important;
            }
            .sidebar-dropdown-menu a {
                justify-content: center !important;
                padding: 0.75rem 0 !important;
                gap: 0 !important;
                position: relative !important;
            }
            .sidebar-dropdown-menu a i {
                width: 20px !important;
                height: 20px !important;
            }
            .sidebar-dropdown-menu a .badge-count {
                position: absolute !important;
                top: 2px !important;
                right: 10px !important;
                width: 18px !important;
                height: 18px !important;
                font-size: 0.62rem !important;
                border-radius: 50% !important;
                margin: 0 !important;
                z-index: 10 !important;
            }
            .sidebar-dropdown-menu a:hover span:not(.badge-count) {
                display: block !important;
            }

            /* Main content offset */
            .main, .main-content {
                margin-left: 72px !important;
                width: calc(100% - 72px) !important;
                max-width: none !important;
                padding: 16px !important;
                padding-top: 14px !important;
            }
        }

        /* ── 3. Small Desktop only (768px - 992px) ── */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .stat-cards { grid-template-columns: repeat(2, 1fr) !important; gap: 14px !important; }
            .stat-card-value { font-size: 26px !important; }
            .dashboard-grid { grid-template-columns: 1fr !important; gap: 18px !important; }
            .two-column { grid-template-columns: 1.3fr 1fr !important; gap: 18px !important; }
            .sc-filter-row, .archive-filter-bar { flex-direction: row !important; flex-wrap: wrap !important; gap: 10px !important; }
            .sc-search-group { min-width: 180px !important; flex: 1.2 !important; }
            .sc-select-group { min-width: 110px !important; flex: 1 !important; }
            .sc-table-card { padding: 1.25rem !important; }
            .sc-data-table th, .sc-data-table td,
            .archive-table th, .archive-table td,
            #submittedTable th, #submittedTable td { padding: 0.6rem 0.7rem !important; font-size: 0.8rem !important; }
            .activity-card { height: auto !important; max-height: 480px !important; }
            .activity-feed { max-height: 380px !important; overflow-y: auto !important; }
        }

        /* ── 4. Desktop only (992px - 1200px) ── */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .main, .main-content { padding: 20px !important; padding-top: 14px !important; }
            .stat-cards { grid-template-columns: repeat(4, 1fr) !important; gap: 16px !important; }
            .stat-card-value { font-size: 30px !important; }
            .dashboard-grid { grid-template-columns: 1.4fr 1fr !important; gap: 20px !important; }
            .two-column { grid-template-columns: 1.5fr 1fr !important; gap: 20px !important; }
            .analytics-card, .activity-card { height: 500px !important; min-height: 500px !important; max-height: 500px !important; }
            .sc-filter-row, .archive-filter-bar { flex-direction: row !important; flex-wrap: wrap !important; gap: 12px !important; }
            .sc-search-group { min-width: 180px !important; flex: 1.3 !important; }
            .sc-select-group { min-width: 120px !important; flex: 1 !important; }
            .sc-data-table th, .sc-data-table td,
            .archive-table th, .archive-table td,
            #submittedTable th, #submittedTable td { padding: 0.75rem 0.9rem !important; font-size: 0.875rem !important; }
        }

        /* ── 5. Large Desktop (1200px+) ── */
        @media (min-width: 1200px) {
            .hamburger-btn { display: none !important; }
            .mobile-header { display: none !important; }
            .sidebar {
                width: var(--sidebar-width) !important;
                transform: translateX(0) !important;
                overflow: hidden !important;
            }
            /* Restore full sidebar label visibility at 1200px+ */
            .sidebar-brand { justify-content: flex-start !important; padding: 0 1.5rem !important; gap: 0.65rem !important; }
            .sidebar-brand span { display: inline !important; }
            .sidebar-menu a { justify-content: flex-start !important; padding: 0.75rem 1.5rem !important; gap: 0.75rem !important; }
            .sidebar-menu a span:not(.badge-count) { display: inline !important; position: static !important; background: none !important; color: inherit !important; padding: 0 !important; border-radius: 0 !important; font-size: inherit !important; font-weight: inherit !important; white-space: normal !important; box-shadow: none !important; pointer-events: auto !important; transform: none !important; }
            .sidebar-dropdown-menu a { justify-content: flex-start !important; padding: 0.6rem 1.25rem 0.6rem 2.25rem !important; gap: 0.65rem !important; }
            .sidebar-dropdown-menu a i { width: 18px !important; height: 18px !important; }
            .sidebar-dropdown-menu a .badge-count { position: static !important; margin-left: auto !important; width: 24px !important; height: 24px !important; font-size: 0.7rem !important; }
            .sidebar-dropdown-toggle .dropdown-chevron,
            .sidebar-dropdown-toggle i:last-child { display: inline-block !important; }
            .main { margin-left: var(--sidebar-width) !important; width: calc(100% - var(--sidebar-width)) !important; padding: var(--content-padding) !important; padding-top: 14px !important; }
            .stat-cards { grid-template-columns: repeat(4, 1fr) !important; gap: 24px !important; }
            .stat-card-value { font-size: 36px !important; }
            .dashboard-grid { grid-template-columns: 1.8fr 1fr !important; gap: 24px !important; }
            .two-column { grid-template-columns: 2fr 1fr !important; gap: 24px !important; }
            .sc-table-card { padding: 2.5rem; }
            .sc-table-card-title { font-size: 1.35rem; }
            .sc-filter-row { gap: 16px; }
            .sc-filter-left { gap: 16px; flex-wrap: nowrap; }
            .sc-filter-row-inline { flex-wrap: nowrap; flex: 1; }
            .sc-search-group { min-width: 0; flex: 1.3; }
            .sc-select-group { min-width: 0; flex: 1; }
            .sc-filter-row-inline .sc-select-group { min-width: 0; flex: 1; }
            .sc-filter-right { padding-top: 20px; }
            .sc-data-table th, .sc-data-table td,
            .archive-table th, .archive-table td,
            #submittedTable th, #submittedTable td { padding: 1rem 1.25rem; font-size: 0.9rem; }
            .sc-data-table th { font-size: 0.8rem; }
            .welcome-greeting { padding: 4px 0 16px 24px; }
            .sc-pagination { flex-direction: row; justify-content: space-between; align-items: center; gap: 16px; margin-top: 2rem; }
            
            /* Reduced & Equal heights for dashboard cards on desktop */
            .analytics-card, .activity-card { height: 550px !important; min-height: 550px !important; max-height: 550px !important; flex: none; }

            /* Remove header container at 1200px+ but keep the time and date */
            header.sc-flat-header {
                background: transparent !important;
                border-bottom: none !important;
                box-shadow: none !important;
                height: auto !important;
                padding: 0 !important;
                margin-bottom: 12px !important;
            }

            /* Larger, more readable inputs at 1200px+ */
            input[type=text], input[type=date], input[type=number], input[type=tel], select, textarea {
                font-size: 15px !important;
                padding: 12px 14px !important;
                height: 48px !important;
                border: 1px solid #C7D2FE !important;
            }
            textarea { height: auto !important; min-height: 90px !important; }

            /* Filters: match the input look at 1200px+ */
            #statusBtn, #assistanceBtn, #barangayBtn,
            #archiveBrgyBtn, #archiveTypeBtn,
            .archive-filter-bar {
                border-color: #C7D2FE !important;
            }
            #statusBtn, #assistanceBtn, #barangayBtn,
            #archiveBrgyBtn, #archiveTypeBtn {
                height: 44px !important;
                font-size: 15px !important;
            }
            #searchInput, #archiveSearch,
            #searchInput + button, #archiveSearch + button {
                height: 44px !important;
            }
            #searchInput, #archiveSearch {
                border-right: none !important;
                border-radius: 6px 0 0 6px !important;
            }
            .chart-wrapper { flex: 1; min-height: 0; height: 100%; flex-direction: row; align-items: center; gap: 24px; }
            .chart-legend { flex: 0 1 300px; width: auto; flex-direction: column; flex-wrap: nowrap; justify-content: center; gap: 16px; }
            .chart-canvas { min-height: 0 !important; height: 100%; flex: 1; }
            .activity-feed { flex: 1; min-height: 0; max-height: none; overflow-y: auto; padding-right: 8px; }
        }

        /* Uniform SweetAlert & Custom Modal Styles */
        .swal2-popup {
            border-radius: 16px !important;
            padding: 1.5rem !important;
            font-family: inherit !important;
            max-width: 95vw !important;
            box-sizing: border-box !important;
        }
        .swal2-title {
            color: #1A237E !important;
            font-weight: 700 !important;
            font-size: 1.35rem !important;
        }
        .swal2-html-container {
            font-size: 0.925rem !important;
            color: #374151 !important;
            max-height: 75vh !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        .swal2-confirm {
            background-color: #1A237E !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 10px 24px !important;
        }
        .swal2-cancel {
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 10px 24px !important;
        }
        .modal-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        @media (max-width: 575.98px) {
            .swal2-popup {
                padding: 1rem 0.75rem !important;
                border-radius: 12px !important;
            }
            .swal2-title {
                font-size: 1.1rem !important;
            }
            .swal2-actions {
                flex-direction: column-reverse !important;
                gap: 8px !important;
                width: 100% !important;
                margin-top: 1rem !important;
            }
            .swal2-actions button {
                width: 100% !important;
                margin: 0 !important;
                height: 44px !important;
            }
            .modal-grid-2 {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
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
    <button id="hamburgerBtn" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <i data-lucide="menu" style="width:24px;height:24px"></i>
    </button>
    @yield('content')
    <div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>
</div>
@include('admin.partials.account-status')
@stack('scripts')
<script>
    function confirmLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1A237E',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, log out',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

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
    });


</script>
</body>
</html>
