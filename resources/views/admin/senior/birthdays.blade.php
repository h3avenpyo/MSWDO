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
            --primary:#1A237E;--primary-hover:#121858;--sidebar-bg:#1A237E;--accent-yellow:#FBC02D;--background:#F5F7FB;--surface:#FFFFFF;--border:#E5E7EB;--text-primary:#111827;--text-secondary:#6B7280;--text-muted:#9CA3AF;--success:#16A34A;--success-bg:#ECFDF5;--danger:#DC2626;--danger-bg:#FEF2F2;--info:#3B82F6;--info-bg:#EEF2FF;--purple:#7C3AED;--purple-bg:#F3E8FF;--shadow:0 4px 6px -1px rgba(0,0,0,.05);--font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);height:100vh;overflow:hidden;}
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

        .main{flex:1;min-width:0;margin-left:260px;padding:32px;max-width:calc(100% - 260px);height:100vh;display:flex;flex-direction:column;overflow:hidden;animation:fadeIn .3s ease;}

        .stat-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:32px;animation:fadeInUp .6s ease-out;flex-shrink:0;}
        @media(max-width:1024px){.stat-cards{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:480px){.stat-cards{grid-template-columns:1fr;}}

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
        .table-scroll{flex:1;overflow-y:auto;}
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

        .form-input{background:var(--background);border:1px solid var(--border);border-radius:10px;padding:10px 14px;color:var(--text-primary);font-size:14px;font-family:var(--font-family);transition:border-color .2s ease,box-shadow .2s ease;width:100%;height:42px;}
        .form-input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}
        .form-input::placeholder{color:var(--text-muted);}

        .form-select{background:var(--background);border:1px solid var(--border);border-radius:10px;padding:10px 14px;color:var(--text-primary);font-size:14px;font-family:var(--font-family);transition:border-color .2s ease,box-shadow .2s ease;width:100%;height:42px;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px;}
        .form-select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,35,126,.08);}

        .form-label{font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px;display:block;}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;border:none;border-radius:10px;font-family:var(--font-family);font-size:14px;font-weight:600;cursor:pointer;transition:all .15s ease;padding:10px 20px;}
        .btn svg{width:16px;height:16px;}
        .btn-primary{background:var(--primary);color:white;}
        .btn-primary:hover{background:var(--primary-hover);}
        .btn-ghost{background:transparent;border:1px solid var(--border);color:var(--text-secondary);}
        .btn-ghost:hover{border-color:var(--primary);color:var(--primary);}
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
        .modal-box{background:var(--surface);border-radius:16px;width:100%;max-width:800px;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);}
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

        @media(max-width:768px){
            .sidebar{transform:translateX(-100%);}
            .sidebar.show{transform:translateX(0);}
            .main{margin-left:0;max-width:100%;padding:16px;height:100vh;}
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
            <li><a href="/admin/senior/birthday-payouts"><i data-lucide="banknote" style="width:20px;height:20px"></i> Birthday Payouts</a></li>
            <li><a href="/admin/senior/birthday-payouts/history"><i data-lucide="history" style="width:20px;height:20px"></i> Payout History</a></li>
            <li><a href="/admin/senior/statistics"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i> Statistics</a></li>
            <li><a href="/admin/senior/reports"><i data-lucide="file-text" style="width:20px;height:20px"></i> Reports</a></li>
            <li><a href="/admin/senior/archive"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
        </ul>
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

        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8 flex-shrink-0"
                style="margin-top:-32px;margin-left:-32px;margin-right:-32px;margin-bottom:24px">
            <div class="flex items-center"><h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Birthday Beneficiaries</h1></div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">{{ $initials }}</div>
            </div>
        </header>

        {{-- Stat Cards --}}
        <div class="stat-cards">
            <div class="stat-card stat-card-red" onclick="applyFilter('today')">
                <div class="stat-card-content">
                    <div class="stat-card-label">TODAY'S BIRTHDAYS</div>
                    <div class="stat-card-value" id="statToday">{{ $todayCount }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="cake"></i></div>
            </div>
            <div class="stat-card stat-card-orange" onclick="applyFilter('week')">
                <div class="stat-card-content">
                    <div class="stat-card-label">THIS WEEK</div>
                    <div class="stat-card-value" id="statWeek">{{ $weekCount }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="calendar-days"></i></div>
            </div>
            <div class="stat-card stat-card-blue" onclick="applyFilter('nextmonth')">
                <div class="stat-card-content">
                    <div class="stat-card-label">NEXT MONTH</div>
                    <div class="stat-card-value" id="statNextMonth">{{ $nextMonthCount }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="calendar"></i></div>
            </div>
            <div class="stat-card stat-card-green" onclick="applyFilter('all')">
                <div class="stat-card-content">
                    <div class="stat-card-label">TOTAL (30 DAYS)</div>
                    <div class="stat-card-value" id="statTotal">{{ $total }}</div>
                </div>
                <div class="stat-card-icon"><i data-lucide="users"></i></div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="filter-section">
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px" id="filterChips">
                <span class="filter-chip active" data-filter="all" onclick="applyFilter('all')"><i data-lucide="calendar"></i> This Month</span>
                <span class="filter-chip" data-filter="today" onclick="applyFilter('today')"><i data-lucide="cake"></i> Today</span>
                <span class="filter-chip" data-filter="week" onclick="applyFilter('week')"><i data-lucide="calendar-days"></i> This Week</span>
                <span class="filter-chip" data-filter="nextmonth" onclick="applyFilter('nextmonth')"><i data-lucide="calendar-clock"></i> Next Month</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;align-items:end">
                <div>
                    <label class="form-label">Search</label>
                    <input type="text" id="searchInput" class="form-input" placeholder="Name, control no., barangay..." onkeyup="debounceSearch()">
                </div>
                <div>
                    <label class="form-label">Barangay</label>
                    <select id="barangayFilter" class="form-select" onchange="loadData()">
                        <option value="">All Barangays</option>
                        @foreach($barangays as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Month</label>
                    <select id="monthFilter" class="form-select" onchange="loadData()">
                        <option value="">All Months</option>
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Sort By</label>
                    <select id="sortField" class="form-select" onchange="loadData()">
                        <option value="birth_date">Birthday</option>
                        <option value="full_name">Last Name</option>
                        <option value="barangay">Barangay</option>
                        <option value="control_number">Control No.</option>
                        <option value="age">Age</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Order</label>
                    <select id="sortDir" class="form-select" onchange="loadData()">
                        <option value="asc">Asc</option>
                        <option value="desc">Desc</option>
                    </select>
                </div>
                <div style="display:flex;gap:8px">
                    <button class="btn btn-primary" style="flex:1;height:42px" onclick="loadData()"><i data-lucide="search"></i> Search</button>
                    <button class="btn btn-ghost" style="height:42px;width:42px;padding:0" onclick="resetFilters()"><i data-lucide="x"></i></button>
                </div>
            </div>
        </div>

        {{-- Action Bar --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-shrink:0">
            <div style="display:flex;align-items:center;gap:12px">
                <span style="font-size:13px;color:var(--text-secondary);font-weight:500" id="resultCount">Loading...</span>
                <select class="form-select" style="width:auto;min-width:100px;height:34px;font-size:13px" onchange="loadData()" id="perPageSelect">
                    <option value="15">15 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
            <div class="view-toggle">
                <button class="active" id="viewTable" onclick="setView('table')"><i data-lucide="table-2"></i></button>
                <button id="viewGrouped" onclick="setView('grouped')"><i data-lucide="layout-list"></i></button>
            </div>
        </div>

        {{-- Table View --}}
        <div class="table-card" id="tableView" style="flex:1;display:flex;flex-direction:column;min-height:0;overflow:hidden">
            <div class="table-wrapper">
                <div class="table-scroll" style="flex:1;overflow-y:auto;min-height:0">
                    <table id="birthdayTable">
                        <thead>
                            <tr>
                                <th style="width:4%">#</th>
                                <th style="width:11%">Control No.</th>
                                <th style="width:5%">ID</th>
                                <th style="width:18%">Full Name</th>
                                <th style="width:10%">Birth Date</th>
                                <th style="width:7%">Age</th>
                                <th style="width:7%">Turning</th>
                                <th style="width:10%">Barangay</th>
                                <th style="width:11%">Contact</th>
                                <th style="width:10%">Countdown</th>
                                <th style="width:7%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr><td colspan="11" style="text-align:center;padding:40px 0;color:var(--text-muted)">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="paginationWrapper" style="padding:16px 20px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:13px;color:var(--text-muted)" id="paginationInfo"></span>
                    <ul class="pagination" id="paginationLinks"></ul>
                </div>
            </div>
        </div>

        {{-- Barangay Grouped View --}}
        <div class="table-card" id="groupedView" style="display:none;padding:0;flex:1;min-height:0;overflow:hidden">
            <div id="groupedContent" style="padding:24px;overflow-y:auto;flex:1">
                <div style="text-align:center;padding:40px 0;color:var(--text-muted)">Loading...</div>
            </div>
        </div>
    </div>
</div>

{{-- Profile Modal --}}
<div class="modal-overlay" id="profileModal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="modal-box" style="max-width:900px">
        <div class="modal-header-bar">
            <h4><i data-lucide="user-circle"></i> Beneficiary Profile</h4>
            <button class="modal-close-btn" onclick="document.getElementById('profileModal').classList.remove('show')"><i data-lucide="x"></i></button>
        </div>
        <div class="modal-body-scroll">
            <div id="profileContent" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div style="text-align:center;padding:40px 0"><div class="spinner"></div></div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentFilter = 'all';
    let currentView = 'table';
    let searchTimeout;

    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('show'); }

    function updateDateTime() {
        const n = new Date();
        const el = document.getElementById('currentDateTime');
        if (el) el.textContent = n.toLocaleDateString('en-PH', { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' });
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

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
            tbody.innerHTML = `<tr><td colspan="11" style="text-align:center;padding:40px 0;color:var(--text-muted)"><i data-lucide="calendar-check" style="width:40px;height:40px;display:block;margin:0 auto 12px;opacity:.3"></i>No birthday beneficiaries found.</td></tr>`;
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

            return `<tr>
                <td style="color:var(--text-muted);font-weight:600">${i + 1}</td>
                <td><strong style="font-size:13px">${s.control_number}</strong></td>
                <td><span style="font-size:12px;color:var(--text-secondary)">${s.osca_id}</span></td>
                <td><div style="display:flex;align-items:center;gap:8px"><span class="avatar-circle">${initial}</span><strong>${s.full_name}</strong></div></td>
                <td><span style="font-size:13px">${s.birth_date_formatted}</span></td>
                <td><strong>${s.current_age}</strong></td>
                <td><span class="badge" style="background:var(--info-bg);color:var(--info)">${s.age_turning}</span></td>
                <td>${s.barangay !== '-' ? `<span class="badge" style="background:var(--info-bg);color:var(--info);font-weight:500">${s.barangay}</span>` : `<span style="color:var(--text-muted)">-</span>`}</td>
                <td>${s.contact_number !== '-' ? `<a href="tel:${s.contact_number}" style="color:var(--primary);text-decoration:none;font-size:13px">${s.contact_number}</a>` : `<span style="color:var(--text-muted)">-</span>`}</td>
                <td>${countdownHtml}</td>
                <td><button class="btn btn-primary btn-sm" style="padding:6px 10px" onclick="viewProfile(${s.id})"><i data-lucide="eye"></i></button></td>
            </tr>`;
        }).join('');
        lucide.createIcons();
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
                                        return `<tr><td style="color:var(--text-muted)">${i + 1}</td><td><strong>${s.full_name}</strong></td><td>${s.birth_date}</td><td>${cd}</td></tr>`;
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

    loadData();
    lucide.createIcons();
</script>

<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
</body>
</html>