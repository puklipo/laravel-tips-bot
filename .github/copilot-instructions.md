# Laravel Tips bot

## Project Overview

This is a Laravel-based bot that generates and distributes Laravel tips using GitHub Copilot SDK for Laravel. The bot sends tips to multiple channels including Discord webhooks, Nostr protocol, and HTTP endpoints.

## Architecture

### Core Components

- **AI System**: GitHub Copilot integration using laravel-copilot-sdk package
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
- **GitHub Actions**: Automated testing and scheduled tip generation

## Environment Variables Required

### Required for All Commands
- `TIPS_API_TOKEN`: API token for HTTP endpoint notifications
- `DISCORD_WEBHOOK`: Discord webhook URL for notifications
- `NOSTR_SK`: Nostr private key for Nostr protocol notifications

### GitHub Actions Repository Variables
- `COPILOT_GITHUB_TOKEN`
- `COPILOT_MODEL`: Copilot model ID to use
