@php
    $archiveRoutePrefix = $archiveRoutePrefix ?? 'desk-officer';
    $isAdminMonitoringArchive = $archiveRoutePrefix === 'admin';
@endphp
<style>
    @media (max-width: 79.9375rem) {
        .archived-monitoring-records .archived-monitoring-table-view { display: none !important; }
        .archived-monitoring-records .archived-monitoring-cards-view { display: block !important; }
        .archived-monitoring-cards-view .monitoring-mobile-card-actions {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(3rem, 1fr)) !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }
        .archived-monitoring-cards-view .monitoring-mobile-card-actions > :is(a, button, form) {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 3rem !important;
            min-width: 3rem !important;
            min-height: 3rem !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 0.5rem !important;
        }
        .archived-monitoring-cards-view .monitoring-mobile-card-actions form > button {
            width: 100% !important;
            height: 100% !important;
            min-height: 3rem !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 0.5rem !important;
        }
        .archived-monitoring-cards-view .monitoring-mobile-card-actions :is(a, button) > i {
            margin: 0 !important;
            line-height: 1 !important;
        }
    }
    @media (min-width: 80rem) {
        .archived-monitoring-records .archived-monitoring-table-view { display: block !important; }
        .archived-monitoring-records .archived-monitoring-cards-view { display: none !important; }
    }
