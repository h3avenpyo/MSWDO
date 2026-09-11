<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>In-Between Birthday Cash Gift - Senior Citizen</title>
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
        .app{display:flex;min-height:100vh;flex-direction:row;}
        .stat-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:24px;}
        .stat-card{background:var(--surface);border-radius:24px;padding:20px;display:flex;align-items:center;justify-content:space-between;box-shadow:var(--shadow);border:1px solid var(--border);transition:all .3s ease;}
        .stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);}
        .stat-card-label{font-size:12px;color:var(--text-secondary);font-weight:500;margin-bottom:4px;}
        .stat-card-value{font-size:28px;font-weight:700;color:var(--text-primary);}
        .stat-card-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;}
        .stat-card-blue .stat-card-icon{background:var(--info-bg);color:var(--icon-blue);}
        .stat-card-green .stat-card-icon{background:var(--success-bg);color:var(--icon-green);}
        .stat-card-purple .stat-card-icon{background:var(--purple-bg);color:var(--icon-purple);}
        .stat-card-orange .stat-card-icon{background:var(--accent-yellow);color:white;}
        .analytics-card{background:var(--surface);border-radius:16px;padding:24px;box-shadow:var(--shadow);border:1px solid var(--border);margin-bottom:20px;}
        .dashboard-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-bottom:20px;}
        .quick-actions-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;}
        .quick-action-card{background:var(--surface);border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:var(--shadow);border:1px solid var(--border);text-decoration:none;color:inherit;transition:all .3s ease;}
        .quick-action-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-hover);}
        .quick-action-icon{width:48px;height:48px;border-radius:12px;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .quick-action-title{font-weight:600;font-size:16px;margin-bottom:4px;}
        .quick-action-description{font-size:13px;color:var(--text-secondary);}
        .page-header{margin-bottom:24px;}
        .page-header h1{font-size:28px;font-weight:700;margin:0 0 8px 0;color:var(--text-primary);}
        .page-header p{margin:0;color:var(--text-secondary);}
    </style>
</head>
<body>
<div class="app">
    @include('admin.senior.partials.navigation', ['active' => 'in-between', 'mobileSubtitle' => 'In-Between Benefits'])
    
    <div class="main">
        <div class="main-scroll">
            <div class="page-header">
                <h1>In-Between Birthday Cash Gift</h1>
                <p class="text-gray-600">Municipality of Silang - Senior Citizen Benefits</p>
            </div>

            <!-- Stat Cards -->
            <div class="stat-cards">
                <div class="stat-card stat-card-blue">
                    <div class="stat-card-content">
                        <div class="stat-card-label">TOTAL ELIGIBLE</div>
                        <div class="stat-card-value">{{ $totalEligible }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="users"></i></div>
                </div>
                <div class="stat-card stat-card-green">
                    <div class="stat-card-content">
                        <div class="stat-card-label">CLAIMED</div>
                        <div class="stat-card-value">{{ $totalClaimed }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="check-circle"></i></div>
                </div>
                <div class="stat-card stat-card-purple">
                    <div class="stat-card-content">
                        <div class="stat-card-label">PENDING</div>
                        <div class="stat-card-value">{{ $totalPending }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="clock"></i></div>
                </div>
                <div class="stat-card stat-card-orange">
                    <div class="stat-card-content">
                        <div class="stat-card-label">AMOUNT RELEASED</div>
                        <div class="stat-card-value">₱{{ number_format($totalAmountReleased, 2) }}</div>
                    </div>
                    <div class="stat-card-icon"><i data-lucide="wallet"></i></div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Upcoming Eligible Seniors -->
                <div class="analytics-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Upcoming Eligible Seniors</h3>
                        <a href="/admin/senior/in-between/eligibility-list" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                    </div>
                    @if($upcomingEligible->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingEligible as $senior)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium">{{ $senior->full_name }}</div>
                                        <div class="text-sm text-gray-600">Age: {{ $senior->age }} | {{ $senior->barangay }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-green-600">Eligible</div>
                                        <div class="text-xs text-gray-500">{{ $senior->birth_date->format('M d') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">No upcoming eligible seniors</div>
                    @endif
                </div>

                <!-- Approaching Next Interval -->
                <div class="analytics-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Approaching Next Interval</h3>
                        <a href="/admin/senior/in-between/eligibility-list" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                    </div>
                    @if($approachingNextInterval->count() > 0)
                        <div class="space-y-3">
                            @foreach($approachingNextInterval as $senior)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium">{{ $senior->full_name }}</div>
                                        <div class="text-sm text-gray-600">Current Age: {{ $senior->age }} | {{ $senior->barangay }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-blue-600">Next Interval</div>
                                        <div class="text-xs text-gray-500">{{ $senior->birth_date->addYear($senior->age + 1)->age }} years</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">No seniors approaching next interval</div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions-grid">
                <a href="/admin/senior/in-between/eligibility-list" class="quick-action-card">
                    <div class="quick-action-icon">
                        <i data-lucide="users"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Eligibility List</div>
                        <div class="quick-action-description">View all eligible seniors</div>
                    </div>
                </a>
                <a href="/admin/senior/in-between/history" class="quick-action-card">
                    <div class="quick-action-icon">
                        <i data-lucide="history"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Benefit History</div>
                        <div class="quick-action-description">View all benefit transactions</div>
                    </div>
                </a>
                <a href="/admin/senior/in-between/reports" class="quick-action-card">
                    <div class="quick-action-icon">
                        <i data-lucide="bar-chart-3"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Reports</div>
                        <div class="quick-action-description">Generate benefit reports</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>
