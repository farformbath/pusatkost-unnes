<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Review\Actions\RecalculateListingRatingAction;
use App\Domain\Review\Models\Review;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReviewController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        if (! $currentUser->isAdmin()) {
            abort(403, 'Akses khusus administrator UNNESKost.');
        }

        $rating = $request->query('rating');

        $query = Review::query()
            ->with(['user:id,name,email', 'listing:id,name,slug'])
            ->latest();

        if ($rating && is_numeric($rating)) {
            $query->where('rating', (int) $rating);
        }

        $reviews = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
            'selected_rating' => $rating,
        ]);
    }

    public function destroy(Review $review, RecalculateListingRatingAction $recalculate, Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        if (! $currentUser->isAdmin()) {
            abort(403, 'Akses khusus administrator UNNESKost.');
        }

        $listingId = $review->listing_id;
        $review->delete();

        if ($listingId) {
            $recalculate->execute($listingId);
        }

        return back()->with('success', 'Ulasan berhasil dihapus oleh administrator.');
    }
}
