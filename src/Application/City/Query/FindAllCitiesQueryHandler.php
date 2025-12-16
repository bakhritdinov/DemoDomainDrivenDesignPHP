<?php

namespace App\Application\City\Query;

use App\Application\QueryHandler;
use App\Core\Domain\City\Repository\CityRepositoryInterface;

readonly class FindAllCitiesQueryHandler implements QueryHandler
{
    public function __construct(public CityRepositoryInterface $cityRepository)
    {
    }

    public function __invoke(FindAllCitiesQuery $query): array
    {
        return $this->cityRepository->all();
    }
}