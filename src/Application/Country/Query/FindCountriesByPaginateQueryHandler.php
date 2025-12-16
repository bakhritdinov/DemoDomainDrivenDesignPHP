<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindCountriesByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountriesByPaginateQuery $query): array
    {
        return $this->countryRepository->paginate($query->getPage(), $query->getOffset(), $query->getFilters());
    }
}