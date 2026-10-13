<form method="{{ $monitoringReport ? 'GET' : 'POST' }}" action="{{ $monitoringReport ? route('admin.bcpc-case-monitoring.print') : route('admin.quarterly-reports.store') }}" class="flex flex-wrap items-end gap-3 bg-white border border-gray-200 rounded-xl p-4">
    @unless($monitoringReport) @csrf @endunless
    <label class="text-sm text-gray-600">Barangay
        <select name="barangay_id" required class="block border border-gray-300 rounded-lg px-3 py-2 mt-1">
            <option value="">Select Barangay</option>
            @foreach($barangays as $barangay)
                <option value="{{ $barangay->id }}" @selected(request('barangay_id') == $barangay->id)>{{ $barangay->name }}</option>
            @endforeach
        </select>
    </label>
    <label class="text-sm text-gray-600">Quarter
        <select name="quarter" required class="block border border-gray-300 rounded-lg px-3 py-2 mt-1">
            @foreach([1, 2, 3, 4] as $q)
                <option value="{{ $q }}" @selected(request('quarter', ceil(now()->month / 3)) == $q)>Q{{ $q }}</option>
            @endforeach
        </select>
    </label>
    <label class="text-sm text-gray-600">Year
        <input type="number" name="year" min="2020" max="2100" required value="{{ request('year', now()->year) }}" class="block w-28 border border-gray-300 rounded-lg px-3 py-2 mt-1">
    </label>
    @unless($monitoringReport)
        <input type="hidden" name="type" value="{{ auth()->user()->defaultCaseDepartment() }}">
    @endunless
    <button type="submit" class="px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm">{{ $monitoringReport ? 'Generate Monitoring Report' : 'Generate Barangay Cases Report' }}</button>
</form>
