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
            --shadow:0 4px 6px -1px rgba(0,0,0,.05);
            --shadow-hover:0 10px 25px rgba(0,0,0,.1);
            --font-family:'Public Sans',-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;}
        html,body{margin:0;padding:0;background:var(--background);color:var(--text-primary);font-family:var(--font-family);height:100vh;overflow-x:hidden;}
        body{font-size:14px;line-height:1.5;}
        h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
        button{font-family:inherit;cursor:pointer;}
        .app{display:flex;min-height:100vh;}

        /* Sidebar */
        .sidebar{width:var(--sidebar-width);flex-shrink:0;background:var(--primary);color:#FFF;position:fixed;left:0;top:0;height:100vh;z-index:1000;display:flex;flex-direction:column;transition:transform .3s ease;}
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
        .main{flex:1;min-width:0;margin-left:var(--sidebar-width);padding:var(--content-padding);max-width:calc(100% - var(--sidebar-width));min-height:100vh;display:flex;flex-direction:column;overflow-y:auto;animation:fadeIn .3s ease;}

        /* Dashboard Grid */
        .dashboard-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:32px;}
        @media(max-width:1024px){.dashboard-grid{grid-template-columns:1fr;}}

        /* Stat Cards */
        .stat-cards{display:grid;grid-template-columns:repeat(5,1fr);gap:20px;margin-bottom:32px;animation:fadeInUp .6s ease-out;}
        @media(max-width:1024px){.stat-cards{grid-template-columns:repeat(3,1fr);}}
        @media(max-width:768px){.stat-cards{grid-template-columns:1fr 1fr;}}
        @media(max-width:480px){.stat-cards{grid-template-columns:1fr;}}

        .stat-card{background:var(--surface);border-radius:16px;padding:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;position:relative;overflow:hidden;}
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
        .analytics-card{background:var(--surface);border-radius:16px;padding:24px;box-shadow:var(--shadow);border:1px solid var(--border);min-height:420px;animation:fadeInUp .6s ease-out .1s backwards;}
        .analytics-card h3{font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:20px;}

        /* Activity Card */
        .activity-card{background:var(--surface);border-radius:16px;padding:24px;box-shadow:var(--shadow);border:1px solid var(--border);min-height:420px;animation:fadeInUp .6s ease-out .2s backwards;}
        .activity-card h3{font-size:14px;font-weight:600;color:var(--text-primary);margin-bottom:20px;}
        .activity-feed{max-height:340px;overflow-y:auto;padding-right:8px;}
        .activity-feed::-webkit-scrollbar{width:6px;}
        .activity-feed::-webkit-scrollbar-track{background:var(--background);border-radius:3px;}
        .activity-feed::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px;}
        .activity-feed::-webkit-scrollbar-thumb:hover{background:var(--text-muted);}

        .activity-item{display:flex;gap:14px;padding:14px;border-radius:12px;background:var(--background);margin-bottom:10px;transition:all .2s ease;}
        .activity-item:last-child{margin-bottom:0;}
        .activity-item:hover{transform:translateX(4px);background:var(--surface);box-shadow:0 2px 8px rgba(0,0,0,.04);}
        .activity-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .activity-icon svg{width:18px;height:18px;}
        .activity-content{flex:1;min-width:0;}
        .activity-text{font-size:13px;font-weight:500;color:var(--text-primary);margin-bottom:2px;line-height:1.4;}
        .activity-time{font-size:11px;color:var(--text-muted);}

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

        /* ── Sidebar Overlay ── */
        .sidebar-overlay.active { display: block !important; }

        /* ── Responsive: Tablet (< 1024px) ── */
        @media (max-width: 1023px) {
            .sidebar { transform: translateX(-100%) !important; z-index: 1001 !important; }
            .sidebar.show { transform: translateX(0) !important; }
            .main, .main-content { margin-left: 0 !important; max-width: 100% !important; }
            .main { padding: 16px !important; }
            .stat-cards { grid-template-columns: repeat(2, 1fr) !important; }
            .dashboard-grid { grid-template-columns: 1fr !important; }
        }

        /* ── Responsive: Mobile (< 768px) ── */
        @media (max-width: 767px) {
            .main, .main-content { padding: 12px !important; }
            .stat-cards { grid-template-columns: 1fr !important; }
            .topnav, .top-navbar { padding: 10px 12px !important; }
            .topnav-datetime, .navbar-datetime { display: none !important; }
            .filter-bar, .filter-group { flex-direction: column; }
            .filter-bar > div, .filter-group > div { width: 100% !important; min-width: 0 !important; }
        }

        /* ── Responsive: Small Mobile (< 480px) ── */
        @media (max-width: 479px) {
            .stat-card-icon { width: 40px !important; height: 40px !important; }
            .stat-card-value { font-size: 24px !important; }
            .stat-cards { gap: 12px !important; }
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
            <li><a href="/admin/senior" class="active"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
            <li><a href="/admin/senior/registration"><i data-lucide="user-plus" style="width:20px;height:20px"></i> Registration</a></li>
            <li><a href="/admin/senior/masterlist"><i data-lucide="list" style="width:20px;height:20px"></i> Masterlist</a></li>
            <li><a href="/admin/senior/birthdays"><i data-lucide="cake" style="width:20px;height:20px"></i> Birthday Beneficiaries</a></li>
            <li><a href="/admin/senior/birthday-payouts"><i data-lucide="banknote" style="width:20px;height:20px"></i> Birthday Payouts</a></li>
            <li><a href="/admin/senior/birthday-payouts/history"><i data-lucide="history" style="width:20px;height:20px"></i> Payout History</a></li>
            <li><a href="/admin/senior/statistics"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i> Statistics</a></li>
            <li><a href="/admin/senior/reports"><i data-lucide="file-text" style="width:20px;height:20px"></i> Reports</a></li>
            <li><a href="/admin/senior/archive"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;"></div>

    <!-- Main Content -->
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
            use App\Models\Senior\SeniorCitizenRecord;
            $bdayToday = SeniorCitizenRecord::where('status','active')->whereNotNull('birth_date')->whereRaw("MONTH(birth_date) = ? AND DAY(birth_date) = ?", [now()->format('n'), now()->format('j')])->count();
            $bdayWeek = SeniorCitizenRecord::where('status','active')->whereNotNull('birth_date')->where(function($q){ $s=now();$e=now()->addDays(7);$sMD=$s->format('m-d');$eMD=$e->format('m-d');if($sMD<=$eMD){$q->whereRaw("DATE_FORMAT(birth_date,'%m-%d') BETWEEN ? AND ?",[$sMD,$eMD]);}else{$q->whereRaw("DATE_FORMAT(birth_date,'%m-%d') >= ?",[$sMD])->orWhereRaw("DATE_FORMAT(birth_date,'%m-%d') <= ?",[$eMD]);}})->count();
            $bdayNextMonth = SeniorCitizenRecord::where('status','active')->whereNotNull('birth_date')->whereRaw("MONTH(birth_date) = ?", [now()->addMonth()->format('n')])->count();
        @endphp

        <!-- Page Header -->
        <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8"
                style="margin-top: calc(-1 * var(--content-padding)); margin-left: calc(-1 * var(--content-padding)); margin-right: calc(-1 * var(--content-padding));">
            <div class="flex items-center">
                <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Senior Citizen Dashboard</h1>
            </div>
            <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
                <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime"></div>
                <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                    {{ $initials }}
                </div>
            </div>
        </header>

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
                    <button class="text-xs font-semibold px-3 py-1 rounded-lg" style="background:var(--info-bg);color:var(--icon-blue);border:none" onclick="document.getElementById('barangayModal').style.display='flex'">View All</button>
                </div>
                <div style="display:flex;align-items:center;gap:24px">
                    <div style="width:280px;height:280px;flex-shrink:0"><canvas id="barangayDonut"></canvas></div>
                    <div id="barangayLegend" style="flex:1;min-width:0;max-height:220px;overflow-y:auto"></div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="activity-card">
                <div class="flex items-center justify-between mb-5">
                    <h3><i data-lucide="activity" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;color:var(--icon-blue)"></i>Recent Activities</h3>
                    <button class="text-xs font-semibold px-3 py-1 rounded-lg" style="background:var(--danger-bg);color:var(--danger);border:none" onclick="confirmClearActivities()">Clear</button>
                </div>
                <div class="activity-feed">
                    @if(count($recentActivities) > 0)
                        @foreach($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="activity-icon" style="background:var(--info-bg);color:var(--icon-blue)">
                                <i data-lucide="{{ $activity['action'] == 'registered' ? 'user-plus' : ($activity['action'] == 'archived' ? 'archive' : ($activity['action'] == 'restored' ? 'undo-2' : 'id-card')) }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-text">{{ ucfirst($activity['action']) }} <strong>{{ $activity['name'] }}</strong></div>
                                <div class="activity-time">{{ $activity['identifier'] }} &middot; {{ $activity['timestamp'] }}</div>
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
        const opts={weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'numeric',minute:'2-digit',hour12:true};
        document.getElementById('currentDateTime').textContent=now.toLocaleDateString('en-US',opts).replace(',',' at');
    }
    updateDateTime();
    setInterval(updateDateTime,60000);

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
</script>
</body>
</html>
