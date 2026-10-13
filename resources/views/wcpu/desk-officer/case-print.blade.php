<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Case {{ $case->case_number }} - Official Report</title>
<style>
/* A4 portrait with comfortable margins for official letter */
/* UPDATED: comfortable 15mm 20mm margins */
@page { size: A4 portrait; margin: 15mm 20mm; counter-increment: page; }
html,body{margin:0;padding:0;height:100%;font-family: "Times New Roman", Times, serif;color:#000;}
/* Slightly larger base font for better readability */
body{font-size:13px;line-height:1.22}

/* Header - centered identity */
.header {
	display:flex;
	flex-direction:column;
	align-items:center;
	justify-content:center;
	gap:4px;
	margin-bottom:8px;
	text-align:center;
}
.gov-left, .gov-right {
	text-align:center;
	font-size:13px;
	line-height:1.15;
}
.header-center { display:none; } /* optional emblem placeholder hidden for centered header */

.report-title {
	text-align:center;
	margin:8px 0 12px;
}
.report-title h1{
	font-size:20px;
	letter-spacing:1px;
	margin:0;
}
.report-sub { text-align:center; font-size:12px; color:#333; margin-bottom:6px; }

/* Main container */
/* UPDATED: small top padding inside container for extra spacing from page edge */
.container{max-width:190mm;margin:0 auto;padding:4mm 3mm 13mm;}

/* Metadata table */
.meta-table, .data-table {
	width:100%;
	border-collapse:collapse;
	margin-bottom:8px;
}
.meta-table th, .meta-table td, .data-table th, .data-table td {
	border:1px solid #000;
	padding:7px 9px;
	vertical-align:middle;
	font-size:11px;
	text-align:left;
	height:32px;
}
.meta-table th { width:28%; background:#f2f2f2; font-weight:700; text-transform:uppercase; font-size:11px; }
.data-table th { width:28%; background:#f2f2f2; font-weight:700; }
.section-title { font-weight:700; font-size:12px; margin:8px 0 5px; text-transform:uppercase; }

/* Two-column compact grid */
.row { display:flex; gap:10px; margin-bottom:6px; }
.col { flex:1; min-width:0; }
.kv { display:flex; gap:6px; align-items:flex-start; }
.kv .k { width:30%; font-weight:700; font-size:12px; }
.kv .v { font-size:12px; word-break:break-word; }

/* Paragraphs in pre-wrap */
.prewrap { white-space:pre-wrap; word-wrap:break-word; hyphens:auto; font-size:12px; }

/* Two-column section layout */
.two-column-container { 
	display:flex; 
	gap:12px; 
	margin-bottom:8px;
	page-break-inside:avoid;
}
.two-column-container .column { 
	flex:1; 
	min-width:0;
	box-sizing:border-box;
}
.two-column-container .section-title { margin-top:0; }

/* Print helpers */
@media print {
	.two-column-container { 
		display:flex !important; 
		-webkit-print-color-adjust:exact;
		print-color-adjust:exact;
	}
	.two-column-container .column {
		flex:1 !important;
	}
}

/* Signature: use table to guarantee side-by-side placement and avoid breaks */
.sig-table {
	width:100%;
	border-collapse:collapse;
	margin-top:20px;
	page-break-inside:avoid;
	-webkit-column-break-inside:avoid;
	-webkit-page-break-inside:avoid;
}
.sig-table td {
	width:50%;
	vertical-align:bottom;
	text-align:center;
	padding-top:12px;
}
.sig-line {
	display:block;
	margin:34px auto 10px;
	width:70%;
	border-top:1px solid #000;
	height:1px;
}
.small-muted{font-size:12px;color:#666;}

/* Footer - fixed and small */
.footer {
	position:fixed;
	bottom:4mm;
	left:20mm;
	right:20mm;
	font-size:8px;
	line-height:1.2;
	color:#333;
	text-align:center;
	padding-top:4px;
	border-top:1px solid #e0e0e0;
	background:transparent;
}

/* Reduce spacing for long reports */
.table-compact th, .table-compact td { padding:7px 9px; font-size:12px; }

/* Print helpers */
@media print {
	.header, .footer { -webkit-print-color-adjust:exact; color-adjust:exact; }
}
</style>
@php
	$isBcpcCase = strtoupper((string) ($case->type ?? 'VAWC')) === 'BCPC';
	$mswdoLogoPath = public_path('images/mswdo_logo.jpg');
	$silangLogoPath = public_path('images/silang.png');
	$mswdoLogoBase64 = file_exists($mswdoLogoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($mswdoLogoPath)) : '';
	$silangLogoBase64 = file_exists($silangLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($silangLogoPath)) : '';
@endphp
</head>
<body>
<div class="container">

	<table data-print-locked style="width: 100%; border: none; margin-bottom: 10px; border-collapse: collapse;">
		<tr>
			<td style="width: 56px; vertical-align: middle; text-align: right; border: none; padding: 0 2px 0 0;">
				@if($mswdoLogoBase64)
					<img src="{{ $mswdoLogoBase64 }}" style="width: 48px; height: 48px;" alt="MSWDO Logo">
				@endif
			</td>
			<td style="vertical-align: middle; text-align: center; border: none; padding: 0;">
				<div style="font-weight:700; font-size:13px;">REPUBLIC OF THE PHILIPPINES</div>
				<div style="font-size:12px;">Province of Cavite &bull; Municipality of Silang</div>
				<div style="font-weight:700; font-size:13px; margin-top:2px;">BARANGAY {{ strtoupper($case->barangay->name ?? 'N/A') }}</div>
				<div style="font-size:11px;">{{ $isBcpcCase ? 'Barangay Council for the Protection of Children (BCPC)' : 'Social Welfare and Development (SWD)' }}</div>
			</td>
			<td style="width: 56px; vertical-align: middle; text-align: left; border: none; padding: 0 0 0 2px;">
				@if($silangLogoBase64)
					<img src="{{ $silangLogoBase64 }}" style="width: 48px; height: 48px;" alt="Bayan ng Silang Logo">
				@endif
			</td>
		</tr>
	</table>

	<div class="report-title">
		<h1>{{ $isBcpcCase ? 'BCPC Case Report' : 'Barangay Case Report' }}</h1>
	</div>
	<div class="report-sub" data-print-locked>{{ $isBcpcCase ? 'Official record of reported BCPC cases' : 'Official record of reported SWD incidents' }} — Confidential</div>

	<!-- Top metadata -->
	@php
	    // detect explicit case reference fields (if present on the model)
	    $caseRef = $case->case_ref ?? $case->case_reference ?? $case->reference ?? null;
	@endphp

	<table class="meta-table table-compact">
		<tr>
			<th>Case Number</th>
			<td data-print-locked>{{ $case->case_number ?? 'N/A' }}</td>
			<th>Date Filed</th>
			<td data-print-locked>{{ $case->created_at ? $case->created_at->format('F j, Y \a\t g:i A') : 'N/A' }}</td>
		</tr>
		<tr>
			<th>Status</th>
			<td data-print-locked>{{ $case->status ?? 'N/A' }}</td>
			<th>Type of Abuse</th>
			<td data-print-locked>{{ $case->abuse_type ?? 'N/A' }}</td>
		</tr>
		@if($caseRef && (string)$caseRef !== (string)$case->id)
		<tr>
			<th>Case Ref.</th>
			<td colspan="3">{{ $caseRef }}</td>
		</tr>
		@endif
	</table>

	<!-- Victim & Perpetrator -->
	@php
		$hasVictimGuardian = $case->guardian || $case->guardian_contact || $case->guardian_address || $case->relationship_to_victim;
		$hasPerpetratorGuardian = $case->guardian_abuser_name || $case->guardian_abuser_contact || $case->guardian_abuser_address || $case->relationship_to_abuser;
	@endphp

	@if($hasVictimGuardian || $hasPerpetratorGuardian)
		{{-- If any guardian exists, show each person with their guardian --}}
		<table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
			<tr>
				<td style="width:50%; vertical-align:top; padding-right:6px; height:200px;">
					<div class="section-title">Victim Information</div>
					<table class="data-table table-compact">
						<tr><th>Full Name</th><td>{{ censorLastName($case->victim_name) }}</td></tr>
						<tr>
							<th>Age / Gender</th>
							<td>{{ $case->victim_age ?? 'N/A' }} / {{ $case->victim_gender ?? 'N/A' }}</td>
						</tr>
						<tr><th>Contact / Email</th><td>{{ $case->victim_contact ?? 'N/A' }} {{ $case->victim_email ? ' / '.$case->victim_email : '' }}</td></tr>
						<tr><th>Address</th><td class="prewrap">{{ $case->victim_address ?? 'N/A' }}</td></tr>
					</table>
			</td>
			@if($hasVictimGuardian)
			<td style="width:50%; vertical-align:top; padding-left:6px; height:200px;">
				<div class="section-title">Guardian of Victim Information</div>
					<table class="data-table table-compact">
						@if($case->guardian)
						<tr><th>Guardian Name</th><td>{{ censorLastName($case->guardian) }}</td></tr>
						@endif
						@if($case->guardian_contact)
						<tr><th>Guardian Contact</th><td>{{ $case->guardian_contact }}</td></tr>
						@endif
						@if($case->guardian_address)
						<tr><th>Guardian Address</th><td>{{ $case->guardian_address }}</td></tr>
						@endif
						@if($case->relationship_to_victim)
						<tr><th>Relationship to Victim</th><td>{{ $case->relationship_to_victim }}</td></tr>
						@endif
					</table>
				</td>
				@endif
			</tr>
		</table>

	<table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
		<tr>
			<td style="width:50%; vertical-align:top; padding-right:6px; height:200px;">
				<div class="section-title">Perpetrator Information</div>
					<table class="data-table table-compact">
						<tr><th>Full Name</th><td>{{ censorLastName($case->abuser_name) }}</td></tr>
						<tr><th>Age</th><td>{{ $case->abuser_age ?? 'N/A' }}</td></tr>
						<tr><th>Relationship</th><td>{{ $case->abuse_relationship ?? 'N/A' }}</td></tr>
					</table>
			</td>
			@if($hasPerpetratorGuardian)
			<td style="width:50%; vertical-align:top; padding-left:6px; height:200px;">
				<div class="section-title">Guardian of Perpetrator Information</div>
					<table class="data-table table-compact">
						@if($case->guardian_abuser_name)
						<tr><th>Guardian Name</th><td>{{ censorLastName($case->guardian_abuser_name) }}</td></tr>
						@endif
						@if($case->guardian_abuser_contact)
						<tr><th>Guardian Contact</th><td>{{ $case->guardian_abuser_contact }}</td></tr>
						@endif
						@if($case->guardian_abuser_address)
						<tr><th>Guardian Address</th><td>{{ $case->guardian_abuser_address }}</td></tr>
						@endif
					@if($case->relationship_to_abuser)
					<tr><th>Relationship to Perpetrator</th><td>{{ $case->relationship_to_abuser }}</td></tr>
					@endif
				</table>
			</td>
			@else
			<td style="width:50%; vertical-align:top; padding-left:6px; height:200px;">
				<div class="section-title">Incident Details</div>
				<table class="data-table table-compact">
					<tr><th>Date of Incident</th><td>{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'N/A' }}</td></tr>
					<tr><th>Time</th><td>{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('h:i A') : 'N/A' }}</td></tr>
						<tr><th>Location</th><td class="prewrap">{{ $case->incident_location ?? 'N/A' }}</td></tr>
						<tr><th>Details</th><td class="prewrap">{{ $case->incident_details ?? 'N/A' }}</td></tr>
					</table>
				</td>
				@endif
			</tr>
		</table>

	@if($hasPerpetratorGuardian)
	{{-- If perpetrator has guardian, show incident details and case management in next row --}}
	<table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
		<tr>
			<td style="width:50%; vertical-align:top; padding-right:6px; height:200px;">
				<div class="section-title">Incident Details</div>
					<table class="data-table table-compact">
						<tr><th>Date of Incident</th><td>{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'N/A' }}</td></tr>
						<tr><th>Time</th><td>{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('h:i A') : 'N/A' }}</td></tr>
						<tr><th>Location</th><td class="prewrap">{{ $case->incident_location ?? 'N/A' }}</td></tr>
					<tr><th>Details</th><td class="prewrap">{{ $case->incident_details ?? 'N/A' }}</td></tr>
				</table>
			</td>
				<td style="width:50%; vertical-align:top; padding-left:6px; height:200px;">
					<div class="section-title">Case Management</div>
					<table class="data-table table-compact">
						<tr><th>Created</th><td>{{ $case->created_at ? $case->created_at->format('F j, Y \a\t g:i A') : 'N/A' }}</td></tr>
						<tr><th>Last Updated</th><td>{{ $case->updated_at ? $case->updated_at->format('F j, Y \a\t g:i A') : 'N/A' }}</td></tr>
						<tr><th>Assigned Officer / Created By</th><td>{{ $case->assignedTo->full_name ?? 'N/A' }} / {{ $case->createdBy->full_name ?? 'System' }}</td></tr>
					</table>
				</td>
			</tr>
		</table>
		@else
		{{-- If no perpetrator guardian, incident details already shown, just show case management --}}
		<div class="section-title">Case Management</div>
		<table class="data-table table-compact">
			<tr><th>Created</th><td>{{ $case->created_at ? $case->created_at->format('F j, Y \a\t g:i A') : 'N/A' }}</td></tr>
			<tr><th>Last Updated</th><td>{{ $case->updated_at ? $case->updated_at->format('F j, Y \a\t g:i A') : 'N/A' }}</td></tr>
			<tr><th>Assigned Officer / Created By</th><td>{{ $case->assignedTo->full_name ?? 'N/A' }} / {{ $case->createdBy->full_name ?? 'System' }}</td></tr>
		</table>
		@endif
	@else
		{{-- If no guardians, show victim and perpetrator side-by-side --}}
		<table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
			<tr>
				<td style="width:50%; vertical-align:top; padding-right:6px; height:200px;">
					<div class="section-title">Victim Information</div>
					<table class="data-table table-compact">
						<tr><th>Full Name</th><td>{{ censorLastName($case->victim_name) }}</td></tr>
						<tr>
							<th>Age / Gender</th>
							<td>{{ $case->victim_age ?? 'N/A' }} / {{ $case->victim_gender ?? 'N/A' }}</td>
						</tr>
						<tr><th>Contact / Email</th><td>{{ $case->victim_contact ?? 'N/A' }} {{ $case->victim_email ? ' / '.$case->victim_email : '' }}</td></tr>
						<tr><th>Address</th><td class="prewrap">{{ $case->victim_address ?? 'N/A' }}</td></tr>
					</table>
				</td>
				<td style="width:50%; vertical-align:top; padding-left:6px; height:200px;">
					<div class="section-title">Perpetrator Information</div>
					<table class="data-table table-compact">
						<tr><th>Full Name</th><td>{{ censorLastName($case->abuser_name) }}</td></tr>
						<tr><th>Age</th><td>{{ $case->abuser_age ?? 'N/A' }}</td></tr>
						<tr><th>Relationship</th><td>{{ $case->abuse_relationship ?? 'N/A' }}</td></tr>
					</table>
				</td>
			</tr>
		</table>

		<table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
			<tr>
				<td style="width:50%; vertical-align:top; padding-right:6px; height:200px;">
					<div class="section-title">Incident Details</div>
					<table class="data-table table-compact">
						<tr><th>Date of Incident</th><td>{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') : 'N/A' }}</td></tr>
						<tr><th>Time</th><td>{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('h:i A') : 'N/A' }}</td></tr>
						<tr><th>Location</th><td class="prewrap">{{ $case->incident_location ?? 'N/A' }}</td></tr>
						<tr><th>Details</th><td class="prewrap">{{ $case->incident_details ?? 'N/A' }}</td></tr>
					</table>
				</td>
				<td style="width:50%; vertical-align:top; padding-left:6px; height:200px;">
					<div class="section-title">Case Management</div>
					<table class="data-table table-compact">
						<tr><th>Created</th><td>{{ $case->created_at ? $case->created_at->format('F j, Y \a\t g:i A') : 'N/A' }}</td></tr>
						<tr><th>Last Updated</th><td>{{ $case->updated_at ? $case->updated_at->format('F j, Y \a\t g:i A') : 'N/A' }}</td></tr>
						<tr><th>Assigned Officer / Created By</th><td>{{ $case->assignedTo->full_name ?? 'N/A' }} / {{ $case->createdBy->full_name ?? 'System' }}</td></tr>
					</table>
				</td>
			</tr>
		</table>
	@endif

	@php
		$isBCPCCase = $case->type === 'BCPC';
		$bcpcData = [];
		if ($isBCPCCase && $case->case_details) {
			$decoded = json_decode($case->case_details, true);
			if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
				$bcpcData = $decoded;
			}
		}
	@endphp

	@if($isBCPCCase && !empty($bcpcData))
		@php
			$hasCrimesAgainstChildren = false;
			if(isset($bcpcData['bcpc_crimes_against_children']) && !empty($bcpcData['bcpc_crimes_against_children'])) {
				foreach($bcpcData['bcpc_crimes_against_children'] as $crimeKey => $yearData) {
					if($crimeKey !== 'total') {
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
							$hasCrimesAgainstChildren = true;
							break;
						}
					}
				}
			}
		@endphp
		@if($hasCrimesAgainstChildren)
		<div class="section-title">BCPC Case Information</div>
		<table class="data-table table-compact" style="margin-bottom: 8px;">
			<tr>
				<th colspan="3" style="text-align: center; background: #f2f2f2;">Crimes Against Children</th>
			</tr>
			<tr>
				<th>Crime Type</th>
				<th>Year 2023 (M/F/Total)</th>
				<th>Year 2024 (M/F/Total)</th>
			</tr>
			@if(isset($bcpcData['bcpc_crimes_against_children']))
				@foreach($bcpcData['bcpc_crimes_against_children'] as $crimeKey => $yearData)
					@php
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						$hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
					@endphp
					@if($crimeKey !== 'total' && $hasRowData)
					<tr>
						<td>{{ ucfirst(str_replace('_', ' ', $crimeKey)) }}</td>
						<td>
							{{ $male2023 }} / 
							{{ $female2023 }} / 
							{{ $male2023 + $female2023 }}
						</td>
						<td>
							{{ $male2024 }} / 
							{{ $female2024 }} / 
							{{ $male2024 + $female2024 }}
						</td>
					</tr>
					@endif
				@endforeach
			@endif
		</table>
		@endif

		@php
			$hasCiclCrimes = false;
			if(isset($bcpcData['bcpc_cicl_crimes']) && !empty($bcpcData['bcpc_cicl_crimes'])) {
				foreach($bcpcData['bcpc_cicl_crimes'] as $crimeKey => $yearData) {
					if($crimeKey !== 'total') {
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
							$hasCiclCrimes = true;
							break;
						}
					}
				}
			}
		@endphp
		@if($hasCiclCrimes)
		<table class="data-table table-compact" style="margin-bottom: 8px;">
			<tr>
				<th colspan="3" style="text-align: center; background: #f2f2f2;">CICL Crimes</th>
			</tr>
			<tr>
				<th>Crime Type</th>
				<th>Year 2023 (M/F/Total)</th>
				<th>Year 2024 (M/F/Total)</th>
			</tr>
			@if(isset($bcpcData['bcpc_cicl_crimes']))
				@foreach($bcpcData['bcpc_cicl_crimes'] as $crimeKey => $yearData)
					@php
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						$hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
					@endphp
					@if($crimeKey !== 'total' && $hasRowData)
					<tr>
						<td>{{ ucfirst(str_replace('_', ' ', $crimeKey)) }}</td>
						<td>
							{{ $male2023 }} / 
							{{ $female2023 }} / 
							{{ $male2023 + $female2023 }}
						</td>
						<td>
							{{ $male2024 }} / 
							{{ $female2024 }} / 
							{{ $male2024 + $female2024 }}
						</td>
					</tr>
					@endif
				@endforeach
			@endif
		</table>
		@endif

		@php
			$hasCiclStatus = false;
			if(isset($bcpcData['bcpc_cicl_status']) && !empty($bcpcData['bcpc_cicl_status'])) {
				foreach($bcpcData['bcpc_cicl_status'] as $categoryKey => $yearData) {
					if($categoryKey !== 'total') {
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
							$hasCiclStatus = true;
							break;
						}
					}
				}
			}
		@endphp
		@if($hasCiclStatus)
		<table class="data-table table-compact" style="margin-bottom: 8px;">
			<tr>
				<th colspan="3" style="text-align: center; background: #f2f2f2;">CICL Status</th>
			</tr>
			<tr>
				<th>Category</th>
				<th>Year 2023 (M/F/Total)</th>
				<th>Year 2024 (M/F/Total)</th>
			</tr>
			@if(isset($bcpcData['bcpc_cicl_status']))
				@foreach($bcpcData['bcpc_cicl_status'] as $categoryKey => $yearData)
					@php
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						$hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
					@endphp
					@if($categoryKey !== 'total' && $hasRowData)
					<tr>
						<td>{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
						<td>
							{{ $male2023 }} / 
							{{ $female2023 }} / 
							{{ $male2023 + $female2023 }}
						</td>
						<td>
							{{ $male2024 }} / 
							{{ $female2024 }} / 
							{{ $male2024 + $female2024 }}
						</td>
					</tr>
					@endif
				@endforeach
			@endif
		</table>
		@endif

		@php
			$hasChildrenAtRisk = false;
			if(isset($bcpcData['bcpc_children_at_risk']) && !empty($bcpcData['bcpc_children_at_risk'])) {
				foreach($bcpcData['bcpc_children_at_risk'] as $categoryKey => $yearData) {
					if($categoryKey !== 'total') {
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
							$hasChildrenAtRisk = true;
							break;
						}
					}
				}
			}
		@endphp
		@if($hasChildrenAtRisk)
		<table class="data-table table-compact" style="margin-bottom: 8px;">
			<tr>
				<th colspan="3" style="text-align: center; background: #f2f2f2;">Children at Risk</th>
			</tr>
			<tr>
				<th>Category</th>
				<th>Year 2023 (M/F/Total)</th>
				<th>Year 2024 (M/F/Total)</th>
			</tr>
			@if(isset($bcpcData['bcpc_children_at_risk']))
				@foreach($bcpcData['bcpc_children_at_risk'] as $categoryKey => $yearData)
					@php
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						$hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
					@endphp
					@if($categoryKey !== 'total' && $hasRowData)
					<tr>
						<td>{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
						<td>
							{{ $male2023 }} / 
							{{ $female2023 }} / 
							{{ $male2023 + $female2023 }}
						</td>
						<td>
							{{ $male2024 }} / 
							{{ $female2024 }} / 
							{{ $male2024 + $female2024 }}
						</td>
					</tr>
					@endif
				@endforeach
			@endif
		</table>
		@endif

		@php
			$hasSpecialProtection = false;
			if(isset($bcpcData['bcpc_special_protection']) && !empty($bcpcData['bcpc_special_protection'])) {
				foreach($bcpcData['bcpc_special_protection'] as $categoryKey => $yearData) {
					if($categoryKey !== 'total') {
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
							$hasSpecialProtection = true;
							break;
						}
					}
				}
			}
		@endphp
		@if($hasSpecialProtection)
		<table class="data-table table-compact" style="margin-bottom: 8px;">
			<tr>
				<th colspan="3" style="text-align: center; background: #f2f2f2;">Special Protection</th>
			</tr>
			<tr>
				<th>Category</th>
				<th>Year 2023 (M/F/Total)</th>
				<th>Year 2024 (M/F/Total)</th>
			</tr>
			@if(isset($bcpcData['bcpc_special_protection']))
				@foreach($bcpcData['bcpc_special_protection'] as $categoryKey => $yearData)
					@php
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						$hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
					@endphp
					@if($categoryKey !== 'total' && $hasRowData)
					<tr>
						<td>{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</td>
						<td>
							{{ $male2023 }} / 
							{{ $female2023 }} / 
							{{ $male2023 + $female2023 }}
						</td>
						<td>
							{{ $male2024 }} / 
							{{ $female2024 }} / 
							{{ $male2024 + $female2024 }}
						</td>
					</tr>
					@endif
				@endforeach
			@endif
		</table>
		@endif

		@php
			$hasOrganizations = false;
			if(isset($bcpcData['bcpc_organizations']) && !empty($bcpcData['bcpc_organizations'])) {
				foreach($bcpcData['bcpc_organizations'] as $orgKey => $yearData) {
					if($orgKey !== 'total') {
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						if($male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0) {
							$hasOrganizations = true;
							break;
						}
					}
				}
			}
		@endphp
		@if($hasOrganizations)
		<table class="data-table table-compact" style="margin-bottom: 8px;">
			<tr>
				<th colspan="3" style="text-align: center; background: #f2f2f2;">Organizations</th>
			</tr>
			<tr>
				<th>Organization</th>
				<th>Year 2023 (M/F/Total)</th>
				<th>Year 2024 (M/F/Total)</th>
			</tr>
			@if(isset($bcpcData['bcpc_organizations']))
				@foreach($bcpcData['bcpc_organizations'] as $orgKey => $yearData)
					@php
						$male2023 = $yearData['year_2023']['male'] ?? 0;
						$female2023 = $yearData['year_2023']['female'] ?? 0;
						$male2024 = $yearData['year_2024']['male'] ?? 0;
						$female2024 = $yearData['year_2024']['female'] ?? 0;
						$hasRowData = $male2023 > 0 || $female2023 > 0 || $male2024 > 0 || $female2024 > 0;
					@endphp
					@if($orgKey !== 'total' && $hasRowData)
					<tr>
						<td>{{ ucfirst(str_replace('_', ' ', $orgKey)) }}</td>
						<td>
							{{ $male2023 }} / 
							{{ $female2023 }} / 
							{{ $male2023 + $female2023 }}
						</td>
						<td>
							{{ $male2024 }} / 
							{{ $female2024 }} / 
							{{ $male2024 + $female2024 }}
						</td>
					</tr>
					@endif
				@endforeach
			@endif
		</table>
		@endif
	@endif

	{{-- Replace existing signatures block with two-cell table --}}
	<table class="sig-table no-print">
		<tr>
			<td data-print-locked>
				<div style="font-weight:700;">Prepared by:</div>
				<span class="sig-line"></span>
				<div class="small-muted">{{ (($isBcpcAdminPrint ?? false) || auth()->user()?->isBcpcAdmin()) ? 'BCPC Administrator' : ((($isSwdAdminPrint ?? false) || auth()->user()?->isVawcAdmin()) ? 'SWD Administrator' : ($isBcpcCase ? 'BCPC Desk Officer' : 'SWD Desk Officer')) }}</div>
			</td>
			<td data-print-locked>
				<div style="font-weight:700;">Approved by:</div>
				<span class="sig-line"></span>
				<div class="small-muted">Barangay Captain</div>
			</td>
		</tr>
	</table>

</div>

<footer class="footer">
	<div>Case Report | {{ strtoupper((string) ($case->type ?? '')) === 'BCPC' ? 'Barangay Council for the Protection of Children' : 'Women and Children Protection Unit' }} | Generated by iServe Silang Case Management System</div>
</footer>
@include('print.edit-controls')
</body>
</html>
