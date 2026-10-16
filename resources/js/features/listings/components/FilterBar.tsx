import React from 'react';
import { router } from '@inertiajs/react';
import { Facility, GenderType } from '@/types/listing';
import { FacilityIcon } from '@/features/listings/components/FacilityBadge';
import { SortDropdown } from '@/components/ui/SortDropdown';

interface FilterBarProps {
    facilities?: Facility[];
    filters: {
        keyword?: string;
        gender?: string;
        area?: string;
        areas?: string[];
        min_price?: number | null;
        max_price?: number | null;
        facility_ids?: string[];
        sort?: string;
    };
    totalResults: number;
    onOpenFilterModal: () => void;
}

export const FilterBar: React.FC<FilterBarProps> = ({
    facilities = [],
    filters,
    totalResults,
    onOpenFilterModal,
}) => {
    // Optimistic facility selection state for instant visual feedback
    const [optimisticFacilityIds, setOptimisticFacilityIds] = React.useState<string[]>(filters.facility_ids || []);

    React.useEffect(() => {
        setOptimisticFacilityIds(filters.facility_ids || []);
    }, [filters.facility_ids]);

    const handleGenderChange = (gender: string) => {
        router.get(
            '/kos',
            { ...filters, gender: gender === filters.gender ? '' : gender },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['listings', 'filters', 'facilities'],
            }
        );
    };

    const handleSortChange = (sort: string) => {
        router.get(
            '/kos',
            { ...filters, sort: sort !== 'recommended' ? sort : undefined },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['listings', 'filters', 'facilities'],
            }
        );
    };

    const handleToggleFacility = (facilityId: string) => {
        const currentIds = optimisticFacilityIds;
        const newIds = currentIds.includes(facilityId)
            ? currentIds.filter((id) => id !== facilityId)
            : [...currentIds, facilityId];

        // 1. Instant local visual update
        setOptimisticFacilityIds(newIds);

        // 2. Fetch filtered listings seamlessly without reloads
        router.get(
            '/kos',
            {
                ...filters,
                facility_ids: newIds.length > 0 ? newIds : undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['listings', 'filters', 'facilities'],
            }
        );
    };

    const handleRemoveArea = (areaToRemove: string) => {
        const currentAreas = filters.areas && filters.areas.length > 0
            ? filters.areas
            : (filters.area ? filters.area.split(',').map((a) => a.trim()).filter(Boolean) : []);

        const newAreas = currentAreas.filter((a) => a !== areaToRemove);

        router.get(
            '/kos',
            {
                ...filters,
                area: newAreas.length > 0 ? newAreas.join(',') : undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['listings', 'filters', 'facilities'],
            }
        );
    };

    const handleReset = () => {
        setOptimisticFacilityIds([]);
        router.get(
            '/kos',
            {},
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['listings', 'filters', 'facilities'],
            }
        );
    };

    const activeAreas = filters.areas && filters.areas.length > 0
        ? filters.areas
        : (filters.area ? filters.area.split(',').map((a) => a.trim()).filter(Boolean) : []);

    const activeFilterCount =
        (filters.keyword ? 1 : 0) +
        (filters.gender ? 1 : 0) +
        (activeAreas.length > 0 ? activeAreas.length : 0) +
        (filters.min_price || filters.max_price ? 1 : 0) +
        (optimisticFacilityIds.length > 0 ? optimisticFacilityIds.length : 0) +
        (filters.sort && filters.sort !== 'recommended' ? 1 : 0);

    return (
        <div className="w-full bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs mb-8 space-y-3.5">
            {/* Row 1: Gender / Category Pills & Sort / Filter Modal */}
            <div className="flex flex-wrap items-center justify-between gap-4">
                {/* Gender Pills */}
                <div className="flex flex-wrap items-center gap-2">
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">
                        Kategori:
                    </span>

                    <button
                        type="button"
                        onClick={() => handleGenderChange('')}
                        className={`px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all cursor-pointer ${
                            !filters.gender
                                ? 'bg-slate-900 text-white shadow-xs'
                                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                        }`}
                    >
                        Semua ({totalResults})
                    </button>

                    {(['putri', 'putra', 'campur'] as GenderType[]).map((g) => (
                        <button
                            key={g}
                            type="button"
                            onClick={() => handleGenderChange(g)}
                            className={`px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all capitalize cursor-pointer ${
                                filters.gender === g
                                    ? 'bg-[#FF6565] text-white shadow-xs shadow-[#FF6565]/25'
                                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                            }`}
                        >
                            Kos {g}
                        </button>
                    ))}
                </div>

                {/* Filter Modal Trigger & Sort Button */}
                <div className="flex items-center gap-3">
                    {/* Sort Dropdown */}
                    <div className="flex items-center gap-1.5">
                        <span className="text-xs text-slate-400 font-semibold hidden sm:inline">Urutkan:</span>
                        <SortDropdown
                            value={filters.sort || 'recommended'}
                            onChange={handleSortChange}
                        />
                    </div>

                    {/* Filter Lengkap Button */}
                    <button
                        type="button"
                        onClick={onOpenFilterModal}
                        className={`flex items-center gap-2 px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all cursor-pointer ${
                            activeFilterCount > 0
                                ? 'border-[#FF6565] text-[#FF6565] bg-rose-50/50 shadow-xs'
                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                        }`}
                    >
                        <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filter Lengkap</span>
                        {activeFilterCount > 0 && (
                            <span className="w-4 h-4 rounded-full bg-[#FF6565] text-white text-[10px] flex items-center justify-center font-black">
                                {activeFilterCount}
                            </span>
                        )}
                    </button>
                </div>
            </div>

            {/* Row 2: Quick Facility Filter Badges (AC, KM Dalam, Parkir Mobil, WiFi, etc.) with Multi-Select */}
            {facilities.length > 0 && (
                <div className="pt-2.5 border-t border-slate-100/90 flex flex-wrap items-center gap-2">
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1">
                        <span>Fasilitas:</span>
                    </span>

                    {(() => {
                        const priorityKeywords = ['ac', 'kamar mandi', 'bath', 'mobil', 'motor', 'wifi', 'kasur', 'dapur', '24 jam', 'lemari'];
                        const sortedFacilities = [...facilities].sort((a, b) => {
                            const aName = (a.name || '').toLowerCase();
                            const bName = (b.name || '').toLowerCase();
                            const aIdx = priorityKeywords.findIndex((k) => aName.includes(k));
                            const bIdx = priorityKeywords.findIndex((k) => bName.includes(k));
                            const aScore = aIdx !== -1 ? aIdx : 999;
                            const bScore = bIdx !== -1 ? bIdx : 999;
                            return aScore - bScore;
                        });

                        return sortedFacilities.map((fac) => {
                            const isSelected = optimisticFacilityIds.includes(fac.id);
                            return (
                                <button
                                    key={fac.id}
                                    type="button"
                                    onClick={() => handleToggleFacility(fac.id)}
                                    className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs transition-all cursor-pointer select-none border ${
                                        isSelected
                                            ? 'bg-rose-50 border-[#FF6565] text-[#FF6565] font-bold shadow-xs shadow-[#FF6565]/10'
                                            : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-700 font-medium'
                                    }`}
                                >
                                    <FacilityIcon
                                        name={fac.icon || fac.name}
                                        className={`w-3.5 h-3.5 ${isSelected ? 'text-[#FF6565]' : 'text-slate-500'}`}
                                    />
                                    <span>{fac.name}</span>
                                    {isSelected && (
                                        <span className="w-3.5 h-3.5 rounded-full bg-[#FF6565] text-white flex items-center justify-center text-[9px] font-black shrink-0">
                                            ✓
                                        </span>
                                    )}
                                </button>
                            );
                        });
                    })()}
                </div>
            )}

            {/* Row 3: Active Filters Row (if any) */}
            {activeFilterCount > 0 && (
                <div className="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-xs">
                    <span className="text-[11px] text-slate-400 font-semibold">Filter Aktif:</span>

                    {filters.keyword && (
                        <span className="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded-md font-medium flex items-center gap-1">
                            Kata kunci: &quot;{filters.keyword}&quot;
                        </span>
                    )}

                    {activeAreas.map((areaName) => (
                        <button
                            key={areaName}
                            type="button"
                            onClick={() => handleRemoveArea(areaName)}
                            className="px-2.5 py-0.5 bg-slate-100 hover:bg-rose-50 hover:text-[#FF6565] text-slate-800 rounded-md font-medium flex items-center gap-1.5 transition-colors cursor-pointer group"
                            title="Klik untuk hapus"
                        >
                            <span>Area {areaName}</span>
                            <span className="text-slate-400 group-hover:text-[#FF6565] font-bold text-[10px]">✕</span>
                        </button>
                    ))}

                    {(filters.min_price || filters.max_price) && (
                        <span className="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded-md font-medium flex items-center gap-1">
                            Budget: {filters.min_price ? `Rp ${new Intl.NumberFormat('id-ID').format(filters.min_price)}` : 'Rp 0'} - {filters.max_price ? `Rp ${new Intl.NumberFormat('id-ID').format(filters.max_price)}` : 'Tak Terbatas'}
                        </span>
                    )}

                    {optimisticFacilityIds.length > 0 && (
                        <>
                            {optimisticFacilityIds.map((id) => {
                                const matched = facilities.find((f) => f.id === id);
                                if (!matched) return null;
                                return (
                                    <button
                                        key={id}
                                        type="button"
                                        onClick={() => handleToggleFacility(id)}
                                        className="px-2.5 py-0.5 bg-rose-50 text-[#FF6565] border border-rose-100 rounded-md font-medium flex items-center gap-1.5 hover:bg-rose-100 transition-colors cursor-pointer group"
                                        title="Klik untuk hapus"
                                    >
                                        <FacilityIcon name={matched.icon || matched.name} className="w-3 h-3" />
                                        <span>{matched.name}</span>
                                        <span className="font-bold text-[10px]">✕</span>
                                    </button>
                                );
                            })}
                        </>
                    )}

                    <button
                        type="button"
                        onClick={handleReset}
                        className="text-xs font-bold text-[#FF6565] hover:underline cursor-pointer ml-auto"
                    >
                        Hapus Semua
                    </button>
                </div>
            )}
        </div>
    );
};

