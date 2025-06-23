<?php

declare(strict_types=1);

namespace Tests\Feature\Prism;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Prism\Prism\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Prism\Prism\ValueObjects\Usage;
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

        $fakeResponse = TextResponseFake::make()
            ->withText('これは新機能とバグ修正を含むテストリリースです。')
            ->withUsage(new Usage(120, 58));

        Prism::fake([$fakeResponse]);

        $response = $this->artisan('prism:chat:release');

        $response->assertSuccessful();
    }
}