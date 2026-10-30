<form id="referralEditForm" action="{{ url('/desk-officer/referrals/' . $referral->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="_method" value="PUT">
    <div class="mb-3">
        <label class="form-label">Client name</label>
        <input type="text" name="client_name" class="form-control" value="{{ $referral->client_name }}" required>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="form-label">Age</label>
            <input type="text" name="client_age" class="form-control" value="{{ $referral->client_age }}">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Sex</label>
            <select name="client_sex" class="form-select">
                <option value="Male" {{ $referral->client_sex == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $referral->client_sex == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ $referral->client_sex == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Referring/Receiving agency</label>
            @if($usesRecipientDropdown ?? false)
            <select name="recipient_id" class="form-select">
                @if(!$recipients->contains('email', $referral->recipient_email))
                <option value="" selected>{{ $referral->recipient_name ?: 'Select recipient' }}</option>
                @endif
                @foreach($recipients as $recipient)
                <option value="{{ $recipient->id }}" @selected($recipient->email === $referral->recipient_email)>{{ $recipient->name }}</option>
                @endforeach
            </select>
            @else
            <input type="text" name="recipient_name" class="form-control" value="{{ $referral->recipient_name }}">
            @endif
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Service/s needed</label>
        <textarea name="specific_services_requested" class="form-control" rows="2">{{ $referral->specific_services_requested }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Referred by</label>
        <input type="text" name="referred_by_name" class="form-control" value="{{ $referral->referred_by_name }}">
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Date referred</label>
            <div class="referral-edit-date-field" style="display: contents;"><input type="date" name="date_of_referral" class="form-control" value="{{ $referral->date_of_referral ? \Carbon\Carbon::parse($referral->date_of_referral)->format('Y-m-d') : '' }}" readonly><svg class="referral-edit-date-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Date feedback received <span class="text-danger">*</span></label>
            <div class="referral-edit-date-field" style="display: contents;"><input type="date" name="received_at" class="form-control" value="{{ optional($referral->feedback_received_at)->format('Y-m-d') }}" required><svg class="referral-edit-date-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg></div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Select status</option>
                <option value="Pending" {{ $referral->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Completed" {{ $referral->status == 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Action taken</label>
        <textarea name="action_taken" class="form-control" rows="2">{{ $referral->feedback }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control" rows="2">{{ $referral->additional_notes }}</textarea>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>

<script>
(function(){
    const form = document.getElementById('referralEditForm');
    form.addEventListener('submit', function(e){
        e.preventDefault();
        const modalEl = document.getElementById('referralDetailsModal');
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) {
                // close modal and reload
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
                location.reload();
            } else {
                if (window.Swal) Swal.fire({ icon: 'error', title: 'Unable to Update Referral', text: 'Failed to save changes' });
                else alert('Failed to save changes');
            }
        }).catch(() => {
            if (window.Swal) Swal.fire({ icon: 'error', title: 'Unable to Update Referral', text: 'Failed to save changes' });
                else alert('Failed to save changes');
        });
    });
})();
</script>
