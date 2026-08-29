<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Birthday Payout List</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 6mm 7mm 7mm 7mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        /* â”€â”€â”€ LETTERHEAD â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .lh-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 3px;
        }
        .lh-table td { border: none; padding: 0; vertical-align: middle; }
        .lh-logo { width: 58px; text-align: center; }
        .lh-center {
            text-align: center;
            padding: 0 6px;
            line-height: 1.3;
        }
        .lh-meta  { font-size: 8.5px; color: #4b5563; }
        .lh-title { font-size: 13px; font-weight: bold; color: #1A237E; margin: 2px 0 1px; }
        .lh-sub   { font-size: 9px; color: #374151; margin: 0; }

        .rule-thick { border-top: 2.5px solid #1A237E; margin: 2px 0 1px; }
        .rule-thin  { border-top: 1px solid #1A237E; margin-bottom: 3px; }

        /* â”€â”€â”€ DOC TITLE ROW â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .doc-title-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 3px;
        }
        .doc-title-table td { border: none; padding: 0 4px; vertical-align: middle; }
        .doc-title-left  { font-size: 12px; font-weight: bold; color: #1A237E; text-transform: uppercase; }
        .doc-title-right {
            font-size: 9px;
            color: #374151;
            text-align: right;
        }

        /* â”€â”€â”€ META BAR â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .meta-bar {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            margin-bottom: 5px;
        }
        .meta-bar td {
            border: none;
            padding: 3px 7px;
            font-size: 9.5px;
            vertical-align: middle;
        }

        /* â”€â”€â”€ DATA TABLE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10px;
        }
        .data-table thead { display: table-header-group; }
        .data-table tr    { page-break-inside: avoid; }

        .data-table th {
            background: #1A237E;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            padding: 5px 4px;
            border: 1px solid #1A237E;
        }
        .data-table th.tl { text-align: left; }

        .data-table td {
            padding: 6px 4px;
            border: 1px solid #94a3b8;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }
        .data-table td.tl { text-align: left; }
        .data-table tbody tr:nth-child(even) { background: #f8fafc; }

        .total-row td {
            font-weight: bold;
            background: #e8eaf6;
            border-top: 2px solid #1A237E;
            font-size: 10px;
        }

        /* â”€â”€â”€ SIGNATURES â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 14px;
        }
        .sig-table td {
            border: none;
            text-align: center;
            padding: 0 12px;
            vertical-align: bottom;
        }
        .sig-space { height: 36px; }
        .sig-line  { border-top: 1.5px solid #0f172a; margin-bottom: 3px; }
        .sig-name  { font-size: 10px; font-weight: bold; }
        .sig-role  { font-size: 9px; color: #374151; }

        /* â”€â”€â”€ PAGE BREAK â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .page-break { page-break-after: always; }
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
    $barangayGroups = isset($payoutsByBarangay) ? $payoutsByBarangay : collect(['All Barangays' => $payouts]);
@endphp

@foreach($barangayGroups as $brgyName => $brgyPayouts)

{{-- â•â• LETTERHEAD â•â• --}}
<table class="lh-table">
    <tr>
        <td class="lh-logo">
            @if($silangLogo)
                <img src="{{ $silangLogo }}" style="width:52px;height:52px;object-fit:contain;display:block;margin:0 auto;" alt="Silang Seal">
            @endif
        </td>
        <td class="lh-center">
            <div class="lh-meta">Republic of the Philippines &bull; Province of Cavite &bull; Municipality of Silang</div>
            <div class="lh-title">MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</div>
            <div class="lh-sub">Office of the Senior Citizens Affairs (OSCA)</div>
        </td>
        <td class="lh-logo">
            @if($dswdLogo)
                <img src="{{ $dswdLogo }}" style="width:52px;height:52px;object-fit:contain;display:block;margin:0 auto;" alt="DSWD Logo">
            @endif
        </td>
    </tr>
</table>

<div class="rule-thick"></div>
<div class="rule-thin"></div>

{{-- â•â• DOCUMENT TITLE + QUICK INFO (single row) â•â• --}}
<table class="doc-title-table">
    <tr>
        <td class="doc-title-left">Birthday Financial Assistance &ndash; Payout List</td>
        <td class="doc-title-right">
            <strong>Period:</strong> {{ $month }} {{ $year }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Barangay:</strong> {{ $brgyName }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Beneficiaries:</strong> {{ $brgyPayouts->count() }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Total Amount:</strong> PHP {{ number_format($brgyPayouts->sum('amount'), 2) }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Date Printed:</strong> {{ now()->format('M d, Y g:i A') }}
        </td>
    </tr>
</table>

{{-- â•â• DATA TABLE â•â• --}}
<table class="data-table">
    <thead>
        <tr>
            <th style="width:4%;">No.</th>
            <th style="width:15%;">Control No.</th>
            <th style="width:10%;">OSCA ID</th>
            <th class="tl" style="width:20%;">Full Name</th>
            <th style="width:10%;">Birthday</th>
            <th style="width:5%;">Age</th>
            <th style="width:8%;">Amount</th>
            <th style="width:14%;">Signature</th>
            <th class="tl" style="width:14%;">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($brgyPayouts as $index => $payout)
        @php
            $senior = $payout->senior;
            $age    = $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->age : '-';
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $senior->control_number ?? '-' }}</td>
            <td>{{ $senior->osca_id ?? '-' }}</td>
            <td class="tl"><strong>{{ $senior->full_name }}</strong></td>
            <td>{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') : '-' }}</td>
            <td>{{ $age }}</td>
            <td>PHP {{ number_format($payout->amount, 2) }}</td>
            <td style="height:30px;"></td>
            <td class="tl">{{ $payout->remarks ?? '' }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="6" style="text-align:right;padding-right:8px;">TOTAL</td>
            <td>PHP {{ number_format($brgyPayouts->sum('amount'), 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>

{{-- â•â• SIGNATURES â•â• --}}
<table class="sig-table">
    <tr>
        <td><div class="sig-space"></div><div class="sig-line"></div><div class="sig-name">Prepared by</div><div class="sig-role">MSWDO Staff</div></td>
        <td><div class="sig-space"></div><div class="sig-line"></div><div class="sig-name">Checked by</div><div class="sig-role">OSCA Officer</div></td>
        <td><div class="sig-space"></div><div class="sig-line"></div><div class="sig-name">Approved by</div><div class="sig-role">MSWDO Head</div></td>
    </tr>
</table>

@if(!$loop->last)
    <div class="page-break"></div>
@endif
@endforeach

</body>
</html>

