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
    /* ── Full-height layout ── */
    html, body { overflow-x: hidden !important; overflow-y: auto !important; }
    .main {
        display: flex !important;
        flex-direction: column !important;
        padding-top: 14px !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
    }
    @media (max-width: 767.98px) { .main { padding-top: 72px !important; } }

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

    /* ── Pagination ── */
    .sc-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; flex-shrink: 0; padding: 4px 0; flex-wrap: wrap; }
    .sc-pagination-info { font-size: 0.813rem; color: #6B7280; font-weight: 500; }
    .sc-pagination-controls { display: flex; gap: 4px; flex-wrap: wrap; }
    .sc-page-btn { height: 36px; min-width: 36px; padding: 0 10px; border: 1px solid #E5E7EB; border-radius: 6px; background: #fff; color: #374151; font-size: 0.813rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
    .sc-page-btn:hover:not(:disabled) { background: #F3F4F6; border-color: #D1D5DB; }
    .sc-page-btn.active { background: #1A237E; color: #fff; border-color: #1A237E; font-weight: 700; }
    .sc-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

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

    .filter-search-wrap { display: flex; align-items: stretch; width: 100%; border-radius: 8px; box-sizing: border-box; transition: box-shadow .15s; }
    .filter-search-wrap:focus-within { box-shadow: 0 0 0 3px rgba(26,35,126,.12); border-radius: 8px; }
    .filter-search-btn { height: 44px !important; padding: 0 20px; border: 1px solid #1A237E; border-radius: 0 8px 8px 0; background: #1A237E; color: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: background .15s; flex-shrink: 0; box-sizing: border-box !important; margin: 0 !important; align-self: stretch; }
    .filter-search-btn:hover { background: #121858; }

    .filter-dropdown { flex: 0 1 200px; min-width: 180px; position: relative; }
    .filter-select-btn { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 0 14px; height: 44px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 0.875rem; cursor: pointer; background: #fff; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
    .filter-select-btn:hover { border-color: #9CA3AF; }
    .filter-select-label { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #111827; font-weight: 500; }
    .filter-menu { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #D1D5DB; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 50; max-height: 260px; overflow-y: auto; padding: 4px; }
    .role-opt { padding: 8px 12px; border-radius: 6px; font-size: 14px; cursor: pointer; transition: background .15s; }
    .role-opt:hover { background: #F3F4F6; }
    .role-opt.selected { background: #EEF2FF; color: #1A237E; font-weight: 600; }
    #roleBtn.active { border-color: #1A237E; background: #EEF2FF; }

    /* ── Mobile: stacked card rows (no horizontal scroll) ── */
    @media (max-width: 767.98px) {
        .officers-table-wrap { padding: 1rem; }
        .officers-scroll { overflow: visible !important; }
        .gov-table { display: block; width: 100%; margin-top: 0.75rem; }
        .gov-table thead { display: none; }
        .gov-table tbody { display: block; }
        .gov-table tbody tr {
            display: block;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 12px;
            padding: 10px 12px;
            box-shadow: var(--shadow);
        }
        .gov-table tbody tr:last-child { margin-bottom: 0; }
        .gov-table tbody td {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            border: none;
            border-bottom: 1px solid var(--border);
            white-space: normal;
            word-break: break-word;
            text-align: right;
        }
        .gov-table tbody td:last-child { border-bottom: none; }
        .gov-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .03em;
            flex-shrink: 0;
            min-width: 70px;
            text-align: left;
        }
        .gov-table tbody td[data-label="Action"] {
            justify-content: flex-end;
            border-bottom: none;
            padding-top: 10px;
        }
        .gov-table tbody td[data-label="Action"]::before { display: none !important; }
        .table-actions { justify-content: flex-end; }
        .sc-pagination { flex-direction: column !important; align-items: center !important; gap: 8px !important; }
        .sc-pagination-controls { justify-content: center !important; }
        .sc-pagination-info { text-align: center !important; }
        
        .filter-dropdown { min-width: 0; width: 100%; }
        .filter-select-btn { width: 100%; }
    }

    /* ── Desktop: full-height, no-scroll ── */
    @media (min-width: 1200px) {
        .main { height: 100vh !important; overflow: hidden !important; }
        .officers-table-wrap { flex: 1; min-height: 0; overflow-y: auto; display: flex; flex-direction: column; }
        .sc-pagination { flex-direction: row; justify-content: space-between; margin-top: 12px; flex-shrink: 0; }
        .sc-pagination-controls { justify-content: flex-end; }
        .sc-pagination-info { text-align: left; }
        
        .filter-dropdown { flex: 0 1 200px; min-width: 180px; }
    }
    
    .sc-pagination { flex-shrink: 0; }
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
<div class="flex items-center justify-between flex-wrap gap-2 mb-3">
    <div class="flex items-center gap-3 flex-wrap" style="align-items: flex-end !important;">
        <div class="filter-item filter-search" style="display: flex; flex-direction: column; gap: 6px; flex: 0 0 auto;">
            <label class="filter-label">Search</label>
            <div class="filter-search-wrap" style="display: flex; align-items: stretch; width: 100%; border-radius: 8px; box-sizing: border-box; transition: box-shadow .15s;">
                <input type="text" class="form-control text-xs" placeholder="Search officer..." style="width: 220px; padding-left: 16px; height: 44px; border: 1px solid #D1D5DB; border-right: none; border-radius: 8px 0 0 8px; flex: 1 1 auto; min-width: 0; box-sizing: border-box !important; margin: 0 !important;" id="searchInput" value="{{ request()->get('search', '') }}" oninput="handleSearch()" onkeydown="if(event.key==='Enter'){event.preventDefault();handleSearch();}">
                <button type="button" class="filter-search-btn" style="height: 44px !important; padding: 0 20px; border: 1px solid #1A237E; border-radius: 0 8px 8px 0; background: #1A237E; color: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: background .15s; flex-shrink: 0; box-sizing: border-box !important; margin: 0 !important; align-self: stretch;" onclick="handleSearch()">
                    <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
        </div>
        <div class="filter-item filter-dropdown" id="roleDropdown" style="display: flex; flex-direction: column; gap: 6px; flex: 0 0 auto;">
            <label class="filter-label">Filter by Role</label>
            <div onclick="toggleRoleMenu()" class="filter-select-btn" id="roleBtn">
                <span id="roleLabel" class="filter-select-label">{{ request()->get('role', 'All Roles') }}</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="roleMenu" class="filter-menu" style="display:none"></div>
        </div>
        <button type="button" onclick="clearFilters()" style="height: 44px; padding: 0 16px; border: 1px solid #DC2626; border-radius: 8px; background: #fff; color: #DC2626; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all .15s; font-size: 0.875rem; font-weight: 500; gap: 6px; flex-shrink: 0;" onmouseover="this.style.borderColor='#B91C1C';this.style.color='#B91C1C';this.style.background='#FEF2F2';" onmouseout="this.style.borderColor='#DC2626';this.style.color='#DC2626';this.style.background='#fff';">
            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
            Clear
        </button>
    </div>
</div>

<div class="officers-table-wrap">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
        <div>
            <h3 class="text-base font-bold text-slate-800 m-0 flex items-center gap-2">
                <i data-lucide="user-check" style="width: 20px; height: 20px; color: var(--primary);"></i>
                <span>MSWDO Active Officers</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Registered system accounts and status indicators.</p>
        </div>
    </div>

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
                    <td data-label="Officer">
                        <div class="flex items-center gap-2.5">
                            <div class="avatar-initial">{{ strtoupper(substr($officer->name ?? 'O', 0, 2)) }}</div>
                            <span class="font-semibold text-slate-800">{{ $officer->name ?? 'Officer' }}</span>
                        </div>
                    </td>
                    <td data-label="Email" class="hidden md:table-cell text-slate-600 text-sm">{{ $officer->email ?? '-' }}</td>
                    <td data-label="Role" class="text-slate-700 text-sm font-medium">
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
                    <td data-label="Contact" class="hidden lg:table-cell text-slate-600 text-sm">{{ $officer->phone ?? '-' }}</td>
                    <td data-label="Status" class="hidden sm:table-cell">
                        @php $statusVal = is_object($officer->status) ? $officer->status->value : $officer->status; @endphp
                        @if($statusVal === 'active' || empty($statusVal))
                            <span class="badge-status active">Active</span>
                        @else
                            <span class="badge-status inactive">Inactive</span>
                        @endif
                    </td>
                    <td data-label="Action">
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

<!-- Laravel Pagination -->
<div class="sc-pagination">
    <div class="sc-pagination-info">
        @if($officers->count() > 0)
            Showing {{ $officers->firstItem() }}–{{ $officers->lastItem() }} of {{ $officers->total() }} Records
        @else
            Showing 0 of 0 Records
        @endif
    </div>
    <div class="sc-pagination-controls">
        {{ $officers->appends(request()->only(['search', 'role', 'per_page']))->links('vendor.pagination.custom-simple') }}
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
        
        // Initialize role dropdown and set current filter from URL
        populateRoleDropdown();
        const urlRole = "{{ request()->get('role', 'All Roles') }}";
        if (urlRole && urlRole !== 'All Roles') {
            window.roleFilterState = urlRole;
            document.getElementById('roleLabel').textContent = urlRole;
            var btn = document.getElementById('roleBtn');
            btn.classList.add('active');
            btn.setAttribute('data-filter', urlRole);
            highlightRoleOpt();
        }

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

    function handleSearch() {
        const searchValue = document.getElementById('searchInput').value.trim();
        const roleValue = window.roleFilterState === 'All Roles' ? '' : window.roleFilterState;
        
        const url = new URL(window.location.href);
        if (searchValue) {
            url.searchParams.set('search', searchValue);
        } else {
            url.searchParams.delete('search');
        }
        if (roleValue) {
            url.searchParams.set('role', roleValue);
        } else {
            url.searchParams.delete('role');
        }
        
        window.location.href = url.toString();
    }

    function clearFilters() {
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        url.searchParams.delete('role');
        window.location.href = url.toString();
    }

    // Role filter state
    window.roleFilterState = 'All Roles';

    function populateRoleDropdown() {
        const roleMenu = document.getElementById('roleMenu');
        if(roleMenu) {
            const roles = ['All Roles', 'Administrator', 'Social Case Worker (Encoder)', 'Social Case Worker (Checker)', 'Encoder', 'Staff', 'Senior Citizen Officer', 'Financial Assistance Officer', 'Financial Assistance Step 1', 'Financial Assistance Step 2'];
            roleMenu.innerHTML = '';
            roles.forEach(role => {
                roleMenu.innerHTML += `<div class="role-opt" data-value="${role}" onclick="selectRole(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${role}</div>`;
            });
            highlightRoleOpt();
        }
    }

    function toggleRoleMenu() {
        var menu = document.getElementById('roleMenu');
        var arrow = document.querySelector('#roleBtn [data-lucide="chevron-down"]');
        if(menu.style.display === 'none' || !menu.style.display) {
            menu.style.display = 'block';
            if(arrow) arrow.style.transform = 'rotate(180deg)';
            highlightRoleOpt();
        } else {
            menu.style.display = 'none';
            if(arrow) arrow.style.transform = '';
        }
        event.stopPropagation();
    }

    function selectRole(el) {
        var val = el.getAttribute('data-value');
        window.roleFilterState = val;
        document.getElementById('roleLabel').textContent = el.textContent;
        document.getElementById('roleMenu').style.display = 'none';
        var arrow = document.querySelector('#roleBtn [data-lucide="chevron-down"]');
        if(arrow) arrow.style.transform = '';
        highlightRoleOpt();
        var btn = document.getElementById('roleBtn');
        if(val && val !== 'All Roles') { 
            btn.classList.add('active'); 
            btn.setAttribute('data-filter', val); 
        } else { 
            btn.classList.remove('active'); 
            btn.removeAttribute('data-filter'); 
        }
        handleSearch();
        event.stopPropagation();
    }

    function highlightRoleOpt() {
        var opts = document.querySelectorAll('.role-opt');
        opts.forEach(function(o) {
            if(o.getAttribute('data-value') === window.roleFilterState) o.classList.add('selected');
            else o.classList.remove('selected');
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        var roleDD = document.getElementById('roleDropdown');
        if(roleDD && !roleDD.contains(e.target)) {
            var menu = document.getElementById('roleMenu');
            var arrow = document.querySelector('#roleBtn [data-lucide="chevron-down"]');
            if(menu) menu.style.display = 'none';
            if(arrow) arrow.style.transform = '';
        }
    }, true);

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
