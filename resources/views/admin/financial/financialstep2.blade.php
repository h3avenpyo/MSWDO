@extends('layouts.financial')

@section('title', 'Financial Assistance: Step 2 Masterlist - MSWDO Admin')
@section('page-title', 'Financial Assistance Step 2 Masterlist')

@section('page-styles')
<link href="{{ asset('css/financialstep2.css') }}" rel="stylesheet">
@endsection

@section('content')
@php
$userName = session('admin_user_name') ?? 'Officer';
@endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-xs" role="alert">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-xs" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Step Wizard Header Card -->
<div class="step-wizard-card animate-fade-in mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h2 class="wizard-heading mb-1"><i class="fas fa-hand-holding-usd me-2"></i>Financial Assistance: Step 2 Masterlist</h2>
            <p class="mb-0 text-white-50 hero-subtitle">Today's Client General Intake Masterlist &bull; Step 2 Financial Assistance records processed today ({{ date('F d, Y') }})</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="user-welcome">
                <i class="fas fa-user-circle me-1"></i>
                <span>{{ $userName }}</span>
            </div>
        </div>
    </div>

    <!-- Workflow Progression Pipeline -->
    <div class="workflow-pipeline-box mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2 text-white-50 small">
            <span class="text-white fw-bold"><i class="fas fa-route me-1"></i> Workflow:</span>
            <span class="badge bg-white text-dark rounded-pill px-3 py-1">1. General Intake (Step 1)</span>
            <i class="fas fa-arrow-right text-white-50"></i>
            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">2. Financial Masterlist (Step 2)</span>
        </div>
    </div>
</div>

<!-- Stat Cards Grid -->
<div class="stat-cards-grid mb-4">
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Total Masterlist Records</p>
                <h3 class="stat-value">{{ number_format($totalQueueCount ?? 0) }}</h3>
            </div>
            <div class="stat-icon primary"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Eligible for Payout</p>
                <h3 class="stat-value">{{ number_format($pendingPayoutCount ?? 0) }}</h3>
            </div>
            <div class="stat-icon warning"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Today's Intake Queue</p>
                <h3 class="stat-value">{{ number_format($todayQueueCount ?? 0) }}</h3>
            </div>
            <div class="stat-icon success"><i class="fas fa-calendar-check"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Total Recommended Aid</p>
                <h3 class="stat-value">&#8369;{{ number_format($totalRecommendedAmount ?? 0, 2) }}</h3>
            </div>
            <div class="stat-icon info"><i class="fas fa-coins"></i></div>
        </div>
    </div>
</div>

<!-- Search, Filter & Sorting Controls -->
<div class="filter-card animate-fade-in mb-4">
    <form action="{{ route('admin.financial.financialstep2') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-3 col-lg-3">
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search Beneficiary / Control No.</label>
            <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Control No, Client, Rep, Barangay..." value="{{ request('search') }}">
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
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-info-circle me-1"></i> Status</label>
            <select name="status" class="form-select form-select-sm rounded-3">
                <option value="All">All Statuses</option>
                <option value="ready_payout" {{ request('status') == 'ready_payout' ? 'selected' : '' }}>Eligible for Payout</option>
                <option value="for_assessment" {{ request('status') == 'for_assessment' ? 'selected' : '' }}>For Assessment</option>
            </select>
        </div>
        <div class="col-md-2 col-lg-2">
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-sort me-1"></i> Sort By</label>
            <select name="sort" class="form-select form-select-sm rounded-3">
                <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Date (Newest First)</option>
                <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Date (Oldest First)</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Client Name (A-Z)</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Client Name (Z-A)</option>
                <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Grant Amount (Highest)</option>
                <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Grant Amount (Lowest)</option>
                <option value="control_asc" {{ request('sort') == 'control_asc' ? 'selected' : '' }}>Control No. (Asc)</option>
            </select>
        </div>
        <div class="col-md-1 col-lg-1 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary btn-brand-primary rounded-3 w-100 fw-semibold" title="Apply Filter">
                <i class="fas fa-filter"></i>
            </button>
            @if(request()->hasAny(['search', 'barangay', 'category', 'status', 'sort', 'date']))
            <a href="{{ route('admin.financial.financialstep2') }}" class="btn btn-sm btn-outline-secondary rounded-3" title="Reset Filters">
                <i class="fas fa-redo"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Content Workspace: Masterlist Table Directory -->