</style>
<section class="archived-monitoring-records bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
    <h2 class="px-6 py-4 text-lg font-semibold text-purple-800">Monitoring Reports</h2>
    <div class="archived-monitoring-table-view d-none d-xl-block hidden xl:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-amber-700 to-amber-600 text-white">
                <tr><th class="px-6 py-4 text-left">Barangay</th><th class="px-6 py-4 text-left">Period</th><th class="px-6 py-4 text-left">Submitted By</th><th class="px-6 py-4 text-left">Archived At</th><th class="px-6 py-4 text-center">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($archivedMonitorings as $monitoring)
                @php
                    $periodKey = ($isAdminMonitoringArchive ? $monitoring->barangay_id . '-' : '') . $monitoring->quarter . '-' . $monitoring->year;
                    $monitoringRestoreDisabled = !empty(($activeMonitoringPeriods ?? [])[$periodKey]);
                    $monitoringViewUrl = route($archiveRoutePrefix . '.bcpc-case-monitoring.edit', ['view' => $monitoring->id, 'archived' => 1] + ($isAdminMonitoringArchive ? ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year] : []));
                @endphp
                <tr data-archive-period="Q{{ $monitoring->quarter }} {{ $monitoring->year }}">
                    <td class="px-6 py-4">{{ $monitoring->barangay?->name }}</td>
                    <td class="px-6 py-4">Q{{ $monitoring->quarter }} {{ $monitoring->year }}</td>
                    <td class="px-6 py-4">{{ $monitoring->creator?->first_name }} {{ $monitoring->creator?->last_name }}</td>
                    <td class="px-6 py-4">{{ $monitoring->archived_at?->format('M d, Y g:i A') }}</td>
                    <td class="px-6 py-4"><div class="flex items-center justify-center gap-2">
                        <a href="{{ $monitoringViewUrl }}" title="View Record" @if($isAdminMonitoringArchive) data-admin-archive-view="monitoring" data-modal-title="Monitoring Report" @endif class="inline-flex w-10 h-10 items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition"><i class="fas fa-eye text-sm"></i><span class="sr-only">View</span></a>
                        @if($isAdminMonitoringArchive)
                        <button type="button" data-monitoring-restore aria-label="Restore report" @disabled($monitoringRestoreDisabled) title="{{ $monitoringRestoreDisabled ? 'An active monitoring report already exists for this barangay, quarter, and year' : 'Restore Report' }}"
                            @unless($monitoringRestoreDisabled) onclick="restoreMonitoring({{ $monitoring->id }}, {{ \Illuminate\Support\Js::from($monitoring->barangay?->name ?? 'Barangay') }}, {{ \Illuminate\Support\Js::from('Q' . $monitoring->quarter . ' ' . $monitoring->year) }})" @endunless
                            class="inline-flex w-10 h-10 items-center justify-center rounded-lg shadow-sm {{ $monitoringRestoreDisabled ? 'bg-red-100 text-red-800 cursor-not-allowed' : 'bg-purple-700 hover:bg-purple-800 text-white' }}"><i class="fas fa-undo text-sm"></i><span class="sr-only">Restore</span></button>
                        @else
                        <form method="POST" action="{{ route('desk-officer.bcpc-case-monitoring.restore', $monitoring->id) }}" class="restore-monitoring-form m-0 p-0 inline-flex">
                            @csrf
                            <button type="submit" @disabled($monitoringRestoreDisabled) title="{{ $monitoringRestoreDisabled ? 'An active monitoring report already exists for this quarter and year' : 'Restore Report' }}" class="inline-flex w-10 h-10 items-center justify-center rounded-lg shadow-sm transition {{ $monitoringRestoreDisabled ? 'bg-red-100 text-red-800 cursor-not-allowed' : 'bg-purple-700 hover:bg-purple-800 text-white' }}"><i class="fas fa-undo text-sm"></i><span class="sr-only">Restore</span></button>
                        </form>
                        @endif
                    </div></td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No archived monitoring reports.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile and Tablet Responsive Cards View -->
    <div class="archived-monitoring-cards-view d-block d-xl-none xl:hidden p-3 sm:p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            @forelse($archivedMonitorings as $monitoring)
            @php
                $periodKey = ($isAdminMonitoringArchive ? $monitoring->barangay_id . '-' : '') . $monitoring->quarter . '-' . $monitoring->year;
                $monitoringRestoreDisabled = !empty(($activeMonitoringPeriods ?? [])[$periodKey]);
                $monitoringViewUrl = route($archiveRoutePrefix . '.bcpc-case-monitoring.edit', ['view' => $monitoring->id, 'archived' => 1] + ($isAdminMonitoringArchive ? ['barangay_id' => $monitoring->barangay_id, 'quarter' => $monitoring->quarter, 'year' => $monitoring->year] : []));
            @endphp
            <article class="archived-monitoring-card border border-gray-100 rounded-xl p-4 bg-white shadow-sm hover:shadow-md transition flex flex-col justify-between" data-archive-period="Q{{ $monitoring->quarter }} {{ $monitoring->year }}">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center font-bold flex-shrink-0 text-sm">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="min-w-0 truncate">
                                <h3 class="font-bold text-gray-800 text-sm truncate">{{ $monitoring->barangay?->name ?? 'N/A' }}</h3>
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
                </div>

                <div class="monitoring-mobile-card-actions flex items-center justify-center gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ $monitoringViewUrl }}" data-icon-action="labelled" title="View Record" @if($isAdminMonitoringArchive) data-admin-archive-view="monitoring" data-modal-title="Monitoring Report" @endif class="inline-flex items-center justify-center bg-blue-600 hover:bg-[#A01830] text-white rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-eye text-sm"></i><span class="sr-only">View</span></a>
                    @if($isAdminMonitoringArchive)
                        <button type="button" data-monitoring-restore aria-label="Restore report" @disabled($monitoringRestoreDisabled) title="{{ $monitoringRestoreDisabled ? 'An active monitoring report already exists for this barangay, quarter, and year' : 'Restore Report' }}"
                            @unless($monitoringRestoreDisabled) onclick="restoreMonitoring({{ $monitoring->id }}, {{ \Illuminate\Support\Js::from($monitoring->barangay?->name ?? 'Barangay') }}, {{ \Illuminate\Support\Js::from('Q' . $monitoring->quarter . ' ' . $monitoring->year) }})" @endunless
                            class="inline-flex w-10 h-10 items-center justify-center rounded-lg shadow-sm {{ $monitoringRestoreDisabled ? 'bg-red-100 text-red-800 cursor-not-allowed' : 'bg-purple-700 hover:bg-purple-800 text-white' }}"><i class="fas fa-undo text-sm"></i><span class="sr-only">Restore</span></button>
                        @else
                        <form method="POST" action="{{ route('desk-officer.bcpc-case-monitoring.restore', $monitoring->id) }}" class="restore-monitoring-form inline-flex items-center justify-center m-0 p-0">
                        @csrf
                        <button type="submit" @disabled($monitoringRestoreDisabled) data-icon-action="labelled" title="{{ $monitoringRestoreDisabled ? 'An active monitoring report already exists for this quarter and year' : 'Restore Report' }}" class="inline-flex items-center justify-center {{ $monitoringRestoreDisabled ? 'bg-red-100 text-red-800 cursor-not-allowed' : 'bg-purple-700 hover:bg-purple-800 text-white' }} rounded-lg transition shadow-sm border-0 p-0 m-0"><i class="fas fa-undo text-sm"></i><span class="sr-only">Restore</span></button>
                    </form>
                        @endif
                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-10 text-gray-500">
                <div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-archive text-2xl"></i>
                </div>
                <p class="text-gray-700 font-semibold text-sm">No archived monitoring reports.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
