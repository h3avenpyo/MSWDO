<?php

namespace App\Models\Senior;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BirthdayPayout extends Model
{
    protected $table = 'birthday_payouts';

    protected $fillable = [
        'senior_id',
        'payout_year',
        'amount',
        'status',
        'released_by',
        'released_date',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'released_date' => 'datetime',
        'status' => PayoutStatus::class,
    ];

    public function senior(): BelongsTo
    {
        return $this->belongsTo(SeniorCitizenRecord::class, 'senior_id');
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'released_by');
    }

    public function history(): HasMany
    {
        return $this->hasMany(BirthdayPayoutHistory::class, 'payout_id')->orderBy('created_at', 'desc');
    }

    public function scopePending($query)
    {
        return $query->where('status', PayoutStatus::Pending);
    }

    public function scopeReleased($query)
    {
        return $query->where('status', PayoutStatus::Released);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', PayoutStatus::Cancelled);
    }

    public function canBeReleased(): bool
    {
        return $this->status === PayoutStatus::Pending;
    }

    public function markAsReleased($releasedBy, $remarks = null): void
    {
        $this->update([
            'status' => PayoutStatus::Released,
            'released_by' => $releasedBy,
            'released_date' => now(),
            'remarks' => $remarks,
        ]);
    }

    public function cancel($remarks = null): void
    {
        $this->update([
            'status' => PayoutStatus::Cancelled,
            'remarks' => $remarks,
        ]);
    }
}
