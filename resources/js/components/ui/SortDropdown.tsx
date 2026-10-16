import React, { useState, useRef, useEffect } from 'react';

export interface SortOption {
    value: string;
    label: string;
    sublabel?: string;
    icon: string;
}

export const DEFAULT_SORT_OPTIONS: SortOption[] = [
    {
        value: 'recommended',
        label: 'Rekomendasi Terbaik',
        sublabel: 'Skor kecocokan & popularitas mahasiswa',
        icon: '⭐',
    },
    {
        value: 'price_asc',
        label: 'Harga Termurah',
        sublabel: 'Urutan dari tarif sewa paling hemat',
        icon: '💵',
    },
    {
        value: 'price_desc',
        label: 'Fasilitas & Harga Premium',
        sublabel: 'Kamar eksklusif & fasilitas lengkap',
        icon: '💎',
    },
    {
        value: 'rating_desc',
        label: 'Rating Tertinggi',
        sublabel: 'Ulasan kepuasan penyewa bintang 5',
        icon: '★',
    },
    {
        value: 'newest',
        label: 'Kos Baru Ditambahkan',
        sublabel: 'Listing yang baru saja diinput pemilik',
        icon: '🆕',
    },
];

interface SortDropdownProps {
    value: string;
    onChange: (value: string) => void;
    options?: SortOption[];
    className?: string;
}

export const SortDropdown: React.FC<SortDropdownProps> = ({
    value,
    onChange,
    options = DEFAULT_SORT_OPTIONS,
    className = '',
}) => {
    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    const currentOption = options.find((opt) => opt.value === value) || options[0];

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        const handleEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setIsOpen(false);
            }
        };

        if (isOpen) {
            document.addEventListener('mousedown', handleClickOutside);
            document.addEventListener('keydown', handleEscape);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('keydown', handleEscape);
        };
    }, [isOpen]);

    const handleSelect = (val: string) => {
        onChange(val);
        setIsOpen(false);
    };

    return (
        <div ref={dropdownRef} className={`relative inline-block text-left ${className}`}>
            {/* Custom Popover Trigger */}
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                className={`inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all cursor-pointer select-none active:scale-[0.98] ${
                    isOpen || (value && value !== 'recommended')
                        ? 'border-[#FF6565] bg-rose-50/50 text-slate-900 shadow-2xs'
                        : 'border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 shadow-2xs'
                }`}
                aria-haspopup="listbox"
                aria-expanded={isOpen}
            >
                <span className="text-sm">{currentOption?.icon}</span>
                <span className="truncate max-w-[130px] sm:max-w-none">{currentOption?.label}</span>
                <svg
                    className={`w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0 ${
                        isOpen ? 'rotate-180 text-[#FF6565]' : ''
                    }`}
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {/* Floating Popover Menu */}
            {isOpen && (
                <div
                    className="absolute right-0 top-full mt-2 w-72 sm:w-80 bg-white rounded-2xl shadow-xl shadow-slate-900/10 border border-slate-200/90 p-1.5 z-50 animate-in fade-in zoom-in-95 duration-150 origin-top-right"
                    role="listbox"
                >
                    <div className="px-3 py-2 border-b border-slate-100 flex items-center justify-between mb-1">
                        <span className="text-[10px] font-black uppercase tracking-wider text-slate-400">
                            Urutan Tampilan Kos
                        </span>
                        <span className="text-[10px] font-semibold text-slate-400">
                            5 Pilihan
                        </span>
                    </div>

                    <div className="space-y-0.5">
                        {options.map((opt) => {
                            const isSelected = opt.value === value || (!value && opt.value === 'recommended');

                            return (
                                <button
                                    key={opt.value}
                                    type="button"
                                    onClick={() => handleSelect(opt.value)}
                                    className={`w-full text-left p-2.5 rounded-xl transition-all flex items-start gap-3 cursor-pointer group ${
                                        isSelected
                                            ? 'bg-rose-50/80 border border-rose-200 text-slate-950 font-bold shadow-2xs'
                                            : 'hover:bg-slate-50 border border-transparent text-slate-700'
                                    }`}
                                    role="option"
                                    aria-selected={isSelected}
                                >
                                    <span className="text-base shrink-0 mt-0.5">{opt.icon}</span>

                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-1.5">
                                            <span
                                                className={`text-xs ${
                                                    isSelected ? 'font-black text-[#FF6565]' : 'font-bold text-slate-800 group-hover:text-slate-950'
                                                }`}
                                            >
                                                {opt.label}
                                            </span>
                                        </div>
                                        {opt.sublabel && (
                                            <p className="text-[11px] text-slate-500 font-normal leading-tight mt-0.5">
                                                {opt.sublabel}
                                            </p>
                                        )}
                                    </div>

                                    {isSelected && (
                                        <span className="w-4 h-4 rounded-full bg-[#FF6565] text-white flex items-center justify-center text-[10px] font-black shrink-0 shadow-2xs mt-1">
                                            ✓
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                </div>
            )}
        </div>
    );
};
