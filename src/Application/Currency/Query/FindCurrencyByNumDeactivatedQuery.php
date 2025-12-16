<?php

namespace App\Application\Currency\Query;

use App\Application\Query;

readonly class FindCurrencyByNumDeactivatedQuery implements Query
{
    public function __construct(private int $num)
    {
    }

    public function getNum(): int
    {
        return $this->num;
    }
}