<?php

declare(strict_types=1);

namespace App\Http\Controllers\Booking;

use App\Domain\Payment\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ReceiptController extends Controller
{
    public function show(Payment $payment): Response
    {
        Gate::authorize('viewReceipt', $payment);

        $payment->load(['booking.user', 'booking.listing.owner', 'booking.room']);

        return Inertia::render('Booking/Receipt', [
            'payment' => $payment,
            'booking' => $payment->booking,
        ]);
    }
}
