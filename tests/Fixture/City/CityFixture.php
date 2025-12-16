<?php

namespace App\Tests\Fixture\City;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\Region\Entity\Region;
use Symfony\Component\Uid\Uuid;

final class CityFixture
{
    public static function getOne(
        Region $region,
        string $type = 'city',
        string $name = 'Moscow',
        bool   $isActive = true,
        Uuid   $id = null
    ): City
    {
        $city = new City($region, $type, $name);

        $city->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(City::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($city, $id ?: Uuid::v1());

        return $city;
    }
}