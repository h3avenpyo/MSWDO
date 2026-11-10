<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\MswdoActivityLog;
use App\Services\MswdoActivityLogger;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivityLogController extends Controller
{
    private function authorizeAdministrator(): void
    {
        $user = app(MswdoActivityLogger::class)->actor();
        abort_unless($user && $user->role === UserRole::Admin && $user->getRawOriginal('status') === 'active', 403);
    }

    public function index(Request $request)
    {
        $this->authorizeAdministrator();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'user' => ['nullable', 'integer', 'min:1'],
            'role' => ['nullable', 'string', 'max:100', Rule::in(array_column(UserRole::cases(), 'value'))],
            'activity_type' => ['nullable', 'string', 'max:50'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
        $query = MswdoActivityLog::query();
        if ($search = $filters['search'] ?? null) {
            $query->where(function ($query) use ($search) {
                foreach (['user_name', 'role', 'description', 'module', 'activity_type', 'subject_id'] as $column) {
                    $query->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }
        foreach (['user' => 'user_id', 'role' => 'role', 'activity_type' => 'activity_type'] as $filter => $column) {
            if (! empty($filters[$filter])) {
                $query->where($column, $filters[$filter]);
            }
        }
        // Store application timestamps in UTC; interpret date filters in local office time.
        if (! empty($filters['from'])) {
            $query->where('created_at', '>=', CarbonImmutable::parse($filters['from'], 'Asia/Manila')->startOfDay()->utc());
        }
        if (! empty($filters['to'])) {
            $query->where('created_at', '<', CarbonImmutable::parse($filters['to'], 'Asia/Manila')->addDay()->startOfDay()->utc());
        }

        $logs = $query->orderByDesc('id')->paginate(5)->withQueryString();
        $users = MswdoActivityLog::select('user_id', 'user_name')->distinct()->orderBy('user_name')->get();
        $types = MswdoActivityLog::select('activity_type')->distinct()->orderBy('activity_type')->pluck('activity_type');
        $roles = array_filter(UserRole::cases(), fn (UserRole $role) => ! in_array($role, [UserRole::Encoder, UserRole::Staff], true));

        return view('admin.mswdo-admin.activity-logs.index', compact('logs', 'users', 'types', 'roles'));
    }

    public function show(MswdoActivityLog $activityLog)
    {
        $this->authorizeAdministrator();

        return view('admin.mswdo-admin.activity-logs.show', compact('activityLog'));
    }
}
