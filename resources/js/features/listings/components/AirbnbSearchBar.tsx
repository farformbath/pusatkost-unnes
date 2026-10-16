import React, { useEffect, useRef, useState } from 'react';
import { router } from '@inertiajs/react';

interface AirbnbSearchBarProps {
    initialValues?: {
        keyword?: string;
        area?: string;
        areas?: string[];
        gender?: string;
        min_price?: number | null;
        max_price?: number | null;
        facility_ids?: string[];
        sort?: string;
    };
}

interface AreaOption {
    value: string;
    label: string;
    description: string;
    badge?: string;
}

const UNNES_AREAS: AreaOption[] = [
    {
        value: '',
        label: 'Semua Wilayah',
        description: 'Seluruh kos di kawasan kampus UNNES',
    },
    {
        value: 'Sekaran',
        label: 'Sekaran',
        description: 'Dekat Rektorat, FBS, FEB, FH & FISIP',
        badge: 'Pusat Kampus',
    },
    {
        value: 'Banaran',
        label: 'Banaran',
        description: 'Dekat Fak. Teknik (FT) & Fak. MIPA',
        badge: 'Jalan Kaki',
    },
    {
        value: 'Taman Siswa',
        label: 'Taman Siswa',
        description: 'Pusat Kuliner, Cafe, Bank & Pertokoan',
        badge: 'Strategis',
    },
    {
        value: 'Gunungpati',
        label: 'Gunungpati',
        description: 'Dekat FIP, FIK & Kantor Kecamatan',
    },
    {
        value: 'Patemon',
        label: 'Patemon',
        description: 'Kawasan Sejuk Dekat Gerbang Belakang',
        badge: 'Asri & Tenang',
    },
    {
        value: 'Menoreh',
        label: 'Menoreh / Sampangan',
        description: 'Akses Cepat Menuju Kota Semarang',
    },
    {
        value: 'Kalisegoro',
        label: 'Kalisegoro',
        description: 'Lingkungan Residensial Nyaman',
    },
];

