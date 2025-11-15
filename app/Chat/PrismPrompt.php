<?php

declare(strict_types=1);

namespace App\Chat;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

class PrismPrompt implements Arrayable
{
    protected string $model = 'global.anthropic.claude-sonnet-4-5-20250929-v1:0';

    public function __construct(
        protected readonly string|Closure $prompt,
    ) {}

    public static function make(
        string|Closure $prompt,
    ): static {
        return new static(prompt: $prompt);
    }

    public function withModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return array{
     *     model: string,
     *     max_tokens: int,
     *     temperature: float,
     *     messages: array{
     *         0: array{role: string, content: string},
     *         1: array{role: string, content: string}
     *     }
     * }
     */
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => is_callable($this->prompt) ? call_user_func($this->prompt) : $this->prompt,
                ],
            ],
        ];
    }

    /**
     * Get the prompt content for Prism usage
     */
    public function getPromptContent(): string
    {
        return is_callable($this->prompt) ? call_user_func($this->prompt) : $this->prompt;
    }

    /**
     * Get the model for Bedrock usage
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
