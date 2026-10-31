@extends(auth()->user()?->position === 'Desk Officer' ? 'layouts.desk-officer' : 'layouts.admin-tailwind')

@section('title', 'Barangay Cases - iSERVE SILANG Admin')

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
@include('admin-tailwind.components.tablet-profile-pagination')
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 47.9375rem) {
        html body.portal-ui #mainContent .tablet-profile-pagination {
            flex-direction: column;
            justify-content: center;
            gap: .5rem;
            padding: .75rem;
            background: #fff;
            text-align: center;
        }
        html body.portal-ui #mainContent .tablet-profile-pagination > div:last-child {
            display: flex;
            justify-content: center;
            gap: clamp(.25rem, 2vw, .625rem);
            max-width: 100%;
        }
        html body.portal-ui #mainContent .tablet-profile-pagination #page-numbers { display: contents !important; }
        html body.portal-ui #mainContent .tablet-profile-pagination #mobile-page-info { display: none !important; }
        html body.portal-ui #mainContent .tablet-profile-pagination button {
            flex: 0 0 auto;
            width: clamp(2rem, 10vw, 2.75rem) !important;
            min-width: 0 !important;
            height: 2.75rem !important;
            padding: 0 !important;
            margin: 0 !important;
            border: 1px solid #e5e7eb !important;
            border-radius: .5rem;
            background: #fff !important;
            color: #C41E3A !important;
            box-shadow: 0 1px 2px rgb(0 0 0 / 4%);
        }
        html body.portal-ui #mainContent .tablet-profile-pagination button[class~="bg-[#C41E3A]"] {
            background: #C41E3A !important;
            border-color: #C41E3A !important;
            color: #fff !important;
        }
        html body.portal-ui #mainContent .tablet-profile-pagination button:disabled { opacity: .4 !important; }
        html body.portal-ui #mainContent .tablet-profile-pagination button > i { color: inherit !important; }
        html body.portal-ui #mainContent :is(#barangay-filter-popup, #barangay-sort-popup) {
            position: fixed !important;
            inset: max(1rem, env(safe-area-inset-top)) auto auto 50% !important;
            transform: translateX(-50%) !important;
            width: min(15rem, calc(100vw - 2rem - env(safe-area-inset-left) - env(safe-area-inset-right))) !important;
            max-height: calc(100dvh - max(1rem, env(safe-area-inset-top)) - max(1rem, env(safe-area-inset-bottom)));
            box-sizing: border-box;
            overflow-y: auto;
            margin: 0 !important;
            padding: .75rem !important;
            z-index: 60 !important;
        }
        html body.portal-ui #mainContent :is(#barangay-filter-popup, #barangay-sort-popup) h4 { font-size: .875rem; margin-bottom: .5rem; }
        html body.portal-ui #mainContent :is(#barangay-filter-popup, #barangay-sort-popup) :is(input, select, label, button) { font-size: .75rem !important; }
        html body.portal-ui #mainContent :is(#barangay-filter-popup, #barangay-sort-popup) .mb-4 { margin-bottom: .5rem; }
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 47.9375rem) {
        .portal-ui .swal2-popup.barangay-cases-print-popup .swal2-actions {
            display: flex !important;
            flex-wrap: nowrap !important;
            width: calc(100% - 2rem);
            gap: .75rem;
        }
        .portal-ui .swal2-popup.barangay-cases-print-popup .swal2-actions > :is(.swal2-cancel, .swal2-confirm) {
            flex: 1 1 0 !important;
            width: 100%;
            min-width: 0;
            min-height: 2.75rem;
            margin: 0 !important;
            padding: .625rem .5rem;
        }
    }
