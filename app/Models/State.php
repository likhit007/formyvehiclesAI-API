<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'code',
    ];

    protected $guarded = [];

    protected $keyType = 'string';

    public $incrementing = false;
}
