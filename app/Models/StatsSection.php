<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatsSection extends Model
{
    protected $table = 'stats_section';

    protected $fillable = [
        'clients_count',
        'clients_label',
        'sms_count',
        'sms_label',
        'delivery_count',
        'delivery_label',
        'years_count',
        'years_label',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getContent()
    {
        return self::firstOrCreate([], [
            'clients_count' => '500',
            'clients_label' => 'Happy Clients',
            'sms_count' => '10',
            'sms_label' => 'Million+ SMS',
            'delivery_count' => '99',
            'delivery_label' => '% Delivery Rate',
            'years_count' => '5',
            'years_label' => 'Years Experience',
            'is_active' => true,
        ]);
    }
}
