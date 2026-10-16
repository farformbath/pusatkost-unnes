import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';

interface SuccessProps {
    bookingCode: string;
    bookingId: string;
}

export default function Success({ bookingCode, bookingId }: SuccessProps) {
    return (
        <PublicLayout>
            <Head title="Pengajuan Sewa Berhasil | Pusat Kost UNNES" />

            <div className="max-w-md w-full mx-auto my-8 bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center space-y-6">
                <div className="w-16 h-16 mx-auto bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-3xl">
                    ✓
                </div>

                <div className="space-y-2">
                    <h1 className="text-2xl font-black text-slate-900">
                        Pengajuan Sewa Berhasil!
                    </h1>
                    <p className="text-xs text-slate-500">
                        Kode Booking Anda:
                    </p>
                    <p className="text-lg font-mono font-bold text-[#FF6565] bg-rose-50 py-1.5 px-3 rounded-xl inline-block border border-rose-100">
                        #{bookingCode}
                    </p>
                </div>

                <p className="text-xs text-slate-600 leading-relaxed">
                    Unit kamar telah berhasil dikunci pesimistik untuk Anda. Silakan selesaikan pembayaran sebelum batas waktu berakhir.
                </p>

                <div className="space-y-2 pt-2">
                    <Link href={`/booking/${bookingId}`} className="w-full block">
                        <Button variant="primary" size="lg" className="w-full shadow-xs">
                            Lihat Tagihan & Cara Bayar →
                        </Button>
                    </Link>
                    <Link href="/kos" className="w-full block">
                        <Button variant="outline" size="sm" className="w-full">
                            Kembali ke Katalog
                        </Button>
                    </Link>
                </div>
            </div>
        </PublicLayout>
    );
}
