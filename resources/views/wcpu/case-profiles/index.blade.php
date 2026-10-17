@extends($isAdmin ? 'layouts.admin-tailwind' : 'layouts.desk-officer')

@section('title', 'Victim and Perpetrator Profiles')

@php
    $isSwdDeskOfficer = auth()->user()?->position === 'Desk Officer' && (auth()->user()?->defaultCaseDepartment() === 'VAWC' || auth()->user()?->isMultiCaseDepartment());
@endphp

@push('styles')
<style>
    .guardian-dialog::backdrop {
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(4px) !important;
    }
    @if(auth()->user()->isVawcAdmin() || $isSwdDeskOfficer)
    html { scrollbar-gutter: stable; }
    body.swal2-shown { padding-right: 0 !important; }
    @endif
    @media (max-width: 47.9375rem) {
        .case-profiles-table-view { display: none !important; }
        .case-profiles-mobile-cards { display: grid !important; gap: .75rem; }
        .case-profiles-mobile-cards .case-profile-card { min-width: 0; padding: 1rem; }
        .case-profiles-mobile-cards .case-profile-card-action {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 3rem !important;
            min-width: 3rem !important;
            height: 3rem !important;
            min-height: 3rem !important;
            padding: 0 !important;
        }
        #case-profiles-content {
            min-width: 0;
            padding-top: max(1rem, env(safe-area-inset-top)) !important;
            padding-right: max(1rem, env(safe-area-inset-right)) !important;
            padding-bottom: max(1.5rem, calc(1rem + env(safe-area-inset-bottom))) !important;
            padding-left: max(1rem, env(safe-area-inset-left)) !important;
        }
        #case-profiles-filter-form { gap: .75rem; }
        .case-profiles-age-department-row { width: 100%; }
        .case-profiles-age-department-row:has(.case-profiles-department-switcher) {
            display: block;
        }
        .case-profiles-department-switcher {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
            min-width: 0;
        }
        .case-profiles-department-switcher > a,
        #case-profiles-filter-form :is(input, button, a) {
            min-height: 3rem;
        }
        .case-profiles-department-switcher > a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .5rem !important;
            text-align: center;
        }
        .case-profiles-age-department-row label { min-width: 0; }
        .case-profiles-age-department-row input { width: 100% !important; }
    }
    @if(auth()->user()->isDepartmentAdmin())
    @media (max-width: 47.9375rem) {
        .case-profiles-mobile-cards {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }
        .case-profiles-mobile-cards .case-profile-card {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            overflow: hidden;
        }
        .case-profiles-mobile-cards .case-profile-card > div:first-child,
        .case-profiles-mobile-cards .case-profile-card > div:last-child {
            min-width: 0;
        }
        .case-profiles-mobile-cards .case-profile-card :is(p, a, span) {
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .case-profiles-mobile-cards .case-profile-card > div:first-child > div:first-child,
        .case-profiles-mobile-cards .case-profile-card > div:last-child > span {
            min-width: 0;
        }
        .case-profiles-mobile-cards .case-profile-card > div:last-child {
            gap: .75rem;
        }
        .case-profiles-mobile-cards .case-profile-card-action {
            flex: 0 0 3rem;
        }
    }
    @endif
    @media (min-width: 37.5rem) and (max-width: 79.9375rem) and (orientation: portrait) {
        .case-profiles-table-view .case-profiles-table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed;
        }
        .case-profiles-table-view .case-profiles-table :is(th, td) {
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        .case-profiles-table-view .case-profiles-table :is(th, td):nth-child(3) { width: 28%; }
        .case-profiles-table-view .case-profiles-table :is(th, td):nth-child(6) { width: 17%; }
        .case-profiles-table-view .case-profiles-table td:nth-child(6) > div { min-width: 0; }
    }
    @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment())
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        #case-profiles-filter-form .case-profiles-clear-button {
            display: inline-flex;
            width: 100%;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
    }
    @endif

    /* Dashboard-style red navbar for mobile/tablet, matching /admin-dashboard.
       The shared layouts keep their mobile top bar visible up to 80rem, so hide
       it across that whole range (otherwise its recolored dark red shows). */
    @media (max-width: 80rem) {
        html body.portal-ui #mobileTopBar {
            display: none !important;
        }
        html body.portal-ui #mainContent > header.sticky {
            background: #C41E3A !important;
            border-bottom-color: #99172E !important;
        }
        html body.portal-ui #mainContent > header > div > div:first-of-type :is(h1, h2, h3) {
            color: #ffffff !important;
        }
        html body.portal-ui #mainContent > header > div > div:first-of-type p {
            color: rgba(255, 255, 255, .85) !important;
        }
        html body.portal-ui #mainContent > header :is(#mobileMenuToggle, #pageMobileMenuToggle) {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            color: #ffffff !important;
        }
        html body.portal-ui #mainContent > header :is(#mobileMenuToggle, #pageMobileMenuToggle) > i {
            color: inherit !important;
        }
    }

    /* Vertical dropdown burger menu, matching /admin-dashboard. */
    @media (max-width: 63.9375rem) {
        html body.portal-ui.case-profiles-mobile-nav #sidebar {
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
        html body.portal-ui.case-profiles-mobile-nav #sidebar.sidebar-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        html body.portal-ui.case-profiles-mobile-nav #sidebar.sidebar-collapsed,
        html body.portal-ui.case-profiles-mobile-nav #sidebar.sidebar-collapsed-init {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        html body.portal-ui.case-profiles-mobile-nav #sidebar .sidebar-header {
            display: none !important;
        }
        html body.portal-ui.case-profiles-mobile-nav #sidebar .sidebar-text {
            opacity: 1 !important;
            max-width: none !important;
        }
        html body.portal-ui.case-profiles-mobile-nav #sidebar .sidebar-menu {
            flex: 0 1 auto;
            min-height: 0;
            padding: .5rem !important;
            overflow-y: auto !important;
        }
        html body.portal-ui.case-profiles-mobile-nav #sidebar .sidebar-link {
            justify-content: flex-start !important;
            gap: .75rem !important;
        }
        html body.portal-ui.case-profiles-mobile-nav #sidebar #sidebarCloseBtn {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
