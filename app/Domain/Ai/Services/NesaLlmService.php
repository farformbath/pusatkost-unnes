<?php

declare(strict_types=1);

namespace App\Domain\Ai\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class NesaLlmService
{
    public function __construct(
        private readonly NesaToolService $toolService,
    ) {}

    public function getToolService(): NesaToolService
    {
        return $this->toolService;
    }

    /**
     * Sends chat messages to the configured OpenAI-compatible LLM provider with optional tool definitions.
     *
     * @param  list<array{role: string, content: string|null, tool_calls?: list<array<string, mixed>>|null, tool_call_id?: string|null}>  $messages
     * @return array{content: string|null, tool_calls: list<array<string, mixed>>|null, tokens_used: int|null, is_fallback: bool}
     */
    public function sendChat(array $messages, bool $includeTools = true): array
    {
        $rawBaseUrl = config('ai.base_url');
        $baseUrl = is_string($rawBaseUrl) && $rawBaseUrl !== ''
            ? rtrim($rawBaseUrl, '/')
            : 'https://discovery-api.intern-ai.org.cn/v1';

        $rawApiKey = config('ai.api_key');
        $apiKey = is_string($rawApiKey) ? $rawApiKey : '';

        $rawModel = config('ai.model');
        $model = is_string($rawModel) && $rawModel !== '' ? $rawModel : 'intern-latest';

        $rawMaxTokens = config('ai.max_tokens');
        $maxTokens = is_int($rawMaxTokens) ? $rawMaxTokens : 1024;

        $rawTemperature = config('ai.temperature');
        $temperature = is_float($rawTemperature) || is_int($rawTemperature) ? (float) $rawTemperature : 0.7;

        $rawTimeout = config('ai.timeout_seconds');
        $timeoutSeconds = is_int($rawTimeout) ? $rawTimeout : 15;

        /** @var array<string, mixed> $payload */
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ];

        if ($includeTools) {
            $tools = $this->toolService->getToolDeclarations();
            if (! empty($tools)) {
                $payload['tools'] = $tools;
                $payload['tool_choice'] = 'auto';
            }
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout($timeoutSeconds)
                ->post($baseUrl.'/chat/completions', $payload);

            if (! $response->successful()) {
                Log::warning('NesaLlmService received non-200 HTTP response from LLM provider', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'content' => null,
                    'tool_calls' => null,
                    'tokens_used' => 0,
                    'is_fallback' => true,
                ];
            }

            /** @var mixed $data */
            $data = $response->json();

            /** @var list<mixed> $choices */
            $choices = is_array($data) && isset($data['choices']) && is_array($data['choices'])
                ? array_values($data['choices'])
                : [];

            /** @var array<string, mixed> $firstChoice */
            $firstChoice = ! empty($choices) && is_array($choices[0]) ? $choices[0] : [];

            /** @var array<string, mixed> $choice */
            $choice = isset($firstChoice['message']) && is_array($firstChoice['message'])
                ? $firstChoice['message']
                : [];

            /** @var string|null $content */
            $content = isset($choice['content']) && is_string($choice['content']) ? $choice['content'] : null;

            /** @var list<array<string, mixed>>|null $toolCalls */
            $toolCalls = null;
            if (isset($choice['tool_calls']) && is_array($choice['tool_calls'])) {
                /** @var list<array<string, mixed>> $toolCalls */
                $toolCalls = array_values(array_filter($choice['tool_calls'], 'is_array'));
            }

            /** @var array<string, mixed> $usage */
            $usage = is_array($data) && isset($data['usage']) && is_array($data['usage'])
                ? $data['usage']
                : [];

            $tokensUsed = isset($usage['total_tokens']) && (is_int($usage['total_tokens']) || is_numeric($usage['total_tokens']))
                ? (int) $usage['total_tokens']
                : null;

            return [
                'content' => $content,
                'tool_calls' => $toolCalls,
                'tokens_used' => $tokensUsed,
                'is_fallback' => false,
            ];
        } catch (Throwable $e) {
            Log::error('NesaLlmService request failed with exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'content' => null,
                'tool_calls' => null,
                'tokens_used' => 0,
                'is_fallback' => true,
            ];
        }
    }
}
