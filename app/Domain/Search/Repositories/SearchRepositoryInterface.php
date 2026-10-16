<?php

declare(strict_types=1);

namespace App\Domain\Search\Repositories;

use App\Domain\Listing\Models\Listing;
use App\Domain\Search\DTOs\SearchCriteria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SearchRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Listing>
     */
    public function search(SearchCriteria $criteria): LengthAwarePaginator;
}
