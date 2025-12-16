<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Core\Domain\City\Repository\CityRepositoryInterface;

readonly class FindCityByQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $cityRepository)
    {
    }

    public function __invoke(FindCityByQuery $query): array
    {
        return $this->cityRepository->search($query->getQuery(), $query->getFilters());
    }
}