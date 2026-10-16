import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import { Facility } from '@/types/listing';
import { Modal } from '@/components/ui/Modal';
import { Button } from '@/components/ui/Button';

interface FilterModalProps {
    isOpen: boolean;
    onClose: () => void;
    facilities: Facility[];
    currentFilters: {
        keyword?: string;
        gender?: string;
        area?: string;
        areas?: string[];
        min_price?: number | null;
        max_price?: number | null;
        facility_ids?: string[];
        sort?: string;
    };
}

const UNNES_AREAS = [
    { value: '', label: 'Semua Area', desc: 'Seluruh Sekitar UNNES' },
    { value: 'Sekaran', label: 'Sekaran', desc: 'Rektorat / FBS / FEB' },
    { value: 'Banaran', label: 'Banaran', desc: 'Teknik / MIPA' },
    { value: 'Taman Siswa', label: 'Taman Siswa', desc: 'Kuliner & Bank' },
    { value: 'Gunungpati', label: 'Gunungpati', desc: 'FIP / FIK' },
    { value: 'Patemon', label: 'Patemon', desc: 'Gerbang Belakang' },
    { value: 'Menoreh', label: 'Menoreh', desc: 'Sampangan' },
    { value: 'Kalisegoro', label: 'Kalisegoro', desc: 'Asri & Tenang' },
];

