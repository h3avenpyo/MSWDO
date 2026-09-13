@extends('admin.social-case.layout')
@section('title', 'Case Encoding - Social Case Study')

@section('content')
<!-- Mobile Header (visible only on mobile) -->
@php
$logo = null;
if(file_exists(public_path('images/mswdo-logo.png'))){
    $logo='mswdo-logo.png';
}else{
    $files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);
    if(!empty($files))
    $logo=basename($files[0]);
}
@endphp
<div class="mobile-header">
    <button id="mobileMenuBtn" class="mobile-menu-btn" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mobile-menu-icon">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>
    <div class="mobile-header-brand">
        <div class="mobile-brand-text">
            <h1 class="mobile-brand-title">MSWDO SILANG</h1>
            <p class="mobile-brand-subtitle">Case Encoding</p>
        </div>
        <div class="mobile-logo">
            @if($logo)
            <img src="{{ asset('images/'.$logo) }}" class="mobile-logo-img">
            @endif
        </div>
    </div>
</div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/dswd.png') }}" style="width:48px;height:48px;object-fit:contain;flex-shrink:0;" alt="DSWD">
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        @if((string) session('admin_user_role') !== 'social_worker')
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>Client Eligibility</span></a></li>
        @endif
        @if((string) session('admin_user_role') !== 'eligibility_checker')
        <li><a href="/admin/social-case/submitted"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        @endif
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li class="sidebar-dropdown" id="onlineRequestsDropdown">
            <a href="#" class="sidebar-dropdown-toggle" onclick="toggleDropdown('onlineRequestsDropdown'); return false;">
                <i data-lucide="file-text" style="width:20px;height:20px"></i>
                <span>Online Requests</span>
                <i data-lucide="chevron-down" class="dropdown-chevron" style="width:16px;height:16px;margin-left:auto;"></i>
            </a>
            <ul class="sidebar-dropdown-menu">
                <li><a href="/admin/social-case/online-requests"><i data-lucide="clock" style="width:18px;height:18px"></i><span>Pending Requests</span><span class="badge-count badge-pending" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#F59E0B;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['pending'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/accepted"><i data-lucide="check-circle" style="width:18px;height:18px"></i><span>Accepted Requests</span><span class="badge-count badge-accepted" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#10B981;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['accepted'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/rejected"><i data-lucide="x-circle" style="width:18px;height:18px"></i><span>Rejected Requests</span><span class="badge-count badge-rejected" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#EF4444;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['rejected'] ?? 0 }}</span></a></li>
            </ul>
        </li>
        @endif
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <!-- Modern Page Header -->
    @php
        $userName = 'Social Case Study Officer';
        $words = explode(' ', $userName);
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $initials = strtoupper(substr($userName, 0, 2));
        }
    @endphp
    <!-- Page Sub-Header -->
    <div class="flex justify-between items-center mb-6">
        <p class="text-[#6B7280] text-sm m-0">Step 2 of 2 — Complete the intake form for the social case study.</p>
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

    <div id="intakeFormContent" style="overflow-y: visible; height: auto; padding-right: 10px;" class="intake-scroll"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') . '?v=' . filemtime(public_path('js/social-case.js')) }}"></script>
<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle('open');
    }
}

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        loadIntakeForm();
    });
</script>
@endpush

