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
                className={cn('h-10 w-10 rounded-xl object-contain shadow-xs', className)}
            />
        );
    }

    return (
        <img
            src="/images/logo-horizontal.png"
            alt={alt}
            className={cn('h-10 w-auto object-contain', className)}
        />
    );
};

export default Logo;
