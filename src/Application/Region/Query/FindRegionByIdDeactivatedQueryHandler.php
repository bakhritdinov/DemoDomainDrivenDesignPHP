<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindRegionByIdDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindRegionByIdDeactivatedQuery $query): ?Region
    {
        return $this->regionRepository->ofIdDeactivated($query->getRegionId());
    }
}