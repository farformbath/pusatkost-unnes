import React, { InputHTMLAttributes, forwardRef, useId } from 'react';
import { cn } from '@/lib/cn';

export interface CheckboxProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'type'> {
    label?: string;
    description?: string;
}

export const Checkbox = forwardRef<HTMLInputElement, CheckboxProps>(
    ({ className = '', label, description, id, ...props }, ref) => {
        const fallbackId = useId();
        const checkboxId = id || fallbackId;

        return (
            <div className="relative flex items-start gap-2">
                <div className="flex h-5 items-center">
                    <input
                        type="checkbox"
                        id={checkboxId}
                        ref={ref}
                        className={cn(
                            'h-4 w-4 rounded border-border text-primary focus:ring-primary/20 focus:ring-2 focus:ring-offset-1',
                            className
                        )}
                        {...props}
                    />
                </div>
                {(label || description) && (
                    <div className="text-sm">
                        {label && (
                            <label htmlFor={checkboxId} className="font-medium text-foreground cursor-pointer">
                                {label}
                            </label>
                        )}
                        {description && (
                            <p className="text-xs text-muted">{description}</p>
                        )}
                    </div>
                )}
            </div>
        );
    }
);

Checkbox.displayName = 'Checkbox';
