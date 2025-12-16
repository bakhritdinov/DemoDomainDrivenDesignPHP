<?php

namespace App\Application\Region\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class FindAllRegionsQueryHandler implements QueryHandler
{
    public function __construct(public RegionRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindAllRegionsQuery $query): array
    {
        return $this->regionRepository->all();
    }
}