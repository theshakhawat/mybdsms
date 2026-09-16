<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_logo',
        'company_address',
        'company_email',
        'company_phone',
        'company_hours',
        'social_facebook',
        'social_twitter',
        'social_linkedin',
        'social_youtube',
        'account_panel_url',
        'sms_panel_url',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_branch',
        'bank_routing_number',
        'bank_instructions',
        'office_payment_instructions',
    ];

    /**
     * Get the first/only settings record
     */
    public static function getSettings()
    {
        return self::firstOrCreate([]);
    }

    /**
     * Get a single setting value
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::first();
        return $setting ? $setting->$key : $default;
    }

    /**
     * Update settings
     */
    public static function updateSettings(array $data)
    {
        $settings = self::firstOrCreate([]);
        $settings->fill($data);
        $settings->save();

        return $settings;
    }
}
