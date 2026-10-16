import React from 'react';
import { Room } from '@/types/listing';
import { StatusBadge } from '@/components/ui/Badge';
import { FacilityBadge } from './FacilityBadge';
import { Button } from '@/components/ui/Button';

interface RoomCardProps {
    room: Room;
    onSelectRoom?: (room: Room) => void;
}

export const RoomCard: React.FC<RoomCardProps> = ({ room, onSelectRoom }) => {
    const isAvailable = room.status === 'available' && room.available_rooms > 0;

    return (
        <div className="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs space-y-4 hover:border-slate-300 transition-colors">
            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <div className="flex items-center gap-2">
                        <h4 className="text-base font-bold text-slate-900">{room.name}</h4>
                        {room.size && (
                            <span className="text-xs px-2 py-0.5 rounded bg-slate-100 font-medium text-slate-600">
                                {room.size}
                            </span>
                        )}
                    </div>
                    <p className="text-xs text-slate-500 mt-0.5">
                        {isAvailable ? (
                            <span className="text-teal-700 font-medium">Tersedia {room.available_rooms} dari {room.total_rooms} kamar</span>
                        ) : (
                            <span className="text-slate-400 font-medium">Kamar penuh / tidak tersedia</span>
                        )}
                    </p>
                </div>

                <div className="flex items-center gap-2 self-start">
                    <StatusBadge status={room.status} />
                </div>
            </div>

            {/* Room Facilities */}
            {room.facilities && room.facilities.length > 0 && (
                <div className="space-y-1.5">
                    <span className="text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Fasilitas Kamar
                    </span>
                    <div className="flex flex-wrap gap-1.5">
                        {room.facilities.map((facility) => (
                            <FacilityBadge key={facility.id} facility={facility} />
                        ))}
                    </div>
                </div>
            )}

            {/* Price & Action */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                <div>
                    <div className="flex items-baseline gap-1">
                        <span className="text-lg font-black text-[#FF6565]">
                            {room.formatted_price_monthly}
                        </span>
                        <span className="text-xs text-slate-500 font-medium">/ bulan</span>
                    </div>
                    {room.price_deposit > 0 && (
                        <p className="text-xs text-slate-400">
                            + Deposit {room.formatted_price_deposit} (dikembalikan saat checkout)
                        </p>
                    )}
                </div>

                {onSelectRoom && (
                    <Button
                        variant={isAvailable ? 'primary' : 'outline'}
                        size="sm"
                        disabled={!isAvailable}
                        onClick={() => onSelectRoom(room)}
                    >
                        {isAvailable ? 'Pilih Kamar Ini' : 'Penuh'}
                    </Button>
                )}
            </div>
        </div>
    );
};
