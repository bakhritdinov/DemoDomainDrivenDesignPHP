<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindCountryByNumericCodeQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountryByNumericCodeQuery $query): ?Country
    {
        return $this->countryRepository->ofNumericCode($query->getNumericCode());
    }
}