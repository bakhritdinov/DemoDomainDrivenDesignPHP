<?php

namespace App\Application\Language\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;

class FindLanguageByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public LanguageRepositoryInterface $repository)
    {
    }

    public function __invoke(FindLanguageByPaginateQuery $query): array
    {
        return $this->repository->paginate($query->getPage(), $query->getOffset(), $query->getFilters());
    }
}