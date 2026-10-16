import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Heart, Star } from '@phosphor-icons/react';
import { cn } from '@/lib/cn';
import { Listing } from '@/types/listing';
import { GenderBadge } from '@/components/ui/Badge';
import { FacilityBadge } from './FacilityBadge';

interface ListingCardProps {
    listing: Listing;
    initialWishlisted?: boolean;
}

export const ListingCard: React.FC<ListingCardProps> = ({ listing, initialWishlisted = false }) => {
    const [isFavorite, setIsFavorite] = useState(initialWishlisted);
    const [isToggling, setIsToggling] = useState(false);

    const primaryImageUrl = listing.primary_image?.url
        || (listing.images && listing.images.length > 0 ? listing.images[0].url : '/images/logo.jpg');

    const getDistanceSnippet = (name: string, area: string) => {
        if (area.toLowerCase().includes('sekaran')) return '3 - 5 menit jalan kaki ke Rektorat & FBS';
        if (area.toLowerCase().includes('banaran')) return '3 menit naik motor ke FMIPA & FT';
        if (area.toLowerCase().includes('taman')) return 'Jalan protokol Taman Siswa Sekaran';
        return `Kawasan strategis Area ${area} sekitar UNNES`;
    };

    const handleToggleWishlist = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();

        setIsFavorite(!isFavorite);
        setIsToggling(true);

        router.post(
            '/wishlist/toggle',
            { listing_id: listing.id },
            {
                preserveScroll: true,
                preserveState: true,
                onFinish: () => setIsToggling(false),
            }
        );
    };

    return (
        <div className="group relative flex flex-col bg-transparent">
            {/* 4:3 Aspect Ratio Container with Smooth Rounded Edges */}
            <div className="relative aspect-4/3 w-full overflow-hidden rounded-2xl bg-slate-100 border border-slate-200/80 shadow-xs group-hover:shadow-md transition-all duration-300">
                <Link href={`/kos/${listing.slug}`} className="block w-full h-full">
                    <img
                        src={primaryImageUrl}
                        alt={listing.name}
                        className="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                        loading="lazy"
                    />
                </Link>

                {/* Floating Gender Badge */}
                <div className="absolute top-3.5 left-3.5 z-10 shadow-xs">
                    <GenderBadge gender={listing.gender_type} />
                </div>

                {/* Favorite Heart Button */}
                <button
                    onClick={handleToggleWishlist}
                    disabled={isToggling}
                    className="absolute top-3.5 right-3.5 w-8 h-8 rounded-full bg-white/90 backdrop-blur-xs shadow-md flex items-center justify-center transition-transform hover:scale-110 cursor-pointer z-10"
                    aria-label="Wishlist"
                >
                    <Heart
                        weight={isFavorite ? 'fill' : 'regular'}
                        className={cn(
                            'w-4 h-4 transition-colors',
                            isFavorite ? 'text-primary' : 'text-muted hover:text-primary'
                        )}
                    />
                </button>

                {/* Area Tag */}
                <div className="absolute bottom-3 left-3 z-10">
                    <span className="px-2.5 py-1 text-[11px] font-bold bg-slate-900/80 backdrop-blur-xs text-white rounded-md shadow-xs">
                        Area {listing.area}
                    </span>
                </div>
            </div>

            {/* Info Body */}
            <div className="mt-3 flex flex-col gap-1">
                <div className="flex items-center justify-between gap-2">
                    <Link
                        href={`/kos/${listing.slug}`}
                        className="font-bold text-slate-950 text-sm hover:text-primary transition-colors line-clamp-1"
                    >
                        {listing.name}
                    </Link>

                    <div className="flex items-center gap-1 text-xs font-semibold text-slate-900 shrink-0">
                        <Star weight="fill" className="w-3.5 h-3.5 text-amber-500" />
                        <span>{listing.rating_avg > 0 ? listing.rating_avg.toFixed(1) : 'Baru'}</span>
                        {listing.review_count > 0 && <span className="text-slate-400 font-normal">({listing.review_count})</span>}
                    </div>
                </div>

                <p className="text-xs text-slate-500 line-clamp-1">
                    {getDistanceSnippet(listing.name, listing.area)}
                </p>

                {/* Facilities Pills snippet */}
                {listing.facilities && listing.facilities.length > 0 && (
                    <div className="flex flex-wrap gap-1 pt-1">
                        {listing.facilities.slice(0, 3).map((f) => (
                            <FacilityBadge key={f.id} facility={f} className="text-[10px] py-0.5 px-2" />
                        ))}
                    </div>
                )}

                {/* Price Section */}
                <div className="mt-1 flex items-baseline justify-between pt-1">
                    <span className="text-[11px] text-slate-400">Mulai dari</span>
                    <div className="flex items-baseline gap-1">
                        <span className="font-extrabold text-sm text-primary">
                            {listing.formatted_starting_price}
                        </span>
                        <span className="text-xs text-slate-500 font-medium">/ bulan</span>
                    </div>
                </div>
            </div>
        </div>
    );
};
