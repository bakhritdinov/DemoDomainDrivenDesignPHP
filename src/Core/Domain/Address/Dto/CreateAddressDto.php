<?php

namespace App\Core\Domain\Address\Dto;

readonly class CreateAddressDto
{
    public function __construct(
        public string $address
    )
    {
    }
}