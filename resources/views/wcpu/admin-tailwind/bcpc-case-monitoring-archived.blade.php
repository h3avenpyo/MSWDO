@extends('layouts.admin-tailwind')

@section('title', 'Archived BCPC Case Monitoring - iSERVE SILANG Admin')

@push('styles')
<style>
    .archived-static-table { table-layout: fixed; }
    .archived-static-table th:last-child, .archived-static-table td:last-child { width: 15rem; white-space: nowrap; }
    @media (max-width: 47.9375rem) and (orientation: portrait) {
        .archived-bcpc-monitoring-table-view { display: none !important; }
        .archived-bcpc-monitoring-mobile-cards { display: grid !important; gap: .75rem; }
        .archived-bcpc-monitoring-mobile-cards .archived-bcpc-monitoring-card { min-width: 0; }
        .archived-bcpc-monitoring-mobile-cards .archived-bcpc-monitoring-card-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .5rem;
        }
        .archived-bcpc-monitoring-mobile-cards .archived-bcpc-monitoring-card-actions > :is(a, button) {
            width: 100% !important;
            min-width: 0;
            height: 2.75rem !important;
            min-height: 2.75rem !important;
            padding: .5rem !important;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Archived BCPC Case Monitoring</h2>
            <p class="text-sm text-gray-500 hidden sm:block">View archived BCPC monitoring records</p>
        </div>
        @include('admin-tailwind.components.header-account')
    </div>
</header>
<div class="p-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Info Banner -->
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-xl mb-6 shadow-sm">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-amber-600 mt-0.5"></i>
            <p class="text-sm text-amber-800">
                Archived BCPC case monitoring records are excluded from active monitoring tables. You can review submitted form details or restore records to active status anytime.
            </p>
        </div>
    </div>

    <!-- Filters -->
    <form id="archived-bcpc-report-filter-form" method="GET" action="{{ route('admin.bcpc-case-monitoring.archived') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:flex xl:items-center gap-2 sm:gap-3">
            <div class="relative min-w-0 sm:col-span-2 xl:flex-1 xl:min-w-[240px]"><i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i><input type="search" name="search" id="archived-bcpc-report-search" value="{{ request('search') }}" placeholder="Search archived reports..." class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></div>
            <select name="quarter" id="archived-bcpc-report-quarter" class="w-full xl:w-[130px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Quarters</option>@foreach([1, 2, 3, 4] as $quarterOption)<option value="{{ $quarterOption }}" {{ (string) $quarter === (string) $quarterOption ? 'selected' : '' }}>Q{{ $quarterOption }}</option>@endforeach</select>
            <select name="year" id="archived-bcpc-report-year" class="w-full xl:w-[115px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Years</option>@foreach(range(2026, 2030) as $yearOption)<option value="{{ $yearOption }}" {{ (string) $year === (string) $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>@endforeach</select>
            <select name="barangay_id" id="archived-bcpc-report-barangay" class="w-full xl:w-[160px] h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700"><option value="">All Barangays</option>@foreach($barangays as $barangay)<option value="{{ $barangay->id }}" {{ (string) $barangayId === (string) $barangay->id ? 'selected' : '' }}>{{ $barangay->name }}</option>@endforeach</select>
            <button type="submit" id="archived-bcpc-report-filter-button" class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0"><i class="fas fa-filter"></i><span>Filter</span></button>
            @if(request()->filled('search') || request()->filled('quarter') || request()->filled('year') || request()->filled('barangay_id'))<a href="{{ route('admin.bcpc-case-monitoring.archived') }}" class="inline-flex h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition shrink-0"><i class="fas fa-times"></i><span>Clear</span></a>@endif
        </div>
    </form>

    <!-- Table Container -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <!-- Desktop Table View -->
        <div class="archived-bcpc-monitoring-table-view overflow-x-auto hidden md:block">
            <table class="archived-static-table w-full text-sm">
                <thead class="bg-gradient-to-r from-amber-700 to-amber-600 text-white">
                    <tr>
                        <th class="px-5 py-3.5 text-left font-semibold text-xs uppercase tracking-wider">Barangay</th>
                        <th class="px-5 py-3.5 text-center font-semibold text-xs uppercase tracking-wider">Period</th>
                        <th class="px-5 py-3.5 text-left font-semibold text-xs uppercase tracking-wider">Submitted By</th>
                        <th class="px-5 py-3.5 text-left font-semibold text-xs uppercase tracking-wider">Archived At</th>
                        <th class="px-5 py-3.5 text-center font-semibold text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($monitorings as $monitoring)
                        @php($restoreBlocked = !empty(($activeMonitoringPeriods ?? [])[$monitoring->barangay_id . '-' . $monitoring->quarter . '-' . $monitoring->year]))
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-semibold flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-sm"></i>
                                    </div>
                                    <span class="text-gray-800 font-semibold">{{ $monitoring->barangay->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-calendar-alt text-xs"></i>
                                    Q{{ $monitoring->quarter }} {{ $monitoring->year }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-gray-800 font-medium">
                                    {{ $monitoring->creator?->first_name }} {{ $monitoring->creator?->last_name }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $monitoring->creator?->email }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-700">{{ $monitoring->archived_at?->format('M d, Y') ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $monitoring->archived_at?->format('g:i A') ?? '' }}</div>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2 [&>a]:inline-flex [&>a]:items-center [&>a]:justify-center [&>button]:inline-flex [&>button]:items-center [&>button]:justify-center">
                                    <a href="{{ route('admin.bcpc-case-monitoring.edit', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'view' => $monitoring->id, 'archived' => 1]) }}" data-admin-archive-view="monitoring" data-modal-title="BCPC Case Monitoring Details"
                                       class="inline-flex items-center justify-center w-9 h-9 bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg text-xs font-medium transition shadow-sm"
                                       title="View Details" aria-label="View Details">
                                        <i class="fas fa-eye"></i>
                                        <span class="sr-only">View Details</span>
                                    </a>
                                    <button type="button" {{ $restoreBlocked ? 'disabled' : '' }}
                                            @unless($restoreBlocked) onclick="restoreMonitoring({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')" @endunless
                                            class="inline-flex items-center justify-center w-9 h-9 {{ $restoreBlocked ? '!bg-gray-300 hover:!bg-gray-300 !text-gray-500 opacity-50 blur-[0.35px] cursor-not-allowed pointer-events-none' : 'bg-[#C41E3A] hover:bg-[#A01830] text-white' }} rounded-lg text-xs font-medium transition shadow-sm"
                                            title="{{ $restoreBlocked ? 'An active BCPC monitoring case already exists for this barangay, quarter, and year' : 'Restore Record' }}">
                                        <i class="fas fa-undo"></i>
                                        <span class="sr-only">Restore</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-archive text-3xl"></i>
                                </div>
                                <p class="text-gray-700 font-semibold text-base">No archived BCPC case monitoring records found.</p>
                                <p class="text-gray-400 text-xs mt-1">Archived case monitoring reports will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View -->
        <div class="archived-bcpc-monitoring-mobile-cards md:hidden p-3 space-y-3">
            @forelse($monitorings as $monitoring)
                @php($restoreBlocked = !empty(($activeMonitoringPeriods ?? [])[$monitoring->barangay_id . '-' . $monitoring->quarter . '-' . $monitoring->year]))
                <div class="archived-bcpc-monitoring-card border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center font-bold flex-shrink-0 text-sm">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="truncate">
                                <h3 class="font-bold text-gray-800 text-sm truncate">{{ $monitoring->barangay->name ?? 'N/A' }}</h3>
                                <p class="text-xs text-gray-500 truncate">{{ $monitoring->creator?->first_name }} {{ $monitoring->creator?->last_name }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0">
                            <i class="fas fa-calendar-alt text-[10px]"></i>
                            Q{{ $monitoring->quarter }} {{ $monitoring->year }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3 bg-gray-50 rounded-lg px-3 py-2">
                        <span>Archived At:</span>
                        <span class="font-medium text-gray-700">{{ $monitoring->archived_at?->format('M d, Y g:i A') ?? 'N/A' }}</span>
                    </div>

                    <div class="archived-bcpc-monitoring-card-actions grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 [&>a]:inline-flex [&>a]:items-center [&>a]:justify-center [&>button]:inline-flex [&>button]:items-center [&>button]:justify-center">
                        <a href="{{ route('admin.bcpc-case-monitoring.edit', ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year, 'view' => $monitoring->id, 'archived' => 1]) }}" data-admin-archive-view="monitoring" data-modal-title="BCPC Case Monitoring Details"
                           class="inline-flex items-center justify-center w-10 h-10 bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg text-xs font-medium transition shadow-sm"
                           title="View Details" aria-label="View Details">
                            <i class="fas fa-eye text-xs"></i>
                            <span class="sr-only">View Details</span>
                        </a>
                        <button type="button" {{ $restoreBlocked ? 'disabled' : '' }}
                                @unless($restoreBlocked) onclick="restoreMonitoring({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')" @endunless
                                class="inline-flex items-center justify-center gap-1 px-2.5 py-2 {{ $restoreBlocked ? '!bg-gray-300 hover:!bg-gray-300 !text-gray-500 opacity-50 blur-[0.35px] cursor-not-allowed pointer-events-none' : 'bg-[#C41E3A] hover:bg-[#A01830] text-white' }} rounded-lg text-xs font-medium transition shadow-sm"
                                title="{{ $restoreBlocked ? 'An active BCPC monitoring case already exists for this barangay, quarter, and year' : 'Restore Record' }}">
                            <i class="fas fa-undo text-xs"></i>
                            <span>Restore</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-500">
                    <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-archive text-2xl"></i>
                    </div>
                    <p class="text-gray-700 font-semibold text-sm">No archived BCPC case monitoring records found.</p>
                </div>
            @endforelse
        </div>

        @include('components.archived-pagination', ['paginator' => $monitorings, 'label' => 'reports'])
    </div>
</div>
@endsection

@push('scripts')
@include('admin-tailwind.components.archive-view-modal')
<script>
const archivedBcpcFilterForm = document.getElementById('archived-bcpc-report-filter-form');
let archivedBcpcSearchTimer;
document.getElementById('archived-bcpc-report-search')?.addEventListener('input', () => {
    clearTimeout(archivedBcpcSearchTimer);
    archivedBcpcSearchTimer = setTimeout(() => archivedBcpcFilterForm?.requestSubmit(), 250);
});
['archived-bcpc-report-quarter', 'archived-bcpc-report-year', 'archived-bcpc-report-barangay'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', () => archivedBcpcFilterForm?.requestSubmit());
});

function restoreMonitoring(id, barangayName, period) {
    Swal.fire({
        title: 'Restore Case Monitoring?',
        html: `
            <div class="text-center py-2">
                <i class="fas fa-undo text-5xl text-emerald-500 mb-4"></i>
                <p class="text-gray-700 font-medium">Are you sure you want to restore the BCPC case monitoring for <strong>${barangayName}</strong> (${period})?</p>
                <p class="text-gray-400 text-xs mt-2">This record will return to active monitoring records.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Restore',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Restoring...',
                text: 'Please wait while we restore this record.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/admin/bcpc-case-monitoring/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success) throw new Error(data.message || 'Failed to restore record.');
                return data;
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Restored!',
                    text: data.message || 'Record has been restored.',
                    confirmButtonText: 'OK'
                }).then(() => window.location.href = @json(route('admin.bcpc-case-monitoring.archived')));
            })
            .catch(error => Swal.fire('Error', error.message || 'An error occurred while restoring the record.', 'error'));
        }
    });
}

function deleteMonitoring(id) {
    Swal.fire({ title: 'Permanently delete record?', text: 'This archived monitoring report cannot be restored.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#b91c1c', confirmButtonText: 'Delete permanently' }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title: 'Deleting...', text: 'Please wait while we delete this archived record.', allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
        fetch(`/admin/bcpc-case-monitoring/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async response => { const data = await response.json().catch(() => ({})); if (!response.ok || !data.success) throw new Error(data.message || 'Failed to permanently delete record.'); return data; })
            .then(data => Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message || 'Archived monitoring report deleted successfully.', confirmButtonText: 'OK' }))
            .then(() => window.location.reload())
            .catch(error => Swal.fire('Error', error.message || 'Failed to permanently delete record.', 'error'));
    });
}
</script>
@endpush
