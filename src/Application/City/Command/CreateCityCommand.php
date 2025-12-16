<?php

namespace App\Application\City\Command;

use App\Application\Command;
use App\Core\Domain\City\Dto\CreateCityDto;

readonly class CreateCityCommand implements Command
{
    public function __construct(private CreateCityDto $createCityDto)
    {
    }

    public function getCreateCityDto(): CreateCityDto
    {
        return $this->createCityDto;
    }

}
