@extends('layouts.admin-tailwind')

@section('title', 'Referral Info - iSERVE SILANG Admin')

@push('styles')
<style>
    /* Prevent page scrolling - only table should scroll */
    html, body { overflow: hidden !important; height: 100vh !important; }
    #mainContent { height: 100vh !important; overflow: hidden !important; }
    #recipients-table { width: 100% !important; min-width: 0 !important; max-width: 100% !important; table-layout: fixed !important; }
    #recipients-table th,
    #recipients-table td { vertical-align: middle; }
    #recipients-table th:not(.recipient-actions-heading),
    #recipients-table td:not(.recipient-actions-cell) {
        overflow: visible;
        text-overflow: clip;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.35;
    }
    #recipients-table .recipient-actions-heading,
    #recipients-table .recipient-actions-cell { text-align: center !important; }
    #recipients-table .recipient-actions-cell { overflow: visible; padding-left: 0.75rem; padding-right: 0.75rem; }
    #recipients-table .recipient-actions { display: inline-flex; max-width: 100%; align-items: center; justify-content: center; gap: 0.5rem; white-space: nowrap; }
    #recipients-table .recipient-actions > button { flex: 0 0 auto; }
    /* Android tablets (600px–1280px): the shared Admin navigation is a
       drawer; this page keeps its table readable in an isolated scroll area. */
    @media (min-width: 37.5rem) and (max-width: 80rem) {
        html, body { height: auto !important; overflow: auto !important; }
        #mainContent { height: auto !important; overflow: visible !important; }
        #referral-info-main {
            height: auto !important;
            min-height: 0;
            overflow: visible !important;
            padding: clamp(1rem, 2vw, 1.5rem) !important;
        }
        #referral-info-summary,
        #recipients-search-card { padding: clamp(1rem, 2vw, 1.5rem) !important; }
        #recipients-search-card > div { flex-wrap: wrap; }
        #recipients-search { width: 100%; margin-left: 0; }
        #recipients-table-panel { min-width: 0; }
        #recipients-table { min-width: 56rem !important; }
        #recipients-table col:nth-child(1) { width: 14% !important; }
        #recipients-table col:nth-child(2) { width: 22% !important; }
        #recipients-table col:nth-child(3) { width: 14% !important; }
        #recipients-table col:nth-child(4) { width: 10% !important; }
        #recipients-table col:nth-child(5) { width: 10% !important; }
        #recipients-table col:nth-child(6) { width: 10% !important; }
        #recipients-table col:nth-child(7) { width: 20% !important; }
        #recipients-table th,
        #recipients-table td {
            padding: .75rem clamp(.625rem, 1.2vw, 1rem) !important;
            font-size: clamp(.6875rem, 1.15vw, .8125rem) !important;
            line-height: 1.4;
        }
        #recipients-table .recipient-actions-cell { padding-inline: .75rem !important; }
        #recipients-table .recipient-actions { gap: .375rem !important; }
        #recipients-table .recipient-actions > button {
            width: 2.25rem;
            min-width: 2.25rem;
            height: 2.25rem;
            min-height: 2.25rem;
            padding: 0 !important;
            justify-content: center;
        }
    }

    /* Phone-only recipient cards. Tablet and desktop continue using the
       existing table layout. */
    @media (max-width: 47.9375rem) {
        html, body { height: auto !important; min-height: 100%; overflow-x: hidden !important; overflow-y: auto !important; }
        #mainContent { height: auto !important; overflow: visible !important; }
        #referral-info-main {
            height: auto !important;
            min-height: 0;
            overflow: visible !important;
            padding-bottom: calc(1rem + env(safe-area-inset-bottom)) !important;
        }
        #recipients-table-panel { display: none !important; }
        #recipients-mobile-cards { display: flex !important; }
        #recipientsCardsMobile { overflow: visible !important; flex: none !important; }
        #recipientsCardsMobile .recipient-row { min-width: 0; padding: 1rem; }
        #recipientsCardsMobile .recipient-row > div:nth-child(2) { flex-wrap: wrap; gap: .5rem 1rem; }
        #recipientsCardsMobile .recipient-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            column-gap: .5rem !important;
            row-gap: .375rem !important;
            padding-top: .5rem !important;
        }

        #recipientsCardsMobile .recipient-card-actions > :is(button, a) {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            min-width: 0 !important;
            height: 2.375rem !important;
            min-height: 2.375rem !important;
            max-height: 2.375rem !important;
            padding: 0 .5rem !important;
            gap: .375rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
            box-sizing: border-box !important;
            border-radius: .5rem !important;
            text-decoration: none !important;
        }

        #recipientsCardsMobile .recipient-card-actions > :is(button, a) i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: .8125rem !important;
            line-height: 1 !important;
        }
    }
    @media (max-width: 40rem) {
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $matchReportTable = auth()->user()->isDepartmentAdmin();
@endphp

<!-- Top Header -->
<header class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            @if(auth()->user()?->isDepartmentAdmin())
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            @endif
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Referral Info</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">Manage email recipients</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @include('admin-tailwind.components.notifications')
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main id="referral-info-main" class="px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 lg:pt-8 pb-4 flex flex-col h-[calc(100vh-80px)] overflow-hidden">
    
    <!-- Header Card -->
    <div id="referral-info-summary" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 flex-shrink-0">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Email Recipients Management</h3>
                    <p class="text-sm text-gray-500">Manage referral email recipients for case reports</p>
                </div>
            </div>
            
            <button id="addRecipientBtn" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-sm font-medium">
                <i class="fas fa-plus"></i>
                <span>Add New Recipient</span>
            </button>
        </div>
    </div>
    
    <!-- Search -->
    <div id="recipients-search-card" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-gray-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Recipients List</h3>
            <div id="recipients-search" class="ml-auto">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Search recipients..."
                        class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recipients Table (Desktop) -->
    <div id="recipients-table-panel" class="{{ $matchReportTable ? 'flex flex-col bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 min-h-0' : 'hidden md:flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex-1 min-h-0' }}">
        <div class="{{ $matchReportTable ? 'overflow-x-auto' : 'overflow-auto flex-1' }}">
            <table id="recipients-table" data-disable-action-controls="true" class="w-full table-fixed">
                <colgroup>
                    <col style="width: 16%"><col style="width: 21%"><col style="width: 14%"><col style="width: 11%"><col style="width: 9%"><col style="width: 8%"><col style="width: 21%">
                </colgroup>
                <thead class="{{ $matchReportTable ? 'bg-gradient-to-r from-purple-700 to-purple-600 text-white' : 'bg-gray-50 border-b border-gray-200 sticky top-0' }}">
                    <tr>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'w-[18%] px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}">Name</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'w-[22%] px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}">Email</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'w-[15%] px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}">Contact</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'w-[12%] px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}">Role</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-center text-sm font-semibold' : 'w-[10%] px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider' }}">Status</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'w-[13%] px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}">Created</th>
                        <th class="recipient-actions-heading {{ $matchReportTable ? 'px-6 py-4 text-center text-sm font-semibold' : 'w-[10%] px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider' }}">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="recipientsTableBody">
                    <!-- Loading state -->
                    <tr id="loading-row">
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin mb-3"></div>
                                <p class="text-gray-500 text-sm">Loading recipients...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls -->
        <div class="{{ $matchReportTable ? 'px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4 flex-shrink-0' : 'px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between flex-shrink-0' }}">
            <div class="text-sm text-gray-600">
                <span id="pagination-info">Showing 0 - 0 of 0 recipients</span>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    id="prev-page-btn" 
                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed rounded-lg transition-colors text-sm"
                    disabled
                >
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
                <div id="page-numbers" class="flex items-center gap-1">
                    <!-- Page numbers will be inserted here -->
                </div>
                <button 
                    id="next-page-btn" 
                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed rounded-lg transition-colors text-sm"
                    disabled
                >
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Recipients Cards (Mobile) -->
    <div id="recipients-mobile-cards" class="hidden flex-col flex-1 min-h-0">
        <div id="recipientsCardsMobile" class="space-y-4 overflow-auto flex-1">
            <!-- Loading state -->
            <div id="mobile-loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-gray-500 text-sm">Loading recipients...</p>
            </div>
        </div>
        
        <!-- Mobile Pagination -->
        <div class="mt-4 px-4 py-3 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-between flex-shrink-0">
            <div class="text-sm text-gray-600">
                <span id="mobile-pagination-info">Page 1 of 1</span>
            </div>
            <div class="flex items-center gap-2">
                <button 
                    id="mobile-prev-btn" 
                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed rounded-lg transition-colors text-sm"
                    disabled
                >
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
                <button 
                    id="mobile-next-btn" 
                    class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed rounded-lg transition-colors text-sm"
                    disabled
                >
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Add/Edit Modal -->
<div id="recipientModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="modalOverlay"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden relative">
            <!-- Modal Header -->
            <div class="bg-[#C41E3A] px-6 py-4 flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-semibold text-white">Add New Recipient</h3>
                <button id="closeModal" class="text-white/80 hover:text-white transition-colors @if(auth()->user()->isBcpcAdmin()) hidden @endif">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <form id="recipientForm" class="p-6">
                <input type="hidden" id="recipientId" value="">
                
                <div class="space-y-4">
                    <div>
                        <label for="recipientName" class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="recipientName" name="name" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                            placeholder="Full name">
                    </div>
                    
                    <div>
                        <label for="recipientEmail" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="recipientEmail" name="email" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                            placeholder="email@example.com">
                    </div>
                    
                    <div>
                        <label for="recipientContact" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Number
                        </label>
                        <input type="tel" id="recipientContact" name="contact_number" maxlength="11" inputmode="numeric"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                            placeholder="09123456789">
                    </div>
                    
                    <div>
                        <label for="recipientRole" class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        @if(auth()->user()->isDepartmentAdmin())
                        <select id="recipientRole" name="role"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm bg-white">
                            <option value="">Select role</option>
                            @if(auth()->user()->isVawcAdmin())
                            <option value="SWD - Staff">SWD - Staff</option>
                            <option value="SWD - Focal">SWD - Focal</option>
                            @else
                            <option value="BCPC - Staff">BCPC - Staff</option>
                            <option value="BCPC - Focal">BCPC - Focal</option>
                            @endif
                        </select>
                        @else
                        <input type="text" id="recipientRole" name="role"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm"
                            placeholder="e.g., Manager, Officer">
                        @endif
                    </div>
                </div>
                
                <div class="flex gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" id="cancelBtn" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit" id="saveBtn" class="flex-1 px-4 py-2.5 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-sm font-medium">
                        Save Recipient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Recipient Modal -->
