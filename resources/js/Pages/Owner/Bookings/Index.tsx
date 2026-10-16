import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Booking, BookingStatus } from '@/types/booking';
import { PaginatedData } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Badge, GenderBadge } from '@/components/ui/Badge';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';

interface IndexProps {
    bookings: PaginatedData<Booking>;
    filters: {
        status: string;
    };
}

export default function Index({ bookings, filters }: IndexProps) {
    const [selectedBookingForReject, setSelectedBookingForReject] = useState<Booking | null>(null);
    const [rejectReason, setRejectReason] = useState('');
    const [processing, setProcessing] = useState(false);

    const handleFilterStatus = (status: string) => {
        router.get(
            '/owner/bookings',
            { status: status === filters.status ? '' : status },
            { preserveState: true, preserveScroll: true }
        );
    };

    const handleApprove = (booking: Booking) => {
        if (confirm(`Setujui pengajuan sewa kamar dari ${booking.tenant_name} (${booking.booking_code})?`)) {
            setProcessing(true);
            router.post(`/owner/bookings/${booking.id}/approve`, {}, {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setProcessing(false),
            });
        }
    };

    const handleRejectSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedBookingForReject) return;

        setProcessing(true);
        router.post(`/owner/bookings/${selectedBookingForReject.id}/reject`, {
            reason: rejectReason,
        }, {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => {
                setProcessing(false);
                setSelectedBookingForReject(null);
                setRejectReason('');
            },
        });
    };

    const getStatusVariant = (status: BookingStatus) => {
        switch (status) {
            case 'approved':
                return 'published';
            case 'pending':
                return 'draft';
            case 'rejected':
            case 'cancelled':
            case 'expired':
                return 'maintenance';
            case 'completed':
                return 'available';
            default:
                return 'neutral';
        }
    };

    const statuses: { label: string; value: string }[] = [
        { label: 'Semua', value: '' },
        { label: 'Menunggu Konfirmasi', value: 'pending' },
        { label: 'Disetujui', value: 'approved' },
        { label: 'Ditolak', value: 'rejected' },
        { label: 'Dibatalkan', value: 'cancelled' },
    ];

    return (
        <PublicLayout>
            <Head title="Manajemen Pengajuan Booking | Dashboard Pemilik Kos UNNES" />

            <div className="space-y-6">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight">
                            Manajemen Pengajuan Sewa Masuk
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500">
                            Konfirmasi atau tolak pesanan sewa kamar dari calon mahasiswa dan penyewa UNNES.
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link href="/owner/payouts">
                            <Button variant="primary" size="sm" className="bg-slate-900 hover:bg-black font-bold">
                                💸 Riwayat Payout
                            </Button>
                        </Link>
                        <Link href="/owner/kos">
                            <Button variant="outline" size="sm">
                                🏢 Kelola Properti Kos
                            </Button>
                        </Link>
                    </div>
                </div>

                {/* Filter Tabs */}
                <div className="flex flex-wrap gap-2 pb-2">
                    {statuses.map((s) => (
                        <button
                            key={s.value}
                            onClick={() => handleFilterStatus(s.value)}
                            className={`px-3.5 py-1.5 text-xs font-semibold rounded-xl transition-colors cursor-pointer ${
                                filters.status === s.value
                                    ? 'bg-slate-900 text-white shadow-xs'
                                    : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'
                            }`}
                        >
                            {s.label}
                        </button>
                    ))}
                </div>

                {/* Bookings Table */}
                {bookings.data.length > 0 ? (
                    <div className="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div className="divide-y divide-slate-100">
                            {bookings.data.map((booking) => (
                                <div
                                    key={booking.id}
                                    className="p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-slate-50/60 transition-colors"
                                >
                                    <div className="space-y-2">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <span className="font-mono font-bold text-slate-900">
                                                #{booking.booking_code}
                                            </span>
                                            <Badge variant={getStatusVariant(booking.status)}>
                                                {booking.status.toUpperCase()}
                                            </Badge>
                                            <Badge variant={booking.payment_status === 'paid' ? 'published' : 'draft'}>
                                                Bayar: {booking.payment_status.toUpperCase()}
                                            </Badge>
                                            {booking.listing && <GenderBadge gender={booking.listing.gender_type} />}
                                        </div>

                                        <div className="text-xs text-slate-700 space-y-0.5">
                                            <p>
                                                <strong>{booking.tenant_name}</strong> ({booking.tenant_phone})
                                            </p>
                                            <p className="text-slate-500">
                                                Kos: <strong>{booking.listing?.name}</strong> • Kamar: <strong>{booking.room?.name}</strong>
                                            </p>
                                            <p className="text-slate-500">
                                                Mulai: <strong>{booking.start_date}</strong> • Durasi: <strong>{booking.duration_months} Bulan</strong>
                                            </p>
                                        </div>

                                        <div className="text-xs font-bold text-[#FF6565]">
                                            Total: {booking.formatted_grand_total}
                                        </div>
                                    </div>

                                    {/* Action Buttons */}
                                    <div className="flex flex-wrap items-center gap-2 self-end lg:self-center">
                                        <Link href={`/booking/${booking.id}`} target="_blank">
                                            <Button variant="ghost" size="sm">
                                                Lihat Rincian Tagihan ↗
                                            </Button>
                                        </Link>

                                        {booking.status === 'pending' && (
                                            <>
                                                <Button
                                                    variant="primary"
                                                    size="sm"
                                                    disabled={processing}
                                                    onClick={() => handleApprove(booking)}
                                                >
                                                    ✓ Setujui
                                                </Button>

                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    className="text-red-600 hover:bg-red-50"
                                                    disabled={processing}
                                                    onClick={() => setSelectedBookingForReject(booking)}
                                                >
                                                    ✕ Tolak
                                                </Button>
                                            </>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                ) : (
                    <div className="text-center py-16 px-4 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                        <div className="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center text-xl">
                            📋
                        </div>
                        <h3 className="text-base font-bold text-slate-800">
                            Tidak ada pengajuan sewa
                        </h3>
                        <p className="text-xs text-slate-500 max-w-sm mx-auto">
                            Belum ada pemesanan yang masuk dengan status filter saat ini.
                        </p>
                    </div>
                )}
            </div>

            {/* Reject Modal */}
            <Modal
                isOpen={!!selectedBookingForReject}
                onClose={() => setSelectedBookingForReject(null)}
                title="Konfirmasi Tolak Pengajuan Sewa"
                maxWidth="md"
            >
                {selectedBookingForReject && (
                    <form onSubmit={handleRejectSubmit} className="space-y-4">
                        <p className="text-xs text-slate-600">
                            Apakah Anda yakin ingin menolak pesanan <strong>#{selectedBookingForReject.booking_code}</strong> dari <strong>{selectedBookingForReject.tenant_name}</strong>? Unit kamar akan otomatis dikembalikan ke stok ketersediaan publik.
                        </p>

                        <div className="space-y-1.5">
                            <label className="block text-xs font-semibold text-slate-700">
                                Alasan Penolakan (Opsional)
                            </label>
                            <textarea
                                rows={3}
                                className="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 focus:border-red-500 focus:outline-none"
                                placeholder="Contoh: Kamar sedang dilakukan perbaikan plafon..."
                                value={rejectReason}
                                onChange={(e) => setRejectReason(e.target.value)}
                            />
                        </div>

                        <div className="pt-3 border-t border-slate-100 flex justify-end gap-2">
                            <Button variant="outline" size="sm" type="button" onClick={() => setSelectedBookingForReject(null)}>
                                Batal
                            </Button>
                            <Button variant="primary" size="sm" type="submit" isLoading={processing}>
                                Tolak & Kembalikan Stok Kamar
                            </Button>
                        </div>
                    </form>
                )}
            </Modal>
        </PublicLayout>
    );
}
