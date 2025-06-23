<?php

declare(strict_types=1);

namespace App\Console\Commands\Prism;

use App\Chat\PrismPrompt;
use App\Notifications\ReleaseNotification;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use PrismPHP\Prism\Prism;
use PrismPHP\Prism\Providers\Bedrock\Bedrock;
use Revolution\Nostr\Notifications\NostrRoute;

class ReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prism:chat:release {repo=laravel/framework}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate release summaries using Prism+Bedrock';

    /**
     * Execute the console command.
     *
     * @throws RequestException
     */
    public function handle(): void
    {
        Http::baseUrl('https://api.github.com/repos/')
            ->timeout(120)
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

        $date = Carbon::parse(time: $release['published_at'], timezone: 'UTC');

        if ($date->tz(config('app.timezone'))->addDay()->lessThan(now())) {
            return;
        }

        if (blank($release['body'])) {
            return;
        }

        $note = $this->chat(body: $release['body']);

        $this->info($note);

        if (blank($note)) {
            return;
        }

        Notification::route('discord-webhook', config('services.discord.webhook'))
            ->route('nostr', NostrRoute::to(sk: config('nostr.keys.sk')))
            ->route('http', config('tips.api_token'))
            ->notify(new ReleaseNotification(
                repo: $this->argument('repo'),
                ver: $release['tag_name'],
                url: $release['html_url'],
                note: $note,
            ));
    }

    private function chat(string $body): string
    {
        $prompt = PrismPrompt::make(
            prompt: fn () => collect([
                '次のリリースノートを日本語で要約してください。',
                '- 結果だけを出力。',
                '- @から始まるユーザー名を含めない。',
                '- URLを含めない。',
                '----',
                trim($body),
            ])->join(PHP_EOL)
        );

        $response = Prism::text()
            ->using(Bedrock::KEY, $prompt->getModel())
            ->withPrompt($prompt->getPromptContent())
            ->generate();

        $content = trim($response->text);
        $this->info($content);

        $this->line('strlen: '.mb_strlen($content));
        $this->line('usage: '.$response->usage);

        return $content;
    }
}
