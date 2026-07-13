@extends('layouts.admin')

@section('title', 'Eligibility Result')

@section('navbar-title', 'Eligibility Result')

@section('content')
            <div class="card">
                <div class="card-body">
            <div class="status-card-header mb-3">
                <div>
                    <p class="status-title">Eligibility validation for {{ $client->full_name }}</p>
                    <p class="text-muted small">Birthdate: {{ $client->birthdate->format('M d, Y') }}</p>
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
                <a href="/admin/social-case" class="btn btn-outline-primary btn-block">New Search</a>
                <a href="/admin/social-case-eligibility/register" class="btn btn-outline-secondary btn-block">Register Another Client</a>
                <a href="{{ $eligible ? route('admin.social-case-studies.create', $client) : '#' }}" class="btn btn-primary btn-block {{ $eligible ? '' : 'btn-disabled' }}">Proceed to Interview</a>
            </div>
                </div>
            </div>
        </div>
@endsection
