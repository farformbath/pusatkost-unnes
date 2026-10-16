import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';
import { Payout } from '@/types/payment';
import { formatRupiah } from '@/lib/formatRupiah';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface OwnerPayoutsIndexProps {
    payouts: {
        data: Payout[];
        current_page: number;
        last_page: number;
        total: number;
        links: PaginationLink[];
    };
    filters: {
        status?: string;
    };
    metrics: {
        total_received: number;
        pending_payouts: number;
        total_transactions: number;
    };
}

export default function Index({ payouts, metrics }: OwnerPayoutsIndexProps) {

    const getPayoutBadge = (st: string) => {
        switch (st) {
            case 'paid':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">✓ LUNAS DITRANSFER</span>;
            case 'processing':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">⏳ PROSES TRANSFER</span>;
            case 'failed':
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800">✕ GAGAL</span>;
            case 'pending':
            default:
                return <span className="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">⏳ MENUNGGU VERIFIKASI</span>;
        }
    };

    return (
        <PublicLayout>
            <Head title="Riwayat Payout & Pencairan Dana | Pusat Kost UNNES" />

            <div className="max-w-6xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 mb-1">
                            <Link href="/owner/bookings" className="text-xs font-semibold text-slate-500 hover:text-slate-900">
                                ← Kembali ke Manajemen Booking
                            </Link>
                        </div>
                        <h1 className="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Riwayat Payout & Pencairan Dana Sewa
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500">
                            Pantau seluruh penyaluran dana sewa yang telah diverifikasi dan dicairkan ke rekening Anda.
                        </p>
                    </div>
                </div>

                {/* Metrics */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div className="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                        <div className="text-xs font-semibold text-slate-500 mb-1">Total Dana Diterima</div>
                        <div className="text-2xl font-black text-emerald-700">{formatRupiah(metrics.total_received)}</div>
                        <div className="text-[11px] text-slate-400 mt-1">Akumulasi pencairan lunas</div>
                    </div>
                    <div className="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                        <div className="text-xs font-semibold text-slate-500 mb-1">Pencairan Dalam Proses</div>
                        <div className="text-2xl font-black text-amber-600">{metrics.pending_payouts}</div>
                        <div className="text-[11px] text-slate-400 mt-1">Verifikasi admin & gateway</div>
                    </div>
                    <div className="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
                        <div className="text-xs font-semibold text-slate-500 mb-1">Total Transaksi Sewa</div>
                        <div className="text-2xl font-black text-slate-900">{metrics.total_transactions}</div>
                        <div className="text-[11px] text-slate-400 mt-1">Semua pesanan lunas</div>
                    </div>
                </div>

                {/* Payout Table */}
                <div className="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs sm:text-sm">
                            <thead className="bg-slate-50 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <tr>
                                    <th className="px-6 py-4">ID Payout & Booking</th>
                                    <th className="px-6 py-4">Penyewa & Kos</th>
                                    <th className="px-6 py-4">Net Diterima</th>
                                    <th className="px-6 py-4">Status</th>
                                    <th className="px-6 py-4 text-right">Kuitansi / Detail</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {payouts.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={5} className="text-center py-12 text-slate-400">
                                            Belum ada riwayat pencairan dana payout.
                                        </td>
                                    </tr>
                                ) : (
                                    payouts.data.map((p) => (
                                        <tr key={p.id} className="hover:bg-slate-50/70 transition-colors">
                                            <td className="px-6 py-4">
                                                <div className="font-mono font-bold text-slate-900 text-xs">
                                                    PAY-{p.id.substring(0, 8).toUpperCase()}
                                                </div>
                                                <div className="text-[11px] text-slate-500 mt-0.5">
                                                    Booking: <span className="font-mono font-medium">#{p.booking?.booking_code}</span>
                                                </div>
                                                <div className="text-[10px] text-slate-400 mt-0.5">
                                                    {p.completed_at ? new Date(p.completed_at).toLocaleDateString('id-ID', {
                                                        day: 'numeric',
                                                        month: 'short',
                                                        year: 'numeric',
                                                    }) : new Date(p.created_at).toLocaleDateString('id-ID')}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="font-bold text-slate-900">
                                                    {p.booking?.tenant_name || p.booking?.user?.name}
                                                </div>
                                                <div className="text-[11px] text-slate-500">
                                                    {p.booking?.listing?.name} • Kamar {p.booking?.room?.name}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="font-black text-emerald-700 text-sm">
                                                    {p.formatted_net_amount}
                                                </div>
                                                {p.platform_fee > 0 && (
                                                    <div className="text-[10px] text-slate-400">
                                                        Gross: {p.formatted_gross_amount} (Fee: {p.formatted_platform_fee})
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-6 py-4">
                                                {getPayoutBadge(p.status)}
                                            </td>
                                            <td className="px-6 py-4 text-right space-x-2">
                                                <Link href={`/owner/payouts/${p.id}/receipt`} target="_blank">
                                                    <Button variant="outline" size="sm" className="font-bold text-xs">
                                                        🖨️ Kuitansi PDF
                                                    </Button>
                                                </Link>
                                                <Link href={`/owner/payouts/${p.id}`}>
                                                    <Button variant="ghost" size="sm" className="font-bold text-xs text-slate-700">
                                                        Detail →
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
                    {payouts.links.length > 3 && (
                        <div className="px-6 py-4 bg-slate-50 border-t border-slate-200/80 flex items-center justify-between">
                            <div className="text-xs text-slate-500">
                                Menampilkan <span className="font-bold">{payouts.data.length}</span> dari <span className="font-bold">{payouts.total}</span> pencairan
                            </div>
                            <div className="flex gap-1">
                                {payouts.links.map((link, idx) => (
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
