<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindRegionsByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindRegionsByPaginateQuery $query): array
    {
        return $this->regionRepository->paginate($query->getPage(), $query->getOffset(), $query->getFilters());
    }
}