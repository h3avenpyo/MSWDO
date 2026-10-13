<?php

namespace App\Models\Wcpu;

class PasswordToken extends WcpuBaseModel
{
    protected $table = 'password_reset_tokens';

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    public $timestamps = false;
}
