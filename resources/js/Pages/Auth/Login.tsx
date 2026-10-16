import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';

interface LoginProps {
    status?: string | null;
    can_show_demo?: boolean;
}

export default function Login({ status, can_show_demo }: LoginProps) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: true,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/login');
    };

    const fillDemoUser = (email: string) => {
        setData((prev) => ({
            ...prev,
            email: email,
            password: 'password',
        }));
    };

    return (
        <PublicLayout>
            <Head title="Masuk Akun | Pusat Kost UNNES" />

            <div className="max-w-md mx-auto my-8">
                <div className="bg-white rounded-2xl p-8 border border-slate-200/90 shadow-sm space-y-6">
                    {/* Header */}
                    <div className="text-center space-y-1.5">
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight">
                            Masuk ke Akun Anda
                        </h1>
                        <p className="text-xs text-slate-500">
                            Kelola pemesanan kamar, favorit kos, atau manajemen properti Anda
                        </p>
                    </div>

                    {status && (
                        <div className="p-3 bg-emerald-50 text-emerald-800 text-xs font-semibold rounded-xl border border-emerald-200">
                            {status}
                        </div>
                    )}

                    {/* Quick Demo Autofill Chips (Protected: Local/Testing Environment Only) */}
                    {can_show_demo && (
                        <div className="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 space-y-2 text-left">
                            <span className="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">
                                Demo Akun Cepat (Dev Only):
                            </span>
                            <div className="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    onClick={() => fillDemoUser('sukardi@unneskost.com')}
                                    className="text-[11px] font-semibold bg-white border border-slate-200 px-2.5 py-1 rounded-lg hover:border-primary hover:text-primary transition-colors cursor-pointer active:scale-98"
                                >
                                    🏠 Pemilik Kos (H. Sukardi)
                                </button>
                                <button
                                    type="button"
                                    onClick={() => fillDemoUser('anisa.rahma@students.unnes.ac.id')}
                                    className="text-[11px] font-semibold bg-white border border-slate-200 px-2.5 py-1 rounded-lg hover:border-primary hover:text-primary transition-colors cursor-pointer active:scale-98"
                                >
                                    🎓 Mahasiswa (Anisa FBS)
                                </button>
                            </div>
                        </div>
                    )}

                    {/* Login Form */}
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <Input
                            type="email"
                            label="Alamat Email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="nama@email.com"
                            required
                            error={errors.email}
                        />

                        <Input
                            type="password"
                            label="Kata Sandi (Password)"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="Masukkan kata sandi"
                            required
                            error={errors.password}
                        />

                        <div className="flex items-center justify-between text-xs">
                            <label className="flex items-center gap-2 cursor-pointer text-slate-600">
                                <input
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="rounded text-primary focus:ring-primary"
                                />
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            className="w-full shadow-md shadow-primary/30 font-bold"
                            isLoading={processing}
                            disabled={processing}
                        >
                            Masuk Sekarang
                        </Button>
                    </form>

                    {/* Bottom CTA */}
                    <div className="text-center pt-2 border-t border-slate-100 text-xs text-slate-500">
                        Belum punya akun UNNESKost?{' '}
                        <Link href="/register" className="font-bold text-primary hover:underline">
                            Daftar Gratis
                        </Link>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
