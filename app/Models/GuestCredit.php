<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestCredit extends Model {
    protected $fillable = [
        'device_signature',
        'credits',
        'last_reset',
    ];

    public function setDeviceSignatureAttribute($value) {
        $this->attributes['device_signature'] = trim($value);
    }

}
