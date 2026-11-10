<div class="activity-user">
    <div><span class="activity-user-name">{{ $log->user_id }} - {{ $log->user_name }}</span><span class="activity-user-role">{{ \App\Enums\UserRole::tryFrom($log->role)?->label() ?? $log->role }}</span></div>
</div>
