@extends($isAdmin ? 'layouts.admin-tailwind' : 'layouts.desk-officer')

@section('title', 'Quarterly Report - ' . ($barangay->name ?? 'Barangay'))

@section('content')
@if(request()->routeIs('desk-officer.quarterly-reports.show'))
<style>
    @media (max-width: 47.9375rem) {
        body.portal-ui #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle {
            font-size: 1.125rem !important;
        }

        body.portal-ui #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle > i {
            font-size: inherit !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Match the notification controls on the quarterly reports list. */
        body.portal-ui #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] button[aria-label="Notifications"] {
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

        body.portal-ui #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }

        body.portal-ui #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] button[aria-label="Notifications"]>i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            vertical-align: middle !important;
        }

        body.portal-ui #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] #notifications-unread-count {
            position: absolute !important;
            width: 1.25rem !important;
            height: 1.25rem !important;
            min-width: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            padding: 0 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-size: .75rem !important;
            line-height: 1 !important;
            font-weight: 700 !important;
            border: 2px solid #C41E3A !important;
            border-radius: 9999px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2) !important;
        }

        #mobileTopBar,
        #mobileMenuToggle {
            display: none !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) > div {
            display: flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding: .75rem 1rem .75rem .75rem !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) > div > div:first-child {
            display: flex !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: .5rem !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) :is(#pageMobileMenuToggle, #dashboardMenuToggle) {
            display: inline-flex !important;
            flex: 0 0 2.25rem !important;
            width: 2.25rem !important;
            height: 2.25rem !important;
            min-width: 2.25rem !important;
            min-height: 2.25rem !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            color: #ffffff !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) > div > div:first-child > div {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            width: auto !important;
            margin: 0 !important;
            text-align: left !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) :is(h1, h2, h3) {
            margin: 0 !important;
            font-size: 1.05rem !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            text-align: left !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) p {
            display: block !important;
            margin: .125rem 0 0 !important;
            font-size: .6875rem !important;
            line-height: 1.2 !important;
            color: rgba(255, 255, 255, .85) !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) > div > div:last-child {
            flex: 0 0 auto !important;
            margin-left: .5rem !important;
        }

        /* Keep report tables on larger screens, but use the existing card
           markup for every report section on phone-sized screens. */
        #abuse-type-classification .abuse-type-table-view,
        #case-status-breakdown .case-status-table-view,
        #case-records .case-records-table-view {
            display: none !important;
        }

        #abuse-type-mobile-cards,
        #case-status-mobile-cards,
        #case-records-mobile-cards {
            display: block !important;
            padding: .75rem !important;
        }

        #abuse-type-mobile-cards > article,
        #case-status-mobile-cards > article,
        #case-records-mobile-cards > article {
            min-width: 0;
            padding: 1rem;
        }

        #abuse-type-mobile-cards p,
        #case-status-mobile-cards p,
        #case-records-mobile-cards p,
        #case-records-mobile-cards span {
            overflow-wrap: anywhere;
        }
    }
</style>
@endif
@if(request()->routeIs('desk-officer.quarterly-reports.show'))
<style>
    @if(request()->boolean('archived') && request()->boolean('archive_report_preview') && in_array($report->type ?? 'VAWC', ['BCPC', 'VAWC', 'SWD'], true))
    @media (max-width: 47.9375rem) {
        .quarterly-report-detail-header {
            display: none !important;
        }
    }
    @endif
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        .quarterly-report-type-label {
            justify-content: center;
            text-align: center;
        }
        #case-status-breakdown .case-status-table-view { display: none !important; }
        #case-status-mobile-cards { display: block !important; padding: .75rem; }
        #case-status-mobile-cards > article {
            min-width: 0;
            padding: 1rem;
        }
    }
