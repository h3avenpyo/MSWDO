@extends('admin.social-case.layout')
@section('title', 'Case Encoding - Social Case Study')

@section('content')
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Dashboard</a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i> New case</a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i> All cases</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i> Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="page-head">
        <div>
            <h1>Case Encoding</h1>
            <p>Step 2 of 2 — Complete the intake form for the social case study.</p>
        </div>
        <button class="btn ghost" onclick="window.location.href='/admin/social-case/new'">
            <i data-lucide="arrow-left" style="width:16px;height:16px"></i> Back
        </button>
    </div>

    <!-- Progress Stepper -->
    <div class="stepper">
        <div class="step completed">
            <div class="step-number">✓</div>
            <span>Client Eligibility</span>
        </div>
        <div class="step-connector completed"></div>
        <div class="step active">
            <div class="step-number">2</div>
            <span>Case Encoding</span>
        </div>
    </div>

    <div id="intakeFormContent"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadIntakeForm();
    });
</script>
@endpush
