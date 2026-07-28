@extends('layouts.financial')

@section('title', 'Financial Assistance: Step 1 - MSWDO Admin')
@section('page-title', 'Financial Assistance Module')

@section('content')
@php
$userName = session('admin_user_name') ?? 'Officer';
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #1A237E;">General Intake Form</h4>
        <p class="text-muted small mb-0">Fill out the form below to create a new general intake record.</p>
    </div>
</div>

<!-- Content Workspace: Table Directory & Action Cards -->
<div class="row g-4">
    <div class="col-12">
        <div class="card animate-fade-in">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title-clean">Step 1 Intakes Directory</h3>
                    <p class="card-subtitle-clean">Recent financial assistance client assessments</p>
                </div>
                <a href="{{ route('admin.beneficiary-intake.create') }}" class="btn btn-brand">
                    <i class="fas fa-plus me-1"></i> New Client Intake
                </a>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th>Control No.</th>
                                <th>Client Name</th>
                                <th>Date Intake</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
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
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $intake->display_category }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold" style="font-size: var(--text-xs);">Step 1 Completed</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.beneficiary-intake.show', $intake) }}" class="btn btn-sm btn-outline-primary me-1" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.beneficiary-intake.edit', $intake) }}" class="btn btn-sm btn-outline-secondary" title="Edit Intake">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-4">
                                    <div class="empty-state-box text-center">
                                        <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                        <h4 class="fw-bold mb-1" style="font-size: var(--text-md); color: var(--color-text-primary);">No recent financial assistance intakes found</h4>
                                        <p class="text-muted mb-0" style="font-size: var(--text-sm);">Click "New Client Intake" to create an initial assessment record.</p>
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
@endsection
