<?php

namespace App\Models\SocialCase;

use Illuminate\Database\Eloquent\Model;

class SocialCaseReport extends Model
{
    protected $connection = 'mswdo_social_case';

    protected $table = 'social_case_reports';

    protected $fillable = [
        'case_number',
        'title',
        'description',
        'created_by',
        'status',
    ];
}
