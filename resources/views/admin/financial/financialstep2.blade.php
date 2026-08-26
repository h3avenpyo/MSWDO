@extends('layouts.financial')

@section('title', 'Financial Assistance: Step 2 Masterlist - MSWDO Admin')
@section('page-title', 'Financial Assistance Step 2 Masterlist')

@section('page-styles')
<style>
.table-clean th {
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    background: #F8FAFC;
    border-bottom: 2px solid #E2E8F0;
    padding: 12px 14px;
}
.table-clean td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid #F1F5F9;
    font-size: 0.875rem;
}
.table-clean tbody tr:hover {
    background-color: #F8FAFC;
}
.filter-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 16px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.badge-amount {
    background-color: #ECFDF5;
    color: #065F46;
    font-weight: 700;
    font-size: 0.85rem;
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid #A7F3D0;
    display: inline-block;
}
.badge-category {
    background: #F1F5F9;
    color: #334155;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid #E2E8F0;
}
.modal-section-title {
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #1A237E;
    border-bottom: 2px solid #EEF2FF;
    padding-bottom: 6px;
    margin-bottom: 12px;
}
.detail-field-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #64748B;
    margin-bottom: 2px;
}
.detail-field-value {
    font-size: 0.88rem;
    font-weight: 600;
    color: #1E293B;
}
.workflow-pipeline-box {
    background: linear-gradient(135deg, #1A237E 0%, #283593 100%);
    border-radius: 14px;
    padding: 14px 20px;
    color: #FFFFFF;
}
</style>
@endsection

@section('content')
@php
$userName = session('admin_user_name') ?? 'Officer';
@endphp

<!-- Step Wizard Header Card -->
<div class="step-wizard-card animate-fade-in mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h2 class="wizard-heading mb-1"><i class="fas fa-hand-holding-usd me-2"></i>Financial Assistance: Step 2 Masterlist</h2>
            <p class="mb-0 text-white-50" style="font-size: var(--text-sm);">Client General Intake Masterlist &bull; Select a client to proceed to Step 2 Financial Assistance processing</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="user-welcome">
                <i class="fas fa-user-circle me-1"></i>
                <span>Hello, {{ $userName }}</span>
            </div>
        </div>
    </div>

    <!-- Workflow Progression Pipeline -->
    <div class="workflow-pipeline-box mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2 text-white-50 small">
            <span class="text-white fw-bold"><i class="fas fa-route me-1"></i> Workflow:</span>
            <span class="badge bg-white text-dark rounded-pill px-3 py-1">1. General Intake (Step 1)</span>
            <i class="fas fa-arrow-right text-white-50"></i>
            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">2. Financial Masterlist</span>
            <i class="fas fa-arrow-right text-white-50"></i>
            <span class="badge bg-light text-secondary rounded-pill px-3 py-1">3. Select Client</span>
            <i class="fas fa-arrow-right text-white-50"></i>
            <span class="badge bg-light text-secondary rounded-pill px-3 py-1">4. Process Financial Assistance (Step 2)</span>
        </div>
    </div>

    <div class="step-wizard-nav pt-2">
        <a href="{{ route('admin.financial.financialstep1') }}" class="step-item-pill text-decoration-none">
            <div class="step-circle"><i class="fas fa-check"></i></div>
            <div class="step-label">Step 1: Intake &amp; Assessment (Completed)</div>
        </a>
        <div class="step-item-pill active">
            <div class="step-circle"><i class="fas fa-list-check"></i></div>
            <div class="step-label">Step 2: Financial Masterlist (Active)</div>
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
            <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100 fw-semibold" style="background: #1A237E; border-color: #1A237E;" title="Apply Filter">
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
                    <h3 class="card-title-clean"><i class="fas fa-address-book me-2 text-primary"></i>General Intake Masterlist Records</h3>
                    <p class="card-subtitle-clean">All clients who completed Step 1 General Intake &bull; Select a client to open their Financial Step 2 processing page</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.beneficiary-intake.transmittal') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-print me-1"></i> Print Transmittal Summary
                    </a>
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
                                <th class="text-end">Step 2 Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($intakes as $intake)
                            <tr>
                                <td>
                                    <span class="fw-bold" style="color: #1A237E;">{{ $intake->control_number }}</span>
                                    <div>
                                        <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">{{ $intake->client_type ?? 'New' }}</span>
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
                                    <div class="text-primary small mt-1" style="font-size: 0.75rem;">
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
                                    <div class="text-muted small" style="font-size: 0.75rem;"><i class="fas fa-user-edit me-1"></i>{{ $intake->encoderUser?->name ?? 'MSWDO Staff' }}</div>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge-category">{{ $intake->display_category }}</span>
                                    </div>
                                    <div class="text-muted small mt-1 text-truncate" style="max-width: 200px;" title="{{ $intake->display_assistance_purpose }}">
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
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-check-circle me-1"></i>Eligible for Payout
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-clock me-1"></i>For Assessment
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.financial.financialstep2.process', $intake) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" style="background: #1A237E; border-color: #1A237E;" title="Open Financial Step 2 Processing for this client">
                                            <i class="fas fa-hand-holding-usd me-1"></i> Process Client
                                        </a>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" title="Quick Preview Step 1 Data" onclick="viewIntakeDetails({{ json_encode($intake) }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.beneficiary-intake.transmittal', ['ids' => $intake->id]) }}" target="_blank" class="btn btn-outline-secondary" title="Print Disbursement Slip">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="p-5 text-center">
                                    <div class="empty-state-box text-center py-4">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                        <h4 class="fw-bold mb-1" style="font-size: var(--text-md); color: var(--color-text-primary);">No General Intake records found in Masterlist</h4>
                                        <p class="text-muted mb-0" style="font-size: var(--text-sm);">
                                            @if(request()->hasAny(['search', 'barangay', 'category', 'status', 'date']))
                                                No records matched your search filters. Try resetting the filter criteria.
                                            @else
                                                Clients who complete the Step 1 General Intake process will automatically appear in this masterlist.
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
            <div class="modal-header text-white" style="background: #1A237E; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
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
                            <div class="detail-field-value p-2 rounded bg-light border" id="modalSocialWorkerAssessment" style="white-space: pre-line; font-size: 0.85rem;">--</div>
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
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                    <a id="modalProcessClientLink" href="#" class="btn btn-primary btn-sm rounded-pill px-4" style="background: #1A237E; border-color: #1A237E;">
                        <i class="fas fa-hand-holding-usd me-1"></i> Open Step 2 Processing
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
function viewIntakeDetails(intake) {
    if (!intake) return;

    // Header info
    document.getElementById('modalControlNumberHeader').textContent = 'Control No: ' + (intake.control_number || 'N/A') + ' • Processed: ' + (intake.date_processed ? intake.date_processed.split('T')[0] : 'N/A');
    document.getElementById('modalProcessClientLink').href = '/admin/financial/financialstep2/process/' + intake.id;

    // Beneficiary details
    const benFullName = [intake.beneficiary_first_name, intake.beneficiary_middle_name, intake.beneficiary_last_name, intake.beneficiary_extension_name].filter(Boolean).join(' ') || 'N/A';
    document.getElementById('modalBeneficiaryName').textContent = benFullName;
    document.getElementById('modalBeneficiarySexAge').textContent = (intake.beneficiary_sex || '--') + ' / ' + (intake.beneficiary_age ? intake.beneficiary_age + ' yrs' : '--');
    document.getElementById('modalBeneficiaryBirthday').textContent = intake.beneficiary_birthday ? intake.beneficiary_birthday.split('T')[0] : '--';
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
        document.getElementById('modalRepSexAge').textContent = (intake.rep_sex || '--') + ' / ' + (intake.rep_age ? intake.rep_age + ' yrs' : '--');
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

    // Show modal
    const modalEl = document.getElementById('intakeQuickViewModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
</script>
@endsection