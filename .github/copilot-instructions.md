# Laravel Tips bot

## Project Overview

This is a Laravel-based bot that generates and distributes Laravel tips using AWS Bedrock (via revolution/laravel-amazon-bedrock package). The bot sends tips to multiple channels including Discord webhooks, Nostr protocol, and HTTP endpoints.

## Architecture

### Core Components

- **Chat System**: AWS Bedrock integration using revolution/laravel-amazon-bedrock package (default model: `global.anthropic.claude-sonnet-4-5-20250929-v1:0`)
- **Console Commands** (`app/Console/Commands/`):
    - `ChatTipsCommand`: Bedrock tip generation (`php artisan chat:tips`)
    - `ReleaseCommand`: Bedrock release summaries (`php artisan chat:release`)
    - `NostrProfile`: Nostr protocol profile management (`php artisan nostr:profile`)
    - `GenerateKeysCommand`: Key generation utilities (`php artisan nostr:generate-keys`)
- **Notifications System** (`app/Notifications/`):
    - `TipsNotification`: Multi-channel tip distribution
    - `ReleaseNotification`: Release announcements
    - Custom HTTP channel for API endpoints

### Configuration

- **AWS Bedrock**: Configured via `config/bedrock.php`
- **Nostr**: Protocol configuration in `config/nostr.php`
- **Tips**: API token configuration in `config/tips.php`
- **Services**: Discord webhook configuration in `config/services.php`

## Development Commands

### Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
```

### Testing
```bash
vendor/bin/phpunit                    # Run all tests
vendor/bin/phpunit tests/Feature/     # Run feature tests only
vendor/bin/phpunit tests/Unit/        # Run unit tests only
```

### Code Quality
```bash
vendor/bin/pint                     # Run Laravel Pint linter (dry run)
vendor/bin/pint --test       # Test code style without fixing
```

### Bot Operations
```bash
# AWS Bedrock commands  
php artisan chat:tips                 # Generate tips using AWS Bedrock
php artisan chat:release              # Generate release summaries using AWS Bedrock
```

## Key Features

- **Multi-language Support**: Tips can be generated in English or Japanese (5/10 probability for Japanese)
- **Multiple Distribution Channels**: Discord, Nostr protocol, and HTTP endpoints
- **AWS Bedrock Integration**: Uses revolution/laravel-amazon-bedrock package with Claude Sonnet models
- **Automated Testing**: PHPUnit test suite with feature and unit tests
- **GitHub Actions**: Automated testing and scheduled tip generation

## Environment Variables Required

### AWS Bedrock Configuration
- `AWS_BEDROCK_MODEL`: Bedrock model ID (default: `global.anthropic.claude-sonnet-4-5-20250929-v1:0`)
- `AWS_BEDROCK_API_KEY`: AWS access key for Bedrock
- `AWS_DEFAULT_REGION`: AWS region (default: us-east-1)

### Required for All Commands
- `TIPS_API_TOKEN`: API token for HTTP endpoint notifications
- `DISCORD_WEBHOOK`: Discord webhook URL for notifications
- `NOSTR_SK`: Nostr private key for Nostr protocol notifications

### GitHub Actions Repository Variables
- `AWS_BEDROCK_MODEL`: Bedrock model ID to use (change this to use a new model)

## Testing Strategy

Tests are organized into Feature and Unit test suites:

### Feature Tests
- `tests/Feature/ChatTest.php`: AWS Bedrock chat functionality testing
- `tests/Feature/ReleaseTest.php`: AWS Bedrock release functionality testing
- `tests/Feature/NostrTest.php`: Nostr protocol integration

### Unit Tests
- `tests/Unit/ExampleTest.php`: Basic unit test examples
