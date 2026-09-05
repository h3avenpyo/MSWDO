@extends('admin.layout')
@section('title', 'MSWDO – Add Officers')
@section('page_title', 'Add Officers')

@section('content')
@php
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', $adminName);
$initials = count($words) >= 2
    ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
    : strtoupper(substr($adminName,0,2));
@endphp

<style>
    /* ── Modern Dashboard Base ── */
    .dashboard-container {
        background: #F8FAFC;
        min-height: 100vh;
        padding: 2rem;
    }

    /* ── Modern Page Header ── */
    .page-header {
        background: #1E3A8A;
        border-radius: 16px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
    }

    /* ── Form Card ── */
    .form-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        border-left: 4px solid #1E3A8A;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748B;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .form-control, .form-select {
        width: 100%;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: #374151;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1E3A8A;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        background: #ffffff;
    }

    /* ── Dropdown select ── */
    .select-dropdown-wrap {
        position: relative;
    }
    .select-dropdown-wrap .form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 8px;
        padding: 0.75rem 2.5rem 0.75rem 1rem;
        font-size: 0.875rem;
        color: #374151;
        cursor: pointer;
        transition: all .2s ease;
    }
    .select-dropdown-wrap .form-select:hover {
        border-color: #1E3A8A;
        background: #ffffff;
    }
    .select-dropdown-wrap .form-select:focus {
        border-color: #1E3A8A;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        outline: none;
    }
    .select-dropdown-wrap::after {
        content: '';
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #64748B;
        pointer-events: none;
        transition: transform .2s ease, border-color .2s ease;
    }
    .select-dropdown-wrap:hover::after {
        border-top-color: #1E3A8A;
    }
    .select-dropdown-wrap:focus-within::after {
        transform: translateY(-50%) rotate(180deg);
        border-top-color: #1E3A8A;
    }

    .select-hint {
        font-size: 0.75rem;
        color: #64748B;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .select-hint svg {
        width: 14px;
        height: 14px;
        color: #1E3A8A;
        flex-shrink: 0;
    }

    .btn-submit {
        background: #1E3A8A;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-submit:hover {
        background: #1E40AF;
    }

    .btn-cancel {
        background: #ffffff;
        color: #374151;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-cancel:hover {
        background: #EFF6FF;
        border-color: #1E3A8A;
        color: #1E3A8A;
    }

    /* ── Password strength feedback ── */
    .pw-checklist {
        list-style: none;
        margin: 0.5rem 0 0 0;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.25rem 0.75rem;
    }
    .pw-checklist li {
        font-size: 0.75rem;
        color: #64748B;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: color .25s ease;
    }
    .pw-checklist li .circle-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #CBD5E1;
        display: inline-block;
        flex-shrink: 0;
    }
    .pw-checklist li.met {
        color: #1E3A8A;
        font-weight: 600;
    }
    .pw-checklist li.met .circle-dot {
        background: #1E3A8A;
    }
    .pw-match-msg {
        font-size: 0.75rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        transition: all .25s ease;
    }
    .pw-match-msg.match { color: #1E3A8A; font-weight: 600; }
    .pw-match-msg.no-match { color: #DC2626; }

    /* ── Status Selection ── */
    .status-selection {
        display: flex;
        gap: 1rem;
    }
    .status-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 8px;
        cursor: pointer;
        transition: all .2s ease;
    }
    .status-option:hover {
        border-color: #1E3A8A;
        background: #ffffff;
    }
    .status-option.selected {
        border-color: #1E3A8A;
        background: #EFF6FF;
        box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
    }
    .status-option input[type="radio"] {
        display: none;
    }
    .status-circle {
        width: 18px;
        height: 18px;
        border: 2px solid #CBD5E1;
        border-radius: 50%;
        background: #fff;
        flex-shrink: 0;
        transition: all .2s ease;
        position: relative;
    }
    .status-option:hover .status-circle {
        border-color: #1E3A8A;
    }
    .status-option.selected .status-circle {
        border-color: #1E3A8A;
        background: #1E3A8A;
    }
    .status-option.selected .status-circle::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 6px;
        height: 6px;
        background: #fff;
        border-radius: 50%;
    }
    .status-label {
        font-size: 0.875rem;
        color: #374151;
        font-weight: 500;
    }

    /* ── Alert Messages ── */
    .alert-success {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        color: #1E3A8A;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .alert-error {
        background: #FEE2E2;
        border: 1px solid #FCA5A5;
        color: #DC2626;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
    }
