<?php

declare(strict_types=1);

namespace Tests\Feature\OpenAI;

use Illuminate\Support\Facades\Notification;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
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
                        'index' => 0,
                        'message' => [
                            'content' => 'test',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->artisan('openai:chat:tips');

        $response->assertSuccessful();
    }
}
