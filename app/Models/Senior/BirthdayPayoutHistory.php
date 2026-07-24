<?php

namespace App\Models\Senior;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayPayoutHistory extends Model
{
    protected $table = 'birthday_payout_history';

    protected $fillable = [
        'payout_id',
        'senior_id',
        'action',
        'details',
        'performed_by',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function payout(): BelongsTo
    {
        return $this->belongsTo(BirthdayPayout::class, 'payout_id');
    }

    public function senior(): BelongsTo
    {
        return $this->belongsTo(SeniorCitizenRecord::class, 'senior_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }

    public static function logAction($payoutId, $seniorId, $action, $details = null, $performedBy = null, $ipAddress = null): static
    {
        return self::create([
            'payout_id' => $payoutId,
            'senior_id' => $seniorId,
            'action' => $action,
            'details' => $details,
            'performed_by' => $performedBy,
            'ip_address' => $ipAddress,
        ]);
    }
}
