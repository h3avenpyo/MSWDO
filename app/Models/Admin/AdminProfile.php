<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    protected $fillable = [
        'user_id',
        'position',
        'employee_id',
        'phone',
        'address',
        'status',
        'role',
        'email',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
