@extends('layouts.admin-tailwind')

@section('title', 'Referrals - iSERVE SILANG Admin')

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 85.375rem) {
        html body.portal-ui #adminReferralEditForm .admin-referral-edit-date-field {
            display: block !important;
            position: relative;
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }
        html body.portal-ui #adminReferralEditForm .admin-referral-edit-date-field > input[type=date] {
            display: block;
            box-sizing: border-box;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            min-height: 2.75rem;
            padding-right: 2.75rem !important;
            -webkit-appearance: none;
            appearance: none;
        }
        html body.portal-ui #adminReferralEditForm .admin-referral-edit-date-field > .admin-referral-edit-date-icon {
            display: block !important;
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: #64748b;
            pointer-events: none;
            z-index: 1;
        }
        html body.portal-ui #adminReferralEditForm .admin-referral-edit-date-field > input[type=date]::-webkit-calendar-picker-indicator {
            opacity: 0;
        }
        html body.portal-ui #adminReferralEditForm .admin-referral-edit-date-field > input[type=date]::-webkit-date-and-time-value {
            text-align: left;
            min-width: 0;
        }
    }
</style>
@endif
@endpush
@push('styles')
<style>
    @if(auth()->user()?->isDepartmentAdmin())
    @media (max-width: 85.375rem) {
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > :is(.view-referral-btn, .edit-referral-btn, .print-referral-btn, .archive-referral-btn) {
            background-color: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #fff !important;
            transition: none !important;
        }
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > :is(.view-referral-btn, .edit-referral-btn, .print-referral-btn, .archive-referral-btn) > i {
            color: inherit !important;
        }
    }
    @media (max-width: 85.375rem) {
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > :is(button, a) {
            font-size: 0 !important;
            gap: 0 !important;
        }
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > :is(button, a) > span {
            font-size: 0 !important;
            margin: 0 !important;
        }
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > :is(button, a) > i {
            font-size: .875rem !important;
        }
    }
    @media (max-width: 85.375rem) {
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > .view-referral-btn { order: 1; }
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > :is(.edit-referral-btn, .disabled-referral-edit) { order: 2; }
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > .print-referral-btn { order: 3; }
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-card-actions > .archive-referral-btn { order: 4; }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        body.portal-ui.admin-referrals-mobile-nav #referralModal {
            z-index: 100 !important;
        }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui.admin-referrals-mobile-nav #referrals-mobile-cards .referral-cards-list > .referrals-empty-state {
            grid-column: 1 / -1;
            width: 100%;
            text-align: center;
        }
    }
    @media (max-width: 47.9375rem) {
        #referrals-summary-heading {
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: .5rem !important;
        }
        #referrals-summary-heading > div { min-width: 0; gap: .5rem; }
        #referrals-summary-heading > .download-referrals-btn {
            flex-shrink: 0;
            margin-left: auto;
            padding-inline: .625rem;
            white-space: nowrap;
        }
    }
    @endif
    @if(auth()->user()->isDepartmentAdmin())
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        #referrals-table-pagination,
        #referrals-card-pagination {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border: 0 !important;
            box-shadow: none !important;
        }
        #referrals-card-pagination > p { text-align: left; flex-shrink: 0; }
        #referrals-card-pagination > div {
            margin-top: 0;
            margin-left: auto;
            justify-content: flex-end;
            transform: none;
        }
        #referrals-table-pagination > div:last-child { margin-left: auto; }
    }
    @media (max-width: 47.9375rem) {
        #referrals-card-pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            border: 0 !important;
            box-shadow: none !important;
        }
        #referrals-card-pagination > p { text-align: left; flex-shrink: 0; }
        #referrals-card-pagination > div {
            min-width: 0;
            margin-top: 0;
            margin-left: auto;
            justify-content: flex-end;
            transform: none;
        }
        #referrals-card-pagination > div > div,
        #referrals-card-pagination > div > div > div {
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        #referrals-card-pagination a { flex-shrink: 0; }
        #referrals-card-pagination > :not(.referrals-phone-pagination) { display: none !important; }
        #referrals-card-pagination > .referrals-phone-pagination { display: block !important; width: 100%; margin: 0; }
        #referrals-card-pagination .referrals-phone-pagination > [data-pagination-label] {
            flex-direction: column; justify-content: center; gap: .5rem; padding: 0; text-align: center;
        }
        #referrals-card-pagination .referrals-phone-pagination [data-pagination-label] > div:last-child {
            gap: clamp(.25rem, 2vw, .625rem); justify-content: center;
        }
        #referrals-card-pagination .referrals-phone-pagination a {
            width: clamp(2rem, 10vw, 2.75rem) !important; min-width: 0 !important; height: 2.75rem !important;
            padding: 0 !important; margin: 0 !important; border: 1px solid #e5e7eb !important;
            border-radius: .5rem; background: #fff !important; color: #C41E3A !important;
            box-shadow: 0 1px 2px rgb(0 0 0 / 4%);
        }
        #referrals-card-pagination .referrals-phone-pagination a[class~="bg-[#C41E3A]"] {
            background: #C41E3A !important; border-color: #C41E3A !important; color: #fff !important;
        }
        #referrals-card-pagination .referrals-phone-pagination a > i { color: inherit !important; }
    }
    @endif
    /* Android tablets (600px–1280px): the shared Admin sidebar is the drawer;
       Referrals uses fluid spacing and lets only the table scroll horizontally. */
    @media (min-width: 37.5rem) and (max-width: 80rem) {
        #referrals-main {
            padding: clamp(1rem, 2vw, 1.5rem) !important;
        }
        #referrals-summary-card {
            padding: clamp(1rem, 2vw, 1.5rem) !important;
        }
        #referrals-table-panel {
            min-width: 0;
        }
        #referrals-table-panel table {
            min-width: 55rem;
        }
        #referrals-table-panel :is(th, td) {
            padding: .75rem clamp(.625rem, 1.2vw, 1rem) !important;
            font-size: clamp(.6875rem, 1.15vw, .8125rem);
        }
        #referrals-table-panel td:last-child > div {
            gap: .375rem;
            flex-wrap: nowrap;
        }
        #referrals-table-panel td:last-child :is(button, a) {
            flex: 0 0 auto;
            width: 2.25rem;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 !important;
        }
        #referrals-table-panel > div:last-child {
            padding: 1rem clamp(1rem, 2vw, 1.5rem) !important;
        }
        #referrals-table-panel > div:last-child > div:last-child {
            max-width: 100%;
            overflow-x: auto;
        }
    }

    /* Phone referral cards. */
    @media (max-width: 47.9375rem) {
        #referrals-table-panel { display: none !important; }
        #referrals-mobile-cards {
            display: block !important;
            padding-bottom: calc(.75rem + env(safe-area-inset-bottom));
        }
        #referrals-mobile-cards .referral-cards-list {
            max-height: none !important;
            overflow: visible !important;
        }
        #referrals-mobile-cards .referral-card {
            min-width: 0;
            padding: 1rem;
        }
        #referrals-mobile-cards .referral-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        #referrals-mobile-cards .referral-card-actions > :is(button, a) {
            width: 100% !important;
            min-width: 0;
            min-height: 3rem;
            padding: .5rem !important;
        }
        #referralModal.is-edit-mode #closeModal,
        #referralModal[data-mode="edit"] #closeModal {
            display: none !important;
        }
    }
    #referralModal[data-mode="edit"] .portal-modal-dismiss-footer .portal-modal-close { display: none !important; }
    @media (max-width: 48rem) {
        #referralModal[data-mode="edit"] .portal-modal-dismiss-footer .portal-modal-close {
            display: none !important;
        }
        #referralModal.is-edit-mode #closeModal,
        #referralModal[data-mode="edit"] #closeModal {
            display: none !important;
        }
    }
    @media (min-width: 40.0625rem) and (max-width: 47.9375rem) {
        #referrals-mobile-cards .referral-card-actions { grid-template-columns: repeat(4, minmax(0, 1fr)); }
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
@if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
<style>
    #referCaseButton:disabled {
        background-color: #FEE2E2 !important;
        border-color: #FCA5A5 !important;
        color: #B91C1C !important;
        font-weight: 700 !important;
    }
    #referCaseButton:disabled > i {
        color: #B91C1C !important;
        font-weight: 900 !important;
    }
    #referCaseButton:disabled > span {
        color: #B91C1C !important;
        font-weight: 700 !important;
    }
    .portal-ui #mainContent button.disabled-referral-edit.disabled-referral-edit:disabled {
        background-color: #FEE2E2 !important;
        border-color: #FCA5A5 !important;
        color: #B91C1C !important;
    }
    .portal-ui #mainContent button.disabled-referral-edit.disabled-referral-edit:disabled > i {
        color: #B91C1C !important;
        font-weight: 900 !important;
    }

    /* Keep the referral registry readable at every desktop/tablet width.  The
       wrapper remains the horizontal-scroll boundary when this minimum width
       cannot fit, instead of compressing column content. */
    #referrals-table-panel > div:first-child { overflow-x: auto; }
    #referrals-table-panel table {
        min-width: 76rem;
        table-layout: fixed;
    }
    #referrals-table-panel table :is(th, td) {
        padding: .875rem 1rem !important;
        vertical-align: middle;
    }
    #referrals-table-panel table thead { background-color: #b91c1c !important; }
    #referrals-table-panel table th { color: #ffffff !important; }
    #referrals-table-panel table th { white-space: nowrap; }

    /* Client, age, sex, services, agency, date, feedback, actions. */
    #referrals-table-panel table :is(th, td):nth-child(1) { width: 15%; text-align: left; }
    #referrals-table-panel table :is(th, td):nth-child(2) { width: 5%; text-align: center; }
    #referrals-table-panel table :is(th, td):nth-child(3) { width: 6%; text-align: center; }
    #referrals-table-panel table :is(th, td):nth-child(4) { width: 18%; text-align: left; }
    #referrals-table-panel table :is(th, td):nth-child(5) { width: 19%; text-align: left; }
    #referrals-table-panel table :is(th, td):nth-child(6) { width: 11%; text-align: left; white-space: nowrap; }
    #referrals-table-panel table :is(th, td):nth-child(7) { width: 14%; text-align: left; }
    #referrals-table-panel table :is(th, td):nth-child(8) { width: 12%; text-align: center; white-space: nowrap; }

    /* Preserve the two-line client identity without splitting identifiers. */
    #referrals-table-panel table td:nth-child(1) p {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Services may use two readable lines; agency email may wrap at a safe
       point rather than forcing adjacent columns narrower. */
    #referrals-table-panel table td:nth-child(4) p {
        display: -webkit-box;
        max-width: none;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        white-space: normal;
    }
    #referrals-table-panel table td:nth-child(5) p:first-child {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    #referrals-table-panel table td:nth-child(5) p:nth-child(2) {
        max-width: none;
        overflow-wrap: anywhere;
        white-space: normal;
    }

    /* Feedback retains its complete value through the existing title tooltip. */
    #referrals-table-panel table td:nth-child(7) p {
        max-width: none;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    #referrals-table-panel table td:nth-child(8) > div {
        display: flex;
        flex-wrap: nowrap;
        justify-content: center;
        gap: .5rem;
    }
    #referrals-table-panel table td:nth-child(8) :is(button, a) {
        flex: 0 0 auto;
        min-width: 2.25rem;
        min-height: 2.25rem;
    }

    /* Tablet portrait presentation for department admins. */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        #referrals-table-panel { display: none !important; }
        #referrals-mobile-cards { display: block !important; }
        #referrals-mobile-cards .referral-cards-list { max-height: none !important; overflow: visible !important; }
        #referrals-mobile-cards .referral-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        #referrals-mobile-cards .referral-card-actions > :is(button, a) {
            width: 100% !important;
            min-width: 0;
            min-height: 3rem;
            padding: .5rem !important;
        }
    }
