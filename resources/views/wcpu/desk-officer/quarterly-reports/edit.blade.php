@extends(auth()->user()?->position === 'Desk Officer' ? 'layouts.desk-officer' : 'layouts.admin-tailwind')

@section('title', 'Edit Quarterly Report')

@section('content')
@php
    $isAdminEditor = auth()->user()?->position === 'Administrator';
    $quarterlyIndexRoute = $isAdminEditor ? 'admin.quarterly-reports.index' : 'desk-officer.quarterly-reports.index';
    $quarterlyShowRoute = $isAdminEditor ? 'admin.quarterly-reports.show' : 'desk-officer.quarterly-reports.show';
    $quarterlyRegenerateRoute = $isAdminEditor ? 'admin.quarterly-reports.regenerate' : 'desk-officer.quarterly-reports.regenerate';
@endphp
@if($isAdminEditor)
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
<header class="admin-reference-mobile-header sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header" data-quarterly-report-edit-header>
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="admin-reference-mobile-leading min-w-0">
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="admin-reference-mobile-title">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Edit Quarterly Report</h2>
                <p class="admin-reference-mobile-subtitle text-sm text-gray-500">{{ $barangay->name ?? 'Barangay' }} &bull; Period: Q{{ $report->quarter }} {{ $report->year }}</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
@if(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->isMultiCaseDepartment() || in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
<style>
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        .quarterly-report-edit-type-label {
            justify-content: center;
            text-align: center;
        }
        #quarterly-report-edit-actions,
        #quarterly-report-edit-actions > div {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }
        #quarterly-report-edit-actions {
            gap: .75rem;
        }
        #quarterly-report-edit-actions > div {
            gap: .625rem;
        }
        #quarterly-report-edit-actions form {
            width: 100%;
        }
        #quarterly-report-edit-actions :is(a, button) {
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: center;
            text-align: center;
        }
        #quarterly-report-edit-actions :is(i, span) {
            flex-shrink: 0;
        }
    }
</style>
@endif
@unless($isAdminEditor)
@include('components.desk-officer.bcpc-dashboard-header', [
    'title' => 'Edit Quarterly Report',
    'subtitle' => ($barangay->name ?? 'Barangay') . ' • Period: Q' . $report->quarter . ' ' . $report->year,
])
@endunless
<div class="max-w-4xl mx-auto py-6 px-4">
    <!-- Breadcrumb / Back -->
    <div class="mb-6">
        <div class="mb-3 flex items-center gap-2">
            <a href="{{ route($quarterlyIndexRoute) }}"
               class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-800 font-medium transition">
                <i class="fas fa-arrow-left"></i> Back to Quarterly Reports
            </a>
            <span class="quarterly-report-edit-type-label inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ ($report->type ?? 'VAWC') === 'BCPC' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700' }}">
                {{ str_replace('VAWC', 'SWD', $report->type ?? 'VAWC') }} REPORT
            </span>
        </div>
        @unless($isAdminEditor)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-3">
                    @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
                    <span class="p-2.5 bg-purple-100 text-purple-700 rounded-xl">
                        <i class="fas fa-edit"></i>
                    </span>
                    @endunless
                    Edit Quarterly Report
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $barangay->name ?? 'Barangay' }} &bull; Period: <strong>Q{{ $report->quarter }} {{ $report->year }}</strong>
                </p>
            </div>
            @endunless
        </div>
        @endunless
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 shadow-sm">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-info-circle text-lg"></i>
                <h2 class="font-semibold text-lg">Report Summary & Sync Status</h2>
            </div>
            <span class="text-xs font-semibold bg-white/20 px-3 py-1 rounded-full">
                Q{{ $report->quarter }} {{ $report->year }}
            </span>
        </div>

        <div class="p-6 space-y-6">
            <!-- Grid info -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-purple-50/70 border border-purple-100 rounded-xl p-4">
                    <span class="text-xs font-medium text-purple-600 uppercase tracking-wide">Total Recorded Cases</span>
                    <div class="text-3xl font-extrabold text-purple-900 mt-1">
                        {{ $report->report_data['total_cases'] ?? 0 }}
                    </div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Generated On</span>
                    <div class="text-base font-bold text-gray-800 mt-1">
                        {{ $report->created_at->format('M d, Y') }}
                    </div>
                    <div class="text-xs text-gray-400">{{ $report->created_at->format('h:i A') }}</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Generated By</span>
                    <div class="text-base font-bold text-gray-800 mt-1">
                        {{ $report->user->first_name ?? 'User' }} {{ $report->user->last_name ?? '' }}
                    </div>
                    <div class="text-xs text-gray-400">{{ $report->user->email ?? '' }}</div>
                </div>
            </div>

            <!-- Notice / Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800 flex items-start gap-3">
                <i class="fas fa-lightbulb text-blue-600 mt-0.5 text-base"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-1">Updating this Quarterly Report</h4>
                    <p class="text-blue-700 leading-relaxed">
                        If cases have been added, modified, or updated for Barangay <strong>{{ $barangay->name }}</strong> during 
                        <strong>Q{{ $report->quarter }} {{ $report->year }}</strong>, click the <strong>Regenerate Report</strong> button below. 
                        The system will automatically recalculate the cases, refresh the abuse and status breakdowns, and regenerate the official PDF document.
                    </p>
                </div>
            </div>

            <!-- Action buttons -->
            <div id="quarterly-report-edit-actions" class="pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route($quarterlyShowRoute, $report->id) }}" @if($isAdminEditor) target="_blank" @endif class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium shadow-sm transition">
                        <i class="fas fa-file-pdf"></i>
                        <span>View Current PDF</span>
                    </a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route($quarterlyIndexRoute) }}"
                       class="px-4 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-medium text-sm transition">
                        Cancel
                    </a>
                    <form id="regenerate-quarterly-report-form" action="{{ route($quarterlyRegenerateRoute, $report->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-700 to-purple-800 hover:from-purple-800 hover:to-purple-900 text-white rounded-xl text-sm font-medium shadow-md transition">
                            <i class="fas fa-sync-alt"></i>
                            <span>Regenerate & Update Report</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@if(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->defaultCaseDepartment() === 'BCPC' || auth()->user()?->isMultiCaseDepartment()))
@push('scripts')
@include('desk-officer.quarterly-reports._bcpc-result', ['formId' => 'regenerate-quarterly-report-form', 'operation' => 'update'])
@endpush
@endif
