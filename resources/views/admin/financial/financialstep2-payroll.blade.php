@extends('layouts.financial')

@section('title', 'Financial Assistance: Step 2 Payroll Generation - MSWDO Admin')
@section('page-title', 'Step 2: Payroll Generation')

@section('page-styles')
<style>
/* ============================================================
   DESIGN TOKENS - Policy 2.0 Compliance System
   ============================================================ */
:root {
    /* Brand (10% accent) */
    --color-brand: hsl(235, 66%, 30%);        /* #1A237E Navy Brand */
    --color-brand-light: hsl(235, 66%, 96%);  /* Light brand tint */
    --color-brand-dark: hsl(235, 66%, 22%);   /* #0D1663 Brand dark */

    /* Semantic Accents */
    --color-success: hsl(158, 64%, 40%);      /* #10B981 Emerald */
    --color-success-light: hsl(152, 76%, 96%);/* #ECFDF5 */
    --color-success-border: hsl(152, 60%, 70%);/* Visible contrast border */
    --color-warning: hsl(38, 92%, 50%);       /* #F59E0B Amber */
    --color-warning-light: hsl(48, 100%, 96%);/* #FFFBEB */
    --color-warning-border: hsl(48, 90%, 65%);/* Visible contrast border */
    --color-danger: hsl(0, 72%, 51%);         /* #DC2626 Rose */

    /* Surfaces (60% canvas, 30% secondary) */
    --color-canvas: hsl(210, 40%, 98%);       /* #F8FAFC Page Canvas (60%) */
    --color-surface: hsl(0, 0%, 100%);        /* #FFFFFF Card/Panel (30%) */
    --color-overlay: hsl(220, 14%, 97%);      /* #F1F5F9 Input/Subpanel */

    /* Text */
    --color-text-primary: hsl(222, 47%, 11%); /* #0F172A */
    --color-text-secondary: hsl(215, 16%, 35%);/* #334155 */
    --color-text-muted: hsl(215, 16%, 50%);   /* #64748B */

    /* Borders (Lightness >= 10% darker than background) */
    --color-border: hsl(214, 20%, 82%);       /* #CBD5E1 Visible edge */
    --color-border-strong: hsl(215, 16%, 65%);/* #94A3B8 Focus/emphasis */

    /* Shadows */
    --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.04);
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.04);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.09), 0 4px 8px rgba(0, 0, 0, 0.05);

    /* Border Radius Scale */
    --radius-xs: 4px;
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 14px;
    --radius-xl: 18px;
    --radius-full: 9999px;

    /* Transitions */
    --transition-fast: 100ms ease;
    --transition-base: 180ms ease;
    --transition-slow: 320ms ease;

    /* Type Scale */
    --text-xs: 11px;
    --text-sm: 13px;
    --text-base: 15px;
    --text-md: 17px;
    --text-lg: 20px;
    --text-xl: 24px;
    --text-2xl: 30px;
    --text-3xl: 40px;
}

/* ============================================================
   COMPONENT STYLES
   ============================================================ */
.payroll-wizard-hero {
    background: linear-gradient(135deg, hsl(235, 66%, 28%) 0%, hsl(235, 66%, 36%) 100%);
    border-radius: var(--radius-xl);
    padding: 20px 24px;
    color: #FFFFFF;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
}

.payroll-stat-card {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    box-shadow: var(--shadow-sm);
    transition: transform var(--transition-base), box-shadow var(--transition-base);
}
.payroll-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.table-clean th {
    font-size: var(--text-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--color-text-secondary);
    background: var(--color-canvas);
    border-bottom: 2px solid var(--color-border);
    padding: 12px 14px;
}
.table-clean td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid var(--color-border);
    font-size: var(--text-sm);
}
.table-clean tbody tr {
    transition: background-color var(--transition-fast);
}
.table-clean tbody tr:hover {
    background-color: var(--color-brand-light);
}

.filter-card {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    box-shadow: var(--shadow-sm);
}

.badge-category {
    background: var(--color-canvas);
    color: var(--color-text-secondary);
    font-weight: 600;
    font-size: var(--text-xs);
    padding: 3px 8px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-border);
    display: inline-block;
}

