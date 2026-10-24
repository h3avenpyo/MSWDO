@extends(auth()->user()?->position === 'Desk Officer' ? 'layouts.desk-officer' : 'layouts.admin-tailwind')

@section('title', 'Cases - ' . ($barangay->name ?? 'N/A') . ' - iSERVE SILANG Admin')

@php
$isSwdDeskOfficer = auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'VAWC'
&& !auth()->user()?->isMultiCaseDepartment();
$isBarangayDeskOfficer = auth()->user()?->position === 'Desk Officer'
&& in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)
&& !auth()->user()?->isMultiCaseDepartment();
$isMultiroleDeskOfficer = auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment();
@endphp

@push('styles')
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-pagination {
            padding: 0 !important;
            background: #fff !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-pagination > .barangay-legacy-pagination { display: none !important; }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-pagination > .barangay-tablet-pagination { display: block !important; }
        html body.portal-ui.view-cases-admin-mobile-nav .barangay-tablet-pagination > [data-pagination-label] {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            border-top: 0;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .barangay-tablet-pagination > [data-pagination-label] > div:last-child { justify-content: flex-end; flex-wrap: wrap; margin-left: auto; }
        html body.portal-ui.view-cases-admin-mobile-nav .barangay-tablet-pagination a { min-width: 2.25rem !important; min-height: 2.25rem !important; height: 2.25rem !important; }
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        body.portal-ui.view-cases-admin-mobile-nav :is(#caseModal, #editCaseModal) > .fixed {
            z-index: 100 !important;
        }
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-main { display: flex !important; flex-direction: column !important; }
        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-main > .view-cases-statistics {
            order: -2;
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: .5rem !important;
            width: 100%;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-main > .view-cases-statistics > div { min-width: 0; padding: .75rem !important; }
        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-main .view-cases-statistics > div > div { gap: .375rem !important; }
        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-main .view-cases-statistics p:first-child { font-size: .625rem !important; line-height: 1.3; overflow-wrap: anywhere; }
        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-main > .view-cases-toolbar {
            order: -1;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important;
            width: 100%;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-main .view-cases-toolbar-actions { width: auto !important; margin-left: auto; flex-wrap: nowrap !important; }
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<script>
(() => {
    const media = matchMedia('(min-width: 48rem) and (max-width: 85.375rem)');
    const sync = () => {
        const url = new URL(location.href);
        if (media.matches === (url.searchParams.get('tablet_cases') === '1')) return;
        if (media.matches) url.searchParams.set('tablet_cases', '1');
        else url.searchParams.delete('tablet_cases');
        url.searchParams.delete('page');
        location.replace(url.href);
    };
    sync();
    media.addEventListener('change', sync);
})();
</script>
<style>
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-table-desktop { display: none !important; }
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-table-cards {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 1rem !important;
            max-height: none !important;
            overflow: visible !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-table-cards .case-card {
            width: 100% !important;
            min-width: 0 !important;
            margin: 0 !important;
            box-sizing: border-box;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-table-cards > .view-cases-empty-state {
            grid-column: 1 / -1;
            width: 100%;
            text-align: center;
        }
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 47.9375rem) {
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards .case-card {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0 .5rem;
            padding: .75rem !important;
            align-items: end;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards .case-card > :is(div:first-child, p) { grid-column: 1 / -1; }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards .case-card > span {
            grid-column: 1;
            grid-row: 4;
            justify-self: start;
            align-self: center;
            margin-top: .5rem !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards .case-card-actions {
            grid-column: 2;
            grid-row: 4;
            align-self: center;
            display: flex !important;
            justify-content: flex-end !important;
            flex-wrap: nowrap !important;
            gap: .5rem !important;
            margin: .5rem 0 0 !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards .case-card-actions :is(button, a, form, form > button) {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex: 0 0 2.25rem !important;
            width: 2.25rem !important;
            min-width: 2.25rem !important;
            height: 2.25rem !important;
            min-height: 2.25rem !important;
            padding: 0 !important;
            border-radius: .5rem !important;
        }
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 47.9375rem) {
        html body.portal-ui.view-cases-admin-mobile-nav #view-cases-filter-panel {
            position: fixed !important;
            top: max(1rem, env(safe-area-inset-top)) !important;
            left: 50% !important;
            right: auto !important;
            width: min(17rem, calc(100vw - 2rem - env(safe-area-inset-left) - env(safe-area-inset-right))) !important;
            max-width: 100% !important;
            transform: translateX(-50%) !important;
            max-height: calc(100dvh - max(1rem, env(safe-area-inset-top)) - max(1rem, env(safe-area-inset-bottom)));
            box-sizing: border-box;
            margin-top: 0 !important;
            padding: .75rem !important;
            overflow-y: auto;
            z-index: 60 !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #view-cases-filter-panel h4 { font-size: .875rem; margin-bottom: .5rem; }
        html body.portal-ui.view-cases-admin-mobile-nav #view-cases-filter-panel form > .mb-4 { margin-bottom: .625rem; }
        html body.portal-ui.view-cases-admin-mobile-nav #view-cases-filter-panel :is(label, input, select, button) { font-size: .75rem !important; }
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (max-width: 47.9375rem) {
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent > #barangay-cases-view-header .view-cases-header-leading :is(h2, p) { color: #fff !important; }
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-toolbar {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: .5rem !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-toolbar > a {
            width: auto !important;
            flex: 0 1 auto !important;
            margin-right: auto !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-toolbar-actions {
            width: auto !important;
            flex: 0 0 auto !important;
            margin: 0 !important;
            flex-wrap: nowrap !important;
        }
    }
</style>
@endif
@if(auth()->user()?->position !== 'Desk Officer')
<style>
    @media (min-width: 48rem) {
        body.portal-ui.view-cases-admin-mobile-nav #mobileTopBar {
            display: none !important;
        }
    }

    @media (min-width: 48rem) and (max-width: 80rem) {
        body.portal-ui.view-cases-admin-mobile-nav #sidebar {
            inset: 0 auto 0 0 !important;
            height: 100dvh !important;
        }

        body.portal-ui.view-cases-admin-mobile-nav #sidebarToggleBtn {
            top: 1.5rem !important;
        }
    }

    @media (max-width: 47.9375rem) {
        html body.portal-ui.view-cases-admin-mobile-nav #mobileTopBar {
            display: none !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent>header#barangay-cases-view-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 30 !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-header>div {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-top: max(1rem, env(safe-area-inset-top)) !important;
            padding-bottom: 1rem !important;
            padding-left: max(.75rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .tablet-header-row {
            display: flex !important;
            align-items: center !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent #mobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-header-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: .625rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
            margin: 0 !important;
            align-self: center !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent .view-cases-header-leading>div {
            width: auto !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            margin: 0 !important;
            align-self: center !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #dashboardMenuToggle {
            position: relative !important;
            left: 0 !important;
            margin: 0 !important;
            transform: none !important;
            display: inline-flex !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
            padding: 0 !important;
            line-height: 1 !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #dashboardMenuToggle i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.25rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
            vertical-align: middle !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-header h2 {
            color: #ffffff !important;
            margin: 0 !important;
            line-height: 1.25 !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-header p {
            color: rgba(255, 255, 255, .85) !important;
            margin: 0 !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-header>div>div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
            display: flex !important;
            align-items: center !important;
            align-self: center !important;
            margin: 0 !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #barangay-cases-view-header>div>div:last-child>div.relative {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            align-self: center !important;
        }

        /* Notifications bell button matching admin/dashboard */
        html body.portal-ui.view-cases-admin-mobile-nav #mainContent>header#barangay-cases-view-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
            background-color: #A01830 !important;
            color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: var(--mswdo-profile, #BC0202) !important;
            border: 1px solid #8f0202 !important;
            box-shadow: 0 2px 5px rgba(188, 2, 2, .18) !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            position: relative !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent>header#barangay-cases-view-header button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent>header#barangay-cases-view-header button[aria-label="Notifications"]>i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 1.05rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            vertical-align: middle !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent>header#barangay-cases-view-header :is(#notifications-unread-count, [data-unread-badge], .notification-badge) {
            position: absolute !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem !important;
            top: -0.25rem !important;
            right: -0.25rem !important;
            font-size: 0.75rem !important;
            line-height: 1 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 9999px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
            border: 2px solid #C41E3A !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav .notification-dropdown {
            position: fixed !important;
            top: calc(max(1rem, env(safe-area-inset-top)) + 3.75rem) !important;
            left: 1rem !important;
            right: 1rem !important;
            width: auto !important;
            max-width: calc(100vw - 2rem) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important;
            border-radius: 0.875rem !important;
            z-index: 1000 !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar {
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
            box-shadow: 0 14px 28px rgba(0, 0, 0, .22) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-8px) !important;
            transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
            background: #C41E3A !important;
            background-image: none !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar nav :is(a, button),
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #fff !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #fff !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar nav :is(a, button):hover,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #fff !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-link:is([class~="bg-purple-600"], [class~="bg-purple-700/70"]),
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar nav [x-show]> :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #fff !important;
            box-shadow: inset 3px 0 0 #fff !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #sidebar #sidebarCloseBtn,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebarToggleBtn {
            display: none !important;
        }

        html body.portal-ui.view-cases-admin-mobile-nav #mainContent {
            width: 100% !important;
            min-width: 0 !important;
            margin-left: 0 !important;
        }
    }

    /* Prevent page scrolling - only table should scroll */
    html,
    body {
        overflow: hidden !important;
        height: 100vh !important;
    }

    #mainContent {
        height: 100vh !important;
        overflow: hidden !important;
    }

    [x-cloak] {
        display: none !important;
    }

    /* Phone and tablet views use cards; the table is desktop-only. */
    @media (max-width: 80rem) {
        .view-cases-table-desktop {
            display: none !important;
        }

        .view-cases-table-cards {
            display: block !important;
        }
    }

    @media (min-width: 80.0625rem) {
        .view-cases-table-desktop {
            display: block !important;
        }

        .view-cases-table-cards {
            display: none !important;
        }
    }

    /* Keep case content in a single fluid column while the shared sidebar is
       an off-canvas drawer on Android tablets (600px–1280px). */
    @media (min-width: 37.5rem) and (max-width: 80rem) {

        html,
        body {
            height: auto !important;
            overflow: auto !important;
        }

        #mainContent {
            height: auto !important;
            overflow: visible !important;
        }

        #mainContent>main {
            height: auto !important;
            min-height: 0;
            overflow: visible !important;
            padding: clamp(1rem, 2vw, 1.5rem) !important;
        }

        .view-cases-toolbar {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: .75rem !important;
        }

        .view-cases-toolbar>.relative {
            flex: 1 1 220px;
            min-width: 0;
        }

        .view-cases-toolbar-actions {
            margin-left: auto !important;
            flex-wrap: wrap !important;
            width: auto !important;
        }

        .view-cases-table-desktop table {
            min-width: 52rem;
        }

        .view-cases-table-desktop :is(th, td) {
            padding: .75rem clamp(.625rem, 1.2vw, 1rem) !important;
            font-size: clamp(.6875rem, 1.2vw, .8125rem);
        }

        .view-cases-table-cards .case-card {
            display: flex;
            flex-direction: column;
            min-height: 13rem;
        }

        .view-cases-table-cards .case-card>span {
            order: 5;
            align-self: flex-start;
            margin-top: .5rem;
        }

        .view-cases-table-cards .case-card-actions {
            order: 6;
            display: grid;
            grid-template-columns: repeat(3, 2.5rem);
            justify-content: end;
            gap: .5rem;
            margin-top: auto !important;
            margin-bottom: 0 !important;
        }

        .view-cases-table-cards .case-card-actions :is(button, form, form > button) {
            width: 2.5rem;
            min-width: 2.5rem;
            height: 2.5rem;
            min-height: 2.5rem;
            padding: 0 !important;
        }
    }

    /* Phone-first case view: keep the existing card presentation within the
       viewport while allowing the document to scroll naturally. */
    @media (max-width: 37.4375rem),
    (max-height: 30rem) and (max-width: 58rem) {

        html,
        body {
            height: auto !important;
            min-height: 100%;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        #mainContent {
            height: auto !important;
            min-height: 100%;
            overflow: visible !important;
        }

        #barangay-cases-view-header>div {
            padding-top: max(.75rem, env(safe-area-inset-top)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
            padding-left: max(1rem, env(safe-area-inset-left)) !important;
        }

        #barangay-cases-view-header h2 {
            font-size: clamp(1.125rem, 5vw, 1.375rem);
            line-height: 1.25;
        }

        #barangay-cases-view-main {
            height: auto !important;
            min-height: 0;
            overflow: visible !important;
            padding: 1rem max(1rem, env(safe-area-inset-right)) calc(1rem + env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left)) !important;
        }

        #barangay-cases-view-main :is(button, input, select) {
            min-height: 3rem;
        }

        .view-cases-toolbar {
            display: grid !important;
            grid-template-columns: minmax(0, 9rem) auto;
            align-items: stretch;
            gap: .5rem !important;
            justify-content: space-between;
        }

        .view-cases-toolbar>a {
            min-width: 0;
            min-height: 2.75rem;
            padding: .5rem .75rem !important;
            font-size: .75rem !important;
            justify-content: center;
            text-align: center;
            white-space: normal;
        }

        .view-cases-toolbar-actions {
            width: auto !important;
            min-width: 0;
            gap: .5rem !important;
        }

        .view-cases-toolbar-actions> :is(button, .relative > button) {
            min-height: 3rem;
        }

        .view-cases-statistics {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: .75rem !important;
        }

        .view-cases-statistics>div {
            min-width: 0;
            padding: .875rem !important;
        }

        .view-cases-statistics>div>div {
            min-width: 0;
            gap: .5rem !important;
        }

        .view-cases-statistics p:first-child {
            font-size: .6875rem;
            overflow-wrap: anywhere;
        }

        .view-cases-list {
            flex: none !important;
            min-height: 0;
            overflow: visible !important;
        }

        .view-cases-list>div:first-child {
            padding: .875rem 1rem !important;
        }

        .view-cases-table-cards {
            overflow: visible !important;
            padding: .75rem !important;
        }

        .view-cases-table-cards .case-card {
            min-width: 0;
            padding: 1rem !important;
            overflow: hidden;
        }

        .view-cases-table-cards .case-card>div:first-child {
            flex-wrap: wrap;
        }

        .view-cases-table-cards .case-card>div:first-child>div {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .view-cases-table-cards .case-card-actions {
            min-height: 3rem;
            gap: .5rem !important;
            flex-wrap: nowrap !important;
        }

        .view-cases-table-cards .case-card-actions :is(button, a, form > button) {
            width: 3rem !important;
            min-width: 3rem !important;
            height: 3rem !important;
            min-height: 3rem !important;
            padding: 0 !important;
        }

        .view-cases-pagination {
            padding: 1rem !important;
        }

        .view-cases-pagination>div {
            gap: .75rem !important;
        }

        .view-cases-pagination>div>p {
            text-align: center;
        }

        .view-cases-pagination>div>div {
            flex-wrap: wrap;
            justify-content: center;
        }

        .view-cases-pagination :is(a, span) {
            min-width: 3rem;
            min-height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    }


    /* Tablet view (768px–1439px) for department admins: keep summary cards and
       case cards within a fluid single-column layout. */
    @media (min-width: 48rem) and (max-width: 89.9375rem) {

        html,
        body {
            height: auto !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        #mainContent {
            height: auto !important;
            overflow: visible !important;
        }

        #barangay-cases-view-main {
            height: auto !important;
            overflow: visible !important;
            padding-left: max(1rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
            padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom))) !important;
        }

        .view-cases-table-desktop {
            display: none !important;
        }

        .view-cases-table-cards {
            display: flex !important;
        }

        display: flex !important;
        flex-direction: column !important;
        flex: none !important;
        gap: .75rem !important;
        overflow: visible !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
        padding: .75rem !important;
    }

    .view-cases-table-cards .case-card {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        padding: 1rem !important;
        margin: 0 !important;
    }

    .view-cases-table-cards .case-card * {
        box-sizing: border-box !important;
    }

    .view-cases-table-cards .case-card :is(h1, h2, h3, h4, p, a, span, div) {
        min-width: 0;
        overflow-wrap: anywhere !important;
        word-break: break-word !important;
    }

    .view-cases-table-cards .case-card-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: .5rem !important;
        flex-wrap: nowrap !important;
        margin-top: .75rem !important;
    }

    .view-cases-table-cards .case-card-actions :is(button, a, form, form > button) {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 2.75rem !important;
        min-width: 2.75rem !important;
        height: 2.75rem !important;
        min-height: 2.75rem !important;
        padding: 0 !important;
        flex: 0 0 auto !important;
    }

    .view-cases-statistics {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: .75rem !important;
    }

    .view-cases-statistics>div {
        min-width: 0;
        padding: .875rem 1rem !important;
    }

    .view-cases-statistics>div>div {
        min-width: 0;
    }

    .view-cases-statistics p:first-child {
        overflow-wrap: anywhere;
        line-height: 1.2;
    }

    .view-cases-toolbar {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: .75rem !important;
    }

    .view-cases-toolbar>.relative {
        flex: 1 1 220px;
        min-width: 0;
    }

    .view-cases-toolbar-actions {
        margin-left: auto !important;
        flex-wrap: wrap !important;
        width: auto !important;
    }
    }

    @media (max-width: 47.9375rem) {

        html,
        body {
            height: auto !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        #mainContent {
            height: auto !important;
            overflow: visible !important;
        }

        #barangay-cases-view-header>div {
            padding-left: max(0.75rem, env(safe-area-inset-left)) !important;
            padding-right: max(0.75rem, env(safe-area-inset-right)) !important;
        }

        #barangay-cases-view-main {
            height: auto !important;
            overflow: visible !important;
            padding-left: max(0.75rem, env(safe-area-inset-left)) !important;
            padding-right: max(0.75rem, env(safe-area-inset-right)) !important;
            padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom))) !important;
        }

        .view-cases-table-desktop {
            display: none !important;
        }

        .view-cases-table-cards {
            display: flex !important;
        }

        .view-cases-toolbar {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: .5rem !important;
        }

        .view-cases-toolbar>.relative,
        .view-cases-toolbar>a {
            width: 100% !important;
            max-width: 100% !important;
        }

        .view-cases-toolbar-actions {
            width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: .5rem !important;
        }

        .view-cases-toolbar-actions>* {
            flex: 1 1 calc(50% - .25rem) !important;
            justify-content: center !important;
        }

        .view-cases-statistics {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: .75rem !important;
        }

        .view-cases-statistics>div {
            min-width: 0;
            padding: .875rem !important;
        }

        .view-cases-statistics>div>div {
            min-width: 0;
            gap: .5rem !important;
        }

        .view-cases-statistics p:first-child {
            font-size: .6875rem;
            overflow-wrap: anywhere;
        }

        .view-cases-list {
            flex: none !important;
            min-height: 0;
            overflow: visible !important;
        }

        .view-cases-table-cards {
            display: flex !important;
            flex-direction: column !important;
            gap: .75rem !important;
            overflow: visible !important;
            padding: .75rem !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        .view-cases-table-cards .case-card {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            padding: 1rem !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
            margin: 0 !important;
        }

        .view-cases-table-cards .case-card * {
            box-sizing: border-box !important;
        }

        .view-cases-table-cards .case-card :is(h1, h2, h3, h4, p, a, span, div) {
            min-width: 0;
            overflow-wrap: anywhere !important;
            word-break: break-word !important;
        }

        .view-cases-table-cards .case-card>div:first-child {
            flex-wrap: wrap;
            gap: .5rem !important;
        }

        .view-cases-table-cards .case-card>div:first-child>div {
            min-width: 0;
            overflow-wrap: anywhere;
            flex: 1 1 auto;
        }

        .view-cases-table-cards .case-card-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: .5rem !important;
            flex-wrap: nowrap !important;
            margin-top: .75rem !important;
        }

        .view-cases-table-cards .case-card-actions :is(button, a, form, form > button) {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 2.75rem !important;
            min-width: 2.75rem !important;
            height: 2.75rem !important;
            min-height: 2.75rem !important;
            padding: 0 !important;
            flex: 0 0 auto !important;
        }

        .view-cases-pagination {
            padding: 1rem !important;
        }

        .view-cases-pagination>div {
            gap: .75rem !important;
        }
    }
