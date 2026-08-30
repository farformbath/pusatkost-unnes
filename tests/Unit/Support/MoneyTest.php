<?php

declare(strict_types=1);

use App\Support\Money;

test('it accepts valid positive integer values for Rupiah', function () {
    $money = new Money(850000);

    expect($money->amount)->toBe(850000)
        ->and($money->toInteger())->toBe(850000);
});

test('it accepts zero value for free or zero-deposit scenarios', function () {
    $money = Money::from(0);

    expect($money->amount)->toBe(0)
        ->and($money->toRupiah())->toBe('Rp 0');
});

test('it formats integer amount into human readable Rupiah string', function () {
    expect(Money::format(150000))->toBe('Rp 150.000')
        ->and(Money::format(1250000))->toBe('Rp 1.250.000')
        ->and((string) Money::from(750000))->toBe('Rp 750.000');
});

test('it rejects negative monetary values by throwing InvalidArgumentException', function () {
    expect(fn () => new Money(-50000))
        ->toThrow(InvalidArgumentException::class, 'Nilai uang tidak boleh bernilai negatif');
});
