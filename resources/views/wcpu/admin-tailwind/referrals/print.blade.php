<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    @php $isBcpcReferral = strtoupper((string) ($referral->case->type ?? '')) === 'BCPC'; $referralDepartmentName = $isBcpcReferral ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit'; $referralFormName = $isBcpcReferral ? 'BCPC - Referral for Service' : 'SWD Form 2 - Referral for Service'; @endphp
    <title>{{ $referralFormName }}</title>
    <style>
        @page { size: A4 portrait; margin: 8mm 11mm 12mm; }
        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #252a31;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.6pt;
            line-height: 1.2;
        }

        .header-table { width: 88%; margin: 0 auto 7px; border-collapse: collapse; }
        .header-table td { padding: 0; border: 0; vertical-align: middle; }
        .logo-cell { width: 55px; text-align: center; }
        .logo { display: block; width: 50px; height: 50px; margin: 0 auto; object-fit: contain; }
        .header-copy { padding: 0 6px; text-align: center; }
        .republic { color: #1d1d1d; font-family: "Times New Roman", Times, serif; font-size: 9.5pt; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .municipality { margin-top: 1px; font-family: "Times New Roman", Times, serif; font-size: 9.5pt; font-weight: bold; text-transform: uppercase; }
        .office { margin-top: 2px; font-family: "Times New Roman", Times, serif; font-size: 8.5pt; font-weight: bold; text-transform: uppercase; }
        .form-title { margin-top: 4px; padding: 3px 0; border-top: 1px solid #9b1b30; border-bottom: 1px solid #9b1b30; color: #8f172c; font-family: "Times New Roman", Times, serif; font-size: 9pt; font-weight: bold; letter-spacing: .3px; text-transform: uppercase; }

        .section {
            margin-bottom: 6px;
            padding: 6px 8px;
            border: 1px solid #cbd2d9;
            page-break-inside: avoid;
        }

        .section-caption {
            margin: -6px -8px 5px;
            padding: 4px 7px;
            background: #9b1b30;
            color: #ffffff;
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .row { display: table; width: 100%; margin-bottom: 3px; }
        .row:last-child { margin-bottom: 0; }
        .col { display: table-cell; padding: 0 3px; vertical-align: middle; }
        .col-33 { width: 33.33%; }
        .col-50 { width: 50%; }
        .col-100 { width: 100%; }
        .label { color: #353b42; font-size: 7.2pt; font-weight: bold; white-space: nowrap; }
        .value { display: inline-block; min-width: 40px; min-height: 12px; padding: 1px 3px; border-bottom: 1px solid #7b848d; color: #252a31; vertical-align: middle; }
        .value-full { width: calc(100% - 10px); }
        .small { margin-bottom: 5px; color: #59636d; font-size: 6.8pt; line-height: 1.2; }

        .signature-section { display: table; width: 100%; margin-top: 12px; page-break-inside: avoid; }
        .sig-col { display: table-cell; width: 50%; padding: 0 20px; text-align: center; vertical-align: top; }
        .sigline { width: 165px; margin: 16px auto 3px; border-top: 1px solid #3e454c; }
        .siglabel { color: #59636d; font-size: 6.8pt; }
        .footer { position: fixed; right: 0; bottom: -7mm; left: 0; border-top: 1px solid #d8dde2; color: #6b737b; font-size: 6.5pt; line-height: 1.2; text-align: center; }
    </style>
</head>
@php
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $silangLogoPath = public_path('images/silang.png');
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
    $silangLogoBase64 = file_exists($silangLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($silangLogoPath)) : '';
    $displayValue = static function (...$values) {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') return $value;
            if (is_numeric($value)) return $value;
        }
        return 'Not provided';
    };
    $caseNumber = $displayValue($referral->case?->case_number);
    $referralDate = $referral->date_of_referral?->format('F d, Y') ?? 'Not provided';
    $recipientName = $displayValue($referral->recipient_name, $referral->recipient?->name);
    $recipientAddress = $displayValue($referral->recipient_address, $referral->recipient?->getAttribute('address'));
    $clientName = $displayValue($referral->client_name, $referral->case?->victim_name);
    $clientAge = $displayValue($referral->client_age, $referral->case?->victim_age);
    $clientSex = $displayValue($referral->client_sex, $referral->case?->victim_gender);
    $clientAddress = $displayValue($referral->client_address, $referral->case?->victim_address);
    $guardianName = $displayValue($referral->family_guardian_name, $referral->case?->guardian);
    $guardianContact = $displayValue($referral->family_guardian_contact, $referral->case?->guardian_contact);
    $guardianAddress = $displayValue($referral->family_guardian_address, $referral->case?->guardian_address);
@endphp
<body>
    <table class="header-table" role="presentation">
        <tr>
            <td class="logo-cell">
                @if($mswdoLogoBase64)
                    <img class="logo" src="{{ $mswdoLogoBase64 }}" alt="MSWDO Logo">
                @endif
            </td>
            <td class="header-copy">
                <div class="republic">Republic of the Philippines</div>
                <div class="municipality">Municipality of Silang, Province of Cavite</div>
                <div class="office">{{ $isBcpcReferral ? 'BARANGAY COUNCIL FOR THE PROTECTION OF CHILDREN' : 'Women and Children Protection Unit' }}</div>
                <div class="form-title">{{ $isBcpcReferral ? 'BCPC - REFERRAL FOR SERVICE' : 'SWD Form 2 - Referral for Service' }}<br>Confidential</div>
            </td>
            <td class="logo-cell">
                @if($silangLogoBase64)
                    <img class="logo" src="{{ $silangLogoBase64 }}" alt="Bayan ng Silang Logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-caption">Referral Information</div>
        <div class="row">
            <div class="col col-50">
                <span class="label">Case No.:</span>
                <span class="value" style="width:60%;">{{ $caseNumber }}</span>
            </div>
            <div class="col col-50">
                <span class="label">Date of Referral:</span>
                <span class="value" style="width:55%;">{{ $referralDate }}</span>
            </div>
        </div>
        <div class="row"><div class="col col-100"><span class="label">To:</span> <span class="value value-full">{{ $recipientName }}</span></div></div>
        <div class="row"><div class="col col-100"><span class="label">Name of Client:</span> <span class="value value-full">{{ $clientName }}</span></div></div>
        <div class="row">
            <div class="col col-33"><span class="label">Age:</span> <span class="value" style="width:50%;">{{ $clientAge }}</span></div>
            <div class="col col-33"><span class="label">Sex:</span> <span class="value" style="width:55%;">{{ $clientSex }}</span></div>
            <div class="col col-33"><span class="label">Address:</span> <span class="value" style="width:45%;">{{ $clientAddress }}</span></div>
        </div>
        <div class="row">
            <div class="col col-50"><span class="label">Name of Family/Guardian:</span> <span class="value" style="width:50%;">{{ $guardianName }}</span></div>
            <div class="col col-50"><span class="label">Contact No.:</span> <span class="value" style="width:60%;">{{ $guardianContact }}</span></div>
        </div>
        <div class="row"><div class="col col-100"><span class="label">Address:</span> <span class="value value-full">{{ $guardianAddress }}</span></div></div>
        <div class="row" style="margin-top:4px;"><div class="col col-100"><span class="label">Reason/s for Referral:</span></div></div>
        <div class="row"><div class="col col-100"><span class="value value-full">{{ $displayValue($referral->reason_for_referral) }}</span></div></div>
        <div class="row" style="margin-top:4px;"><div class="col col-100"><span class="label">Specific Service/s Requested:</span></div></div>
        <div class="row"><div class="col col-100"><span class="value value-full">{{ $displayValue($referral->specific_services_requested) }}</span></div></div>
    </div>

    <div class="section">
        <div class="section-caption">Referring Party / Agency</div>
        <div class="small">Please refer to attached report/intake form/case summary for more information. Feedback requested and send to Referring Party/Agency:</div>
        <div class="row"><div class="col col-100"><span class="label">Address:</span> <span class="value value-full">{{ $displayValue($referral->referred_by_address) }}</span></div></div>
        <div class="row"><div class="col col-100"><span class="label">Cell Phone No.:</span> <span class="value value-full">{{ $displayValue($referral->referred_by_contact) }}</span></div></div>
        <div class="row"><div class="col col-100"><span class="label">Email address:</span> <span class="value value-full">{{ $displayValue($referral->referred_by_email) }}</span></div></div>
        <div class="row"><div class="col col-100"><span class="label">Contact Person:</span> <span class="value value-full">{{ $displayValue($referral->referred_by_name) }}</span></div></div>
        <div class="row"><div class="col col-100"><span class="label">Referred by:</span> <span class="value value-full">{{ $displayValue($referral->referred_by_name) }}</span></div></div>
    </div>

    <div class="signature-section">
        <div class="sig-col"><div class="sigline"></div><div class="siglabel">Signature over Printed Name</div></div>
        <div class="sig-col"><div class="sigline"></div><div class="siglabel">Designation</div></div>
    </div>

    <div class="footer">
        <div>{{ $referralFormName }} | {{ $referralDepartmentName }} | Generated by iServe Silang Case Management System</div>
    </div>
@include('print.edit-controls')
</body>
</html>
