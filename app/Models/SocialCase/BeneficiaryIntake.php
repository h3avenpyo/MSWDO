<?php

namespace App\Models\SocialCase;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryIntake extends Model
{
    protected $fillable = [
        'client_id',
        'social_case_study_id',
        'control_number',
        'client_type',
        'date_processed',
        'time_start',
        'time_end',
        'encoder',
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
        'social_worker_assessment',
        'recommended_assistance_type',
        'assistance_purpose',
        'recommended_amount',
        'interviewed_by',
        'reviewed_by',
        'medical_conditions',
        'medical_condition_other',
        'service_provided',
        'purpose',
        'purpose_other',
        'submitted_to',
    ];

    protected $casts = [
        'date_processed' => 'date',
        'beneficiary_birthday' => 'date',
        'rep_birthday' => 'date',
        'is_client_beneficiary' => 'boolean',
        'has_representative' => 'boolean',
        'medical_conditions' => 'array',
        'beneficiary_categories' => 'array',
        'family_composition' => 'array',
        'beneficiary_monthly_salary' => 'decimal:2',
        'rep_monthly_salary' => 'decimal:2',
        'recommended_amount' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class);
    }

    public function encoderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoder');
    }

    public function getClientFullNameAttribute(): ?string
    {
        return $this->client?->full_name;
    }

    public function getBeneficiaryFullNameAttribute(): ?string
    {
        $parts = array_filter([
            $this->beneficiary_first_name,
            $this->beneficiary_middle_name,
            $this->beneficiary_last_name,
            $this->beneficiary_extension_name,
        ], fn($p) => !is_null($p) && $p !== '');

        if (! empty($parts)) {
            return implode(' ', $parts);
        }
        if ($this->is_client_beneficiary && $this->client) {
            return $this->client_full_name;
        }
        return 'N/A';
    }

    public function getRepresentativeFullNameAttribute(): ?string
    {
        if (! $this->has_representative) {
            return null;
        }
        $parts = array_filter([
            $this->rep_first_name,
            $this->rep_middle_name,
            $this->rep_last_name,
            $this->rep_extension_name,
        ], fn($p) => !is_null($p) && $p !== '');

        return ! empty($parts) ? implode(' ', $parts) : 'N/A';
    }

    public function getBeneficiaryAddressFormattedAttribute(): string
    {
        $parts = array_filter([
            $this->beneficiary_street_address,
            $this->beneficiary_barangay,
            $this->beneficiary_city ?? 'Silang',
            $this->beneficiary_province ?? 'Cavite',
            $this->beneficiary_region ?? 'Region IV-A',
        ]);
        return implode(', ', $parts);
    }

    public function getRepresentativeAddressFormattedAttribute(): ?string
    {
        if (! $this->has_representative) {
            return null;
        }
        $parts = array_filter([
            $this->rep_street_address,
            $this->rep_barangay,
            $this->rep_city ?? 'Silang',
            $this->rep_province ?? 'Cavite',
            $this->rep_region ?? 'Region IV-A',
        ]);
        return ! empty($parts) ? implode(', ', $parts) : 'N/A';
    }

    public function getDisplayCategoryAttribute(): string
    {
        if (! empty($this->beneficiary_categories) && is_array($this->beneficiary_categories)) {
            return implode(', ', $this->beneficiary_categories);
        }
        if ($this->beneficiary_category === 'Other' && ! empty($this->beneficiary_category_other)) {
            return $this->beneficiary_category_other;
        }
        return $this->beneficiary_category ?? 'N/A';
    }

    public function getDisplayAssistancePurposeAttribute(): string
    {
        if (($this->assistance_purpose === 'Other Medical Conditions' || $this->assistance_purpose === 'Others') && ! empty($this->purpose_other)) {
            return $this->purpose_other;
        }
        return $this->assistance_purpose ?? 'N/A';
    }
}
