<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default SMS Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "textbee", "log"
    |
    */
    'default' => env('SMS_PROVIDER', 'textbee'),

    /*
    |--------------------------------------------------------------------------
    | TextBee SMS Gateway Settings
    |--------------------------------------------------------------------------
    */
    'api_key' => env('SMS_API_KEY'),
    'api_url' => env('SMS_API_URL', 'https://api.textbee.dev/api/v1/gateway/send-sms'),
    'device_id' => env('SMS_DEVICE_ID'),
    'sender_name' => env('SMS_SENDER_NAME', 'MSWDO'),
    'devices_url' => env('SMS_DEVICES_URL', 'https://api.textbee.dev/api/v1/gateway/devices'),
];
