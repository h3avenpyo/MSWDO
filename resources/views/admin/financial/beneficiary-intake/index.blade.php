@extends('layouts.financial')

@section('title', 'Beneficiary Intake Masterlist - MSWDO Silang')
@section('page-title', 'Beneficiary Intake Masterlist')

@section('page-styles')
<link href="{{ asset('css/beneficiary-intake/index.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">Beneficiary Intake Records</h4>
            <p class="text-muted small mb-0">View, search, edit, and manage all intake sheets.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary fw-bold rounded-pill px-3" data-bs-toggle="modal"
                data-bs-target="#transmittalModal" id="btnOpenTransmittalModal">
                <i class="fas fa-file-invoice me-1"></i> Transmittal Report
            </button>
            {{-- <a href="{{ route('admin.beneficiary-intake.create') }}"
                class="btn btn-primary fw-bold rounded-pill px-4" style="background: #1A237E; border: none;">
                <i class="fas fa-plus me-1"></i> Create New Intake
            </a> --}}
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="filter-card">
        <form action="{{ route('admin.beneficiary-intake.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary small">Search Name or Control No.</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                        placeholder="Type name or control no..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-secondary small">Filter by Barangay</label>
                <select name="barangay" class="form-select" onchange="this.form.submit()">
                    <option value="All">All Barangays</option>
                    @foreach($barangays as $brgy)
                    <option value="{{ $brgy }}" {{ request('barangay')==$brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-secondary small">Filter by Category</label>
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="All">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category')==$cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100 fw-bold rounded-3">Filter</button>
                @if(request()->hasAny(['search', 'barangay', 'category']))
                <a href="{{ route('admin.beneficiary-intake.index') }}" class="btn btn-outline-secondary rounded-3"
                    title="Reset Filters"><i class="fas fa-redo"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>Control Number</th>
                        <th>Beneficiary Name</th>
                        <th>Barangay</th>
                        <th>Category</th>
                        <th>Representative</th>
                        <th>Date Processed</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($intakes as $intake)
                    <tr>
                        <td>
                            <span class="fw-bold text-primary">{{ $intake->control_number }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $intake->beneficiary_full_name }}</div>
                            <div class="text-muted small">{{ $intake->beneficiary_contact_number ?? 'No contact' }} • {{
                                $intake->beneficiary_age ?? 'N/A' }} yrs ({{ $intake->beneficiary_sex ?? 'N/A' }})</div>
                        </td>
                        <td>
                            <i class="fas fa-map-marker-alt text-muted me-1 small"></i>{{ $intake->beneficiary_barangay
                            ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-cat">{{ $intake->display_category }}</span>
                        </td>
                        <td>
                            @if($intake->has_representative)
                            <div class="badge-rep mb-1"><i class="fas fa-user-friends me-1"></i> Has Representative
                            </div>
                            <div class="small fw-semibold text-dark">{{ $intake->representative_full_name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Rel: {{ $intake->rep_relationship ??
                                'N/A' }}</div>
                            @else
                            <span class="text-muted small"><i class="fas fa-user me-1"></i> Self</span>
                            @endif
                        </td>
                        <td>
                            {{ $intake->date_processed ? $intake->date_processed->format('M d, Y') : 'N/A' }}
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
                                <!-- Delete -->
                                {{-- <form action="{{ route('admin.beneficiary-intake.destroy', $intake) }}"
                                    method="POST" class="d-inline delete-intake-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger action-btn btn-delete-intake"
                                        title="Delete Record">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form> --}}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-50"></i>
                                <h6 class="fw-bold text-secondary">No Beneficiary Intake Records Found</h6>
                                <p class="text-muted small mb-3">Try clearing search filters or create a new intake
                                    sheet.</p>
                                <a href="{{ route('admin.beneficiary-intake.create') }}"
                                    class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="fas fa-plus me-1"></i> Create New Intake
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($intakes->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $intakes->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Transmittal Options Generator Modal -->
<div class="modal fade" id="transmittalModal" tabindex="-1" aria-labelledby="transmittalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="transmittalModalLabel" style="color: #1A237E;">
                    <i class="fas fa-file-invoice me-2 text-primary"></i>Generate Transmittal Report
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.beneficiary-intake.transmittal') }}" method="GET" target="_blank"
                id="transmittalForm">
                <div class="modal-body py-4">

                    @if(request()->filled('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request()->filled('barangay') && request('barangay') !== 'All')
                    <input type="hidden" name="barangay" value="{{ request('barangay') }}">
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Transmittal Date</label>
                            <input type="date" name="transmittal_date" class="form-control" value="{{ date('Y-m-d') }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Session / Time</label>
                            <select name="transmittal_session" class="form-select" required>
                                <option value="AM" selected>AM Session</option>
                                <option value="PM">PM Session</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Assigned Staff Name</label>
                        <input type="text" name="staff_name" class="form-control"
                            value="{{ session('admin_user_name') ?? 'FRANCES' }}" placeholder="e.g. FRANCES" required>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light border px-3 rounded-pill"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold"
                        style="background: #1A237E; border: none;">
                        <i class="fas fa-print me-1"></i> Generate &amp; Print Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script src="{{ asset('js/beneficiary-intake/index.js') }}"></script>
@endsection