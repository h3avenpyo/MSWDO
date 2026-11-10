@extends('admin.mswdo-admin.layout')
@section('title', 'User Logs - MSWDO')
@section('page_title', 'User Logs')
@section('content')
@include('admin.mswdo-admin.activity-logs.styles')
<div class="user-logs-page">
    <header class="activity-header">
        <h1>User Logs</h1>
        <p>System activity monitoring</p>
    </header>
    <form class="activity-card activity-toolbar" method="GET" action="{{ route('admin.activity-logs.index') }}">
        <div class="activity-toolbar-top">
            <div class="activity-toolbar-heading">
                <span class="activity-history-icon"><i data-lucide="history" aria-hidden="true"></i></span>
                <div><h2>System Activity Logs</h2><p>Track all MSWDO user activities</p></div>
            </div>
            <div class="activity-search">
                <label class="activity-sr-only" for="activity-search">Search logs</label>
                <i data-lucide="search" aria-hidden="true"></i>
                <input id="activity-search" name="search" value="{{ request('search') }}" placeholder="Search logs..." maxlength="200">
                <button type="submit" aria-label="Search logs"><i data-lucide="arrow-right" aria-hidden="true"></i></button>
            </div>
        </div>
        @if($errors->any())<div class="activity-errors" role="alert">{{ $errors->first() }}</div>@endif
        <details class="activity-filter-panel" @if(request()->anyFilled(['user', 'role', 'activity_type', 'from', 'to']) || $errors->any()) open @endif>
            <summary><i data-lucide="sliders-horizontal" aria-hidden="true"></i> Search filters</summary>
            <div class="activity-filters">
                <div><label for="activity-user">User</label><select id="activity-user" name="user"><option value="">All users</option>@foreach($users->unique('user_id') as $user)<option value="{{ $user->user_id }}" @selected((string)request('user') === (string)$user->user_id)>{{ $user->user_name }} (#{{ $user->user_id }})</option>@endforeach</select></div>
                <div><label for="activity-role">Role</label><select id="activity-role" name="role"><option value="">All roles</option>@foreach($roles as $role)<option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>@endforeach</select></div>
                <div><label for="activity-type">Activity type</label><select id="activity-type" name="activity_type"><option value="">All activities</option>@foreach($types as $type)<option value="{{ $type }}" @selected(request('activity_type') === $type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
                <div><label for="activity-from">From date</label><input id="activity-from" type="date" name="from" value="{{ request('from') }}"></div>
                <div><label for="activity-to">To date</label><input id="activity-to" type="date" name="to" value="{{ request('to') }}"></div>
            </div>
            <div class="activity-actions"><button class="activity-button" type="submit">Apply filters</button><a class="activity-link" href="{{ route('admin.activity-logs.index') }}">Reset filters</a></div>
        </details>
    </form>
    <section class="activity-results" aria-label="User logs">
        <div class="activity-desktop activity-scroll" tabindex="0" role="region" aria-label="Activity table">
            <table class="activity-table activity-log-table">
                <colgroup><col style="width:12%"><col style="width:10%"><col style="width:28%"><col style="width:30%"><col style="width:20%"></colgroup>
                <thead><tr><th scope="col">Date</th><th scope="col">Time</th><th scope="col">Action</th><th scope="col">User</th><th scope="col" class="activity-details-cell">Details</th></tr></thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->timezone('Asia/Manila')->format('M d, Y') }}</td>
                        <td>{{ $log->created_at->timezone('Asia/Manila')->format('h:i:s A') }}</td>
                        <td>@include('admin.mswdo-admin.activity-logs.action', ['log' => $log])</td>
                        <td>@include('admin.mswdo-admin.activity-logs.user', ['log' => $log])</td>
                        <td class="activity-details-cell"><a class="activity-view" href="{{ route('admin.activity-logs.show', $log) }}" data-activity-dialog="activity-dialog-{{ $log->id }}" aria-label="View activity {{ $log->id }}" aria-haspopup="dialog" aria-controls="activity-dialog-{{ $log->id }}"><i data-lucide="eye" aria-hidden="true"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">@include('admin.mswdo-admin.activity-logs.empty')</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="activity-mobile">
            @forelse($logs as $log)
                <a class="activity-mobile-card" href="{{ route('admin.activity-logs.show', $log) }}" data-activity-dialog="activity-dialog-{{ $log->id }}" aria-haspopup="dialog" aria-controls="activity-dialog-{{ $log->id }}">
                    <div class="activity-mobile-top">@include('admin.mswdo-admin.activity-logs.user', ['log' => $log])<i data-lucide="eye" class="activity-mobile-eye" aria-hidden="true"></i></div>
                    <p class="activity-mobile-date">{{ $log->created_at->timezone('Asia/Manila')->format('M d, Y') }} &middot; {{ $log->created_at->timezone('Asia/Manila')->format('h:i:s A') }}</p>
                    @include('admin.mswdo-admin.activity-logs.action', ['log' => $log])
                </a>
            @empty
                <div class="activity-mobile-card">@include('admin.mswdo-admin.activity-logs.empty')</div>
            @endforelse
        </div>
        <footer class="activity-results-footer">
            <div>Showing {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} of {{ number_format($logs->total()) }} logs<span class="activity-timezone">Philippine time (UTC+08:00)</span></div>
            <nav class="activity-pagination" aria-label="Activity log pagination">
                <span class="activity-sr-only">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
                @if($logs->onFirstPage())<span aria-disabled="true" aria-label="Previous page"><i data-lucide="chevron-left" aria-hidden="true"></i></span>@else<a href="{{ $logs->previousPageUrl() }}" rel="prev" aria-label="Previous page"><i data-lucide="chevron-left" aria-hidden="true"></i></a>@endif
                @php($firstPage = max(1, min($logs->currentPage() - 2, $logs->lastPage() - 4)))
                @for($page = $firstPage; $page <= min($logs->lastPage(), $firstPage + 4); $page++)
                    @if($page === $logs->currentPage())<span aria-current="page">{{ $page }}</span>@else<a href="{{ $logs->url($page) }}" aria-label="Page {{ $page }}">{{ $page }}</a>@endif
                @endfor
                @if($logs->hasMorePages())<a href="{{ $logs->nextPageUrl() }}" rel="next" aria-label="Next page"><i data-lucide="chevron-right" aria-hidden="true"></i></a>@else<span aria-disabled="true" aria-label="Next page"><i data-lucide="chevron-right" aria-hidden="true"></i></span>@endif
            </nav>
        </footer>
    </section>
