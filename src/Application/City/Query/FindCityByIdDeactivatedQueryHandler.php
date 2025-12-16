<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Repository\CityRepositoryInterface;

readonly class FindCityByIdDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $cityRepository)
    {
    }

    public function __invoke(FindCityByIdDeactivatedQuery $query): ?City
    {
        return $this->cityRepository->ofIdDeactivated($query->getCityId());
    }
}