.status-pill-encoded {
    background-color: var(--color-success-light);
    color: var(--color-success);
    border: 1px solid var(--color-success-border);
    font-weight: 600;
    font-size: var(--text-xs);
    padding: 4px 10px;
    border-radius: var(--radius-full);
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.status-pill-pending {
    background-color: var(--color-warning-light);
    color: var(--color-warning);
    border: 1px solid var(--color-warning-border);
    font-weight: 600;
    font-size: var(--text-xs);
    padding: 4px 10px;
    border-radius: var(--radius-full);
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.amount-input-group {
    position: relative;
    max-width: 170px;
}
.amount-input-group .currency-symbol {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-text-muted);
    font-weight: 700;
    font-size: var(--text-sm);
    pointer-events: none;
    z-index: 4;
}
.amount-input-field {
    padding-left: 26px !important;
    font-weight: 700;
    font-size: var(--text-sm);
    color: var(--color-text-primary);
    border-radius: var(--radius-sm);
    border: 1.5px solid var(--color-border);
    transition: border-color var(--transition-base), box-shadow var(--transition-base), background-color var(--transition-base);
}
.amount-input-field:focus {
    border-color: var(--color-brand);
    box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.15);
    background-color: var(--color-surface);
}
.amount-input-field.is-saved {
    border-color: var(--color-success);
    background-color: var(--color-success-light);
}
.amount-input-field.is-unencoded {
    border-color: var(--color-warning);
    background-color: var(--color-warning-light);
}

.btn-save-amount {
    background: var(--color-brand);
    color: #FFFFFF;
    border: 1px solid var(--color-brand);
    border-radius: var(--radius-sm);
    padding: 6px 10px;
    font-size: var(--text-xs);
    font-weight: 600;
    transition: background-color var(--transition-base), transform var(--transition-fast), box-shadow var(--transition-base);
}
.btn-save-amount:hover {
    background: var(--color-brand-dark);
    color: #FFFFFF;
    box-shadow: var(--shadow-xs);
    transform: scale(1.02);
}

.payroll-readiness-banner {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    color: var(--color-text-primary);
}

.btn-generate-payroll {
    background: linear-gradient(135deg, hsl(158, 64%, 40%) 0%, hsl(158, 64%, 34%) 100%);
    color: #FFFFFF;
    border: 1px solid hsl(158, 64%, 30%);
    font-weight: 700;
    font-size: var(--text-sm);
    border-radius: var(--radius-full);
    padding: 10px 24px;
    box-shadow: var(--shadow-sm);
    transition: transform var(--transition-fast), box-shadow var(--transition-base);
}
.btn-generate-payroll:hover:not(:disabled) {
    background: linear-gradient(135deg, hsl(158, 64%, 34%) 0%, hsl(158, 64%, 28%) 100%);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
    color: #FFFFFF;
}
.btn-generate-payroll:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.preset-btn {
    font-size: var(--text-xs);
    padding: 2px 7px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-border);
    background: var(--color-surface);
    color: var(--color-text-secondary);
    font-weight: 600;
    cursor: pointer;
    transition: background-color var(--transition-fast), color var(--transition-fast), border-color var(--transition-fast);
}
.preset-btn:hover {
    background: var(--color-brand-light);
    color: var(--color-brand);
    border-color: var(--color-brand);
}

.rep-badge-self {
    background: var(--color-canvas);
    border: 1px solid var(--color-border);
    color: var(--color-text-secondary);
    font-size: var(--text-xs);
    padding: 2px 7px;
    border-radius: var(--radius-xs);
    font-weight: 600;
    display: inline-block;
}
.rep-badge-authorized {
    background: hsl(235, 80%, 96%);
    border: 1px solid hsl(235, 60%, 80%);
    color: hsl(235, 66%, 32%);
    font-size: var(--text-xs);
    padding: 2px 7px;
    border-radius: var(--radius-xs);
    font-weight: 600;
    display: inline-block;
}

/* Modal Surface Styling - Policy 2.0 No Nested Cards */
.modal-detail-panel {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: 16px;
    margin-bottom: 14px;
}
.modal-section-title {
    font-size: var(--text-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--color-brand);
    border-bottom: 1.5px solid var(--color-border);
    padding-bottom: 8px;
    margin-bottom: 12px;
}
.detail-field-label {
    font-size: var(--text-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--color-text-muted);
    margin-bottom: 2px;
}
.detail-field-value {
    font-size: var(--text-sm);
    font-weight: 600;
    color: var(--color-text-primary);
}
</style>
@endsection

@section('content')
@php
$userName = session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'Step 2 Officer';
@endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm border-success-subtle" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm border-danger-subtle" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Step Wizard Header Hero -->
<div class="payroll-wizard-hero mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h2 class="h4 fw-bold mb-1"><i class="fas fa-file-invoice-dollar me-2"></i>Step 2: Payroll Generation &amp; Financial Encoding</h2>
            <p class="mb-0 text-white-50 small">
                Encode and review the financial assistance amount for each intake processed today ({{ date('F d, Y') }}) before generating the printable payroll.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="user-welcome">
                <i class="fas fa-user-circle me-1"></i>
                <span>{{ $userName }}</span>
            </div>
        </div>
    </div>

    <!-- Workflow Pipeline Navigation -->
    <div class="step-wizard-nav pt-2">
        <a href="{{ route('admin.financial.financialstep1') }}" class="step-item-pill text-decoration-none">
            <div class="step-circle"><i class="fas fa-check"></i></div>
            <div class="step-label">Step 1: Intake &amp; Assessment</div>
        </a>
        <a href="{{ route('admin.financial.financialstep2') }}" class="step-item-pill text-decoration-none">
            <div class="step-circle"><i class="fas fa-list-check"></i></div>
            <div class="step-label">Step 2: Masterlist</div>
        </a>
        <div class="step-item-pill active">
            <div class="step-circle"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="step-label">Step 2: Payroll Generation (Active)</div>
        </div>
        <a href="{{ route('admin.financial.financialstep2.all-intakes') }}" class="step-item-pill text-decoration-none">
            <div class="step-circle"><i class="fas fa-layer-group"></i></div>
            <div class="step-label">All Intakes (Step 1 Submissions)</div>
        </a>
    </div>
