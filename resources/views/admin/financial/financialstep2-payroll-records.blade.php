@extends('layouts.financial')

@section('title', 'Step 2: Payroll Records - MSWDO Admin')
@section('page-title', 'Step 2: Payroll Records')

@section('page-styles')
<link href="{{ asset('css/financialstep2-payroll-records.css') }}" rel="stylesheet">
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
            <i class="fas fa-exclamation-circle fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($isDateView)
    {{-- ========================================================================= --}}
    {{-- VIEW A: DEDICATED DATE VIEW (ALL PAYROLLS GENERATED FOR THIS SELECTED DATE) --}}
    {{-- ========================================================================= --}}

    <!-- Breadcrumb / Back Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.financial.financialstep2.payroll-records') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="fas fa-arrow-left me-1"></i> Back to All Payroll Dates
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.financial.financialstep2.payroll', ['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')]) }}"
                class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-xs btn-brand-primary">
                <i class="fas fa-edit me-1"></i> Payroll Generation
            </a>
            <a href="{{ route('admin.financial.financialstep2') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-list-check me-1"></i> Step 2 Masterlist
            </a>
        </div>
    </div>

    <!-- Date Hero Banner -->
    <div class="date-hero-banner mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold text-xs mb-2">
                    <i class="fas fa-calendar-day me-1"></i> Payroll Date
                </span>
                <h3 class="h4 fw-bold mb-1">
                    {{ $selectedDate ? $selectedDate->format('F d, Y') : (request('date') ?: 'Selected Date') }}
                </h3>
                <p class="text-white-50 small mb-0">
                    Displaying all separately generated payroll records and full beneficiary masterlists for this date.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-light text-dark rounded-pill px-3 py-2 fw-semibold">
                    <i class="fas fa-file-invoice me-1 text-primary"></i> {{ $payrollRecords->count() }} Separate {{
                    Str::plural('Record', $payrollRecords->count()) }}
                </span>
                <span class="badge bg-light text-dark rounded-pill px-3 py-2 fw-semibold">
                    <i class="fas fa-users me-1 text-primary"></i> {{ $grandTotalBeneficiaries }} {{
                    Str::plural('Beneficiary', $grandTotalBeneficiaries) }}
                </span>
                <span class="badge bg-success text-white rounded-pill px-3 py-2 fw-bold">
                    <i class="fas fa-coins me-1"></i> {{ $formattedGrandTotalAmount }}
                </span>
                @if($payrollRecords->isNotEmpty())
                <a href="{{ route('admin.financial.financialstep2.payroll.print', ['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')]) }}"
                    target="_blank" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark shadow-xs">
                    <i class="fas fa-print me-1"></i> Print Date Payroll Sheet
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Search & Filter within Date -->
    <div class="filter-card mb-4">
        <form id="datePayrollFilterForm" action="{{ route('admin.financial.financialstep2.payroll-records') }}" method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="date" value="{{ $selectedDate ? $selectedDate->format('Y-m-d') : request('date') }}">
            <div class="col-md-4 col-lg-4">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search Beneficiary, Rep, Control No.</label>
                <input type="text" id="recordsSearchInput" name="search" class="form-control form-control-sm rounded-3" placeholder="Control No, Beneficiary, Rep..." value="{{ request('search') }}" autocomplete="off">
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
            <div class="col-md-3 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-file-invoice me-1"></i> Payroll Record / Batch</label>
                <select name="payroll_id" class="form-select form-select-sm rounded-3">
                    <option value="">All Records on this Date ({{ isset($allDatePayrolls) ? $allDatePayrolls->count() : $payrollRecords->count() }})</option>
                    @if(isset($allDatePayrolls))
                        @foreach($allDatePayrolls as $pBatch)
                        <option value="{{ $pBatch->id }}" {{ request('payroll_id') == $pBatch->id ? 'selected' : '' }}>
                            {{ $pBatch->payroll_number }} ({{ $pBatch->total_beneficiaries }} beneficiaries)
                        </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-sort me-1"></i> Sort By</label>
                <select name="sort" class="form-select form-select-sm rounded-3">
                    <option value="control_asc" {{ request('sort') == 'control_asc' || !request('sort') ? 'selected' : '' }}>Control No. (Asc)</option>
                    <option value="control_desc" {{ request('sort') == 'control_desc' ? 'selected' : '' }}>Control No. (Desc)</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Beneficiary (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Beneficiary (Z-A)</option>
                    <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Amount (Highest)</option>
                    <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Amount (Lowest)</option>
                </select>
            </div>
            <div class="col-md-1 col-lg-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100 fw-semibold btn-brand-primary" title="Apply Filters">
                    <i class="fas fa-filter"></i>
                </button>
                @if(request()->hasAny(['search', 'barangay', 'payroll_id', 'sort']))
                <a href="{{ route('admin.financial.financialstep2.payroll-records', ['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')]) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2" title="Reset Filters for this Date">
                    <i class="fas fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>

        @if(request()->hasAny(['search', 'barangay', 'payroll_id', 'sort']))
        <!-- Active Filter Badges for Date View -->
        <div class="d-flex align-items-center gap-2 flex-wrap mt-2 pt-2 border-top">
            <span class="text-muted small fw-semibold me-1"><i class="fas fa-sliders-h me-1"></i> Active Filters:</span>
            @if(request('search'))
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Keyword: "{{ request('search') }}"
                <a href="{{ route('admin.financial.financialstep2.payroll-records', array_merge(['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')], request()->except(['search', 'date']))) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('barangay') && request('barangay') !== 'All')
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Barangay: {{ request('barangay') }}
                <a href="{{ route('admin.financial.financialstep2.payroll-records', array_merge(['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')], request()->except(['barangay', 'date']))) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('payroll_id'))
            <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-2.5 py-1 text-xs">
                Filtered to 1 Batch
                <a href="{{ route('admin.financial.financialstep2.payroll-records', array_merge(['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')], request()->except(['payroll_id', 'date']))) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('sort') && request('sort') !== 'control_asc')
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Sort: {{ ucfirst(str_replace('_', ' ', request('sort'))) }}
                <a href="{{ route('admin.financial.financialstep2.payroll-records', array_merge(['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')], request()->except(['sort', 'date']))) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            <a href="{{ route('admin.financial.financialstep2.payroll-records', ['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')]) }}" class="text-danger small text-decoration-none ms-1">
                Reset filters
            </a>
        </div>
        @endif
    </div>

    <!-- Controls Bar -->
    @if($payrollRecords->isNotEmpty())
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="text-muted small">
            Showing <strong>{{ $payrollRecords->count() }}</strong> separate {{ Str::plural('payroll record',
            $payrollRecords->count()) }}
            (Total: <strong>{{ $grandTotalBeneficiaries }}</strong> {{ Str::plural('beneficiary',
            $grandTotalBeneficiaries) }} &bull; <strong class="text-success">{{ $formattedGrandTotalAmount }}</strong>)
            @if(request('payroll_id'))
            <span class="badge bg-light text-muted border ms-2">Filtered to 1 specific record</span>
            <a href="{{ route('admin.financial.financialstep2.payroll-records', ['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')]) }}"
                class="text-primary text-xs ms-1 text-decoration-none">Show all for this date</a>
            @endif
        </div>
        <div class="d-flex align-items-center gap-1">
            <button type="button" id="btnExpandAll" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold text-xs btn-expand-all">
                <i class="fas fa-angles-down me-1"></i> Expand All
            </button>
            <button type="button" id="btnCollapseAll" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold text-xs btn-collapse-all">
                <i class="fas fa-angles-up me-1"></i> Collapse All
            </button>
        </div>
    </div>
    @endif

    <!-- Separate Payroll Records on this Date (Collapsible Cards) -->
    @forelse($payrollRecords as $index => $record)
    <div class="records-table-card mb-4" id="payroll-card-{{ $record->id }}">
        <div
            class="record-header p-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap flex-grow-1 cursor-pointer" role="button"
                data-bs-toggle="collapse" data-bs-target="#collapseRecord-{{ $record->id }}"
                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapseRecord-{{ $record->id }}">
                <div class="p-2 rounded-circle bg-primary-subtle text-primary">
                    <i class="fas fa-file-invoice-dollar fs-5"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="fw-bold text-dark mb-0 font-monospace">
                            {{ $record->payroll_number }}
                        </h5>
                        @if($record->created_at)
                        <span class="badge bg-light text-muted border text-xs">
                            <i class="fas fa-clock me-1"></i>{{ $record->created_at->format('h:i A') }}
                        </span>
                        @endif
                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold text-xs">
                            <i class="fas fa-check-circle me-1"></i> Generated Official Payroll
                        </span>
                    </div>
                    <div class="text-muted small mt-1">
                        Showing <strong>{{ $record->recordBeneficiariesCount }}</strong> {{ Str::plural('beneficiary',
                        $record->recordBeneficiariesCount) }}
                        | Total Assistance: <strong class="text-success">{{ $record->formattedRecordAmount }}</strong>
                        | Disbursing Officer: <span class="text-dark fw-medium">{{ $record->disbursing_officer ?: 'MSWDO Disbursing Officer' }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                @if($record->recordBeneficiariesCount > 0)
                <a href="{{ route('admin.financial.financialstep2.payroll.print', ['payroll_id' => $record->id, 'barangay' => request('barangay')]) }}"
                    target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-xs btn-brand-primary">
                    <i class="fas fa-print me-1"></i> Print Payroll
                </a>
                @endif
                <button type="button" class="btn btn-sm btn-light border rounded-circle btn-circle-toggle"
                    data-bs-toggle="collapse" data-bs-target="#collapseRecord-{{ $record->id }}"
                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                    aria-controls="collapseRecord-{{ $record->id }}" title="Toggle Table">
                    <i class="fas fa-chevron-down record-toggle-icon text-muted"></i>
                </button>
            </div>
        </div>

        <!-- Collapsible Table Body -->
        <div id="collapseRecord-{{ $record->id }}" class="collapse {{ $index === 0 ? 'show' : '' }} record-collapse">
            <div class="table-responsive">
                <table class="table table-clean table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center th-col-num">#</th>
                            <th class="th-col-control">Control No.</th>
                            <th>Name of Representative</th>
                            <th>Name of Beneficiary</th>
                            <th>Barangay</th>
                            <th>Contact Number</th>
                            <th class="text-end">Amount of Financial Assistance</th>
                            <th class="text-center">Payroll Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($record->payrollRows as $row)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $row->item_no }}</td>
                            <td>
                                <span class="badge-control-no">
                                    {{ $row->control_number }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $row->representative_name }}</div>
                                @if(!$row->is_separate_rep)
                                <span class="badge bg-light text-muted border text-xs text-rep-type">(Beneficiary as Rep)</span>
                                @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle text-xs text-rep-type">(Representative)</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $row->beneficiary_name }}</div>
                            </td>
                            <td>
                                <div class="text-dark small"><i class="fas fa-map-marker-alt text-muted me-1"></i>{{
                                    $row->barangay }}</div>
                            </td>
                            <td>
                                <div class="text-dark small font-monospace"><i
                                        class="fas fa-phone text-muted me-1"></i>{{ $row->contact_number }}</div>
                            </td>
                            <td class="text-end">
                                <span class="badge-amount">
                                    {{ $row->formatted_amount }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="text-dark small fw-semibold">{{ $row->payroll_date }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No beneficiaries found for this payroll record matching the filter criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($record->payrollRows->isNotEmpty())
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="6" class="text-end text-uppercase fw-bold text-dark pe-3">Total for this
                                Payroll ({{ $record->recordBeneficiariesCount }} Beneficiaries):</th>
                            <th class="text-end">
                                <span class="badge-amount fs-6">
                                    {{ $record->formattedRecordAmount }}
                                </span>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="records-table-card p-5 text-center">
        <div class="py-5">
            <div class="mb-3">
                <i class="fas fa-file-invoice-dollar text-muted opacity-50 empty-state-icon-lg"></i>
            </div>
            <h5 class="fw-bold text-dark">No Generated Payroll Records for this Date</h5>
            <p class="text-muted small mb-4">
                @if(request()->hasAny(['search', 'barangay', 'payroll_id']))
                No records matched your search or barangay filters for this date.
                @else
                There are no generated payroll records for this date.
                @endif
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('admin.financial.financialstep2.payroll-records') }}"
                    class="btn btn-sm btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i> View All Dates
                </a>
                <a href="{{ route('admin.financial.financialstep2.payroll', ['date' => $selectedDate ? $selectedDate->format('Y-m-d') : request('date')]) }}"
                    class="btn btn-sm btn-primary rounded-pill px-4 btn-brand-primary">
                    <i class="fas fa-edit me-1"></i> Open in Payroll Generation
                </a>
            </div>
        </div>
    </div>
    @endforelse

    @else
    {{-- ========================================================================= --}}
    {{-- VIEW B: MAIN PAYROLL RECORDS DIRECTORY (ORGANIZED BY PAYROLL DATE) --}}
    {{-- ========================================================================= --}}

    <!-- Main Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-brand-color">
                <i class="fas fa-file-invoice-dollar me-2"></i>Step 2: Payroll Records
            </h4>
            <p class="text-muted small mb-0">Official payroll records organized by date. Click View on any date to
                inspect all payroll batches and complete beneficiary lists.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.financial.financialstep2.payroll') }}"
                class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-xs btn-brand-primary">
                <i class="fas fa-edit me-1"></i> Payroll Generation
            </a>
            <a href="{{ route('admin.financial.financialstep2') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-list-check me-1"></i> Step 2 Masterlist
            </a>
        </div>
    </div>

    <!-- Quick Summary Metric Cards (Instant High-Volume Overview) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-metric-card d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-primary-subtle text-primary">
                    <i class="fas fa-calendar-alt fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Dates Recorded</div>
                    <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalDatesCount ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-metric-card d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-info-subtle text-info">
                    <i class="fas fa-file-invoice fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Separate Payrolls</div>
                    <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalRecordsCount ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-metric-card d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-warning-subtle text-warning">
                    <i class="fas fa-users fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Beneficiaries</div>
                    <h4 class="fw-bold mb-0 text-dark">{{ number_format($grandTotalBeneficiaries ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-metric-card d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-success-subtle text-success">
                    <i class="fas fa-coins fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Assistance</div>
                    <h4 class="fw-bold mb-0 text-success">{{ $formattedGrandTotalAmount ?? '₱0.00' }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="filter-card mb-4">
        <form id="payrollRecordsFilterForm" action="{{ route('admin.financial.financialstep2.payroll-records') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search Across Records</label>
                <input type="text" id="recordsSearchInput" name="search" class="form-control form-control-sm rounded-3" placeholder="Beneficiary, Rep, Control #, Payroll #..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-calendar me-1"></i> Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm rounded-3" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-calendar-alt me-1"></i> Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm rounded-3" value="{{ request('date_to') }}">
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
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-sort me-1"></i> Sort By</label>
                <select name="sort" class="form-select form-select-sm rounded-3">
                    <option value="date_desc" {{ request('sort') == 'date_desc' || !request('sort') ? 'selected' : '' }}>Date (Newest First)</option>
                    <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest First)</option>
                    <option value="beneficiaries_desc" {{ request('sort') == 'beneficiaries_desc' ? 'selected' : '' }}>Most Beneficiaries</option>
                    <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Highest Total Amount</option>
                    <option value="records_desc" {{ request('sort') == 'records_desc' ? 'selected' : '' }}>Most Separate Payrolls</option>
                </select>
            </div>
            <div class="col-md-1 col-lg-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100 fw-semibold btn-brand-primary" title="Apply Filters">
                    <i class="fas fa-filter"></i>
                </button>
                @if(request()->hasAny(['search', 'barangay', 'date_from', 'date_to', 'sort']))
                <a href="{{ route('admin.financial.financialstep2.payroll-records') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2" title="Reset Filters">
                    <i class="fas fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>

        @if(request()->hasAny(['search', 'barangay', 'date_from', 'date_to', 'sort']))
        <!-- Active Filter Badges -->
        <div class="d-flex align-items-center gap-2 flex-wrap mt-2 pt-2 border-top">
            <span class="text-muted small fw-semibold me-1"><i class="fas fa-sliders-h me-1"></i> Active Filters:</span>
            @if(request('search'))
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Keyword: "{{ request('search') }}"
                <a href="{{ route('admin.financial.financialstep2.payroll-records', request()->except('search')) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('date_from'))
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                From: {{ Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                <a href="{{ route('admin.financial.financialstep2.payroll-records', request()->except('date_from')) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('date_to'))
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                To: {{ Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                <a href="{{ route('admin.financial.financialstep2.payroll-records', request()->except('date_to')) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('barangay') && request('barangay') !== 'All')
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Barangay: {{ request('barangay') }}
                <a href="{{ route('admin.financial.financialstep2.payroll-records', request()->except('barangay')) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            @if(request('sort') && request('sort') !== 'date_desc')
            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs">
                Sort: {{ ucfirst(str_replace('_', ' ', request('sort'))) }}
                <a href="{{ route('admin.financial.financialstep2.payroll-records', request()->except('sort')) }}" class="text-muted ms-1 text-decoration-none">&times;</a>
            </span>
            @endif
            <a href="{{ route('admin.financial.financialstep2.payroll-records') }}" class="text-danger small text-decoration-none ms-1">
                Clear all
            </a>
        </div>
        @endif
    </div>

    <!-- Directory Overview Bar with Result Counters -->
    @if(isset($paginatedDateGroups) && $paginatedDateGroups->total() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="text-muted small">
            Showing <strong>{{ $paginatedDateGroups->firstItem() }} - {{ $paginatedDateGroups->lastItem() }}</strong> of
            <strong>{{ $paginatedDateGroups->total() }}</strong> {{ Str::plural('date', $paginatedDateGroups->total())
            }}
            @if(request('search') || request('barangay'))
            <span class="badge bg-light text-primary border ms-2"><i class="fas fa-filter me-1"></i> Filter
                Active</span>
            @endif
        </div>
        <div>
            <span class="badge bg-light text-muted border text-xs">
                <i class="fas fa-info-circle me-1 text-primary"></i> Click "View Date Payrolls" to open full records for
                any date
            </span>
        </div>
    </div>
    @endif

    <!-- Paginated Date-Grouped Payroll Records List -->
    @forelse($paginatedDateGroups as $group)
    <div class="date-group-card">
        <!-- Date Group Header -->
        <div class="date-group-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="p-2.5 rounded-circle bg-primary text-white shadow-xs bg-brand-icon">
                    <i class="fas fa-calendar-alt fs-5"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="fw-bold text-dark mb-0">
                            {{ $group->formatted_date }}
                        </h5>
                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs fw-semibold">
                            <i class="fas fa-file-invoice me-1 text-primary"></i> {{ $group->records_count }} Separate
                            {{ Str::plural('Payroll', $group->records_count) }}
                        </span>
                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-xs fw-semibold">
                            <i class="fas fa-users me-1 text-secondary"></i> {{ $group->total_beneficiaries }} {{
                            Str::plural('Beneficiary', $group->total_beneficiaries) }}
                        </span>
                        <span class="badge-amount">
                            {{ $group->formatted_total_amount }}
                        </span>
                    </div>
                    <div class="text-muted small mt-1">
                        {{ $group->records_count }} separate {{ Str::plural('payroll record was', $group->records_count)
                        }} generated on this date.
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.financial.financialstep2.payroll.print', ['date' => $group->payroll_date]) }}"
                    target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
                    <i class="fas fa-print me-1"></i> Print All
                </a>
                <a href="{{ route('admin.financial.financialstep2.payroll-records.date', ['date' => $group->payroll_date]) }}"
                    class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-xs btn-brand-primary">
                    <i class="fas fa-folder-open me-1"></i> View Date Payrolls
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="date-group-card p-5 text-center">
        <div class="py-5">
            <div class="mb-3">
                <i class="fas fa-file-invoice-dollar text-muted opacity-50 empty-state-icon-lg"></i>
            </div>
            <h5 class="fw-bold text-dark">No Payroll Records Found</h5>
            <p class="text-muted small mb-4">
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'barangay']))
                No payroll records matched your search and filter criteria. Try resetting the filters.
                @else
                No payroll records have been generated yet. Use the Payroll Generation tab to process Step 1 intakes.
                @endif
            </p>
            <div class="d-flex justify-content-center gap-2">
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'barangay']))
                <a href="{{ route('admin.financial.financialstep2.payroll-records') }}"
                    class="btn btn-sm btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-rotate-left me-1"></i> Clear Filters
                </a>
                @endif
                <a href="{{ route('admin.financial.financialstep2.payroll') }}"
                    class="btn btn-sm btn-primary rounded-pill px-4 btn-brand-primary">
                    <i class="fas fa-edit me-1"></i> Go to Payroll Generation
                </a>
            </div>
        </div>
    </div>
    @endforelse

    <!-- Pagination Controls (Scalable Navigation for High Record Volumes) -->
    @if(isset($paginatedDateGroups) && $paginatedDateGroups->hasPages())
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4 pt-2">
        <div class="text-muted small">
            Showing page <strong>{{ $paginatedDateGroups->currentPage() }}</strong> of <strong>{{
                $paginatedDateGroups->lastPage() }}</strong> (Total: {{ $paginatedDateGroups->total() }} {{
            Str::plural('date', $paginatedDateGroups->total()) }})
        </div>
        <div>
            {{ $paginatedDateGroups->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif

    @endif

</div>
@endsection

@section('page-scripts')
<script src="{{ asset('js/financialstep2-payroll-records.js') }}"></script>
@endsection