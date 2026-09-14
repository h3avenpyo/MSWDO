<?php

namespace App\Models\SocialCase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    protected $fillable = [
        'social_case_study_id',
        'person_id',
        'full_name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
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

    /**
     * The person (client master record) this family member was linked to, if a
     * confident match was found when the case was recorded. Relatives linked here
     * are NEVER the main client of the case.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Client::class, 'person_id');
    }

    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->full_name)) {
            return $this->full_name;
        }

        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ], fn ($part) => $part !== null && $part !== '')));
    }
}
