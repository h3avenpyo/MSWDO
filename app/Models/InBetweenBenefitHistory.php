<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InBetweenBenefitHistory extends Model
{
    protected $table = 'in_between_benefit_history';

    protected $fillable = [
        'senior_id',
        'full_name',
        'birth_date',
        'current_age',
        'benefit_type',
        'eligibility_interval',
        'birthday_year',
        'amount',
        'application_date',
        'payout_date',
        'status',
        'reference_number',
        'processed_by',
        'approved_by',
        'remarks',
        'audit_trail',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'application_date' => 'date',
        'payout_date' => 'date',
        'amount' => 'decimal:2',
        'audit_trail' => 'array',
    ];

    public function senior(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Senior\SeniorCitizenRecord::class, 'senior_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeClaimed($query)
    {
        return $query->whereIn('status', ['approved', 'released']);
    }

    public function scopeReleased($query)
    {
        return $query->where('status', 'released');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByInterval($query, string $interval)
    {
        return $query->where('eligibility_interval', $interval);
    }

    public function scopeBySenior($query, int $seniorId)
    {
        return $query->where('senior_id', $seniorId);
    }

    public function addAuditTrail(string $action, string $description, ?int $userId = null): void
    {
        $auditTrail = $this->audit_trail ?? [];
        $auditTrail[] = [
            'action' => $action,
            'description' => $description,
            'user_id' => $userId,
            'timestamp' => now()->toIso8601String(),
        ];
        $this->audit_trail = $auditTrail;
        $this->save();
    }

    protected static function booted()
    {
        static::creating(function ($benefit) {
            if (empty($benefit->reference_number)) {
                $benefit->reference_number = static::generateNextReferenceNumber();
            }
        });
    }

    public static function generateNextReferenceNumber(): string
    {
        $year = now()->format('Y');
        $maxId = (int) static::max('id');
        $nextNum = $maxId + 1;
        $ref = 'IBG-' . $year . '-' . str_pad($nextNum, 8, '0', STR_PAD_LEFT);
        while (static::where('reference_number', $ref)->exists()) {
            $nextNum++;
            $ref = 'IBG-' . $year . '-' . str_pad($nextNum, 8, '0', STR_PAD_LEFT);
        }
        return $ref;
    }

    public function generateReferenceNumber(): string
    {
        return $this->reference_number ?: ('IBG-' . now()->format('Y') . '-' . str_pad($this->id ?: 1, 8, '0', STR_PAD_LEFT));
    }

    public function isClaimed(): bool
    {
        return in_array($this->status, ['approved', 'released']);
    }

    public function canBeClaimed(): bool
    {
        return $this->status === 'pending';
    }
}
