<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseInterview extends Model
{
    protected $fillable = [
        'social_case_study_id',
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
        'additional_requirements',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
        'monthly_expenses' => 'decimal:2',
        'recommended_amount' => 'decimal:2',
    ];

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class, 'social_case_study_id');
    }
}
