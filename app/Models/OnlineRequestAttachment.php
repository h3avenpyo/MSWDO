<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineRequestAttachment extends Model
{
    use \App\Models\Concerns\LogsMswdoActivity;

    protected $fillable = [
        'online_request_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function onlineRequest()
    {
        return $this->belongsTo(OnlineRequest::class, 'online_request_id');
    }
}
