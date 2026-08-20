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
    .password-reset-table-wrap {
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-approved { background: #D1FAE5; color: #065F46; }
    .status-completed { background: #DBEAFE; color: #1E40AF; }
    .status-rejected { background: #FEE2E2; color: #991B1B; }

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
    .btn-primary { background: #2563EB; color: white; }
    .btn-primary:hover { background: #1D4ED8; }
    .btn-secondary { background: #64748B; color: white; }
    .btn-secondary:hover { background: #475569; }

    .reset-link-input {
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        padding: 0.35rem 0.5rem;
        font-size: 0.75rem;
        color: #475569;
        width: 200px;
    }

    /* ── Mobile: stacked card rows (no horizontal scroll) ── */
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
        .gov-table tbody td.empty-state-cell {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            text-align: center !important;
            padding: 2rem 1rem !important;
        }
        .gov-table tbody td.empty-state-cell::before { display: none !important; }
    }
</style>

<div class="password-reset-table-wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-primary); margin: 0;">Password Reset Requests</h2>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0.25rem 0 0;">Review and approve password reset requests from users</p>
        </div>
    </div>

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
                <tr>
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
                                <form method="POST" action="{{ route('admin.password-reset.approve', $request->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this password reset request?')">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.password-reset.reject', $request->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this password reset request?')">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @elseif($request->status === 'approved')
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <span style="font-size: 0.75rem; color: #64748B;">Email sent to user</span>
                                <form method="POST" action="{{ route('admin.password-reset.delete', $request->id) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Delete this record?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.password-reset.delete', $request->id) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Delete this record?')">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state-cell" style="text-align: center; padding: 3rem 1rem; color: #64748B;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 48px; height: 48px; color: #CBD5E1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p style="font-size: 1rem; font-weight: 500; margin: 0;">No password reset requests</p>
                            <p style="font-size: 0.875rem; margin: 0;">Requests will appear here when users submit them.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
// Show flash messages as SweetAlert popups
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