</style>
@endif
@if(!$isAdmin && auth()->user()?->defaultCaseDepartment() === 'BCPC')
<style>
    .swal2-popup .swal2-styled { background-color: #C41E3A !important; color: #ffffff !important; min-width: 7rem; min-height: 2.5rem; border-radius: .5rem; }
    .swal2-popup .swal2-styled:hover { background-color: #A01830 !important; }
</style>
@endif
@if(!$isAdmin && auth()->user()?->position === 'Desk Officer' && (auth()->user()?->isMultiCaseDepartment() || in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
<style>
    @media (max-width: 47.9375rem) {
        #mobileTopBar,
        #mobileMenuToggle {
            display: none !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) > div {
            display: flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding: .75rem 1rem .75rem .75rem !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) > div > div:first-child {
            display: flex !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: .5rem !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) :is(#pageMobileMenuToggle, #dashboardMenuToggle) {
            display: inline-flex !important;
            flex: 0 0 2.25rem !important;
            width: 2.25rem !important;
            height: 2.25rem !important;
            min-width: 2.25rem !important;
            min-height: 2.25rem !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            color: #ffffff !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) > div > div:first-child > div {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            width: auto !important;
            margin: 0 !important;
            text-align: left !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) :is(h1, h2, h3) {
            margin: 0 !important;
            font-size: 1.05rem !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            text-align: left !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) p {
            display: block !important;
            margin: .125rem 0 0 !important;
            font-size: .6875rem !important;
            line-height: 1.2 !important;
            color: rgba(255, 255, 255, .85) !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        #mainContent > header:is(#desk-officer-dashboard-header, .admin-reference-mobile-header) > div > div:last-child {
            flex: 0 0 auto !important;
            margin-left: .5rem !important;
        }
    }
</style>
@endif
@if($isAdmin)
@include('admin-tailwind.quarterly-reports._portrait-sidebar')
<style>
    @if(auth()->user()?->isDepartmentAdmin())
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: landscape) {
        html body.portal-ui.admin-reference-mobile-nav > aside#sidebar:not(.sidebar-collapsed):not(.sidebar-collapsed-init) ~ #sidebarToggleBtn {
            top: 2.75rem !important;
            left: 18rem !important;
            transform: translate(-50%, -50%) !important;
        }
    }
    @endif
    body.portal-ui.archive-report-modal-preview #case-records .case-records-table-view { display: none !important; }
    body.portal-ui.archive-report-modal-preview #case-records-mobile-cards { display: block !important; }
    body.portal-ui.archive-report-modal-preview #case-records-mobile-cards > .case-record-card { padding: 1rem !important; }
    /* Admin phone view: Case Records use cards; tablet and desktop retain
       the original table. */
    @media (max-width: 47.9375rem) {
        #abuse-type-classification .abuse-type-table-view { display: none !important; }
        #abuse-type-mobile-cards { display: block !important; }
        #abuse-type-mobile-cards .abuse-type-card {
            min-width: 0;
            padding: 1rem;
        }
        #abuse-type-mobile-cards .abuse-type-card p { overflow-wrap: anywhere; }
        #case-records .case-records-table-view { display: none !important; }
        #case-records-mobile-cards { display: block !important; }
        #case-records-mobile-cards .case-record-card {
            min-width: 0;
            padding: 1rem;
        }
        #case-records-mobile-cards .case-record-card p,
        #case-records-mobile-cards .case-record-card span { overflow-wrap: anywhere; }
    }
</style>
@endif
@if($isAdmin && in_array($report->type ?? 'VAWC', ['BCPC', 'SWD', 'VAWC'], true))
<style>
    @media (max-width: 47.9375rem) {
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header#desk-officer-dashboard-header > div {
            padding: .75rem 1rem .75rem .75rem !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header#desk-officer-dashboard-header > div > div:first-of-type {
            display: flex !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: .5rem !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle {
            display: inline-flex !important;
            flex: 0 0 2.25rem !important;
            width: 2.25rem !important;
            height: 2.25rem !important;
            min-width: 2.25rem !important;
            min-height: 2.25rem !important;
            margin: 0 !important;
            padding: 0 !important;
            align-items: center !important;
            justify-content: center !important;
            border: 0 !important;
            background: transparent !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header#desk-officer-dashboard-header > div > div:first-of-type > div {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            width: auto !important;
            margin: 0 !important;
            text-align: left !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header#desk-officer-dashboard-header h2 {
            margin: 0 !important;
            font-size: 1.05rem !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;
            color: #ffffff !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header#desk-officer-dashboard-header p {
            margin: .125rem 0 0 !important;
            font-size: .6875rem !important;
            line-height: 1.2 !important;
            color: rgba(255, 255, 255, .85) !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header#desk-officer-dashboard-header > div > div:last-child {
            flex: 0 0 auto !important;
            margin-left: .5rem !important;
        }
    }

    @media (max-width: 47.9375rem) and (orientation: portrait) {
        .quarterly-report-type-label {
            justify-content: center;
            text-align: center;
        }
        #abuse-type-classification .abuse-type-table-view,
        #case-records .case-records-table-view,
        #case-status-breakdown .case-status-table-view { display: none !important; }
        #abuse-type-mobile-cards,
        #case-records-mobile-cards,
        #case-status-mobile-cards { display: block !important; }
        :is(#abuse-type-mobile-cards, #case-records-mobile-cards, #case-status-mobile-cards) > article {
            min-width: 0;
            padding: 1rem;
        }
        #case-status-mobile-cards { padding: .75rem; }
        #case-status-breakdown { overflow: hidden; }
        #case-status-breakdown table {
            width: 100%;
            table-layout: fixed;
        }
        #case-status-breakdown th,
        #case-status-breakdown td {
            min-width: 0;
            padding: .75rem .5rem;
            overflow-wrap: anywhere;
        }
        #case-status-breakdown :is(th, td):nth-child(1) { width: 52%; }
        #case-status-breakdown :is(th, td):nth-child(2) { width: 25%; }
        #case-status-breakdown :is(th, td):nth-child(3) {
            width: 23%;
            white-space: nowrap;
        }
    }
