<?php

namespace App\Application\Language\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;

class FindLanguageByIdDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public LanguageRepositoryInterface $repository)
    {
    }

    public function __invoke(FindLanguageByIdDeactivatedQuery $query): ?Language
    {
        return $this->repository->ofIdDeactivated($query->getLanguageId());
    }
}