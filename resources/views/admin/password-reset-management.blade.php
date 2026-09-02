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

    /* ── Panel Header ── */
    .panel-header { margin-bottom: 1rem; }
    .panel-header h2 { font-size: 2rem; font-weight: 700; color: var(--text-primary); margin: 0; }
    .panel-header p { font-size: 0.875rem; color: var(--text-secondary); margin: 0.25rem 0 0; }

    /* ── Table wrap ── */
    .password-reset-table-wrap {
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 1.5rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    /* ── Table ── */
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

    /* ── Badges ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: .04em;
    }
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-approved { background: #D1FAE5; color: #065F46; }
    .status-completed { background: #DBEAFE; color: #1E40AF; }
    .status-rejected { background: #FEE2E2; color: #991B1B; }

    /* ── Buttons ── */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }
    .btn-success { background: #16A34A; color: white; }
    .btn-success:hover { background: #15803D; }
    .btn-danger { background: #DC2626; color: white; }
    .btn-danger:hover { background: #B91C1C; }
    .btn-secondary { background: #64748B; color: white; }
    .btn-secondary:hover { background: #475569; }

    /* ── Pagination ── */
    .sc-pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 14px; flex-shrink: 0; padding: 4px 0; flex-wrap: wrap; }
    .sc-pagination-info { font-size: 0.813rem; color: #6B7280; font-weight: 500; }
    .sc-pagination-controls { display: flex; gap: 4px; flex-wrap: wrap; }
    .sc-page-btn { height: 36px; min-width: 36px; padding: 0 10px; border: 1px solid #E5E7EB; border-radius: 6px; background: #fff; color: #374151; font-size: 0.813rem; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
    .sc-page-btn:hover:not(:disabled) { background: #F3F4F6; border-color: #D1D5DB; }
    .sc-page-btn.active { background: #1A237E; color: #fff; border-color: #1A237E; font-weight: 700; }
    .sc-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* ── Empty State ── */
    .empty-row { background: transparent !important; border: none !important; box-shadow: none !important; }
    .empty-cell { padding: 3rem 1rem !important; text-align: center !important; border: none !important; }
    .empty-cell::before { display: none !important; content: none !important; }
    .empty-state-content { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 12px; padding: 2rem 1rem; margin-top: 50px; }
    .empty-icon-wrap { width: 72px; height: 72px; border-radius: 50%; background: #EEF2FF; display: flex; align-items: center; justify-content: center; color: #1A237E; }
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
    }
</style>

<div class="panel-header">
    <h2>Password Reset Requests</h2>
    <p>Review and approve password reset requests from users</p>
</div>

<div class="password-reset-table-wrap">
    <table class="gov-table">
        <thead>
            <tr>
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
                                <form method="POST" action="{{ route('admin.password-reset.approve', $request->id) }}" class="swal-form" data-swal-title="Approve Request" data-swal-text="Are you sure you want to approve the password reset request for {{ $request->email }}?" data-swal-icon="question" data-swal-confirm="Yes, Approve" data-swal-color="#16A34A" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.password-reset.reject', $request->id) }}" class="swal-form" data-swal-title="Reject Request" data-swal-text="Are you sure you want to reject the password reset request for {{ $request->email }}?" data-swal-icon="warning" data-swal-confirm="Yes, Reject" data-swal-color="#DC2626" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            </div>
                        @elseif($request->status === 'approved')
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <span style="font-size: 0.75rem; color: #64748B;">Email sent to user</span>
                                <form method="POST" action="{{ route('admin.password-reset.delete', $request->id) }}" class="swal-form" data-swal-title="Delete Record" data-swal-text="Are you sure you want to delete this password reset record for {{ $request->email }}?" data-swal-icon="warning" data-swal-confirm="Yes, Delete" data-swal-color="#DC2626" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary">Delete</button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.password-reset.delete', $request->id) }}" class="swal-form" data-swal-title="Delete Record" data-swal-text="Are you sure you want to delete this password reset record for {{ $request->email }}?" data-swal-icon="warning" data-swal-confirm="Yes, Delete" data-swal-color="#DC2626" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="6" class="empty-cell">
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
        {{ $requests->appends(request()->query())->links('vendor.pagination.custom-simple') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endpush
