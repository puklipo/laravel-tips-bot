<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Chat\Prompt;
use App\Notifications\ReleaseNotification;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use OpenAI\Laravel\Facades\OpenAI;
use Revolution\Nostr\Notifications\NostrRoute;

class ReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:release {repo=laravel/framework}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @throws RequestException
     */
    public function handle(): void
    {
        Http::baseUrl('https://api.github.com/repos/')
            ->get($this->argument('repo').'/releases', [
                'per_page' => 5,
            ])
            ->throw()
            ->collect()
            ->reverse()
            ->each($this->release(...));
    }

    private function release(array $release): void
    {
        if (! Arr::exists($release, 'published_at')) {
            return;
        }

        $date = Carbon::parse(time: $release['published_at'], tz: 'UTC');

        if ($date->tz(config('app.tz'))->addDay()->lessThan(now())) {
            return;
        }

        $note = $this->chat(body: $release['body']);

        if (blank($note)) {
            return;
        }

        Notification::route('discord', config('services.discord.channel'))
            ->route('nostr', NostrRoute::to(sk: config('nostr.keys.sk')))
            ->notify(new ReleaseNotification(
                repo: $this->argument('repo'),
                ver: $release['tag_name'],
                url: $release['html_url'],
                note: $note,
            ));
    }

    private function chat(string $body): string
    {
        $response = OpenAI::chat()->create(
            Prompt::make(
                system: 'You are Laravel mentor.',
                prompt: fn () => collect([
                    '次のリリースノートを日本語で要約してください。',
                    '',
                    trim($body),
                ])->join(PHP_EOL)
            )->withTemperature(0.0)->toArray()
        );

        $content = trim(Arr::get($response, 'choices.0.message.content'));
        $this->info($content);

        $this->line('strlen: '.mb_strlen($content));
        $this->line('total_tokens: '.Arr::get($response, 'usage.total_tokens'));

        return $content;
    }
}
