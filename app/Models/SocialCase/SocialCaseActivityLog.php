<?php

namespace App\Models\SocialCase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialCaseActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'details',
        'case_info',
        'admin',
    ];

    protected $casts = [
        'case_info' => 'array',
    ];
}
