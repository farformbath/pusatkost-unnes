import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';
import { Payout, PaymentDetail } from '@/types/payment';
import { Booking } from '@/types/booking';

interface OwnerPayoutShowProps {
    payout: Payout;
    booking: Booking;
    payment: PaymentDetail;
}

export default function Show({ payout, booking, payment }: OwnerPayoutShowProps) {
    const isPaid = payout.status === 'paid';

    return (
        <PublicLayout>
            <Head title={`Detail Payout #${payout.id.substring(0, 8)} | UNNESKost`} />

            <div className="max-w-4xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <Link href="/owner/payouts" className="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 mb-1">
                            ← Kembali ke Riwayat Payout
                        </Link>
                        <h1 className="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Detail Pencairan Dana Sewa
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500">
                            ID Payout: <span className="font-mono font-bold text-slate-700">PAY-{payout.id.substring(0, 8).toUpperCase()}</span>
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link href={`/owner/payouts/${payout.id}/receipt`} target="_blank">
                            <Button variant="primary" size="sm" className="bg-slate-900 hover:bg-black font-bold text-xs shadow-xs">
                                🖨️ Cetak / Unduh Kuitansi Payout
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Status Card */}
                <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Pencairan</div>
                            <div className="mt-1">
                                {isPaid ? (
                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        ✓ DANA TELAH DITRANSFER (LUNAS)
                                    </span>
                                ) : (
                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        ⏳ {payout.status.toUpperCase()}
                                    </span>
                                )}
                            </div>
                        </div>

                        <div className="text-left sm:text-right">
                            <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Net Diterima</div>
                            <div className="text-2xl font-black text-emerald-700 mt-0.5">
                                {payout.formatted_net_amount}
                            </div>
                        </div>
                    </div>

                    {/* Breakdown Details */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                            <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Informasi Booking</div>
                            <div className="text-sm font-bold text-slate-900">Kode Booking: #{booking.booking_code}</div>
                            <div className="text-xs text-slate-600">Unit: {booking.listing?.name} — Kamar {booking.room?.name}</div>
                            <div className="text-xs text-slate-600">Penyewa: {booking.tenant_name || booking.user?.name}</div>
                            <div className="text-xs text-slate-600">Periode: {booking.start_date} s/d {booking.end_date} ({booking.duration_months} Bulan)</div>
                        </div>

                        <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                            <div className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rincian Pembayaran Gateway</div>
                            <div className="text-xs text-slate-600">Invoice: <span className="font-mono font-bold text-slate-900">{payment.invoice_number}</span></div>
                            <div className="text-xs text-slate-600">Metode Bayar: <span className="uppercase font-mono font-medium">{payment.payment_channel}</span></div>
                            <div className="text-xs text-slate-600">Gross Pembayaran: {payment.formatted_gross_amount}</div>
                            <div className="text-xs text-slate-600">Biaya Platform: {payout.formatted_platform_fee}</div>
                            <div className="text-xs font-bold text-emerald-700 pt-1 border-t border-slate-200">
                                Net Disbursed: {payout.formatted_net_amount}
                            </div>
                        </div>
                    </div>

                    {/* Transfer / Reference Metadata */}
                    {payout.provider_payout_id && (
                        <div className="p-4 rounded-2xl bg-blue-50 border border-blue-200/60 text-xs text-blue-900 space-y-1">
                            <div className="font-bold flex items-center gap-1">
                                ℹ️ Referensi Transfer Bank / Gateway:
                            </div>
                            <div className="font-mono text-xs">{payout.provider_payout_id}</div>
                            <div className="text-[11px] text-blue-700">
                                Dicairkan pada: {payout.completed_at ? new Date(payout.completed_at).toLocaleString('id-ID') : '-'}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
