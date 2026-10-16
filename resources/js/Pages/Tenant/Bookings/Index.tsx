import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Booking } from '@/types/booking';
import { Button } from '@/components/ui/Button';
import { GenderBadge } from '@/components/ui/Badge';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface TenantBookingsProps {
    bookings: {
        data: Booking[];
        current_page: number;
        last_page: number;
        total: number;
        links: PaginationLink[];
    };
    counts: {
        all: number;
        pending: number;
        active: number;
        completed: number;
        cancelled: number;
    };
    current_status: string;
}

export default function Index({ bookings, counts, current_status }: TenantBookingsProps) {
    const tabs = [
        { id: 'all', label: 'Semua Booking', count: counts.all },
        { id: 'pending', label: 'Menunggu Bayar', count: counts.pending },
        { id: 'active', label: 'Aktif / Lunas', count: counts.active },
        { id: 'completed', label: 'Selesai', count: counts.completed },
        { id: 'cancelled', label: 'Dibatalkan', count: counts.cancelled },
    ];

    const getStatusBadge = (booking: Booking) => {
        if (booking.status === 'cancelled' || booking.payment_status === 'cancelled') {
            return (
                <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                    Dibatalkan
                </span>
            );
        }
        if (booking.payment_status === 'paid') {
            return (
                <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Lunas & Aktif
                </span>
            );
        }
        return (
            <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1">
                <span className="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                Menunggu Pembayaran
            </span>
        );
    };

    return (
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <Head title="Riwayat Booking Kos Saya - UNNESKost" />

            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
                        Riwayat & Daftar Booking Saya
                    </h1>
                    <p className="text-xs text-slate-500 mt-1">
                        Pantau status pemesanan kamar, selesaikan pembayaran, dan unduh kwitansi sewa resmi.
                    </p>
                </div>
                <Link href="/kos">
                    <Button variant="outline" size="sm">
                        🔍 Cari Kos Lainnya
                    </Button>
                </Link>
            </div>

            {/* Status Filter Tabs */}
            <div className="flex flex-wrap gap-2 my-6">
                {tabs.map((tab) => (
                    <Link
                        key={tab.id}
                        href={`/tenant/bookings${tab.id !== 'all' ? `?status=${tab.id}` : ''}`}
                        preserveScroll
                        preserveState
                        className={`px-4 py-2 rounded-xl text-xs font-semibold transition-all ${
                            current_status === tab.id
                                ? 'bg-slate-900 text-white shadow-xs'
                                : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'
                        }`}
                    >
                        {tab.label} ({tab.count})
                    </Link>
                ))}
            </div>

            {/* Bookings List */}
            {bookings.data.length > 0 ? (
                <div className="space-y-4">
                    {bookings.data.map((booking) => {
                        const imageUrl = booking.listing?.primary_image?.url || '/images/logo.jpg';
                        const isPending = booking.payment_status === 'pending' && booking.status !== 'cancelled';
                        const isPaid = booking.payment_status === 'paid';

                        return (
                            <div
                                key={booking.id}
                                className="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 transition-all hover:shadow-md"
                            >
                                <div className="flex flex-col md:flex-row gap-5">
                                    {/* Thumbnail */}
                                    <div className="w-full md:w-48 h-32 rounded-xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200/60">
                                        <img
                                            src={imageUrl}
                                            alt={booking.listing?.name || 'Kos'}
                                            className="w-full h-full object-cover"
                                        />
                                    </div>

                                    {/* Info Body */}
                                    <div className="flex-1 min-w-0 flex flex-col justify-between">
                                        <div>
                                            <div className="flex flex-wrap items-center justify-between gap-2 mb-2">
                                                <div className="flex items-center gap-2">
                                                    <span className="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-md">
                                                        #{booking.booking_code}
                                                    </span>
                                                    {booking.listing && (
                                                        <GenderBadge gender={booking.listing.gender_type} />
                                                    )}
                                                </div>
                                                {getStatusBadge(booking)}
                                            </div>

                                            <h2 className="text-base font-bold text-slate-900 truncate">
                                                {booking.listing?.name || 'Kos Sekitar UNNES'}
                                            </h2>
                                            <p className="text-xs text-slate-500 mt-0.5">
                                                Kamar: <strong className="text-slate-700">{booking.room?.name || 'Tipe Kamar'}</strong> • Durasi: {booking.duration_months} Bulan ({booking.start_date} s/d {booking.end_date})
                                            </p>
                                        </div>

                                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100 mt-4">
                                            <div>
                                                <span className="text-[11px] text-slate-400 block">Total Pembayaran</span>
                                                <span className="text-base font-extrabold text-[#FF6565]">
                                                    {booking.formatted_grand_total}
                                                </span>
                                            </div>

                                            <div className="flex flex-wrap items-center gap-2">
                                                {isPending && (
                                                    <Link href={`/booking/${booking.id}`}>
                                                        <Button variant="primary" size="sm" className="font-bold">
                                                            ⚡ Bayar Sekarang
                                                        </Button>
                                                    </Link>
                                                )}

                                                {isPaid && (
                                                    <Link href={`/booking/${booking.id}/invoice`}>
                                                        <Button variant="outline" size="sm" className="border-slate-200">
                                                            📄 Cetak Invoice / Kwitansi
                                                        </Button>
                                                    </Link>
                                                )}

                                                <Link href={`/booking/${booking.id}`}>
                                                    <Button variant="ghost" size="sm">
                                                        Rincian
                                                    </Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            ) : (
                <div className="text-center py-20 px-4 bg-white rounded-3xl border border-slate-200 shadow-xs">
                    <div className="text-4xl mb-3">🏢</div>
                    <h3 className="font-bold text-slate-800 text-base mb-1">
                        Belum Ada Booking Pada Kategori Ini
                    </h3>
                    <p className="text-xs text-slate-500 max-w-sm mx-auto mb-6">
                        Temukan berbagai pilihan kamar kos nyaman di sekitar kampus UNNES Sekaran, Banaran, dan Gunungpati.
                    </p>
                    <Link href="/kos">
                        <Button variant="primary" size="md">
                            Mulai Cari Kos Sekarang →
                        </Button>
                    </Link>
                </div>
            )}

            {/* Pagination */}
            {bookings.last_page > 1 && (
                <div className="flex justify-center items-center gap-1.5 pt-6 pb-8">
                    {bookings.links.map((link, idx) => {
                        if (!link.url) {
                            return (
                                <span
                                    key={idx}
                                    className="px-3 py-1.5 text-xs text-slate-400 rounded-md"
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            );
                        }

                        return (
                            <Link
                                key={idx}
                                href={link.url}
                                preserveScroll
                                preserveState
                                className={`px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-colors ${
                                    link.active
                                        ? 'bg-[#FF6565] text-white shadow-xs'
                                        : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        );
                    })}
                </div>
            )}
        </div>
    );
}

Index.layout = (page: React.ReactNode) => <PublicLayout>{page}</PublicLayout>;
