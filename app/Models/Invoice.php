<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'order_id',
        'package_id',
        'package_name',
        'plan_type',
        'price_per_sms',
        'sms_count',
        'amount',
        'paid_amount',
        'payment_method',
        'status',
        'due_date',
        'paid_at',
        'sender_bank_name',
        'sender_account',
        'trx_id',
        'slip_image',
        'customer_notes',
        'admin_notes',
        'created_by',
    ];

    protected $casts = [
        'price_per_sms' => 'decimal:4',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'sms_count' => 'integer',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the user that owns the invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the invoice.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the package plan associated with the invoice.
     */
    public function package()
    {
        return $this->belongsTo(PricingPlan::class, 'package_id');
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ym');
        $lastInvoice = self::where('invoice_number', 'like', "{$prefix}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, strrpos($lastInvoice->invoice_number, '-') + 1);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return "{$prefix}-{$nextNumber}";
    }
}

