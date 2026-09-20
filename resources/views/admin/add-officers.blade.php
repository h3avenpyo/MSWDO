@extends('admin.layout')
@section('title', 'MSWDO – Add New Officer')
@section('page_title', 'Add New Officer')

@section('content')
@php
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', trim($adminName));
$initials = count($words) >= 2
    ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
    : strtoupper(substr($adminName,0,2));
@endphp

<style>
    /* ── Dashboard Header Banner ── */
    .dash-banner {
        background: linear-gradient(135deg, #1A237E 0%, #1E3A8A 55%, #1e40af 100%);
        border-radius: 18px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 10px 25px -5px rgba(26, 35, 126, 0.25);
        position: relative;
        overflow: hidden;
        color: #FFFFFF;
    }
    .dash-banner::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(251, 192, 45, 0.2) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }
    .dash-banner-title {
        font-family: 'Public Sans', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
        letter-spacing: -0.02em;
    }
    .dash-banner-sub {
        font-size: 0.925rem;
        color: rgba(255, 255, 255, 0.85);
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .dash-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.825rem;
        font-weight: 500;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .dash-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ADE80;
        box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.35);
        animation: pulse-dot 2s infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(0.85); }
    }

    /* ── Form Container Card ── */
    .form-shell {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0F172A;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #F1F5F9;
    }
    .form-section-title svg {
        color: #1A237E;
        width: 20px;
        height: 20px;
    }

    .field-group {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .field-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .field-label .required {
        color: #DC2626;
        margin-left: 2px;
    }

    .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-icon {
        position: absolute;
        left: 1rem;
        width: 18px;
        height: 18px;
        color: #94A3B8;
        pointer-events: none;
    }
    .form-input {
        width: 100%;
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        padding: 0.75rem 1rem 0.75rem 2.65rem;
        font-size: 0.9rem;
        color: #1E293B;
        outline: none;
        transition: all 0.2s ease;
    }
    .form-input:focus {
        background: #FFFFFF;
        border-color: #1A237E;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.12);
    }
    .form-select-custom {
        width: 100%;
        background: #F8FAFC url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") no-repeat right 0.75rem center/1.25rem 1.25rem;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        padding: 0.75rem 2.5rem 0.75rem 2.65rem;
        font-size: 0.9rem;
        color: #1E293B;
        outline: none;
        appearance: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .form-select-custom:focus {
        background-color: #FFFFFF;
        border-color: #1A237E;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.12);
    }

    .pw-toggle-btn {
        position: absolute;
        right: 0.75rem;
        background: transparent;
        border: none;
        color: #94A3B8;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease;
    }
    .pw-toggle-btn:hover { color: #1A237E; }

    /* ── Password Strength Checklist ── */
    .pw-criteria {
        background: #F8FAFC;
        border: 1px solid #EDF2F7;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        margin-top: 0.5rem;
    }
    .pw-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.4rem 1rem;
    }
    .pw-item {
        font-size: 0.75rem;
        color: #64748B;
        display: flex;
        align-items: center;
        gap: 0.45rem;
        transition: color 0.2s ease;
    }
    .pw-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #CBD5E1;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    .pw-item.met {
        color: #059669;
        font-weight: 600;
    }
    .pw-item.met .pw-dot {
        background: #059669;
        box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.2);
    }

    /* Match feedback message */
    .pw-match-indicator {
        font-size: 0.75rem;
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 600;
    }
    .match-ok { color: #059669; }
    .match-no { color: #DC2626; }

    /* ── Radio Pill Selector for Account Status ── */
    .status-pill-group {
        display: flex;
        gap: 0.75rem;
    }
    .status-pill-label {
        flex: 1;
        cursor: pointer;
        position: relative;
    }
    .status-pill-label input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    .status-pill-box {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        transition: all 0.2s ease;
    }
    .status-pill-label input:checked + .status-pill-box.opt-active {
        background: #ECFDF5;
        border-color: #059669;
        color: #059669;
        box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.15);
    }
    .status-pill-label input:checked + .status-pill-box.opt-inactive {
        background: #FEF2F2;
        border-color: #DC2626;
        color: #DC2626;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.15);
    }



    /* ── Action Rails ── */
    .btn-action-primary {
        background: #1A237E;
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        padding: 0.85rem 1.75rem;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(26, 35, 126, 0.2);
    }
    .btn-action-primary:hover {
        background: #121858;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(26, 35, 126, 0.3);
    }
    .btn-action-secondary {
        background: #FFFFFF;
        color: #475569;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        padding: 0.85rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-action-secondary:hover {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #94A3B8;
    }

    @media (max-width: 767.98px) {
        .dash-banner {
            padding: 1.25rem 1.4rem;
            border-radius: 14px;
            margin-bottom: 1.25rem;
        }
        .dash-banner-title { font-size: 1.35rem; }
        .form-shell { padding: 1.25rem; }
    }
</style>

{{-- Page Header Banner --}}
<header class="dash-banner flex flex-col md:flex-row md:items-center justify-between gap-4 select-none">
    <div>
        <div class="dash-banner-title">Add New Officer</div>
        <div class="dash-banner-sub">
            <span>MSWDO Silang — Officer Enrollment</span>
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
            <div class="text-sm font-semibold text-white tracking-wide" id="liveClock">Loading date...</div>
        </div>
        <div class="w-11 h-11 rounded-full bg-white/20 border-2 border-white/40 text-white font-bold text-sm flex items-center justify-center shadow-inner cursor-default" title="Logged in as: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

{{-- Alerts --}}
@if(session('success'))
    <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm" id="successAlert">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm" id="errorAlert">
        <div class="flex items-center gap-2 font-bold mb-1 text-rose-900">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
            <span>Please correct the following errors:</span>
        </div>
        <ul class="list-disc pl-5 m-0 space-y-1 text-xs">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Main Form Card --}}
