@extends('layouts.desk-officer')
@section('title', 'Quarterly Reports')
@php
    $isSingleDepartmentDeskOfficer = auth()->user()?->position === 'Desk Officer'
        && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)
        && !auth()->user()?->isMultiCaseDepartment();
@endphp
@push('styles')
<style>
    @media (max-width: 767px) {
        .quarterly-report-actions-container,
        .quarterly-report-actions { width: 100%; }
        .quarterly-generate-report-button {
            display: inline-flex;
            width: 100%;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            padding-right: .75rem;
            padding-left: .75rem;
        }
    }
    @media (max-width: 47.9375rem) {
        /* Match the dashboard notification button without shared theme overrides. */
        @if($isSingleDepartmentDeskOfficer)
        html body.portal-ui #mainContent>header#desk-officer-dashboard-header > div > div:last-child > .relative {
            display: flex !important;
            align-items: center !important;
            align-self: center !important;
            line-height: 1 !important;
        }
        body.portal-ui #mainContent>header#desk-officer-dashboard-header button[aria-label="Notifications"],
        @endif
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

        @if($isSingleDepartmentDeskOfficer)
        body.portal-ui #mainContent>header#desk-officer-dashboard-header button[aria-label="Notifications"]:hover,
        @endif
        body.portal-ui #mainContent>header#desk-officer-dashboard-header [data-multirole-notifications] button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }

        @if($isSingleDepartmentDeskOfficer)
        body.portal-ui #mainContent>header#desk-officer-dashboard-header button[aria-label="Notifications"]>i { font-size: 1.05rem !important; }
        body.portal-ui #mainContent>header#desk-officer-dashboard-header button[aria-label="Notifications"]>i,
        @endif
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

        @if($isSingleDepartmentDeskOfficer)
        body.portal-ui #mainContent>header#desk-officer-dashboard-header #notifications-unread-count,
        @endif
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

        body.desk-officer-quarterly-reports-mobile-nav #mobileTopBar,
        body.desk-officer-quarterly-reports-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent #mobileMenuToggle,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav .tablet-header-row > #mobileMenuToggle,
        #mobileMenuToggle {
            display: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header.portal-page-header #pageMobileMenuToggle {
            display: inline-flex !important;
            flex: 0 0 2.5rem !important;
            width: 2.5rem !important;
            height: 2.5rem !important;
            align-items: center !important;
            justify-content: center !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent > header.portal-page-header #pageMobileMenuToggle > i {
            display: inline-flex !important;
            font-size: 1.25rem !important;
            line-height: 1 !important;
            color: #ffffff !important;
            margin: 0 !important;
        }
    }
    @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment())
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        .quarterly-print-all-button {
            display: inline-flex;
            width: 100%;
            height: 2.5rem;
            min-height: 2.5rem;
            align-items: center;
            justify-content: center;
            padding-right: .75rem;
            padding-left: .75rem;
        }
    }
    @endif

    @media (min-width: 37.5rem) and (max-width: 80rem) {
        .quarterly-report-actions-container {
            margin-left: auto !important;
            margin-right: 0 !important;
            justify-content: flex-end !important;
        }
        .quarterly-report-actions { justify-content: flex-end !important; }
        #desk-quarterly-reports-filter-form {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: clamp(.75rem, 1.5vw, 1rem);
        }
        #desk-quarterly-reports-filter-form > div {
            width: auto !important;
            min-width: 0 !important;
        }
        #desk-quarterly-reports-filter-form > div:first-child,
        #desk-quarterly-reports-filter-form > div:last-child { grid-column: auto !important; }
        #desk-quarterly-reports-filter-form > div > label { display: none !important; }
        #desk-quarterly-reports-filter-form > div:last-child {
            flex-direction: row;
            align-items: stretch;
        }
        #desk-quarterly-reports-filter-form :is(button, a) { justify-self: stretch; }
    }
</style>
@endpush

@push('styles')
<style>
@media (max-width: 85.375rem) {
    html body.portal-ui .swal2-popup.quarterly-print-all-popup .swal2-actions {
        display: flex !important; flex-wrap: nowrap !important; gap: .625rem !important;
        width: calc(100% - 3rem) !important; margin: 1rem auto 0 !important; padding: 0 !important;
    }
    html body.portal-ui .swal2-popup.quarterly-print-all-popup .swal2-actions > button {
        flex: 1 1 0 !important; min-width: 0 !important; margin: 0 !important;
        padding: .625rem .75rem !important;
    }
}
</style>
@endpush

@section('content')

