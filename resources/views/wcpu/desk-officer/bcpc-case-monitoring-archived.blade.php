@extends('layouts.desk-officer')

@section('title', 'Archived BCPC Case Monitoring')

@section('content')
@include('components.desk-officer.bcpc-dashboard-header', ['title' => 'Archived BCPC Case Monitoring', 'subtitle' => auth()->user()->isMultiCaseDepartment() ? 'Barangay ' . $barangay->name . ' • Archived Records' : 'View archived BCPC monitoring records'])
<style>
    .bcpc-monitoring-actions > :is(a, button) { width: 2.5rem; height: 2.5rem; padding: 0 !important; gap: 0 !important; background-color: #C41E3A !important; border-color: #C41E3A !important; color: #ffffff !important; transition: none !important; }
    .bcpc-monitoring-actions > :is(a, button) > i { margin: 0 !important; }
    .bcpc-monitoring-actions > :is(a, button):is(:hover, :focus, :active),
    .bcpc-monitoring-actions > button[title="Restore Record"]:is(:hover, :focus, :active) { background-color: #A01830 !important; border-color: #A01830 !important; color: #ffffff !important; }
    .bcpc-monitoring-actions > button:disabled,
    .bcpc-monitoring-actions > button:disabled:hover { background-color: #FEE2E2 !important; border-color: #FCA5A5 !important; color: #991B1B !important; cursor: not-allowed !important; }
    .monitoring-search-control { flex: 1 1 15rem !important; min-width: 15rem !important; }
    .monitoring-quarter-control { flex: 0 0 8.125rem; width: 8.125rem; }
    .monitoring-year-control { flex: 0 0 7.1875rem; width: 7.1875rem; }
    @media (max-width: 37.4375rem) {
        .monitoring-archived-filter-controls { display: grid !important; grid-template-columns: minmax(0, 1fr); gap: .75rem; width: 100%; }
        .monitoring-archived-filter-controls > :is(div, select, button, a) { width: 100% !important; min-width: 0 !important; max-width: none !important; flex: none !important; height: 3rem !important; min-height: 3rem !important; }
        .monitoring-archived-filter-controls > div { height: auto !important; }
        .monitoring-archived-filter-controls .monitoring-search-control { min-width: 0 !important; }
        .monitoring-archived-filter-controls #monitoring-search { height: 2.5rem !important; min-height: 2.5rem !important; }
        .archived-monitoring-table-view { display: none !important; }
        .archived-monitoring-mobile-cards { display: grid !important; gap: .75rem; }
        .archived-monitoring-card-actions { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
        .archived-monitoring-card-actions > :is(a, button) { width: 100% !important; height: 3rem !important; min-width: 3rem !important; min-height: 3rem !important; }
    }
    @media (min-width: 37.5rem) and (min-height: 30.0625rem) {
        .archived-monitoring-table-view { display: block !important; }
        .archived-monitoring-mobile-cards { display: none !important; }
    }
</style>
<div class="max-w-7xl mx-auto py-4 px-2 sm:px-4">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        @unless(auth()->user()?->position === 'Desk Officer' && in_array(auth()->user()?->defaultCaseDepartment(), ['BCPC', 'VAWC'], true))
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                Archived BCPC Case Monitoring
            </h1>
            <p class="text-gray-500 text-sm mt-1">Barangay {{ $barangay->name }} &bull; Archived Records</p>
        </div>
        @endunless
        @unless(auth()->user()?->position === 'Desk Officer' && (auth()->user()?->defaultCaseDepartment() === 'BCPC' || auth()->user()?->isMultiCaseDepartment()))
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('desk-officer.bcpc-case-monitoring.edit') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Active Records</span>
                @if(($activeCount ?? 0) > 0)
                    <span class="bg-white/25 px-2 py-0.5 rounded-full text-xs font-bold">{{ $activeCount }}</span>
                @endif
            </a>
            </div>
        </div>
        @endunless
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search and Filter Bar -->
    <form method="GET" action="{{ route('desk-officer.bcpc-case-monitoring.archived') }}" class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 mb-6">
        <div class="monitoring-archived-filter-controls flex items-center gap-2 sm:gap-3">
            <div class="monitoring-search-control relative flex-1 min-w-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="monitoring-search" name="search" value="{{ request('search') }}" placeholder="Search archived records..." class="w-full h-10 pl-9 pr-4 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
            <select id="monitoring-quarter" name="quarter" class="monitoring-quarter-control h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Quarters</option>
                @foreach([1, 2, 3, 4] as $quarterOption)
                    <option value="{{ $quarterOption }}" {{ ($quarter ?? request('quarter')) == $quarterOption ? 'selected' : '' }}>Q{{ $quarterOption }}</option>
                @endforeach
            </select>
            <select id="monitoring-year" name="year" class="monitoring-year-control h-10 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700">
                <option value="">All Years</option>
                @foreach(range(2026, 2030) as $yearOption)
                    <option value="{{ $yearOption }}" {{ ($year ?? request('year')) == $yearOption ? 'selected' : '' }}>{{ $yearOption }}</option>
                @endforeach
            </select>
            <button type="submit" style="flex: 0 0 auto !important;" class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-purple-700 hover:bg-purple-800 text-white rounded-xl text-sm font-medium shadow-sm transition shrink-0">
                <i class="fas fa-filter"></i>
                <span>Filter</span>
            </button>
            @if(request()->filled('search') || request()->filled('quarter') || request()->filled('year'))
            <a href="{{ route('desk-officer.bcpc-case-monitoring.archived') }}" style="flex: 0 0 auto !important;" class="inline-flex h-10 items-center justify-center gap-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition shrink-0">
                <i class="fas fa-times"></i>
                <span>Clear</span>
            </a>
            @endif
        </div>
    </form>

    <!-- Archived Table -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <!-- Desktop Table View -->
        <div class="archived-monitoring-table-view overflow-x-auto hidden md:block">
            <table class="w-full text-sm">
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
                        @php($restoreDisabled = !empty($activePeriods[$monitoring->quarter . '-' . $monitoring->year]))
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
                                <div class="text-sm font-medium text-gray-700">{{ $monitoring->archived_at?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $monitoring->archived_at?->copy()->timezone('Asia/Manila')->format('g:i A') ?? '' }}</div>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <div class="bcpc-monitoring-actions flex items-center justify-center gap-2 [&>a]:inline-flex [&>a]:items-center [&>a]:justify-center [&>button]:inline-flex [&>button]:items-center [&>button]:justify-center">
                                    <a href="{{ route('desk-officer.bcpc-case-monitoring.edit', ['view' => $monitoring->id, 'archived' => 1]) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                       title="View Record">
                                        <i class="fas fa-eye"></i>
                                        <span class="sr-only">View</span>
                                    </a>
                                    <button type="button" {{ $restoreDisabled ? 'disabled' : '' }}
                                            @unless($restoreDisabled) onclick="restoreMonitoring({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')" @endunless
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition shadow-sm"
                                            title="Restore Record">
                                        <i class="fas fa-undo"></i>
                                        <span class="sr-only">Restore</span>
                                    </button>
                                    @unless(auth()->user()->defaultCaseDepartment() === 'BCPC' || auth()->user()->isMultiCaseDepartment())<button type="button" onclick="deleteMonitoring({{ $monitoring->id }})" class="inline-flex items-center justify-center bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg" title="Delete Record"><i class="fas fa-trash"></i></button>@endunless
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
                                <p class="text-gray-400 text-xs mt-1">Archived reports will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View -->
        <div class="archived-monitoring-mobile-cards md:hidden p-3 space-y-3">
            @forelse($monitorings as $monitoring)
                @php($restoreDisabled = !empty($activePeriods[$monitoring->quarter . '-' . $monitoring->year]))
                <article class="archived-monitoring-card border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition">
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
                        <span class="font-medium text-gray-700">{{ $monitoring->archived_at?->copy()->timezone('Asia/Manila')->format('M d, Y g:i A') ?? 'N/A' }}</span>
                    </div>

                    <div class="bcpc-monitoring-actions archived-monitoring-card-actions grid grid-cols-3 gap-2 pt-2 border-t border-gray-100 [&>a]:inline-flex [&>a]:items-center [&>a]:justify-center [&>button]:inline-flex [&>button]:items-center [&>button]:justify-center">
                        <a href="{{ route('desk-officer.bcpc-case-monitoring.edit', ['view' => $monitoring->id, 'archived' => 1]) }}"
                           class="inline-flex items-center justify-center gap-1 px-2.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition shadow-sm">
                            <i class="fas fa-eye text-xs"></i>
                            <span class="sr-only">View</span>
                        </a>
                        <button type="button" {{ $restoreDisabled ? 'disabled' : '' }}
                                @unless($restoreDisabled) onclick="restoreMonitoring({{ $monitoring->id }}, '{{ addslashes($monitoring->barangay->name ?? 'Barangay') }}', 'Q{{ $monitoring->quarter }} {{ $monitoring->year }}')" @endunless
                                class="inline-flex items-center justify-center gap-1 px-2.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition shadow-sm">
                            <i class="fas fa-undo text-xs"></i>
                            <span class="sr-only">Restore</span>
                        </button>
                        @unless(auth()->user()->defaultCaseDepartment() === 'BCPC' || auth()->user()->isMultiCaseDepartment())<button type="button" onclick="deleteMonitoring({{ $monitoring->id }})" class="inline-flex items-center justify-center bg-[#C41E3A] hover:bg-[#A01830] text-white rounded-lg"><i class="fas fa-trash text-xs"></i></button>@endunless
                    </div>
                </article>
            @empty
                <div class="text-center py-10 text-gray-500">
                    <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-archive text-2xl"></i>
                    </div>
                    <p class="text-gray-700 font-semibold text-sm">No archived BCPC case monitoring records found.</p>
                </div>
            @endforelse
        </div>

        @if($monitorings->hasPages())
            @include('components.archived-pagination', ['paginator' => $monitorings, 'label' => 'archived BCPC reports'])
        @endif
    </div>
</div>

@push('scripts')
<script>
function restoreMonitoring(id, barangayName, period) {
    Swal.fire({
        title: 'Restore Case Monitoring?',
        html: `
            <div class="text-center py-2">
                <i class="fas fa-undo text-5xl text-emerald-500 mb-4"></i>
                <p class="text-gray-700 font-medium">Are you sure you want to restore the BCPC case monitoring for <strong>${barangayName || 'this barangay'}</strong> (${period || ''})?</p>
                <p class="text-gray-400 text-xs mt-2">This record will be moved back to active BCPC case monitoring records.</p>
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
                title: 'Restoring Monitoring Report...',
                text: 'Please wait while the monitoring report is restored.',
                allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/desk-officer/bcpc-case-monitoring/${id}/restore`, {
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Restored!',
                        text: data.message || 'Record has been restored.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#059669',
                        showConfirmButton: true
                    }).then(() => window.location.href = @json(route('desk-officer.bcpc-case-monitoring.archived')));
                } else {
                    Swal.fire('Error', data.message || 'Failed to restore record.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'An error occurred while restoring the record.', 'error'));
        }
    });
}

function deleteMonitoring(id) {
    Swal.fire({ icon: 'warning', title: 'Delete archived report?', text: 'This archived BCPC monitoring report will be permanently deleted.', showCancelButton: true, confirmButtonText: 'Delete', confirmButtonColor: '#C41E3A' }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`/desk-officer/bcpc-case-monitoring/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } })
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Unable to delete the report.');
                Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message }).then(() => window.location.reload());
            })
            .catch(error => Swal.fire({ icon: 'error', title: 'Unable to Delete', text: error.message }));
    });
}
// Live client-side search & filter on table rows
const monSearch = document.getElementById('monitoring-search');
const monQuarter = document.getElementById('monitoring-quarter');
const monYear = document.getElementById('monitoring-year');
function filterMonitoringRows() {
    const q = (monSearch?.value || '').toLowerCase().trim();
    const qVal = (monQuarter?.value || '').toLowerCase().trim();
    const yVal = (monYear?.value || '').toLowerCase().trim();
    document.querySelectorAll('table tbody tr').forEach(row => {
        if (row.cells.length < 5) return;
        const text = row.textContent.toLowerCase();
        const periodCell = (row.cells[1]?.textContent || '').toLowerCase().trim();
        const matchesSearch = !q || text.includes(q);
        const matchesQuarter = !qVal || periodCell.includes('q' + qVal);
        const matchesYear = !yVal || periodCell.includes(yVal);
        row.style.display = (matchesSearch && matchesQuarter && matchesYear) ? '' : 'none';
    });
    document.querySelectorAll('.grid > div.bg-gray-50').forEach(card => {
        const text = card.textContent.toLowerCase();
        const matchesSearch = !q || text.includes(q);
        const matchesQuarter = !qVal || text.includes('q' + qVal);
        const matchesYear = !yVal || text.includes(yVal);
        card.style.display = (matchesSearch && matchesQuarter && matchesYear) ? '' : 'none';
    });
}
if (monSearch) monSearch.addEventListener('input', filterMonitoringRows);
if (monQuarter) monQuarter.addEventListener('change', filterMonitoringRows);
if (monYear) monYear.addEventListener('change', filterMonitoringRows);
</script>
@endpush
@endsection
