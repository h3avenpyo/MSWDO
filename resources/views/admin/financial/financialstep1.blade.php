@extends('layouts.financial')

@section('title', 'Financial Assistance: Step 1 - MSWDO Admin')
@section('page-title', 'Financial Assistance Module')

@section('content')
@php
$userName = session('admin_user_name') ?? 'Officer';
@endphp

<style>
@media (max-width: 575.98px) {
    .step-label {
        white-space: normal;
        font-size: 0.7rem;
    }
    .step-item-pill {
        padding: 0.3rem 0.5rem;
    }
    form.row.g-2 > [class*="col-md-"] {
        width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
    }
}
</style>

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

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #1A237E;">General Intake Form &amp; Assessment (Step 1)</h4>
        <p class="text-muted small mb-0">Collect beneficiary identifying information, documentary requirements, and social worker assessment.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="{{ route('admin.beneficiary-intake.create') }}" class="btn btn-primary-action shadow-sm" id="btnNewClientIntake">
            <i class="fas fa-plus"></i>
            <span>New Client Intake</span>
        </a>
    </div>
</div>

<!-- Search Bar (Restricted to Today's Intakes) -->
<div class="card border-0 shadow-sm rounded-3 mb-4 p-3 bg-white">
    <form action="{{ route('admin.financial.financialstep1') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-9 col-lg-9">
            <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-search me-1"></i> Search Today's Intake ({{ date('F d, Y') }})</label>
            <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Search by Control No, Client Name, Rep, or Barangay..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold w-100" style="background: #1A237E; border-color: #1A237E;">
                <i class="fas fa-search me-1"></i> Search
            </button>
            @if(request()->filled('search'))
            <a href="{{ route('admin.financial.financialstep1') }}" class="btn btn-sm btn-light border rounded-3 px-3" title="Clear Search">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Content Workspace: Step 1 Table Directory (Isolated to Today's Step 1 Data) -->
<div class="row g-4">
    <div class="col-12">
        <div class="card animate-fade-in">
            <div class="card-header-clean d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h3 class="card-title-clean">
                        @if(request()->filled('search'))
                            Search Results for "{{ request('search') }}" in Today's Intakes &bull; <span class="fw-bold text-primary">{{ date('F d, Y') }}</span>
                        @else
                            Today's Intake Assessments &bull; <span class="fw-bold text-primary">{{ date('F d, Y') }}</span>
                        @endif
                    </h3>
                    <p class="card-subtitle-clean">General Intake Sheets and Assessment Records processed today (Step 1)</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if(request()->filled('search'))
                        <span class="badge bg-light text-secondary border px-2.5 py-1.5 rounded-pill small fw-semibold">
                            <i class="fas fa-search me-1 text-primary"></i> {{ method_exists($recentIntakes, 'total') ? $recentIntakes->total() : count($recentIntakes) }} Matched
                        </span>
                    @endif
                </div>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table-clean w-100">
                        <thead>
                            <tr>
                                <th>Control No.</th>
                                <th>Client / Beneficiary Name</th>
                                <th>Date Intake</th>
                                <th>Category &amp; Barangay</th>
                                <th>Assistance / Purpose Requested</th>
                                <th>Intake Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentIntakes as $intake)
                            <tr>
                                <td>
                                    <span class="fw-bold" style="color: var(--color-primary);">{{ $intake->control_number }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $intake->beneficiary_full_name }}</div>
                                    @if($intake->has_representative)
                                    <div class="text-muted small"><i class="fas fa-user-friends me-1"></i>Rep: {{ $intake->representative_full_name }}</div>
                                    @endif
                                </td>
                                <td>{{ $intake->date_processed ? $intake->date_processed->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <div><span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill">{{ $intake->display_category }}</span></div>
                                    <div class="text-muted small">{{ $intake->beneficiary_barangay ?? 'Silang' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">
                                        {{ $intake->recommended_assistance_type ?: ($intake->service_provided ?: 'General Assistance') }}
                                    </div>
                                    <div class="text-muted small">{{ $intake->display_assistance_purpose }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: var(--text-xs);">
                                        <i class="fas fa-check-circle me-1"></i>Intake Recorded
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <!-- View -->
                                        <a href="{{ route('admin.beneficiary-intake.show', $intake) }}"
                                            class="btn btn-sm btn-outline-primary action-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <!-- Edit -->
                                        <a href="{{ route('admin.beneficiary-intake.edit', $intake) }}"
                                            class="btn btn-sm btn-outline-secondary action-btn" title="Edit Record">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Archive -->
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger action-btn btn-archive-step1"
                                            data-id="{{ $intake->id }}"
                                            data-control="{{ $intake->control_number }}"
                                            data-name="{{ $intake->beneficiary_full_name }}"
                                            title="Archive Record">
                                            <i class="fas fa-box-archive"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-4">
                                    <div class="empty-state-box text-center py-3">
                                        <i class="fas fa-clipboard-list fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                        <h4 class="fw-bold mb-1" style="font-size: var(--text-md); color: var(--color-text-primary);">No intake records processed today</h4>
                                        <p class="text-muted mb-3" style="font-size: var(--text-sm);">Only intake records processed on {{ date('F d, Y') }} appear here. Click "New Client Intake" to create an assessment record.</p>
                                        <a href="{{ route('admin.beneficiary-intake.create') }}" class="btn btn-sm btn-primary rounded-pill px-4" style="background: #1A237E;">
                                            <i class="fas fa-plus me-1"></i> New Client Intake
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($recentIntakes, 'hasPages') && $recentIntakes->hasPages())
                <div class="pt-3 border-top d-flex justify-content-end">
                    {{ $recentIntakes->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Step 1 Archive Action -->
<form id="archiveStep1Form" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="reason" id="archiveStep1Reason">
</form>
@endsection

@section('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-archive-step1').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const controlNumber = this.getAttribute('data-control');
            const name = this.getAttribute('data-name');

            Swal.fire({
                title: 'I-archive ang Intake Record?',
                html: `Are you sure you want to archive this intake record for <strong>${name}</strong> (<span class="text-primary font-monospace">${controlNumber}</span>)?<br><br><small class="text-muted">The record will be safely removed from the active intake list but will remain available in the Archive.</small>`,
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Reason for archiving (optional)',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '<i class="fas fa-box-archive me-1"></i> I-archive Record',
                cancelButtonText: 'Kanselahin',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Ina-archive ang Record...',
                        text: 'Sandali lamang...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const form = document.getElementById('archiveStep1Form');
                    document.getElementById('archiveStep1Reason').value = result.value || '';
                    form.action = `/admin/financial/step1/archive/${id}`;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
