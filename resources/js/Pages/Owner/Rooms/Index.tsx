import React, { FormEvent, useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { Facility, Listing, Room } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { StatusBadge } from '@/components/ui/Badge';
import { Input } from '@/components/ui/Input';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/ui/Modal';
import { FacilityBadge } from '@/features/listings/components/FacilityBadge';

interface IndexProps {
    listing: Listing;
    facilities: Facility[];
}

export default function Index({ listing, facilities }: IndexProps) {
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm<{
        name: string;
        size: string;
        price_monthly: number;
        price_deposit: number;
        total_rooms: number;
        available_rooms: number;
        facility_ids: string[];
    }>({
        name: '',
        size: '3x4 m',
        price_monthly: 750000,
        price_deposit: 0,
        total_rooms: 1,
        available_rooms: 1,
        facility_ids: [],
    });

    const handleFacilityToggle = (facilityId: string) => {
        if (data.facility_ids.includes(facilityId)) {
            setData('facility_ids', data.facility_ids.filter((id) => id !== facilityId));
        } else {
            setData('facility_ids', [...data.facility_ids, facilityId]);
        }
    };

    const handleCreateRoom = (e: FormEvent) => {
        e.preventDefault();
        post(`/owner/kos/${listing.id}/kamar`, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                setIsAddModalOpen(false);
                reset();
            },
        });
    };

    const handleToggleAvailability = (room: Room) => {
        router.patch(`/owner/kamar/${room.id}/toggle`, {}, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <PublicLayout>
            <Head title={`Kelola Kamar | ${listing.name}`} />

            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                    <div>
                        <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider block">
                            Manajemen Tipe Kamar
                        </span>
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                            {listing.name}
                        </h1>
                        <p className="text-xs text-slate-500 mt-1">
                            {listing.rooms?.length || 0} tipe kamar terdaftar.
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link href={`/owner/kos/${listing.id}/edit`}>
                            <Button variant="outline" size="sm">
                                ← Edit Kos & Foto
                            </Button>
                        </Link>
                        <Button variant="primary" size="sm" onClick={() => setIsAddModalOpen(true)}>
                            + Tambah Tipe Kamar
                        </Button>
                    </div>
                </div>

                {/* Rooms List */}
                <div className="space-y-4">
                    {listing.rooms && listing.rooms.length > 0 ? (
                        listing.rooms.map((room) => (
                            <div
                                key={room.id}
                                className="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-slate-300 transition-colors"
                            >
                                <div className="space-y-2">
                                    <div className="flex flex-wrap items-center gap-2">
                                        <h3 className="text-base font-bold text-slate-900">{room.name}</h3>
                                        {room.size && (
                                            <span className="text-xs px-2 py-0.5 rounded-md bg-slate-100 font-medium text-slate-600">
                                                {room.size}
                                            </span>
                                        )}
                                        <StatusBadge status={room.status} />
                                    </div>

                                    <div className="flex items-baseline gap-2">
                                        <span className="text-lg font-black text-[#FF6565]">
                                            {room.formatted_price_monthly}
                                        </span>
                                        <span className="text-xs text-slate-500">/ bulan</span>
                                        {room.price_deposit > 0 && (
                                            <span className="text-xs text-slate-400">
                                                • Deposit {room.formatted_price_deposit}
                                            </span>
                                        )}
                                    </div>

                                    <p className="text-xs text-slate-600">
                                        Tersedia: <strong>{room.available_rooms}</strong> dari <strong>{room.total_rooms}</strong> kamar
                                    </p>

                                    {/* Room facilities */}
                                    {room.facilities && room.facilities.length > 0 && (
                                        <div className="flex flex-wrap gap-1.5 pt-1">
                                            {room.facilities.map((f) => (
                                                <FacilityBadge key={f.id} facility={f} className="text-[11px] py-0.5" />
                                            ))}
                                        </div>
                                    )}
                                </div>

                                {/* Availability Toggle Action */}
                                <div className="flex items-center gap-2 self-end sm:self-center">
                                    <Button
                                        variant={room.status === 'available' ? 'outline' : 'secondary'}
                                        size="sm"
                                        onClick={() => handleToggleAvailability(room)}
                                    >
                                        {room.status === 'available' ? 'Tandai Kamar Penuh' : 'Tandai Kamar Tersedia'}
                                    </Button>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="text-center py-16 px-4 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                            <div className="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
                                🛏️
                            </div>
                            <h3 className="text-lg font-bold text-slate-800">
                                Belum ada kamar yang didaftarkan
                            </h3>
                            <p className="text-sm text-slate-500 max-w-md mx-auto">
                                Kos Anda memerlukan minimal 1 tipe kamar sebelum dapat dipublikasikan ke pencari kos.
                            </p>
                            <Button variant="primary" size="md" onClick={() => setIsAddModalOpen(true)}>
                                + Tambah Tipe Kamar Pertama
                            </Button>
                        </div>
                    )}
                </div>
            </div>

            {/* Add Room Modal */}
            <Modal
                isOpen={isAddModalOpen}
                onClose={() => setIsAddModalOpen(false)}
                title="Tambah Tipe Kamar Baru"
                maxWidth="lg"
            >
                <form onSubmit={handleCreateRoom} className="space-y-4">
                    <Input
                        label="Nama Tipe Kamar"
                        placeholder="Contoh: Kamar Mandi Luar Standar, Kamar VIP AC"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        error={errors.name}
                        required
                    />

                    <div className="grid grid-cols-2 gap-3">
                        <Input
                            label="Ukuran Kamar"
                            placeholder="Contoh: 3x3 m, 3x4 m"
                            value={data.size}
                            onChange={(e) => setData('size', e.target.value)}
                            error={errors.size}
                        />

                        <Input
                            label="Harga Sewa Bulanan (Rp)"
                            type="number"
                            placeholder="750000"
                            value={data.price_monthly}
                            onChange={(e) => setData('price_monthly', parseInt(e.target.value, 10) || 0)}
                            error={errors.price_monthly}
                            required
                        />
                    </div>

                    <div className="grid grid-cols-2 gap-3">
                        <Input
                            label="Total Jumlah Kamar Tipe Ini"
                            type="number"
                            min={1}
                            value={data.total_rooms}
                            onChange={(e) => setData('total_rooms', parseInt(e.target.value, 10) || 1)}
                            error={errors.total_rooms}
                            required
                        />

                        <Input
                            label="Jumlah Kamar yang Tersedia"
                            type="number"
                            min={0}
                            value={data.available_rooms}
                            onChange={(e) => setData('available_rooms', parseInt(e.target.value, 10) || 0)}
                            error={errors.available_rooms}
                            required
                        />
                    </div>

                    <div className="space-y-2">
                        <label className="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Fasilitas Khusus Kamar Ini
                        </label>
                        <div className="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-1">
                            {facilities.map((f) => (
                                <label key={f.id} className="flex items-center gap-2 text-xs text-slate-700">
                                    <input
                                        type="checkbox"
                                        checked={data.facility_ids.includes(f.id)}
                                        onChange={() => handleFacilityToggle(f.id)}
                                        className="rounded text-[#FF6565]"
                                    />
                                    <span>{f.name}</span>
                                </label>
                            ))}
                        </div>
                    </div>

                    <div className="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <Button variant="outline" type="button" onClick={() => setIsAddModalOpen(false)}>
                            Batal
                        </Button>
                        <Button variant="primary" type="submit" isLoading={processing}>
                            Simpan Tipe Kamar
                        </Button>
                    </div>
                </form>
            </Modal>
        </PublicLayout>
    );
}
