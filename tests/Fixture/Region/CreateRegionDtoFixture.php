<?php

namespace App\Tests\Fixture\Region;


use App\Core\Domain\Region\Dto\CreateRegionDto;

final class CreateRegionDtoFixture
{
    public static function getOne(
        string $countryAlpha2 = 'RU',
        string $name = 'Moscow',
        string $code = 'RU-MOW',
        ?bool  $isActive = null
    ): CreateRegionDto
    {
        return new CreateRegionDto($countryAlpha2, $name, $code, $isActive);
    }
}