import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { ShieldCheck, MapTrifold, Receipt, LockSimple, GraduationCap } from '@phosphor-icons/react';
import { Logo } from '@/components/shared/Logo';
import { Button } from '@/components/ui/Button';

interface WelcomeProps {
    appName?: string;
    _phpVersion?: string;
    _laravelVersion?: string;
}

export const Welcome: React.FC<WelcomeProps> = ({
    appName = 'Pusat Kost UNNES',
}) => {
    return (
        <>
            <Head title={`Selamat Datang | ${appName}`} />

            <div className="min-h-screen bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-primary selection:text-white">
                {/* Navbar */}
                <header className="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <Logo variant="horizontal" className="h-9 sm:h-10 md:h-11" />
                        </div>
                        <div className="flex items-center gap-2 sm:gap-3">
                            <Link href="/kos">
                                <Button variant="primary" size="sm" className="shadow-xs shadow-primary/30">
                                    Jelajahi Kos UNNES →
                                </Button>
                            </Link>
                        </div>
                    </div>
                </header>

                {/* Main Hero Section */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 flex-1 flex flex-col justify-center">
                    <div className="max-w-4xl mx-auto text-center space-y-7">
                        {/* Trust Eyebrow */}
                        <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-50 border border-primary-200 text-primary text-xs font-bold shadow-2xs">
                            <GraduationCap weight="bold" className="w-4 h-4" />
                            <span>Platform Pencarian & Sewa Kos Mahasiswa Universitas Negeri Semarang</span>
                        </div>

                        {/* Display Headline */}
                        <h1 className="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-slate-950 font-sans leading-[1.15]">
                            Temukan Kos Nyaman, Dekat Fakultas,{' '}
                            <span className="text-primary">Bebas Penipuan.</span>
                        </h1>

                        <p className="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto font-normal">
                            Katalog kos putra, putri, dan campur terverifikasi di kawasan Sekaran, Banaran, Taman Siswa, dan seluruh gerbang kampus UNNES dengan jaminan ketersediaan kamar riil.
                        </p>

                        {/* CTA Buttons */}
                        <div className="pt-2 flex flex-wrap items-center justify-center gap-3">
                            <Link href="/kos">
                                <Button variant="primary" size="lg" className="shadow-lg shadow-primary/25 font-bold px-8 py-3.5 text-sm">
                                    Cari Kos Sekarang
                                </Button>
                            </Link>
                            <Link href="/owner/kos/create">
                                <Button variant="outline" size="lg" className="border-slate-300 font-bold px-7 py-3.5 text-sm text-slate-700 hover:bg-slate-50">
                                    Pasang Iklan Kos
                                </Button>
                            </Link>
                        </div>

                        {/* Campus Area Quick Links */}
                        <div className="pt-8 border-t border-slate-200/80">
                            <p className="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                                Wilayah Populer Sekitar Kampus:
                            </p>
                            <div className="flex flex-wrap items-center justify-center gap-2">
                                {[
                                    { name: 'Sekaran', desc: 'Dekat Rektorat, FBS, FEB' },
                                    { name: 'Banaran', desc: 'Dekat FT & FMIPA' },
                                    { name: 'Taman Siswa', desc: 'Pusat Kuliner & Bank' },
                                    { name: 'Gunungpati', desc: 'Dekat FIP & FIK' },
                                    { name: 'Patemon', desc: 'Gerbang Belakang' },
                                ].map((area) => (
                                    <Link
                                        key={area.name}
                                        href={`/kos?area=${encodeURIComponent(area.name)}`}
                                        className="px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 transition-all shadow-2xs hover:border-slate-300"
                                    >
                                        {area.name} <span className="text-slate-400 font-normal">({area.desc})</span>
                                    </Link>
                                ))}
                            </div>
                        </div>

                        {/* 4 Feature Highlights Grid */}
                        <div className="pt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left">
                            <div className="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1.5">
                                <div className="w-10 h-10 rounded-2xl bg-primary-50 flex items-center justify-center text-primary">
                                    <ShieldCheck weight="bold" className="w-5 h-5" />
                                </div>
                                <div className="text-sm font-bold text-slate-900">Verifikasi Dua Arah</div>
                                <div className="text-xs text-slate-500 leading-relaxed">
                                    Admin mengonfirmasi tenant dan owner via WhatsApp sebelum aktivasi sewa.
                                </div>
                            </div>

                            <div className="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1.5">
                                <div className="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                    <MapTrifold weight="bold" className="w-5 h-5" />
                                </div>
                                <div className="text-sm font-bold text-slate-900">Peta Jarak Fakultas</div>
                                <div className="text-xs text-slate-500 leading-relaxed">
                                    Lihat batas resmi kampus UNNES 121 titik dan perkiraan waktu tempuh jalan kaki.
                                </div>
                            </div>

                            <div className="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1.5">
                                <div className="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                                    <Receipt weight="bold" className="w-5 h-5" />
                                </div>
                                <div className="text-sm font-bold text-slate-900">Kwitansi Digital Sah</div>
                                <div className="text-xs text-slate-500 leading-relaxed">
                                    Dapatkan invoice dan bukti pembayaran PDF resmi yang siap dicetak kapan saja.
                                </div>
                            </div>

                            <div className="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-1.5">
                                <div className="w-10 h-10 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                                    <LockSimple weight="bold" className="w-5 h-5" />
                                </div>
                                <div className="text-sm font-bold text-slate-900">Bebas Double-Booking</div>
                                <div className="text-xs text-slate-500 leading-relaxed">
                                    Proteksi stok kamar realtime dengan penguncian database saat checkout berlangsung.
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="border-t border-slate-200 bg-white py-6">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                        <div className="flex items-center gap-2">
                            <span>© {new Date().getFullYear()} Pusat Kost UNNES (unneskost.com)</span>
                        </div>
                        <div className="flex items-center gap-4">
                            <span>Sekaran, Gunungpati, Kota Semarang</span>
                            <span>•</span>
                            <span className="font-medium text-slate-700">Modular Monolith Architecture</span>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
};

export default Welcome;
