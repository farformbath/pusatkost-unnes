<?php

declare(strict_types=1);

namespace App\Http\Controllers\Booking;

use App\Domain\Booking\Actions\CancelBookingAction;
use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Domain\Listing\Models\Room;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingRepositoryInterface $repository,
    ) {}

    public function checkout(Room $room): Response
    {
        $room->load(['listing.images', 'facilities']);

        /** @var User|null $user */
        $user = Auth::user();

        return Inertia::render('Booking/Checkout', [
            'room' => $room,
            'listing' => $room->listing,
            'user' => $user,
        ]);
    }

    public function store(StoreBookingRequest $request, CreateBookingAction $action): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user() ?? User::firstOrCreate(
            ['email' => 'penyewa@unneskost.com'],
            [
                'name' => $request->validated('tenant_name'),
                'password' => bcrypt('password123'),
            ]
        );

        $dto = $request->toDTO($user->id);
        $booking = $action->execute($dto);

        return redirect()->route('booking.show', $booking->id)->with('success', 'Pengajuan booking berhasil dibuat!');
    }

    public function show(string $id, Request $request): Response
    {
        $booking = $this->repository->findById($id);

        if (! $booking) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        /** @var User|null $currentUser */
        $currentUser = $request->user();
        if ($currentUser) {
            $this->authorizeForUser($currentUser, 'view', $booking);
        }

        return Inertia::render('Booking/Show', [
            'booking' => $booking,
        ]);
    }

    public function cancel(string $id, Request $request, CancelBookingAction $action): RedirectResponse
    {
        $booking = $this->repository->findById($id);

        if (! $booking) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        /** @var User|null $currentUser */
        $currentUser = $request->user();
        if ($currentUser) {
            $this->authorizeForUser($currentUser, 'cancel', $booking);
        }

        $action->execute($booking);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
