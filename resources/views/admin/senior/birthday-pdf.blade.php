<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Birthday Beneficiaries Report</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 10px; color: #333; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1A237E; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0 0 4px; color: #1A237E; }
        .header h2 { font-size: 12px; margin: 0 0 4px; color: #333; }
        .header p { font-size: 9px; margin: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8.5px; }
        th { background: #1A237E; color: white; padding: 5px 4px; text-align: left; font-weight: 600; font-size: 7.5px; text-transform: uppercase; }
        td { padding: 4px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .summary { margin-top: 15px; }
        .summary table { width: 40%; }
        .summary th { background: #FBC02D; color: #333; }
        .footer { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 8px; color: #666; text-align: center; }
        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7px; font-weight: 600; }
        .badge-active { background: #dcfce7; color: #166534; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MUNICIPAL SOCIAL WELFARE AND DEVELOPMENT OFFICE</h1>
        <h2>Silang, Cavite</h2>
        <p>Birthday Beneficiaries Master List</p>
        <p>Generated: {{ $dateGenerated }}</p>
        <p>Total Beneficiaries: <strong>{{ $total }}</strong></p>
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
                <td>{{ $s['control_number'] }}</td>
                <td>{{ $s['osca_id'] }}</td>
                <td><strong>{{ $s['full_name'] }}</strong></td>
                <td>{{ $s['birth_date'] }}</td>
                <td>{{ $s['current_age'] }}</td>
                <td>{{ $s['age_turning'] }}</td>
                <td>{{ $s['barangay'] }}</td>
                <td>{{ $s['contact_number'] }}</td>
                <td>{{ ucfirst($s['status']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($barangaySummary->count() > 0)
    <div class="summary">
        <h3 style="font-size: 10px; color: #1A237E; margin-bottom: 5px;">Barangay Summary</h3>
        <table>
            <thead><tr><th>Barangay</th><th>Total Beneficiaries</th></tr></thead>
            <tbody>
                @foreach($barangaySummary as $bs)
                <tr><td>{{ $bs['barangay'] }}</td><td><strong>{{ $bs['count'] }}</strong></td></tr>
                @endforeach
                <tr style="font-weight: bold; background: #f0f0f0;"><td>Grand Total</td><td>{{ $total }}</td></tr>
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Prepared by: _____________________________ &nbsp;&nbsp;&nbsp; Date: {{ date('F d, Y') }}</p>
        <p>MSWDO Silang - Birthday Beneficiaries Report</p>
    </div>
</body>
</html>
