<?php

namespace App\Application\Country\Query;

use App\Application\Query;

readonly class FindCountryByAlpha2Query implements Query
{
    public function __construct(private string $alpha2)
    {
    }

    public function getAlpha2(): string
    {
        return $this->alpha2;
    }
}