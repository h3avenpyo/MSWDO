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
        'assistance_type',
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

    protected $appends = [
        'beneficiary_full_name',
        'display_assistance_purpose',
        'uri_ng_assistance',
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

    protected static function booted(): void
    {
        static::saving(function (OnlineFinancialIntake $intake) {
            $upperFields = [
                'client_type',
                'beneficiary_last_name',
                'beneficiary_first_name',
                'beneficiary_middle_name',
                'beneficiary_extension_name',
                'beneficiary_street_address',
                'beneficiary_barangay',
                'beneficiary_city',
                'beneficiary_province',
                'beneficiary_region',
                'beneficiary_sex',
                'beneficiary_civil_status',
                'beneficiary_occupation',
                'beneficiary_category',
                'beneficiary_category_other',
                'beneficiary_relationship',
                'rep_last_name',
                'rep_first_name',
                'rep_middle_name',
                'rep_extension_name',
                'rep_street_address',
                'rep_barangay',
                'rep_city',
                'rep_province',
                'rep_region',
                'rep_sex',
                'rep_civil_status',
                'rep_occupation',
                'rep_relationship',
                'assistance_purpose',
                'assistance_type',
                'service_provided',
                'purpose',
                'purpose_other',
                'medical_condition_other',
                'submitted_to',
            ];

            foreach ($upperFields as $field) {
                if (!empty($intake->{$field}) && is_string($intake->{$field})) {
                    $intake->{$field} = mb_strtoupper(trim($intake->{$field}), 'UTF-8');
                }
            }

            if (is_array($intake->beneficiary_categories)) {
                $intake->beneficiary_categories = array_map(function ($item) {
                    return is_string($item) ? mb_strtoupper(trim($item), 'UTF-8') : $item;
                }, $intake->beneficiary_categories);
            }

            if (is_array($intake->medical_conditions)) {
                $intake->medical_conditions = array_map(function ($item) {
                    return is_string($item) ? mb_strtoupper(trim($item), 'UTF-8') : $item;
                }, $intake->medical_conditions);
            }

            if (is_array($intake->family_composition)) {
                $intake->family_composition = array_map(function ($member) {
                    if (!is_array($member)) {
                        return $member;
                    }
                    foreach (['name', 'relationship', 'occupation', 'civil_status', 'educational_attainment'] as $field) {
                        if (isset($member[$field]) && is_string($member[$field])) {
                            $member[$field] = mb_strtoupper(trim($member[$field]), 'UTF-8');
                        }
                    }
                    return $member;
                }, $intake->family_composition);
            }
        });
    }

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
        return mb_strtoupper(implode(' ', $parts), 'UTF-8');
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
        return mb_strtoupper(implode(' ', $parts), 'UTF-8');
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

    public function getDisplayAssistancePurposeAttribute(): string
    {
        if (! empty($this->assistance_type)) {
            return mb_strtoupper($this->assistance_type, 'UTF-8');
        }

        if (($this->assistance_purpose === 'Other Medical Conditions' || $this->assistance_purpose === 'Others' || $this->assistance_purpose === 'Other' || $this->assistance_purpose === 'ASSISTANCE PURPOSE') && ! empty($this->purpose_other)) {
            return mb_strtoupper($this->purpose_other, 'UTF-8');
        }
        return mb_strtoupper($this->assistance_purpose ?? 'N/A', 'UTF-8');
    }

    public function getUriNgAssistanceAttribute(): ?string
    {
        return $this->assistance_type;
    }

    public function setUriNgAssistanceAttribute($value): void
    {
        $this->attributes['assistance_type'] = $value;
    }

    public function getAssistancePurposeTypeAttribute(): ?string
    {
        return $this->assistance_type;
    }

    public function setAssistancePurposeTypeAttribute($value): void
    {
        $this->attributes['assistance_type'] = $value;
    }
}
