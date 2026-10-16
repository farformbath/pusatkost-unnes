import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/Button';

interface ReviewFormProps {
    listingSlug: string;
}

export const ReviewForm: React.FC<ReviewFormProps> = ({ listingSlug }) => {
    const [hoverRating, setHoverRating] = useState<number | null>(null);

    const { data, setData, post, processing, reset, errors, recentlySuccessful } = useForm({
        rating: 5,
        comment: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/kos/${listingSlug}/reviews`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => reset(),
        });
    };

    return (
        <div className="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 className="text-base font-bold text-slate-900">
                Tulis Ulasan & Pengalaman Anda
            </h3>

            {recentlySuccessful && (
                <div className="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-800 font-semibold">
                    ✓ Terima kasih! Ulasan dan rating bintang Anda telah berhasil diterbitkan.
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
                {/* Star Rating Selector */}
                <div>
                    <label className="block text-xs font-bold text-slate-600 mb-1.5">
                        Beri Nilai Rating (1 s.d. 5 Bintang)
                    </label>
                    <div className="flex items-center gap-1.5">
                        {[1, 2, 3, 4, 5].map((star) => {
                            const isFilled = star <= (hoverRating ?? data.rating);
                            return (
                                <button
                                    key={star}
                                    type="button"
                                    onClick={() => setData('rating', star)}
                                    onMouseEnter={() => setHoverRating(star)}
                                    onMouseLeave={() => setHoverRating(null)}
                                    className="text-2xl transition-transform hover:scale-125 cursor-pointer focus:outline-none"
                                >
                                    <span className={isFilled ? 'text-amber-500' : 'text-slate-300'}>
                                        ★
                                    </span>
                                </button>
                            );
                        })}
                        <span className="text-xs font-bold text-slate-700 ml-2">
                            {data.rating}.0 ({data.rating === 5 ? 'Sangat Puas' : data.rating === 4 ? 'Puas' : data.rating === 3 ? 'Cukup' : data.rating === 2 ? 'Kurang' : 'Sangat Kurang'})
                        </span>
                    </div>
                    {errors.rating && <p className="text-xs text-red-500 mt-1">{errors.rating}</p>}
                </div>

                {/* Comment Textarea */}
                <div>
                    <label className="block text-xs font-bold text-slate-600 mb-1.5">
                        Ulasan & Catatan Pengalaman
                    </label>
                    <textarea
                        rows={3}
                        value={data.comment}
                        onChange={(e) => setData('comment', e.target.value)}
                        placeholder="Ceritakan kebersihan kamar, keramahan pengelola, kecepatan WiFi, keamanan lingkungan sekitar UNNES..."
                        required
                        className="w-full text-xs p-3 rounded-xl border border-slate-200 focus:border-[#FF6565] focus:outline-none bg-slate-50"
                    />
                    {errors.comment && <p className="text-xs text-red-500 mt-1">{errors.comment}</p>}
                </div>

                <div className="flex justify-end">
                    <Button
                        type="submit"
                        variant="primary"
                        size="md"
                        isLoading={processing}
                        disabled={processing || !data.comment.trim()}
                    >
                        Kirim Ulasan & Rating
                    </Button>
                </div>
            </form>
        </div>
    );
};