</style>
@endif
@if(auth()->user()?->position !== 'Desk Officer')
<style>
    @media (max-width: 40rem) {
        html body.portal-ui.barangay-cases-admin-mobile-nav #mobileTopBar {
            display: none !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header.sticky {
            background: #C41E3A !important;
            border-bottom-color: #99172E !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header > div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent .tablet-header-row {
            grid-template-columns: minmax(0, 1fr) auto !important;
            align-items: flex-start !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent #mobileMenuToggle {
            display: none !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent .barangay-cases-header-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
            grid-column: 1 !important;
            grid-row: 1 !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent .tablet-header-row > div:last-child {
            grid-column: 2 !important;
            grid-row: 1 !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #dashboardMenuToggle {
            display: inline-flex !important;
            flex: 0 0 2.5rem;
            width: 2.5rem;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #dashboardMenuToggle i {
            color: inherit !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent .barangay-cases-header-leading > div {
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header h2 {
            color: #ffffff !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header p {
            display: block !important;
            color: rgba(255,255,255,.85) !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header > div > div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header button[aria-label="Notifications"] {
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header button[aria-label="Notifications"] > i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header #notifications-unread-count {
            width: 1.25rem !important;
            height: 1.25rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar {
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: auto !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
            max-height: calc(100vh - 60px) !important;
            overflow-y: auto !important;
            z-index: 999 !important;
            border-radius: 0 0 14px 14px !important;
            box-shadow: 0 14px 28px rgba(0,0,0,.22) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-8px) !important;
            transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
            background: #C41E3A !important;
            background-image: none !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar nav :is(a, button),
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #fff !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar nav :is(a, button) :is(i,svg,span),
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar .sidebar-link :is(i,svg,span) {
            color: #fff !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar nav :is(a,button):hover,
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #fff !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar .sidebar-link:is([class~="bg-purple-600"],[class~="bg-purple-700/70"]),
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar nav [x-show] > :is(a,button).bg-purple-600 {
            background: #99172E !important;
            color: #fff !important;
            box-shadow: inset 3px 0 0 #fff !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebar #sidebarCloseBtn,
        html body.portal-ui.barangay-cases-admin-mobile-nav #sidebarToggleBtn {
            display: none !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent {
            width: 100% !important;
            min-width: 0 !important;
            margin-left: 0 !important;
        }
    }

    @media (max-width: 26.875rem) {
        html body.portal-ui.barangay-cases-admin-mobile-nav #mainContent > header button {
            min-width: 3rem !important;
            min-height: 3rem !important;
        }
    }

    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table :is(th, td) {
            padding: .75rem .375rem !important;
            overflow-wrap: anywhere;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table :is(th, td):first-child {
            width: 24% !important;
            white-space: normal !important;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table td:first-child > span {
            white-space: normal !important;
            overflow-wrap: anywhere;
        }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table col:nth-child(1) { width: 24% !important; }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table col:nth-child(2) { width: 11% !important; }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table col:nth-child(3) { width: 10% !important; }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table col:nth-child(4) { width: 19% !important; }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table col:nth-child(5) { width: 10% !important; }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table :is(col, th, td):last-child { width: 26% !important; }
        html body.portal-ui.barangay-cases-admin-mobile-nav #barangay-table .barangay-view-cases-button {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            padding-inline: .375rem !important;
            gap: .25rem !important;
            font-size: .75rem !important;
        }
    }

    /* Keep the Barangay column and its labels fixed across pagination pages. */
    #barangay-table {
        width: 100% !important;
        min-width: 760px !important;
        table-layout: fixed !important;
    }

    #barangay-table :is(th, td):first-child {
        width: 28% !important;
        text-align: left !important;
    }

    #barangay-table td:first-child {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    #barangay-table td:first-child > span {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .portal-ui .swal2-popup.barangay-cases-print-popup .swal2-validation-message.barangay-cases-empty-warning {
        margin-bottom: 1rem !important;
    }

    @media (min-width: 1024px) {
        /* Prevent page scrolling on desktop - only table should scroll */
        html, body { overflow: hidden !important; height: 100vh !important; }
        #mainContent { height: 100vh !important; overflow: hidden !important; }
    }

    /* Android tablets (600px–1280px): keep the content independent from the
       drawer and allow the data grid itself to scroll rather than squeezing. */
    @media (min-width: 37.5rem) and (max-width: 80rem) {
        html, body { height: auto !important; overflow: auto !important; }
        #mainContent { height: auto !important; overflow: visible !important; }
        #mainContent > main {
            height: auto !important;
            min-height: 0;
            overflow: visible !important;
            padding: clamp(1rem, 2vw, 1.5rem) !important;
        }
        #barangay-table { min-width: 47.5rem !important; }
        #barangay-table :is(th, td) {
            padding: .75rem clamp(.625rem, 1.25vw, 1rem) !important;
            font-size: clamp(.6875rem, 1.25vw, .8125rem);
        }
    }

    /* Barangay Cases tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
    @media (min-width: 48rem) and (max-width: 63.9375rem) {
        html body.portal-ui #mobileTopBar { display: none !important; }
        html body.portal-ui #mobileMenuToggle { display: none !important; }
        html body.portal-ui #dashboardMenuToggle { display: none !important; }
        html body.portal-ui #sidebarCloseBtn { display: none !important; }
        html body.portal-ui #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }
        html body.portal-ui #mainContent > header.sticky {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        html body.portal-ui #mainContent > header h2 {
            color: #1f2937 !important;
        }
        html body.portal-ui #mainContent > header p {
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
@endif

@if(auth()->user()?->isDepartmentAdmin())
<style>
    html body.portal-ui #mainContent .tablet-profile-pagination {
        background: #fff !important; padding: 1rem 1.5rem !important;
        flex-direction: row !important; flex-wrap: wrap !important;
        justify-content: space-between !important; gap: 1rem !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination #page-numbers { display: contents !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination #mobile-page-info { display: none !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination button {
        width: 2.25rem !important; min-width: 2.25rem !important; height: 2.25rem !important;
        min-height: 0 !important; padding: 0 !important; margin: 0 !important; border-radius: .5rem !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination > div:last-child { gap: .5rem !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination button:disabled { opacity: .5 !important; background: #fff !important; border-color: #d1d5db !important; }
    @media (max-width: 47.9375rem) {
        html body.portal-ui #mainContent .tablet-profile-pagination { gap: .75rem !important; padding: .75rem !important; text-align: left; }
        html body.portal-ui #mainContent .tablet-profile-pagination button { width: 2rem !important; min-width: 2rem !important; height: 2rem !important; font-size: .6875rem !important; }
        html body.portal-ui #mainContent .tablet-profile-pagination > div:last-child { gap: .25rem !important; }
    }
</style>
@endif
@endpush

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 85.375rem) {
        html body.portal-ui .admin-mobile-picker-field {
            display: block !important;
            position: relative;
            width: 100%;
            min-width: 0;
            max-width: 100%;
        }
        html body.portal-ui .admin-mobile-picker-field > :is(input[type=date], input[type=time]) {
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
        html body.portal-ui .admin-mobile-picker-field > .admin-mobile-picker-icon {
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
        html body.portal-ui .admin-mobile-picker-field > :is(input[type=date], input[type=time])::-webkit-calendar-picker-indicator {
            opacity: 0;
        }
        html body.portal-ui .admin-mobile-picker-field > :is(input[type=date], input[type=time])::-webkit-date-and-time-value {
            text-align: left;
            min-width: 0;
        }
    }
</style>
@endif
@endpush

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
<style>
@media (max-width: 47.9375rem) {
    html body.portal-ui #mainContent .tablet-profile-pagination {
        display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important;
        align-items: center !important; justify-content: space-between !important;
        width: 100% !important; min-width: 0 !important; box-sizing: border-box !important;
        padding: .75rem .625rem !important; gap: .5rem !important;
        background: #fff !important; border-top: 1px solid #f1f5f9 !important;
        border-radius: 0 0 .75rem .75rem !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination > div:first-child {
        flex: 1 1 auto !important; min-width: 0 !important; font-size: .625rem !important;
        line-height: 1.25 !important; color: #64748b !important; white-space: nowrap !important; text-align: left !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination > div:last-child {
        display: flex !important; flex: 0 0 auto !important; flex-wrap: nowrap !important;
        align-items: center !important; justify-content: flex-end !important; gap: .25rem !important;
        width: auto !important; margin: 0 !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination #page-numbers { display: contents !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination #mobile-page-info { display: none !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination button {
        flex: 0 0 1.875rem !important; width: 1.875rem !important; min-width: 1.875rem !important;
        height: 1.875rem !important; min-height: 1.875rem !important;
        padding: 0 !important; margin: 0 !important; border: 1px solid #e5e7eb !important;
        border-radius: .5rem !important; background: #fff !important; color: #C41E3A !important;
        font-size: .6875rem !important; box-shadow: 0 1px 3px rgba(15,23,42,.04) !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination button[class~="bg-[#C41E3A]"] {
        background: #C41E3A !important; border-color: #C41E3A !important; color: #fff !important;
    }
    html body.portal-ui #mainContent .tablet-profile-pagination button > i { color: inherit !important; font-size: .6875rem !important; }
    html body.portal-ui #mainContent .tablet-profile-pagination button:disabled { opacity: .4 !important; }
}
@media (max-width: 359px) {
    html body.portal-ui #mainContent .tablet-profile-pagination button {
        flex-basis: 1.625rem !important; width: 1.625rem !important; min-width: 1.625rem !important;
    }
}
</style>
@endif
@endpush

@section('content')

@php
    $isBcpcBarangayCasesViewer = auth()->user()->isBcpcAdmin()
        || (auth()->user()->position === 'Desk Officer' && auth()->user()->defaultCaseDepartment() === 'BCPC');
    $isSwdBarangayCasesViewer = auth()->user()->isVawcAdmin()
        || (auth()->user()->position === 'Desk Officer' && auth()->user()->defaultCaseDepartment() === 'VAWC');
    $usesFixedBarangayTable = $isBcpcBarangayCasesViewer || $isSwdBarangayCasesViewer;
@endphp

<!-- Top Header -->
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="barangay-cases-header-leading flex items-center gap-3 min-w-0">
            @if(auth()->user()->isDepartmentAdmin())
            <button id="dashboardMenuToggle" class="hidden" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            @endif
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Barangay Cases</h2>
                <p class="text-sm text-gray-500 {{ auth()->user()->isDepartmentAdmin() ? '' : 'hidden sm:block' }}">View and manage cases by barangay</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <!-- Notifications -->
            @include('admin-tailwind.components.notifications')
            
            <!-- User Menu -->
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
<main class="px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 lg:pt-8 pb-4 flex flex-col lg:h-[calc(100vh-80px)] lg:overflow-hidden min-h-0">
    <!-- Search and Filter Section -->
    <div class="flex flex-col gap-4 mb-6">
        <!-- Search Box -->
        <div class="flex-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input 
                type="text" 
                id="barangay-search" 
                placeholder="Search barangay..." 
                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors text-sm"
            >
        </div>
        
        <!-- Action Buttons -->
        <div class="flex gap-2 sm:gap-3 w-full">
            @if(auth()->user()->position === 'Desk Officer' && auth()->user()->defaultCaseDepartment() === 'BCPC' && auth()->user()->isMultiCaseDepartment())
            <a href="{{ route('desk-officer.cases.index', ['type' => 'bcpc']) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2.5 {{ strtolower(request('type', '')) === 'bcpc' ? 'bg-purple-700 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }} rounded-lg transition-colors text-xs sm:text-sm font-medium">
                <i class="fas fa-child"></i><span>BCPC Cases</span>
            </a>
            <a href="{{ route('desk-officer.cases.index', ['type' => 'vawc']) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2.5 {{ strtolower(request('type', '')) === 'vawc' ? 'bg-purple-700 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }} rounded-lg transition-colors text-xs sm:text-sm font-medium">
                <i class="fas fa-shield-alt"></i><span>SWD Cases</span>
            </a>
            @endif
            <!-- Print All Button -->
            <button 
                type="button" 
                id="printButton" 
                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium shadow-sm hover:shadow-md"
            >
                <i class="fas fa-print"></i>
                <span class="hidden xs:inline">Print All</span><span class="xs:hidden">Print</span>
            </button>
            
            <!-- Filter Button -->
            <div class="relative flex-1 sm:flex-none" x-data="{ open: false }">
                <button 
                    @click="open = !open"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-xs sm:text-sm font-medium"
                >
                    <i class="fas fa-filter text-gray-500"></i>
                    <span>Filter</span>
                </button>
                
                <!-- Filter Dropdown -->
                <div id="barangay-filter-popup"
                    @if(auth()->user()?->isDepartmentAdmin()) x-cloak @endif
                    x-show="open" 
                    @click.away="open = false"
                    x-transition
                    class="fixed sm:absolute left-4 right-4 sm:left-0 sm:right-auto mt-2 sm:w-72 bg-white rounded-xl shadow-lg border border-gray-100 z-50 p-4"
                >
                    <h4 class="font-semibold text-gray-800 mb-3">Filter Options</h4>
                    
                    <!-- Status Filter -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                            <option value="">All Status</option>
                            <option value="Case Referred">Case Referred</option>
                            <option value="Case Filed">Case Filed</option>
                            <option value="Case closed">Case Closed</option>
                        </select>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date Range</label>
                        <div class="space-y-2">
                            <div class="admin-mobile-picker-field" style="display: contents;"><input type="date" id="date-from" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm" placeholder="From"><svg class="admin-mobile-picker-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
                            <div class="admin-mobile-picker-field" style="display: contents;"><input type="date" id="date-to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm" placeholder="To"><svg class="admin-mobile-picker-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
                        </div>
                    </div>
                    
                    <!-- Filter Actions -->
                    <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                        <button id="reset-filters" class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                            Clear
                        </button>
                        <button id="apply-filters" class="px-3 py-1.5 text-sm bg-purple-700 text-white hover:bg-purple-800 rounded-lg transition-colors">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Sort Button -->
            <div class="relative flex-1 sm:flex-none" x-data="{ open: false }">
                <button 
                    @click="open = !open"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-xs sm:text-sm font-medium"
                >
                    <i class="fas fa-sort-amount-down text-gray-500"></i>
                    <span>Sort</span>
                </button>
                
                <!-- Sort Dropdown -->
                <div id="barangay-sort-popup"
                    @if(auth()->user()?->isDepartmentAdmin()) x-cloak @endif
                    x-show="open" 
                    @click.away="open = false"
                    x-transition
                    class="fixed sm:absolute left-4 right-4 sm:left-0 sm:right-auto mt-2 sm:w-56 bg-white rounded-xl shadow-lg border border-gray-100 z-50 p-4"
                >
                    <h4 class="font-semibold text-gray-800 mb-3">Sort By</h4>
                    
                    <select id="sort-by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm mb-3">
                        <option value="name">Barangay Name</option>
                        <option value="total_cases">Total Cases</option>
                        @if(auth()->user()->isVawcAdmin())
                        <option value="case_referred">Case Referred</option>
                        <option value="case_filed">Case Filed</option>
                        <option value="case_closed">Case Closed</option>
                        @else
                        <option value="active_cases">Active Cases</option>
                        <option value="resolved_cases">Resolved Cases</option>
                        @endif
                    </select>
                    
                    <div class="flex gap-2">
                        <button id="sort-asc" class="sort-btn flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition-colors text-sm" data-order="asc">
                            <i class="fas fa-arrow-up text-xs"></i>
                            Asc
                        </button>
                        <button id="sort-desc" class="sort-btn flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gray-100 hover:bg-purple-100 hover:text-purple-700 rounded-lg transition-colors text-sm" data-order="desc">
                            <i class="fas fa-arrow-down text-xs"></i>
                            Desc
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Barangay Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col flex-1 min-h-0">
        <!-- Desktop Table Container -->
        <div class="hidden md:flex flex-col flex-1 min-h-0">
            <div class="overflow-auto flex-1">
                <table id="barangay-table" class="w-full {{ $usesFixedBarangayTable ? 'table-fixed min-w-[760px]' : '' }}">
                    @if($usesFixedBarangayTable)
                    <colgroup>
                        <col class="w-[28%]">
                        <col class="w-[12%]">
                        <col class="w-[12%]">
                        <col class="w-[16%]">
                        <col class="w-[10%]">
                        <col class="w-[22%]">
                    </colgroup>
                    @endif
                    <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white sticky top-0">
                        <tr>
                            <th class="px-4 lg:px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Barangay</th>
                            <th class="px-4 lg:px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Referred</th>
                            <th class="px-4 lg:px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Filed</th>
                            <th class="px-4 lg:px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Complete Cases</th>
                            <th class="px-4 lg:px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Total</th>
                            <th class="px-4 lg:px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="barangay-table-body" class="divide-y divide-gray-100">
                        <!-- Loading State -->
                        <tr id="loading-row">
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin mb-3"></div>
                                    <p class="text-gray-500 text-sm">Loading barangay data...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Mobile Card View -->
        <div id="barangay-mobile-cards" class="md:hidden overflow-auto flex-1 p-4 space-y-3">
            <!-- Loading State -->
            <div id="mobile-loading" class="flex flex-col items-center justify-center py-12">
                <div class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin mb-3"></div>
                <p class="text-gray-500 text-sm">Loading barangay data...</p>
            </div>
        </div>
        
        <!-- Pagination Controls -->
        <div class="tablet-profile-pagination px-4 sm:px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between flex-shrink-0">
            <div class="text-xs sm:text-sm text-gray-600">
                <span id="pagination-info">Showing 0 - 0 of 0</span>
            </div>
            <div class="flex items-center gap-1 sm:gap-2">
                <button 
                    id="prev-page-btn" 
                    class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 bg-white border border-gray-300 hover:bg-gray-50 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed rounded-lg transition-colors text-sm"
                    disabled
                >
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
                <div id="page-numbers" class="hidden sm:flex items-center gap-1">
                    <!-- Page numbers will be inserted here -->
                </div>
                <span id="mobile-page-info" class="sm:hidden text-xs text-gray-600 px-2"></span>
                <button 
                    id="next-page-btn" 
                    class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 bg-white border border-gray-300 hover:bg-gray-50 disabled:bg-red-100 disabled:text-red-800 disabled:border-red-200 disabled:opacity-100 disabled:cursor-not-allowed rounded-lg transition-colors text-sm"
                    disabled
                >
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    window.suppressSuccessfulVawcReferralWarning = sessionStorage.getItem('successful-vawc-referral-return') === '1'
        || sessionStorage.getItem('referral-success-suppress') === '1'
        || new URLSearchParams(window.location.search).has('referral_success');
    if (window.suppressSuccessfulVawcReferralWarning) {
        sessionStorage.removeItem('successful-vawc-referral-return');
        sessionStorage.removeItem('referral-success-suppress');
        window.history.replaceState({}, document.title, '{{ route("admin-barangay-cases") }}');
    }
</script>
@if(!auth()->user()->isVawcAdmin() && request()->boolean('referral_unavailable') && !request()->boolean('referral_success') && !session('referral_success'))
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.suppressSuccessfulVawcReferralWarning) return;

    window.history.replaceState({}, document.title, '{{ route("admin-barangay-cases") }}');
    Swal.fire({
        icon: 'info',
        title: 'Referral Unavailable',
        text: 'This case has already been referred or completed.',
        confirmButtonColor: '#7c3aed'
    });
});
</script>
@endif
<script>
    window.BarangayShowUrlTemplate = "{{ route('admin.barangay-cases.show', ['barangay' => '__ID__']) }}";
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('barangay-table-body');
    const prevPageBtn = document.getElementById('prev-page-btn');
    const nextPageBtn = document.getElementById('next-page-btn');
    const pageNumbersContainer = document.getElementById('page-numbers');
    const paginationInfo = document.getElementById('pagination-info');
    const searchInput = document.getElementById('barangay-search');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const sortBySelect = document.getElementById('sort-by');
    const sortButtons = document.querySelectorAll('.sort-btn');
    const printButton = document.getElementById('printButton');
    
    // Pagination and filter variables
    const itemsPerPage = {{ ($isBcpcBarangayCasesViewer || $isSwdBarangayCasesViewer) ? 5 : 10 }};
    let currentPage = 1;
    let allBarangays = [];
    let filteredBarangays = [];
    @if(auth()->user()?->isDepartmentAdmin())
    matchMedia('(min-width: 48rem) and (max-width: 85.375rem)').addEventListener('change', () => renderPageNumbers(Math.ceil(filteredBarangays.length / itemsPerPage)));
    @endif
    let currentFilters = { status: '', dateFrom: '', dateTo: '' };
    let currentSort = { field: 'name', order: 'asc' };
    const isSwdAdmin = @json(auth()->user()->isVawcAdmin());
    const isBcpcAdmin = @json($isBcpcBarangayCasesViewer);
    
    // Print button handler
    if (printButton) {
        printButton.addEventListener('click', async function() {
            let printableBarangays = [];
            try {
                const response = await fetch('/api/barangays', { credentials: 'same-origin' });
                printableBarangays = await response.json();
            } catch (error) {
                return;
            }

            const barangayOptions = printableBarangays.map(barangay =>
                `<option value="${barangay.id}">${barangay.name}</option>`
            ).join('');

            if (!barangayOptions) {
                return;
            }

            // Build date range message if filters are applied
            let filterMessage = '';
            let dateRangeText = '';
            
            if (currentFilters.dateFrom || currentFilters.dateTo) {
                if (currentFilters.dateFrom && currentFilters.dateTo) {
                    const fromDate = new Date(currentFilters.dateFrom).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    const toDate = new Date(currentFilters.dateTo).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    dateRangeText = `${fromDate} to ${toDate}`;
                    filterMessage = `<p class="text-purple-600 text-sm mt-2 font-medium"><i class="fas fa-calendar-alt mr-1"></i>Date Range: ${dateRangeText}</p>`;
                } else if (currentFilters.dateFrom) {
                    const fromDate = new Date(currentFilters.dateFrom).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    dateRangeText = `From ${fromDate}`;
                    filterMessage = `<p class="text-purple-600 text-sm mt-2 font-medium"><i class="fas fa-calendar-alt mr-1"></i>${dateRangeText}</p>`;
                } else if (currentFilters.dateTo) {
                    const toDate = new Date(currentFilters.dateTo).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    dateRangeText = `Until ${toDate}`;
                    filterMessage = `<p class="text-purple-600 text-sm mt-2 font-medium"><i class="fas fa-calendar-alt mr-1"></i>${dateRangeText}</p>`;
                }
            }
            
            const printMessage = (currentFilters.dateFrom || currentFilters.dateTo) 
                ? 'Print cases for the selected date range?' 
                : 'Are you sure you want to print all cases?';
            
            Swal.fire({
                title: 'Print Cases',
                html: `
                    <div class="text-center py-4">
                        <i class="fas fa-print text-5xl text-purple-500 mb-4"></i>
                        <p class="text-gray-600">${printMessage}</p>
                        ${filterMessage}
                        <select id="print-barangay" class="w-full mt-4 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Select barangay</option>
                            ${barangayOptions}
                        </select>
                        <p class="text-gray-400 text-sm mt-2">This will open a print preview.</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Print',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                @if(auth()->user()->isDepartmentAdmin())
                customClass: { popup: 'barangay-cases-print-popup' },
                didOpen: () => {
                    const select = document.getElementById('print-barangay');
                    const wrapper = document.createElement('div');
                    wrapper.className = 'mt-4 text-left';
                    const toggle = document.createElement('button');
                    toggle.type = 'button';
                    toggle.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg text-sm flex items-center justify-between bg-white';
                    toggle.setAttribute('aria-haspopup', 'listbox');
                    toggle.setAttribute('aria-expanded', 'false');
                    const label = document.createElement('span');
                    label.textContent = select.options[select.selectedIndex].textContent;
                    toggle.append(label);
                    const arrow = document.createElement('i');
                    arrow.className = 'fas fa-chevron-down';
                    arrow.setAttribute('aria-hidden', 'true');
                    toggle.append(arrow);
                    const menu = document.createElement('div');
                    menu.id = 'print-barangay-options';
                    menu.setAttribute('role', 'listbox');
                    menu.setAttribute('aria-label', 'Select barangay');
                    menu.className = 'mt-1 w-full border border-gray-300 rounded-lg bg-white overflow-y-auto';
                    menu.style.maxHeight = '12rem';
                    menu.hidden = true;
                    toggle.setAttribute('aria-controls', menu.id);
                    const close = () => { menu.hidden = true; toggle.setAttribute('aria-expanded', 'false'); };
                    Array.from(select.options).forEach(option => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'block w-full px-3 py-2 text-left text-sm hover:bg-gray-100';
                        item.setAttribute('role', 'option');
                        item.setAttribute('aria-selected', String(option.selected));
                        item.textContent = option.textContent;
                        item.addEventListener('click', () => {
                            select.value = option.value;
                            label.textContent = option.textContent;
                            menu.querySelectorAll('[role="option"]').forEach(node => node.setAttribute('aria-selected', String(node === item)));
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                            close();
                            toggle.focus();
                        });
                        menu.append(item);
                    });
                    toggle.addEventListener('click', () => { menu.hidden = !menu.hidden; toggle.setAttribute('aria-expanded', String(!menu.hidden)); });
                    wrapper.addEventListener('keydown', event => {
                        if (event.key === 'Escape') { event.stopPropagation(); close(); toggle.focus(); }
                        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                            event.preventDefault();
                            menu.hidden = false;
                            toggle.setAttribute('aria-expanded', 'true');
                            const items = Array.from(menu.children);
                            const index = items.indexOf(document.activeElement);
                            items[(index + (event.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length].focus();
                        }
                    });
                    wrapper.addEventListener('focusout', event => { if (!wrapper.contains(event.relatedTarget)) close(); });
                    select.hidden = true;
                    select.after(wrapper);
                    wrapper.append(toggle, menu);
                },
                @endif
                preConfirm: async () => {
                    const barangayId = document.getElementById('print-barangay').value;

                    if (!barangayId) {
                        Swal.showValidationMessage('Please select a barangay.');
                        return false;
                    }

                    const params = new URLSearchParams();
                    params.set('back', 'barangay-list');
                    const selectedDepartment = new URLSearchParams(window.location.search).get('type');
                    if (selectedDepartment) params.append('type', selectedDepartment);
                    if (currentFilters.dateFrom) params.append('date_from', currentFilters.dateFrom);
                    if (currentFilters.dateTo) params.append('date_to', currentFilters.dateTo);
                    if (currentFilters.status) params.append('status', currentFilters.status);

                    try {
                        const response = await fetch(`/api/barangay-cases/${encodeURIComponent(barangayId)}?${params.toString()}`, { credentials: 'same-origin' });
                        const data = await response.json();
                        const printableStatuses = ['Case Referred', 'Case Filed', 'Case closed', 'Case Closed', 'Complete case', 'Complete Case'];
                        if (!data.success || !data.cases.some(caseItem => printableStatuses.includes(caseItem.status))) {
                            Swal.showValidationMessage('Printing cannot proceed because this barangay has no referred, filed, or complete cases.');
                            document.querySelector('.swal2-popup.barangay-cases-print-popup .swal2-validation-message')?.classList.add('barangay-cases-empty-warning');
                            return false;
                        }
                    } catch (error) {
                        Swal.showValidationMessage('Unable to check the selected barangay cases. Please try again.');
                        return false;
                    }

                    return barangayId;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Build selected barangay print URL with filters
                    let printUrl = '{{ route("admin.barangay-cases.print-individual", ["barangay" => "__ID__"]) }}'
                        .replace('__ID__', encodeURIComponent(result.value));
                    const params = new URLSearchParams();
                    const selectedDepartment = new URLSearchParams(window.location.search).get('type');
                    if (selectedDepartment) params.append('type', selectedDepartment);
                    
                    if (currentFilters.dateFrom) {
                        params.append('date_from', currentFilters.dateFrom);
                    }
                    if (currentFilters.dateTo) {
                        params.append('date_to', currentFilters.dateTo);
                    }
                    if (currentFilters.status) {
                        params.append('status', currentFilters.status);
                    }
                    
                    if (params.toString()) {
                        printUrl += '?' + params.toString();
                    }
                    
                    window.location.assign(printUrl);
                }
            });
        });
    }
    
    // Apply filters
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            currentFilters = {
                status: document.getElementById('status-filter').value,
                dateFrom: document.getElementById('date-from').value,
                dateTo: document.getElementById('date-to').value
            };
            // Reload data from API with filters
            loadBarangayData();
        });
    }
    
    // Reset filters
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            document.getElementById('status-filter').value = '';
            document.getElementById('date-from').value = '';
            document.getElementById('date-to').value = '';
            currentFilters = { status: '', dateFrom: '', dateTo: '' };
            // Reload data from API without filters
            loadBarangayData();
        });
    }
    
    // Sort handlers
    if (sortBySelect) {
        sortBySelect.addEventListener('change', function() {
            currentSort.field = this.value;
            sortBarangays();
        });
    }
    
    sortButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentSort.order = this.getAttribute('data-order');
            sortBarangays();
            sortButtons.forEach(btn => btn.classList.remove('bg-purple-100', 'text-purple-700'));
            this.classList.add('bg-purple-100', 'text-purple-700');
        });
    });
    
    // Search functionality
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            filterBarangays();
        });
    }
    
    // Pagination handlers
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderBarangays();
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', function() {
            const totalPages = Math.ceil(filteredBarangays.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderBarangays();
            }
        });
    }
    
    function filterBarangays() {
        // Apply search filter locally on loaded data
        filteredBarangays = [...allBarangays];
        currentPage = 1;
        applySearch();
        sortBarangays();
    }
    
    function applySearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        if (searchTerm) {
            filteredBarangays = filteredBarangays.filter(item => 
                item.name.toLowerCase().includes(searchTerm)
            );
        } else {
            filteredBarangays = [...allBarangays];
        }
    }
    
    function sortBarangays() {
        filteredBarangays.sort((a, b) => {
            let valA, valB;
            
            switch (currentSort.field) {
                case 'total_cases':
                    valA = a.total_cases || 0;
                    valB = b.total_cases || 0;
                    break;
                case 'case_referred':
                    valA = a.case_referred || 0;
                    valB = b.case_referred || 0;
                    break;
                case 'case_filed':
                    valA = a.case_filed || 0;
                    valB = b.case_filed || 0;
                    break;
                case 'case_closed':
                    valA = a.case_closed || 0;
                    valB = b.case_closed || 0;
                    break;
                case 'active_cases':
                    valA = a.active_cases || 0;
                    valB = b.active_cases || 0;
                    break;
                case 'resolved_cases':
                    valA = a.resolved_cases || 0;
                    valB = b.resolved_cases || 0;
                    break;
                default:
                    valA = a.name.toLowerCase();
                    valB = b.name.toLowerCase();
            }
            
            if (currentSort.order === 'asc') {
                return valA > valB ? 1 : valA < valB ? -1 : 0;
            } else {
                return valA < valB ? 1 : valA > valB ? -1 : 0;
            }
        });
        
        renderBarangays();
    }
    
    function renderBarangays() {
        const totalItems = filteredBarangays.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
        const paginatedItems = filteredBarangays.slice(startIndex, endIndex);
        
        const mobileCardsContainer = document.getElementById('barangay-mobile-cards');
        const mobilePageInfo = document.getElementById('mobile-page-info');
        
        tableBody.innerHTML = '';
        if (mobileCardsContainer) {
            mobileCardsContainer.innerHTML = '';
        }
        
        // Update pagination info
        if (totalItems > 0) {
            paginationInfo.textContent = `Showing ${startIndex + 1} - ${endIndex} of ${totalItems}`;
            if (mobilePageInfo) mobilePageInfo.textContent = `${currentPage}/${totalPages}`;
        } else {
            paginationInfo.textContent = 'No barangays found';
            if (mobilePageInfo) mobilePageInfo.textContent = '';
        }
        
        // Update pagination buttons
        prevPageBtn.disabled = currentPage <= 1;
        nextPageBtn.disabled = currentPage >= totalPages;
        
        // Render page numbers
        renderPageNumbers(totalPages);
        
        if (paginatedItems.length === 0) {
            // Desktop empty state
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                            <p class="text-sm">No barangays found</p>
                        </div>
                    </td>
                </tr>
            `;
            // Mobile empty state
            if (mobileCardsContainer) {
                mobileCardsContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p class="text-sm">No barangays found</p>
                    </div>
                `;
            }
            return;
        }
        
        paginatedItems.forEach(item => {
            // Desktop Table Row
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors cursor-pointer';
            
            row.innerHTML = `
                <td class="barangay-name-cell px-4 lg:px-6 py-4">
                    <span class="font-semibold text-purple-800">${item.name}</span>
                </td>
                <td class="px-4 lg:px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold">
                        ${item.case_referred || 0}
                    </span>
                </td>
                <td class="px-4 lg:px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                        ${item.case_filed || 0}
                    </span>
                </td>
                <td class="px-4 lg:px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-semibold">
                        ${item.case_closed || 0}
                    </span>
                </td>
                <td class="px-4 lg:px-6 py-4 text-center">
                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-bold">
                        ${item.total_cases || 0}
                    </span>
                </td>
                <td class="px-4 lg:px-6 py-4 text-center">
                    <button 
                        class="btn-view barangay-view-cases-button inline-flex h-10 items-center justify-center gap-2 px-4 bg-[#C41E3A] hover:bg-[#A01830] active:bg-[#8B1429] text-white rounded-lg transition-colors text-sm font-medium whitespace-nowrap"
                        style="min-width: 148px;"
                        data-barangay-id="${item.id}"
                        data-icon-action="labelled"
                    >
                        <i class="fas fa-eye w-4 text-center"></i>
                        <span>View Cases</span>
                    </button>
                </td>
            `;
            
            // View button click handler
            const viewBtn = row.querySelector('.btn-view');
            viewBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const barangayId = item.id;
                const tmpl = window.BarangayShowUrlTemplate || '/admin-barangay-cases/__ID__';
                const url = tmpl.replace('__ID__', encodeURIComponent(barangayId));
                const type = new URLSearchParams(window.location.search).get('type');
                window.location.href = type ? `${url}?type=${encodeURIComponent(type)}` : url;
            });
            
            tableBody.appendChild(row);
            
            // Mobile Card
            if (mobileCardsContainer) {
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg border border-gray-200 p-4 shadow-sm';
                card.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-purple-800">${item.name}</h3>
                        <span class="inline-flex items-center px-2.5 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-bold">
                            ${item.total_cases || 0} cases
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div class="text-center p-2 bg-orange-50 rounded-lg">
                            <p class="text-lg font-semibold text-orange-700">${item.case_referred || 0}</p>
                            <p class="text-xs text-orange-600">Referred</p>
                        </div>
                        <div class="text-center p-2 bg-green-50 rounded-lg">
                            <p class="text-lg font-semibold text-green-700">${item.case_filed || 0}</p>
                            <p class="text-xs text-green-600">Filed</p>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <p class="text-lg font-semibold text-gray-700">${item.case_closed || 0}</p>
                            <p class="text-xs text-gray-600">Complete Cases</p>
                        </div>
                    </div>
                    <button 
                        class="btn-view-mobile w-full inline-flex h-10 items-center justify-center gap-2 px-4 bg-[#C41E3A] hover:bg-[#A01830] active:bg-[#8B1429] text-white rounded-lg transition-colors text-sm font-medium"
                        data-barangay-id="${item.id}"
                    >
                        <i class="fas fa-eye w-4 text-center"></i>
                        View Cases
                    </button>
                `;
                
                // Mobile view button click handler
                const mobileViewBtn = card.querySelector('.btn-view-mobile');
                mobileViewBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const barangayId = item.id;
                    const tmpl = window.BarangayShowUrlTemplate || '/admin-barangay-cases/__ID__';
                    const url = tmpl.replace('__ID__', encodeURIComponent(barangayId));
                    const type = new URLSearchParams(window.location.search).get('type');
                    window.location.href = type ? `${url}?type=${encodeURIComponent(type)}` : url;
                });
                
                mobileCardsContainer.appendChild(card);
            }
        });
    }
    
    function renderPageNumbers(totalPages) {
        pageNumbersContainer.innerHTML = '';
        
        if (totalPages <= 1) return;

        if (@json(auth()->user()?->isDepartmentAdmin())) {
            const first = Math.max(1, Math.min(currentPage - 1, totalPages - 3));
            for (let page = first; page <= Math.min(totalPages, first + 3); page++) pageNumbersContainer.appendChild(createPageButton(page));
            return;
        }
        
        // Determine which page numbers to show
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        
        // Adjust if we're near the start or end
        if (currentPage <= 3) {
            endPage = Math.min(5, totalPages);
        }
        if (currentPage >= totalPages - 2) {
            startPage = Math.max(1, totalPages - 4);
        }
        
        // First page
        if (startPage > 1) {
            pageNumbersContainer.appendChild(createPageButton(1));
            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-gray-400';
                ellipsis.textContent = '...';
                pageNumbersContainer.appendChild(ellipsis);
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            pageNumbersContainer.appendChild(createPageButton(i));
        }
        
        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'px-2 text-gray-400';
                ellipsis.textContent = '...';
                pageNumbersContainer.appendChild(ellipsis);
            }
            pageNumbersContainer.appendChild(createPageButton(totalPages));
        }
    }
    
    function createPageButton(pageNum) {
        const btn = document.createElement('button');
        btn.className = pageNum === currentPage 
            ? 'inline-flex items-center justify-center w-9 h-9 bg-[#C41E3A] text-white border border-[#C41E3A] rounded-lg text-sm font-medium'
            : 'inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm text-gray-700';
        btn.textContent = pageNum;
        btn.addEventListener('click', function() {
            currentPage = pageNum;
            renderBarangays();
        });
        return btn;
    }
    
    // Load initial data
    function loadBarangayData() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin mb-3"></div>
                        <p class="text-gray-500 text-sm">Loading barangay data...</p>
                    </div>
                </td>
            </tr>
        `;
        
        // Build URL with filter parameters
        let url = '/api/barangay-cases';
        const params = new URLSearchParams();
        const selectedDepartment = new URLSearchParams(window.location.search).get('type');
        if (selectedDepartment) {
            params.append('type', selectedDepartment);
        }
        
        if (currentFilters.status) {
            params.append('status', currentFilters.status);
        }
        if (currentFilters.dateFrom) {
            params.append('date_from', currentFilters.dateFrom);
        }
        if (currentFilters.dateTo) {
            params.append('date_to', currentFilters.dateTo);
        }
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                allBarangays = data;
                filteredBarangays = [...allBarangays];
                if (isSwdAdmin || isBcpcAdmin) {
                    filterBarangays();
                } else {
                    sortBarangays();
                }
            })
            .catch(error => {
                console.error('Error loading barangay data:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-red-500">
                                <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                                <p class="text-sm mb-3">Failed to load barangay data</p>
                                <button onclick="location.reload()" class="px-4 py-2 bg-purple-700 text-white rounded-lg text-sm hover:bg-purple-800 transition-colors">
                                    <i class="fas fa-sync-alt mr-2"></i>Retry
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
    }
    
    // Initialize
    loadBarangayData();
});
</script>
@endpush

@endsection
