import React, { useRef } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';

interface EditProfileProps {
    user: {
        id: number | string;
        name: string;
        email: string;
        phone?: string | null;
        avatar_url?: string | null;
        role: string;
        owner_verification_status?: string | null;
    };
    status?: string | null;
}

export default function Edit({ user, status }: EditProfileProps) {
    const fileInputRef = useRef<HTMLInputElement>(null);

    const profileForm = useForm({
        name: user.name || '',
        email: user.email || '',
        phone: user.phone || '',
    });

    const avatarForm = useForm<{ avatar: File | null }>({
        avatar: null,
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const handleProfileSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        profileForm.put('/profile', {
            preserveScroll: true,
        });
    };

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            avatarForm.setData('avatar', e.target.files[0]);
            avatarForm.post('/profile/avatar', {
                preserveScroll: true,
                forceFormData: true,
            });
        }
    };

    const handlePasswordSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        passwordForm.put('/profile/password', {
            preserveScroll: true,
            onSuccess: () => passwordForm.reset(),
        });
    };

    return (
        <PublicLayout>
            <Head title="Pengaturan Profil | Pusat Kost UNNES" />

            <div className="max-w-2xl mx-auto space-y-6">
                {/* Page Title */}
                <div>
                    <h1 className="text-2xl font-black text-slate-900 tracking-tight">
                        Pengaturan Akun & Profil
                    </h1>
                    <p className="text-xs text-slate-500 mt-1">
                        Perbarui foto profil, identitas kontak, nomor WhatsApp, dan keamanan kata sandi Anda.
                    </p>
                </div>

                {status && (
                    <div className="p-3 bg-emerald-50 text-emerald-800 text-xs font-semibold rounded-2xl border border-emerald-200">
                        {status}
                    </div>
                )}

                {/* Section 0: Avatar Upload & Identity Card */}
                <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center gap-6">
                    <div className="relative group shrink-0">
                        <div className="w-24 h-24 rounded-full overflow-hidden bg-slate-100 border-2 border-slate-200 flex items-center justify-center text-3xl font-black text-slate-700 shadow-inner">
                            {user.avatar_url ? (
                                <img
                                    src={user.avatar_url}
                                    alt={user.name}
                                    className="w-full h-full object-cover"
                                />
                            ) : (
                                <span>{user.name.charAt(0).toUpperCase()}</span>
                            )}
                        </div>
                    </div>

                    <div className="space-y-2 text-center sm:text-left flex-1">
                        <div className="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h2 className="text-base font-bold text-slate-900">{user.name}</h2>
                            <span className="px-2.5 py-0.5 bg-rose-50 text-[#FF6565] border border-rose-100 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                {user.role}
                            </span>
                            {user.owner_verification_status && (
                                <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${
                                    user.owner_verification_status === 'verified'
                                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                        : user.owner_verification_status === 'rejected'
                                        ? 'bg-red-50 text-red-700 border border-red-200'
                                        : 'bg-amber-50 text-amber-700 border border-amber-200'
                                }`}>
                                    Status Mitra: {user.owner_verification_status}
                                </span>
                            )}
                        </div>
                        <p className="text-xs text-slate-500">{user.email}</p>

                        <div>
                            <input
                                type="file"
                                ref={fileInputRef}
                                onChange={handleAvatarChange}
                                accept="image/png, image/jpeg, image/webp"
                                className="hidden"
                            />
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                onClick={() => fileInputRef.current?.click()}
                                isLoading={avatarForm.processing}
                                disabled={avatarForm.processing}
                                className="text-xs mt-1"
                            >
                                📷 Unggah Foto Profil Baru
                            </Button>
                            {avatarForm.errors.avatar && (
                                <p className="text-xs text-red-500 mt-1">{avatarForm.errors.avatar}</p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Section 1: Profile Details */}
                <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                    <div className="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 className="text-base font-bold text-slate-900">Informasi Pribadi</h2>
                            <p className="text-xs text-slate-500">Nama dan kontak aktif Anda</p>
                        </div>
                    </div>

                    <form onSubmit={handleProfileSubmit} className="space-y-4">
                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                value={profileForm.data.name}
                                onChange={(e) => profileForm.setData('name', e.target.value)}
                                required
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                            {profileForm.errors.name && (
                                <p className="text-xs text-red-500 mt-1">{profileForm.errors.name}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                Alamat Email
                            </label>
                            <input
                                type="email"
                                value={profileForm.data.email}
                                onChange={(e) => profileForm.setData('email', e.target.value)}
                                required
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                            {profileForm.errors.email && (
                                <p className="text-xs text-red-500 mt-1">{profileForm.errors.email}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor WhatsApp / Telepon
                            </label>
                            <input
                                type="tel"
                                value={profileForm.data.phone}
                                onChange={(e) => profileForm.setData('phone', e.target.value)}
                                placeholder="081234567890"
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                            {profileForm.errors.phone && (
                                <p className="text-xs text-red-500 mt-1">{profileForm.errors.phone}</p>
                            )}
                        </div>

                        <div className="flex justify-end pt-2">
                            <Button
                                type="submit"
                                variant="primary"
                                size="md"
                                isLoading={profileForm.processing}
                                disabled={profileForm.processing}
                            >
                                Simpan Perubahan Profil
                            </Button>
                        </div>
                    </form>
                </div>

                {/* Section 2: Update Password */}
                <div className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                    <div className="pb-4 border-b border-slate-100">
                        <h2 className="text-base font-bold text-slate-900">Keamanan Kata Sandi</h2>
                        <p className="text-xs text-slate-500">Pastikan akun Anda menggunakan password yang aman</p>
                    </div>

                    <form onSubmit={handlePasswordSubmit} className="space-y-4">
                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                Kata Sandi Saat Ini
                            </label>
                            <input
                                type="password"
                                value={passwordForm.data.current_password}
                                onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                required
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                            {passwordForm.errors.current_password && (
                                <p className="text-xs text-red-500 mt-1">{passwordForm.errors.current_password}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                Kata Sandi Baru
                            </label>
                            <input
                                type="password"
                                value={passwordForm.data.password}
                                onChange={(e) => passwordForm.setData('password', e.target.value)}
                                required
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                            {passwordForm.errors.password && (
                                <p className="text-xs text-red-500 mt-1">{passwordForm.errors.password}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input
                                type="password"
                                value={passwordForm.data.password_confirmation}
                                onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                                required
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                        </div>

                        <div className="flex justify-end pt-2">
                            <Button
                                type="submit"
                                variant="outline"
                                size="md"
                                isLoading={passwordForm.processing}
                                disabled={passwordForm.processing}
                            >
                                Perbarui Kata Sandi
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </PublicLayout>
    );
}