</div>

<!-- Stat Cards Grid (Policy 2.0 Unified Palette) -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="payroll-stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="detail-field-label">Today's Total Intakes</div>
                    <h3 class="h4 fw-bold text-dark mb-0 mt-1" id="statTotalIntakes">{{ number_format($totalTodayCount ?? 0) }}</h3>
                </div>
                <div class="p-3 rounded-circle" style="background: hsl(235, 66%, 95%); color: var(--color-brand);">
                    <i class="fas fa-users fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="payroll-stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="detail-field-label">Amounts Encoded</div>
                    <h3 class="h4 fw-bold mb-0 mt-1" style="color: var(--color-success);" id="statEncodedCount">{{ number_format($encodedCount ?? 0) }}</h3>
                </div>
                <div class="p-3 rounded-circle" style="background: var(--color-success-light); color: var(--color-success);">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="payroll-stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="detail-field-label">Pending Amount</div>
                    <h3 class="h4 fw-bold mb-0 mt-1" style="color: {{ ($pendingCount ?? 0) > 0 ? 'var(--color-warning)' : 'var(--color-text-muted)' }};" id="statPendingCount">{{ number_format($pendingCount ?? 0) }}</h3>
                </div>
                <div class="p-3 rounded-circle" style="background: var(--color-warning-light); color: var(--color-warning);">
                    <i class="fas fa-hourglass-half fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="payroll-stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="detail-field-label">Total Payroll Fund</div>
                    <h3 class="h4 fw-bold mb-0 mt-1" style="color: var(--color-brand);" id="statTotalPayrollAmount">&#8369;{{ number_format($totalPayrollAmount ?? 0, 2) }}</h3>
                </div>
                <div class="p-3 rounded-circle" style="background: hsl(235, 66%, 95%); color: var(--color-brand);">
                    <i class="fas fa-coins fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payroll Verification & Readiness Banner (Clean neutral background) -->
<div id="readinessBanner" class="payroll-readiness-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="fs-2">
            <i id="readinessIcon" class="fas {{ $totalTodayCount === 0 ? 'fa-info-circle text-muted' : ($allAmountsEncoded ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning') }}"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1 text-dark" id="readinessTitle">
                @if($totalTodayCount === 0)
                    No Intake Records Recorded For Today
                @elseif($allAmountsEncoded)
                    All Intakes Verified &amp; Encoded! Ready for Payroll Generation
                @else
                    {{ $pendingCount }} of {{ $totalTodayCount }} Intakes Pending Financial Assistance Amount
                @endif
            </h5>
            <p class="mb-0 text-muted small" id="readinessSubtitle">
                @if($totalTodayCount === 0)
                    Clients who complete the Step 1 General Intake today will automatically appear here for amount encoding.
                @elseif($allAmountsEncoded)
                    Every client in today's masterlist has an assigned grant amount. You can now generate and print the official signed payroll sheet.
                @else
                    Please encode the financial assistance amount for all remaining intakes below. Once verified, the Generate Payroll button will be enabled.
                @endif
            </p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold" onclick="document.getElementById('bulkPayrollForm').requestSubmit()">
            <i class="fas fa-save me-1"></i> Save All Amounts
        </button>
        <button type="button" id="btnGeneratePayroll" class="btn btn-generate-payroll d-inline-flex align-items-center gap-2" onclick="handleGeneratePayrollClick()" {{ ($totalTodayCount > 0 && $allAmountsEncoded) ? '' : 'disabled' }}>
            <i class="fas fa-print"></i> Generate Payroll
        </button>
    </div>
</div>

