<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Revolution\Amazon\Bedrock\Facades\Bedrock;
use Revolution\Amazon\Bedrock\Testing\TextResponseFake;
use Revolution\Amazon\Bedrock\ValueObjects\Usage;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_tips(): void
    {
        Notification::fake();

        $fakeResponse = TextResponseFake::make()
            ->withText('This is a fake Laravel tip about using Eloquent relationships effectively.')
            ->withUsage(new Usage(85, 42));

        Bedrock::fake([$fakeResponse]);

        $response = $this->artisan('chat:tips');

        $response->assertSuccessful();
    }
}
