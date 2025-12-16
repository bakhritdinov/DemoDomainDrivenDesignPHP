<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindAllCountriesQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindAllCountriesQuery $query): array
    {
        return $this->countryRepository->all();
    }
}