<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindCountryByAlpha2DeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountryByAlpha2DeactivatedQuery $query): ?Country
    {
        return $this->countryRepository->ofAlpha2Deactivated($query->getAlpha2());
    }
}