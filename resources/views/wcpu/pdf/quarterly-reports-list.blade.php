@php
    $isBcpcReport = strtoupper((string) ($reportType ?? 'VAWC')) === 'BCPC';
    $isSwdAdminPrint = !$isBcpcReport && auth()->user()?->isVawcAdmin();
    $departmentLabel = $isBcpcReport ? 'BCPC' : 'SWD';
@endphp
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>{{ $departmentLabel }} Quarterly Reports</title><style>
@page { size: legal landscape; margin: 15mm 20mm; }
* { box-sizing: border-box; } body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; } table { border-collapse: collapse; }
.header { width: 92%; margin: 0 auto 20px; border-bottom: 2px solid #000; }.header td { border: 0; padding: 10px 2px; text-align: center; vertical-align: middle; }
.republic { font-size: 11pt; letter-spacing: 2px; font-weight: bold; }.municipality { font-size: 12pt; font-weight: bold; margin-top: 4px; }.province { font-style: italic; margin-top: 3px; }.office { font-size: 11pt; font-weight: bold; margin-top: 10px; }.title { font-size: 13pt; font-weight: bold; margin-top: 12px; color: #1a5f1a; }
.info { width: 100%; margin-bottom: 15px; background: #f9f9f9; border: 1px solid #ddd; }.info td { border: 0; padding: 8px 12px; }
.report { width: 100%; table-layout: fixed; border: 2px solid #000; }.report th,.report td { border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle; }.report th { background: #e8d4b8; font-weight: bold; }.report th:first-child { background: #d4a574; text-align: left; }.report td:first-child,.report td:nth-child(5) { text-align: left; }
.signatures { width: 100%; margin-top: 40px; }.signatures td { width: 33.33%; border: 0; text-align: center; padding: 20px 15px; vertical-align: top; }.sig-label { margin-bottom: 50px; font-weight: bold; }.sig-line { border-bottom: 2px solid #000; width: 200px; margin: 0 auto 10px; }.sig-name { font-weight: bold; }.sig-title { font-style: italic; color: #555; }
</style></head><body>
@php
    $mswdo = public_path('images/mswdo_logo.jpg'); $silang = public_path('images/silang.png');
    $mswdoImage = file_exists($mswdo) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdo)) : '';
    $silangImage = file_exists($silang) ? 'data:image/png;base64,' . base64_encode(file_get_contents($silang)) : '';
@endphp
<table class="header"><tr><td style="width:50px">@if($mswdoImage)<img src="{{ $mswdoImage }}" style="width:48px;height:48px">@endif</td><td><div class="republic">REPUBLIC OF THE PHILIPPINES</div><div class="municipality">MUNICIPALITY OF SILANG</div><div class="province">Province of Cavite</div><div class="office">{{ $isBcpcReport ? 'BARANGAY COUNCIL FOR THE PROTECTION OF CHILDREN (BCPC)' : 'WOMEN AND CHILDREN PROTECTION UNIT (WCPU)' }}</div><div class="title">QUARTERLY COMPLIANCE MONITORING REPORT RE: {{ $departmentLabel }}</div></td><td style="width:50px">@if($silangImage)<img src="{{ $silangImage }}" style="width:48px;height:48px">@endif</td></tr></table>
<table class="info"><tr><td @if($isBcpcReport || $isSwdAdminPrint) data-print-locked @endif><strong>Municipality:</strong> Silang, Cavite</td><td style="text-align:right" @if($isBcpcReport || $isSwdAdminPrint) data-print-locked @endif><strong>Period Covered:</strong> {{ request('quarter') ? 'Q' . request('quarter') . ' ' : '' }}{{ request('year') ?? 'All Years' }}<br><strong>Region IV-A</strong></td></tr></table>
<table class="report"><thead><tr><th style="width:22%">BARANGAY</th><th style="width:12%">PERIOD</th><th style="width:10%">TYPE</th><th style="width:10%">CASES</th><th style="width:24%">SUBMITTED BY</th><th style="width:22%">DATE SUBMITTED</th></tr></thead><tbody>
@forelse($reports as $report)<tr><td>{{ $report->barangay?->name ?? 'N/A' }}</td><td>Q{{ $report->quarter }} {{ $report->year }}</td><td>{{ $departmentLabel }}</td><td>{{ data_get($report->report_data, 'total_cases', 0) }}</td><td>{{ trim(($report->user?->first_name ?? '') . ' ' . ($report->user?->last_name ?? '')) ?: 'N/A' }}</td><td>{{ $report->created_at?->format('M d, Y') }}</td></tr>
@empty<tr><td colspan="6" style="padding:20px;color:#666">No quarterly reports found.</td></tr>@endforelse
</tbody></table>
<table class="signatures"><tr><td @if($isBcpcReport || $isSwdAdminPrint) data-print-locked @endif><div class="sig-label">Accomplished by:</div><div class="sig-line"></div><div class="sig-name">{{ $isBcpcReport ? 'BCPC SILANG' : 'SWD SILANG' }}</div><div class="sig-title">{{ $isBcpcReport ? 'BCPC Administrator' : ($isSwdAdminPrint ? 'SWD Administrator' : 'WCPU Desk Officer') }}</div></td><td><div class="sig-label">Noted by:</div><div class="sig-line"></div><div class="sig-title">Municipal Mayor</div></td><td><div class="sig-label">Recorded by:</div><div class="sig-line"></div><div class="sig-title">Municipal Secretary</div></td></tr></table>
<div style="position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8pt;">Quarterly Compliance Monitoring Report | {{ $isBcpcReport ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System</div>
@include('print.edit-controls')
</body></html>
