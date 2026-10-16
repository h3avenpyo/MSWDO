<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLoginCode extends Model
{
    protected $fillable = [
        'user_id',
        'auth_source',
        'email',
        'code',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];
}
