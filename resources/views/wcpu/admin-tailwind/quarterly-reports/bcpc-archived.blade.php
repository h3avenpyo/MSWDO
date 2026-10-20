@extends('layouts.admin-tailwind')
@section('title', request('section') === 'barangay-cases' ? 'Archived Barangay Cases' : (auth()->user()->isVawcAdmin() ? 'Archived Case Reports' : 'Archived BCPC Reports'))
@push('styles')
<style>
    .archived-static-table { table-layout: fixed; }
    .archived-static-table th:last-child, .archived-static-table td:last-child { width: 15rem; white-space: nowrap; }
    @media (max-width: 47.9375rem) {
        .archived-barangay-cases-section .archived-barangay-cases-table-view { display: none !important; }
        .archived-barangay-cases-section .archived-barangay-cases-mobile-cards { display: block !important; }
        .archived-barangay-cases-mobile-cards .archived-barangay-case-card { min-width: 0; padding: 1rem; }
        .archived-barangay-cases-mobile-cards .archived-barangay-case-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        .archived-barangay-cases-mobile-cards .archived-barangay-case-actions > form { display: contents; }
        .archived-barangay-cases-mobile-cards .archived-barangay-case-actions > :is(a, button),
        .archived-barangay-cases-mobile-cards .archived-barangay-case-actions > form > button {
            width: 100% !important;
            box-sizing: border-box;
            max-width: 100% !important;
            min-width: 0 !important;
            min-height: 3rem;
            padding: .5rem !important;
            justify-content: center;
        }
        .archived-barangay-cases-mobile-cards .delete-confirm-form > button {
            background-color: #C41E3A !important;
        }
        .archived-barangay-cases-mobile-cards .delete-confirm-form > button:hover {
            background-color: #A01830 !important;
        }
        .archived-barangay-cases-mobile-cards .restore-confirm-form > button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0 !important;
        }
        .archived-barangay-cases-mobile-cards .restore-confirm-form > button i {
            font-size: 1rem !important;
        }
    }
