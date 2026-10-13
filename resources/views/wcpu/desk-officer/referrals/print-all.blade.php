<!-- filepath: c:\Users\USER\WCPU_WEB\resources\views\desk-officer\referrals\print-all.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>VAW Form 4 - Referral Registry</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: right;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .instructions {
            border: 1px solid #000;
            padding: 5px;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .instructions-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 11px;
            line-height: 1.2;
            height: 42px;
        }
        td {
            font-size: 11px;
            text-align: left;
            min-height: 26px;
        }
        .footer {
            margin-top: 8px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
@php
    $isBcpcReferralRegistry = strtoupper((string) ($referrals->first()?->case?->type ?? $department ?? auth()->user()?->defaultCaseDepartment())) === 'BCPC';
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
                <div style="font-size: 10pt;">Republic of the Philippines &bull; {{ $isBcpcReferralRegistry ? 'Barangay Council for the Protection of Children (BCPC)' : 'Women and Children Protection Unit (WCPU)' }}</div>
                <div style="font-size: 16pt; font-weight: bold; margin-top: 2px;">REFERRAL REGISTRY</div>
            </td>
            <td style="width: 65px; vertical-align: middle; text-align: right; border: none; padding: 0; font-size: 9pt; color: #888; font-weight: bold;">
                CONFIDENTIAL
            </td>
        </tr>
    </table>
    
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Name of client</th>
                <th style="width: 4%;">Age</th>
                <th style="width: 4%;">Sex</th>
                <th style="width: 10%;">Service/s<br>needed</th>
                <th style="width: 10%;">Referring/<br>Receiving agency</th>
                <th style="width: 8%;">From/to<br>whom</th>
                <th style="width: 8%;">Date of<br>referral</th>
                <th style="width: 8%;">Date referral<br>received/sent</th>
                <th style="width: 15%;">Action taken by<br>receiving<br>organization</th>
                <th style="width: 8%;">By<br>whom</th>
                <th style="width: 13%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($referrals as $referral)
            <tr>
                <td>{{ censorLastName($referral->client_name) }}</td>
                <td style="text-align: center;">{{ $referral->client_age }}</td>
                <td style="text-align: center;">{{ $referral->client_sex }}</td>
                <td>{{ $referral->specific_services_requested }}</td>
                <td>{{ $referral->recipient_name }}</td>
                <td>{{ $referral->referred_by_name }}</td>
                <td style="text-align: center;">{{ \Carbon\Carbon::parse($referral->date_of_referral)->format('M d, Y') }}</td>
                <td style="text-align: center;">{{ $referral->feedback_received_at ? \Carbon\Carbon::parse($referral->feedback_received_at)->format('M d, Y') : '' }}</td>
                <td>{{ $referral->feedback ?? '' }}</td>
                <td>{{ $referral->referredBy->first_name ?? '' }}</td>
                <td>{{ $referral->additional_notes ?? '' }}</td>
            </tr>
            @endforeach
            @for($i = count($referrals); $i < 10; $i++)
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>
    
    <div class="footer">
        Referral Registry | {{ $isBcpcReferralRegistry ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System
    </div>
@include('print.edit-controls')
</body>
</html>
