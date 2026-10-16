<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\Actions\ProcessPaymentAction;
use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
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
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotificationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;

    private User $userB;

    private User $owner;

    private Listing $listing;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->userA = User::create(['name' => 'User A', 'email' => 'userA@example.com', 'password' => bcrypt('password')]);
        $this->userA->assignRole(UserRole::TENANT->value);

        $this->userB = User::create(['name' => 'User B', 'email' => 'userB@example.com', 'password' => bcrypt('password')]);
        $this->userB->assignRole(UserRole::TENANT->value);

        $this->owner = User::create(['name' => 'Owner', 'email' => 'owner@example.com', 'password' => bcrypt('password')]);
        $this->owner->assignRole(UserRole::OWNER->value);

        $this->listing = Listing::create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Cantik',
            'slug' => 'kost-cantik',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran',
            'description' => 'Desc',
        ]);

        $this->room = Room::create([
            'listing_id' => $this->listing->id,
            'name' => 'Kamar 1',
            'size' => '3x3',
            'price_monthly' => 600000,
            'price_deposit' => 100000,
            'total_rooms' => 5,
            'available_rooms' => 5,
            'status' => RoomStatus::AVAILABLE,
        ]);
    }

    public function test_user_a_cannot_read_or_mark_user_b_notification(): void
    {
        $booking = Booking::create([
            'booking_code' => 'BK-B002',
            'user_id' => $this->userB->id,
            'listing_id' => $this->listing->id,
            'room_id' => $this->room->id,
            'tenant_name' => $this->userB->name,
            'tenant_phone' => '08123456789',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(37)->toDateString(),
            'duration_months' => 1,
            'total_amount' => 600000,
            'deposit_amount' => 100000,
            'grand_total' => 700000,
            'expired_at' => now()->addHours(24),
            'status' => BookingStatus::PENDING,
        ]);

        $this->userB->notify(new BookingStatusChangedNotification($booking, BookingStatus::APPROVED));

        $this->assertSame(1, $this->userB->unreadNotifications()->count());
        $this->assertSame(0, $this->userA->unreadNotifications()->count());

        $notificationId = $this->userB->unreadNotifications()->first()->id;

        // User A attempts to mark User B's notification as read
        $response = $this->actingAs($this->userA)->post("/notifications/{$notificationId}/read");
        $response->assertStatus(404);

        // User B's notification is still unread
        $this->assertSame(1, $this->userB->fresh()->unreadNotifications()->count());
    }

    public function test_notifications_are_not_persisted_on_transaction_rollback(): void
    {
        $initialNotifs = DB::table('notifications')->count();

        try {
            DB::transaction(function (): void {
                $action = new CreateBookingAction;
                $action->execute(new BookingData(
                    userId: (int) $this->userA->id,
                    listingId: (string) $this->listing->id,
                    roomId: (string) $this->room->id,
                    tenantName: 'User A',
                    tenantPhone: '08123456789',
                    startDate: now()->addDays(7)->toDateString(),
                    durationMonths: 1,
                ));

                // Simulate unexpected runtime failure
                throw new Exception('Simulated database error');
            });
        } catch (Exception) {
            // caught
        }

        // Zero notifications should be saved
        $this->assertSame($initialNotifs, DB::table('notifications')->count());
    }

    public function test_payment_idempotency_prevents_duplicate_notifications(): void
    {
        $booking = Booking::create([
            'booking_code' => 'BK-PAY-01',
            'user_id' => $this->userA->id,
            'listing_id' => $this->listing->id,
            'room_id' => $this->room->id,
            'tenant_name' => $this->userA->name,
            'tenant_phone' => '08123456789',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(37)->toDateString(),
            'duration_months' => 1,
            'total_amount' => 600000,
            'deposit_amount' => 100000,
            'grand_total' => 700000,
            'expired_at' => now()->addHours(24),
            'status' => BookingStatus::PENDING,
            'payment_status' => PaymentStatus::PENDING,
        ]);

        $action = new ProcessPaymentAction;

        // First payment execution
        $action->execute($booking, 'bca_va');
        $this->assertSame(PaymentStatus::PAID, $booking->fresh()->payment_status);
        $userNotifCount1 = $this->userA->notifications()->count();
        $ownerNotifCount1 = $this->owner->notifications()->count();

        // Second duplicate payment execution (idempotent)
        $action->execute($booking, 'bca_va');
        $userNotifCount2 = $this->userA->notifications()->count();
        $ownerNotifCount2 = $this->owner->notifications()->count();

        $this->assertSame($userNotifCount1, $userNotifCount2);
        $this->assertSame($ownerNotifCount1, $ownerNotifCount2);
    }
}
