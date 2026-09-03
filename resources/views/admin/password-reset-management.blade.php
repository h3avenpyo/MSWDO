@extends('admin.layout')
@section('title', 'MSWDO – Password Reset Management')
@section('page_title', 'Password Reset Management')

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

    /* ── Modern Page Header ── */
    .page-header {
        background: #1E3A8A;
        border-radius: 16px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(30, 58, 138, 0.1);
    }

    /* ── Panel Header ── */
    .panel-header { margin-bottom: 1rem; }
    .panel-header h2 { font-size: 2rem; font-weight: 700; color: #1E3A8A; margin: 0; }
    .panel-header p { font-size: 0.875rem; color: #64748B; margin: 0.25rem 0 0; }

    /* ── Table wrap ── */
    .password-reset-table-wrap {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        border-left: 4px solid #1E3A8A;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }

    .form-control {
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
    .form-control:focus {
        border-color: #1E3A8A;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        background: #fff;
    }

    /* ── Table Scroll Container ── */
    .table-scroll-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #1E3A8A #EFF6FF;
    }
    .table-scroll-container::-webkit-scrollbar {
        height: 8px;
    }
    .table-scroll-container::-webkit-scrollbar-track {
        background: #EFF6FF;
        border-radius: 4px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb {
        background: #1E3A8A;
        border-radius: 4px;
    }
    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #1E40AF;
    }

    /* ── Table ── */
    .gov-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
        margin-top: 1rem;
    }
    .gov-table th {
        background: #EFF6FF;
        color: #1E3A8A;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.25rem;
        border-bottom: 3px solid #1E3A8A;
        text-align: left;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .gov-table th:first-child { border-radius: 8px 0 0 0; }
    .gov-table th:last-child { border-radius: 0 8px 0 0; }
    .gov-table td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 3px solid #E5E7EB;
        color: #374151;
        white-space: nowrap;
    }
    .gov-table tr:last-child td { border-bottom: none; }

    /* ── Badges ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-approved { background: #EFF6FF; color: #1E3A8A; }
    .status-completed { background: #DCFCE7; color: #15803D; }
    .status-rejected { background: #FEE2E2; color: #DC2626; }

    /* ── Filter Styles ── */
    .filter-item { display: flex; flex-direction: column; gap: 6px; }
    .filter-label { font-size: 0.75rem; font-weight: 600; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; }
    .filter-search-wrap { display: flex; align-items: stretch; width: 100%; border-radius: 8px; box-sizing: border-box; transition: box-shadow .15s; }
    .filter-search-wrap:focus-within { box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12); border-radius: 8px; }
    .filter-search-btn { height: 44px !important; padding: 0 20px; border: 1px solid #1E3A8A; border-radius: 0 8px 8px 0; background: #1E3A8A; color: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: background .15s; flex-shrink: 0; box-sizing: border-box !important; margin: 0 !important; align-self: stretch; }
    .filter-search-btn:hover { background: #1E40AF; }

    .filter-dropdown { flex: 0 1 200px; min-width: 180px; position: relative; }
    .filter-select-btn { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 0 14px; height: 44px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 0.875rem; cursor: pointer; background: #EFF6FF; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
    .filter-select-btn:hover { border-color: #1E3A8A; background: #ffffff; }
    .filter-select-label { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #374151; font-weight: 500; }
    .filter-menu { position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.12); z-index: 50; max-height: 260px; overflow-y: auto; padding: 4px; }
    .status-opt { padding: 8px 12px; border-radius: 6px; font-size: 14px; cursor: pointer; transition: background .15s; }
    .status-opt:hover { background: #EFF6FF; }
    .status-opt.selected { background: #EFF6FF; color: #1E3A8A; font-weight: 600; }
    #statusBtn.active { border-color: #1E3A8A; background: #EFF6FF; }

    .filter-search input::placeholder { color: #64748B; font-weight: 500; font-size: 0.875rem; }

    .filter-reset-btn { display: none !important; }
    .filter-reset-btn.visible { display: inline-flex !important; }

    /* ── Buttons ── */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }
    .btn-success { background: #1E3A8A; color: white; }
    .btn-success:hover { background: #1E40AF; }
    .btn-danger { background: #DC2626; color: white; }
    .btn-danger:hover { background: #B91C1C; }
    .btn-secondary { background: #64748B; color: white; }
    .btn-secondary:hover { background: #475569; }

    /* ── Pagination ── */
    .sc-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; flex-shrink: 0; padding: 4px 0; flex-wrap: wrap; }
    .sc-pagination-info { font-size: 0.813rem; color: #64748B; font-weight: 500; }
    .sc-pagination-controls { display: flex; gap: 4px; flex-wrap: wrap; }
    .sc-page-btn { height: 36px; min-width: 36px; padding: 0 10px; border: 1px solid #E5E7EB; border-radius: 8px; background: #fff; color: #374151; font-size: 0.813rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
    .sc-page-btn:hover:not(:disabled) { background: #EFF6FF; border-color: #1E3A8A; color: #1E3A8A; }
    .sc-page-btn.active { background: #1E3A8A; color: #fff; border-color: #1E3A8A; font-weight: 700; }
    .sc-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* ── Empty State ── */
    .empty-row { background: transparent !important; border: none !important; box-shadow: none !important; }
    .empty-cell { padding: 3rem 1rem !important; text-align: center !important; border: none !important; }
    .empty-cell::before { display: none !important; content: none !important; }
    .empty-state-content { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 12px; padding: 2rem 1rem; margin-top: 50px; }
    .empty-icon-wrap { width: 72px; height: 72px; border-radius: 50%; background: #EFF6FF; display: flex; align-items: center; justify-content: center; color: #1E3A8A; }
    .empty-title { font-size: 1rem; font-weight: 600; color: #374151; margin: 0; }
    .empty-subtitle { font-size: 0.85rem; color: #9CA3AF; margin: 0; }

    /* ── Mobile: stacked card rows ── */
    @media (max-width: 767.98px) {
        .password-reset-table-wrap { padding: 1rem; }
        .gov-table { display: block; width: 100%; margin-top: 0.75rem; }
        .gov-table thead { display: none; }
        .gov-table tbody { display: block; }
        .gov-table tbody tr {
            display: block;
            background: #ffffff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            margin-bottom: 12px;
            padding: 10px 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .gov-table tbody tr:last-child { margin-bottom: 0; }
        .gov-table tbody td {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            border: none;
            border-bottom: 1px solid #E5E7EB;
            white-space: normal;
            word-break: break-word;
            text-align: right;
        }
        .gov-table tbody td:last-child { border-bottom: none; }
        .gov-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #64748B;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .03em;
            flex-shrink: 0;
            min-width: 80px;
            text-align: left;
        }
        .gov-table tbody td[data-label="Action"] {
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            border-bottom: none;
            padding-top: 10px;
        }
        .gov-table tbody td[data-label="Action"]::before { display: none !important; }
        .gov-table tbody td.empty-cell { display: flex !important; justify-content: center !important; align-items: center !important; text-align: center !important; padding: 2rem 1rem !important; }
        .gov-table tbody td.empty-cell::before { display: none !important; }
        .sc-pagination { flex-direction: column !important; align-items: center !important; gap: 8px !important; }
        .sc-pagination-controls { justify-content: center !important; }
        .sc-pagination-info { text-align: center !important; }
        
        .filter-dropdown { min-width: 0; width: 100%; }
        .filter-select-btn { width: 100%; }
    }

    /* ── Tablet ── */
    @media (min-width: 768px) and (max-width: 1199.98px) {
        .sc-pagination { flex-direction: row; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
    }

    /* ── Desktop: full-height, no-scroll ── */
    @media (min-width: 1200px) {
        .main { height: 100vh !important; overflow: hidden !important; }
        .password-reset-table-wrap { flex: 1; min-height: 0; overflow-y: auto; display: flex; flex-direction: column; }
        .sc-pagination { flex-direction: row; justify-content: space-between; margin-top: 12px; flex-shrink: 0; }
        .sc-pagination-controls { justify-content: flex-end; }
        .sc-pagination-info { text-align: left; }
        
        .filter-dropdown { flex: 0 1 200px; min-width: 180px; }
    }
    
    .sc-pagination { flex-shrink: 0; }
</style>

{{-- Page Header --}}
<header class="page-header flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 select-none">
    <div>
        <h1 class="font-['Public_Sans'] text-[28px] md:text-[32px] lg:text-[36px] font-bold text-white leading-none m-0">Password Reset Management</h1>
        <p class="text-sm md:text-base text-white/90 mt-2 font-medium">MSWDO Silang — Review and approve password reset requests</p>
    </div>
    <div class="flex items-center gap-5 sm:gap-4 lg:gap-5 w-full sm:w-auto justify-between sm:justify-end">
        <div class="font-['Public_Sans'] text-[13px] md:text-[14px] lg:text-[15px] font-medium text-white/90" id="currentDateTime">Loading date...</div>
        <div class="w-12 h-12 rounded-full bg-white/20 text-white font-bold text-base flex items-center justify-center cursor-pointer transition-all duration-200 hover:bg-white/30 select-none" title="Admin: {{ $adminName }}">
            {{ $initials }}
        </div>
    </div>
</header>

<div class="flex items-center justify-between flex-wrap gap-2 mb-3">
    <div class="flex items-center gap-3 flex-wrap" style="align-items: flex-end !important;">
        <div class="filter-item filter-search" style="display: flex; flex-direction: column; gap: 6px; flex: 0 0 auto;">
            <label class="filter-label">Search</label>
            <div class="filter-search-wrap">
                <input type="text" class="form-control" placeholder="Search name or email..." style="width: 220px; height: 44px; border-right: none; border-radius: 8px 0 0 8px;" id="searchInput" value="{{ request()->get('search', '') }}" oninput="updateClearButtonVisibility()" onkeydown="if(event.key==='Enter'){event.preventDefault();handleSearch();}">
                <button type="button" class="filter-search-btn" style="border: 1px solid #1E3A8A; background: #1E3A8A;" onclick="handleSearch()">
                    <i data-lucide="search" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
        </div>
        <div class="filter-item filter-dropdown" id="statusDropdown" style="display: flex; flex-direction: column; gap: 6px; flex: 0 0 auto;">
            <label class="filter-label">Filter by Status</label>
            <div onclick="toggleStatusMenu()" class="filter-select-btn" id="statusBtn">
                <span id="statusLabel" class="filter-select-label">{{ request()->get('status', 'All Status') }}</span>
                <i data-lucide="chevron-down" style="width:16px;height:16px;color:#9CA3AF;flex-shrink:0"></i>
            </div>
            <div id="statusMenu" class="filter-menu" style="display:none"></div>
        </div>
        <button type="button" class="filter-reset-btn" onclick="clearFilters()" style="height: 44px; padding: 0 16px; border: 1px solid #DC2626; border-radius: 8px; background: #fff; color: #DC2626; cursor: pointer; align-items: center; justify-content: center; transition: all .15s; font-size: 0.875rem; font-weight: 500; gap: 6px; flex-shrink: 0;" onmouseover="this.style.borderColor='#B91C1C';this.style.color='#B91C1C';this.style.background='#FEF2F2';" onmouseout="this.style.borderColor='#DC2626';this.style.color='#DC2626';this.style.background='#fff';">
            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
            Clear
        </button>
    </div>
</div>

<div class="table-scroll-container">
    <table class="gov-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Requested</th>
                <th>Expires</th>
                <th>Processed By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr class="data-row">
                    <td data-label="Name" style="font-weight: 500;">
                        @if($request->user)
                            {{ $request->user->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td data-label="Email" style="font-weight: 500;">{{ $request->email }}</td>
                    <td data-label="Status">
                        @if($request->status === 'pending')
                            <span class="status-badge status-pending">Pending</span>
                        @elseif($request->status === 'approved')
                            <span class="status-badge status-approved">Approved</span>
                        @elseif($request->status === 'completed')
                            <span class="status-badge status-completed">Completed</span>
                        @elseif($request->status === 'rejected')
                            <span class="status-badge status-rejected">Rejected</span>
                        @endif
                    </td>
                    <td data-label="Requested">{{ $request->requested_at->format('M d, Y - g:i A') }}</td>
                    <td data-label="Expires">
                        @if($request->expires_at)
                            {{ $request->expires_at->format('M d, Y - g:i A') }}
                        @else
                            -
                        @endif
                    </td>
                    <td data-label="Processed By">
                        @if($request->processedBy)
                            {{ $request->processedBy->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td data-label="Action">
                        @if($request->status === 'pending')
                            <div style="display: flex; gap: 0.5rem;">
                                <form method="POST" action="{{ route('admin.password-reset.approve', $request->id) }}" class="swal-form" data-swal-title="Approve Request" data-swal-text="Are you sure you want to approve the password reset request for {{ $request->user->name ?? $request->email }}?" data-swal-icon="question" data-swal-confirm="Yes, Approve" data-swal-color="#16A34A" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.password-reset.reject', $request->id) }}" class="swal-form" data-swal-title="Reject Request" data-swal-text="Are you sure you want to reject the password reset request for {{ $request->user->name ?? $request->email }}?" data-swal-icon="warning" data-swal-confirm="Yes, Reject" data-swal-color="#DC2626" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            </div>
                        @elseif($request->status === 'approved')
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <span style="font-size: 0.75rem; color: #64748B;">Email sent to user</span>
                                <form method="POST" action="{{ route('admin.password-reset.delete', $request->id) }}" class="swal-form" data-swal-title="Delete Record" data-swal-text="Are you sure you want to delete this password reset record for {{ $request->user->name ?? $request->email }}?" data-swal-icon="warning" data-swal-confirm="Yes, Delete" data-swal-color="#DC2626" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary">Delete</button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.password-reset.delete', $request->id) }}" class="swal-form" data-swal-title="Delete Record" data-swal-text="Are you sure you want to delete this password reset record for {{ $request->user->name ?? $request->email }}?" data-swal-icon="warning" data-swal-confirm="Yes, Delete" data-swal-color="#DC2626" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="7" class="empty-cell">
                        <div class="empty-state-content">
                            <div class="empty-icon-wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width: 32px; height: 32px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="empty-title">No password reset requests</p>
                            <p class="empty-subtitle">Requests will appear here when users submit them.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Laravel Pagination -->
<div class="sc-pagination">
    <div class="sc-pagination-info">
        @if($requests->count() > 0)
            Showing {{ $requests->firstItem() }}–{{ $requests->lastItem() }} of {{ $requests->total() }} Records
        @else
            Showing 0 of 0 Records
        @endif
    </div>
    <div class="sc-pagination-controls">
        {{ $requests->appends(request()->only(['search', 'status', 'per_page']))->links('vendor.pagination.custom-simple') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize status dropdown and set current filter from URL
    populateStatusDropdown();
    const urlStatus = "{{ request()->get('status', 'All Status') }}";
    if (urlStatus && urlStatus !== 'All Status') {
        window.statusFilterState = urlStatus;
        document.getElementById('statusLabel').textContent = urlStatus;
        var btn = document.getElementById('statusBtn');
        btn.classList.add('active');
        btn.setAttribute('data-filter', urlStatus);
        highlightStatusOpt();
    }
    updateClearButtonVisibility();

    if (typeof lucide !== 'undefined') lucide.createIcons();
    // ── SweetAlert confirmations for all forms ──
    document.querySelectorAll('.swal-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const f = this;
            Swal.fire({
                title: f.dataset.swalTitle || 'Are you sure?',
                text: f.dataset.swalText || 'This action cannot be undone.',
                icon: f.dataset.swalIcon || 'warning',
                showCancelButton: true,
                confirmButtonColor: f.dataset.swalColor || '#1A237E',
                cancelButtonColor: '#6B7280',
                confirmButtonText: f.dataset.swalConfirm || 'Yes',
                cancelButtonText: 'Cancel',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            }).then(function(result) {
                if (result.isConfirmed) {
                    f.submit();
                }
            });
        });
    });
});

// ── Flash message SweetAlert popups ──
@if(session('success'))
    Swal.fire({
        title: 'Success',
        text: '{{ session('success') }}',
        icon: 'success',
        confirmButtonColor: '#16A34A',
        confirmButtonText: 'OK',
        background: '#ffffff',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
@endif

@if(session('error'))
    Swal.fire({
        title: 'Error',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonColor: '#DC2626',
        confirmButtonText: 'OK',
        background: '#ffffff',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
@endif

// Search and filter functions
function handleSearch() {
    const searchValue = document.getElementById('searchInput').value.trim();
    const statusValue = window.statusFilterState === 'All Status' ? '' : window.statusFilterState;
    
    const url = new URL(window.location.href);
    if (searchValue) {
        url.searchParams.set('search', searchValue);
    } else {
        url.searchParams.delete('search');
    }
    if (statusValue) {
        url.searchParams.set('status', statusValue);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location.href = url.toString();
}

// Status filter state
window.statusFilterState = 'All Status';

function populateStatusDropdown() {
    const statusMenu = document.getElementById('statusMenu');
    if(statusMenu) {
        const statuses = ['All Status', 'pending', 'approved', 'completed', 'rejected'];
        statusMenu.innerHTML = '';
        statuses.forEach(status => {
            statusMenu.innerHTML += `<div class="status-opt" data-value="${status}" onclick="selectStatus(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${status}</div>`;
        });
        highlightStatusOpt();
    }
}

function toggleStatusMenu() {
    var menu = document.getElementById('statusMenu');
    var arrow = document.querySelector('#statusBtn [data-lucide="chevron-down"]');
    if(menu.style.display === 'none' || !menu.style.display) {
        menu.style.display = 'block';
        if(arrow) arrow.style.transform = 'rotate(180deg)';
        highlightStatusOpt();
    } else {
        menu.style.display = 'none';
        if(arrow) arrow.style.transform = '';
    }
    event.stopPropagation();
}

function selectStatus(el) {
    var val = el.getAttribute('data-value');
    window.statusFilterState = val;
    document.getElementById('statusLabel').textContent = el.textContent;
    document.getElementById('statusMenu').style.display = 'none';
    var arrow = document.querySelector('#statusBtn [data-lucide="chevron-down"]');
    if(arrow) arrow.style.transform = '';
    highlightStatusOpt();
    var btn = document.getElementById('statusBtn');
    if(val && val !== 'All Status') {
        btn.classList.add('active');
        btn.setAttribute('data-filter', val);
    } else {
        btn.classList.remove('active');
        btn.removeAttribute('data-filter');
    }
    handleSearch();
    updateClearButtonVisibility();
    event.stopPropagation();
}

function highlightStatusOpt() {
    var opts = document.querySelectorAll('.status-opt');
    opts.forEach(function(o) {
        if(o.getAttribute('data-value') === window.statusFilterState) o.classList.add('selected');
        else o.classList.remove('selected');
    });
}

// Close menu when clicking outside
document.addEventListener('click', function(e) {
    var statusDD = document.getElementById('statusDropdown');
    if(statusDD && !statusDD.contains(e.target)) {
        var menu = document.getElementById('statusMenu');
        var arrow = document.querySelector('#statusBtn [data-lucide="chevron-down"]');
        if(menu) menu.style.display = 'none';
        if(arrow) arrow.style.transform = '';
    }
}, true);

function clearFilters() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('status');
    window.location.href = url.toString();
}

function updateClearButtonVisibility() {
    var searchValue = document.getElementById('searchInput').value.trim();
    var statusValue = window.statusFilterState !== 'All Status';
    var clearBtn = document.querySelector('.filter-reset-btn');
    if (clearBtn) {
        if (searchValue || statusValue) {
            clearBtn.classList.add('visible');
        } else {
            clearBtn.classList.remove('visible');
        }
    }
}
</script>
@endpush
