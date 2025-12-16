<?php

namespace App\Application\Currency\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Currency\Entity\Currency;
use App\Core\Domain\Currency\Repository\CurrencyRepositoryInterface;

readonly class FindCurrencyByNumDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public CurrencyRepositoryInterface $repository)
    {
    }

    public function __invoke(FindCurrencyByNumDeactivatedQuery $query): ?Currency
    {
        return $this->repository->ofNumDeactivated($query->getNum());
    }
}