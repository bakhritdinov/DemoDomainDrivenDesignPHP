<?php

namespace App\Tests\Fixture\Country;

use App\Core\Domain\Country\Dto\CreateCountryDto;

final class CreateCountryDtoFixture
{
    public static function getOne(
        string $name = 'Russia',
        int    $numericCode = 643,
        string $alpha2 = 'RU',
        string $alpha3 = 'RUS',
        bool   $isActive = null
    ): CreateCountryDto
    {
        return new CreateCountryDto($name, $numericCode, $alpha2, $alpha3, $isActive);
    }
}