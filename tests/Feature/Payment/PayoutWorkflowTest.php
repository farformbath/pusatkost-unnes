<?php

declare(strict_types=1);

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus as BookingPaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Domain\Payment\Enums\PayoutStatus;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentReview;
use App\Domain\Payment\Models\Payout;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');

    $this->otherOwner = User::factory()->create();
    $this->otherOwner->assignRole('owner');

    $this->tenant = User::factory()->create();
    $this->tenant->assignRole('tenant');

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->listing = Listing::create([
        'owner_id' => $this->owner->id,
        'name' => 'Kost Pelangi Sekaran',
        'slug' => 'kost-pelangi-sekaran',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Dekat Fakultas Ilmu Pendidikan UNNES',
        'address' => 'Jl. Gang Cempaka No. 5',
        'area' => 'Sekaran',
    ]);

    $this->room = Room::create([
        'listing_id' => $this->listing->id,
        'name' => 'Kamar Mawar 1',
        'price_monthly' => 1000000,
        'price_deposit' => 250000,
        'total_rooms' => 3,
        'available_rooms' => 3,
        'status' => RoomStatus::AVAILABLE,
    ]);

    $this->booking = Booking::create([
        'booking_code' => 'BK-PAYOUT-'.strtoupper(uniqid()),
        'user_id' => $this->tenant->id,
        'listing_id' => $this->listing->id,
        'room_id' => $this->room->id,
        'tenant_name' => 'Siti Nurhaliza',
        'tenant_phone' => '08987654321',
        'start_date' => now()->toDateString(),
        'duration_months' => 1,
        'end_date' => now()->addMonth()->toDateString(),
        'total_amount' => 1000000,
        'deposit_amount' => 250000,
        'grand_total' => 1250000,
        'status' => BookingStatus::APPROVED,
        'payment_status' => BookingPaymentStatus::PAID,
        'expired_at' => now()->addDay(),
    ]);

    $this->payment = Payment::create([
        'booking_id' => $this->booking->id,
        'invoice_number' => 'INV-20260902-9999',
        'payment_provider' => 'sandbox',
        'payment_channel' => 'bca_va',
        'gross_amount' => 1250000,
        'transaction_status' => PaymentStatus::PAID,
        'paid_at' => now(),
        'verified_at' => now(),
    ]);
});

test('payout approval is blocked if contact verification is incomplete', function () {
    $review = PaymentReview::create([
        'payment_id' => $this->payment->id,
        'booking_id' => $this->booking->id,
        'status' => PaymentReviewStatus::CONTACTING,
        'tenant_contacted' => true,
        'owner_contacted' => false, // Missing owner contact
        'tenant_confirmed' => true,
        'owner_confirmed' => false,
    ]);

    $this->actingAs($this->admin)
        ->post("/admin/payment-reviews/{$review->id}/approve", [
            'platform_fee' => 25000,
        ])
        ->assertForbidden();
});

test('admin can approve payout when fully verified and payout executes cleanly', function () {
    Notification::fake();

    $review = PaymentReview::create([
        'payment_id' => $this->payment->id,
        'booking_id' => $this->booking->id,
        'status' => PaymentReviewStatus::VERIFIED,
        'tenant_contacted' => true,
        'owner_contacted' => true,
        'tenant_confirmed' => true,
        'owner_confirmed' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->post("/admin/payment-reviews/{$review->id}/approve", [
            'platform_fee' => 50000,
            'payout_provider' => 'sandbox',
        ]);

    $response->assertRedirect();

    $review->refresh();
    expect($review->status)->toBe(PaymentReviewStatus::APPROVED)
        ->and($review->approved_at)->not->toBeNull()
        ->and($review->admin_id)->toBe($this->admin->id);

    // Verify Payout record
    $payout = Payout::where('payment_id', $this->payment->id)->first();
    expect($payout)->not->toBeNull()
        ->and($payout->owner_id)->toBe($this->owner->id)
        ->and($payout->gross_amount)->toBe(1250000)
        ->and($payout->platform_fee)->toBe(50000)
        ->and($payout->net_amount)->toBe(1200000) // 1,250,000 - 50,000
        ->and($payout->status)->toBe(PayoutStatus::PAID)
        ->and($payout->completed_at)->not->toBeNull();
});

test('owner can view their payouts and payout receipt', function () {
    $payout = Payout::create([
        'payment_id' => $this->payment->id,
        'booking_id' => $this->booking->id,
        'owner_id' => $this->owner->id,
        'gross_amount' => 1250000,
        'platform_fee' => 50000,
        'net_amount' => 1200000,
        'payout_provider' => 'sandbox',
        'provider_payout_id' => 'PO-SANDBOX-1234',
        'status' => PayoutStatus::PAID,
        'idempotency_key' => 'PO-IDEM-001',
        'completed_at' => now(),
    ]);

    // Owner views index
    $this->actingAs($this->owner)
        ->get('/owner/payouts')
        ->assertOk();

    // Owner views detail
    $this->actingAs($this->owner)
        ->get("/owner/payouts/{$payout->id}")
        ->assertOk();

    // Owner views receipt
    $this->actingAs($this->owner)
        ->get("/owner/payouts/{$payout->id}/receipt")
        ->assertOk();
});

test('cross-owner isolation prevents another owner from viewing someone elses payout or receipt', function () {
    $payout = Payout::create([
        'payment_id' => $this->payment->id,
        'booking_id' => $this->booking->id,
        'owner_id' => $this->owner->id,
        'gross_amount' => 1250000,
        'platform_fee' => 50000,
        'net_amount' => 1200000,
        'payout_provider' => 'sandbox',
        'provider_payout_id' => 'PO-SANDBOX-5555',
        'status' => PayoutStatus::PAID,
        'idempotency_key' => 'PO-IDEM-002',
        'completed_at' => now(),
    ]);

    // Other owner attempts to view detail
    $this->actingAs($this->otherOwner)
        ->get("/owner/payouts/{$payout->id}")
        ->assertForbidden();

    // Other owner attempts to view receipt
    $this->actingAs($this->otherOwner)
        ->get("/owner/payouts/{$payout->id}/receipt")
        ->assertForbidden();
});

test('tenant can view their official payment receipt', function () {
    $this->actingAs($this->tenant)
        ->get("/payment/{$this->payment->id}/receipt")
        ->assertOk();
});
