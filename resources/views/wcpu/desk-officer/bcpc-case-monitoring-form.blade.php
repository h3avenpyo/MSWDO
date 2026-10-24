@extends('layouts.desk-officer')

@section('title', 'BCPC Case Monitoring Form')

@push('styles')
@php
    $usesTabletMonitoringCards = $isView || request()->filled('edit') || request()->filled('add');
@endphp
<link rel="stylesheet" href="{{ asset('css/bcpc-monitoring-mobile-cards.css') }}?v={{ filemtime(public_path('css/bcpc-monitoring-mobile-cards.css')) }}">
<link rel="stylesheet" href="{{ asset('css/bcpc-monitoring-card-layout.css') }}?v={{ filemtime(public_path('css/bcpc-monitoring-card-layout.css')) }}" media="screen and (max-width: {{ $usesTabletMonitoringCards ? '80rem' : '47.9375rem' }})">
@if($usesTabletMonitoringCards)
<link rel="stylesheet" href="{{ asset('css/bcpc-monitoring-tablet-cards.css') }}?v={{ filemtime(public_path('css/bcpc-monitoring-tablet-cards.css')) }}" media="screen and (min-width: 48rem) and (max-width: 80rem)">
@endif
<style>
    @media (max-width: 47.9375rem) {
        body.portal-ui #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle {
            font-size: 1.125rem !important;
        }

        body.portal-ui #mainContent > header#desk-officer-dashboard-header #pageMobileMenuToggle > i {
            font-size: inherit !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body.portal-ui #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"] {
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
            position: relative !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        body.portal-ui #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"]:hover {
            background: #8f0202 !important;
        }

        body.portal-ui #mainContent > header#desk-officer-dashboard-header button[aria-label="Notifications"] > i {
            font-size: 1rem !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
            color: #ffffff !important;
        }

        body.portal-ui #mainContent > header#desk-officer-dashboard-header #notifications-unread-count {
            position: absolute !important;
            width: 1.25rem !important;
            min-width: 1.25rem !important;
            height: 1.25rem !important;
            top: -.25rem !important;
            right: -.25rem !important;
            padding: 0 !important;
            font-size: .75rem !important;
            line-height: 1 !important;
            background-color: #eab308 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border: 2px solid #C41E3A !important;
            border-radius: 9999px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2) !important;
        }
    }
</style>
@endpush

@section('content')

