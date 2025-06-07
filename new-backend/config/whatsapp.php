<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for WhatsApp gateway integration.
    |
    */

    'base_url' => env('WHATSAPP_BASE_URL', 'http://brevet.online:8005'),

    'session_id' => env('WHATSAPP_SESSION_ID', 'job-placement'),

    'timeout' => env('WHATSAPP_TIMEOUT', 30),

    'auto_broadcast' => env('WHATSAPP_AUTO_BROADCAST', true),

    'rate_limit' => [
        'messages_per_minute' => env('WHATSAPP_RATE_LIMIT', 60),
        'delay_between_messages' => env('WHATSAPP_MESSAGE_DELAY', 500), // milliseconds
    ],

    'retry' => [
        'max_attempts' => env('WHATSAPP_MAX_RETRIES', 3),
        'backoff_seconds' => [10, 30, 60],
    ],
];
