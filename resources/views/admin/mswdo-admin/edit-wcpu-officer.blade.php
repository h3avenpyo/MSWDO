@extends('admin.mswdo-admin.layout')
@section('title', 'MSWDO – Edit Officer')
@section('page_title', 'Edit Officer')

@section('content')
@php
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', $adminName);
$initials = count($words) >= 2
    ? strtoupper($words[0][0] . $words[1][0])
    : strtoupper(substr($adminName, 0, 2));

$portal = $officer->isBcpcAdmin() ? 'BCPC' : 'SWD';
@endphp

<style>
    .form-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        padding: 1.75rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
    }
    .form-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #64748B;
        margin-bottom: .4rem;
    }
    .form-control, .form-select {
        width: 100%;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        padding: .65rem .85rem;
        font-size: .875rem;
        color: #1E293B;
        outline: none;
        transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--brand, #B11116);
        box-shadow: 0 0 0 3px rgba(177, 17, 22, .1);
        background: #fff;
    }
    .form-control[readonly] {
        background: #F1F5F9;
        color: #475569;
        cursor: not-allowed;
    }
    .select-hint {
        font-size: .72rem;
        color: #94A3B8;
        margin-top: .35rem;
        display: flex;
        align-items: center;
        gap: .3rem;
    }
    .portal-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: .25rem .6rem;
        border-radius: 999px;
        background: #FEF2F2;
        color: #B11116;
        border: 1px solid #FECACA;
    }
    .btn-submit {
        background: var(--brand, #B11116);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: .6rem 1.2rem;
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
        transition: filter .2s;
    }
    .btn-submit:hover { filter: brightness(.93); }
</style>

<header class="dash-banner flex flex-col md:flex-row md:items-center justify-between gap-4 select-none">
    <div>
        <div class="dash-banner-title">Edit {{ $portal }} Officer</div>
        <div class="dash-banner-sub">
            <span>WCPU Portal Account — Update Officer Information</span>
            <span class="opacity-40">•</span>
            <span class="dash-live-badge">
                <span class="dash-pulse-dot"></span>
                <span>System Online</span>
            </span>
        </div>
    </div>
    <div class="flex items-center gap-3 self-start md:self-auto">
        <div class="text-right hidden sm:block">
            <div class="text-xs font-semibold uppercase tracking-wider text-white/70">Philippine Standard Time</div>
            <div class="text-sm font-semibold text-white tracking-wide" id="currentDateTime">Loading date...</div>
        </div>
        <div class="w-11 h-11 rounded-full bg-white/20 border-2 border-white/40 text-white font-bold text-sm flex items-center justify-center shadow-inner cursor-default" title="Logged in as: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

<div class="form-card">
    <div class="mb-4 flex items-start justify-between gap-3 flex-wrap">
        <div>
            <h2 class="text-lg font-bold text-slate-800 m-0">Edit Officer Account</h2>
            <p class="text-xs text-slate-500 mt-1">This account signs in to the {{ $portal }} portal (WCPU).</p>
        </div>
        <span class="portal-pill">
            <i data-lucide="shield" class="w-3.5 h-3.5"></i>
            {{ $portal }} Portal
        </span>
    </div>

    @if($errors->any())
        <div class="p-3 mb-4 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200" id="errorAlert">
            <ul class="list-disc pl-5 m-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.wcpu-officers.update', $officer->id) }}">
        @method('PUT')
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" placeholder="Enter first name" value="{{ old('first_name', $officer->first_name) }}" required>
            </div>
            <div>
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" placeholder="Enter last name" value="{{ old('last_name', $officer->last_name) }}" required>
            </div>
            <div>
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email address" value="{{ old('email', $officer->email) }}" required>
            </div>
            <div>
                <label class="form-label">Contact Number</label>
                <input type="text" name="phone_number" class="form-control" placeholder="e.g. 0917XXXXXXX" value="{{ old('phone_number', $officer->phone_number) }}" maxlength="11">
            </div>
            <div>
                <label class="form-label">Position</label>
                <input type="text" class="form-control" value="{{ $officer->position }}" readonly>
            </div>
            <div>
                <label class="form-label">Department</label>
                <input type="text" class="form-control" value="{{ $officer->department }}" readonly>
            </div>
            <div>
                <label class="form-label">Account Status</label>
                <select class="form-select" name="status" required>
                    <option value="active" {{ old('status', $officer->is_active ? 'active' : 'inactive') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $officer->is_active ? 'active' : 'inactive') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <p class="select-hint">
                    <i data-lucide="info"></i>
                    <span>Inactive accounts cannot sign in to the portal</span>
                </p>
            </div>
            <div class="md:col-span-2 mt-2 flex justify-end gap-2">
                <a href="{{ route('admin.officers-directory') }}" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">Cancel</a>
                <button type="submit" class="btn-submit">Update Officer</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateDateTime() {
            const now = new Date();
            const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true };
            const el = document.getElementById('currentDateTime');
            if (el) el.textContent = now.toLocaleDateString('en-US', opts).replace(',', ' at');
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);

        if (typeof lucide !== 'undefined') lucide.createIcons();

        function restrictToName(input) {
            if (!input) return;
            const clean = () => { input.value = input.value.replace(/[^\p{L}\s.]/gu, ''); };
            input.addEventListener('input', clean);
            clean();
        }
        restrictToName(document.querySelector('input[name="first_name"]'));
        restrictToName(document.querySelector('input[name="last_name"]'));

        const phoneInput = document.querySelector('input[name="phone_number"]');
        if (phoneInput) {
            const cleanPhone = () => { phoneInput.value = phoneInput.value.replace(/\D/g, '').slice(0, 11); };
            phoneInput.addEventListener('input', cleanPhone);
            cleanPhone();
        }

        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            setTimeout(function() {
                errorAlert.style.transition = 'opacity 0.5s ease';
                errorAlert.style.opacity = '0';
                setTimeout(function() { errorAlert.style.display = 'none'; }, 500);
            }, 5000);
        }
    });
</script>
@endpush
