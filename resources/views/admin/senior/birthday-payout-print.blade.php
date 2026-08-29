<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birthday Payout List</title>
    <style>
        @page {
            size: A4;
            margin: 12mm 14mm 14mm 14mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        /* ── Header ── */
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
        .logo-cell {
            width: 72px;
            text-align: center;
            vertical-align: middle;
        }
        .gov-cell {
            text-align: center;
            vertical-align: middle;
            padding: 0 6px;
            line-height: 1.35;
        }
        .gov-meta {
            font-size: 9.5px;
            color: #374151;
            font-weight: 500;
        }
        .gov-name {
            font-size: 13px;
            font-weight: bold;
            color: #1A237E;
            margin: 3px 0 1px;
        }
        .gov-sub {
            font-size: 10px;
            color: #374151;
            margin: 0;
        }
        .divider-thick {
            border-top: 2.5px solid #1A237E;
            margin: 4px 0 1px;
        }
        .divider-thin {
            border-top: 1px solid #1A237E;
            margin-bottom: 6px;
        }
        /* ── Report Title ── */
        .report-title {
            text-align: center;
            margin: 0 0 6px;
        }
        .report-title h2 {
            font-size: 13px;
            font-weight: bold;
            color: #1A237E;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .report-title p {
            font-size: 11px;
            color: #374151;
            margin: 2px 0 0;
        }
        /* ── Info Bar ── */
        .info-bar {
            width: 100%;
            border-collapse: collapse;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            margin-bottom: 8px;
            table-layout: fixed;
        }
        .info-bar td {
            padding: 4px 8px;
            border: none;
            font-size: 10.5px;
            vertical-align: middle;
        }
        /* ── Data Table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            table-layout: fixed;
        }
        .data-table thead {
            display: table-header-group;
            background: #1A237E;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .data-table th {
            padding: 6px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #1A237E;
            color: #ffffff;
        }
        .data-table th.text-left { text-align: left; }
        .data-table td {
            padding: 6px 5px;
            border: 1px solid #94a3b8;
            text-align: center;
            vertical-align: middle;
            font-size: 10.5px;
        }
        .data-table td.text-left { text-align: left; }
        .data-table tbody tr:nth-child(even) { background: #f8fafc; }
        /* ── Section Header (barangay) ── */
        .barangay-header {
            background: #e8eaf6;
            border: 1px solid #1A237E;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 11px;
            color: #1A237E;
            margin: 10px 0 0;
        }
        /* ── Total Row ── */
        .total-row td {
            font-weight: bold;
            background: #e8eaf6;
            border-top: 2px solid #1A237E;
        }
        /* ── Signature Section ── */
        .signature-section {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 40px;
        }
        .signature-section td {
            border: none;
            text-align: center;
            width: 33%;
            padding: 0 10px;
            vertical-align: bottom;
        }
        .sig-line {
            border-top: 1.5px solid #0f172a;
            margin-top: 50px;
            margin-bottom: 4px;
        }
        .sig-name {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .sig-label {
            font-size: 10px;
            color: #374151;
        }
        /* ── Page break ── */
        .page-break { page-break-after: always; }
        .no-print { display: none !important; }
        @media screen {
            .no-print { display: block !important; }
            body { padding: 20px; background: #f3f4f6; }
        }
    </style>
</head>
<body>
    @php
        $silangLogo = '';
        $dswdLogo   = '';
        if (file_exists(public_path('images/silang.png'))) {
            $silangLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/silang.png')));
        }
        if (file_exists(public_path('images/dswd.png'))) {
            $dswdLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/dswd.png')));
        }
    @endphp

    @php
        $barangayGroups = isset($payoutsByBarangay) ? $payoutsByBarangay : collect(['All Barangays' => $payouts]);
        $loopIndex = 0;
    @endphp

    @foreach($barangayGroups as $brgyName => $brgyPayouts)
    @php $loopIndex++; @endphp

    {{-- ═══════════ PAGE HEADER ═══════════ --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if($silangLogo)
                    <img src="{{ $silangLogo }}" style="width: 64px; height: 64px; object-fit: contain; display: block; margin: 0 auto;" alt="Silang Seal">
                @endif
            </td>
            <td class="gov-cell">
                <div class="gov-meta">Republic of the Philippines &bull; Province of Cavite &bull; Municipality of Silang</div>
                <div class="gov-name">MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</div>
                <div class="gov-sub">Office of the Senior Citizens Affairs (OSCA)</div>
            </td>
            <td class="logo-cell">
                @if($dswdLogo)
                    <img src="{{ $dswdLogo }}" style="width: 64px; height: 64px; object-fit: contain; display: block; margin: 0 auto;" alt="DSWD Logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="divider-thick"></div>
    <div class="divider-thin"></div>

    <div class="report-title">
        <h2>Birthday Financial Assistance &ndash; Payout List</h2>
        <p>{{ $month }} {{ $year }}</p>
    </div>

    {{-- ═══════════ INFO BAR ═══════════ --}}
    <table class="info-bar">
        <tr>
            <td><strong>Barangay:</strong> {{ $brgyName }}</td>
            <td style="text-align:right;"><strong>Date Printed:</strong> {{ now()->format('F d, Y g:i A') }}</td>
        </tr>
        <tr>
            <td>
                <strong>Total Beneficiaries:</strong> {{ $brgyPayouts->count() }}
                &nbsp;&nbsp;
                <strong>Total Amount:</strong> &#8369;{{ number_format($brgyPayouts->sum('amount'), 2) }}
            </td>
            <td style="text-align:right;"><strong>Period:</strong> {{ $month }} {{ $year }}</td>
        </tr>
    </table>

    {{-- ═══════════ DATA TABLE ═══════════ --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 13%;">Control No.</th>
                <th style="width: 12%;">OSCA ID</th>
                <th class="text-left" style="width: 25%;">Full Name</th>
                <th style="width: 13%;">Birthday</th>
                <th style="width: 5%;">Age</th>
                <th style="width: 9%;">Amount</th>
                <th style="width: 13%;">Signature</th>
                <th class="text-left" style="width: 5%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($brgyPayouts as $index => $payout)
            @php
                $senior = $payout->senior;
                $age = $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->age : '-';
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $senior->control_number ?? '-' }}</td>
                <td>{{ $senior->osca_id ?? '-' }}</td>
                <td class="text-left"><strong>{{ $senior->full_name }}</strong></td>
                <td>{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $age }}</td>
                <td>&#8369;{{ number_format($payout->amount, 2) }}</td>
                <td style="height: 28px;"></td>
                <td class="text-left">{{ $payout->remarks ?? '' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" style="text-align:right; padding-right: 8px;">TOTAL</td>
                <td>&#8369;{{ number_format($brgyPayouts->sum('amount'), 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    {{-- ═══════════ SIGNATURES ═══════════ --}}
    <table class="signature-section">
        <tr>
            <td>
                <div class="sig-line"></div>
                <div class="sig-name">Prepared by</div>
                <div class="sig-label">MSWDO Staff</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-name">Checked by</div>
                <div class="sig-label">OSCA Officer</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-name">Approved by</div>
                <div class="sig-label">MSWDO Head</div>
            </td>
        </tr>
    </table>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
    @endforeach

    <div class="no-print" style="margin: 30px 0; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 24px; font-size: 14px; cursor: pointer; background: #1A237E; color: white; border: none; border-radius: 4px; margin-right: 8px;">Print</button>
        <button onclick="window.close()" style="padding: 10px 24px; font-size: 14px; cursor: pointer; background: #6B7280; color: white; border: none; border-radius: 4px;">Close</button>
    </div>
</body>
</html>

