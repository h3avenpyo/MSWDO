@if($isArchived ?? false)
<div class="archived-referral-details space-y-5 text-left">
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <h3 class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">Client Information</h3>
        <dl class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3 sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Name of Client</dt><dd class="mt-1 break-words text-sm font-medium text-gray-900">{{ censorLastName($referral->client_name) ?: '—' }}</dd></div>
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Age</dt><dd class="mt-1 break-words text-sm text-gray-900">{{ $referral->client_age ?? '—' }}</dd></div>
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Sex</dt><dd class="mt-1 break-words text-sm text-gray-900">{{ $referral->client_sex ?: '—' }}</dd></div>
        </dl>
    </section>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <h3 class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">Referral Information</h3>
        <dl class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3 sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Service/s Needed</dt><dd class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-900">{{ $referral->specific_services_requested ?: '—' }}</dd></div>
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">From/To Whom</dt><dd class="mt-1 break-words text-sm text-gray-900">{{ $referral->referred_by_name ?: '—' }}</dd></div>
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Date of Referral</dt><dd class="mt-1 break-words text-sm text-gray-900">{{ $referral->date_of_referral ? \Carbon\Carbon::parse($referral->date_of_referral)->format('M d, Y') : '—' }}</dd></div>
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Date Referral Received/Sent</dt><dd class="mt-1 break-words text-sm text-gray-900">{{ $referral->feedback_received_at ? \Carbon\Carbon::parse($referral->feedback_received_at)->format('M d, Y') : '—' }}</dd></div>
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3 sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Remarks</dt><dd class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-900">{{ $referral->additional_notes ?: '—' }}</dd></div>
        </dl>
    </section>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <h3 class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">Receiving Agency and Response</h3>
        <dl class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3 sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Referring/Receiving Agency</dt><dd class="mt-1 break-words text-sm text-gray-900">{{ $referral->recipient_name ?: '—' }}</dd></div>
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3 sm:col-span-2"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Action Taken by Receiving Organization</dt><dd class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-900">{{ $referral->feedback ?: 'No feedback data' }}</dd></div>
            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3"><dt class="text-xs font-medium uppercase tracking-wide text-gray-500">By Whom</dt><dd class="mt-1 break-words text-sm text-gray-900">{{ optional($referral->referredBy)->first_name ?? '—' }}</dd></div>
        </dl>
    </section>
</div>
@else
<div>
    <style>
        .form-control[readonly] {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
            color: #6c757d;
        }
    </style>
    <form id="referralDetailsForm">
        @csrf
        <fieldset disabled>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Name of Client</label>
            <input type="text" class="form-control" name="client_name" value="{{ censorLastName($referral->client_name) }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Age</label>
            <input type="number" class="form-control" name="client_age" value="{{ $referral->client_age }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Sex</label>
            <input type="text" class="form-control" name="client_sex" value="{{ $referral->client_sex }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Service/s Needed</label>
            <textarea class="form-control" name="specific_services_requested" rows="3" readonly>{{ $referral->specific_services_requested }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Referring/Receiving Agency</label>
            <input type="text" class="form-control" name="recipient_name" value="{{ $referral->recipient_name }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">From/To Whom</label>
            <input type="text" class="form-control" name="referred_by_name" value="{{ $referral->referred_by_name }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Date of Referral</label>
            <input type="date" class="form-control" name="date_of_referral" value="{{ \Carbon\Carbon::parse($referral->date_of_referral)->format('Y-m-d') }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Date Referral Received/Sent</label>
            <input type="date" class="form-control" name="received_at" value="{{ $referral->feedback_received_at ? \Carbon\Carbon::parse($referral->feedback_received_at)->format('Y-m-d') : '' }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Action Taken by Receiving Organization</label>
            <textarea class="form-control" name="action_taken" rows="3" placeholder="No feedback data" readonly>{{ $referral->feedback }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">By Whom</label>
            <input type="text" class="form-control" name="by_whom" value="{{ optional($referral->referredBy)->first_name ?? '-' }}" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label font-medium text-gray-700">Remarks</label>
            <textarea class="form-control" name="remarks" rows="3" readonly>{{ $referral->additional_notes }}</textarea>
        </div>
        </fieldset>
        
        @unless($isArchived ?? false)
        <div class="mb-3 d-flex justify-content-end gap-2">
            <a href="{{ route('desk-officer.referrals.print', $referral->id) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print"></i> Print
            </a>
        </div>
        @endunless
    </form>
</div>
@endif
