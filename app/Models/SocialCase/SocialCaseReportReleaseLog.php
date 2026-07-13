<?php

namespace App\Models\SocialCase;

use Illuminate\Database\Eloquent\Model;

class SocialCaseReportReleaseLog extends Model
{
    protected $connection = 'mswdo_social_case';
    protected $fillable = [
        'social_case_study_id', 'social_case_report_id', 'released_by',
        'released_by_name', 'released_to', 'released_at', 'ip_address', 'user_agent',
    ];

    protected $casts = ['released_at' => 'datetime'];
}
