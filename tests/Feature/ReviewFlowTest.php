<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Review\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $owner;

    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->owner = User::query()->create([
            'name' => 'Owner Kos',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Melati Sekaran',
            'slug' => 'kost-melati-sekaran',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Taman Siswa No. 1',
            'description' => 'Kost nyaman',
        ]);
    }

    public function test_user_can_submit_review_for_a_listing(): void
    {
        $response = $this->actingAs($this->user)
            ->post("/kos/{$this->listing->slug}/reviews", [
                'rating' => 5,
                'comment' => 'Kos sangat nyaman dan bersih, dekat dengan gerbang UNNES.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
            'rating' => 5,
            'comment' => 'Kos sangat nyaman dan bersih, dekat dengan gerbang UNNES.',
        ]);

        $this->listing->refresh();
        $this->assertSame(1, $this->listing->review_count);
        $this->assertSame(5.0, (float) $this->listing->rating_avg);
    }

    public function test_owner_can_reply_to_a_review(): void
    {
        $review = Review::query()->create([
            'user_id' => $this->user->id,
            'listing_id' => $this->listing->id,
            'rating' => 5,
            'comment' => 'Pelayanan ramah sekali.',
        ]);

        $response = $this->actingAs($this->owner)
            ->post("/reviews/{$review->id}/reply", [
                'reply' => 'Sama-sama kak, semoga betah!',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'owner_reply' => 'Sama-sama kak, semoga betah!',
        ]);
    }
}
