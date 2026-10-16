<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WCPU Module Configuration
    |--------------------------------------------------------------------------
    |
    | Centralized settings for the Women and Children Protection Unit (WCPU)
    | module integrated into the MSWDO centralized application.
    |
    */

    'database' => [
        'connection' => 'wcpu',
    ],

    'recipients' => [
        'use_db' => (bool) env('USE_DB_RECIPIENTS', true),
        'fallback_list' => env('CASE_REPORT_RECIPIENTS'),
        'fallback_single' => env('CASE_REPORT_FALLBACK'),
    ],

    'mail' => [
        'mailer' => env('WCPU_MAILER', 'wcpu'),
        'from_address' => env('WCPU_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')),
        'from_name' => env('WCPU_MAIL_FROM_NAME', 'WCPU Case Reports'),
    ],

    'activity_log' => [
        'connection' => env('ACTIVITY_LOGGER_DB_CONNECTION', 'wcpu'),
        'enabled' => (bool) env('ACTIVITY_LOGGER_ENABLED', true),
    ],

];
