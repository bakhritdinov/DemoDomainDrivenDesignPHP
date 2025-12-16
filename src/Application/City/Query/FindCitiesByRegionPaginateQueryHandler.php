<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Core\Domain\City\Repository\CityRepositoryInterface;

readonly class FindCitiesByRegionPaginateQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $cityRepository)
    {
    }

    public function __invoke(FindCitiesByRegionPaginateQuery $query): array
    {
        return $this->cityRepository->ofRegionPaginate($query->getRegion(), $query->getPage(), $query->getOffset(), $query->getFilters());
    }
}