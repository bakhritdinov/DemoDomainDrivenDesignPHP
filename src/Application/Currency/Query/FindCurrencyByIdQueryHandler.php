<?php

namespace App\Application\Currency\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;

readonly class FindCurrencyByIdQueryHandler implements QueryHandler
{
    public function __construct(public CurrencyRepositoryInterface $repository)
    {
    }

    public function __invoke(FindCurrencyByIdQuery $query): ?Currency
    {
        return $this->repository->ofId($query->getCurrencyId());
    }
}