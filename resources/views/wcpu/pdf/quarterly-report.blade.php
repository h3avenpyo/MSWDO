<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quarterly Compliance Monitoring Report - {{ $barangay->name }}</title>
    <style>
        @page { 
            size: legal landscape; 
            margin: 12mm 20mm;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 10pt; 
            line-height: 1.4; 
            color: #000; 
            background: #fff; 
        }
        
        table { border-collapse: collapse; }
        .page-content { width: 92%; max-width: 290mm; margin: 0 auto; }
        
        /* Header Section */
        .header-table {
            width: 100%;
            margin: 0 auto 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 10px 2px;
        }
        
        .republic {
            font-size: 11pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
        }
        
        .municipality {
            font-size: 12pt;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 4px;
        }
        
        .province {
            font-size: 10pt;
            font-style: italic;
            margin-top: 3px;
        }
        
        .office-name {
            font-size: 11pt;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .report-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 12px;
            color: #1a5f1a;
        }
        
        /* Info Row */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        
        .info-table td {
            border: none;
            padding: 8px 12px;
        }
        
        /* Main Table */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
            table-layout: fixed;
            page-break-inside: auto;
            border: 2px solid #000;
        }
        
        table.report-table th,
        table.report-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            page-break-inside: avoid;
        }
        
        table.report-table th {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 9pt;
            white-space: nowrap;
            page-break-after: avoid;
            color: #000;
        }
        
        table.report-table thead {
            display: table-header-group;
        }
        
        table.report-table thead tr:first-child th {
            border-bottom: 2px solid #000;
        }
        
        table.report-table tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        table.report-table tbody tr:nth-child(even) {
            background: #f5f5f5;
        }
        
        table.report-table tbody tr:hover {
            background: #e8e8e8;
        }
        
        /* Prevent table from breaking across pages if possible */
        .table-container {
            page-break-inside: avoid;
            margin-bottom: 25px;
        }
        
        /* Section headers in table */
        .section-header {
            background: #d4a574 !important;
            font-weight: bold;
            text-align: center !important;
        }
        
        .sub-header {
            background: #e8d4b8 !important;
        }

        /* Keep every Republic Act heading readable within its table column. */
        table.report-table th.sub-header {
            padding: 5px 3px;
            font-size: 7.5pt;
            line-height: 1.15;
            white-space: normal;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
        
        /* Totals row */
        .totals-row {
            font-weight: bold;
            background: #f0f0f0;
        }
        
        /* Signature Section */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: separate;
            border-spacing: 20px;
        }
        
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            border: none;
            padding: 20px 15px;
        }
        
        .sig-label {
            font-size: 10pt;
            margin-bottom: 50px;
            font-weight: bold;
            color: #333;
        }
        
        .sig-line {
            border-bottom: 2px solid #000;
            width: 200px;
            margin: 0 auto 10px auto;
        }
        
        .sig-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: #000;
        }
        
        .sig-title {
            font-size: 10pt;
            font-style: italic;
            color: #555;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 8mm;
            left: 20mm;
            right: 20mm;
            font-size: 7pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $silangLogoPath = public_path('images/silang.png');
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $silangLogoBase64 = file_exists($silangLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($silangLogoPath)) : '';
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
@endphp
    <div class="page-content">
    <!-- Header -->
    <table class="header-table" data-print-locked>
        <tr>
            <td style="width: 50px; text-align: center; vertical-align: middle;">
                @if($mswdoLogoBase64)
                    <img src="{{ $mswdoLogoBase64 }}" style="width: 48px; height: 48px;" alt="MSWDO Logo">
                @endif
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <div class="republic">REPUBLIC OF THE PHILIPPINES</div>
                <div class="municipality">MUNICIPALITY OF SILANG</div>
                <div class="province">Province of Cavite</div>
                <div class="office-name">{{ !empty($reportData['is_bcpc_report']) ? 'BARANGAY COUNCIL FOR THE PROTECTION OF CHILDREN (BCPC)' : 'SOCIAL WELFARE AND DEVELOPMENT (SWD)' }}</div>
                <div class="report-title">QUARTERLY COMPLIANCE MONITORING REPORT</div>
            </td>
            <td style="width: 50px; text-align: center; vertical-align: middle;">
                @if($silangLogoBase64)
                    <img src="{{ $silangLogoBase64 }}" style="width: 48px; height: 48px;" alt="Silang Logo">
                @endif
            </td>
        </tr>
    </table>
    
    <!-- Info Row -->
    <table class="info-table" data-print-locked>
        <tr>
            <td style="text-align: left;">
                <strong>Municipality:</strong> Silang, Cavite<br>
                <strong>Barangay:</strong> {{ $barangay->name }}
            </td>
            <td style="text-align: right;">
                <strong>Period Covered:</strong> {{ $period }} ({{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }})<br>
                <strong>Region IV-A</strong>
            </td>
        </tr>
    </table>
    
    @php
        // Build the list of abuse type columns (descriptive names from case_types)
        $abuseTypeColumns = collect($caseTypes)->map(function($name, $ra) {
            return ['ra' => $ra, 'name' => $name];
        })->values()->toArray();
        
        $cases = $reportData['cases'] ?? [];
        $statusLabel = fn ($status) => strcasecmp(trim((string) $status), 'Case closed') === 0 ? 'Case Closed' : trim((string) $status);
        $allStatuses = collect(['Case Filed', 'Case Referred', 'Case Closed'])
            ->merge(collect($cases)->pluck('status')->filter()->map($statusLabel))
            ->unique()->values()->all();
        $abuseTypeColCount = count($abuseTypeColumns);
        $statusColCount = count($allStatuses);
        $totalCols = 1 + $abuseTypeColCount + $statusColCount;
    @endphp
    
    <!-- Report Table -->
    <div class="table-container">
        <table class="report-table" data-quarterly-report-table>
            <thead data-print-locked>
            <tr>
                <th rowspan="2" style="background: #d4a574; width: 18%; text-align: left;">NAME</th>
                <th colspan="{{ $abuseTypeColCount }}" class="section-header">{{ !empty($reportData['is_bcpc_report']) ? 'BCPC' : 'SWD' }} CASES</th>
                <th colspan="{{ $statusColCount }}" class="section-header">STATUS</th>
            </tr>
            <tr>
                @foreach($abuseTypeColumns as $col)
                    <th class="sub-header">{{ $col['ra'] }}</th>
                @endforeach
                @foreach($allStatuses as $status)
                    <th class="sub-header">{{ str_replace('Case ', '', $status) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $abuseTypeTotals = array_fill(0, $abuseTypeColCount, 0);
                $statusTotals = array_fill(0, $statusColCount, 0);
            @endphp
            
            @forelse($cases as $case)
            @php
                $abuseTypeMatches = [];
                foreach ($abuseTypeColumns as $i => $col) {
                    $match = ($case['abuse_type'] ?? '') === $col['ra'] ? 1 : 0;
                    $abuseTypeMatches[] = $match;
                    $abuseTypeTotals[$i] += $match;
                }
                
                $statusMatches = [];
                foreach ($allStatuses as $i => $status) {
                    $match = $statusLabel($case['status'] ?? '') === $status ? 1 : 0;
                    $statusMatches[] = $match;
                    $statusTotals[$i] += $match;
                }
            @endphp
            <tr data-quarterly-case-row>
                <td style="text-align: left;">{{ $case['victim_name'] ?? 'N/A' }}</td>
                @foreach($abuseTypeMatches as $match)
                    <td>{{ $match ? '/' : '' }}</td>
                @endforeach
                @foreach($statusMatches as $match)
                    <td>{{ $match ? '/' : '' }}</td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ $totalCols }}" style="text-align: center; padding: 20px; color: #666;">
                    No cases reported for this quarter.
                </td>
            </tr>
            @endforelse
            
            @if(count($cases) > 0)
            <tr class="totals-row" data-quarterly-totals-row>
                <td style="text-align: right; font-weight: bold;">TOTAL:</td>
                @foreach($abuseTypeTotals as $total)
                    <td data-quarterly-total>{{ $total }}</td>
                @endforeach
                @foreach($statusTotals as $total)
                    <td data-quarterly-total>{{ $total }}</td>
                @endforeach
            </tr>
            @endif
        </tbody>
    </table>
    </div>
    
    <!-- Signature Section -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-label" data-print-locked>Accomplished by:</div>
                <div class="sig-line"></div>
                <div class="sig-name" data-print-locked>{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</div>
                <div class="sig-title">{{ !empty($reportData['is_bcpc_report']) ? (($user?->isBcpcAdmin() ?? false) ? 'BCPC Administrator' : 'BCPC Desk Officer') : (($user?->isVawcAdmin() ?? false) ? 'SWD Administrator' : 'SWD Desk Officer') }}</div>
            </td>
            <td>
                <div class="sig-label" data-print-locked>Noted by:</div>
                <div class="sig-line"></div>
                <div class="sig-name">&nbsp;</div>
                <div class="sig-title">Municipal Mayor</div>
            </td>
            <td>
                <div class="sig-label" data-print-locked>Recorded by:</div>
                <div class="sig-line"></div>
                <div class="sig-name">&nbsp;</div>
                <div class="sig-title">Municipal Secretary</div>
            </td>
        </tr>
    </table>
<div class="footer">Quarterly Compliance Monitoring Report | {{ !empty($reportData['is_bcpc_report']) ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System</div>
    </div>
@if($printEditControls ?? true)
    @include('print.edit-controls')
    <script>
        (() => {
            const reportTable = document.querySelector('[data-quarterly-report-table]');
            if (!reportTable) return;

            const refreshTotals = () => {
                const rows = [...reportTable.querySelectorAll('[data-quarterly-case-row]')];
                reportTable.querySelectorAll('[data-quarterly-total]').forEach((totalCell) => {
                    const columnIndex = totalCell.cellIndex;
                    totalCell.textContent = rows.reduce((total, row) => {
                        const cell = row.cells[columnIndex];
                        return total + (cell?.textContent.trim() ? 1 : 0);
                    }, 0);
                });
            };

            reportTable.addEventListener('input', (event) => {
                if (event.target.closest('[data-quarterly-case-row] td')) refreshTotals();
            });
        })();
    </script>
@endif
</body>
</html>
