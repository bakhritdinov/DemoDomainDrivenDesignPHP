<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindRegionByCodeDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindRegionByCodeDeactivatedQuery $query): ?Region
    {
        return $this->regionRepository->ofCodeDeactivated($query->getCode());
    }
}