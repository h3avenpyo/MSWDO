@extends('layouts.financial')

@section('title', 'Intake Online Applications - MSWDO Admin')
@section('page-title', 'Financial Assistance Module')

@section('page-styles')
<style>
    /* ── MSWDO Financial Red Theme ── */
    :root {
        --financial-red: #b11116;
        --financial-red-dark: #8e0d11;
        --financial-red-deep: #7a0b0e;
        --financial-red-light: #fef2f2;
        --financial-red-border: #fca5a5;
    }

    /* Step Wizard Banner */
    .step-wizard-card {
        background: linear-gradient(135deg, #b11116 0%, #c4181e 50%, #8e0d11 100%) !important;
        color: #ffffff !important;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 16px rgba(177, 17, 22, 0.22) !important;
        border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .wizard-heading {
        font-size: 1.25rem;
        font-weight: 800;
        color: #ffffff !important;
        letter-spacing: -0.01em;
        margin-bottom: 0.25rem;
    }

    .hero-subtitle {
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.92) !important;
    }

    .step-1-badge {
        background: #ffffff !important;
        color: #b11116 !important;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        vertical-align: middle;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    /* Red Action Buttons */
    .btn-red-theme,
    .btn-primary-action {
        background: linear-gradient(135deg, #b11116 0%, #c4181e 50%, #8e0d11 100%) !important;
        border: 1px solid #8e0d11 !important;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(177, 17, 22, 0.2);
        transition: all 0.2s ease-in-out;
    }

    .btn-red-theme:hover,
    .btn-primary-action:hover {
        background: #8e0d11 !important;
        border-color: #7a0b0e !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(177, 17, 22, 0.32) !important;
        transform: translateY(-1px);
    }

    .btn-red-theme:focus,
    .btn-red-theme:active,
    .btn-primary-action:focus,
    .btn-primary-action:active {
        background: #7a0b0e !important;
        border-color: #7a0b0e !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 0.25rem rgba(177, 17, 22, 0.25) !important;
        transform: none;
    }

    /* Status Metric Tabs */
    .status-pill-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-weight: 500;
        transition: all 0.15s ease-in-out;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        text-decoration: none;
    }

    .status-pill-btn .status-pill-count {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease-in-out;
    }

    .status-pill-btn:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #b11116;
    }

    .status-pill-btn:hover .status-pill-count {
        background: #ffffff;
        color: #b11116;
        border-color: #fca5a5;
    }

    .status-pill-btn.active {
        background: linear-gradient(135deg, #b11116 0%, #c4181e 50%, #8e0d11 100%) !important;
        border-color: #8e0d11 !important;
        color: #ffffff !important;
        font-weight: 700;
        box-shadow: 0 3px 8px rgba(177, 17, 22, 0.28) !important;
    }

    .status-pill-btn.active .status-pill-count {
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
    }

    .status-pill-btn.active i {
        color: #ffffff !important;
    }

    /* Filter Controls Focus */
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #b11116 !important;
        box-shadow: 0 0 0 0.2rem rgba(177, 17, 22, 0.15) !important;
    }

    .btn-clear-filter:hover {
        background-color: #fee2e2 !important;
        color: #b11116 !important;
        border-color: #fca5a5 !important;
    }

    /* Table Highlight */
    .table-hover tbody tr:hover td {
        background-color: rgba(254, 242, 242, 0.35) !important;
    }

    .text-financial-red {
        color: #b11116 !important;
    }

    .bg-financial-red {
        background-color: #b11116 !important;
        color: #ffffff !important;
    }

    .border-financial-red {
        border-color: #b11116 !important;
    }

    /* Pagination */
    .pagination .page-item.active .page-link {
        background-color: #b11116 !important;
        border-color: #b11116 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(177, 17, 22, 0.25);
    }

    .pagination .page-item .page-link:hover:not(.active) {
        background-color: #fef2f2 !important;
        color: #b11116 !important;
        border-color: #fecaca !important;
    }

    .pagination .page-item .page-link {
        color: #334155;
    }

    /* Enforce uppercase on data information */
    .table tbody tr td,
    #reviewModalBody strong,
    #reviewModalBody span.text-dark,
    #reviewModalBody #benAddress,
    #reviewModalBody #repAddress,
    #reviewModalBody #benOccupationSalary,
    #reviewModalBody #repOccupationSalary,
    #reviewModalBody #modalAssistancePurpose,
    #reviewModalBody #modalMedicalConditions,
    #reviewModalBody #modalClientType,
    #reviewModalBody #familyCompositionTableBody td {
        text-transform: uppercase !important;
    }

    @media (max-width: 575.98px) {
        .filter-card .row.g-3 > [class*="col-"] {
            width: 100%;
            max-width: 100%;
            flex: 0 0 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- Alerts -->
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
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="wizard-heading mb-1 d-flex align-items-center flex-wrap gap-2 text-white">
                    <span><i class="fas fa-globe me-2"></i>Intake Online Applications</span>
                    <span class="badge step-1-badge rounded-pill px-2.5 py-1">Step 1</span>
                </h2>
                <p class="mb-0 hero-subtitle">
                    Suriin at patunayan ang mga aplikasyong isinumite sa pamamagitan ng Online Financial Assistance bago
                    ilipat sa regular na Step 1 Intake.
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <a href="{{ route('admin.financial.financialstep1') }}"
                    class="btn btn-light rounded-pill px-3 fw-bold shadow-xs" style="color: #b11116;">
                    <i class="fas fa-arrow-left me-1"></i>
                    <span>Pumunta sa Step 1 Intake</span>
                </a>
            </div>
        </div>
    </div>

    @php
        $currStatus = request('status', 'All');
        if (!request()->filled('status')) {
            $currStatus = 'All';
        }
    @endphp

    <!-- Status Tabs / Summary Metric Pills -->
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
        <a href="{{ route('admin.financial.online-intakes.index') }}"
            class="btn btn-sm status-pill-btn rounded-pill px-3 {{ $currStatus === 'All' ? 'active' : '' }}">
            <span>Lahat (All)</span>
            <span class="badge status-pill-count ms-1.5 rounded-pill">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('admin.financial.online-intakes.index', array_merge(request()->except(['status', 'page']), ['status' => 'For Review'])) }}"
            class="btn btn-sm status-pill-btn rounded-pill px-3 {{ $currStatus === 'For Review' ? 'active' : '' }}">
            <i class="fas fa-clock me-1 text-warning"></i>
            <span>For Review (Pending)</span>
            <span class="badge status-pill-count ms-1.5 rounded-pill">{{ $pendingCount }}</span>
        </a>
        <a href="{{ route('admin.financial.online-intakes.index', array_merge(request()->except(['status', 'page']), ['status' => 'Accepted'])) }}"
            class="btn btn-sm status-pill-btn rounded-pill px-3 {{ $currStatus === 'Accepted' ? 'active' : '' }}">
            <i class="fas fa-check-circle me-1 text-success"></i>
            <span>Accepted (Transferred)</span>
            <span class="badge status-pill-count ms-1.5 rounded-pill">{{ $acceptedCount }}</span>
        </a>
        <a href="{{ route('admin.financial.online-intakes.index', array_merge(request()->except(['status', 'page']), ['status' => 'Rejected'])) }}"
            class="btn btn-sm status-pill-btn rounded-pill px-3 {{ $currStatus === 'Rejected' ? 'active' : '' }}">
            <i class="fas fa-times-circle me-1 text-danger"></i>
            <span>Rejected</span>
            <span class="badge status-pill-count ms-1.5 rounded-pill">{{ $rejectedCount }}</span>
        </a>
    </div>

    <!-- Compound Filtering Form -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white filter-card">
        <form action="{{ route('admin.financial.online-intakes.index') }}" method="GET" class="row g-3 align-items-end">

            <!-- Search Query -->
            <div class="col-md-4 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1">
                    <i class="fas fa-search me-1" style="color: #b11116;"></i> Search Name / Control No.
                </label>
                <input type="text" name="search" class="form-control form-control-sm rounded-3"
                    placeholder="Hal. Juan Dela Cruz o Control No." value="{{ request('search') }}" maxlength="100">
            </div>

            <!-- Status Filter -->
            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="All" {{ request('status')=='All' || !request()->filled('status') ? 'selected' : ''
                        }}>Lahat ng Status</option>
                    <option value="For Review" {{ request('status')=='For Review' ? 'selected' : '' }}>For Review
                    </option>
                    <option value="Accepted" {{ request('status')=='Accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="Rejected" {{ request('status')=='Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Barangay Filter -->
            <div class="col-md-3 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1">Barangay</label>
                <select name="barangay" class="form-select form-select-sm rounded-3">
                    <option value="All">Lahat ng Barangay</option>
                    @foreach($barangays as $b)
                    <option value="{{ $b }}" {{ request('barangay')==$b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range: From -->
            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1">Petsa Mula (From)</label>
                <input type="date" name="date_from" class="form-control form-control-sm rounded-3"
                    value="{{ request('date_from') }}">
            </div>

            <!-- Date Range: To -->
            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1">Petsa Hanggang (To)</label>
                <input type="date" name="date_to" class="form-control form-control-sm rounded-3"
                    value="{{ request('date_to') }}">
            </div>

            <!-- Filter Actions -->
            <div class="col-12 d-flex gap-2 justify-content-end pt-1">
                <button type="submit" class="btn btn-sm btn-red-theme rounded-3 px-4 fw-semibold shadow-xs">
                    <i class="fas fa-filter me-1"></i> Apply Filters
                </button>
                <a href="{{ route('admin.financial.online-intakes.index') }}"
                    class="btn btn-sm btn-light border btn-clear-filter rounded-3 px-3 fw-semibold text-secondary"
                    title="Reset all filters">
                    <i class="fas fa-undo me-1"></i> Reset / Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div
            class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    Mga Isinumiteng Online Intake Application
                </h6>
                <span class="text-muted small">Ipinapakita: {{ $applications->firstItem() ?? 0 }} hanggang {{
                    $applications->lastItem() ?? 0 }} sa kabuuang {{ $applications->total() }} tala</span>
            </div>
            @if(request()->anyFilled(['search', 'status', 'barangay', 'date_from', 'date_to']))
            <span class="badge px-2.5 py-1.5 rounded-pill small fw-semibold" style="background-color: #fef2f2; color: #b11116; border: 1px solid #fecaca;">
                <i class="fas fa-filter me-1" style="color: #b11116;"></i> May Aktibong Filter
            </span>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                <thead class="table-light text-secondary"
                    style="font-size: 0.775rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <tr>
                        <th class="ps-3" style="width: 170px;">Control No. / Ref ID</th>
                        <th style="width: 140px;">Petsa Isinumite</th>
                        <th>Benepisyaryo (Beneficiary)</th>
                        <th>Barangay</th>
                        <th>Layunin / Pangangailangan</th>
                        <th class="text-center" style="width: 130px;">Status</th>
                        <th class="text-end pe-3" style="width: 150px;">Aksyon</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    <tr>
                        <!-- Control Number / Online Reference ID -->
                        <td class="ps-3">
                            @if($app->control_number)
                            <div class="font-monospace fw-bold" style="font-size: 0.85rem; color: #b11116;">{{
                                $app->control_number }}</div>
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5"
                                style="font-size: 0.68rem;">
                                <i class="fas fa-check-circle me-1"></i>Assigned (Step 1)
                            </span>
                            @else
                            <div class="font-monospace text-muted small fw-semibold">
                                <i class="fas fa-hashtag me-0.5 text-secondary"></i>{{ $app->reference_number }}
                            </div>
                            <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem;"
                                title="Ang Control Number ay itatalaga lamang kapag opisyal nang tinanggap (Accepted) ng staff.">
                                <i class="fas fa-hourglass-half me-1 text-warning"></i>Walang Control No. (Pending)
                            </span>
                            @endif
                        </td>

                        <!-- Date Submitted -->
                        <td class="text-muted small">
                            <div class="fw-semibold text-dark text-uppercase">{{ $app->date_submitted ? $app->date_submitted->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-muted text-uppercase" style="font-size: 0.75rem;">{{ $app->date_submitted ?
                                $app->date_submitted->format('h:i A') : '' }}</div>
                        </td>

                        <!-- Beneficiary Info -->
                        <td>
                            <div class="fw-bold text-dark text-uppercase">{{ $app->beneficiary_full_name }}</div>
                            <div class="text-muted small text-uppercase">
                                <span>{{ $app->beneficiary_age }} TAONG GULANG</span> /
                                <span>{{ $app->beneficiary_sex }}</span>
                                @if($app->has_representative && $app->rep_full_name)
                                <span class="d-block text-secondary mt-0.5 text-uppercase" style="font-size: 0.75rem;">
                                    <i class="fas fa-user-shield me-1 text-muted"></i>REP: {{ $app->rep_full_name }} ({{
                                    $app->rep_relationship ?: 'KINATAWAN' }})
                                </span>
                                @endif
                            </div>
                        </td>

                        <!-- Barangay -->
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1 text-uppercase">{{ $app->beneficiary_barangay
                                }}</span>
                        </td>

                        <!-- Purpose -->
                        <td>
                            @php
                                $requestedPurpose = !empty($app->assistance_type) 
                                    ? $app->assistance_type 
                                    : (!empty($app->display_assistance_purpose) && $app->display_assistance_purpose !== 'N/A' 
                                        ? $app->display_assistance_purpose 
                                        : ($app->assistance_purpose ?: 'GENERAL ASSISTANCE'));
                                $medCondition = $app->display_medical_condition;
                            @endphp
                            <div class="text-dark fw-medium text-uppercase">{{ $requestedPurpose }}</div>
                            @if(!empty($medCondition) && $medCondition !== 'N/A' && mb_strtoupper($medCondition) !== mb_strtoupper($requestedPurpose))
                            <div class="text-muted small text-truncate text-uppercase mt-0.5" style="max-width: 220px;"
                                title="{{ $medCondition }}">
                                <i class="fas fa-notes-medical me-1 text-danger"></i>{{ $medCondition }}
                            </div>
                            @elseif(!empty($app->medical_conditions) && is_array($app->medical_conditions))
                            <div class="text-muted small text-truncate text-uppercase mt-0.5" style="max-width: 220px;"
                                title="{{ strtoupper(implode(', ', $app->medical_conditions)) }}">
                                <i class="fas fa-notes-medical me-1 text-danger"></i>{{ strtoupper(implode(', ',
                                $app->medical_conditions)) }}
                            </div>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="text-center">
                            @if($app->status === 'Accepted')
                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill text-uppercase">
                                <i class="fas fa-check-circle me-1"></i>Accepted
                            </span>
                            @elseif($app->status === 'Rejected')
                            <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill text-uppercase">
                                <i class="fas fa-times-circle me-1"></i>Rejected
                            </span>
                            @else
                            <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-semibold text-uppercase">
                                <i class="fas fa-clock me-1"></i>For Review
                            </span>
                            @endif

                            @if($app->sms_status === 'Sent')
                            <div class="mt-1">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;" title="Awtomatikong Tagalog SMS naipadala noong {{ $app->sms_notified_at?->format('M d, Y h:i A') }}">
                                    <i class="fas fa-sms me-1"></i>SMS Naipadala
                                </span>
                            </div>
                            @elseif($app->sms_status === 'Failed')
                            <div class="mt-1">
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;" title="{{ $app->sms_error }}">
                                    <i class="fas fa-exclamation-triangle me-1"></i>SMS Nabigo
                                </span>
                            </div>
                            @elseif($app->sms_status === 'No Number')
                            <div class="mt-1">
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                    <i class="fas fa-phone-slash me-1"></i>Walang Numero
                                </span>
                            </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end pe-3">
                            <button type="button" class="btn btn-sm btn-primary-action px-3 py-1 rounded-3 shadow-xs"
                                onclick="openReviewModal({{ $app->id }})">
                                <i class="fas fa-clipboard-check me-1"></i> Review
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="mb-2">
                                <i class="fas fa-folder-open fa-3x text-slate-300" style="opacity: 0.5;"></i>
                            </div>
                            <p class="fw-semibold mb-1 text-secondary">Walang Nahanap na Online Intake Application</p>
                            <p class="small text-muted mb-0">Subukang baguhin ang mga filter o maghintay ng mga bagong
                                aplikasyon mula sa online form.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $applications->links() }}
        </div>
        @endif
    </div>