<!-- Search, Filter & Controls -->
<div class="filter-card mb-4">
    <form action="{{ route('admin.financial.financialstep2.payroll') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-3 col-lg-3">
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search Beneficiary, Rep, Control No.</label>
            <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Control No, Beneficiary, Rep, Barangay..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2 col-lg-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i> Barangay</label>
            <select name="barangay" class="form-select form-select-sm rounded-3">
                <option value="All">All Barangays</option>
                @foreach($barangays as $brgy)
                <option value="{{ $brgy }}" {{ request('barangay') == $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-lg-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-tags me-1"></i> Category</label>
            <select name="category" class="form-select form-select-sm rounded-3">
                <option value="All">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-lg-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-filter me-1"></i> Encoding Status</label>
            <select name="status" class="form-select form-select-sm rounded-3">
                <option value="All">All Statuses</option>
                <option value="encoded" {{ request('status') == 'encoded' ? 'selected' : '' }}>Amount Encoded</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Missing Amount</option>
            </select>
        </div>
        <div class="col-md-2 col-lg-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-sort me-1"></i> Sort By</label>
            <select name="sort" class="form-select form-select-sm rounded-3">
                <option value="control_asc" {{ request('sort') == 'control_asc' ? 'selected' : '' }}>Control No. (Asc)</option>
                <option value="control_desc" {{ request('sort') == 'control_desc' ? 'selected' : '' }}>Control No. (Desc)</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Beneficiary (A-Z)</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Beneficiary (Z-A)</option>
                <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Amount (Highest)</option>
                <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Amount (Lowest)</option>
            </select>
        </div>
        <div class="col-md-1 col-lg-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100 fw-semibold" style="background: var(--color-brand); border-color: var(--color-brand);" title="Apply Filter">
                <i class="fas fa-filter"></i>
            </button>
            @if(request()->hasAny(['search', 'barangay', 'category', 'status', 'sort']))
            <a href="{{ route('admin.financial.financialstep2.payroll') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset Filters">
                <i class="fas fa-redo"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Masterlist Table Form (Policy 2.0 Direct Surface) -->
<form id="bulkPayrollForm" action="{{ route('admin.financial.financialstep2.payroll.bulk-update-amounts') }}" method="POST">
    @csrf
    <div class="filter-card mb-4 p-0 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-4 border-bottom" style="border-color: var(--color-border) !important;">
            <div>
                <h3 class="h6 fw-bold mb-1 text-dark">
                    <i class="fas fa-list-ol me-2" style="color: var(--color-brand);"></i>Today's Intakes for Payroll Encoding ({{ count($intakes) }} Records)
                </h3>
                <p class="text-muted small mb-0">
                    Enter the approved financial grant amount in the input field for each beneficiary. Fast save per row or bulk save all.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" style="background: var(--color-brand); border-color: var(--color-brand);">
                    <i class="fas fa-save me-1"></i> Save All Encoded Amounts
                </button>
            </div>
        </div>
        <div class="p-3">
            <div class="table-responsive">
                <table class="table-clean w-100 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th style="width: 130px;">Control No.</th>
                            <th>Beneficiary Name</th>
                            <th>Representative Name</th>
                            <th>Barangay &amp; Contact</th>
                            <th>Category &amp; Purpose</th>
                            <th style="width: 250px;">Financial Assistance Amount (&#8369;)</th>
                            <th style="width: 140px;">Status</th>
                            <th class="text-end" style="width: 90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($intakes as $index => $intake)
                        @php
                            $hasAmount = !is_null($intake->recommended_amount) && $intake->recommended_amount > 0;
                            $currentAmount = $hasAmount ? number_format($intake->recommended_amount, 2, '.', '') : '';
                            
                            // Representative Name Rule:
                            // If has separate rep -> show rep name.
                            // If no separate rep -> show beneficiary name.
                            $hasRep = $intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A';
                            $repDisplayName = $hasRep ? $intake->representative_full_name : $intake->beneficiary_full_name;
                        @endphp
                        <tr id="row-intake-{{ $intake->id }}" class="{{ $hasAmount ? '' : 'table-warning-subtle' }}">
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold" style="color: var(--color-brand);">{{ $intake->control_number }}</span>
                                <div>
                                    <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill" style="font-size: var(--text-xs); border-color: var(--color-border) !important;">{{ $intake->client_type ?? 'New' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $intake->beneficiary_full_name }}</div>
                                <div class="text-muted small">
                                    @if($intake->beneficiary_age)
                                        {{ $intake->beneficiary_age }} yrs &bull;
                                    @endif
                                    {{ $intake->beneficiary_sex ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold {{ $hasRep ? 'text-primary' : 'text-dark' }}">
                                    {{ $repDisplayName }}
                                </div>
                                <div>
                                    @if($hasRep)
                                        <span class="rep-badge-authorized"><i class="fas fa-user-friends me-1"></i>{{ $intake->rep_relationship ?? 'Authorized Rep' }}</span>
                                    @else
                                        <span class="rep-badge-self"><i class="fas fa-user-check me-1"></i>Self (Beneficiary as Rep)</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-dark fw-medium">{{ $intake->beneficiary_barangay ?? 'Silang, Cavite' }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-phone-alt me-1 text-secondary" style="font-size: var(--text-xs);"></i>
                                    {{ $hasRep && !empty($intake->rep_contact_number) ? $intake->rep_contact_number : ($intake->beneficiary_contact_number ?: 'No contact') }}
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="badge-category">{{ $intake->display_category }}</span>
                                </div>
                                <div class="text-muted small mt-1 text-truncate" style="max-width: 170px;" title="{{ $intake->display_assistance_purpose }}">
                                    <i class="fas fa-notes-medical me-1 text-danger"></i>{{ $intake->display_assistance_purpose }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="amount-input-group flex-grow-1">
                                            <span class="currency-symbol">&#8369;</span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   name="amounts[{{ $intake->id }}]" 
                                                   id="input-amount-{{ $intake->id }}"
                                                   class="form-control form-control-sm amount-input-field {{ $hasAmount ? 'is-saved' : 'is-unencoded' }}" 
                                                   placeholder="0.00" 
                                                   value="{{ $currentAmount }}"
                                                   data-intake-id="{{ $intake->id }}"
                                                   onkeydown="if(event.key==='Enter'){event.preventDefault(); saveSingleAmount({{ $intake->id }});}"
                                                   oninput="handleAmountInputChanged({{ $intake->id }})">
                                        </div>
                                        <button type="button" 
                                                id="btn-save-{{ $intake->id }}"
                                                class="btn btn-save-amount" 
                                                title="Save this amount" 
                                                onclick="saveSingleAmount({{ $intake->id }})">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                    <!-- Quick Preset Buttons -->
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="text-muted" style="font-size: var(--text-xs); font-weight: 600;">Presets:</span>
                                        <button type="button" class="preset-btn" onclick="applyPresetAmount({{ $intake->id }}, 1000)">&#8369;1k</button>
                                        <button type="button" class="preset-btn" onclick="applyPresetAmount({{ $intake->id }}, 2000)">&#8369;2k</button>
                                        <button type="button" class="preset-btn" onclick="applyPresetAmount({{ $intake->id }}, 3000)">&#8369;3k</button>
                                        <button type="button" class="preset-btn" onclick="applyPresetAmount({{ $intake->id }}, 5000)">&#8369;5k</button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div id="status-badge-{{ $intake->id }}">
                                    @if($hasAmount)
                                        <span class="status-pill-encoded">
                                            <i class="fas fa-check-circle"></i> Encoded
                                        </span>
                                    @else
                                        <span class="status-pill-pending">
                                            <i class="fas fa-exclamation-circle"></i> Required
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" title="View Full Intake Profile" onclick="viewIntakeDetails({{ json_encode($intake) }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-5 text-center">
                                <div class="empty-state-box text-center py-4">
                                    <i class="fas fa-receipt fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                    <h4 class="fw-bold mb-1" style="font-size: var(--text-md); color: var(--color-text-primary);">No Intake records found for today</h4>
                                    <p class="text-muted mb-0" style="font-size: var(--text-sm);">
                                        @if(request()->hasAny(['search', 'barangay', 'category', 'status', 'sort']))
                                            No records matched your search filters. Try resetting the filter criteria.
                                        @else
                                            Beneficiaries who complete Step 1 General Intake today will automatically appear here for amount encoding.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bottom Table Summary Footer -->
            <div class="d-flex justify-content-start align-items-center flex-wrap gap-3 p-3 rounded-3 mt-3 border" style="background: var(--color-canvas); border-color: var(--color-border) !important;">
                <span class="text-muted small">Total Listed: <strong>{{ count($intakes) }}</strong> Intakes</span>
                <span class="text-muted small">|</span>
                <span class="small fw-semibold" style="color: var(--color-success);">
                    <i class="fas fa-check-circle me-1"></i> <span id="footerEncodedCount">{{ $encodedCount }}</span> Encoded
                </span>
                <span class="small fw-semibold" style="color: var(--color-warning);">
                    <i class="fas fa-hourglass-half me-1"></i> <span id="footerPendingCount">{{ $pendingCount }}</span> Pending
                </span>
            </div>
        </div>
    </div>
</form>

<!-- Step 1 General Intake Preview Modal (Policy 2.0 Direct Panel Hierarchy) -->
<div class="modal fade" id="intakeQuickViewModal" tabindex="-1" aria-labelledby="intakeQuickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white" style="background: var(--color-brand);">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-check fa-lg"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="intakeQuickViewModalLabel">General Intake Preview</h5>
                        <div class="text-white-50 small" id="modalControlNumberHeader">Control No: N/A</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: var(--color-canvas);">
                <!-- Section 1: Beneficiary Information -->
                <div class="modal-detail-panel">
                    <div class="modal-section-title">
                        <i class="fas fa-user me-1"></i> Section I: Beneficiary Identifying Information
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-field-label">Full Name</div>
                            <div class="detail-field-value" id="modalBeneficiaryName">N/A</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-field-label">Sex / Age</div>
                            <div class="detail-field-value" id="modalBeneficiarySexAge">N/A</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Birthdate</div>
                            <div class="detail-field-value" id="modalBeneficiaryBirthday">N/A</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Contact Number</div>
                            <div class="detail-field-value" id="modalBeneficiaryContact">N/A</div>
                        </div>
                        <div class="col-md-5">
                            <div class="detail-field-label">Complete Address</div>
                            <div class="detail-field-value" id="modalBeneficiaryAddress">N/A</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Barangay</div>
                            <div class="detail-field-value" id="modalBeneficiaryBarangay">N/A</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-field-label">Occupation</div>
                            <div class="detail-field-value" id="modalBeneficiaryOccupation">N/A</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-field-label">Monthly Salary</div>
                            <div class="detail-field-value" id="modalBeneficiarySalary">N/A</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-field-label">Category</div>
                            <div class="detail-field-value" id="modalBeneficiaryCategory">N/A</div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Representative Information -->
                <div class="modal-detail-panel" id="modalRepresentativeCard">
                    <div class="modal-section-title">
                        <i class="fas fa-user-friends me-1"></i> Section II: Authorized Representative Information
                    </div>
                    <div class="row g-3" id="modalRepresentativeContent">
                        <div class="col-md-4">
                            <div class="detail-field-label">Representative Name</div>
                            <div class="detail-field-value" id="modalRepName">N/A</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Relationship to Beneficiary</div>
                            <div class="detail-field-value" id="modalRepRelationship">N/A</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-field-label">Sex / Age</div>
                            <div class="detail-field-value" id="modalRepSexAge">N/A</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Contact Number</div>
                            <div class="detail-field-value" id="modalRepContact">N/A</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field-label">Representative Address</div>
                            <div class="detail-field-value" id="modalRepAddress">N/A</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Occupation</div>
                            <div class="detail-field-value" id="modalRepOccupation">N/A</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Monthly Salary</div>
                            <div class="detail-field-value" id="modalRepSalary">N/A</div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Assessment & Assistance Recommendation -->
                <div class="modal-detail-panel mb-0">
                    <div class="modal-section-title">
                        <i class="fas fa-hand-holding-medical me-1"></i> Section III: Assessment &amp; Assistance Recommendations
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-field-label">Medical Condition / Concerns</div>
                            <div class="detail-field-value" id="modalMedicalConditions">N/A</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field-label">Reason for Assistance (Purpose)</div>
                            <div class="detail-field-value" id="modalAssistancePurpose">N/A</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-field-label">Social Worker Assessment</div>
                            <div class="detail-field-value p-3 rounded-2 border" id="modalSocialWorkerAssessment" style="background: var(--color-canvas); border-color: var(--color-border) !important; white-space: pre-line; font-size: var(--text-sm);">N/A</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field-label">Recommended Assistance Type</div>
                            <div class="detail-field-value fw-bold" style="color: var(--color-brand);" id="modalRecommendedType">N/A</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field-label">Recommended Amount (Assessed Grant)</div>
                            <div class="detail-field-value fs-5 fw-bold" style="color: var(--color-success);" id="modalRecommendedAmount">N/A</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top d-flex justify-content-between" style="background: var(--color-surface); border-color: var(--color-border) !important;">
                <span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i> Step 1 General Intake Record</span>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
const CSRF_TOKEN = "{{ csrf_token() }}";
const UPDATE_AMOUNT_URL = "{{ route('admin.financial.financialstep2.payroll.update-amount') }}";
const PRINT_PAYROLL_URL = "{{ route('admin.financial.financialstep2.payroll.print') }}";

let totalIntakesCount = {{ $totalTodayCount ?? 0 }};
let encodedIntakesCount = {{ $encodedCount ?? 0 }};
let pendingIntakesCount = {{ $pendingCount ?? 0 }};
let isAllEncoded = {{ $allAmountsEncoded ? 'true' : 'false' }};

function applyPresetAmount(intakeId, amount) {
    const input = document.getElementById('input-amount-' + intakeId);
    if (!input) return;
    input.value = parseFloat(amount).toFixed(2);
    saveSingleAmount(intakeId);
}

function handleAmountInputChanged(intakeId) {
    const input = document.getElementById('input-amount-' + intakeId);
    if (!input) return;
    input.classList.remove('is-saved');
    if (parseFloat(input.value) > 0) {
        input.classList.remove('is-unencoded');
    } else {
        input.classList.add('is-unencoded');
    }
}

function saveSingleAmount(intakeId) {
    const input = document.getElementById('input-amount-' + intakeId);
    const btn = document.getElementById('btn-save-' + intakeId);
    if (!input) return;

    const amountVal = parseFloat(input.value);
    if (isNaN(amountVal) || amountVal < 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Amount',
            text: 'Please enter a valid non-negative financial assistance amount.',
            confirmButtonColor: '#1A237E'
        });
        return;
    }

    const origBtnHtml = btn ? btn.innerHTML : '';
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }

    fetch(UPDATE_AMOUNT_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({
            intake_id: intakeId,
            recommended_amount: amountVal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (btn) {
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                btn.innerHTML = origBtnHtml;
                btn.disabled = false;
            }, 1200);
        }

        if (data.success) {
            input.value = amountVal.toFixed(2);
            input.classList.add('is-saved');
            input.classList.remove('is-unencoded');

            // Update row status badge
            const statusBadgeEl = document.getElementById('status-badge-' + intakeId);
            const rowEl = document.getElementById('row-intake-' + intakeId);
            if (amountVal > 0) {
                if (statusBadgeEl) {
                    statusBadgeEl.innerHTML = '<span class="status-pill-encoded"><i class="fas fa-check-circle"></i> Encoded</span>';
                }
                if (rowEl) rowEl.classList.remove('table-warning-subtle');
            } else {
                if (statusBadgeEl) {
                    statusBadgeEl.innerHTML = '<span class="status-pill-pending"><i class="fas fa-exclamation-circle"></i> Required</span>';
                }
                if (rowEl) rowEl.classList.add('table-warning-subtle');
            }

            // Update stats
            updateDashboardMetrics(data);

            // Toast notification
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: data.message
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error Saving Amount',
                text: data.message || 'Could not save financial assistance amount.',
                confirmButtonColor: '#1A237E'
            });
        }
    })
    .catch(err => {
        if (btn) {
            btn.innerHTML = origBtnHtml;
            btn.disabled = false;
        }
        Swal.fire({
            icon: 'error',
            title: 'Connection Error',
            text: 'An error occurred while saving the amount. Please try again.',
            confirmButtonColor: '#1A237E'
        });
    });
}

function updateDashboardMetrics(data) {
    if (!data) return;

    totalIntakesCount = data.total_today_count;
    encodedIntakesCount = data.encoded_count;
    pendingIntakesCount = data.pending_count;
    isAllEncoded = data.all_amounts_encoded;

    document.getElementById('statTotalIntakes').textContent = data.total_today_count;
    document.getElementById('statEncodedCount').textContent = data.encoded_count;
    document.getElementById('statPendingCount').textContent = data.pending_count;
    document.getElementById('statTotalPayrollAmount').textContent = data.formatted_total_payroll_amount;
    
    document.getElementById('footerEncodedCount').textContent = data.encoded_count;
    document.getElementById('footerPendingCount').textContent = data.pending_count;

    const readinessBanner = document.getElementById('readinessBanner');
    const readinessIcon = document.getElementById('readinessIcon');
    const readinessTitle = document.getElementById('readinessTitle');
    const readinessSubtitle = document.getElementById('readinessSubtitle');
    const btnGenerate = document.getElementById('btnGeneratePayroll');

    if (totalIntakesCount === 0) {
        readinessBanner.className = 'payroll-readiness-banner d-flex justify-content-between align-items-center flex-wrap gap-3';
        readinessIcon.className = 'fas fa-info-circle text-muted';
        readinessTitle.textContent = "No Intake Records Recorded For Today";
        readinessSubtitle.textContent = "Clients who complete the Step 1 General Intake today will automatically appear here for amount encoding.";
        if (btnGenerate) btnGenerate.disabled = true;
    } else if (isAllEncoded) {
        readinessBanner.className = 'payroll-readiness-banner d-flex justify-content-between align-items-center flex-wrap gap-3';
        readinessIcon.className = 'fas fa-check-circle text-success';
        readinessTitle.textContent = "All Intakes Verified & Encoded! Ready for Payroll Generation";
        readinessSubtitle.textContent = "Every client in today's masterlist has an assigned grant amount. You can now generate and print the official signed payroll sheet.";
        if (btnGenerate) btnGenerate.disabled = false;
    } else {
        readinessBanner.className = 'payroll-readiness-banner d-flex justify-content-between align-items-center flex-wrap gap-3';
        readinessIcon.className = 'fas fa-exclamation-triangle text-warning';
        readinessTitle.textContent = pendingIntakesCount + " of " + totalIntakesCount + " Intakes Pending Financial Assistance Amount";
        readinessSubtitle.textContent = "Please encode the financial assistance amount for all remaining intakes below. Once verified, the Generate Payroll button will be enabled.";
        if (btnGenerate) btnGenerate.disabled = true;
    }
}

function handleGeneratePayrollClick() {
    if (totalIntakesCount === 0) {
        Swal.fire({
            icon: 'info',
            title: 'No Intakes Today',
            text: 'There are no intake records processed for today to generate a payroll.',
            confirmButtonColor: '#1A237E'
        });
        return;
    }

    if (!isAllEncoded && pendingIntakesCount > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Unencoded Intakes Detected',
            text: 'There are still ' + pendingIntakesCount + ' intakes without an assigned amount. Please encode all amounts before generating the final payroll.',
            confirmButtonColor: '#1A237E',
            showCancelButton: true,
            confirmButtonText: 'Proceed Anyway',
            cancelButtonText: 'Review Intakes'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(PRINT_PAYROLL_URL, '_blank');
            }
        });
        return;
    }

    window.open(PRINT_PAYROLL_URL, '_blank');
}

