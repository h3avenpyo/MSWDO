<?php

namespace App\Models\SocialCase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialCaseReport extends Model
{
    protected $fillable = [
        'social_case_study_id',
        'case_number',
        'title',
        'generated_at',
        'generated_by',
        'description',
        'body',
        'snapshot',
        'created_by',
        'status',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'snapshot' => 'array',
    ];

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SocialCaseStudy::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'generated_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
