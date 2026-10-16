import React, { ReactNode, useState, useEffect, useRef } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { GearSix, Crown, UsersThree, Star, House, ClipboardText, Receipt, SignOut, Heart, CaretDown } from '@phosphor-icons/react';
import { Logo } from '@/components/shared/Logo';
import { Button } from '@/components/ui/Button';
import { NotificationBell } from '@/components/shared/NotificationBell';
import { ErrorBoundary } from '@/components/shared/ErrorBoundary';
import { ToastProvider, useToast } from '@/components/ui/Toast';
import { NesaWidget } from '@/components/nesa/NesaWidget';
import { SharedProps } from '@/types/auth';

interface PublicLayoutProps {
    children: ReactNode;
}

const FlashHandler: React.FC = () => {
    const { flash } = usePage<SharedProps>().props;
    const toast = useToast();
    const lastFlashKeyRef = useRef<string>('');

    useEffect(() => {
        const currentKey = `${flash?.success ?? ''}:::${flash?.error ?? ''}`;
        if (currentKey === ':::' || currentKey === lastFlashKeyRef.current) {
            return;
        }

        lastFlashKeyRef.current = currentKey;

        if (flash?.success) {
            toast.success(flash.success, 'Berhasil');
        }
        if (flash?.error) {
            toast.error(flash.error, 'Pemberitahuan');
        }
    }, [flash, toast]);

    return null;
};

