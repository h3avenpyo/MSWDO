<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Senior Citizen Statistics - MSWDO Silang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { corePlugins: { preflight: false } }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root{
            --primary:#1A237E;--primary-hover:#121858;--primary-dark:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--purple:#7C3AED;--purple-bg:#F3E8FF;--icon-blue:#3B82F6;--icon-green:#16A34A;--icon-purple:#7C3AED;--sidebar-width:260px;--content-padding:32px;--shadow:0 10px 30px rgba(15,23,42,.08);--shadow-hover:0 20px 40px rgba(15,23,42,.12);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);min-height:100%;}
        body{font-size:14px;line-height:1.5;overflow-x:hidden;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        a{text-decoration:none;}

        /* ── Buttons ── */
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--border);border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:500;cursor:pointer;transition:all .2s ease;padding:10px 20px;background:var(--surface);color:var(--text-primary);box-shadow:var(--shadow);height:44px;min-height:44px;text-decoration:none;white-space:nowrap;}
        .btn:hover{border-color:var(--primary);transform:translateY(-1px);}
        .btn svg{width:16px;height:16px;}
        .btn.primary{background:var(--primary);color:#FFFFFF;border-color:var(--primary);}
        .btn.primary:hover{background:var(--primary-hover);border-color:var(--primary-hover);transform:translateY(-1px);}
        .btn-clear{background:var(--surface);color:var(--danger);border-color:var(--danger);font-weight:600;}
        .btn-clear:hover{border-color:var(--danger);color:var(--danger);}

        /* ── Filter Section ── */
        .filter-section{background:var(--surface);border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);padding:12px 20px 20px 20px;}
        .section-spacing{margin-bottom:8px;margin-top:-12px;}
        .filter-field{display:flex;flex-direction:column;gap:1px;min-width:0;}
        .filter-label{font-size:11px;font-weight:600;color:var(--text-primary);margin-bottom:2px;margin-top:0;display:block;text-transform:uppercase;letter-spacing:0.05em;height:18px;line-height:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .filter-select{width:100%;height:44px;min-height:44px;border:1px solid var(--border);border-radius:8px;padding:0 12px;font-size:13px;color:var(--text-primary);background:var(--surface);cursor:pointer;transition:all .2s ease;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");background-repeat:no-repeat;background-position:right 0.75rem center;background-size:16px 12px;}
        .filter-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        #filterGrid{display:grid;grid-template-columns:1fr;gap:12px;align-items:end;}
        .filter-actions{display:flex;gap:8px;align-items:center;min-width:0;}
        .filter-actions .btn{flex:1;}

        /* ── Stat Cards ── */
        .stat-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:8px;margin-bottom:8px;}
        .stat-card{background:var(--surface);border-radius:16px;padding:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;position:relative;overflow:hidden;min-width:0;width:100%;max-width:100%;animation:fadeInUp .6s ease-out backwards;}
        .stat-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;transition:all .3s ease;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);}
        .stat-card-blue::before{background:var(--icon-blue);}
        .stat-card-green::before{background:var(--icon-green);}
        .stat-card-purple::before{background:var(--icon-purple);}
        .stat-card-red::before{background:var(--danger);}
        .stat-card-orange::before{background:#F59E0B;}
        .stat-card-content{flex:1;min-width:0;}
        .stat-card-label{font-size:11px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:var(--text-secondary);margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .stat-card-value{font-size:28px;font-weight:700;color:var(--text-primary);line-height:1;}
        .stat-card-icon{width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .stat-card-icon svg,.stat-card-icon img{width:24px;height:24px;object-fit:contain;}
        .stat-card-blue .stat-card-icon{background:var(--info-bg);color:var(--icon-blue);}
        .stat-card-green .stat-card-icon{background:var(--success-bg);color:var(--icon-green);}
        .stat-card-purple .stat-card-icon{background:var(--purple-bg);color:var(--icon-purple);}
        .stat-card-red .stat-card-icon{background:var(--danger-bg);color:var(--danger);}
        .stat-card-orange .stat-card-icon{background:#FFF7ED;color:#F59E0B;}
        .delay-1{animation-delay:.1s;}
        .delay-2{animation-delay:.2s;}
        .delay-3{animation-delay:.3s;}

        /* ── Charts ── */
        .charts-outer{width:100%;max-width:100%;box-sizing:border-box;}
        .charts-grid{display:grid;grid-template-columns:1fr;gap:16px;width:100%;max-width:100%;box-sizing:border-box;}
        .analytics-card{background:var(--surface);border-radius:16px;padding:20px;box-shadow:var(--shadow);border:1px solid var(--border);min-width:0;width:100%;max-width:100%;box-sizing:border-box;animation:fadeInUp .6s ease-out backwards;}
        .analytics-card h3{font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:16px;display:flex;align-items:center;gap:6px;}
        .analytics-card h3 svg{width:16px;height:16px;color:var(--icon-blue);}
        .chart-container{position:relative;height:260px;width:100%;max-width:100%;}

        /* ── Animations ── */
        @keyframes fadeInUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}

        /* ══════════════════════════════════════════════
           RESPONSIVE BREAKPOINTS
           ══════════════════════════════════════════════ */



        /* ── Small desktop (1200–1299px): filters in one row ── */
        @media (min-width:1200px) and (max-width:1299px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:24px;}
            #filterGrid{grid-template-columns:minmax(130px,1fr) minmax(110px,1fr) minmax(170px,1fr) minmax(110px,1fr) minmax(130px,1fr) auto;}
            .filter-actions{flex:0 0 auto;}
            .filter-actions .btn{flex:none;padding:0 22px;}
            .filter-label{font-size:13px !important;}
            .filter-select{font-size:14px !important;height:48px !important;min-height:48px !important;}
            .btn{height:48px !important;min-height:48px !important;font-size:14px !important;}
            .stat-cards{gap:12px;margin-bottom:8px;}
            .charts-grid{gap:24px;grid-template-columns:1fr 1fr;}
            .chart-container{height:320px;}
            .analytics-card{padding:24px;}
            .analytics-card h3{font-size:15px;margin-bottom:20px;}
            .stat-card-value{font-size:35px;}
        }

        /* ── Medium desktop (1300–1399px): filters in one row ── */
        @media (min-width:1300px) and (max-width:1399px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:24px;}
            #filterGrid{grid-template-columns:minmax(130px,1fr) minmax(110px,1fr) minmax(170px,1fr) minmax(110px,1fr) minmax(130px,1fr) auto;}
            .filter-actions{flex:0 0 auto;}
            .filter-actions .btn{flex:none;padding:0 22px;}
            .filter-label{font-size:13px !important;}
            .filter-select{font-size:14px !important;height:48px !important;min-height:48px !important;}
            .btn{height:48px !important;min-height:48px !important;font-size:14px !important;}
            .stat-cards{gap:12px;margin-bottom:8px;}
            .charts-grid{gap:24px;grid-template-columns:1fr 1fr;}
            .chart-container{height:320px;}
            .analytics-card{padding:24px;}
            .analytics-card h3{font-size:15px;margin-bottom:20px;}
            .stat-card-value{font-size:36px;}
        }

        /* ── Large laptop (1400px+): six stat cards in one row ── */
        @media (min-width:1400px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:24px;}
            #filterGrid{grid-template-columns:minmax(130px,1fr) minmax(110px,1fr) minmax(170px,1fr) minmax(110px,1fr) minmax(130px,1fr) auto;}
            .filter-actions{flex:0 0 auto;}
            .filter-actions .btn{flex:none;padding:0 22px;}
            .filter-label{font-size:13px !important;}
            .filter-select{font-size:14px !important;height:48px !important;min-height:48px !important;}
            .btn{height:48px !important;min-height:48px !important;font-size:14px !important;}
            .stat-cards{grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:8px;}
            .charts-grid{gap:24px;grid-template-columns:1fr 1fr;}
            .chart-container{height:360px;}
            .analytics-card{padding:28px;}
            .analytics-card h3{font-size:15px;margin-bottom:20px;}
            .stat-card-value{font-size:38px;}
        }

        /* ── Extra large desktop (1600px+): larger font size ── */
        @media (min-width:1600px){
            .stat-card-value{font-size:42px;}
        }



        /* ── Small tablet (768–959px): filters three per row ── */
        @media (min-width:768px) and (max-width:959px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:16px !important;}
            #filterGrid{grid-template-columns:repeat(3,1fr);gap:12px;}
            .filter-actions .btn{flex:1;}
            .stat-cards{grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:8px;}
            .charts-grid{grid-template-columns:1fr;gap:16px;}
            .chart-container{height:290px;}
            .analytics-card{padding:16px;}
            .stat-card-value{font-size:30px;}
        }

        /* ── Medium tablet (960–1099px): filters four per row ── */
        @media (min-width:960px) and (max-width:1099px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:16px !important;}
            #filterGrid{grid-template-columns:repeat(4,1fr);gap:12px;}
            .filter-actions .btn{flex:1;}
            .stat-cards{grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:8px;}
            .charts-grid{grid-template-columns:1fr;gap:16px;}
            .chart-container{height:290px;}
            .analytics-card{padding:16px;}
            .stat-card-value{font-size:33px;}
        }

        /* ── Large tablet (1100–1199px): filters five per row ── */
        @media (min-width:1100px) and (max-width:1199px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:16px !important;}
            #filterGrid{grid-template-columns:repeat(5,1fr);gap:12px;}
            .filter-actions .btn{flex:1;}
            .stat-cards{grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:8px;}
            .charts-grid{grid-template-columns:1fr;gap:16px;}
            .chart-container{height:290px;}
            .analytics-card{padding:16px;}
            .stat-card-value{font-size:34px;}
        }

        /* ── Small desktop (1200–1299px): filters in one row ── */
        @media (min-width:1200px) and (max-width:1299px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:24px;}
            #filterGrid{grid-template-columns:minmax(130px,1fr) minmax(110px,1fr) minmax(170px,1fr) minmax(110px,1fr) minmax(130px,1fr) auto;}
            .filter-actions{flex:0 0 auto;}
            .filter-actions .btn{flex:none;padding:0 22px;}
            .filter-label{font-size:13px !important;}
            .filter-select{font-size:14px !important;height:48px !important;min-height:48px !important;}
            .btn{height:48px !important;min-height:48px !important;font-size:14px !important;}
            .stat-cards{gap:12px;margin-bottom:8px;}
            .charts-grid{gap:24px;grid-template-columns:1fr 1fr;}
            .chart-container{height:320px;}
            .analytics-card{padding:24px;}
            .analytics-card h3{font-size:15px;margin-bottom:20px;}
            .stat-card-value{font-size:35px;}
        }

        /* ── Medium desktop (1300–1399px): filters in one row ── */
        @media (min-width:1300px) and (max-width:1399px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:24px;}
            #filterGrid{grid-template-columns:minmax(130px,1fr) minmax(110px,1fr) minmax(170px,1fr) minmax(110px,1fr) minmax(130px,1fr) auto;}
            .filter-actions{flex:0 0 auto;}
            .filter-actions .btn{flex:none;padding:0 22px;}
            .filter-label{font-size:13px !important;}
            .filter-select{font-size:14px !important;height:48px !important;min-height:48px !important;}
            .btn{height:48px !important;min-height:48px !important;font-size:14px !important;}
            .stat-cards{gap:12px;margin-bottom:8px;}
            .charts-grid{gap:24px;grid-template-columns:1fr 1fr;}
            .chart-container{height:320px;}
            .analytics-card{padding:24px;}
            .analytics-card h3{font-size:15px;margin-bottom:20px;}
            .stat-card-value{font-size:36px;}
        }

        /* ── Large mobile (600–767px): filters three per row ── */
        @media (min-width:600px) and (max-width:767px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:14px !important;}
            #filterGrid{grid-template-columns:repeat(3,1fr);gap:12px;}
            .filter-actions{flex-direction:column;gap:8px;}
            .filter-actions .btn{width:100%;flex:1 1 auto;min-height:44px;}
            .stat-cards{grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;}
            .stat-card{padding:16px;flex-direction:column;align-items:flex-start;gap:8px;}
            .stat-card::before{display:none;}
            .stat-card-content{width:100%;padding-right:48px;}
            .stat-card-value{font-size:28px;}
            .stat-card-icon{width:40px;height:40px;position:absolute;top:14px;right:14px;}
            .stat-card-icon svg,.stat-card-icon img{width:20px;height:20px;}
            .charts-grid{grid-template-columns:1fr;gap:16px;}
            .chart-container{height:260px;}
            .analytics-card{padding:14px !important;border-radius:14px !important;}
        }

        /* ── Medium mobile (480–599px): filters two per row ── */
        @media (min-width:480px) and (max-width:599px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:14px !important;}
            #filterGrid{grid-template-columns:1fr 1fr;gap:12px;}
            .filter-actions{flex-direction:column;gap:8px;}
            .filter-actions .btn{width:100%;flex:1 1 auto;min-height:44px;}
            .stat-cards{grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;}
            .stat-card{padding:16px;flex-direction:column;align-items:flex-start;gap:8px;}
            .stat-card::before{display:none;}
            .stat-card-content{width:100%;padding-right:48px;}
            .stat-card-value{font-size:26px;}
            .stat-card-icon{width:40px;height:40px;position:absolute;top:14px;right:14px;}
            .stat-card-icon svg,.stat-card-icon img{width:20px;height:20px;}
            .charts-grid{grid-template-columns:1fr;gap:16px;}
            .chart-container{height:260px;}
            .analytics-card{padding:14px !important;border-radius:14px !important;}
        }

        /* ── Small mobile (<480px): filters two per row ── */
        @media (max-width:479px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:14px !important;}
            #filterGrid{grid-template-columns:1fr 1fr;gap:12px;}
            .filter-actions{flex-direction:column;gap:8px;}
            .filter-actions .btn{width:100%;flex:1 1 auto;min-height:44px;}
            .stat-cards{grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;}
            .stat-card{padding:16px;flex-direction:column;align-items:flex-start;gap:8px;}
            .stat-card::before{display:none;}
            .stat-card-content{width:100%;padding-right:48px;}
            .stat-card-value{font-size:28px;}
            .stat-card-icon{width:40px;height:40px;position:absolute;top:14px;right:14px;}
            .stat-card-icon svg,.stat-card-icon img{width:20px;height:20px;}
            .charts-grid{grid-template-columns:1fr;gap:16px;}
            .chart-container{height:260px;}
            .analytics-card{padding:14px !important;border-radius:14px !important;}
        }

        /* ── Small mobile (<480px): filters two per row ── */
        @media (max-width:479px){
            .section-spacing{margin-bottom:8px;margin-top:-12px;}
            .filter-section{padding:12px !important;}
            #filterGrid{grid-template-columns:1fr 1fr;gap:12px;}
            .filter-actions{flex-direction:column;gap:8px;}
            .filter-actions .btn{width:100%;flex:1 1 auto;min-height:44px;}
            .stat-cards{grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;}
            .stat-card{padding:14px;flex-direction:column;align-items:flex-start;gap:8px;}
            .stat-card::before{display:none;}
            .stat-card-content{width:100%;padding-right:48px;}
            .stat-card-value{font-size:22px;}
            .stat-card-icon{width:36px;height:36px;position:absolute;top:12px;right:12px;}
            .stat-card-icon svg,.stat-card-icon img{width:16px;height:16px;}
            .chart-container{height:240px;}
            .analytics-card{padding:12px !important;}
        }

        /* ── Very small mobile (<360px): filters two per row ── */
        @media (max-width:359px){
            .stat-card-value{font-size:20px;}
        }
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'statistics', 'mobileSubtitle' => 'Statistics'])

    <div class="main">
        <div class="main-scroll">
            <!-- Filter Section -->
            <div class="filter-section section-spacing">
                <form id="filterForm" method="GET" action="{{ route('admin.senior.analytics') }}" autocomplete="off">
                    <div id="filterGrid">
                        <div class="filter-field">
                            <label class="filter-label" for="yearFilter">Year</label>
                            <select class="filter-select" id="yearFilter" name="year">
                                <option value="2026" {{ $year == 2026 ? 'selected' : '' }}>2026</option>
                                <option value="2025" {{ $year == 2025 ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ $year == 2024 ? 'selected' : '' }}>2024</option>
                                <option value="2023" {{ $year == 2023 ? 'selected' : '' }}>2023</option>
                                <option value="2022" {{ $year == 2022 ? 'selected' : '' }}>2022</option>
                            </select>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label" for="monthFilter">Month</label>
                            <select class="filter-select" id="monthFilter" name="month">
                                <option value="" {{ $month === null || $month === '' ? 'selected' : '' }}>All</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $i, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label" for="barangayFilter">Barangay</label>
                            <select class="filter-select" id="barangayFilter" name="barangay">
                                <option value="" {{ $barangay === null || $barangay === '' ? 'selected' : '' }}>All</option>
                                @foreach($allBarangays as $b)
                                    <option value="{{ $b }}" {{ $barangay === $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label" for="genderFilter">Gender</label>
                            <select class="filter-select" id="genderFilter" name="gender">
                                <option value="" {{ $gender === null || $gender === '' ? 'selected' : '' }}>All</option>
                                <option value="Male" {{ $gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $gender == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="filter-field">
                            <label class="filter-label" for="ageGroupFilter">Age Group</label>
                            <select class="filter-select" id="ageGroupFilter" name="age_group">
                                <option value="" {{ $ageGroup === null || $ageGroup === '' ? 'selected' : '' }}>All</option>
                                <option value="60-69" {{ $ageGroup == '60-69' ? 'selected' : '' }}>60-69</option>
                                <option value="70-79" {{ $ageGroup == '70-79' ? 'selected' : '' }}>70-79</option>
                                <option value="80-89" {{ $ageGroup == '80-89' ? 'selected' : '' }}>80-89</option>
                                <option value="90-99" {{ $ageGroup == '90-99' ? 'selected' : '' }}>90-99</option>
                                <option value="100+" {{ $ageGroup == '100+' ? 'selected' : '' }}>100+</option>
                            </select>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn primary">
                                <i data-lucide="check" style="width:16px;height:16px"></i> Apply
                            </button>
                            <a href="{{ route('admin.senior.analytics') }}" class="btn btn-clear">
                                <i data-lucide="rotate-ccw" style="width:16px;height:16px"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="stat-cards">
                <div class="stat-card stat-card-blue">
                    <div class="stat-card-content">
                        <div class="stat-card-label">TOTAL SENIORS</div>
                        <div class="stat-card-value">{{ $totalSeniors }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="users"></i></div>
                </div>
                <div class="stat-card stat-card-blue delay-1">
                    <div class="stat-card-content">
                        <div class="stat-card-label">MALE</div>
                        <div class="stat-card-value">{{ $maleCount }}</div>
                    </div>
                    <div class="stat-card-icon">
                        <img src="{{ asset('images/male.png') }}" alt="Male">
                    </div>
                </div>
                <div class="stat-card stat-card-purple delay-2">
                    <div class="stat-card-content">
                        <div class="stat-card-label">FEMALE</div>
                        <div class="stat-card-value">{{ $femaleCount }}</div>
                    </div>
                    <div class="stat-card-icon">
                        <img src="{{ asset('images/female.png') }}" alt="Female">
                    </div>
                </div>
                <div class="stat-card stat-card-green delay-3">
                    <div class="stat-card-content">
                        <div class="stat-card-label">ACTIVE</div>
                        <div class="stat-card-value">{{ $activeSeniors }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="check-circle"></i></div>
                </div>
                <div class="stat-card stat-card-red">
                    <div class="stat-card-content">
                        <div class="stat-card-label">INACTIVE</div>
                        <div class="stat-card-value">{{ $inactiveSeniors }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="user-x"></i></div>
                </div>
                <div class="stat-card stat-card-orange">
                    <div class="stat-card-content">
                        <div class="stat-card-label">BARANGAYS</div>
                        <div class="stat-card-value">{{ $totalBarangays }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="map-pin"></i></div>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-outer">
                <div class="charts-grid">
                    <div class="analytics-card">
                        <h3><i data-lucide="pie-chart"></i>Gender Distribution</h3>
                        <div class="chart-container">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                    <div class="analytics-card delay-1">
                        <h3><i data-lucide="activity"></i>Age Group Distribution</h3>
                        <div class="chart-container">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    // Force barangay filter to "All" on page load
    document.addEventListener('DOMContentLoaded', function() {
        const barangayFilter = document.getElementById('barangayFilter');
        if (barangayFilter) {
            // Always reset to "All"
            barangayFilter.value = '';
            // Remove barangay parameter from URL
            const url = new URL(window.location);
            url.searchParams.delete('barangay');
            window.history.replaceState({}, '', url);
        }
    });
</script>

<script>
    const isCompact = window.innerWidth < 480;
    const isTablet = window.innerWidth >= 480 && window.innerWidth < 1200;
    const legendFontSize = isCompact ? 10 : (isTablet ? 11 : 13);

    // Gender Distribution Chart
    const genderLabels = {!! json_encode($genderStats->pluck('sex')) !!};
    const genderValues = {!! json_encode($genderStats->pluck('total')) !!};
    const genderColors = ['#1A237E', '#EC4899'];
    const genderTotal = genderValues.reduce((a, b) => a + b, 0);

    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderValues,
                backgroundColor: genderColors,
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 20 },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: isCompact ? 12 : (isTablet ? 18 : 25),
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: isCompact ? 8 : 10,
                        font: { size: legendFontSize, weight: 500 },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = genderTotal > 0 ? ((value / genderTotal) * 100).toFixed(1) : '0.0';
                                    return {
                                        text: `${label} — ${value} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const percentage = genderTotal > 0 ? ((context.raw / genderTotal) * 100).toFixed(1) : '0.0';
                            return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '72%'
        }
    });

    // Age Groups Chart
    const ageLabels = {!! json_encode($ageGroups->pluck('age_group')) !!};
    const ageValues = {!! json_encode($ageGroups->pluck('total')) !!};
    const ageColors = ageLabels.map(label => {
        if (label === '60-69') return 'rgba(26, 35, 126, 0.85)';
        if (label === '70-79') return 'rgba(63, 81, 181, 0.85)';
        if (label === '80-89') return 'rgba(92, 107, 192, 0.85)';
        if (label === '90-99') return 'rgba(121, 134, 203, 0.85)';
        return 'rgba(159, 168, 218, 0.85)';
    });

    new Chart(document.getElementById('ageChart'), {
        type: 'bar',
        data: {
            labels: ageLabels,
            datasets: [{
                label: 'Seniors',
                data: ageValues,
                backgroundColor: ageColors,
                borderRadius: 8,
                barPercentage: 0.6,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: { top: 20, bottom: 10, left: 10, right: 10 }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' seniors';
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0, font: { size: isCompact ? 10 : 11 } }, grid: { color: 'rgba(0,0,0,0.06)' }, border: { display: false } },
                x: { grid: { display: false }, ticks: { font: { size: isCompact ? 10 : 12, weight: 500 } }, border: { display: false } }
            }
        }
    });
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>
