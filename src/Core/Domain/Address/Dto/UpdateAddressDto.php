<?php

namespace App\Core\Domain\Address\Dto;

readonly class UpdateAddressDto
{
    public function __construct(
        public ?bool $isActive
    )
    {
    }
}