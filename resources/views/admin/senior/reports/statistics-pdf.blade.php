<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Senior Citizen Statistics Report</title>
    <style>
        @page {
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 0;
            line-height: 1.35;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px solid #1A237E;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header .sub {
            font-size: 9.5px;
            color: #4B5563;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .header h1 {
            color: #1A237E;
            margin: 2px 0 3px 0;
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.2px;
        }
        .header .meta {
            color: #6B7280;
            font-size: 10px;
            margin: 0;
        }
        .filter-info {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 7px 12px;
            margin-bottom: 14px;
        }
        .filter-table {
            width: 100%;
            border-collapse: collapse;
        }
        .filter-table td {
            font-size: 10.5px;
            padding: 2px 4px;
            border: none;
        }
        .filter-label {
            color: #1A237E;
            font-weight: bold;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1A237E;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1.5px solid #CBD5E1;
            padding-bottom: 3px;
            margin: 12px 0 8px 0;
        }
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .summary-grid td {
            border: 1px solid #CBD5E1;
            padding: 8px 10px;
            text-align: center;
            background: #FFFFFF;
        }
        .summary-num {
            font-size: 16px;
            font-weight: bold;
            color: #1A237E;
            margin-bottom: 2px;
        }
        .summary-lbl {
            font-size: 9px;
            text-transform: uppercase;
            color: #475569;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10.5px;
        }
        .data-table th {
            background: #1A237E;
            color: #FFFFFF;
            padding: 5px 8px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: left;
            border: 1px solid #1A237E;
        }
        .data-table td {
            padding: 4.5px 8px;
            border: 1px solid #E2E8F0;
            color: #1F2937;
        }
        .data-table tbody tr:nth-child(even) {
            background: #F8FAFC;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .two-col {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .two-col td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .two-col td:first-child {
            padding-right: 8px;
            width: 50%;
        }
        .two-col td:last-child {
            padding-left: 8px;
            width: 50%;
        }
        .footer {
            margin-top: 15px;
            border-top: 1px solid #E2E8F0;
            padding-top: 8px;
            font-size: 9.5px;
            color: #64748B;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="sub">Republic of the Philippines &bull; Province of Cavite &bull; Municipality of Silang</div>
        <h1>MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</h1>
        <div class="meta">Senior Citizen Statistics &amp; Program Demographics Report &bull; Generated: {{ date('F j, Y h:i A') }}</div>
    </div>

    <!-- Active Filters Summary -->
    <div class="filter-info">
        <table class="filter-table">
            <tr>
                <td><span class="filter-label">Year:</span> {{ !empty($year) ? $year : 'All' }}</td>
                <td><span class="filter-label">Month:</span> {{ !empty($month) && is_numeric($month) ? date('F', mktime(0, 0, 0, (int)$month, 1)) : 'All' }}</td>
                <td><span class="filter-label">Barangay:</span> {{ !empty($barangay) ? $barangay : 'All' }}</td>
                <td><span class="filter-label">Gender:</span> {{ !empty($gender) ? $gender : 'All' }}</td>
                <td><span class="filter-label">Age Group:</span> {{ !empty($ageGroup) ? $ageGroup : 'All' }}</td>
            </tr>
        </table>
    </div>

    <!-- Key Metrics Summary Cards -->
    <div class="section-title">Executive Summary</div>
    <table class="summary-grid">
        <tr>
            <td style="width: 20%; background: #EEF2FF;">
                <div class="summary-num">{{ number_format($totalSeniors) }}</div>
                <div class="summary-lbl">Total Matching</div>
            </td>
            <td style="width: 20%; background: #F0FDF4;">
                <div class="summary-num" style="color: #16A34A;">{{ number_format($activeSeniors) }}</div>
                <div class="summary-lbl">Active Seniors</div>
            </td>
            <td style="width: 20%; background: #FEF2F2;">
                <div class="summary-num" style="color: #DC2626;">{{ number_format($inactiveSeniors) }}</div>
                <div class="summary-lbl">Inactive / Pending</div>
            </td>
            <td style="width: 20%; background: #EFF6FF;">
                <div class="summary-num" style="color: #2563EB;">{{ number_format($maleCount) }}</div>
                <div class="summary-lbl">Male Seniors</div>
            </td>
            <td style="width: 20%; background: #FDF2F8;">
                <div class="summary-num" style="color: #DB2777;">{{ number_format($femaleCount) }}</div>
                <div class="summary-lbl">Female Seniors</div>
            </td>
        </tr>
    </table>

    <!-- Gender & Age Distribution Side by Side -->
    <table class="two-col">
        <tr>
            <td>
                <div class="section-title">Gender Breakdown</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Gender</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Share (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Male</strong></td>
                            <td class="text-right">{{ number_format($maleCount) }}</td>
                            <td class="text-right">{{ $totalSeniors > 0 ? number_format(($maleCount / $totalSeniors) * 100, 1) : '0.0' }}%</td>
                        </tr>
                        <tr>
                            <td><strong>Female</strong></td>
                            <td class="text-right">{{ number_format($femaleCount) }}</td>
                            <td class="text-right">{{ $totalSeniors > 0 ? number_format(($femaleCount / $totalSeniors) * 100, 1) : '0.0' }}%</td>
                        </tr>
                        <tr style="background: #E2E8F0; font-weight: bold;">
                            <td>Total</td>
                            <td class="text-right">{{ number_format($maleCount + $femaleCount) }}</td>
                            <td class="text-right">100.0%</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <div class="section-title">Age Group Distribution</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Age Group</th>
                            <th class="text-right">Count</th>
                            <th class="text-right">Share (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $ageTotal = 0; @endphp
                        @forelse($ageGroups as $ag)
                            @php $ageTotal += $ag->total; @endphp
                            <tr>
                                <td><strong>{{ $ag->age_group ?? 'Unknown' }}</strong></td>
                                <td class="text-right">{{ number_format($ag->total) }}</td>
                                <td class="text-right">{{ $totalSeniors > 0 ? number_format(($ag->total / $totalSeniors) * 100, 1) : '0.0' }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center" style="color: #64748B;">No age records found.</td>
                            </tr>
                        @endforelse
                        <tr style="background: #E2E8F0; font-weight: bold;">
                            <td>Total Classified</td>
                            <td class="text-right">{{ number_format($ageTotal) }}</td>
                            <td class="text-right">100.0%</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Barangay Distribution (2-column layout to save space and fit nicely) -->
    <div class="section-title">Barangay Distribution Summary</div>
    @php
        $barangayCount = $barangayStats->count();
        $halfCount = (int) ceil($barangayCount / 2);
        $col1 = $barangayStats->slice(0, $halfCount);
        $col2 = $barangayStats->slice($halfCount);
    @endphp

    <table class="two-col">
        <tr>
            <td>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Barangay</th>
                            <th class="text-right">Seniors</th>
                            <th class="text-right">Share (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($col1 as $index => $item)
                        <tr>
                            <td class="text-center" style="color: #64748B; width: 22px;">{{ $index + 1 }}</td>
                            <td>{{ $item['barangay'] }}</td>
                            <td class="text-right font-bold">{{ number_format($item['total']) }}</td>
                            <td class="text-right" style="color: #475569;">{{ $totalSeniors > 0 ? number_format(($item['total'] / $totalSeniors) * 100, 1) : '0.0' }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Barangay</th>
                            <th class="text-right">Seniors</th>
                            <th class="text-right">Share (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($col2 as $index => $item)
                        <tr>
                            <td class="text-center" style="color: #64748B; width: 22px;">{{ $halfCount + $loop->iteration }}</td>
                            <td>{{ $item['barangay'] }}</td>
                            <td class="text-right font-bold">{{ number_format($item['total']) }}</td>
                            <td class="text-right" style="color: #475569;">{{ $totalSeniors > 0 ? number_format(($item['total'] / $totalSeniors) * 100, 1) : '0.0' }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        This document was automatically generated by the MSWDO Senior Citizen Information System. Confidential document for official use only.
    </div>
</body>
</html>