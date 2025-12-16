<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindCountryByQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountryByQuery $query): array
    {
        return $this->countryRepository->search($query->getQuery(), $query->getFilters());
    }
}