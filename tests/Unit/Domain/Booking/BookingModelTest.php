<?php

declare(strict_types=1);

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Models\Payment;
use App\Domain\Booking\Models\RoomStatusAudit;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates booking with UUID, integer amounts, and relations', function () {
    $owner = User::factory()->create();
    $tenant = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Cantik Sekaran',
        'slug' => 'kost-cantik-sekaran',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Test',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar 101',
        'price_monthly' => 800000,
        'price_deposit' => 200000,
    ]);

    $booking = Booking::create([
        'booking_code' => 'BK-20260901-0001',
        'user_id' => $tenant->id,
        'listing_id' => $listing->id,
        'room_id' => $room->id,
        'tenant_name' => 'Siti Nurhaliza',
        'tenant_phone' => '081234567890',
        'start_date' => '2026-10-01',
        'duration_months' => 3,
        'end_date' => '2027-01-01',
        'total_amount' => 2400000,
        'deposit_amount' => 200000,
        'grand_total' => 2600000,
        'status' => BookingStatus::PENDING,
        'payment_status' => PaymentStatus::PENDING,
        'expired_at' => now()->addDay(),
    ]);

    expect($booking->id)->toBeString()
        ->and($booking->booking_code)->toBe('BK-20260901-0001')
        ->and($booking->status)->toBe(BookingStatus::PENDING)
        ->and($booking->payment_status)->toBe(PaymentStatus::PENDING)
        ->and($booking->total_amount)->toBe(2400000)
        ->and($booking->formatted_total_amount)->toBe('Rp 2.400.000')
        ->and($booking->grand_total)->toBe(2600000)
        ->and($booking->formatted_grand_total)->toBe('Rp 2.600.000')
        ->and($booking->user->id)->toBe($tenant->id)
        ->and($booking->listing->id)->toBe($listing->id)
        ->and($booking->room->id)->toBe($room->id);
});

test('it handles payment and audit relations', function () {
    $owner = User::factory()->create();
    $tenant = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Hebat',
        'slug' => 'kost-hebat',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Test',
        'address' => 'Jl. Banaran',
        'area' => 'Banaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Standar',
        'price_monthly' => 600000,
    ]);

    $booking = Booking::create([
        'booking_code' => 'BK-20260901-0002',
        'user_id' => $tenant->id,
        'listing_id' => $listing->id,
        'room_id' => $room->id,
        'tenant_name' => 'Budi Santoso',
        'tenant_phone' => '08987654321',
        'start_date' => '2026-10-01',
        'duration_months' => 1,
        'end_date' => '2026-11-01',
        'total_amount' => 600000,
        'deposit_amount' => 0,
        'grand_total' => 600000,
        'expired_at' => now()->addDay(),
    ]);

    $payment = Payment::create([
        'booking_id' => $booking->id,
        'invoice_number' => 'INV-20260901-0001',
        'payment_channel' => 'bca_va',
        'payment_reference' => '889900112233',
        'gross_amount' => 600000,
        'transaction_status' => 'pending',
    ]);

    $audit = RoomStatusAudit::create([
        'room_id' => $room->id,
        'booking_id' => $booking->id,
        'user_id' => $tenant->id,
        'previous_status' => 'available',
        'new_status' => 'available',
        'previous_available_count' => 2,
        'new_available_count' => 1,
        'reason' => 'Booking created',
    ]);

    expect($booking->payments)->toHaveCount(1)
        ->and($booking->payments->first()->invoice_number)->toBe('INV-20260901-0001')
        ->and($booking->roomStatusAudits)->toHaveCount(1)
        ->and($booking->roomStatusAudits->first()->reason)->toBe('Booking created')
        ->and($payment->booking->id)->toBe($booking->id)
        ->and($audit->room->id)->toBe($room->id);
});
