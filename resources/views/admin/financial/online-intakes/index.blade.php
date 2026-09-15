@extends('layouts.financial')

@section('title', 'Intake Online Applications - MSWDO Admin')
@section('page-title', 'Financial Assistance Module')

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

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">
                <i class="fas fa-globe me-2 text-primary"></i>Intake Online Applications
            </h4>
            <p class="text-muted small mb-0">
                Suriin at patunayan ang mga aplikasyong isinumite sa pamamagitan ng Online Financial Assistance bago ilipat sa regular na Step 1 Intake.
            </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('admin.financial.financialstep1') }}" class="btn btn-outline-primary btn-sm rounded-3 px-3 shadow-xs">
                <i class="fas fa-arrow-left me-1"></i>
                <span>Pumunta sa Step 1 Intake</span>
            </a>
        </div>
    </div>

    <!-- Status Tabs / Summary Metric Pills -->
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
        <a href="{{ route('admin.financial.online-intakes.index') }}" 
           class="btn btn-sm rounded-pill px-3 {{ (!request()->filled('status') || request('status') === 'All') ? 'btn-primary' : 'btn-light border' }}"
           style="{{ (!request()->filled('status') || request('status') === 'All') ? 'background:#1A237E; border-color:#1A237E;' : '' }}">
            <span>Lahat (All)</span>
            <span class="badge bg-white text-dark ms-1.5 rounded-pill">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('admin.financial.online-intakes.index', array_merge(request()->except(['status', 'page']), ['status' => 'For Review'])) }}" 
           class="btn btn-sm rounded-pill px-3 {{ request('status') === 'For Review' ? 'btn-warning text-dark fw-bold' : 'btn-light border' }}">
            <i class="fas fa-clock me-1 text-warning"></i>
            <span>For Review (Pending)</span>
            <span class="badge bg-dark text-white ms-1.5 rounded-pill">{{ $pendingCount }}</span>
        </a>
        <a href="{{ route('admin.financial.online-intakes.index', array_merge(request()->except(['status', 'page']), ['status' => 'Accepted'])) }}" 
           class="btn btn-sm rounded-pill px-3 {{ request('status') === 'Accepted' ? 'btn-success text-white fw-bold' : 'btn-light border' }}">
            <i class="fas fa-check-circle me-1 text-success"></i>
            <span>Accepted (Transferred)</span>
            <span class="badge bg-white text-success ms-1.5 rounded-pill">{{ $acceptedCount }}</span>
        </a>
        <a href="{{ route('admin.financial.online-intakes.index', array_merge(request()->except(['status', 'page']), ['status' => 'Rejected'])) }}" 
           class="btn btn-sm rounded-pill px-3 {{ request('status') === 'Rejected' ? 'btn-danger text-white fw-bold' : 'btn-light border' }}">
            <i class="fas fa-times-circle me-1 text-danger"></i>
            <span>Rejected</span>
            <span class="badge bg-white text-danger ms-1.5 rounded-pill">{{ $rejectedCount }}</span>
        </a>
    </div>

    <!-- Compound Filtering Form -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
        <form action="{{ route('admin.financial.online-intakes.index') }}" method="GET" class="row g-3 align-items-end">
            
            <!-- Search Query -->
            <div class="col-md-4 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1">
                    <i class="fas fa-search me-1"></i> Search Name / Control No.
                </label>
                <input type="text" name="search" class="form-control form-control-sm rounded-3" 
                       placeholder="Hal. Juan Dela Cruz o Control No." 
                       value="{{ request('search') }}">
            </div>

            <!-- Status Filter -->
            <div class="col-md-3 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1">Status</label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="All" {{ request('status') == 'All' || !request()->filled('status') ? 'selected' : '' }}>Lahat ng Status</option>
                    <option value="For Review" {{ request('status') == 'For Review' ? 'selected' : '' }}>For Review</option>
                    <option value="Accepted" {{ request('status') == 'Accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Barangay Filter -->
            <div class="col-md-3 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1">Barangay</label>
                <select name="barangay" class="form-select form-select-sm rounded-3">
                    <option value="All">Lahat ng Barangay</option>
                    @foreach($barangays as $b)
                    <option value="{{ $b }}" {{ request('barangay') == $b ? 'selected' : '' }}>{{ $b }}</option>
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
                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-4 fw-semibold shadow-xs" style="background:#1A237E; border-color:#1A237E;">
                    <i class="fas fa-filter me-1"></i> Apply Filters
                </button>
                <a href="{{ route('admin.financial.online-intakes.index') }}" class="btn btn-sm btn-light border rounded-3 px-3 fw-semibold text-secondary" title="Reset all filters">
                    <i class="fas fa-undo me-1"></i> Reset / Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    Mga Isinumiteng Online Intake Application
                </h6>
                <span class="text-muted small">Ipinapakita: {{ $applications->firstItem() ?? 0 }} hanggang {{ $applications->lastItem() ?? 0 }} sa kabuuang {{ $applications->total() }} tala</span>
            </div>
            @if(request()->anyFilled(['search', 'status', 'barangay', 'date_from', 'date_to']))
            <span class="badge bg-light text-primary border px-2.5 py-1.5 rounded-pill small fw-semibold">
                <i class="fas fa-filter me-1"></i> May Aktibong Filter
            </span>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                <thead class="table-light text-secondary" style="font-size: 0.775rem; text-transform: uppercase; letter-spacing: 0.05em;">
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
                                <div class="font-monospace fw-bold text-primary" style="font-size: 0.85rem;">{{ $app->control_number }}</div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 0.68rem;">
                                    <i class="fas fa-check-circle me-1"></i>Assigned (Step 1)
                                </span>
                            @else
                                <div class="font-monospace text-muted small fw-semibold">
                                    <i class="fas fa-hashtag me-0.5 text-secondary"></i>{{ $app->reference_number }}
                                </div>
                                <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem;" title="Ang Control Number ay itatalaga lamang kapag opisyal nang tinanggap (Accepted) ng staff.">
                                    <i class="fas fa-hourglass-half me-1 text-warning"></i>Walang Control No. (Pending)
                                </span>
                            @endif
                        </td>

                        <!-- Date Submitted -->
                        <td class="text-muted small">
                            <div class="fw-semibold text-dark">{{ $app->date_submitted ? $app->date_submitted->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">{{ $app->date_submitted ? $app->date_submitted->format('h:i A') : '' }}</div>
                        </td>

                        <!-- Beneficiary Info -->
                        <td>
                            <div class="fw-bold text-dark">{{ $app->beneficiary_full_name }}</div>
                            <div class="text-muted small">
                                <span>{{ $app->beneficiary_age }} taong gulang</span> &bull; 
                                <span>{{ $app->beneficiary_sex }}</span>
                                @if($app->has_representative && $app->rep_full_name)
                                <span class="d-block text-secondary mt-0.5" style="font-size: 0.75rem;">
                                    <i class="fas fa-user-shield me-1 text-muted"></i>Rep: {{ $app->rep_full_name }} ({{ $app->rep_relationship ?: 'Kinatawan' }})
                                </span>
                                @endif
                            </div>
                        </td>

                        <!-- Barangay -->
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">{{ $app->beneficiary_barangay }}</span>
                        </td>

                        <!-- Purpose -->
                        <td>
                            <div class="text-dark fw-medium">{{ $app->assistance_purpose ?: 'General Assistance' }}</div>
                            @if(!empty($app->medical_conditions) && is_array($app->medical_conditions))
                            <div class="text-muted small text-truncate" style="max-width: 220px;" title="{{ implode(', ', $app->medical_conditions) }}">
                                <i class="fas fa-notes-medical me-1 text-danger"></i>{{ implode(', ', $app->medical_conditions) }}
                            </div>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="text-center">
                            @if($app->status === 'Accepted')
                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i>Accepted
                                </span>
                            @elseif($app->status === 'Rejected')
                                <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill">
                                    <i class="fas fa-times-circle me-1"></i>Rejected
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-semibold">
                                    <i class="fas fa-clock me-1"></i>For Review
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end pe-3">
                            <button type="button" 
                                    class="btn btn-sm btn-primary-action px-3 py-1 rounded-3 shadow-xs" 
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
                            <p class="small text-muted mb-0">Subukang baguhin ang mga filter o maghintay ng mga bagong aplikasyon mula sa online form.</p>
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
            <div class="modal-header border-bottom py-3 px-4" style="background: #F8FAFC;">
                <div class="d-flex align-items-center gap-3">
                    <div class="h-10 w-10 rounded-circle bg-primary text-white d-flex align-items-center justify-center p-2" style="width:40px;height:40px;">
                        <i class="fas fa-file-invoice fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-primary mb-0" id="reviewModalLabel">
                            Review Online Intake Application &bull; <span id="modalControlNumber"></span>
                        </h5>
                        <p class="text-muted small mb-0">
                            Isinumite noong: <span id="modalDateSubmitted" class="fw-medium text-dark"></span>
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="modalStatusBadge" class="badge rounded-pill px-3 py-1.5 fw-semibold"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4" id="reviewModalBody">
                
                <!-- Loading State -->
                <div id="modalLoadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2">Kinukuha ang kumpletong detalye ng aplikasyon...</p>
                </div>

                <!-- Content Sheet -->
                <div id="modalContentDetails" class="d-none">

                    <!-- Notification Banner for Accepted / Rejected -->
                    <div id="modalAuditNotice" class="alert d-none rounded-3 mb-4 p-3 shadow-xs"></div>

                    <!-- Row 1: Beneficiary Information -->
                    <div class="card border rounded-3 p-3 mb-3 bg-white">
                        <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-user me-2 text-primary"></i>Impormasyon ng Benepisyaryo (Beneficiary Information)</span>
                            <span class="badge bg-light text-dark border font-normal small" id="modalClientType"></span>
                        </h6>
                        <div class="row g-3 text-sm">
                            <div class="col-md-4">
                                <span class="text-muted small d-block">Buong Pangalan:</span>
                                <strong id="benFullName" class="text-dark"></strong>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted small d-block">Edad / Kasarian:</span>
                                <span id="benAgeSex" class="text-dark fw-medium"></span>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted small d-block">Petsa ng Kapanganakan:</span>
                                <span id="benBirthday" class="text-dark"></span>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted small d-block">Katayuang Sibil:</span>
                                <span id="benCivilStatus" class="text-dark"></span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Tirahan / Barangay:</span>
                                <span id="benAddress" class="text-dark"></span>, <strong id="benBarangay" class="text-primary"></strong>, Silang, Cavite
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted small d-block">Numero ng Kontak:</span>
                                <span id="benContact" class="text-dark font-monospace"></span>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted small d-block">Trabaho / Buwanang Sahod:</span>
                                <span id="benOccupationSalary" class="text-dark"></span>
                            </div>
                            <div class="col-12" id="benCategoryBox">
                                <span class="text-muted small d-block mb-1">Kategorya ng Benepisyaryo:</span>
                                <div id="benCategoriesList" class="d-flex gap-1.5 flex-wrap"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Representative Information (Conditional) -->
                    <div id="repSectionCard" class="card border rounded-3 p-3 mb-3 bg-white d-none">
                        <h6 class="fw-bold text-secondary mb-3 pb-2 border-bottom">
                            <i class="fas fa-user-shield me-2 text-secondary"></i>Impormasyon ng Kinatawan (Authorized Representative)
                        </h6>
                        <div class="row g-3 text-sm">
                            <div class="col-md-4">
                                <span class="text-muted small d-block">Pangalan ng Kinatawan:</span>
                                <strong id="repFullName" class="text-dark"></strong>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted small d-block">Relasyon sa Benepisyaryo:</span>
                                <strong id="repRelationship" class="text-primary"></strong>
                            </div>
                            <div class="col-md-2">
                                <span class="text-muted small d-block">Edad / Kasarian:</span>
                                <span id="repAgeSex" class="text-dark"></span>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted small d-block">Numero ng Kontak:</span>
                                <span id="repContact" class="text-dark font-monospace"></span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Tirahan:</span>
                                <span id="repAddress" class="text-dark"></span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Trabaho / Buwanang Kita:</span>
                                <span id="repOccupationSalary" class="text-dark"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Assistance Request & Medical Needs -->
                    <div class="card border rounded-3 p-3 mb-3 bg-white">
                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                            <i class="fas fa-hand-holding-medical me-2 text-primary"></i>Layunin at Pangangailangan ng Tulong
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Hinihiling na Tulong (Assistance Purpose):</span>
                                <div id="modalAssistancePurpose" class="fw-bold text-dark mt-0.5"></div>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Mga Karamdaman / Medikal na Kondisyon:</span>
                                <div id="modalMedicalConditions" class="mt-0.5"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Family Composition Table -->
                    <div class="card border rounded-3 p-3 bg-white">
                        <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                            <i class="fas fa-users me-2 text-primary"></i>Komposisyon ng Pamilya (Family Composition)
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
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-danger mb-0">
                    <i class="fas fa-times-circle me-1"></i> Tanggihan ang Online Application
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Pakisaad ang opisyal na dahilan ng pagtanggi sa online intake application na ito.
                </p>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">Dahilan ng Pag-reject <span class="text-danger">*</span></label>
                    <select id="rejectReasonSelect" class="form-select form-select-sm mb-2" onchange="handleRejectSelectChange(this)">
                        <option value="">Pumili ng dahilan...</option>
                        <option value="Hindi kwalipikado sa ilalim ng patakaran ng MSWDO">Hindi kwalipikado sa ilalim ng patakaran ng MSWDO</option>
                        <option value="May umiiral na 6-Month Validity duplicate restriction">May umiiral na 6-Month Validity duplicate restriction</option>
                        <option value="Hindi kumpleto o malabo ang mga impormasyon">Hindi kumpleto o malabo ang mga impormasyon</option>
                        <option value="Hindi residente ng Bayan ng Silang">Hindi residente ng Bayan ng Silang</option>
                        <option value="Iba pa (Pakilagay sa ibaba)">Iba pa (Pakilagay sa ibaba)</option>
                    </select>
                    <textarea id="rejectionReasonText" rows="3" class="form-control form-control-sm rounded-3" placeholder="Ilarawan ang detalyadong dahilan..."></textarea>
                </div>
                <div>
                    <label class="form-label small fw-bold text-muted">Karagdagang Tala ng Staff (Opsyonal)</label>
                    <textarea id="rejectNotesText" rows="2" class="form-control form-control-sm rounded-3" placeholder="Internal review notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-3 bg-light">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Kanselahin</button>
                <button type="button" class="btn btn-sm btn-danger px-4 rounded-3 fw-bold" onclick="submitRejectApplication()">
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
            currentAppDetails = data;

            // Populate header info
            if (data.control_number) {
                document.getElementById('modalControlNumber').innerHTML = `<span class="badge bg-primary text-white font-monospace fs-6 px-2.5 py-1 shadow-xs"><i class="fas fa-check-circle me-1"></i>${data.control_number}</span>`;
            } else {
                document.getElementById('modalControlNumber').innerHTML = `<span class="badge bg-light text-secondary border font-monospace fs-6 px-2.5 py-1"><i class="fas fa-hashtag me-1"></i>${data.reference_number || ('ONLINE-' + String(data.id).padStart(5, '0'))} <span class="fw-normal text-muted ms-1 small">(Walang Control No. - Pending Review)</span></span>`;
            }
            document.getElementById('modalDateSubmitted').textContent = data.date_submitted;
            document.getElementById('modalClientType').textContent = `Client Status: ${data.client_type || 'New'}`;

            // Status Badge
            const statusBadge = document.getElementById('modalStatusBadge');
            statusBadge.className = `badge rounded-pill px-3 py-1.5 fw-semibold ${data.status_badge}`;
            statusBadge.textContent = data.status;

            // Populate Beneficiary Info
            const ben = data.beneficiary;
            document.getElementById('benFullName').textContent = ben.full_name;
            document.getElementById('benAgeSex').textContent = `${ben.age} y/o &bull; ${ben.sex}`;
            document.getElementById('benBirthday').textContent = ben.birthday;
            document.getElementById('benCivilStatus').textContent = ben.civil_status;
            document.getElementById('benAddress').textContent = ben.address;
            document.getElementById('benBarangay').textContent = ben.barangay;
            document.getElementById('benContact').textContent = ben.contact_number;
            document.getElementById('benOccupationSalary').textContent = `${ben.occupation} (${ben.monthly_salary ? '₱' + ben.monthly_salary : 'Walang Kita'})`;

            // Categories
            const catList = document.getElementById('benCategoriesList');
            catList.innerHTML = '';
            if (ben.category) {
                catList.innerHTML += `<span class="badge bg-primary text-white">${ben.category}</span>`;
            }
            if (ben.categories && Array.isArray(ben.categories)) {
                ben.categories.forEach(c => {
                    if (c !== ben.category) {
                        catList.innerHTML += `<span class="badge bg-light text-dark border">${c}</span>`;
                    }
                });
            }
            if (!catList.innerHTML) {
                catList.innerHTML = '<span class="text-muted small">Walang nakatalang espesyal na kategorya</span>';
            }

            // Representative
            const repCard = document.getElementById('repSectionCard');
            if (data.has_representative && data.representative) {
                repCard.classList.remove('d-none');
                const rep = data.representative;
                document.getElementById('repFullName').textContent = rep.full_name;
                document.getElementById('repRelationship').textContent = rep.relationship;
                document.getElementById('repAgeSex').textContent = `${rep.age || 'N/A'} y/o &bull; ${rep.sex || 'N/A'}`;
                document.getElementById('repContact').textContent = rep.contact_number || 'Walang Contact';
                document.getElementById('repAddress').textContent = rep.address || 'Kapareho ng benepisyaryo';
                document.getElementById('repOccupationSalary').textContent = `${rep.occupation || 'Wala'} (${rep.monthly_salary ? '₱' + rep.monthly_salary : 'Walang Kita'})`;
            } else {
                repCard.classList.add('d-none');
            }

            // Assistance purpose & medical
            document.getElementById('modalAssistancePurpose').textContent = data.assistance_purpose || 'General Financial Assistance';
            const medContainer = document.getElementById('modalMedicalConditions');
            if (data.medical_conditions && data.medical_conditions.length > 0) {
                medContainer.innerHTML = data.medical_conditions.map(m => `<span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1 mb-1">${m}</span>`).join(' ');
            } else {
                medContainer.innerHTML = '<span class="text-muted small">Walang nakasaad na medikal na karamdaman</span>';
            }

            // Family Composition
            const famBody = document.getElementById('familyCompositionTableBody');
            famBody.innerHTML = '';
            if (data.family_composition && data.family_composition.length > 0) {
                data.family_composition.forEach(member => {
                    famBody.innerHTML += `
                        <tr>
                            <td><strong>${member.name || 'N/A'}</strong></td>
                            <td>${member.relationship || 'N/A'}</td>
                            <td>${member.age || 'N/A'}</td>
                            <td>${member.sex || 'N/A'}</td>
                            <td>${member.civil_status || 'N/A'}</td>
                            <td>${member.occupation || 'N/A'}</td>
                            <td>${member.monthly_salary ? '₱' + Number(member.monthly_salary).toLocaleString() : '₱0.00'}</td>
                        </tr>
                    `;
                });
            } else {
                famBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-2">Walang nakalistang ibang miyembro ng pamilya.</td></tr>';
            }

            // Status Notice & Action Buttons
            const noticeBox = document.getElementById('modalAuditNotice');
            const actionContainer = document.getElementById('modalActionButtons');
            actionContainer.innerHTML = '';

            if (data.status === 'Accepted') {
                noticeBox.className = 'alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4';
                noticeBox.innerHTML = `
                    <i class="fas fa-check-circle fa-lg text-success"></i>
                    <div>
                        <strong>Matagumpay na Naitanggap at Nailipat sa Step 1:</strong> 
                        Sinuri ni <u>${data.review_info.reviewed_by || 'Officer'}</u> noong ${data.review_info.reviewed_at || 'N/A'}. 
                        <a href="{{ route('admin.financial.financialstep1') }}" class="fw-bold text-success text-decoration-underline ms-2">Buksan ang Step 1 Intake Directory</a>
                    </div>
                `;
                noticeBox.classList.remove('d-none');
            } else if (data.status === 'Rejected') {
                noticeBox.className = 'alert alert-danger d-flex align-items-start gap-2 rounded-3 mb-4';
                noticeBox.innerHTML = `
                    <i class="fas fa-times-circle fa-lg text-danger mt-1"></i>
                    <div>
                        <strong>Tinanggihan ang Aplikasyon (Rejected):</strong><br>
                        <span>Dahilan: ${data.review_info.rejection_reason || 'Walang nakasaad'}</span><br>
                        <small class="text-muted">Sinuri ni ${data.review_info.reviewed_by || 'Officer'} noong ${data.review_info.reviewed_at || 'N/A'}</small>
                    </div>
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
        confirmButtonColor: '#1A237E',
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
        text: 'Nagsasagawa ng duplicate check at naglilipat sa Step 1 Intake...',
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
                confirmButtonColor: '#1A237E',
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
            Swal.fire({
                title: 'Naitanggap Na!',
                html: `
                    <p class="text-secondary mb-3">${body.message}</p>
                    <div class="alert alert-success py-2.5 px-3 text-center my-2 border border-success rounded-3 bg-success-subtle">
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size:0.72rem;">Itinalagang Opisyal na Control Number (Step 1):</small>
                        <strong class="font-monospace fs-4 text-success">${body.data.control_number}</strong>
                    </div>
                    <small class="text-muted d-block mt-2">Nailipat na ang rekord sa regular na Step 1 Intake Directory.</small>
                `,
                icon: 'success',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Hindi Naitanggap',
                text: body.message || 'May naganap na error sa paglipat ng rekord.',
                icon: 'error',
                confirmButtonColor: '#DC2626'
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
        text: 'Sigurado ka bang nais mong tanggihan ang online intake application na ito?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Oo, I-reject',
        cancelButtonText: 'Kanselahin'
    }).then((result) => {
        if (result.isConfirmed) {
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
                    Swal.fire({
                        title: 'Nai-reject Na',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#1A237E'
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
</script>
@endsection
