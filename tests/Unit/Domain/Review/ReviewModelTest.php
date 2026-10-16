<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Review;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Review\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_review_with_uuid_and_relations(): void
    {
        $user = User::query()->create([
            'name' => 'Reviewer Mahasiswa',
            'email' => 'reviewer@example.com',
            'password' => bcrypt('password'),
        ]);

        $owner = User::query()->create([
            'name' => 'Owner Kos',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);

        $listing = Listing::query()->create([
            'owner_id' => $owner->id,
            'name' => 'Kost Melati Sekaran',
            'slug' => 'kost-melati-sekaran',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Taman Siswa No. 1',
            'description' => 'Kost nyaman',
        ]);

        $review = Review::query()->create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'rating' => 5,
            'comment' => 'Kost sangat bersih dan pemilik ramah sekali!',
        ]);

        $this->assertNotEmpty($review->id);
        $this->assertSame(5, $review->rating);
        $this->assertSame('Kost sangat bersih dan pemilik ramah sekali!', $review->comment);
        $this->assertSame($user->id, $review->user->id);
        $this->assertSame($listing->id, $review->listing->id);
    }
}
