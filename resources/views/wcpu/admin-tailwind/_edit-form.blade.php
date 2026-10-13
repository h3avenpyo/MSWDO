@php
    $isClosed = in_array(($case->status ?? ''), ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'], true) || preg_match('/closed|complet/i', trim((string) ($case->status ?? '')));
    $isReferred = in_array(($case->status ?? ''), ['Case Referred', 'Referred'], true) || preg_match('/referred/i', trim((string) ($case->status ?? '')));
    $isReadOnly = $isClosed || (auth()->user()?->position !== 'Desk Officer' && $isReferred);
    $otherAbuseDetails = '';
    $incidentDetails = $case->incident_details;
    if ($case->abuse_type === 'Others'
        && preg_match('/^Specific Type of Abuse \(Others\):\s*(.*?)\R\R(.*)$/s', (string) $case->incident_details, $matches)) {
        $otherAbuseDetails = $matches[1];
        $incidentDetails = $matches[2];
    }
@endphp

<div class="p-6">
    <!-- Note Banner -->
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-amber-800">
            <strong>Note:</strong> Fields marked with <span class="text-red-500">*</span> are required.
        </p>
    </div>

    @if($isReadOnly)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <p class="text-sm text-yellow-800">
                <strong>Read-only:</strong> {{ $isClosed ? 'This case is already closed and can no longer be edited.' : 'This case cannot be edited.' }}
            </p>
        </div>
    @endif

    <form id="edit-case-form-modal" action="{{ $editAction ?? route('cases.update', $case->id) }}" method="POST" enctype="multipart/form-data"
          @if(isset($recipients) && $recipients->count())
              data-recipients='@json($recipients->map(function($r){ return ["id"=>$r->id,"name"=>$r->name,"email"=>$r->email]; }))'
          @endif>
        @csrf
        @method('PUT')
        
        <input type="hidden" name="case_designation" value="{{ $case->case_designation }}">
        <input type="hidden" name="case_number" value="{{ $case->case_number }}">

        <fieldset @if($isReadOnly) disabled @endif class="space-y-6">
            
            <!-- Case Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b-2 border-gray-200 mb-4">
                    Case Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-xs text-blue-600 uppercase tracking-wide mb-1">Assigned Barangay</p>
                        <p class="font-semibold text-gray-800">
                            {{ is_object($case->barangay) ? $case->barangay->name : ($case->barangay ?? 'Not Assigned') }}
                            <span class="text-gray-500">[{{ is_object($case->barangay) ? strtoupper($case->barangay->code) : 'N/A' }}]</span>
                        </p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-xs text-green-600 uppercase tracking-wide mb-1">Case Number</p>
                        <p class="font-semibold text-gray-800">{{ $case->case_number }}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Case Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="Case Filed" {{ $case->status == 'Case Filed' ? 'selected' : '' }}>Case Filed</option>
                        <option value="Case closed" {{ in_array($case->status, ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'], true) ? 'selected' : '' }}>Case Closed</option>
                    </select>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Case Priority <span class="text-red-500">*</span></label>
                    <select id="priority" name="priority" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="Non-urgent" {{ $case->priority === 'Non-urgent' ? 'selected' : '' }}>Non-urgent</option>
                        <option value="Urgent" {{ $case->priority === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="Emergency" {{ $case->priority === 'Emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                </div>
            </div>

            <!-- Victim Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b-2 border-gray-200 mb-4">
                    Victim Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="victim_name" name="victim_name" value="{{ $case->victim_name }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Age <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="victim_age" name="victim_age" value="{{ $case->victim_age }}" min="1" max="120"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="victim_gender" name="victim_gender" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">Select</option>
                            <option value="Female" {{ $case->victim_gender == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Male" {{ $case->victim_gender == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="LGBTQIA+" {{ $case->victim_gender == 'LGBTQIA+' ? 'selected' : '' }}>LGBTQIA+</option>
                            <option value="Prefer not to say" {{ $case->victim_gender == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Number
                        </label>
                        <input type="text" id="victim_contact" name="victim_contact" value="{{ $case->victim_contact }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="victim_address" name="victim_address" value="{{ $case->victim_address }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                    </div>
                </div>
            </div>

            <!-- Hidden fields for referral -->
            <input type="hidden" id="client_name" name="client_name" value="{{ $case->victim_name }}">
            <input type="hidden" id="client_age" name="client_age" value="{{ $case->victim_age }}">
            <input type="hidden" id="client_sex" name="client_sex" value="{{ $case->victim_gender }}">
            <input type="hidden" id="client_contact" name="client_contact" value="{{ $case->victim_contact }}">
            <input type="hidden" id="client_address" name="client_address" value="{{ $case->victim_address }}">

            <!-- Guardian Information (for minors) -->
            <div id="guardian-section" style="display: {{ $case->victim_age < 18 ? 'block' : 'none' }};">
                <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b-2 border-gray-200 mb-4">
                    Guardian Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Name</label>
                        <input type="text" id="guardian_name" name="guardian" value="{{ $case->guardian }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Contact</label>
                        <input type="text" id="guardian_contact" name="guardian_contact" value="{{ $case->guardian_contact }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Address</label>
                        <input type="text" id="guardian_address" name="guardian_address" value="{{ $case->guardian_address }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Relationship to Victim</label>
                        <select id="guardian_relationship" name="relationship_to_victim" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
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

            <!-- Perpetrator Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b-2 border-gray-200 mb-4">
                    Perpetrator Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Perpetrator Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="abuser_name" name="perpetrator_name" value="{{ $case->abuser_name }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <label for="abuser_address" class="block text-sm font-medium text-gray-700 mt-3 mb-2">Perpetrator Address <span class="text-red-500">*</span></label>
                        <input type="text" id="abuser_address" name="abuser_address" maxlength="2000" value="{{ old('abuser_address', $case->abuser_address ?? '') }}" placeholder="Full Address" class="form-control w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Perpetrator Age</label>
                        <input type="number" id="abuser_age" name="abuser_age" value="{{ $case->abuser_age }}" min="1" max="120"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Relationship to Victim <span class="text-red-500">*</span>
                        </label>
                        <select id="relationship" name="relationship_to_perpetrator" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
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

            <!-- Guardian of Perpetrator (for minor perpetrators) -->
            <div id="guardian-abuser-section" style="display: {{ ($case->abuser_age ?? 18) < 18 ? 'block' : 'none' }};">
                <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b-2 border-gray-200 mb-4">
                    Guardian of Perpetrator Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Name</label>
                        <input type="text" id="guardian_abuser_name" name="guardian_abuser_name" value="{{ $case->guardian_abuser_name }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Contact</label>
                        <input type="text" id="guardian_abuser_contact" name="guardian_abuser_contact" value="{{ $case->guardian_abuser_contact }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Guardian Address</label>
                        <input type="text" id="guardian_abuser_address" name="guardian_abuser_address" value="{{ $case->guardian_abuser_address }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Relationship to Perpetrator</label>
                        <select id="relationship_to_abuser" name="relationship_to_abuser" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
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

            <!-- Incident Details -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 pb-2 border-b-2 border-gray-200 mb-4">
                    Incident Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Incident <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="incident_date" name="incident_date" value="{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('Y-m-d') : '' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Time of Incident <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="incident_time" name="incident_time" value="{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('H:i') : '' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date Reported <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="date_reported" name="date_reported" value="{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('Y-m-d') : '' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type of Abuse <span class="text-red-500">*</span>
                    </label>
                    <select id="abuse_type" name="abuse_type" onchange="const c = document.getElementById('other-abuse-container'); const t = document.getElementById('other_abuse_details'); if (c) c.classList.toggle('hidden', this.value !== 'Others'); if (t) { t.required = (this.value === 'Others'); if (this.value !== 'Others') t.value = ''; }" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Select type of abuse</option>
                        @foreach($caseTypes as $caseType)
                            @if(strcasecmp($caseType->republic_act, 'Others') !== 0)
                            <option value="{{ $caseType->republic_act }}" {{ ($case->abuse_type == $caseType->republic_act || $case->abuse_type == $caseType->name) ? 'selected' : '' }}>
                                @if($caseType->republic_act) {{ $caseType->republic_act }} - @endif {{ $caseType->name }}
                            </option>
                            @endif
                        @endforeach
                        <option value="Others" {{ $case->abuse_type === 'Others' ? 'selected' : '' }}>Others</option>
                    </select>
                </div>
                <div id="other-abuse-container" class="{{ $case->abuse_type === 'Others' ? '' : 'hidden' }} mt-4">
                    <label for="other_abuse_details" class="block text-sm font-medium text-gray-700 mb-2">Specify other type of abuse <span class="text-red-500">*</span></label>
                    <textarea id="other_abuse_details" name="other_abuse_details" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none" {{ $case->abuse_type === 'Others' ? 'required' : '' }}>{{ $otherAbuseDetails }}</textarea>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Incident Location <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="incident_location" name="incident_location" value="{{ $case->incident_location }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Incident Details <span class="text-red-500">*</span>
                    </label>
                    <textarea id="incident_details" name="incident_details" rows="4"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none" required>{{ $incidentDetails }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Witness Name</label>
                        <input type="text" id="witness_name" name="witness_name" value="{{ $case->witness_name }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Witness Contact</label>
                        <input type="text" id="witness_contact" name="witness_contact" value="{{ $case->witness_contact }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Submit Button -->
        <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors font-medium" @if($isReadOnly) disabled @endif>
                <i class="fas fa-save"></i>
                Update Case
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sync victim fields with hidden client fields
    const syncMap = [
        {visible: 'victim_name', hidden: 'client_name'},
        {visible: 'victim_age', hidden: 'client_age'},
        {visible: 'victim_gender', hidden: 'client_sex'},
        {visible: 'victim_contact', hidden: 'client_contact'},
        {visible: 'victim_address', hidden: 'client_address'}
    ];
    
    syncMap.forEach(pair => {
        const visible = document.getElementById(pair.visible);
        const hidden = document.getElementById(pair.hidden);
        if (visible && hidden) {
            hidden.value = visible.value || '';
            visible.addEventListener('input', () => { hidden.value = visible.value || ''; });
        }
    });

    // Toggle guardian section based on victim age
    const victimAgeInput = document.getElementById('victim_age');
    const guardianSection = document.getElementById('guardian-section');
    
    function toggleGuardianSection() {
        const age = parseInt(victimAgeInput.value);
        guardianSection.style.display = (age && age < 18) ? 'block' : 'none';
    }
    
    victimAgeInput.addEventListener('input', toggleGuardianSection);
    toggleGuardianSection();

    // Toggle abuser guardian section based on abuser age
    const abuserAgeInput = document.getElementById('abuser_age');
    const guardianAbuserSection = document.getElementById('guardian-abuser-section');
    
    function toggleGuardianAbuserSection() {
        const age = parseInt(abuserAgeInput.value);
        guardianAbuserSection.style.display = (age && age < 18) ? 'block' : 'none';
    }
    
    abuserAgeInput.addEventListener('input', toggleGuardianAbuserSection);
    toggleGuardianAbuserSection();

});
</script>
