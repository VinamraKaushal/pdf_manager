<?php

// Model: UserCred.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCred extends Model
{
    protected $fillable = ['email', 'password', 'otp', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];
}
