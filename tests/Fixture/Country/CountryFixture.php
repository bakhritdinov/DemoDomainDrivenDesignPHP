<?php

namespace App\Tests\Fixture\Country;

use App\Core\Domain\Country\Entity\Country;
use Symfony\Component\Uid\Uuid;

final class CountryFixture
{
    public static function getOne(
        string $name = 'Russia',
        int    $numericCode = 643,
        string $alpha2 = 'RU',
        string $alpha3 = 'RUS',
        ?bool  $isActive = null,
        Uuid   $id = null
    ): Country
    {
        $country = new Country($name, $numericCode, $alpha2, $alpha3);

        if (!is_null($isActive)) {
            $country->changeIsActive($isActive);
        }

        $reflectionClass = new \ReflectionClass(Country::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($country, $id ?: Uuid::v1());

        return $country;
    }
}