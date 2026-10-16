<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Search;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\Search\Actions\SearchListingsAction;
use App\Domain\Search\DTOs\SearchCriteria;
use App\Domain\Search\Repositories\EloquentSearchRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchListingsActionTest extends TestCase
{
    use RefreshDatabase;

    private SearchListingsAction $action;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new SearchListingsAction(new EloquentSearchRepository);

        $this->owner = User::query()->create([
            'name' => 'Owner Test',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_it_searches_by_name_address_and_case_insensitive(): void
    {
        Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Cantika Sekaran',
            'slug' => 'kost-cantika-sekaran',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Gang Cempaka No. 8',
            'description' => 'Kost putri asri dan tenang',
        ]);

        // Find by name (case-insensitive)
        $resName = $this->action->execute(new SearchCriteria(keyword: 'cantika'));
        $this->assertSame(1, $resName->total());
        $this->assertSame('Kost Cantika Sekaran', $resName->items()[0]->name);

        // Find by address
        $resAddress = $this->action->execute(new SearchCriteria(keyword: 'Cempaka'));
        $this->assertSame(1, $resAddress->total());

        // No result
        $resEmpty = $this->action->execute(new SearchCriteria(keyword: 'nonexistent12345'));
        $this->assertSame(0, $resEmpty->total());
    }

    public function test_it_filters_by_all_genders_and_areas(): void
    {
        Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Putri Sekaran',
            'slug' => 'kost-putri-sekaran',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran',
            'description' => 'Kost putri',
        ]);

        Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Putra Banaran',
            'slug' => 'kost-putra-banaran',
            'area' => 'Banaran',
            'gender_type' => GenderType::PUTRA,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Banaran',
            'description' => 'Kost putra',
        ]);

        Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Campur Patemon',
            'slug' => 'kost-campur-patemon',
            'area' => 'Patemon',
            'gender_type' => GenderType::CAMPUR,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Patemon',
            'description' => 'Kost campur',
        ]);

        $this->assertSame(1, $this->action->execute(new SearchCriteria(gender: GenderType::PUTRI))->total());
        $this->assertSame(1, $this->action->execute(new SearchCriteria(gender: GenderType::PUTRA))->total());
        $this->assertSame(1, $this->action->execute(new SearchCriteria(gender: GenderType::CAMPUR))->total());
        $this->assertSame(1, $this->action->execute(new SearchCriteria(area: 'Banaran'))->total());
    }

    public function test_it_filters_multiple_facilities_with_and_semantics(): void
    {
        $wifi = Facility::query()->create(['name' => 'WiFi Cepat', 'slug' => 'wifi-cepat', 'category' => 'general']);
        $ac = Facility::query()->create(['name' => 'AC', 'slug' => 'ac', 'category' => 'room']);
        $kmDalam = Facility::query()->create(['name' => 'KM Dalam', 'slug' => 'km-dalam', 'category' => 'room']);

        // Listing 1: only WiFi
        $l1 = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost WiFi Saja',
            'slug' => 'kost-wifi-saja',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran 1',
            'description' => 'Desc',
        ]);
        $l1->facilities()->attach([$wifi->id]);

        // Listing 2: WiFi + AC
        $l2 = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost WiFi dan AC',
            'slug' => 'kost-wifi-dan-ac',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran 2',
            'description' => 'Desc',
        ]);
        $l2->facilities()->attach([$wifi->id, $ac->id]);

        // Listing 3: WiFi + AC + KM Dalam
        $l3 = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kost Lengkap',
            'slug' => 'kost-lengkap',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran 3',
            'description' => 'Desc',
        ]);
        $l3->facilities()->attach([$wifi->id, $ac->id, $kmDalam->id]);

        // WiFi only filter -> returns all 3
        $this->assertSame(3, $this->action->execute(new SearchCriteria(facilityIds: [$wifi->id]))->total());

        // WiFi + AC filter -> returns l2 and l3 (AND logic)
        $this->assertSame(2, $this->action->execute(new SearchCriteria(facilityIds: [$wifi->id, $ac->id]))->total());

        // WiFi + AC + KM Dalam filter -> returns only l3 (AND logic)
        $resAll = $this->action->execute(new SearchCriteria(facilityIds: [$wifi->id, $ac->id, $kmDalam->id]));
        $this->assertSame(1, $resAll->total());
        $this->assertSame('Kost Lengkap', $resAll->items()[0]->name);
    }

    public function test_it_handles_pagination_and_sorting(): void
    {
        for ($i = 1; $i <= 15; $i++) {
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
                'price_monthly' => 500000 + ($i * 50000),
                'price_deposit' => 100000,
                'total_rooms' => 2,
                'available_rooms' => 2,
                'status' => RoomStatus::AVAILABLE,
            ]);
        }

        // Page 1 with perPage 12
        $page1 = $this->action->execute(new SearchCriteria(page: 1, perPage: 12));
        $this->assertSame(15, $page1->total());
        $this->assertCount(12, $page1->items());
        $this->assertSame(1, $page1->currentPage());

        // Page 2 with perPage 12
        $page2 = $this->action->execute(new SearchCriteria(page: 2, perPage: 12));
        $this->assertCount(3, $page2->items());
        $this->assertSame(2, $page2->currentPage());

        // Price Low sorting: First item should be lowest price
        $sortedLow = $this->action->execute(new SearchCriteria(sort: 'price_low', perPage: 12));
        $this->assertSame('Kost UNNES 1', $sortedLow->items()[0]->name);

        // Price High sorting: First item should be highest price
        $sortedHigh = $this->action->execute(new SearchCriteria(sort: 'price_high', perPage: 12));
        $this->assertSame('Kost UNNES 15', $sortedHigh->items()[0]->name);
    }
}
