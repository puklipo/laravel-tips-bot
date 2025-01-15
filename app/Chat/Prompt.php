<?php

declare(strict_types=1);

namespace App\Chat;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

class Prompt implements Arrayable
{
    protected string $model = 'o1-mini';
    protected int $max_tokens = 1000;
    protected float $temperature = 1.0;

    public function __construct(
        protected readonly string $system,
        protected readonly string|Closure $prompt,
    ) {
    }

    public static function make(
        string $system,
        string|Closure $prompt,
    ): static {
        return new static(system: $system, prompt: $prompt);
    }

    public function withModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function withMaxTokens(int $max_tokens): static
    {
        $this->max_tokens = $max_tokens;

        return $this;
    }

    public function withTemperature(float $temperature): static
    {
        $this->temperature = $temperature;

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
            'max_tokens' => $this->max_tokens,
            'temperature' => $this->temperature,
            'messages' => [
                ['role' => 'system', 'content' => $this->system],
                [
                    'role' => 'user',
                    'content' => is_callable($this->prompt) ? call_user_func($this->prompt) : $this->prompt,
                ],
            ],
        ];
    }
}
