{{-- filepath: c:\Users\USER\WCPU_WEB\resources\views\desk-officer\referral-form.blade.php --}}
@extends('layouts.desk-officer')

@section('title', 'Refer Case - iSERVE SILANG Desk Officer')

@section('content')
@php
    $isMultiroleReferral = auth()->check() && auth()->user()->position === 'Desk Officer' && auth()->user()->isMultiCaseDepartment();
@endphp
@include('components.multirole-referral-fields')
<div class="px-4 py-6">
    <div class="bg-white shadow-md rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Case Referral Form</h1>
                <p class="text-sm text-gray-600 mt-1">VAW Form 2 - Referral for Service</p>
            </div>
            <a href="{{ route('desk-officer.cases.index') }}" class="text-purple-600 hover:text-purple-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Cases
            </a>
        </div>

        <form id="referral-form" action="{{ route('desk-officer.cases.refer.store', ['hash' => encrypt($case->id)]) }}" method="POST">
            @csrf

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-3"><i class="fas fa-info-circle mr-2"></i>Case Information</h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div><strong>Case Number:</strong> {{ $case->case_number }}</div>
                    <div><strong>Date Reported:</strong> {{ $case->created_at->format('F d, Y') }}</div>
                    <div><strong>Status:</strong> <span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">{{ $case->status }}</span></div>
                    <div><strong>Type:</strong> {{ $case->abuse_type }}</div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Date of Referral <span class="text-red-500">*</span></label>
                        <input type="date" name="date_of_referral" value="{{ old('date_of_referral', now()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-purple-500 focus:border-purple-500" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Recipient <span class="text-red-500">*</span></label>
                        <select name="recipient_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">Select Recipient</option>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}">{{ $recipient->name }} @if($recipient->role)({{ $recipient->role }})@endif</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Referring Party Information</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="referred_by_name" value="{{ old('referred_by_name', Auth::user()->first_name . ' ' . Auth::user()->last_name) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" required readonly>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Designation <span class="text-red-500">*</span></label>
                            <input type="text" name="referred_by_designation" value="{{ old('referred_by_designation', Auth::user()->position) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" required readonly>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="referred_by_address" value="{{ old('referred_by_address', \App\Services\Wcpu\MultiroleReferralFields::officeAddress(Auth::user())) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" required readonly>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Contact Number</label>
                            <input type="tel" name="referred_by_contact" value="{{ old('referred_by_contact', Auth::user()->contact_number ?? Auth::user()->phone_number) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" placeholder="11-digit phone number" readonly>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">
                                {{ Auth::user()->position === 'Administrator' ? 'Email' : 'Email or Username' }}
                            </label>
                            <input type="{{ Auth::user()->position === 'Administrator' ? 'email' : 'text' }}" name="referred_by_email" value="{{ old('referred_by_email', Auth::user()->email) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" readonly>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Client/Victim Information (Auto-filled)</h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-3 rounded">
                            <label class="block text-sm text-gray-600 mb-1">Name</label>
                            <p class="font-medium">{{ censorLastName($case->victim_name) }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <label class="block text-sm text-gray-600 mb-1">Age</label>
                            <p class="font-medium">{{ $case->victim_age }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <label class="block text-sm text-gray-600 mb-1">Sex</label>
                            <p class="font-medium">{{ $case->victim_gender }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded md:col-span-2">
                            <label class="block text-sm text-gray-600 mb-1">Address</label>
                            <p class="font-medium">{{ $case->victim_address }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <label class="block text-sm text-gray-600 mb-1">Contact</label>
                            <p class="font-medium">{{ $case->victim_contact ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Family/Guardian (Optional)</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Name</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name', $case->guardian) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" readonly>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Contact</label>
                            <input type="tel" name="guardian_contact" value="{{ old('guardian_contact', $case->guardian_contact) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" maxlength="11" pattern="[0-9]{11}" inputmode="numeric" placeholder="11-digit phone number" readonly>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Address</label>
                            <input type="text" name="guardian_address" value="{{ old('guardian_address', $case->guardian_address) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" readonly>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Relationship to Victim</label>
                            <input type="hidden" name="relationship_to_victim" value="{{ old('relationship_to_victim', $case->relationship_to_victim) }}">
                            <input type="text" value="{{ old('relationship_to_victim', $case->relationship_to_victim) ?: 'N/A' }}" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" readonly>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Reason for Referral <span class="text-red-500">*</span></label>
                            <textarea name="reason_for_referral" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-700" required readonly>{{ old('reason_for_referral', \App\Services\Wcpu\MultiroleReferralFields::reason($case)) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Services Requested <span class="text-red-500">*</span></label>
                            <textarea name="specific_services_requested" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Legal assistance, counseling, shelter, etc." required>{{ old('specific_services_requested') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Additional Notes</label>
                            <textarea name="additional_notes" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ old('additional_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t">
                    <a href="{{ route('desk-officer.cases.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Referral
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enforce numbers-only input on contact fields (max 11 digits)
    function enforceNumericInput(input) {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
        });
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    }

    // Apply to all tel inputs
    document.querySelectorAll('input[type="tel"]').forEach(enforceNumericInput);

    const form = document.getElementById('referral-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Confirm Referral',
            text: 'Submit this referral?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7c3299',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, submit'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-Skip-Loading': '1'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
                        sessionStorage.setItem('successful-vawc-referral-return', '1');
                        sessionStorage.setItem('referral-success-suppress', '1');
                        Swal.fire({title: 'Success!', html: `Referral created<br><strong>Ref#:</strong> ${data.referral_number}`, icon: 'success', confirmButtonColor: '#7c3299'})
                        .then(() => {
                            if (window.portalUnsavedChanges) window.portalUnsavedChanges.reset();
                            window.location.href = '{{ route("desk-officer.cases.index") }}';
                        });
                    } else {
                        throw new Error(data.message || 'Failed');
                    }
                })
                .catch(error => {
                    Swal.fire({title: 'Error!', text: error.message, icon: 'error', confirmButtonColor: '#7c3299'});
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Submit Referral';
                });
            }
        });
    });
});
</script>
@endpush
@endsection
