@if(auth()->user() && (strtolower(trim((string) auth()->user()->position)) === 'desk officer' ||
in_array(strtoupper((string) auth()->user()->defaultCaseDepartment()), ['BCPC', 'VAWC'], true)))
<header id="desk-officer-dashboard-header"
    class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header {{ $headerClass ?? '' }}">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4"
        style="display: flex !important; flex-direction: row !important; align-items: center !important; flex-wrap: nowrap !important;">
        <div class="flex flex-row min-w-0 items-center gap-3"
            style="display: flex !important; flex-direction: row !important; align-items: center !important;">
            <button type="button" id="pageMobileMenuToggle"
                class="inline-flex flex-none items-center justify-center text-white focus:outline-none"
                aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="min-w-0 {{ $titleClass ?? '' }}"
                style="display: flex !important; flex: 1 1 auto !important; flex-direction: column !important; justify-content: center !important; min-width: 0 !important; text-align: left !important;">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $title }}</h2>
                @if(!empty($subtitle))
                <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @unless(auth()->user()?->isMultiCaseDepartment() && request()->boolean('archive_report_preview') &&
        request()->boolean('archived') && request()->routeIs('desk-officer.quarterly-reports.show',
        'desk-officer.bcpc-case-monitoring.edit'))
        @include('components.desk-officer.header-account')
        @endunless
    </div>
</header>
@endif