@if($isAdmin && auth()->user()->isDepartmentAdmin())
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Victim and Perpetrator Profiles</h2>
            <p class="text-sm text-gray-500 hidden sm:block">View and manage case profiles</p>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Victim and Perpetrator Profiles', 'subtitle' => 'View and manage case profiles'])
<div id="case-profiles-content" class="p-4 sm:p-6 lg:p-8 space-y-8">
    @unless(auth()->user()->isDepartmentAdmin() || (auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Victim and Perpetrator Profiles</h1>
    </div>
    @endunless
    @if((auth()->user()->isDepartmentAdmin() || !$isAdmin))
        <form id="case-profiles-filter-form" method="GET" action="{{ route('case-profiles.index') }}" class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-4 bg-white border border-gray-200 rounded-xl p-4">
            @if(!$isAdmin)
                <input type="hidden" name="type" value="{{ $profileDepartment }}">
            @endif
            <div class="case-profiles-search-age-row flex w-full items-center gap-2 sm:flex-1">
                <div class="case-profiles-search-control min-w-0 flex-1">
                    <span class="relative block w-full">
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400" aria-hidden="true"></i>
                        <input type="search" id="profile-search-input" name="search" maxlength="255" value="{{ request('search') }}" placeholder="Search name or address" aria-label="Search profiles" class="block w-full border border-gray-300 rounded-lg py-2 pl-9 pr-3">
                    </span>
                </div>
                <span class="case-profiles-age-control relative block w-28 shrink-0">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400" aria-hidden="true"></i>
                    <input type="number" id="profile-age-input" name="age" min="0" max="120" value="{{ request('age') }}" placeholder="All ages" aria-label="Search profiles by age" class="block w-full border border-gray-300 rounded-lg py-2 pl-8 pr-3">
                </span>
            </div>
            <button class="px-4 py-2 bg-purple-700 text-white rounded-lg" type="submit">Filter</button>
            <a href="{{ route('case-profiles.index') }}" class="case-profiles-clear-button px-4 py-2 border border-gray-300 rounded-lg">Clear</a>
            @if(!$isAdmin && auth()->user()->isMultiCaseDepartment())
            <div class="case-profiles-department-switcher flex gap-2" aria-label="Profile department">
                @foreach(auth()->user()->caseDepartments() as $department)
                <a href="{{ route('case-profiles.index', ['type' => $department, 'age' => request('age'), 'search' => request('search')]) }}" class="px-4 py-2 rounded-lg {{ $profileDepartment === $department ? 'bg-purple-700 text-white' : 'bg-purple-50 text-purple-700' }}">{{ $department === 'VAWC' ? 'SWD' : $department }}</a>
                @endforeach
            </div>
            @endif
        </form>
    @endif
    <div id="case-profiles-results" class="space-y-8">
    @foreach(['Victim Profiles' => $victims, 'Perpetrator Profiles' => $perpetrators] as $heading => $profiles)
        @php
            $isVictim = $heading === 'Victim Profiles';
            $hasProfileFilter = request()->filled('search') || request()->filled('age');
            $hideEmptySwdProfileTable = auth()->user()->defaultCaseDepartment() === 'VAWC'
                && $hasProfileFilter
                && $profiles->total() === 0;
            $hideEmptyBcpcProfileTable = auth()->user()->defaultCaseDepartment() === 'BCPC'
                && $hasProfileFilter
                && $profiles->total() === 0;
        @endphp
        @if(!$hideEmptySwdProfileTable && !$hideEmptyBcpcProfileTable)
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <h2 class="px-5 py-4 text-lg font-semibold text-purple-800">{{ $heading }}</h2>
            <div class="case-profiles-table-view">
            <div class="overflow-x-auto">
                <table class="case-profiles-table w-full min-w-[900px] table-fixed text-sm text-left">
                    <colgroup>
                        <col class="w-[16%]">
                        <col class="w-[12%]">
                        <col class="w-[22%]">
                        <col class="w-[9%]">
                        <col class="w-[21%]">
                        <col class="w-[20%]">
                    </colgroup>
                    <thead class="bg-purple-700 text-white">
                        <tr>
                            @foreach(['Name', 'Cases', 'Type of Case', 'Age', 'Address', 'Guardian Information'] as $column)
                                <th class="px-5 py-3 {{ $loop->last ? 'text-center align-middle' : '' }}">{{ $column }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($profiles as $records)
                            @php $person = $records->first(); @endphp
                            <tr class="align-top hover:bg-purple-50">
                                <td class="px-5 py-4 font-medium break-words">{{ $isVictim ? $person->victim_name : $person->abuser_name }}</td>
                                <td class="px-5 py-4">
                                    <details>
                                        <summary class="cursor-pointer text-purple-700">{{ $records->count() }} {{ $records->count() === 1 ? 'case' : 'cases' }}</summary>
                                        @foreach($records as $case)
                                            <a class="block mt-2 text-purple-700 underline break-words [overflow-wrap:anywhere]" href="{{ $isAdmin ? route('cases.show', ['id' => $case->id, 'from' => 'case-profiles']) : route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'from' => in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) ? 'case-profiles' : null]) }}">{{ $case->case_number }}</a>
                                        @endforeach
                                    </details>
                                </td>
                                <td class="px-5 py-4 break-words">{{ $records->map(fn($case) => ($isSwdDeskOfficer && $case->type === 'VAWC' ? 'SWD' : $case->type) . ' — ' . ($case->caseType?->name ?? $case->abuse_type))->unique()->implode(', ') }}</td>
                                <td class="px-5 py-4">{{ ($isVictim ? $person->victim_age : $person->abuser_age) ?? 'Not recorded' }}</td>
                                <td class="px-5 py-4 break-words">{{ ($isVictim ? $person->victim_address : $person->abuser_address) ?: 'Not recorded' }}</td>
                                <td class="px-5 py-4 align-middle text-center">
                                    @php
                                        $guardians = $records->map(fn($case) => $isVictim
                                            ? [$case->guardian, $case->relationship_to_victim, $case->guardian_contact, $case->guardian_address]
                                            : [$case->guardian_abuser_name, $case->relationship_to_abuser, $case->guardian_abuser_contact, $case->guardian_abuser_address]
                                        )->filter(fn($info) => count(array_filter($info)) > 0)->unique(fn($info) => json_encode($info));
                                    @endphp
                                    @if($guardians->isNotEmpty())
                                        @php $guardianDialogId = 'guardian-' . ($isVictim ? 'victim-' : 'perpetrator-') . $person->id; @endphp
                                        <div class="flex min-h-10 items-center justify-center">
                                            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-purple-700 text-white hover:bg-purple-800 text-xs font-medium shadow-xs transition" aria-haspopup="dialog" aria-controls="{{ $guardianDialogId }}" onclick="document.getElementById('{{ $guardianDialogId }}').showModal()">
                                                <i class="fas fa-eye flex h-4 w-4 shrink-0 items-center justify-center leading-none text-xs" aria-hidden="true"></i>
                                                <span class="sr-only">View</span>
                                            </button>
                                        </div>
                                        <dialog id="{{ $guardianDialogId }}" aria-labelledby="{{ $guardianDialogId }}-title" class="guardian-dialog rounded-2xl p-0 shadow-2xl w-full max-w-lg text-gray-800 border border-gray-100 overflow-hidden backdrop:bg-slate-900/60 backdrop:backdrop-blur-xs">
                                            {{-- Modal Header --}}
                                            <div class="bg-gradient-to-r from-purple-700 to-purple-600 px-6 py-4 flex items-center justify-between text-white">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-white text-base">
                                                        <i class="fas fa-user-shield"></i>
                                                    </div>
                                                    <div>
                                                        <h3 id="{{ $guardianDialogId }}-title" class="text-base font-bold text-white leading-tight">Guardian Information</h3>
                                                        <p class="text-xs text-purple-200 mt-0.5">{{ $isVictim ? 'Victim' : 'Perpetrator' }}: <span class="text-white font-semibold">{{ $isVictim ? $person->victim_name : $person->abuser_name }}</span></p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal Content --}}
                                            <div class="p-6 space-y-4 max-h-[65vh] overflow-y-auto">
                                                @foreach($guardians as $gIndex => $info)
                                                    <div class="bg-gray-50/80 border border-gray-200 rounded-xl p-4">
                                                        @if($guardians->count() > 1)
                                                            <div class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3 flex items-center gap-1.5">
                                                                <i class="fas fa-id-card"></i> Guardian #{{ $gIndex + 1 }}
                                                            </div>
                                                        @endif
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                                            <div>
                                                                <span class="text-[11px] text-gray-500 uppercase font-semibold block mb-0.5">Guardian Name</span>
                                                                <span class="font-bold text-gray-900 break-words">{{ $info[0] ?: 'Not recorded' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[11px] text-gray-500 uppercase font-semibold block mb-0.5">Relationship</span>
                                                                <span class="font-medium text-gray-800 break-words">{{ $info[1] ?: 'Not recorded' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[11px] text-gray-500 uppercase font-semibold block mb-0.5">Contact Number</span>
                                                                <span class="font-medium text-gray-800 break-words">{{ $info[2] ?: 'Not recorded' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-[11px] text-gray-500 uppercase font-semibold block mb-0.5">Address</span>
                                                                <span class="font-medium text-gray-800 break-words">{{ $info[3] ?: 'Not recorded' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Modal Footer --}}
                                            <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-200 flex justify-end">
                                                <button type="button" class="px-4 py-2 rounded-xl bg-purple-700 text-white text-xs font-semibold hover:bg-purple-800 transition shadow-xs" onclick="this.closest('dialog').close()">
                                                    Close
                                                </button>
                                            </div>
                                        </dialog>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500">No profiles found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if((auth()->user()->isDepartmentAdmin() || !$isAdmin) && $profiles->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Showing {{ $profiles->firstItem() ?? 0 }} - {{ $profiles->lastItem() ?? 0 }} of {{ $profiles->total() }} profiles
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ $profiles->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $profiles->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                            <i class="fas fa-chevron-left text-gray-600"></i>
                        </a>
                        @php
                            $current = $profiles->currentPage();
                            $last = $profiles->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);
                        @endphp
                        <div class="flex items-center gap-1">
                            @if($start > 1)
                                <a href="{{ $profiles->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">1</a>
                                @if($start > 2)
                                    <span class="px-2 text-gray-400">…</span>
                                @endif
                            @endif
                            @for($i = $start; $i <= $end; $i++)
                                <a href="{{ $profiles->url($i) }}" class="inline-flex items-center justify-center w-9 h-9 {{ $current == $i ? 'bg-[#C41E3A] text-white border border-[#C41E3A] font-medium' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} rounded-lg text-sm">
                                    {{ $i }}
                                </a>
                            @endfor
                            @if($end < $last)
                                @if($end < $last - 1)
                                    <span class="px-2 text-gray-400">…</span>
                                @endif
                                <a href="{{ $profiles->url($last) }}" class="inline-flex items-center justify-center w-9 h-9 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm">{{ $last }}</a>
                            @endif
                        </div>
                        <a href="{{ $profiles->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors text-sm {{ $profiles->currentPage() == $profiles->lastPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                            <i class="fas fa-chevron-right text-gray-600"></i>
                        </a>
                    </div>
                </div>
            @endif
            </div>

            <!-- Admin phone-only profile cards -->
            <div class="case-profiles-mobile-cards hidden p-3 space-y-3">
                @forelse($profiles as $records)
                    @php
                        $person = $records->first();
                        $profileAge = ($isVictim ? $person->victim_age : $person->abuser_age) ?? 'Not recorded';
                        $guardians = $records->map(fn($case) => $isVictim
                            ? [$case->guardian, $case->relationship_to_victim, $case->guardian_contact, $case->guardian_address]
                            : [$case->guardian_abuser_name, $case->relationship_to_abuser, $case->guardian_abuser_contact, $case->guardian_abuser_address]
                        )->filter(fn($info) => count(array_filter($info)) > 0)->unique(fn($info) => json_encode($info));
                        $guardianDialogId = 'guardian-' . ($isVictim ? 'victim-' : 'perpetrator-') . $person->id;
                    @endphp
                    <article class="case-profile-card bg-white rounded-xl border border-gray-200 shadow-sm" data-profile-age="{{ $profileAge }}">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 mb-1">{{ $isVictim ? 'Victim' : 'Perpetrator' }}</p>
                                <p class="font-semibold text-gray-800 break-words">{{ $isVictim ? $person->victim_name : $person->abuser_name }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700">{{ $records->count() }} {{ $records->count() === 1 ? 'case' : 'cases' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                            <div><p class="text-xs text-gray-500 mb-1">Age</p><p class="text-gray-800">{{ $profileAge }}</p></div>
                            <div class="min-w-0"><p class="text-xs text-gray-500 mb-1">Address</p><p class="text-gray-800 break-words">{{ ($isVictim ? $person->victim_address : $person->abuser_address) ?: 'Not recorded' }}</p></div>
                            <div class="col-span-2"><p class="text-xs text-gray-500 mb-1">Type of Case</p><p class="text-gray-800 break-words">{{ $records->map(fn($case) => ($isSwdDeskOfficer && $case->type === 'VAWC' ? 'SWD' : $case->type) . ' - ' . ($case->caseType?->name ?? $case->abuse_type))->unique()->implode(', ') }}</p></div>
                            <div class="col-span-2"><p class="text-xs text-gray-500 mb-1">Cases</p><div class="flex flex-wrap gap-x-3 gap-y-1">@foreach($records as $case)<a class="text-sm text-purple-700 underline" href="{{ $isAdmin ? route('cases.show', ['id' => $case->id, 'from' => 'case-profiles']) : route('desk-officer.cases.show', ['hash' => encrypt($case->id), 'from' => in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true) ? 'case-profiles' : null]) }}">{{ $case->case_number }}</a>@endforeach</div></div>
                        </div>
                        @if($guardians->isNotEmpty())
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100"><span class="text-sm font-medium text-gray-700">Guardian Information</span><button type="button" class="case-profile-card-action rounded-lg bg-purple-700 text-white hover:bg-purple-800 transition" aria-haspopup="dialog" aria-controls="{{ $guardianDialogId }}" onclick="const guardianDialog = document.getElementById('{{ $guardianDialogId }}'); if (guardianDialog && !guardianDialog.open) { document.body.appendChild(guardianDialog); guardianDialog.showModal(); }"><i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View guardian information</span></button></div>
                        @endif
                    </article>
                @empty
                <div class="rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-500">No profiles found.</div>
                @endforelse
                @if((auth()->user()->isDepartmentAdmin() || !$isAdmin) && $profiles->hasPages())
                    @include('components.archived-pagination', ['paginator' => $profiles, 'label' => 'profiles'])
                @endif
            </div>
        </section>
        @endif
    @endforeach
    @if(auth()->user()->position === 'Desk Officer' && auth()->user()->defaultCaseDepartment() === 'VAWC' && (request()->filled('search') || request()->filled('age')) && $victims->total() === 0 && $perpetrators->total() === 0)
        <div class="bg-white border border-gray-200 rounded-xl px-5 py-8 text-center text-gray-500">
            No results found.
        </div>
    @endif
    </div>
</div>
<style>
    .guardian-dialog { margin: auto; max-width: min(32rem, calc(100vw - 2rem)); max-height: 85vh; overflow-y: auto; }
    .guardian-dialog::backdrop { background: rgba(0, 0, 0, .5); }
</style>
@if(auth()->user()->defaultCaseDepartment() === 'BCPC' || (auth()->user()->position === 'Desk Officer' && auth()->user()->defaultCaseDepartment() === 'VAWC'))
@push('scripts')
<script>
(() => {
    const searchInput = document.getElementById('profile-search-input') || document.querySelector('#case-profiles-filter-form input[name="search"]');
    const ageInput = document.getElementById('profile-age-input') || document.querySelector('#case-profiles-filter-form input[name="age"]');

    function filterProfileRows() {
        const q = (searchInput?.value || '').toLowerCase().trim();
        const ageVal = (ageInput?.value || '').trim();

        const isMobileProfileView = window.matchMedia('(max-width: 47.9375rem)').matches;
        const profileItems = isMobileProfileView
            ? document.querySelectorAll('.case-profile-card')
            : document.querySelectorAll('#case-profiles-results table tbody tr');
        profileItems.forEach(row => {
            if (!isMobileProfileView && row.cells.length < 5) return;
            const text = row.textContent.toLowerCase();
            const ageCell = isMobileProfileView
                ? (row.dataset.profileAge || '').trim()
                : (row.cells[3]?.textContent || '').trim();
            const matchesSearch = !q || text.includes(q);
            const matchesAge = !ageVal || ageCell === ageVal;
            row.style.display = (matchesSearch && matchesAge) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterProfileRows);
    if (ageInput) ageInput.addEventListener('input', filterProfileRows);

    // Reset unsaved changes state when clicking Clear
    document.querySelectorAll('#case-profiles-filter-form a').forEach(a => {
        if (a.textContent.trim().toLowerCase() === 'clear') {
            a.addEventListener('click', () => {
                if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
            });
        }
    });
})();
</script>
@endpush
@endif
@if(auth()->user()->isVawcAdmin())
@push('scripts')
<script>
(() => {
    let activeRequest;
    const refreshProfiles = async (url) => {
        activeRequest?.abort();
        const request = new AbortController();
        activeRequest = request;
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: request.signal,
            });
            if (!response.ok) return;
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const replacement = page.querySelector('#case-profiles-results');
            const current = document.querySelector('#case-profiles-results');
            if (!replacement || !current || request.signal.aborted || activeRequest !== request) return;
            replacement.style.minHeight = `${current.offsetHeight}px`;
            current.replaceWith(replacement);
            history.replaceState({}, '', url);
        } catch (error) {
            if (error.name !== 'AbortError') throw error;
        }
    };

    document.addEventListener('input', (event) => {
        const form = event.target.closest('#case-profiles-filter-form');
        if (!form || !['search', 'number'].includes(event.target.type)) return;
        const url = new URL(form.action, location.origin);
        url.search = new URLSearchParams(new FormData(form)).toString();
        refreshProfiles(url);
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('#case-profiles-content a[href]');
        if (!link) return;
        const url = new URL(link.href, location.origin);
        if (url.origin !== location.origin || !['victims_page', 'perpetrators_page'].some((name) => url.searchParams.has(name))) return;
        event.preventDefault();
        refreshProfiles(url);
    });
})();
</script>
@endpush
@endif

@push('scripts')
<script>
(function () {
    var body = document.body;
    var themed = body.classList.contains('bcpc-admin-header-theme')
        || body.classList.contains('swd-admin-header-theme')
        || body.classList.contains('desk-officer-header-theme');
    if (!themed) return;

    var mq = window.matchMedia('(max-width: 63.9375rem)');

    var apply = function () {
        if (mq.matches) {
            body.classList.add('case-profiles-mobile-nav');
        } else {
            body.classList.remove('case-profiles-mobile-nav');
        }

        var sidebar = document.getElementById('sidebar');
        if (!sidebar) return;

        var open = mq.matches && sidebar.classList.contains('sidebar-open');
        if (open) {
            var header = document.querySelector('#mainContent > header') || document.querySelector('#mainContent > .portal-clock-only-header');
            if (header) {
                var bottom = Math.max(0, header.getBoundingClientRect().bottom);
                sidebar.style.setProperty('top', bottom + 'px', 'important');
                sidebar.style.setProperty('max-height', 'calc(100vh - ' + bottom + 'px)', 'important');
            }
            body.style.overflow = 'hidden';
            document.documentElement.style.setProperty('overflow-y', 'hidden', 'important');
        } else {
            sidebar.style.removeProperty('top');
            sidebar.style.removeProperty('max-height');
            body.style.overflow = '';
            document.documentElement.style.removeProperty('overflow-y');
        }
    };

    var sidebar = document.getElementById('sidebar');
    if (sidebar) {
        new MutationObserver(apply).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
    }
    mq.addEventListener('change', apply);
    window.addEventListener('resize', apply);
    document.addEventListener('DOMContentLoaded', apply);
    apply();
})();
</script>
@endpush
@endsection
