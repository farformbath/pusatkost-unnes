<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Repositories\ListingRepositoryInterface;
use App\Domain\Search\Actions\SearchListingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Search\SearchListingRequest;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ListingController extends Controller
{
    public function index(SearchListingRequest $request, SearchListingsAction $searchAction): Response
    {
        $criteria = $request->toCriteria();
        $listings = $searchAction->execute($criteria);
        $facilities = Facility::query()->orderBy('name')->get();

        return Inertia::render('Listings/Index', [
            'listings' => $listings,
            'facilities' => $facilities,
            'filters' => [
                'keyword' => $criteria->keyword ?? '',
                'gender' => $criteria->gender instanceof GenderType ? $criteria->gender->value : '',
                'area' => $criteria->area ?? '',
                'areas' => $criteria->areas,
                'min_price' => $criteria->minPrice,
                'max_price' => $criteria->maxPrice,
                'facility_ids' => $criteria->facilityIds,
                'sort' => $criteria->sort,
            ],
        ]);
    }

    public function show(string $slug, ListingRepositoryInterface $repository): Response
    {
        $listing = $repository->findBySlug($slug);

        if (! $listing) {
            throw new NotFoundHttpException('Kos tidak ditemukan atau belum dipublikasikan.');
        }

        return Inertia::render('Listings/Show', [
            'listing' => $listing,
        ]);
    }
}
