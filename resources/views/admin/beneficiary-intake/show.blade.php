@extends('layouts.admin')

@section('title', 'Beneficiary Intake Details')

@section('navbar-title', 'Beneficiary Intake Details')

@section('page-styles')
    <style>
        .info-label {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 500;
            color: var(--text);
            font-size: 0.95rem;
        }

        .section-title {
            font-weight: 600;
            color: var(--text);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }
    </style>
@endsection

@section('content')
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Intake #{{ $intake->control_number }}</h5>
                        <a href="{{ route('admin.beneficiary-intake.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>

                    <!-- Processing Information -->
                    <div class="mb-4">
                        <h6 class="section-title">Processing Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-label">Control Number</div>
                                <div class="info-value">{{ $intake->control_number }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Date Processed</div>
                                <div class="info-value">{{ $intake->date_processed->format('F d, Y') }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Encoder</div>
                                <div class="info-value">{{ $intake->encoder }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div class="mb-4">
                        <h6 class="section-title">Client Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">{{ $intake->client_full_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Birthday</div>
                                <div class="info-value">{{ $intake->client_birthday->format('F d, Y') }} ({{ $intake->client_age }} years old)</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Sex</div>
                                <div class="info-value">{{ $intake->client_sex }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Civil Status</div>
                                <div class="info-value">{{ $intake->client_civil_status }}</div>
                            </div>
                            <div class="col-md-8">
                                <div class="info-label">Address</div>
                                <div class="info-value">{{ $intake->client_address }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Barangay</div>
                                <div class="info-value">{{ $intake->client_barangay }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Contact Number</div>
                                <div class="info-value">{{ $intake->client_contact_number }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Occupation</div>
                                <div class="info-value">{{ $intake->client_occupation ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Monthly Income</div>
                                <div class="info-value">{{ $intake->client_monthly_income ? '₱' . number_format($intake->client_monthly_income, 2) : 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Beneficiary Information -->
                    <div class="mb-4">
                        <h6 class="section-title">Beneficiary Information</h6>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Client is {{ $intake->is_client_beneficiary ? 'also the beneficiary' : 'not the beneficiary' }}
                        </div>
                        @if(!$intake->is_client_beneficiary)
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-label">Beneficiary Name</div>
                                <div class="info-value">{{ $intake->beneficiary_full_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Birthday</div>
                                <div class="info-value">{{ $intake->beneficiary_birthday ? $intake->beneficiary_birthday->format('F d, Y') . ' (' . $intake->beneficiary_age . ' years old)' : 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Sex</div>
                                <div class="info-value">{{ $intake->beneficiary_sex ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Barangay</div>
                                <div class="info-value">{{ $intake->beneficiary_barangay ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Relationship to Client</div>
                                <div class="info-value">{{ $intake->beneficiary_relationship ?? 'N/A' }}</div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Medical Conditions -->
                    <div class="mb-4">
                        <h6 class="section-title">Medical Conditions</h6>
                        @if($intake->medical_conditions && is_array($intake->medical_conditions) && count($intake->medical_conditions) > 0)
                        <div class="row g-2">
                            @foreach($intake->medical_conditions as $condition)
                            <div class="col-md-3">
                                <span class="badge bg-primary">{{ $condition }}</span>
                            </div>
                            @endforeach
                        </div>
                        @if($intake->medical_condition_other)
                        <div class="mt-2">
                            <div class="info-label">Other Condition</div>
                            <div class="info-value">{{ $intake->medical_condition_other }}</div>
                        </div>
                        @endif
                        @else
                        <div class="text-muted">No medical conditions specified</div>
                        @endif
                    </div>

                    <!-- Service and Purpose -->
                    <div class="mb-4">
                        <h6 class="section-title">Service and Purpose</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-label">Service Provided</div>
                                <div class="info-value">{{ $intake->service_provided }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Purpose</div>
                                <div class="info-value">{{ $intake->purpose }}</div>
                            </div>
                            @if($intake->purpose_other)
                            <div class="col-md-12">
                                <div class="info-label">Other Purpose</div>
                                <div class="info-value">{{ $intake->purpose_other }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submitted To -->
                    <div class="mb-4">
                        <h6 class="section-title">Submitted To</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-label">Office/Agency</div>
                                <div class="info-value">{{ $intake->submitted_to }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-label">Created At</div>
                                <div class="info-value small">{{ $intake->created_at->format('F d, Y g:i A') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Last Updated</div>
                                <div class="info-value small">{{ $intake->updated_at->format('F d, Y g:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

