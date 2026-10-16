<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Domain\Listing\Actions\CreateListingAction;
use App\Domain\Listing\Actions\PublishListingAction;
use App\Domain\Listing\Actions\UpdateListingAction;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Repositories\ListingRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Http\Requests\Listing\UpdateListingRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListingController extends Controller
{
    private function resolveUser(Request $request): User
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user instanceof User) {
            return $user;
        }

        // Fallback for development if not authenticated
        /** @var User $fallback */
        $fallback = User::first() ?? User::factory()->create();

        return $fallback;
    }

    public function index(Request $request, ListingRepositoryInterface $repository): Response
    {
        $user = $this->resolveUser($request);
        $listings = $repository->getOwnerListings((int) $user->id);

        return Inertia::render('Owner/Listings/Index', [
            'listings' => $listings,
        ]);
    }

    public function create(Request $request): Response
    {
        $user = $this->resolveUser($request);
        $this->authorizeForUser($user, 'create', Listing::class);

        $facilities = Facility::all();

        return Inertia::render('Owner/Listings/Create', [
            'facilities' => $facilities,
        ]);
    }

    public function store(StoreListingRequest $request, CreateListingAction $action): RedirectResponse
    {
        $user = $this->resolveUser($request);
        $this->authorizeForUser($user, 'create', Listing::class);

        $listing = $action->handle($request->toDTO((int) $user->id));

        return redirect()->route('owner.listings.edit', $listing->id)
            ->with('success', 'Kos berhasil didaftarkan sebagai draf. Silakan tambahkan kamar dan foto.');
    }

    public function edit(Request $request, Listing $listing): Response
    {
        $user = $this->resolveUser($request);
        $this->authorizeForUser($user, 'update', $listing);

        $listing->load(['facilities', 'images', 'rooms']);
        $facilities = Facility::all();

        return Inertia::render('Owner/Listings/Edit', [
            'listing' => $listing,
            'facilities' => $facilities,
        ]);
    }

    public function update(UpdateListingRequest $request, Listing $listing, UpdateListingAction $action): RedirectResponse
    {
        $user = $this->resolveUser($request);
        $this->authorizeForUser($user, 'update', $listing);

        $action->handle($listing, $request->toDTO((int) $user->id));

        return redirect()->route('owner.listings.edit', $listing->id)
            ->with('success', 'Informasi kos berhasil diperbarui.');
    }

    public function publish(Request $request, Listing $listing, PublishListingAction $action): RedirectResponse
    {
        $user = $this->resolveUser($request);
        $this->authorizeForUser($user, 'publish', $listing);

        $action->handle($listing);

        return redirect()->route('owner.listings.index')
            ->with('success', 'Kos berhasil dipublikasikan!');
    }
}
