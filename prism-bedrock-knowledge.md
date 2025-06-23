# Prism+Bedrock Knowledge: AWS Bedrock Integration with Laravel

This guide explains how to use Prism PHP with AWS Bedrock to integrate Claude AI models into Laravel applications, specifically documented from the Laravel Tips Bot implementation.

## Overview

Prism+Bedrock provides a seamless way to integrate AWS Bedrock's Claude models into Laravel applications. This implementation demonstrates a dual AI provider architecture where you can switch between OpenAI and AWS Bedrock (via Prism PHP) without changing your application logic.

## What is Prism PHP?

Prism PHP is a unified API client for multiple AI providers including AWS Bedrock, OpenAI, Anthropic, Ollama, and others. It provides a consistent interface across different AI services, making it easy to switch between providers or use multiple providers in the same application.

## Architecture Components

### 1. Core Chat System

The implementation uses a `PrismPrompt` class that acts as a wrapper for AI interactions:

```php
// app/Chat/PrismPrompt.php
class PrismPrompt implements Arrayable
{
    protected string $model = 'us.anthropic.claude-3-7-sonnet-20250219-v1:0';
    
    public function __construct(protected readonly string|Closure $prompt) {}
    
    public function withModel(string $model): static
    public function getPromptContent(): string
    public function getModel(): string
}
```

### 2. Console Commands

Two main command types are implemented:

#### Tips Generation Command (`prism:chat:tips`)
```php
// app/Console/Commands/Prism/ChatTipsCommand.php
$response = Prism::text()
    ->using(Bedrock::KEY, $prompt->getModel())
    ->withPrompt($prompt->getPromptContent())
    ->generate();
```

#### Release Summaries Command (`prism:chat:release`)
```php
// app/Console/Commands/Prism/ReleaseCommand.php
$response = Prism::text()
    ->using(Bedrock::KEY, $prompt->getModel())
    ->withPrompt($prompt->getPromptContent())
    ->generate();
```

### 3. Configuration

Configuration is managed through `config/prism.php`:

```php
'bedrock' => [
    'region' => env('AWS_REGION', 'us-east-2'),
    'use_default_credential_provider' => env('AWS_USE_DEFAULT_CREDENTIAL_PROVIDER', false),
    'api_key' => env('AWS_ACCESS_KEY_ID'),
    'api_secret' => env('AWS_SECRET_ACCESS_KEY'),
    'session_token' => env('AWS_SESSION_TOKEN'), // For temporary credentials
],
```

## Installation & Setup

### 1. Install Dependencies

```bash
composer require prism-php/prism:0.74.1
composer require prism-php/bedrock:^1.0
```

### 2. Environment Configuration

Add these variables to your `.env` file:

```env
# AWS Bedrock Configuration
AWS_ACCESS_KEY_ID=your_aws_access_key
AWS_SECRET_ACCESS_KEY=your_aws_secret_key
AWS_REGION=us-east-2
BEDROCK_MODEL=anthropic.claude-3-haiku-20240307-v1:0

# Optional: For temporary credentials
AWS_SESSION_TOKEN=your_session_token

# Optional: Use default AWS credential chain
AWS_USE_DEFAULT_CREDENTIAL_PROVIDER=false
```

### 3. Publish Configuration

```bash
php artisan vendor:publish --provider="Prism\\ServiceProvider"
```

## Usage Examples

### Basic Text Generation

```php
use Prism\Prism\Prism;
use Prism\Bedrock\Bedrock;
use App\Chat\PrismPrompt;

$prompt = PrismPrompt::make('Generate a Laravel tip about Eloquent relationships');

$response = Prism::text()
    ->using(Bedrock::KEY, $prompt->getModel())
    ->withPrompt($prompt->getPromptContent())
    ->generate();

$content = trim($response->text);
```

### Using Different Models

```php
$prompt = PrismPrompt::make('Your prompt here')
    ->withModel('us.anthropic.claude-3-7-sonnet-20250219-v1:0');

// Available models:
// - anthropic.claude-3-haiku-20240307-v1:0 (fast, cost-effective)
// - anthropic.claude-3-sonnet-20240229-v1:0 (balanced)
// - us.anthropic.claude-3-7-sonnet-20250219-v1:0 (most capable)
```

### Token Usage Tracking

```php
$response = Prism::text()
    ->using(Bedrock::KEY, $model)
    ->withPrompt($prompt)
    ->generate();

$totalTokens = $response->usage->promptTokens + 
               $response->usage->completionTokens + 
               ($response->usage->cacheWriteInputTokens ?? 0) + 
               ($response->usage->cacheReadInputTokens ?? 0) + 
               ($response->usage->thoughtTokens ?? 0);
```

## Advanced Features

### 1. Multi-language Support

The implementation supports probabilistic language selection:

