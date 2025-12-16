<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindRegionByNameDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindRegionByNameDeactivatedQuery $query): ?Region
    {
        return $this->regionRepository->ofNameDeactivated($query->getName());
    }
}