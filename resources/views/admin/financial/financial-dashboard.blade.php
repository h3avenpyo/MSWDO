@extends('layouts.financial')

@section('title', 'Financial Assistance Dashboard - MSWDO Admin')
@section('page-title', 'Financial Assistance Dashboard')

@section('content')
@php
$userName = session('admin_user_name') ?? 'Officer';
@endphp
<!-- Step Wizard Header Card -->
<div class="step-wizard-card animate-fade-in mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h2 class="wizard-heading mb-1"><i class="fas fa-hand-holding-usd me-2"></i>Financial Assistance Workflow</h2>
            <p class="mb-0 text-white-50" style="font-size: var(--text-sm);">Two-Step General Intake, Verification &amp; Disbursement Management</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="user-welcome">
                <i class="fas fa-user-circle me-1"></i>
                <span>Hello, {{ $userName }}</span>
            </div>
        </div>
    </div>

    <!-- Step Responsibilities Embedded Widget -->
    <div class="wizard-responsibilities-box mb-3">
        <div class="wizard-responsibility-title">
            <i class="fas fa-info-circle"></i> Integrated Workflow Overview
        </div>
        <div class="row g-2">
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;"><strong>Step 1:</strong> Encode new client general intake assessment and identifying info.</span>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;"><strong>Step 1:</strong> Validate documentary requirements &amp; recommend assistance amount.</span>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;"><strong>Step 2:</strong> Automatically retrieve Step 1 intake data for verification queue.</span>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;"><strong>Step 2:</strong> Certify payroll, process voucher &amp; release cash disbursement.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="step-wizard-nav pt-2">
        <a href="{{ route('admin.financial.financialstep1') }}" class="step-item-pill text-decoration-none {{ request()->is('admin/financial/financialstep1*') ? 'active' : '' }}">
            <div class="step-circle"><i class="fas fa-clipboard-check"></i></div>
            <div class="step-label">Step 1: Intake &amp; Assessment</div>
        </a>
        <a href="{{ route('admin.financial.financialstep2') }}" class="step-item-pill text-decoration-none {{ request()->is('admin/financial/financialstep2*') ? 'active' : '' }}">
            <div class="step-circle"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="step-label">Step 2: Verification &amp; Disbursement</div>
        </a>
    </div>
</div>

<!-- Dynamic Metric Stat Cards Grid -->
<div class="stat-cards-grid">
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Total Intakes</p>
                <h3 class="stat-value">{{ number_format($totalIntakes ?? 0) }}</h3>
            </div>
            <div class="stat-icon primary"><i class="fas fa-folder-open"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Today's Intakes</p>
                <h3 class="stat-value">{{ number_format($todayIntakes ?? 0) }}</h3>
            </div>
            <div class="stat-icon warning"><i class="fas fa-calendar-day"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Step 1 Completed</p>
                <h3 class="stat-value">{{ number_format($step1Approved ?? 0) }}</h3>
            </div>
            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Ready for Step 2</p>
                <h3 class="stat-value">{{ number_format($readyForStep2 ?? 0) }}</h3>
            </div>
            <div class="stat-icon info"><i class="fas fa-arrow-right"></i></div>
        </div>
    </div>
</div>

