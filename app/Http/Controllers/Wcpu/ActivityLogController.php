<?php

namespace App\Http\Controllers\Wcpu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the login activities.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Log the request
            Log::info('Fetching activity logs', [
                'user_id' => Auth::id(),
                'ip' => request()->ip()
            ]);

            // Debug: Log database connection info
            Log::debug('Database connection', [
                'default' => config('database.default'),
                'connections' => array_keys(config('database.connections'))
            ]);

            // Get activities with pagination and eager loading
            $query = Activity::with(['causer']);
            
            // Debug: Log the raw SQL query
            $sql = $query->toSql();
            Log::debug('Activity log query', ['sql' => $sql]);

            $activities = $query->orderBy('created_at', 'desc')
                              ->paginate(15);

            // Debug: Log the first activity if exists
            if ($activities->count() > 0) {
                Log::debug('First activity log entry', [
                    'id' => $activities[0]->id,
                    'description' => $activities[0]->description,
                    'causer' => $activities[0]->causer,
                    'properties' => $activities[0]->properties
                ]);
            }

            // Format the activities for the view
            $formattedLogs = $activities->map(function($activity) {
                try {
                    // Safely get causer information
                    $causer = $activity->causer;
                    $userName = $causer ? $causer->name : 'System';
                    $userRole = $causer ? ($causer->role ?? 'user') : 'system';

                    // Get properties safely
                    $properties = $activity->properties ?? [];
                    if (is_string($properties)) {
                        $properties = json_decode($properties, true) ?? [];
                    }

                    // Format the date and time
                    $date = $activity->created_at ? $activity->created_at->format('M d, Y') : 'N/A';
                    $time = $activity->created_at ? $activity->created_at->format('h:i A') : 'N/A';

                    // Determine the action type and icon
                    $action = $activity->description ?? 'Performed an action';
                    $icon = 'fa-info-circle';
                    
                    $actionLower = strtolower($action);
                    if (str_contains($actionLower, 'login')) {
                        $icon = 'fa-sign-in-alt';
                    } elseif (str_contains($actionLower, 'create') || str_contains($actionLower, 'add')) {
                        $icon = 'fa-plus-circle';
                    } elseif (str_contains($actionLower, 'update') || str_contains($actionLower, 'edit')) {
                        $icon = 'fa-edit';
                    } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'remove')) {
                        $icon = 'fa-trash';
                    } elseif (str_contains($actionLower, 'export') || str_contains($actionLower, 'download')) {
                        $icon = 'fa-file-export';
                    }

                    return [
                        'id' => $activity->id,
                        'date' => $date,
                        'time' => $time,
                        'action' => $action,
                        'user' => $userName,
                        'role' => $userRole,
                        'icon' => $icon,
                        'causer' => $causer,
                        'properties' => $properties,
                        'log_name' => $activity->log_name ?? 'default',
                        'created_at' => $activity->created_at,
                        'updated_at' => $activity->updated_at
                    ];
                } catch (\Exception $e) {
                    Log::error('Error formatting activity log', [
                        'activity_id' => $activity->id ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return [
                        'id' => $activity->id ?? 'error',
                        'date' => 'Error',
                        'time' => 'Error',
                        'action' => 'Error processing this log',
                        'user' => 'System',
                        'role' => 'system',
                        'icon' => 'fa-exclamation-triangle',
                        'error' => $e->getMessage()
                    ];
                }
            });

            return view('admin.admin_user_logs', [
                'activities' => $formattedLogs,
                'pagination' => $activities
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ActivityLogController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return an empty collection in case of error but with debug info
            return view('admin.admin_user_logs', [
                'activities' => collect([
                    [
                        'id' => 'error',
                        'date' => now()->format('M d, Y'),
                        'time' => now()->format('h:i A'),
                        'action' => 'Error loading activity logs',
                        'user' => 'System',
                        'role' => 'system',
                        'icon' => 'fa-exclamation-circle',
                        'error' => $e->getMessage()
                    ]
                ]),
                'pagination' => null,
                'error' => $e->getMessage()
            ]);
        }
    }
}
