import React, { InputHTMLAttributes, forwardRef } from 'react';
import { cn } from '@/lib/cn';

export interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
    hint?: string;
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
    ({ className = '', label, error, hint, id, ...props }, ref) => {
        const inputId = id || (label ? label.toLowerCase().replace(/\s+/g, '-') : undefined);

        return (
            <div className="w-full space-y-1.5">
                {label && (
                    <label
                        htmlFor={inputId}
                        className="block text-sm font-medium text-foreground-secondary"
                    >
                        {label}
                    </label>
                )}
                <input
                    id={inputId}
                    ref={ref}
                    className={cn(
                        'w-full px-3.5 py-2 text-sm rounded-lg border bg-surface dark:bg-surface-elevated dark:text-foreground dark:border-border transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1',
                        error
                            ? 'border-red-400 focus:border-red-500 focus:ring-red-200 text-red-900'
                            : 'border-border focus:border-primary focus:ring-primary/20 text-foreground',
                        'disabled:bg-surface-sunken disabled:text-muted-foreground disabled:cursor-not-allowed',
                        className
                    )}
                    {...props}
                />
                {error && <p className="text-xs text-red-600 font-medium">{error}</p>}
                {hint && !error && <p className="text-xs text-muted">{hint}</p>}
            </div>
        );
    }
);

Input.displayName = 'Input';
