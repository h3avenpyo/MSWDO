<div class="case-form-container">
    <div class="form-card">
        <div class="form-content">
            <div class="form-note">
                <strong>Note:</strong> Fields marked with <span class="required-field">*</span> are required to fill.
            </div>
            @php
                $isClosed = in_array(($case->status ?? ''), ['Case closed', 'Case Closed', 'Complete case', 'Complete Case'], true) || preg_match('/closed|complet/i', trim((string) ($case->status ?? '')));
                $isReferred = in_array(($case->status ?? ''), ['Case Referred', 'Referred'], true) || preg_match('/referred/i', trim((string) ($case->status ?? '')));
                $isReadOnly = $isClosed || (auth()->user()?->position !== 'Desk Officer' && $isReferred);
            @endphp
            <form id="edit-case-form-modal" action="{{ route('desk-officer.cases.update', ['hash' => encrypt($case->id)]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @if($isReadOnly)
                    <div class="alert alert-warning" role="alert">
                        <strong>Read-only:</strong> {{ $isClosed ? 'This case is already closed and can no longer be edited.' : 'This case cannot be edited.' }}
                    </div>
                @endif
                <h3 class="section-title">Case Information</h3>
                {{-- Disable inputs when case is read-only; keep Cancel button active (placed outside the fieldset) --}}
                <fieldset @if($isReadOnly) disabled @endif>
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
                         <option value="Case Filed" {{ $case->status == 'Case Filed' ? 'selected' : '' }}>Case Filed</option>
                         <option value="Case Closed" {{ $case->status == 'Case Closed' ? 'selected' : '' }}>Case Closed</option>
                     </select>
                 </div>
                <div class="form-group">
                    <label for="priority" class="form-label">Case Priority <span class="required-field">*</span></label>
                    <select id="priority" name="priority" class="form-control" required>
                        <option value="Non-urgent" {{ $case->priority === 'Non-urgent' ? 'selected' : '' }}>Non-urgent</option>
                        <option value="Urgent" {{ $case->priority === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="Emergency" {{ $case->priority === 'Emergency' ? 'selected' : '' }}>Emergency</option>
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
                            <input type="text" id="victim_contact" name="victim_contact" class="form-control" placeholder="Contact Number" value="{{ $case->victim_contact }}" required>
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

               {{-- Hidden fields to satisfy referral insertion that expects client_* names --}}
               <input type="hidden" id="client_name" name="client_name" value="{{ $case->victim_name }}">
               <input type="hidden" id="client_age" name="client_age" value="{{ $case->victim_age }}">
               <input type="hidden" id="client_sex" name="client_sex" value="{{ $case->victim_gender }}">
               <input type="hidden" id="client_contact" name="client_contact" value="{{ $case->victim_contact }}">
               <input type="hidden" id="client_address" name="client_address" value="{{ $case->victim_address }}">

               <script>
                   // keep the hidden client_* fields in sync with the visible victim_* inputs
                   (function(){
                       var map = [
                           {visible: 'victim_name', hidden: 'client_name'},
                           {visible: 'victim_age', hidden: 'client_age'},
                           {visible: 'victim_gender', hidden: 'client_sex'},
                           {visible: 'victim_contact', hidden: 'client_contact'},
                           {visible: 'victim_address', hidden: 'client_address'}
                       ];
                       function syncOne(pair){
                           var v = document.getElementById(pair.visible);
                           var h = document.getElementById(pair.hidden);
                           if(!v || !h) return;
                           h.value = v.value || '';
                           v.addEventListener('input', function(){ h.value = v.value || ''; });
                       }
                       map.forEach(syncOne);
                   })();
               </script>
                <!-- Perpetrator Information -->
                <hr class="section-divider">
                    <h3 class="section-title">Perpetrator Information</h3>
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="abuser-name" class="form-label">Perpetrator Name <span class="required-field">*</span>:</label>
                            <input type="text" id="abuser_name" name="perpetrator_name" class="form-control" placeholder="Full Name" value="{{ $case->abuser_name }}" required>
                        <label for="abuser_address" class="block text-sm font-medium text-gray-700 mt-3 mb-2">Perpetrator Address <span class="required-field">*</span></label>
                        <input type="text" id="abuser_address" name="abuser_address" maxlength="2000" value="{{ old('abuser_address', $case->abuser_address ?? '') }}" placeholder="Full Address" class="form-control w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm" required>
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
                            <label for="relationship" class="form-label"    >Relationship to Victim <span class="required-field">*</span>:</label>
                            <!-- Abuser relation: must post as abuse_relationship -->
                            <select id="abuse_relationship" name="abuse_relationship" class="form-control" required>
                                <option value="">Select Relationship</option>
                                <option value="spouse" {{ ($case->abuse_relationship ?? '') === 'spouse' ? 'selected' : '' }}>Spouse</option>
                                <option value="partner" {{ ($case->abuse_relationship ?? '') === 'partner' ? 'selected' : '' }}>Live-in Partner</option>
                                <option value="parent" {{ ($case->abuse_relationship ?? '') === 'parent' ? 'selected' : '' }}>Parent</option>
                                <option value="child" {{ ($case->abuse_relationship ?? '') === 'child' ? 'selected' : '' }}>Child</option>
                                <option value="sibling" {{ ($case->abuse_relationship ?? '') === 'sibling' ? 'selected' : '' }}>Sibling</option>
                                <option value="relative" {{ ($case->abuse_relationship ?? '') === 'relative' ? 'selected' : '' }}>Other Relative</option>
                                <option value="acquaintance" {{ ($case->abuse_relationship ?? '') === 'acquaintance' ? 'selected' : '' }}>Acquaintance</option>
                                <option value="stranger" {{ ($case->abuse_relationship ?? '') === 'stranger' ? 'selected' : '' }}>Stranger</option>
                            </select>
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
                            <div class="case-edit-date-time-field" style="display: contents;"><input type="date" id="incident_date" name="incident_date" class="form-control" value="{{ $case->incident_date ? \Carbon\Carbon::parse($case->incident_date)->format('Y-m-d') : '' }}" required><svg class="case-edit-date-time-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="time-reported" class="form-label">Time of Incident <span class="required-field">*</span></label>
                            <div class="case-edit-date-time-field" style="display: contents;"><input type="time" id="incident_time" name="incident_time" class="form-control" value="{{ $case->incident_time ? \Carbon\Carbon::parse($case->incident_time)->format('H:i') : '' }}" required><svg class="case-edit-date-time-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="dateReported" class="form-label">Date Reported <span class="required-field">*</span></label>
                            <div class="case-edit-date-time-field" style="display: contents;"><input type="date" id="date_reported" name="date_reported" class="form-control" value="{{ $case->date_reported ? \Carbon\Carbon::parse($case->date_reported)->format('Y-m-d') : '' }}" required><svg class="case-edit-date-time-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="abuse_type">Type of Abuse <span class="required-field">*</span></label>
                    <select class="form-control" id="abuse_type" name="abuse_type" onchange="const c = document.getElementById('other-abuse-container'); const t = document.getElementById('other_abuse_details'); if (c) c.classList.toggle('hidden', this.value !== 'Others'); if (t) { t.required = (this.value === 'Others'); if (this.value !== 'Others') t.value = ''; }" required>
                        <option value="">Select type of abuse</option>
                        @foreach($caseTypes as $caseType)
                            @if(strcasecmp($caseType->republic_act, 'Others') !== 0)
                            <option value="{{ $caseType->republic_act }}" {{ ($case->abuse_type == $caseType->republic_act || $case->abuse_type == $caseType->name) ? 'selected' : '' }}>
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
                </fieldset>
               <div class="form-actions {{ auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' ? 'flex justify-end pt-6 border-t border-gray-200 mt-6' : 'd-flex flex-column flex-sm-row gap-2 mt-3' }}">
                    @unless(auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC')
                    <button type="button" id="cancel-edit-button" class="btn btn-outline-secondary w-100 w-sm-auto text-center" style="min-width: 120px;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    @endunless
                    <button type="submit" class="{{ auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC' ? 'inline-flex items-center gap-2 px-6 py-3 bg-[#C41E3A] text-white rounded-lg hover:bg-[#A01830] transition-colors font-medium' : 'btn text-white w-100 w-sm-auto' }}" @if(auth()->user()?->position === 'Desk Officer' && auth()->user()?->defaultCaseDepartment() === 'BCPC') style="background-color: #C41E3A !important;" @else style="background-color: #7c3299 !important; min-width: 120px;" @endif @if($isReadOnly) disabled @endif>
                        <i class="fas fa-save"></i> Update Case
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
