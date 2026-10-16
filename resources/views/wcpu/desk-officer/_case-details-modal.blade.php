@php
    $caseType = $case->caseType ?? null;
    // Use controller helper to get hex color for the status (expects 6-char hex without '#')
    $hex = \App\Http\Controllers\DeskOfficerController::getStatusColor($case->status);
    $hex = preg_match('/^[0-9a-fA-F]{6}$/', $hex) ? $hex : '6c757d';
    $bgColor = '#'.$hex;
    // convert to RGB and compute simple luminance to pick readable text color
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $luminance = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
    $textColor = $luminance > 186 ? '#111' : '#fff';

    // null-safe status mapping
    $statusMap = [
        'Case Filed' => 'success',
        'Case Referred' => 'info',
        'Case Closed' => 'danger',
    ];
    $statusKey = $case->status ?? '';
    $statusClass = isset($statusMap[$statusKey]) ? $statusMap[$statusKey] : 'secondary';
@endphp

<!-- Hidden metadata for JavaScript -->
<div style="display:none;" 
     data-case-number="{{ $case->case_number }}"
     data-case-id="{{ $case->id }}"
     data-is-owner="{{ ($isOwner ?? true) ? '1' : '0' }}"
     {{-- expose victim/client fields for referral JS --}}
     data-client-name="{{ $case->victim_name ?? '' }}"
     data-client-age="{{ $case->victim_age ?? '' }}"
     data-client-sex="{{ $case->victim_gender ?? '' }}"
     data-client-address="{{ $case->victim_address ?? '' }}"
     data-client-contact="{{ $case->victim_contact ?? '' }}"
></div>

