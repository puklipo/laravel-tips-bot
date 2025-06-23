<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AWS Bedrock Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for AWS Bedrock integration with
    | Prism PHP library. Set up your AWS credentials and preferred models.
    |
    */

    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),

    'access_key' => env('AWS_ACCESS_KEY_ID'),

    'secret_key' => env('AWS_SECRET_ACCESS_KEY'),

    'model' => env('BEDROCK_MODEL', 'anthropic.claude-3-haiku-20240307-v1:0'),

    /*
    |--------------------------------------------------------------------------
    | Model Mappings
    |--------------------------------------------------------------------------
    |
    | Map OpenAI models to their AWS Bedrock equivalents for easier migration
    |
    */

    'model_mappings' => [
        'o4-mini' => 'anthropic.claude-3-haiku-20240307-v1:0',
        'gpt-4' => 'anthropic.claude-3-sonnet-20240229-v1:0',
        'gpt-4o' => 'anthropic.claude-3-opus-20240229-v1:0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Parameters
    |--------------------------------------------------------------------------
    |
    | Default parameters for different models
    |
    */

    'parameters' => [
        'max_tokens' => 4096,
        'temperature' => 0.7,
        'top_p' => 0.9,
    ],

];