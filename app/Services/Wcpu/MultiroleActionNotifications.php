<?php

namespace App\Services\Wcpu;

use App\Models\Wcpu\{Barangay, BcpcCaseMonitoring, CaseModel, CaseReferral, QuarterlyReport, User};
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class MultiroleActionNotifications
{
    public static function record(string $action, Model $record): void
    {
        $actor = auth()->user() ?? auth('wcpu')->user();
        if (!$actor || $actor->position !== 'Desk Officer' || !$actor->isMultiCaseDepartment()
            || !in_array(get_class($record), [CaseModel::class, CaseReferral::class, QuarterlyReport::class, BcpcCaseMonitoring::class], true)
            || str_starts_with($action, 'Archived')) return;
        if (str_starts_with($action, 'Updated') && !array_diff(array_keys($record->getChanges()), ['updated_at', 'pdf_path', 'archived_at', 'deleted_at'])) return;

        $case = $record instanceof CaseReferral ? $record->case : ($record instanceof CaseModel ? $record : null);
        $department = $case?->type ?? ($record instanceof BcpcCaseMonitoring ? 'BCPC' : $record->type);
        $barangayId = $case?->case_designation ?? $record->barangay_id;
        $barangay = $barangayId ? Barangay::where('id', $barangayId)->orWhere('name', $barangayId)->first() : null;
        $recipients = User::where('is_active', true)->get()->filter(function ($user) use ($department, $barangay) {
            if (!$user->canAccessCaseDepartment($department)) return false;
            return strtolower($user->position) === 'administrator'
                || ($user->position === 'Desk Officer' && $barangay && in_array((string) $user->barangay, [(string) $barangay->id, (string) $barangay->name], true));
        })->pluck('id')->map(fn ($id) => (int) $id)->all();
        $kind = match (true) {
            $record instanceof CaseModel => 'case',
            $record instanceof CaseReferral => 'referral',
            $record instanceof QuarterlyReport => 'quarterly report',
            default => 'monitoring report',
        };
        $verb = str_starts_with($action, 'Added') ? 'filed' : 'edited';
        if ($record instanceof CaseReferral && $verb === 'filed') $verb = 'referred';
        if ($record instanceof CaseModel && $record->wasChanged('status') && $record->status === 'Case Referred') $verb = 'referred';
        if ($record instanceof BcpcCaseMonitoring && $record->wasChanged('submitted_at') && $record->submitted_at) $verb = 'submitted';
        $label = $case?->case_number ?? ('Q'.$record->quarter.' '.$record->year);
        activity('multirole_workflow')->performedOn($record)->causedBy($actor)
            ->withProperties(['recipient_ids' => $recipients, 'department' => $department, 'barangay_id' => $barangayId, 'kind' => $kind])
            ->log(($department === 'VAWC' ? 'SWD' : $department).' '.$kind.' '.$label.' was '.$verb.'.');
    }

    public static function forUser(User $user): array
    {
        $readIds = $user->read_notifications ?? [];
        return Activity::with('causer')->where('log_name', 'multirole_workflow')->whereJsonContains('properties->recipient_ids', (int) $user->id)
            ->latest('id')->limit(20)->get()->map(function ($entry) use ($user, $readIds) {
                $officer = $user->position === 'Desk Officer';
                $kind = $entry->properties['kind'] ?? 'case';
                $url = match ($kind) {
                    'case' => $officer ? (route_has('desk-officer.cases.show') ? route('desk-officer.cases.show', encrypt($entry->subject_id)) : '#') : (route_has('cases.show') ? route('cases.show', $entry->subject_id) : '#'),
                    'referral' => route_has($officer ? 'desk-officer.referrals.show' : 'admin.referrals.show') ? route($officer ? 'desk-officer.referrals.show' : 'admin.referrals.show', $entry->subject_id) : '#',
                    'quarterly report' => route_has($officer ? 'desk-officer.quarterly-reports.show' : 'admin.quarterly-reports.show') ? route($officer ? 'desk-officer.quarterly-reports.show' : 'admin.quarterly-reports.show', $entry->subject_id) : '#',
                    default => route_has($officer ? 'desk-officer.bcpc-case-monitoring.edit' : 'admin.bcpc-case-monitoring.edit') ? route($officer ? 'desk-officer.bcpc-case-monitoring.edit' : 'admin.bcpc-case-monitoring.edit', ['view' => $entry->subject_id]) : '#',
                };
                $id = 'multirole_action_'.$entry->id;
                $deskOfficer = trim(($entry->causer?->first_name ?? '') . ' ' . ($entry->causer?->last_name ?? ''))
                    ?: ($entry->causer?->username ?? 'Desk Officer');
                return ['id' => $id, 'type' => 'multirole_action', 'icon' => 'bell', 'text' => e($entry->description),
                    'time' => $entry->created_at->diffForHumans(), 'timestamp' => $entry->created_at->getTimestamp(),
                    'unread' => !in_array($id, $readIds, true) && (!$user->notifications_last_viewed_at || $entry->created_at->gte($user->notifications_last_viewed_at)),
                    'url' => $url,
                    'action_details' => [
                        'action' => $entry->description ?: 'Portal action recorded',
                        'desk_officer' => $deskOfficer,
                        'performed_at' => $entry->created_at->format('M d, Y h:i A'),
                        'url' => $url,
                    ],
                ];
            })->all();
    }
}

if (!function_exists('route_has')) {
    function route_has(string $name): bool {
        return \Illuminate\Support\Facades\Route::has($name);
    }
}
