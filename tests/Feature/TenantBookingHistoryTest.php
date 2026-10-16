<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TenantBookingHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $tenant;

    private User $otherTenant;

    private User $owner;

    private Listing $listing;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => UserRole::ADMIN->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => UserRole::OWNER->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => UserRole::TENANT->value, 'guard_name' => 'web']);

        $this->owner = User::factory()->create();
        $this->owner->assignRole(UserRole::OWNER->value);

        $this->tenant = User::factory()->create();
        $this->tenant->assignRole(UserRole::TENANT->value);

        $this->otherTenant = User::factory()->create();
        $this->otherTenant->assignRole(UserRole::TENANT->value);

        $this->listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kos Mahasiswa UNNES',
            'slug' => 'kos-mahasiswa-unnes',
            'description' => 'Deskripsi kos mahasiswa',
            'gender_type' => GenderType::PUTRI,
            'address' => 'Sekaran',
            'area' => 'Sekaran',
            'status' => ListingStatus::PUBLISHED,
        ]);

        $this->room = Room::query()->create([
            'listing_id' => $this->listing->id,
            'name' => 'Kamar Deluxe 01',
            'price_monthly' => 850000,
            'price_deposit' => 200000,
            'status' => RoomStatus::AVAILABLE,
            'total_rooms' => 5,
            'available_rooms' => 4,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_tenant_bookings(): void
    {
        $this->get('/tenant/bookings')->assertRedirect('/login');
    }

    public function test_tenant_can_view_booking_history_dashboard(): void
    {
        Booking::query()->create([
            'booking_code' => 'UNS-TEST-001',
            'user_id' => $this->tenant->id,
            'listing_id' => $this->listing->id,
            'room_id' => $this->room->id,
            'tenant_name' => $this->tenant->name,
            'tenant_phone' => '081234567890',
            'start_date' => now()->toDateString(),
            'duration_months' => 3,
            'end_date' => now()->addMonths(3)->toDateString(),
            'total_amount' => 2550000,
            'deposit_amount' => 200000,
            'grand_total' => 2750000,
            'status' => BookingStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
            'expired_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->tenant)->get('/tenant/bookings');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Tenant/Bookings/Index')
            ->has('bookings.data', 1)
            ->where('counts.all', 1)
            ->where('counts.pending', 1)
        );
    }

    public function test_tenant_can_view_invoice(): void
    {
        $booking = Booking::query()->create([
            'booking_code' => 'UNS-INV-001',
            'user_id' => $this->tenant->id,
            'listing_id' => $this->listing->id,
            'room_id' => $this->room->id,
            'tenant_name' => $this->tenant->name,
            'tenant_phone' => '081234567890',
            'start_date' => now()->toDateString(),
            'duration_months' => 1,
            'end_date' => now()->addMonth()->toDateString(),
            'total_amount' => 850000,
            'deposit_amount' => 200000,
            'grand_total' => 1050000,
            'status' => BookingStatus::APPROVED,
            'payment_status' => PaymentStatus::PAID,
            'expired_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->tenant)->get("/booking/{$booking->id}/invoice");

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Booking/Invoice')
            ->where('booking.id', $booking->id)
            ->where('booking.booking_code', 'UNS-INV-001')
        );
    }

    public function test_other_tenant_cannot_view_unauthorized_invoice(): void
    {
        $booking = Booking::query()->create([
            'booking_code' => 'UNS-INV-002',
            'user_id' => $this->tenant->id,
            'listing_id' => $this->listing->id,
            'room_id' => $this->room->id,
            'tenant_name' => $this->tenant->name,
            'tenant_phone' => '081234567890',
            'start_date' => now()->toDateString(),
            'duration_months' => 1,
            'end_date' => now()->addMonth()->toDateString(),
            'total_amount' => 850000,
            'deposit_amount' => 200000,
            'grand_total' => 1050000,
            'status' => BookingStatus::APPROVED,
            'payment_status' => PaymentStatus::PAID,
            'expired_at' => now()->addHours(24),
        ]);

        $response = $this->actingAs($this->otherTenant)->get("/booking/{$booking->id}/invoice");

        $response->assertStatus(403);
    }
}
