<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizen ID Card - MSWDO Silang</title>
    @vite(['resources/css/admin-compat.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #1A237E;
            --primary-dark: #121858;
            --secondary: #6B7280;
            --accent: #FBC02D;
            --danger: #D32F2F;
            --background: #F8FAFC;
            --cards: #FFFFFF;
            --text: #1F2937;
            --muted: #6B7280;
            --sidebar-bg: #1A237E;
            --border: #E5E7EB;
        }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            background: var(--sidebar-bg);
            width: 260px;
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .sidebar-brand i { font-size: 1.3rem; color: var(--accent); }
        .sidebar-menu {
            list-style: none;
            margin: 0;
            padding: 1rem 0;
            flex: 1;
        }
        .sidebar-menu li { margin-bottom: .2rem; }
        .sidebar-menu a {
            color: rgba(255,255,255,.75);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
            font-size: .9rem;
            border-left: 3px solid transparent;
            transition: all .2s ease;
        }
        .sidebar-menu a:hover { 
            background: rgba(255,255,255,.1); 
            color: var(--accent); 
        }
        .sidebar-menu a.active {
            background: rgba(255,255,255,.1);
            color: var(--accent);
            border-left-color: var(--accent);
        }
        .sidebar-menu a i { width: 20px; text-align: center; font-size: .95rem; }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 0;
            min-height: 100vh;
        }

        .top-navbar {
            background-color: var(--cards);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .breadcrumb-nav { font-size: .8rem; color: var(--muted); margin: 0; }
        .breadcrumb-nav a { color: var(--primary); text-decoration: none; }
        .page-title { font-size: 1.15rem; font-weight: 700; margin: 0; }

        /* ID Card Preview Styling */
        .id-card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            margin: 2rem 0;
        }

        /* 85.60 mm x 53.98 mm ratio is roughly 1.585 */
        .pvc-card {
            width: 475px;
            height: 300px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
            font-family: Arial, sans-serif;
            color: #000000;
            border: 1px solid #d1d5db;
            user-select: none;
            background-image: radial-gradient(circle at 10% 20%, rgba(26, 35, 126, 0.03) 0%, rgba(251, 192, 45, 0.02) 90%);
        }

        /* Front Side Design */
        .pvc-header {
            background-color: #1A237E;
            color: #ffffff;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #FBC02D;
        }
        .pvc-header-text {
            text-align: center;
            flex: 1;
        }
        .pvc-header-text h5 {
            font-size: 9.5px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .pvc-header-text p {
            font-size: 7.5px;
            margin: 0;
            opacity: 0.9;
        }
        .pvc-header-text span {
            font-size: 7.5px;
            font-weight: bold;
            color: #FBC02D;
            display: block;
            margin-top: 1px;
        }
        .pvc-logo {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 50%;
            background-color: white;
            padding: 2px;
        }

        .pvc-body {
            padding: 12px;
            display: flex;
            height: 250px;
        }
        .pvc-photo-section {
            width: 105px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .pvc-photo {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border: 2px solid #1A237E;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pvc-id-num {
            font-size: 9.5px;
            font-weight: bold;
            color: #D32F2F;
            background: rgba(211, 47, 47, 0.08);
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid rgba(211, 47, 47, 0.2);
            text-align: center;
            word-break: break-all;
            width: 98px;
        }
        .pvc-qr-code {
            width: 54px;
            height: 54px;
            object-fit: contain;
            margin-top: auto;
            border: 1px solid #E5E7EB;
            padding: 2px;
            background: white;
        }
        .pvc-details {
            flex: 1;
            padding-left: 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .pvc-name {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin: 0;
            text-transform: uppercase;
            border-bottom: 1.5px solid #E5E7EB;
            padding-bottom: 2px;
        }
        .pvc-field {
            display: flex;
            font-size: 9px;
            margin: 0;
            line-height: 1.3;
        }
        .pvc-label {
            font-weight: bold;
            color: #4B5563;
            width: 65px;
            text-transform: uppercase;
        }
        .pvc-value {
            color: #1F2937;
            flex: 1;
            font-weight: 500;
        }
        .pvc-signature-holder {
            margin-top: auto;
            border-top: 1px dashed #9CA3AF;
            text-align: center;
            font-size: 7.5px;
            color: #6B7280;
            padding-top: 2px;
            width: 80%;
            align-self: center;
        }

        /* Back Side Design */
        .pvc-back-header {
            background-color: #F8FAFC;
            border-bottom: 2px solid #1A237E;
            padding: 8px 12px;
            text-align: center;
        }
        .pvc-back-header h6 {
            font-size: 8.5px;
            font-weight: bold;
            color: #1A237E;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pvc-back-body {
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            height: calc(100% - 35px);
        }
        .pvc-back-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            font-size: 8.5px;
        }
        .pvc-back-field-full {
            grid-column: span 2;
        }
        .pvc-back-label {
            font-weight: bold;
            color: #4B5563;
            text-transform: uppercase;
            font-size: 7.5px;
            margin-bottom: 1px;
            display: block;
        }
        .pvc-back-value {
            color: #111827;
            font-weight: 600;
            display: block;
            line-height: 1.2;
        }
        .pvc-signatures-row {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 10px;
        }
        .pvc-sign-block {
            text-align: center;
            width: 45%;
        }
        .pvc-sign-line {
            border-top: 1.5px solid #000000;
            padding-top: 2px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .pvc-sign-subtitle {
            font-size: 6.5px;
            color: #4B5563;
        }
        .pvc-back-footer {
            background-color: #1A237E;
            color: #ffffff;
            font-size: 7.5px;
            text-align: center;
            padding: 4px 10px;
            margin-top: auto;
            line-height: 1.2;
            font-weight: 500;
        }

        /* Print styles */
        @media print {
            body {
                background: none;
                color: #000;
            }
            .sidebar, .top-navbar, .page-body-container, .card-actions-wrapper, .modal-backdrop, .sweet-alert, .swal2-container {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            .print-layout {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 85.60mm;
            }
            
            .print-pvc-card {
                width: 85.60mm;
                height: 53.98mm;
                box-shadow: none !important;
                border: none !important;
                margin: 0 0 10mm 0 !important;
                page-break-inside: avoid;
                page-break-after: always;
                border-radius: 0 !important;
                background-image: none !important;
                overflow: hidden;
            }
            
            /* Print CSS Scaling sizes */
            .print-pvc-card .pvc-header {
                padding: 1.5mm 2.5mm !important;
                border-bottom: 0.8mm solid #FBC02D !important;
            }
            .print-pvc-card .pvc-header-text h5 { font-size: 8pt !important; }
            .print-pvc-card .pvc-header-text p { font-size: 6pt !important; }
            .print-pvc-card .pvc-header-text span { font-size: 6pt !important; }
            .print-pvc-card .pvc-logo { width: 8.5mm !important; height: 8.5mm !important; }
            .print-pvc-card .pvc-body { padding: 2.5mm 3.5mm !important; height: auto !important; }
            .print-pvc-card .pvc-photo-section { width: 23mm !important; gap: 1.5mm !important; }
            .print-pvc-card .pvc-photo { width: 21mm !important; height: 21mm !important; border-radius: 1mm !important; }
            .print-pvc-card .pvc-id-num { font-size: 7.5pt !important; padding: 0.5mm 1.5mm !important; width: auto !important; }
            .print-pvc-card .pvc-qr-code { width: 13mm !important; height: 13mm !important; }
            .print-pvc-card .pvc-details { padding-left: 3mm !important; gap: 1mm !important; }
            .print-pvc-card .pvc-name { font-size: 11pt !important; }
            .print-pvc-card .pvc-field { font-size: 7.5pt !important; }
            .print-pvc-card .pvc-label { width: 17mm !important; }
            .print-pvc-card .pvc-signature-holder { font-size: 6pt !important; margin-top: 1mm !important; }
            
            .print-pvc-card .pvc-back-header { padding: 1.5mm 2.5mm !important; }
            .print-pvc-card .pvc-back-header h6 { font-size: 7pt !important; }
            .print-pvc-card .pvc-back-body { padding: 2mm 3.5mm !important; }
            .print-pvc-card .pvc-back-details { gap: 1.5mm !important; font-size: 7.5pt !important; }
            .print-pvc-card .pvc-back-label { font-size: 6pt !important; }
            .print-pvc-card .pvc-sign-line { font-size: 7pt !important; }
            .print-pvc-card .pvc-sign-subtitle { font-size: 5.5pt !important; }
            .print-pvc-card .pvc-back-footer { font-size: 6pt !important; padding: 1mm 2mm !important; }
        }

        .card-actions-wrapper {
            background-color: var(--cards);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .btn-action {
            font-weight: 600;
            border-radius: 8px;
            padding: 0.6rem 1.25rem;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .id-card-container { overflow-x: auto; }

        /* ── Sidebar Overlay ── */
        .sidebar-overlay.active { display: block !important; }
        .sidebar { transform: translateX(-100%) !important; z-index: 1001 !important; }
        .sidebar.show { transform: translateX(0) !important; }
        .main, .main-content { margin-left: 0 !important; max-width: 100% !important; }
        .main, .main-content { padding: 16px !important; }
        .stat-cards { grid-template-columns: repeat(2, 1fr) !important; }
        .dashboard-grid { grid-template-columns: 1fr !important; }

        /* ── Desktop (min-width: 1200px) ── */
        @media (min-width: 1200px) {
            .sidebar { transform: none !important; z-index: 1000 !important; }
            .sidebar.show { transform: none !important; }
            .main, .main-content { margin-left: 260px !important; max-width: calc(100% - 260px) !important; }
            .main, .main-content { padding: 0 !important; }
        }

        .main, .main-content { padding: 12px !important; }
        .stat-cards { grid-template-columns: 1fr !important; }
        .topnav, .top-navbar { padding: 10px 12px !important; }
        .topnav-datetime, .navbar-datetime { display: none !important; }
        .filter-bar, .filter-group { flex-wrap: wrap; }
        .filter-bar > div, .filter-group > div { min-width: 0 !important; }

        /* ── Tablet+ (min-width: 768px) ── */
        @media (min-width: 768px) {
            .main, .main-content { padding: 16px !important; }
            .stat-cards { grid-template-columns: repeat(2, 1fr) !important; }
            .topnav, .top-navbar { padding: 1rem 2rem !important; }
            .topnav-datetime, .navbar-datetime { display: block !important; }
            .filter-bar, .filter-group { flex-wrap: nowrap !important; }
            .filter-bar > div, .filter-group > div { min-width: auto !important; }
        }

        /* ── Responsive: Small Mobile (< 480px) ── */
        @media (max-width: 479px) {
            .stat-card-icon { width: 40px !important; height: 40px !important; }
            .stat-card-value { font-size: 24px !important; }
            .stat-cards { gap: 12px !important; }
        }
        .mobile-header{display:flex !important;position:fixed;top:0;left:0;right:0;z-index:1000;background:#1A237E;color:#fff;padding:0 16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);align-items:center;justify-content:space-between;height:80px;}
        .mobile-header-brand{display:flex;align-items:center;gap:16px;flex:1;min-width:0;}
        .mobile-logo{width:56px;height:56px;border-radius:50%;background:#FBC02D;padding:4px;flex-shrink:0;}
        .mobile-logo-img{width:100%;height:100%;border-radius:50%;object-fit:cover;}
        .mobile-brand-text{flex:1;min-width:0;}
        .mobile-brand-title{font-size:18px;font-weight:700;color:#ffffff;margin:0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mobile-brand-subtitle{font-size:12px;color:rgba(255,255,255,0.8);margin:2px 0 0 0;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mobile-menu-btn{display:flex;align-items:center;justify-content:center;background:transparent;border:none;color:#ffffff;cursor:pointer;padding:8px;flex-shrink:0;margin-right:24px;}
        .mobile-menu-icon{width:32px;height:32px;}
        .main,.main-content{margin-left:0 !important;max-width:100% !important;height:auto !important;overflow:visible !important;padding:12px 14px !important;padding-top:90px !important;}
        .top-navbar{display:none !important;}
        .hamburger-btn{display:none !important;}
        @media(min-width:768px){.main,.main-content{padding:16px !important;padding-top:64px !important;}.top-navbar{display:block !important;}.hamburger-btn{display:flex !important;}.mobile-header{display:none !important;}}
        @media(min-width:1200px){.hamburger-btn{display:none !important;}.mobile-header{display:none !important;}}
        @media(max-width:479px){.main,.main-content{padding:10px !important;padding-top:88px !important;}.mobile-header{height:72px !important;}.mobile-logo{width:48px !important;height:48px !important;}.mobile-brand-title{font-size:16px !important;}.mobile-brand-subtitle{font-size:11px !important;}.mobile-menu-icon{width:28px !important;height:28px !important;}}
    </style>
</head>
<body>

<!-- ======================== SIDEBAR ======================== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-user-friends"></i>
        <span>Senior Citizen</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/senior"><i class="fas fa-user-friends"></i> Dashboard</a></li>
        <li><a href="/admin/senior/registration"><i class="fas fa-user-plus"></i> Registration</a></li>
        <li><a href="/admin/senior/masterlist" class="active"><i class="fas fa-list"></i> Masterlist</a></li>
        <li><a href="/admin/senior/birthdays"><i class="fas fa-birthday-cake"></i> Birthday Beneficiaries</a></li>
        <li><a href="/admin/senior/payouts-history"><i class="fas fa-history"></i> Payout History</a></li>
        <li><a href="/admin/senior/statistics"><i class="fas fa-chart-bar"></i> Statistics</a></li>
        <li><a href="/admin/senior/reports"><i class="fas fa-file-alt"></i> Reports</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>
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
            <p class="mobile-brand-subtitle">Senior Citizen ID Card</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<!-- ======================== MAIN ======================== -->
<div class="main-content">
    
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-lg-none me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h5 class="mb-0 page-title">Senior Citizen ID Card</h5>
                    <p class="breadcrumb-nav">
                        <a href="/admin/senior">Dashboard</a> / <a href="/admin/senior/masterlist">Masterlist</a> / ID Card
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-4 text-muted small d-none d-md-block" id="currentDateTime"></div>
                <div style="width: 35px; height: 35px; font-size: 0.875rem; background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; border-radius: 50%;">{{ strtoupper(substr((session('admin_user_name') ?? 'Admin User'), 0, 2)) }}</div>
            </div>
        </div>
    </nav>

    <!-- Page Body (Standard Browser Layout) -->
    <div class="page-body p-4 page-body-container">
        
        <div style="display:flex;align-items:flex-start;gap:10px;background:#EEF2FF;border:1px solid #C7D2FE;border-radius:10px;padding:12px 16px;margin-bottom:1.25rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#1A237E" stroke-width="2" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
            </svg>
            <p style="margin:0;font-size:0.875rem;color:#3730A3;line-height:1.5;">Generate and print the official ID card for registered senior citizens.</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                
                @if(!$senior->senior_id_number)
                    <!-- ======================== FORM TO GENERATE ID ======================== -->
                    <div class="card p-4">
                        <h4 class="h5 fw-bold mb-1 text-primary"><i class="fas fa-magic me-2"></i>Generate ID Card</h4>
                        <p class="text-muted small mb-4">Complete the fields below to generate the printable ID card profile for <strong>{{ $senior->full_name }}</strong>.</p>
                        
                        <form action="{{ route('admin.senior.id-card.generate', $senior->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Profile Photo <span class="text-danger">*</span></label>
                                    <input type="file" name="photo" class="form-control" accept="image/*" required>
                                    <div class="form-text small">Select a high-resolution, formal headshot photo (JPG, PNG, max 2MB).</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Blood Type</label>
                                    <select class="form-select" name="blood_type">
                                        <option value="">Unknown / Not Available</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Civil Status</label>
                                    <select class="form-select" name="civil_status">
                                        <option value="">Select status...</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold text-muted border-bottom pb-2">Emergency Contact Details</h6>
                                </div>
                                
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Contact Person Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" placeholder="FullName of contact person">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" name="emergency_contact_number" class="form-control" placeholder="e.g. 0917XXXXXXX">
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" class="form-control" placeholder="e.g. Spouse, Son, Daughter">
                                </div>
                                
                                <div class="col-12 text-end mt-4">
                                    <a href="/admin/senior/masterlist" class="btn btn-light border me-2">Back to Masterlist</a>
                                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-check me-2"></i>Generate Card</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- ======================== ID CARD PREVIEWS ======================== -->
                    <div class="card p-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                            <h4 class="h5 fw-bold mb-0 text-primary"><i class="fas fa-id-card me-2"></i>ID Card Preview</h4>
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Generated</span>
                        </div>
                        
                        <div class="id-card-container">
                            
                            <!-- Front Side Card -->
                            <div class="pvc-card" id="frontCard">
                                <div class="pvc-header">
                                    <img src="{{ public_path('images/mswdo-logo.png') && file_exists(public_path('images/mswdo-logo.png')) ? asset('images/mswdo-logo.png') : 'https://ui-avatars.com/api/?name=Silang&background=fff&color=1A237E' }}" class="pvc-logo" alt="Seal">
                                    <div class="pvc-header-text">
                                        <h5>Republic of the Philippines</h5>
                                        <p>Province of Cavite • Municipality of Silang</p>
                                        <span>OFFICE OF THE SENIOR CITIZENS AFFAIRS</span>
                                    </div>
                                    <img src="{{ public_path('images/mswdo-logo.png') && file_exists(public_path('images/mswdo-logo.png')) ? asset('images/mswdo-logo.png') : 'https://ui-avatars.com/api/?name=OSCA&background=fff&color=1A237E' }}" class="pvc-logo" alt="OSCA">
                                </div>
                                <div class="pvc-body">
                                    <div class="pvc-photo-section">
                                        <img src="{{ asset($senior->photo) }}" class="pvc-photo" alt="Photo">
                                        <div class="pvc-id-num">{{ $senior->senior_id_number }}</div>
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($senior->qr_code) }}" class="pvc-qr-code" alt="QR Code">
                                    </div>
                                    <div class="pvc-details">
                                        <p class="pvc-name">{{ $senior->full_name }}</p>
                                        
                                        <div class="pvc-field mt-1">
                                            <span class="pvc-label">Birthdate:</span>
                                            <span class="pvc-value">{{ \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="pvc-field">
                                            <span class="pvc-label">Age / Sex:</span>
                                            <span class="pvc-value">{{ $senior->age }} yrs old / {{ $senior->sex }}</span>
                                        </div>
                                        <div class="pvc-field">
                                            <span class="pvc-label">Barangay:</span>
                                            <span class="pvc-value">{{ $senior->barangay }}</span>
                                        </div>
                                        <div class="pvc-field">
                                            <span class="pvc-label">Issued:</span>
                                            <span class="pvc-value">{{ \Carbon\Carbon::parse($senior->date_issued)->format('M d, Y') }}</span>
                                        </div>
                                        
                                        <div class="pvc-signature-holder">
                                            SIGNATURE OF HOLDER
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Back Side Card -->
                            <div class="pvc-card" id="backCard">
                                <div class="pvc-back-header">
                                    <h6>ID Card Terms &amp; Reference Information</h6>
                                </div>
                                <div class="pvc-back-body">
                                    <div class="pvc-back-details">
                                        <div class="pvc-back-field-full">
                                            <span class="pvc-back-label">Residential Address:</span>
                                            <span class="pvc-back-value">{{ $senior->address }}, {{ $senior->barangay }}, Silang, Cavite</span>
                                        </div>
                                        <div>
                                            <span class="pvc-back-label">Blood Type:</span>
                                            <span class="pvc-back-value">{{ $senior->blood_type ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="pvc-back-label">Civil Status:</span>
                                            <span class="pvc-back-value">{{ $senior->civil_status ?? 'N/A' }}</span>
                                        </div>
                                        <div class="pvc-back-field-full">
                                            <span class="pvc-back-label">Emergency Contact Name:</span>
                                            <span class="pvc-back-value">{{ $senior->emergency_contact_name ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="pvc-back-label">Contact Number:</span>
                                            <span class="pvc-back-value">{{ $senior->emergency_contact_number ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="pvc-back-label">Relationship:</span>
                                            <span class="pvc-back-value">{{ $senior->emergency_contact_relationship ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="pvc-signatures-row">
                                        <div class="pvc-sign-block">
                                            @if($oscaHeadSignature && file_exists(public_path($oscaHeadSignature)))
                                                <img src="{{ asset($oscaHeadSignature) }}" style="max-height: 20px; max-width: 100%; margin-bottom: 2px;">
                                            @else
                                                <div style="height: 12px;"></div>
                                            @endif
                                            <div class="pvc-sign-line">OSCA head</div>
                                            <div class="pvc-sign-subtitle">OSCA Silang Head Office</div>
                                        </div>
                                        <div class="pvc-sign-block">
                                            @if($mswdoOfficerSignature && file_exists(public_path($mswdoOfficerSignature)))
                                                <img src="{{ asset($mswdoOfficerSignature) }}" style="max-height: 20px; max-width: 100%; margin-bottom: 2px;">
                                            @else
                                                <div style="height: 12px;"></div>
                                            @endif
                                            <div class="pvc-sign-line">MSWDO officer</div>
                                            <div class="pvc-sign-subtitle">Authorized Signature</div>
                                        </div>
                                    </div>
                                    
                                    <div class="pvc-back-footer">
                                        If found, please return this card to the Office of the Senior Citizens Affairs, Municipality of Silang, Cavite.
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                @endif
                
            </div>
            
            <!-- Metadata and Action Controls -->
            <div class="col-lg-4">
                <div class="card-actions-wrapper">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Options &amp; Control</h5>
                    
                    @if($senior->senior_id_number)
                        <div class="mb-4">
                            <label class="small text-muted fw-bold d-block mb-1">ID Number</label>
                            <p class="fw-bold text-danger mb-3">{{ $senior->senior_id_number }}</p>
                            
                            <label class="small text-muted fw-bold d-block mb-1">Issued On</label>
                            <p class="mb-3">{{ \Carbon\Carbon::parse($senior->date_issued)->format('M d, Y') }}</p>
                            
                            <label class="small text-muted fw-bold d-block mb-1">Total Print Count</label>
                            <p class="mb-3"><span class="badge bg-secondary px-3 py-2 fs-6" id="badgePrintCount">{{ $senior->print_count }}</span></p>
                            
                            <label class="small text-muted fw-bold d-block mb-1">Last Printed At</label>
                            <p class="mb-0 text-muted" id="textLastPrinted">
                                {{ $senior->last_printed_at ? \Carbon\Carbon::parse($senior->last_printed_at)->format('M d, Y h:i A') : 'Never Printed' }}
                            </p>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button onclick="printCard('both')" class="btn btn-action btn-primary"><i class="fas fa-print"></i> Print Both Sides</button>
                            <button onclick="printCard('front')" class="btn btn-action btn-outline-primary"><i class="fas fa-id-card"></i> Print Front Side</button>
                            <button onclick="printCard('back')" class="btn btn-action btn-outline-primary"><i class="fas fa-arrow-left"></i> Print Back Side</button>
                            <a href="{{ route('admin.senior.id-card.download', $senior->id) }}" class="btn btn-action btn-success"><i class="fas fa-file-pdf"></i> Download PDF</a>
                            <button onclick="reprintFlow()" class="btn btn-action btn-danger"><i class="fas fa-redo"></i> Reprint Card</button>
                            <a href="/admin/senior/masterlist" class="btn btn-action btn-light border"><i class="fas fa-arrow-left"></i> Back to Masterlist</a>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-id-card fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p class="small">Card not generated yet. Use the form to generate the card.</p>
                        </div>
                        <div class="d-grid">
                            <a href="/admin/senior/masterlist" class="btn btn-action btn-light border"><i class="fas fa-arrow-left"></i> Back to Masterlist</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
    </div><!-- /page-body -->
</div><!-- /main-content -->

<!-- ======================== PRINT-ONLY LAYOUT (HIDDEN ON BROWSER VIEW) ======================== -->
@if($senior->senior_id_number)
    <div class="print-layout d-none" id="printArea">
        <!-- Front Card -->
        <div class="pvc-card print-pvc-card" id="printFrontCard">
            <div class="pvc-header">
                <img src="{{ public_path('images/mswdo-logo.png') && file_exists(public_path('images/mswdo-logo.png')) ? asset('images/mswdo-logo.png') : 'https://ui-avatars.com/api/?name=Silang&background=fff&color=1A237E' }}" class="pvc-logo" alt="Seal">
                <div class="pvc-header-text">
                    <h5>Republic of the Philippines</h5>
                    <p>Province of Cavite • Municipality of Silang</p>
                    <span>OFFICE OF THE SENIOR CITIZENS AFFAIRS</span>
                </div>
                <img src="{{ public_path('images/mswdo-logo.png') && file_exists(public_path('images/mswdo-logo.png')) ? asset('images/mswdo-logo.png') : 'https://ui-avatars.com/api/?name=OSCA&background=fff&color=1A237E' }}" class="pvc-logo" alt="OSCA">
            </div>
            <div class="pvc-body">
                <div class="pvc-photo-section">
                    <img src="{{ asset($senior->photo) }}" class="pvc-photo" alt="Photo">
                    <div class="pvc-id-num">{{ $senior->senior_id_number }}</div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($senior->qr_code) }}" class="pvc-qr-code" alt="QR Code">
                </div>
                <div class="pvc-details">
                    <p class="pvc-name">{{ $senior->full_name }}</p>
                    
                    <div class="pvc-field mt-1">
                        <span class="pvc-label">Birthdate:</span>
                        <span class="pvc-value">{{ \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="pvc-field">
                        <span class="pvc-label">Age / Sex:</span>
                        <span class="pvc-value">{{ $senior->age }} yrs old / {{ $senior->sex }}</span>
                    </div>
                    <div class="pvc-field">
                        <span class="pvc-label">Barangay:</span>
                        <span class="pvc-value">{{ $senior->barangay }}</span>
                    </div>
                    <div class="pvc-field">
                        <span class="pvc-label">Issued:</span>
                        <span class="pvc-value">{{ \Carbon\Carbon::parse($senior->date_issued)->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="pvc-signature-holder">
                        SIGNATURE OF HOLDER
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Card -->
        <div class="pvc-card print-pvc-card" id="printBackCard">
            <div class="pvc-back-header">
                <h6>ID Card Terms &amp; Reference Information</h6>
            </div>
            <div class="pvc-back-body">
                <div class="pvc-back-details">
                    <div class="pvc-back-field-full">
                        <span class="pvc-back-label">Residential Address:</span>
                        <span class="pvc-back-value">{{ $senior->address }}, {{ $senior->barangay }}, Silang, Cavite</span>
                    </div>
                    <div>
                        <span class="pvc-back-label">Blood Type:</span>
                        <span class="pvc-back-value">{{ $senior->blood_type ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="pvc-back-label">Civil Status:</span>
                        <span class="pvc-back-value">{{ $senior->civil_status ?? 'N/A' }}</span>
                    </div>
                    <div class="pvc-back-field-full">
                        <span class="pvc-back-label">Emergency Contact Name:</span>
                        <span class="pvc-back-value">{{ $senior->emergency_contact_name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="pvc-back-label">Contact Number:</span>
                        <span class="pvc-back-value">{{ $senior->emergency_contact_number ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="pvc-back-label">Relationship:</span>
                        <span class="pvc-back-value">{{ $senior->emergency_contact_relationship ?? 'N/A' }}</span>
                    </div>
                </div>
                
                <div class="pvc-signatures-row">
                    <div class="pvc-sign-block">
                        @if($oscaHeadSignature && file_exists(public_path($oscaHeadSignature)))
                            <img src="{{ asset($oscaHeadSignature) }}" style="max-height: 12px; max-width: 100%; margin-bottom: 2px;">
                        @else
                            <div style="height: 12px;"></div>
                        @endif
                        <div class="pvc-sign-line">OSCA head</div>
                        <div class="pvc-sign-subtitle">OSCA Silang Head Office</div>
                    </div>
                    <div class="pvc-sign-block">
                        @if($mswdoOfficerSignature && file_exists(public_path($mswdoOfficerSignature)))
                            <img src="{{ asset($mswdoOfficerSignature) }}" style="max-height: 12px; max-width: 100%; margin-bottom: 2px;">
                        @else
                            <div style="height: 12px;"></div>
                        @endif
                        <div class="pvc-sign-line">MSWDO officer</div>
                        <div class="pvc-sign-subtitle">Authorized Signature</div>
                    </div>
                </div>
                
                <div class="pvc-back-footer">
                    If found, please return this card to the Office of the Senior Citizens Affairs, Municipality of Silang, Cavite.
                </div>
            </div>
        </div>
    </div>
@endif

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
        const now = new Date();
        const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' };
        const el = document.getElementById('currentDateTime');
        if (el) el.textContent = now.toLocaleDateString('en-PH', opts);
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Show alerts if session success/error
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#1A237E',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        });
    @endif
    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#1A237E',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        });
    @endif

    @if($senior->senior_id_number)
        function printCard(mode) {
            // Setup printable layout before opening printing dialog
            const front = document.getElementById('printFrontCard');
            const back = document.getElementById('printBackCard');
            
            front.classList.add('d-none');
            back.classList.add('d-none');

            if (mode === 'front') {
                front.classList.remove('d-none');
            } else if (mode === 'back') {
                back.classList.remove('d-none');
            } else {
                front.classList.remove('d-none');
                back.classList.remove('d-none');
            }

            // Temporarily unhide print layout and call printer
            const printArea = document.getElementById('printArea');
            printArea.classList.remove('d-none');
            
            window.print();
            
            // Hide it again
            printArea.classList.add('d-none');
        }

        function reprintFlow() {
            Swal.fire({
                title: 'Reprint ID Card',
                text: 'Are you sure you want to trigger a reprint? This will increment the reprint counter and log the transaction.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D32F2F',
                cancelButtonColor: '#EF4444',
                confirmButtonText: 'Yes, Reprint',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Call backend reprint endpoint
                    fetch('{{ route('admin.senior.id-card.reprint', $senior->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update UI details
                            document.getElementById('badgePrintCount').innerText = data.print_count;
                            document.getElementById('textLastPrinted').innerText = data.last_printed_at;
                            
                            Swal.fire({
                                title: 'Counter Incremented!',
                                text: 'Reprint has been recorded. Opening printing dialog now.',
                                icon: 'success',
                                confirmButtonColor: '#1A237E',
                                background: '#ffffff',
                                customClass: { popup: 'rounded-4 shadow-lg' }
                            }).then(() => {
                                printCard('both');
                            });
                        } else {
                            Swal.fire('Error', 'Failed to increment reprint transaction.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Something went wrong.', 'error');
                    });
                }
            });
        }
    @endif
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

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
            customClass: {
                popup: 'rounded-4 shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

</body>
</html>
