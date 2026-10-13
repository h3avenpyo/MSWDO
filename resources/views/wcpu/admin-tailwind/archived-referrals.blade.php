@extends('layouts.admin-tailwind')
@section('title', 'Archived Referrals - iSERVE SILANG Admin')
@push('styles')
<style>
    .archived-static-table { width: 100%; table-layout: fixed; font-size: .8125rem; }
    .archived-static-table :is(th, td) { padding: .625rem .75rem !important; line-height: 1.4; }
    .archived-static-table th:last-child, .archived-static-table td:last-child { width: 10rem; white-space: nowrap; }
    #viewArchivedRecipientModal .archived-recipient-modal-card { max-height: calc(100dvh - 2rem); }
    #viewArchivedRecipientModal #viewArchivedRecipientContent { min-height: 0; overflow-y: auto; overscroll-behavior: contain; }
    .swal2-popup.admin-archived-referral-popup { box-sizing: border-box; width: min(48rem, calc(100vw - 2rem)) !important; max-height: min(48rem, calc(100dvh - 2rem)); grid-template-rows: auto minmax(0, 1fr) auto; overflow: hidden; padding: 0; }
    .swal2-popup.admin-archived-referral-popup .swal2-title { grid-column: 1; grid-row: 1; display: block; min-width: 0; max-width: 100%; margin: 0; padding: 1rem 3.5rem .625rem; color: #2d3748; font-size: 1.5rem; font-weight: 600; line-height: 1.25; text-align: center; position: relative; z-index: 1; }
    .swal2-popup.admin-archived-referral-popup .swal2-html-container { box-sizing: border-box; width: 100%; min-height: 0; max-height: calc(100dvh - 9rem); margin: 0; padding: 0 .875rem .875rem; overflow-y: auto; overscroll-behavior: contain; text-align: left; }
    .swal2-popup.admin-archived-referral-popup .swal2-close { position: absolute; top: .5rem; right: .5rem; z-index: 2; color: #4b5563; }
    @media (max-width: 47.9375rem) {
        .swal2-popup.admin-archived-referral-popup { width: calc(100vw - 1rem) !important; max-height: calc(100dvh - 1rem); }
        .swal2-popup.admin-archived-referral-popup .swal2-html-container { max-height: calc(100dvh - 8rem); padding: 0 .75rem .75rem; }
    }
    @if(auth()->user()->isDepartmentAdmin())
    .archived-static-table .archive-action[data-action="restore"] { background-color: #C41E3A !important; color: #ffffff !important; }
    .archived-static-table .archive-action[data-action="restore"]:hover { background-color: #A01830 !important; color: #ffffff !important; }
    .archived-static-table .archive-action[data-action="restore"] i { color: #ffffff !important; }
    @endif
    #archived-referrals-controls { position: relative; gap: .75rem; }
    @media (min-width: 37.5rem) and (max-width: 80rem) {
        #archived-referrals-controls { width: 100%; }
        #archived-referrals-search { width: 100%; }
        #archived-referrals-filter-panel { right: 0; left: auto; width: min(100%, 18rem); }
    }
    @media (max-width: 47.9375rem) {
        #archived-referrals-controls { gap: .75rem; }
        #archived-referrals-controls > .relative:last-child { align-self: flex-start; }
        #archived-referrals-filter-panel { left: 0; right: auto; width: min(18rem, calc(100vw - 2rem)); }
        .archived-referrals-table-view { display: none !important; }
        .archived-referrals-mobile-cards { display: block !important; }
        .archived-referrals-mobile-cards .archived-referral-card { min-width: 0; padding: 1rem; }
        .archived-referrals-mobile-cards .archived-referral-card-actions {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        .archived-referrals-mobile-cards .archived-referral-card-actions > button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            min-width: 0;
            min-height: 3rem;
            padding: .5rem !important;
        }
        .archived-referrals-mobile-cards .archived-referral-card-actions .archive-action,
        .archived-referrals-mobile-cards .archived-referral-card-actions .archive-action:hover {
            background-color: #C41E3A !important;
            color: #ffffff !important;
        }
        .archived-referrals-mobile-cards .archived-referral-card-actions .archive-action i { color: #ffffff !important; }
        .archived-referrals-mobile-cards .archived-referral-card-actions .archive-action[data-action="restore"] {
            font-size: 0 !important;
        }
        .archived-referrals-mobile-cards .archived-referral-card-actions .archive-action[data-action="restore"] i {
            font-size: 1rem !important;
        }
    }
</style>
@endpush
@section('content')
@if(auth()->user()->isDepartmentAdmin())
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Archived Referrals</h2>
            <p class="text-sm text-gray-500 hidden sm:block">View and Manage archived referrals.</p>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
@endif
<div class="space-y-6 px-4 sm:px-6 lg:px-8 {{ auth()->user()->isDepartmentAdmin() ? 'pt-6' : '' }}">
    @unless(auth()->user()->isDepartmentAdmin())
    <div>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Archived Referrals</h2>
        <p class="text-sm text-gray-500 mt-1">View and Manage archived referrals.</p>
    </div>
    @endunless
    <div id="archived-referrals-controls" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-start">
        <div class="relative w-full sm:max-w-md">
            <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
            <input id="archived-referrals-search" type="search" placeholder="Search archived referrals..." class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500">
        </div>
        <div class="relative self-end sm:self-auto">
            <button id="archived-referrals-filter-button" type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50" aria-expanded="false" aria-controls="archived-referrals-filter-panel">
                <i class="fas fa-filter text-purple-600" aria-hidden="true"></i> Filter
            </button>
            <div id="archived-referrals-filter-panel" class="absolute right-0 z-20 mt-2 hidden rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                <label for="archived-referrals-type" class="mb-1 block text-xs font-medium text-gray-600">Show</label>
                <select id="archived-referrals-type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500">
                    <option value="all">All archived records</option>
                    <option value="referral">Referrals only</option>
                    <option value="recipient">Referral info only</option>
                </select>
            </div>
        </div>
    </div>
    <p id="archived-referrals-result-count" class="text-sm text-gray-500" aria-live="polite"></p>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Referrals</h3>
        <div class="archived-referrals-table-view">
        <table class="archived-static-table w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3">Referral Number</th><th class="p-3">Client</th><th class="p-3">Recipient</th><th class="p-3">Date of Referral</th><th class="p-3">Archived At</th><th class="p-3 text-center">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($referrals as $referral)
@if(auth()->user()->isVawcAdmin())
                <tr data-archived-referral-row data-archive-table-row data-archive-record="referral"><td class="p-3">{{ $referral->referral_number }}</td><td class="p-3">{{ $referral->client_name }}</td><td class="p-3">{{ $referral->recipient_name }}<br>{{ $referral->recipient_email }}</td><td class="p-3">{{ $referral->date_of_referral?->format('M d, Y') }}</td><td class="p-3">{{ $referral->deleted_at?->format('M d, Y') }}</td><td class="p-3 text-center"><div class="flex justify-center gap-2">@if(auth()->user()->isVawcAdmin())<button type="button" data-swd-archived-referral="swd-archived-referral-{{ $referral->id }}" title="View Referral" aria-label="View Referral" class="px-3 py-2 text-white bg-[#C41E3A] rounded-lg hover:bg-[#A01830]"><i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View</span></button>@endif<button type="button" class="archive-action px-3 py-2 text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100" data-action="restore" data-url="{{ route('admin.referrals.archived.restore', ['type' => 'referral', 'id' => $referral->id]) }}">Restore</button></div></td></tr>
@else
                <tr data-archived-referral-row data-archive-table-row data-archive-record="referral"><td class="p-3">{{ $referral->referral_number }}</td><td class="p-3">{{ $referral->client_name }}</td><td class="p-3">{{ $referral->recipient_name }}<br>{{ $referral->recipient_email }}</td><td class="p-3">{{ $referral->date_of_referral?->format('M d, Y') }}</td><td class="p-3">{{ $referral->deleted_at?->format('M d, Y') }}</td><td class="p-3 text-center"><div class="flex justify-center gap-2"><button type="button" class="view-archived-referral inline-flex items-center gap-1.5 rounded-lg bg-purple-50 px-3 py-2 text-purple-700 hover:bg-purple-100" aria-label="View referral details" data-referral-number="{{ $referral->referral_number }}" data-client-name="{{ $referral->client_name }}" data-client-age="{{ $referral->client_age }}" data-client-sex="{{ $referral->client_sex }}" data-date="{{ $referral->date_of_referral?->format('M d, Y') }}" data-services="{{ $referral->specific_services_requested }}" data-recipient-name="{{ $referral->recipient_name }}" data-recipient-email="{{ $referral->recipient_email }}" data-feedback="{{ $referral->feedback }}" data-feedback-date="{{ $referral->feedback_received_at?->format('M d, Y') }}" data-notes="{{ $referral->additional_notes }}"><i class="fas fa-eye" aria-hidden="true"></i>View</button><button type="button" class="archive-action px-3 py-2 text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100" data-action="restore" data-url="{{ route('admin.referrals.archived.restore', ['type' => 'referral', 'id' => $referral->id]) }}">Restore</button></div></td></tr>
@endif
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-500">No archived referrals.</td></tr>
                @endforelse
            </tbody>
        </table>
        @include('components.archived-pagination', ['paginator' => $referrals, 'label' => 'referrals'])
        </div>
        <div class="archived-referrals-mobile-cards hidden space-y-3">
            @forelse($referrals as $referral)
            <article class="archived-referral-card bg-white rounded-xl border border-gray-200 shadow-sm" data-archived-referral-row data-archive-card data-archive-record="referral">
                <div class="flex items-start justify-between gap-3 mb-4"><div class="min-w-0"><p class="text-xs text-gray-500 mb-1">Referral Number</p><p class="font-semibold text-gray-800 break-words">{{ $referral->referral_number }}</p></div><p class="text-xs text-gray-500 text-right">Archived<br>{{ $referral->deleted_at?->format('M d, Y') }}</p></div>
                <div class="grid grid-cols-2 gap-3 text-sm mb-4"><div class="min-w-0"><p class="text-xs text-gray-500 mb-1">Client</p><p class="font-medium text-gray-800 break-words">{{ $referral->client_name }}</p></div><div class="min-w-0"><p class="text-xs text-gray-500 mb-1">Recipient</p><p class="font-medium text-gray-800 break-words">{{ $referral->recipient_name }}</p><p class="text-xs text-gray-500 break-all">{{ $referral->recipient_email }}</p></div><div class="col-span-2"><p class="text-xs text-gray-500 mb-1">Date of Referral</p><p class="text-gray-700">{{ $referral->date_of_referral?->format('M d, Y') }}</p></div></div>
@if(auth()->user()->isVawcAdmin())
                <div class="archived-referral-card-actions pt-3 border-t border-gray-100">@if(auth()->user()->isVawcAdmin())<button type="button" data-swd-archived-referral="swd-archived-referral-{{ $referral->id }}" title="View Referral" aria-label="View Referral" class="px-3 py-2 text-white bg-[#C41E3A] rounded-lg hover:bg-[#A01830]"><i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View</span></button>@endif<button type="button" class="archive-action rounded-lg text-sm font-medium" data-action="restore" data-url="{{ route('admin.referrals.archived.restore', ['type' => 'referral', 'id' => $referral->id]) }}" title="Restore" aria-label="Restore"><i class="fas fa-undo" aria-hidden="true"></i></button></div>
@else
                <div class="archived-referral-card-actions pt-3 border-t border-gray-100"><button type="button" class="view-archived-referral inline-flex items-center justify-center gap-2 rounded-lg bg-purple-50 px-3 py-2 text-sm font-medium text-purple-700 hover:bg-purple-100" title="View" aria-label="View referral details" data-referral-number="{{ $referral->referral_number }}" data-client-name="{{ $referral->client_name }}" data-client-age="{{ $referral->client_age }}" data-client-sex="{{ $referral->client_sex }}" data-date="{{ $referral->date_of_referral?->format('M d, Y') }}" data-services="{{ $referral->specific_services_requested }}" data-recipient-name="{{ $referral->recipient_name }}" data-recipient-email="{{ $referral->recipient_email }}" data-feedback="{{ $referral->feedback }}" data-feedback-date="{{ $referral->feedback_received_at?->format('M d, Y') }}" data-notes="{{ $referral->additional_notes }}"><i class="fas fa-eye" aria-hidden="true"></i>View</button><button type="button" class="archive-action rounded-lg text-sm font-medium" data-action="restore" data-url="{{ route('admin.referrals.archived.restore', ['type' => 'referral', 'id' => $referral->id]) }}" title="Restore" aria-label="Restore"><i class="fas fa-undo" aria-hidden="true"></i></button></div>
@endif
            </article>
            @empty
            <div class="rounded-xl border border-gray-200 p-6 text-center text-gray-500">No archived referrals.</div>
            @endforelse
            @include('components.archived-pagination', ['paginator' => $referrals, 'label' => 'referrals'])
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Referral Info</h3>
        <div class="archived-referrals-table-view">
        <table class="archived-static-table w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Contact Number</th><th class="p-3">Role</th><th class="p-3">Archived At</th><th class="p-3 text-center">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recipients as $recipient)
@if(auth()->user()->isVawcAdmin())
                <tr data-archive-table-row data-archive-record="recipient"><td class="p-3">{{ $recipient->name }}</td><td class="p-3">{{ $recipient->email }}</td><td class="p-3">{{ $recipient->contact_number ?: 'N/A' }}</td><td class="p-3">{{ $recipient->role ?: 'N/A' }}</td><td class="p-3">{{ $recipient->deleted_at?->format('M d, Y') }}</td><td class="p-3 text-center"><div class="flex justify-center gap-2">@if(auth()->user()->isVawcAdmin())<button type="button" data-swd-archived-recipient="swd-archived-recipient-{{ $recipient->id }}" title="View Referral Info" aria-label="View Referral Info" class="px-3 py-2 text-white bg-[#C41E3A] rounded-lg hover:bg-[#A01830]"><i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View</span></button>@endif<button type="button" class="archive-action px-3 py-2 text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100" data-action="restore" data-url="{{ route('admin.referrals.archived.restore', ['type' => 'info', 'id' => $recipient->id]) }}">Restore</button></div></td></tr>
@else
                <tr data-archive-table-row data-archive-record="recipient"><td class="p-3">{{ $recipient->name }}</td><td class="p-3">{{ $recipient->email }}</td><td class="p-3">{{ $recipient->contact_number ?: 'N/A' }}</td><td class="p-3">{{ $recipient->role ?: 'N/A' }}</td><td class="p-3">{{ $recipient->deleted_at?->format('M d, Y') }}</td><td class="p-3 text-center"><div class="flex justify-center gap-2"><button type="button" class="view-archived-recipient p-2 text-gray-500 transition-colors hover:bg-blue-50 hover:text-blue-600" title="View Details" aria-label="View recipient details" data-name="{{ $recipient->name }}" data-email="{{ $recipient->email }}" data-contact="{{ $recipient->contact_number }}" data-role="{{ $recipient->role }}" data-active="{{ $recipient->active ? '1' : '0' }}" data-created="{{ $recipient->created_at?->format('M d, Y') }}"><i class="fas fa-eye" aria-hidden="true"></i></button><button type="button" class="archive-action px-3 py-2 text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100" data-action="restore" data-url="{{ route('admin.referrals.archived.restore', ['type' => 'info', 'id' => $recipient->id]) }}">Restore</button></div></td></tr>
@endif
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-500">No archived referral info.</td></tr>
                @endforelse
            </tbody>
        </table>
        @include('components.archived-pagination', ['paginator' => $recipients, 'label' => 'recipients'])
        </div>
        <div class="archived-referrals-mobile-cards hidden space-y-3">
            @forelse($recipients as $recipient)
            <article class="archived-referral-card bg-white rounded-xl border border-gray-200 shadow-sm" data-archive-card data-archive-record="recipient">
                <div class="flex items-start justify-between gap-3 mb-4"><div class="min-w-0"><p class="text-xs text-gray-500 mb-1">Name</p><p class="font-semibold text-gray-800 break-words">{{ $recipient->name }}</p></div><p class="text-xs text-gray-500 text-right">Archived<br>{{ $recipient->deleted_at?->format('M d, Y') }}</p></div>
                <div class="grid grid-cols-2 gap-3 text-sm mb-4"><div class="col-span-2 min-w-0"><p class="text-xs text-gray-500 mb-1">Email</p><p class="text-gray-700 break-all">{{ $recipient->email }}</p></div><div><p class="text-xs text-gray-500 mb-1">Contact Number</p><p class="text-gray-700">{{ $recipient->contact_number ?: 'N/A' }}</p></div><div><p class="text-xs text-gray-500 mb-1">Role</p><p class="text-gray-700 break-words">{{ $recipient->role ?: 'N/A' }}</p></div></div>
@if(auth()->user()->isVawcAdmin())
                <div class="archived-referral-card-actions pt-3 border-t border-gray-100">@if(auth()->user()->isVawcAdmin())<button type="button" data-swd-archived-recipient="swd-archived-recipient-{{ $recipient->id }}" title="View Referral Info" aria-label="View Referral Info" class="px-3 py-2 text-white bg-[#C41E3A] rounded-lg hover:bg-[#A01830]"><i class="fas fa-eye" aria-hidden="true"></i><span class="sr-only">View</span></button>@endif<button type="button" class="archive-action rounded-lg text-sm font-medium" data-action="restore" data-url="{{ route('admin.referrals.archived.restore', ['type' => 'info', 'id' => $recipient->id]) }}" title="Restore" aria-label="Restore"><i class="fas fa-undo" aria-hidden="true"></i></button></div>
@else
                <div class="archived-referral-card-actions pt-3 border-t border-gray-100"><button type="button" class="view-archived-recipient inline-flex items-center justify-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100" title="View Details" aria-label="View recipient details" data-name="{{ $recipient->name }}" data-email="{{ $recipient->email }}" data-contact="{{ $recipient->contact_number }}" data-role="{{ $recipient->role }}" data-active="{{ $recipient->active ? '1' : '0' }}" data-created="{{ $recipient->created_at?->format('M d, Y') }}"><i class="fas fa-eye" aria-hidden="true"></i>View</button><button type="button" class="archive-action rounded-lg text-sm font-medium" data-action="restore" data-url="{{ route('admin.referrals.archived.restore', ['type' => 'info', 'id' => $recipient->id]) }}" title="Restore" aria-label="Restore"><i class="fas fa-undo" aria-hidden="true"></i></button></div>
@endif
            </article>
            @empty
            <div class="rounded-xl border border-gray-200 p-6 text-center text-gray-500">No archived referral info.</div>
            @endforelse
            @include('components.archived-pagination', ['paginator' => $recipients, 'label' => 'recipients'])
        </div>
    </div>
</div>
<div id="viewArchivedRecipientModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="viewArchivedRecipientTitle">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" data-close-archived-recipient></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="archived-recipient-modal-card relative flex w-full max-w-md flex-col overflow-hidden rounded-xl bg-white shadow-xl">
            <div class="shrink-0 flex items-center justify-between bg-[#C41E3A] px-6 py-4">
                <h3 id="viewArchivedRecipientTitle" class="flex items-center gap-2 text-lg font-semibold text-white"><i class="fas fa-eye"></i><span>Recipient Details</span></h3>
                <button type="button" class="text-white/80 transition-colors hover:text-white" aria-label="Close" data-close-archived-recipient><i class="fas fa-times text-xl"></i></button>
            </div>
            <div id="viewArchivedRecipientContent" class="min-h-0 flex-1 space-y-4 overflow-y-auto p-6"></div>
            <div class="shrink-0 flex justify-end border-t border-gray-100 bg-gray-50 px-6 py-3"><button type="button" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300" data-close-archived-recipient>Close</button></div>
        </div>
    </div>
</div>
@if(auth()->user()->isVawcAdmin())
@foreach($referrals as $referral)
<template id="swd-archived-referral-{{ $referral->id }}">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        @foreach(['referral_number', 'date_of_referral', 'status', 'client_name', 'client_age', 'client_sex', 'client_address', 'client_contact', 'family_guardian_name', 'family_guardian_contact', 'family_guardian_address', 'recipient_name', 'recipient_address', 'recipient_contact', 'recipient_email', 'referred_by_name', 'referred_by_designation', 'referred_by_address', 'referred_by_contact', 'referred_by_email', 'reason_for_referral', 'specific_services_requested', 'additional_notes', 'feedback', 'feedback_received_at', 'deleted_at'] as $field)
        <div class="rounded-lg border border-gray-200 p-3">
            <dt class="text-xs text-gray-500 mb-1">{{ $field === 'deleted_at' ? 'Archived At' : ucwords(str_replace('_', ' ', $field)) }}</dt>
            <dd class="text-gray-800 whitespace-pre-wrap break-words">{{ $referral->$field instanceof \DateTimeInterface ? $referral->$field->format('M d, Y h:i A') : ($referral->$field ?? '—') }}</dd>
        </div>
        @endforeach
    </dl>
</template>
@endforeach
@foreach($recipients as $recipient)
<template id="swd-archived-recipient-{{ $recipient->id }}">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        @foreach(['name', 'email', 'contact_number', 'role', 'deleted_at'] as $field)
        <div class="rounded-lg border border-gray-200 p-3">
            <dt class="text-xs text-gray-500 mb-1">{{ $field === 'deleted_at' ? 'Archived At' : ucwords(str_replace('_', ' ', $field)) }}</dt>
            <dd class="text-gray-800 whitespace-pre-wrap break-words">{{ $recipient->$field instanceof \DateTimeInterface ? $recipient->$field->format('M d, Y h:i A') : ($recipient->$field ?: 'N/A') }}</dd>
        </div>
        @endforeach
    </dl>
</template>
@endforeach
@endif
@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('archived-referrals-search');
    const filterButton = document.getElementById('archived-referrals-filter-button');
    const filterPanel = document.getElementById('archived-referrals-filter-panel');
    const type = document.getElementById('archived-referrals-type');
    const count = document.getElementById('archived-referrals-result-count');

    const applyArchivedReferralFilters = () => {
        const query = (search?.value || '').trim().toLowerCase();
        const selectedType = type?.value || 'all';
        let visible = 0;

        const records = window.matchMedia('(max-width: 47.9375rem)').matches
            ? document.querySelectorAll('[data-archive-card]')
            : document.querySelectorAll('[data-archive-table-row]');
        records.forEach((row) => {
            const matchesText = !query || row.textContent.toLowerCase().includes(query);
            const matchesType = selectedType === 'all' || row.dataset.archiveRecord === selectedType;
            const show = matchesText && matchesType;
            row.classList.toggle('hidden', !show);
            if (show) visible += 1;
        });

        if (count) count.textContent = query || selectedType !== 'all'
            ? `${visible} matching archived ${visible === 1 ? 'record' : 'records'}`
            : '';
    };

    search?.addEventListener('input', applyArchivedReferralFilters);
    type?.addEventListener('change', applyArchivedReferralFilters);
    filterButton?.addEventListener('click', () => {
        const open = filterPanel.classList.toggle('hidden');
        filterButton.setAttribute('aria-expanded', (!open).toString());
    });
    document.addEventListener('click', (event) => {
        if (!filterPanel || !filterButton || filterPanel.classList.contains('hidden')) return;
        if (!filterPanel.contains(event.target) && !filterButton.contains(event.target)) {
            filterPanel.classList.add('hidden');
            filterButton.setAttribute('aria-expanded', 'false');
        }
    });
});

document.addEventListener('click', async function (event) {
    const recipientViewButton = event.target.closest('.view-archived-recipient');
    if (recipientViewButton) {
        const fields = [
            ['Full Name', recipientViewButton.dataset.name],
            ['Email Address', recipientViewButton.dataset.email],
            ['Contact Number', recipientViewButton.dataset.contact || 'None'],
            ['Role / Designation', recipientViewButton.dataset.role || 'N/A'],
            ['Status', recipientViewButton.dataset.active === '1' ? 'Active' : 'Inactive'],
            ['Date Added', recipientViewButton.dataset.created || 'N/A']
        ];
        const content = document.getElementById('viewArchivedRecipientContent');
        content.replaceChildren();
        const card = document.createElement('div');
        card.className = 'space-y-3 rounded-xl border border-gray-100 bg-gray-50 p-4';
        fields.forEach(([label, value]) => {
            const field = document.createElement('div');
            const fieldLabel = document.createElement('span');
            fieldLabel.className = 'block text-xs font-semibold uppercase text-gray-500';
            fieldLabel.textContent = label;
            const fieldValue = document.createElement('span');
            fieldValue.className = 'block break-words text-sm text-gray-800';
            fieldValue.textContent = value;
            field.append(fieldLabel, fieldValue);
            card.appendChild(field);
        });
        content.appendChild(card);
        document.getElementById('viewArchivedRecipientModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        return;
    }

    if (event.target.closest('[data-close-archived-recipient]')) {
        document.getElementById('viewArchivedRecipientModal').classList.add('hidden');
        document.body.style.overflow = '';
        return;
    }

    const viewButton = event.target.closest('.view-archived-referral');
    if (viewButton) {
        const details = document.createElement('div');
        details.className = 'space-y-5 text-left';
        const addSection = (title, fields) => {
            const section = document.createElement('section');
            section.className = 'overflow-hidden rounded-xl border border-gray-200 bg-white';
            const heading = document.createElement('h3');
            heading.className = 'border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800';
            heading.textContent = title;
            const list = document.createElement('dl');
            list.className = 'grid grid-cols-1 gap-3 p-4 sm:grid-cols-2';
            fields.forEach(([label, value, wide = false]) => {
                const item = document.createElement('div');
                item.className = `min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3${wide ? ' sm:col-span-2' : ''}`;
                const fieldLabel = document.createElement('dt');
                fieldLabel.className = 'text-xs font-medium uppercase tracking-wide text-gray-500';
                fieldLabel.textContent = label;
                const fieldValue = document.createElement('dd');
                fieldValue.className = 'mt-1 whitespace-pre-wrap break-words text-sm text-gray-900';
                fieldValue.textContent = value?.trim() || '—';
                item.append(fieldLabel, fieldValue);
                list.appendChild(item);
            });
            section.append(heading, list);
            details.appendChild(section);
        };
        addSection('Client Information', [
            ['Client', viewButton.dataset.clientName, true],
            ['Age', viewButton.dataset.clientAge],
            ['Sex', viewButton.dataset.clientSex]
        ]);
        addSection('Referral Information', [
            ['Referral Number', viewButton.dataset.referralNumber],
            ['Date of Referral', viewButton.dataset.date],
            ['Services Requested', viewButton.dataset.services, true],
            ['Additional Notes', viewButton.dataset.notes, true]
        ]);
        addSection('Receiving Agency and Response', [
            ['Recipient', viewButton.dataset.recipientName],
            ['Recipient Email', viewButton.dataset.recipientEmail],
            ['Feedback Received', viewButton.dataset.feedbackDate],
            ['Feedback', viewButton.dataset.feedback, true]
        ]);
        Swal.fire({
            title: 'Referral Details',
            html: details,
            width: 'min(56rem, calc(100vw - 2rem))',
            customClass: { popup: 'archive-view-modal-popup admin-archived-referral-popup' },
            showCloseButton: true,
            confirmButtonText: 'Close',
            confirmButtonColor: '#7e22ce'
        });
        return;
    }

    const button = event.target.closest('.archive-action');
    if (!button || button.disabled) return;
    const deleting = button.dataset.action === 'delete';
    const result = await Swal.fire({
        title: deleting ? 'Delete Archived Referral?' : 'Restore Archived Referral?',
        text: deleting ? 'This permanently deletes this record. This cannot be undone.' : 'Only this record will be restored to the active table.',
        icon: 'warning', showCancelButton: true,
        confirmButtonText: deleting ? 'Delete' : 'Restore',
        confirmButtonColor: deleting ? '#ef4444' : '#9333ea'
    });
    if (!result.isConfirmed) return;
    button.disabled = true;
    try {
        if (@json(auth()->user()->isBcpcAdmin())) {
            Swal.fire({ title: 'Processing...', text: `${deleting ? 'Deleting' : 'Restoring'} referral. Please wait.`, allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        }
        const response = await fetch(button.dataset.url, {
            method: deleting ? 'DELETE' : 'POST',
            headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json'}
        });
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || 'Unable to update archived referral');
        await Swal.fire({
            icon: 'success',
            title: deleting ? 'Deleted!' : 'Restored!',
            text: data.message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#9333ea',
            showConfirmButton: true
        });
        window.location.reload();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#9333ea',
            showConfirmButton: true
        });
        button.disabled = false;
    }
});
</script>
@endpush
