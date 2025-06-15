<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpAuthLog extends Model
{
    protected $fillable = [
        'user_id', 'email', 'ip_address', 'authenticated_at',
    ];

    public $timestamps = true;
}
