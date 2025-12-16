<?php

namespace App\Application\Language\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Language\Entity\Language;
use App\Core\Domain\Language\Repository\LanguageRepositoryInterface;

class FindLanguageByCodeQueryHandler implements QueryHandler
{
    public function __construct(public LanguageRepositoryInterface $repository)
    {
    }

    public function __invoke(FindLanguageByCodeQuery $query): ?Language
    {
        return $this->repository->ofCode($query->getLanguageCode());
    }

}