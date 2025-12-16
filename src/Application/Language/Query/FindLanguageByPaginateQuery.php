<?php

namespace App\Application\Language\Query;

use App\Application\Query;

readonly class FindLanguageByPaginateQuery implements Query
{
    public function __construct(private int $page, private int $offset, private array $filters = ['isActive' => true])
    {
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