</style>
@endif
@if(auth()->user()?->position === 'Desk Officer')
<style>
    /* Match the Desk Officer dashboard / create-case top navbar and sidebar on mobile and tablet. */
    @media (max-width: 47.9375rem) {

        body.desk-officer-cases-view-mobile-nav #mobileTopBar,
        body.desk-officer-cases-view-mobile-nav #mobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 30 !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header>div {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-top: max(1rem, env(safe-area-inset-top)) !important;
            padding-bottom: 1rem !important;
            padding-left: max(.75rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }

        body.desk-officer-cases-view-mobile-nav #barangay-cases-view-header .view-cases-header-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: .625rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
            margin: 0 !important;
            align-self: center !important;
        }

        body.desk-officer-cases-view-mobile-nav #barangay-cases-view-header .view-cases-header-leading>div {
            width: auto !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            margin: 0 !important;
            align-self: center !important;
        }

        html body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header :is(h1, h2, h3, p) {
            color: #ffffff !important;
        }

        body.desk-officer-cases-view-mobile-nav #dashboardMenuToggle {
            position: relative !important;
            left: 0 !important;
            margin: 0 !important;
            transform: none !important;
            display: inline-flex !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
            padding: 0 !important;
            line-height: 1 !important;
        }

        body.desk-officer-cases-view-mobile-nav #dashboardMenuToggle>i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.25rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header>div>div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
            display: flex !important;
            align-items: center !important;
            align-self: center !important;
            margin: 0 !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header>div>div:last-child>div.relative {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            align-self: center !important;
        }

        html body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
            background: var(--mswdo-profile, #BC0202) !important;
            border: 1px solid #8f0202 !important;
            box-shadow: 0 2px 5px rgba(188, 2, 2, .18) !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            align-self: center !important;
            position: relative !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        html body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }

        html body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header button[aria-label="Notifications"]>i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 1.05rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
        }

        html body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header :is(#notifications-unread-count, [data-unread-badge], .notification-badge) {
            position: absolute !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem !important;
            top: -0.25rem !important;
            right: -0.25rem !important;
            font-size: 0.75rem !important;
            line-height: 1 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 9999px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
            border: 2px solid #C41E3A !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header div:has(> img[alt="User Profile"]) {
            display: none !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav .notification-dropdown {
            position: fixed !important;
            top: calc(max(1rem, env(safe-area-inset-top)) + 3.75rem) !important;
            left: 1rem !important;
            right: 1rem !important;
            width: auto !important;
            max-width: calc(100vw - 2rem) !important;
            z-index: 1000 !important;
        }

        /* Sidebar dropdown drawer */
        body.desk-officer-cases-view-mobile-nav #sidebar {
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
            box-shadow: 0 14px 28px rgba(0, 0, 0, .22) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateY(-8px) !important;
            transition: transform .25s ease, opacity .25s ease, visibility .25s ease !important;
            background: #C41E3A !important;
            background-image: none !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav #sidebar nav :is(a, button),
        body.portal-ui.desk-officer-cases-view-mobile-nav #sidebar .sidebar-link {
            background: #C41E3A !important;
            color: #ffffff !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav #sidebar nav :is(a, button) :is(i, svg, span),
        body.portal-ui.desk-officer-cases-view-mobile-nav #sidebar .sidebar-link :is(i, svg, span) {
            color: #ffffff !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav #sidebar nav :is(a, button):hover,
        body.portal-ui.desk-officer-cases-view-mobile-nav #sidebar .sidebar-link:hover {
            background: #99172E !important;
            color: #ffffff !important;
        }

        body.portal-ui.desk-officer-cases-view-mobile-nav #sidebar .sidebar-link:is([class~="bg-purple-600"], [class~="bg-purple-700/70"]),
        body.portal-ui.desk-officer-cases-view-mobile-nav #sidebar nav [x-show]> :is(a, button).bg-purple-600 {
            background: #99172E !important;
            color: #ffffff !important;
            box-shadow: inset 3px 0 0 #ffffff !important;
        }

        body.desk-officer-cases-view-mobile-nav #mainContent {
            width: 100% !important;
            min-width: 0 !important;
            margin-left: 0 !important;
        }
    }

    /* Tablet view: collapsed rail, full-width content and clean white header. */
    @media (min-width: 48rem) and (max-width: 80rem) {

        body.desk-officer-cases-view-mobile-nav #mobileTopBar,
        body.desk-officer-cases-view-mobile-nav #mobileMenuToggle,
        body.desk-officer-cases-view-mobile-nav #dashboardMenuToggle,
        body.desk-officer-cases-view-mobile-nav #sidebarCloseBtn,
        body.desk-officer-cases-view-mobile-nav #sidebarToggleBtn {
            display: none !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        html body.portal-ui.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        body.desk-officer-cases-view-mobile-nav #mainContent>header#barangay-cases-view-header>div {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            min-height: 4.25rem !important;
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar {
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

        body.desk-officer-cases-view-mobile-nav #sidebar .sidebar-header {
            display: flex !important;
            min-height: 4.25rem !important;
            padding-left: 0.75rem !important;
            padding-right: 0.5rem !important;
            justify-content: flex-start !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar:not(.sidebar-expanded) .sidebar-text {
            display: none !important;
        }

        body.desk-officer-cases-view-mobile-nav #sidebar~#mainContent {
            width: calc(100% - 4.5rem) !important;
            min-width: 0 !important;
            margin-left: 4.5rem !important;
        }
    }
