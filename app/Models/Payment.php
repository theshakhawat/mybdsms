<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'payment_ref_id',
        'trx_id',
        'gateway',
        'amount',
        'charge',
        'currency',
        'status',
        'response_data',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'charge' => 'decimal:2',
        'response_data' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
