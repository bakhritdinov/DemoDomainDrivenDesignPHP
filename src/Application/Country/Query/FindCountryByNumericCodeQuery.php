<?php

namespace App\Application\Country\Query;

use App\Application\Query;

readonly class FindCountryByNumericCodeQuery implements Query
{
    public function __construct(private string $numericCode)
    {
    }

    public function getNumericCode(): string
    {
        return $this->numericCode;
    }
}