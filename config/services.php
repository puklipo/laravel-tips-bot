<?php

return [
    'discord' => [
        'webhook' => env('DISCORD_WEBHOOK'),
    ],

    'bedrock' => [
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'access_key' => env('AWS_ACCESS_KEY_ID'),
        'secret_key' => env('AWS_SECRET_ACCESS_KEY'),
        'model' => env('BEDROCK_MODEL', 'anthropic.claude-3-haiku-20240307-v1:0'),
    ],
];