export const FilterModal: React.FC<FilterModalProps> = ({
    isOpen,
    onClose,
    facilities,
    currentFilters,
}) => {
    const parseInitialAreas = (): string[] => {
        if (currentFilters.areas && Array.isArray(currentFilters.areas)) {
            return currentFilters.areas.filter(Boolean);
        }
        if (currentFilters.area) {
            return currentFilters.area.split(',').map((a) => a.trim()).filter(Boolean);
        }
        return [];
    };

    const [selectedAreas, setSelectedAreas] = useState<string[]>(parseInitialAreas());
    const [minPrice, setMinPrice] = useState<number | ''>(currentFilters.min_price ?? '');
    const [maxPrice, setMaxPrice] = useState<number | ''>(currentFilters.max_price ?? '');
    const [selectedFacilities, setSelectedFacilities] = useState<string[]>(currentFilters.facility_ids ?? []);
    const [selectedSort, setSelectedSort] = useState<string>(currentFilters.sort ?? 'recommended');

    // Sync local state when currentFilters prop changes
    React.useEffect(() => {
        setSelectedAreas(parseInitialAreas());
        setMinPrice(currentFilters.min_price ?? '');
        setMaxPrice(currentFilters.max_price ?? '');
        setSelectedFacilities(currentFilters.facility_ids ?? []);
        setSelectedSort(currentFilters.sort ?? 'recommended');
    }, [currentFilters]);

    const handleToggleArea = (targetArea: string) => {
        if (targetArea === '') {
            setSelectedAreas([]);
            return;
        }
        setSelectedAreas((prev) =>
            prev.includes(targetArea)
                ? prev.filter((a) => a !== targetArea)
                : [...prev, targetArea]
        );
    };

    const handleToggleFacility = (id: string) => {
        setSelectedFacilities((prev) =>
            prev.includes(id) ? prev.filter((item) => item !== id) : [...prev, id]
        );
    };

    const handleApply = () => {
        router.get(
            '/kos',
            {
                keyword: currentFilters.keyword || undefined,
                gender: currentFilters.gender || undefined,
                area: selectedAreas.length > 0 ? selectedAreas.join(',') : undefined,
                min_price: minPrice !== '' ? Number(minPrice) : undefined,
                max_price: maxPrice !== '' ? Number(maxPrice) : undefined,
                facility_ids: selectedFacilities.length > 0 ? selectedFacilities : undefined,
                sort: selectedSort !== 'recommended' ? selectedSort : undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['listings', 'filters', 'facilities'],
                onFinish: () => onClose(),
            }
        );
    };

    const handleResetAll = () => {
        setSelectedAreas([]);
        setMinPrice('');
        setMaxPrice('');
        setSelectedFacilities([]);
        setSelectedSort('recommended');
    };

    return (
        <Modal isOpen={isOpen} onClose={onClose} title="Filter Lengkap Pencarian Kos" maxWidth="lg">
            <div className="space-y-6 max-h-[70vh] overflow-y-auto pr-1">
                {/* 1. Wilayah / Area UNNES */}
                <div className="space-y-3">
                    <div className="flex items-center justify-between">
                        <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Wilayah / Area Sekitar Kampus (Multi-Pilihan)
                        </h3>
                        {selectedAreas.length > 0 && (
                            <span className="text-[11px] font-bold text-[#FF6565]">
                                {selectedAreas.length} Wilayah Dipilih
                            </span>
                        )}
                    </div>

                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        {UNNES_AREAS.map((item) => {
                            const isSelected = item.value === ''
                                ? selectedAreas.length === 0
                                : selectedAreas.includes(item.value);

                            return (
                                <button
                                    key={item.value || 'all'}
                                    type="button"
                                    onClick={() => handleToggleArea(item.value)}
                                    className={`p-2.5 rounded-2xl text-left border transition-all cursor-pointer flex flex-col justify-between group ${
                                        isSelected
                                            ? 'border-[#FF6565] bg-rose-50/80 text-slate-900 shadow-2xs'
                                            : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'
                                    }`}
                                >
                                    <div className="flex items-center justify-between w-full mb-1">
                                        <div className={`text-xs font-bold ${isSelected ? 'text-[#FF6565]' : 'text-slate-900'}`}>
                                            {item.label}
                                        </div>
                                        {isSelected && (
                                            <span className="w-4 h-4 rounded-full bg-[#FF6565] text-white flex items-center justify-center text-[9px] font-black">
                                                ✓
                                            </span>
                                        )}
                                    </div>
                                    <div className="text-[10px] text-slate-400 truncate">
                                        {item.desc}
                                    </div>
                                </button>
                            );
                        })}
                    </div>
                </div>

                {/* 2. Rentang Harga Bulanan */}
                <div className="space-y-3 border-t border-slate-100 pt-4">
                    <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Rentang Harga Sewa (Per Bulan)
                    </h3>

                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block text-[11px] font-semibold text-slate-500 mb-1">
                                Minimum (Rp)
                            </label>
                            <input
                                type="number"
                                step="50000"
                                min="0"
                                placeholder="Contoh: 500.000"
                                value={minPrice}
                                onChange={(e) => setMinPrice(e.target.value === '' ? '' : Number(e.target.value))}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#FF6565] focus:outline-none bg-slate-50"
                            />
                        </div>

                        <div>
                            <label className="block text-[11px] font-semibold text-slate-500 mb-1">
                                Maksimum (Rp)
                            </label>
                            <input
                                type="number"
                                step="50000"
                                min="0"
                                placeholder="Contoh: 1.500.000"
                                value={maxPrice}
                                onChange={(e) => setMaxPrice(e.target.value === '' ? '' : Number(e.target.value))}
                                className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#FF6565] focus:outline-none bg-slate-50"
                            />
                        </div>
                    </div>

                    {/* Quick Budget Chips */}
                    <div className="flex flex-wrap gap-2 pt-1">
                        <button
                            type="button"
                            onClick={() => { setMinPrice(0); setMaxPrice(750000); }}
                            className="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-[#FF6565] transition-colors cursor-pointer"
                        >
                            &lt; Rp 750 rb / bln
                        </button>
                        <button
                            type="button"
                            onClick={() => { setMinPrice(750000); setMaxPrice(1200000); }}
                            className="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-[#FF6565] transition-colors cursor-pointer"
                        >
                            Rp 750 rb - 1,2 jt
                        </button>
                        <button
                            type="button"
                            onClick={() => { setMinPrice(1200000); setMaxPrice(2000000); }}
                            className="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-[#FF6565] transition-colors cursor-pointer"
                        >
                            &gt; Rp 1,2 jt / bln
                        </button>
                    </div>
                </div>

                {/* 3. Fasilitas Kos */}
                <div className="space-y-3 border-t border-slate-100 pt-4">
                    <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Fasilitas yang Diinginkan
                    </h3>

                    <div className="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        {facilities.map((fac) => {
                            const isChecked = selectedFacilities.includes(fac.id);
                            return (
                                <label
                                    key={fac.id}
                                    className={`flex items-center gap-2 p-2.5 rounded-xl border text-xs font-medium cursor-pointer transition-colors ${
                                        isChecked
                                            ? 'border-[#FF6565] bg-rose-50/50 text-slate-900 font-bold'
                                            : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'
                                    }`}
                                >
                                    <input
                                        type="checkbox"
                                        checked={isChecked}
                                        onChange={() => handleToggleFacility(fac.id)}
                                        className="rounded border-slate-300 text-[#FF6565] focus:ring-[#FF6565] h-3.5 w-3.5"
                                    />
                                    <span className="truncate">{fac.name}</span>
                                </label>
                            );
                        })}
                    </div>
                </div>

                {/* 4. Urutkan Berdasarkan */}
                <div className="space-y-3 border-t border-slate-100 pt-4">
                    <div className="flex items-center justify-between">
                        <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Urutkan Hasil
                        </h3>
                        <span className="text-[11px] text-slate-400">Pilih 1 kriteria</span>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        {[
                            { label: 'Rekomendasi Terbaik', sub: 'Skor kecocokan mahasiswa UNNES', value: 'recommended', icon: '⭐' },
                            { label: 'Harga Termurah', sub: 'Urutan tarif sewa paling hemat', value: 'price_asc', icon: '💵' },
                            { label: 'Fasilitas & Harga Premium', sub: 'Kamar eksklusif & fasilitas lengkap', value: 'price_desc', icon: '💎' },
                            { label: 'Rating Tertinggi', sub: 'Ulasan kepuasan bintang 5', value: 'rating_desc', icon: '★' },
                            { label: 'Kos Baru Ditambahkan', sub: 'Listing baru diinput pemilik', value: 'newest', icon: '🆕' },
                        ].map((s) => {
                            const isSelected = selectedSort === s.value || (!selectedSort && s.value === 'recommended');
                            return (
                                <button
                                    key={s.value}
                                    type="button"
                                    onClick={() => setSelectedSort(s.value)}
                                    className={`p-3 rounded-2xl text-left border transition-all cursor-pointer flex items-start gap-2.5 active:scale-[0.98] ${
                                        isSelected
                                            ? 'border-[#FF6565] bg-rose-50/70 text-slate-950 shadow-2xs'
                                            : 'border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700'
                                    }`}
                                >
                                    <span className="text-base shrink-0 mt-0.5">{s.icon}</span>
                                    <div className="flex-1 min-w-0">
                                        <div className={`text-xs ${isSelected ? 'font-black text-[#FF6565]' : 'font-bold text-slate-800'}`}>
                                            {s.label}
                                        </div>
                                        <div className="text-[10px] text-slate-500 font-normal leading-tight mt-0.5">
                                            {s.sub}
                                        </div>
                                    </div>
                                    {isSelected && (
                                        <span className="w-4 h-4 rounded-full bg-[#FF6565] text-white flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">
                                            ✓
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                </div>
            </div>

            {/* Modal Actions Footer */}
            <div className="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button
                    type="button"
                    onClick={handleResetAll}
                    className="text-xs font-bold text-slate-500 hover:text-[#FF6565] cursor-pointer"
                >
                    Reset Pilihan
                </button>

                <div className="flex gap-2">
                    <Button variant="outline" size="sm" onClick={onClose}>
                        Batal
                    </Button>
                    <Button variant="primary" size="sm" onClick={handleApply}>
                        Terapkan Filter ({selectedAreas.length + selectedFacilities.length + (minPrice !== '' || maxPrice !== '' ? 1 : 0) + (selectedSort !== 'recommended' ? 1 : 0)})
                    </Button>
                </div>
            </div>
        </Modal>
    );
};
