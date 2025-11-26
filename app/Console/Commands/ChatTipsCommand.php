<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Notifications\TipsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Lottery;
use Revolution\Amazon\Bedrock\Facades\Bedrock;
use Revolution\Amazon\Bedrock\ValueObjects\Usage;
use Revolution\Nostr\Notifications\NostrRoute;

class ChatTipsCommand extends Command
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
    protected $description = 'Generate Laravel tips using Bedrock';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $response = Bedrock::text()
            ->using(Bedrock::KEY, config('bedrock.model'))
            ->withPrompt($this->prompt())
            ->asText();

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

    protected function calculateTotalTokens(Usage $usage): int
    {
        return $usage->promptTokens +
               $usage->completionTokens;
    }

    protected function prompt(): string
    {
        $prompt = collect(config('prompt.tips'))->random();

        $lang = Lottery::odds(chances: 5, outOf: 10)
            ->winner(fn () => 'Answer in japanese.')
            ->loser(fn () => 'Answer in english.')
            ->choose();

        return collect([
            $prompt,
            $lang,
            'Use ## for markdown headings.',
            'Please provide only one answer.',
        ])->join(PHP_EOL);
    }
}
