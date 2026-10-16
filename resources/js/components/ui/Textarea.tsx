import React, { TextareaHTMLAttributes, forwardRef } from 'react';
import { cn } from '@/lib/cn';

export interface TextareaProps extends TextareaHTMLAttributes<HTMLTextAreaElement> {
    label?: string;
    error?: string;
    hint?: string;
}

export const Textarea = forwardRef<HTMLTextAreaElement, TextareaProps>(
    ({ className = '', label, error, hint, id, rows = 4, ...props }, ref) => {
        const textareaId = id || (label ? label.toLowerCase().replace(/\s+/g, '-') : undefined);

        return (
            <div className="w-full space-y-1.5">
                {label && (
                    <label
                        htmlFor={textareaId}
                        className="block text-sm font-medium text-foreground"
                    >
                        {label}
                    </label>
                )}
                <textarea
                    id={textareaId}
                    ref={ref}
                    rows={rows}
                    className={cn(
                        'w-full px-3.5 py-2 text-sm rounded-lg border bg-surface transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1',
                        error
                            ? 'border-red-400 focus:border-red-500 focus:ring-red-200 text-red-900'
                            : 'border-border focus:border-primary focus:ring-primary/20 text-foreground',
                        'disabled:bg-surface-sunken disabled:text-muted disabled:cursor-not-allowed',
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

Textarea.displayName = 'Textarea';
