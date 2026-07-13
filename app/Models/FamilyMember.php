<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    protected $connection = 'mswdo_social_case';

    protected $fillable = [
        'social_case_study_id', 'full_name', 'relationship', 'age', 'sex',
        'occupation', 'monthly_income', 'is_dependent', 'notes',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
        'is_dependent' => 'boolean',
    ];

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class);
    }
}
