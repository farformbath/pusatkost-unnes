import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Logo } from '@/components/shared/Logo';
import { Button } from '@/components/ui/Button';

interface ErrorProps {
    status: number;
    message?: string;
}

export default function Error({ status, message }: ErrorProps) {
    const errorDetails: Record<number, { title: string; description: string; icon: string }> = {
        403: {
            title: 'Akses Dibatasi (403 Forbidden)',
            description: 'Maaf, Anda tidak memiliki izin atau hak akses untuk membuka halaman ini.',
            icon: '🔒',
        },
        404: {
            title: 'Halaman Tidak Ditemukan (404)',
            description: 'Kos atau halaman yang Anda cari mungkin telah dihapus, dipindahkan, atau tautan tidak valid.',
            icon: '🔍',
        },
        500: {
            title: 'Terjadi Kesalahan Server (500)',
            description: 'Sistem internal mengalami kendala pemrosesan data. Tim teknis telah menerima log anomali ini.',
            icon: '⚠️',
        },
        503: {
            title: 'Layanan Dalam Pemeliharaan (503)',
            description: 'Platform UNNESKost sedang menjalani pemeliharaan berkala untuk peningkatan performa.',
            icon: '🛠️',
        },
    };

    const details = errorDetails[status] || {
        title: `Terjadi Kesalahan (${status})`,
        description: message || 'Terjadi anomali saat memproses permintaan Anda.',
        icon: '⚠️',
    };

    return (
        <div className="min-h-screen bg-[#F8FAFC] text-slate-900 font-sans flex flex-col justify-between p-4">
            <Head title={`${details.title} | Pusat Kost UNNES`} />

            {/* Header Mini */}
            <header className="max-w-4xl w-full mx-auto py-4 flex justify-between items-center">
                <Link href="/" className="flex items-center gap-2">
                    <Logo variant="horizontal" className="h-9 w-auto" />
                </Link>
            </header>

            {/* Main Error Box */}
            <main className="max-w-md w-full mx-auto my-auto bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm text-center space-y-6">
                <div className="w-16 h-16 bg-rose-50 text-[#FF6565] rounded-3xl flex items-center justify-center text-3xl mx-auto border border-rose-100 shadow-2xs">
                    {details.icon}
                </div>

                <div className="space-y-2">
                    <h1 className="text-xl font-black text-slate-900 tracking-tight">
                        {details.title}
                    </h1>
                    <p className="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">
                        {details.description}
                    </p>
                </div>

                <div className="space-y-2 pt-2">
                    <Link href="/kos" className="block w-full">
                        <Button variant="primary" size="md" className="w-full shadow-xs">
                            Jelajahi Katalog Kos UNNES
                        </Button>
                    </Link>
                    <Link href="/" className="block w-full">
                        <Button variant="outline" size="sm" className="w-full">
                            Kembali ke Beranda
                        </Button>
                    </Link>
                </div>
            </main>

            {/* Footer */}
            <footer className="text-center py-4 text-xs text-slate-400">
                Pusat Kost UNNES &copy; {new Date().getFullYear()} - Sistem Pencarian & Sewa Kos Kampus
            </footer>
        </div>
    );
}
