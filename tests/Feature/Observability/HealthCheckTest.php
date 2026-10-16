<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

test('health check returns 200 and healthy json payload when all services are healthy', function (): void {
    Storage::fake('public');

    Redis::shouldReceive('connection->ping')
        ->andReturn(true);

    $response = $this->getJson('/health');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'environment',
            'timestamp',
            'services' => [
                'database' => ['status', 'latency_ms', 'driver'],
                'redis' => ['status', 'latency_ms'],
                'storage' => ['status', 'latency_ms', 'disk'],
            ],
            'version',
        ]);

    expect($response->json('status'))->toBe('healthy')
        ->and($response->json('services.database.status'))->toBe('up')
        ->and($response->json('services.redis.status'))->toBe('up')
        ->and($response->json('services.storage.status'))->toBe('up')
        ->and($response->json('version'))->toBe('3.0.0');

    // Verify no secret leak
    $content = (string) $response->getContent();
    expect($content)->not->toContain('password')
        ->not->toContain('secret')
        ->not->toContain('postgres://');
});

test('health check returns 503 when database is unavailable', function (): void {
    Storage::fake('public');

    DB::shouldReceive('connection->getPdo')
        ->andThrow(new Exception('Database connection error'));

    $response = $this->getJson('/health');

    $response->assertStatus(503)
        ->assertJson([
            'status' => 'unhealthy',
            'services' => [
                'database' => [
                    'status' => 'down',
                    'error' => 'Database connection failed',
                ],
            ],
        ]);
});

test('health check returns 503 when redis is unavailable', function (): void {
    Storage::fake('public');

    Redis::shouldReceive('connection->ping')
        ->andThrow(new Exception('Redis server unreachable'));

    $response = $this->getJson('/health');

    $response->assertStatus(503)
        ->assertJson([
            'status' => 'unhealthy',
            'services' => [
                'redis' => [
                    'status' => 'down',
                    'error' => 'Redis connection failed',
                ],
            ],
        ]);
});

test('health check returns 503 when storage write probe fails', function (): void {
    Storage::shouldReceive('disk')
        ->with('public')
        ->andThrow(new Exception('Disk permission denied'));

    $response = $this->getJson('/health');

    $response->assertStatus(503)
        ->assertJson([
            'status' => 'unhealthy',
            'services' => [
                'storage' => [
                    'status' => 'down',
                    'error' => 'Storage read/write probe failed',
                ],
            ],
        ]);
});
