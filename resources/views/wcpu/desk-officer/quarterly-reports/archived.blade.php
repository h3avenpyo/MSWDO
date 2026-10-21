@extends('layouts.desk-officer')
@php($archiveTitle = in_array(auth()->user()?->defaultCaseDepartment(), ['VAWC', 'BCPC'], true) ? 'Archived Reports' :
'Archived Quarterly Reports')
@section('title', $archiveTitle)
@push('styles')
<style>
    .archived-quarterly-reports-stable,
    .archived-quarterly-reports-stable :is(table, button, a) {
        animation: none !important;
        transition: none !important;
    }

    @if(auth()->user()?->position==='Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)) #archived-report-quarter {
        width: 130px !important;
        min-width: 130px !important;
        max-width: 130px !important;
        flex: 0 0 130px !important;
    }

    #archived-report-year {
        width: 115px !important;
        min-width: 115px !important;
        max-width: 115px !important;
        flex: 0 0 115px !important;
    }

    @endif

    @media (max-width: 47.9375rem) {
        /* Mobile header: horizontal flex layout with hamburger on left, title/subtitle beside it, notification on right */
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileTopBar {
            display: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav .tablet-header-row > #mobileMenuToggle,
        body.desk-officer-quarterly-reports-mobile-nav #mobileMenuToggle,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebarCloseBtn,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #sidebarOverlay {
            display: none !important;
            pointer-events: none !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header {
            background: #C41E3A !important;
            border-bottom: 1px solid #99172E !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header>div {
            align-items: flex-start !important;
            flex-wrap: nowrap !important;
            min-width: 0 !important;
            padding-left: max(.5rem, env(safe-area-inset-left)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:first-of-type,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header>div>div:first-of-type {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: flex-start !important;
            gap: .5rem !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:first-of-type>div,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header>div>div:first-of-type>div {
            min-width: 0 !important;
            flex: 1 1 auto !important;
            text-align: left !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) {
            position: relative !important;
            left: 0 !important;
            margin-left: 0 !important;
            transform: none !important;
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
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav :is(#dashboardMenuToggle, #pageMobileMenuToggle) i {
            color: inherit !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) :is(h1, h2, h3),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header :is(h1, h2, h3) {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header p {
            display: block !important;
            margin-top: .125rem !important;
            color: #ffffff !important;
            opacity: 1 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) p :is(span, strong),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header p :is(span, strong) {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky) div:first-of-type :is(span, a),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header div:first-of-type :is(span, a) {
            color: #ffffff !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header:is(#desk-officer-dashboard-header, .portal-page-header, .sticky)>div>div:last-child,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header>div>div:last-child {
            flex: 0 0 auto !important;
            flex-wrap: nowrap !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header button[aria-label="Notifications"],
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"] {
            box-sizing: border-box !important;
            width: 2.5rem !important;
            min-width: 2.5rem !important;
            height: 2.5rem !important;
            min-height: 2.5rem !important;
            padding: 0 !important;
            border-radius: .625rem !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header button[aria-label="Notifications"]>i,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header button[aria-label="Notifications"]>i {
            margin: 0 !important;
            font-size: 1rem !important;
            line-height: 1 !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #mainContent>header #notifications-unread-count,
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header #notifications-unread-count {
            width: 1.25rem !important;
            height: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            font-size: .75rem !important;
            line-height: 1 !important;
        }

        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header div:has(> img[alt="User Profile"]),
        html body.portal-ui.desk-officer-quarterly-reports-mobile-nav #desk-officer-dashboard-header button:has(> img[alt="User Profile"]) {
            display: none !important;
        }

        .archived-quarterly-reports-stable {
            padding: 1rem;
            padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom)));
        }

        .archived-quarterly-filter-controls {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) !important;
            gap: .75rem !important;
            width: 100%;
        }

        .archived-quarterly-filter-controls> :is(div, select, button, a) {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            flex: none !important;
            height: 3rem !important;
            min-height: 3rem !important;
        }

        .archived-quarterly-filter-controls>div {
            height: auto !important;
        }

        .archived-quarterly-filter-controls input {
            height: 3rem !important;
        }

        .archived-quarterly-filter-controls :is(select, button, a) {
            justify-content: center;
        }
    }

    @if(auth()->user()?->position==='Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)) @media (max-width: 47.9375rem) and (orientation: portrait) {

        #archived-report-quarter,
        #archived-report-year {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            flex: none !important;
        }

        .archived-quarterly-filter-controls {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: .625rem !important;
        }

        .archived-quarterly-filter-controls>div,
        .archived-quarterly-filter-controls> :is(button, a) {
            grid-column: 1 / -1;
        }
    }

    @endif
</style>
@endpush
@section('content')
@include('components.desk-officer.bcpc-dashboard-header', ['title' => $archiveTitle, 'subtitle' =>
auth()->user()->isMultiCaseDepartment() ? 'Review or restore archived quarterly reports for your assigned barangay.' :
'View archived quarterly reports'])
<div class="archived-quarterly-reports-stable w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(),
        ['BCPC', 'VAWC'], true))
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $archiveTitle }}</h1>
            <p class="text-gray-500 text-sm mt-0.5">Review, download, or restore archived quarterly reports for your
                assigned barangay.</p>
        </div>
        @endunless
        @unless(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->isMultiCaseDepartment() ||
        in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('desk-officer.quarterly-reports.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-file-alt"></i>
                <span>Active Reports</span>
            </a>
        </div>
        @endunless
    </div>
    @if(session('success'))<p class="p-4 mb-4 bg-green-50 text-green-700 rounded-lg border border-green-200">{{
        session('success') }}</p>@endif
    <!-- Search and Filter Bar -->
    <form method="GET" action="{{ route('desk-officer.quarterly-reports.archived') }}"
        class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="archived-quarterly-filter-controls flex items-center gap-2 sm:gap-3">
            <div class="relative flex-1 min-w-0" style="flex: 1 1 auto !important; min-width: 0 !important;">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" id="archived-report-search" value="{{ request('search') }}"
                    placeholder="Search archived reports..."
                    class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <select name="quarter" id="archived-report-quarter"
                class="h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Quarters</option>
                @foreach([1, 2, 3, 4] as $qOpt)
                <option value="{{ $qOpt }}" {{ request('quarter')==$qOpt ? 'selected' : '' }}>Q{{ $qOpt }}</option>
                @endforeach
            </select>
            <select name="year" id="archived-report-year"
                class="h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Years</option>
                @foreach(range(2026, 2030) as $yOpt)
                <option value="{{ $yOpt }}" {{ request('year')==$yOpt ? 'selected' : '' }}>{{ $yOpt }}</option>
                @endforeach
            </select>
            <button type="submit" style="flex: 0 0 auto !important;"
                class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0">
                <i class="fas fa-filter"></i>
                <span>Filter</span>
            </button>
            @if(request()->filled('search') || request()->filled('quarter') || request()->filled('year'))
            <a href="{{ route('desk-officer.quarterly-reports.archived') }}" style="flex: 0 0 auto !important;"
                class="inline-flex h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition shrink-0">
                <i class="fas fa-times"></i>
                <span>Clear</span>
            </a>
            @endif
        </div>
    </form>

    @include('desk-officer.quarterly-reports._tables', ['archived' => true])
    @if(isset($archivedMonitorings))
    @include('desk-officer.quarterly-reports._archived-monitorings')
    @endif
</div>

@push('scripts')
<script>
    // Live client-side search & filter on archived quarterly reports tables
const repSearch = document.getElementById('archived-report-search');
const repQuarter = document.getElementById('archived-report-quarter');
const repYear = document.getElementById('archived-report-year');
function filterReportRows() {
    const q = (repSearch?.value || '').toLowerCase().trim();
    const quarterVal = (repQuarter?.value || '').toLowerCase().trim();
    const yearVal = (repYear?.value || '').toLowerCase().trim();
    document.querySelectorAll('.desk-officer-quarterly-report-table table tbody tr').forEach(row => {
        if (row.cells.length < 5) return;
        const text = row.textContent.toLowerCase();
        const periodCell = (row.cells[0]?.textContent || '').toLowerCase().trim();
        const matchesSearch = !q || text.includes(q);
        const matchesQuarter = !quarterVal || periodCell.includes('q' + quarterVal);
        const matchesYear = !yearVal || periodCell.includes(yearVal);
        row.style.display = (matchesSearch && matchesQuarter && matchesYear) ? '' : 'none';
    });
}
if (repSearch) repSearch.addEventListener('input', filterReportRows);
if (repQuarter) repQuarter.addEventListener('change', filterReportRows);
if (repYear) repYear.addEventListener('change', filterReportRows);
</script>
@endpush
@endsection