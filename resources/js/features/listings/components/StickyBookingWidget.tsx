import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { Listing } from '@/types/listing';
import { Button } from '@/components/ui/Button';
import { formatRupiah } from '@/lib/formatRupiah';

interface StickyBookingWidgetProps {
    listing: Listing;
    selectedRoomId?: string;
    onSelectRoom?: (roomId: string) => void;
}

export const StickyBookingWidget: React.FC<StickyBookingWidgetProps> = ({
    listing,
    selectedRoomId,
    onSelectRoom,
}) => {
    const availableRooms = listing.rooms?.filter((r) => r.status === 'available' && r.available_rooms > 0) || [];
    const [roomId, setRoomId] = useState(selectedRoomId || (availableRooms[0]?.id ?? ''));
    const [durationMonths, setDurationMonths] = useState(1);
    const today = new Date().toISOString().split('T')[0];
    const [checkIn, setCheckIn] = useState(today);

    const selectedRoom = listing.rooms?.find((r) => r.id === roomId) || availableRooms[0];
    const priceMonthly = selectedRoom ? Number(selectedRoom.price_monthly) : 0;
    const totalPrice = priceMonthly * Number(durationMonths);
    const depositPrice = selectedRoom ? Number(selectedRoom.price_deposit) : 0;
    const grandTotal = totalPrice + depositPrice;


    const handleBookingSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedRoom) return;
        router.visit(`/booking/${selectedRoom.id}/checkout`);
    };

    return (
        <div className="sticky top-28 bg-white rounded-3xl p-6 shadow-xl shadow-slate-900/5 border border-slate-200/90 space-y-5">
            {/* Header: Price Snapshot */}
            <div className="flex items-baseline justify-between pb-4 border-b border-slate-100">
                <div className="flex items-baseline gap-1">
                    <span className="text-2xl font-black text-primary">
                        {priceMonthly > 0 ? formatRupiah(priceMonthly) : 'Rp 0'}
                    </span>
                    <span className="text-xs text-slate-500 font-medium">/ bulan</span>
                </div>
                <div className="flex items-center gap-1 text-[11px] font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-full">
                    <span>Snapshot Harga</span>
                </div>
            </div>

            {/* Booking Form */}
            <form onSubmit={handleBookingSubmit} className="space-y-4">
                {/* 1. Room Picker */}
                <div>
                    <label className="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                        Pilih Tipe Kamar
                    </label>
                    {availableRooms.length > 0 ? (
                        <div className="space-y-2">
                            {availableRooms.map((room) => {
                                const isSelected = room.id === roomId || (!roomId && room.id === availableRooms[0].id);
                                return (
                                    <div
                                        key={room.id}
                                        onClick={() => {
                                            setRoomId(room.id);
                                            if (onSelectRoom) onSelectRoom(room.id);
                                        }}
                                        className={`p-3 rounded-2xl border transition-all cursor-pointer flex items-center justify-between gap-3 active:scale-[0.99] ${
                                            isSelected
                                                ? 'border-[#FF6565] bg-rose-50/60 shadow-2xs'
                                                : 'border-slate-200/90 bg-white hover:bg-slate-50'
                                        }`}
                                    >
                                        <div className="min-w-0">
                                            <div className="flex items-center gap-2">
                                                <span className={`text-xs ${isSelected ? 'font-black text-slate-900' : 'font-bold text-slate-700'}`}>
                                                    {room.name}
                                                </span>
                                                <span className="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                                    Sisa {room.available_rooms} kamar
                                                </span>
                                            </div>
                                            <div className="text-[11px] text-slate-500 font-medium mt-0.5">
                                                {room.size ? `${room.size} • ` : ''}{room.formatted_price_monthly}/bln
                                            </div>
                                        </div>

                                        <div className={`w-4 h-4 rounded-full border flex items-center justify-center text-[10px] shrink-0 ${
                                            isSelected
                                                ? 'border-[#FF6565] bg-[#FF6565] text-white font-black'
                                                : 'border-slate-300 bg-white'
                                        }`}>
                                            {isSelected && '✓'}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="p-3 bg-slate-100 rounded-xl text-xs text-slate-500 font-medium">
                            Saat ini belum ada kamar yang berstatus Available.
                        </div>
                    )}
                </div>

                {/* 2. Datepicker & Duration Grid */}
                <div className="space-y-3">
                    <div>
                        <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                            Rencana Tanggal Masuk
                        </label>
                        <input
                            type="date"
                            value={checkIn}
                            onChange={(e) => setCheckIn(e.target.value)}
                            min={today}
                            required
                            className="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-800 focus:border-slate-900 focus:outline-none bg-slate-50/50"
                        />
                    </div>

                    <div>
                        <label className="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            Durasi Sewa
                        </label>
                        <div className="grid grid-cols-4 gap-1.5 bg-slate-100/80 p-1 rounded-xl">
                            {[
                                { value: 1, label: '1 Bln' },
                                { value: 3, label: '3 Bln' },
                                { value: 6, label: '6 Bln' },
                                { value: 12, label: '1 Thn' },
                            ].map((dur) => (
                                <button
                                    key={dur.value}
                                    type="button"
                                    onClick={() => setDurationMonths(dur.value)}
                                    className={`py-2 text-xs font-bold rounded-lg transition-all cursor-pointer ${
                                        durationMonths === dur.value
                                            ? 'bg-white text-slate-950 shadow-xs'
                                            : 'text-slate-600 hover:text-slate-900'
                                    }`}
                                >
                                    {dur.label}
                                </button>
                            ))}
                        </div>
                    </div>
                </div>

                {/* 3. Price Breakdown Calculation */}
                <div className="pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div className="flex justify-between text-slate-600">
                        <span>
                            {formatRupiah(priceMonthly)} × {durationMonths} Bulan
                        </span>
                        <span className="font-semibold">{formatRupiah(totalPrice)}</span>
                    </div>

                    {depositPrice > 0 && (
                        <div className="flex justify-between text-slate-600">
                            <span>Deposit Jaminan</span>
                            <span className="font-semibold">{formatRupiah(depositPrice)}</span>
                        </div>
                    )}

                    <div className="flex justify-between text-slate-500">
                        <span>Biaya Platform UNNES</span>
                        <span className="text-emerald-700 font-semibold">Gratis</span>
                    </div>

                    <div className="pt-2 border-t border-slate-100 flex justify-between text-sm font-bold text-slate-900">
                        <span>Estimasi Total</span>
                        <span className="text-[#FF6565] text-base font-black">{formatRupiah(grandTotal)}</span>
                    </div>
                </div>

                {/* 4. Action Button */}
                <Button
                    variant="primary"
                    size="lg"
                    type="submit"
                    disabled={availableRooms.length === 0}
                    className="w-full py-3.5 shadow-md shadow-[#FF6565]/25 font-bold"
                >
                    {availableRooms.length > 0 ? 'Ajukan Sewa Sekarang →' : 'Semua Kamar Penuh'}
                </Button>
            </form>

            {/* Safe Notice */}
            <div className="pt-2 border-t border-slate-100 flex items-center gap-2 text-[11px] text-slate-400">
                <span className="text-emerald-600 font-bold">✓</span>
                <span>Jaminan Bebas Double-Booking dengan lock pesimistik.</span>
            </div>
        </div>
    );
};
