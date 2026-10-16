import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Wishlist } from '@/types/wishlist';
import { PaginatedData } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { ListingCard } from '@/features/listings/components/ListingCard';
import { Button } from '@/components/ui/Button';

interface IndexProps {
    wishlists: PaginatedData<Wishlist>;
}

export default function Index({ wishlists }: IndexProps) {
    return (
        <PublicLayout>
            <Head title="Kos Favorit Saya (Wishlist) | Pusat Kost UNNES" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/80 pb-5">
                    <div>
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>❤️</span>
                            <span>Kos Favorit Tersimpan</span>
                        </h1>
                        <p className="text-xs text-slate-500 mt-1">
                            Daftar kos pilihan yang Anda simpan untuk perbandingan dan pengajuan sewa.
                        </p>
                    </div>

                    <Link href="/kos">
                        <Button variant="outline" size="sm">
                            + Cari Kos Lainnya
                        </Button>
                    </Link>
                </div>

                {/* Wishlist Grid */}
                {wishlists.data.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                        {wishlists.data.map((item) => (
                            item.listing ? (
                                <ListingCard
                                    key={item.id}
                                    listing={item.listing}
                                    initialWishlisted={true}
                                />
                            ) : null
                        ))}
                    </div>
                ) : (
                    <div className="text-center py-20 px-4 bg-white rounded-3xl border border-slate-200 shadow-xs max-w-md mx-auto my-8 space-y-4">
                        <div className="w-16 h-16 mx-auto bg-rose-50 rounded-full flex items-center justify-center text-3xl text-[#FF6565]">
                            🤍
                        </div>
                        <div>
                            <h3 className="font-bold text-slate-800 text-base mb-1">
                                Belum Ada Kos Favorit
                            </h3>
                            <p className="text-xs text-slate-500 max-w-xs mx-auto">
                                Klik ikon hati ❤️ pada kartu kos di katalog untuk menyimpannya ke daftar wishlist Anda.
                            </p>
                        </div>
                        <Link href="/kos" className="inline-block">
                            <Button variant="primary" size="sm">
                                Jelajahi Katalog Kos UNNES
                            </Button>
                        </Link>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
