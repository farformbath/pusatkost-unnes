<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wishlist;

use App\Domain\Wishlist\Actions\ToggleWishlistAction;
use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wishlist\ToggleWishlistRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function index(Request $request, WishlistRepositoryInterface $wishlistRepository): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            $user = User::query()->first() ?? User::query()->create([
                'name' => 'Pengguna Demo',
                'email' => 'demo@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $wishlists = $wishlistRepository->getUserWishlists($user->id, 12);

        return Inertia::render('Wishlist/Index', [
            'wishlists' => $wishlists,
        ]);
    }

    public function toggle(
        ToggleWishlistRequest $request,
        ToggleWishlistAction $toggleWishlistAction
    ): RedirectResponse {
        /** @var string $listingId */
        $listingId = $request->validated('listing_id');

        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            $user = User::query()->first() ?? User::query()->create([
                'name' => 'Pengguna Demo',
                'email' => 'demo@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $isAdded = $toggleWishlistAction->execute($user->id, $listingId);

        $message = $isAdded
            ? 'Kos berhasil ditambahkan ke Wishlist!'
            : 'Kos berhasil dihapus dari Wishlist.';

        return back()->with('success', $message);
    }
}
