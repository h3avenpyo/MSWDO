@extends('layouts.admin-tailwind')

@section('title', 'Archived BCPC Case Monitoring - iSERVE SILANG Admin')

@push('styles')
<style>
    @if(auth()->user()?->isDepartmentAdmin())
    @media (max-width: 47.9375rem) {
        html body.portal-ui #archived-bcpc-results > [data-pagination-label] { display: none !important; }
        html body.portal-ui #archived-bcpc-results > .archived-monitoring-tablet-pagination { display: block !important; background: #fff; }
        html body.portal-ui .archived-monitoring-tablet-pagination > [data-pagination-label] {
            flex-direction: column; justify-content: center; gap: .5rem; padding: .75rem; text-align: center;
        }
        html body.portal-ui .archived-monitoring-tablet-pagination [data-pagination-label] > div:last-child {
            justify-content: center; gap: clamp(.25rem, 2vw, .625rem); max-width: 100%;
        }
        html body.portal-ui .archived-monitoring-tablet-pagination a {
            flex: 0 0 auto; width: clamp(2rem, 10vw, 2.75rem) !important; min-width: 0 !important;
            height: 2.75rem !important; padding: 0 !important; margin: 0 !important;
            border: 1px solid #e5e7eb !important; border-radius: .5rem;
            background: #fff !important; color: #C41E3A !important;
            box-shadow: 0 1px 2px rgb(0 0 0 / 4%);
        }
        html body.portal-ui .archived-monitoring-tablet-pagination a[class~="bg-[#C41E3A]"] {
            background: #C41E3A !important; border-color: #C41E3A !important; color: #fff !important;
        }
        html body.portal-ui .archived-monitoring-tablet-pagination a > i { color: inherit !important; }
    }
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui #archived-bcpc-results > [data-pagination-label] { display: none !important; }
        html body.portal-ui #archived-bcpc-results > .archived-monitoring-tablet-pagination { display: block !important; background: #fff; }
        html body.portal-ui .archived-monitoring-tablet-pagination > [data-pagination-label] { display: flex; align-items: center; justify-content: space-between; }
        html body.portal-ui .archived-monitoring-tablet-pagination > [data-pagination-label] > div:last-child { margin-left: auto; justify-content: flex-end; flex-wrap: wrap; }
    }
    @endif
    @if(auth()->user()?->isDepartmentAdmin())
    @media (max-width: 85.375rem) {
        html body.portal-ui .archived-bcpc-monitoring-card-actions > a[data-admin-archive-view="monitoring"] {
            gap: 0 !important;
        }
        html body.portal-ui .archived-bcpc-monitoring-card-actions > a[data-admin-archive-view="monitoring"] > span {
            display: none !important;
        }
    }
    @endif
    @media (min-width: 48rem) and (max-width: 85.375rem) {
        html body.portal-ui:is(.bcpc-admin-header-theme, .swd-admin-header-theme) .archived-bcpc-monitoring-mobile-cards > .archive-empty-state {
            grid-column: 1 / -1;
            width: 100%;
            text-align: center;
        }
    }

    @if(auth()->user()?->isBcpcAdmin())
    @media (max-width: 85.375rem) {
        .archived-monitoring-restore-label { display: none; }
    }
    @endif
    .archived-static-table { table-layout: fixed; }
    .archived-static-table th:last-child, .archived-static-table td:last-child { width: 7rem; white-space: nowrap; }
    @media (min-width: 48rem) and (max-width: 80rem) {
        #archived-bcpc-report-filter-form > div {
            display: grid !important;
            grid-template-columns: minmax(120px, 1.6fr) repeat(3, minmax(90px, 1.15fr)) minmax(72px, .9fr) !important;
            align-items: center !important;
            gap: .5rem !important;
        }
        #archived-bcpc-report-filter-form > div:has(> .flex a) {
            grid-template-columns: minmax(120px, 1.6fr) repeat(3, minmax(90px, 1.15fr)) repeat(2, minmax(72px, .9fr)) !important;
        }
        #archived-bcpc-report-filter-form > div > .relative,
        #archived-bcpc-report-filter-form > div > select,
        #archived-bcpc-report-filter-form > div > .flex {
            grid-column: auto !important;
        }
        #archived-bcpc-report-filter-form > div > .flex { display: contents !important; }
        #archived-bcpc-report-filter-form :is(input, select, button, a) {
            box-sizing: border-box;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            height: 2.5rem !important;
        }
    }
    @media (max-width: 47.9375rem)@if(auth()->user()?->isBcpcAdmin()), (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait)@endif {
        .archived-bcpc-monitoring-table-view { display: none !important; }
        .archived-bcpc-monitoring-mobile-cards { display: grid !important; gap: .75rem; }
        .archived-bcpc-monitoring-mobile-cards .archived-bcpc-monitoring-card { min-width: 0; }
        .archived-bcpc-monitoring-mobile-cards .archived-bcpc-monitoring-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        .archived-bcpc-monitoring-mobile-cards .archived-bcpc-monitoring-card-actions > :is(a, button) {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            min-width: 0;
            height: 2.75rem !important;
            min-height: 2.75rem !important;
            padding: .5rem !important;
        }
    }

    /* Archived BCPC Case Monitoring tablet navigation (768px–1023px): collapsed sidebar on the left, standard header (not top navbar), no collapsed button. */
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
</style>
@endif
@endpush

