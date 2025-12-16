<?php

namespace App\Core\Domain\City\Dto;

use Symfony\Component\Uid\Uuid;

readonly class UpdateCityDto
{
    public function __construct(
        public ?Uuid   $regionId,
        public ?string $type,
        public ?string $name,
        public ?bool   $isActive
    )
    {
    }
}