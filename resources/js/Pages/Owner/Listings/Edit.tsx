import React, { FormEvent, ChangeEvent, useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { Facility, GenderType, Listing } from '@/types/listing';
import { PublicLayout } from '@/components/shared/PublicLayout';
import { Input } from '@/components/ui/Input';
import { Select } from '@/components/ui/Select';
import { Button } from '@/components/ui/Button';

interface EditProps {
    listing: Listing;
    facilities: Facility[];
}

export default function Edit({ listing, facilities }: EditProps) {
    const [uploading, setUploading] = useState(false);
    const [uploadError, setUploadError] = useState<string | null>(null);

    const { data, setData, put, processing, errors } = useForm<{
        name: string;
        gender_type: GenderType;
        area: string;
        address: string;
        description: string;
        rules: string;
        facility_ids: string[];
    }>({
        name: listing.name,
        gender_type: listing.gender_type,
        area: listing.area,
        address: listing.address,
        description: listing.description,
        rules: listing.rules || '',
        facility_ids: listing.facilities?.map((f) => f.id) || [],
    });

    const areas = ['Sekaran', 'Banaran', 'Taman Siswa', 'Gunungpati', 'Patemon', 'Menoreh'];

    const handleFacilityToggle = (facilityId: string) => {
        if (data.facility_ids.includes(facilityId)) {
            setData('facility_ids', data.facility_ids.filter((id) => id !== facilityId));
        } else {
            setData('facility_ids', [...data.facility_ids, facilityId]);
        }
    };

    const handleUpdateInfo = (e: FormEvent) => {
        e.preventDefault();
        put(`/owner/kos/${listing.id}`, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    const handleImageUpload = (e: ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;

        setUploading(true);
        setUploadError(null);

        const formData = new FormData();
        formData.append('image', file);

        router.post(`/owner/kos/${listing.id}/images`, formData, {
            forceFormData: true,
            preserveScroll: true,
            preserveState: true,
            onFinish: () => {
                setUploading(false);
            },
            onError: (err) => {
                setUploadError(err.image || 'Gagal mengunggah foto.');
            },
        });
    };

    const handlePublish = () => {
        if (confirm('Publikasikan kos ini ke katalog publik?')) {
            router.post(`/owner/kos/${listing.id}/publish`, {}, {
                preserveScroll: true,
                preserveState: true,
            });
        }
    };

    return (
        <PublicLayout>
            <Head title={`Edit ${listing.name} | Pusat Kost UNNES`} />

            <div className="max-w-5xl mx-auto space-y-6">
                {/* Header with Publish CTA */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
                    <div>
                        <h1 className="text-2xl font-black text-slate-900 tracking-tight">
                            {listing.name}
                        </h1>
                        <p className="text-xs text-slate-500 mt-1">
                            Status saat ini: <strong className="uppercase">{listing.status}</strong>
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <Link href="/owner/kos">
                            <Button variant="ghost" size="sm">
                                ← Kembali
                            </Button>
                        </Link>
                        <Link href={`/owner/kos/${listing.id}/kamar`}>
                            <Button variant="outline" size="sm">
                                🛏️ Kelola Kamar ({listing.rooms?.length || 0})
                            </Button>
                        </Link>

                        {listing.status === 'draft' && (
                            <Button variant="primary" size="sm" onClick={handlePublish}>
                                🚀 Publikasikan Kos
                            </Button>
                        )}
                    </div>
                </div>

                {/* Grid 2-Column: Form Info (Left) & Image Gallery Manager (Right) */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Left: Edit Info Form */}
                    <form onSubmit={handleUpdateInfo} className="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                        <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                            Informasi Properti
                        </h2>

                        <Input
                            label="Nama Kos"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            error={errors.name}
                            required
                        />

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <Select
                                label="Tipe Kos"
                                value={data.gender_type}
                                onChange={(e) => setData('gender_type', e.target.value as GenderType)}
                                error={errors.gender_type}
                                options={[
                                    { value: 'putri', label: 'Kos Putri' },
                                    { value: 'putra', label: 'Kos Putra' },
                                    { value: 'campur', label: 'Kos Campur' },
                                ]}
                            />

                            <Select
                                label="Area Sekitar UNNES"
                                value={data.area}
                                onChange={(e) => setData('area', e.target.value)}
                                error={errors.area}
                                options={areas.map((a) => ({ value: a, label: `Area ${a}` }))}
                            />
                        </div>

                        <Input
                            label="Alamat Lengkap"
                            value={data.address}
                            onChange={(e) => setData('address', e.target.value)}
                            error={errors.address}
                            required
                        />

                        <div className="space-y-1.5">
                            <label className="block text-sm font-medium text-slate-700">Deskripsi</label>
                            <textarea
                                rows={4}
                                className="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                required
                            />
                        </div>

                        <div className="space-y-1.5">
                            <label className="block text-sm font-medium text-slate-700">Peraturan Kos</label>
                            <textarea
                                rows={3}
                                className="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#FF6565] focus:ring-2 focus:ring-[#FF6565]/20 focus:outline-none"
                                value={data.rules}
                                onChange={(e) => setData('rules', e.target.value)}
                            />
                        </div>

                        <div className="space-y-3">
                            <label className="block text-sm font-medium text-slate-700">Fasilitas Bersama</label>
                            <div className="grid grid-cols-2 gap-2">
                                {facilities.map((f) => (
                                    <label key={f.id} className="flex items-center gap-2 text-xs text-slate-700">
                                        <input
                                            type="checkbox"
                                            checked={data.facility_ids.includes(f.id)}
                                            onChange={() => handleFacilityToggle(f.id)}
                                            className="rounded border-slate-300 text-[#FF6565] focus:ring-[#FF6565]"
                                        />
                                        <span>{f.name}</span>
                                    </label>
                                ))}
                            </div>
                        </div>

                        <div className="pt-4 border-t border-slate-100 flex justify-end">
                            <Button variant="primary" type="submit" isLoading={processing}>
                                Simpan Perubahan Info
                            </Button>
                        </div>
                    </form>

                    {/* Right: Images Manager */}
                    <div className="space-y-6">
                        <div className="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                            <h2 className="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                                Foto Galeri Kos ({listing.images?.length || 0})
                            </h2>

                            {/* Uploader Box */}
                            <label className="flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-300 rounded-2xl hover:border-[#FF6565] transition-colors cursor-pointer bg-slate-50/50">
                                <span className="text-2xl mb-1">📸</span>
                                <span className="text-xs font-semibold text-slate-700">
                                    {uploading ? 'Mengunggah...' : 'Klik untuk Unggah Foto'}
                                </span>
                                <span className="text-[11px] text-slate-400 mt-0.5">
                                    Format JPG/PNG/WebP, Maks 5MB
                                </span>
                                <input
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    onChange={handleImageUpload}
                                    className="hidden"
                                    disabled={uploading}
                                />
                            </label>
                            {uploadError && <p className="text-xs text-red-600">{uploadError}</p>}

                            {/* Uploaded Images List */}
                            <div className="grid grid-cols-2 gap-3 pt-2">
                                {listing.images && listing.images.length > 0 ? (
                                    listing.images.map((img) => (
                                        <div key={img.id} className="relative aspect-4/3 rounded-xl overflow-hidden border border-slate-200 group">
                                            <img src={img.url} alt="Kos foto" className="w-full h-full object-cover" />
                                            {img.is_primary && (
                                                <span className="absolute top-1.5 left-1.5 px-1.5 py-0.5 text-[10px] font-bold bg-[#FF6565] text-white rounded">
                                                    Foto Utama
                                                </span>
                                            )}
                                        </div>
                                    ))
                                ) : (
                                    <p className="col-span-2 text-center text-xs text-slate-400 py-4">
                                        Belum ada foto yang diunggah. Wajib minimal 1 foto utama untuk publikasi.
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
