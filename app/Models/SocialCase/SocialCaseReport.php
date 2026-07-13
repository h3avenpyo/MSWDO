<?php

namespace App\Models\SocialCase;

use App\Models\SocialCaseStudy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialCaseReport extends Model
{
    protected $connection = 'mswdo_social_case';

    protected $table = 'social_case_reports';

    protected $fillable = [
        'social_case_study_id',
        'case_number',
        'title',
        'description',
        'created_by',
        'generated_at',
        'generated_by',
        'status',
        'body',
        'snapshot',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'snapshot' => 'array',
    ];

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(SocialCaseStudy::class);
    }
}
