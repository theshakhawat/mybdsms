<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'nid_front',
        'nid_back',
        'trade_license',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'status' => 'string'
    ];
}
