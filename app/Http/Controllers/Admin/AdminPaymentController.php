<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Payment\Actions\ApprovePayoutAction;
use App\Domain\Payment\Actions\RecordPartyContactAction;
use App\Domain\Payment\Actions\RejectPayoutAction;
use App\Domain\Payment\Actions\StartAdminReviewAction;
use App\Domain\Payment\DTOs\ContactVerificationData;
use App\Domain\Payment\DTOs\PayoutData;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentReview;
use App\Domain\Payment\Models\Payout;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApprovePayoutRequest;
use App\Http\Requests\Admin\RejectPayoutRequest;
use App\Http\Requests\Admin\VerifyContactRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AdminPaymentController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Payment::class);

        $query = Payment::query()
            ->with(['booking.user', 'booking.listing.owner', 'booking.room', 'review.admin', 'payout'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $status = $request->string('status')->value();
            $query->where('transaction_status', $status);
        }

        if ($request->filled('review_status')) {
            $reviewStatus = $request->string('review_status')->value();
            $query->whereHas('review', function ($q) use ($reviewStatus): void {
                $q->where('status', $reviewStatus);
            });
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->where(function ($q) use ($search): void {
                $q->where('invoice_number', 'ilike', "%{$search}%")
                    ->orWhere('payment_reference', 'ilike', "%{$search}%")
                    ->orWhereHas('booking', function ($b) use ($search): void {
                        $b->where('booking_code', 'ilike', "%{$search}%")
                            ->orWhere('tenant_name', 'ilike', "%{$search}%")
                            ->orWhereHas('user', function ($u) use ($search): void {
                                $u->where('name', 'ilike', "%{$search}%")
                                    ->orWhere('email', 'ilike', "%{$search}%");
                            });
                    });
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_volume' => (int) Payment::where('transaction_status', PaymentStatus::PAID)->sum('gross_amount'),
            'pending_review' => PaymentReview::whereIn('status', [PaymentReviewStatus::PENDING, PaymentReviewStatus::CONTACTING])->count(),
            'verified_ready' => PaymentReview::where('status', PaymentReviewStatus::VERIFIED)->count(),
            'completed_payouts' => Payout::where('status', 'paid')->count(),
        ];

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['search', 'status', 'review_status']),
            'metrics' => $metrics,
        ]);
    }

    public function show(Payment $payment, StartAdminReviewAction $startAdminReviewAction): Response
    {
        Gate::authorize('view', $payment);

        /** @var User $admin */
        $admin = auth()->user();

        // Start admin review automatically if status is PENDING and payment is PAID
        if ($payment->transaction_status === PaymentStatus::PAID) {
            $startAdminReviewAction->execute($payment, $admin);
        }

        $payment->load([
            'booking.user',
            'booking.listing.owner',
            'booking.room',
            'review.admin',
            'payout.owner',
            'audits.actor',
        ]);

        return Inertia::render('Admin/Payments/Show', [
            'payment' => $payment,
            'review' => $payment->review,
            'payout' => $payment->payout,
            'booking' => $payment->booking,
            'audits' => $payment->audits,
        ]);
    }

    public function verifyContact(
        VerifyContactRequest $request,
        PaymentReview $review,
        RecordPartyContactAction $recordPartyContactAction
    ): RedirectResponse {
        Gate::authorize('updateContact', $review);

        /** @var User $admin */
        $admin = auth()->user();

        $adminNotes = $request->filled('admin_notes') ? $request->string('admin_notes')->value() : null;

        $dto = new ContactVerificationData(
            tenantContacted: (bool) $request->boolean('tenant_contacted'),
            ownerContacted: (bool) $request->boolean('owner_contacted'),
            tenantConfirmed: (bool) $request->boolean('tenant_confirmed'),
            ownerConfirmed: (bool) $request->boolean('owner_confirmed'),
            adminNotes: $adminNotes,
        );

        $recordPartyContactAction->execute($review, $dto, $admin);

        return back()->with('success', 'Status kontak & verifikasi kedua pihak berhasil diperbarui.');
    }

    public function approve(
        ApprovePayoutRequest $request,
        PaymentReview $review,
        ApprovePayoutAction $approvePayoutAction
    ): RedirectResponse {
        Gate::authorize('approve', $review);

        /** @var User $admin */
        $admin = auth()->user();

        $platformFee = $request->integer('platform_fee', 0);
        $payoutProvider = $request->filled('payout_provider') ? $request->string('payout_provider')->value() : 'sandbox';

        $payoutData = new PayoutData(
            paymentId: $review->payment_id,
            platformFee: $platformFee,
            payoutProvider: $payoutProvider,
        );

        $approvePayoutAction->execute($review, $payoutData, $admin);

        return back()->with('success', 'Pembayaran telah disetujui dan dana berhasil dicairkan ke rekening pemilik kos!');
    }

    public function reject(
        RejectPayoutRequest $request,
        PaymentReview $review,
        RejectPayoutAction $rejectPayoutAction
    ): RedirectResponse {
        Gate::authorize('reject', $review);

        /** @var User $admin */
        $admin = auth()->user();

        $rejectPayoutAction->execute(
            $review,
            $request->string('reason')->value(),
            $admin
        );

        return back()->with('success', 'Pencairan dana sewa telah ditolak/dibatalkan.');
    }
}
