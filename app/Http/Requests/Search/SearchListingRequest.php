<?php

declare(strict_types=1);

namespace App\Http\Requests\Search;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Search\DTOs\SearchCriteria;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', Rule::enum(GenderType::class)],
            'area' => ['nullable'],
            'areas' => ['nullable', 'array'],
            'areas.*' => ['string', 'max:50'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'min:0', 'gte:min_price'],
            'facility_ids' => ['nullable', 'array'],
            'facility_ids.*' => ['string', 'uuid'],
            'sort' => ['nullable', 'string', Rule::in([
                'relevance',
                'recommended',
                'price_low',
                'price_high',
                'price_asc',
                'price_desc',
                'rating_desc',
                'newest',
            ])],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', Rule::in([12, 24, 48])],
        ];
    }

    public function toCriteria(): SearchCriteria
    {
        /** @var array{keyword?: ?string, gender?: ?string, area?: string|list<string>|null, areas?: list<string>|null, min_price?: ?int, max_price?: ?int, facility_ids?: list<string>, sort?: ?string, page?: ?int, per_page?: ?int} $validated */
        $validated = $this->validated();

        return SearchCriteria::fromArray($validated);
    }
}
