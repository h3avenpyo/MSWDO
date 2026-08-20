@extends('admin.social-case.layout')
@section('title', 'Online Requests')
@section('page_title', 'Online Requests')

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
    #onlineRequestsTable tbody tr:hover {
        background: #F8FAFC;
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
    #onlineRequestsTable tbody td[data-label="Status"] {
        min-width: 120px;
        white-space: nowrap;
    }
    #onlineRequestsTable tbody td[data-label="Date"] {
        min-width: 140px;
        white-space: nowrap;
        color: #64748B;
        font-size: 0.813rem;
    }
    #onlineRequestsTable tbody td[data-label="Action"] {
        min-width: 140px;
        white-space: nowrap;
    }
    
    /* Badge Status */
    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: .04em;
    }
    .badge-status.pending {
        background: #FEF3C7;
        color: #92400E;
    }
    .badge-status.approved {
        background: #DCFCE7;
        color: #15803D;
    }
    .badge-status.rejected {
        background: #FEE2E2;
        color: #DC2626;
    }
    .badge-status.in_progress {
        background: #DBEAFE;
        color: #1E40AF;
    }
    .badge-status.archived {
        background: #E5E7EB;
        color: #6B7280;
    }
    .badge-status.approved {
        background: #DCFCE7;
        color: #15803D;
    }

    /* Warning indicator */
    .warning-sign {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-left: 8px;
        color: #DC2626;
        cursor: help;
        vertical-align: middle;
        animation: warningFloat 2s ease-in-out infinite;
    }
    .warning-sign svg { width: 28px; height: 28px; }
    .warning-sign:hover { color: #B91C1C; }
    @keyframes warningFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .warning-tooltip {
        position: absolute;
        background: #1F2937;
        color: #fff;
        font-size: 0.75rem;
        padding: 6px 10px;
        border-radius: 6px;
        white-space: nowrap;
        max-width: 260px;
        z-index: 50;
        pointer-events: none;
        opacity: 0;
        transition: opacity .15s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        line-height: 1.4;
    }
    .warning-sign:hover .warning-tooltip { opacity: 1; }
    
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
        font-size: 0.813rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all .15s;
        text-decoration: none;
    }
    .sc-page-btn:hover:not(:disabled) {
        background: #F3F4F6;
        border-color: #D1D5DB;
    }
    .sc-page-btn.active {
        background: #1A237E;
        color: #fff;
        border-color: #1A237E;
        font-weight: 700;
    }
    .sc-page-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    
    /* Empty State */
    .empty-row {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
    .empty-cell {
        padding: 3rem 1rem !important;
        text-align: center !important;
        border: none !important;
    }
    .empty-cell::before {
        display: none !important;
        content: none !important;
    }
    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .empty-icon-wrap {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #EEF2FF;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
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
        margin-bottom: 6px;
    }
    .empty-subtitle {
        font-size: 0.875rem;
        color: #6B7280;
        line-height: 1.5;
        max-width: 360px;
    }
    
    /* Mobile Responsive */
    @media (max-width: 1199.98px) {
        .online-requests-panel {
            background: transparent;
            border: none;
            padding: 0;
            box-shadow: none;
        }
        .online-requests-table-wrap {
            overflow: visible;
            border: none;
            background: transparent;
        }
        
        #onlineRequestsTable,
        #onlineRequestsTable thead,
        #onlineRequestsTable tbody,
        #onlineRequestsTable tbody tr,
        #onlineRequestsTable tbody td {
            display: block !important;
            width: 100% !important;
        }
        #onlineRequestsTable {
            min-width: 0 !important;
        }
        #onlineRequestsTable thead {
            display: none !important;
        }
        
        #onlineRequestsTable tbody tr:not(.empty-row) {
            background: #ffffff !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 12px !important;
            margin-bottom: 12px !important;
            padding: 14px 18px !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        #onlineRequestsTable tbody tr:not(.empty-row):hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }
        
        #onlineRequestsTable tbody td {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 8px 0 !important;
            border-bottom: 1px solid #F1F5F9 !important;
            font-size: 0.875rem !important;
            gap: 12px !important;
            white-space: normal !important;
            word-break: break-word !important;
            max-width: none !important;
            overflow: visible !important;
            min-width: 0 !important;
        }
        #onlineRequestsTable tbody td:last-child {
            border-bottom: none !important;
        }
        #onlineRequestsTable tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            font-size: 0.75rem;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            min-width: 120px;
            flex-shrink: 0;
        }
    }
</style>

