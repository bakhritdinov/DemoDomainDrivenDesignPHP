<?php

namespace App\Core\Domain\Address\Dto;

readonly class AddressDto
{
    public function __construct(
        public string  $address,
        public ?string $postalCode,
        public string  $country,
        public string  $alpha2,
        public string  $region,
        public string  $regionCode,
        public string  $city,
        public string  $cityType,
        public ?string $street,
        public ?string $settlement,
        public string  $house,
        public ?string $entrance,
        public ?string $floor,
        public ?string $flat,
        public float   $latitude,
        public float   $longitude
    )
    {
    }
}