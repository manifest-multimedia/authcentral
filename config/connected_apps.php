<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Connected Applications
    |--------------------------------------------------------------------------
    |
    | This file defines the applications that connect to AuthCentral for
    | SSO and roles/permissions synchronization.
    |
    */

    'apps' => [
        'cis' => [
            'name' => 'College Information System',
            'url' => env('CIS_URL', 'https://college.pnmtc.edu.gh'),
            'api_url' => env('CIS_API_URL', 'https://college.pnmtc.edu.gh/api'),
            'webhook_url' => env('CIS_WEBHOOK_URL', 'https://college.pnmtc.edu.gh/api/roles-permissions/sync'),
            'api_key' => env('CIS_API_KEY'),
            'webhook_secret' => env('CIS_WEBHOOK_SECRET'),
        ],
        
        'mhtia' => [
            'name' => 'Ministry HQ Training & Innovation App',
            'url' => env('MHTIA_URL', 'https://mhtia.pnmtc.edu.gh'),
            'api_url' => env('MHTIA_API_URL', 'https://mhtia.pnmtc.edu.gh/api'),
            'webhook_url' => env('MHTIA_WEBHOOK_URL', 'https://mhtia.pnmtc.edu.gh/api/roles-permissions/sync'),
            'api_key' => env('MHTIA_API_KEY'),
            'webhook_secret' => env('MHTIA_WEBHOOK_SECRET'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Global settings for webhook synchronization.
    |
    */

    'webhooks' => [
        'max_retries' => env('WEBHOOK_MAX_RETRIES', 5),
        'retry_after' => [
            30,     // 30 seconds
            300,    // 5 minutes
            1800,   // 30 minutes
            7200,   // 2 hours
            21600,  // 6 hours
        ],
        'dead_letter_threshold' => env('WEBHOOK_DEAD_LETTER_THRESHOLD', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reconciliation Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for periodic roles/permissions reconciliation.
    |
    */

    'reconciliation' => [
        'schedule' => env('RECONCILIATION_SCHEDULE', '0 1 * * *'), // 1 AM daily
        'batch_size' => env('RECONCILIATION_BATCH_SIZE', 100),
        'timeout' => env('RECONCILIATION_TIMEOUT', 300), // 5 minutes
    ],
];