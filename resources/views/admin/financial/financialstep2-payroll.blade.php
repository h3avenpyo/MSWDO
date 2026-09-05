@extends('layouts.financial')

@section('title', 'Financial Assistance: Step 2 Payroll Generation - MSWDO Admin')
@section('page-title', 'Step 2: Payroll Generation')

@section('page-styles')
<link href="{{ asset('css/financialstep2-payroll.css') }}" rel="stylesheet">
@endsection

@section('content')
@php
$userName = session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'Step 2 Officer';
@endphp

<div class="container-fluid px-0" id="payrollWorkspace"
    data-csrf-token="{{ csrf_token() }}"
    data-update-amount-url="{{ route('admin.financial.financialstep2.payroll.update-amount') }}"
    data-generate-payroll-url="{{ route('admin.financial.financialstep2.payroll.generate') }}"
    data-print-payroll-url="{{ route('admin.financial.financialstep2.payroll.print') }}"
    data-payroll-records-url="{{ route('admin.financial.financialstep2.payroll-records') }}"
    data-payroll-date="{{ isset($targetDate) ? $targetDate->format('Y-m-d') : (request('date') ?: date('Y-m-d')) }}"
    data-total-intakes="{{ $totalTodayCount ?? 0 }}"
    data-encoded-intakes="{{ $encodedCount ?? 0 }}"
    data-pending-intakes="{{ $pendingCount ?? 0 }}"
    data-all-encoded="{{ $allAmountsEncoded ? 'true' : 'false' }}">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm border-success-subtle"
        role="alert">
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
                <h2 class="h4 fw-bold mb-1"><i class="fas fa-file-invoice-dollar me-2"></i>Step 2: Payroll Generation &amp;
                    Financial Encoding</h2>
                <p class="mb-0 text-white-50 small">
                    Encode and review the financial assistance amount for each unprocessed intake for {{ isset($targetDate)
                    ? $targetDate->format('F d, Y') : date('F d, Y') }} before generating the official payroll.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.financial.financialstep2.payroll-records', ['date' => isset($targetDate) ? $targetDate->format('Y-m-d') : date('Y-m-d')]) }}"
                    class="btn btn-outline-light btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fas fa-archive me-1"></i> View Payroll Records
                </a>
                <div class="user-welcome">
                    <i class="fas fa-user-circle me-1"></i>
                    <span>{{ $userName }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(isset($pendingDates) && $pendingDates->count() > 1)
    <!-- Dates with Pending Intakes Navigator -->
    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
        <span class="small fw-bold text-muted text-uppercase me-1"><i class="fas fa-calendar-alt me-1"></i> Dates with
            Pending Intakes:</span>
        @foreach($pendingDates as $pd)
        @php
        $isCurDate = isset($targetDate) && $targetDate->format('Y-m-d') === $pd->intake_date;
        $formattedPd = \Carbon\Carbon::parse($pd->intake_date)->format('M d, Y');
        @endphp
        <a href="{{ route('admin.financial.financialstep2.payroll', ['date' => $pd->intake_date]) }}"
            class="btn btn-sm rounded-pill px-3 fw-semibold {{ $isCurDate ? 'btn-brand-primary shadow-xs' : 'btn-outline-secondary bg-white' }}">
            <i class="fas fa-calendar-day me-1"></i> {{ $formattedPd }}
            <span class="badge {{ $isCurDate ? 'bg-light text-dark' : 'bg-secondary' }} rounded-pill ms-1">{{ $pd->count
                }}</span>
        </a>
        @endforeach
    </div>
    @endif

    <!-- Stat Cards Grid (Policy 2.0 Unified Palette) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="payroll-stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="detail-field-label">Unprocessed Intakes</div>
                        <h3 class="h4 fw-bold text-dark mb-0 mt-1" id="statTotalIntakes">{{ number_format($totalTodayCount
                            ?? 0) }}</h3>
                    </div>
                    <div class="p-3 rounded-circle stat-icon-brand">
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
                        <h3 class="h4 fw-bold mb-0 mt-1 stat-val-success" id="statEncodedCount">{{
                            number_format($encodedCount ?? 0) }}</h3>
                    </div>
                    <div class="p-3 rounded-circle stat-icon-success">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="payroll-stat-card">
                <div class="detail-field-label">Pending Amount</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="h4 fw-bold mb-0 mt-1 {{ ($pendingCount ?? 0) > 0 ? 'stat-val-warning' : 'stat-val-muted' }}"
                            id="statPendingCount">{{ number_format($pendingCount ?? 0) }}</h3>
                    </div>
                    <div class="p-3 rounded-circle stat-icon-warning">
                        <i class="fas fa-hourglass-half fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="payroll-stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="detail-field-label">Payroll Fund Total</div>
                        <h3 class="h4 fw-bold mb-0 mt-1 stat-val-brand" id="statTotalPayrollAmount">
                            &#8369;{{ number_format($totalPayrollAmount ?? 0, 2) }}</h3>
                    </div>
                    <div class="p-3 rounded-circle stat-icon-brand">
                        <i class="fas fa-coins fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payroll Verification & Readiness Banner (Clean neutral background) -->
    <div id="readinessBanner"
        class="payroll-readiness-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="fs-2">
                <i id="readinessIcon"
                    class="fas {{ $totalTodayCount === 0 ? 'fa-clipboard-check text-muted' : ($allAmountsEncoded ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-warning') }}"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1 text-dark" id="readinessTitle">
                    @if($totalTodayCount === 0)
                    No Pending Intakes for Payroll Generation
                    @elseif($allAmountsEncoded)
                    All Intakes Verified &amp; Encoded! Ready for Payroll Generation
                    @else
                    {{ $pendingCount }} of {{ $totalTodayCount }} Intakes Pending Financial Assistance Amount
                    @endif
                </h5>
                <p class="mb-0 text-muted small" id="readinessSubtitle">
                    @if($totalTodayCount === 0)
                    All intakes processed for this date have been generated into payroll records or no new intakes have been
                    recorded yet in Step 1.
                    @elseif($allAmountsEncoded)
                    Every client in the pending list has an assigned grant amount. You can now generate the official payroll
                    record.
                    @else
                    Please encode the financial assistance amount for all remaining intakes below. Once verified, the
                    Generate Payroll button will be enabled.
                    @endif
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold"
                id="btnBulkSave" {{ $totalTodayCount===0 ? 'disabled' : '' }}>
                <i class="fas fa-save me-1"></i> Save All Amounts
            </button>
            <button type="button" id="btnGeneratePayroll"
                class="btn btn-generate-payroll d-inline-flex align-items-center gap-2"
                {{ ($totalTodayCount> 0 && $allAmountsEncoded) ? '' : 'disabled' }}>
                <i class="fas fa-file-invoice-dollar"></i> Generate Payroll
            </button>
        </div>
    </div>

    <!-- Search, Filter & Controls -->
    <div class="filter-card mb-4">
        <form id="payrollFilterForm" action="{{ route('admin.financial.financialstep2.payroll') }}" method="GET"
            class="row g-2 align-items-end">
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-calendar-alt me-1"></i> Intake
                    Date</label>
                <input type="date" name="date" class="form-control form-control-sm rounded-3"
                    value="{{ request('date', isset($targetDate) ? $targetDate->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-3 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search
                    Beneficiary, Rep, Control No.</label>
                <input type="text" id="searchInput" name="search" class="form-control form-control-sm rounded-3"
                    placeholder="Control No, Beneficiary, Rep, Barangay..." value="{{ request('search') }}"
                    autocomplete="off">
            </div>
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>
                    Barangay</label>
                <select name="barangay" class="form-select form-select-sm rounded-3">
                    <option value="All">All Barangays</option>
                    @foreach($barangays as $brgy)
                    <option value="{{ $brgy }}" {{ request('barangay')==$brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-tags me-1"></i> Category</label>
                <select name="category" class="form-select form-select-sm rounded-3">
                    <option value="All">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category')==$cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 col-lg-1">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-filter me-1"></i> Status</label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="All">All</option>
                    <option value="encoded" {{ request('status')=='encoded' ? 'selected' : '' }}>Encoded</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-1 col-lg-1">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-sort me-1"></i> Sort</label>
                <select name="sort" class="form-select form-select-sm rounded-3">
                    <option value="date_desc" {{ request('sort')=='date_desc' || !request('sort') ? 'selected' : '' }}>Date
                        &darr;</option>
                    <option value="date_asc" {{ request('sort')=='date_asc' ? 'selected' : '' }}>Date &uarr;</option>
                    <option value="control_asc" {{ request('sort')=='control_asc' ? 'selected' : '' }}>Ctrl # &uarr;
                    </option>
                    <option value="control_desc" {{ request('sort')=='control_desc' ? 'selected' : '' }}>Ctrl # &darr;
                    </option>
                    <option value="name_asc" {{ request('sort')=='name_asc' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name_desc" {{ request('sort')=='name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="amount_desc" {{ request('sort')=='amount_desc' ? 'selected' : '' }}>Amount &darr;
                    </option>
                    <option value="amount_asc" {{ request('sort')=='amount_asc' ? 'selected' : '' }}>Amount &uarr;</option>
                </select>
            </div>
            <div class="col-md-1 col-lg-1 d-flex gap-1">
                @if(request()->hasAny(['date', 'search', 'barangay', 'category', 'status', 'sort']))
                <a href="{{ route('admin.financial.financialstep2.payroll') }}"
                    class="btn btn-sm btn-outline-secondary rounded-3 w-100 fw-semibold" title="Reset Filters">
                    <i class="fas fa-redo"></i>
                </a>
                @else
                <button type="button" class="btn btn-sm btn-light border rounded-3 w-100 text-muted" disabled
                    title="Filters apply automatically">
                    <i class="fas fa-bolt"></i>
                </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Masterlist Table Form (Policy 2.0 Direct Surface) -->
    <form id="bulkPayrollForm" action="{{ route('admin.financial.financialstep2.payroll.bulk-update-amounts') }}"
        method="POST">
        @csrf
        <div class="filter-card mb-4 p-0 overflow-hidden">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-4 border-bottom border-subtle-color">
                <div>
                    <h3 class="h6 fw-bold mb-1 text-dark">
                        <i class="fas fa-list-ol me-2 icon-brand-color"></i>Pending Intakes for Payroll
                        Encoding ({{ count($intakes) }} Records)
                    </h3>
                    <p class="text-muted small mb-0">
                        Enter the approved financial grant amount in the input field for each beneficiary. Fast save per row
                        or bulk save all.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.financial.financialstep2.payroll.print', ['date' => isset($targetDate) ? $targetDate->format('Y-m-d') : date('Y-m-d')]) }}"
                        target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                        <i class="fas fa-print me-1"></i> Print Payroll
                    </a>
                    <button type="submit" class="btn btn-primary btn-brand-primary btn-sm rounded-pill px-3 fw-semibold">
                        <i class="fas fa-save me-1"></i> Save All Encoded Amounts
                    </button>
                </div>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table-clean w-100 align-middle">
                        <thead>
                            <tr>
                                <th class="th-w-40">#</th>
                                <th class="th-w-120">Control No.</th>
                                <th class="th-w-110">Date Processed</th>
                                <th>Beneficiary Name</th>
                                <th>Representative Name</th>
                                <th>Barangay &amp; Contact</th>
                                <th>Category &amp; Purpose</th>
                                <th class="th-w-240">Financial Assistance Amount (&#8369;)</th>
                                <th class="th-w-120">Status</th>
                                <th class="text-end th-w-80">Action</th>
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
                            $hasRep = $intake->has_representative && !empty(trim($intake->representative_full_name ?? ''))
                            && $intake->representative_full_name !== 'N/A';
                            $repDisplayName = $hasRep ? $intake->representative_full_name : $intake->beneficiary_full_name;
                            @endphp
                            <tr id="row-intake-{{ $intake->id }}" class="{{ $hasAmount ? '' : 'table-warning-subtle' }}">
                                <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold text-brand-color">{{ $intake->control_number }}</span>
                                    <div>
                                        <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill badge-client-subtle">{{
                                            $intake->client_type ?? 'New' }}</span>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill badge-client-subtle">
                                        <i class="fas fa-calendar-day text-primary me-1"></i>
                                        {{ $intake->date_processed ?
                                        \Carbon\Carbon::parse($intake->date_processed)->format('M d, Y') :
                                        ($intake->created_at ? $intake->created_at->format('M d, Y') : 'N/A') }}
                                    </span>
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
                                        <span class="rep-badge-authorized"><i class="fas fa-user-friends me-1"></i>{{
                                            $intake->rep_relationship ?? 'Authorized Rep' }}</span>
                                        @else
                                        <span class="rep-badge-self"><i class="fas fa-user-check me-1"></i>Self (Beneficiary
                                            as Rep)</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium">{{ $intake->beneficiary_barangay ?? 'Silang, Cavite' }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-phone-alt me-1 text-secondary font-text-xs"></i>
                                        {{ $hasRep && !empty($intake->rep_contact_number) ? $intake->rep_contact_number :
                                        ($intake->beneficiary_contact_number ?: 'No contact') }}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge-category">{{ $intake->display_category }}</span>
                                    </div>
                                    <div class="text-muted small mt-1 text-truncate purpose-max-w"
                                        title="{{ $intake->display_assistance_purpose }}">
                                        <i class="fas fa-notes-medical me-1 text-danger"></i>{{
                                        $intake->display_assistance_purpose }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="amount-input-group flex-grow-1">
                                                <span class="currency-symbol">&#8369;</span>
                                                <input type="number" step="0.01" min="0" name="amounts[{{ $intake->id }}]"
                                                    id="input-amount-{{ $intake->id }}"
                                                    class="form-control form-control-sm amount-input-field {{ $hasAmount ? 'is-saved' : 'is-unencoded' }}"
                                                    placeholder="0.00" value="{{ $currentAmount }}"
                                                    data-intake-id="{{ $intake->id }}">
                                            </div>
                                            <button type="button" id="btn-save-{{ $intake->id }}"
                                                class="btn btn-save-amount" title="Save this amount"
                                                data-intake-id="{{ $intake->id }}">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                        <!-- Quick Preset Buttons -->
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="text-muted preset-label">Presets:</span>
                                            <button type="button" class="preset-btn" data-intake-id="{{ $intake->id }}" data-amount="1000">&#8369;1k</button>
                                            <button type="button" class="preset-btn" data-intake-id="{{ $intake->id }}" data-amount="2000">&#8369;2k</button>
                                            <button type="button" class="preset-btn" data-intake-id="{{ $intake->id }}" data-amount="3000">&#8369;3k</button>
                                            <button type="button" class="preset-btn" data-intake-id="{{ $intake->id }}" data-amount="5000">&#8369;5k</button>
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
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 btn-view-intake"
                                        title="View Full Intake Profile"
                                        data-intake="{{ json_encode($intake) }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="p-5 text-center">
                                    <div class="empty-state-box text-center py-4">
                                        <i class="fas fa-clipboard-check fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                        <h4 class="fw-bold mb-1 empty-title">No Pending Intakes for Payroll Generation</h4>
                                        <p class="text-muted mb-3 empty-desc">
                                            @if(request()->hasAny(['date', 'search', 'barangay', 'category', 'status', 'sort']))
                                            No ungenerated records matched your search filters. Try resetting the filter criteria.
                                            @else
                                            All intakes for this date have already been generated into official payrolls, or no new intakes have arrived from Step 1.
                                            @endif
                                        </p>
                                        <a href="{{ route('admin.financial.financialstep2.payroll-records') }}"
                                            class="btn btn-sm btn-primary btn-brand-primary rounded-pill px-4">
                                            <i class="fas fa-archive me-1"></i> View All Generated Payrolls
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Table Summary Footer -->
                <div class="d-flex justify-content-start align-items-center flex-wrap gap-3 p-3 rounded-3 mt-3 border panel-footer-canvas">
                    <span class="text-muted small">Total Listed: <strong>{{ count($intakes) }}</strong> Intakes</span>
                    <span class="text-muted small">|</span>
                    <span class="small fw-semibold text-color-success">
                        <i class="fas fa-check-circle me-1"></i> <span id="footerEncodedCount">{{ $encodedCount }}</span>
                        Encoded
                    </span>
                    <span class="small fw-semibold text-color-warning">
                        <i class="fas fa-hourglass-half me-1"></i> <span id="footerPendingCount">{{ $pendingCount }}</span>
                        Pending
                    </span>
                </div>
            </div>
        </div>
    </form>

    <!-- Step 1 General Intake Preview Modal (Policy 2.0 Direct Panel Hierarchy) -->
    <div class="modal fade" id="intakeQuickViewModal" tabindex="-1" aria-labelledby="intakeQuickViewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white modal-header-brand">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-clipboard-check fa-lg"></i>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="intakeQuickViewModalLabel">General Intake Preview</h5>
                            <div class="text-white-50 small" id="modalControlNumberHeader">Control No: N/A</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 modal-body-canvas">
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
                            <i class="fas fa-hand-holding-medical me-1"></i> Section III: Assessment &amp; Assistance
                            Recommendations
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
                                <div class="detail-field-value p-3 rounded-2 border modal-assessment-box" id="modalSocialWorkerAssessment">
                                    N/A</div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-field-label">Recommended Assistance Type</div>
                                <div class="detail-field-value fw-bold text-brand-color"
                                    id="modalRecommendedType">N/A</div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-field-label">Recommended Amount (Assessed Grant)</div>
                                <div class="detail-field-value fs-5 fw-bold text-color-success"
                                    id="modalRecommendedAmount">N/A</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top d-flex justify-content-between modal-footer-surface">
                    <span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i> Step 1 General
                        Intake Record</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script src="{{ asset('js/financialstep2-payroll.js') }}"></script>
@endsection