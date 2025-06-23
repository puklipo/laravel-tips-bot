# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel-based bot that generates and distributes Laravel tips using either OpenAI's API or AWS Bedrock (via Prism PHP). The bot supports dual implementations for easy switching between AI providers and sends tips to multiple channels including Discord webhooks, Nostr protocol, and HTTP endpoints.

## Architecture

### Core Components

- **Chat System**: Dual implementation supporting both OpenAI and Prism+Bedrock
  - `app/Chat/OpenAIPrompt.php`: OpenAI API interactions with configurable models and prompts
  - `app/Chat/PrismPrompt.php`: Prism+Bedrock integration for AWS Claude models
- **Console Commands**: 
  - **Backward Compatible Commands** (`app/Console/Commands/`):
    - `ChatCommand`: Main tip generation command (`php artisan chat:tips`)
    - `ReleaseCommand`: Release management (`php artisan chat:release`)
  - **OpenAI Commands** (`app/Console/OpenAI/`):
    - `ChatTipsCommand`: OpenAI tip generation (`php artisan openai:chat:tips`)
    - `ReleaseCommand`: OpenAI release summaries (`php artisan openai:chat:release`)
  - **Prism+Bedrock Commands** (`app/Console/Prism/`):
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
- **AWS Bedrock**: Configured via `config/bedrock.php` and AWS environment variables
- **Nostr**: Protocol configuration in `config/nostr.php`
- **Tips**: API token configuration in `config/tips.php`
- **Services**: Discord webhook and AWS Bedrock service configurations

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
# Backward compatible commands (use OpenAI by default)
php artisan chat:tips                 # Generate and distribute Laravel tips
php artisan chat:release             # Generate release summaries

# OpenAI-specific commands
php artisan openai:chat:tips          # Generate tips using OpenAI
php artisan openai:chat:release       # Generate release summaries using OpenAI

# Prism+Bedrock commands  
php artisan prism:chat:tips           # Generate tips using AWS Bedrock
php artisan prism:chat:release        # Generate release summaries using AWS Bedrock

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
  - **OpenAI Integration**: Uses OpenAI's API (currently configured for o4-mini model)
  - **AWS Bedrock Integration**: Uses Prism PHP library with Claude models via AWS Bedrock
- **Seamless Switching**: GitHub Actions workflows support switching between OpenAI and Bedrock via repository variables
- **Backward Compatibility**: Original command signatures maintained for easy migration
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

### Required for All Commands
- `TIPS_API_TOKEN`: API token for HTTP endpoint notifications
- `DISCORD_WEBHOOK`: Discord webhook URL for notifications
- `NOSTR_SK`: Nostr private key for Nostr protocol notifications

### GitHub Actions Repository Variables (for switching between providers)
- `TIPS_COMMAND`: Command to run for tips (default: `chat:tips`, options: `openai:chat:tips`, `prism:chat:tips`)
- `RELEASE_COMMAND`: Command to run for releases (default: `chat:release`, options: `openai:chat:release`, `prism:chat:release`)

## Testing Strategy

Tests are organized into Feature and Unit test suites. Key test files:
- `tests/Feature/ChatTest.php`: Chat functionality testing
- `tests/Feature/NostrTest.php`: Nostr protocol integration
- `tests/Feature/PromptTest.php`: Prompt generation testing
- `tests/Feature/ReleaseTest.php`: Release functionality