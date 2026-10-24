@extends('layouts.admin-tailwind')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/bcpc-monitoring-mobile-cards.css') }}">
@if($isView || request()->filled('edit'))
<link rel="stylesheet" href="{{ asset('css/bcpc-monitoring-card-layout.css') }}?v={{ filemtime(public_path('css/bcpc-monitoring-card-layout.css')) }}" media="screen and (max-width: 80rem)">
<link rel="stylesheet" href="{{ asset('css/bcpc-monitoring-tablet-cards.css') }}" media="screen and (min-width: 48rem) and (max-width: 80rem)">
@endif
<link rel="stylesheet" href="{{ asset('css/desk-officer-tables.css') }}">
<style>
    @if($isView || request()->filled('edit'))
    @media (max-width: 80rem) {
        #mainContent .bcpc-monitoring-back-to-list {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            box-sizing: border-box;
            padding: .5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            background: #ffffff;
            color: #374151;
            font-size: .875rem;
            font-weight: 500;
            line-height: 1.25rem;
            box-shadow: 0 1px 2px rgb(0 0 0 / 5%);
        }
        #mainContent .bcpc-monitoring-back-to-list:hover {
            background: #f9fafb;
            color: #374151;
        }
        #mainContent .bcpc-monitoring-back-to-list,
        #mainContent .bcpc-monitoring-back-to-list:is(:hover, :focus, :active),
        #mainContent .bcpc-monitoring-back-to-list > :is(i, span) {
            color: #374151 !important;
        }
        #mainContent .bcpc-monitoring-back-to-list > i {
            margin: 0;
            flex-shrink: 0;
        }
    }
    @endif
    @if(request()->filled('edit'))
    @media (max-width: 47.9375rem) {
        #bcpc-monitoring-save-actions {
            align-items: stretch;
            gap: .75rem;
            margin-top: 1rem;
            padding-top: 0;
            border-top: 0;
        }
        #bcpc-monitoring-save-actions > div:last-child { width: 100%; }
        #bcpc-monitoring-save-actions button[type="submit"] {
            justify-content: center;
            width: 100%;
            min-width: 0;
            min-height: 3rem;
            padding: .75rem 1rem;
            font-size: .875rem;
            font-weight: 500;
            line-height: 1.35;
            white-space: normal;
            background-image: none;
            box-shadow: 0 1px 2px rgb(0 0 0 / 5%);
        }
    }
    @endif
    @media (min-width: 48rem) and (max-width: 85.375rem) and (orientation: portrait) {
        #bcpc-monitoring-save-actions {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100%;
        }
        #bcpc-monitoring-save-actions > div:first-child { flex: 1 1 auto; min-width: 0; }
        #bcpc-monitoring-save-actions > div:last-child { flex: 0 0 auto; margin-left: auto; }
    }
</style>
@if(auth()->user()->isDepartmentAdmin())
@include('admin-tailwind.quarterly-reports._portrait-sidebar')
<style>
    @media (min-width: 48rem) {
        body:has([data-bcpc-monitoring-form-header]) #mobileTopBar { display: none !important; }
    }
    @media (min-width: 48rem) and (max-width: 80rem) {
        body:has([data-bcpc-monitoring-form-header]) #sidebar {
            inset: 0 auto 0 0 !important;
            height: 100dvh !important;
        }
        body:has([data-bcpc-monitoring-form-header]) #sidebarToggleBtn { top: 1.5rem !important; }
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
@endif
@endpush

@section('title', 'BCPC Case Monitoring')

@section('content')

