<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizens Masterlist Report</title>
    <style>
        @page {
            margin: 6mm 8mm 8mm 8mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11.5px;
            line-height: 1.3;
            color: #0f172a;
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
            padding: 2px 4px;
            vertical-align: middle;
        }
        .logo-cell {
            width: 80px;
            text-align: center;
            vertical-align: middle;
            padding: 0;
        }
        .gov {
            text-align: center;
            vertical-align: middle;
            line-height: 1.3;
            color: #0f172a;
            padding: 0 6px;
        }
        .gov .gov-line {
            font-size: 10px;
            font-weight: 500;
            color: #374151;
        }
        .gov h2 {
            margin: 3px 0 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            font-weight: bold;
            color: #1A237E;
            letter-spacing: 0.4px;
        }
        .line {
            border-top: 2px solid #1A237E;
            margin: 2px 0 1px;
        }
        .line2 {
            border-top: 1px solid #1A237E;
            margin-bottom: 4px;
        }
        .report-title {
            text-align: center;
            margin: 2px 0 4px;
        }
        .report-title h3 {
            font-size: 14px;
            margin: 0;
            color: #1A237E;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .info-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            font-size: 11px;
            table-layout: fixed;
        }
        .info-bar td {
            border: none;
            padding: 4px 8px;
            vertical-align: middle;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 11.5px;
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
            padding: 7px 4.5px;
            text-align: left;
            font-weight: bold;
            font-size: 11.5px;
            text-transform: uppercase;
            border: 1px solid #1A237E;
            color: #ffffff;
            overflow: hidden;
            word-wrap: break-word;
        }
        .data-table td {
            padding: 7px 4.5px;
            border: 1px solid #94a3b8;
            vertical-align: middle;
            overflow: hidden;
            word-wrap: break-word;
            font-size: 11.5px;
            color: #0f172a;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    @php
        $silangLogo = '';
        $dswdLogo = '';
        $mswdoLogo = '';
        if (file_exists(public_path('images/silang.png'))) {
            $silangLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/silang.png')));
        }
        if (file_exists(public_path('images/dswd.png'))) {
            $dswdLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/dswd.png')));
        }
        if (file_exists(public_path('images/mswdo-logo.png'))) {
            $mswdoLogo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/mswdo-logo.png')));
        }

        // Calculate dynamic column widths based on dataset content
        $maxNameLen = 18;
        $maxAddrLen = 25;
        $maxBrgyLen = 10;
        foreach ($seniors as $s) {
            $maxNameLen = max($maxNameLen, mb_strlen($s->full_name ?? ''));
            $maxAddrLen = max($maxAddrLen, mb_strlen($s->address ?? ''));
            $maxBrgyLen = max($maxBrgyLen, mb_strlen($s->barangay ?? ''));
        }

        // Fixed narrow columns (total: 42.5%)
        $colNum = 3.5;
        $colCtrl = 13.5;
        $colBdate = 10.5;
        $colSex = 4.0;
        $colAge = 4.0;
        $colContact = 11.0;
        $fixedTotal = $colNum + $colCtrl + $colBdate + $colSex + $colAge + $colContact; // 46.5%

        // Proportionally distribute flexible budget (53.5%)
        $flexBudget = 100.0 - $fixedTotal;
        $totalFlexWeight = $maxNameLen + $maxAddrLen + $maxBrgyLen;

        $colName = round(($maxNameLen / $totalFlexWeight) * $flexBudget, 1);
        $colBrgy = round(($maxBrgyLen / $totalFlexWeight) * $flexBudget, 1);
        $colAddr = round($flexBudget - $colName - $colBrgy, 1);
    @endphp

    <!-- Report Header -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if($silangLogo)
                    <img src="{{ $silangLogo }}" style="width: 72px; height: 72px; object-fit: contain; display: block; margin: 0 auto;" alt="Silang Seal">
                @endif
            </td>
            <td class="gov">
                <div class="gov-line">Republic of the Philippines &bull; Province of Cavite &bull; Municipality of Silang</div>
                <h2>MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</h2>
                <div style="font-size: 10px; color: #475569; margin-top: 2px;">Senior Citizens Masterlist Report</div>
            </td>
            <td class="logo-cell">
                @if($dswdLogo)
                    <img src="{{ $dswdLogo }}" style="width: 72px; height: 72px; object-fit: contain; display: block; margin: 0 auto;" alt="DSWD Logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="line"></div>
    <div class="line2"></div>

    <table class="info-bar">
        <tr>
            <td><strong>Date Generated:</strong> {{ $date }}</td>
            <td style="text-align: right;">
                <strong>Total Records:</strong> {{ number_format($total) }}
                @if(isset($totalParts) && $totalParts > 1)
                    <span style="color:#1A237E;font-weight:bold;">&nbsp;(Batch {{ $currentPart }} of {{ $totalParts }} &bull; Records {{ number_format($partStart) }}–{{ number_format($partEnd) }})</span>
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

    <!-- Dynamic Continuous Data Table with Repeating Header -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: {{ $colNum }}%; text-align: center;">#</th>
                <th style="width: {{ $colCtrl }}%;">Control No.</th>
                <th style="width: {{ $colName }}%;">Full Name</th>
                <th style="width: {{ $colAddr }}%;">Address</th>
                <th style="width: {{ $colBrgy }}%;">Barangay</th>
                <th style="width: {{ $colBdate }}%;">Birth Date</th>
                <th style="width: {{ $colSex }}%; text-align: center;">Sex</th>
                <th style="width: {{ $colAge }}%; text-align: center;">Age</th>
                <th style="width: {{ $colContact }}%;">Contact No.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($seniors as $index => $senior)
            <tr>
                <td class="nowrap" style="text-align: center; font-weight: bold; color: #475569;">{{ (isset($partStart) ? $partStart - 1 : 0) + $loop->iteration }}</td>
                <td class="nowrap" style="font-weight: 600;">{{ $senior->control_number ?? '-' }}</td>
                <td><strong style="color: #0f172a;">{{ $senior->full_name ?? '-' }}</strong></td>
                <td>{{ $senior->address ?? '-' }}</td>
                <td>{{ $senior->barangay ?? '-' }}</td>
                <td class="nowrap">{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') : '-' }}</td>
                <td class="nowrap" style="text-align: center;">{{ $senior->sex ? substr($senior->sex, 0, 1) : '-' }}</td>
                <td class="nowrap" style="text-align: center; font-weight: 600;">{{ $senior->age ?? '-' }}</td>
                <td class="nowrap">{{ $senior->contact_number ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; padding: 25px; color: #64748b;">No senior citizen records found matching the criteria.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- DomPDF Dynamic Page Script for Repeating Footer and Page Counter -->
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Helvetica, Arial, sans-serif", "normal");
            $size = 8.5;
            $color = array(0.35, 0.40, 0.45);
            $w = $pdf->get_width();
            $h = $pdf->get_height();

            $footerLeft = "New Municipal Building, Brgy. Biga I, Silang, Cavite • MSWDO Senior Citizen System";
            $pdf->page_text(22, $h - 16, $footerLeft, $font, $size, $color);

            $pdf->page_text($w - 85, $h - 16, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, $size, $color);
        }
    </script>
</body>
</html>
