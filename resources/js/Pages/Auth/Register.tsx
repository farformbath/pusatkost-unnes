import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        phone: '',
        role: 'tenant', // 'tenant' or 'owner'
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <PublicLayout>
            <Head title="Daftar Akun Baru | Pusat Kost UNNES" />

            <div className="max-w-lg mx-auto my-8">
                <div className="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm space-y-6">
                    {/* Header */}
                    <div className="text-center space-y-1.5">
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight">
                            Buat Akun UNNESKost
                        </h1>
                        <p className="text-xs text-slate-500">
                            Pilih tipe akun yang sesuai dengan kebutuhan Anda di sekitar kampus UNNES
                        </p>
                    </div>

                    {/* Role Selector Tabs */}
                    <div className="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl">
                        <button
                            type="button"
                            onClick={() => setData('role', 'tenant')}
                            className={`py-2.5 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
                                data.role === 'tenant'
                                    ? 'bg-white text-slate-900 shadow-sm'
                                    : 'text-slate-600 hover:text-slate-900'
                            }`}
                        >
                            <span>🎓</span>
                            <span>Pencari Kos (Mahasiswa)</span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setData('role', 'owner')}
                            className={`py-2.5 px-3 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 ${
                                data.role === 'owner'
                                    ? 'bg-white text-[#FF6565] shadow-sm'
                                    : 'text-slate-600 hover:text-slate-900'
                            }`}
                        >
                            <span>🏠</span>
                            <span>Pemilik Kos (Mitra)</span>
                        </button>
                    </div>

                    {/* Registration Form */}
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                {data.role === 'owner' ? 'Nama Lengkap Pemilik / Pengelola' : 'Nama Lengkap Mahasiswa'}
                            </label>
                            <input
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Contoh: Budi Santoso"
                                required
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                            {errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                Alamat Email Aktif
                            </label>
                            <input
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                placeholder="nama@email.com"
                                required
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                            {errors.email && <p className="text-xs text-red-500 mt-1">{errors.email}</p>}
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor WhatsApp Aktif (Opsional)
                            </label>
                            <input
                                type="tel"
                                value={data.phone}
                                onChange={(e) => setData('phone', e.target.value)}
                                placeholder="081234567890"
                                className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                            />
                            {errors.phone && <p className="text-xs text-red-500 mt-1">{errors.phone}</p>}
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kata Sandi
                                </label>
                                <input
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    placeholder="Min. 8 karakter"
                                    required
                                    className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                                />
                                {errors.password && <p className="text-xs text-red-500 mt-1">{errors.password}</p>}
                            </div>

                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1.5">
                                    Ulangi Kata Sandi
                                </label>
                                <input
                                    type="password"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                    placeholder="Konfirmasi sandi"
                                    required
                                    className="w-full text-xs p-3 rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none bg-slate-50/50"
                                />
                            </div>
                        </div>

                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            className="w-full shadow-md shadow-[#FF6565]/30 font-bold mt-2"
                            isLoading={processing}
                            disabled={processing}
                        >
                            {data.role === 'owner' ? 'Daftar Sebagai Pemilik Kos' : 'Daftar Sebagai Pencari Kos'}
                        </Button>
                    </form>

                    {/* Bottom Link */}
                    <div className="text-center pt-2 border-t border-slate-100 text-xs text-slate-500">
                        Sudah memiliki akun?{' '}
                        <Link href="/login" className="font-bold text-[#FF6565] hover:underline">
                            Masuk di Sini
                        </Link>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
