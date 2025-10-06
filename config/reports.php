<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Daily Report Generation Time
    |--------------------------------------------------------------------------
    |
    | The time when daily reports should be automatically generated.
    | Format: HH:MM (24-hour format)
    |
    */
    'daily_generation_time' => env('DAILY_REPORT_TIME', '23:59'),

    /*
    |--------------------------------------------------------------------------
    | Export Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for report exports
    |
    */
    'export' => [
        'max_records' => 50000,
        'chunk_size' => 1000,
        'timeout' => 300, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for report caching
    |
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // seconds (1 hour)
    ],
];
