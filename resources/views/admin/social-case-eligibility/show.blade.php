<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Eligibility Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1A237E;
            --success: #16A34A;
            --danger: #DC2626;
            --background: #F8FAFC;
            --card: #FFFFFF;
            --border: #E5E7EB;
            --text: #111827;
        }
        body { background: var(--background); color: var(--text); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; }
        .container { max-width: 1100px; margin: 0 auto; padding: 2rem 1rem; }
        .status-card { border-radius: 18px; border: 1px solid var(--border); box-shadow: 0 16px 40px rgba(15, 23, 42, .05); }
        .status-card-header { display: flex; justify-content: space-between; align-items: center; }
        .status-title { font-size: 1.25rem; font-weight: 700; margin: 0; }
        .badge-state { font-size: .9rem; padding: .6rem 1rem; border-radius: 999px; }
        .badge-eligible { background: rgba(22, 163, 74, .12); color: #166534; }
        .badge-not-eligible { background: rgba(220, 38, 38, .12); color: #991B1B; }
        .summary-list { list-style: none; padding: 0; margin: 0; }
        .summary-list li { padding: 1rem 0; border-bottom: 1px solid var(--border); }
        .summary-list li:last-child { border-bottom: 0; }
        .summary-list .label { display: block; color: #6B7280; font-size: .9rem; }
        .summary-list .value { font-weight: 700; font-size: 1rem; }
        .action-group { display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 1.5rem; }
        .btn-block { flex: 1 1 220px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="mb-4">
            <a href="/admin/social-case" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i> Back to Eligibility Validation</a>
        </div>
        <div class="card status-card p-4">
            <div class="status-card-header mb-3">
                <div>
                    <p class="status-title">Eligibility validation for {{ $client->full_name }}</p>
                    <p class="text-muted small">Birthdate: {{ $client->birthdate->format('M d, Y') }}</p>
                </div>
                <span class="badge-state {{ $eligible ? 'badge-eligible' : 'badge-not-eligible' }}">
                    {{ $eligible ? 'ELIGIBLE' : 'NOT ELIGIBLE' }}
                </span>
            </div>

            @if($eligible)
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Client is eligible to proceed.
                </div>
            @else
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    This client has already received assistance within the last six (6) months and is currently not eligible.
                </div>
            @endif

            <ul class="summary-list">
                <li>
                    <span class="label">Client Name</span>
                    <span class="value">{{ $client->full_name }}</span>
                </li>
                <li>
                    <span class="label">Last Assistance Type</span>
                    <span class="value">{{ $latestAssistance?->assistance_type ?? 'None' }}</span>
                </li>
                <li>
                    <span class="label">Release Date</span>
                    <span class="value">{{ $latestAssistance?->release_date?->format('M d, Y') ?? 'None' }}</span>
                </li>
                <li>
                    <span class="label">Eligible Again Date</span>
                    <span class="value">{{ $eligibleAgainDate?->format('M d, Y') ?? 'N/A' }}</span>
                </li>
                @unless($eligible)
                <li>
                    <span class="label">Remaining Waiting Period</span>
                    <span class="value">{{ $eligibleAgainDate ? now()->diffForHumans($eligibleAgainDate, ['parts' => 3, 'short' => true, 'syntax' => now()->lessThan($eligibleAgainDate) ? 1 : 0]) : 'N/A' }}</span>
                </li>
                @endunless
            </ul>

            <div class="action-group">
                <a href="/admin/social-case-eligibility" class="btn btn-outline-primary btn-block">New Search</a>
                <a href="/admin/social-case-eligibility/register" class="btn btn-outline-secondary btn-block">Register Another Client</a>
                <a href="{{ $eligible ? route('admin.social-case-eligibility.case-studies.create', $client) : '#' }}" class="btn btn-primary btn-block {{ $eligible ? '' : 'btn-disabled' }}">Proceed to Interview</a>
            </div>
        </div>
    </div>
</body>
</html>
