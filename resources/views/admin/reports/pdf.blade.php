<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>MSWDO Official Report – {{ $serviceTitle }}</title>
    <style>
        @page {
            margin: 12mm 14mm 14mm 14mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #0F172A;
            margin: 0;
            padding: 0;
            line-height: 1.35;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px solid #1A237E;
            padding-bottom: 8px;
            margin-bottom: 12px;
            position: relative;
        }
        .header .republic {
            font-size: 8.5px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 2px;
        }
        .header h1 {
            color: #1A237E;
            margin: 2px 0;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.2px;
        }
        .header .sub-header {
            font-size: 11.5px;
            color: #1E3A8A;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 2px 0 3px 0;
        }
        .header .meta {
            color: #64748B;
            font-size: 9px;
            margin: 0;
        }
        .meta-pill-bar {
            background: #F1F5F9;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 5px 8px;
            margin-bottom: 12px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            font-size: 9px;
            padding: 2px 3px;
            border: none;
        }
        .meta-label {
            color: #1A237E;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5px;
        }

        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #1A237E;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1.5px solid #CBD5E1;
            padding-bottom: 3px;
            margin: 10px 0 6px 0;
        }

        /* Summary KPIs */
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .summary-grid td {
            border: 1px solid #CBD5E1;
            padding: 6px 8px;
            text-align: center;
            background: #FFFFFF;
        }
        .summary-num {
            font-size: 14px;
            font-weight: bold;
            color: #1A237E;
            margin-bottom: 2px;
        }
        .summary-lbl {
            font-size: 8px;
            text-transform: uppercase;
            color: #475569;
            font-weight: bold;
            letter-spacing: 0.2px;
        }

        /* Two columns */
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
            padding-right: 6px;
            width: 50%;
        }
        .two-col td:last-child {
            padding-left: 6px;
            width: 50%;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }
        .data-table th {
            background: #1A237E;
            color: #FFFFFF;
            padding: 4px 6px;
            font-size: 8.5px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: left;
            border: 1px solid #1A237E;
        }
        .data-table td {
            padding: 3.5px 6px;
            border: 1px solid #E2E8F0;
            color: #1E293B;
        }
        .data-table tbody tr:nth-child(even) {
            background: #F8FAFC;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Signatory Block */
        .signatory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .signatory-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .sig-col-left {
            width: 50%;
            padding-right: 25px;
            text-align: left;
        }
        .sig-col-right {
            width: 50%;
            padding-left: 25px;
            text-align: left;
        }
        .sig-header {
            font-size: 9.5px;
            font-weight: bold;
            color: #334155;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 0;
        }
        .sig-spacer {
            height: 38px;
        }
        .sig-inner-table {
            width: 240px;
            border-collapse: collapse;
        }
        .sig-name-cell {
            border-bottom: 1.5px solid #0F172A !important;
            padding-bottom: 2px !important;
        }
        .sig-name {
            font-size: 11px;
            font-weight: bold;
            color: #0F172A;
            display: block;
        }
        .sig-meta-cell {
            padding-top: 3px !important;
        }
        .sig-title {
            font-size: 9px;
            color: #1E293B;
            font-weight: bold;
            line-height: 1.35;
        }
        .sig-office {
            font-size: 8.5px;
            color: #64748B;
            line-height: 1.35;
        }

        .footer {
            margin-top: 10px;
            border-top: 1px solid #CBD5E1;
            padding-top: 5px;
            font-size: 8px;
            color: #64748B;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="republic">Republic of the Philippines &bull; Province of Cavite &bull; Municipality of Silang</div>
        <h1>MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</h1>
        <div class="sub-header">{{ $serviceTitle }} – Official {{ ucfirst($range['period']) }} Report</div>
        <div class="meta">Coverage: {{ $range['label'] }} ({{ $range['formattedRange'] }}) &bull; Generated: {{ $generatedAt }}</div>
    </div>

    <!-- Metadata Strip -->
    <div class="meta-pill-bar">
        <table class="meta-table">
            <tr>
                <td><span class="meta-label">Reporting Interval:</span> {{ ucfirst($range['period']) }} ({{ $range['label'] }})</td>
                <td><span class="meta-label">Date Range:</span> {{ $range['formattedRange'] }}</td>
                <td><span class="meta-label">Officer In Charge:</span> {{ $generatedBy ?: 'Fred Calos' }}</td>
                <td class="text-right"><span class="meta-label">Classification:</span> Official Public Welfare Record</td>
            </tr>
        </table>
    </div>

    <!-- Executive KPI Grid -->
    <div class="section-title">1. Executive Overview &amp; Output Indicators</div>
    <table class="summary-grid">
        <tr>
            <td style="background: #EEF2FF;">
                <div class="summary-num">{{ number_format($stats['totalVolume'] ?? 0) }}</div>
                <div class="summary-lbl">Total Volume / Beneficiaries</div>
            </td>
            @if(isset($stats['pendingVolume']))
            <td style="background: #FEF3C7;">
                <div class="summary-num" style="color:#B45309;">{{ number_format($stats['pendingVolume']) }}</div>
                <div class="summary-lbl">Pending / In Review</div>
            </td>
            @endif
            @if(isset($stats['completedVolume']))
            <td style="background: #ECFDF5;">
                <div class="summary-num" style="color:#047857;">{{ number_format($stats['completedVolume']) }}</div>
                <div class="summary-lbl">Completed / Released</div>
            </td>
            @endif
            @if(isset($stats['activeSeniors']))
            <td style="background: #ECFDF5;">
                <div class="summary-num" style="color:#047857;">{{ number_format($stats['activeSeniors']) }}</div>
                <div class="summary-lbl">Active Senior Masterlist</div>
            </td>
            @endif
            @if(isset($stats['payoutBeneficiaries']) && $stats['payoutBeneficiaries'] > 0)
            <td style="background: #EFF6FF;">
                <div class="summary-num" style="color:#1D4ED8;">{{ number_format($stats['payoutBeneficiaries']) }}</div>
                <div class="summary-lbl">Birthday Cash Recipients</div>
            </td>
            @endif
            <td style="background: #F0FDF4;">
                <div class="summary-num" style="color:#15803D;">&#8369;{{ number_format($stats['totalAmount'] ?? 0, 2) }}</div>
                <div class="summary-lbl">Total Financial Disbursed</div>
            </td>
        </tr>
    </table>

    <!-- Category Breakdown & Barangay Breakdown side-by-side -->
    <div class="section-title">2. Categorical &amp; Demographic Breakdown</div>
    <table class="two-col">
        <tr>
            <td>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Program Category / Classification</th>
                            <th class="text-right" style="width: 25%;">Volume</th>
                            <th class="text-right" style="width: 25%;">Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $catTotal = array_sum($breakdowns['categories'] ?? []) ?: 1; 
                        @endphp
                        @forelse($breakdowns['categories'] ?? [] as $category => $count)
                        <tr>
                            <td><strong>{{ $category }}</strong></td>
                            <td class="text-right">{{ number_format($count) }}</td>
                            <td class="text-right">{{ number_format(($count / $catTotal) * 100, 1) }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center" style="color:#94A3B8;">No categorical records logged in this interval.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Top Barangays</th>
                            <th class="text-right" style="width: 35%;">Served</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($breakdowns['barangays'] ?? [] as $brgy => $count)
                        <tr>
                            <td>{{ $brgy }}</td>
                            <td class="text-right"><strong>{{ number_format($count) }}</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center" style="color:#94A3B8;">No barangay distribution recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Detailed Casework / Registration Roster -->
    <div class="section-title">3. Casework &amp; Client Registry (Sample Listing)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 16%;">Control / Ref No.</th>
                <th style="width: 26%;">Client / Beneficiary Name</th>
                <th style="width: 18%;">Barangay</th>
                <th style="width: 18%;">Service / Classification</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 12%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $rec)
            <tr>
                <td><strong>{{ $rec['ref'] }}</strong></td>
                <td>{{ $rec['name'] }}</td>
                <td>{{ $rec['barangay'] }}</td>
                <td>{{ $rec['category'] }}</td>
                <td>{{ $rec['status'] }}</td>
                <td class="text-right">{{ $rec['amount'] > 0 ? '&#8369;' . number_format($rec['amount'], 2) : '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color:#94A3B8; padding: 12px;">No individual records matched the selected period criteria.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signatory Section -->
    <table class="signatory-table">
        <tr>
            <td class="sig-col-left">
                <div class="sig-header">PREPARED BY:</div>
                <div class="sig-spacer"></div>
                <table class="sig-inner-table">
                    <tr>
                        <td class="sig-name-cell">
                            <span class="sig-name">{{ $generatedBy ?: 'Fred Calos' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-meta-cell">
                            <div class="sig-title">System Administrator / Case Encoder</div>
                            <div class="sig-office">MSWDO Silang, Cavite</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="sig-col-right">
                <div class="sig-header">APPROVED BY:</div>
                <div class="sig-spacer"></div>
                <table class="sig-inner-table">
                    <tr>
                        <td class="sig-name-cell">
                            <span class="sig-name">MSWDO HEAD OFFICER</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-meta-cell">
                            <div class="sig-title">Municipal Social Welfare &amp; Development Officer</div>
                            <div class="sig-office">LGU Silang, Cavite</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        This document is system-generated by the iSERVE SILANG MSWDO Portal on {{ $generatedAt }}. For official verification, consult the MSWDO Records Section.
    </div>
</body>
</html>