</div>

<!-- ==================================================== -->
<!-- APPLICATION REVIEW MODAL (Step 1 Staff Review Sheet) -->
<!-- ==================================================== -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">

            <!-- Modal Header -->
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #b11116 0%, #c4181e 50%, #8e0d11 100%) !important; border-bottom: none;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                        style="width:40px;height:40px; background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.3);">
                        <i class="fas fa-file-invoice fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="reviewModalLabel">
                            Review Online Intake Application &mdash; <span id="modalControlNumber"></span>
                        </h5>
                        <p class="text-white small mb-0" style="opacity: 0.9;">
                            Isinumite noong: <span id="modalDateSubmitted" class="fw-semibold text-white"></span>
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="modalStatusBadge" class="badge rounded-pill px-3 py-1.5 fw-semibold shadow-xs"></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4" id="reviewModalBody">

                <!-- Loading State -->
                <div id="modalLoadingSpinner" class="text-center py-5">
                    <div class="spinner-border" style="color: #b11116;" role="status"></div>
                    <p class="text-muted small mt-2">Kinukuha ang kumpletong detalye ng aplikasyon...</p>
                </div>

                <!-- Content Sheet -->
                <div id="modalContentDetails" class="d-none">
                    <section class="border rounded-3 p-3 mb-4" aria-labelledby="uploadedDocumentsHeading">
                        <h6 id="uploadedDocumentsHeading" class="fw-bold mb-3">Mga Isinumiteng Dokumento</h6>
                        <ul id="uploadedDocumentsList" class="list-unstyled mb-0"></ul>
                    </section>

                    <!-- Notification Banner for Accepted / Rejected -->
                    <div id="modalAuditNotice" class="alert d-none rounded-3 mb-4 p-3 shadow-xs"></div>

                    <!-- Row 1: Beneficiary Information -->
                    <div class="card border rounded-3 p-3 mb-3 bg-white">
                        <h6
                            class="fw-bold mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between" style="color: #b11116;">
                            <span><i class="fas fa-user me-2" style="color: #b11116;"></i>Impormasyon ng Benepisyaryo (Beneficiary
                                Information)</span>
                            <span class="badge bg-light text-dark border font-normal small text-uppercase" id="modalClientType"></span>
                        </h6>
                        <div class="row g-3 text-sm">
                            <div class="col-md-4">
                                <span class="text-muted small d-block">Buong Pangalan:</span>
                                <strong id="benFullName" class="text-dark text-uppercase"></strong>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted small d-block">Edad / Kasarian:</span>
                                <span id="benAgeSex" class="text-dark fw-medium text-uppercase"></span>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted small d-block">Petsa ng Kapanganakan:</span>
                                <span id="benBirthday" class="text-dark text-uppercase"></span>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted small d-block">Katayuang Sibil:</span>
                                <span id="benCivilStatus" class="text-dark text-uppercase"></span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Tirahan / Barangay:</span>
                                <span id="benAddress" class="text-dark text-uppercase"></span>, <strong id="benBarangay"
                                    style="color: #b11116;" class="text-uppercase"></strong>, SILANG, CAVITE
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted small d-block">Numero ng Kontak:</span>
                                <span id="benContact" class="text-dark font-monospace text-uppercase"></span>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted small d-block">Trabaho / Buwanang Sahod:</span>
                                <span id="benOccupationSalary" class="text-dark text-uppercase"></span>
                            </div>
                            <div class="col-12" id="benCategoryBox">
                                <span class="text-muted small d-block mb-1">Kategorya ng Benepisyaryo:</span>
                                <div id="benCategoriesList" class="d-flex gap-1.5 flex-wrap text-uppercase"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Representative Information (Conditional) -->
                    <div id="repSectionCard" class="card border rounded-3 p-3 mb-3 bg-white d-none">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color: #b11116;">
                            <i class="fas fa-user-shield me-2" style="color: #b11116;"></i>Impormasyon ng Kinatawan (Authorized
                            Representative)
                        </h6>
                        <div class="row g-3 text-sm">
                            <div class="col-md-4">
                                <span class="text-muted small d-block">Pangalan ng Kinatawan:</span>
                                <strong id="repFullName" class="text-dark text-uppercase"></strong>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted small d-block">Relasyon sa Benepisyaryo:</span>
                                <strong id="repRelationship" style="color: #b11116;" class="text-uppercase"></strong>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted small d-block">Edad / Kasarian:</span>
                                <span id="repAgeSex" class="text-dark text-uppercase"></span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted small d-block">Numero ng Kontak:</span>
                                <span id="repContact" class="text-dark font-monospace text-uppercase"></span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Tirahan:</span>
                                <span id="repAddress" class="text-dark text-uppercase"></span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Trabaho / Buwanang Kita:</span>
                                <span id="repOccupationSalary" class="text-dark text-uppercase"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Assistance Request & Medical Needs -->
                    <div class="card border rounded-3 p-3 mb-3 bg-white">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color: #b11116;">
                            <i class="fas fa-hand-holding-medical me-2" style="color: #b11116;"></i>Layunin at Pangangailangan ng
                            Tulong
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Hinihiling na Tulong (Assistance Purpose):</span>
                                <div id="modalAssistancePurpose" class="fw-bold text-dark mt-0.5 text-uppercase"></div>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Mga Karamdaman / Medikal na Kondisyon:</span>
                                <div id="modalMedicalConditions" class="mt-0.5 text-uppercase"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Family Composition Table -->
                    <div class="card border rounded-3 p-3 bg-white">
                        <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color: #b11116;">
                            <i class="fas fa-users me-2" style="color: #b11116;"></i>Komposisyon ng Pamilya (Family Composition)
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="font-size: 0.825rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pangalan</th>
                                        <th>Relasyon</th>
                                        <th>Edad</th>
                                        <th>Kasarian</th>
                                        <th>Katayuang Sibil</th>
                                        <th>Trabaho</th>
                                        <th>Buwanang Sahod</th>
                                    </tr>
                                </thead>
                                <tbody id="familyCompositionTableBody">
                                    <!-- populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer: Actions -->
            <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">
                    Isara (Close)
                </button>

                <div id="modalActionButtons" class="d-flex gap-2">
                    <!-- Dynamic Buttons: Accept or Reject -->
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ==================================================== -->
<!-- REJECT REASON MODAL -->
<!-- ==================================================== -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom py-3" style="background: #fef2f2;">
                <h6 class="modal-title fw-bold mb-0" style="color: #b11116 !important;">
                    <i class="fas fa-times-circle me-1" style="color: #b11116;"></i> Tanggihan ang Online Application
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Pakisaad ang opisyal na dahilan ng pagtanggi sa online intake application na ito.
                </p>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Dahilan ng Pag-reject <span
                            class="text-danger">*</span></label>
                    <select id="rejectReasonSelect" class="form-select form-select-sm mb-2"
                        onchange="handleRejectSelectChange(this)">
                        <option value="">Pumili ng dahilan...</option>
                        <option value="Hindi kwalipikado sa ilalim ng patakaran ng MSWDO">Hindi kwalipikado sa ilalim ng
                            patakaran ng MSWDO</option>
                        <option value="Hindi kumpleto o malabo ang mga impormasyon">Hindi kumpleto o malabo ang mga
                            impormasyon</option>
                        <option value="Hindi residente ng Bayan ng Silang">Hindi residente ng Bayan ng Silang</option>
                        <option value="Iba pa (Pakilagay sa ibaba)">Iba pa (Pakilagay sa ibaba)</option>
                    </select>
                    <textarea id="rejectionReasonText" rows="3" class="form-control form-control-sm rounded-3"
                        maxlength="500" placeholder="Ilarawan ang detalyadong dahilan..."></textarea>
                </div>
                <div>
                    <label class="form-label small fw-bold text-muted">Karagdagang Tala ng Staff (Opsyonal)</label>
                    <textarea id="rejectNotesText" rows="2" class="form-control form-control-sm rounded-3"
                        maxlength="500" placeholder="Internal review notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-3 bg-light">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Kanselahin</button>
                <button type="button" class="btn btn-sm btn-red-theme px-4 rounded-3 fw-bold"
                    onclick="submitRejectApplication()">
                    Kumpirmahin ang Pag-reject
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
    let currentActiveAppId = null;
