<?php

namespace App\Application\Address\Query;

use App\Application\Query;
use App\Core\Domain\City\Entity\City;

readonly class FindAddressesByCityPaginateQuery implements Query
{
    public function __construct(private City $city, private int $page, private int $offset, private array $filters = ['isActive' => true])
    {
    }

    public function getCity(): City
    {
        return $this->city;
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