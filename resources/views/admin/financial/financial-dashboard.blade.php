@extends('layouts.financial')

@section('title', 'Financial Assistance Dashboard - MSWDO Admin')
@section('page-title', 'Financial Assistance Dashboard')

@section('content')
@php
$userName = session('admin_user_name') ?? 'Officer';
@endphp

<!-- Header Card -->
<div class="step-wizard-card animate-fade-in mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h2 class="wizard-heading mb-1"><i class="fas fa-hand-holding-usd me-2"></i>Financial Assistance Dashboard</h2>
            <p class="mb-0 text-white-50" style="font-size: var(--text-sm);">General Intake Assessment &amp; Assistance Management System</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="user-welcome">
                <i class="fas fa-user-circle me-1"></i>
                <span>Hello, {{ $userName }}</span>
            </div>
        </div>
    </div>

    <!-- Workflow Overview Widget -->
    <div class="wizard-responsibilities-box mb-3">
        <div class="wizard-responsibility-title">
            <i class="fas fa-info-circle"></i> General Intake &amp; Assessment Overview
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;"><strong>Client Intake:</strong> Encode new client general intake assessment and identifying information.</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-check-circle text-warning mt-1 flex-shrink-0" style="font-size: 0.85rem;"></i>
                    <span class="text-white-50" style="font-size: var(--text-xs); line-height: 1.35;"><strong>Social Assessment:</strong> Review documentary requirements, evaluate circumstances, and complete the intake record.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="step-wizard-nav pt-2">
        <a href="{{ route('admin.financial.financialstep1') }}" class="step-item-pill text-decoration-none active">
            <div class="step-circle"><i class="fas fa-clipboard-check"></i></div>
            <div class="step-label">Step 1: Intake &amp; Assessment</div>
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
                <p class="stat-label">Assessments Completed</p>
                <h3 class="stat-value">{{ number_format($step1Approved ?? 0) }}</h3>
            </div>
            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="card animate-fade-in mb-0">
        <div class="stat-card-inner">
            <div>
                <p class="stat-label">All Intake Records</p>
                <h3 class="stat-value">{{ number_format($totalIntakes ?? 0) }}</h3>
            </div>
            <div class="stat-icon info"><i class="fas fa-list-check"></i></div>
        </div>
    </div>
</div>

<!-- Workflow Navigation Modules -->
<div class="row g-4 mt-1">
    <div class="col-lg-12">
        <div class="card animate-fade-in">
            <div class="card-header-clean">
                <div>
                    <h3 class="card-title-clean">Financial Assistance Modules</h3>
                    <p class="card-subtitle-clean">Access intake assessment and client records directory</p>
                </div>
            </div>
            <div class="p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('admin.financial.financialstep1') }}"
                            class="action-tile h-100 flex-column align-items-start justify-content-between p-4">
                            <div class="d-flex align-items-center justify-content-between w-100 mb-3">
                                <div class="stat-icon primary"><i class="fas fa-clipboard-list"></i></div>
                                <span class="badge bg-primary text-white rounded-pill px-3 py-1">Step 1</span>
                            </div>
                            <div>
                                <div class="action-tile-title mb-1" style="font-size: var(--text-md);">Step 1: Intake &amp; Assessment</div>
                                <div class="action-tile-desc">Encode new client intake details, verify documentary requirements, and complete initial social assessment.</div>
                            </div>
                            <div class="mt-3 text-primary fw-semibold small">
                                Open Step 1 Module <i class="fas fa-arrow-right ms-1"></i>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="/admin/beneficiary-intake"
                            class="action-tile h-100 flex-column align-items-start justify-content-between p-4">
                            <div class="d-flex align-items-center justify-content-between w-100 mb-3">
                                <div class="stat-icon info"><i class="fas fa-folder-open"></i></div>
                                <span class="badge bg-info text-white rounded-pill px-3 py-1">Directory</span>
                            </div>
                            <div>
                                <div class="action-tile-title mb-1" style="font-size: var(--text-md);">All Intakes Directory</div>
                                <div class="action-tile-desc">View existing intake records, track assessment details, print intake sheets, and manage beneficiary cases.</div>
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
</div>
@endsection