<!-- Workflow Navigation -->
<div class="row g-4 mt-1">
    <div class="col-lg-12">
        <div class="card animate-fade-in">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title-clean">Financial Assistance Workflow Modules</h3>
                    <p class="card-subtitle-clean">Access intake assessment, verification queue, and client masterlist</p>
                </div>
            </div>
            <div class="p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('admin.financial.financialstep1') }}"
                            class="action-tile h-100 flex-column align-items-start justify-content-between p-4">
                            <div class="d-flex align-items-center justify-content-between w-100 mb-3">
                                <div class="stat-icon primary"><i class="fas fa-clipboard-list"></i></div>
                                <span class="badge bg-primary text-white rounded-pill px-3 py-1">Step 1</span>
                            </div>
                            <div>
                                <div class="action-tile-title mb-1" style="font-size: var(--text-md);">Step 1: Intake &amp; Assessment</div>
                                <div class="action-tile-desc">Encode new client intake details, upload documentary requirements, and complete initial social assessment.</div>
                            </div>
                            <div class="mt-3 text-primary fw-semibold small">
                                Open Step 1 Module <i class="fas fa-arrow-right ms-1"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.financial.financialstep2') }}"
                            class="action-tile h-100 flex-column align-items-start justify-content-between p-4">
                            <div class="d-flex align-items-center justify-content-between w-100 mb-3">
                                <div class="stat-icon success"><i class="fas fa-hand-holding-usd"></i></div>
                                <span class="badge bg-success text-white rounded-pill px-3 py-1">Step 2</span>
                            </div>
                            <div>
                                <div class="action-tile-title mb-1" style="font-size: var(--text-md);">Step 2: Verification &amp; Payout</div>
                                <div class="action-tile-desc">Automatically fetch Step 1 intake records, verify client documents, and process cash disbursement release.</div>
                            </div>
                            <div class="mt-3 text-success fw-semibold small">
                                Open Step 2 Module <i class="fas fa-arrow-right ms-1"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="/admin/beneficiary-intake"
                            class="action-tile h-100 flex-column align-items-start justify-content-between p-4">
                            <div class="d-flex align-items-center justify-content-between w-100 mb-3">
                                <div class="stat-icon info"><i class="fas fa-folder-open"></i></div>
                                <span class="badge bg-info text-white rounded-pill px-3 py-1">Masterlist</span>
                            </div>
                            <div>
                                <div class="action-tile-title mb-1" style="font-size: var(--text-md);">All Intakes Directory</div>
                                <div class="action-tile-desc">View existing intake records, track assessment statuses, export transmittals, and manage beneficiary cases.</div>
                            </div>
                            <div class="mt-3 text-info fw-semibold small">
                                View Directory <i class="fas fa-arrow-right ms-1"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent General Intake Assessments Table -->
    <div class="col-12">
        <div class="card animate-fade-in">
            <div class="card-header-clean d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h3 class="card-title-clean"><i class="fas fa-clock-rotate-left me-2 text-primary"></i>Recent General Intake Assessments</h3>
                    <p class="card-subtitle-clean">Latest client intake sheets available for financial assistance processing</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.beneficiary-intake.create') }}" class="btn btn-sm btn-brand rounded-pill px-3">
                        <i class="fas fa-plus me-1"></i> New Intake (Step 1)
                    </a>
                    <a href="{{ route('admin.financial.financialstep2') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-arrow-right me-1"></i> Go to Step 2 Queue
                    </a>
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
                                <th>Category / Barangay</th>
                                <th>Recommended Amount</th>
                                <th>Workflow Stage</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentIntakes as $intake)
                            <tr>
                                <td>
                                    <span class="fw-bold" style="color: #1A237E;">{{ $intake->control_number }}</span>
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
                                    <div class="text-muted small mt-0.5">{{ $intake->beneficiary_barangay ?? 'Silang' }}</div>
                                </td>
                                <td>
                                    @if($intake->recommended_amount)
                                        <span class="fw-bold text-success">&#8369;{{ number_format($intake->recommended_amount, 2) }}</span>
                                    @else
                                        <span class="text-muted small">Pending Assessment</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: var(--text-xs);">
                                        <i class="fas fa-check me-1"></i>Step 1 Completed
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.beneficiary-intake.show', $intake) }}" class="btn btn-sm btn-outline-primary me-1" title="View Full Intake Sheet">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.financial.financialstep2', ['search' => $intake->control_number]) }}" class="btn btn-sm btn-outline-success" title="Process in Step 2">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center">
                                    <div class="empty-state-box text-center py-3">
                                        <i class="fas fa-clipboard-list fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                        <h4 class="fw-bold mb-1" style="font-size: var(--text-md); color: var(--color-text-primary);">No intake records yet</h4>
                                        <p class="text-muted mb-3" style="font-size: var(--text-sm);">Click "New Intake (Step 1)" to create the first client intake assessment.</p>
                                        <a href="{{ route('admin.beneficiary-intake.create') }}" class="btn btn-sm btn-primary rounded-pill px-4" style="background: #1A237E;">
                                            <i class="fas fa-plus me-1"></i> Create General Intake Sheet
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection