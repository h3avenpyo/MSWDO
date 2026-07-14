@extends('admin.social-case.layout')
@section('title', 'Create Social Case Study')

@section('content')
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/social-case/new" class="active"><i data-lucide="user-plus" style="width:20px;height:20px"></i> New case</a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i> All cases</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="page-head">
        <div>
            <h1>Create Social Case Study</h1>
            <p>Step 1 of 2 — Search for an existing client and verify eligibility before starting a new Social Case Study.</p>
        </div>
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
                    <input type="text" id="elig-name" placeholder="Search by Full Name, Client ID, or Contact Number" value="{{ $clientName ?? '' }}">
                    <i data-lucide="search" class="search-icon" style="width:20px;height:20px"></i>
                </div>
                
                <div class="hint" style="margin-bottom:16px">We'll check if this client received a social case study in the last 6 months.</div>
                
                <div style="display:flex;gap:12px">
                    <button class="btn primary" onclick="startEligibilityCheck()" style="flex:1">
                        <i data-lucide="search" style="width:16px;height:16px"></i> Search
                    </button>
                    <button class="btn ghost" onclick="window.location.href='/admin/social-case/new?register=true'" style="flex:1">
                        <i data-lucide="user-plus" style="width:16px;height:16px"></i> Register New Client
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
                    <div class="workflow-step active">
                        <div class="workflow-step-number">1</div>
                        <span>Search Client</span>
                    </div>
                    <div class="workflow-step-arrow"><i data-lucide="arrow-down" style="width:12px;height:12px"></i></div>
                    <div class="workflow-step">
                        <div class="workflow-step-number">2</div>
                        <span>Check Eligibility</span>
                    </div>
                    <div class="workflow-step-arrow"><i data-lucide="arrow-down" style="width:12px;height:12px"></i></div>
                    <div class="workflow-step">
                        <div class="workflow-step-number">3</div>
                        <span>Start Case</span>
                    </div>
                    <div class="workflow-step-arrow"><i data-lucide="arrow-down" style="width:12px;height:12px"></i></div>
                    <div class="workflow-step">
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
<script src="{{ asset('js/social-case.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadNewCase();
    });
</script>
@endpush