function viewIntakeDetails(intake) {
    if (!intake) return;

    document.getElementById('modalControlNumberHeader').textContent = 'Control No: ' + (intake.control_number || 'N/A') + ' | Processed: ' + (intake.date_processed ? intake.date_processed.split('T')[0] : 'N/A');

    const benFullName = [intake.beneficiary_first_name, intake.beneficiary_middle_name, intake.beneficiary_last_name, intake.beneficiary_extension_name].filter(Boolean).join(' ') || 'N/A';
    document.getElementById('modalBeneficiaryName').textContent = benFullName;
    document.getElementById('modalBeneficiarySexAge').textContent = (intake.beneficiary_sex || 'N/A') + ' / ' + (intake.beneficiary_age ? intake.beneficiary_age + ' yrs' : 'N/A');
    document.getElementById('modalBeneficiaryBirthday').textContent = intake.beneficiary_birthday ? intake.beneficiary_birthday.split('T')[0] : 'N/A';
    document.getElementById('modalBeneficiaryContact').textContent = intake.beneficiary_contact_number || 'N/A';
    
    const benAddress = [intake.beneficiary_street_address, intake.beneficiary_barangay, intake.beneficiary_city || 'Silang', intake.beneficiary_province || 'Cavite'].filter(Boolean).join(', ') || 'N/A';
    document.getElementById('modalBeneficiaryAddress').textContent = benAddress;
    document.getElementById('modalBeneficiaryBarangay').textContent = intake.beneficiary_barangay || 'Silang';
    document.getElementById('modalBeneficiaryOccupation').textContent = intake.beneficiary_occupation || 'N/A';
    document.getElementById('modalBeneficiarySalary').textContent = intake.beneficiary_monthly_salary ? '₱' + parseFloat(intake.beneficiary_monthly_salary).toLocaleString('en-US', {minimumFractionDigits: 2}) : 'N/A';
    
    let categoriesText = intake.beneficiary_category || 'N/A';
    if (Array.isArray(intake.beneficiary_categories) && intake.beneficiary_categories.length > 0) {
        categoriesText = intake.beneficiary_categories.join(', ');
    }
    document.getElementById('modalBeneficiaryCategory').textContent = categoriesText;

    // Representative details
    const repCard = document.getElementById('modalRepresentativeCard');
    if (intake.has_representative) {
        repCard.style.display = 'block';
        const repFullName = [intake.rep_first_name, intake.rep_middle_name, intake.rep_last_name, intake.rep_extension_name].filter(Boolean).join(' ') || 'N/A';
        document.getElementById('modalRepName').textContent = repFullName;
        document.getElementById('modalRepRelationship').textContent = intake.rep_relationship || 'Representative';
        document.getElementById('modalRepSexAge').textContent = (intake.rep_sex || 'N/A') + ' / ' + (intake.rep_age ? intake.rep_age + ' yrs' : 'N/A');
        document.getElementById('modalRepContact').textContent = intake.rep_contact_number || 'N/A';
        const repAddress = [intake.rep_street_address, intake.rep_barangay, intake.rep_city || 'Silang', intake.rep_province || 'Cavite'].filter(Boolean).join(', ') || 'N/A';
        document.getElementById('modalRepAddress').textContent = repAddress;
        document.getElementById('modalRepOccupation').textContent = intake.rep_occupation || 'N/A';
        document.getElementById('modalRepSalary').textContent = intake.rep_monthly_salary ? '₱' + parseFloat(intake.rep_monthly_salary).toLocaleString('en-US', {minimumFractionDigits: 2}) : 'N/A';
    } else {
        repCard.style.display = 'none';
    }

    // Assessment & Assistance
    let medCond = 'None';
    if (Array.isArray(intake.medical_conditions) && intake.medical_conditions.length > 0) {
        medCond = intake.medical_conditions.join(', ');
    } else if (intake.medical_condition_other) {
        medCond = intake.medical_condition_other;
    }
    document.getElementById('modalMedicalConditions').textContent = medCond;
    document.getElementById('modalAssistancePurpose').textContent = intake.purpose_other || intake.assistance_purpose || intake.purpose || 'N/A';
    document.getElementById('modalSocialWorkerAssessment').textContent = intake.social_worker_assessment || 'Assessment completed in Step 1 intake.';
    document.getElementById('modalRecommendedType').textContent = intake.recommended_assistance_type || intake.service_provided || 'Financial Assistance';
    
    if (intake.recommended_amount) {
        document.getElementById('modalRecommendedAmount').textContent = '₱' + parseFloat(intake.recommended_amount).toLocaleString('en-US', {minimumFractionDigits: 2});
    } else {
        document.getElementById('modalRecommendedAmount').textContent = 'To be assessed';
    }

    const modalEl = document.getElementById('intakeQuickViewModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
</script>
@endsection
