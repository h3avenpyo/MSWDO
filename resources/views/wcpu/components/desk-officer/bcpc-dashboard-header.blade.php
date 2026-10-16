@if(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="{{ $titleClass ?? '' }}">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $title }}</h2>
            @if(!empty($subtitle))
                <p class="text-sm text-gray-500 hidden sm:block">{{ $subtitle }}</p>
            @endif
        </div>
        @unless(auth()->user()?->isMultiCaseDepartment() && request()->boolean('archive_report_preview') && request()->boolean('archived') && request()->routeIs('desk-officer.quarterly-reports.show', 'desk-officer.bcpc-case-monitoring.edit'))
        @include('components.desk-officer.header-account')
        @endunless
    </div>
</header>
@endif
