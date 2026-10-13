@php
    $caseNumber = $case->case_number ?? 'N/A';
    $createdAt  = $case->created_at?->timezone('Asia/Manila');
    $updatedAt  = $case->updated_at?->timezone('Asia/Manila');
    $barangay   = $case->barangay->name ?? 'N/A';
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Case {{ $caseNumber }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color:#222; line-height:1.35; }
        h1 { font-size:18px; margin:0 0 6px; }
        h2 { font-size:15px; margin:22px 0 6px; border-bottom:1px solid #999; padding-bottom:3px; }
        table { width:100%; border-collapse:collapse; margin-bottom:14px; }
        th, td { border:1px solid #ccc; padding:6px 8px; vertical-align:top; text-align:left; }
        th { width:32%; background:#f2f2f2; }
        .muted { color:#666; font-size:11px; }
        .section { margin-top:18px; }
        .block { background:#fafafa; padding:8px; border:1px solid #ddd; white-space:pre-wrap; }
        .footer { margin-top:30px; font-size:11px; color:#555; }
        .signature { margin-top:50px; text-align:center; }
        .signature-line { margin:0 auto 4px; width:240px; border-top:2px solid #000; height:2px; }
    </style>
</head>
<body>
    <table style="width: 100%; border: none; margin-bottom: 12px; border-collapse: collapse;">
        <tr>
            <td style="width: 50px; vertical-align: middle; text-align: center; border: none; padding: 0 4px;">
                @if($mswdoLogoBase64)
                    <img src="{{ $mswdoLogoBase64 }}" style="width: 52px; height: 52px;" alt="MSWDO Logo">
                @endif
            </td>
            <td style="vertical-align: middle; text-align: center; border: none; padding: 0 4px;">
                <h1 style="margin: 0; font-size: 18px; text-transform: uppercase;">Barangay Case Report</h1>
                <div class="muted" style="margin-top: 4px;">
                    Generated: {{ $generatedAt->format('F j, Y g:i A') }} ({{ $generatedAt->format('T') }})
                </div>
            </td>
            <td style="width: 50px; border: none; padding: 0 4px;"></td>
        </tr>
    </table>

    <h2>Case Details</h2>
    <table>
        <tr><th>Case Number</th><td>{{ $caseNumber }}</td></tr>
        <tr><th>Status</th><td>{{ $case->status ?? 'N/A' }}</td></tr>
        <tr><th>Type of Abuse</th><td>{{ $case->abuse_type ?? 'N/A' }}</td></tr>
        <tr><th>Barangay</th><td>{{ $barangay }}</td></tr>
        <tr><th>Date Filed</th><td>{{ $createdAt?->format('F j, Y g:i A') ?? 'N/A' }}</td></tr>
        <tr><th>Last Updated</th><td>{{ $updatedAt?->format('F j, Y g:i A') ?? 'N/A' }}</td></tr>
        <tr><th>Date Reported</th><td>{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('F j, Y') : 'N/A' }}</td></tr>
    </table>

    <h2>Victim Information</h2>
    <table>
        <tr><th>Full Name</th><td>{{ $case->victim_name ?? 'N/A' }}</td></tr>
        <tr><th>Age</th><td>{{ $case->victim_age ?? 'N/A' }}</td></tr>
        <tr><th>Gender</th><td>{{ $case->victim_gender ?? 'N/A' }}</td></tr>
        <tr><th>Contact</th><td>{{ $case->victim_contact ?? 'N/A' }}</td></tr>
        <tr><th>Email</th><td>{{ $case->victim_email ?? 'N/A' }}</td></tr>
        <tr><th>Address</th><td>{{ $case->victim_address ?? 'N/A' }}</td></tr>
    </table>

    <h2>Perpetrator Information</h2>
    <table>
        <tr><th>Full Name</th><td>{{ $case->abuser_name ?? 'N/A' }}</td></tr>
        <tr><th>Age</th><td>{{ $case->abuser_age ?? 'N/A' }}</td></tr>
        <tr><th>Relationship</th><td>{{ $case->abuse_relationship ?? 'N/A' }}</td></tr>
    </table>

    <h2>Incident Details</h2>
    <table>
        <tr><th>Date of Incident</th><td>{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'N/A' }}</td></tr>
        <tr><th>Time of Incident</th><td>{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('g:i A') : 'N/A' }}</td></tr>
        <tr><th>Location</th><td>{{ $case->incident_location ?? 'N/A' }}</td></tr>
        <tr><th>Details</th><td><div class="block">{{ $case->incident_details ?? 'No details provided.' }}</div></td></tr>
    </table>

    @if($case->witness_name || $case->witness_contact)
        <h2>Witness Information</h2>
        <table>
            <tr><th>Name</th><td>{{ $case->witness_name ?? 'N/A' }}</td></tr>
            <tr><th>Contact</th><td>{{ $case->witness_contact ?? 'N/A' }}</td></tr>
        </table>
    @endif

    <div class="signature">
        <div class="signature-line"></div>
        <div>AUTHORIZED SIGNATURE</div>
    </div>

    <div class="footer">
        Case Report | {{ strtoupper((string) ($case->type ?? '')) === 'BCPC' ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System
    </div>
@include('print.edit-controls')
</body>
</html>
