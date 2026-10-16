import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Booking } from '@/types/booking';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Badge, GenderBadge } from '@/components/ui/Badge';
import { Button } from '@/components/ui/Button';

interface ShowProps {
    booking: Booking;
}

export default function Show({ booking }: ShowProps) {
    const [timeLeft, setTimeLeft] = useState<string>('');
    const [copied, setCopied] = useState(false);
    const [isPaying, setIsPaying] = useState(false);
    const [isCancelling, setIsCancelling] = useState(false);

    const latestPayment = booking.latest_payment || (booking.payments && booking.payments.length > 0 ? booking.payments[0] : null);
    const vaNumber = latestPayment?.payment_reference || '88001234567890';

    // 24-hour expiration countdown
    useEffect(() => {
        const updateTimer = () => {
            const expireDate = new Date(booking.expired_at).getTime();
            const now = new Date().getTime();
            const diff = expireDate - now;

            if (diff <= 0 || booking.payment_status === 'paid' || booking.status === 'cancelled') {
                setTimeLeft('Selesai / Kadaluarsa');
                return;
            }

            const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((diff / (1000 * 60)) % 60);
            const seconds = Math.floor((diff / 1000) % 60);

            setTimeLeft(`${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
        };

        updateTimer();
        const interval = setInterval(updateTimer, 1000);
        return () => clearInterval(interval);
    }, [booking.expired_at, booking.payment_status, booking.status]);

    const handleCopyVA = () => {
        navigator.clipboard.writeText(vaNumber);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    const handleSimulatePayment = () => {
        if (confirm('Simulasikan pembayaran lunas untuk pesanan ini?')) {
            setIsPaying(true);
            router.post(`/booking/${booking.id}/payment`, {
                payment_channel: 'bca_va',
            }, {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setIsPaying(false),
            });
        }
    };

    const handleCancelBooking = () => {
        if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Unit kamar akan dikembalikan ke ketersediaan umum.')) {
            setIsCancelling(true);
            router.post(`/booking/${booking.id}/cancel`, {}, {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setIsCancelling(false),
            });
        }
    };

    const getStatusVariant = (status: string) => {
        switch (status) {
            case 'approved':
            case 'paid':
                return 'published';
            case 'pending':
                return 'draft';
            case 'cancelled':
            case 'rejected':
            case 'expired':
                return 'maintenance';
            default:
                return 'neutral';
        }
    };

    return (
        <PublicLayout>
            <Head title={`Tagihan #${booking.booking_code} | Pusat Kost UNNES`} />

            <div className="max-w-5xl mx-auto space-y-6">
                {/* Booking Status Card */}
                <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="space-y-1.5">
                        <div className="flex flex-wrap items-center gap-2">
                            <h1 className="text-xl sm:text-2xl font-black text-slate-900">
                                Pesanan #{booking.booking_code}
                            </h1>
                            <Badge variant={getStatusVariant(booking.status)} size="md">
                                Status: {booking.status.toUpperCase()}
                            </Badge>
                            <Badge variant={getStatusVariant(booking.payment_status)} size="md">
                                Pembayaran: {booking.payment_status.toUpperCase()}
                            </Badge>
                        </div>
                        <p className="text-xs text-slate-500">
                            Dibuat pada {new Date(booking.created_at).toLocaleString('id-ID')}
                        </p>
                    </div>

                    {booking.payment_status === 'pending' && booking.status === 'pending' && (
                        <div className="p-3 bg-amber-50 rounded-2xl border border-amber-200 text-center sm:text-right">
                            <span className="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">
                                Batas Waktu Pembayaran
                            </span>
                            <span className="text-lg font-black text-amber-900 font-mono">
                                {timeLeft || 'Memuat...'}
                            </span>
                        </div>
                    )}
                </div>

                {/* 2-Column Grid: Payment Instructions & Booking Summary */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Left Column: Virtual Account & Actions */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Payment Box */}
                        {booking.payment_status === 'pending' && booking.status !== 'cancelled' ? (
                            <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                                <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center justify-between">
                                    <span>Instruksi Pembayaran Digital</span>
                                    <span className="text-xs font-normal text-slate-500">BCA Virtual Account</span>
                                </h2>

                                <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200/60 space-y-3">
                                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <span className="text-xs text-slate-500 block">Nomor Rekening Virtual Account</span>
                                            <span className="text-xl font-mono font-black text-slate-900 tracking-wider">
                                                {vaNumber}
                                            </span>
                                        </div>
                                        <Button variant="outline" size="sm" onClick={handleCopyVA}>
                                            {copied ? '✓ Tersalin' : '📋 Salin Nomor'}
                                        </Button>
                                    </div>

                                    <div className="pt-2 border-t border-slate-200/60 flex justify-between items-baseline">
                                        <span className="text-xs text-slate-500">Total Tagihan</span>
                                        <span className="text-lg font-black text-[#FF6565]">
                                            {booking.formatted_grand_total}
                                        </span>
                                    </div>
                                </div>

                                {/* Sandbox Simulation Notice */}
                                <div className="p-4 bg-rose-50/60 rounded-2xl border border-rose-200 text-xs text-rose-900 space-y-2">
                                    <div className="flex items-center gap-1.5 font-bold text-rose-800">
                                        <span>🛠️ Mode Uji Coba Sandbox (Simulasi Pembayaran)</span>
                                    </div>
                                    <p className="text-[11px] leading-relaxed text-rose-700">
                                        Ini adalah simulator sistem pembayaran. Anda dapat menguji pelunasan pesanan secara instan untuk melihat transisi status kamar.
                                    </p>
                                    <Button
                                        variant="primary"
                                        size="md"
                                        className="w-full mt-2"
                                        isLoading={isPaying}
                                        disabled={isPaying}
                                        onClick={handleSimulatePayment}
                                    >
                                        ⚡ Simulasi Bayar Sekarang (Sandbox Test)
                                    </Button>
                                </div>

                                {/* Cancel Booking Action */}
                                <div className="pt-2 flex justify-end">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        className="text-slate-500 hover:text-red-600"
                                        isLoading={isCancelling}
                                        disabled={isCancelling}
                                        onClick={handleCancelBooking}
                                    >
                                        Batalkan Pesanan Ini
                                    </Button>
                                </div>
                            </div>
                        ) : (
                            /* Paid / Cancelled State Card */
                            <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                                <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                                    Status Tagihan Pembayaran
                                </h2>

                                {booking.payment_status === 'paid' ? (
                                    <div className="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-900 space-y-3">
                                        <div className="flex items-center gap-2 font-bold text-emerald-800 text-base">
                                            <span>✓</span>
                                            <span>Pembayaran Lunas Terverifikasi</span>
                                        </div>
                                        <p className="text-xs text-emerald-700">
                                            Kamar Anda telah resmi terpesan. Silakan tunjukkan bukti kode booking <strong>#{booking.booking_code}</strong> saat tiba di lokasi kos sekitar kampus UNNES.
                                        </p>
                                        <div className="pt-2 flex flex-wrap items-center gap-2">
                                            <Link href={`/booking/${booking.id}/invoice`}>
                                                <Button variant="outline" size="sm" className="bg-white text-slate-800 border-slate-200 shadow-xs">
                                                    📄 Invoice Pemesanan
                                                </Button>
                                            </Link>
                                            {booking.payment?.id && (
                                                <Link href={`/payment/${booking.payment.id}/receipt`} target="_blank">
                                                    <Button variant="primary" size="sm" className="bg-slate-900 hover:bg-black font-bold text-xs shadow-xs">
                                                        🖨️ Cetak Kuitansi Resmi
                                                    </Button>
                                                </Link>
                                            )}
                                        </div>
                                    </div>
                                ) : (
                                    <div className="p-4 bg-slate-100 rounded-2xl border border-slate-200 text-slate-700 space-y-1 text-xs">
                                        <p className="font-bold text-slate-900">Pemesanan Telah Dibatalkan / Kadaluarsa</p>
                                        <p>Unit kamar ini telah dikembalikan ke katalog publik.</p>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>

                    {/* Right Column: Room & Kos Information */}
                    <div className="space-y-6">
                        <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                            <h3 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                                Rincian Sewa Kamar
                            </h3>

                            {booking.listing && (
                                <div className="space-y-2 text-xs">
                                    <div className="flex items-center gap-2">
                                        <GenderBadge gender={booking.listing.gender_type} />
                                        <span className="font-semibold text-slate-700">Area {booking.listing.area}</span>
                                    </div>
                                    <h4 className="text-sm font-bold text-slate-900">{booking.listing.name}</h4>
                                    <p className="text-slate-500">{booking.listing.address}</p>
                                </div>
                            )}

                            {booking.room && (
                                <div className="p-3 bg-slate-50 rounded-2xl border border-slate-200/60 text-xs space-y-1">
                                    <span className="font-bold text-slate-900 block">{booking.room.name}</span>
                                    {booking.room.size && <span className="text-slate-500 block">Ukuran: {booking.room.size}</span>}
                                    <span className="text-[#FF6565] font-bold block">{booking.room.formatted_price_monthly} / bulan</span>
                                </div>
                            )}

                            <div className="border-t border-slate-100 pt-3 space-y-1.5 text-xs text-slate-600">
                                <div className="flex justify-between">
                                    <span>Penyewa:</span>
                                    <span className="font-semibold text-slate-800">{booking.tenant_name}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>No. WhatsApp:</span>
                                    <span className="font-semibold text-slate-800">{booking.tenant_phone}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Check-in:</span>
                                    <span className="font-semibold text-slate-800">{booking.start_date}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Durasi:</span>
                                    <span className="font-semibold text-slate-800">{booking.duration_months} Bulan</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Check-out:</span>
                                    <span className="font-semibold text-slate-800">{booking.end_date}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
