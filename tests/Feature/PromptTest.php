<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Chat\Prompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PromptTest extends TestCase
{
    public function test_make(): void
    {
        $p = Prompt::make(prompt: 'test');

        $this->assertInstanceOf(Prompt::class, $p);
        $this->assertSame('test', $p->toArray()['messages'][0]['content']);
    }

    public function test_with(): void
    {
        $p = Prompt::make( prompt: 'test')
            ->withModel('model');

        $this->assertSame('model', $p->toArray()['model']);
    }

    public function test_closure(): void
    {
        $p = new Prompt(
            prompt: fn () => app()->version(),
        );

        $this->assertSame(app()->version(), $p->toArray()['messages'][0]['content']);
    }
}
