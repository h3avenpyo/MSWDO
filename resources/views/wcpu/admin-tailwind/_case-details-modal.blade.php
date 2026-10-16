@php
    $caseType = $case->caseType ?? null;
    $statusColors = [
        'Case Filed' => 'bg-green-100 text-green-800',
        'Case Referred' => 'bg-orange-100 text-orange-800',
        'Case Closed' => 'bg-red-100 text-red-800',
        'Under Investigation' => 'bg-blue-100 text-blue-800',
        'Resolved' => 'bg-green-100 text-green-800',
        'Closed' => 'bg-gray-100 text-gray-800',
    ];
    $statusClass = $statusColors[$case->status] ?? 'bg-gray-100 text-gray-800';
    $isDeskOfficer = auth()->user()?->position === 'Desk Officer';
    $specificAbuseExplanation = null;
    $incidentDetailsForDisplay = $case->incident_details;
    if ((auth()->user()->isDepartmentAdmin() || $isDeskOfficer) && $case->abuse_type === 'Others'
        && preg_match('/^Specific Type of Abuse \(Others\):\s*(.*?)\R\R(.*)$/s', (string) $case->incident_details, $matches)) {
        $specificAbuseExplanation = trim($matches[1]);
        $incidentDetailsForDisplay = $matches[2];
    }
@endphp

<!-- Hidden metadata for JavaScript -->
<div class="hidden" 
     data-case-number="{{ $case->case_number }}"
     data-case-id="{{ $case->id }}"
     @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC') data-case-status="{{ $case->status }}" @endif
     data-barangay-id="{{ $case->case_designation ?? '' }}"
     data-barangay-name="{{ $case->barangay->name ?? '' }}"
     data-victim-email="{{ $case->victim_contact ?? '' }}"></div>

<!-- Print link for modal header -->
<a href="{{ auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' ? route('desk-officer.cases.print', ['hash' => encrypt($case->id)]) : route('admin.case.print', $case->id) }}" class="hidden modal-print-link"></a>
<a href="{{ auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' ? route('desk-officer.cases.refer', ['hash' => encrypt($case->id)]) : route('cases.refer.form', $case->id) }}" class="hidden modal-refer-link"></a>

