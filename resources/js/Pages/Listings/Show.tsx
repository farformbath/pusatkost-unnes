import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Listing } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { GenderBadge } from '@/components/ui/Badge';
import { FacilityBadge } from '@/features/listings/components/FacilityBadge';
import { ImageGallery } from '@/features/listings/components/ImageGallery';
import { StickyBookingWidget } from '@/features/listings/components/StickyBookingWidget';
import { ReviewList } from '@/features/reviews/components/ReviewList';
import { ReviewForm } from '@/features/reviews/components/ReviewForm';
import { MapView } from '@/components/ui/MapView';

interface ShowProps {
    listing: Listing;
}

export default function Show({ listing }: ShowProps) {
    const [selectedRoomId, setSelectedRoomId] = useState(
        listing.rooms?.find((r) => r.status === 'available' && r.available_rooms > 0)?.id || ''
    );
    const [isWishlisted, setIsWishlisted] = useState(false);
    const [isTogglingWishlist, setIsTogglingWishlist] = useState(false);

    const handleToggleWishlist = () => {
        setIsWishlisted(!isWishlisted);
        setIsTogglingWishlist(true);

        router.post(
            '/wishlist/toggle',
            { listing_id: listing.id },
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setIsTogglingWishlist(false),
            }
        );
    };

    return (
        <PublicLayout>
            <Head title={`${listing.name} | Kos ${listing.area} Sekitar UNNES`} />

            {/* Breadcrumb & Navigation */}
            <div className="mb-4 flex items-center justify-between">
                <Link
                    href="/kos"
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#FF6565] transition-colors"
                >
                    <span>← Kembali ke Semua Kos</span>
                </Link>

                <div className="flex items-center gap-2">
                    <button
                        onClick={() => alert(`Tautan kos ${listing.name} telah disalin ke clipboard!`)}
                        className="text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white border border-slate-200 px-3.5 py-1.5 rounded-full hover:shadow-xs transition-all cursor-pointer active:scale-98"
                    >
                        📤 Bagikan
                    </button>
                    <button
                        onClick={handleToggleWishlist}
                        disabled={isTogglingWishlist}
                        className={`text-xs font-semibold px-3.5 py-1.5 rounded-full border transition-all cursor-pointer flex items-center gap-1.5 active:scale-98 ${
                            isWishlisted
                                ? 'bg-rose-50 border-[#FF6565] text-[#FF6565]'
                                : 'bg-white border-slate-200 text-slate-700 hover:text-rose-600'
                        }`}
                    >
                        <span>{isWishlisted ? '❤️' : '🤍'}</span>
                        <span>{isWishlisted ? 'Tersimpan' : 'Simpan'}</span>
                    </button>
                </div>
            </div>

            {/* Title & Metadata */}
            <div className="mb-6 space-y-2">
                <div className="flex flex-wrap items-center gap-2">
                    <GenderBadge gender={listing.gender_type} />
                    <span className="px-2.5 py-0.5 text-xs font-bold bg-slate-900 text-white rounded-md">
                        Area {listing.area}
                    </span>
                    {listing.rating_avg > 0 && (
                        <span className="flex items-center gap-1 text-xs font-bold text-slate-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                            ★ {listing.rating_avg.toFixed(1)} ({listing.review_count} ulasan terverifikasi)
                        </span>
                    )}
                </div>

                <h1 className="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    {listing.name}
                </h1>

                <p className="text-xs sm:text-sm text-slate-600 flex items-center gap-1.5">
                    <svg className="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{listing.address}</span>
                </p>
            </div>

            {/* Airbnb 5-Photo Gallery */}
            <div className="mb-10">
                <ImageGallery images={listing.images} listingName={listing.name} />
            </div>

            {/* Main Content Layout (Left Info + Right Sticky Widget) */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-10">
                {/* Left Column (2 Cols) */}
                <div className="lg:col-span-2 space-y-8">
                    {/* Host Overview */}
                    <div className="flex items-center justify-between pb-6 border-b border-slate-200">
                        <div>
                            <h2 className="text-base sm:text-lg font-bold text-slate-900">
                                Dikelola oleh Pengelola Terverifikasi
                            </h2>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Mitra Resmi Pusat Kost UNNES • {listing.rooms?.length || 0} Pilihan Tipe Kamar
                            </p>
                        </div>
                        <div className="w-12 h-12 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-base shadow-sm">
                            {listing.name.charAt(0)}
                        </div>
                    </div>

                    {/* Deskripsi Properti */}
                    <div className="pb-6 border-b border-slate-200 space-y-3">
                        <h3 className="text-base font-bold text-slate-900">Tentang Kos Ini</h3>
                        <p className="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                            {listing.description}
                        </p>
                    </div>

                    {/* Daftar Kamar & Ketersediaan */}
                    <div className="pb-6 border-b border-slate-200 space-y-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-base font-bold text-slate-900">Pilihan Tipe Kamar</h3>
                                <p className="text-xs text-slate-500 mt-0.5">
                                    Pilih kamar yang diinginkan untuk reservasi dan kalkulasi rincian sewa
                                </p>
                            </div>
                        </div>

                        {listing.rooms && listing.rooms.length > 0 ? (
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {listing.rooms.map((room) => {
                                    const isAvailable = room.status === 'available' && room.available_rooms > 0;
                                    const isSelected = selectedRoomId === room.id;

                                    return (
                                        <div
                                            key={room.id}
                                            onClick={() => {
                                                if (isAvailable) setSelectedRoomId(room.id);
                                            }}
                                            className={`p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between gap-3 active:scale-[0.99] ${
                                                isSelected
                                                    ? 'border-[#FF6565] bg-rose-50/60 shadow-xs'
                                                    : isAvailable
                                                    ? 'border-slate-200 bg-white hover:bg-slate-50/80 hover:border-slate-300'
                                                    : 'border-slate-200 bg-slate-50/60 opacity-60 cursor-not-allowed'
                                            }`}
                                        >
                                            <div className="space-y-1.5">
                                                <div className="flex items-start justify-between gap-2">
                                                    <div className="font-black text-slate-950 text-sm">
                                                        {room.name}
                                                    </div>
                                                    <div className={`w-4 h-4 rounded-full border flex items-center justify-center text-[10px] shrink-0 ${
                                                        isSelected
                                                            ? 'border-[#FF6565] bg-[#FF6565] text-white font-black'
                                                            : 'border-slate-300 bg-white'
                                                    }`}>
                                                        {isSelected && '✓'}
                                                    </div>
                                                </div>

                                                <div className="flex flex-wrap items-center gap-2 text-xs">
                                                    {room.size && (
                                                        <span className="text-slate-500 font-medium">
                                                            📐 {room.size}
                                                        </span>
                                                    )}
                                                    {isAvailable ? (
                                                        <span className="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200/60">
                                                            Sisa {room.available_rooms} kamar
                                                        </span>
                                                    ) : (
                                                        <span className="px-2 py-0.5 rounded-md bg-slate-200 text-slate-600 font-bold text-[10px]">
                                                            Penuh
                                                        </span>
                                                    )}
                                                </div>
                                            </div>

                                            <div className="pt-2 border-t border-slate-100 flex items-baseline justify-between">
                                                <span className="text-[11px] text-slate-400 font-medium">Tarif Bulanan</span>
                                                <div className="flex items-baseline gap-1">
                                                    <span className="font-extrabold text-sm text-[#FF6565]">
                                                        {room.formatted_price_monthly}
                                                    </span>
                                                    <span className="text-[11px] text-slate-500">/bln</span>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        ) : (
                            <p className="text-xs text-slate-400">Belum ada kamar yang didaftarkan.</p>
                        )}
                    </div>

                    {/* Fasilitas Properti */}
                    {listing.facilities && listing.facilities.length > 0 && (
                        <div className="pb-6 border-b border-slate-200 space-y-3">
                            <h3 className="text-base font-bold text-slate-900">Fasilitas Kos</h3>
                            <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                {listing.facilities.map((facility) => (
                                    <div key={facility.id} className="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-slate-200">
                                        <FacilityBadge facility={facility} />
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Reviews & Ratings Section */}
                    <div className="pb-6 border-b border-slate-200 space-y-6">
                        <ReviewList
                            reviews={listing.reviews || []}
                            ratingAvg={listing.rating_avg}
                            reviewCount={listing.review_count}
                            listingSlug={listing.slug}
                        />

                        <ReviewForm listingSlug={listing.slug} />
                    </div>

                    {/* Peraturan Kos */}
                    {listing.rules && (
                        <div className="pb-6 border-b border-slate-200 space-y-3">
                            <h3 className="text-base font-bold text-slate-900">Peraturan Kos</h3>
                            <p className="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-amber-50/50 p-4 rounded-xl border border-amber-100 text-amber-900">
                                {listing.rules}
                            </p>
                        </div>
                    )}

                    {/* Lokasi & Peta Sekitar Kampus UNNES */}
                    <div className="space-y-3">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-base font-bold text-slate-900">Lokasi & Akses Kampus UNNES</h3>
                                <p className="text-xs text-slate-500 mt-0.5">
                                    Area {listing.area} • {listing.address}
                                </p>
                            </div>
                        </div>

                        <MapView singleListing={listing} height="360px" />

                        <div className="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-2 text-xs">
                            <div className="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span className="font-bold text-slate-800 block mb-0.5">📍 Rektorat UNNES</span>
                                <span className="text-slate-500 text-[11px]">± 3-5 Menit</span>
                            </div>
                            <div className="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span className="font-bold text-slate-800 block mb-0.5">🎓 Fakultas Terdekat</span>
                                <span className="text-slate-500 text-[11px]">FBS, FEB, FT, FMIPA</span>
                            </div>
                            <div className="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span className="font-bold text-slate-800 block mb-0.5">🍜 Pusat Kuliner</span>
                                <span className="text-slate-500 text-[11px]">Jalan Taman Siswa Sekaran</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Column: Sticky Booking Card */}
                <div className="lg:col-span-1">
                    <StickyBookingWidget
                        listing={listing}
                        selectedRoomId={selectedRoomId}
                        onSelectRoom={(id) => setSelectedRoomId(id)}
                    />
                </div>
            </div>
        </PublicLayout>
    );
}
