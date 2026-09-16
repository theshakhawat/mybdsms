<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'package_id',
        'order_number',
        'package_name',
        'plan_type',
        'price_per_sms',
        'amount',
        'sms_count',
        'payment_method',
        'payment_status',
        'status',
    ];

    protected $casts = [
        'price_per_sms' => 'decimal:4',
        'amount' => 'decimal:2',
        'sms_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(PricingPlan::class, 'package_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
