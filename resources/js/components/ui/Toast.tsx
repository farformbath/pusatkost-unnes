import React, { createContext, useContext, useState, useCallback, ReactNode } from 'react';

export type ToastType = 'success' | 'error' | 'info' | 'warning';

export interface ToastItem {
    id: string;
    type: ToastType;
    message: string;
    title?: string;
}

interface ToastContextType {
    showToast: (message: string, type?: ToastType, title?: string) => void;
    success: (message: string, title?: string) => void;
    error: (message: string, title?: string) => void;
    info: (message: string, title?: string) => void;
    warning: (message: string, title?: string) => void;
}

const ToastContext = createContext<ToastContextType | undefined>(undefined);

export const ToastProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [toasts, setToasts] = useState<ToastItem[]>([]);

    const removeToast = useCallback((id: string) => {
        setToasts((prev) => prev.filter((t) => t.id !== id));
    }, []);

    const showToast = useCallback((message: string, type: ToastType = 'info', title?: string) => {
        const id = Math.random().toString(36).substring(2, 9);
        const newToast: ToastItem = { id, type, message, title };

        setToasts((prev) => [...prev, newToast]);

        setTimeout(() => {
            removeToast(id);
        }, 4000);
    }, [removeToast]);

    const success = useCallback((message: string, title?: string) => showToast(message, 'success', title), [showToast]);
    const error = useCallback((message: string, title?: string) => showToast(message, 'error', title), [showToast]);
    const info = useCallback((message: string, title?: string) => showToast(message, 'info', title), [showToast]);
    const warning = useCallback((message: string, title?: string) => showToast(message, 'warning', title), [showToast]);

    const getIcon = (type: ToastType) => {
        switch (type) {
            case 'success':
                return '✓';
            case 'error':
                return '✕';
            case 'warning':
                return '⚠️';
            case 'info':
            default:
                return 'ℹ️';
        }
    };

    const getTypeStyles = (type: ToastType) => {
        switch (type) {
            case 'success':
                return 'bg-emerald-50 border-emerald-200 text-emerald-900 icon-bg-emerald';
            case 'error':
                return 'bg-rose-50 border-rose-200 text-rose-900 icon-bg-rose';
            case 'warning':
                return 'bg-amber-50 border-amber-200 text-amber-900 icon-bg-amber';
            case 'info':
            default:
                return 'bg-slate-900 border-slate-800 text-white icon-bg-dark';
        }
    };

    return (
        <ToastContext.Provider value={{ showToast, success, error, info, warning }}>
            {children}

            {/* Floating Toast Container */}
            <div className="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full px-4 sm:px-0 pointer-events-none">
                {toasts.map((toast) => (
                    <div
                        key={toast.id}
                        className={`pointer-events-auto flex items-start gap-3 p-4 rounded-2xl border shadow-xl transition-all duration-300 animate-in slide-in-from-bottom-5 ${getTypeStyles(toast.type)}`}
                        role="alert"
                    >
                        <div className="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black shrink-0 bg-white/40 shadow-2xs">
                            {getIcon(toast.type)}
                        </div>

                        <div className="flex-1 text-xs">
                            {toast.title && <p className="font-bold mb-0.5">{toast.title}</p>}
                            <p className="leading-relaxed font-medium">{toast.message}</p>
                        </div>

                        <button
                            type="button"
                            onClick={() => removeToast(toast.id)}
                            className="text-current opacity-60 hover:opacity-100 transition-opacity p-0.5 cursor-pointer text-xs"
                            aria-label="Tutup"
                        >
                            ✕
                        </button>
                    </div>
                ))}
            </div>
        </ToastContext.Provider>
    );
};

export const useToast = (): ToastContextType => {
    const context = useContext(ToastContext);
    if (!context) {
        throw new Error('useToast must be used within a ToastProvider');
    }
    return context;
};
