<?php

namespace App\Tests\Fixture\Region;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Region\Entity\Region;
use Symfony\Component\Uid\Uuid;

final class RegionFixture
{
    public static function getOne(
        Country $country,
        string  $name = 'Moscow',
        string  $code = 'RU-MOW',
        bool    $isActive = true,
        Uuid    $id = null
    ): Region
    {
        $region = new Region($country, $name, $code);

        $region->changeIsActive($isActive);

        $reflectionClass = new \ReflectionClass(Region::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($region, $id ?: Uuid::v1());

        return $region;
    }
}