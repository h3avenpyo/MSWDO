@extends('layouts.financial')

@section('title', 'Step 2: Monthly Payroll Liquidation - MSWDO Admin')
@section('page-title', 'Step 2: Monthly Payroll Liquidation')

@section('page-styles')
<link href="{{ asset('css/financialstep2-liquidation.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-xs" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-xs" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Hero Banner -->
    <div class="liquidation-hero-banner mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold text-xs mb-2">
                    <i class="fas fa-calendar-check me-1"></i> Monthly Financial Liquidation
                </span>
                <h3 class="h4 fw-bold mb-1">
                    Monthly Payroll Liquidation Dashboard
                </h3>
                <p class="text-white-50 small mb-0">
                    Monitor, summarize, and liquidate financial assistance payrolls <strong>per month</strong>. Track monthly allocated funds, claimed disbursements, and remaining balances in real time.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.financial.financialstep2.payroll-records') }}"
                    class="btn btn-outline-light btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fas fa-archive me-1"></i> Payroll Records
                </a>
                <a href="{{ route('admin.financial.financialstep2') }}"
                    class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark shadow-xs">
                    <i class="fas fa-list-check me-1"></i> Step 2 Masterlist
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Summary Metric Cards (Global Overview) -->
    <div class="row g-3 mb-4">
        <!-- Total Allocated Amount -->
        <div class="col-sm-6 col-lg-3">
            <div class="stat-metric-card card-allocated d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-primary-subtle text-primary">
                    <i class="fas fa-coins fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase">Total Allocated Funds</div>
                    <h4 class="fw-bold mb-0 text-dark" id="global-total-allocated" data-raw-amount="{{ $globalTotalAllocated }}">{{ $formattedGlobalAllocated }}</h4>
                    <div class="text-muted text-2xs mt-0.5">
                        Across {{ $totalMonthsCount }} {{ Str::plural('month', $totalMonthsCount) }} ({{ $globalTotalBeneficiaries }} clients)
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Claimed / Disbursed Amount -->
        <div class="col-sm-6 col-lg-3">
            <div class="stat-metric-card card-claimed d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-success-subtle text-success">
                    <i class="fas fa-check-circle fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase">Total Claimed Amount</div>
                    <h4 class="fw-bold mb-0 text-success" id="global-total-claimed" data-raw-amount="{{ $globalTotalClaimed }}">{{ $formattedGlobalClaimed }}</h4>
                    <div class="text-muted text-2xs mt-0.5">
                        <span id="global-claimed-count">{{ $globalClaimedCount }}</span> {{ Str::plural('beneficiary', $globalClaimedCount) }} released
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Unclaimed Amount -->
        <div class="col-sm-6 col-lg-3">
            <div class="stat-metric-card card-remaining d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-warning-subtle text-warning">
                    <i class="fas fa-clock fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase">Total Unclaimed Amount</div>
                    <h4 class="fw-bold mb-0 text-warning-emphasis" id="global-total-unclaimed" data-raw-amount="{{ $globalTotalUnclaimed }}">{{ $formattedGlobalUnclaimed }}</h4>
                    <div class="text-muted text-2xs mt-0.5">
                        <span id="global-unclaimed-count">{{ $globalUnclaimedCount }}</span> {{ Str::plural('beneficiary', $globalUnclaimedCount) }} pending
                    </div>
                </div>
            </div>
        </div>

        <!-- Remaining Balance -->
        <div class="col-sm-6 col-lg-3">
            <div class="stat-metric-card card-remaining d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-warning-subtle text-warning-emphasis">
                    <i class="fas fa-wallet fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase">Remaining Balance</div>
                    <h4 class="fw-bold mb-0 text-dark" id="global-remaining-balance" data-raw-amount="{{ $globalRemainingBalance }}">{{ $formattedGlobalRemaining }}</h4>
                    <div class="text-muted text-2xs mt-0.5">
                        Rate: <strong class="text-success" id="global-liquidation-rate">{{ $globalLiquidationRate }}%</strong> liquidated
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search, Filter & Controls Bar -->
    <div class="filter-card mb-4">
        <form id="liquidationFilterForm" action="{{ route('admin.financial.financialstep2.liquidation') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-5 col-lg-5">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search Payroll Reference or Beneficiary</label>
                <input type="text" id="liquidationSearchInput" name="search" class="form-control form-control-sm rounded-3"
                    placeholder="Search by Control No, Beneficiary, Payroll Reference..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div class="col-md-3 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-calendar-alt me-1"></i> Filter by Month</label>
                <select name="month" class="form-select form-select-sm rounded-3">
                    <option value="All">All Months</option>
                    @if(isset($availableMonths) && count($availableMonths) > 0)
                        @foreach($availableMonths as $mKey => $mLabel)
                        <option value="{{ $mKey }}" {{ request('month') === $mKey ? 'selected' : '' }}>
                            {{ $mLabel }}
                        </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-filter me-1"></i> Liquidation Status</label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="All">All Statuses</option>
                    <option value="fully_liquidated" {{ request('status') === 'fully_liquidated' ? 'selected' : '' }}>Fully Liquidated (100%)</option>
                    <option value="partially_liquidated" {{ request('status') === 'partially_liquidated' ? 'selected' : '' }}>Partially Liquidated</option>
                    <option value="unreleased" {{ request('status') === 'unreleased' ? 'selected' : '' }}>Unreleased (0%)</option>
                </select>
            </div>
            <div class="col-md-2 col-lg-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100 fw-semibold btn-brand-primary" title="Apply Filters">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'month', 'status']))
                <a href="{{ route('admin.financial.financialstep2.liquidation') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2" title="Reset Filters">
                    <i class="fas fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>

        @if(request()->hasAny(['search', 'month', 'status']))
        <!-- Active Filter Tags -->
        <div class="d-flex align-items-center gap-2 flex-wrap mt-2 pt-2 border-top">
            <span class="text-muted small fw-semibold me-1"><i class="fas fa-sliders-h me-1"></i> Active Filters:</span>
            @if(request('search'))
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Keyword: "{{ request('search') }}"
                <a href="{{ route('admin.financial.financialstep2.liquidation', request()->except('search')) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('month') && request('month') !== 'All')
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Month: {{ $availableMonths[request('month')] ?? request('month') }}
                <a href="{{ route('admin.financial.financialstep2.liquidation', request()->except('month')) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('status') && request('status') !== 'All')
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                <a href="{{ route('admin.financial.financialstep2.liquidation', request()->except('status')) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            <a href="{{ route('admin.financial.financialstep2.liquidation') }}" class="text-danger small text-decoration-none ms-1">Reset all</a>
        </div>
        @endif
    </div>

    <!-- Controls Bar -->
    @if($monthlyRecords->isNotEmpty())
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="text-muted small">
            Showing <strong>{{ $monthlyRecords->count() }}</strong> {{ Str::plural('monthly liquidation record', $monthlyRecords->count()) }}
            (Total Allocated: <strong class="text-dark">{{ $formattedGlobalAllocated }}</strong> &bull; Total Claimed: <strong class="text-success">{{ $formattedGlobalClaimed }}</strong> &bull; Remaining: <strong class="text-warning-emphasis">{{ $formattedGlobalRemaining }}</strong>)
        </div>
    </div>
    @endif

    <!-- Monthly Liquidation Cards -->
    @forelse($monthlyRecords as $month)
    <div class="payroll-liquidation-card mb-4" id="month-card-{{ $month->month_key }}">
        <!-- Month Header with Essential Metrics -->
        <div class="payroll-card-header p-3.5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3 flex-wrap flex-grow-1">
                    <div class="p-2.5 rounded-circle bg-primary-subtle text-primary">
                        <i class="fas fa-calendar-day fs-4"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="fw-bold text-dark mb-0">{{ $month->month_label }}</h5>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-0.5 text-xs">
                                <i class="fas fa-layer-group me-1"></i> {{ $month->payrolls_count }} {{ Str::plural('Payroll Batch', $month->payrolls_count) }}
                            </span>
                            <span id="month-status-badge-{{ $month->month_key }}"
                                class="badge bg-{{ $month->liquidationStatusClass }}-subtle text-{{ $month->liquidationStatusClass === 'warning' ? 'warning-emphasis' : $month->liquidationStatusClass }} border border-{{ $month->liquidationStatusClass }}-subtle rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                @if($month->liquidationStatus === 'Fully Liquidated')
                                <i class="fas fa-check-circle me-1"></i>
                                @elseif($month->liquidationStatus === 'Partially Liquidated')
                                <i class="fas fa-clock me-1"></i>
                                @else
                                <i class="fas fa-hourglass-start me-1"></i>
                                @endif
                                {{ $month->liquidationStatus }}
                            </span>
                        </div>
                        <div class="text-muted small mt-1">
                            Monthly Summary &bull; Beneficiaries: <strong>{{ $month->totalBeneficiariesCount }}</strong>
                            (<span class="text-success fw-semibold" id="month-claimed-count-{{ $month->month_key }}">{{ $month->claimedCount }}</span> Claimed /
                            <span class="text-warning-emphasis fw-semibold" id="month-unclaimed-count-{{ $month->month_key }}">{{ $month->unclaimedCount }}</span> Unclaimed)
                        </div>
                    </div>
                </div>

                <!-- Month Actions: Generate Monthly Liquidation Report Button -->
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.financial.financialstep2.liquidation.report.month', $month->month_key) }}" target="_blank"
                        class="btn btn-sm btn-primary rounded-pill px-3.5 py-1.5 fw-bold btn-brand-primary shadow-xs" title="Generate Official Monthly Liquidation Report">
                        <i class="fas fa-print me-1"></i> Generate Monthly Liquidation Report
                    </a>
                </div>
            </div>

            <!-- Financial Summary Box for this Month (4 Core Figures) -->
            <div class="row g-2 mt-3 pt-3 border-top">
                <!-- 1. Total Allocated Amount -->
                <div class="col-sm-6 col-md-3">
                    <div class="finance-metric-box">
                        <div class="text-muted small text-uppercase fw-bold text-2xs">Total Allocated Amount</div>
                        <div class="h5 fw-bold mb-0 text-dark" id="month-allocated-{{ $month->month_key }}" data-raw-amount="{{ $month->totalAllocated }}">
                            {{ $month->formattedAllocated }}
                        </div>
                        <div class="text-muted text-2xs mt-0.5">Allocated for {{ $month->month_label }} ({{ $month->totalBeneficiariesCount }} clients)</div>
                    </div>
                </div>

                <!-- 2. Total Claimed Amount -->
                <div class="col-sm-6 col-md-3">
                    <div class="finance-metric-box border-success-subtle bg-success-subtle bg-opacity-25">
                        <div class="text-success small text-uppercase fw-bold text-2xs">Total Claimed Amount</div>
                        <div class="h5 fw-bold mb-0 text-success" id="month-claimed-{{ $month->month_key }}" data-raw-amount="{{ $month->totalClaimed }}">
                            {{ $month->formattedClaimed }}
                        </div>
                        <div class="text-muted text-2xs mt-0.5">Released to claimed beneficiaries</div>
                    </div>
                </div>

                <!-- 3. Total Unclaimed Amount -->
                <div class="col-sm-6 col-md-3">
                    <div class="finance-metric-box border-warning-subtle bg-warning-subtle bg-opacity-25">
                        <div class="text-warning-emphasis small text-uppercase fw-bold text-2xs">Total Unclaimed Amount</div>
                        <div class="h5 fw-bold mb-0 text-warning-emphasis" id="month-unclaimed-{{ $month->month_key }}" data-raw-amount="{{ $month->totalUnclaimed }}">
                            {{ $month->formattedUnclaimed }}
                        </div>
                        <div class="text-muted text-2xs mt-0.5">Pending beneficiary disbursement</div>
                    </div>
                </div>

                <!-- 4. Remaining Balance -->
                <div class="col-sm-6 col-md-3">
                    <div class="finance-metric-box border-warning-subtle" style="background-color: #FFFDF5;">
                        <div class="text-dark small text-uppercase fw-bold text-2xs">Remaining Balance</div>
                        <div class="h5 fw-bold mb-0 text-dark" id="month-remaining-{{ $month->month_key }}" data-raw-amount="{{ $month->remainingBalance }}">
                            {{ $month->formattedRemaining }}
                        </div>
                        <div class="text-muted text-2xs mt-0.5">Unreleased balance for {{ $month->month_label }}</div>
                    </div>
                </div>
            </div>

            <!-- Liquidation Progress Bar for this Month -->
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center small mb-1">
                    <span class="text-muted fw-semibold text-2xs text-uppercase">Monthly Liquidation Progress</span>
                    <span class="fw-bold text-dark text-xs" id="month-progress-text-{{ $month->month_key }}">{{ $month->liquidationRate }}% Liquidated</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div id="month-progress-{{ $month->month_key }}" class="progress-bar bg-{{ $month->liquidationStatusClass === 'warning' ? 'warning' : ($month->liquidationStatusClass === 'success' ? 'success' : 'secondary') }} progress-bar-liquidation"
                        role="progressbar" style="width: {{ $month->liquidationRate }}%;" aria-valuenow="{{ $month->liquidationRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <!-- Empty State -->
    <div class="payroll-liquidation-card p-5 text-center">
        <div class="py-5">
            <div class="mb-3">
                <i class="fas fa-calendar-times text-muted opacity-50" style="font-size: 4rem;"></i>
            </div>
            <h5 class="fw-bold text-dark">No Monthly Liquidation Records Found</h5>
            <p class="text-muted small mb-4">
                @if(request()->hasAny(['search', 'month', 'status']))
                No monthly records matched your filter criteria. Try resetting the filters.
                @else
                No payroll records have been generated yet. Use the Payroll Generation tab to process Step 1 intakes.
                @endif
            </p>
            <div class="d-flex justify-content-center gap-2">
                @if(request()->hasAny(['search', 'month', 'status']))
                <a href="{{ route('admin.financial.financialstep2.liquidation') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-rotate-left me-1"></i> Reset Filters
                </a>
                @else
                <a href="{{ route('admin.financial.financialstep2.payroll') }}" class="btn btn-sm btn-primary rounded-pill px-4 btn-brand-primary">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Go to Payroll Generation
                </a>
                @endif
            </div>
        </div>
    </div>
    @endforelse

</div>
@endsection

@section('page-scripts')
<script src="{{ asset('js/financialstep2-liquidation.js') }}"></script>
@endsection
