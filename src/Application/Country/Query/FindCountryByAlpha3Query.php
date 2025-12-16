<?php

namespace App\Application\Country\Query;

use App\Application\Query;

readonly class FindCountryByAlpha3Query implements Query
{
    public function __construct(private string $alpha3)
    {
    }

    public function getAlpha3(): string
    {
        return $this->alpha3;
    }
}