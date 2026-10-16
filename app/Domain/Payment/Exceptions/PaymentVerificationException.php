<?php

declare(strict_types=1);

namespace App\Domain\Payment\Exceptions;

use RuntimeException;

class PaymentVerificationException extends RuntimeException
{
    public static function invalidSignature(): self
    {
        return new self('Invalid webhook signature from payment provider.');
    }

    public static function paymentNotFound(string $reference): self
    {
        return new self("Payment transaction not found for reference: {$reference}");
    }

    public static function alreadyProcessed(string $invoice): self
    {
        return new self("Payment {$invoice} has already been verified and processed.");
    }
}
