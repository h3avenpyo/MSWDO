@extends('admin.social-case.layout')
@section('title', 'Create Social Case Study')

@section('content')
<!-- Mobile Header (visible only on mobile) -->
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
            <p class="mobile-brand-subtitle">Create Social Case Study</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/social-case/new" class="active"><i data-lucide="user-plus" style="width:20px;height:20px"></i> New case</a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i> All cases</a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i> Archive</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <!-- Modern Page Header -->
    @php
        $userName = 'Social Case Study Officer';
        $words = explode(' ', $userName);
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($userName, 0, 2));
        }
    @endphp
    <header class="bg-white border-b border-[#E5E7EB] flex flex-col sm:flex-row justify-between sm:items-center shadow-[0_1px_3px_rgba(15,23,42,0.05)] lg:h-[72px] lg:px-8 lg:py-5 md:px-6 md:py-4 px-4 py-4 gap-4 sm:gap-0 select-none mb-6 sm:mb-8">
        <div class="flex items-center">
            <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Create Social Case Study</h1>
        </div>
        <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
            <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Thursday, July 16, 2026 at 01:51 PM</div>
            <div class="w-11 h-11 rounded-full bg-[#4338CA] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(67,56,202,0.3)] hover:scale-105 select-none" title="User Profile: {{ $userName }}">
                {{ $initials }}
            </div>
        </div>
    </header>

    <!-- Page Sub-Header -->
    <div class="mb-6">
        <p class="text-[#6B7280] text-sm m-0">Step 1 of 2 — Search for an existing client and verify eligibility before starting a new Social Case Study.</p>
    </div>

    <!-- Progress Stepper -->
    <div class="stepper">
        <div class="step active">
            <div class="step-number">1</div>
            <span>Client Eligibility</span>
        </div>
        <div class="step-connector"></div>
        <div class="step inactive">
            <div class="step-number">2</div>
            <span>Case Encoding</span>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="two-column">
        <!-- Left Column: Search Form -->
        <div>
            <div class="panel">
                <h3><i data-lucide="search" style="width:18px;height:18px"></i> Search Client</h3>
                
                <div class="search-box-large">
                    <input type="text" id="elig-name" placeholder="Search by Full Name" value="{{ $clientName ?? '' }}">
                </div>
                
                <div class="hint" style="margin-bottom:16px">We'll check if this client received a social case study in the last 6 months.</div>
                
                <div style="display:flex;justify-content:center">
                    <button class="btn primary" onclick="startEligibilityCheck()" style="width:auto;padding:8px 32px">
                        <i data-lucide="search" style="width:16px;height:16px"></i> Search
                    </button>
                </div>

                <!-- Search Results -->
                <div id="searchResults" class="search-results" style="display:none;"></div>
            </div>

            <!-- Client Summary (shown after selection) -->
            <div id="clientSummary" class="client-summary" style="display:none;">
                <div class="client-summary-header">
                    <div class="client-avatar" id="clientAvatar">JD</div>
                    <div>
                        <div class="client-name" id="clientNameDisplay">Juan Dela Cruz</div>
                        <div style="font-size:12px;color:var(--text-muted)">Selected Client</div>
                    </div>
                </div>
                <div class="client-info-grid">
                    <div class="client-info-item">
                        <span class="client-info-label">Age</span>
                        <span class="client-info-value" id="clientAge">52</span>
                    </div>
                    <div class="client-info-item">
                        <span class="client-info-label">Sex</span>
                        <span class="client-info-value" id="clientSex">Male</span>
                    </div>
                    <div class="client-info-item">
                        <span class="client-info-label">Barangay</span>
                        <span class="client-info-value" id="clientBarangay">Biluso</span>
                    </div>
                    <div class="client-info-item">
                        <span class="client-info-label">Last Case Study</span>
                        <span class="client-info-value" id="clientLastCase">None</span>
                    </div>
                </div>
            </div>

            <!-- Eligibility Status -->
            <div id="eligibilityStatus"></div>
        </div>

        <!-- Intake Form Container (shown when proceeding with new client) -->
        <div id="intakeFormContainer" style="display:none;"></div>

        <!-- Right Column: Information Panel -->
        <div>
            <div class="info-panel">
                <h4><i data-lucide="info" style="width:16px;height:16px"></i> Eligibility Rules</h4>
                <ul>
                    <li><i data-lucide="check" style="width:16px;height:16px"></i> Client must not have received a Social Case Study within the last 6 months</li>
                    <li><i data-lucide="check" style="width:16px;height:16px"></i> Existing clients can be reused for new cases</li>
                    <li><i data-lucide="check" style="width:16px;height:16px"></i> New clients may be registered if not found</li>
                </ul>
            </div>

            <div class="info-panel" style="margin-top:16px">
                <h4><i data-lucide="list-ordered" style="width:16px;height:16px"></i> Process</h4>
                <div class="workflow-steps">
                    <div class="workflow-step active" id="workflowStep1">
                        <div class="workflow-step-number">1</div>
                        <span>Search Client</span>
                    </div>
                    <div class="workflow-step-arrow"><i data-lucide="arrow-down" style="width:12px;height:12px"></i></div>
                    <div class="workflow-step" id="workflowStep2">
                        <div class="workflow-step-number">2</div>
                        <span>Check Eligibility</span>
                    </div>
                    <div class="workflow-step-arrow"><i data-lucide="arrow-down" style="width:12px;height:12px"></i></div>
                    <div class="workflow-step" id="workflowStep3">
                        <div class="workflow-step-number">3</div>
                        <span>Start Case</span>
                    </div>
                    <div class="workflow-step-arrow"><i data-lucide="arrow-down" style="width:12px;height:12px"></i></div>
                    <div class="workflow-step" id="workflowStep4">
                        <div class="workflow-step-number">4</div>
                        <span>Complete Encoding</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') . '?v=' . filemtime(public_path('js/social-case.js')) }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadNewCase();
    });
</script>
@endpush
