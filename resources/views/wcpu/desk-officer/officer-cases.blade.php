@extends('layouts.desk-officer')

@section('title', 'Cases - iSERVE SILANG Desk Officer')

@php
    $defaultCaseType = strtolower(Auth::user()->defaultCaseDepartment());
    $isDeskOfficer = strtolower(trim((string) Auth::user()?->position)) === 'desk officer';
    $isMultiroleDeskOfficer = $isDeskOfficer && Auth::user()->isMultiCaseDepartment();
    $isSwdDeskOfficer = $isDeskOfficer && Auth::user()->defaultCaseDepartment() === 'VAWC';
@endphp

@push('modals')
<!-- Alpine modal (hidden by default). We'll toggle via Alpine `openModal` state. -->
<div x-data="{ open: false, content: '', caseNumber: '', caseId: '', printUrl: '', editUrl: '', referUrl: '', victimEmail: '', isOwner: false, canRefer: true }"
     x-cloak 
     @open-case-modal.window="open = true; content = $event.detail.content; caseNumber = $event.detail.caseNumber || ''; caseId = $event.detail.caseId || ''; printUrl = $event.detail.printUrl || '#'; editUrl = $event.detail.editUrl || '#'; referUrl = $event.detail.referUrl || '#'; victimEmail = $event.detail.victimEmail || ''; isOwner = $event.detail.isOwner || false; canRefer = $event.detail.canRefer ?? true;"
     id="caseModal">
  <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-black/50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-hidden flex flex-col relative" id="caseModalInner">
      <!-- Close button (top-right corner) -->
      <button @click="open = false" class="absolute top-3 right-3 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition">
        <i class="fas fa-times text-sm"></i>
      </button>

      <!-- Header with title and action buttons -->
      <div class="case-modal-top-header p-4 border-b pr-12">
        <div class="mb-2">
          <h5 class="text-lg font-semibold" x-text="'Case #' + caseNumber">Case Details</h5>
          <div class="case-modal-top-summary" x-html="new DOMParser().parseFromString(content || '', 'text/html').querySelector('[data-case-modal-summary]')?.innerHTML || ''"></div>
        </div>
        <div class="case-modal-header-actions flex items-center gap-2 flex-wrap {{ Auth::user()->position === 'Desk Officer' && (Auth::user()->defaultCaseDepartment() === 'BCPC' || Auth::user()->isMultiCaseDepartment()) ? 'w-full justify-end' : '' }}">
          @if(Auth::user()->position === 'Desk Officer' && (Auth::user()->defaultCaseDepartment() === 'BCPC' || Auth::user()->isMultiCaseDepartment()))
          <a :href="printUrl" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
            <i class="fas fa-print"></i><span>Print</span>
          </a>
          <a id="bcpc-refer-case-button" :href="canRefer ? referUrl : '#'" :aria-disabled="(!canRefer).toString()" class="inline-flex items-center gap-2 px-3 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-sm font-medium">
            <i class="fas fa-share-square"></i><span>Refer Case</span>
          </a>
          @else
          <a x-show="isOwner" :href="referUrl" class="btn btn-success btn-sm">
            <i class="fas fa-share-square me-1"></i> Refer Case
          </a>
          <a href="#" @click.prevent="window.previewCasePrint(printUrl, caseNumber)" class="btn btn-primary btn-sm">
            <i class="fas fa-print me-1"></i> Preview & Print
          </a>
          @endif
        </div>
      </div>
      
      <!-- Content area -->
      <div class="p-4 overflow-auto flex-1" x-html="content">
        <!-- content loaded dynamically via fetch when opening -->
        <div class="text-center py-8">
          <svg class="animate-spin h-8 w-8 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
          </svg>
          <p class="mt-3 text-sm text-gray-600">Loading case details...</p>
        </div>
      </div>
      

    </div>
  </div>
</div>

