<?php

namespace App\Models\Senior;

use Illuminate\Database\Eloquent\Model;

class SeniorActivityLog extends Model
{
    protected $table = 'senior_activity_logs';

    protected $fillable = [
        'action',
        'name',
        'identifier',
        'admin',
    ];

    /**
     * Log an activity to the database.
     */
    public static function log(string $action, string $name, string $identifier, ?string $admin = null): self
    {
        return self::create([
            'action' => $action,
            'name' => $name,
            'identifier' => $identifier,
            'admin' => $admin ?? session('admin_user_name') ?? 'Admin',
        ]);
    }

    /**
     * Get the most recent activities.
     */
    public static function recent(int $limit = 10)
    {
        return self::orderByDesc('created_at')->limit($limit)->get();
    }
}
