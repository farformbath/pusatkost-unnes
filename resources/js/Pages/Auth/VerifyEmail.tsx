import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Button } from '@/components/ui/Button';

interface VerifyEmailProps {
    status?: string | null;
}

export default function VerifyEmail({ status }: VerifyEmailProps) {
    const { post, processing } = useForm({});

    const handleResend = (e: React.FormEvent) => {
        e.preventDefault();
        post('/email/verification-notification');
    };

    return (
        <PublicLayout>
            <Head title="Verifikasi Alamat Email | Pusat Kost UNNES" />

            <div className="max-w-md mx-auto my-10">
                <div className="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-sm space-y-6 text-center">
                    <div className="w-14 h-14 bg-rose-50 text-[#FF6565] rounded-full flex items-center justify-center text-2xl mx-auto border border-rose-100">
                        ✉️
                    </div>

                    <div className="space-y-2">
                        <h1 className="text-xl font-black text-slate-900 tracking-tight">
                            Verifikasi Alamat Email Anda
                        </h1>
                        <p className="text-xs text-slate-500 leading-relaxed">
                            Terima kasih telah mendaftar di Pusat Kost UNNES. Sebelum memulai, silakan klik tautan verifikasi yang baru saja kami kirimkan ke email Anda.
                        </p>
                    </div>

                    {status === 'verification-link-sent' && (
                        <div className="p-3 bg-emerald-50 text-emerald-800 text-xs font-semibold rounded-xl border border-emerald-200">
                            Tautan verifikasi baru telah berhasil dikirim ke alamat email Anda.
                        </div>
                    )}

                    <form onSubmit={handleResend} className="space-y-3">
                        <Button
                            type="submit"
                            variant="primary"
                            size="md"
                            className="w-full shadow-xs"
                            isLoading={processing}
                            disabled={processing}
                        >
                            Kirim Ulang Email Verifikasi
                        </Button>
                    </form>
                </div>
            </div>
        </PublicLayout>
    );
}
