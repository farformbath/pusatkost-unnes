<?php

declare(strict_types=1);

namespace App\Domain\Search\Actions;

use App\Domain\Listing\Models\Listing;
use App\Domain\Search\DTOs\SearchCriteria;
use App\Domain\Search\Repositories\SearchRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchListingsAction
{
    public function __construct(
        private readonly SearchRepositoryInterface $searchRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Listing>
     */
    public function execute(SearchCriteria $criteria): LengthAwarePaginator
    {
        return $this->searchRepository->search($criteria);
    }
}
