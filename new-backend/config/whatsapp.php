<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Configuration
    |--------------------------------------------------------------------------
    */

    'gateway_url' => env('WHATSAPP_GATEWAY_URL', 'http://brevet.online:8005'),
    
    'api_key' => env('WHATSAPP_API_KEY', ''), // Not needed for this gateway
    
    'default_session' => env('WHATSAPP_DEFAULT_SESSION', 'job-placement'),
    
    'timeout' => env('WHATSAPP_TIMEOUT', 30), // seconds
    
    'retry_attempts' => env('WHATSAPP_RETRY_ATTEMPTS', 3),
    
    'message_delay' => env('WHATSAPP_MESSAGE_DELAY', 2000), // milliseconds
    
    /*
    |--------------------------------------------------------------------------
    | Template Messages
    |--------------------------------------------------------------------------
    */
    
    'templates' => [
        'welcome_message' => [
            'name' => 'welcome_message',
            'variables' => ['applicant_name', 'email', 'password'],
        ],
        
        'job_opportunity' => [
            'name' => 'job_opportunity', 
            'variables' => ['applicant_name', 'job_title', 'company_name', 'location', 'salary_range'],
        ],
        
        'selection_update' => [
            'name' => 'selection_update',
            'variables' => ['applicant_name', 'job_title', 'company_name', 'stage', 'application_number'],
        ],
        
        'application_rejected' => [
            'name' => 'application_rejected',
            'variables' => ['applicant_name', 'job_title', 'company_name', 'reason'],
        ],
        
        'application_accepted' => [
            'name' => 'application_accepted',
            'variables' => ['applicant_name', 'job_title', 'company_name', 'application_number'],
        ],
        
        'interview_scheduled' => [
            'name' => 'interview_scheduled',
            'variables' => ['applicant_name', 'job_title', 'date_time', 'location', 'type'],
        ],
        
        'psikotes_scheduled' => [
            'name' => 'psikotes_scheduled',
            'variables' => ['applicant_name', 'job_title', 'date_time', 'location'],
        ],
        
        'medical_scheduled' => [
            'name' => 'medical_scheduled',
            'variables' => ['applicant_name', 'job_title', 'date_time', 'location'],
        ],
        
        'placement_confirmation' => [
            'name' => 'placement_confirmation',
            'variables' => ['applicant_name', 'company_name', 'position_title', 'start_date', 'placement_number'],
        ],
        
        'contract_expiry_alert' => [
            'name' => 'contract_expiry_alert',
            'variables' => ['applicant_name', 'company_name', 'position', 'days_until_expiry', 'end_date'],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    
    'rate_limit' => [
        'enabled' => env('WHATSAPP_RATE_LIMIT_ENABLED', true),
        'max_per_minute' => env('WHATSAPP_MAX_PER_MINUTE', 30),
        'max_per_hour' => env('WHATSAPP_MAX_PER_HOUR', 500),
        'max_per_day' => env('WHATSAPP_MAX_PER_DAY', 5000),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    
    'webhook' => [
        'url' => env('WHATSAPP_WEBHOOK_URL'),
        'token' => env('WHATSAPP_WEBHOOK_TOKEN'),
        'enabled' => env('WHATSAPP_WEBHOOK_ENABLED', false),
    ],
];