<?php

namespace App\Application\City\Command;

use App\Application\Command;
use App\Core\Domain\City\Dto\UpdateCityDto;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCityCommand implements Command
{
    public function __construct(private Uuid $cityId, private UpdateCityDto $updateCityDto)
    {
    }

    public function getCityId(): Uuid
    {
        return $this->cityId;
    }

    public function getUpdateCityDto(): UpdateCityDto
    {
        return $this->updateCityDto;
    }

}