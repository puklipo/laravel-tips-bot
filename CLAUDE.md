# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel-based bot that generates and distributes Laravel tips using either OpenAI's API or AWS Bedrock (via Prism PHP). The bot supports dual implementations for easy switching between AI providers and sends tips to multiple channels including Discord webhooks, Nostr protocol, and HTTP endpoints.

## Architecture

### Core Components

- **Chat System**: Dual implementation supporting both OpenAI and Prism+Bedrock
  - `app/Chat/OpenAIPrompt.php`: OpenAI API interactions (default model: o4-mini)
  - `app/Chat/PrismPrompt.php`: Prism+Bedrock integration for AWS Claude models (default model: us.anthropic.claude-3-7-sonnet-20250219-v1:0)
- **Console Commands**: 
  - **OpenAI Commands** (`app/Console/Commands/OpenAI/`):
    - `ChatTipsCommand`: OpenAI tip generation (`php artisan openai:chat:tips`)
    - `ReleaseCommand`: OpenAI release summaries (`php artisan openai:chat:release`)
  - **Prism+Bedrock Commands** (`app/Console/Commands/Prism/`):
    - `ChatTipsCommand`: Bedrock tip generation (`php artisan prism:chat:tips`)
    - `ReleaseCommand`: Bedrock release summaries (`php artisan prism:chat:release`)
  - **Other Commands**:
    - `NostrProfile`: Nostr protocol profile management (`php artisan nostr:profile`)
    - `GenerateKeysCommand`: Key generation utilities
- **Notifications System** (`app/Notifications/`):
  - `TipsNotification`: Multi-channel tip distribution
  - `ReleaseNotification`: Release announcements
  - Custom HTTP channel for API endpoints

### Configuration

- **OpenAI**: Configured via `config/openai.php` and `OPENAI_API_KEY` environment variable
- **Prism+Bedrock**: Configured via `config/prism.php` with support for multiple AI providers including AWS Bedrock
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
./vendor/bin/pint                     # Run Laravel Pint linter (dry run)
./vendor/bin/pint --test --ansi       # Test code style without fixing
```

### Bot Operations
```bash
# OpenAI-specific commands
php artisan openai:chat:tips          # Generate tips using OpenAI
php artisan openai:chat:release       # Generate release summaries using OpenAI

# Prism+Bedrock commands  
php artisan prism:chat:tips           # Generate tips using AWS Bedrock via Prism
php artisan prism:chat:release        # Generate release summaries using AWS Bedrock via Prism

# Other commands
php artisan nostr:profile             # Manage Nostr profile
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
- **Dual AI Provider Support**: 
  - **OpenAI Integration**: Uses OpenAI's API with o4-mini model by default
  - **AWS Bedrock Integration**: Uses Prism PHP library with Claude Sonnet models via AWS Bedrock
- **Seamless Switching**: GitHub Actions workflows support switching between OpenAI and Bedrock via repository variables
- **Provider-Specific Commands**: Clear namespace separation between OpenAI and Prism implementations
- **Automated Testing**: PHPUnit test suite with feature and unit tests
- **GitHub Actions**: Automated testing, Claude integration, and scheduled tip generation

## Environment Variables Required

### OpenAI Configuration (Optional - for OpenAI commands)
- `OPENAI_API_KEY`: OpenAI API authentication

### AWS Bedrock Configuration (Optional - for Prism commands)
- `AWS_ACCESS_KEY_ID`: AWS access key for Bedrock
- `AWS_SECRET_ACCESS_KEY`: AWS secret key for Bedrock  
- `AWS_DEFAULT_REGION`: AWS region (default: us-east-1)
- `BEDROCK_MODEL`: Bedrock model ID (default: anthropic.claude-3-haiku-20240307-v1:0)
- `AWS_USE_PATH_STYLE_ENDPOINT`: AWS path style endpoint setting (default: false)

### Required for All Commands
- `TIPS_API_TOKEN`: API token for HTTP endpoint notifications
- `DISCORD_WEBHOOK`: Discord webhook URL for notifications
- `NOSTR_SK`: Nostr private key for Nostr protocol notifications

### GitHub Actions Repository Variables (for switching between providers)
- `TIPS_COMMAND`: Command to run for tips (default: `chat:tips`, options: `openai:chat:tips`, `prism:chat:tips`)
- `RELEASE_COMMAND`: Command to run for releases (default: `chat:release`, options: `openai:chat:release`, `prism:chat:release`)

## Testing Strategy

Tests are organized into Feature and Unit test suites with provider-specific organization:

### Feature Tests
- **OpenAI Tests** (`tests/Feature/OpenAI/`):
  - `ChatTest.php`: OpenAI chat functionality testing
  - `PromptTest.php`: OpenAI prompt generation testing  
  - `ReleaseTest.php`: OpenAI release functionality testing
- **Prism Tests** (`tests/Feature/Prism/`):
  - `ChatTest.php`: Prism+Bedrock chat functionality testing
  - `PromptTest.php`: Prism prompt generation testing
  - `ReleaseTest.php`: Prism release functionality testing
- **General Tests**:
  - `tests/Feature/NostrTest.php`: Nostr protocol integration

### Unit Tests
- `tests/Unit/ExampleTest.php`: Basic unit test examples