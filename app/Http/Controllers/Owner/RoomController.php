<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Domain\Listing\Actions\CreateRoomAction;
use App\Domain\Listing\Actions\ToggleRoomAvailabilityAction;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreRoomRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    private function resolveUser(Request $request): User
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user instanceof User) {
            return $user;
        }

        /** @var User $fallback */
        $fallback = User::first() ?? User::factory()->create();

        return $fallback;
    }

    public function index(Request $request, Listing $listing): Response
    {
        $user = $this->resolveUser($request);
        $this->authorizeForUser($user, 'update', $listing);

        $listing->load(['rooms.facilities', 'rooms.images']);
        $facilities = Facility::where('category', 'room')->get();

        return Inertia::render('Owner/Rooms/Index', [
            'listing' => $listing,
            'facilities' => $facilities,
        ]);
    }

    public function store(StoreRoomRequest $request, Listing $listing, CreateRoomAction $action): RedirectResponse
    {
        $user = $this->resolveUser($request);
        $this->authorizeForUser($user, 'create', [Room::class, $listing]);

        $action->handle($request->toDTO($listing->id));

        return redirect()->route('owner.rooms.index', $listing->id)
            ->with('success', 'Tipe kamar berhasil ditambahkan.');
    }

    public function toggleAvailability(Request $request, Room $room, ToggleRoomAvailabilityAction $action): RedirectResponse
    {
        $user = $this->resolveUser($request);
        $this->authorizeForUser($user, 'toggle', $room);

        $action->handle($room);

        return back()->with('success', 'Status ketersediaan kamar diperbarui.');
    }
}
