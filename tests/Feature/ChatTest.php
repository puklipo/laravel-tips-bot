<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use OpenAI\Laravel\Facades\OpenAI;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_tips(): void
    {
        Notification::fake();

//        OpenAI::fake();
//
//        $response = $this->artisan('chat:tips');
//
//        $response->assertSuccessful();

        Notification::assertNothingSent();
    }
}
