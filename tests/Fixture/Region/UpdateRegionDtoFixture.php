<?php

namespace App\Tests\Fixture\Region;

use App\Core\Domain\Region\Dto\UpdateRegionDto;
use Symfony\Component\Uid\Uuid;

final class UpdateRegionDtoFixture
{
    public static function getOne(
        Uuid   $countryId = null,
        string $name = null,
        string $code = null,
        bool   $isActive = null
    ): UpdateRegionDto
    {
        return new UpdateRegionDto($countryId, $name, $code, $isActive);
    }
}