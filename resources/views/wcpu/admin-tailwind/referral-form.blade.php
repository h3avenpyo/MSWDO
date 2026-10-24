@extends(auth()->user()?->position === 'Desk Officer' ? 'layouts.desk-officer' : 'layouts.admin-tailwind')

@section('title', auth()->user()?->position === 'Desk Officer' ? 'Refer Case - iSERVE SILANG Desk Officer' : 'Refer Case - iSERVE SILANG Admin')

@section('content')
@php
    $isMultiroleReferral = auth()->check() && auth()->user()->position === 'Desk Officer' && auth()->user()->isMultiCaseDepartment();
    $isBcpcReferralCase = strtoupper((string) ($case->type ?? '')) === 'BCPC';
    $isDepartmentAdminReferral = auth()->user()?->isDepartmentAdmin() ?? false;
@endphp
@if($isDepartmentAdminReferral)
@include('admin-tailwind.quarterly-reports._portrait-sidebar')
@include('admin-tailwind.components.tablet-portrait-sidebar-overlay')
@endif
@include('components.multirole-referral-fields')

<!-- Top Header -->
<header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 py-3.5 sm:py-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Case Referral Form</h2>
            <p class="text-sm text-gray-500 hidden sm:block">{{ $isBcpcReferralCase ? 'BCPC Form 2 - Referral for Service' : 'SWD Form 2 - Referral for Service' }}</p>
        </div>
        <div class="flex items-center gap-4">
            @if(auth()->user()?->position === 'Desk Officer')
                @include('desk-officer.components.notifications')
            @else
                @include('admin-tailwind.components.notifications')
            @endif
            <div class="hidden sm:flex items-center gap-3">
                <img src="{{ asset('images/mswdo_logo.jpg') }}" alt="User Profile" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->position }}</p>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="px-4 sm:px-6 lg:px-8 pt-4 pb-4 sm:pt-6 sm:pb-6 lg:pt-8 lg:pb-8 max-w-5xl mx-auto">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-4 py-2 {{ auth()->user()?->position === 'Desk Officer' ? 'bg-[#C41E3A] hover:bg-[#A01830]' : 'bg-purple-700 hover:bg-purple-800' }} text-white rounded-lg transition-colors text-sm font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Back</span>
        </a>
    </div>

    <form id="referral-form" action="{{ route('cases.refer.store', $case->id) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Case Information Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-700 to-purple-600">
                <h3 class="text-white font-medium flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Case Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-600">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Case Number</p>
                        <p class="font-semibold text-gray-800">{{ $case->case_number }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-600">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Date Reported</p>
                        <p class="font-semibold text-gray-800">{{ $case->created_at->format('F d, Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-600">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Status</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $case->status }}
                        </span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-purple-600">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Type of Abuse</p>
                        <p class="font-semibold text-gray-800">{{ $case->abuse_type }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipient Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-gray-800 font-semibold flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-purple-600"></i>
                    {{ $isDepartmentAdminReferral ? 'Recipient Details' : 'Referral Details' }}
                </h3>
            </div>
            <div class="p-6">
                @if($isDepartmentAdminReferral)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Name of Recipient <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="recipient_name" value="{{ old('recipient_name') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="recipient_contact" value="{{ old('recipient_contact') }}"
                               maxlength="11" minlength="11" pattern="[0-9]{11}" inputmode="numeric" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Office Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="recipient_address" value="{{ old('recipient_address') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="recipient_email" value="{{ old('recipient_email') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                    </div>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Referral <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_referral" value="{{ old('date_of_referral', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Recipient <span class="text-red-500">*</span>
                        </label>
                        <select name="recipient_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                            <option value="">Select Recipient</option>
                            @foreach($recipients as $recipient)
                                <option value="{{ $recipient->id }}">{{ $recipient->name }} ({{ $recipient->role }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Referring Party Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-gray-800 font-semibold flex items-center gap-2">
                    <i class="fas fa-user-tie text-purple-600"></i>
                    Referring Party Information
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="referred_by_name" value="{{ old('referred_by_name', Auth::user()->first_name . ' ' . Auth::user()->last_name) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Designation <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="referred_by_designation" value="{{ old('referred_by_designation', Auth::user()->position) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="referred_by_address" value="{{ old('referred_by_address', \App\Services\Wcpu\MultiroleReferralFields::officeAddress(Auth::user())) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required readonly>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="referred_by_contact" value="{{ old('referred_by_contact', Auth::user()->contact_number ?? Auth::user()->phone_number) }}"
                               maxlength="11" minlength="11" pattern="[0-9]{11}" inputmode="numeric" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ Auth::user()->position === 'Administrator' ? 'Email' : 'Email or Username' }}
                        </label>
                        <input type="{{ Auth::user()->position === 'Administrator' ? 'email' : 'text' }}" name="referred_by_email" value="{{ old('referred_by_email', Auth::user()->email) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client/Victim Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-gray-800 font-semibold flex items-center gap-2">
                    <i class="fas fa-user text-purple-600"></i>
                    Client/Victim Information
                    <span class="text-xs font-normal text-gray-500">(Auto-filled from case)</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Name</p>
                        <p class="font-semibold text-gray-800">{{ censorLastName($case->victim_name) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Age</p>
                        <p class="font-semibold text-gray-800">{{ $case->victim_age }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Sex</p>
                        <p class="font-semibold text-gray-800">{{ $case->victim_gender }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500 sm:col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Address</p>
                        <p class="font-semibold text-gray-800">{{ $case->victim_address }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Contact</p>
                        <p class="font-semibold text-gray-800">{{ $case->victim_contact ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guardian Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-gray-800 font-semibold flex items-center gap-2">
                    <i class="fas fa-user-shield text-purple-600"></i>
                    Guardian Information
                    <span class="text-xs font-normal text-gray-500">(Optional)</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name', $case->guardian) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact</label>
                        <input type="text" name="guardian_contact" value="{{ old('guardian_contact', $case->guardian_contact) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input type="text" name="guardian_address" value="{{ old('guardian_address', $case->guardian_address) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Relationship to Victim</label>
                        <input type="hidden" name="relationship_to_victim" value="{{ old('relationship_to_victim', $case->relationship_to_victim) }}">
                        <input type="text" value="{{ old('relationship_to_victim', $case->relationship_to_victim) ?: 'N/A' }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 transition-colors" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reason & Services -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-gray-800 font-semibold flex items-center gap-2">
                    <i class="fas fa-file-alt text-purple-600"></i>
                    Referral Details
                </h3>
            </div>
            <div class="p-6 space-y-6">
                @if($isDepartmentAdminReferral)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Referral <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_of_referral" value="{{ old('date_of_referral', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Referral <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason_for_referral" rows="4" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none" 
                              required readonly>{{ old('reason_for_referral', \App\Services\Wcpu\MultiroleReferralFields::reason($case)) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Services Requested <span class="text-red-500">*</span>
                    </label>
                    <textarea name="specific_services_requested" rows="4" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none" 
                              placeholder="Legal assistance, counseling, shelter, etc." 
                              required>{{ old('specific_services_requested') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="additional_notes" rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none">{{ old('additional_notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4">
            <a href="javascript:history.back()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition-colors text-sm font-medium shadow-sm">
                <i class="fas fa-paper-plane"></i>
                Submit Referral
            </button>
        </div>
    </form>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('referral-form');
    form.addEventListener('submit', function(e) {
        // Client-side validation for guardian contact
        const victimAge = parseInt('{{ $case->victim_age }}', 10);
        const guardianName = form.querySelector('input[name="guardian_name"]').value.trim();
        const guardianContact = form.querySelector('input[name="guardian_contact"]').value.trim();
        if ((victimAge < 18 || guardianName.length > 0) && guardianContact.length === 0) {
            e.preventDefault();
            Swal.fire({
                title: 'Missing Guardian Contact',
                text: 'Guardian contact number is required for minors or when guardian is specified.',
                icon: 'warning',
                confirmButtonColor: '#7c3aed'
            });
            return;
        }
        e.preventDefault();
        Swal.fire({
            title: 'Confirm Referral',
            text: 'Are you sure you want to submit this referral?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-check mr-2"></i>Yes, submit',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = form.querySelector('button[type="submit"]');
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
                Swal.fire({
                    title: 'Submitting Referral...',
                    text: 'Please wait while the referral is being saved.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-Skip-Loading': '1'
                    }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Unable to submit the referral. Please try again.');
                    }
                    return data;
                })
                .then(data => {
                    Swal.fire({
                        title: 'Success!',
                        html: `<div class="text-center">
                            <p class="mb-2">Referral has been submitted successfully.</p>
                            <p class="text-sm text-gray-600"><strong>Referral #:</strong> ${data.referral_number}</p>
                        </div>`,
                        icon: 'success',
                        confirmButtonColor: '#7c3aed',
                        confirmButtonText: 'OK',
                        showConfirmButton: true
                    }).then(() => {
                        sessionStorage.setItem('successful-vawc-referral-return', '1');
                        sessionStorage.setItem('referral-success-suppress', '1');
                        @if($isMultiroleReferral && strtoupper((string) ($case->type ?? '')) === 'VAWC')
                        window.location.replace('{{ route("desk-officer.cases.index") }}?type=VAWC&referral_success=1');
                        @elseif(auth()->user()?->position === 'Desk Officer')
                        window.location.replace('{{ route("desk-officer.cases.index") }}?referral_success=1');
                        @elseif(auth()->user()?->isDepartmentAdmin())
                        window.location.replace('{{ route("admin.referrals") }}');
                        @else
                        window.location.replace('{{ route("admin-barangay-cases") }}?referral_success=1');
                        @endif
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#7c3aed',
                        confirmButtonText: 'OK',
                        showConfirmButton: true
                    });
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
            }
        });
    });
});
</script>
@endpush

@endsection
