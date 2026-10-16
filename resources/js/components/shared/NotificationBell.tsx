import React, { useEffect, useRef, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import { SharedProps } from '@/types/auth';
import { NotificationItem } from '@/types/notification';

const getCsrfToken = (): string => {
    if (typeof document === 'undefined') return '';
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        return meta.getAttribute('content') || '';
    }
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

export const NotificationBell: React.FC = () => {
    const { notifications } = usePage<SharedProps>().props;
    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    // Optimistic local state to prevent any page reloads or UI lag
    const [items, setItems] = useState<NotificationItem[]>(notifications?.items || []);
    const [unreadCount, setUnreadCount] = useState<number>(notifications?.unread_count || 0);

    // Sync with Inertia props when page updates
    useEffect(() => {
        if (notifications) {
            setItems(notifications.items || []);
            setUnreadCount(notifications.unread_count || 0);
        }
    }, [notifications]);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent | TouchEvent) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        if (isOpen) {
            document.addEventListener('mousedown', handleClickOutside);
            document.addEventListener('touchstart', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('touchstart', handleClickOutside);
        };
    }, [isOpen]);

    const handleMarkAllAsRead = async (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();

        if (unreadCount === 0) return;

        // 1. Optimistic instant local update
        const previousItems = [...items];
        const previousCount = unreadCount;

        setUnreadCount(0);
        setItems((prev) =>
            prev.map((item) => ({
                ...item,
                read_at: item.read_at || new Date().toISOString(),
            }))
        );

        // 2. Silent background request (zero page reload)
        try {
            const response = await fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });

            if (!response.ok) {
                // Revert on error
                setItems(previousItems);
                setUnreadCount(previousCount);
            }
        } catch {
            setItems(previousItems);
            setUnreadCount(previousCount);
        }
    };

    const handleMarkSingleAsRead = async (id: string, e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();

        // 1. Optimistic update
        setItems((prev) =>
            prev.map((item) => (item.id === id ? { ...item, read_at: new Date().toISOString() } : item))
        );
        setUnreadCount((prev) => Math.max(0, prev - 1));

        // 2. Silent background request
        try {
            await fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            });
        } catch {
            // Ignore background error
        }
    };

    const handleItemClick = (id: string, isRead: boolean, url?: string) => {
        // Mark as read in background if unread
        if (!isRead) {
            setItems((prev) =>
                prev.map((item) => (item.id === id ? { ...item, read_at: new Date().toISOString() } : item))
            );
            setUnreadCount((prev) => Math.max(0, prev - 1));

            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
            }).catch(() => {});
        }

        setIsOpen(false);

        // Seamless navigation without double reload
        if (url) {
            router.visit(url);
        }
    };

    return (
        <div ref={dropdownRef} className="relative">
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                className="relative p-2 text-slate-600 hover:text-slate-900 rounded-full hover:bg-slate-100 transition-colors cursor-pointer focus:outline-none"
                aria-label="Notifikasi"
            >
                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>

                {unreadCount > 0 && (
                    <span className="absolute top-1 right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#FF6565] px-1 text-[10px] font-bold text-white shadow-xs">
                        {unreadCount > 9 ? '9+' : unreadCount}
                    </span>
                )}
            </button>

            {/* Notification Popover Drawer */}
            {isOpen && (
                <div className="absolute right-0 mt-3 w-80 sm:w-96 rounded-2xl bg-white shadow-2xl border border-slate-200/90 py-3 z-50 animate-in fade-in">
                    <div className="flex items-center justify-between px-4 pb-2.5 border-b border-slate-100">
                        <div className="flex items-center gap-2">
                            <span className="font-bold text-xs text-slate-900">Notifikasi</span>
                            {unreadCount > 0 && (
                                <span className="bg-rose-50 text-[#FF6565] text-[10px] font-bold px-2 py-0.5 rounded-full border border-rose-100">
                                    {unreadCount} baru
                                </span>
                            )}
                        </div>

                        {unreadCount > 0 && (
                            <button
                                type="button"
                                onClick={handleMarkAllAsRead}
                                className="text-[11px] font-semibold text-[#FF6565] hover:underline cursor-pointer"
                            >
                                Tandai semua dibaca
                            </button>
                        )}
                    </div>

                    <div className="max-h-80 overflow-y-auto divide-y divide-slate-50">
                        {items && items.length > 0 ? (
                            items.map((notif) => {
                                const isRead = !!notif.read_at;
                                return (
                                    <div
                                        key={notif.id}
                                        onClick={() => handleItemClick(notif.id, isRead, notif.data?.action_url)}
                                        className={`p-3.5 hover:bg-slate-50/80 transition-colors cursor-pointer text-left relative group ${
                                            !isRead ? 'bg-rose-50/30' : ''
                                        }`}
                                    >
                                        <div className="flex items-start justify-between gap-2 mb-1">
                                            <h4 className={`text-xs leading-snug ${!isRead ? 'font-bold text-slate-950' : 'font-medium text-slate-700'}`}>
                                                {notif.data?.title || 'Notifikasi Baru'}
                                            </h4>
                                            <div className="flex items-center gap-1.5 shrink-0">
                                                <span className="text-[10px] text-slate-400">
                                                    {new Date(notif.created_at).toLocaleDateString('id-ID', {
                                                        month: 'short',
                                                        day: 'numeric',
                                                    })}
                                                </span>
                                                {!isRead && (
                                                    <button
                                                        type="button"
                                                        onClick={(e) => handleMarkSingleAsRead(notif.id, e)}
                                                        title="Tandai dibaca"
                                                        className="opacity-0 group-hover:opacity-100 p-0.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded transition-all cursor-pointer"
                                                    >
                                                        <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                )}
                                            </div>
                                        </div>
                                        <p className="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                            {notif.data?.message}
                                        </p>
                                    </div>
                                );
                            })
                        ) : (
                            <div className="py-8 text-center px-4 space-y-1">
                                <p className="text-xs font-semibold text-slate-700">Belum ada notifikasi</p>
                                <p className="text-[11px] text-slate-400">Pembaruan status booking dan sewa Anda akan muncul di sini.</p>
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
};

