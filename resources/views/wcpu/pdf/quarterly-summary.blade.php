<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quarterly Summary Report - Q{{ $quarter }} {{ $year }}</title>
    <style>
        @page { 
            size: legal landscape; 
            margin: 12mm 20mm;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 10pt; 
            line-height: 1.5; 
            color: #222; 
        }
        
        table { border-collapse: collapse; }
        .page-content { width: 92%; max-width: 290mm; margin: 0 auto; }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .header-logos {
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 5px;
        }
        
        .header-logos td {
            border: none;
            vertical-align: middle;
        }
        
        .header-line-1 {
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #444;
        }
        
        .header-line-2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
        }
        
        .header-line-3 {
            font-size: 9pt;
            font-style: italic;
            color: #555;
        }
        
        .header-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
            letter-spacing: 0.5px;
        }
        
        .header-subtitle {
            font-size: 9.5pt;
            color: #444;
            margin-top: 2px;
        }
        
        /* Summary row */
        .summary-row {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        
        .summary-row td {
            border: 1px solid #aaa;
            padding: 5px 10px;
            background: #fafafa;
        }
        
        .summary-label {
            font-weight: bold;
            background: #f0f0f0 !important;
        }
        
        /* Main table */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        
        table.report-table th {
            background: #f5f5f5;
            border: 1px solid #999;
            padding: 5px 6px;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            font-size: 8pt;
        }
        
        table.report-table td {
            border: 1px solid #bbb;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
        }
        
        table.report-table tbody tr:nth-child(even) {
            background: #fafafa;
        }
        
        .col-header-type {
            background: #f0e6d6 !important;
        }
        
        .col-header-status {
            background: #d6ecd6 !important;
        }
        
        .col-sub-type {
            background: #faf4eb !important;
            font-size: 7.5pt;
        }
        
        .col-sub-status {
            background: #ebf5eb !important;
            font-size: 7.5pt;
        }
        
        .totals-row td {
            font-weight: bold;
            background: #eee !important;
            border-top: 2px solid #666;
        }
        
        .barangay-name {
            text-align: left !important;
        }
        
        .check { color: #2d7a2d; }
        .pending { color: #b37700; }
        
        /* Signatures - inline with content, no page break */
        .signatures {
            width: 100%;
            margin-top: 15px;
        }
        
        .signatures td {
            border: none;
            vertical-align: top;
            padding: 8px 15px;
            width: 50%;
        }
        
        .sig-label {
            font-size: 8.5pt;
            color: #555;
            margin-bottom: 30px;
        }
        
        .sig-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin-bottom: 4px;
        }
        
        .sig-title {
            font-size: 9pt;
            font-weight: bold;
        }

        .sig-position {
            font-size: 8pt;
            font-style: italic;
            color: #555;
        }
        
        .footer-text {
            text-align: center;
            font-size: 7pt;
            color: #aaa;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 4px;
        }
    </style>
</head>
<body>
@php
    $isBcpcReport = strtoupper((string) ($reportType ?? 'VAWC')) === 'BCPC';
    $silangLogoPath = public_path('images/silang.png');
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $silangLogoBase64 = file_exists($silangLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($silangLogoPath)) : '';
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
@endphp
    <div class="page-content">
    <!-- Header -->
    <div class="header">
        <table class="header-logos">
            <tr>
                <td style="width: 50px; text-align: center; border: none;">
                    @if($mswdoLogoBase64)
                        <img src="{{ $mswdoLogoBase64 }}" style="width: 52px; height: 52px;" alt="MSWDO Logo">
                    @endif
                </td>
                <td style="text-align: center; border: none;">
                    <div class="header-line-1">Republic of the Philippines</div>
                    <div class="header-line-2">Municipality of Silang</div>
                    <div class="header-line-3">Province of Cavite</div>
                </td>
                <td style="width: 50px; text-align: center; border: none;">
                    @if($silangLogoBase64)
                        <img src="{{ $silangLogoBase64 }}" style="width: 52px; height: 52px;" alt="Silang Logo">
                    @endif
                </td>
            </tr>
        </table>
        <div class="header-title">Consolidated Quarterly Compliance Monitoring Summary</div>
        <div class="header-subtitle">{{ $isBcpcReport ? 'BCPC' : 'R.A. 9262 (VAWAC)' }} &mdash; Q{{ $quarter }} {{ $year }} ({{ $startDate->format('F j') }} &ndash; {{ $endDate->format('F j, Y') }})</div>
    </div>
    
    <!-- Summary Stats Row -->
    <table class="summary-row">
        <tr>
            <td class="summary-label">Reports Submitted</td>
            <td>{{ $submittedCount }} out of {{ $totalBarangays }} barangays ({{ $totalBarangays > 0 ? round(($submittedCount / $totalBarangays) * 100) : 0 }}%)</td>
            <td class="summary-label">Total Cases</td>
            <td>{{ $grandTotals['total_cases'] }}</td>
            <td class="summary-label">Pending</td>
            <td>{{ $totalBarangays - $submittedCount }} barangays</td>
        </tr>
    </table>
    
    @php
        $abuseTypeColumns = collect($caseTypes)->map(function($name, $ra) {
            return ['ra' => $ra, 'name' => $name];
        })->values()->toArray();
        
        $abuseTypeColCount = count($abuseTypeColumns);
        $statusColCount = count($allStatuses);
    @endphp
    
    <!-- Data Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 16%; text-align: left; padding-left: 8px;">BARANGAY</th>
                <th rowspan="2" style="width: 6%;">STATUS</th>
                <th rowspan="2" style="width: 5%;">TOTAL</th>
                @if($abuseTypeColCount > 0)
                <th colspan="{{ $abuseTypeColCount }}" class="col-header-type">BY TYPE</th>
                @endif
                @if($statusColCount > 0)
                <th colspan="{{ $statusColCount }}" class="col-header-status">ACTION</th>
                @endif
            </tr>
            <tr>
                @foreach($abuseTypeColumns as $col)
                <th class="col-sub-type" title="{{ $col['name'] }}">{{ $col['ra'] }}</th>
                @endforeach
                @foreach($allStatuses as $status)
                <th class="col-sub-status">{{ str_replace('Case ', '', $status) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($barangaySummary as $row)
            <tr>
                <td class="barangay-name" style="padding-left: 8px;">{{ $row['barangay'] }}</td>
                <td>
                    @if($row['submitted'])
                    <span class="check">/</span>
                    @else
                    <span class="pending">&mdash;</span>
                    @endif
                </td>
                <td style="font-weight: bold;">{{ $row['total_cases'] ?: '' }}</td>
                @foreach($abuseTypeColumns as $col)
                <td>{{ $row['abuse_types'][$col['ra']] ?: '' }}</td>
                @endforeach
                @foreach($allStatuses as $status)
                <td>{{ $row['statuses'][$status] ?: '' }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td style="text-align: right; padding-right: 8px;" colspan="2">TOTAL</td>
                <td>{{ $grandTotals['total_cases'] }}</td>
                @foreach($abuseTypeColumns as $col)
                <td>{{ $grandTotals['abuse_types'][$col['ra']] ?: '' }}</td>
                @endforeach
                @foreach($allStatuses as $status)
                <td>{{ $grandTotals['statuses'][$status] ?: '' }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>
    
    <!-- Signatures - kept together with the table -->
    <table class="signatures">
        <tr>
            <td>
                <div class="sig-label">Prepared by:</div>
                <div class="sig-line"></div>
                <div class="sig-title">{{ $isBcpcReport ? 'BCPC Administrator' : 'WCPU Administrator' }}</div>
            </td>
            <td>
                <div class="sig-label">Noted by:</div>
                <div class="sig-line"></div>
                <div class="sig-position">{{ $isBcpcReport ? 'BCPC Desk Officer' : 'Municipal Social Welfare and Development Officer' }}</div>
            </td>
        </tr>
    </table>
    
    <div class="footer-text">
        Quarterly Summary | {{ $isBcpcReport ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System
    </div>
    </div>
@include('print.edit-controls')
</body>
</html>