</style>
@endif
@if($isBarangayDeskOfficer || $isMultiroleDeskOfficer)
<style>
    @media (max-width: 767px),
    (hover: none) and (pointer: coarse) {
        #barangay-cases-view-main {
            height: auto !important;
            min-height: 0;
            overflow: visible !important;
        }

        .view-cases-toolbar {
            display: contents !important;
        }

        .view-cases-toolbar>.relative {
            order: 1;
            width: 100%;
            margin-bottom: .5rem;
        }

        .view-cases-active-filters {
            order: 2;
            margin-bottom: .5rem !important;
        }

        .view-cases-statistics {
            order: 3;
            gap: .5rem !important;
            margin-bottom: .5rem !important;
        }

        .view-cases-statistics>div {
            padding: .75rem !important;
        }

        .view-cases-toolbar-actions {
            order: 4;
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100% !important;
            gap: .5rem !important;
            margin-bottom: .75rem;
        }

        .view-cases-toolbar-actions>.relative,
        .view-cases-toolbar-actions>button,
        .view-cases-toolbar-actions button {
            width: 100%;
        }

        .view-cases-toolbar-actions>button,
        .view-cases-toolbar-actions>.relative>button {
            min-height: 2.5rem;
            justify-content: center;
            padding: .5rem .75rem !important;
        }

        .view-cases-list {
            order: 5;
        }

        .view-cases-table-desktop {
            display: none !important;
        }

        .view-cases-table-cards {
            display: block !important;
            padding: .5rem !important;
        }

        .view-cases-table-cards .case-card {
            position: relative;
            padding: .75rem .75rem 3.5rem !important;
        }

        .view-cases-table-cards .case-card>span {
            position: absolute;
            bottom: .875rem;
            left: .75rem;
        }

        .view-cases-table-cards .case-card-actions {
            position: absolute;
            right: .75rem;
            bottom: .625rem;
            margin: 0 !important;
            gap: .5rem !important;
            flex-wrap: nowrap !important;
        }

        .view-cases-table-cards .case-card-actions :is(button, form, form > button) {
            width: 2.25rem !important;
            min-width: 2.25rem !important;
            height: 2.25rem !important;
            min-height: 2.25rem !important;
            padding: 0 !important;
        }
    }

    @media (min-width: 768px) and (max-width: 1439px) {

        /* Stable tablet sidebar dimensions and internal navigation scrolling. */
        #sidebar:not(.sidebar-collapsed) {
            position: fixed !important;
            inset: 0 auto 0 0 !important;
            width: 18rem !important;
            min-width: 18rem !important;
            max-width: 18rem !important;
            height: 100dvh !important;
            max-height: 100dvh !important;
            box-sizing: border-box;
            overflow: hidden;
        }

        #sidebar .sidebar-menu {
            min-height: 0;
            overflow-y: auto;
        }

        .view-cases-statistics {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: .75rem !important;
        }

        .view-cases-statistics>div {
            min-width: 0;
            padding: .875rem 1rem !important;
        }

        .view-cases-statistics>div>div {
            min-width: 0;
        }

        .view-cases-statistics p:first-child {
            overflow-wrap: anywhere;
            line-height: 1.2;
        }

        .view-cases-table-desktop {
            display: none !important;
        }

        .view-cases-table-cards {
            display: block !important;
        }

        .view-cases-table-cards .case-card {
            min-width: 0;
        }

        .view-cases-table-cards .case-card-actions {
            justify-content: flex-end !important;
            gap: .5rem !important;
            flex-wrap: nowrap !important;
        }

        .view-cases-table-cards .case-card-actions :is(button, a, form > button) {
            flex: 0 0 auto;
        }

        .multirole-desk-case-cards .case-card-actions {
            justify-content: flex-end !important;
            gap: .5rem !important;
            flex-wrap: nowrap !important;
        }

        .multirole-desk-case-cards .case-card-actions>* {
            flex: 0 0 auto !important;
        }

        .multirole-desk-case-cards .case-card-actions :is(button, a, form > button) {
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
        }
    }
