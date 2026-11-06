@extends('admin.mswdo-admin.layout')
@section('title', 'MSWDO – Edit Officer')
@section('page_title', 'Edit Officer')

@section('content')
@php
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', $adminName);
$initials = count($words) >= 2
    ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
    : strtoupper(substr($adminName,0,2));
@endphp

<style>
    /* ── Minimalist Form & Card ── */
    .form-card {
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-control, .form-select {
        width: 100%;
        background: #F8FAFC;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        font-size: 0.875rem;
        color: var(--text-primary);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        background: #fff;
    }

    /* ── Dropdown select – distinct look ── */
    .select-dropdown-wrap {
        position: relative;
    }
    .select-dropdown-wrap .form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        padding: 0.65rem 2.8rem 0.65rem 0.85rem;
        font-size: 0.875rem;
        color: var(--text-primary);
        cursor: pointer;
        transition: all .25s ease;
    }
    .select-dropdown-wrap .form-select:hover {
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 2px 8px rgba(26, 35, 126, .08);
    }
    .select-dropdown-wrap .form-select:focus {
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(26, 35, 126, .1);
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
        border-top-color: var(--primary);
    }
    .select-dropdown-wrap:focus-within::after {
        transform: translateY(-50%) rotate(180deg);
        border-top-color: var(--primary);
    }

    .select-hint {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-top: 0.35rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    .select-hint svg {
        width: 13px;
        height: 13px;
        color: var(--primary);
        flex-shrink: 0;
    }

    .btn-submit {
        background: var(--brand, #B11116);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.65rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-submit:hover {
        background: #8E0D11;
    }
    .btn-submit:focus-visible {
        outline: 3px solid rgba(177, 17, 22, .3);
        outline-offset: 2px;
    }

    /* ── Status Selection with Circle Radio Buttons ── */
    .status-selection {
        display: flex;
        gap: 1rem;
    }
    .status-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        background: #F8FAFC;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        cursor: pointer;
        transition: all .2s ease;
    }
    .status-option:hover {
        border-color: var(--primary);
        background: #EFF6FF;
    }
    .status-option.selected {
        border-color: var(--primary);
        background: #EFF6FF;
        box-shadow: 0 0 0 2px rgba(26, 35, 126, 0.1);
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
        border-color: var(--primary);
    }
    .status-option.selected .status-circle {
        border-color: var(--primary);
        background: var(--primary);
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
        font-size: 0.8rem;
        color: var(--text-primary);
        font-weight: 500;
    }
</style>

{{-- Page Header Banner --}}
<header class="dash-banner flex flex-col md:flex-row md:items-center justify-between gap-4 select-none">
    <div>
        <div class="dash-banner-title">Edit Officer</div>
        <div class="dash-banner-sub">
            <span>MSWDO Silang — Update Officer Information</span>
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

<!-- Form Card -->
<div class="form-card">
    <div class="mb-4">
        <h2 class="text-lg font-bold text-slate-800 m-0">Edit Officer Account</h2>
        <p class="text-xs text-slate-500 mt-1">Update officer information and account settings.</p>
    </div>

    @if(session('success'))
        <div class="p-3 mb-4 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200" id="successAlert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="p-3 mb-4 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200" id="errorAlert">
            <ul class="list-disc pl-5 m-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.officers.update', $officer->id) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter full name" value="{{ old('name', $officer->name) }}" required>
            </div>
            <div>
                <label class="form-label" for="officerUsername">Username</label>
                <input type="text" id="officerUsername" name="username" class="form-control" placeholder="e.g. maria.delacruz" value="{{ old('username', $officer->username) }}" minlength="3" maxlength="255" pattern="[A-Za-z0-9_.\-]+" autocomplete="off" autocapitalize="none" spellcheck="false">
                <p class="text-xs text-slate-500 mt-1"> Use at least 3 letters, numbers, periods, underscores, or hyphens. Log in with this username or your email.</p>
                @error('username') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email address" value="{{ old('email', $officer->email) }}" required>
            </div>
            <div>
                <label class="form-label">Role / Assignment</label>
                <div class="select-dropdown-wrap">
                    <select class="form-select" name="role" id="roleSelect" required>
                        @php
                            $selectedRole = old('role', $officer->role instanceof \BackedEnum ? $officer->role->value : $officer->role);
                        @endphp
                        <option value="" disabled @selected(!$selectedRole)>Select Role / Assignment</option>
                        @foreach(\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}" @selected($selectedRole === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="select-hint">
                    <i data-lucide="info"></i>
                    <span>Select a role from the dropdown</span>
                </p>
            </div>
            <div>
                <label class="form-label">Contact Number</label>
                <input type="text" name="phone" class="form-control" placeholder="e.g. 0917XXXXXXX" value="{{ old('phone', $officer->phone) }}">
            </div>
            <div>
                <label class="form-label">Signature Position</label>
                <select class="form-select" name="signature_position">
                    <option value="">None</option>
                    <option value="osca_head" {{ old('signature_position', $officer->signature_position) == 'osca_head' ? 'selected' : '' }}>OSCA Head</option>
                    <option value="mswdo_officer" {{ old('signature_position', $officer->signature_position) == 'mswdo_officer' ? 'selected' : '' }}>MSWDO Officer</option>
                    <option value="mswdo_staff" {{ old('signature_position', $officer->signature_position) == 'mswdo_staff' ? 'selected' : '' }}>MSWDO Staff</option>
                </select>
                <p class="select-hint">
                    <i data-lucide="info"></i>
                    <span>Select if this officer's signature should appear on ID cards</span>
                </p>
            </div>
            <div class="md:col-span-2 mt-2 flex justify-end gap-2">
                <button type="button" class="px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition" onclick="location.reload()">Cancel</button>
                <button type="submit" class="btn-submit">Update Officer</button>
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
</script>
@endpush
