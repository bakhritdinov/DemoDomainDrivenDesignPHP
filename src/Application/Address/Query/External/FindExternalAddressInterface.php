<?php

namespace App\Application\Address\Query\External;

use App\Core\Domain\Address\Dto\AddressDto;

interface FindExternalAddressInterface
{
    public function find(string $address): ?AddressDto;
}