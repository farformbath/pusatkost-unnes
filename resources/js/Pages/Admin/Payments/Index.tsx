import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';
import { PaymentDetail } from '@/types/payment';
import { SortDropdown, SortOption } from '@/components/ui/SortDropdown';
import { formatRupiah } from '@/lib/formatRupiah';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaymentsIndexProps {
    payments: {
        data: PaymentDetail[];
        current_page: number;
        last_page: number;
        total: number;
        links: PaginationLink[];
    };
    filters: {
        search?: string;
        status?: string;
        review_status?: string;
    };
    metrics: {
        total_volume: number;
        pending_review: number;
        verified_ready: number;
        completed_payouts: number;
    };
}

export default function Index({ payments, filters, metrics }: PaymentsIndexProps) {
    const [search, setSearch] = useState(filters.search || '');
    const [status, setStatus] = useState(filters.status || '');
    const [reviewStatus, setReviewStatus] = useState(filters.review_status || '');

    const handleFilter = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/admin/payments', {
            search: search || undefined,
            status: status || undefined,
            review_status: reviewStatus || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };


    const getPaymentBadge = (st: string) => {
        switch (st) {
            case 'paid':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">✓ LUNAS</span>;
            case 'pending':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">⏳ PENDING</span>;
            case 'failed':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">✕ GAGAL</span>;
            default:
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-50 text-slate-700 border border-slate-200">{st.toUpperCase()}</span>;
        }
    };

    const getReviewBadge = (st?: string) => {
        switch (st) {
            case 'approved':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">✓ Disetujui & Payout</span>;
            case 'verified':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">★ Terverifikasi (Siap Payout)</span>;
            case 'contacting':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">📞 Sedang Dihubungi</span>;
            case 'rejected':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800">✕ Ditolak</span>;
            case 'pending':
            default:
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">⏳ Menunggu Review</span>;
        }
    };

    const paymentStatusOptions: SortOption[] = [
        { value: '', label: 'Semua Status Bayar', sublabel: 'Lunas, pending, & gagal', icon: '💳' },
        { value: 'paid', label: '✓ Lunas (Paid)', sublabel: 'Pembayaran sudah terverifikasi', icon: '🟢' },
        { value: 'pending', label: '⏳ Menunggu Bayar', sublabel: 'Invoice belum diselesaikan', icon: '🟡' },
        { value: 'failed', label: '✕ Pembayaran Gagal', sublabel: 'Expired atau transaksi gagal', icon: '🔴' },
    ];

    const reviewStatusOptions: SortOption[] = [
        { value: '', label: 'Semua Status Review', sublabel: 'Seluruh tahap moderasi admin', icon: '📋' },
        { value: 'pending', label: '⏳ Menunggu Review', sublabel: 'Baru lunas, belum diverifikasi', icon: '🟡' },
        { value: 'contacting', label: '📞 Sedang Dihubungi', sublabel: 'Proses kontak tenant / owner', icon: '🔵' },
        { value: 'verified', label: '✓ Terverifikasi', sublabel: 'Kedua pihak ok, siap payout', icon: '🟢' },
        { value: 'approved', label: '🚀 Disetujui & Payout', sublabel: 'Dana berhasil disalurkan', icon: '🟣' },
        { value: 'rejected', label: '✕ Ditolak', sublabel: 'Pembatalan & pengembalian', icon: '🔴' },
    ];

    return (
        <PublicLayout>
            <Head title="Admin Payment & Payout Review | Pusat Kost UNNES" />

            <div className="max-w-7xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 mb-1">
                            <Link href="/admin/dashboard" className="text-xs font-semibold text-slate-500 hover:text-slate-900">
                                ← Kembali ke Dashboard Admin
                            </Link>
                        </div>
                        <h1 className="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Verifikasi Pembayaran & Payout Dana
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500">
                            Kelola alur verifikasi dua arah (Tenant & Owner) sebelum pelepasan dana sewa kos.
                        </p>
                    </div>
                </div>

                {/* Metric Summary Cards */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                        <div className="text-xs font-semibold text-slate-500 mb-1">Total Volume Transaksi</div>
                        <div className="text-xl sm:text-2xl font-black text-slate-900">{formatRupiah(metrics.total_volume)}</div>
                        <div className="text-[11px] text-emerald-600 font-medium mt-1">Lunas terverifikasi</div>
                    </div>
                    <div className="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                        <div className="text-xs font-semibold text-slate-500 mb-1">Perlu Verifikasi Kontak</div>
                        <div className="text-xl sm:text-2xl font-black text-amber-600">{metrics.pending_review}</div>
                        <div className="text-[11px] text-slate-500 font-medium mt-1">Tenant / Owner</div>
                    </div>
                    <div className="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                        <div className="text-xs font-semibold text-slate-500 mb-1">Siap Payout (Verified)</div>
                        <div className="text-xl sm:text-2xl font-black text-blue-600">{metrics.verified_ready}</div>
                        <div className="text-[11px] text-slate-500 font-medium mt-1">Kedua pihak terkonfirmasi</div>
                    </div>
                    <div className="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                        <div className="text-xs font-semibold text-slate-500 mb-1">Payout Sukses</div>
                        <div className="text-xl sm:text-2xl font-black text-emerald-600">{metrics.completed_payouts}</div>
                        <div className="text-[11px] text-slate-500 font-medium mt-1">Tersalurkan ke rekening owner</div>
                    </div>
                </div>

                {/* Filter & Search Bar */}
                <form onSubmit={handleFilter} className="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col md:flex-row gap-3 items-center">
                    <div className="flex-1 w-full">
                        <input
                            type="text"
                            placeholder="Cari nomor invoice, kode booking, atau nama tenant..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full text-xs sm:text-sm rounded-xl border border-slate-200 px-3.5 py-2 focus:border-slate-900 focus:ring-slate-900"
                        />
                    </div>
                    <div className="w-full md:w-auto">
                        <SortDropdown
                            value={status}
                            onChange={(val) => setStatus(val)}
                            options={paymentStatusOptions}
                            className="w-full md:w-auto"
                        />
                    </div>
                    <div className="w-full md:w-auto">
                        <SortDropdown
                            value={reviewStatus}
                            onChange={(val) => setReviewStatus(val)}
                            options={reviewStatusOptions}
                            className="w-full md:w-auto"
                        />
                    </div>
                    <Button type="submit" variant="primary" size="sm" className="w-full md:w-auto font-bold">
                        Filter
                    </Button>
                </form>

                {/* Transactions Table */}
                <div className="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs sm:text-sm">
                            <thead className="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <tr>
                                    <th className="px-6 py-4">Invoice & Booking</th>
                                    <th className="px-6 py-4">Penyewa & Kos</th>
                                    <th className="px-6 py-4">Nominal</th>
                                    <th className="px-6 py-4">Status Bayar</th>
                                    <th className="px-6 py-4">Status Review</th>
                                    <th className="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {payments.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={6} className="text-center py-12 text-slate-400">
                                            Tidak ada transaksi pembayaran yang sesuai kriteria filter.
                                        </td>
                                    </tr>
                                ) : (
                                    payments.data.map((p) => (
                                        <tr key={p.id} className="hover:bg-slate-50/70 transition-colors">
                                            <td className="px-6 py-4">
                                                <div className="font-mono font-bold text-slate-900 text-xs">
                                                    {p.invoice_number}
                                                </div>
                                                <div className="text-[11px] text-slate-500 mt-0.5">
                                                    Booking: <span className="font-mono font-medium">#{p.booking?.booking_code}</span>
                                                </div>
                                                <div className="text-[10px] text-slate-400 mt-0.5">
                                                    {new Date(p.created_at).toLocaleDateString('id-ID', {
                                                        day: 'numeric',
                                                        month: 'short',
                                                        year: 'numeric',
                                                    })}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="font-bold text-slate-900">
                                                    {p.booking?.tenant_name || p.booking?.user?.name}
                                                </div>
                                                <div className="text-[11px] text-slate-500">
                                                    {p.booking?.listing?.name} • Kamar {p.booking?.room?.name}
                                                </div>
                                                <div className="text-[10px] text-slate-400">
                                                    Owner: {p.booking?.listing?.owner?.name || '-'}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="font-black text-slate-900">
                                                    {p.formatted_gross_amount}
                                                </div>
                                                <div className="text-[10px] text-slate-400 uppercase font-mono">
                                                    {p.payment_channel}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                {getPaymentBadge(p.transaction_status)}
                                            </td>
                                            <td className="px-6 py-4">
                                                {getReviewBadge(p.review?.status)}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link href={`/admin/payments/${p.id}`}>
                                                    <Button variant="outline" size="sm" className="font-bold text-xs">
                                                        Detail & Verifikasi →
                                                    </Button>
                                                </Link>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {payments.links.length > 3 && (
                        <div className="px-6 py-4 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between">
                            <div className="text-xs text-slate-500">
                                Menampilkan <span className="font-bold">{payments.data.length}</span> dari <span className="font-bold">{payments.total}</span> transaksi
                            </div>
                            <div className="flex gap-1">
                                {payments.links.map((link, idx) => (
                                    <Link
                                        key={idx}
                                        href={link.url || '#'}
                                        preserveScroll
                                        preserveState
                                        className={`px-3 py-1.5 text-xs rounded-lg font-medium transition-colors ${
                                            link.active
                                                ? 'bg-slate-900 text-white'
                                                : link.url
                                                ? 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'
                                                : 'text-slate-300 pointer-events-none'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
