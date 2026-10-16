<?php

declare(strict_types=1);

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus as BookingPaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\Payment\Actions\CreatePaymentAction;
use App\Domain\Payment\DTOs\PaymentData;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentReview;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');

    $this->tenant = User::factory()->create();
    $this->tenant->assignRole('tenant');

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');

    $this->listing = Listing::create([
        'owner_id' => $this->owner->id,
        'name' => 'Kost Asri Sekaran',
        'slug' => 'kost-asri-sekaran',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Dekat Gerbang Utama UNNES',
        'address' => 'Jl. Taman Siswa No. 12',
        'area' => 'Sekaran',
    ]);

    $this->room = Room::create([
        'listing_id' => $this->listing->id,
        'name' => 'Kamar 101',
        'price_monthly' => 850000,
        'price_deposit' => 200000,
        'total_rooms' => 5,
        'available_rooms' => 5,
        'status' => RoomStatus::AVAILABLE,
    ]);

    $this->booking = Booking::create([
        'booking_code' => 'BK-'.strtoupper(uniqid()),
        'user_id' => $this->tenant->id,
        'listing_id' => $this->listing->id,
        'room_id' => $this->room->id,
        'tenant_name' => 'Budi Santoso',
        'tenant_phone' => '081234567890',
        'start_date' => now()->toDateString(),
        'duration_months' => 1,
        'end_date' => now()->addMonth()->toDateString(),
        'total_amount' => 850000,
        'deposit_amount' => 200000,
        'grand_total' => 1050000,
        'status' => BookingStatus::PENDING,
        'payment_status' => BookingPaymentStatus::PENDING,
        'expired_at' => now()->addDay(),
    ]);
});

test('create payment action is idempotent', function () {
    $action = app(CreatePaymentAction::class);

    $dto = new PaymentData(
        bookingId: $this->booking->id,
        paymentChannel: 'bca_va',
        paymentProvider: 'sandbox',
        idempotencyKey: 'IDEM-TEST-12345'
    );

    $payment1 = $action->execute($this->booking, $dto);
    $payment2 = $action->execute($this->booking, $dto);

    expect($payment1->id)->toBe($payment2->id)
        ->and(Payment::where('idempotency_key', 'IDEM-TEST-12345')->count())->toBe(1);
});

test('webhook verifies valid payment and transitions status to PAID and creates review', function () {
    Notification::fake();

    $payment = Payment::create([
        'booking_id' => $this->booking->id,
        'invoice_number' => 'INV-20260902-0001',
        'payment_provider' => 'sandbox',
        'payment_channel' => 'bca_va',
        'gross_amount' => 1050000,
        'transaction_status' => PaymentStatus::PENDING,
        'idempotency_key' => 'IDEM-HOOK-001',
    ]);

    $payload = [
        'order_id' => $payment->invoice_number,
        'gross_amount' => 1050000,
        'transaction_status' => 'settlement',
        'transaction_id' => 'TRX-SB-998877',
        'payment_type' => 'bca_va',
    ];

    $response = $this->postJson('/webhooks/payment/sandbox', $payload);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => [
                'status' => 'paid',
            ],
        ]);

    $payment->refresh();
    $this->booking->refresh();

    expect($payment->transaction_status)->toBe(PaymentStatus::PAID)
        ->and($payment->verified_at)->not->toBeNull()
        ->and($payment->provider_transaction_id)->toBe('TRX-SB-998877')
        ->and($this->booking->payment_status)->toBe(BookingPaymentStatus::PAID)
        ->and($this->booking->status)->toBe(BookingStatus::APPROVED);

    // Verify PaymentReview is created
    $review = PaymentReview::where('payment_id', $payment->id)->first();
    expect($review)->not->toBeNull()
        ->and($review->status)->toBe(PaymentReviewStatus::PENDING)
        ->and($review->tenant_contacted)->toBeFalse();
});