@php
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
@if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->canAccessCaseDepartment('BCPC'))
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'BCPC Case Monitoring', 'subtitle' => 'Barangay ' . $barangay->name . ' • Period: Q' . $quarter . ' ' . $year, 'titleClass' => ($fromArchived ?? request()->boolean('archived')) ? 'flex-1 min-w-0' : ''])
@endif
<div class="max-w-7xl mx-auto py-4 px-2 sm:px-4">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        @php
            $isSingleBcpcDeskOfficer = auth()->user()->defaultCaseDepartment() === 'BCPC'
                && !auth()->user()->isMultiCaseDepartment();
            $isArchivedMonitoringView = ($fromArchived ?? false)
                || !empty($monitoring?->archived_at)
                || request()->boolean('archived');
        @endphp
        @unless(auth()->user()?->position === 'Desk Officer' && auth()->user()?->canAccessCaseDepartment('BCPC'))
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">BCPC Case Monitoring</h1>
            <p class="text-gray-500 text-sm mt-1">Barangay {{ $barangay->name }} &bull; Period: Q{{ $quarter }} {{ $year }}</p>
        </div>
        @endunless
        <div class="flex flex-wrap items-center gap-3 {{ $isSingleBcpcDeskOfficer && $isArchivedMonitoringView ? 'w-full justify-between' : '' }}">
            <a href="{{ ($fromArchived ?? false) ? route('desk-officer.bcpc-case-monitoring.archived') : route('desk-officer.bcpc-case-monitoring.edit') }}"
               data-back
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                <i class="fas fa-arrow-left"></i>
                <span>Back to List</span>
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($barangay)
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
            @if($usesTabletMonitoringCards)
                <button type="button" id="bcpc-monitoring-clear-section-filter">
                    <i class="fas fa-times" aria-hidden="true"></i>
                    <span>Clear filter</span>
                </button>
            @endif
            </div>
        </div>

        <!-- Monitoring Data Form -->
        <form id="bcpc-monitoring-form" data-card-max-width="{{ $usesTabletMonitoringCards ? '80rem' : '47.9375rem' }}" method="POST" action="{{ route('desk-officer.bcpc-case-monitoring.update') }}" class="space-y-6">
            @csrf
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
                                                        <input type="number"
                                                               aria-label="{{ $rowLabel }}, {{ $label }}, {{ $sexLabel }}"
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
                                        <label class="bcpc-monitoring-mobile-label" for="bcpc-organization-{{ $i }}-name">Name of Organization</label>
                                        <input type="text" 
                                               id="bcpc-organization-{{ $i }}-name"
                                               name="bcpc_organizations[{{ $i }}][name]" 
                                               value="{{ old('bcpc_organizations.'.$i.'.name', data_get($data, 'bcpc_organizations.'.$i.'.name')) }}" 
                                               placeholder="Organization Name"
                                               @if($isView) readonly @endif
                                               class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-purple-500 {{ $isView ? 'bg-gray-50' : 'bg-white' }}">
                                    </td>
                                    <td class="border border-gray-200 p-2">
                                        <label class="bcpc-monitoring-mobile-label" for="bcpc-organization-{{ $i }}-person">Responsible Person &amp; Designation</label>
                                        <input type="text" 
                                               id="bcpc-organization-{{ $i }}-person"
                                               name="bcpc_organizations[{{ $i }}][responsible_person]" 
                                               value="{{ old('bcpc_organizations.'.$i.'.responsible_person', data_get($data, 'bcpc_organizations.'.$i.'.responsible_person')) }}" 
                                               placeholder="Name & Position"
                                               @if($isView) readonly @endif
                                               class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-purple-500 {{ $isView ? 'bg-gray-50' : 'bg-white' }}">
                                    </td>
                                    <td class="border border-gray-200 p-2">
                                        <label class="bcpc-monitoring-mobile-label" for="bcpc-organization-{{ $i }}-email">Email Address</label>
                                        <input type="email" 
                                               id="bcpc-organization-{{ $i }}-email"
                                               name="bcpc_organizations[{{ $i }}][email]" 
                                               value="{{ old('bcpc_organizations.'.$i.'.email', data_get($data, 'bcpc_organizations.'.$i.'.email')) }}" 
                                               placeholder="email@example.com"
                                               @if($isView) readonly @endif
                                               class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-purple-500 {{ $isView ? 'bg-gray-50' : 'bg-white' }}">
                                    </td>
                                    <td class="border border-gray-200 p-2">
                                        <label class="bcpc-monitoring-mobile-label" for="bcpc-organization-{{ $i }}-phone">Phone / Mobile</label>
                                        <input type="text" 
                                               id="bcpc-organization-{{ $i }}-phone"
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

            <!-- Action Buttons -->
            <div class="bcpc-monitoring-form-actions flex flex-wrap items-center justify-end gap-3 pt-4">
                @unless($isView)
                <a href="{{ ($fromArchived ?? false) ? route('desk-officer.bcpc-case-monitoring.archived') : route('desk-officer.bcpc-case-monitoring.edit') }}"
                   data-back
                   class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </a>
                @endunless
                @if(!$isView)
                    <button type="submit" 
                            class="inline-flex items-center gap-2 bg-purple-700 hover:bg-purple-800 text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-sm transition">
                        <i class="fas fa-save"></i>
                        <span>Save BCPC Case Monitoring</span>
                    </button>
                    <button id="save-and-send-to-admin" type="submit" name="send_to_admin" value="1"
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium shadow-sm transition">
                        <i class="fas fa-paper-plane"></i>
                        <span>Save & Send to Admin</span>
                    </button>
                @endif
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script src="{{ asset('js/bcpc-monitoring-mobile-cards.js') }}?v={{ filemtime(public_path('js/bcpc-monitoring-mobile-cards.js')) }}" defer></script>
@include('components.bcpc-monitoring-input-restrictions')
<script>
@if(session('bcpc_sent_to_admin'))
document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({
        icon: 'success',
        title: 'Successfully Sent to Admin',
        text: @json(session('success')),
        confirmButtonColor: '#059669',
    });
});
@endif

