<?php

namespace App\Application\Country\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;

readonly class FindCountryByAlpha3DeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public CountryRepositoryInterface $countryRepository)
    {
    }

    public function __invoke(FindCountryByAlpha3DeactivatedQuery $query): ?Country
    {
        return $this->countryRepository->ofAlpha3Deactivated($query->getAlpha3());
    }
}