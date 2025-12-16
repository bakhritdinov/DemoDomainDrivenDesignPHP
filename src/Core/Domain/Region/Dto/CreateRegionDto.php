<?php

namespace App\Core\Domain\Region\Dto;

readonly class CreateRegionDto
{
    public function __construct(
        public string $countryAlpha2,
        public string $name,
        public string $code,
        public ?bool  $isActive = null
    )
    {
    }
}