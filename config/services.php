<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'nagad' => [
        'mode' => env('NAGAD_MODE', 'sandbox'),
        'base_url' => env('NAGAD_BASE_URL'),
        'merchant_id' => env('NAGAD_MERCHANT_ID'),
        'account_number' => env('NAGAD_ACCOUNT_NUMBER'),
        'callback_url' => env('NAGAD_CALLBACK_URL'),
        'public_key_path' => storage_path('app/nagad_public_key.pem'),
        'private_key_path' => storage_path('app/nagad_private_key.pem'),
    ],
    'dgepay' => [
        'base_url'      => env('DGEPAY_BASE_URL'),
        'client_id'     => env('DGEPAY_CLIENT_ID'),
        'client_secret' => env('DGEPAY_CLIENT_SECRET'),
        'api_key'       => env('DGEPAY_API_KEY'),
        'merchant_name' => env('DGEPAY_MERCHANT_NAME'),
        'callback_url'  => env('DGEPAY_CALLBACK_URL'),
    ],

];
