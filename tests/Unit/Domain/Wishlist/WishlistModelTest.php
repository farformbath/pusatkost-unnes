<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Wishlist;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Wishlist\Models\Wishlist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_wishlist_with_uuid_and_relations(): void
    {
        $user = User::query()->create([
            'name' => 'User Mahasiswa',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $owner = User::query()->create([
            'name' => 'Owner Kos',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);

        $listing = Listing::query()->create([
            'owner_id' => $owner->id,
            'name' => 'Kost Favorit Sekaran',
            'slug' => 'kost-favorit-sekaran',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRA,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran Raya No. 5',
            'description' => 'Kost favorit mahasiswa',
        ]);

        $wishlist = Wishlist::query()->create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
        ]);

        $this->assertNotEmpty($wishlist->id);
        $this->assertSame($user->id, $wishlist->user->id);
        $this->assertSame($listing->id, $wishlist->listing->id);
    }
}
