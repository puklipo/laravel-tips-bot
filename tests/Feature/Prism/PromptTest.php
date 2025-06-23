<?php

declare(strict_types=1);

namespace Tests\Feature\Prism;

use App\Chat\PrismPrompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PromptTest extends TestCase
{
    public function test_make(): void
    {
        $p = PrismPrompt::make(prompt: 'test');

        $this->assertInstanceOf(PrismPrompt::class, $p);
        $this->assertSame('test', $p->toArray()['messages'][0]['content']);
    }

    public function test_with(): void
    {
        $p = PrismPrompt::make(prompt: 'test')
            ->withModel('anthropic.claude-3-sonnet-20240229-v1:0');

        $this->assertSame('anthropic.claude-3-sonnet-20240229-v1:0', $p->toArray()['model']);
    }

    public function test_closure(): void
    {
        $p = new PrismPrompt(
            prompt: fn () => app()->version(),
        );

        $this->assertSame(app()->version(), $p->toArray()['messages'][0]['content']);
    }

    public function test_get_prompt_content(): void
    {
        $p = PrismPrompt::make(prompt: 'test content');
        
        $this->assertSame('test content', $p->getPromptContent());
    }

    public function test_get_model(): void
    {
        $p = PrismPrompt::make(prompt: 'test')
            ->withModel('custom-model');
        
        $this->assertSame('custom-model', $p->getModel());
    }
}