import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';

interface Owner {
    id: string;
    name: string;
    email: string;
    phone: string | null;
    owner_verification_status: string;
    created_at: string;
    listings_count?: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface OwnersIndexProps {
    owners: {
        data: Owner[];
        current_page: number;
        last_page: number;
        total: number;
        links: PaginationLink[];
    };
    counts: {
        all: number;
        pending: number;
        verified: number;
        rejected: number;
    };
    current_status: string;
}

export default function Index({ owners, counts, current_status }: OwnersIndexProps) {
    const handleVerify = (owner: Owner) => {
        if (confirm(`Verifikasi pemilik kos ${owner.name}?`)) {
            router.post(`/admin/owners/${owner.id}/verify`, {}, {
                preserveScroll: true,
                preserveState: true,
            });
        }
    };

    const handleReject = (owner: Owner) => {
        if (confirm(`Tolak verifikasi pemilik kos ${owner.name}?`)) {
            router.post(`/admin/owners/${owner.id}/reject`, {}, {
                preserveScroll: true,
                preserveState: true,
            });
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'verified':
                return (
                    <span className="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center gap-1 w-fit">
                        <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Terverifikasi
                    </span>
                );
            case 'rejected':
                return (
                    <span className="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200/60 flex items-center gap-1 w-fit">
                        <span className="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Ditolak
                    </span>
                );
            default:
                return (
                    <span className="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center gap-1 w-fit">
                        <span className="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Menunggu Verifikasi
                    </span>
                );
        }
    };

    const tabs = [
        { id: 'all', label: 'Semua Pemilik', count: counts.all },
        { id: 'pending', label: 'Menunggu', count: counts.pending },
        { id: 'verified', label: 'Terverifikasi', count: counts.verified },
        { id: 'rejected', label: 'Ditolak', count: counts.rejected },
    ];

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <Head title="Manajemen Pemilik Kos - Admin UNNESKost" />

            {/* Header & Back */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Link href="/admin/dashboard" className="text-xs font-semibold text-slate-500 hover:text-slate-900">
                            ← Kembali ke Dashboard Admin
                        </Link>
                    </div>
                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
                        Manajemen & Verifikasi Pemilik Kos
                    </h1>
                </div>
            </div>

            {/* Status Filter Tabs */}
            <div className="flex flex-wrap gap-2 my-6">
                {tabs.map((tab) => (
                    <Link
                        key={tab.id}
                        href={`/admin/owners${tab.id !== 'all' ? `?status=${tab.id}` : ''}`}
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

            {/* Owners Table */}
            <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                {owners.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider">
                                <tr>
                                    <th className="px-6 py-3.5">Nama & Kontak</th>
                                    <th className="px-6 py-3.5">Status Verifikasi</th>
                                    <th className="px-6 py-3.5">Jumlah Kos</th>
                                    <th className="px-6 py-3.5">Terdaftar Sejak</th>
                                    <th className="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-slate-700">
                                {owners.data.map((owner) => (
                                    <tr key={owner.id} className="hover:bg-slate-50/50 transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="font-bold text-slate-900 text-sm">{owner.name}</div>
                                            <div className="text-slate-500 mt-0.5">{owner.email}</div>
                                            <div className="text-slate-400 mt-0.5">
                                                {owner.phone ? `WA: ${owner.phone}` : 'Belum isi no. WhatsApp'}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            {getStatusBadge(owner.owner_verification_status)}
                                        </td>
                                        <td className="px-6 py-4 font-semibold text-slate-900">
                                            {owner.listings_count ?? 0} Properti
                                        </td>
                                        <td className="px-6 py-4 text-slate-500">
                                            {new Date(owner.created_at).toLocaleDateString('id-ID', {
                                                day: 'numeric',
                                                month: 'short',
                                                year: 'numeric',
                                            })}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex items-center justify-end gap-1.5">
                                                {owner.owner_verification_status !== 'verified' && (
                                                    <Button
                                                        variant="primary"
                                                        size="sm"
                                                        onClick={() => handleVerify(owner)}
                                                        className="bg-emerald-600 hover:bg-emerald-700 text-white"
                                                    >
                                                        Verifikasi
                                                    </Button>
                                                )}
                                                {owner.owner_verification_status !== 'rejected' && (
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => handleReject(owner)}
                                                        className="text-rose-600 hover:bg-rose-50 border-slate-200"
                                                    >
                                                        Tolak
                                                    </Button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="text-center py-16 px-4">
                        <div className="text-3xl mb-2">🔍</div>
                        <h3 className="font-bold text-slate-800 text-sm">Tidak Ada Pemilik Kos</h3>
                        <p className="text-xs text-slate-500 mt-1">
                            Tidak ditemukan pemilik kos pada kategori filter ini.
                        </p>
                    </div>
                )}
            </div>

            {/* Pagination */}
            {owners.last_page > 1 && (
                <div className="flex justify-center items-center gap-1.5 pt-6 pb-8">
                    {owners.links.map((link, idx) => {
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
