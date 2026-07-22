<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Senior Citizen ID - MSWDO Silang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1A237E;
            --primary-dark: #121858;
            --accent: #FBC02D;
            --success: #166534;
            --success-bg: #DCFCE7;
            --background: #F8FAFC;
        }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1F2937;
            padding: 2rem 1rem;
        }

        .verify-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .verify-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .verify-header {
            background: var(--primary);
            color: #ffffff;
            padding: 1.5rem;
            text-align: center;
            border-bottom: 4px solid var(--accent);
        }

        .verify-header h1 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .verify-header p {
            font-size: 0.75rem;
            margin: 0.25rem 0 0 0;
            opacity: 0.9;
        }

        .status-badge {
            background-color: var(--success-bg);
            color: var(--success);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1.5rem 0;
            border: 1px solid rgba(22, 101, 52, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .photo-wrapper {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto;
        }

        .senior-photo {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .detail-row {
            display: flex;
            border-bottom: 1px solid #F1F5F9;
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 700;
            color: #4B5563;
            width: 140px;
            text-transform: uppercase;
            font-size: 0.75rem;
            align-self: center;
        }

        .detail-value {
            color: #1F2937;
            font-weight: 500;
            flex: 1;
        }

        .verify-footer {
            text-align: center;
            padding: 1.5rem;
            color: #6B7280;
            font-size: 0.75rem;
            background-color: #F9FAFB;
            border-top: 1px solid #E5E7EB;
        }
    </style>
</head>
<body>

<div class="verify-container">
    
    <!-- Header Seal/Brand -->
    <div class="text-center mb-4">
        <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--primary); margin: 0;">MSWDO SILANG</h2>
        <p class="text-muted small">Online Credential Verification System</p>
    </div>

    <!-- Verification Card -->
    <div class="verify-card">
        
        <div class="verify-header">
            <h1>Office of the Senior Citizens Affairs</h1>
            <p>Municipality of Silang, Cavite</p>
        </div>

        <div class="text-center p-4 pb-0">
            <!-- Verified Badge -->
            @if($senior->status->value == 'active')
                <div class="status-badge">
                    <i class="fas fa-check-circle"></i> Verified Active Member
                </div>
            @else
                <div class="status-badge bg-warning-subtle text-warning-emphasis border-warning-subtle">
                    <i class="fas fa-exclamation-circle"></i> Status: {{ ucfirst($senior->status->value) }}
                </div>
            @endif

            <!-- Profile Photo -->
            <div class="photo-wrapper">
                @if($senior->photo && file_exists(public_path($senior->photo)))
                    <img src="{{ asset($senior->photo) }}" class="senior-photo" alt="Photo">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($senior->full_name) }}&background=1A237E&color=fff&size=128" class="senior-photo" alt="Avatar">
                @endif
            </div>
            
            <h3 class="mt-3 mb-1" style="font-size: 1.25rem; font-weight: 800; color: #111827; text-transform: uppercase;">{{ $senior->full_name }}</h3>
            <p class="text-danger fw-bold small mb-4">{{ $senior->senior_id_number ?? 'No ID Generated' }}</p>
        </div>

        <div class="verify-details">
            <div class="detail-row">
                <span class="detail-label">Control No:</span>
                <span class="detail-value">{{ $senior->control_number ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Barangay:</span>
                <span class="detail-value">{{ $senior->barangay ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Address:</span>
                <span class="detail-value">{{ $senior->address ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Birth Date:</span>
                <span class="detail-value">{{ $senior->birth_date ? \Carbon\Carbon::parse($senior->birth_date)->format('F d, Y') : '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Age / Sex:</span>
                <span class="detail-value">{{ $senior->age ?? '-' }} years old / {{ $senior->sex ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Blood Type:</span>
                <span class="detail-value">{{ $senior->blood_type ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Civil Status:</span>
                <span class="detail-value">{{ $senior->civil_status ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date Issued:</span>
                <span class="detail-value">{{ $senior->date_issued ? \Carbon\Carbon::parse($senior->date_issued)->format('F d, Y') : '-' }}</span>
            </div>
        </div>

        <div class="verify-footer">
            <i class="fas fa-shield-alt text-success me-1"></i> This record is securely fetched from the MSWDO Silang database.
        </div>

    </div>

</div>

</body>
</html>
