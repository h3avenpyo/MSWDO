<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birthday Payout List - Print</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            body { margin: 0; padding: 0; font-family: 'Times New Roman', serif; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            .page { min-height: 267mm; }
        }
        body { font-family: 'Times New Roman', serif; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .header h1 { font-size: 24px; margin: 0; font-weight: bold; }
        .header h2 { font-size: 18px; margin: 5px 0; font-weight: bold; }
        .header h3 { font-size: 16px; margin: 5px 0; font-weight: bold; }
        .header p { margin: 5px 0; font-size: 14px; }
        .info-row { margin: 15px 0; font-size: 14px; }
        .info-row strong { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        table th { background: #f0f0f0; font-weight: bold; text-align: center; }
        table td { text-align: center; }
        table td.text-left { text-align: left; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 30%; }
        .signature-box .line { border-top: 1px solid #000; margin-top: 60px; }
        .signature-box .label { font-size: 12px; margin-top: 5px; }
        .no-print { margin: 20px 0; text-align: center; }
        .no-print button { padding: 10px 20px; font-size: 14px; cursor: pointer; }
    </style>
</head>
<body>
    @if(isset($payoutsByBarangay))
        @foreach($payoutsByBarangay as $barangayName => $barangayPayouts)
            <div class="page">
                <div class="header">
                    <h1>Municipality of Silang</h1>
                    <h2>Office of the Senior Citizens Affairs (OSCA)</h2>
                    <h3>Birthday Financial Assistance</h3>
                    <h3>Payout List</h3>
                </div>

                <div class="info-row">
                    <strong>Birthday Month:</strong> {{ $month }}
                </div>
                <div class="info-row">
                    <strong>Year:</strong> {{ $year }}
                </div>
                <div class="info-row">
                    <strong>Barangay:</strong> {{ $barangayName }}
                </div>
                <div class="info-row">
                    <strong>Date Printed:</strong> {{ now()->format('F d, Y g:i A') }}
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th style="width: 100px;">Control No.</th>
                            <th style="width: 100px;">OSCA ID</th>
                            <th class="text-left">Full Name</th>
                            <th style="width: 100px;">Birthday</th>
                            <th style="width: 50px;">Age</th>
                            <th style="width: 80px;">Amount</th>
                            <th style="width: 120px;">Signature</th>
                            <th class="text-left">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangayPayouts as $index => $payout)
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
                                <td>₱{{ number_format($payout->amount, 2) }}</td>
                                <td></td>
                                <td class="text-left">{{ $payout->remarks ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

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
            </div>
            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <div class="header">
            <h1>Municipality of Silang</h1>
            <h2>Office of the Senior Citizens Affairs (OSCA)</h2>
            <h3>Birthday Financial Assistance</h3>
            <h3>Payout List</h3>
        </div>

        <div class="info-row">
            <strong>Birthday Month:</strong> {{ $month }}
        </div>
        <div class="info-row">
            <strong>Year:</strong> {{ $year }}
        </div>
        <div class="info-row">
            <strong>Barangay:</strong> {{ $barangay ?: 'All Barangays' }}
        </div>
        <div class="info-row">
            <strong>Date Printed:</strong> {{ now()->format('F d, Y g:i A') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th style="width: 100px;">Control No.</th>
                    <th style="width: 100px;">OSCA ID</th>
                    <th class="text-left">Full Name</th>
                    <th style="width: 100px;">Birthday</th>
                    <th style="width: 50px;">Age</th>
                    <th style="width: 80px;">Amount</th>
                    <th style="width: 120px;">Signature</th>
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
                        <td>₱{{ number_format($payout->amount, 2) }}</td>
                        <td></td>
                        <td class="text-left">{{ $payout->remarks ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
    @endif

    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
</body>
</html>
