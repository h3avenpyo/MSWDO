<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SocialCase\SocialCaseReport;

class SocialCaseStudy extends Model
{
    use HasFactory;

    protected $connection = 'mswdo_social_case';
    protected $table = 'social_case_studies';

    protected $fillable = [
        'client_id',
        'officer_id',
        'case_number',
        'date_processed',
        'client_last_name',
        'client_first_name',
        'client_middle_name',
        'client_age',
        'client_sex',
        'client_barangay',
        'beneficiary_last_name',
        'beneficiary_first_name',
        'beneficiary_middle_name',
        'beneficiary_age',
        'beneficiary_birthday',
        'beneficiary_sex',
        'beneficiary_barangay',
        'medical_conditions',
        'additional_requirements',
        'interview_reason',
        'interview_situation',
        'interview_household',
        'monthly_income',
        'monthly_expenses',
        'family_illnesses',
        'previous_assistance',
        'interview_notes',
        'social_worker_assessment',
        'recommendation',
        'recommended_amount',
        'service_provided',
        'purpose',
        'submitted_to',
        'encoded_by',
        'status',
        'workflow_step',
        'requirements_complete',
        'interview_complete',
        'evaluation_complete',
        'report_generated',
        'released_at',
        'released_by',
        'released_to',
        'assistance_released',
        'assistance_amount',
        'assistance_date',
        'summary',
        'interview_date',
    ];

    protected $casts = [
        'date_processed' => 'date',
        'beneficiary_birthday' => 'date',
        'interview_date' => 'date',
        'assistance_date' => 'date',
        'released_at' => 'datetime',
        'medical_conditions' => 'array',
        'monthly_income' => 'decimal:2',
        'monthly_expenses' => 'decimal:2',
        'recommended_amount' => 'decimal:2',
        'assistance_amount' => 'decimal:2',
        'requirements_complete' => 'boolean',
        'interview_complete' => 'boolean',
        'evaluation_complete' => 'boolean',
        'report_generated' => 'boolean',
        'assistance_released' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function beneficiaryIntake(): HasOne
    {
        return $this->hasOne(BeneficiaryIntake::class);
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(SocialCaseReport::class);
    }
}