<!-- Mobile Header -->
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
            <p class="mobile-brand-subtitle">Online Requests</p>
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
        <li><a href="/admin/social-case/new"><i data-lucide="user-plus" style="width:20px;height:20px"></i><span>New case</span></a></li>
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li><a href="#" onclick="return false" style="opacity:0.5;pointer-events:none;cursor:not-allowed" title="Not available for eligibility checker accounts"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @else
        <li><a href="/admin/social-case/submitted"><i data-lucide="send" style="width:20px;height:20px"></i><span>Submitted Cases</span></a></li>
        @endif
        <li><a href="/admin/social-case/cases"><i data-lucide="list" style="width:20px;height:20px"></i><span>All cases</span></a></li>
        <li><a href="/admin/social-case/archive"><i data-lucide="archive" style="width:20px;height:20px"></i><span>Archive</span></a></li>
        @if((string) session('admin_user_role') === 'eligibility_checker')
        <li><a href="/admin/social-case/online-requests" class="active"><i data-lucide="file-text" style="width:20px;height:20px"></i><span>Online Requests</span></a></li>
        @elseif((string) session('admin_user_role') === 'social_worker')
        <li><a href="#" onclick="return false" style="opacity:0.5;pointer-events:none;cursor:not-allowed" title="Not available for social worker accounts"><i data-lucide="file-text" style="width:20px;height:20px"></i><span>Online Requests</span></a></li>
        @endif
        <li><a href="#" onclick="confirmLogout(event)"><i data-lucide="log-out" style="width:20px;height:20px"></i><span>Logout</span></a></li>
    </ul>
</div>

