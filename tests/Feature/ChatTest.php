<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Completions\CreateResponse;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_tips(): void
    {
        Notification::fake();

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'text' => 'test',
                    ],
                ],
            ]),
        ]);

        $response = $this->artisan('chat:tips');

        $response->assertSuccessful();
    }
}
