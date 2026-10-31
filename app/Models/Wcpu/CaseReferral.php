<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseReferral extends WcpuBaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'case_id',
        'referral_number',
        'date_of_referral',
        'referred_by_name',
        'referred_by_designation',
        'referred_by_address',
        'referred_by_contact',
        'referred_by_email',
        'recipient_name',
        'recipient_address',
        'recipient_contact',
        'recipient_email',
        'client_name',
        'client_age',
        'client_sex',
        'client_address',
        'client_contact',
        'family_guardian_name',
        'family_guardian_contact',
        'family_guardian_address',
        'reason_for_referral',
        'specific_services_requested',
        'additional_notes',
        'status',
        'feedback',
        'feedback_received_at',
        'referred_by_user_id',
        'created_by_department_admin',
    ];

    protected $casts = [
        'date_of_referral' => 'date',
        'feedback_received_at' => 'datetime',
        'created_by_department_admin' => 'boolean',
    ];

    // Relationships
    public function scopeForRecipientDepartments($query, array $departments)
    {
        $roles = [];
        foreach ($departments as $department) {
            $prefix = $department === 'BCPC' ? 'BCPC' : 'SWD';
            $roles[] = $prefix.' - Focal';
            $roles[] = $prefix.' - Staff';
        }
        return $query->where(function ($referrals) use ($departments, $roles) {
            $referrals->whereHas('recipient', fn ($recipients) => $recipients->whereIn('role', $roles))
                ->orWhere(function ($otherReferrals) use ($departments) {
                    $otherReferrals->whereDoesntHave('recipient', fn ($recipients) => $recipients
                        ->whereIn('role', ['BCPC - Focal', 'BCPC - Staff', 'SWD - Focal', 'SWD - Staff']))
                        ->whereHas('case', fn ($cases) => $cases->whereIn('type', $departments));
                });
        });
    }

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(ReportRecipient::class, 'recipient_email', 'email')->withTrashed();
    }

    public function departmentAdminReferralsForCase(): HasMany
    {
        return $this->hasMany(static::class, 'case_id', 'case_id')
            ->withTrashed()
            ->where('created_by_department_admin', true);
    }

    public function caseHasDepartmentAdminReferral(): bool
    {
        if ($this->case_id === null) {
            return false;
        }

        if (array_key_exists('department_admin_referrals_for_case_exists', $this->attributes)) {
            return (bool) $this->attributes['department_admin_referrals_for_case_exists'];
        }

        return $this->departmentAdminReferralsForCase()->exists();
    }

    // Generate unique referral number
    public static function generateReferralNumber()
    {
        $date = now()->format('Ymd');
        $prefix = "REF-{$date}-";
        
        $lastReferral = self::withTrashed()->where('referral_number', 'LIKE', "{$prefix}%")
            ->orderBy('referral_number', 'desc')
            ->first();
        
        if ($lastReferral) {
            $lastNumber = intval(substr($lastReferral->referral_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return $prefix . $newNumber;
    }
}