</div>
@foreach($logs as $log)
<dialog class="activity-dialog" id="activity-dialog-{{ $log->id }}" aria-labelledby="activity-dialog-title-{{ $log->id }}">
    <div class="activity-dialog-header"><h2 id="activity-dialog-title-{{ $log->id }}"><i data-lucide="info" aria-hidden="true"></i> Activity Details</h2><form method="dialog"><button aria-label="Close activity details"><i data-lucide="x" aria-hidden="true"></i></button></form></div>
    <div class="activity-dialog-body"><p class="activity-dialog-description">{{ $log->description }}</p>@include('admin.mswdo-admin.activity-logs.details', ['activityLog' => $log])</div>
</dialog>
@endforeach
@endsection
@push('scripts')
<script>
    document.addEventListener('click', function (event) {
        const link = event.target.closest('[data-activity-dialog]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const dialog = document.getElementById(link.dataset.activityDialog);
        if (dialog && typeof dialog.showModal === 'function') {
            event.preventDefault();
            dialog.showModal();
        }
    });
    document.querySelectorAll('.activity-dialog').forEach(function (dialog) {
        dialog.addEventListener('click', function (event) {
            if (event.target !== dialog) return;
            const bounds = dialog.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) dialog.close();
        });
    });
</script>
@endpush