<div class="form-shell">
    <form method="POST" action="{{ route('admin.officers.store') }}" enctype="multipart/form-data" id="officerForm">
        @csrf

        {{-- Section 1: Personal & Contact Information --}}
        <div class="form-section-title">
            <i data-lucide="user"></i>
            <span>Personal &amp; Contact Details</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">
            <div class="field-group">
                <label class="field-label">Full Name <span class="required">*</span></label>
                <div class="input-wrap">
                    <i data-lucide="user-check" class="input-icon"></i>
                    <input type="text" name="name" class="form-input" placeholder="e.g. Maria Santos Dela Cruz" value="{{ old('name') }}" required autocomplete="off">
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Email Address (Login Username) <span class="required">*</span></label>
                <div class="input-wrap">
                    <i data-lucide="mail" class="input-icon"></i>
                    <input type="email" name="email" class="form-input" placeholder="e.g. maria.delacruz@mswdo.gov.ph" value="{{ old('email') }}" required autocomplete="off">
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Contact Number</label>
                <div class="input-wrap">
                    <i data-lucide="phone" class="input-icon"></i>
                    <input type="text" name="phone" class="form-input" placeholder="e.g. 09171234567" value="{{ old('phone') }}" maxlength="20">
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Initial Account Status</label>
                <div class="status-pill-group">
                    <label class="status-pill-label">
                        <input type="radio" name="status" value="active" {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                        <div class="status-pill-box opt-active">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span>Active Account</span>
                        </div>
                    </label>
                    <label class="status-pill-label">
                        <input type="radio" name="status" value="inactive" {{ old('status') === 'inactive' ? 'checked' : '' }}>
                        <div class="status-pill-box opt-inactive">
                            <i data-lucide="pause-circle" class="w-4 h-4"></i>
                            <span>Inactive / On Hold</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Section 2: Role & System Privileges --}}
        <div class="form-section-title">
            <i data-lucide="shield-check"></i>
            <span>Departmental Role &amp; Access Assignment</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">
            <div class="field-group md:col-span-2">
                <label class="field-label">System Role Assignment <span class="required">*</span></label>
                <div class="input-wrap">
                    <i data-lucide="briefcase" class="input-icon"></i>
                    <select class="form-select-custom" name="role" id="roleSelect" required>
                        <option value="" disabled selected>Select Role / Module Responsibility</option>
                        <option value="Senior Citizen officer" {{ old('role') == 'Senior Citizen officer' ? 'selected' : '' }}>Senior Citizen Officer (OSCA Portal &amp; Payouts)</option>
                        <option value="financialstep1" {{ old('role') == 'financialstep1' ? 'selected' : '' }}>Financial Assistance Step 1 (Intake &amp; Application)</option>
                        <option value="financialstep2" {{ old('role') == 'financialstep2' ? 'selected' : '' }}>Financial Assistance Step 2 (Approval &amp; Voucher)</option>
                        <option value="eligibility_checker" {{ old('role') == 'eligibility_checker' ? 'selected' : '' }}>Social Case Worker — Eligibility Checker</option>
                        <option value="social_worker" {{ old('role') == 'social_worker' ? 'selected' : '' }}>Social Case Worker — Case Encoder</option>
                        <option value="encoder" {{ old('role') == 'encoder' ? 'selected' : '' }}>General System Encoder</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Departmental Staff</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>System Administrator (Full Management)</option>
                    </select>
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-indigo-600"></i>
                    <span>This role determines the sidebar modules and data permissions available to the officer.</span>
                </div>
            </div>
        </div>

        {{-- Section 3: Credentials & Security --}}
        <div class="form-section-title">
            <i data-lucide="lock"></i>
            <span>Security Credentials</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">
            <div class="field-group">
                <label class="field-label">Password <span class="required">*</span></label>
                <div class="input-wrap">
                    <i data-lucide="key-round" class="input-icon"></i>
                    <input type="password" id="passwordInput" name="password" class="form-input pr-10" placeholder="Minimum 8 characters" required autocomplete="new-password">
                    <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('passwordInput', this)" title="Toggle password visibility">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
                
                {{-- Strength meter & criteria --}}
                <div class="pw-criteria">
                    <div class="text-[11px] font-semibold text-slate-600 uppercase tracking-wide mb-1.5">Password Requirements:</div>
                    <ul class="pw-list">
                        <li class="pw-item" id="reqLen"><span class="pw-dot"></span> 8+ characters</li>
                        <li class="pw-item" id="reqUp"><span class="pw-dot"></span> One uppercase</li>
                        <li class="pw-item" id="reqLow"><span class="pw-dot"></span> One lowercase</li>
                        <li class="pw-item" id="reqNum"><span class="pw-dot"></span> One number</li>
                        <li class="pw-item" id="reqSpec"><span class="pw-dot"></span> One special character</li>
                    </ul>
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Confirm Password <span class="required">*</span></label>
                <div class="input-wrap">
                    <i data-lucide="shield-check" class="input-icon"></i>
                    <input type="password" id="confirmPasswordInput" name="password_confirmation" class="form-input pr-10" placeholder="Re-type password" required autocomplete="new-password">
                    <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('confirmPasswordInput', this)" title="Toggle password visibility">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
                <div id="matchMessage" class="pw-match-indicator" style="display:none;"></div>
            </div>
        </div>

        {{-- Section 4: Signature & Authorization (Optional) --}}
        <div class="form-section-title">
            <i data-lucide="award"></i>
            <span>Signature &amp; ID Card Authorization (Optional)</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            <div class="field-group md:col-span-2">
                <label class="field-label">Official Signature Position</label>
                <div class="input-wrap">
                    <i data-lucide="award" class="input-icon"></i>
                    <select class="form-select-custom" name="signature_position">
                        <option value="">None (Standard Staff)</option>
                        <option value="osca_head" {{ old('signature_position') == 'osca_head' ? 'selected' : '' }}>OSCA Head / Signatory</option>
                        <option value="mswdo_officer" {{ old('signature_position') == 'mswdo_officer' ? 'selected' : '' }}>MSWDO Department Officer</option>
                        <option value="mswdo_staff" {{ old('signature_position') == 'mswdo_staff' ? 'selected' : '' }}>MSWDO Authorized Staff</option>
                    </select>
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Designate whether this officer's digital signature will be embedded in senior IDs and certifications.
                </div>
            </div>
        </div>

        {{-- Form Actions Rail --}}
        <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3 flex-wrap">
            <a href="{{ route('admin.officers-directory') }}" class="btn-action-secondary">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="btn-action-primary">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Add Officer</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Live Clock with Philippine formatting
    function updateLiveClock() {
        const now = new Date();
        const opts = { 
            weekday: 'short', 
            month: 'short', 
            day: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit', 
            second: '2-digit', 
            hour12: true 
        };
        const el = document.getElementById('liveClock');
        if (el) el.textContent = now.toLocaleDateString('en-US', opts);
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);

    // Show popup if redirected with officerCreated
    @if(session('officer_created') || ($officerCreated ?? false))
        Swal.fire({
            title: 'Officer Created Successfully!',
            text: 'The new officer account is active and can now log in.',
            icon: 'success',
            confirmButtonColor: '#1A237E',
            confirmButtonText: 'View Officers Directory',
            showCancelButton: true,
            cancelButtonText: 'Add Another',
            cancelButtonColor: '#64748B'
        }).then((res) => {
            if (res.isConfirmed) {
                window.location.href = "{{ route('admin.officers-directory') }}";
            }
        });
    @endif

    // Password live criteria checking
    const pwInput = document.getElementById('passwordInput');
    const confirmInput = document.getElementById('confirmPasswordInput');
    const matchMsg = document.getElementById('matchMessage');

    const reqLen = document.getElementById('reqLen');
    const reqUp  = document.getElementById('reqUp');
    const reqLow = document.getElementById('reqLow');
    const reqNum = document.getElementById('reqNum');
    const reqSpec = document.getElementById('reqSpec');

    function checkPassword() {
        const val = pwInput.value;
        const confirmVal = confirmInput.value;

        // Length
        if (val.length >= 8) reqLen.classList.add('met');
        else reqLen.classList.remove('met');

        // Upper
        if (/[A-Z]/.test(val)) reqUp.classList.add('met');
        else reqUp.classList.remove('met');

        // Lower
        if (/[a-z]/.test(val)) reqLow.classList.add('met');
        else reqLow.classList.remove('met');

        // Number
        if (/[0-9]/.test(val)) reqNum.classList.add('met');
        else reqNum.classList.remove('met');

        // Special
        if (/[^A-Za-z0-9]/.test(val)) reqSpec.classList.add('met');
        else reqSpec.classList.remove('met');

        // Confirmation Match
        if (confirmVal.length > 0) {
            matchMsg.style.display = 'flex';
            if (val === confirmVal) {
                matchMsg.className = 'pw-match-indicator match-ok';
                matchMsg.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i> Passwords match';
            } else {
                matchMsg.className = 'pw-match-indicator match-no';
                matchMsg.innerHTML = '<i data-lucide="x" class="w-3.5 h-3.5"></i> Passwords do not match';
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        } else {
            matchMsg.style.display = 'none';
        }
    }

    if (pwInput) pwInput.addEventListener('input', checkPassword);
    if (confirmInput) confirmInput.addEventListener('input', checkPassword);
});

// Toggle password visibility
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPw = input.type === 'password';
    input.type = isPw ? 'text' : 'password';
    btn.innerHTML = isPw 
        ? '<i data-lucide="eye-off" class="w-4 h-4"></i>' 
        : '<i data-lucide="eye" class="w-4 h-4"></i>';
    if (typeof lucide !== 'undefined') lucide.createIcons();
}
</script>
@endpush
