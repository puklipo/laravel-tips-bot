<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Revolution\Nostr\Facades\Nostr;
use Tests\TestCase;

class NostrTest extends TestCase
{
    public function test_profile(): void
    {
        Nostr::shouldReceive('pool->publish')->twice();

        $this->artisan('nostr:profile')
             ->assertSuccessful();
    }
}