</style>
@endif
@if(!$isAdmin)
@include('components.desk-officer.bcpc-dashboard-header', [
    'title' => str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') . ' Quarterly Compliance Report',
    'subtitle' => 'Barangay ' . ($barangay->name ?? 'N/A') . ' • Period: Q' . $report->quarter . ' ' . $report->year,
    'titleClass' => (request()->boolean('archived') || $report->archived_at) ? 'flex-1 min-w-0' : '',
])
@endif
@if($isAdmin && (auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin()))
<style>
    @media (min-width: 48rem) {
        body.portal-ui.admin-reference-mobile-nav #mobileTopBar { display: none !important; }
    }
    @media (min-width: 48rem) and (max-width: 80rem) {
        body.portal-ui.admin-reference-mobile-nav #sidebar {
            inset: 0 auto 0 0 !important;
            height: 100dvh !important;
        }
        body.portal-ui.admin-reference-mobile-nav #sidebarToggleBtn { top: 1.5rem !important; }
    }
    @media (max-width: 40rem) {
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div {
            column-gap: .75rem !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header > div > .admin-reference-mobile-leading {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header .admin-reference-mobile-title {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            text-align: left !important;
        }
        html body.portal-ui.admin-reference-mobile-nav #mainContent > header.admin-reference-mobile-header #dashboardMenuToggle {
            display: inline-flex !important;
            position: relative !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }
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
<header class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0 {{ request()->boolean('archived') ? 'flex-1' : '' }}">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }} Quarterly Compliance Report</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">Barangay {{ $barangay->name ?? 'N/A' }} &bull; Period: Q{{ $report->quarter }} {{ $report->year }}</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8" data-archive-report-title="{{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }} Quarterly Compliance Report" data-archive-report-subtitle="Barangay {{ $barangay->name ?? 'N/A' }} • Period: Q{{ $report->quarter }} {{ $report->year }}">
    <!-- Header -->
    <div class="quarterly-report-detail-header mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="{{ (($isAdmin && request()->boolean('archived')) || (!$isAdmin && $report->archived_at)) ? 'flex-1 min-w-0' : '' }}">
            <div class="quarterly-report-navigation-row flex items-center gap-2 mb-2">
                @if($isAdmin && auth()->user()?->isDepartmentAdmin() && request('from') === 'edit' && !request()->boolean('archived'))
                <a href="{{ route('admin.quarterly-reports.edit', $report->id) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-purple-600 hover:text-purple-800 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Edit Quarterly Reports</span>
                </a>
                @else
                <a href="{{ ($isAdmin && request()->boolean('archived')) || (!$isAdmin && $report->archived_at) ? ($isAdmin ? route('admin.quarterly-reports.archived', ['section' => 'quarterly']) : route('desk-officer.quarterly-reports.archived')) : ($isAdmin ? route('admin.quarterly-reports.index') : route('desk-officer.quarterly-reports.index')) }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-purple-600 hover:text-purple-800 transition">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ ($isAdmin && request()->boolean('archived')) || (!$isAdmin && $report->archived_at) ? 'Back to Archived Quarterly Reports' : 'Back to Quarterly Reports' }}</span>
                </a>
                @endif
                @unless($isAdmin && ($report->type ?? 'VAWC') === 'BCPC')
                <span class="text-gray-300">|</span>
                <span class="quarterly-report-type-label inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ ($report->type ?? 'VAWC') === 'BCPC' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700' }}">
                    {{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }} Report
                </span>
                @endunless
            @if($report->archived_at && !request()->boolean('archived'))
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                    Archived
                </span>
                @endif
            </div>
            @if($isAdmin && !(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin()))
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                {{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }} Quarterly Compliance Report
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Barangay <strong>{{ $barangay->name ?? 'N/A' }}</strong> &bull; Period: <strong>Q{{ $report->quarter }} {{ $report->year }}</strong>
            </p>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            @php
                $isReportArchived = !empty($report->archived_at) || request()->boolean('archived');
            @endphp
            @if(!$isAdmin && !$isReportArchived)
                @if(($report->type ?? 'VAWC') !== 'BCPC' && auth()->user()?->defaultCaseDepartment() !== 'VAWC' && !auth()->user()?->isMultiCaseDepartment())
                <button type="button"
                        onclick="sendReportToAdmin({{ $report->id }}, 'Q{{ $report->quarter }} {{ $report->year }} ({{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }})')"
                        class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-initial px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-xl text-xs sm:text-sm font-medium shadow-sm transition">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send to Admin</span>
                </button>
                @endif
            @endif

            @if($isReportArchived)
                @if(!($isAdmin && in_array($report->type ?? 'VAWC', ['VAWC', 'SWD', 'BCPC'], true)))
                @unless(!$isAdmin && auth()->user()?->position === 'Desk Officer' && (auth()->user()?->isMultiCaseDepartment() || in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
                <button type="button" disabled @if(!$isAdmin && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) && !auth()->user()?->isMultiCaseDepartment()) style="width: auto !important; flex: 0 0 auto !important; white-space: nowrap;" @endif title="Print is unavailable for archived reports" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-initial px-4 py-2 bg-red-100 text-red-800 rounded-xl text-xs sm:text-sm font-medium cursor-not-allowed shadow-sm">
                    <i class="fas fa-print"></i>
                    <span>Print Report</span>
                </button>
                @endunless
                @endif
                @if(!$isAdmin && !request()->boolean('archived'))
                <form method="POST" class="restore-confirm-form inline-block" action="{{ route('desk-officer.quarterly-reports.restore', $report->id) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-initial px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-xs sm:text-sm font-medium shadow-sm transition">
                        <i class="fas fa-undo"></i>
                        <span>Restore Report</span>
                    </button>
                </form>
                @endif
            @else
                <button type="button" id="print-quarterly-report" class="inline-flex items-center justify-center gap-2 flex-1 sm:flex-initial px-4 py-2 bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-xl text-xs sm:text-sm font-medium shadow-sm transition">
                    <i class="fas fa-print"></i>
                    <span>Print Report</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Summary Banner -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-purple-50/70 border border-purple-100 rounded-xl p-3.5">
                <span class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Total Cases Recorded</span>
                <div class="text-2xl sm:text-3xl font-black text-purple-900 mt-0.5">
                    {{ $report->report_data['total_cases'] ?? 0 }}
                </div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Period Covered</span>
                <div class="text-base font-bold text-gray-800 mt-1">
                    Quarter {{ $report->quarter }}, {{ $report->year }}
                </div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Barangay</span>
                <div class="text-base font-bold text-gray-800 mt-1">
                    {{ $barangay->name ?? 'N/A' }}
                </div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Generated / Recorded</span>
                <div class="text-base font-bold text-gray-800 mt-1">
                    {{ ($report->updated_at ?? $report->created_at)->timezone('Asia/Manila')->format('M d, Y') }}
                </div>
                <div class="text-xs text-gray-400">{{ ($report->updated_at ?? $report->created_at)->timezone('Asia/Manila')->format('h:i A') }}</div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Abuse Types Breakdown Table -->
        <div id="abuse-type-classification" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">
                <h2 class="font-bold text-gray-800 text-base md:text-lg flex items-center gap-2">
                    <span class="w-2 h-5 bg-purple-600 rounded-full inline-block"></span>
                    Abuse Type Classification
                </h2>
                <p class="text-sm text-gray-500 mt-1 pl-4">Distribution of cases categorized by Republic Act and nature of offense.</p>
            </div>
            @php
                $abuseCounts = $report->report_data['abuse_type_counts'] ?? [];
                $totalCases = max(1, (int)($report->report_data['total_cases'] ?? 0));
            @endphp
            <div class="abuse-type-table-view">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-purple-50/50 text-gray-700">
                        <tr>
                            <th class="border border-gray-200 px-5 py-3 text-left font-semibold">Abuse Type / Republic Act</th>
                            <th class="border border-gray-200 px-5 py-3 text-center font-semibold w-40">Number of Cases</th>
                            <th class="border border-gray-200 px-5 py-3 text-center font-semibold w-40">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($abuseCounts as $abuseType => $count)
                            <tr class="hover:bg-gray-50/80">
                                <td class="border border-gray-200 px-5 py-3 font-medium text-gray-800">{{ $abuseType }}</td>
                                <td class="border border-gray-200 px-5 py-3 text-center font-bold text-gray-800">{{ $count }}</td>
                                <td class="border border-gray-200 px-5 py-3 text-center text-gray-600">
                                    {{ number_format(($count / $totalCases) * 100, 1) }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="border border-gray-200 px-5 py-6 text-center text-gray-400">No abuse type breakdown available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>

            <!-- Admin phone-only abuse classification cards -->
            <div id="abuse-type-mobile-cards" class="hidden p-3 space-y-3">
                @forelse($abuseCounts as $abuseType => $count)
                <article class="abuse-type-card bg-white rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-xs text-gray-500 mb-1">Abuse Type / Republic Act</p>
                    <p class="font-medium text-gray-800 mb-4">{{ $abuseType }}</p>
                    <div class="grid grid-cols-2 gap-3 text-sm border-t border-gray-100 pt-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Number of Cases</p>
                            <p class="font-bold text-gray-800">{{ $count }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Percentage</p>
                            <p class="font-medium text-gray-700">{{ number_format(($count / $totalCases) * 100, 1) }}%</p>
                        </div>
                    </div>
                </article>
                @empty
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-sm text-gray-400">No abuse type breakdown available</div>
                @endforelse
            </div>
        </div>

        <!-- Case Status Breakdown Table -->
        <div id="case-status-breakdown" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">
                <h2 class="font-bold text-gray-800 text-base md:text-lg flex items-center gap-2">
                    <span class="w-2 h-5 bg-purple-600 rounded-full inline-block"></span>
                    Case Status Breakdown
                </h2>
                <p class="text-sm text-gray-500 mt-1 pl-4">Current progression and disposition of reported cases.</p>
            </div>
            <div class="case-status-table-view overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-purple-50/50 text-gray-700">
                        <tr>
                            <th class="border border-gray-200 px-5 py-3 text-left font-semibold">Case Status</th>
                            <th class="border border-gray-200 px-5 py-3 text-center font-semibold w-40">Number of Cases</th>
                            <th class="border border-gray-200 px-5 py-3 text-center font-semibold w-40">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $statusCounts = $report->report_data['status_counts'] ?? [];
                        @endphp
                        @forelse($statusCounts as $status => $count)
                            <tr class="hover:bg-gray-50/80">
                                <td class="border border-gray-200 px-5 py-3 font-medium text-gray-800">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="border border-gray-200 px-5 py-3 text-center font-bold text-gray-800">{{ $count }}</td>
                                <td class="border border-gray-200 px-5 py-3 text-center text-gray-600">
                                    {{ number_format(($count / $totalCases) * 100, 1) }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="border border-gray-200 px-5 py-6 text-center text-gray-400">No status breakdown available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="case-status-mobile-cards" class="hidden space-y-3">
                @forelse($statusCounts as $status => $count)
                <article class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span>{{ $status }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-4 pt-3 border-t border-gray-100 text-sm">
                        <div><p class="text-xs text-gray-500 mb-1">Number of Cases</p><p class="font-bold text-gray-800">{{ $count }}</p></div>
                        <div><p class="text-xs text-gray-500 mb-1">Percentage</p><p class="font-medium text-gray-700">{{ number_format(($count / $totalCases) * 100, 1) }}%</p></div>
                    </div>
                </article>
                @empty
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-sm text-gray-400">No status breakdown available</div>
                @endforelse
            </div>
        </div>

        <!-- Detailed Cases Table -->
        @if(!empty($report->report_data['cases']))
            <div id="case-records" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">
                    <h2 class="font-bold text-gray-800 text-base md:text-lg flex items-center gap-2">
                        <span class="w-2 h-5 bg-purple-600 rounded-full inline-block"></span>
                        Case Records for Q{{ $report->quarter }} {{ $report->year }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 pl-4">Detailed records included in this quarterly period.</p>
                </div>
                <div class="case-records-table-view">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-purple-50/50 text-gray-700">
                            <tr>
                                <th class="border border-gray-200 px-4 py-3 text-left font-semibold">Victim / Complainant</th>
                                <th class="border border-gray-200 px-4 py-3 text-left font-semibold">Abuse Classification</th>
                                <th class="border border-gray-200 px-4 py-3 text-center font-semibold">Current Status</th>
                                <th class="border border-gray-200 px-4 py-3 text-center font-semibold">Date Reported</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($caseRecords as $case)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="border border-gray-200 px-4 py-2.5 font-medium text-gray-800">
                                        {{ $case['victim_name'] ?? 'N/A' }}
                                    </td>
                                    <td class="border border-gray-200 px-4 py-2.5 text-gray-700">
                                        {{ $case['abuse_type_name'] ?? $case['abuse_type'] ?? 'N/A' }}
                                    </td>
                                    <td class="border border-gray-200 px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $case['status'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="border border-gray-200 px-4 py-2.5 text-center text-gray-600 text-xs">
                                        {{ $case['date_reported'] ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($caseRecords->hasPages())
                    @include('components.archived-pagination', ['paginator' => $caseRecords, 'label' => 'case records'])
                @endif
                </div>

                <!-- Admin phone-only case record cards -->
                <div id="case-records-mobile-cards" class="hidden p-3 space-y-3">
                    @foreach($caseRecords as $case)
                    <article class="case-record-card bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 mb-1">Victim / Complainant</p>
                                <p class="font-semibold text-gray-800">{{ $case['victim_name'] ?? 'N/A' }}</p>
                            </div>
                            <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                {{ $case['status'] ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Abuse Classification</p>
                                <p class="text-gray-700">{{ $case['abuse_type_name'] ?? $case['abuse_type'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Date Reported</p>
                                <p class="text-gray-700">{{ $case['date_reported'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </article>
                    @endforeach
                    @if($caseRecords->hasPages())
                        @include('components.archived-pagination', ['paginator' => $caseRecords, 'label' => 'case records'])
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@if(!$isAdmin)
<script>
const quarterlyPrintButton = document.getElementById('print-quarterly-report');
if (quarterlyPrintButton) {
    quarterlyPrintButton.addEventListener('click', () => {
        const openPreview = () => window.open(@json(route('desk-officer.quarterly-reports.download', ['id' => $report->id, 'preview' => 1])), '_blank', 'noopener');
        if (typeof Swal === 'undefined') return openPreview();
        Swal.fire({
            title: 'Print Report?',
            text: 'A print preview will open before you print or download the report.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Open Preview',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#C41E3A'
        }).then(result => { if (result.isConfirmed) openPreview(); });
    });
}

function sendReportToAdmin(id, period) {
    Swal.fire({
        title: 'Send to Admin?',
        html: `
            <div class="text-center py-2">
                <i class="fas fa-paper-plane text-5xl text-emerald-500 mb-4"></i>
                <p class="text-gray-700 font-medium">Are you sure you want to send the quarterly report for <strong>${period}</strong> to the Administrator?</p>
                <p class="text-gray-400 text-xs mt-2">This report will be submitted directly to Admin for compliance monitoring.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Yes, Send to Admin',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sending to Admin...',
                text: 'Please wait while we submit the report to Admin.',
                allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`{{ url('desk-officer/quarterly-reports') }}/${id}/send-to-admin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sent Successfully!',
                        text: data.message || 'Report has been sent to Admin.',
                        confirmButtonColor: '#059669'
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to send report to Admin.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'An error occurred while sending report to Admin.', 'error'));
        }
    });
}
document.querySelectorAll('.restore-confirm-form').forEach(form => form.addEventListener('submit', async event => {
    event.preventDefault();
    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            title: 'Restore archived report?',
            text: 'This report will be returned to the active reports list.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Restore',
            confirmButtonColor: '#7c3aed'
        });
        if (result.isConfirmed) form.submit();
    } else {
        if (confirm('Restore this report to the active reports list?')) form.submit();
    }
}));
</script>
@endif
@if($isAdmin)
<script>
const quarterlyPrintButton = document.getElementById('print-quarterly-report');
if (quarterlyPrintButton) {
    quarterlyPrintButton.addEventListener('click', () => {
        const openPreview = () => window.open(@json(route('admin.quarterly-reports.download', ['id' => $report->id, 'preview' => 1])), '_blank', 'noopener');
        if (typeof Swal === 'undefined') return openPreview();
        Swal.fire({
            title: 'Print Report?',
            text: 'A print preview will open before you print or download the report.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Open Preview',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#C41E3A'
        }).then(result => { if (result.isConfirmed) openPreview(); });
    });
}
</script>
@endif
@endsection
