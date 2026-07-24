<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birthday Payout Receipt</title>
    <style>
        @media print {
            body { margin: 0; padding: 20px; font-family: 'Times New Roman', serif; }
            .no-print { display: none !important; }
        }
        body { font-family: 'Times New Roman', serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .receipt { border: 2px solid #000; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .header h1 { font-size: 20px; margin: 0; font-weight: bold; }
        .header h2 { font-size: 16px; margin: 5px 0; font-weight: bold; }
        .header h3 { font-size: 14px; margin: 5px 0; font-weight: bold; }
        .info-section { margin: 20px 0; }
        .info-row { margin: 10px 0; font-size: 14px; }
        .info-row strong { font-weight: bold; display: inline-block; width: 150px; }
        .amount-section { text-align: center; margin: 30px 0; padding: 20px; background: #f9f9f9; border: 1px solid #000; }
        .amount-section .amount { font-size: 32px; font-weight: bold; color: #1A237E; }
        .amount-section .label { font-size: 14px; color: #666; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 45%; }
        .signature-box .line { border-top: 1px solid #000; margin-top: 60px; }
        .signature-box .label { font-size: 12px; margin-top: 5px; }
        .no-print { margin: 20px 0; text-align: center; }
        .no-print button { padding: 10px 20px; font-size: 14px; cursor: pointer; margin: 0 5px; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 5px; font-weight: bold; font-size: 14px; }
        .status-released { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>Municipality of Silang</h1>
            <h2>Office of the Senior Citizens Affairs (OSCA)</h2>
            <h3>Birthday Financial Assistance Acknowledgement Receipt</h3>
        </div>

        <div class="info-section">
            <div class="info-row">
                <strong>Receipt No:</strong> BP-{{ str_pad($payout->id, 6, '0', STR_PAD_LEFT) }}
            </div>
            <div class="info-row">
                <strong>Date:</strong> {{ $payout->released_date ? $payout->released_date->format('F d, Y g:i A') : now()->format('F d, Y g:i A') }}
            </div>
        </div>

        <div class="info-section">
            <h3 style="border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 15px;">Beneficiary Information</h3>
            <div class="info-row">
                <strong>Full Name:</strong> {{ $payout->senior->full_name }}
            </div>
            <div class="info-row">
                <strong>Control No:</strong> {{ $payout->senior->control_number ?? '-' }}
            </div>
            <div class="info-row">
                <strong>OSCA ID:</strong> {{ $payout->senior->osca_id ?? '-' }}
            </div>
            <div class="info-row">
                <strong>Barangay:</strong> {{ $payout->senior->barangay }}
            </div>
            <div class="info-row">
                <strong>Birthday:</strong> {{ $payout->senior->birth_date ? \Carbon\Carbon::parse($payout->senior->birth_date)->format('F d, Y') : '-' }}
            </div>
            <div class="info-row">
                <strong>Age:</strong> {{ $payout->senior->birth_date ? \Carbon\Carbon::parse($payout->senior->birth_date)->age : '-' }}
            </div>
            <div class="info-row">
                <strong>Contact:</strong> {{ $payout->senior->contact_number ?? '-' }}
            </div>
        </div>

        <div class="amount-section">
            <div class="amount">₱{{ number_format($payout->amount, 2) }}</div>
            <div class="label">Birthday Financial Assistance Amount</div>
            <div style="margin-top: 10px;">
                <span class="status-badge status-{{ $payout->status->value }}">
                    {{ ucfirst($payout->status->value) }}
                </span>
            </div>
        </div>

        <div class="info-section">
            <h3 style="border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 15px;">Payout Details</h3>
            <div class="info-row">
                <strong>Birthday Month:</strong> {{ $payout->birth_month }}
            </div>
            <div class="info-row">
                <strong>Payout Year:</strong> {{ $payout->payout_year }}
            </div>
            @if($payout->released_by)
                <div class="info-row">
                    <strong>Released By:</strong> {{ $payout->releasedBy->name }}
                </div>
            @endif
            @if($payout->remarks)
                <div class="info-row">
                    <strong>Remarks:</strong> {{ $payout->remarks }}
                </div>
            @endif
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="line"></div>
                <div class="label">Beneficiary Signature</div>
            </div>
            <div class="signature-box">
                <div class="line"></div>
                <div class="label">OSCA Representative</div>
            </div>
        </div>

        <div style="margin-top: 30px; font-size: 11px; color: #666; text-align: center;">
            <p>This receipt serves as acknowledgement of the birthday financial assistance received from the Office of Senior Citizens Affairs (OSCA) Silang.</p>
            <p>Generated on {{ now()->format('F d, Y g:i A') }}</p>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()">Print Receipt</button>
        <button onclick="window.close()">Close</button>
    </div>
</body>
</html>
