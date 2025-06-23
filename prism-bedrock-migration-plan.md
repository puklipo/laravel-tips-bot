# Prism + AWS Bedrock Migration Plan

## Overview

This document outlines the migration plan from direct OpenAI integration to Prism PHP library with AWS Bedrock. The approach maintains both OpenAI and Prism implementations side-by-side, allowing seamless switching via GitHub Actions workflow configuration.

## Current State Analysis

### OpenAI Usage
- Package: `openai-php/laravel` v0.11.0
- Core wrapper: `app/Chat/Prompt.php`
- Commands using OpenAI:
  - `ChatCommand` (`chat:tips`) - Laravel tip generation
  - `ReleaseCommand` (`chat:release`) - Release note summaries
  - `ImageCommand` (`image`) - DALL-E image generation (to be removed)

### GitHub Actions Integration
- `tips.yml` - Runs `php artisan chat:tips` weekly
- `release.yml` - Runs `php artisan chat:release` daily for multiple repositories
- Uses secrets: `OPENAI_API_KEY`, `NOSTR_SK`, `TIPS_API_TOKEN`, `DISCORD_WEBHOOK`

## Migration Strategy

### Phase 1: File Structure Reorganization

#### 1.1 Remove Image Generation
- **Delete**: `app/Console/Commands/ImageCommand.php`
- **Reason**: User specified no image generation needed

#### 1.2 Rename Core Prompt Class
- **Move**: `app/Chat/Prompt.php` → `app/Chat/OpenAIPrompt.php`
- **Update**: Namespace and class name to `OpenAIPrompt`

#### 1.3 Create OpenAI Command Directory
- **Create**: `app/Console/OpenAI/` directory
- **Move**: Existing commands to new location with updated signatures:
  - `ChatCommand.php` → `app/Console/OpenAI/ChatTipsCommand.php`
    - Signature: `chat:tips` → `openai:chat:tips`
  - `ReleaseCommand.php` → `app/Console/OpenAI/ReleaseCommand.php`
    - Signature: `chat:release` → `openai:chat:release`

### Phase 2: Prism Implementation

#### 2.1 Add Prism Dependencies
```json
{
  "require": {
    "prism-php/prism": "^1.0",
    "prism-php/bedrock": "^1.0"
  }
}
```

#### 2.2 Create Prism Prompt Class
- **Create**: `app/Chat/PrismPrompt.php`
- Interface compatible with `OpenAIPrompt` for easy switching
- Support for AWS Bedrock Claude models

#### 2.3 Create Prism Commands
- **Create**: `app/Console/Prism/` directory
- **Implement**: New commands using Prism:
  - `app/Console/Prism/ChatTipsCommand.php`
    - Signature: `prism:chat:tips`
  - `app/Console/Prism/ReleaseCommand.php`
    - Signature: `prism:chat:release`

### Phase 3: Configuration Management

#### 3.1 Environment Variables
```env
# AWS Bedrock Configuration
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
BEDROCK_MODEL=anthropic.claude-3-haiku-20240307-v1:0

# Keep existing OpenAI for fallback
OPENAI_API_KEY=
```

#### 3.2 Laravel Configuration
- **Update**: `config/app.php` for AWS services
- **Create**: `config/bedrock.php` for Bedrock-specific settings
- **Update**: `config/services.php` for AWS credentials

### Phase 4: GitHub Actions Workflow Updates

#### 4.1 Dual Workflow Support
Create environment-based command selection:

```yaml
# Current tips.yml approach
- name: Run tips command
  run: php artisan ${{ env.TIPS_COMMAND }}
  env:
    TIPS_COMMAND: openai:chat:tips  # or prism:chat:tips
    OPENAI_API_KEY: ${{ secrets.OPENAI_API_KEY }}
    AWS_ACCESS_KEY_ID: ${{ secrets.AWS_ACCESS_KEY_ID }}
    AWS_SECRET_ACCESS_KEY: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
```

#### 4.2 Switching Mechanism
- **Method 1**: Update repository secrets to change `TIPS_COMMAND` value
- **Method 2**: Use workflow dispatch with input selection
- **Method 3**: Environment-specific branches with different configs

### Phase 5: Implementation Details

