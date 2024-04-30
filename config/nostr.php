<?php

return [
    'keys' => [
        'sk' => env('NOSTR_SK'),
        'nsec' => env('NOSTR_NSEC'),
        'pk' => env('NOSTR_PK'),
        'npub' => env('NOSTR_NPUB'),
    ],

    /**
     * @see https://github.com/kawax/nostr-vercel-api
     */
    'api_base' => env('NOSTR_API_BASE', 'https://nostr-vercel-api.vercel.app/api/'),

    /**
     * The first relay is used as the primary relay.
     */
    'relays' => [
        'wss://relay.damus.io',

        'wss://relay.nostr.band',

        'wss://nostr.h3z.jp',
        'wss://nostr.holybea.com',
        'wss://nostr.fediverse.jp',
        'wss://relay.nostr.wirednet.jp',
        'wss://nostr-relay.nokotaro.com',
        'wss://relay-jp.nostr.wirednet.jp',
    ],
];
