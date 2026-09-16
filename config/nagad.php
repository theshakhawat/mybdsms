<?php

return [
    "sandbox"         => env("NAGAD_SANDBOX", true), // if true it will redirect to sandbox url

    "merchant_id"     => env("NAGAD_MERCHANT_ID", ""),
    "merchant_number" => env("NAGAD_MERCHANT_NUMBER", ""),
    "public_key"      => env("NAGAD_PUBLIC_KEY", ""),
    "private_key"     => env("NAGAD_PRIVATE_KEY", ""),
    "callback_url"    => env("NAGAD_CALLBACK_URL", "http://127.0.0.1:8000/user/nagad/callback"), // By default you can change it in your callback url

    // "merchant_id_2"     => env("NAGAD_MERCHANT_ID2", ""),
    // "merchant_number_2" => env("NAGAD_MERCHANT_NUMBER2", ""),
    // "public_key_2"      => env("NAGAD_PUBLIC_KEY2", ""),
    // "private_key_2"     => env("NAGAD_PRIVATE_KEY2", ""),
    // "callback_url_2"    => env("NAGAD_CALLBACK_URL2", "http://your_domain/user/nagad/callback"),

    //you can add more account as your wise

    'timezone'        => 'Asia/Dhaka', // By default
    "response_type"   => "json" // By default json you can change response type json/html
];
