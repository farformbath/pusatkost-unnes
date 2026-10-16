<?php

declare(strict_types=1);

namespace App\Domain\Ai\Services;

use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\Search\Actions\SearchListingsAction;
use App\Domain\Search\DTOs\SearchCriteria;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;

class NesaToolService
{
    /**
     * UNNES campus faculty directory and zoning information.
     *
     * @var array<string, array{name: string, nearest_gate: string, recommended_areas: list<string>, landmarks: list<string>, tips: string}>
     */
    private const array CAMPUS_FACULTIES = [
        'FBS' => [
            'name' => 'Fakultas Bahasa dan Seni (FBS)',
            'nearest_gate' => 'Gerbang Utama (Jl. Sekaran Raya) & Gerbang Depan',
            'recommended_areas' => ['Sekaran', 'Taman Siswa'],
            'landmarks' => ['Gedung B9', 'Gedung B6', 'Dekat Bundaran Rektorat'],
            'tips' => 'Area Sekaran dan Taman Siswa sangat ideal karena mahasiswa dapat berjalan kaki ke ruang kuliah FBS.',
        ],
        'FT' => [
            'name' => 'Fakultas Teknik (FT)',
            'nearest_gate' => 'Gerbang Banaran & Gerbang Belakang',
            'recommended_areas' => ['Banaran', 'Kalisegoro'],
            'landmarks' => ['Dekat Lapangan Prof. Dumadi', 'Danau UNNES', 'Asrama Mahasiswa'],
            'tips' => 'Memilih kos di area Banaran memudahkan akses langsung ke laboratorium dan bengkel FT.',
        ],
        'FMIPA' => [
            'name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam (FMIPA)',
            'nearest_gate' => 'Gerbang Banaran / Gerbang Samping',
            'recommended_areas' => ['Banaran', 'Sekaran'],
            'landmarks' => ['Gedung D', 'Laboratorium Terpadu', 'Danau UNNES'],
            'tips' => 'Kos di perbatasan Sekaran-Banaran sangat strategis untuk mahasiswa FMIPA.',
        ],
        'FEB' => [
            'name' => 'Fakultas Ekonomika dan Bisnis (FEB)',
            'nearest_gate' => 'Gerbang Utama & Simpang Tujuh Sekaran',
            'recommended_areas' => ['Sekaran', 'Banaran'],
            'landmarks' => ['Gedung C7', 'Gedung Laboratorium Akuntansi', 'Pusat Bahasa'],
            'tips' => 'Area Sekaran memiliki banyak fasilitas fotokopi, percetakan, dan kuliner terjangkau.',
        ],
        'FIP' => [
            'name' => 'Fakultas Ilmu Pendidikan dan Psikologi (FIPP / FIP)',
            'nearest_gate' => 'Gerbang Sekaran Timur & Gerbang Patemon',
            'recommended_areas' => ['Patemon', 'Sekaran'],
            'landmarks' => ['Gedung A', 'Dekat Lapangan Olahraga'],
            'tips' => 'Area Patemon menawarkan suasana kos yang relatif tenang dan sejuk untuk belajar.',
        ],
        'FIK' => [
            'name' => 'Fakultas Kedokteran & Ilmu Keolahragaan (FIK)',
            'nearest_gate' => 'Gerbang Selatan (Jl. Gunungpati)',
            'recommended_areas' => ['Gunungpati', 'Sekaran'],
            'landmarks' => ['Kolam Renang UNNES', 'Stadion Tri Lomba Juang Sekaran'],
            'tips' => 'Area Gunungpati dan Sekaran Selatan memudahkan mobilitas menuju venue olahraga dan perkuliahan.',
        ],
        'FIS' => [
            'name' => 'Fakultas Ilmu Sosial dan Ilmu Politik (FISIP / FIS)',
            'nearest_gate' => 'Gerbang Utama Sekaran',
            'recommended_areas' => ['Sekaran', 'Banaran'],
            'landmarks' => ['Gedung C4', 'Dekat Masjid Kampus Ulul Albab'],
            'tips' => 'Dekat dengan perpustakaan pusat UNNES dan Masjid Ulul Albab.',
        ],
        'FH' => [
            'name' => 'Fakultas Hukum (FH)',
            'nearest_gate' => 'Gerbang Utama Sekaran',
            'recommended_areas' => ['Sekaran', 'Taman Siswa'],
            'landmarks' => ['Gedung K', 'Dekat Bundaran Rektorat'],
            'tips' => 'Area Sekaran dan Taman Siswa sangat dekat dengan gedung perkuliahan FH.',
        ],
        'FK' => [
            'name' => 'Fakultas Kedokteran (FK)',
            'nearest_gate' => 'Kawasan UNNES Gunungpati & Sekaran',
            'recommended_areas' => ['Gunungpati', 'Sekaran', 'Menoreh'],
            'landmarks' => ['Gedung Dekanat FK', 'Rumah Sakit Jejaring'],
            'tips' => 'Pilih kos di Gunungpati atau Sekaran yang memiliki privasi tinggi untuk menunjang studi intensif.',
        ],
        'FARMASI' => [
            'name' => 'Fakultas Farmasi / Prodi Farmasi',
            'nearest_gate' => 'Gerbang Banaran / Lab Terpadu FMIPA',
            'recommended_areas' => ['Banaran', 'Sekaran'],
            'landmarks' => ['Laboratorium Bahan Alam', 'Gedung D FMIPA'],
            'tips' => 'Pilih kos di Banaran agar mudah menjangkau jadwal praktikum laboratorium pagi dan malam.',
        ],
    ];

