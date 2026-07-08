<?php

namespace App\Models\Senior;

use Illuminate\Database\Eloquent\Model;
use App\Models\Senior\BirthdayPayoutHistory;

class BirthdayPayout extends Model
{
    protected $connection = 'mswdo_senior';

    protected $table = 'birthday_payouts';

    protected $fillable = [
        'senior_id',
        'birth_month',
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
    ];

    /**
     * Relationship with Senior Citizen Record
     */
    public function senior()
    {
        return $this->belongsTo(SeniorCitizenRecord::class, 'senior_id');
    }

    /**
     * Relationship with User (released by)
     */
    public function releasedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'released_by');
    }

    /**
     * Relationship with Payout History
     */
    public function history()
    {
        return $this->hasMany(BirthdayPayoutHistory::class, 'payout_id')->orderBy('created_at', 'desc');
    }

    /**
     * Scope for pending payouts
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for released payouts
     */
    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    /**
     * Scope for cancelled payouts
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Check if payout can be released
     */
    public function canBeReleased()
    {
        return $this->status === 'pending';
    }

    /**
     * Mark payout as released
     */
    public function markAsReleased($releasedBy, $remarks = null)
    {
        $this->update([
            'status' => 'released',
            'released_by' => $releasedBy,
            'released_date' => now(),
            'remarks' => $remarks,
        ]);
    }

    /**
     * Cancel payout
     */
    public function cancel($remarks = null)
    {
        $this->update([
            'status' => 'cancelled',
            'remarks' => $remarks,
        ]);
    }
}
