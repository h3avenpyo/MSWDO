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
            background: #ffffff;
            vertical-align: top;
            border-radius: 2mm;
            overflow: hidden;
        }
        .card-inner {
            height: 100%;
            position: relative;
            background-image: radial-gradient(circle at 10% 20%, rgba(26, 35, 126, 0.03) 0%, rgba(251, 192, 45, 0.02) 90%);
        }
        /* Front Side Styles */
        .pvc-header {
            background-color: #1A237E;
            color: #ffffff;
            padding: 2mm 3mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 0.8mm solid #FBC02D;
        }
        .pvc-header-text {
            text-align: center;
            flex: 1;
        }
        .pvc-header-text h5 {
            font-size: 8pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .pvc-header-text p {
            font-size: 6pt;
            margin: 0;
            opacity: 0.9;
        }
        .pvc-header-text span {
            font-size: 6pt;
            font-weight: bold;
            color: #FBC02D;
            display: block;
            margin-top: 0.5mm;
        }
        .pvc-logo {
            width: 8.5mm;
            height: 8.5mm;
            object-fit: contain;
            border-radius: 50%;
            background-color: white;
            padding: 0.5mm;
        }
        .pvc-body {
            padding: 2.5mm 3.5mm;
            display: flex;
            height: calc(100% - 8mm);
        }
        .pvc-photo-section {
            width: 23mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5mm;
        }
        .pvc-photo {
            width: 21mm;
            height: 21mm;
            object-fit: cover;
            border: 2px solid #1A237E;
            border-radius: 1mm;
        }
        .pvc-id-num {
            font-size: 7.5pt;
            font-weight: bold;
            color: #D32F2F;
            background: rgba(211, 47, 47, 0.08);
            padding: 0.5mm 1.5mm;
            border-radius: 1mm;
            border: 1px solid rgba(211, 47, 47, 0.2);
            text-align: center;
            word-break: break-all;
            width: 100%;
        }
        .pvc-qr-code {
            width: 13mm;
            height: 13mm;
            object-fit: contain;
            margin-top: auto;
            border: 1px solid #E5E7EB;
            padding: 0.5mm;
            background: white;
        }
        .pvc-details {
            flex: 1;
            padding-left: 3mm;
            display: flex;
            flex-direction: column;
            gap: 1mm;
        }
        .pvc-name {
            font-size: 11pt;
            font-weight: bold;
            color: #111827;
            margin: 0;
            text-transform: uppercase;
            border-bottom: 1.5px solid #E5E7EB;
            padding-bottom: 0.5mm;
        }
        .pvc-field {
            display: flex;
            font-size: 7.5pt;
            margin: 0;
            line-height: 1.3;
        }
        .pvc-label {
            font-weight: bold;
            color: #4B5563;
            width: 17mm;
            text-transform: uppercase;
        }
        .pvc-value {
            color: #1F2937;
            flex: 1;
            font-weight: 500;
        }
        .pvc-signature-holder {
            margin-top: auto;
            border-top: 1px dashed #9CA3AF;
            text-align: center;
            font-size: 6pt;
            color: #6B7280;
            padding-top: 0.5mm;
            width: 80%;
            align-self: center;
        }
        /* Back Side Styles */
        .pvc-back-header {
            background-color: #F8FAFC;
            border-bottom: 2px solid #1A237E;
            padding: 2mm 3mm;
            text-align: center;
        }
        .pvc-back-header h6 {
            font-size: 7pt;
            font-weight: bold;
            color: #1A237E;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .pvc-back-body {
            padding: 2mm 3.5mm;
            display: flex;
            flex-direction: column;
            height: calc(100% - 6mm);
        }
        .pvc-back-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5mm;
            font-size: 7.5pt;
        }
        .pvc-back-field-full {
            grid-column: span 2;
        }
        .pvc-back-label {
            font-weight: bold;
            color: #4B5563;
            text-transform: uppercase;
            font-size: 6pt;
            margin-bottom: 0.3mm;
            display: block;
        }
        .pvc-back-value {
            color: #111827;
            font-weight: 600;
            display: block;
            line-height: 1.2;
        }
        .pvc-signatures-row {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 2mm;
        }
        .pvc-sign-block {
            text-align: center;
            width: 45%;
        }
        .pvc-sign-line {
            border-top: 1.5px solid #000000;
            padding-top: 0.5mm;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .pvc-sign-subtitle {
            font-size: 5.5pt;
            color: #4B5563;
        }
        .pvc-back-footer {
            background-color: #1A237E;
            color: #ffffff;
            font-size: 6pt;
            text-align: center;
            padding: 1mm 2mm;
            margin-top: auto;
            line-height: 1.2;
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
                    <div class="card-inner">
                        <div class="pvc-header">
                            <img src="{{ public_path('images/mswdo-logo.png') && file_exists(public_path('images/mswdo-logo.png')) ? asset('images/mswdo-logo.png') : 'https://ui-avatars.com/api/?name=Silang&background=fff&color=1A237E' }}" class="pvc-logo" alt="Seal">
                            <div class="pvc-header-text">
                                <h5>Republic of the Philippines</h5>
                                <p>Province of Cavite • Municipality of Silang</p>
                                <span>OFFICE OF THE SENIOR CITIZENS AFFAIRS</span>
                            </div>
                            <img src="{{ public_path('images/mswdo-logo.png') && file_exists(public_path('images/mswdo-logo.png')) ? asset('images/mswdo-logo.png') : 'https://ui-avatars.com/api/?name=OSCA&background=fff&color=1A237E' }}" class="pvc-logo" alt="OSCA">
                        </div>
                        <div class="pvc-body">
                            <div class="pvc-photo-section">
                                @if($senior->photo && file_exists(public_path($senior->photo)))
                                    <img src="{{ asset($senior->photo) }}" class="pvc-photo" alt="Photo">
                                @elseif($senior->avatar_image && file_exists(public_path($senior->avatar_image)))
                                    <img src="{{ asset($senior->avatar_image) }}" class="pvc-photo" alt="Photo">
                                @else
                                    <div class="pvc-photo" style="background: #1A237E; color: white; display: flex; align-items: center; justify-content: center; font-size: 10pt; font-weight: bold;">{{ strtoupper(substr($senior->full_name, 0, 2)) }}</div>
                                @endif
                                <div class="pvc-id-num">{{ $senior->senior_id_number }}</div>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($senior->qr_code) }}" class="pvc-qr-code" alt="QR Code">
                            </div>
                            <div class="pvc-details">
                                <p class="pvc-name">{{ $senior->full_name }}</p>
                                
                                <div class="pvc-field" style="margin-top: 1mm;">
                                    <span class="pvc-label">Birthdate:</span>
                                    <span class="pvc-value">{{ \Carbon\Carbon::parse($senior->birth_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="pvc-field">
                                    <span class="pvc-label">Age / Sex:</span>
                                    <span class="pvc-value">{{ $senior->age }} yrs old / {{ $senior->sex }}</span>
                                </div>
                                <div class="pvc-field">
                                    <span class="pvc-label">Barangay:</span>
                                    <span class="pvc-value">{{ $senior->barangay }}</span>
                                </div>
                                <div class="pvc-field">
                                    <span class="pvc-label">Issued:</span>
                                    <span class="pvc-value">{{ \Carbon\Carbon::parse($senior->date_issued)->format('M d, Y') }}</span>
                                </div>
                                
                                <div class="pvc-signature-holder">
                                    SIGNATURE OF HOLDER
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td width="20px"></td>
                <!-- BACK SIDE -->
                <td class="card">
                    <div class="card-inner">
                        <div class="pvc-back-header">
                            <h6>ID Card Terms &amp; Reference Information</h6>
                        </div>
                        <div class="pvc-back-body">
                            <div class="pvc-back-details">
                                <div class="pvc-back-field-full">
                                    <span class="pvc-back-label">Residential Address:</span>
                                    <span class="pvc-back-value">{{ $senior->address }}, {{ $senior->barangay }}, Silang, Cavite</span>
                                </div>
                                <div>
                                    <span class="pvc-back-label">Blood Type:</span>
                                    <span class="pvc-back-value">{{ $senior->blood_type ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="pvc-back-label">Civil Status:</span>
                                    <span class="pvc-back-value">{{ $senior->civil_status ?? 'N/A' }}</span>
                                </div>
                                <div class="pvc-back-field-full">
                                    <span class="pvc-back-label">Emergency Contact Name:</span>
                                    <span class="pvc-back-value">{{ $senior->emergency_contact_name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="pvc-back-label">Contact Number:</span>
                                    <span class="pvc-back-value">{{ $senior->emergency_contact_number ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="pvc-back-label">Relationship:</span>
                                    <span class="pvc-back-value">{{ $senior->emergency_contact_relationship ?? 'N/A' }}</span>
                                </div>
                            </div>
                            
                            <div class="pvc-signatures-row">
                                <div class="pvc-sign-block">
                                    @if($oscaHeadSignature && file_exists(public_path($oscaHeadSignature)))
                                        <img src="{{ asset($oscaHeadSignature) }}" style="max-height: 4mm; max-width: 100%; margin-bottom: 0.5mm;">
                                    @else
                                        <div style="height: 2mm;"></div>
                                    @endif
                                    <div class="pvc-sign-line">OSCA head</div>
                                    <div class="pvc-sign-subtitle">OSCA Silang Head Office</div>
                                </div>
                                <div class="pvc-sign-block">
                                    @if($mswdoOfficerSignature && file_exists(public_path($mswdoOfficerSignature)))
                                        <img src="{{ asset($mswdoOfficerSignature) }}" style="max-height: 4mm; max-width: 100%; margin-bottom: 0.5mm;">
                                    @else
                                        <div style="height: 2mm;"></div>
                                    @endif
                                    <div class="pvc-sign-line">MSWDO officer</div>
                                    <div class="pvc-sign-subtitle">Authorized Signature</div>
                                </div>
                            </div>
                            
                            <div class="pvc-back-footer">
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
