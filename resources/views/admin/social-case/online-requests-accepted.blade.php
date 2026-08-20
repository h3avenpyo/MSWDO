@extends('admin.social-case.layout')
@section('title', 'Accepted Online Requests')
@section('page_title', 'Accepted Online Requests')

@section('content')
<style>
    /* Panel & Table Styles */
    .online-requests-panel {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 0;
        padding: 0;
        height: 80vh;
        display: flex;
        flex-direction: column;
    }
    .online-requests-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        background: #fff;
        flex: 1;
        min-height: 0;
    }
    
    /* Table Base */
    #onlineRequestsTable {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: auto;
    }
    #onlineRequestsTable thead tr {
        background: #F8FAFC;
        border-bottom: 2px solid #E2E8F0;
    }
    #onlineRequestsTable thead th {
        padding: 12px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        white-space: nowrap;
        text-align: left;
        border-bottom: 2px solid #E2E8F0;
    }
    #onlineRequestsTable tbody tr {
        border-bottom: 1px solid #F1F5F9;
        transition: background .15s;
    }
    #onlineRequestsTable tbody tr:last-child {
        border-bottom: none;
    }
    #onlineRequestsTable tbody td {
        padding: 12px 14px;
        font-size: 0.875rem;
        color: #1E293B;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
    }
    
    /* Column specific spacing */
    #onlineRequestsTable tbody td[data-label="Name"] {
        min-width: 200px;
        font-weight: 600;
        color: #0F172A;
        white-space: normal;
        word-break: break-word;
    }
    #onlineRequestsTable tbody td[data-label="Contact"] {
        min-width: 140px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Service Type"] {
        min-width: 150px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Assistance Type"] {
        min-width: 150px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Barangay"] {
        min-width: 140px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Date Accepted"] {
        min-width: 140px;
        white-space: nowrap;
        color: #475569;
    }
    #onlineRequestsTable tbody td[data-label="Action"] {
        min-width: 100px;
        white-space: nowrap;
    }
    
    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .status-approved {
        background: #DCFCE7;
        color: #15803D;
    }
    
    /* Empty State */
    .empty-cell {
        padding: 0 !important;
        text-align: center !important;
        border: none !important;
        vertical-align: middle !important;
        height: 100%;
    }
    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 12px;
        padding: 2rem 1rem;
        margin-top: 120px;
    }
    .empty-icon-wrap {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #EEF2FF;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1A237E;
    }
    .empty-icon-wrap svg {
        width: 36px;
        height: 36px;
    }
    .empty-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1F2937;
        margin: 0;
    }
    .empty-subtitle {
        font-size: 0.875rem;
        color: #6B7280;
        margin: 0;
        line-height: 1.5;
        max-width: 360px;
    }
    
    /* Pagination */
    .sc-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 14px;
        flex-shrink: 0;
        padding: 4px 0;
        flex-wrap: wrap;
    }
    .sc-pagination-info {
        font-size: 0.813rem;
        color: #6B7280;
        font-weight: 500;
    }
    .sc-pagination-controls {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }
    .sc-page-btn {
        height: 36px;
        min-width: 36px;
        padding: 0 10px;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        background: #fff;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s;
    }
    .sc-page-btn:hover:not(:disabled) {
        background: #F8FAFC;
        border-color: #CBD5E1;
    }
    .sc-page-btn.active {
        background: #1A237E;
        color: #fff;
        border-color: #1A237E;
    }
    .sc-page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Action Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    .btn-primary {
        background: #1A237E;
        color: #fff;
    }
    .btn-primary:hover {
        background: #121858;
    }
    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    /* ── Sidebar Badge Styling ── */
    .sidebar-badge {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-left: auto;
    }

    .badge-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 0.7rem;
        font-weight: 700;
        line-height: 1;
        color: #fff;
    }

    .badge-pending {
        background: #F59E0B;
    }

    .badge-accepted {
        background: #10B981;
    }

    .badge-rejected {
        background: #EF4444;
    }
