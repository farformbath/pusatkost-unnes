import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Listing, Facility, PaginatedData } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { AirbnbSearchBar } from '@/features/listings/components/AirbnbSearchBar';
import { FilterBar } from '@/features/listings/components/FilterBar';
import { ListingCard } from '@/features/listings/components/ListingCard';
import { FilterModal } from '@/features/search/components/FilterModal';
import { MapView } from '@/components/ui/MapView';
import { Button } from '@/components/ui/Button';

interface IndexProps {
    listings: PaginatedData<Listing>;
    facilities: Facility[];
    filters: {
        keyword?: string;
        gender?: string;
        area?: string;
        areas?: string[];
        min_price?: number | null;
        max_price?: number | null;
        facility_ids?: string[];
        sort?: string;
    };
}

export default function Index({ listings, facilities, filters }: IndexProps) {
    const [isFilterModalOpen, setIsFilterModalOpen] = useState(false);
    const [viewMode, setViewMode] = useState<'grid' | 'map'>('grid');

    const handleResetAll = () => {
        router.get('/kos', {}, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['listings', 'filters', 'facilities'],
        });
    };

    const currentArea = filters.area || '';

    return (
        <>
            <Head title="Pusat Kost UNNES | Cari dan Sewa Kos Sekitar Kampus UNNES Semarang" />

            {/* Top Search Capsule (Airbnb-style) */}
            <div className="pt-1 pb-3">
                <AirbnbSearchBar initialValues={filters} />
            </div>

            {/* Horizontal Filter Bar with FilterModal trigger */}
            <FilterBar
                facilities={facilities}
                filters={filters}
                totalResults={listings.total}
                onOpenFilterModal={() => setIsFilterModalOpen(true)}
            />

            {/* View Mode & Result Summary Bar */}
            <div className="flex items-center justify-between my-4 px-1">
                <div className="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span>Menampilkan <span className="text-[#FF6565] font-black">{listings.total}</span> kos terverifikasi</span>
                    {currentArea && (
                        <span className="text-xs font-semibold px-2 py-0.5 rounded-md bg-rose-50 text-[#FF6565] border border-rose-200">
                            Area {currentArea}
                        </span>
                    )}
                </div>

                <div className="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                    <button
                        type="button"
                        onClick={() => setViewMode('grid')}
                        className={`px-3 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer flex items-center gap-1 ${
                            viewMode === 'grid'
                                ? 'bg-white text-slate-900 shadow-xs'
                                : 'text-slate-600 hover:text-slate-900'
                        }`}
                    >
                        <span>📋</span>
                        <span className="hidden sm:inline">Katalog</span>
                    </button>
                    <button
                        type="button"
                        onClick={() => setViewMode('map')}
                        className={`px-3 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer flex items-center gap-1 ${
                            viewMode === 'map'
                                ? 'bg-white text-slate-900 shadow-xs'
                                : 'text-slate-600 hover:text-slate-900'
                        }`}
                    >
                        <span>🗺️</span>
                        <span className="hidden sm:inline">Peta UNNES</span>
                    </button>
                </div>
            </div>

            {/* Floating Map/Grid Quick Toggle Pill on Mobile */}
            <div className="fixed bottom-6 left-1/2 -translate-x-1/2 z-30 shadow-xl md:hidden">
                <button
                    type="button"
                    onClick={() => setViewMode(viewMode === 'grid' ? 'map' : 'grid')}
                    className="flex items-center gap-2 bg-slate-950 hover:bg-black text-white px-5 py-3 rounded-full text-xs font-bold transition-transform hover:scale-105 active:scale-95 cursor-pointer shadow-lg shadow-black/20"
                >
                    {viewMode === 'grid' ? (
                        <>
                            <span>🗺️</span>
                            <span>Buka Peta ({listings.total})</span>
                        </>
                    ) : (
                        <>
                            <span>📋</span>
                            <span>Buka Daftar Kos</span>
                        </>
                    )}
                </button>
            </div>

            {/* Map View Mode vs Grid View Mode */}
            {viewMode === 'map' ? (
                <div className="mb-12">
                    <div className="flex items-center justify-between mb-3 px-1">
                        <div className="text-xs font-bold text-slate-700">
                            Peta Lokasi Kos Sekitar Kampus UNNES ({listings.data.length} Kos di Tampilan Ini)
                        </div>
                        <span className="text-[11px] text-slate-500">
                            Klik marker untuk zoom atau kartu harga untuk melihat detail
                        </span>
                    </div>
                    <MapView listings={listings.data} height="600px" />
                </div>
            ) : listings.data.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    {listings.data.map((listing) => (
                        <ListingCard key={listing.id} listing={listing} />
                    ))}
                </div>
            ) : (
                <div className="text-center py-20 px-4 bg-white rounded-3xl border border-slate-200 shadow-xs max-w-md mx-auto my-8 space-y-4">
                    <div className="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center text-3xl text-slate-400">
                        🏢
                    </div>
                    <div>
                        <h3 className="font-bold text-slate-800 text-base mb-1">
                            Tidak Ada Kos Sesuai Kriteria
                        </h3>
                        <p className="text-xs text-slate-500 max-w-xs mx-auto">
                            Coba ubah kata kunci, perluas rentang harga, atau hapus beberapa fasilitas terpilih.
                        </p>
                    </div>
                    <div>
                        <Button variant="primary" size="sm" onClick={handleResetAll}>
                            Reset Semua Filter
                        </Button>
                    </div>
                </div>
            )}

            {/* Pagination */}
            {listings.last_page > 1 && (
                <div className="flex justify-center items-center gap-1.5 pt-4 pb-8">
                    {listings.links.map((link, idx) => {
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
                                only={['listings', 'filters', 'facilities']}
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

            {/* Faceted Filter Modal */}
            <FilterModal
                isOpen={isFilterModalOpen}
                onClose={() => setIsFilterModalOpen(false)}
                facilities={facilities}
                currentFilters={filters}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <PublicLayout>{page}</PublicLayout>;
