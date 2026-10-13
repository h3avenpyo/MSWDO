<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class ReportRecipient extends WcpuBaseModel
{
    use SoftDeletes;

    // only include columns that are guaranteed to exist in the table
    protected $fillable = ['email','name','contact_number','role','active'];

    // safe default columns to select when callers don't specify columns
    protected static $safeSelect = ['id', 'name', 'email', 'contact_number', 'role'];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope a query to only active recipients.
     * If $columns provided, those are used; otherwise a safe default list is selected.
     */
    public function scopeActive($q, $columns = null)
    {
        $q = $q->where('active', true);
        // if caller already specified select() later, we won't override it; this applies only when no explicit columns requested
        if ($columns) {
            return $q->select($columns);
        }

        // defensive: if schema changed, compute available safe columns on wcpu connection
        $available = [];
        $schema = Schema::connection('wcpu');
        foreach (self::$safeSelect as $col) {
            if ($schema->hasColumn($this->getTable(), $col)) {
                $available[] = $col;
            }
        }
        // fallback to id,name if somehow others missing
        if (empty($available)) {
            $available = ['id', 'name'];
        }
        return $q->select($available);
    }
}
