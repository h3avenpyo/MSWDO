@extends('admin.layout')
@section('title', 'MSWDO – Officers Directory')
@section('page_title', 'Officers Directory')

@section('content')
@php
$adminName = session('admin_user_name') ?? 'Admin User';
$words = explode(' ', $adminName);
$initials = count($words) >= 2
    ? strtoupper(substr($words[0],0,1).substr($words[1],0,1))
    : strtoupper(substr($adminName,0,2));
@endphp

<style>
    /* ── Table Card ── */
    .officers-table-wrap {
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 1.5rem;
    }
    .gov-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
        margin-top: 1rem;
    }
    .gov-table th {
        background: #F8FAFC;
        color: var(--text-secondary);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid var(--border);
        text-align: left;
        white-space: nowrap;
    }
    .gov-table td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
        color: var(--text-primary);
    }
    .gov-table tr:hover td { background: #F8FAFC; }
    .gov-table tr:last-child td { border-bottom: none; }

    /* ── Table Action Buttons matching Senior Masterlist Theme ── */
    .table-actions {
        display: flex;
        gap: 6px;
        align-items: center;
    }
    .table-action-btn {
        width: 34px !important;
        height: 34px !important;
        min-height: 34px !important;
        max-height: 34px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease;
        border: 1px solid var(--border);
        text-decoration: none;
    }
    .table-action-btn:hover {
        transform: none;
    }
    .table-action-btn svg {
        width: 16px !important;
        height: 16px !important;
    }
    .table-action-btn.btn-edit {
        background: #EEF2FF;
        color: #4338CA;
        border-color: #C7D2FE;
    }
    .table-action-btn.btn-edit:hover {
        background: #4338CA;
        color: #FFFFFF;
        border-color: #4338CA;
    }
    .table-action-btn.btn-deactivate {
        background: #FEF2F2;
        color: #DC2626;
        border-color: #FECACA;
    }
    .table-action-btn.btn-deactivate:hover {
        background: #DC2626;
        color: #FFFFFF;
        border-color: #DC2626;
    }

    .avatar-initial {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--primary);
        color: #FFFFFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: .04em;
    }
    .badge-status.active { background: #DCFCE7; color: #15803D; }
    .badge-status.inactive { background: #FEE2E2; color: #DC2626; }

    .form-control {
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
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        background: #fff;
    }
</style>

{{-- Page Header --}}
<header class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none mb-6">
    <div>
        <h1 class="font-['Public_Sans'] text-[24px] md:text-[28px] lg:text-[32px] font-bold text-[#111827] leading-none m-0">Officers Directory</h1>
        <p class="text-sm text-slate-500 mt-1 font-medium">MSWDO Silang — View All Registered Officers</p>
    </div>
    <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
        <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-[#6B7280]" id="currentDateTime">Loading date...</div>
        <div class="w-11 h-11 rounded-full bg-[#1A237E] text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:shadow-[0_4px_12px_rgba(26,35,126,0.3)] hover:scale-105 select-none" title="Admin: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

@if(session('success'))
    <div class="p-3 mb-4 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200" id="successAlert" style="display:none;">{{ session('success') }}</div>
@endif

<!-- Directory Table -->
<div class="officers-table-wrap">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
        <div>
            <h3 class="text-base font-bold text-slate-800 m-0 flex items-center gap-2">
                <i data-lucide="user-check" style="width: 20px; height: 20px; color: var(--primary);"></i>
                <span>MSWDO Active Officers</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Registered system accounts and status indicators.</p>
        </div>
        <div style="position: relative;">
            <input type="text" class="form-control text-xs" placeholder="Search officer..." style="max-width: 220px; padding-left: 2.2rem; height: 38px;" oninput="filterTable(this.value)">
            <i data-lucide="search" style="width: 15px; height: 15px; position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94A3B8; pointer-events: none;"></i>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="gov-table" id="officersTable">
            <thead>
                <tr>
                    <th>Officer</th>
                    <th class="hidden md:table-cell">Email</th>
                    <th>Role</th>
                    <th class="hidden lg:table-cell">Contact</th>
                    <th class="hidden sm:table-cell">Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="officersTableBody">
                @forelse($officers as $officer)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="avatar-initial">{{ strtoupper(substr($officer->name ?? 'O', 0, 2)) }}</div>
                                <span class="font-semibold text-slate-800">{{ $officer->name ?? 'Officer' }}</span>
                            </div>
                        </td>
                        <td class="hidden md:table-cell text-slate-600 text-sm">{{ $officer->email ?? '-' }}</td>
                        <td class="text-slate-700 text-sm font-medium">
                            @php
                                $roleLabel = $officer->role;
                                if (is_object($officer->role) && method_exists($officer->role, 'label')) {
                                    $roleLabel = $officer->role->label();
                                } elseif (is_string($officer->role)) {
                                    $roleLabel = ucfirst(str_replace('_', ' ', $officer->role));
                                    // Handle specific role name mappings
                                    $roleMap = [
                                        'admin' => 'Administrator',
                                        'social_worker' => 'Social Case Worker (Encoder)',
                                        'eligibility_checker' => 'Social Case Worker (Checker)',
                                        'encoder' => 'Encoder',
                                        'staff' => 'Staff',
                                        'senior_citizen_officer' => 'Senior Citizen Officer',
                                        'financial_assistance_officer' => 'Financial Assistance Officer',
                                        'financialstep1' => 'Financial Assistance Step 1',
                                        'financialstep2' => 'Financial Assistance Step 2',
                                    ];
                                    $roleLabel = $roleMap[strtolower($officer->role)] ?? $roleLabel;
                                }
                            @endphp
                            {{ $roleLabel }}
                        </td>
                        <td class="hidden lg:table-cell text-slate-600 text-sm">{{ $officer->phone ?? '-' }}</td>
                        <td class="hidden sm:table-cell">
                            @php $statusVal = is_object($officer->status) ? $officer->status->value : $officer->status; @endphp
                            @if($statusVal === 'active' || empty($statusVal))
                                <span class="badge-status active">Active</span>
                            @else
                                <span class="badge-status inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @php $statusVal = is_object($officer->status) ? $officer->status->value : $officer->status; @endphp
                            <div class="table-actions">
                                <a href="{{ route('admin.officers.edit', $officer->id) }}" class="table-action-btn btn-edit" title="Edit Officer">
                                    <i data-lucide="pencil"></i>
                                </a>
                                @if($statusVal === 'active' || empty($statusVal))
                                    <button type="button" class="table-action-btn btn-deactivate" title="Deactivate Officer" onclick="deactivateOfficer({{ $officer->id }}, '{{ $officer->name }}')">
                                        <i data-lucide="ban"></i>
                                    </button>
                                @else
                                    <button type="button" class="table-action-btn btn-edit" title="Activate Officer" style="background:#DCFCE7;color:#15803D;border-color:#86EFAC;" onclick="activateOfficer({{ $officer->id }}, '{{ $officer->name }}')">
                                        <i data-lucide="check"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-slate-400 py-6">No officers registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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

        const flashSuccess = @json(session('success'));
        if (flashSuccess) {
            Swal.fire({
                title: 'Success',
                text: flashSuccess,
                icon: 'success',
                confirmButtonColor: '#16A34A',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        }
    });

    function filterTable(query) {
        const q = query.toLowerCase().trim();
        const rows = document.querySelectorAll('#officersTableBody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }

    function deactivateOfficer(id, name) {
        Swal.fire({
            title: 'Deactivate Officer?',
            text: `Are you sure you want to deactivate ${name}? They will no longer be able to access the system.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Deactivate',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/officers/${id}/deactivate`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function activateOfficer(id, name) {
        Swal.fire({
            title: 'Activate Officer?',
            text: `Are you sure you want to activate ${name}? They will regain access to the system.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Activate',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/officers/${id}/activate`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
