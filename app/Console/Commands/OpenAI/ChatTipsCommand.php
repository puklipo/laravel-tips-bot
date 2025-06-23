<?php

declare(strict_types=1);

namespace App\Console\Commands\OpenAI;

use App\Chat\OpenAIPrompt;
use App\Notifications\TipsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Lottery;
use OpenAI\Laravel\Facades\OpenAI;
use Revolution\Nostr\Notifications\NostrRoute;

class ChatTipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openai:chat:tips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laravel tips using OpenAI';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $response = OpenAI::chat()->create(
            OpenAIPrompt::make(
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

        Notification::route('discord-webhook', config('services.discord.webhook'))
            ->route('nostr', NostrRoute::to(sk: config('nostr.keys.sk')))
            ->route('http', config('tips.api_token'))
            ->notify(new TipsNotification($tips));
    }

    protected function prompt(): string
    {
        $prompt = collect([
            // Advanced tips
            'Tell me one advanced Laravel tips.',
            'Share an advanced Laravel technique that many developers don\'t know about.',
            'Explain one Laravel feature that can significantly improve code performance.',
            'Describe a lesser-known Laravel method that can simplify complex tasks.',

            // Documentation-based
            'Select one page from the official Laravel documentation and explain it with a practical example.',
            'Pick a Laravel feature from the docs and show how to use it in a real-world scenario.',

            // Q&A format
            'Generate one Laravel Frequently Asked Questions and Answers.',
            'Generate one unusual question and answer for Laravel.',
            'Create a challenging Laravel interview question with a detailed answer.',

            // Best practices
            'Share one Laravel best practice that improves code maintainability.',
            'Explain one Laravel security best practice with an example.',
            'Describe one Laravel testing best practice that developers should follow.',

            // Performance & optimization
            'Share one Laravel performance optimization tip with code examples.',
            'Explain how to optimize Laravel database queries with a practical example.',
            'Describe one Laravel caching strategy that can boost application performance.',

            // Eloquent & database
            'Share one advanced Eloquent ORM tip with a practical example.',
            'Explain one Laravel database migration technique that solves common problems.',
            'Describe one Laravel relationship feature that simplifies data retrieval.',

            // Architecture & patterns
            'Explain one Laravel design pattern implementation with code examples.',
            'Share one Laravel service container tip that improves dependency injection.',
            'Describe one Laravel middleware technique for solving common problems.',

            // Modern Laravel features
            'Explain one Laravel feature introduced in recent versions with practical usage.',
            'Share one Laravel Livewire or Inertia.js tip for better user experiences.',
            'Describe one Laravel API resource technique for better API development.',

            // Debugging & troubleshooting
            'Share one Laravel debugging technique that helps identify issues quickly.',
            'Explain one Laravel logging strategy that improves application monitoring.',
            'Describe one Laravel error handling pattern with implementation details.',

            // Artisan & CLI
            'Share one useful Laravel Artisan command with practical examples.',
            'Explain how to create a custom Laravel Artisan command for a specific task.',

            // Configuration & environment
            'Share one Laravel configuration tip that improves application flexibility.',
            'Explain one Laravel environment management technique for different deployment stages.',
        ])->random();

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