let currentAppDetails = null;

function openReviewModal(appId) {
    currentActiveAppId = appId;
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    modal.show();

    // Reset view
    document.getElementById('modalLoadingSpinner').classList.remove('d-none');
    document.getElementById('modalContentDetails').classList.add('d-none');
    document.getElementById('modalActionButtons').innerHTML = '';
    document.getElementById('modalAuditNotice').classList.add('d-none');

    fetch(`/admin/financial/online-intakes/${appId}`)
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                Swal.fire('Error', res.message || 'Hindi ma-load ang detalye ng aplikasyon.', 'error');
                return;
            }

            const data = res.data;
            const documentsList = document.getElementById('uploadedDocumentsList');
            documentsList.replaceChildren();
            (data.documents || []).forEach(document => {
                const item = window.document.createElement('li');
                item.className = 'mb-2';
                const link = window.document.createElement('a');
                link.href = document.url;
                link.textContent = `${document.label} — ${document.name}`;
                link.style.overflowWrap = 'anywhere';
                item.appendChild(link);
                documentsList.appendChild(item);
            });
            if (!data.documents || data.documents.length === 0) {
                documentsList.textContent = 'Walang na-upload na dokumento.';
            }
            currentAppDetails = data;

            // Populate header info
            if (data.control_number) {
                document.getElementById('modalControlNumber').innerHTML = `<span class="badge text-white font-monospace fs-6 px-2.5 py-1 shadow-xs text-uppercase" style="background-color: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.4);"><i class="fas fa-check-circle me-1"></i>${data.control_number}</span>`;
            } else {
                document.getElementById('modalControlNumber').innerHTML = `<span class="badge font-monospace fs-6 px-2.5 py-1 text-uppercase" style="background-color: rgba(255,255,255,0.2); color: #ffffff; border: 1px solid rgba(255,255,255,0.3);"><i class="fas fa-hashtag me-1"></i>${data.reference_number || ('ONLINE-' + String(data.id).padStart(5, '0'))} <span class="fw-normal ms-1 small" style="opacity: 0.9;">(WALANG CONTROL NO. - PENDING REVIEW)</span></span>`;
            }
            document.getElementById('modalDateSubmitted').textContent = String(data.date_submitted || '').toUpperCase();
            document.getElementById('modalClientType').textContent = `CLIENT STATUS: ${String(data.client_type || 'NEW').toUpperCase()}`;

            // Status Badge
            const statusBadge = document.getElementById('modalStatusBadge');
            statusBadge.className = `badge rounded-pill px-3 py-1.5 fw-semibold text-uppercase ${data.status_badge}`;
            statusBadge.textContent = String(data.status || '').toUpperCase();

            // Populate Beneficiary Info
            const ben = data.beneficiary;
            document.getElementById('benFullName').textContent = (ben.full_name || '').toUpperCase();
            document.getElementById('benAgeSex').textContent = `${ben.age} Y/O / ${(ben.sex || '').toUpperCase()}`;
            document.getElementById('benBirthday').textContent = (ben.birthday || 'N/A').toUpperCase();
            document.getElementById('benCivilStatus').textContent = (ben.civil_status || 'N/A').toUpperCase();
            document.getElementById('benAddress').textContent = (ben.address || 'N/A').toUpperCase();
            document.getElementById('benBarangay').textContent = (ben.barangay || '').toUpperCase();
            document.getElementById('benContact').textContent = ben.contact_number || 'N/A';
            document.getElementById('benOccupationSalary').textContent = `${(ben.occupation || 'NONE / UNEMPLOYED').toUpperCase()} (${ben.monthly_salary ? '₱' + ben.monthly_salary : '0.00'})`;

            // Categories
            const catList = document.getElementById('benCategoriesList');
            catList.innerHTML = '';
            if (ben.category) {
                catList.innerHTML += `<span class="badge text-white shadow-xs text-uppercase" style="background-color: #b11116;">${String(ben.category).toUpperCase()}</span>`;
            }
            if (ben.categories && Array.isArray(ben.categories)) {
                ben.categories.forEach(c => {
                    if (c !== ben.category) {
                        catList.innerHTML += `<span class="badge bg-light text-dark border text-uppercase">${String(c).toUpperCase()}</span>`;
                    }
                });
            }
            if (!catList.innerHTML) {
                catList.innerHTML = '<span class="text-muted small text-uppercase">WALANG NAKATALANG ESPESYAL NA KATEGORYA</span>';
            }

            // Representative
            const repCard = document.getElementById('repSectionCard');
            if (data.has_representative && data.representative) {
                repCard.classList.remove('d-none');
                const rep = data.representative;
                document.getElementById('repFullName').textContent = (rep.full_name || '').toUpperCase();
                document.getElementById('repRelationship').textContent = (rep.relationship || 'REPRESENTATIVE').toUpperCase();
                document.getElementById('repAgeSex').textContent = `${rep.age || 'N/A'} Y/O / ${(rep.sex || 'N/A').toUpperCase()}`;
                document.getElementById('repContact').textContent = rep.contact_number || 'WALANG CONTACT';
                document.getElementById('repAddress').textContent = (rep.address || 'KAPAREHO NG BENEPISYARYO').toUpperCase();
                document.getElementById('repOccupationSalary').textContent = `${(rep.occupation || 'NONE').toUpperCase()} (${rep.monthly_salary ? '₱' + rep.monthly_salary : '0.00'})`;
            } else {
                repCard.classList.add('d-none');
            }

            // Assistance purpose & medical
            const assistancePurpose = (data.assistance_type || data.display_assistance_purpose || 'GENERAL FINANCIAL ASSISTANCE').toUpperCase();
            document.getElementById('modalAssistancePurpose').textContent = assistancePurpose;

            const medContainer = document.getElementById('modalMedicalConditions');
            let medList = [];
            if (Array.isArray(data.medical_conditions) && data.medical_conditions.length > 0) {
                medList = data.medical_conditions.filter(m => m && String(m).trim() !== '');
            } else if (data.display_medical_condition) {
                medList = [data.display_medical_condition];
            } else if (data.medical_condition_other) {
                medList = [data.medical_condition_other];
            } else if (data.assistance_purpose && data.assistance_purpose !== data.assistance_type && data.assistance_purpose !== 'GENERAL ASSISTANCE REQUEST') {
                medList = [data.assistance_purpose];
            }

            if (medList.length > 0) {
                medContainer.innerHTML = medList.map(m => `<span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1 mb-1 text-uppercase">${String(m).toUpperCase()}</span>`).join(' ');
            } else {
                medContainer.innerHTML = '<span class="text-muted small text-uppercase">WALANG NAKASAAD NA MEDIKAL NA KARAMDAMAN</span>';
            }

            // Family Composition
            const famBody = document.getElementById('familyCompositionTableBody');
            famBody.innerHTML = '';
            if (data.family_composition && data.family_composition.length > 0) {
                data.family_composition.forEach(member => {
                    famBody.innerHTML += `
                        <tr class="text-uppercase">
                            <td><strong>${(member.name || 'N/A').toUpperCase()}</strong></td>
                            <td>${(member.relationship || 'N/A').toUpperCase()}</td>
                            <td>${member.age || 'N/A'}</td>
                            <td>${(member.sex || 'N/A').toUpperCase()}</td>
                            <td>${(member.civil_status || 'N/A').toUpperCase()}</td>
                            <td>${(member.occupation || 'N/A').toUpperCase()}</td>
                            <td>${member.monthly_salary ? '₱' + Number(member.monthly_salary).toLocaleString() : '₱0.00'}</td>
                        </tr>
                    `;
                });
            } else {
                famBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-2 text-uppercase">WALANG NAKALISTANG IBANG MIYEMBRO NG PAMILYA.</td></tr>';
            }

            // Status Notice & Action Buttons
            const noticeBox = document.getElementById('modalAuditNotice');
            const actionContainer = document.getElementById('modalActionButtons');
            actionContainer.innerHTML = '';

            let smsStatusBanner = '';
            if (data.sms_info) {
                if (data.sms_info.sms_status === 'Sent') {
                    smsStatusBanner = `
                        <div class="mt-2.5 pt-2 border-top border-success-subtle small text-success d-flex align-items-center gap-2">
                            <i class="fas fa-sms fa-lg"></i>
                            <span><strong>Awtomatikong Tagalog SMS:</strong> Matagumpay na naipadala ang abiso sa aplikante (${data.sms_info.applicant_contact || 'Numero'}) noong ${data.sms_info.sms_notified_at || 'kamakailan'}.</span>
                        </div>
                    `;
                } else if (data.sms_info.sms_status === 'Failed') {
                    smsStatusBanner = `
                        <div class="mt-2.5 pt-2 border-top border-danger-subtle small text-danger d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <span><strong>SMS Status:</strong> Nabigo ang pagpapadala ng SMS (${data.sms_info.sms_error || 'Provider issue'}).</span>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm py-0.5 px-3 rounded-pill fw-bold" onclick="retrySmsNotification(${data.id})">
                                <i class="fas fa-redo me-1"></i> Muling Ipadala ang SMS (Retry)
                            </button>
                        </div>
                    `;
                } else if (data.sms_info.sms_status === 'No Number') {
                    smsStatusBanner = `
                        <div class="mt-2.5 pt-2 border-top border-secondary-subtle small text-muted">
                            <i class="fas fa-phone-slash me-1"></i>
                            <span>Walang nakatalang numero ng kontak kaya hindi napadalhan ng SMS.</span>
                        </div>
                    `;
                }
            }

            if (data.status === 'Accepted') {
                noticeBox.className = 'alert alert-success d-block rounded-3 mb-4 shadow-xs';
                noticeBox.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle fa-lg text-success"></i>
                        <div>
                            <strong>Matagumpay na Naitanggap at Nailipat sa Step 1:</strong> 
                            Sinuri ni <u>${data.review_info.reviewed_by || 'Officer'}</u> noong ${data.review_info.reviewed_at || 'N/A'}. 
                            <a href="{{ route('admin.financial.financialstep1') }}" class="fw-bold text-success text-decoration-underline ms-2">Buksan ang Step 1 Intake Directory</a>
                        </div>
                    </div>
                    ${smsStatusBanner}
                `;
                noticeBox.classList.remove('d-none');
            } else if (data.status === 'Rejected') {
                noticeBox.className = 'alert alert-danger d-block rounded-3 mb-4 shadow-xs';
                noticeBox.innerHTML = `
                    <div class="d-flex align-items-start gap-2">
                        <i class="fas fa-times-circle fa-lg text-danger mt-1"></i>
                        <div>
                            <strong>Tinanggihan ang Aplikasyon (Rejected):</strong><br>
                            <span>Dahilan: ${data.review_info.rejection_reason || 'Walang nakasaad'}</span><br>
                            <small class="text-muted">Sinuri ni ${data.review_info.reviewed_by || 'Officer'} noong ${data.review_info.reviewed_at || 'N/A'}</small>
                        </div>
                    </div>
                    ${smsStatusBanner}
                `;
                noticeBox.classList.remove('d-none');
            } else {
                // "For Review" State -> Allow Accept or Reject
                actionContainer.innerHTML = `
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-3 px-3 fw-semibold" onclick="openRejectModal()">
                        <i class="fas fa-times me-1"></i> Tanggihan (Reject)
                    </button>
                    <button type="button" class="btn btn-primary-action btn-sm rounded-3 px-4 shadow-sm" onclick="confirmAcceptApplication(false)">
                        <i class="fas fa-check-circle me-1"></i> Tanggapin at Ilipat sa Step 1 (Accept)
                    </button>
                `;
            }

            // Show details
            document.getElementById('modalLoadingSpinner').classList.add('d-none');
            document.getElementById('modalContentDetails').classList.remove('d-none');
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Nagkaroon ng problema sa pagkuha ng impormasyon.', 'error');
        });
}

function confirmAcceptApplication(overrideDuplicate = false) {
    if (!currentActiveAppId) return;

    Swal.fire({
        title: overrideDuplicate ? 'Kumpirmahin ang Paglipat sa Step 1' : 'Tanggapin at Ilipat sa Step 1?',
        text: overrideDuplicate 
            ? 'Sigurado ka bang nais mong ipagpatuloy ang pag-accept sa kabila ng babala sa 6-month validity?' 
            : 'Ang aplikasyong ito ay ililipat bilang opisyal na Step 1 Intake record para sa araw na ito.',
        icon: overrideDuplicate ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: '#b11116',
        cancelButtonColor: '#64748B',
        confirmButtonText: overrideDuplicate ? 'Oo, Ipagpatuloy ang Pag-accept' : 'Oo, Tanggapin (Accept)',
        cancelButtonText: 'Kanselahin'
    }).then((result) => {
        if (result.isConfirmed) {
            executeAccept(overrideDuplicate);
        }
    });
}

function executeAccept(overrideDuplicate) {
    Swal.fire({
        title: 'Pinoproseso...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`/admin/financial/online-intakes/${currentActiveAppId}/accept`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            confirm_duplicate_override: overrideDuplicate
        })
    })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(({ status, body }) => {
        if (status === 409 && body.is_duplicate) {
            // Duplicate detected
            Swal.fire({
                title: 'Babala: Duplikasyon Nakita!',
                html: `
                    <div class="text-start small p-2 bg-light rounded border text-danger mb-2">
                        ${body.warning_message || body.message}
                    </div>
                    <p class="small text-muted mb-0">Nais mo bang ipagpatuloy ang pag-accept bilang awtorisadong staff?</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b11116',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ipagpatuloy Pa Rin (Override)',
                cancelButtonText: 'Huwag Ipagpatuloy'
            }).then(overrideRes => {
                if (overrideRes.isConfirmed) {
                    executeAccept(true);
                }
            });
            return;
        }

        if (body.success) {
            let smsNotice = '';
            if (body.data && body.data.sms) {
                if (body.data.sms.success) {
                    smsNotice = `
                        <div class="alert alert-success py-2 px-3 text-center my-2 border border-success rounded-3 bg-success-subtle small">
                            <i class="fas fa-sms me-1"></i> <strong>Awtomatikong SMS:</strong> Matagumpay na naipadala ang mensahe sa aplikante!
                        </div>
                    `;
                } else if (body.data.sms.error) {
                    smsNotice = `
                        <div class="alert alert-warning py-2 px-3 text-center my-2 border border-warning rounded-3 bg-warning-subtle small">
                            <i class="fas fa-exclamation-triangle me-1"></i> <strong>Paalala sa SMS:</strong> ${body.data.sms.error}
                        </div>
                    `;
                }
            }

            Swal.fire({
                title: 'Naitanggap Na!',
                html: `
                    <p class="text-secondary mb-2">${body.message}</p>
                    <div class="alert alert-success py-2.5 px-3 text-center my-2 border border-success rounded-3 bg-success-subtle">
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size:0.72rem;">Itinalagang Opisyal na Control Number (Step 1):</small>
                        <strong class="font-monospace fs-4 text-success">${body.data.control_number}</strong>
                    </div>
                    ${smsNotice}
                    <small class="text-muted d-block mt-2">Nailipat na ang rekord sa regular na Step 1 Intake Directory.</small>
                `,
                icon: 'success',
                confirmButtonColor: '#b11116',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Hindi Naitanggap',
                text: body.message || 'May naganap na error sa paglipat ng rekord.',
                icon: 'error',
                confirmButtonColor: '#b11116'
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Nagkaroon ng problema sa koneksyon.', 'error');
    });
}

