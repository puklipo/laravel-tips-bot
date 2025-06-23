<?php

declare(strict_types=1);

namespace Tests\Feature\Prism;

use Illuminate\Support\Facades\Notification;
use Prism\Prism\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Prism\Prism\ValueObjects\Usage;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_tips(): void
    {
        Notification::fake();

        $fakeResponse = TextResponseFake::make()
            ->withText('This is a fake Laravel tip about using Eloquent relationships effectively.')
            ->withUsage(new Usage(85, 42));

        Prism::fake([$fakeResponse]);

        $response = $this->artisan('prism:chat:tips');

        $response->assertSuccessful();
    }
}