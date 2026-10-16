<?php

declare(strict_types=1);

namespace App\Domain\Payment\Exceptions;

use RuntimeException;

class PayoutNotAllowedException extends RuntimeException
{
    public static function unverifiedPrerequisites(): self
    {
        return new self('Payout cannot be approved: both tenant and owner must be contacted and confirmed by admin.');
    }

    public static function paymentNotPaid(): self
    {
        return new self('Payout cannot be created for an unpaid booking transaction.');
    }

    public static function alreadyPaid(): self
    {
        return new self('Payout has already been executed and paid to owner.');
    }

    public static function unauthorized(): self
    {
        return new self('Only authorized administrators can approve and release payouts.');
    }
}
