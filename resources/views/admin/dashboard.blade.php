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

    /* ── Card Panel ── */
    .card-panel {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #E5E7EB;
        padding: 1.5rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        margin-bottom: 1.5rem;
    }

    /* ── Action Buttons ── */
    .action-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 600;
        border: 1px solid #E5E7EB; background: #F9FAFB; color: #374151;
        cursor: pointer; transition: all .15s; text-decoration: none;
    }
    .action-btn:hover { background: #F3F4F6; border-color: #D1D5DB; }
    .action-btn.primary { background: #1A237E; color: #fff; border-color: #1A237E; }
    .action-btn.primary:hover { background: #121858; }

    /* ── Data Table ── */
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
    .data-table th { background: #F8FAFC; color: #6B7280; font-weight: 600; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: .05em; padding: .75rem 1rem; text-align: left;
        border-bottom: 1px solid #E5E7EB; white-space: nowrap; }
    .data-table td { padding: .75rem 1rem; border-bottom: 1px solid #F3F4F6; color: #374151; vertical-align: middle; }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #F8FAFC; }

    /* ── Status Badges ── */
    .badge-status { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px;
        font-size: 0.72rem; font-weight: 700; letter-spacing: .04em; }
    .badge-status.active   { background: #DCFCE7; color: #15803D; }
    .badge-status.inactive { background: #FEE2E2; color: #DC2626; }

    /* ── Reports Summary Grid ── */
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
    }
    .report-stat-box {
        background: #F8FAFC; border: 1px solid #E5E7EB; border-radius: 10px;
        padding: 1rem 1.25rem;
    }
    .report-stat-box .stat-title { font-size: 0.75rem; color: #6B7280; font-weight: 500; margin-bottom: 6px; }
    .report-stat-box .stat-num   { font-size: 1.6rem; font-weight: 700; color: #111827; }
</style>

{{-- Page Header --}}
<header class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none mb-6">
    <div>
        <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Admin Dashboard</h1>
        <p class="text-sm text-slate-500 mt-1 font-medium">MSWDO Silang — System Overview</p>
    </div>
    <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
        <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Loading date...</div>
        <div class="w-11 h-11 rounded-full bg-[#1A237E] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(26,35,126,0.3)] hover:scale-105 select-none" title="Admin: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

{{-- Service Breakdown --}}
<div class="flex justify-between items-center mb-6 flex-wrap gap-2">
    <div>
        <h3 class="text-xl font-bold text-slate-800 m-0 flex items-center gap-2">
            <i data-lucide="grid" class="w-6 h-6 text-indigo-600"></i> Service Breakdown
        </h3>
        <p class="text-sm text-slate-500 m-0 mt-1">Status breakdown across all services</p>
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
    <div style="display:flex; gap:1.5rem;">
        @foreach($topKeys as $serviceName)
        @php $stats = $servicesArray[$serviceName]; @endphp
        <div class="card-panel" style="flex:1; margin-bottom:0; padding:1.5rem; min-height:280px;">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-slate-800 m-0">{{ $serviceName }}</h4>
                <span class="text-sm text-slate-500 font-medium">Total: {{ $stats['total'] }}</span>
            </div>
            <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1rem; align-items:stretch;">
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
    <div style="display:flex; gap:1.5rem;">
        @foreach($bottomKeys as $serviceName)
        @php $stats = $servicesArray[$serviceName]; @endphp
        <div class="card-panel" style="flex:1; margin-bottom:0; padding:1.5rem; min-height:280px;">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-slate-800 m-0">{{ $serviceName }}</h4>
                <span class="text-sm text-slate-500 font-medium">Total: {{ $stats['total'] }}</span>
            </div>
            <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1rem; align-items:stretch;">
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
<div class="card-panel" style="margin-bottom:1.5rem;">
    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="text-xl font-bold text-slate-800 m-0 flex items-center gap-2">
                <i data-lucide="clock" class="w-6 h-6 text-indigo-600"></i> Recent Cases
            </h3>
            <p class="text-sm text-slate-500 m-0 mt-1">Latest updated cases across all services</p>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#F8FAFC; border-bottom:2px solid #E5E7EB;">
                    <th style="padding:1rem; text-align:left; font-weight:600; color:#475569; font-size:0.875rem;">Client</th>
                    <th style="padding:1rem; text-align:left; font-weight:600; color:#475569; font-size:0.875rem;">Service</th>
                    <th style="padding:1rem; text-align:left; font-weight:600; color:#475569; font-size:0.875rem;">Officer</th>
                    <th style="padding:1rem; text-align:left; font-weight:600; color:#475569; font-size:0.875rem;">Status</th>
                    <th style="padding:1rem; text-align:left; font-weight:600; color:#475569; font-size:0.875rem;">Updated</th>
                </tr>
            </thead>
            <tbody>
                @if($recentCases && $recentCases->count() > 0)
                    @foreach($recentCases as $case)
                    <tr style="border-bottom:1px solid #E5E7EB; transition:background-color 0.2s;">
                        <td style="padding:1rem; color:#1E293B; font-size:0.875rem;">{{ $case['client'] }}</td>
                        <td style="padding:1rem; color:#475569; font-size:0.875rem;">{{ $case['service'] }}</td>
                        <td style="padding:1rem; color:#475569; font-size:0.875rem;">{{ $case['officer'] }}</td>
                        <td style="padding:1rem;">
                            @php
                                $statusColor = '#64748B';
                                if($case['status'] === 'Active' || $case['status'] === 'Resolved') {
                                    $statusColor = '#10B981';
                                } elseif($case['status'] === 'Pending') {
                                    $statusColor = '#F59E0B';
                                } elseif($case['status'] === 'Overdue' || $case['status'] === 'Rejected') {
                                    $statusColor = '#EF4444';
                                }
                            @endphp
                            <span style="background:{{ $statusColor }}; color:white; padding:0.25rem 0.75rem; border-radius:9999px; font-size:0.75rem; font-weight:500;">{{ $case['status'] }}</span>
                        </td>
                        <td style="padding:1rem; color:#64748B; font-size:0.875rem;">{{ $case['updated'] }}</td>
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
    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="text-base font-bold text-slate-800 m-0 flex items-center gap-2">
                <i data-lucide="file-bar-chart-2" class="w-5 h-5 text-indigo-600"></i> Reports Summary
            </h3>
            <p class="text-xs text-slate-500 m-0 mt-0.5">Key activity &amp; financial performance indicators</p>
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