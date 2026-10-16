<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Review;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Review\Actions\CreateReviewAction;
use App\Domain\Review\Actions\RecalculateListingRatingAction;
use App\Domain\Review\Actions\ReplyReviewAction;
use App\Domain\Review\DTOs\ReviewData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateReviewActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateReviewAction $createAction;

    private ReplyReviewAction $replyAction;

    private User $owner;

    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createAction = new CreateReviewAction(new RecalculateListingRatingAction);
        $this->replyAction = new ReplyReviewAction;

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

    public function test_it_creates_review_and_recalculates_listing_rating(): void
    {
        $user1 = User::query()->create([
            'name' => 'User Satu',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = User::query()->create([
            'name' => 'User Dua',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        // 1st review (Rating 5)
        $this->createAction->execute(new ReviewData(
            userId: $user1->id,
            listingId: $this->listing->id,
            rating: 5,
            comment: 'Sangat bagus!'
        ));

        $this->listing->refresh();
        $this->assertSame(1, $this->listing->review_count);
        $this->assertSame(5.0, (float) $this->listing->rating_avg);

        // 2nd review (Rating 4)
        $review2 = $this->createAction->execute(new ReviewData(
            userId: $user2->id,
            listingId: $this->listing->id,
            rating: 4,
            comment: 'Bagus dan nyaman.'
        ));

        $this->listing->refresh();
        $this->assertSame(2, $this->listing->review_count);
        $this->assertSame(4.5, (float) $this->listing->rating_avg);

        // Owner replies to review
        $updatedReview = $this->replyAction->execute($review2->id, 'Terima kasih atas ulasannya kak!');
        $this->assertSame('Terima kasih atas ulasannya kak!', $updatedReview->owner_reply);
        $this->assertNotNull($updatedReview->owner_replied_at);
    }
}
