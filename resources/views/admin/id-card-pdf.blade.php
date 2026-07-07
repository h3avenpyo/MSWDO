<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #F8FAFC;
        }
        .cards-container {
            padding: 20px;
        }
        .cards-table {
            width: 100%;
            border-collapse: collapse;
        }
        .card {
            width: 85.6mm;
            height: 54mm;
            border: 1px solid #000;
            background: linear-gradient(180deg, #1A237E 0%, #283593 50%, #1A237E 100%);
            vertical-align: top;
        }
        .card-inner {
            background: white;
            margin: 1.5mm;
            height: 51mm;
            border: 1px solid #1A237E;
            position: relative;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 30pt;
            color: rgba(26, 35, 126, 0.05);
            font-weight: bold;
            text-transform: uppercase;
            z-index: 0;
            pointer-events: none;
        }
        .card-content {
            position: relative;
            z-index: 1;
            height: 100%;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            background: #1A237E;
        }
        .header-table td {
            padding: 1.5mm 1mm;
            vertical-align: middle;
        }
        .logo-cell {
            width: 12mm;
            text-align: center;
        }
        .logo {
            width: 10mm;
            height: 10mm;
            border-radius: 50%;
            background: white;
            border: 1px solid #FBC02D;
        }
        .gov-info {
            font-size: 6pt;
            color: white;
            text-align: center;
            line-height: 1.4;
        }
        .gov-info strong {
            display: block;
            font-size: 8pt;
            text-transform: uppercase;
            margin-bottom: 0.5mm;
            letter-spacing: 0.5px;
        }
        .gov-info .dept {
            font-size: 7pt;
            font-weight: bold;
            color: #FBC02D;
            margin-top: 0.5mm;
        }
        .header-divider {
            height: 1mm;
            background: linear-gradient(90deg, #1A237E 0%, #FBC02D 50%, #1A237E 100%);
        }
        .body-table {
            width: 100%;
            border-collapse: collapse;
        }
        .body-table td {
            padding: 1.5mm;
            vertical-align: top;
        }
        .photo-cell {
            width: 24mm;
        }
        .photo-frame {
            border: 2px solid #1A237E;
            padding: 1mm;
            background: #F8FAFC;
        }
        .photo {
            width: 18mm;
            height: 24mm;
            border: 1px solid #E5E7EB;
            display: block;
        }
        .id-badge {
            background: #D32F2F;
            color: white;
            font-size: 5pt;
            font-weight: bold;
            padding: 0.5mm 1mm;
            text-align: center;
            margin-top: 1mm;
            border-radius: 2px;
        }
        .signature-box {
            border-top: 1px solid #000;
            padding-top: 0.5mm;
            text-align: center;
            font-size: 4.5pt;
            color: #666;
            margin-top: 1mm;
        }
        .info-cell {
            padding-left: 2mm;
        }
        .name {
            font-size: 11pt;
            font-weight: bold;
            color: #1A237E;
            margin: 0 0 1.5mm 0;
            text-transform: uppercase;
            border-bottom: 2px solid #1A237E;
            padding-bottom: 0.5mm;
            letter-spacing: 0.5px;
        }
        .field {
            font-size: 6pt;
            margin: 1mm 0;
            line-height: 1.4;
        }
        .label {
            font-weight: bold;
            color: #666;
            width: 14mm;
            display: inline-block;
            font-size: 5.5pt;
            text-transform: uppercase;
        }
        .value {
            color: #000;
            font-weight: 600;
            font-size: 6pt;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }
        .footer-table td {
            padding: 1mm;
            vertical-align: middle;
        }
        .qr-cell {
            width: 18mm;
        }
        .qr-code {
            width: 16mm;
            height: 16mm;
            border: 1px solid #E5E7EB;
            display: block;
            padding: 1mm;
            background: white;
        }
        .signature-section {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            padding-top: 0.5mm;
            font-size: 5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.5mm;
        }
        .signature-label {
            font-size: 4.5pt;
            color: #666;
        }
        /* Back Side Styles */
        .back-header {
            background: #1A237E;
            color: white;
            padding: 1.5mm;
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .back-body-table {
            width: 100%;
            border-collapse: collapse;
        }
        .back-body-table td {
            padding: 1.5mm;
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 6pt;
            font-weight: bold;
            color: #1A237E;
            text-transform: uppercase;
            border-bottom: 2px solid #1A237E;
            padding-bottom: 0.5mm;
            margin-bottom: 1.5mm;
            letter-spacing: 0.3px;
        }
        .back-field {
            font-size: 5.5pt;
            margin: 1mm 0;
            line-height: 1.4;
        }
        .back-label {
            font-weight: bold;
            color: #666;
            display: block;
            font-size: 5pt;
            text-transform: uppercase;
            margin-bottom: 0.3mm;
        }
        .back-value {
            color: #000;
            font-weight: 600;
            font-size: 5.5pt;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }
        .signatures-table td {
            width: 50%;
            padding: 1.5mm;
            vertical-align: top;
        }
        .sign-line {
            border-top: 1px solid #000;
            padding-top: 1mm;
            font-size: 5.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 0.5mm;
        }
        .sign-subtitle {
            font-size: 4.5pt;
            color: #666;
            text-align: center;
        }
        .back-footer {
            background: #1A237E;
            color: white;
            font-size: 4.5pt;
            text-align: center;
            padding: 1mm;
            margin-top: 2mm;
            font-weight: 500;
            line-height: 1.3;
            letter-spacing: 0.2px;
        }
    </style>
</head>
<body>
    <div class="cards-container">
        <table class="cards-table">
            <tr>
                <!-- FRONT SIDE -->
                <td class="card">
                    <div class="card-inner">
                        <div class="watermark">OSCA</div>
                        <div class="card-content">
                            <table class="header-table">
                                <tr>
                                    <td class="logo-cell"><div class="logo"></div></td>
                                    <td class="gov-info">
                                        <strong>Republic of the Philippines</strong>
                                        Province of Cavite • Municipality of Silang<br>
                                        <span class="dept">OFFICE OF THE SENIOR CITIZENS AFFAIRS</span>
                                    </td>
                                    <td class="logo-cell"><div class="logo"></div></td>
                                </tr>
                            </table>
                            <div class="header-divider"></div>
                            <table class="body-table">
                                <tr>
                                    <td class="photo-cell">
                                        <div class="photo-frame">
                                            @if($senior->photo && file_exists(public_path($senior->photo)))
                                                <img src="{{ asset($senior->photo) }}" class="photo">
                                            @elseif($senior->avatar_image && file_exists(public_path($senior->avatar_image)))
                                                <img src="{{ asset($senior->avatar_image) }}" class="photo">
                                            @else
                                                <div class="photo" style="background: #1A237E; color: white; text-align: center; line-height: 24mm; font-size: 12pt;">{{ strtoupper(substr($senior->full_name, 0, 2)) }}</div>
                                            @endif
                                        </div>
                                        <div class="id-badge">{{ $senior->senior_id_number ?? $senior->control_number }}</div>
                                        <div class="signature-box">Signature</div>
                                    </td>
                                    <td class="info-cell">
                                        <p class="name">{{ $senior->full_name }}</p>
                                        <div class="field"><span class="label">Address:</span> <span class="value">{{ $senior->address }}</span></div>
                                        <div class="field"><span class="label">Barangay:</span> <span class="value">{{ $senior->barangay }}</span></div>
                                        <div class="field"><span class="label">Birthdate:</span> <span class="value">{{ \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') }}</span></div>
                                        <div class="field"><span class="label">Sex:</span> <span class="value">{{ $senior->sex }}</span></div>
                                        <div class="field"><span class="label">Civil Status:</span> <span class="value">{{ $senior->civil_status ?? 'N/A' }}</span></div>
                                        <div class="field"><span class="label">ID Number:</span> <span class="value">{{ $senior->senior_id_number ?? $senior->control_number }}</span></div>
                                        <div class="field"><span class="label">Date Issued:</span> <span class="value">{{ $senior->date_issued ? \Carbon\Carbon::parse($senior->date_issued)->format('M d, Y') : 'N/A' }}</span></div>
                                    </td>
                                </tr>
                            </table>
                            <table class="footer-table">
                                <tr>
                                    <td class="signature-section">
                                        <div class="signature-line">Signature of Holder</div>
                                        <div class="signature-label">Sign above line</div>
                                    </td>
                                    <td class="qr-cell">
                                        @if($senior->qr_code_image && file_exists(public_path($senior->qr_code_image)))
                                            <img src="{{ asset($senior->qr_code_image) }}" class="qr-code">
                                        @else
                                            <div class="qr-code" style="background: #f0f0f0; text-align: center; line-height: 14mm; font-size: 4pt;">QR</div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td width="20px"></td>
                <!-- BACK SIDE -->
                <td class="card">
                    <div class="card-inner">
                        <div class="watermark">OSCA</div>
                        <div class="card-content">
                            <div class="back-header">Emergency Contact Information</div>
                            <table class="back-body-table">
                                <tr>
                                    <td>
                                        <div class="section-title">Emergency Contact</div>
                                        <div class="back-field"><span class="back-label">Contact Person:</span> <span class="back-value">{{ $senior->emergency_contact_name ?? 'N/A' }}</span></div>
                                        <div class="back-field"><span class="back-label">Relationship:</span> <span class="back-value">{{ $senior->emergency_contact_relationship ?? 'N/A' }}</span></div>
                                        <div class="back-field"><span class="back-label">Contact Number:</span> <span class="back-value">{{ $senior->emergency_contact_number ?? 'N/A' }}</span></div>
                                        <div class="back-field"><span class="back-label">Address:</span> <span class="back-value">{{ $senior->address }}</span></div>
                                    </td>
                                    <td>
                                        <div class="section-title">Medical Info</div>
                                        <div class="back-field"><span class="back-label">Blood Type:</span> <span class="back-value">{{ $senior->blood_type ?? 'N/A' }}</span></div>
                                        <div class="back-field"><span class="back-label">PhilHealth No:</span> <span class="back-value">{{ $senior->philsys_number ?? 'N/A' }}</span></div>
                                        <div class="back-field"><span class="back-label">OSCA ID:</span> <span class="back-value">{{ $senior->osca_id ?? 'N/A' }}</span></div>
                                        <div class="back-field"><span class="back-label">Civil Status:</span> <span class="back-value">{{ $senior->civil_status ?? 'N/A' }}</span></div>
                                    </td>
                                </tr>
                            </table>
                            <table class="signatures-table">
                                <tr>
                                    <td>
                                        <div class="sign-line">OSCA Head</div>
                                        <div class="sign-subtitle">OSCA Silang Head Office</div>
                                    </td>
                                    <td>
                                        <div class="sign-line">MSWDO Officer</div>
                                        <div class="sign-subtitle">Authorized Signature</div>
                                    </td>
                                </tr>
                            </table>
                            <div class="back-footer">
                                If found, please return this card to the Office of the Senior Citizens Affairs, Municipality of Silang, Cavite.
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
