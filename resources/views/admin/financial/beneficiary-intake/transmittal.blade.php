<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transmittal Report - MSWDO Silang</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Transmittal CSS -->
    <link href="{{ asset('css/beneficiary-intake/transmittal.css') }}" rel="stylesheet">
</head>
<body class="transmittal-page">

    <!-- Web Control Top Action Bar (Hidden on Print) -->
    <div class="no-print bg-dark text-white py-3 px-4 shadow-sm mb-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.beneficiary-intake.index') }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Back to Intake Directory
                </a>
                <span class="text-white-50">|</span>
                <span class="fw-semibold">Transmittal Date: <span class="text-warning">{{ $transmittalDate }}</span></span>
                <span class="badge bg-primary rounded-pill px-3">{{ $transmittalSession }} Session</span>
                <span class="text-white-50">({{ count($intakes) }} Records)</span>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-warning fw-bold text-dark px-4 rounded-pill">
                    <i class="fas fa-print me-1"></i> Print Transmittal Report
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Document Wrapper -->
    <div class="transmittal-wrapper">

        <!-- Government Header -->
        <div class="transmittal-header">
            @if(file_exists(public_path('iservesilang1.ico')))
                <img src="{{ asset('iservesilang1.ico') }}" alt="Silang Seal" class="transmittal-logo">
            @elseif(file_exists(public_path('iservesilang.ico')))
                <img src="{{ asset('iservesilang.ico') }}" alt="Silang Seal" class="transmittal-logo">
            @endif
            
            <div class="header-govt-title">
                <div>Republic of the Philippines</div>
                <div>Province of Cavite</div>
                <div>Municipality of Silang, Cavite</div>
            </div>
            <div class="header-office-title">
                Municipal Social Welfare and Development Office
            </div>

            <!-- Transmittal Date & Session Subheader Line -->
            <div class="transmittal-meta-bar">
                <span>Transmittal</span>
                <span class="meta-field-underline">{{ $transmittalDate }}</span>
                <span class="meta-field-underline">{{ $transmittalSession }}</span>
            </div>
        </div>

        <!-- 8-Column Transmittal Data Grid -->
        <table class="transmittal-table">
            <thead>
                <tr>
                    <th class="col-surname">SURNAME</th>
                    <th class="col-firstname">FIRST NAME</th>
                    <th class="col-middlename">MIDDLE NAME</th>
                    <th class="col-purpose">PURPOSE</th>
                    <th class="col-barangay">BARANGAY</th>
                    <th class="col-contact">CONTACT NO.</th>
                    <th class="col-birthday">BIRTHDAY</th>
                    <th class="col-staff">STAFF</th>
                </tr>
            </thead>
            <tbody>
                @forelse($intakes as $intake)
                <tr>
                    <td class="col-surname">{{ mb_strtoupper($intake->beneficiary_last_name ?? '') }}</td>
                    <td class="col-firstname">{{ mb_strtoupper($intake->beneficiary_first_name ?? '') }}</td>
                    <td class="col-middlename">{{ mb_strtoupper($intake->beneficiary_middle_name ?? '') }}</td>
                    <td class="col-purpose">{{ mb_strtoupper($intake->display_assistance_purpose ?? 'N/A') }}</td>
                    <td class="col-barangay">{{ $intake->beneficiary_barangay ?? 'N/A' }}</td>
                    <td class="col-contact">{{ $intake->beneficiary_contact_number ?? '' }}</td>
                    <td class="col-birthday">{{ $intake->beneficiary_birthday ? $intake->beneficiary_birthday->format('m/d/y') : '' }}</td>
                    <td class="col-staff">{{ mb_strtoupper($intake->interviewed_by ?? $intake->encoderUser?->name ?? $staffName) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-3 text-muted">No intake records selected for this transmittal.</td>
                </tr>
                @endforelse

                <!-- Pad blank rows up to 15 rows for physical sheet uniformity -->
                @php
                    $rowCount = count($intakes);
                    $targetRows = max(15 - $rowCount, 0);
                @endphp
                @for($i = 0; $i < $targetRows; $i++)
                <tr>
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

    </div>

</body>
</html>