document.addEventListener('DOMContentLoaded', () => {
    const monitoringForm = document.getElementById('bcpc-monitoring-form');
    const saveAndSendButton = document.getElementById('save-and-send-to-admin');

    if (!monitoringForm || !saveAndSendButton) return;

    saveAndSendButton.addEventListener('click', async (event) => {
        event.preventDefault();
        if (!monitoringForm.reportValidity()) return;

        saveAndSendButton.disabled = true;
        Swal.fire({
            title: 'Saving changes...',
            text: 'Please wait while the BCPC monitoring report is saved.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => Swal.showLoading(),
        });

        try {
            const formData = new FormData(monitoringForm);
            formData.delete('send_to_admin');

            const saveResponse = await fetch(monitoringForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });
            const saveData = await saveResponse.json().catch(() => ({}));

            if (!saveResponse.ok || !saveData.success || !saveData.monitoring_id) {
                const validationErrors = Object.values(saveData.errors || {}).flat().join(' ');
                throw new Error(validationErrors || saveData.message || 'Unable to save the BCPC monitoring report.');
            }

            const confirmation = await Swal.fire({
            title: 'Send to Admin again?',
            text: 'The saved BCPC monitoring report will be sent to the Administrator.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Send to Admin',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            });

            if (!confirmation.isConfirmed) {
                saveAndSendButton.disabled = false;
                return;
            }

            Swal.fire({
                title: 'Sending to Admin...',
                text: 'Please wait while the report is being sent to the Administrator.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading(),
            });

            const sendResponse = await fetch(`/desk-officer/bcpc-case-monitoring/${saveData.monitoring_id}/send-to-admin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': monitoringForm.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const sendData = await sendResponse.json().catch(() => ({}));

            if (!sendResponse.ok || !sendData.success) {
                throw new Error(sendData.message || 'Unable to send the report to Admin.');
            }

            if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
            await Swal.fire({
                icon: 'success',
                title: 'Successfully Sent to Admin',
                text: sendData.message || 'The report has been sent to Admin.',
                confirmButtonColor: '#059669',
            });
            window.location.href = @json(route('desk-officer.bcpc-case-monitoring.edit'));
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Unable to Complete Request',
                text: error.message || 'An unexpected error occurred.',
            });
            saveAndSendButton.disabled = false;
        }
    });
});

function sendToAdmin(id, period) {
    Swal.fire({
        title: 'Send to Admin?',
        html: `
            <div class="text-center py-2">
                <i class="fas fa-paper-plane text-5xl text-emerald-500 mb-4"></i>
                <p class="text-gray-700 font-medium">Are you sure you want to send the BCPC case monitoring for <strong>{{ addslashes($barangay->name) }}</strong> (${period}) to the Administrator?</p>
                <p class="text-gray-400 text-xs mt-2">This will update the quarterly compliance report and notify the Admin office.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Yes, Send to Admin',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sending to Admin...',
                text: 'Please wait while we submit the report to Admin.',
                allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/desk-officer/bcpc-case-monitoring/${id}/send-to-admin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
                    Swal.fire({
                        icon: 'success',
                        title: 'Sent Successfully!',
                        text: data.message || 'Report has been sent to Admin.',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
                        window.location.href = "{{ route('desk-officer.bcpc-case-monitoring.edit') }}";
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to send report to Admin.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'An error occurred while sending report to Admin.', 'error'));
        }
    });
}

document.querySelectorAll('.bcpc-monitoring-table').forEach((table) => {
    const isReadOnly = {{ $isView ? 'true' : 'false' }};
    if (isReadOnly) return;

    const updateTotals = () => {
        const rows = [...table.tBodies[0].rows];
        rows.forEach((row) => {
            const cells = row.cells;
            const isTotalRow = cells[0].textContent.trim().toUpperCase() === 'TOTAL';

            for (let start = 1; start < cells.length; start += 3) {
                const maleInput = cells[start]?.querySelector('input');
                const femaleInput = cells[start + 1]?.querySelector('input');
                const totalInput = cells[start + 2]?.querySelector('input');

                if (!maleInput || !femaleInput || !totalInput) continue;

                if (isTotalRow) {
                    const sums = rows.filter((other) => other !== row).reduce((result, other) => {
                        const m = other.cells[start]?.querySelector('input');
                        const f = other.cells[start + 1]?.querySelector('input');
                        result.male += Number(m?.value || 0);
                        result.female += Number(f?.value || 0);
                        return result;
                    }, { male: 0, female: 0 });

                    maleInput.value = sums.male;
                    femaleInput.value = sums.female;
                    totalInput.value = sums.male + sums.female;
                    maleInput.readOnly = femaleInput.readOnly = totalInput.readOnly = true;
                } else {
                    const maleVal = Number(maleInput.value || 0);
                    const femaleVal = Number(femaleInput.value || 0);
                    totalInput.value = maleVal + femaleVal;
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
</script>
@endpush
@endsection
