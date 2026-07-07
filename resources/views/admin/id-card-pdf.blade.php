<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .cards-container {
            padding: 20px;
            background: #F8FAFC;
        }
        .cards-table {
            width: 100%;
            border-collapse: collapse;
        }
        .card {
            width: 85.6mm;
            height: 53.98mm;
            border: 2px solid #000;
            background: white;
            vertical-align: top;
        }
        .header {
            background: #1A237E;
            color: white;
            border-bottom: 0.5mm solid #FBC02D;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 1mm 2mm;
            vertical-align: middle;
        }
        .logo {
            width: 6mm;
            height: 6mm;
            background: white;
            border-radius: 50%;
        }
        .header-text {
            text-align: center;
        }
        .header-text h5 {
            font-size: 6pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-text p {
            font-size: 4.5pt;
            margin: 0;
        }
        .header-text span {
            font-size: 4.5pt;
            font-weight: bold;
            color: #FBC02D;
        }
        .body-table {
            width: 100%;
            border-collapse: collapse;
            height: 42mm;
        }
        .body-table td {
            padding: 2mm;
            vertical-align: top;
        }
        .photo-section {
            width: 18mm;
            vertical-align: top;
        }
        .photo {
            width: 16mm;
            height: 16mm;
            border: 0.3mm solid #1A237E;
            display: block;
        }
        .id-num {
            font-size: 5pt;
            font-weight: bold;
            color: #D32F2F;
            background: #FFEBEE;
            padding: 0.5mm;
            border: 0.1mm solid #FFCDD2;
            text-align: center;
            width: 16mm;
            margin-top: 1mm;
        }
        .qr-code {
            width: 10mm;
            height: 10mm;
            border: 0.1mm solid #E5E7EB;
            padding: 0.5mm;
            background: white;
            display: block;
            margin-top: 2mm;
        }
        .details {
            padding-left: 2mm;
        }
        .name {
            font-size: 7pt;
            font-weight: bold;
            color: #111827;
            margin: 0 0 0.5mm 0;
            text-transform: uppercase;
            border-bottom: 0.2mm solid #E5E7EB;
            padding-bottom: 0.5mm;
        }
        .field {
            font-size: 5pt;
            margin: 0.5mm 0;
        }
        .label {
            font-weight: bold;
            color: #4B5563;
            width: 10mm;
            display: inline-block;
            text-transform: uppercase;
        }
        .value {
            color: #1F2937;
            font-weight: 500;
        }
        .signature-holder {
            border-top: 0.1mm dashed #9CA3AF;
            text-align: center;
            font-size: 4pt;
            color: #6B7280;
            padding-top: 0.5mm;
            margin-top: 2mm;
        }
        /* Back Side Styles */
        .back-header {
            background-color: #F8FAFC;
            border-bottom: 2px solid #1A237E;
            padding: 2mm 3mm;
            text-align: center;
        }
        .back-header h6 {
            font-size: 6pt;
            font-weight: bold;
            color: #1A237E;
            margin: 0;
            text-transform: uppercase;
        }
        .back-body {
            padding: 2.5mm 3.5mm;
            height: 42mm;
        }
        .back-details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 5.5pt;
        }
        .back-details-table td {
            padding: 0.5mm;
            vertical-align: top;
        }
        .back-label {
            font-weight: bold;
            color: #4B5563;
            text-transform: uppercase;
            font-size: 4.5pt;
            display: block;
            margin-bottom: 0.2mm;
        }
        .back-value {
            color: #111827;
            font-weight: 600;
            display: block;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            padding: 0.5mm;
            vertical-align: top;
        }
        .sign-line {
            border-top: 0.3mm solid #000000;
            padding-top: 0.5mm;
            font-size: 5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .sign-subtitle {
            font-size: 4pt;
            color: #4B5563;
        }
        .back-footer {
            background-color: #1A237E;
            color: white;
            font-size: 4pt;
            text-align: center;
            padding: 1mm 2mm;
            margin-top: 2mm;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="cards-container">
        <table class="cards-table">
            <tr>
                <!-- FRONT SIDE -->
                <td class="card">
                    <table class="header-table">
                        <tr>
                            <td width="6mm"><div class="logo"></div></td>
                            <td class="header-text">
                                <h5>Republic of the Philippines</h5>
                                <p>Province of Cavite • Municipality of Silang</p>
                                <span>OFFICE OF THE SENIOR CITIZENS AFFAIRS</span>
                            </td>
                            <td width="6mm"><div class="logo"></div></td>
                        </tr>
                    </table>
                    <table class="body-table">
                        <tr>
                            <td class="photo-section">
                                @if($senior->photo && file_exists(public_path($senior->photo)))
                                    <img src="{{ asset($senior->photo) }}" class="photo">
                                @elseif($senior->avatar_image && file_exists(public_path($senior->avatar_image)))
                                    <img src="{{ asset($senior->avatar_image) }}" class="photo">
                                @else
                                    <div class="photo" style="background: #1A237E; color: white; text-align: center; line-height: 16mm; font-size: 8pt;">{{ strtoupper(substr($senior->full_name, 0, 2)) }}</div>
                                @endif
                                <div class="id-num">{{ $senior->senior_id_number ?? $senior->control_number }}</div>
                                @if($senior->qr_code_image && file_exists(public_path($senior->qr_code_image)))
                                    <img src="{{ asset($senior->qr_code_image) }}" class="qr-code">
                                @else
                                    <div class="qr-code" style="background: #f0f0f0; text-align: center; line-height: 10mm; font-size: 3pt;">QR</div>
                                @endif
                            </td>
                            <td class="details">
                                <p class="name">{{ $senior->full_name }}</p>
                                <div class="field"><span class="label">Birthdate:</span> <span class="value">{{ \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') }}</span></div>
                                <div class="field"><span class="label">Age:</span> <span class="value">{{ $senior->age }}</span></div>
                                <div class="field"><span class="label">Sex:</span> <span class="value">{{ $senior->sex }}</span></div>
                                <div class="field"><span class="label">Barangay:</span> <span class="value">{{ $senior->barangay }}</span></div>
                                <div class="field"><span class="label">Address:</span> <span class="value">{{ $senior->address }}</span></div>
                                <div class="signature-holder">SIGNATURE OF HOLDER</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20px"></td>
                <!-- BACK SIDE -->
                <td class="card">
                    <div class="back-header">
                        <h6>ID Card Terms &amp; Reference Information</h6>
                    </div>
                    <div class="back-body">
                        <table class="back-details-table">
                            <tr>
                                <td colspan="2">
                                    <span class="back-label">Residential Address:</span>
                                    <span class="back-value">{{ $senior->address }}, {{ $senior->barangay }}, Silang, Cavite</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="back-label">Blood Type:</span>
                                    <span class="back-value">{{ $senior->blood_type ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="back-label">Civil Status:</span>
                                    <span class="back-value">{{ $senior->civil_status ?? 'N/A' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span class="back-label">Emergency Contact Name:</span>
                                    <span class="back-value">{{ $senior->emergency_contact_name ?? 'N/A' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="back-label">Contact Number:</span>
                                    <span class="back-value">{{ $senior->emergency_contact_number ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="back-label">Relationship:</span>
                                    <span class="back-value">{{ $senior->emergency_contact_relationship ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        </table>
                        <table class="signatures-table">
                            <tr>
                                <td>
                                    <span class="sign-line">OSCA Head</span>
                                    <span class="sign-subtitle">OSCA Silang Head Office</span>
                                </td>
                                <td>
                                    <span class="sign-line">MSWDO Officer</span>
                                    <span class="sign-subtitle">Authorized Signature</span>
                                </td>
                            </tr>
                        </table>
                        <div class="back-footer">
                            If found, please return this card to the Office of the Senior Citizens Affairs, Municipality of Silang, Cavite.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