<!-- Refer Case Modal -->
<div x-data="{ 
       open: false, 
       caseId: '', 
       recipients: [], 
       selectedRecipients: [],
       loading: true,
       selectAll: false,
       async loadRecipients() {
         this.loading = true;
         try {
           const response = await fetch('{{ route('desk-officer.api.recipients') }}?t=' + Date.now());
           const data = await response.json();
           if (data.success) {
             this.recipients = data.recipients;
           }
         } catch (error) {
           console.error('Error loading recipients:', error);
         } finally {
           this.loading = false;
         }
       },
       toggleSelectAll() {
         if (this.selectAll) {
           this.selectedRecipients = this.recipients.map(r => r.email);
         } else {
           this.selectedRecipients = [];
         }
       }
     }" 
     x-cloak
     @open-refer-modal.window="open = true; caseId = $event.detail.caseId; loadRecipients();"
     @close-refer-modal.window="open = false; selectedRecipients = []; selectAll = false;"
     id="referModal">
  <div x-show="open" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto p-4 bg-black/50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
      <div class="mb-4">
        <h5 class="text-lg font-semibold"><i class="fas fa-share-square me-2"></i>Refer Case</h5>
      </div>
      <p class="text-sm mb-3">Select recipient(s) to send this case report:</p>
      
      <div class="bg-gray-50 border border-gray-200 rounded p-3 max-h-48 overflow-auto mb-3">
        <div class="mb-2" x-show="!loading && recipients.length > 0">
          <input type="checkbox" id="selectAllRecipients" class="me-2" x-model="selectAll" @change="toggleSelectAll()">
          <label for="selectAllRecipients" class="text-sm cursor-pointer">Select All</label>
        </div>
        <div class="recipient-list">
          <template x-if="loading">
            <p class="text-sm text-gray-500">Loading recipients...</p>
          </template>
          <template x-if="!loading && recipients.length === 0">
            <p class="text-sm text-gray-500">No recipients found</p>
          </template>
          <template x-if="!loading && recipients.length > 0">
            <div>
              <template x-for="recipient in recipients" :key="recipient.id">
                <div class="mb-2">
                  <input type="checkbox" :id="'recipient-' + recipient.id" :value="recipient.email" x-model="selectedRecipients" class="me-2">
                  <label :for="'recipient-' + recipient.id" class="text-sm cursor-pointer">
                    <span x-text="recipient.name"></span>
                    <small class="text-gray-500">
                      <span x-show="recipient.role" x-text="' - ' + recipient.role"></span>
                      <span x-show="recipient.email" x-text="' (' + recipient.email + ')'"></span>
                    </small>
                  </label>
                </div>
              </template>
            </div>
          </template>
        </div>
      </div>
      
      <small class="text-gray-600 text-xs">A PDF copy of the case report will be attached.</small>
      
      <div class="flex gap-2 mt-4">
        <button @click="open = false" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button @click="submitRefer(caseId, selectedRecipients)" :disabled="selectedRecipients.length === 0" class="px-4 py-2 rounded text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" style="background: linear-gradient(135deg,#7c3299,#6a1b9a);">
          <i class="fas fa-paper-plane me-1"></i> Send Now
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Send Copy Modal -->
<div x-data="{ open: false, caseId: '', emailAddress: '' }" 
     x-cloak
     @open-send-copy-modal.window="open = true; caseId = $event.detail.caseId; emailAddress = $event.detail.victimEmail || '';"
     @close-send-copy-modal.window="open = false; emailAddress = '';"
     id="sendCopyModal">
  <div x-show="open" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto p-4 bg-black/50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[calc(100vh-2rem)] max-h-[calc(100dvh-2rem)] overflow-y-auto overscroll-contain p-6">
      <div class="mb-4">
        <h5 class="text-lg font-semibold"><i class="fas fa-envelope me-2"></i>Send Case Copy</h5>
      </div>
      <p class="text-sm mb-3">A PDF copy of this case report will be sent to the victim's email address:</p>
      
      <div class="mb-4">
        <label for="emailInput" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
        <div class="w-full px-3 py-2 border border-gray-200 rounded-md bg-gray-50 text-gray-700 font-medium">
          <span x-text="emailAddress || 'No email address available'"></span>
        </div>
        <input type="hidden" x-model="emailAddress" required>
      </div>
      
      <small class="text-gray-600 text-xs block">A PDF snapshot of current case details will be attached.</small>
      
      <div class="flex gap-2 mt-4">
        <button @click="open = false" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">
          <i class="fas fa-times me-1"></i> Cancel
        </button>
        <button @click="submitSendCopy(caseId, emailAddress)" :disabled="!emailAddress || emailAddress.trim() === ''" class="px-4 py-2 rounded text-white hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed" style="background: linear-gradient(135deg,#7c3299,#6a1b9a);">
          <i class="fas fa-paper-plane me-1"></i> Send Now
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Case Modal -->
<div x-data="{ open: false, content: '', caseNumber: '' }" 
     x-cloak
     @open-edit-modal.window="open = true; content = $event.detail.content; caseNumber = $event.detail.caseNumber || '';"
     @close-edit-modal.window="open = false"
     id="editCaseModal">
  <div x-show="open" x-transition.opacity @click.self="window.requestCloseEditCaseModal ? window.requestCloseEditCaseModal() : (open = false)" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white {{ $isMultiroleDeskOfficer ? 'rounded-xl shadow-2xl' : 'rounded-lg shadow-xl' }} w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col">
      <div class="flex items-center justify-between p-4 border-b {{ $isMultiroleDeskOfficer ? 'border-gray-200 bg-gray-50' : '' }}">
        <h5 class="text-lg font-semibold" x-text="'Edit Case #' + caseNumber">Edit Case</h5>
        <button type="button" @click="window.requestCloseEditCaseModal ? window.requestCloseEditCaseModal() : (open = false)" class="text-gray-500 hover:text-gray-700 {{ $isMultiroleDeskOfficer ? 'p-2 hover:bg-gray-100 rounded-lg transition-colors' : '' }}">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="overflow-auto flex-1 p-4" x-html="content">
        <div class="text-center py-8">
          <svg class="animate-spin h-8 w-8 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
          </svg>
          <p class="mt-3 text-sm text-gray-600">Loading edit form...</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/desk-officer.css') }}">
    <style>
        /* Print-specific content will be controlled later via JS; keep original print CSS embedded for generated print window */
        [x-cloak] { display: none !important; }

        /* Mobile & Tablet views (<= 80rem / <= 1280px): Hide Cases List table */
        @media (max-width: 80rem) {
            #desk-officer-cases-content .cases-table-desktop,
            .cases-list-section .cases-table-desktop {
                display: none !important;
            }
        }

        /* Mobile view (< 48rem / < 768px): Show case cards using mobile spacing */
        @media (max-width: 47.999rem), (max-width: 767.98px) {
            #desk-officer-cases-content #mobile-case-cards,
            .cases-list-section #mobile-case-cards {
                display: flex !important;
                flex-direction: column !important;
                gap: 0.75rem !important;
                padding: 0.875rem !important;
            }
            #desk-officer-cases-content #mobile-case-cards .bcpc-case-referred-badge {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                line-height: 1.25 !important;
            }
        }

        /* Desktop view (> 80rem / > 1280px): Display Cases List table, hide case cards */
        @media (min-width: 80.0625rem) {
            #desk-officer-cases-content .cases-table-desktop,
            .cases-list-section .cases-table-desktop {
                display: block !important;
            }
            #desk-officer-cases-content #mobile-case-cards,
            .cases-list-section #mobile-case-cards {
                display: none !important;
            }
        }

        @if($isMultiroleDeskOfficer)
        @media (max-width: 47.9375rem) and (orientation: portrait) {
            #caseModal .case-modal-header-actions {
                justify-content: flex-end;
                margin-top: .5rem;
                margin-right: -.5rem;
            }
            #caseModal .case-modal-summary-labels {
                width: 100%;
                margin-left: auto;
                justify-content: flex-end;
            }
        }
        .multirole-cases-page .swd-desk-table th { padding: .75rem 1.5rem; text-transform: uppercase; letter-spacing: .05em; }
        .multirole-cases-page .swd-desk-table td { padding: 1rem 1.5rem; }
        /* Tablet Case Cards: Card presentation on tablet screens (768px–1280px / 48rem–80rem) */
        @media (min-width: 48rem) and (max-width: 80rem) {
            #desk-officer-cases-content #mobile-case-cards {
                display: flex !important;
                flex-direction: column !important;
                gap: 0.875rem !important;
                padding: 1rem !important;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card {
                margin: 0 !important;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 0.75rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card > .p-4 {
                padding: 1.125rem 1.25rem !important;
                display: flex;
                flex-direction: column;
                gap: 0.625rem;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-details {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 0.5rem 1.5rem !important;
                margin: 0.25rem 0 !important;
                padding: 0.625rem 0 !important;
                border-top: 1px solid #f3f4f6;
                border-bottom: 1px solid #f3f4f6;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-details > div {
                display: flex !important;
                align-items: center;
                gap: 0.375rem;
                font-size: 0.875rem !important;
                color: #4b5563 !important;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-actions {
                display: flex !important;
                justify-content: flex-end !important;
                align-items: center !important;
                gap: 0.5rem !important;
                margin-top: 0.25rem !important;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-actions > * {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 0.375rem !important;
                height: 2.25rem !important;
                min-width: 2.25rem !important;
                border-radius: 0.5rem !important;
                font-size: 0.8125rem !important;
                font-weight: 500 !important;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-actions > :is(a, button):not(form button) {
                padding: 0 0.875rem !important;
                width: auto !important;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-actions form {
                display: inline-flex !important;
                margin: 0 !important;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-actions form > button {
                width: 2.25rem !important;
                height: 2.25rem !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-action-label {
                display: inline !important;
            }
            #desk-officer-cases-content #mobile-case-cards .case-card-icon-action i {
                margin: 0 !important;
                font-size: 0.75rem !important;
            }
        }

        /* 4-column details on wider tablet landscape (1024px to 1280px) */
        @media (min-width: 64rem) and (max-width: 80rem) {
            #desk-officer-cases-content #mobile-case-cards .case-card-details {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }
        }
        @endif
        @if($isDeskOfficer)
        .swd-desk-cases-page .swd-desk-case-controls > * { flex: 0 0 auto; }
        .swd-desk-cases-page .swd-desk-case-controls > :is(button, a) { justify-content: center; }
        .swd-desk-cases-page:not(.multirole-cases-page) .swd-desk-table th,
        .swd-desk-cases-page:not(.multirole-cases-page) .swd-desk-table td { vertical-align: middle; }
        .swd-desk-cases-page:not(.multirole-cases-page) .swd-desk-table th { white-space: nowrap; padding: .75rem 1.5rem; text-transform: uppercase; letter-spacing: .05em; }
        .swd-desk-cases-page:not(.multirole-cases-page) .swd-desk-table td { padding: 1rem 1.5rem; }
        .swd-desk-cases-page .swd-desk-case-controls { margin-bottom: 1rem; }
        .swd-desk-cases-page .case-summary-grid { margin-bottom: 1rem; }
        .swd-desk-cases-page .case-summary-grid > div { min-height: 0; padding: .875rem 1rem; }
        .swd-desk-cases-page .cases-list-heading { padding: 1rem 1.25rem; }
        @if(in_array(Auth::user()->defaultCaseDepartment(), ['BCPC', 'SWD', 'VAWC'], true))
        .swd-desk-cases-page :is(.swd-desk-table, .case-card-actions) :is(.view-case, .edit-case:not(:disabled), button[title="Archive Case"]:not(:disabled)) {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #ffffff !important;
            transition: none !important;
            -webkit-tap-highlight-color: transparent;
        }
        .swd-desk-cases-page :is(.swd-desk-table, .case-card-actions) :is(.view-case, .edit-case:not(:disabled), button[title="Archive Case"]:not(:disabled)):is(:hover, :focus, :active) {
            background-color: #A01830 !important;
            border-color: #A01830 !important;
            color: #ffffff !important;
        }
        @endif
        /* ==========================================================================
           Container Queries and View Cases Page Content Base
           ========================================================================== */
        #mainContent {
            container-type: inline-size;
            container-name: cases-main;
        }

        #desk-officer-cases-content {
            container-type: inline-size;
            container-name: cases-content;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        /* Top header text truncation to prevent overflow */
        #desk-officer-dashboard-header .min-w-0 h2,
        #desk-officer-dashboard-header .min-w-0 p {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ==========================================================================
           Mobile and Tablet Navigation (matching Case Profiles reference)
           ========================================================================== */
        /* Hide redundant mobile top bar across mobile and tablet */
        @media (max-width: 80rem) {
            html body.portal-ui #mobileTopBar {
                display: none !important;
            }
        }

        /* Mobile View (< 48rem / < 768px): Single header with hamburger drawer */
        @media (max-width: 47.9375rem) {
            html body.portal-ui #mobileTopBar {
                display: none !important;
            }
            html body.portal-ui #mobileMenuToggle,
            html body.portal-ui #sidebarCloseBtn,
            html body.portal-ui #sidebarOverlay {
                display: none !important;
                pointer-events: none !important;
            }
            html body.portal-ui #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
            html body.portal-ui #desk-officer-dashboard-header {
                background: #C41E3A !important;
                border-bottom: 1px solid #99172E !important;
                position: sticky !important;
                top: 0 !important;
                z-index: 35 !important;
                width: 100% !important;
            }
            html body.portal-ui #desk-officer-dashboard-header > div {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                flex-wrap: nowrap !important;
                gap: 0.5rem !important;
                padding-left: max(.5rem, env(safe-area-inset-left)) !important;
                padding-right: max(1rem, env(safe-area-inset-right)) !important;
                min-width: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            html body.portal-ui #desk-officer-dashboard-header > div > div:first-child {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                align-items: center !important;
                gap: 0.5rem !important;
                min-width: 0 !important;
                flex: 1 1 auto !important;
            }
            html body.portal-ui :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
                position: relative !important;
                left: 0 !important;
                margin-left: 0 !important;
                transform: none !important;
                display: inline-flex !important;
                flex: 0 0 2.5rem !important;
                width: 2.5rem !important;
                height: 2.5rem !important;
                min-width: 2.5rem !important;
                min-height: 2.5rem !important;
                align-items: center !important;
                justify-content: center !important;
                border: 0 !important;
                border-radius: 0.375rem !important;
                background: transparent !important;
                box-shadow: none !important;
                color: #ffffff !important;
                padding: 0 !important;
                cursor: pointer;
            }
            html body.portal-ui :is(#dashboardMenuToggle, #pageMobileMenuToggle) > i {
                color: inherit !important;
            }
            html body.portal-ui #desk-officer-dashboard-header > div > div:first-child > div {
                width: auto !important;
                flex: 1 1 auto !important;
                min-width: 0 !important;
                text-align: left !important;
            }
            html body.portal-ui #desk-officer-dashboard-header h2 {
                color: #ffffff !important;
                font-size: clamp(0.875rem, 3.8vw, 1.125rem) !important;
                font-weight: 700 !important;
                line-height: 1.25 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                margin: 0 !important;
            }
            html body.portal-ui #desk-officer-dashboard-header p {
                display: block !important;
                color: rgba(255, 255, 255, 0.85) !important;
                font-size: clamp(0.6875rem, 2.8vw, 0.75rem) !important;
                line-height: 1.2 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                margin: 0 !important;
            }
            html body.portal-ui #desk-officer-dashboard-header > div > div:last-child {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
                flex: 0 0 auto !important;
                flex-wrap: nowrap !important;
            }
            html body.portal-ui #desk-officer-dashboard-header button[aria-label="Notifications"] {
                color: #ffffff !important;
                background-color: #A01830 !important;
                box-sizing: border-box !important;
                width: 2.5rem !important;
                min-width: 2.5rem !important;
                height: 2.5rem !important;
                min-height: 2.5rem !important;
                padding: 0 !important;
                border-radius: .625rem !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            html body.portal-ui #desk-officer-dashboard-header button[aria-label="Notifications"]:hover {
                background-color: rgba(255, 255, 255, 0.15) !important;
            }

            /* Mobile Sidebar Dropdown Drawer below header */
            html body.portal-ui #sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: auto !important;
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                max-height: calc(100vh - 60px) !important;
                max-height: calc(100dvh - 60px) !important;
                overflow-y: auto !important;
                z-index: 999 !important;
                border-radius: 0 0 14px 14px !important;
                box-shadow: 0 14px 28px rgba(0, 0, 0, .22) !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transform: translateY(-8px) !important;
                transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
                background: #C41E3A !important;
                background-image: none !important;
            }
            html body.portal-ui #sidebar.sidebar-open {
                opacity: 1 !important;
                visibility: visible !important;
                transform: translateY(0) !important;
            }
            html body.portal-ui #sidebar.sidebar-collapsed,
            html body.portal-ui #sidebar.sidebar-collapsed-init {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
            }
            html body.portal-ui #sidebar .sidebar-header { display: none !important; }
            html body.portal-ui #sidebar .sidebar-text { opacity: 1 !important; max-width: none !important; }
            html body.portal-ui #sidebar .sidebar-menu { flex: 0 1 auto; min-height: 0; padding: .5rem !important; overflow-y: auto !important; }
            html body.portal-ui #sidebar .sidebar-link { justify-content: flex-start !important; gap: .75rem !important; }
            html body.portal-ui #sidebar #sidebarCloseBtn { display: none !important; }
            html body.portal-ui #sidebarToggleBtn { display: none !important; }
            html body.portal-ui #sidebar nav :is(a, button),
            html body.portal-ui #sidebar .sidebar-link {
                background: #C41E3A !important;
                color: #ffffff !important;
            }
            html body.portal-ui #sidebar nav :is(a, button) :is(i, svg, span),
            html body.portal-ui #sidebar .sidebar-link :is(i, svg, span) {
                color: #ffffff !important;
            }
            html body.portal-ui #sidebar nav :is(a, button):hover,
            html body.portal-ui #sidebar .sidebar-link:hover {
                background: #99172E !important;
                color: #ffffff !important;
            }
            html body.portal-ui #sidebar .sidebar-link:is([class~="bg-purple-600"], [class~="bg-purple-700/70"]),
            html body.portal-ui #sidebar nav [x-show] > :is(a, button).bg-purple-600 {
                background: #99172E !important;
                color: #ffffff !important;
                box-shadow: inset 3px 0 0 #ffffff !important;
            }

            /* Mobile Content Padding */
            #desk-officer-cases-content {
                padding: 0.75rem !important;
                padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom))) !important;
            }
        }

        /* Screen widths < 640px: hide user profile avatar/name to keep header clean */
        @media (max-width: 40rem) {
            html body.portal-ui #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) div:has(> img[alt="User Profile"]) {
                display: none !important;
            }
            html body.portal-ui #mainContent > header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) button:has(> img[alt="User Profile"]) {
                display: none !important;
            }
        }

        /* Extra small devices (<= 340px) */
        @media (max-width: 21.25rem) {
            #desk-officer-cases-content {
                padding: 0.5rem !important;
            }
        }

        /* Tablet navigation (768px–1023px / 48rem–63.9375rem): collapsed sidebar rail on left, standard white header, no hamburger button */
        @media (min-width: 48rem) and (max-width: 63.9375rem) {
            html body.portal-ui #mobileTopBar { display: none !important; }
            html body.portal-ui #mobileMenuToggle { display: none !important; }
            html body.portal-ui #dashboardMenuToggle { display: none !important; }
            html body.portal-ui #pageMobileMenuToggle { display: none !important; }
            html body.portal-ui #sidebarCloseBtn { display: none !important; }
            html body.portal-ui #sidebarOverlay {
                display: none !important;
                pointer-events: none !important;
            }
            html body.portal-ui #mainContent > header.sticky,
            html body.portal-ui #desk-officer-dashboard-header {
                background: #ffffff !important;
                border-bottom: 1px solid #e5e7eb !important;
            }
            html body.portal-ui #mainContent > header > div > div:first-of-type :is(h1, h2, h3),
            html body.portal-ui #desk-officer-dashboard-header h2 {
                color: #1f2937 !important;
            }
            html body.portal-ui #mainContent > header > div > div:first-of-type p,
            html body.portal-ui #desk-officer-dashboard-header p {
                color: #6b7280 !important;
            }
            html body.portal-ui #sidebar {
                inset: 0 auto 0 0 !important;
                top: 0 !important;
                left: 0 !important;
                bottom: 0 !important;
                right: auto !important;
                height: 100vh !important;
                height: 100dvh !important;
                max-height: none !important;
                opacity: 1 !important;
                visibility: visible !important;
                transform: none !important;
                background: #C41E3A !important;
                border-radius: 0 !important;
                z-index: 40 !important;
                width: 4.5rem !important;
                min-width: 4.5rem !important;
                max-width: 4.5rem !important;
                transition: width 0.3s ease !important;
            }
            html body.portal-ui #sidebar .sidebar-header {
                display: flex !important;
                min-height: 5.5rem;
                padding-left: 0.75rem !important;
                padding-right: 0.5rem !important;
                justify-content: flex-start !important;
            }
            html body.portal-ui #sidebar .sidebar-header img {
                width: 3rem !important;
                height: 3rem !important;
                flex-shrink: 0 !important;
            }
            html body.portal-ui #sidebar .sidebar-header .sidebar-text {
                display: none !important;
            }
            html body.portal-ui #sidebar .sidebar-text {
                display: none !important;
            }
            html body.portal-ui #sidebar .sidebar-link {
                justify-content: center !important;
                padding: 0.75rem !important;
            }
            html body.portal-ui #sidebar .sidebar-link i {
                margin: 0 !important;
                font-size: 1.25rem !important;
            }
            html body.portal-ui #sidebarToggleBtn {
                display: none !important;
            }
            html body.portal-ui #sidebar ~ #mainContent {
                margin-left: 4.5rem !important;
                width: calc(100% - 4.5rem) !important;
            }
            #desk-officer-cases-content {
                padding: clamp(1rem, 2vw, 1.5rem) !important;
            }
        }

        /* ==========================================================================
           Summary Statistic Cards Responsiveness
           ========================================================================== */
        .case-summary-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 0.75rem !important;
            width: 100%;
            margin-bottom: 1.25rem !important;
        }

        .case-summary-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 0.875rem 1.125rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
            transition: box-shadow 0.15s ease, border-color 0.15s ease;
        }

        .case-summary-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07);
            border-color: #d1d5db;
        }

        .case-summary-dot {
            width: 0.75rem;
            height: 0.75rem;
            min-width: 0.75rem;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        .case-summary-label {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            overflow-wrap: normal !important;
            word-break: keep-all !important;
            line-height: 1.25;
            margin: 0;
        }

        .case-summary-value {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
            margin-top: 0.125rem;
            margin-bottom: 0;
        }

        /* Summary Grid on Mobile (< 640px) */
        @media (max-width: 39.999rem) {
            .case-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 0.625rem !important;
                margin-bottom: 1rem !important;
            }
            .case-summary-card {
                padding: 0.625rem 0.75rem !important;
                gap: 0.5rem !important;
            }
            .case-summary-label {
                font-size: 0.6875rem !important;
            }
            .case-summary-value {
                font-size: 1rem !important;
            }
        }

        /* Narrow content width (< 590px via Container Query, e.g. tablet portrait with open sidebar) */
        @container cases-content (max-width: 589.98px) {
            .case-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 0.625rem !important;
            }
            .case-summary-card {
                padding: 0.75rem 0.875rem !important;
                gap: 0.625rem !important;
            }
            .case-summary-label {
                font-size: 0.6875rem !important;
            }
            .case-summary-value {
                font-size: 1.0625rem !important;
            }
        }

        /* Explicit tablet portrait fallback with expanded sidebar */
        #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) ~ #mainContent .case-summary-grid,
        html:not(.bcpc-desk-sidebar-collapsed) #mainContent .case-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
        @media (min-width: 64rem) {
            #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) ~ #mainContent .case-summary-grid,
            html:not(.bcpc-desk-sidebar-collapsed) #mainContent .case-summary-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }
        }
        #sidebar.sidebar-collapsed ~ #mainContent .case-summary-grid,
        #sidebar.sidebar-collapsed-init ~ #mainContent .case-summary-grid,
        html.bcpc-desk-sidebar-collapsed #mainContent .case-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        }

        /* ==========================================================================
           Search and Action Controls Responsiveness
           ========================================================================== */
        .case-controls-section {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
            margin-bottom: 1.25rem;
        }

        .case-search-wrapper {
            width: 100%;
            min-width: 0;
        }

        .case-actions-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
        }

        .case-department-switcher {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .case-buttons-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .case-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 2.5rem; /* 40px */
            padding: 0 0.875rem;
            border-radius: 0.625rem;
            font-size: 0.8125rem;
            font-weight: 600;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.15s ease;
            gap: 0.375rem;
        }

        /* Mobile Controls Layout (< 640px) */
        @media (max-width: 39.999rem) {
            .case-controls-section {
                gap: 0.625rem;
                margin-bottom: 1rem;
            }
            .case-actions-toolbar {
                display: flex !important;
                flex-direction: column !important;
                gap: 0.5rem !important;
                width: 100% !important;
            }
            .case-department-switcher {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                width: 100% !important;
                gap: 0.5rem !important;
            }
            .case-department-switcher > a {
                width: 100% !important;
                min-width: 0 !important;
                padding: 0 0.5rem !important;
                font-size: 0.75rem !important;
                height: 2.5rem !important;
            }
            .case-buttons-group {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                width: 100% !important;
                gap: 0.5rem !important;
            }
            .case-filter-wrapper {
                width: 100% !important;
            }
            .case-filter-btn,
            .case-print-btn {
                width: 100% !important;
                min-width: 0 !important;
                padding: 0 0.5rem !important;
                font-size: 0.75rem !important;
                height: 2.5rem !important;
            }
        }

        /* Tablet portrait & landscape controls layout */
        @media (min-width: 40rem) and (max-width: 63.999rem) {
            .case-controls-section {
                flex-direction: column;
                gap: 0.75rem;
            }
            .case-search-wrapper {
                width: 100%;
            }
            .case-actions-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                gap: 0.5rem;
            }
        }

        /* Desktop layout (>= 64rem / 1024px) */
        @media (min-width: 64rem) {
            .case-controls-section {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
            }
            .case-search-wrapper {
                max-width: 22rem;
                flex: 1 1 22rem;
            }
            .case-actions-toolbar {
                width: auto;
                justify-content: flex-end;
            }
        }

        /* Retain Case Cards actions and list heading on tablet/mobile */
        .swd-desk-cases-page .cases-list-heading { padding: .875rem 1rem; }
        .swd-desk-cases-page #mobile-case-cards .case-card { margin-top: .75rem; }
        .swd-desk-cases-page #mobile-case-cards .case-card-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(5rem, 1fr));
            gap: .5rem;
        }
        .swd-desk-cases-page #mobile-case-cards .case-card-actions > * { min-width: 0; }
        @endif
        
        /* Modal content styles */
        #caseModal .case-details dt { 
            font-weight: 600; 
            margin-top: 0.75rem; 
            margin-bottom: 0.25rem;
            color: #4b5563;
        }
        #caseModal .case-details dd { 
            margin-bottom: 0.5rem; 
            margin-left: 0;
            color: #1f2937;
        }
        #caseModal .case-details .card {
            border: 1px solid #e5e7eb;
        }
        #caseModal .case-details .card-header {
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
        }
        #caseModal .case-details .card-body {
            padding: 1rem;
        }

        /* Keep every case-card action the same size on mobile. */
        @media (max-width: 767px) {
            #desk-officer-cases-content #mobile-case-cards .case-card {
                min-width: 0;
                margin: 0 !important;
            }
            #mobile-case-cards .case-card-actions {
                display: grid !important;
                grid-template-columns: repeat(auto-fit, minmax(3rem, 1fr));
                gap: .5rem !important;
            }

            #mobile-case-cards .case-card-actions > * {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 0;
                width: 100%;
                height: 3rem;
                min-height: 3rem;
            }

            #mobile-case-cards .case-card-actions > form > button {
                width: 100% !important;
                height: 100% !important;
                padding: 0;
            }

            #mobile-case-cards .case-card-actions > form.inline-flex {
                display: flex !important;
                width: 100% !important;
            }

            #mobile-case-cards .case-card-icon-action { gap: 0 !important; }
            #mobile-case-cards .case-card-icon-action .case-card-action-label { display: none !important; }
            #mobile-case-cards .case-card-icon-action i { margin: 0 !important; }
        }
        
        /* Edit modal form styles */
        #editCaseModal .case-form-container {
            max-width: 100%;
        }
        #editCaseModal .form-card {
            box-shadow: none;
            border: none;
        }
        #editCaseModal .required-field {
            color: #dc3545;
        }
        
        /* Mobile Modal Responsive Styles */
        @media (max-width: 576px) {
            /* Case modal: don't fill entire screen */
            #caseModalInner {
                margin: 0.75rem !important;
                max-height: 85vh !important;
                border-radius: 16px !important;
                max-width: calc(100vw - 1.5rem) !important;
            }
            
            #editCaseModal .bg-white,
            #referModal .bg-white {
                margin: 0.75rem !important;
                max-height: 85vh !important;
                border-radius: 16px !important;
            }
            
            /* Info rows stack vertically on mobile */
            #caseModal .info-row {
                flex-direction: column !important;
                gap: 0.25rem !important;
            }
            
            #caseModal .info-label {
                min-width: unset !important;
                font-size: 0.8rem !important;
            }
            
            #caseModal .info-value {
                font-size: 0.9rem !important;
            }
            
            /* Section headings */
            #caseModal .section-heading,
            #caseModal .mb-0[style*="font-weight:700"] {
                font-size: 0.9rem !important;
            }
            
            /* Modal header padding */
            #caseModal .p-4.border-b {
                padding: 0.75rem 1rem !important;
                padding-right: 2.5rem !important;
            }
            
            /* Modal body padding */
            #caseModal .p-4.overflow-auto,
            #editCaseModal .p-4.overflow-auto {
                padding: 0.75rem !important;
            }
            
            /* Buttons */
            #caseModal .btn,
            #editCaseModal .btn {
                font-size: 0.8rem !important;
                padding: 0.4rem 0.75rem !important;
            }
            
            /* Bootstrap dl/dt/dd styling for desk-officer modal */
            #caseModal .case-details dl {
                margin: 0;
            }
            
            #caseModal .case-details dt {
                font-size: 0.8rem !important;
                margin-top: 0.5rem !important;
            }
            
            #caseModal .case-details dd {
                font-size: 0.9rem !important;
                margin-bottom: 0.25rem !important;
            }
            
            /* Bootstrap columns - stack on mobile */
            #caseModal .row > .col-md-6 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
            
            #caseModal .card {
                margin-bottom: 0.75rem !important;
            }
            
            #caseModal .card-header {
                padding: 0.5rem 0.75rem !important;
            }
            
            #caseModal .card-header h5 {
                font-size: 0.9rem !important;
            }
            
            #caseModal .card-body {
                padding: 0.75rem !important;
            }
        }
        
        @media (max-width: 375px) {
            #caseModal .text-lg.font-semibold,
            #editCaseModal .text-lg.font-semibold {
                font-size: 0.95rem !important;
            }
            
            #caseModal .btn,
            #editCaseModal .btn {
                font-size: 0.75rem !important;
                padding: 0.35rem 0.5rem !important;
            }
            
            #caseModal .case-details dt {
                font-size: 0.75rem !important;
            }
            
            #caseModal .case-details dd {
                font-size: 0.85rem !important;
            }
        }
    </style>
