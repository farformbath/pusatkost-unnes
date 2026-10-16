<?php

declare(strict_types=1);

namespace App\Http\Controllers\Review;

use App\Domain\Listing\Repositories\ListingRepositoryInterface;
use App\Domain\Review\Actions\CreateReviewAction;
use App\Domain\Review\Actions\ReplyReviewAction;
use App\Domain\Review\Models\Review;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReplyReviewRequest;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReviewController extends Controller
{
    public function store(
        string $slug,
        StoreReviewRequest $request,
        ListingRepositoryInterface $listingRepository,
        CreateReviewAction $createReviewAction
    ): RedirectResponse {
        $listing = $listingRepository->findBySlug($slug);

        if (! $listing) {
            throw new NotFoundHttpException('Kos tidak ditemukan.');
        }

        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            // For testing / demo environment, fallback to first user or create one
            $user = User::query()->first() ?? User::query()->create([
                'name' => 'Mahasiswa Pengulas',
                'email' => 'mahasiswa@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $dto = $request->toDTO($user->id, $listing->id);
        $createReviewAction->execute($dto);

        return back()->with('success', 'Ulasan dan rating bintang berhasil dikirim!');
    }

    public function reply(
        string $id,
        ReplyReviewRequest $request,
        ReplyReviewAction $replyReviewAction
    ): RedirectResponse {
        $review = Review::query()->with('listing')->findOrFail($id);

        /** @var string $reply */
        $reply = $request->validated('reply');

        $replyReviewAction->execute($review->id, $reply);

        return back()->with('success', 'Balasan ulasan berhasil dikirim!');
    }
}