<div class="space-y-6">
    <!-- Case Header -->
    <div class="bg-gradient-to-r from-purple-700 to-purple-600 rounded-lg p-4 text-white">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold text-lg">{{ $case->case_number ?? 'N/A' }}</h3>
                <p class="text-purple-200 text-sm">{{ $case->abuse_type ?? 'N/A' }}</p>
            </div>
            <div class="case-modal-summary-labels flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">{{ ($case->status ?? '') === 'Case closed' ? 'Case Closed' : ($case->status ?? 'Pending') }}</span>
                @php $priorityClass = ['Emergency' => 'bg-red-100 text-red-800', 'Urgent' => 'bg-amber-100 text-amber-800', 'Non-urgent' => 'bg-green-100 text-green-800'][$case->priority] ?? 'bg-gray-100 text-gray-800'; @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $priorityClass }}">{{ $case->priority ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Victim Information -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user text-purple-600"></i>
                    Victim Information
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Name:</span>
                    <span class="text-sm text-gray-800">{{ censorLastName($case->victim_name) }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Age:</span>
                    <span class="text-sm text-gray-800">{{ $case->victim_age ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Gender:</span>
                    <span class="text-sm text-gray-800">{{ $case->victim_gender ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Contact:</span>
                    <span class="text-sm text-gray-800">{{ $case->victim_contact ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Email:</span>
                    <span class="text-sm text-gray-800">{{ $case->victim_email ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-start gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Address:</span>
                    <span class="text-sm text-gray-800">{{ $case->victim_address ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Guardian Information -->
        @if($case->guardian || $case->guardian_contact || $case->guardian_address || $case->relationship_to_victim)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-shield text-purple-600"></i>
                    Guardian Information
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                @if($case->guardian)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Name:</span>
                    <span class="text-sm text-gray-800">{{ censorLastName($case->guardian) }}</span>
                </div>
                @endif
                @if($case->guardian_contact)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Contact:</span>
                    <span class="text-sm text-gray-800">{{ $case->guardian_contact }}</span>
                </div>
                @endif
                @if($case->guardian_address)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-start gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Address:</span>
                    <span class="text-sm text-gray-800">{{ $case->guardian_address }}</span>
                </div>
                @endif
                @if($case->relationship_to_victim)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Relationship:</span>
                    <span class="text-sm text-gray-800">{{ $case->relationship_to_victim }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Perpetrator Information -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-slash text-red-600"></i>
                    Perpetrator Information
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Name:</span>
                    <span class="text-sm text-gray-800">{{ censorLastName($case->abuser_name ?? $case->perpetrator_name ?? 'N/A') }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Age:</span>
                    <span class="text-sm text-gray-800">{{ $case->abuser_age ?? $case->perpetrator_age ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Relationship:</span>
                    <span class="text-sm text-gray-800">{{ $case->abuse_relationship ?? $case->relationship_to_perpetrator ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-start gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Address of the Perpetrator:</span>
                    <span class="text-sm text-gray-800">{{ $case->abuser_address ?: 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Guardian of Perpetrator Information -->
        @if($case->guardian_abuser_name || $case->guardian_abuser_contact || $case->guardian_abuser_address || $case->relationship_to_abuser)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-tie text-orange-600"></i>
                    Guardian of Perpetrator
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                @if($case->guardian_abuser_name)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Name:</span>
                    <span class="text-sm text-gray-800">{{ censorLastName($case->guardian_abuser_name) }}</span>
                </div>
                @endif
                @if($case->guardian_abuser_contact)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Contact:</span>
                    <span class="text-sm text-gray-800">{{ $case->guardian_abuser_contact }}</span>
                </div>
                @endif
                @if($case->guardian_abuser_address)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-start gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Address:</span>
                    <span class="text-sm text-gray-800">{{ $case->guardian_abuser_address }}</span>
                </div>
                @endif
                @if($case->relationship_to_abuser)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Relationship:</span>
                    <span class="text-sm text-gray-800">{{ $case->relationship_to_abuser }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Incident Details -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden md:col-span-2">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    Incident Details
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Date & Time:</span>
                    <span class="text-sm text-gray-800">
                        {{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'N/A' }}
                        @if($case->incident_time) at {{ $case->incident_time }} @endif
                    </span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-start gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Location:</span>
                    <span class="text-sm text-gray-800">{{ $case->incident_location ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-start gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">{{ auth()->user()->isDepartmentAdmin() ? 'Incident Details:' : 'Details:' }}</span>
                    <span class="text-sm text-gray-800">{{ $incidentDetailsForDisplay ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        @if($isDeskOfficer)
        <!-- Desk Officer: Types of Abuse -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden md:col-span-2">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-list-alt text-[#C41E3A]"></i>
                    Types of Abuse
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-4 py-3 grid grid-cols-1 sm:grid-cols-[14rem_minmax(0,1fr)] gap-1 sm:gap-4">
                    <span class="text-sm font-medium text-gray-500">Type of Abuse:</span>
                    <span class="text-sm text-gray-800">{{ $case->abuse_type ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 grid grid-cols-1 sm:grid-cols-[14rem_minmax(0,1fr)] gap-1 sm:gap-4">
                    <span class="text-sm font-medium text-gray-500">Specific Type of Abuse Explanation:</span>
                    <span class="text-sm text-gray-800 whitespace-pre-wrap">{{ $case->abuse_type === 'Others' ? ($specificAbuseExplanation ?: 'N/A') : 'N/A' }}</span>
                </div>
            </div>
        </div>
        @elseif(auth()->user()->isDepartmentAdmin())
        <!-- Types of Abuse -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden md:col-span-2">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-list-alt text-purple-600"></i>
                    Types of Abuse
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-start gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-56 flex-shrink-0">Type of Abuse:</span>
                    <span class="text-sm text-gray-800">{{ $case->abuse_type ?? 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-start gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-56 flex-shrink-0">Specific Type of Abuse Explanation:</span>
                    <span class="text-sm text-gray-800 whitespace-pre-wrap">{{ $specificAbuseExplanation ?: 'N/A' }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Case Information -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden md:col-span-2">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-folder text-purple-600"></i>
                    Case Information
                </h4>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Date Reported:</span>
                    <span class="text-sm text-gray-800">{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('F j, Y') : 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Last Updated:</span>
                    <span class="text-sm text-gray-800">{{ $case->updated_at ? $case->updated_at->format('F j, Y \a\t g:i A') : 'N/A' }}</span>
                </div>
                @if($case->createdBy)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Reported By:</span>
                    <span class="text-sm text-gray-800">{{ $case->createdBy->first_name ?? '' }} {{ $case->createdBy->last_name ?? '' }}</span>
                </div>
                @endif
                @if($case->barangay)
                <div class="px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-1">
                    <span class="text-sm font-medium text-gray-500 sm:w-32 flex-shrink-0">Barangay:</span>
                    <span class="text-sm text-gray-800">{{ $case->barangay->name }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    @php
        $isBCPCCase = $case->type === 'BCPC';
        $bcpcData = [];
        if ($isBCPCCase && $case->case_details) {
            $decoded = json_decode($case->case_details, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $bcpcData = $decoded;
            }
        }
    @endphp

    @if($isBCPCCase && !empty($bcpcData))
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden md:col-span-2">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-child text-blue-600"></i>
                BCPC Case Information
            </h4>
        </div>
        <div class="divide-y divide-gray-100">
            @php
                $hasCrimesAgainstChildren = false;
                if(isset($bcpcData['bcpc_crimes_against_children']) && !empty($bcpcData['bcpc_crimes_against_children'])) {
                    foreach($bcpcData['bcpc_crimes_against_children'] as $crimeKey => $yearData) {
                        if($crimeKey !== 'total') {
                            $male2023 = $yearData['year_2023']['male'] ?? 0;
                            $female2023 = $yearData['year_2023']['female'] ?? 0;
                            $male2024 = $yearData['year_2024']['male'] ?? 0;
                            $female2024 = $yearData['year_2024']['female'] ?? 0;
                            if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
                                $hasCrimesAgainstChildren = true;
                                break;
                            }
                        }
                    }
                }
            @endphp
            @if($hasCrimesAgainstChildren)
                <div class="px-4 py-3">
                    <h5 class="font-medium text-gray-700 mb-2">Crimes Against Children</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Crime Type</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2023 (M/F/Total)</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2024 (M/F/Total)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bcpcData['bcpc_crimes_against_children'] as $crimeKey => $yearData)
                                    @php
                                        $male2023 = $yearData['year_2023']['male'] ?? 0;
                                        $female2023 = $yearData['year_2023']['female'] ?? 0;
                                        $male2024 = $yearData['year_2024']['male'] ?? 0;
                                        $female2024 = $yearData['year_2024']['female'] ?? 0;
                                        $hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
                                    @endphp
                                    @if($crimeKey !== 'total' && $hasRowData)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2">{{ ucfirst(str_replace('_', ' ', $crimeKey)) }}</td>
                                        <td class="px-3 py-2">
                                            {{ $male2023 }} / 
                                            {{ $female2023 }} / 
                                            {{ $male2023 + $female2023 }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $male2024 }} / 
                                            {{ $female2024 }} / 
                                            {{ $male2024 + $female2024 }}
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @php
                $hasCiclCrimes = false;
                if(isset($bcpcData['bcpc_cicl_crimes']) && !empty($bcpcData['bcpc_cicl_crimes'])) {
                    foreach($bcpcData['bcpc_cicl_crimes'] as $crimeKey => $yearData) {
                        if($crimeKey !== 'total') {
                            $male2023 = $yearData['year_2023']['male'] ?? 0;
                            $female2023 = $yearData['year_2023']['female'] ?? 0;
                            $male2024 = $yearData['year_2024']['male'] ?? 0;
                            $female2024 = $yearData['year_2024']['female'] ?? 0;
                            if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
                                $hasCiclCrimes = true;
                                break;
                            }
                        }
                    }
                }
            @endphp
            @if($hasCiclCrimes)
                <div class="px-4 py-3">
                    <h5 class="font-medium text-gray-700 mb-2">CICL Crimes</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Crime Type</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2023 (M/F/Total)</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2024 (M/F/Total)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bcpcData['bcpc_cicl_crimes'] as $crimeKey => $yearData)
                                    @php
                                        $male2023 = $yearData['year_2023']['male'] ?? 0;
                                        $female2023 = $yearData['year_2023']['female'] ?? 0;
                                        $male2024 = $yearData['year_2024']['male'] ?? 0;
                                        $female2024 = $yearData['year_2024']['female'] ?? 0;
                                        $hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
                                    @endphp
                                    @if($crimeKey !== 'total' && $hasRowData)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2">{{ ucfirst(str_replace('_', ' ', $crimeKey)) }}</td>
                                        <td class="px-3 py-2">
                                            {{ $male2023 }} / 
                                            {{ $female2023 }} / 
                                            {{ $male2023 + $female2023 }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $male2024 }} / 
                                            {{ $female2024 }} / 
                                            {{ $male2024 + $female2024 }}
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @php
                $hasCiclStatus = false;
                if(isset($bcpcData['bcpc_cicl_status']) && !empty($bcpcData['bcpc_cicl_status'])) {
                    foreach($bcpcData['bcpc_cicl_status'] as $categoryKey => $yearData) {
                        if($categoryKey !== 'total') {
                            $male2023 = $yearData['year_2023']['male'] ?? 0;
                            $female2023 = $yearData['year_2023']['female'] ?? 0;
                            $male2024 = $yearData['year_2024']['male'] ?? 0;
                            $female2024 = $yearData['year_2024']['female'] ?? 0;
                            if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
                                $hasCiclStatus = true;
                                break;
                            }
                        }
                    }
                }
            @endphp
            @if($hasCiclStatus)
                <div class="px-4 py-3">
                    <h5 class="font-medium text-gray-700 mb-2">CICL Status</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Category</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2023 (M/F/Total)</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2024 (M/F/Total)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bcpcData['bcpc_cicl_status'] as $categoryKey => $yearData)
                                    @php
                                        $male2023 = $yearData['year_2023']['male'] ?? 0;
                                        $female2023 = $yearData['year_2023']['female'] ?? 0;
                                        $male2024 = $yearData['year_2024']['male'] ?? 0;
                                        $female2024 = $yearData['year_2024']['female'] ?? 0;
                                        $hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
                                    @endphp
                                    @if($categoryKey !== 'total' && $hasRowData)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2">{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                                        <td class="px-3 py-2">
                                            {{ $male2023 }} / 
                                            {{ $female2023 }} / 
                                            {{ $male2023 + $female2023 }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $male2024 }} / 
                                            {{ $female2024 }} / 
                                            {{ $male2024 + $female2024 }}
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @php
                $hasChildrenAtRisk = false;
                if(isset($bcpcData['bcpc_children_at_risk']) && !empty($bcpcData['bcpc_children_at_risk'])) {
                    foreach($bcpcData['bcpc_children_at_risk'] as $categoryKey => $yearData) {
                        if($categoryKey !== 'total') {
                            $male2023 = $yearData['year_2023']['male'] ?? 0;
                            $female2023 = $yearData['year_2023']['female'] ?? 0;
                            $male2024 = $yearData['year_2024']['male'] ?? 0;
                            $female2024 = $yearData['year_2024']['female'] ?? 0;
                            if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
                                $hasChildrenAtRisk = true;
                                break;
                            }
                        }
                    }
                }
            @endphp
            @if($hasChildrenAtRisk)
                <div class="px-4 py-3">
                    <h5 class="font-medium text-gray-700 mb-2">Children at Risk</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Category</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2023 (M/F/Total)</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2024 (M/F/Total)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bcpcData['bcpc_children_at_risk'] as $categoryKey => $yearData)
                                    @php
                                        $male2023 = $yearData['year_2023']['male'] ?? 0;
                                        $female2023 = $yearData['year_2023']['female'] ?? 0;
                                        $male2024 = $yearData['year_2024']['male'] ?? 0;
                                        $female2024 = $yearData['year_2024']['female'] ?? 0;
                                        $hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
                                    @endphp
                                    @if($categoryKey !== 'total' && $hasRowData)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2">{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                                        <td class="px-3 py-2">
                                            {{ $male2023 }} / 
                                            {{ $female2023 }} / 
                                            {{ $male2023 + $female2023 }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $male2024 }} / 
                                            {{ $female2024 }} / 
                                            {{ $male2024 + $female2024 }}
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @php
                $hasSpecialProtection = false;
                if(isset($bcpcData['bcpc_special_protection']) && !empty($bcpcData['bcpc_special_protection'])) {
                    foreach($bcpcData['bcpc_special_protection'] as $categoryKey => $yearData) {
                        if($categoryKey !== 'total') {
                            $male2023 = $yearData['year_2023']['male'] ?? 0;
                            $female2023 = $yearData['year_2023']['female'] ?? 0;
                            $male2024 = $yearData['year_2024']['male'] ?? 0;
                            $female2024 = $yearData['year_2024']['female'] ?? 0;
                            if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
                                $hasSpecialProtection = true;
                                break;
                            }
                        }
                    }
                }
            @endphp
            @if($hasSpecialProtection)
                <div class="px-4 py-3">
                    <h5 class="font-medium text-gray-700 mb-2">Special Protection</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Category</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2023 (M/F/Total)</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2024 (M/F/Total)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bcpcData['bcpc_special_protection'] as $categoryKey => $yearData)
                                    @php
                                        $male2023 = $yearData['year_2023']['male'] ?? 0;
                                        $female2023 = $yearData['year_2023']['female'] ?? 0;
                                        $male2024 = $yearData['year_2024']['male'] ?? 0;
                                        $female2024 = $yearData['year_2024']['female'] ?? 0;
                                        $hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
                                    @endphp
                                    @if($categoryKey !== 'total' && $hasRowData)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2">{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                                        <td class="px-3 py-2">
                                            {{ $male2023 }} / 
                                            {{ $female2023 }} / 
                                            {{ $male2023 + $female2023 }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $male2024 }} / 
                                            {{ $female2024 }} / 
                                            {{ $male2024 + $female2024 }}
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @php
                $hasOrganizations = false;
                if(isset($bcpcData['bcpc_organizations']) && !empty($bcpcData['bcpc_organizations'])) {
                    foreach($bcpcData['bcpc_organizations'] as $orgKey => $yearData) {
                        if($orgKey !== 'total') {
                            $male2023 = $yearData['year_2023']['male'] ?? 0;
                            $female2023 = $yearData['year_2023']['female'] ?? 0;
                            $male2024 = $yearData['year_2024']['male'] ?? 0;
                            $female2024 = $yearData['year_2024']['female'] ?? 0;
                            if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
                                $hasOrganizations = true;
                                break;
                            }
                        }
                    }
                }
            @endphp
            @if($hasOrganizations)
                <div class="px-4 py-3">
                    <h5 class="font-medium text-gray-700 mb-2">Organizations</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Organization</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2023 (M/F/Total)</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Year 2024 (M/F/Total)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bcpcData['bcpc_organizations'] as $orgKey => $yearData)
                                    @php
                                        $male2023 = $yearData['year_2023']['male'] ?? 0;
                                        $female2023 = $yearData['year_2023']['female'] ?? 0;
                                        $male2024 = $yearData['year_2024']['male'] ?? 0;
                                        $female2024 = $yearData['year_2024']['female'] ?? 0;
                                        $hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
                                    @endphp
                                    @if($orgKey !== 'total' && $hasRowData)
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2">{{ ucfirst(str_replace('_', ' ', $orgKey)) }}</td>
                                        <td class="px-3 py-2">
                                            {{ $male2023 }} / 
                                            {{ $female2023 }} / 
                                            {{ $male2023 + $female2023 }}
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $male2024 }} / 
                                            {{ $female2024 }} / 
                                            {{ $male2024 + $female2024 }}
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
