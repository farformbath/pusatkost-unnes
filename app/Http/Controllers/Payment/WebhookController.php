<?php

declare(strict_types=1);

namespace App\Http\Controllers\Payment;

use App\Domain\Payment\Actions\VerifyPaymentAction;
use App\Domain\Payment\Exceptions\PaymentVerificationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __invoke(Request $request, string $provider, VerifyPaymentAction $verifyPaymentAction): JsonResponse
    {
        $correlationId = $request->header('X-Request-Id', $request->header('X-Correlation-Id', 'unknown'));
        $signature = $request->header('X-Callback-Signature', $request->header('X-Signature'));

        Log::info("Payment webhook received for provider: {$provider}", [
            'correlation_id' => $correlationId,
            'provider' => $provider,
            'ip' => $request->ip(),
        ]);

        try {
            /** @var array<string, mixed> $payload */
            $payload = $request->all();
            $payment = $verifyPaymentAction->execute($payload, is_string($signature) ? $signature : null);

            return response()->json([
                'success' => true,
                'message' => 'Payment webhook processed successfully.',
                'data' => [
                    'payment_id' => $payment->id,
                    'invoice_number' => $payment->invoice_number,
                    'status' => $payment->transaction_status->value,
                ],
            ]);
        } catch (PaymentVerificationException $e) {
            Log::warning("Payment webhook verification failed: {$e->getMessage()}", [
                'correlation_id' => $correlationId,
                'provider' => $provider,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            Log::error("Unexpected error in payment webhook: {$e->getMessage()}", [
                'correlation_id' => $correlationId,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error processing payment webhook.',
            ], 500);
        }
    }
}
