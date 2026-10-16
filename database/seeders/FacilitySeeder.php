<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Listing\Models\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            // Room Facilities
            ['name' => 'Kamar Mandi Dalam', 'icon' => 'bath', 'category' => 'room'],
            ['name' => 'AC (Air Conditioner)', 'icon' => 'air-vent', 'category' => 'room'],
            ['name' => 'Kasur & Bantal', 'icon' => 'bed', 'category' => 'room'],
            ['name' => 'Lemari Pakaian', 'icon' => 'wardrobe', 'category' => 'room'],
            ['name' => 'Meja & Kursi Belajar', 'icon' => 'armchair', 'category' => 'room'],
            ['name' => 'Jendela Luar', 'icon' => 'sun', 'category' => 'room'],

            // Property / General Facilities
            ['name' => 'WiFi Cepat', 'icon' => 'wifi', 'category' => 'property'],
            ['name' => 'Parkir Motor Luas', 'icon' => 'bike', 'category' => 'property'],
            ['name' => 'Parkir Mobil', 'icon' => 'car', 'category' => 'property'],
            ['name' => 'Dapur Bersama', 'icon' => 'utensils', 'category' => 'property'],
            ['name' => 'Mesin Cuci Bersama', 'icon' => 'washing-machine', 'category' => 'property'],
            ['name' => 'Akses 24 Jam', 'icon' => 'clock', 'category' => 'property'],
            ['name' => 'CCTV Keamanan', 'icon' => 'shield-check', 'category' => 'property'],
            ['name' => 'Listrik Termasuk (Free Listrik)', 'icon' => 'zap', 'category' => 'property'],
            ['name' => 'Air Bersih PAM/Sumur', 'icon' => 'droplets', 'category' => 'property'],
        ];

        foreach ($facilities as $facility) {
            Facility::firstOrCreate(
                ['slug' => Str::slug($facility['name'])],
                [
                    'name' => $facility['name'],
                    'icon' => $facility['icon'],
                    'category' => $facility['category'],
                ]
            );
        }
    }
}
