import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Listing, PaginatedData } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { StatusBadge, GenderBadge } from '@/components/ui/Badge';
import { Button } from '@/components/ui/Button';

interface IndexProps {
    listings: PaginatedData<Listing>;
}

export default function Index({ listings }: IndexProps) {
    const handlePublish = (listing: Listing) => {
        if (confirm(`Publikasikan kos "${listing.name}" agar dapat dilihat pencari kos?`)) {
            router.post(`/owner/kos/${listing.id}/publish`, {}, {
                preserveScroll: true,
                preserveState: true,
            });
        }
    };

    return (
        <PublicLayout>
            <Head title="Manajemen Kos Saya | Dashboard Pemilik Kos UNNES" />

            <div className="space-y-6">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight">
                            Manajemen Kos Saya
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500">
                            Kelola data properti kos, tipe kamar, galeri foto, dan status ketersediaan.
                        </p>
                    </div>

                    <Link href="/owner/kos/create">
                        <Button variant="primary" size="md">
                            + Daftarkan Kos Baru
                        </Button>
                    </Link>
                </div>

                {/* Table or Cards */}
                {listings.data.length > 0 ? (
                    <div className="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                        <div className="divide-y divide-slate-100">
                            {listings.data.map((listing) => (
                                <div
                                    key={listing.id}
                                    className="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/60 transition-colors"
                                >
                                    <div className="flex items-start gap-4">
                                        <div className="w-20 h-20 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-slate-200/60">
                                            <img
                                                src={listing.primary_image?.url || '/images/logo.jpg'}
                                                alt={listing.name}
                                                className="w-full h-full object-cover"
                                            />
                                        </div>

                                        <div className="space-y-1.5">
                                            <div className="flex flex-wrap items-center gap-2">
                                                <h3 className="text-base font-bold text-slate-900">
                                                    {listing.name}
                                                </h3>
                                                <StatusBadge status={listing.status} />
                                                <GenderBadge gender={listing.gender_type} />
                                            </div>

                                            <p className="text-xs text-slate-500">
                                                Area {listing.area} • {listing.address}
                                            </p>

                                            <div className="flex items-center gap-4 text-xs text-slate-600 pt-1">
                                                <span>
                                                    <strong>{listing.rooms?.length || 0}</strong> Tipe Kamar
                                                </span>
                                                <span>•</span>
                                                <span className="text-[#FF6565] font-bold">
                                                    Mulai {listing.formatted_starting_price}/bln
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Actions */}
                                    <div className="flex flex-wrap items-center gap-2 self-end sm:self-center">
                                        <Link href={`/owner/kos/${listing.id}/kamar`}>
                                            <Button variant="outline" size="sm">
                                                🛏️ Kelola Kamar ({listing.rooms?.length || 0})
                                            </Button>
                                        </Link>

                                        <Link href={`/owner/kos/${listing.id}/edit`}>
                                            <Button variant="outline" size="sm">
                                                ✏️ Edit Kos & Foto
                                            </Button>
                                        </Link>

                                        {listing.status === 'draft' && (
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                onClick={() => handlePublish(listing)}
                                            >
                                                🚀 Publikasikan
                                            </Button>
                                        )}

                                        {listing.status === 'published' && (
                                            <Link href={`/kos/${listing.slug}`} target="_blank">
                                                <Button variant="ghost" size="sm">
                                                    Lihat di Web ↗
                                                </Button>
                                            </Link>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                ) : (
                    <div className="text-center py-16 px-4 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                        <div className="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
                            🏢
                        </div>
                        <h3 className="text-lg font-bold text-slate-800">
                            Anda belum mendaftarkan kos
                        </h3>
                        <p className="text-sm text-slate-500 max-w-md mx-auto">
                            Mulai pasang iklan kos Anda di Pusat Kost UNNES agar dapat ditemukan oleh ribuan mahasiswa UNNES setiap semester.
                        </p>
                        <Link href="/owner/kos/create">
                            <Button variant="primary" size="md">
                                + Daftarkan Kos Pertama Saya
                            </Button>
                        </Link>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