@php
    $isGenerated = auth()->user()->isBcpcAdmin() && request()->boolean('generate');
    $isBcpcAdminAdd = auth()->user()->isBcpcAdmin() && request()->boolean('add');
    $isMonitoringEdit = auth()->user()->isDepartmentAdmin() && request()->filled('edit');
    $isReferenceHeader = $isView || $isMonitoringEdit || (auth()->user()->isDepartmentAdmin() && request()->boolean('add'));
    $data = $monitoring->data ?? [];
    $currentYear = (int) ($year ?? now()->year);
    $yearGroups = [
        'year_2023' => 'Year ' . ($currentYear - 2),
        'year_2024' => 'Year ' . ($currentYear - 1),
        'total_2025' => 'Year ' . $currentYear,
    ];
    $sexColumns = ['male' => 'Male', 'female' => 'Female', 'total' => 'Total'];
    $tables = [
        [
            'title' => 'Crimes Committed Against Children',
            'note' => 'What types of crime have been committed against children in the past 2 years?',
            'name' => 'bcpc_crimes_against_children',
            'label' => 'Crimes Against Children',
            'rows' => [
                'rape' => 'Rape',
                'incestuous_rape' => 'Incestuous Rape',
                'attempted_frustrated_rape' => 'Attempted/Frustrated Rape',
                'acts_of_lasciviousness' => 'Acts of Lasciviousness',
                'physical_injuries' => 'Physical Injuries',
                'kidnapping_illegal_detention' => 'Kidnapping/Illegal Detention',
                'trafficking' => 'RA 9208 amended by RA 10634 (Trafficking in persons)',
                'drugs_substance_abuse' => 'Drugs/Substance Abuse',
                'others' => 'Others, specify',
                'total' => 'TOTAL'
            ]
        ],
        [
            'title' => 'Crimes Committed by Children in Conflict with the Law (CICL)',
            'note' => 'What types of crime have been committed by children in conflict with the law (CICL) in the past 2 years?',
            'name' => 'bcpc_cicl_crimes',
            'label' => 'Crimes Against Children',
            'rows' => [
                'rape' => 'Rape',
                'incestuous_rape' => 'Incestuous Rape',
                'attempted_frustrated_rape' => 'Attempted/Frustrated Rape',
                'acts_of_lasciviousness' => 'Acts of Lasciviousness',
                'physical_injuries' => 'Physical Injuries'
            ]
        ],
        [
            'title' => 'Children in Conflict with the Law (CICL) and Status',
            'note' => 'Summary: Number of Children in Conflict with the Law (CICL) and their status. Choose one category and avoid double counting.',
            'name' => 'bcpc_cicl_status',
            'label' => 'Categories of CICL',
            'rows' => [
                'diversion_program' => 'Number of CICL undergoing Diversion Program',
                'jail_detention_center' => 'Number of CICL in Jail/Detention Center',
                'bahay_pagasa' => 'Number of CICL in Bahay Pag-asa',
                'ijisc' => 'Number of CICL under Intensive Juvenile Intervention and Support for Center (IJISC)',
                'rrcy' => 'Number of CICL in RRCY',
                'after_care_program' => 'Number of CICL undergoing After-Care Program',
                're_offended' => 'Number of CICL that have re-offended',
                'fully_integrated' => 'Number of CICL that have been fully integrated',
                'community_based' => 'Number of CICL undergoing community-based',
                'cbdr' => 'Number of CICL undergoing Community-Based Rehabilitation Program (CBDR)',
                'others' => 'Others, please specify:',
                'total' => 'Total'
            ]
        ],
        [
            'title' => 'Children at Risk',
            'note' => 'Number of Children at Risk as defined in RA 9344 as amended. Choose only one category for the child and avoid double counting.',
            'name' => 'bcpc_children_at_risk',
            'label' => 'Categories of CAR',
            'rows' => [
                'curfew_hour' => 'Curfew Hour',
                'truancy' => 'Truancy',
                'parental_disobedience' => 'Parental Disobedience',
                'anti_smoking' => 'Anti-Smoking',
                'anti_drinking' => 'Anti-Drinking',
                'disorderly_conduct' => 'Disorderly conduct',
                'public_scandal' => 'Public scandal',
                'harassment' => 'Harassment',
                'public_intoxication' => 'Public intoxication',
                'criminal_nuisance' => 'Criminal nuisance',
                'vandalism' => 'Vandalism',
                'gambling' => 'Gambling',
                'mendicancy' => 'Mendicancy (Presidential Decree No. 1563)'
            ]
        ],
        [
            'title' => 'Children Needing Special Protection',
            'note' => 'Children needing special protection and other child protection categories.',
            'name' => 'bcpc_special_protection',
            'label' => 'Category',
            'rows' => [
                'parents_lost_jobs_covid' => 'Children whose parents lost jobs due to COVID-19',
                'children_with_disabilities' => 'Children with disabilities',
                'indigenous_families' => 'Children of indigenous families',
                'four_ps_families' => '4Ps families',
                'child_surrenderers_drug_campaign' => 'Child-surrenderers in relation to the Philippine Campaign Against Illegal Drugs',
                'total' => 'TOTAL'
            ]
        ],
    ];
