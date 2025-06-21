# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel-based bot that generates and distributes Laravel tips using OpenAI's API. The bot sends tips to multiple channels including Discord webhooks, Nostr protocol, and HTTP endpoints.

## Architecture

### Core Components

- **Chat System** (`app/Chat/Prompt.php`): Wrapper for OpenAI API interactions with configurable models and prompts
- **Console Commands** (`app/Console/Commands/`):
  - `ChatCommand`: Main tip generation command (`php artisan chat:tips`)
  - `NostrProfile`: Nostr protocol profile management (`php artisan nostr:profile`)
  - `GenerateKeysCommand`: Key generation utilities
  - `ReleaseCommand`: Release management
  - `ImageCommand`: Image processing
- **Notifications System** (`app/Notifications/`):
  - `TipsNotification`: Multi-channel tip distribution
  - `ReleaseNotification`: Release announcements
  - Custom HTTP channel for API endpoints

### Configuration

- **OpenAI**: Configured via `config/openai.php` and `OPENAI_API_KEY` environment variable
- **Nostr**: Protocol configuration in `config/nostr.php`
- **Tips**: API token configuration in `config/tips.php`
- **Services**: Discord webhook and other service configurations

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
./vendor/bin/pint                     # Run Laravel Pint linter (dry run)
./vendor/bin/pint --test --ansi       # Test code style without fixing
```

### Bot Operations
```bash
php artisan chat:tips                 # Generate and distribute Laravel tips
php artisan nostr:profile            # Manage Nostr profile
```

### IDE Support
```bash
php artisan ide-helper:generate       # Generate IDE helper files
php artisan ide-helper:models -N      # Generate model helpers
php artisan ide-helper:meta           # Generate meta files
```

## Key Features

- **Multi-language Support**: Tips can be generated in English or Japanese (5/10 probability for Japanese)
- **Multiple Distribution Channels**: Discord, Nostr protocol, and HTTP endpoints
- **OpenAI Integration**: Uses OpenAI's API (currently configured for o4-mini model)
- **Automated Testing**: PHPUnit test suite with feature and unit tests
- **GitHub Actions**: Automated testing, Claude integration, and scheduled tip generation

## Environment Variables Required

- `OPENAI_API_KEY`: OpenAI API authentication
- `TIPS_API_TOKEN`: API token for HTTP endpoint notifications
- Discord webhook URL in services configuration
- Nostr private key configuration

## Testing Strategy

Tests are organized into Feature and Unit test suites. Key test files:
- `tests/Feature/ChatTest.php`: Chat functionality testing
- `tests/Feature/NostrTest.php`: Nostr protocol integration
- `tests/Feature/PromptTest.php`: Prompt generation testing
- `tests/Feature/ReleaseTest.php`: Release functionality