import React from 'react';
import { cn } from '@/lib/cn';

export interface LogoProps {
    variant?: 'horizontal' | 'square';
    className?: string;
    alt?: string;
}

export const Logo: React.FC<LogoProps> = ({
    variant = 'horizontal',
    className = '',
    alt = 'Pusat Kost UNNES',
}) => {
    if (variant === 'square') {
        return (
            <img
                src="/images/logo.jpg"
                alt={alt}
                className={cn('h-12 w-12 rounded-2xl object-contain shadow-xs', className)}
            />
        );
    }

    return (
        <img
            src="/images/logo-horizontal.png"
            alt={alt}
            className={cn('h-9 sm:h-10 md:h-11 w-auto object-contain transition-all', className)}
        />
    );
};
