@extends('layouts.financial')

@section('title', 'Financial Assistance: Step 1 - MSWDO Admin')
@section('page-title', 'Financial Assistance Module')

@section('content')
@php
$userName = session('admin_user_name') ?? 'Officer';
@endphp
<!-- Step Wizard Header Card -->
<div class="step-wizard-card animate-fade-in mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <div>
            <h2 class="wizard-heading mb-1"><i class="fas fa-clipboard-list me-2"></i>Financial Assistance: Step 1</h2>
            <p class="mb-0 text-white-50" style="font-size: var(--text-sm);">Intake &amp; Initial Assessment Workflow</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/admin/financial/dashboard" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
            <span class="badge-user-welcome">
                <i class="fas fa-user-check me-1"></i> Welcome: {{ $userName }}
            </span>
        </div>
    </div>

    <div class="step-wizard-nav pt-2">
        <div class="step-item-pill active">
            <div class="step-circle"><i class="fas fa-clipboard-check"></i></div>
            <div class="step-label">Intake &amp; Initial Assessment</div>
        </div>
    </div>
</div>

<!-- Content Workspace: Table Directory & Action Cards -->
<div class="row g-4">
    <div class="col-lg-8">
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
                                <th>Assistance Type</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="p-4">
                                    <div class="empty-state-box">
                                        <i class="fas fa-inbox fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                        <h4 class="fw-bold mb-1" style="font-size: var(--text-md); color: var(--color-text-primary);">No recent financial assistance intakes found</h4>
                                        <p class="text-muted mb-0" style="font-size: var(--text-sm);">Click "New Client Intake" to create an initial assessment record.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Step 1 Responsibilities Widget -->
        <div class="card animate-fade-in">
            <div class="card-header-clean">
                <h3 class="card-title-clean"><i class="fas fa-info-circle text-info me-2"></i>Step 1 Responsibilities</h3>
            </div>
            <div class="p-3">
                <ul class="responsibility-list">
                    <li class="responsibility-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Verify client identification &amp; basic documentary requirements.</span>
                    </li>
                    <li class="responsibility-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Conduct initial intake assessment &amp; interview.</span>
                    </li>
                    <li class="responsibility-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Encode beneficiary information in system.</span>
                    </li>
                    <li class="responsibility-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Forward validated intake assessments for authorization &amp; disbursement.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
