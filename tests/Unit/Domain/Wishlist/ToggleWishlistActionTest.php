<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Wishlist;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Wishlist\Actions\ToggleWishlistAction;
use App\Domain\Wishlist\Models\Wishlist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToggleWishlistActionTest extends TestCase
{
    use RefreshDatabase;

    private ToggleWishlistAction $action;

    private User $user;

    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ToggleWishlistAction;

        $this->user = User::query()->create([
            'name' => 'User Test',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $owner = User::query()->create([
            'name' => 'Owner Test',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->listing = Listing::query()->create([
            'owner_id' => $owner->id,
            'name' => 'Kost Banaran',
            'slug' => 'kost-banaran',
            'area' => 'Banaran',
            'gender_type' => GenderType::PUTRA,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Banaran No. 2',
            'description' => 'Kost oke',
        ]);
    }

    public function test_it_toggles_wishlist_state(): void
    {
        // 1st toggle -> Should add to wishlist (returns true)
        $added = $this->action->execute($this->user->id, $this->listing->id);
        $this->assertTrue($added);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
        ]);

        // 2nd toggle -> Should remove from wishlist (returns false)
        $removed = $this->action->execute($this->user->id, $this->listing->id);
        $this->assertFalse($removed);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
        ]);
    }
}
