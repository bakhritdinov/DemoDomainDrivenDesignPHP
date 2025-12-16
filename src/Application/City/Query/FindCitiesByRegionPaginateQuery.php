<?php

namespace App\Application\City\Query;

use App\Application\Query;
use App\Core\Domain\Region\Entity\Region;

readonly class FindCitiesByRegionPaginateQuery implements Query
{
    public function __construct(private Region $region, private int $page, private int $offset, private array $filters = ['isActive' => true])
    {
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

}