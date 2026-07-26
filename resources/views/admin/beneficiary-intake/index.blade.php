@extends('layouts.financial')

@section('title', 'Beneficiary Intake Masterlist - MSWDO Silang')
@section('page-title', 'Beneficiary Intake Masterlist')

@section('page-styles')
<style>
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }

    .table thead {
        background-color: #F8FAFC;
    }

    .table thead th {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        border-bottom: 1px solid #E2E8F0;
        padding: 0.85rem 1rem;
    }

    .table tbody td {
        padding: 0.85rem 1rem;
        font-size: 0.875rem;
        color: #1E293B;
        vertical-align: middle;
    }

    .badge-cat {
        background-color: #EEF2FF;
        color: #1A237E;
        font-weight: 600;
        border-radius: 6px;
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
    }

    .badge-rep {
        background-color: #F0FDF4;
        color: #15803D;
        font-weight: 600;
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1A237E;">Beneficiary Intake Records</h4>
            <p class="text-muted small mb-0">View, search, edit, and manage all intake sheets.</p>
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
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Type name or control no..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-secondary small">Filter by Barangay</label>
                <select name="barangay" class="form-select" onchange="this.form.submit()">
                    <option value="All">All Barangays</option>
                    @foreach($barangays as $brgy)
                    <option value="{{ $brgy }}" {{ request('barangay') == $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-secondary small">Filter by Category</label>
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="All">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100 fw-bold rounded-3">Filter</button>
                @if(request()->hasAny(['search', 'barangay', 'category']))
                <a href="{{ route('admin.beneficiary-intake.index') }}" class="btn btn-outline-secondary rounded-3" title="Reset Filters"><i class="fas fa-redo"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                            <div class="text-muted small">{{ $intake->beneficiary_contact_number ?? 'No contact' }} • {{ $intake->beneficiary_age ?? 'N/A' }} yrs ({{ $intake->beneficiary_sex ?? 'N/A' }})</div>
                        </td>
                        <td>
                            <i class="fas fa-map-marker-alt text-muted me-1 small"></i>{{ $intake->beneficiary_barangay ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="badge-cat">{{ $intake->display_category }}</span>
                        </td>
                        <td>
                            @if($intake->has_representative)
                            <div class="badge-rep mb-1"><i class="fas fa-user-friends me-1"></i> Has Representative</div>
                            <div class="small fw-semibold text-dark">{{ $intake->representative_full_name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Rel: {{ $intake->rep_relationship ?? 'N/A' }}</div>
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
                                <a href="{{ route('admin.beneficiary-intake.show', $intake) }}" class="btn btn-sm btn-outline-primary action-btn" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <!-- Edit -->
                                <a href="{{ route('admin.beneficiary-intake.edit', $intake) }}" class="btn btn-sm btn-outline-secondary action-btn" title="Edit Record">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Delete -->
                                <form action="{{ route('admin.beneficiary-intake.destroy', $intake) }}" method="POST" class="d-inline delete-intake-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger action-btn btn-delete-intake" title="Delete Record">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-50"></i>
                                <h6 class="fw-bold text-secondary">No Beneficiary Intake Records Found</h6>
                                <p class="text-muted small mb-3">Try clearing search filters or create a new intake sheet.</p>
                                <a href="{{ route('admin.beneficiary-intake.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
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
@endsection

@section('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete-intake');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.delete-intake-form');
                Swal.fire({
                    title: 'Delete Intake Sheet?'
                    , text: "This action will permanently delete this intake record."
                    , icon: 'warning'
                    , showCancelButton: true
                    , confirmButtonColor: '#DC2626'
                    , cancelButtonColor: '#64748B'
                    , confirmButtonText: 'Yes, Delete It'
                    , cancelButtonText: 'Cancel'
                    , customClass: {
                        popup: 'rounded-4 shadow'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });

</script>
@endsection
