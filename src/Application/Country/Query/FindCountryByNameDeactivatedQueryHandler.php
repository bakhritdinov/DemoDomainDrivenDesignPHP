<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindCountryByNameDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountryByNameDeactivatedQuery $query): ?Country
    {
        return $this->countryRepository->ofNameDeactivated($query->getName());
    }
}