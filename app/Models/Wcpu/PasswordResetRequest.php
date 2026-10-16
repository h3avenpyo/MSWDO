<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetRequest extends WcpuBaseModel
{
    protected $fillable = [
        'user_id',
        'username',
        'status',
        'requested_at',
        'admin_id',
        'resolved_at',
        'admin_notes',
        'new_password',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('requested_at', 'desc');
    }
}
