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
            'LaravelのTIPSを一つ生成。',
            'Laravel公式ドキュメント(https://laravel.com/docs)から1ページ選択して解説。',
            'Laravelのよくある質問と回答を一つ生成。',
            'Laravelの珍しい質問と回答を一つ生成。',
        ])->random();

        $lang = Lottery::odds(8, 10)
                       ->winner(fn () => '日本語で。')
                       ->loser(fn () => '英語で。')
                       ->choose();

        $length = '1000文字までに制限。';

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            //'temperature' => 0.3,
            'messages' => [
                ['role' => 'system', 'content' => 'あなたはLaravelに詳しい優秀なプログラマーです'],
                ['role' => 'user', 'content' => $prompt.$lang.$length],
            ],
        ]);

        $tips = trim(Arr::get($response, 'choices.0.message.content'));
        $this->info($tips);
        $this->info(mb_strlen($tips));

        if (blank($tips)) {
            return;
        }

        Notification::route('discord', config('services.discord.channel'))
                    ->route('nostr', NostrRoute::to(sk: config('nostr.keys.sk')))
                    ->notify(new TipsNotification($tips));
    }
}
