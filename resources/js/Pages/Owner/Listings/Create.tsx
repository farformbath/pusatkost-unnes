import React, { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { Facility, GenderType } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Input } from '@/components/ui/Input';
import { Select } from '@/components/ui/Select';
import { Button } from '@/components/ui/Button';

interface CreateProps {
    facilities: Facility[];
}

export default function Create({ facilities }: CreateProps) {
    const { data, setData, post, processing, errors } = useForm<{
        name: string;
        gender_type: GenderType;
        area: string;
        address: string;
        description: string;
        rules: string;
        facility_ids: string[];
    }>({
        name: '',
        gender_type: 'putri',
        area: 'Sekaran',
        address: '',
        description: '',
        rules: '',
        facility_ids: [],
    });

    const areas = ['Sekaran', 'Banaran', 'Taman Siswa', 'Gunungpati', 'Patemon', 'Menoreh'];

    const handleFacilityToggle = (facilityId: string) => {
        if (data.facility_ids.includes(facilityId)) {
            setData('facility_ids', data.facility_ids.filter((id) => id !== facilityId));
        } else {
            setData('facility_ids', [...data.facility_ids, facilityId]);
        }
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/owner/kos');
    };

    return (
        <PublicLayout>
            <Head title="Daftarkan Kos Baru | Pusat Kost UNNES" />

            <div className="max-w-4xl mx-auto space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight">
                            Daftarkan Kos Baru (Langkah 1 dari 2)
                        </h1>
                        <p className="text-xs sm:text-sm text-slate-500">
                            Isi informasi dasar properti kos Anda. Anda dapat mengunggah foto dan menambahkan tipe kamar setelah langkah ini.
                        </p>
                    </div>

                    <Link href="/owner/kos">
                        <Button variant="ghost" size="sm">
                            ← Batal & Kembali
                        </Button>
                    </Link>
                </div>

                <form onSubmit={handleSubmit} className="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                    {/* Basic Info */}
                    <div className="space-y-4">
                        <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                            1. Informasi Utama
                        </h2>

                        <Input
                            label="Nama Kos"
                            placeholder="Contoh: Kost Putri Melati Sekaran"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            error={errors.name}
                            required
                        />

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <Select
                                label="Tipe Kos (Gender)"
                                value={data.gender_type}
                                onChange={(e) => setData('gender_type', e.target.value as GenderType)}
                                error={errors.gender_type}
                                options={[
                                    { value: 'putri', label: 'Kos Putri (Khusus Mahasiswi)' },
                                    { value: 'putra', label: 'Kos Putra (Khusus Mahasiswa)' },
                                    { value: 'campur', label: 'Kos Campur' },
                                ]}
                            />

                            <Select
                                label="Wilayah / Area Sekitar UNNES"
                                value={data.area}
                                onChange={(e) => setData('area', e.target.value)}
                                error={errors.area}
                                options={areas.map((a) => ({ value: a, label: `Area ${a}` }))}
                            />
                        </div>

                        <Input
                            label="Alamat Lengkap"
                            placeholder="Contoh: Jl. Taman Siswa No. 15, Gang Melati RT 02 RW 03, Sekaran"
                            value={data.address}
                            onChange={(e) => setData('address', e.target.value)}
                            error={errors.address}
                            required
                        />
                    </div>

                    {/* Description & Rules */}
                    <div className="space-y-4">
                        <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                            2. Deskripsi & Peraturan
                        </h2>

                        <div className="space-y-1.5">
                            <label className="block text-sm font-medium text-slate-700">
                                Deskripsi Kos
                            </label>
                            <textarea
                                rows={4}
                                className="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none"
                                placeholder="Jelaskan keunggulan kos, jarak ke fakultas tertentu di UNNES, suasana lingkungan..."
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                required
                            />
                            {errors.description && <p className="text-xs text-red-600 font-medium">{errors.description}</p>}
                        </div>

                        <div className="space-y-1.5">
                            <label className="block text-sm font-medium text-slate-700">
                                Peraturan Kos (Opsional)
                            </label>
                            <textarea
                                rows={3}
                                className="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none"
                                placeholder="Contoh: Gerbang dikunci pukul 23:00, dilarang merokok di dalam kamar, tamu lawan jenis hanya di ruang tamu..."
                                value={data.rules}
                                onChange={(e) => setData('rules', e.target.value)}
                            />
                            {errors.rules && <p className="text-xs text-red-600 font-medium">{errors.rules}</p>}
                        </div>
                    </div>

                    {/* Facilities Checkboxes */}
                    <div className="space-y-4">
                        <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                            3. Fasilitas Bersama & Keamanan
                        </h2>

                        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            {facilities.map((facility) => {
                                const isChecked = data.facility_ids.includes(facility.id);
                                return (
                                    <label
                                        key={facility.id}
                                        className={`flex items-center gap-2.5 p-3 rounded-xl border text-xs font-medium cursor-pointer transition-colors ${
                                            isChecked
                                                ? 'border-[#FF6565] bg-rose-50/50 text-[#FF6565]'
                                                : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                                        }`}
                                    >
                                        <input
                                            type="checkbox"
                                            checked={isChecked}
                                            onChange={() => handleFacilityToggle(facility.id)}
                                            className="rounded border-slate-300 text-[#FF6565] focus:ring-[#FF6565]"
                                        />
                                        <span>{facility.name}</span>
                                    </label>
                                );
                            })}
                        </div>
                    </div>

                    {/* Submit Button */}
                    <div className="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <Link href="/owner/kos">
                            <Button variant="outline" type="button">
                                Batal
                            </Button>
                        </Link>
                        <Button variant="primary" type="submit" isLoading={processing}>
                            Simpan & Lanjut Unggah Foto →
                        </Button>
                    </div>
                </form>
            </div>
        </PublicLayout>
    );
}
