@extends('layouts.financial')

@section('title', 'Archived Intakes - MSWDO Admin')
@section('page-title', 'Archived Intakes')

@section('content')
<div class="container-fluid">

    <!-- Flash Notifications -->
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
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">Archived Intake Records</h4>
            <p class="text-muted small mb-0">View, search, and restore archived Step 1 general intake forms.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.financial.financialstep1') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Step 1 Intake
            </a>
            <a href="{{ route('admin.beneficiary-intake.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-list me-1"></i> All Intakes
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
        <form action="{{ route('admin.financial.step1.archive') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-5 col-lg-5">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search</label>
                <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Search by Control No, Name, Representative, Barangay..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 col-lg-3">
                <label class="form-label small fw-bold text-muted mb-1">Barangay</label>
                <select name="barangay" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                    <option value="All">All Barangays</option>
                    @foreach($barangays as $brgy)
                    <option value="{{ $brgy }}" {{ request('barangay') == $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1">Category</label>
                <select name="category" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                    <option value="All">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold w-100" style="background: #1A237E; border-color: #1A237E;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'barangay', 'category']))
                <a href="{{ route('admin.financial.step1.archive') }}" class="btn btn-sm btn-light border rounded-3 px-3" title="Clear Filters">
                    <i class="fas fa-redo"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Records Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3" style="width: 140px;">Control No.</th>
                        <th>Beneficiary Name</th>
                        <th>Barangay</th>
                        <th>Category</th>
                        <th>Date Processed</th>
                        <th>Archived Date</th>
                        <th class="text-end pe-4" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivedIntakes as $intake)
                    <tr>
                        <td class="ps-3 font-monospace fw-bold text-primary">
                            {{ $intake->control_number }}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $intake->beneficiary_full_name }}</div>
                            @if($intake->has_representative)
                            <div class="text-muted small">
                                <i class="fas fa-user-friends me-1"></i>Rep: {{ $intake->representative_full_name }}
                            </div>
                            @endif
                        </td>
                        <td>{{ $intake->beneficiary_barangay ?? 'Silang' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill">{{ $intake->display_category }}</span>
                        </td>
                        <td>{{ $intake->date_processed ? $intake->date_processed->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <div class="small text-muted">{{ $intake->archived_at ? $intake->archived_at->format('M d, Y') : 'Archived' }}</div>
                            @if($intake->archive_reason)
                            <div class="text-muted small fst-italic" style="font-size: 0.75rem;">{{ Str::limit($intake->archive_reason, 25) }}</div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <!-- View -->
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary action-btn btn-preview-intake"
                                    title="View Details"
                                    data-intake='@json($intake)'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <!-- Restore -->
                                <button type="button"
                                    class="btn btn-sm btn-outline-success action-btn btn-restore-step1"
                                    data-id="{{ $intake->id }}"
                                    data-control="{{ $intake->control_number }}"
                                    data-name="{{ $intake->beneficiary_full_name }}"
                                    title="Restore Record">
                                    <i class="fas fa-rotate-left"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-5 text-center text-muted">
                            <i class="fas fa-archive fa-2x mb-2 opacity-50 d-block"></i>
                            <div>No archived intake records found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($archivedIntakes->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $archivedIntakes->firstItem() }} to {{ $archivedIntakes->lastItem() }} of {{ $archivedIntakes->total() }} records
            </div>
            <div>
                {{ $archivedIntakes->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>

</div>

<!-- Details Modal -->
<div class="modal fade" id="intakePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header py-3 px-4 text-white" style="background: #1A237E;">
                <h5 class="modal-title fw-bold m-0" style="font-size: 1.05rem;">
                    <i class="fas fa-file-alt me-2"></i>Archived Intake Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Beneficiary Info -->
                <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    Beneficiary Information
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small">Control Number</div>
                        <div class="fw-bold font-monospace text-primary" id="previewControlNumber">--</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Full Name</div>
                        <div class="fw-semibold text-dark" id="previewBeneficiaryName">--</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Sex / Age</div>
                        <div class="text-dark" id="previewBeneficiarySexAge">--</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Contact Number</div>
                        <div class="text-dark" id="previewBeneficiaryContact">--</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Barangay</div>
                        <div class="text-dark" id="previewBeneficiaryBarangay">--</div>
                    </div>
                    <div class="col-md-8">
                        <div class="text-muted small">Complete Address</div>
                        <div class="text-dark" id="previewBeneficiaryAddress">--</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Category</div>
                        <div class="text-dark" id="previewBeneficiaryCategory">--</div>
                    </div>
                </div>

                <!-- Representative Info (if applicable) -->
                <div id="previewRepresentativeCard" style="display: none;">
                    <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                        Representative Information
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="text-muted small">Representative Name</div>
                            <div class="fw-semibold text-dark" id="previewRepName">--</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Relationship</div>
                            <div class="text-dark" id="previewRepRelationship">--</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Contact Number</div>
                            <div class="text-dark" id="previewRepContact">--</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Address</div>
                            <div class="text-dark" id="previewRepAddress">--</div>
                        </div>
                    </div>
                </div>

                <!-- Assistance Details -->
                <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    Assistance Assessment
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Assistance Requested</div>
                        <div class="text-dark" id="previewRecommendedType">--</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Recommended Amount</div>
                        <div class="fw-bold text-success fs-6" id="previewRecommendedAmount">--</div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-muted small">Purpose / Medical Concern</div>
                        <div class="text-dark" id="previewAssistancePurpose">--</div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-muted small">Social Worker Assessment</div>
                        <div class="p-2 rounded bg-light border small text-dark mt-1" id="previewSocialWorkerAssessment">--</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Restore POST Action -->
<form id="restoreStep1Form" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const previewModalEl = document.getElementById('intakePreviewModal');
    const previewModal = previewModalEl ? new bootstrap.Modal(previewModalEl) : null;

    document.querySelectorAll('.btn-preview-intake').forEach(button => {
        button.addEventListener('click', function () {
            const dataStr = this.getAttribute('data-intake');
            if (!dataStr) return;
            const intake = JSON.parse(dataStr);

            document.getElementById('previewControlNumber').textContent = intake.control_number || '--';
            document.getElementById('previewBeneficiaryName').textContent = intake.beneficiary_full_name || (intake.beneficiary_first_name + ' ' + intake.beneficiary_last_name);
            document.getElementById('previewBeneficiarySexAge').textContent = (intake.beneficiary_sex || '--') + ' / ' + (intake.beneficiary_age ? intake.beneficiary_age + ' yrs' : '--');
            document.getElementById('previewBeneficiaryContact').textContent = intake.beneficiary_contact_number || '--';
            document.getElementById('previewBeneficiaryAddress').textContent = intake.beneficiary_address || '--';
            document.getElementById('previewBeneficiaryBarangay').textContent = intake.beneficiary_barangay || '--';
            document.getElementById('previewBeneficiaryCategory').textContent = intake.display_category || intake.beneficiary_category || '--';

            // Representative
            const repCard = document.getElementById('previewRepresentativeCard');
            if (intake.has_representative && (intake.rep_first_name || intake.rep_last_name)) {
                repCard.style.display = 'block';
                document.getElementById('previewRepName').textContent = intake.representative_full_name || (intake.rep_first_name + ' ' + intake.rep_last_name);
                document.getElementById('previewRepRelationship').textContent = intake.rep_relationship || '--';
                document.getElementById('previewRepContact').textContent = intake.rep_contact_number || '--';
                document.getElementById('previewRepAddress').textContent = intake.rep_address || '--';
            } else {
                repCard.style.display = 'none';
            }

            // Assistance
            document.getElementById('previewRecommendedType').textContent = intake.recommended_assistance_type || intake.service_provided || 'General Assistance';
            document.getElementById('previewRecommendedAmount').textContent = (intake.recommended_amount && parseFloat(intake.recommended_amount) > 0) ? '₱' + parseFloat(intake.recommended_amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '₱0.00';
            document.getElementById('previewAssistancePurpose').textContent = intake.display_assistance_purpose || intake.purpose || '--';
            document.getElementById('previewSocialWorkerAssessment').textContent = intake.social_worker_assessment || 'No assessment notes recorded.';

            if (previewModal) previewModal.show();
        });
    });

    // Restore Action
    document.querySelectorAll('.btn-restore-step1').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const controlNumber = this.getAttribute('data-control');
            const name = this.getAttribute('data-name');

            Swal.fire({
                title: 'Restore Intake Record?',
                html: `Are you sure you want to restore the intake record for <strong>${name}</strong> (${controlNumber}) to the active Step 1 list?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Restore',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('restoreStep1Form');
                    form.action = `/admin/financial/step1/restore/${id}`;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
