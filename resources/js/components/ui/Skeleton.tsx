import React, { HTMLAttributes } from 'react';
import { cn } from '@/lib/cn';

export interface SkeletonProps extends HTMLAttributes<HTMLDivElement> {
    variant?: 'text' | 'circular' | 'rectangular';
    width?: string | number;
    height?: string | number;
}

export const Skeleton = ({
    className = '',
    variant = 'text',
    width,
    height,
    ...props
}: SkeletonProps) => {
    return (
        <div
            className={cn(
                'animate-pulse bg-slate-200 dark:bg-slate-700',
                variant === 'text' && 'h-4 w-full rounded-md',
                variant === 'circular' && 'rounded-full',
                variant === 'rectangular' && 'rounded-lg',
                className
            )}
            style={{ width, height, ...props.style }}
            {...props}
        />
    );
};

export const SkeletonCard = ({ className = '' }: { className?: string }) => {
    return (
        <div className={cn("flex flex-col gap-3 p-3 bg-surface rounded-xl border border-border-subtle", className)}>
            <Skeleton variant="rectangular" className="w-full aspect-[4/3] rounded-lg" />
            <div className="space-y-2 mt-2">
                <Skeleton variant="text" className="w-3/4 h-5" />
                <Skeleton variant="text" className="w-1/2 h-4" />
                <Skeleton variant="text" className="w-full h-4" />
            </div>
            <div className="mt-2 pt-2 border-t border-border-subtle flex justify-between items-center">
                <Skeleton variant="text" className="w-1/3 h-5" />
                <Skeleton variant="circular" className="w-8 h-8" />
            </div>
        </div>
    );
};