</style>
@endif
@if(auth()->user()?->isVawcAdmin())
<style>
    /* SWD referrals: allow feedback to grow within its cell and keep actions
       anchored to the left, wrapping cleanly when space is limited. */
    #referrals-table-panel table :is(th, td):nth-child(8) { text-align: left; }
    #referrals-table-panel table td:nth-child(7) p {
        overflow: visible;
        text-overflow: clip;
        white-space: normal;
        overflow-wrap: anywhere;
    }
    #referrals-table-panel table td:nth-child(8) > div {
        flex-wrap: wrap;
        justify-content: flex-start;
    }
</style>
@endif

<style>
    /* Referrals tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #dashboardMenuToggle { display: none !important; }
        html body.portal-ui #sidebarCloseBtn { display: none !important; }
        html body.portal-ui #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header,
        html body.portal-ui #mainContent > header.sticky {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header h2,
        html body.portal-ui #mainContent > header.sticky h2 {
            color: #1f2937 !important;
        }
        html body.portal-ui #mainContent > header.admin-reference-mobile-header p,
        html body.portal-ui #mainContent > header.sticky p {
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
        html body.portal-ui #sidebar .sidebar-header .sidebar-text,
        html body.portal-ui #sidebar .sidebar-text {
            opacity: 0 !important;
            max-width: 0 !important;
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #sidebar .sidebar-menu {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui #sidebar .sidebar-link {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui #sidebar .sidebar-link i {
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            text-align: center !important;
        }
        html body.portal-ui #sidebar ~ #mainContent,
        html body.portal-ui #sidebar.sidebar-collapsed ~ #mainContent,
        html body.portal-ui #sidebar.sidebar-collapsed-init ~ #mainContent {
            width: calc(100% - 4.5rem) !important;
            margin-left: 4.5rem !important;
            min-width: 0 !important;
        }
        html body.portal-ui #sidebarToggleBtn {
            display: none !important;
        }
    }

    @media (min-width: 48rem) and (max-width: 80rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #sidebar {
            inset: 0 auto 0 0 !important;
            height: 100vh !important;
            height: 100dvh !important;
        }
    }
</style>
<style>
    /* Use the existing referral cards throughout tablet portrait and landscape. */
    @media (min-width: 48rem) and (max-width: 80rem) {
        html body.portal-ui #referrals-table-panel {
            display: none !important;
        }
        html body.portal-ui #referrals-mobile-cards {
            display: block !important;
            width: 100%;
            min-width: 0;
            padding-bottom: 1rem;
        }
        html body.portal-ui #referrals-mobile-cards .referral-cards-list {
            max-height: none !important;
            overflow: visible !important;
        }
        html body.portal-ui #referrals-mobile-cards .referral-card {
            min-width: 0;
        }
        html body.portal-ui #referrals-mobile-cards .referral-card-actions {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .5rem;
        }
        html body.portal-ui #referrals-mobile-cards .referral-card-actions > :is(button, a) {
            width: 100% !important;
            min-width: 0;
            min-height: 2.75rem;
            padding: .375rem .5rem !important;
            white-space: nowrap;
            font-size: .75rem;
        }
    }
    @if(auth()->user()?->isDepartmentAdmin())
    @media (max-width: 47.9375rem) {
        html body.portal-ui #referrals-mobile-cards .referral-card-actions {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .375rem;
            align-items: stretch;
        }
        html body.portal-ui #referrals-mobile-cards .referral-card-actions > :is(button, a) {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 100% !important;
            min-width: 0 !important;
            height: 2.75rem !important;
            min-height: 2.75rem !important;
            padding: .375rem .25rem !important;
            gap: .25rem;
            font-size: .6875rem;
            white-space: nowrap;
        }
    }
    @endif
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
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Referrals Management</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">View and manage case referrals</p>
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
<main id="referrals-main" class="px-4 pt-4 pb-4 sm:p-6 lg:p-8 overflow-x-hidden">
    
    <!-- Header Card -->
    <div id="referrals-summary-card" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6">
        <div id="referrals-summary-heading" class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">All Referrals</h3>
                    <p class="text-sm text-gray-500">{{ $referrals->total() }} total {{ Str::plural('referral', $referrals->total()) }}</p>
                </div>
            </div>
            
            <a href="{{ url('/admin/referrals-print-all') . '?' . http_build_query(array_merge(request()->only(['status', 'search']), ['preview' => 1])) }}"
                class="download-referrals-btn inline-flex items-center gap-2 px-4 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-sm font-medium">
                <i class="fas fa-print"></i>
                <span>Print All</span>
            </a>
        </div>
    </div>
    
    @if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
    <form id="referrals-search-form" method="GET" action="{{ route('admin.referrals') }}" class="mb-4 w-full sm:w-80">
        <label class="relative block">
            <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
            <input id="referrals-search-input" type="search" name="search" value="{{ request('search') }}" placeholder="Search referrals..." class="h-10 w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500">
        </label>
    </form>
    @endif

    <!-- Referrals Table (Desktop) -->
    <div id="referrals-table-panel" class="{{ $matchReportTable ? 'bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100' : 'hidden lg:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col min-h-0' }}">
        <div class="{{ $matchReportTable ? 'overflow-x-auto' : 'overflow-auto max-h-[calc(100vh-380px)]' }}">
            <table class="{{ $matchReportTable ? 'w-full' : 'w-full table-fixed' }}">
                <thead class="{{ $matchReportTable ? 'bg-gradient-to-r from-purple-700 to-purple-600 text-white' : 'bg-gray-50 border-b border-gray-200 sticky top-0 z-10' }}">
                    <tr>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}" @unless($matchReportTable) style="width:18%" @endunless>Client</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-center text-sm font-semibold' : 'px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider' }}" @unless($matchReportTable) style="width:6%" @endunless>Age</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-center text-sm font-semibold' : 'px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider' }}" @unless($matchReportTable) style="width:6%" @endunless>Sex</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}" @unless($matchReportTable) style="width:20%" @endunless>Services Needed</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}" @unless($matchReportTable) style="width:20%" @endunless>Receiving Agency</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}" @unless($matchReportTable) style="width:10%" @endunless>Date</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 text-left text-sm font-semibold' : 'px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider' }}" @unless($matchReportTable) style="width:7%" @endunless>Feedback</th>
                        <th class="{{ $matchReportTable ? 'px-6 py-4 '.(auth()->user()?->isVawcAdmin() ? 'text-left' : 'text-center').' text-sm font-semibold' : 'px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider' }}" @unless($matchReportTable) style="width:10%" @endunless>Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($referrals as $referral)
                    @php
                        $recipientUnavailable = $referral->recipient
                            && ($referral->recipient->trashed() || !$referral->recipient->active);
                        $editDisabled = false;
                        $editDisabledTitle = 'Edit';
                    @endphp
                    <tr class="{{ $matchReportTable ? 'hover:bg-purple-50/50 transition' : 'hover:bg-gray-50 transition-colors' }}" data-referral-id="{{ $referral->id }}">
                        <td class="{{ $matchReportTable ? 'px-6' : 'px-4' }} py-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $referral->client_name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $referral->referral_number }}</p>
                            </div>
                        </td>
                        <td class="{{ $matchReportTable ? 'px-6' : 'px-4' }} py-4 text-center text-sm text-gray-600">{{ $referral->client_age }}</td>
                        <td class="{{ $matchReportTable ? 'px-6' : 'px-4' }} py-4 text-center text-sm text-gray-600">{{ $referral->client_sex }}</td>
                        <td class="{{ $matchReportTable ? 'px-6' : 'px-4' }} py-4">
                            <p class="text-sm text-gray-600 max-w-[150px] truncate" title="{{ $referral->specific_services_requested }}">
                                {{ Str::limit($referral->specific_services_requested, 40) }}
                            </p>
                        </td>
                        <td class="{{ $matchReportTable ? 'px-6' : 'px-4' }} py-4">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $referral->recipient_name }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-[180px]">{{ $referral->created_by_department_admin ? $referral->recipient_address : $referral->recipient_email }}</p>
                                @if($recipientUnavailable)
                                    <p class="mt-1 text-xs font-medium text-red-600">The recipient is currently unavailable. The referral cannot be processed due to this reason.</p>
                                @endif
                            </div>
                        </td>
                        <td class="{{ $matchReportTable ? 'px-6' : 'px-4' }} py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($referral->date_of_referral)->format('M d, Y') }}
                        </td>
                        <td class="{{ $matchReportTable ? 'px-6' : 'px-4' }} py-4">
                            @if($referral->feedback)
                                <p class="text-sm text-gray-600 {{ auth()->user()?->isVawcAdmin() ? 'max-w-none whitespace-normal break-words' : 'max-w-[120px] truncate' }}" title="{{ $referral->feedback }}">
                                    {{ auth()->user()?->isVawcAdmin() ? $referral->feedback : Str::limit($referral->feedback, 30) }}
                                </p>
                            @else
                                <span class="text-xs text-gray-400">No feedback yet</span>
                            @endif
                        </td>
                        <td class="{{ $matchReportTable ? 'px-6' : 'px-4' }} py-4 align-middle">
                            <div class="flex w-full items-center {{ auth()->user()?->isVawcAdmin() ? 'justify-start flex-wrap' : 'justify-center' }} gap-2">
                                <button type="button" @if(auth()->user()?->isDepartmentAdmin()) style="background: #2563EB !important; border-color: #2563EB !important; color: #fff !important;" @endif class="{{ $matchReportTable ? 'view-referral-btn inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition shadow-sm' : 'view-referral-btn inline-flex items-center justify-center px-3 py-1.5 bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100 rounded-lg text-xs font-medium transition-colors' }}"
                                    title="View Details" data-id="{{ $referral->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" @disabled($editDisabled) @if(auth()->user()?->isDepartmentAdmin() && !$editDisabled) style="background: #D97706 !important; border-color: #D97706 !important; color: #fff !important;" @endif class="{{ $editDisabled ? 'disabled-referral-edit inline-flex items-center justify-center border border-red-200 bg-red-50 text-red-800 px-3 py-1.5 rounded-lg text-xs font-medium cursor-not-allowed' : ($matchReportTable ? 'edit-referral-btn inline-flex items-center justify-center bg-purple-700 hover:bg-purple-800 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition' : 'edit-referral-btn inline-flex items-center justify-center px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 hover:bg-green-100 rounded-lg text-xs font-medium transition-colors') }}"
                                    title="{{ $editDisabledTitle }}" data-id="{{ $referral->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="{{ route('admin.referrals.print', $referral->id) }}"
                                    @if(auth()->user()?->isDepartmentAdmin()) style="background: #0F766E !important; border-color: #0F766E !important; color: #fff !important;" @endif class="print-referral-btn {{ $matchReportTable ? 'inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition' : 'inline-flex items-center justify-center px-3 py-1.5 bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100 rounded-lg text-xs font-medium transition-colors' }}" title="Print">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if(auth()->user()->isDepartmentAdmin())
