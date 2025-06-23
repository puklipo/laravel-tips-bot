<?php

declare(strict_types=1);

namespace Tests\Feature\Prism;

use Illuminate\Support\Facades\Notification;
use Prism\Prism\Prism;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_tips(): void
    {
        Notification::fake();

        Prism::fake([
            'text' => 'This is a fake Laravel tip about using Eloquent relationships effectively.',
            'usage' => (object) [
                'promptTokens' => 85,
                'completionTokens' => 42,
                'cacheWriteInputTokens' => 0,
                'cacheReadInputTokens' => 0,
                'thoughtTokens' => 0,
            ],
        ]);

        $response = $this->artisan('prism:chat:tips');

        $response->assertSuccessful();
    }
}