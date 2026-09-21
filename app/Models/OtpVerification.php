<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'mobile_number',
        'code',
        'expires_at',
        'consumed',
    ];

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed' => 'boolean',
    ];

    protected $keyType = 'string';

    public $incrementing = false;
}
