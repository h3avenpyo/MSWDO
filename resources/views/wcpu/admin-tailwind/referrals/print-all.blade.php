@php $isBcpcReferralRegistry = strtoupper((string) ((auth()->user()?->isDepartmentAdmin() ? auth()->user()->defaultCaseDepartment() : null) ?? $referrals->first()?->case?->type ?? $department ?? auth()->user()?->defaultCaseDepartment() ?? (auth()->user()?->isBcpcAdmin() ? 'BCPC' : 'VAWC'))) === 'BCPC'; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Referral Registry</title>
    <style>
        @page { size: A4 landscape; margin: 14mm 12mm 17mm; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #252a31;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.3;
        }

        .header-table,
        .referrals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            width: 78%;
            margin: 0 auto 10px;
        }

        .header-table td {
            padding: 0;
            border: 0;
            vertical-align: middle;
        }

        .logo-cell {
            width: 76px;
            text-align: center;
        }

        .logo {
            display: block;
            width: 62px;
            height: 62px;
            margin: 0 auto;
            object-fit: contain;
        }

        .header-copy {
            padding: 0 10px;
            text-align: center;
        }

        .republic {
            color: #1c1c1c;
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .municipality {
            margin-top: 2px;
            color: #1c1c1c;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .province {
            margin-top: 1px;
            color: #4d5359;
            font-family: "Times New Roman", Times, serif;
            font-size: 9pt;
            font-style: italic;
        }

        .office {
            margin-top: 6px;
            color: #2b3036;
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .report-title {
            margin-top: 7px;
            padding: 5px 0;
            border-top: 1.5px solid #9b1b30;
            border-bottom: 1.5px solid #9b1b30;
            color: #8f172c;
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .report-meta {
            margin: 0 0 10px;
            padding: 6px 10px;
            border: 1px solid #d6dbe0;
            background: #f7f8fa;
            color: #59636d;
            font-size: 8pt;
            text-align: center;
        }

        .referrals-table {
            border: 1px solid #c8cfd6;
            table-layout: fixed;
        }

        .referrals-table thead { display: table-header-group; }
        .referrals-table tr { page-break-inside: avoid; }

        .referrals-table th {
            padding: 7px 5px;
            border: 1px solid #c8cfd6;
            background: #9b1b30;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            line-height: 1.25;
            text-align: center;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .referrals-table td {
            padding: 7px 5px;
            border: 1px solid #d8dde2;
            color: #303841;
            font-size: 7.8pt;
            overflow-wrap: break-word;
            text-align: left;
            vertical-align: middle;
        }

        .referrals-table tbody tr:nth-child(even) td { background: #f8f9fa; }
        .center { text-align: center !important; }
        .sex-column { overflow-wrap: normal !important; white-space: nowrap; }

        .empty-state {
            padding: 24px;
            border: 1px solid #d8dde2;
            color: #66717b;
            font-size: 9pt;
            text-align: center;
        }

        .footer {
            position: fixed;
            right: 0;
            bottom: -10mm;
            left: 0;
            border-top: 1px solid #d8dde2;
            color: #6b737b;
            font-size: 7.5pt;
            text-align: center;
        }
    </style>
</head>
@php
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $silangLogoPath = public_path('images/silang.png');
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
    $silangLogoBase64 = file_exists($silangLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($silangLogoPath)) : '';
@endphp
<body data-pdf-print-url="{{ route('admin.referrals.print-edited-pdf') }}" data-csrf-token="{{ csrf_token() }}">

    <table class="header-table" role="presentation">
        <tr>
            <td class="logo-cell">
                @if($mswdoLogoBase64)
                    <img class="logo" src="{{ $mswdoLogoBase64 }}" alt="MSWDO Logo">
                @endif
            </td>
            <td class="header-copy">
                <div class="republic">Republic of the Philippines</div>
                <div class="municipality">Municipality of Silang</div>
                <div class="province">Province of Cavite</div>
                <div class="office">{{ $isBcpcReferralRegistry ? 'Barangay Council for the Protection of Children (BCPC)' : 'Social Welfare and Development (SWD)' }}</div>
                <div class="report-title">Referral Registry</div>
            </td>
            <td class="logo-cell">
                @if($silangLogoBase64)
                    <img class="logo" src="{{ $silangLogoBase64 }}" alt="Bayan ng Silang Logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="report-meta">Referral Registry | Generated {{ now()->format('F d, Y') }} | Total Referrals: {{ $referrals->count() }}</div>

    @if($referrals->count())
        <table class="referrals-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Name of Client</th>
                    <th style="width: 4%;">Age</th>
                    <th class="sex-column" style="width: 6%;">Sex</th>
                    <th style="width: 10%;">Service/s Needed</th>
                    <th style="width: 10%;">Referring / Receiving Agency</th>
                    <th style="width: 8%;">From / To Whom</th>
                    <th style="width: 8%;">Date of Referral</th>
                    <th style="width: 8%;">Date Referral Received / Sent</th>
                    <th style="width: 15%;">Action Taken by Receiving Organization</th>
                    <th style="width: 8%;">By Whom</th>
                    <th style="width: 11%;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referrals as $referral)
                    <tr>
                        <td>{{ $referral->client_name }}</td>
                        <td class="center">{{ $referral->client_age }}</td>
                        <td class="center sex-column">{{ $referral->client_sex }}</td>
                        <td>{{ $referral->specific_services_requested }}</td>
                        <td>{{ $referral->recipient_name }}</td>
                        <td>{{ $referral->referred_by_name }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($referral->date_of_referral)->format('M d, Y') }}</td>
                        <td class="center">{{ $referral->feedback_received_at ? \Carbon\Carbon::parse($referral->feedback_received_at)->format('M d, Y') : '' }}</td>
                        <td>{{ $referral->feedback ?? '' }}</td>
                        <td>{{ $referral->referredBy->first_name ?? '' }}</td>
                        <td>{{ $referral->additional_notes ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">No referrals are available for this report.</div>
    @endif

    <div class="footer">Referral Registry | {{ $isBcpcReferralRegistry ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System</div>
@include('print.edit-controls')
</body>
</html>
