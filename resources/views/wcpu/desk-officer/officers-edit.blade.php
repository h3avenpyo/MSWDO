@extends('layouts.desk-officer')

@section('title', 'Edit Case - iSERVE SILANG Desk Officer')

@section('content')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/desk-officer.css') }}">
@endpush

<div class="desk-officer-container">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center px-3">
        <h1 class="text-black fw-bold fs-2 mb-3 mb-sm-0 text-center text-sm-start px-4">Edit Case #{{ $case->case_number }}</h1>
        <a href="{{ route('desk-officer.cases.index') }}" 
           class="btn px-4 py-2 text-center" 
           style="background-color: #7c3299; color: white; font-size: 1rem;">
            <i class="fas fa-arrow-left me-1"></i> Back to Cases
        </a>
    </div>
</div>

    <div class="case-form-container mx-3 mx-md-5">
            <div class="form-card">
                <div class="form-content">
                    <div class="form-note">
                        <strong>Note:</strong> Fields marked with <span class="required-field">*</span> are required to fill.
                    </div>
                <form id="edit-case-form" action="{{ route('desk-officer.cases.update', ['hash' => encrypt($case->id)]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <h3 class="section-title">Case Information</h3>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="case-designation" class="form-label">Assigned Barangay</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <strong>{{ $case->barangay->name ?? 'Not Assigned' }}</strong> - [{{ strtoupper($case->barangay->code ?? 'N/A') }}]
                                </div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="case-number" class="form-label">Case Number:</label>
                                <div class="alert alert-success">
                                    <i class="fas fa-hashtag me-2"></i>
                                    <span>{{ $case->case_number }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Case Status <span class="required-field">*</span></label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="Pending" {{ $case->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ $case->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Resolved" {{ $case->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    
                    <!-- Victim Information -->
                    
                    <hr class="section-divider">
                    <h3 class="section-title">Victim Information</h3>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="victim-name" class="form-label">Full Name <span class="required-field">*</span></label>
                                <input type="text" id="victim_name" name="victim_name" class="form-control" placeholder="Full Name" value="{{ $case->victim_name }}" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="victim-age" class="form-label">Victim Age <span class="required-field">*</span>:</label>
                                <input type="number" id="victim_age" name="victim_age" class="form-control" placeholder="Age" min="1" max="120" value="{{ $case->victim_age }}" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="victim-type" class="form-label">Gender<span class="required-field">*</span>:</label>
                                <select id="victim_gender" name="victim_gender" class="form-control" required>
                                    <option value="">Select</option>
                                    <option value="Female" {{ $case->victim_gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Male" {{ $case->victim_gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="LGBTQIA+" {{ $case->victim_gender == 'LGBTQIA+' ? 'selected' : '' }}>LGBTQIA+</option>
                                    <option value="Prefer not to say" {{ $case->victim_gender == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="victim-contact" class="form-label">Contact Number <span class="required-field">*</span>:</label>
                                <input type="text" id="victim_contact" name="victim_contact" class="form-control" placeholder="Contact Number" value="{{ $case->victim_contact }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="victim-address" class="form-label">Address <span class="required-field">*</span>:</label>
                                <input type="text" id="victim_address" name="victim_address" class="form-control" placeholder="Current Address" value="{{ $case->victim_address }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Guardian Information (shown only if victim is under 18) -->
                    <div id="guardian-section" style="display: {{ $case->victim_age < 18 ? 'block' : 'none' }};">
                        <hr class="section-divider">
                        <h3 class="section-title">Guardian of Victim Information</h3>
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="guardian_name" class="form-label">Guardian Name <span class="required-field">*</span>:</label>
                                    <input type="text" id="guardian_name" name="guardian" class="form-control" placeholder="Full Name of Guardian" value="{{ $case->guardian }}">
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="guardian_contact" class="form-label">Guardian Contact <span class="required-field">*</span>:</label>
                                    <input type="text" id="guardian_contact" name="guardian_contact" class="form-control" placeholder="Contact Number" value="{{ $case->guardian_contact }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="guardian_address" class="form-label">Guardian Address <span class="required-field">*</span>:</label>
                                    <input type="text" id="guardian_address" name="guardian_address" class="form-control" placeholder="Full Address" value="{{ $case->guardian_address }}">
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="relationship_to_victim" class="form-label">Relationship to Victim <span class="required-field">*</span>:</label>
                                    <select id="relationship_to_victim" name="relationship_to_victim" class="form-control">
                                        <option value="">Select Relationship</option>
                                        <option value="Parent" {{ $case->relationship_to_victim == 'Parent' ? 'selected' : '' }}>Parent</option>
                                        <option value="Legal Guardian" {{ $case->relationship_to_victim == 'Legal Guardian' ? 'selected' : '' }}>Legal Guardian</option>
                                        <option value="Grandparent" {{ $case->relationship_to_victim == 'Grandparent' ? 'selected' : '' }}>Grandparent</option>
                                        <option value="Aunt/Uncle" {{ $case->relationship_to_victim == 'Aunt/Uncle' ? 'selected' : '' }}>Aunt/Uncle</option>
                                        <option value="Sibling" {{ $case->relationship_to_victim == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                        <option value="Other" {{ $case->relationship_to_victim == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Perpetrator Information -->
                    <hr class="section-divider">
                    <h3 class="section-title">Perpetrator Information</h3>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="abuser-name" class="form-label">Perpetrator Name <span class="required-field">*</span>:</label>
                                <input type="text" id="abuser_name" name="perpetrator_name" class="form-control" placeholder="Full Name" value="{{ $case->abuser_name }}" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="abuser-age" class="form-label">Perpetrator Age:</label>
                                <input type="number" id="abuser_age" name="abuser_age" class="form-control" placeholder="Age" min="1" max="120" value="{{ $case->abuser_age }}">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="relationship" class="form-label">Relationship to Victim <span class="required-field">*</span>:</label>
                                <!-- this field is for the ABUSER relation; name must be abuse_relationship -->
                                <select id="abuse_relationship" name="abuse_relationship" class="form-control" required>
                                     <option value="">Select Relationship</option>
                                     <option value="spouse" {{ $case->abuse_relationship == 'spouse' ? 'selected' : '' }}>Spouse</option>
                                     <option value="partner" {{ $case->abuse_relationship == 'partner' ? 'selected' : '' }}>Live-in Partner</option>
                                     <option value="parent" {{ $case->abuse_relationship == 'parent' ? 'selected' : '' }}>Parent</option>
                                     <option value="child" {{ $case->abuse_relationship == 'child' ? 'selected' : '' }}>Child</option>
                                     <option value="sibling" {{ $case->abuse_relationship == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                     <option value="relative" {{ $case->abuse_relationship == 'relative' ? 'selected' : '' }}>Other Relative</option>
                                     <option value="acquaintance" {{ $case->abuse_relationship == 'acquaintance' ? 'selected' : '' }}>Acquaintance</option>
                                     <option value="stranger" {{ $case->abuse_relationship == 'stranger' ? 'selected' : '' }}>Stranger</option>
                                 </select>
                             </div>
                         </div>
                    </div>

                    <!-- Guardian of Perpetrator Information (shown only if perpetrator is under 18) -->
                    <div id="guardian-abuser-section" style="display: {{ $case->abuser_age < 18 ? 'block' : 'none' }};">
                        <hr class="section-divider">
                        <h3 class="section-title">Guardian of Perpetrator Information</h3>
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="guardian_abuser_name" class="form-label">Guardian Name <span class="required-field">*</span>:</label>
                                    <input type="text" id="guardian_abuser_name" name="guardian_abuser_name" class="form-control" placeholder="Full Name of Guardian" value="{{ $case->guardian_abuser_name }}">
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="guardian_abuser_contact" class="form-label">Guardian Contact <span class="required-field">*</span>:</label>
                                    <input type="text" id="guardian_abuser_contact" name="guardian_abuser_contact" class="form-control" placeholder="Contact Number" value="{{ $case->guardian_abuser_contact }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="guardian_abuser_address" class="form-label">Guardian Address <span class="required-field">*</span>:</label>
                                    <input type="text" id="guardian_abuser_address" name="guardian_abuser_address" class="form-control" placeholder="Full Address" value="{{ $case->guardian_abuser_address }}">
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="relationship_to_abuser" class="form-label">Relationship to Perpetrator <span class="required-field">*</span>:</label>
                                    <select id="relationship_to_abuser" name="relationship_to_abuser" class="form-control">
                                        <option value="">Select Relationship</option>
                                        <option value="Parent" {{ $case->relationship_to_abuser == 'Parent' ? 'selected' : '' }}>Parent</option>
                                        <option value="Legal Guardian" {{ $case->relationship_to_abuser == 'Legal Guardian' ? 'selected' : '' }}>Legal Guardian</option>
                                        <option value="Grandparent" {{ $case->relationship_to_abuser == 'Grandparent' ? 'selected' : '' }}>Grandparent</option>
                                        <option value="Aunt/Uncle" {{ $case->relationship_to_abuser == 'Aunt/Uncle' ? 'selected' : '' }}>Aunt/Uncle</option>
                                        <option value="Sibling" {{ $case->relationship_to_abuser == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                        <option value="Other" {{ $case->relationship_to_abuser == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Incident Details -->
                    <hr class="section-divider">
                    <h3 class="section-title">Incident Details</h3>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="date-reported" class="form-label">Date of Incident <span class="required-field">*</span></label>
                                <input type="date" id="incident_date" name="incident_date" class="form-control" value="{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="time-reported" class="form-label">Time of Incident <span class="required-field">*</span></label>
                                <input type="time" id="incident_time" name="incident_time" class="form-control" value="{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('H:i') : '' }}" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="dateReported" class="form-label">Date Reported <span class="required-field">*</span></label>
                                <input type="date" id="date_reported" name="date_reported" class="form-control" value="{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="abuse_type">Type of Abuse <span class="required-field">*</span></label>
                        <select class="form-control" id="abuse_type" name="abuse_type" required>
                            <option value="">Select type of abuse</option>
                        @foreach($caseTypes as $caseType)
                            @if(strcasecmp($caseType->republic_act, 'Others') !== 0)
                            <option value="{{ $caseType->republic_act }}" {{ $case->abuse_type == $caseType->republic_act ? 'selected' : '' }}>
                                    @if($caseType->republic_act)
                                        {{ $caseType->republic_act }} - 
                                    @endif
                                    {{ $caseType->name }}
                            </option>
                            @endif
                        @endforeach
                        <option value="Others" {{ $case->abuse_type === 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
                <div id="other-abuse-container" class="form-group {{ $case->abuse_type === 'Others' ? '' : 'hidden' }}">
                    <label class="form-label" for="other_abuse_details">Specify other type of abuse <span class="required-field">*</span></label>
                    <textarea class="form-control" id="other_abuse_details" name="other_abuse_details" rows="3" {{ $case->abuse_type === 'Others' ? 'required' : '' }}>{{ $otherAbuseDetails ?? '' }}</textarea>
                </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="incident_location">Incident Location</label>
                        <input type="text" class="form-control" id="incident_location" name="incident_location" placeholder="Where did the incident happen?" value="{{ $case->incident_location }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="incident_details">Incident Details</label>
                        <textarea class="form-control" id="incident_details" name="incident_details" rows="4" placeholder="Provide details about the case" required>{{ $incidentDetailsForEdit ?? $case->incident_details }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="witness_name">Witness Name (if any)</label>
                        <input type="text" class="form-control" id="witness_name" name="witness_name" placeholder="Full name of witness" value="{{ $case->witness_name }}">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="witness_contact">Witness Contact</label>
                        <input type="text" class="form-control" id="witness_contact" name="witness_contact" placeholder="Contact number of witness" value="{{ $case->witness_contact }}">
                    </div>
                                    
                    <hr class="section-divider">

                   <div class="form-actions d-flex flex-column flex-sm-row gap-2 mt-3">
                        <button type="button" onclick="cancelEditCase()" class="btn btn-outline-secondary w-100 w-sm-auto text-center" style="min-width: 120px;">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn text-white w-100 w-sm-auto" style="background-color: #7c3299; min-width: 120px;">
                            <i class="fas fa-save"></i> Update Case
                        </button>
                    </div>

                </form>
            </div>
        </div>
</div>

@push('scripts')
<script>
function cancelEditCase() {
    window.history.back();
}

document.addEventListener('DOMContentLoaded', function() {
    // Guardian section toggle for victim
    const victimAgeInput = document.getElementById('victim_age');
    const guardianSection = document.getElementById('guardian-section');
    const guardianInputs = guardianSection ? guardianSection.querySelectorAll('input, select') : [];

    function toggleGuardianSection() {
        if (!guardianSection) return;
        const age = parseInt(victimAgeInput.value);
        if (age && age < 18) {
            guardianSection.style.display = 'block';
            guardianInputs.forEach(input => input.required = true);
        } else {
            guardianSection.style.display = 'none';
            guardianInputs.forEach(input => input.required = false);
        }
    }

    if (victimAgeInput) {
        victimAgeInput.addEventListener('input', toggleGuardianSection);
        victimAgeInput.addEventListener('change', toggleGuardianSection);
    }

    // Guardian section toggle for abuser
    const abuserAgeInput = document.getElementById('abuser_age');
    const guardianAbuserSection = document.getElementById('guardian-abuser-section');
    const guardianAbuserInputs = guardianAbuserSection ? guardianAbuserSection.querySelectorAll('input, select') : [];

    function toggleGuardianAbuserSection() {
        if (!guardianAbuserSection) return;
        const age = parseInt(abuserAgeInput.value);
        if (age && age < 18) {
            guardianAbuserSection.style.display = 'block';
            guardianAbuserInputs.forEach(input => input.required = true);
        } else {
            guardianAbuserSection.style.display = 'none';
            guardianAbuserInputs.forEach(input => input.required = false);
        }
    }

    if (abuserAgeInput) {
        abuserAgeInput.addEventListener('input', toggleGuardianAbuserSection);
        abuserAgeInput.addEventListener('change', toggleGuardianAbuserSection);
    }
});
</script>
@endpush
