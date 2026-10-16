import React, { useState, useEffect, useRef } from 'react';
import { Link } from '@inertiajs/react';
import {
    ChatCircleDots,
    PaperPlaneRight,
    X,
    ArrowClockwise,
    Sparkle,
    MapPin,
    Tag,
} from '@phosphor-icons/react';
import { formatRupiah } from '@/lib/formatRupiah';
import { GenderBadge } from '@/components/ui/Badge';
import { GenderType } from '@/types/listing';
import { ListingSummary, NesaChatResponse, NesaHistoryResponse, NesaMessage } from '@/types/nesa';

const STORAGE_KEY = 'unneskost_nesa_session_token';

const QUICK_CHIPS = [
    'Kos putri dekat FBS < 1 jt',
    'Kos putra dekat FT free listrik',
    'Kos campur ada parkir mobil',
];

const getCsrfToken = (): string => {
    if (typeof document === 'undefined') return '';
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        const content = meta.getAttribute('content');
        if (content) return content;
    }
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
};

const normalizeGender = (gender: string): GenderType => {
    const lower = gender.toLowerCase();
    if (lower === 'putri' || lower === 'putra' || lower === 'campur') {
        return lower;
    }
    return 'campur';
};

interface NesaListingCardProps {
    listing: ListingSummary;
}

const NesaListingCard: React.FC<NesaListingCardProps> = ({ listing }) => {
    const gender = normalizeGender(listing.gender);

    return (
        <Link
            href={`/kos/${listing.slug}`}
            className="group flex items-center gap-3 p-2 bg-slate-50 hover:bg-rose-50/40 border border-slate-200/90 hover:border-primary/40 rounded-xl transition-all duration-150 text-left"
        >
            <div className="relative w-16 h-16 sm:w-18 sm:h-18 shrink-0 rounded-lg overflow-hidden bg-slate-200">
                <img
                    src={listing.primary_image_url || '/images/logo.jpg'}
                    alt={listing.name}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    loading="lazy"
                    onError={(e) => {
                        (e.target as HTMLImageElement).src = '/images/logo.jpg';
                    }}
                />
            </div>
            <div className="flex-1 min-w-0 pr-1">
                <div className="flex items-center gap-1.5 mb-0.5">
                    <GenderBadge gender={gender} className="text-[9px] px-1.5 py-0" />
                    <span className="flex items-center gap-0.5 text-[10px] text-slate-500 truncate">
                        <MapPin weight="fill" className="w-2.5 h-2.5 text-primary shrink-0" />
                        {listing.area}
                    </span>
                </div>
                <h4 className="font-bold text-xs text-slate-800 group-hover:text-primary transition-colors truncate">
                    {listing.name}
                </h4>
                <p className="text-[11px] font-bold text-primary mt-0.5">
                    {formatRupiah(listing.starting_price)}
                    <span className="text-[9px] font-normal text-slate-500"> / bln</span>
                </p>
            </div>
        </Link>
    );
};

