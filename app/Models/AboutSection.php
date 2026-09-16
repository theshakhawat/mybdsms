<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    protected $table = 'about_section';

    protected $fillable = [
        'title',
        'badge',
        'subtitle',
        'description',
        'image',
        'features',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    public static function getContent()
    {
        return self::firstOrCreate([], [
            'title' => 'Why Choose MyBDSMS?',
            'badge' => 'About Us',
            'subtitle' => 'A trusted SMS service provider committed to delivering excellence in bulk messaging solutions for businesses of all sizes.',
            'description' => 'MyBDSMS, a venture of Freelancer Digital Expert, is Bangladesh\'s leading SMS service provider. We understand that in today\'s fast-paced digital world, effective communication is key to business success.',
            'features' => [
                'Direct connections with all mobile operators',
                '99.9% delivery guarantee',
                'Real-time delivery reports',
                '24/7 customer support',
                'Competitive pricing in Bangladesh',
            ],
            'is_active' => true,
        ]);
    }
}
