<?php

declare(strict_types=1);

namespace App\Http\Controllers\Booking;

use App\Domain\Booking\Actions\ProcessPaymentAction;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\SimulatePaymentRequest;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(
        private readonly BookingRepositoryInterface $repository,
    ) {}

    public function simulate(string $id, SimulatePaymentRequest $request, ProcessPaymentAction $action): RedirectResponse
    {
        $booking = $this->repository->findById($id);

        if (! $booking) {
            abort(404, 'Data pemesanan tidak ditemukan.');
        }

        /** @var string $channel */
        $channel = $request->validated('payment_channel');
        $action->execute($booking, $channel);

        return back()->with('success', 'Pembayaran berhasil diverifikasi via Virtual Account simulator!');
    }
}
