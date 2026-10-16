<?php

declare(strict_types=1);

namespace App\Http\Controllers\Booking;

use App\Domain\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function show(string $id, Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $booking = Booking::query()
            ->with([
                'listing.owner',
                'listing.primaryImage',
                'room',
                'latestPayment',
                'user',
            ])
            ->findOrFail($id);

        $isTenant = (int) $user->id === (int) $booking->user_id;
        $isOwner = $booking->listing && (int) $user->id === (int) $booking->listing->owner_id;
        $isAdmin = $user->isAdmin();

        if (! $isTenant && ! $isOwner && ! $isAdmin) {
            abort(403, 'Anda tidak memiliki hak untuk melihat kwitansi ini.');
        }

        return Inertia::render('Booking/Invoice', [
            'booking' => $booking,
        ]);
    }
}
