<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InBetweenBenefitConfiguration extends Model
{
    protected $table = 'in_between_benefit_configurations';

    protected $fillable = [
        'benefit_name',
        'description',
        'benefit_amount',
        'milestone_ages',
        'eligible_intervals',
        'is_active',
        'effective_date',
        'expiry_date',
        'ordinance_reference',
        'eligibility_rules',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'benefit_amount' => 'decimal:2',
        'milestone_ages' => 'array',
        'eligible_intervals' => 'array',
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getMilestoneAgesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value ?? '[]', true);
    }

    public function getEligibleIntervalsAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value ?? '[]', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('effective_date')
                    ->orWhere('effective_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            });
    }

    public function getEligibilityIntervalForAge(int $age): ?string
    {
        $intervals = $this->eligible_intervals;
        
        foreach ($intervals as $interval) {
            $parts = explode('-', $interval);
            if (count($parts) === 2) {
                $min = (int) $parts[0];
                $max = (int) $parts[1];
                if ($age >= $min && $age <= $max) {
                    return $interval;
                }
            }
        }
        
        return null;
    }

    public function isMilestoneAge(int $age): bool
    {
        return in_array($age, $this->milestone_ages);
    }
}
