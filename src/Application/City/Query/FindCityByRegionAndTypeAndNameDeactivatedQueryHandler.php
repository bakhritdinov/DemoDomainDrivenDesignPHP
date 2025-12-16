<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;

readonly class FindCityByRegionAndTypeAndNameDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $cityRepository)
    {
    }

    public function __invoke(FindCityByRegionAndTypeAndNameDeactivatedQuery $query): ?City
    {
        return $this->cityRepository->ofRegionAndTypeAndNameDeactivated($query->getRegion(), $query->getType(), $query->getName());
    }
}