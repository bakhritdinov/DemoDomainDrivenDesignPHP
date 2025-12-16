<?php

namespace App\Tests\Fixture\City;

use App\Core\Domain\City\Dto\UpdateCityDto;
use Symfony\Component\Uid\Uuid;

final class UpdateCityDtoFixture
{
    public static function getOne(
        Uuid   $regionId = null,
        string $type = 'city',
        string $name = 'Moscow',
        bool   $isActive = null
    ): UpdateCityDto
    {
        return new UpdateCityDto($regionId, $type, $name, $isActive);
    }
}