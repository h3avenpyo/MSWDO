@php
    $actionIcon = match ($log->activity_type) {
        'login' => 'log-in', 'logout' => 'log-out', 'created' => 'circle-plus',
        'updated' => 'pencil', 'deleted' => 'trash-2', 'archived' => 'archive',
        'restored' => 'archive-restore', 'print', 'reprint', 'pdf' => 'printer',
        'export', 'download' => 'download', 'send' => 'send', default => 'activity',
    };
@endphp
<div class="activity-action"><i data-lucide="{{ $actionIcon }}" aria-hidden="true"></i><div><span class="activity-action-description">{{ $log->description }}</span><span class="activity-action-module">{{ ucfirst($log->activity_type) }} &middot; {{ $log->module }}</span></div></div>
