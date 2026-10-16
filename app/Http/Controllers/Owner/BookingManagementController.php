<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Domain\Booking\Actions\ApproveBookingAction;
use App\Domain\Booking\Actions\RejectBookingAction;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BookingManagementController extends Controller
{
    public function __construct(
        private readonly BookingRepositoryInterface $repository,
    ) {}

    public function index(Request $request): Response
    {
        /** @var User $owner */
        $owner = Auth::user() ?? User::first() ?? User::factory()->create();

        $status = $request->query('status');
        $statusStr = is_string($status) ? $status : null;

        $bookings = $this->repository->getForOwner($owner->id, $statusStr);

        return Inertia::render('Owner/Bookings/Index', [
            'bookings' => $bookings,
            'filters' => [
                'status' => $statusStr ?? '',
            ],
        ]);
    }

    public function approve(string $id, Request $request, ApproveBookingAction $action): RedirectResponse
    {
        $booking = $this->repository->findById($id);

        if (! $booking) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        /** @var User $user */
        $user = $request->user() ?? Auth::user() ?? User::first();
        $this->authorizeForUser($user, 'approve', $booking);

        $action->execute($booking);

        return back()->with('success', "Booking {$booking->booking_code} berhasil disetujui!");
    }

    public function reject(string $id, Request $request, RejectBookingAction $action): RedirectResponse
    {
        $booking = $this->repository->findById($id);

        if (! $booking) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        /** @var User $user */
        $user = $request->user() ?? Auth::user() ?? User::first();
        $this->authorizeForUser($user, 'reject', $booking);

        $reason = $request->input('reason');
        $action->execute($booking, is_string($reason) ? $reason : null);

        return back()->with('success', "Booking {$booking->booking_code} ditolak.");
    }
}
