import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { Review } from '@/types/review';
import { Button } from '@/components/ui/Button';

interface ReviewListProps {
    reviews: Review[];
    ratingAvg: number;
    reviewCount: number;
    listingSlug: string;
}

export const ReviewList: React.FC<ReviewListProps> = ({
    reviews,
    ratingAvg,
    reviewCount,
}) => {
    const [replyingReviewId, setReplyingReviewId] = useState<string | null>(null);
    const [replyText, setReplyText] = useState('');
    const [isSubmittingReply, setIsSubmittingReply] = useState(false);

    const handleReplySubmit = (reviewId: string) => {
        if (!replyText.trim()) return;

        setIsSubmittingReply(true);
        router.post(
            `/reviews/${reviewId}/reply`,
            { reply: replyText },
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    setReplyingReviewId(null);
                    setReplyText('');
                },
                onFinish: () => setIsSubmittingReply(false),
            }
        );
    };

    return (
        <div className="space-y-6">
            {/* Header / Summary Card */}
            <div className="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div className="flex items-center gap-4">
                    <div className="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col items-center justify-center text-amber-900 shadow-xs">
                        <span className="text-2xl font-black">{ratingAvg > 0 ? ratingAvg.toFixed(1) : '-'}</span>
                        <div className="flex text-amber-500 text-xs">
                            {'★'.repeat(Math.round(ratingAvg || 5))}
                        </div>
                    </div>

                    <div>
                        <h3 className="text-base font-bold text-slate-900">
                            Ulasan & Pengalaman Penghuni
                        </h3>
                        <p className="text-xs text-slate-500">
                            Berdasarkan <strong>{reviewCount} ulasan</strong> dari mahasiswa & penghuni terverifikasi UNNES.
                        </p>
                    </div>
                </div>

                <div className="flex items-center gap-2 text-xs font-semibold text-slate-700 bg-slate-50 px-3 py-2 rounded-xl border border-slate-200/80">
                    <span className="text-emerald-600 font-bold">✓</span>
                    <span>100% Ulasan Transparan & Bebas Rekayasa</span>
                </div>
            </div>

            {/* Review Cards */}
            {reviews && reviews.length > 0 ? (
                <div className="space-y-4">
                    {reviews.map((rev) => (
                        <div
                            key={rev.id}
                            className="bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs space-y-3"
                        >
                            <div className="flex items-start justify-between gap-4">
                                <div className="flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-sm shadow-xs">
                                        {rev.user?.name ? rev.user.name.charAt(0) : 'U'}
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <span className="text-xs font-bold text-slate-900">
                                                {rev.user?.name || 'Mahasiswa UNNES'}
                                            </span>
                                            <span className="text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-md">
                                                ✓ Terverifikasi
                                            </span>
                                        </div>
                                        <span className="text-[11px] text-slate-400">
                                            {new Date(rev.created_at).toLocaleDateString('id-ID', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                            })}
                                        </span>
                                    </div>
                                </div>

                                <div className="flex items-center gap-1 text-amber-500 text-sm font-bold bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                    <span>{'★'.repeat(rev.rating)}</span>
                                    <span className="text-slate-800 text-xs font-black ml-1">{rev.rating}.0</span>
                                </div>
                            </div>

                            <p className="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                {rev.comment}
                            </p>

                            {/* Owner Reply if exists */}
                            {rev.owner_reply && (
                                <div className="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 space-y-1 text-xs">
                                    <div className="flex items-center justify-between text-[11px] font-bold text-slate-900">
                                        <span>💬 Balasan Pengelola Kos</span>
                                        {rev.owner_replied_at && (
                                            <span className="text-slate-400 font-normal">
                                                {new Date(rev.owner_replied_at).toLocaleDateString('id-ID')}
                                            </span>
                                        )}
                                    </div>
                                    <p className="text-slate-600 italic">
                                        &quot;{rev.owner_reply}&quot;
                                    </p>
                                </div>
                            )}

                            {/* Reply Button for Owner */}
                            {!rev.owner_reply && replyingReviewId !== rev.id && (
                                <button
                                    type="button"
                                    onClick={() => {
                                        setReplyingReviewId(rev.id);
                                        setReplyText('');
                                    }}
                                    className="text-[11px] font-bold text-slate-500 hover:text-[#FF6565] cursor-pointer"
                                >
                                    + Balas Ulasan Ini (Sebagai Pemilik Kos)
                                </button>
                            )}

                            {/* Reply Form */}
                            {replyingReviewId === rev.id && (
                                <div className="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                                    <textarea
                                        rows={2}
                                        value={replyText}
                                        onChange={(e) => setReplyText(e.target.value)}
                                        placeholder="Tuliskan ucapan terima kasih atau klarifikasi resmi..."
                                        className="w-full text-xs p-2.5 rounded-lg border border-slate-300 focus:border-[#FF6565] focus:outline-none bg-white"
                                    />
                                    <div className="flex justify-end gap-2">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => setReplyingReviewId(null)}
                                        >
                                            Batal
                                        </Button>
                                        <Button
                                            variant="primary"
                                            size="sm"
                                            isLoading={isSubmittingReply}
                                            disabled={isSubmittingReply}
                                            onClick={() => handleReplySubmit(rev.id)}
                                        >
                                            Kirim Balasan
                                        </Button>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            ) : (
                <div className="text-center py-10 bg-white rounded-2xl border border-slate-200/80 p-6 space-y-2">
                    <p className="text-xs font-semibold text-slate-600">
                        Belum ada ulasan untuk kos ini.
                    </p>
                    <p className="text-[11px] text-slate-400">
                        Jadilah yang pertama memberikan ulasan dan rating pengalaman Anda tinggal di kos ini!
                    </p>
                </div>
            )}
        </div>
    );
};
