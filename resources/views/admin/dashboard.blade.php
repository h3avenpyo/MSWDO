@extends('admin.layout')
@section('title', 'MSWDO Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@section('content')
@php
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', $adminName);
$initials = count($words) >= 2
    ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
    : strtoupper(substr($adminName,0,2));
@endphp

<style>

    /* ── Modern Dashboard Base ── */
    .dashboard-container {
        background: #F8FAFC;
        min-height: 100vh;
        padding: 2rem;
    }

    /* ── Modern Page Header ── */
    .page-header {
        background: #1E3A8A;
        border-radius: 16px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
    }

    /* ── Card Panel ── */
    .card-panel {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        border-left: 4px solid #1E3A8A;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        transition: box-shadow 0.2s ease;
    }
    .card-panel:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* ── Section Headers ── */
    .section-header {
        display: flex;
        justify-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1E3A8A;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }
    .section-subtitle {
        font-size: 0.95rem;
        color: #64748B;
        margin: 0.5rem 0 0 0;
        font-weight: 500;
    }

    /* ── Action Buttons ── */
    .action-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 600;
        border: 1px solid #E5E7EB; background: #ffffff;
        color: #374151; cursor: pointer; transition: all 0.2s ease;
        text-decoration: none;
    }
    .action-btn:hover { 
        background: #EFF6FF;
        border-color: #1E3A8A;
        color: #1E3A8A;
    }
    .action-btn.primary { 
        background: #1E3A8A;
        color: #fff;
        border-color: #1E3A8A;
    }
    .action-btn.primary:hover { 
        background: #1E40AF;
    }

    /* ── Data Table ── */
    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.875rem; }
    .data-table th { 
        background: #EFF6FF;
        color: #1E3A8A; font-weight: 600; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.05em; padding: 1rem 1.25rem; 
        text-align: left; white-space: nowrap;
        border-bottom: 2px solid #1E3A8A;
    }
    .data-table th:first-child { border-radius: 8px 0 0 0; }
    .data-table th:last-child { border-radius: 0 8px 0 0; }
    .data-table td { 
        padding: 1rem 1.25rem; border-bottom: 1px solid #E5E7EB; 
        color: #374151; vertical-align: middle; background: #ffffff;
        white-space: nowrap;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #EFF6FF; }

    /* ── Scrollable Table Container ── */
    .table-scroll-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #1E3A8A #EFF6FF;
    }
    .table-scroll-container::-webkit-scrollbar {
        height: 8px;
    }
    .table-scroll-container::-webkit-scrollbar-track {
        background: #EFF6FF;
        border-radius: 4px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb {
        background: #1E3A8A;
        border-radius: 4px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #1E40AF;
    }

    /* ── Status Badges ── */
    .badge-status { 
        display: inline-flex; align-items: center; padding: 4px 12px; 
        border-radius: 6px; font-size: 0.75rem; font-weight: 600;
    }
    .badge-status.active   { 
        background: #EFF6FF;
        color: #1E3A8A;
    }
    .badge-status.inactive { 
        background: #FEE2E2;
        color: #DC2626;
    }

    /* ── Reports Summary Grid ── */
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.25rem;
    }
    .report-stat-box {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 12px;
        padding: 1.5rem;
        transition: box-shadow 0.2s ease;
    }
    .report-stat-box:hover {
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
    }
    .report-stat-box .stat-title { 
        font-size: 0.75rem; color: #64748B; font-weight: 600; 
        margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .report-stat-box .stat-num   { 
        font-size: 1.75rem; font-weight: 700; color: #1E3A8A;
    }

    /* ── Service Stat Boxes ── */
    .sb-stats-grid > div {
        background: #EFF6FF !important;
        border: 1px solid #BFDBFE !important;
        border-radius: 12px !important;
        padding: 1.25rem 1.5rem !important;
        transition: box-shadow 0.2s ease;
        position: relative;
    }
    .sb-stats-grid > div:hover {
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
    }
    .sb-stats-grid > div .text-sm {
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        color: #64748B !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem !important;
    }
    .sb-stats-grid > div .text-2xl {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: #1E3A8A !important;
    }

    /* ── Responsive Breakpoints ── */
    @media (max-width: 1024px) {
        .dashboard-container {
            padding: 1.5rem;
        }
        .page-header {
            padding: 1.5rem 2rem;
        }
        .service-breakdown-row {
            flex-direction: column !important;
        }
        .service-breakdown-row > .card-panel {
            width: 100% !important;
            min-height: auto !important;
            min-width: 0 !important;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
        .page-header {
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
        }
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .section-title {
            font-size: 1.25rem;
        }
        .service-breakdown-row {
            flex-direction: column !important;
            gap: 1rem !important;
        }
        .service-breakdown-row > .card-panel {
            width: 100% !important;
            min-height: auto !important;
            min-width: 0 !important;
            padding: 1.5rem !important;
            border-radius: 12px !important;
        }
        .service-breakdown-row .sb-stats-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
        .service-breakdown-row .sb-stats-grid > div {
            height: auto !important;
            padding: 1.25rem !important;
        }
        .reports-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 1rem !important;
        }
        .card-panel table {
            font-size: 0.8rem;
        }
        .card-panel table th,
        .card-panel table td {
            padding: 0.75rem 1rem !important;
        }
        .action-btn {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 480px) {
        .page-header {
            padding: 1rem 1.25rem;
        }
        .section-title {
            font-size: 1.125rem;
        }
        .reports-grid {
            grid-template-columns: 1fr !important;
        }
        .card-panel table th:nth-child(4),
        .card-panel table td:nth-child(4) {
            display: none;
        }
    }
</style>

{{-- Page Header --}}
<header class="page-header flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none">
    <div>
        <h1 class="font-['Public_Sans'] text-[28px] md:text-[32px] lg:text-[36px] font-bold text-white leading-none m-0">Admin Dashboard</h1>
        <p class="text-sm md:text-base text-white/90 mt-2 font-medium">MSWDO Silang — System Overview</p>
    </div>
    <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
        <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-white/90" id="currentDateTime">Loading date...</div>
        <div class="w-12 h-12 rounded-full bg-white/20 text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:bg-white/30 select-none" title="Admin: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

{{-- Service Breakdown --}}
<div class="section-header">
    <div>
        <h3 class="section-title">
            <i data-lucide="grid" class="w-7 h-7"></i> Service Breakdown
        </h3>
        <p class="section-subtitle">Status breakdown across all services</p>
    </div>
</div>

<div style="display:flex; flex-direction:column; gap:1.5rem; margin-bottom:2rem;">
    @php
        $servicesArray = $serviceBreakdown instanceof \Illuminate\Support\Collection ? $serviceBreakdown->toArray() : $serviceBreakdown;
        $serviceKeys = array_keys($servicesArray);
        $topKeys = array_slice($serviceKeys, 0, 3);
        $bottomKeys = array_slice($serviceKeys, 3, 2);
    @endphp

    <!-- Top row: 3 cards -->
    <div class="service-breakdown-row" style="display:flex; gap:1.5rem; flex-wrap:wrap;">
        @foreach($topKeys as $serviceName)
        @php $stats = $servicesArray[$serviceName]; @endphp
        <div class="card-panel" style="flex:1; min-width:300px; margin-bottom:0; padding:2rem; min-height:300px;">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-xl font-bold text-slate-800 m-0">{{ $serviceName }}</h4>
                <span class="text-sm font-medium bg-[#EFF6FF] text-[#1E3A8A] px-3 py-1 rounded-full">Total: {{ $stats['total'] }}</span>
            </div>
            <div class="sb-stats-grid" style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.25rem; align-items:stretch;">
                @php
                    $label1 = 'Active';
                    $label2 = 'Pending';
                    $label3 = 'Overdue';
                    $label4 = 'Completed';

                    if ($serviceName === 'Social Case Study') {
                        $label1 = 'Total Clients';
                        $label2 = 'Cases This Month';
                        $label3 = 'Released Today';
                        $label4 = 'Total Released';
                    } elseif ($serviceName === 'Financial Assistance') {
                        $label1 = 'Total Intakes';
                        $label2 = 'Pending';
                        $label3 = 'Step 1 Approved';
                        $label4 = 'Ready Step 2';
                    } elseif ($serviceName === 'Senior Citizen') {
                        $label1 = 'Total Seniors';
                        $label2 = 'Active Seniors';
                        $label3 = 'Archived';
                        $label4 = 'Total Payout';
                    } elseif ($serviceName === 'VAWC' || $serviceName === 'BCPC') {
                        $label1 = 'Total Cases';
                        $label2 = 'Active Cases';
                        $label3 = 'Overdue';
                        $label4 = 'Resolved';
                    }
                @endphp
                <div style="background:#F8FAFC; border:1px solid #E5E7EB; border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; justify-content:center; height:100px;">
                    <div class="text-sm text-slate-500 mb-2 font-medium">{{ $label1 }}</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['active'] }}</div>
                </div>
                <div style="background:#F8FAFC; border:1px solid #E5E7EB; border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; justify-content:center; height:100px;">
                    <div class="text-sm text-slate-500 mb-2 font-medium">{{ $label2 }}</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['pending'] }}</div>
                </div>
                <div style="background:#F8FAFC; border:1px solid #E5E7EB; border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; justify-content:center; height:100px;">
                    <div class="text-sm text-slate-500 mb-2 font-medium">{{ $label3 }}</div>
                    <div class="text-2xl font-bold @if($stats['overdue'] > 0) text-red-600 @else text-slate-800 @endif">{{ $stats['overdue'] }}</div>
                </div>
                <div style="background:#F8FAFC; border:1px solid #E5E7EB; border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; justify-content:center; height:100px;">
                    <div class="text-sm text-slate-500 mb-2 font-medium">{{ $label4 }}</div>
                    <div class="text-2xl font-bold text-green-600">
                        @if($serviceName === 'Senior Citizen')
                            ₱{{ number_format($stats['completed'], 2) }}
                        @else
                            {{ $stats['completed'] }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Bottom row: 2 cards with full width -->
    <div class="service-breakdown-row" style="display:flex; gap:1.5rem; flex-wrap:wrap;">
        @foreach($bottomKeys as $serviceName)
        @php $stats = $servicesArray[$serviceName]; @endphp
        <div class="card-panel" style="flex:1; min-width:300px; margin-bottom:0; padding:2rem; min-height:300px;">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-xl font-bold text-slate-800 m-0">{{ $serviceName }}</h4>
                <span class="text-sm font-medium bg-[#EFF6FF] text-[#1E3A8A] px-3 py-1 rounded-full">Total: {{ $stats['total'] }}</span>
            </div>
            <div class="sb-stats-grid" style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.25rem; align-items:stretch;">
                @php
                    $label1 = 'Active';
                    $label2 = 'Pending';
                    $label3 = 'Overdue';
                    $label4 = 'Completed';

                    if ($serviceName === 'Social Case Study') {
                        $label1 = 'Total Clients';
                        $label2 = 'Cases This Month';
                        $label3 = 'Released Today';
                        $label4 = 'Total Released';
                    } elseif ($serviceName === 'Financial Assistance') {
                        $label1 = 'Total Intakes';
                        $label2 = 'Pending';
                        $label3 = 'Step 1 Approved';
                        $label4 = 'Ready Step 2';
                    } elseif ($serviceName === 'Senior Citizen') {
                        $label1 = 'Total Seniors';
                        $label2 = 'Active Seniors';
                        $label3 = 'Archived';
                        $label4 = 'Total Payout';
                    } elseif ($serviceName === 'VAWC' || $serviceName === 'BCPC') {
                        $label1 = 'Total Cases';
                        $label2 = 'Active Cases';
                        $label3 = 'Overdue';
                        $label4 = 'Resolved';
                    }
                @endphp
                <div style="background:#F8FAFC; border:1px solid #E5E7EB; border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; justify-content:center; height:100px;">
                    <div class="text-sm text-slate-500 mb-2 font-medium">{{ $label1 }}</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['active'] }}</div>
                </div>
                <div style="background:#F8FAFC; border:1px solid #E5E7EB; border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; justify-content:center; height:100px;">
                    <div class="text-sm text-slate-500 mb-2 font-medium">{{ $label2 }}</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['pending'] }}</div>
                </div>
                <div style="background:#F8FAFC; border:1px solid #E5E7EB; border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; justify-content:center; height:100px;">
                    <div class="text-sm text-slate-500 mb-2 font-medium">{{ $label3 }}</div>
                    <div class="text-2xl font-bold @if($stats['overdue'] > 0) text-red-600 @else text-slate-800 @endif">{{ $stats['overdue'] }}</div>
                </div>
                <div style="background:#F8FAFC; border:1px solid #E5E7EB; border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; justify-content:center; height:100px;">
                    <div class="text-sm text-slate-500 mb-2 font-medium">{{ $label4 }}</div>
                    <div class="text-2xl font-bold text-green-600">
                        @if($serviceName === 'Senior Citizen')
                            ₱{{ number_format($stats['completed'], 2) }}
                        @else
                            {{ $stats['completed'] }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Recent Cases --}}
