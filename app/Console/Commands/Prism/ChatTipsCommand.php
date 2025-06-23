<?php

declare(strict_types=1);

namespace App\Console\Commands\Prism;

use App\Chat\PrismPrompt;
use App\Notifications\TipsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Lottery;
use Prism\Bedrock\Bedrock;
use Prism\Prism\Prism;
use Revolution\Nostr\Notifications\NostrRoute;

class ChatTipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prism:chat:tips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laravel tips using Prism+Bedrock';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $prompt = PrismPrompt::make(
            prompt: $this->prompt(),
        );

        $response = Prism::text()
            ->using(Bedrock::KEY, $prompt->getModel())
            ->withPrompt($prompt->getPromptContent())
            ->generate();

        $tips = trim($response->text);
        $this->info($tips);

        $this->line('strlen: '.mb_strlen($tips));
        $this->line('total_tokens: '.$this->calculateTotalTokens($response->usage));

        if (blank($tips)) {
            return;
        }

        Notification::route('discord-webhook', config('services.discord.webhook'))
            ->route('nostr', NostrRoute::to(sk: config('nostr.keys.sk')))
            ->route('http', config('tips.api_token'))
            ->notify(new TipsNotification($tips));
    }

    protected function calculateTotalTokens($usage): int
    {
        return $usage->promptTokens +
               $usage->completionTokens +
               ($usage->cacheWriteInputTokens ?? 0) +
               ($usage->cacheReadInputTokens ?? 0) +
               ($usage->thoughtTokens ?? 0);
    }

    protected function prompt(): string
    {
        $prompt = collect([
            'Tell me one advanced Laravel tips.',
            // 'Select one page from the official Laravel documentation and explain it.',
            // 'Generate one Laravel Frequently Asked Questions and Answers.',
            // 'Generate one unusual question and answer for Laravel.',
        ])->random();

        $lang = Lottery::odds(chances: 5, outOf: 10)
            ->winner(fn () => 'Answer in japanese.')
            ->loser(fn () => 'Answer in english.')
            ->choose();

        return collect([
            $prompt,
            $lang,
            'Please provide only one answer.',
        ])->join(PHP_EOL);
    }
}
