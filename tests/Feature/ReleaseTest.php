<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
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
                    'body' => 'test',
                    'tag_name' => 'v10.0.0',
                    'html_url' => 'https://',
                ],
            ]),
        ]);

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    'message' => [
                        'content' => 'test',
                    ],
                ],
            ]),
        ]);

        $response = $this->artisan('chat:release');

        $response->assertSuccessful();
    }
}