@section('content')
<header data-archived-bcpc-monitoring-header class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Archived BCPC Case Monitoring</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500 hidden sm:block">View archived BCPC monitoring records</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Info Banner -->
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-xl mb-6 shadow-sm">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
            <p class="text-sm text-amber-800">
                Archived BCPC case monitoring records are excluded from active monitoring tables. You can review submitted form details or restore records to active status anytime.
            </p>
        </div>
    </div>

    <!-- Filters -->
    <form id="archived-bcpc-report-filter-form" method="GET" action="{{ route('admin.bcpc-case-monitoring.archived') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 items-center gap-2 sm:gap-3">
            <div class="relative min-w-0 sm:col-span-2 md:col-span-4 lg:col-span-1"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i><input type="search" name="search" id="archived-bcpc-report-search" value="{{ request('search') }}" placeholder="Search archived reports..." class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></div>
            <select name="quarter" id="archived-bcpc-report-quarter" class="w-full h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Quarters</option>@foreach([1, 2, 3, 4] as $quarterOption)<option value="{{ $quarterOption }}" {{ (string) $quarter === (string) $quarterOption ? 'selected' : '' }}>Q{{ $quarterOption }}</option>@endforeach</select>
            <select name="year" id="archived-bcpc-report-year" class="w-full h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Years</option>@foreach(range(2026, 2030) as $yearOption)<option value="{{ $yearOption }}" {{ (string) $year === (string) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>@endforeach</select>
            <select name="barangay_id" id="archived-bcpc-report-barangay" class="w-full h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Barangays</option>@foreach($barangays as $barangay)<option value="{{ $barangay->id }}" {{ (string) $barangayId === (string) $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>@endforeach</select>
            <div class="flex items-center gap-2 sm:col-span-2 md:col-span-4 lg:col-span-1"><button type="submit" id="archived-bcpc-report-filter-button" class="inline-flex w-full flex-1 h-10 items-center justify-center gap-2 px-3 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0"><i class="fas fa-filter"></i><span>Filter</span></button>
                @if(request()->filled('search') || request()->filled('quarter') || request()->filled('year') || request()->filled('barangay_id'))<a href="{{ route('admin.bcpc-case-monitoring.archived') }}" class="inline-flex w-full flex-1 h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition shrink-0"><i class="fas fa-times"></i><span>Clear</span></a>@endif
            </div>
        </div>
    </form>

    <!-- Table Container -->
    <div id="archived-bcpc-results" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <!-- Desktop Table View -->
        <div class="archived-bcpc-monitoring-table-view overflow-x-auto hidden md:block">
            <table class="archived-static-table w-full min-w-[640px] text-sm table-fixed">
                <colgroup>
                    <col class="w-[28%]">
                    <col class="w-[18%]">
                    <col class="w-[26%]">
                    <col class="w-[18%]">
                    <col class="w-[10%] min-w-[7rem]">
                </colgroup>
                <thead class="bg-gradient-to-r from-amber-700 to-amber-600 text-white">
                    <tr>
                        <th class="px-4 py-3.5 sm:px-5 text-left font-semibold text-xs uppercase tracking-wider">Barangay</th>
                        <th class="px-4 py-3.5 sm:px-5 text-center font-semibold text-xs uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3.5 sm:px-5 text-left font-semibold text-xs uppercase tracking-wider">Submitted By</th>
                        <th class="px-4 py-3.5 sm:px-5 text-left font-semibold text-xs uppercase tracking-wider">Archived At</th>
                        <th class="px-4 py-3.5 sm:px-5 text-center font-semibold text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($monitorings as $monitoring)
                        @php($restoreBlocked = !empty(($activeMonitoringPeriods ?? [])[$monitoring->barangay_id . '-' . $monitoring->quarter . '-' . $monitoring->year]))
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4">
                                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                                    <div class="w-9 h-9 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-semibold flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-sm"></i>
                                    </div>
                                    <span class="text-gray-800 font-semibold truncate">{{ $monitoring->barangay->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 px-2.5 sm:px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-calendar-alt text-xs"></i>
                                    Q{{ $monitoring->quarter }} {{ $monitoring->year }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4 min-w-0">
                                <div class="text-gray-800 font-medium truncate">
                                    {{ $monitoring->creator?->first_name }} {{ $monitoring->creator?->last_name }}
                                </div>
                                <div class="text-xs text-gray-400 truncate">
                                    {{ $monitoring->creator?->email }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4 text-gray-600 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-700">{{ $monitoring->archived_at?->format('M d, Y') ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $monitoring->archived_at?->format('g:i A') ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3.5 sm:px-5 sm:py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2 [&>a]:inline-flex [&>a]:items-center [&>a]:justify-center [&>button]:inline-flex [&>button]:items-center [&>button]:justify-center">
                                    <a href="{{ route('admin.bcpc-case-monitoring.edit', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'view' => $monitoring->id, 'archived' => 1]) }}" data-admin-archive-view="monitoring" data-modal-title="BCPC Case Monitoring Details"
                                       class="inline-flex items-center justify-center w-9 h-9 bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg text-xs font-medium transition shadow-sm"
                                       title="View Details" aria-label="View Details">
                                        <i class="fas fa-eye"></i>
                                        <span class="sr-only">View Details</span>
                                    </a>
                                    <button type="button" {{ $restoreBlocked ? 'disabled' : '' }}
                                            @unless($restoreBlocked) onclick="restoreMonitoring({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')" @endunless
                                            class="inline-flex items-center justify-center w-9 h-9 {{ $restoreBlocked ? '!bg-red-100 hover:!bg-red-100 !text-red-800 !border !border-red-200 cursor-not-allowed pointer-events-none' : 'bg-[#C41E3A] hover:bg-[#A01830] text-white' }} rounded-lg text-xs font-medium transition shadow-sm"
                                            title="{{ $restoreBlocked ? 'An active BCPC monitoring case already exists for this barangay, quarter, and year' : 'Restore Record' }}">
                                        <i class="fas fa-undo"></i>
                                        <span class="sr-only">Restore</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-archive text-3xl"></i>
                                </div>
                                <p class="text-gray-700 font-semibold text-base">No archived BCPC case monitoring records found.</p>
                                <p class="text-gray-400 text-xs mt-1">Archived case monitoring reports will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View -->
        <div class="archived-bcpc-monitoring-mobile-cards md:hidden p-3 space-y-3">
            @forelse($monitorings as $monitoring)
                @php($restoreBlocked = !empty(($activeMonitoringPeriods ?? [])[$monitoring->barangay_id . '-' . $monitoring->quarter . '-' . $monitoring->year]))
                <div class="archived-bcpc-monitoring-card border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center font-bold flex-shrink-0 text-sm">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="truncate">
                                <h3 class="font-bold text-gray-800 text-sm truncate">{{ $monitoring->barangay->name ?? 'N/A' }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $monitoring->creator?->first_name }} {{ $monitoring->creator?->last_name }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0">
                            <i class="fas fa-calendar-alt text-[10px]"></i>
                            Q{{ $monitoring->quarter }} {{ $monitoring->year }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3 bg-gray-50 rounded-lg px-3 py-2">
                        <span>Archived At:</span>
                        <span class="font-medium text-gray-700">{{ $monitoring->archived_at?->format('M d, Y g:i A') ?? 'N/A' }}</span>
                    </div>

                    <div class="archived-bcpc-monitoring-card-actions grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 [&>a]:inline-flex [&>a]:items-center [&>a]:justify-center [&>button]:inline-flex [&>button]:items-center [&>button]:justify-center">
                        <a href="{{ route('admin.bcpc-case-monitoring.edit', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'view' => $monitoring->id, 'archived' => 1]) }}" data-admin-archive-view="monitoring" data-modal-title="BCPC Case Monitoring Details"
                           class="inline-flex items-center justify-center gap-1.5 w-full h-10 bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg text-xs font-medium transition shadow-sm"
                           title="View Details" aria-label="View Details">
                            <i class="fas fa-eye text-xs"></i>
                            <span>View</span>
                        </a>
                        <button type="button" {{ $restoreBlocked ? 'disabled' : '' }}
                                @unless($restoreBlocked) onclick="restoreMonitoring({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')" @endunless
                                class="inline-flex items-center justify-center gap-1.5 w-full h-10 {{ $restoreBlocked ? '!bg-red-100 hover:!bg-red-100 !text-red-800 !border !border-red-200 cursor-not-allowed pointer-events-none' : 'bg-[#C41E3A] hover:bg-[#A01830] text-white' }} rounded-lg text-xs font-medium transition shadow-sm"
                                title="{{ $restoreBlocked ? 'An active BCPC monitoring case already exists for this barangay, quarter, and year' : 'Restore Record' }}">
                            <i class="fas fa-undo text-xs"></i>
                            <span @if(auth()->user()?->isBcpcAdmin()) class="archived-monitoring-restore-label" @endif>Restore</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="archive-empty-state text-center py-10 text-gray-500">
                    <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-archive text-2xl"></i>
                    </div>
                    <p class="text-gray-700 font-semibold text-sm">No archived BCPC case monitoring records found.</p>
                </div>
            @endforelse
        </div>

        @include('components.archived-pagination', ['paginator' => $monitorings, 'label' => 'reports'])
        @if(auth()->user()?->isDepartmentAdmin())
        <div class="archived-monitoring-tablet-pagination hidden">
            @include('components.archived-pagination', ['paginator' => $monitorings, 'label' => 'reports', 'pageLinkLimit' => 4])
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if(auth()->user()?->isDepartmentAdmin())
<script>
document.addEventListener('DOMContentLoaded', () => {
    let activeRequest;
    document.addEventListener('click', async event => {
        const link = event.target.closest('#archived-bcpc-results [data-pagination-label] a[href]');
        if (!matchMedia('(max-width: 85.375rem)').matches || !link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.classList.contains('pointer-events-none')) return;
        event.preventDefault();
        activeRequest?.abort();
        const controller = new AbortController();
        activeRequest = controller;
        const results = document.getElementById('archived-bcpc-results');
        results.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(link.href, { signal: controller.signal, headers: { Accept: 'text/html' } });
            if (!response.ok || response.redirected) throw new Error('Unable to load archived monitoring records');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (controller.signal.aborted) return;
            const next = page.getElementById('archived-bcpc-results');
            if (!next) throw new Error('Missing monitoring records');
            const top = results.getBoundingClientRect().top;
            results.replaceWith(next);
            history.replaceState(null, '', link.href);
            window.scrollBy({ top: next.getBoundingClientRect().top - top, behavior: 'instant' });
        } catch (error) {
            if (error.name !== 'AbortError') location.assign(link.href);
        } finally {
            results.removeAttribute('aria-busy');
        }
    });
});
</script>
@endif
@include('admin-tailwind.components.archive-view-modal')
<script>
const archivedBcpcFilterForm = document.getElementById('archived-bcpc-report-filter-form');
let archivedBcpcSearchTimer;
document.getElementById('archived-bcpc-report-search')?.addEventListener('input', () => {
    clearTimeout(archivedBcpcSearchTimer);
    archivedBcpcSearchTimer = setTimeout(() => archivedBcpcFilterForm?.requestSubmit(), 250);
});
['archived-bcpc-report-quarter', 'archived-bcpc-report-year', 'archived-bcpc-report-barangay'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', () => archivedBcpcFilterForm?.requestSubmit());
});

function restoreMonitoring(id, barangayName, period) {
    Swal.fire({
        title: 'Restore Case Monitoring?',
        customClass: { popup: 'bcpc-monitoring-restore-confirm' },
        html: `
            <div class="text-center py-2">
                <i class="fas fa-undo text-5xl text-emerald-500 mb-4"></i>
                <p class="text-gray-700 font-medium">Are you sure you want to restore the BCPC case monitoring for <strong>${barangayName}</strong> (${period})?</p>
                <p class="text-gray-400 text-xs mt-2">This record will return to active monitoring records.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Restore',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Restoring...',
                text: 'Please wait while we restore this record.',
                allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/admin/bcpc-case-monitoring/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to restore record.');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Restored!',
                    customClass: { popup: 'bcpc-monitoring-restore-success' },
                    text: data.message || 'Record has been restored.',
                    confirmButtonText: 'OK'
                }).then(() => window.location.href = @json(route('admin.bcpc-case-monitoring.archived')));
            })
            .catch(error => Swal.fire('Error', error.message || 'An error occurred while restoring the record.', 'error'));
        }
    });
}

function deleteMonitoring(id) {
    Swal.fire({ title: 'Permanently delete record?', text: 'This archived monitoring report cannot be restored.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#b91c1c', confirmButtonText: 'Delete permanently' }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title: 'Deleting...', text: 'Please wait while we delete this archived record.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        fetch(`/admin/bcpc-case-monitoring/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async response => { const data = await response.json().catch(() => ({})); if (!response.ok || !data.success) throw new Error(data.message || 'Failed to permanently delete record.'); return data; })
            .then(data => Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message || 'Archived monitoring report deleted successfully.', confirmButtonText: 'OK' }))
            .then(() => window.location.reload())
            .catch(error => Swal.fire('Error', error.message || 'Failed to permanently delete record.', 'error'));
    });
}
</script>
@endpush
