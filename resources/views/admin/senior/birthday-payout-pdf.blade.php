<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birthday Payout List - PDF</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        .container {
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 22px;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 16px;
            margin: 5px 0;
            font-weight: bold;
        }
        .header h3 {
            font-size: 14px;
            margin: 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .info-row {
            margin: 8px 0;
            font-size: 12px;
            display: flex;
        }
        .info-row strong {
            font-weight: bold;
            min-width: 120px;
        }
        .info-row span {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        table thead {
            background: #1A237E;
            color: white;
        }
        table th {
            border: 1px solid #000;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }
        table td.text-left {
            text-align: left;
        }
        table tbody tr:nth-child(even) {
            background: #f5f5f5;
        }
        .summary-section {
            margin-top: 20px;
            padding: 15px;
            background: #f0f0f0;
            border: 1px solid #000;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 12px;
        }
        .summary-row strong {
            font-weight: bold;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 30%;
        }
        .signature-box .line {
            border-top: 1px solid #000;
            margin-top: 70px;
        }
        .signature-box .label {
            font-size: 11px;
            margin-top: 5px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Republic of the Philippines</h1>
            <h2>Province of Cavite</h2>
            <h2>Municipality of Silang</h2>
            <h3>Office of the Senior Citizens Affairs (OSCA)</h3>
            <h3 style="margin-top: 15px;">Birthday Financial Assistance Payout List</h3>
        </div>

        <div class="info-section">
            <div class="info-row">
                <strong>Birthday Month:</strong>
                <span>{{ $month }}</span>
            </div>
            <div class="info-row">
                <strong>Payout Year:</strong>
                <span>{{ $year }}</span>
            </div>
            <div class="info-row">
                <strong>Barangay:</strong>
                <span>{{ $barangay ?: 'All Barangays' }}</span>
            </div>
            <div class="info-row">
                <strong>Date Generated:</strong>
                <span>{{ now()->format('F d, Y g:i A') }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 35px;">No.</th>
                    <th style="width: 100px;">Control No.</th>
                    <th style="width: 100px;">OSCA ID</th>
                    <th class="text-left">Full Name</th>
                    <th style="width: 100px;">Birthday</th>
                    <th style="width: 50px;">Age</th>
                    <th style="width: 80px;">Amount</th>
                    <th style="width: 150px;">Signature</th>
                    <th class="text-left">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payouts as $index => $payout)
                    @php
                        $senior = $payout->senior;
                        $age = $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->age : '-';
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $senior->control_number ?? '-' }}</td>
                        <td>{{ $senior->osca_id ?? '-' }}</td>
                        <td class="text-left">{{ $senior->full_name }}</td>
                        <td>{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('F d, Y') : '-' }}</td>
                        <td>{{ $age }}</td>
                        <td>PHP {{ number_format($payout->amount, 2) }}</td>
                        <td></td>
                        <td class="text-left">{{ $payout->remarks ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-row">
                <strong>Total Beneficiaries:</strong>
                <span>{{ $payouts->count() }}</span>
            </div>
            <div class="summary-row">
                <strong>Total Amount:</strong>
                <span>PHP {{ number_format($payouts->sum('amount'), 2) }}</span>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="line"></div>
                <div class="label">Prepared by</div>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <div class="label">Checked by</div>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <div class="label">Approved by</div>
            </div>
        </div>

        <div class="footer">
            <p>This document is generated by the MSWDO Silang Senior Citizen Management System</p>
            <p>Page 1 of 1</p>
        </div>
    </div>
</body>
</html>