export const NesaWidget: React.FC = () => {
    const [isOpen, setIsOpen] = useState<boolean>(false);
    const [sessionToken, setSessionToken] = useState<string>(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem(STORAGE_KEY) || '';
        }
        return '';
    });
    const [messages, setMessages] = useState<NesaMessage[]>([]);
    const [recommendedListingsMap, setRecommendedListingsMap] = useState<Record<string, ListingSummary>>({});
    const [input, setInput] = useState<string>('');
    const [isLoading, setIsLoading] = useState<boolean>(false);

    const messagesEndRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);

    // Auto-scroll to bottom whenever messages or loading state changes
    useEffect(() => {
        if (isOpen) {
            messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
        }
    }, [messages, isLoading, isOpen]);

    // Focus input on drawer open
    useEffect(() => {
        if (isOpen) {
            setTimeout(() => {
                inputRef.current?.focus();
            }, 100);
        }
    }, [isOpen]);

    // Restore chat history from server if sessionToken exists
    useEffect(() => {
        if (!sessionToken) return;

        let isMounted = true;
        const fetchHistory = async () => {
            try {
                const res = await window.fetch(`/api/nesa/history?session_token=${encodeURIComponent(sessionToken)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!res.ok) return;

                const data: NesaHistoryResponse = await res.json();
                if (!isMounted) return;

                if (data.messages && data.messages.length > 0) {
                    const listingMap: Record<string, ListingSummary> = {};
                    if (data.recommended_listings) {
                        data.recommended_listings.forEach((item) => {
                            listingMap[item.id] = item;
                        });
                    }
                    setRecommendedListingsMap(listingMap);

                    // Attach recommended listings to the last assistant message
                    const restoredMessages: NesaMessage[] = data.messages.map((m, index) => {
                        const isLastAssistant =
                            m.role === 'assistant' &&
                            !data.messages.slice(index + 1).some((after) => after.role === 'assistant');

                        return {
                            id: m.id,
                            role: m.role,
                            content: m.content,
                            created_at: m.created_at,
                            recommended_listings: isLastAssistant ? data.recommended_listings : undefined,
                        };
                    });

                    setMessages(restoredMessages);
                }
            } catch (err) {
                console.error('Gagal memuat riwayat chat NESA:', err);
            }
        };

        void fetchHistory();

        return () => {
            isMounted = false;
        };
    }, [sessionToken]);

    const handleResetSession = () => {
        if (typeof window !== 'undefined') {
            localStorage.removeItem(STORAGE_KEY);
        }
        setSessionToken('');
        setMessages([]);
        setRecommendedListingsMap({});
        setInput('');
    };

    const handleSendMessage = async (customMessage?: string) => {
        const text = (customMessage ?? input).trim();
        if (!text || isLoading) return;

        const userMsgId = `user_${Date.now()}`;
        const userMsg: NesaMessage = {
            id: userMsgId,
            role: 'user',
            content: text,
            created_at: new Date().toISOString(),
        };

        setMessages((prev) => [...prev, userMsg]);
        if (!customMessage) {
            setInput('');
        }
        setIsLoading(true);

        try {
            const csrfToken = getCsrfToken();
            const res = await window.fetch('/api/nesa/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message: text,
                    session_token: sessionToken || undefined,
                }),
            });

            if (!res.ok) {
                throw new Error(`HTTP ${res.status}`);
            }

            const data: NesaChatResponse = await res.json();

            if (data.session_token && data.session_token !== sessionToken) {
                setSessionToken(data.session_token);
                if (typeof window !== 'undefined') {
                    localStorage.setItem(STORAGE_KEY, data.session_token);
                }
            }

            if (data.recommended_listings && data.recommended_listings.length > 0) {
                setRecommendedListingsMap((prev) => {
                    const next = { ...prev };
                    data.recommended_listings.forEach((item) => {
                        next[item.id] = item;
                    });
                    return next;
                });
            }

            const assistantMsg: NesaMessage = {
                id: `nesa_${Date.now()}`,
                role: 'assistant',
                content: data.reply,
                created_at: new Date().toISOString(),
                recommended_listings: data.recommended_listings,
            };

            setMessages((prev) => [...prev, assistantMsg]);
        } catch (err) {
            console.error('NESA chat request error:', err);
            setMessages((prev) => [
                ...prev,
                {
                    id: `err_${Date.now()}`,
                    role: 'assistant',
                    content: 'Maaf kak, saat ini terjadi gangguan koneksi ke server NESA. Silakan coba kirim ulang pertanyaan kakak ya.',
                    created_at: new Date().toISOString(),
                },
            ]);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <>
            {/* Floating Action Button (FAB) */}
            {!isOpen && (
                <button
                    type="button"
                    onClick={() => setIsOpen(true)}
                    className="fixed bottom-6 right-6 z-40 flex items-center gap-2.5 px-4 py-3 bg-primary hover:bg-primary-600 text-white rounded-full shadow-lg shadow-primary/30 hover:shadow-xl hover:shadow-primary/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 cursor-pointer"
                    aria-label="Buka Chat NESA"
                >
                    <div className="relative flex items-center justify-center">
                        <ChatCircleDots weight="fill" className="w-6 h-6" />
                        <span className="absolute -top-1 -right-1 flex h-3 w-3">
                            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" />
                            <span className="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 border-2 border-white" />
                        </span>
                    </div>
                    <div className="text-left hidden sm:block">
                        <span className="block text-xs font-bold leading-tight">Tanya NESA</span>
                        <span className="block text-[10px] text-white/90 font-medium leading-none">Asisten Kos UNNES</span>
                    </div>
                </button>
            )}

            {/* Expandable Chat Drawer */}
            {isOpen && (
                <div
                    className="fixed inset-0 sm:inset-auto sm:bottom-6 sm:right-6 z-50 flex flex-col w-full sm:w-[400px] h-[100dvh] sm:h-[620px] sm:max-h-[85vh] bg-surface sm:rounded-3xl shadow-2xl border border-border overflow-hidden"
                    role="dialog"
                    aria-label="NESA Chat Drawer"
                >
                    {/* Header */}
                    <div className="bg-surface border-b border-border px-4 py-3 flex items-center justify-between shadow-2xs">
                        <div className="flex items-center gap-2.5">
                            <div className="relative w-9 h-9 rounded-2xl bg-primary-50 border border-primary-200 flex items-center justify-center text-primary">
                                <Sparkle weight="fill" className="w-5 h-5 text-primary" />
                                <span className="absolute -top-0.5 -right-0.5 flex h-2.5 w-2.5">
                                    <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75" />
                                    <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500" />
                                </span>
                            </div>
                            <div>
                                <div className="flex items-center gap-1.5">
                                    <h3 className="font-bold text-sm text-foreground leading-tight">NESA</h3>
                                    <span className="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.2 rounded-full border border-emerald-200/60">
                                        Online
                                    </span>
                                </div>
                                <p className="text-[11px] text-muted leading-tight">Asisten Pintar Kos UNNES</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-1 text-muted">
                            <button
                                type="button"
                                onClick={handleResetSession}
                                className="p-1.5 hover:bg-slate-100 hover:text-foreground rounded-lg transition-colors cursor-pointer"
                                title="Reset Percakapan"
                                aria-label="Reset Percakapan"
                            >
                                <ArrowClockwise weight="bold" className="w-4 h-4" />
                            </button>
                            <button
                                type="button"
                                onClick={() => setIsOpen(false)}
                                className="p-1.5 hover:bg-slate-100 hover:text-foreground rounded-lg transition-colors cursor-pointer"
                                title="Tutup Chat"
                                aria-label="Tutup Chat"
                            >
                                <X weight="bold" className="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {/* Chat Messages Body */}
                    <div className="flex-1 overflow-y-auto p-4 space-y-3.5 bg-surface-sunken">
                        {/* Welcome message if no conversation yet */}
                        {messages.length === 0 && (
                            <div className="bg-surface rounded-2xl p-4 border border-border shadow-xs text-xs space-y-3">
                                <div className="flex items-start gap-2.5">
                                    <div className="w-8 h-8 rounded-xl bg-primary-50 text-primary flex items-center justify-center shrink-0 mt-0.5">
                                        <Sparkle weight="fill" className="w-4 h-4" />
                                    </div>
                                    <div className="space-y-1">
                                        <p className="font-bold text-foreground">Halo kak! Ada yang bisa NESA bantu?</p>
                                        <p className="text-muted leading-relaxed">
                                            NESA bisa bantu carikan info kos di sekitar Sekaran, Banaran, Patemon, dekat fakultasmu, atau fasilitas yang kamu butuhkan.
                                        </p>
                                    </div>
                                </div>

                                <div className="pt-2 border-t border-border">
                                    <p className="text-[11px] font-bold text-foreground-secondary mb-2 flex items-center gap-1">
                                        <Tag weight="bold" className="w-3 h-3 text-primary" />
                                        Contoh pertanyaan cepat:
                                    </p>
                                    <div className="flex flex-col gap-1.5">
                                        {QUICK_CHIPS.map((chip) => (
                                            <button
                                                key={chip}
                                                type="button"
                                                onClick={() => handleSendMessage(chip)}
                                                className="text-left text-xs bg-surface-sunken hover:bg-primary-50 hover:text-primary hover:border-primary-200 border border-border px-3 py-2 rounded-xl transition-all cursor-pointer font-medium text-foreground-secondary"
                                            >
                                                &ldquo;{chip}&rdquo;
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Message History */}
                        {messages.map((msg) => (
                            <div
                                key={msg.id}
                                className={`flex flex-col ${msg.role === 'user' ? 'items-end' : 'items-start'}`}
                            >
                                <div
                                    className={`max-w-[85%] rounded-2xl px-3.5 py-2.5 text-xs sm:text-sm leading-relaxed shadow-xs ${
                                        msg.role === 'user'
                                            ? 'bg-primary text-white rounded-br-xs font-medium'
                                            : 'bg-surface text-foreground border border-border rounded-bl-xs'
                                    }`}
                                >
                                    <p className="whitespace-pre-wrap">{msg.content}</p>
                                </div>

                                {/* Recommended Listings Cards */}
                                {msg.role === 'assistant' && msg.recommended_listings && msg.recommended_listings.length > 0 && (
                                    <div className="mt-2.5 w-full space-y-2">
                                        <p className="text-[11px] font-bold text-foreground-secondary flex items-center gap-1 px-1">
                                            <Sparkle weight="fill" className="w-3 h-3 text-primary" />
                                            Rekomendasi Kos Terkait:
                                        </p>
                                        <div className="space-y-1.5">
                                            {msg.recommended_listings.map((item) => {
                                                const listing = recommendedListingsMap[item.id] ?? item;
                                                return <NesaListingCard key={listing.id} listing={listing} />;
                                            })}
                                        </div>
                                    </div>
                                )}
                            </div>
                        ))}

                        {/* Loading Typing Indicator */}
                        {isLoading && (
                            <div className="flex items-start gap-2 max-w-[85%]">
                                <div className="bg-surface text-foreground border border-border rounded-2xl rounded-bl-xs px-3.5 py-2.5 text-xs flex items-center gap-1.5 shadow-xs">
                                    <span className="w-1.5 h-1.5 rounded-full bg-primary animate-bounce [animation-delay:-0.3s]" />
                                    <span className="w-1.5 h-1.5 rounded-full bg-primary animate-bounce [animation-delay:-0.15s]" />
                                    <span className="w-1.5 h-1.5 rounded-full bg-primary animate-bounce" />
                                    <span className="text-[11px] text-muted ml-1">NESA sedang mencari...</span>
                                </div>
                            </div>
                        )}

                        <div ref={messagesEndRef} />
                    </div>

                    {/* Quick Chips row if messages exist */}
                    {messages.length > 0 && (
                        <div className="bg-surface px-3 pt-2 pb-1 border-t border-border flex items-center gap-1.5 overflow-x-auto no-scrollbar text-[11px]">
                            {QUICK_CHIPS.map((chip) => (
                                <button
                                    key={chip}
                                    type="button"
                                    onClick={() => handleSendMessage(chip)}
                                    disabled={isLoading}
                                    className="shrink-0 bg-surface-sunken hover:bg-primary-50 hover:text-primary hover:border-primary-200 border border-border px-2.5 py-1 rounded-full text-muted transition-colors cursor-pointer disabled:opacity-50"
                                >
                                    {chip}
                                </button>
                            ))}
                        </div>
                    )}

                    {/* Input Footer */}
                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            void handleSendMessage();
                        }}
                        className="bg-surface p-3 border-t border-border flex items-center gap-2"
                    >
                        <input
                            ref={inputRef}
                            type="text"
                            value={input}
                            onChange={(e) => setInput(e.target.value)}
                            placeholder="Tanya info kos di sekitar UNNES..."
                            disabled={isLoading}
                            className="flex-1 bg-surface-sunken border border-border focus:bg-surface focus:border-primary focus:ring-2 focus:ring-primary/20 rounded-xl px-3.5 py-2 text-xs sm:text-sm text-foreground outline-none transition-all placeholder:text-muted disabled:opacity-50"
                        />
                        <button
                            type="submit"
                            disabled={isLoading || input.trim() === ''}
                            className="p-2.5 rounded-xl bg-primary hover:bg-primary-600 disabled:opacity-40 disabled:cursor-not-allowed text-white shadow-xs transition-all active:scale-95 cursor-pointer shrink-0"
                            aria-label="Kirim Pesan"
                        >
                            <PaperPlaneRight weight="fill" className="w-4 h-4" />
                        </button>
                    </form>
                </div>
            )}
        </>
    );
};
