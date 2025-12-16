<?php

namespace App\Application\Region\Query;

use App\Application\Query;
use App\Core\Domain\Country\Entity\Country;

readonly class FindRegionsByCountryPaginateQuery implements Query
{
    public function __construct(private Country $country, private int $page, private int $offset, private array $filters = ['isActive' => true])
    {
    }

    public function getCountry(): Country
    {
        return $this->country;
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