@endpush

@section('content')
<script>document.body.classList.add('view-cases-desk-mobile-nav');</script>
@if($isDeskOfficer)
@php
    $currentCaseType = strtoupper((string) request('type', (Auth::user()?->defaultCaseDepartment() ?: 'BCPC')));
    $currentDept = in_array($currentCaseType, ['VAWC', 'SWD'], true) ? 'SWD' : 'BCPC';
@endphp
@include('components.desk-officer.bcpc-dashboard-header', [
    'title' => 'Welcome, ' . $currentDept . ' SILANG',
    'subtitle' => $currentDept . ' Desk Officer - Silang, Cavite'
])
@endif
<!-- Print Content (hidden on screen, visible on print popup) -->
<div class="print-content hidden">
    <div class="print-header">
        <h1>{{ Auth::user()->canAccessCaseDepartment('BCPC') ? 'BCPC Barangay Case Management' : 'Women and Children Protection Unit' }}</h1>
        <h2>{{ $barangayName }} Barangay Cases Report</h2>
        <div class="print-info">
            Generated on: {{ now()->format('F j, Y \a\t g:i A') }} ({{ now()->format('T') }})<br>
            Total Cases: {{ $stats['total_cases'] ?? 0 }}
        </div>
    </div>

    <div class="print-summary-section">
        <div class="print-summary-title">{{ $barangayName }} Cases Summary</div>
        <div class="print-summary-stats">
            <div class="print-summary-stat">
                <span class="print-number">{{ $stats['total_cases'] ?? 0 }}</span>
                <div class="print-label">Total Cases</div>
            </div>
            <div class="print-summary-stat">
                <span class="print-number">{{ $stats['pending_cases'] ?? 0 }}</span>
                <div class="print-label">Pending Cases</div>
            </div>
            <div class="print-summary-stat">
                <span class="print-number">{{ $stats['resolved_cases'] ?? 0 }}</span>
                <div class="print-label">Case Filed</div>
            </div>
        </div>
    </div>

    @if($cases->count() > 0)
        <table class="print-cases-table">
            <thead>
                <tr>
                    <th style="width: 16%;">Case Number</th>
                    <th style="width: 16%;">Date Reported</th>
                    <th style="width: 24%;">Victim Name</th>
                    <th style="width: 20%;">Type of Abuse</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 12%;">Date Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cases as $case)
                    <tr>
                        <td class="print-case-number">{{ $case->case_number ?? 'N/A' }}</td>
                        <td>{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ censorLastName($case->victim_name) }}</td>
                        <td>{{ ucfirst($case->abuse_type ?? 'N/A') }}</td>
                        <td class="print-status">{{ $case->status ?? 'Pending' }}</td>
                        <td>{{ $case->created_at ? $case->created_at->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="print-no-cases">
            <h3>No Cases Found</h3>
            <p>There are no cases recorded for {{ $barangayName }} barangay at this time.</p>
        </div>
    @endif

    <div class="print-footer">
        {{ $barangayName }} Barangay Cases Report | {{ auth()->user()?->defaultCaseDepartment() === 'BCPC' ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System
    </div>
</div>

<div id="desk-officer-cases-content" class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 {{ $isDeskOfficer ? 'space-y-0 swd-desk-cases-page' : '' }} {{ $isMultiroleDeskOfficer ? 'multirole-cases-page' : '' }}">
<!-- Page Header (Fallback for non-desk-officer roles) -->
@unless($isDeskOfficer)
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="w-full sm:w-auto text-left">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $isSwdDeskOfficer ? 'View Cases' : (request()->filled('type') ? (strtolower(request('type')) === 'vawc' ? 'SWD' : strtoupper(request('type'))) : (Auth::user()->isMultiCaseDepartment() ? 'BCPC and SWD' : strtoupper(Auth::user()->defaultCaseDepartment()))) . ' Cases Management' }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $isSwdDeskOfficer ? 'View and manage cases by barangay' : 'Viewing cases for ' . $barangayName }}</p>
    </div>
</div>
@endunless

<!-- Case Summary Statistics Cards -->
<div class="case-summary-grid">
    <div class="case-summary-card">
        <span class="case-summary-dot bg-amber-400"></span>
        <div class="min-w-0 flex-1">
            <p class="case-summary-label" title="Case Closed">Case Closed</p>
            <p class="case-summary-value">{{ $stats['case_closed'] ?? 0 }}</p>
        </div>
    </div>
    <div class="case-summary-card">
        <span class="case-summary-dot bg-orange-500"></span>
        <div class="min-w-0 flex-1">
            <p class="case-summary-label" title="Case Referred">Case Referred</p>
            <p class="case-summary-value">{{ $stats['case_referred'] ?? 0 }}</p>
        </div>
    </div>
    <div class="case-summary-card">
        <span class="case-summary-dot bg-green-500"></span>
        <div class="min-w-0 flex-1">
            <p class="case-summary-label" title="Case Filed">Case Filed</p>
            <p class="case-summary-value">{{ $stats['case_filed'] ?? 0 }}</p>
        </div>
    </div>
    <div class="case-summary-card">
        <span class="case-summary-dot bg-[#C41E3A]"></span>
        <div class="min-w-0 flex-1">
            <p class="case-summary-label" title="Total Cases">Total Cases</p>
            <p class="case-summary-value">{{ $stats['total_cases'] ?? 0 }}</p>
        </div>
    </div>
</div>

<!-- Controls Section: Search and Action Toolbar -->
<div class="case-controls-section">
    <!-- Search Bar -->
    <div class="case-search-wrapper">
        <div class="relative w-full">
            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input id="case-search" type="text" placeholder="Search cases..." class="w-full h-10 pl-10 pr-4 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#C41E3A]/20 focus:border-[#C41E3A] transition">
        </div>
    </div>

    <!-- Actions Toolbar: Department Switcher + Filter + Print (+ New Case) -->
    <div class="case-actions-toolbar swd-desk-case-controls">
        @if(Auth::user()->isMultiCaseDepartment())
        <div class="case-department-switcher">
            <a href="{{ route('desk-officer.cases.index', ['type' => 'bcpc']) }}" class="case-action-btn case-dept-btn {{ strtolower(request('type', 'bcpc')) === 'bcpc' ? 'bg-purple-700 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-child"></i> <span>BCPC Cases</span>
            </a>
            <a href="{{ route('desk-officer.cases.index', ['type' => 'vawc']) }}" class="case-action-btn case-dept-btn {{ strtolower(request('type', '')) === 'vawc' ? 'bg-purple-700 text-white shadow-purple-200' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-shield-alt"></i> <span>SWD Cases</span>
            </a>
        </div>
        @endif

        <div class="case-buttons-group">
            <div x-data="{ open: false }" class="relative case-filter-wrapper">
                <button @click="open = !open" type="button" class="case-action-btn case-filter-btn bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-filter"></i> <span>Filter</span>
                    <svg :class="{ 'rotate-180': open }" class="h-4 w-4 transition-transform ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-cloak x-show="open" @click.away="open = false" x-transition class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-56 sm:w-64 max-w-[calc(100vw-2rem)] bg-white border border-gray-200 rounded-xl shadow-xl z-40" style="display: none; max-height: 320px; overflow-y: auto;">
                    <div class="p-4 space-y-3">
                        <div>
                            <label for="status-filter" class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                            <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-200 focus:border-purple-600">
                                <option value="">All Status</option>
                                <option value="Case Filed" {{ ($activeFilters['status'] ?? '') === 'Case Filed' ? 'selected' : '' }}>Case Filed</option>
                                <option value="Case Referred" {{ ($activeFilters['status'] ?? '') === 'Case Referred' ? 'selected' : '' }}>Case Referred</option>
                                <option value="Case closed" {{ ($activeFilters['status'] ?? '') === 'Case closed' ? 'selected' : '' }}>Case Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Date Range</label>
                            <input id="date-from" type="date" value="{{ $activeFilters['dateFrom'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2">
                            <input id="date-to" type="date" value="{{ $activeFilters['dateTo'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" id="reset-filters" class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">Clear</button>
                            <button type="button" id="apply-filters" class="px-3 py-1.5 text-xs font-medium bg-purple-700 text-white hover:bg-purple-800 rounded-lg shadow-sm transition">Apply</button>
                        </div>
                    </div>
                </div>
            </div>

            <button onclick="printAllCases()" type="button" class="case-action-btn case-print-btn {{ $isDeskOfficer ? 'bg-[#C41E3A] hover:bg-[#A01830]' : 'bg-green-600 hover:bg-green-700' }} text-white shadow-sm">
                <i class="fas fa-print"></i> <span>{{ $isDeskOfficer ? 'Print All' : 'Print All Cases' }}</span>
            </button>

            @unless(Auth::user()->isMultiCaseDepartment())
            <a href="{{ route('desk-officer.cases.create', ['type' => $defaultCaseType]) }}" class="case-action-btn bg-purple-700 text-white hover:bg-purple-800 shadow-sm">
                <i class="fas fa-plus"></i> <span>New Case</span>
            </a>
            @endunless
        </div>
    </div>
</div>

<div class="cases-list-section bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="cases-list-heading px-5 py-4 bg-[#C41E3A] text-white">
        <h3 class="font-semibold flex items-center gap-2"><i class="fas fa-folder-open"></i> Cases List ({{ $cases->total() }} total)</h3>
    </div>
<!-- Cases Table (Desktop) -->
<div class="cases-table-desktop hidden xl:block overflow-x-auto">
    <table class="min-w-full table-fixed min-w-[1100px] swd-desk-table">
        <thead class="bg-[#C41E3A] text-white">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-white">Case #</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-white">Date Reported</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-white">Victim Name</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-white">Type of Case</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-white">Status</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-white">Case Priority</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-white">Last Updated</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-white">Created By</th>
                <th class="text-center px-4 py-3 text-xs font-semibold text-white no-print">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cases as $case)
            <tr class="border-t hover:bg-gray-50 case-row">
                <td class="px-4 py-3 font-semibold"><a href="{{ route('desk-officer.cases.show', ['hash' => encrypt($case->id)]) }}" class="text-inherit hover:text-[#C41E3A] hover:underline">#{{ $case->case_number }}</a></td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $case->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">{{ censorLastName($case->victim_name) }}</td>
                <td class="px-4 py-3">{{ $case->abuse_type }}</td>
                <td class="px-4 py-3 text-center">
                    @php
                                        $hex = \App\Http\Controllers\Wcpu\DeskOfficerController::getStatusColor($case->status);
                        $hex = preg_match('/^[0-9a-fA-F]{6}$/', $hex) ? $hex : '6c757d';
                        $bgColor = '#' . $hex;
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        $luminance = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
                        $textColor = $luminance > 186 ? '#111' : '#fff';
                    @endphp
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                        {{ $case->status === 'Resolved' ? 'Case Filed' : (($case->status ?? '') === 'Case closed' ? 'Case Closed' : $case->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    @php $priorityColors = ['Emergency' => ['#dc2626', '#fff'], 'Urgent' => ['#f59e0b', '#111'], 'Non-urgent' => ['#16a34a', '#fff']]; $priorityStyle = $priorityColors[$case->priority] ?? ['#6b7280', '#fff']; @endphp
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded" style="background-color: {{ $priorityStyle[0] }}; color: {{ $priorityStyle[1] }};">{{ $case->priority ?? 'N/A' }}</span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600 text-center">
                    <span class="block">{{ $case->updated_at->format('M d, Y') }}</span>
                    <span class="block">{{ $case->updated_at->format('g:i A') }}</span>
                </td>
                <td class="px-4 py-3 text-sm">
                    @if($case->created_by == ($currentUserId ?? Auth::id()))
                        <span class="inline-flex items-center gap-1 text-purple-700 font-medium">
                            <i class="fas fa-user-circle text-xs"></i> You
                        </span>
                    @else
                        <span class="text-gray-500">
                            {{ $case->createdBy ? $case->createdBy->first_name . ' ' . substr($case->createdBy->last_name ?? '', 0, 1) . '.' : 'Unknown' }}
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 no-print {{ Auth::user()->defaultCaseDepartment() === 'BCPC' ? 'text-center' : '' }}">
                    <div class="inline-flex flex-wrap gap-2 {{ Auth::user()->defaultCaseDepartment() === 'BCPC' ? 'justify-center' : '' }}">
                        @php $editDisabled = $case->status !== 'Case Filed'; @endphp
                        @if(Auth::user()->position === 'Desk Officer' && Auth::user()->defaultCaseDepartment() === 'BCPC')
                        <button type="button" class="view-case inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#C41E3A] text-white hover:bg-[#A01830] transition-colors"
                            data-ajax-url="{{ route('desk-officer.cases.show', ['hash' => encrypt($case->id)]) }}"
                            data-case-number="{{ $case->case_number }}"
                            data-case-id="{{ $case->id }}"
                            data-case-status="{{ $case->status }}"
                            title="View Case">
                            <i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View</span>
                        </button>
                        @endif
                        @if($case->created_by == ($currentUserId ?? Auth::id()) || (Auth::user()->position === 'Desk Officer' && Auth::user()->defaultCaseDepartment() === 'BCPC'))
                        @if($editDisabled)
                        <span class="inline-flex h-9 w-9 flex-none items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm cursor-not-allowed" title="Only Case Filed cases can be edited" aria-disabled="true">
                            <i class="fas fa-edit"></i>
                        </span>
                        @else
                        <a href="{{ route('desk-officer.cases.edit', ['hash' => encrypt($case->id)]) }}"
                           class="edit-case inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-400 text-sm text-gray-600 hover:bg-gray-50 transition-colors"
                           data-ajax-url="{{ route('desk-officer.cases.edit', ['hash' => encrypt($case->id)]) }}"
                           data-case-number="{{ $case->case_number }}"
                           title="Edit Case">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                        @include('components.case-archive-button', ['archiveUrl' => route('desk-officer.cases.destroy', ['hash' => encrypt($case->id), 'type' => request('type', strtolower($case->type ?? 'bcpc'))])])
                        @else
                        @if(Auth::user()->defaultCaseDepartment() === 'BCPC')
                        <span class="inline-flex h-9 w-9 flex-none items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm cursor-not-allowed" title="Only the creator can edit this case" aria-disabled="true">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-800 cursor-not-allowed" title="Only the creator can archive this case" aria-disabled="true">
                            <i class="fas fa-archive"></i>
                        </span>
                        @else
                        <span class="inline-flex items-center justify-center px-3 py-1.5 text-xs text-gray-400" title="Only the creator can edit this case">
                            <i class="fas fa-lock"></i>
                        </span>
                        @endif
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center px-4 py-8 text-gray-500">No cases found</td>
            </tr>
            @endforelse
            <tr id="desktop-search-empty" class="hidden">
                <td colspan="8" class="text-center px-4 py-8 text-gray-500">No results found</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Cases Cards (Mobile & Tablet) -->
<div class="xl:hidden space-y-3" id="mobile-case-cards">
    @forelse($cases as $case)
    <div class="case-card bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4">
            {{-- Header: Case # + Status --}}
            <div class="flex items-center justify-between mb-2">
                <a href="{{ route('desk-officer.cases.show', ['hash' => encrypt($case->id)]) }}" class="text-sm font-bold text-purple-800 hover:text-[#C41E3A] hover:underline">#{{ $case->case_number }}</a>
                @php
                                        $hex = \App\Http\Controllers\Wcpu\DeskOfficerController::getStatusColor($case->status);
                    $hex = preg_match('/^[0-9a-fA-F]{6}$/', $hex) ? $hex : '6c757d';
                    $bgColor = '#' . $hex;
                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                    $luminance = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
                    $textColor = $luminance > 186 ? '#111' : '#fff';
                    $isBcpcView = strtolower(request('type', $defaultCaseType)) === 'bcpc';
                    $isCaseReferredBadge = $isBcpcView && trim((string)$case->status) === 'Case Referred';
                @endphp
                <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $isCaseReferredBadge ? 'bcpc-case-referred-badge' : '' }}" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                    {{ $case->status === 'Resolved' ? 'Case Filed' : $case->status }}
                </span>
            </div>

            {{-- Victim Name --}}
            <h3 class="font-semibold text-gray-800 text-base mb-1">{{ censorLastName($case->victim_name) }}</h3>

            {{-- Details grid --}}
            <div class="case-card-details grid grid-cols-2 gap-x-4 gap-y-1 text-sm text-gray-500 mb-3">
                <div><i class="fas fa-gavel text-xs mr-1 text-purple-400"></i>{{ $case->abuse_type }}</div>
                <div><i class="fas fa-calendar text-xs mr-1 text-purple-400"></i>{{ $case->created_at->format('M d, Y') }}</div>
                <div>
                    <i class="fas fa-user text-xs mr-1 text-purple-400"></i>
                    @if($case->created_by == ($currentUserId ?? Auth::id()))
                        <span class="text-purple-700 font-medium">You</span>
                    @else
                        {{ $case->createdBy ? $case->createdBy->first_name . ' ' . substr($case->createdBy->last_name ?? '', 0, 1) . '.' : 'Unknown' }}
                    @endif
                </div>
                <div><i class="fas fa-sync text-xs mr-1 text-purple-400"></i>{{ $case->updated_at->format('M d, Y') }}</div>
            </div>

            {{-- Action buttons --}}
            <div class="case-card-actions flex items-center gap-2">
                @php $editDisabled = $case->status !== 'Case Filed'; @endphp
                @if(Auth::user()->position === 'Desk Officer' && Auth::user()->defaultCaseDepartment() === 'BCPC')
                <button type="button" class="case-card-icon-action view-case flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-[#C41E3A] text-white hover:bg-[#A01830] rounded-lg text-sm font-medium transition"
                    data-ajax-url="{{ route('desk-officer.cases.show', ['hash' => encrypt($case->id)]) }}"
                    data-case-number="{{ $case->case_number }}"
                    data-case-id="{{ $case->id }}"
                    data-case-status="{{ $case->status }}">
                    <i class="fas fa-eye text-xs" aria-hidden="true"></i><span class="case-card-action-label">View</span>
                </button>
                @endif
                @if($case->created_by == ($currentUserId ?? Auth::id()) || (Auth::user()->position === 'Desk Officer' && Auth::user()->defaultCaseDepartment() === 'BCPC'))
                @if($editDisabled)
                <span class="case-card-icon-action flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 border border-red-200 bg-red-50 text-red-600 shadow-sm rounded-lg text-sm cursor-not-allowed" title="Only Case Filed cases can be edited" aria-disabled="true">
                    <i class="fas fa-edit text-xs"></i><span class="case-card-action-label">Edit</span>
                </span>
                @else
                <a href="{{ route('desk-officer.cases.edit', ['hash' => encrypt($case->id)]) }}"
                   class="case-card-icon-action edit-case flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 border border-gray-400 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium transition"
                   data-ajax-url="{{ route('desk-officer.cases.edit', ['hash' => encrypt($case->id)]) }}"
                   data-case-number="{{ $case->case_number }}">
                    <i class="fas fa-edit text-xs"></i><span class="case-card-action-label">Edit</span>
                </a>
                @endif
                @include('components.case-archive-button', ['archiveUrl' => route('desk-officer.cases.destroy', ['hash' => encrypt($case->id), 'type' => request('type', strtolower($case->type ?? 'bcpc'))])])
                @else
                @if(Auth::user()->defaultCaseDepartment() === 'BCPC')
                        <span class="case-card-icon-action flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 border border-red-200 bg-red-50 text-red-600 shadow-sm rounded-lg text-sm cursor-not-allowed" title="Only the creator can edit" aria-disabled="true">
                    <i class="fas fa-edit text-xs"></i><span class="case-card-action-label">Edit</span>
                </span>
                        <span class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 border border-red-200 bg-red-50 text-red-800 rounded-lg text-sm cursor-not-allowed" title="Only the creator can archive" aria-disabled="true">
                    <i class="fas fa-archive text-xs"></i> Archive
                </span>
                @else
                <span class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 border border-gray-200 text-gray-400 rounded-lg text-sm cursor-not-allowed" title="Only the creator can edit">
                    <i class="fas fa-lock text-xs"></i> Locked
                </span>
                @endif
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
        <p class="text-sm">No cases found</p>
    </div>
    @endforelse
    <div id="mobile-search-empty" class="hidden flex-col items-center justify-center py-12 text-gray-400">
        <i class="fas fa-search text-4xl mb-3 opacity-50"></i>
        <p class="text-sm">No results found</p>
    </div>
</div>

@if($cases->hasPages())
<div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
    <div class="text-sm text-gray-600">
        Showing {{ $cases->firstItem() ?? 0 }} - {{ $cases->lastItem() ?? 0 }} of {{ $cases->total() }} cases
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ $cases->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $cases->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
            <i class="fas fa-chevron-left text-gray-600"></i>
        </a>
        @php
            $current = $cases->currentPage();
            $last = $cases->lastPage();
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);
        @endphp
        <div class="flex items-center gap-1">
            @if($start > 1)
                <a href="{{ $cases->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">1</a>
                @if($start > 2)
                    <span class="px-2 text-gray-400">&hellip;</span>
                @endif
            @endif
            @for($i = $start; $i <= $end; $i++)
                <a href="{{ $cases->url($i) }}" class="inline-flex items-center justify-center w-9 h-9 {{ $current == $i ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} rounded-lg text-sm">{{ $i }}</a>
            @endfor
            @if($end < $last)
                @if($end < $last - 1)
                    <span class="px-2 text-gray-400">&hellip;</span>
                @endif
                <a href="{{ $cases->url($last) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">{{ $last }}</a>
            @endif
        </div>
        <a href="{{ $cases->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $cases->currentPage() == $cases->lastPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
            <i class="fas fa-chevron-right text-gray-600"></i>
        </a>
    </div>
</div>
@endif
</div>

</div>

@if(
    Auth::user()?->position === 'Desk Officer'
    && Auth::user()?->defaultCaseDepartment() === 'BCPC'
    && !Auth::user()?->isMultiCaseDepartment()
    && session('bcpc_case_archived_success')
)
<div id="bcpc-case-archived-success" role="alertdialog" aria-modal="true" aria-labelledby="bcpc-case-archived-success-title" style="position:fixed;inset:0;z-index:20000;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.45);">
    <div class="w-full max-w-sm rounded-xl bg-white p-8 text-center shadow-xl">
        <i class="fas fa-check-circle mb-4 text-5xl text-green-500" aria-hidden="true"></i>
        <h2 id="bcpc-case-archived-success-title" class="mb-3 text-2xl font-semibold text-gray-800">Archived!</h2>
        <p class="mb-6 text-gray-600">Case archived successfully.</p>
        <button type="button" class="rounded-lg bg-[#C41E3A] px-6 py-2 font-medium text-white hover:bg-[#A01830]" onclick="document.getElementById('bcpc-case-archived-success').remove()">OK</button>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('case-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            // Filter desktop table rows
            const rows = document.querySelectorAll('tbody tr.case-row');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
            // Filter mobile cards
            const cards = document.querySelectorAll('#mobile-case-cards .case-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });

            const hasDesktopMatch = Array.from(rows).some(row => row.style.display !== 'none');
            const hasMobileMatch = Array.from(cards).some(card => card.style.display !== 'none');
            const desktopEmpty = document.getElementById('desktop-search-empty');
            const mobileEmpty = document.getElementById('mobile-search-empty');

            if (desktopEmpty) {
                desktopEmpty.classList.toggle('hidden', !searchTerm || hasDesktopMatch);
            }
            if (mobileEmpty) {
                mobileEmpty.classList.toggle('hidden', !searchTerm || hasMobileMatch);
                mobileEmpty.classList.toggle('flex', !!searchTerm && !hasMobileMatch);
            }
        });
    }

    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const url = new URL(window.location.href);
            const status = document.getElementById('status-filter').value;
            const dateFrom = document.getElementById('date-from').value;
            const dateTo = document.getElementById('date-to').value;

            status ? url.searchParams.set('status', status) : url.searchParams.delete('status');
            dateFrom ? url.searchParams.set('date_from', dateFrom) : url.searchParams.delete('date_from');
            dateTo ? url.searchParams.set('date_to', dateTo) : url.searchParams.delete('date_to');
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });
    }
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            const url = new URL(window.location.href);
            ['status', 'date_from', 'date_to', 'case_type', 'page'].forEach(key => url.searchParams.delete(key));
            window.location.href = url.toString();
        });
    }

    // Filter functionality (links with .filter-option)
    const filterOptions = document.querySelectorAll('.filter-option');
    filterOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const status = this.dataset.status || '';
            const caseType = this.dataset.caseType || '';

            // update active state (visual)
            filterOptions.forEach(opt => opt.classList.remove('bg-indigo-50', 'text-indigo-700'));
            this.classList.add('bg-indigo-50', 'text-indigo-700');

            // Update URL and navigate (reset to page=1)
            const url = new URL(window.location.href);
            if (status) {
                url.searchParams.set('status', status);
                url.searchParams.delete('type');
            } else if (caseType) {
                url.searchParams.set('case_type', caseType);
                url.searchParams.delete('status');
            } else {
                url.searchParams.delete('status');
                url.searchParams.delete('case_type');
                url.searchParams.delete('type');
            }
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });
    });

    // Highlight active filter option from URL
    (function highlightActiveFilter() {
        const urlParams = new URLSearchParams(window.location.search);
        const statusFilter = urlParams.get('status');
        const caseTypeFilter = urlParams.get('case_type');

        if (statusFilter) {
            const el = document.querySelector(`.filter-option[data-status="${statusFilter}"]`);
            if (el) el.classList.add('bg-indigo-50', 'text-indigo-700');
        } else if (caseTypeFilter) {
            const el = document.querySelector(`.filter-option[data-case-type="${caseTypeFilter}"]`);
            if (el) el.classList.add('bg-indigo-50', 'text-indigo-700');
        }
    })();

    // Modal behavior: AJAX loader when clicking ".view-case"
    // Intercept clicks and open content in modal instead of navigating to new page
    const setBcpcReferCaseState = (canRefer) => {
        const referButton = document.getElementById('bcpc-refer-case-button');
        if (!referButton) return;

        if (canRefer) {
            referButton.style.removeProperty('background-color');
            referButton.style.removeProperty('cursor');
            referButton.style.removeProperty('pointer-events');
            referButton.style.removeProperty('opacity');
            return;
        }

        referButton.style.setProperty('background-color', '#9ca3af', 'important');
        referButton.style.setProperty('cursor', 'not-allowed');
        referButton.style.setProperty('pointer-events', 'none');
        referButton.style.setProperty('opacity', '0.7');
    };

    document.querySelectorAll('.view-case').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ajaxUrl = this.dataset.ajaxUrl;
            const canRefer = !/closed|referred/i.test(this.dataset.caseStatus || '');
            console.log('View case clicked:', ajaxUrl);
            if (!ajaxUrl) return; // do normal navigation

            // Always prevent default navigation and show modal
            e.preventDefault();

            // Show loading state immediately by dispatching event with loading content
            const loadingHtml = `
                <div class="text-center py-8">
                    <svg class="animate-spin h-8 w-8 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <p class="mt-3 text-sm text-gray-600">Loading case details...</p>
                </div>
            `;
            
            window.dispatchEvent(new CustomEvent('open-case-modal', {
                detail: { content: loadingHtml, editUrl: '#', canRefer: canRefer }
            }));
            setBcpcReferCaseState(canRefer);

            // Fetch the actual content
            fetch(ajaxUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(r => r.text())
                .then(html => {
                    // Parse the HTML to extract metadata
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Extract URLs and data from hidden elements in the HTML
                    const editLink = doc.querySelector('a.modal-edit-link, a[href*="/edit"]');
                    const editUrl = editLink ? editLink.getAttribute('href') : '#';
                    
                    const printLink = doc.querySelector('a.modal-print-link, a[href*="/print"]');
                    const printUrl = printLink ? printLink.getAttribute('href') : '#';
                    
                    const referLink = doc.querySelector('a.modal-refer-link, a[href*="/refer"]');
                    const referUrl = referLink ? referLink.getAttribute('href') : '#';
                    
                    const caseNumber = doc.querySelector('[data-case-number]')?.dataset.caseNumber || '';
                    const caseId = doc.querySelector('[data-case-id]')?.dataset.caseId || '';
                    const victimEmail = doc.querySelector('[data-victim-email]')?.dataset.victimEmail || '';
                    const openedCaseStatus = doc.querySelector('[data-case-status]')?.dataset.caseStatus || this.dataset.caseStatus || '';
                    const canRefer = !/closed|referred/i.test(openedCaseStatus);

                    // Check ownership flag
                    const isOwnerEl = doc.querySelector('[data-is-owner]');
                    const isOwner = isOwnerEl ? isOwnerEl.dataset.isOwner === '1' : false;

                    // Dispatch event to update modal content with all data
                    window.dispatchEvent(new CustomEvent('open-case-modal', {
                        detail: { 
                            content: html, 
                            editUrl: editUrl,
                            printUrl: printUrl,
                            referUrl: referUrl,
                            caseNumber: caseNumber,
                            caseId: caseId,
                            victimEmail: victimEmail,
                            isOwner: isOwner,
                            canRefer: canRefer
                        }
                    }));
                    setBcpcReferCaseState(canRefer);
                })
                .catch(err => {
                    console.error('Failed to load case details:', err);
                    const errorHtml = `
                        <div class="text-center py-8">
                            <svg class="h-12 w-12 text-red-400 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-gray-600">Failed to load case details.</p>
                            <button onclick="window.location.href='${this.href}'" class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Open in new page</button>
                        </div>
                    `;
                    window.dispatchEvent(new CustomEvent('open-case-modal', {
                        detail: { content: errorHtml, editUrl: '#' }
                    }));
                });
        });
    });

    // Edit Case Modal behavior
    document.querySelectorAll('.edit-case').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ajaxUrl = this.dataset.ajaxUrl;
            const caseNumber = this.dataset.caseNumber || '';
            console.log('Edit case clicked:', ajaxUrl);
            
            if (!ajaxUrl) return; // do normal navigation

            // Prevent default navigation and show modal
            e.preventDefault();

            const loadEditForm = () => {
                if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset(false);
                // Show loading state
            const loadingHtml = `
                <div class="text-center py-8">
                    <svg class="animate-spin h-8 w-8 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <p class="mt-3 text-sm text-gray-600">Loading edit form...</p>
                </div>
            `;
            
            window.dispatchEvent(new CustomEvent('open-edit-modal', {
                detail: { content: loadingHtml, caseNumber: caseNumber }
            }));

            // Fetch the edit form
            fetch(ajaxUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(r => r.text())
                .then(html => {
                    // Dispatch event to update modal content
                    window.dispatchEvent(new CustomEvent('open-edit-modal', {
                        detail: { content: html, caseNumber: caseNumber }
                    }));
                    
                    // Attach form submit handler after content is loaded
                    setTimeout(() => attachEditFormHandler(), 100);
                })
                .catch(err => {
                    console.error('Failed to load edit form:', err);
                    const errorHtml = `
                        <div class="text-center py-8">
                            <svg class="h-12 w-12 text-red-400 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-gray-600">Failed to load edit form.</p>
                            <button onclick="window.location.href='${this.href}'" class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Open in new page</button>
                        </div>
                    `;
                    window.dispatchEvent(new CustomEvent('open-edit-modal', {
                        detail: { content: errorHtml, caseNumber: caseNumber }
                    }));
                });
            };

            if (@json(Auth::user()->position === 'Desk Officer' && Auth::user()->defaultCaseDepartment() === 'BCPC')) {
                loadEditForm();
                return;
            }

            const confirmationText = `Are you sure you want to edit Case No. ${caseNumber}?`;
            if (window.Swal) {
                Swal.fire({
                    title: 'Edit Case?',
                    text: confirmationText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Edit',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#C41E3A',
                }).then((result) => {
                    if (result.isConfirmed) loadEditForm();
                });
                return;
            }

            if (window.confirm(confirmationText)) loadEditForm();
        });
    });

    // Function to close edit modal with unsaved changes confirmation
    window.requestCloseEditCaseModal = function() {
        const editModal = document.getElementById('editCaseModal');
        if (!editModal) return;

        const doClose = () => {
            if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
            window.dispatchEvent(new CustomEvent('close-edit-modal'));
            if (editModal._x_dataStack && editModal._x_dataStack[0]) {
                editModal._x_dataStack[0].open = false;
                editModal._x_dataStack[0].content = '';
            } else if (editModal.__x && editModal.__x.$data) {
                editModal.__x.$data.open = false;
                editModal.__x.$data.content = '';
            }
        };

        if (window.portalUnsavedChanges && window.portalUnsavedChanges.isDirty()) {
            window.portalUnsavedChanges.confirmExit(doClose);
        } else {
            doClose();
        }
    };

    // Delegated handler: close edit modal when injected form's Cancel button is clicked
    document.addEventListener('click', function(e) {
        const btn = e.target.closest && e.target.closest('#cancel-edit-button');
        if (!btn) return;
        e.preventDefault();
        window.requestCloseEditCaseModal();
    });

}); // DOMContentLoaded

