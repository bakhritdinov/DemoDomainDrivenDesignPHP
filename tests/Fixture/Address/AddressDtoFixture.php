<?php

namespace App\Tests\Fixture\Address;

use App\Core\Domain\Address\Dto\AddressDto;

class AddressDtoFixture
{
    public static function getOne(
        string  $address,
        string  $postalCode,
        string  $country,
        string  $alpha2,
        string  $region,
        string  $regionCode,
        string  $city,
        string  $cityType,
        string  $street,
        string  $settlement,
        ?string $house,
        ?string $entrance,
        ?string $floor,
        ?string $flat
    ): AddressDto
    {
        return new AddressDto(
            $address,
            $postalCode,
            $country,
            $alpha2,
            $region,
            $regionCode,
            $city,
            $cityType,
            $street,
            $settlement,
            $house,
            $entrance,
            $floor,
            $flat,
            12.323232,
            23.343434
        );
    }

    public static function getOneFilled(
        string  $address = null,
        string  $postalCode = null,
        string  $country = null,
        string  $alpha2 = null,
        string  $region = null,
        string  $regionCode = null,
        string  $city = null,
        string  $cityType = null,
        string  $street = null,
        string  $settlement = null,
        ?string $house = null,
        ?string $entrance = null,
        ?string $floor = null,
        ?string $flat = null
    ): AddressDto
    {
        return new AddressDto(
            $address ?: '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1',
            $postalCode ?: '309850',
            $country ?: 'Россия',
            $alpha2 ?: 'RU',
            $region ?: 'Белгородская',
            $regionCode ?: 'RU-BEL',
            $city ?: 'Алексеевка',
            $cityType ?: 'город',
            $street ?: 'ул Слободская',
            '',
            $house ?: '1/1',
            $entrance,
            $floor,
            $flat,
            12.323232,
            23.343434
        );
    }
}