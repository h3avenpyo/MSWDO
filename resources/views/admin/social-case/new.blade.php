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

<style>
    /* new.blade.php scoped */
    .main { padding-top: 14px !important; }
    @media (max-width: 767.98px) { .main { padding-top: 72px !important; } }
    .role-banner {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 16px;
    }
    .role-banner i, .role-banner svg { flex-shrink: 0; margin-top: 2px; }
    .role-banner-text { flex: 1; min-width: 0; }
    .role-banner-title { font-weight: 700; font-size: 14px; line-height: 1.4; }
    .role-banner-subtitle {
        font-size: 13px;
        line-height: 1.5;
        white-space: normal;
        overflow-wrap: break-word;
        word-break: break-word;
    }
    .page-subtitle {
        color: #6B7280;
        font-size: 0.875rem;
        margin: 0;
        white-space: normal;
        overflow-wrap: break-word;
        word-break: break-word;
        line-height: 1.5;
    }
</style>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        <li><a href="/admin/social-case/new" class="active"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>New case</span></a></li>
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li><a href="#" onclick="return false" style="opacity:0.5;pointer-events:none;cursor:not-allowed" title="Not available for eligibility checker accounts"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @else
        <li><a href="/admin/social-case/submitted"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @endif
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
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
    <!-- Page Sub-Header -->
    <div style="margin-bottom:20px;">
        @if($canCheckEligibility && !$canEncode)
            <div class="role-banner" style="background:#EEF2FF;border:1px solid #C7D2FE;">
                <i data-lucide="shield-check" style="width:20px;height:20px;color:#4338CA;"></i>
                <div class="role-banner-text">
                    <div class="role-banner-title" style="color:#4338CA;">Eligibility Checking Only</div>
                    <div class="role-banner-subtitle" style="color:#4F46E5;">Your account is responsible for verifying client eligibility. Eligible clients are forwarded to the case encoder for Social Case Study encoding.</div>
                </div>
            </div>
            <p class="page-subtitle">Search for an existing client, verify their eligibility, then submit eligible clients for case encoding.</p>
        @else
            <p class="page-subtitle">Step 1 of 2 — Search for an existing client and verify eligibility before starting a new Social Case Study. Clients forwarded by the eligibility checker are listed below.</p>
        @endif
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
            <span>{{ $canEncode ? 'Case Encoding' : 'Forward to Encoder' }}</span>
        </div>
    </div>

    @if($canEncode)
    <!-- Encoder Queue: clients forwarded by the eligibility checker -->
    <div class="panel" style="margin-bottom:24px">
        <h3 style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <i data-lucide="inbox" style="width:18px;height:18px;color:var(--icon-blue)"></i>
            Clients For Case Encoding
        </h3>
        <p style="font-size:13px;color:var(--text-muted);margin:0 0 12px">Clients that already passed eligibility checking and are waiting to be encoded.</p>
        <div id="encoderQueue" style="max-height:280px;overflow-y:auto">
            <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px">Loading...</div>
        </div>
    </div>
    @endif

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
                    @if($canEncode)
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
                    @else
                    <div class="workflow-step-arrow"><i data-lucide="arrow-down" style="width:12px;height:12px"></i></div>
                    <div class="workflow-step" id="workflowStep3">
                        <div class="workflow-step-number">3</div>
                        <span>Submit to Case Encoder</span>
                    </div>
                    @endif
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
