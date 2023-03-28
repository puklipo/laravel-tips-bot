<?php

declare(strict_types=1);

use App\Chat\Prompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PromptTest extends TestCase
{
    public function test_make(): void
    {
        $p = Prompt::make(system: 'sys', prompt: 'test');

        $this->assertSame('sys', $p->toArray()['messages'][0]['content']);
    }

    public function test_with(): void
    {
        $p = Prompt::make(system: 'sys', prompt: 'test')
                   ->withModel('model')
                   ->withMaxTokens(1)
                   ->withTemperature(0.5);

        $this->assertSame('model', $p->toArray()['model']);
        $this->assertSame(1, $p->toArray()['max_tokens']);
        $this->assertSame(0.5, $p->toArray()['temperature']);
    }

    public function test_closure(): void
    {
        $p = new Prompt(
            system: 'sys',
            prompt: fn () => app()->version(),
        );

        $this->assertSame(app()->version(), $p->toArray()['messages'][1]['content']);
    }
}
