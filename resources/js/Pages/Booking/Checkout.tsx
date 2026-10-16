import React, { FormEvent, useMemo } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { Listing, Room } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Input } from '@/components/ui/Input';
import { Select } from '@/components/ui/Select';
import { Button } from '@/components/ui/Button';
import { GenderBadge } from '@/components/ui/Badge';
import { formatRupiah } from '@/lib/formatRupiah';

interface CheckoutProps {
    room: Room;
    listing: Listing;
    user?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

export default function Checkout({ room, listing, user }: CheckoutProps) {
    const today = new Date().toISOString().split('T')[0];

    const { data, setData, post, processing, errors } = useForm<{
        listing_id: string;
        room_id: string;
        tenant_name: string;
        tenant_phone: string;
        start_date: string;
        duration_months: number;
        notes: string;
    }>({
        listing_id: listing.id,
        room_id: room.id,
        tenant_name: user?.name || '',
        tenant_phone: '',
        start_date: today,
        duration_months: 1,
        notes: '',
    });


    // Real-time calculation with Pure Integer Rupiah (Zero Float)
    const calculation = useMemo(() => {
        const duration = Math.max(1, Math.min(12, data.duration_months));
        const totalAmount = room.price_monthly * duration;
        const depositAmount = room.price_deposit || 0;
        const grandTotal = totalAmount + depositAmount;

        return {
            duration,
            totalAmount,
            depositAmount,
            grandTotal,
            formattedTotal: formatRupiah(totalAmount),
            formattedDeposit: formatRupiah(depositAmount),
            formattedGrandTotal: formatRupiah(grandTotal),
        };
    }, [room.price_monthly, room.price_deposit, data.duration_months]);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/booking');
    };

