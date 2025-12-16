<?php

namespace App\Tests\Fixture\Country;

use App\Core\Domain\Country\Dto\UpdateCountryDto;

final class UpdateCountryDtoFixture
{
    public static function getOne(
        string $name = null,
        int    $numericCode = null,
        string $alpha2 = null,
        string $alpha3 = null,
        bool   $isActive = null
    ): UpdateCountryDto
    {
        return new UpdateCountryDto($name, $numericCode, $alpha2, $alpha3, $isActive);
    }
}