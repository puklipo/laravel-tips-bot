<?php

declare(strict_types=1);

return [
    'discord' => [
        'webhook' => env('DISCORD_WEBHOOK'),
    ],

    'github' => [
        'token' => env('GITHUB_TOKEN'),
    ],
];
