<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Chat\Prompt;
use App\Notifications\TipsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Lottery;
use OpenAI\Laravel\Facades\OpenAI;
use Revolution\Nostr\Notifications\NostrRoute;

class ChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:tips';

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
        $response = OpenAI::chat()->create(
            Prompt::make(
                system: 'Act as a good programmer who knows Laravel.',
                prompt: $this->prompt(),
            )->toArray()
        );

        $tips = trim(Arr::get($response, 'choices.0.message.content'));
        $this->info($tips);

        $this->line('strlen: '.mb_strlen($tips));
        $this->line('total_tokens: '.Arr::get($response, 'usage.total_tokens'));

        if (blank($tips)) {
            return;
        }

        Notification::route('discord', config('services.discord.channel'))
                    ->route('nostr', NostrRoute::to(sk: config('nostr.keys.sk')))
                    ->notify(new TipsNotification($tips));
    }

    protected function prompt(): string
    {
        $prompt = collect([
            'Tell me one Laravel tips.',
            'Select one page from the official Laravel documentation(https://laravel.com/docs) and explain it.',
            'Generate one Laravel Frequently Asked Questions and Answers.',
            'Generate one unusual question and answer for Laravel.',
        ])->random();

        $lang = Lottery::odds(chances: 8, outOf: 10)
                       ->winner(fn () => 'Answer in japanese.')
                       ->loser(fn () => 'Answer in english.')
                       ->choose();

        return $prompt.$lang.'only one answer.';
    }
}
