<?php

namespace App\Models\Senior;

use Illuminate\Database\Eloquent\Model;

class BirthdayPayoutHistory extends Model
{
    protected $connection = 'mswdo_senior';

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

    /**
     * Relationship with BirthdayPayout
     */
    public function payout()
    {
        return $this->belongsTo(BirthdayPayout::class, 'payout_id');
    }

    /**
     * Relationship with Senior Citizen Record
     */
    public function senior()
    {
        return $this->belongsTo(SeniorCitizenRecord::class, 'senior_id');
    }

    /**
     * Relationship with User (performed by)
     */
    public function performedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }

    /**
     * Log an action to the history
     */
    public static function logAction($payoutId, $seniorId, $action, $details = null, $performedBy = null, $ipAddress = null)
    {
        return self::on('mswdo_senior')->create([
            'payout_id' => $payoutId,
            'senior_id' => $seniorId,
            'action' => $action,
            'details' => $details,
            'performed_by' => $performedBy,
            'ip_address' => $ipAddress,
        ]);
    }
}
