<?php

namespace App\Core\Domain\City\Dto;


readonly class CreateCityDto
{
    public function __construct(
        public string $regionCode,
        public string $type,
        public string $name,
        public ?bool  $isActive = null
    )
    {
    }
}