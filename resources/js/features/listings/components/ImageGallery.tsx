import React, { useState } from 'react';
import { ListingImage } from '@/types/listing';
import { Modal } from '@/components/ui/Modal';

interface ImageGalleryProps {
    images?: ListingImage[];
    listingName: string;
}

export const ImageGallery: React.FC<ImageGalleryProps> = ({
    images = [],
    listingName,
}) => {
    const [selectedImage, setSelectedImage] = useState<string | null>(null);

    const displayImages = images.length > 0
        ? images
        : [{ id: 'fallback', url: '/images/logo.jpg', is_primary: true, file_path: '', sort_order: 1, listing_id: '', room_id: null }];

    const primaryImage = displayImages.find((img) => img.is_primary) || displayImages[0];
    const secondaryImages = displayImages.filter((img) => img.id !== primaryImage.id).slice(0, 4);

    return (
        <>
            <div className="rounded-2xl overflow-hidden border border-slate-200/80 shadow-xs bg-slate-100">
                {/* Desktop Grid Layout */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-2 p-2">
                    {/* Main Image (Span 2 or 3 cols) */}
                    <div
                        className="md:col-span-2 md:row-span-2 relative aspect-4/3 md:aspect-auto md:h-96 rounded-xl overflow-hidden cursor-pointer group"
                        onClick={() => setSelectedImage(primaryImage.url)}
                    >
                        <img
                            src={primaryImage.url}
                            alt={listingName}
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                        <div className="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <span className="px-3 py-1.5 bg-slate-900/75 text-white text-xs font-semibold rounded-lg backdrop-blur-xs">
                                Lihat Foto Penuh
                            </span>
                        </div>
                    </div>

                    {/* Secondary Thumbnails */}
                    {secondaryImages.length > 0 ? (
                        secondaryImages.map((img, idx) => (
                            <div
                                key={img.id}
                                className="relative aspect-4/3 md:h-46 rounded-xl overflow-hidden cursor-pointer group"
                                onClick={() => setSelectedImage(img.url)}
                            >
                                <img
                                    src={img.url}
                                    alt={`${listingName} foto ${idx + 2}`}
                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                                <div className="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity" />
                            </div>
                        ))
                    ) : (
                        <div className="hidden md:flex md:col-span-2 h-96 items-center justify-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs text-slate-400">
                            Foto detail lainnya belum diunggah
                        </div>
                    )}
                </div>
            </div>

            {/* Lightbox Modal */}
            <Modal
                isOpen={!!selectedImage}
                onClose={() => setSelectedImage(null)}
                title={`Galeri Foto: ${listingName}`}
                maxWidth="2xl"
            >
                {selectedImage && (
                    <div className="aspect-16/10 w-full overflow-hidden rounded-xl bg-slate-950 flex items-center justify-center">
                        <img
                            src={selectedImage}
                            alt={listingName}
                            className="max-h-full max-w-full object-contain"
                        />
                    </div>
                )}
            </Modal>
        </>
    );
};
