@extends('admin.layout')
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
        background: var(--primary);
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
        background: var(--primary-dark);
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

{{-- Page Header --}}
<header class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none mb-6">
    <div>
        <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Edit Officer</h1>
        <p class="text-sm text-slate-500 mt-1 font-medium">MSWDO Silang — Update Officer Information</p>
    </div>
    <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
        <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Loading date...</div>
        <div class="w-11 h-11 rounded-full bg-[#1A237E] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(26,35,126,0.3)] hover:scale-105 select-none" title="Admin: {{ $adminName }}">
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
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email address" value="{{ old('email', $officer->email) }}" required>
            </div>
            <div>
                <label class="form-label">Role / Assignment</label>
                <div class="select-dropdown-wrap">
                    <select class="form-select" name="role" id="roleSelect" required>
                        <option value="" disabled selected>Select Role / Assignment</option>
                        <option value="Senior Citizen officer" {{ old('role', $officer->role) == 'Senior Citizen officer' ? 'selected' : '' }}>Senior Citizen Officer</option>
                        <option value="Financial assistance officer" {{ old('role', $officer->role) == 'Financial assistance officer' ? 'selected' : '' }}>Financial Assistance Officer</option>
                        <option value="financialstep1" {{ old('role', $officer->role) == 'financialstep1' ? 'selected' : '' }}>Financial Assistance Step 1</option>
                        <option value="financialstep2" {{ old('role', $officer->role) == 'financialstep2' ? 'selected' : '' }}>Financial Assistance Step 2</option>
                        <option value="eligibility_checker" {{ old('role', $officer->role) == 'eligibility_checker' ? 'selected' : '' }}>Social Case Worker (Checker)</option>
                        <option value="social_worker" {{ old('role', $officer->role) == 'social_worker' ? 'selected' : '' }}>Social Case Worker (Encoder)</option>
                        <option value="encoder" {{ old('role', $officer->role) == 'encoder' ? 'selected' : '' }}>Encoder</option>
                        <option value="staff" {{ old('role', $officer->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role', $officer->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
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
                <label class="form-label">Status</label>
                <div class="status-selection">
                    <label class="status-option {{ old('status', $officer->status) == 'active' ? 'selected' : '' }}">
                        <input type="radio" name="status" value="active" {{ old('status', $officer->status) == 'active' ? 'checked' : '' }} required>
                        <span class="status-circle"></span>
                        <span class="status-label">Active</span>
                    </label>
                    <label class="status-option {{ old('status', $officer->status) == 'inactive' ? 'selected' : '' }}">
                        <input type="radio" name="status" value="inactive" {{ old('status', $officer->status) == 'inactive' ? 'checked' : '' }}>
                        <span class="status-circle"></span>
                        <span class="status-label">Inactive</span>
                    </label>
                </div>
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
            <div class="md:col-span-2">
                <label class="form-label">Signature Image</label>
                <input type="file" name="signature_image" class="form-control" accept="image/*">
                @if($officer->signature_image)
                    <p class="select-hint">
                        <i data-lucide="check"></i>
                        <span>Current signature: {{ basename($officer->signature_image) }}</span>
                    </p>
                @endif
                <p class="select-hint">
                    <i data-lucide="info"></i>
                    <span>Upload signature image (PNG, JPG) for ID cards</span>
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