// Function to close edit modal
function closeEditModal() {
    if (window.requestCloseEditCaseModal) {
        window.requestCloseEditCaseModal();
    } else {
        const editModal = document.getElementById('editCaseModal');
        if (editModal && editModal.__x) {
            editModal.__x.$data.open = false;
        }
    }
}

// Function to attach form submit handler
function attachEditFormHandler() {
    const form = document.getElementById('edit-case-form-modal');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const actionUrl = this.action;
        
        // Show loading state
        Swal.fire({
            title: 'Updating Case',
            html: '<div class="text-center"><i class="fas fa-spinner fa-spin text-4xl text-purple-500 mb-3"></i><p class="text-gray-700">Please wait...</p></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (response.redirected) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Case updated successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => window.location.href = response.url);
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (data) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Case updated successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    closeEditModal();
                    window.location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update case. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });
}

// Functions for Refer Case and Send Copy modals
async function submitRefer(caseId, selectedRecipients) {
    if (!caseId) {
        Swal.fire('Error', 'Case ID not found', 'error');
        return;
    }
    
    if (!selectedRecipients || selectedRecipients.length === 0) {
        Swal.fire('Error', 'Please select at least one recipient', 'warning');
        return;
    }
    
    // Show loading state
    Swal.fire({
        title: 'Referring Case',
        html: '<div class="text-center"><i class="fas fa-spinner fa-spin text-4xl text-purple-500 mb-3"></i><p class="text-gray-700">Sending emails...</p></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
            allowEscapeKey: false
    });
    
    try {
        const response = await fetch(`/desk-officer/cases/${caseId}/refer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                recipients: selectedRecipients
            })
        });
        
        // Try to parse JSON regardless of status to get error message
        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            console.error('Failed to parse JSON:', parseError);
            throw new Error('Invalid server response');
        }
        
        if (response.ok && data.success) {
            // Close the refer modal by dispatching a custom event
            window.dispatchEvent(new CustomEvent('close-refer-modal'));
            
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload the page to update case status and dashboard counts
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to refer case',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Error referring case:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'An error occurred while referring the case',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}

async function submitSendCopy(caseId, emailAddress) {
    if (!caseId) {
        Swal.fire('Error', 'Case ID not found', 'error');
        return;
    }
    
    if (!emailAddress || emailAddress.trim() === '') {
        Swal.fire('Error', 'Please enter an email address', 'warning');
        return;
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailAddress)) {
        Swal.fire('Error', 'Please enter a valid email address', 'warning');
        return;
    }
    
    // Show loading state
    Swal.fire({
        title: 'Sending Copy',
        html: '<div class="text-center"><i class="fas fa-spinner fa-spin text-4xl text-purple-500 mb-3"></i><p class="text-gray-700">Sending email...</p></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
            allowEscapeKey: false
    });
    
    try {
        const formData = new FormData();
        formData.append('victim_email', emailAddress);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        const response = await fetch(`/admin/cases/${caseId}/send-copy`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        // Check if response is ok
        if (response.ok) {
            // Close the send copy modal by dispatching event
            window.dispatchEvent(new CustomEvent('close-send-copy-modal'));
            
            Swal.fire({
                title: 'Success!',
                text: 'Case copy sent successfully',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else {
            const errorText = await response.text();
            Swal.fire({
                title: 'Error',
                text: 'Failed to send case copy',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Error sending case copy:', error);
        Swal.fire({
            title: 'Error',
            text: 'An error occurred while sending the case copy',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
}

// Print All Cases Function (now streams a PDF preview)
window.previewCasePrint = function(printUrl, caseNumber) {
    if (!printUrl || printUrl === '#') return;

    const openPreview = () => window.open(printUrl, '_blank', 'noopener');
    if (typeof Swal === 'undefined') {
        openPreview();
        return;
    }

    Swal.fire({
        title: `Preview Case #${caseNumber || ''}`,
        text: 'A print preview will open before you print or download the case.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Open Preview',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#7c3aed',
    }).then((result) => {
        if (result.isConfirmed) openPreview();
    });
};

function printAllCases() {
    Swal.fire({
        title: 'Print All Cases',
        text: 'Are you sure you want to print all cases for {{ $barangayName }}?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, print them!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const printUrl = new URL('{{ route('desk-officer.cases.print-all') }}', window.location.origin);
            const currentParams = new URLSearchParams(window.location.search);
            const activeType = currentParams.get('type') || '{{ $isMultiroleDeskOfficer ? 'bcpc' : (strtolower($defaultCaseType ?? 'bcpc')) }}';
            printUrl.searchParams.set('type', activeType);
            ['status', 'date_from', 'date_to'].forEach(key => {
                if (currentParams.has(key)) printUrl.searchParams.set(key, currentParams.get(key));
            });
            const pdfUrl = printUrl.toString();
            window.open(pdfUrl, '_blank', 'noopener');
        }
    });
}
</script>
@endpush


@endsection
