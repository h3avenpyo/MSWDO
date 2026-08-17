<?php

namespace App\Models\SocialCase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    protected $fillable = [
        'social_case_study_id',
        'full_name',
        'relationship',
        'age',
        'sex',
        'education',
        'occupation',
        'monthly_income',
        'is_dependent',
        'notes',
    ];

    protected $casts = [
        'is_dependent' => 'boolean',
    ];

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class);
    }
}
