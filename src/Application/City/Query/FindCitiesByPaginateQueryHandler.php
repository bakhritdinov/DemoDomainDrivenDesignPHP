<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Core\Domain\City\Repository\CityRepositoryInterface;

readonly class FindCitiesByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $cityRepository)
    {
    }

    public function __invoke(FindCitiesByPaginateQuery $query): array
    {
        return $this->cityRepository->paginate($query->getPage(), $query->getOffset(), $query->getFilters());
    }
}