@push('styles')
<style>
    /* ── Senior Citizen Style Tokens & Form Card ── */
    .form-card {
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 32px;
        overflow: visible;
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 6px;
        display: block;
        text-transform: uppercase;
        letter-spacing: .3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .form-input {
        width: 100%;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: var(--text-primary);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        font-family: var(--font-family);
        height: 44px;
        box-sizing: border-box;
    }
    .form-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(26, 35, 126, .1);
    }
    .form-input::placeholder {
        color: var(--text-muted);
    }
    .form-input[readonly] {
        background-color: #F8FAFC;
        color: var(--text-secondary);
        cursor: default;
    }
    select.form-input {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        background-size: 1rem;
        padding-right: 2.5rem;
    }
    textarea.form-input {
        resize: vertical;
        height: auto;
        min-height: 90px;
    }
    input[type="date"].form-input {
        position: relative;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236B7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3e%3cline x1='16' y1='2' x2='16' y2='6'/%3e%3cline x1='8' y1='2' x2='8' y2='6'/%3e%3cline x1='3' y1='10' x2='21' y2='10'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 18px;
        padding-right: 38px;
    }
    input[type="date"].form-input::-webkit-calendar-picker-indicator {
        opacity: 0;
        cursor: pointer;
        position: absolute;
        right: 0;
        top: 0;
        width: 38px;
        height: 100%;
    }
    input[type="number"].form-input {
        -moz-appearance: textfield;
        appearance: textfield;
    }
    input[type="number"].form-input::-webkit-inner-spin-button,
    input[type="number"].form-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .btn {
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text-primary);
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow);
        transition: all .2s ease;
        height: 42px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn:hover {
        border-color: var(--primary);
        transform: translateY(-1px);
    }
    .btn.primary {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }
    .btn.primary:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
    }
    .btn-sm {
        padding: 5px 12px;
        font-size: 12.5px;
        height: 34px;
        width: auto;
        max-width: max-content;
    }

    /* ── Agency & Requirement Cards ── */
    .agency-card, .req-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--surface);
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }
    .agency-card:hover, .req-card:hover {
        border-color: var(--primary);
        background: #F8FAFC;
    }
    .agency-card.selected, .req-card.selected {
        background: var(--primary);
        border-color: var(--primary);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(26, 35, 126, 0.2);
    }
    .agency-card.selected:hover, .req-card.selected:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
    }
    .agency-checkbox-box, .req-checkbox-box {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        border: 2px solid var(--border);
        background: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .agency-card.selected .agency-checkbox-box,
    .req-card.selected .req-checkbox-box {
        background: #FFFFFF;
        border-color: #FFFFFF;
    }
    .agency-card.selected .agency-checkbox-box svg,
    .req-card.selected .req-checkbox-box svg {
        stroke: var(--primary) !important;
    }
    .agency-card.selected .agency-name,
    .agency-card.selected .agency-abbr,
    .req-card.selected .req-name {
        color: #FFFFFF !important;
    }

    /* ── Family Composition Table ── */
    .family-table-container {
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        background: var(--surface);
    }
    .family-table {
        width: 100%;
        border-collapse: collapse;
    }
    .family-table th {
        background: #F9FAFB;
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: .04em;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    .family-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .family-table tr:last-child td {
        border-bottom: none;
    }
    .family-table .form-input {
        height: 38px;
        padding: 6px 10px;
        font-size: 13px;
        border-radius: 8px;
    }
    .btn-icon-danger {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #FEE2E2;
        background: #FEF2F2;
        color: #DC2626;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-icon-danger:hover {
        background: #DC2626;
        color: #FFFFFF;
        border-color: #DC2626;
    }

    /* ── Custom Modal Styles (Matching Senior Citizen Registration) ── */
    .custom-modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        z-index: 99999;
        backdrop-filter: blur(4px);
        transition: opacity 0.2s ease;
    }
    .custom-modal-backdrop.active {
        display: flex;
    }
    .custom-modal-dialog {
        background: var(--background);
        border-radius: 14px;
        width: 100%;
        max-width: 820px;
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .custom-modal-header {
        background: #1A237E;
        color: #ffffff;
        padding: 14px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }
    .custom-modal-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #ffffff;
    }
    .custom-modal-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2px;
        font-size: 22px;
    }
    .custom-modal-close:hover { opacity: 1; }
    .custom-modal-body {
        padding: 18px 22px;
        overflow-y: auto;
        flex: 1;
        -webkit-overflow-scrolling: touch;
    }
    .custom-modal-section {
        margin-bottom: 18px;
    }
    .custom-modal-section:last-child {
        margin-bottom: 0;
    }
    .custom-modal-section-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
        padding-bottom: 6px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .custom-modal-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    .custom-modal-col-full {
        grid-column: 1 / -1;
    }
    .custom-modal-col-span2 {
        grid-column: span 2;
    }
    .custom-modal-field {
        margin-bottom: 0;
    }
    .custom-modal-label {
        font-weight: 600;
        color: var(--text-muted);
        font-size: 0.72rem;
        display: block;
        margin-bottom: 3px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .custom-modal-value {
        background: var(--surface);
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 500;
        border: 1px solid var(--border);
        color: var(--text-primary);
        font-size: 0.85rem;
        overflow-wrap: anywhere;
        min-height: 36px;
        display: flex;
        align-items: center;
    }
    .custom-modal-footer {
        padding: 12px 22px;
        border-top: 1px solid var(--border);
        background: var(--surface);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-shrink: 0;
    }

    @media (max-width: 991.98px) {
        .custom-modal-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .custom-modal-col-span2 {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 767.98px) {
        .form-card {
            padding: 20px 16px;
        }
        .family-table thead {
            display: none;
        }
        .family-table, .family-table tbody, .family-table tr, .family-table td {
            display: block;
            width: 100%;
        }
        .family-table tr {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            position: relative;
            background: #FAFAFA;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .family-table td {
            padding: 4px 0 !important;
            border-bottom: none !important;
        }
        .family-mobile-label {
            display: block !important;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .custom-modal-backdrop {
            padding: 8px;
        }
        .custom-modal-grid {
            grid-template-columns: 1fr !important;
            gap: 8px;
        }
        .custom-modal-col-full, .custom-modal-col-span2 {
            grid-column: 1 / -1 !important;
        }
    }

    /* ── SweetAlert Custom Popup & Scrollbar ── */
    .swal2-popup.swal-custom-popup {
        border-radius: 18px !important;
        padding: 24px 20px !important;
        font-family: var(--font-family) !important;
        box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.25) !important;
        max-width: 480px !important;
        width: 92% !important;
    }
    .swal2-popup.swal-custom-popup .swal2-title {
        padding: 0 !important;
        margin: 12px 0 0 0 !important;
    }
    .swal2-popup.swal-custom-popup .swal2-html-container {
        margin: 10px 0 0 0 !important;
        padding: 0 !important;
    }
    .swal2-popup.swal-custom-popup .swal2-actions {
        margin: 16px 0 0 0 !important;
    }
    .swal2-popup.swal-custom-popup .swal2-confirm {
        border-radius: 10px !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        padding: 10px 24px !important;
        box-shadow: 0 4px 12px rgba(26, 35, 126, 0.2) !important;
    }
    .swal-error-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .swal-error-scroll::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 4px;
    }
    .swal-error-scroll::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 4px;
    }
    .swal-error-scroll::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }
</style>
@endpush