    return (
        <PublicLayout>
            <Head title={`Pengajuan Sewa ${room.name} | ${listing.name}`} />

            <div className="max-w-5xl mx-auto space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight">
                            Formulir Pengajuan Sewa Kamar Kos
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500">
                            Isi data calon penyewa dan pilih durasi masa sewa Anda di sekitar kampus UNNES.
                        </p>
                    </div>

                    <Link href={`/kos/${listing.slug}`}>
                        <Button variant="ghost" size="sm">
                            ← Kembali ke Detail Kos
                        </Button>
                    </Link>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    {/* Left Form: Tenant Data & Duration */}
                    <form onSubmit={handleSubmit} className="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                        {/* Section 1: Tenant Information */}
                        <div className="space-y-4">
                            <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                                1. Data Diri Calon Penyewa
                            </h2>

                            <Input
                                label="Nama Lengkap Sesuai KTP / KTM"
                                placeholder="Contoh: Siti Nurhaliza"
                                value={data.tenant_name}
                                onChange={(e) => setData('tenant_name', e.target.value)}
                                error={errors.tenant_name}
                                required
                            />

                            <Input
                                label="Nomor WhatsApp Aktif"
                                placeholder="Contoh: 081234567890"
                                value={data.tenant_phone}
                                onChange={(e) => setData('tenant_phone', e.target.value)}
                                error={errors.tenant_phone}
                                hint="Nomor ini akan digunakan pengelola kos untuk konfirmasi check-in."
                                required
                            />
                        </div>

                        {/* Section 2: Booking Period */}
                        <div className="space-y-4">
                            <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                                2. Jadwal & Durasi Sewa
                            </h2>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <Input
                                    label="Tanggal Mulai Masuk (Check-in)"
                                    type="date"
                                    min={today}
                                    value={data.start_date}
                                    onChange={(e) => setData('start_date', e.target.value)}
                                    error={errors.start_date}
                                    required
                                />

                                <Select
                                    label="Durasi Sewa"
                                    value={data.duration_months}
                                    onChange={(e) => setData('duration_months', parseInt(e.target.value, 10) || 1)}
                                    error={errors.duration_months}
                                    options={[
                                        { value: 1, label: '1 Bulan (Bulanan)' },
                                        { value: 3, label: '3 Bulan (1 Triwulan)' },
                                        { value: 6, label: '6 Bulan (1 Semester)' },
                                        { value: 12, label: '12 Bulan (1 Tahun Penuh)' },
                                    ]}
                                />
                            </div>

                            <div className="space-y-1.5">
                                <label className="block text-sm font-medium text-slate-700">
                                    Catatan Tambahan untuk Pemilik Kos (Opsional)
                                </label>
                                <textarea
                                    rows={3}
                                    className="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none"
                                    placeholder="Contoh: Perkiraan tiba jam 14.00, membawa 1 motor..."
                                    value={data.notes}
                                    onChange={(e) => setData('notes', e.target.value)}
                                />
                                {errors.notes && <p className="text-xs text-red-600 font-medium">{errors.notes}</p>}
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div className="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <p className="text-xs text-slate-400">
                                Unit kamar akan dikunci sementara selama proses pembayaran.
                            </p>
                            <Button
                                variant="primary"
                                size="lg"
                                type="submit"
                                isLoading={processing}
                                disabled={processing}
                            >
                                {processing ? 'Memproses Pesanan...' : 'Lanjut ke Pembayaran →'}
                            </Button>
                        </div>
                    </form>

                    {/* Right Summary Card */}
                    <div className="space-y-4">
                        <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4 sticky top-24">
                            <h3 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                                Rincian Pesanan
                            </h3>

                            {/* Property & Room Snapshot */}
                            <div className="space-y-2 text-xs">
                                <div className="flex items-center gap-2">
                                    <GenderBadge gender={listing.gender_type} />
                                    <span className="font-semibold text-slate-700">Area {listing.area}</span>
                                </div>
                                <h4 className="text-sm font-bold text-slate-900">{listing.name}</h4>
                                <p className="text-slate-500">{listing.address}</p>

                                <div className="mt-2 p-3 bg-slate-50 rounded-xl border border-slate-200/60 space-y-1">
                                    <span className="text-xs font-bold text-slate-800 block">{room.name}</span>
                                    {room.size && <span className="text-slate-500 block">Ukuran: {room.size}</span>}
                                    <span className="text-[#FF6565] font-extrabold block">{room.formatted_price_monthly} / bulan</span>
                                </div>
                            </div>

                            {/* Price Breakdown */}
                            <div className="border-t border-slate-100 pt-3 space-y-2 text-xs">
                                <div className="flex justify-between text-slate-600">
                                    <span>Biaya Sewa ({calculation.duration} Bulan)</span>
                                    <span className="font-semibold">{calculation.formattedTotal}</span>
                                </div>

                                {calculation.depositAmount > 0 && (
                                    <div className="flex justify-between text-slate-600">
                                        <span>Deposit Jaminan (Dikembalikan)</span>
                                        <span className="font-semibold">{calculation.formattedDeposit}</span>
                                    </div>
                                )}

                                <div className="flex justify-between text-slate-500">
                                    <span>Biaya Platform UNNES</span>
                                    <span className="text-emerald-700 font-semibold">Gratis</span>
                                </div>

                                <div className="border-t border-slate-100 pt-3 flex justify-between items-baseline">
                                    <span className="text-sm font-bold text-slate-900">Total Pembayaran</span>
                                    <span className="text-lg font-black text-[#FF6565]">
                                        {calculation.formattedGrandTotal}
                                    </span>
                                </div>
                            </div>

                            <div className="p-3 bg-emerald-50 rounded-xl border border-emerald-200/60 text-xs text-emerald-800 space-y-1">
                                <p className="font-semibold">✓ Jaminan Bebas Double-Booking</p>
                                <p className="text-[11px] text-emerald-700">
                                    Kamar ini langsung dikunci pesimistik secara eksklusif untuk Anda selama 24 jam.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