<div class="row g-4">
    <div class="col-12">
        <div class="card animate-fade-in">
            <div class="card-header-clean d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h3 class="card-title-clean"><i class="fas fa-address-book me-2 text-primary"></i>Today's General Intake Masterlist Records ({{ date('M d, Y') }})</h3>
                    <p class="card-subtitle-clean">Clients processed today &bull; To view all historical intakes, visit the <a href="{{ route('admin.financial.financialstep2.all-intakes') }}" class="text-primary fw-semibold text-decoration-none">All Intakes</a> page</p>
                </div>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table-clean w-100">
                        <thead>
                            <tr>
                                <th>Control No.</th>
                                <th>Client / Beneficiary Information</th>
                                <th>Address &amp; Contact</th>
                                <th>Intake Date &amp; Officer</th>
                                <th>Category / Medical Purpose</th>
                                <th>Assessed Grant</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($intakes as $intake)
                            <tr>
                                <td>
                                    <span class="fw-bold text-brand-color">{{ $intake->control_number }}</span>
                                    <div>
                                        <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill badge-client-subtle">{{ $intake->client_type ?? 'New' }}</span>
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
                                    @if($intake->has_representative)
                                    <div class="text-primary small mt-1 font-text-xs">
                                        <i class="fas fa-user-friends me-1"></i>Rep: <strong>{{ $intake->representative_full_name }}</strong> ({{ $intake->rep_relationship ?? 'Representative' }})
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark fw-medium">{{ $intake->beneficiary_barangay ?? 'Silang, Cavite' }}</div>
                                    <div class="text-muted small">{{ $intake->beneficiary_contact_number ?? 'No contact' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $intake->date_processed ? $intake->date_processed->format('M d, Y') : 'N/A' }}</div>
                                    <div class="text-muted small font-text-xs"><i class="fas fa-user-edit me-1"></i>{{ $intake->encoderUser?->name ?? 'MSWDO Staff' }}</div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge-category">{{ $intake->display_category }}</span>
                                    </div>
                                    <div class="text-muted small mt-1 text-truncate purpose-max-w-200" title="{{ $intake->display_assistance_purpose }}">
                                        <i class="fas fa-notes-medical me-1 text-danger"></i>{{ $intake->display_assistance_purpose }}
                                    </div>
                                </td>
                                <td>
                                    @if($intake->recommended_amount)
                                        <span class="badge-amount">&#8369;{{ number_format($intake->recommended_amount, 2) }}</span>
                                    @else
                                        <span class="text-muted small">For Assessment</span>
                                    @endif
                                </td>
                                <td>
                                    @if($intake->recommended_amount && $intake->recommended_amount > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold badge-status-payout">
                                            <i class="fas fa-check-circle me-1"></i>Eligible for Payout
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold badge-status-assessment">
                                            <i class="fas fa-clock me-1"></i>For Assessment
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-medium btn-view-intake" title="Quick Preview Record" data-intake="{{ json_encode($intake) }}">
                                        <i class="fas fa-eye me-1"></i> View
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-5 text-center">
                                    <div class="empty-state-box text-center py-4">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                        <h4 class="fw-bold mb-1 empty-title">No General Intake records found for today</h4>
                                        <p class="text-muted mb-0 empty-desc">
                                            @if(request()->hasAny(['search', 'barangay', 'category', 'status', 'sort']))
                                                No records matched your search filters for today. Try resetting the filter criteria.
                                            @else
                                                Clients who complete the Step 1 General Intake process today will automatically appear in this masterlist.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($intakes, 'hasPages') && $intakes->hasPages())
                <div class="pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        Showing <strong>{{ $intakes->firstItem() }}</strong> to <strong>{{ $intakes->lastItem() }}</strong> of <strong>{{ $intakes->total() }}</strong> clients
                    </div>
                    <div>
                        {{ $intakes->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Step 1 General Intake Quick Preview Modal -->
<div class="modal fade" id="intakeQuickViewModal" tabindex="-1" aria-labelledby="intakeQuickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-white modal-header-brand">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-check fa-lg"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="intakeQuickViewModalLabel">General Intake Masterlist Preview (Step 1 Record)</h5>
                        <div class="text-white-50 small" id="modalControlNumberHeader">Control No: --</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Section 1: Beneficiary Information -->
                <div class="card border-0 shadow-xs rounded-3 mb-3 p-3 bg-white">
                    <div class="modal-section-title">
                        <i class="fas fa-user me-1"></i> Section I: Beneficiary Identifying Information
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-field-label">Buong Pangalan (Full Name)</div>
                            <div class="detail-field-value" id="modalBeneficiaryName">--</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-field-label">Kasarian / Edad</div>
                            <div class="detail-field-value" id="modalBeneficiarySexAge">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Kapanganakan (Birthdate)</div>
                            <div class="detail-field-value" id="modalBeneficiaryBirthday">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Contact Number</div>
                            <div class="detail-field-value" id="modalBeneficiaryContact">--</div>
                        </div>
                        <div class="col-md-5">
                            <div class="detail-field-label">Tirahan (Complete Address)</div>
                            <div class="detail-field-value" id="modalBeneficiaryAddress">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Barangay</div>
                            <div class="detail-field-value" id="modalBeneficiaryBarangay">--</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-field-label">Trabaho</div>
                            <div class="detail-field-value" id="modalBeneficiaryOccupation">--</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-field-label">Buwanang Sahod</div>
                            <div class="detail-field-value" id="modalBeneficiarySalary">--</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-field-label">Kategorya (Category)</div>
                            <div class="detail-field-value" id="modalBeneficiaryCategory">--</div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Representative Information -->
                <div class="card border-0 shadow-xs rounded-3 mb-3 p-3 bg-white" id="modalRepresentativeCard">
                    <div class="modal-section-title">
                        <i class="fas fa-user-friends me-1"></i> Section II: Authorized Representative Information
                    </div>
                    <div class="row g-3" id="modalRepresentativeContent">
                        <div class="col-md-4">
                            <div class="detail-field-label">Pangalan ng Kinatawan</div>
                            <div class="detail-field-value" id="modalRepName">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Relasyon sa Benepisyaryo</div>
                            <div class="detail-field-value" id="modalRepRelationship">--</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-field-label">Kasarian / Edad</div>
                            <div class="detail-field-value" id="modalRepSexAge">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Contact Number</div>
                            <div class="detail-field-value" id="modalRepContact">--</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field-label">Tirahan ng Kinatawan</div>
                            <div class="detail-field-value" id="modalRepAddress">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Trabaho</div>
                            <div class="detail-field-value" id="modalRepOccupation">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-field-label">Buwanang Sahod</div>
                            <div class="detail-field-value" id="modalRepSalary">--</div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Assessment & Assistance Recommendation -->
                <div class="card border-0 shadow-xs rounded-3 mb-0 p-3 bg-white">
                    <div class="modal-section-title">
                        <i class="fas fa-hand-holding-medical me-1"></i> Section III: Assessment &amp; Assistance Recommendations
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-field-label">Medikal na Kondisyon / Concerns</div>
                            <div class="detail-field-value" id="modalMedicalConditions">--</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field-label">Dahilan ng Paghingi ng Tulong (Purpose)</div>
                            <div class="detail-field-value" id="modalAssistancePurpose">--</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-field-label">Pagsusuri ng Social Worker (Assessment)</div>
                            <div class="detail-field-value p-2 rounded bg-light border modal-assessment-box" id="modalSocialWorkerAssessment">--</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field-label">Inirerekomendang Uri ng Tulong</div>
                            <div class="detail-field-value text-primary fw-bold" id="modalRecommendedType">--</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field-label">Inirerekomendang Halaga (Assessed Grant)</div>
                            <div class="detail-field-value text-success fs-5 fw-bold" id="modalRecommendedAmount">--</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top d-flex justify-content-between">
                <span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i> Inherited from Step 1 General Intake</span>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script src="{{ asset('js/financialstep2.js') }}"></script>
@endsection