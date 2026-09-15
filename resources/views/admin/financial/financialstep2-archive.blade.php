@extends('layouts.financial')

@section('title', 'Archived Financial Records - MSWDO Admin')
@section('page-title', 'Archived Financial Records')

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
            <h4 class="fw-bold mb-1" style="color: #1A237E;">Archived Financial Records</h4>
            <p class="text-muted small mb-0">View, search, and restore completed or archived financial assistance records.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.financial.financialstep2.all-intakes') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Step 2 Masterlist
            </a>
            <a href="{{ route('admin.financial.financialstep2.payroll-records') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-file-invoice-dollar me-1"></i> Payroll Records
            </a>
            <a href="{{ route('admin.financial.financialstep2.liquidation') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-receipt me-1"></i> Liquidation
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
        <form action="{{ route('admin.financial.financialstep2.archive') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4 col-lg-4">
                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search</label>
                <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Search by Control No, Name, Rep, Payroll No..." value="{{ request('search') }}">
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
                <label class="form-label small fw-bold text-muted mb-1">Claim Status</label>
                <select name="claim_status" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                    <option value="All">All Statuses</option>
                    <option value="Claimed" {{ request('claim_status') == 'Claimed' ? 'selected' : '' }}>Claimed</option>
                    <option value="Unclaimed" {{ request('claim_status') == 'Unclaimed' ? 'selected' : '' }}>Unclaimed</option>
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <label class="form-label small fw-bold text-muted mb-1">Month &amp; Year</label>
                <input type="month" name="month" class="form-control form-control-sm rounded-3" value="{{ request('month') }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-1 col-lg-1 d-flex gap-1">
                @if(request()->hasAny(['search', 'barangay', 'claim_status', 'month']))
                <a href="{{ route('admin.financial.financialstep2.archive') }}" class="btn btn-sm btn-light border rounded-3 w-100" title="Reset Filters">
                    <i class="fas fa-redo"></i>
                </a>
                @else
                <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100" style="background: #1A237E; border-color: #1A237E;" title="Filter">
                    <i class="fas fa-filter"></i>
                </button>
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
                        <th>Payroll Batch</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Archived Date</th>
                        <th class="text-end pe-4" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archivedRecords as $record)
                    <tr>
                        <td class="ps-3 font-monospace fw-bold text-primary">
                            {{ $record->control_number }}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $record->beneficiary_full_name }}</div>
                            @if($record->has_representative)
                            <div class="text-muted small">
                                <i class="fas fa-user-friends me-1"></i>Rep: {{ $record->representative_full_name }}
                            </div>
                            @endif
                        </td>
                        <td>{{ $record->beneficiary_barangay ?? 'Silang' }}</td>
                        <td>
                            @if($record->payrollRecord)
                                <div class="font-monospace small text-dark">{{ $record->payrollRecord->payroll_number }}</div>
                            @elseif($record->payroll_date)
                                <div class="small text-muted">{{ Carbon\Carbon::parse($record->payroll_date)->format('M d, Y') }}</div>
                            @else
                                <span class="text-muted small fst-italic">--</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-success">&#8369;{{ number_format($record->recommended_amount ?? 0, 2) }}</span>
                        </td>
                        <td>
                            @if($record->claim_status === 'Claimed')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 small">
                                    Claimed
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 small">
                                    Unclaimed
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="small text-muted">{{ $record->archived_at ? $record->archived_at->format('M d, Y') : 'Archived' }}</div>
                            @if($record->archive_reason)
                            <div class="text-muted small fst-italic" style="font-size: 0.75rem;">{{ Str::limit($record->archive_reason, 25) }}</div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <!-- View -->
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary action-btn btn-preview-record"
                                    title="View Details"
                                    data-record='@json($record)'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <!-- Restore -->
                                <button type="button"
                                    class="btn btn-sm btn-outline-success action-btn btn-restore-step2"
                                    data-id="{{ $record->id }}"
                                    data-control="{{ $record->control_number }}"
                                    data-name="{{ $record->beneficiary_full_name }}"
                                    title="Restore Record">
                                    <i class="fas fa-rotate-left"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-5 text-center text-muted">
                            <i class="fas fa-archive fa-2x mb-2 opacity-50 d-block"></i>
                            <div>No archived financial assistance records found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($archivedRecords->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $archivedRecords->firstItem() }} to {{ $archivedRecords->lastItem() }} of {{ $archivedRecords->total() }} records
            </div>
            <div>
                {{ $archivedRecords->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>

</div>

<!-- Details Modal -->
<div class="modal fade" id="financialPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header py-3 px-4 text-white" style="background: #1A237E;">
                <h5 class="modal-title fw-bold m-0" style="font-size: 1.05rem;">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Archived Financial Record Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Financial Summary -->
                <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    Assistance &amp; Payment Summary
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="text-muted small">Control Number</div>
                        <div class="fw-bold font-monospace text-primary" id="previewControlNo">--</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Assistance Amount</div>
                        <div class="fw-bold text-success fs-6" id="previewAssistanceAmount">&#8369;0.00</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Claim Status</div>
                        <div id="previewClaimStatusBadge">--</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Payroll Batch / Reference</div>
                        <div class="font-monospace text-dark" id="previewPayrollRef">--</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Assistance Purpose</div>
                        <div class="text-dark" id="previewPurpose">--</div>
                    </div>
                </div>

                <!-- Beneficiary Info -->
                <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    Beneficiary Information
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small">Beneficiary Name</div>
                        <div class="fw-semibold text-dark" id="previewBenName">--</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Sex / Age</div>
                        <div class="text-dark" id="previewBenSexAge">--</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Contact Number</div>
                        <div class="text-dark" id="previewBenContact">--</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Address</div>
                        <div class="text-dark" id="previewBenAddress">--</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Barangay</div>
                        <div class="text-dark" id="previewBenBarangay">--</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Category</div>
                        <div class="text-dark" id="previewBenCategory">--</div>
                    </div>
                </div>

                <!-- Representative Info (if applicable) -->
                <div id="previewRepCard" style="display: none;">
                    <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                        Representative Information
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="text-muted small">Representative Name</div>
                            <div class="fw-semibold text-dark" id="previewRepName">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Relationship</div>
                            <div class="text-dark" id="previewRepRel">--</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Contact Number</div>
                            <div class="text-dark" id="previewRepContact">--</div>
                        </div>
                    </div>
                </div>

                <!-- Assessment Notes -->
                <h6 class="fw-bold text-secondary text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                    Assessment Notes
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-2 rounded bg-light border small text-dark" id="previewAssessment">--</div>
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
<form id="restoreStep2Form" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const previewModalEl = document.getElementById('financialPreviewModal');
    const previewModal = previewModalEl ? new bootstrap.Modal(previewModalEl) : null;

    document.querySelectorAll('.btn-preview-record').forEach(button => {
        button.addEventListener('click', function () {
            const dataStr = this.getAttribute('data-record');
            if (!dataStr) return;
            const record = JSON.parse(dataStr);

            document.getElementById('previewControlNo').textContent = record.control_number || '--';
            document.getElementById('previewAssistanceAmount').textContent = (record.recommended_amount && parseFloat(record.recommended_amount) > 0) ? '₱' + parseFloat(record.recommended_amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) : '₱0.00';
            
            // Claim Status Badge
            const claimBadgeEl = document.getElementById('previewClaimStatusBadge');
            if (record.claim_status === 'Claimed') {
                claimBadgeEl.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 small">Claimed</span>';
            } else {
                claimBadgeEl.innerHTML = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-0.5 small">Unclaimed</span>';
            }

            // Payroll Reference
            const payrollRef = record.payroll_record ? record.payroll_record.payroll_number : (record.payroll_date ? 'Date: ' + record.payroll_date : 'Not Linked');
            document.getElementById('previewPayrollRef').textContent = payrollRef;

            // Beneficiary
            document.getElementById('previewBenName').textContent = record.beneficiary_full_name || (record.beneficiary_first_name + ' ' + record.beneficiary_last_name);
            document.getElementById('previewBenSexAge').textContent = (record.beneficiary_sex || '--') + ' / ' + (record.beneficiary_age ? record.beneficiary_age + ' yrs' : '--');
            document.getElementById('previewBenContact').textContent = record.beneficiary_contact_number || '--';
            document.getElementById('previewBenAddress').textContent = record.beneficiary_address || '--';
            document.getElementById('previewBenBarangay').textContent = record.beneficiary_barangay || '--';
            document.getElementById('previewBenCategory').textContent = record.display_category || record.beneficiary_category || '--';

            // Representative
            const repCard = document.getElementById('previewRepCard');
            if (record.has_representative && (record.rep_first_name || record.rep_last_name)) {
                repCard.style.display = 'block';
                document.getElementById('previewRepName').textContent = record.representative_full_name || (record.rep_first_name + ' ' + record.rep_last_name);
                document.getElementById('previewRepRel').textContent = record.rep_relationship || '--';
                document.getElementById('previewRepContact').textContent = record.rep_contact_number || '--';
            } else {
                repCard.style.display = 'none';
            }

            // Assessment & Purpose
            document.getElementById('previewPurpose').textContent = record.display_assistance_purpose || record.purpose || '--';
            document.getElementById('previewAssessment').textContent = record.social_worker_assessment || 'No assessment notes recorded.';

            if (previewModal) previewModal.show();
        });
    });

    // Restore Action
    document.querySelectorAll('.btn-restore-step2').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const controlNumber = this.getAttribute('data-control');
            const name = this.getAttribute('data-name');

            Swal.fire({
                title: 'Restore Financial Record?',
                html: `Are you sure you want to restore the financial assistance record for <strong>${name}</strong> (${controlNumber}) to the active Step 2 masterlist?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Restore',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('restoreStep2Form');
                    form.action = `/admin/financial/financialstep2/restore/${id}`;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
