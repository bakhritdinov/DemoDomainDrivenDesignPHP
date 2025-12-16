<?php

namespace App\Application\Region\Query;

use App\Application\Query;

readonly class FindRegionByQuery implements Query
{
    public function __construct(private string $query, private array $filters = ['isActive' => true])
    {
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}