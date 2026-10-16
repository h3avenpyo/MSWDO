{{-- filepath: c:\Users\USER\WCPU_WEB\resources\views\desk-officer\referral-print.blade.php --}}
@php
    $department = strtoupper($referral->case->type ?? 'VAWC') === 'VAWC' ? 'SWD' : strtoupper($referral->case->type);
    $isBcpc = $department === 'BCPC';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>
        {{ $isBcpc ? 'BCPC - REFERRAL FOR SERVICE' : 'VAW Form 2 - Referral for Service' }}
    </title>
    
    <style>
        :root {
            --form-color: {{ $isBcpc ? '#dc2626' : '#7c3299' }};
        }
        @page {
            margin: 15mm 20mm;
            size: A4 portrait;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #222;
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 1px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .subheader {
            text-align: center;
            font-size: 10px;
            margin-bottom: 12px;
            font-weight: bold;
            color: var(--form-color);
            line-height: 1.3;
        }
        .section {
            margin-bottom: 12px;
            border: 1.2px solid var(--form-color);
            border-radius: 4px;
            padding: 12px 16px;
            background: #fafafa;
        }
        .row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
            min-height: 20px;
        }
        .col {
            display: table-cell;
            vertical-align: middle;
            padding: 2px 4px;
        }
        .col-25 { width: 25%; }
        .col-33 { width: 33.33%; }
        .col-50 { width: 50%; }
        .col-75 { width: 75%; }
        .col-100 { width: 100%; }
        .label {
            font-weight: bold;
            color: #222;
            font-size: 11px;
            white-space: nowrap;
            margin-right: 4px;
        }
        .value {
            border-bottom: 1px solid #444;
            display: inline-block;
            min-width: 80px;
            min-height: 16px;
            padding: 1px 4px;
            vertical-align: middle;
        }
        .value-full {
            width: calc(100% - 10px);
        }
        .small {
            font-size: 9px;
            color: #666;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .signature-section {
            margin-top: 35px;
            display: table;
            width: 100%;
        }
        .sig-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }
        .sigline {
            border-top: 1.5px solid #444;
            margin: 25px auto 5px auto;
            width: 200px;
        }
        .siglabel {
            color: #666;
            font-size: 10px;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            color: #999;
            margin-top: 20px;
            line-height: 1.4;
        }
    </style>
</head>
@php
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
@endphp
<body>
    <table style="width: 100%; border: none; margin-bottom: 12px; border-collapse: collapse;">
        <tr>
            <td style="width: 65px; vertical-align: middle; text-align: left; border: none; padding: 0;">
                @if($mswdoLogoBase64)
                    <img src="{{ $mswdoLogoBase64 }}" style="width: 55px; height: 55px;" alt="MSWDO Logo">
                @endif
            </td>
            <td style="vertical-align: middle; text-align: center; border: none; padding: 0;">
                <div class="header">Republic of the Philippines</div>
                <div class="header">
                    {{ $isBcpc ? 'BARANGAY COUNCIL FOR THE PROTECTION OF CHILDREN' : 'Women and Children Protection Unit' }}
                </div>
                <div class="subheader" style="margin-bottom: 0;">
                    {{ $isBcpc ? 'BCPC - REFERRAL FOR SERVICE' : 'VAW Form 2 - Referral for Service' }}<br>
                    CONFIDENTIAL
                </div>
            </td>
            <td style="width: 65px; border: none; padding: 0;"></td>
        </tr>
    </table>
    <div class="section">
        <div class="row">
            <div class="col col-50">
                <span class="label">Case No.:</span>
                <span class="value" style="width:60%;">{{ $referral->case->case_number ?? '' }}</span>
            </div>
            <div class="col col-50">
                <span class="label">Date of Referral:</span>
                <span class="value" style="width:55%;">{{ $referral->date_of_referral->format('F d, Y') }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">To:</span>
                <span class="value value-full">{{ $referral->recipient_name }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Address:</span>
                <span class="value value-full">{{ $referral->recipient_address ?? '' }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Contact Person:</span>
                <span class="value value-full">{{ $referral->recipient_name }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Name of Client:</span>
                <span class="value value-full">{{ censorLastName($referral->client_name) }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-33">
                <span class="label">Age:</span>
                <span class="value" style="width:50%;">{{ $referral->client_age }}</span>
            </div>
            <div class="col col-33">
                <span class="label">Sex:</span>
                <span class="value" style="width:55%;">{{ $referral->client_sex }}</span>
            </div>
            <div class="col col-33">
                <span class="label">Address:</span>
                <span class="value" style="width:45%;">{{ $referral->client_address }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-50">
                <span class="label">Name of Family/Guardian:</span>
                <span class="value" style="width:50%;">{{ censorLastName($referral->family_guardian_name) }}</span>
            </div>
            <div class="col col-50">
                <span class="label">Contact No.:</span>
                <span class="value" style="width:60%;">{{ $referral->family_guardian_contact }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Address:</span>
                <span class="value value-full">{{ $referral->family_guardian_address }}</span>
            </div>
        </div>
        
        <div class="row" style="margin-top:8px;">
            <div class="col col-100">
                <span class="label">Reason/s for Referral:</span>
            </div>
        </div>
        <div class="row">
            <div class="col col-100">
                <span class="value value-full">{{ $referral->reason_for_referral }}</span>
            </div>
        </div>
        
        <div class="row" style="margin-top:8px;">
            <div class="col col-100">
                <span class="label">Specific Service/s Requested:</span>
            </div>
        </div>
        <div class="row">
            <div class="col col-100">
                <span class="value value-full">{{ $referral->specific_services_requested }}</span>
            </div>
        </div>
    </div>
    
    <div class="section">
        <div class="small">
            Please refer to attached report/intake form/case summary for more information. Feedback requested and send to Referring Party/Agency:
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Address:</span>
                <span class="value value-full">{{ $referral->referred_by_address }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Cell Phone No.:</span>
                <span class="value value-full">{{ $referral->referred_by_contact }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Email address:</span>
                <span class="value value-full">{{ $referral->referred_by_email }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Contact Person:</span>
                <span class="value value-full">{{ $referral->referred_by_name }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col col-100">
                <span class="label">Referred by:</span>
                <span class="value value-full">{{ $referral->referred_by_name }}</span>
            </div>
        </div>
    </div>
    
    <div class="signature-section">
        <div class="sig-col">
            <div class="sigline"></div>
            <div class="siglabel">Signature over Printed Name</div>
        </div>
        <div class="sig-col">
            <div class="sigline"></div>
            <div class="siglabel">Designation</div>
        </div>
    </div>
    
    <div class="footer">
        <div>
            {{ $isBcpc ? 'BCPC - Referral for Service' : 'VAW Form 2 - Referral for Service' }}
            | {{ $isBcpc ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System
        </div>
    </div>
@include('print.edit-controls')
</body>
</html>
