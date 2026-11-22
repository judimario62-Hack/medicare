<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'chat_model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
        'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'openai'),
        'ollama' => [
            'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
            'model' => env('OLLAMA_MODEL', 'medgemma'),
            'timeout' => (int) env('OLLAMA_TIMEOUT', 300),
        ],
    ],

    'bootstrap_accounts' => [
        'admin' => [
            'name' => env('MEDICARE_ADMIN_NAME'),
            'email' => env('MEDICARE_ADMIN_EMAIL'),
            'password' => env('MEDICARE_ADMIN_PASSWORD'),
        ],
        'doctor' => [
            'name' => env('MEDICARE_DOCTOR_NAME'),
            'email' => env('MEDICARE_DOCTOR_EMAIL'),
            'password' => env('MEDICARE_DOCTOR_PASSWORD'),
        ],
        'staff' => [
            'name' => env('MEDICARE_STAFF_NAME'),
            'email' => env('MEDICARE_STAFF_EMAIL'),
            'password' => env('MEDICARE_STAFF_PASSWORD'),
        ],
    ],

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
];
