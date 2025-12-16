<?php

namespace App\Tests\Fixture\Address;

use App\Core\Domain\Address\Dto\AddressDto;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\ValueObject\Point;
use App\Core\Domain\City\Entity\City;
use Symfony\Component\Uid\Uuid;

final class AddressFixture
{
    public static function getOne(
        City   $city,
        string $address,
        string $postalCode,
        string $street,
        string $house,
        string $flat = null,
        string $entrance = null,
        string $floor = null,
        Uuid   $id = null
    ): Address
    {
        $point = new Point(12.122133, 32.333333);
        $address = new Address($city, $address, $postalCode, $street, $house, $flat, $entrance, $floor, $point);

        $reflectionClass = new \ReflectionClass(Address::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($address, $id ?: Uuid::v1());

        return $address;
    }

    public static function getOneForIsActive(
        City   $city,
        string $address,
        string $postalCode,
        string $street,
        string $house,
        string $flat = null,
        string $entrance = null,
        string $floor = null,
        bool   $isActive = true,
        Uuid   $id = null
    ): Address
    {
        $point = new Point(12.122133, 32.333333);
        $address = new Address($city, $address, $postalCode, $street, $house, $flat, $entrance, $floor, $point);

        $address->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(Address::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($address, $id ?: Uuid::v1());

        return $address;
    }

    public static function getOneFromAddressDto(City $city, AddressDto $dto, bool $isActive = null, Uuid $id = null): Address
    {
        $point = new Point($dto->latitude, $dto->longitude);
        $address = new Address($city, $dto->address, $dto->postalCode, $dto->street, $dto->house, $dto->flat, $dto->entrance, $dto->floor, $point);

        if (!is_null($isActive)) {
            $address->changeIsActive($isActive);
        }

        $reflectionClass = new \ReflectionClass(Address::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($address, $id ?: Uuid::v1());

        return $address;
    }
}