import React, { HTMLAttributes } from 'react';
import { cn } from '@/lib/cn';

export interface CardProps extends HTMLAttributes<HTMLDivElement> {
    hoverable?: boolean;
}

export const Card: React.FC<CardProps> = ({
    children,
    className = '',
    hoverable = false,
    ...props
}) => {
    return (
        <div
            className={cn(
                'bg-surface rounded-xl border border-border shadow-xs overflow-hidden',
                hoverable && 'transition-all duration-200 hover:shadow-md hover:border-border',
                className
            )}
            {...props}
        >
            {children}
        </div>
    );
};

export const CardHeader: React.FC<HTMLAttributes<HTMLDivElement>> = ({ children, className = '', ...props }) => (
    <div className={cn('p-5 border-b border-border-subtle', className)} {...props}>
        {children}
    </div>
);

export const CardContent: React.FC<HTMLAttributes<HTMLDivElement>> = ({ children, className = '', ...props }) => (
    <div className={cn('p-5', className)} {...props}>
        {children}
    </div>
);

export const CardFooter: React.FC<HTMLAttributes<HTMLDivElement>> = ({ children, className = '', ...props }) => (
    <div className={cn('p-5 bg-surface-sunken/50 dark:bg-surface-elevated border-t border-border-subtle flex items-center justify-between', className)} {...props}>
        {children}
    </div>
);
