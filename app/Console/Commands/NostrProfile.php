<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Revolution\Nostr\Event;
use Revolution\Nostr\Facades\Nostr;
use Revolution\Nostr\Kind;
use Revolution\Nostr\Profile;
use Revolution\Nostr\Tags\ReferenceTag;

class NostrProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nostr:profile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $profile = new Profile(
            name: 'laravel_tips',
            display_name: 'Laravel Tips [bot]',
            about: 'ChatGPT APIによる自動生成なので内容は間違いを多く含みます。「間違いを指摘してLaravelのスキルを上げる」のがおすすめの使い方。'.PHP_EOL.
            'As it is generated automatically by the ChatGPT API, the content contains many errors. It is recommended to use it to point out mistakes and increase your Laravel skills.',
            website: 'https://puklipo.com/',
            nip05: 'pcse@getalby.com',
            lud16: 'pcse@getalby.com',
        );

        $event = Event::make(
            kind: Kind::Metadata,
            content: $profile->toJson(),
            created_at: now()->timestamp,
        );

        Nostr::pool()->publish(event: $event, sk: config('nostr.keys.sk'));

        $this->relays();
    }

    protected function relays(): void
    {
        $relays = collect(config('nostr.relays'))
            ->map(fn ($relay) => ReferenceTag::make(r: $relay));

        $event = Event::make(
            kind: Kind::RelayList,
            created_at: now()->timestamp,
            tags: $relays->toArray(),
        );

        Nostr::pool()->publish(event: $event, sk: config('nostr.keys.sk'));
    }
}
