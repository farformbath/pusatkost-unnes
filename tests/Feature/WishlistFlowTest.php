<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WishlistFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function test_user_can_toggle_wishlist_and_view_wishlist_page(): void
    {
        // 1. Toggle Add
        $response = $this->actingAs($this->user)
            ->post('/wishlist/toggle', [
                'listing_id' => $this->listing->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
        ]);

        // 2. View Wishlist Index Page
        $viewResponse = $this->actingAs($this->user)->get('/wishlist');
        $viewResponse->assertStatus(200);
        $viewResponse->assertInertia(fn (Assert $page) => $page
            ->component('Wishlist/Index')
            ->has('wishlists.data', 1)
        );

        // 3. Toggle Remove
        $removeResponse = $this->actingAs($this->user)
            ->post('/wishlist/toggle', [
                'listing_id' => $this->listing->id,
            ]);

        $removeResponse->assertRedirect();
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
        ]);
    }
}
