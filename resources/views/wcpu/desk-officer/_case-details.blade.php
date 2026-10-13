@php
    $caseType = $case->caseType ?? null;
    $statusClass = [
        'Pending' => 'warning',
        'In Progress' => 'info',
        'Resolved' => 'success'
    ][$case->status] ?? 'secondary';
@endphp

<div class="case-details">
    <div class="case-header mb-4">
        <h4 class="mb-0">Case #{{ $case->case_number }}</h4>
        <div class="text-muted">
            <span class="badge bg-{{ $statusClass }}">{{ $case->status }}</span>
            @if($caseType)
                <span class="ms-2">{{ $case->abuse_type }}</span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Victim Information</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Name</dt>
                        <dd>{{ censorLastName($case->victim_name) }}</dd>
                        
                        <dt>Age</dt>
                        <dd>{{ $case->victim_age }}</dd>
                        
                        <dt>Gender</dt>
                        <dd>{{ $case->victim_gender }}</dd>
                        
                        <dt>Contact</dt>
                        <dd>{{ $case->victim_contact ?? 'N/A' }}</dd>
                       
                       <dt>Email</dt>
                       <dd>{{ $case->victim_email ?? 'N/A' }}</dd>

                        <dt>Address</dt>
                        <dd>{{ $case->victim_address }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        @if($case->guardian || $case->guardian_contact || $case->guardian_address || $case->relationship_to_victim)
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Guardian of Victim Information</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        @if($case->guardian)
                        <dt>Name</dt>
                        <dd>{{ censorLastName($case->guardian) }}</dd>
                        @endif
                        
                        @if($case->guardian_contact)
                        <dt>Contact</dt>
                        <dd>{{ $case->guardian_contact }}</dd>
                        @endif
                        
                        @if($case->guardian_address)
                        <dt>Address</dt>
                        <dd>{{ $case->guardian_address }}</dd>
                        @endif
                        
                        @if($case->relationship_to_victim)
                        <dt>Relationship to Victim</dt>
                        <dd>{{ $case->relationship_to_victim }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Perpetrator Information</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Name</dt>
                        <dd>{{ censorLastName($case->abuser_name) }}</dd>
                        
                        <dt>Age</dt>
                        <dd>{{ $case->abuser_age }}</dd>
                        
                        <dt>Relationship to Victim</dt>
                        <dd>{{ $case->abuse_relationship }}</dd>
                        <dt>Address of the Perpetrator</dt>
                        <dd>{{ $case->abuser_address ?: 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        @if($case->guardian_abuser_name || $case->guardian_abuser_contact || $case->guardian_abuser_address || $case->relationship_to_abuser)
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Guardian of Perpetrator Information</h5>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        @if($case->guardian_abuser_name)
                        <dt>Name</dt>
                        <dd>{{ censorLastName($case->guardian_abuser_name) }}</dd>
                        @endif
                        
                        @if($case->guardian_abuser_contact)
                        <dt>Contact</dt>
                        <dd>{{ $case->guardian_abuser_contact }}</dd>
                        @endif
                        
                        @if($case->guardian_abuser_address)
                        <dt>Address</dt>
                        <dd>{{ $case->guardian_abuser_address }}</dd>
                        @endif
                        
                        @if($case->relationship_to_abuser)
                        <dt>Relationship to Perpetrator</dt>
                        <dd>{{ $case->relationship_to_abuser }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Incident Details</h5>
        </div>
        <div class="card-body">
            <dl class="mb-0">
                <dt>Date & Time</dt>
                <dd>{{ \Carbon\Carbon::parse($case->incident_date)->format('F j, Y') }} at {{ $case->incident_time }}</dd>
                
                <dt>Location</dt>
                <dd>{{ $case->incident_location }}</dd>
                
                <dt>Details</dt>
                <dd class="mb-0">{{ $case->incident_details }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Case Information</h5>
        </div>
        <div class="card-body">
            <dl class="mb-0">
                <dt>Date Reported</dt>
                <dd>{{ $case->created_at->format('F j, Y \a\t g:i A') }}</dd>
                
                <dt>Case Status</dt>
                <dd>{{ $case->status ?? 'N/A' }}</dd>
                <dt>Case Priority</dt>
                <dd>{{ $case->priority ?? 'N/A' }}</dd>
                <dt>Last Updated</dt>
                <dd>{{ $case->updated_at->diffForHumans() }}</dd>
                
                <dt>Reported By</dt>
                <dd>{{ $case->createdBy->name ?? 'System' }}</dd>
            </dl>
        </div>
    </div>
</div>
