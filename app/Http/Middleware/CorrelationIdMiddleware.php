<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CorrelationIdMiddleware
{
    /**
     * Handle an incoming request and attach validated correlation ID to logger context and response header.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rawId = $request->header('X-Request-Id') ?? $request->header('X-Correlation-Id');

        // Validate correlation ID: must be alphanumeric with hyphens/underscores, between 8 and 64 chars
        if (is_string($rawId) && preg_match('/^[a-zA-Z0-9\-_]{8,64}$/', $rawId) === 1) {
            $correlationId = $rawId;
        } else {
            $correlationId = (string) Str::uuid();
        }

        // 1. Contextual Structured Logging
        Log::withContext([
            'correlation_id' => $correlationId,
        ]);

        // 2. Process Request
        $response = $next($request);

        // 3. Attach X-Request-Id to Response Headers
        $response->headers->set('X-Request-Id', $correlationId);

        return $response;
    }
}
