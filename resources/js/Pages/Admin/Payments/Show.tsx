import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';
import { PaymentDetail, PaymentReview, Payout, PaymentAudit } from '@/types/payment';
import { Booking } from '@/types/booking';
import { formatRupiah } from '@/lib/formatRupiah';

interface ShowProps {
    payment: PaymentDetail;
    review: PaymentReview | null;
    payout: Payout | null;
    booking: Booking;
    audits: PaymentAudit[];
}

export default function Show({ payment, review, payout, booking, audits }: ShowProps) {
    const [showApproveModal, setShowApproveModal] = useState(false);
    const [showRejectModal, setShowRejectModal] = useState(false);
    const [rejectReason, setRejectReason] = useState('');

    const isPaid = payment.transaction_status === 'paid';
    const isPayoutPaid = payout?.status === 'paid';

    // Contact Verification Form
    const { data, setData, post, processing } = useForm({
        tenant_contacted: review?.tenant_contacted ?? false,
        owner_contacted: review?.owner_contacted ?? false,
        tenant_confirmed: review?.tenant_confirmed ?? false,
        owner_confirmed: review?.owner_confirmed ?? false,
        admin_notes: review?.admin_notes ?? '',
    });

    // Payout Approval Form
    const [platformFee, setPlatformFee] = useState(0);
    const [payoutProcessing, setPayoutProcessing] = useState(false);

    const isAllConfirmed = data.tenant_contacted
        && data.owner_contacted
        && data.tenant_confirmed
        && data.owner_confirmed;

    const isApprovable = isPaid
        && isAllConfirmed
        && !isPayoutPaid
        && review?.status !== 'approved'
        && review?.status !== 'rejected';

    const netPayoutAmount = Math.max(0, payment.gross_amount - platformFee);

    const handleSaveContact = (e: React.FormEvent) => {
        e.preventDefault();
        if (!review) return;
        post(`/admin/payment-reviews/${review.id}/contact`, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    const handleConfirmApprove = () => {
        if (!review) return;
        setPayoutProcessing(true);
        router.post(`/admin/payment-reviews/${review.id}/approve`, {
            platform_fee: platformFee,
            payout_provider: 'sandbox',
        }, {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => {
                setPayoutProcessing(false);
                setShowApproveModal(false);
            },
        });
    };

    const handleConfirmReject = () => {
        if (!review || !rejectReason.trim()) return;
        router.post(`/admin/payment-reviews/${review.id}/reject`, {
            reason: rejectReason,
        }, {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => {
                setShowRejectModal(false);
                setRejectReason('');
            },
        });
    };


    return (
        <PublicLayout>
            <Head title={`Review Pembayaran #${payment.invoice_number} | UNNESKost`} />

            <div className="max-w-6xl mx-auto space-y-6">
                {/* Header & Back Navigation */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <Link href="/admin/payments" className="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 mb-1">
                            ← Kembali ke Daftar Pembayaran
                        </Link>
                        <h1 className="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                            Review Pembayaran {payment.invoice_number}
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500">
                            Booking Code: <span className="font-mono font-bold text-slate-700">#{booking.booking_code}</span> • ID: {payment.id}
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link href={`/payment/${payment.id}/receipt`} target="_blank">
                            <Button variant="outline" size="sm" className="font-bold text-xs">
                                🖨️ Cetak Kwitansi Penyewa
                            </Button>
                        </Link>
                        {payout && (
                            <Link href={`/owner/payouts/${payout.id}/receipt`} target="_blank">
                                <Button variant="outline" size="sm" className="font-bold text-xs">
                                    📄 Kwitansi Payout Owner
                                </Button>
                            </Link>
                        )}
                    </div>
                </div>

                {/* Status Bar Banner */}
                <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
                    <div>
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Pembayaran Gateway</div>
                        <div className="mt-1 flex items-center gap-2">
                            {isPaid ? (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    ✓ LUNAS (PAID)
                                </span>
                            ) : (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    ⏳ {payment.transaction_status.toUpperCase()}
                                </span>
                            )}
                        </div>
                    </div>

                    <div>
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tahap Verifikasi Admin</div>
                        <div className="mt-1">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {review?.status.toUpperCase() || 'PENDING'}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Pencairan (Payout)</div>
                        <div className="mt-1">
                            {isPayoutPaid ? (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    💸 LUNAS DITRANSFER
                                </span>
                            ) : (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {payout?.status ? payout.status.toUpperCase() : 'BELUM DIRILIS'}
                                </span>
                            )}
                        </div>
                    </div>

                    <div>
                        <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Gross Transaksi</div>
                        <div className="text-xl font-black text-slate-900 mt-0.5">
                            {payment.formatted_gross_amount}
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    {/* Left 2 Cols: Party Details & Contact Verification Form */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Transaction & Party Overview */}
                        <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                            <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                                Rincian Transaksi & Pihak Terlibat
                            </h2>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                {/* Tenant Info */}
                                <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                                    <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        🧑‍🎓 Calon Penyewa (Tenant)
                                    </div>
                                    <div className="font-black text-slate-900 text-sm">
                                        {booking.tenant_name || booking.user?.name}
                                    </div>
                                    <div className="text-xs text-slate-600">
                                        Email: <span className="font-medium">{booking.user?.email}</span>
                                    </div>
                                    <div className="text-xs text-slate-600">
                                        Telepon/WA: <span className="font-mono font-bold text-slate-900">{booking.tenant_phone || '-'}</span>
                                    </div>
                                </div>

                                {/* Owner Info */}
                                <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                                    <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        🏠 Pemilik Kos (Owner)
                                    </div>
                                    <div className="font-black text-slate-900 text-sm">
                                        {booking.listing?.owner?.name || 'Owner'}
                                    </div>
                                    <div className="text-xs text-slate-600">
                                        Email: <span className="font-medium">{booking.listing?.owner?.email}</span>
                                    </div>
                                    <div className="text-xs text-slate-600">
                                        Telepon/WA: <span className="font-mono font-bold text-slate-900">{booking.listing?.owner?.phone || '-'}</span>
                                    </div>
                                </div>
                            </div>

                            {/* Property & Room */}
                            <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1.5">
                                <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    Unit Kos yang Dipesan
                                </div>
                                <div className="font-bold text-slate-900 text-sm">
                                    {booking.listing?.name} — Kamar {booking.room?.name}
                                </div>
                                <div className="text-xs text-slate-500">
                                    Area {booking.listing?.area} • Durasi: {booking.duration_months} Bulan ({booking.start_date} s/d {booking.end_date})
                                </div>
                            </div>
                        </div>

                        {/* Step 2: Contact Verification Checklist */}
                        <form onSubmit={handleSaveContact} className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                            <div className="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <h2 className="text-base font-bold text-slate-900">
                                        Form Verifikasi Kontak Dua Pihak
                                    </h2>
                                    <p className="text-xs text-slate-500">
                                        Admin wajib menghubungi tenant dan owner untuk konfirmasi kesiapan kamar & identitas.
                                    </p>
                                </div>
                            </div>

                            <div className="space-y-4">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {/* Tenant Checkboxes */}
                                    <div className="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-3">
                                        <div className="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                            <span>🧑‍🎓</span> Verifikasi Penyewa (Tenant)
                                        </div>
                                        <label className="flex items-center gap-2.5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                checked={data.tenant_contacted}
                                                onChange={(e) => setData('tenant_contacted', e.target.checked)}
                                                className="w-4 h-4 rounded text-slate-900 focus:ring-slate-900"
                                            />
                                            <span className="text-xs font-semibold text-slate-700">Penyewa sudah dihubungi via WA/Telp</span>
                                        </label>
                                        <label className="flex items-center gap-2.5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                checked={data.tenant_confirmed}
                                                onChange={(e) => setData('tenant_confirmed', e.target.checked)}
                                                className="w-4 h-4 rounded text-slate-900 focus:ring-slate-900"
                                            />
                                            <span className="text-xs font-semibold text-slate-700">Penyewa mengonfirmasi pesanan valid</span>
                                        </label>
                                    </div>

                                    {/* Owner Checkboxes */}
                                    <div className="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 space-y-3">
                                        <div className="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                            <span>🏠</span> Verifikasi Pemilik (Owner)
                                        </div>
                                        <label className="flex items-center gap-2.5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                checked={data.owner_contacted}
                                                onChange={(e) => setData('owner_contacted', e.target.checked)}
                                                className="w-4 h-4 rounded text-slate-900 focus:ring-slate-900"
                                            />
                                            <span className="text-xs font-semibold text-slate-700">Pemilik kos sudah dihubungi</span>
                                        </label>
                                        <label className="flex items-center gap-2.5 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                checked={data.owner_confirmed}
                                                onChange={(e) => setData('owner_confirmed', e.target.checked)}
                                                className="w-4 h-4 rounded text-slate-900 focus:ring-slate-900"
                                            />
                                            <span className="text-xs font-semibold text-slate-700">Pemilik kos siap menerima penyewa</span>
                                        </label>
                                    </div>
                                </div>

                                {/* Admin Notes */}
                                <div className="space-y-1.5">
                                    <label className="text-xs font-bold text-slate-700">
                                        Catatan Internal Admin (Optional)
                                    </label>
                                    <textarea
                                        rows={3}
                                        value={data.admin_notes}
                                        onChange={(e) => setData('admin_notes', e.target.value)}
                                        placeholder="Contoh: Sudah konfirmasi via WA pukul 10.30 WIB, owner setuju penyewa check-in tanggal 1..."
                                        className="w-full text-xs sm:text-sm rounded-xl border border-slate-200 p-3 focus:border-slate-900 focus:ring-slate-900"
                                    />
                                </div>

                                <div className="flex items-center justify-between pt-2">
                                    <div className="text-[11px] text-slate-500">
                                        {isAllConfirmed ? (
                                            <span className="text-emerald-600 font-bold flex items-center gap-1">
                                                ✓ Seluruh syarat verifikasi dua pihak telah terpenuhi!
                                            </span>
                                        ) : (
                                            <span className="text-amber-600 font-medium">
                                                * Centang keempat opsi di atas untuk mengaktifkan tombol pelepasan dana payout.
                                            </span>
                                        )}
                                    </div>

                                    <Button type="submit" variant="outline" size="sm" disabled={processing} className="font-bold text-xs">
                                        {processing ? 'Menyimpan...' : '💾 Simpan Status Kontak'}
                                    </Button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {/* Right Column: Payout Decision & Audit Trail */}
                    <div className="space-y-6">
                        {/* Payout Action Card */}
                        <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                            <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                                Keputusan Payout Dana
                            </h2>

                            <div className="space-y-3 text-xs">
                                <div className="flex justify-between py-1.5 border-b border-slate-100">
                                    <span className="text-slate-500">Gross Pembayaran</span>
                                    <span className="font-bold text-slate-900">{payment.formatted_gross_amount}</span>
                                </div>
                                <div className="flex justify-between py-1.5 border-b border-slate-100">
                                    <span className="text-slate-500">Biaya Platform / Admin</span>
                                    <span className="font-bold text-slate-900">{formatRupiah(platformFee)}</span>
                                </div>
                                <div className="flex justify-between py-2 bg-slate-50 px-3 rounded-xl">
                                    <span className="font-bold text-slate-700">Net Payout ke Owner</span>
                                    <span className="font-black text-slate-900 text-sm">{formatRupiah(netPayoutAmount)}</span>
                                </div>
                            </div>

                            {/* Payout Already Executed */}
                            {isPayoutPaid ? (
                                <div className="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/60 text-emerald-800 text-xs space-y-1">
                                    <div className="font-bold flex items-center gap-1">
                                        ✓ Payout Berhasil Dicairkan
                                    </div>
                                    <div className="text-[11px] text-emerald-700">
                                        Ref: <span className="font-mono">{payout?.provider_payout_id}</span>
                                    </div>
                                    <div className="text-[10px] text-emerald-600">
                                        Dicairkan pada: {payout?.completed_at ? new Date(payout.completed_at).toLocaleString('id-ID') : '-'}
                                    </div>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {/* Action Buttons */}
                                    <Button
                                        type="button"
                                        variant="primary"
                                        size="lg"
                                        disabled={!isApprovable || payoutProcessing}
                                        onClick={() => setShowApproveModal(true)}
                                        className="w-full bg-slate-950 hover:bg-black font-black text-sm shadow-md py-3.5"
                                    >
                                        Approve & Release Payment 💸
                                    </Button>

                                    {!isApprovable && (
                                        <p className="text-[11px] text-amber-700 bg-amber-50 p-3 rounded-xl border border-amber-200/60 leading-relaxed">
                                            ⚠️ <strong>Pelepasan dana terkunci:</strong> Pastikan pembayaran lunas dan checklist verifikasi kedua belah pihak telah dicentang & disimpan.
                                        </p>
                                    )}

                                    {review?.status !== 'rejected' && (
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => setShowRejectModal(true)}
                                            className="w-full text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 font-bold"
                                        >
                                            ✕ Tolak & Batalkan Payout
                                        </Button>
                                    )}
                                </div>
                            )}
                        </div>

                        {/* Audit Trail Timeline */}
                        <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                            <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                                📋 Audit Trail & Riwayat Aksi
                            </h3>

                            <div className="space-y-3">
                                {audits.length === 0 ? (
                                    <div className="text-xs text-slate-400">Belum ada riwayat audit.</div>
                                ) : (
                                    audits.map((a) => (
                                        <div key={a.id} className="text-xs border-l-2 border-slate-300 pl-3 py-1 space-y-0.5">
                                            <div className="font-bold text-slate-800">
                                                {a.action}
                                            </div>
                                            <div className="text-[10px] text-slate-500">
                                                Oleh: {a.actor?.name || 'System / Webhook'} • {new Date(a.created_at).toLocaleString('id-ID')}
                                            </div>
                                        </div>
                                    ))
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Confirmation Modal for Payout Approval */}
            <Modal
                isOpen={showApproveModal}
                onClose={() => setShowApproveModal(false)}
                title="Konfirmasi Pelepasan Dana Payout"
            >
                <div className="space-y-4 text-xs sm:text-sm">
                    <p className="text-slate-600">
                        Anda akan menyetujui transaksi dan <strong>mencairkan dana sewa</strong> kepada pemilik kos berikut:
                    </p>

                    <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-2 text-xs">
                        <div className="flex justify-between">
                            <span className="text-slate-500">Pemilik Kos (Owner):</span>
                            <span className="font-bold text-slate-900">{booking.listing?.owner?.name}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-slate-500">Unit Kos:</span>
                            <span className="font-bold text-slate-900">{booking.listing?.name}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-slate-500">Nominal Gross:</span>
                            <span className="font-bold text-slate-900">{payment.formatted_gross_amount}</span>
                        </div>
                        <div className="flex justify-between border-t border-slate-200 pt-1.5">
                            <span className="font-bold text-slate-700">Net Transfer ke Owner:</span>
                            <span className="font-black text-emerald-700 text-sm">{formatRupiah(netPayoutAmount)}</span>
                        </div>
                    </div>

                    <div className="space-y-1.5">
                        <label className="text-xs font-bold text-slate-700">Biaya Platform (Opsional):</label>
                        <input
                            type="number"
                            min="0"
                            value={platformFee}
                            onChange={(e) => setPlatformFee(Math.max(0, parseInt(e.target.value) || 0))}
                            className="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:border-slate-900 focus:ring-slate-900"
                        />
                    </div>

                    <div className="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-[11px] leading-relaxed">
                        ⚠️ <strong>Peringatan:</strong> Aksi ini bersifat permanen dan akan memicu eksekusi disbursement otomatis ke akun/rekening pemilik kos.
                    </div>

                    <div className="flex items-center justify-end gap-2 pt-2">
                        <Button variant="ghost" size="sm" onClick={() => setShowApproveModal(false)}>
                            Batal
                        </Button>
                        <Button
                            variant="primary"
                            size="sm"
                            disabled={payoutProcessing}
                            onClick={handleConfirmApprove}
                            className="bg-slate-950 hover:bg-black font-bold"
                        >
                            {payoutProcessing ? 'Memproses Payout...' : 'Konfirmasi & Rilis Payout'}
                        </Button>
                    </div>
                </div>
            </Modal>

            {/* Reject Modal */}
            <Modal
                isOpen={showRejectModal}
                onClose={() => setShowRejectModal(false)}
                title="Tolak / Batalkan Payout"
            >
                <div className="space-y-4 text-xs sm:text-sm">
                    <p className="text-slate-600">
                        Masukkan alasan penolakan atau pembatalan pelepasan dana ini:
                    </p>
                    <textarea
                        rows={3}
                        value={rejectReason}
                        onChange={(e) => setRejectReason(e.target.value)}
                        placeholder="Contoh: Kamar tidak siap huni, penyewa membatalkan sewa..."
                        className="w-full text-xs rounded-xl border border-slate-200 p-3 focus:border-slate-900 focus:ring-slate-900"
                    />
                    <div className="flex items-center justify-end gap-2 pt-2">
                        <Button variant="ghost" size="sm" onClick={() => setShowRejectModal(false)}>
                            Batal
                        </Button>
                        <Button
                            variant="danger"
                            size="sm"
                            disabled={!rejectReason.trim()}
                            onClick={handleConfirmReject}
                            className="font-bold"
                        >
                            Tolak Payout
                        </Button>
                    </div>
                </div>
            </Modal>
        </PublicLayout>
    );
}
