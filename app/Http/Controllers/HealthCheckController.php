<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class HealthCheckController extends Controller
{
    /**
     * Perform comprehensive system health check for orchestrators and uptime monitors.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $isHealthy = true;
        $services = [];

        // 1. PostgreSQL Database Check
        try {
            $dbStart = microtime(true);
            DB::connection()->getPdo();
            $dbLatency = round((microtime(true) - $dbStart) * 1000, 2);

            $driver = is_string(config('database.default')) ? config('database.default') : 'pgsql';

            $services['database'] = [
                'status' => 'up',
                'latency_ms' => $dbLatency,
                'driver' => $driver,
            ];
        } catch (Throwable $e) {
            $isHealthy = false;
            Log::warning('Health check: database connection failed', ['exception' => $e->getMessage()]);

            $services['database'] = [
                'status' => 'down',
                'error' => 'Database connection failed',
            ];
        }

        // 2. Redis Cache Check
        try {
            $redisStart = microtime(true);
            Redis::connection()->ping();
            $redisLatency = round((microtime(true) - $redisStart) * 1000, 2);

            $services['redis'] = [
                'status' => 'up',
                'latency_ms' => $redisLatency,
            ];
        } catch (Throwable $e) {
            $isHealthy = false;
            Log::warning('Health check: Redis connection failed', ['exception' => $e->getMessage()]);

            $services['redis'] = [
                'status' => 'down',
                'error' => 'Redis connection failed',
            ];
        }

        // 3. Storage Health Check (Write / Read / Delete Probe)
        $probePath = 'health_probes/probe-'.Str::uuid().'.tmp';
        $probeData = 'health-probe-'.(string) hrtime(true);

        try {
            $storageStart = microtime(true);
            $disk = Storage::disk('public');

            // Write probe
            $written = $disk->put($probePath, $probeData);
            if (! $written) {
                throw new \RuntimeException('Storage write probe failed');
            }

            // Read probe
            $readContent = $disk->get($probePath);
            if ($readContent !== $probeData) {
                throw new \RuntimeException('Storage read verification mismatch');
            }

            $storageLatency = round((microtime(true) - $storageStart) * 1000, 2);

            $services['storage'] = [
                'status' => 'up',
                'latency_ms' => $storageLatency,
                'disk' => 'public',
            ];
        } catch (Throwable $e) {
            $isHealthy = false;
            Log::warning('Health check: Storage probe failed', ['exception' => $e->getMessage()]);

            $services['storage'] = [
                'status' => 'down',
                'error' => 'Storage read/write probe failed',
            ];
        } finally {
            // Guarantee cleanup of temporary probe file
            try {
                if (isset($disk) && $disk->exists($probePath)) {
                    $disk->delete($probePath);
                }
            } catch (Throwable) {
                // Silently ignore cleanup errors
            }
        }

        $env = is_string(config('app.env')) ? config('app.env') : 'production';

        $payload = [
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'environment' => $env,
            'timestamp' => now()->toIso8601String(),
            'services' => $services,
            'version' => '3.0.0',
        ];

        return response()->json($payload, $isHealthy ? 200 : 503);
    }
}
