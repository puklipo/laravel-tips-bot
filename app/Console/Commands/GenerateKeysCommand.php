<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Revolution\Nostr\Facades\Nostr;

class GenerateKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nostr:generate-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $keys = Nostr::native()->key()->generate();

        // $keys = Nostr::native()->key()->fromNsec(nsec: '');

        $this->info('NOSTR_SK='.$keys->json('sk', ''));
        $this->line('NOSTR_NSEC='.$keys->json('nsec', ''));
        $this->line('NOSTR_PK='.$keys->json('pk', ''));
        $this->line('NOSTR_NPUB='.$keys->json('npub', ''));

        return 0;
    }
}
