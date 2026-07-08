<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizens Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1A237E;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #1A237E;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background: #1A237E;
            color: white;
        }
        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }
        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        .status-active {
            background: #10B981;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .status-pending {
            background: #F59E0B;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Senior Citizens Report</h1>
        <p>Municipal Social Welfare and Development Office</p>
        <p>Silang, Cavite</p>
    </div>

    <div class="info-bar">
        <div><strong>Date Generated:</strong> {{ $date }}</div>
        <div><strong>Total Records:</strong> {{ $total }}</div>
        @if($barangay)
            <div><strong>Barangay:</strong> {{ $barangay }}</div>
        @endif
        @if($search)
            <div><strong>Search:</strong> {{ $search }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Control Number</th>
                <th>Full Name</th>
                <th>Address</th>
                <th>Barangay</th>
                <th>Birth Date</th>
                <th>Sex</th>
                <th>Age</th>
                <th>Contact Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seniors as $senior)
            <tr>
                <td>{{ $senior->control_number ?? '-' }}</td>
                <td>{{ $senior->full_name ?? '-' }}</td>
                <td>{{ $senior->address ?? '-' }}</td>
                <td>{{ $senior->barangay ?? '-' }}</td>
                <td>{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') : '-' }}</td>
                <td>{{ $senior->sex ?? '-' }}</td>
                <td>{{ $senior->age ?? '-' }}</td>
                <td>{{ $senior->contact_number ?? '-' }}</td>
                <td>
                    @if($senior->status == 'active')
                        <span class="status-active">Active</span>
                    @else
                        <span class="status-pending">{{ ucfirst($senior->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This document is generated automatically by the MSWDO Senior Citizen Management System.</p>
        <p>Generated on {{ now()->format('F d, Y g:i A') }}</p>
    </div>
</body>
</html>