<div id="viewRecipientModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="viewModalOverlay"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden relative">
            <!-- Modal Header -->
            <div class="bg-[#C41E3A] px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-eye"></i>
                    <span>Recipient Details</span>
                </h3>
                <button id="closeViewModal" class="text-white/80 hover:text-white transition-colors @if(auth()->user()->isBcpcAdmin()) hidden @endif">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6 space-y-4" id="viewRecipientContent">
            </div>
            
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="button" id="closeViewModalBtn" class="portal-modal-close px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('recipientModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const modalTitle = document.getElementById('modalTitle');
    const recipientForm = document.getElementById('recipientForm');
    const recipientId = document.getElementById('recipientId');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('recipientsTableBody');
    const mobileContainer = document.getElementById('recipientsCardsMobile');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Pagination variables
    const itemsPerPage = {{ auth()->user()->isDepartmentAdmin() ? 5 : 10 }};
    let currentPage = 1;
    let allRecipients = [];
    let filteredRecipients = [];
    
    // Load recipients from Blade
    const rawRecipients = @json($recipients);
    const bcpcStatusControl = @json(auth()->user()->isBcpcAdmin());
    const vawcStatusControl = @json(auth()->user()->isVawcAdmin());
    allRecipients = rawRecipients.map(r => ({
        id: r.id,
        name: r.name,
        email: r.email,
        contact_number: r.contact_number,
        role: r.role,
        active: r.active,
        has_active_referrals: r.has_active_referrals === true || r.has_active_referrals === 1,
        created_at: r.created_at ? new Date(r.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'
    }));
    
    filteredRecipients = [...allRecipients];
    renderRecipients();
    
    // Render recipients
    function renderRecipients() {
        const totalPages = Math.ceil(filteredRecipients.length / itemsPerPage);
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = filteredRecipients.slice(start, end);
        
        // Desktop table
        if (pageData.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No recipients found</p>
                            <p class="text-sm text-gray-400 mt-1">Add recipients to receive case referral emails.</p>
                        </div>
                    </td>
                </tr>`;
        } else {
            tableBody.innerHTML = pageData.map(r => `
                <tr class="{{ $matchReportTable ? 'hover:bg-purple-50/50 transition recipient-row' : 'hover:bg-gray-50 transition-colors recipient-row' }}" data-id="${r.id}">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">${escapeHtml(r.name)}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${escapeHtml(r.email)}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${r.contact_number || '—'}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">${r.role || 'N/A'}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${r.active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            ${r.active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">${r.created_at}</td>
                    <td class="recipient-actions-cell px-4 py-4 text-center align-middle">
                        <div class="recipient-actions">
                            <button type="button" class="{{ $matchReportTable ? 'view-btn inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition shadow-sm' : 'view-btn p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors' }}"
                                title="View Details" data-id="${r.id}" data-name="${escapeHtml(r.name)}" 
                                data-email="${escapeHtml(r.email)}" data-contact="${escapeHtml(r.contact_number || '')}" 
                                data-role="${escapeHtml(r.role || '')}" data-active="${r.active ? '1' : '0'}"
                                data-created="${escapeHtml(r.created_at || '')}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="{{ $matchReportTable ? 'edit-btn inline-flex items-center justify-center gap-1.5 bg-purple-700 hover:bg-purple-800 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition' : 'edit-btn p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors' }}"
                                title="Edit" data-id="${r.id}" data-name="${escapeHtml(r.name)}" 
                                data-email="${escapeHtml(r.email)}" data-contact="${r.contact_number || ''}" 
                                data-role="${r.role || ''}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="toggle-btn inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-white transition shadow-sm ${bcpcStatusControl ? (r.active ? 'bcpc-referral-disable-control' : 'bcpc-referral-enable-control') : (vawcStatusControl ? (r.active ? 'swd-disable-control' : 'swd-enable-control') : (r.active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'))}"
                                title="${r.active ? 'Deactivate' : 'Activate'}" data-id="${r.id}" data-active="${r.active ? '1' : '0'}">
                                <i class="fas fa-toggle-${r.active ? 'on' : 'off'}"></i>
                            </button>
                            <button type="button" class="{{ $matchReportTable ? 'delete-btn inline-flex items-center justify-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-medium transition' : 'delete-btn p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors' }}"
                                title="{{ auth()->user()->isDepartmentAdmin() ? 'Archive' : 'Delete' }}" data-id="${r.id}" data-name="${escapeHtml(r.name)}">
                                <i class="fas fa-{{ auth()->user()->isDepartmentAdmin() ? 'archive' : 'trash' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        // Mobile cards
        if (pageData.length === 0) {
            mobileContainer.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No recipients found</p>
                    <p class="text-sm text-gray-400 mt-1">Add recipients to receive case referral emails.</p>
                </div>`;
        } else {
            mobileContainer.innerHTML = pageData.map(r => `
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 recipient-row" data-id="${r.id}">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">${escapeHtml(r.name)}</p>
                            <p class="text-xs text-gray-500">${escapeHtml(r.email)}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${r.active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            ${r.active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                        ${r.contact_number ? `<span><i class="fas fa-phone mr-1"></i>${r.contact_number}</span>` : ''}
                        ${r.role ? `<span><i class="fas fa-user-tag mr-1"></i>${r.role}</span>` : ''}
                        <span><i class="fas fa-calendar mr-1"></i>${r.created_at}</span>
                    </div>
                    
                    <div class="recipient-card-actions flex gap-2 pt-3 border-t border-gray-100">
                        <button type="button" class="view-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                            data-id="${r.id}" data-name="${escapeHtml(r.name)}" 
                            data-email="${escapeHtml(r.email)}" data-contact="${escapeHtml(r.contact_number || '')}" 
                            data-role="${escapeHtml(r.role || '')}" data-active="${r.active ? '1' : '0'}"
                            data-created="${escapeHtml(r.created_at || '')}">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                        <button type="button" class="edit-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
                            data-id="${r.id}" data-name="${escapeHtml(r.name)}" 
                            data-email="${escapeHtml(r.email)}" data-contact="${r.contact_number || ''}" 
                            data-role="${r.role || ''}">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                        <button type="button" class="toggle-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium ${bcpcStatusControl ? ('text-white ' + (r.active ? 'bcpc-referral-disable-control' : 'bcpc-referral-enable-control')) : (vawcStatusControl ? ('text-white ' + (r.active ? 'swd-disable-control' : 'swd-enable-control')) : (r.active ? 'text-red-700 bg-red-50 hover:bg-red-100' : 'text-green-700 bg-green-50 hover:bg-green-100'))} rounded-lg transition-colors"
                            data-id="${r.id}" data-active="${r.active ? '1' : '0'}">
                            <i class="fas fa-toggle-${r.active ? 'on' : 'off'}"></i>
                            ${r.active ? 'Deactivate' : 'Activate'}
                        </button>
                        <button type="button" class="delete-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
                            data-id="${r.id}" data-name="${escapeHtml(r.name)}">
                            <i class="fas fa-{{ auth()->user()->isDepartmentAdmin() ? 'archive' : 'trash' }}"></i>
                            {{ auth()->user()->isDepartmentAdmin() ? 'Archive' : 'Delete' }}
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        updatePagination(totalPages);
    }
    
    // Update pagination controls
    function updatePagination(totalPages) {
        const paginationInfo = document.getElementById('pagination-info');
        const mobilePaginationInfo = document.getElementById('mobile-pagination-info');
        const prevPageBtn = document.getElementById('prev-page-btn');
        const nextPageBtn = document.getElementById('next-page-btn');
        const mobilePrevBtn = document.getElementById('mobile-prev-btn');
        const mobileNextBtn = document.getElementById('mobile-next-btn');
        const pageNumbers = document.getElementById('page-numbers');
        
        const total = filteredRecipients.length;
        const start = total === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, total);
        
        if (paginationInfo) paginationInfo.textContent = `Showing ${start} - ${end} of ${total} recipients`;
        if (mobilePaginationInfo) mobilePaginationInfo.textContent = `Page ${currentPage} of ${Math.max(1, totalPages)}`;
        
        // Enable/disable buttons
        const canPrev = currentPage > 1;
        const canNext = currentPage < totalPages;
        
        if (prevPageBtn) prevPageBtn.disabled = !canPrev;
        if (nextPageBtn) nextPageBtn.disabled = !canNext;
        if (mobilePrevBtn) mobilePrevBtn.disabled = !canPrev;
        if (mobileNextBtn) mobileNextBtn.disabled = !canNext;
        
        // Page numbers
        if (pageNumbers) {
            let html = '';
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const isActive = i === currentPage;
                html += `
                    <button class="page-num inline-flex items-center justify-center w-9 h-9 ${isActive ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white border border-gray-300 hover:bg-gray-50 text-gray-700'} rounded-lg transition-colors text-sm"
                        data-page="${i}">${i}</button>
                `;
            }
            pageNumbers.innerHTML = html;
        }
    }
    
    // Helper function
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    
    // Pagination event listeners
    document.getElementById('prev-page-btn')?.addEventListener('click', () => {
        if (currentPage > 1) { currentPage--; renderRecipients(); }
    });
    
    document.getElementById('next-page-btn')?.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredRecipients.length / itemsPerPage);
        if (currentPage < totalPages) { currentPage++; renderRecipients(); }
    });
    
    document.getElementById('mobile-prev-btn')?.addEventListener('click', () => {
        if (currentPage > 1) { currentPage--; renderRecipients(); }
    });
    
    document.getElementById('mobile-next-btn')?.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredRecipients.length / itemsPerPage);
        if (currentPage < totalPages) { currentPage++; renderRecipients(); }
    });
    
    document.getElementById('page-numbers')?.addEventListener('click', (e) => {
        const btn = e.target.closest('.page-num');
        if (btn) {
            currentPage = parseInt(btn.dataset.page);
            renderRecipients();
        }
    });
    
    // Search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value.toLowerCase().trim();
            if (searchTerm === '') {
                filteredRecipients = [...allRecipients];
            } else {
                filteredRecipients = allRecipients.filter(r => 
                    r.name.toLowerCase().includes(searchTerm) ||
                    r.email.toLowerCase().includes(searchTerm) ||
                    (r.contact_number && r.contact_number.includes(searchTerm)) ||
                    (r.role && r.role.toLowerCase().includes(searchTerm))
                );
            }
            currentPage = 1;
            renderRecipients();
        }, 300);
    });
    
    // Open modal for add
    document.getElementById('addRecipientBtn').addEventListener('click', function() {
        modalTitle.textContent = 'Add New Recipient';
        recipientForm.reset();
        recipientId.value = '';
        document.getElementById('recipientRole').required = @json(auth()->user()->isDepartmentAdmin());
        openModal();
    });
    
    // View button click
    document.addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.view-btn');
        if (viewBtn) {
            e.preventDefault();
            e.stopPropagation();
            const name = viewBtn.dataset.name;
            const email = viewBtn.dataset.email;
            const contact = viewBtn.dataset.contact || 'None';
            const role = viewBtn.dataset.role || 'N/A';
            const isActive = viewBtn.dataset.active === '1';
            const created = viewBtn.dataset.created || 'N/A';

            document.getElementById('viewRecipientContent').innerHTML = `
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3 border border-gray-100">
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold block">Full Name</span>
                            <span class="text-sm font-bold text-gray-800">${escapeHtml(name)}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase font-semibold block">Email Address</span>
                            <span class="text-sm font-medium text-gray-800">${escapeHtml(email)}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-semibold block">Contact Number</span>
                                <span class="text-sm text-gray-700">${escapeHtml(contact)}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-semibold block">Role / Designation</span>
                                <span class="text-sm text-gray-700">${escapeHtml(role)}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-semibold block">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                    ${isActive ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-semibold block">Date Added</span>
                                <span class="text-sm text-gray-600">${escapeHtml(created)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('viewRecipientModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    });

    const closeViewModal = () => {
        const vModal = document.getElementById('viewRecipientModal');
        if (vModal) vModal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.getElementById('closeViewModal')?.addEventListener('click', closeViewModal);
    document.getElementById('closeViewModalBtn')?.addEventListener('click', closeViewModal);
    document.getElementById('viewModalOverlay')?.addEventListener('click', closeViewModal);

    // Edit button click
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            e.preventDefault();
            e.stopPropagation();
            modalTitle.textContent = 'Edit Recipient';
            recipientId.value = editBtn.dataset.id;
            document.getElementById('recipientName').value = editBtn.dataset.name || '';
            document.getElementById('recipientEmail').value = editBtn.dataset.email || '';
            document.getElementById('recipientContact').value = editBtn.dataset.contact || '';
            document.getElementById('recipientRole').value = editBtn.dataset.role || '';
            document.getElementById('recipientRole').required = false;
            openModal();
        }
    });
    
    // Toggle status
    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.toggle-btn');
        if (toggleBtn) {
            const id = toggleBtn.dataset.id;
            const isActive = toggleBtn.dataset.active === '1';
            const recipient = allRecipients.find(item => item.id === Number(id));
            const hasActiveReferrals = recipient?.has_active_referrals === true;
            
            Swal.fire({
                title: isActive ? 'Deactivate Recipient?' : 'Activate Recipient?',
                html: isActive && hasActiveReferrals
                    ? 'This recipient has active referrals. Are you sure you want to deactivate the account?'
                    : (isActive ? 'This recipient will no longer receive emails.' : 'This recipient will start receiving emails.'),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7c3aed',
                confirmButtonText: isActive ? 'Deactivate' : 'Activate'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/referral-info/${id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Updated!', data.message, 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Failed to update status', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Failed to update status', 'error');
                    });
                }
            });
        }
    });
    
    // Delete button click
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            const id = deleteBtn.dataset.id;
            const name = deleteBtn.dataset.name;
            const recipient = allRecipients.find(item => item.id === Number(id));
            const hasActiveReferrals = recipient?.has_active_referrals === true;
            
            Swal.fire({
                title: @json(auth()->user()->isDepartmentAdmin() ? 'Archive Referral Info?' : 'Delete Recipient?'),
                html: `<p>Are you sure you want to {{ auth()->user()->isDepartmentAdmin() ? 'archive' : 'delete' }} <strong>${name}</strong>?</p>${hasActiveReferrals ? '<p class="text-sm text-gray-500 mt-2">This recipient has active referral cases. Are you sure you want to archive this referral information?</p>' : '<p class="text-sm text-gray-500 mt-2">{{ auth()->user()->isDepartmentAdmin() ? 'The referral information will be archived.' : 'This action cannot be undone.' }}</p>'}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: @json(auth()->user()->isDepartmentAdmin() ? 'Yes, Archive' : 'Delete'),
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/referral-info/${id}`, {
                        method: 'DELETE',
                        @if(auth()->user()->isDepartmentAdmin())
                        body: JSON.stringify({confirm_linked_referrals: hasActiveReferrals}),
                        @endif
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            allRecipients = allRecipients.filter(recipient => recipient.id !== Number(id));
                            filteredRecipients = filteredRecipients.filter(recipient => recipient.id !== Number(id));
                            const totalPages = Math.max(1, Math.ceil(filteredRecipients.length / itemsPerPage));
                            currentPage = Math.min(currentPage, totalPages);
                            renderRecipients();
                            Swal.fire(@json(auth()->user()->isDepartmentAdmin() ? 'Archived!' : 'Deleted!'), data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message || @json(auth()->user()->isDepartmentAdmin() ? 'Failed to archive' : 'Failed to delete'), 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', @json(auth()->user()->isDepartmentAdmin() ? 'Failed to archive referral info' : 'Failed to delete recipient'), 'error');
                    });
                }
            });
        }
    });
    
    // Form submission
    recipientForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = recipientId.value;
        const url = id ? `/admin/referral-info/${id}` : '/admin/referral-info';
        const method = id ? 'PUT' : 'POST';
        
        const formData = {
            name: document.getElementById('recipientName').value,
            email: document.getElementById('recipientEmail').value,
            contact_number: document.getElementById('recipientContact').value || null,
            role: document.getElementById('recipientRole').value || null
        };

        if (!id && @json(auth()->user()->isDepartmentAdmin()) && !formData.role) {
            Swal.fire('Role is required', 'Please select a role before creating a recipient.', 'error');
            return;
        }

        const normalizedName = formData.name.trim().toLocaleLowerCase();
        const normalizedEmail = formData.email.trim().toLocaleLowerCase();
        const duplicate = allRecipients.find(recipient => Number(recipient.id) !== Number(id || 0)
            && (recipient.name.trim().toLocaleLowerCase() === normalizedName || recipient.email.trim().toLocaleLowerCase() === normalizedEmail));
        if (duplicate) {
            Swal.fire('Recipient already exists', duplicate.email.trim().toLocaleLowerCase() === normalizedEmail ? 'This email is already taken.' : 'This user already exists.', 'warning');
            return;
        }
        
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success!', data.message, 'success')
                    .then(() => window.location.reload());
            } else {
                const errors = data.errors ? Object.values(data.errors).flat().join('<br>') : 'Failed to save';
                Swal.fire('Error', errors, 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Failed to save recipient', 'error');
        });
    });
    
    // Modal controls
    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('recipientName').focus();
    }
    
    function hideModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    closeModal.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);
    modalOverlay.addEventListener('click', hideModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') hideModal();
    });
    
    // Phone number validation
    document.getElementById('recipientContact').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
    });
});
</script>
@endpush

@endsection
