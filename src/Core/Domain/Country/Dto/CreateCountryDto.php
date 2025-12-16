<?php

namespace App\Core\Domain\Country\Dto;

readonly class CreateCountryDto
{
    public function __construct(
        public string $name,
        public int    $numericCode,
        public string $alpha2,
        public string $alpha3,
        public ?bool  $isActive = null
    )
    {
    }
}