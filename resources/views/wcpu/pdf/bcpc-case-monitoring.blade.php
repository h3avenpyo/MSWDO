<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BCPC Case Monitoring - {{ $barangay->name }}</title>
    <style>
        @page { size: legal landscape; margin: 11mm 12mm 14mm; }
        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: #252a31;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            line-height: 1.25;
        }

        .header-table { width: 82%; margin: 0 auto 9px; border-collapse: collapse; }
        .header-table td { padding: 0; border: 0; vertical-align: middle; }
        .logo-cell { width: 58px; text-align: center; }
        .logo { display: block; width: 52px; height: 52px; margin: 0 auto; object-fit: contain; }
        .header-copy { padding: 0 8px; text-align: center; }
        .republic { color: #1d1d1d; font-family: "Times New Roman", Times, serif; font-size: 9pt; font-weight: bold; letter-spacing: 1.2px; text-transform: uppercase; }
        .municipality { margin-top: 1px; font-family: "Times New Roman", Times, serif; font-size: 10.5pt; font-weight: bold; text-transform: uppercase; }
        .province { margin-top: 1px; color: #515960; font-family: "Times New Roman", Times, serif; font-size: 8pt; font-style: italic; }
        .report-title { margin-top: 5px; padding: 4px 0; border-top: 1.5px solid #9b1b30; border-bottom: 1.5px solid #9b1b30; color: #8f172c; font-family: "Times New Roman", Times, serif; font-size: 11pt; font-weight: bold; letter-spacing: .4px; text-transform: uppercase; }
        .report-meta { margin: 0 0 10px; padding: 5px 8px; border: 1px solid #d6dce1; background: #f7f8fa; color: #555f68; font-size: 7.5pt; text-align: center; }

        .monitoring-section { margin-bottom: 12px; page-break-inside: avoid; }
        .section-title { margin: 0; padding: 6px 8px; border: 1px solid #9b1b30; border-bottom: 0; background: #9b1b30; color: #fff; font-size: 9pt; font-weight: bold; }
        .note { margin: 0; padding: 4px 8px; border: 1px solid #d5dbe0; border-top: 0; background: #fafbfc; color: #5f6871; font-size: 7pt; font-style: italic; }

        .monitoring-table { width: 100%; border-collapse: collapse; border: 1px solid #c7cfd6; table-layout: fixed; }
        .monitoring-table thead { display: table-header-group; }
        .monitoring-table tr { page-break-inside: avoid; }
        .monitoring-table th { padding: 5px 3px; border: 1px solid #c7cfd6; background: #efe3d2; color: #29231e; font-size: 7pt; font-weight: bold; line-height: 1.15; text-align: center; vertical-align: middle; }
        .monitoring-table thead tr:first-child th { background: #ded0bb; }
        .monitoring-table td { padding: 4px 3px; border: 1px solid #d8dde2; color: #303841; font-size: 7pt; text-align: center; vertical-align: middle; }
        .monitoring-table tbody tr:nth-child(even) td { background: #f8f9fa; }
        .label-cell { background: #fcfcfd; font-weight: bold; text-align: left !important; }
        .total-row td { background: #f4e9eb !important; color: #7d1628; font-weight: bold; }

        .organizations-table { width: 100%; border-collapse: collapse; border: 1px solid #c7cfd6; table-layout: fixed; }
        .organizations-table thead { display: table-header-group; }
        .organizations-table tr { page-break-inside: avoid; }
        .organizations-table th { padding: 5px 4px; border: 1px solid #c7cfd6; background: #efe3d2; color: #29231e; font-size: 7pt; font-weight: bold; text-align: center; }
        .organizations-table td { min-height: 20px; padding: 5px 4px; border: 1px solid #d8dde2; color: #303841; font-size: 7pt; overflow-wrap: break-word; vertical-align: middle; }
        .organizations-table tbody tr:nth-child(even) td { background: #f8f9fa; }

        .footer { position: fixed; right: 0; bottom: -9mm; left: 0; border-top: 1px solid #d8dde2; color: #6b737b; font-size: 6.8pt; text-align: center; }
    </style>
</head>
@php
    $mswdoLogoPath = public_path('images/mswdo_logo.jpg');
    $silangLogoPath = public_path('images/silang.png');
    $mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
    $silangLogoBase64 = file_exists($silangLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($silangLogoPath)) : '';
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
                <div class="municipality">Municipality of Silang</div>
                <div class="province">Province of Cavite</div>
                <div class="report-title">BCPC Case Monitoring Report</div>
            </td>
            <td class="logo-cell">
                @if($silangLogoBase64)
                    <img class="logo" src="{{ $silangLogoBase64 }}" alt="Bayan ng Silang Logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="report-meta">
        Barangay: <strong>{{ $barangay->name }}</strong>
        &nbsp; | &nbsp; Period: <strong>Q{{ $quarter }} {{ $year }}</strong>
        &nbsp; | &nbsp; Generated: {{ now('Asia/Manila')->format('F j, Y g:i A') }}
    </div>

    @foreach($tables as $table)
        <section class="monitoring-section">
            <h2 class="section-title">{{ $table['title'] }}</h2>
            <p class="note">{{ $table['note'] }}</p>
            <table class="monitoring-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 37%;">{{ $table['label'] }}</th>
                        @foreach($yearGroups as $label)
                            <th colspan="3">{{ str_replace(['Year ', 'Total '], '', $label) }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($yearGroups as $yearKey => $label)
                            @foreach($sexColumns as $sexLabel)
                                <th style="width: 7%;">{{ $sexLabel === 'Male' ? 'M' : ($sexLabel === 'Female' ? 'F' : 'Total') }}</th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($table['rows'] as $rowKey => $rowLabel)
                        <tr class="{{ $rowKey === 'total' ? 'total-row' : '' }}">
                            <td class="label-cell">{{ $rowLabel }}</td>
                            @foreach($yearGroups as $yearKey => $label)
                                @foreach($sexColumns as $sexKey => $sexLabel)
                                    <td>{{ data_get($data, $table['name'].'.'.$rowKey.'.'.$yearKey.'.'.$sexKey, 0) }}</td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endforeach

    <section class="monitoring-section">
        <h2 class="section-title">Child Protection Organizations</h2>
        <p class="note">List of NGOs, CSOs, FBOs, POs, and Children/Youth Organizations working on child protection.</p>
        <table class="organizations-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Organization</th>
                    <th style="width: 30%;">Person &amp; Designation</th>
                    <th style="width: 20%;">Email</th>
                    <th style="width: 20%;">Contact</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 3; $i++)
                    <tr>
                        <td>{{ data_get($data, 'bcpc_organizations.'.$i.'.name') ?? '-' }}</td>
                        <td>{{ data_get($data, 'bcpc_organizations.'.$i.'.responsible_person') ?? '-' }}</td>
                        <td>{{ data_get($data, 'bcpc_organizations.'.$i.'.email') ?? '-' }}</td>
                        <td>{{ data_get($data, 'bcpc_organizations.'.$i.'.phone') ?? '-' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </section>

    <div class="footer">BCPC Case Monitoring Report | Barangay Council for the Protection of Children | Generated by iServe Silang Case Management System</div>
@include('print.edit-controls')
</body>
</html>
