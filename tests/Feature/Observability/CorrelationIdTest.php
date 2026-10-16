<?php

declare(strict_types=1);

use Illuminate\Support\Str;

test('every request attaches a unique safe X-Request-Id header to response when header is missing', function (): void {
    $response = $this->get('/health');

    $response->assertHeader('X-Request-Id');
    $requestId = $response->headers->get('X-Request-Id');

    expect($requestId)->toBeString()
        ->not->toBeEmpty()
        ->and(Str::isUuid((string) $requestId))->toBeTrue();
});

test('middleware preserves valid X-Request-Id header', function (): void {
    $customId = 'req-trace-id-abc-12345';

    $response = $this->withHeader('X-Request-Id', $customId)->get('/health');

    $response->assertHeader('X-Request-Id', $customId);
});

test('middleware preserves and normalizes valid X-Correlation-Id header', function (): void {
    $customId = 'corr-trace-998877';

    $response = $this->withHeader('X-Correlation-Id', $customId)->get('/health');

    $response->assertHeader('X-Request-Id', $customId);
});

test('middleware replaces invalid or malicious ID with a safe generated UUID', function (): void {
    $maliciousId = "invalid id with spaces; DROP TABLE users; \n\r";

    $response = $this->withHeader('X-Request-Id', $maliciousId)->get('/health');

    $requestId = $response->headers->get('X-Request-Id');
    expect($requestId)->not->toBe($maliciousId)
        ->and(Str::isUuid((string) $requestId))->toBeTrue();
});

test('middleware replaces oversized ID (> 64 chars) with a safe generated UUID', function (): void {
    $oversizedId = str_repeat('a', 128);

    $response = $this->withHeader('X-Request-Id', $oversizedId)->get('/health');

    $requestId = $response->headers->get('X-Request-Id');
    expect($requestId)->not->toBe($oversizedId)
        ->and(Str::isUuid((string) $requestId))->toBeTrue();
});
