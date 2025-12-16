<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindRegionsByCountryPaginateQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindRegionsByCountryPaginateQuery $query): array
    {
        return $this->regionRepository->ofCountryPaginate($query->getCountry(), $query->getPage(), $query->getOffset(), $query->getFilters());
    }
}