<!DOCTYPE html>
@php $isBcpcCase = strtoupper((string) ($case->type ?? 'VAWC')) === 'BCPC'; @endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Report - {{ $case->case_number }} - {{ $isBcpcCase ? 'BCPC' : 'iSERVE SILANG' }}</title>
    <style>
        @media print {
            @page {
                margin: 15mm 20mm;
                size: A4;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .action-buttons {
                display: none !important;
            }
            
            body {
                margin: 0;
                font-size: 12px;
            }
            
            .case-report {
                margin: 0;
                padding: 20px;
                box-shadow: none;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .case-report {
            background: white;
            margin: 20px auto;
            max-width: 800px;
            padding: 30px;
            font-family: Arial, sans-serif;
            line-height: 1.4;
        }
        
        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .case-info-line {
            margin: 10px 0;
            font-size: 14px;
        }
        
        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        
        .form-table th,
        .form-table td {
            border: 1px solid #000;
            padding: 10px 12px;
            text-align: left;
            vertical-align: middle;
            height: 38px;
        }
        
        .form-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .label-cell {
            width: 28%;
            font-weight: bold;
            background-color: #f5f5f5;
            text-align: left;
        }
        
        .value-cell {
            width: 72%;
            text-align: left;
        }
        
        .section-header {
            font-weight: bold;
            font-size: 14px;
            margin: 25px 0 10px 0;
            text-transform: uppercase;
        }

        .two-column-container {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .two-column-container .column {
            flex: 1;
            min-width: 0;
            box-sizing: border-box;
        }

        .two-column-container .section-header {
            margin-top: 0;
        }

        @media print {
            .two-column-container {
                display: flex !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .two-column-container .column {
                flex: 1 !important;
            }
        }
        
        .action-buttons {
            margin: 20px 0;
            text-align: center;
        }
        
        .btn {
            padding: 12px 24px;
            margin: 0 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            text-decoration: none;
        }
        
        .btn-back {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-back:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            color: white;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #7c3299, #6a1b9a);
            color: white;
        }
        
        .btn-print:hover {
            background: linear-gradient(135deg, #6a1b9a, #5a1585);
            color: white;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin: 30px 0 5px 0;
            height: 20px;
        }

        /* Reliable PDF signature layout */
        .signature-table { width:100%; margin-top:45px; border-collapse:collapse; }
        .signature-table td { width:50%; vertical-align:top; padding:0 24px; }
        .signature-cell .sig-label { font-size:13px; margin-bottom:16px; }
        .signature-cell .sig-line { border-bottom:1px solid #000; height:22px; margin:46px 0 6px; }
        .signature-cell .sig-role { text-align:center; font-size:12px; }
        /* Hide old flex version in print/PDF if it exists */
        .signature-section { display:none !important; }
        @media screen {
            /* If you still want the original flex layout on screen, show it and hide table */
            /* .signature-section { display:flex !important; } */
            /* .signature-table { display:none; } */
        }

        /* Optional footer placeholder (hidden on screen) */
        @media print {
            .email-note { display:none; }
        }
    </style>
</head>
<body>
    <div class="case-report">
        <!-- Action Buttons -->
        <div class="action-buttons no-print">
            <a href="javascript:history.back()" class="btn btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Case
            </a>
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i>
                Print Report
            </button>
        </div>

        <!-- Header -->
        <table style="width: 100%; border: none; margin-bottom: 15px; border-collapse: collapse;">
            <tr>
                <td style="width: 65px; vertical-align: middle; text-align: left; border: none; padding: 0;">
                    <img src="{{ asset('images/mswdo_logo.jpg') }}" style="width: 55px; height: 55px;" alt="MSWDO Logo">
                </td>
                <td style="vertical-align: middle; text-align: center; border: none; padding: 0;">
                    <div class="report-title" style="margin-bottom: 3px;">{{ $isBcpcCase ? 'BCPC CASE REPORT FORM' : 'SWD CASE REPORT FORM' }}</div>
                    <div class="case-info-line">Case Number: <strong>{{ $case->case_number }}</strong></div>
                    <div class="case-info-line">BARANGAY: <strong>{{ $barangayName }}</strong>, Municipality: Silang, Cavite</div>
                    <div class="case-info-line">Status: <strong>{{ $case->status }}</strong></div>
                    <div class="case-info-line" style="margin-top: 4px; font-size: 11px; color: #666;">
                        <strong>Generated on:</strong> {{ now()->format('F j, Y \a\t g:i A') }} ({{ now()->format('T') }})
                    </div>
                </td>
                <td style="width: 65px; border: none; padding: 0;"></td>
            </tr>
        </table>

        <!-- Case Information -->
        <div class="section-header">CASE INFORMATION</div>
        <table class="form-table">
            <tr>
                <td class="label-cell">Case Number</td>
                <td class="value-cell">{{ $case->case_number }}</td>
            </tr>
            <tr>
                <td class="label-cell">Type of Case</td>
                <td class="value-cell">{{ $case->abuse_type ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Date Reported</td>
                <td class="value-cell">{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('F j, Y') : 'Not specified' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Status</td>
                <td class="value-cell">{{ $case->status ?? 'Pending' }}</td>
            </tr>
        </table>

        <!-- Victim Information -->
        @php
            $hasVictimGuardian = $case->guardian || $case->guardian_contact || $case->guardian_address || $case->relationship_to_victim;
            $hasPerpetratorGuardian = $case->guardian_abuser_name || $case->guardian_abuser_contact || $case->guardian_abuser_address || $case->relationship_to_abuser;
        @endphp

        @if($hasVictimGuardian || $hasPerpetratorGuardian)
            {{-- If any guardian exists, show each person with their guardian --}}
            <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                <tr>
                    <td style="width:50%; vertical-align:top; padding-right:8px; height:200px;">
                        <div class="section-header">VICTIM INFORMATION</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Full Name</td>
                                <td class="value-cell">{{ $case->victim_name ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Age</td>
                                <td class="value-cell">{{ $case->victim_age ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Gender</td>
                                <td class="value-cell">{{ $case->victim_gender ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Contact Number</td>
                                <td class="value-cell">{{ $case->victim_contact ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Address</td>
                                <td class="value-cell">{{ $case->victim_address ?? 'Not provided' }}</td>
                            </tr>
                        </table>
                    </td>
                    @if($hasVictimGuardian)
                    <td style="width:50%; vertical-align:top; padding-left:8px; height:200px;">
                        <!-- Guardian of Victim Information -->
                        <div class="section-header">GUARDIAN OF VICTIM INFORMATION</div>
                        <table class="form-table">
                            @if($case->guardian)
                            <tr>
                                <td class="label-cell">Guardian Name</td>
                                <td class="value-cell">{{ censorLastName($case->guardian) }}</td>
                            </tr>
                            @endif
                            @if($case->guardian_contact)
                            <tr>
                                <td class="label-cell">Guardian Contact</td>
                                <td class="value-cell">{{ $case->guardian_contact }}</td>
                            </tr>
                            @endif
                            @if($case->guardian_address)
                            <tr>
                                <td class="label-cell">Guardian Address</td>
                                <td class="value-cell">{{ $case->guardian_address }}</td>
                            </tr>
                            @endif
                            @if($case->relationship_to_victim)
                            <tr>
                                <td class="label-cell">Relationship to Victim</td>
                                <td class="value-cell">{{ $case->relationship_to_victim }}</td>
                            </tr>
                            @endif
                        </table>
                    </td>
                    @endif
                </tr>
            </table>

            <!-- Perpetrator Information -->
            <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                <tr>
                    <td style="width:50%; vertical-align:top; padding-right:8px; height:200px;">
                        <div class="section-header">PERPETRATOR INFORMATION</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Full Name</td>
                                <td class="value-cell">{{ censorLastName($case->abuser_name) }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Age</td>
                                <td class="value-cell">{{ $case->abuser_age ?? 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Relationship to Victim</td>
                                <td class="value-cell">{{ $case->abuse_relationship ?? 'Not specified' }}</td>
                            </tr>
                        </table>
                    </td>
                    @if($hasPerpetratorGuardian)
                    <td style="width:50%; vertical-align:top; padding-left:8px; height:200px;">
                        <!-- Guardian of Perpetrator Information -->
                        <div class="section-header">GUARDIAN OF PERPETRATOR INFORMATION</div>
                        <table class="form-table">
                            @if($case->guardian_abuser_name)
                            <tr>
                                <td class="label-cell">Guardian Name</td>
                                <td class="value-cell">{{ censorLastName($case->guardian_abuser_name) }}</td>
                            </tr>
                            @endif
                            @if($case->guardian_abuser_contact)
                            <tr>
                                <td class="label-cell">Guardian Contact</td>
                                <td class="value-cell">{{ $case->guardian_abuser_contact }}</td>
                            </tr>
                            @endif
                            @if($case->guardian_abuser_address)
                            <tr>
                                <td class="label-cell">Guardian Address</td>
                                <td class="value-cell">{{ $case->guardian_abuser_address }}</td>
                            </tr>
                            @endif
                            @if($case->relationship_to_abuser)
                            <tr>
                                <td class="label-cell">Relationship to Perpetrator</td>
                                <td class="value-cell">{{ $case->relationship_to_abuser }}</td>
                            </tr>
                            @endif
                        </table>
                    </td>
                    @else
                    <td style="width:50%; vertical-align:top; padding-left:8px; height:200px;">
                        <div class="section-header">INCIDENT DETAILS</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Date of Incident</td>
                                <td class="value-cell">{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Time of Incident</td>
                                <td class="value-cell">{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('h:i A') : 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Location</td>
                                <td class="value-cell">{{ $case->incident_location ?? 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Incident Details</td>
                                <td class="value-cell">{{ $case->incident_details ?? 'No details provided' }}</td>
                            </tr>
                        </table>
                    </td>
                    @endif
                </tr>
            </table>

            @if($hasPerpetratorGuardian)
            {{-- If perpetrator has guardian, show incident details and case management in next row --}}
            <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                <tr>
                    <td style="width:50%; vertical-align:top; padding-right:8px; height:200px;">
                        <div class="section-header">INCIDENT DETAILS</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Date of Incident</td>
                                <td class="value-cell">{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Time of Incident</td>
                                <td class="value-cell">{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('h:i A') : 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Location</td>
                                <td class="value-cell">{{ $case->incident_location ?? 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Incident Details</td>
                                <td class="value-cell">{{ $case->incident_details ?? 'No details provided' }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%; vertical-align:top; padding-left:8px; height:200px;">
                        <div class="section-header">CASE MANAGEMENT</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Case Created</td>
                                <td class="value-cell">{{ $case->created_at ? $case->created_at->format('F j, Y \a\t g:i A') : 'Not available' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Last Updated</td>
                                <td class="value-cell">{{ $case->updated_at ? $case->updated_at->format('F j, Y \a\t g:i A') : 'Not available' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Assigned Officer</td>
                                <td class="value-cell">{{ $case->assignedTo->full_name ?? 'Not assigned' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Created By</td>
                                <td class="value-cell">{{ $case->createdBy->full_name ?? 'System' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            @else
            {{-- If no perpetrator guardian, incident details already shown, just show case management --}}
            <div class="section-header">CASE MANAGEMENT</div>
            <table class="form-table">
                <tr>
                    <td class="label-cell">Case Created</td>
                    <td class="value-cell">{{ $case->created_at ? $case->created_at->format('F j, Y \a\t g:i A') : 'Not available' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Last Updated</td>
                    <td class="value-cell">{{ $case->updated_at ? $case->updated_at->format('F j, Y \a\t g:i A') : 'Not available' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Assigned Officer</td>
                    <td class="value-cell">{{ $case->assignedTo->full_name ?? 'Not assigned' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Created By</td>
                    <td class="value-cell">{{ $case->createdBy->full_name ?? 'System' }}</td>
                </tr>
            </table>
            @endif
        @else
            {{-- If no guardians, show victim and perpetrator side-by-side --}}
            <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                <tr>
                    <td style="width:50%; vertical-align:top; padding-right:8px; height:200px;">
                        <div class="section-header">VICTIM INFORMATION</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Full Name</td>
                                <td class="value-cell">{{ censorLastName($case->victim_name) }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Age</td>
                                <td class="value-cell">{{ $case->victim_age ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Gender</td>
                                <td class="value-cell">{{ $case->victim_gender ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Contact Number</td>
                                <td class="value-cell">{{ $case->victim_contact ?? 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Address</td>
                                <td class="value-cell">{{ $case->victim_address ?? 'Not provided' }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%; vertical-align:top; padding-left:8px; height:200px;">
                        <div class="section-header">PERPETRATOR INFORMATION</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Full Name</td>
                                <td class="value-cell">{{ censorLastName($case->abuser_name) }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Age</td>
                                <td class="value-cell">{{ $case->abuser_age ?? 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Relationship to Victim</td>
                                <td class="value-cell">{{ $case->abuse_relationship ?? 'Not specified' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Incident Details and Case Management -->
            <table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
                <tr>
                    <td style="width:50%; vertical-align:top; padding-right:8px; height:200px;">
                        <div class="section-header">INCIDENT DETAILS</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Date of Incident</td>
                                <td class="value-cell">{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Time of Incident</td>
                                <td class="value-cell">{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('h:i A') : 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Location</td>
                                <td class="value-cell">{{ $case->incident_location ?? 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Incident Details</td>
                                <td class="value-cell">{{ $case->incident_details ?? 'No details provided' }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%; vertical-align:top; padding-left:8px; height:200px;">
                        <div class="section-header">CASE MANAGEMENT</div>
                        <table class="form-table">
                            <tr>
                                <td class="label-cell">Case Created</td>
                                <td class="value-cell">{{ $case->created_at ? $case->created_at->format('F j, Y \a\t g:i A') : 'Not available' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Last Updated</td>
                                <td class="value-cell">{{ $case->updated_at ? $case->updated_at->format('F j, Y \a\t g:i A') : 'Not available' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Assigned Officer</td>
                                <td class="value-cell">{{ $case->assignedTo->full_name ?? 'Not assigned' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Created By</td>
                                <td class="value-cell">{{ $case->createdBy->full_name ?? 'System' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        @endif

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
            <div class="section-header">BCPC CASE INFORMATION</div>
            <table class="form-table" style="margin-bottom: 10px;">
                <tr>
                    <th colspan="3" style="text-align: center; background: #f0f0f0; padding: 8px;">CRIMES AGAINST CHILDREN</th>
                </tr>
                <tr>
                    <th>Crime Type</th>
                    <th>Year 2023 (M/F/Total)</th>
                    <th>Year 2024 (M/F/Total)</th>
                </tr>
                @if(isset($bcpcData['bcpc_crimes_against_children']))
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
                            <td class="label-cell">{{ ucfirst(str_replace('_', ' ', $crimeKey)) }}</td>
                            <td class="value-cell">
                                {{ $male2023 }} / 
                                {{ $female2023 }} / 
                                {{ $male2023 + $female2023 }}
                            </td>
                            <td class="value-cell">
                                {{ $male2024 }} / 
                                {{ $female2024 }} / 
                                {{ $male2024 + $female2024 }}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
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
            <table class="form-table" style="margin-bottom: 10px;">
                <tr>
                    <th colspan="3" style="text-align: center; background: #f0f0f0; padding: 8px;">CICL CRIMES</th>
                </tr>
                <tr>
                    <th>Crime Type</th>
                    <th>Year 2023 (M/F/Total)</th>
                    <th>Year 2024 (M/F/Total)</th>
                </tr>
                @if(isset($bcpcData['bcpc_cicl_crimes']))
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
                            <td class="label-cell">{{ ucfirst(str_replace('_', ' ', $crimeKey)) }}</td>
                            <td class="value-cell">
                                {{ $male2023 }} / 
                                {{ $female2023 }} / 
                                {{ $male2023 + $female2023 }}
                            </td>
                            <td class="value-cell">
                                {{ $male2024 }} / 
                                {{ $female2024 }} / 
                                {{ $male2024 + $female2024 }}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
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
            <table class="form-table" style="margin-bottom: 10px;">
                <tr>
                    <th colspan="3" style="text-align: center; background: #f0f0f0; padding: 8px;">CICL STATUS</th>
                </tr>
                <tr>
                    <th>Category</th>
                    <th>Year 2023 (M/F/Total)</th>
                    <th>Year 2024 (M/F/Total)</th>
                </tr>
                @if(isset($bcpcData['bcpc_cicl_status']))
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
                            <td class="label-cell">{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                            <td class="value-cell">
                                {{ $male2023 }} / 
                                {{ $female2023 }} / 
                                {{ $male2023 + $female2023 }}
                            </td>
                            <td class="value-cell">
                                {{ $male2024 }} / 
                                {{ $female2024 }} / 
                                {{ $male2024 + $female2024 }}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
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
            <table class="form-table" style="margin-bottom: 10px;">
                <tr>
                    <th colspan="3" style="text-align: center; background: #f0f0f0; padding: 8px;">CHILDREN AT RISK</th>
                </tr>
                <tr>
                    <th>Category</th>
                    <th>Year 2023 (M/F/Total)</th>
                    <th>Year 2024 (M/F/Total)</th>
                </tr>
                @if(isset($bcpcData['bcpc_children_at_risk']))
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
                            <td class="label-cell">{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                            <td class="value-cell">
                                {{ $male2023 }} / 
                                {{ $female2023 }} / 
                                {{ $male2023 + $female2023 }}
                            </td>
                            <td class="value-cell">
                                {{ $male2024 }} / 
                                {{ $female2024 }} / 
                                {{ $male2024 + $female2024 }}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
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
            <table class="form-table" style="margin-bottom: 10px;">
                <tr>
                    <th colspan="3" style="text-align: center; background: #f0f0f0; padding: 8px;">SPECIAL PROTECTION</th>
                </tr>
                <tr>
                    <th>Category</th>
                    <th>Year 2023 (M/F/Total)</th>
                    <th>Year 2024 (M/F/Total)</th>
                </tr>
                @if(isset($bcpcData['bcpc_special_protection']))
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
                            <td class="label-cell">{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
                            <td class="value-cell">
                                {{ $male2023 }} / 
                                {{ $female2023 }} / 
                                {{ $male2023 + $female2023 }}
                            </td>
                            <td class="value-cell">
                                {{ $male2024 }} / 
                                {{ $female2024 }} / 
                                {{ $male2024 + $female2024 }}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
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
            <table class="form-table" style="margin-bottom: 10px;">
                <tr>
                    <th colspan="3" style="text-align: center; background: #f0f0f0; padding: 8px;">ORGANIZATIONS</th>
                </tr>
                <tr>
                    <th>Organization</th>
                    <th>Year 2023 (M/F/Total)</th>
                    <th>Year 2024 (M/F/Total)</th>
                </tr>
                @if(isset($bcpcData['bcpc_organizations']))
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
                            <td class="label-cell">{{ ucfirst(str_replace('_', ' ', $orgKey)) }}</td>
                            <td class="value-cell">
                                {{ $male2023 }} / 
                                {{ $female2023 }} / 
                                {{ $male2023 + $female2023 }}
                            </td>
                            <td class="value-cell">
                                {{ $male2024 }} / 
                                {{ $female2024 }} / 
                                {{ $male2024 + $female2024 }}
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
            </table>
            @endif
        @endif

        <!-- NEW: Table-based signature block for PDF -->
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="sig-label">Prepared by:</div>
                    <div class="sig-line"></div>
                    <div class="sig-role">{{ $isBcpcCase ? 'BCPC Desk Officer' : 'WCPU Desk Officer' }}</div>
                </td>
                <td class="signature-cell">
                    <div class="sig-label">Approved by:</div>
                    <div class="sig-line"></div>
                    <div class="sig-role">Barangay Captain</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="email-note" style="text-align:center;margin:25px 0 10px;font-size:11px;color:#777;">
        Case Report | {{ strtoupper((string) ($case->type ?? '')) === 'BCPC' ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System
    </div>

    <script>
        // Auto-focus print dialog when page loads (optional)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });
    </script>
@include('print.edit-controls')
</body>
</html>