</style>

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
            <p class="mobile-brand-subtitle">Accepted Online Requests</p>
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
        <i data-lucide="file-text" style="width:24px;height:24px"></i>
        <span>Social Case Study</span>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin/social-case/dashboard"><i data-lucide="layout-dashboard" style="width:20px;height:20px"></i><span>Dashboard</span></a></li>
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>Client Eligibility</span></a></li>
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li><a href="#" onclick="return false" style="opacity:0.5;pointer-events:none;cursor:not-allowed" title="Not available for eligibility checker accounts"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @else
        <li><a href="/admin/social-case/submitted"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @endif
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        @if((string) session('admin_user_role') === 'eligibility_checker' || (string) session('admin_user_role') === 'social_worker')
        <li class="sidebar-dropdown open" id="onlineRequestsDropdown">
            <a href="#" class="sidebar-dropdown-toggle" onclick="toggleDropdown('onlineRequestsDropdown'); return false;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <i data-lucide="file-text" style="width:20px;height:20px"></i>
                    <span>Online Requests</span>
                </div>
                <i data-lucide="chevron-down" style="width:16px;height:16px"></i>
            </a>
            <ul class="sidebar-dropdown-menu">
                <li><a href="/admin/social-case/online-requests">Pending Requests <span class="badge-count badge-pending" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#F59E0B;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['pending'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/accepted" class="active">Accepted Requests <span class="badge-count badge-accepted" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#10B981;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['accepted'] ?? 0 }}</span></a></li>
                <li><a href="/admin/social-case/online-requests/rejected">Rejected Requests <span class="badge-count badge-rejected" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#EF4444;color:#fff;font-size:0.7rem;font-weight:700;margin-left:auto;">{{ $onlineRequestCounts['rejected'] ?? 0 }}</span></a></li>
            </ul>
        </li>
        @endif
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <header class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none mb-4 lg:mb-2">
        <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Accepted Online Requests</h1>
    </header>
    <div style="margin-bottom:10px;">
        <p class="text-sm text-slate-500 m-0">View and manage accepted online service requests from the public.</p>
    </div>

    <div class="online-requests-panel">
        <div class="online-requests-table-wrap">
            <table id="onlineRequestsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Service Type</th>
                        <th>Assistance Type</th>
                        <th>Barangay</th>
                        <th>Date Accepted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($acceptedRequests as $req)
                    <tr data-name="{{ $req->first_name }} {{ $req->last_name }}">
                        <td data-label="Name">
                            <div style="display:flex;align-items:center;flex-wrap:wrap;position:relative;">
                                <span>{{ $req->first_name }} {{ $req->last_name }}</span>
                            </div>
                            <div class="text-xs text-slate-500">{{ $req->email }}</div>
                        </td>
                        <td data-label="Contact">{{ $req->contact_number }}</td>
                        <td data-label="Service Type">{{ ucfirst(str_replace('_', ' ', $req->service_type)) }}</td>
                        <td data-label="Assistance Type">{{ ucfirst(str_replace('_', ' ', $req->assistance_type)) }}</td>
                        <td data-label="Barangay">{{ $req->barangay }}</td>
                        <td data-label="Date Accepted">{{ $req->updated_at->format('M d, Y') }}</td>
                        <td data-label="Action">
                            <button class="btn btn-primary btn-sm" onclick="viewOnlineRequest({{ $req->id }})" title="View">
                                <i data-lucide="eye" style="width:14px;height:14px"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7" class="empty-cell">
                            <div class="empty-state-content">
                                <div class="empty-icon-wrap">
                                    <i data-lucide="check-circle"></i>
                                </div>
                                <div class="empty-title">No accepted requests</div>
                                <div class="empty-subtitle">Accepted online service requests will appear here after approval.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="sc-pagination">
            <div class="sc-pagination-info">
                @if($acceptedRequests->count() > 0)
                    Showing {{ $acceptedRequests->firstItem() }} to {{ $acceptedRequests->lastItem() }} of {{ $acceptedRequests->total() }} requests
                @else
                    Showing 0 of 0 requests
                @endif
            </div>
            <div class="sc-pagination-controls">
                @if($acceptedRequests->hasPages())
                    @if($acceptedRequests->onFirstPage())
                        <span class="sc-page-btn" disabled>Previous</span>
                    @else
                        <a href="{{ $acceptedRequests->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                    @endif
                    
                    @foreach($acceptedRequests->getUrlRange(1, $acceptedRequests->lastPage()) as $page => $url)
                        @if($page == $acceptedRequests->currentPage())
                            <span class="sc-page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="sc-page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                    
                    @if($acceptedRequests->hasMorePages())
                        @if($acceptedRequests->onLastPage())
                            <span class="sc-page-btn" disabled>Next</span>
                        @else
                            <a href="{{ $acceptedRequests->nextPageUrl() }}" class="sc-page-btn">Next</a>
                        @endif
                    @endif
                @else
                    <span class="sc-page-btn" disabled>Previous</span>
                    <span class="sc-page-btn active">1</span>
                    <span class="sc-page-btn" disabled>Next</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle('open');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

function viewOnlineRequest(id) {
    // Fetch the online request details and show in a modal
    fetch(`/admin/social-case/online-requests/${id}`)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: '<div style="display: flex; align-items: center; gap: 10px;"><i data-lucide="file-text" style="width: 24px; height: 24px; color: #1A237E;"></i><span>Online Request Details</span></div>',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Personal Information</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Full Name</span>
                                    <span style="font-size: 14px; color: #1F2937; font-weight: 500;">${data.first_name} ${data.last_name}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Email</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.email}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Contact Number</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.contact_number}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Barangay</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.barangay}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Service Information</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Service Type</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.service_type}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Assistance Type</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.assistance_type}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Request Details</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Status</span>
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #DCFCE7; color: #15803D;">${data.status}</span>
                                </div>
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Date Submitted</span>
                                    <span style="font-size: 14px; color: #1F2937;">${data.created_at}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Additional Information</h4>
                            <div style="margin-bottom: 12px;">
                                <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Situation</span>
                                <p style="margin: 0; font-size: 14px; color: #1F2937; line-height: 1.5;">${data.situation}</p>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Notes</span>
                                <p style="margin: 0; font-size: 14px; color: #1F2937; line-height: 1.5;">${data.notes}</p>
                            </div>
                            ${data.attachments_html}
                        </div>
                    </div>
                `,
                icon: false,
                confirmButtonText: 'Close',
                confirmButtonColor: '#1A237E',
                width: '600px',
                didOpen: () => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Failed to load request details',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}
</script>
@endpush