const PublicLayoutContent: React.FC<PublicLayoutProps> = ({ children }) => {
    const { auth } = usePage<SharedProps>().props;
    const user = auth?.user;
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const userMenuRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent | TouchEvent) => {
            if (userMenuRef.current && !userMenuRef.current.contains(event.target as Node)) {
                setUserMenuOpen(false);
            }
        };

        if (userMenuOpen) {
            document.addEventListener('mousedown', handleClickOutside);
            document.addEventListener('touchstart', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('touchstart', handleClickOutside);
        };
    }, [userMenuOpen]);

    const handleLogout = (e: React.MouseEvent) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <div className="min-h-screen bg-surface-sunken text-foreground flex flex-col font-sans antialiased selection:bg-[#FF6565] selection:text-white">
            <FlashHandler />

            {/* Top Navigation Bar */}
            <header className="sticky top-0 z-40 bg-surface/95 backdrop-blur-md border-b border-border shadow-2xs">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
                    {/* Brand Logo */}
                    <Link href="/kos" className="flex items-center gap-2.5 group py-1">
                        <Logo variant="horizontal" className="h-9 sm:h-10 md:h-11 w-auto group-hover:opacity-95 transition-all" />
                    </Link>

                    {/* Right Menu Navigation */}
                    <div className="flex items-center gap-3 sm:gap-5 text-xs sm:text-sm font-medium text-foreground-secondary">
                        <Link href="/kos" className="hover:text-primary transition-colors hidden md:block">
                            Cari Kos
                        </Link>
                        <Link href="/wishlist" className="hover:text-primary transition-colors flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl hover:bg-slate-100">
                            <Heart weight="fill" className="w-4 h-4 text-primary" />
                            <span className="hidden sm:inline font-semibold">Favorit</span>
                        </Link>

                        {/* Owner Specific Shortcuts */}
                        {user?.is_owner && (
                            <>
                                <Link href="/owner/bookings" className="hover:text-primary transition-colors hidden lg:block font-semibold">
                                    Persetujuan Booking
                                </Link>
                                <Link href="/owner/kos">
                                    <Button variant="outline" size="sm" className="hidden sm:inline-flex shadow-2xs">
                                        Dashboard Pemilik
                                    </Button>
                                </Link>
                            </>
                        )}

                        {/* Notification Bell (for authenticated users) */}
                        {user && <NotificationBell />}

                        {/* User Profile / Auth State */}
                        {user ? (
                            <div ref={userMenuRef} className="relative">
                                <button
                                    type="button"
                                    onClick={() => setUserMenuOpen(!userMenuOpen)}
                                    className="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-100 transition-colors cursor-pointer border border-slate-200/90 active:scale-98"
                                >
                                    <div className="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs overflow-hidden">
                                        {user.avatar_url ? (
                                            <img src={user.avatar_url} alt={user.name} className="w-full h-full object-cover" />
                                        ) : (
                                            <span>{user.name.charAt(0).toUpperCase()}</span>
                                        )}
                                    </div>
                                    <span className="text-xs font-bold text-slate-800 hidden sm:inline max-w-28 truncate">
                                        {user.name}
                                    </span>
                                    <CaretDown weight="bold" className="w-3.5 h-3.5 text-muted-foreground" />
                                </button>

                                {/* Dropdown Menu */}
                                {userMenuOpen && (
                                    <div className="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-slate-200/90 py-2 z-50 animate-in fade-in text-xs">
                                        <div className="px-4 py-2.5 border-b border-slate-100">
                                            <p className="font-bold text-slate-900 truncate">{user.name}</p>
                                            <p className="text-[11px] text-slate-400 truncate">{user.email}</p>
                                            <div className="flex items-center gap-1 mt-1.5">
                                                <span className="px-2 py-0.5 bg-rose-50 text-[#FF6565] border border-rose-100 rounded-md text-[10px] font-bold">
                                                    {user.is_owner ? 'Pemilik Kos' : 'Mahasiswa'}
                                                </span>
                                                {user.is_owner && user.owner_verification_status && (
                                                    <span className={`px-1.5 py-0.5 rounded-md text-[9px] font-bold uppercase ${
                                                        user.owner_verification_status === 'verified'
                                                            ? 'bg-emerald-50 text-emerald-700'
                                                            : 'bg-amber-50 text-amber-700'
                                                    }`}>
                                                        {user.owner_verification_status}
                                                    </span>
                                                )}
                                            </div>
                                        </div>

                                        <Link
                                            href="/profile"
                                            className="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 transition-colors font-medium text-slate-700"
                                            onClick={() => setUserMenuOpen(false)}
                                        >
                                            <GearSix weight="bold" className="w-3.5 h-3.5 shrink-0" /> Pengaturan Profil
                                        </Link>

                                        {user.is_admin && (
                                            <>
                                                <Link
                                                    href="/admin/dashboard"
                                                    className="flex items-center gap-2 px-4 py-2 hover:bg-purple-50 text-purple-800 transition-colors font-bold"
                                                    onClick={() => setUserMenuOpen(false)}
                                                >
                                                    <Crown weight="bold" className="w-3.5 h-3.5 shrink-0" /> Panel Administrator
                                                </Link>
                                                <Link
                                                    href="/admin/owners"
                                                    className="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 transition-colors font-medium text-slate-700"
                                                    onClick={() => setUserMenuOpen(false)}
                                                >
                                                    <UsersThree weight="bold" className="w-3.5 h-3.5 shrink-0" /> Verifikasi Pemilik
                                                </Link>
                                                <Link
                                                    href="/admin/reviews"
                                                    className="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 transition-colors font-medium text-slate-700"
                                                    onClick={() => setUserMenuOpen(false)}
                                                >
                                                    <Star weight="bold" className="w-3.5 h-3.5 shrink-0" /> Moderasi Ulasan
                                                </Link>
                                            </>
                                        )}

                                        {user.is_owner && (
                                            <>
                                                <Link
                                                    href="/owner/kos"
                                                    className="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 transition-colors font-medium text-slate-700"
                                                    onClick={() => setUserMenuOpen(false)}
                                                >
                                                    <House weight="bold" className="w-3.5 h-3.5 shrink-0" /> Kelola Kos Saya
                                                </Link>
                                                <Link
                                                    href="/owner/bookings"
                                                    className="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 transition-colors font-medium text-slate-700"
                                                    onClick={() => setUserMenuOpen(false)}
                                                >
                                                    <ClipboardText weight="bold" className="w-3.5 h-3.5 shrink-0" /> Manajemen Booking
                                                </Link>
                                            </>
                                        )}

                                        <Link
                                            href="/tenant/bookings"
                                            className="flex items-center gap-2 px-4 py-2 hover:bg-slate-50 transition-colors font-medium text-slate-700"
                                            onClick={() => setUserMenuOpen(false)}
                                        >
                                            <Receipt weight="bold" className="w-3.5 h-3.5 shrink-0" /> Riwayat Booking Saya
                                        </Link>

                                        <div className="border-t border-slate-100 mt-1 pt-1">
                                            <button
                                                type="button"
                                                onClick={handleLogout}
                                                className="w-full flex items-center gap-2 text-left px-4 py-2 text-rose-600 hover:bg-rose-50 transition-colors font-bold cursor-pointer"
                                            >
                                                <SignOut weight="bold" className="w-3.5 h-3.5 shrink-0" /> Keluar (Logout)
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </div>
                        ) : (
                            <div className="flex items-center gap-2">
                                <Link href="/login">
                                    <Button variant="ghost" size="sm">
                                        Masuk
                                    </Button>
                                </Link>
                                <Link href="/register">
                                    <Button variant="primary" size="sm" className="shadow-xs shadow-[#FF6565]/30 font-bold">
                                        Daftar
                                    </Button>
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </header>

            {/* Main Content Area protected by ErrorBoundary */}
            <main className="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <ErrorBoundary>
                    {children}
                </ErrorBoundary>
            </main>

            {/* Clean Minimalist Footer */}
            <footer className="border-t border-border py-8 mt-auto text-xs text-muted bg-surface">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div className="flex flex-wrap items-center gap-6">
                        <Link href="/kos" className="hover:text-primary transition-colors font-medium">Katalog Kos UNNES</Link>
                        <Link href="/owner/kos" className="hover:text-primary transition-colors font-medium">Area Pemilik Kos</Link>
                        <Link href="/owner/bookings" className="hover:text-primary transition-colors font-medium">Manajemen Booking</Link>
                    </div>
                    <div>
                        <span className="font-semibold text-slate-700">Pusat Kost UNNES v3</span> (unneskost.com) - Hak Cipta 2026. All rights reserved.
                    </div>
                </div>
            </footer>

            {/* NESA AI Smart Assistant Floating Widget */}
            <NesaWidget />
        </div>
    );
};

export const PublicLayout: React.FC<PublicLayoutProps> = ({ children }) => {
    return (
        <ToastProvider>
            <PublicLayoutContent>
                {children}
            </PublicLayoutContent>
        </ToastProvider>
    );
};