#### 5.1 File Structure After Migration
```
app/
├── Chat/
│   ├── OpenAIPrompt.php      # Renamed from Prompt.php
│   └── PrismPrompt.php       # New Prism implementation
└── Console/
    ├── OpenAI/               # Existing commands moved here
    │   ├── ChatTipsCommand.php    # openai:chat:tips
    │   └── ReleaseCommand.php     # openai:chat:release
    └── Prism/                # New Prism commands
        ├── ChatTipsCommand.php    # prism:chat:tips
        └── ReleaseCommand.php     # prism:chat:release
```

#### 5.2 Command Signature Mapping
| Current | OpenAI Version | Prism Version |
|---------|----------------|---------------|
| `chat:tips` | `openai:chat:tips` | `prism:chat:tips` |
| `chat:release` | `openai:chat:release` | `prism:chat:release` |
| `image` | *Deleted* | *Not implemented* |

#### 5.3 Model Mapping
| OpenAI Model | AWS Bedrock Equivalent |
|--------------|------------------------|
| `o4-mini` | `anthropic.claude-3-haiku-20240307-v1:0` |
| `gpt-4` | `anthropic.claude-3-sonnet-20240229-v1:0` |
| `gpt-4o` | `anthropic.claude-3-opus-20240229-v1:0` |

### Phase 6: Testing Strategy

#### 6.1 Unit Tests
- Update existing OpenAI tests to use `OpenAIPrompt`
- Create parallel Prism tests for `PrismPrompt`
- Mock AWS Bedrock responses

#### 6.2 Integration Tests
- Test both command variants work identically
- Verify notification channels work with both implementations
- Test workflow switching mechanism

#### 6.3 Feature Parity Testing
- Compare output quality between OpenAI and Bedrock
- Validate Japanese/English language switching works
- Ensure tip format consistency

### Phase 7: Deployment & Rollout

#### 7.1 Gradual Migration
1. Deploy both implementations simultaneously
2. Start with test runs using Prism commands manually
3. Switch workflow to use Prism commands
4. Monitor for 1-2 weeks
5. Remove OpenAI dependency if satisfied

#### 7.2 Rollback Strategy
- Keep OpenAI commands and dependency until confident
- Simple workflow change to revert to OpenAI
- Maintain both secrets (OpenAI + AWS) during transition

#### 7.3 Monitoring
- Track response times (OpenAI vs Bedrock)
- Monitor token usage and costs
- Watch for API errors or failures
- Compare content quality metrics

## Implementation Timeline

| Week | Tasks |
|------|-------|
| Week 1 | File reorganization, OpenAI command migration |
| Week 2 | Prism implementation, PrismPrompt class |
| Week 3 | Testing, configuration setup |
| Week 4 | Workflow updates, gradual rollout |

## Risk Assessment

### High Risk
- **AWS Bedrock service availability**: Mitigation: Keep OpenAI fallback
- **Model response format differences**: Mitigation: Thorough testing and format validation

### Medium Risk
- **GitHub Actions secret management**: Mitigation: Document secret requirements clearly
- **Cost implications**: Mitigation: Monitor usage patterns closely

### Low Risk
- **Command signature conflicts**: Mitigation: Clear namespace separation
- **Configuration complexity**: Mitigation: Comprehensive documentation

## Success Criteria

1. ✅ Both OpenAI and Prism commands work identically
2. ✅ GitHub Actions can switch between implementations easily
3. ✅ No disruption to existing notification channels
4. ✅ Maintain or improve response quality
5. ✅ Cost-effective compared to OpenAI
6. ✅ Reliable AWS Bedrock integration

## Post-Migration Considerations

### Cleanup Tasks (After 30 days)
- Remove OpenAI dependency from composer.json
- Delete OpenAI command directory
- Clean up OpenAI-related configuration
- Update documentation

### Optimization Opportunities
- Fine-tune Bedrock model parameters
- Implement response caching if beneficial
- Optimize prompt engineering for Claude models
- Consider cost optimization strategies

## Dependencies

### Required AWS Setup
- AWS account with Bedrock access enabled
- IAM user with Bedrock permissions
- Bedrock model access request (Claude models)

### Required Secrets
```
# GitHub Repository Secrets
AWS_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY
OPENAI_API_KEY (keep during transition)
NOSTR_SK
TIPS_API_TOKEN
DISCORD_WEBHOOK
```

This migration plan ensures a smooth transition while maintaining system reliability and providing clear rollback capabilities.