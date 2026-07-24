@extends('layouts.admin')

@section('title', 'Step 3: Eligibility Validation')

@section('navbar-title', 'Step 3: Eligibility Validation')

@section('content')
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body">
            <div class="status-card-header mb-3">
                <div>
                    <h4 class="fw-bold text-primary mb-1"><i class="fas fa-check-shield me-2"></i>Step 3: Eligibility Validation</h4>
                    <p class="text-muted small">Client: {{ $client->full_name }} | Birthdate: {{ $client->birthdate->format('M d, Y') }}</p>
                </div>
                <span class="badge-state {{ $eligible ? 'badge-eligible' : 'badge-not-eligible' }} eligible-status">
                    {{ $eligible ? 'ELIGIBLE' : 'NOT ELIGIBLE' }}
                </span>
            </div>

            @if($eligible)
                <div class="alert alert-success reason-text">
                    <i class="fas fa-check-circle me-2"></i>
                    Client is eligible to proceed.
                </div>
            @else
                <div class="alert alert-danger reason-text">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    This client has already received assistance within the last six (6) months and is currently not eligible.
                </div>
            @endif

            <ul class="summary-list">
                @unless($eligible)
                <li>
                    <span class="label">Rejection Reason</span>
                    <span class="value">Received approved or released assistance within the last six months.</span>
                </li>
                @endunless
                <li>
                    <span class="label">Client Name</span>
                    <span class="value">{{ $client->full_name }}</span>
                </li>
                <li>
                    <span class="label">Last Assistance Type</span>
                    <span class="value assistance-type">{{ $latestAssistance?->assistance_type ?? 'None' }}</span>
                </li>
                <li>
                    <span class="label">Release Date</span>
                    <span class="value assistance-date">{{ $latestAssistance?->release_date?->format('M d, Y') ?? 'None' }}</span>
                </li>
                <li>
                    <span class="label">Eligible Again Date</span>
                    <span class="value next-eligible-date">{{ $eligibleAgainDate?->format('M d, Y') ?? 'N/A' }}</span>
                </li>
                @unless($eligible)
                <li>
                    <span class="label">Remaining Waiting Period</span>
                    <span class="value">{{ $eligibleAgainDate ? now()->diffForHumans($eligibleAgainDate, ['parts' => 3, 'short' => true, 'syntax' => now()->lessThan($eligibleAgainDate) ? 1 : 0]) : 'N/A' }}</span>
                </li>
                @endunless
            </ul>

            <div class="action-group">
                <a href="{{ route('admin.social-case-eligibility.index') }}" class="btn btn-outline-primary btn-block">New Search</a>
                <a href="/admin/social-case-eligibility/register" class="btn btn-outline-secondary btn-block">Register Another Client</a>
                @if($eligible)
                    <a href="{{ route('admin.beneficiary-intake.create', $client) }}" class="btn btn-primary btn-block">Proceed to Beneficiary Intake</a>
                @else
                    <div class="d-grid gap-2 w-100">
                        <a href="{{ route('admin.social-case-eligibility.rejection-letter', $client) }}" class="btn btn-outline-danger btn-block">
                            <i class="fas fa-file-pdf me-2"></i>Print Ineligibility Notice
                        </a>
                        <form action="{{ route('admin.social-case-eligibility.reject', $client) }}" method="POST" class="d-grid w-100 m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-block">Reject &amp; Close</button>
                        </form>
                    </div>
                @endif
            </div>
                </div>
            </div>
        </div>
@endsection
