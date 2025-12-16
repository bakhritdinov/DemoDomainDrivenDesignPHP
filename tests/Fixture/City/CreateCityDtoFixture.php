<?php

namespace App\Tests\Fixture\City;

use App\Core\Domain\City\Dto\CreateCityDto;

final class CreateCityDtoFixture
{
    public static function getOne(
        string $regionCode = 'RU-MOW',
        string $type = 'city',
        string $name = 'Moscow',
        bool   $isActive = null
    ): CreateCityDto
    {
        return new CreateCityDto($regionCode, $type, $name, $isActive);
    }
}