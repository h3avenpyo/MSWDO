<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    use \App\Models\Concerns\LogsMswdoActivity;

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
