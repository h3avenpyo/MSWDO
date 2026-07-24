<?php

namespace App\Models\SocialCase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialCaseReportReleaseLog extends Model
{
    protected $fillable = [
        'social_case_study_id',
        'social_case_report_id',
        'released_by',
        'released_by_name',
        'released_to',
        'released_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'released_at' => 'datetime',
    ];

    public function socialCaseStudy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\SocialCase\SocialCaseStudy::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(SocialCaseReport::class, 'social_case_report_id');
    }

    public function releasedByUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'released_by');
    }
}
