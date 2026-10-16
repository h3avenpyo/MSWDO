<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends WcpuBaseModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'target_audience',
        'location',
        'end_time',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForUser($query, $user)
    {
        $isAdmin = strtolower($user->position) === 'administrator';
        $isDeskOfficer = strtolower($user->position) === 'desk officer';

        if ($isDeskOfficer) {
            $audiences = ['all'];
            if ($user->canAccessCaseDepartment('BCPC')) $audiences[] = 'bcpc_only';
            if ($user->canAccessCaseDepartment('VAWC')) $audiences[] = 'vawc_only';
            return $query->where(fn ($q) => $q->whereIn('target_audience', $audiences)->orWhere('created_by', $user->id));
        }
        
        return $query->where(function ($q) use ($user, $isAdmin) {
            $q->where('target_audience', 'all')
              ->orWhere('created_by', $user->id);

            if ($isAdmin) {
                $q->orWhere('target_audience', 'admins_only');
            }
        });
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('event_date', $date);
    }
}
