<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\Notification\Notifications\BookingStatusChangedNotification;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $tenant;

    private User $owner;

    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->tenant = User::query()->create([
            'name' => 'Tenant Test',
            'email' => 'tenant@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->tenant->assignRole(UserRole::TENANT->value);

        $this->owner = User::query()->create([
            'name' => 'Owner Test',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->owner->assignRole(UserRole::OWNER->value);

        $listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Banaran',
            'slug' => 'kost-banaran',
            'area' => 'Banaran',
            'gender_type' => GenderType::PUTRA,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Banaran',
            'description' => 'Desc',
        ]);

        $room = Room::query()->create([
            'listing_id' => $listing->id,
            'name' => 'Kamar 1',
            'size' => '3x3',
            'price_monthly' => 800000,
            'price_deposit' => 100000,
            'total_rooms' => 2,
            'available_rooms' => 2,
            'status' => RoomStatus::AVAILABLE,
        ]);

        $this->booking = Booking::query()->create([
            'booking_code' => 'BK-TEST-001',
            'user_id' => $this->tenant->id,
            'listing_id' => $listing->id,
            'room_id' => $room->id,
            'tenant_name' => $this->tenant->name,
            'tenant_phone' => '08123456789',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(37)->toDateString(),
            'duration_months' => 1,
            'total_amount' => 800000,
            'deposit_amount' => 100000,
            'grand_total' => 900000,
            'expired_at' => now()->addHours(24),
            'status' => BookingStatus::PENDING,
        ]);
    }

    public function test_notifications_can_be_retrieved_and_marked_as_read(): void
    {
        // 1. Send Notification
        $this->tenant->notify(new BookingStatusChangedNotification($this->booking, BookingStatus::APPROVED));

        $this->assertSame(1, $this->tenant->unreadNotifications()->count());

        // 2. Fetch via API
        $response = $this->actingAs($this->tenant)->getJson('/notifications');
        $response->assertStatus(200);
        $response->assertJsonPath('unread_count', 1);

        $notificationId = $this->tenant->unreadNotifications()->first()->id;

        // 3. Mark Single as Read
        $readResponse = $this->actingAs($this->tenant)->post("/notifications/{$notificationId}/read");
        $readResponse->assertRedirect();

        $this->assertSame(0, $this->tenant->fresh()->unreadNotifications()->count());

        // 4. Mark All as Read
        $this->tenant->notify(new BookingStatusChangedNotification($this->booking, BookingStatus::COMPLETED));
        $this->assertSame(1, $this->tenant->unreadNotifications()->count());

        $readAllResponse = $this->actingAs($this->tenant)->post('/notifications/read-all');
        $readAllResponse->assertRedirect();

        $this->assertSame(0, $this->tenant->fresh()->unreadNotifications()->count());
    }
}
