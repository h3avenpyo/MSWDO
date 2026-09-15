<?php

namespace App\Models\Financial;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineFinancialIntake extends Model
{
    protected $fillable = [
        'status',
        'date_submitted',
        'control_number',
        'client_type',
        'is_client_beneficiary',
        'beneficiary_last_name',
        'beneficiary_first_name',
        'beneficiary_middle_name',
        'beneficiary_extension_name',
        'beneficiary_street_address',
        'beneficiary_barangay',
        'beneficiary_city',
        'beneficiary_province',
        'beneficiary_region',
        'beneficiary_contact_number',
        'beneficiary_birthday',
        'beneficiary_age',
        'beneficiary_sex',
        'beneficiary_civil_status',
        'beneficiary_occupation',
        'beneficiary_monthly_salary',
        'beneficiary_category',
        'beneficiary_category_other',
        'beneficiary_categories',
        'beneficiary_relationship',
        'has_representative',
        'rep_last_name',
        'rep_first_name',
        'rep_middle_name',
        'rep_extension_name',
        'rep_street_address',
        'rep_barangay',
        'rep_city',
        'rep_province',
        'rep_region',
        'rep_contact_number',
        'rep_birthday',
        'rep_age',
        'rep_sex',
        'rep_civil_status',
        'rep_occupation',
        'rep_monthly_salary',
        'rep_relationship',
        'family_composition',
        'medical_conditions',
        'medical_condition_other',
        'assistance_purpose',
        'service_provided',
        'purpose',
        'purpose_other',
        'submitted_to',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'rejection_reason',
        'beneficiary_intake_id',
    ];

    protected $casts = [
        'date_submitted' => 'datetime',
        'beneficiary_birthday' => 'date',
        'rep_birthday' => 'date',
        'reviewed_at' => 'datetime',
        'is_client_beneficiary' => 'boolean',
        'has_representative' => 'boolean',
        'beneficiary_categories' => 'array',
        'family_composition' => 'array',
        'medical_conditions' => 'array',
        'beneficiary_monthly_salary' => 'decimal:2',
        'rep_monthly_salary' => 'decimal:2',
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function beneficiaryIntake(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryIntake::class, 'beneficiary_intake_id');
    }

    public function getBeneficiaryFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->beneficiary_first_name,
            $this->beneficiary_middle_name ? mb_substr($this->beneficiary_middle_name, 0, 1) . '.' : null,
            $this->beneficiary_last_name,
            $this->beneficiary_extension_name,
        ]);
        return implode(' ', $parts);
    }

    public function getRepFullNameAttribute(): ?string
    {
        if (!$this->has_representative && empty($this->rep_first_name)) {
            return null;
        }

        $parts = array_filter([
            $this->rep_first_name,
            $this->rep_middle_name ? mb_substr($this->rep_middle_name, 0, 1) . '.' : null,
            $this->rep_last_name,
            $this->rep_extension_name,
        ]);
        return implode(' ', $parts);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Accepted' => 'bg-success text-white',
            'Rejected' => 'bg-danger text-white',
            default => 'bg-warning text-dark', // 'For Review'
        };
    }

    public function getReferenceNumberAttribute(): string
    {
        return 'ONLINE-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}
