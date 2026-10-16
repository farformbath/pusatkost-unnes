<?php

declare(strict_types=1);

namespace App\Http\Controllers\Owner;

use App\Domain\Payment\Models\Payout;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class OwnerPayoutController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Payout::class);

        /** @var User $owner */
        $owner = auth()->user();

        $query = Payout::query()
            ->where('owner_id', $owner->id)
            ->with(['booking.user', 'booking.listing', 'booking.room', 'payment'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->value());
        }

        $payouts = $query->paginate(10)->withQueryString();

        $metrics = [
            'total_received' => (int) Payout::where('owner_id', $owner->id)->where('status', 'paid')->sum('net_amount'),
            'pending_payouts' => Payout::where('owner_id', $owner->id)->whereIn('status', ['pending', 'processing'])->count(),
            'total_transactions' => Payout::where('owner_id', $owner->id)->count(),
        ];

        return Inertia::render('Owner/Payouts/Index', [
            'payouts' => $payouts,
            'filters' => $request->only(['status']),
            'metrics' => $metrics,
        ]);
    }

    public function show(Payout $payout): Response
    {
        Gate::authorize('view', $payout);

        $payout->load(['booking.user', 'booking.listing', 'booking.room', 'payment.review', 'owner']);

        return Inertia::render('Owner/Payouts/Show', [
            'payout' => $payout,
            'booking' => $payout->booking,
            'payment' => $payout->payment,
        ]);
    }

    public function receipt(Payout $payout): Response
    {
        Gate::authorize('viewReceipt', $payout);

        $payout->load(['booking.user', 'booking.listing', 'booking.room', 'payment', 'owner']);

        return Inertia::render('Owner/Payouts/Receipt', [
            'payout' => $payout,
            'booking' => $payout->booking,
            'owner' => $payout->owner,
        ]);
    }
}