<div class="case-details">
    <div class="case-header mb-4" style="background-color: #e9ecef; padding: .75rem 1rem; border-radius: .5rem;">
        <h4 class="mb-0" style="font-weight:700; margin:0;">Case #{{ $case->case_number }}</h4>
        <div class="text-muted" style="margin-top:.35rem;">
            {{-- Use computed color (from DeskOfficerController::getStatusColor) for consistent badge coloring --}}
            <span class="badge" style="background-color: {{ $bgColor }}; color: {{ $textColor }}; border-radius: .375rem; padding: .35rem .6rem; font-weight:600;">
                {{ ($case->status ?? '') === 'Case closed' ? 'Case Closed' : ($case->status ?? 'N/A') }}
            </span>
            @php $priorityColors = ['Emergency' => ['#dc2626', '#fff'], 'Urgent' => ['#f59e0b', '#111'], 'Non-urgent' => ['#16a34a', '#fff']]; $priorityStyle = $priorityColors[$case->priority] ?? ['#6b7280', '#fff']; @endphp
            <span class="badge" style="background-color: {{ $priorityStyle[0] }}; color: {{ $priorityStyle[1] }}; border-radius: .375rem; padding: .35rem .6rem; font-weight:600;">{{ $case->priority ?? 'N/A' }}</span>
             @if($caseType)
                 <span class="ms-2">{{ $case->abuse_type }}</span>
             @endif
         </div>
     </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header" style="background-color: #e9ecef; padding: .5rem 1rem; border-radius: .4rem;">
                    <h5 class="mb-0" style="font-weight:700; margin:0; color:#222;">Victim Information</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Name</dt>
                        <dd>{{ censorLastName($case->victim_name) }}</dd>

                        <dt>Age</dt>
                        <dd>{{ $case->victim_age ?? 'N/A' }}</dd>

                        <dt>Gender</dt>
                        <dd>{{ $case->victim_gender ?? 'N/A' }}</dd>

                        <dt>Contact</dt>
                        <dd>{{ $case->victim_contact ?? 'N/A' }}</dd>

                        <dt>Address</dt>
                        <dd>{{ $case->victim_address ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header" style="background-color: #e9ecef; padding: .5rem 1rem; border-radius: .4rem;">
                    <h5 class="mb-0" style="font-weight:700; margin:0; color:#222;">Perpetrator Information</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Name</dt>
                        <dd>{{ censorLastName($case->abuser_name) }}</dd>
                        
                        <dt>Age</dt>
                        <dd>{{ $case->abuser_age ?? 'N/A' }}</dd>
                        
                        <dt>Relationship to Victim</dt>
                        <dd>{{ $case->abuse_relationship ?? 'N/A' }}</dd>
                        <dt>Address of the Perpetrator</dt>
                        <dd>{{ $case->abuser_address ?: 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Guardian of Victim card: show only when any guardian data exists --}}
    @if(!empty($case->guardian) || !empty($case->guardian_contact) || !empty($case->guardian_address) || !empty($case->relationship_to_victim))
    <div class="card mb-4">
        <div class="card-header" style="background-color: #e9ecef; padding: .5rem 1rem; border-radius: .4rem;">
            <h5 class="mb-0" style="font-weight:700; margin:0; color:#222;">Guardian of Victim Information</h5>
        </div>
        <div class="card-body">
            <dl class="mb-0">
                <dt>Guardian Name</dt>
                <dd>{{ censorLastName($case->guardian) }}</dd>

                <dt>Guardian Contact</dt>
                <dd>{{ $case->guardian_contact ?? '—' }}</dd>

                <dt>Guardian Address</dt>
                <dd>{{ $case->guardian_address ?? '—' }}</dd>

                <dt>Relationship to Victim</dt>
                <dd>{{ $case->relationship_to_victim ?? '—' }}</dd>
            </dl>
        </div>
    </div>
    @endif

    {{-- Guardian of Perpetrator card: show only when any guardian data exists --}}
    @if(!empty($case->guardian_abuser_name) || !empty($case->guardian_abuser_contact) || !empty($case->guardian_abuser_address) || !empty($case->relationship_to_abuser))
    <div class="card mb-4">
        <div class="card-header" style="background-color: #e9ecef; padding: .5rem 1rem; border-radius: .4rem;">
            <h5 class="mb-0" style="font-weight:700; margin:0; color:#222;">Guardian of Perpetrator Information</h5>
        </div>
        <div class="card-body">
            <dl class="mb-0">
                <dt>Guardian Name</dt>
                <dd>{{ censorLastName($case->guardian_abuser_name) }}</dd>

                <dt>Guardian Contact</dt>
                <dd>{{ $case->guardian_abuser_contact ?? '—' }}</dd>

                <dt>Guardian Address</dt>
                <dd>{{ $case->guardian_abuser_address ?? '—' }}</dd>

                <dt>Relationship to Perpetrator</dt>
                <dd>{{ $case->relationship_to_abuser ?? '—' }}</dd>
            </dl>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header" style="background-color: #e9ecef; padding: .5rem 1rem; border-radius: .4rem;">
            <h5 class="mb-0" style="font-weight:700; margin:0; color:#222;">Incident Details</h5>
        </div>
        <div class="card-body">
            <dl class="mb-0">
                <dt>Date & Time</dt>
                <dd>{{ \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') }} at {{ $case->incident_time }}</dd>
                
                <dt>Location</dt>
                <dd>{{ $case->incident_location ?? 'N/A' }}</dd>
                
                <dt>Details</dt>
                <dd class="mb-2">{{ $case->incident_details ?? 'N/A' }}</dd>

                {{-- Witness information --}}
                @if(!empty($case->witness_name) || !empty($case->witness_contact))
                    <dt>Witness Name</dt>
                    <dd>{{ $case->witness_name ?? '—' }}</dd>

                    <dt>Witness Contact</dt>
                    <dd>{{ $case->witness_contact ?? '—' }}</dd>
                @else
                    <dt>Witness</dt>
                    <dd>N/A</dd>
                @endif
            </dl>
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
    <div class="card mb-4">
        <div class="card-header" style="background-color: #e9ecef; padding: .5rem 1rem; border-radius: .4rem;">
            <h5 class="mb-0" style="font-weight:700; margin:0; color:#222;">BCPC Case Information</h5>
        </div>
        <div class="card-body">
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
                <h6 class="mt-3 mb-2" style="font-weight:600;">Crimes Against Children</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Crime Type</th>
                            <th>Year 2023 (M/F/Total)</th>
                            <th>Year 2024 (M/F/Total)</th>
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
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $crimeKey)) }}</td>
                                <td>
                                    {{ $male2023 }} / 
                                    {{ $female2023 }} / 
                                    {{ $male2023 + $female2023 }}
                                </td>
                                <td>
                                    {{ $male2024 }} / 
                                    {{ $female2024 }} / 
                                    {{ $male2024 + $female2024 }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
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
                <h6 class="mt-3 mb-2" style="font-weight:600;">CICL Crimes</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Crime Type</th>
                            <th>Year 2023 (M/F/Total)</th>
                            <th>Year 2024 (M/F/Total)</th>
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
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $crimeKey)) }}</td>
                                <td>
                                    {{ $male2023 }} / 
                                    {{ $female2023 }} / 
                                    {{ $male2023 + $female2023 }}
                                </td>
                                <td>
                                    {{ $male2024 }} / 
                                    {{ $female2024 }} / 
                                    {{ $male2024 + $female2024 }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
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
                <h6 class="mt-3 mb-2" style="font-weight:600;">CICL Status</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Year 2023 (M/F/Total)</th>
                            <th>Year 2024 (M/F/Total)</th>
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
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                                <td>
                                    {{ $male2023 }} / 
                                    {{ $female2023 }} / 
                                    {{ $male2023 + $female2023 }}
                                </td>
                                <td>
                                    {{ $male2024 }} / 
                                    {{ $female2024 }} / 
                                    {{ $male2024 + $female2024 }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
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
                <h6 class="mt-3 mb-2" style="font-weight:600;">Children at Risk</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Year 2023 (M/F/Total)</th>
                            <th>Year 2024 (M/F/Total)</th>
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
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                                <td>
                                    {{ $male2023 }} / 
                                    {{ $female2023 }} / 
                                    {{ $male2023 + $female2023 }}
                                </td>
                                <td>
                                    {{ $male2024 }} / 
                                    {{ $female2024 }} / 
                                    {{ $male2024 + $female2024 }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
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
                <h6 class="mt-3 mb-2" style="font-weight:600;">Special Protection</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Year 2023 (M/F/Total)</th>
                            <th>Year 2024 (M/F/Total)</th>
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
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                                <td>
                                    {{ $male2023 }} / 
                                    {{ $female2023 }} / 
                                    {{ $male2023 + $female2023 }}
                                </td>
                                <td>
                                    {{ $male2024 }} / 
                                    {{ $female2024 }} / 
                                    {{ $male2024 + $female2024 }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
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
                <h6 class="mt-3 mb-2" style="font-weight:600;">Organizations</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Year 2023 (M/F/Total)</th>
                            <th>Year 2024 (M/F/Total)</th>
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
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $orgKey)) }}</td>
                                <td>
                                    {{ $male2023 }} / 
                                    {{ $female2023 }} / 
                                    {{ $male2023 + $female2023 }}
                                </td>
                                <td>
                                    {{ $male2024 }} / 
                                    {{ $female2024 }} / 
                                    {{ $male2024 + $female2024 }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header" style="background-color: #e9ecef; padding: .5rem 1rem; border-radius: .4rem;">
            <h5 class="mb-0" style="font-weight:700; margin:0; color:#222;">Case Information</h5>
        </div>
        <div class="card-body">
            <dl class="mb-0">
                <dt>Date Reported</dt>
                <dd>{{ $case->created_at->format('F j, Y \a\t g:i A') }}</dd>
                
                <dt>Last Updated</dt>
                <dd>{{ $case->updated_at->diffForHumans() }}</dd>
                
                <dt>Reported By</dt>
                @php
                    // Prefer loaded relation, otherwise try to resolve by id
                    $reporter = $case->createdBy ?? null;
                    if (!$reporter && !empty($case->created_by)) {
                        $reporter = \App\Models\Wcpu\User::find($case->created_by);
                    }
                    // Build readable name
                    $reporterName = null;
                    if ($reporter) {
                        $reporterName = $reporter->name ?? trim(($reporter->first_name ?? '') . ' ' . ($reporter->last_name ?? ''));
                    }
                    $reporterName = $reporterName ?: (!empty($case->created_by) ? 'User #' . $case->created_by : 'System');
                @endphp
                <dd>{{ $reporterName }}</dd>
            </dl>
        </div>
    </div>
    
    {{-- Hidden links for JavaScript to extract --}}
    @if($isOwner ?? true)
    <a href="{{ route('desk-officer.cases.edit', ['hash' => encrypt($case->id)]) }}" style="display:none;" class="modal-edit-link"></a>
    <a href="{{ route('desk-officer.cases.refer', ['hash' => encrypt($case->id)]) }}" style="display:none;" class="modal-refer-link"></a>
    @endif
    <a href="{{ route('desk-officer.cases.print', ['hash' => encrypt($case->id)]) }}" style="display:none;" class="modal-print-link"></a>
</div>
