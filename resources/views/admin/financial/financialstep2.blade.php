@extends('layouts.financial')

@section('title', 'Financial Assistance: Step 2 - MSWDO Admin')
@section('page-title', 'Financial Assistance Module')

@section('content')
@php
$userName = session('admin_user_name') ?? 'Officer';
@endphp

<!-- Step Wizard Header Card -->
<div class="step-wizard-card animate-fade-in mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h2 class="wizard-heading mb-1"><i class="fas fa-hand-holding-usd me-2"></i>Financial Assistance: Step 2</h2>
            <p class="mb-0 text-white-50" style="font-size: var(--text-sm);">Verification &amp; Cash Disbursement Workflow</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="user-welcome">
                <i class="fas fa-user-circle me-1"></i>
                <span>Hello, {{ $userName }}</span>
            </div>
        </div>
    </div>

    <!-- Step 2 Responsibilities Embedded Widget -->
    <div class="wizard-responsibilities-box mb-3">
        <div class="wizard-responsibility-title">
            <i class="fas fa-info-circle"></i> Step 2 Responsibilities
        </div>
        <div class="row g-2">
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;">Verify client identification &amp; approved Step 1 intake requirements.</span>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;">Review assessment findings &amp; validate assistance grant recommendations.</span>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;">Certify payroll and process voucher authorization.</span>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;">Facilitate cash release &amp; secure beneficiary acknowledgment receipts.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="step-wizard-nav pt-2">
        <div class="step-item-pill">
            <div class="step-circle"><i class="fas fa-check"></i></div>
            <div class="step-label">Step 1: Intake &amp; Initial Assessment</div>
        </div>
        <div class="step-item-pill active">
            <div class="step-circle"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="step-label">Step 2: Verification &amp; Disbursement</div>
        </div>
    </div>
</div>

<!-- Stat Cards Grid -->
<div class="stat-cards-grid">
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">For Verification</p>
                <h3 class="stat-value">0</h3>
            </div>
            <div class="stat-icon primary"><i class="fas fa-file-invoice-dollar"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Pending Payout</p>
                <h3 class="stat-value">0</h3>
            </div>
            <div class="stat-icon warning"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Disbursed Today</p>
                <h3 class="stat-value">0</h3>
            </div>
            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">Total Released</p>
                <h3 class="stat-value">&#8369;0.00</h3>
            </div>
            <div class="stat-icon info"><i class="fas fa-coins"></i></div>
        </div>
    </div>
</div>

<!-- Content Workspace: Table Directory & Action Cards -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card animate-fade-in">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title-clean">Today's Verification &amp; Disbursement Queue</h3>
                    <p class="card-subtitle-clean">Intake records awaiting Step 2 verification &bull; <span class="fw-bold text-primary">{{ date('F d, Y') }}</span></p>
                </div>
            </div>
            <div class="p-3">
                <div class="table-responsive">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th>Control No.</th>
                                <th>Client Name</th>
                                <th>Date Intake</th>
                                <th>Category / Purpose</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="p-4">
                                    <div class="empty-state-box text-center py-4">
                                        <i class="fas fa-receipt fa-3x mb-3 text-muted opacity-50 d-block"></i>
                                        <h4 class="fw-bold mb-1" style="font-size: var(--text-md); color: var(--color-text-primary);">No records in queue for today ({{ date('M d, Y') }})</h4>
                                        <p class="text-muted mb-0" style="font-size: var(--text-sm);">Completed Step 1 general intake assessments will appear here for verification and cash release.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection