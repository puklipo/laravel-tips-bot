<?php

namespace Tests\Feature\Prism;

use Tests\TestCase;

class ReleaseTest extends TestCase
{
    public function test_release(): void
    {
        // Skip this test for now due to Prism/Bedrock setup complexity
        $this->markTestSkipped('Prism/Bedrock integration test skipped - requires AWS credentials and complex setup');
    }
}