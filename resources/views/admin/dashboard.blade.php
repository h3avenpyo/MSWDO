@extends('admin.social-case.layout')
@section('title', 'MSWDO Admin Dashboard')

@section('content')
@php
$logo = null;
if(file_exists(public_path('images/mswdo-logo.png'))){
    $logo='mswdo-logo.png';
}else{
    $files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);
    if(!empty($files)) $logo=basename($files[0]);
}
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', $adminName);
$initials = count($words) >= 2
    ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
    : strtoupper(substr($adminName,0,2));
@endphp

{{-- Mobile Header --}}
<div class="mobile-header">
    <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
    <div class="mobile-header-brand">
        <div class="mobile-brand-text">
            <h1 class="mobile-brand-title">MSWDO SILANG</h1>
            <p class="mobile-brand-subtitle">Admin Dashboard</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<style>
    .main { padding-top: 14px !important; }
    @media (max-width: 767.98px) { .main { padding-top: 72px !important; } }
    .main > header { margin-top: 0 !important; padding-top: 0 !important; }

    /* ── Module Quick-Access Cards ── */
    .module-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .module-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        color: inherit;
        border: 1px solid #E5E7EB;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        transition: transform .15s, box-shadow .15s;
    }
    .module-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,.1); text-decoration: none; color: inherit; }
    .module-card-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .module-card-icon svg { width: 24px; height: 24px; }
    .module-card-icon.blue   { background: #EEF2FF; color: #4338CA; }
    .module-card-icon.green  { background: #ECFDF5; color: #059669; }
    .module-card-icon.orange { background: #FFF7ED; color: #C2410C; }
    .module-card-icon.purple { background: #F5F3FF; color: #7C3AED; }
    .module-card-label { font-size: 0.75rem; color: #6B7280; font-weight: 500; margin: 0 0 2px; }
    .module-card-title { font-size: 0.95rem; font-weight: 700; color: #111827; margin: 0; }
    .module-card-arrow { margin-left: auto; color: #9CA3AF; }

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

{{-- Admin Sidebar --}}
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="layout-grid" style="width:24px;height:24px"></i>
        <span>MSWDO Admin</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/dashboard" class="active"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        <li><a href="/admin/social-case/dashboard"><i data-lucide="folder-open" style="width:20px;height:20px"></i><span>Social Case Study</span></a></li>
        <li><a href="/admin/financial/dashboard"><i data-lucide="circle-dollar-sign" style="width:20px;height:20px"></i><span>Financial Assistance</span></a></li>
        <li><a href="/admin/senior"><i data-lucide="users" style="width:20px;height:20px"></i><span>Senior Citizen</span></a></li>
        <li><a href="/admin/add-officers"><i data-lucide="user-check" style="width:20px;height:20px"></i><span>Add Officers</span></a></li>
        <li style="border-top:1px solid rgba(255,255,255,.1);margin-top:.5rem;padding-top:.5rem;">
            <a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a>
        </li>
    </ul>
</div>

<div class="main">
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

    {{-- Stat Cards --}}
    <div class="stat-cards" style="margin-bottom:1.5rem;">
        <div class="stat-card stat-card-blue">
            <div class="stat-card-content">
                <div class="stat-card-label">TOTAL CASES</div>
                <div class="stat-card-value counter" data-target="{{ $totalCases }}">0</div>
            </div>
            <div class="stat-card-icon"><i data-lucide="folder"></i></div>
        </div>
        <div class="stat-card stat-card-green">
            <div class="stat-card-content">
                <div class="stat-card-label">RESOLVED CASES</div>
                <div class="stat-card-value counter" data-target="{{ $resolvedCases }}">0</div>
            </div>
            <div class="stat-card-icon"><i data-lucide="check-circle"></i></div>
        </div>
        <div class="stat-card stat-card-purple">
            <div class="stat-card-content">
                <div class="stat-card-label">PENDING CASES</div>
                <div class="stat-card-value counter" data-target="{{ $pendingCases }}">0</div>
            </div>
            <div class="stat-card-icon"><i data-lucide="clock"></i></div>
        </div>
        <div class="stat-card stat-card-teal">
            <div class="stat-card-content">
                <div class="stat-card-label">TOTAL OFFICERS</div>
                <div class="stat-card-value counter" data-target="{{ $totalUsers }}">0</div>
            </div>
            <div class="stat-card-icon"><i data-lucide="users"></i></div>
        </div>
    </div>

    {{-- Module Quick Access --}}
    <div class="module-cards">
        <a href="/admin/social-case/dashboard" class="module-card">
            <div class="module-card-icon blue"><i data-lucide="folder-open"></i></div>
            <div>
                <p class="module-card-label">Module</p>
                <p class="module-card-title">Social Case Study</p>
            </div>
            <div class="module-card-arrow"><i data-lucide="arrow-right" style="width:16px;height:16px"></i></div>
        </a>
        <a href="/admin/financial/dashboard" class="module-card">
            <div class="module-card-icon green"><i data-lucide="circle-dollar-sign"></i></div>
            <div>
                <p class="module-card-label">Module</p>
                <p class="module-card-title">Financial Assistance</p>
            </div>
            <div class="module-card-arrow"><i data-lucide="arrow-right" style="width:16px;height:16px"></i></div>
        </a>
        <a href="/admin/senior" class="module-card">
            <div class="module-card-icon orange"><i data-lucide="users"></i></div>
            <div>
                <p class="module-card-label">Module</p>
                <p class="module-card-title">Senior Citizen</p>
            </div>
            <div class="module-card-arrow"><i data-lucide="arrow-right" style="width:16px;height:16px"></i></div>
        </a>
        <a href="/admin/add-officers" class="module-card">
            <div class="module-card-icon purple"><i data-lucide="user-check"></i></div>
            <div>
                <p class="module-card-label">Management</p>
                <p class="module-card-title">Add Officers</p>
            </div>
            <div class="module-card-arrow"><i data-lucide="arrow-right" style="width:16px;height:16px"></i></div>
        </a>
    </div>

    {{-- Officers Directory --}}
    <div class="card-panel" style="margin-bottom:1.5rem;">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="text-base font-bold text-slate-800 m-0 flex items-center gap-2">
                    <i data-lucide="user-check" class="w-5 h-5 text-indigo-600"></i> Officers Directory
                </h3>
                <p class="text-xs text-slate-500 m-0 mt-0.5">All registered system accounts</p>
            </div>
            <a href="/admin/add-officers" class="action-btn primary" style="text-decoration:none;">
                <i data-lucide="plus" class="w-4 h-4"></i> Add Officer
            </a>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($officers as $officer)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($officer->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-slate-800">{{ $officer->name }}</span>
                            </div>
                        </td>
                        <td class="text-slate-600 text-sm">{{ $officer->email }}</td>
                        <td class="text-slate-700 text-sm">{{ $officer->role?->label() ?? $officer->role }}</td>
                        <td class="text-slate-600 text-sm">{{ $officer->phone ?? '—' }}</td>
                        <td>
                            @php $statusVal = is_object($officer->status) ? $officer->status->value : $officer->status; @endphp
                            @if($statusVal === 'active')
                                <span class="badge-status active">Active</span>
                            @else
                                <span class="badge-status inactive">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-400 font-medium">No officers have been added yet.</td>
                    </tr>
                    @endforelse
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
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Counter animation
    document.querySelectorAll('.counter').forEach(function (counter) {
        var target = parseInt(counter.getAttribute('data-target')) || 0;
        if (target === 0) { counter.textContent = '0'; return; }
        var step = target / (1800 / 16);
        var current = 0;
        function tick() {
            current += step;
            if (current < target) { counter.textContent = Math.floor(current); requestAnimationFrame(tick); }
            else { counter.textContent = target; }
        }
        tick();
    });
});
</script>
@endpush