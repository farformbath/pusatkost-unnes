<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus as BookingPaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\ListingImage;
use App\Domain\Listing\Models\Room;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Domain\Payment\Enums\PayoutStatus;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentReview;
use App\Domain\Payment\Models\Payout;
use App\Domain\Review\Models\Review;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Domain\Wishlist\Models\Wishlist;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        // ====================================================
        // 1. SETUP OWNERS & ADMIN
        // ====================================================
        $admin = User::firstOrCreate(
            ['email' => 'admin@unneskost.com'],
            [
                'name' => 'Admin Pusat Kost UNNES',
                'phone' => '081299998888',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole(UserRole::ADMIN->value);

        $owner1 = User::firstOrCreate(
            ['email' => 'sukardi@unneskost.com'],
            [
                'name' => 'Bapak H. Sukardi (Owner UNNES)',
                'phone' => '081234567890',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner1->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $owner1->save();
        $owner1->assignRole(UserRole::OWNER->value);

        $owner2 = User::firstOrCreate(
            ['email' => 'nurhayati@unneskost.com'],
            [
                'name' => 'Ibu Hj. Nurhayati (Owner Sekaran)',
                'phone' => '081298765432',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner2->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $owner2->save();
        $owner2->assignRole(UserRole::OWNER->value);

        $owner3 = User::firstOrCreate(
            ['email' => 'sugeng@unneskost.com'],
            [
                'name' => 'Bapak Sugeng Riyadi (Owner Banaran)',
                'phone' => '081345678901',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner3->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $owner3->save();
        $owner3->assignRole(UserRole::OWNER->value);

        $owner4 = User::firstOrCreate(
            ['email' => 'bambang.wijaya@unneskost.com'],
            [
                'name' => 'Bapak Bambang Wijaya (Owner Sampangan & Menoreh)',
                'phone' => '081211223344',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner4->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $owner4->save();
        $owner4->assignRole(UserRole::OWNER->value);

        $owner5 = User::firstOrCreate(
            ['email' => 'sri.wahyuni@unneskost.com'],
            [
                'name' => 'Ibu Dra. Sri Wahyuni (Owner Gunungpati)',
                'phone' => '081322334455',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner5->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $owner5->save();
        $owner5->assignRole(UserRole::OWNER->value);

        $owner6 = User::firstOrCreate(
            ['email' => 'dwi.santoso@unneskost.com'],
            [
                'name' => 'Bapak Dwi Santoso (Owner Kalisegoro)',
                'phone' => '081399887766',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner6->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $owner6->save();
        $owner6->assignRole(UserRole::OWNER->value);

        $owner7 = User::firstOrCreate(
            ['email' => 'retno.handayani@unneskost.com'],
            [
                'name' => 'Ibu Retno Handayani, M.Pd (Owner Patemon & Tamsis)',
                'phone' => '081366554433',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $owner7->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $owner7->save();
        $owner7->assignRole(UserRole::OWNER->value);

        // Pending Owner for testing Admin verification flow
        $ownerPending = User::firstOrCreate(
            ['email' => 'hendra.kurnia@unneskost.com'],
            [
                'name' => 'Bapak Hendra Kurnia (Owner Baru - Pending Verifikasi)',
                'phone' => '081388776655',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $ownerPending->owner_verification_status = OwnerVerificationStatus::PENDING;
        $ownerPending->save();
        $ownerPending->assignRole(UserRole::OWNER->value);

        // ====================================================
        // 2. SETUP TENANTS (MAHASISWA UNNES)
        // ====================================================
        $tenant1 = User::firstOrCreate(
            ['email' => 'anisa.rahma@students.unnes.ac.id'],
            [
                'name' => 'Anisa Rahmawati (FBS UNNES)',
                'phone' => '081333444555',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant1->assignRole(UserRole::TENANT->value);

        $tenant2 = User::firstOrCreate(
            ['email' => 'dimas.pratama@students.unnes.ac.id'],
            [
                'name' => 'Dimas Pratama (FT UNNES)',
                'phone' => '081777888999',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant2->assignRole(UserRole::TENANT->value);

        $tenant3 = User::firstOrCreate(
            ['email' => 'rizky.fauzi@students.unnes.ac.id'],
            [
                'name' => 'Rizky Fauzi (FEB UNNES)',
                'phone' => '081555666777',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant3->assignRole(UserRole::TENANT->value);

        $tenant4 = User::firstOrCreate(
            ['email' => 'siti.nurhaliza@students.unnes.ac.id'],
            [
                'name' => 'Siti Nurhaliza (FMIPA UNNES)',
                'phone' => '081888999000',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant4->assignRole(UserRole::TENANT->value);

        $tenant5 = User::firstOrCreate(
            ['email' => 'budi.santoso@students.unnes.ac.id'],
            [
                'name' => 'Budi Santoso (FIP UNNES)',
                'phone' => '081233445566',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant5->assignRole(UserRole::TENANT->value);

        $tenant6 = User::firstOrCreate(
            ['email' => 'maya.anggraini@students.unnes.ac.id'],
            [
                'name' => 'Maya Anggraini (FH UNNES)',
                'phone' => '081344556677',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant6->assignRole(UserRole::TENANT->value);

        $tenant7 = User::firstOrCreate(
            ['email' => 'bagas.kurniawan@students.unnes.ac.id'],
            [
                'name' => 'Bagas Kurniawan (FIK UNNES)',
                'phone' => '081655667788',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant7->assignRole(UserRole::TENANT->value);

        $tenant8 = User::firstOrCreate(
            ['email' => 'cindy.claudia@students.unnes.ac.id'],
            [
                'name' => 'Cindy Claudia (FIS UNNES)',
                'phone' => '081766778899',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant8->assignRole(UserRole::TENANT->value);

        $tenant9 = User::firstOrCreate(
            ['email' => 'farhan.ramadhan@students.unnes.ac.id'],
            [
                'name' => 'Farhan Ramadhan (Kedokteran UNNES)',
                'phone' => '081877889900',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant9->assignRole(UserRole::TENANT->value);

        $tenant10 = User::firstOrCreate(
            ['email' => 'nabila.putri@students.unnes.ac.id'],
            [
                'name' => 'Nabila Putri (Farmasi UNNES)',
                'phone' => '081988990011',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $tenant10->assignRole(UserRole::TENANT->value);

        $facilities = Facility::all();

        // Helper for seeding rich photo galleries
        $seedListingGallery = function (Listing $listing, string $mainRoom, string $altRoom, string $facade = 'images/rooms/facade_1.jpg'): void {
            $listing->images()->delete();

            $images = [
                ['path' => $mainRoom, 'is_primary' => true, 'sort' => 1],
                ['path' => $facade, 'is_primary' => false, 'sort' => 2],
                ['path' => 'images/rooms/bathroom_1.jpg', 'is_primary' => false, 'sort' => 3],
                ['path' => 'images/rooms/communal_1.jpg', 'is_primary' => false, 'sort' => 4],
                ['path' => $altRoom, 'is_primary' => false, 'sort' => 5],
            ];

            foreach ($images as $img) {
                ListingImage::create([
                    'listing_id' => $listing->id,
                    'file_path' => $img['path'],
                    'is_primary' => $img['is_primary'],
                    'sort_order' => $img['sort'],
                ]);
            }
        };

        // ====================================================
        // 3. SEED LISTINGS (19 LISTINGS ACROSS ALL UNNES REGIONS)
        // ====================================================

        // ----------------------------------------------------
        // 1. Sekaran - Putri: Kost Putri Melati Sekaran
        // ----------------------------------------------------
        $listing1 = Listing::firstOrCreate(
            ['slug' => 'kost-putri-melati-sekaran'],
            [
                'owner_id' => $owner1->id,
                'name' => 'Kost Putri Melati Sekaran',
                'gender_type' => GenderType::PUTRI,
                'description' => "Kost putri asri, aman, dan nyaman berjarak hanya 300 meter dari Rektorat dan Fakultas Bahasa dan Seni (FBS) UNNES.\n\nLingkungan tenang untuk belajar, dilengkapi penjaga 24 jam dan gerbang otomatis.",
                'address' => 'Jl. Taman Siswa Gang Melati No. 12, Sekaran, Gunungpati',
                'area' => 'Sekaran',
                'latitude' => -7.0498,
                'longitude' => 110.3950,
                'rules' => "1. Jam malam pukul 23.00 WIB.\n2. Tamu laki-laki dilarang masuk kamar, hanya diperbolehkan di ruang tamu utama.\n3. Menjaga ketenangan dan kebersihan bersama.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.8,
                'review_count' => 14,
            ]
        );
        $listing1->update(['latitude' => -7.0498, 'longitude' => 110.3950]);
        $listing1->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'dapur-bersama', 'cctv-keamanan', 'akses-24-jam'])->pluck('id')
        );
        $seedListingGallery($listing1, 'images/rooms/room_1.jpg', 'images/rooms/room_3.jpg');

        $room1A = Room::firstOrCreate(
            ['listing_id' => $listing1->id, 'name' => 'Kamar Tipe A (Kamar Mandi Dalam + AC)'],
            [
                'size' => '3x4 m',
                'price_monthly' => 950000,
                'price_deposit' => 200000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 3,
            ]
        );
        $room1A->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );
        $room1B = Room::firstOrCreate(
            ['listing_id' => $listing1->id, 'name' => 'Kamar Tipe B (Kamar Mandi Luar)'],
            [
                'size' => '3x3 m',
                'price_monthly' => 700000,
                'price_deposit' => 100000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 6,
                'available_rooms' => 2,
            ]
        );
        $room1B->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 2. Sekaran - Putra: Kost Putra Anugerah Sekaran
        // ----------------------------------------------------
        $listing2 = Listing::firstOrCreate(
            ['slug' => 'kost-putra-anugerah-sekaran'],
            [
                'owner_id' => $owner1->id,
                'name' => 'Kost Putra Anugerah Sekaran',
                'gender_type' => GenderType::PUTRA,
                'description' => "Kost putra nyaman dan tenang di Jl. Gang Pete Sekaran. Hanya 5 menit jalan kaki ke Gedung Serba Guna (GSG) dan Bank BNI UNNES.\n\nCocok untuk mahasiswa yang aktif berorganisasi maupun riset akademik. Kamar luas dengan sirkulasi udara sejuk.",
                'address' => 'Jl. Gang Pete No. 15, Sekaran, Gunungpati',
                'area' => 'Sekaran',
                'latitude' => -7.0482,
                'longitude' => 110.3935,
                'rules' => "1. Bebas jam malam dengan kunci gerbang masing-masing.\n2. Dilarang merokok di area lorong kamar.\n3. Menjaga ketertiban lingkungan sekitar warga.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.7,
                'review_count' => 11,
            ]
        );
        $listing2->update(['latitude' => -7.0482, 'longitude' => 110.3935]);
        $listing2->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'cctv-keamanan', 'akses-24-jam', 'listrik-termasuk-free-listrik'])->pluck('id')
        );
        $seedListingGallery($listing2, 'images/rooms/room_2.jpg', 'images/rooms/room_1.jpg');

        $room2A = Room::firstOrCreate(
            ['listing_id' => $listing2->id, 'name' => 'Kamar Regular Putra Gang Pete'],
            [
                'size' => '3x3 m',
                'price_monthly' => 650000,
                'price_deposit' => 100000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 10,
                'available_rooms' => 4,
            ]
        );
        $room2A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room2B = Room::firstOrCreate(
            ['listing_id' => $listing2->id, 'name' => 'Kamar VIP Putra (KM Dalam)'],
            [
                'size' => '3.5x4 m',
                'price_monthly' => 850000,
                'price_deposit' => 150000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 4,
                'available_rooms' => 1,
            ]
        );
        $room2B->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 3. Sekaran - Campur: Kost Campur Sekaran Central Residence
        // ----------------------------------------------------
        $listing3 = Listing::firstOrCreate(
            ['slug' => 'kost-campur-sekaran-central-residence'],
            [
                'owner_id' => $owner2->id,
                'name' => 'Kost Campur Sekaran Central Residence',
                'gender_type' => GenderType::CAMPUR,
                'description' => "Hunian kos modern di jalur utama Sekaran dekat bundaran UNNES. Gedung 3 lantai dengan konsep co-living mahasiswa dan fresh graduate.\n\nFasilitas komplit dengan rooftop santai dan view perbukitan Gunung Ungaran.",
                'address' => 'Jl. Raya Sekaran No. 102, Sekaran, Gunungpati',
                'area' => 'Sekaran',
                'latitude' => -7.0491,
                'longitude' => 110.3962,
                'rules' => "1. Wajib menunjukkan kartu identitas sah (KTP/KTM).\n2. Dilarang membawa minuman keras dan narkotika.\n3. Tamu menginap wajib konfirmasi pengelola kos.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.9,
                'review_count' => 18,
            ]
        );
        $listing3->update(['latitude' => -7.0491, 'longitude' => 110.3962]);
        $listing3->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-mobil', 'parkir-motor-luas', 'cctv-keamanan', 'dapur-bersama', 'mesin-cuci-bersama', 'akses-24-jam'])->pluck('id')
        );
        $seedListingGallery($listing3, 'images/rooms/room_3.jpg', 'images/rooms/room_2.jpg');

        $room3A = Room::firstOrCreate(
            ['listing_id' => $listing3->id, 'name' => 'Studio Deluxe AC + KM Dalam'],
            [
                'size' => '4x4 m',
                'price_monthly' => 1350000,
                'price_deposit' => 300000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 6,
                'available_rooms' => 2,
            ]
        );
        $room3A->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room3B = Room::firstOrCreate(
            ['listing_id' => $listing3->id, 'name' => 'Kamar Standar AC'],
            [
                'size' => '3x3.5 m',
                'price_monthly' => 1050000,
                'price_deposit' => 200000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 6,
                'available_rooms' => 3,
            ]
        );
        $room3B->facilities()->sync(
            $facilities->whereIn('slug', ['ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 4. Banaran - Putra: Kost Putra Banaran Mandiri
        // ----------------------------------------------------
        $listing4 = Listing::firstOrCreate(
            ['slug' => 'kost-putra-banaran-mandiri'],
            [
                'owner_id' => $owner3->id,
                'name' => 'Kost Putra Banaran Mandiri',
                'gender_type' => GenderType::PUTRA,
                'description' => "Kost putra bersih dan strategis, hanya 5 menit jalan kaki ke Fakultas Teknik (FT) dan Fakultas Matematika & IPA (FMIPA) UNNES.\n\nFree listrik dan air, parkir motor luas berkanopi.",
                'address' => 'Jl. Raya Banaran No. 45, Banaran, Gunungpati',
                'area' => 'Banaran',
                'latitude' => -7.0520,
                'longitude' => 110.3985,
                'rules' => "1. Bebas jam malam (kunci gerbang masing-masing).\n2. Dilarang membawa miras dan narkoba.\n3. Menjaga kebersihan dapur dan tempat parkir.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.6,
                'review_count' => 9,
            ]
        );
        $listing4->update(['latitude' => -7.0520, 'longitude' => 110.3985]);
        $listing4->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'listrik-termasuk-free-listrik', 'akses-24-jam'])->pluck('id')
        );
        $seedListingGallery($listing4, 'images/rooms/room_2.jpg', 'images/rooms/room_1.jpg');

        $room4A = Room::firstOrCreate(
            ['listing_id' => $listing4->id, 'name' => 'Kamar Standar Putra Banaran'],
            [
                'size' => '3x3 m',
                'price_monthly' => 600000,
                'price_deposit' => 0,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 10,
                'available_rooms' => 4,
            ]
        );
        $room4A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 5. Banaran - Putri: Kost Putri Banaran Harmoni
        // ----------------------------------------------------
        $listing5 = Listing::firstOrCreate(
            ['slug' => 'kost-putri-banaran-harmoni'],
            [
                'owner_id' => $owner3->id,
                'name' => 'Kost Putri Banaran Harmoni',
                'gender_type' => GenderType::PUTRI,
                'description' => "Kost putri khusus mahasiswi UNNES di jantung Banaran. Sangat dekat dengan Fakultas Teknik dan FMIPA (hanya 200 meter).\n\nDilengkapi sistem gerbang kartu akses, ruang belajar bersama ber-AC, dan pantry modern di setiap lantai.",
                'address' => 'Jl. Raya Banaran No. 82, Banaran, Gunungpati',
                'area' => 'Banaran',
                'latitude' => -7.0535,
                'longitude' => 110.3995,
                'rules' => "1. Jam malam 22.30 WIB.\n2. Tamu pria hanya di ruang tamu depan.\n3. Menjaga kebersihan bersama pantry dan jemuran.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.8,
                'review_count' => 13,
            ]
        );
        $listing5->update(['latitude' => -7.0535, 'longitude' => 110.3995]);
        $listing5->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'dapur-bersama', 'cctv-keamanan', 'mesin-cuci-bersama'])->pluck('id')
        );
        $seedListingGallery($listing5, 'images/rooms/room_1.jpg', 'images/rooms/room_2.jpg');

        $room5A = Room::firstOrCreate(
            ['listing_id' => $listing5->id, 'name' => 'Kamar Melati KM Luar'],
            [
                'size' => '3x3 m',
                'price_monthly' => 750000,
                'price_deposit' => 100000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 3,
            ]
        );
        $room5A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room5B = Room::firstOrCreate(
            ['listing_id' => $listing5->id, 'name' => 'Kamar Mawar KM Dalam + AC'],
            [
                'size' => '3x4 m',
                'price_monthly' => 1100000,
                'price_deposit' => 250000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 5,
                'available_rooms' => 2,
            ]
        );
        $room5B->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 6. Banaran - Putra: Kost Putra Teknika Banaran
        // ----------------------------------------------------
        $listing6 = Listing::firstOrCreate(
            ['slug' => 'kost-putra-teknika-banaran'],
            [
                'owner_id' => $owner3->id,
                'name' => 'Kost Putra Teknika Banaran',
                'gender_type' => GenderType::PUTRA,
                'description' => "Kos putra favorit mahasiswa Teknik Elektro, Mesin, dan Sipil UNNES. Dekat laboratorium dan bengkel kampus FT.\n\nWiFi high speed 100 Mbps sangat lancar untuk render 3D, coding, dan download tugas praktikum.",
                'address' => 'Jl. Gang Delima No. 7, Banaran, Gunungpati',
                'area' => 'Banaran',
                'latitude' => -7.0545,
                'longitude' => 110.3970,
                'rules' => "1. Bebas jam malam sopan (akses 24 jam).\n2. Dilarang membuat kebisingan di atas jam 23.00 WIB.\n3. Parkir motor tertata rapi.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.6,
                'review_count' => 8,
            ]
        );
        $listing6->update(['latitude' => -7.0545, 'longitude' => 110.3970]);
        $listing6->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'akses-24-jam', 'listrik-termasuk-free-listrik'])->pluck('id')
        );
        $seedListingGallery($listing6, 'images/rooms/room_2.jpg', 'images/rooms/room_3.jpg');

        $room6A = Room::firstOrCreate(
            ['listing_id' => $listing6->id, 'name' => 'Kamar Hemat Teknika (Penuh)'],
            [
                'size' => '3x3 m',
                'price_monthly' => 550000,
                'price_deposit' => 0,
                'status' => RoomStatus::OCCUPIED,
                'total_rooms' => 8,
                'available_rooms' => 0,
            ]
        );
        $room6A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );
        $room6B = Room::firstOrCreate(
            ['listing_id' => $listing6->id, 'name' => 'Kamar Teknika Plus'],
            [
                'size' => '3.5x3.5 m',
                'price_monthly' => 700000,
                'price_deposit' => 100000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 4,
                'available_rooms' => 2,
            ]
        );
        $room6B->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 7. Taman Siswa - Campur: Kost Campur Taman Siswa Eksklusif
        // ----------------------------------------------------
        $listing7 = Listing::firstOrCreate(
            ['slug' => 'kost-campur-taman-siswa-eksklusif'],
            [
                'owner_id' => $owner2->id,
                'name' => 'Kost Campur Taman Siswa Eksklusif',
                'gender_type' => GenderType::CAMPUR,
                'description' => 'Hunian kos eksklusif dan tenang di jalan protokol Taman Siswa Sekaran. Bangunan baru 2 lantai dengan ventilasi udara maksimal dan fasilitas premium.',
                'address' => 'Jl. Taman Siswa No. 88, Sekaran, Gunungpati',
                'area' => 'Taman Siswa',
                'latitude' => -7.0475,
                'longitude' => 110.3970,
                'rules' => "1. Wajib menunjukkan kartu identitas (KTP/KTM).\n2. Tamu wajib lapor penjaga kos.\n3. Dilarang merokok di dalam kamar ber-AC.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.9,
                'review_count' => 21,
            ]
        );
        $listing7->update(['latitude' => -7.0475, 'longitude' => 110.3970]);
        $listing7->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-mobil', 'parkir-motor-luas', 'mesin-cuci-bersama', 'cctv-keamanan', 'akses-24-jam'])->pluck('id')
        );
        $seedListingGallery($listing7, 'images/rooms/room_3.jpg', 'images/rooms/room_2.jpg');

        $room7A = Room::firstOrCreate(
            ['listing_id' => $listing7->id, 'name' => 'Kamar VIP Queen Bed (KM Dalam + AC)'],
            [
                'size' => '4x4 m',
                'price_monthly' => 1250000,
                'price_deposit' => 300000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 6,
                'available_rooms' => 2,
            ]
        );
        $room7A->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 8. Taman Siswa - Putri: Kost Putri Taman Siswa Graha Mulia
        // ----------------------------------------------------
        $listing8 = Listing::firstOrCreate(
            ['slug' => 'kost-putri-taman-siswa-graha-mulia'],
            [
                'owner_id' => $owner7->id,
                'name' => 'Kost Putri Taman Siswa Graha Mulia',
                'gender_type' => GenderType::PUTRI,
                'description' => "Kost putri elegan berkonsep syariah di kawasan strategis Taman Siswa. Sangat dekat dengan minimarket, apotek, laundry, dan pusat jajanan mahasiswa UNNES.\n\nKeamanan terjamin dengan penjagaan satpam malam dan akses sidik jari.",
                'address' => 'Jl. Taman Siswa Gang Kantil No. 20, Sekaran, Gunungpati',
                'area' => 'Taman Siswa',
                'latitude' => -7.0465,
                'longitude' => 110.3982,
                'rules' => "1. Pintu gerbang utama ditutup pukul 22.30 WIB.\n2. Tamu pria hanya diizinkan di gazebo depan.\n3. Kebersihan dapur bersama dijaga bergantian.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.9,
                'review_count' => 16,
            ]
        );
        $listing8->update(['latitude' => -7.0465, 'longitude' => 110.3982]);
        $listing8->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'cctv-keamanan', 'dapur-bersama', 'mesin-cuci-bersama'])->pluck('id')
        );
        $seedListingGallery($listing8, 'images/rooms/room_1.jpg', 'images/rooms/room_3.jpg');

        $room8A = Room::firstOrCreate(
            ['listing_id' => $listing8->id, 'name' => 'Kamar Kantil KM Dalam'],
            [
                'size' => '3x3.5 m',
                'price_monthly' => 900000,
                'price_deposit' => 150000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 3,
            ]
        );
        $room8A->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room8B = Room::firstOrCreate(
            ['listing_id' => $listing8->id, 'name' => 'Kamar Kantil AC Suite'],
            [
                'size' => '3.5x4 m',
                'price_monthly' => 1200000,
                'price_deposit' => 250000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 4,
                'available_rooms' => 1,
            ]
        );
        $room8B->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 9. Taman Siswa - Putra: Kost Putra Taman Siswa Cendekia
        // ----------------------------------------------------
        $listing9 = Listing::firstOrCreate(
            ['slug' => 'kost-putra-taman-siswa-cendekia'],
            [
                'owner_id' => $owner7->id,
                'name' => 'Kost Putra Taman Siswa Cendekia',
                'gender_type' => GenderType::PUTRA,
                'description' => "Kos putra favorit di jalur Taman Siswa, dekat dengan gerbang utama UNNES dan Bank Mandiri.\n\nTempat parkir motor sangat luas dalam pagar tertutup, dapur bersama lengkap dengan kompor gas dan kulkas 2 pintu.",
                'address' => 'Jl. Taman Siswa No. 34, Sekaran, Gunungpati',
                'area' => 'Taman Siswa',
                'latitude' => -7.0458,
                'longitude' => 110.3965,
                'rules' => "1. Bebas jam malam tertib.\n2. Wajib menjaga kebersihan kamar mandi dan lorong.\n3. Dilarang minum minuman keras.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.6,
                'review_count' => 10,
            ]
        );
        $listing9->update(['latitude' => -7.0458, 'longitude' => 110.3965]);
        $listing9->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'dapur-bersama', 'cctv-keamanan', 'akses-24-jam'])->pluck('id')
        );
        $seedListingGallery($listing9, 'images/rooms/room_2.jpg', 'images/rooms/room_1.jpg');

        $room9A = Room::firstOrCreate(
            ['listing_id' => $listing9->id, 'name' => 'Kamar Cendekia AC'],
            [
                'size' => '3x3.5 m',
                'price_monthly' => 850000,
                'price_deposit' => 150000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 4,
            ]
        );
        $room9A->facilities()->sync(
            $facilities->whereIn('slug', ['ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );
        $room9B = Room::firstOrCreate(
            ['listing_id' => $listing9->id, 'name' => 'Kamar Cendekia Non-AC'],
            [
                'size' => '3x3 m',
                'price_monthly' => 650000,
                'price_deposit' => 50000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 6,
                'available_rooms' => 2,
            ]
        );
        $room9B->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 10. Patemon - Putri: Kost Putri Griya Patemon Asri
        // ----------------------------------------------------
        $listing10 = Listing::firstOrCreate(
            ['slug' => 'kost-putri-griya-patemon-asri'],
            [
                'owner_id' => $owner2->id,
                'name' => 'Kost Putri Griya Patemon Asri',
                'gender_type' => GenderType::PUTRI,
                'description' => 'Kost putri dekat pintu gerbang belakang UNNES Patemon. Suasana sejuk, tenang untuk menyusun skripsi dan belajar kelompok.',
                'address' => 'Jl. Raya Patemon Gang Mawar No. 5, Patemon, Gunungpati',
                'area' => 'Patemon',
                'latitude' => -7.0550,
                'longitude' => 110.3850,
                'rules' => "1. Khusus mahasiswi baik-baik.\n2. Gerbang dikunci pukul 22.30 WIB.\n3. Kebersihan kamar mandi luar dijaga bersama.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.7,
                'review_count' => 8,
            ]
        );
        $listing10->update(['latitude' => -7.0550, 'longitude' => 110.3850]);
        $listing10->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'dapur-bersama', 'air-bersih-pamsumur'])->pluck('id')
        );
        $seedListingGallery($listing10, 'images/rooms/room_1.jpg', 'images/rooms/room_2.jpg');

        $room10A = Room::firstOrCreate(
            ['listing_id' => $listing10->id, 'name' => 'Kamar Patemon Standard'],
            [
                'size' => '3x3 m',
                'price_monthly' => 550000,
                'price_deposit' => 50000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 3,
            ]
        );
        $room10A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 11. Patemon - Campur: Kost Campur Paviliun Patemon Hijau
        // ----------------------------------------------------
        $listing11 = Listing::firstOrCreate(
            ['slug' => 'kost-campur-paviliun-patemon-hijau'],
            [
                'owner_id' => $owner7->id,
                'name' => 'Kost Campur Paviliun Patemon Hijau',
                'gender_type' => GenderType::CAMPUR,
                'description' => "Hunian paviliun asri bernuansa taman tropis di Patemon Baru. Jarak 400 meter dari Fakultas Ilmu Pendidikan (FIP) dan gerbang barat UNNES.\n\nSangat nyaman untuk mahasiswa S1/S2 dan pasangan halal yang menginginkan suasana tenang privat.",
                'address' => 'Jl. Patemon Baru No. 16, Patemon, Gunungpati',
                'area' => 'Patemon',
                'latitude' => -7.0565,
                'longitude' => 110.3830,
                'rules' => "1. Pasangan suami istri wajib melampirkan fotokopi buku nikah.\n2. Menjaga ketenangan dan tidak menyalakan musik keras.\n3. Parkir mobil dan motor wajib di slot masing-masing.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.8,
                'review_count' => 12,
            ]
        );
        $listing11->update(['latitude' => -7.0565, 'longitude' => 110.3830]);
        $listing11->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-mobil', 'parkir-motor-luas', 'cctv-keamanan', 'dapur-bersama', 'air-bersih-pamsumur'])->pluck('id')
        );
        $seedListingGallery($listing11, 'images/rooms/room_3.jpg', 'images/rooms/room_1.jpg');

        $room11A = Room::firstOrCreate(
            ['listing_id' => $listing11->id, 'name' => 'Paviliun Deluxe 1 Bed'],
            [
                'size' => '3.5x4.5 m',
                'price_monthly' => 1000000,
                'price_deposit' => 200000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 6,
                'available_rooms' => 2,
            ]
        );
        $room11A->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room11B = Room::firstOrCreate(
            ['listing_id' => $listing11->id, 'name' => 'Paviliun Studio AC'],
            [
                'size' => '4x4.5 m',
                'price_monthly' => 1300000,
                'price_deposit' => 300000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 3,
                'available_rooms' => 1,
            ]
        );
        $room11B->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 12. Patemon - Putra: Kost Putra Patemon Sejahtera
        // ----------------------------------------------------
        $listing12 = Listing::firstOrCreate(
            ['slug' => 'kost-putra-patemon-sejahtera'],
            [
                'owner_id' => $owner1->id,
                'name' => 'Kost Putra Patemon Sejahtera',
                'gender_type' => GenderType::PUTRA,
                'description' => "Kos putra super hemat dan bersahabat di Patemon. Dekat dengan lapangan desa dan rute angkot kampus.\n\nBiaya sewa sudah termasuk pemakaian listrik, air sumur artesis bening tidak pernah tekor, dan WiFi bersama.",
                'address' => 'Jl. Raya Patemon No. 88, Patemon, Gunungpati',
                'area' => 'Patemon',
                'latitude' => -7.0580,
                'longitude' => 110.3860,
                'rules' => "1. Bebas jam malam sopan (kunci gerbang dipegang masing-masing).\n2. Dilarang membawa barang berbahaya dan miras.\n3. Gotong royong menjaga kebersihan tempat sampah.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.5,
                'review_count' => 7,
            ]
        );
        $listing12->update(['latitude' => -7.0580, 'longitude' => 110.3860]);
        $listing12->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'listrik-termasuk-free-listrik', 'air-bersih-pamsumur'])->pluck('id')
        );
        $seedListingGallery($listing12, 'images/rooms/room_2.jpg', 'images/rooms/room_3.jpg');

        $room12A = Room::firstOrCreate(
            ['listing_id' => $listing12->id, 'name' => 'Kamar Hemat Putra Patemon'],
            [
                'size' => '3x3 m',
                'price_monthly' => 480000,
                'price_deposit' => 0,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 12,
                'available_rooms' => 5,
            ]
        );
        $room12A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 13. Gunungpati - Putri: Kost Putri Gunungpati Green Hill
        // ----------------------------------------------------
        $listing13 = Listing::firstOrCreate(
            ['slug' => 'kost-putri-gunungpati-green-hill'],
            [
                'owner_id' => $owner5->id,
                'name' => 'Kost Putri Gunungpati Green Hill',
                'gender_type' => GenderType::PUTRI,
                'description' => "Hunian mahasiswi di dataran sejuk perbukitan Gunungpati, sangat dekat dengan Fakultas Ilmu Keolahragaan (FIK) dan Fakultas Kedokteran UNNES.\n\nUdara segar bebas polusi, dikelilingi taman rindang dan view perkebunan.",
                'address' => 'Jl. Raya Gunungpati No. 77, Gunungpati, Semarang',
                'area' => 'Gunungpati',
                'latitude' => -7.0620,
                'longitude' => 110.3790,
                'rules' => "1. Jam malam pukul 22.00 WIB.\n2. Dilarang merokok dan membawa hewan peliharaan.\n3. Tamu hanya boleh bertamu di pendopo utama.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.8,
                'review_count' => 15,
            ]
        );
        $listing13->update(['latitude' => -7.0620, 'longitude' => 110.3790]);
        $listing13->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'dapur-bersama', 'cctv-keamanan', 'air-bersih-pamsumur'])->pluck('id')
        );
        $seedListingGallery($listing13, 'images/rooms/room_1.jpg', 'images/rooms/room_3.jpg');

        $room13A = Room::firstOrCreate(
            ['listing_id' => $listing13->id, 'name' => 'Kamar Standard Hillview'],
            [
                'size' => '3x3.5 m',
                'price_monthly' => 750000,
                'price_deposit' => 100000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 3,
            ]
        );
        $room13A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room13B = Room::firstOrCreate(
            ['listing_id' => $listing13->id, 'name' => 'Kamar Executive AC Balcony'],
            [
                'size' => '3.5x4 m',
                'price_monthly' => 1150000,
                'price_deposit' => 200000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 4,
                'available_rooms' => 2,
            ]
        );
        $room13B->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 14. Gunungpati - Putra: Kost Putra Graha Gunungpati Juara
        // ----------------------------------------------------
        $listing14 = Listing::firstOrCreate(
            ['slug' => 'kost-putra-graha-gunungpati-juara'],
            [
                'owner_id' => $owner5->id,
                'name' => 'Kost Putra Graha Gunungpati Juara',
                'gender_type' => GenderType::PUTRA,
                'description' => "Kost putra dekat Stadion Gelora Prof. Dirham dan Kolam Renang FIK UNNES. Favorit mahasiswa atlit dan olahraga yang sering latihan pagi dan sore.\n\nKamar berlantai keramik bersih dengan sirkulasi jendela besar.",
                'address' => 'Jl. Tentara Pelajar No. 12, Gunungpati, Semarang',
                'area' => 'Gunungpati',
                'latitude' => -7.0595,
                'longitude' => 110.3810,
                'rules' => "1. Menjaga kebersihan perlengkapan olahraga dan sepatu.\n2. Bebas jam malam sopan.\n3. Parkir motor wajib dikunci stang.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.6,
                'review_count' => 9,
            ]
        );
        $listing14->update(['latitude' => -7.0595, 'longitude' => 110.3810]);
        $listing14->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'akses-24-jam', 'listrik-termasuk-free-listrik'])->pluck('id')
        );
        $seedListingGallery($listing14, 'images/rooms/room_2.jpg', 'images/rooms/room_1.jpg');

        $room14A = Room::firstOrCreate(
            ['listing_id' => $listing14->id, 'name' => 'Kamar Atlit Standard'],
            [
                'size' => '3x3 m',
                'price_monthly' => 600000,
                'price_deposit' => 50000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 10,
                'available_rooms' => 4,
            ]
        );
        $room14A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room14B = Room::firstOrCreate(
            ['listing_id' => $listing14->id, 'name' => 'Kamar Atlit AC KM Dalam'],
            [
                'size' => '3.5x3.5 m',
                'price_monthly' => 900000,
                'price_deposit' => 150000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 4,
                'available_rooms' => 2,
            ]
        );
        $room14B->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 15. Menoreh - Campur: Kost Campur Menoreh Executive Kostel
        // ----------------------------------------------------
        $listing15 = Listing::firstOrCreate(
            ['slug' => 'kost-campur-menoreh-executive-kostel'],
            [
                'owner_id' => $owner4->id,
                'name' => 'Kost Campur Menoreh Executive Kostel',
                'gender_type' => GenderType::CAMPUR,
                'description' => "Hunian kostel bintang 3 di Jalan Menoreh Raya Sampangan. Akses prima hanya 10 menit ke kampus UNNES Sekaran dan 12 menit ke Simpang Lima Semarang.\n\nDilengkapi fasilitas parkir mobil luas berkanopi, lift, dispenser air minum gratis di setiap lorong, dan security 24 jam.",
                'address' => 'Jl. Menoreh Raya No. 42, Sampangan, Gajahmungkur',
                'area' => 'Menoreh',
                'latitude' => -7.0250,
                'longitude' => 110.3910,
                'rules' => "1. Wajib menitipkan KTP/KTM di pos keamanan saat check-in.\n2. Dilarang merokok di area tertutup ber-AC.\n3. Kebersihan bersama dijaga dengan ketat.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.9,
                'review_count' => 24,
            ]
        );
        $listing15->update(['latitude' => -7.0250, 'longitude' => 110.3910]);
        $listing15->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-mobil', 'parkir-motor-luas', 'cctv-keamanan', 'dapur-bersama', 'mesin-cuci-bersama', 'akses-24-jam'])->pluck('id')
        );
        $seedListingGallery($listing15, 'images/rooms/room_3.jpg', 'images/rooms/room_2.jpg');

        $room15A = Room::firstOrCreate(
            ['listing_id' => $listing15->id, 'name' => 'Suite Kostel King Bed'],
            [
                'size' => '4x5 m',
                'price_monthly' => 1600000,
                'price_deposit' => 350000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 6,
                'available_rooms' => 2,
            ]
        );
        $room15A->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room15B = Room::firstOrCreate(
            ['listing_id' => $listing15->id, 'name' => 'Superior AC + KM Dalam'],
            [
                'size' => '3.5x4 m',
                'price_monthly' => 1250000,
                'price_deposit' => 250000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 10,
                'available_rooms' => 4,
            ]
        );
        $room15B->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 16. Menoreh - Putri: Kost Putri Menoreh Vista
        // ----------------------------------------------------
        $listing16 = Listing::firstOrCreate(
            ['slug' => 'kost-putri-menoreh-vista'],
            [
                'owner_id' => $owner4->id,
                'name' => 'Kost Putri Menoreh Vista',
                'gender_type' => GenderType::PUTRI,
                'description' => "Kost putri bersih dan nyaman di Menoreh Utara Sampangan. Dekat halte Feeder Trans Semarang langsung menuju halte Rektorat UNNES.\n\nKawasan sangat ramai kuliner (dekat Mirota, minimarket, warung makan padang, cafe nugas).",
                'address' => 'Jl. Menoreh Utara V No. 9, Sampangan, Gajahmungkur',
                'area' => 'Menoreh',
                'latitude' => -7.0275,
                'longitude' => 110.3930,
                'rules' => "1. Jam malam 22.30 WIB.\n2. Tamu pria tidak diperkenankan melewati ruang tamu.\n3. Dilarang membawa hewan peliharaan.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.7,
                'review_count' => 10,
            ]
        );
        $listing16->update(['latitude' => -7.0275, 'longitude' => 110.3930]);
        $listing16->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'dapur-bersama', 'cctv-keamanan'])->pluck('id')
        );
        $seedListingGallery($listing16, 'images/rooms/room_1.jpg', 'images/rooms/room_2.jpg');

        $room16A = Room::firstOrCreate(
            ['listing_id' => $listing16->id, 'name' => 'Kamar Vista Bersih'],
            [
                'size' => '3x3 m',
                'price_monthly' => 700000,
                'price_deposit' => 100000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 3,
            ]
        );
        $room16A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 17. Kalisegoro - Campur: Kost Campur Kalisegoro Residence
        // ----------------------------------------------------
        $listing17 = Listing::firstOrCreate(
            ['slug' => 'kost-campur-kalisegoro-residence'],
            [
                'owner_id' => $owner1->id,
                'name' => 'Kost Campur Kalisegoro Residence',
                'gender_type' => GenderType::CAMPUR,
                'description' => 'Kost modern minimalis di kawasan sejuk Kalisegoro. Desain Scandinavian dengan pencahayaan alami dan parkir mobil luas.',
                'address' => 'Jl. Kalisegoro Asri No. 22, Kalisegoro, Gunungpati',
                'area' => 'Kalisegoro',
                'latitude' => -7.0360,
                'longitude' => 110.3740,
                'rules' => "1. Tamu pria/wanita mengikuti aturan kos umum.\n2. Jam tenang mulai pukul 22.00 WIB.\n3. Dilarang hewan peliharaan.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.9,
                'review_count' => 17,
            ]
        );
        $listing17->update(['latitude' => -7.0360, 'longitude' => 110.3740]);
        $listing17->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-mobil', 'parkir-motor-luas', 'cctv-keamanan', 'dapur-bersama', 'mesin-cuci-bersama'])->pluck('id')
        );
        $seedListingGallery($listing17, 'images/rooms/room_3.jpg', 'images/rooms/room_1.jpg');

        $room17A = Room::firstOrCreate(
            ['listing_id' => $listing17->id, 'name' => 'Studio Room AC + KM Dalam'],
            [
                'size' => '4x4 m',
                'price_monthly' => 1400000,
                'price_deposit' => 300000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 3,
            ]
        );
        $room17A->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 18. Kalisegoro - Putri: Kost Putri Kalisegoro Cantik Sejuk
        // ----------------------------------------------------
        $listing18 = Listing::firstOrCreate(
            ['slug' => 'kost-putri-kalisegoro-cantik-sejuk'],
            [
                'owner_id' => $owner6->id,
                'name' => 'Kost Putri Kalisegoro Cantik Sejuk',
                'gender_type' => GenderType::PUTRI,
                'description' => "Kos putri tenang dan teduh di Kalisegoro Permai, dekat Embung UNNES. Sangat cocok bagi mahasiswi tingkat akhir yang butuh konsentrasi tinggi menyelesaikan tugas akhir atau skripsi.\n\nAir sumur pegunungan sangat jernih dan segar.",
                'address' => 'Jl. Kalisegoro Permai No. 14, Kalisegoro, Gunungpati',
                'area' => 'Kalisegoro',
                'latitude' => -7.0345,
                'longitude' => 110.3725,
                'rules' => "1. Jam malam 22.00 WIB.\n2. Wajib menjaga suasana tenang terutama di malam hari.\n3. Dilarang membawa rokok dan vape.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.8,
                'review_count' => 11,
            ]
        );
        $listing18->update(['latitude' => -7.0345, 'longitude' => 110.3725]);
        $listing18->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'dapur-bersama', 'air-bersih-pamsumur'])->pluck('id')
        );
        $seedListingGallery($listing18, 'images/rooms/room_1.jpg', 'images/rooms/room_2.jpg');

        $room18A = Room::firstOrCreate(
            ['listing_id' => $listing18->id, 'name' => 'Kamar Asri Sejuk KM Dalam'],
            [
                'size' => '3x3.5 m',
                'price_monthly' => 750000,
                'price_deposit' => 100000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 8,
                'available_rooms' => 4,
            ]
        );
        $room18A->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar', 'jendela-luar'])->pluck('id')
        );
        $room18B = Room::firstOrCreate(
            ['listing_id' => $listing18->id, 'name' => 'Kamar Sejuk AC'],
            [
                'size' => '3.5x4 m',
                'price_monthly' => 1000000,
                'price_deposit' => 200000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 4,
                'available_rooms' => 1,
            ]
        );
        $room18B->facilities()->sync(
            $facilities->whereIn('slug', ['kamar-mandi-dalam', 'ac-air-conditioner', 'kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ----------------------------------------------------
        // 19. Trangkil - Putra: Kost Putra Garuda Trangkil
        // ----------------------------------------------------
        $listing19 = Listing::firstOrCreate(
            ['slug' => 'kost-putra-garuda-trangkil'],
            [
                'owner_id' => $owner3->id,
                'name' => 'Kost Putra Garuda Trangkil',
                'gender_type' => GenderType::PUTRA,
                'description' => 'Kost putra di kawasan Trangkil Gunungpati, akses mudah ke Sampangan dan kampus UNNES. Kamar luas dengan sirkulasi udara baik.',
                'address' => 'Jl. Trangkil Raya No. 18, Trangkil, Gunungpati',
                'area' => 'Trangkil',
                'latitude' => -7.0440,
                'longitude' => 110.4030,
                'rules' => "1. Bebas jam malam sopan.\n2. Parkir motor rapi di dalam gerbang.\n3. Saling menghormati sesama penghuni.",
                'status' => ListingStatus::PUBLISHED,
                'rating_avg' => 4.5,
                'review_count' => 6,
            ]
        );
        $listing19->update(['latitude' => -7.0440, 'longitude' => 110.4030]);
        $listing19->facilities()->sync(
            $facilities->whereIn('slug', ['wifi-cepat', 'parkir-motor-luas', 'akses-24-jam', 'cctv-keamanan'])->pluck('id')
        );
        $seedListingGallery($listing19, 'images/rooms/room_2.jpg', 'images/rooms/room_3.jpg');

        $room19A = Room::firstOrCreate(
            ['listing_id' => $listing19->id, 'name' => 'Kamar Garuda KM Luar'],
            [
                'size' => '3x3.5 m',
                'price_monthly' => 650000,
                'price_deposit' => 100000,
                'status' => RoomStatus::AVAILABLE,
                'total_rooms' => 6,
                'available_rooms' => 2,
            ]
        );
        $room19A->facilities()->sync(
            $facilities->whereIn('slug', ['kasur-bantal', 'lemari-pakaian', 'meja-kursi-belajar'])->pluck('id')
        );

        // ====================================================
        // 4. REVIEWS & RATINGS (REALISTIC REVIEWS FOR LISTINGS)
        // ====================================================
        Review::firstOrCreate(
            ['user_id' => $tenant1->id, 'listing_id' => $listing1->id],
            [
                'rating' => 5,
                'comment' => 'Kos sangat bersih, aman, dan nyaman untuk belajar. Ibu kos juga sangat ramah dan tanggap kalau ada lampu yang mati. Dekat banget sama kampus FBS tinggal jalan kaki 3 menit!',
                'owner_reply' => 'Terima kasih banyak Mbak Anisa atas ulasannya. Semoga lancar studinya di UNNES ya!',
                'owner_replied_at' => now()->subDays(2),
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant2->id, 'listing_id' => $listing1->id],
            [
                'rating' => 5,
                'comment' => 'Fasilitas sesuai dengan yang tertera di aplikasi. WiFi kencang buat nugas dan parkir motor aman berkanopi.',
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant3->id, 'listing_id' => $listing2->id],
            [
                'rating' => 5,
                'comment' => 'Lokasi top banget buat anak Teknik dan MIPA. Bebas jam malam tapi lingkungan tetap aman dan kondusif.',
                'owner_reply' => 'Terima kasih Mas Rizky, sukses selalu kuliahnya!',
                'owner_replied_at' => now()->subDay(),
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant4->id, 'listing_id' => $listing7->id],
            [
                'rating' => 5,
                'comment' => 'Kamar VIP sangat lega dan AC dingin. Parkir mobil leluasa dan ada mesin cuci bersama yang sangat membantu!',
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant5->id, 'listing_id' => $listing10->id],
            [
                'rating' => 5,
                'comment' => 'Kos Patemon yang sangat adem dan damai. Ibu kos baik sekali suka ngasih cemilan kalau lagi akhir bulan.',
                'owner_reply' => 'Alhamdulillah, terima kasih ya Mas Budi. Semangat terus belajarnya!',
                'owner_replied_at' => now()->subDays(3),
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant6->id, 'listing_id' => $listing8->id],
            [
                'rating' => 5,
                'comment' => 'Dekat banget sama jalan utama Taman Siswa. Kalau lapar tengah malam tinggal jalan kaki ke sentra kuliner. Keamanan mantap!',
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant7->id, 'listing_id' => $listing14->id],
            [
                'rating' => 4,
                'comment' => 'Sangat cocok untuk mahasiswa FIK. Cuma 3 menit sampai lapangan sepakbola dan kolam renang UNNES.',
                'owner_reply' => 'Terima kasih Mas Bagas, salam olahraga!',
                'owner_replied_at' => now()->subDays(5),
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant8->id, 'listing_id' => $listing13->id],
            [
                'rating' => 5,
                'comment' => 'Pemandangannya juara! Udaranya sejuk jadi jarang nyalain AC. Sinyal WiFi stabil dipakai kuliah online dan ngerjain tugas.',
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant9->id, 'listing_id' => $listing15->id],
            [
                'rating' => 5,
                'comment' => 'Konsep kostelnya modern banget. Parkir mobil luas, water heater berfungsi baik, dan ada satpam 24 jam. Sangat puas!',
                'owner_reply' => 'Terima kasih Mas Farhan atas kepercayaan tinggal di Menoreh Executive.',
                'owner_replied_at' => now()->subDays(1),
            ]
        );

        Review::firstOrCreate(
            ['user_id' => $tenant10->id, 'listing_id' => $listing18->id],
            [
                'rating' => 5,
                'comment' => 'Airnya jernih banget, kosnya tenang dan homy. Pas banget buat yang lagi ngerjain skripsi tanpa distraksi.',
            ]
        );

        // ====================================================
        // 5. WISHLIST DATA
        // ====================================================
        Wishlist::firstOrCreate(['user_id' => $tenant1->id, 'listing_id' => $listing7->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant1->id, 'listing_id' => $listing8->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant2->id, 'listing_id' => $listing1->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant2->id, 'listing_id' => $listing6->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant3->id, 'listing_id' => $listing17->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant3->id, 'listing_id' => $listing3->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant4->id, 'listing_id' => $listing5->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant5->id, 'listing_id' => $listing11->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant6->id, 'listing_id' => $listing15->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant7->id, 'listing_id' => $listing2->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant8->id, 'listing_id' => $listing18->id]);
        Wishlist::firstOrCreate(['user_id' => $tenant9->id, 'listing_id' => $listing7->id]);

        // ====================================================
        // 6. BOOKINGS, PAYMENTS, REVIEWS & PAYOUTS WORKFLOW DATA
        // ====================================================

        // Booking 1: APPROVED & PAID (Active, verified & payout complete)
        $booking1 = Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20260901-0001'],
            [
                'user_id' => $tenant1->id,
                'room_id' => $room1A->id,
                'listing_id' => $listing1->id,
                'start_date' => now()->addDays(3)->toDateString(),
                'duration_months' => 6,
                'end_date' => now()->addDays(3)->addMonths(6)->toDateString(),
                'total_amount' => 5700000,
                'deposit_amount' => 200000,
                'grand_total' => 5900000,
                'status' => BookingStatus::APPROVED,
                'payment_status' => BookingPaymentStatus::PAID,
                'tenant_name' => $tenant1->name,
                'tenant_phone' => $tenant1->phone ?? '081333444555',
                'notes' => 'Mahasiswi baru FBS UNNES 2026',
                'expired_at' => now()->addDays(7),
            ]
        );

        $payment1 = Payment::firstOrCreate(
            ['invoice_number' => 'INV-20260901-0001'],
            [
                'booking_id' => $booking1->id,
                'payment_provider' => 'sandbox',
                'payment_channel' => 'bca_va',
                'payment_reference' => 'PAY-REF-20260901-001',
                'provider_transaction_id' => 'TRX-SB-001001',
                'gross_amount' => 5900000,
                'transaction_status' => PaymentStatus::PAID,
                'idempotency_key' => 'IDEM-SEED-001',
                'paid_at' => now()->subDays(5),
                'verified_at' => now()->subDays(4),
            ]
        );

        $review1 = PaymentReview::firstOrCreate(
            ['payment_id' => $payment1->id],
            [
                'booking_id' => $booking1->id,
                'status' => PaymentReviewStatus::APPROVED,
                'tenant_contacted' => true,
                'owner_contacted' => true,
                'tenant_confirmed' => true,
                'owner_confirmed' => true,
                'admin_id' => $admin->id,
                'admin_notes' => 'Penyewa sudah check-in dan kunci kamar diterima dengan baik.',
                'reviewed_at' => now()->subDays(4),
                'approved_at' => now()->subDays(3),
            ]
        );

        Payout::firstOrCreate(
            ['payment_id' => $payment1->id],
            [
                'booking_id' => $booking1->id,
                'owner_id' => $owner1->id,
                'gross_amount' => 5900000,
                'platform_fee' => 50000,
                'net_amount' => 5850000,
                'payout_provider' => 'sandbox',
                'provider_payout_id' => 'PO-SB-001001',
                'status' => PayoutStatus::PAID,
                'idempotency_key' => 'IDEM-PO-001',
                'requested_at' => now()->subDays(3),
                'completed_at' => now()->subDays(2),
            ]
        );

        // Booking 2: APPROVED & PAID (Banaran, paid via Mandiri VA)
        $booking2 = Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20260910-0002'],
            [
                'user_id' => $tenant2->id,
                'room_id' => $room4A->id,
                'listing_id' => $listing4->id,
                'start_date' => now()->addDays(5)->toDateString(),
                'duration_months' => 3,
                'end_date' => now()->addDays(5)->addMonths(3)->toDateString(),
                'total_amount' => 1800000,
                'deposit_amount' => 0,
                'grand_total' => 1800000,
                'status' => BookingStatus::APPROVED,
                'payment_status' => BookingPaymentStatus::PAID,
                'tenant_name' => $tenant2->name,
                'tenant_phone' => $tenant2->phone ?? '081777888999',
                'notes' => 'Mahasiswa FT UNNES angkatan 2024',
                'expired_at' => now()->addDays(5),
            ]
        );

        $payment2 = Payment::firstOrCreate(
            ['invoice_number' => 'INV-20260910-0002'],
            [
                'booking_id' => $booking2->id,
                'payment_provider' => 'sandbox',
                'payment_channel' => 'mandiri_va',
                'payment_reference' => 'PAY-REF-20260910-002',
                'provider_transaction_id' => 'TRX-SB-002002',
                'gross_amount' => 1800000,
                'transaction_status' => PaymentStatus::PAID,
                'idempotency_key' => 'IDEM-SEED-002',
                'paid_at' => now()->subDays(3),
                'verified_at' => now()->subDays(2),
            ]
        );

        PaymentReview::firstOrCreate(
            ['payment_id' => $payment2->id],
            [
                'booking_id' => $booking2->id,
                'status' => PaymentReviewStatus::APPROVED,
                'tenant_contacted' => true,
                'owner_contacted' => true,
                'tenant_confirmed' => true,
                'owner_confirmed' => true,
                'admin_id' => $admin->id,
                'admin_notes' => 'Konfirmasi via WhatsApp selesai, owner sudah memberikan akses.',
                'reviewed_at' => now()->subDays(2),
                'approved_at' => now()->subDay(),
            ]
        );

        Payout::firstOrCreate(
            ['payment_id' => $payment2->id],
            [
                'booking_id' => $booking2->id,
                'owner_id' => $owner3->id,
                'gross_amount' => 1800000,
                'platform_fee' => 25000,
                'net_amount' => 1775000,
                'payout_provider' => 'sandbox',
                'provider_payout_id' => 'PO-SB-002002',
                'status' => PayoutStatus::PAID,
                'idempotency_key' => 'IDEM-PO-002',
                'requested_at' => now()->subDay(),
                'completed_at' => now()->subHours(12),
            ]
        );

        // Booking 3: PAID, READY FOR ADMIN REVIEW (Pending Admin verification)
        $booking3 = Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20260915-0003'],
            [
                'user_id' => $tenant3->id,
                'room_id' => $room7A->id,
                'listing_id' => $listing7->id,
                'start_date' => now()->addDays(2)->toDateString(),
                'duration_months' => 1,
                'end_date' => now()->addDays(2)->addMonth()->toDateString(),
                'total_amount' => 1250000,
                'deposit_amount' => 300000,
                'grand_total' => 1550000,
                'status' => BookingStatus::APPROVED,
                'payment_status' => BookingPaymentStatus::PAID,
                'tenant_name' => $tenant3->name,
                'tenant_phone' => $tenant3->phone ?? '081555666777',
                'notes' => 'Pembayaran lunas via QRIS, menunggu verifikasi checklist admin',
                'expired_at' => now()->addDays(3),
            ]
        );

        $payment3 = Payment::firstOrCreate(
            ['invoice_number' => 'INV-20260915-0003'],
            [
                'booking_id' => $booking3->id,
                'payment_provider' => 'sandbox',
                'payment_channel' => 'qris',
                'payment_reference' => 'PAY-REF-20260915-003',
                'provider_transaction_id' => 'TRX-SB-003003',
                'gross_amount' => 1550000,
                'transaction_status' => PaymentStatus::PAID,
                'idempotency_key' => 'IDEM-SEED-003',
                'paid_at' => now()->subHours(6),
                'verified_at' => null,
            ]
        );

        PaymentReview::firstOrCreate(
            ['payment_id' => $payment3->id],
            [
                'booking_id' => $booking3->id,
                'status' => PaymentReviewStatus::PENDING,
                'tenant_contacted' => false,
                'owner_contacted' => false,
                'tenant_confirmed' => false,
                'owner_confirmed' => false,
                'admin_id' => null,
                'admin_notes' => null,
            ]
        );

        // Booking 4: PENDING APPROVAL (Waiting owner response)
        Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20260920-0004'],
            [
                'user_id' => $tenant4->id,
                'room_id' => $room19A->id,
                'listing_id' => $listing19->id,
                'start_date' => now()->addDays(7)->toDateString(),
                'duration_months' => 3,
                'end_date' => now()->addDays(7)->addMonths(3)->toDateString(),
                'total_amount' => 1950000,
                'deposit_amount' => 100000,
                'grand_total' => 2050000,
                'status' => BookingStatus::PENDING,
                'payment_status' => BookingPaymentStatus::PENDING,
                'tenant_name' => $tenant4->name,
                'tenant_phone' => $tenant4->phone ?? '081888999000',
                'notes' => 'Menunggu konfirmasi ketersediaan kamar dari bapak pengelola',
                'expired_at' => now()->addDays(2),
            ]
        );

        // Booking 5: APPROVED, WAITING PAYMENT (Tenant Budi)
        Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20260925-0005'],
            [
                'user_id' => $tenant5->id,
                'room_id' => $room2A->id,
                'listing_id' => $listing2->id,
                'start_date' => now()->addDays(10)->toDateString(),
                'duration_months' => 6,
                'end_date' => now()->addDays(10)->addMonths(6)->toDateString(),
                'total_amount' => 3900000,
                'deposit_amount' => 100000,
                'grand_total' => 4000000,
                'status' => BookingStatus::APPROVED,
                'payment_status' => BookingPaymentStatus::PENDING,
                'tenant_name' => $tenant5->name,
                'tenant_phone' => $tenant5->phone ?? '081233445566',
                'notes' => 'Invoice diterbitkan, menunggu pembayaran dari penyewa',
                'expired_at' => now()->addDay(),
            ]
        );

        // Booking 6: VERIFIED, READY FOR PAYOUT (Graha Mulia)
        $booking6 = Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20261001-0006'],
            [
                'user_id' => $tenant6->id,
                'room_id' => $room8A->id,
                'listing_id' => $listing8->id,
                'start_date' => now()->addDays(4)->toDateString(),
                'duration_months' => 12,
                'end_date' => now()->addDays(4)->addYear()->toDateString(),
                'total_amount' => 10800000,
                'deposit_amount' => 150000,
                'grand_total' => 10950000,
                'status' => BookingStatus::APPROVED,
                'payment_status' => BookingPaymentStatus::PAID,
                'tenant_name' => $tenant6->name,
                'tenant_phone' => $tenant6->phone ?? '081344556677',
                'notes' => 'Sewa tahunan mahasiswi FH UNNES',
                'expired_at' => now()->addDays(5),
            ]
        );

        $payment6 = Payment::firstOrCreate(
            ['invoice_number' => 'INV-20261001-0006'],
            [
                'booking_id' => $booking6->id,
                'payment_provider' => 'sandbox',
                'payment_channel' => 'bni_va',
                'payment_reference' => 'PAY-REF-20261001-006',
                'provider_transaction_id' => 'TRX-SB-006006',
                'gross_amount' => 10950000,
                'transaction_status' => PaymentStatus::PAID,
                'idempotency_key' => 'IDEM-SEED-006',
                'paid_at' => now()->subDay(),
                'verified_at' => now()->subHours(10),
            ]
        );

        PaymentReview::firstOrCreate(
            ['payment_id' => $payment6->id],
            [
                'booking_id' => $booking6->id,
                'status' => PaymentReviewStatus::VERIFIED,
                'tenant_contacted' => true,
                'owner_contacted' => true,
                'tenant_confirmed' => true,
                'owner_confirmed' => true,
                'admin_id' => $admin->id,
                'admin_notes' => 'Kedua pihak sudah terhubung dan konfirmasi serah terima kunci disetujui.',
                'reviewed_at' => now()->subHours(10),
            ]
        );

        Payout::firstOrCreate(
            ['payment_id' => $payment6->id],
            [
                'booking_id' => $booking6->id,
                'owner_id' => $owner7->id,
                'gross_amount' => 10950000,
                'platform_fee' => 100000,
                'net_amount' => 10850000,
                'payout_provider' => 'sandbox',
                'provider_payout_id' => 'PO-SB-006006',
                'status' => PayoutStatus::PENDING,
                'idempotency_key' => 'IDEM-PO-006',
                'requested_at' => now()->subHours(8),
            ]
        );

        // Booking 7: CANCELLED (Tenant Bagas)
        Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20261005-0007'],
            [
                'user_id' => $tenant7->id,
                'room_id' => $room14A->id,
                'listing_id' => $listing14->id,
                'start_date' => now()->addDays(14)->toDateString(),
                'duration_months' => 1,
                'end_date' => now()->addDays(14)->addMonth()->toDateString(),
                'total_amount' => 600000,
                'deposit_amount' => 50000,
                'grand_total' => 650000,
                'status' => BookingStatus::CANCELLED,
                'payment_status' => BookingPaymentStatus::EXPIRED,
                'tenant_name' => $tenant7->name,
                'tenant_phone' => $tenant7->phone ?? '081655667788',
                'notes' => 'Pemesanan dibatalkan karena pindah kampus cabang',
                'expired_at' => now()->subDay(),
            ]
        );

        // Booking 8: CONTACTING STATUS REVIEW (Cindy Claudia)
        $booking8 = Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20261008-0008'],
            [
                'user_id' => $tenant8->id,
                'room_id' => $room13A->id,
                'listing_id' => $listing13->id,
                'start_date' => now()->addDays(3)->toDateString(),
                'duration_months' => 3,
                'end_date' => now()->addDays(3)->addMonths(3)->toDateString(),
                'total_amount' => 2250000,
                'deposit_amount' => 100000,
                'grand_total' => 2350000,
                'status' => BookingStatus::APPROVED,
                'payment_status' => BookingPaymentStatus::PAID,
                'tenant_name' => $tenant8->name,
                'tenant_phone' => $tenant8->phone ?? '081766778899',
                'notes' => 'Mahasiswi FIS semester 3',
                'expired_at' => now()->addDays(3),
            ]
        );

        $payment8 = Payment::firstOrCreate(
            ['invoice_number' => 'INV-20261008-0008'],
            [
                'booking_id' => $booking8->id,
                'payment_provider' => 'sandbox',
                'payment_channel' => 'bca_va',
                'payment_reference' => 'PAY-REF-20261008-008',
                'provider_transaction_id' => 'TRX-SB-008008',
                'gross_amount' => 2350000,
                'transaction_status' => PaymentStatus::PAID,
                'idempotency_key' => 'IDEM-SEED-008',
                'paid_at' => now()->subHours(12),
                'verified_at' => null,
            ]
        );

        PaymentReview::firstOrCreate(
            ['payment_id' => $payment8->id],
            [
                'booking_id' => $booking8->id,
                'status' => PaymentReviewStatus::CONTACTING,
                'tenant_contacted' => true,
                'owner_contacted' => false,
                'tenant_confirmed' => true,
                'owner_confirmed' => false,
                'admin_id' => $admin->id,
                'admin_notes' => 'Tenant sudah konfirmasi, sedang menunggu respon konfirmasi dari Ibu Sri Wahyuni.',
                'reviewed_at' => now()->subHours(6),
            ]
        );

        // Booking 9: APPROVED & PAID (Menoreh Executive Kostel)
        $booking9 = Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20261011-0009'],
            [
                'user_id' => $tenant9->id,
                'room_id' => $room15A->id,
                'listing_id' => $listing15->id,
                'start_date' => now()->addDays(1)->toDateString(),
                'duration_months' => 6,
                'end_date' => now()->addDays(1)->addMonths(6)->toDateString(),
                'total_amount' => 9600000,
                'deposit_amount' => 350000,
                'grand_total' => 9950000,
                'status' => BookingStatus::APPROVED,
                'payment_status' => BookingPaymentStatus::PAID,
                'tenant_name' => $tenant9->name,
                'tenant_phone' => $tenant9->phone ?? '081877889900',
                'notes' => 'Koas Kedokteran UNNES',
                'expired_at' => now()->addDays(4),
            ]
        );

        $payment9 = Payment::firstOrCreate(
            ['invoice_number' => 'INV-20261011-0009'],
            [
                'booking_id' => $booking9->id,
                'payment_provider' => 'sandbox',
                'payment_channel' => 'mandiri_va',
                'payment_reference' => 'PAY-REF-20261011-009',
                'provider_transaction_id' => 'TRX-SB-009009',
                'gross_amount' => 9950000,
                'transaction_status' => PaymentStatus::PAID,
                'idempotency_key' => 'IDEM-SEED-009',
                'paid_at' => now()->subDays(2),
                'verified_at' => now()->subDay(),
            ]
        );

        PaymentReview::firstOrCreate(
            ['payment_id' => $payment9->id],
            [
                'booking_id' => $booking9->id,
                'status' => PaymentReviewStatus::APPROVED,
                'tenant_contacted' => true,
                'owner_contacted' => true,
                'tenant_confirmed' => true,
                'owner_confirmed' => true,
                'admin_id' => $admin->id,
                'admin_notes' => 'Semua checklist terpenuhi.',
                'reviewed_at' => now()->subDay(),
                'approved_at' => now()->subHours(18),
            ]
        );

        Payout::firstOrCreate(
            ['payment_id' => $payment9->id],
            [
                'booking_id' => $booking9->id,
                'owner_id' => $owner4->id,
                'gross_amount' => 9950000,
                'platform_fee' => 75000,
                'net_amount' => 9875000,
                'payout_provider' => 'sandbox',
                'provider_payout_id' => 'PO-SB-009009',
                'status' => PayoutStatus::PAID,
                'idempotency_key' => 'IDEM-PO-009',
                'requested_at' => now()->subHours(18),
                'completed_at' => now()->subHours(14),
            ]
        );

        // Booking 10: VERIFIED STATUS (Nabila Putri)
        $booking10 = Booking::firstOrCreate(
            ['booking_code' => 'UNNES-20261014-0010'],
            [
                'user_id' => $tenant10->id,
                'room_id' => $room18A->id,
                'listing_id' => $listing18->id,
                'start_date' => now()->addDays(6)->toDateString(),
                'duration_months' => 6,
                'end_date' => now()->addDays(6)->addMonths(6)->toDateString(),
                'total_amount' => 4500000,
                'deposit_amount' => 100000,
                'grand_total' => 4600000,
                'status' => BookingStatus::APPROVED,
                'payment_status' => BookingPaymentStatus::PAID,
                'tenant_name' => $tenant10->name,
                'tenant_phone' => $tenant10->phone ?? '081988990011',
                'notes' => 'Mahasiswi Farmasi UNNES',
                'expired_at' => now()->addDays(5),
            ]
        );

        $payment10 = Payment::firstOrCreate(
            ['invoice_number' => 'INV-20261014-0010'],
            [
                'booking_id' => $booking10->id,
                'payment_provider' => 'sandbox',
                'payment_channel' => 'qris',
                'payment_reference' => 'PAY-REF-20261014-010',
                'provider_transaction_id' => 'TRX-SB-010010',
                'gross_amount' => 4600000,
                'transaction_status' => PaymentStatus::PAID,
                'idempotency_key' => 'IDEM-SEED-010',
                'paid_at' => now()->subHours(5),
                'verified_at' => now()->subHours(2),
            ]
        );

        PaymentReview::firstOrCreate(
            ['payment_id' => $payment10->id],
            [
                'booking_id' => $booking10->id,
                'status' => PaymentReviewStatus::VERIFIED,
                'tenant_contacted' => true,
                'owner_contacted' => true,
                'tenant_confirmed' => true,
                'owner_confirmed' => true,
                'admin_id' => $admin->id,
                'admin_notes' => 'Siap pencairan payout ke pemilik kos.',
                'reviewed_at' => now()->subHours(2),
            ]
        );
    }
}
