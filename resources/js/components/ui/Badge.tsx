import React, { HTMLAttributes } from 'react';
import { cn } from '@/lib/cn';
import { GenderType, ListingStatus, RoomStatus } from '@/types/listing';

export interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
    variant?: 'putra' | 'putri' | 'campur' | 'published' | 'draft' | 'available' | 'occupied' | 'maintenance' | 'neutral';
    size?: 'sm' | 'md';
}

export const Badge: React.FC<BadgeProps> = ({
    children,
    className = '',
    variant = 'neutral',
    size = 'md',
    ...props
}) => {
    const baseStyles = 'inline-flex items-center font-bold rounded-lg tracking-tight';

    const variants = {
        putri: 'bg-rose-50 text-rose-700 border border-rose-200/80',
        putra: 'bg-blue-50 text-blue-700 border border-blue-200/80',
        campur: 'bg-purple-50 text-purple-700 border border-purple-200/80',
        published: 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
        draft: 'bg-amber-50 text-amber-700 border border-amber-200/80',
        available: 'bg-teal-50 text-teal-700 border border-teal-200/80',
        occupied: 'bg-slate-100 text-slate-600 border border-slate-200',
        maintenance: 'bg-orange-50 text-orange-700 border border-orange-200/80',
        neutral: 'bg-slate-100 text-slate-700 border border-slate-200',
    };

    const sizes = {
        sm: 'px-2 py-0.5 text-[10px]',
        md: 'px-2.5 py-1 text-xs',
    };

    return (
        <span className={cn(baseStyles, variants[variant], sizes[size], className)} {...props}>
            {children}
        </span>
    );
};

export const GenderBadge: React.FC<{ gender: GenderType; className?: string }> = ({ gender, className }) => {
    const labels: Record<GenderType, string> = {
        putri: 'Kos Putri',
        putra: 'Kos Putra',
        campur: 'Kos Campur',
    };

    return (
        <Badge variant={gender} className={className}>
            {labels[gender]}
        </Badge>
    );
};

export const StatusBadge: React.FC<{ status: ListingStatus | RoomStatus; className?: string }> = ({ status, className }) => {
    const labels: Record<string, string> = {
        draft: 'Draft',
        published: 'Dipublikasikan',
        archived: 'Diarsipkan',
        available: 'Tersedia',
        occupied: 'Penuh',
        maintenance: 'Perbaikan',
    };

    const variant = (status === 'archived' ? 'neutral' : status) as BadgeProps['variant'];

    return (
        <Badge variant={variant} className={className}>
            {labels[status] || status}
        </Badge>
    );
};
