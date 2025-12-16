<?php

namespace App\Application\Language\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;

class FindLanguageByCodeDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public LanguageRepositoryInterface $repository)
    {
    }

    public function __invoke(FindLanguageByCodeDeactivatedQuery $query): ?Language
    {
        return $this->repository->ofCodeDeactivated($query->getLanguageCode());
    }

}