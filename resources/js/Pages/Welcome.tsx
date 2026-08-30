import React from 'react';
import { Head } from '@inertiajs/react';
import { Logo } from '@/components/shared/Logo';

interface WelcomeProps {
    appName?: string;
    phpVersion?: string;
    laravelVersion?: string;
}

export const Welcome: React.FC<WelcomeProps> = ({
    appName = 'Pusat Kost UNNES',
    phpVersion = '8.4',
    laravelVersion = '12.x',
}) => {
    return (
        <>
            <Head title={`Selamat Datang — ${appName}`} />

            <div className="min-h-screen bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-[#FF6565] selection:text-white">
                {/* Navbar */}
                <header className="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <Logo variant="horizontal" className="h-9 sm:h-10" />
                        </div>
                        <div className="flex items-center gap-2 sm:gap-3">
                            <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Milestone 0 Verified
                            </span>
                        </div>
                    </div>
                </header>

                {/* Main Hero / Verification Card */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 flex-1 flex flex-col justify-center">
                    <div className="max-w-3xl mx-auto text-center space-y-6">
                        <div className="inline-flex items-center justify-center p-2 bg-white rounded-2xl shadow-sm border border-slate-200 mb-2">
                            <Logo variant="square" className="h-16 w-16" />
                        </div>

                        <h1 className="text-3xl sm:text-5xl font-bold tracking-tight text-slate-900 font-sans">
                            Platform Pencarian & Sewa Kost{' '}
                            <span className="text-[#FF6565]">Sekitar Kampus UNNES</span>
                        </h1>

                        <p className="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto">
                            Fondasi sistem arsitektur Modular Monolith, Strict TypeScript, Inertia.js v3, dan PostgreSQL 16 telah aktif dan terverifikasi secara menyeluruh.
                        </p>

                        {/* Stack Verification Badges */}
                        <div className="pt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-left">
                            <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                                <div className="text-xs text-slate-500 font-medium">Core Backend</div>
                                <div className="text-sm font-semibold text-slate-900 mt-1">Laravel {laravelVersion}</div>
                                <div className="text-xs text-slate-500">PHP {phpVersion} Strict</div>
                            </div>

                            <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                                <div className="text-xs text-slate-500 font-medium">Frontend Stack</div>
                                <div className="text-sm font-semibold text-slate-900 mt-1">React 19 & Inertia v3</div>
                                <div className="text-xs text-slate-500">Tailwind CSS v4</div>
                            </div>

                            <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                                <div className="text-xs text-slate-500 font-medium">Database & Cache</div>
                                <div className="text-sm font-semibold text-slate-900 mt-1">PostgreSQL 16</div>
                                <div className="text-xs text-slate-500">Redis 7 In-Memory</div>
                            </div>

                            <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
                                <div className="text-xs text-slate-500 font-medium">Quality Gate</div>
                                <div className="text-sm font-semibold text-emerald-600 mt-1">PHPStan Level Max</div>
                                <div className="text-xs text-slate-500">Pest & Pint Verified</div>
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