<div class="card-panel">
    <div class="section-header">
        <div>
            <h3 class="section-title">
                <i data-lucide="clock" class="w-7 h-7"></i> Recent Cases
            </h3>
            <p class="section-subtitle">Latest updated cases across all services</p>
        </div>
    </div>

    <div class="table-scroll-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Service</th>
                    <th>Officer</th>
                    <th>Status</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody>
                @if($recentCases && $recentCases->count() > 0)
                    @foreach($recentCases as $case)
                    <tr>
                        <td>{{ $case['client'] }}</td>
                        <td>{{ $case['service'] }}</td>
                        <td>{{ $case['officer'] }}</td>
                        <td>
                            @php
                                $statusClass = 'badge-status';
                                if($case['status'] === 'Active' || $case['status'] === 'Resolved') {
                                    $statusClass .= ' active';
                                } elseif($case['status'] === 'Pending') {
                                    $statusClass .= ' inactive';
                                } elseif($case['status'] === 'Overdue' || $case['status'] === 'Rejected') {
                                    $statusClass .= ' inactive';
                                }
                            @endphp
                            <span class="{{ $statusClass }}">{{ $case['status'] }}</span>
                        </td>
                        <td>{{ $case['updated'] }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="padding:2rem; text-align:center; color:#94A3B8; font-size:0.875rem;">
                            No recent cases found
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Reports Summary --}}
<div class="card-panel">
    <div class="section-header">
        <div>
            <h3 class="section-title">
                <i data-lucide="file-bar-chart-2" class="w-7 h-7"></i> Reports Summary
            </h3>
            <p class="section-subtitle">Key activity &amp; financial performance indicators</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="action-btn"><i data-lucide="file-text" class="w-4 h-4 text-red-600"></i> PDF</button>
            <button class="action-btn"><i data-lucide="table" class="w-4 h-4 text-emerald-600"></i> Excel</button>
            <button class="action-btn primary"><i data-lucide="bar-chart-3" class="w-4 h-4"></i> Analytics</button>
        </div>
    </div>
    <div class="reports-grid">
        <div class="report-stat-box">
            <div class="stat-title">Cases This Month</div>
            <div class="stat-num">{{ $reportsSummary['casesThisMonth'] ?? 0 }}</div>
        </div>
        <div class="report-stat-box">
            <div class="stat-title">Closed This Month</div>
            <div class="stat-num">{{ $reportsSummary['closedThisMonth'] ?? 0 }}</div>
        </div>
        <div class="report-stat-box">
            <div class="stat-title">Pending Cases</div>
            <div class="stat-num">{{ $reportsSummary['pendingCases'] ?? 0 }}</div>
        </div>
        <div class="report-stat-box">
            <div class="stat-title">Generated Reports</div>
            <div class="stat-num">{{ $reportsSummary['generatedReports'] ?? 0 }}</div>
        </div>
        <div class="report-stat-box">
            <div class="stat-title">Financial Released</div>
            <div class="stat-num">₱{{ number_format($reportsSummary['financialReleased'] ?? 0, 2) }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Date/time
    function updateDateTime() {
        const now = new Date();
        const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true };
        const el = document.getElementById('currentDateTime');
        if (el) el.textContent = now.toLocaleDateString('en-US', opts).replace(',', ' at');
    }
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        // Re-create icons after a short delay to ensure dynamic icons are rendered
        setTimeout(() => lucide.createIcons(), 100);
    }
});
</script>
@endpush