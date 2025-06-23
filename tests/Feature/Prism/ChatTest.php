<?php

declare(strict_types=1);

namespace Tests\Feature\Prism;

use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_tips(): void
    {
        // Skip this test for now due to Prism/Bedrock setup complexity
        $this->markTestSkipped('Prism/Bedrock integration test skipped - requires AWS credentials and complex setup');
    }
}