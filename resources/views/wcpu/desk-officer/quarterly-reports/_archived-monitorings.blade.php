<section class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
    <h2 class="px-6 py-4 text-lg font-semibold text-purple-800">Monitoring Reports</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-amber-700 to-amber-600 text-white">
                <tr><th class="px-6 py-4 text-left">Barangay</th><th class="px-6 py-4 text-left">Period</th><th class="px-6 py-4 text-left">Submitted By</th><th class="px-6 py-4 text-left">Archived At</th><th class="px-6 py-4 text-center">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($archivedMonitorings as $monitoring)
                @php($monitoringRestoreDisabled = !empty($activeMonitoringPeriods[$monitoring->quarter . '-' . $monitoring->year]))
                <tr data-archive-period="Q{{ $monitoring->quarter }} {{ $monitoring->year }}">
                    <td class="px-6 py-4">{{ $monitoring->barangay?->name }}</td>
                    <td class="px-6 py-4">Q{{ $monitoring->quarter }} {{ $monitoring->year }}</td>
                    <td class="px-6 py-4">{{ $monitoring->creator?->first_name }} {{ $monitoring->creator?->last_name }}</td>
                    <td class="px-6 py-4">{{ $monitoring->archived_at?->format('M d, Y g:i A') }}</td>
                    <td class="px-6 py-4"><div class="flex items-center justify-center gap-2">
                        <a href="{{ route('desk-officer.bcpc-case-monitoring.edit', ['view' => $monitoring->id, 'archived' => 1]) }}" title="View Record" class="inline-flex w-10 h-10 items-center justify-center bg-blue-600 text-white rounded-lg"><i class="fas fa-eye"></i><span class="sr-only">View</span></a>
                        <form method="POST" action="{{ route('desk-officer.bcpc-case-monitoring.restore', $monitoring->id) }}" class="restore-monitoring-form">
                            @csrf
                            <button type="submit" @disabled($monitoringRestoreDisabled) title="{{ $monitoringRestoreDisabled ? 'An active monitoring report already exists for this quarter and year' : 'Restore Report' }}" class="inline-flex w-10 h-10 items-center justify-center rounded-lg {{ $monitoringRestoreDisabled ? 'bg-red-100 text-red-800 cursor-not-allowed' : 'bg-purple-700 text-white' }}"><i class="fas fa-undo"></i><span class="sr-only">Restore</span></button>
                        </form>
                    </div></td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No archived monitoring reports.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
