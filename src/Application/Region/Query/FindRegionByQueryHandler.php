<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindRegionByQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindRegionByQuery $query): array
    {
        return $this->regionRepository->search($query->getQuery(), $query->getFilters());
    }
}