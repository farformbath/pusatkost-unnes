<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ListingSearchFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::query()->create([
            'name' => 'Owner Test',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_can_search_listings_via_http_endpoint(): void
    {
        $listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Putri Melati UNNES',
            'slug' => 'kost-putri-melati-unnes',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran Raya No. 10',
            'description' => 'Kost khusus mahasiswi UNNES',
        ]);

        Room::query()->create([
            'listing_id' => $listing->id,
            'name' => 'Kamar Utama',
            'size' => '3x3',
            'price_monthly' => 800000,
            'price_deposit' => 200000,
            'total_rooms' => 2,
            'available_rooms' => 2,
            'status' => RoomStatus::AVAILABLE,
        ]);

        $response = $this->get('/kos?keyword=Melati&gender=putri&area=Sekaran');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Listings/Index')
            ->has('listings.data', 1)
            ->where('listings.data.0.name', 'Kost Putri Melati UNNES')
            ->where('filters.keyword', 'Melati')
            ->where('filters.gender', 'putri')
            ->where('filters.area', 'Sekaran')
        );
    }

    public function test_can_search_listings_with_multiple_areas(): void
    {
        $listing1 = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Sekaran Asri',
            'slug' => 'kost-sekaran-asri',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran Raya No. 10',
            'description' => 'Kost di Sekaran',
        ]);

        $listing2 = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Banaran Nyaman',
            'slug' => 'kost-banaran-nyaman',
            'area' => 'Banaran',
            'gender_type' => GenderType::PUTRA,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Banaran No. 5',
            'description' => 'Kost di Banaran',
        ]);

        $listing3 = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Patemon Damai',
            'slug' => 'kost-patemon-damai',
            'area' => 'Patemon',
            'gender_type' => GenderType::CAMPUR,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Patemon No. 1',
            'description' => 'Kost di Patemon',
        ]);

        foreach ([$listing1, $listing2, $listing3] as $l) {
            Room::query()->create([
                'listing_id' => $l->id,
                'name' => 'Kamar Standard',
                'size' => '3x3',
                'price_monthly' => 700000,
                'price_deposit' => 100000,
                'total_rooms' => 1,
                'available_rooms' => 1,
                'status' => RoomStatus::AVAILABLE,
            ]);
        }

        // Test comma-separated string
        $response = $this->get('/kos?area=Sekaran,Banaran');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Listings/Index')
            ->has('listings.data', 2)
            ->where('filters.areas', ['Sekaran', 'Banaran'])
        );

        // Test array
        $responseArr = $this->get('/kos?areas[]=Sekaran&areas[]=Patemon');
        $responseArr->assertStatus(200);
        $responseArr->assertInertia(fn (Assert $page) => $page
            ->component('Listings/Index')
            ->has('listings.data', 2)
            ->where('filters.areas', ['Sekaran', 'Patemon'])
        );
    }

    public function test_filter_with_empty_strings_and_facility_ids(): void
    {
        $facility = Facility::query()->create([
            'name' => 'AC Testing',
            'slug' => 'ac-testing',
            'icon' => 'air-vent',
            'category' => 'room',
        ]);

        $response = $this->get("/kos?keyword=&gender=&area=&min_price=&max_price=&facility_ids[]={$facility->id}");
        $response->assertStatus(200);
    }

    public function test_can_filter_listings_by_price_and_facilities(): void
    {
        $facility = Facility::query()->create([
            'name' => 'AC',
            'slug' => 'ac',
            'icon' => 'wind',
            'category' => 'room',
        ]);

        $listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Ber-AC Banaran',
            'slug' => 'kost-ber-ac-banaran',
            'area' => 'Banaran',
            'gender_type' => GenderType::PUTRA,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Banaran Raya No. 15',
            'description' => 'Kost berfasilitas AC',
        ]);

        $listing->facilities()->attach($facility->id);

        Room::query()->create([
            'listing_id' => $listing->id,
            'name' => 'Kamar Deluxe',
            'size' => '4x4',
            'price_monthly' => 1200000,
            'price_deposit' => 300000,
            'total_rooms' => 1,
            'available_rooms' => 1,
            'status' => RoomStatus::AVAILABLE,
        ]);

        $response = $this->get("/kos?min_price=1000000&max_price=1500000&facility_ids[]={$facility->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Listings/Index')
            ->has('listings.data', 1)
            ->where('listings.data.0.name', 'Kost Ber-AC Banaran')
        );
    }

    public function test_it_validates_price_range_integrity(): void
    {
        // min_price > max_price -> should fail validation (redirect back with validation errors)
        $response = $this->get('/kos?min_price=2000000&max_price=1000000');
        $response->assertSessionHasErrors(['max_price']);
    }

    public function test_it_prevents_n_plus_one_queries_on_search_index(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $listing = Listing::query()->create([
                'owner_id' => $this->owner->id,
                'name' => "Kost UNNES {$i}",
                'slug' => "kost-unnes-{$i}",
                'area' => 'Sekaran',
                'gender_type' => GenderType::PUTRI,
                'status' => ListingStatus::PUBLISHED,
                'address' => "Jl. Sekaran No {$i}",
                'description' => 'Desc',
            ]);

            Room::query()->create([
                'listing_id' => $listing->id,
                'name' => 'Kamar Standard',
                'size' => '3x3',
                'price_monthly' => 700000,
                'price_deposit' => 100000,
                'total_rooms' => 2,
                'available_rooms' => 2,
                'status' => RoomStatus::AVAILABLE,
            ]);
        }

        $queryCount = 0;
        DB::listen(function () use (&$queryCount): void {
            $queryCount++;
        });

        $response = $this->get('/kos');
        $response->assertStatus(200);

        // Constant predictable query count for index page (count query + listings + relations: facilities, rooms, primaryImage, images)
        $this->assertLessThanOrEqual(10, $queryCount);
    }
}