</style>
@if(auth()->user()->isDepartmentAdmin())
<style>
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

    /* Archived Quarterly Reports tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
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
@endif
@endpush
@section('content')
@if(auth()->user()->isDepartmentAdmin())
<header class="{{ request('section') === 'barangay-cases' ? 'admin-reference-mobile-header' : '' }} sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="{{ request('section') === 'barangay-cases' ? 'admin-reference-mobile-leading min-w-0' : '' }}">
            @if(request('section') === 'barangay-cases')
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            @endif
            <div class="{{ request('section') === 'barangay-cases' ? 'admin-reference-mobile-title' : '' }}">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ request('section') === 'barangay-cases' ? 'Archived Barangay Cases' : 'Archived Case Reports' }}</h2>
                <p class="{{ request('section') === 'barangay-cases' ? 'admin-reference-mobile-subtitle' : '' }} text-sm text-gray-500 hidden sm:block">{{ request('section') === 'barangay-cases' ? 'View all archived barangay cases' : 'View and Manage archived case reports.' }}</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
<div class="p-4 sm:p-6 space-y-6">
    @unless(auth()->user()->isDepartmentAdmin())
    <h1 class="text-2xl font-bold text-gray-800">Archived BCPC Reports</h1>
    @endunless
    @if(session('success'))<p class="p-4 bg-green-50 text-green-700 rounded-lg">{{ session('success') }}</p>@endif
    @if(request('section') === 'barangay-cases')
    <form id="archived-case-filter-form" method="GET" action="{{ route('admin.quarterly-reports.archived') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
        <input type="hidden" name="section" value="barangay-cases">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:flex xl:items-center gap-2 sm:gap-3">
            <div class="relative min-w-0 sm:col-span-2 xl:flex-1 xl:min-w-[240px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input id="archived-case-search" type="search" name="search" value="{{ $archivedCaseSearch ?? request('search') }}" placeholder="Search archived cases..." class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <select name="quarter" data-archived-case-filter class="w-full xl:w-[130px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Quarters</option>
                @foreach([1, 2, 3, 4] as $quarter)<option value="{{ $quarter }}" {{ (string) ($archivedCaseQuarter ?? request('quarter')) === (string) $quarter ? 'selected' : '' }}>Q{{ $quarter }}</option>@endforeach
            </select>
            <select name="year" data-archived-case-filter class="w-full xl:w-[115px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Years</option>
                @foreach(range(2026, 2030) as $year)<option value="{{ $year }}" {{ (string) ($archivedCaseYear ?? request('year')) === (string) $year ? 'selected' : '' }}>{{ $year }}</option>@endforeach
            </select>
            <select name="barangay_id" data-archived-case-filter class="w-full xl:w-[160px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Barangays</option>
                @foreach($barangays as $barangay)<option value="{{ $barangay->id }}" {{ (string) ($archivedCaseBarangayId ?? request('barangay_id')) === (string) $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>@endforeach
            </select>
            <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0"><i class="fas fa-filter"></i><span>Filter</span></button>
            @if(request()->filled('search') || request()->filled('quarter') || request()->filled('year') || request()->filled('barangay_id'))<a href="{{ route('admin.quarterly-reports.archived', ['section' => 'barangay-cases']) }}" class="inline-flex h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition shrink-0"><i class="fas fa-times"></i><span>Clear</span></a>@endif
        </div>
    </form>
    @elseif(auth()->user()->isVawcAdmin())
    <form id="archived-case-filter-form" method="GET" action="{{ route('admin.quarterly-reports.archived') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:flex xl:items-center gap-2 sm:gap-3">
            <div class="relative min-w-0 sm:col-span-2 xl:flex-1 xl:min-w-[240px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" aria-hidden="true"></i>
                <input id="archived-case-search" type="search" name="search" value="{{ $archivedCaseSearch ?? request('search') }}" placeholder="Search archived cases..." class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500" aria-label="Search archived barangay cases">
            </div>
            <select name="quarter" data-archived-case-filter class="w-full xl:w-[130px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700" aria-label="Filter by quarter">
                <option value="">All Quarters</option>
                @foreach([1, 2, 3, 4] as $quarter)<option value="{{ $quarter }}" {{ (string) ($archivedCaseQuarter ?? request('quarter')) === (string) $quarter ? 'selected' : '' }}>Q{{ $quarter }}</option>@endforeach
            </select>
            <select name="year" data-archived-case-filter class="w-full xl:w-[115px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700" aria-label="Filter by year">
                <option value="">All Years</option>
                @foreach(range(2026, 2030) as $year)<option value="{{ $year }}" {{ (string) ($archivedCaseYear ?? request('year')) === (string) $year ? 'selected' : '' }}>{{ $year }}</option>@endforeach
            </select>
            <select name="barangay_id" data-archived-case-filter class="w-full xl:w-[160px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700" aria-label="Filter by barangay">
                <option value="">All Barangays</option>
                @foreach($barangays as $barangay)<option value="{{ $barangay->id }}" {{ (string) ($archivedCaseBarangayId ?? request('barangay_id')) === (string) $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>@endforeach
            </select>
            <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0"><i class="fas fa-filter"></i><span>Filter</span></button>
        </div>
    </form>
    @endif
    @foreach((auth()->user()->isVawcAdmin() ? ['Archived Barangay Cases' => $barangayCases] : ['Archived Barangay Cases' => $barangayCases]) as $heading => $records)
        @php $isMonitoring = $heading === 'BCPC Monitoring Reports'; @endphp
        <section id="archived-barangay-cases-table" class="archived-barangay-cases-section bg-white border border-gray-200 rounded-xl overflow-hidden">
            <h2 class="text-lg font-semibold px-5 py-4 text-purple-800">{{ $heading }}</h2>
            <div class="archived-barangay-cases-table-view">
            <div class="overflow-x-auto">
                <table class="archived-static-table w-full text-sm text-left">
                    <thead class="bg-purple-700 text-white"><tr>
                        @if($heading === 'Archived Barangay Cases')
                        <th class="px-5 py-3">Case Number</th><th class="px-5 py-3">Barangay</th><th class="px-5 py-3">Victim</th><th class="px-5 py-3">Archived At</th><th class="px-5 py-3 text-center">Actions</th>
                        @else
                        <th class="px-5 py-3">Barangay</th><th class="px-5 py-3">Period</th><th class="px-5 py-3">Archived At</th><th class="px-5 py-3 text-center">Actions</th>
                        @endif
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($records as $record)
                            <tr>
                                @if($heading === 'Archived Barangay Cases')
                                <td class="px-5 py-4">{{ $record->case_number }}</td>
                                <td class="px-5 py-4">{{ $record->barangay?->name ?? 'N/A' }}</td>
                                <td class="px-5 py-4">{{ $record->victim_name }}</td>
                                <td class="px-5 py-4">{{ $record->deleted_at?->format('M d, Y h:i A') }}</td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-[#C41E3A] text-white no-underline hover:bg-[#A01830] active:bg-[#8B1429] transition-colors" href="{{ route('cases.show', ['id' => $record->id, 'archived' => 1]) }}" data-admin-archive-view="case" data-swd-archive-view="case" data-modal-title="Case Details" title="View Case"><i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View</span></a>
                                        @if($record->trashed())
                                        <form method="POST" action="{{ route('cases.restore', $record->id) }}" class="restore-confirm-form">
                                            @csrf
                                            <button type="submit" class="px-3 py-2 rounded-lg text-white transition-colors" style="background-color: #C41E3A !important;">Restore</button>
                                        </form>
                                        @elseif(auth()->user()->isVawcAdmin())
                                        <span class="px-3 py-2 rounded-lg bg-green-100 text-green-700 text-sm font-medium">Restored</span>
                                        @endif
                                    </div>
                                </td>
                                @else
                                <td class="px-5 py-4">{{ $record->barangay?->name ?? 'N/A' }}</td>
                                <td class="px-5 py-4">Q{{ $record->quarter }} {{ $record->year }}</td>
                                <td class="px-5 py-4">{{ $record->archived_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}</td>
                                <td class="px-5 py-4 text-center">
                                    @php($restoreBlocked = $isMonitoring
                                        ? (($activeMonitoringPeriods ?? [])["{$record->quarter}-{$record->year}"] ?? false)
                                        : (auth()->user()->isDepartmentAdmin() && ($activeReportPeriods["{$record->quarter}-{$record->year}"] ?? false)))
                                    <div class="flex items-center justify-center gap-3">
                                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-[#C41E3A] text-white hover:bg-[#A01830] active:bg-[#8B1429] transition-colors" href="{{ $isMonitoring ? route('admin.bcpc-case-monitoring.edit', ['view' => $record->id, 'archived' => 1]) : route('admin.quarterly-reports.show', ['id' => $record->id, 'archived' => 1]) }}" data-admin-archive-view="{{ $isMonitoring ? 'monitoring' : 'report' }}" data-modal-title="{{ $isMonitoring ? 'BCPC Monitoring Report Details' : 'Quarterly Report Details' }}" title="View Details" aria-label="View Details"><i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View Details</span></a>
                                        <form method="POST" class="restore-confirm-form" action="{{ $isMonitoring ? route('admin.bcpc-case-monitoring.restore', $record->id) : route('admin.quarterly-reports.restore', $record->id) }}">
                                            @csrf
                                            <button type="submit" @disabled($restoreBlocked) class="px-3 py-2 rounded-lg {{ $restoreBlocked ? 'bg-red-100 text-red-800 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 text-white' }} transition-colors">Restore</button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="{{ $heading === 'Archived Barangay Cases' ? 5 : 4 }}" class="text-center text-gray-500 px-5 py-8">No archived {{ strtolower($heading) }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($records->hasPages())
                <div data-archived-case-pagination class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">Showing {{ $records->firstItem() ?? 0 }} - {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} reports</div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $records->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $records->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"><i class="fas fa-chevron-left text-gray-600"></i></a>
                        @php($current = $records->currentPage())
                        @php($last = $records->lastPage())
                        @php($start = max(1, $current - 2))
                        @php($end = min($last, $current + 2))
                        @if($start > 1)<a href="{{ $records->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">1</a>@if($start > 2)<span class="px-2 text-gray-400">...</span>@endif @endif
                        @for($page = $start; $page <= $end; $page++)
                            <a href="{{ $records->url($page) }}" class="inline-flex items-center justify-center w-9 h-9 {{ $current == $page ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} rounded-lg text-sm">{{ $page }}</a>
                        @endfor
                        @if($end < $last) @if($end < $last - 1)<span class="px-2 text-gray-400">...</span>@endif <a href="{{ $records->url($last) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">{{ $last }}</a>@endif
                        <a href="{{ $records->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $records->currentPage() == $records->lastPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"><i class="fas fa-chevron-right text-gray-600"></i></a>
                    </div>
                </div>
            @endif
            </div>

            @if($heading === 'Archived Barangay Cases')
            <div class="archived-barangay-cases-mobile-cards hidden p-3 space-y-3">
                @forelse($records as $record)
                <article class="archived-barangay-case-card bg-white rounded-xl border border-gray-200 shadow-sm" data-id="{{ $record->id }}">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Case Number</p>
                            <p class="font-semibold text-gray-800 break-words">{{ $record->case_number }}</p>
                        </div>
                        <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-700">{{ $record->status }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Barangay</p>
                            <p class="font-medium text-gray-800 break-words">{{ $record->barangay?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-gray-500 mb-1">Victim</p>
                            <p class="text-gray-700 break-words">{{ $record->victim_name }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500 mb-1">Archived At</p>
                            <p class="text-gray-700">{{ $record->deleted_at?->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="archived-barangay-case-actions pt-3 border-t border-gray-100">
                        <a class="inline-flex items-center gap-1 rounded-lg bg-[#C41E3A] text-white text-xs font-medium no-underline hover:bg-[#A01830]" href="{{ route('cases.show', ['id' => $record->id, 'archived' => 1]) }}" data-admin-archive-view="case" data-swd-archive-view="case" data-modal-title="Case Details" title="View Case"><i class="fas fa-eye" aria-hidden="true"></i><span>View</span></a>
                        @if($record->trashed())
                        <form method="POST" action="{{ route('cases.restore', $record->id) }}" class="restore-confirm-form">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-[#C41E3A] text-white text-xs font-medium" style="background-color: #C41E3A !important;" title="Restore" aria-label="Restore"><i class="fas fa-undo" aria-hidden="true"></i></button>
                        </form>
                        @elseif(auth()->user()->isVawcAdmin())
                        <span class="col-span-2 inline-flex min-h-12 items-center justify-center rounded-lg bg-green-100 text-green-700 text-sm font-medium">Restored</span>
                        @endif
                    </div>
                </article>
                @empty
                <div class="rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-500">No archived barangay cases.</div>
                @endforelse
                @if($records->hasPages())
                    @include('components.archived-pagination', ['paginator' => $records, 'label' => 'archived cases'])
                @endif
            </div>
            @endif
        </section>
    @endforeach
</div>
@push('scripts')
@include('admin-tailwind.components.archive-view-modal')
<script>
document.querySelectorAll('.restore-confirm-form').forEach(form => form.addEventListener('submit', async event => {
    event.preventDefault();
    const result = await Swal.fire({
        title: 'Restore archived record?',
        text: 'This record will be returned to the active reports list.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Restore',
        confirmButtonColor: '#7c3aed'
    });
    if (result.isConfirmed) {
        Swal.fire({ title: 'Restoring...', text: 'Please wait while this archived record is restored.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        const isSwdArchivedCase = @json(auth()->user()->isVawcAdmin()) && form.action.includes('/cases/');
        if (isSwdArchivedCase) {
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) throw new Error(data.message || 'Failed to restore archived case.');

                form.closest('tr, .archived-barangay-case-card')?.remove();
                Swal.fire({ icon: 'success', title: 'Restored!', text: data.message, confirmButtonText: 'OK' });
            } catch (error) {
                Swal.fire('Error', error.message || 'Failed to restore archived case.', 'error');
            }
            return;
        }
        if (form.dataset.ajaxDelete !== 'true') {
            form.submit();
            return;
        }
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) throw new Error(data.message || 'Failed to permanently delete archived monitoring report.');
            return data;
        })
        .then(data => Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, confirmButtonText: 'OK' }))
        .then(() => window.location.reload())
        .catch(error => Swal.fire('Error', error.message || 'Failed to permanently delete archived monitoring report.', 'error'));
    }
}));
document.querySelectorAll('.delete-confirm-form').forEach(form => form.addEventListener('submit', async event => {
    event.preventDefault();
    const result = await Swal.fire({title: 'Permanently delete archived record?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete', confirmButtonColor: '#dc2626'});
    if (result.isConfirmed) {
        Swal.fire({ title: 'Deleting...', text: 'Please wait while this archived record is deleted.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        form.submit();
    }
}));
document.querySelectorAll('form[data-ajax-delete="true"]').forEach(form => form.addEventListener('submit', async event => {
    event.preventDefault();
    event.stopImmediatePropagation();
    event.stopPropagation();

    const result = await Swal.fire({
        title: 'Delete BCPC Monitoring Report?',
        text: 'This archived report will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#C41E3A'
    });
    if (!result.isConfirmed) return;

    Swal.fire({
        title: 'Deleting...',
        text: 'Please wait while the report is deleted.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to delete the BCPC monitoring report.');
        }

        form.closest('tr, .archived-barangay-case-card')?.remove();
        await Swal.fire({
            icon: 'success',
            title: 'Report Deleted Successfully',
            text: data.message || 'The BCPC monitoring report was deleted successfully.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#C41E3A'
        });
    } catch (error) {
        Swal.fire('Error', error.message || 'Failed to delete the BCPC monitoring report.', 'error');
    }
}));
const archivedCaseFilterForm = document.getElementById('archived-case-filter-form');
const archivedCaseSearch = document.getElementById('archived-case-search');
let archivedCaseSearchTimer;
let archivedCaseRequest;

async function refreshArchivedBarangayCases(url) {
    const table = document.getElementById('archived-barangay-cases-table');
    if (!table) return;

    archivedCaseRequest?.abort();
    archivedCaseRequest = new AbortController();
    table.classList.add('opacity-60', 'pointer-events-none');
    table.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: archivedCaseRequest.signal,
        });
        if (!response.ok) throw new Error('Unable to update archived cases.');

        const documentResponse = new DOMParser().parseFromString(await response.text(), 'text/html');
        const updatedTable = documentResponse.getElementById('archived-barangay-cases-table');
        if (!updatedTable) throw new Error('Unable to update archived cases.');

        table.replaceWith(updatedTable);
        window.history.replaceState({}, '', url);
    } catch (error) {
        if (error.name !== 'AbortError') {
            const activeTable = document.getElementById('archived-barangay-cases-table');
            activeTable?.classList.remove('opacity-60', 'pointer-events-none');
            activeTable?.removeAttribute('aria-busy');
        }
    }
}

function submitArchivedCaseFilters() {
    if (!archivedCaseFilterForm) return;
    const query = new URLSearchParams(new FormData(archivedCaseFilterForm));
    refreshArchivedBarangayCases(`${archivedCaseFilterForm.action}?${query.toString()}`);
}

if (archivedCaseFilterForm && archivedCaseSearch) {
    archivedCaseFilterForm.addEventListener('submit', event => {
        event.preventDefault();
        submitArchivedCaseFilters();
    });
    archivedCaseSearch.addEventListener('input', () => {
        clearTimeout(archivedCaseSearchTimer);
        archivedCaseSearchTimer = setTimeout(submitArchivedCaseFilters, 300);
    });
    archivedCaseFilterForm.querySelectorAll('[data-archived-case-filter]').forEach(filter => {
        filter.addEventListener('change', submitArchivedCaseFilters);
    });
}

document.addEventListener('click', event => {
    const link = event.target.closest('#archived-barangay-cases-table [data-archived-case-pagination] a[href]');
    if (!link || !archivedCaseFilterForm) return;
    event.preventDefault();
    refreshArchivedBarangayCases(link.href);
});

@if(session('success'))
Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), confirmButtonText: 'OK' });
@endif
</script>
@endpush
@endsection
