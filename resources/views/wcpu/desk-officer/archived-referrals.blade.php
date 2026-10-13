@extends('layouts.desk-officer')
@section('title', 'Archived Referrals')
@push('styles')
<style>
@media (max-width:47.9375rem){.archived-referrals-table{display:none!important}.archived-referrals-cards{display:grid!important;gap:.75rem;padding:.75rem}.archived-referral-card{padding:1rem;min-width:0}.archived-referral-actions{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.5rem}.archived-referral-actions>:is(button,form){display:inline-flex;width:100%;height:3rem;min-width:3rem;min-height:3rem}.archived-referral-actions :is(button,a){display:inline-flex;align-items:center;justify-content:center;width:100%;height:100%;padding:0}}
@media (min-width:37.5rem){.archived-referrals-cards{display:none!important}}
.swal2-popup.archived-referral-view-popup{box-sizing:border-box;width:min(48rem,calc(100vw - 2rem))!important;max-height:calc(100dvh - 2rem);overflow:hidden;padding:0}
.swal2-popup.archived-referral-view-popup .swal2-title{padding:1rem 3.5rem .625rem}
.swal2-popup.archived-referral-view-popup .swal2-html-container{box-sizing:border-box;width:100%;max-height:calc(100dvh - 5.5rem);margin:0;padding:0 .875rem .875rem;overflow-y:auto;overscroll-behavior:contain;text-align:left}
.swal2-popup.archived-referral-view-popup .swal2-close{color:#4b5563}
@media(max-width:47.9375rem){.swal2-popup.archived-referral-view-popup{width:calc(100vw - 1rem)!important;max-height:calc(100dvh - 1rem)}.swal2-popup.archived-referral-view-popup .swal2-html-container{max-height:calc(100dvh - 5rem);padding:0 .75rem .75rem}}
@if(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->isMultiCaseDepartment() || in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true)))
@media (max-width:47.9375rem) and (orientation:portrait){
    .archived-referral-filter-actions{width:100%;flex-wrap:wrap}
    .archived-referral-filter-button{display:inline-flex;width:100%;height:2.75rem;min-height:2.75rem;align-items:center;justify-content:center;padding-right:.75rem;padding-left:.75rem}
}
@endif
</style>
@endpush
@section('content')
@php($isBcpcDeskOfficer = auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' && !auth()->user()?->isMultiCaseDepartment())
@php($isVawcDeskOfficer = auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'VAWC' && !auth()->user()?->isMultiCaseDepartment())
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Archived Referrals', 'subtitle' => 'View archived case referrals'])
<div class="p-4 sm:p-6 lg:p-8">
    @unless($isBcpcDeskOfficer || $isVawcDeskOfficer || auth()->user()->isMultiCaseDepartment() || (auth()->user()?->position === 'Desk Officer' && auth()->user()?->isMultiCaseDepartment()))
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Archived Referrals</h1>
    </div>
    @endunless

    <!-- Search and Filter Bar -->
    <form method="GET" action="{{ route('desk-officer.referrals.archived') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" id="archived-referral-search" value="{{ request('search') }}" placeholder="Search archived referrals (Ref #, Client, Recipient)..." class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div class="archived-referral-filter-actions flex items-center gap-2">
                @if($isVawcDeskOfficer || $isBcpcDeskOfficer || auth()->user()->isMultiCaseDepartment())
                <select name="status" aria-label="Filter archived referrals by status" class="h-10 px-3 border border-gray-300 rounded-xl text-sm bg-white text-gray-700">
                    <option value="">All Status</option>
                    @foreach(['Pending', 'Accepted', 'Completed'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected(request('status') === $statusOption)>{{ $statusOption }}</option>
                    @endforeach
                </select>
                @endif
                <button type="submit" class="archived-referral-filter-button inline-flex h-10 items-center justify-center gap-2 px-4 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
                @if(request()->filled('search') || (($isVawcDeskOfficer || $isBcpcDeskOfficer || auth()->user()->isMultiCaseDepartment()) && request()->filled('status')))
                <a href="{{ route('desk-officer.referrals.archived') }}" class="inline-flex h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                    <i class="fas fa-times"></i>
                    <span>Clear</span>
                </a>
                @endif
            </div>
        </div>
    </form>
    @php($groups = isset($referralGroups) && !empty($referralGroups) ? $referralGroups : ['' => $referrals])
    @foreach($groups as $department => $referralList)
        <section class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            @if(auth()->user()->isMultiCaseDepartment())
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-[#991b1b]">{{ $department === 'VAWC' ? 'SWD' : $department }} Archived Referrals</h2>
            </div>
            @endif
            <div class="archived-referrals-table overflow-x-auto"><table class="w-full">
                <thead class="bg-gradient-to-r from-purple-700 to-purple-600 text-white"><tr><th class="px-6 py-4 text-left">Referral Number</th><th class="px-6 py-4 text-left">Client</th><th class="px-6 py-4 text-left">Recipient</th><th class="px-6 py-4 text-left">Date</th><th class="px-6 py-4 text-left">Archived At</th>@if($isBcpcDeskOfficer || $isVawcDeskOfficer || auth()->user()->isMultiCaseDepartment())<th class="px-6 py-4 text-center">Actions</th>@endif</tr></thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($referralList as $referral)
                    <tr data-archive-status="{{ $referral->status }}" class="hover:bg-purple-50/50"><td class="px-6 py-4">{{ $referral->referral_number }}</td><td class="px-6 py-4">{{ $referral->client_name }}</td><td class="px-6 py-4">{{ $referral->recipient_name }}</td><td class="px-6 py-4">{{ $referral->date_of_referral?->format('M d, Y') }}</td><td class="px-6 py-4">{{ $referral->deleted_at?->format('M d, Y') }}</td>@if($isBcpcDeskOfficer || $isVawcDeskOfficer || auth()->user()->isMultiCaseDepartment())<td class="px-6 py-4"><div class="flex items-center justify-center gap-2"><button type="button" data-icon-action="labelled" data-referral-id="{{ $referral->id }}" title="View Referral" class="archived-view-referral-btn inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#C41E3A] text-white hover:bg-[#A01830]"><i class="fas fa-eye"></i><span class="sr-only">View</span></button><form method="POST" class="restore-archived-referral-form" action="{{ route('desk-officer.referrals.restore', $referral->id) }}">@csrf<button type="submit" title="Restore Referral" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#C41E3A] text-white hover:bg-[#A01830]"><i class="fas fa-undo"></i><span class="sr-only">Restore</span></button></form>@unless($isVawcDeskOfficer || $isBcpcDeskOfficer || auth()->user()->isMultiCaseDepartment())<form method="POST" class="delete-archived-referral-form" action="{{ route('desk-officer.referrals.force-delete', $referral->id) }}">@csrf @method('DELETE')<button type="submit" title="Delete Referral" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-[#C41E3A] text-white hover:bg-[#A01830]"><i class="fas fa-trash"></i><span class="sr-only">Delete</span></button></form>@endunless</div></td>@endif</tr>
                @empty
                    <tr><td colspan="{{ ($isBcpcDeskOfficer || $isVawcDeskOfficer || auth()->user()->isMultiCaseDepartment()) ? 6 : 5 }}" class="px-6 py-12 text-center text-gray-500">No archived referrals.</td></tr>
                @endforelse
                </tbody>
            </table></div>
            <div class="archived-referrals-cards hidden">
                @forelse($referralList as $referral)
                <article data-archive-status="{{ $referral->status }}" class="archived-referral-card rounded-xl border border-gray-200 bg-white shadow-sm"><div class="flex items-start justify-between gap-3 border-b border-gray-100 pb-3"><div class="min-w-0"><p class="text-xs text-gray-500">Referral Number</p><p class="font-semibold text-gray-800 break-all">{{ $referral->referral_number }}</p></div><div class="shrink-0 text-right"><p class="text-xs text-gray-500">Archived</p><p class="text-sm font-medium text-gray-800">{{ $referral->deleted_at?->format('M d, Y') }}</p></div></div><dl class="grid grid-cols-2 gap-x-4 gap-y-3 py-3 text-sm"><div><dt class="text-xs text-gray-500">Client</dt><dd class="font-medium text-gray-800 break-words">{{ $referral->client_name }}</dd></div><div><dt class="text-xs text-gray-500">Date</dt><dd class="font-medium text-gray-800">{{ $referral->date_of_referral?->format('M d, Y') }}</dd></div><div class="col-span-2"><dt class="text-xs text-gray-500">Recipient</dt><dd class="font-medium text-gray-800 break-words">{{ $referral->recipient_name }}</dd></div></dl>@if($isBcpcDeskOfficer || $isVawcDeskOfficer || auth()->user()->isMultiCaseDepartment())<div class="archived-referral-actions border-t border-gray-100 pt-3"><button type="button" data-referral-id="{{ $referral->id }}" class="archived-view-referral-btn rounded-lg bg-[#C41E3A] text-white"><i class="fas fa-eye"></i><span class="sr-only">View</span></button><form method="POST" class="restore-archived-referral-form" action="{{ route('desk-officer.referrals.restore', $referral->id) }}">@csrf<button type="submit" class="rounded-lg bg-[#C41E3A] text-white"><i class="fas fa-undo"></i><span class="sr-only">Restore</span></button></form>@unless($isVawcDeskOfficer || $isBcpcDeskOfficer || auth()->user()->isMultiCaseDepartment())<form method="POST" class="delete-archived-referral-form" action="{{ route('desk-officer.referrals.force-delete', $referral->id) }}">@csrf @method('DELETE')<button type="submit" class="rounded-lg bg-[#C41E3A] text-white"><i class="fas fa-trash"></i><span class="sr-only">Delete</span></button></form>@endunless</div>@endif</article>
                @empty <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-gray-500">No archived referrals.</div>@endforelse
            </div>
            @if($referralList->hasPages())@include('components.archived-pagination', ['paginator' => $referralList, 'label' => 'archived referrals'])@endif
        </section>
    @endforeach

<div class="modal fade" id="archivedReferralDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 720px;">
        <div class="modal-content">
            <div class="modal-header bg-[#C41E3A] text-white" style="background-color: #C41E3A !important; color: white !important;">
                <h5 class="modal-title text-white flex items-center gap-2"><i class="fas fa-file-alt"></i> Referral Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="archivedReferralDetailsContent"></div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('archivedReferralDetailsModal');
    const modal = new bootstrap.Modal(modalElement);
    const content = document.getElementById('archivedReferralDetailsContent');
    document.querySelectorAll('.archived-view-referral-btn').forEach(button => button.addEventListener('click', () => {
        content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-secondary" role="status"></div><div>Loading...</div></div>';
        modal.show();
        fetch(`/desk-officer/referrals/${button.dataset.referralId}/details?archived=1`)
            .then(response => response.text())
            .then(html => { content.innerHTML = html; })
            .catch(() => { content.innerHTML = '<div class="alert alert-danger">Failed to load referral details.</div>'; });
    }));
});

document.querySelectorAll('.delete-archived-referral-form').forEach(form => {
    form.addEventListener('submit', event => {
        event.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Delete archived referral?',
            text: 'This referral will be permanently deleted.',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#C41E3A'
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});

document.querySelectorAll('.restore-archived-referral-form').forEach(form => {
    form.addEventListener('submit', event => {
        event.preventDefault();
        Swal.fire({
            icon: 'question',
            title: 'Restore archived referral?',
            text: 'This referral will be returned to the active referrals list.',
            showCancelButton: true,
            confirmButtonText: 'Restore',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#7c3aed'
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});
// Live search filter on archived referrals table rows
const refSearch = document.getElementById('archived-referral-search');
if (refSearch) {
    refSearch.addEventListener('input', () => {
        const q = refSearch.value.toLowerCase().trim();
        document.querySelectorAll('table tbody tr').forEach(row => {
            if (row.cells.length < 4) return;
            const text = row.textContent.toLowerCase();
            row.style.display = (!q || text.includes(q)) ? '' : 'none';
        });
    });
}
</script>
@endpush
