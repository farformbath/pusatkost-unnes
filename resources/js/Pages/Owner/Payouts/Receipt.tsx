import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/Button';
import { Payout } from '@/types/payment';
import { Booking } from '@/types/booking';

interface OwnerPayoutReceiptProps {
    payout: Payout;
    booking: Booking;
    owner: {
        id: number;
        name: string;
        email: string;
        phone?: string | null;
    };
}

export default function Receipt({ payout, booking, owner }: OwnerPayoutReceiptProps) {
    const handlePrint = () => {
        window.print();
    };

    const isPaid = payout.status === 'paid';
    const receiptCode = `PAY-${payout.id.substring(0, 8).toUpperCase()}`;

    return (
        <div className="min-h-screen bg-slate-100/70 py-8 px-4 sm:px-6 print:bg-white print:p-0">
            <Head title={`Kwitansi Payout #${receiptCode} - Pusat Kost UNNES`} />

            {/* Action Bar (Hidden on Print) */}
            <div className="max-w-3xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 print:hidden">
                <Link
                    href="/owner/payouts"
                    className="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1"
                >
                    ← Kembali ke Riwayat Payout
                </Link>

                <div className="flex items-center gap-2">
                    <Button
                        variant="primary"
                        size="sm"
                        onClick={handlePrint}
                        className="bg-slate-900 hover:bg-black font-bold shadow-xs"
                    >
                        🖨️ Cetak / Simpan PDF
                    </Button>
                </div>
            </div>

            {/* Receipt Sheet */}
            <div className="max-w-3xl mx-auto bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-xl shadow-slate-900/5 relative overflow-hidden print:border-none print:shadow-none print:p-4 print:rounded-none">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-8 border-b-2 border-slate-900/90">
                    <div className="flex items-center gap-3">
                        <img
                            src="/images/logo.jpg"
                            alt="Pusat Kost UNNES"
                            className="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-xs"
                        />
                        <div>
                            <h1 className="text-xl font-black text-slate-950 tracking-tight">
                                Pusat Kost UNNES
                            </h1>
                            <p className="text-xs text-slate-500 font-medium">
                                Bukti Penyaluran Dana Sewa Resmi (Owner Payout Receipt)
                            </p>
                        </div>
                    </div>

                    <div className="text-left sm:text-right">
                        <div className="text-xs text-slate-400 font-bold uppercase tracking-wider">
                            Nomor Kuitansi
                        </div>
                        <div className="text-base font-black font-mono text-slate-900">
                            {receiptCode}
                        </div>
                        <div className="text-[11px] text-slate-500 mt-0.5">
                            Tanggal:{' '}
                            {payout.completed_at
                                ? new Date(payout.completed_at).toLocaleDateString('id-ID', {
                                      day: 'numeric',
                                      month: 'long',
                                      year: 'numeric',
                                  })
                                : new Date(payout.created_at).toLocaleDateString('id-ID')}
                        </div>
                    </div>
                </div>

                {/* Status Stamp */}
                <div className="my-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span className="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">
                            Status Penyaluran Dana
                        </span>
                        <div className="flex items-center gap-2 mt-0.5">
                            {isPaid ? (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    ✓ LUNAS DITRANSFER (SETTLED)
                                </span>
                            ) : (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    ⏳ {payout.status.toUpperCase()}
                                </span>
                            )}
                            <span className="text-xs text-slate-600 font-medium">
                                Ref Provider: <strong>{payout.provider_payout_id || 'SANDBOX-DIRECT'}</strong>
                            </span>
                        </div>
                    </div>

                    <div className="text-left sm:text-right">
                        <span className="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">
                            Total Net Diterima Owner
                        </span>
                        <div className="text-xl font-black text-emerald-700 font-mono">
                            {payout.formatted_net_amount}
                        </div>
                    </div>
                </div>

                {/* Party & Booking Metadata */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-8 my-8 text-xs">
                    <div>
                        <h2 className="font-bold text-slate-900 uppercase tracking-wider mb-2 text-[11px]">
                            Penerima Dana (Owner):
                        </h2>
                        <div className="font-bold text-slate-950 text-sm">{owner.name}</div>
                        <div className="text-slate-600 mt-0.5">{owner.email}</div>
                        {owner.phone && <div className="text-slate-600 font-mono">{owner.phone}</div>}
                    </div>

                    <div>
                        <h2 className="font-bold text-slate-900 uppercase tracking-wider mb-2 text-[11px]">
                            Rincian Pemesanan:
                        </h2>
                        <div className="text-slate-600">
                            Unit Kos:{' '}
                            <strong className="text-slate-900">
                                {booking.listing?.name} — Kamar {booking.room?.name}
                            </strong>
                        </div>
                        <div className="text-slate-600 mt-0.5">
                            Penyewa:{' '}
                            <strong className="text-slate-900">{booking.tenant_name || booking.user?.name}</strong>
                        </div>
                        <div className="text-slate-600 mt-0.5">
                            Kode Booking:{' '}
                            <strong className="text-slate-900 font-mono">#{booking.booking_code}</strong>
                        </div>
                        <div className="text-slate-600 mt-0.5">
                            Durasi Sewa: {booking.duration_months} Bulan ({booking.start_date} s/d {booking.end_date})
                        </div>
                    </div>
                </div>

                {/* Table of Breakdown */}
                <div className="my-8 overflow-hidden rounded-2xl border border-slate-200">
                    <table className="w-full text-left text-xs">
                        <thead className="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                            <tr>
                                <th className="px-4 py-3">Deskripsi Komponen</th>
                                <th className="px-4 py-3 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            <tr>
                                <td className="px-4 py-3">
                                    <div className="font-bold text-slate-800">
                                        Gross Pembayaran Sewa Kamar ({booking.duration_months} Bulan)
                                    </div>
                                    <div className="text-[11px] text-slate-500">
                                        Dibayar oleh penyewa melalui payment gateway
                                    </div>
                                </td>
                                <td className="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                    {payout.formatted_gross_amount}
                                </td>
                            </tr>
                            <tr>
                                <td className="px-4 py-3">
                                    <div className="font-bold text-slate-800">Biaya Layanan / Admin Platform</div>
                                    <div className="text-[11px] text-slate-500">
                                        Operasional & verifikasi sistem platform Pusat Kost UNNES
                                    </div>
                                </td>
                                <td className="px-4 py-3 text-right font-mono text-slate-600">
                                    - {payout.formatted_platform_fee}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot className="bg-slate-50 border-t-2 border-slate-900/90 font-bold">
                            <tr>
                                <td className="px-4 py-3 text-slate-900 font-black uppercase text-xs">
                                    Total Net Payout yang Disalurkan
                                </td>
                                <td className="px-4 py-3 text-right font-black font-mono text-emerald-800 text-sm">
                                    {payout.formatted_net_amount}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {/* Footer Notes */}
                <div className="mt-12 pt-6 border-t border-slate-200 text-center text-[11px] text-slate-400 space-y-1">
                    <p className="font-semibold text-slate-600">
                        Pusat Kost UNNES — Platform Manajemen Kos Terintegrasi Universitas Negeri Semarang
                    </p>
                    <p>
                        Dokumen ini adalah kuitansi digital resmi yang diterbitkan secara otomatis dan sah tanpa tanda
                        tangan basah.
                    </p>
                </div>
            </div>
        </div>
    );
}
