<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Copilot\CopilotRuntime;
use App\Notifications\ReleaseNotification;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
    protected $description = 'Generate release summaries using Bedrock';

    /**
     * Execute the console command.
     *
     * @throws RequestException
     */
    public function handle(): void
    {
        Http::baseUrl('https://api.github.com/repos/')
            ->withToken(config('services.github.token'))
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

        if ($date->plus(hours: 6)->lt(now('UTC'))) {
            return;
        }

        if (Str::length($release['body']) < 50) {
            $this->info('Release body is too short, skipping.');
            $this->line($release['body']);

            return;
        }

        $note = $this->copilot(body: $release['body']);

        $this->info($note);

        if (blank($note)) {
            return;
        }

        Notification::route('discord-webhook', config('services.discord.webhook'))
            ->route('http', config('tips.api_token'))
            // ->route('nostr', NostrRoute::to(sk: config('nostr.keys.sk')))
            ->notify(new ReleaseNotification(
                repo: $this->argument('repo'),
                ver: $release['tag_name'],
                url: $release['html_url'],
                note: $note,
            ));
    }

    private function copilot(string $body): string
    {
        $content = CopilotRuntime::run($this->prompt($body));

        $this->info($content);

        // $this->line('strlen: '.mb_strlen($content));

        return $content;
    }

    protected function prompt(string $body): string
    {
        return collect([
            '次の`'.$this->argument('repo').'`のリリースノートを日本語で要約してください。',
            '- 結果だけをMarkdown形式で出力して '.Storage::path('copilot.md').' に保存。',
            '- @から始まるユーザー名を含めない。',
            '- URLを含めない。',
            '----',
            trim($body),
        ])->join(PHP_EOL);
    }
}