@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Quarterly Reports', 'subtitle' => 'View and manage quarterly reports'])
<div class="w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
        <h1 class="text-2xl font-bold text-gray-800">Quarterly Reports</h1>
        @endunless
        <div class="quarterly-report-actions-container ml-auto flex flex-wrap items-center justify-end gap-4">
            <div class="quarterly-report-actions flex flex-wrap items-center justify-end gap-2">
            @unless(auth()->user()->isMultiCaseDepartment() || (auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
            <a href="{{ route('desk-officer.quarterly-reports.archived') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm {{ auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) ? 'border border-[#C41E3A] bg-[#C41E3A] text-white hover:bg-[#A01830]' : 'border border-purple-200 text-purple-700 hover:bg-purple-50' }}">
                @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
                <i class="fas fa-archive text-amber-500"></i>
                @endunless
                <span>Archived Reports</span>
            </a>
            @endunless
            @if(auth()->user()->isMultiCaseDepartment())
            <button type="button" onclick="openReportTypeModal()" class="quarterly-generate-report-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">Generate Quarterly Report</button>
            <button type="button" onclick="printAllQuarterlyReports()" class="quarterly-print-all-button inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm"><i class="fas fa-print" aria-hidden="true"></i><span>Print All</span></button>
            @else
            <a href="{{ route('desk-officer.quarterly-reports.create') }}" class="quarterly-generate-report-button px-5 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">Generate Quarterly Report</a>
            @endif
            </div>
        </div>
    </div>
    @if(session('success'))<p class="p-4 mb-4 bg-green-50 text-green-700 rounded-lg">{{ session('success') }}</p>@endif
    @if(session('error'))<p class="p-4 mb-4 bg-red-50 text-red-700 rounded-lg">{{ session('error') }}</p>@endif

    <div class="bg-white border border-gray-100 rounded-xl shadow-md p-4 mb-6">
        <form id="desk-quarterly-reports-filter-form" action="{{ route('desk-officer.quarterly-reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:items-start gap-3">
            @if(request()->filled('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <div class="min-w-0">
                <label for="desk-quarterly-barangay-search" class="block text-sm font-medium text-gray-600 mb-1 lg:hidden">Barangay</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" aria-hidden="true"></i>
                    <input type="search" id="desk-quarterly-barangay-search" name="barangay_name" aria-label="Search Barangay" maxlength="255" placeholder="Search Barangay" autocomplete="off"
                           value="{{ old('barangay_name', request('barangay_name')) }}"
                           class="w-full h-10 pl-9 pr-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                </div>
                @error('barangay_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="min-w-0">
                <label class="block text-sm font-medium text-gray-600 mb-1 lg:hidden">Quarter</label>
                <select name="quarter" aria-label="Quarter" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="">All Quarters</option>
                    <option value="1" {{ request('quarter') == 1 ? 'selected' : '' }}>Q1</option>
                    <option value="2" {{ request('quarter') == 2 ? 'selected' : '' }}>Q2</option>
                    <option value="3" {{ request('quarter') == 3 ? 'selected' : '' }}>Q3</option>
                    <option value="4" {{ request('quarter') == 4 ? 'selected' : '' }}>Q4</option>
                </select>
            </div>
            <div class="min-w-0">
                <label class="block text-sm font-medium text-gray-600 mb-1 lg:hidden">Year</label>
                <select name="year" aria-label="Year" class="w-full h-10 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 text-sm">
                    <option value="">All Years</option>
                    @for($year = now()->year; $year >= now()->year - 3; $year--)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-1">
                <button type="submit" class="w-full sm:flex-1 lg:flex-1 h-10 px-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition">
                    <i class="fas fa-filter mr-1" aria-hidden="true"></i> Filter
                </button>
                @if(request()->hasAny(['barangay_name', 'quarter', 'year']))
                    <a href="{{ route('desk-officer.quarterly-reports.index') }}" class="w-full sm:flex-1 lg:flex-1 h-10 inline-flex items-center justify-center px-3 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg text-sm transition">Clear</a>
                @endif
            </div>
        </form>
    </div>

    @include('desk-officer.quarterly-reports._tables')
</div>
@endsection
@push('scripts')
@if(auth()->user()->isMultiCaseDepartment())
<script>
async function printAllQuarterlyReports() {
    const result = await Swal.fire({ title: 'Print All Reports', customClass: { popup: 'quarterly-print-all-popup' }, input: 'select', inputOptions: { BCPC: 'BCPC Case Reports', VAWC: 'SWD Case Reports' }, inputPlaceholder: 'Select department', showCancelButton: true, confirmButtonText: 'Print', inputValidator: value => !value ? 'Select a department.' : undefined });
    if (!result.isConfirmed) return;
    const counts = @json(collect($reportGroups)->map(fn ($group) => $group->total()));
    if (!counts[result.value]) {
        await Swal.fire({ icon: 'info', title: 'No Reports to Print', text: 'There are no quarterly reports to print.' });
        return;
    }
    window.open(@json(route('desk-officer.quarterly-reports.print-all')) + '?type=' + encodeURIComponent(result.value), '_blank', 'noopener');
}
</script>
@endif
@if(request('select_type'))
<script>document.addEventListener('DOMContentLoaded', () => openReportTypeModal());</script>
@endif
@endpush
