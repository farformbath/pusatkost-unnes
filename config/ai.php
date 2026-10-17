<?php

declare(strict_types=1);

return [
    'base_url' => env('AI_BASE_URL', 'https://discovery-api.intern-ai.org.cn/v1'),
    'api_key' => env('AI_API_KEY', ''),
    'model' => env('AI_MODEL', 'deepseek-v4-flash-0731'),
    'max_tokens' => (int) env('AI_MAX_TOKENS', 1024),
    'temperature' => (float) env('AI_TEMPERATURE', 0.7),
    'timeout_seconds' => (int) env('AI_TIMEOUT_SECONDS', 25),
];
