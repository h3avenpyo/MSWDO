<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizens Report</title>
    <style>
        @page {
            margin: 12mm 10mm 15mm 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 4px;
            table-layout: fixed;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .gov {
            text-align: center;
            line-height: 1.25;
            color: #222;
        }
        .gov .gov-line {
            font-size: 11px;
        }
        .gov h2 {
            margin: 5px 0 0;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: bold;
            color: #1A237E;
            letter-spacing: 0.5px;
        }
        .line {
            border-top: 2px solid #000;
            margin: 6px 0 2px;
        }
        .line2 {
            border-top: 1px solid #000;
            margin-bottom: 12px;
        }
        .report-title {
            text-align: center;
            margin: 8px 0 12px;
        }
        .report-title h3 {
            font-size: 14px;
            margin: 0;
            color: #1A237E;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 10px;
            table-layout: fixed;
        }
        .info-bar td {
            border: none;
            padding: 5px 10px;
            vertical-align: middle;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
            table-layout: fixed;
        }
        .data-table thead {
            display: table-header-group;
            background: #1A237E;
            color: white;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .data-table th {
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #1A237E;
            color: #ffffff;
            overflow: hidden;
            word-wrap: break-word;
        }
        .data-table td {
            padding: 5px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            overflow: hidden;
            word-wrap: break-word;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e1;
            color: #64748b;
            font-size: 8.5px;
        }
    </style>
</head>
<body>
    @php
        $silangLogo = '';
        $dswdLogo = '';
        if (file_exists(public_path('images/silang.png'))) {
            $silangLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/silang.png')));
        }
        if (file_exists(public_path('images/dswd.png'))) {
            $dswdLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/dswd.png')));
        }
        $chunks = $seniors->chunk(30);
        $totalPages = $chunks->count();
    @endphp

    @forelse($chunks as $pageIndex => $chunk)
        @if($pageIndex > 0)
            <div style="page-break-before: always;"></div>
        @endif

        @if($pageIndex === 0)
            <!-- Social Case Styled Header (First Page) -->
            <table class="header-table">
                <tr>
                    <td style="width: 70px; text-align: left;">
                        @if($silangLogo)
                            <img src="{{ $silangLogo }}" style="width: 55px; height: 55px; object-fit: contain;" alt="Silang Logo">
                        @endif
                    </td>
                    <td class="gov">
                        <div class="gov-line">Republic of the Philippines</div>
                        <div class="gov-line">Province of Cavite</div>
                        <div class="gov-line">Municipality of Silang</div>
                        <h2>MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT</h2>
                    </td>
                    <td style="width: 70px; text-align: right;">
                        @if($dswdLogo)
                            <img src="{{ $dswdLogo }}" style="width: 55px; height: 55px; object-fit: contain;" alt="DSWD Logo">
                        @endif
                    </td>
                </tr>
            </table>

            <div class="line"></div>
            <div class="line2"></div>

            <div class="report-title">
                <h3>Senior Citizens Masterlist Report</h3>
            </div>

            <table class="info-bar">
                <tr>
                    <td><strong>Date Generated:</strong> {{ $date }}</td>
                    <td style="text-align: right;">
                        <strong>Total Records:</strong> {{ number_format($total) }}
                        @if(isset($totalParts) && $totalParts > 1)
                            <span style="color:#1A237E;font-weight:bold;">&nbsp;(Part {{ $currentPart }} of {{ $totalParts }} &bull; Records {{ number_format($partStart) }}–{{ number_format($partEnd) }})</span>
                        @endif
                    </td>
                </tr>
                @if($barangay || $search)
                <tr>
                    @if($barangay)
                        <td><strong>Barangay:</strong> {{ $barangay }}</td>
                    @else
                        <td></td>
                    @endif
                    @if($search)
                        <td style="text-align: right;"><strong>Search:</strong> {{ $search }}</td>
                    @else
                        <td></td>
                    @endif
                </tr>
                @endif
            </table>
        @else
            <!-- Compact Running Header (Subsequent Pages) -->
            <table class="header-table" style="margin-bottom: 8px;">
                <tr>
                    <td style="text-align: left; font-size: 8.5px; color: #1A237E; font-weight: bold;">
                        MSWDO SILANG &bull; SENIOR CITIZENS MASTERLIST REPORT
                        @if(isset($totalParts) && $totalParts > 1)
                            (PART {{ $currentPart }} OF {{ $totalParts }})
                        @endif
                    </td>
                    <td style="text-align: right; font-size: 8.5px; color: #64748b;">
                        {{ $barangay ? 'Barangay: ' . $barangay . ' | ' : '' }}Page {{ $pageIndex + 1 }} of {{ $totalPages }}
                    </td>
                </tr>
            </table>
        @endif

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 14%;">Control Number</th>
                    <th style="width: 22%;">Full Name</th>
                    <th style="width: 24%;">Address</th>
                    <th style="width: 14%;">Barangay</th>
                    <th style="width: 10%;">Birth Date</th>
                    <th style="width: 5%;">Sex</th>
                    <th style="width: 4%;">Age</th>
                    <th style="width: 11%;">Contact Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $senior)
                <tr>
                    <td>{{ $senior->control_number ?? '-' }}</td>
                    <td><strong>{{ $senior->full_name ?? '-' }}</strong></td>
                    <td>{{ $senior->address ?? '-' }}</td>
                    <td>{{ $senior->barangay ?? '-' }}</td>
                    <td>{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') : '-' }}</td>
                    <td>{{ $senior->sex ?? '-' }}</td>
                    <td>{{ $senior->age ?? '-' }}</td>
                    <td>{{ $senior->contact_number ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($pageIndex === $totalPages - 1)
            <div class="footer">
                <p style="margin: 2px 0;">New Municipal Building, Barangay Biga I, Emilio Aguinaldo Highway, Silang, Cavite, Philippines</p>
                <p style="margin: 2px 0;">This document was generated automatically by the MSWDO Senior Citizen Management System on {{ now()->format('F d, Y g:i A') }}</p>
            </div>
        @endif
    @empty
        <p style="text-align:center; padding: 30px; color: #666;">No senior citizen records found matching the criteria.</p>
    @endforelse
</body>
</html>
