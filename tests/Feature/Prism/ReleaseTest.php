<?php

declare(strict_types=1);

namespace Tests\Feature\Prism;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Prism\Prism\Prism;
use Tests\TestCase;

class ReleaseTest extends TestCase
{
    public function test_release(): void
    {
        Notification::fake();

        Http::fake([
            '*' => Http::response([
                [
                    'published_at' => now()->toDateTimeString(),
                    'body' => 'This is a test release with new features and bug fixes.',
                    'tag_name' => 'v10.0.0',
                    'html_url' => 'https://github.com/laravel/framework/releases/tag/v10.0.0',
                ],
            ]),
        ]);

        Prism::fake([
            'text' => 'これは新機能とバグ修正を含むテストリリースです。',
            'usage' => (object) [
                'promptTokens' => 120,
                'completionTokens' => 58,
                'cacheWriteInputTokens' => 0,
                'cacheReadInputTokens' => 0,
                'thoughtTokens' => 0,
            ],
        ]);

        $response = $this->artisan('prism:chat:release');

        $response->assertSuccessful();
    }
}