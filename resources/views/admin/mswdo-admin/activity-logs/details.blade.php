<dl class="activity-meta">
    <div><dt>User</dt><dd>{{ $activityLog->user_name }} (#{{ $activityLog->user_id }})</dd></div>
    <div><dt>Role at time of activity</dt><dd>{{ \App\Enums\UserRole::tryFrom($activityLog->role)?->label() ?? $activityLog->role }}</dd></div>
    <div><dt>Activity</dt><dd>{{ ucfirst($activityLog->activity_type) }}</dd></div>
    <div><dt>Module</dt><dd>{{ $activityLog->module }}</dd></div>
    <div><dt>Date and time (Philippine time)</dt><dd>{{ $activityLog->created_at->timezone('Asia/Manila')->format('M d, Y h:i:s A') }}</dd></div>
    <div><dt>Affected record</dt><dd>{{ $activityLog->subject_type ? class_basename($activityLog->subject_type).' #'.$activityLog->subject_id : '—' }}</dd></div>
</dl>
<h2 class="activity-changes-heading">Record changes</h2>
<p class="activity-privacy-note">Only permitted workflow fields are shown. Passwords, personal details and confidential content are excluded.</p>
<div class="activity-scroll" role="region" aria-label="Record changes" tabindex="0">
    <table class="activity-table"><thead><tr><th scope="col">Field</th><th scope="col">Old value</th><th scope="col">New value</th></tr></thead><tbody>
    @forelse(array_unique(array_merge(array_keys($activityLog->old_values ?? []), array_keys($activityLog->new_values ?? []))) as $field)
        <tr><th scope="row">{{ Str::headline($field) }}</th><td>{{ isset($activityLog->old_values[$field]) ? (is_bool($activityLog->old_values[$field]) ? ($activityLog->old_values[$field] ? 'Yes' : 'No') : $activityLog->old_values[$field]) : '—' }}</td><td>{{ isset($activityLog->new_values[$field]) ? (is_bool($activityLog->new_values[$field]) ? ($activityLog->new_values[$field] ? 'Yes' : 'No') : $activityLog->new_values[$field]) : '—' }}</td></tr>
    @empty
        <tr><td colspan="3">No displayable field changes were recorded for this activity.</td></tr>
    @endforelse
    </tbody></table>
</div>
