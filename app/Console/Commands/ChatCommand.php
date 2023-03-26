<?php

namespace App\Console\Commands;

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
        $prompt = collect([
            'Tell me one Laravel tips. ',
            'Laravel公式ドキュメント(https://laravel.com/docs)から1ページ選択して解説。',
            'Laravelのよくある質問と回答を一つ生成。',
            'Laravelの珍しい質問と回答を一つ生成。',
        ])->random();

        $lang = Lottery::odds(chances: 8, outOf: 10)
                       ->winner(fn () => '日本語で。')
                       ->loser(fn () => '英語で。')
                       ->choose();

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'max_tokens' => 1000,
            //'temperature' => 0.3,
            'messages' => [
                ['role' => 'system', 'content' => 'あなたはLaravelに詳しい優秀なプログラマーです'],
                ['role' => 'user', 'content' => $prompt.$lang],
            ],
        ]);

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
}