@endphp
@if((auth()->user()?->isBcpcAdmin() && ($isView || request()->filled('edit') || $isBcpcAdminAdd)) || ($isView && request()->boolean('archived') && auth()->user()?->isVawcAdmin()) || (request()->filled('edit') && auth()->user()?->isVawcAdmin()))
<header data-bcpc-monitoring-form-header class="{{ $isReferenceHeader ? 'admin-reference-mobile-header' : '' }} sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm portal-page-header">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div class="{{ $isReferenceHeader ? 'admin-reference-mobile-leading' : '' }} min-w-0 {{ $isView && request()->boolean('archived') ? 'flex-1' : '' }}">
            @if($isReferenceHeader)
            <button id="dashboardMenuToggle" type="button" aria-label="Open navigation menu" aria-controls="sidebar" aria-expanded="false">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            @endif
            <div class="{{ $isReferenceHeader ? 'admin-reference-mobile-title' : '' }}">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">BCPC Case Monitoring</h2>
                <p class="{{ $isReferenceHeader ? 'admin-reference-mobile-subtitle' : '' }} text-sm text-gray-500">Barangay {{ $barangay->name }} &bull; Period: Q{{ $quarter }} {{ $year }}</p>
            </div>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
<div class="max-w-7xl mx-auto py-4 px-2 sm:px-4">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        @if(!((auth()->user()?->isBcpcAdmin() && ($isView || request()->filled('edit') || $isBcpcAdminAdd)) || ($isView && request()->boolean('archived') && auth()->user()?->isVawcAdmin()) || (request()->filled('edit') && auth()->user()?->isVawcAdmin())))
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">BCPC Case Monitoring</h1>
            <p class="text-gray-500 text-sm mt-1">Barangay {{ $barangay->name }} &bull; Period: Q{{ $quarter }} {{ $year }}</p>
        </div>
        @endif
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ $isView && request()->boolean('archived') ? route('admin.bcpc-case-monitoring.archived') : route('admin.bcpc-case-monitoring.edit') }}"
               class="bcpc-monitoring-back-to-list inline-flex items-center gap-1.5 text-sm font-medium text-purple-600 hover:text-purple-800 transition">
                <i class="fas fa-arrow-left"></i>
                <span>{{ $isView && request()->boolean('archived') ? 'Back to Archived BCPC Reports' : 'Back to List' }}</span>
            </a>
            @if(!$isView && !auth()->user()->isBcpcAdmin())
            <button type="button" onclick="window.open('{{ route('admin.bcpc-case-monitoring.print', ['barangay_id' => $barangay->id, 'quarter' => $quarter, 'year' => $year]) }}', '_blank', 'noopener')"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium shadow-sm transition">
                <i class="fas fa-print"></i>
                <span>Print Report</span>
            </button>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($barangay)
        @if($isView || request()->filled('edit'))
        <div id="bcpc-monitoring-section-filter-panel" data-monitoring-filter hidden>
            <label for="bcpc-monitoring-section-filter">Show section</label>
            <div class="bcpc-monitoring-section-filter-controls">
            <select id="bcpc-monitoring-section-filter">
                @foreach($tables as $table)
                    <option value="{{ $table['name'] }}">{{ $table['title'] }}</option>
                @endforeach
                <option value="bcpc_organizations">Child Protection Organizations</option>
                <option value="all">All sections</option>
            </select>
                <button type="button" id="bcpc-monitoring-clear-section-filter">
                    <i class="fas fa-times" aria-hidden="true"></i>
                    <span>Clear filter</span>
                </button>
            </div>
        </div>

        @endif
        <!-- Monitoring Data Form -->
        <form id="bcpc-monitoring-form" data-card-max-width="{{ ($isView || request()->filled('edit')) ? '80rem' : '47.9375rem' }}" method="POST" action="{{ route('admin.bcpc-case-monitoring.update') }}" class="space-y-6">
            @csrf
            @if(auth()->user()->isBcpcAdmin() && $monitoring?->exists)<input type="hidden" name="monitoring_id" value="{{ $monitoring->id }}">@endif
            <input type="hidden" name="barangay_id" value="{{ $barangay->id }}">
            <input type="hidden" name="quarter" value="{{ $quarter }}">
            <input type="hidden" name="year" value="{{ $year }}">

            @foreach($tables as $table)
                <div data-monitoring-section="{{ $table['name'] }}" data-page-size="1" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-800 to-purple-700 px-6 py-4 text-white">
                        <h2 class="text-base font-bold">{{ $table['title'] }}</h2>
                        <p class="text-xs text-purple-200 mt-0.5">{{ $table['note'] }}</p>
                    </div>

                    <div class="bcpc-monitoring-table-wrap overflow-x-auto">
                        <table data-monitoring-cards class="bcpc-monitoring-table w-full text-xs border-collapse">
                            <thead>
                                <tr class="bg-purple-50 text-purple-900 border-b border-purple-100">
                                    <th rowspan="2" class="border border-purple-100 px-4 py-3 text-left font-semibold min-w-[260px]">
                                        {{ $table['label'] }}
                                    </th>
                                    @foreach($yearGroups as $yearKey => $label)
                                        <th colspan="3" class="border border-purple-100 px-3 py-2 text-center font-bold {{ $yearKey === 'total_2025' ? 'bg-purple-100 text-purple-900' : '' }}">
                                            {{ $label }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr class="bg-purple-50/60 text-purple-800 border-b border-purple-100">
                                    @foreach($yearGroups as $yearKey => $label)
                                        @foreach($sexColumns as $sexKey => $sexLabel)
                                            <th class="border border-purple-100 px-2.5 py-1.5 text-center font-medium {{ $yearKey === 'total_2025' ? 'bg-purple-100/70' : '' }}">
                                                {{ $sexLabel }}
                                            </th>
                                        @endforeach
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($table['rows'] as $rowKey => $rowLabel)
                                    @php $isTotal = $rowKey === 'total'; @endphp
                                    <tr data-total-row="{{ $isTotal ? 'true' : 'false' }}" class="{{ $isTotal ? 'bg-purple-50/40 font-bold' : 'hover:bg-gray-50' }}">
                                        <td class="bcpc-monitoring-row-label border border-gray-200 px-4 py-2 {{ $isTotal ? 'text-purple-900 font-bold' : 'text-gray-800' }}">
                                            {{ $rowLabel }}
                                        </td>
                                        @foreach($yearGroups as $yearKey => $label)
                                            @foreach($sexColumns as $sexKey => $sexLabel)
                                                <td class="bcpc-monitoring-value-cell border border-gray-200 p-1 text-center {{ $yearKey === 'total_2025' ? 'bg-purple-50/20' : '' }}">
                                                    @if($sexKey === 'male')
                                                        <span class="bcpc-monitoring-mobile-year" aria-hidden="true">{{ $label }}</span>
                                                    @endif
                                                    <div class="bcpc-monitoring-field">
                                                        <span class="bcpc-monitoring-mobile-label" aria-hidden="true">{{ $sexLabel }}</span>
                                                    <input aria-label="{{ $rowLabel }}, {{ $label }}, {{ $sexLabel }}" type="number"
                                                           min="0"
                                                           name="{{ $table['name'] }}[{{ $rowKey }}][{{ $yearKey }}][{{ $sexKey }}]"
                                                           value="{{ old($table['name'].'.'.$rowKey.'.'.$yearKey.'.'.$sexKey, data_get($data, $table['name'].'.'.$rowKey.'.'.$yearKey.'.'.$sexKey, 0)) }}"
                                                           @if($isView) readonly @endif
                                                           class="w-full text-center border-0 rounded px-1.5 py-1 text-xs focus:ring-1 focus:ring-purple-500 {{ $isTotal ? 'font-bold text-purple-900 bg-transparent' : ($isView ? 'bg-transparent text-gray-700' : 'bg-white hover:bg-gray-50') }}">
                                                    </div>
                                                </td>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('desk-officer.partials.monitoring-mobile-pagination', ['paginationLabel' => $table['title']])
                </div>
            @endforeach

            <!-- Child Protection Organizations -->
            <div data-monitoring-section="bcpc_organizations" data-page-size="1" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-purple-800 to-purple-700 px-6 py-4 text-white">
                    <h2 class="text-base font-bold">Child Protection Organizations</h2>
                    <p class="text-xs text-purple-200 mt-0.5">List of NGOs, CSOs, FBOs, POs, and Youth/Children Organizations in your Barangay.</p>
                </div>

                <div class="bcpc-monitoring-table-wrap overflow-x-auto">
                    <table data-monitoring-cards class="bcpc-organizations-table w-full text-xs border-collapse">
                        <thead>
                            <tr class="bg-purple-50 text-purple-900 border-b border-purple-100">
                                <th class="border border-purple-100 px-4 py-3 text-left font-semibold">Name of Organization</th>
                                <th class="border border-purple-100 px-4 py-3 text-left font-semibold">Responsible Person & Designation</th>
                                <th class="border border-purple-100 px-4 py-3 text-left font-semibold">Email Address</th>
                                <th class="border border-purple-100 px-4 py-3 text-left font-semibold">Phone / Mobile</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @for($i = 0; $i < 3; $i++)
                                <tr class="hover:bg-gray-50" data-card-title="Organization {{ $i + 1 }}">
                                    <td class="border border-gray-200 p-2">
                                        <span class="bcpc-monitoring-mobile-label">Name of Organization</span>
                                        <input type="text"
                                               name="bcpc_organizations[{{ $i }}][name]"
                                               value="{{ old('bcpc_organizations.'.$i.'.name', data_get($data, 'bcpc_organizations.'.$i.'.name')) }}"
                                               placeholder="Organization Name"
                                               @if($isView) readonly @endif
                                               class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-purple-500 {{ $isView ? 'bg-gray-50' : 'bg-white' }}">
                                    </td>
                                    <td class="border border-gray-200 p-2">
                                        <span class="bcpc-monitoring-mobile-label">Responsible Person &amp; Designation</span>
                                        <input type="text"
                                               name="bcpc_organizations[{{ $i }}][responsible_person]"
                                               value="{{ old('bcpc_organizations.'.$i.'.responsible_person', data_get($data, 'bcpc_organizations.'.$i.'.responsible_person')) }}"
                                               placeholder="Name & Position"
                                               @if($isView) readonly @endif
                                               class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-purple-500 {{ $isView ? 'bg-gray-50' : 'bg-white' }}">
                                    </td>
                                    <td class="border border-gray-200 p-2">
                                        <span class="bcpc-monitoring-mobile-label">Email Address</span>
                                        <input type="email"
                                               name="bcpc_organizations[{{ $i }}][email]"
                                               value="{{ old('bcpc_organizations.'.$i.'.email', data_get($data, 'bcpc_organizations.'.$i.'.email')) }}"
                                               placeholder="email@example.com"
                                               @if($isView) readonly @endif
                                               class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-purple-500 {{ $isView ? 'bg-gray-50' : 'bg-white' }}">
                                    </td>
                                    <td class="border border-gray-200 p-2">
                                        <span class="bcpc-monitoring-mobile-label">Phone / Mobile</span>
                                        <input type="text"
                                               name="bcpc_organizations[{{ $i }}][phone]"
                                               value="{{ old('bcpc_organizations.'.$i.'.phone', data_get($data, 'bcpc_organizations.'.$i.'.phone')) }}"
                                               placeholder="Phone Number"
                                               @if($isView) readonly @endif
                                               class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-purple-500 {{ $isView ? 'bg-gray-50' : 'bg-white' }}">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                @include('desk-officer.partials.monitoring-mobile-pagination', ['paginationLabel' => 'Child Protection Organizations'])
            </div>

            @if($isGenerated)
                <div class="pt-4 flex justify-end">
                    <a href="{{ route('admin.bcpc-case-monitoring.print', ['barangay_id' => $barangay->id, 'quarter' => $quarter, 'year' => $year]) }}" target="_blank" rel="noopener" class="px-6 py-3 rounded-xl bg-purple-700 text-white font-semibold hover:bg-purple-800">Preview / Print Report</a>
                </div>
            @endif
            @if(!$isView)
            <!-- Action Buttons -->
            <div id="bcpc-monitoring-save-actions" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle text-purple-600 mr-1"></i>
                    Saving will update the BCPC case monitoring record.
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-700 to-purple-900 hover:from-purple-800 hover:to-purple-950 text-white font-semibold px-6 py-3 rounded-xl shadow-md hover:shadow-lg transition">
                        <i class="fas fa-save"></i>
                        <span>Save BCPC Case Monitoring</span>
                    </button>
                </div>
            </div>
            @endif
        </form>
    @endif
</div>

@unless(auth()->user()->isBcpcAdmin())
<!-- Print Modal -->
<div id="printModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h2 class="text-xl font-bold text-center mb-4">Print Monitoring Report</h2>
        <p class="text-gray-600 text-center mb-6">Select the format for printing the BCPC Case Monitoring report.</p>
        <div class="space-y-3">
            <button onclick="window.print()" class="w-full px-4 py-3 bg-purple-700 hover:bg-purple-800 text-white rounded-lg font-medium">
                Print as PDF
            </button>
            <button onclick="document.getElementById('printModal').classList.add('hidden')" class="w-full px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
        </div>
    </div>
</div>

@endunless
@endsection

@push('scripts')
@if($isView || request()->filled('edit'))
<script src="{{ asset('js/bcpc-monitoring-mobile-cards.js') }}" defer></script>
@endif
@include('components.bcpc-monitoring-input-restrictions')
<script>
document.addEventListener('DOMContentLoaded', () => {
    @if(!$isView)
    document.querySelectorAll('.bcpc-monitoring-table').forEach((table) => {
        const updateTotals = () => {
            const rows = [...table.tBodies[0].rows];
            rows.forEach((row) => {
                const cells = row.cells;
                const isTotalRow = cells[0].textContent.trim().toLowerCase() === 'total';

                for (let start = 1; start < cells.length; start += 3) {
                    const maleInput = cells[start]?.querySelector('input');
                    const femaleInput = cells[start + 1]?.querySelector('input');
                    const totalInput = cells[start + 2]?.querySelector('input');

                    if (!maleInput || !femaleInput || !totalInput) continue;

                    if (isTotalRow) {
                        const otherRows = rows.filter((r) => r !== row);
                        const sums = otherRows.reduce((acc, r) => {
                            const mVal = Number(r.cells[start]?.querySelector('input')?.value || 0);
                            const fVal = Number(r.cells[start + 1]?.querySelector('input')?.value || 0);
                            acc.male += mVal;
                            acc.female += fVal;
                            return acc;
                        }, { male: 0, female: 0 });

                        maleInput.value = sums.male;
                        femaleInput.value = sums.female;
                        totalInput.value = sums.male + sums.female;

                        maleInput.readOnly = true;
                        femaleInput.readOnly = true;
                        totalInput.readOnly = true;
                    } else {
                        const mVal = Number(maleInput.value || 0);
                        const fVal = Number(femaleInput.value || 0);
                        totalInput.value = mVal + fVal;
                        totalInput.readOnly = true;
                    }
                }
            });
        };

        table.addEventListener('input', updateTotals);
        updateTotals();

        table.querySelectorAll('input[type="number"]:not([readonly])').forEach((input) => {
            input.addEventListener('focus', () => {
                if (input.value === '0') {
                    input.value = '';
                }
            });

            input.addEventListener('blur', () => {
                if (input.value === '') {
                    input.value = '0';
                    updateTotals();
                }
            });
        });
    });
    @endif
});
</script>
@endpush
