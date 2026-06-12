<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $table =
        'otp_verifications';

    protected $fillable = [

        'userid',

        'purpose',

        'otp_hash',

        'expires_at',

        'is_verified'

    ];

    protected $casts = [

        'expires_at' => 'datetime',

        'is_verified' => 'boolean'

    ];
}
