<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Birthday Beneficiaries - Print</title>
    @vite(['resources/css/admin-compat.css'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #1A237E; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #1A237E; padding-bottom: 15px; }
        .header .logo-placeholder { width: 70px; height: 70px; border-radius: 50%; background: #1A237E; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 24px; margin: 0 auto 10px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; color: #1A237E; font-weight: 800; }
        .header h2 { font-size: 14px; margin: 0 0 4px; color: #333; font-weight: 600; }
        .header p { font-size: 10px; margin: 0; color: #666; }
        .meta { display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9px; }
        th { background: #1A237E; color: white; padding: 6px 5px; text-align: left; font-weight: 600; font-size: 8px; text-transform: uppercase; }
        td { padding: 5px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .summary-section { margin-top: 20px; page-break-inside: avoid; }
        .summary-section h3 { font-size: 11px; color: #1A237E; margin-bottom: 5px; }
        .summary-table { width: 50%; }
        .summary-table th { background: #FBC02D; color: #333; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; }
        .signature { margin-top: 25px; }
        .signature-line { width: 200px; border-top: 1px solid #333; margin-top: 35px; }
        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7.5px; font-weight: 600; }
        .badge-active { background: #dcfce7; color: #166534; }
        @media print { @page { size: landscape; margin: 15mm; } body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } .no-print { display: none !important; } }
        .btn-print { position: fixed; top: 10px; right: 10px; z-index: 9999; }
    </style>
</head>
<body>
    <button class="btn btn-primary btn-print no-print" onclick="window.print()"><i class="fas fa-print me-1"></i> Print</button>

    <div class="header">
        <div class="logo-placeholder">M</div>
        <h1>MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</h1>
        <h2>Silang, Cavite</h2>
        <p>Birthday Beneficiaries Report</p>
    </div>

    <div class="meta">
        <div><strong>Date Generated:</strong> {{ $dateGenerated }}</div>
        <div><strong>Total Beneficiaries:</strong> {{ $total }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Control No.</th>
                <th>Senior ID</th>
                <th>Full Name</th>
                <th>Birth Date</th>
                <th>Current Age</th>
                <th>Age Turning</th>
                <th>Barangay</th>
                <th>Contact No.</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seniors as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $s['control_number'] }}</strong></td>
                <td>{{ $s['osca_id'] }}</td>
                <td>{{ $s['full_name'] }}</td>
                <td>{{ $s['birth_date'] }}</td>
                <td>{{ $s['current_age'] }}</td>
                <td>{{ $s['age_turning'] }}</td>
                <td>{{ $s['barangay'] }}</td>
                <td>{{ $s['contact_number'] }}</td>
                <td><span class="badge badge-active">{{ ucfirst($s['status']) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($barangaySummary->count() > 0)
    <div class="summary-section">
        <h3><i class="fas fa-map-pin me-1"></i> Barangay Summary</h3>
        <table class="summary-table">
            <thead><tr><th>Barangay</th><th>Total Beneficiaries</th></tr></thead>
            <tbody>
                @foreach($barangaySummary as $bs)
                <tr><td>{{ $bs['barangay'] }}</td><td><strong>{{ $bs['count'] }}</strong></td></tr>
                @endforeach
                <tr style="font-weight: bold; background: #f0f0f0;"><td>Grand Total</td><td><strong>{{ $total }}</strong></td></tr>
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <div class="row">
            <div class="col-4">
                <p><strong>Prepared by:</strong></p>
                <div class="signature-line"></div>
                <p style="font-size: 9px; color: #666;">Signature over Printed Name</p>
            </div>
            <div class="col-4 text-center">
                <p><strong>Noted by:</strong></p>
                <div class="signature-line"></div>
                <p style="font-size: 9px; color: #666;">MSWDO Officer</p>
            </div>
            <div class="col-4 text-end">
                <p><strong>Date:</strong> {{ date('F d, Y') }}</p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() { /* auto-print disabled to let user review */ };
    </script>
</body>
</html>
