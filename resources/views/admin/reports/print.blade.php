<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Report – {{ $serviceTitle }} ({{ $range['label'] }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Public Sans', sans-serif;
            background: #F8FAFC;
            color: #0F172A;
            margin: 0;
            padding: 24px;
            font-size: 13px;
            line-height: 1.4;
        }
        .report-sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
            padding: 36px 42px;
            border: 1px solid #E2E8F0;
        }
        .header-bar {
            text-align: center;
            border-bottom: 2.5px solid #1A237E;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .header-bar .republic {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748B;
            font-weight: 600;
        }
        .header-bar h1 {
            color: #1A237E;
            font-size: 20px;
            font-weight: 800;
            margin: 4px 0 2px 0;
            letter-spacing: -0.01em;
        }
        .header-bar .sub {
            font-size: 14px;
            color: #1E3A8A;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .header-bar .date-meta {
            font-size: 12px;
            color: #64748B;
            margin-top: 4px;
        }
        .meta-strip {
            background: #F1F5F9;
            border-radius: 8px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .meta-strip strong { color: #1A237E; }
        .section-header {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1A237E;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #E2E8F0;
            padding-bottom: 6px;
            margin: 22px 0 12px 0;
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .kpi-tile {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 14px;
            text-align: center;
        }
        .kpi-tile .kpi-num {
            font-size: 22px;
            font-weight: 800;
            color: #1A237E;
            line-height: 1.1;
        }
        .kpi-tile .kpi-lbl {
            font-size: 11px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 16px;
        }
        .table-custom th {
            background: #1A237E;
            color: #FFFFFF;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            padding: 8px 10px;
            text-align: left;
        }
        .table-custom td {
            padding: 7px 10px;
            border-bottom: 1px solid #E2E8F0;
            color: #1E293B;
        }
        .table-custom tr:nth-child(even) td {
            background: #F8FAFC;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        .signatories {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-top: 40px;
            padding-top: 10px;
            page-break-inside: avoid;
        }
        .sig-col {
            flex: 1;
            max-width: 320px;
            text-align: left;
        }
        .sig-lbl {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 0;
        }
        .sig-space {
            height: 38px;
        }
        .sig-name-box {
            display: inline-block;
            min-width: 240px;
            border-bottom: 1.5px solid #0F172A;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }
        .sig-name {
            font-weight: 700;
            font-size: 13px;
            color: #0F172A;
        }
        .sig-title {
            font-size: 11.5px;
            font-weight: 600;
            color: #1E293B;
            line-height: 1.35;
        }
        .sig-office {
            font-size: 11px;
            color: #64748B;
            line-height: 1.35;
        }
        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .btn-action {
            background: #1A237E;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-action:hover { background: #121858; }
        .btn-secondary {
            background: #FFFFFF;
            color: #334155;
            border: 1px solid #CBD5E1;
        }
        .btn-secondary:hover { background: #F1F5F9; }

        @media print {
            body { background: #FFFFFF; padding: 0; }
            .report-sheet { border: none; box-shadow: none; padding: 0; }
            .no-print-bar { display: none !important; }
            @page { margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <a href="{{ route('admin.dashboard') }}" class="btn-action btn-secondary">
            &larr; Back to Dashboard
        </a>
        <div style="display:flex; gap:8px;">
            <button type="button" onclick="window.print()" class="btn-action">
                Print Report
            </button>
            <a href="{{ route('admin.dashboard.reports.pdf', request()->all()) }}" class="btn-action">
                Download PDF
            </a>
        </div>
    </div>

    <div class="report-sheet">
        <div class="header-bar">
            <div class="republic">Republic of the Philippines &bull; Province of Cavite &bull; Municipality of Silang</div>
            <h1>MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</h1>
            <div class="sub">{{ $serviceTitle }} – Official {{ ucfirst($range['period']) }} Report</div>
            <div class="date-meta">Coverage: {{ $range['label'] }} ({{ $range['formattedRange'] }}) &bull; Generated: {{ $generatedAt }}</div>
        </div>

        <div class="meta-strip">
            <div><strong>Interval:</strong> {{ ucfirst($range['period']) }} ({{ $range['label'] }})</div>
            <div><strong>Date Range:</strong> {{ $range['formattedRange'] }}</div>
            <div><strong>Prepared By:</strong> {{ $generatedBy }}</div>
            <div><strong>Office:</strong> MSWDO Silang, Cavite</div>
        </div>

        <div class="section-header">1. Executive Summary &amp; Output Indicators</div>
        <div class="kpi-grid">
            <div class="kpi-tile">
                <div class="kpi-num">{{ number_format($stats['totalVolume'] ?? 0) }}</div>
                <div class="kpi-lbl">Total Volume / Beneficiaries</div>
            </div>
            @if(isset($stats['pendingVolume']))
            <div class="kpi-tile">
                <div class="kpi-num" style="color:#B45309;">{{ number_format($stats['pendingVolume']) }}</div>
                <div class="kpi-lbl">Pending Review</div>
            </div>
            @endif
            @if(isset($stats['completedVolume']))
            <div class="kpi-tile">
                <div class="kpi-num" style="color:#059669;">{{ number_format($stats['completedVolume']) }}</div>
                <div class="kpi-lbl">Completed / Released</div>
            </div>
            @endif
            @if(isset($stats['activeSeniors']))
            <div class="kpi-tile">
                <div class="kpi-num" style="color:#059669;">{{ number_format($stats['activeSeniors']) }}</div>
                <div class="kpi-lbl">Active Seniors</div>
            </div>
            @endif
            <div class="kpi-tile">
                <div class="kpi-num" style="color:#15803D;">&#8369;{{ number_format($stats['totalAmount'] ?? 0, 2) }}</div>
                <div class="kpi-lbl">Total Financial Disbursed</div>
            </div>
        </div>

        <div class="section-header">2. Categorical &amp; Demographic Breakdown</div>
        <div class="two-columns">
            <div>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Program Classification</th>
                            <th style="text-align:right;">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($breakdowns['categories'] ?? [] as $cat => $cnt)
                        <tr>
                            <td><strong>{{ $cat }}</strong></td>
                            <td style="text-align:right;">{{ number_format($cnt) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="text-align:center; color:#94A3B8;">No categorical data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Top Barangays</th>
                            <th style="text-align:right;">Served</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($breakdowns['barangays'] ?? [] as $b => $cnt)
                        <tr>
                            <td>{{ $b }}</td>
                            <td style="text-align:right;"><strong>{{ number_format($cnt) }}</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="text-align:center; color:#94A3B8;">No barangay distribution available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-header">3. Casework &amp; Client Registry</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Ref No.</th>
                    <th>Beneficiary Name</th>
                    <th>Barangay</th>
                    <th>Classification / Purpose</th>
                    <th>Status</th>
                    <th style="text-align:right;">Amount</th>
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
                    <td style="text-align:right;">{{ $rec['amount'] > 0 ? '&#8369;' . number_format($rec['amount'], 2) : '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#94A3B8; padding:16px;">No client records found for this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="signatories">
            <div class="sig-col">
                <div class="sig-lbl">PREPARED BY:</div>
                <div class="sig-space"></div>
                <div class="sig-name-box">
                    <span class="sig-name">{{ $generatedBy ?: 'Fred Calos' }}</span>
                </div>
                <div class="sig-title">System Administrator / Case Encoder</div>
                <div class="sig-office">MSWDO Silang, Cavite</div>
            </div>
            <div class="sig-col">
                <div class="sig-lbl">APPROVED BY:</div>
                <div class="sig-space"></div>
                <div class="sig-name-box">
                    <span class="sig-name">MSWDO HEAD OFFICER</span>
                </div>
                <div class="sig-title">Municipal Social Welfare &amp; Development Officer</div>
                <div class="sig-office">LGU Silang, Cavite</div>
            </div>
        </div>
    </div>
</body>
</html>
