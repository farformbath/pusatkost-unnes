<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;
use Stringable;

/**
 * Money representation and formatting for UNNESKost.
 * Invariant: All monetary values are strictly represented in integer Rupiah units (zero float).
 */
final readonly class Money implements Stringable
{
    /**
     * @param  int  $amount  Amount in exact integer Rupiah
     */
    public function __construct(
        public int $amount
    ) {
        if ($this->amount < 0) {
            throw new InvalidArgumentException("Nilai uang tidak boleh bernilai negatif: {$this->amount}");
        }
    }

    public static function from(int $amount): self
    {
        return new self($amount);
    }

    /**
     * Format integer Rupiah into standard human-readable string.
     * Example: 850000 -> "Rp 850.000"
     */
    public static function format(int $amount): string
    {
        return (new self($amount))->toRupiah();
    }

    public function toRupiah(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }

    public function toInteger(): int
    {
        return $this->amount;
    }

    public function __toString(): string
    {
        return $this->toRupiah();
    }
}
