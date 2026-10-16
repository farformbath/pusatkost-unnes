import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Booking } from '@/types/booking';
import { Button } from '@/components/ui/Button';

interface InvoiceProps {
    booking: Booking;
}

export default function Invoice({ booking }: InvoiceProps) {
    const handlePrint = () => {
        window.print();
    };

    const isPaid = booking.payment_status === 'paid';

    return (
        <div className="min-h-screen bg-slate-100/70 py-8 px-4 sm:px-6 print:bg-white print:p-0">
            <Head title={`Invoice Sewa #${booking.booking_code} - Pusat Kost UNNES`} />

            {/* Action Bar (Hidden on Print) */}
            <div className="max-w-3xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 print:hidden">
                <Link
                    href="/tenant/bookings"
                    className="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1"
                >
                    ← Kembali ke Riwayat Booking
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

            {/* Invoice Sheet */}
            <div className="max-w-3xl mx-auto bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/90 shadow-xl shadow-slate-900/5 relative overflow-hidden print:border-none print:shadow-none print:p-4 print:rounded-none">
                {/* Header with Pusat Kost UNNES Brand & Invoice Stamp */}
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
                                Bukti Transaksi & Tagihan Sewa Resmi
                            </p>
                        </div>
                    </div>

                    <div className="text-left sm:text-right">
                        <div className="text-xs text-slate-400 font-bold uppercase tracking-wider">
                            Nomor Invoice
                        </div>
                        <div className="text-base font-black font-mono text-slate-900">
                            INV/{booking.booking_code}
                        </div>
                        <div className="text-[11px] text-slate-500 mt-0.5">
                            Tanggal: {new Date(booking.created_at).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric',
                            })}
                        </div>
                    </div>
                </div>

                {/* Status Stamp */}
                <div className="my-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span className="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">
                            Status Transaksi
                        </span>
                        <div className="flex items-center gap-2 mt-0.5">
                            {isPaid ? (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    ✓ LUNAS (PAID)
                                </span>
                            ) : (
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    ⏳ MENUNGGU PEMBAYARAN
                                </span>
                            )}
                            <span className="text-xs text-slate-600 font-medium">
                                Kode Booking: <strong>#{booking.booking_code}</strong>
                            </span>
                        </div>
                    </div>

                    {(booking.paid_at || booking.latest_payment?.paid_at || booking.payment?.paid_at) && (
                        <div className="text-left sm:text-right">
                            <span className="text-[11px] text-slate-400 font-semibold uppercase tracking-wider block">
                                Waktu Pelunasan
                            </span>
                            <span className="text-xs font-bold text-slate-800">
                                {new Date(booking.paid_at || booking.latest_payment?.paid_at || booking.payment?.paid_at || '').toLocaleString('id-ID')}
                            </span>
                        </div>
                    )}
                </div>

                {/* Tenant & Property Details Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-8 my-8 text-xs">
                    {/* Tenant Info */}
                    <div className="space-y-1.5">
                        <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Penyewa (Mahasiswa)
                        </span>
                        <div className="font-bold text-sm text-slate-900">{booking.tenant_name}</div>
                        <div className="text-slate-600">WhatsApp: {booking.tenant_phone}</div>
                        <div className="text-slate-600">Email: {booking.user?.email || '-'}</div>
                    </div>

                    {/* Owner & Property Info */}
                    <div className="space-y-1.5">
                        <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Properti & Pemilik Kos
                        </span>
                        <div className="font-bold text-sm text-slate-900">{booking.listing?.name}</div>
                        <div className="text-slate-600">Pemilik: {booking.listing?.owner?.name}</div>
                        <div className="text-slate-600">
                            Lokasi: Area {booking.listing?.area} • {booking.listing?.address}
                        </div>
                    </div>
                </div>

                {/* Booking Period Table */}
                <div className="rounded-2xl border border-slate-200 overflow-hidden my-6">
                    <table className="w-full text-left text-xs">
                        <thead className="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                            <tr>
                                <th className="px-5 py-3">Rincian Unit Kamar</th>
                                <th className="px-5 py-3">Periode Sewa</th>
                                <th className="px-5 py-3">Durasi</th>
                                <th className="px-5 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100 text-slate-700">
                            <tr>
                                <td className="px-5 py-4">
                                    <div className="font-bold text-slate-900">{booking.room?.name || 'Kamar Kos'}</div>
                                    <div className="text-slate-400 text-[11px]">
                                        Tipe Kos: {booking.listing?.gender_type ? `Kos ${booking.listing.gender_type}` : 'Campur'}
                                    </div>
                                </td>
                                <td className="px-5 py-4">
                                    <div>Mulai: {booking.start_date}</div>
                                    <div className="text-slate-400">Sampai: {booking.end_date}</div>
                                </td>
                                <td className="px-5 py-4 font-semibold">
                                    {booking.duration_months} Bulan
                                </td>
                                <td className="px-5 py-4 text-right font-bold text-slate-900">
                                    {booking.formatted_total_amount}
                                </td>
                            </tr>
                            {booking.deposit_amount > 0 && (
                                <tr>
                                    <td colSpan={3} className="px-5 py-3 text-slate-600">
                                        Deposit Jaminan (Akan dikembalikan di akhir sewa)
                                    </td>
                                    <td className="px-5 py-3 text-right font-semibold text-slate-900">
                                        {booking.formatted_deposit_amount}
                                    </td>
                                </tr>
                            )}
                            <tr>
                                <td colSpan={3} className="px-5 py-3 text-slate-600">
                                    Biaya Layanan Platform
                                </td>
                                <td className="px-5 py-3 text-right font-semibold text-emerald-700">
                                    Gratis (Rp 0)
                                </td>
                            </tr>
                        </tbody>
                        <tfoot className="bg-slate-50/80 font-bold text-slate-900 border-t-2 border-slate-200">
                            <tr>
                                <td colSpan={3} className="px-5 py-4 text-sm font-black">
                                    TOTAL TAGIHAN
                                </td>
                                <td className="px-5 py-4 text-right text-base font-black text-[#FF6565]">
                                    {booking.formatted_grand_total}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {/* Verification Notice & Footer */}
                <div className="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                    <div>
                        <div className="font-bold text-slate-800">Sistem Verifikasi Pusat Kost UNNES</div>
                        <div className="text-[11px] text-slate-400">
                            Invoice digital ini sah dan diterbitkan secara otomatis oleh platform Pusat Kost UNNES.
                        </div>
                    </div>
                    <div className="text-center sm:text-right text-[11px] text-slate-400">
                        Kawasan Kampus Universitas Negeri Semarang<br />
                        Sekaran, Gunungpati, Kota Semarang
                    </div>
                </div>
            </div>
        </div>
    );
}
