import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';
import { formatRupiah } from '@/lib/formatRupiah';

interface PendingOwner {
    id: string;
    name: string;
    email: string;
    phone: string | null;
    created_at: string;
    owner_verification_status: string;
}

interface RecentReview {
    id: string;
    rating: number;
    comment: string;
    created_at: string;
    user?: { id: string; name: string; email: string };
    listing?: { id: string; name: string; slug: string };
}

interface DashboardProps {
    stats: {
        total_listings: number;
        published_listings: number;
        total_owners: number;
        pending_owners_count: number;
        total_bookings: number;
        total_revenue: number;
        total_reviews: number;
        pending_reviews_count?: number;
        verified_payouts_count?: number;
    };
    pending_owners: PendingOwner[];
    recent_reviews: RecentReview[];
}

export default function Dashboard({ stats, pending_owners, recent_reviews }: DashboardProps) {
    const handleVerify = (ownerId: string, ownerName: string) => {
        if (confirm(`Verifikasi pemilik kos ${ownerName}?`)) {
            router.post(`/admin/owners/${ownerId}/verify`, {}, {
                preserveScroll: true,
                preserveState: true,
            });
        }
    };

    const handleReject = (ownerId: string, ownerName: string) => {
        if (confirm(`Tolak status verifikasi ${ownerName}?`)) {
            router.post(`/admin/owners/${ownerId}/reject`, {}, {
                preserveScroll: true,
                preserveState: true,
            });
        }
    };


    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <Head title="Admin Dashboard - Pusat Kost UNNES" />

            {/* Header Title */}
            <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <div className="flex items-center gap-2">
                        <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700">
                            Super Admin
                        </span>
                        <h1 className="text-2xl font-black text-slate-950 tracking-tight">
                            Panel Administrator
                        </h1>
                    </div>
                    <p className="text-sm text-slate-500 mt-1">
                        Pusat kendali, verifikasi pemilik properti, moderasi ulasan, dan pelepasan dana (payout) Pusat Kost UNNES.
                    </p>
                </div>

                {/* Quick Navigation Tabs */}
                <div className="flex flex-wrap items-center gap-2">
                    <Link href="/admin/payments">
                        <Button variant="primary" size="sm" className="bg-slate-900 hover:bg-black font-bold shadow-xs">
                            💳 Pembayaran & Payout {stats.pending_reviews_count ? `(${stats.pending_reviews_count})` : ''}
                        </Button>
                    </Link>
                    <Link href="/admin/owners">
                        <Button variant="outline" size="sm">
                            Kelola Pemilik ({stats.total_owners})
                        </Button>
                    </Link>
                    <Link href="/admin/reviews">
                        <Button variant="outline" size="sm">
                            Moderasi Ulasan ({stats.total_reviews})
                        </Button>
                    </Link>
                </div>
            </div>

            {/* Operational Alert Banner if Payments need Contact */}
            {((stats.pending_reviews_count ?? 0) > 0 || (stats.verified_payouts_count ?? 0) > 0) && (
                <div className="my-6 p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
                    <div className="flex items-center gap-3">
                        <div className="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center text-lg text-amber-800 shrink-0">
                            ⚡
                        </div>
                        <div>
                            <div className="font-bold text-xs text-amber-950">
                                Antrean Operasional Pembayaran & Payout
                            </div>
                            <div className="text-[11px] text-amber-800 mt-0.5">
                                {stats.pending_reviews_count ?? 0} transaksi butuh konfirmasi WhatsApp 2-arah • {stats.verified_payouts_count ?? 0} dana siap disalurkan ke rekening owner.
                            </div>
                        </div>
                    </div>
                    <Link href="/admin/payments">
                        <Button variant="primary" size="sm" className="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shrink-0">
                            Buka Antrean Review →
                        </Button>
                    </Link>
                </div>
            )}

            {/* Metrics KPI Grid */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 my-6">
                <div className="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <div className="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Total Kos Terdaftar
                    </div>
                    <div className="mt-2 flex items-baseline justify-between">
                        <span className="text-2xl font-black text-slate-950">{stats.total_listings}</span>
                        <span className="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/60">
                            {stats.published_listings} Tayang
                        </span>
                    </div>
                </div>

                <div className="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <div className="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Verifikasi Pemilik
                    </div>
                    <div className="mt-2 flex items-baseline justify-between">
                        <span className="text-2xl font-black text-slate-950">{stats.total_owners}</span>
                        {stats.pending_owners_count > 0 ? (
                            <span className="text-xs font-bold text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full border border-amber-200 animate-pulse">
                                {stats.pending_owners_count} Menunggu
                            </span>
                        ) : (
                            <span className="text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                0 Menunggu
                            </span>
                        )}
                    </div>
                </div>

                <div className="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <div className="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Total Transaksi Sewa
                    </div>
                    <div className="mt-2 flex items-baseline justify-between">
                        <span className="text-2xl font-black text-slate-950">{stats.total_bookings}</span>
                        <span className="text-xs font-medium text-slate-500">Pemesanan</span>
                    </div>
                </div>

                <div className="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
                    <div className="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Volume Transaksi
                    </div>
                    <div className="mt-2 flex items-baseline justify-between">
                        <span className="text-xl font-black text-[#FF6565]">
                            {formatRupiah(stats.total_revenue)}
                        </span>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {/* Antrean Verifikasi Pemilik */}
                <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                    <div className="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <div>
                            <h2 className="text-base font-bold text-slate-900">
                                Antrean Verifikasi Pemilik
                            </h2>
                            <p className="text-xs text-slate-500">
                                Pemilik kos yang mendaftar dan menunggu persetujuan admin
                            </p>
                        </div>
                        <Link
                            href="/admin/owners?status=pending"
                            className="text-xs font-semibold text-[#FF6565] hover:underline"
                        >
                            Lihat Semua →
                        </Link>
                    </div>

                    {pending_owners.length > 0 ? (
                        <div className="space-y-3">
                            {pending_owners.map((owner) => (
                                <div
                                    key={owner.id}
                                    className="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100"
                                >
                                    <div className="min-w-0 flex-1 pr-4">
                                        <div className="flex items-center gap-2">
                                            <span className="font-bold text-sm text-slate-900 truncate">
                                                {owner.name}
                                            </span>
                                            <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                                Pending
                                            </span>
                                        </div>
                                        <div className="text-xs text-slate-500 truncate mt-0.5">
                                            {owner.email} • {owner.phone || 'Tanpa no. HP'}
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-1.5 shrink-0">
                                        <button
                                            type="button"
                                            onClick={() => handleVerify(owner.id, owner.name)}
                                            className="px-3 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors cursor-pointer shadow-xs"
                                        >
                                            Verifikasi
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => handleReject(owner.id, owner.name)}
                                            className="px-2.5 py-1.5 text-xs font-semibold bg-white hover:bg-rose-50 text-rose-600 border border-slate-200 rounded-lg transition-colors cursor-pointer"
                                        >
                                            Tolak
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12 text-slate-400 text-xs">
                            <span className="text-2xl block mb-2">🎉</span>
                            Semua pemilik kos telah diverifikasi! Tidak ada antrean pending.
                        </div>
                    )}
                </div>

                {/* Ulasan Terbaru */}
                <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                    <div className="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                        <div>
                            <h2 className="text-base font-bold text-slate-900">
                                Ulasan Mahasiswa Terbaru
                            </h2>
                            <p className="text-xs text-slate-500">
                                Ulasan dan rating yang baru saja dikirimkan oleh tenant
                            </p>
                        </div>
                        <Link
                            href="/admin/reviews"
                            className="text-xs font-semibold text-[#FF6565] hover:underline"
                        >
                            Kelola Semua →
                        </Link>
                    </div>

                    {recent_reviews.length > 0 ? (
                        <div className="space-y-3">
                            {recent_reviews.map((rev) => (
                                <div
                                    key={rev.id}
                                    className="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs"
                                >
                                    <div className="flex items-center justify-between mb-1">
                                        <div className="flex items-center gap-1.5">
                                            <span className="font-bold text-slate-900">
                                                {rev.user?.name || 'Mahasiswa'}
                                            </span>
                                            <span className="text-slate-400">di</span>
                                            <span className="font-semibold text-slate-700 truncate max-w-[160px]">
                                                {rev.listing?.name || 'Kos'}
                                            </span>
                                        </div>
                                        <div className="flex items-center text-amber-500 font-bold">
                                            ★ {rev.rating}/5
                                        </div>
                                    </div>
                                    <p className="text-slate-600 line-clamp-2 italic">
                                        &quot;{rev.comment}&quot;
                                    </p>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12 text-slate-400 text-xs">
                            Belum ada ulasan yang masuk.
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

Dashboard.layout = (page: React.ReactNode) => <PublicLayout>{page}</PublicLayout>;
