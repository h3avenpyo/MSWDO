@if(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
<header id="desk-officer-dashboard-header" class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="flex min-w-0 items-center gap-3">
            <button
                type="button"
                id="pageMobileMenuToggle"
                class="inline-flex h-9 w-9 flex-none items-center justify-center rounded-lg bg-[#C41E3A] text-white shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C41E3A] focus-visible:ring-offset-2 sm:hidden"
                aria-label="Open navigation menu"
                aria-controls="sidebar"
                aria-expanded="false"
            >
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            <div class="min-w-0 {{ $titleClass ?? '' }}">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $title }}</h2>
                @if(!empty($subtitle))
                    <p class="text-sm text-gray-500 hidden sm:block">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @unless(auth()->user()?->isMultiCaseDepartment() && request()->boolean('archive_report_preview') && request()->boolean('archived') && request()->routeIs('desk-officer.quarterly-reports.show', 'desk-officer.bcpc-case-monitoring.edit'))
        @include('components.desk-officer.header-account')
        @endunless
    </div>
</header>
@endif
