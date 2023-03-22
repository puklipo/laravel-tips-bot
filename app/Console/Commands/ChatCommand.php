<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

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
            'LaravelのTIPSを一つ生成',
            'Laravel公式ドキュメント(https://laravel.com/docs)から1ページ選択して解説',
            'Laravelのよくある質問と回答を一つ生成',
        ])->random();

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'あなたはLaravelに詳しい優秀なプログラマーです'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $tips = Arr::get($response, 'choices.0.message.content');
        $this->info(trim($tips));
    }
}
