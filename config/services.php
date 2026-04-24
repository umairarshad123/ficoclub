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


    'google' => [
        'ready_sheet_id' => env('GOOGLE_READY_SHEET_ID'),
    'sheet_id'         => env('GOOGLE_SHEET_ID'),
    'credentials_path' => env('GOOGLE_CREDENTIALS_PATH'),
        ],

    'ghl' => [
        'funding_webhook_url' => env('GHL_FUNDING_WEBHOOK_URL'),
        'checkout_webhook_url' => env('GHL_CHECKOUT_WEBHOOK_URL'),
        'referral_webhook_url' => env('GHL_REFERRAL_WEBHOOK_URL'),
        'lead_webhook_url' => env('GHL_LEAD_URL'),
        'roadmap_webhook_url' => env('GHL_ROADMAP_WEBHOOK_URL'),
        'recurring_failed_webhook_url' => env('GHL_RECURRING_FAILED_WEBHOOK_URL'),
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

    'authorize_net' => [
    'api_login_id'      => env('AUTHORIZE_NET_API_LOGIN_ID'),
    'transaction_key'   => env('AUTHORIZE_NET_TRANSACTION_KEY'),
    'public_client_key' => env('AUTHORIZE_NET_PUBLIC_CLIENT_KEY'),
    'environment'       => env('AUTHORIZE_NET_ENVIRONMENT', 'sandbox'),
],

];
