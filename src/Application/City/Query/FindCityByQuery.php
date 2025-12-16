<?php

namespace App\Application\City\Query;

use App\Application\Query;

readonly class FindCityByQuery implements Query
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