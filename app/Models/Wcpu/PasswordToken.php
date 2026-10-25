<?php

namespace App\Models\Wcpu;

class PasswordToken extends WcpuBaseModel
{
    protected $table = 'vawc_password_reset_tokens';

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    public $timestamps = false;
}