export const AirbnbSearchBar: React.FC<AirbnbSearchBarProps> = ({ initialValues }) => {
    const parseInitialAreas = (): string[] => {
        if (initialValues?.areas && Array.isArray(initialValues.areas)) {
            return initialValues.areas.filter(Boolean);
        }
        if (initialValues?.area) {
            return initialValues.area.split(',').map((a) => a.trim()).filter(Boolean);
        }
        return [];
    };

    const [keyword, setKeyword] = useState(initialValues?.keyword || '');
    const [selectedAreas, setSelectedAreas] = useState<string[]>(parseInitialAreas());
    const [gender, setGender] = useState(initialValues?.gender || '');
    const [areaDropdownOpen, setAreaDropdownOpen] = useState(false);
    const [genderDropdownOpen, setGenderDropdownOpen] = useState(false);

    const areaRef = useRef<HTMLDivElement>(null);
    const genderRef = useRef<HTMLDivElement>(null);
    const isFirstMount = useRef(true);
    const lastSubmittedKeyword = useRef(initialValues?.keyword || '');

    // Close popovers on click outside
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent | TouchEvent) => {
            if (areaRef.current && !areaRef.current.contains(event.target as Node)) {
                setAreaDropdownOpen(false);
            }
            if (genderRef.current && !genderRef.current.contains(event.target as Node)) {
                setGenderDropdownOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        document.addEventListener('touchstart', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('touchstart', handleClickOutside);
        };
    }, []);

    // Synchronize local state with props when filter changes externally
    useEffect(() => {
        setKeyword(initialValues?.keyword || '');
        lastSubmittedKeyword.current = initialValues?.keyword || '';
        setSelectedAreas(parseInitialAreas());
        setGender(initialValues?.gender || '');
    }, [initialValues?.keyword, initialValues?.area, initialValues?.areas, initialValues?.gender]);

    const executeSearch = (searchKeyword: string, searchAreas: string[], searchGender: string) => {
        lastSubmittedKeyword.current = searchKeyword;
        router.get(
            '/kos',
            {
                ...initialValues,
                keyword: searchKeyword.trim() || undefined,
                area: searchAreas.length > 0 ? searchAreas.join(',') : undefined,
                gender: searchGender || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['listings', 'filters', 'facilities'],
            }
        );
    };

    // Debounce keyword input (400ms) only when user actively changes keyword
    useEffect(() => {
        if (isFirstMount.current) {
            isFirstMount.current = false;
            return;
        }

        if (keyword === lastSubmittedKeyword.current) {
            return;
        }

        const timer = setTimeout(() => {
            executeSearch(keyword, selectedAreas, gender);
        }, 400);

        return () => clearTimeout(timer);
    }, [keyword]);

    const handleSearchSubmit = (e?: React.FormEvent) => {
        if (e) e.preventDefault();
        setAreaDropdownOpen(false);
        setGenderDropdownOpen(false);
        executeSearch(keyword, selectedAreas, gender);
    };

    const handleToggleArea = (targetArea: string) => {
        let newAreas: string[];
        if (targetArea === '') {
            newAreas = [];
        } else if (selectedAreas.includes(targetArea)) {
            newAreas = selectedAreas.filter((a) => a !== targetArea);
        } else {
            newAreas = [...selectedAreas, targetArea];
        }
        setSelectedAreas(newAreas);
        executeSearch(keyword, newAreas, gender);
    };

    const handleGenderSelect = (newGender: string) => {
        setGender(newGender);
        setGenderDropdownOpen(false);
        executeSearch(keyword, selectedAreas, newGender);
    };

    const getSelectedAreaLabel = () => {
        if (selectedAreas.length === 0) return 'Semua Wilayah';
        if (selectedAreas.length === 1) return selectedAreas[0];
        if (selectedAreas.length === 2) return `${selectedAreas[0]}, ${selectedAreas[1]}`;
        return `${selectedAreas.length} Wilayah Dipilih`;
    };

    const getGenderLabel = () => {
        if (gender === 'putra') return 'Kos Putra';
        if (gender === 'putri') return 'Kos Putri';
        if (gender === 'campur') return 'Kos Campur';
        return 'Semua Tipe';
    };

    return (
        <div className="w-full max-w-4xl mx-auto mb-6">
            <form
                onSubmit={handleSearchSubmit}
                className="bg-white rounded-3xl md:rounded-full p-2 pl-4 sm:pl-5 shadow-lg shadow-slate-200/50 hover:shadow-xl transition-all duration-300 border border-slate-200/90 flex flex-col md:flex-row items-center divide-y md:divide-y-0 md:divide-x divide-slate-100 relative"
            >
                {/* Partition 1: Search Keyword */}
                <div className="flex-1 w-full py-2 px-3 flex items-center justify-between group text-left">
                    <div className="flex flex-col flex-1 min-w-0 pr-2">
                        <label className="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Cari Kos
                        </label>
                        <input
                            type="text"
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                            placeholder="Nama kos, gang, atau ciri lokasi..."
                            className="p-0 border-0 text-xs sm:text-sm font-bold text-slate-900 placeholder-slate-400 focus:ring-0 w-full truncate bg-transparent outline-none"
                        />
                    </div>
                </div>

                {/* Partition 2: Modern Area UNNES Popover */}
                <div ref={areaRef} className="w-full md:w-64 py-2 px-3 text-left relative">
                    <div
                        className="flex items-center justify-between cursor-pointer group"
                        onClick={() => {
                            setAreaDropdownOpen(!areaDropdownOpen);
                            setGenderDropdownOpen(false);
                        }}
                    >
                        <div className="flex flex-col flex-1 min-w-0 pr-2">
                            <label className="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-[#FF6565] transition-colors cursor-pointer">
                                Area UNNES
                            </label>
                            <div className="text-xs sm:text-sm font-bold text-slate-900 truncate">
                                {getSelectedAreaLabel()}
                            </div>
                        </div>

                        <div className={`w-6 h-6 rounded-full flex items-center justify-center transition-transform duration-200 text-slate-400 group-hover:text-slate-700 ${areaDropdownOpen ? 'rotate-180 bg-slate-100' : ''}`}>
                            <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {/* Rich Modern Area Dropdown Popover */}
                    {areaDropdownOpen && (
                        <div className="absolute top-full left-0 md:-left-12 mt-3 w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-slate-200/90 p-3 z-50 animate-in fade-in zoom-in-95 duration-150">
                            <div className="px-3 py-2 border-b border-slate-100 flex items-center justify-between mb-1.5">
                                <span className="text-xs font-black text-slate-900 tracking-tight">
                                    Pilih Wilayah (Multi-Pilihan)
                                </span>
                                {selectedAreas.length > 0 ? (
                                    <button
                                        type="button"
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            handleToggleArea('');
                                        }}
                                        className="text-[10px] font-bold text-[#FF6565] hover:underline cursor-pointer bg-rose-50 px-2 py-0.5 rounded-full"
                                    >
                                        Reset ({selectedAreas.length})
                                    </button>
                                ) : (
                                    <span className="text-[10px] font-semibold text-slate-400">
                                        Pilih 1 atau lebih
                                    </span>
                                )}
                            </div>

                            <div className="max-h-72 overflow-y-auto space-y-1 pr-1 custom-scrollbar">
                                {UNNES_AREAS.map((item) => {
                                    const isSelected = item.value === ''
                                        ? selectedAreas.length === 0
                                        : selectedAreas.includes(item.value);

                                    return (
                                        <button
                                            key={item.value || 'all'}
                                            type="button"
                                            onClick={() => handleToggleArea(item.value)}
                                            className={`w-full text-left p-2.5 rounded-2xl transition-all flex items-center gap-3 cursor-pointer group ${
                                                isSelected
                                                    ? 'bg-rose-50/80 border border-rose-200 text-slate-900 shadow-2xs'
                                                    : 'hover:bg-slate-50 border border-transparent text-slate-700'
                                            }`}
                                        >
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-center gap-2">
                                                    <span className={`text-xs font-bold ${isSelected ? 'text-[#FF6565]' : 'text-slate-900'}`}>
                                                        {item.label}
                                                    </span>
                                                    {item.badge && (
                                                        <span className={`text-[9px] font-bold px-1.5 py-0.2 rounded-md ${
                                                            isSelected
                                                                ? 'bg-[#FF6565] text-white'
                                                                : 'bg-slate-200/70 text-slate-600'
                                                        }`}>
                                                            {item.badge}
                                                        </span>
                                                    )}
                                                </div>
                                                <p className="text-[11px] text-slate-500 leading-tight mt-0.5 truncate">
                                                    {item.description}
                                                </p>
                                            </div>

                                            {isSelected && (
                                                <div className="w-5 h-5 rounded-full bg-[#FF6565] text-white flex items-center justify-center text-[10px] font-black shrink-0 shadow-2xs">
                                                    ✓
                                                </div>
                                            )}
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>

                {/* Partition 3: Gender Popover */}
                <div ref={genderRef} className="w-full md:w-44 py-2 px-3 text-left relative">
                    <div
                        className="flex items-center justify-between cursor-pointer group"
                        onClick={() => {
                            setGenderDropdownOpen(!genderDropdownOpen);
                            setAreaDropdownOpen(false);
                        }}
                    >
                        <div className="flex flex-col flex-1 min-w-0 pr-2">
                            <label className="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider group-hover:text-[#FF6565] transition-colors cursor-pointer">
                                Tipe Kos
                            </label>
                            <div className="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-900 truncate">
                                <span>{getGenderLabel()}</span>
                            </div>
                        </div>

                        <div className={`w-6 h-6 rounded-full flex items-center justify-center transition-transform duration-200 text-slate-400 group-hover:text-slate-700 ${genderDropdownOpen ? 'rotate-180 bg-slate-100' : ''}`}>
                            <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {/* Popover Dropdown */}
                    {genderDropdownOpen && (
                        <div className="absolute top-full left-0 md:-left-8 mt-3 w-56 bg-white rounded-3xl shadow-2xl border border-slate-200/90 p-2 z-50 animate-in fade-in zoom-in-95 duration-150">
                            {[
                                { value: '', label: 'Semua Tipe', desc: 'Putri, Putra & Campur' },
                                { value: 'putri', label: 'Khusus Putri', desc: 'Hunian aman mahasiswi' },
                                { value: 'putra', label: 'Khusus Putra', desc: 'Bebas & kondusif' },
                                { value: 'campur', label: 'Kos Campur', desc: 'Putra & Putri / Pasutri' },
                            ].map((opt) => {
                                const isSelected = gender === opt.value;
                                return (
                                    <button
                                        key={opt.value}
                                        type="button"
                                        onClick={() => handleGenderSelect(opt.value)}
                                        className={`w-full text-left p-2.5 rounded-2xl transition-all flex items-center gap-3 cursor-pointer group ${
                                            isSelected
                                                ? 'bg-rose-50/80 border border-rose-200 text-slate-900 shadow-2xs'
                                                : 'hover:bg-slate-50 border border-transparent text-slate-700'
                                        }`}
                                    >
                                        <div className="flex-1 min-w-0">
                                            <div className={`text-xs font-bold ${isSelected ? 'text-[#FF6565]' : 'text-slate-900'}`}>
                                                {opt.label}
                                            </div>
                                            <div className="text-[10px] text-slate-400 truncate">
                                                {opt.desc}
                                            </div>
                                        </div>
                                        {isSelected && (
                                            <div className="w-4 h-4 rounded-full bg-[#FF6565] text-white flex items-center justify-center text-[9px] font-black shrink-0">
                                                ✓
                                            </div>
                                        )}
                                    </button>
                                );
                            })}
                        </div>
                    )}
                </div>

                {/* Circular Brand Coral Search Button */}
                <div className="w-full md:w-auto pt-2 md:pt-0 md:pl-1 shrink-0 flex justify-end">
                    <button
                        type="submit"
                        className="w-11 h-11 rounded-full bg-[#FF6565] hover:bg-[#ff4d4d] active:bg-[#e04343] text-white flex items-center justify-center shadow-md shadow-[#FF6565]/35 transition-transform hover:scale-105 active:scale-95 cursor-pointer"
                        aria-label="Cari Kos"
                    >
                        <svg className="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    );
};
