<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Revolution\Copilot\Facades\Copilot;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_tips(): void
    {
        Notification::fake();

        Copilot::fake('This is a fake Laravel tip about using Eloquent relationships effectively.');

        $response = $this->artisan('chat:tips');

        $response->assertSuccessful();
    }
}
