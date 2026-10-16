import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';
import { SortDropdown, SortOption } from '@/components/ui/SortDropdown';

interface ReviewItem {
    id: string;
    rating: number;
    comment: string;
    owner_reply: string | null;
    created_at: string;
    user?: { id: string; name: string; email: string };
    listing?: { id: string; name: string; slug: string };
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface ReviewsIndexProps {
    reviews: {
        data: ReviewItem[];
        current_page: number;
        last_page: number;
        total: number;
        links: PaginationLink[];
    };
    selected_rating?: string;
}

export default function Index({ reviews, selected_rating }: ReviewsIndexProps) {
    const handleDelete = (review: ReviewItem) => {
        if (confirm(`Hapus ulasan dari "${review.user?.name || 'Mahasiswa'}"? Aksi ini akan memperbarui rating kos terkait.`)) {
            router.delete(`/admin/reviews/${review.id}`, {
                preserveScroll: true,
                preserveState: true,
            });
        }
    };

    const ratingOptions: SortOption[] = [
        { value: '', label: 'Semua Bintang', sublabel: 'Tampilkan seluruh ulasan masuk', icon: '⭐' },
        { value: '5', label: '5 Bintang', sublabel: 'Ulasan rating 5.0 (Sempurna)', icon: '★' },
        { value: '4', label: '4 Bintang', sublabel: 'Ulasan rating 4.0 - 4.9 (Bagus)', icon: '★' },
        { value: '3', label: '3 Bintang', sublabel: 'Ulasan rating 3.0 - 3.9 (Cukup)', icon: '★' },
        { value: '2', label: '2 Bintang', sublabel: 'Ulasan rating 2.0 - 2.9 (Kurang)', icon: '★' },
        { value: '1', label: '1 Bintang', sublabel: 'Ulasan rating 1.0 - 1.9 (Buruk)', icon: '★' },
    ];

    const handleRatingChange = (val: string) => {
        router.get(`/admin/reviews${val ? `?rating=${val}` : ''}`, {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <Head title="Moderasi Ulasan - Admin Pusat Kost UNNES" />

            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <div className="flex items-center gap-2 mb-1">
                        <Link href="/admin/dashboard" className="text-xs font-semibold text-slate-500 hover:text-slate-900">
                            ← Kembali ke Dashboard Admin
                        </Link>
                    </div>
                    <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
                        Moderasi Ulasan & Rating Mahasiswa
                    </h1>
                </div>

                {/* Rating Filter */}
                <div className="flex items-center gap-2">
                    <span className="text-xs text-slate-500 font-medium">Filter Bintang:</span>
                    <SortDropdown
                        value={selected_rating || ''}
                        onChange={handleRatingChange}
                        options={ratingOptions}
                    />
                </div>
            </div>

            {/* Reviews Table */}
            <div className="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden mt-6">
                {reviews.data.length > 0 ? (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200 uppercase tracking-wider">
                                <tr>
                                    <th className="px-6 py-3.5">Penyewa & Kos</th>
                                    <th className="px-6 py-3.5">Rating</th>
                                    <th className="px-6 py-3.5">Komentar & Balasan</th>
                                    <th className="px-6 py-3.5">Tanggal</th>
                                    <th className="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-slate-700">
                                {reviews.data.map((review) => (
                                    <tr key={review.id} className="hover:bg-slate-50/50 transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="font-bold text-slate-900 text-sm">
                                                {review.user?.name || 'Mahasiswa'}
                                            </div>
                                            <div className="text-slate-500 text-[11px]">
                                                {review.user?.email}
                                            </div>
                                            {review.listing && (
                                                <Link
                                                    href={`/kos/${review.listing.slug}`}
                                                    className="text-xs font-medium text-[#FF6565] hover:underline block mt-1"
                                                    target="_blank"
                                                >
                                                    Properti: {review.listing.name} ↗
                                                </Link>
                                            )}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center text-amber-500 font-bold text-sm">
                                                ★ {review.rating}/5
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 max-w-md">
                                            <p className="text-slate-800 italic">
                                                &quot;{review.comment}&quot;
                                            </p>
                                            {review.owner_reply && (
                                                <div className="mt-2 p-2 bg-slate-50 rounded-lg border border-slate-100 text-[11px] text-slate-600">
                                                    <span className="font-bold text-slate-800">Balasan Pemilik: </span>
                                                    {review.owner_reply}
                                                </div>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-slate-500 whitespace-nowrap">
                                            {new Date(review.created_at).toLocaleDateString('id-ID', {
                                                day: 'numeric',
                                                month: 'short',
                                                year: 'numeric',
                                            })}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() => handleDelete(review)}
                                                className="text-rose-600 hover:bg-rose-50 border-rose-200"
                                            >
                                                Hapus Ulasan
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                ) : (
                    <div className="text-center py-16 px-4">
                        <div className="text-3xl mb-2">💬</div>
                        <h3 className="font-bold text-slate-800 text-sm">Tidak Ada Ulasan</h3>
                        <p className="text-xs text-slate-500 mt-1">
                            Belum ada ulasan yang sesuai dengan filter ini.
                        </p>
                    </div>
                )}
            </div>

            {/* Pagination */}
            {reviews.last_page > 1 && (
                <div className="flex justify-center items-center gap-1.5 pt-6 pb-8">
                    {reviews.links.map((link, idx) => {
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
