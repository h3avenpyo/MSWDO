<?php

namespace App\Models\Financial;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineFinancialIntake extends Model
{
    public const DOCUMENT_LABELS = [
        'request_letter' => 'Liham para kay Hon. Mayor Edward E. Carranza (Ret.)',
        'medical_certificate' => 'Medical Certificate o Medical Abstract',
        'indigency_certificate' => 'Barangay Certificate of Indigency na may dry seal (Patient at Claimant)',
        'medical_bill' => 'Reseta, Laboratory Request, Treatment Protocol, Final o Running Hospital Bill',
        'valid_id' => 'Valid ID na may pirma o thumbmark (Patient at Claimant)',
    ];

    protected $fillable = [
        'documents',
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
        'sms_status',
        'sms_error',
        'sms_notified_at',
        'approval_sms_sent_at',
        'rejection_sms_sent_at',
    ];

    protected $appends = [
        'beneficiary_full_name',
        'display_assistance_purpose',
        'display_medical_condition',
        'uri_ng_assistance',
        'applicant_name',
        'applicant_contact_number',
    ];

    protected $casts = [
        'documents' => 'array',
        'date_submitted' => 'datetime',
        'beneficiary_birthday' => 'date',
        'rep_birthday' => 'date',
        'reviewed_at' => 'datetime',
        'sms_notified_at' => 'datetime',
        'approval_sms_sent_at' => 'datetime',
        'rejection_sms_sent_at' => 'datetime',
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

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FinancialAssistanceMessage::class, 'online_intake_id')->orderBy('created_at', 'desc');
    }

    public function latestMessage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FinancialAssistanceMessage::class, 'online_intake_id')->latestOfMany();
    }

    public function getApplicantNameAttribute(): string
    {
        if ($this->has_representative && !empty(trim($this->rep_full_name ?? '')) && $this->rep_full_name !== 'N/A') {
            return $this->rep_full_name;
        }
        return $this->beneficiary_full_name;
    }

    public function getApplicantContactNumberAttribute(): ?string
    {
        if ($this->has_representative && !empty(trim($this->rep_contact_number ?? '')) && $this->rep_contact_number !== 'N/A') {
            return $this->rep_contact_number;
        }
        return $this->beneficiary_contact_number;
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

    public function getDisplayMedicalConditionAttribute(): ?string
    {
        if (!empty($this->medical_conditions) && is_array($this->medical_conditions) && count($this->medical_conditions) > 0) {
            $filtered = array_values(array_filter($this->medical_conditions, fn($c) => !empty($c) && is_string($c) && trim($c) !== ''));
            if (!empty($filtered)) {
                return mb_strtoupper(implode(', ', $filtered), 'UTF-8');
            }
        }

        if (!empty($this->medical_condition_other)) {
            return mb_strtoupper($this->medical_condition_other, 'UTF-8');
        }

        if (($this->assistance_purpose === 'Other Medical Conditions' || $this->assistance_purpose === 'Others' || $this->assistance_purpose === 'Other') && !empty($this->purpose_other)) {
            return mb_strtoupper($this->purpose_other, 'UTF-8');
        }

        if (!empty($this->assistance_purpose) && $this->assistance_purpose !== 'ASSISTANCE PURPOSE' && $this->assistance_purpose !== 'GENERAL ASSISTANCE REQUEST') {
            return mb_strtoupper($this->assistance_purpose, 'UTF-8');
        }

        return null;
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