</style>
@endif
@if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
<style>
    .view-cases-table-desktop table :is(th, td) {
        text-align: center;
        vertical-align: middle;
    }
</style>
@endif
@if(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'SWD',
'VAWC'], true))
<style>
    .view-cases-table-desktop table :is(th, td) {
        text-align: center;
        vertical-align: middle;
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    /* Tablet department-admin view: fixed collapsed sidebar and case cards. */
    @media (min-width: 48rem) and (max-width: 89.9375rem) {
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar.sidebar-collapsed-init {
            position: fixed !important;
            inset: 0 auto 0 0 !important;
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: none !important;
            overflow: hidden !important;
            border-radius: 0 !important;
            transform: none !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav.bcpc-admin-header-theme #sidebar,
        html body.portal-ui.view-cases-admin-mobile-nav.swd-admin-header-theme #sidebar {
            background: #B11116 !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-header {
            display: flex !important;
            min-height: 5.5rem !important;
            padding-left: .75rem !important;
            padding-right: .5rem !important;
            justify-content: flex-start !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-header img {
            width: 3rem !important;
            height: 3rem !important;
            min-width: 3rem !important;
            flex-shrink: 0 !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-text,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-chevron,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-submenu {
            display: none !important;
            max-width: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-menu {
            min-height: 0 !important;
            padding-left: .5rem !important;
            padding-right: .5rem !important;
            overflow-x: hidden !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar .sidebar-link,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar nav :is(a, button) {
            justify-content: center !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar ~ #mainContent,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar.sidebar-collapsed ~ #mainContent,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar.sidebar-collapsed-init ~ #mainContent {
            width: calc(100% - 4.5rem) !important;
            min-width: 0 !important;
            margin-left: 4.5rem !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebarToggleBtn {
            display: none !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-desktop {
            display: none !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards {
            display: flex !important;
            flex-direction: column !important;
            flex: none !important;
            gap: .75rem !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            overflow: visible !important;
            box-sizing: border-box !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards .case-card {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            min-height: 13rem !important;
            box-sizing: border-box !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards .case-card > span {
            order: 5;
            align-self: flex-start;
            margin-top: .5rem;
        }
        html body.portal-ui.view-cases-admin-mobile-nav .view-cases-table-cards .case-card-actions {
            order: 6;
            margin-top: auto !important;
            margin-bottom: 0 !important;
        }
    }
</style>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<style>
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: landscape) {
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) {
            width: 18rem !important;
            min-width: 18rem !important;
            max-width: 18rem !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) .sidebar-text,
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) .sidebar-chevron {
            display: inline-block !important;
            max-width: none !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) nav :is(a, button) {
            justify-content: flex-start !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) nav button.sidebar-link {
            justify-content: space-between !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) .sidebar-header img {
            width: 2.25rem !important;
            min-width: 2.25rem !important;
            height: 2.25rem !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) ~ #mainContent {
            width: calc(100% - 18rem) !important;
            margin-left: 18rem !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) ~ #sidebarToggleBtn {
            display: flex !important;
            top: 2.75rem !important;
            left: calc(18rem - .75rem) !important;
            transform: translateY(-50%) !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-header {
            height: 7rem !important;
            min-height: 7rem !important;
            padding: 1.25rem 0 0 !important;
            display: flex !important;
            align-items: flex-start !important;
            justify-content: center !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-header > div {
            width: 100%;
            justify-content: center !important;
            gap: 0 !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) .sidebar-header img {
            width: 2.25rem !important;
            min-width: 2.25rem !important;
            height: 2.25rem !important;
        }
        html body.portal-ui.view-cases-admin-mobile-nav #sidebar:is(.sidebar-collapsed, .sidebar-collapsed-init) ~ #sidebarToggleBtn {
            display: flex !important;
            top: 4.75rem !important;
            left: 2.25rem !important;
            transform: translateX(-50%) !important;
        }
    }
</style>
@endif
@endpush

@section('content')

<!-- View Case Modal -->
<div x-data="{ open: false, content: '', caseNumber: '', caseId: '', caseStatus: '', printUrl: '', referUrl: '', isBcpcAdmin: {{ auth()->user()->isBcpcAdmin() ? 'true' : 'false' }}, isVawcAdmin: {{ auth()->user()->isVawcAdmin() ? 'true' : 'false' }}, isBarangayDeskOfficer: {{ $isBarangayDeskOfficer ? 'true' : 'false' }}, allowBackdropClose: {{ auth()->user()->isDepartmentAdmin() ? 'false' : 'true' }} }"
    x-cloak
    @open-case-modal.window="open = true; content = $event.detail.content; caseNumber = $event.detail.caseNumber || ''; caseId = $event.detail.caseId || ''; caseStatus = $event.detail.caseStatus || ''; printUrl = $event.detail.printUrl || '#';"
    @close-case-modal.window="open = false" id="caseModal"
    x-effect="open ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click.self="if (allowBackdropClose) open = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.stop x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div
                class="case-modal-top-header flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                <div>
                    <h5 class="text-lg font-semibold text-gray-800" x-text="'Case #' + caseNumber">Case Details</h5>
                    <div class="case-modal-top-summary"
                        x-html="new DOMParser().parseFromString(content || '', 'text/html').querySelector('[data-case-modal-summary]')?.innerHTML || ''">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="printUrl" target="_blank"
                        class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                        <i class="fas fa-print"></i>
                        <span class="hidden sm:inline">Print</span>
                    </a>
                    <a :href="(isBarangayDeskOfficer || isBcpcAdmin || isVawcAdmin) && /referred|closed|complet/.test((caseStatus || '').trim().toLowerCase()) ? null : (referUrl || ('/admin/cases/' + caseId + '/refer'))"
                        :aria-disabled="(isBarangayDeskOfficer || isBcpcAdmin || isVawcAdmin) && /referred|closed|complet/.test((caseStatus || '').trim().toLowerCase())"
                        :tabindex="(isBarangayDeskOfficer || isBcpcAdmin || isVawcAdmin) && /referred|closed|complet/.test((caseStatus || '').trim().toLowerCase()) ? -1 : 0"
                        :class="(isBarangayDeskOfficer || isBcpcAdmin || isVawcAdmin) && /referred|closed|complet/.test((caseStatus || '').trim().toLowerCase()) ? 'cursor-not-allowed border border-red-200 bg-red-50 text-red-800 pointer-events-none' : 'bg-purple-700 text-white hover:bg-purple-800'"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg transition-colors text-sm font-medium">
                        <i class="fas fa-share-square"></i>
                        <span class="hidden sm:inline">Refer Case</span>
                    </a>
                    <button @click="open = false"
                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
            <!-- Modal Body -->
            <div class="p-4 overflow-auto flex-1" x-html="content">
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-purple-200 border-t-purple-700">
                    </div>
                    <p class="mt-4 text-sm text-gray-500">Loading case details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Case Modal -->
<div x-data="{ open: false, content: '', caseNumber: '', allowBackdropClose: {{ auth()->user()->isDepartmentAdmin() ? 'false' : 'true' }} }"
    x-cloak
    @open-edit-modal.window="open = true; content = $event.detail.content; caseNumber = $event.detail.caseNumber || '';"
    @close-edit-modal.window="open = false" id="editCaseModal"
    x-effect="open ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click.self="if (allowBackdropClose) open = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div @click.stop x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                <h5 class="text-lg font-semibold text-gray-800" x-text="'Edit Case #' + caseNumber">Edit Case</h5>
                <button type="button"
                    @click="{{ $isBarangayDeskOfficer ? 'window.portalUnsavedChanges.confirmExit(() => { open = false; window.portalUnsavedChanges.reset(); })' : 'open = false' }}"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-4 overflow-auto flex-1" x-html="content">
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-purple-200 border-t-purple-700">
                    </div>
                    <p class="mt-4 text-sm text-gray-500">Loading edit form...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Header -->
<header id="barangay-cases-view-header" class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="view-cases-header-leading flex items-center gap-3 min-w-0">
            <button id="dashboardMenuToggle" class="hidden" type="button" aria-label="Open navigation menu"
                aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="min-w-0">
                <h2 class="text-lg sm:text-2xl font-bold text-gray-800 truncate leading-tight m-0">{{ $barangay->name }}
                    Cases</h2>
                <p class="text-xs sm:text-sm text-gray-500 hidden sm:block mt-0.5">Viewing cases for this barangay</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            @if($isBarangayDeskOfficer)
            @include('desk-officer.components.notifications')
            @else
            @include('admin-tailwind.components.notifications')
            @endif
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile"
                    class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{
                        Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main id="barangay-cases-view-main"
    class="px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 pb-4 flex flex-col h-[calc(100vh-80px)] overflow-hidden">

    <!-- Action Buttons -->
    <div class="view-cases-toolbar flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
        @if($isBarangayDeskOfficer)
        <div class="relative w-full sm:max-w-md sm:flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input id="swd-case-search" type="text" placeholder="Search cases..."
                class="w-full h-10 pl-10 pr-4 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300">
        </div>
        @endif
        <!-- The barangay directory is unavailable to SWD Desk Officers. -->
        @unless($isBarangayDeskOfficer)
        <a href="{{ route('admin-barangay-cases', request()->filled('type') ? ['type' => request('type')] : []) }}"
            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-xs sm:text-sm font-medium whitespace-nowrap">
            <i class="fas fa-arrow-left"></i>
            <span class="hidden xs:inline">Back to</span> <span>Barangay List</span>
        </a>
        @endunless

        <!-- Right Side - Action Buttons -->
        <div
            class="view-cases-toolbar-actions flex w-full items-center justify-end gap-2 sm:w-auto {{ $isBarangayDeskOfficer ? 'sm:ml-auto' : '' }}">
            <!-- Filter Button -->
            <div class="relative {{ $isBarangayDeskOfficer ? 'single-role-desk-view-filter' : '' }}"
                x-data="{ open: false }">
                <button @click="open = !open"
                    class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-xs sm:text-sm font-medium">
                    <i class="fas fa-filter text-purple-600"></i>
                    <span>Filter</span>
                    @if(!empty($currentFilters['status']) || !empty($currentFilters['dateFrom']) ||
                    !empty($currentFilters['dateTo']))
                    <span class="w-2 h-2 bg-purple-600 rounded-full"></span>
                    @endif
                </button>

                <!-- Filter Dropdown -->
                <div id="view-cases-filter-panel" x-show="open" x-cloak @click.away="open = false" x-transition
                    class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-gray-100 z-20 p-4">
                    <form method="GET" action="{{ route('admin.barangay-cases.show', $barangay->id) }}">
                        @if(request()->filled('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                        @endif
                        <h4 class="font-semibold text-gray-800 mb-3">Filter Options</h4>

                        <!-- Status Filter -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                            <select name="status" id="status-filter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                                <option value="">All Status</option>
                                <option value="Case Filed" {{ ($currentFilters['status'] ?? '' )=='Case Filed'
                                    ? 'selected' : '' }}>Case Filed</option>
                                <option value="Case Referred" {{ ($currentFilters['status'] ?? '' )=='Case Referred'
                                    ? 'selected' : '' }}>Case Referred</option>
                                <option value="Case closed" {{ ($currentFilters['status'] ?? '' )=='Case closed'
                                    ? 'selected' : '' }}>Case Closed</option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date Range</label>
                            <div class="space-y-2">
                                <input type="date" name="date_from" id="date-from"
                                    value="{{ $currentFilters['dateFrom'] ?? '' }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                                    placeholder="From">
                                <input type="date" name="date_to" id="date-to"
                                    value="{{ $currentFilters['dateTo'] ?? '' }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm"
                                    placeholder="To">
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                            <a href="{{ route('admin.barangay-cases.show', array_filter(['barangay' => $barangay->id, 'type' => request('type')])) }}"
                                class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                Reset
                            </a>
                            <button type="submit"
                                class="px-3 py-1.5 text-sm bg-purple-700 text-white hover:bg-purple-800 rounded-lg transition-colors">
                                Apply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Print Button -->
            <button type="button" id="printButton"
                class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs sm:text-sm font-medium whitespace-nowrap {{ $isBarangayDeskOfficer ? 'single-role-desk-view-print' : '' }}">
                <i class="fas fa-print"></i>
                <span class="hidden sm:inline">Print All</span><span class="sm:hidden">Print</span>
            </button>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if(!empty($currentFilters['status']) || !empty($currentFilters['dateFrom']) || !empty($currentFilters['dateTo']))
    <div class="view-cases-active-filters flex flex-wrap items-center gap-2 mb-4 text-sm">
        <span class="text-gray-500">Active Filters:</span>
        @if(!empty($currentFilters['status']))
        <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
            Status: {{ $currentFilters['status'] }}
        </span>
        @endif
        @if(!empty($currentFilters['dateFrom']))
        <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
            From: {{ \Carbon\Carbon::parse($currentFilters['dateFrom'])->format('M d, Y') }}
        </span>
        @endif
        @if(!empty($currentFilters['dateTo']))
        <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
            To: {{ \Carbon\Carbon::parse($currentFilters['dateTo'])->format('M d, Y') }}
        </span>
        @endif
        <a href="{{ route('admin.barangay-cases.show', array_filter(['barangay' => $barangay->id, 'type' => request('type')])) }}"
            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">Clear
            filters</a>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="view-cases-statistics grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Case Closed</p>
                    <p class="text-lg font-bold text-gray-800">{{ ($allCases ?? $cases)->whereIn('status', ['Case
                        closed', 'Case Closed', 'Complete case', 'Complete Case'])->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Case Referred</p>
                    <p class="text-lg font-bold text-gray-800">{{ ($allCases ?? $cases)->where('status', 'Case
                        Referred')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Case Filed</p>
                    <p class="text-lg font-bold text-gray-800">{{ ($allCases ?? $cases)->where('status', 'Case
                        Filed')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-purple-600"></div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Total Cases</p>
                    <p class="text-lg font-bold text-gray-800">{{ ($allCases ?? $cases)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cases Table Card -->
    <div
        class="view-cases-list bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col flex-1 min-h-0 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-700 to-purple-600 flex-shrink-0">
            <h3 class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-folder-open"></i>
                Cases List ({{ $cases->total() }} total)
            </h3>
        </div>

        <!-- Desktop Table -->
        <div class="view-cases-table-desktop flex-1 overflow-auto min-h-0">
            <table class="w-full table-fixed">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider w-[9%]">
                            Case #</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider w-[10%]">
                            Date Reported</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider w-[12%]">
                            Victim Name</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider w-[10%]">
                            Type of Abuse</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider w-[10%]">
                            Status</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider w-[10%]">
                            Case Priority</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider w-[12%]">
                            Last Updated</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-semibold text-purple-700 uppercase tracking-wider w-[12%]">
                            Generated By</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-semibold text-purple-700 uppercase tracking-wider w-[15%]">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cases as $case)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <button type="button"
                                class="view-case text-purple-700 hover:text-purple-900 font-semibold text-sm underline"
                                data-ajax-url="{{ route('cases.show', $case->id) }}"
                                data-case-number="{{ $isSwdDeskOfficer ? preg_replace('/^VAWC-/', 'SWD-', $case->case_number ?? '') : $case->case_number }}"
                                data-case-id="{{ $case->id }}"
                                data-case-status="{{ $case->hasBeenReferred() ? 'Case Referred' : $case->status }}">
                                {{ $isSwdDeskOfficer ? preg_replace('/^VAWC-/', 'SWD-', $case->case_number ?? 'N/A') :
                                ($case->case_number ?? 'N/A') }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('M d, Y') :
                            'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 truncate">
                            {{ censorLastName($case->victim_name) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($case->abuse_type)
                            @php
                            $abuseType = strtolower($case->abuse_type);
                            $typeColor = '#' .
                            \App\Http\Controllers\Wcpu\AdminController::getAbuseTypeColor($abuseType);
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                style="background-color: {{ $typeColor }}20; color: {{ $typeColor }};">
                                {{ ucfirst($case->abuse_type) }}
                            </span>
                            @else
                            <span class="text-sm text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td
                            class="px-6 py-4 {{ auth()->user()?->isDepartmentAdmin() || auth()->user()?->position === 'Desk Officer' ? 'text-center' : '' }}">
                            @if($case->status)
                            @php
                            $statusColor = '#' .
                            \App\Http\Controllers\Wcpu\AdminController::getStatusColor($case->status);
                            @endphp
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium text-white"
                                style="background-color: {{ $statusColor }};">
                                {{ ($case->status ?? '') === 'Case closed' ? 'Case Closed' : $case->status }}
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-500 text-white">
                                Pending
                            </span>
                            @endif
                        </td>
                        <td
                            class="px-6 py-4 {{ auth()->user()?->isDepartmentAdmin() || auth()->user()?->position === 'Desk Officer' ? 'text-center' : '' }}">
                            @php
                            $priorityValue = trim((string) $case->priority);
                            $priorityKey = strtolower($priorityValue);
                            $priorityLabels = ['emergency' => 'Emergency', 'urgent' => 'Urgent', 'non-urgent' =>
                            'Non-urgent', 'normal' => 'Non-urgent'];
                            $priorityColors = ['emergency' => ['#dc2626', '#fff'], 'urgent' => ['#f59e0b', '#111'],
                            'non-urgent' => ['#16a34a', '#fff'], 'normal' => ['#16a34a', '#fff']];
                            $priorityStyle = $priorityColors[$priorityKey] ?? ['#6b7280', '#fff'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                style="background-color: {{ $priorityStyle[0] }}; color: {{ $priorityStyle[1] }};">{{
                                $priorityLabels[$priorityKey] ?? ($priorityValue ?: 'N/A') }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $case->updated_at ? \Carbon\Carbon::parse($case->updated_at)->format('M d, Y g:i A') :
                            'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="inline-flex items-center gap-1 text-purple-700 font-medium">
                                <i class="fas fa-user-circle text-xs" aria-hidden="true"></i> You
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button"
                                    class="view-case {{ auth()->user()->isVawcAdmin() ? 'swd-view-action' : (auth()->user()->isBcpcAdmin() ? 'bcpc-view-action' : '') }} {{ auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'VAWC' ? 'flex-none' : '' }} inline-flex w-10 h-10 items-center justify-center rounded-lg bg-[#C41E3A] border border-[#C41E3A] text-white hover:bg-[#A01830] active:bg-[#8B1429] transition-colors"
                                    data-ajax-url="{{ route('cases.show', $case->id) }}"
                                    data-case-number="{{ $isSwdDeskOfficer ? preg_replace('/^VAWC-/', 'SWD-', $case->case_number ?? '') : $case->case_number }}"
                                    data-case-id="{{ $case->id }}"
                                    data-case-status="{{ $case->hasBeenReferred() ? 'Case Referred' : $case->status }}"
                                    title="View Details">
                                    <i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View
                                        Details</span>
                                </button>
                                @php
                                $isClosedCase = preg_match('/closed|complet/i', trim((string) $case->status));
                                $isReferredCase = $case->hasBeenReferred();
                                $isCaseDeskOfficer = $isBarangayDeskOfficer;
                                $isBcpcDeskOfficer = auth()->user()?->position === 'Desk Officer'
                                && auth()->user()?->defaultCaseDepartment() === 'BCPC'
                                && !auth()->user()?->isMultiCaseDepartment();
                                $editDisabled = $isCaseDeskOfficer
                                ? ($isClosedCase || $isReferredCase)
                                : (auth()->user()->isDepartmentAdmin() ? ($isClosedCase || $isReferredCase) :
                                $isClosedCase);
                                @endphp
                                <button type="button"
                                    class="edit-case inline-flex items-center justify-center {{ $isCaseDeskOfficer ? 'w-10 h-10 flex-none' : 'w-9 h-9' }} rounded-lg border transition-colors {{ $editDisabled ? 'cursor-not-allowed border-red-200 bg-red-50 text-red-600 shadow-sm opacity-100' : 'border-gray-400 text-gray-600 hover:bg-gray-50' }}"
                                    data-ajax-url="{{ route('cases.edit', $case->id) }}"
                                    data-case-number="{{ $isSwdDeskOfficer ? preg_replace('/^VAWC-/', 'SWD-', $case->case_number ?? '') : $case->case_number }}"
                                    title="{{ $editDisabled ? ($isClosedCase ? 'Editing is unavailable for closed cases' : 'Editing is unavailable for referred cases') : 'Edit Case' }}"
                                    {{ $editDisabled ? 'disabled' : '' }}>
                                    <i class="fas fa-edit"></i>
                                </button>
                                @include('components.case-archive-button', [
                                'archiveUrl' => route('cases.destroy', $case->id),
                                'buttonClass' => $isBcpcDeskOfficer ? 'w-10 h-10 flex-none border-[#C41E3A] bg-[#C41E3A]
                                text-white hover:bg-[#A01830]' : ($isCaseDeskOfficer ? 'w-10 h-10 flex-none
                                border-amber-500 text-amber-700 hover:bg-amber-50' : null),
                                ])
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="h-[22rem]">
                        <td colspan="9" class="p-0 align-middle text-center">
                            <div class="flex min-h-[22rem] flex-col items-center justify-center px-6">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <h4 class="text-gray-600 font-medium mb-1">No Cases Found</h4>
                                <p class="text-sm text-gray-400">There are no cases recorded for {{ $barangay->name }}
                                    yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div
            class="view-cases-table-cards {{ $isMultiroleDeskOfficer ? 'multirole-desk-case-cards' : '' }} flex-1 overflow-auto p-4 space-y-3">
            @forelse($cases as $case)
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:border-purple-300 transition-colors case-card"
                data-ajax-url="{{ route('cases.show', $case->id) }}"
                data-case-number="{{ $isSwdDeskOfficer ? preg_replace('/^VAWC-/', 'SWD-', $case->case_number ?? '') : $case->case_number }}"
                data-case-id="{{ $case->id }}">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="text-sm font-semibold text-purple-700">{{ $isSwdDeskOfficer ? preg_replace('/^VAWC-/',
                            'SWD-', $case->case_number ?? 'N/A') : ($case->case_number ?? 'N/A') }}</p>
                        <p class="text-xs text-gray-500">{{ $case->date_reported ?
                            \Carbon\Carbon::parse($case->date_reported)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    @php $mobileStatus = $case->status ?: 'Pending'; @endphp
                    @php $statusColor = '#' . \App\Http\Controllers\Wcpu\AdminController::getStatusColor($mobileStatus);
                    @endphp
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white flex-shrink-0"
                        style="background-color: {{ $statusColor }};">
                        {{ $mobileStatus }}
                    </span>
                </div>
                <p class="text-sm text-gray-800 mb-2">{{ censorLastName($case->victim_name) }}</p>
                <p class="text-xs text-gray-500 mb-2"><i class="fas fa-user-circle text-purple-700"
                        aria-hidden="true"></i> Generated by: You</p>
                <div class="case-card-actions flex items-center justify-end gap-2 mb-3">
                    <button type="button"
                        class="view-case {{ auth()->user()->isVawcAdmin() ? 'swd-view-action' : (auth()->user()->isBcpcAdmin() ? 'bcpc-view-action' : '') }} inline-flex w-10 h-10 items-center justify-center rounded-lg bg-[#C41E3A] border border-[#C41E3A] text-white hover:bg-[#A01830] active:bg-[#8B1429] transition-colors"
                        data-ajax-url="{{ route('cases.show', $case->id) }}"
                        data-case-number="{{ $isSwdDeskOfficer ? preg_replace('/^VAWC-/', 'SWD-', $case->case_number ?? '') : $case->case_number }}"
                        data-case-id="{{ $case->id }}"
                        data-case-status="{{ $case->hasBeenReferred() ? 'Case Referred' : $case->status }}"
                        title="View Case">
                        <i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View Details</span>
                    </button>
                    @php
                    $isClosedCase = preg_match('/closed|complet/i', trim((string) $case->status));
                    $isReferredCase = $case->hasBeenReferred();
                    $isCaseDeskOfficer = $isBarangayDeskOfficer;
                    $isBcpcDeskOfficer = auth()->user()?->position === 'Desk Officer'
                    && auth()->user()?->defaultCaseDepartment() === 'BCPC'
                    && !auth()->user()?->isMultiCaseDepartment();
                    $editDisabled = $isCaseDeskOfficer
                    ? ($isClosedCase || $isReferredCase)
                    : (auth()->user()->isDepartmentAdmin() ? ($isClosedCase || $isReferredCase) : $isClosedCase);
                    @endphp
                    <button type="button"
                        class="edit-case inline-flex items-center justify-center w-10 h-10 rounded-lg border transition-colors {{ $editDisabled ? 'cursor-not-allowed border-red-200 bg-red-50 text-red-600 shadow-sm opacity-100' : 'border-gray-400 text-gray-600 hover:bg-gray-50' }}"
                        data-ajax-url="{{ route('cases.edit', $case->id) }}"
                        data-case-number="{{ $isSwdDeskOfficer ? preg_replace('/^VAWC-/', 'SWD-', $case->case_number ?? '') : $case->case_number }}"
                        title="{{ $editDisabled ? ($isClosedCase ? 'Editing is unavailable for closed cases' : 'Editing is unavailable for referred cases') : 'Edit Case' }}"
                        {{ $editDisabled ? 'disabled' : '' }}>
                        <i class="fas fa-edit" aria-hidden="true"></i><span class="sr-only">Edit</span>
                    </button>
                    @include('components.case-archive-button', [
                    'archiveUrl' => route('cases.destroy', $case->id),
                    'buttonClass' => $isBcpcDeskOfficer ? 'w-10 h-10 border-[#C41E3A] bg-[#C41E3A] text-white
                    hover:bg-[#A01830]' : ($isCaseDeskOfficer ? 'w-10 h-10 border-amber-500 text-amber-700
                    hover:bg-amber-50' : null),
                    ])
                </div>
                @php $typeColor = '#' .
                \App\Http\Controllers\Wcpu\AdminController::getAbuseTypeColor(strtolower($case->abuse_type ?? ''));
                @endphp
                <span
                    class="{{ $case->abuse_type ? 'inline-flex' : 'hidden' }} items-center px-2 py-0.5 rounded-full text-xs font-medium"
                    style="background-color: {{ $typeColor }}20; color: {{ $typeColor }};">
                    {{ ucfirst($case->abuse_type ?? '') }}
                </span>
            </div>
            @empty
            <div class="view-cases-empty-state flex flex-col items-center justify-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <h4 class="text-gray-600 font-medium mb-1">No Cases Found</h4>
                <p class="text-sm text-gray-400">There are no cases recorded for {{ $barangay->name }} yet.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div
            class="view-cases-pagination {{ $cases->hasPages() ? '' : 'hidden' }} px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
            @if(auth()->user()?->isDepartmentAdmin())
            <div class="barangay-tablet-pagination hidden">
                @include('components.archived-pagination', ['paginator' => $cases, 'label' => 'cases', 'pageLinkLimit' => 4])
            </div>
            @endif
            <div class="barangay-legacy-pagination flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-600">
                    Showing {{ $cases->firstItem() }} - {{ $cases->lastItem() }} of {{ $cases->total() }} cases
                </p>
                <div class="flex items-center gap-1">
                    {{-- Previous Button --}}
                    @if($cases->onFirstPage())
                    <span
                        class="px-3 py-2 border border-red-200 bg-red-50 text-red-800 rounded-lg text-sm cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                    @else
                    <a href="{{ $cases->previousPageUrl() }}"
                        class="{{ auth()->user()?->isDepartmentAdmin() ? 'admin-page-control ' : '' }}px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    @endif

                    {{-- Page Numbers --}}
                    @php
                    $currentPage = $cases->currentPage();
                    $lastPage = $cases->lastPage();
                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $currentPage + 2);

                    // Ensure we always show at least 5 pages when possible
                    if ($end - $start < 4) { if ($start==1) { $end=min($lastPage, $start + 4); } elseif
                        ($end==$lastPage) { $start=max(1, $end - 4); } } @endphp @if($start> 1)
                        <a href="{{ $cases->url(1) }}"
                            class="{{ auth()->user()?->isDepartmentAdmin() ? 'admin-page-control ' : '' }}px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">1</a>
                        @if($start > 2)
                        <span class="px-2 py-2 text-gray-400 text-sm">...</span>
                        @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++) @if($i==$currentPage) <span
                            class="px-3 py-2 bg-[#C41E3A] text-white border border-[#C41E3A] rounded-lg text-sm font-medium">
                            {{ $i }}</span>
                            @else
                            <a href="{{ $cases->url($i) }}"
                                class="{{ auth()->user()?->isDepartmentAdmin() ? 'admin-page-control ' : '' }}px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">{{
                                $i }}</a>
                            @endif
                            @endfor

                            @if($end < $lastPage) @if($end < $lastPage - 1) <span
                                class="px-2 py-2 text-gray-400 text-sm">...</span>
                                @endif
                                <a href="{{ $cases->url($lastPage) }}"
                                    class="{{ auth()->user()?->isDepartmentAdmin() ? 'admin-page-control ' : '' }}px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">{{
                                    $lastPage }}</a>
                                @endif

                                {{-- Next Button --}}
                                @if($cases->hasMorePages())
                                <a href="{{ $cases->nextPageUrl() }}"
                                    class="{{ auth()->user()?->isDepartmentAdmin() ? 'admin-page-control ' : '' }}px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                                @else
                                <span
                                    class="px-3 py-2 border border-red-200 bg-red-50 text-red-800 rounded-lg text-sm cursor-not-allowed">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                                @endif
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
@if(auth()->user()?->isDepartmentAdmin())
<script>
document.addEventListener('DOMContentLoaded', () => {
    const portrait = matchMedia('(min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)');
    ['caseModal', 'editCaseModal'].forEach(id => {
        const modal = document.getElementById(id);
        if (!modal) return;
        const home = document.createComment(id + ' position');
        modal.before(home);
        const position = () => {
            const move = () => {
                if (portrait.matches) document.body.append(modal);
                else home.after(modal);
            };
            if (window.Alpine?.mutateDom) window.Alpine.mutateDom(move);
            else move();
        };
        position();
        portrait.addEventListener('change', position);
    });
});
</script>
@endif
@if(auth()->user()?->isDepartmentAdmin())
<script>
document.addEventListener('DOMContentLoaded', () => {
    const responsivePagination = matchMedia('(max-width: 85.375rem)');
    let activeRequest;
    document.addEventListener('click', async event => {
        const link = event.target.closest('.view-cases-pagination a[href]');
        if (!responsivePagination.matches || !link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        activeRequest?.abort();
        const controller = new AbortController();
        activeRequest = controller;
        const cards = document.querySelector('.view-cases-table-cards');
        cards?.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(link.href, { signal: controller.signal, headers: { Accept: 'text/html' } });
            if (!response.ok || response.redirected) throw new Error('Unable to load cases');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (controller.signal.aborted) return;
            const replacements = ['.view-cases-table-cards', '.view-cases-table-desktop', '.view-cases-pagination']
                .map(selector => [document.querySelector(selector), page.querySelector(selector)]);
            if (replacements.some(([current, next]) => !current || !next)) throw new Error('Missing cases');
            const top = cards.getBoundingClientRect().top;
            replacements.forEach(([current, next]) => current.replaceWith(next));
            history.replaceState(null, '', link.href);
            document.dispatchEvent(new CustomEvent('case-list-updated'));
            const nextCards = document.querySelector('.view-cases-table-cards');
            window.scrollBy({ top: nextCards.getBoundingClientRect().top - top, behavior: 'instant' });
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
    const isSwdAdmin = @json(auth()->user()->isVawcAdmin());
    let activeViewRequest;
    let activeEditRequest;
    const swdCaseSearch = document.getElementById('swd-case-search');
    if (swdCaseSearch) {
        swdCaseSearch.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
            });
            document.querySelectorAll('.case-card').forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
            });
        });
    }
    // Current filters from the page
    const currentFilters = {
        status: '{{ $currentFilters['status'] ?? '' }}',
        dateFrom: '{{ $currentFilters['dateFrom'] ?? '' }}',
        dateTo: '{{ $currentFilters['dateTo'] ?? '' }}'
    };
    
    // Print button handler
    const printButton = document.getElementById('printButton');
    if (printButton) {
        printButton.addEventListener('click', async function() {
            if (@json(auth()->user()->isVawcAdmin() || auth()->user()->isBcpcAdmin() || $isBarangayDeskOfficer)) {
                const barangayName = @json($barangay->name);
                let printUrl = @json(route('admin.barangay-cases.print-individual', ['barangay' => $barangay->id]));
                const params = new URLSearchParams();
                if (currentFilters.dateFrom) params.append('date_from', currentFilters.dateFrom);
                if (currentFilters.dateTo) params.append('date_to', currentFilters.dateTo);
                if (currentFilters.status) params.append('status', currentFilters.status);
                if (params.toString()) printUrl += '?' + params.toString();

                Swal.fire({
                    title: 'Print Cases',
                    text: `Are you sure you want to print ${barangayName}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Print',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#7c3aed',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) window.open(printUrl, '_blank');
                });
                return;
            }

            let barangays;

            try {
                const response = await fetch('/api/barangays');
                barangays = await response.json();
            } catch (error) {
                return;
            }

            const barangayOptions = barangays.map(barangay =>
                `<option value="${barangay.id}">${barangay.name}</option>`
            ).join('');

            // Build date range message if filters are applied
            let filterMessage = '';
            let dateRangeText = '';
            
            if (currentFilters.dateFrom || currentFilters.dateTo) {
                if (currentFilters.dateFrom && currentFilters.dateTo) {
                    const fromDate = new Date(currentFilters.dateFrom).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    const toDate = new Date(currentFilters.dateTo).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    dateRangeText = `${fromDate} to ${toDate}`;
                    filterMessage += `<p class="text-purple-600 text-sm mt-2 font-medium"><i class="fas fa-calendar-alt mr-1"></i>Date Range: ${dateRangeText}</p>`;
                } else if (currentFilters.dateFrom) {
                    const fromDate = new Date(currentFilters.dateFrom).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    filterMessage += `<p class="text-purple-600 text-sm mt-2 font-medium"><i class="fas fa-calendar-alt mr-1"></i>From: ${fromDate}</p>`;
                } else if (currentFilters.dateTo) {
                    const toDate = new Date(currentFilters.dateTo).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    filterMessage += `<p class="text-purple-600 text-sm mt-2 font-medium"><i class="fas fa-calendar-alt mr-1"></i>Until: ${toDate}</p>`;
                }
            }
            
            if (currentFilters.status) {
                filterMessage += `<p class="text-purple-600 text-sm mt-1 font-medium"><i class="fas fa-tag mr-1"></i>Status: ${currentFilters.status}</p>`;
            }
            
            const hasFilters = currentFilters.dateFrom || currentFilters.dateTo || currentFilters.status;
            const printMessage = hasFilters 
                ? 'Print cases with the selected filters?' 
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
                        <p class="text-gray-400 text-sm mt-2">This will open a print preview in a new window.</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Print',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#7c3aed',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                preConfirm: () => {
                    const barangayId = document.getElementById('print-barangay').value;

                    if (!barangayId) {
                        Swal.showValidationMessage('Please select a barangay.');
                    }

                    return barangayId;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Build print URL with filters
                    let printUrl = '{{ route("admin.barangay-cases.print-individual", ["barangay" => "__ID__"]) }}'
                        .replace('__ID__', encodeURIComponent(result.value));
                    const params = new URLSearchParams();
                    
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
                    
                    window.open(printUrl, '_blank');
                }
            });
        });
    }

    // Intercept these actions at the earliest event phase. This keeps a case
    // modal action from being treated as navigation by any page-level handler.
    window.addEventListener('click', function(event) {
        const actionButton = event.target.closest('.view-case, .edit-case');
        if (!actionButton || actionButton.disabled) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        actionButton.dispatchEvent(new CustomEvent('case-modal-action'));
    }, true);

    function bindCaseListActions(root) {
    // View case modal handlers
    root.querySelectorAll('.view-case').forEach(btn => {
        btn.addEventListener('case-modal-action', function() {
            const ajaxUrl = this.dataset.ajaxUrl;
            const caseNumber = this.dataset.caseNumber || '';
            const caseId = this.dataset.caseId || '';
            const caseStatus = this.dataset.caseStatus || '';
            
            if (!ajaxUrl) return;

            const loadingHtml = `
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-purple-200 border-t-purple-700"></div>
                    <p class="mt-4 text-sm text-gray-500">Loading case details...</p>
                </div>`;
            
            const openLoadingModal = () => window.dispatchEvent(new CustomEvent('open-case-modal', {
                detail: { content: loadingHtml, caseNumber: caseNumber, caseId: caseId, caseStatus: caseStatus }
            }));
            if (@json(auth()->user()->isVawcAdmin())) {
                openLoadingModal();
            } else {
                setTimeout(openLoadingModal, 0);
            }

            if (isSwdAdmin) activeViewRequest?.abort();
            const request = isSwdAdmin ? new AbortController() : null;
            if (isSwdAdmin) activeViewRequest = request;

            fetch(ajaxUrl, { credentials: 'same-origin', cache: 'no-store', redirect: 'manual', signal: request?.signal, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }})
                .then(r => {
                    if (!r.ok) throw new Error('Unable to load case details.');
                    return r.text();
                })
                .then(html => {
                    if (isSwdAdmin && activeViewRequest !== request) return;
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const printUrl = doc.querySelector('a[href*="/print"]')?.getAttribute('href') || '#';
                    const referUrl = doc.querySelector('a[href*="/refer"]')?.getAttribute('href') || '';

                    window.dispatchEvent(new CustomEvent('open-case-modal', {
                        detail: { content: html, printUrl: printUrl, referUrl: referUrl, caseNumber: caseNumber, caseId: caseId, caseStatus: caseStatus }
                    }));
                })
                .catch(err => {
                    if (err.name === 'AbortError') return;
                    console.error('Failed to load case details:', err);
                    window.dispatchEvent(new CustomEvent('open-case-modal', {
                        detail: { content: '<div class="text-center py-8"><p class="text-red-500">Failed to load case details.</p></div>', caseNumber: caseNumber, caseId: caseId, caseStatus: caseStatus }
                    }));
                });
        });
    });

    // Edit case modal handlers
    root.querySelectorAll('.edit-case').forEach(btn => {
        btn.addEventListener('case-modal-action', function() {
            const ajaxUrl = this.dataset.ajaxUrl;
            const caseNumber = this.dataset.caseNumber || '';
            if (@json($isBarangayDeskOfficer) && window.portalUnsavedChanges) window.portalUnsavedChanges.reset(false);
            
            if (!ajaxUrl) return;

            const loadingHtml = `
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-purple-200 border-t-purple-700"></div>
                    <p class="mt-4 text-sm text-gray-500">Loading edit form...</p>
                </div>`;
            
            const openLoadingModal = () => window.dispatchEvent(new CustomEvent('open-edit-modal', {
                detail: { content: loadingHtml, caseNumber: caseNumber }
            }));
            if (@json(auth()->user()->isVawcAdmin())) {
                openLoadingModal();
            } else {
                setTimeout(openLoadingModal, 0);
            }

            if (isSwdAdmin) {
                activeViewRequest?.abort();
                window.dispatchEvent(new CustomEvent('close-case-modal'));
                activeEditRequest?.abort();
            }
            const request = isSwdAdmin ? new AbortController() : null;
            if (isSwdAdmin) activeEditRequest = request;

            fetch(ajaxUrl, { credentials: 'same-origin', cache: 'no-store', redirect: 'manual', signal: request?.signal, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }})
                .then(r => {
                    if (!r.ok) throw new Error('Unable to load the edit form.');
                    return r.text();
                })
                .then(html => {
                    if (isSwdAdmin && activeEditRequest !== request) return;
                    window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: { content: html, caseNumber: caseNumber } }));
                    setTimeout(() => attachEditFormHandler(), 100);
                })
                .catch(err => {
                    if (err.name === 'AbortError') return;
                    console.error('Failed to load edit form:', err);
                    window.dispatchEvent(new CustomEvent('open-edit-modal', {
                        detail: { content: '<div class="text-center py-8"><p class="text-red-500">Failed to load edit form.</p></div>', caseNumber: caseNumber }
                    }));
                });
        });
    });

    // Mobile card click handlers
    root.querySelectorAll('.case-card').forEach(card => {
        card.addEventListener('click', function() {
            const ajaxUrl = this.dataset.ajaxUrl;
            const caseNumber = this.dataset.caseNumber || '';
            const caseId = this.dataset.caseId || '';
            const caseStatus = this.dataset.caseStatus || '';
            
            if (!ajaxUrl) return;

            const loadingHtml = `
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-purple-200 border-t-purple-700"></div>
                    <p class="mt-4 text-sm text-gray-500">Loading case details...</p>
                </div>`;
            
            window.dispatchEvent(new CustomEvent('open-case-modal', { 
                detail: { content: loadingHtml, caseNumber: caseNumber, caseId: caseId, caseStatus: caseStatus }
            }));

            fetch(ajaxUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }})
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const printUrl = doc.querySelector('a[href*="/print"]')?.getAttribute('href') || '#';
                    const referUrl = doc.querySelector('a[href*="/refer"]')?.getAttribute('href') || '';

                    window.dispatchEvent(new CustomEvent('open-case-modal', {
                        detail: { content: html, printUrl: printUrl, referUrl: referUrl, caseNumber: caseNumber, caseId: caseId, caseStatus: caseStatus }
                    }));
                })
                .catch(err => {
                    console.error('Failed to load case details:', err);
                });
        });
    });

    }
    bindCaseListActions(document);
    document.addEventListener('case-list-updated', () => bindCaseListActions(document));

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const caseModal = document.getElementById('caseModal');
            const editModal = document.getElementById('editCaseModal');
            if (caseModal && caseModal.__x) caseModal.__x.$data.open = false;
            if (editModal && editModal.__x) editModal.__x.$data.open = false;
        }
    });
});

function closeEditModal() {
    window.dispatchEvent(new CustomEvent('close-edit-modal'));
}

function attachEditFormHandler() {
    const form = document.getElementById('edit-case-form-modal');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const selectedDepartment = new URLSearchParams(window.location.search).get('type');
        if (selectedDepartment) formData.set('type', selectedDepartment);
        const actionUrl = this.action;
        
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
        .then(async response => {
            if (response.redirected) {
                Swal.fire({ title: 'Success!', text: 'Case updated successfully', icon: 'success', confirmButtonText: 'OK' })
                    .then(() => window.location.assign(response.url));
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            if (data && data.success) {
                Swal.fire({ title: 'Success!', text: data.message || 'Case updated successfully', icon: 'success', confirmButtonText: 'OK' })
                .then(() => {
                    if (data.redirect_url) {
                        window.location.assign(data.redirect_url);
                    } else {
                        window.location.reload();
                    }
                });
            } else if (data && !data.success) {
                let errorMessage = data.message || 'Update failed';
                if (data.errors) errorMessage += '\n\nErrors:\n' + Object.values(data.errors).flat().join('\n');
                Swal.fire({ title: 'Validation Error!', text: errorMessage, icon: 'error', confirmButtonText: 'OK' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ title: 'Error!', text: error.message || 'Failed to update case. Please try again.', icon: 'error', confirmButtonText: 'OK' });
        });
    });
}

// Archive via JSON so the active list updates immediately while retaining the loading modal.
document.addEventListener('submit', function(event) {
    const form = event.target.closest('form[data-confirm-action="archive"]');
    if (!form) return;

    event.preventDefault();
    Swal.fire({
        title: form.dataset.confirmTitle || 'Archive Case?',
        text: form.dataset.confirmMessage || 'Archive this case?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Archive',
        confirmButtonColor: '#d97706'
    }).then((result) => {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Archiving Case...',
            text: 'Please wait while the case is being archived.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading()
        });

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(async (response) => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) throw new Error(data.message || 'Unable to archive the case.');
            return data;
        })
        .then((data) => {
            if (@json($isBarangayDeskOfficer || auth()->user()->isDepartmentAdmin())) {
                Swal.fire({
                    icon: 'success',
                    title: 'Archived!',
                    text: data.message || 'Case archived successfully.',
                    confirmButtonColor: '#C41E3A',
                    confirmButtonText: 'OK'
                }).then(() => window.location.reload());
                return;
            }
            const row = form.closest('tr');
            const card = form.closest('.case-card');
            if (row) row.remove();
            if (card) card.remove();
            Swal.fire({ icon: 'success', title: 'Archived!', text: data.message, confirmButtonColor: '#7c3aed' });
        })
        .catch((error) => Swal.fire({ icon: 'error', title: 'Unable to Archive Case', text: error.message, confirmButtonColor: '#7c3aed' }));
    });
});
</script>
@endpush

@endsection