function openRejectModal() {
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
    document.getElementById('rejectReasonSelect').value = '';
    document.getElementById('rejectionReasonText').value = '';
    document.getElementById('rejectNotesText').value = '';
    rejectModal.show();
}

function handleRejectSelectChange(selectEl) {
    const val = selectEl.value;
    if (val && !val.includes('Iba pa')) {
        document.getElementById('rejectionReasonText').value = val;
    } else {
        document.getElementById('rejectionReasonText').value = '';
    }
}

function submitRejectApplication() {
    const reason = document.getElementById('rejectionReasonText').value.trim();
    const notes = document.getElementById('rejectNotesText').value.trim();

    if (!reason) {
        Swal.fire('Kailangan ang Dahilan', 'Pakisaad ang dahilan ng pag-reject.', 'warning');
        return;
    }

    Swal.fire({
        title: 'I-reject ang Application?',
        text: 'Sigurado ka bang nais mong tanggihan ang online intake application na ito? Awtomatikong magpapadala ng magalang na mensahe abiso sa aplikante.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b11116',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Oo, I-reject',
        cancelButtonText: 'Kanselahin'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Pinoproseso...',
                text: 'Itinatala ang pagtanggi at nagpapadala ng magalang na Tagalog SMS...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/financial/online-intakes/${currentActiveAppId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rejection_reason: reason,
                    review_notes: notes
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    let smsNotice = '';
                    if (res.data && res.data.sms) {
                        if (res.data.sms.success) {
                            smsNotice = `
                                <div class="alert alert-success py-2 px-3 text-center my-2 border border-success rounded-3 bg-success-subtle small">
                                    <i class="fas fa-sms me-1"></i> <strong>Awtomatikong SMS:</strong> Matagumpay na naipadala ang magalang na Tagalog SMS notification sa aplikante!
                                </div>
                            `;
                        } else if (res.data.sms.error) {
                            smsNotice = `
                                <div class="alert alert-warning py-2 px-3 text-center my-2 border border-warning rounded-3 bg-warning-subtle small">
                                    <i class="fas fa-exclamation-triangle me-1"></i> <strong>Paalala sa SMS:</strong> ${res.data.sms.error}
                                </div>
                            `;
                        }
                    }

                    Swal.fire({
                        title: 'Nai-reject Na',
                        html: `
                            <p class="text-secondary mb-2">${res.message}</p>
                            ${smsNotice}
                        `,
                        icon: 'success',
                        confirmButtonColor: '#b11116'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message || 'Hindi nai-reject ang aplikasyon.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Nagkaroon ng problema sa koneksyon.', 'error');
            });
        }
    });
}

function retrySmsNotification(id) {
    Swal.fire({
        title: 'Ipadala ang SMS Abiso?',
        text: 'Nais mo bang subukang muling ipadala ang opisyal na Tagalog SMS notification?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#b11116',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Oo, Ipadala',
        cancelButtonText: 'Kanselahin'
    }).then((res) => {
        if (res.isConfirmed) {
            Swal.fire({
                title: 'Nagpapadala ng SMS...',
                text: 'Pakihintay habang nakikipag-ugnayan sa SMS gateway...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/financial/online-intakes/${id}/resend-sms`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json().then(data => ({ status: r.status, body: data })))
            .then(({ status, body }) => {
                if (body.success) {
                    Swal.fire('Naipadala Na!', body.message || 'Matagumpay na naipadala ang SMS.', 'success').then(() => {
                        openReviewModal(id);
                    });
                } else {
                    Swal.fire('Hindi Naipadala', body.message || 'Nabigo ang pagpapadala ng SMS.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Nagkaroon ng problema sa koneksyon.', 'error');
            });
        }
    });
}
</script>
@endsection
