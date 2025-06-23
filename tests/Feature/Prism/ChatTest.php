<?php

declare(strict_types=1);

namespace Tests\Feature\Prism;

use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_tips(): void
    {
        // Skip this test for now due to Bedrock provider compatibility issue
        $this->markTestSkipped('Prism/Bedrock provider compatibility issue - bedrock package has missing abstract method implementations');
    }
}