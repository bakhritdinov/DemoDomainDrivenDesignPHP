<?php

namespace App\Core\Domain\Region\Dto;

use Symfony\Component\Uid\Uuid;

readonly class UpdateRegionDto
{
    public function __construct(
        public ?Uuid   $countryId,
        public ?string $name,
        public ?string $code,
        public ?bool   $isActive
    )
    {
    }
}