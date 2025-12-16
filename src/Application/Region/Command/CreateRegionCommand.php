<?php

namespace App\Application\Region\Command;

use App\Application\Command;
use App\Core\Domain\Region\Dto\CreateRegionDto;

readonly class CreateRegionCommand implements Command
{
    public function __construct(private CreateRegionDto $createRegionDto)
    {
    }

    public function getCreateRegionDto(): CreateRegionDto
    {
        return $this->createRegionDto;
    }

}