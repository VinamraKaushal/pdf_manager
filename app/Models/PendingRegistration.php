<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    // Mass‑assignable attributes
    protected $fillable = [
        'name',
        'email',
        'password',
        'otp',
        'expires_at',
    ];

    // Casts
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Check if the pending registration is still valid.
     */
    public function isValid(): bool
    {
        return $this->expires_at && now()->lessThanOrEqualTo($this->expires_at);
    }
}