<button type="button" class="{{ $matchReportTable ? 'archive-referral-btn inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-medium transition' : 'archive-referral-btn inline-flex items-center justify-center px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 rounded-lg text-xs font-medium transition-colors' }}" title="Archive" data-id="{{ $referral->id }}"><i class="fas fa-archive"></i><span class="sr-only">Archive</span></button>
@endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No referrals found</p>
                                <p class="text-sm text-gray-400 mt-1">Referrals will appear here once created.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div id="referrals-table-pagination" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4">
            @if(auth()->user()?->isDepartmentAdmin())
            <div class="referrals-profile-pagination">@include('components.archived-pagination', ['paginator' => $referrals, 'label' => 'referrals', 'pageLinkLimit' => 4])</div>
            @endif
            <div class="text-sm text-gray-600">
                Showing <span class="font-medium">{{ $referrals->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $referrals->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $referrals->total() }}</span> referrals
            </div>
            @if($referrals->hasPages())
            <div class="flex items-center gap-2">
                <a href="{{ $referrals->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $referrals->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </a>
                <div class="flex items-center gap-1">
                    @php
                        $current = $referrals->currentPage();
                        $last = $referrals->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp
                    @if($start > 1)
                        <a href="{{ $referrals->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm">1</a>
                        @if($start > 2)
                            <span class="px-2">…</span>
                        @endif
                    @endif
                    @for($i = $start; $i <= $end; $i++)
                        <a href="{{ $referrals->url($i) }}" class="inline-flex items-center justify-center w-9 h-9 {{ $referrals->currentPage() == $i ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} rounded-lg text-sm">
                            {{ $i }}
                        </a>
                    @endfor
                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="px-2">…</span>
                        @endif
                        <a href="{{ $referrals->url($last) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm">{{ $last }}</a>
                    @endif
                </div>
                <a href="{{ $referrals->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $referrals->currentPage() == $referrals->lastPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Referrals Cards (Mobile/Tablet) -->
    @if(auth()->user()?->isDepartmentAdmin())
    <style>
        .referral-tablet-details, .referral-tablet-heading { display: none; }
        @media (min-width: 48rem) and (max-width: 85.375rem) {
            html body.portal-ui #referrals-table-panel { display: none !important; }
            html body.portal-ui #referrals-mobile-cards { display: block !important; background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1rem; }
            .referral-tablet-heading { display: block; color: #B11116; font-size: 1rem; font-weight: 600; margin-bottom: 1.25rem; }
            html body.portal-ui #referrals-mobile-cards .referral-cards-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; max-height: none !important; overflow: visible !important; }
            html body.portal-ui #referrals-mobile-cards .referral-card { display: flex; flex-direction: column; min-width: 0; margin: 0 !important; padding: 1rem; border: 1px solid #dbe1e8; border-radius: .625rem; }
            #referrals-mobile-cards .referral-card > .referral-original-details { display: none; }
            .referral-tablet-details { display: block; font-size: .8125rem; line-height: 1.4; overflow-wrap: anywhere; }
            .referral-tablet-details small { display: block; color: #6b7280; font-size: .6875rem; }
            .referral-tablet-details .referral-tablet-top { display: flex; justify-content: space-between; gap: .75rem; padding-bottom: .75rem; border-bottom: 1px solid #f1f5f9; }
            .referral-tablet-details dl { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .625rem; padding-block: .75rem; }
            .referral-tablet-details dt { font-size: .6875rem; font-weight: 600; color: #6b7280; }
            .referral-tablet-details .referral-wide { grid-column: 1 / -1; }
            html body.portal-ui #referrals-mobile-cards .referral-card-actions { display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .5rem; margin-top: auto; }
            html body.portal-ui #referrals-mobile-cards .referral-card-actions > :is(button, a) { width: 100% !important; min-width: 0; height: 2.75rem !important; min-height: 2.75rem; border-radius: .5rem !important; }
        }
    </style>
    @endif
    <div id="referrals-mobile-cards" class="hidden">
        @if(auth()->user()?->isDepartmentAdmin())
        <h3 class="referral-tablet-heading">{{ auth()->user()->isBcpcAdmin() ? 'BCPC' : 'SWD' }} Referrals</h3>
        @endif
        <div class="referral-cards-list space-y-4 max-h-[calc(100vh-420px)] overflow-y-auto pb-2">
            @forelse($referrals as $referral)
            @php
                $recipientUnavailable = $referral->recipient
                    && ($referral->recipient->trashed() || !$referral->recipient->active);
                $editDisabled = false;
                $editDisabledTitle = 'Edit';
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 referral-card" data-id="{{ $referral->id }}">
                @if(auth()->user()?->isDepartmentAdmin())
                <div class="referral-tablet-details">
                    <div class="referral-tablet-top">
                        <div><small>Client</small><strong>{{ $referral->client_name }}</strong><small>{{ $referral->referral_number }}</small></div>
                        <div class="text-right"><small>Date</small>{{ \Carbon\Carbon::parse($referral->date_of_referral)->format('M d, Y') }}</div>
                    </div>
                    <dl>
                        <div><dt>Age</dt><dd>{{ $referral->client_age }}</dd></div>
                        <div><dt>Sex</dt><dd>{{ $referral->client_sex }}</dd></div>
                        <div class="referral-wide"><dt>Services Needed</dt><dd>{{ $referral->specific_services_requested ?: 'N/A' }}</dd></div>
                        <div class="referral-wide"><dt>Receiving Agency</dt><dd>{{ $referral->recipient_name }}<small>{{ $referral->created_by_department_admin ? $referral->recipient_address : $referral->recipient_email }}</small></dd>
                            @if($recipientUnavailable)<small class="text-red-600">The recipient is currently unavailable. The referral cannot be processed due to this reason.</small>@endif
                        </div>
                        <div class="referral-wide"><dt>Feedback</dt><dd>{{ $referral->feedback ?: 'No feedback yet' }}</dd></div>
                    </dl>
                </div>
                @endif
                <div class="referral-original-details flex items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $referral->client_name }}</p>
                        <p class="text-xs text-gray-500">{{ $referral->referral_number }}</p>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-gray-500">
                        <span class="px-2 py-0.5 bg-gray-100 rounded">{{ $referral->client_age }} yrs</span>
                        <span class="px-2 py-0.5 bg-gray-100 rounded">{{ $referral->client_sex }}</span>
                    </div>
                </div>
                
                <div class="referral-original-details space-y-2 text-sm mb-4">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-building text-purple-500 mt-0.5 w-4"></i>
                        <div>
                            <p class="font-medium text-gray-700">{{ $referral->recipient_name }}</p>
                            <p class="text-xs text-gray-500">{{ $referral->created_by_department_admin ? $referral->recipient_address : $referral->recipient_email }}</p>
                            @if($recipientUnavailable)
                                <p class="mt-1 text-xs font-medium text-red-600">The recipient is currently unavailable. The referral cannot be processed due to this reason.</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar text-purple-500 w-4"></i>
                        <span class="text-gray-600">{{ \Carbon\Carbon::parse($referral->date_of_referral)->format('M d, Y') }}</span>
                    </div>
                    @if($referral->specific_services_requested)
                    <div class="flex items-start gap-2">
                        <i class="fas fa-clipboard-list text-purple-500 mt-0.5 w-4"></i>
                        <span class="text-gray-600 text-xs">{{ Str::limit($referral->specific_services_requested, 60) }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="referral-card-actions flex gap-2 pt-3 border-t border-gray-100">
                    <button type="button" @if(auth()->user()?->isDepartmentAdmin()) style="background: #2563EB !important; border-color: #2563EB !important; color: #fff !important;" @endif class="view-referral-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
                        data-id="{{ $referral->id }}">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    <button type="button" @disabled($editDisabled) @if(auth()->user()?->isDepartmentAdmin() && !$editDisabled) style="background: #D97706 !important; border-color: #D97706 !important; color: #fff !important;" @endif class="{{ $editDisabled ? 'disabled-referral-edit flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-red-800 border border-red-200 bg-red-50 rounded-lg cursor-not-allowed' : 'edit-referral-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors' }}"
                        title="{{ $editDisabledTitle }}" data-id="{{ $referral->id }}">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                                <a href="{{ route('admin.referrals.print', $referral->id) }}"
                        @if(auth()->user()?->isDepartmentAdmin()) style="background: #0F766E !important; border-color: #0F766E !important; color: #fff !important;" @endif class="print-referral-btn flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <i class="fas fa-print"></i>
                        Print
                    </a>
                    @if(auth()->user()->isDepartmentAdmin())
<button type="button" class="archive-referral-btn p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Archive" data-id="{{ $referral->id }}"><i class="fas fa-archive"></i><span class="sr-only">Archive</span></button>
@endif
                </div>
            </div>
            @empty
            <div class="referrals-empty-state bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">No referrals found</p>
                <p class="text-sm text-gray-400 mt-1">Referrals will appear here once created.</p>
            </div>
            @endforelse
        </div>
        
        <!-- Mobile Pagination -->
        <div id="referrals-card-pagination" class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 mt-4">
            @if(auth()->user()?->isDepartmentAdmin())
            <div class="referrals-phone-pagination hidden">
                @if($referrals->hasPages())
                    @include('components.archived-pagination', ['paginator' => $referrals, 'label' => 'referrals', 'pageLinkLimit' => 4])
                @else
                    <p class="text-center text-sm text-gray-600">Showing {{ $referrals->firstItem() ?? 0 }} - {{ $referrals->lastItem() ?? 0 }} of {{ $referrals->total() }} referrals</p>
                @endif
            </div>
            @endif
            <p class="text-center text-xs text-gray-600">
                Showing {{ $referrals->firstItem() ?? 0 }}-{{ $referrals->lastItem() ?? 0 }} of {{ $referrals->total() }}
            </p>
            @if($referrals->hasPages())
            <div class="flex justify-center mt-2 scale-90 origin-center">
                <div class="flex items-center gap-2">
                    <a href="{{ $referrals->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $referrals->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                        <i class="fas fa-chevron-left text-gray-600"></i>
                    </a>
                    @php
                        $current = $referrals->currentPage();
                        $last = $referrals->lastPage();
                        $start = max(1, $current - 2);
                        $end = min($last, $current + 2);
                    @endphp
                    <div class="flex items-center gap-1">
                        @if($start > 1)
                            <a href="{{ $referrals->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm">1</a>
                            @if($start > 2)
                                <span class="px-2">…</span>
                            @endif
                        @endif
                        @for($i = $start; $i <= $end; $i++)
                            <a href="{{ $referrals->url($i) }}" class="inline-flex items-center justify-center w-9 h-9 {{ $referrals->currentPage() == $i ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} rounded-lg text-sm">
                                {{ $i }}
                            </a>
                        @endfor
                        @if($end < $last)
                            @if($end < $last - 1)
                                <span class="px-2">…</span>
                            @endif
                            <a href="{{ $referrals->url($last) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 rounded-lg text-sm">{{ $last }}</a>
                        @endif
                    </div>
                    <a href="{{ $referrals->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $referrals->currentPage() == $referrals->lastPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                        <i class="fas fa-chevron-right text-gray-600"></i>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</main>

<!-- Referral Details Modal -->
<div id="referralModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" id="modalOverlay"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden relative">
            <!-- Modal Header -->
            <div class="bg-[#C41E3A] px-6 py-4 flex items-center justify-between gap-3">
                <h3 id="referralModalTitle" class="min-w-0 text-lg font-semibold text-white flex items-center gap-2">
                    <i id="referralModalIcon" class="fas fa-file-alt"></i>
                    <span id="referralModalHeading">Referral Details</span>
                </h3>
                <div class="flex shrink-0 items-center gap-2">
                    @if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
                    <button id="referCaseButton" type="button" disabled aria-disabled="true" class="inline-flex cursor-not-allowed items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-[#EEEEEE] px-2 py-1.5 text-xs font-bold text-black [&>i]:font-black [&>i]:text-black sm:gap-2 sm:px-3 sm:py-2 sm:text-sm" aria-label="Refer Case unavailable after referral">
                        <i class="fas fa-share-square" aria-hidden="true"></i>
                        <span>Refer Case</span>
                    </button>
                    @endif
                    <button id="closeModal" class="text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Content -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]" id="referralDetails">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-700"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@if(auth()->user()?->isDepartmentAdmin())
<script>
(() => {
    const selectors = ['#referrals-table-panel', '#referrals-mobile-cards'];
    let pending = null;
    const cancelRefresh = () => pending?.abort();
    const refreshReferrals = async () => {
        const regions = selectors.map(selector => document.querySelector(selector));
        const modal = document.getElementById('referralModal');
        if (document.hidden || pending || regions.some(region => !region || region.hasAttribute('aria-busy'))
            || (modal && !modal.classList.contains('hidden')) || document.querySelector('.swal2-container')) return;
        const url = location.href;
        const request = new AbortController();
        pending = request;
        const timeout = setTimeout(() => request.abort(), 10000);
        try {
            const response = await fetch(url, {
                cache: 'no-store', signal: request.signal,
                headers: { 'Accept': 'text/html', 'X-Skip-Loading': '1' },
            });
            if (!response.ok || response.redirected) return;
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const replacements = selectors.map(selector => page.querySelector(selector));
            if (request.signal.aborted || location.href !== url || replacements.some(region => !region)
                || regions.some((region, index) => document.querySelector(selectors[index]) !== region || region.hasAttribute('aria-busy'))
                || (modal && !modal.classList.contains('hidden')) || document.querySelector('.swal2-container')) return;
            regions.forEach((region, index) => {
                if (region.innerHTML !== replacements[index].innerHTML) region.innerHTML = replacements[index].innerHTML;
            });
        } catch (_) {
            // Preserve the current referrals if a background refresh is unavailable.
        } finally {
            clearTimeout(timeout);
            if (pending === request) pending = null;
        }
    };
    document.addEventListener('input', cancelRefresh, true);
    document.addEventListener('submit', cancelRefresh, true);
    document.addEventListener('click', event => {
        if (event.target.closest('#referrals-main a[href], #referrals-main button')) cancelRefresh();
    }, true);
    window.addEventListener('popstate', cancelRefresh);
    window.addEventListener('focus', refreshReferrals);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) cancelRefresh();
        else refreshReferrals();
    });
    window.setInterval(refreshReferrals, 3000);
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const responsivePagination = matchMedia('(max-width: 85.375rem)');
    let activeRequest;
    document.addEventListener('click', async event => {
        const link = event.target.closest('#referrals-card-pagination a[href], #referrals-table-pagination a[href]');
        if (!responsivePagination.matches || !link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.classList.contains('pointer-events-none')) return;
        event.preventDefault();
        activeRequest?.abort();
        const controller = new AbortController();
        activeRequest = controller;
        const cards = document.getElementById('referrals-mobile-cards');
        cards?.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(link.href, { signal: controller.signal, headers: { Accept: 'text/html' } });
            if (!response.ok || response.redirected) throw new Error('Unable to load referrals');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (controller.signal.aborted) return;
            const replacements = ['#referrals-table-panel', '#referrals-mobile-cards']
                .map(selector => [document.querySelector(selector), page.querySelector(selector)]);
            if (replacements.some(([current, next]) => !current || !next)) throw new Error('Missing referral list');
            const top = cards.getBoundingClientRect().top;
            replacements.forEach(([current, next]) => current.replaceWith(next));
            history.replaceState(null, '', link.href);
            window.scrollBy({ top: document.getElementById('referrals-mobile-cards').getBoundingClientRect().top - top, behavior: 'instant' });
        } catch (error) {
            if (error.name !== 'AbortError') location.assign(link.href);
        } finally {
            cards?.removeAttribute('aria-busy');
        }
    });
});
</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('referralModal');
    @if(auth()->user()?->isDepartmentAdmin())
    const portraitModalMedia = matchMedia('(min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)');
    const modalHome = document.createComment('Referral modal position');
    modal.before(modalHome);
    const positionReferralModal = () => {
        if (portraitModalMedia.matches) document.body.append(modal);
        else modalHome.after(modal);
    };
    positionReferralModal();
    portraitModalMedia.addEventListener('change', positionReferralModal);
    @endif
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModal = document.getElementById('closeModal');
    const referralDetails = document.getElementById('referralDetails');
    const referCaseButton = document.getElementById('referCaseButton');
    let referralDetailsRequestId = 0;

    function configureReferCaseButton(referUrl = null) {
        if (!referCaseButton) return;

        referCaseButton.style.display = '';
        const enabled = Boolean(referUrl);
        referCaseButton.disabled = !enabled;
        referCaseButton.setAttribute('aria-disabled', enabled ? 'false' : 'true');
        referCaseButton.dataset.referUrl = referUrl || '';
        referCaseButton.className = enabled
            ? 'inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-[#FF8F00] px-2 py-1.5 text-xs font-bold text-white hover:bg-[#FF8F00] sm:gap-2 sm:px-3 sm:py-2 sm:text-sm'
            : 'inline-flex cursor-not-allowed items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-[#EEEEEE] px-2 py-1.5 text-xs font-bold text-black [&>i]:font-black [&>i]:text-black sm:gap-2 sm:px-3 sm:py-2 sm:text-sm';
        referCaseButton.setAttribute('aria-label', enabled ? 'Refer Case' : 'Refer Case unavailable after referral');
    }

    referCaseButton?.addEventListener('click', function() {
        if (!this.disabled && this.dataset.referUrl) {
            if (@json(auth()->user()?->isDepartmentAdmin()) && matchMedia('(min-width: 48rem) and (max-width: 85.375rem)').matches) {
                const referralId = new URL(this.dataset.referUrl, location.origin).searchParams.get('source_referral');
                if (referralId) sessionStorage.setItem('admin-referral-details-return', JSON.stringify({ id: referralId, url: location.href }));
            }
            window.location.assign(this.dataset.referUrl);
        }
    });

    window.addEventListener('pageshow', () => {
        if (!@json(auth()->user()?->isDepartmentAdmin()) || !matchMedia('(min-width: 48rem) and (max-width: 85.375rem)').matches) return;
        const saved = sessionStorage.getItem('admin-referral-details-return');
        if (!saved) return;
        sessionStorage.removeItem('admin-referral-details-return');
        try {
            const previous = JSON.parse(saved);
            if (previous.url === location.href && /^\d+$/.test(previous.id)) fetchReferralDetails(previous.id);
        } catch (_) {
            // Ignore an invalid saved return state.
        }
    });

    document.addEventListener('click', async function (event) {
        const printButton = event.target.closest('.print-referral-btn');
        const downloadButton = event.target.closest('.download-referrals-btn');
        if (!printButton && !downloadButton) return;

        event.preventDefault();
        const link = printButton || downloadButton;
        const result = await Swal.fire({
            title: downloadButton ? 'Print all referrals?' : 'Open referral printout?',
            text: downloadButton ? 'This will open a print preview of the referral registry.' : 'This will open the referral print preview.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: downloadButton ? 'Preview' : 'Open printout',
            confirmButtonColor: '#7c3aed'
        });
        if (result.isConfirmed) {
            if (printButton) window.location.assign(link.href);
            else window.location.assign(`${link.href}${link.href.includes('?') ? '&' : '?'}preview=1`);
        }
    });
    
    // View referral details
    document.addEventListener('click', function(e) {
        const viewBtn = e.target.closest('.view-referral-btn');
        if (viewBtn) {
            const referralId = viewBtn.dataset.id;
            fetchReferralDetails(referralId);
        }
    });
    
    const notificationReferralId = new URLSearchParams(window.location.search).get('view_referral');
    if (notificationReferralId && /^\d+$/.test(notificationReferralId)) {
        fetchReferralDetails(notificationReferralId);
    }

    function fetchReferralDetails(id) {
        const requestId = ++referralDetailsRequestId;
        configureReferCaseButton();
        // Show loading
        referralDetails.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-700"></div>
            </div>
        `;
        modal.classList.remove('hidden');
        modal.classList.remove('is-edit-mode');
        modal.removeAttribute('data-mode');
        document.body.style.overflow = 'hidden';
        const modalHeading = document.getElementById('referralModalHeading');
        const modalIcon = document.getElementById('referralModalIcon');
        if (modalHeading) modalHeading.textContent = 'Referral Details';
        if (modalIcon) modalIcon.className = 'fas fa-file-alt';
        
        // Fetch details via AJAX
        fetch(`/admin/referrals/${id}/json`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (requestId !== referralDetailsRequestId) return;
            showReferralDetails(data);
        })
        .catch(error => {
            if (requestId !== referralDetailsRequestId) return;
            console.error('Error fetching referral details:', error);
            referralDetails.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-500">Failed to load referral details</p>
                </div>
            `;
        });
    }
    
    function showReferralDetails(data) {
        configureReferCaseButton(data.refer_url);
        referralDetails.innerHTML = `
            <div class="space-y-6">
                <!-- Client Information -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fas fa-user text-purple-600"></i>
                        Client Information
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Name</p>
                            <p class="text-sm font-medium text-gray-800">${data.client_name || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Age</p>
                            <p class="text-sm font-medium text-gray-800">${data.client_age || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Sex</p>
                            <p class="text-sm font-medium text-gray-800">${data.client_sex || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Referral Details -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-purple-600"></i>
                        Referral Information
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Referral Number</p>
                                <p class="text-sm font-medium text-gray-800">${data.referral_number || 'N/A'}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Date of Referral</p>
                                <p class="text-sm font-medium text-gray-800">${data.date_of_referral || 'N/A'}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Services Requested</p>
                            <p class="text-sm text-gray-800">${data.specific_services_requested || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Receiving Agency -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fas fa-building text-purple-600"></i>
                        Receiving Agency
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Agency Name</p>
                            <p class="text-sm font-medium text-gray-800">${data.recipient_name || 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm font-medium text-gray-800">${data.recipient_email || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                
                ${data.feedback ? `
                <!-- Feedback -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fas fa-comment-alt text-purple-600"></i>
                        Feedback
                    </h4>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-gray-800">${data.feedback}</p>
                        ${data.feedback_received_at ? `<p class="text-xs text-gray-500 mt-2">Received: ${data.feedback_received_at}</p>` : ''}
                    </div>
                </div>
                ` : ''}
                
                ${data.additional_notes ? `
                <!-- Notes -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-purple-600"></i>
                        Additional Notes
                    </h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700">${data.additional_notes}</p>
                    </div>
                </div>
                ` : ''}
            </div>
        `;
    }

    // Edit referral - load edit form into modal
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-referral-btn');
        if (editBtn) {
            referralDetailsRequestId += 1;
            const id = editBtn.dataset.id;
            if (referCaseButton) referCaseButton.style.display = 'none';
            // show loading state
            referralDetails.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-700"></div>
                </div>
            `;
            modal.classList.remove('hidden');
            modal.classList.add('is-edit-mode');
            modal.setAttribute('data-mode', 'edit');
            document.body.style.overflow = 'hidden';
            const modalHeading = document.getElementById('referralModalHeading');
            const modalIcon = document.getElementById('referralModalIcon');
            if (modalHeading) modalHeading.textContent = 'Edit Referral';
            if (modalIcon) modalIcon.className = 'fas fa-edit';

            fetch(`/admin/referrals/${id}/json`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                // render simple edit form for admin fields
                referralDetails.innerHTML = `
                    <form id="adminReferralEditForm">
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500">Referral Number</label>
                                <p class="text-sm font-medium text-gray-800">${data.referral_number || 'N/A'}</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Date of Referral</label>
                                @if(auth()->user()->isDepartmentAdmin())
                                <p class="w-full border rounded p-2 bg-gray-50 text-sm text-gray-800">${data.date_of_referral || 'N/A'}</p>
                                @else
                                <input type="date" name="date_of_referral" class="w-full border rounded p-2" value="${data.date_of_referral_input || ''}">
                                @endif
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Feedback</label>
                                <textarea name="action_taken" class="w-full border rounded p-2" rows="3">${data.feedback || ''}</textarea>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">{{ auth()->user()->isDepartmentAdmin() ? 'Date of Feedback' : 'Date feedback received' }}@if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin()) <span class="text-red-500">*</span>@endif</label>
                                <div class="admin-referral-edit-date-field" style="display: contents;"><input type="date" name="received_at" class="w-full border rounded p-2" value="${data.feedback_received_at ? (new Date(data.feedback_received_at)).toISOString().slice(0,10) : ''}" @if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin()) required @endif><svg class="admin-referral-edit-date-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Remarks</label>
                                <textarea name="remarks" class="w-full border rounded p-2" rows="2">${data.additional_notes || ''}</textarea>
                            </div>
                            <div class="flex justify-end gap-2">

                                <button type="button" id="cancelEdit" class="px-4 py-2 bg-gray-100 rounded">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded">Save changes</button>
                            </div>
                        </div>
                    </form>
                `;

                // cancel handler

                document.getElementById('cancelEdit').addEventListener('click', hideModal);
                // submit handler
                document.getElementById('adminReferralEditForm').addEventListener('submit', function(ev){
                    ev.preventDefault();
                    const form = ev.target;
                    const formData = new FormData(form);
                    fetch(`/admin/referrals/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    }).then(async r => {
                        const response = await r.json().catch(() => ({}));
                        if (!r.ok || !response.success) throw new Error(response.message || 'Failed to save changes');
                        return response;
                    })
                    .then(resp => {
                        hideModal();
                        Swal.fire({ icon: 'success', title: 'Updated!', text: resp.message || 'Referral updated successfully.', confirmButtonText: 'OK' })
                            .then(() => window.location.assign(resp.redirect || window.location.href));
                    }).catch(err => {
                        console.error(err);
                        Swal.fire('Error', err.message || 'Failed to save changes', 'error');
                    });
                });
            }).catch(err => {
                console.error(err);
                referralDetails.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                        </div>
                        <p class="text-gray-500">Failed to load edit form</p>
                    </div>
                `;
            });
        }
    });
    
    // Close modal
    function hideModal() {
        modal.classList.add('hidden');
        modal.classList.remove('is-edit-mode');
        modal.removeAttribute('data-mode');
        document.body.style.overflow = '';
    }
    
    closeModal.addEventListener('click', hideModal);
    modalOverlay.addEventListener('click', hideModal);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') hideModal();
    });
});
</script>
@endpush

@endsection

@if(auth()->user()->isDepartmentAdmin())
@push('scripts')
<script>
document.addEventListener('click', async function(event) {
    const button = event.target.closest('.archive-referral-btn');
    if (!button || button.disabled) return;
    const result = await Swal.fire({title: 'Archive Referral?', text: 'This referral will be archived. Referral info stays active while other referrals remain.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Archive', confirmButtonColor: '#ef4444'});
    if (!result.isConfirmed) return;
    button.disabled = true;
    try {
        Swal.fire({ title: 'Archiving Referral...', text: 'Please wait while the referral is archived.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        const response = await fetch(`/admin/referrals/${button.dataset.id}/archive`, {method: 'POST', headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json', 'X-Skip-Loading': '1'}});
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || 'Failed to archive referral');
        if (@json(auth()->user()->isBcpcAdmin())) {
            await Swal.fire({
                icon: 'success',
                title: 'Archived!',
                text: data.message,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#ef4444'
            });
            window.location.reload();
            return;
        }
        await Swal.fire('Archived!', data.message, 'success');
        window.location.reload();
    } catch (error) {
        Swal.fire('Error', error.message, 'error');
        button.disabled = false;
    }
});
</script>
@endpush
@endif

@if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
@push('scripts')
<script>
const referralsSearchInput = document.getElementById('referrals-search-input');
let referralsSearchTimer;
if (referralsSearchInput) {
    referralsSearchInput.addEventListener('input', () => {
        clearTimeout(referralsSearchTimer);
        referralsSearchTimer = setTimeout(() => referralsSearchInput.form.submit(), 200);
    });
}
</script>
@endpush
@endif

@include('admin-tailwind.components.profile-pagination', ['paginationRegions' => ['#referrals-table-panel', '#referrals-mobile-cards'], 'paginationLinks' => '#referrals-table-pagination a[href], #referrals-card-pagination a[href]'])