test('webhook handles idempotency safely on duplicate callbacks', function () {
    $payment = Payment::create([
        'booking_id' => $this->booking->id,
        'invoice_number' => 'INV-20260902-0002',
        'payment_provider' => 'sandbox',
        'payment_channel' => 'bca_va',
        'gross_amount' => 1050000,
        'transaction_status' => PaymentStatus::PAID,
        'verified_at' => now(),
    ]);

    $payload = [
        'order_id' => $payment->invoice_number,
        'gross_amount' => 1050000,
        'transaction_status' => 'settlement',
        'transaction_id' => 'TRX-SB-DUPLICATE',
    ];

    $response = $this->postJson('/webhooks/payment/sandbox', $payload);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);
});

test('admin can view payment list and payment show page', function () {
    $payment = Payment::create([
        'booking_id' => $this->booking->id,
        'invoice_number' => 'INV-20260902-0003',
        'payment_provider' => 'sandbox',
        'payment_channel' => 'bca_va',
        'gross_amount' => 1050000,
        'transaction_status' => PaymentStatus::PAID,
        'verified_at' => now(),
    ]);

    PaymentReview::create([
        'payment_id' => $payment->id,
        'booking_id' => $this->booking->id,
        'status' => PaymentReviewStatus::PENDING,
        'tenant_contacted' => false,
        'owner_contacted' => false,
        'tenant_confirmed' => false,
        'owner_confirmed' => false,
    ]);

    // As Admin
    $response = $this->actingAs($this->admin)->get('/admin/payments');
    $response->assertOk();

    $showResponse = $this->actingAs($this->admin)->get("/admin/payments/{$payment->id}");
    $showResponse->assertOk();

    // Opening show page transitions PENDING review to CONTACTING
    $review = PaymentReview::where('payment_id', $payment->id)->first();
    expect($review->status)->toBe(PaymentReviewStatus::CONTACTING);
});

test('non-admin cannot access admin payment review page', function () {
    $payment = Payment::create([
        'booking_id' => $this->booking->id,
        'invoice_number' => 'INV-20260902-0004',
        'payment_provider' => 'sandbox',
        'payment_channel' => 'bca_va',
        'gross_amount' => 1050000,
        'transaction_status' => PaymentStatus::PAID,
    ]);

    // As Tenant
    $this->actingAs($this->tenant)
        ->get('/admin/payments')
        ->assertForbidden();

    // As Owner
    $this->actingAs($this->owner)
        ->get("/admin/payments/{$payment->id}")
        ->assertForbidden();
});

test('admin can record contact checklist and review becomes VERIFIED when all 4 flags true', function () {
    $payment = Payment::create([
        'booking_id' => $this->booking->id,
        'invoice_number' => 'INV-20260902-0005',
        'payment_provider' => 'sandbox',
        'payment_channel' => 'bca_va',
        'gross_amount' => 1050000,
        'transaction_status' => PaymentStatus::PAID,
    ]);

    $review = PaymentReview::create([
        'payment_id' => $payment->id,
        'booking_id' => $this->booking->id,
        'status' => PaymentReviewStatus::CONTACTING,
        'tenant_contacted' => false,
        'owner_contacted' => false,
        'tenant_confirmed' => false,
        'owner_confirmed' => false,
    ]);

    // Partial update
    $this->actingAs($this->admin)
        ->post("/admin/payment-reviews/{$review->id}/contact", [
            'tenant_contacted' => true,
            'owner_contacted' => true,
            'tenant_confirmed' => false,
            'owner_confirmed' => false,
            'admin_notes' => 'Menunggu respon konfirmasi tenant',
        ])
        ->assertRedirect();

    $review->refresh();
    expect($review->status)->toBe(PaymentReviewStatus::CONTACTING)
        ->and($review->tenant_contacted)->toBeTrue()
        ->and($review->tenant_confirmed)->toBeFalse();

    // All 4 flags confirmed
    $this->actingAs($this->admin)
        ->post("/admin/payment-reviews/{$review->id}/contact", [
            'tenant_contacted' => true,
            'owner_contacted' => true,
            'tenant_confirmed' => true,
            'owner_confirmed' => true,
            'admin_notes' => 'Kedua pihak sudah konfirmasi siap huni',
        ])
        ->assertRedirect();

    $review->refresh();
    expect($review->status)->toBe(PaymentReviewStatus::VERIFIED)
        ->and($review->isEligibleForApproval())->toBeTrue();
});
