<?php

namespace App\Core\Domain\Country\Dto;

readonly class UpdateCountryDto
{
    public function __construct(
        public ?string $name,
        public ?int    $numericCode,
        public ?string $alpha2,
        public ?string $alpha3,
        public ?bool   $isActive
    )
    {
    }
}