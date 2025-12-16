<?php

namespace App\Application\Language\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;

class FindLanguageQueryHandler implements QueryHandler
{
    public function __construct(public LanguageRepositoryInterface $repository)
    {
    }

    public function __invoke(FindLanguageQuery $query): array
    {
        return $this->repository->all();
    }
}