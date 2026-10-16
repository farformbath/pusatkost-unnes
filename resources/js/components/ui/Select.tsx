import React, { SelectHTMLAttributes, forwardRef } from 'react';
import { cn } from '@/lib/cn';

export interface SelectOption {
    value: string | number;
    label: string;
}

export interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
    label?: string;
    error?: string;
    options?: SelectOption[];
}

export const Select = forwardRef<HTMLSelectElement, SelectProps>(
    ({ className = '', label, error, options, children, id, ...props }, ref) => {
        const selectId = id || (label ? label.toLowerCase().replace(/\s+/g, '-') : undefined);

        return (
            <div className="w-full space-y-1.5">
                {label && (
                    <label
                        htmlFor={selectId}
                        className="block text-sm font-medium text-foreground-secondary"
                    >
                        {label}
                    </label>
                )}
                <select
                    id={selectId}
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
                >
                    {options
                        ? options.map((opt) => (
                              <option key={opt.value} value={opt.value}>
                                  {opt.label}
                              </option>
                          ))
                        : children}
                </select>
                {error && <p className="text-xs text-red-600 font-medium">{error}</p>}
            </div>
        );
    }
);

Select.displayName = 'Select';