</style>

{{-- Page Header --}}
<header class="page-header flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none">
    <div>
        <h1 class="font-['Public_Sans'] text-[28px] md:text-[32px] lg:text-[36px] font-bold text-white leading-none m-0">Officers Directory</h1>
        <p class="text-sm md:text-base text-white/90 mt-2 font-medium">MSWDO Silang — Manage Staff &amp; Officer Accounts</p>
    </div>
    <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
        <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-white/90" id="currentDateTime">Loading date...</div>
        <div class="w-12 h-12 rounded-full bg-white/20 text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:bg-white/30 select-none" title="Admin: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

<!-- Form Card -->
<div class="form-card">
    <div class="mb-4">
        <h2 class="text-xl font-bold text-[#1E3A8A] m-0">Create Officer Account</h2>
        <p class="text-sm text-slate-500 mt-1">Register a new social worker or administrator to access the MSWDO platform.</p>
    </div>

    @if(session('success'))
        <div class="alert-success mb-4" id="successAlert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error mb-4" id="errorAlert">
            <ul class="list-disc pl-5 m-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.officers.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter full name" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email address" value="{{ old('email') }}" required>
            </div>
            <div>
                <label class="form-label">Role / Assignment</label>
                <div class="select-dropdown-wrap">
                    <select class="form-select" name="role" id="roleSelect" required>
                        <option value="" disabled selected>Select Role / Assignment</option>
                        <option value="Senior Citizen officer" {{ old('role') == 'Senior Citizen officer' ? 'selected' : '' }}>Senior Citizen Officer</option>
                        <option value="Financial assistance officer" {{ old('role') == 'Financial assistance officer' ? 'selected' : '' }}>Financial Assistance Officer</option>
                        <option value="financialstep1" {{ old('role') == 'financialstep1' ? 'selected' : '' }}>Financial Assistance Step 1</option>
                        <option value="financialstep2" {{ old('role') == 'financialstep2' ? 'selected' : '' }}>Financial Assistance Step 2</option>
                        <option value="eligibility_checker" {{ old('role') == 'eligibility_checker' ? 'selected' : '' }}>Social Case Worker (Checker)</option>
                        <option value="social_worker" {{ old('role') == 'social_worker' ? 'selected' : '' }}>Social Case Worker (Encoder)</option>
                        <option value="encoder" {{ old('role') == 'encoder' ? 'selected' : '' }}>Encoder</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
                <p class="select-hint">
                    <i data-lucide="info"></i>
                    <span>Select a role from the dropdown</span>
                </p>
            </div>
            <div>
                <label class="form-label">Contact Number</label>
                <input type="text" name="phone" class="form-control" placeholder="e.g. 0917XXXXXXX" value="{{ old('phone') }}">
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" id="passwordInput" name="password" class="form-control" placeholder="Create password" required oninput="checkPassword()">
                <div id="pwFeedback" class="mt-2" style="display:none;">
                    <ul class="pw-checklist">
                        <li id="reqLength"><span class="circle-dot"></span> At least 8 characters</li>
                        <li id="reqUpper"><span class="circle-dot"></span> One uppercase letter</li>
                        <li id="reqLower"><span class="circle-dot"></span> One lowercase letter</li>
                        <li id="reqNumber"><span class="circle-dot"></span> One number</li>
                        <li id="reqSpecial"><span class="circle-dot"></span> One special character</li>
                    </ul>
                </div>
            </div>
            <div>
                <label class="form-label">Confirm Password</label>
                <input type="password" id="confirmPasswordInput" name="password_confirmation" class="form-control" placeholder="Confirm password" required oninput="checkPassword()">
                <div id="pwMatchMsg" class="pw-match-msg" style="display:none;"></div>
            </div>
            <div>
                <label class="form-label">Signature Position</label>
                <select class="form-select" name="signature_position">
                    <option value="">None</option>
                    <option value="osca_head" {{ old('signature_position') == 'osca_head' ? 'selected' : '' }}>OSCA Head</option>
                    <option value="mswdo_officer" {{ old('signature_position') == 'mswdo_officer' ? 'selected' : '' }}>MSWDO Officer</option>
                    <option value="mswdo_staff" {{ old('signature_position') == 'mswdo_staff' ? 'selected' : '' }}>MSWDO Staff</option>
                </select>
                <p class="select-hint">
                    <i data-lucide="info"></i>
                    <span>Select if this officer's signature should appear on ID cards</span>
                </p>
            </div>
            <div class="md:col-span-2 mt-2 flex justify-end gap-2">
                <button type="button" class="btn-cancel" onclick="location.reload()">Cancel</button>
                <button type="submit" class="btn-submit">Add Officer</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Date/time
        function updateDateTime() {
            const now = new Date();
            const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true };
            const el = document.getElementById('currentDateTime');
            if (el) el.textContent = now.toLocaleDateString('en-US', opts).replace(',', ' at');
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);

        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Status selection visual feedback
        const statusOptions = document.querySelectorAll('.status-option');
        statusOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            option.addEventListener('click', function(e) {
                statusOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
            });
            // Initialize selected state
            if (radio.checked) {
                option.classList.add('selected');
            }
        });

        // Show success popup if officer was just created
        @if($officerCreated ?? false)
            Swal.fire({
                title: 'Officer Added Successfully!',
                icon: 'success',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'Continue',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                },
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(function() {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 500);
            }, 3000);
        }

        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            setTimeout(function() {
                errorAlert.style.transition = 'opacity 0.5s ease';
                errorAlert.style.opacity = '0';
                setTimeout(function() {
                    errorAlert.style.display = 'none';
                }, 500);
            }, 3000);
        }
    });

    function checkPassword() {
        const pw = document.getElementById('passwordInput').value;
        const feedback = document.getElementById('pwFeedback');

        if (pw.length === 0) {
            feedback.style.display = 'none';
            return;
        }
        feedback.style.display = 'block';

        const checks = {
            length:  pw.length >= 8,
            upper:   /[A-Z]/.test(pw),
            lower:   /[a-z]/.test(pw),
            number:  /[0-9]/.test(pw),
            special: /[^A-Za-z0-9]/.test(pw)
        };

        toggleReq('reqLength',  checks.length);
        toggleReq('reqUpper',   checks.upper);
        toggleReq('reqLower',   checks.lower);
        toggleReq('reqNumber',  checks.number);
        toggleReq('reqSpecial', checks.special);

        if (document.getElementById('confirmPasswordInput').value.length > 0) {
            checkPasswordMatch();
        }
    }

    function toggleReq(id, met) {
        const el = document.getElementById(id);
        if (met) {
            el.classList.add('met');
        } else {
            el.classList.remove('met');
        }
    }

    function checkPasswordMatch() {
        const pw = document.getElementById('passwordInput').value;
        const cpw = document.getElementById('confirmPasswordInput').value;
        const msg = document.getElementById('pwMatchMsg');

        if (cpw.length === 0) {
            msg.style.display = 'none';
            return;
        }
        msg.style.display = 'flex';

        if (pw === cpw) {
            msg.className = 'pw-match-msg match';
            msg.innerHTML = '<span>✓ Passwords match</span>';
        } else {
            msg.className = 'pw-match-msg no-match';
            msg.innerHTML = '<span>✕ Passwords do not match</span>';
        }
    }
</script>
@endpush