```php
use Illuminate\Support\Lottery;

$lang = Lottery::odds(chances: 5, outOf: 10)
    ->winner(fn () => 'Answer in japanese.')
    ->loser(fn () => 'Answer in english.')
    ->choose();
```

### 2. Notification Integration

Results can be distributed to multiple channels:

```php
use Illuminate\Support\Facades\Notification;
use Revolution\Nostr\Notifications\NostrRoute;

Notification::route('discord-webhook', config('services.discord.webhook'))
    ->route('nostr', NostrRoute::to(sk: config('nostr.keys.sk')))
    ->route('http', config('tips.api_token'))
    ->notify(new TipsNotification($content));
```

### 3. GitHub Actions Integration

Switch between AI providers using environment variables:

```yaml
# .github/workflows/tips.yml
- name: Run tips command
  run: php artisan ${{ env.TIPS_COMMAND }}
  env:
    TIPS_COMMAND: 'prism:chat:tips'  # or 'openai:chat:tips'
    AWS_ACCESS_KEY_ID: ${{ secrets.AWS_ACCESS_KEY_ID }}
    AWS_SECRET_ACCESS_KEY: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
    AWS_REGION: 'us-east-2'
```

## Testing

### 1. Test Setup

```php
use Prism\Prism\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Prism\Prism\ValueObjects\Usage;

public function test_tips(): void
{
    Notification::fake();
    
    $fakeResponse = TextResponseFake::make()
        ->withText('This is a fake Laravel tip about using Eloquent relationships effectively.')
        ->withUsage(new Usage(85, 42));
    
    Prism::fake([$fakeResponse]);
    
    $response = $this->artisan('prism:chat:tips');
    $response->assertSuccessful();
}
```

### 2. Testing Commands

```bash
# Run all tests
vendor/bin/phpunit

# Run Prism-specific tests
vendor/bin/phpunit tests/Feature/Prism/

# Run individual test files
vendor/bin/phpunit tests/Feature/Prism/ChatTest.php
vendor/bin/phpunit tests/Feature/Prism/PromptTest.php
vendor/bin/phpunit tests/Feature/Prism/ReleaseTest.php
```

## Best Practices

### 1. Error Handling

```php
try {
    $response = Prism::text()
        ->using(Bedrock::KEY, $model)
        ->withPrompt($prompt)
        ->generate();
        
    if (blank($response->text)) {
        // Handle empty response
        return;
    }
    
} catch (\Exception $e) {
    Log::error('Prism+Bedrock error: ' . $e->getMessage());
    // Handle error appropriately
}
```

### 2. Model Selection

- **Haiku**: Fast and cost-effective for simple tasks
- **Sonnet**: Balanced performance and cost for most use cases  
- **Opus/Sonnet-7**: Most capable for complex reasoning tasks

### 3. Cost Optimization

- Monitor token usage with the built-in usage tracking
- Use appropriate models for your use case
- Consider caching responses for repeated queries
- Implement rate limiting for high-volume applications

### 4. Security

- Never commit AWS credentials to version control
- Use IAM roles with minimal required permissions
- Consider using temporary credentials for enhanced security
- Implement proper input validation and sanitization

## Troubleshooting

### Common Issues

1. **Authentication Errors**
   - Verify AWS credentials are correctly set
   - Check IAM permissions for Bedrock access
   - Ensure region is correctly configured

2. **Model Access Issues**
   - Verify model is available in your AWS region
   - Check if model access is enabled in Bedrock console
   - Confirm model ID matches exactly

3. **Rate Limiting**
   - Implement exponential backoff
   - Monitor API usage in AWS console
   - Consider request queuing for high-volume scenarios

### Debug Mode

Enable debug logging by setting:

```env
LOG_LEVEL=debug
```

This will log detailed information about Prism requests and responses.

## Migration from OpenAI

If migrating from OpenAI to Prism+Bedrock:

1. **Install Prism packages** (shown above)
2. **Update configuration** with AWS credentials
3. **Switch commands** from `openai:*` to `prism:*`
4. **Update GitHub Actions** environment variables
5. **Test thoroughly** with your specific use cases

## Resources

- [Prism PHP Documentation](https://prism-php.dev/)
- [AWS Bedrock Documentation](https://docs.aws.amazon.com/bedrock/)
- [Laravel Console Commands](https://laravel.com/docs/artisan)
- [AWS SDK for PHP](https://docs.aws.amazon.com/sdk-for-php/)

## Conclusion

Prism+Bedrock integration provides a robust foundation for AI-powered Laravel applications. The dual provider architecture demonstrated in this Laravel Tips Bot allows for flexible AI provider switching while maintaining consistent application logic. This approach ensures scalability, cost optimization, and the ability to leverage the best features of different AI providers as needed.