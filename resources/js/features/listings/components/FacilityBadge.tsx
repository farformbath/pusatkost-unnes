import React from 'react';
import { Facility } from '@/types/listing';
import { cn } from '@/lib/cn';

interface FacilityIconProps {
    name?: string | null;
    className?: string;
}

export const FacilityIcon: React.FC<FacilityIconProps> = ({ name, className = 'w-3.5 h-3.5' }) => {
    const iconKey = (name || '').toLowerCase();

    // WiFi
    if (iconKey.includes('wifi')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
            </svg>
        );
    }

    // AC / Air Conditioner
    if (iconKey.includes('ac') || iconKey.includes('air-vent') || iconKey.includes('air conditioner') || iconKey.includes('wind')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        );
    }

    // Kamar Mandi Dalam / Bath
    if (iconKey.includes('bath') || iconKey.includes('kamar mandi') || iconKey.includes('km') || iconKey.includes('toilet')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v16h16V4H4zm4 4h8v8H8V8z" />
            </svg>
        );
    }

    // Parkir Mobil / Car
    if (iconKey.includes('car') || iconKey.includes('mobil')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM5 11l1.5-4.5A2 2 0 018.4 5h7.2a2 2 0 011.9 1.5L19 11M3 14h18v3H3v-3z" />
            </svg>
        );
    }

    // Parkir Motor / Bike
    if (iconKey.includes('bike') || iconKey.includes('motor')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
            </svg>
        );
    }

    // Kasur & Bantal / Bed
    if (iconKey.includes('bed') || iconKey.includes('kasur')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 7v11m0-4h18m0-7v11M3 10h18M7 7h.01M17 7h.01" />
            </svg>
        );
    }

    // Lemari Pakaian / Wardrobe
    if (iconKey.includes('wardrobe') || iconKey.includes('lemari')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 4h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 011-1zm7 0v16M9 10v2m6-2v2" />
            </svg>
        );
    }

    // Meja & Kursi Belajar / Armchair / Desk
    if (iconKey.includes('armchair') || iconKey.includes('meja') || iconKey.includes('kursi')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16v6H4V6zm2 6v8m12-8v8M9 12v4m6-4v4" />
            </svg>
        );
    }

    // Dapur Bersama / Utensils
    if (iconKey.includes('utensils') || iconKey.includes('dapur')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
        );
    }

    // Mesin Cuci / Washing Machine
    if (iconKey.includes('washing') || iconKey.includes('cuci')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm7 5a4 4 0 100 8 4 4 0 000-8zm-4-2h.01M16 6h.01" />
            </svg>
        );
    }

    // Akses 24 Jam / Clock
    if (iconKey.includes('clock') || iconKey.includes('24 jam') || iconKey.includes('akses')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        );
    }

    // CCTV Keamanan / Shield Check
    if (iconKey.includes('shield') || iconKey.includes('cctv') || iconKey.includes('keamanan')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        );
    }

    // Listrik / Zap
    if (iconKey.includes('zap') || iconKey.includes('listrik')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        );
    }

    // Air / Droplets
    if (iconKey.includes('droplet') || iconKey.includes('air')) {
        return (
            <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        );
    }

    // Default Checkmark / Tag
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
        </svg>
    );
};

interface FacilityBadgeProps {
    facility: Facility;
    className?: string;
    showIcon?: boolean;
}

export const FacilityBadge: React.FC<FacilityBadgeProps> = ({
    facility,
    className = '',
    showIcon = true,
}) => {
    return (
        <span
            className={cn(
                'inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100/90 text-slate-700 border border-slate-200/60',
                className
            )}
        >
            {showIcon && <span className="text-slate-500"><FacilityIcon name={facility.icon || facility.name} /></span>}
            <span>{facility.name}</span>
        </span>
    );
};