    public function __construct(
        private readonly SearchListingsAction $searchListingsAction,
    ) {}

    /**
     * Returns OpenAI-compatible function declarations for NESA agentic tools.
     *
     * @return list<array{type: string, function: array{name: string, description: string, parameters: array{type: string, properties: array<string, mixed>, required: list<string>}}}>
     */
    public function getToolDeclarations(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_kos',
                    'description' => 'Mencari kos di lingkar kampus UNNES berdasarkan kriteria pengguna seperti gender, area/kelurahan, batas harga per bulan, dan fasilitas.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'gender' => [
                                'type' => 'string',
                                'enum' => ['putra', 'putri', 'campur'],
                                'description' => 'Tipe kos berdasarkan gender penghuni',
                            ],
                            'area' => [
                                'type' => 'string',
                                'enum' => [
                                    'Sekaran',
                                    'Banaran',
                                    'Taman Siswa',
                                    'Patemon',
                                    'Gunungpati',
                                    'Menoreh',
                                    'Kalisegoro',
                                    'Trangkil',
                                ],
                                'description' => 'Area atau kelurahan di sekitar UNNES Semarang',
                            ],
                            'max_price' => [
                                'type' => 'integer',
                                'description' => 'Batas harga sewa maksimal per bulan dalam Rupiah (contoh: 1200000)',
                            ],
                            'min_price' => [
                                'type' => 'integer',
                                'description' => 'Batas harga sewa minimal per bulan dalam Rupiah (contoh: 500000)',
                            ],
                            'facilities' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'string',
                                ],
                                'description' => 'Daftar fasilitas penting yang dicari (misal: AC, KM Dalam, WiFi, Parkir Mobil)',
                            ],
                        ],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_kos_detail',
                    'description' => 'Mengambil rincian lengkap kos berdasarkan slug unik kos.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'slug' => [
                                'type' => 'string',
                                'description' => 'Slug pengenal unik dari listing kos',
                            ],
                        ],
                        'required' => ['slug'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_campus_guide',
                    'description' => 'Memberikan panduan lokasi gerbang kampus UNNES, fakultas terdekat, dan rekomendasi zona kos.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'faculty' => [
                                'type' => 'string',
                                'description' => 'Nama atau singkatan fakultas di UNNES (contoh: FBS, FT, FMIPA, FEB, FIP, FIK, FIS, FK, FH, Farmasi)',
                            ],
                        ],
                        'required' => ['faculty'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Executes the requested tool and returns structured execution payload.
     *
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    public function execute(string $toolName, array $arguments): array
    {
        return match ($toolName) {
            'search_kos' => $this->executeSearchKos($arguments),
            'get_kos_detail' => $this->executeGetKosDetail($arguments),
            'get_campus_guide' => $this->executeGetCampusGuide($arguments),
            default => [
                'error' => "Unknown tool: {$toolName}",
                'recommended_listing_ids' => [],
            ],
        };
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    private function executeSearchKos(array $arguments): array
    {
        if (isset($arguments['facilities']) && is_array($arguments['facilities']) && ! empty($arguments['facilities'])) {
            /** @var list<string> $facilityNames */
            $facilityNames = array_values(array_filter($arguments['facilities'], 'is_string'));
            $slugs = array_map(fn (string $f) => Str::slug($f), $facilityNames);

            /** @var list<string> $facilityIds */
            $facilityIds = Facility::query()
                ->whereIn('slug', $slugs)
                ->orWhereIn('id', $facilityNames)
                ->pluck('id')
                ->all();

            if (! empty($facilityIds)) {
                $arguments['facility_ids'] = $facilityIds;
            }
        }

        /** @var array{keyword?: string|null, gender?: string|null, area?: string|list<string>|null, areas?: list<string>|string|null, min_price?: int|string|null, max_price?: int|string|null, facility_ids?: list<string>|string|null, sort?: string|null, page?: int|string|null, per_page?: int|string|null, latitude?: float|string|null, longitude?: float|string|null, radius?: float|string|null} $criteriaData */
        $criteriaData = $arguments;

        $criteria = SearchCriteria::fromArray($criteriaData);
        $paginator = $this->searchListingsAction->execute($criteria);

        /** @var list<string> $listingIds */
        $listingIds = [];

        /** @var list<array{id: string, name: string, slug: string, gender_type: string, area: string, address: string, starting_price: int, formatted_starting_price: string, rating_avg: float, facilities: list<string>}> $listings */
        $listings = [];

        foreach ($paginator->items() as $listing) {
            $listingIds[] = (string) $listing->id;
            $listings[] = [
                'id' => (string) $listing->id,
                'name' => (string) $listing->name,
                'slug' => (string) $listing->slug,
                'gender_type' => $listing->gender_type->value,
                'area' => (string) $listing->area,
                'address' => (string) $listing->address,
                'starting_price' => $listing->starting_price,
                'formatted_starting_price' => $listing->formatted_starting_price,
                'rating_avg' => $listing->rating_avg,
                'facilities' => $listing->facilities->pluck('name')->values()->all(),
            ];
        }

        return [
            'count' => count($listings),
            'total' => $paginator->total(),
            'recommended_listing_ids' => $listingIds,
            'listings' => $listings,
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    private function executeGetKosDetail(array $arguments): array
    {
        $slug = isset($arguments['slug']) && is_string($arguments['slug']) ? $arguments['slug'] : '';

        $listing = Listing::query()
            ->published()
            ->where('slug', $slug)
            ->with([
                'primaryImage',
                'images',
                'facilities',
                'rooms' => function (EloquentBuilder $query): void {
                    $query->where('status', RoomStatus::AVAILABLE)
                        ->where('available_rooms', '>', 0)
                        ->with('facilities');
                },
            ])
            ->first();

        if ($listing === null) {
            return [
                'found' => false,
                'message' => "Kos dengan slug '{$slug}' tidak ditemukan atau belum dipublikasikan.",
                'recommended_listing_ids' => [],
            ];
        }

        return [
            'found' => true,
            'recommended_listing_ids' => [(string) $listing->id],
            'listing' => [
                'id' => (string) $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'gender_type' => $listing->gender_type->value,
                'area' => $listing->area,
                'address' => $listing->address,
                'description' => $listing->description,
                'rules' => $listing->rules,
                'starting_price' => $listing->starting_price,
                'formatted_starting_price' => $listing->formatted_starting_price,
                'rating_avg' => $listing->rating_avg,
                'review_count' => $listing->review_count,
                'facilities' => $listing->facilities->pluck('name')->values()->all(),
                'available_rooms' => $listing->rooms->map(fn (Room $room): array => [
                    'name' => $room->name,
                    'price_monthly' => $room->price_monthly,
                    'available_rooms' => $room->available_rooms,
                    'size' => $room->size,
                ])->values()->all(),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    private function executeGetCampusGuide(array $arguments): array
    {
        $rawFaculty = isset($arguments['faculty']) && is_string($arguments['faculty']) ? trim($arguments['faculty']) : '';
        $facultyKey = strtoupper($rawFaculty);

        if (isset(self::CAMPUS_FACULTIES[$facultyKey])) {
            $guide = self::CAMPUS_FACULTIES[$facultyKey];

            return [
                'faculty' => $facultyKey,
                'faculty_name' => $guide['name'],
                'nearest_gate' => $guide['nearest_gate'],
                'recommended_areas' => $guide['recommended_areas'],
                'landmarks' => $guide['landmarks'],
                'tips' => $guide['tips'],
                'recommended_listing_ids' => [],
            ];
        }

        return [
            'faculty' => $facultyKey,
            'message' => "Data spesifik untuk fakultas '{$rawFaculty}' tidak ditemukan. Berikut daftar fakultas UNNES yang tersedia.",
            'available_faculties' => array_keys(self::CAMPUS_FACULTIES),
            'nearest_gate' => 'Gerbang Utama UNNES Sekaran',
            'recommended_areas' => ['Sekaran', 'Banaran', 'Patemon', 'Gunungpati'],
            'recommended_listing_ids' => [],
        ];
    }
}