<div class="main">
    <div style="margin-bottom:10px;">
        <p class="text-sm text-slate-500 m-0">View and manage online service requests from the public.</p>
    </div>

    <!-- Table Panel -->
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
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($onlineRequests as $request)
                    <tr>
                        <td data-label="Name">
                            <div style="display:flex;align-items:center;flex-wrap:wrap;position:relative;">
                                <span>{{ $request->first_name }} {{ $request->last_name }}</span>
                                @if($request->warning_existing || $request->warning_recent)
                                <span class="warning-sign" title="@if($request->warning_existing)Client already exists in records.@endif @if($request->warning_recent)Has request history within the last 6 months.@endif" style="position:relative;">
                                    <i data-lucide="alert-triangle" style="width:28px;height:28px"></i>
                                    <span class="warning-tooltip" style="top:100%;left:0;margin-top:6px;">
                                        @if($request->warning_existing && $request->warning_recent)Client already exists in records and has request history within the last 6 months.
                                        @elseif($request->warning_existing)This client name already exists in the records.
                                        @elseHas request history within the last 6 months.@endif
                                    </span>
                                </span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-500">{{ $request->email }}</div>
                        </td>
                        <td data-label="Contact">{{ $request->contact_number }}</td>
                        <td data-label="Service Type">{{ ucfirst(str_replace('_', ' ', $request->service_type)) }}</td>
                        <td data-label="Assistance Type">{{ ucfirst(str_replace('_', ' ', $request->assistance_type)) }}</td>
                        <td data-label="Barangay">{{ $request->barangay }}</td>
                        <td data-label="Status">
                            <span class="badge-status {{ $request->status }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td data-label="Date">
                            <div>{{ $request->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-slate-400">{{ $request->created_at->format('g:i A') }}</div>
                        </td>
                        <td data-label="Action">
                            <button class="btn primary btn-sm" onclick="viewOnlineRequest({{ $request->id }})" title="View">
                                <i data-lucide="eye" style="width:14px;height:14px"></i>
                            </button>
                            @if($request->status !== 'archived' && $request->status !== 'approved')
                            <button class="btn btn-sm" onclick="archiveOnlineRequest({{ $request->id }})" title="Archive" style="background: #DC2626; color: white; border: none;">
                                <i data-lucide="archive" style="width:14px;height:14px"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="8" class="empty-cell">
                            <div class="empty-state-content">
                                <div class="empty-icon-wrap">
                                    <i data-lucide="inbox"></i>
                                </div>
                                <div class="empty-title">No requests yet</div>
                                <div class="empty-subtitle">Online service requests will appear here once users submit them through the website.</div>
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
            @if($onlineRequests->count() > 0)
                Showing {{ $onlineRequests->firstItem() }} to {{ $onlineRequests->lastItem() }} of {{ $onlineRequests->total() }} requests
            @else
                Showing 0 of 0 requests
            @endif
        </div>
        <div class="sc-pagination-controls">
            @if($onlineRequests->hasPages())
                @if($onlineRequests->onFirstPage())
                    <span class="sc-page-btn" disabled>Previous</span>
                @else
                    <a href="{{ $onlineRequests->previousPageUrl() }}" class="sc-page-btn">Previous</a>
                @endif
                
                @foreach($onlineRequests->getUrlRange(1, $onlineRequests->lastPage()) as $page => $url)
                    @if($page == $onlineRequests->currentPage())
                        <span class="sc-page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="sc-page-btn">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($onlineRequests->hasMorePages())
                    @if($onlineRequests->onLastPage())
                        <span class="sc-page-btn" disabled>Next</span>
                    @else
                        <a href="{{ $onlineRequests->nextPageUrl() }}" class="sc-page-btn">Next</a>
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
@endsection

@push('scripts')
<script>
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
            const showAcceptButton = data.status !== 'approved' && data.status !== 'archived';
            const showDeclineButton = data.status !== 'rejected' && data.status !== 'archived' && data.status !== 'approved';
            
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
                        
                        <div style="background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                            <h4 style="margin: 0 0 12px 0; color: #1A237E; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Request Details</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                <div>
                                    <span style="display: block; font-size: 11px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Status</span>
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: ${data.status === 'Pending' ? '#FEF3C7' : data.status === 'Approved' ? '#DCFCE7' : data.status === 'Rejected' ? '#FEE2E2' : data.status === 'In progress' ? '#DBEAFE' : '#E5E7EB'}; color: ${data.status === 'Pending' ? '#92400E' : data.status === 'Approved' ? '#15803D' : data.status === 'Rejected' ? '#DC2626' : data.status === 'In progress' ? '#1E40AF' : '#6B7280'};">${data.status}</span>
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
                showCancelButton: true,
                showConfirmButton: showAcceptButton,
                showDenyButton: showDeclineButton,
                confirmButtonText: 'Accept Request',
                denyButtonText: 'Decline',
                cancelButtonText: 'Close',
                confirmButtonColor: '#15803D',
                denyButtonColor: '#DC2626',
                cancelButtonColor: '#6B7280',
                width: '600px',
                didOpen: () => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                },
                preConfirm: () => {
                    if (!showAcceptButton) {
                        return false;
                    }
                    return new Promise((resolve) => {
                        Swal.fire({
                            title: 'Accept Request',
                            text: 'Are you sure you want to accept this request? This will send an email notification to the applicant.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Accept',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#15803D',
                            cancelButtonColor: '#6B7280'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Call the accept function
                                acceptOnlineRequest(id).then(resolve);
                            } else {
                                resolve(false);
                            }
                        });
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && result.value === true) {
                    Swal.fire({
                        title: 'Request Accepted',
                        text: 'The request has been accepted and an email notification has been sent.',
                        icon: 'success',
                        confirmButtonColor: '#15803D',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else if (result.isDenied) {
                    declineOnlineRequest(id);
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

function declineOnlineRequest(id) {
    const reasons = [
        'Incomplete requirements',
        'Duplicate request',
        'Ineligible for assistance',
        'Incorrect information',
        'Outside coverage area',
        'Other'
    ];
    const reasonOptions = reasons.map(function (r, i) {
        return '<option value="' + r + '"' + (i === 0 ? ' selected' : '') + '>' + r + '</option>';
    }).join('');

    Swal.fire({
        title: 'Decline Request',
        html: '<div style="text-align:left">' +
                '<p style="font-size:14px;color:#374151;margin:0 0 14px;">Please select a reason for declining this request. The applicant will be notified via email.</p>' +
                '<label style="display:block;font-size:11px;color:#6B7280;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Reason</label>' +
                '<select id="declineReason" style="width:100%;padding:10px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;color:#111827;background:#fff;margin-bottom:14px;">' + reasonOptions + '</select>' +
                '<label id="otherReasonLabel" style="display:none;font-size:11px;color:#6B7280;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Specify reason</label>' +
                '<textarea id="otherReason" style="display:none;width:100%;padding:10px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:14px;color:#111827;min-height:70px;resize:vertical;" placeholder="Enter the reason..."></textarea>' +
              '</div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Decline',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        didOpen: function () {
            var select = document.getElementById('declineReason');
            var otherLabel = document.getElementById('otherReasonLabel');
            var otherText = document.getElementById('otherReason');
            select.addEventListener('change', function () {
                var showOther = select.value === 'Other';
                otherLabel.style.display = showOther ? 'block' : 'none';
                otherText.style.display = showOther ? 'block' : 'none';
            });
        },
        preConfirm: function () {
            var select = document.getElementById('declineReason');
            var otherText = document.getElementById('otherReason');
            var reason = select ? select.value : '';
            if (reason === 'Other') {
                reason = (otherText ? otherText.value : '').trim();
                if (!reason) {
                    Swal.showValidationMessage('Please specify a reason');
                    return false;
                }
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const reason = result.value;
            fetch(`/admin/social-case/online-requests/${id}/decline`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Declined!',
                        text: 'Online request has been declined and the applicant has been notified via email.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to decline request',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to decline request',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}

function acceptOnlineRequest(id) {
    return new Promise((resolve) => {
        fetch(`/admin/social-case/online-requests/${id}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resolve(true);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Failed to accept request',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                resolve(false);
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Failed to accept request',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            resolve(false);
        });
    });
}

function archiveOnlineRequest(id) {
    Swal.fire({
        title: 'Archive Request',
        text: 'Are you sure you want to archive this online request?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, archive it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/social-case/online-requests/${id}/archive`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Archived!',
                        text: 'Online request has been archived.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Failed to archive request',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to archive request',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}
</script>
@endpush